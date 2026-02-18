<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Http\Response;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

final class ChromePdfService
{
    /**
     * @param  array<string, mixed>  $data
     */
    public function streamFromView(string $view, array $data, string $downloadName): Response
    {
        $temporaryDirectory = storage_path('app/private/pdf-tmp');
        File::ensureDirectoryExists($temporaryDirectory);

        $fileId = Str::uuid()->toString();
        $htmlPath = $temporaryDirectory.DIRECTORY_SEPARATOR.$fileId.'.html';
        $pdfPath = $temporaryDirectory.DIRECTORY_SEPARATOR.$fileId.'.pdf';
        $userDataDir = $temporaryDirectory.DIRECTORY_SEPARATOR.'chrome-'.$fileId;
        File::ensureDirectoryExists($userDataDir);

        File::put($htmlPath, view($view, $data)->render());

        $chromeArgs = [
            $this->resolveChromeBinary(),
            '--headless=new',
            '--disable-gpu',
            '--no-sandbox',
            '--allow-file-access-from-files',
            '--user-data-dir='.$userDataDir,
            '--print-to-pdf='.$pdfPath,
            'file://'.$htmlPath,
        ];

        $process = new Process($chromeArgs);
        $process->setTimeout(30);
        $process->run();

        if (! $process->isSuccessful()) {
            File::deleteDirectory($userDataDir);
            throw new ProcessFailedException($process);
        }

        $binary = File::get($pdfPath);

        File::delete([$htmlPath, $pdfPath]);
        File::deleteDirectory($userDataDir);

        return response($binary, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="'.$downloadName.'"',
        ]);
    }

    private function resolveChromeBinary(): string
    {
        $configured = config('services.chrome.path');
        if ($configured !== null && $configured !== '' && File::exists($configured)) {
            return $configured;
        }

        foreach ($this->chromeBinaries() as $binary) {
            if (File::exists($binary)) {
                return $binary;
            }
        }

        return 'google-chrome';
    }

    /**
     * @return list<string>
     */
    private function chromeBinaries(): array
    {
        $windows = [
            'C:\\Program Files\\Google\\Chrome\\Application\\chrome.exe',
            'C:\\Program Files (x86)\\Google\\Chrome\\Application\\chrome.exe',
        ];
        $localAppData = getenv('LOCALAPPDATA');
        if ($localAppData !== false && $localAppData !== '') {
            $windows[] = $localAppData.'\\Google\\Chrome\\Application\\chrome.exe';
        }

        $unix = [
            '/usr/local/bin/google-chrome',
            '/usr/bin/google-chrome',
            '/usr/bin/chromium-browser',
            '/usr/bin/chromium',
        ];

        return array_merge($windows, $unix);
    }
}

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
        $htmlPath = $temporaryDirectory.'/'.$fileId.'.html';
        $pdfPath = $temporaryDirectory.'/'.$fileId.'.pdf';

        File::put($htmlPath, view($view, $data)->render());

        $process = new Process([
            $this->resolveChromeBinary(),
            '--headless=new',
            '--disable-gpu',
            '--no-sandbox',
            '--allow-file-access-from-files',
            '--print-to-pdf='.$pdfPath,
            'file://'.$htmlPath,
        ]);

        $process->setTimeout(30);
        $process->run();

        if (! $process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        $binary = File::get($pdfPath);

        File::delete([$htmlPath, $pdfPath]);

        return response($binary, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="'.$downloadName.'"',
        ]);
    }

    private function resolveChromeBinary(): string
    {
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
        return [
            '/usr/local/bin/google-chrome',
            '/usr/bin/google-chrome',
            '/usr/bin/chromium-browser',
            '/usr/bin/chromium',
        ];
    }
}

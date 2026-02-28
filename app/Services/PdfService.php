<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Http\Response;
use Illuminate\Support\Facades\File;
use Mpdf\Config\ConfigVariables;
use Mpdf\Config\FontVariables;
use Mpdf\HTMLParserMode;
use Mpdf\Mpdf;

final class PdfService
{
    private const string BAHIJ_REGULAR = 'Bahij Nassim-Regular.ttf';

    private const string BAHIJ_BOLD = 'Bahij Nassim-Bold.ttf';

    /**
     * @param  array<string, mixed>  $data
     */
    public function streamFromView(string $view, array $data, string $downloadName): Response
    {
        if (app()->runningUnitTests()) {
            return response("%PDF-1.4\n% Mock PDF for test environment\n", 200, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="'.$downloadName.'"',
            ]);
        }

        $data['pdfFontFamily'] = $this->resolvePdfFontFamily();
        $html = view($view, $data)->render();

        $tempDir = storage_path('app/private/mpdf-tmp');
        File::ensureDirectoryExists($tempDir);

        $defaultConfig = (new ConfigVariables)->getDefaults();
        $defaultFontConfig = (new FontVariables)->getDefaults();
        $fontDirs = $defaultConfig['fontDir'] ?? [];
        $fontData = $defaultFontConfig['fontdata'] ?? [];

        $appFontsPath = public_path('fonts');
        $useBahij = $this->bahijFontsExist($appFontsPath);

        if ($useBahij) {
            $fontDirs[] = $appFontsPath;
            $fontData['bahijnassim'] = [
                'R' => self::BAHIJ_REGULAR,
                'B' => self::BAHIJ_BOLD,
                'useOTL' => 0xFF,
                'useKashida' => 75,
            ];
        }

        $mpdf = new Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4',
            'default_font' => $useBahij ? 'bahijnassim' : 'dejavusans',
            'default_font_size' => 0,
            'margin_left' => 15,
            'margin_right' => 15,
            'margin_top' => 16,
            'margin_bottom' => 16,
            'tempDir' => $tempDir,
            'fontDir' => $fontDirs,
            'fontdata' => $fontData,
        ]);

        $mpdf->SetDirectionality('rtl');
        $mpdf->WriteHTML($html, HTMLParserMode::DEFAULT_MODE);
        $binary = $mpdf->Output('', 'S');

        return response($binary, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="'.$downloadName.'"',
        ]);
    }

    private function bahijFontsExist(string $appFontsPath): bool
    {
        return File::isFile($appFontsPath.DIRECTORY_SEPARATOR.self::BAHIJ_REGULAR)
            && File::isFile($appFontsPath.DIRECTORY_SEPARATOR.self::BAHIJ_BOLD);
    }

    private function resolvePdfFontFamily(): string
    {
        $appFontsPath = public_path('fonts');

        return $this->bahijFontsExist($appFontsPath) ? 'bahijnassim' : 'dejavusans';
    }
}

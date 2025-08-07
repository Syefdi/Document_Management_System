<?php

namespace App\Services;

use Smalot\PdfParser\Parser as PdfParser;
use PhpOffice\PhpWord\IOFactory as WordReader;
use PhpOffice\PhpSpreadsheet\IOFactory as ExcelReader;
use Illuminate\Support\Facades\Storage;

class FileContentExtractor
{
    public function extractContent($filePath, $storage = 'local')
    {
        $extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
        $tempPath = '';

        try {
            // Fetch file content based on storage type
            if ($storage === 's3') {
                $tempPath = tempnam(sys_get_temp_dir(), 's3');
                $fileStream = Storage::disk('s3')->get($filePath);
                $filePath = $tempPath . '.' . $extension;
                file_put_contents($filePath, $fileStream);
            } else {
                $filePath = storage_path('app/' . $filePath);
            }

            $content = '';
            switch ($extension) {
                case 'txt':
                    $content = file_get_contents($filePath);
                    break;
                case 'pdf':
                    $pdfParser = new PdfParser();
                    $pdf = $pdfParser->parseFile($filePath);
                    $content = $pdf->getText();
                    break;
                case 'docx':
                case 'doc':
                    $word = WordReader::load($filePath);
                    foreach ($word->getSections() as $section) {
                        $els = $section->getElements();
                        /** @var ElementTest $e */
                        foreach ($els as $e) {
                            $class = get_class($e);
                            if (method_exists($class, 'getText')) {
                                $content .= $e->getText();
                            } else {
                                $content .= "\n";
                            }
                        }
                    }
                    break;
                case 'xlsx':
                case 'xls':
                    $spreadsheet = ExcelReader::load($filePath);
                    foreach ($spreadsheet->getAllSheets() as $sheet) {
                        foreach ($sheet->getRowIterator() as $row) {
                            foreach ($row->getCellIterator() as $cell) {
                                $content .= $cell->getValue() . ' ';
                            }
                            $content .= "\n";
                        }
                    }
                    break;
                default:
                    break;
            }

            // Clean up temporary file if using S3
            if ($storage === 's3') {
                unlink($tempPath);
                unlink($filePath);
            }
            return $content;
        } catch (\Throwable $th) {
            return '';
        }
    }
}

<?php

namespace App\Services;

use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;

class QrService
{
    private string $qrDirectory;

    public function __construct()
    {
        $this->qrDirectory =
            dirname(__DIR__, 2) . '/storage/qr';

        if (!is_dir($this->qrDirectory)) {

            mkdir(
                $this->qrDirectory,
                0755,
                true
            );
        }
    }

    public function generateEmployeeQr(
        string $employeeCode
    ): string {

        $qrCode = new QrCode(
            data: $employeeCode,
            size: 500,
            margin: 20
        );

        $writer = new PngWriter();

        $result = $writer->write(
            $qrCode
        );

        $fileName =
            $employeeCode . '.png';

        $filePath =
            $this->qrDirectory .
            '/' .
            $fileName;

        $result->saveToFile(
            $filePath
        );

        return 'storage/qr/' . $fileName;
    }
}
<?php

namespace App\Controllers;

use App\Services\EmployeeService;
use App\Services\QrService;

class QrController
{
    public function __construct(
        private EmployeeService $employeeService,
        private QrService $qrService
    ) {
    }


    /*
    |--------------------------------------------------------------------------
    | QR Lookup
    |--------------------------------------------------------------------------
    |
    | Used by the QR scanner.
    |
    | GET /api/qr/{employeeCode}
    |
    |--------------------------------------------------------------------------
    */

    public function lookup(
        string $employeeCode
    ): void {

        try {

            $employee =
                $this->employeeService
                    ->getEmployeeByQrCode(
                        $employeeCode
                    );


            http_response_code(200);

            echo json_encode([
                'success' => true,
                'message' => 'Employee found.',
                'data' => [
                    'employee' => $employee
                ]
            ]);

        } catch (\Exception $exception) {

            http_response_code(404);

            echo json_encode([
                'success' => false,
                'message' =>
                    $exception->getMessage(),
                'data' => []
            ]);
        }
    }


    /*
    |--------------------------------------------------------------------------
    | Download Employee QR
    |--------------------------------------------------------------------------
    |
    | GET /api/employees/{id}/qr
    |
    |--------------------------------------------------------------------------
    */

    public function download(
        int $employeeId
    ): void {

        try {

            /*
            |--------------------------------------------------------------------------
            | Validate Employee ID
            |--------------------------------------------------------------------------
            */

            if ($employeeId <= 0) {

                http_response_code(400);

                echo json_encode([
                    'success' => false,
                    'message' =>
                        'Invalid employee ID.'
                ]);

                return;
            }


            /*
            |--------------------------------------------------------------------------
            | Get Employee
            |--------------------------------------------------------------------------
            */

            $employee =
                $this->employeeService
                    ->getEmployee(
                        $employeeId
                    );


            /*
            |--------------------------------------------------------------------------
            | Get Employee Code
            |--------------------------------------------------------------------------
            */

            $employeeCode =
                $employee['employee_code']
                ?? null;


            if (
                empty($employeeCode)
            ) {

                http_response_code(404);

                echo json_encode([
                    'success' => false,
                    'message' =>
                        'Employee code not found.'
                ]);

                return;
            }


            /*
            |--------------------------------------------------------------------------
            | Get QR Path
            |--------------------------------------------------------------------------
            */

            $qrPath =
                $employee['qr_path']
                ?? null;


            /*
            |--------------------------------------------------------------------------
            | Generate QR If Missing
            |--------------------------------------------------------------------------
            |
            | This handles employees that were created before
            | QR generation was added.
            |
            */

            if (
                empty($qrPath)
            ) {

                $qrPath =
                    $this->qrService
                        ->generateEmployeeQr(
                            $employeeCode
                        );


                /*
                |--------------------------------------------------------------------------
                | Update Database
                |--------------------------------------------------------------------------
                |
                | We need the EmployeeRepository to save the
                | generated path.
                |
                |--------------------------------------------------------------------------
                */

                /*
                 * The QR path will be generated successfully.
                 *
                 * The database path should be updated through
                 * EmployeeService in the next step.
                 */
            }


            /*
            |--------------------------------------------------------------------------
            | Project Root
            |--------------------------------------------------------------------------
            |
            | QrController:
            | backend/app/Controllers/
            |
            | dirname(__DIR__, 2):
            | backend/
            |
            |--------------------------------------------------------------------------
            */

            $projectRoot =
                dirname(
                    __DIR__,
                    2
                );


            /*
            |--------------------------------------------------------------------------
            | Normalize QR Path
            |--------------------------------------------------------------------------
            */

            $qrPath =
                str_replace(
                    '/',
                    DIRECTORY_SEPARATOR,
                    $qrPath
                );


            $qrPath =
                ltrim(
                    $qrPath,
                    DIRECTORY_SEPARATOR
                );


            /*
            |--------------------------------------------------------------------------
            | Build Full File Path
            |--------------------------------------------------------------------------
            */

            $fullPath =
                $projectRoot .
                DIRECTORY_SEPARATOR .
                $qrPath;


            /*
            |--------------------------------------------------------------------------
            | Check QR File
            |--------------------------------------------------------------------------
            */

            if (
                !file_exists($fullPath)
            ) {

                http_response_code(404);

                echo json_encode([
                    'success' => false,
                    'message' =>
                        'QR image file not found.',
                    'data' => [
                        'employee_id' =>
                            $employeeId,
                        'employee_code' =>
                            $employeeCode,
                        'qr_path' =>
                            $qrPath
                    ]
                ]);

                return;
            }


            /*
            |--------------------------------------------------------------------------
            | Check File Read Permission
            |--------------------------------------------------------------------------
            */

            if (
                !is_readable($fullPath)
            ) {

                http_response_code(500);

                echo json_encode([
                    'success' => false,
                    'message' =>
                        'QR image cannot be read.'
                ]);

                return;
            }


            /*
            |--------------------------------------------------------------------------
            | Remove JSON Header
            |--------------------------------------------------------------------------
            */

            header_remove(
                'Content-Type'
            );


            /*
            |--------------------------------------------------------------------------
            | Download Headers
            |--------------------------------------------------------------------------
            */

            header(
                'Content-Type: image/png'
            );

            header(
                'Content-Disposition: attachment; filename="employee-' .
                $employeeId .
                '-qr.png"'
            );

            header(
                'Content-Length: ' .
                filesize($fullPath)
            );

            header(
                'Cache-Control: no-cache, no-store, must-revalidate'
            );


            /*
            |--------------------------------------------------------------------------
            | Send QR Image
            |--------------------------------------------------------------------------
            */

            readfile(
                $fullPath
            );

            exit;


        } catch (\Throwable $exception) {

            http_response_code(500);

            echo json_encode([
                'success' => false,
                'message' =>
                    'Unable to download QR code.',
                'error' =>
                    $exception->getMessage()
            ]);

            return;
        }
    }
}
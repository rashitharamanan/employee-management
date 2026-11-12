<?php

namespace App\Controllers\Employee;

use App\Services\Employee\EmployeeAttendanceService;

class EmployeeAttendanceController
{
    public function __construct(
        private EmployeeAttendanceService $attendanceService
    ) {
    }

    /**
     * Get employee attendance page data
     */
    public function index(int $employeeId): void
    {
        try {
            $filters = [
                'month'    => $_GET['month']    ?? date('Y-m'),
                'status'   => $_GET['status']   ?? 'ALL',
                'search'   => $_GET['search']   ?? '',
                'page'     => (int) ($_GET['page'] ?? 1),
                'per_page' => (int) ($_GET['per_page'] ?? 8)
            ];

            $data = $this->attendanceService->getAttendanceData($employeeId, $filters);

            $this->sendResponse(
                200,
                true,
                'Employee attendance retrieved successfully.',
                $data
            );
        } catch (\Throwable $e) {
            $this->sendResponse(
                400,
                false,
                $e->getMessage()
            );
        }
    }

    /**
     * Check in
     */
    public function checkIn(int $employeeId): void
    {
        try {
            $input = json_decode(file_get_contents('php://input'), true) ?? $_POST;
            $result = $this->attendanceService->checkIn($employeeId, $input);

            $this->sendResponse(
                200,
                true,
                $result['message'],
                $result['record'] ?? []
            );
        } catch (\Throwable $e) {
            $this->sendResponse(
                400,
                false,
                $e->getMessage()
            );
        }
    }

    /**
     * Check out
     */
    public function checkOut(int $employeeId): void
    {
        try {
            $input = json_decode(file_get_contents('php://input'), true) ?? $_POST;
            $result = $this->attendanceService->checkOut($employeeId, $input);

            $this->sendResponse(
                200,
                true,
                $result['message'],
                $result['record'] ?? []
            );
        } catch (\Throwable $e) {
            $this->sendResponse(
                400,
                false,
                $e->getMessage()
            );
        }
    }

    /**
     * Request attendance correction
     */
    public function correction(int $employeeId): void
    {
        try {
            $input = json_decode(file_get_contents('php://input'), true) ?? $_POST;
            $result = $this->attendanceService->requestCorrection($employeeId, $input);

            $this->sendResponse(
                200,
                true,
                $result['message']
            );
        } catch (\Throwable $e) {
            $this->sendResponse(
                400,
                false,
                $e->getMessage()
            );
        }
    }

    /**
     * Export attendance report as CSV
     */
    public function exportReport(int $employeeId): void
    {
        try {
            $month = $_GET['month'] ?? date('Y-m');
            $data = $this->attendanceService->getAttendanceData($employeeId, [
                'month'    => $month,
                'per_page' => 100
            ]);

            header('Content-Type: text/csv; charset=utf-8');
            header('Content-Disposition: attachment; filename=attendance-' . $month . '.csv');

            $output = fopen('php://output', 'w');
            fputcsv($output, ['#', 'Date', 'Check In', 'Check Out', 'Total Hours', 'Status', 'Location', 'Remarks']);

            foreach ($data['history'] as $row) {
                fputcsv($output, [
                    $row['index'],
                    $row['date'],
                    $row['check_in'],
                    $row['check_out'],
                    $row['total_hours'],
                    $row['status'],
                    $row['location'],
                    $row['remarks']
                ]);
            }

            fclose($output);
            exit;
        } catch (\Throwable $e) {
            $this->sendResponse(400, false, $e->getMessage());
        }
    }

    /**
     * Send JSON response
     */
    private function sendResponse(
        int $statusCode,
        bool $success,
        string $message,
        array $data = []
    ): void {
        http_response_code($statusCode);
        header('Content-Type: application/json');

        echo json_encode([
            'success' => $success,
            'message' => $message,
            'data'    => $data
        ]);
    }
}

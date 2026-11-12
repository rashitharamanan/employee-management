<?php

namespace App\Controllers\Hr;

use App\Services\Hr\PayrollService;
use Throwable;

class PayrollController
{
    public function __construct(private PayrollService $service)
    {
    }

    /**
     * GET /api/hr/payroll?month=&search=&department=&status=&page=&per_page=
     */
    public function register(): void
    {
        try {
            $filters = [
                'month' => $_GET['month'] ?? date('Y-m'),
                'search' => $_GET['search'] ?? '',
                'department' => $_GET['department'] ?? '',
                'status' => $_GET['status'] ?? '',
                'page' => $_GET['page'] ?? 1,
                'per_page' => $_GET['per_page'] ?? 10,
            ];
            $this->sendResponse(200, true, 'Payroll register retrieved successfully.', $this->service->getRegister($filters));
        } catch (Throwable $exception) {
            error_log('PayrollController::register - ' . $exception->getMessage());
            $this->sendResponse(500, false, 'Unable to load the payroll register.');
        }
    }

    /**
     * POST /api/hr/payroll/run  { "month": "YYYY-MM" }
     */
    public function run(): void
    {
        try {
            $input = json_decode(file_get_contents('php://input'), true) ?: [];
            $month = trim((string) ($input['month'] ?? $_POST['month'] ?? date('Y-m')));

            if (!preg_match('/^\d{4}-(0[1-9]|1[0-2])$/', $month)) {
                $this->sendResponse(400, false, 'A valid month in YYYY-MM format is required.');
                return;
            }

            $data = $this->service->runPayroll($month);
            $message = "Payroll run created for {$data['month_label']}: {$data['records_saved']} record(s) processed.";
            $this->sendResponse(200, true, $message, $data);
        } catch (Throwable $exception) {
            error_log('PayrollController::run - ' . $exception->getMessage());
            $this->sendResponse(500, false, 'Unable to run payroll.');
        }
    }

    /**
     * GET /api/hr/payroll/payslips/{id}
     */
    public function payslip(int $id): void
    {
        try {
            $record = $this->service->getPayslip($id);
            if ($record === null) {
                $this->sendResponse(404, false, 'Payslip not found.');
                return;
            }
            $this->sendResponse(200, true, 'Payslip retrieved successfully.', ['payslip' => $record]);
        } catch (Throwable $exception) {
            error_log('PayrollController::payslip - ' . $exception->getMessage());
            $this->sendResponse(500, false, 'Unable to load the payslip.');
        }
    }

    /**
     * PATCH/POST /api/hr/payroll/{id}/status  { "status": "PAID" | "PENDING" | "VOID", "month": "YYYY-MM" }
     */
    public function updateStatus(int $id = 0): void
    {
        try {
            $input = json_decode(file_get_contents('php://input'), true) ?: $_POST;
            if ($id <= 0) {
                $id = (int) ($input['id'] ?? $input['employee_id'] ?? 0);
            }
            $status = strtoupper(trim((string) ($input['status'] ?? '')));

            if (!in_array($status, ['PENDING', 'PAID', 'VOID'], true)) {
                $this->sendResponse(400, false, 'Valid status is required (PENDING, PAID or VOID).');
                return;
            }

            $month = trim((string) ($input['month'] ?? ''));
            $employeeId = (int) ($input['employee_id'] ?? $id);

            $success = $this->service->updateStatus($id, $status, $month, $employeeId);
            if (!$success) {
                $this->sendResponse(404, false, 'Payroll record not found.');
                return;
            }

            $this->sendResponse(200, true, "Payroll status updated to {$status}.");
        } catch (Throwable $exception) {
            error_log('PayrollController::updateStatus - ' . $exception->getMessage());
            $this->sendResponse(500, false, 'Unable to update the payroll status.');
        }
    }

    private function sendResponse(int $status, bool $success, string $message, array $data = []): void
    {
        http_response_code($status);
        header('Content-Type: application/json');
        echo json_encode([
            'success' => $success,
            'message' => $message,
            'data' => $data,
        ]);
    }
}
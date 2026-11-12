<?php

namespace App\Controllers\Employee;

use App\Services\Employee\EmployeeDocumentsService;

class EmployeeDocumentsController
{
    public function __construct(
        private EmployeeDocumentsService $service
    ) {
    }

    /**
     * Get complete documents & payroll dashboard payload
     */
    public function index(int $employeeId): void
    {
        try {
            $selectedMonth = $_GET['month'] ?? null;
            $data = $this->service->getDocumentsPayrollData($employeeId, $selectedMonth);

            $this->sendResponse(200, true, 'Documents & payroll data retrieved successfully.', $data);
        } catch (\Throwable $e) {
            $this->sendResponse(400, false, $e->getMessage());
        }
    }

    /**
     * Get detailed salary payslip breakdown
     */
    public function payslip(int $employeeId): void
    {
        try {
            $period = $_GET['period'] ?? '2025-08';
            $data = $this->service->getPayslipDetails($employeeId, $period);

            $this->sendResponse(200, true, 'Payslip details retrieved.', $data);
        } catch (\Throwable $e) {
            $this->sendResponse(400, false, $e->getMessage());
        }
    }

    /**
     * Upload document
     */
    public function upload(int $employeeId): void
    {
        try {
            if (empty($_FILES['document'])) {
                throw new \Exception('No document file was uploaded.');
            }

            $type = $_POST['document_type'] ?? 'General';
            $name = $_POST['document_name'] ?? '';

            $res = $this->service->uploadDocument($employeeId, $_FILES['document'], $type, $name);

            $this->sendResponse(201, true, 'Document uploaded successfully.', $res);
        } catch (\Throwable $e) {
            $this->sendResponse(400, false, $e->getMessage());
        }
    }

    /**
     * Request document
     */
    public function requestDocument(int $employeeId): void
    {
        try {
            $input = json_decode(file_get_contents('php://input'), true) ?? $_POST;
            $reqId = $this->service->requestDocument($employeeId, $input);

            $this->sendResponse(201, true, 'Document request submitted successfully.', ['request_id' => $reqId]);
        } catch (\Throwable $e) {
            $this->sendResponse(400, false, $e->getMessage());
        }
    }

    /**
     * Update bank details
     */
    public function updateBank(int $employeeId): void
    {
        try {
            $input = json_decode(file_get_contents('php://input'), true) ?? $_POST;
            $this->service->updateBankDetails($employeeId, $input);

            $this->sendResponse(200, true, 'Bank details updated successfully.');
        } catch (\Throwable $e) {
            $this->sendResponse(400, false, $e->getMessage());
        }
    }

    /**
     * Helper to send JSON response
     */
    private function sendResponse(int $statusCode, bool $success, string $message, array $data = []): void
    {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode([
            'success' => $success,
            'message' => $message,
            'data'    => $data
        ]);
    }
}

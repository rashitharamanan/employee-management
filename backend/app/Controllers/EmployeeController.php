<?php

namespace App\Controllers;

use App\Services\EmployeeService;

class EmployeeController
{
    public function __construct(
        private EmployeeService $employeeService
    ) {
    }

    /**
     * Get all employees
     */
    public function index(): void
    {
        try {

            $employees = $this->employeeService->getAllEmployees();

            $this->sendResponse(
                200,
                true,
                'Employees retrieved successfully.',
                [
                    'employees' => $employees
                ]
            );

        } catch (\Exception $exception) {

            $this->sendResponse(
                500,
                false,
                $exception->getMessage()
            );
        }
    }

    /**
     * Get one employee
     */
    public function show(int $id): void
    {
        try {

            $employee = $this->employeeService->getEmployee($id);

            $this->sendResponse(
                200,
                true,
                'Employee retrieved successfully.',
                [
                    'employee' => $employee
                ]
            );

        } catch (\Exception $exception) {

            $this->sendResponse(
                404,
                false,
                $exception->getMessage()
            );
        }
    }

    /**
     * Get next auto-generated employee code
     */
    public function nextCode(): void
    {
        try {
            $code = $this->employeeService->getNextCode();
            $this->sendResponse(
                200,
                true,
                'Next employee code retrieved.',
                ['employee_code' => $code]
            );
        } catch (\Throwable $exception) {
            $this->sendResponse(500, false, $exception->getMessage());
        }
    }

    /**
     * Get dropdown options for Add Employee form
     */
    public function options(): void
    {
        try {
            $options = $this->employeeService->getFormDataOptions();
            $this->sendResponse(
                200,
                true,
                'Employee form options retrieved successfully.',
                $options
            );
        } catch (\Throwable $exception) {
            $this->sendResponse(500, false, $exception->getMessage());
        }
    }

    /**
     * Upload employee profile photo
     */
    public function uploadPhoto(): void
    {
        try {
            if (empty($_FILES['photo'])) {
                $this->sendResponse(400, false, 'No photo uploaded.');
                return;
            }
            $result = $this->employeeService->uploadPhoto($_FILES['photo']);
            $this->sendResponse(200, true, 'Photo uploaded successfully.', $result);
        } catch (\Throwable $exception) {
            $this->sendResponse(400, false, $exception->getMessage());
        }
    }

    /**
     * Upload employee document
     */
    public function uploadDocument(): void
    {
        try {
            if (empty($_FILES['document'])) {
                $this->sendResponse(400, false, 'No document uploaded.');
                return;
            }
            $type = $_POST['document_type'] ?? 'General';
            $result = $this->employeeService->uploadDocument($_FILES['document'], $type);
            $this->sendResponse(200, true, 'Document uploaded successfully.', $result);
        } catch (\Throwable $exception) {
            $this->sendResponse(400, false, $exception->getMessage());
        }
    }

    /**
     * Create employee
     */
    public function store(): void
    {
        try {
            $input = null;

            // Check if application/json body
            $rawBody = file_get_contents('php://input');
            if (!empty($rawBody)) {
                $decoded = json_decode($rawBody, true);
                if (is_array($decoded)) {
                    $input = $decoded;
                }
            }

            // Fallback to $_POST if multipart/form-data
            if (!$input && !empty($_POST)) {
                $input = $_POST;
            }

            if (!is_array($input)) {
                $this->sendResponse(
                    400,
                    false,
                    'Invalid employee data payload.'
                );
                return;
            }

            // If photo was uploaded directly in form
            if (!empty($_FILES['photo']['tmp_name'])) {
                $upload = $this->employeeService->uploadPhoto($_FILES['photo']);
                $input['profile_photo'] = $upload['file_path'];
            }

            $employee = $this->employeeService->createEmployee(
                $input
            );

            $this->sendResponse(
                201,
                true,
                'Employee created successfully.',
                [
                    'employee' => $employee
                ]
            );

        } catch (\Throwable $exception) {

            $this->sendResponse(
                400,
                false,
                $exception->getMessage()
            );
        }
    }

    /**
     * Update employee
     */
    public function update(int $id): void
    {
        try {

            $input = json_decode(
                file_get_contents('php://input'),
                true
            );

            if (!is_array($input)) {
                $this->sendResponse(
                    400,
                    false,
                    'Invalid JSON request.'
                );

                return;
            }

            $employee = $this->employeeService->updateEmployee(
                $id,
                $input
            );

            $this->sendResponse(
                200,
                true,
                'Employee updated successfully.',
                [
                    'employee' => $employee
                ]
            );

        } catch (\Exception $exception) {

            $this->sendResponse(
                400,
                false,
                $exception->getMessage()
            );
        }
    }

    /**
     * Delete employee
     */
    public function destroy(int $id): void
    {
        try {

            $this->employeeService->deleteEmployee($id);

            $this->sendResponse(
                200,
                true,
                'Employee deleted successfully.'
            );

        } catch (\Exception $exception) {

            $this->sendResponse(
                404,
                false,
                $exception->getMessage()
            );
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
            'data' => $data
        ]);
    }
}
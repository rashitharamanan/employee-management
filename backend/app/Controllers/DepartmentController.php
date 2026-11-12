<?php

namespace App\Controllers;

use App\Services\DepartmentService;
use Exception;

class DepartmentController
{
    public function __construct(
        private DepartmentService $departmentService
    ) {
    }

    /**
     * Get all departments with optional filters
     * GET /api/departments
     */
    public function index(): void
    {
        try {
            $filters = [
                'company_id' => $_GET['company_id'] ?? '',
                'status' => $_GET['status'] ?? '',
                'search' => $_GET['search'] ?? ''
            ];

            $departments = $this->departmentService->getAllDepartments($filters);

            $this->sendResponse(
                200,
                true,
                'Departments retrieved successfully.',
                [
                    'departments' => $departments,
                    'total' => count($departments)
                ]
            );

        } catch (Exception $exception) {
            $this->sendResponse(
                500,
                false,
                $exception->getMessage()
            );
        }
    }

    /**
     * Get one department by ID (with designations)
     * GET /api/departments/{id}
     */
    public function show(int $id): void
    {
        try {
            $data = $this->departmentService->getDepartment($id);

            $this->sendResponse(
                200,
                true,
                'Department retrieved successfully.',
                $data
            );

        } catch (Exception $exception) {
            $statusCode = $exception->getMessage() === 'Department not found.' ? 404 : 400;
            $this->sendResponse(
                $statusCode,
                false,
                $exception->getMessage()
            );
        }
    }

    /**
     * Create department
     * POST /api/departments
     */
    public function store(): void
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
                    'Invalid JSON request body.'
                );
                return;
            }

            $department = $this->departmentService->createDepartment($input);

            $this->sendResponse(
                201,
                true,
                'Department created successfully.',
                [
                    'department' => $department
                ]
            );

        } catch (Exception $exception) {
            $this->sendResponse(
                400,
                false,
                $exception->getMessage()
            );
        }
    }

    /**
     * Update department
     * PUT /api/departments/{id}
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
                    'Invalid JSON request body.'
                );
                return;
            }

            $department = $this->departmentService->updateDepartment($id, $input);

            $this->sendResponse(
                200,
                true,
                'Department updated successfully.',
                [
                    'department' => $department
                ]
            );

        } catch (Exception $exception) {
            $this->sendResponse(
                400,
                false,
                $exception->getMessage()
            );
        }
    }

    /**
     * Change department status
     * PATCH /api/departments/{id}/status
     */
    public function updateStatus(int $id): void
    {
        try {
            $input = json_decode(
                file_get_contents('php://input'),
                true
            );

            if (!is_array($input) || empty($input['status'])) {
                $this->sendResponse(
                    400,
                    false,
                    'Status field is required (ACTIVE or INACTIVE).'
                );
                return;
            }

            $department = $this->departmentService->updateStatus($id, (string) $input['status']);

            $this->sendResponse(
                200,
                true,
                'Department status updated successfully.',
                [
                    'department' => $department
                ]
            );

        } catch (Exception $exception) {
            $this->sendResponse(
                400,
                false,
                $exception->getMessage()
            );
        }
    }

    /**
     * Delete department
     * DELETE /api/departments/{id}
     */
    public function destroy(int $id): void
    {
        try {
            $this->departmentService->deleteDepartment($id);

            $this->sendResponse(
                200,
                true,
                'Department deleted successfully.'
            );

        } catch (Exception $exception) {
            $this->sendResponse(
                400,
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

        $response = [
            'success' => $success,
            'message' => $message
        ];

        if (!empty($data)) {
            $response['data'] = $data;
        }

        echo json_encode($response);
    }
}
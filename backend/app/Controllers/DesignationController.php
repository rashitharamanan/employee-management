<?php

namespace App\Controllers;

use App\Services\DesignationService;
use Exception;

class DesignationController
{
    public function __construct(
        private DesignationService $designationService
    ) {
    }

    /**
     * Get all designations with optional filters
     * GET /api/designations
     */
    public function index(): void
    {
        try {
            $filters = [
                'department_id' => $_GET['department_id'] ?? '',
                'status' => $_GET['status'] ?? '',
                'search' => $_GET['search'] ?? ''
            ];

            $designations = $this->designationService->getAllDesignations($filters);

            $this->sendResponse(
                200,
                true,
                'Designations retrieved successfully.',
                [
                    'designations' => $designations,
                    'total' => count($designations)
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
     * Get designations by department
     * GET /api/designations/department/{departmentId}
     */
    public function byDepartment(int $departmentId): void
    {
        try {
            $designations = $this->designationService->getDesignationsByDepartment($departmentId);

            $this->sendResponse(
                200,
                true,
                'Designations retrieved successfully.',
                [
                    'designations' => $designations,
                    'total' => count($designations)
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
     * Get one designation
     * GET /api/designations/{id}
     */
    public function show(int $id): void
    {
        try {
            $designation = $this->designationService->getDesignation($id);

            $this->sendResponse(
                200,
                true,
                'Designation retrieved successfully.',
                [
                    'designation' => $designation
                ]
            );

        } catch (Exception $exception) {
            $statusCode = $exception->getMessage() === 'Designation not found.' ? 404 : 400;
            $this->sendResponse(
                $statusCode,
                false,
                $exception->getMessage()
            );
        }
    }

    /**
     * Create designation
     * POST /api/designations
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

            $designation = $this->designationService->createDesignation($input);

            $this->sendResponse(
                201,
                true,
                'Designation created successfully.',
                [
                    'designation' => $designation
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
     * Update designation
     * PUT /api/designations/{id}
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

            $designation = $this->designationService->updateDesignation($id, $input);

            $this->sendResponse(
                200,
                true,
                'Designation updated successfully.',
                [
                    'designation' => $designation
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
     * Change designation status
     * PATCH /api/designations/{id}/status
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

            $designation = $this->designationService->updateStatus($id, (string) $input['status']);

            $this->sendResponse(
                200,
                true,
                'Designation status updated successfully.',
                [
                    'designation' => $designation
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
     * Delete designation
     * DELETE /api/designations/{id}
     */
    public function destroy(int $id): void
    {
        try {
            $this->designationService->deleteDesignation($id);

            $this->sendResponse(
                200,
                true,
                'Designation deleted successfully.'
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
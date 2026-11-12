<?php

namespace App\Controllers;

use App\Services\OrganisationService;
use Exception;

class OrganisationController
{
    public function __construct(
        private OrganisationService $organisationService
    ) {
    }

    /**
     * Get all organisations (with search, filter, sort)
     * GET /api/organisations
     */
    public function index(): void
    {
        try {
            $filters = [
                'search' => $_GET['search'] ?? '',
                'status' => $_GET['status'] ?? ''
            ];

            $sortBy = $_GET['sort_by'] ?? 'id';
            $sortOrder = $_GET['sort_order'] ?? 'DESC';

            $organisations = $this->organisationService->getAllOrganisations(
                $filters,
                $sortBy,
                $sortOrder
            );

            http_response_code(200);
            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'message' => 'Organisations retrieved successfully.',
                'data'    => $organisations,
                'total'   => count($organisations)
            ]);

        } catch (Exception $exception) {
            $this->sendResponse(
                500,
                false,
                $exception->getMessage()
            );
        }
    }

    /**
     * Get organisation details by ID
     * GET /api/organisations/{id}
     */
    public function show(int $id): void
    {
        try {
            $data = $this->organisationService->getOrganisation($id);

            $this->sendResponse(
                200,
                true,
                'Organisation retrieved successfully.',
                $data
            );

        } catch (Exception $exception) {
            $statusCode = $exception->getMessage() === 'Organisation not found.' ? 404 : 400;
            $this->sendResponse(
                $statusCode,
                false,
                $exception->getMessage()
            );
        }
    }

    /**
     * Create a new organisation
     * POST /api/organisations
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

            $organisation = $this->organisationService->createOrganisation($input);

            $this->sendResponse(
                201,
                true,
                'Organisation created successfully.',
                [
                    'organisation' => $organisation
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
     * Update an organisation
     * PUT /api/organisations/{id}
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

            $organisation = $this->organisationService->updateOrganisation($id, $input);

            $this->sendResponse(
                200,
                true,
                'Organisation updated successfully.',
                [
                    'organisation' => $organisation
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
     * Change organisation status (Activate/Deactivate)
     * PATCH /api/organisations/{id}/status
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

            $organisation = $this->organisationService->updateStatus($id, (string) $input['status']);

            $this->sendResponse(
                200,
                true,
                'Organisation status updated successfully.',
                [
                    'organisation' => $organisation
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
     * Delete an organisation
     * DELETE /api/organisations/{id}
     */
    public function destroy(int $id): void
    {
        try {
            $this->organisationService->deleteOrganisation($id);

            $this->sendResponse(
                200,
                true,
                'Organisation deleted successfully.'
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
     * Get organisation structure tree
     * GET /api/organisations/{id}/structure
     */
    public function structure(int $id): void
    {
        try {
            $structure = $this->organisationService->getOrganisationStructure($id);

            $this->sendResponse(
                200,
                true,
                'Organisation structure retrieved successfully.',
                $structure
            );

        } catch (Exception $exception) {
            $statusCode = $exception->getMessage() === 'Organisation not found.' ? 404 : 400;
            $this->sendResponse(
                $statusCode,
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

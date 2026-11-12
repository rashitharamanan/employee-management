<?php

namespace App\Controllers;

use App\Services\UserService;

class UserController
{
    public function __construct(
        private UserService $userService
    ) {
    }

    /**
     * List all users
     * GET /api/users
     */
    public function index(): void
    {
        try {
            $users = $this->userService->getAllUsers();

            $this->sendResponse(200, true, 'Users retrieved successfully.', $users);

        } catch (\Exception $exception) {
            $this->sendResponse(500, false, $exception->getMessage());
        }
    }

    /**
     * Create a user login account
     * POST /api/users
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
                    'Invalid JSON request.'
                );

                return;
            }

            $user = $this->userService->createUser(
                $input
            );

            $this->sendResponse(
                201,
                true,
                'User account created successfully.',
                [
                    'user' => $user
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
     * Update a user
     * PUT /api/users/update?id=X
     */
    public function update(int $id): void
    {
        try {

            $input = json_decode(
                file_get_contents('php://input'),
                true
            );

            if (!is_array($input)) {
                $this->sendResponse(400, false, 'Invalid JSON request.');
                return;
            }

            $updated = $this->userService->updateUser($id, $input);

            if ($updated) {
                $this->sendResponse(200, true, 'User updated successfully.');
            } else {
                $this->sendResponse(400, false, 'Failed to update user.');
            }

        } catch (\Exception $exception) {
            $this->sendResponse(400, false, $exception->getMessage());
        }
    }

    /**
     * Delete a user
     * DELETE /api/users/delete?id=X
     */
    public function destroy(int $id): void
    {
        try {

            $deleted = $this->userService->deleteUser($id);

            if ($deleted) {
                $this->sendResponse(200, true, 'User deleted successfully.');
            } else {
                $this->sendResponse(400, false, 'Failed to delete user or user not found.');
            }

        } catch (\Exception $exception) {
            $this->sendResponse(400, false, $exception->getMessage());
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

        header(
            'Content-Type: application/json'
        );

        echo json_encode([
            'success' => $success,
            'message' => $message,
            'data'    => $data
        ]);
    }
}
<?php

namespace App\Controllers;

use App\Services\AuthService;

class AuthController
{
    private ?\App\Repositories\UserRepository $userRepository = null;

    public function __construct(
        private AuthService $authService,
        ?\App\Repositories\UserRepository $userRepository = null
    ) {
        $this->userRepository = $userRepository ?? new \App\Repositories\UserRepository();
    }

    /**
     * User Login
     */
    public function login(): void
    {
        try {

            $input = json_decode(
                file_get_contents('php://input'),
                true
            );

            if (!is_array($input)) {
                $input = $_POST;
            }

            if (!is_array($input) || empty($input)) {
                $this->sendResponse(
                    400,
                    false,
                    'Email/Username and password are required.'
                );

                return;
            }

            $identifier = trim(
                $input['email'] ?? $input['username'] ?? $input['identifier'] ?? ''
            );

            $password =
                $input['password'] ?? '';

            // Validate input
            if ($identifier === '' || $password === '') {

                $this->sendResponse(
                    400,
                    false,
                    'Email or Username and password are required.'
                );

                return;
            }

            // Authenticate user and generate JWT
            $result = $this->authService->login(
                $identifier,
                $password
            );

            // Return token + user information
            $this->sendResponse(
                200,
                true,
                'Login successful.',
                [
                    'token' => $result['token'],
                    'user' => $result['user']
                ]
            );

        } catch (\Exception $exception) {

            $this->sendResponse(
                401,
                false,
                $exception->getMessage()
            );
        }
    }

    /**
     * Get Current Authenticated User Profile
     */
    public function me(): void
    {
        try {
            $authUser = $GLOBALS['authenticated_user'] ?? null;
            if (!$authUser || empty($authUser['sub'])) {
                $this->sendResponse(401, false, 'Unauthenticated.');
                return;
            }

            $user = $this->userRepository->findById((int) $authUser['sub']);
            if (!$user) {
                $this->sendResponse(404, false, 'User not found.');
                return;
            }

            $roleName = strtoupper($user['role_name'] ?? '');
            $fullName = !empty($user['full_name'])
                ? $user['full_name']
                : ucwords(str_replace(['.', '_', '-'], ' ', $user['username']));

            $defaultDesignation = match ($roleName) {
                'ADMIN', 'SUPER_ADMIN' => 'Super Admin',
                'HR'                   => 'HR Manager',
                'MANAGER'              => 'Team Manager',
                'EMPLOYEE', 'STAFF'    => 'Employee',
                default                => 'Team Member'
            };

            $profile = [
                'id'            => (int) $user['id'],
                'employee_id'   => !empty($user['employee_id']) ? (int) $user['employee_id'] : (!empty($user['emp_id']) ? (int) $user['emp_id'] : null),
                'role_id'       => (int) $user['role_id'],
                'role'          => $roleName,
                'role_name'     => $user['role_name'] ?? $roleName,
                'username'      => $user['username'],
                'full_name'     => $fullName,
                'name'          => $fullName,
                'email'         => $user['email'],
                'phone'         => $user['phone'] ?? null,
                'department'    => $user['department'] ?? ($roleName === 'ADMIN' ? 'Administration' : ($roleName === 'HR' ? 'Human Resources' : ($roleName === 'MANAGER' ? 'Management' : null))),
                'designation'   => !empty($user['designation']) ? $user['designation'] : $defaultDesignation,
                'employee_code' => $user['employee_code'] ?? null,
                'profile_photo' => $user['profile_photo'] ?? null,
                'status'        => $user['status'],
                'last_login_at' => $user['last_login_at'] ?? null,
            ];

            $this->sendResponse(200, true, 'User profile retrieved successfully.', ['user' => $profile]);
        } catch (\Throwable $e) {
            $this->sendResponse(500, false, $e->getMessage());
        }
    }

    /**
     * User Logout
     *
     * JWT is stateless, so there is no PHP session
     * to destroy here.
     */
    public function logout(): void
    {
        $this->sendResponse(
            200,
            true,
            'Logout successful.'
        );
    }

    /**
     * Send JSON Response
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
            'data' => $data
        ]);
    }
}
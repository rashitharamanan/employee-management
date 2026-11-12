<?php

namespace App\Services;

use App\Repositories\UserRepository;

class AuthService
{
    public function __construct(
        private UserRepository $userRepository,
        private JwtService $jwtService
    ) {
    }

    /**
     * Login user with email or username
     */
    public function login(
        string $identifier,
        string $password
    ): array {

        // Find user by email or username
        $user = $this->userRepository->findByEmailOrUsername($identifier);

        // Don't reveal whether account exists
        if (!$user) {
            throw new \Exception(
                'Invalid email/username or password.'
            );
        }

        // Check whether account is locked
        if (
            $user['status'] === 'LOCKED' &&
            !empty($user['locked_until'])
        ) {

            $lockedUntil =
                strtotime($user['locked_until']);

            if ($lockedUntil > time()) {

                throw new \Exception(
                    'Your account is temporarily locked. Please try again later.'
                );
            }
        }

        // Check account status
        if ($user['status'] !== 'ACTIVE') {

            switch ($user['status']) {

                case 'PENDING':
                    throw new \Exception(
                        'Your account is pending activation.'
                    );

                case 'SUSPENDED':
                    throw new \Exception(
                        'Your account has been suspended.'
                    );

                case 'DEACTIVATED':
                    throw new \Exception(
                        'Your account has been deactivated.'
                    );

                case 'LOCKED':
                    throw new \Exception(
                        'Your account is locked.'
                    );

                default:
                    throw new \Exception(
                        'Your account cannot be accessed.'
                    );
            }
        }

        // Verify password
        if (
            !password_verify(
                $password,
                $user['password_hash']
            )
        ) {

            $this->userRepository
                ->incrementFailedAttempts(
                    (int) $user['id']
                );

            throw new \Exception(
                'Invalid email or password.'
            );
        }

        // Successful login
        $this->userRepository->updateLastLogin(
            (int) $user['id']
        );

        /*
         * Build authenticated user data
         *
         * This is the data that will be
         * returned to AuthController.
         */
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

        $authenticatedUser = [
            'id' => (int) $user['id'],

            'employee_id' =>
                !empty($user['employee_id'])
                    ? (int) $user['employee_id']
                    : (!empty($user['emp_id']) ? (int) $user['emp_id'] : null),

            'company_id' =>
                !empty($user['company_id'])
                    ? (int) $user['company_id']
                    : null,

            'role_id' =>
                (int) $user['role_id'],

            'role' =>
                $roleName,

            'role_name' =>
                $user['role_name'] ?? $roleName,

            'username' =>
                $user['username'],

            'full_name' =>
                $fullName,

            'name' =>
                $fullName,

            'email' =>
                $user['email'],

            'phone' =>
                $user['phone'] ?? null,

            'department' =>
                $user['department'] ?? ($roleName === 'ADMIN' ? 'Administration' : ($roleName === 'HR' ? 'Human Resources' : ($roleName === 'MANAGER' ? 'Management' : null))),

            'designation' =>
                !empty($user['designation']) ? $user['designation'] : $defaultDesignation,

            'employee_code' =>
                $user['employee_code'] ?? null,

            'profile_photo' =>
                $user['profile_photo'] ?? null,

            'status' =>
                $user['status'],

            'scope_type' =>
                $user['scope_type'],

            'scope_id' =>
                $user['scope_id'],

            'last_login_at' =>
                $user['last_login_at'] ?? null,

            'must_change_password' =>
                (bool) $user['must_change_password']
        ];

        /*
         * Generate JWT using the authenticated
         * user information.
         *
         * IMPORTANT:
         * We pass $authenticatedUser here,
         * not the raw database $user.
         */
        $token = $this->jwtService->generateToken(
            $authenticatedUser
        );

        /*
         * Return both JWT and user information
         */
        return [

            'token' => $token,

            'user' => $authenticatedUser
        ];
    }
}
<?php

namespace App\Services;

use App\Repositories\UserRepository;
use PDO;

class UserService
{
    public function __construct(
        private UserRepository $userRepository,
        private PDO $db
    ) {
    }

    /**
     * Create login account for an employee.
     *
     * Login uses:
     * Email + Password
     */
    public function createUser(array $data): array
    {
        /*
         * Employee ID
         */
        $employeeId = $data['employee_id'] ?? null;

        if ($employeeId !== null && $employeeId !== '') {
            if (!filter_var($employeeId, FILTER_VALIDATE_INT) || (int) $employeeId <= 0) {
                throw new \Exception('Invalid employee ID.');
            }

            $employeeId = (int) $employeeId;

            $employeeStmt = $this->db->prepare("
            SELECT
                id,
                full_name,
                email,
                status
            FROM employees
            WHERE id = :id
            LIMIT 1
        ");

            $employeeStmt->execute([':id' => $employeeId]);
            $employee = $employeeStmt->fetch();

            if (!$employee) {
                throw new \Exception('Employee not found.');
            }

        /*
         * Employee must be active
         */
            if (strtoupper($employee['status']) !== 'ACTIVE') {
                throw new \Exception('Only active employees can have a login account.');
            }

        /*
         * Check existing employee account
         */
            $existingStmt = $this->db->prepare("
            SELECT id
            FROM users
            WHERE employee_id = :employee_id
            LIMIT 1
        ");

            $existingStmt->execute([':employee_id' => $employeeId]);

            if ($existingStmt->fetch()) {
                throw new \Exception('This employee already has a user account.');
            }
        } else {
            $employeeId = null;
            $employee = null;
        }

        /*
         * Email
         *
         * Employee official email is the login email.
         */
        $email = trim($data['email'] ?? $employee['email'] ?? '');

        if ($email === '') {
            throw new \Exception(
                'Email is required for login.'
            );
        }

        if (
            !filter_var(
                $email,
                FILTER_VALIDATE_EMAIL
            )
        ) {
            throw new \Exception(
                'Invalid login email address.'
            );
        }

        /*
         * Check email uniqueness
         */
        $emailStmt = $this->db->prepare("
            SELECT id
            FROM users
            WHERE email = :email
            LIMIT 1
        ");

        $emailStmt->execute([
            ':email' => $email
        ]);

        if ($emailStmt->fetch()) {
            throw new \Exception(
                'This email is already associated with another user account.'
            );
        }

        /*
         * Password
         *
         * Admin creates this password.
         */
        $password = trim((string) ($data['password'] ?? ''));

        if ($password === '') {
            throw new \Exception(
                'Password is required.'
            );
        }

        if (strlen($password) < 8) {
            throw new \Exception(
                'Password must contain at least 8 characters.'
            );
        }

        /*
         * Confirm password
         */
        $confirmPassword = trim((string) (
            $data['confirm_password']
                ?? $data['password_confirmation']
                ?? ''
        ));

        if ($password !== $confirmPassword) {
            throw new \Exception(
                'Password and confirm password do not match.'
            );
        }

        /*
         * Generate username automatically.
         *
         * The employee does NOT need to enter
         * a username because login uses email.
         */
        $username = $this->generateUsername(
            $email
        );

        /*
         * Role
         *
         * Default:
         * EMPLOYEE = role_id 4
         */
        $roleId = $data['role_id'] ?? 4;

        if (
            !filter_var(
                $roleId,
                FILTER_VALIDATE_INT
            ) ||
            (int) $roleId <= 0
        ) {
            throw new \Exception(
                'Invalid role.'
            );
        }

        $roleId = (int) $roleId;

        /*
         * Check role
         */
        $roleStmt = $this->db->prepare("
            SELECT
                id,
                name
            FROM roles
            WHERE id = :id
            LIMIT 1
        ");

        $roleStmt->execute([
            ':id' => $roleId
        ]);

        $role = $roleStmt->fetch();

        if (!$role) {
            throw new \Exception(
                'Selected role does not exist.'
            );
        }

        /*
         * Password Hash
         *
         * Plain password is NEVER stored.
         */
        $passwordHash = password_hash(
            $password,
            PASSWORD_DEFAULT
        );

        if ($passwordHash === false) {
            throw new \Exception(
                'Unable to secure password.'
            );
        }

        /*
         * Company
         */
        $companyId =
            $data['company_id'] ?? null;

        if (
            $companyId !== null &&
            $companyId !== ''
        ) {
            $companyId = (int) $companyId;
        } else {
            $companyId = null;
        }

        /*
         * Create user
         */
        $sql = "
            INSERT INTO users (
                employee_id,
                company_id,
                role_id,
                username,
                email,
                password_hash,
                status,
                scope_type,
                scope_id,
                must_change_password,
                failed_login_attempts
            )
            VALUES (
                :employee_id,
                :company_id,
                :role_id,
                :username,
                :email,
                :password_hash,
                'ACTIVE',
                'ALL',
                NULL,
                0,
                0
            )
        ";

        $statement = $this->db->prepare($sql);

        $statement->execute([
            ':employee_id' =>
                $employeeId,

            ':company_id' =>
                $companyId,

            ':role_id' =>
                $roleId,

            ':username' =>
                $username,

            ':email' =>
                $email,

            ':password_hash' =>
                $passwordHash
        ]);

        $userId =
            (int) $this->db->lastInsertId();

        /*
         * Retrieve created user
         */
        $user =
            $this->userRepository->findById(
                $userId
            );

        if (!$user) {
            throw new \Exception(
                'User account was created but could not be retrieved.'
            );
        }

        /*
         * Never send password hash
         * to frontend.
         */
        unset(
            $user['password_hash']
        );

        return $user;
    }

    /**
     * Generate unique username.
     *
     * Username is NOT used for login.
     * Email is used for login.
     */
    private function generateUsername(
        string $email
    ): string {

        $localPart =
            strstr(
                $email,
                '@',
                true
            );

        $localPart =
            preg_replace(
                '/[^A-Za-z0-9._-]/',
                '',
                $localPart
            );

        $localPart =
            substr(
                $localPart ?: 'employee',
                0,
                40
            );

        $username = $localPart;
        $counter = 1;

        while (true) {

            $stmt = $this->db->prepare("
                SELECT id
                FROM users
                WHERE username = :username
                LIMIT 1
            ");

            $stmt->execute([
                ':username' => $username
            ]);

            if (!$stmt->fetch()) {
                return $username;
            }

            $username =
                $localPart . $counter;

            $counter++;
        }
    }

    /**
     * Get all users for listing in User Management UI.
     */
    public function getAllUsers(): array
    {
        $users = $this->userRepository->findAll();

        // Never expose password hash
        foreach ($users as &$u) {
            unset($u['password_hash']);
        }

        return $users;
    }

    /**
     * Update an existing user's details.
     */
    public function updateUser(int $id, array $data): bool
    {
        // Resolve role_id from role name string if provided
        $roleId = $data['role_id'] ?? null;

        if (!$roleId && isset($data['role'])) {
            $roleMap = [
                'admin'    => 1,
                'hr'       => 2,
                'manager'  => 3,
                'employee' => 4,
            ];
            $roleId = $roleMap[strtolower($data['role'])] ?? 4;
        }

        $companyId = isset($data['company_id']) && $data['company_id'] !== ''
            ? (int) $data['company_id']
            : null;

        return $this->userRepository->update($id, [
            'username'   => $data['username'] ?? '',
            'email'      => $data['email']    ?? '',
            'status'     => $data['status']   ?? 'ACTIVE',
            'role_id'    => $roleId,
            'company_id' => $companyId,
        ]);
    }

    /**
     * Delete a user by ID.
     */
    public function deleteUser(int $id): bool
    {
        return $this->userRepository->delete($id);
    }
}
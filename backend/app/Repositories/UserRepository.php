<?php

namespace App\Repositories;

class UserRepository
{
    private \PDO $db;

    public function __construct(?\PDO $db = null)
    {
        if ($db === null) {
            $database = new \Database();
            $db = $database->connect();
        }
        $this->db = $db;
    }

    /**
     * Find a user by email or username.
     */
    public function findByEmailOrUsername(string $identifier): ?array
    {
        $sql = "
            SELECT
                u.id,
                u.employee_id,
                u.company_id,
                u.role_id,
                u.username,
                u.email,
                u.password_hash,
                u.status,
                u.scope_type,
                u.scope_id,
                u.must_change_password,
                u.failed_login_attempts,
                u.locked_until,
                u.last_login_at,

                r.name AS role_name,

                COALESCE(e1.full_name, e2.full_name) AS full_name,
                COALESCE(e1.employee_code, e2.employee_code) AS employee_code,
                COALESCE(e1.department, e2.department) AS department,
                COALESCE(e1.designation, e2.designation) AS designation,
                COALESCE(e1.phone, e2.phone) AS phone,
                COALESCE(e1.profile_photo, e2.profile_photo) AS profile_photo,
                COALESCE(e1.id, e2.id) AS emp_id

            FROM users u

            INNER JOIN roles r
                ON r.id = u.role_id

            LEFT JOIN employees e1
                ON e1.id = u.employee_id

            LEFT JOIN employees e2
                ON (u.employee_id IS NULL AND e2.email = u.email)

            WHERE u.email = :email OR u.username = :username

            LIMIT 1
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':email'    => $identifier,
            ':username' => $identifier
        ]);

        $user = $stmt->fetch();

        return $user ?: null;
    }

    /**
     * Find a user by email.
     */
    public function findByEmail(string $email): ?array
    {
        return $this->findByEmailOrUsername($email);
    }

    /**
     * Find a user by ID.
     */
    public function findById(int $id): ?array
    {
        $sql = "
            SELECT
                u.id,
                u.employee_id,
                u.company_id,
                u.role_id,
                u.username,
                u.email,
                u.password_hash,
                u.status,
                u.scope_type,
                u.scope_id,
                u.must_change_password,
                u.failed_login_attempts,
                u.locked_until,
                u.last_login_at,

                r.name AS role_name,

                COALESCE(e1.full_name, e2.full_name) AS full_name,
                COALESCE(e1.employee_code, e2.employee_code) AS employee_code,
                COALESCE(e1.department, e2.department) AS department,
                COALESCE(e1.designation, e2.designation) AS designation,
                COALESCE(e1.phone, e2.phone) AS phone,
                COALESCE(e1.profile_photo, e2.profile_photo) AS profile_photo,
                COALESCE(e1.id, e2.id) AS emp_id

            FROM users u

            INNER JOIN roles r
                ON r.id = u.role_id

            LEFT JOIN employees e1
                ON e1.id = u.employee_id

            LEFT JOIN employees e2
                ON (u.employee_id IS NULL AND e2.email = u.email)

            WHERE u.id = :id

            LIMIT 1
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':id' => $id
        ]);

        $user = $stmt->fetch();

        return $user ?: null;
    }

    /**
     * Update the user's last login time.
     */
    public function updateLastLogin(int $userId): bool
    {
        $sql = "
            UPDATE users
            SET
                last_login_at = NOW(),
                failed_login_attempts = 0,
                locked_until = NULL
            WHERE id = :id
        ";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            ':id' => $userId
        ]);
    }

    /**
     * Increase failed login attempts.
     */
    public function incrementFailedAttempts(int $userId): bool
    {
        $sql = "
            UPDATE users
            SET failed_login_attempts = failed_login_attempts + 1
            WHERE id = :id
        ";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            ':id' => $userId
        ]);
    }

    /**
     * Lock the account until a specified time.
     */
    public function lockAccount(int $userId, string $lockedUntil): bool
    {
        $sql = "
            UPDATE users
            SET
                status = 'LOCKED',
                locked_until = :locked_until
            WHERE id = :id
        ";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            ':id' => $userId,
            ':locked_until' => $lockedUntil
        ]);
    }

    /**
     * Get all users with role and organisation info.
     */
    public function findAll(): array
    {
        $sql = "
            SELECT
                u.id,
                u.employee_id,
                CONCAT('USR', LPAD(u.id, 3, '0')) AS user_id,
                u.username,
                u.email,
                u.status,
                u.last_login_at  AS last_login,
                u.created_at,
                u.updated_at,
                COALESCE(e.full_name, u.username) AS full_name,

                r.name           AS role,

                COALESCE(c.name, '') AS organisation_name

            FROM users u

            LEFT JOIN roles r
                ON r.id = u.role_id

            LEFT JOIN companies c
                ON c.id = u.company_id
                
            LEFT JOIN employees e
                ON e.id = u.employee_id

            ORDER BY u.created_at DESC
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Create a new user.
     */
    public function create(array $data): int
    {
        $sql = "
            INSERT INTO users
                (username, email, password_hash, status, role_id, company_id, created_at, updated_at)
            VALUES
                (:username, :email, :password_hash, :status, :role_id, :company_id, NOW(), NOW())
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':username'      => $data['username']      ?? '',
            ':email'         => $data['email']         ?? '',
            ':password_hash' => $data['password_hash'] ?? '',
            ':status'        => strtoupper($data['status'] ?? 'ACTIVE'),
            ':role_id'       => $data['role_id']       ?? null,
            ':company_id'    => $data['company_id']    ?? null,
        ]);

        return (int) $this->db->lastInsertId();
    }

    /**
     * Update an existing user.
     */
    public function update(int $id, array $data): bool
    {
        $sql = "
            UPDATE users
            SET
                username   = :username,
                email      = :email,
                status     = :status,
                role_id    = :role_id,
                company_id = :company_id,
                updated_at = NOW()
            WHERE id = :id
        ";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            ':id'         => $id,
            ':username'   => $data['username']  ?? '',
            ':email'      => $data['email']     ?? '',
            ':status'     => strtoupper($data['status'] ?? 'ACTIVE'),
            ':role_id'    => $data['role_id']   ?? null,
            ':company_id' => $data['company_id'] ?? null,
        ]);
    }

    /**
     * Delete a user by ID.
     */
    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM users WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }
}
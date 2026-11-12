<?php

namespace App\Repositories\Organisation;

use PDO;

class OrganisationRepository
{
    public function __construct(
        private PDO $db
    ) {
    }

    /**
     * Get all organisations with optional filters, search, sorting and aggregate counts
     */
    public function getAll(
        array $filters = [],
        string $sortBy = 'id',
        string $sortOrder = 'DESC'
    ): array {
        $allowedSortColumns = [
            'id' => 'c.id',
            'name' => 'c.name',
            'code' => 'c.code',
            'email' => 'c.email',
            'status' => 'c.status',
            'created_at' => 'c.created_at'
        ];

        $orderColumn = $allowedSortColumns[$sortBy] ?? 'c.id';
        $orderDirection = strtoupper($sortOrder) === 'ASC' ? 'ASC' : 'DESC';

        $whereClauses = [];
        $params = [];

        // Search filter (name, code, email, phone, city)
        if (!empty($filters['search'])) {
            $search = '%' . trim($filters['search']) . '%';
            $whereClauses[] = "(c.name LIKE :search_name OR c.code LIKE :search_code OR c.email LIKE :search_email OR c.phone LIKE :search_phone OR c.city LIKE :search_city)";
            $params[':search_name'] = $search;
            $params[':search_code'] = $search;
            $params[':search_email'] = $search;
            $params[':search_phone'] = $search;
            $params[':search_city'] = $search;
        }

        // Status filter
        if (!empty($filters['status'])) {
            $status = strtoupper(trim($filters['status']));
            if (in_array($status, ['ACTIVE', 'INACTIVE'])) {
                $whereClauses[] = "c.status = :status";
                $params[':status'] = $status;
            }
        }

        $whereSql = !empty($whereClauses) ? 'WHERE ' . implode(' AND ', $whereClauses) : '';

        $sql = "
            SELECT
                c.id,
                c.name,
                c.code,
                c.email,
                c.phone,
                c.address,
                c.city,
                c.state,
                c.country,
                c.status,
                c.created_at,
                c.updated_at,
                0 AS branches_count,
                (SELECT COUNT(*) FROM departments d WHERE d.company_id = c.id) AS departments_count,
                (SELECT COUNT(*) FROM users u WHERE u.company_id = c.id) AS users_count,
                (SELECT COUNT(DISTINCT u.employee_id) FROM users u WHERE u.company_id = c.id AND u.employee_id IS NOT NULL) AS employees_count
            FROM companies c
            {$whereSql}
            ORDER BY {$orderColumn} {$orderDirection}
        ";

        $statement = $this->db->prepare($sql);
        $statement->execute($params);

        $results = $statement->fetchAll(PDO::FETCH_ASSOC);

        // Format numerical counts to integer
        return array_map(function ($row) {
            $row['id'] = (int) $row['id'];
            $row['branches_count'] = (int) $row['branches_count'];
            $row['departments_count'] = (int) $row['departments_count'];
            $row['users_count'] = (int) $row['users_count'];
            $row['employees_count'] = (int) $row['employees_count'];
            return $row;
        }, $results);
    }

    /**
     * Find organisation by ID with aggregate counts
     */
    public function findById(int $id): ?array
    {
        $sql = "
            SELECT
                c.id,
                c.name,
                c.code,
                c.email,
                c.phone,
                c.address,
                c.city,
                c.state,
                c.country,
                c.status,
                c.created_at,
                c.updated_at,
                0 AS branches_count,
                (SELECT COUNT(*) FROM departments d WHERE d.company_id = c.id) AS departments_count,
                (SELECT COUNT(*) FROM users u WHERE u.company_id = c.id) AS users_count,
                (SELECT COUNT(DISTINCT u.employee_id) FROM users u WHERE u.company_id = c.id AND u.employee_id IS NOT NULL) AS employees_count
            FROM companies c
            WHERE c.id = :id
            LIMIT 1
        ";

        $statement = $this->db->prepare($sql);
        $statement->execute([':id' => $id]);

        $result = $statement->fetch(PDO::FETCH_ASSOC);
        if (!$result) {
            return null;
        }

        $result['id'] = (int) $result['id'];
        $result['branches_count'] = (int) $result['branches_count'];
        $result['departments_count'] = (int) $result['departments_count'];
        $result['users_count'] = (int) $result['users_count'];
        $result['employees_count'] = (int) $result['employees_count'];

        return $result;
    }

    /**
     * Find organisation by code (with optional exclusion for updates)
     */
    public function findByCode(string $code, ?int $excludeId = null): ?array
    {
        $sql = "SELECT id, name, code FROM companies WHERE UPPER(code) = UPPER(:code)";
        $params = [':code' => trim($code)];

        if ($excludeId !== null) {
            $sql .= " AND id != :exclude_id";
            $params[':exclude_id'] = $excludeId;
        }

        $sql .= " LIMIT 1";

        $statement = $this->db->prepare($sql);
        $statement->execute($params);

        $result = $statement->fetch(PDO::FETCH_ASSOC);
        return $result ?: null;
    }

    /**
     * Create a new organisation
     */
    public function create(array $data): int
    {
        $sql = "
            INSERT INTO companies (
                name,
                code,
                email,
                phone,
                address,
                city,
                state,
                country,
                status
            ) VALUES (
                :name,
                :code,
                :email,
                :phone,
                :address,
                :city,
                :state,
                :country,
                :status
            )
        ";

        $statement = $this->db->prepare($sql);
        $statement->execute([
            ':name' => trim($data['name']),
            ':code' => strtoupper(trim($data['code'])),
            ':email' => !empty($data['email']) ? trim($data['email']) : null,
            ':phone' => !empty($data['phone']) ? trim($data['phone']) : null,
            ':address' => !empty($data['address']) ? trim($data['address']) : null,
            ':city' => !empty($data['city']) ? trim($data['city']) : null,
            ':state' => !empty($data['state']) ? trim($data['state']) : null,
            ':country' => !empty($data['country']) ? trim($data['country']) : 'India',
            ':status' => in_array(strtoupper($data['status'] ?? ''), ['ACTIVE', 'INACTIVE']) ? strtoupper($data['status']) : 'ACTIVE'
        ]);

        return (int) $this->db->lastInsertId();
    }

    /**
     * Update an organisation
     */
    public function update(int $id, array $data): bool
    {
        $sql = "
            UPDATE companies
            SET
                name = :name,
                code = :code,
                email = :email,
                phone = :phone,
                address = :address,
                city = :city,
                state = :state,
                country = :country,
                status = :status
            WHERE id = :id
        ";

        $statement = $this->db->prepare($sql);
        return $statement->execute([
            ':id' => $id,
            ':name' => trim($data['name']),
            ':code' => strtoupper(trim($data['code'])),
            ':email' => !empty($data['email']) ? trim($data['email']) : null,
            ':phone' => !empty($data['phone']) ? trim($data['phone']) : null,
            ':address' => !empty($data['address']) ? trim($data['address']) : null,
            ':city' => !empty($data['city']) ? trim($data['city']) : null,
            ':state' => !empty($data['state']) ? trim($data['state']) : null,
            ':country' => !empty($data['country']) ? trim($data['country']) : 'India',
            ':status' => in_array(strtoupper($data['status'] ?? ''), ['ACTIVE', 'INACTIVE']) ? strtoupper($data['status']) : 'ACTIVE'
        ]);
    }

    /**
     * Update organisation status
     */
    public function updateStatus(int $id, string $status): bool
    {
        $sql = "UPDATE companies SET status = :status WHERE id = :id";
        $statement = $this->db->prepare($sql);
        return $statement->execute([
            ':id' => $id,
            ':status' => strtoupper($status)
        ]);
    }

    /**
     * Delete an organisation
     */
    public function delete(int $id): bool
    {
        $sql = "DELETE FROM companies WHERE id = :id";
        $statement = $this->db->prepare($sql);
        return $statement->execute([':id' => $id]);
    }

    /**
     * Get departments belonging to an organisation
     */
    public function getDepartmentsByCompanyId(int $companyId): array
    {
        $sql = "
            SELECT
                d.id,
                d.name,
                d.company_id,
                d.status,
                d.created_at,
                d.updated_at,
                (SELECT COUNT(*) FROM designations des WHERE des.department_id = d.id) AS designations_count,
                (SELECT COUNT(*) FROM employees e WHERE e.department = d.name) AS employees_count
            FROM departments d
            WHERE d.company_id = :company_id
            ORDER BY d.name ASC
        ";

        $statement = $this->db->prepare($sql);
        $statement->execute([':company_id' => $companyId]);

        $results = $statement->fetchAll(PDO::FETCH_ASSOC);
        return array_map(function ($row) {
            $row['id'] = (int) $row['id'];
            $row['company_id'] = $row['company_id'] !== null ? (int) $row['company_id'] : null;
            $row['designations_count'] = (int) $row['designations_count'];
            $row['employees_count'] = (int) $row['employees_count'];
            return $row;
        }, $results);
    }

    /**
     * Get users belonging to an organisation
     */
    public function getUsersByCompanyId(int $companyId): array
    {
        $sql = "
            SELECT
                u.id,
                u.employee_id,
                u.company_id,
                u.role_id,
                u.username,
                u.email,
                u.status,
                u.last_login_at,
                u.created_at,
                r.name AS role_name,
                e.full_name AS employee_name,
                e.employee_code,
                e.department,
                e.designation
            FROM users u
            INNER JOIN roles r ON r.id = u.role_id
            LEFT JOIN employees e ON e.id = u.employee_id
            WHERE u.company_id = :company_id
            ORDER BY u.id DESC
        ";

        $statement = $this->db->prepare($sql);
        $statement->execute([':company_id' => $companyId]);

        $results = $statement->fetchAll(PDO::FETCH_ASSOC);
        return array_map(function ($row) {
            $row['id'] = (int) $row['id'];
            $row['employee_id'] = $row['employee_id'] !== null ? (int) $row['employee_id'] : null;
            $row['company_id'] = $row['company_id'] !== null ? (int) $row['company_id'] : null;
            $row['role_id'] = (int) $row['role_id'];
            return $row;
        }, $results);
    }

    /**
     * Get complete organisation structure:
     * Organisation -> Departments -> Designations -> Employees
     */
    public function getStructure(int $companyId): ?array
    {
        $organisation = $this->findById($companyId);
        if (!$organisation) {
            return null;
        }

        // Get departments
        $departments = $this->getDepartmentsByCompanyId($companyId);

        // For each department, get designations and employees
        foreach ($departments as &$dept) {
            $deptId = (int) $dept['id'];
            $deptName = $dept['name'];

            // Get designations
            $desSql = "
                SELECT
                    des.id,
                    des.name,
                    des.status,
                    (SELECT COUNT(*) FROM employees e WHERE e.department = :dept_name AND e.designation = des.name) AS employees_count
                FROM designations des
                WHERE des.department_id = :dept_id
                ORDER BY des.name ASC
            ";
            $desStmt = $this->db->prepare($desSql);
            $desStmt->execute([
                ':dept_name' => $deptName,
                ':dept_id' => $deptId
            ]);
            $designations = $desStmt->fetchAll(PDO::FETCH_ASSOC);

            // For each designation, get employees
            foreach ($designations as &$des) {
                $des['id'] = (int) $des['id'];
                $des['employees_count'] = (int) $des['employees_count'];

                $empSql = "
                    SELECT
                        e.id,
                        e.employee_code,
                        e.full_name,
                        e.email,
                        e.phone,
                        e.status,
                        e.profile_photo,
                        e.joining_date
                    FROM employees e
                    WHERE e.department = :dept_name
                      AND e.designation = :des_name
                    ORDER BY e.full_name ASC
                ";
                $empStmt = $this->db->prepare($empSql);
                $empStmt->execute([
                    ':dept_name' => $deptName,
                    ':des_name' => $des['name']
                ]);
                $employees = $empStmt->fetchAll(PDO::FETCH_ASSOC);
                $des['employees'] = array_map(function ($e) {
                    $e['id'] = (int) $e['id'];
                    return $e;
                }, $employees);
            }

            $dept['designations'] = $designations;
        }

        return [
            'organisation' => $organisation,
            'departments' => $departments
        ];
    }
}

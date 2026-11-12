<?php

namespace App\Repositories\Department;

use PDO;

class DepartmentRepository
{
    public function __construct(
        private PDO $db
    ) {
    }

    /**
     * Get all departments with filters, search, and aggregate counts
     */
    public function getAll(array $filters = []): array
    {
        $whereClauses = [];
        $params = [];

        // Filter by company_id
        if (!empty($filters['company_id'])) {
            $whereClauses[] = "d.company_id = :company_id";
            $params[':company_id'] = (int) $filters['company_id'];
        }

        // Filter by status
        if (!empty($filters['status'])) {
            $status = strtoupper(trim($filters['status']));
            if (in_array($status, ['ACTIVE', 'INACTIVE'])) {
                $whereClauses[] = "d.status = :status";
                $params[':status'] = $status;
            }
        }

        // Search by name
        if (!empty($filters['search'])) {
            $whereClauses[] = "d.name LIKE :search";
            $params[':search'] = '%' . trim($filters['search']) . '%';
        }

        $whereSql = !empty($whereClauses) ? 'WHERE ' . implode(' AND ', $whereClauses) : '';

        $sql = "
            SELECT
                d.id,
                d.name,
                d.company_id,
                c.name AS company_name,
                c.code AS company_code,
                d.status,
                d.created_at,
                d.updated_at,
                (SELECT COUNT(*) FROM designations des WHERE des.department_id = d.id) AS designations_count,
                (SELECT COUNT(*) FROM employees e WHERE e.department = d.name) AS employees_count
            FROM departments d
            LEFT JOIN companies c ON c.id = d.company_id
            {$whereSql}
            ORDER BY d.name ASC
        ";

        $statement = $this->db->prepare($sql);
        $statement->execute($params);

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
     * Get all active departments (optionally filtered by company)
     */
    public function getAllActive(?int $companyId = null): array
    {
        $sql = "
            SELECT
                d.id,
                d.name,
                d.company_id,
                c.name AS company_name,
                d.status,
                d.created_at,
                d.updated_at
            FROM departments d
            LEFT JOIN companies c ON c.id = d.company_id
            WHERE d.status = 'ACTIVE'
        ";

        $params = [];
        if ($companyId !== null && $companyId > 0) {
            $sql .= " AND (d.company_id = :company_id OR d.company_id IS NULL)";
            $params[':company_id'] = $companyId;
        }

        $sql .= " ORDER BY d.name ASC";

        $statement = $this->db->prepare($sql);
        $statement->execute($params);

        $results = $statement->fetchAll(PDO::FETCH_ASSOC);
        return array_map(function ($row) {
            $row['id'] = (int) $row['id'];
            $row['company_id'] = $row['company_id'] !== null ? (int) $row['company_id'] : null;
            return $row;
        }, $results);
    }

    /**
     * Find department by ID
     */
    public function findById(int $id): ?array
    {
        $sql = "
            SELECT
                d.id,
                d.name,
                d.company_id,
                c.name AS company_name,
                c.code AS company_code,
                d.status,
                d.created_at,
                d.updated_at,
                (SELECT COUNT(*) FROM designations des WHERE des.department_id = d.id) AS designations_count,
                (SELECT COUNT(*) FROM employees e WHERE e.department = d.name) AS employees_count
            FROM departments d
            LEFT JOIN companies c ON c.id = d.company_id
            WHERE d.id = :id
            LIMIT 1
        ";

        $statement = $this->db->prepare($sql);
        $statement->execute([':id' => $id]);

        $department = $statement->fetch(PDO::FETCH_ASSOC);
        if (!$department) {
            return null;
        }

        $department['id'] = (int) $department['id'];
        $department['company_id'] = $department['company_id'] !== null ? (int) $department['company_id'] : null;
        $department['designations_count'] = (int) $department['designations_count'];
        $department['employees_count'] = (int) $department['employees_count'];

        return $department;
    }

    /**
     * Find department by name
     */
    public function findByName(string $name, ?int $companyId = null, ?int $excludeId = null): ?array
    {
        $sql = "SELECT id, name, company_id FROM departments WHERE UPPER(name) = UPPER(:name)";
        $params = [':name' => trim($name)];

        if ($companyId !== null) {
            $sql .= " AND company_id = :company_id";
            $params[':company_id'] = $companyId;
        }

        if ($excludeId !== null) {
            $sql .= " AND id != :exclude_id";
            $params[':exclude_id'] = $excludeId;
        }

        $sql .= " LIMIT 1";

        $statement = $this->db->prepare($sql);
        $statement->execute($params);

        $department = $statement->fetch(PDO::FETCH_ASSOC);
        return $department ?: null;
    }

    /**
     * Create department
     */
    public function create(array $data): int
    {
        $sql = "
            INSERT INTO departments (
                name,
                company_id,
                status
            ) VALUES (
                :name,
                :company_id,
                :status
            )
        ";

        $statement = $this->db->prepare($sql);
        $statement->execute([
            ':name' => trim($data['name']),
            ':company_id' => !empty($data['company_id']) ? (int) $data['company_id'] : 1,
            ':status' => in_array(strtoupper($data['status'] ?? ''), ['ACTIVE', 'INACTIVE']) ? strtoupper($data['status']) : 'ACTIVE'
        ]);

        return (int) $this->db->lastInsertId();
    }

    /**
     * Update department
     */
    public function update(int $id, array $data): bool
    {
        $sql = "
            UPDATE departments
            SET
                name = :name,
                company_id = :company_id,
                status = :status
            WHERE id = :id
        ";

        $statement = $this->db->prepare($sql);
        return $statement->execute([
            ':id' => $id,
            ':name' => trim($data['name']),
            ':company_id' => !empty($data['company_id']) ? (int) $data['company_id'] : 1,
            ':status' => in_array(strtoupper($data['status'] ?? ''), ['ACTIVE', 'INACTIVE']) ? strtoupper($data['status']) : 'ACTIVE'
        ]);
    }

    /**
     * Update department status
     */
    public function updateStatus(int $id, string $status): bool
    {
        $sql = "UPDATE departments SET status = :status WHERE id = :id";
        $statement = $this->db->prepare($sql);
        return $statement->execute([
            ':id' => $id,
            ':status' => strtoupper($status)
        ]);
    }

    /**
     * Delete department
     */
    public function delete(int $id): bool
    {
        $sql = "DELETE FROM departments WHERE id = :id";
        $statement = $this->db->prepare($sql);
        return $statement->execute([':id' => $id]);
    }

    /**
     * Get designations under a department
     */
    public function getDesignations(int $departmentId): array
    {
        $sql = "
            SELECT
                id,
                department_id,
                name,
                status,
                created_at,
                updated_at
            FROM designations
            WHERE department_id = :department_id
            ORDER BY name ASC
        ";

        $statement = $this->db->prepare($sql);
        $statement->execute([':department_id' => $departmentId]);

        $results = $statement->fetchAll(PDO::FETCH_ASSOC);
        return array_map(function ($row) {
            $row['id'] = (int) $row['id'];
            $row['department_id'] = (int) $row['department_id'];
            return $row;
        }, $results);
    }
}
<?php

namespace App\Repositories\Designation;

use PDO;

class DesignationRepository
{
    public function __construct(
        private PDO $db
    ) {
    }

    /**
     * Get all designations with optional filters and counts
     */
    public function getAll(array $filters = []): array
    {
        $whereClauses = [];
        $params = [];

        // Filter by department_id
        if (!empty($filters['department_id'])) {
            $whereClauses[] = "des.department_id = :department_id";
            $params[':department_id'] = (int) $filters['department_id'];
        }

        // Filter by status
        if (!empty($filters['status'])) {
            $status = strtoupper(trim($filters['status']));
            if (in_array($status, ['ACTIVE', 'INACTIVE'])) {
                $whereClauses[] = "des.status = :status";
                $params[':status'] = $status;
            }
        }

        // Search by name
        if (!empty($filters['search'])) {
            $whereClauses[] = "des.name LIKE :search";
            $params[':search'] = '%' . trim($filters['search']) . '%';
        }

        $whereSql = !empty($whereClauses) ? 'WHERE ' . implode(' AND ', $whereClauses) : '';

        $sql = "
            SELECT
                des.id,
                des.department_id,
                dept.name AS department_name,
                des.name,
                des.status,
                des.created_at,
                des.updated_at,
                (SELECT COUNT(*) FROM employees e WHERE e.department = dept.name AND e.designation = des.name) AS employees_count
            FROM designations des
            INNER JOIN departments dept ON dept.id = des.department_id
            {$whereSql}
            ORDER BY des.name ASC
        ";

        $statement = $this->db->prepare($sql);
        $statement->execute($params);

        $results = $statement->fetchAll(PDO::FETCH_ASSOC);
        return array_map(function ($row) {
            $row['id'] = (int) $row['id'];
            $row['department_id'] = (int) $row['department_id'];
            $row['employees_count'] = (int) $row['employees_count'];
            return $row;
        }, $results);
    }

    /**
     * Get all active designations
     */
    public function getAllActive(): array
    {
        $sql = "
            SELECT
                des.id,
                des.department_id,
                dept.name AS department_name,
                des.name,
                des.status,
                des.created_at,
                des.updated_at
            FROM designations des
            INNER JOIN departments dept ON dept.id = des.department_id
            WHERE des.status = 'ACTIVE'
            ORDER BY des.name ASC
        ";

        $statement = $this->db->query($sql);
        $results = $statement->fetchAll(PDO::FETCH_ASSOC);

        return array_map(function ($row) {
            $row['id'] = (int) $row['id'];
            $row['department_id'] = (int) $row['department_id'];
            return $row;
        }, $results);
    }

    /**
     * Get active designations belonging to a department
     */
    public function getByDepartmentId(int $departmentId): array
    {
        $sql = "
            SELECT
                des.id,
                des.department_id,
                dept.name AS department_name,
                des.name,
                des.status,
                des.created_at,
                des.updated_at,
                (SELECT COUNT(*) FROM employees e WHERE e.department = dept.name AND e.designation = des.name) AS employees_count
            FROM designations des
            INNER JOIN departments dept ON dept.id = des.department_id
            WHERE des.department_id = :department_id
              AND des.status = 'ACTIVE'
            ORDER BY des.name ASC
        ";

        $statement = $this->db->prepare($sql);
        $statement->execute([
            ':department_id' => $departmentId
        ]);

        $results = $statement->fetchAll(PDO::FETCH_ASSOC);
        return array_map(function ($row) {
            $row['id'] = (int) $row['id'];
            $row['department_id'] = (int) $row['department_id'];
            $row['employees_count'] = (int) $row['employees_count'];
            return $row;
        }, $results);
    }

    /**
     * Find designation by ID
     */
    public function findById(int $id): ?array
    {
        $sql = "
            SELECT
                des.id,
                des.department_id,
                dept.name AS department_name,
                des.name,
                des.status,
                des.created_at,
                des.updated_at,
                (SELECT COUNT(*) FROM employees e WHERE e.department = dept.name AND e.designation = des.name) AS employees_count
            FROM designations des
            INNER JOIN departments dept ON dept.id = des.department_id
            WHERE des.id = :id
            LIMIT 1
        ";

        $statement = $this->db->prepare($sql);
        $statement->execute([
            ':id' => $id
        ]);

        $designation = $statement->fetch(PDO::FETCH_ASSOC);
        if (!$designation) {
            return null;
        }

        $designation['id'] = (int) $designation['id'];
        $designation['department_id'] = (int) $designation['department_id'];
        $designation['employees_count'] = (int) $designation['employees_count'];

        return $designation;
    }

    /**
     * Find designation by name and department
     */
    public function findByNameAndDepartment(string $name, int $departmentId, ?int $excludeId = null): ?array
    {
        $sql = "SELECT id, department_id, name FROM designations WHERE UPPER(name) = UPPER(:name) AND department_id = :department_id";
        $params = [
            ':name' => trim($name),
            ':department_id' => $departmentId
        ];

        if ($excludeId !== null) {
            $sql .= " AND id != :exclude_id";
            $params[':exclude_id'] = $excludeId;
        }

        $sql .= " LIMIT 1";

        $statement = $this->db->prepare($sql);
        $statement->execute($params);

        $designation = $statement->fetch(PDO::FETCH_ASSOC);
        return $designation ?: null;
    }

    /**
     * Create designation
     */
    public function create(array $data): int
    {
        $sql = "
            INSERT INTO designations (
                department_id,
                name,
                status
            ) VALUES (
                :department_id,
                :name,
                :status
            )
        ";

        $statement = $this->db->prepare($sql);
        $statement->execute([
            ':department_id' => (int) $data['department_id'],
            ':name' => trim($data['name']),
            ':status' => in_array(strtoupper($data['status'] ?? ''), ['ACTIVE', 'INACTIVE']) ? strtoupper($data['status']) : 'ACTIVE'
        ]);

        return (int) $this->db->lastInsertId();
    }

    /**
     * Update designation
     */
    public function update(int $id, array $data): bool
    {
        $sql = "
            UPDATE designations
            SET
                department_id = :department_id,
                name = :name,
                status = :status
            WHERE id = :id
        ";

        $statement = $this->db->prepare($sql);
        return $statement->execute([
            ':id' => $id,
            ':department_id' => (int) $data['department_id'],
            ':name' => trim($data['name']),
            ':status' => in_array(strtoupper($data['status'] ?? ''), ['ACTIVE', 'INACTIVE']) ? strtoupper($data['status']) : 'ACTIVE'
        ]);
    }

    /**
     * Update designation status
     */
    public function updateStatus(int $id, string $status): bool
    {
        $sql = "UPDATE designations SET status = :status WHERE id = :id";
        $statement = $this->db->prepare($sql);
        return $statement->execute([
            ':id' => $id,
            ':status' => strtoupper($status)
        ]);
    }

    /**
     * Delete designation
     */
    public function delete(int $id): bool
    {
        $sql = "DELETE FROM designations WHERE id = :id";
        $statement = $this->db->prepare($sql);
        return $statement->execute([':id' => $id]);
    }
}
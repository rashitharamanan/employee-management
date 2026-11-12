<?php

namespace App\Repositories\Hr;

use PDO;

class HrEmployeeRepository
{
    public function __construct(private PDO $db)
    {
    }

    public function search(array $filters): array
    {
        $where = [];
        $params = [];
        $this->addFilters($where, $params, $filters);

        $page = max(1, (int) ($filters['page'] ?? 1));
        $perPage = min(100, max(1, (int) ($filters['per_page'] ?? 10)));
        $offset = ($page - 1) * $perPage;
        $whereSql = $where ? ' WHERE ' . implode(' AND ', $where) : '';

        $count = $this->db->prepare("SELECT COUNT(*) FROM employees{$whereSql}");
        $count->execute($params);
        $total = (int) $count->fetchColumn();

        $sql = "SELECT id, employee_code, full_name, profile_photo, email, phone,
                       department, designation, joining_date, employment_type, status
                FROM employees{$whereSql}
                ORDER BY joining_date DESC, id DESC
                LIMIT :limit OFFSET :offset";
        $statement = $this->db->prepare($sql);
        foreach ($params as $key => $value) {
            $statement->bindValue($key, $value);
        }
        $statement->bindValue(':limit', $perPage, PDO::PARAM_INT);
        $statement->bindValue(':offset', $offset, PDO::PARAM_INT);
        $statement->execute();

        return [
            'employees' => $statement->fetchAll(PDO::FETCH_ASSOC),
            'pagination' => [
                'page' => $page,
                'per_page' => $perPage,
                'total' => $total,
                'total_pages' => $total > 0 ? (int) ceil($total / $perPage) : 0,
            ],
        ];
    }

    public function getSummary(): array
    {
        $row = $this->db->query(
            "SELECT COUNT(*) AS total,
                    SUM(status = 'ACTIVE') AS active,
                    SUM(status = 'INACTIVE') AS inactive,
                    SUM(status = 'SUSPENDED') AS suspended,
                    SUM(joining_date >= DATE_FORMAT(CURDATE(), '%Y-%m-01')) AS new_this_month
             FROM employees"
        )->fetch(PDO::FETCH_ASSOC) ?: [];

        return [
            'total_employees' => (int) ($row['total'] ?? 0),
            'active_employees' => (int) ($row['active'] ?? 0),
            'inactive_employees' => (int) ($row['inactive'] ?? 0),
            'suspended_employees' => (int) ($row['suspended'] ?? 0),
            'new_joiners_this_month' => (int) ($row['new_this_month'] ?? 0),
            'departments' => (int) $this->db->query("SELECT COUNT(DISTINCT NULLIF(TRIM(department), '')) FROM employees")->fetchColumn(),
            'designations' => (int) $this->db->query("SELECT COUNT(DISTINCT NULLIF(TRIM(designation), '')) FROM employees")->fetchColumn(),
        ];
    }

    public function getDepartmentBreakdown(): array
    {
        return $this->db->query(
            "SELECT COALESCE(NULLIF(TRIM(department), ''), 'Unassigned') AS department,
                    COUNT(*) AS employee_count,
                    ROUND(COUNT(*) * 100 / NULLIF((SELECT COUNT(*) FROM employees), 0), 1) AS percentage
             FROM employees GROUP BY COALESCE(NULLIF(TRIM(department), ''), 'Unassigned')
             ORDER BY employee_count DESC"
        )->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getEmploymentTypeBreakdown(): array
    {
        return $this->db->query(
            "SELECT COALESCE(NULLIF(TRIM(employment_type), ''), 'Unspecified') AS employment_type,
                    COUNT(*) AS employee_count,
                    ROUND(COUNT(*) * 100 / NULLIF((SELECT COUNT(*) FROM employees), 0), 1) AS percentage
             FROM employees GROUP BY COALESCE(NULLIF(TRIM(employment_type), ''), 'Unspecified')
             ORDER BY employee_count DESC"
        )->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getDesignations(): array
    {
        return $this->db->query(
            "SELECT DISTINCT designation FROM employees
             WHERE NULLIF(TRIM(designation), '') IS NOT NULL
             ORDER BY designation"
        )->fetchAll(PDO::FETCH_COLUMN);
    }

    public function getRecentJoiners(int $limit = 5): array
    {
        $limit = min(20, max(1, $limit));
        $statement = $this->db->query(
            "SELECT id, employee_code, full_name, designation, joining_date, profile_photo
             FROM employees ORDER BY joining_date DESC, id DESC LIMIT {$limit}"
        );
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    private function addFilters(array &$where, array &$params, array $filters): void
    {
        if (!empty($filters['search'])) {
            $where[] = '(full_name LIKE :search OR employee_code LIKE :search OR email LIKE :search OR phone LIKE :search)';
            $params[':search'] = '%' . trim((string) $filters['search']) . '%';
        }
        foreach (['status', 'department', 'designation', 'employment_type'] as $field) {
            if (!empty($filters[$field])) {
                $where[] = "{$field} = :{$field}";
                $params[":{$field}"] = trim((string) $filters[$field]);
            }
        }
    }
}

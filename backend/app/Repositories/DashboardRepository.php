<?php

namespace App\Repositories;

use PDO;

class DashboardRepository
{
    public function __construct(private PDO $db)
    {
    }

    public function getSummary(): array
    {
        $employee = $this->db->query(
            "SELECT COUNT(*) AS total,
                    SUM(status = 'ACTIVE') AS active,
                    SUM(status = 'INACTIVE') AS inactive,
                    SUM(status = 'SUSPENDED') AS suspended,
                    SUM(status = 'ON_LEAVE') AS on_leave,
                    SUM(YEAR(created_at) = YEAR(CURDATE()) AND MONTH(created_at) = MONTH(CURDATE())) AS new_this_month
             FROM employees"
        )->fetch(PDO::FETCH_ASSOC) ?: [];

        $departmentCount = $this->db->query(
            "SELECT COUNT(DISTINCT NULLIF(TRIM(department), '')) FROM employees"
        )->fetchColumn();

        return [
            'total_employees' => (int) ($employee['total'] ?? 0),
            'active_employees' => (int) ($employee['active'] ?? 0),
            'inactive_employees' => (int) ($employee['inactive'] ?? 0),
            'suspended_employees' => (int) ($employee['suspended'] ?? 0),
            'on_leave_employees' => (int) ($employee['on_leave'] ?? 0),
            'new_this_month' => (int) ($employee['new_this_month'] ?? 0),
            'total_departments' => (int) $departmentCount,
            'total_users' => (int) $this->db->query('SELECT COUNT(*) FROM users')->fetchColumn(),
            'total_roles' => (int) $this->db->query('SELECT COUNT(*) FROM roles')->fetchColumn(),
            'total_permissions' => (int) $this->db->query('SELECT COUNT(*) FROM permissions')->fetchColumn(),
            'database_size_bytes' => $this->databaseSizeBytes()
        ];
    }

    public function getDepartmentBreakdown(): array
    {
        $statement = $this->db->query(
            "SELECT COALESCE(NULLIF(TRIM(department), ''), 'Unassigned') AS department,
                    COUNT(*) AS employee_count,
                    ROUND(COUNT(*) * 100 / NULLIF((SELECT COUNT(*) FROM employees), 0), 1) AS percentage
             FROM employees
             GROUP BY COALESCE(NULLIF(TRIM(department), ''), 'Unassigned')
             ORDER BY employee_count DESC"
        );
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getEmployeeTrend(): array
    {
        $statement = $this->db->query(
            "SELECT DATE_FORMAT(created_at, '%Y-%m') AS month,
                    COUNT(*) AS employee_count
             FROM employees
             WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)
             GROUP BY DATE_FORMAT(created_at, '%Y-%m')
             ORDER BY month"
        );
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getRecentEmployees(int $limit = 5): array
    {
        $limit = max(1, min($limit, 20));
        $statement = $this->db->query(
            "SELECT id, employee_code, full_name, department, designation, status, created_at
             FROM employees ORDER BY created_at DESC, id DESC LIMIT {$limit}"
        );
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    private function databaseSizeBytes(): int
    {
        $statement = $this->db->query(
            "SELECT COALESCE(SUM(data_length + index_length), 0)
             FROM information_schema.tables
             WHERE table_schema = DATABASE()"
        );
        return (int) $statement->fetchColumn();
    }
}

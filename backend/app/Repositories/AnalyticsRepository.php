<?php

namespace App\Repositories;

use PDO;

class AnalyticsRepository
{
    public function __construct(private PDO $db)
    {
    }

    public function ensureSchema(): void
    {
        $this->db->exec(<<<'SQL'
            CREATE TABLE IF NOT EXISTS attendance_records (
                id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                employee_id INT UNSIGNED NOT NULL,
                attendance_date DATE NOT NULL,
                check_in DATETIME NULL,
                check_out DATETIME NULL,
                status ENUM('PRESENT', 'ABSENT', 'LATE', 'LEAVE') NOT NULL DEFAULT 'PRESENT',
                created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                UNIQUE KEY uq_attendance_employee_date (employee_id, attendance_date),
                KEY idx_attendance_date (attendance_date),
                KEY idx_attendance_status (status)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
        SQL);

        $this->db->exec(<<<'SQL'
            CREATE TABLE IF NOT EXISTS generated_reports (
                id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                report_name VARCHAR(160) NOT NULL,
                category VARCHAR(60) NOT NULL DEFAULT 'General',
                generated_by VARCHAR(120) NOT NULL DEFAULT 'System',
                generated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                report_format ENUM('PDF', 'EXCEL', 'CSV') NOT NULL DEFAULT 'PDF',
                date_from DATE NULL,
                date_to DATE NULL,
                created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                KEY idx_reports_generated_at (generated_at)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
        SQL);
    }

    public function getSummary(?string $from, ?string $to): array
    {
        $params = $this->dateParams($from, $to);
        $employee = $this->db->query(
            "SELECT COUNT(*) AS total_employees,
                    SUM(status = 'ACTIVE') AS active_employees
             FROM employees"
        )->fetch(PDO::FETCH_ASSOC) ?: [];

        $attendance = $this->db->prepare(
            "SELECT COUNT(*) AS total_records,
                    SUM(status = 'PRESENT') AS present,
                    SUM(status = 'ABSENT') AS absent,
                    SUM(status = 'LATE') AS late
             FROM attendance_records
             WHERE attendance_date BETWEEN :from AND :to"
        );
        $attendance->execute($params);
        $attendance = $attendance->fetch(PDO::FETCH_ASSOC) ?: [];

        $reports = $this->db->query('SELECT COUNT(*) FROM generated_reports')->fetchColumn();
        $present = (int) ($attendance['present'] ?? 0);
        $records = (int) ($attendance['total_records'] ?? 0);

        return [
            'total_employees' => (int) ($employee['total_employees'] ?? 0),
            'active_employees' => (int) ($employee['active_employees'] ?? 0),
            'attendance_rate' => $records > 0 ? round(($present / $records) * 100, 1) : 0,
            'total_reports' => (int) $reports,
            'attendance_records' => $records,
            'present' => $present,
            'absent' => (int) ($attendance['absent'] ?? 0),
            'late' => (int) ($attendance['late'] ?? 0),
            'date_from' => $params[':from'],
            'date_to' => $params[':to']
        ];
    }

    public function getEmployeeTrend(string $from, string $to): array
    {
        $statement = $this->db->prepare(
            "SELECT attendance_date AS date,
                    SUM(status = 'PRESENT') AS new_employees,
                    SUM(status IN ('ABSENT', 'LATE')) AS inactive_employees
             FROM attendance_records
             WHERE attendance_date BETWEEN :from AND :to
             GROUP BY attendance_date
             ORDER BY attendance_date"
        );
        $statement->execute([':from' => $from, ':to' => $to]);
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getDepartmentDistribution(): array
    {
        $statement = $this->db->query(
            "SELECT d.name AS department,
                    COUNT(e.id) AS employees,
                    ROUND(COUNT(e.id) * 100 / NULLIF((SELECT COUNT(*) FROM employees), 0), 1) AS percentage
             FROM departments d
             LEFT JOIN employees e ON (e.department = d.name)
             WHERE d.name NOT LIKE 'Test%'
             GROUP BY d.id, d.name
             ORDER BY employees DESC, d.name ASC
             LIMIT 5"
        );
        $results = $statement->fetchAll(PDO::FETCH_ASSOC);

        if (empty($results) || count($results) < 5) {
            $defaultDepts = ['IT', 'HR', 'Finance', 'Marketing', 'Sales'];
            $map = [];
            foreach ($results as $r) {
                $map[$r['department']] = $r;
            }
            $final = [];
            foreach ($defaultDepts as $dName) {
                $final[] = $map[$dName] ?? [
                    'department' => $dName,
                    'employees' => 0,
                    'percentage' => 0
                ];
            }
            return $final;
        }

        return $results;
    }

    public function getRecentReports(int $limit = 10): array
    {
        $limit = max(1, min($limit, 50));
        $statement = $this->db->query(
            "SELECT id, report_name, category, generated_by, generated_at, report_format, date_from, date_to
             FROM generated_reports ORDER BY generated_at DESC LIMIT {$limit}"
        );
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    public function createReport(array $data): int
    {
        $statement = $this->db->prepare(
            'INSERT INTO generated_reports (report_name, category, generated_by, report_format, date_from, date_to) VALUES (:report_name, :category, :generated_by, :report_format, :date_from, :date_to)'
        );
        $statement->execute([
            ':report_name' => $data['report_name'],
            ':category' => $data['category'],
            ':generated_by' => $data['generated_by'],
            ':report_format' => $data['report_format'],
            ':date_from' => $data['date_from'],
            ':date_to' => $data['date_to']
        ]);
        return (int) $this->db->lastInsertId();
    }

    private function dateParams(?string $from, ?string $to): array
    {
        $defaultTo = date('Y-m-d');
        $defaultFrom = date('Y-m-d', strtotime('-6 days'));
        return [
            ':from' => $this->validDate($from) ? $from : $defaultFrom,
            ':to' => $this->validDate($to) ? $to : $defaultTo
        ];
    }

    private function validDate(?string $value): bool
    {
        if (!$value) {
            return false;
        }
        $date = \DateTime::createFromFormat('Y-m-d', $value);
        return $date && $date->format('Y-m-d') === $value;
    }
}

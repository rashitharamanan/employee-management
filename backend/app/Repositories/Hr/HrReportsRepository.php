<?php

namespace App\Repositories\Hr;

use PDO;

class HrReportsRepository
{
    public function __construct(private PDO $db)
    {
    }

    public function ensureSchema(): void
    {
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

    /*
    | -------------------------------------------------------------------------
    | KPI Summary
    | -------------------------------------------------------------------------
    */

    public function getEmployeeStats(string $monthStart, string $yearStart): array
    {
        $statement = $this->db->prepare(
            "SELECT COUNT(*) AS total_employees,
                    COALESCE(SUM(status = 'ACTIVE'), 0) AS active_employees,
                    COALESCE(SUM(joining_date >= :month_start), 0) AS new_hires_month
             FROM employees"
        );
        $statement->execute([':month_start' => $monthStart]);
        $employees = $statement->fetch(PDO::FETCH_ASSOC) ?: [];

        $statement = $this->db->prepare(
            "SELECT COALESCE(SUM(last_working_date >= :month_start), 0) AS exits_month,
                    COALESCE(SUM(last_working_date >= :year_start), 0) AS exits_year
             FROM exit_records"
        );
        $statement->execute([':month_start' => $monthStart, ':year_start' => $yearStart]);
        $exits = $statement->fetch(PDO::FETCH_ASSOC) ?: [];

        return [
            'total_employees' => (int) ($employees['total_employees'] ?? 0),
            'active_employees' => (int) ($employees['active_employees'] ?? 0),
            'new_hires_month' => (int) ($employees['new_hires_month'] ?? 0),
            'exits_month' => (int) ($exits['exits_month'] ?? 0),
            'exits_year' => (int) ($exits['exits_year'] ?? 0),
        ];
    }

    public function getAttendanceTotals(string $from, string $to): array
    {
        $statement = $this->db->prepare(
            "SELECT COUNT(*) AS total_records,
                    COALESCE(SUM(status = 'PRESENT'), 0) AS present,
                    COALESCE(SUM(status = 'ABSENT'), 0) AS absent,
                    COALESCE(SUM(status = 'LATE'), 0) AS late,
                    COALESCE(SUM(status = 'LEAVE'), 0) AS on_leave
             FROM attendance_records
             WHERE attendance_date BETWEEN :from AND :to"
        );
        $statement->execute([':from' => $from, ':to' => $to]);
        return $statement->fetch(PDO::FETCH_ASSOC) ?: [];
    }

    public function countPendingLeaveRequests(): int
    {
        return (int) $this->db->query(
            "SELECT COUNT(*) FROM leave_requests WHERE status = 'PENDING'"
        )->fetchColumn();
    }

    public function getAverageRating(): float
    {
        return round((float) $this->db->query(
            'SELECT AVG(overall_rating) FROM performance_reviews'
        )->fetchColumn(), 2);
    }

    public function getTrainingTotals(): array
    {
        $enrollments = $this->db->query(
            "SELECT COUNT(*) AS total, COALESCE(SUM(status = 'COMPLETED'), 0) AS completed,
                    ROUND(AVG(score), 1) AS avg_score
             FROM training_enrollments"
        )->fetch(PDO::FETCH_ASSOC) ?: [];

        $programs = $this->db->query('SELECT COUNT(*) FROM training_programs')->fetchColumn();

        return [
            'total_enrollments' => (int) ($enrollments['total'] ?? 0),
            'completed_enrollments' => (int) ($enrollments['completed'] ?? 0),
            'avg_training_score' => (float) ($enrollments['avg_score'] ?? 0),
            'total_programs' => (int) $programs,
        ];
    }

    public function getPayrollTotals(string $yearPrefix): array
    {
        $statement = $this->db->prepare(
            "SELECT COALESCE(SUM(net_pay), 0) AS total_net_pay,
                    COALESCE(SUM(gross_pay), 0) AS total_gross_pay,
                    COALESCE(SUM(status = 'PAID'), 0) AS paid_runs,
                    COALESCE(SUM(status = 'PENDING'), 0) AS pending_runs
             FROM payroll_records
             WHERE pay_period LIKE :year_prefix"
        );
        $statement->execute([':year_prefix' => $yearPrefix . '%']);
        return $statement->fetch(PDO::FETCH_ASSOC) ?: [];
    }

    public function countOpenPositions(): int
    {
        return (int) $this->db->query(
            "SELECT COUNT(*) FROM job_openings WHERE status = 'OPEN'"
        )->fetchColumn();
    }

    /*
    | -------------------------------------------------------------------------
    | Headcount & Department Charts
    | -------------------------------------------------------------------------
    */

    public function countHeadcountBefore(string $date): int
    {
        $statement = $this->db->prepare('SELECT COUNT(*) FROM employees WHERE joining_date < :date');
        $statement->execute([':date' => $date]);
        $joined = (int) $statement->fetchColumn();

        $statement = $this->db->prepare('SELECT COUNT(*) FROM exit_records WHERE last_working_date < :date');
        $statement->execute([':date' => $date]);

        return $joined - (int) $statement->fetchColumn();
    }

    public function getHiresByMonth(string $from, string $to): array
    {
        $statement = $this->db->prepare(
            "SELECT DATE_FORMAT(joining_date, '%Y-%m') AS month, COUNT(*) AS hires
             FROM employees
             WHERE joining_date BETWEEN :from AND :to
             GROUP BY DATE_FORMAT(joining_date, '%Y-%m')"
        );
        $statement->execute([':from' => $from, ':to' => $to]);
        return $statement->fetchAll(PDO::FETCH_KEY_PAIR);
    }

    public function getExitsByMonth(string $from, string $to): array
    {
        $statement = $this->db->prepare(
            "SELECT DATE_FORMAT(last_working_date, '%Y-%m') AS month, COUNT(*) AS exits
             FROM exit_records
             WHERE last_working_date BETWEEN :from AND :to
             GROUP BY DATE_FORMAT(last_working_date, '%Y-%m')"
        );
        $statement->execute([':from' => $from, ':to' => $to]);
        return $statement->fetchAll(PDO::FETCH_KEY_PAIR);
    }

    public function getDepartmentHeadcount(): array
    {
        return $this->db->query(
            "SELECT COALESCE(NULLIF(department, ''), 'Unassigned') AS department,
                    COUNT(*) AS employees,
                    ROUND(COUNT(*) * 100 / NULLIF((SELECT COUNT(*) FROM employees), 0), 1) AS percentage
             FROM employees
             GROUP BY COALESCE(NULLIF(department, ''), 'Unassigned')
             ORDER BY employees DESC"
        )->fetchAll(PDO::FETCH_ASSOC);
    }

    /*
    | -------------------------------------------------------------------------
    | Attendance Trend
    | -------------------------------------------------------------------------
    */

    public function getAttendanceTrend(int $days = 14): array
    {
        $statement = $this->db->prepare(
            "SELECT attendance_date AS date,
                    COALESCE(SUM(status = 'PRESENT'), 0) AS present,
                    COALESCE(SUM(status = 'ABSENT'), 0) AS absent,
                    COALESCE(SUM(status = 'LATE'), 0) AS late,
                    COALESCE(SUM(status = 'LEAVE'), 0) AS on_leave
             FROM attendance_records
             WHERE attendance_date >= DATE_SUB(CURDATE(), INTERVAL :days DAY)
             GROUP BY attendance_date
             ORDER BY attendance_date"
        );
        $statement->execute([':days' => max(1, $days)]);
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    /*
    | -------------------------------------------------------------------------
    | Leave Analysis
    | -------------------------------------------------------------------------
    */

    public function getLeaveByType(string $yearPrefix): array
    {
        $statement = $this->db->prepare(
            "SELECT COALESCE(lt.name, 'Uncategorised') AS leave_type,
                    COUNT(*) AS requests,
                    COALESCE(SUM(lr.total_days), 0) AS total_days,
                    COALESCE(SUM(CASE WHEN lr.status = 'APPROVED' THEN lr.total_days ELSE 0 END), 0) AS approved_days
             FROM leave_requests lr
             LEFT JOIN leave_types lt ON lt.id = lr.leave_type_id
             WHERE lr.from_date LIKE :year_prefix
             GROUP BY COALESCE(lt.name, 'Uncategorised')
             ORDER BY requests DESC"
        );
        $statement->execute([':year_prefix' => $yearPrefix . '%']);
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getLeaveByStatus(string $yearPrefix): array
    {
        $statement = $this->db->prepare(
            "SELECT status, COUNT(*) AS requests, COALESCE(SUM(total_days), 0) AS days
             FROM leave_requests
             WHERE from_date LIKE :year_prefix
             GROUP BY status
             ORDER BY requests DESC"
        );
        $statement->execute([':year_prefix' => $yearPrefix . '%']);
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getLeaveByMonth(string $yearPrefix): array
    {
        $statement = $this->db->prepare(
            "SELECT DATE_FORMAT(from_date, '%Y-%m') AS month,
                    COUNT(*) AS requests,
                    COALESCE(SUM(total_days), 0) AS days
             FROM leave_requests
             WHERE from_date LIKE :year_prefix AND status <> 'REJECTED'
             GROUP BY DATE_FORMAT(from_date, '%Y-%m')
             ORDER BY month"
        );
        $statement->execute([':year_prefix' => $yearPrefix . '%']);
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    /*
    | -------------------------------------------------------------------------
    | Recruitment Funnel
    | -------------------------------------------------------------------------
    */

    public function getRecruitmentSummary(): array
    {
        $candidates = $this->db->query(
            "SELECT COUNT(*) AS total_candidates, COALESCE(SUM(stage = 'HIRED'), 0) AS hired
             FROM recruitment_candidates"
        )->fetch(PDO::FETCH_ASSOC) ?: [];

        $openings = $this->db->query(
            "SELECT COALESCE(SUM(status = 'OPEN'), 0) AS open_positions,
                    COALESCE(SUM(CASE WHEN status = 'OPEN' THEN vacancies ELSE 0 END), 0) AS open_vacancies,
                    COUNT(*) AS total_openings
             FROM job_openings"
        )->fetch(PDO::FETCH_ASSOC) ?: [];

        $interviews = $this->db->query(
            "SELECT COALESCE(SUM(status = 'SCHEDULED'), 0) AS scheduled,
                    COALESCE(SUM(status = 'COMPLETED'), 0) AS completed
             FROM recruitment_interviews"
        )->fetch(PDO::FETCH_ASSOC) ?: [];

        return [
            'total_candidates' => (int) ($candidates['total_candidates'] ?? 0),
            'hired' => (int) ($candidates['hired'] ?? 0),
            'open_positions' => (int) ($openings['open_positions'] ?? 0),
            'open_vacancies' => (int) ($openings['open_vacancies'] ?? 0),
            'total_openings' => (int) ($openings['total_openings'] ?? 0),
            'interviews_scheduled' => (int) ($interviews['scheduled'] ?? 0),
            'interviews_completed' => (int) ($interviews['completed'] ?? 0),
        ];
    }

    public function getCandidatesByStage(): array
    {
        $rows = $this->db->query(
            'SELECT stage, COUNT(*) AS count FROM recruitment_candidates GROUP BY stage'
        )->fetchAll(PDO::FETCH_KEY_PAIR) ?: [];

        $labels = ['APPLIED' => 'Applied', 'SCREENING' => 'In Review', 'INTERVIEW' => 'In Interview', 'OFFERED' => 'Offered', 'HIRED' => 'Hired'];
        $total = (int) array_sum($rows);

        return array_map(
            static fn ($key, $label): array => [
                'key' => $key,
                'label' => $label,
                'count' => (int) ($rows[$key] ?? 0),
                'percentage' => $total ? round(((int) ($rows[$key] ?? 0)) / $total * 100, 1) : 0,
            ],
            array_keys($labels),
            $labels
        );
    }

    /*
    | -------------------------------------------------------------------------
    | Training & Performance
    | -------------------------------------------------------------------------
    */

    public function getEnrollmentsByStatus(): array
    {
        return $this->db->query(
            'SELECT status, COUNT(*) AS count FROM training_enrollments GROUP BY status ORDER BY count DESC'
        )->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getProgramsByStatus(): array
    {
        return $this->db->query(
            'SELECT status, COUNT(*) AS count FROM training_programs GROUP BY status ORDER BY count DESC'
        )->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAverageScoreByCategory(): array
    {
        return $this->db->query(
            "SELECT COALESCE(NULLIF(p.category, ''), 'General') AS category,
                    COUNT(e.id) AS enrollments,
                    ROUND(AVG(NULLIF(e.score, 0)), 1) AS avg_score
             FROM training_enrollments e
             INNER JOIN training_programs p ON p.id = e.program_id
             GROUP BY COALESCE(NULLIF(p.category, ''), 'General')
             ORDER BY enrollments DESC"
        )->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getPerformanceByDepartment(): array
    {
        return $this->db->query(
            "SELECT COALESCE(NULLIF(e.department, ''), 'Unassigned') AS department,
                    COUNT(r.id) AS reviews,
                    ROUND(AVG(r.overall_rating), 2) AS avg_rating
             FROM performance_reviews r
             INNER JOIN employees e ON e.id = r.employee_id
             GROUP BY COALESCE(NULLIF(e.department, ''), 'Unassigned')
             ORDER BY avg_rating DESC"
        )->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getTopPerformers(int $limit = 5): array
    {
        $limit = max(1, min($limit, 25));
        return $this->db->query(
            "SELECT e.id, e.employee_code, e.full_name,
                    COALESCE(NULLIF(e.department, ''), 'Unassigned') AS department,
                    ROUND(AVG(r.overall_rating), 2) AS avg_rating,
                    COUNT(r.id) AS reviews
             FROM performance_reviews r
             INNER JOIN employees e ON e.id = r.employee_id
             GROUP BY e.id, e.employee_code, e.full_name, COALESCE(NULLIF(e.department, ''), 'Unassigned')
             ORDER BY avg_rating DESC, reviews DESC
             LIMIT {$limit}"
        )->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getGoalsByStatus(): array
    {
        return $this->db->query(
            'SELECT status, COUNT(*) AS count, ROUND(AVG(progress), 0) AS avg_progress
             FROM performance_goals GROUP BY status ORDER BY count DESC'
        )->fetchAll(PDO::FETCH_ASSOC);
    }

    /*
    | -------------------------------------------------------------------------
    | Payroll Trend
    | -------------------------------------------------------------------------
    */

    public function getPayrollByMonth(string $yearPrefix): array
    {
        $statement = $this->db->prepare(
            "SELECT pay_period AS month,
                    COUNT(*) AS payslips,
                    COALESCE(SUM(gross_pay), 0) AS gross,
                    COALESCE(SUM(deductions), 0) AS deductions,
                    COALESCE(SUM(net_pay), 0) AS net
             FROM payroll_records
             WHERE pay_period LIKE :year_prefix
             GROUP BY pay_period
             ORDER BY pay_period"
        );
        $statement->execute([':year_prefix' => $yearPrefix . '%']);
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    /*
    | -------------------------------------------------------------------------
    | Generated Reports
    | -------------------------------------------------------------------------
    */

    public function getRecentReports(int $limit = 10): array
    {
        $limit = max(1, min($limit, 100));
        return $this->db->query(
            "SELECT id, report_name, category, generated_by, generated_at, report_format, date_from, date_to
             FROM generated_reports
             ORDER BY generated_at DESC, id DESC
             LIMIT {$limit}"
        )->fetchAll(PDO::FETCH_ASSOC);
    }

    public function createReport(array $data): int
    {
        $statement = $this->db->prepare(
            'INSERT INTO generated_reports (report_name, category, generated_by, report_format, date_from, date_to)
             VALUES (:report_name, :category, :generated_by, :report_format, :date_from, :date_to)'
        );
        $statement->execute([
            ':report_name' => $data['report_name'],
            ':category' => $data['category'],
            ':generated_by' => $data['generated_by'],
            ':report_format' => $data['report_format'],
            ':date_from' => $data['date_from'],
            ':date_to' => $data['date_to'],
        ]);

        return (int) $this->db->lastInsertId();
    }

    public function countReports(): int
    {
        return (int) $this->db->query('SELECT COUNT(*) FROM generated_reports')->fetchColumn();
    }

    /*
    | -------------------------------------------------------------------------
    | Export Datasets (CSV)
    | -------------------------------------------------------------------------
    */

    public function getEmployeeExportRows(): array
    {
        return $this->db->query(
            "SELECT e.id, e.employee_code, e.full_name, e.email, e.phone,
                    COALESCE(NULLIF(e.department, ''), 'Unassigned') AS department,
                    COALESCE(NULLIF(e.designation, ''), 'Unassigned') AS designation,
                    e.employment_type, e.joining_date, e.status
             FROM employees e
             ORDER BY e.employee_code"
        )->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAttendanceExportRows(string $from, string $to): array
    {
        $statement = $this->db->prepare(
            "SELECT a.attendance_date, e.employee_code, e.full_name,
                    COALESCE(NULLIF(e.department, ''), 'Unassigned') AS department,
                    a.check_in, a.check_out, a.status
             FROM attendance_records a
             INNER JOIN employees e ON e.id = a.employee_id
             WHERE a.attendance_date BETWEEN :from AND :to
             ORDER BY a.attendance_date DESC, e.employee_code"
        );
        $statement->execute([':from' => $from, ':to' => $to]);
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getLeaveExportRows(string $from, string $to): array
    {
        $statement = $this->db->prepare(
            "SELECT lr.id, e.employee_code, e.full_name,
                    COALESCE(lt.name, 'Uncategorised') AS leave_type,
                    lr.from_date, lr.to_date, lr.total_days, lr.status,
                    lr.approved_at, lr.created_at
             FROM leave_requests lr
             INNER JOIN employees e ON e.id = lr.employee_id
             LEFT JOIN leave_types lt ON lt.id = lr.leave_type_id
             WHERE lr.from_date BETWEEN :from AND :to
             ORDER BY lr.from_date DESC"
        );
        $statement->execute([':from' => $from, ':to' => $to]);
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getPayrollExportRows(string $from, string $to): array
    {
        $statement = $this->db->prepare(
            "SELECT p.pay_period, e.employee_code, e.full_name,
                    COALESCE(NULLIF(e.department, ''), 'Unassigned') AS department,
                    p.basic_salary, p.gross_pay, p.deductions, p.net_pay, p.status, p.paid_on
             FROM payroll_records p
             INNER JOIN employees e ON e.id = p.employee_id
             WHERE p.pay_period BETWEEN :from AND :to
             ORDER BY p.pay_period DESC, e.employee_code"
        );
        $statement->execute([':from' => $from, ':to' => $to]);
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getTrainingExportRows(string $from, string $to): array
    {
        $statement = $this->db->prepare(
            "SELECT e.employee_code, e.full_name, tp.title AS program, tp.category,
                    te.enrollment_date, te.completion_date, te.status, te.score, te.certificate_issued
             FROM training_enrollments te
             INNER JOIN employees e ON e.id = te.employee_id
             INNER JOIN training_programs tp ON tp.id = te.program_id
             WHERE te.enrollment_date BETWEEN :from AND :to
             ORDER BY te.enrollment_date DESC"
        );
        $statement->execute([':from' => $from, ':to' => $to]);
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getPerformanceExportRows(string $from, string $to): array
    {
        $statement = $this->db->prepare(
            "SELECT e.employee_code, e.full_name,
                    COALESCE(NULLIF(e.department, ''), 'Unassigned') AS department,
                    r.review_period_start, r.review_period_end, r.review_date,
                    r.overall_rating, r.status
             FROM performance_reviews r
             INNER JOIN employees e ON e.id = r.employee_id
             WHERE r.review_date BETWEEN :from AND :to
             ORDER BY r.review_date DESC"
        );
        $statement->execute([':from' => $from, ':to' => $to]);
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getRecruitmentExportRows(string $from, string $to): array
    {
        $statement = $this->db->prepare(
            "SELECT c.full_name, c.email, j.title AS job_title,
                    COALESCE(NULLIF(j.department, ''), 'Unassigned') AS department,
                    c.stage, c.source, c.applied_at
             FROM recruitment_candidates c
             LEFT JOIN job_openings j ON j.id = c.job_opening_id
             WHERE c.applied_at BETWEEN :from AND :to
             ORDER BY c.applied_at DESC"
        );
        $statement->execute([':from' => $from, ':to' => $to]);
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }
}

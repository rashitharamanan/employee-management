<?php

namespace App\Repositories\Hr;

use PDO;

class HrDashboardRepository
{
    public function __construct(private PDO $db)
    {
    }

    public function getDashboard(): array
    {
        $today = date('Y-m-d');
        $weekStart = date('Y-m-d', strtotime('monday this week'));
        $monthStart = date('Y-m-01');

        return [
            'summary' => $this->getSummary($today, $monthStart),
            'attendance' => $this->getAttendance($weekStart, $today),
            'leave_overview' => $this->getLeaveOverview($monthStart, $today),
            'pending_leave_requests' => $this->getPendingLeaveRequests(),
            'new_joiners' => $this->getNewJoiners($monthStart),
            'upcoming_birthdays' => $this->getUpcomingBirthdays(),
            'upcoming_anniversaries' => $this->getUpcomingAnniversaries(),
            'documents_expiring' => [],
            'recent_activities' => [],
        ];
    }

    public function ensureRecruitmentSchema(): void
    {
        $this->db->exec("CREATE TABLE IF NOT EXISTS job_openings (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY, title VARCHAR(150) NOT NULL, department VARCHAR(100) NOT NULL,
            location VARCHAR(100) NULL, employment_type VARCHAR(50) NOT NULL DEFAULT 'Full-time', vacancies INT NOT NULL DEFAULT 1,
            applicants_count INT NOT NULL DEFAULT 0, status ENUM('OPEN','CLOSED','DRAFT') NOT NULL DEFAULT 'OPEN',
            hiring_manager VARCHAR(120) NULL, posted_at DATE NULL, created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            KEY idx_job_status (status), KEY idx_job_department (department)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
        $this->db->exec("CREATE TABLE IF NOT EXISTS recruitment_candidates (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY, full_name VARCHAR(150) NOT NULL, email VARCHAR(150) NULL, job_opening_id INT UNSIGNED NULL,
            stage ENUM('APPLIED','SCREENING','INTERVIEW','OFFERED','HIRED','REJECTED') NOT NULL DEFAULT 'APPLIED',
            source VARCHAR(80) NULL, profile_photo VARCHAR(255) NULL, applied_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            KEY idx_candidate_stage (stage), KEY idx_candidate_job (job_opening_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
        $this->db->exec("CREATE TABLE IF NOT EXISTS recruitment_interviews (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY, candidate_id INT UNSIGNED NOT NULL, interviewer VARCHAR(150) NULL,
            interview_at DATETIME NOT NULL, interview_type VARCHAR(60) NOT NULL DEFAULT 'Video Interview', status ENUM('SCHEDULED','COMPLETED','CANCELLED') NOT NULL DEFAULT 'SCHEDULED',
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP, KEY idx_interview_datetime (interview_at), KEY idx_interview_candidate (candidate_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    }

    public function ensureOnboardingExitSchema(): void
    {
        $this->db->exec("CREATE TABLE IF NOT EXISTS onboarding_records (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY, employee_id INT UNSIGNED NOT NULL, joining_date DATE NOT NULL,
            status ENUM('PENDING','IN_PROGRESS','COMPLETED') NOT NULL DEFAULT 'PENDING', current_stage VARCHAR(100) NULL, progress TINYINT UNSIGNED NOT NULL DEFAULT 0,
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP, updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            UNIQUE KEY uq_onboarding_employee (employee_id), KEY idx_onboarding_status (status)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
        $this->db->exec("CREATE TABLE IF NOT EXISTS exit_records (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY, employee_id INT UNSIGNED NOT NULL, last_working_date DATE NOT NULL,
            status ENUM('PENDING','IN_PROGRESS','COMPLETED') NOT NULL DEFAULT 'PENDING', current_stage VARCHAR(100) NULL, progress TINYINT UNSIGNED NOT NULL DEFAULT 0,
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP, updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            UNIQUE KEY uq_exit_employee (employee_id), KEY idx_exit_status (status)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
        $this->db->exec("CREATE TABLE IF NOT EXISTS hr_workflow_tasks (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY, workflow_type ENUM('ONBOARDING','EXIT') NOT NULL, record_id INT UNSIGNED NOT NULL,
            task_name VARCHAR(200) NOT NULL, assigned_to VARCHAR(120) NULL, due_date DATE NULL,
            status ENUM('PENDING','IN_PROGRESS','COMPLETED') NOT NULL DEFAULT 'PENDING', created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            KEY idx_workflow_tasks (workflow_type, record_id), KEY idx_workflow_status (status)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
        $this->db->exec("CREATE TABLE IF NOT EXISTS hr_document_templates (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY, name VARCHAR(180) NOT NULL, category ENUM('ONBOARDING','EXIT','GENERAL') NOT NULL DEFAULT 'GENERAL',
            file_path VARCHAR(255) NULL, status ENUM('ACTIVE','INACTIVE') NOT NULL DEFAULT 'ACTIVE', created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    }
public function ensurePayrollSchema(): void
    {
        $this->db->exec("CREATE TABLE IF NOT EXISTS payroll_records (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            employee_id INT UNSIGNED NOT NULL,
            pay_period VARCHAR(7) NOT NULL,
            basic_salary DECIMAL(12,2) NOT NULL DEFAULT 0.00,
            hra DECIMAL(12,2) NOT NULL DEFAULT 0.00,
            special_allowance DECIMAL(12,2) NOT NULL DEFAULT 0.00,
            allowances DECIMAL(12,2) NOT NULL DEFAULT 0.00,
            gross_pay DECIMAL(12,2) NOT NULL DEFAULT 0.00,
            pf DECIMAL(12,2) NOT NULL DEFAULT 0.00,
            professional_tax DECIMAL(12,2) NOT NULL DEFAULT 0.00,
            income_tax DECIMAL(12,2) NOT NULL DEFAULT 0.00,
            deductions DECIMAL(12,2) NOT NULL DEFAULT 0.00,
            net_pay DECIMAL(12,2) NOT NULL DEFAULT 0.00,
            status ENUM('PENDING','PAID','VOID') NOT NULL DEFAULT 'PENDING',
            paid_on DATE NULL,
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            UNIQUE KEY uq_payroll_employee_period (pay_period, employee_id),
            KEY idx_payroll_period (pay_period),
            KEY idx_payroll_status (status)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

        try {
            $count = (int) $this->db->query("SELECT COUNT(*) FROM payroll_records")->fetchColumn();
            if ($count > 0) {
                return;
            }
        } catch (\Throwable $e) {
            return;
        }

        try {
            $employees = $this->db->query(
                "SELECT id, employee_code, full_name, profile_photo, department, designation,
                        joining_date, employment_type, basic_salary
                 FROM employees ORDER BY full_name ASC"
            )->fetchAll(PDO::FETCH_ASSOC) ?: [];
            if (empty($employees)) {
                return;
            }

            $periods = [date('Y-m') => 'PENDING', date('Y-m', strtotime('-1 month')) => 'PAID'];
            $insert = $this->db->prepare(
                "INSERT INTO payroll_records
                 (employee_id, pay_period, basic_salary, hra, special_allowance, allowances, gross_pay,
                  pf, professional_tax, income_tax, deductions, net_pay, status, paid_on)
                 VALUES (:employee_id, :pay_period, :basic_salary, :hra, :special_allowance, :allowances, :gross_pay,
                         :pf, :professional_tax, :income_tax, :deductions, :net_pay, :status, :paid_on)"
            );
            $now = date('Y-m');

            foreach ($employees as $row) {
                $basic = (float) ($row['basic_salary'] ?? 0);
                if ($basic <= 0) {
                    continue;
                }

                foreach ($periods as $period => $status) {
                    $record = $this->buildPayrollRecord($row, $period, $now);
                    if ($record === null) {
                        continue;
                    }

                    $insert->execute([
                        ':employee_id' => $row['id'],
                        ':pay_period' => $period,
                        ':basic_salary' => $record['basic_salary'],
                        ':hra' => $record['hra'],
                        ':special_allowance' => $record['special_allowance'],
                        ':allowances' => $record['allowances'],
                        ':gross_pay' => $record['gross_pay'],
                        ':pf' => $record['pf'],
                        ':professional_tax' => $record['professional_tax'],
                        ':income_tax' => $record['income_tax'],
                        ':deductions' => $record['deductions'],
                        ':net_pay' => $record['net_pay'],
                        ':status' => $status,
                        ':paid_on' => ($status === 'PAID') ? date('Y-m-t', strtotime($period . '-01')) : null,
                    ]);
                }
            }
        } catch (\Throwable $e) {
            // Leave demo seeding failures silent; the register still works from employee master data.
        }
    }

    public function getOnboardingExitDashboard(array $filters): array
    {
        $this->ensureOnboardingExitSchema();
        $this->seedOnboardingExitData();
        $onboarding = $this->getWorkflowRecords('ONBOARDING', $filters);
        $exit = $this->getWorkflowRecords('EXIT', $filters);
        return [
            'summary' => $this->getOnboardingExitSummary(),
            'onboarding_pipeline' => $this->getWorkflowPipeline('ONBOARDING'),
            'exit_pipeline' => $this->getWorkflowPipeline('EXIT'),
            'new_joiners' => $onboarding,
            'exit_processes' => $exit,
            'onboarding_tasks' => $this->getWorkflowTasks('ONBOARDING'),
            'exit_tasks' => $this->getWorkflowTasks('EXIT'),
            'upcoming_milestones' => $this->getWorkflowMilestones(),
            'documents' => $this->db->query("SELECT id, name, category, file_path FROM hr_document_templates WHERE status = 'ACTIVE' ORDER BY name ASC")->fetchAll(PDO::FETCH_ASSOC) ?: [],
        ];
    }

    private function seedOnboardingExitData(): void
    {
        try {
            $countO = (int) $this->db->query("SELECT COUNT(*) FROM onboarding_records")->fetchColumn();
            if ($countO === 0) {
                $employees = $this->db->query("SELECT id, joining_date FROM employees ORDER BY id ASC LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);
                if (!empty($employees)) {
                    $insertO = $this->db->prepare("INSERT INTO onboarding_records (employee_id, joining_date, status, current_stage, progress) VALUES (:employee_id, :joining_date, :status, :current_stage, :progress)");
                    $stages = [
                        ['status' => 'IN_PROGRESS', 'stage' => 'Documentation & IT Setup', 'progress' => 75, 'days' => '+5 days'],
                        ['status' => 'PENDING', 'stage' => 'Background Verification', 'progress' => 35, 'days' => '+10 days'],
                        ['status' => 'COMPLETED', 'stage' => 'Orientation Scheduled', 'progress' => 90, 'days' => '+20 days']
                    ];
                    foreach ($stages as $idx => $st) {
                        if (isset($employees[$idx])) {
                            $insertO->execute([
                                ':employee_id' => $employees[$idx]['id'],
                                ':joining_date' => date('Y-m-d', strtotime($st['days'])),
                                ':status' => $st['status'],
                                ':current_stage' => $st['stage'],
                                ':progress' => $st['progress']
                            ]);
                        }
                    }
                }
            }

            $countE = (int) $this->db->query("SELECT COUNT(*) FROM exit_records")->fetchColumn();
            if ($countE === 0) {
                $employees = $this->db->query("SELECT id FROM employees ORDER BY id DESC LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);
                if (!empty($employees)) {
                    $insertE = $this->db->prepare("INSERT INTO exit_records (employee_id, last_working_date, status, current_stage, progress) VALUES (:employee_id, :last_working_date, :status, :current_stage, :progress)");
                    $exitStages = [
                        ['status' => 'PENDING', 'stage' => 'IT Asset Handover Pending', 'progress' => 60, 'days' => '+15 days'],
                        ['status' => 'IN_PROGRESS', 'stage' => 'Finance Dues & Accounts', 'progress' => 40, 'days' => '+5 days']
                    ];
                    foreach ($exitStages as $idx => $st) {
                        if (isset($employees[$idx])) {
                            $insertE->execute([
                                ':employee_id' => $employees[$idx]['id'],
                                ':last_working_date' => date('Y-m-d', strtotime($st['days'])),
                                ':status' => $st['status'],
                                ':current_stage' => $st['stage'],
                                ':progress' => $st['progress']
                            ]);
                        }
                    }
                }
            }
        } catch (\Throwable $e) {}
    }

    private function getOnboardingExitSummary(): array
    {
        $onboarding = $this->db->query("SELECT SUM(status = 'IN_PROGRESS') AS active, SUM(status = 'COMPLETED' AND MONTH(updated_at) = MONTH(CURDATE()) AND YEAR(updated_at) = YEAR(CURDATE())) AS completed FROM onboarding_records")->fetch(PDO::FETCH_ASSOC) ?: [];
        $exit = $this->db->query("SELECT SUM(status = 'IN_PROGRESS') AS active, SUM(status = 'COMPLETED' AND MONTH(updated_at) = MONTH(CURDATE()) AND YEAR(updated_at) = YEAR(CURDATE())) AS completed, SUM(status = 'PENDING') AS clearance_pending FROM exit_records")->fetch(PDO::FETCH_ASSOC) ?: [];
        $tasks = (int) $this->db->query("SELECT COUNT(*) FROM hr_workflow_tasks WHERE status <> 'COMPLETED'")->fetchColumn();
        return ['onboarding_in_progress' => (int) ($onboarding['active'] ?? 0), 'onboarding_completed_month' => (int) ($onboarding['completed'] ?? 0), 'pending_tasks' => $tasks, 'exit_in_progress' => (int) ($exit['active'] ?? 0), 'exit_completed_month' => (int) ($exit['completed'] ?? 0), 'final_clearance_pending' => (int) ($exit['clearance_pending'] ?? 0)];
    }

    private function getWorkflowPipeline(string $type): array
    {
        $stages = $type === 'ONBOARDING' ? ['Offer Accepted','Document Submission','Account Setup','Orientation','Training','Completed'] : ['Resignation Submitted','Manager Approval','Handover','Asset Return','Final Clearance','Completed'];
        $table = $type === 'ONBOARDING' ? 'onboarding_records' : 'exit_records';
        $rows = $this->db->query("SELECT current_stage, COUNT(*) AS count FROM {$table} GROUP BY current_stage")->fetchAll(PDO::FETCH_KEY_PAIR) ?: [];
        return array_map(static fn ($index, $stage) => ['step' => $index + 1, 'stage' => $stage, 'count' => (int) ($rows[$stage] ?? 0)], array_keys($stages), $stages);
    }

    private function getWorkflowRecords(string $type, array $filters): array
    {
        $table = $type === 'ONBOARDING' ? 'onboarding_records' : 'exit_records'; $dateColumn = $type === 'ONBOARDING' ? 'joining_date' : 'last_working_date';
        $idPrefix = $type === 'ONBOARDING' ? 'EMP' : 'EXT';
        $statement = $this->db->prepare("SELECT r.id, r.status, r.current_stage, r.progress, r.{$dateColumn} AS milestone_date, r.{$dateColumn} AS joining_date, r.{$dateColumn} AS last_working_day, e.full_name, e.employee_code, e.department, e.designation AS role, e.profile_photo, e.email, CONCAT('{$idPrefix}-', LPAD(r.id, 3, '0')) AS code_id FROM {$table} r INNER JOIN employees e ON e.id = r.employee_id ORDER BY r.{$dateColumn} ASC LIMIT 15");
        $statement->execute(); return $statement->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    private function getWorkflowTasks(string $type): array
    {
        $statement = $this->db->prepare("SELECT id, task_name, assigned_to, due_date, status FROM hr_workflow_tasks WHERE workflow_type = :type ORDER BY due_date ASC, id ASC LIMIT 10");
        $statement->execute([':type' => $type]); return $statement->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    private function getWorkflowMilestones(): array
    {
        $rows = $this->db->query("SELECT 'ONBOARDING' AS workflow_type, r.id, r.joining_date AS milestone_date, r.current_stage, e.full_name, e.profile_photo FROM onboarding_records r INNER JOIN employees e ON e.id = r.employee_id WHERE r.status <> 'COMPLETED' UNION ALL SELECT 'EXIT', r.id, r.last_working_date, r.current_stage, e.full_name, e.profile_photo FROM exit_records r INNER JOIN employees e ON e.id = r.employee_id WHERE r.status <> 'COMPLETED' ORDER BY milestone_date ASC LIMIT 8")->fetchAll(PDO::FETCH_ASSOC) ?: [];
        return $rows;
    }

    public function getRecruitmentDashboard(array $filters): array
    {
        $this->ensureRecruitmentSchema();
        return [
            'summary' => $this->getRecruitmentSummary(),
            'pipeline' => $this->getRecruitmentPipeline(),
            'recent_candidates' => $this->getRecentCandidates(),
            'candidate_sources' => $this->getCandidateSources(),
            'job_openings_data' => $this->getJobOpenings($filters),
            'upcoming_interviews' => $this->getUpcomingInterviews(),
            'filters' => ['departments' => $this->db->query('SELECT DISTINCT department FROM job_openings ORDER BY department')->fetchAll(PDO::FETCH_COLUMN) ?: []],
        ];
    }

    private function getRecruitmentSummary(): array
    {
        $jobs = $this->db->query("SELECT COUNT(*) AS total, SUM(status = 'OPEN') AS open_jobs FROM job_openings")->fetch(PDO::FETCH_ASSOC) ?: [];
        $candidates = $this->db->query("SELECT COUNT(*) AS total, SUM(stage = 'INTERVIEW') AS interviewing, SUM(stage = 'OFFERED') AS offers, SUM(stage = 'HIRED') AS hired FROM recruitment_candidates")->fetch(PDO::FETCH_ASSOC) ?: [];
        return ['active_job_openings' => (int) ($jobs['open_jobs'] ?? 0), 'total_applicants' => (int) ($candidates['total'] ?? 0), 'in_interview' => (int) ($candidates['interviewing'] ?? 0), 'offers_extended' => (int) ($candidates['offers'] ?? 0), 'hired' => (int) ($candidates['hired'] ?? 0)];
    }

    private function getRecruitmentPipeline(): array
    {
        $rows = $this->db->query("SELECT stage, COUNT(*) AS count FROM recruitment_candidates GROUP BY stage")->fetchAll(PDO::FETCH_KEY_PAIR) ?: [];
        $stages = ['APPLIED' => 'Applied', 'SCREENING' => 'In Review', 'INTERVIEW' => 'In Interview', 'OFFERED' => 'Offered', 'HIRED' => 'Hired'];
        $total = array_sum(array_map('intval', $rows));
        return array_map(static fn ($key, $label) => ['key' => $key, 'label' => $label, 'count' => (int) ($rows[$key] ?? 0), 'percentage' => $total ? round(((int) ($rows[$key] ?? 0) / $total) * 100, 1) : 0], array_keys($stages), $stages);
    }

    private function getRecentCandidates(): array
    {
        return $this->db->query("SELECT c.id, c.full_name, c.profile_photo, c.stage, c.applied_at, j.title AS job_title FROM recruitment_candidates c LEFT JOIN job_openings j ON j.id = c.job_opening_id ORDER BY c.applied_at DESC LIMIT 5")->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    private function getCandidateSources(): array
    {
        $rows = $this->db->query("SELECT COALESCE(NULLIF(source, ''), 'Other') AS source, COUNT(*) AS count FROM recruitment_candidates GROUP BY COALESCE(NULLIF(source, ''), 'Other') ORDER BY count DESC")->fetchAll(PDO::FETCH_ASSOC) ?: [];
        $total = array_sum(array_column($rows, 'count'));
        return array_map(static fn ($row) => ['source' => $row['source'], 'count' => (int) $row['count'], 'percentage' => $total ? round(((int) $row['count'] / $total) * 100, 1) : 0], $rows);
    }

    private function getJobOpenings(array $filters): array
    {
        $where = ['1=1']; $params = [];
        if (trim((string) ($filters['search'] ?? '')) !== '') { $where[] = '(title LIKE :search OR department LIKE :search OR location LIKE :search)'; $params[':search'] = '%' . trim((string) $filters['search']) . '%'; }
        if (trim((string) ($filters['department'] ?? '')) !== '') { $where[] = 'department = :department'; $params[':department'] = trim((string) $filters['department']); }
        if (trim((string) ($filters['status'] ?? '')) !== '') { $where[] = 'status = :status'; $params[':status'] = strtoupper(trim((string) $filters['status'])); }
        $whereSql = implode(' AND ', $where); $page = max(1, (int) ($filters['page'] ?? 1)); $perPage = min(100, max(1, (int) ($filters['per_page'] ?? 10))); $offset = ($page - 1) * $perPage;
        $count = $this->db->prepare("SELECT COUNT(*) FROM job_openings WHERE {$whereSql}"); $count->execute($params); $total = (int) $count->fetchColumn();
        $statement = $this->db->prepare("SELECT * FROM job_openings WHERE {$whereSql} ORDER BY posted_at DESC, id DESC LIMIT :limit OFFSET :offset"); foreach ($params as $key => $value) $statement->bindValue($key, $value); $statement->bindValue(':limit', $perPage, PDO::PARAM_INT); $statement->bindValue(':offset', $offset, PDO::PARAM_INT); $statement->execute();
        return ['jobs' => $statement->fetchAll(PDO::FETCH_ASSOC) ?: [], 'pagination' => ['current_page' => $page, 'per_page' => $perPage, 'total_items' => $total, 'total_pages' => $total ? (int) ceil($total / $perPage) : 0]];
    }

    public function updateJobOpeningStatus(int $id, string $status): bool
    {
        $status = strtoupper($status);
        if (!in_array($status, ['OPEN', 'CLOSED', 'DRAFT'], true)) {
            return false;
        }
        $stmt = $this->db->prepare("UPDATE job_openings SET status = :status WHERE id = :id");
        return $stmt->execute([':status' => $status, ':id' => $id]);
    }

    public function createJobOpening(array $data): int
    {
        $stmt = $this->db->prepare("INSERT INTO job_openings (title, department, location, employment_type, vacancies, applicants_count, status, hiring_manager, posted_at) VALUES (:title, :department, :location, :employment_type, :vacancies, 0, 'OPEN', :hiring_manager, CURDATE())");
        $stmt->execute([
            ':title' => $data['title'] ?? 'Untitled Position',
            ':department' => $data['department'] ?? 'General',
            ':location' => $data['location'] ?? 'Headquarters',
            ':employment_type' => $data['employment_type'] ?? 'Full-time',
            ':vacancies' => max(1, (int) ($data['vacancies'] ?? 1)),
            ':hiring_manager' => $data['hiring_manager'] ?? 'HR Team'
        ]);
        return (int) $this->db->lastInsertId();
    }

    public function updateJobOpening(int $id, array $data): bool
    {
        $fields = [];
        $params = [':id' => $id];
        if (!empty($data['title'])) { $fields[] = 'title = :title'; $params[':title'] = $data['title']; }
        if (!empty($data['department'])) { $fields[] = 'department = :department'; $params[':department'] = $data['department']; }
        if (!empty($data['location'])) { $fields[] = 'location = :location'; $params[':location'] = $data['location']; }
        if (!empty($data['employment_type'])) { $fields[] = 'employment_type = :employment_type'; $params[':employment_type'] = $data['employment_type']; }
        if (isset($data['vacancies'])) { $fields[] = 'vacancies = :vacancies'; $params[':vacancies'] = max(1, (int) $data['vacancies']); }
        if (!empty($data['hiring_manager'])) { $fields[] = 'hiring_manager = :hiring_manager'; $params[':hiring_manager'] = $data['hiring_manager']; }
        if (!empty($data['status'])) {
            $status = strtoupper((string) $data['status']);
            if (in_array($status, ['OPEN', 'CLOSED', 'DRAFT'], true)) {
                $fields[] = 'status = :status';
                $params[':status'] = $status;
            }
        }
        if (empty($fields)) {
            return false;
        }
        $sql = "UPDATE job_openings SET " . implode(', ', $fields) . " WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }

    public function updateCandidateStage(int $id, string $stage): bool
    {
        $stage = strtoupper($stage);
        $stmt = $this->db->prepare("UPDATE recruitment_candidates SET stage = :stage WHERE id = :id");
        return $stmt->execute([':stage' => $stage, ':id' => $id]);
    }

    public function updateOnboardingStatus(int $id, string $status): bool
    {
        $status = strtoupper($status);
        if ($status === 'ACTIVE') $status = 'COMPLETED';
        $stmt = $this->db->prepare("UPDATE onboarding_records SET status = :status WHERE id = :id");
        return $stmt->execute([':status' => $status, ':id' => $id]);
    }

    public function updateOnboardingRecord(int $id, array $data): bool
    {
        $fields = [];
        $params = [':id' => $id];
        if (!empty($data['status'])) {
            $status = strtoupper((string) $data['status']);
            if ($status === 'ACTIVE') $status = 'COMPLETED';
            $fields[] = 'status = :status';
            $params[':status'] = $status;
        }
        if (isset($data['progress'])) {
            $fields[] = 'progress = :progress';
            $params[':progress'] = max(0, min(100, (int) $data['progress']));
        }
        if (!empty($data['current_stage']) || !empty($data['stage'])) {
            $fields[] = 'current_stage = :current_stage';
            $params[':current_stage'] = $data['current_stage'] ?? $data['stage'];
        }
        if (!empty($data['joining_date'])) {
            $fields[] = 'joining_date = :joining_date';
            $params[':joining_date'] = $data['joining_date'];
        }
        if (empty($fields)) return false;
        $sql = "UPDATE onboarding_records SET " . implode(', ', $fields) . " WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }

    public function updateExitStatus(int $id, string $status): bool
    {
        $status = strtoupper($status);
        if ($status === 'NOTICE') $status = 'PENDING';
        if ($status === 'TERMINATED') $status = 'COMPLETED';
        $stmt = $this->db->prepare("UPDATE exit_records SET status = :status WHERE id = :id");
        return $stmt->execute([':status' => $status, ':id' => $id]);
    }

    public function updateExitRecord(int $id, array $data): bool
    {
        $fields = [];
        $params = [':id' => $id];
        if (!empty($data['status'])) {
            $status = strtoupper((string) $data['status']);
            if ($status === 'NOTICE') $status = 'PENDING';
            if ($status === 'TERMINATED') $status = 'COMPLETED';
            $fields[] = 'status = :status';
            $params[':status'] = $status;
        }
        if (isset($data['progress'])) {
            $fields[] = 'progress = :progress';
            $params[':progress'] = max(0, min(100, (int) $data['progress']));
        }
        if (!empty($data['clearance_stage']) || !empty($data['current_stage'])) {
            $fields[] = 'current_stage = :current_stage';
            $params[':current_stage'] = $data['clearance_stage'] ?? $data['current_stage'];
        }
        if (!empty($data['last_working_day']) || !empty($data['last_working_date'])) {
            $fields[] = 'last_working_date = :last_working_date';
            $params[':last_working_date'] = $data['last_working_day'] ?? $data['last_working_date'];
        }
        if (empty($fields)) return false;
        $sql = "UPDATE exit_records SET " . implode(', ', $fields) . " WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }

    private function getUpcomingInterviews(): array
    {
        $statement = $this->db->query("SELECT i.id, i.interview_at, i.interview_type, i.interviewer, c.full_name AS candidate_name, c.profile_photo, j.title AS job_title FROM recruitment_interviews i INNER JOIN recruitment_candidates c ON c.id = i.candidate_id LEFT JOIN job_openings j ON j.id = c.job_opening_id WHERE i.status = 'SCHEDULED' AND i.interview_at >= NOW() ORDER BY i.interview_at ASC LIMIT 5");
        return $statement->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    private function seedRecruitmentData(): void
    {
        try {
            $count = (int) $this->db->query("SELECT COUNT(*) FROM job_openings")->fetchColumn();
            if ($count > 0) {
                return;
            }
        } catch (\Throwable $e) {}
        $jobs = [['Senior UI/UX Designer','Design','Chennai','Full-time',2,28,'OPEN','Jessica Morgan'],['UX/UI Designer','Design','Bangalore','Full-time',2,15,'OPEN','Olivia Johnson'],['Product Manager','Product','Hyderabad','Full-time',1,25,'OPEN','Michael Chen'],['HR Executive','HR','Chennai','Full-time',2,18,'OPEN','Linda Carter'],['QA Analyst','Engineering','Mumbai','Full-time',2,20,'OPEN','David Lee'],['Marketing Coordinator','Marketing','Chennai','Full-time',1,12,'CLOSED','Olivia Taylor']];
        $insertJob = $this->db->prepare('INSERT INTO job_openings (title, department, location, employment_type, vacancies, applicants_count, status, hiring_manager, posted_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, CURDATE())'); foreach ($jobs as $job) $insertJob->execute($job);
        $jobIds = $this->db->query('SELECT id FROM job_openings ORDER BY id')->fetchAll(PDO::FETCH_COLUMN);
        $names = ['Priya Nair','Arun Kumar','Vishal Raj','Anitha Das','Rahul Mehta','Sneha Iyer','Daniel Martin','Kavya S']; $stages = ['APPLIED','SCREENING','INTERVIEW','OFFERED','HIRED','APPLIED','INTERVIEW','SCREENING']; $sources = ['Job Portal','Employee Referral','Company Website','Social Media','Walk-in','Job Portal','Employee Referral','Other'];
        $insertCandidate = $this->db->prepare('INSERT INTO recruitment_candidates (full_name, email, job_opening_id, stage, source, applied_at) VALUES (?, ?, ?, ?, ?, DATE_SUB(NOW(), INTERVAL ? DAY))'); foreach ($names as $index => $name) $insertCandidate->execute([$name, strtolower(str_replace(' ', '.', $name)) . '@example.com', $jobIds[$index % count($jobIds)], $stages[$index], $sources[$index], $index + 1]);
        $candidateIds = $this->db->query('SELECT id FROM recruitment_candidates ORDER BY id')->fetchAll(PDO::FETCH_COLUMN); $insertInterview = $this->db->prepare('INSERT INTO recruitment_interviews (candidate_id, interviewer, interview_at, interview_type) VALUES (?, ?, ?, ?)'); foreach (array_slice($candidateIds, 0, 4) as $index => $candidateId) $insertInterview->execute([$candidateId, 'HR Manager', date('Y-m-d H:i:s', strtotime('+' . ($index + 1) . ' day 10:00')), 'Video Interview']);
    }

    /**
     * Dashboard data for the HR attendance workspace.
     */
    public function getAttendanceDashboard(array $filters): array
    {
        $date = $this->validDate((string) ($filters['date'] ?? ''))
            ? (string) $filters['date']
            : date('Y-m-d');
        $trendDays = min(31, max(2, (int) ($filters['trend_days'] ?? 7)));

        return [
            'date' => $date,
            'summary' => $this->getAttendanceSummary($date),
            'trend' => $this->getAttendanceTrend($date, $trendDays),
            'today_status' => $this->getAttendanceSummary($date),
            'quick_stats' => $this->getAttendanceQuickStats($date),
            'highlights' => $this->getAttendanceHighlights($date),
            'departments' => $this->getAttendanceByDepartment($date),
            'recent_activities' => $this->getRecentAttendanceActivities($date),
            'filters' => ['departments' => $this->getAttendanceDepartments()],
            'employees_data' => $this->getAttendanceEmployees($date, $filters),
        ];
    }
/**
     * Payroll dashboard data for the HR payroll workspace.
     *
     * NOTE: Payroll figures are computed from the employee master records
     * (basic salary + standard allowances / deductions) until a dedicated
     * payroll / payslip table is introduced.
     */
    public function getPayrollDashboard(array $filters): array
    {
        try {
            $month = $this->validMonth((string) ($filters['month'] ?? ''));
            $search = trim((string) ($filters['search'] ?? ''));
            $department = trim((string) ($filters['department'] ?? ''));
            $statusFilter = strtoupper(trim((string) ($filters['status'] ?? '')));
            $page = max(1, (int) ($filters['page'] ?? 1));
            $perPage = min(50, max(5, (int) ($filters['per_page'] ?? 10)));
            $now = date('Y-m');

            $records = $this->buildPayrollRecords($month, $now);

            // Filter in memory so summary + distribution always share the same snapshot.
            $filtered = array_values(array_filter($records, static function (array $record) use ($search, $department, $statusFilter): bool {
                if ($search !== '' && stripos($record['full_name'] . ' ' . $record['employee_code'] . ' ' . $record['designation'], $search) === false) {
                    return false;
                }
                if ($department !== '' && $record['department'] !== $department) {
                    return false;
                }
                if ($statusFilter !== '' && strtoupper($record['status']) !== $statusFilter) {
                    return false;
                }
                return true;
            }));

            $total = count($filtered);
            $totalPages = $total ? (int) ceil($total / $perPage) : 0;
            $pageRecords = $total ? array_slice($filtered, ($page - 1) * $perPage, $perPage) : [];

            return [
                'month' => $month,
                'month_label' => date('F Y', strtotime($month . '-01')),
                'summary' => $this->getPayrollSummary($records, $month),
                'filters' => ['departments' => $this->getPayrollDepartments()],
                'employees' => $pageRecords,
                'pagination' => [
                    'current_page' => $page,
                    'per_page' => $perPage,
                    'total_items' => $total,
                    'total_pages' => $totalPages,
                ],
                'department_distribution' => $this->getPayrollDepartmentDistribution($records),
                'recent_payments' => $this->getRecentPayments($records),
            ];
        } catch (\Throwable $exception) {
            return [
                'month' => date('Y-m'),
                'month_label' => date('F Y'),
                'summary' => $this->emptyPayrollSummary(),
                'filters' => ['departments' => []],
                'employees' => [],
                'pagination' => ['current_page' => 1, 'per_page' => 10, 'total_items' => 0, 'total_pages' => 0],
                'department_distribution' => [],
                'recent_payments' => [],
            ];
        }
    }

    private function validMonth(string $value): string
    {
        if ($value !== '' && preg_match('/^\d{4}-(0[1-9]|1[0-2])$/', $value) === 1) {
            return $value;
        }
        return date('Y-m');
    }

    private function buildPayrollRecords(string $month, string $now): array
    {
        $statement = $this->db->query(
            "SELECT id, employee_code, full_name, profile_photo, department, designation,
                    joining_date, employment_type, basic_salary
             FROM employees
             ORDER BY full_name ASC"
        );
        $rows = $statement->fetchAll(PDO::FETCH_ASSOC) ?: [];

        $records = [];
        foreach ($rows as $row) {
            $record = $this->buildPayrollRecord($row, $month, $now);
            if ($record !== null) {
                $records[] = $record;
            }
        }
        return $records;
    }

    private function buildPayrollRecord(array $row, string $month, string $now): ?array
    {
        $joinDate = (string) ($row['joining_date'] ?? '');
        $monthEnd = date('Y-m-t', strtotime($month . '-01'));

        // Employees who joined after the selected month's pay date are not part of that run.
        if ($joinDate !== '' && $joinDate > $monthEnd) {
            return null;
        }

        $basic = (float) ($row['basic_salary'] ?? 0);
        $hasSalary = $basic > 0;

        $hra = round($basic * 0.30, 2);
        $specialAllowance = round($basic * 0.12, 2);
        $allowances = round($hra + $specialAllowance, 2);
        $gross = round($basic + $allowances, 2);

        $pf = $hasSalary ? round(min($basic * 0.12, 1800), 2) : 0;
        $professionalTax = $hasSalary ? 200 : 0;
        $incomeTax = $hasSalary ? round(max($gross * 0.05, 0), 2) : 0;
        $deductions = round($pf + $professionalTax + $incomeTax, 2);
        $net = round($gross - $deductions, 2);

        $paid = $hasSalary && $month < $now;
        $status = $paid ? 'PAID' : 'PENDING';

        return [
            'id' => (int) $row['id'],
            'employee_code' => $row['employee_code'] ?? '',
            'full_name' => $row['full_name'] ?? '',
            'profile_photo' => $row['profile_photo'] ?? null,
            'department' => $row['department'] ?? 'Unassigned',
            'designation' => $row['designation'] ?? 'Unassigned',
            'joining_date' => $joinDate,
            'employment_type' => $row['employment_type'] ?? 'Full-time',
            'basic_salary' => $basic,
            'hra' => $hra,
            'special_allowance' => $specialAllowance,
            'allowances' => $allowances,
            'gross_pay' => $gross,
            'pf' => $pf,
            'professional_tax' => $professionalTax,
            'income_tax' => $incomeTax,
            'deductions' => $deductions,
            'net_pay' => $net,
            'status' => $status,
            'paid_on' => $paid ? $monthEnd : null,
            'salary_pending' => !$hasSalary,
        ];
    }

    private function getPayrollSummary(array $records, string $month): array
    {
        $eligible = count($records);
        $paid = 0;
        $gross = 0.0;
        $allowances = 0.0;
        $deductions = 0.0;
        $net = 0.0;

        foreach ($records as $record) {
            if ($record['status'] === 'PAID') {
                $paid++;
            }
            $gross += $record['gross_pay'];
            $allowances += $record['allowances'];
            $deductions += $record['deductions'];
            $net += $record['net_pay'];
        }

        return [
            'month' => $month,
            'month_label' => date('F Y', strtotime($month . '-01')),
            'total_employees' => $eligible,
            'paid_employees' => $paid,
            'pending_employees' => $eligible - $paid,
            'total_payroll' => round($net, 2),
            'gross_payroll' => round($gross, 2),
            'total_allowances' => round($allowances, 2),
            'total_deductions' => round($deductions, 2),
            'average_net_pay' => $eligible ? round($net / $eligible, 2) : 0,
        ];
    }

    private function getPayrollDepartmentDistribution(array $records): array
    {
        $groups = [];
        $totalNet = array_sum(array_column($records, 'net_pay'));
        foreach ($records as $record) {
            $dept = $record['department'] ?: 'Unassigned';
            if (!isset($groups[$dept])) {
                $groups[$dept] = ['department' => $dept, 'count' => 0, 'total' => 0.0];
            }
            $groups[$dept]['count']++;
            $groups[$dept]['total'] += $record['net_pay'];
        }

        $distribution = [];
        foreach ($groups as $dept => $group) {
            $distribution[] = [
                'department' => $dept,
                'count' => $group['count'],
                'total' => round($group['total'], 2),
                'percentage' => $totalNet > 0 ? round(($group['total'] / $totalNet) * 100, 1) : 0,
            ];
        }
        usort($distribution, static fn (array $a, array $b): int => $b['total'] <=> $a['total']);

        return $distribution;
    }

    private function getRecentPayments(array $records): array
    {
        $payments = array_values(array_filter($records, static fn (array $record): bool => $record['status'] === 'PAID'));
        usort($payments, static fn (array $a, array $b): int => $b['net_pay'] <=> $a['net_pay']);

        return array_map(static fn (array $record): array => [
            'full_name' => $record['full_name'],
            'profile_photo' => $record['profile_photo'] ?? null,
            'designation' => $record['designation'],
            'department' => $record['department'],
            'net_pay' => $record['net_pay'],
            'paid_on' => $record['paid_on'],
        ], array_slice($payments, 0, 6));
    }

    private function getPayrollDepartments(): array
    {
        try {
            return $this->db->query("SELECT DISTINCT department FROM employees WHERE status = 'ACTIVE' AND department IS NOT NULL AND department != '' ORDER BY department ASC")->fetchAll(PDO::FETCH_COLUMN) ?: [];
        } catch (\Throwable $e) {
            return [];
        }
    }

    private function emptyPayrollSummary(): array
    {
        return [
            'month' => date('Y-m'),
            'month_label' => date('F Y'),
            'total_employees' => 0,
            'paid_employees' => 0,
            'pending_employees' => 0,
            'total_payroll' => 0,
            'gross_payroll' => 0,
            'total_allowances' => 0,
            'total_deductions' => 0,
            'average_net_pay' => 0,
        ];
    }

    private function getAttendanceSummary(string $date): array
    {
        $total = (int) $this->db->query("SELECT COUNT(*) FROM employees WHERE status = 'ACTIVE'")->fetchColumn();
        $statement = $this->db->prepare(
            "SELECT SUM(status = 'PRESENT') AS present, SUM(status = 'ABSENT') AS absent,
                    SUM(status = 'LATE') AS late, SUM(status = 'LEAVE') AS half_day
             FROM attendance_records WHERE attendance_date = :date"
        );
        $statement->execute([':date' => $date]);
        $attendance = $statement->fetch(PDO::FETCH_ASSOC) ?: [];
        $leaveStatement = $this->db->prepare(
            "SELECT COUNT(DISTINCT employee_id) FROM leave_requests
             WHERE status = 'APPROVED' AND from_date <= :leave_from AND to_date >= :leave_to"
        );
        $leaveStatement->execute([':leave_from' => $date, ':leave_to' => $date]);

        $present = (int) ($attendance['present'] ?? 0);
        $late = (int) ($attendance['late'] ?? 0);
        $onLeave = max((int) $leaveStatement->fetchColumn(), (int) ($attendance['half_day'] ?? 0));
        $recordedAbsent = (int) ($attendance['absent'] ?? 0);
        $absent = max($recordedAbsent, max(0, $total - $present - $late - $onLeave));

        return [
            'total_employees' => $total,
            'present' => $present,
            'absent' => $absent,
            'late' => $late,
            'on_leave' => $onLeave,
            'half_day' => (int) ($attendance['half_day'] ?? 0),
            'present_percentage' => $total ? round(($present / $total) * 100, 1) : 0,
            'absent_percentage' => $total ? round(($absent / $total) * 100, 1) : 0,
            'late_percentage' => $total ? round(($late / $total) * 100, 1) : 0,
            'on_leave_percentage' => $total ? round(($onLeave / $total) * 100, 1) : 0,
        ];
    }

    private function getAttendanceTrend(string $date, int $days): array
    {
        $start = (new \DateTimeImmutable($date))->modify('-' . ($days - 1) . ' days');
        $statement = $this->db->prepare(
            "SELECT attendance_date, SUM(status = 'PRESENT') AS present,
                    SUM(status = 'ABSENT') AS absent, SUM(status = 'LATE') AS late
             FROM attendance_records WHERE attendance_date BETWEEN :start AND :end
             GROUP BY attendance_date"
        );
        $statement->execute([':start' => $start->format('Y-m-d'), ':end' => $date]);
        $rows = [];
        foreach ($statement->fetchAll(PDO::FETCH_ASSOC) ?: [] as $row) $rows[$row['attendance_date']] = $row;

        $trend = [];
        for ($i = 0; $i < $days; $i++) {
            $day = $start->modify("+{$i} days");
            $key = $day->format('Y-m-d');
            $row = $rows[$key] ?? [];
            $trend[] = ['date' => $key, 'label' => $day->format('d M'), 'present' => (int) ($row['present'] ?? 0), 'absent' => (int) ($row['absent'] ?? 0), 'late' => (int) ($row['late'] ?? 0)];
        }
        return $trend;
    }

    private function getAttendanceQuickStats(string $date): array
    {
        $statement = $this->db->prepare(
            "SELECT AVG(TIME_TO_SEC(TIME(check_in))) AS avg_check_in,
                    AVG(TIME_TO_SEC(TIME(check_out))) AS avg_check_out,
                    AVG(CASE WHEN check_in IS NOT NULL AND check_out IS NOT NULL THEN TIMESTAMPDIFF(MINUTE, check_in, check_out) END) AS avg_work_minutes,
                    AVG(status = 'PRESENT') * 100 AS on_time_percentage
             FROM attendance_records WHERE attendance_date = :date"
        );
        $statement->execute([':date' => $date]);
        $row = $statement->fetch(PDO::FETCH_ASSOC) ?: [];
        $formatTime = static fn ($seconds): ?string => $seconds === null ? null : date('h:i A', (int) $seconds);
        return [
            'average_check_in' => $formatTime($row['avg_check_in'] ?? null),
            'average_check_out' => $formatTime($row['avg_check_out'] ?? null),
            'average_work_minutes' => (int) round((float) ($row['avg_work_minutes'] ?? 0)),
            'on_time_percentage' => round((float) ($row['on_time_percentage'] ?? 0), 1),
        ];
    }

    private function getAttendanceHighlights(string $date): array
    {
        $leave = $this->db->prepare("SELECT COUNT(*) FROM leave_requests WHERE status = 'APPROVED' AND from_date <= :leave_from AND to_date >= :leave_to");
        $leave->execute([':leave_from' => $date, ':leave_to' => $date]);
        return ['work_from_home' => 0, 'business_trip' => 0, 'outdoor_duty' => 0, 'early_leave' => 0, 'on_leave' => (int) $leave->fetchColumn()];
    }

    private function getAttendanceByDepartment(string $date): array
    {
        $statement = $this->db->prepare(
            "SELECT COALESCE(NULLIF(e.department, ''), 'Unassigned') AS department,
                    COUNT(*) AS total_employees,
                    SUM(ar.status IN ('PRESENT', 'LATE')) AS present_employees
             FROM employees e
             LEFT JOIN attendance_records ar ON ar.employee_id = e.id AND ar.attendance_date = :date
             WHERE e.status = 'ACTIVE'
             GROUP BY COALESCE(NULLIF(e.department, ''), 'Unassigned')
             ORDER BY present_employees DESC, department ASC"
        );
        $statement->execute([':date' => $date]);
        return array_map(static function (array $row): array {
            $total = (int) $row['total_employees']; $present = (int) $row['present_employees'];
            return ['department' => $row['department'], 'present' => $present, 'total' => $total, 'percentage' => $total ? round(($present / $total) * 100, 1) : 0];
        }, $statement->fetchAll(PDO::FETCH_ASSOC) ?: []);
    }

    private function getAttendanceEmployees(string $date, array $filters): array
    {
        $where = ["e.status = 'ACTIVE'"];
        $params = [
            ':date_att' => $date,
            ':leave_from' => $date,
            ':leave_to' => $date,
        ];
        if (trim((string) ($filters['search'] ?? '')) !== '') {
            $params[':search'] = '%' . trim((string) $filters['search']) . '%';
            $where[] = '(e.full_name LIKE :search OR e.employee_code LIKE :search OR e.department LIKE :search)';
        }
        if (trim((string) ($filters['department'] ?? '')) !== '') {
            $params[':department'] = trim((string) $filters['department']);
            $where[] = 'e.department = :department';
        }
        $whereSql = implode(' AND ', $where);
        $page = max(1, (int) ($filters['page'] ?? 1));
        $perPage = min(100, max(1, (int) ($filters['per_page'] ?? 10)));
        $offset = ($page - 1) * $perPage;

        $countParams = array_filter(
            $params,
            static fn ($key) => !in_array($key, [':date_att', ':leave_from', ':leave_to'], true),
            ARRAY_FILTER_USE_KEY
        );
        $count = $this->db->prepare("SELECT COUNT(*) FROM employees e WHERE {$whereSql}");
        $count->execute($countParams);
        $total = (int) $count->fetchColumn();

        $statement = $this->db->prepare(
            "SELECT e.id, e.full_name, e.employee_code, e.department, e.designation, e.profile_photo,
                    ar.check_in, ar.check_out,
                    CASE WHEN lr.id IS NOT NULL THEN 'ON_LEAVE' WHEN ar.id IS NULL THEN 'ABSENT' ELSE ar.status END AS attendance_status
             FROM employees e
             LEFT JOIN attendance_records ar ON ar.employee_id = e.id AND ar.attendance_date = :date_att
             LEFT JOIN leave_requests lr ON lr.employee_id = e.id AND lr.status = 'APPROVED' AND lr.from_date <= :leave_from AND lr.to_date >= :leave_to
             WHERE {$whereSql} ORDER BY e.full_name ASC LIMIT :limit OFFSET :offset"
        );
        foreach ($params as $key => $value) $statement->bindValue($key, $value);
        $statement->bindValue(':limit', $perPage, PDO::PARAM_INT);
        $statement->bindValue(':offset', $offset, PDO::PARAM_INT);
        $statement->execute();
        $employees = array_map(static function (array $row): array {
            $checkIn = $row['check_in'] ? date('h:i A', strtotime($row['check_in'])) : null; $checkOut = $row['check_out'] ? date('h:i A', strtotime($row['check_out'])) : null;
            $minutes = ($row['check_in'] && $row['check_out']) ? (int) round((strtotime($row['check_out']) - strtotime($row['check_in'])) / 60) : null;
            return ['id' => (int) $row['id'], 'name' => $row['full_name'], 'employee_code' => $row['employee_code'], 'department' => $row['department'] ?: 'Unassigned', 'designation' => $row['designation'], 'profile_photo' => $row['profile_photo'], 'check_in' => $checkIn, 'check_out' => $checkOut, 'total_minutes' => $minutes, 'status' => $row['attendance_status']];
        }, $statement->fetchAll(PDO::FETCH_ASSOC) ?: []);
        return ['employees' => $employees, 'pagination' => ['current_page' => $page, 'per_page' => $perPage, 'total_items' => $total, 'total_pages' => $total ? (int) ceil($total / $perPage) : 0]];
    }

    private function getRecentAttendanceActivities(string $date): array
    {
        $statement = $this->db->prepare("SELECT e.full_name, e.profile_photo, ar.status, ar.check_in, ar.check_out FROM attendance_records ar INNER JOIN employees e ON e.id = ar.employee_id WHERE ar.attendance_date = :date ORDER BY COALESCE(ar.check_out, ar.check_in) DESC LIMIT 10");
        $statement->execute([':date' => $date]);
        return array_map(static function (array $row): array { $time = $row['check_out'] ?: $row['check_in']; return ['employee_name' => $row['full_name'], 'profile_photo' => $row['profile_photo'], 'status' => $row['status'], 'time' => $time ? date('h:i A', strtotime($time)) : null]; }, $statement->fetchAll(PDO::FETCH_ASSOC) ?: []);
    }

    private function getAttendanceDepartments(): array
    {
        return $this->db->query("SELECT DISTINCT department FROM employees WHERE status = 'ACTIVE' AND department IS NOT NULL AND department != '' ORDER BY department ASC")->fetchAll(PDO::FETCH_COLUMN) ?: [];
    }

    private function validDate(string $value): bool
    {
        $date = \DateTimeImmutable::createFromFormat('!Y-m-d', $value);
        return $date && $date->format('Y-m-d') === $value;
    }

    private function getSummary(string $today, string $monthStart): array
    {
        $employees = $this->db->query(
            "SELECT COUNT(*) AS total, SUM(status = 'ACTIVE') AS active FROM employees"
        )->fetch(PDO::FETCH_ASSOC) ?: [];

        $attendance = $this->db->prepare(
            "SELECT SUM(status = 'PRESENT') AS present,
                    SUM(status = 'ABSENT') AS absent,
                    SUM(status = 'LATE') AS late,
                    SUM(status = 'LEAVE') AS on_leave
             FROM attendance_records WHERE attendance_date = :today"
        );
        $attendance->execute([':today' => $today]);
        $attendance = $attendance->fetch(PDO::FETCH_ASSOC) ?: [];

        $onLeave = $this->db->prepare(
            "SELECT COUNT(*) FROM leave_requests
             WHERE status = 'APPROVED' AND from_date <= :today AND to_date >= :today"
        );
        $onLeave->execute([':today' => $today]);

        $pending = $this->db->query(
            "SELECT COUNT(*) FROM leave_requests WHERE status = 'PENDING'"
        )->fetchColumn();

        $newJoiners = $this->db->prepare(
            "SELECT COUNT(*) FROM employees WHERE joining_date >= :month_start AND joining_date <= :today"
        );
        $newJoiners->execute([':month_start' => $monthStart, ':today' => $today]);

        return [
            'total_employees' => (int) ($employees['total'] ?? 0),
            'present_today' => (int) ($attendance['present'] ?? 0),
            'absent_today' => (int) ($attendance['absent'] ?? 0),
            'late_today' => (int) ($attendance['late'] ?? 0),
            'on_leave_today' => max((int) $onLeave->fetchColumn(), (int) ($attendance['on_leave'] ?? 0)),
            'new_joiners' => (int) $newJoiners->fetchColumn(),
            'pending_leave_requests' => (int) $pending,
            'documents_expiring' => 0,
        ];
    }

    private function getAttendance(string $weekStart, string $today): array
    {
        $statement = $this->db->prepare(
            "SELECT attendance_date AS date,
                    SUM(status = 'PRESENT') AS present,
                    SUM(status = 'ABSENT') AS absent,
                    SUM(status = 'LATE') AS late,
                    SUM(status = 'LEAVE') AS half_day
             FROM attendance_records
             WHERE attendance_date BETWEEN :week_start AND :today
             GROUP BY attendance_date ORDER BY attendance_date"
        );
        $statement->execute([':week_start' => $weekStart, ':today' => $today]);
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    private function getLeaveOverview(string $monthStart, string $today): array
    {
        $statement = $this->db->prepare(
            "SELECT lt.name AS leave_type, COUNT(*) AS requests
             FROM leave_requests lr
             INNER JOIN leave_types lt ON lt.id = lr.leave_type_id
             WHERE lr.status IN ('APPROVED', 'PENDING')
               AND lr.from_date <= :today AND lr.to_date >= :month_start
             GROUP BY lt.id, lt.name ORDER BY requests DESC"
        );
        $statement->execute([':month_start' => $monthStart, ':today' => $today]);
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    private function getPendingLeaveRequests(int $limit = 5): array
    {
        $statement = $this->db->query(
            "SELECT lr.id, e.full_name AS employee_name, e.profile_photo,
                    lt.name AS leave_type, lr.from_date, lr.to_date, lr.total_days
             FROM leave_requests lr
             LEFT JOIN employees e ON e.id = lr.employee_id
             LEFT JOIN leave_types lt ON lt.id = lr.leave_type_id
             WHERE lr.status = 'PENDING'
             ORDER BY lr.created_at ASC LIMIT {$limit}"
        );
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    private function getNewJoiners(string $monthStart, int $limit = 5): array
    {
        $statement = $this->db->prepare(
            "SELECT id, full_name, designation, joining_date, profile_photo
             FROM employees WHERE joining_date >= :month_start
             ORDER BY joining_date DESC, id DESC LIMIT {$limit}"
        );
        $statement->execute([':month_start' => $monthStart]);
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    private function getUpcomingBirthdays(): array
    {
        return $this->getUpcomingEmployeeDates('date_of_birth', 'birthday');
    }

    private function getUpcomingAnniversaries(): array
    {
        return $this->getUpcomingEmployeeDates('joining_date', 'anniversary');
    }

    private function getUpcomingEmployeeDates(string $column, string $dateKey): array
    {
        $statement = $this->db->query(
            "SELECT id, full_name, designation, {$column} AS event_date, profile_photo
             FROM employees WHERE {$column} IS NOT NULL
             ORDER BY ((MONTH({$column}) - MONTH(CURDATE()) + 12) % 12),
                      DAY({$column}) LIMIT 5"
        );

        return array_map(static function (array $row) use ($dateKey): array {
            $row['event_type'] = $dateKey;
            return $row;
        }, $statement->fetchAll(PDO::FETCH_ASSOC));
    }
}

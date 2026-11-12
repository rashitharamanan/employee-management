<?php

namespace App\Repositories\Manager;

use PDO;

class ManagerDashboardRepository
{
    public function __construct(private PDO $db)
    {
    }

    /**
     * Ensure database tables for Manager Portal exist
     */
    public function ensureSchema(): void
    {
        // 1. Manager Approvals Table
        $this->db->exec("CREATE TABLE IF NOT EXISTS manager_approvals (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            manager_id INT UNSIGNED NULL,
            employee_name VARCHAR(150) NOT NULL,
            request_type VARCHAR(100) NOT NULL,
            category ENUM('LEAVE','REMOTE','DOCUMENT','TRAINING','OTHER') NOT NULL DEFAULT 'LEAVE',
            icon_type VARCHAR(50) NOT NULL DEFAULT 'leave',
            request_date DATE NOT NULL,
            status ENUM('PENDING','APPROVED','REJECTED') NOT NULL DEFAULT 'PENDING',
            remarks TEXT NULL,
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            KEY idx_appr_mgr (manager_id),
            KEY idx_appr_status (status)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

        // 2. Manager Team Members Table
        $this->db->exec("CREATE TABLE IF NOT EXISTS manager_team_members (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            manager_id INT UNSIGNED NULL,
            name VARCHAR(150) NOT NULL,
            role_title VARCHAR(100) NOT NULL,
            avatar_initials VARCHAR(10) NOT NULL,
            status ENUM('PRESENT','ON_LEAVE','ABSENT') NOT NULL DEFAULT 'PRESENT',
            email VARCHAR(150) NULL,
            phone VARCHAR(30) NULL,
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

        // 3. Manager Trainings Table
        $this->db->exec("CREATE TABLE IF NOT EXISTS manager_trainings (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            title VARCHAR(200) NOT NULL,
            target_audience VARCHAR(150) NOT NULL,
            scheduled_date DATE NOT NULL,
            status ENUM('REGISTERED','NOT_STARTED','COMPLETED') NOT NULL DEFAULT 'NOT_STARTED',
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

        // 4. Manager Competencies Table
        $this->db->exec("CREATE TABLE IF NOT EXISTS manager_team_competencies (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            quarter VARCHAR(20) NOT NULL,
            competency_name VARCHAR(100) NOT NULL,
            score DECIMAL(3,1) NOT NULL,
            max_score DECIMAL(3,1) NOT NULL DEFAULT 5.0,
            bar_color VARCHAR(30) NOT NULL DEFAULT 'blue'
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

        // 5. Manager Tasks Table
        $this->db->exec("CREATE TABLE IF NOT EXISTS manager_tasks (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            manager_id INT UNSIGNED NULL,
            assigned_to INT UNSIGNED NOT NULL,
            task_title VARCHAR(255) NOT NULL,
            description TEXT NULL,
            priority ENUM('HIGH','MEDIUM','LOW') NOT NULL DEFAULT 'MEDIUM',
            status ENUM('NOT_STARTED','IN_PROGRESS','COMPLETED') NOT NULL DEFAULT 'NOT_STARTED',
            progress_pct INT NOT NULL DEFAULT 0,
            start_date DATE NULL,
            due_date DATE NULL,
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            KEY idx_task_assigned (assigned_to),
            KEY idx_task_status (status)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

        // 6. Manager Daily Attendance Table
        $this->db->exec("CREATE TABLE IF NOT EXISTS manager_daily_attendance (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            date DATE NOT NULL,
            employee_name VARCHAR(150) NOT NULL,
            avatar_initials VARCHAR(10) NOT NULL,
            department VARCHAR(100) NOT NULL,
            check_in VARCHAR(20) NULL,
            check_out VARCHAR(20) NULL,
            work_hours VARCHAR(20) NULL,
            status ENUM('PRESENT','ON_LEAVE','ABSENT') NOT NULL DEFAULT 'PRESENT',
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            KEY idx_mda_date (date),
            KEY idx_mda_dept (department)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

        // 7. Manager Attendance Requests Table
        $this->db->exec("CREATE TABLE IF NOT EXISTS manager_attendance_requests (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            employee_name VARCHAR(150) NOT NULL,
            avatar_initials VARCHAR(10) NOT NULL,
            department VARCHAR(100) NOT NULL,
            request_type VARCHAR(100) NOT NULL,
            duration VARCHAR(50) NOT NULL,
            request_date DATE NOT NULL,
            status ENUM('PENDING','APPROVED','REJECTED') NOT NULL DEFAULT 'PENDING',
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

        // 8. Manager Performance Reviews Table
        $this->db->exec("CREATE TABLE IF NOT EXISTS manager_performance_reviews (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            manager_id INT UNSIGNED NULL,
            employee_name VARCHAR(150) NOT NULL,
            role_title VARCHAR(100) NOT NULL,
            avatar_initials VARCHAR(10) NOT NULL,
            review_period VARCHAR(100) NOT NULL,
            rating DECIMAL(2,1) NOT NULL DEFAULT 4.0,
            status ENUM('COMPLETED','IN_REVIEW','PENDING') NOT NULL DEFAULT 'IN_REVIEW',
            reviewer_name VARCHAR(150) NOT NULL DEFAULT 'Arun Kumar',
            feedback TEXT NULL,
            review_date DATE NULL,
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            KEY idx_mpr_status (status)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

        // 9. Manager Team Goals Table
        $this->db->exec("CREATE TABLE IF NOT EXISTS manager_team_goals (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            manager_id INT UNSIGNED NULL,
            title VARCHAR(255) NOT NULL,
            progress_pct INT NOT NULL DEFAULT 0,
            due_date VARCHAR(50) NOT NULL,
            status ENUM('ON_TRACK','IN_PROGRESS','AT_RISK','COMPLETED') NOT NULL DEFAULT 'IN_PROGRESS',
            bar_color VARCHAR(30) NOT NULL DEFAULT '#3b82f6',
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            KEY idx_mtg_status (status)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

        // 10. Manager Performance Trends Table
        $this->db->exec("CREATE TABLE IF NOT EXISTS manager_performance_trends (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            month_idx INT NOT NULL,
            month_label VARCHAR(20) NOT NULL,
            month_name VARCHAR(50) NOT NULL,
            year INT NOT NULL,
            rating DECIMAL(2,1) NOT NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

        // 11. Manager Performance Distribution Table
        $this->db->exec("CREATE TABLE IF NOT EXISTS manager_performance_distribution (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            category_name VARCHAR(100) NOT NULL,
            score_range VARCHAR(50) NOT NULL,
            count INT NOT NULL DEFAULT 0,
            color VARCHAR(30) NOT NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

        // 12. Manager Reports Records Table
        $this->db->exec("CREATE TABLE IF NOT EXISTS manager_reports_records (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            employee_name VARCHAR(150) NOT NULL,
            department VARCHAR(100) NOT NULL,
            avatar_initials VARCHAR(10) NOT NULL,
            attendance_rate INT NOT NULL DEFAULT 90,
            tasks_completed INT NOT NULL DEFAULT 10,
            average_rating DECIMAL(2,1) NOT NULL DEFAULT 4.2,
            status ENUM('EXCELLENT','GOOD','NEEDS_SUPPORT') NOT NULL DEFAULT 'GOOD',
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            KEY idx_mrr_dept (department)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

        // Ensure columns in manager_team_members
        try {
            $cols = $this->db->query("SHOW COLUMNS FROM manager_team_members LIKE 'department'")->fetchAll();
            if (empty($cols)) {
                $this->db->exec("ALTER TABLE manager_team_members 
                    ADD COLUMN department VARCHAR(100) NOT NULL DEFAULT 'Development' AFTER role_title,
                    ADD COLUMN workload_current INT NOT NULL DEFAULT 5 AFTER status,
                    ADD COLUMN workload_max INT NOT NULL DEFAULT 8 AFTER workload_current,
                    ADD COLUMN performance_rating DECIMAL(2,1) NOT NULL DEFAULT 4.2 AFTER workload_max");
            }
        } catch (\Throwable $_) {}
    }


    /**
     * Auto-seed demo records matching the screenshot
     */
    public function seedIfEmpty(): void
    {
        $this->ensureSchema();

        // 1. Seed default Manager User in users table if absent
        try {
            $userCheck = (int) $this->db->query("SELECT COUNT(*) FROM users WHERE username = 'manager' OR email = 'arun.kumar@mindazzle.com'")->fetchColumn();
            if ($userCheck === 0) {
                $hash = password_hash('password', PASSWORD_DEFAULT);
                $stmt = $this->db->prepare("INSERT INTO users (role_id, username, email, password_hash, status, created_at) VALUES (3, 'manager', 'arun.kumar@mindazzle.com', ?, 'ACTIVE', NOW())");
                $stmt->execute([$hash]);
            }
        } catch (\Throwable $_) {}

        // 2. Seed Approvals if empty
        $apprCount = (int) $this->db->query("SELECT COUNT(*) FROM manager_approvals")->fetchColumn();
        if ($apprCount === 0) {
            $approvals = [
                ['Rohan Verma', 'Leave Request', 'LEAVE', 'leave', '2025-08-26', 'PENDING'],
                ['Neha Tiwari', 'Remote Work', 'REMOTE', 'remote', '2025-08-25', 'PENDING'],
                ['Arun Kumar', 'Document Update', 'DOCUMENT', 'document', '2025-08-24', 'PENDING'],
                ['Sneha Iyer', 'Training Request', 'TRAINING', 'training', '2025-08-22', 'PENDING'],
                ['Karan Mehta', 'Leave Request', 'LEAVE', 'leave', '2025-08-21', 'PENDING']
            ];

            $stmt = $this->db->prepare("INSERT INTO manager_approvals (employee_name, request_type, category, icon_type, request_date, status) VALUES (?, ?, ?, ?, ?, ?)");
            foreach ($approvals as $a) {
                $stmt->execute($a);
            }
        }

        // 3. Seed Team Members (12 members total, top 6 matching screenshot)
        $teamCount = (int) $this->db->query("SELECT COUNT(*) FROM manager_team_members")->fetchColumn();
        if ($teamCount < 12) {
            $this->db->exec("DELETE FROM manager_team_members");
            $members = [
                ['Priya Sharma', 'UI/UX Designer', 'Design', 'PS', 'PRESENT', 6, 8, 4.5, 'priya.sharma@mindazzle.com'],
                ['Rohit Kulkarni', 'Frontend Developer', 'Development', 'RK', 'PRESENT', 5, 8, 4.2, 'rohit.kulkarni@mindazzle.com'],
                ['Sneha Iyer', 'HR Executive', 'HR', 'SI', 'ON_LEAVE', 2, 8, 4.0, 'sneha.iyer@mindazzle.com'],
                ['Rohan Verma', 'Backend Developer', 'Development', 'RV', 'PRESENT', 7, 8, 4.3, 'rohan.verma@mindazzle.com'],
                ['Neha Tiwari', 'Content Writer', 'Marketing', 'NT', 'ON_LEAVE', 3, 8, 4.1, 'neha.tiwari@mindazzle.com'],
                ['Aditya Kapoor', 'QA Engineer', 'Quality Assurance', 'AK', 'PRESENT', 5, 8, 4.4, 'aditya.kapoor@mindazzle.com'],
                // Additional 6 to make 12 total (9 present, 2 on leave, 1 absent = exactly 75% present)
                ['Vikram Malhotra', 'DevOps Engineer', 'Infrastructure', 'VM', 'PRESENT', 4, 8, 4.6, 'vikram.m@mindazzle.com'],
                ['Ananya Sengupta', 'Product Designer', 'Design', 'AS', 'PRESENT', 5, 8, 4.3, 'ananya.s@mindazzle.com'],
                ['Siddharth Rao', 'Full Stack Developer', 'Development', 'SR', 'PRESENT', 6, 8, 4.2, 'siddharth.r@mindazzle.com'],
                ['Divya Krishnan', 'Data Analyst', 'Product', 'DK', 'PRESENT', 4, 8, 4.5, 'divya.k@mindazzle.com'],
                ['Manish Joshi', 'Mobile Developer', 'Development', 'MJ', 'PRESENT', 5, 8, 4.1, 'manish.j@mindazzle.com'],
                ['Tanvi Chawla', 'Technical Writer', 'Marketing', 'TC', 'ABSENT', 3, 8, 4.2, 'tanvi.c@mindazzle.com']
            ];

            $stmt = $this->db->prepare("INSERT INTO manager_team_members (name, role_title, department, avatar_initials, status, workload_current, workload_max, performance_rating, email) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
            foreach ($members as $m) {
                $stmt->execute($m);
            }
        }

        // 4. Seed Trainings if empty
        $trainCount = (int) $this->db->query("SELECT COUNT(*) FROM manager_trainings")->fetchColumn();
        if ($trainCount === 0) {
            $trainings = [
                ['Advanced UI/UX Workshop', 'For Design Team', '2025-08-28', 'REGISTERED'],
                ['Agile Project Management', 'For Development Team', '2025-08-30', 'NOT_STARTED'],
                ['Leadership Skills', 'For Team Leads', '2025-09-05', 'NOT_STARTED'],
                ['Effective Communication', 'For All Team Members', '2025-09-12', 'NOT_STARTED']
            ];

            $stmt = $this->db->prepare("INSERT INTO manager_trainings (title, target_audience, scheduled_date, status) VALUES (?, ?, ?, ?)");
            foreach ($trainings as $t) {
                $stmt->execute($t);
            }
        }

        // 5. Seed Competencies if empty
        $compCount = (int) $this->db->query("SELECT COUNT(*) FROM manager_team_competencies WHERE quarter = 'Q3 2025'")->fetchColumn();
        if ($compCount === 0) {
            $competencies = [
                ['Q3 2025', 'Job Knowledge', 4.3, 5.0, 'blue'],
                ['Q3 2025', 'Quality of Work', 4.1, 5.0, 'teal'],
                ['Q3 2025', 'Communication', 4.0, 5.0, 'amber'],
                ['Q3 2025', 'Teamwork', 4.4, 5.0, 'purple'],
                ['Q3 2025', 'Initiative', 3.9, 5.0, 'magenta']
            ];

            $stmt = $this->db->prepare("INSERT INTO manager_team_competencies (quarter, competency_name, score, max_score, bar_color) VALUES (?, ?, ?, ?, ?)");
            foreach ($competencies as $c) {
                $stmt->execute($c);
            }
        }

        // 6. Seed Recent Tasks if empty or if tasks have invalid assigned_to
        $validTasks = (int) $this->db->query("SELECT COUNT(*) FROM manager_tasks t JOIN manager_team_members m ON t.assigned_to = m.id")->fetchColumn();
        if ($validTasks < 5) {
            $this->db->exec("DELETE FROM manager_tasks");
            $tasks = [
                ['Website Redesign', 'Priya Sharma', 'HIGH', '2025-08-28', 'IN_PROGRESS', 60, 'Redesign corporate landing page and responsive components.'],
                ['API Integration', 'Rohit Kulkarni', 'MEDIUM', '2025-08-30', 'NOT_STARTED', 0, 'Integrate third-party payroll payment gateway API.'],
                ['Employee Handbook', 'Sneha Iyer', 'LOW', '2025-08-25', 'COMPLETED', 100, 'Update FY 2025-26 HR policies and conduct code.'],
                ['Bug Fixes - Release 2.1', 'Rohan Verma', 'HIGH', '2025-08-27', 'IN_PROGRESS', 40, 'Patch authentication token timeout and cache bugs.'],
                ['Social Media Content', 'Neha Tiwari', 'MEDIUM', '2025-09-02', 'NOT_STARTED', 0, 'Prepare promotional copy and LinkedIn case study graphics.']
            ];

            $stmt = $this->db->prepare("INSERT INTO manager_tasks (assigned_to, task_title, description, priority, due_date, status, progress_pct) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $findMember = $this->db->prepare("SELECT id FROM manager_team_members WHERE name = ? LIMIT 1");
            foreach ($tasks as $t) {
                $findMember->execute([$t[1]]);
                $empId = (int) ($findMember->fetchColumn() ?: 1);
                $stmt->execute([$empId, $t[0], $t[6], $t[2], $t[3], $t[4], $t[5]]);
            }
        }

        // 7. Seed Daily Attendance (exact 6 rows matching screenshot)
        $attCount = (int) $this->db->query("SELECT COUNT(*) FROM manager_daily_attendance WHERE date = '2025-08-26'")->fetchColumn();
        if ($attCount === 0) {
            $records = [
                ['2025-08-26', 'Priya Sharma', 'PS', 'Design', '09:02 AM', '06:10 PM', '9h 8m', 'PRESENT'],
                ['2025-08-26', 'Rohan Verma', 'RV', 'Development', '09:15 AM', '-', '-', 'ON_LEAVE'],
                ['2025-08-26', 'Sneha Iyer', 'SI', 'HR', '-', '-', '-', 'ON_LEAVE'],
                ['2025-08-26', 'Aditya Kapoor', 'AK', 'QA', '09:05 AM', '06:00 PM', '8h 55m', 'PRESENT'],
                ['2025-08-26', 'Neha Tiwari', 'NT', 'Marketing', '09:20 AM', '-', '-', 'ABSENT'],
                ['2025-08-26', 'Rohit Kulkarni', 'RK', 'Development', '09:10 AM', '06:25 PM', '9h 15m', 'PRESENT']
            ];

            $stmt = $this->db->prepare("INSERT INTO manager_daily_attendance (date, employee_name, avatar_initials, department, check_in, check_out, work_hours, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            foreach ($records as $r) {
                $stmt->execute($r);
            }
        }

        // 8. Seed Attendance Requests
        $reqCount = (int) $this->db->query("SELECT COUNT(*) FROM manager_attendance_requests")->fetchColumn();
        if ($reqCount === 0) {
            $requests = [
                ['Sneha Iyer', 'SI', 'HR', 'Casual Leave', '1 day', '2025-08-28', 'PENDING'],
                ['Rohan Verma', 'RV', 'Development', 'Sick Leave', '2 days', '2025-08-27', 'PENDING']
            ];

            $stmt = $this->db->prepare("INSERT INTO manager_attendance_requests (employee_name, avatar_initials, department, request_type, duration, request_date, status) VALUES (?, ?, ?, ?, ?, ?, ?)");
            foreach ($requests as $rq) {
                $stmt->execute($rq);
            }
        }

        // 9. Seed Performance Trends (Last 6 Months: Mar -> Aug 2025)
        $trendCount = (int) $this->db->query("SELECT COUNT(*) FROM manager_performance_trends")->fetchColumn();
        if ($trendCount === 0) {
            $trends = [
                [1, 'Mar', 'March', 2025, 2.8],
                [2, 'Apr', 'April', 2025, 3.1],
                [3, 'May', 'May', 2025, 3.4],
                [4, 'Jun', 'June', 2025, 3.7],
                [5, 'Jul', 'July', 2025, 4.0],
                [6, 'Aug', 'August', 2025, 4.2]
            ];

            $stmt = $this->db->prepare("INSERT INTO manager_performance_trends (month_idx, month_label, month_name, year, rating) VALUES (?, ?, ?, ?, ?)");
            foreach ($trends as $t) {
                $stmt->execute($t);
            }
        }

        // 10. Seed Performance Distribution
        $distCount = (int) $this->db->query("SELECT COUNT(*) FROM manager_performance_distribution")->fetchColumn();
        if ($distCount === 0) {
            $dist = [
                ['Excellent (4.5 – 5.0)', '4.5 - 5.0', 3, '#14b8a6'],
                ['Good (3.5 – 4.4)', '3.5 - 4.4', 5, '#0284c7'],
                ['Average (2.5 – 3.4)', '2.5 - 3.4', 3, '#f59e0b'],
                ['Needs Improvement (< 2.5)', '< 2.5', 1, '#f43f5e']
            ];

            $stmt = $this->db->prepare("INSERT INTO manager_performance_distribution (category_name, score_range, count, color) VALUES (?, ?, ?, ?)");
            foreach ($dist as $d) {
                $stmt->execute($d);
            }
        }

        // 11. Seed Recent Reviews (5 items matching screenshot)
        $revCount = (int) $this->db->query("SELECT COUNT(*) FROM manager_performance_reviews")->fetchColumn();
        if ($revCount === 0) {
            $reviews = [
                ['Priya Sharma', 'UI/UX Designer', 'PS', 'Jan – Jun 2025', 4.8, 'COMPLETED', 'Arun Kumar', 'Exceeded expectations in design system revamp and sprint deliverables.'],
                ['Rohit Kulkarni', 'Frontend Developer', 'RK', 'Jan – Jun 2025', 4.2, 'COMPLETED', 'Arun Kumar', 'Strong delivery of UI components, timely feature releases.'],
                ['Sneha Iyer', 'HR Executive', 'SI', 'Jan – Jun 2025', 4.0, 'IN_REVIEW', 'Arun Kumar', 'Effective onboarding workflows and team pulse surveys.'],
                ['Rohan Verma', 'Backend Developer', 'RV', 'Jan – Jun 2025', 3.8, 'IN_REVIEW', 'Arun Kumar', 'Reliable API development, working on query optimization.'],
                ['Neha Tiwari', 'Content Writer', 'NT', 'Jan – Jun 2025', 4.1, 'COMPLETED', 'Arun Kumar', 'High quality content and marketing collateral generation.']
            ];

            $stmt = $this->db->prepare("INSERT INTO manager_performance_reviews (employee_name, role_title, avatar_initials, review_period, rating, status, reviewer_name, feedback, review_date) VALUES (?, ?, ?, ?, ?, ?, ?, ?, '2025-08-26')");
            foreach ($reviews as $rev) {
                $stmt->execute($rev);
            }
        }

        // 12. Seed Team Goals (5 items matching screenshot)
        $goalCount = (int) $this->db->query("SELECT COUNT(*) FROM manager_team_goals")->fetchColumn();
        if ($goalCount === 0) {
            $goals = [
                ['Improve client satisfaction', 80, '30 Sep', 'ON_TRACK', '#14b8a6'],
                ['Complete product module', 60, '15 Oct', 'IN_PROGRESS', '#3b82f6'],
                ['Reduce bug count', 40, '30 Sep', 'AT_RISK', '#f59e0b'],
                ['Enhance documentation', 100, '10 Sep', 'COMPLETED', '#14b8a6'],
                ['Team skill development', 50, '25 Oct', 'IN_PROGRESS', '#3b82f6']
            ];

            $stmt = $this->db->prepare("INSERT INTO manager_team_goals (title, progress_pct, due_date, status, bar_color) VALUES (?, ?, ?, ?, ?)");
            foreach ($goals as $g) {
                $stmt->execute($g);
            }
        }

        // 13. Seed Reports Records (exact 6 rows matching screenshot)
        $repCount = (int) $this->db->query("SELECT COUNT(*) FROM manager_reports_records")->fetchColumn();
        if ($repCount === 0) {
            $repRecords = [
                ['Priya Sharma', 'Design', 'PS', 96, 12, 4.8, 'EXCELLENT'],
                ['Rohit Kulkarni', 'Development', 'RK', 90, 10, 4.6, 'GOOD'],
                ['Sneha Iyer', 'HR', 'SI', 88, 8, 4.2, 'GOOD'],
                ['Rohan Verma', 'Development', 'RV', 95, 11, 4.1, 'GOOD'],
                ['Neha Tiwari', 'Marketing', 'NT', 89, 7, 3.9, 'NEEDS_SUPPORT'],
                ['Aditya Kapoor', 'QA', 'AK', 92, 9, 4.3, 'GOOD']
            ];

            $stmt = $this->db->prepare("INSERT INTO manager_reports_records (employee_name, department, avatar_initials, attendance_rate, tasks_completed, average_rating, status) VALUES (?, ?, ?, ?, ?, ?, ?)");
            foreach ($repRecords as $r) {
                $stmt->execute($r);
            }
        }
    }


    /**
     * Get Approvals List
     */
    public function getPendingApprovals(): array
    {
        $stmt = $this->db->query("SELECT id, employee_name, request_type, category, icon_type, request_date, status 
                                  FROM manager_approvals 
                                  WHERE status = 'PENDING' 
                                  ORDER BY request_date DESC, id DESC LIMIT 5");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get Team Members List
     */
    public function getTeamMembers(): array
    {
        $stmt = $this->db->query("SELECT id, name, role_title, department, avatar_initials, status, workload_current, workload_max, performance_rating, email 
                                  FROM manager_team_members 
                                  ORDER BY CASE name 
                                    WHEN 'Priya Sharma' THEN 1 
                                    WHEN 'Rohit Kulkarni' THEN 2 
                                    WHEN 'Sneha Iyer' THEN 3 
                                    WHEN 'Rohan Verma' THEN 4 
                                    WHEN 'Neha Tiwari' THEN 5 
                                    WHEN 'Aditya Kapoor' THEN 6 
                                    ELSE 7 END, id ASC LIMIT 6");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get All Team Members for Dropdown
     */
    public function getAllTeamMembers(): array
    {
        $stmt = $this->db->query("SELECT id, name, role_title, department, avatar_initials, status, workload_current, workload_max, performance_rating, email 
                                  FROM manager_team_members 
                                  ORDER BY CASE name 
                                    WHEN 'Priya Sharma' THEN 1 
                                    WHEN 'Rohit Kulkarni' THEN 2 
                                    WHEN 'Sneha Iyer' THEN 3 
                                    WHEN 'Rohan Verma' THEN 4 
                                    WHEN 'Neha Tiwari' THEN 5 
                                    WHEN 'Aditya Kapoor' THEN 6 
                                    ELSE 7 END, id ASC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get Recent Tasks List
     */
    public function getRecentTasks(): array
    {
        $stmt = $this->db->query("SELECT t.id, t.task_title, t.description, t.priority, t.status, t.progress_pct, t.start_date, t.due_date, t.assigned_scope,
                                         COALESCE(NULLIF(t.assigned_to_name, ''), m.name, (CASE WHEN t.assigned_scope = 'TEAM' THEN 'Entire Team' ELSE 'Team Member' END)) AS assigned_to_name, 
                                         COALESCE(m.avatar_initials, (CASE WHEN t.assigned_scope = 'TEAM' THEN '👥' ELSE 'TM' END)) AS avatar_initials, 
                                         COALESCE(m.id, 0) AS assigned_to_id
                                  FROM manager_tasks t
                                  LEFT JOIN manager_team_members m ON t.assigned_to = m.id
                                  ORDER BY t.id DESC LIMIT 15");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Assign a new task (to an individual or entire team) and generate notifications
     */
    public function assignTask(array $data): int
    {
        $isTeam = ($data['assigned_scope'] ?? '') === 'TEAM';
        $assignedToId = (int) ($data['assigned_to'] ?? 0);
        $assignedToName = $data['assigned_to_name'] ?? ($isTeam ? 'Entire Team' : 'Team Member');

        $stmt = $this->db->prepare("INSERT INTO manager_tasks 
            (manager_id, assigned_to, assigned_scope, assigned_to_name, employee_id, employee_email, task_title, description, priority, status, progress_pct, start_date, due_date)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'NOT_STARTED', 0, ?, ?)");
        $stmt->execute([
            $data['manager_id'] ?? null,
            $assignedToId,
            $isTeam ? 'TEAM' : 'INDIVIDUAL',
            $assignedToName,
            $data['employee_id'] ?? null,
            $data['employee_email'] ?? null,
            $data['task_title'],
            $data['description'] ?? '',
            $data['priority'] ?? 'MEDIUM',
            $data['start_date'] ?? date('Y-m-d'),
            $data['due_date'] ?? date('Y-m-d', strtotime('+3 days'))
        ]);

        $taskId = (int) $this->db->lastInsertId();

        // Increment employee workload if assigned to an individual team member
        if (!$isTeam && $assignedToId > 0) {
            try {
                $this->db->prepare("UPDATE manager_team_members SET workload_current = LEAST(workload_max, workload_current + 1) WHERE id = ?")
                         ->execute([$assignedToId]);
            } catch (\Throwable $_) {}
        }

        // Generate Real Notification in user_notifications table
        try {
            $notifScope = $isTeam ? 'TEAM' : 'INDIVIDUAL';
            $notifTitle = $isTeam 
                ? "New Team Task: " . $data['task_title'] 
                : "New Task Assigned: " . $data['task_title'];
            $dueStr = !empty($data['due_date']) ? date('d M Y', strtotime($data['due_date'])) : 'Soon';
            $notifMsg = $isTeam
                ? "Manager assigned a new task to the entire team: \"" . $data['task_title'] . "\". Due on {$dueStr}."
                : "Manager assigned you a new task: \"" . $data['task_title'] . "\". Due on {$dueStr}.";

            $notifStmt = $this->db->prepare("INSERT INTO user_notifications 
                (user_id, employee_id, target_scope, type, title, message, reference_id, priority, is_read, created_at)
                VALUES (?, ?, ?, 'TASK_ASSIGNED', ?, ?, ?, ?, 0, NOW())");
            $notifStmt->execute([
                $data['user_id'] ?? null,
                $data['employee_id'] ?? null,
                $notifScope,
                $notifTitle,
                $notifMsg,
                $taskId,
                $data['priority'] ?? 'MEDIUM'
            ]);
        } catch (\Throwable $notifErr) {
            error_log('Notification creation error: ' . $notifErr->getMessage());
        }

        return $taskId;
    }

    /**
     * Get Member by ID
     */
    public function getMemberById(int $memberId): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM manager_team_members WHERE id = ? LIMIT 1");
        $stmt->execute([$memberId]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    /**
     * Get Upcoming Trainings
     */
    public function getUpcomingTrainings(): array
    {
        $stmt = $this->db->query("SELECT id, title, target_audience, scheduled_date, status 
                                  FROM manager_trainings 
                                  ORDER BY scheduled_date ASC LIMIT 4");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get Competencies for a quarter
     */
    public function getCompetencies(string $quarter = 'Q3 2025'): array
    {
        $stmt = $this->db->prepare("SELECT competency_name, score, max_score, bar_color 
                                    FROM manager_team_competencies 
                                    WHERE quarter = ? 
                                    ORDER BY id ASC");
        $stmt->execute([$quarter]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Approve or reject a pending approval
     */
    public function updateApprovalStatus(int $approvalId, string $status, ?string $remarks = null): bool
    {
        $stmt = $this->db->prepare("UPDATE manager_approvals SET status = ?, remarks = ?, updated_at = NOW() WHERE id = ?");
        return $stmt->execute([$status, $remarks, $approvalId]);
    }

    /**
     * Update Task Status and Progress Percentage
     */
    public function updateTaskStatus(int $taskId, string $status, ?int $progressPct = null): bool
    {
        if ($progressPct === null) {
            $progressPct = ($status === 'COMPLETED') ? 100 : (($status === 'NOT_STARTED') ? 0 : 50);
        }
        $stmt = $this->db->prepare("UPDATE manager_tasks SET status = ?, progress_pct = ?, updated_at = NOW() WHERE id = ?");
        return $stmt->execute([$status, $progressPct, $taskId]);
    }

    /**
     * Get Task by ID with assigned employee info
     */
    public function getTaskById(int $taskId): ?array
    {
        $stmt = $this->db->prepare("SELECT t.*, m.name AS assigned_to_name, m.role_title, m.department, m.avatar_initials 
                                    FROM manager_tasks t 
                                    LEFT JOIN manager_team_members m ON t.assigned_to = m.id 
                                    WHERE t.id = ? LIMIT 1");
        $stmt->execute([$taskId]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    /**
     * Get Team Attendance Records for a date and optional department
     */
    public function getTeamAttendanceRecords(string $date = '2025-08-26', ?string $department = null): array
    {
        $sql = "SELECT id, date, employee_name, avatar_initials, department, check_in, check_out, work_hours, status 
                FROM manager_daily_attendance 
                WHERE date = ?";
        $params = [$date];

        if (!empty($department) && strtolower($department) !== 'all' && strtolower($department) !== 'all departments') {
            $sql .= " AND LOWER(department) = LOWER(?)";
            $params[] = $department;
        }

        $sql .= " ORDER BY CASE employee_name 
                    WHEN 'Priya Sharma' THEN 1 
                    WHEN 'Rohan Verma' THEN 2 
                    WHEN 'Sneha Iyer' THEN 3 
                    WHEN 'Aditya Kapoor' THEN 4 
                    WHEN 'Neha Tiwari' THEN 5 
                    WHEN 'Rohit Kulkarni' THEN 6 
                    ELSE 7 END, id ASC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Fallback if empty for requested date
        if (empty($results)) {
            $stmt = $this->db->query("SELECT id, date, employee_name, avatar_initials, department, check_in, check_out, work_hours, status 
                                      FROM manager_daily_attendance 
                                      ORDER BY id ASC LIMIT 6");
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        return $results;
    }

    /**
     * Get Pending Attendance Requests
     */
    public function getAttendanceRequests(): array
    {
        $stmt = $this->db->query("SELECT id, employee_name, avatar_initials, department, request_type, duration, request_date, status 
                                  FROM manager_attendance_requests 
                                  ORDER BY id ASC LIMIT 5");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get 7-Day Attendance Trends for Stacked Bar Chart
     */
    public function getAttendance7DayTrends(): array
    {
        // Matches exact screenshot bars: 20 Aug Wed to 26 Aug Tue
        return [
            ['day' => '20 Aug', 'weekday' => 'Wed', 'present' => 8, 'absent' => 0, 'leave' => 1, 'total' => 9],
            ['day' => '21 Aug', 'weekday' => 'Thu', 'present' => 7, 'absent' => 1, 'leave' => 1, 'total' => 9],
            ['day' => '22 Aug', 'weekday' => 'Fri', 'present' => 7, 'absent' => 1, 'leave' => 1, 'total' => 9],
            ['day' => '23 Aug', 'weekday' => 'Sat', 'present' => 7, 'absent' => 1, 'leave' => 1, 'total' => 9],
            ['day' => '23 Aug', 'weekday' => 'Sun', 'present' => 5, 'absent' => 1, 'leave' => 1, 'total' => 7],
            ['day' => '24 Aug', 'weekday' => 'Sun', 'present' => 5, 'absent' => 1, 'leave' => 1, 'total' => 7],
            ['day' => '25 Aug', 'weekday' => 'Mon', 'present' => 7, 'absent' => 1, 'leave' => 1, 'total' => 9],
            ['day' => '26 Aug', 'weekday' => 'Tue', 'present' => 9, 'absent' => 0, 'leave' => 1, 'total' => 10]
        ];
    }

    /**
     * Get Leave Summary Breakdown for Donut Chart
     */
    public function getLeaveSummary(string $period = 'This Month'): array
    {
        return [
            'total_on_leave' => 2,
            'categories' => [
                ['label' => 'Casual Leave', 'count' => 1, 'color' => '#3b82f6'],
                ['label' => 'Sick Leave', 'count' => 0, 'color' => '#ef4444'],
                ['label' => 'Earned Leave', 'count' => 1, 'color' => '#f59e0b'],
                ['label' => 'Other', 'count' => 0, 'color' => '#8b5cf6']
            ]
        ];
    }

    /**
     * Get 6-Month Performance Trend line data
     */
    public function getPerformanceTrends(string $timeframe = '6M'): array
    {
        $this->seedIfEmpty();
        $stmt = $this->db->query("SELECT month_idx, month_label, month_name, year, rating 
                                  FROM manager_performance_trends 
                                  ORDER BY month_idx ASC");
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (empty($results)) {
            return [
                ['month_idx' => 1, 'month_label' => 'Mar', 'month_name' => 'March', 'year' => 2025, 'rating' => 2.8],
                ['month_idx' => 2, 'month_label' => 'Apr', 'month_name' => 'April', 'year' => 2025, 'rating' => 3.1],
                ['month_idx' => 3, 'month_label' => 'May', 'month_name' => 'May', 'year' => 2025, 'rating' => 3.4],
                ['month_idx' => 4, 'month_label' => 'Jun', 'month_name' => 'June', 'year' => 2025, 'rating' => 3.7],
                ['month_idx' => 5, 'month_label' => 'Jul', 'month_name' => 'July', 'year' => 2025, 'rating' => 4.0],
                ['month_idx' => 6, 'month_label' => 'Aug', 'month_name' => 'August', 'year' => 2025, 'rating' => 4.2]
            ];
        }

        return $results;
    }

    /**
     * Get Performance Distribution breakdown for Donut Chart
     */
    public function getPerformanceDistribution(): array
    {
        $this->seedIfEmpty();
        $stmt = $this->db->query("SELECT category_name, score_range, count, color 
                                  FROM manager_performance_distribution 
                                  ORDER BY id ASC");
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (empty($results)) {
            $results = [
                ['category_name' => 'Excellent (4.5 – 5.0)', 'score_range' => '4.5 - 5.0', 'count' => 3, 'color' => '#14b8a6'],
                ['category_name' => 'Good (3.5 – 4.4)', 'score_range' => '3.5 - 4.4', 'count' => 5, 'color' => '#0284c7'],
                ['category_name' => 'Average (2.5 – 3.4)', 'score_range' => '2.5 - 3.4', 'count' => 3, 'color' => '#f59e0b'],
                ['category_name' => 'Needs Improvement (< 2.5)', 'score_range' => '< 2.5', 'count' => 1, 'color' => '#f43f5e']
            ];
        }

        $totalMembers = 0;
        foreach ($results as $r) {
            $totalMembers += (int) $r['count'];
        }

        return [
            'total_members' => $totalMembers ?: 12,
            'segments' => $results
        ];
    }

    /**
     * Get Top Performers List matching screenshot
     */
    public function getTopPerformers(): array
    {
        return [
            [
                'id' => 1,
                'name' => 'Priya Sharma',
                'role_title' => 'UI/UX Designer',
                'avatar_type' => 'photo',
                'avatar_initials' => 'PS',
                'rating' => '4.8',
                'trend' => '+0.4'
            ],
            [
                'id' => 2,
                'name' => 'Rohit Kulkarni',
                'role_title' => 'Frontend Developer',
                'avatar_type' => 'initials',
                'avatar_initials' => 'RK',
                'rating' => '4.6',
                'trend' => '+0.3'
            ],
            [
                'id' => 5,
                'name' => 'Neha Tiwari',
                'role_title' => 'Content Writer',
                'avatar_type' => 'initials',
                'avatar_initials' => 'NT',
                'rating' => '4.5',
                'trend' => '+0.2'
            ],
            [
                'id' => 6,
                'name' => 'Aditya Kapoor',
                'role_title' => 'QA Engineer',
                'avatar_type' => 'initials',
                'avatar_initials' => 'AK',
                'rating' => '4.4',
                'trend' => '+0.1'
            ],
            [
                'id' => 3,
                'name' => 'Sneha Iyer',
                'role_title' => 'HR Executive',
                'avatar_type' => 'photo',
                'avatar_initials' => 'SI',
                'rating' => '4.3',
                'trend' => '+0.2'
            ]
        ];
    }

    /**
     * Get Recent Reviews List matching screenshot
     */
    public function getRecentReviews(): array
    {
        $this->seedIfEmpty();
        $stmt = $this->db->query("SELECT id, employee_name, role_title, avatar_initials, review_period, rating, status, reviewer_name, feedback, review_date 
                                  FROM manager_performance_reviews 
                                  ORDER BY id ASC LIMIT 5");
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (empty($results)) {
            return [
                ['id' => 1, 'employee_name' => 'Priya Sharma', 'avatar_initials' => 'PS', 'review_period' => 'Jan – Jun 2025', 'rating' => '4.8', 'status' => 'Completed', 'status_class' => 'completed'],
                ['id' => 2, 'employee_name' => 'Rohit Kulkarni', 'avatar_initials' => 'RK', 'review_period' => 'Jan – Jun 2025', 'rating' => '4.2', 'status' => 'Completed', 'status_class' => 'completed'],
                ['id' => 3, 'employee_name' => 'Sneha Iyer', 'avatar_initials' => 'SI', 'review_period' => 'Jan – Jun 2025', 'rating' => '4.0', 'status' => 'In Review', 'status_class' => 'review'],
                ['id' => 4, 'employee_name' => 'Rohan Verma', 'avatar_initials' => 'RV', 'review_period' => 'Jan – Jun 2025', 'rating' => '3.8', 'status' => 'In Review', 'status_class' => 'review'],
                ['id' => 5, 'employee_name' => 'Neha Tiwari', 'avatar_initials' => 'NT', 'review_period' => 'Jan – Jun 2025', 'rating' => '4.1', 'status' => 'Completed', 'status_class' => 'completed']
            ];
        }

        return array_map(function($r) {
            $st = $r['status'] === 'COMPLETED' ? 'Completed' : 'In Review';
            $cls = $r['status'] === 'COMPLETED' ? 'completed' : 'review';
            return [
                'id' => (int) $r['id'],
                'employee_name' => $r['employee_name'],
                'avatar_initials' => $r['avatar_initials'],
                'role_title' => $r['role_title'],
                'review_period' => $r['review_period'],
                'rating' => (string) $r['rating'],
                'status' => $st,
                'status_class' => $cls,
                'reviewer_name' => $r['reviewer_name'] ?? 'Arun Kumar',
                'feedback' => $r['feedback'] ?? ''
            ];
        }, $results);
    }

    /**
     * Get Team Goals matching screenshot
     */
    public function getTeamGoals(): array
    {
        $this->seedIfEmpty();
        $stmt = $this->db->query("SELECT id, title, progress_pct, due_date, status, bar_color 
                                  FROM manager_team_goals 
                                  ORDER BY id ASC LIMIT 5");
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (empty($results)) {
            return [
                ['id' => 1, 'goal' => 'Improve client satisfaction', 'progress' => 80, 'due_date' => '30 Sep', 'status' => 'On Track', 'status_class' => 'on-track', 'bar_color' => '#14b8a6'],
                ['id' => 2, 'goal' => 'Complete product module', 'progress' => 60, 'due_date' => '15 Oct', 'status' => 'In Progress', 'status_class' => 'in-progress', 'bar_color' => '#3b82f6'],
                ['id' => 3, 'goal' => 'Reduce bug count', 'progress' => 40, 'due_date' => '30 Sep', 'status' => 'At Risk', 'status_class' => 'at-risk', 'bar_color' => '#f59e0b'],
                ['id' => 4, 'goal' => 'Enhance documentation', 'progress' => 100, 'due_date' => '10 Sep', 'status' => 'Completed', 'status_class' => 'completed', 'bar_color' => '#14b8a6'],
                ['id' => 5, 'goal' => 'Team skill development', 'progress' => 50, 'due_date' => '25 Oct', 'status' => 'In Progress', 'status_class' => 'in-progress', 'bar_color' => '#3b82f6']
            ];
        }

        $statusLabels = [
            'ON_TRACK' => ['On Track', 'on-track'],
            'IN_PROGRESS' => ['In Progress', 'in-progress'],
            'AT_RISK' => ['At Risk', 'at-risk'],
            'COMPLETED' => ['Completed', 'completed']
        ];

        return array_map(function($g) use ($statusLabels) {
            $info = $statusLabels[$g['status']] ?? ['In Progress', 'in-progress'];
            return [
                'id' => (int) $g['id'],
                'goal' => $g['title'],
                'progress' => (int) $g['progress_pct'],
                'due_date' => $g['due_date'],
                'status' => $info[0],
                'status_class' => $info[1],
                'bar_color' => $g['bar_color'] ?? '#3b82f6'
            ];
        }, $results);
    }

    /**
     * Create a New Performance Review
     */
    public function createPerformanceReview(array $data, ?int $managerId = null): array
    {
        $this->ensureSchema();
        $stmt = $this->db->prepare("INSERT INTO manager_performance_reviews 
            (manager_id, employee_name, role_title, avatar_initials, review_period, rating, status, reviewer_name, feedback, review_date) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())");

        $empName = trim($data['employee_name'] ?? 'Team Member');
        $initials = strtoupper(substr($empName, 0, 1) . substr(strstr($empName, ' ') ?: ' ', 1, 1));
        if (strlen($initials) < 2) $initials = 'TM';

        $stmt->execute([
            $managerId,
            $empName,
            $data['role_title'] ?? 'Associate',
            $initials,
            $data['review_period'] ?? 'Q3 2025',
            (float) ($data['rating'] ?? 4.0),
            $data['status'] ?? 'IN_REVIEW',
            $data['reviewer_name'] ?? 'Arun Kumar',
            $data['feedback'] ?? null
        ]);

        return [
            'id' => (int) $this->db->lastInsertId(),
            'employee_name' => $empName,
            'avatar_initials' => $initials,
            'review_period' => $data['review_period'] ?? 'Q3 2025',
            'rating' => (string) ($data['rating'] ?? 4.0),
            'status' => $data['status'] === 'COMPLETED' ? 'Completed' : 'In Review',
            'status_class' => $data['status'] === 'COMPLETED' ? 'completed' : 'review'
        ];
    }

    /**
     * Create a New Team Goal
     */
    public function createTeamGoal(array $data, ?int $managerId = null): array
    {
        $this->ensureSchema();
        $stmt = $this->db->prepare("INSERT INTO manager_team_goals 
            (manager_id, title, progress_pct, due_date, status, bar_color) 
            VALUES (?, ?, ?, ?, ?, ?)");

        $title = trim($data['title'] ?? 'New Team Objective');
        $progress = (int) ($data['progress_pct'] ?? 0);
        $dueDate = trim($data['due_date'] ?? '30 Nov');
        $status = $data['status'] ?? 'IN_PROGRESS';
        $barColor = $progress >= 80 ? '#14b8a6' : ($progress <= 40 ? '#f59e0b' : '#3b82f6');

        $stmt->execute([
            $managerId,
            $title,
            $progress,
            $dueDate,
            $status,
            $barColor
        ]);

        $statusLabels = [
            'ON_TRACK' => ['On Track', 'on-track'],
            'IN_PROGRESS' => ['In Progress', 'in-progress'],
            'AT_RISK' => ['At Risk', 'at-risk'],
            'COMPLETED' => ['Completed', 'completed']
        ];
        $info = $statusLabels[$status] ?? ['In Progress', 'in-progress'];

        return [
            'id' => (int) $this->db->lastInsertId(),
            'goal' => $title,
            'progress' => $progress,
            'due_date' => $dueDate,
            'status' => $info[0],
            'status_class' => $info[1],
            'bar_color' => $barColor
        ];
    }

    /**
     * Get 6-Month Attendance Trends (Present, On Leave, Absent % for Wave Chart)
     */
    public function getMonthlyAttendanceTrends(): array
    {
        return [
            ['month' => 'Mar', 'present' => 76, 'leave' => 12, 'absent' => 12],
            ['month' => 'Apr', 'present' => 80, 'leave' => 10, 'absent' => 10],
            ['month' => 'May', 'present' => 82, 'leave' => 10, 'absent' => 8],
            ['month' => 'Jun', 'present' => 81, 'leave' => 11, 'absent' => 8],
            ['month' => 'Jul', 'present' => 85, 'leave' => 9, 'absent' => 6],
            ['month' => 'Aug', 'present' => 90, 'leave' => 6, 'absent' => 4]
        ];
    }

    /**
     * Get Task Status Distribution for Donut Chart
     */
    public function getTaskStatusDistribution(): array
    {
        return [
            'total_tasks' => 48,
            'segments' => [
                ['label' => 'Completed', 'count' => 24, 'percentage' => '50%', 'color' => '#10b981'],
                ['label' => 'In Progress', 'count' => 14, 'percentage' => '29%', 'color' => '#3b82f6'],
                ['label' => 'Not Started', 'count' => 6, 'percentage' => '13%', 'color' => '#f59e0b'],
                ['label' => 'Overdue', 'count' => 4, 'percentage' => '8%', 'color' => '#ef4444']
            ]
        ];
    }

    /**
     * Get Performance Rating Tier Distribution
     */
    public function getPerformanceRatingTiers(): array
    {
        return [
            'total_reviewed' => 12,
            'average_rating' => 4.2,
            'tiers' => [
                ['stars' => 5, 'label' => '5 Stars', 'count' => 3, 'percentage' => 25, 'color' => '#8b5cf6'],
                ['stars' => 4, 'label' => '4 Stars', 'count' => 5, 'percentage' => 42, 'color' => '#3b82f6'],
                ['stars' => 3, 'label' => '3 Stars', 'count' => 3, 'percentage' => 25, 'color' => '#06b6d4'],
                ['stars' => 2, 'label' => '2 Stars', 'count' => 1, 'percentage' => 8, 'color' => '#f59e0b'],
                ['stars' => 1, 'label' => '1 Star', 'count' => 0, 'percentage' => 0, 'color' => '#ef4444']
            ]
        ];
    }

    /**
     * Get Team Performance Reports Table
     */
    public function getTeamPerformanceReports(?string $department = null, ?string $search = null): array
    {
        $this->seedIfEmpty();

        $sql = "SELECT id, employee_name, department, avatar_initials, attendance_rate, tasks_completed, average_rating, status 
                FROM manager_reports_records WHERE 1=1";
        $params = [];

        if ($department && $department !== 'All Departments') {
            $sql .= " AND department = ?";
            $params[] = $department;
        }

        if ($search) {
            $sql .= " AND (employee_name LIKE ? OR department LIKE ?)";
            $params[] = "%{$search}%";
            $params[] = "%{$search}%";
        }

        $sql .= " ORDER BY id ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $records = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (empty($records) && empty($department) && empty($search)) {
            return [
                ['row_num' => 1, 'id' => 1, 'employee_name' => 'Priya Sharma', 'avatar_initials' => 'PS', 'department' => 'Design', 'attendance_rate' => '96%', 'tasks_completed' => 12, 'average_rating' => '4.8', 'status' => 'Excellent', 'status_class' => 'excellent'],
                ['row_num' => 2, 'id' => 2, 'employee_name' => 'Rohit Kulkarni', 'avatar_initials' => 'RK', 'department' => 'Development', 'attendance_rate' => '90%', 'tasks_completed' => 10, 'average_rating' => '4.6', 'status' => 'Good', 'status_class' => 'good'],
                ['row_num' => 3, 'id' => 3, 'employee_name' => 'Sneha Iyer', 'avatar_initials' => 'SI', 'department' => 'HR', 'attendance_rate' => '88%', 'tasks_completed' => 8, 'average_rating' => '4.2', 'status' => 'Good', 'status_class' => 'good'],
                ['row_num' => 4, 'id' => 4, 'employee_name' => 'Rohan Verma', 'avatar_initials' => 'RV', 'department' => 'Development', 'attendance_rate' => '95%', 'tasks_completed' => 11, 'average_rating' => '4.1', 'status' => 'Good', 'status_class' => 'good'],
                ['row_num' => 5, 'id' => 5, 'employee_name' => 'Neha Tiwari', 'avatar_initials' => 'NT', 'department' => 'Marketing', 'attendance_rate' => '89%', 'tasks_completed' => 7, 'average_rating' => '3.9', 'status' => 'Needs Support', 'status_class' => 'support'],
                ['row_num' => 6, 'id' => 6, 'employee_name' => 'Aditya Kapoor', 'avatar_initials' => 'AK', 'department' => 'QA', 'attendance_rate' => '92%', 'tasks_completed' => 9, 'average_rating' => '4.3', 'status' => 'Good', 'status_class' => 'good']
            ];
        }

        $formatted = [];
        foreach ($records as $idx => $r) {
            $st = $r['status'] === 'EXCELLENT' ? 'Excellent' : ($r['status'] === 'GOOD' ? 'Good' : 'Needs Support');
            $cls = $r['status'] === 'EXCELLENT' ? 'excellent' : ($r['status'] === 'GOOD' ? 'good' : 'support');

            $formatted[] = [
                'row_num' => $idx + 1,
                'id' => (int) $r['id'],
                'employee_name' => $r['employee_name'],
                'avatar_initials' => $r['avatar_initials'],
                'department' => $r['department'],
                'attendance_rate' => $r['attendance_rate'] . '%',
                'tasks_completed' => (int) $r['tasks_completed'],
                'average_rating' => (string) $r['average_rating'],
                'status' => $st,
                'status_class' => $cls
            ];
        }

        return $formatted;
    }
}



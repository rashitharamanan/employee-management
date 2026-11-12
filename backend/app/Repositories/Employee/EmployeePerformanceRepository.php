<?php

namespace App\Repositories\Employee;

use PDO;

class EmployeePerformanceRepository
{
    public function __construct(private PDO $db)
    {
    }

    /**
     * Ensure required tables and schemas exist
     */
    public function ensureSchema(): void
    {
        $this->db->exec("CREATE TABLE IF NOT EXISTS performance_reviews (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            employee_id INT UNSIGNED NOT NULL,
            reviewer_id INT UNSIGNED NULL,
            reviewer_name VARCHAR(150) NULL,
            reviewer_role VARCHAR(150) NULL,
            review_period_name VARCHAR(100) NULL,
            review_period_start DATE NOT NULL,
            review_period_end DATE NOT NULL,
            review_date DATE NULL,
            overall_rating DECIMAL(3,2) NULL,
            overall_comments TEXT NULL,
            employee_comments TEXT NULL,
            status ENUM('DRAFT','SUBMITTED','APPROVED','COMPLETED') NOT NULL DEFAULT 'DRAFT',
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            KEY idx_review_employee (employee_id),
            KEY idx_review_status (status)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

        // Check if reviewer_name column exists, if not add it
        try {
            $cols = $this->db->query("SHOW COLUMNS FROM performance_reviews LIKE 'reviewer_name'")->fetchAll();
            if (empty($cols)) {
                $this->db->exec("ALTER TABLE performance_reviews ADD COLUMN reviewer_name VARCHAR(150) NULL AFTER reviewer_id");
            }
            $cols2 = $this->db->query("SHOW COLUMNS FROM performance_reviews LIKE 'reviewer_role'")->fetchAll();
            if (empty($cols2)) {
                $this->db->exec("ALTER TABLE performance_reviews ADD COLUMN reviewer_role VARCHAR(150) NULL AFTER reviewer_name");
            }
            $cols3 = $this->db->query("SHOW COLUMNS FROM performance_reviews LIKE 'review_period_name'")->fetchAll();
            if (empty($cols3)) {
                $this->db->exec("ALTER TABLE performance_reviews ADD COLUMN review_period_name VARCHAR(100) NULL AFTER reviewer_role");
            }
        } catch (\Throwable $_) {}

        $this->db->exec("CREATE TABLE IF NOT EXISTS performance_goals (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            employee_id INT UNSIGNED NOT NULL,
            review_id INT UNSIGNED NULL,
            title VARCHAR(255) NOT NULL,
            description TEXT NULL,
            start_date DATE NULL,
            target_date DATE NULL,
            progress TINYINT UNSIGNED NOT NULL DEFAULT 0,
            status ENUM('ACTIVE','COMPLETED','ARCHIVED') NOT NULL DEFAULT 'ACTIVE',
            display_status VARCHAR(50) NOT NULL DEFAULT 'In Progress',
            priority ENUM('LOW','MEDIUM','HIGH') NOT NULL DEFAULT 'MEDIUM',
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            KEY idx_goal_employee (employee_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

        try {
            $cols = $this->db->query("SHOW COLUMNS FROM performance_goals LIKE 'start_date'")->fetchAll();
            if (empty($cols)) {
                $this->db->exec("ALTER TABLE performance_goals ADD COLUMN start_date DATE NULL AFTER description");
            }
            $cols2 = $this->db->query("SHOW COLUMNS FROM performance_goals LIKE 'display_status'")->fetchAll();
            if (empty($cols2)) {
                $this->db->exec("ALTER TABLE performance_goals ADD COLUMN display_status VARCHAR(50) NOT NULL DEFAULT 'In Progress' AFTER status");
            }
        } catch (\Throwable $_) {}

        $this->db->exec("CREATE TABLE IF NOT EXISTS performance_ratings (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            review_id INT UNSIGNED NOT NULL,
            criteria VARCHAR(150) NOT NULL,
            rating DECIMAL(3,2) NOT NULL,
            weight DECIMAL(3,2) NOT NULL DEFAULT 1.00,
            comments TEXT NULL,
            color VARCHAR(30) NULL DEFAULT 'purple',
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            KEY idx_rating_review (review_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

        try {
            $cols = $this->db->query("SHOW COLUMNS FROM performance_ratings LIKE 'color'")->fetchAll();
            if (empty($cols)) {
                $this->db->exec("ALTER TABLE performance_ratings ADD COLUMN color VARCHAR(30) NULL DEFAULT 'purple' AFTER comments");
            }
            // Allow decimal ratings
            $this->db->exec("ALTER TABLE performance_ratings MODIFY COLUMN rating DECIMAL(3,2) NOT NULL");
        } catch (\Throwable $_) {}

        $this->db->exec("CREATE TABLE IF NOT EXISTS training_programs (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            title VARCHAR(255) NOT NULL,
            description TEXT NULL,
            provider VARCHAR(150) NOT NULL DEFAULT 'Mindazzle Academy',
            category VARCHAR(100) NULL,
            trainer VARCHAR(150) NULL,
            duration VARCHAR(50) NULL DEFAULT '2h 30m',
            rating DECIMAL(2,1) NOT NULL DEFAULT 4.5,
            icon_type VARCHAR(50) NOT NULL DEFAULT 'default',
            delivery_mode ENUM('ONLINE','IN_PERSON','HYBRID') NOT NULL DEFAULT 'ONLINE',
            status ENUM('DRAFT','ACTIVE','COMPLETED','CANCELLED') NOT NULL DEFAULT 'ACTIVE',
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

        try {
            $cols = $this->db->query("SHOW COLUMNS FROM training_programs LIKE 'provider'")->fetchAll();
            if (empty($cols)) {
                $this->db->exec("ALTER TABLE training_programs ADD COLUMN provider VARCHAR(150) NOT NULL DEFAULT 'Mindazzle Academy' AFTER description");
            }
            $cols2 = $this->db->query("SHOW COLUMNS FROM training_programs LIKE 'rating'")->fetchAll();
            if (empty($cols2)) {
                $this->db->exec("ALTER TABLE training_programs ADD COLUMN rating DECIMAL(2,1) NOT NULL DEFAULT 4.5 AFTER trainer");
            }
            $cols3 = $this->db->query("SHOW COLUMNS FROM training_programs LIKE 'icon_type'")->fetchAll();
            if (empty($cols3)) {
                $this->db->exec("ALTER TABLE training_programs ADD COLUMN icon_type VARCHAR(50) NOT NULL DEFAULT 'default' AFTER rating");
            }
            $cols4 = $this->db->query("SHOW COLUMNS FROM training_programs LIKE 'duration'")->fetchAll();
            if (empty($cols4)) {
                $this->db->exec("ALTER TABLE training_programs ADD COLUMN duration VARCHAR(50) NULL DEFAULT '2h 30m' AFTER trainer");
            }
        } catch (\Throwable $_) {}

        $this->db->exec("CREATE TABLE IF NOT EXISTS training_enrollments (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            program_id INT UNSIGNED NOT NULL,
            employee_id INT UNSIGNED NOT NULL,
            enrollment_date DATE NOT NULL,
            status ENUM('ENROLLED','IN_PROGRESS','COMPLETED','DROPPED') NOT NULL DEFAULT 'ENROLLED',
            progress TINYINT UNSIGNED NOT NULL DEFAULT 0,
            completion_date DATE NULL,
            score DECIMAL(5,2) NULL,
            certificate_issued TINYINT(1) NOT NULL DEFAULT 0,
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            UNIQUE KEY uq_enrollment (program_id, employee_id),
            KEY idx_enroll_employee (employee_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

        try {
            $cols = $this->db->query("SHOW COLUMNS FROM training_enrollments LIKE 'progress'")->fetchAll();
            if (empty($cols)) {
                $this->db->exec("ALTER TABLE training_enrollments ADD COLUMN progress TINYINT UNSIGNED NOT NULL DEFAULT 0 AFTER status");
            }
        } catch (\Throwable $_) {}

        $this->db->exec("CREATE TABLE IF NOT EXISTS training_certificates (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            enrollment_id INT UNSIGNED NOT NULL,
            employee_id INT UNSIGNED NOT NULL,
            course_title VARCHAR(255) NOT NULL,
            certificate_number VARCHAR(100) NOT NULL,
            issued_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            file_path VARCHAR(255) NULL,
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            UNIQUE KEY uq_cert_number (certificate_number),
            KEY idx_cert_emp (employee_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

        try {
            $cols = $this->db->query("SHOW COLUMNS FROM training_certificates LIKE 'employee_id'")->fetchAll();
            if (empty($cols)) {
                $this->db->exec("ALTER TABLE training_certificates ADD COLUMN employee_id INT UNSIGNED NOT NULL AFTER enrollment_id");
            }
            $cols2 = $this->db->query("SHOW COLUMNS FROM training_certificates LIKE 'course_title'")->fetchAll();
            if (empty($cols2)) {
                $this->db->exec("ALTER TABLE training_certificates ADD COLUMN course_title VARCHAR(255) NOT NULL AFTER employee_id");
            }
        } catch (\Throwable $_) {}
    }

    /**
     * Seeds initial demo data matching screenshot if employee has no records yet
     */
    public function seedIfEmpty(int $employeeId): void
    {
        $this->ensureSchema();

        // 1. Seed Programs if empty
        $progCount = (int) $this->db->query("SELECT COUNT(*) FROM training_programs")->fetchColumn();
        if ($progCount < 4) {
            $programs = [
                ['Advanced Figma', 'Master advanced Auto Layout, design tokens, interactive components and prototyping.', 'Mindazzle Academy', 'Design', '2h 30m', 4.8, 'figma'],
                ['UI/UX Best Practices', 'Comprehensive guide to user research, wireframing, and accessibility standards.', 'Coursera', 'Design', '5h 00m', 4.7, 'uiux'],
                ['Agile Fundamentals', 'Learn Scrum, Kanban, sprints, and agile product development frameworks.', 'LinkedIn Learning', 'Management', '3h 45m', 4.6, 'agile'],
                ['Design Leadership', 'Developing executive presence, leading cross-functional teams, and design critique.', 'Udemy', 'Leadership', '4h 15m', 4.5, 'leadership'],
                ['Product Management', 'Product lifecycle, roadmapping, backlog prioritization, and metrics.', 'Mindazzle Academy', 'Management', '6h 00m', 4.6, 'pm'],
                ['Effective Communication', 'Interpersonal communications, presentation mastery, and storytelling.', 'Coursera', 'Soft Skills', '3h 15m', 4.7, 'communication'],
                ['Project Management', 'Planning, execution, risk management, and stakeholder communication.', 'Udemy', 'Management', '4h 20m', 4.6, 'project'],
                ['Design Thinking', 'Empathize, define, ideate, prototype, and test solution methodologies.', 'LinkedIn Learning', 'Innovation', '2h 10m', 4.5, 'thinking'],
            ];

            $pStmt = $this->db->prepare("INSERT INTO training_programs (title, description, provider, category, duration, rating, icon_type, status) VALUES (?, ?, ?, ?, ?, ?, ?, 'ACTIVE')");
            foreach ($programs as $prog) {
                $pStmt->execute($prog);
            }
        }

        // 2. Check reviews for employee
        $revCount = (int) $this->db->query("SELECT COUNT(*) FROM performance_reviews WHERE employee_id = {$employeeId}")->fetchColumn();
        if ($revCount === 0) {
            // Seed Reviews matching screenshot:
            // Jul 2026 (4.3), Jan 2026 (4.0), Jul 2025 (3.8), Jan 2025 (3.6)
            $reviews = [
                [
                    'period'   => 'Jul 2026',
                    'start'    => '2026-01-01',
                    'end'      => '2026-06-30',
                    'date'     => '2026-07-15',
                    'rating'   => 4.30,
                    'reviewer' => 'Rahul Mehta',
                    'role'     => 'Engineering Manager',
                    'comments' => 'Priya delivered exceptional work on the Client Portal redesign. Her prototyping speed, stakeholder alignment, and design system governance have elevated team velocity significantly.',
                    'ratings'  => [
                        ['Job Knowledge', 4.5, 'Deep expertise in Figma design tokens, component architecture, and design tokens hierarchy.', 'purple'],
                        ['Quality of Work', 4.2, 'Exceptional attention to pixel fidelity, user states, and micro-interactions.', 'blue'],
                        ['Communication', 4.3, 'Clear design rationale presentations and proactive cross-team collaboration.', 'cyan'],
                        ['Teamwork', 4.4, 'Highly supportive of junior designers and engineers during handoffs.', 'orange'],
                        ['Initiative', 4.0, 'Proactively initiated the component library overhaul and accessibility audit.', 'pink']
                    ]
                ],
                [
                    'period'   => 'Jan 2026',
                    'start'    => '2025-07-01',
                    'end'      => '2025-12-31',
                    'date'     => '2026-01-18',
                    'rating'   => 4.00,
                    'reviewer' => 'Rahul Mehta',
                    'role'     => 'Engineering Manager',
                    'comments' => 'Strong performance during Q4 delivery cycles. Demonstrates high ownership and high aesthetic quality.',
                    'ratings'  => [
                        ['Job Knowledge', 4.1, 'Solid understanding of design standards and workflows.', 'purple'],
                        ['Quality of Work', 4.0, 'Consistently delivers polished designs with few revision rounds.', 'blue'],
                        ['Communication', 4.0, 'Articulates design decisions clearly with stakeholders.', 'cyan'],
                        ['Teamwork', 4.1, 'Cooperative and reliable team player.', 'orange'],
                        ['Initiative', 3.8, 'Takes on challenging tasks willingly.', 'pink']
                    ]
                ],
                [
                    'period'   => 'Jul 2025',
                    'start'    => '2025-01-01',
                    'end'      => '2025-06-30',
                    'date'     => '2025-07-12',
                    'rating'   => 3.80,
                    'reviewer' => 'Sneha Iyer',
                    'role'     => 'HR Manager',
                    'comments' => 'Demonstrated substantial growth in product thinking and teamwork. Great cultural fit.',
                    'ratings'  => [
                        ['Job Knowledge', 3.8, 'Growing grasp of company product suite.', 'purple'],
                        ['Quality of Work', 3.8, 'Good visual quality and timely deliveries.', 'blue'],
                        ['Communication', 3.9, 'Open to feedback and communicative.', 'cyan'],
                        ['Teamwork', 3.9, 'Collaborates enthusiastically.', 'orange'],
                        ['Initiative', 3.6, 'Growing independence in day-to-day execution.', 'pink']
                    ]
                ],
                [
                    'period'   => 'Jan 2025',
                    'start'    => '2024-07-01',
                    'end'      => '2024-12-31',
                    'date'     => '2025-01-15',
                    'rating'   => 3.60,
                    'reviewer' => 'Sneha Iyer',
                    'role'     => 'HR Manager',
                    'comments' => 'Successful onboarding review. Priya has quickly integrated into our product development cycle.',
                    'ratings'  => [
                        ['Job Knowledge', 3.5, 'Quick learner with modern UI tooling.', 'purple'],
                        ['Quality of Work', 3.6, 'Dependable execution on design tickets.', 'blue'],
                        ['Communication', 3.7, 'Asks great clarifying questions.', 'cyan'],
                        ['Teamwork', 3.8, 'Positive and receptive team member.', 'orange'],
                        ['Initiative', 3.4, 'Following established patterns effectively.', 'pink']
                    ]
                ]
            ];

            $rStmt = $this->db->prepare("INSERT INTO performance_reviews (employee_id, reviewer_name, reviewer_role, review_period_name, review_period_start, review_period_end, review_date, overall_rating, overall_comments, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'COMPLETED')");
            $cStmt = $this->db->prepare("INSERT INTO performance_ratings (review_id, criteria, rating, weight, comments, color) VALUES (?, ?, ?, 1.0, ?, ?)");

            foreach ($reviews as $rev) {
                $rStmt->execute([
                    $employeeId,
                    $rev['reviewer'],
                    $rev['role'],
                    $rev['period'],
                    $rev['start'],
                    $rev['end'],
                    $rev['date'],
                    $rev['rating'],
                    $rev['comments']
                ]);
                $reviewId = (int) $this->db->lastInsertId();

                foreach ($rev['ratings'] as $item) {
                    $cStmt->execute([
                        $reviewId,
                        $item[0],
                        $item[1],
                        $item[2],
                        $item[3]
                    ]);
                }
            }
        }

        // 3. Check goals for employee
        $goalCount = (int) $this->db->query("SELECT COUNT(*) FROM performance_goals WHERE employee_id = {$employeeId}")->fetchColumn();
        if ($goalCount === 0) {
            $goals = [
                ['UI Redesign for Client Portal', 'Complete responsive wireframes, user testing, and high-fidelity prototype handoff for the Q3 client portal release.', '2026-07-01', '2026-09-30', 85, 'ACTIVE', 'On Track', 'HIGH'],
                ['Improve Design System', 'Standardize typography scale, color tokens, and rebuild core buttons and inputs in Figma and Storybook.', '2026-07-01', '2026-10-30', 60, 'ACTIVE', 'In Progress', 'MEDIUM'],
                ['Complete Advanced Figma Course', 'Finish certification modules on variable modes, nested components, and automated code handoff.', '2026-08-15', '2026-09-15', 100, 'COMPLETED', 'Completed', 'HIGH'],
                ['Support 2 Product Releases', 'Provide QA design verification, bug scrubs, and release asset packs for Mobile App v2 and Web Portal v4.', '2026-09-01', '2026-12-31', 25, 'ACTIVE', 'At Risk', 'MEDIUM']
            ];

            $gStmt = $this->db->prepare("INSERT INTO performance_goals (employee_id, title, description, start_date, target_date, progress, status, display_status, priority) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
            foreach ($goals as $g) {
                $gStmt->execute([
                    $employeeId,
                    $g[0],
                    $g[1],
                    $g[2],
                    $g[3],
                    $g[4],
                    $g[5],
                    $g[6],
                    $g[7]
                ]);
            }
        }

        // 4. Check enrollments for employee
        $enrCount = (int) $this->db->query("SELECT COUNT(*) FROM training_enrollments WHERE employee_id = {$employeeId}")->fetchColumn();
        if ($enrCount === 0) {
            // Find IDs of seeded programs
            $programs = $this->db->query("SELECT id, title FROM training_programs")->fetchAll(PDO::FETCH_KEY_PAIR);

            $enrollmentMap = [
                'Advanced Figma'        => [80, 'IN_PROGRESS', 0],
                'UI/UX Best Practices'  => [60, 'IN_PROGRESS', 0],
                'Agile Fundamentals'    => [100, 'COMPLETED', 1],
                'Design Leadership'     => [0, 'ENROLLED', 0],
                'Product Management'    => [40, 'IN_PROGRESS', 0]
            ];

            $eStmt = $this->db->prepare("INSERT INTO training_enrollments (program_id, employee_id, enrollment_date, status, progress, certificate_issued) VALUES (?, ?, '2026-07-10', ?, ?, ?)");
            $certStmt = $this->db->prepare("INSERT INTO training_certificates (enrollment_id, employee_id, course_title, certificate_number, issued_at) VALUES (?, ?, ?, ?, ?)");

            foreach ($programs as $progId => $progTitle) {
                if (isset($enrollmentMap[$progTitle])) {
                    [$progVal, $statusVal, $certVal] = $enrollmentMap[$progTitle];
                    $eStmt->execute([$progId, $employeeId, $statusVal, $progVal, $certVal]);
                    $enrollId = (int) $this->db->lastInsertId();

                    if ($certVal === 1) {
                        $certStmt->execute([
                            $enrollId,
                            $employeeId,
                            $progTitle,
                            'CERT-2026-AF-998',
                            '2026-08-20 14:30:00'
                        ]);
                    }
                }
            }

            // Seed second certificate (Human-Centered Design)
            $certStmt->execute([
                999,
                $employeeId,
                'Human-Centered Design Fundamentals',
                'CERT-2026-HCD-108',
                '2026-05-14 11:15:00'
            ]);
        }
    }

    /**
     * Get employee details
     */
    public function getEmployee(int $employeeId): ?array
    {
        $stmt = $this->db->prepare("SELECT id, employee_code, full_name, email, department, designation, profile_photo FROM employees WHERE id = :id LIMIT 1");
        $stmt->execute([':id' => $employeeId]);
        $emp = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($emp) {
            $nameParts = explode(' ', trim($emp['full_name'] ?? ''));
            $emp['first_name'] = $nameParts[0] ?? '';
            $emp['last_name']  = isset($nameParts[1]) ? implode(' ', array_slice($nameParts, 1)) : '';
            return $emp;
        }

        return [
            'id'            => $employeeId,
            'employee_code' => 'EMP1024',
            'full_name'     => 'Priya Nair',
            'first_name'    => 'Priya',
            'last_name'     => 'Nair',
            'email'         => 'priya.nair@mindazzle.com',
            'department'    => 'Design & Product',
            'designation'   => 'UI/UX Designer',
            'profile_photo' => null
        ];
    }

    /**
     * Get all performance reviews for employee
     */
    public function getReviews(int $employeeId): array
    {
        $stmt = $this->db->prepare("
            SELECT pr.*, 
                   COUNT(prat.id) as criteria_count
            FROM performance_reviews pr
            LEFT JOIN performance_ratings prat ON prat.review_id = pr.id
            WHERE pr.employee_id = :employee_id
            GROUP BY pr.id
            ORDER BY pr.review_period_end DESC
        ");
        $stmt->execute([':employee_id' => $employeeId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get rating criteria breakdown for a review
     */
    public function getReviewRatings(int $reviewId): array
    {
        $stmt = $this->db->prepare("
            SELECT criteria, rating, comments, color
            FROM performance_ratings
            WHERE review_id = :review_id
            ORDER BY id ASC
        ");
        $stmt->execute([':review_id' => $reviewId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get single review with ratings
     */
    public function getReviewWithRatings(int $reviewId): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM performance_reviews WHERE id = :id LIMIT 1");
        $stmt->execute([':id' => $reviewId]);
        $review = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$review) return null;

        $review['ratings'] = $this->getReviewRatings($reviewId);
        return $review;
    }

    /**
     * Get goals for employee
     */
    public function getGoals(int $employeeId): array
    {
        $stmt = $this->db->prepare("
            SELECT id, title, description, start_date, target_date, progress, status, display_status, priority
            FROM performance_goals
            WHERE employee_id = :employee_id
            ORDER BY id ASC
        ");
        $stmt->execute([':employee_id' => $employeeId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get training progress / enrollments
     */
    public function getTrainingProgress(int $employeeId): array
    {
        $stmt = $this->db->prepare("
            SELECT te.id as enrollment_id,
                   te.program_id,
                   tp.title as course_name,
                   COALESCE(tp.provider, 'Mindazzle Academy') as provider,
                   te.progress,
                   te.status,
                   te.certificate_issued,
                   tc.id as certificate_id,
                   tc.certificate_number
            FROM training_enrollments te
            JOIN training_programs tp ON tp.id = te.program_id
            LEFT JOIN training_certificates tc ON tc.enrollment_id = te.id
            WHERE te.employee_id = :employee_id
            ORDER BY te.id ASC
        ");
        $stmt->execute([':employee_id' => $employeeId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get recommended training programs
     */
    public function getRecommendedTraining(int $employeeId): array
    {
        $stmt = $this->db->prepare("
            SELECT tp.id, tp.title, tp.description, tp.provider, tp.category, tp.duration, tp.rating, tp.icon_type,
                   (CASE WHEN te.id IS NOT NULL THEN 1 ELSE 0 END) as is_enrolled
            FROM training_programs tp
            LEFT JOIN training_enrollments te ON te.program_id = tp.id AND te.employee_id = :employee_id
            WHERE tp.title IN ('Advanced Figma', 'Effective Communication', 'Project Management', 'Design Thinking')
            ORDER BY tp.id ASC
        ");
        $stmt->execute([':employee_id' => $employeeId]);
        $list = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Fallback if not found
        if (empty($list)) {
            $stmt2 = $this->db->prepare("
                SELECT tp.id, tp.title, tp.description, tp.provider, tp.category, tp.duration, tp.rating, tp.icon_type,
                       (CASE WHEN te.id IS NOT NULL THEN 1 ELSE 0 END) as is_enrolled
                FROM training_programs tp
                LEFT JOIN training_enrollments te ON te.program_id = tp.id AND te.employee_id = :employee_id
                LIMIT 4
            ");
            $stmt2->execute([':employee_id' => $employeeId]);
            $list = $stmt2->fetchAll(PDO::FETCH_ASSOC);
        }

        return $list;
    }

    /**
     * Count certificates earned by employee this year
     */
    public function getCertificatesCount(int $employeeId, string $year = null): int
    {
        $year = $year ?? date('Y');
        $stmt = $this->db->prepare("
            SELECT COUNT(*) FROM training_certificates
            WHERE employee_id = :employee_id
              AND YEAR(issued_at) = :year
        ");
        $stmt->execute([
            ':employee_id' => $employeeId,
            ':year'        => $year
        ]);
        return (int) $stmt->fetchColumn();
    }

    /**
     * Get certificate details
     */
    public function getCertificate(int $employeeId, int $certificateId): ?array
    {
        $stmt = $this->db->prepare("
            SELECT tc.*, e.full_name as employee_name, e.designation, e.department
            FROM training_certificates tc
            JOIN employees e ON e.id = tc.employee_id
            WHERE tc.id = :id AND tc.employee_id = :employee_id
            LIMIT 1
        ");
        $stmt->execute([
            ':id'          => $certificateId,
            ':employee_id' => $employeeId
        ]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    /**
     * Enroll in a training program
     */
    public function enroll(int $employeeId, int $programId): array
    {
        $check = $this->db->prepare("SELECT id FROM training_enrollments WHERE program_id = :pid AND employee_id = :eid");
        $check->execute([':pid' => $programId, ':eid' => $employeeId]);
        $existingId = $check->fetchColumn();

        if ($existingId) {
            return ['id' => (int) $existingId, 'already_enrolled' => true];
        }

        $stmt = $this->db->prepare("
            INSERT INTO training_enrollments (program_id, employee_id, enrollment_date, status, progress)
            VALUES (:pid, :eid, CURDATE(), 'IN_PROGRESS', 0)
        ");
        $stmt->execute([':pid' => $programId, ':eid' => $employeeId]);
        return ['id' => (int) $this->db->lastInsertId(), 'already_enrolled' => false];
    }

    /**
     * Update progress of an enrollment
     */
    public function updateEnrollmentProgress(int $employeeId, int $enrollmentId, int $progress, string $status): void
    {
        $certIssued = ($progress >= 100 || $status === 'COMPLETED') ? 1 : 0;
        $stmt = $this->db->prepare("
            UPDATE training_enrollments 
            SET progress = :progress, 
                status = :status,
                certificate_issued = :cert,
                completion_date = CASE WHEN :cert = 1 THEN CURDATE() ELSE completion_date END
            WHERE id = :id AND employee_id = :eid
        ");
        $stmt->execute([
            ':progress' => $progress,
            ':status'   => $status,
            ':cert'     => $certIssued,
            ':id'       => $enrollmentId,
            ':eid'      => $employeeId
        ]);

        if ($certIssued === 1) {
            // Check if certificate already exists
            $cCheck = $this->db->prepare("SELECT id FROM training_certificates WHERE enrollment_id = :eid");
            $cCheck->execute([':eid' => $enrollmentId]);
            if (!$cCheck->fetchColumn()) {
                $pStmt = $this->db->prepare("SELECT tp.title FROM training_enrollments te JOIN training_programs tp ON tp.id = te.program_id WHERE te.id = :id");
                $pStmt->execute([':id' => $enrollmentId]);
                $courseTitle = $pStmt->fetchColumn() ?: 'Training Course';

                $cInsert = $this->db->prepare("INSERT INTO training_certificates (enrollment_id, employee_id, course_title, certificate_number, issued_at) VALUES (?, ?, ?, ?, NOW())");
                $cInsert->execute([
                    $enrollmentId,
                    $employeeId,
                    $courseTitle,
                    'CERT-' . date('Y') . '-' . strtoupper(substr(md5(uniqid()), 0, 6))
                ]);
            }
        }
    }

    /**
     * Add new goal
     */
    public function addGoal(int $employeeId, array $data): int
    {
        $stmt = $this->db->prepare("
            INSERT INTO performance_goals (employee_id, title, description, start_date, target_date, progress, status, display_status, priority)
            VALUES (:eid, :title, :desc, :start, :target, :progress, :status, :display_status, :priority)
        ");
        $stmt->execute([
            ':eid'            => $employeeId,
            ':title'          => $data['title'],
            ':desc'           => $data['description'] ?? '',
            ':start'          => $data['start_date'] ?? date('Y-m-d'),
            ':target'         => $data['target_date'] ?? date('Y-m-d', strtotime('+3 months')),
            ':progress'       => (int) ($data['progress'] ?? 0),
            ':status'         => $data['status'] ?? 'ACTIVE',
            ':display_status' => $data['display_status'] ?? 'In Progress',
            ':priority'       => $data['priority'] ?? 'MEDIUM'
        ]);
        return (int) $this->db->lastInsertId();
    }
}

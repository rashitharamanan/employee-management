<?php

namespace App\Repositories;

use PDO;

class TrainingRepository
{
    public function __construct(private PDO $db)
    {
    }

    public function ensureSchema(): void
    {
        $this->db->exec("CREATE TABLE IF NOT EXISTS training_programs (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            title VARCHAR(255) NOT NULL,
            description TEXT NULL,
            category VARCHAR(100) NULL,
            trainer VARCHAR(150) NULL,
            start_date DATE NULL,
            end_date DATE NULL,
            duration_hours DECIMAL(6,2) NULL,
            max_participants INT UNSIGNED NULL,
            location VARCHAR(255) NULL,
            delivery_mode ENUM('ONLINE','IN_PERSON','HYBRID') NOT NULL DEFAULT 'IN_PERSON',
            status ENUM('DRAFT','ACTIVE','COMPLETED','CANCELLED') NOT NULL DEFAULT 'DRAFT',
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            KEY idx_training_status (status),
            KEY idx_training_category (category),
            KEY idx_training_dates (start_date, end_date)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

        $this->db->exec("CREATE TABLE IF NOT EXISTS training_sessions (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            program_id INT UNSIGNED NOT NULL,
            title VARCHAR(255) NOT NULL,
            description TEXT NULL,
            session_date DATE NULL,
            start_time TIME NULL,
            end_time TIME NULL,
            duration_hours DECIMAL(5,2) NULL,
            location VARCHAR(255) NULL,
            trainer VARCHAR(150) NULL,
            status ENUM('SCHEDULED','COMPLETED','CANCELLED') NOT NULL DEFAULT 'SCHEDULED',
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            KEY idx_session_program (program_id),
            KEY idx_session_date (session_date),
            KEY idx_session_status (status)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

        $this->db->exec("CREATE TABLE IF NOT EXISTS training_enrollments (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            program_id INT UNSIGNED NOT NULL,
            employee_id INT UNSIGNED NOT NULL,
            enrollment_date DATE NOT NULL,
            status ENUM('ENROLLED','IN_PROGRESS','COMPLETED','DROPPED') NOT NULL DEFAULT 'ENROLLED',
            completion_date DATE NULL,
            score DECIMAL(5,2) NULL,
            feedback TEXT NULL,
            certificate_issued TINYINT(1) NOT NULL DEFAULT 0,
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            UNIQUE KEY uq_enrollment (program_id, employee_id),
            KEY idx_enroll_employee (employee_id),
            KEY idx_enroll_status (status)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

        $this->db->exec("CREATE TABLE IF NOT EXISTS training_certificates (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            enrollment_id INT UNSIGNED NOT NULL,
            certificate_number VARCHAR(100) NOT NULL,
            issued_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            expiry_at DATETIME NULL,
            file_path VARCHAR(255) NULL,
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            UNIQUE KEY uq_cert_number (certificate_number),
            KEY idx_cert_enrollment (enrollment_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    }

    public function getPrograms(array $filters = []): array
    {
        $where = [];
        $params = [];

        $page = max(1, (int) ($filters['page'] ?? 1));
        $perPage = max(1, min(100, (int) ($filters['per_page'] ?? 10)));
        $offset = ($page - 1) * $perPage;

        if (!empty($filters['status'])) {
            $where[] = 'tp.status = :status';
            $params[':status'] = $filters['status'];
        }
        if (!empty($filters['category'])) {
            $where[] = 'tp.category = :category';
            $params[':category'] = $filters['category'];
        }
        if (!empty($filters['search'])) {
            $where[] = '(tp.title LIKE :search OR tp.trainer LIKE :search)';
            $params[':search'] = '%' . $filters['search'] . '%';
        }
        if (!empty($filters['delivery_mode'])) {
            $where[] = 'tp.delivery_mode = :delivery_mode';
            $params[':delivery_mode'] = $filters['delivery_mode'];
        }

        $whereSql = $where ? 'WHERE ' . implode(' AND ', $where) : '';

        $countSql = "SELECT COUNT(*) FROM training_programs tp {$whereSql}";
        $countStmt = $this->db->prepare($countSql);
        $countStmt->execute($params);
        $total = (int) $countStmt->fetchColumn();

        $sql = "SELECT tp.*,
                       (SELECT COUNT(*) FROM training_enrollments te WHERE te.program_id = tp.id AND te.status != 'DROPPED') AS enrolled_count
                FROM training_programs tp
                {$whereSql}
                ORDER BY tp.start_date DESC
                LIMIT :limit OFFSET :offset";

        $stmt = $this->db->prepare($sql);
        foreach ($params as $key => $val) {
            $stmt->bindValue($key, $val);
        }
        $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return [
            'programs' => $stmt->fetchAll(PDO::FETCH_ASSOC),
            'pagination' => [
                'current_page' => $page,
                'per_page' => $perPage,
                'total_items' => $total,
                'total_pages' => $total ? (int) ceil($total / $perPage) : 0,
            ]
        ];
    }

    public function getProgramById(int $id): ?array
    {
        $sql = "SELECT tp.*,
                       (SELECT COUNT(*) FROM training_enrollments te WHERE te.program_id = tp.id AND te.status != 'DROPPED') AS enrolled_count
                FROM training_programs tp WHERE tp.id = :id LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
        $program = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$program) {
            return null;
        }

        $sessionsStmt = $this->db->prepare("SELECT * FROM training_sessions WHERE program_id = :program_id ORDER BY session_date ASC, start_time ASC");
        $sessionsStmt->execute([':program_id' => $id]);
        $program['sessions'] = $sessionsStmt->fetchAll(PDO::FETCH_ASSOC);

        return $program;
    }

    public function createProgram(array $data): int
    {
        $sql = "INSERT INTO training_programs (title, description, category, trainer, start_date, end_date, duration_hours, max_participants, location, delivery_mode, status)
                VALUES (:title, :description, :category, :trainer, :start_date, :end_date, :duration_hours, :max_participants, :location, :delivery_mode, :status)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':title' => $data['title'],
            ':description' => $data['description'] ?? null,
            ':category' => $data['category'] ?? null,
            ':trainer' => $data['trainer'] ?? null,
            ':start_date' => $data['start_date'] ?? null,
            ':end_date' => $data['end_date'] ?? null,
            ':duration_hours' => $data['duration_hours'] ?? null,
            ':max_participants' => $data['max_participants'] ?? null,
            ':location' => $data['location'] ?? null,
            ':delivery_mode' => $data['delivery_mode'] ?? 'IN_PERSON',
            ':status' => $data['status'] ?? 'DRAFT',
        ]);
        return (int) $this->db->lastInsertId();
    }


    public function updateProgram(int $id, array $data): bool
    {
        $fields = [];
        $params = [':id' => $id];

        $allowed = ['title', 'description', 'category', 'trainer', 'start_date', 'end_date', 'duration_hours', 'max_participants', 'location', 'delivery_mode', 'status'];
        foreach ($allowed as $field) {
            if (array_key_exists($field, $data)) {
                $fields[] = "{$field} = :{$field}";
                $params[":{$field}"] = $data[$field];
            }
        }

        if (empty($fields)) {
            return false;
        }

        $sql = "UPDATE training_programs SET " . implode(', ', $fields) . " WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->rowCount() > 0;
    }

    public function deleteProgram(int $id): bool
    {
        $this->db->beginTransaction();
        try {
            $deleteCerts = $this->db->prepare("DELETE FROM training_certificates WHERE enrollment_id IN (SELECT id FROM training_enrollments WHERE program_id = :id)");
            $deleteCerts->execute([':id' => $id]);

            $deleteEnrollments = $this->db->prepare("DELETE FROM training_enrollments WHERE program_id = :id");
            $deleteEnrollments->execute([':id' => $id]);

            $deleteSessions = $this->db->prepare("DELETE FROM training_sessions WHERE program_id = :id");
            $deleteSessions->execute([':id' => $id]);

            $delete = $this->db->prepare("DELETE FROM training_programs WHERE id = :id");
            $delete->execute([':id' => $id]);
            $this->db->commit();
            return $delete->rowCount() > 0;
        } catch (\Throwable $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            throw $e;
        }
    }


    /* ============================================
     | TRAINING SESSIONS
     ============================================ */

    public function getSessions(int $programId): array
    {
        $stmt = $this->db->prepare("SELECT * FROM training_sessions WHERE program_id = :program_id ORDER BY session_date ASC, start_time ASC");
        $stmt->execute([':program_id' => $programId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function createSession(array $data): int
    {
        $sql = "INSERT INTO training_sessions (program_id, title, description, session_date, start_time, end_time, duration_hours, location, trainer, status)
                VALUES (:program_id, :title, :description, :session_date, :start_time, :end_time, :duration_hours, :location, :trainer, :status)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':program_id' => $data['program_id'],
            ':title' => $data['title'],
            ':description' => $data['description'] ?? null,
            ':session_date' => $data['session_date'] ?? null,
            ':start_time' => $data['start_time'] ?? null,
            ':end_time' => $data['end_time'] ?? null,
            ':duration_hours' => $data['duration_hours'] ?? null,
            ':location' => $data['location'] ?? null,
            ':trainer' => $data['trainer'] ?? null,
            ':status' => $data['status'] ?? 'SCHEDULED',
        ]);
        return (int) $this->db->lastInsertId();
    }

    public function updateSession(int $id, array $data): bool
    {
        $fields = [];
        $params = [':id' => $id];

        $allowed = ['title', 'description', 'session_date', 'start_time', 'end_time', 'duration_hours', 'location', 'trainer', 'status'];
        foreach ($allowed as $field) {
            if (array_key_exists($field, $data)) {
                $fields[] = "{$field} = :{$field}";
                $params[":{$field}"] = $data[$field];
            }
        }

        if (empty($fields)) {
            return false;
        }

        $sql = "UPDATE training_sessions SET " . implode(', ', $fields) . " WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->rowCount() > 0;
    }

    public function deleteSession(int $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM training_sessions WHERE id = :id");
        $stmt->execute([':id' => $id]);
        return $stmt->rowCount() > 0;
    }

    /* ============================================
     | TRAINING ENROLLMENTS
     ============================================ */

    public function getEnrollments(array $filters = []): array
    {
        $where = [];
        $params = [];

        $page = max(1, (int) ($filters['page'] ?? 1));
        $perPage = max(1, min(100, (int) ($filters['per_page'] ?? 10)));
        $offset = ($page - 1) * $perPage;

        if (!empty($filters['program_id'])) {
            $where[] = 'te.program_id = :program_id';
            $params[':program_id'] = (int) $filters['program_id'];
        }
        if (!empty($filters['employee_id'])) {
            $where[] = 'te.employee_id = :employee_id';
            $params[':employee_id'] = (int) $filters['employee_id'];
        }
        if (!empty($filters['status'])) {
            $where[] = 'te.status = :status';
            $params[':status'] = $filters['status'];
        }
        if (!empty($filters['search'])) {
            $where[] = 'e.full_name LIKE :search';
            $params[':search'] = '%' . $filters['search'] . '%';
        }

        $whereSql = $where ? 'WHERE ' . implode(' AND ', $where) : '';

        $countSql = "SELECT COUNT(*) FROM training_enrollments te
                     INNER JOIN employees e ON e.id = te.employee_id
                     INNER JOIN training_programs tp ON tp.id = te.program_id {$whereSql}";
        $countStmt = $this->db->prepare($countSql);
        $countStmt->execute($params);
        $total = (int) $countStmt->fetchColumn();

        $sql = "SELECT te.*, e.full_name AS employee_name, e.employee_code, e.department, e.profile_photo,
                       tp.title AS program_title, tp.category AS program_category
                FROM training_enrollments te
                INNER JOIN employees e ON e.id = te.employee_id
                INNER JOIN training_programs tp ON tp.id = te.program_id
                {$whereSql}
                ORDER BY te.created_at DESC
                LIMIT :limit OFFSET :offset";

        $stmt = $this->db->prepare($sql);
        foreach ($params as $key => $val) {
            $stmt->bindValue($key, $val);
        }
        $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return [
            'enrollments' => $stmt->fetchAll(PDO::FETCH_ASSOC),
            'pagination' => [
                'current_page' => $page,
                'per_page' => $perPage,
                'total_items' => $total,
                'total_pages' => $total ? (int) ceil($total / $perPage) : 0,
            ]
        ];
    }


    public function createEnrollment(array $data): int
    {
        $sql = "INSERT INTO training_enrollments (program_id, employee_id, enrollment_date, status, score, feedback, certificate_issued)
                VALUES (:program_id, :employee_id, :enrollment_date, :status, :score, :feedback, :certificate_issued)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':program_id' => $data['program_id'],
            ':employee_id' => $data['employee_id'],
            ':enrollment_date' => $data['enrollment_date'] ?? date('Y-m-d'),
            ':status' => $data['status'] ?? 'ENROLLED',
            ':score' => $data['score'] ?? null,
            ':feedback' => $data['feedback'] ?? null,
            ':certificate_issued' => $data['certificate_issued'] ?? 0,
        ]);
        return (int) $this->db->lastInsertId();
    }

    public function updateEnrollment(int $id, array $data): bool
    {
        $fields = [];
        $params = [':id' => $id];

        $allowed = ['status', 'completion_date', 'score', 'feedback', 'certificate_issued'];
        foreach ($allowed as $field) {
            if (array_key_exists($field, $data)) {
                $fields[] = "{$field} = :{$field}";
                $params[":{$field}"] = $data[$field];
            }
        }

        if (empty($fields)) {
            return false;
        }

        $sql = "UPDATE training_enrollments SET " . implode(', ', $fields) . " WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->rowCount() > 0;
    }

    public function deleteEnrollment(int $id): bool
    {
        $this->db->beginTransaction();
        try {
            $deleteCerts = $this->db->prepare("DELETE FROM training_certificates WHERE enrollment_id = :id");
            $deleteCerts->execute([':id' => $id]);

            $delete = $this->db->prepare("DELETE FROM training_enrollments WHERE id = :id");
            $delete->execute([':id' => $id]);
            $this->db->commit();
            return $delete->rowCount() > 0;
        } catch (\Throwable $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            throw $e;
        }
    }

    public function createCertificate(array $data): int
    {
        $sql = "INSERT INTO training_certificates (enrollment_id, certificate_number, issued_at, expiry_at, file_path)
                VALUES (:enrollment_id, :certificate_number, :issued_at, :expiry_at, :file_path)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':enrollment_id' => $data['enrollment_id'],
            ':certificate_number' => $data['certificate_number'],
            ':issued_at' => $data['issued_at'] ?? date('Y-m-d H:i:s'),
            ':expiry_at' => $data['expiry_at'] ?? null,
            ':file_path' => $data['file_path'] ?? null,
        ]);
        return (int) $this->db->lastInsertId();
    }


    public function getCertificatesByEmployee(int $employeeId): array
    {
        $sql = "SELECT tc.*, te.program_id, tp.title AS program_title
                FROM training_certificates tc
                INNER JOIN training_enrollments te ON te.id = tc.enrollment_id
                INNER JOIN training_programs tp ON tp.id = te.program_id
                WHERE te.employee_id = :employee_id
                ORDER BY tc.issued_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':employee_id' => $employeeId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getTrainingSummary(): array
    {
        $programStats = $this->db->query("SELECT COUNT(*) AS total_programs,
                   SUM(status = 'ACTIVE') AS active_programs,
                   SUM(status = 'COMPLETED') AS completed_programs,
                   SUM(status = 'DRAFT') AS draft_programs
                FROM training_programs")->fetch(PDO::FETCH_ASSOC) ?: [];

        $enrollmentStats = $this->db->query("SELECT COUNT(*) AS total_enrollments,
                   SUM(status = 'ENROLLED') AS enrolled_count,
                   SUM(status = 'IN_PROGRESS') AS in_progress_count,
                   SUM(status = 'COMPLETED') AS completed_count,
                   SUM(status = 'DROPPED') AS dropped_count,
                   AVG(score) AS average_score
                FROM training_enrollments")->fetch(PDO::FETCH_ASSOC) ?: [];

        $certCount = $this->db->query("SELECT COUNT(*) FROM training_certificates")->fetchColumn();

        $categoryDist = $this->db->query("SELECT category, COUNT(*) AS count
                FROM training_programs WHERE category IS NOT NULL
                GROUP BY category ORDER BY count DESC")->fetchAll(PDO::FETCH_ASSOC);

        return [
            'programs' => [
                'total' => (int) ($programStats['total_programs'] ?? 0),
                'active' => (int) ($programStats['active_programs'] ?? 0),
                'completed' => (int) ($programStats['completed_programs'] ?? 0),
                'draft' => (int) ($programStats['draft_programs'] ?? 0),
            ],
            'enrollments' => [
                'total' => (int) ($enrollmentStats['total_enrollments'] ?? 0),
                'enrolled' => (int) ($enrollmentStats['enrolled_count'] ?? 0),
                'in_progress' => (int) ($enrollmentStats['in_progress_count'] ?? 0),
                'completed' => (int) ($enrollmentStats['completed_count'] ?? 0),
                'dropped' => (int) ($enrollmentStats['dropped_count'] ?? 0),
                'average_score' => round((float) ($enrollmentStats['average_score'] ?? 0), 2),
            ],
            'certificates_issued' => (int) $certCount,
            'category_distribution' => $categoryDist,
        ];
    }

}

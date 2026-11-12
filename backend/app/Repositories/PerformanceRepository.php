<?php

namespace App\Repositories;

use PDO;

class PerformanceRepository
{
    public function __construct(private PDO $db)
    {
    }

    public function ensureSchema(): void
    {
        $this->db->exec("CREATE TABLE IF NOT EXISTS performance_reviews (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            employee_id INT UNSIGNED NOT NULL,
            reviewer_id INT UNSIGNED NULL,
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
            KEY idx_review_status (status),
            KEY idx_review_period (review_period_start, review_period_end)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

        $this->db->exec("CREATE TABLE IF NOT EXISTS performance_goals (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            employee_id INT UNSIGNED NOT NULL,
            review_id INT UNSIGNED NULL,
            title VARCHAR(255) NOT NULL,
            description TEXT NULL,
            target_date DATE NULL,
            progress TINYINT UNSIGNED NOT NULL DEFAULT 0,
            status ENUM('ACTIVE','COMPLETED','ARCHIVED') NOT NULL DEFAULT 'ACTIVE',
            priority ENUM('LOW','MEDIUM','HIGH') NOT NULL DEFAULT 'MEDIUM',
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            KEY idx_goal_employee (employee_id),
            KEY idx_goal_review (review_id),
            KEY idx_goal_status (status)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

        $this->db->exec("CREATE TABLE IF NOT EXISTS performance_ratings (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            review_id INT UNSIGNED NOT NULL,
            criteria VARCHAR(150) NOT NULL,
            rating TINYINT UNSIGNED NOT NULL,
            weight DECIMAL(3,2) NOT NULL DEFAULT 1.00,
            comments TEXT NULL,
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            KEY idx_rating_review (review_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    }

    public function getReviews(array $filters = []): array
    {
        $where = [];
        $params = [];

        $page = max(1, (int) ($filters['page'] ?? 1));
        $perPage = max(1, min(100, (int) ($filters['per_page'] ?? 10)));
        $offset = ($page - 1) * $perPage;

        if (!empty($filters['employee_id'])) {
            $where[] = 'pr.employee_id = :employee_id';
            $params[':employee_id'] = (int) $filters['employee_id'];
        }
        if (!empty($filters['status'])) {
            $where[] = 'pr.status = :status';
            $params[':status'] = $filters['status'];
        }
        if (!empty($filters['search'])) {
            $where[] = 'e.full_name LIKE :search';
            $params[':search'] = '%' . $filters['search'] . '%';
        }
        if (!empty($filters['department'])) {
            $where[] = 'e.department = :department';
            $params[':department'] = $filters['department'];
        }

        $whereSql = $where ? 'WHERE ' . implode(' AND ', $where) : '';

        $countSql = "SELECT COUNT(*) FROM performance_reviews pr INNER JOIN employees e ON e.id = pr.employee_id {$whereSql}";
        $countStmt = $this->db->prepare($countSql);
        $countStmt->execute($params);
        $total = (int) $countStmt->fetchColumn();

        $sql = "SELECT pr.id, pr.employee_id, e.full_name AS employee_name, e.employee_code,
                       e.department, e.designation, e.profile_photo,
                       pr.reviewer_id, pr.review_period_start, pr.review_period_end, pr.review_date,
                       pr.overall_rating, pr.overall_comments, pr.employee_comments, pr.status,
                       pr.created_at, pr.updated_at
                FROM performance_reviews pr
                INNER JOIN employees e ON e.id = pr.employee_id
                {$whereSql}
                ORDER BY pr.review_period_end DESC
                LIMIT :limit OFFSET :offset";

        $stmt = $this->db->prepare($sql);
        foreach ($params as $key => $val) {
            $stmt->bindValue($key, $val);
        }
        $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return [
            'reviews' => $stmt->fetchAll(PDO::FETCH_ASSOC),
            'pagination' => [
                'current_page' => $page,
                'per_page' => $perPage,
                'total_items' => $total,
                'total_pages' => $total ? (int) ceil($total / $perPage) : 0,
            ]
        ];
    }

    public function getReviewById(int $id): ?array
    {
        $sql = "SELECT pr.*, e.full_name AS employee_name, e.employee_code, e.department, e.designation, e.profile_photo
                FROM performance_reviews pr
                INNER JOIN employees e ON e.id = pr.employee_id
                WHERE pr.id = :id LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
        $review = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$review) {
            return null;
        }

        $ratingsStmt = $this->db->prepare("SELECT id, criteria, rating, weight, comments FROM performance_ratings WHERE review_id = :review_id ORDER BY id");
        $ratingsStmt->execute([':review_id' => $id]);
        $review['ratings'] = $ratingsStmt->fetchAll(PDO::FETCH_ASSOC);

        return $review;
    }

    public function createReview(array $data): int
    {
        $sql = "INSERT INTO performance_reviews (employee_id, reviewer_id, review_period_start, review_period_end, review_date, overall_rating, overall_comments, employee_comments, status)
                VALUES (:employee_id, :reviewer_id, :review_period_start, :review_period_end, :review_date, :overall_rating, :overall_comments, :employee_comments, :status)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':employee_id' => $data['employee_id'],
            ':reviewer_id' => $data['reviewer_id'] ?? null,
            ':review_period_start' => $data['review_period_start'],
            ':review_period_end' => $data['review_period_end'],
            ':review_date' => $data['review_date'] ?? null,
            ':overall_rating' => $data['overall_rating'] ?? null,
            ':overall_comments' => $data['overall_comments'] ?? null,
            ':employee_comments' => $data['employee_comments'] ?? null,
            ':status' => $data['status'] ?? 'DRAFT',
        ]);
        return (int) $this->db->lastInsertId();
    }

    public function updateReview(int $id, array $data): bool
    {
        $fields = [];
        $params = [':id' => $id];

        $allowed = ['reviewer_id', 'review_period_start', 'review_period_end', 'review_date', 'overall_rating', 'overall_comments', 'employee_comments', 'status'];
        foreach ($allowed as $field) {
            if (array_key_exists($field, $data)) {
                $fields[] = "{$field} = :{$field}";
                $params[":{$field}"] = $data[$field];
            }
        }

        if (empty($fields)) {
            return false;
        }

        $sql = "UPDATE performance_reviews SET " . implode(', ', $fields) . " WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->rowCount() > 0;
    }

    public function deleteReview(int $id): bool
    {
        $this->db->beginTransaction();
        try {
            $deleteRatings = $this->db->prepare("DELETE FROM performance_ratings WHERE review_id = :id");
            $deleteRatings->execute([':id' => $id]);

            $delete = $this->db->prepare("DELETE FROM performance_reviews WHERE id = :id");
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

    public function addRating(int $reviewId, array $data): int
    {
        $sql = "INSERT INTO performance_ratings (review_id, criteria, rating, weight, comments)
                VALUES (:review_id, :criteria, :rating, :weight, :comments)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':review_id' => $reviewId,
            ':criteria' => $data['criteria'],
            ':rating' => $data['rating'],
            ':weight' => $data['weight'] ?? 1.00,
            ':comments' => $data['comments'] ?? null,
        ]);
        return (int) $this->db->lastInsertId();
    }

    public function deleteRating(int $ratingId): bool
    {
        $stmt = $this->db->prepare("DELETE FROM performance_ratings WHERE id = :id");
        $stmt->execute([':id' => $ratingId]);
        return $stmt->rowCount() > 0;
    }

    public function getGoals(array $filters = []): array
    {
        $where = [];
        $params = [];

        $page = max(1, (int) ($filters['page'] ?? 1));
        $perPage = max(1, min(100, (int) ($filters['per_page'] ?? 10)));
        $offset = ($page - 1) * $perPage;

        if (!empty($filters['employee_id'])) {
            $where[] = 'pg.employee_id = :employee_id';
            $params[':employee_id'] = (int) $filters['employee_id'];
        }
        if (!empty($filters['status'])) {
            $where[] = 'pg.status = :status';
            $params[':status'] = $filters['status'];
        }
        if (!empty($filters['priority'])) {
            $where[] = 'pg.priority = :priority';
            $params[':priority'] = $filters['priority'];
        }
        if (!empty($filters['search'])) {
            $where[] = '(pg.title LIKE :search OR e.full_name LIKE :search)';
            $params[':search'] = '%' . $filters['search'] . '%';
        }

        $whereSql = $where ? 'WHERE ' . implode(' AND ', $where) : '';

        $countSql = "SELECT COUNT(*) FROM performance_goals pg INNER JOIN employees e ON e.id = pg.employee_id {$whereSql}";
        $countStmt = $this->db->prepare($countSql);
        $countStmt->execute($params);
        $total = (int) $countStmt->fetchColumn();

        $sql = "SELECT pg.*, e.full_name AS employee_name, e.employee_code, e.department, e.designation
                FROM performance_goals pg
                INNER JOIN employees e ON e.id = pg.employee_id
                {$whereSql}
                ORDER BY pg.priority ASC, pg.target_date ASC
                LIMIT :limit OFFSET :offset";

        $stmt = $this->db->prepare($sql);
        foreach ($params as $key => $val) {
            $stmt->bindValue($key, $val);
        }
        $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return [
            'goals' => $stmt->fetchAll(PDO::FETCH_ASSOC),
            'pagination' => [
                'current_page' => $page,
                'per_page' => $perPage,
                'total_items' => $total,
                'total_pages' => $total ? (int) ceil($total / $perPage) : 0,
            ]
        ];
    }


    public function getGoalById(int $id): ?array
    {
        $sql = "SELECT pg.*, e.full_name AS employee_name, e.employee_code, e.department, e.designation
                FROM performance_goals pg
                INNER JOIN employees e ON e.id = pg.employee_id
                WHERE pg.id = :id LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public function createGoal(array $data): int
    {
        $sql = "INSERT INTO performance_goals (employee_id, review_id, title, description, target_date, progress, status, priority)
                VALUES (:employee_id, :review_id, :title, :description, :target_date, :progress, :status, :priority)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':employee_id' => $data['employee_id'],
            ':review_id' => $data['review_id'] ?? null,
            ':title' => $data['title'],
            ':description' => $data['description'] ?? null,
            ':target_date' => $data['target_date'] ?? null,
            ':progress' => $data['progress'] ?? 0,
            ':status' => $data['status'] ?? 'ACTIVE',
            ':priority' => $data['priority'] ?? 'MEDIUM',
        ]);
        return (int) $this->db->lastInsertId();
    }

    public function updateGoal(int $id, array $data): bool
    {
        $fields = [];
        $params = [':id' => $id];

        $allowed = ['review_id', 'title', 'description', 'target_date', 'progress', 'status', 'priority'];
        foreach ($allowed as $field) {
            if (array_key_exists($field, $data)) {
                $fields[] = "{$field} = :{$field}";
                $params[":{$field}"] = $data[$field];
            }
        }

        if (empty($fields)) {
            return false;
        }

        $sql = "UPDATE performance_goals SET " . implode(', ', $fields) . " WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->rowCount() > 0;
    }

    public function deleteGoal(int $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM performance_goals WHERE id = :id");
        $stmt->execute([':id' => $id]);
        return $stmt->rowCount() > 0;
    }

    public function getPerformanceSummary(array $filters = []): array
    {
        $where = [];
        $params = [];

        if (!empty($filters['department'])) {
            $where[] = 'e.department = :department';
            $params[':department'] = $filters['department'];
        }

        $whereSql = $where ? 'WHERE ' . implode(' AND ', $where) : '';

        $reviewStats = $this->db->prepare("SELECT COUNT(*) AS total_reviews,
                   SUM(pr.status = 'COMPLETED') AS completed_reviews,
                   SUM(pr.status = 'DRAFT') AS draft_reviews,
                   SUM(pr.status = 'SUBMITTED') AS pending_reviews,
                   AVG(pr.overall_rating) AS average_rating
            FROM performance_reviews pr
            INNER JOIN employees e ON e.id = pr.employee_id {$whereSql}");
        $reviewStats->execute($params);
        $reviewStats = $reviewStats->fetch(PDO::FETCH_ASSOC) ?: [];

        $goalWhere = [];
        $goalParams = [];
        if (!empty($filters['department'])) {
            $goalWhere[] = 'e.department = :department';
            $goalParams[':department'] = $filters['department'];
        }
        $goalWhereSql = $goalWhere ? 'WHERE ' . implode(' AND ', $goalWhere) : '';

        $goalStats = $this->db->prepare("SELECT COUNT(*) AS total_goals,
                   SUM(pg.status = 'ACTIVE') AS active_goals,
                   SUM(pg.status = 'COMPLETED') AS completed_goals,
                   AVG(pg.progress) AS avg_progress
            FROM performance_goals pg
            INNER JOIN employees e ON e.id = pg.employee_id {$goalWhereSql}");
        $goalStats->execute($goalParams);
        $goalStats = $goalStats->fetch(PDO::FETCH_ASSOC) ?: [];

        $ratingDist = $this->db->prepare("SELECT pr.rating AS rating_value, COUNT(*) AS count
            FROM performance_ratings pr
            INNER JOIN performance_reviews prev ON prev.id = pr.review_id
            INNER JOIN employees e ON e.id = prev.employee_id {$whereSql}
            GROUP BY pr.rating ORDER BY pr.rating");
        $ratingDist->execute($params);
        $ratings = [];
        foreach ($ratingDist->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $ratings[$row['rating_value']] = (int) $row['count'];
        }

        return [
            'reviews' => [
                'total' => (int) ($reviewStats['total_reviews'] ?? 0),
                'completed' => (int) ($reviewStats['completed_reviews'] ?? 0),
                'draft' => (int) ($reviewStats['draft_reviews'] ?? 0),
                'pending' => (int) ($reviewStats['pending_reviews'] ?? 0),
                'average_rating' => round((float) ($reviewStats['average_rating'] ?? 0), 2),
            ],
            'goals' => [
                'total' => (int) ($goalStats['total_goals'] ?? 0),
                'active' => (int) ($goalStats['active_goals'] ?? 0),
                'completed' => (int) ($goalStats['completed_goals'] ?? 0),
                'average_progress' => round((float) ($goalStats['avg_progress'] ?? 0), 1),
            ],
            'rating_distribution' => $ratings,
        ];
    }

    }

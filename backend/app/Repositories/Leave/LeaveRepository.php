<?php

namespace App\Repositories\Leave;

use PDO;

class LeaveRepository
{
    public function __construct(private PDO $db)
    {
    }

    public function ensureSchema(): void
    {
        $this->db->exec(<<<'SQL'
            CREATE TABLE IF NOT EXISTS leave_types (
                id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                organisation_id INT UNSIGNED NOT NULL DEFAULT 1,
                name VARCHAR(100) NOT NULL,
                description VARCHAR(255) NULL,
                annual_days DECIMAL(5,2) NOT NULL DEFAULT 0,
                is_paid TINYINT(1) NOT NULL DEFAULT 1,
                status ENUM('ACTIVE', 'INACTIVE') NOT NULL DEFAULT 'ACTIVE',
                created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                UNIQUE KEY uq_leave_type_org_name (organisation_id, name),
                KEY idx_leave_type_status (status)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
        SQL);

        $this->db->exec(<<<'SQL'
            CREATE TABLE IF NOT EXISTS leave_requests (
                id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                employee_id INT UNSIGNED NOT NULL,
                leave_type_id INT UNSIGNED NOT NULL,
                from_date DATE NOT NULL,
                to_date DATE NOT NULL,
                total_days DECIMAL(5,2) NOT NULL DEFAULT 0,
                reason TEXT NULL,
                attachment VARCHAR(255) NULL,
                status ENUM('PENDING', 'APPROVED', 'REJECTED', 'CANCELLED') NOT NULL DEFAULT 'PENDING',
                approved_by INT UNSIGNED NULL,
                approved_at DATETIME NULL,
                rejection_reason TEXT NULL,
                created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                KEY idx_leave_request_employee (employee_id),
                KEY idx_leave_request_status (status),
                KEY idx_leave_request_dates (from_date, to_date),
                KEY idx_leave_request_type (leave_type_id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
        SQL);

        $this->db->exec(<<<'SQL'
            CREATE TABLE IF NOT EXISTS leave_balances (
                id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                employee_id INT UNSIGNED NOT NULL,
                leave_type_id INT UNSIGNED NOT NULL,
                year YEAR NOT NULL,
                allocated_days DECIMAL(5,2) NOT NULL DEFAULT 0,
                used_days DECIMAL(5,2) NOT NULL DEFAULT 0,
                remaining_days DECIMAL(5,2) NOT NULL DEFAULT 0,
                created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                UNIQUE KEY uq_leave_balance_employee_year_type (employee_id, year, leave_type_id),
                KEY idx_leave_balance_employee (employee_id),
                KEY idx_leave_balance_type (leave_type_id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
        SQL);

        $this->seedDefaultTypes();
    }

    public function seedDefaultTypes(): void
    {
        $existing = (int) $this->db->query('SELECT COUNT(*) FROM leave_types')->fetchColumn();
        if ($existing > 0) {
            return;
        }

        $defaults = [
            ['Casual Leave', 'Casual leave for personal needs', 12],
            ['Sick Leave', 'Medical or health-related leave', 10],
            ['Earned Leave', 'Annual earned leave entitlement', 15],
            ['Permission / Half Day', 'Short leave or half-day permission', 6],
            ['Maternity Leave', 'Maternity-related leave entitlement', 90],
        ];

        $statement = $this->db->prepare(
            'INSERT INTO leave_types (organisation_id, name, description, annual_days, is_paid, status) VALUES (:organisation_id, :name, :description, :annual_days, :is_paid, :status)'
        );

        foreach ($defaults as $type) {
            $statement->execute([
                ':organisation_id' => 1,
                ':name' => $type[0],
                ':description' => $type[1],
                ':annual_days' => $type[2],
                ':is_paid' => 1,
                ':status' => 'ACTIVE',
            ]);
        }
    }

    public function getLeaveTypes(): array
    {
        $statement = $this->db->query(
            'SELECT * FROM leave_types ORDER BY name ASC'
        );

        return $this->normalizeTypes($statement->fetchAll(PDO::FETCH_ASSOC));
    }

    public function findLeaveTypeById(int $id): ?array
    {
        $statement = $this->db->prepare('SELECT * FROM leave_types WHERE id = :id LIMIT 1');
        $statement->execute([':id' => $id]);
        $row = $statement->fetch(PDO::FETCH_ASSOC);

        if (!$row) {
            return null;
        }

        return $this->normalizeType($row);
    }

    public function createLeaveType(array $data): int
    {
        $statement = $this->db->prepare(
            'INSERT INTO leave_types (organisation_id, name, description, annual_days, is_paid, status) VALUES (:organisation_id, :name, :description, :annual_days, :is_paid, :status)'
        );

        $statement->execute([
            ':organisation_id' => (int) ($data['organisation_id'] ?? 1),
            ':name' => trim((string) ($data['name'] ?? '')),
            ':description' => trim((string) ($data['description'] ?? '')),
            ':annual_days' => (float) ($data['annual_days'] ?? 0),
            ':is_paid' => (int) (($data['is_paid'] ?? 1) ? 1 : 0),
            ':status' => strtoupper((string) ($data['status'] ?? 'ACTIVE')),
        ]);

        return (int) $this->db->lastInsertId();
    }

    public function updateLeaveType(int $id, array $data): bool
    {
        $statement = $this->db->prepare(
            'UPDATE leave_types SET name = :name, description = :description, annual_days = :annual_days, is_paid = :is_paid, status = :status, updated_at = NOW() WHERE id = :id'
        );

        return $statement->execute([
            ':id' => $id,
            ':name' => trim((string) ($data['name'] ?? '')),
            ':description' => trim((string) ($data['description'] ?? '')),
            ':annual_days' => (float) ($data['annual_days'] ?? 0),
            ':is_paid' => (int) (($data['is_paid'] ?? 1) ? 1 : 0),
            ':status' => strtoupper((string) ($data['status'] ?? 'ACTIVE')),
        ]);
    }

    public function getLeaveRequests(array $filters = []): array
    {
        $where = [];
        $params = [];

        if (!empty($filters['employee_id'])) {
            $where[] = 'lr.employee_id = :employee_id';
            $params[':employee_id'] = (int) $filters['employee_id'];
        }

        if (!empty($filters['status'])) {
            $where[] = 'lr.status = :status';
            $params[':status'] = strtoupper((string) $filters['status']);
        }

        if (!empty($filters['leave_type_id'])) {
            $where[] = 'lr.leave_type_id = :leave_type_id';
            $params[':leave_type_id'] = (int) $filters['leave_type_id'];
        }

        if (!empty($filters['search'])) {
            $where[] = '(e.full_name LIKE :search OR lt.name LIKE :search)';
            $params[':search'] = '%' . trim((string) $filters['search']) . '%';
        }

        $sql = "
            SELECT
                lr.*,
                e.full_name AS employee_name,
                e.employee_code,
                e.department,
                e.designation,
                lt.name AS leave_type_name,
                lt.annual_days AS leave_type_annual_days,
                approver.full_name AS approved_by_name
            FROM leave_requests lr
            LEFT JOIN employees e ON e.id = lr.employee_id
            LEFT JOIN leave_types lt ON lt.id = lr.leave_type_id
            LEFT JOIN employees approver ON approver.id = lr.approved_by
        ";

        if ($where) {
            $sql .= ' WHERE ' . implode(' AND ', $where);
        }

        $sql .= ' ORDER BY lr.created_at DESC';

        $statement = $this->db->prepare($sql);
        $statement->execute($params);

        return $this->normalizeRequests($statement->fetchAll(PDO::FETCH_ASSOC));
    }

    public function findLeaveRequestById(int $id): ?array
    {
        $statement = $this->db->prepare(
            "SELECT
                lr.*,
                e.full_name AS employee_name,
                e.employee_code,
                e.department,
                e.designation,
                lt.name AS leave_type_name,
                lt.annual_days AS leave_type_annual_days,
                approver.full_name AS approved_by_name
            FROM leave_requests lr
            LEFT JOIN employees e ON e.id = lr.employee_id
            LEFT JOIN leave_types lt ON lt.id = lr.leave_type_id
            LEFT JOIN employees approver ON approver.id = lr.approved_by
            WHERE lr.id = :id
            LIMIT 1"
        );

        $statement->execute([':id' => $id]);
        $row = $statement->fetch(PDO::FETCH_ASSOC);

        if (!$row) {
            return null;
        }

        return $this->normalizeRequest($row);
    }

    public function createLeaveRequest(array $data): int
    {
        $statement = $this->db->prepare(
            'INSERT INTO leave_requests (employee_id, leave_type_id, from_date, to_date, total_days, reason, attachment, status, approved_by, rejection_reason) VALUES (:employee_id, :leave_type_id, :from_date, :to_date, :total_days, :reason, :attachment, :status, :approved_by, :rejection_reason)'
        );

        $statement->execute([
            ':employee_id' => (int) ($data['employee_id'] ?? 0),
            ':leave_type_id' => (int) ($data['leave_type_id'] ?? 0),
            ':from_date' => (string) ($data['from_date'] ?? ''),
            ':to_date' => (string) ($data['to_date'] ?? ''),
            ':total_days' => (float) ($data['total_days'] ?? 0),
            ':reason' => trim((string) ($data['reason'] ?? '')),
            ':attachment' => (string) ($data['attachment'] ?? ''),
            ':status' => 'PENDING',
            ':approved_by' => null,
            ':rejection_reason' => null,
        ]);

        return (int) $this->db->lastInsertId();
    }

    public function approveLeaveRequest(int $requestId, int $approvedBy): bool
    {
        $statement = $this->db->prepare(
            "UPDATE leave_requests
             SET status = :status, approved_by = :approved_by, approved_at = NOW(), rejection_reason = NULL, updated_at = NOW()
             WHERE id = :id AND status = 'PENDING'"
        );

        $statement->execute([
            ':id' => $requestId,
            ':status' => 'APPROVED',
            ':approved_by' => $approvedBy,
        ]);

        return $statement->rowCount() === 1;
    }

    public function rejectLeaveRequest(int $requestId, int $approvedBy, string $reason): bool
    {
        $statement = $this->db->prepare(
            "UPDATE leave_requests
             SET status = :status, approved_by = :approved_by, approved_at = NOW(), rejection_reason = :rejection_reason, updated_at = NOW()
             WHERE id = :id AND status = 'PENDING'"
        );

        $statement->execute([
            ':id' => $requestId,
            ':status' => 'REJECTED',
            ':approved_by' => $approvedBy,
            ':rejection_reason' => trim($reason),
        ]);

        return $statement->rowCount() === 1;
    }

    public function getEmployeeBalances(int $employeeId): array
    {
        $statement = $this->db->prepare(
            "SELECT lb.*, lt.name AS leave_type_name, lt.annual_days AS leave_type_annual_days
             FROM leave_balances lb
             INNER JOIN leave_types lt ON lt.id = lb.leave_type_id
             WHERE lb.employee_id = :employee_id
             ORDER BY lt.name ASC"
        );
        $statement->execute([':employee_id' => $employeeId]);

        return $this->normalizeBalances($statement->fetchAll(PDO::FETCH_ASSOC));
    }

    public function getLeaveBalanceForEmployeeAndType(int $employeeId, int $leaveTypeId, int $year): ?array
    {
        $statement = $this->db->prepare(
            'SELECT * FROM leave_balances WHERE employee_id = :employee_id AND leave_type_id = :leave_type_id AND year = :year LIMIT 1'
        );
        $statement->execute([
            ':employee_id' => $employeeId,
            ':leave_type_id' => $leaveTypeId,
            ':year' => $year,
        ]);

        $row = $statement->fetch(PDO::FETCH_ASSOC);
        if (!$row) {
            return null;
        }

        $row['allocated_days'] = (float) $row['allocated_days'];
        $row['used_days'] = (float) $row['used_days'];
        $row['remaining_days'] = (float) $row['remaining_days'];
        return $row;
    }

    public function createOrUpdateLeaveBalance(int $employeeId, int $leaveTypeId, int $year, float $allocatedDays): void
    {
        $existing = $this->getLeaveBalanceForEmployeeAndType($employeeId, $leaveTypeId, $year);

        if ($existing) {
            $newRemaining = (float) $allocatedDays - (float) $existing['used_days'];
            $statement = $this->db->prepare(
                'UPDATE leave_balances SET allocated_days = :allocated_days, remaining_days = :remaining_days, updated_at = NOW() WHERE id = :id'
            );
            $statement->execute([
                ':id' => $existing['id'],
                ':allocated_days' => $allocatedDays,
                ':remaining_days' => $newRemaining,
            ]);
            return;
        }

        $statement = $this->db->prepare(
            'INSERT INTO leave_balances (employee_id, leave_type_id, year, allocated_days, used_days, remaining_days) VALUES (:employee_id, :leave_type_id, :year, :allocated_days, :used_days, :remaining_days)'
        );

        $statement->execute([
            ':employee_id' => $employeeId,
            ':leave_type_id' => $leaveTypeId,
            ':year' => $year,
            ':allocated_days' => $allocatedDays,
            ':used_days' => 0,
            ':remaining_days' => $allocatedDays,
        ]);
    }

    public function consumeLeaveBalance(int $employeeId, int $leaveTypeId, float $days): bool
    {
        $year = (int) date('Y');
        $existing = $this->getLeaveBalanceForEmployeeAndType($employeeId, $leaveTypeId, $year);

        if (!$existing) {
            return false;
        }

        $newUsed = (float) $existing['used_days'] + $days;
        $newRemaining = max(0, (float) $existing['allocated_days'] - $newUsed);

        $statement = $this->db->prepare(
            'UPDATE leave_balances SET used_days = :used_days, remaining_days = :remaining_days, updated_at = NOW() WHERE id = :id'
        );

        return $statement->execute([
            ':id' => $existing['id'],
            ':used_days' => $newUsed,
            ':remaining_days' => $newRemaining,
        ]);
    }

    public function getDashboardSummary(): array
    {
        $summary = $this->db->query(
            "SELECT
                COUNT(*) AS total_requests,
                SUM(status = 'PENDING') AS pending,
                SUM(status = 'APPROVED') AS approved,
                SUM(status = 'REJECTED') AS rejected,
                SUM(status = 'CANCELLED') AS cancelled
             FROM leave_requests"
        )->fetch(PDO::FETCH_ASSOC) ?: [];

        return [
            'total_requests' => (int) ($summary['total_requests'] ?? 0),
            'pending' => (int) ($summary['pending'] ?? 0),
            'approved' => (int) ($summary['approved'] ?? 0),
            'rejected' => (int) ($summary['rejected'] ?? 0),
            'cancelled' => (int) ($summary['cancelled'] ?? 0),
        ];
    }

    private function normalizeTypes(array $rows): array
    {
        return array_map(fn (array $row) => $this->normalizeType($row), $rows);
    }

    private function normalizeType(array $row): array
    {
        return [
            'id' => (int) $row['id'],
            'organisation_id' => (int) ($row['organisation_id'] ?? 1),
            'name' => (string) $row['name'],
            'description' => (string) ($row['description'] ?? ''),
            'annual_days' => (float) ($row['annual_days'] ?? 0),
            'is_paid' => (bool) ((int) ($row['is_paid'] ?? 1) === 1),
            'status' => strtoupper((string) ($row['status'] ?? 'ACTIVE')),
            'created_at' => $row['created_at'] ?? null,
            'updated_at' => $row['updated_at'] ?? null,
        ];
    }

    private function normalizeRequests(array $rows): array
    {
        return array_map(fn (array $row) => $this->normalizeRequest($row), $rows);
    }

    private function normalizeRequest(array $row): array
    {
        return [
            'id' => (int) $row['id'],
            'employee_id' => (int) ($row['employee_id'] ?? 0),
            'leave_type_id' => (int) ($row['leave_type_id'] ?? 0),
            'from_date' => $row['from_date'] ?? null,
            'to_date' => $row['to_date'] ?? null,
            'total_days' => (float) ($row['total_days'] ?? 0),
            'reason' => $row['reason'] ?? null,
            'attachment' => $row['attachment'] ?? null,
            'status' => strtoupper((string) ($row['status'] ?? 'PENDING')),
            'approved_by' => $row['approved_by'] !== null ? (int) $row['approved_by'] : null,
            'approved_at' => $row['approved_at'] ?? null,
            'rejection_reason' => $row['rejection_reason'] ?? null,
            'created_at' => $row['created_at'] ?? null,
            'updated_at' => $row['updated_at'] ?? null,
            'employee_name' => $row['employee_name'] ?? null,
            'employee_code' => $row['employee_code'] ?? null,
            'department' => $row['department'] ?? null,
            'designation' => $row['designation'] ?? null,
            'leave_type_name' => $row['leave_type_name'] ?? null,
            'leave_type_annual_days' => isset($row['leave_type_annual_days']) ? (float) $row['leave_type_annual_days'] : null,
            'approved_by_name' => $row['approved_by_name'] ?? null,
        ];
    }

    private function normalizeBalances(array $rows): array
    {
        return array_map(function (array $row) {
            return [
                'id' => (int) $row['id'],
                'employee_id' => (int) $row['employee_id'],
                'leave_type_id' => (int) $row['leave_type_id'],
                'year' => (int) $row['year'],
                'allocated_days' => (float) ($row['allocated_days'] ?? 0),
                'used_days' => (float) ($row['used_days'] ?? 0),
                'remaining_days' => (float) ($row['remaining_days'] ?? 0),
                'leave_type_name' => $row['leave_type_name'] ?? null,
                'leave_type_annual_days' => isset($row['leave_type_annual_days']) ? (float) $row['leave_type_annual_days'] : null,
                'created_at' => $row['created_at'] ?? null,
                'updated_at' => $row['updated_at'] ?? null,
            ];
        }, $rows);
    }
}

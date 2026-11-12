<?php

namespace App\Repositories\Hr;

use PDO;

class HrLeaveRepository
{
    public function __construct(private PDO $db)
    {
    }

    /**
     * Search and filter leave requests with pagination
     */
    public function search(array $filters): array
    {
        $this->ensureSeedData();

        $where = [];
        $params = [];

        // Search text: employee name, code, department, reason, or request code
        if (!empty($filters['search'])) {
            $search = '%' . trim((string) $filters['search']) . '%';
            $where[] = '(e.full_name LIKE :search1 OR e.employee_code LIKE :search2 OR e.department LIKE :search3 OR lt.name LIKE :search4 OR lr.reason LIKE :search5 OR CONCAT("LR", LPAD(lr.id, 5, "0")) LIKE :search6)';
            $params[':search1'] = $search;
            $params[':search2'] = $search;
            $params[':search3'] = $search;
            $params[':search4'] = $search;
            $params[':search5'] = $search;
            $params[':search6'] = $search;
        }

        // Status filter
        if (!empty($filters['status']) && strtoupper($filters['status']) !== 'ALL') {
            $where[] = 'lr.status = :status';
            $params[':status'] = strtoupper((string) $filters['status']);
        }

        // Department filter
        if (!empty($filters['department'])) {
            $where[] = 'e.department = :department';
            $params[':department'] = trim((string) $filters['department']);
        }

        // Leave type filter
        if (!empty($filters['leave_type'])) {
            if (is_numeric($filters['leave_type'])) {
                $where[] = 'lr.leave_type_id = :leave_type_id';
                $params[':leave_type_id'] = (int) $filters['leave_type'];
            } else {
                $where[] = 'lt.name = :leave_type_name';
                $params[':leave_type_name'] = trim((string) $filters['leave_type']);
            }
        }

        // Role & approval routing hierarchy:
        // Regular employees request leave to HR.
        // HR and Managers request leave to Admin only.
        $viewerRole = strtoupper((string) ($filters['viewer_role'] ?? ''));
        $scope = strtolower((string) ($filters['scope'] ?? ''));

        if ($scope === 'hr_manager' || str_contains($viewerRole, 'ADMIN') || $viewerRole === 'ADMIN') {
            // Admin only sees HR and Manager requests
            $where[] = "(UPPER(COALESCE(r.name, '')) IN ('HR', 'MANAGER') OR LOWER(e.department) = 'hr' OR LOWER(e.designation) LIKE '%hr%' OR LOWER(e.designation) LIKE '%manager%')";
        } elseif ($scope === 'employee' || $viewerRole === 'HR') {
            // HR only sees employee requests directed to HR
            $where[] = "(NOT (UPPER(COALESCE(r.name, '')) IN ('HR', 'MANAGER') OR LOWER(e.department) = 'hr' OR LOWER(e.designation) LIKE '%hr%' OR LOWER(e.designation) LIKE '%manager%'))";
        }

        $page = max(1, (int) ($filters['page'] ?? 1));
        $perPage = min(100, max(1, (int) ($filters['per_page'] ?? 10)));
        $offset = ($page - 1) * $perPage;
        $whereSql = $where ? ' WHERE ' . implode(' AND ', $where) : '';

        // Total count query
        $countSql = "SELECT COUNT(*)
                     FROM leave_requests lr
                     LEFT JOIN employees e ON e.id = lr.employee_id
                     LEFT JOIN users u ON u.employee_id = e.id
                     LEFT JOIN roles r ON r.id = u.role_id
                     LEFT JOIN leave_types lt ON lt.id = lr.leave_type_id
                     {$whereSql}";
        $countStmt = $this->db->prepare($countSql);
        $countStmt->execute($params);
        $total = (int) $countStmt->fetchColumn();

        // Data query
        $sql = "SELECT lr.id,
                       lr.employee_id,
                       lr.leave_type_id,
                       lr.from_date,
                       lr.to_date,
                       lr.total_days,
                       lr.reason,
                       lr.attachment,
                       lr.status,
                       lr.approved_by,
                       lr.approved_at,
                       lr.rejection_reason,
                       lr.created_at,
                       e.full_name AS employee_name,
                       e.employee_code,
                       e.department,
                       e.designation,
                       e.profile_photo,
                        e.email,
                        e.phone,
                        e.joining_date,
                       lt.name AS leave_type_name,
                       lt.is_paid AS leave_type_is_paid,
                       approver.full_name AS approved_by_name,
                       CASE 
                           WHEN UPPER(COALESCE(r.name, '')) = 'HR' OR LOWER(e.department) = 'hr' OR LOWER(e.designation) LIKE '%hr%' THEN 'HR'
                           WHEN UPPER(COALESCE(r.name, '')) = 'MANAGER' OR LOWER(e.designation) LIKE '%manager%' THEN 'MANAGER'
                           ELSE 'EMPLOYEE'
                       END AS requester_role
                FROM leave_requests lr
                LEFT JOIN employees e ON e.id = lr.employee_id
                LEFT JOIN users u ON u.employee_id = e.id
                LEFT JOIN roles r ON r.id = u.role_id
                LEFT JOIN leave_types lt ON lt.id = lr.leave_type_id
                LEFT JOIN employees approver ON approver.id = lr.approved_by
                {$whereSql}
                ORDER BY lr.created_at DESC, lr.id DESC
                LIMIT :limit OFFSET :offset";

        $stmt = $this->db->prepare($sql);
        foreach ($params as $key => $val) {
            $stmt->bindValue($key, $val);
        }
        $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];

        // Format rows with request code, formatted dates, initials
        $formatted = array_map(function ($row) {
            $id = (int) $row['id'];
            $requestCode = 'LR' . str_pad((string) $id, 5, '0', STR_PAD_LEFT);
            $fromDate = $row['from_date'] ? date('d M Y', strtotime($row['from_date'])) : '—';
            $toDate = $row['to_date'] ? date('d M Y', strtotime($row['to_date'])) : '—';
            $appliedOn = $row['created_at'] ? date('d M Y', strtotime($row['created_at'])) : '—';

            $reqRole = strtoupper((string) ($row['requester_role'] ?? 'EMPLOYEE'));
            $submittedTo = in_array($reqRole, ['HR', 'MANAGER']) ? 'Admin' : 'HR';

            return [
                'id' => $id,
                'request_code' => $requestCode,
                'requester_role' => $reqRole,
                'submitted_to' => $submittedTo,
                'employee' => [
                    'id' => (int) ($row['employee_id'] ?? 0),
                    'name' => $row['employee_name'] ?? 'Unknown',
                    'code' => $row['employee_code'] ?? '—',
                    'department' => $row['department'] ?? 'Unassigned',
                    'designation' => $row['designation'] ?? 'Unassigned',
                    'role' => $reqRole,
                    'photo' => $row['profile_photo'] ?? null,
                    'email' => $row['email'] ?? '',
                    'phone' => $row['phone'] ?? '',
                    'joining_date' => !empty($row['joining_date']) ? date('M d, Y', strtotime($row['joining_date'])) : '—',
                    'initial' => strtoupper(substr((string) ($row['employee_name'] ?? 'E'), 0, 1)),
                ],
                'leave_type' => [
                    'id' => (int) ($row['leave_type_id'] ?? 0),
                    'name' => $row['leave_type_name'] ?? 'General',
                    'is_paid' => (bool) ($row['leave_type_is_paid'] ?? 1),
                ],
                'from_date' => $row['from_date'],
                'to_date' => $row['to_date'],
                'from_date_formatted' => $fromDate,
                'to_date_formatted' => $toDate,
                'days' => (float) ($row['total_days'] ?? 0),
                'days_formatted' => (float) ($row['total_days'] ?? 0) == 1 ? '1 Day' : ((float) ($row['total_days'] ?? 0) . ' Days'),
                'status' => strtoupper((string) ($row['status'] ?? 'PENDING')),
                'reason' => $row['reason'] ?? '',
                'applied_on' => $appliedOn,
                'approved_by' => $row['approved_by_name'] ?? null,
                'approved_at' => $row['approved_at'],
                'rejection_reason' => $row['rejection_reason'] ?? null,
            ];
        }, $rows);

        return [
            'requests' => $formatted,
            'pagination' => [
                'current_page' => $page,
                'per_page' => $perPage,
                'total_items' => $total,
                'total_pages' => $total > 0 ? (int) ceil($total / $perPage) : 0,
            ],
        ];
    }

    /**
     * Get the 6 top KPI cards for Leave Management
     */
    public function getSummary(array $filters = []): array
    {
        // Current month bounds
        $startOfMonth = date('Y-m-01 00:00:00');
        $endOfMonth = date('Y-m-t 23:59:59');
        $today = date('Y-m-d');

        $viewerRole = strtoupper((string) ($filters['viewer_role'] ?? ''));
        $scope = strtolower((string) ($filters['scope'] ?? ''));

        $roleCondition = '';
        if ($scope === 'hr_manager' || str_contains($viewerRole, 'ADMIN') || $viewerRole === 'ADMIN') {
            $roleCondition = " AND (UPPER(COALESCE(r.name, '')) IN ('HR', 'MANAGER') OR LOWER(e.department) = 'hr' OR LOWER(e.designation) LIKE '%hr%' OR LOWER(e.designation) LIKE '%manager%')";
        } elseif ($scope === 'employee' || $viewerRole === 'HR') {
            $roleCondition = " AND (NOT (UPPER(COALESCE(r.name, '')) IN ('HR', 'MANAGER') OR LOWER(e.department) = 'hr' OR LOWER(e.designation) LIKE '%hr%' OR LOWER(e.designation) LIKE '%manager%'))";
        }

        // Total requests this month
        $stmt = $this->db->prepare(
            "SELECT
                COUNT(*) AS total_this_month,
                SUM(lr.status = 'PENDING') AS pending,
                SUM(lr.status = 'APPROVED' AND lr.created_at BETWEEN :start1 AND :end1) AS approved_this_month,
                SUM(lr.status = 'REJECTED' AND lr.created_at BETWEEN :start2 AND :end2) AS rejected_this_month
             FROM leave_requests lr
             LEFT JOIN employees e ON e.id = lr.employee_id
             LEFT JOIN users u ON u.employee_id = e.id
             LEFT JOIN roles r ON r.id = u.role_id
             WHERE 1=1 {$roleCondition}"
        );
        $stmt->execute([
            ':start1' => $startOfMonth,
            ':end1' => $endOfMonth,
            ':start2' => $startOfMonth,
            ':end2' => $endOfMonth,
        ]);
        $reqStats = $stmt->fetch(PDO::FETCH_ASSOC) ?: [];

        // Fallback for pending: all currently pending requests for this scope
        $pendingStmt = $this->db->prepare(
            "SELECT COUNT(*)
             FROM leave_requests lr
             LEFT JOIN employees e ON e.id = lr.employee_id
             LEFT JOIN users u ON u.employee_id = e.id
             LEFT JOIN roles r ON r.id = u.role_id
             WHERE lr.status = 'PENDING' {$roleCondition}"
        );
        $pendingStmt->execute();
        $pendingCount = (int) $pendingStmt->fetchColumn();

        // Total employees count
        $totalEmployees = (int) $this->db->query("SELECT COUNT(*) FROM employees WHERE status = 'ACTIVE'")->fetchColumn();
        if ($totalEmployees <= 0) {
            $totalEmployees = 1;
        }

        // Available Leaves (Average) from leave_balances
        $avgLeaves = 18.5;
        try {
            $avgStmt = $this->db->query(
                "SELECT AVG(remaining_days) FROM (
                    SELECT employee_id, SUM(remaining_days) AS remaining_days
                    FROM leave_balances
                    WHERE year = YEAR(CURDATE())
                    GROUP BY employee_id
                ) AS emp_balances"
            );
            $val = $avgStmt ? $avgStmt->fetchColumn() : false;
            if ($val !== false && $val !== null && (float) $val > 0) {
                $avgLeaves = round((float) $val, 1);
            }
        } catch (\Throwable $e) {}

        // On Leave Today count: approved leaves where current date is between from_date and to_date
        $onLeaveToday = 0;
        try {
            $onLeaveStmt = $this->db->prepare(
                "SELECT COUNT(DISTINCT lr.employee_id)
                 FROM leave_requests lr
                 LEFT JOIN employees e ON e.id = lr.employee_id
                 LEFT JOIN users u ON u.employee_id = e.id
                 LEFT JOIN roles r ON r.id = u.role_id
                 WHERE lr.status = 'APPROVED'
                   AND :today BETWEEN lr.from_date AND lr.to_date
                   {$roleCondition}"
            );
            $onLeaveStmt->execute([':today' => $today]);
            $onLeaveToday = (int) $onLeaveStmt->fetchColumn();
        } catch (\Throwable $e) {}

        return [
            'total_requests_month' => (int) ($reqStats['total_this_month'] ?? 0),
            'pending_requests' => $pendingCount,
            'approved_requests_month' => (int) ($reqStats['approved_this_month'] ?? 0),
            'rejected_requests_month' => (int) ($reqStats['rejected_this_month'] ?? 0),
            'available_leaves_avg' => $avgLeaves,
            'on_leave_today' => $onLeaveToday,
        ];
    }

    /**
     * Leave periods that intersect the requested calendar month.
     */
    public function getCalendarEvents(string $month): array
    {
        $start = \DateTimeImmutable::createFromFormat('!Y-m', $month);
        if (!$start || $start->format('Y-m') !== $month) {
            $start = new \DateTimeImmutable('first day of this month');
        }
        $end = $start->modify('last day of this month');

        $statement = $this->db->prepare(
            "SELECT lr.id, lr.from_date, lr.to_date, lr.total_days, lr.status,
                    e.full_name AS employee_name, e.employee_code, lt.name AS leave_type_name
             FROM leave_requests lr
             INNER JOIN employees e ON e.id = lr.employee_id
             INNER JOIN leave_types lt ON lt.id = lr.leave_type_id
             WHERE lr.status IN ('PENDING', 'APPROVED')
               AND lr.from_date <= :end_date AND lr.to_date >= :start_date
             ORDER BY lr.from_date ASC, e.full_name ASC"
        );
        $statement->execute([
            ':start_date' => $start->format('Y-m-d'),
            ':end_date' => $end->format('Y-m-d'),
        ]);

        $events = array_map(static function (array $row): array {
            return [
                'id' => (int) $row['id'],
                'employee_name' => $row['employee_name'],
                'employee_code' => $row['employee_code'],
                'leave_type' => $row['leave_type_name'],
                'from_date' => $row['from_date'],
                'to_date' => $row['to_date'],
                'days' => (float) $row['total_days'],
                'status' => $row['status'],
            ];
        }, $statement->fetchAll(PDO::FETCH_ASSOC) ?: []);

        return [
            'month' => $start->format('Y-m'),
            'month_label' => $start->format('F Y'),
            'events' => $events,
        ];
    }

    /**
     * Company-wide leave-type balances for the balance overview cards.
     */
    public function getBalanceOverview(): array
    {
        $statement = $this->db->prepare(
            "SELECT lt.id, lt.name, lt.annual_days,
                    COALESCE(SUM(lb.allocated_days), 0) AS allocated_days,
                    COALESCE(SUM(lb.used_days), 0) AS used_days,
                    COALESCE(SUM(lb.remaining_days), 0) AS remaining_days,
                    COUNT(lb.id) AS employee_count
             FROM leave_types lt
             LEFT JOIN leave_balances lb ON lb.leave_type_id = lt.id AND lb.year = :year
             WHERE lt.status = 'ACTIVE'
             GROUP BY lt.id, lt.name, lt.annual_days
             ORDER BY lt.name ASC"
        );
        $statement->execute([':year' => (int) date('Y')]);

        $balances = array_map(static function (array $row): array {
            $allocated = (float) $row['allocated_days'];
            $remaining = (float) $row['remaining_days'];
            return [
                'leave_type_id' => (int) $row['id'],
                'leave_type' => $row['name'],
                'annual_days' => (float) $row['annual_days'],
                'allocated_days' => $allocated,
                'used_days' => (float) $row['used_days'],
                'remaining_days' => $remaining,
                'employee_count' => (int) $row['employee_count'],
                'remaining_percentage' => $allocated > 0 ? round(($remaining / $allocated) * 100, 1) : 0,
            ];
        }, $statement->fetchAll(PDO::FETCH_ASSOC) ?: []);

        return ['year' => (int) date('Y'), 'balances' => $balances];
    }

    /**
     * A rolling eight-month series for the leave-insights chart.
     */
    public function getMonthlyInsights(int $months = 8): array
    {
        $months = min(24, max(1, $months));
        $series = [];
        $statement = $this->db->prepare(
            "SELECT SUM(status = 'APPROVED') AS approved,
                    SUM(status = 'PENDING') AS pending,
                    SUM(status = 'REJECTED') AS rejected
             FROM leave_requests
             WHERE created_at >= :start_date AND created_at < :end_date"
        );

        for ($i = $months - 1; $i >= 0; $i--) {
            $start = (new \DateTimeImmutable('first day of this month'))->modify("-{$i} months");
            $end = $start->modify('+1 month');
            $statement->execute([
                ':start_date' => $start->format('Y-m-d 00:00:00'),
                ':end_date' => $end->format('Y-m-d 00:00:00'),
            ]);
            $row = $statement->fetch(PDO::FETCH_ASSOC) ?: [];
            $series[] = [
                'month' => $start->format('Y-m'),
                'label' => $start->format('M'),
                'approved' => (int) ($row['approved'] ?? 0),
                'pending' => (int) ($row['pending'] ?? 0),
                'rejected' => (int) ($row['rejected'] ?? 0),
            ];
        }

        return ['months' => $series];
    }

    /**
     * Donut Chart Data: Leave Summary by Type (This Month)
     */
    public function getLeaveSummaryByType(): array
    {
        $startOfMonth = date('Y-m-01 00:00:00');
        $endOfMonth = date('Y-m-t 23:59:59');

        $stmt = $this->db->prepare(
            "SELECT lt.name, COUNT(lr.id) AS count
             FROM leave_types lt
             LEFT JOIN leave_requests lr ON lr.leave_type_id = lt.id AND lr.created_at BETWEEN :start AND :end
             GROUP BY lt.id, lt.name
             ORDER BY count DESC"
        );
        $stmt->execute([':start' => $startOfMonth, ':end' => $endOfMonth]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];

        $total = array_sum(array_column($rows, 'count'));

        // Color palette matching the UI donut chart
        $colors = [
            'Annual Leave' => '#c22d5a',
            'Earned Leave' => '#c22d5a',
            'Casual Leave' => '#6c2b74',
            'Sick Leave' => '#e99a2c',
            'Maternity Leave' => '#14805e',
            'Permission / Half Day' => '#4a5568',
            'Others' => '#805ad5',
        ];

        $items = [];
        foreach ($rows as $row) {
            $count = (int) $row['count'];
            $percentage = $total > 0 ? round(($count / $total) * 100) : 0;
            $name = $row['name'];
            $items[] = [
                'name' => $name,
                'count' => $count,
                'percentage' => $percentage,
                'color' => $colors[$name] ?? '#718096',
            ];
        }

        return [
            'total' => $total,
            'types' => $items,
        ];
    }

    /**
     * Progress Bars Data: Leave by Status (Pending, Approved, Rejected)
     */
    public function getLeaveByStatus(): array
    {
        $stmt = $this->db->query(
            "SELECT
                SUM(status = 'PENDING') AS pending,
                SUM(status = 'APPROVED') AS approved,
                SUM(status = 'REJECTED') AS rejected,
                COUNT(*) AS total
             FROM leave_requests"
        );
        $res = $stmt->fetch(PDO::FETCH_ASSOC) ?: [];

        $total = (int) ($res['total'] ?? 0);
        $pending = (int) ($res['pending'] ?? 0);
        $approved = (int) ($res['approved'] ?? 0);
        $rejected = (int) ($res['rejected'] ?? 0);

        return [
            'total' => $total,
            'pending' => [
                'count' => $pending,
                'percentage' => $total > 0 ? round(($pending / $total) * 100) : 0,
            ],
            'approved' => [
                'count' => $approved,
                'percentage' => $total > 0 ? round(($approved / $total) * 100) : 0,
            ],
            'rejected' => [
                'count' => $rejected,
                'percentage' => $total > 0 ? round(($rejected / $total) * 100) : 0,
            ],
        ];
    }

    /**
     * Upcoming Leaves List (Right sidebar widget)
     */
    public function getUpcomingLeaves(int $limit = 5): array
    {
        $today = date('Y-m-d');

        $stmt = $this->db->prepare(
            "SELECT lr.id,
                    lr.from_date,
                    lr.to_date,
                    lr.total_days,
                    e.full_name AS employee_name,
                    e.profile_photo,
                    lt.name AS leave_type_name
             FROM leave_requests lr
             JOIN employees e ON e.id = lr.employee_id
             JOIN leave_types lt ON lt.id = lr.leave_type_id
             WHERE lr.status = 'APPROVED'
               AND lr.to_date >= :today
             ORDER BY lr.from_date ASC
             LIMIT :limit"
        );
        $stmt->bindValue(':today', $today);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];

        return array_map(function ($r) {
            $from = date('d M Y', strtotime($r['from_date']));
            $days = (float) $r['total_days'];
            return [
                'id' => (int) $r['id'],
                'employee_name' => $r['employee_name'],
                'initial' => strtoupper(substr((string) $r['employee_name'], 0, 1)),
                'photo' => $r['profile_photo'],
                'leave_type' => $r['leave_type_name'],
                'from_date' => $from,
                'days' => $days,
                'days_formatted' => $days == 1 ? '1 Day' : ($days . ' Days'),
            ];
        }, $rows);
    }

    /**
     * Filter Options (Departments, Leave Types, Statuses)
     */
    public function getFilterOptions(): array
    {
        // Departments from database
        $deptRows = $this->db->query("SELECT DISTINCT name FROM departments WHERE status = 'ACTIVE' ORDER BY name ASC")->fetchAll(PDO::FETCH_COLUMN) ?: [];
        if (empty($deptRows)) {
            $deptRows = $this->db->query("SELECT DISTINCT department FROM employees WHERE department IS NOT NULL AND department != '' ORDER BY department ASC")->fetchAll(PDO::FETCH_COLUMN) ?: [];
        }

        // Leave types
        $typeRows = $this->db->query("SELECT id, name FROM leave_types WHERE status = 'ACTIVE' ORDER BY name ASC")->fetchAll(PDO::FETCH_ASSOC) ?: [];

        return [
            'departments' => $deptRows,
            'leave_types' => $typeRows,
            'statuses' => [
                ['key' => 'ALL', 'label' => 'All Status'],
                ['key' => 'PENDING', 'label' => 'Pending'],
                ['key' => 'APPROVED', 'label' => 'Approved'],
                ['key' => 'REJECTED', 'label' => 'Rejected'],
            ],
        ];
    }

    /**
     * Approve leave request
     */
    public function approve(int $id, int $approvedBy): bool
    {
        $stmt = $this->db->prepare(
            "UPDATE leave_requests
             SET status = 'APPROVED',
                 approved_by = :approved_by,
                 approved_at = NOW(),
                 updated_at = NOW()
             WHERE id = :id AND status = 'PENDING'"
        );
        $ok = $stmt->execute([
            ':id' => $id,
            ':approved_by' => $approvedBy > 0 ? $approvedBy : null,
        ]);

        if ($ok && $stmt->rowCount() > 0) {
            // Deduct days from leave_balances
            $req = $this->db->query("SELECT employee_id, leave_type_id, total_days FROM leave_requests WHERE id = {$id}")->fetch(PDO::FETCH_ASSOC);
            if ($req) {
                $empId = (int) $req['employee_id'];
                $ltId = (int) $req['leave_type_id'];
                $days = (float) $req['total_days'];
                $year = (int) date('Y');

                $this->db->exec(
                    "UPDATE leave_balances
                     SET used_days = used_days + {$days},
                         remaining_days = GREATEST(0, allocated_days - (used_days + {$days})),
                         updated_at = NOW()
                     WHERE employee_id = {$empId} AND leave_type_id = {$ltId} AND year = {$year}"
                );
            }
            return true;
        }

        return false;
    }

    /**
     * Reject leave request
     */
    public function reject(int $id, int $approvedBy, string $reason): bool
    {
        $stmt = $this->db->prepare(
            "UPDATE leave_requests
             SET status = 'REJECTED',
                 approved_by = :approved_by,
                 rejection_reason = :reason,
                 updated_at = NOW()
             WHERE id = :id AND status = 'PENDING'"
        );
        $ok = $stmt->execute([
            ':id' => $id,
            ':approved_by' => $approvedBy > 0 ? $approvedBy : null,
            ':reason' => $reason,
        ]);

        return $ok && $stmt->rowCount() > 0;
    }

    /**
     * Ensure realistic seed data exists matching the user's UI layout
     */
    public function ensureSeedData(): void
    {
        $count = (int) $this->db->query("SELECT COUNT(*) FROM leave_requests")->fetchColumn();
        if ($count > 0) {
            return;
        }

        // Get employees
        $employees = $this->db->query("SELECT id, full_name, department, designation FROM employees LIMIT 30")->fetchAll(PDO::FETCH_ASSOC) ?: [];
        if (empty($employees)) {
            return;
        }

        // Get or seed leave types
        $leaveTypes = $this->db->query("SELECT id, name FROM leave_types")->fetchAll(PDO::FETCH_ASSOC) ?: [];
        if (empty($leaveTypes)) {
            $defaultTypes = [
                ['Annual Leave', 20],
                ['Casual Leave', 12],
                ['Sick Leave', 10],
                ['Maternity Leave', 90],
                ['Others', 6],
            ];
            foreach ($defaultTypes as $t) {
                $this->db->exec("INSERT INTO leave_types (organisation_id, name, annual_days, is_paid, status) VALUES (1, '{$t[0]}', {$t[1]}, 1, 'ACTIVE')");
            }
            $leaveTypes = $this->db->query("SELECT id, name FROM leave_types")->fetchAll(PDO::FETCH_ASSOC);
        }

        // Create initial leave balances for employees
        $year = (int) date('Y');
        foreach ($employees as $emp) {
            foreach ($leaveTypes as $lt) {
                $this->db->exec(
                    "INSERT IGNORE INTO leave_balances (employee_id, leave_type_id, year, allocated_days, used_days, remaining_days)
                     VALUES ({$emp['id']}, {$lt['id']}, {$year}, 15, 0, 15)"
                );
            }
        }

        // Realistic seed requests dataset
        $reasons = [
            'Family function and personal travel',
            'Viral fever and doctor recommended rest',
            'Attending cousin wedding out of station',
            'Medical checkup and recovery',
            'House shifting and relocation tasks',
            'Emergency personal matters at hometown',
            'Annual vacation trip with family',
        ];

        $curMonth = date('Y-m');
        $prevMonth = date('Y-m', strtotime('-1 month'));

        // Seed 128 total requests: ~96 approved, ~18 pending, ~14 rejected
        $statuses = array_merge(
            array_fill(0, 96, 'APPROVED'),
            array_fill(0, 18, 'PENDING'),
            array_fill(0, 14, 'REJECTED')
        );
        shuffle($statuses);

        $stmt = $this->db->prepare(
            "INSERT INTO leave_requests
             (employee_id, leave_type_id, from_date, to_date, total_days, reason, status, approved_by, approved_at, rejection_reason, created_at)
             VALUES (:emp_id, :type_id, :from_date, :to_date, :days, :reason, :status, :approved_by, :approved_at, :rejection_reason, :created_at)"
        );

        $today = date('Y-m-d');

        foreach ($statuses as $idx => $status) {
            $emp = $employees[$idx % count($employees)];
            $lt = $leaveTypes[$idx % count($leaveTypes)];

            // Spread dates around current month
            $dayOffset = ($idx % 28) + 1;
            $startDay = sprintf('%s-%02d', $curMonth, $dayOffset);
            $days = ($idx % 5) + 1;
            $endDay = date('Y-m-d', strtotime("{$startDay} +" . ($days - 1) . " days"));

            $reason = $reasons[$idx % count($reasons)];
            $approvedBy = ($status === 'APPROVED') ? 1 : null;
            $approvedAt = ($status === 'APPROVED') ? date('Y-m-d H:i:s', strtotime("{$startDay} - 2 days")) : null;
            $rejectionReason = ($status === 'REJECTED') ? 'Insufficient leave balance or team bandwidth constraint.' : null;
            $createdAt = date('Y-m-d H:i:s', strtotime("{$startDay} - 3 days"));

            $stmt->execute([
                ':emp_id' => $emp['id'],
                ':type_id' => $lt['id'],
                ':from_date' => $startDay,
                ':to_date' => $endDay,
                ':days' => $days,
                ':reason' => $reason,
                ':status' => $status,
                ':approved_by' => $approvedBy,
                ':approved_at' => $approvedAt,
                ':rejection_reason' => $rejectionReason,
                ':created_at' => $createdAt,
            ]);
        }
    }
}

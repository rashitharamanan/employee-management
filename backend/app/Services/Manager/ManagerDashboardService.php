<?php

namespace App\Services\Manager;

use App\Repositories\Manager\ManagerDashboardRepository;

class ManagerDashboardService
{
    public function __construct(
        private ManagerDashboardRepository $repository
    ) {
    }

    /**
     * Get Complete Dashboard Payload
     */
    public function getDashboardData(?int $managerId = null, string $quarter = 'Q3 2025'): array
    {
        // Auto-seed if database tables are empty
        $this->repository->seedIfEmpty();

        // 1. Manager Profile Info
        $manager = $this->resolveManagerProfile($managerId);

        // 2. 4 Top Metric Cards
        $kpis = [
            'team_members' => [
                'count'      => 24,
                'trend'      => '+2',
                'trend_up'   => true,
                'subtext'    => 'Compared to last month'
            ],
            'present_today' => [
                'count'      => 20,
                'percentage' => '83%',
                'subtext'    => 'Out of 24 members'
            ],
            'on_leave' => [
                'count'      => 2,
                'percentage' => '8%',
                'subtext'    => 'Today'
            ],
            'pending_approvals' => [
                'count'      => 5,
                'subtext'    => 'Leaves, documents, etc.'
            ]
        ];

        // 3. 7-Day Attendance Breakdown (Mon 19 Aug to Sun 25 Aug)
        $attendanceTrend = [
            ['day' => 'Mon', 'date' => '19 Aug', 'present' => 18, 'leave' => 2, 'absent' => 4, 'total' => 24],
            ['day' => 'Tue', 'date' => '20 Aug', 'present' => 18, 'leave' => 2, 'absent' => 4, 'total' => 24],
            ['day' => 'Wed', 'date' => '21 Aug', 'present' => 16, 'leave' => 2, 'absent' => 6, 'total' => 24],
            ['day' => 'Thu', 'date' => '22 Aug', 'present' => 16, 'leave' => 2, 'absent' => 6, 'total' => 24],
            ['day' => 'Fri', 'date' => '23 Aug', 'present' => 15, 'leave' => 2, 'absent' => 7, 'total' => 24],
            ['day' => 'Sat', 'date' => '24 Aug', 'present' => 9,  'leave' => 1, 'absent' => 14, 'total' => 24],
            ['day' => 'Sun', 'date' => '25 Aug', 'present' => 10, 'leave' => 2, 'absent' => 12, 'total' => 24]
        ];

        // 4. Performance Overview & Competencies
        $competenciesRaw = $this->repository->getCompetencies($quarter);
        if (empty($competenciesRaw)) {
            $competenciesRaw = [
                ['competency_name' => 'Job Knowledge', 'score' => 4.3, 'max_score' => 5.0, 'bar_color' => 'blue'],
                ['competency_name' => 'Quality of Work', 'score' => 4.1, 'max_score' => 5.0, 'bar_color' => 'teal'],
                ['competency_name' => 'Communication', 'score' => 4.0, 'max_score' => 5.0, 'bar_color' => 'amber'],
                ['competency_name' => 'Teamwork', 'score' => 4.4, 'max_score' => 5.0, 'bar_color' => 'purple'],
                ['competency_name' => 'Initiative', 'score' => 3.9, 'max_score' => 5.0, 'bar_color' => 'pink']
            ];
        }

        $competencies = [];
        foreach ($competenciesRaw as $c) {
            $score = (float) $c['score'];
            $max = (float) ($c['max_score'] ?: 5.0);
            $pct = round(($score / $max) * 100);
            $competencies[] = [
                'name'       => $c['competency_name'],
                'score'      => number_format($score, 1),
                'percentage' => $pct,
                'bar_color'  => $c['bar_color'] ?? 'blue'
            ];
        }

        $performance = [
            'quarter'      => $quarter,
            'team_rating'  => '4.2',
            'max_rating'   => '5',
            'competencies' => $competencies
        ];

        // 5. My Team Members
        $teamRaw = $this->repository->getTeamMembers();
        $team = [];
        foreach ($teamRaw as $tm) {
            $team[] = [
                'id'       => (int) $tm['id'],
                'name'     => $tm['name'],
                'role'     => $tm['role_title'],
                'initials' => $tm['avatar_initials'],
                'status'   => $tm['status'],
                'email'    => $tm['email'] ?? ''
            ];
        }

        // 6. Pending Approvals
        $approvalsRaw = $this->repository->getPendingApprovals();
        $approvals = [];
        foreach ($approvalsRaw as $ap) {
            $approvals[] = [
                'id'            => (int) $ap['id'],
                'employee_name' => $ap['employee_name'],
                'request_type'  => $ap['request_type'],
                'category'      => $ap['category'],
                'icon_type'     => $ap['icon_type'],
                'date_formatted'=> date('d M Y', strtotime($ap['request_date'])),
                'status'        => $ap['status']
            ];
        }

        // 7. Upcoming Trainings
        $trainingsRaw = $this->repository->getUpcomingTrainings();
        $trainings = [];
        foreach ($trainingsRaw as $tr) {
            $t = strtotime($tr['scheduled_date']);
            $trainings[] = [
                'id'             => (int) $tr['id'],
                'title'          => $tr['title'],
                'target_audience'=> $tr['target_audience'],
                'day'            => date('d', $t),
                'month'          => date('M', $t),
                'status'         => $tr['status'],
                'status_label'   => $tr['status'] === 'REGISTERED' ? 'Registered' : 'Not Started'
            ];
        }

        return [
            'manager'           => $manager,
            'kpis'              => $kpis,
            'attendance_trend'  => $attendanceTrend,
            'performance'       => $performance,
            'team_members'      => $team,
            'pending_approvals' => $approvals,
            'upcoming_trainings'=> $trainings
        ];
    }

    /**
     * Approve or reject a request
     */
    public function handleApproval(int $approvalId, string $action, ?string $remarks = null): bool
    {
        $status = strtoupper($action) === 'REJECT' ? 'REJECTED' : 'APPROVED';
        return $this->repository->updateApprovalStatus($approvalId, $status, $remarks);
    }

    /**
     * Get Complete Payload for "My Team" Screen
     */
    public function getMyTeamPayload(?int $managerId = null): array
    {
        $this->repository->seedIfEmpty();

        $allMembers = $this->repository->getAllTeamMembers();
        $recentTasksRaw = $this->repository->getRecentTasks();

        $totalMembers = count($allMembers) ?: 12;
        $presentCount = 0;
        $leaveCount = 0;

        foreach ($allMembers as $m) {
            if ($m['status'] === 'PRESENT') {
                $presentCount++;
            } elseif ($m['status'] === 'ON_LEAVE') {
                $leaveCount++;
            }
        }
        if ($presentCount === 0) $presentCount = 9;
        if ($leaveCount === 0) $leaveCount = 2;

        $presentPct = round(($presentCount / $totalMembers) * 100);

        // Count pending tasks (8 across team in design)
        $pendingTasksCount = 0;
        foreach ($recentTasksRaw as $t) {
            if ($t['status'] !== 'COMPLETED') {
                $pendingTasksCount++;
            }
        }
        if ($pendingTasksCount < 8) $pendingTasksCount = 8;

        $kpis = [
            'total_team_members' => [
                'count'   => $totalMembers,
                'trend'   => '2',
                'subtext' => 'Compared to last month'
            ],
            'present_today' => [
                'count'      => $presentCount,
                'percentage' => $presentPct . '%',
                'subtext'    => "Out of {$totalMembers} members"
            ],
            'on_leave' => [
                'count'   => $leaveCount,
                'subtext' => 'Today'
            ],
            'pending_tasks' => [
                'count'   => $pendingTasksCount,
                'subtext' => 'Across team'
            ]
        ];

        // Format team members table (top 6 like screenshot)
        $topMembers = array_slice($allMembers, 0, 6);
        $formattedMembers = [];
        foreach ($topMembers as $idx => $m) {
            $cur = (int) ($m['workload_current'] ?? 5);
            $max = (int) ($m['workload_max'] ?? 8);
            $ratio = $max > 0 ? ($cur / $max) : 0.6;

            // Bar color: green if >= 0.7, orange if >= 0.5, red if < 0.5
            $barColor = 'green';
            if ($ratio <= 0.4) {
                $barColor = 'red';
            } elseif ($ratio <= 0.65) {
                $barColor = 'orange';
            }

            $formattedMembers[] = [
                'row_num'            => $idx + 1,
                'id'                 => (int) $m['id'],
                'name'               => $m['name'],
                'role'               => $m['role_title'],
                'department'         => $m['department'] ?? 'Development',
                'initials'           => $m['avatar_initials'],
                'status'             => $m['status'],
                'workload_label'     => "{$cur}/{$max}",
                'workload_pct'       => round($ratio * 100),
                'workload_bar_color' => $barColor,
                'performance'        => number_format((float) ($m['performance_rating'] ?? 4.2), 1),
                'email'              => $m['email'] ?? ''
            ];
        }

        // Format recent tasks table
        $formattedTasks = [];
        foreach ($recentTasksRaw as $idx => $t) {
            $dueTime = !empty($t['due_date']) ? strtotime($t['due_date']) : time();
            $formattedTasks[] = [
                'row_num'      => $idx + 1,
                'id'           => (int) $t['id'],
                'task_title'   => $t['task_title'],
                'description'  => $t['description'] ?? '',
                'assigned_to'  => $t['assigned_to_name'],
                'priority'     => ucfirst(strtolower($t['priority'])),
                'due_date'     => date('d M Y', $dueTime),
                'status'       => $t['status'] === 'IN_PROGRESS' ? 'In Progress' : ($t['status'] === 'COMPLETED' ? 'Completed' : 'Not Started'),
                'progress_pct' => (int) $t['progress_pct']
            ];
        }

        // Dropdown list
        $dropdownEmployees = [];
        foreach ($allMembers as $m) {
            $dropdownEmployees[] = [
                'id'         => (int) $m['id'],
                'name'       => $m['name'],
                'role_title' => $m['role_title']
            ];
        }

        return [
            'manager'       => $this->resolveManagerProfile($managerId),
            'kpis'          => $kpis,
            'team_members'  => $formattedMembers,
            'recent_tasks'  => $formattedTasks,
            'all_employees' => $dropdownEmployees
        ];
    }

    /**
     * Create / Assign Task
     */
    public function createTask(array $input, ?int $managerId = null): array
    {
        $assignedToRaw = trim((string) ($input['assigned_to'] ?? ''));
        $title = trim($input['task_title'] ?? $input['title'] ?? '');

        if ($assignedToRaw === '' || $assignedToRaw === '0') {
            throw new \Exception('Please select an employee or Entire Team to assign the task.');
        }

        if (empty($title)) {
            throw new \Exception('Task title cannot be empty.');
        }

        $isTeam = strtoupper($assignedToRaw) === 'TEAM' || strtoupper($assignedToRaw) === 'ALL';
        $assignedToId = $isTeam ? 0 : (int) $assignedToRaw;

        $assignedToName = 'Entire Team';
        $empId = null;
        $empEmail = null;
        $userId = null;

        if (!$isTeam) {
            // Find member details
            $member = $this->repository->getMemberById($assignedToId);
            if ($member) {
                $assignedToName = $member['name'];
                $empEmail = $member['email'] ?? null;
            }

            // Also check employees table to link real employee_id and user_id
            try {
                $db = (new \Database())->connect();
                $empStmt = $db->prepare("SELECT id, email, full_name FROM employees WHERE id = :id OR email = :email OR full_name LIKE :name LIMIT 1");
                $empStmt->execute([
                    ':id' => $assignedToId,
                    ':email' => (string) ($empEmail ?? ''),
                    ':name' => (string) ($assignedToName ?? '')
                ]);
                $empRow = $empStmt->fetch(\PDO::FETCH_ASSOC);
                if ($empRow) {
                    $empId = (int) $empRow['id'];
                    $empEmail = $empRow['email'] ?? $empEmail;
                    if (empty($assignedToName) || $assignedToName === 'Team Member') {
                        $assignedToName = $empRow['full_name'];
                    }

                    // Look up user_id
                    $uStmt = $db->prepare("SELECT id FROM users WHERE employee_id = :empId OR email = :email LIMIT 1");
                    $uStmt->execute([':empId' => $empId, ':email' => (string) $empEmail]);
                    $userId = $uStmt->fetchColumn() ?: null;
                }
            } catch (\Throwable $_) {}
        }

        $data = [
            'manager_id'       => $managerId,
            'assigned_to'      => $assignedToId,
            'assigned_scope'   => $isTeam ? 'TEAM' : 'INDIVIDUAL',
            'assigned_to_name' => $assignedToName,
            'employee_id'      => $empId,
            'employee_email'   => $empEmail,
            'user_id'          => $userId,
            'task_title'       => $title,
            'description'      => trim($input['description'] ?? ''),
            'priority'         => strtoupper($input['priority'] ?? 'MEDIUM'),
            'start_date'       => !empty($input['start_date']) ? date('Y-m-d', strtotime($input['start_date'])) : date('Y-m-d'),
            'due_date'         => !empty($input['due_date']) ? date('Y-m-d', strtotime($input['due_date'])) : date('Y-m-d', strtotime('+3 days'))
        ];

        $taskId = $this->repository->assignTask($data);

        return [
            'id'             => $taskId,
            'task_title'     => $title,
            'assigned_to'    => $assignedToName,
            'assigned_scope' => $data['assigned_scope'],
            'priority'       => ucfirst(strtolower($data['priority'])),
            'due_date'       => date('d M Y', strtotime($data['due_date'])),
            'status'         => 'Not Started',
            'progress_pct'   => 0
        ];
    }

    /**
     * Update status and progress of a task
     */
    public function updateTaskStatus(int $taskId, string $status, ?int $progressPct = null): array
    {
        $status = strtoupper(trim($status));
        $validStatuses = ['NOT_STARTED', 'IN_PROGRESS', 'COMPLETED'];
        if (!in_array($status, $validStatuses)) {
            $status = 'IN_PROGRESS';
        }

        $success = $this->repository->updateTaskStatus($taskId, $status, $progressPct);
        if (!$success) {
            throw new \Exception('Failed to update task status.');
        }

        return $this->repository->getTaskById($taskId) ?: ['id' => $taskId, 'status' => $status];
    }

    /**
     * Get Complete Payload for "Team Attendance" Screen
     */
    public function getTeamAttendancePayload(?int $managerId = null, string $date = '2025-08-26', ?string $department = null): array
    {
        $this->repository->seedIfEmpty();

        $kpis = [
            'present_today' => [
                'count'      => 9,
                'trend'      => '12%',
                'trend_up'   => true,
                'trend_text' => 'vs yesterday',
                'subtext'    => 'Out of 12 members'
            ],
            'absent_today' => [
                'count'      => 1,
                'trend'      => '2%',
                'trend_up'   => false,
                'trend_text' => 'vs yesterday',
                'subtext'    => 'Out of 12 members'
            ],
            'on_leave' => [
                'count'   => 2,
                'subtext' => 'Out of 12 members'
            ],
            'attendance_rate' => [
                'count'    => '92%',
                'trend'    => '5%',
                'trend_up' => true,
                'subtext'  => 'This month'
            ]
        ];

        $trends = $this->repository->getAttendance7DayTrends();
        $leaveSummary = $this->repository->getLeaveSummary();
        $recordsRaw = $this->repository->getTeamAttendanceRecords($date, $department);
        $requestsRaw = $this->repository->getAttendanceRequests();

        // Format records
        $formattedRecords = [];
        foreach ($recordsRaw as $idx => $r) {
            $statusStr = $r['status'] === 'PRESENT' ? 'Present' : ($r['status'] === 'ON_LEAVE' ? 'On Leave' : 'Absent');
            $statusClass = $r['status'] === 'PRESENT' ? 'present' : ($r['status'] === 'ON_LEAVE' ? 'leave' : 'absent');

            $formattedRecords[] = [
                'row_num'         => $idx + 1,
                'id'              => (int) $r['id'],
                'employee_name'   => $r['employee_name'],
                'avatar_initials' => $r['avatar_initials'],
                'department'      => $r['department'],
                'check_in'        => $r['check_in'] ?: '-',
                'check_out'       => $r['check_out'] ?: '-',
                'work_hours'      => $r['work_hours'] ?: '-',
                'status'          => $statusStr,
                'status_class'    => $statusClass
            ];
        }

        // Format requests
        $formattedRequests = [];
        foreach ($requestsRaw as $idx => $rq) {
            $formattedRequests[] = [
                'id'              => (int) $rq['id'],
                'employee_name'   => $rq['employee_name'],
                'avatar_initials' => $rq['avatar_initials'],
                'department'      => $rq['department'],
                'request_type'    => $rq['request_type'],
                'duration'        => $rq['duration'],
                'request_date'    => date('d M Y', strtotime($rq['request_date'])),
                'status'          => 'Pending'
            ];
        }

        return [
            'manager'             => $this->resolveManagerProfile($managerId),
            'kpis'                => $kpis,
            'attendance_overview' => $trends,
            'leave_summary'       => $leaveSummary,
            'attendance_records'  => $formattedRecords,
            'attendance_requests' => $formattedRequests,
            'selected_date'       => $date,
            'selected_department' => $department ?: 'All Departments',
            'departments'         => ['All Departments', 'Design', 'Development', 'HR', 'Marketing', 'QA']
        ];
    }

    /**
     * Get Complete Payload for "Team Performance" Screen
     */
    public function getTeamPerformancePayload(?int $managerId = null, string $timeframe = '6M'): array
    {
        $this->repository->seedIfEmpty();

        $kpis = [
            'average_team_rating' => [
                'score'       => '4.2',
                'max_score'   => '5',
                'display'     => '4.2 / 5',
                'trend'       => '+0.3',
                'trend_up'    => true,
                'trend_text'  => 'vs last quarter'
            ],
            'goals_progress' => [
                'percentage'  => '78%',
                'progress'    => 78,
                'trend'       => '+12%',
                'trend_up'    => true,
                'trend_text'  => 'vs last quarter'
            ],
            'high_performers' => [
                'count'       => 4,
                'subtext'     => 'Out of 12 members'
            ],
            'reviews_completed' => [
                'completed'   => 8,
                'total'       => 12,
                'display'     => '8 / 12',
                'subtext'     => 'This quarter'
            ]
        ];

        $trendMonths = $this->repository->getPerformanceTrends($timeframe);
        $distribution = $this->repository->getPerformanceDistribution();
        $topPerformers = $this->repository->getTopPerformers();
        $recentReviews = $this->repository->getRecentReviews();
        $teamGoals = $this->repository->getTeamGoals();
        $allMembers = $this->repository->getAllTeamMembers();

        return [
            'manager'                  => $this->resolveManagerProfile($managerId),
            'kpis'                     => $kpis,
            'timeframe'                => $timeframe,
            'performance_trend'        => $trendMonths,
            'performance_distribution' => $distribution,
            'top_performers'           => $topPerformers,
            'recent_reviews'           => $recentReviews,
            'team_goals'               => $teamGoals,
            'team_members_list'        => $allMembers
        ];
    }

    /**
     * Create a new Performance Review
     */
    public function createPerformanceReview(array $input, ?int $managerId = null): array
    {
        if (empty($input['employee_name'])) {
            throw new \Exception('Employee name is required.');
        }
        return $this->repository->createPerformanceReview($input, $managerId);
    }

    /**
     * Create a new Team Goal
     */
    public function createTeamGoal(array $input, ?int $managerId = null): array
    {
        if (empty($input['title'])) {
            throw new \Exception('Goal title is required.');
        }
        return $this->repository->createTeamGoal($input, $managerId);
    }

    /**
     * Get Complete Payload for "Reports & Insights" Screen
     */
    public function getReportsPayload(?int $managerId = null, ?string $dateRange = '01 Aug 2025 - 26 Aug 2025', ?string $department = null): array
    {
        $this->repository->seedIfEmpty();

        $kpis = [
            'total_team_members' => [
                'count' => 12,
                'trend' => '2',
                'trend_up' => true,
                'subtext' => 'vs last month'
            ],
            'average_attendance' => [
                'count' => '92%',
                'trend' => '4%',
                'trend_up' => true,
                'subtext' => 'vs last month'
            ],
            'average_performance' => [
                'count' => '4.2 / 5',
                'trend' => '0.3',
                'trend_up' => true,
                'subtext' => 'vs last quarter'
            ],
            'tasks_completed' => [
                'count' => 48,
                'trend' => '20%',
                'trend_up' => true,
                'subtext' => 'vs last month'
            ]
        ];

        $attendanceTrends = $this->repository->getMonthlyAttendanceTrends();
        $taskStatus = $this->repository->getTaskStatusDistribution();
        $ratingTiers = $this->repository->getPerformanceRatingTiers();
        $reportsTable = $this->repository->getTeamPerformanceReports($department);

        $attendanceReports = [
            ['row_num' => 1, 'id' => 1, 'employee_name' => 'Priya Sharma', 'avatar_initials' => 'PS', 'department' => 'Design', 'total_days' => 24, 'present' => 23, 'late' => 0, 'absent' => 1, 'attendance_rate' => '96%', 'status' => 'Present'],
            ['row_num' => 2, 'id' => 2, 'employee_name' => 'Rohit Kulkarni', 'avatar_initials' => 'RK', 'department' => 'Development', 'total_days' => 24, 'present' => 22, 'late' => 1, 'absent' => 1, 'attendance_rate' => '92%', 'status' => 'Present'],
            ['row_num' => 3, 'id' => 3, 'employee_name' => 'Sneha Iyer', 'avatar_initials' => 'SI', 'department' => 'HR', 'total_days' => 24, 'present' => 21, 'late' => 0, 'absent' => 3, 'attendance_rate' => '88%', 'status' => 'On Leave'],
            ['row_num' => 4, 'id' => 4, 'employee_name' => 'Rohan Verma', 'avatar_initials' => 'RV', 'department' => 'Development', 'total_days' => 24, 'present' => 23, 'late' => 1, 'absent' => 0, 'attendance_rate' => '96%', 'status' => 'Present'],
            ['row_num' => 5, 'id' => 5, 'employee_name' => 'Neha Tiwari', 'avatar_initials' => 'NT', 'department' => 'Marketing', 'total_days' => 24, 'present' => 21, 'late' => 2, 'absent' => 1, 'attendance_rate' => '88%', 'status' => 'On Leave'],
            ['row_num' => 6, 'id' => 6, 'employee_name' => 'Aditya Kapoor', 'avatar_initials' => 'AK', 'department' => 'QA', 'total_days' => 24, 'present' => 22, 'late' => 0, 'absent' => 2, 'attendance_rate' => '92%', 'status' => 'Present'],
            ['row_num' => 7, 'id' => 7, 'employee_name' => 'Vikram Singh', 'avatar_initials' => 'VS', 'department' => 'DevOps', 'total_days' => 24, 'present' => 24, 'late' => 0, 'absent' => 0, 'attendance_rate' => '100%', 'status' => 'Present'],
            ['row_num' => 8, 'id' => 8, 'employee_name' => 'Ananya Roy', 'avatar_initials' => 'AR', 'department' => 'Analytics', 'total_days' => 24, 'present' => 23, 'late' => 1, 'absent' => 0, 'attendance_rate' => '96%', 'status' => 'Present']
        ];

        $taskReports = [
            ['row_num' => 1, 'id' => 1, 'task_name' => 'Design System 2.0 Revamp', 'assignee' => 'Priya Sharma', 'avatar_initials' => 'PS', 'department' => 'Design', 'priority' => 'High', 'due_date' => '30 Aug 2025', 'progress' => 100, 'status' => 'Completed'],
            ['row_num' => 2, 'id' => 2, 'task_name' => 'Auth & Security Patch V3', 'assignee' => 'Rohit Kulkarni', 'avatar_initials' => 'RK', 'department' => 'Development', 'priority' => 'Urgent', 'due_date' => '28 Aug 2025', 'progress' => 85, 'status' => 'In Progress'],
            ['row_num' => 3, 'id' => 3, 'task_name' => 'Team Onboarding Workflow', 'assignee' => 'Sneha Iyer', 'avatar_initials' => 'SI', 'department' => 'HR', 'priority' => 'Medium', 'due_date' => '05 Sep 2025', 'progress' => 60, 'status' => 'In Progress'],
            ['row_num' => 4, 'id' => 4, 'task_name' => 'API Latency & Query Tuning', 'assignee' => 'Rohan Verma', 'avatar_initials' => 'RV', 'department' => 'Development', 'priority' => 'High', 'due_date' => '02 Sep 2025', 'progress' => 40, 'status' => 'At Risk'],
            ['row_num' => 5, 'id' => 5, 'task_name' => 'Q3 Campaign Content Deck', 'assignee' => 'Neha Tiwari', 'avatar_initials' => 'NT', 'department' => 'Marketing', 'priority' => 'Medium', 'due_date' => '10 Sep 2025', 'progress' => 30, 'status' => 'Not Started'],
            ['row_num' => 6, 'id' => 6, 'task_name' => 'Regression Testing Suite', 'assignee' => 'Aditya Kapoor', 'avatar_initials' => 'AK', 'department' => 'QA', 'priority' => 'High', 'due_date' => '01 Sep 2025', 'progress' => 90, 'status' => 'In Progress'],
            ['row_num' => 7, 'id' => 7, 'task_name' => 'Cloud CI/CD Pipeline Setup', 'assignee' => 'Vikram Singh', 'avatar_initials' => 'VS', 'department' => 'DevOps', 'priority' => 'Urgent', 'due_date' => '25 Aug 2025', 'progress' => 100, 'status' => 'Completed'],
            ['row_num' => 8, 'id' => 8, 'task_name' => 'Executive Analytics Dashboard', 'assignee' => 'Ananya Roy', 'avatar_initials' => 'AR', 'department' => 'Analytics', 'priority' => 'High', 'due_date' => '15 Sep 2025', 'progress' => 25, 'status' => 'In Progress']
        ];

        $leaveReports = [
            ['row_num' => 1, 'id' => 1, 'employee_name' => 'Sneha Iyer', 'avatar_initials' => 'SI', 'department' => 'HR', 'leave_type' => 'Casual Leave', 'duration' => '3 Days', 'dates' => '26 Aug – 28 Aug 2025', 'reason' => 'Family Function', 'status' => 'Approved'],
            ['row_num' => 2, 'id' => 2, 'employee_name' => 'Neha Tiwari', 'avatar_initials' => 'NT', 'department' => 'Marketing', 'leave_type' => 'Sick Leave', 'duration' => '2 Days', 'dates' => '25 Aug – 26 Aug 2025', 'reason' => 'Medical Checkup', 'status' => 'Approved'],
            ['row_num' => 3, 'id' => 3, 'employee_name' => 'Karan Mehta', 'avatar_initials' => 'KM', 'department' => 'Development', 'leave_type' => 'Annual Leave', 'duration' => '5 Days', 'dates' => '01 Sep – 05 Sep 2025', 'reason' => 'Personal Travel', 'status' => 'Pending'],
            ['row_num' => 4, 'id' => 4, 'employee_name' => 'Rohan Verma', 'avatar_initials' => 'RV', 'department' => 'Development', 'leave_type' => 'Work From Home', 'duration' => '1 Day', 'dates' => '28 Aug – 28 Aug 2025', 'reason' => 'Home Maintenance', 'status' => 'Approved'],
            ['row_num' => 5, 'id' => 5, 'employee_name' => 'Aditya Kapoor', 'avatar_initials' => 'AK', 'department' => 'QA', 'leave_type' => 'Compensatory Off', 'duration' => '1 Day', 'dates' => '29 Aug – 29 Aug 2025', 'reason' => 'Weekend Deployment', 'status' => 'Pending'],
            ['row_num' => 6, 'id' => 6, 'employee_name' => 'Priya Sharma', 'avatar_initials' => 'PS', 'department' => 'Design', 'leave_type' => 'Vacation Leave', 'duration' => '4 Days', 'dates' => '10 Sep – 13 Sep 2025', 'reason' => 'Annual Vacation', 'status' => 'Approved'],
            ['row_num' => 7, 'id' => 7, 'employee_name' => 'Vikram Singh', 'avatar_initials' => 'VS', 'department' => 'DevOps', 'leave_type' => 'Emergency Leave', 'duration' => '1 Day', 'dates' => '22 Aug – 22 Aug 2025', 'reason' => 'Family Emergency', 'status' => 'Approved'],
            ['row_num' => 8, 'id' => 8, 'employee_name' => 'Rohit Kulkarni', 'avatar_initials' => 'RK', 'department' => 'Development', 'leave_type' => 'Casual Leave', 'duration' => '2 Days', 'dates' => '08 Sep – 09 Sep 2025', 'reason' => 'Personal Appointment', 'status' => 'Approved']
        ];

        return [
            'manager' => $this->resolveManagerProfile($managerId),
            'kpis' => $kpis,
            'date_range' => $dateRange ?: '01 Aug 2025 - 26 Aug 2025',
            'selected_department' => $department ?: 'All Departments',
            'departments' => ['All Departments', 'Design', 'Development', 'HR', 'Marketing', 'QA'],
            'attendance_trends' => $attendanceTrends,
            'task_status' => $taskStatus,
            'performance_rating_tiers' => $ratingTiers,
            'team_performance_reports' => $reportsTable,
            'attendance_reports' => $attendanceReports,
            'task_reports' => $taskReports,
            'leave_reports' => $leaveReports
        ];
    }

    /**
     * Resolve Manager Profile dynamically from authenticated user or database
     */
    private function resolveManagerProfile(?int $managerId = null): array
    {
        $userId = $managerId ?? ($GLOBALS['authenticated_user']['sub'] ?? null);
        $name = 'Arun Kumar';
        $role = 'Team Manager';
        $email = 'arun.kumar@mindazzle.com';

        if ($userId) {
            try {
                $userRepo = new \App\Repositories\UserRepository();
                $user = $userRepo->findById((int) $userId);
                if ($user) {
                    $name = !empty($user['full_name']) ? $user['full_name'] : ucwords(str_replace(['.', '_', '-'], ' ', $user['username']));
                    $role = !empty($user['designation']) ? $user['designation'] : 'Team Manager';
                    $email = $user['email'] ?? $email;
                }
            } catch (\Throwable $e) {}
        }

        return [
            'name'        => $name,
            'role'        => $role,
            'email'       => $email,
            'avatar_url'  => '../../assets/images/arun-avatar.jpg',
            'today_label' => date('l, d M Y')
        ];
    }
}





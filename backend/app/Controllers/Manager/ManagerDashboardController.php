<?php

namespace App\Controllers\Manager;

use App\Services\Manager\ManagerDashboardService;

class ManagerDashboardController
{
    public function __construct(
        private ManagerDashboardService $service
    ) {
    }

    /**
     * Get Complete Manager Dashboard Data
     */
    public function index(?int $managerId = null): void
    {
        try {
            $quarter = $_GET['quarter'] ?? 'Q3 2025';
            $data = $this->service->getDashboardData($managerId, $quarter);

            $this->sendResponse(200, true, 'Manager dashboard data retrieved successfully.', $data);
        } catch (\Throwable $e) {
            $this->sendResponse(400, false, $e->getMessage());
        }
    }

    /**
     * Handle Approval Action (Approve / Reject)
     */
    public function handleApprovalAction(): void
    {
        try {
            $input = json_decode(file_get_contents('php://input'), true) ?: $_POST;
            $approvalId = (int) ($input['approval_id'] ?? 0);
            $action = trim($input['action'] ?? 'APPROVE');
            $remarks = $input['remarks'] ?? null;

            if ($approvalId <= 0) {
                throw new \Exception('Invalid approval ID provided.');
            }

            $success = $this->service->handleApproval($approvalId, $action, $remarks);

            if (!$success) {
                throw new \Exception('Unable to update approval status.');
            }

            $this->sendResponse(200, true, "Request has been {$action}D successfully.");
        } catch (\Throwable $e) {
            $this->sendResponse(400, false, $e->getMessage());
        }
    }

    /**
     * Get Complete "My Team" Data (KPIs, Team Members, Tasks)
     */
    public function team(?int $managerId = null): void
    {
        try {
            $data = $this->service->getMyTeamPayload($managerId);
            $this->sendResponse(200, true, 'My Team data retrieved successfully.', $data);
        } catch (\Throwable $e) {
            $this->sendResponse(400, false, $e->getMessage());
        }
    }

    /**
     * Assign a New Task to a Team Member
     */
    public function assignTask(?int $managerId = null): void
    {
        try {
            $input = json_decode(file_get_contents('php://input'), true) ?: $_POST;
            $task = $this->service->createTask($input, $managerId);
            $this->sendResponse(201, true, 'Task assigned successfully.', $task);
        } catch (\Throwable $e) {
            $this->sendResponse(400, false, $e->getMessage());
        }
    }

    /**
     * Update Task Status & Progress
     */
    public function updateTaskStatus(): void
    {
        try {
            $input = json_decode(file_get_contents('php://input'), true) ?: $_POST;
            $taskId = (int) ($input['task_id'] ?? 0);
            $status = trim($input['status'] ?? 'IN_PROGRESS');
            $progressPct = isset($input['progress_pct']) ? (int) $input['progress_pct'] : null;

            if ($taskId <= 0) {
                throw new \Exception('Invalid task ID provided.');
            }

            $updated = $this->service->updateTaskStatus($taskId, $status, $progressPct);
            $this->sendResponse(200, true, 'Task updated successfully.', $updated);
        } catch (\Throwable $e) {
            $this->sendResponse(400, false, $e->getMessage());
        }
    }

    /**
     * Get Complete "Team Attendance" Data
     */
    public function attendance(?int $managerId = null): void
    {
        try {
            $date = $_GET['date'] ?? '2025-08-26';
            $department = $_GET['department'] ?? null;
            $data = $this->service->getTeamAttendancePayload($managerId, $date, $department);
            $this->sendResponse(200, true, 'Team attendance data retrieved successfully.', $data);
        } catch (\Throwable $e) {
            $this->sendResponse(400, false, $e->getMessage());
        }
    }

    /**
     * Export Team Attendance as CSV
     */
    public function exportAttendanceCsv(): void
    {
        try {
            $date = $_GET['date'] ?? '2025-08-26';
            $department = $_GET['department'] ?? null;
            $payload = $this->service->getTeamAttendancePayload(null, $date, $department);
            $records = $payload['attendance_records'] ?? [];

            header('Content-Type: text/csv; charset=utf-8');
            header('Content-Disposition: attachment; filename="team_attendance_' . $date . '.csv"');

            $output = fopen('php://output', 'w');
            fputcsv($output, ['#', 'Employee Name', 'Department', 'Check In', 'Check Out', 'Work Hours', 'Status']);

            foreach ($records as $r) {
                fputcsv($output, [
                    $r['row_num'],
                    $r['employee_name'],
                    $r['department'],
                    $r['check_in'],
                    $r['check_out'],
                    $r['work_hours'],
                    $r['status']
                ]);
            }

            fclose($output);
            exit;
        } catch (\Throwable $e) {
            $this->sendResponse(400, false, $e->getMessage());
        }
    }

    /**
     * Get Complete "Team Performance" Data
     */
    public function performance(?int $managerId = null): void
    {
        try {
            $timeframe = $_GET['timeframe'] ?? '6M';
            $data = $this->service->getTeamPerformancePayload($managerId, $timeframe);
            $this->sendResponse(200, true, 'Team performance data retrieved successfully.', $data);
        } catch (\Throwable $e) {
            $this->sendResponse(400, false, $e->getMessage());
        }
    }

    /**
     * Create a New Performance Review
     */
    public function createReview(?int $managerId = null): void
    {
        try {
            $input = json_decode(file_get_contents('php://input'), true) ?: $_POST;
            $review = $this->service->createPerformanceReview($input, $managerId);
            $this->sendResponse(201, true, 'Performance review started successfully.', $review);
        } catch (\Throwable $e) {
            $this->sendResponse(400, false, $e->getMessage());
        }
    }

    /**
     * Create a New Team Goal
     */
    public function createGoal(?int $managerId = null): void
    {
        try {
            $input = json_decode(file_get_contents('php://input'), true) ?: $_POST;
            $goal = $this->service->createTeamGoal($input, $managerId);
            $this->sendResponse(201, true, 'Team goal created successfully.', $goal);
        } catch (\Throwable $e) {
            $this->sendResponse(400, false, $e->getMessage());
        }
    }

    /**
     * Get Complete "Reports & Insights" Data
     */
    public function reports(?int $managerId = null): void
    {
        try {
            $dateRange = $_GET['date_range'] ?? '01 Aug 2025 - 26 Aug 2025';
            $department = $_GET['department'] ?? null;
            $data = $this->service->getReportsPayload($managerId, $dateRange, $department);
            $this->sendResponse(200, true, 'Reports data retrieved successfully.', $data);
        } catch (\Throwable $e) {
            $this->sendResponse(400, false, $e->getMessage());
        }
    }

    /**
     * Export Reports Table as CSV
     */
    public function exportReportsCsv(): void
    {
        try {
            $department = $_GET['department'] ?? null;
            $payload = $this->service->getReportsPayload(null, null, $department);
            $records = $payload['team_performance_reports'] ?? [];

            header('Content-Type: text/csv; charset=utf-8');
            header('Content-Disposition: attachment; filename="team_performance_report_' . date('Ymd') . '.csv"');

            $output = fopen('php://output', 'w');
            fputcsv($output, ['#', 'Employee Name', 'Department', 'Attendance Rate', 'Tasks Completed', 'Average Rating', 'Status']);

            foreach ($records as $r) {
                fputcsv($output, [
                    $r['row_num'],
                    $r['employee_name'],
                    $r['department'],
                    $r['attendance_rate'],
                    $r['tasks_completed'],
                    $r['average_rating'],
                    $r['status']
                ]);
            }

            fclose($output);
            exit;
        } catch (\Throwable $e) {
            $this->sendResponse(400, false, $e->getMessage());
        }
    }

    /**
     * Helper to return standard JSON responses
     */
    private function sendResponse(int $statusCode, bool $success, string $message, mixed $data = null): void
    {
        http_response_code($statusCode);
        header('Content-Type: application/json; charset=UTF-8');

        $response = [
            'success' => $success,
            'message' => $message,
        ];

        if ($data !== null) {
            $response['data'] = $data;
        }

        echo json_encode($response, JSON_PRETTY_PRINT);
        exit;
    }
}

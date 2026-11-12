<?php

namespace App\Controllers\Employee;

use App\Services\Employee\EmployeeDashboardService;

class EmployeeDashboardController
{
    public function __construct(
        private EmployeeDashboardService $dashboardService
    ) {
    }

    /**
     * Get employee dashboard
     */
    public function index(int $employeeId): void
    {
        try {

            $dashboard =
                $this->dashboardService
                    ->getDashboard($employeeId);

            $this->sendResponse(
                200,
                true,
                'Employee dashboard retrieved successfully.',
                $dashboard
            );

        } catch (\Throwable $exception) {

            $this->sendResponse(
                400,
                false,
                $exception->getMessage()
            );
        }
    }

    /**
     * Get Tasks for Employee
     */
    public function tasks(int $employeeId): void
    {
        try {
            $tasks = $this->dashboardService->getTasks($employeeId);
            $this->sendResponse(200, true, 'Tasks retrieved successfully.', ['tasks' => $tasks]);
        } catch (\Throwable $e) {
            $this->sendResponse(400, false, $e->getMessage());
        }
    }

    /**
     * Update Task Status
     */
    public function updateTaskStatus(): void
    {
        try {
            $input = json_decode(file_get_contents('php://input'), true) ?: $_POST;
            $taskId = (int) ($input['task_id'] ?? 0);
            $status = trim($input['status'] ?? 'IN_PROGRESS');
            $pct = isset($input['progress_pct']) ? (int) $input['progress_pct'] : null;

            if ($taskId <= 0) {
                throw new \Exception('Invalid task ID.');
            }

            $success = $this->dashboardService->updateTaskStatus($taskId, $status, $pct);
            $this->sendResponse(200, true, 'Task status updated successfully.', ['success' => $success]);
        } catch (\Throwable $e) {
            $this->sendResponse(400, false, $e->getMessage());
        }
    }

    /**
     * Get Notifications
     */
    public function notifications(?int $employeeId = null, ?int $userId = null): void
    {
        try {
            $notifs = $this->dashboardService->getNotifications($employeeId, $userId);
            $this->sendResponse(200, true, 'Notifications retrieved.', $notifs);
        } catch (\Throwable $e) {
            $this->sendResponse(400, false, $e->getMessage());
        }
    }

    /**
     * Mark Notifications Read
     */
    public function markNotificationsRead(?int $employeeId = null, ?int $userId = null): void
    {
        try {
            $ok = $this->dashboardService->markNotificationsRead($employeeId, $userId);
            $this->sendResponse(200, true, 'Notifications marked as read.', ['success' => $ok]);
        } catch (\Throwable $e) {
            $this->sendResponse(400, false, $e->getMessage());
        }
    }

    /**
     * Send JSON response
     */
    private function sendResponse(
        int $statusCode,
        bool $success,
        string $message,
        array $data = []
    ): void {

        http_response_code($statusCode);

        header('Content-Type: application/json');

        echo json_encode([
            'success' => $success,
            'message' => $message,
            'data' => $data
        ]);
    }
}
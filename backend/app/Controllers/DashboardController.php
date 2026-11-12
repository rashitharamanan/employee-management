<?php

namespace App\Controllers;

use App\Repositories\DashboardRepository;

class DashboardController
{
    public function __construct(private DashboardRepository $dashboardRepository)
    {
    }

    public function index(): void
    {
        $this->respond(200, true, 'Dashboard data retrieved successfully.', [
            'summary' => $this->dashboardRepository->getSummary(),
            'employee_trend' => $this->dashboardRepository->getEmployeeTrend(),
            'employees_by_department' => $this->dashboardRepository->getDepartmentBreakdown(),
            'recent_employees' => $this->dashboardRepository->getRecentEmployees(),
            'recent_activities' => [],
            'activity_status' => 'No activity log is configured.'
        ]);
    }

    private function respond(int $status, bool $success, string $message, array $data): void
    {
        http_response_code($status);
        echo json_encode([
            'success' => $success,
            'message' => $message,
            'data' => $data
        ]);
    }
}

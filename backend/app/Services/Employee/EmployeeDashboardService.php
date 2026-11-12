<?php

namespace App\Services\Employee;

use App\Repositories\Employee\EmployeeDashboardRepository;

class EmployeeDashboardService
{
    public function __construct(
        private EmployeeDashboardRepository $dashboardRepository
    ) {
    }

    /**
     * Get complete employee dashboard
     */
    public function getDashboard(int $employeeId): array
    {
        if ($employeeId <= 0) {
            throw new \Exception('Invalid employee ID.');
        }

        $employee = $this->dashboardRepository->getEmployee($employeeId);

        if (!$employee) {
            throw new \Exception('Employee not found.');
        }

        $yearMonth = date('Y-m');

        return [
            'employee' => [
                'id'              => (int) $employee['id'],
                'employee_code'   => $employee['employee_code'] ?? null,
                'name'            => $employee['full_name'] ?? '',
                'first_name'      => $employee['first_name'] ?? null,
                'last_name'       => $employee['last_name'] ?? null,
                'email'           => $employee['email'] ?? null,
                'department'      => $employee['department'] ?? null,
                'designation'     => $employee['designation'] ?? null,
                'profile_photo'   => $employee['profile_photo'] ?? null,
                'joining_date'    => $employee['joining_date'] ?? null,
                'employment_type' => $employee['employment_type'] ?? null,
                'work_location'   => $employee['work_location'] ?? null,
                'work_shift'      => $employee['work_shift'] ?? null,
                'status'          => $employee['status'] ?? null
            ],

            'attendance' =>
                $this->dashboardRepository
                    ->getTodayAttendance($employeeId),

            'attendance_monthly' =>
                $this->dashboardRepository
                    ->getMonthlyAttendance($employeeId, $yearMonth),

            'leave_balance' =>
                $this->dashboardRepository
                    ->getLeaveBalance($employeeId),

            'performance' =>
                $this->dashboardRepository
                    ->getPerformance($employeeId),

            'training_summary' =>
                $this->dashboardRepository
                    ->getTrainingSummary($employeeId),

            'upcoming_leave' =>
                $this->dashboardRepository
                    ->getUpcomingLeave($employeeId),

            'upcoming_holidays' =>
                $this->dashboardRepository
                    ->getUpcomingHolidays(),

            'goals' =>
                $this->dashboardRepository
                    ->getGoals($employeeId),

            'training' =>
                $this->dashboardRepository
                    ->getTraining($employeeId),

            'recent_activity' =>
                $this->dashboardRepository
                    ->getRecentActivity($employeeId),

            'tasks' =>
                $this->dashboardRepository
                    ->getAssignedTasks($employeeId, $employee['email'] ?? null),

            'notifications' =>
                $this->dashboardRepository
                    ->getNotifications($employeeId, null)
        ];
    }

    public function getTasks(int $employeeId, ?string $email = null): array
    {
        return $this->dashboardRepository->getAssignedTasks($employeeId, $email);
    }

    public function getNotifications(?int $employeeId = null, ?int $userId = null): array
    {
        return $this->dashboardRepository->getNotifications($employeeId, $userId);
    }

    public function markNotificationsRead(?int $employeeId = null, ?int $userId = null): bool
    {
        return $this->dashboardRepository->markNotificationsAsRead($employeeId, $userId);
    }

    public function updateTaskStatus(int $taskId, string $status, ?int $progressPct = null): bool
    {
        return $this->dashboardRepository->updateTaskStatus($taskId, $status, $progressPct);
    }
}
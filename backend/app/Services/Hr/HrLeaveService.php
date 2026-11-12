<?php

namespace App\Services\Hr;

use App\Repositories\Hr\HrLeaveRepository;
use Throwable;

class HrLeaveService
{
    public function __construct(private HrLeaveRepository $repository)
    {
    }

    /**
     * Get the full overview for the leave management dashboard
     */
    public function getLeaveOverview(array $filters): array
    {
        try {
            $this->repository->ensureSeedData();
            $month = (string) ($filters['month'] ?? date('Y-m'));

            return [
                'summary' => $this->repository->getSummary($filters),
                'calendar' => $this->repository->getCalendarEvents($month),
                'leave_balances' => $this->repository->getBalanceOverview(),
                'leave_insights' => $this->repository->getMonthlyInsights(),
                'donut_chart' => $this->repository->getLeaveSummaryByType(),
                'status_bars' => $this->repository->getLeaveByStatus(),
                'upcoming_leaves' => $this->repository->getUpcomingLeaves(5),
                'filters' => $this->repository->getFilterOptions(),
                'requests_data' => $this->repository->search($filters),
            ];
        } catch (Throwable $e) {
            error_log('Error in HrLeaveService::getLeaveOverview - ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Approve a leave request
     */
    public function approveRequest(int $id, int $approvedBy): bool
    {
        if ($id <= 0) {
            return false;
        }
        return $this->repository->approve($id, $approvedBy);
    }

    /**
     * Reject a leave request
     */
    public function rejectRequest(int $id, int $approvedBy, string $reason): bool
    {
        if ($id <= 0 || empty(trim($reason))) {
            return false;
        }
        return $this->repository->reject($id, $approvedBy, trim($reason));
    }
}

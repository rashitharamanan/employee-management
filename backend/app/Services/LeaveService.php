<?php

namespace App\Services;

use App\Repositories\Leave\LeaveRepository;
use DateTime;
use Exception;

class LeaveService
{
    public function __construct(private LeaveRepository $leaveRepository)
    {
    }

    public function getLeaveTypes(): array
    {
        return $this->leaveRepository->getLeaveTypes();
    }

    public function getLeaveType(int $id): array
    {
        if ($id <= 0) {
            throw new Exception('Invalid leave type ID.');
        }

        $leaveType = $this->leaveRepository->findLeaveTypeById($id);
        if (!$leaveType) {
            throw new Exception('Leave type not found.');
        }

        return $leaveType;
    }

    public function createLeaveType(array $data): array
    {
        $name = trim((string) ($data['name'] ?? ''));
        if ($name === '') {
            throw new Exception('Leave type name is required.');
        }

        $annualDays = (float) ($data['annual_days'] ?? 0);
        if ($annualDays <= 0) {
            throw new Exception('Annual leave days must be greater than zero.');
        }

        $status = strtoupper((string) ($data['status'] ?? 'ACTIVE'));
        if (!in_array($status, ['ACTIVE', 'INACTIVE'])) {
            $status = 'ACTIVE';
        }

        $id = $this->leaveRepository->createLeaveType([
            'name' => $name,
            'description' => $data['description'] ?? '',
            'annual_days' => $annualDays,
            'is_paid' => $data['is_paid'] ?? true,
            'status' => $status,
            'organisation_id' => $data['organisation_id'] ?? 1,
        ]);

        return $this->getLeaveType($id);
    }

    public function updateLeaveType(int $id, array $data): array
    {
        $existing = $this->getLeaveType($id);

        $name = trim((string) ($data['name'] ?? $existing['name']));
        if ($name === '') {
            throw new Exception('Leave type name is required.');
        }

        $annualDays = (float) ($data['annual_days'] ?? $existing['annual_days']);
        if ($annualDays <= 0) {
            throw new Exception('Annual leave days must be greater than zero.');
        }

        $status = strtoupper((string) ($data['status'] ?? $existing['status']));
        if (!in_array($status, ['ACTIVE', 'INACTIVE'])) {
            $status = $existing['status'];
        }

        $updated = $this->leaveRepository->updateLeaveType($id, [
            'name' => $name,
            'description' => $data['description'] ?? $existing['description'],
            'annual_days' => $annualDays,
            'is_paid' => $data['is_paid'] ?? $existing['is_paid'],
            'status' => $status,
        ]);

        if (!$updated) {
            throw new Exception('Failed to update leave type.');
        }

        return $this->getLeaveType($id);
    }

    public function getLeaveRequests(array $filters = []): array
    {
        return $this->leaveRepository->getLeaveRequests($filters);
    }

    public function getLeaveRequest(int $id): array
    {
        if ($id <= 0) {
            throw new Exception('Invalid leave request ID.');
        }

        $request = $this->leaveRepository->findLeaveRequestById($id);
        if (!$request) {
            throw new Exception('Leave request not found.');
        }

        return $request;
    }

    public function createLeaveRequest(array $data): array
    {
        $employeeId = (int) ($data['employee_id'] ?? 0);
        $leaveTypeId = (int) ($data['leave_type_id'] ?? 0);

        if ($employeeId <= 0) {
            throw new Exception('Employee is required.');
        }

        if ($leaveTypeId <= 0) {
            throw new Exception('Leave type is required.');
        }

        $leaveType = $this->getLeaveType($leaveTypeId);

        $fromDate = (string) ($data['from_date'] ?? '');
        $toDate = (string) ($data['to_date'] ?? '');
        $days = $this->calculateDays($fromDate, $toDate);

        if ($days <= 0) {
            throw new Exception('Valid leave dates are required.');
        }

        if (trim((string) ($data['reason'] ?? '')) === '') {
            throw new Exception('Reason for leave is required.');
        }

        $id = $this->leaveRepository->createLeaveRequest([
            'employee_id' => $employeeId,
            'leave_type_id' => $leaveTypeId,
            'from_date' => $fromDate,
            'to_date' => $toDate,
            'total_days' => $days,
            'reason' => $data['reason'],
            'attachment' => $data['attachment'] ?? '',
        ]);

        $this->ensureLeaveBalanceEntry($employeeId, $leaveTypeId, $leaveType['annual_days']);

        return $this->getLeaveRequest($id);
    }

    public function approveLeaveRequest(int $requestId, int $approvedBy): array
    {
        $request = $this->getLeaveRequest($requestId);

        if (!in_array($request['status'], ['PENDING'])) {
            throw new Exception('Only pending leave requests can be approved.');
        }

        $user = $GLOBALS['authenticated_user'] ?? null;
        $approverRole = strtoupper((string) ($user->role ?? ''));
        $empDept = strtoupper((string) ($request['employee']['department'] ?? $request['department'] ?? ''));
        $empDesig = strtoupper((string) ($request['employee']['designation'] ?? $request['designation'] ?? ''));
        $isHrOrManager = ($empDept === 'HR' || str_contains($empDesig, 'HR') || str_contains($empDesig, 'MANAGER'));

        if ($isHrOrManager && $approverRole === 'HR') {
            throw new Exception('Leave requests from HR and Managers can only be approved by the Admin.');
        }

        $updated = $this->leaveRepository->approveLeaveRequest($requestId, $approvedBy);
        if (!$updated) {
            throw new Exception('Unable to approve leave request.');
        }

        $this->consumeLeaveBalance($request['employee_id'], $request['leave_type_id'], $request['total_days']);

        return $this->getLeaveRequest($requestId);
    }

    public function rejectLeaveRequest(int $requestId, int $approvedBy, string $reason): array
    {
        $request = $this->getLeaveRequest($requestId);

        if (!in_array($request['status'], ['PENDING'])) {
            throw new Exception('Only pending leave requests can be rejected.');
        }

        $user = $GLOBALS['authenticated_user'] ?? null;
        $approverRole = strtoupper((string) ($user->role ?? ''));
        $empDept = strtoupper((string) ($request['employee']['department'] ?? $request['department'] ?? ''));
        $empDesig = strtoupper((string) ($request['employee']['designation'] ?? $request['designation'] ?? ''));
        $isHrOrManager = ($empDept === 'HR' || str_contains($empDesig, 'HR') || str_contains($empDesig, 'MANAGER'));

        if ($isHrOrManager && $approverRole === 'HR') {
            throw new Exception('Leave requests from HR and Managers can only be rejected by the Admin.');
        }

        $reason = trim($reason);
        if ($reason === '') {
            throw new Exception('A rejection reason is required.');
        }

        $updated = $this->leaveRepository->rejectLeaveRequest($requestId, $approvedBy, $reason);
        if (!$updated) {
            throw new Exception('Unable to reject leave request.');
        }

        return $this->getLeaveRequest($requestId);
    }

    public function getEmployeeBalances(int $employeeId): array
    {
        if ($employeeId <= 0) {
            throw new Exception('Invalid employee ID.');
        }

        $balances = $this->leaveRepository->getEmployeeBalances($employeeId);
        if (empty($balances)) {
            $types = $this->leaveRepository->getLeaveTypes();
            foreach ($types as $type) {
                $this->ensureLeaveBalanceEntry($employeeId, $type['id'], $type['annual_days']);
            }
            $balances = $this->leaveRepository->getEmployeeBalances($employeeId);
        }

        return $balances;
    }

    public function getDashboardSummary(): array
    {
        return $this->leaveRepository->getDashboardSummary();
    }

    private function calculateDays(string $fromDate, string $toDate): float
    {
        $from = DateTime::createFromFormat('Y-m-d', $fromDate);
        $to = DateTime::createFromFormat('Y-m-d', $toDate);

        if (!$from || !$to || $from > $to) {
            return 0.0;
        }

        $interval = $from->diff($to);
        $days = (int) $interval->days + 1;
        return (float) $days;
    }

    private function ensureLeaveBalanceEntry(int $employeeId, int $leaveTypeId, float $annualDays): void
    {
        $year = (int) date('Y');
        $existing = $this->leaveRepository->getLeaveBalanceForEmployeeAndType($employeeId, $leaveTypeId, $year);

        if ($existing) {
            return;
        }

        $this->leaveRepository->createOrUpdateLeaveBalance($employeeId, $leaveTypeId, $year, $annualDays);
    }

    private function consumeLeaveBalance(int $employeeId, int $leaveTypeId, float $days): void
    {
        $this->leaveRepository->consumeLeaveBalance($employeeId, $leaveTypeId, $days);
    }
}

<?php

namespace App\Services\Hr;

use App\Repositories\Hr\PayrollRepository;
use Throwable;

class PayrollService
{
    public function __construct(private PayrollRepository $repository)
    {
    }

    /**
     * Payroll register for a month. Uses the persisted run when one exists,
     * otherwise computes a live draft from the employee master records.
     */
    public function getRegister(array $filters): array
    {
        try {
            $month = $this->validMonth((string) ($filters['month'] ?? ''));
            $search = trim((string) ($filters['search'] ?? ''));
            $department = trim((string) ($filters['department'] ?? ''));
            $statusFilter = strtoupper(trim((string) ($filters['status'] ?? '')));
            $page = max(1, (int) ($filters['page'] ?? 1));
            $perPage = min(50, max(5, (int) ($filters['per_page'] ?? 10)));

            $records = $this->repository->hasRun($month)
                ? $this->repository->getMonthRecords($month)
                : $this->buildDraft($month);

            $filtered = array_values(array_filter(
                $records,
                static function (array $record) use ($search, $department, $statusFilter): bool {
                    if ($search !== '' && stripos(($record['full_name'] ?? '') . ' ' . ($record['employee_code'] ?? '') . ' ' . ($record['designation'] ?? ''), $search) === false) {
                        return false;
                    }
                    if ($department !== '' && ($record['department'] ?? '') !== $department) {
                        return false;
                    }
                    if ($statusFilter !== '' && strtoupper((string) ($record['status'] ?? '')) !== $statusFilter) {
                        return false;
                    }
                    return true;
                }
            ));

            $total = count($filtered);
            $totalPages = $total ? (int) ceil($total / $perPage) : 0;

            return [
                'month' => $month,
                'month_label' => date('F Y', strtotime($month . '-01')),
                'summary' => $this->buildSummary($records, $month),
                'filters' => ['departments' => $this->repository->getDepartments()],
                'employees' => $total ? array_slice($filtered, ($page - 1) * $perPage, $perPage) : [],
                'pagination' => [
                    'current_page' => $page,
                    'per_page' => $perPage,
                    'total_items' => $total,
                    'total_pages' => $totalPages,
                ],
                'department_distribution' => $this->buildDistribution($records),
                'recent_payments' => $this->buildRecentPayments($records),
            ];
        } catch (Throwable $e) {
            error_log('PayrollService::getRegister - ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Compute and persist the payroll run for a month.
     */
    public function runPayroll(string $month): array
    {
        $month = $this->validMonth($month);
        $records = $this->buildDraft($month);
        $saved = $this->repository->saveRun($month, $records);

        return [
            'month' => $month,
            'month_label' => date('F Y', strtotime($month . '-01')),
            'records_processed' => count($records),
            'records_saved' => $saved,
            'summary' => $this->buildSummary($records, $month),
        ];
    }

    /**
     * Fetch a single persisted payslip.
     */
    public function getPayslip(int $id): ?array
    {
        if ($id <= 0) {
            return null;
        }
        return $this->repository->findPayslip($id);
    }

    /**
     * Update a payroll record status.
     */
    public function updateStatus(int $id, string $status, ?string $month = null, int $employeeId = 0): bool
    {
        $status = strtoupper(trim($status));
        if (!in_array($status, ['PENDING', 'PAID', 'VOID'], true)) {
            return false;
        }

        $month = $this->validMonth((string) ($month ?? ''));
        if (!$this->repository->hasRun($month)) {
            $this->runPayroll($month);
        }

        return $this->repository->updateStatusByMonth($id, $status, $month, $employeeId);
    }

    private function validMonth(string $value): string
    {
        if ($value !== '' && preg_match('/^\d{4}-(0[1-9]|1[0-2])$/', $value) === 1) {
            return $value;
        }
        return date('Y-m');
    }

    private function buildDraft(string $month): array
    {
        $employees = $this->repository->getEmployees();
        $now = date('Y-m');
        $records = [];
        foreach ($employees as $row) {
            $record = $this->buildRecord($row, $month, $now);
            if ($record !== null) {
                $records[] = $record;
            }
        }
        return $records;
    }

    private function buildRecord(array $row, string $month, string $now): ?array
    {
        $joinDate = (string) ($row['joining_date'] ?? '');
        $monthEnd = date('Y-m-t', strtotime($month . '-01'));

        // Employees who joined after the month's pay date are not part of that run.
        if ($joinDate !== '' && $joinDate > $monthEnd) {
            return null;
        }

        $basic = (float) ($row['basic_salary'] ?? 0);
        $hasSalary = $basic > 0;

        $hra = round($basic * 0.30, 2);
        $specialAllowance = round($basic * 0.12, 2);
        $allowances = round($hra + $specialAllowance, 2);
        $gross = round($basic + $allowances, 2);

        $pf = $hasSalary ? round(min($basic * 0.12, 1800), 2) : 0;
        $professionalTax = $hasSalary ? 200 : 0;
        $incomeTax = $hasSalary ? round(max($gross * 0.05, 0), 2) : 0;
        $deductions = round($pf + $professionalTax + $incomeTax, 2);
        $net = round($gross - $deductions, 2);

        $paid = $hasSalary && $month < $now;

        return [
            'id' => (int) $row['id'],
            'employee_id' => (int) $row['id'],
            'employee_code' => $row['employee_code'] ?? '',
            'full_name' => $row['full_name'] ?? '',
            'profile_photo' => $row['profile_photo'] ?? null,
            'department' => $row['department'] ?? 'Unassigned',
            'designation' => $row['designation'] ?? 'Unassigned',
            'joining_date' => $joinDate,
            'employment_type' => $row['employment_type'] ?? 'Full-time',
            'basic_salary' => $basic,
            'hra' => $hra,
            'special_allowance' => $specialAllowance,
            'allowances' => $allowances,
            'gross_pay' => $gross,
            'pf' => $pf,
            'professional_tax' => $professionalTax,
            'income_tax' => $incomeTax,
            'deductions' => $deductions,
            'net_pay' => $net,
            'status' => $paid ? 'PAID' : 'PENDING',
            'paid_on' => $paid ? $monthEnd : null,
            'salary_pending' => !$hasSalary,
        ];
    }

    private function buildSummary(array $records, string $month): array
    {
        $eligible = count($records);
        $paid = 0;
        $gross = 0.0;
        $allowances = 0.0;
        $deductions = 0.0;
        $net = 0.0;

        foreach ($records as $record) {
            if (($record['status'] ?? '') === 'PAID') {
                $paid++;
            }
            $gross += (float) $record['gross_pay'];
            $allowances += (float) $record['allowances'];
            $deductions += (float) $record['deductions'];
            $net += (float) $record['net_pay'];
        }

        return [
            'month' => $month,
            'month_label' => date('F Y', strtotime($month . '-01')),
            'total_employees' => $eligible,
            'paid_employees' => $paid,
            'pending_employees' => $eligible - $paid,
            'total_payroll' => round($net, 2),
            'gross_payroll' => round($gross, 2),
            'total_allowances' => round($allowances, 2),
            'total_deductions' => round($deductions, 2),
            'average_net_pay' => $eligible ? round($net / $eligible, 2) : 0,
        ];
    }

    private function buildDistribution(array $records): array
    {
        $groups = [];
        $totalNet = array_sum(array_map(static fn (array $r): float => (float) ($r['net_pay'] ?? 0), $records));

        foreach ($records as $record) {
            $dept = $record['department'] ?: 'Unassigned';
            if (!isset($groups[$dept])) {
                $groups[$dept] = ['department' => $dept, 'count' => 0, 'total' => 0.0];
            }
            $groups[$dept]['count']++;
            $groups[$dept]['total'] += (float) $record['net_pay'];
        }

        $distribution = [];
        foreach ($groups as $dept => $group) {
            $distribution[] = [
                'department' => $dept,
                'count' => $group['count'],
                'total' => round($group['total'], 2),
                'percentage' => $totalNet > 0 ? round(($group['total'] / $totalNet) * 100, 1) : 0,
            ];
        }
        usort($distribution, static fn (array $a, array $b): int => $b['total'] <=> $a['total']);

        return $distribution;
    }

    private function buildRecentPayments(array $records): array
    {
        $payments = array_values(array_filter($records, static fn (array $record): bool => $record['status'] === 'PAID'));
        usort($payments, static fn (array $a, array $b): int => (float) $b['net_pay'] <=> (float) $a['net_pay']);

        return array_map(static fn (array $record): array => [
            'full_name' => $record['full_name'],
            'profile_photo' => $record['profile_photo'] ?? null,
            'designation' => $record['designation'],
            'department' => $record['department'],
            'net_pay' => $record['net_pay'],
            'paid_on' => $record['paid_on'],
        ], array_slice($payments, 0, 6));
    }
    }
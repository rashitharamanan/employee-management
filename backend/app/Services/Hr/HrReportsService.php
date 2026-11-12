<?php

namespace App\Services\Hr;

use App\Repositories\Hr\HrReportsRepository;
use DateTimeImmutable;
use InvalidArgumentException;

class HrReportsService
{
    private const EXPORT_TYPES = [
        'employees' => ['label' => 'Employee Master', 'category' => 'Employment'],
        'attendance' => ['label' => 'Attendance Log', 'category' => 'Attendance'],
        'leave' => ['label' => 'Leave Records', 'category' => 'Leave'],
        'payroll' => ['label' => 'Payroll Register', 'category' => 'Payroll'],
        'training' => ['label' => 'Training Enrollments', 'category' => 'Training'],
        'performance' => ['label' => 'Performance Reviews', 'category' => 'Performance'],
        'recruitment' => ['label' => 'Recruitment Pipeline', 'category' => 'Recruitment'],
    ];

    public function __construct(private HrReportsRepository $repository)
    {
    }

    public function getDashboard(array $filters): array
    {
        [$from, $to] = $this->resolveRange($filters['from'] ?? null, $filters['to'] ?? null);
        $year = $this->resolveYear($filters['year'] ?? null);
        $monthStart = (new DateTimeImmutable($to))->format('Y-m') . '-01';
        $yearStart = $year . '-01-01';
        $yearPrefix = $year . '%';

        $employees = $this->repository->getEmployeeStats($monthStart, $yearStart);
        $attendance = $this->repository->getAttendanceTotals($from, $to);
        $records = (int) ($attendance['total_records'] ?? 0);
        $present = (int) ($attendance['present'] ?? 0);

        $training = $this->repository->getTrainingTotals();
        $enrollments = max(1, (int) $training['total_enrollments']);
        $payroll = $this->repository->getPayrollTotals($yearPrefix);
        $recruitment = $this->repository->getRecruitmentSummary();
        $totalEmployees = max(1, (int) $employees['total_employees']);

        return [
            'range' => ['from' => $from, 'to' => $to, 'year' => $year],
            'summary' => [
                'total_employees' => (int) $employees['total_employees'],
                'active_employees' => (int) $employees['active_employees'],
                'new_hires_month' => (int) $employees['new_hires_month'],
                'exits_month' => (int) $employees['exits_month'],
                'attrition_rate' => round((int) $employees['exits_year'] / $totalEmployees * 100, 1),
                'attendance_rate' => $records > 0 ? round($present / $records * 100, 1) : 0,
                'attendance_records' => $records,
                'pending_leave_requests' => $this->repository->countPendingLeaveRequests(),
                'avg_performance_rating' => $this->repository->getAverageRating(),
                'training_completion_rate' => round((int) $training['completed_enrollments'] / $enrollments * 100, 1),
                'avg_training_score' => (float) $training['avg_training_score'],
                'total_programs' => (int) $training['total_programs'],
                'payroll_cost_ytd' => round((float) $payroll['total_net_pay'], 2),
                'payroll_pending_runs' => (int) $payroll['pending_runs'],
                'open_positions' => $recruitment['open_positions'],
                'total_reports' => $this->repository->countReports(),
            ],
            'headcount_trend' => $this->buildHeadcountTrend($from, $to),
            'employees_by_department' => $this->repository->getDepartmentHeadcount(),
            'attendance_overview' => [
                'present' => (int) ($attendance['present'] ?? 0),
                'absent' => (int) ($attendance['absent'] ?? 0),
                'late' => (int) ($attendance['late'] ?? 0),
                'on_leave' => (int) ($attendance['on_leave'] ?? 0),
                'rate' => $records > 0 ? round($present / $records * 100, 1) : 0,
            ],
            'attendance_trend' => $this->repository->getAttendanceTrend(14),
            'leave_analysis' => [
                'by_type' => $this->repository->getLeaveByType($yearPrefix),
                'by_status' => $this->repository->getLeaveByStatus($yearPrefix),
                'by_month' => $this->repository->getLeaveByMonth($yearPrefix),
            ],
            'recruitment_funnel' => [
                'summary' => $recruitment,
                'stages' => $this->repository->getCandidatesByStage(),
            ],
            'training_performance' => [
                'enrollments_by_status' => $this->repository->getEnrollmentsByStatus(),
                'programs_by_status' => $this->repository->getProgramsByStatus(),
                'avg_score_by_category' => $this->repository->getAverageScoreByCategory(),
                'performance_by_department' => $this->repository->getPerformanceByDepartment(),
                'goals_by_status' => $this->repository->getGoalsByStatus(),
                'top_performers' => $this->repository->getTopPerformers(5),
                'payroll_by_month' => $this->repository->getPayrollByMonth($yearPrefix),
            ],
            'recent_reports' => $this->repository->getRecentReports(10),
        ];
    }

    /*
    | -------------------------------------------------------------------------
    | Headcount Trend (cumulative, computed in PHP for portability)
    | -------------------------------------------------------------------------
    */

    private function buildHeadcountTrend(string $from, string $to): array
    {
        $hires = $this->repository->getHiresByMonth($from, $to);
        $exits = $this->repository->getExitsByMonth($from, $to);
        $headcount = $this->repository->countHeadcountBefore(
            (new DateTimeImmutable($from))->format('Y-m') . '-01'
        );

        $trend = [];
        foreach ($this->monthList($from, $to) as $month) {
            $monthHires = (int) ($hires[$month] ?? 0);
            $monthExits = (int) ($exits[$month] ?? 0);
            $headcount += $monthHires - $monthExits;

            $trend[] = [
                'month' => $month,
                'hires' => $monthHires,
                'exits' => $monthExits,
                'headcount' => max(0, $headcount),
            ];
        }

        return $trend;
    }

    private function monthList(string $from, string $to): array
    {
        $cursor = (new DateTimeImmutable($from))->modify('first day of this month');
        $end = (new DateTimeImmutable($to))->modify('first day of this month');
        $months = [];

        while ($cursor <= $end && count($months) < 36) {
            $months[] = $cursor->format('Y-m');
            $cursor = $cursor->modify('first day of next month');
        }

        return $months ?: [(new DateTimeImmutable())->format('Y-m')];
    }

    /*
    | -------------------------------------------------------------------------
    | Report Generation
    | -------------------------------------------------------------------------
    */

    public function generateReport(array $input): int
    {
        $name = trim((string) ($input['report_name'] ?? ''));
        if ($name === '') {
            throw new InvalidArgumentException('Report name is required.');
        }
        if (mb_strlen($name) > 160) {
            throw new InvalidArgumentException('Report name must be 160 characters or fewer.');
        }

        $format = strtoupper(trim((string) ($input['report_format'] ?? 'PDF')));
        if (!in_array($format, ['PDF', 'EXCEL', 'CSV'], true)) {
            throw new InvalidArgumentException('Report format must be PDF, EXCEL, or CSV.');
        }

        $category = trim((string) ($input['category'] ?? 'General'));

        return $this->repository->createReport([
            'report_name' => $name,
            'category' => $category !== '' ? $category : 'General',
            'generated_by' => $input['generated_by'] ?? 'System',
            'report_format' => $format,
            'date_from' => $this->dateOrNull($input['date_from'] ?? null),
            'date_to' => $this->dateOrNull($input['date_to'] ?? null),
        ]);
    }

    public function getRecentReports(int $limit = 10): array
    {
        return $this->repository->getRecentReports($limit);
    }

    /*
    | -------------------------------------------------------------------------
    | CSV Export
    | -------------------------------------------------------------------------
    */

    public function exportCsv(string $type, ?string $from, ?string $to, string $generatedBy): array
    {
        $type = strtolower(trim($type));
        if (!isset(self::EXPORT_TYPES[$type])) {
            throw new InvalidArgumentException(
                'Unsupported report type. Use one of: ' . implode(', ', array_keys(self::EXPORT_TYPES)) . '.'
            );
        }

        [$from, $to] = $this->resolveRange($from, $to);
        $meta = self::EXPORT_TYPES[$type];
        $rows = $this->datasetRows($type, $from, $to);

        $filename = str_replace(' ', '_', strtolower($meta['label']))
            . '_' . $from . '_to_' . $to . '.csv';

        // Log the export in generated_reports so it appears under Recent Reports.
        $this->repository->createReport([
            'report_name' => $meta['label'] . ' (' . $from . ' → ' . $to . ')',
            'category' => $meta['category'],
            'generated_by' => $generatedBy,
            'report_format' => 'CSV',
            'date_from' => $from,
            'date_to' => $to,
        ]);

        return ['filename' => $filename, 'rows' => $rows, 'label' => $meta['label']];
    }

    public function exportTypes(): array
    {
        return array_map(
            static fn (string $key, array $meta): array => ['key' => $key, 'label' => $meta['label'], 'category' => $meta['category']],
            array_keys(self::EXPORT_TYPES),
            self::EXPORT_TYPES
        );
    }

    private function datasetRows(string $type, string $from, string $to): array
    {
        return match ($type) {
            'employees' => $this->repository->getEmployeeExportRows(),
            'attendance' => $this->repository->getAttendanceExportRows($from, $to),
            'leave' => $this->repository->getLeaveExportRows($from, $to),
            'payroll' => $this->repository->getPayrollExportRows(
                (new DateTimeImmutable($from))->format('Y-m'),
                (new DateTimeImmutable($to))->format('Y-m')
            ),
            'training' => $this->repository->getTrainingExportRows($from, $to),
            'performance' => $this->repository->getPerformanceExportRows($from, $to),
            'recruitment' => $this->repository->getRecruitmentExportRows($from, $to),
            default => [],
        };
    }

    /*
    | -------------------------------------------------------------------------
    | Helpers
    | -------------------------------------------------------------------------
    */

    /**
     * @return array{0: string, 1: string} [from, to] as Y-m-d strings.
     */
    private function resolveRange(?string $from, ?string $to): array
    {
        $today = new DateTimeImmutable();

        if ($from !== null && $from !== '') {
            $from = $this->validateDate($from, 'from');
        } else {
            $from = $today->modify('first day of this month')->format('Y-m-d');
        }

        if ($to !== null && $to !== '') {
            $to = $this->validateDate($to, 'to');
        } else {
            $to = $today->format('Y-m-d');
        }

        if ($from > $to) {
            [$from, $to] = [$to, $from];
        }

        return [$from, $to];
    }

    private function resolveYear(?string $year): string
    {
        if ($year === null || $year === '') {
            return (new DateTimeImmutable())->format('Y');
        }

        if (!preg_match('/^\d{4}$/', $year)) {
            throw new InvalidArgumentException('Year must be a four-digit value (e.g. 2026).');
        }

        return $year;
    }

    private function validateDate(string $value, string $field): string
    {
        $date = DateTimeImmutable::createFromFormat('Y-m-d', $value);
        $errors = DateTimeImmutable::getLastErrors();

        if (!$date || $date->format('Y-m-d') !== $value || ($errors && ($errors['warning_count'] || $errors['error_count']))) {
            throw new InvalidArgumentException(ucfirst($field) . ' date must use YYYY-MM-DD format.');
        }

        return $value;
    }

    private function dateOrNull(mixed $value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        return $this->validateDate((string) $value, 'date');
    }
}

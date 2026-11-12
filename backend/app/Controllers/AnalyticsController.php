<?php

namespace App\Controllers;

use App\Repositories\AnalyticsRepository;

class AnalyticsController
{
    public function __construct(private AnalyticsRepository $analyticsRepository)
    {
    }

    public function dashboard(): void
    {
        $from = $_GET['from'] ?? null;
        $to = $_GET['to'] ?? null;
        $summary = $this->analyticsRepository->getSummary($from, $to);

        $this->respond(200, true, 'Analytics dashboard retrieved successfully.', [
            'summary' => $summary,
            'employee_trend' => $this->analyticsRepository->getEmployeeTrend($summary['date_from'], $summary['date_to']),
            'employees_by_department' => $this->analyticsRepository->getDepartmentDistribution(),
            'attendance_overview' => [
                'present' => $summary['present'],
                'absent' => $summary['absent'],
                'late' => $summary['late']
            ],
            'recent_reports' => $this->analyticsRepository->getRecentReports()
        ]);
    }

    public function reports(): void
    {
        $this->respond(200, true, 'Reports retrieved successfully.', [
            'reports' => $this->analyticsRepository->getRecentReports(50)
        ]);
    }

    public function generateReport(): void
    {
        $input = json_decode(file_get_contents('php://input'), true);
        $input = is_array($input) ? $input : [];
        $name = trim((string) ($input['report_name'] ?? ''));
        $format = strtoupper(trim((string) ($input['report_format'] ?? 'PDF')));

        if ($name === '') {
            $this->respond(400, false, 'Report name is required.');
            return;
        }
        if (!in_array($format, ['PDF', 'EXCEL', 'CSV'], true)) {
            $this->respond(400, false, 'Report format must be PDF, EXCEL, or CSV.');
            return;
        }

        $user = $GLOBALS['authenticated_user'] ?? null;
        $id = $this->analyticsRepository->createReport([
            'report_name' => $name,
            'category' => trim((string) ($input['category'] ?? 'General')) ?: 'General',
            'generated_by' => (string) ($user->email ?? 'System'),
            'report_format' => $format,
            'date_from' => $this->dateOrNull($input['date_from'] ?? null),
            'date_to' => $this->dateOrNull($input['date_to'] ?? null)
        ]);

        $this->respond(201, true, 'Report generated successfully.', ['id' => $id]);
    }

    private function dateOrNull(mixed $value): ?string
    {
        if (!$value) {
            return null;
        }
        $date = \DateTime::createFromFormat('Y-m-d', (string) $value);
        if (!$date || $date->format('Y-m-d') !== $value) {
            throw new \InvalidArgumentException('Dates must use YYYY-MM-DD format.');
        }
        return $value;
    }

    private function respond(int $status, bool $success, string $message, array $data = []): void
    {
        http_response_code($status);
        echo json_encode(['success' => $success, 'message' => $message, 'data' => $data]);
    }
}

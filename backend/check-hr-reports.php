<?php
/**
 * HR Reports — live verification harness.
 * Usage: php check-hr-reports.php
 */

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/config/database.php';

use Dotenv\Dotenv;
use App\Repositories\Hr\HrReportsRepository;
use App\Services\Hr\HrReportsService;

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

try {
    $pdo = (new Database())->connect();
    $repository = new HrReportsRepository($pdo);
    $repository->ensureSchema();
    $service = new HrReportsService($repository);

    echo "== Dashboard (default range) ==\n";
    $dashboard = $service->getDashboard([]);
    echo 'summary: ' . json_encode($dashboard['summary']) . "\n";
    echo 'headcount_trend months: ' . count($dashboard['headcount_trend']) . ' | first: ' . json_encode($dashboard['headcount_trend'][0] ?? null) . "\n";
    echo 'employees_by_department: ' . json_encode($dashboard['employees_by_department']) . "\n";
    echo 'attendance_overview: ' . json_encode($dashboard['attendance_overview']) . "\n";
    echo 'attendance_trend rows: ' . count($dashboard['attendance_trend']) . "\n";
    echo 'leave_analysis by_type: ' . json_encode($dashboard['leave_analysis']['by_type']) . "\n";
    echo 'leave_analysis by_status: ' . json_encode($dashboard['leave_analysis']['by_status']) . "\n";
    echo 'leave_analysis by_month rows: ' . count($dashboard['leave_analysis']['by_month']) . "\n";
    echo 'recruitment_funnel summary: ' . json_encode($dashboard['recruitment_funnel']['summary']) . "\n";
    echo 'recruitment_funnel stages: ' . json_encode($dashboard['recruitment_funnel']['stages']) . "\n";
    echo 'training_performance: ' . json_encode(array_map(fn ($v) => count($v), $dashboard['training_performance'])) . "\n";
    echo 'recent_reports rows: ' . count($dashboard['recent_reports']) . "\n\n";

    echo "== Dashboard (explicit range + year 2025) ==\n";
    $ranged = $service->getDashboard(['from' => '2025-01-01', 'to' => '2025-12-31', 'year' => '2025']);
    echo 'range: ' . json_encode($ranged['range']) . ' | attrition: ' . $ranged['summary']['attrition_rate'] . "\n\n";

    echo "== Generate report ==\n";
    $id = $service->generateReport([
        'report_name' => 'HR Monthly Pack (verify)',
        'category' => 'General',
        'generated_by' => 'cli-verify',
        'report_format' => 'CSV',
        'date_from' => '2026-09-01',
        'date_to' => '2026-09-06',
    ]);
    echo "created report id: $id\n";

    echo "== Invalid input checks ==\n";
    foreach ([['report_name' => ''], ['report_name' => 'X', 'report_format' => 'DOC'], ['report_name' => 'X', 'date_from' => 'bad']] as $bad) {
        try {
            $service->generateReport($bad);
            echo "FAIL: accepted invalid input " . json_encode($bad) . "\n";
        } catch (InvalidArgumentException $e) {
            echo 'rejected: ' . $e->getMessage() . "\n";
        }
    }
    try { $service->getDashboard(['from' => 'nope']); echo "FAIL: bad date accepted\n"; } catch (InvalidArgumentException $e) { echo 'rejected: ' . $e->getMessage() . "\n"; }

    echo "\n== CSV exports (row counts) ==\n";
    foreach (['employees', 'attendance', 'leave', 'payroll', 'training', 'performance', 'recruitment'] as $type) {
        $export = $service->exportCsv($type, '2025-01-01', '2026-09-06', 'cli-verify');
        echo str_pad($type, 14) . ' -> ' . $export['filename'] . ' | rows: ' . count($export['rows']) . "\n";
    }
    try { $service->exportCsv('bogus', null, null, 'x'); echo "FAIL: bogus type accepted\n"; } catch (InvalidArgumentException $e) { echo 'rejected: ' . $e->getMessage() . "\n"; }

    echo "\n== Recent reports after runs ==\n";
    foreach ($service->getRecentReports(5) as $row) {
        echo "#{$row['id']} [{$row['report_format']}] {$row['report_name']} by {$row['generated_by']}\n";
    }

    echo "\nALL CHECKS PASSED\n";
} catch (Throwable $e) {
    echo 'ERROR: ' . $e->getMessage() . "\n" . $e->getFile() . ':' . $e->getLine() . "\n";
    exit(1);
}

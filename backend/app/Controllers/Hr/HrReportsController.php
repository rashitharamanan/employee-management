<?php

namespace App\Controllers\Hr;

use App\Services\Hr\HrReportsService;
use InvalidArgumentException;
use Throwable;

class HrReportsController
{
    public function __construct(private HrReportsService $service)
    {
    }

    /*
    | GET /api/hr/reports  — KPIs, charts, recent reports
    */
    public function dashboard(): void
    {
        try {
            $filters = [
                'from' => $_GET['from'] ?? null,
                'to' => $_GET['to'] ?? null,
                'year' => $_GET['year'] ?? null,
            ];

            $this->sendResponse(200, true, 'HR reports dashboard retrieved successfully.', $this->service->getDashboard($filters));
        } catch (InvalidArgumentException $exception) {
            $this->sendResponse(400, false, $exception->getMessage());
        } catch (Throwable $exception) {
            $this->sendResponse(500, false, 'Unable to load HR reports dashboard data.');
        }
    }

    /*
    | GET /api/hr/reports/list — recent generated reports
    */
    public function reports(): void
    {
        try {
            $limit = max(1, min((int) ($_GET['limit'] ?? 10), 100));

            $this->sendResponse(200, true, 'Recent reports retrieved successfully.', [
                'reports' => $this->service->getRecentReports($limit),
                'export_types' => $this->service->exportTypes(),
            ]);
        } catch (Throwable $exception) {
            $this->sendResponse(500, false, 'Unable to load recent reports.');
        }
    }

    /*
    | POST /api/hr/reports/generate — register a generated report
    */
    public function generate(): void
    {
        try {
            $input = json_decode(file_get_contents('php://input'), true);
            $input = is_array($input) ? $input : [];
            $input['generated_by'] = $this->authenticatedUserEmail();

            $id = $this->service->generateReport($input);

            $this->sendResponse(201, true, 'Report generated successfully.', ['id' => $id]);
        } catch (InvalidArgumentException $exception) {
            $this->sendResponse(400, false, $exception->getMessage());
        } catch (Throwable $exception) {
            $this->sendResponse(500, false, 'Unable to generate the report.');
        }
    }

    /*
    | GET /api/hr/reports/export?type=employees&from=&to= — CSV download
    */
    public function export(): void
    {
        try {
            $export = $this->service->exportCsv(
                (string) ($_GET['type'] ?? ''),
                $_GET['from'] ?? null,
                $_GET['to'] ?? null,
                $this->authenticatedUserEmail()
            );

            header('Content-Type: text/csv; charset=utf-8');
            header('Content-Disposition: attachment; filename="' . $export['filename'] . '"');
            header('Pragma: no-cache');
            header('Expires: 0');

            $output = fopen('php://output', 'w');
            fwrite($output, "\xEF\xBB\xBF");

            if (!empty($export['rows'])) {
                fputcsv($output, array_keys($export['rows'][0]));
                foreach ($export['rows'] as $row) {
                    fputcsv($output, array_map(
                        static fn ($value) => $value === null ? '' : (string) $value,
                        array_values($row)
                    ));
                }
            } else {
                fputcsv($output, ['message']);
                fputcsv($output, ['No data available for the selected range.']);
            }

            fclose($output);
        } catch (InvalidArgumentException $exception) {
            http_response_code(400);
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => $exception->getMessage()]);
        } catch (Throwable $exception) {
            http_response_code(500);
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Unable to export the report.']);
        }
    }

    private function sendResponse(int $status, bool $success, string $message, array $data = []): void
    {
        http_response_code($status);
        header('Content-Type: application/json');
        echo json_encode([
            'success' => $success,
            'message' => $message,
            'data' => $data,
        ]);
    }

    private function authenticatedUserEmail(): string
    {
        $user = $GLOBALS['authenticated_user'] ?? null;

        return (string) (is_object($user) ? ($user->email ?? 'System') : ($user['email'] ?? 'System'));
    }
}

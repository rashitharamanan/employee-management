<?php

namespace App\Controllers;

use App\Services\PerformanceService;

class PerformanceController
{
    public function __construct(
        private PerformanceService $service
    ) {
    }

    public function reviews(): void
    {
        try {
            $filters = [
                'search' => $_GET['search'] ?? '',
                'status' => $_GET['status'] ?? '',
                'department' => $_GET['department'] ?? '',
                'employee_id' => $_GET['employee_id'] ?? '',
                'page' => $_GET['page'] ?? 1,
                'per_page' => $_GET['per_page'] ?? 10,
            ];
            $this->sendResponse(200, true, 'Performance reviews retrieved successfully.', $this->service->getReviews($filters));
        } catch (\Throwable $exception) {
            $this->sendResponse(500, false, 'Unable to load performance reviews.');
        }
    }

    public function review(int $id): void
    {
        try {
            $this->sendResponse(200, true, 'Performance review retrieved successfully.', ['review' => $this->service->getReview($id)]);
        } catch (\Exception $exception) {
            $this->sendResponse(404, false, $exception->getMessage());
        }
    }

    public function storeReview(): void
    {
        try {
            $input = json_decode(file_get_contents('php://input'), true);
            if (!is_array($input)) {
                $this->sendResponse(400, false, 'Invalid JSON payload.');
                return;
            }
            $review = $this->service->createReview($input);
            $this->sendResponse(201, true, 'Performance review created successfully.', ['review' => $review]);
        } catch (\Exception $exception) {
            $this->sendResponse(400, false, $exception->getMessage());
        }
    }

    public function updateReview(int $id): void
    {
        try {
            $input = json_decode(file_get_contents('php://input'), true);
            if (!is_array($input)) {
                $this->sendResponse(400, false, 'Invalid JSON payload.');
                return;
            }
            $review = $this->service->updateReview($id, $input);
            $this->sendResponse(200, true, 'Performance review updated successfully.', ['review' => $review]);
        } catch (\Exception $exception) {
            $this->sendResponse(400, false, $exception->getMessage());
        }
    }

    public function destroyReview(int $id): void
    {
        try {
            $this->service->deleteReview($id);
            $this->sendResponse(200, true, 'Performance review deleted successfully.');
        } catch (\Exception $exception) {
            $this->sendResponse(404, false, $exception->getMessage());
        }
    }

    public function goals(): void
    {
        try {
            $filters = [
                'search' => $_GET['search'] ?? '',
                'status' => $_GET['status'] ?? '',
                'priority' => $_GET['priority'] ?? '',
                'employee_id' => $_GET['employee_id'] ?? '',
                'page' => $_GET['page'] ?? 1,
                'per_page' => $_GET['per_page'] ?? 10,
            ];
            $this->sendResponse(200, true, 'Goals retrieved successfully.', $this->service->getGoals($filters));
        } catch (\Throwable $exception) {
            $this->sendResponse(500, false, 'Unable to load goals.');
        }
    }

    public function goal(int $id): void
    {
        try {
            $this->sendResponse(200, true, 'Goal retrieved successfully.', ['goal' => $this->service->getGoal($id)]);
        } catch (\Exception $exception) {
            $this->sendResponse(404, false, $exception->getMessage());
        }
    }

    public function storeGoal(): void
    {
        try {
            $input = json_decode(file_get_contents('php://input'), true);
            if (!is_array($input)) {
                $this->sendResponse(400, false, 'Invalid JSON payload.');
                return;
            }
            $goal = $this->service->createGoal($input);
            $this->sendResponse(201, true, 'Goal created successfully.', ['goal' => $goal]);
        } catch (\Exception $exception) {
            $this->sendResponse(400, false, $exception->getMessage());
        }
    }

    public function updateGoal(int $id): void
    {
        try {
            $input = json_decode(file_get_contents('php://input'), true);
            if (!is_array($input)) {
                $this->sendResponse(400, false, 'Invalid JSON payload.');
                return;
            }
            $goal = $this->service->updateGoal($id, $input);
            $this->sendResponse(200, true, 'Goal updated successfully.', ['goal' => $goal]);
        } catch (\Exception $exception) {
            $this->sendResponse(400, false, $exception->getMessage());
        }
    }

    public function destroyGoal(int $id): void
    {
        try {
            $this->service->deleteGoal($id);
            $this->sendResponse(200, true, 'Goal deleted successfully.');
        } catch (\Exception $exception) {
            $this->sendResponse(404, false, $exception->getMessage());
        }
    }

    public function summary(): void
    {
        try {
            $filters = ['department' => $_GET['department'] ?? ''];
            $this->sendResponse(200, true, 'Performance summary retrieved successfully.', $this->service->getSummary($filters));
        } catch (\Throwable $exception) {
            $this->sendResponse(500, false, 'Unable to load performance summary.');
        }
    }

    private function sendResponse(int $statusCode, bool $success, string $message, array $data = []): void
    {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode([
            'success' => $success,
            'message' => $message,
            'data' => $data,
        ]);
    }
}

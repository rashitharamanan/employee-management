<?php

namespace App\Controllers\Employee;

use App\Services\Employee\EmployeePerformanceService;

class EmployeePerformanceController
{
    public function __construct(
        private EmployeePerformanceService $service
    ) {
    }

    /**
     * Get complete employee performance page data
     */
    public function index(int $employeeId): void
    {
        try {
            $selectedReviewId = isset($_GET['review_id']) && is_numeric($_GET['review_id']) 
                ? (int)$_GET['review_id'] 
                : null;

            $data = $this->service->getPerformanceData($employeeId, $selectedReviewId);

            $this->sendResponse(200, true, 'Performance & training data retrieved successfully.', $data);
        } catch (\Throwable $e) {
            $this->sendResponse(400, false, $e->getMessage());
        }
    }

    /**
     * Get single review details
     */
    public function reviewDetail(int $employeeId): void
    {
        try {
            $reviewId = (int)($_GET['id'] ?? 0);
            if ($reviewId <= 0) {
                throw new \Exception('Invalid review ID.');
            }

            $data = $this->service->getReviewDetail($employeeId, $reviewId);
            if (!$data) {
                $this->sendResponse(404, false, 'Review not found.');
                return;
            }

            $this->sendResponse(200, true, 'Review details retrieved.', $data);
        } catch (\Throwable $e) {
            $this->sendResponse(400, false, $e->getMessage());
        }
    }

    /**
     * Enroll in a training program
     */
    public function enrollTraining(int $employeeId): void
    {
        try {
            $input = json_decode(file_get_contents('php://input'), true) ?? $_POST;
            $programId = (int)($input['program_id'] ?? 0);

            if ($programId <= 0) {
                throw new \Exception('Program ID is required.');
            }

            $res = $this->service->enrollTraining($employeeId, $programId);
            $msg = $res['already_enrolled'] 
                ? 'You are already enrolled in this course.' 
                : 'Enrolled in training course successfully!';

            $this->sendResponse(200, true, $msg, $res);
        } catch (\Throwable $e) {
            $this->sendResponse(400, false, $e->getMessage());
        }
    }

    /**
     * Update training progress
     */
    public function updateProgress(int $employeeId): void
    {
        try {
            $input = json_decode(file_get_contents('php://input'), true) ?? $_POST;
            $enrollmentId = (int)($input['enrollment_id'] ?? 0);
            $progress = (int)($input['progress'] ?? 0);

            if ($enrollmentId <= 0) {
                throw new \Exception('Enrollment ID is required.');
            }

            $this->service->updateTrainingProgress($employeeId, $enrollmentId, $progress);

            $this->sendResponse(200, true, 'Course progress updated successfully.');
        } catch (\Throwable $e) {
            $this->sendResponse(400, false, $e->getMessage());
        }
    }

    /**
     * Add new goal
     */
    public function addGoal(int $employeeId): void
    {
        try {
            $input = json_decode(file_get_contents('php://input'), true) ?? $_POST;

            if (empty($input['title'])) {
                throw new \Exception('Goal title is required.');
            }

            $id = $this->service->addGoal($employeeId, $input);

            $this->sendResponse(201, true, 'Goal created successfully.', ['id' => $id]);
        } catch (\Throwable $e) {
            $this->sendResponse(400, false, $e->getMessage());
        }
    }

    /**
     * Get certificate details
     */
    public function getCertificate(int $employeeId): void
    {
        try {
            $certId = (int)($_GET['id'] ?? 0);
            if ($certId <= 0) {
                throw new \Exception('Certificate ID is required.');
            }

            $cert = $this->service->getCertificate($employeeId, $certId);
            if (!$cert) {
                $this->sendResponse(404, false, 'Certificate not found.');
                return;
            }

            $this->sendResponse(200, true, 'Certificate retrieved successfully.', $cert);
        } catch (\Throwable $e) {
            $this->sendResponse(400, false, $e->getMessage());
        }
    }

    /**
     * Helper to send JSON response
     */
    private function sendResponse(int $statusCode, bool $success, string $message, array $data = []): void
    {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode([
            'success' => $success,
            'message' => $message,
            'data'    => $data
        ]);
    }
}

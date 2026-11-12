<?php

namespace App\Controllers;

use App\Services\TrainingService;

class TrainingController
{
    public function __construct(
        private TrainingService $service
    ) {
    }

    public function programs(): void
    {
        try {
            $filters = [
                'search' => $_GET['search'] ?? '',
                'status' => $_GET['status'] ?? '',
                'category' => $_GET['category'] ?? '',
                'delivery_mode' => $_GET['delivery_mode'] ?? '',
                'page' => $_GET['page'] ?? 1,
                'per_page' => $_GET['per_page'] ?? 10,
            ];
            $this->sendResponse(200, true, 'Training programs retrieved successfully.', $this->service->getPrograms($filters));
        } catch (\Throwable $exception) {
            $this->sendResponse(500, false, 'Unable to load training programs.');
        }
    }

    public function program(int $id): void
    {
        try {
            $this->sendResponse(200, true, 'Training program retrieved successfully.', ['program' => $this->service->getProgram($id)]);
        } catch (\Exception $exception) {
            $this->sendResponse(404, false, $exception->getMessage());
        }
    }

    public function storeProgram(): void
    {
        try {
            $input = json_decode(file_get_contents('php://input'), true);
            if (!is_array($input)) {
                $this->sendResponse(400, false, 'Invalid JSON payload.');
                return;
            }
            $program = $this->service->createProgram($input);
            $this->sendResponse(201, true, 'Training program created successfully.', ['program' => $program]);
        } catch (\Exception $exception) {
            $this->sendResponse(400, false, $exception->getMessage());
        }
    }

    public function updateProgram(int $id): void
    {
        try {
            $input = json_decode(file_get_contents('php://input'), true);
            if (!is_array($input)) {
                $this->sendResponse(400, false, 'Invalid JSON payload.');
                return;
            }
            $program = $this->service->updateProgram($id, $input);
            $this->sendResponse(200, true, 'Training program updated successfully.', ['program' => $program]);
        } catch (\Exception $exception) {
            $this->sendResponse(400, false, $exception->getMessage());
        }
    }

    public function destroyProgram(int $id): void
    {
        try {
            $this->service->deleteProgram($id);
            $this->sendResponse(200, true, 'Training program deleted successfully.');
        } catch (\Exception $exception) {
            $this->sendResponse(404, false, $exception->getMessage());
        }
    }

    public function sessions(int $programId): void
    {
        try {
            $this->sendResponse(200, true, 'Training sessions retrieved successfully.', ['sessions' => $this->service->getSessions($programId)]);
        } catch (\Throwable $exception) {
            $this->sendResponse(500, false, 'Unable to load training sessions.');
        }
    }

    public function storeSession(): void
    {
        try {
            $input = json_decode(file_get_contents('php://input'), true);
            if (!is_array($input)) {
                $this->sendResponse(400, false, 'Invalid JSON payload.');
                return;
            }
            $id = $this->service->createSession($input);
            $this->sendResponse(201, true, 'Training session created successfully.', ['id' => $id]);
        } catch (\Exception $exception) {
            $this->sendResponse(400, false, $exception->getMessage());
        }
    }

    public function updateSession(int $id): void
    {
        try {
            $input = json_decode(file_get_contents('php://input'), true);
            if (!is_array($input)) {
                $this->sendResponse(400, false, 'Invalid JSON payload.');
                return;
            }
            $this->service->updateSession($id, $input);
            $this->sendResponse(200, true, 'Training session updated successfully.');
        } catch (\Exception $exception) {
            $this->sendResponse(400, false, $exception->getMessage());
        }
    }

    public function destroySession(int $id): void
    {
        try {
            $this->service->deleteSession($id);
            $this->sendResponse(200, true, 'Training session deleted successfully.');
        } catch (\Exception $exception) {
            $this->sendResponse(400, false, $exception->getMessage());
        }
    }

    public function enrollments(): void
    {
        try {
            $filters = [
                'search' => $_GET['search'] ?? '',
                'status' => $_GET['status'] ?? '',
                'program_id' => $_GET['program_id'] ?? '',
                'employee_id' => $_GET['employee_id'] ?? '',
                'page' => $_GET['page'] ?? 1,
                'per_page' => $_GET['per_page'] ?? 10,
            ];
            $this->sendResponse(200, true, 'Enrollments retrieved successfully.', $this->service->getEnrollments($filters));
        } catch (\Throwable $exception) {
            $this->sendResponse(500, false, 'Unable to load enrollments.');
        }
    }

    public function storeEnrollment(): void
    {
        try {
            $input = json_decode(file_get_contents('php://input'), true);
            if (!is_array($input)) {
                $this->sendResponse(400, false, 'Invalid JSON payload.');
                return;
            }
            $id = $this->service->createEnrollment($input);
            $this->sendResponse(201, true, 'Enrollment created successfully.', ['id' => $id]);
        } catch (\Exception $exception) {
            $this->sendResponse(400, false, $exception->getMessage());
        }
    }

    public function updateEnrollment(int $id): void
    {
        try {
            $input = json_decode(file_get_contents('php://input'), true);
            if (!is_array($input)) {
                $this->sendResponse(400, false, 'Invalid JSON payload.');
                return;
            }
            $this->service->updateEnrollment($id, $input);
            $this->sendResponse(200, true, 'Enrollment updated successfully.');
        } catch (\Exception $exception) {
            $this->sendResponse(400, false, $exception->getMessage());
        }
    }

    public function destroyEnrollment(int $id): void
    {
        try {
            $this->service->deleteEnrollment($id);
            $this->sendResponse(200, true, 'Enrollment deleted successfully.');
        } catch (\Exception $exception) {
            $this->sendResponse(400, false, $exception->getMessage());
        }
    }

    public function issueCertificate(): void
    {
        try {
            $input = json_decode(file_get_contents('php://input'), true);
            if (!is_array($input)) {
                $this->sendResponse(400, false, 'Invalid JSON payload.');
                return;
            }
            $certId = $this->service->issueCertificate($input);
            $this->sendResponse(201, true, 'Certificate issued successfully.', ['id' => $certId]);
        } catch (\Exception $exception) {
            $this->sendResponse(400, false, $exception->getMessage());
        }
    }

    public function certificates(int $employeeId): void
    {
        try {
            $this->sendResponse(200, true, 'Certificates retrieved successfully.', ['certificates' => $this->service->getCertificatesByEmployee($employeeId)]);
        } catch (\Throwable $exception) {
            $this->sendResponse(500, false, 'Unable to load certificates.');
        }
    }

    public function summary(): void
    {
        try {
            $this->sendResponse(200, true, 'Training summary retrieved successfully.', $this->service->getSummary());
        } catch (\Throwable $exception) {
            $this->sendResponse(500, false, 'Unable to load training summary.');
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

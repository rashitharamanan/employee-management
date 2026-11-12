<?php

namespace App\Services;

use App\Repositories\TrainingRepository;
use PDO;

class TrainingService
{
    public function __construct(
        private TrainingRepository $repository,
        private PDO $db
    ) {
    }

    public function getPrograms(array $filters = []): array
    {
        return $this->repository->getPrograms($filters);
    }

    public function getProgram(int $id): array
    {
        $program = $this->repository->getProgramById($id);
        if (!$program) {
            throw new \Exception('Training program not found.');
        }
        return $program;
    }

    public function createProgram(array $data): array
    {
        $this->validateProgramData($data);

        $programId = $this->repository->createProgram([
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            'category' => $data['category'] ?? null,
            'trainer' => $data['trainer'] ?? null,
            'start_date' => $data['start_date'] ?? null,
            'end_date' => $data['end_date'] ?? null,
            'duration_hours' => isset($data['duration_hours']) ? (float) $data['duration_hours'] : null,
            'max_participants' => isset($data['max_participants']) ? (int) $data['max_participants'] : null,
            'location' => $data['location'] ?? null,
            'delivery_mode' => $data['delivery_mode'] ?? 'IN_PERSON',
            'status' => $data['status'] ?? 'DRAFT',
        ]);

        return $this->repository->getProgramById($programId);
    }

    public function updateProgram(int $id, array $data): array
    {
        $program = $this->repository->getProgramById($id);
        if (!$program) {
            throw new \Exception('Training program not found.');
        }

        $updateData = [];
        $allowed = ['title', 'description', 'category', 'trainer', 'start_date', 'end_date', 'duration_hours', 'max_participants', 'location', 'delivery_mode', 'status'];
        foreach ($allowed as $field) {
            if (array_key_exists($field, $data)) {
                $updateData[$field] = $data[$field];
            }
        }

        if (!empty($updateData)) {
            $this->repository->updateProgram($id, $updateData);
        }

        return $this->repository->getProgramById($id);
    }

    public function deleteProgram(int $id): void
    {
        $program = $this->repository->getProgramById($id);
        if (!$program) {
            throw new \Exception('Training program not found.');
        }
        $this->repository->deleteProgram($id);
    }

    public function getSessions(int $programId): array
    {
        return $this->repository->getSessions($programId);
    }

    public function createSession(array $data): int
    {
        if (empty($data['program_id'])) {
            throw new \Exception('Program ID is required.');
        }
        if (empty($data['title'])) {
            throw new \Exception('Session title is required.');
        }
        return $this->repository->createSession($data);
    }

    public function updateSession(int $id, array $data): bool
    {
        $updateData = [];
        $allowed = ['title', 'description', 'session_date', 'start_time', 'end_time', 'duration_hours', 'location', 'trainer', 'status'];
        foreach ($allowed as $field) {
            if (array_key_exists($field, $data)) {
                $updateData[$field] = $data[$field];
            }
        }
        return $this->repository->updateSession($id, $updateData);
    }

    public function deleteSession(int $id): bool
    {
        return $this->repository->deleteSession($id);
    }

    public function getEnrollments(array $filters = []): array
    {
        return $this->repository->getEnrollments($filters);
    }

    public function createEnrollment(array $data): int
    {
        if (empty($data['program_id'])) {
            throw new \Exception('Program ID is required.');
        }
        if (empty($data['employee_id'])) {
            throw new \Exception('Employee ID is required.');
        }
        return $this->repository->createEnrollment($data);
    }

    public function updateEnrollment(int $id, array $data): bool
    {
        $updateData = [];
        $allowed = ['status', 'completion_date', 'score', 'feedback', 'certificate_issued'];
        foreach ($allowed as $field) {
            if (array_key_exists($field, $data)) {
                $updateData[$field] = $data[$field];
            }
        }
        return $this->repository->updateEnrollment($id, $updateData);
    }

    public function deleteEnrollment(int $id): bool
    {
        return $this->repository->deleteEnrollment($id);
    }

    public function issueCertificate(array $data): int
    {
        if (empty($data['enrollment_id'])) {
            throw new \Exception('Enrollment ID is required.');
        }
        if (empty($data['certificate_number'])) {
            throw new \Exception('Certificate number is required.');
        }
        $certId = $this->repository->createCertificate($data);
        // Mark enrollment as certificate issued
        $this->repository->updateEnrollment($data['enrollment_id'], ['certificate_issued' => 1]);
        return $certId;
    }

    public function getCertificatesByEmployee(int $employeeId): array
    {
        return $this->repository->getCertificatesByEmployee($employeeId);
    }

    public function getSummary(): array
    {
        return $this->repository->getTrainingSummary();
    }

    private function validateProgramData(array $data): void
    {
        if (empty($data['title'])) {
            throw new \Exception('Program title is required.');
        }
        if (!empty($data['delivery_mode']) && !in_array($data['delivery_mode'], ['ONLINE', 'IN_PERSON', 'HYBRID'], true)) {
            throw new \Exception('Delivery mode must be ONLINE, IN_PERSON, or HYBRID.');
        }
    }
}

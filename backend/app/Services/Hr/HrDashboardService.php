<?php

namespace App\Services\Hr;

use App\Repositories\Hr\HrDashboardRepository;

class HrDashboardService
{
    public function __construct(private HrDashboardRepository $repository)
    {
    }

    public function getDashboard(): array
    {
        return $this->repository->getDashboard();
    }

    public function getAttendanceDashboard(array $filters): array
    {
        return $this->repository->getAttendanceDashboard($filters);
    }

    public function getRecruitmentDashboard(array $filters): array
    {
        return $this->repository->getRecruitmentDashboard($filters);
    }

    public function getOnboardingExitDashboard(array $filters): array
    {
        return $this->repository->getOnboardingExitDashboard($filters);
    }

    public function getPayrollDashboard(array $filters): array
    {
        return $this->repository->getPayrollDashboard($filters);
    }

    public function updateJobOpeningStatus(int $id, string $status): bool
    {
        return $this->repository->updateJobOpeningStatus($id, $status);
    }

    public function createJobOpening(array $data): int
    {
        return $this->repository->createJobOpening($data);
    }

    public function updateJobOpening(int $id, array $data): bool
    {
        return $this->repository->updateJobOpening($id, $data);
    }

    public function updateCandidateStage(int $id, string $stage): bool
    {
        return $this->repository->updateCandidateStage($id, $stage);
    }

    public function updateOnboardingStatus(int $id, string $status): bool
    {
        return $this->repository->updateOnboardingStatus($id, $status);
    }

    public function updateOnboardingRecord(int $id, array $data): bool
    {
        return $this->repository->updateOnboardingRecord($id, $data);
    }

    public function updateExitStatus(int $id, string $status): bool
    {
        return $this->repository->updateExitStatus($id, $status);
    }

    public function updateExitRecord(int $id, array $data): bool
    {
        return $this->repository->updateExitRecord($id, $data);
    }
}

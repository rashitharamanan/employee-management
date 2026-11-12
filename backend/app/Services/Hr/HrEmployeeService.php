<?php

namespace App\Services\Hr;

use App\Repositories\Hr\HrEmployeeRepository;

class HrEmployeeService
{
    public function __construct(private HrEmployeeRepository $repository)
    {
    }

    public function getDirectory(array $filters): array
    {
        $directory = $this->repository->search($filters);
        $directory['summary'] = $this->repository->getSummary();
        $directory['by_department'] = $this->repository->getDepartmentBreakdown();
        $directory['by_employment_type'] = $this->repository->getEmploymentTypeBreakdown();
        $directory['designations'] = $this->repository->getDesignations();
        $directory['recent_joiners'] = $this->repository->getRecentJoiners();
        return $directory;
    }
}

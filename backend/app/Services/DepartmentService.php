<?php

namespace App\Services;

use App\Repositories\Department\DepartmentRepository;
use Exception;

class DepartmentService
{
    public function __construct(
        private DepartmentRepository $departmentRepository
    ) {
    }

    /**
     * Get all departments with optional filters
     */
    public function getAllDepartments(array $filters = []): array
    {
        return $this->departmentRepository->getAll($filters);
    }

    /**
     * Get all active departments (optionally filtered by company)
     */
    public function getAllActiveDepartments(?int $companyId = null): array
    {
        return $this->departmentRepository->getAllActive($companyId);
    }

    /**
     * Get one department with designations
     */
    public function getDepartment(int $id): array
    {
        if ($id <= 0) {
            throw new Exception('Invalid department ID.');
        }

        $department = $this->departmentRepository->findById($id);
        if (!$department) {
            throw new Exception('Department not found.');
        }

        $designations = $this->departmentRepository->getDesignations($id);

        return [
            'department' => $department,
            'designations' => $designations
        ];
    }

    /**
     * Create department
     */
    public function createDepartment(array $data): array
    {
        $name = trim($data['name'] ?? '');
        if ($name === '') {
            throw new Exception('Department name is required.');
        }
        if (strlen($name) < 2 || strlen($name) > 100) {
            throw new Exception('Department name must be between 2 and 100 characters.');
        }

        $companyId = !empty($data['company_id']) ? (int) $data['company_id'] : 1;

        // Check if department name already exists
        $existing = $this->departmentRepository->findByName($name);
        if ($existing) {
            throw new Exception("Department with name '{$name}' already exists.");
        }

        $status = strtoupper(trim($data['status'] ?? 'ACTIVE'));
        if (!in_array($status, ['ACTIVE', 'INACTIVE'])) {
            $status = 'ACTIVE';
        }

        $id = $this->departmentRepository->create([
            'name' => $name,
            'company_id' => $companyId,
            'status' => $status
        ]);

        return $this->departmentRepository->findById($id);
    }

    /**
     * Update department
     */
    public function updateDepartment(int $id, array $data): array
    {
        if ($id <= 0) {
            throw new Exception('Invalid department ID.');
        }

        $existing = $this->departmentRepository->findById($id);
        if (!$existing) {
            throw new Exception('Department not found.');
        }

        $name = trim($data['name'] ?? $existing['name']);
        if ($name === '') {
            throw new Exception('Department name is required.');
        }
        if (strlen($name) < 2 || strlen($name) > 100) {
            throw new Exception('Department name must be between 2 and 100 characters.');
        }

        $companyId = !empty($data['company_id']) ? (int) $data['company_id'] : ($existing['company_id'] ?? 1);

        // Check uniqueness excluding current ID
        $nameMatch = $this->departmentRepository->findByName($name, null, $id);
        if ($nameMatch) {
            throw new Exception("Department with name '{$name}' already exists.");
        }

        $status = strtoupper(trim($data['status'] ?? $existing['status']));
        if (!in_array($status, ['ACTIVE', 'INACTIVE'])) {
            $status = $existing['status'];
        }

        $this->departmentRepository->update($id, [
            'name' => $name,
            'company_id' => $companyId,
            'status' => $status
        ]);

        return $this->departmentRepository->findById($id);
    }

    /**
     * Update department status
     */
    public function updateStatus(int $id, string $status): array
    {
        if ($id <= 0) {
            throw new Exception('Invalid department ID.');
        }

        $existing = $this->departmentRepository->findById($id);
        if (!$existing) {
            throw new Exception('Department not found.');
        }

        $status = strtoupper(trim($status));
        if (!in_array($status, ['ACTIVE', 'INACTIVE'])) {
            throw new Exception("Invalid status. Allowed values: 'ACTIVE', 'INACTIVE'.");
        }

        $this->departmentRepository->updateStatus($id, $status);

        return $this->departmentRepository->findById($id);
    }

    /**
     * Delete department (with safety checks)
     */
    public function deleteDepartment(int $id): bool
    {
        if ($id <= 0) {
            throw new Exception('Invalid department ID.');
        }

        $existing = $this->departmentRepository->findById($id);
        if (!$existing) {
            throw new Exception('Department not found.');
        }

        if ($existing['designations_count'] > 0 || $existing['employees_count'] > 0) {
            throw new Exception(
                "Cannot delete department '{$existing['name']}' because it contains {$existing['designations_count']} designation(s) and {$existing['employees_count']} employee(s). Please reassign them first, or set status to INACTIVE."
            );
        }

        return $this->departmentRepository->delete($id);
    }
}
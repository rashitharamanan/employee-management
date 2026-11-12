<?php

namespace App\Services;

use App\Repositories\Designation\DesignationRepository;
use Exception;

class DesignationService
{
    public function __construct(
        private DesignationRepository $designationRepository
    ) {
    }

    /**
     * Get all designations with optional filters
     */
    public function getAllDesignations(array $filters = []): array
    {
        return $this->designationRepository->getAll($filters);
    }

    /**
     * Get all active designations
     */
    public function getAllActiveDesignations(): array
    {
        return $this->designationRepository->getAllActive();
    }

    /**
     * Get designations by department
     */
    public function getDesignationsByDepartment(int $departmentId): array
    {
        if ($departmentId <= 0) {
            throw new Exception('Invalid department ID.');
        }

        return $this->designationRepository->getByDepartmentId($departmentId);
    }

    /**
     * Get one designation
     */
    public function getDesignation(int $id): array
    {
        if ($id <= 0) {
            throw new Exception('Invalid designation ID.');
        }

        $designation = $this->designationRepository->findById($id);

        if (!$designation) {
            throw new Exception('Designation not found.');
        }

        return $designation;
    }

    /**
     * Create designation
     */
    public function createDesignation(array $data): array
    {
        $departmentId = $data['department_id'] ?? null;
        if (empty($departmentId) || (int) $departmentId <= 0) {
            throw new Exception('Valid department ID is required.');
        }
        $departmentId = (int) $departmentId;

        $name = trim($data['name'] ?? '');
        if ($name === '') {
            throw new Exception('Designation name is required.');
        }
        if (strlen($name) < 2 || strlen($name) > 100) {
            throw new Exception('Designation name must be between 2 and 100 characters.');
        }

        // Check uniqueness in department
        $existing = $this->designationRepository->findByNameAndDepartment($name, $departmentId);
        if ($existing) {
            throw new Exception("Designation '{$name}' already exists in this department.");
        }

        $status = strtoupper(trim($data['status'] ?? 'ACTIVE'));
        if (!in_array($status, ['ACTIVE', 'INACTIVE'])) {
            $status = 'ACTIVE';
        }

        $id = $this->designationRepository->create([
            'department_id' => $departmentId,
            'name' => $name,
            'status' => $status
        ]);

        return $this->designationRepository->findById($id);
    }

    /**
     * Update designation
     */
    public function updateDesignation(int $id, array $data): array
    {
        if ($id <= 0) {
            throw new Exception('Invalid designation ID.');
        }

        $existing = $this->designationRepository->findById($id);
        if (!$existing) {
            throw new Exception('Designation not found.');
        }

        $departmentId = !empty($data['department_id']) ? (int) $data['department_id'] : $existing['department_id'];
        $name = trim($data['name'] ?? $existing['name']);
        if ($name === '') {
            throw new Exception('Designation name is required.');
        }
        if (strlen($name) < 2 || strlen($name) > 100) {
            throw new Exception('Designation name must be between 2 and 100 characters.');
        }

        // Check uniqueness in department excluding current ID
        $nameMatch = $this->designationRepository->findByNameAndDepartment($name, $departmentId, $id);
        if ($nameMatch) {
            throw new Exception("Designation '{$name}' already exists in this department.");
        }

        $status = strtoupper(trim($data['status'] ?? $existing['status']));
        if (!in_array($status, ['ACTIVE', 'INACTIVE'])) {
            $status = $existing['status'];
        }

        $this->designationRepository->update($id, [
            'department_id' => $departmentId,
            'name' => $name,
            'status' => $status
        ]);

        return $this->designationRepository->findById($id);
    }

    /**
     * Update designation status
     */
    public function updateStatus(int $id, string $status): array
    {
        if ($id <= 0) {
            throw new Exception('Invalid designation ID.');
        }

        $existing = $this->designationRepository->findById($id);
        if (!$existing) {
            throw new Exception('Designation not found.');
        }

        $status = strtoupper(trim($status));
        if (!in_array($status, ['ACTIVE', 'INACTIVE'])) {
            throw new Exception("Invalid status. Allowed values: 'ACTIVE', 'INACTIVE'.");
        }

        $this->designationRepository->updateStatus($id, $status);

        return $this->designationRepository->findById($id);
    }

    /**
     * Delete designation (with safety checks)
     */
    public function deleteDesignation(int $id): bool
    {
        if ($id <= 0) {
            throw new Exception('Invalid designation ID.');
        }

        $existing = $this->designationRepository->findById($id);
        if (!$existing) {
            throw new Exception('Designation not found.');
        }

        if ($existing['employees_count'] > 0) {
            throw new Exception(
                "Cannot delete designation '{$existing['name']}' because it is assigned to {$existing['employees_count']} employee(s). Please reassign them first, or set status to INACTIVE."
            );
        }

        return $this->designationRepository->delete($id);
    }
}
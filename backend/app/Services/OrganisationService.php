<?php

namespace App\Services;

use App\Repositories\Organisation\OrganisationRepository;
use Exception;

class OrganisationService
{
    public function __construct(
        private OrganisationRepository $organisationRepository
    ) {
    }

    /**
     * Get all organisations with optional filters
     */
    public function getAllOrganisations(
        array $filters = [],
        string $sortBy = 'id',
        string $sortOrder = 'DESC'
    ): array {
        return $this->organisationRepository->getAll($filters, $sortBy, $sortOrder);
    }

    /**
     * Get single organisation details with departments and users
     */
    public function getOrganisation(int $id): array
    {
        if ($id <= 0) {
            throw new Exception('Invalid organisation ID.');
        }

        $organisation = $this->organisationRepository->findById($id);
        if (!$organisation) {
            throw new Exception('Organisation not found.');
        }

        $departments = $this->organisationRepository->getDepartmentsByCompanyId($id);
        $users = $this->organisationRepository->getUsersByCompanyId($id);

        return [
            'organisation' => $organisation,
            'departments' => $departments,
            'users' => $users
        ];
    }

    /**
     * Create a new organisation
     */
    public function createOrganisation(array $data): array
    {
        // 1. Validate name
        $name = trim($data['name'] ?? '');
        if ($name === '') {
            throw new Exception('Organisation name is required.');
        }
        if (strlen($name) < 2 || strlen($name) > 150) {
            throw new Exception('Organisation name must be between 2 and 150 characters.');
        }

        // 2. Validate code
        $code = strtoupper(trim($data['code'] ?? ''));
        if ($code === '') {
            throw new Exception('Organisation code is required.');
        }
        if (!preg_match('/^[A-Z0-9_-]{2,50}$/', $code)) {
            throw new Exception('Organisation code must be 2-50 characters alphanumeric (letters, numbers, hyphens, underscores).');
        }

        // 3. Check code uniqueness
        $existing = $this->organisationRepository->findByCode($code);
        if ($existing) {
            throw new Exception("Organisation code '{$code}' is already in use by '{$existing['name']}'.");
        }

        // 4. Validate email if provided
        $email = trim($data['email'] ?? '');
        if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception('Invalid email address.');
        }

        // 5. Validate status
        $status = strtoupper(trim($data['status'] ?? 'ACTIVE'));
        if (!in_array($status, ['ACTIVE', 'INACTIVE'])) {
            $status = 'ACTIVE';
        }

        $id = $this->organisationRepository->create([
            'name' => $name,
            'code' => $code,
            'email' => $email !== '' ? $email : null,
            'phone' => trim($data['phone'] ?? ''),
            'address' => trim($data['address'] ?? ''),
            'city' => trim($data['city'] ?? ''),
            'state' => trim($data['state'] ?? ''),
            'country' => trim($data['country'] ?? 'India'),
            'status' => $status
        ]);

        return $this->organisationRepository->findById($id);
    }

    /**
     * Update an organisation
     */
    public function updateOrganisation(int $id, array $data): array
    {
        if ($id <= 0) {
            throw new Exception('Invalid organisation ID.');
        }

        $existing = $this->organisationRepository->findById($id);
        if (!$existing) {
            throw new Exception('Organisation not found.');
        }

        // 1. Validate name
        $name = trim($data['name'] ?? $existing['name']);
        if ($name === '') {
            throw new Exception('Organisation name is required.');
        }
        if (strlen($name) < 2 || strlen($name) > 150) {
            throw new Exception('Organisation name must be between 2 and 150 characters.');
        }

        // 2. Validate code
        $code = strtoupper(trim($data['code'] ?? $existing['code']));
        if ($code === '') {
            throw new Exception('Organisation code is required.');
        }
        if (!preg_match('/^[A-Z0-9_-]{2,50}$/', $code)) {
            throw new Exception('Organisation code must be 2-50 characters alphanumeric.');
        }

        // 3. Check code uniqueness excluding current organisation
        $codeMatch = $this->organisationRepository->findByCode($code, $id);
        if ($codeMatch) {
            throw new Exception("Organisation code '{$code}' is already used by another organisation.");
        }

        // 4. Validate email if provided
        $email = trim($data['email'] ?? ($existing['email'] ?? ''));
        if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception('Invalid email address.');
        }

        // 5. Validate status
        $status = strtoupper(trim($data['status'] ?? $existing['status']));
        if (!in_array($status, ['ACTIVE', 'INACTIVE'])) {
            $status = $existing['status'];
        }

        $this->organisationRepository->update($id, [
            'name' => $name,
            'code' => $code,
            'email' => $email !== '' ? $email : null,
            'phone' => trim($data['phone'] ?? ($existing['phone'] ?? '')),
            'address' => trim($data['address'] ?? ($existing['address'] ?? '')),
            'city' => trim($data['city'] ?? ($existing['city'] ?? '')),
            'state' => trim($data['state'] ?? ($existing['state'] ?? '')),
            'country' => trim($data['country'] ?? ($existing['country'] ?? 'India')),
            'status' => $status
        ]);

        return $this->organisationRepository->findById($id);
    }

    /**
     * Update organisation status
     */
    public function updateStatus(int $id, string $status): array
    {
        if ($id <= 0) {
            throw new Exception('Invalid organisation ID.');
        }

        $existing = $this->organisationRepository->findById($id);
        if (!$existing) {
            throw new Exception('Organisation not found.');
        }

        $status = strtoupper(trim($status));
        if (!in_array($status, ['ACTIVE', 'INACTIVE'])) {
            throw new Exception("Invalid status. Allowed values: 'ACTIVE', 'INACTIVE'.");
        }

        $this->organisationRepository->updateStatus($id, $status);

        return $this->organisationRepository->findById($id);
    }

    /**
     * Delete an organisation (with safety checks)
     */
    public function deleteOrganisation(int $id): bool
    {
        if ($id <= 0) {
            throw new Exception('Invalid organisation ID.');
        }

        $existing = $this->organisationRepository->findById($id);
        if (!$existing) {
            throw new Exception('Organisation not found.');
        }

        // Prevent deleting default company with ID 1 if it has records
        if ($id === 1) {
            throw new Exception('The primary default organisation cannot be deleted. You may edit it or change its status.');
        }

        // Check if organisation has assigned departments or users
        if ($existing['departments_count'] > 0 || $existing['users_count'] > 0) {
            throw new Exception(
                "Cannot delete organisation '{$existing['name']}' because it has {$existing['departments_count']} department(s) and {$existing['users_count']} user(s) assigned. Please reassign or remove them first, or set status to INACTIVE."
            );
        }

        return $this->organisationRepository->delete($id);
    }

    /**
     * Get organisation structure tree
     */
    public function getOrganisationStructure(int $id): array
    {
        if ($id <= 0) {
            throw new Exception('Invalid organisation ID.');
        }

        $structure = $this->organisationRepository->getStructure($id);
        if (!$structure) {
            throw new Exception('Organisation not found.');
        }

        return $structure;
    }
}

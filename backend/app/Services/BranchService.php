<?php

namespace App\Services;

use App\Repositories\Branch\BranchRepository;
use Exception;

class BranchService
{
    public function __construct(private BranchRepository $branchRepository) {}

    public function getAllBranches(array $filters = []): array
    {
        return $this->branchRepository->getAll($filters);
    }

    public function createBranch(array $data): array
    {
        $name = trim($data['name'] ?? '');
        if ($name === '') {
            throw new Exception('Branch name is required.');
        }
        if (strlen($name) < 2 || strlen($name) > 150) {
            throw new Exception('Branch name must be between 2 and 150 characters.');
        }

        $companyId = !empty($data['company_id']) ? (int) $data['company_id'] : 1;
        $location  = trim($data['location'] ?? '');
        $phone     = trim($data['phone']    ?? '');
        $status    = strtolower(trim($data['status'] ?? 'active'));
        if (!in_array($status, ['active', 'inactive'])) {
            $status = 'active';
        }

        $id = $this->branchRepository->create([
            'company_id' => $companyId,
            'name'       => $name,
            'location'   => $location ?: null,
            'phone'      => $phone    ?: null,
            'status'     => $status,
        ]);

        return $this->branchRepository->findById($id) ?? ['id' => $id, 'name' => $name];
    }
}

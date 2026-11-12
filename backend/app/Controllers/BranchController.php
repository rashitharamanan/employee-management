<?php

namespace App\Controllers;

use App\Services\BranchService;
use Exception;

class BranchController
{
    public function __construct(private BranchService $branchService) {}

    /** GET /api/branches */
    public function index(): void
    {
        try {
            $filters = [
                'company_id' => $_GET['company_id'] ?? '',
                'status'     => $_GET['status']     ?? '',
            ];
            $branches = $this->branchService->getAllBranches($filters);
            $this->ok(200, 'Branches retrieved successfully.', ['branches' => $branches, 'total' => count($branches)]);
        } catch (Exception $e) {
            $this->ok(500, $e->getMessage(), [], false);
        }
    }

    /** POST /api/branches */
    public function store(): void
    {
        try {
            $input = json_decode(file_get_contents('php://input'), true);
            if (!is_array($input)) {
                $this->ok(400, 'Invalid JSON request body.', [], false);
                return;
            }
            $branch = $this->branchService->createBranch($input);
            $this->ok(201, 'Branch created successfully.', ['branch' => $branch]);
        } catch (Exception $e) {
            $this->ok(400, $e->getMessage(), [], false);
        }
    }

    private function ok(int $code, string $msg, array $data = [], bool $success = true): void
    {
        http_response_code($code);
        header('Content-Type: application/json');
        $res = ['success' => $success, 'message' => $msg];
        if ($data) $res['data'] = $data;
        echo json_encode($res);
    }
}

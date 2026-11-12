<?php

namespace App\Controllers;

use App\Repositories\PermissionRepository;

class PermissionController
{
    public function __construct(
        private PermissionRepository $permissionRepository
    ) {
    }

    public function roles(): void
    {
        $this->respond(200, true, 'Roles retrieved successfully.', [
            'roles' => $this->permissionRepository->getRoles()
        ]);
    }

    public function permissions(): void
    {
        $this->respond(200, true, 'Permissions retrieved successfully.', [
            'permissions' => $this->permissionRepository->getPermissions()
        ]);
    }

    public function rolePermissions(int $roleId): void
    {
        $this->respond(200, true, 'Role permissions retrieved successfully.', [
            'permissions' => $this->permissionRepository->getRolePermissions($roleId)
        ]);
    }

    public function storeRole(): void
    {
        $input = $this->input();
        $name = trim((string) ($input['name'] ?? ''));
        $description = trim((string) ($input['description'] ?? ''));

        if ($name === '') {
            $this->respond(400, false, 'Role name is required.');
            return;
        }

        try {
            $id = $this->permissionRepository->createRole($name, $description);
            $this->respond(201, true, 'Role created successfully.', ['id' => $id]);
        } catch (\PDOException $exception) {
            $this->respond(400, false, 'Role name may already exist.');
        }
    }

    public function updateRole(int $roleId): void
    {
        $input = $this->input();
        $name = trim((string) ($input['name'] ?? ''));
        $description = trim((string) ($input['description'] ?? ''));

        if ($name === '') {
            $this->respond(400, false, 'Role name is required.');
            return;
        }

        $updated = $this->permissionRepository->updateRole($roleId, $name, $description);
        $this->respond($updated ? 200 : 404, $updated, $updated ? 'Role updated successfully.' : 'Role not found.');
    }

    public function destroyRole(int $roleId): void
    {
        try {
            $deleted = $this->permissionRepository->deleteRole($roleId);
            $this->respond($deleted ? 200 : 404, $deleted, $deleted ? 'Role deleted successfully.' : 'Role not found.');
        } catch (\DomainException $exception) {
            $this->respond(409, false, $exception->getMessage());
        }
    }

    public function replaceRolePermissions(int $roleId): void
    {
        $input = $this->input();
        $permissionIds = $input['permission_ids'] ?? null;

        if (!is_array($permissionIds)) {
            $this->respond(400, false, 'permission_ids must be an array.');
            return;
        }

        try {
            $this->permissionRepository->replaceRolePermissions($roleId, $permissionIds);
            $this->respond(200, true, 'Role permissions updated successfully.');
        } catch (\DomainException $exception) {
            $this->respond(400, false, $exception->getMessage());
        }
    }

    private function input(): array
    {
        $input = json_decode(file_get_contents('php://input'), true);
        return is_array($input) ? $input : [];
    }

    private function respond(int $status, bool $success, string $message, array $data = []): void
    {
        http_response_code($status);
        echo json_encode([
            'success' => $success,
            'message' => $message,
            'data' => $data
        ]);
    }
}
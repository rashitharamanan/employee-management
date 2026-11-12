<?php

namespace App\Repositories;

use PDO;

class PermissionRepository
{
    public function __construct(
        private PDO $db
    ) {
    }

    public function hasPermission(string $role, string $permission): bool
    {
        $statement = $this->db->prepare(
            "SELECT 1 FROM role_permissions rp
             INNER JOIN roles r ON r.id = rp.role_id
             INNER JOIN permissions p ON p.id = rp.permission_id
             WHERE UPPER(r.name) = UPPER(:role) AND p.name = :permission
             LIMIT 1"
        );
        $statement->execute([
            ':role' => $role,
            ':permission' => $permission
        ]);

        return (bool) $statement->fetchColumn();
    }

    public function getRoles(): array
    {
        $statement = $this->db->query(
            "SELECT r.id, r.name, r.description, r.created_at, r.updated_at,
                    COUNT(DISTINCT u.id) AS users_count,
                    COUNT(DISTINCT rp.permission_id) AS permissions_count
             FROM roles r
             LEFT JOIN users u ON u.role_id = r.id
             LEFT JOIN role_permissions rp ON rp.role_id = r.id
             GROUP BY r.id, r.name, r.description, r.created_at, r.updated_at
             ORDER BY r.id"
        );

        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getPermissions(): array
    {
        $statement = $this->db->query(
            "SELECT p.id, p.name, p.description, p.created_at,
                    COUNT(DISTINCT rp.role_id) AS assigned_roles
             FROM permissions p
             LEFT JOIN role_permissions rp ON rp.permission_id = p.id
             GROUP BY p.id, p.name, p.description, p.created_at
             ORDER BY p.id"
        );

        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getRolePermissions(int $roleId): array
    {
        $statement = $this->db->prepare(
            "SELECT p.id, p.name, p.description, p.created_at
             FROM permissions p
             INNER JOIN role_permissions rp ON rp.permission_id = p.id
             WHERE rp.role_id = :role_id
             ORDER BY p.id"
        );
        $statement->execute([':role_id' => $roleId]);

        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    public function createRole(string $name, string $description): int
    {
        $statement = $this->db->prepare(
            'INSERT INTO roles (name, description, created_at, updated_at) VALUES (:name, :description, NOW(), NOW())'
        );
        $statement->execute([
            ':name' => $name,
            ':description' => $description
        ]);

        return (int) $this->db->lastInsertId();
    }

    public function updateRole(int $roleId, string $name, string $description): bool
    {
        $statement = $this->db->prepare(
            'UPDATE roles SET name = :name, description = :description, updated_at = NOW() WHERE id = :id'
        );
        $statement->execute([
            ':id' => $roleId,
            ':name' => $name,
            ':description' => $description
        ]);

        return $statement->rowCount() > 0;
    }

    public function deleteRole(int $roleId): bool
    {
        $this->db->beginTransaction();

        try {
            $userCheck = $this->db->prepare('SELECT COUNT(*) FROM users WHERE role_id = :id');
            $userCheck->execute([':id' => $roleId]);

            if ((int) $userCheck->fetchColumn() > 0) {
                throw new \DomainException('Role cannot be deleted while users are assigned to it.');
            }

            $permissions = $this->db->prepare('DELETE FROM role_permissions WHERE role_id = :id');
            $permissions->execute([':id' => $roleId]);
            $role = $this->db->prepare('DELETE FROM roles WHERE id = :id');
            $role->execute([':id' => $roleId]);
            $this->db->commit();

            return $role->rowCount() > 0;
        } catch (\Throwable $exception) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            throw $exception;
        }
    }

    public function replaceRolePermissions(int $roleId, array $permissionIds): void
    {
        $this->db->beginTransaction();

        try {
            $roleCheck = $this->db->prepare('SELECT id FROM roles WHERE id = :id');
            $roleCheck->execute([':id' => $roleId]);

            if (!$roleCheck->fetchColumn()) {
                throw new \DomainException('Role not found.');
            }

            $permissionIds = array_values(array_unique(array_map('intval', $permissionIds)));
            if ($permissionIds) {
                $placeholders = implode(',', array_fill(0, count($permissionIds), '?'));
                $permissionCheck = $this->db->prepare(
                    "SELECT COUNT(*) FROM permissions WHERE id IN ($placeholders)"
                );
                $permissionCheck->execute($permissionIds);

                if ((int) $permissionCheck->fetchColumn() !== count($permissionIds)) {
                    throw new \DomainException('One or more permissions do not exist.');
                }
            }

            $delete = $this->db->prepare('DELETE FROM role_permissions WHERE role_id = :role_id');
            $delete->execute([':role_id' => $roleId]);

            $insert = $this->db->prepare(
                'INSERT INTO role_permissions (role_id, permission_id) VALUES (:role_id, :permission_id)'
            );
            foreach ($permissionIds as $permissionId) {
                $insert->execute([
                    ':role_id' => $roleId,
                    ':permission_id' => $permissionId
                ]);
            }

            $this->db->commit();
        } catch (\Throwable $exception) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            throw $exception;
        }
    }
}

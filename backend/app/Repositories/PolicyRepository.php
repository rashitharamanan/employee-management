<?php

namespace App\Repositories;

use PDO;

class PolicyRepository
{
    public function __construct(
        private PDO $db
    ) {
    }

    public function ensureSchema(): void
    {
        $this->db->exec(<<<'SQL'
            CREATE TABLE IF NOT EXISTS system_policies (
                id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(120) NOT NULL,
                category VARCHAR(60) NOT NULL DEFAULT 'General',
                description VARCHAR(255) NOT NULL DEFAULT '',
                policy_value TEXT NULL,
                status ENUM('ACTIVE', 'INACTIVE') NOT NULL DEFAULT 'ACTIVE',
                created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                UNIQUE KEY uq_system_policies_name (name),
                KEY idx_system_policies_status (status),
                KEY idx_system_policies_category (category)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
        SQL);

        $this->db->exec(<<<'SQL'
            CREATE TABLE IF NOT EXISTS system_configurations (
                id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                config_key VARCHAR(120) NOT NULL,
                config_value TEXT NULL,
                description VARCHAR(255) NOT NULL DEFAULT '',
                category VARCHAR(60) NOT NULL DEFAULT 'General',
                status ENUM('ACTIVE', 'INACTIVE') NOT NULL DEFAULT 'ACTIVE',
                created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                UNIQUE KEY uq_system_configurations_key (config_key),
                KEY idx_system_configurations_status (status),
                KEY idx_system_configurations_category (category)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
        SQL);
    }

    public function getPolicySummary(): array
    {
        $statement = $this->db->query(
            "SELECT COUNT(*) AS total_policies,
                    SUM(status = 'ACTIVE') AS active_policies,
                    MAX(updated_at) AS last_policy_update
             FROM system_policies"
        );

        return $statement->fetch(PDO::FETCH_ASSOC) ?: [];
    }

    public function getConfigurationSummary(): array
    {
        $statement = $this->db->query(
            "SELECT COUNT(*) AS configuration_items,
                    MAX(updated_at) AS last_configuration_update
             FROM system_configurations"
        );

        return $statement->fetch(PDO::FETCH_ASSOC) ?: [];
    }

    public function getPolicies(?string $status = null, ?string $category = null): array
    {
        $where = [];
        $params = [];

        if ($status !== null && $status !== '') {
            $where[] = 'status = :status';
            $params[':status'] = strtoupper($status);
        }
        if ($category !== null && $category !== '') {
            $where[] = 'category = :category';
            $params[':category'] = $category;
        }

        $sql = 'SELECT id, name, category, description, policy_value, status, created_at, updated_at FROM system_policies';
        if ($where) {
            $sql .= ' WHERE ' . implode(' AND ', $where);
        }
        $sql .= ' ORDER BY id';

        $statement = $this->db->prepare($sql);
        $statement->execute($params);

        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getConfigurations(?string $status = null, ?string $category = null): array
    {
        $where = [];
        $params = [];

        if ($status !== null && $status !== '') {
            $where[] = 'status = :status';
            $params[':status'] = strtoupper($status);
        }
        if ($category !== null && $category !== '') {
            $where[] = 'category = :category';
            $params[':category'] = $category;
        }

        $sql = 'SELECT id, config_key, config_value, description, category, status, created_at, updated_at FROM system_configurations';
        if ($where) {
            $sql .= ' WHERE ' . implode(' AND ', $where);
        }
        $sql .= ' ORDER BY id';

        $statement = $this->db->prepare($sql);
        $statement->execute($params);

        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    public function createPolicy(array $data): int
    {
        $statement = $this->db->prepare(
            'INSERT INTO system_policies (name, category, description, policy_value, status) VALUES (:name, :category, :description, :policy_value, :status)'
        );
        $statement->execute([
            ':name' => $data['name'],
            ':category' => $data['category'],
            ':description' => $data['description'],
            ':policy_value' => $data['policy_value'],
            ':status' => $data['status']
        ]);

        return (int) $this->db->lastInsertId();
    }

    public function updatePolicy(int $id, array $data): bool
    {
        $statement = $this->db->prepare(
            'UPDATE system_policies SET name = :name, category = :category, description = :description, policy_value = :policy_value, status = :status WHERE id = :id'
        );
        $statement->execute([
            ':id' => $id,
            ':name' => $data['name'],
            ':category' => $data['category'],
            ':description' => $data['description'],
            ':policy_value' => $data['policy_value'],
            ':status' => $data['status']
        ]);

        return $statement->rowCount() > 0;
    }

    public function deletePolicy(int $id): bool
    {
        $statement = $this->db->prepare('DELETE FROM system_policies WHERE id = :id');
        $statement->execute([':id' => $id]);

        return $statement->rowCount() > 0;
    }

    public function createConfiguration(array $data): int
    {
        $statement = $this->db->prepare(
            'INSERT INTO system_configurations (config_key, config_value, description, category, status) VALUES (:config_key, :config_value, :description, :category, :status)'
        );
        $statement->execute([
            ':config_key' => $data['config_key'],
            ':config_value' => $data['config_value'],
            ':description' => $data['description'],
            ':category' => $data['category'],
            ':status' => $data['status']
        ]);

        return (int) $this->db->lastInsertId();
    }

    public function updateConfiguration(int $id, array $data): bool
    {
        $statement = $this->db->prepare(
            'UPDATE system_configurations SET config_key = :config_key, config_value = :config_value, description = :description, category = :category, status = :status WHERE id = :id'
        );
        $statement->execute([
            ':id' => $id,
            ':config_key' => $data['config_key'],
            ':config_value' => $data['config_value'],
            ':description' => $data['description'],
            ':category' => $data['category'],
            ':status' => $data['status']
        ]);

        return $statement->rowCount() > 0;
    }

    public function deleteConfiguration(int $id): bool
    {
        $statement = $this->db->prepare('DELETE FROM system_configurations WHERE id = :id');
        $statement->execute([':id' => $id]);

        return $statement->rowCount() > 0;
    }
}

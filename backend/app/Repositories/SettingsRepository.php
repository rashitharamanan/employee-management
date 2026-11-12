<?php

namespace App\Repositories;

use PDO;

class SettingsRepository
{
    public function __construct(private PDO $db)
    {
    }

    public function ensureSchema(): void
    {
        $this->db->exec(<<<'SQL'
            CREATE TABLE IF NOT EXISTS system_settings (
                id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                setting_key VARCHAR(120) NOT NULL,
                setting_value TEXT NULL,
                setting_group VARCHAR(60) NOT NULL DEFAULT 'GENERAL',
                updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                UNIQUE KEY uq_system_settings_key (setting_key),
                KEY idx_system_settings_group (setting_group)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
        SQL);

        $defaults = [
            'system_name' => ['Employee Management System', 'GENERAL'],
            'date_format' => ['DD MMM YYYY', 'GENERAL'],
            'time_format' => ['12 Hour', 'GENERAL'],
            'timezone' => ['Asia/Kolkata', 'GENERAL'],
            'language' => ['English (US)', 'GENERAL'],
            'items_per_page' => ['10', 'GENERAL'],
            'maintenance_mode' => ['0', 'GENERAL'],
            'session_timeout' => ['30', 'SECURITY'],
            'password_expiry_days' => ['90', 'SECURITY']
        ];

        $statement = $this->db->prepare(
            'INSERT IGNORE INTO system_settings (setting_key, setting_value, setting_group) VALUES (:key, :value, :group)'
        );
        foreach ($defaults as $key => [$value, $group]) {
            $statement->execute([':key' => $key, ':value' => $value, ':group' => $group]);
        }
    }

    public function getSettings(string $group = 'GENERAL'): array
    {
        $statement = $this->db->prepare(
            'SELECT setting_key, setting_value, setting_group, updated_at FROM system_settings WHERE setting_group = :group ORDER BY setting_key'
        );
        $statement->execute([':group' => strtoupper($group)]);

        $settings = [];
        foreach ($statement->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $settings[$row['setting_key']] = [
                'value' => $row['setting_value'],
                'group' => $row['setting_group'],
                'updated_at' => $row['updated_at']
            ];
        }
        return $settings;
    }

    public function updateSettings(array $values): void
    {
        $allowed = [
            'system_name', 'date_format', 'time_format', 'timezone',
            'language', 'items_per_page', 'maintenance_mode',
            'session_timeout', 'password_expiry_days'
        ];
        $statement = $this->db->prepare(
            'UPDATE system_settings SET setting_value = :value WHERE setting_key = :key'
        );

        foreach ($values as $key => $value) {
            if (!in_array($key, $allowed, true)) {
                continue;
            }
            if (is_array($value) || is_object($value)) {
                throw new \InvalidArgumentException("Invalid value for {$key}.");
            }
            $statement->execute([
                ':key' => $key,
                ':value' => (string) $value
            ]);
        }
    }

    public function getSystemInformation(): array
    {
        $counts = $this->db->query(
            "SELECT
                (SELECT COUNT(*) FROM users) AS total_users,
                (SELECT COUNT(*) FROM employees) AS total_employees,
                (SELECT COUNT(*) FROM companies) AS total_organisations"
        )->fetch(PDO::FETCH_ASSOC) ?: [];

        $version = $this->db->query('SELECT VERSION()')->fetchColumn();
        return [
            'application_name' => 'Employee Management System',
            'environment' => $_ENV['APP_ENV'] ?? 'local',
            'php_version' => PHP_VERSION,
            'laravel_version' => 'Not applicable',
            'database' => 'MySQL ' . $version,
            'web_server' => $_SERVER['SERVER_SOFTWARE'] ?? 'Apache',
            'server_os' => PHP_OS,
            'total_users' => (int) ($counts['total_users'] ?? 0),
            'total_employees' => (int) ($counts['total_employees'] ?? 0),
            'total_organisations' => (int) ($counts['total_organisations'] ?? 0),
            'storage_used_bytes' => $this->storageUsedBytes(),
            'storage_limit_bytes' => 10737418240
        ];
    }

    public function getOperationalStatus(): array
    {
        $settings = $this->getSettings('GENERAL');
        $maintenance = ($settings['maintenance_mode']['value'] ?? '0') === '1';
        return [
            'version' => 'v1.0.0',
            'released_at' => '2025-05-20',
            'server_time' => date('Y-m-d H:i:s'),
            'timezone' => date_default_timezone_get(),
            'database_status' => $this->db->query('SELECT 1')->fetchColumn() === 1 ? 'Healthy' : 'Unavailable',
            'last_backup' => null,
            'maintenance_mode' => $maintenance
        ];
    }

    private function storageUsedBytes(): int
    {
        $storagePath = dirname(__DIR__, 2) . '/storage';
        if (!is_dir($storagePath)) {
            return 0;
        }
        $size = 0;
        $iterator = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($storagePath, \FilesystemIterator::SKIP_DOTS));
        foreach ($iterator as $file) {
            if ($file->isFile()) {
                $size += $file->getSize();
            }
        }
        return $size;
    }
}

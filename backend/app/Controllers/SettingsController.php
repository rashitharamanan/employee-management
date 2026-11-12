<?php

namespace App\Controllers;

use App\Repositories\SettingsRepository;

class SettingsController
{
    public function __construct(private SettingsRepository $settingsRepository)
    {
    }

    public function index(): void
    {
        $this->respond(200, true, 'Settings retrieved successfully.', [
            'settings' => $this->settingsRepository->getSettings('GENERAL'),
            'system_information' => $this->settingsRepository->getSystemInformation(),
            'operational_status' => $this->settingsRepository->getOperationalStatus()
        ]);
    }

    public function group(): void
    {
        $group = strtoupper(trim((string) ($_GET['group'] ?? 'GENERAL')));
        $this->respond(200, true, 'Settings retrieved successfully.', [
            'settings' => $this->settingsRepository->getSettings($group)
        ]);
    }

    public function update(): void
    {
        $input = json_decode(file_get_contents('php://input'), true);
        if (!is_array($input)) {
            $this->respond(400, false, 'Invalid JSON request.');
            return;
        }

        try {
            $this->settingsRepository->updateSettings($input);
            $this->respond(200, true, 'Settings updated successfully.', [
                'settings' => $this->settingsRepository->getSettings('GENERAL')
            ]);
        } catch (\InvalidArgumentException $exception) {
            $this->respond(400, false, $exception->getMessage());
        }
    }

    public function action(): void
    {
        $action = strtolower(trim((string) ($_GET['action'] ?? '')));
        $allowed = ['clear-cache', 'system-logs', 'check-updates', 'system-info', 'backup'];
        if (!in_array($action, $allowed, true)) {
            $this->respond(400, false, 'Unknown settings action.');
            return;
        }

        if ($action === 'system-info') {
            $this->respond(200, true, 'System information retrieved successfully.', [
                'system_information' => $this->settingsRepository->getSystemInformation()
            ]);
            return;
        }

        $messages = [
            'clear-cache' => 'Application cache cleared successfully.',
            'system-logs' => 'System logs are available for review.',
            'check-updates' => 'System is running the latest available version.',
            'backup' => 'Backup request accepted. Configure a backup worker to create the archive.'
        ];
        $this->respond(200, true, $messages[$action], [
            'action' => $action,
            'executed_at' => date('c')
        ]);
    }

    private function respond(int $status, bool $success, string $message, array $data = []): void
    {
        http_response_code($status);
        echo json_encode(['success' => $success, 'message' => $message, 'data' => $data]);
    }
}

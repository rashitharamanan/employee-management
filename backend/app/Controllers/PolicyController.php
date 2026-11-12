<?php

namespace App\Controllers;

use App\Repositories\PolicyRepository;

class PolicyController
{
    public function __construct(
        private PolicyRepository $policyRepository
    ) {
    }

    public function index(): void
    {
        $this->respond(200, true, 'Policy dashboard retrieved successfully.', [
            'summary' => array_merge(
                $this->policyRepository->getPolicySummary(),
                $this->policyRepository->getConfigurationSummary()
            ),
            'policies' => $this->policyRepository->getPolicies(),
            'configurations' => $this->policyRepository->getConfigurations()
        ]);
    }

    public function policies(): void
    {
        $this->respond(200, true, 'Policies retrieved successfully.', [
            'policies' => $this->policyRepository->getPolicies(
                $_GET['status'] ?? null,
                $_GET['category'] ?? null
            )
        ]);
    }

    public function configurations(): void
    {
        $this->respond(200, true, 'Configurations retrieved successfully.', [
            'configurations' => $this->policyRepository->getConfigurations(
                $_GET['status'] ?? null,
                $_GET['category'] ?? null
            )
        ]);
    }

    public function storePolicy(): void
    {
        try {
            $data = $this->policyInput();
            $id = $this->policyRepository->createPolicy($data);
            $this->respond(201, true, 'Policy created successfully.', ['id' => $id]);
        } catch (\InvalidArgumentException $exception) {
            $this->respond(400, false, $exception->getMessage());
        } catch (\PDOException $exception) {
            $this->respond(409, false, 'A policy with this name already exists.');
        }
    }

    public function updatePolicy(int $id): void
    {
        try {
            $updated = $this->policyRepository->updatePolicy($id, $this->policyInput());
            $this->respond($updated ? 200 : 404, $updated, $updated ? 'Policy updated successfully.' : 'Policy not found.');
        } catch (\InvalidArgumentException $exception) {
            $this->respond(400, false, $exception->getMessage());
        } catch (\PDOException $exception) {
            $this->respond(409, false, 'A policy with this name already exists.');
        }
    }

    public function deletePolicy(int $id): void
    {
        $deleted = $this->policyRepository->deletePolicy($id);
        $this->respond($deleted ? 200 : 404, $deleted, $deleted ? 'Policy deleted successfully.' : 'Policy not found.');
    }

    public function storeConfiguration(): void
    {
        try {
            $id = $this->policyRepository->createConfiguration($this->configurationInput());
            $this->respond(201, true, 'Configuration created successfully.', ['id' => $id]);
        } catch (\InvalidArgumentException $exception) {
            $this->respond(400, false, $exception->getMessage());
        } catch (\PDOException $exception) {
            $this->respond(409, false, 'A configuration with this key already exists.');
        }
    }

    public function updateConfiguration(int $id): void
    {
        try {
            $updated = $this->policyRepository->updateConfiguration($id, $this->configurationInput());
            $this->respond($updated ? 200 : 404, $updated, $updated ? 'Configuration updated successfully.' : 'Configuration not found.');
        } catch (\InvalidArgumentException $exception) {
            $this->respond(400, false, $exception->getMessage());
        } catch (\PDOException $exception) {
            $this->respond(409, false, 'A configuration with this key already exists.');
        }
    }

    public function deleteConfiguration(int $id): void
    {
        $deleted = $this->policyRepository->deleteConfiguration($id);
        $this->respond($deleted ? 200 : 404, $deleted, $deleted ? 'Configuration deleted successfully.' : 'Configuration not found.');
    }

    private function policyInput(): array
    {
        $input = $this->input();
        $name = trim((string) ($input['name'] ?? ''));
        if ($name === '') {
            throw new \InvalidArgumentException('Policy name is required.');
        }

        return [
            'name' => $name,
            'category' => trim((string) ($input['category'] ?? 'General')) ?: 'General',
            'description' => trim((string) ($input['description'] ?? '')),
            'policy_value' => $input['policy_value'] ?? null,
            'status' => $this->status($input['status'] ?? 'ACTIVE')
        ];
    }

    private function configurationInput(): array
    {
        $input = $this->input();
        $key = trim((string) ($input['config_key'] ?? ''));
        if ($key === '') {
            throw new \InvalidArgumentException('Configuration key is required.');
        }

        return [
            'config_key' => $key,
            'config_value' => $input['config_value'] ?? null,
            'description' => trim((string) ($input['description'] ?? '')),
            'category' => trim((string) ($input['category'] ?? 'General')) ?: 'General',
            'status' => $this->status($input['status'] ?? 'ACTIVE')
        ];
    }

    private function status(mixed $status): string
    {
        $status = strtoupper(trim((string) $status));
        if (!in_array($status, ['ACTIVE', 'INACTIVE'], true)) {
            throw new \InvalidArgumentException('Status must be ACTIVE or INACTIVE.');
        }
        return $status;
    }

    private function input(): array
    {
        $input = json_decode(file_get_contents('php://input'), true);
        return is_array($input) ? $input : [];
    }

    private function respond(int $status, bool $success, string $message, array $data = []): void
    {
        http_response_code($status);
        echo json_encode(['success' => $success, 'message' => $message, 'data' => $data]);
    }
}

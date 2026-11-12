<?php

namespace App\Controllers\Hr;

use App\Services\Hr\HrDashboardService;
use App\Services\Hr\HrEmployeeService;
use App\Services\Hr\HrLeaveService;
use Throwable;

class HrController
{
    public function __construct(
        private HrDashboardService $service,
        private HrEmployeeService $employeeService,
        private HrLeaveService $leaveService
    )
    {
    }

    public function dashboard(): void
    {
        try {
            $this->sendResponse(200, true, 'HR dashboard data retrieved successfully.', $this->service->getDashboard());
        } catch (Throwable $exception) {
            $this->sendResponse(500, false, 'Unable to load HR dashboard data.');
        }
    }

    public function employees(): void
    {
        try {
            $filters = [
                'search' => $_GET['search'] ?? '',
                'status' => $_GET['status'] ?? '',
                'department' => $_GET['department'] ?? '',
                'designation' => $_GET['designation'] ?? '',
                'employment_type' => $_GET['employment_type'] ?? '',
                'page' => (int) ($_GET['page'] ?? 1),
                'per_page' => (int) ($_GET['per_page'] ?? 10),
            ];
            $this->sendResponse(200, true, 'HR employee directory retrieved successfully.', $this->employeeService->getDirectory($filters));
        } catch (Throwable $exception) {
            $this->sendResponse(500, false, 'Unable to load the HR employee directory.');
        }
    }

    public function attendance(): void
    {
        try {
            $filters = [
                'date' => $_GET['date'] ?? date('Y-m-d'),
                'search' => $_GET['search'] ?? '',
                'department' => $_GET['department'] ?? '',
                'page' => (int) ($_GET['page'] ?? 1),
                'per_page' => (int) ($_GET['per_page'] ?? 10),
                'trend_days' => (int) ($_GET['trend_days'] ?? 7),
            ];
            $this->sendResponse(200, true, 'HR attendance dashboard retrieved successfully.', $this->service->getAttendanceDashboard($filters));
        } catch (Throwable $exception) {
            $this->sendResponse(500, false, 'Unable to load HR attendance dashboard data.');
        }
    }

    public function recruitment(): void
    {
        try {
            $filters = [
                'search' => $_GET['search'] ?? '',
                'department' => $_GET['department'] ?? '',
                'status' => $_GET['status'] ?? '',
                'page' => (int) ($_GET['page'] ?? 1),
                'per_page' => (int) ($_GET['per_page'] ?? 10),
            ];
            $this->sendResponse(200, true, 'HR recruitment dashboard retrieved successfully.', $this->service->getRecruitmentDashboard($filters));
        } catch (Throwable $exception) {
            $this->sendResponse(500, false, 'Unable to load HR recruitment dashboard data.');
        }
    }

    public function updateJobOpeningStatus(): void
    {
        try {
            $input = json_decode(file_get_contents('php://input'), true) ?: $_POST;
            $id = (int) ($input['id'] ?? 0);
            $status = trim((string) ($input['status'] ?? ''));

            if ($id <= 0 || empty($status)) {
                $this->sendResponse(400, false, 'Job ID and status are required.');
                return;
            }

            $success = $this->service->updateJobOpeningStatus($id, $status);
            if ($success) {
                $this->sendResponse(200, true, 'Job status updated successfully.', ['id' => $id, 'status' => strtoupper($status)]);
            } else {
                $this->sendResponse(400, false, 'Failed to update job status.');
            }
        } catch (\Throwable $e) {
            $this->sendResponse(500, false, 'Error updating job status: ' . $e->getMessage());
        }
    }

    public function createJobOpening(): void
    {
        try {
            $input = json_decode(file_get_contents('php://input'), true) ?: $_POST;
            $title = trim((string) ($input['title'] ?? ''));
            if (empty($title)) {
                $this->sendResponse(400, false, 'Job title is required.');
                return;
            }

            $id = $this->service->createJobOpening($input);
            if ($id > 0) {
                $this->sendResponse(201, true, 'Job opening created successfully.', ['id' => $id]);
            } else {
                $this->sendResponse(400, false, 'Failed to create job opening.');
            }
        } catch (\Throwable $e) {
            $this->sendResponse(500, false, 'Error creating job opening: ' . $e->getMessage());
        }
    }

    public function updateJobOpening(): void
    {
        try {
            $input = json_decode(file_get_contents('php://input'), true) ?: $_POST;
            $id = (int) ($input['id'] ?? 0);

            if ($id <= 0) {
                $this->sendResponse(400, false, 'Job ID is required.');
                return;
            }

            $success = $this->service->updateJobOpening($id, $input);
            if ($success) {
                $this->sendResponse(200, true, 'Job opening updated successfully.', ['id' => $id]);
            } else {
                $this->sendResponse(400, false, 'Failed to update job opening.');
            }
        } catch (\Throwable $e) {
            $this->sendResponse(500, false, 'Error updating job opening: ' . $e->getMessage());
        }
    }

    public function updateCandidateStage(): void
    {
        try {
            $input = json_decode(file_get_contents('php://input'), true) ?: $_POST;
            $id = (int) ($input['id'] ?? 0);
            $stage = trim((string) ($input['stage'] ?? ''));

            if ($id <= 0 || empty($stage)) {
                $this->sendResponse(400, false, 'Candidate ID and stage are required.');
                return;
            }

            $success = $this->service->updateCandidateStage($id, $stage);
            if ($success) {
                $this->sendResponse(200, true, 'Candidate stage updated successfully.', ['id' => $id, 'stage' => strtoupper($stage)]);
            } else {
                $this->sendResponse(400, false, 'Failed to update candidate stage.');
            }
        } catch (\Throwable $e) {
            $this->sendResponse(500, false, 'Error updating candidate stage: ' . $e->getMessage());
        }
    }

    public function updateOnboardingStatus(): void
    {
        try {
            $input = json_decode(file_get_contents('php://input'), true) ?: $_POST;
            $id = (int) ($input['id'] ?? $input['record_id'] ?? 0);
            $status = trim((string) ($input['status'] ?? ''));

            if ($id <= 0 && !empty($input['employee_id'])) {
                $id = (int) preg_replace('/\D/', '', (string) $input['employee_id']);
            }

            if ($id <= 0 || empty($status)) {
                $this->sendResponse(400, false, 'Onboarding ID and status are required.');
                return;
            }

            $success = $this->service->updateOnboardingStatus($id, $status);
            if ($success) {
                $this->sendResponse(200, true, 'Onboarding status updated successfully.', ['id' => $id, 'status' => strtoupper($status)]);
            } else {
                $this->sendResponse(400, false, 'Failed to update onboarding status.');
            }
        } catch (\Throwable $e) {
            $this->sendResponse(500, false, 'Error updating onboarding status: ' . $e->getMessage());
        }
    }

    public function updateOnboardingRecord(): void
    {
        try {
            $input = json_decode(file_get_contents('php://input'), true) ?: $_POST;
            $id = (int) ($input['id'] ?? $input['record_id'] ?? 0);

            if ($id <= 0 && !empty($input['employee_id'])) {
                $id = (int) preg_replace('/\D/', '', (string) $input['employee_id']);
            }

            if ($id <= 0) {
                $this->sendResponse(400, false, 'Onboarding ID is required.');
                return;
            }

            $success = $this->service->updateOnboardingRecord($id, $input);
            if ($success) {
                $this->sendResponse(200, true, 'Onboarding details saved successfully.', ['id' => $id]);
            } else {
                $this->sendResponse(400, false, 'Failed to update onboarding details.');
            }
        } catch (\Throwable $e) {
            $this->sendResponse(500, false, 'Error updating onboarding details: ' . $e->getMessage());
        }
    }

    public function updateExitStatus(): void
    {
        try {
            $input = json_decode(file_get_contents('php://input'), true) ?: $_POST;
            $id = (int) ($input['id'] ?? $input['record_id'] ?? 0);
            $status = trim((string) ($input['status'] ?? ''));

            if ($id <= 0 && !empty($input['exit_id'])) {
                $id = (int) preg_replace('/\D/', '', (string) $input['exit_id']);
            }

            if ($id <= 0 || empty($status)) {
                $this->sendResponse(400, false, 'Exit clearance ID and status are required.');
                return;
            }

            $success = $this->service->updateExitStatus($id, $status);
            if ($success) {
                $this->sendResponse(200, true, 'Exit clearance status updated successfully.', ['id' => $id, 'status' => strtoupper($status)]);
            } else {
                $this->sendResponse(400, false, 'Failed to update exit clearance status.');
            }
        } catch (\Throwable $e) {
            $this->sendResponse(500, false, 'Error updating exit status: ' . $e->getMessage());
        }
    }

    public function updateExitRecord(): void
    {
        try {
            $input = json_decode(file_get_contents('php://input'), true) ?: $_POST;
            $id = (int) ($input['id'] ?? $input['record_id'] ?? 0);

            if ($id <= 0 && !empty($input['exit_id'])) {
                $id = (int) preg_replace('/\D/', '', (string) $input['exit_id']);
            }

            if ($id <= 0) {
                $this->sendResponse(400, false, 'Exit record ID is required.');
                return;
            }

            $success = $this->service->updateExitRecord($id, $input);
            if ($success) {
                $this->sendResponse(200, true, 'Exit clearance checklist saved successfully.', ['id' => $id]);
            } else {
                $this->sendResponse(400, false, 'Failed to update exit clearance.');
            }
        } catch (\Throwable $e) {
            $this->sendResponse(500, false, 'Error updating exit clearance: ' . $e->getMessage());
        }
    }

    public function onboardingExit(): void
    {
        try {
            $filters = ['search' => $_GET['search'] ?? '', 'page' => (int) ($_GET['page'] ?? 1), 'per_page' => (int) ($_GET['per_page'] ?? 10)];
            $this->sendResponse(200, true, 'HR onboarding and exit dashboard retrieved successfully.', $this->service->getOnboardingExitDashboard($filters));
        } catch (Throwable $exception) {
            $this->sendResponse(500, false, 'Unable to load HR onboarding and exit dashboard data.');
        }
    }

    public function payroll(): void
    {
        try {
            $filters = ['month' => $_GET['month'] ?? date('Y-m'), 'search' => $_GET['search'] ?? '', 'department' => $_GET['department'] ?? '', 'status' => $_GET['status'] ?? '', 'page' => (int) ($_GET['page'] ?? 1), 'per_page' => (int) ($_GET['per_page'] ?? 10)];
            $this->sendResponse(200, true, 'HR payroll dashboard retrieved successfully.', $this->service->getPayrollDashboard($filters));
        } catch (Throwable $exception) {
            $this->sendResponse(500, false, 'Unable to load HR payroll dashboard data.');
        }
    }

    public function leaveOverview(): void
    {
        try {
            $user = $GLOBALS['authenticated_user'] ?? null;
            $viewerRole = strtoupper((string) ($user->role ?? ''));

            $filters = [
                'search' => $_GET['search'] ?? '',
                'status' => $_GET['status'] ?? '',
                'department' => $_GET['department'] ?? '',
                'leave_type' => $_GET['leave_type'] ?? '',
                'month' => $_GET['month'] ?? date('Y-m'),
                'page' => (int) ($_GET['page'] ?? 1),
                'per_page' => (int) ($_GET['per_page'] ?? 10),
                'scope' => $_GET['scope'] ?? '',
                'viewer_role' => $viewerRole,
            ];
            $this->sendResponse(200, true, 'HR leave overview retrieved successfully.', $this->leaveService->getLeaveOverview($filters));
        } catch (Throwable $exception) {
            $this->sendResponse(500, false, 'Unable to load HR leave overview data.');
        }
    }

    public function approveLeave(int $id): void
    {
        try {
            $approvedBy = $this->authenticatedUserId();
            $success = $this->leaveService->approveRequest($id, $approvedBy);
            if ($success) {
                $this->sendResponse(200, true, 'Leave request approved successfully.');
            } else {
                $this->sendResponse(400, false, 'Failed to approve leave request. It may have already been processed.');
            }
        } catch (Throwable $exception) {
            $this->sendResponse(500, false, 'Error occurred while approving leave request.');
        }
    }

    public function rejectLeave(int $id): void
    {
        try {
            $input = json_decode(file_get_contents('php://input'), true) ?: [];
            $reason = $input['reason'] ?? '';
            if (empty(trim($reason))) {
                $this->sendResponse(400, false, 'Rejection reason is required.');
                return;
            }
            
            $rejectedBy = $this->authenticatedUserId();
            $success = $this->leaveService->rejectRequest($id, $rejectedBy, $reason);
            if ($success) {
                $this->sendResponse(200, true, 'Leave request rejected successfully.');
            } else {
                $this->sendResponse(400, false, 'Failed to reject leave request. It may have already been processed.');
            }
        } catch (Throwable $exception) {
            $this->sendResponse(500, false, 'Error occurred while rejecting leave request.');
        }
    }

    private function sendResponse(int $status, bool $success, string $message, array $data = []): void
    {
        http_response_code($status);
        header('Content-Type: application/json');
        echo json_encode([
            'success' => $success,
            'message' => $message,
            'data' => $data,
        ]);
    }

    private function authenticatedUserId(): int
    {
        $user = $GLOBALS['authenticated_user'] ?? null;
        $userId = is_object($user) ? ($user->sub ?? 0) : ($user['sub'] ?? 0);

        return max(0, (int) $userId);
    }
}

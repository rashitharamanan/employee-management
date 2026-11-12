<?php

namespace App\Controllers;

use App\Services\LeaveService;
use Exception;

class LeaveController
{
    public function __construct(private LeaveService $leaveService)
    {
    }

    public function types(): void
    {
        try {
            $types = $this->leaveService->getLeaveTypes();
            $this->sendResponse(200, true, 'Leave types retrieved successfully.', [
                'leave_types' => $types,
                'total' => count($types),
            ]);
        } catch (Exception $exception) {
            $this->sendResponse(400, false, $exception->getMessage());
        }
    }

    public function showType(int $id): void
    {
        try {
            $type = $this->leaveService->getLeaveType($id);
            $this->sendResponse(200, true, 'Leave type retrieved successfully.', ['leave_type' => $type]);
        } catch (Exception $exception) {
            $status = $exception->getMessage() === 'Leave type not found.' ? 404 : 400;
            $this->sendResponse($status, false, $exception->getMessage());
        }
    }

    public function storeType(): void
    {
        try {
            $input = json_decode(file_get_contents('php://input'), true);
            if (!is_array($input)) {
                $this->sendResponse(400, false, 'Invalid JSON request body.');
                return;
            }

            $type = $this->leaveService->createLeaveType($input);
            $this->sendResponse(201, true, 'Leave type created successfully.', ['leave_type' => $type]);
        } catch (Exception $exception) {
            $this->sendResponse(400, false, $exception->getMessage());
        }
    }

    public function updateType(int $id): void
    {
        try {
            $input = json_decode(file_get_contents('php://input'), true);
            if (!is_array($input)) {
                $this->sendResponse(400, false, 'Invalid JSON request body.');
                return;
            }

            $type = $this->leaveService->updateLeaveType($id, $input);
            $this->sendResponse(200, true, 'Leave type updated successfully.', ['leave_type' => $type]);
        } catch (Exception $exception) {
            $this->sendResponse(400, false, $exception->getMessage());
        }
    }

    public function requests(): void
    {
        try {
            $filters = [
                'status' => $_GET['status'] ?? '',
                'leave_type_id' => $_GET['leave_type_id'] ?? '',
                'employee_id' => $_GET['employee_id'] ?? '',
                'search' => $_GET['search'] ?? '',
            ];

            $requests = $this->leaveService->getLeaveRequests($filters);
            $this->sendResponse(200, true, 'Leave requests retrieved successfully.', [
                'leave_requests' => $requests,
                'total' => count($requests),
            ]);
        } catch (Exception $exception) {
            $this->sendResponse(400, false, $exception->getMessage());
        }
    }

    public function showRequest(int $id): void
    {
        try {
            $request = $this->leaveService->getLeaveRequest($id);
            $this->sendResponse(200, true, 'Leave request retrieved successfully.', ['leave_request' => $request]);
        } catch (Exception $exception) {
            $status = $exception->getMessage() === 'Leave request not found.' ? 404 : 400;
            $this->sendResponse($status, false, $exception->getMessage());
        }
    }

    public function storeRequest(): void
    {
        try {
            $input = json_decode(file_get_contents('php://input'), true);
            if (!is_array($input)) {
                $this->sendResponse(400, false, 'Invalid JSON request body.');
                return;
            }

            $request = $this->leaveService->createLeaveRequest($input);
            $this->sendResponse(201, true, 'Leave request created successfully.', ['leave_request' => $request]);
        } catch (Exception $exception) {
            $this->sendResponse(400, false, $exception->getMessage());
        }
    }

    public function approve(int $id): void
    {
        try {
            $input = json_decode(file_get_contents('php://input'), true) ?? [];
            $approvedBy = (int) ($input['approved_by'] ?? $GLOBALS['authenticated_user']->sub ?? 0);

            $request = $this->leaveService->approveLeaveRequest($id, $approvedBy);
            $this->sendResponse(200, true, 'Leave request approved successfully.', ['leave_request' => $request]);
        } catch (Exception $exception) {
            $this->sendResponse(400, false, $exception->getMessage());
        }
    }

    public function reject(int $id): void
    {
        try {
            $input = json_decode(file_get_contents('php://input'), true) ?? [];
            $approvedBy = (int) ($input['approved_by'] ?? $GLOBALS['authenticated_user']->sub ?? 0);
            $reason = trim((string) ($input['rejection_reason'] ?? $input['reason'] ?? ''));

            $request = $this->leaveService->rejectLeaveRequest($id, $approvedBy, $reason);
            $this->sendResponse(200, true, 'Leave request rejected successfully.', ['leave_request' => $request]);
        } catch (Exception $exception) {
            $this->sendResponse(400, false, $exception->getMessage());
        }
    }

    public function balances(int $employeeId): void
    {
        try {
            $balances = $this->leaveService->getEmployeeBalances($employeeId);
            $this->sendResponse(200, true, 'Leave balances retrieved successfully.', [
                'employee_id' => $employeeId,
                'leave_balances' => $balances,
                'total' => count($balances),
            ]);
        } catch (Exception $exception) {
            $this->sendResponse(400, false, $exception->getMessage());
        }
    }

    public function summary(): void
    {
        try {
            $summary = $this->leaveService->getDashboardSummary();
            $this->sendResponse(200, true, 'Leave summary retrieved successfully.', ['summary' => $summary]);
        } catch (Exception $exception) {
            $this->sendResponse(400, false, $exception->getMessage());
        }
    }

    private function sendResponse(int $statusCode, bool $success, string $message, array $data = []): void
    {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode([
            'success' => $success,
            'message' => $message,
            'data' => $data,
        ]);
    }
}

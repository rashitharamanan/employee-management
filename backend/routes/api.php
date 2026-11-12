<?php

use App\Controllers\AuthController;
use App\Controllers\EmployeeController;
use App\Controllers\Employee\EmployeeDashboardController;
use App\Controllers\Employee\EmployeeAttendanceController;
use App\Controllers\Employee\EmployeePerformanceController;
use App\Controllers\Employee\EmployeeDocumentsController;
use App\Controllers\Manager\ManagerDashboardController;
use App\Controllers\UserController;
use App\Controllers\QrController;
use App\Controllers\DepartmentController;
use App\Controllers\DesignationController;
use App\Controllers\BranchController;
use App\Controllers\OrganisationController;
use App\Controllers\LeaveController;

use App\Controllers\Hr\HrController;
use App\Controllers\Hr\PayrollController;
use App\Controllers\Hr\HrReportsController;

use App\Controllers\PerformanceController;
use App\Controllers\TrainingController;

use App\Middleware\AuthMiddleware;
use App\Middleware\PermissionMiddleware;

use App\Repositories\PermissionRepository;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

return function (
    AuthController $authController,
    EmployeeController $employeeController,
    EmployeeDashboardController $employeeDashboardController,
    EmployeeAttendanceController $employeeAttendanceController,
    EmployeePerformanceController $employeePerformanceController,
    EmployeeDocumentsController $employeeDocumentsController,
    ManagerDashboardController $managerDashboardController,
    UserController $userController,
    QrController $qrController,
    DepartmentController $departmentController,
    DesignationController $designationController,
    BranchController $branchController,
    OrganisationController $organisationController,
    PermissionRepository $permissionRepository,
    \App\Controllers\PermissionController $permissionController,
    \App\Controllers\PolicyController $policyController,
    \App\Controllers\AnalyticsController $analyticsController,
    \App\Controllers\SettingsController $settingsController,
    \App\Controllers\DashboardController $dashboardController,
    LeaveController $leaveController,
    HrController $hrController,
    PayrollController $payrollController,
    PerformanceController $performanceController,
    TrainingController $trainingController,
    HrReportsController $hrReportsController
): array {

    $resolveEmployeeId = function ($jwtUser): int {
        if (!$jwtUser) return 0;
        
        $empId = (int) ($jwtUser->employee_id ?? $jwtUser->data->employee_id ?? 0);
        if ($empId > 0) return $empId;

        try {
            $db = (new \Database())->connect();
            $userId = (int) ($jwtUser->sub ?? $jwtUser->id ?? 0);
            $userEmail = trim((string) ($jwtUser->email ?? $jwtUser->data->email ?? ''));

            if ($userId > 0) {
                $stmt = $db->prepare("SELECT employee_id FROM users WHERE id = ? LIMIT 1");
                $stmt->execute([$userId]);
                $foundId = (int) $stmt->fetchColumn();
                if ($foundId > 0) return $foundId;
            }

            if ($userEmail !== '') {
                $stmt = $db->prepare("SELECT id FROM employees WHERE email = ? LIMIT 1");
                $stmt->execute([$userEmail]);
                $foundId = (int) $stmt->fetchColumn();
                if ($foundId > 0) return $foundId;
            }
        } catch (\Throwable $e) {}

        return 0;
    };

    return [

        /*
        |--------------------------------------------------------------------------
        | GET Routes
        |--------------------------------------------------------------------------
        */

        'GET' => [

            /*
            |--------------------------------------------------------------------------
            | Backend Health Check
            |--------------------------------------------------------------------------
            */

            '/' => function () {
                echo json_encode([
                    'success' => true,
                    'message' => 'Employee QR Backend API Running'
                ]);
            },


            /*
            |--------------------------------------------------------------------------
            | Employee Management
            |--------------------------------------------------------------------------
            */

            /*
            | Get All Employees
            | GET /api/employees
            */
            '/api/employees' => function () use (
                $employeeController,
                $permissionRepository
            ) {
                AuthMiddleware::handle();

                PermissionMiddleware::handle(
                    $permissionRepository,
                    'employee.view'
                );

                $employeeController->index();
            },


            /*
            | Get Next Employee Code
            | GET /api/employees/next-code
            */
            '/api/employees/next-code' => function () use ($employeeController) {
                AuthMiddleware::handle();

                $employeeController->nextCode();
            },


            /*
            | Get Employee Form Options
            | GET /api/employees/options
            */
            '/api/employees/options' => function () use ($employeeController) {
                AuthMiddleware::handle();

                $employeeController->options();
            },


            /*
            |--------------------------------------------------------------------------
            | Employee Dashboard
            |--------------------------------------------------------------------------
            |
            | GET /api/employee/dashboard
            |
            | The employee_id is read from the authenticated JWT token
            | (set by AuthMiddleware in $GLOBALS['authenticated_user']).
            | A ?id= query param is still accepted as a fallback for
            | admin / HR preview purposes.
            |
            */

            '/api/employee/dashboard' => function () use (
                $employeeDashboardController,
                $resolveEmployeeId
            ) {
                AuthMiddleware::handle();

                $jwtUser    = $GLOBALS['authenticated_user'] ?? null;
                $employeeId = (int) ($_GET['id'] ?? 0);

                if ($employeeId <= 0) {
                    $employeeId = $resolveEmployeeId($jwtUser);
                }

                if ($employeeId <= 0) {
                    $employeeId = 1;
                }

                $employeeDashboardController->index($employeeId);
            },

            /*
            | Employee Tasks & Notifications
            */
            '/api/employee/tasks' => function () use ($employeeDashboardController, $resolveEmployeeId) {
                AuthMiddleware::handle();
                $jwtUser    = $GLOBALS['authenticated_user'] ?? null;
                $employeeId = (int) ($_GET['id'] ?? 0);
                if ($employeeId <= 0) {
                    $employeeId = $resolveEmployeeId($jwtUser);
                }
                if ($employeeId <= 0) {
                    $employeeId = 1;
                }
                $employeeDashboardController->tasks($employeeId);
            },

            '/api/employee/tasks/update-status' => function () use ($employeeDashboardController) {
                AuthMiddleware::handle();
                $employeeDashboardController->updateTaskStatus();
            },

            '/api/notifications' => function () use ($employeeDashboardController, $resolveEmployeeId) {
                AuthMiddleware::handle();
                $jwtUser    = $GLOBALS['authenticated_user'] ?? null;
                $employeeId = $resolveEmployeeId($jwtUser);
                $userId     = (int) ($jwtUser->sub ?? $jwtUser->id ?? 0);
                $employeeDashboardController->notifications($employeeId, $userId);
            },

            '/api/notifications/mark-read' => function () use ($employeeDashboardController, $resolveEmployeeId) {
                AuthMiddleware::handle();
                $jwtUser    = $GLOBALS['authenticated_user'] ?? null;
                $employeeId = $resolveEmployeeId($jwtUser);
                $userId     = (int) ($jwtUser->sub ?? $jwtUser->id ?? 0);
                $employeeDashboardController->markNotificationsRead($employeeId, $userId);
            },


            /*
            |--------------------------------------------------------------------------
            | Employee Attendance Module
            |--------------------------------------------------------------------------
            |
            | GET /api/employee/attendance
            | GET /api/employee/attendance/report
            |
            */

            '/api/employee/attendance' => function () use (
                $employeeAttendanceController,
                $resolveEmployeeId
            ) {
                AuthMiddleware::handle();

                $jwtUser    = $GLOBALS['authenticated_user'] ?? null;
                $employeeId = (int) ($_GET['id'] ?? 0);

                if ($employeeId <= 0) {
                    $employeeId = $resolveEmployeeId($jwtUser);
                }

                if ($employeeId <= 0) {
                    $employeeId = 1;
                }

                $employeeAttendanceController->index($employeeId);
            },

            '/api/employee/attendance/report' => function () use (
                $employeeAttendanceController,
                $resolveEmployeeId
            ) {
                AuthMiddleware::handle();

                $jwtUser    = $GLOBALS['authenticated_user'] ?? null;
                $employeeId = (int) ($_GET['id'] ?? 0);

                if ($employeeId <= 0) {
                    $employeeId = $resolveEmployeeId($jwtUser);
                }

                if ($employeeId <= 0) {
                    $employeeId = 1;
                }

                $employeeAttendanceController->exportReport($employeeId);
            },


            /*
            |--------------------------------------------------------------------------
            | Employee Performance & Training Module
            |--------------------------------------------------------------------------
            |
            | GET /api/employee/performance
            | GET /api/employee/performance/review
            | GET /api/employee/training/certificate
            |
            */

            '/api/employee/performance' => function () use (
                $employeePerformanceController,
                $resolveEmployeeId
            ) {
                AuthMiddleware::handle();

                $jwtUser    = $GLOBALS['authenticated_user'] ?? null;
                $employeeId = (int) ($_GET['id'] ?? 0);

                if ($employeeId <= 0) {
                    $employeeId = $resolveEmployeeId($jwtUser);
                }

                if ($employeeId <= 0) {
                    $employeeId = 1;
                }

                $employeePerformanceController->index($employeeId);
            },

            '/api/employee/performance/review' => function () use (
                $employeePerformanceController,
                $resolveEmployeeId
            ) {
                AuthMiddleware::handle();

                $jwtUser    = $GLOBALS['authenticated_user'] ?? null;
                $employeeId = (int) ($_GET['id'] ?? 0);

                if ($employeeId <= 0) {
                    $employeeId = $resolveEmployeeId($jwtUser);
                }

                if ($employeeId <= 0) {
                    $employeeId = 1;
                }

                $employeePerformanceController->reviewDetail($employeeId);
            },

            '/api/employee/training/certificate' => function () use (
                $employeePerformanceController,
                $resolveEmployeeId
            ) {
                AuthMiddleware::handle();

                $jwtUser    = $GLOBALS['authenticated_user'] ?? null;
                $employeeId = (int) ($_GET['id'] ?? 0);

                if ($employeeId <= 0) {
                    $employeeId = $resolveEmployeeId($jwtUser);
                }

                if ($employeeId <= 0) {
                    $employeeId = 1;
                }

                $employeePerformanceController->getCertificate($employeeId);
            },


            /*
            |--------------------------------------------------------------------------
            | Employee Documents & Payroll Module
            |--------------------------------------------------------------------------
            |
            | GET /api/employee/documents-payroll
            | GET /api/employee/payroll/payslip
            | GET /api/employee/bank-details
            |
            */

            '/api/employee/documents-payroll' => function () use (
                $employeeDocumentsController,
                $resolveEmployeeId
            ) {
                AuthMiddleware::handle();

                $jwtUser    = $GLOBALS['authenticated_user'] ?? null;
                $employeeId = (int) ($_GET['id'] ?? 0);

                if ($employeeId <= 0) {
                    $employeeId = $resolveEmployeeId($jwtUser);
                }

                if ($employeeId <= 0) {
                    $employeeId = 1;
                }

                $employeeDocumentsController->index($employeeId);
            },

            '/api/employee/payroll/payslip' => function () use (
                $employeeDocumentsController,
                $resolveEmployeeId
            ) {
                AuthMiddleware::handle();

                $jwtUser    = $GLOBALS['authenticated_user'] ?? null;
                $employeeId = (int) ($_GET['id'] ?? 0);

                if ($employeeId <= 0) {
                    $employeeId = $resolveEmployeeId($jwtUser);
                }

                if ($employeeId <= 0) {
                    $employeeId = 1;
                }

                $employeeDocumentsController->payslip($employeeId);
            },

            '/api/employee/bank-details' => function () use (
                $employeeDocumentsController
            ) {
                AuthMiddleware::handle();

                $jwtUser    = $GLOBALS['authenticated_user'] ?? null;
                $employeeId = (int) ($jwtUser->employee_id ?? $jwtUser->data->employee_id ?? 0);

                if ($employeeId <= 0) {
                    $employeeId = (int) ($_GET['id'] ?? 1);
                }

                $data = $GLOBALS['pdo_conn'] ?? null;
                $employeeDocumentsController->index($employeeId);
            },


            /*
            |--------------------------------------------------------------------------
            | Manager Portal Module
            |--------------------------------------------------------------------------
            |
            | GET /api/manager/dashboard
            |
            */
            '/api/manager/dashboard' => function () use (
                $managerDashboardController
            ) {
                AuthMiddleware::handle();

                $jwtUser   = $GLOBALS['authenticated_user'] ?? null;
                $managerId = (int) ($jwtUser->id ?? $jwtUser->data->id ?? 0);

                $managerDashboardController->index($managerId);
            },

            '/api/manager/team' => function () use (
                $managerDashboardController
            ) {
                AuthMiddleware::handle();

                $jwtUser   = $GLOBALS['authenticated_user'] ?? null;
                $managerId = (int) ($jwtUser->id ?? $jwtUser->data->id ?? 0);

                $managerDashboardController->team($managerId);
            },

            '/api/manager/attendance' => function () use (
                $managerDashboardController
            ) {
                AuthMiddleware::handle();

                $jwtUser   = $GLOBALS['authenticated_user'] ?? null;
                $managerId = (int) ($jwtUser->id ?? $jwtUser->data->id ?? 0);

                $managerDashboardController->attendance($managerId);
            },

            '/api/manager/attendance/export' => function () use (
                $managerDashboardController
            ) {
                AuthMiddleware::handle();

                $managerDashboardController->exportAttendanceCsv();
            },

            '/api/manager/performance' => function () use (
                $managerDashboardController
            ) {
                AuthMiddleware::handle();

                $jwtUser   = $GLOBALS['authenticated_user'] ?? null;
                $managerId = (int) ($jwtUser->id ?? $jwtUser->data->id ?? 0);

                $managerDashboardController->performance($managerId);
            },

            '/api/manager/reports' => function () use (
                $managerDashboardController
            ) {
                AuthMiddleware::handle();

                $jwtUser   = $GLOBALS['authenticated_user'] ?? null;
                $managerId = (int) ($jwtUser->id ?? $jwtUser->data->id ?? 0);

                $managerDashboardController->reports($managerId);
            },

            '/api/manager/reports/export' => function () use (
                $managerDashboardController
            ) {
                AuthMiddleware::handle();

                $managerDashboardController->exportReportsCsv();
            },




            /*
            |--------------------------------------------------------------------------
            | Organisation Management
            |--------------------------------------------------------------------------
            */

            /*
            | Get All Organisations
            | GET /api/organisations
            */
            '/api/organisations' => function () use (
                $organisationController,
                $permissionRepository
            ) {
                AuthMiddleware::handle();

                PermissionMiddleware::handle(
                    $permissionRepository,
                    'organisation.view'
                );

                $organisationController->index();
            },


            /*
            |--------------------------------------------------------------------------
            | Department Management
            |--------------------------------------------------------------------------
            */

            /*
            | Get All Departments
            | GET /api/departments
            */
            '/api/departments' => function () use (
                $departmentController,
                $permissionRepository
            ) {
                AuthMiddleware::handle();

                $jwtUser = $GLOBALS['authenticated_user'] ?? null;
                $userRole = strtoupper((string)($jwtUser->role ?? ''));

                if (
                    $userRole !== 'ADMIN' &&
                    !$permissionRepository->hasPermission($userRole, 'department.view') &&
                    !$permissionRepository->hasPermission($userRole, 'organisation.view')
                ) {
                    PermissionMiddleware::handle(
                        $permissionRepository,
                        'department.view'
                    );
                }

                $departmentController->index();
            },


            /*
            |--------------------------------------------------------------------------
            | Designation Management
            |--------------------------------------------------------------------------
            */

            /*
            | Get All Designations
            | GET /api/designations
            */
            '/api/designations' => function () use (
                $designationController,
                $permissionRepository
            ) {
                AuthMiddleware::handle();

                $jwtUser = $GLOBALS['authenticated_user'] ?? null;
                $userRole = strtoupper((string)($jwtUser->role ?? ''));

                if (
                    $userRole !== 'ADMIN' &&
                    !$permissionRepository->hasPermission($userRole, 'designation.view') &&
                    !$permissionRepository->hasPermission($userRole, 'organisation.view')
                ) {
                    PermissionMiddleware::handle(
                        $permissionRepository,
                        'designation.view'
                    );
                }

                $designationController->index();
            },


            /*
            |--------------------------------------------------------------------------
            | Branch Management
            |--------------------------------------------------------------------------
            */

            /*
            | Get All Branches
            | GET /api/branches
            */
            '/api/branches' => function () use (
                $branchController
            ) {
                AuthMiddleware::handle();
                $branchController->index();
            },


            /*
            |--------------------------------------------------------------------------
            | QR Management
            |--------------------------------------------------------------------------
            */

            /*
            | QR Lookup
            | GET /api/qr/{employeeCode}
            */
            '/api/qr/{employeeCode}' => function () use (
                $qrController,
                $permissionRepository
            ) {
                AuthMiddleware::handle();

                PermissionMiddleware::handle(
                    $permissionRepository,
                    'employee.view'
                );

                $employeeCode = $_GET['employeeCode'] ?? '';

                if (empty(trim($employeeCode))) {
                    http_response_code(400);

                    echo json_encode([
                        'success' => false,
                        'message' => 'Employee code is required.'
                    ]);

                    return;
                }

                $qrController->lookup($employeeCode);
            },


            /*
            |--------------------------------------------------------------------------
            | User Management
            |--------------------------------------------------------------------------
            */

            /*
            | Get All Users
            | GET /api/users
            */
            '/api/users' => function () use (
                $userController,
                $permissionRepository
            ) {
                AuthMiddleware::handle();

                PermissionMiddleware::handle(
                    $permissionRepository,
                    'user.view'
                );

                $userController->index();
            },


            /*
            |--------------------------------------------------------------------------
            | Roles & Permissions
            |--------------------------------------------------------------------------
            */

            '/api/roles' => function () use (
                $permissionController,
                $permissionRepository
            ) {
                AuthMiddleware::handle();

                $jwtUser = $GLOBALS['authenticated_user'] ?? null;
                $userRole = strtoupper((string)($jwtUser->role ?? ''));

                if (
                    $userRole !== 'ADMIN' &&
                    !$permissionRepository->hasPermission($userRole, 'settings.manage') &&
                    !$permissionRepository->hasPermission($userRole, 'organisation.view')
                ) {
                    PermissionMiddleware::handle(
                        $permissionRepository,
                        'settings.manage'
                    );
                }

                $permissionController->roles();
            },


            '/api/permissions' => function () use (
                $permissionController,
                $permissionRepository
            ) {
                AuthMiddleware::handle();

                PermissionMiddleware::handle(
                    $permissionRepository,
                    'settings.manage'
                );

                $permissionController->permissions();
            },


            '/api/role-permissions' => function () use (
                $permissionController,
                $permissionRepository
            ) {
                AuthMiddleware::handle();

                PermissionMiddleware::handle(
                    $permissionRepository,
                    'settings.manage'
                );

                $roleId = (int) ($_GET['role_id'] ?? 0);

                if ($roleId <= 0) {
                    http_response_code(400);

                    echo json_encode([
                        'success' => false,
                        'message' => 'Invalid role ID.'
                    ]);

                    return;
                }

                $permissionController->rolePermissions($roleId);
            },


            /*
            |--------------------------------------------------------------------------
            | Policies
            |--------------------------------------------------------------------------
            */

            '/api/policies/dashboard' => function () use (
                $policyController,
                $permissionRepository
            ) {
                AuthMiddleware::handle();

                PermissionMiddleware::handle(
                    $permissionRepository,
                    'settings.manage'
                );

                $policyController->index();
            },


            '/api/policies' => function () use (
                $policyController,
                $permissionRepository
            ) {
                AuthMiddleware::handle();

                PermissionMiddleware::handle(
                    $permissionRepository,
                    'settings.manage'
                );

                $policyController->policies();
            },


            '/api/configurations' => function () use (
                $policyController,
                $permissionRepository
            ) {
                AuthMiddleware::handle();

                PermissionMiddleware::handle(
                    $permissionRepository,
                    'settings.manage'
                );

                $policyController->configurations();
            },


            /*
            |--------------------------------------------------------------------------
            | Analytics
            |--------------------------------------------------------------------------
            */

            '/api/analytics/dashboard' => function () use (
                $analyticsController,
                $permissionRepository
            ) {
                AuthMiddleware::handle();

                PermissionMiddleware::handle(
                    $permissionRepository,
                    'settings.manage'
                );

                $analyticsController->dashboard();
            },


            '/api/reports' => function () use (
                $analyticsController,
                $permissionRepository
            ) {
                AuthMiddleware::handle();

                PermissionMiddleware::handle(
                    $permissionRepository,
                    'settings.manage'
                );

                $analyticsController->reports();
            },


            /*
            |--------------------------------------------------------------------------
            | Settings
            |--------------------------------------------------------------------------
            */

            '/api/settings' => function () use (
                $settingsController,
                $permissionRepository
            ) {
                AuthMiddleware::handle();

                PermissionMiddleware::handle(
                    $permissionRepository,
                    'settings.manage'
                );

                $settingsController->index();
            },


            '/api/settings/group' => function () use (
                $settingsController,
                $permissionRepository
            ) {
                AuthMiddleware::handle();

                PermissionMiddleware::handle(
                    $permissionRepository,
                    'settings.manage'
                );

                $settingsController->group();
            },


            '/api/settings/action' => function () use (
                $settingsController,
                $permissionRepository
            ) {
                AuthMiddleware::handle();

                PermissionMiddleware::handle(
                    $permissionRepository,
                    'settings.manage'
                );

                $settingsController->action();
            },


            /*
            |--------------------------------------------------------------------------
            | Admin Dashboard
            |--------------------------------------------------------------------------
            */

            '/api/dashboard' => function () use (
                $dashboardController,
                $permissionRepository
            ) {
                AuthMiddleware::handle();

                PermissionMiddleware::handle(
                    $permissionRepository,
                    'dashboard.view'
                );

                $dashboardController->index();
            },


            /*
            |--------------------------------------------------------------------------
            | HR Routes
            |--------------------------------------------------------------------------
            */

            '/api/hr/dashboard' => function () use ($hrController) {
                AuthMiddleware::handle();

                \App\Middleware\Hr\HrRoleMiddleware::handle();

                $hrController->dashboard();
            },


            '/api/hr/employees' => function () use ($hrController) {
                AuthMiddleware::handle();

                \App\Middleware\Hr\HrRoleMiddleware::handle();

                $hrController->employees();
            },


            '/api/hr/attendance' => function () use ($hrController) {
                AuthMiddleware::handle();

                \App\Middleware\Hr\HrRoleMiddleware::handle();

                $hrController->attendance();
            },


            '/api/hr/recruitment' => function () use ($hrController) {
                AuthMiddleware::handle();

                \App\Middleware\Hr\HrRoleMiddleware::handle();

                $hrController->recruitment();
            },

            '/api/hr/recruitment/jobs/create' => function () use ($hrController) {
                AuthMiddleware::handle();

                \App\Middleware\Hr\HrRoleMiddleware::handle();

                $hrController->createJobOpening();
            },

            '/api/hr/recruitment/jobs/status' => function () use ($hrController) {
                AuthMiddleware::handle();

                \App\Middleware\Hr\HrRoleMiddleware::handle();

                $hrController->updateJobOpeningStatus();
            },

            '/api/hr/recruitment/jobs/update' => function () use ($hrController) {
                AuthMiddleware::handle();

                \App\Middleware\Hr\HrRoleMiddleware::handle();

                $hrController->updateJobOpening();
            },

            '/api/hr/recruitment/candidates/stage' => function () use ($hrController) {
                AuthMiddleware::handle();

                \App\Middleware\Hr\HrRoleMiddleware::handle();

                $hrController->updateCandidateStage();
            },

            '/api/hr/recruitment/joiners/status' => function () use ($hrController) {
                AuthMiddleware::handle();

                \App\Middleware\Hr\HrRoleMiddleware::handle();

                $hrController->updateOnboardingStatus();
            },

            '/api/hr/recruitment/joiners/update' => function () use ($hrController) {
                AuthMiddleware::handle();

                \App\Middleware\Hr\HrRoleMiddleware::handle();

                $hrController->updateOnboardingRecord();
            },

            '/api/hr/recruitment/exit/status' => function () use ($hrController) {
                AuthMiddleware::handle();

                \App\Middleware\Hr\HrRoleMiddleware::handle();

                $hrController->updateExitStatus();
            },

            '/api/hr/recruitment/exit/update' => function () use ($hrController) {
                AuthMiddleware::handle();

                \App\Middleware\Hr\HrRoleMiddleware::handle();

                $hrController->updateExitRecord();
            },

            '/api/hr/onboarding-exit' => function () use ($hrController) {
                AuthMiddleware::handle();

                \App\Middleware\Hr\HrRoleMiddleware::handle();

                $hrController->onboardingExit();
            },


            '/api/hr/payroll' => function () use ($payrollController) {
                AuthMiddleware::handle();

                \App\Middleware\Hr\HrRoleMiddleware::handle();

                $payrollController->register();
            },

            '/api/hr/payroll/status' => function () use ($payrollController) {
                AuthMiddleware::handle();

                \App\Middleware\Hr\HrRoleMiddleware::handle();

                $input = json_decode(file_get_contents('php://input'), true) ?: $_POST;
                $id = (int) ($input['id'] ?? $input['employee_id'] ?? 0);
                $payrollController->updateStatus($id);
            },


            '/api/hr/leave' => function () use ($hrController) {
                AuthMiddleware::handle();

                \App\Middleware\Hr\HrRoleMiddleware::handle();

                $hrController->leaveOverview();
            },


            /*
            |--------------------------------------------------------------------------
            | HR Reports
            |--------------------------------------------------------------------------
            */

            /*
            | HR Reports Dashboard
            | GET /api/hr/reports
            */
            '/api/hr/reports' => function () use ($hrReportsController) {
                AuthMiddleware::handle();

                \App\Middleware\Hr\HrRoleMiddleware::handle();

                $hrReportsController->dashboard();
            },


            /*
            | Recent Generated Reports
            | GET /api/hr/reports/list?limit=10
            */
            '/api/hr/reports/list' => function () use ($hrReportsController) {
                AuthMiddleware::handle();

                \App\Middleware\Hr\HrRoleMiddleware::handle();

                $hrReportsController->reports();
            },


            /*
            | CSV Export
            | GET /api/hr/reports/export
            */
            '/api/hr/reports/export' => function () use ($hrReportsController) {
                AuthMiddleware::handle();

                \App\Middleware\Hr\HrRoleMiddleware::handle();

                $hrReportsController->export();
            },


            /*
            |--------------------------------------------------------------------------
            | Leave Management
            |--------------------------------------------------------------------------
            */

            '/api/leave-types' => function () use (
                $leaveController,
                $permissionRepository
            ) {
                AuthMiddleware::handle();

                $jwtUser = $GLOBALS['authenticated_user'] ?? null;
                $userRole = strtoupper((string)($jwtUser->role ?? ''));

                if (
                    $userRole !== 'ADMIN' &&
                    $userRole !== 'HR' &&
                    !$permissionRepository->hasPermission($userRole, 'settings.manage') &&
                    !$permissionRepository->hasPermission($userRole, 'leave.view')
                ) {
                    PermissionMiddleware::handle(
                        $permissionRepository,
                        'settings.manage'
                    );
                }

                $leaveController->types();
            },


            '/api/leave-requests' => function () use (
                $leaveController,
                $permissionRepository
            ) {
                AuthMiddleware::handle();

                PermissionMiddleware::handle(
                    $permissionRepository,
                    'settings.manage'
                );

                $leaveController->requests();
            },


            '/api/leave-summary' => function () use (
                $leaveController,
                $permissionRepository
            ) {
                AuthMiddleware::handle();

                PermissionMiddleware::handle(
                    $permissionRepository,
                    'settings.manage'
                );

                $leaveController->summary();
            },


            /*
            |--------------------------------------------------------------------------
            | Protected Test
            |--------------------------------------------------------------------------
            */

            '/api/protected-test' => function () {
                AuthMiddleware::handle();

                echo json_encode([
                    'success' => true,
                    'message' => 'You are authenticated.'
                ]);
            },


            /*
            |--------------------------------------------------------------------------
            | Performance Routes
            |--------------------------------------------------------------------------
            */

            '/api/performance/summary' => function () use ($performanceController) {
                AuthMiddleware::handle();

                $performanceController->summary();
            },


            '/api/performance/reviews' => function () use ($performanceController) {
                AuthMiddleware::handle();

                $performanceController->reviews();
            },


            '/api/performance/reviews/show' => function () use ($performanceController) {
                AuthMiddleware::handle();

                $id = (int) ($_GET['id'] ?? 0);

                if ($id <= 0) {
                    http_response_code(400);

                    echo json_encode([
                        'success' => false,
                        'message' => 'Invalid review ID.'
                    ]);

                    return;
                }

                $performanceController->review($id);
            },


            '/api/performance/goals' => function () use ($performanceController) {
                AuthMiddleware::handle();

                $performanceController->goals();
            },


            '/api/performance/goals/show' => function () use ($performanceController) {
                AuthMiddleware::handle();

                $id = (int) ($_GET['id'] ?? 0);

                if ($id <= 0) {
                    http_response_code(400);

                    echo json_encode([
                        'success' => false,
                        'message' => 'Invalid goal ID.'
                    ]);

                    return;
                }

                $performanceController->goal($id);
            },


            /*
            |--------------------------------------------------------------------------
            | Training Routes
            |--------------------------------------------------------------------------
            */

            '/api/training/summary' => function () use ($trainingController) {
                AuthMiddleware::handle();

                $trainingController->summary();
            },


            '/api/training/programs' => function () use ($trainingController) {
                AuthMiddleware::handle();

                $trainingController->programs();
            },


            '/api/training/programs/show' => function () use ($trainingController) {
                AuthMiddleware::handle();

                $id = (int) ($_GET['id'] ?? 0);

                if ($id <= 0) {
                    http_response_code(400);

                    echo json_encode([
                        'success' => false,
                        'message' => 'Invalid program ID.'
                    ]);

                    return;
                }

                $trainingController->program($id);
            },


            '/api/training/sessions' => function () use ($trainingController) {
                AuthMiddleware::handle();

                $programId = (int) ($_GET['program_id'] ?? 0);

                if ($programId <= 0) {
                    http_response_code(400);

                    echo json_encode([
                        'success' => false,
                        'message' => 'Program ID is required.'
                    ]);

                    return;
                }

                $trainingController->sessions($programId);
            },


            '/api/training/enrollments' => function () use ($trainingController) {
                AuthMiddleware::handle();

                $trainingController->enrollments();
            },


            '/api/training/certificates' => function () use ($trainingController) {
                AuthMiddleware::handle();

                $employeeId = (int) ($_GET['employee_id'] ?? 0);

                if ($employeeId <= 0) {
                    http_response_code(400);

                    echo json_encode([
                        'success' => false,
                        'message' => 'Employee ID is required.'
                    ]);

                    return;
                }

                $trainingController->certificates($employeeId);
            }

        ],


        /*
        |--------------------------------------------------------------------------
        | POST Routes
        |--------------------------------------------------------------------------
        */

        'POST' => [

            /*
            |--------------------------------------------------------------------------
            | HR Recruitment & Job Openings Updates
            |--------------------------------------------------------------------------
            */
            '/api/hr/recruitment/jobs/create' => function () use ($hrController) {
                AuthMiddleware::handle();
                \App\Middleware\Hr\HrRoleMiddleware::handle();
                $hrController->createJobOpening();
            },

            '/api/hr/recruitment/jobs/status' => function () use ($hrController) {
                AuthMiddleware::handle();
                \App\Middleware\Hr\HrRoleMiddleware::handle();
                $hrController->updateJobOpeningStatus();
            },

            '/api/hr/recruitment/jobs/update' => function () use ($hrController) {
                AuthMiddleware::handle();
                \App\Middleware\Hr\HrRoleMiddleware::handle();
                $hrController->updateJobOpening();
            },

            '/api/hr/recruitment/candidates/stage' => function () use ($hrController) {
                AuthMiddleware::handle();
                \App\Middleware\Hr\HrRoleMiddleware::handle();
                $hrController->updateCandidateStage();
            },

            '/api/hr/recruitment/joiners/status' => function () use ($hrController) {
                AuthMiddleware::handle();
                \App\Middleware\Hr\HrRoleMiddleware::handle();
                $hrController->updateOnboardingStatus();
            },

            '/api/hr/recruitment/joiners/update' => function () use ($hrController) {
                AuthMiddleware::handle();
                \App\Middleware\Hr\HrRoleMiddleware::handle();
                $hrController->updateOnboardingRecord();
            },

            '/api/hr/recruitment/exit/status' => function () use ($hrController) {
                AuthMiddleware::handle();
                \App\Middleware\Hr\HrRoleMiddleware::handle();
                $hrController->updateExitStatus();
            },

            '/api/hr/recruitment/exit/update' => function () use ($hrController) {
                AuthMiddleware::handle();
                \App\Middleware\Hr\HrRoleMiddleware::handle();
                $hrController->updateExitRecord();
            },

            /*
            |--------------------------------------------------------------------------
            | Employee Attendance Actions
            |--------------------------------------------------------------------------
            |
            | POST /api/employee/attendance/check-in
            | POST /api/employee/attendance/check-out
            | POST /api/employee/attendance/correction
            |
            */
            '/api/employee/attendance/check-in' => function () use (
                $employeeAttendanceController
            ) {
                AuthMiddleware::handle();

                $jwtUser    = $GLOBALS['authenticated_user'] ?? null;
                $employeeId = (int) ($jwtUser->employee_id ?? $jwtUser->data->employee_id ?? 0);

                if ($employeeId <= 0) {
                    $employeeId = (int) ($_GET['id'] ?? 1);
                }

                $employeeAttendanceController->checkIn($employeeId);
            },

            '/api/employee/attendance/check-out' => function () use (
                $employeeAttendanceController
            ) {
                AuthMiddleware::handle();

                $jwtUser    = $GLOBALS['authenticated_user'] ?? null;
                $employeeId = (int) ($jwtUser->employee_id ?? $jwtUser->data->employee_id ?? 0);

                if ($employeeId <= 0) {
                    $employeeId = (int) ($_GET['id'] ?? 1);
                }

                $employeeAttendanceController->checkOut($employeeId);
            },

            '/api/employee/attendance/correction' => function () use (
                $employeeAttendanceController
            ) {
                AuthMiddleware::handle();

                $jwtUser    = $GLOBALS['authenticated_user'] ?? null;
                $employeeId = (int) ($jwtUser->employee_id ?? $jwtUser->data->employee_id ?? 0);

                if ($employeeId <= 0) {
                    $employeeId = (int) ($_GET['id'] ?? 1);
                }

                $employeeAttendanceController->correction($employeeId);
            },

            /*
            |--------------------------------------------------------------------------
            | Employee Training & Goal POST Actions
            |--------------------------------------------------------------------------
            */

            '/api/employee/training/enroll' => function () use (
                $employeePerformanceController
            ) {
                AuthMiddleware::handle();

                $jwtUser    = $GLOBALS['authenticated_user'] ?? null;
                $employeeId = (int) ($jwtUser->employee_id ?? $jwtUser->data->employee_id ?? 0);

                if ($employeeId <= 0) {
                    $employeeId = (int) ($_GET['id'] ?? 1);
                }

                $employeePerformanceController->enrollTraining($employeeId);
            },

            '/api/employee/training/progress' => function () use (
                $employeePerformanceController
            ) {
                AuthMiddleware::handle();

                $jwtUser    = $GLOBALS['authenticated_user'] ?? null;
                $employeeId = (int) ($jwtUser->employee_id ?? $jwtUser->data->employee_id ?? 0);

                if ($employeeId <= 0) {
                    $employeeId = (int) ($_GET['id'] ?? 1);
                }

                $employeePerformanceController->updateProgress($employeeId);
            },

            '/api/employee/goals' => function () use (
                $employeePerformanceController
            ) {
                AuthMiddleware::handle();

                $jwtUser    = $GLOBALS['authenticated_user'] ?? null;
                $employeeId = (int) ($jwtUser->employee_id ?? $jwtUser->data->employee_id ?? 0);

                if ($employeeId <= 0) {
                    $employeeId = (int) ($_GET['id'] ?? 1);
                }

                $employeePerformanceController->addGoal($employeeId);
            },

            /*
            |--------------------------------------------------------------------------
            | Employee Documents & Bank Details POST Actions
            |--------------------------------------------------------------------------
            */

            '/api/employee/documents/upload' => function () use (
                $employeeDocumentsController
            ) {
                AuthMiddleware::handle();

                $jwtUser    = $GLOBALS['authenticated_user'] ?? null;
                $employeeId = (int) ($jwtUser->employee_id ?? $jwtUser->data->employee_id ?? 0);

                if ($employeeId <= 0) {
                    $employeeId = (int) ($_GET['id'] ?? 1);
                }

                $employeeDocumentsController->upload($employeeId);
            },

            '/api/employee/documents/request' => function () use (
                $employeeDocumentsController
            ) {
                AuthMiddleware::handle();

                $jwtUser    = $GLOBALS['authenticated_user'] ?? null;
                $employeeId = (int) ($jwtUser->employee_id ?? $jwtUser->data->employee_id ?? 0);

                if ($employeeId <= 0) {
                    $employeeId = (int) ($_GET['id'] ?? 1);
                }

                $employeeDocumentsController->requestDocument($employeeId);
            },

            /*
            |--------------------------------------------------------------------------
            | Manager Approvals POST Action
            |--------------------------------------------------------------------------
            */
            '/api/manager/approvals/action' => function () use (
                $managerDashboardController
            ) {
                AuthMiddleware::handle();

                $managerDashboardController->handleApprovalAction();
            },

            '/api/manager/tasks' => function () use (
                $managerDashboardController
            ) {
                AuthMiddleware::handle();

                $jwtUser   = $GLOBALS['authenticated_user'] ?? null;
                $managerId = (int) ($jwtUser->id ?? $jwtUser->data->id ?? 0);

                $managerDashboardController->assignTask($managerId);
            },

            '/api/manager/tasks/update-status' => function () use (
                $managerDashboardController
            ) {
                AuthMiddleware::handle();

                $managerDashboardController->updateTaskStatus();
            },

            '/api/manager/reviews' => function () use (
                $managerDashboardController
            ) {
                AuthMiddleware::handle();

                $jwtUser   = $GLOBALS['authenticated_user'] ?? null;
                $managerId = (int) ($jwtUser->id ?? $jwtUser->data->id ?? 0);

                $managerDashboardController->createReview($managerId);
            },

            '/api/manager/goals' => function () use (
                $managerDashboardController
            ) {
                AuthMiddleware::handle();

                $jwtUser   = $GLOBALS['authenticated_user'] ?? null;
                $managerId = (int) ($jwtUser->id ?? $jwtUser->data->id ?? 0);

                $managerDashboardController->createGoal($managerId);
            },


            '/api/employee/bank-details' => function () use (
                $employeeDocumentsController
            ) {
                AuthMiddleware::handle();

                $jwtUser    = $GLOBALS['authenticated_user'] ?? null;
                $employeeId = (int) ($jwtUser->employee_id ?? $jwtUser->data->employee_id ?? 0);

                if ($employeeId <= 0) {
                    $employeeId = (int) ($_GET['id'] ?? 1);
                }

                $employeeDocumentsController->updateBank($employeeId);
            },

            /*
            | Login
            */
            '/api/login' => [
                $authController,
                'login'
            ],


            /*
            | Logout
            */
            '/api/logout' => function () use ($authController) {
                AuthMiddleware::handle();

                $authController->logout();
            },

            /*
            | Current Authenticated User Profile
            */
            '/api/me' => function () use ($authController) {
                AuthMiddleware::handle();

                $authController->me();
            },

            '/api/auth/me' => function () use ($authController) {
                AuthMiddleware::handle();

                $authController->me();
            },


            /*
            |--------------------------------------------------------------------------
            | Employee
            |--------------------------------------------------------------------------
            */

            '/api/employees' => function () use (
                $employeeController,
                $permissionRepository
            ) {
                AuthMiddleware::handle();

                PermissionMiddleware::handle(
                    $permissionRepository,
                    'employee.create'
                );

                $employeeController->store();
            },


            '/api/employees/upload-photo' => function () use ($employeeController) {
                AuthMiddleware::handle();

                $employeeController->uploadPhoto();
            },


            '/api/employees/upload-document' => function () use ($employeeController) {
                AuthMiddleware::handle();

                $employeeController->uploadDocument();
            },


            /*
            |--------------------------------------------------------------------------
            | Users
            |--------------------------------------------------------------------------
            */

            '/api/users' => function () use (
                $userController,
                $permissionRepository
            ) {
                AuthMiddleware::handle();

                PermissionMiddleware::handle(
                    $permissionRepository,
                    'user.create'
                );

                $userController->store();
            },


            /*
            |--------------------------------------------------------------------------
            | Roles & Permissions
            |--------------------------------------------------------------------------
            */

            '/api/roles' => function () use (
                $permissionController,
                $permissionRepository
            ) {
                AuthMiddleware::handle();

                $jwtUser = $GLOBALS['authenticated_user'] ?? null;
                $userRole = strtoupper((string)($jwtUser->role ?? ''));

                if (
                    $userRole !== 'ADMIN' &&
                    !$permissionRepository->hasPermission($userRole, 'settings.manage') &&
                    !$permissionRepository->hasPermission($userRole, 'organisation.create')
                ) {
                    PermissionMiddleware::handle(
                        $permissionRepository,
                        'settings.manage'
                    );
                }

                $permissionController->storeRole();
            },


            '/api/role-permissions' => function () use (
                $permissionController,
                $permissionRepository
            ) {
                AuthMiddleware::handle();

                PermissionMiddleware::handle(
                    $permissionRepository,
                    'settings.manage'
                );

                $roleId = (int) ($_GET['role_id'] ?? 0);

                if ($roleId <= 0) {
                    http_response_code(400);

                    echo json_encode([
                        'success' => false,
                        'message' => 'Invalid role ID.'
                    ]);

                    return;
                }

                $permissionController->replaceRolePermissions($roleId);
            },


            /*
            |--------------------------------------------------------------------------
            | Policies
            |--------------------------------------------------------------------------
            */

            '/api/policies' => function () use (
                $policyController,
                $permissionRepository
            ) {
                AuthMiddleware::handle();

                PermissionMiddleware::handle(
                    $permissionRepository,
                    'settings.manage'
                );

                $policyController->storePolicy();
            },


            '/api/configurations' => function () use (
                $policyController,
                $permissionRepository
            ) {
                AuthMiddleware::handle();

                PermissionMiddleware::handle(
                    $permissionRepository,
                    'settings.manage'
                );

                $policyController->storeConfiguration();
            },


            /*
            |--------------------------------------------------------------------------
            | Reports
            |--------------------------------------------------------------------------
            */

            '/api/reports' => function () use (
                $analyticsController,
                $permissionRepository
            ) {
                AuthMiddleware::handle();

                PermissionMiddleware::handle(
                    $permissionRepository,
                    'settings.manage'
                );

                $analyticsController->generateReport();
            },


            /*
            |--------------------------------------------------------------------------
            | Settings
            |--------------------------------------------------------------------------
            */

            '/api/settings' => function () use (
                $settingsController,
                $permissionRepository
            ) {
                AuthMiddleware::handle();

                PermissionMiddleware::handle(
                    $permissionRepository,
                    'settings.manage'
                );

                $settingsController->update();
            },


            /*
            |--------------------------------------------------------------------------
            | Organisation
            |--------------------------------------------------------------------------
            */

            '/api/organisations' => function () use (
                $organisationController,
                $permissionRepository
            ) {
                AuthMiddleware::handle();

                PermissionMiddleware::handle(
                    $permissionRepository,
                    'organisation.create'
                );

                $organisationController->store();
            },


            /*
            |--------------------------------------------------------------------------
            | Department
            |--------------------------------------------------------------------------
            */

            '/api/departments' => function () use (
                $departmentController,
                $permissionRepository
            ) {
                AuthMiddleware::handle();

                $jwtUser = $GLOBALS['authenticated_user'] ?? null;
                $userRole = strtoupper((string)($jwtUser->role ?? ''));

                if (
                    $userRole !== 'ADMIN' &&
                    !$permissionRepository->hasPermission($userRole, 'department.create') &&
                    !$permissionRepository->hasPermission($userRole, 'organisation.create')
                ) {
                    PermissionMiddleware::handle(
                        $permissionRepository,
                        'department.create'
                    );
                }

                $departmentController->store();
            },


            /*
            |--------------------------------------------------------------------------
            | Designation
            |--------------------------------------------------------------------------
            */

            '/api/designations' => function () use (
                $designationController,
                $permissionRepository
            ) {
                AuthMiddleware::handle();

                $jwtUser = $GLOBALS['authenticated_user'] ?? null;
                $userRole = strtoupper((string)($jwtUser->role ?? ''));

                if (
                    $userRole !== 'ADMIN' &&
                    !$permissionRepository->hasPermission($userRole, 'designation.create') &&
                    !$permissionRepository->hasPermission($userRole, 'organisation.create')
                ) {
                    PermissionMiddleware::handle(
                        $permissionRepository,
                        'designation.create'
                    );
                }

                $designationController->store();
            },


            /*
            |--------------------------------------------------------------------------
            | Branch
            |--------------------------------------------------------------------------
            */

            '/api/branches' => function () use (
                $branchController
            ) {
                AuthMiddleware::handle();
                $branchController->store();
            },


            /*
            |--------------------------------------------------------------------------
            | Leave
            |--------------------------------------------------------------------------
            */

            '/api/leave-types' => function () use (
                $leaveController,
                $permissionRepository
            ) {
                AuthMiddleware::handle();

                $jwtUser = $GLOBALS['authenticated_user'] ?? null;
                $userRole = strtoupper((string)($jwtUser->role ?? ''));

                if (
                    $userRole !== 'ADMIN' &&
                    $userRole !== 'HR' &&
                    !$permissionRepository->hasPermission($userRole, 'settings.manage') &&
                    !$permissionRepository->hasPermission($userRole, 'leave.manage')
                ) {
                    PermissionMiddleware::handle(
                        $permissionRepository,
                        'settings.manage'
                    );
                }

                $leaveController->storeType();
            },


            '/api/leave-requests' => function () use (
                $leaveController,
                $permissionRepository
            ) {
                AuthMiddleware::handle();

                PermissionMiddleware::handle(
                    $permissionRepository,
                    'settings.manage'
                );

                $leaveController->storeRequest();
            },


            '/api/leave-requests/approve' => function () use ($leaveController) {
                AuthMiddleware::handle();

                \App\Middleware\Hr\HrRoleMiddleware::handle();

                $id = (int) ($_GET['id'] ?? 0);

                $leaveController->approve($id);
            },


            '/api/leave-requests/reject' => function () use ($leaveController) {
                AuthMiddleware::handle();

                \App\Middleware\Hr\HrRoleMiddleware::handle();

                $id = (int) ($_GET['id'] ?? 0);

                $leaveController->reject($id);
            },


            /*
            |--------------------------------------------------------------------------
            | Payroll
            |--------------------------------------------------------------------------
            */

            '/api/hr/payroll/run' => function () use ($payrollController) {
                AuthMiddleware::handle();

                \App\Middleware\Hr\HrRoleMiddleware::handle();

                $payrollController->run();
            },


            /*
            |--------------------------------------------------------------------------
            | HR Reports
            |--------------------------------------------------------------------------
            */

            '/api/hr/reports/generate' => function () use ($hrReportsController) {
                AuthMiddleware::handle();

                \App\Middleware\Hr\HrRoleMiddleware::handle();

                $hrReportsController->generate();
            },


            /*
            |--------------------------------------------------------------------------
            | Performance
            |--------------------------------------------------------------------------
            */

            '/api/performance/reviews' => function () use ($performanceController) {
                AuthMiddleware::handle();

                $performanceController->storeReview();
            },


            '/api/performance/goals' => function () use ($performanceController) {
                AuthMiddleware::handle();

                $performanceController->storeGoal();
            },


            /*
            |--------------------------------------------------------------------------
            | Training
            |--------------------------------------------------------------------------
            */

            '/api/training/programs' => function () use ($trainingController) {
                AuthMiddleware::handle();

                $trainingController->storeProgram();
            },


            '/api/training/sessions' => function () use ($trainingController) {
                AuthMiddleware::handle();

                $trainingController->storeSession();
            },


            '/api/training/enrollments' => function () use ($trainingController) {
                AuthMiddleware::handle();

                $trainingController->storeEnrollment();
            },


            '/api/training/certificates' => function () use ($trainingController) {
                AuthMiddleware::handle();

                $trainingController->issueCertificate();
            }

        ],


        /*
        |--------------------------------------------------------------------------
        | PUT Routes
        |--------------------------------------------------------------------------
        */

        'PUT' => [

            /*
            |--------------------------------------------------------------------------
            | Employee
            |--------------------------------------------------------------------------
            */

            '/api/employees/update' => function () use (
                $employeeController,
                $permissionRepository
            ) {
                AuthMiddleware::handle();

                PermissionMiddleware::handle(
                    $permissionRepository,
                    'employee.edit'
                );

                $id = (int) ($_GET['id'] ?? 0);

                if ($id <= 0) {
                    http_response_code(400);

                    echo json_encode([
                        'success' => false,
                        'message' => 'Invalid employee ID.'
                    ]);

                    return;
                }

                $employeeController->update($id);
            },


            /*
            |--------------------------------------------------------------------------
            | User
            |--------------------------------------------------------------------------
            */

            '/api/users/update' => function () use (
                $userController,
                $permissionRepository
            ) {
                AuthMiddleware::handle();

                PermissionMiddleware::handle(
                    $permissionRepository,
                    'user.edit'
                );

                $id = (int) ($_GET['id'] ?? 0);

                if ($id <= 0) {
                    http_response_code(400);

                    echo json_encode([
                        'success' => false,
                        'message' => 'Invalid user ID.'
                    ]);

                    return;
                }

                $userController->update($id);
            },


            /*
            |--------------------------------------------------------------------------
            | Roles
            |--------------------------------------------------------------------------
            */

            '/api/roles/update' => function () use (
                $permissionController,
                $permissionRepository
            ) {
                AuthMiddleware::handle();

                $jwtUser = $GLOBALS['authenticated_user'] ?? null;
                $userRole = strtoupper((string)($jwtUser->role ?? ''));

                if (
                    $userRole !== 'ADMIN' &&
                    !$permissionRepository->hasPermission($userRole, 'settings.manage') &&
                    !$permissionRepository->hasPermission($userRole, 'organisation.edit')
                ) {
                    PermissionMiddleware::handle(
                        $permissionRepository,
                        'settings.manage'
                    );
                }

                $id = (int) ($_GET['id'] ?? 0);

                if ($id <= 0) {
                    http_response_code(400);

                    echo json_encode([
                        'success' => false,
                        'message' => 'Invalid role ID.'
                    ]);

                    return;
                }

                $permissionController->updateRole($id);
            },


            /*
            |--------------------------------------------------------------------------
            | Policies
            |--------------------------------------------------------------------------
            */

            '/api/policies/update' => function () use (
                $policyController,
                $permissionRepository
            ) {
                AuthMiddleware::handle();

                PermissionMiddleware::handle(
                    $permissionRepository,
                    'settings.manage'
                );

                $id = (int) ($_GET['id'] ?? 0);

                if ($id <= 0) {
                    http_response_code(400);

                    echo json_encode([
                        'success' => false,
                        'message' => 'Invalid policy ID.'
                    ]);

                    return;
                }

                $policyController->updatePolicy($id);
            },


            '/api/configurations/update' => function () use (
                $policyController,
                $permissionRepository
            ) {
                AuthMiddleware::handle();

                PermissionMiddleware::handle(
                    $permissionRepository,
                    'settings.manage'
                );

                $id = (int) ($_GET['id'] ?? 0);

                if ($id <= 0) {
                    http_response_code(400);

                    echo json_encode([
                        'success' => false,
                        'message' => 'Invalid configuration ID.'
                    ]);

                    return;
                }

                $policyController->updateConfiguration($id);
            },


            /*
            |--------------------------------------------------------------------------
            | Organisation
            |--------------------------------------------------------------------------
            */

            '/api/organisations/update' => function () use (
                $organisationController,
                $permissionRepository
            ) {
                AuthMiddleware::handle();

                PermissionMiddleware::handle(
                    $permissionRepository,
                    'organisation.edit'
                );

                $id = (int) ($_GET['id'] ?? 0);

                if ($id <= 0) {
                    http_response_code(400);

                    echo json_encode([
                        'success' => false,
                        'message' => 'Invalid organisation ID.'
                    ]);

                    return;
                }

                $organisationController->update($id);
            },


            /*
            |--------------------------------------------------------------------------
            | Performance
            |--------------------------------------------------------------------------
            */

            '/api/performance/reviews/update' => function () use ($performanceController) {
                AuthMiddleware::handle();

                $id = (int) ($_GET['id'] ?? 0);

                if ($id <= 0) {
                    http_response_code(400);

                    echo json_encode([
                        'success' => false,
                        'message' => 'Invalid review ID.'
                    ]);

                    return;
                }

                $performanceController->updateReview($id);
            },


            '/api/performance/goals/update' => function () use ($performanceController) {
                AuthMiddleware::handle();

                $id = (int) ($_GET['id'] ?? 0);

                if ($id <= 0) {
                    http_response_code(400);

                    echo json_encode([
                        'success' => false,
                        'message' => 'Invalid goal ID.'
                    ]);

                    return;
                }

                $performanceController->updateGoal($id);
            },


            /*
            |--------------------------------------------------------------------------
            | Training
            |--------------------------------------------------------------------------
            */

            '/api/training/programs/update' => function () use ($trainingController) {
                AuthMiddleware::handle();

                $id = (int) ($_GET['id'] ?? 0);
                if ($id <= 0) {
                    $raw = json_decode(file_get_contents('php://input'), true);
                    $id = (int) ($raw['id'] ?? 0);
                }

                if ($id <= 0) {
                    http_response_code(400);

                    echo json_encode([
                        'success' => false,
                        'message' => 'Invalid program ID.'
                    ]);

                    return;
                }

                $trainingController->updateProgram($id);
            },

            '/api/training/programs/status' => function () use ($trainingController) {
                AuthMiddleware::handle();

                $id = (int) ($_GET['id'] ?? 0);
                if ($id <= 0) {
                    $raw = json_decode(file_get_contents('php://input'), true);
                    $id = (int) ($raw['id'] ?? 0);
                }

                if ($id <= 0) {
                    http_response_code(400);

                    echo json_encode([
                        'success' => false,
                        'message' => 'Invalid program ID.'
                    ]);

                    return;
                }

                $trainingController->updateProgram($id);
            },


            '/api/training/sessions/update' => function () use ($trainingController) {
                AuthMiddleware::handle();

                $id = (int) ($_GET['id'] ?? 0);

                if ($id <= 0) {
                    http_response_code(400);

                    echo json_encode([
                        'success' => false,
                        'message' => 'Invalid session ID.'
                    ]);

                    return;
                }

                $trainingController->updateSession($id);
            },


            '/api/training/enrollments/update' => function () use ($trainingController) {
                AuthMiddleware::handle();

                $id = (int) ($_GET['id'] ?? 0);
                if ($id <= 0) {
                    $raw = json_decode(file_get_contents('php://input'), true);
                    $id = (int) ($raw['id'] ?? 0);
                }

                if ($id <= 0) {
                    http_response_code(400);

                    echo json_encode([
                        'success' => false,
                        'message' => 'Invalid enrollment ID.'
                    ]);

                    return;
                }

                $trainingController->updateEnrollment($id);
            },

            '/api/training/enrollments/status' => function () use ($trainingController) {
                AuthMiddleware::handle();

                $id = (int) ($_GET['id'] ?? 0);
                if ($id <= 0) {
                    $raw = json_decode(file_get_contents('php://input'), true);
                    $id = (int) ($raw['id'] ?? 0);
                }

                if ($id <= 0) {
                    http_response_code(400);

                    echo json_encode([
                        'success' => false,
                        'message' => 'Invalid enrollment ID.'
                    ]);

                    return;
                }

                $trainingController->updateEnrollment($id);
            }

        ],


        /*
        |--------------------------------------------------------------------------
        | DELETE Routes
        |--------------------------------------------------------------------------
        */

        'DELETE' => [

            /*
            |--------------------------------------------------------------------------
            | Employee
            |--------------------------------------------------------------------------
            */

            '/api/employees/delete' => function () use (
                $employeeController,
                $permissionRepository
            ) {
                AuthMiddleware::handle();

                PermissionMiddleware::handle(
                    $permissionRepository,
                    'employee.delete'
                );

                $id = (int) ($_GET['id'] ?? 0);

                if ($id <= 0) {
                    http_response_code(400);

                    echo json_encode([
                        'success' => false,
                        'message' => 'Invalid employee ID.'
                    ]);

                    return;
                }

                $employeeController->destroy($id);
            },


            /*
            |--------------------------------------------------------------------------
            | Users
            |--------------------------------------------------------------------------
            */

            '/api/users/delete' => function () use (
                $userController,
                $permissionRepository
            ) {
                AuthMiddleware::handle();

                PermissionMiddleware::handle(
                    $permissionRepository,
                    'user.delete'
                );

                $id = (int) ($_GET['id'] ?? 0);

                if ($id <= 0) {
                    http_response_code(400);

                    echo json_encode([
                        'success' => false,
                        'message' => 'Invalid user ID.'
                    ]);

                    return;
                }

                $userController->destroy($id);
            },


            /*
            |--------------------------------------------------------------------------
            | Roles
            |--------------------------------------------------------------------------
            */

            '/api/roles/delete' => function () use (
                $permissionController,
                $permissionRepository
            ) {
                AuthMiddleware::handle();

                $jwtUser = $GLOBALS['authenticated_user'] ?? null;
                $userRole = strtoupper((string)($jwtUser->role ?? ''));

                if (
                    $userRole !== 'ADMIN' &&
                    !$permissionRepository->hasPermission($userRole, 'settings.manage') &&
                    !$permissionRepository->hasPermission($userRole, 'organisation.delete')
                ) {
                    PermissionMiddleware::handle(
                        $permissionRepository,
                        'settings.manage'
                    );
                }

                $id = (int) ($_GET['id'] ?? 0);

                if ($id <= 0) {
                    http_response_code(400);

                    echo json_encode([
                        'success' => false,
                        'message' => 'Invalid role ID.'
                    ]);

                    return;
                }

                $permissionController->destroyRole($id);
            },


            /*
            |--------------------------------------------------------------------------
            | Policies
            |--------------------------------------------------------------------------
            */

            '/api/policies/delete' => function () use (
                $policyController,
                $permissionRepository
            ) {
                AuthMiddleware::handle();

                PermissionMiddleware::handle(
                    $permissionRepository,
                    'settings.manage'
                );

                $id = (int) ($_GET['id'] ?? 0);

                if ($id <= 0) {
                    http_response_code(400);

                    echo json_encode([
                        'success' => false,
                        'message' => 'Invalid policy ID.'
                    ]);

                    return;
                }

                $policyController->deletePolicy($id);
            },


            '/api/configurations/delete' => function () use (
                $policyController,
                $permissionRepository
            ) {
                AuthMiddleware::handle();

                PermissionMiddleware::handle(
                    $permissionRepository,
                    'settings.manage'
                );

                $id = (int) ($_GET['id'] ?? 0);

                if ($id <= 0) {
                    http_response_code(400);

                    echo json_encode([
                        'success' => false,
                        'message' => 'Invalid configuration ID.'
                    ]);

                    return;
                }

                $policyController->deleteConfiguration($id);
            },


            /*
            |--------------------------------------------------------------------------
            | Organisation
            |--------------------------------------------------------------------------
            */

            '/api/organisations/delete' => function () use (
                $organisationController,
                $permissionRepository
            ) {
                AuthMiddleware::handle();

                PermissionMiddleware::handle(
                    $permissionRepository,
                    'organisation.delete'
                );

                $id = (int) ($_GET['id'] ?? 0);

                if ($id <= 0) {
                    http_response_code(400);

                    echo json_encode([
                        'success' => false,
                        'message' => 'Invalid organisation ID.'
                    ]);

                    return;
                }

                $organisationController->destroy($id);
            },


            /*
            |--------------------------------------------------------------------------
            | Performance
            |--------------------------------------------------------------------------
            */

            '/api/performance/reviews/delete' => function () use ($performanceController) {
                AuthMiddleware::handle();

                $id = (int) ($_GET['id'] ?? 0);

                if ($id <= 0) {
                    http_response_code(400);

                    echo json_encode([
                        'success' => false,
                        'message' => 'Invalid review ID.'
                    ]);

                    return;
                }

                $performanceController->destroyReview($id);
            },


            '/api/performance/goals/delete' => function () use ($performanceController) {
                AuthMiddleware::handle();

                $id = (int) ($_GET['id'] ?? 0);

                if ($id <= 0) {
                    http_response_code(400);

                    echo json_encode([
                        'success' => false,
                        'message' => 'Invalid goal ID.'
                    ]);

                    return;
                }

                $performanceController->destroyGoal($id);
            },


            /*
            |--------------------------------------------------------------------------
            | Training
            |--------------------------------------------------------------------------
            */

            '/api/training/programs/delete' => function () use ($trainingController) {
                AuthMiddleware::handle();

                $id = (int) ($_GET['id'] ?? 0);

                if ($id <= 0) {
                    http_response_code(400);

                    echo json_encode([
                        'success' => false,
                        'message' => 'Invalid program ID.'
                    ]);

                    return;
                }

                $trainingController->destroyProgram($id);
            },


            '/api/training/sessions/delete' => function () use ($trainingController) {
                AuthMiddleware::handle();

                $id = (int) ($_GET['id'] ?? 0);

                if ($id <= 0) {
                    http_response_code(400);

                    echo json_encode([
                        'success' => false,
                        'message' => 'Invalid session ID.'
                    ]);

                    return;
                }

                $trainingController->destroySession($id);
            },


            '/api/training/enrollments/delete' => function () use ($trainingController) {
                AuthMiddleware::handle();

                $id = (int) ($_GET['id'] ?? 0);

                if ($id <= 0) {
                    http_response_code(400);

                    echo json_encode([
                        'success' => false,
                        'message' => 'Invalid enrollment ID.'
                    ]);

                    return;
                }

                $trainingController->destroyEnrollment($id);
            }

        ]

    ];

};
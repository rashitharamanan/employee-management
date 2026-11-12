<?php

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../config/database.php';

use Dotenv\Dotenv;

/*
|--------------------------------------------------------------------------
| Controllers
|--------------------------------------------------------------------------
*/

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
use App\Controllers\PermissionController;
use App\Controllers\PolicyController;
use App\Controllers\AnalyticsController;
use App\Controllers\SettingsController;
use App\Controllers\DashboardController;
use App\Controllers\LeaveController;
use App\Controllers\Hr\HrController;
use App\Controllers\Hr\PayrollController;
use App\Controllers\Hr\HrReportsController;
use App\Controllers\PerformanceController;
use App\Controllers\TrainingController;

/*
|--------------------------------------------------------------------------
| Services
|--------------------------------------------------------------------------
*/

use App\Services\JwtService;
use App\Services\AuthService;
use App\Services\EmployeeService;
use App\Services\Employee\EmployeeDashboardService;
use App\Services\Employee\EmployeeAttendanceService;
use App\Services\Employee\EmployeePerformanceService;
use App\Services\Employee\EmployeeDocumentsService;
use App\Services\Manager\ManagerDashboardService;
use App\Services\UserService;
use App\Services\QrService;
use App\Services\DepartmentService;
use App\Services\DesignationService;
use App\Services\BranchService;
use App\Services\OrganisationService;
use App\Services\LeaveService;
use App\Services\Hr\HrDashboardService;
use App\Services\Hr\HrEmployeeService;
use App\Services\Hr\HrLeaveService;
use App\Services\Hr\PayrollService;
use App\Services\Hr\HrReportsService;
use App\Services\PerformanceService;
use App\Services\TrainingService;

/*
|--------------------------------------------------------------------------
| Repositories
|--------------------------------------------------------------------------
*/

use App\Repositories\UserRepository;
use App\Repositories\Employee\EmployeeRepository;
use App\Repositories\Employee\EmployeeDashboardRepository;
use App\Repositories\Employee\EmployeeAttendanceRepository;
use App\Repositories\Employee\EmployeePerformanceRepository;
use App\Repositories\Employee\EmployeeDocumentsRepository;
use App\Repositories\Manager\ManagerDashboardRepository;
use App\Repositories\Department\DepartmentRepository;
use App\Repositories\Designation\DesignationRepository;
use App\Repositories\Branch\BranchRepository;
use App\Repositories\Organisation\OrganisationRepository;
use App\Repositories\PermissionRepository;
use App\Repositories\PolicyRepository;
use App\Repositories\AnalyticsRepository;
use App\Repositories\SettingsRepository;
use App\Repositories\DashboardRepository;
use App\Repositories\Leave\LeaveRepository;
use App\Repositories\Hr\HrDashboardRepository;
use App\Repositories\Hr\HrEmployeeRepository;
use App\Repositories\Hr\HrLeaveRepository;
use App\Repositories\Hr\PayrollRepository;
use App\Repositories\Hr\HrReportsRepository;
use App\Repositories\PerformanceRepository;
use App\Repositories\TrainingRepository;

/*
|--------------------------------------------------------------------------
| Middleware
|--------------------------------------------------------------------------
*/

use App\Middleware\AuthMiddleware;
use App\Middleware\PermissionMiddleware;

/*
|--------------------------------------------------------------------------
| Load Environment Variables
|--------------------------------------------------------------------------
*/

$dotenv = Dotenv::createImmutable(
    __DIR__ . '/../'
);

$dotenv->load();

/*
|--------------------------------------------------------------------------
| JSON Response Header
|--------------------------------------------------------------------------
*/

header('Content-Type: application/json');

/*
|--------------------------------------------------------------------------
| Application Initialization
|--------------------------------------------------------------------------
*/

try {

    /*
    |--------------------------------------------------------------------------
    | Database
    |--------------------------------------------------------------------------
    */

    $database = new Database();
    $pdo = $database->connect();


    /*
    |--------------------------------------------------------------------------
    | Repositories
    |--------------------------------------------------------------------------
    */

    $userRepository = new UserRepository($pdo);

    $employeeRepository = new EmployeeRepository($pdo);

    $employeeDashboardRepository =
        new EmployeeDashboardRepository($pdo);

    $employeeAttendanceRepository =
        new EmployeeAttendanceRepository($pdo);
    $employeeAttendanceRepository->ensureSchema();

    $employeePerformanceRepository =
        new EmployeePerformanceRepository($pdo);
    $employeePerformanceRepository->ensureSchema();

    $employeeDocumentsRepository =
        new EmployeeDocumentsRepository($pdo);
    $employeeDocumentsRepository->ensureSchema();

    $managerDashboardRepository =
        new ManagerDashboardRepository($pdo);
    $managerDashboardRepository->seedIfEmpty();

    $departmentRepository =
        new DepartmentRepository($pdo);

    $designationRepository =
        new DesignationRepository($pdo);

    $branchRepository =
        new BranchRepository($pdo);

    $organisationRepository =
        new OrganisationRepository($pdo);

    $permissionRepository =
        new PermissionRepository($pdo);

    $policyRepository =
        new PolicyRepository($pdo);

    $policyRepository->ensureSchema();

    $analyticsRepository =
        new AnalyticsRepository($pdo);

    $analyticsRepository->ensureSchema();

    $settingsRepository =
        new SettingsRepository($pdo);

    $settingsRepository->ensureSchema();

    $dashboardRepository =
        new DashboardRepository($pdo);

    $leaveRepository =
        new LeaveRepository($pdo);

    $leaveRepository->ensureSchema();

    $hrDashboardRepository =
        new HrDashboardRepository($pdo);

    $hrDashboardRepository->ensureRecruitmentSchema();

    $hrDashboardRepository->ensureOnboardingExitSchema();

    $hrDashboardRepository->ensurePayrollSchema();

    $hrEmployeeRepository =
        new HrEmployeeRepository($pdo);

    $hrLeaveRepository =
        new HrLeaveRepository($pdo);

    $payrollRepository =
        new PayrollRepository($pdo);

    $performanceRepository =
        new PerformanceRepository($pdo);

    $performanceRepository->ensureSchema();

    $trainingRepository =
        new TrainingRepository($pdo);

    $trainingRepository->ensureSchema();

    $hrReportsRepository =
        new HrReportsRepository($pdo);

    $hrReportsRepository->ensureSchema();


    /*
    |--------------------------------------------------------------------------
    | Services
    |--------------------------------------------------------------------------
    */

    $jwtService =
        new JwtService();

    $authService =
        new AuthService(
            $userRepository,
            $jwtService
        );

    $userService =
        new UserService(
            $userRepository,
            $pdo
        );

    $qrService =
        new QrService();

    $employeeService =
        new EmployeeService(
            $employeeRepository,
            $userService,
            $qrService,
            $pdo
        );

    /*
    |--------------------------------------------------------------------------
    | Employee Dashboard Service
    |--------------------------------------------------------------------------
    */

    $employeeDashboardService =
        new EmployeeDashboardService(
            $employeeDashboardRepository
        );

    $employeeAttendanceService =
        new EmployeeAttendanceService(
            $employeeAttendanceRepository
        );

    $employeePerformanceService =
        new EmployeePerformanceService(
            $employeePerformanceRepository
        );

    $employeeDocumentsService =
        new EmployeeDocumentsService(
            $employeeDocumentsRepository
        );

    $managerDashboardService =
        new ManagerDashboardService(
            $managerDashboardRepository
        );

    $departmentService =
        new DepartmentService(
            $departmentRepository
        );

    $designationService =
        new DesignationService(
            $designationRepository
        );

    $branchService =
        new BranchService(
            $branchRepository
        );

    $organisationService =
        new OrganisationService(
            $organisationRepository
        );

    $leaveService =
        new LeaveService(
            $leaveRepository
        );

    $hrDashboardService =
        new HrDashboardService(
            $hrDashboardRepository
        );

    $hrEmployeeService =
        new HrEmployeeService(
            $hrEmployeeRepository
        );

    $hrLeaveService =
        new HrLeaveService(
            $hrLeaveRepository
        );

    $payrollService =
        new PayrollService(
            $payrollRepository
        );

    $performanceService =
        new PerformanceService(
            $performanceRepository,
            $pdo
        );

    $trainingService =
        new TrainingService(
            $trainingRepository,
            $pdo
        );

    $hrReportsService =
        new HrReportsService(
            $hrReportsRepository
        );


    /*
    |--------------------------------------------------------------------------
    | Controllers
    |--------------------------------------------------------------------------
    */

    $authController =
        new AuthController(
            $authService
        );

    $userController =
        new UserController(
            $userService
        );

    $employeeController =
        new EmployeeController(
            $employeeService
        );

    /*
    |--------------------------------------------------------------------------
    | Employee Dashboard Controller
    |--------------------------------------------------------------------------
    */

    $employeeDashboardController =
        new EmployeeDashboardController(
            $employeeDashboardService
        );

    $employeeAttendanceController =
        new EmployeeAttendanceController(
            $employeeAttendanceService
        );

    $employeePerformanceController =
        new EmployeePerformanceController(
            $employeePerformanceService
        );

    $employeeDocumentsController =
        new EmployeeDocumentsController(
            $employeeDocumentsService
        );

    $managerDashboardController =
        new ManagerDashboardController(
            $managerDashboardService
        );

    $departmentController =
        new DepartmentController(
            $departmentService
        );

    $designationController =
        new DesignationController(
            $designationService
        );

    $branchController =
        new BranchController(
            $branchService
        );

    $organisationController =
        new OrganisationController(
            $organisationService
        );

    $permissionController =
        new PermissionController(
            $permissionRepository
        );

    $policyController =
        new PolicyController(
            $policyRepository
        );

    $analyticsController =
        new AnalyticsController(
            $analyticsRepository
        );

    $settingsController =
        new SettingsController(
            $settingsRepository
        );

    $dashboardController =
        new DashboardController(
            $dashboardRepository
        );

    $leaveController =
        new LeaveController(
            $leaveService
        );

    $qrController =
        new QrController(
            $employeeService,
            $qrService
        );

    $hrController =
        new HrController(
            $hrDashboardService,
            $hrEmployeeService,
            $hrLeaveService
        );

    $payrollController =
        new PayrollController(
            $payrollService
        );

    $performanceController =
        new PerformanceController(
            $performanceService
        );

    $trainingController =
        new TrainingController(
            $trainingService
        );

    $hrReportsController =
        new HrReportsController(
            $hrReportsService
        );

} catch (\Throwable $exception) {

    http_response_code(500);

    echo json_encode([
        'success' => false,
        'message' => 'Application initialization failed.',
        'error' => $exception->getMessage(),
        'file' => $exception->getFile(),
        'line' => $exception->getLine()
    ]);

    exit;
}


/*
|--------------------------------------------------------------------------
| Load Routes
|--------------------------------------------------------------------------
*/

$routeFactory =
    require __DIR__ . '/../routes/api.php';


$routes = $routeFactory(

    $authController,

    $employeeController,

    /*
     * Employee Dashboard & Attendance Controllers
     */
    $employeeDashboardController,

    $employeeAttendanceController,

    $employeePerformanceController,

    $employeeDocumentsController,

    $managerDashboardController,

    $userController,

    $qrController,

    $departmentController,

    $designationController,

    $branchController,

    $organisationController,

    $permissionRepository,

    $permissionController,

    $policyController,

    $analyticsController,

    $settingsController,

    $dashboardController,

    $leaveController,

    $hrController,

    $payrollController,

    $performanceController,

    $trainingController,

    $hrReportsController
);


/*
|--------------------------------------------------------------------------
| Request Method & URI Normalization
|--------------------------------------------------------------------------
*/

$method =
    $_SERVER['REQUEST_METHOD'];

$uri =
    parse_url(
        $_SERVER['REQUEST_URI'],
        PHP_URL_PATH
    );


$basePath =
    '/employee-qr-system/backend/public';


if (str_starts_with($uri, $basePath)) {

    $uri =
        substr(
            $uri,
            strlen($basePath)
        );
}


if (str_starts_with($uri, '/index.php')) {

    $uri =
        substr(
            $uri,
            strlen('/index.php')
        );
}


$uri =
    '/' . trim(
        $uri,
        '/'
    );


if ($uri === '//') {

    $uri = '/';
}


/*
|--------------------------------------------------------------------------
| Exact Route Matching
|--------------------------------------------------------------------------
*/

$handler = $routes[$method][$uri] ?? $routes['GET'][$uri] ?? null;

if ($handler !== null) {

    if (
        is_array($handler) ||
        is_callable($handler)
    ) {

        call_user_func($handler);

        exit;
    }

    http_response_code(500);

    echo json_encode([
        'success' => false,
        'message' => 'Invalid route handler.'
    ]);

    exit;
}


/*
|--------------------------------------------------------------------------
| Dynamic Employee QR Download
| GET /api/employees/{id}/qr
|--------------------------------------------------------------------------
*/

if (
    preg_match(
        '#^/api/employees/([0-9]+)/qr$#',
        $uri,
        $matches
    )
) {

    $employeeId =
        (int) $matches[1];

    if ($employeeId <= 0) {

        http_response_code(400);

        echo json_encode([
            'success' => false,
            'message' => 'Invalid employee ID.'
        ]);

        exit;
    }

    AuthMiddleware::handle();

    PermissionMiddleware::handle(
        $permissionRepository,
        'employee.view'
    );

    $qrController->download(
        $employeeId
    );

    exit;
}


/*
|--------------------------------------------------------------------------
| Dynamic Employee Routes
| GET / PUT / DELETE /api/employees/{id}
|--------------------------------------------------------------------------
*/

if (
    preg_match(
        '#^/api/employees/([0-9]+)$#',
        $uri,
        $matches
    )
) {

    $employeeId =
        (int) $matches[1];

    if ($employeeId <= 0) {

        http_response_code(400);

        echo json_encode([
            'success' => false,
            'message' => 'Invalid employee ID.'
        ]);

        exit;
    }

    if ($method === 'GET') {

        AuthMiddleware::handle();

        PermissionMiddleware::handle(
            $permissionRepository,
            'employee.view'
        );

        $employeeController->show(
            $employeeId
        );

        exit;
    }

    if ($method === 'PUT') {

        AuthMiddleware::handle();

        PermissionMiddleware::handle(
            $permissionRepository,
            'employee.edit'
        );

        $employeeController->update(
            $employeeId
        );

        exit;
    }

    if ($method === 'DELETE') {

        AuthMiddleware::handle();

        PermissionMiddleware::handle(
            $permissionRepository,
            'employee.delete'
        );

        $employeeController->destroy(
            $employeeId
        );

        exit;
    }

    http_response_code(405);

    echo json_encode([
        'success' => false,
        'message' => 'Method not allowed.'
    ]);

    exit;
}


/*
|--------------------------------------------------------------------------
| Dynamic Organisation Structure Route
|--------------------------------------------------------------------------
*/

if (
    preg_match(
        '#^/api/organisations/([0-9]+)/structure$#',
        $uri,
        $matches
    )
) {

    $orgId =
        (int) $matches[1];

    if ($orgId <= 0) {

        http_response_code(400);

        echo json_encode([
            'success' => false,
            'message' => 'Invalid organisation ID.'
        ]);

        exit;
    }

    if ($method === 'GET') {

        AuthMiddleware::handle();

        PermissionMiddleware::handle(
            $permissionRepository,
            'organisation.view'
        );

        $organisationController->structure(
            $orgId
        );

        exit;
    }

    http_response_code(405);

    echo json_encode([
        'success' => false,
        'message' => 'Method not allowed.'
    ]);

    exit;
}


/*
|--------------------------------------------------------------------------
| Dynamic Organisation Status Route
|--------------------------------------------------------------------------
*/

if (
    preg_match(
        '#^/api/organisations/([0-9]+)/status$#',
        $uri,
        $matches
    )
) {

    $orgId =
        (int) $matches[1];

    if ($orgId <= 0) {

        http_response_code(400);

        echo json_encode([
            'success' => false,
            'message' => 'Invalid organisation ID.'
        ]);

        exit;
    }

    if (
        $method === 'PATCH' ||
        $method === 'PUT'
    ) {

        AuthMiddleware::handle();

        PermissionMiddleware::handle(
            $permissionRepository,
            'organisation.edit'
        );

        $organisationController->updateStatus(
            $orgId
        );

        exit;
    }

    http_response_code(405);

    echo json_encode([
        'success' => false,
        'message' => 'Method not allowed.'
    ]);

    exit;
}


/*
|--------------------------------------------------------------------------
| Dynamic Organisation Routes
|--------------------------------------------------------------------------
*/

if (
    preg_match(
        '#^/api/organisations/([0-9]+)$#',
        $uri,
        $matches
    )
) {

    $orgId =
        (int) $matches[1];

    if ($orgId <= 0) {

        http_response_code(400);

        echo json_encode([
            'success' => false,
            'message' => 'Invalid organisation ID.'
        ]);

        exit;
    }

    if ($method === 'GET') {

        AuthMiddleware::handle();

        PermissionMiddleware::handle(
            $permissionRepository,
            'organisation.view'
        );

        $organisationController->show(
            $orgId
        );

        exit;
    }

    if ($method === 'PUT') {

        AuthMiddleware::handle();

        PermissionMiddleware::handle(
            $permissionRepository,
            'organisation.edit'
        );

        $organisationController->update(
            $orgId
        );

        exit;
    }

    if ($method === 'DELETE') {

        AuthMiddleware::handle();

        PermissionMiddleware::handle(
            $permissionRepository,
            'organisation.delete'
        );

        $organisationController->destroy(
            $orgId
        );

        exit;
    }

    http_response_code(405);

    echo json_encode([
        'success' => false,
        'message' => 'Method not allowed.'
    ]);

    exit;
}


/*
|--------------------------------------------------------------------------
| Dynamic Department Status Route
|--------------------------------------------------------------------------
*/

if (
    preg_match(
        '#^/api/departments/([0-9]+)/status$#',
        $uri,
        $matches
    )
) {

    $deptId =
        (int) $matches[1];

    if ($deptId <= 0) {

        http_response_code(400);

        echo json_encode([
            'success' => false,
            'message' => 'Invalid department ID.'
        ]);

        exit;
    }

    if (
        $method === 'PATCH' ||
        $method === 'PUT'
    ) {

        AuthMiddleware::handle();

        PermissionMiddleware::handle(
            $permissionRepository,
            'department.edit'
        );

        $departmentController->updateStatus(
            $deptId
        );

        exit;
    }

    http_response_code(405);

    echo json_encode([
        'success' => false,
        'message' => 'Method not allowed.'
    ]);

    exit;
}


/*
|--------------------------------------------------------------------------
| Dynamic Department Routes
|--------------------------------------------------------------------------
*/

if (
    preg_match(
        '#^/api/departments/([0-9]+)$#',
        $uri,
        $matches
    )
) {

    $deptId =
        (int) $matches[1];

    if ($deptId <= 0) {

        http_response_code(400);

        echo json_encode([
            'success' => false,
            'message' => 'Invalid department ID.'
        ]);

        exit;
    }

    if ($method === 'GET') {

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

        $departmentController->show(
            $deptId
        );

        exit;
    }

    if ($method === 'PUT') {

        AuthMiddleware::handle();

        $jwtUser = $GLOBALS['authenticated_user'] ?? null;
        $userRole = strtoupper((string)($jwtUser->role ?? ''));

        if (
            $userRole !== 'ADMIN' &&
            !$permissionRepository->hasPermission($userRole, 'department.edit') &&
            !$permissionRepository->hasPermission($userRole, 'organisation.edit')
        ) {
            PermissionMiddleware::handle(
                $permissionRepository,
                'department.edit'
            );
        }

        $departmentController->update(
            $deptId
        );

        exit;
    }

    if ($method === 'DELETE') {

        AuthMiddleware::handle();

        $jwtUser = $GLOBALS['authenticated_user'] ?? null;
        $userRole = strtoupper((string)($jwtUser->role ?? ''));

        if (
            $userRole !== 'ADMIN' &&
            !$permissionRepository->hasPermission($userRole, 'department.delete') &&
            !$permissionRepository->hasPermission($userRole, 'organisation.delete')
        ) {
            PermissionMiddleware::handle(
                $permissionRepository,
                'department.delete'
            );
        }

        $departmentController->destroy(
            $deptId
        );

        exit;
    }

    http_response_code(405);

    echo json_encode([
        'success' => false,
        'message' => 'Method not allowed.'
    ]);

    exit;
}


/*
|--------------------------------------------------------------------------
| Department → Designation List
|--------------------------------------------------------------------------
*/

if (
    preg_match(
        '#^/api/designations/department/([0-9]+)$#',
        $uri,
        $matches
    )
) {

    $departmentId =
        (int) $matches[1];

    if ($departmentId <= 0) {

        http_response_code(400);

        echo json_encode([
            'success' => false,
            'message' => 'Invalid department ID.'
        ]);

        exit;
    }

    if ($method !== 'GET') {

        http_response_code(405);

        echo json_encode([
            'success' => false,
            'message' => 'Method not allowed.'
        ]);

        exit;
    }

    AuthMiddleware::handle();

    PermissionMiddleware::handle(
        $permissionRepository,
        'designation.view'
    );

    $designationController->byDepartment(
        $departmentId
    );

    exit;
}


/*
|--------------------------------------------------------------------------
| Dynamic Designation Status Route
|--------------------------------------------------------------------------
*/

if (
    preg_match(
        '#^/api/designations/([0-9]+)/status$#',
        $uri,
        $matches
    )
) {

    $desId =
        (int) $matches[1];

    if ($desId <= 0) {

        http_response_code(400);

        echo json_encode([
            'success' => false,
            'message' => 'Invalid designation ID.'
        ]);

        exit;
    }

    if (
        $method === 'PATCH' ||
        $method === 'PUT'
    ) {

        AuthMiddleware::handle();

        PermissionMiddleware::handle(
            $permissionRepository,
            'designation.edit'
        );

        $designationController->updateStatus(
            $desId
        );

        exit;
    }

    http_response_code(405);

    echo json_encode([
        'success' => false,
        'message' => 'Method not allowed.'
    ]);

    exit;
}


/*
|--------------------------------------------------------------------------
| Dynamic Designation Routes
|--------------------------------------------------------------------------
*/

if (
    preg_match(
        '#^/api/designations/([0-9]+)$#',
        $uri,
        $matches
    )
) {

    $desId =
        (int) $matches[1];

    if ($desId <= 0) {

        http_response_code(400);

        echo json_encode([
            'success' => false,
            'message' => 'Invalid designation ID.'
        ]);

        exit;
    }

    if ($method === 'GET') {

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

        $designationController->show(
            $desId
        );

        exit;
    }

    if ($method === 'PUT') {

        AuthMiddleware::handle();

        $jwtUser = $GLOBALS['authenticated_user'] ?? null;
        $userRole = strtoupper((string)($jwtUser->role ?? ''));

        if (
            $userRole !== 'ADMIN' &&
            !$permissionRepository->hasPermission($userRole, 'designation.edit') &&
            !$permissionRepository->hasPermission($userRole, 'organisation.edit')
        ) {
            PermissionMiddleware::handle(
                $permissionRepository,
                'designation.edit'
            );
        }

        $designationController->update(
            $desId
        );

        exit;
    }

    if ($method === 'DELETE') {

        AuthMiddleware::handle();

        $jwtUser = $GLOBALS['authenticated_user'] ?? null;
        $userRole = strtoupper((string)($jwtUser->role ?? ''));

        if (
            $userRole !== 'ADMIN' &&
            !$permissionRepository->hasPermission($userRole, 'designation.delete') &&
            !$permissionRepository->hasPermission($userRole, 'organisation.delete')
        ) {
            PermissionMiddleware::handle(
                $permissionRepository,
                'designation.delete'
            );
        }

        $designationController->destroy(
            $desId
        );

        exit;
    }

    http_response_code(405);

    echo json_encode([
        'success' => false,
        'message' => 'Method not allowed.'
    ]);

    exit;
}


/*
|--------------------------------------------------------------------------
| Dynamic QR Lookup
|--------------------------------------------------------------------------
*/

if (
    preg_match(
        '#^/api/qr/([A-Za-z0-9]+)$#',
        $uri,
        $matches
    )
) {

    $employeeCode =
        strtoupper(
            trim($matches[1])
        );

    if ($method === 'GET') {

        AuthMiddleware::handle();

        PermissionMiddleware::handle(
            $permissionRepository,
            'employee.view'
        );

        $qrController->lookup(
            $employeeCode
        );

        exit;
    }

    http_response_code(405);

    echo json_encode([
        'success' => false,
        'message' => 'Method not allowed.'
    ]);

    exit;
}


/*
|--------------------------------------------------------------------------
| Leave Type
|--------------------------------------------------------------------------
*/

if (
    preg_match(
        '#^/api/leave-types/([0-9]+)$#',
        $uri,
        $matches
    )
) {

    $id =
        (int) $matches[1];

    if ($id <= 0) {

        http_response_code(400);

        echo json_encode([
            'success' => false,
            'message' => 'Invalid leave type ID.'
        ]);

        exit;
    }

    if ($method === 'GET') {

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

        $leaveController->showType($id);

        exit;
    }

    if (
        $method === 'PUT' ||
        $method === 'PATCH'
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

        $leaveController->updateType($id);

        exit;
    }

    http_response_code(405);

    echo json_encode([
        'success' => false,
        'message' => 'Method not allowed.'
    ]);

    exit;
}


/*
|--------------------------------------------------------------------------
| Leave Request
|--------------------------------------------------------------------------
*/

if (
    preg_match(
        '#^/api/leave-requests/([0-9]+)$#',
        $uri,
        $matches
    )
) {

    $id =
        (int) $matches[1];

    if ($id <= 0) {

        http_response_code(400);

        echo json_encode([
            'success' => false,
            'message' => 'Invalid leave request ID.'
        ]);

        exit;
    }

    if ($method === 'GET') {

        AuthMiddleware::handle();

        PermissionMiddleware::handle(
            $permissionRepository,
            'settings.manage'
        );

        $leaveController->showRequest($id);

        exit;
    }

    http_response_code(405);

    echo json_encode([
        'success' => false,
        'message' => 'Method not allowed.'
    ]);

    exit;
}


/*
|--------------------------------------------------------------------------
| Leave Request Approve
|--------------------------------------------------------------------------
*/

if (
    preg_match(
        '#^/api/leave-requests/([0-9]+)/approve$#',
        $uri,
        $matches
    )
) {

    $id =
        (int) $matches[1];

    if ($id <= 0) {

        http_response_code(400);

        echo json_encode([
            'success' => false,
            'message' => 'Invalid leave request ID.'
        ]);

        exit;
    }

    if (
        $method === 'PATCH' ||
        $method === 'PUT'
    ) {

        AuthMiddleware::handle();

        PermissionMiddleware::handle(
            $permissionRepository,
            'settings.manage'
        );

        $leaveController->approve($id);

        exit;
    }

    http_response_code(405);

    echo json_encode([
        'success' => false,
        'message' => 'Method not allowed.'
    ]);

    exit;
}


/*
|--------------------------------------------------------------------------
| Leave Request Reject
|--------------------------------------------------------------------------
*/

if (
    preg_match(
        '#^/api/leave-requests/([0-9]+)/reject$#',
        $uri,
        $matches
    )
) {

    $id =
        (int) $matches[1];

    if ($id <= 0) {

        http_response_code(400);

        echo json_encode([
            'success' => false,
            'message' => 'Invalid leave request ID.'
        ]);

        exit;
    }

    if (
        $method === 'PATCH' ||
        $method === 'PUT'
    ) {

        AuthMiddleware::handle();

        PermissionMiddleware::handle(
            $permissionRepository,
            'settings.manage'
        );

        $leaveController->reject($id);

        exit;
    }

    http_response_code(405);

    echo json_encode([
        'success' => false,
        'message' => 'Method not allowed.'
    ]);

    exit;
}


/*
|--------------------------------------------------------------------------
| Leave Balances
|--------------------------------------------------------------------------
*/

if (
    preg_match(
        '#^/api/leave-balances/([0-9]+)$#',
        $uri,
        $matches
    )
) {

    $employeeId =
        (int) $matches[1];

    if ($employeeId <= 0) {

        http_response_code(400);

        echo json_encode([
            'success' => false,
            'message' => 'Invalid employee ID.'
        ]);

        exit;
    }

    if ($method === 'GET') {

        AuthMiddleware::handle();

        PermissionMiddleware::handle(
            $permissionRepository,
            'settings.manage'
        );

        $leaveController->balances(
            $employeeId
        );

        exit;
    }

    http_response_code(405);

    echo json_encode([
        'success' => false,
        'message' => 'Method not allowed.'
    ]);

    exit;
}


/*
|--------------------------------------------------------------------------
| HR Leave Approval
|--------------------------------------------------------------------------
*/

if (
    preg_match(
        '#^/api/hr/leave/([0-9]+)/approve$#',
        $uri,
        $matches
    )
) {

    $id =
        (int) $matches[1];

    if ($id <= 0) {

        http_response_code(400);

        echo json_encode([
            'success' => false,
            'message' => 'Invalid leave request ID.'
        ]);

        exit;
    }

    if ($method === 'POST') {

        AuthMiddleware::handle();

        \App\Middleware\Hr\HrRoleMiddleware::handle();

        $hrController->approveLeave($id);

        exit;
    }

    http_response_code(405);

    echo json_encode([
        'success' => false,
        'message' => 'Method not allowed.'
    ]);

    exit;
}


/*
|--------------------------------------------------------------------------
| HR Leave Rejection
|--------------------------------------------------------------------------
*/

if (
    preg_match(
        '#^/api/hr/leave/([0-9]+)/reject$#',
        $uri,
        $matches
    )
) {

    $id =
        (int) $matches[1];

    if ($id <= 0) {

        http_response_code(400);

        echo json_encode([
            'success' => false,
            'message' => 'Invalid leave request ID.'
        ]);

        exit;
    }

    if ($method === 'POST') {

        AuthMiddleware::handle();

        \App\Middleware\Hr\HrRoleMiddleware::handle();

        $hrController->rejectLeave($id);

        exit;
    }

    http_response_code(405);

    echo json_encode([
        'success' => false,
        'message' => 'Method not allowed.'
    ]);

    exit;
}


/*
|--------------------------------------------------------------------------
| HR Payroll Payslip
|--------------------------------------------------------------------------
*/

if (
    preg_match(
        '#^/api/hr/payroll/payslips/([0-9]+)$#',
        $uri,
        $matches
    )
) {

    $payrollRecordId =
        (int) $matches[1];

    if ($payrollRecordId <= 0) {

        http_response_code(400);

        echo json_encode([
            'success' => false,
            'message' => 'Invalid payroll record ID.'
        ]);

        exit;
    }

    if ($method === 'GET') {

        AuthMiddleware::handle();

        \App\Middleware\Hr\HrRoleMiddleware::handle();

        $payrollController->payslip(
            $payrollRecordId
        );

        exit;
    }

    http_response_code(405);

    echo json_encode([
        'success' => false,
        'message' => 'Method not allowed.'
    ]);

    exit;
}


/*
|--------------------------------------------------------------------------
| HR Payroll Status
|--------------------------------------------------------------------------
*/

if (
    preg_match(
        '#^/api/hr/payroll/([0-9]+)/status$#',
        $uri,
        $matches
    )
) {

    $payrollRecordId =
        (int) $matches[1];

    if ($payrollRecordId <= 0) {

        http_response_code(400);

        echo json_encode([
            'success' => false,
            'message' => 'Invalid payroll record ID.'
        ]);

        exit;
    }

    if (
        $method === 'POST' ||
        $method === 'PATCH' ||
        $method === 'PUT'
    ) {

        AuthMiddleware::handle();

        \App\Middleware\Hr\HrRoleMiddleware::handle();

        $payrollController->updateStatus(
            $payrollRecordId
        );

        exit;
    }

    http_response_code(405);

    echo json_encode([
        'success' => false,
        'message' => 'Method not allowed.'
    ]);

    exit;
}


/*
|--------------------------------------------------------------------------
| Route Not Found
|--------------------------------------------------------------------------
*/

http_response_code(404);

echo json_encode([
    'success' => false,
    'message' => 'Route not found.',
    'method' => $method,
    'uri' => $uri
]);

exit;
<?php
$modules = [
    'attendance' => ['Attendance', 'Monitor today\'s attendance and workforce presence.'],
    'attendance-history' => ['Attendance History', 'Review historical attendance records.'],
    'attendance-corrections' => ['Attendance Corrections', 'Review and manage attendance corrections.'],
    'leave' => ['Leave Management', 'Manage leave activity across the organization.'],
    'leave-requests' => ['Leave Requests', 'Review leave requests that need HR action.'],
    'leave-balance' => ['Leave Balance', 'Review employee leave balances.'],
    'leave-calendar' => ['Leave Calendar', 'View planned employee leave.'],
    'holidays' => ['Holidays', 'Manage organization holidays.'],
    'recruitment' => ['Recruitment', 'Track hiring activity and open positions.'],
    'job-openings' => ['Job Openings', 'Manage current job openings.'],
    'candidates' => ['Candidates', 'Review recruitment candidates.'],
    'interviews' => ['Interviews', 'Coordinate upcoming interviews.'],
    'offers' => ['Offers', 'Manage candidate offers.'],
    'onboarding-exit' => ['Onboarding & Exit', 'Manage employee lifecycle workflows.'],
    'onboarding' => ['Onboarding', 'Track new employee onboarding.'],
    'resignations' => ['Resignations', 'Review employee resignations.'],
    'exit-process' => ['Exit Process', 'Manage employee exit tasks.'],
    'clearance' => ['Clearance', 'Track employee clearance tasks.'],
    'payroll' => ['Payroll', 'Review payroll operations.'],
    'salary' => ['Salary', 'Manage salary information.'],
    'payslips' => ['Payslips', 'Access employee payslips.'],
    'performance-training' => ['Performance & Training', 'Track performance and learning.'],
    'performance-reviews' => ['Performance Reviews', 'Manage employee reviews.'],
    'goals-kpis' => ['Goals / KPIs', 'Track employee goals and KPIs.'],
    'training' => ['Training', 'Manage training programs.'],
    'reports' => ['Reports', 'Generate HR reports and insights.'],
];
$key = preg_replace('/[^a-z-]/', '', strtolower((string) ($_GET['module'] ?? 'reports')));
[$title, $description] = $modules[$key] ?? $modules['reports'];
if ($key === 'leave') {
    header('Location: hr-leave.php');
    exit;
}
if ($key === 'attendance') {
    header('Location: hr-attendance.php');
    exit;
}
if ($key === 'recruitment') {
    header('Location: hr-recruitment.php');
    exit;
}
if ($key === 'onboarding-exit') {
    header('Location: hr-onboarding-exit.php');
    exit;
}
if ($key === 'payroll' || $key === 'salary' || $key === 'payslips') {
    header('Location: hr-payroll.php');
    exit;
}
if (in_array($key, ['performance-training', 'performance-reviews', 'goals-kpis'], true)) {
    header('Location: hr-performance.php');
    exit;
}
if ($key === 'training') {
    header('Location: hr-training.php');
    exit;
}
if (in_array($key, ['reports', 'analytics-reports'], true)) {
    header('Location: hr-reports.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title) ?> - HR Panel</title>
    <link rel="stylesheet" href="../assets/css/app-theme.css?v=1">
    <link rel="stylesheet" href="../assets/css/hr-shell.css?v=1">
    <link rel="stylesheet" href="../assets/css/hr-module.css?v=1">
</head>
<body>
<div class="app">
    <?php include '../includes/hr-sidebar.php'; ?>
    <main class="main-content">
        <?php include '../includes/hr-topbar.php'; ?>
        <section class="content hr-module-page">
            <p class="eyebrow">HR WORKSPACE</p>
            <h1><?= htmlspecialchars($title) ?></h1>
            <p class="module-description"><?= htmlspecialchars($description) ?></p>
            <div class="module-grid"><article><span>◷</span><strong>Coming next</strong><small>This HR workspace is ready for its module data and actions.</small></article><article><span>▤</span><strong>Stay organized</strong><small>Use the HR navigation to move between workforce workflows.</small></article></div>
        </section>
    </main>
</div>
<script src="../assets/js/hr-module.js?v=1"></script>
</body>
</html>

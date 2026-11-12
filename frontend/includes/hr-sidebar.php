<?php
// frontend/includes/hr-sidebar.php
// Jobie-Inspired Modern Curved Sidebar for HR Portal (Preserving HR Color Palette)

$currentPage = basename($_SERVER['PHP_SELF']);
$currentModule = strtolower((string)($_GET['module'] ?? ''));

$isDashboard = ($currentPage === 'hr-dashboard.php');
$isEmployees = ($currentPage === 'hr-employees.php');
$isAttendance = ($currentPage === 'hr-attendance.php' || ($currentPage === 'hr-module.php' && in_array($currentModule, ['attendance', 'attendance-history', 'attendance-corrections'], true)));
$isLeave = ($currentPage === 'hr-leave.php' || ($currentPage === 'hr-module.php' && in_array($currentModule, ['leave', 'leave-requests', 'leave-balance', 'leave-calendar', 'holidays'], true)));
$isRecruitment = (in_array($currentPage, ['hr-recruitment.php', 'hr-onboarding-exit.php'], true) || ($currentPage === 'hr-module.php' && in_array($currentModule, ['recruitment', 'job-openings', 'candidates', 'interviews', 'offers', 'onboarding-exit', 'onboarding', 'resignations', 'exit-process', 'clearance'], true)));
$isPayroll = ($currentPage === 'hr-payroll.php' || ($currentPage === 'hr-module.php' && in_array($currentModule, ['payroll', 'salary', 'payslips'], true)));
$isPerformance = ($currentPage === 'hr-performance.php' || ($currentPage === 'hr-module.php' && in_array($currentModule, ['performance-training', 'performance-reviews', 'goals-kpis'], true)));
$isTraining = ($currentPage === 'hr-training.php' || ($currentPage === 'hr-module.php' && $currentModule === 'training'));
$isReports = ($currentPage === 'hr-reports.php' || ($currentPage === 'hr-module.php' && in_array($currentModule, ['reports', 'analytics-reports'], true)));
?>
<!-- =====================================================
     JOBIE-INSPIRED MODERN CURVED SIDEBAR — HR PORTAL
====================================================== -->
<aside class="sidebar hr-sidebar" id="hrSidebar">

    <!-- BRAND HEADER -->
    <div class="sidebar-brand hr-brand">
        <div class="brand-icon hr-brand-icon">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                <circle cx="9" cy="7" r="4"/>
                <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
                <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
            </svg>
        </div>

        <div class="brand-text hr-brand-text">
            <span class="brand-title">HR Portal</span>
            <span class="brand-sub">People &amp; Culture</span>
        </div>
    </div>

    <!-- NAVIGATION MENU -->
    <nav class="sidebar-menu hr-nav">

        <a href="hr-dashboard.php" class="menu-item <?= $isDashboard ? 'active' : '' ?>">
            <span class="menu-icon">
                <svg width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="3" width="7" height="7" rx="1.5"/><rect x="14" y="3" width="7" height="7" rx="1.5"/>
                    <rect x="14" y="14" width="7" height="7" rx="1.5"/><rect x="3" y="14" width="7" height="7" rx="1.5"/>
                </svg>
            </span>
            <span>Dashboard</span>
        </a>

        <a href="hr-employees.php" class="menu-item <?= $isEmployees ? 'active' : '' ?>">
            <span class="menu-icon">
                <svg width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                    <circle cx="9" cy="7" r="4"/>
                    <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
                    <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                </svg>
            </span>
            <span>Employees</span>
        </a>

        <a href="hr-attendance.php" class="menu-item <?= $isAttendance ? 'active' : '' ?>">
            <span class="menu-icon">
                <svg width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"/>
                    <polyline points="12 6 12 12 16 14"/>
                </svg>
            </span>
            <span>Attendance</span>
        </a>

        <a href="hr-leave.php" class="menu-item <?= $isLeave ? 'active' : '' ?>">
            <span class="menu-icon">
                <svg width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="4" width="18" height="18" rx="3"/>
                    <line x1="16" y1="2" x2="16" y2="6"/>
                    <line x1="8" y1="2" x2="8" y2="6"/>
                    <line x1="3" y1="10" x2="21" y2="10"/>
                    <path d="M8 14h3v4H8z"/>
                </svg>
            </span>
            <span>Leave</span>
        </a>

        <a href="hr-recruitment.php" class="menu-item <?= $isRecruitment ? 'active' : '' ?>">
            <span class="menu-icon">
                <svg width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/>
                    <circle cx="9" cy="7" r="4"/>
                    <line x1="19" y1="8" x2="19" y2="14"/>
                    <line x1="22" y1="11" x2="16" y2="11"/>
                </svg>
            </span>
            <span>Recruitment &amp; Onboarding</span>
        </a>

        <a href="hr-payroll.php" class="menu-item <?= $isPayroll ? 'active' : '' ?>">
            <span class="menu-icon">
                <svg width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="2" y="5" width="20" height="14" rx="2"/>
                    <line x1="2" y1="10" x2="22" y2="10"/>
                </svg>
            </span>
            <span>Payroll</span>
        </a>

        <a href="hr-performance.php" class="menu-item <?= $isPerformance ? 'active' : '' ?>">
            <span class="menu-icon">
                <svg width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12 20v-6"/><path d="M18 20V4"/><path d="M6 20v-4"/>
                </svg>
            </span>
            <span>Performance</span>
        </a>

        <a href="hr-training.php" class="menu-item <?= $isTraining ? 'active' : '' ?>">
            <span class="menu-icon">
                <svg width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M22 10L12 5 2 10l10 5 10-5z"/><path d="M6 12.5V17c0 1.66 2.69 3 6 3s6-1.34 6-3v-4.5"/>
                </svg>
            </span>
            <span>Training</span>
        </a>

        <a href="hr-reports.php" class="menu-item <?= $isReports ? 'active' : '' ?>">
            <span class="menu-icon">
                <svg width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="18" y1="20" x2="18" y2="10"/>
                    <line x1="12" y1="20" x2="12" y2="4"/>
                    <line x1="6" y1="20" x2="6" y2="14"/>
                </svg>
            </span>
            <span>Reports</span>
        </a>

    </nav>

    <!-- SIDEBAR BOTTOM -->
    <div class="sidebar-bottom hr-sidebar-bottom">
        <button type="button" id="logoutButton" class="logout-button hr-logout" onclick="handleHrLogout(event)">
            <span class="menu-icon">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
                    <polyline points="16 17 21 12 16 7"/>
                    <line x1="21" y1="12" x2="9" y2="12"/>
                </svg>
            </span>
            <span>Logout</span>
        </button>

        <div class="sidebar-footer-info">
            <div class="footer-title">HR Management Portal</div>
            <div class="footer-copy">&copy; <?= date('Y') ?> All Rights Reserved</div>
        </div>
    </div>

</aside>

<script>
function handleHrLogout(e) {
    if (e) e.preventDefault();
    localStorage.removeItem('token');
    localStorage.removeItem('auth_token');
    localStorage.removeItem('auth_user');
    localStorage.removeItem('username');
    window.location.href = 'login.php';
}
</script>

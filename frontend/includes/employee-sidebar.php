<?php
$currentPage = basename($_SERVER['PHP_SELF']);
?>
<!-- =====================================================
     JOBIE-INSPIRED MODERN CURVED SIDEBAR — EMPLOYEE PORTAL
====================================================== -->
<aside class="sidebar emp-sidebar" id="empSidebar">

    <!-- BRAND HEADER -->
    <div class="sidebar-brand">
        <div class="brand-icon emp-brand-icon">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                <circle cx="12" cy="7" r="4"/>
            </svg>
        </div>

        <div class="brand-text">
            <span class="brand-title">EmployeePortal</span>
            <span class="brand-sub">Self-Service Suite</span>
        </div>
    </div>

    <!-- NAVIGATION MENU -->
    <nav class="sidebar-menu">

        <!-- 1. Dashboard -->
        <a href="employee-dashboard.php" id="nav-dashboard"
           class="menu-item <?= ($currentPage === 'employee-dashboard.php' || $currentPage === '') ? 'active' : '' ?>">
            <span class="menu-icon">
                <svg width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="3" width="7" height="7" rx="1.5"/><rect x="14" y="3" width="7" height="7" rx="1.5"/>
                    <rect x="14" y="14" width="7" height="7" rx="1.5"/><rect x="3" y="14" width="7" height="7" rx="1.5"/>
                </svg>
            </span>
            <span>Dashboard</span>
        </a>

        <!-- 2. My Attendance -->
        <a href="employee-attendance.php" id="nav-attendance"
           class="menu-item <?= ($currentPage === 'employee-attendance.php') ? 'active' : '' ?>">
            <span class="menu-icon">
                <svg width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"/>
                    <polyline points="12 6 12 12 16 14"/>
                </svg>
            </span>
            <span>My Attendance</span>
        </a>

        <!-- 3. Documents & Payroll -->
        <a href="employee-documents.php" id="nav-documents"
           class="menu-item <?= ($currentPage === 'employee-documents.php' || $currentPage === 'employee-documents-payroll.php') ? 'active' : '' ?>">
            <span class="menu-icon">
                <svg width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                    <polyline points="14 2 14 8 20 8"/>
                    <line x1="16" y1="13" x2="8" y2="13"/>
                    <line x1="16" y1="17" x2="8" y2="17"/>
                    <polyline points="10 9 9 9 8 9"/>
                </svg>
            </span>
            <span>Documents &amp; Payroll</span>
        </a>

        <!-- 4. Performance & Training -->
        <a href="employee-performance.php" id="nav-performance"
           class="menu-item <?= ($currentPage === 'employee-performance.php') ? 'active' : '' ?>">
            <span class="menu-icon">
                <svg width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="18" y1="20" x2="18" y2="10"/>
                    <line x1="12" y1="20" x2="12" y2="4"/>
                    <line x1="6" y1="20" x2="6" y2="14"/>
                </svg>
            </span>
            <span>Performance &amp; Training</span>
        </a>

    </nav>

    <!-- SIDEBAR BOTTOM -->
    <div class="sidebar-bottom">
        <button type="button" id="empLogoutButton" class="logout-button" onclick="handleEmpLogout(event)">
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
            <div class="footer-title">Employee Portal Suite</div>
            <div class="footer-copy">&copy; <?= date('Y') ?> All Rights Reserved</div>
        </div>
    </div>

</aside>

<script>
function handleEmpLogout(e) {
    if (e) e.preventDefault();
    localStorage.removeItem('token');
    localStorage.removeItem('auth_token');
    localStorage.removeItem('auth_user');
    localStorage.removeItem('username');
    window.location.href = '../login.php';
}
</script>

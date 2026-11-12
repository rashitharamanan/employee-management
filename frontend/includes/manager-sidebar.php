<?php
$currentPage = basename($_SERVER['PHP_SELF']);
?>
<!-- =====================================================
     JOBIE-INSPIRED MODERN CURVED SIDEBAR — MANAGER PORTAL
====================================================== -->
<aside class="sidebar mgr-sidebar" id="mgrSidebar">

    <!-- BRAND HEADER -->
    <div class="sidebar-brand">
        <div class="brand-icon mgr-brand-icon">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                <circle cx="9" cy="7" r="4"/>
                <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
                <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
            </svg>
        </div>

        <div class="brand-text">
            <span class="brand-title">ManagerPortal</span>
            <span class="brand-sub">Team Management</span>
        </div>
    </div>

    <!-- NAVIGATION MENU -->
    <nav class="sidebar-menu">

        <!-- 1. Dashboard -->
        <a href="manager-dashboard.php" class="menu-item <?= ($currentPage === 'manager-dashboard.php' || $currentPage === '') ? 'active' : '' ?>">
            <span class="menu-icon">
                <svg width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="3" width="7" height="7" rx="1.5"/><rect x="14" y="3" width="7" height="7" rx="1.5"/>
                    <rect x="14" y="14" width="7" height="7" rx="1.5"/><rect x="3" y="14" width="7" height="7" rx="1.5"/>
                </svg>
            </span>
            <span>Dashboard</span>
        </a>

        <!-- 2. My Team -->
        <a href="manager-team.php" class="menu-item <?= ($currentPage === 'manager-team.php') ? 'active' : '' ?>">
            <span class="menu-icon">
                <svg width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                    <circle cx="9" cy="7" r="4"/>
                    <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
                    <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                </svg>
            </span>
            <span>My Team</span>
        </a>

        <!-- 3. Attendance -->
        <a href="manager-attendance.php" class="menu-item <?= ($currentPage === 'manager-attendance.php') ? 'active' : '' ?>">
            <span class="menu-icon">
                <svg width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                    <line x1="16" y1="2" x2="16" y2="6"/>
                    <line x1="8" y1="2" x2="8" y2="6"/>
                    <line x1="3" y1="10" x2="21" y2="10"/>
                    <polyline points="9 15 12 18 15 15"/>
                </svg>
            </span>
            <span>Attendance</span>
        </a>

        <!-- 4. Performance -->
        <a href="manager-performance.php" class="menu-item <?= ($currentPage === 'manager-performance.php') ? 'active' : '' ?>">
            <span class="menu-icon">
                <svg width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="18" y1="20" x2="18" y2="10"/>
                    <line x1="12" y1="20" x2="12" y2="4"/>
                    <line x1="6" y1="20" x2="6" y2="14"/>
                </svg>
            </span>
            <span>Performance</span>
        </a>

        <!-- 5. Reports -->
        <a href="manager-reports.php" class="menu-item <?= ($currentPage === 'manager-reports.php') ? 'active' : '' ?>">
            <span class="menu-icon">
                <svg width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                    <polyline points="14 2 14 8 20 8"/>
                    <line x1="16" y1="13" x2="8" y2="13"/>
                    <line x1="16" y1="17" x2="8" y2="17"/>
                </svg>
            </span>
            <span>Reports</span>
        </a>

    </nav>

    <!-- SIDEBAR BOTTOM -->
    <div class="sidebar-bottom">
        <button type="button" id="logoutButton" class="logout-button" onclick="handleMgrLogout(event)">
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
            <div class="footer-title">Manager Portal Suite</div>
            <div class="footer-copy">&copy; <?= date('Y') ?> All Rights Reserved</div>
        </div>
    </div>

</aside>

<script>
function handleMgrLogout(e) {
    if (e) e.preventDefault();
    localStorage.removeItem('token');
    localStorage.removeItem('auth_token');
    localStorage.removeItem('auth_user');
    localStorage.removeItem('username');
    window.location.href = '../login.php';
}
</script>

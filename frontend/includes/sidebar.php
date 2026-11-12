<?php
$currentPage = basename($_SERVER['PHP_SELF']);
?>
<!-- =====================================================
     JOBIE-INSPIRED MODERN CURVED SIDEBAR
====================================================== -->
<aside class="sidebar" id="adminSidebar">

    <!-- BRAND HEADER -->
    <div class="sidebar-brand">
        <div class="brand-icon">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/>
            </svg>
        </div>

        <div class="brand-text">
            <span class="brand-title">AdminPortal</span>
            <span class="brand-sub">Management Suite</span>
        </div>
    </div>

    <!-- NAVIGATION MENU -->
    <nav class="sidebar-menu">

        <a href="<?= $currentPage === 'hr-dashboard.php' ? 'hr-dashboard.php' : 'dashboard.php' ?>" class="menu-item <?= in_array($currentPage, ['dashboard.php', 'hr-dashboard.php'], true) ? 'active' : '' ?>">
            <span class="menu-icon">
                <svg width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="3" width="7" height="7" rx="1.5"/><rect x="14" y="3" width="7" height="7" rx="1.5"/>
                    <rect x="14" y="14" width="7" height="7" rx="1.5"/><rect x="3" y="14" width="7" height="7" rx="1.5"/>
                </svg>
            </span>
            <span>Dashboard</span>
        </a>

        <a href="users.php" class="menu-item <?= ($currentPage === 'users.php') ? 'active' : '' ?>">
            <span class="menu-icon">
                <svg width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                    <circle cx="9" cy="7" r="4"/>
                    <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
                    <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                </svg>
            </span>
            <span>User Management</span>
        </a>

        <a href="organisations.php" class="menu-item <?= ($currentPage === 'organisations.php') ? 'active' : '' ?>">
            <span class="menu-icon">
                <svg width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
                    <polyline points="9 22 9 12 15 12 15 22"/>
                </svg>
            </span>
            <span>Organisation</span>
        </a>

        <a href="leave-management.php" class="menu-item <?= ($currentPage === 'leave-management.php') ? 'active' : '' ?>">
            <span class="menu-icon">
                <svg width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="4" width="18" height="18" rx="3"/>
                    <line x1="16" y1="2" x2="16" y2="6"/>
                    <line x1="8" y1="2" x2="8" y2="6"/>
                    <line x1="3" y1="10" x2="21" y2="10"/>
                    <path d="M8 14h3v4H8z"/>
                </svg>
            </span>
            <span>Leave Management</span>
        </a>


        <a href="policies-configuration.php" class="menu-item <?= ($currentPage === 'policies-configuration.php') ? 'active' : '' ?>">
            <span class="menu-icon">
                <svg width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                    <polyline points="14 2 14 8 20 8"/>
                    <line x1="16" y1="13" x2="8" y2="13"/>
                    <line x1="16" y1="17" x2="8" y2="17"/>
                </svg>
            </span>
            <span>Policies &amp; Config</span>
        </a>

        <a href="analytics-reports.php" class="menu-item <?= ($currentPage === 'analytics-reports.php') ? 'active' : '' ?>">
            <span class="menu-icon">
                <svg width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="18" y1="20" x2="18" y2="10"/>
                    <line x1="12" y1="20" x2="12" y2="4"/>
                    <line x1="6" y1="20" x2="6" y2="14"/>
                </svg>
            </span>
            <span>Analytics &amp; Reports</span>
        </a>

        <a href="system-settings.php" class="menu-item <?= ($currentPage === 'system-settings.php') ? 'active' : '' ?>">
            <span class="menu-icon">
                <svg width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="3"/>
                    <path d="M19.4 15a1.7 1.7 0 0 0 .34 1.88l.06.06-1.4 1.4-.06-.06A1.7 1.7 0 0 0 16.46 18a1.7 1.7 0 0 0-1 .98V19a1.7 1.7 0 0 0-1.7 1.7v.08h-2v-.08A1.7 1.7 0 0 0 10.06 19h-.08a1.7 1.7 0 0 0-1-.98 1.7 1.7 0 0 0-1.88.34l-.06.06-1.4-1.4.06-.06A1.7 1.7 0 0 0 6 15.04a1.7 1.7 0 0 0-.98-1H5A1.7 1.7 0 0 0 3.3 12.3H3.2v-2h.1A1.7 1.7 0 0 0 5 9.06v-.08a1.7 1.7 0 0 0-.34-1.88L4.6 7.04 6 5.64l.06.06A1.7 1.7 0 0 0 7.94 6a1.7 1.7 0 0 0 1-.98V5A1.7 1.7 0 0 0 10.64 3.3h2A1.7 1.7 0 0 0 14.34 5v.02a1.7 1.7 0 0 0 1 .98 1.7 1.7 0 0 0 1.88-.34l.06-.06 1.4 1.4-.06.06A1.7 1.7 0 0 0 18.28 9v.08a1.7 1.7 0 0 0 .98 1h.1v2h-.1a1.7 1.7 0 0 0-1.7 1.7v.08a1.7 1.7 0 0 0 1.84 1.14z"/>
                </svg>
            </span>
            <span>System Settings</span>
        </a>

        <a href="scanner.php" class="menu-item <?= ($currentPage === 'scanner.php') ? 'active' : '' ?>">
            <span class="menu-icon">
                <svg width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="3" width="7" height="7" rx="1.5"/><rect x="14" y="3" width="7" height="7" rx="1.5"/>
                    <rect x="3" y="14" width="7" height="7" rx="1.5"/>
                    <path d="M14 14h3v3h-3zM17 17h3v3h-3zM14 20h3v1h-3zM20 14h1v3h-1z"/>
                </svg>
            </span>
            <span>QR Scanner</span>
        </a>

    </nav>

    <!-- SIDEBAR BOTTOM -->
    <div class="sidebar-bottom">
        <button type="button" id="logoutButton" class="logout-button" onclick="handleAdminLogout(event)">
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
            <div class="footer-title">Admin Management Portal</div>
            <div class="footer-copy">&copy; <?= date('Y') ?> All Rights Reserved</div>
        </div>
    </div>

</aside>

<script>
function handleAdminLogout(e) {
    if (e) e.preventDefault();
    localStorage.removeItem('token');
    localStorage.removeItem('auth_token');
    localStorage.removeItem('auth_user');
    localStorage.removeItem('username');
    sessionStorage.clear();

    const path = window.location.pathname;
    if (path.includes('/manager/') || path.includes('/employee/')) {
        window.location.href = '../login.php';
    } else {
        window.location.href = 'login.php';
    }
}

document.addEventListener('DOMContentLoaded', function () {
    const btn = document.getElementById('logoutButton');
    if (btn && !btn._logoutBound) {
        btn._logoutBound = true;
        btn.addEventListener('click', handleAdminLogout);
    }
});
</script>

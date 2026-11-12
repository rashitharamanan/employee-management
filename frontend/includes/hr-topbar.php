<?php
// frontend/includes/hr-topbar.php
// Jobie-Style Master Topbar for HR Portal (Preserving HR Color Palette)

$currentPage = basename($_SERVER['PHP_SELF']);

$pageTitles = [
    'hr-dashboard.php'       => 'Dashboard',
    'hr-employees.php'       => 'Employees',
    'hr-attendance.php'      => 'Attendance',
    'hr-leave.php'           => 'Leave Management',
    'hr-recruitment.php'     => 'Recruitment & Onboarding',
    'hr-onboarding-exit.php' => 'Recruitment & Onboarding',
    'hr-payroll.php'         => 'Payroll',
    'hr-performance.php'     => 'Performance',
    'hr-training.php'        => 'Training',
    'hr-reports.php'         => 'Reports',
    'hr-module.php'          => 'HR Workspace'
];

$currentTitle = $pageTitles[$currentPage] ?? ucwords(trim(str_replace(['hr-', '-', '.php'], ['', ' ', ''], $currentPage)));
?>
<link rel="stylesheet" href="../assets/css/hr-shell.css?v=<?= time() ?>">
<!-- =====================================================
     JOBIE-STYLE MASTER TOPBAR — HR PORTAL
====================================================== -->
<header class="topbar hr-topbar jobie-topbar" id="appTopbar">

    <!-- LEFT: HAMBURGER & PAGE TITLE -->
    <div class="topbar-left">
        <button type="button" id="mobileMenuButton" class="topbar-toggle-btn" aria-label="Toggle navigation">
            <svg width="22" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round">
                <line x1="3" y1="6" x2="21" y2="6"/>
                <line x1="3" y1="12" x2="16" y2="12"/>
                <line x1="3" y1="18" x2="21" y2="18"/>
            </svg>
        </button>
        <h1 class="topbar-page-title"><?= htmlspecialchars($currentTitle) ?></h1>
    </div>

    <!-- CENTER: PILL SEARCH BAR -->
    <div class="topbar-center">
        <div class="top-search jobie-search-pill" id="topSearchWrapper">
            <input type="text" id="searchInput" class="jobie-search-input search-field" placeholder="Search employees, attendance, leaves..." autocomplete="off">
            <button type="button" class="jobie-search-btn search-icon-btn" aria-label="Search">
                <svg class="jobie-search-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                </svg>
            </button>
        </div>
    </div>

    <!-- RIGHT: CHAT, NOTIFICATIONS & USER PROFILE -->
    <div class="topbar-right">
        <!-- Message Action & Dropdown -->
        <div class="topbar-action-wrapper" style="position:relative;">
            <button type="button" class="topbar-action-btn" id="topbarMsgBtn" title="Messages" aria-label="Messages">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
                </svg>
                <span class="action-badge" id="topbarMsgCount" style="display:none;">0</span>
            </button>

            <!-- MESSAGES DROPDOWN PANEL -->
            <div class="topbar-dropdown-panel topbar-msg-panel" id="topbarMsgPanel" style="display:none;">
                <div class="topbar-dropdown-header">
                    <div class="topbar-dropdown-title">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
                        </svg>
                        <strong>Messages</strong>
                        <span class="topbar-count-pill pill-blue" id="topbarMsgDropdownCount">0 new</span>
                    </div>
                    <button type="button" class="topbar-mark-all-btn" id="topbarMsgMarkAllBtn">Mark read</button>
                </div>
                <div class="topbar-dropdown-body" id="topbarMsgList">
                    <div class="topbar-dropdown-empty">
                        <div style="font-size:24px; margin-bottom:6px;">💬</div>
                        <div>No new direct messages</div>
                        <small style="color:#94a3b8; font-size:11.5px; display:block; margin-top:3px;">All team conversations are up to date</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Notification Bell Action & Dropdown -->
        <div class="topbar-action-wrapper" style="position:relative;">
            <button type="button" class="topbar-action-btn" id="notifBtn" title="Notifications" aria-label="Notifications">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/>
                    <path d="M13.73 21a2 2 0 0 1-3.46 0"/>
                </svg>
                <span class="action-badge" id="topbarNotifCount" style="display:none;">0</span>
            </button>

            <!-- NOTIFICATIONS DROPDOWN PANEL -->
            <div class="topbar-dropdown-panel topbar-notif-panel" id="topbarNotifPanel" style="display:none;">
                <div class="topbar-dropdown-header">
                    <div class="topbar-dropdown-title">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/>
                            <path d="M13.73 21a2 2 0 0 1-3.46 0"/>
                        </svg>
                        <strong>Notifications</strong>
                        <span class="topbar-count-pill" id="topbarNotifDropdownCount">0 new</span>
                    </div>
                    <button type="button" class="topbar-mark-all-btn" id="topbarNotifMarkAllBtn">Mark read</button>
                </div>
                <div class="topbar-dropdown-body" id="topbarNotifList">
                    <div class="topbar-dropdown-empty">
                        <div style="font-size:24px; margin-bottom:6px;">🔔</div>
                        <div>No notifications</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- User Profile -->
        <div class="topbar-profile" id="adminMenu" tabindex="0" role="button" aria-label="User profile">
            <div class="profile-avatar" id="adminAvatar">H</div>
            <div class="profile-details">
                <strong class="profile-name" id="adminUsername">HR Manager</strong>
                <span class="profile-role" id="adminRole">HR Department</span>
            </div>
        </div>
    </div>

</header>

<?php include __DIR__ . '/profile-modal.php'; ?>

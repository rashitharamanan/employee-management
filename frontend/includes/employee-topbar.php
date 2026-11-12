<?php
// frontend/includes/employee-topbar.php
// Jobie-Style Master Topbar for Employee Portal (Matches Admin, Manager, and HR)

$currentPage = basename($_SERVER['PHP_SELF']);

$pageTitles = [
    'employee-dashboard.php'   => 'Dashboard',
    'employee-attendance.php'  => 'My Attendance',
    'employee-documents.php'   => 'Documents & Payroll',
    'employee-performance.php' => 'Performance & Training'
];

$currentTitle = $pageTitles[$currentPage] ?? ucwords(trim(str_replace(['employee-', '-', '.php'], ['', ' ', ''], $currentPage)));
?>
<!-- =====================================================
     JOBIE-STYLE MASTER TOPBAR — EMPLOYEE PORTAL
====================================================== -->
<header class="topbar emp-topbar jobie-topbar" id="appTopbar">

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
            <input type="text" id="empSearchInput" class="jobie-search-input search-field" placeholder="Search tasks, documents, attendance..." autocomplete="off">
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
        <div class="emp-notif-wrapper topbar-action-wrapper" style="position:relative;">
            <button type="button" class="topbar-action-btn" id="empNotifBtn" title="Notifications" aria-label="Notifications">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/>
                    <path d="M13.73 21a2 2 0 0 1-3.46 0"/>
                </svg>
                <span class="action-badge" id="empNotifCount" style="display:none;">0</span>
            </button>

            <!-- NOTIFICATIONS DROPDOWN PANEL -->
            <div class="emp-notif-panel topbar-dropdown-panel topbar-notif-panel" id="empNotifPanel" style="display:none;">
                <div class="emp-notif-header topbar-dropdown-header">
                    <div class="emp-notif-header-title topbar-dropdown-title">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/>
                            <path d="M13.73 21a2 2 0 0 1-3.46 0"/>
                        </svg>
                        <strong>Notifications</strong>
                        <span class="emp-notif-count-pill topbar-count-pill" id="empNotifDropdownCount">0 new</span>
                    </div>
                    <button type="button" class="emp-notif-mark-all topbar-mark-all-btn" id="empNotifMarkAllBtn">Mark read</button>
                </div>
                <div class="emp-notif-body topbar-dropdown-body" id="empNotifList">
                    <div class="emp-notif-empty topbar-dropdown-empty">No notifications</div>
                </div>
            </div>
        </div>

        <!-- User Profile -->
        <div class="topbar-profile" id="empProfilePill" tabindex="0" role="button" aria-label="User profile">
            <div class="profile-avatar" id="empAvatarFallback">PN</div>
            <div class="profile-details">
                <strong class="profile-name" id="empTopbarUserName"><span id="userDisplayName">Priya Nair</span></strong>
                <span class="profile-role" id="empTopbarUserRole"><span id="userDisplayRole">UI/UX Designer</span></span>
            </div>
        </div>
    </div>

</header>

<?php include __DIR__ . '/profile-modal.php'; ?>

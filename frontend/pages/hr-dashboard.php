<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HR Dashboard - People & Culture</title>
    <link rel="stylesheet" href="../assets/css/app-theme.css?v=<?= time() ?>">
    <link rel="stylesheet" href="../assets/css/hr-shell.css?v=<?= time() ?>">
    <link rel="stylesheet" href="../assets/css/hr-dashboard.css?v=<?= time() ?>">
    <link rel="stylesheet" href="../assets/css/employee-wizard-modal.css?v=<?= time() ?>">
    <link rel="stylesheet" href="../assets/css/hr-quick-action-modals.css?v=<?= time() ?>">
</head>
<body>
<div class="app">
    <?php include '../includes/hr-sidebar.php'; ?>

    <main class="main-content">
        <?php include '../includes/hr-topbar.php'; ?>

        <section class="hr-dashboard-page">

            <!-- 1. GREETING & DATE HEADER -->
            <header class="hr-dash-header">
                <div class="hr-greeting-col">
                    <h1>Good morning, <span id="hrName">Vasundhara</span> 👋</h1>
                </div>
                <div class="hr-date-badge">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                        <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                        <line x1="16" y1="2" x2="16" y2="6"></line>
                        <line x1="8" y1="2" x2="8" y2="6"></line>
                        <line x1="3" y1="10" x2="21" y2="10"></line>
                    </svg>
                    <time id="currentDate">Wednesday, Sep 30, 2026</time>
                </div>
            </header>

            <!-- 2. TWO-COLUMN DASHBOARD GRID -->
            <div class="hr-dash-container">

                <!-- =========================================================
                     LEFT / MAIN COLUMN (approx 72%)
                     ========================================================= -->
                <div class="hr-dash-main-col">

                    <!-- FOUR KPI PILL CARDS (Pure Violet & Pink Palette) -->
                    <section class="hr-kpi-row">
                        <!-- Total Employees (Deep Plum Violet) -->
                        <a href="hr-employees.php" class="hr-kpi-pill stat-hr-plum">
                            <div class="stat-wave"></div>
                            <div class="stat-icon-wrapper">
                                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                                    <circle cx="9" cy="7" r="4"/>
                                    <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
                                    <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                                </svg>
                            </div>
                            <div class="kpi-pill-content">
                                <span class="kpi-pill-label">Total Employees</span>
                                <strong class="kpi-pill-val" id="kpiTotalEmp">9</strong>
                                <span class="stat-badge">Active Workforce</span>
                            </div>
                        </a>

                        <!-- Present Today (Vibrant Pink) -->
                        <a href="hr-attendance.php" class="hr-kpi-pill stat-hr-pink">
                            <div class="stat-wave"></div>
                            <div class="stat-icon-wrapper">
                                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="12" cy="12" r="10"/>
                                    <polyline points="12 6 12 12 16 14"/>
                                </svg>
                            </div>
                            <div class="kpi-pill-content">
                                <span class="kpi-pill-label">Present Today</span>
                                <strong class="kpi-pill-val" id="kpiPresent">8</strong>
                                <span class="stat-badge" id="kpiPresentBadge">89% Presence</span>
                            </div>
                        </a>

                        <!-- On Leave (Royal Violet) -->
                        <a href="hr-leave.php" class="hr-kpi-pill stat-hr-violet">
                            <div class="stat-wave"></div>
                            <div class="stat-icon-wrapper">
                                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                    <rect x="3" y="4" width="18" height="18" rx="3"/>
                                    <line x1="16" y1="2" x2="16" y2="6"/>
                                    <line x1="8" y1="2" x2="8" y2="6"/>
                                    <line x1="3" y1="10" x2="21" y2="10"/>
                                    <path d="M8 14h3v4H8z"/>
                                </svg>
                            </div>
                            <div class="kpi-pill-content">
                                <span class="kpi-pill-label">On Leave</span>
                                <strong class="kpi-pill-val" id="kpiLeave">4</strong>
                                <span class="stat-badge" id="kpiLeaveBadge">4 Pending Review</span>
                            </div>
                        </a>

                        <!-- Recruitment / Openings (Fuchsia Rose) -->
                        <a href="hr-recruitment.php" class="hr-kpi-pill stat-hr-fuchsia">
                            <div class="stat-wave"></div>
                            <div class="stat-icon-wrapper">
                                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                    <rect x="2" y="7" width="20" height="14" rx="2" ry="2"/>
                                    <path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/>
                                </svg>
                            </div>
                            <div class="kpi-pill-content">
                                <span class="kpi-pill-label">Open Positions</span>
                                <strong class="kpi-pill-val" id="kpiHiring">6</strong>
                                <span class="stat-badge">2 In Interview</span>
                            </div>
                        </a>
                    </section>

                    <!-- MIDDLE SECTION (2 CARDS: PRESENCE DONUT & PENDING APPROVALS) -->
                    <section class="hr-dash-mid-grid">

                        <!-- Card 1: Attendance & Workforce Presence -->
                        <article class="dash-card">
                            <div class="card-header-clean">
                                <div class="card-title-wrap">
                                    <span class="card-badge-icon badge-plum">⚡</span>
                                    <h2 class="card-title">Workforce Presence &amp; Attendance</h2>
                                </div>
                                <span class="prod-trend-pill" id="attendanceRatePill">89% Rate</span>
                            </div>
                            <div class="attendance-body">
                                <div class="prod-donut-wrapper">
                                    <svg class="prod-ring-svg" width="124" height="124" viewBox="0 0 100 100">
                                        <circle class="ring-bg" cx="50" cy="50" r="40" stroke-width="9" fill="none"></circle>
                                        <circle class="ring-plum" cx="50" cy="50" r="40" stroke-width="9" stroke-dasharray="251.2" stroke-dashoffset="40" stroke-linecap="round" fill="none"></circle>
                                        <circle class="ring-pink" cx="50" cy="50" r="40" stroke-width="9" stroke-dasharray="251.2" stroke-dashoffset="210" stroke-linecap="round" fill="none"></circle>
                                    </svg>
                                    <div class="prod-donut-center">
                                        <strong class="prod-pct" id="presencePct">89%</strong>
                                        <small class="prod-sub-txt">Effective</small>
                                    </div>
                                </div>
                                <div class="prod-legend-list">
                                    <div class="prod-legend-item">
                                        <span class="legend-dot-label">
                                            <span class="legend-dot dot-plum"></span>
                                            <span>On-Time Present</span>
                                        </span>
                                        <span class="legend-val" id="valPresent">8 (89%)</span>
                                    </div>
                                    <div class="prod-legend-item">
                                        <span class="legend-dot-label">
                                            <span class="legend-dot dot-pink"></span>
                                            <span>On Approved Leave</span>
                                        </span>
                                        <span class="legend-val" id="valLeave">4 (11%)</span>
                                    </div>
                                    <div class="prod-legend-item">
                                        <span class="legend-dot-label">
                                            <span class="legend-dot dot-violet"></span>
                                            <span>Work From Home</span>
                                        </span>
                                        <span class="legend-val" id="valWfh">1 (11%)</span>
                                    </div>
                                    <div class="prod-legend-item">
                                        <span class="legend-dot-label">
                                            <span class="legend-dot dot-fuchsia"></span>
                                            <span>Late / Half-day</span>
                                        </span>
                                        <span class="legend-val" id="valLate">0 (0%)</span>
                                    </div>
                                </div>
                            </div>
                        </article>

                        <!-- Card 2: Pending HR Approvals (Leave Requests) -->
                        <article class="dash-card">
                            <div class="card-header-clean">
                                <div class="card-title-wrap">
                                    <span class="card-badge-icon badge-pink">📝</span>
                                    <h2 class="card-title">Pending Leave Requests</h2>
                                </div>
                                <a href="hr-leave.php" class="card-header-link">View all requests →</a>
                            </div>
                            <div class="requests-feed" id="pendingRequestsFeed">
                                <!-- Populated dynamically by JS -->
                            </div>
                        </article>

                    </section>

                    <!-- BOTTOM SECTION (2 CARDS: RECRUITMENT PIPELINE & QUICK ACTIONS) -->
                    <section class="hr-dash-bottom-grid">

                        <!-- Card 3: Active Recruitment Pipeline -->
                        <article class="dash-card">
                            <div class="card-header-clean">
                                <div class="card-title-wrap">
                                    <span class="card-badge-icon badge-violet">💼</span>
                                    <h2 class="card-title">Active Recruitment Pipeline</h2>
                                </div>
                                <a href="hr-recruitment.php" class="card-header-link">Openings →</a>
                            </div>
                            <div class="pipeline-list" id="pipelineGrid">
                                <!-- Populated dynamically by JS -->
                            </div>
                        </article>

                        <!-- Card 4: Quick Actions (6 Action Tiles in Pure Violet & Pink) -->
                        <article class="dash-card">
                            <div class="card-header-clean">
                                <div class="card-title-wrap">
                                    <span class="card-badge-icon badge-plum">🎯</span>
                                    <h2 class="card-title">HR Quick Actions</h2>
                                </div>
                            </div>
                            <div class="hr-actions-grid-6">
                                <button type="button" class="hr-action-tile" onclick="openAddEmployeeWizard()" aria-label="Add Employee">
                                    <div class="tile-icon-box tile-plum">
                                        <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                                    </div>
                                    <span>Add Employee</span>
                                </button>
                                <button type="button" class="hr-action-tile" onclick="openQuickLeaveModal()" aria-label="Review Leave">
                                    <div class="tile-icon-box tile-pink">
                                        <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                                    </div>
                                    <span>Review Leave</span>
                                </button>
                                <button type="button" class="hr-action-tile" onclick="openQuickPayrollModal()" aria-label="Run Payroll">
                                    <div class="tile-icon-box tile-violet">
                                        <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><rect x="2" y="5" width="20" height="14" rx="2"></rect><line x1="2" y1="10" x2="22" y2="10"></line></svg>
                                    </div>
                                    <span>Run Payroll</span>
                                </button>
                                <button type="button" class="hr-action-tile" onclick="openQuickJobModal()" aria-label="Post Job">
                                    <div class="tile-icon-box tile-fuchsia">
                                        <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"></path></svg>
                                    </div>
                                    <span>Post Job</span>
                                </button>
                                <button type="button" class="hr-action-tile" onclick="openQuickAttendanceModal()" aria-label="Attendance">
                                    <div class="tile-icon-box tile-rose">
                                        <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                                    </div>
                                    <span>Attendance</span>
                                </button>
                                <button type="button" class="hr-action-tile" onclick="openQuickReportsModal()" aria-label="HR Reports">
                                    <div class="tile-icon-box tile-mulberry">
                                        <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M18 20V10"></path><path d="M12 20V4"></path><path d="M6 20v-6"></path></svg>
                                    </div>
                                    <span>HR Reports</span>
                                </button>
                            </div>
                        </article>

                    </section>

                </div>

                <!-- =========================================================
                     RIGHT RAIL (approx 28% — Calendar & Celebrations)
                     ========================================================= -->
                <aside class="hr-dash-right-rail">

                    <!-- 1. INTERACTIVE HR CALENDAR WIDGET -->
                    <article class="dash-card">
                        <div class="card-header-clean">
                            <div class="card-title-wrap">
                                <span class="card-badge-icon badge-plum">📅</span>
                                <h2 class="card-title">HR Calendar</h2>
                            </div>
                            <a href="hr-leave.php" class="card-header-link">Leaves →</a>
                        </div>
                        <div class="cal-nav-row">
                            <button type="button" class="cal-nav-btn" id="calPrevBtn" aria-label="Previous Month">‹</button>
                            <strong class="cal-month-title" id="calMonthTitle">September 2026</strong>
                            <button type="button" class="cal-nav-btn" id="calNextBtn" aria-label="Next Month">›</button>
                        </div>
                        <div class="calendar-grid-header">
                            <div>Su</div><div>Mo</div><div>Tu</div><div>We</div><div>Th</div><div>Fr</div><div>Sa</div>
                        </div>
                        <div class="calendar-days-body" id="calDaysBody">
                            <!-- Populated dynamically by JS -->
                        </div>
                    </article>

                    <!-- 2. CELEBRATIONS & WORKFORCE MILESTONES -->
                    <article class="dash-card">
                        <div class="card-header-clean">
                            <div class="card-title-wrap">
                                <span class="card-badge-icon badge-pink">🎉</span>
                                <h2 class="card-title">Celebrations &amp; Joiners</h2>
                            </div>
                            <a href="hr-employees.php" class="card-header-link">Directory →</a>
                        </div>
                        <div class="celebrations-list" id="celebrationsList">
                            <!-- Populated dynamically by JS -->
                        </div>
                    </article>

                </aside>

            </div>

        </section>
    </main>
</div>

<!-- ADD EMPLOYEE WIZARD MODAL -->
<?php include '../includes/add-employee-modal.php'; ?>

<!-- HR QUICK ACTION POPUPS / MODALS -->
<?php include '../includes/hr-quick-action-modals.php'; ?>

<script src="../assets/js/theme.js?v=<?= time() ?>"></script>
<script src="../assets/js/employee-wizard-modal.js?v=<?= time() ?>"></script>
<script src="../assets/js/hr-quick-action-modals.js?v=<?= time() ?>"></script>
<script src="../assets/js/hr-dashboard.js?v=<?= time() ?>"></script>
</body>
</html>

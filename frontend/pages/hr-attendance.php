<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HR Attendance - People &amp; Culture</title>
    <link rel="stylesheet" href="../assets/css/app-theme.css?v=<?= time() ?>">
    <link rel="stylesheet" href="../assets/css/hr-shell.css?v=<?= time() ?>">
    <link rel="stylesheet" href="../assets/css/hr-attendance.css?v=<?= time() ?>">
</head>
<body>
<div class="app">
    <?php include '../includes/hr-sidebar.php'; ?>

    <main class="main-content">
        <?php include '../includes/hr-topbar.php'; ?>

        <section class="hr-attendance-page">

            <!-- 1. CENTERED CONTROLS BAR -->
            <header class="hr-attendance-centered-header">
                <div class="hr-attendance-header-actions">
                    <div class="attendance-date-badge">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                            <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                            <line x1="16" y1="2" x2="16" y2="6"></line>
                            <line x1="8" y1="2" x2="8" y2="6"></line>
                            <line x1="3" y1="10" x2="21" y2="10"></line>
                        </svg>
                        <input type="date" id="attendanceDate" aria-label="Attendance date">
                        <button type="button" id="todayBtn" class="today-shortcut-btn" title="Jump to Today">Today</button>
                    </div>
                    <button type="button" id="exportBtn" class="hr-attendance-export-btn" title="Export CSV Report">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                            <polyline points="7 10 12 15 17 10"></polyline>
                            <line x1="12" y1="15" x2="12" y2="3"></line>
                        </svg>
                        <span>Export CSV</span>
                    </button>
                    <a href="scanner.php" class="hr-scanner-shortcut-btn" title="Launch QR Attendance Scanner">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3" y="3" width="7" height="7"></rect>
                            <rect x="14" y="3" width="7" height="7"></rect>
                            <rect x="14" y="14" width="7" height="7"></rect>
                            <rect x="3" y="14" width="7" height="7"></rect>
                        </svg>
                        <span>Scanner</span>
                    </a>
                </div>
            </header>

            <!-- 2. FIVE KPI PILL CARDS (Ultra-Premium Violet & Pink Gradients) -->
            <section class="hr-kpi-row attendance-kpi-row">
                <!-- Present Today (Vibrant Pink) -->
                <article class="hr-kpi-pill stat-hr-pink">
                    <div class="stat-wave"></div>
                    <div class="stat-icon-wrapper">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                            <circle cx="9" cy="7" r="4"/>
                            <polyline points="16 11 18 13 22 9"/>
                        </svg>
                    </div>
                    <div class="kpi-pill-content">
                        <span class="kpi-pill-label">Present Today</span>
                        <strong class="kpi-pill-val" id="presentTotal">—</strong>
                        <span class="stat-badge" id="presentMeta">Loading...</span>
                    </div>
                </article>

                <!-- Absent Today (Deep Plum) -->
                <article class="hr-kpi-pill stat-hr-plum">
                    <div class="stat-wave"></div>
                    <div class="stat-icon-wrapper">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                            <circle cx="9" cy="7" r="4"/>
                            <line x1="17" y1="11" x2="23" y2="17"/>
                            <line x1="23" y1="11" x2="17" y2="17"/>
                        </svg>
                    </div>
                    <div class="kpi-pill-content">
                        <span class="kpi-pill-label">Absent Today</span>
                        <strong class="kpi-pill-val" id="absentTotal">—</strong>
                        <span class="stat-badge" id="absentMeta">Workforce</span>
                    </div>
                </article>

                <!-- Late Arrivals (Royal Violet) -->
                <article class="hr-kpi-pill stat-hr-violet">
                    <div class="stat-wave"></div>
                    <div class="stat-icon-wrapper">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="10"/>
                            <polyline points="12 6 12 12 16 14"/>
                            <path d="M12 2v2"/>
                        </svg>
                    </div>
                    <div class="kpi-pill-content">
                        <span class="kpi-pill-label">Late Arrivals</span>
                        <strong class="kpi-pill-val" id="lateTotal">—</strong>
                        <span class="stat-badge" id="lateMeta">Check-in Punctuality</span>
                    </div>
                </article>

                <!-- On Leave (Fuchsia Rose) -->
                <article class="hr-kpi-pill stat-hr-fuchsia">
                    <div class="stat-wave"></div>
                    <div class="stat-icon-wrapper">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                            <line x1="16" y1="2" x2="16" y2="6"/>
                            <line x1="8" y1="2" x2="8" y2="6"/>
                            <line x1="3" y1="10" x2="21" y2="10"/>
                            <path d="M8 14h4v4H8z"/>
                        </svg>
                    </div>
                    <div class="kpi-pill-content">
                        <span class="kpi-pill-label">On Leave</span>
                        <strong class="kpi-pill-val" id="leaveTotal">—</strong>
                        <span class="stat-badge" id="leaveMeta">Approved Requests</span>
                    </div>
                </article>

                <!-- Total Workforce (Rose Plum) -->
                <article class="hr-kpi-pill stat-hr-rose">
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
                        <span class="kpi-pill-label">Total Staff</span>
                        <strong class="kpi-pill-val" id="statusTotal">—</strong>
                        <span class="stat-badge" id="onTimeRate">Active Roster</span>
                    </div>
                </article>
            </section>

            <!-- 3. TWO BALANCED DASHBOARD ROWS: Analytics Row & The Two Tables in the Same Line -->
            <div class="hr-attendance-layout-wrapper hr-attendance-container">

                <!-- ROW 1: ATTENDANCE TREND & WORKFORCE DISTRIBUTION -->
                <section class="hr-attendance-grid-row hr-attendance-top-row">

                    <!-- CARD 1: ATTENDANCE TREND & ANALYTICS -->
                    <article class="dash-card attendance-trend-card">
                        <div class="card-header-clean">
                            <div class="card-title-wrap">
                                <span class="card-badge-icon badge-plum">📊</span>
                                <h2 class="card-title">Attendance Trends &amp; Punctuality</h2>
                            </div>
                            <div class="trend-header-controls">
                                <select id="trendDays" class="hr-select-sm" aria-label="Select trend duration">
                                    <option value="7">Last 7 Days</option>
                                    <option value="14">Last 14 Days</option>
                                    <option value="30">Last 30 Days</option>
                                </select>
                            </div>
                        </div>

                        <!-- Modern Trend Chart Container -->
                        <div class="trend-chart-outer">
                            <div id="attendanceTrend" class="trend-chart">
                                <span>Loading trend data...</span>
                            </div>
                            <div class="chart-key">
                                <span class="key-item present-key"><i class="key-dot dot-plum"></i>Present</span>
                                <span class="key-item absent-key"><i class="key-dot dot-pink"></i>Absent</span>
                                <span class="key-item late-key"><i class="key-dot dot-violet"></i>Late Arrivals</span>
                            </div>
                        </div>
                    </article>

                    <!-- CARD 2: WORKFORCE STATUS BREAKDOWN RING & CLOCK -->
                    <article class="dash-card status-breakdown-card">
                        <div class="card-header-clean">
                            <div class="card-title-wrap">
                                <span class="card-badge-icon badge-pink">🎯</span>
                                <h2 class="card-title">Workforce Distribution</h2>
                            </div>
                            <div class="live-clock-badge-wrap">
                                <span class="live-pulse-badge">
                                    <span class="pulse-dot"></span> Live
                                </span>
                                <time class="live-time-inline" id="liveTime">00:00:00</time>
                            </div>
                        </div>
                        <div class="attendance-donut-body">
                            <div class="prod-donut-wrapper">
                                <svg class="prod-ring-svg" width="124" height="124" viewBox="0 0 100 100" id="statusRingSvg">
                                    <circle class="ring-bg" cx="50" cy="50" r="38" stroke-width="8" fill="none"></circle>
                                    <circle id="donutRingPresent" class="ring-pink" cx="50" cy="50" r="38" stroke-width="8" stroke-dasharray="238.76" stroke-dashoffset="0" stroke-linecap="round" fill="none"></circle>
                                    <circle id="donutRingLate" class="ring-violet" cx="50" cy="50" r="38" stroke-width="8" stroke-dasharray="238.76" stroke-dashoffset="238.76" stroke-linecap="round" fill="none"></circle>
                                    <circle id="donutRingLeave" class="ring-fuchsia" cx="50" cy="50" r="38" stroke-width="8" stroke-dasharray="238.76" stroke-dashoffset="238.76" stroke-linecap="round" fill="none"></circle>
                                    <circle id="donutRingAbsent" class="ring-plum" cx="50" cy="50" r="38" stroke-width="8" stroke-dasharray="238.76" stroke-dashoffset="238.76" stroke-linecap="round" fill="none"></circle>
                                </svg>
                                <div class="prod-donut-center" id="statusRing">
                                    <strong class="prod-pct" id="statusTotalDonut">—</strong>
                                    <small class="prod-sub-txt">Staff Today</small>
                                </div>
                            </div>
                            <div id="statusLegend" class="prod-legend-list">
                                <!-- Populated by JS -->
                            </div>
                        </div>
                        <div class="donut-card-footer">
                            <span class="live-date-inline" id="clockDate">Today</span>
                            <span class="prod-trend-pill" id="presenceRatePill">Presence Rate</span>
                        </div>
                    </article>

                </section>

                <!-- ROW 2: THE TWO TABLES IN THE SAME LINE (Employee Details Table + Department Presence Table) -->
                <section class="hr-attendance-grid-row hr-attendance-tables-row">

                    <!-- TABLE 1: EMPLOYEE ATTENDANCE DIRECTORY TABLE (WITH SCROLL FOR EMPLOYEE DETAILS) -->
                    <article class="dash-card employee-directory-card">
                        <!-- Tabs Row -->
                        <div class="attendance-tabs-row">
                            <div class="attendance-filter-tabs" id="statusTabs">
                                <button type="button" class="tab-btn active" data-status="">All Records</button>
                                <button type="button" class="tab-btn" data-status="PRESENT">Present</button>
                                <button type="button" class="tab-btn" data-status="LATE">Late</button>
                                <button type="button" class="tab-btn" data-status="ON_LEAVE">On Leave</button>
                                <button type="button" class="tab-btn" data-status="ABSENT">Absent</button>
                            </div>
                            <div class="attendance-tab-meta">
                                <span id="attendanceCount" class="attendance-count-badge">0 employees</span>
                            </div>
                        </div>

                        <!-- Filter Toolbar -->
                        <div class="employee-toolbar">
                            <div class="employee-search">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="11" cy="11" r="8"></circle>
                                    <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                                </svg>
                                <input id="attendanceSearch" type="search" placeholder="Search by name, employee code...">
                            </div>
                            <select id="attendanceDept" aria-label="Filter by department">
                                <option value="">All Departments</option>
                            </select>
                            <select id="attendanceStatusSelect" aria-label="Filter by status">
                                <option value="">All Statuses</option>
                                <option value="PRESENT">Present</option>
                                <option value="LATE">Late</option>
                                <option value="ON_LEAVE">On Leave</option>
                                <option value="ABSENT">Absent</option>
                            </select>
                        </div>

                        <!-- Data Table with Vertical Scroll -->
                        <div class="attendance-table-wrap">
                            <table class="attendance-table">
                                <thead>
                                    <tr>
                                        <th style="width: 50px;">#</th>
                                        <th>Employee</th>
                                        <th>Department</th>
                                        <th>Check In</th>
                                        <th>Check Out</th>
                                        <th>Total Hours</th>
                                        <th>Status</th>
                                        <th style="text-align: right;">Action</th>
                                    </tr>
                                </thead>
                                <tbody id="attendanceEmployees">
                                    <tr><td colspan="8" class="loading-td">Loading attendance data...</td></tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- Table Footer / Pagination -->
                        <div class="employee-footer">
                            <span id="attendancePageInfo" class="attendance-page-info">Showing records...</span>
                            <div id="attendancePagination" class="employee-pagination"></div>
                            <label class="per-page-label">Rows per page:
                                <select id="attendancePerPage" class="per-page-select">
                                    <option value="10" selected>10</option>
                                    <option value="25">25</option>
                                    <option value="50">50</option>
                                </select>
                            </label>
                        </div>
                    </article>

                    <!-- TABLE 2: ATTENDANCE BY DEPARTMENT (IN THE SAME LINE AS EMPLOYEE TABLE) -->
                    <article class="dash-card dept-attendance-card">
                        <div class="card-header-clean">
                            <div class="card-title-wrap">
                                <span class="card-badge-icon badge-violet">🏢</span>
                                <h2 class="card-title">Department Presence</h2>
                            </div>
                            <span class="dept-count-badge">Active Divisions</span>
                        </div>
                        <div id="departmentAttendance" class="department-progress-list">
                            <span>Loading department data...</span>
                        </div>
                        <div class="dept-card-footer">
                            <div class="dept-footer-insight">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="12" cy="12" r="10"></circle>
                                    <line x1="12" y1="16" x2="12" y2="12"></line>
                                    <line x1="12" y1="8" x2="12.01" y2="8"></line>
                                </svg>
                                <span>Punctuality Benchmark: <strong>90% presence target</strong></span>
                            </div>
                        </div>
                    </article>

                </section>

            </div>
        </section>
    </main>
</div>

<script src="../assets/js/hr-attendance.js?v=<?= time() ?>"></script>
</body>
</html>

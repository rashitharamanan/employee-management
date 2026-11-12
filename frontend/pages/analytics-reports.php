<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Analytics & Reports - Admin Portal. Comprehensive organizational intelligence, workforce distribution, and report generation.">
    <title>Analytics &amp; Reports - Admin Portal</title>
    <link rel="stylesheet" href="../assets/css/app-theme.css?v=<?= time() ?>">
    <link rel="stylesheet" href="../assets/css/sidebar.css?v=<?= time() ?>">
    <link rel="stylesheet" href="../assets/css/topbar.css?v=<?= time() ?>">
    <link rel="stylesheet" href="../assets/css/analytics-reports.css?v=<?= time() ?>">
</head>
<body>
<div class="app">
    <?php include '../includes/sidebar.php'; ?>

    <main class="main-content">
        <?php include '../includes/topbar.php'; ?>

        <div class="ar-container">

            <!-- =========================================================
                 TOP ROW: KPI Cards (LEFT, matching user reference image) + Generate Report (RIGHT)
            ========================================================= -->
            <section class="ar-top-section">
                <!-- LEFT: 4 KPI Cards in a 2x2 Grid (Styled like user photo in LUNA palette) -->
                <div class="ar-kpi-grid">
                    <!-- KPI 1: Total Employees (LUNA Ocean Blue) -->
                    <article class="ar-kpi-card card-luna-blue">
                        <div class="ar-kpi-wave"></div>
                        <div class="ar-kpi-left-content">
                            <div class="ar-kpi-text-block">
                                <h3 class="ar-kpi-card-title">Total Emps</h3>
                                <p class="ar-kpi-card-sub" id="kpiTotalEmpsSub">Registered staff</p>
                            </div>
                            <div class="ar-kpi-ring-box">
                                <svg class="ar-kpi-ring-svg" width="32" height="32" viewBox="0 0 34 34">
                                    <circle cx="17" cy="17" r="13.5" fill="transparent" stroke="rgba(255,255,255,0.22)" stroke-width="3.8"/>
                                    <circle id="kpiRingCircle1" cx="17" cy="17" r="13.5" fill="transparent" stroke="#ffffff" stroke-width="3.8" stroke-dasharray="84.82" stroke-dashoffset="0" stroke-linecap="round" transform="rotate(-90 17 17)"/>
                                </svg>
                                <span class="ar-kpi-ring-label" id="kpiRingVal1">100%</span>
                            </div>
                            <!-- Hidden span for JS count binding -->
                            <span id="kpiTotalEmps" class="ar-hidden-stat">12</span>
                        </div>
                        <div class="ar-kpi-illus-wrap">
                            <svg class="ar-kpi-monument" viewBox="0 0 70 85" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M35 2 L38 22 L46 58 L55 82 H15 L24 58 L32 22 Z" fill="rgba(255,255,255,0.22)" stroke="rgba(255,255,255,0.75)" stroke-width="1.8"/>
                                <path d="M26 82 C26 70 44 70 44 82" stroke="rgba(255,255,255,0.9)" stroke-width="2" fill="rgba(255,255,255,0.15)"/>
                                <line x1="28" y1="46" x2="42" y2="46" stroke="rgba(255,255,255,0.85)" stroke-width="2"/>
                                <line x1="31" y1="30" x2="39" y2="30" stroke="rgba(255,255,255,0.85)" stroke-width="1.8"/>
                                <line x1="35" y1="2" x2="35" y2="82" stroke="rgba(255,255,255,0.4)" stroke-width="1" stroke-dasharray="2 2"/>
                                <circle cx="35" cy="14" r="2.5" fill="#ffffff"/>
                                <path d="M12 82 H58" stroke="#ffffff" stroke-width="2.5" stroke-linecap="round"/>
                            </svg>
                        </div>
                    </article>

                    <!-- KPI 2: Active Employees (LUNA Teal / Cyan) -->
                    <article class="ar-kpi-card card-luna-teal">
                        <div class="ar-kpi-wave"></div>
                        <div class="ar-kpi-left-content">
                            <div class="ar-kpi-text-block">
                                <h3 class="ar-kpi-card-title">Active Em</h3>
                                <p class="ar-kpi-card-sub" id="kpiActivePercent">100% active</p>
                            </div>
                            <div class="ar-kpi-ring-box">
                                <svg class="ar-kpi-ring-svg" width="32" height="32" viewBox="0 0 34 34">
                                    <circle cx="17" cy="17" r="13.5" fill="transparent" stroke="rgba(255,255,255,0.22)" stroke-width="3.8"/>
                                    <circle id="kpiRingCircle2" cx="17" cy="17" r="13.5" fill="transparent" stroke="#ffffff" stroke-width="3.8" stroke-dasharray="84.82" stroke-dashoffset="0" stroke-linecap="round" transform="rotate(-90 17 17)"/>
                                </svg>
                                <span class="ar-kpi-ring-label" id="kpiRingVal2">100%</span>
                            </div>
                            <!-- Hidden span for JS count binding -->
                            <span id="kpiActiveEmps" class="ar-hidden-stat">12</span>
                        </div>
                        <div class="ar-kpi-illus-wrap">
                            <svg class="ar-kpi-monument" viewBox="0 0 70 85" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M35 10 L30 18 H40 Z" fill="rgba(255,255,255,0.75)"/>
                                <line x1="35" y1="4" x2="35" y2="10" stroke="#ffffff" stroke-width="2"/>
                                <line x1="25" y1="4" x2="45" y2="4" stroke="#ffffff" stroke-width="2"/>
                                <rect x="18" y="18" width="34" height="52" rx="6" fill="rgba(255,255,255,0.2)" stroke="rgba(255,255,255,0.85)" stroke-width="2"/>
                                <rect x="22" y="24" width="26" height="18" rx="3" fill="rgba(255,255,255,0.3)" stroke="rgba(255,255,255,0.9)" stroke-width="1.5"/>
                                <rect x="22" y="47" width="11" height="11" rx="2" fill="rgba(255,255,255,0.25)"/>
                                <rect x="37" y="47" width="11" height="11" rx="2" fill="rgba(255,255,255,0.25)"/>
                                <circle cx="27" cy="63" r="2" fill="#ffffff"/>
                                <circle cx="43" cy="63" r="2" fill="#ffffff"/>
                                <rect x="15" y="70" width="40" height="4" rx="2" fill="rgba(255,255,255,0.8)"/>
                                <circle cx="24" cy="76" r="3" fill="#ffffff"/>
                                <circle cx="46" cy="76" r="3" fill="#ffffff"/>
                            </svg>
                        </div>
                    </article>

                    <!-- KPI 3: Attendance (Emerald Teal) -->
                    <article class="ar-kpi-card card-luna-emerald">
                        <div class="ar-kpi-wave"></div>
                        <div class="ar-kpi-left-content">
                            <div class="ar-kpi-text-block">
                                <h3 class="ar-kpi-card-title">Attend.</h3>
                                <p class="ar-kpi-card-sub" id="kpiPresentCount">Present today</p>
                            </div>
                            <div class="ar-kpi-ring-box">
                                <svg class="ar-kpi-ring-svg" width="32" height="32" viewBox="0 0 34 34">
                                    <circle cx="17" cy="17" r="13.5" fill="transparent" stroke="rgba(255,255,255,0.22)" stroke-width="3.8"/>
                                    <circle id="kpiRingCircle3" cx="17" cy="17" r="13.5" fill="transparent" stroke="#ffffff" stroke-width="3.8" stroke-dasharray="84.82" stroke-dashoffset="14" stroke-linecap="round" transform="rotate(-90 17 17)"/>
                                </svg>
                                <span class="ar-kpi-ring-label" id="kpiRingVal3">83%</span>
                            </div>
                            <!-- Hidden span for JS count binding -->
                            <span id="kpiAttendance" class="ar-hidden-stat">83.3%</span>
                        </div>
                        <div class="ar-kpi-illus-wrap">
                            <svg class="ar-kpi-monument" viewBox="0 0 70 85" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <g transform="rotate(7 35 45)">
                                    <rect x="23" y="16" width="24" height="60" rx="3" fill="rgba(255,255,255,0.2)" stroke="rgba(255,255,255,0.85)" stroke-width="2"/>
                                    <line x1="23" y1="28" x2="47" y2="28" stroke="#ffffff" stroke-width="1.8"/>
                                    <line x1="23" y1="40" x2="47" y2="40" stroke="#ffffff" stroke-width="1.8"/>
                                    <line x1="23" y1="52" x2="47" y2="52" stroke="#ffffff" stroke-width="1.8"/>
                                    <line x1="23" y1="64" x2="47" y2="64" stroke="#ffffff" stroke-width="1.8"/>
                                    <path d="M29 16 C29 10 41 10 41 16" fill="rgba(255,255,255,0.5)" stroke="#ffffff" stroke-width="1.8"/>
                                    <circle cx="35" cy="8" r="2" fill="#ffffff"/>
                                </g>
                                <line x1="14" y1="80" x2="56" y2="80" stroke="#ffffff" stroke-width="2.5" stroke-linecap="round"/>
                            </svg>
                        </div>
                    </article>

                    <!-- KPI 4: Total Reports (Deep Sapphire Navy) -->
                    <article class="ar-kpi-card card-luna-navy">
                        <div class="ar-kpi-wave"></div>
                        <div class="ar-kpi-left-content">
                            <div class="ar-kpi-text-block">
                                <h3 class="ar-kpi-card-title">Total Reports</h3>
                                <p class="ar-kpi-card-sub">Audit logs</p>
                            </div>
                            <div class="ar-kpi-ring-box">
                                <svg class="ar-kpi-ring-svg" width="32" height="32" viewBox="0 0 34 34">
                                    <circle cx="17" cy="17" r="13.5" fill="transparent" stroke="rgba(255,255,255,0.22)" stroke-width="3.8"/>
                                    <circle id="kpiRingCircle4" cx="17" cy="17" r="13.5" fill="transparent" stroke="#ffffff" stroke-width="3.8" stroke-dasharray="84.82" stroke-dashoffset="0" stroke-linecap="round" transform="rotate(-90 17 17)"/>
                                </svg>
                                <span class="ar-kpi-ring-label" id="kpiRingVal4">100%</span>
                            </div>
                            <!-- Hidden span for JS count binding -->
                            <span id="kpiTotalReports" class="ar-hidden-stat">—</span>
                        </div>
                        <div class="ar-kpi-illus-wrap">
                            <svg class="ar-kpi-monument" viewBox="0 0 70 85" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <polygon points="35,16 16,28 54,28" fill="rgba(255,255,255,0.25)" stroke="#ffffff" stroke-width="2"/>
                                <circle cx="35" cy="11" r="2.5" fill="#ffffff"/>
                                <line x1="35" y1="13" x2="35" y2="16" stroke="#ffffff" stroke-width="2"/>
                                <rect x="18" y="28" width="5" height="42" fill="rgba(255,255,255,0.3)" stroke="#ffffff" stroke-width="1.2"/>
                                <rect x="26" y="28" width="5" height="42" fill="rgba(255,255,255,0.3)" stroke="#ffffff" stroke-width="1.2"/>
                                <rect x="34" y="28" width="5" height="42" fill="rgba(255,255,255,0.3)" stroke="#ffffff" stroke-width="1.2"/>
                                <rect x="41" y="28" width="5" height="42" fill="rgba(255,255,255,0.3)" stroke="#ffffff" stroke-width="1.2"/>
                                <rect x="49" y="28" width="5" height="42" fill="rgba(255,255,255,0.3)" stroke="#ffffff" stroke-width="1.2"/>
                                <rect x="14" y="70" width="44" height="6" rx="2" fill="rgba(255,255,255,0.4)" stroke="#ffffff" stroke-width="1.8"/>
                                <line x1="10" y1="78" x2="62" y2="78" stroke="#ffffff" stroke-width="2.5" stroke-linecap="round"/>
                            </svg>
                        </div>
                    </article>
                </div>

                <!-- RIGHT: Generate Report Card (Left: Module & Dates small; Right: Report Title & Button) -->
                <article class="ar-card ar-card-generator">
                    <div class="ar-card-header compact">
                        <div class="ar-card-title-wrap">
                            <div class="ar-card-icon blue compact">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/>
                                </svg>
                            </div>
                            <div>
                                <h2 class="ar-card-title compact">Generate Report</h2>
                                <span class="ar-card-desc compact">Configure audit parameters &amp; compile export</span>
                            </div>
                        </div>
                    </div>

                    <form id="generateReportForm" class="ar-gen-form-split">
                        <!-- LEFT: Module / Category, From Date, To Date, Format (Small) -->
                        <div class="ar-gen-left">
                            <div class="ar-form-group small">
                                <label for="repCategory">Module / Category</label>
                                <select id="repCategory" class="ar-select small" required>
                                    <option value="Attendance" selected>Attendance Module</option>
                                    <option value="Employees">Employees &amp; Staff</option>
                                    <option value="Department">Department Summary</option>
                                    <option value="Performance">Performance Reviews</option>
                                    <option value="System">System &amp; Security</option>
                                </select>
                            </div>
                            <div class="ar-dates-inline">
                                <div class="ar-form-group small">
                                    <label for="repFromDate">From Date</label>
                                    <input type="date" id="repFromDate" class="ar-input small" required>
                                </div>
                                <div class="ar-form-group small">
                                    <label for="repToDate">To Date</label>
                                    <input type="date" id="repToDate" class="ar-input small" required>
                                </div>
                                <div class="ar-form-group small">
                                    <label for="repFormat">Format</label>
                                    <select id="repFormat" class="ar-select small" required>
                                        <option value="PDF" selected>PDF</option>
                                        <option value="EXCEL">Excel</option>
                                        <option value="CSV">CSV</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- RIGHT: Report Title & Generate Report Button -->
                        <div class="ar-gen-right">
                            <div class="ar-form-group">
                                <label for="repName">Report Title</label>
                                <input type="text" id="repName" class="ar-input" placeholder="e.g. Monthly Attendance &amp; Compliance Audit">
                            </div>
                            <div class="ar-gen-action-wrap">
                                <button type="submit" class="ar-btn-generate" id="btnGenerateReport">
                                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                        <polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/>
                                    </svg>
                                    <span>Generate Report</span>
                                </button>
                            </div>
                        </div>
                        <p class="ar-form-error" id="reportErrorMsg"></p>
                    </form>
                </article>
            </section>

            <!-- =========================================================
                 BOTTOM ROW: Left (Department & Attendance) + Right (Expanded Projects Table)
            ========================================================= -->
            <section class="ar-bottom-section">
                <!-- LEFT COLUMN: Department Chart (down from KPI cards) + Attendance Donut -->
                <div class="ar-left-col">
                    <!-- Employees by Department (Reduced size bar chart) -->
                    <article class="ar-card ar-card-barchart">
                        <div class="ar-card-header compact">
                            <div class="ar-card-title-wrap">
                                <div class="ar-card-icon blue compact">
                                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                        <line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/>
                                    </svg>
                                </div>
                                <div>
                                    <h2 class="ar-card-title compact">Employees by Department</h2>
                                    <span class="ar-card-desc compact">Headcount across 5 core departments</span>
                                </div>
                            </div>
                            <span class="ar-badge-teal compact" id="totalDeptBadge">5 Depts</span>
                        </div>

                        <div class="ar-barchart-wrap compact" id="deptBarChart">
                            <div class="ar-chart-placeholder compact">
                                <div class="ar-spinner"></div>
                                <span>Loading department metrics...</span>
                            </div>
                        </div>
                    </article>

                    <!-- Attendance Donut Chart (Compact) -->
                    <article class="ar-card ar-card-attendance compact">
                        <div class="ar-card-header compact">
                            <div class="ar-card-title-wrap">
                                <div class="ar-card-icon teal compact">
                                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                        <circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>
                                    </svg>
                                </div>
                                <div>
                                    <h2 class="ar-card-title compact">Attendance Overview</h2>
                                    <span class="ar-card-desc compact">Daily punch-in distribution</span>
                                </div>
                            </div>
                        </div>

                        <div class="ar-donut-container compact">
                            <div class="ar-donut-chart-box compact">
                                <svg width="120" height="120" viewBox="0 0 150 150" class="ar-donut-svg">
                                    <circle cx="75" cy="75" r="56" fill="transparent" stroke="#e2e8f0" stroke-width="19"/>
                                    <circle id="donutPresentCircle" cx="75" cy="75" r="56" fill="transparent" stroke="#26658C" stroke-width="19" stroke-dasharray="351.86" stroke-dashoffset="70" stroke-linecap="round" transform="rotate(-90 75 75)"/>
                                    <circle id="donutAbsentCircle" cx="75" cy="75" r="56" fill="transparent" stroke="#ea580c" stroke-width="19" stroke-dasharray="351.86" stroke-dashoffset="310" stroke-linecap="round" transform="rotate(30 75 75)"/>
                                    <circle id="donutLateCircle" cx="75" cy="75" r="56" fill="transparent" stroke="#54ACBF" stroke-width="19" stroke-dasharray="351.86" stroke-dashoffset="340" stroke-linecap="round" transform="rotate(75 75 75)"/>
                                </svg>
                                <div class="ar-donut-center-text compact">
                                    <strong id="donutCenterRate">0%</strong>
                                    <small>Rate</small>
                                </div>
                            </div>
                            <div class="ar-donut-legend compact">
                                <div class="ar-legend-item compact">
                                    <span class="ar-legend-dot blue"></span>
                                    <div class="ar-legend-info">
                                        <span class="ar-legend-name">Present</span>
                                        <strong id="legendPresentVal">0</strong>
                                    </div>
                                </div>
                                <div class="ar-legend-item compact">
                                    <span class="ar-legend-dot orange"></span>
                                    <div class="ar-legend-info">
                                        <span class="ar-legend-name">Absent</span>
                                        <strong id="legendAbsentVal">0</strong>
                                    </div>
                                </div>
                                <div class="ar-legend-item compact">
                                    <span class="ar-legend-dot teal"></span>
                                    <div class="ar-legend-info">
                                        <span class="ar-legend-name">Late</span>
                                        <strong id="legendLateVal">0</strong>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </article>
                </div>

                <!-- RIGHT COLUMN: Projects Table (Expanded with rich detailed columns) -->
                <article class="ar-card ar-card-projects-table">
                    <div class="ar-card-header">
                        <div class="ar-card-title-wrap">
                            <div class="ar-card-icon navy">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                    <polygon points="12 2 2 7 12 12 22 7 12 2"/><polyline points="2 17 12 22 22 17"/><polyline points="2 12 12 17 22 12"/>
                                </svg>
                            </div>
                            <div>
                                <h2 class="ar-card-title">Ongoing Projects Directory</h2>
                                <span class="ar-card-desc">Active organizational milestones, timelines, department assignments &amp; progress</span>
                            </div>
                        </div>
                        <span class="ar-badge-subtle" id="projectCountBadge">6 Active Projects</span>
                    </div>

                    <div class="ar-projects-table-responsive">
                        <table class="ar-projects-table">
                            <thead>
                                <tr>
                                    <th>Project Name &amp; Code</th>
                                    <th>Department</th>
                                    <th>Project Lead &amp; Team</th>
                                    <th>Due Date</th>
                                    <th>Priority</th>
                                    <th>Progress</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Project 1 -->
                                <tr>
                                    <td>
                                        <div class="ar-prj-cell-title">
                                            <div class="ar-prj-avatar p-navy">HR</div>
                                            <div>
                                                <strong>HR QR Attendance Core</strong>
                                                <span class="ar-prj-code">#PRJ-101</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="ar-dept-pill it">IT &amp; Systems</span>
                                    </td>
                                    <td>
                                        <div class="ar-lead-cell">
                                            <span class="ar-lead-name">Alex Vance</span>
                                            <span class="ar-team-size">&bull; 8 Members</span>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="ar-date-cell">
                                            <span>Nov 15, 2026</span>
                                            <small class="ar-due-pill alert">57d left</small>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="ar-priority-pill high">High</span>
                                    </td>
                                    <td>
                                        <div class="ar-tbl-progress">
                                            <div class="ar-tbl-progress-bar">
                                                <div class="ar-tbl-progress-fill" style="width: 85%;"></div>
                                            </div>
                                            <span class="ar-tbl-progress-val">85%</span>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="ar-status-pill-green">In Progress</span>
                                    </td>
                                </tr>

                                <!-- Project 2 -->
                                <tr>
                                    <td>
                                        <div class="ar-prj-cell-title">
                                            <div class="ar-prj-avatar p-teal">FN</div>
                                            <div>
                                                <strong>Payroll Automation &amp; Tax Engine</strong>
                                                <span class="ar-prj-code">#PRJ-102</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="ar-dept-pill finance">Finance</span>
                                    </td>
                                    <td>
                                        <div class="ar-lead-cell">
                                            <span class="ar-lead-name">Elena Rostova</span>
                                            <span class="ar-team-size">&bull; 5 Analysts</span>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="ar-date-cell">
                                            <span>Dec 01, 2026</span>
                                            <small class="ar-due-pill normal">73d left</small>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="ar-priority-pill critical">Critical</span>
                                    </td>
                                    <td>
                                        <div class="ar-tbl-progress">
                                            <div class="ar-tbl-progress-bar">
                                                <div class="ar-tbl-progress-fill teal" style="width: 65%;"></div>
                                            </div>
                                            <span class="ar-tbl-progress-val">65%</span>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="ar-status-pill-teal">Under Review</span>
                                    </td>
                                </tr>

                                <!-- Project 3 -->
                                <tr>
                                    <td>
                                        <div class="ar-prj-cell-title">
                                            <div class="ar-prj-avatar p-blue">MB</div>
                                            <div>
                                                <strong>Employee Mobile Portal v2</strong>
                                                <span class="ar-prj-code">#PRJ-103</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="ar-dept-pill it">IT &amp; Systems</span>
                                    </td>
                                    <td>
                                        <div class="ar-lead-cell">
                                            <span class="ar-lead-name">David Kim</span>
                                            <span class="ar-team-size">&bull; 6 Devs</span>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="ar-date-cell">
                                            <span>Dec 20, 2026</span>
                                            <small class="ar-due-pill normal">92d left</small>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="ar-priority-pill medium">Medium</span>
                                    </td>
                                    <td>
                                        <div class="ar-tbl-progress">
                                            <div class="ar-tbl-progress-bar">
                                                <div class="ar-tbl-progress-fill blue" style="width: 42%;"></div>
                                            </div>
                                            <span class="ar-tbl-progress-val">42%</span>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="ar-status-pill-blue">Sprint 3</span>
                                    </td>
                                </tr>

                                <!-- Project 4 -->
                                <tr>
                                    <td>
                                        <div class="ar-prj-cell-title">
                                            <div class="ar-prj-avatar p-cyan">HR</div>
                                            <div>
                                                <strong>Global Talent Acquisition Hub</strong>
                                                <span class="ar-prj-code">#PRJ-104</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="ar-dept-pill hr">HR &amp; People</span>
                                    </td>
                                    <td>
                                        <div class="ar-lead-cell">
                                            <span class="ar-lead-name">Marcus Sterling</span>
                                            <span class="ar-team-size">&bull; 4 Recruiters</span>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="ar-date-cell">
                                            <span>Oct 30, 2026</span>
                                            <small class="ar-due-pill urgent">41d left</small>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="ar-priority-pill high">High</span>
                                    </td>
                                    <td>
                                        <div class="ar-tbl-progress">
                                            <div class="ar-tbl-progress-bar">
                                                <div class="ar-tbl-progress-fill emerald" style="width: 90%;"></div>
                                            </div>
                                            <span class="ar-tbl-progress-val">90%</span>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="ar-status-pill-green">Testing</span>
                                    </td>
                                </tr>

                                <!-- Project 5 -->
                                <tr>
                                    <td>
                                        <div class="ar-prj-cell-title">
                                            <div class="ar-prj-avatar p-indigo">SL</div>
                                            <div>
                                                <strong>Enterprise CRM &amp; Sales Pipeline</strong>
                                                <span class="ar-prj-code">#PRJ-105</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="ar-dept-pill sales">Sales</span>
                                    </td>
                                    <td>
                                        <div class="ar-lead-cell">
                                            <span class="ar-lead-name">Jordan Bell</span>
                                            <span class="ar-team-size">&bull; 7 Execs</span>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="ar-date-cell">
                                            <span>Nov 28, 2026</span>
                                            <small class="ar-due-pill normal">70d left</small>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="ar-priority-pill medium">Medium</span>
                                    </td>
                                    <td>
                                        <div class="ar-tbl-progress">
                                            <div class="ar-tbl-progress-bar">
                                                <div class="ar-tbl-progress-fill teal" style="width: 74%;"></div>
                                            </div>
                                            <span class="ar-tbl-progress-val">74%</span>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="ar-status-pill-teal">On Track</span>
                                    </td>
                                </tr>

                                <!-- Project 6 -->
                                <tr>
                                    <td>
                                        <div class="ar-prj-cell-title">
                                            <div class="ar-prj-avatar p-amber">MK</div>
                                            <div>
                                                <strong>Annual Brand &amp; Marketing Campaign</strong>
                                                <span class="ar-prj-code">#PRJ-106</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="ar-dept-pill marketing">Marketing</span>
                                    </td>
                                    <td>
                                        <div class="ar-lead-cell">
                                            <span class="ar-lead-name">Sophia Chen</span>
                                            <span class="ar-team-size">&bull; 4 Specialists</span>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="ar-date-cell">
                                            <span>Jan 15, 2027</span>
                                            <small class="ar-due-pill normal">118d left</small>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="ar-priority-pill normal">Normal</span>
                                    </td>
                                    <td>
                                        <div class="ar-tbl-progress">
                                            <div class="ar-tbl-progress-bar">
                                                <div class="ar-tbl-progress-fill amber" style="width: 28%;"></div>
                                            </div>
                                            <span class="ar-tbl-progress-val">28%</span>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="ar-status-pill-amber">Planning</span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </article>
            </section>
        </div>
    </main>
</div>

<!-- TOAST -->
<div class="ar-toast" id="toast"></div>

<!-- SCRIPTS -->
<script src="../assets/js/theme.js?v=1"></script>
<script src="../assets/js/analytics-reports.js?v=<?= time() ?>"></script>
</body>
</html>

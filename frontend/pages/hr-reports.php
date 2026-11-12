<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="HR Reports — Generate insights, analytics, and workforce reports.">
    <title>HR Reports - HR Portal</title>
    <link rel="stylesheet" href="../assets/css/app-theme.css?v=<?= time() ?>">
    <link rel="stylesheet" href="../assets/css/hr-shell.css?v=<?= time() ?>">
    <link rel="stylesheet" href="../assets/css/hr-employees.css?v=<?= time() ?>">
    <link rel="stylesheet" href="../assets/css/hr-reports.css?v=<?= time() ?>">
</head>
<body>
<div class="app">
    <?php include '../includes/hr-sidebar.php'; ?>

    <main class="main-content">
        <?php include '../includes/hr-topbar.php'; ?>

        <section class="rp-page">

            <!-- COMPACT TOP BAR & ACTIONS -->
            <div class="rp-top-bar">
                <div class="rp-top-badge">
                    <span class="rp-pulse-dot"></span>
                    <span>Workforce Analytics &amp; Reports</span>
                </div>
                <div class="rp-heading-actions">
                    <button type="button" id="generateBtn" class="rp-primary-btn">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                        <span>Generate Report</span>
                    </button>
                    <button type="button" id="loadBtn" class="rp-secondary-btn">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><polyline points="23 4 23 10 17 10"/><polyline points="1 20 1 14 7 14"/><path d="M3.51 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15"/></svg>
                        <span>Refresh</span>
                    </button>
                </div>
            </div>

            <div class="rp-error" id="rpError" style="display:none"></div>

            <!-- Filters -->
            <div class="rp-filters">
                <div class="rp-field">
                    <label for="fromDate">From Date</label>
                    <input type="date" id="fromDate" class="rp-input">
                </div>
                <div class="rp-field">
                    <label for="toDate">To Date</label>
                    <input type="date" id="toDate" class="rp-input">
                </div>
                <div class="rp-field">
                    <label for="yearFilter">Year</label>
                    <select id="yearFilter" class="rp-input">
                        <option value="2026" selected>2026</option>
                        <option value="2025">2025</option>
                        <option value="2024">2024</option>
                    </select>
                </div>
                <div class="rp-field">
                    <label>&nbsp;</label>
                    <button id="applyFilters" class="rp-primary-btn" onclick="document.getElementById('loadBtn').click()">Apply Filters</button>
                </div>
            </div>

            <!-- KPI Cards -->
            <section class="employee-kpis rp-kpis" id="kpiCards">
                <div class="employee-kpi kpi-theme-plum"><div class="kpi-wave"></div><div class="kpi-icon-box"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg></div><div class="kpi-details"><span class="kpi-label">Total Employees</span><strong class="kpi-value">—</strong><span class="kpi-pill-badge">Loading...</span></div></div>
                <div class="employee-kpi kpi-theme-violet"><div class="kpi-wave"></div><div class="kpi-icon-box"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg></div><div class="kpi-details"><span class="kpi-label">Attendance Rate</span><strong class="kpi-value">—</strong><span class="kpi-pill-badge">Loading...</span></div></div>
                <div class="employee-kpi kpi-theme-pink"><div class="kpi-wave"></div><div class="kpi-icon-box"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/></svg></div><div class="kpi-details"><span class="kpi-label">Pending Leaves</span><strong class="kpi-value">—</strong><span class="kpi-pill-badge">Loading...</span></div></div>
                <div class="employee-kpi kpi-theme-rose"><div class="kpi-wave"></div><div class="kpi-icon-box"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg></div><div class="kpi-details"><span class="kpi-label">Avg Performance</span><strong class="kpi-value">—</strong><span class="kpi-pill-badge">Loading...</span></div></div>
                <div class="employee-kpi kpi-theme-fuchsia"><div class="kpi-wave"></div><div class="kpi-icon-box"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M22 10v6M2 10l10-5 10 5-10 5z"/></svg></div><div class="kpi-details"><span class="kpi-label">Training Complete</span><strong class="kpi-value">—</strong><span class="kpi-pill-badge">Loading...</span></div></div>
                <div class="employee-kpi kpi-theme-magenta"><div class="kpi-wave"></div><div class="kpi-icon-box"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg></div><div class="kpi-details"><span class="kpi-label">Payroll Cost YTD</span><strong class="kpi-value">—</strong><span class="kpi-pill-badge">Loading...</span></div></div>
                <div class="employee-kpi kpi-theme-rose"><div class="kpi-wave"></div><div class="kpi-icon-box"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><circle cx="12" cy="12" r="10"/><circle cx="12" cy="6" r="2"/></svg></div><div class="kpi-details"><span class="kpi-label">Open Positions</span><strong class="kpi-value">—</strong><span class="kpi-pill-badge">Loading...</span></div></div>
                <div class="employee-kpi kpi-theme-plum"><div class="kpi-wave"></div><div class="kpi-icon-box"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/></svg></div><div class="kpi-details"><span class="kpi-label">Total Reports</span><strong class="kpi-value">—</strong><span class="kpi-pill-badge">Loading...</span></div></div>
            </section>

            <!--__HTML1__-->
            <!-- Charts Grid -->
            <div class="rp-charts">

                <!-- Headcount Trend -->
                <article class="rp-panel">
                    <div class="rp-panel-header">
                        <div>
                            <h2 class="rp-panel-title">Headcount Trend</h2>
                            <p class="rp-panel-sub">Monthly hires, exits, and total headcount</p>
                        </div>
                    </div>
                    <div class="rp-canvas-wrap" id="headcountTrendChart">
                        <div class="rp-chart-empty">Loading chart...</div>
                    </div>
                </article>

                <!-- Department Headcount -->
                <article class="rp-panel">
                    <h2 class="rp-panel-title">Department Headcount</h2>
                    <p class="rp-panel-sub">Employee distribution across departments</p>
                    <div id="departmentHeadcount">
                        <div class="rp-chart-empty">Loading chart...</div>
                    </div>
                </article>

                <!-- Attendance Overview -->
                <article class="rp-panel">
                    <h2 class="rp-panel-title">Attendance Overview</h2>
                    <p class="rp-panel-sub">Overall workforce attendance breakdown</p>
                    <div class="rp-donut-row">
                        <div class="rp-donut" id="attendanceDonut">
                            <div class="rp-donut-center"><span><strong>0%</strong><small>Rate</small></span></div>
                        </div>
                        <div class="rp-legend" id="attendanceLegend"></div>
                    </div>
                </article>

                <!-- Leave Analysis -->
                <article class="rp-panel">
                    <h2 class="rp-panel-title">Leave Analysis</h2>
                    <p class="rp-panel-sub">Leave requests by type</p>
                    <div id="leaveAnalysis">
                        <div class="rp-chart-empty">Loading chart...</div>
                    </div>
                </article>

                <!-- Recruitment Funnel -->
                <article class="rp-panel">
                    <h2 class="rp-panel-title">Recruitment Funnel</h2>
                    <p class="rp-panel-sub">Candidate pipeline by stage</p>
                    <div id="recruitmentFunnel">
                        <div class="rp-chart-empty">Loading chart...</div>
                    </div>
                </article>

                <!-- Training & Performance -->
                <article class="rp-panel">
                    <h2 class="rp-panel-title">Training & Performance</h2>
                    <p class="rp-panel-sub">Top performers and training scores</p>
                    <div id="trainingPerformance">
                        <div class="rp-chart-empty">Loading chart...</div>
                    </div>
                </article>

            </div>

            <!--__HTML2__-->
            <!-- Quick Actions -->
            <div class="rp-quick-actions">
                <button class="rp-quick-btn" id="quickReportCard">
                    <span class="rp-qb-icon">📊</span>
                    <span class="rp-qb-text"><strong>Generate Report</strong><small>Create a new HR report</small></span>
                </button>
                <button class="rp-quick-btn" id="quickExportCard">
                    <span class="rp-qb-icon">📥</span>
                    <span class="rp-qb-text"><strong>Quick Export</strong><small>Download employee data as CSV</small></span>
                </button>
                <button class="rp-quick-btn" id="quickRefreshCard">
                    <span class="rp-qb-icon">🔄</span>
                    <span class="rp-qb-text"><strong>Refresh Data</strong><small>Reload all report data</small></span>
                </button>
            </div>

            <!-- Quick Export Strip -->
            <div class="rp-panel">
                <h2 class="rp-panel-title">Quick Export</h2>
                <p class="rp-panel-sub">Download any dataset as a CSV file</p>
                <div class="rp-toolbar" style="margin-bottom:0">
                    <button class="rp-secondary-btn" data-export-btn="employees">👥 Employees</button>
                    <button class="rp-secondary-btn" data-export-btn="attendance">📅 Attendance</button>
                    <button class="rp-secondary-btn" data-export-btn="leave">🚪 Leave</button>
                    <button class="rp-secondary-btn" data-export-btn="payroll">💰 Payroll</button>
                    <button class="rp-secondary-btn" data-export-btn="training">🎓 Training</button>
                    <button class="rp-secondary-btn" data-export-btn="performance">⭐ Performance</button>
                    <button class="rp-secondary-btn" data-export-btn="recruitment">🎯 Recruitment</button>
                    <input type="hidden" id="exportType" value="employees">
                </div>
            </div>

            <!-- Recent Reports -->
            <div class="rp-reports">
                <article class="rp-panel">
                    <div class="rp-panel-header">
                        <div>
                            <h2 class="rp-panel-title">Recent Reports</h2>
                            <p class="rp-panel-sub" id="reportCount">Loading reports...</p>
                        </div>
                    </div>
                    <div class="rp-table-card">
                        <table class="rp-table">
                            <thead>
                                <tr>
                                    <th>Report Name</th>
                                    <th>Category</th>
                                    <th>Generated By</th>
                                    <th>Date</th>
                                    <th>Format</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody id="reportsBody">
                                <tr><td colspan="6" class="rp-loading">Loading reports...</td></tr>
                            </tbody>
                        </table>
                    </div>
                </article>
            </div>

        </section>

        <!--__HTML3__-->
        <!-- Generate Report Modal -->
        <div class="rp-modal" id="reportModal" aria-hidden="true">
            <div class="rp-modal-card">
                <div class="rp-modal-header">
                    <div>
                        <h2>Generate Report</h2>
                        <p>Create and register a new HR report</p>
                    </div>
                    <button type="button" class="rp-close-btn" data-close="reportModal" aria-label="Close">&times;</button>
                </div>
                <form id="reportForm">
                    <div class="rp-modal-body">
                        <div class="rp-form-grid">
                            <label class="rp-field">Report Name <span class="rp-req">*</span>
                                <input type="text" id="reportName" class="rp-input" placeholder="e.g. Monthly HR Summary" maxlength="160" required>
                            </label>
                            <label class="rp-field">Category
                                <select id="reportCategory" class="rp-input">
                                    <option value="General">General</option>
                                    <option value="Employment">Employment</option>
                                    <option value="Attendance">Attendance</option>
                                    <option value="Leave">Leave</option>
                                    <option value="Payroll">Payroll</option>
                                    <option value="Training">Training</option>
                                    <option value="Performance">Performance</option>
                                    <option value="Recruitment">Recruitment</option>
                                </select>
                            </label>
                            <label class="rp-field">Format
                                <select id="reportFormat" class="rp-input">
                                    <option value="PDF">PDF</option>
                                    <option value="EXCEL">Excel</option>
                                    <option value="CSV">CSV</option>
                                </select>
                            </label>
                            <div></div>
                            <label class="rp-field">Date From
                                <input type="date" id="reportDateFrom" class="rp-input">
                            </label>
                            <label class="rp-field">Date To
                                <input type="date" id="reportDateTo" class="rp-input">
                            </label>
                        </div>
                        <p class="rp-hint">The report will be registered and appear in the Recent Reports list. Use Quick Export above to download CSV data immediately.</p>
                        <p class="rp-error" id="reportError"></p>
                    </div>
                    <div class="rp-modal-actions">
                        <button type="button" class="rp-secondary-btn" data-close="reportModal">Cancel</button>
                        <button type="submit" class="rp-primary-btn">Generate Report</button>
                    </div>
                </form>
            </div>
        </div>

    </main>
</div>

<div class="rp-toast" id="toast" role="status"></div>

<script src="../assets/js/theme.js?v=1"></script>
<script src="../assets/js/hr-reports.js?v=1"></script>
</body>


<?php
// frontend/pages/manager/manager-reports.php
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports & Insights — Mindazzle Manager Portal</title>
    <meta name="description" content="Comprehensive team analytics, attendance trends, task status distribution, and performance reports in Mindazzle Manager Portal.">

    <!-- GOOGLE FONTS (Plus Jakarta Sans) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- STYLES -->
    <link rel="stylesheet" href="../../assets/css/manager-dashboard.css?v=<?= time() ?>">
</head>

<body>

<!-- LOADING OVERLAY -->
<div class="mgr-loading-overlay" id="mgrLoadingOverlay">
    <div class="mgr-spinner"></div>
</div>

<!-- TOAST NOTIFICATIONS CONTAINER -->
<div class="mgr-toast-container" id="mgrToastContainer"></div>

<!-- APP LAYOUT -->
<div class="mgr-app-layout">

    <!-- SIDEBAR -->
    <?php include '../../includes/manager-sidebar.php'; ?>

    <!-- MAIN WRAPPER -->
    <div class="mgr-main-wrapper">

        <!-- =====================================================
             TOPBAR
        ====================================================== -->
        <?php include '../../includes/manager-topbar.php'; ?>

        <!-- =====================================================
             MAIN CONTENT
        ====================================================== -->
        <main class="mgr-content-container">

            <!-- -------------------------------------------------
                 PAGE HEADER & FILTER BAR
            -------------------------------------------------- -->
            <section class="mgr-reports-filter-bar">
                <div class="mgr-filter-bar-header">
                    <h1 class="mgr-reports-page-title">Reports &amp; Insights</h1>
                    <p class="mgr-reports-page-sub">Comprehensive workforce analytics, attendance patterns, and performance metrics</p>
                </div>

                <div class="mgr-filter-bar-actions">
                    <div class="mgr-filter-controls">
                        <!-- Date Range Dropdown -->
                        <div class="mgr-custom-select-wrap">
                            <span class="mgr-select-icon">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                                    <line x1="16" y1="2" x2="16" y2="6"></line>
                                    <line x1="8" y1="2" x2="8" y2="6"></line>
                                    <line x1="3" y1="10" x2="21" y2="10"></line>
                                </svg>
                            </span>
                            <select id="repDateRangeSelect" class="mgr-filter-select" aria-label="Date Range">
                                <option value="01 Aug 2025 - 26 Aug 2025">01 Aug 2025 - 26 Aug 2025</option>
                                <option value="01 Jul 2025 - 31 Jul 2025">01 Jul 2025 - 31 Jul 2025</option>
                                <option value="01 Jun 2025 - 30 Jun 2025">01 Jun 2025 - 30 Jun 2025</option>
                                <option value="Last 90 Days">Last 90 Days</option>
                                <option value="Year 2025">Year 2025</option>
                            </select>
                            <span class="mgr-select-chevron">
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 12 15 18 9"></polyline></svg>
                            </span>
                        </div>

                        <!-- Department Dropdown -->
                        <div class="mgr-custom-select-wrap">
                            <span class="mgr-select-icon">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                    <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"></polygon>
                                </svg>
                            </span>
                            <select id="repDeptSelect" class="mgr-filter-select" aria-label="Department">
                                <option value="All Departments">All Departments</option>
                                <option value="Design">Design</option>
                                <option value="Development">Development</option>
                                <option value="HR">HR</option>
                                <option value="Marketing">Marketing</option>
                                <option value="QA">QA</option>
                            </select>
                            <span class="mgr-select-chevron">
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 12 15 18 9"></polyline></svg>
                            </span>
                        </div>
                    </div>

                    <!-- Export Report Action -->
                    <button class="mgr-btn-export" id="btnExportReport" type="button">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                            <polyline points="7 10 12 15 17 10"></polyline>
                            <line x1="12" y1="15" x2="12" y2="3"></line>
                        </svg>
                        <span>Export Report</span>
                    </button>
                </div>
            </section>

            <!-- -------------------------------------------------
                 ROW 1: 4 TOP METRIC CARDS
            -------------------------------------------------- -->
            <section class="mgr-kpi-grid">

                <!-- Card 1: Total Team Members -->
                <div class="mgr-kpi-card" id="kpiCardTotalMembers">
                    <div class="mgr-kpi-inner">
                        <div class="mgr-kpi-icon-circle kpi-circle-teal">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5c-1.66 0-3 1.34-3 3s1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5C6.34 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z"/>
                            </svg>
                        </div>
                        <div class="mgr-kpi-text-block">
                            <span class="mgr-kpi-label">Total Team Members</span>
                            <div class="mgr-kpi-value-row">
                                <span class="mgr-kpi-value" id="kpiTotalMembersVal">12</span>
                            </div>
                            <span class="mgr-trend-pill-green" id="kpiTotalMembersTrend">↑ 2</span>
                        </div>
                    </div>
                </div>

                <!-- Card 2: Average Attendance -->
                <div class="mgr-kpi-card" id="kpiCardAvgAttendance">
                    <div class="mgr-kpi-inner">
                        <div class="mgr-kpi-icon-circle kpi-circle-green">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M19 3h-1V1h-2v2H8V1H6v2H5c-1.11 0-1.99.9-1.99 2L3 19c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16H5V8h14v11zM7 10h5v5H7z"/>
                            </svg>
                        </div>
                        <div class="mgr-kpi-text-block">
                            <span class="mgr-kpi-label">Average Attendance</span>
                            <div class="mgr-kpi-value-row">
                                <span class="mgr-kpi-value" id="kpiAvgAttendanceVal">92%</span>
                            </div>
                            <span class="mgr-trend-pill-green" id="kpiAvgAttendanceTrend">↑ 4%</span>
                        </div>
                    </div>
                </div>

                <!-- Card 3: Average Performance -->
                <div class="mgr-kpi-card" id="kpiCardAvgPerformance">
                    <div class="mgr-kpi-inner">
                        <div class="mgr-kpi-icon-circle kpi-circle-peach">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/>
                            </svg>
                        </div>
                        <div class="mgr-kpi-text-block">
                            <span class="mgr-kpi-label">Average Performance</span>
                            <div class="mgr-kpi-value-row">
                                <span class="mgr-kpi-value" id="kpiAvgPerformanceVal">4.2 / 5</span>
                            </div>
                            <span class="mgr-trend-pill-green" id="kpiAvgPerformanceTrend">↑ 0.3</span>
                        </div>
                    </div>
                </div>

                <!-- Card 4: Tasks Completed -->
                <div class="mgr-kpi-card" id="kpiCardTasksCompleted">
                    <div class="mgr-kpi-inner">
                        <div class="mgr-kpi-icon-circle kpi-circle-blue">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M9 16.2L4.8 12l-1.4 1.4L9 19 21 7l-1.4-1.4L9 16.2z"/>
                            </svg>
                        </div>
                        <div class="mgr-kpi-text-block">
                            <span class="mgr-kpi-label">Tasks Completed</span>
                            <div class="mgr-kpi-value-row">
                                <span class="mgr-kpi-value" id="kpiTasksCompletedVal">48</span>
                            </div>
                            <span class="mgr-trend-pill-green" id="kpiTasksCompletedTrend">↑ 20%</span>
                        </div>
                    </div>
                </div>

            </section>

            <!-- -------------------------------------------------
                 ROW 2: 3 ANALYTICS VISUAL CARDS
                 (Attendance Wave Curve, Task Donut, Star Tier Breakdown)
            -------------------------------------------------- -->
            <section class="mgr-reports-row2-grid">

                <!-- Left Card: Attendance Trend Wave Chart (NO BAR CHART!) -->
                <div class="mgr-chart-card">
                    <div class="mgr-chart-header">
                        <div>
                            <h2 class="mgr-chart-title">Attendance Trend</h2>
                            <p class="mgr-chart-subtitle">6-Month Attendance Distribution</p>
                        </div>
                        <!-- Custom Wave Legend -->
                        <div class="mgr-wave-legend">
                            <div class="mgr-wave-legend-pill">
                                <span class="mgr-legend-dot" style="background:#10b981;"></span>
                                <span>Present</span>
                            </div>
                            <div class="mgr-wave-legend-pill">
                                <span class="mgr-legend-dot" style="background:#f59e0b;"></span>
                                <span>Leave</span>
                            </div>
                            <div class="mgr-wave-legend-pill">
                                <span class="mgr-legend-dot" style="background:#ef4444;"></span>
                                <span>Absent</span>
                            </div>
                        </div>
                    </div>

                    <!-- Multi-Curve Wave SVG Container -->
                    <div class="mgr-wave-chart-container">
                        <svg id="mgrAttendanceWaveSvg" viewBox="0 0 500 135" preserveAspectRatio="none" style="width:100%;height:135px;display:block;overflow:visible;">
                            <!-- Dynamically populated smooth Bezier spline wave curves with glowing area fill -->
                        </svg>
                        <div class="mgr-wave-x-axis" id="mgrWaveXAxis">
                            <span>Mar</span>
                            <span>Apr</span>
                            <span>May</span>
                            <span>Jun</span>
                            <span>Jul</span>
                            <span>Aug</span>
                        </div>
                    </div>
                </div>

                <!-- Middle Card: Task Status Donut -->
                <div class="mgr-chart-card">
                    <div class="mgr-chart-header">
                        <div>
                            <h2 class="mgr-chart-title">Task Status</h2>
                            <p class="mgr-chart-subtitle">Current task completion rate</p>
                        </div>
                    </div>

                    <div class="mgr-task-donut-wrap">
                        <div class="mgr-donut-svg-box">
                            <svg id="mgrTaskStatusDonutSvg" viewBox="0 0 130 130" style="width:120px;height:120px;transform:rotate(-90deg);">
                                <!-- Segments rendered via JS -->
                            </svg>
                            <div class="mgr-donut-center-label">
                                <div class="mgr-donut-center-val" id="mgrTaskTotalCount">48</div>
                                <div class="mgr-donut-center-lbl">Total Tasks</div>
                            </div>
                        </div>

                        <div class="mgr-dist-legend-list" id="mgrTaskStatusLegendList">
                            <!-- Populated dynamically via JS -->
                        </div>
                    </div>
                </div>

                <!-- Right Card: Performance Rating Star Tiers (NO BAR CHART!) -->
                <div class="mgr-chart-card">
                    <div class="mgr-chart-header">
                        <div>
                            <h2 class="mgr-chart-title">Performance Rating</h2>
                            <p class="mgr-chart-subtitle">Team rating distribution (5★ to 1★)</p>
                        </div>
                    </div>

                    <div class="mgr-rating-tiers-list" id="mgrRatingTiersList">
                        <!-- Populated dynamically via JS with star rows & progress tracks -->
                    </div>
                </div>

            </section>

            <!-- -------------------------------------------------
                 ROW 3: HORIZONTAL TAB BAR
            -------------------------------------------------- -->
            <section class="mgr-reports-tabs-row">
                <button type="button" class="mgr-report-tab active" data-tab="Team Performance">Team Performance</button>
                <button type="button" class="mgr-report-tab" data-tab="Attendance Report">Attendance Report</button>
                <button type="button" class="mgr-report-tab" data-tab="Task Report">Task Report</button>
                <button type="button" class="mgr-report-tab" data-tab="Leave Report">Leave Report</button>
            </section>

            <!-- -------------------------------------------------
                 ROW 4: FULL-WIDTH MAIN REPORT TABLE
            -------------------------------------------------- -->
            <section class="mgr-reports-table-section">

                <div class="mgr-table-card">
                    <div class="mgr-table-header-row">
                        <div>
                            <h2 class="mgr-table-title" id="mgrReportTableTitle">Team Performance Report</h2>
                            <p id="mgrReportTableSubtitle" style="margin:2px 0 0;font-size:12px;color:#64748b;">Employee rating and deliverables matrix</p>
                        </div>

                        <div class="mgr-search-box">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#64748b" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="11" cy="11" r="8"></circle>
                                <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                            </svg>
                            <input type="text" id="reportEmpSearchInput" placeholder="Search employee..." autocomplete="off" />
                        </div>
                    </div>

                    <div class="mgr-table-responsive">
                        <table class="mgr-custom-table" id="mgrReportsTable">
                            <thead id="mgrReportsTableHead">
                                <tr>
                                    <th style="width:40px;">#</th>
                                    <th>Employee</th>
                                    <th>Department</th>
                                    <th>Attendance Rate</th>
                                    <th>Tasks Completed</th>
                                    <th>Average Rating</th>
                                    <th>Status</th>
                                    <th style="text-align:right;">Action</th>
                                </tr>
                            </thead>
                            <tbody id="mgrReportsTableBody">
                                <!-- Populated dynamically by JS -->
                            </tbody>
                        </table>
                    </div>
                </div>

            </section>

            <!-- -------------------------------------------------
                 ROW 5: QUICK REPORTS & CUSTOM REPORT PROMO
            -------------------------------------------------- -->
            <section class="mgr-reports-bottom-grid">

                <!-- Quick Reports Card -->
                <div class="mgr-quick-reports-card">
                    <div class="mgr-quick-rep-header">
                        <div>
                            <h3 class="mgr-quick-rep-title">Quick Reports</h3>
                            <p class="mgr-quick-rep-sub" style="margin:2px 0 0;font-size:12px;color:#64748b;">Fast switch between workforce intelligence views</p>
                        </div>
                    </div>

                    <div class="mgr-quick-rep-list">
                        <div class="mgr-quick-rep-item" id="qrAttendanceReport">
                            <div class="mgr-quick-rep-left">
                                <div class="mgr-quick-rep-icon" style="background:#ecfdf5;color:#10b981;">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                                        <path d="M19 3h-1V1h-2v2H8V1H6v2H5c-1.11 0-1.99.9-1.99 2L3 19c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16H5V8h14v11zM7 10h5v5H7z"/>
                                    </svg>
                                </div>
                                <span class="mgr-quick-rep-name">Attendance Report</span>
                            </div>
                            <svg class="mgr-quick-rep-chevron" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="9 18 15 12 9 6"></polyline>
                            </svg>
                        </div>

                        <div class="mgr-quick-rep-item" id="qrPerformanceReport">
                            <div class="mgr-quick-rep-left">
                                <div class="mgr-quick-rep-icon" style="background:#fff7ed;color:#ea580c;">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                                        <path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/>
                                    </svg>
                                </div>
                                <span class="mgr-quick-rep-name">Performance Report</span>
                            </div>
                            <svg class="mgr-quick-rep-chevron" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="9 18 15 12 9 6"></polyline>
                            </svg>
                        </div>

                        <div class="mgr-quick-rep-item" id="qrTaskReport">
                            <div class="mgr-quick-rep-left">
                                <div class="mgr-quick-rep-icon" style="background:#eff6ff;color:#3b82f6;">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                                        <path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-5 14H7v-2h7v2zm3-4H7v-2h10v2zm0-4H7V7h10v2z"/>
                                    </svg>
                                </div>
                                <span class="mgr-quick-rep-name">Task Report</span>
                            </div>
                            <svg class="mgr-quick-rep-chevron" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="9 18 15 12 9 6"></polyline>
                            </svg>
                        </div>

                        <div class="mgr-quick-rep-item" id="qrLeaveReport">
                            <div class="mgr-quick-rep-left">
                                <div class="mgr-quick-rep-icon" style="background:#fef2f2;color:#ef4444;">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                                        <path d="M11.99 2C6.47 2 2 6.48 2 12s4.47 10 9.99 10C17.52 22 22 17.52 22 12S17.52 2 11.99 2zM12 20c-4.42 0-8-3.58-8-8s3.58-8 8-8 8 3.58 8 8-3.58 8-8 8zm.5-13H11v6l5.25 3.15.75-1.23-4.5-2.67z"/>
                                    </svg>
                                </div>
                                <span class="mgr-quick-rep-name">Leave Report</span>
                            </div>
                            <svg class="mgr-quick-rep-chevron" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="9 18 15 12 9 6"></polyline>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Custom Report Promo Card -->
                <div class="mgr-custom-report-card">
                    <div class="mgr-custom-rep-content">
                        <div class="mgr-custom-rep-icon-circle">
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#ffffff" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                                <line x1="3" y1="9" x2="21" y2="9"></line>
                                <line x1="9" y1="21" x2="9" y2="9"></line>
                            </svg>
                        </div>
                        <div class="mgr-custom-rep-text-wrap">
                            <h4 class="mgr-custom-rep-title">Need a Custom Report?</h4>
                            <p class="mgr-custom-rep-desc">Generate tailored analytics and aggregated metrics for executive and management presentations.</p>
                        </div>
                    </div>
                    <button type="button" class="mgr-btn-custom-rep" id="btnNeedCustomReport">
                        Create Custom Report
                    </button>
                </div>

            </section>

        </main>
    </div>
</div>

<!-- =========================================================
     MODAL: EMPLOYEE REPORT DETAIL
========================================================== -->
<div class="mgr-modal-backdrop" id="mgrEmployeeReportModal">
    <div class="mgr-modal-box">
        <div class="mgr-modal-header">
            <h2 class="mgr-modal-title">Employee Performance Insight</h2>
            <button type="button" class="mgr-modal-close" aria-label="Close modal">&times;</button>
        </div>
        <div class="mgr-modal-body" id="mgrReportModalBody">
            <!-- Populated dynamically via openEmployeeReportModal() -->
        </div>
        <div class="mgr-modal-footer">
            <button type="button" class="mgr-btn-view" onclick="closeModals()">Close</button>
        </div>
    </div>
</div>

<!-- =========================================================
     MODAL: CREATE CUSTOM REPORT
========================================================== -->
<div class="mgr-modal-backdrop" id="mgrCustomReportModal">
    <div class="mgr-modal-box">
        <div class="mgr-modal-header">
            <h2 class="mgr-modal-title">Configure Custom Report</h2>
            <button type="button" class="mgr-modal-close" aria-label="Close modal">&times;</button>
        </div>
        <form id="mgrCustomReportForm">
            <div class="mgr-modal-body">
                <div class="mgr-form-group">
                    <label class="mgr-form-label" for="customRepType">Report Type</label>
                    <select id="customRepType" class="mgr-form-select">
                        <option value="Executive Attendance & Punctuality Summary">Executive Attendance & Punctuality Summary</option>
                        <option value="Comprehensive Team Performance & Rating Matrix">Comprehensive Team Performance & Rating Matrix</option>
                        <option value="Sprint Deliverables & Task Burndown Analysis">Sprint Deliverables & Task Burndown Analysis</option>
                        <option value="Quarterly Leave & Absence Audit">Quarterly Leave & Absence Audit</option>
                    </select>
                </div>
                <div class="mgr-form-group">
                    <label class="mgr-form-label" for="customRepRange">Time Period</label>
                    <select id="customRepRange" class="mgr-form-select">
                        <option value="August 2025 (Current Month)">August 2025 (Current Month)</option>
                        <option value="Q3 2025 (Jul - Sep)">Q3 2025 (Jul - Sep)</option>
                        <option value="Year-to-Date 2025">Year-to-Date 2025</option>
                        <option value="Past 12 Months">Past 12 Months</option>
                    </select>
                </div>
                <div class="mgr-form-group">
                    <label class="mgr-form-label" for="customRepFormat">Export File Format</label>
                    <select id="customRepFormat" class="mgr-form-select">
                        <option value="CSV">Comma Separated Values (.CSV)</option>
                        <option value="PDF">Formatted Document (.PDF)</option>
                        <option value="XLSX">Excel Spreadsheet (.XLSX)</option>
                    </select>
                </div>
            </div>
            <div class="mgr-modal-footer">
                <button type="button" class="mgr-btn-view" onclick="closeModals()">Cancel</button>
                <button type="submit" class="mgr-btn-view" style="background:#0d9488;color:#ffffff;border-color:#0d9488;">Generate & Download</button>
            </div>
        </form>
    </div>
</div>

<!-- SCRIPTS -->
<script src="../../assets/js/theme.js?v=<?= time() ?>"></script>
<script src="../../assets/js/manager-reports.js?v=<?= time() ?>"></script>

</body>
</html>

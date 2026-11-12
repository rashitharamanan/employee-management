<?php
// frontend/pages/manager/manager-attendance.php
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Team Attendance — Mindazzle Manager Portal</title>
    <meta name="description" content="Monitor your team's attendance, working patterns, leave summaries, and pending approvals in Mindazzle Manager Portal.">

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
                 ROW 1: 4 TOP METRIC CARDS
            -------------------------------------------------- -->
            <section class="mgr-kpi-grid">

                <!-- Card 1: Present Today -->
                <div class="mgr-kpi-card" id="kpiCardPresent">
                    <div class="mgr-kpi-inner">
                        <div class="mgr-kpi-icon-circle kpi-circle-mint">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                <circle cx="12" cy="7" r="4"></circle>
                            </svg>
                        </div>
                        <div class="mgr-kpi-text-block">
                            <span class="mgr-kpi-label">Present Today</span>
                            <div class="mgr-kpi-value-row">
                                <span class="mgr-kpi-value" id="kpiPresentVal">9</span>
                                <span class="mgr-trend-pill-green" id="kpiPresentTrend">▲ 12% <span style="font-weight:500;color:#64748b;font-size:10px;">vs yesterday</span></span>
                            </div>
                            <span class="mgr-kpi-subtext" id="kpiPresentSub">Out of 12 members</span>
                        </div>
                    </div>
                </div>

                <!-- Card 2: Absent Today -->
                <div class="mgr-kpi-card" id="kpiCardAbsent">
                    <div class="mgr-kpi-inner">
                        <div class="mgr-kpi-icon-circle kpi-circle-peach">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="12" cy="12" r="10"></circle>
                                <line x1="15" y1="9" x2="9" y2="15"></line>
                                <line x1="9" y1="9" x2="15" y2="15"></line>
                            </svg>
                        </div>
                        <div class="mgr-kpi-text-block">
                            <span class="mgr-kpi-label">Absent Today</span>
                            <div class="mgr-kpi-value-row">
                                <span class="mgr-kpi-value" id="kpiAbsentVal">1</span>
                                <span class="mgr-trend-pill-green" id="kpiAbsentTrend" style="background:#fee2e2;color:#dc2626;">▲ 2% <span style="font-weight:500;color:#64748b;font-size:10px;">vs yesterday</span></span>
                            </div>
                            <span class="mgr-kpi-subtext" id="kpiAbsentSub">Out of 12 members</span>
                        </div>
                    </div>
                </div>

                <!-- Card 3: On Leave -->
                <div class="mgr-kpi-card" id="kpiCardLeave">
                    <div class="mgr-kpi-inner">
                        <div class="mgr-kpi-icon-circle kpi-circle-peach">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                                <line x1="16" y1="2" x2="16" y2="6"></line>
                                <line x1="8" y1="2" x2="8" y2="6"></line>
                                <line x1="3" y1="10" x2="21" y2="10"></line>
                            </svg>
                        </div>
                        <div class="mgr-kpi-text-block">
                            <span class="mgr-kpi-label">On Leave</span>
                            <div class="mgr-kpi-value-row">
                                <span class="mgr-kpi-value" id="kpiOnLeaveVal">2</span>
                            </div>
                            <span class="mgr-kpi-subtext" id="kpiOnLeaveSub">Out of 12 members</span>
                        </div>
                    </div>
                </div>

                <!-- Card 4: Attendance Rate -->
                <div class="mgr-kpi-card" id="kpiCardRate">
                    <div class="mgr-kpi-inner">
                        <div class="mgr-kpi-icon-circle kpi-circle-blue">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="22 12 18 12 15 21 9 3 6 12 2 12"></polyline>
                            </svg>
                        </div>
                        <div class="mgr-kpi-text-block">
                            <span class="mgr-kpi-label">Attendance Rate</span>
                            <div class="mgr-kpi-value-row">
                                <span class="mgr-kpi-value" id="kpiRateVal">92%</span>
                                <span class="mgr-trend-pill-green" id="kpiRateTrend">▲ 5%</span>
                            </div>
                            <span class="mgr-kpi-subtext" id="kpiRateSub">This month</span>
                        </div>
                    </div>
                </div>

            </section>


            <!-- -------------------------------------------------
                 ATTENDANCE MAIN GRID (LEFT: OVERVIEW + TABLE | RIGHT: LEAVE SUMMARY + REQUESTS + ACTIONS)
            -------------------------------------------------- -->
            <section class="mgr-attendance-grid">

                <!-- LEFT COLUMN: Attendance Overview + Team Attendance Table -->
                <div class="mgr-col-left">

                    <!-- Card 1: Attendance Overview (7-Day Stacked Bars) -->
                    <div class="mgr-card" style="margin-bottom:0;">
                        <div class="mgr-card-header-flex">
                            <div class="mgr-card-header-left">
                                <div class="mgr-card-icon-badge blue">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor">
                                        <path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16H5V8h14v11z"/>
                                    </svg>
                                </div>
                                <div>
                                    <h2 class="mgr-card-title" style="margin:0;font-size:15px;font-weight:800;color:#0f172a;">Attendance Overview</h2>
                                </div>
                            </div>

                            <!-- Legend -->
                            <div class="mgr-legend-group">
                                <div class="mgr-legend-item">
                                    <span class="mgr-legend-dot dot-present"></span>
                                    <span>Present</span>
                                </div>
                                <div class="mgr-legend-item">
                                    <span class="mgr-legend-dot" style="background:#ef4444;"></span>
                                    <span>Absent</span>
                                </div>
                                <div class="mgr-legend-item">
                                    <span class="mgr-legend-dot dot-leave"></span>
                                    <span>On Leave</span>
                                </div>
                            </div>
                        </div>

                        <div class="mgr-attendance-chart-wrap">
                            <div class="mgr-attendance-chart-flex">
                                <div class="mgr-chart-y-axis">
                                    <span>0</span>
                                    <span>4</span>
                                    <span>8</span>
                                    <span>12</span>
                                </div>
                                <div class="mgr-chart-bars-row" id="mgrChartBarsRow">
                                    <!-- Dynamically populated by manager-attendance.js -->
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Card 2: Team Attendance Table Card -->
                    <div class="mgr-card mgr-team-attendance-card" style="margin-bottom:0;">
                        <div class="mgr-card-header-flex">
                            <div class="mgr-card-header-left">
                                <div class="mgr-card-icon-badge teal">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor">
                                        <path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5c-1.66 0-3 1.34-3 3s1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5C6.34 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z"/>
                                    </svg>
                                </div>
                                <h2 class="mgr-card-title" style="margin:0;font-size:15px;font-weight:800;color:#0f172a;">Team Attendance</h2>
                            </div>

                            <!-- Date & Dept Filters -->
                            <div class="mgr-filter-group">
                                <select id="dateFilterSelect" class="mgr-filter-select">
                                    <option value="2025-08-26" selected>📅 26 Aug 2025</option>
                                    <option value="2025-08-25">📅 25 Aug 2025</option>
                                    <option value="2025-08-24">📅 24 Aug 2025</option>
                                    <option value="2025-08-23">📅 23 Aug 2025</option>
                                    <option value="2025-08-22">📅 22 Aug 2025</option>
                                    <option value="2025-08-21">📅 21 Aug 2025</option>
                                    <option value="2025-08-20">📅 20 Aug 2025</option>
                                </select>

                                <select id="deptFilterSelect" class="mgr-filter-select">
                                    <option value="All Departments" selected>All Departments</option>
                                    <option value="Design">Design</option>
                                    <option value="Development">Development</option>
                                    <option value="HR">HR</option>
                                    <option value="QA">QA</option>
                                    <option value="Marketing">Marketing</option>
                                </select>
                            </div>
                        </div>

                        <div class="mgr-card-body">
                            <table class="mgr-table" style="margin:0;">
                                <thead>
                                    <tr>
                                        <th style="width:40px;">#</th>
                                        <th>Employee</th>
                                        <th>Department</th>
                                        <th>Check In</th>
                                        <th>Check Out</th>
                                        <th>Work Hours</th>
                                        <th>Status</th>
                                        <th style="text-align:right;">Action</th>
                                    </tr>
                                </thead>
                                <tbody id="mgrAttendanceTableBody">
                                    <!-- Populated dynamically by manager-attendance.js -->
                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>

                <!-- RIGHT COLUMN: Leave Summary + Attendance Requests + Quick Actions -->
                <div class="mgr-col-right">

                    <!-- Card 1: Leave Summary (Donut Chart) -->
                    <div class="mgr-card" style="margin-bottom:0;">
                        <div class="mgr-card-header-flex">
                            <div class="mgr-card-header-left">
                                <div class="mgr-card-icon-badge orange">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor">
                                        <path d="M11.99 2C6.47 2 2 6.48 2 12s4.47 10 9.99 10C17.52 22 22 17.52 22 12S17.52 2 11.99 2zM12 20c-4.42 0-8-3.58-8-8s3.58-8 8-8 8 3.58 8 8-3.58 8-8 8z"/>
                                        <path d="M12.5 7H11v6l5.25 3.15.75-1.23-4.5-2.67z"/>
                                    </svg>
                                </div>
                                <h2 class="mgr-card-title" style="margin:0;font-size:15px;font-weight:800;color:#0f172a;">Leave Summary</h2>
                            </div>

                            <!-- Dropdown Filter -->
                            <select class="mgr-filter-select" id="leaveSummaryPeriod" onchange="showToast('Filtered summary: ' + this.value, 'info')">
                                <option value="This Month" selected>This Month</option>
                                <option value="Last Month">Last Month</option>
                                <option value="This Quarter">This Quarter</option>
                            </select>
                        </div>

                        <div class="mgr-leave-summary-body">
                            <div class="mgr-donut-wrapper">
                                <svg class="mgr-donut-svg" id="mgrLeaveDonutSvg" width="130" height="130" viewBox="0 0 130 130">
                                    <!-- Populated dynamically by manager-attendance.js -->
                                </svg>
                                <div class="mgr-donut-center-text">
                                    <span class="mgr-donut-big-val" id="mgrLeaveDonutVal">2</span>
                                    <span class="mgr-donut-sub">On Leave</span>
                                </div>
                            </div>
                            <div class="mgr-leave-legend-list" id="mgrLeaveLegendList">
                                <!-- Populated dynamically by manager-attendance.js -->
                            </div>
                        </div>
                    </div>

                    <!-- Card 2: Attendance Requests -->
                    <div class="mgr-card" style="margin-bottom:0;">
                        <div class="mgr-card-header-flex">
                            <div class="mgr-card-header-left">
                                <div class="mgr-card-icon-badge orange">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor">
                                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/>
                                    </svg>
                                </div>
                                <h2 class="mgr-card-title" style="margin:0;font-size:15px;font-weight:800;color:#0f172a;">Attendance Requests</h2>
                            </div>
                            <a href="javascript:void(0)" class="mgr-header-link" onclick="showToast('Viewing all pending leave requests')">View All</a>
                        </div>

                        <div class="mgr-requests-list" id="mgrRequestsList">
                            <!-- Populated dynamically by manager-attendance.js -->
                        </div>
                    </div>

                    <!-- Card 3: Quick Actions (2x2 Grid) -->
                    <div class="mgr-card" style="margin-bottom:0;">
                        <div class="mgr-card-header-flex">
                            <div class="mgr-card-header-left">
                                <div class="mgr-card-icon-badge blue">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor">
                                        <path d="M3 13h8V3H3v10zm0 8h8v-6H3v6zm10 0h8V11h-8v10zm0-18v6h8V3h-8z"/>
                                    </svg>
                                </div>
                                <h2 class="mgr-card-title" style="margin:0;font-size:15px;font-weight:800;color:#0f172a;">Quick Actions</h2>
                            </div>
                        </div>

                        <div class="mgr-quick-actions-grid">
                            <!-- 1. Mark Attendance -->
                            <button type="button" class="mgr-quick-action-tile" id="actionMarkAttendance">
                                <div class="mgr-tile-icon blue">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                        <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                                        <line x1="16" y1="2" x2="16" y2="6"/>
                                        <line x1="8" y1="2" x2="8" y2="6"/>
                                        <line x1="3" y1="10" x2="21" y2="10"/>
                                        <polyline points="9 15 12 18 15 15"/>
                                    </svg>
                                </div>
                                <div class="mgr-tile-text">
                                    <span class="mgr-tile-title">Mark Attendance</span>
                                    <span class="mgr-tile-sub">For team member</span>
                                </div>
                            </button>

                            <!-- 2. Approve Leave -->
                            <button type="button" class="mgr-quick-action-tile" id="actionApproveLeave">
                                <div class="mgr-tile-icon green">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                        <polyline points="9 11 12 14 22 4"/>
                                        <path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/>
                                    </svg>
                                </div>
                                <div class="mgr-tile-text">
                                    <span class="mgr-tile-title">Approve Leave</span>
                                    <span class="mgr-tile-sub">2 pending requests</span>
                                </div>
                            </button>

                            <!-- 3. Download Report -->
                            <button type="button" class="mgr-quick-action-tile" id="actionDownloadReport">
                                <div class="mgr-tile-icon purple">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                                        <polyline points="7 10 12 15 17 10"/>
                                        <line x1="12" y1="15" x2="12" y2="3"/>
                                    </svg>
                                </div>
                                <div class="mgr-tile-text">
                                    <span class="mgr-tile-title">Download Report</span>
                                    <span class="mgr-tile-sub">CSV / Excel format</span>
                                </div>
                            </button>

                            <!-- 4. Set Working Days -->
                            <button type="button" class="mgr-quick-action-tile" id="actionSetWorkingDays">
                                <div class="mgr-tile-icon blue">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                        <circle cx="12" cy="12" r="10"/>
                                        <polyline points="12 6 12 12 16 14"/>
                                    </svg>
                                </div>
                                <div class="mgr-tile-text">
                                    <span class="mgr-tile-title">Set Working Days</span>
                                    <span class="mgr-tile-sub" id="workingDaysSubtitle">Manage schedule</span>
                                </div>
                            </button>
                        </div>
                    </div>

                </div>

            </section>

        </main>

    </div>

</div>

<!-- =========================================================
     MODALS
========================================================== -->

<!-- 1. ATTENDANCE RECORD DETAIL MODAL -->
<div class="mgr-modal-backdrop" id="mgrAttendanceModal">
    <div class="mgr-modal-content">
        <div class="mgr-modal-header">
            <h3 class="mgr-modal-title">Attendance Record Details</h3>
            <button class="mgr-modal-close" type="button" aria-label="Close modal">&times;</button>
        </div>
        <div id="mgrAttendanceModalBody">
            <!-- Populated dynamically -->
        </div>
        <div class="mgr-modal-footer">
            <button type="button" class="mgr-btn-view" onclick="closeModals()">Close</button>
        </div>
    </div>
</div>

<!-- 2. LEAVE REQUEST APPROVAL MODAL -->
<div class="mgr-modal-backdrop" id="mgrLeaveApprovalModal">
    <div class="mgr-modal-content">
        <div class="mgr-modal-header">
            <h3 class="mgr-modal-title">Review Leave Request</h3>
            <button class="mgr-modal-close" type="button" aria-label="Close modal">&times;</button>
        </div>
        <div id="mgrLeaveApprovalModalBody">
            <!-- Populated dynamically -->
        </div>
    </div>
</div>

<!-- 3. MARK ATTENDANCE MODAL -->
<div class="mgr-modal-backdrop" id="mgrMarkAttendanceModal">
    <div class="mgr-modal-content" style="max-width:520px;">
        <div class="mgr-modal-header">
            <div>
                <h3 class="mgr-modal-title" style="font-size:16px;font-weight:800;color:#0f172a;">Mark Attendance</h3>
                <span style="font-size:12px;color:#64748b;">Manual punch / attendance entry for team member</span>
            </div>
            <button class="mgr-modal-close" type="button" aria-label="Close modal">&times;</button>
        </div>

        <form id="mgrMarkAttendanceForm" onsubmit="handleSaveMarkAttendance(event)">
            <div style="display:flex;flex-direction:column;gap:14px;margin-top:10px;">
                <!-- Employee Selection -->
                <div class="mgr-form-group">
                    <label class="mgr-form-label" for="markAttEmployee">Team Member <span class="req">*</span></label>
                    <select id="markAttEmployee" class="mgr-form-select" required>
                        <option value="">-- Select Team Member --</option>
                        <option value="1|Priya Sharma|Design|PS">Priya Sharma (Design)</option>
                        <option value="2|Rohan Verma|Development|RV">Rohan Verma (Development)</option>
                        <option value="3|Sneha Iyer|HR|SI">Sneha Iyer (HR)</option>
                        <option value="4|Aditya Kapoor|QA|AK">Aditya Kapoor (QA)</option>
                        <option value="5|Neha Tiwari|Marketing|NT">Neha Tiwari (Marketing)</option>
                        <option value="6|Rohit Kulkarni|Development|RK">Rohit Kulkarni (Development)</option>
                    </select>
                </div>

                <!-- Date & Status Row -->
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                    <div class="mgr-form-group">
                        <label class="mgr-form-label" for="markAttDate">Date <span class="req">*</span></label>
                        <input type="date" id="markAttDate" class="mgr-form-input" value="2025-08-26" required />
                    </div>

                    <div class="mgr-form-group">
                        <label class="mgr-form-label" for="markAttStatus">Attendance Status <span class="req">*</span></label>
                        <select id="markAttStatus" class="mgr-form-select" onchange="handleStatusChange(this.value)">
                            <option value="Present" selected>Present</option>
                            <option value="On Leave">On Leave</option>
                            <option value="Absent">Absent</option>
                            <option value="Half Day">Half Day</option>
                        </select>
                    </div>
                </div>

                <!-- Check In & Check Out Row -->
                <div id="markAttTimeRow" style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                    <div class="mgr-form-group">
                        <label class="mgr-form-label" for="markAttCheckIn">Check-In Time</label>
                        <input type="time" id="markAttCheckIn" class="mgr-form-input" value="09:00" onchange="calculateWorkHours()" />
                    </div>

                    <div class="mgr-form-group">
                        <label class="mgr-form-label" for="markAttCheckOut">Check-Out Time</label>
                        <input type="time" id="markAttCheckOut" class="mgr-form-input" value="18:00" onchange="calculateWorkHours()" />
                    </div>
                </div>

                <!-- Work Hours & Remarks -->
                <div style="display:grid;grid-template-columns:1fr 2fr;gap:12px;">
                    <div class="mgr-form-group">
                        <label class="mgr-form-label" for="markAttWorkHours">Work Hours</label>
                        <input type="text" id="markAttWorkHours" class="mgr-form-input" value="9h 0m" readonly style="background:#f8fafc;" />
                    </div>

                    <div class="mgr-form-group">
                        <label class="mgr-form-label" for="markAttNotes">Remarks / Reason</label>
                        <input type="text" id="markAttNotes" class="mgr-form-input" placeholder="e.g. On-site client visit / manual punch" />
                    </div>
                </div>
            </div>

            <div class="mgr-modal-footer" style="margin-top:20px;">
                <button type="button" class="mgr-btn-view" style="border-color:#cbd5e1;color:#64748b;" onclick="closeModals()">Cancel</button>
                <button type="submit" class="mgr-btn-view" style="background:#0d9488;color:#ffffff;border-color:#0d9488;padding:6px 20px;">Mark Attendance</button>
            </div>
        </form>
    </div>
</div>

<!-- 4. SET WORKING DAYS & SHIFT TIMER MODAL -->
<div class="mgr-modal-backdrop" id="mgrSetWorkingDaysModal">
    <div class="mgr-modal-content" style="max-width:540px;">
        <div class="mgr-modal-header">
            <div>
                <h3 class="mgr-modal-title" style="font-size:16px;font-weight:800;color:#0f172a;">Set Working Days &amp; Shift Timer</h3>
                <span style="font-size:12px;color:#64748b;">Configure weekly working schedule and daily attendance timers</span>
            </div>
            <button class="mgr-modal-close" type="button" aria-label="Close modal">&times;</button>
        </div>

        <form id="mgrWorkingDaysForm" onsubmit="handleSaveWorkingDays(event)">
            <div style="display:flex;flex-direction:column;gap:16px;margin-top:10px;">

                <!-- Weekly Working Days Selection -->
                <div class="mgr-form-group">
                    <label class="mgr-form-label">Working Days (Weekly Schedule) <span class="req">*</span></label>
                    <div class="mgr-days-selector" style="display:flex;gap:6px;flex-wrap:wrap;margin-top:4px;">
                        <label class="mgr-day-pill active">
                            <input type="checkbox" name="workday" value="Mon" checked onchange="toggleDayPill(this)" style="display:none;" />
                            <span>Mon</span>
                        </label>
                        <label class="mgr-day-pill active">
                            <input type="checkbox" name="workday" value="Tue" checked onchange="toggleDayPill(this)" style="display:none;" />
                            <span>Tue</span>
                        </label>
                        <label class="mgr-day-pill active">
                            <input type="checkbox" name="workday" value="Wed" checked onchange="toggleDayPill(this)" style="display:none;" />
                            <span>Wed</span>
                        </label>
                        <label class="mgr-day-pill active">
                            <input type="checkbox" name="workday" value="Thu" checked onchange="toggleDayPill(this)" style="display:none;" />
                            <span>Thu</span>
                        </label>
                        <label class="mgr-day-pill active">
                            <input type="checkbox" name="workday" value="Fri" checked onchange="toggleDayPill(this)" style="display:none;" />
                            <span>Fri</span>
                        </label>
                        <label class="mgr-day-pill">
                            <input type="checkbox" name="workday" value="Sat" onchange="toggleDayPill(this)" style="display:none;" />
                            <span>Sat</span>
                        </label>
                        <label class="mgr-day-pill">
                            <input type="checkbox" name="workday" value="Sun" onchange="toggleDayPill(this)" style="display:none;" />
                            <span>Sun</span>
                        </label>
                    </div>
                    <span id="selectedDaysLabel" style="font-size:11.5px;color:#64748b;margin-top:4px;display:block;">Currently selected: <strong>5 Days / Week (Mon – Fri)</strong></span>
                </div>

                <!-- Shift Timer (Start & End) -->
                <div style="background:#f8fafc;padding:14px;border-radius:10px;border:1px solid #e2e8f0;">
                    <label class="mgr-form-label" style="margin-bottom:8px;font-weight:700;">Daily Shift Timer</label>
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                        <div class="mgr-form-group">
                            <label class="mgr-form-label" for="timerShiftStart" style="font-size:11.5px;">Shift Start Time</label>
                            <input type="time" id="timerShiftStart" class="mgr-form-input" value="09:00" required />
                        </div>

                        <div class="mgr-form-group">
                            <label class="mgr-form-label" for="timerShiftEnd" style="font-size:11.5px;">Shift End Time</label>
                            <input type="time" id="timerShiftEnd" class="mgr-form-input" value="18:00" required />
                        </div>
                    </div>
                </div>

                <!-- Grace Period Timer & Half Day Threshold -->
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                    <div class="mgr-form-group">
                        <label class="mgr-form-label" for="timerGracePeriod">Late Grace Period</label>
                        <select id="timerGracePeriod" class="mgr-form-select">
                            <option value="5">5 Minutes</option>
                            <option value="10">10 Minutes</option>
                            <option value="15" selected>15 Minutes</option>
                            <option value="30">30 Minutes</option>
                        </select>
                    </div>

                    <div class="mgr-form-group">
                        <label class="mgr-form-label" for="timerHalfDay">Half-Day Threshold</label>
                        <select id="timerHalfDay" class="mgr-form-select">
                            <option value="4.0">4 Hours</option>
                            <option value="4.5" selected>4.5 Hours</option>
                            <option value="5.0">5 Hours</option>
                        </select>
                    </div>
                </div>

                <!-- Auto-Checkout Timer Rule -->
                <div class="mgr-form-group">
                    <label class="mgr-form-label" for="timerAutoPunchOut">Auto Clock-Out Rule</label>
                    <select id="timerAutoPunchOut" class="mgr-form-select">
                        <option value="none">Disabled (Manual checkout only)</option>
                        <option value="10h" selected>Auto checkout after 10 hours from Check-In</option>
                        <option value="08pm">Auto checkout at 08:00 PM</option>
                    </select>
                </div>

            </div>

            <div class="mgr-modal-footer" style="margin-top:20px;">
                <button type="button" class="mgr-btn-view" style="border-color:#cbd5e1;color:#64748b;" onclick="closeModals()">Cancel</button>
                <button type="submit" class="mgr-btn-view" style="background:#0d9488;color:#ffffff;border-color:#0d9488;padding:6px 20px;">Save Schedule &amp; Timer</button>
            </div>
        </form>
    </div>
</div>

<!-- SCRIPTS -->
<script src="../../assets/js/theme.js?v=<?= time() ?>"></script>
<script src="../../assets/js/manager-attendance.js?v=<?= time() ?>"></script>

</body>
</html>

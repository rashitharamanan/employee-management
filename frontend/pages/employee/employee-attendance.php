<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mindazzle — My Attendance</title>
    <meta name="description" content="Track your daily attendance, monthly logs, working hours, and time analytics.">

    <!-- GOOGLE FONTS -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- STYLES -->
    <link rel="stylesheet" href="../../assets/css/employee-sidebar.css?v=<?= time() ?>">
    <link rel="stylesheet" href="../../assets/css/employee-attendance.css?v=<?= time() ?>">
</head>

<body>

<!-- LOADING OVERLAY -->
<div class="emp-loading-overlay" id="empLoadingOverlay">
    <div class="emp-spinner"></div>
</div>

<!-- TOAST CONTAINER -->
<div class="emp-toast-container" id="empToastContainer"></div>

<!-- =====================================================
     APP LAYOUT
====================================================== -->
<div class="emp-app-layout">

    <!-- SIDEBAR -->
    <?php include '../../includes/employee-sidebar.php'; ?>

    <!-- MAIN WRAPPER -->
    <div class="emp-main-wrapper">

        <!-- =====================================================
             TOPBAR
        ====================================================== -->
        <?php include '../../includes/employee-topbar.php'; ?>

        <!-- =====================================================
             CONTENT AREA
        ====================================================== -->
        <main class="emp-content-area">

            <!-- === TOP 5 SUMMARY STAT CARDS === -->
            <section class="emp-att-metrics-grid">

                <!-- 1. Present -->
                <div class="emp-metric-card">
                    <div class="emp-metric-icon green">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#10b981" stroke-width="2.2">
                            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                            <circle cx="9" cy="7" r="4"></circle>
                            <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                            <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                        </svg>
                    </div>
                    <div class="emp-metric-info">
                        <div class="emp-metric-number" id="metricPresent">18</div>
                        <div class="emp-metric-title">Present</div>
                        <span class="emp-metric-sub">This Month</span>
                    </div>
                </div>

                <!-- 2. Absent -->
                <div class="emp-metric-card">
                    <div class="emp-metric-icon pink">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#f43f5e" stroke-width="2.2">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                            <circle cx="12" cy="7" r="4"></circle>
                            <line x1="18" y1="8" x2="23" y2="13"></line>
                            <line x1="23" y1="8" x2="18" y2="13"></line>
                        </svg>
                    </div>
                    <div class="emp-metric-info">
                        <div class="emp-metric-number" id="metricAbsent">1</div>
                        <div class="emp-metric-title">Absent</div>
                        <span class="emp-metric-sub">This Month</span>
                    </div>
                </div>

                <!-- 3. Late -->
                <div class="emp-metric-card">
                    <div class="emp-metric-icon orange">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#f59e0b" stroke-width="2.2">
                            <circle cx="12" cy="12" r="10"></circle>
                            <polyline points="12 6 12 12 16 14"></polyline>
                        </svg>
                    </div>
                    <div class="emp-metric-info">
                        <div class="emp-metric-number" id="metricLate">2</div>
                        <div class="emp-metric-title">Late</div>
                        <span class="emp-metric-sub">This Month</span>
                    </div>
                </div>

                <!-- 4. Half Day -->
                <div class="emp-metric-card">
                    <div class="emp-metric-icon purple">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#8b5cf6" stroke-width="2.2">
                            <circle cx="12" cy="12" r="10"></circle>
                            <path d="M12 2a10 10 0 0 1 0 20z" fill="#8b5cf6" opacity="0.3"></path>
                        </svg>
                    </div>
                    <div class="emp-metric-info">
                        <div class="emp-metric-number" id="metricHalfDay">1</div>
                        <div class="emp-metric-title">Half Day</div>
                        <span class="emp-metric-sub">This Month</span>
                    </div>
                </div>

                <!-- 5. Work From Home -->
                <div class="emp-metric-card">
                    <div class="emp-metric-icon blue">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#3b82f6" stroke-width="2.2">
                            <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                            <polyline points="9 22 9 12 15 12 15 22"></polyline>
                        </svg>
                    </div>
                    <div class="emp-metric-info">
                        <div class="emp-metric-number" id="metricWfh">3</div>
                        <div class="emp-metric-title">Work From Home</div>
                        <span class="emp-metric-sub">This Month</span>
                    </div>
                </div>

            </section>


            <!-- === MIDDLE ROW: TODAY'S ATTENDANCE + MONTHLY CALENDAR + MONTHLY SUMMARY === -->
            <section class="emp-att-middle-grid">

                <!-- 1. Today's Attendance -->
                <div class="emp-att-card emp-today-att-card">
                    <div class="emp-att-card-head">
                        <h3>Today's Attendance</h3>
                        <span class="emp-head-date" id="todayDateLabel">Tuesday, 16 September 2026</span>
                    </div>

                    <div class="emp-today-att-body">
                        <div class="emp-today-circle-group">
                            <!-- Circular Ring -->
                            <div class="emp-progress-ring-box">
                                <svg class="emp-ring-svg" viewBox="0 0 140 140">
                                    <circle cx="70" cy="70" r="56" class="emp-ring-bg"></circle>
                                    <circle cx="70" cy="70" r="56" class="emp-ring-active" id="todayRingProgress"></circle>
                                </svg>
                                <div class="emp-ring-center">
                                    <div class="emp-ring-check-icon">
                                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#ffffff" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round">
                                            <polyline points="20 6 9 17 4 12"></polyline>
                                        </svg>
                                    </div>
                                    <span class="emp-ring-status-txt" id="todayStatusTxt">Checked In</span>
                                    <span class="emp-ring-time-txt" id="todayTimeTxt">09:12 AM</span>
                                </div>
                            </div>

                            <!-- In / Out Time Boxes -->
                            <div class="emp-punch-times">
                                <div class="emp-punch-box">
                                    <div class="emp-punch-icon-circle in">
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#10b981" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"></path>
                                            <polyline points="10 17 15 12 10 7"></polyline>
                                            <line x1="15" y1="12" x2="3" y2="12"></line>
                                        </svg>
                                    </div>
                                    <div>
                                        <span class="emp-punch-label">Check In</span>
                                        <span class="emp-punch-value" id="todayCheckInVal">09:12 AM</span>
                                    </div>
                                </div>

                                <div class="emp-punch-box">
                                    <div class="emp-punch-icon-circle out">
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#ef4444" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                                            <polyline points="16 17 21 12 16 7"></polyline>
                                            <line x1="21" y1="12" x2="9" y2="12"></line>
                                        </svg>
                                    </div>
                                    <div>
                                        <span class="emp-punch-label">Check Out</span>
                                        <span class="emp-punch-value" id="todayCheckOutVal">-- : --</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Status Message -->
                        <div class="emp-work-status-line">
                            <span class="emp-status-dot-green"></span>
                            <span id="todayStatusMsg">You are currently at work</span>
                        </div>

                        <!-- Action Button -->
                        <button type="button" class="emp-btn-primary-punch" id="btnPunchAction">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                                <polyline points="16 17 21 12 16 7"></polyline>
                                <line x1="21" y1="12" x2="9" y2="12"></line>
                            </svg>
                            <span id="btnPunchText">Check Out</span>
                        </button>
                    </div>
                </div>

                <!-- 2. Monthly Attendance (Calendar) -->
                <div class="emp-att-card emp-calendar-card">
                    <div class="emp-att-card-head">
                        <h3>Monthly Attendance</h3>
                        <div class="emp-month-nav">
                            <button type="button" class="emp-nav-arrow-btn" id="btnPrevMonth" title="Previous Month">‹</button>
                            <span class="emp-nav-month-label" id="calendarMonthLabel">September 2026</span>
                            <button type="button" class="emp-nav-arrow-btn" id="btnNextMonth" title="Next Month">›</button>
                        </div>
                    </div>

                    <div class="emp-calendar-body">
                        <!-- Days of Week Header -->
                        <div class="emp-cal-week-headers">
                            <span>Sun</span>
                            <span>Mon</span>
                            <span>Tue</span>
                            <span>Wed</span>
                            <span>Thu</span>
                            <span>Fri</span>
                            <span>Sat</span>
                        </div>

                        <!-- Calendar Grid -->
                        <div class="emp-cal-days-grid" id="calendarDaysGrid">
                            <!-- Populated dynamically by JS -->
                        </div>

                        <!-- Calendar Legend -->
                        <div class="emp-cal-legend">
                            <div class="emp-cal-legend-item">
                                <span class="emp-cal-dot green"></span>
                                <span>Present</span>
                            </div>
                            <div class="emp-cal-legend-item">
                                <span class="emp-cal-dot red"></span>
                                <span>Absent</span>
                            </div>
                            <div class="emp-cal-legend-item">
                                <span class="emp-cal-dot orange"></span>
                                <span>Late</span>
                            </div>
                            <div class="emp-cal-legend-item">
                                <span class="emp-cal-dot purple"></span>
                                <span>Half Day</span>
                            </div>
                            <div class="emp-cal-legend-item">
                                <span class="emp-cal-dot blue"></span>
                                <span>Work From Home</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 3. Monthly Summary (Donut Chart) -->
                <div class="emp-att-card emp-summary-card">
                    <div class="emp-att-card-head">
                        <h3>Monthly Summary</h3>
                    </div>

                    <div class="emp-summary-body">
                        <!-- Donut SVG -->
                        <div class="emp-summary-donut-box">
                            <svg class="emp-donut-svg" id="summaryDonutSvg" viewBox="0 0 140 140">
                                <circle cx="70" cy="70" r="54" class="emp-donut-track"></circle>
                                <!-- Multi-color donut segments rendered by JS -->
                            </svg>
                            <div class="emp-summary-donut-center">
                                <span class="emp-summary-total-num" id="summaryTotalDays">25</span>
                                <span class="emp-summary-total-txt">Total Days</span>
                            </div>
                        </div>

                        <!-- Legend Rows with Percentages -->
                        <div class="emp-summary-stats-list">
                            <div class="emp-summary-stat-row">
                                <div class="emp-summary-stat-left">
                                    <span class="emp-cal-dot green"></span>
                                    <span>Present</span>
                                </div>
                                <span class="emp-summary-stat-val" id="summaryPctPresent">18 (72%)</span>
                            </div>

                            <div class="emp-summary-stat-row">
                                <div class="emp-summary-stat-left">
                                    <span class="emp-cal-dot red"></span>
                                    <span>Absent</span>
                                </div>
                                <span class="emp-summary-stat-val" id="summaryPctAbsent">1 (4%)</span>
                            </div>

                            <div class="emp-summary-stat-row">
                                <div class="emp-summary-stat-left">
                                    <span class="emp-cal-dot orange"></span>
                                    <span>Late</span>
                                </div>
                                <span class="emp-summary-stat-val" id="summaryPctLate">2 (8%)</span>
                            </div>

                            <div class="emp-summary-stat-row">
                                <div class="emp-summary-stat-left">
                                    <span class="emp-cal-dot purple"></span>
                                    <span>Half Day</span>
                                </div>
                                <span class="emp-summary-stat-val" id="summaryPctHalf">1 (4%)</span>
                            </div>

                            <div class="emp-summary-stat-row">
                                <div class="emp-summary-stat-left">
                                    <span class="emp-cal-dot blue"></span>
                                    <span>Work From Home</span>
                                </div>
                                <span class="emp-summary-stat-val" id="summaryPctWfh">3 (12%)</span>
                            </div>
                        </div>
                    </div>
                </div>

            </section>


            <!-- === BOTTOM ROW: ATTENDANCE HISTORY TABLE + SIDEBAR ACTIONS === -->
            <section class="emp-att-bottom-grid">

                <!-- Attendance History Table Card -->
                <div class="emp-att-card emp-history-card">
                    <div class="emp-history-head">
                        <h3>Attendance History</h3>

                        <!-- Filter Toolbar -->
                        <div class="emp-filter-toolbar">
                            <!-- Search by date -->
                            <div class="emp-table-search">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                                    <circle cx="11" cy="11" r="8"></circle>
                                    <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                                </svg>
                                <input type="text" id="tableSearchInput" placeholder="Search by date...">
                            </div>

                            <!-- Status Dropdown -->
                            <select id="tableStatusSelect" class="emp-filter-select">
                                <option value="ALL">All Status</option>
                                <option value="PRESENT">Present</option>
                                <option value="LATE">Late</option>
                                <option value="ABSENT">Absent</option>
                                <option value="HALF_DAY">Half Day</option>
                                <option value="WFH">Work From Home</option>
                            </select>

                            <!-- Month Dropdown -->
                            <select id="tableMonthSelect" class="emp-filter-select">
                                <option value="2026-09" selected>September 2026</option>
                                <option value="2026-08">August 2026</option>
                                <option value="2026-07">July 2026</option>
                            </select>

                            <!-- Request Correction Action Button -->
                            <button type="button" class="emp-btn-outline-correction" id="btnOpenCorrectionModal">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                    <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                                </svg>
                                <span>Request Correction</span>
                            </button>
                        </div>
                    </div>

                    <!-- Responsive Table -->
                    <div class="emp-table-container" style="overflow-x:auto; overflow-y:auto; max-height:400px;">
                        <table class="emp-att-table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Date</th>
                                    <th>Check In</th>
                                    <th>Check Out</th>
                                    <th>Total Hours</th>
                                    <th>Status</th>
                                    <th>Location</th>
                                    <th>Remarks</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody id="attendanceTableBody">
                                <!-- Populated dynamically by JS -->
                            </tbody>
                        </table>
                    </div>

                    <!-- Table Footer / Pagination -->
                    <div class="emp-table-footer">
                        <div class="emp-pagination-info" id="paginationInfo">
                            Showing 1 to 8 of 30 entries
                        </div>

                        <div class="emp-pagination-controls" id="paginationControls">
                            <!-- Page buttons populated by JS -->
                        </div>

                        <div class="emp-rows-per-page">
                            <span>Rows per page:</span>
                            <select id="rowsPerPageSelect">
                                <option value="8" selected>8</option>
                                <option value="15">15</option>
                                <option value="30">30</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Right Side Widget Column -->
                <div class="emp-att-side-col">

                    <!-- Quick Actions Card -->
                    <div class="emp-att-card emp-quick-menu-card">
                        <div class="emp-card-header">
                            <h3>Quick Actions</h3>
                        </div>
                        <div class="emp-quick-menu-body">
                            <button type="button" class="emp-menu-action-link" id="actionQuickCorrection">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#6d28d9" stroke-width="2">
                                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                    <polyline points="14 2 14 8 20 8"></polyline>
                                    <line x1="16" y1="13" x2="8" y2="13"></line>
                                    <line x1="16" y1="17" x2="8" y2="17"></line>
                                </svg>
                                <span>Request Attendance Correction</span>
                            </button>

                            <button type="button" class="emp-menu-action-link" id="actionQuickTeam">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#6d28d9" stroke-width="2">
                                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                                    <circle cx="9" cy="7" r="4"></circle>
                                    <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                                    <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                                </svg>
                                <span>View Team Attendance</span>
                            </button>

                            <button type="button" class="emp-menu-action-link" id="actionQuickDownload">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#6d28d9" stroke-width="2">
                                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                                    <polyline points="7 10 12 15 17 10"></polyline>
                                    <line x1="12" y1="15" x2="12" y2="3"></line>
                                </svg>
                                <span>Download Attendance Report</span>
                            </button>
                        </div>
                    </div>

                    <!-- Motivational Widget Card -->
                    <div class="emp-att-card emp-discipline-widget">
                        <div class="emp-discipline-top">
                            <div class="emp-discipline-text">
                                <h4>Discipline today creates a brighter tomorrow.</h4>
                            </div>
                            <div class="emp-discipline-art">
                                <img src="../../assets/images/attendance-widget.jpg" alt="Consistency" class="emp-discipline-img">
                            </div>
                        </div>

                        <div class="emp-discipline-quote">
                            <span class="emp-quote-mark">“</span>
                            <p>Show up, even on the hard days. Progress starts with presence.</p>
                            <span class="emp-quote-author">— Unknown</span>
                        </div>
                    </div>

                </div>

            </section>

        </main>

    </div><!-- /.emp-main-wrapper -->

</div><!-- /.emp-app-layout -->


<!-- =====================================================
     CORRECTION MODAL
====================================================== -->
<div class="emp-modal-backdrop" id="correctionModal" style="display:none;">
    <div class="emp-modal-dialog">
        <div class="emp-modal-header">
            <h3>Request Attendance Correction</h3>
            <button type="button" class="emp-modal-close" id="btnCloseCorrectionModal">✕</button>
        </div>
        <form id="correctionForm" class="emp-modal-body">
            <div class="emp-form-group">
                <label for="corrDate">Date</label>
                <input type="date" id="corrDate" name="date" required value="2026-09-14">
            </div>
            <div class="emp-form-row">
                <div class="emp-form-group">
                    <label for="corrCheckIn">Requested Check In</label>
                    <input type="time" id="corrCheckIn" name="check_in" value="09:00">
                </div>
                <div class="emp-form-group">
                    <label for="corrCheckOut">Requested Check Out</label>
                    <input type="time" id="corrCheckOut" name="check_out" value="18:00">
                </div>
            </div>
            <div class="emp-form-group">
                <label for="corrReason">Reason for Correction</label>
                <textarea id="corrReason" name="reason" rows="3" placeholder="e.g. Forgot to punch QR code on arrival..." required></textarea>
            </div>
            <div class="emp-modal-footer">
                <button type="button" class="emp-btn-cancel" id="btnCancelCorrection">Cancel</button>
                <button type="submit" class="emp-btn-submit" id="btnSubmitCorrection">Submit Request</button>
            </div>
        </form>
    </div>
</div>


<!-- SCRIPTS -->
<script src="../../assets/js/theme.js?v=<?= time() ?>"></script>
<script src="../../assets/js/employee-attendance.js?v=<?= time() ?>"></script>

</body>
</html>

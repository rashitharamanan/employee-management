<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mindazzle — Employee Dashboard</title>
    <meta name="description" content="Your personal employee dashboard — attendance, leave balance, performance, training and recent activity.">

    <!-- GOOGLE FONTS (Plus Jakarta Sans) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Caveat:wght@600&display=swap" rel="stylesheet">

    <!-- STYLES -->
    <link rel="stylesheet" href="../../assets/css/employee-sidebar.css?v=<?= time() ?>">
    <link rel="stylesheet" href="../../assets/css/employee-dashboard.css?v=<?= time() ?>">
</head>

<body>

<!-- =====================================================
     LOADING OVERLAY
====================================================== -->
<div class="emp-loading-overlay" id="empLoadingOverlay">
    <div class="emp-spinner"></div>
</div>

<!-- =====================================================
     TOAST NOTIFICATIONS
====================================================== -->
<div class="emp-toast-container" id="empToastContainer"></div>

<!-- =====================================================
     MAIN LAYOUT
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
             DASHBOARD CONTENT
        ====================================================== -->
        <main class="emp-content-area">

            <!-- === ROW 1: 4 STAT SUMMARY CARDS === -->
            <section class="emp-stats-grid">

                <!-- 1. Today's Attendance -->
                <div class="emp-stat-box" id="statBoxAttendance">
                    <div class="emp-stat-icon-circle green">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#10b981" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="10"></circle>
                            <polyline points="12 6 12 12 16 14"></polyline>
                        </svg>
                    </div>
                    <div class="emp-stat-info">
                        <span class="emp-stat-title">Today's Attendance</span>
                        <div class="emp-stat-val" id="statCheckIn">09:12 AM</div>
                        <div class="emp-badge-wrap">
                            <span class="emp-badge green" id="statAttBadge">Checked In</span>
                        </div>
                    </div>
                    <div class="emp-stat-arrow">›</div>
                </div>

                <!-- 2. Leave Balance -->
                <div class="emp-stat-box" id="statBoxLeave">
                    <div class="emp-stat-icon-circle pink">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#f43f5e" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                            <line x1="16" y1="2" x2="16" y2="6"></line>
                            <line x1="8" y1="2" x2="8" y2="6"></line>
                            <line x1="3" y1="10" x2="21" y2="10"></line>
                        </svg>
                    </div>
                    <div class="emp-stat-info">
                        <span class="emp-stat-title">Leave Balance</span>
                        <div class="emp-stat-val" id="statLeaveBalance">12 Days</div>
                        <span class="emp-stat-note" id="statLeaveSub">out of 18 days</span>
                    </div>
                    <div class="emp-stat-arrow">›</div>
                </div>

                <!-- 3. Performance Rating -->
                <div class="emp-stat-box" id="statBoxPerf">
                    <div class="emp-stat-icon-circle blue">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#3b82f6" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="18" y1="20" x2="18" y2="10"></line>
                            <line x1="12" y1="20" x2="12" y2="4"></line>
                            <line x1="6" y1="20" x2="6" y2="14"></line>
                        </svg>
                    </div>
                    <div class="emp-stat-info">
                        <span class="emp-stat-title">Performance Rating</span>
                        <div class="emp-stat-val" id="statPerfRating">4.3 / 5</div>
                        <span class="emp-stat-note" id="statPerfSub">Last reviewed on 15 Jul 2026</span>
                    </div>
                    <div class="emp-stat-arrow">›</div>
                </div>

                <!-- 4. Training Progress -->
                <div class="emp-stat-box" id="statBoxTraining">
                    <div class="emp-stat-icon-circle blue">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#3b82f6" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M22 10v6M2 10l10-5 10 5-10 5z"></path>
                            <path d="M6 12v5c3 3 9 3 12 0v-5"></path>
                        </svg>
                    </div>
                    <div class="emp-stat-info">
                        <span class="emp-stat-title">Training Progress</span>
                        <div class="emp-stat-val" id="statTrainingProgress">3 / 5</div>
                        <span class="emp-stat-note" id="statTrainingSub">Courses in progress</span>
                    </div>
                    <div class="emp-stat-arrow">›</div>
                </div>

            </section>


            <!-- === ROW 2: MOTIVATION BANNER + QUICK ACTIONS === -->
            <section class="emp-row-banner-actions">

                <!-- Motivational Banner -->
                <div class="emp-banner-card">
                    <div class="emp-banner-left">
                        <h2 class="emp-banner-greeting">Keep Going, <span id="empBannerName">Priya</span>!</h2>
                        <p class="emp-banner-sub">Small steps every day lead to big achievements.</p>
                        
                        <div class="emp-banner-quote">
                            <p class="quote-text">“Success is the sum of small efforts, repeated day in and day out.”</p>
                            <span class="quote-author">— Robert Collier</span>
                        </div>
                    </div>

                    <div class="emp-banner-right">
                        <div class="emp-banner-handwritten">
                            <span>Progress<br>Looks Good<br>On You</span>
                            <svg class="emp-banner-curve" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M5 12c5 5 9 5 14-2"></path>
                            </svg>
                        </div>
                        <div class="emp-banner-art-wrap">
                            <img src="../../assets/images/banner-illustration.jpg" alt="Focus and Growth" class="emp-banner-art">
                        </div>
                    </div>
                </div>

                <!-- Quick Actions Card -->
                <div class="emp-card emp-quick-actions-card">
                    <div class="emp-card-header">
                        <div class="emp-card-title-group">
                            <span class="emp-header-icon blue">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M9 11l3 3L22 4"></path>
                                    <path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"></path>
                                </svg>
                            </span>
                            <h3>Quick Actions</h3>
                        </div>
                    </div>

                    <div class="emp-quick-actions-grid">
                        <!-- Check Out Button -->
                        <button type="button" class="emp-action-tile" id="empQuickCheckOut">
                            <div class="emp-tile-icon-circle red">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#ef4444" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="12" cy="12" r="10"></circle>
                                    <polyline points="12 6 12 12 16 14"></polyline>
                                </svg>
                            </div>
                            <span id="quickCheckInOutText">Check Out</span>
                        </button>

                        <!-- Apply Leave Button -->
                        <a href="employee-leave.php" class="emp-action-tile" id="empQuickApplyLeave">
                            <div class="emp-tile-icon-circle blue">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#3b82f6" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                                    <line x1="16" y1="2" x2="16" y2="6"></line>
                                    <line x1="8" y1="2" x2="8" y2="6"></line>
                                    <line x1="3" y1="10" x2="21" y2="10"></line>
                                    <line x1="12" y1="14" x2="12" y2="18"></line>
                                    <line x1="10" y1="16" x2="14" y2="16"></line>
                                </svg>
                            </div>
                            <span>Apply Leave</span>
                        </a>

                        <!-- View Payslips Button -->
                        <a href="employee-payslips.php" class="emp-action-tile" id="empQuickPayslips">
                            <div class="emp-tile-icon-circle blue">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#3b82f6" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                    <polyline points="14 2 14 8 20 8"></polyline>
                                    <line x1="16" y1="13" x2="8" y2="13"></line>
                                    <line x1="16" y1="17" x2="8" y2="17"></line>
                                </svg>
                            </div>
                            <span>View Payslips</span>
                        </a>

                        <!-- View Documents Button -->
                        <a href="employee-documents.php" class="emp-action-tile" id="empQuickDocs">
                            <div class="emp-tile-icon-circle blue">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#3b82f6" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"></path>
                                </svg>
                            </div>
                            <span>View Documents</span>
                        </a>
                    </div>
                </div>

            </section>


            <!-- === ROW 2.5: ASSIGNED TASKS FROM MANAGER === -->
            <section class="emp-row-tasks" id="empTasksSection">
                <div class="emp-card emp-tasks-main-card">
                    <div class="emp-card-header">
                        <div class="emp-card-title-group">
                            <span class="emp-header-icon blue">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"></path>
                                    <rect x="8" y="2" width="8" height="4" rx="1" ry="1"></rect>
                                    <path d="M9 14l2 2 4-4"></path>
                                </svg>
                            </span>
                            <div class="emp-tasks-header-info">
                                <h3 style="margin:0;font-size:16px;font-weight:700;color:#0f172a;">Assigned Tasks from Manager</h3>
                                <span class="emp-tasks-badge" id="empTasksCountBadge">0 Pending</span>
                            </div>
                        </div>
                        <div class="emp-tasks-filter-group" id="empTaskFilters">
                            <button type="button" class="emp-task-filter-btn active" data-filter="ALL">All</button>
                            <button type="button" class="emp-task-filter-btn" data-filter="PENDING">Pending</button>
                            <button type="button" class="emp-task-filter-btn" data-filter="COMPLETED">Completed</button>
                        </div>
                    </div>

                    <div class="emp-card-content" style="padding-top:14px;">
                        <div class="emp-tasks-grid" id="empTasksList">
                            <!-- Populated dynamically by JS -->
                        </div>
                    </div>
                </div>
            </section>


            <!-- === ROW 3: ATTENDANCE OVERVIEW + UPCOMING LEAVE + UPCOMING HOLIDAYS === -->
            <section class="emp-row-3col">

                <!-- 1. Attendance Overview -->
                <div class="emp-card">
                    <div class="emp-card-header">
                        <div class="emp-card-title-group">
                            <span class="emp-header-icon purple">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                                    <line x1="18" y1="20" x2="18" y2="10"></line>
                                    <line x1="12" y1="20" x2="12" y2="4"></line>
                                    <line x1="6" y1="20" x2="6" y2="14"></line>
                                </svg>
                            </span>
                            <h3>Attendance Overview</h3>
                        </div>
                        <div class="emp-select-wrap">
                            <select id="empAttendanceMonth" class="emp-month-dropdown" aria-label="Select month">
                                <option value="2026-09" selected>September 2026</option>
                                <option value="2026-08">August 2026</option>
                                <option value="2026-07">July 2026</option>
                            </select>
                        </div>
                    </div>

                    <div class="emp-card-content">
                        <div class="emp-attendance-layout">
                            <!-- Donut SVG -->
                            <div class="emp-donut-box">
                                <svg class="emp-donut-ring" id="empDonutSvg" viewBox="0 0 120 120">
                                    <!-- Base circle -->
                                    <circle cx="60" cy="60" r="46" class="emp-donut-bg-circle"></circle>
                                    <!-- Segments appended dynamically by JS -->
                                </svg>
                                <div class="emp-donut-center-info">
                                    <span class="emp-donut-total-num" id="empDonutCenterValue">22</span>
                                    <span class="emp-donut-total-label">Total Days</span>
                                </div>
                            </div>

                            <!-- Legend List -->
                            <div class="emp-legend-list">
                                <div class="emp-legend-item">
                                    <span class="emp-legend-dot" style="background:#10b981;"></span>
                                    <span class="emp-legend-name">Present</span>
                                    <span class="emp-legend-val" id="empLegendPresent">18</span>
                                </div>
                                <div class="emp-legend-item">
                                    <span class="emp-legend-dot" style="background:#ef4444;"></span>
                                    <span class="emp-legend-name">Absent</span>
                                    <span class="emp-legend-val" id="empLegendAbsent">1</span>
                                </div>
                                <div class="emp-legend-item">
                                    <span class="emp-legend-dot" style="background:#f59e0b;"></span>
                                    <span class="emp-legend-name">Late</span>
                                    <span class="emp-legend-val" id="empLegendLate">2</span>
                                </div>
                                <div class="emp-legend-item">
                                    <span class="emp-legend-dot" style="background:#8b5cf6;"></span>
                                    <span class="emp-legend-name">Half Day</span>
                                    <span class="emp-legend-val" id="empLegendHalf">1</span>
                                </div>
                                <div class="emp-legend-item">
                                    <span class="emp-legend-dot" style="background:#3b82f6;"></span>
                                    <span class="emp-legend-name">Work From Home</span>
                                    <span class="emp-legend-val" id="empLegendWfh">3</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 2. Upcoming Leave -->
                <div class="emp-card">
                    <div class="emp-card-header">
                        <div class="emp-card-title-group">
                            <span class="emp-header-icon purple">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                                    <line x1="16" y1="2" x2="16" y2="6"></line>
                                    <line x1="8" y1="2" x2="8" y2="6"></line>
                                    <line x1="3" y1="10" x2="21" y2="10"></line>
                                </svg>
                            </span>
                            <h3>Upcoming Leave</h3>
                        </div>
                        <a href="employee-leave.php" class="emp-link-view-all">View All</a>
                    </div>

                    <div class="emp-card-content">
                        <div class="emp-items-stack" id="empLeaveList">
                            <!-- Rendered dynamically by JS -->
                        </div>
                    </div>
                </div>

                <!-- 3. Upcoming Holidays -->
                <div class="emp-card">
                    <div class="emp-card-header">
                        <div class="emp-card-title-group">
                            <span class="emp-header-icon purple">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                                    <line x1="16" y1="2" x2="16" y2="6"></line>
                                    <line x1="8" y1="2" x2="8" y2="6"></line>
                                    <line x1="3" y1="10" x2="21" y2="10"></line>
                                </svg>
                            </span>
                            <h3>Upcoming Holidays</h3>
                        </div>
                        <a href="employee-attendance.php" class="emp-link-view-all">View All</a>
                    </div>

                    <div class="emp-card-content">
                        <div class="emp-items-stack" id="empHolidayList">
                            <!-- Rendered dynamically by JS -->
                        </div>
                    </div>
                </div>

            </section>


            <!-- === ROW 4: MY GOALS + MY TRAINING + RECENT ACTIVITY === -->
            <section class="emp-row-3col">

                <!-- 1. My Goals -->
                <div class="emp-card">
                    <div class="emp-card-header">
                        <div class="emp-card-title-group">
                            <span class="emp-header-icon purple">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="12" cy="12" r="10"></circle>
                                    <circle cx="12" cy="12" r="6"></circle>
                                    <circle cx="12" cy="12" r="2"></circle>
                                </svg>
                            </span>
                            <h3>My Goals</h3>
                        </div>
                        <a href="employee-performance.php" class="emp-link-view-all">View All</a>
                    </div>

                    <div class="emp-card-content">
                        <div class="emp-progress-stack" id="empGoalsList">
                            <!-- Rendered dynamically by JS -->
                        </div>
                    </div>
                </div>

                <!-- 2. My Training -->
                <div class="emp-card">
                    <div class="emp-card-header">
                        <div class="emp-card-title-group">
                            <span class="emp-header-icon purple">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M22 10v6M2 10l10-5 10 5-10 5z"></path>
                                    <path d="M6 12v5c3 3 9 3 12 0v-5"></path>
                                </svg>
                            </span>
                            <h3>My Training</h3>
                        </div>
                        <a href="employee-performance.php" class="emp-link-view-all">View All</a>
                    </div>

                    <div class="emp-card-content">
                        <div class="emp-progress-stack" id="empTrainingList">
                            <!-- Rendered dynamically by JS -->
                        </div>
                    </div>
                </div>

                <!-- 3. Recent Activity -->
                <div class="emp-card">
                    <div class="emp-card-header">
                        <div class="emp-card-title-group">
                            <span class="emp-header-icon purple">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                    <polyline points="22 12 18 12 15 21 9 3 6 12 2 12"></polyline>
                                </svg>
                            </span>
                            <h3>Recent Activity</h3>
                        </div>
                        <a href="employee-attendance.php" class="emp-link-view-all">View All</a>
                    </div>

                    <div class="emp-card-content">
                        <div class="emp-activity-stack" id="empActivityList">
                            <!-- Rendered dynamically by JS -->
                        </div>
                    </div>
                </div>

            </section>

        </main>

    </div><!-- /.emp-main-wrapper -->

</div><!-- /.emp-app-layout -->


<!-- =====================================================
     SCRIPTS
====================================================== -->
<script src="../../assets/js/theme.js?v=<?= time() ?>"></script>
<script src="../../assets/js/employee-dashboard.js?v=<?= time() ?>"></script>

</body>
</html>

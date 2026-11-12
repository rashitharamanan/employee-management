<?php
// frontend/pages/manager/manager-dashboard.php
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mindazzle — Manager Portal</title>
    <meta name="description" content="Mindazzle Manager Portal. Team attendance, performance ratings, pending approvals, team members, and upcoming training courses.">

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
                
                <!-- Card 1: Team Members -->
                <div class="mgr-kpi-card" onclick="document.getElementById('myTeamCard').scrollIntoView({behavior:'smooth'})">
                    <div class="mgr-kpi-inner">
                        <div class="mgr-kpi-icon-circle kpi-circle-mint">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                                <circle cx="9" cy="7" r="4"></circle>
                                <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                                <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                            </svg>
                        </div>
                        <div class="mgr-kpi-text-block">
                            <span class="mgr-kpi-label">Team Members</span>
                            <div class="mgr-kpi-value-row">
                                <span class="mgr-kpi-value" id="kpiTeamMembersVal">24</span>
                                <span class="mgr-trend-pill-green">▲ +2</span>
                            </div>
                            <span class="mgr-kpi-subtext" id="kpiTeamMembersSub">Compared to last month</span>
                        </div>
                    </div>
                </div>

                <!-- Card 2: Present Today -->
                <div class="mgr-kpi-card" onclick="document.getElementById('teamAttendanceCard').scrollIntoView({behavior:'smooth'})">
                    <div class="mgr-kpi-inner">
                        <div class="mgr-kpi-icon-circle kpi-circle-blue">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                                <line x1="16" y1="2" x2="16" y2="6"></line>
                                <line x1="8" y1="2" x2="8" y2="6"></line>
                                <line x1="3" y1="10" x2="21" y2="10"></line>
                                <path d="M9 16l2 2 4-4"></path>
                            </svg>
                        </div>
                        <div class="mgr-kpi-text-block">
                            <span class="mgr-kpi-label">Present Today</span>
                            <div class="mgr-kpi-value-row">
                                <span class="mgr-kpi-value" id="kpiPresentTodayVal">20</span>
                                <span class="mgr-pill-blue" id="kpiPresentTodayPct">◆ 83%</span>
                            </div>
                            <span class="mgr-kpi-subtext" id="kpiPresentTodaySub">Out of 24 members</span>
                        </div>
                    </div>
                </div>

                <!-- Card 3: On Leave -->
                <div class="mgr-kpi-card" onclick="document.getElementById('myTeamCard').scrollIntoView({behavior:'smooth'})">
                    <div class="mgr-kpi-inner">
                        <div class="mgr-kpi-icon-circle kpi-circle-peach">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="12" cy="12" r="10"></circle>
                                <polyline points="12 6 12 12 16 14"></polyline>
                            </svg>
                        </div>
                        <div class="mgr-kpi-text-block">
                            <span class="mgr-kpi-label">On Leave</span>
                            <div class="mgr-kpi-value-row">
                                <span class="mgr-kpi-value" id="kpiOnLeaveVal">2</span>
                                <span class="mgr-pill-orange" id="kpiOnLeavePct">◆ 8%</span>
                            </div>
                            <span class="mgr-kpi-subtext" id="kpiOnLeaveSub">Today</span>
                        </div>
                    </div>
                </div>

                <!-- Card 4: Pending Approvals -->
                <div class="mgr-kpi-card" onclick="document.getElementById('pendingApprovalsCard').scrollIntoView({behavior:'smooth'})">
                    <div class="mgr-kpi-inner">
                        <div class="mgr-kpi-icon-circle kpi-circle-lavender">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                <line x1="18" y1="20" x2="18" y2="10"></line>
                                <line x1="12" y1="20" x2="12" y2="4"></line>
                                <line x1="6" y1="20" x2="6" y2="14"></line>
                            </svg>
                        </div>
                        <div class="mgr-kpi-text-block">
                            <span class="mgr-kpi-label">Pending Approvals</span>
                            <div class="mgr-kpi-value-row">
                                <span class="mgr-kpi-value" id="kpiPendingApprovalsVal">5</span>
                            </div>
                            <span class="mgr-kpi-subtext" id="kpiPendingApprovalsSub">Leaves, documents, etc.</span>
                        </div>
                    </div>
                    <span class="mgr-kpi-chevron">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="9 18 15 12 9 6"></polyline>
                        </svg>
                    </span>
                </div>

            </section>

            <!-- -------------------------------------------------
                 ROW 2: TWO ANALYTICS CARDS (Attendance & Performance)
            -------------------------------------------------- -->
            <section class="mgr-analytics-grid">
                
                <!-- Left: Team Attendance Stacked Bars -->
                <div class="mgr-card" id="teamAttendanceCard">
                    <div class="mgr-card-header">
                        <div class="mgr-card-title-group">
                            <div class="mgr-card-header-icon" style="color:#2563eb;">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                                    <line x1="16" y1="2" x2="16" y2="6"></line>
                                    <line x1="8" y1="2" x2="8" y2="6"></line>
                                    <line x1="3" y1="10" x2="21" y2="10"></line>
                                    <polyline points="9 15 12 18 15 15"></polyline>
                                </svg>
                            </div>
                            <div>
                                <h2 class="mgr-card-title">Team Attendance</h2>
                                <p class="mgr-card-subtitle">Last 7 days overview</p>
                            </div>
                        </div>

                        <!-- Legend -->
                        <div class="mgr-legend-group">
                            <div class="mgr-legend-item">
                                <span class="dot dot-present" style="width:8px;height:8px;border-radius:50%;"></span>
                                <span>Present</span>
                            </div>
                            <div class="mgr-legend-item">
                                <span class="dot dot-leave" style="width:8px;height:8px;border-radius:50%;"></span>
                                <span>Leave</span>
                            </div>
                            <div class="mgr-legend-item">
                                <span class="dot dot-absent" style="width:8px;height:8px;border-radius:50%;"></span>
                                <span>Absent</span>
                            </div>
                        </div>
                    </div>

                    <!-- Stacked Chart Area -->
                    <div class="mgr-stacked-chart-container">
                        <div class="mgr-y-axis">
                            <span>0</span>
                            <span>5</span>
                            <span>10</span>
                            <span>15</span>
                            <span>20</span>
                            <span>25</span>
                        </div>
                        <div class="mgr-bars-group" id="mgrBarsGroup">
                            <!-- Populated dynamically by manager-dashboard.js -->
                        </div>
                    </div>
                </div>

                <!-- Right: Performance Overview -->
                <div class="mgr-card">
                    <div class="mgr-card-header">
                        <div class="mgr-card-title-group">
                            <div class="mgr-card-header-icon" style="color:#7c3aed;">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                    <line x1="18" y1="20" x2="18" y2="10"></line>
                                    <line x1="12" y1="20" x2="12" y2="4"></line>
                                    <line x1="6" y1="20" x2="6" y2="14"></line>
                                </svg>
                            </div>
                            <div>
                                <h2 class="mgr-card-title">Performance Overview</h2>
                                <p class="mgr-card-subtitle">Team performance this quarter</p>
                            </div>
                        </div>

                        <!-- Quarter Selector -->
                        <div class="mgr-quarter-select-wrap">
                            <select id="mgrQuarterSelect" onchange="onQuarterChange(this.value)">
                                <option value="Q3 2025" selected>Q3 2025</option>
                                <option value="Q2 2025">Q2 2025</option>
                                <option value="Q1 2025">Q1 2025</option>
                            </select>
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" class="chevron">
                                <polyline points="6 9 12 15 18 9"></polyline>
                            </svg>
                        </div>
                    </div>

                    <div class="mgr-performance-body">
                        <!-- Donut Gauge Chart -->
                        <div class="mgr-donut-container">
                            <svg width="130" height="130" viewBox="0 0 130 130" class="mgr-donut-svg">
                                <!-- Background Track -->
                                <circle cx="65" cy="65" r="54" fill="none" stroke="#f1f5f9" stroke-width="12" />
                                <!-- Progress Arc (4.2 out of 5 = 84%) -->
                                <circle id="mgrDonutProgressCircle" cx="65" cy="65" r="54" fill="none" stroke="#6366f1" stroke-width="12"
                                        stroke-dasharray="339.3" stroke-dashoffset="54.3" stroke-linecap="round" style="transition: stroke-dashoffset 0.6s ease;" />
                            </svg>
                            <div class="mgr-donut-center-text">
                                <span class="mgr-donut-rating" id="mgrTeamRatingVal">4.2 / 5</span>
                                <span class="mgr-donut-sub">Team Rating</span>
                            </div>
                        </div>

                        <!-- 5 Competencies Progress Bars -->
                        <div class="mgr-competencies-list" id="mgrCompetenciesList">
                            <!-- Populated dynamically by manager-dashboard.js -->
                        </div>
                    </div>
                </div>

            </section>

            <!-- -------------------------------------------------
                 ROW 3: THREE COLUMNS (My Team, Approvals, Training)
            -------------------------------------------------- -->
            <section class="mgr-triple-grid">
                
                <!-- Column 1: My Team -->
                <div class="mgr-card" id="myTeamCard">
                    <div class="mgr-card-header">
                        <div class="mgr-card-title-group">
                            <div class="mgr-card-header-icon" style="color:#2563eb;">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                                    <circle cx="9" cy="7" r="4"></circle>
                                    <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                                    <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                                </svg>
                            </div>
                            <h2 class="mgr-card-title">My Team</h2>
                        </div>
                        <a href="manager-team.php" class="mgr-view-all-link">View All</a>
                    </div>

                    <div class="mgr-team-table-scroll">
                        <table class="mgr-team-table">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Role</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody id="mgrTeamTableBody">
                                <!-- Populated dynamically by manager-dashboard.js -->
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Column 2: Pending Approvals -->
                <div class="mgr-card" id="pendingApprovalsCard">
                    <div class="mgr-card-header">
                        <div class="mgr-card-title-group">
                            <div class="mgr-card-header-icon" style="color:#ea580c;">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="12" cy="12" r="10"></circle>
                                    <polyline points="12 6 12 12 16 14"></polyline>
                                </svg>
                            </div>
                            <h2 class="mgr-card-title">Pending Approvals</h2>
                        </div>
                        <a href="javascript:void(0)" class="mgr-view-all-link" onclick="showToast('Viewing all approvals queue')">View All</a>
                    </div>

                    <div class="mgr-approvals-list" id="mgrApprovalsList">
                        <!-- Populated dynamically by manager-dashboard.js -->
                    </div>
                </div>

                <!-- Column 3: Upcoming Training -->
                <div class="mgr-card">
                    <div class="mgr-card-header">
                        <div class="mgr-card-title-group">
                            <div class="mgr-card-header-icon" style="color:#7c3aed;">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M22 10v6M2 10l10-5 10 5-10 5z"></path>
                                    <path d="M6 12v5c3 3 9 3 12 0v-5"></path>
                                </svg>
                            </div>
                            <h2 class="mgr-card-title">Upcoming Training</h2>
                        </div>
                        <a href="javascript:void(0)" class="mgr-view-all-link" onclick="showToast('Viewing training schedule')">View All</a>
                    </div>

                    <div class="mgr-training-list" id="mgrTrainingList">
                        <!-- Populated dynamically by manager-dashboard.js -->
                    </div>
                </div>

            </section>

            <!-- -------------------------------------------------
                 ROW 4: BOTTOM BANNER (Team Strength)
            -------------------------------------------------- -->
            <section class="mgr-bottom-banner">
                <div class="banner-left">
                    <div class="banner-bullseye-icon">
                        <!-- Bullseye / Target SVG -->
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="10"></circle>
                            <circle cx="12" cy="12" r="6"></circle>
                            <circle cx="12" cy="12" r="2"></circle>
                        </svg>
                    </div>
                    <div class="banner-text-block">
                        <h3 class="banner-title">Build a stronger team today</h3>
                        <p class="banner-desc">Support your team, approve requests, and track progress — all in one place.</p>
                    </div>
                </div>

                <button type="button" class="banner-action-btn" onclick="document.getElementById('myTeamCard').scrollIntoView({behavior:'smooth'})">
                    <span>View Team Details</span>
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="5" y1="12" x2="19" y2="12"></line>
                        <polyline points="12 5 19 12 12 19"></polyline>
                    </svg>
                </button>

                <div class="banner-right-art">
                    <div class="banner-quote">Great Teams<br>Grow Here!</div>
                    <!-- Cartoon Team Illustration -->
                    <svg width="70" height="60" viewBox="0 0 100 80" fill="none">
                        <!-- Plant -->
                        <path d="M15 65 C15 50 25 45 25 35 C28 50 28 65 25 65 Z" fill="#10b981"/>
                        <circle cx="25" cy="65" r="4" fill="#047857"/>
                        <!-- Person at Laptop -->
                        <circle cx="70" cy="25" r="12" fill="#fcd34d"/>
                        <path d="M55 65 C55 45 85 45 85 65 Z" fill="#0d9488"/>
                        <!-- Laptop -->
                        <polygon points="45 55 65 55 60 40 40 40" fill="#1e293b"/>
                        <polygon points="42 55 68 55 70 58 40 58" fill="#475569"/>
                    </svg>
                </div>
            </section>

        </main>
    </div>
</div>

<!-- SCRIPTS -->
<script src="../../assets/js/theme.js?v=<?= time() ?>"></script>
<script src="../../assets/js/manager-dashboard.js?v=<?= time() ?>"></script>
</body>
</html>

<?php
// frontend/pages/manager/manager-performance.php
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Team Performance — Mindazzle Manager Portal</title>
    <meta name="description" content="Track your team's growth, recognise achievements, set goals, and review performance in Mindazzle Manager Portal.">

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

                <!-- Card 1: Average Team Rating -->
                <div class="mgr-kpi-card" id="kpiCardAvgRating">
                    <div class="mgr-kpi-inner">
                        <div class="mgr-kpi-icon-circle kpi-circle-peach">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/>
                            </svg>
                        </div>
                        <div class="mgr-kpi-text-block">
                            <span class="mgr-kpi-label">Average Team Rating</span>
                            <div class="mgr-kpi-value-row">
                                <span class="mgr-kpi-value" id="kpiAvgRatingVal">4.2 / 5</span>
                            </div>
                            <span class="mgr-trend-pill-green" id="kpiAvgRatingTrend" style="display:inline-flex;margin-top:2px;">↑ +0.3 <span style="font-weight:500;color:#64748b;font-size:10px;">vs last quarter</span></span>
                        </div>
                    </div>
                </div>

                <!-- Card 2: Goals Progress -->
                <div class="mgr-kpi-card" id="kpiCardGoalsProgress">
                    <div class="mgr-kpi-inner">
                        <div class="mgr-kpi-icon-circle kpi-circle-blue">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="12" cy="12" r="10"></circle>
                                <circle cx="12" cy="12" r="6"></circle>
                                <circle cx="12" cy="12" r="2"></circle>
                            </svg>
                        </div>
                        <div class="mgr-kpi-text-block">
                            <span class="mgr-kpi-label">Goals Progress</span>
                            <div class="mgr-kpi-value-row">
                                <span class="mgr-kpi-value" id="kpiGoalsProgressVal">78%</span>
                            </div>
                            <span class="mgr-trend-pill-green" id="kpiGoalsProgressTrend" style="display:inline-flex;margin-top:2px;">↑ +12% <span style="font-weight:500;color:#64748b;font-size:10px;">vs last quarter</span></span>
                        </div>
                    </div>
                </div>

                <!-- Card 3: High Performers -->
                <div class="mgr-kpi-card" id="kpiCardHighPerformers">
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
                            <span class="mgr-kpi-label">High Performers</span>
                            <div class="mgr-kpi-value-row">
                                <span class="mgr-kpi-value" id="kpiHighPerformersVal">4</span>
                            </div>
                            <span class="mgr-kpi-subtext" id="kpiHighPerformersSub">Out of 12 members</span>
                        </div>
                    </div>
                </div>

                <!-- Card 4: Reviews Completed -->
                <div class="mgr-kpi-card" id="kpiCardReviewsCompleted">
                    <div class="mgr-kpi-inner">
                        <div class="mgr-kpi-icon-circle kpi-circle-lavender">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                <line x1="18" y1="20" x2="18" y2="10"/>
                                <line x1="12" y1="20" x2="12" y2="4"/>
                                <line x1="6" y1="20" x2="6" y2="14"/>
                            </svg>
                        </div>
                        <div class="mgr-kpi-text-block">
                            <span class="mgr-kpi-label">Reviews Completed</span>
                            <div class="mgr-kpi-value-row">
                                <span class="mgr-kpi-value" id="kpiReviewsCompletedVal">8 / 12</span>
                            </div>
                            <span class="mgr-kpi-subtext" id="kpiReviewsCompletedSub">This quarter</span>
                        </div>
                    </div>
                </div>

            </section>


            <!-- -------------------------------------------------
                 ROW 2: 3 ANALYTICS CARDS (Trend, Distribution, Top Performers)
            -------------------------------------------------- -->
            <section class="mgr-perf-row2-grid">

                <!-- Card 1: Performance Trend (Line Chart) -->
                <div class="mgr-card" style="margin-bottom:0;">
                    <div class="mgr-card-header-flex">
                        <div class="mgr-card-header-left">
                            <div class="mgr-card-icon-badge purple">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                    <polyline points="22 12 18 12 15 21 9 3 6 12 2 12"></polyline>
                                </svg>
                            </div>
                            <div>
                                <h2 class="mgr-card-title" style="margin:0;font-size:15px;font-weight:800;color:#0f172a;">Performance Trend</h2>
                                <p class="mgr-card-subtitle" style="margin:2px 0 0;font-size:11.5px;color:#64748b;">Average team rating over time</p>
                            </div>
                        </div>

                        <!-- Timeframe Dropdown -->
                        <select id="perfTimeframeSelect" class="mgr-filter-select">
                            <option value="6M" selected>📅 Last 6 Months</option>
                            <option value="12M">📅 Last 12 Months</option>
                            <option value="YTD">📅 This Year</option>
                        </select>
                    </div>

                    <div class="mgr-trend-chart-container">
                        <!-- Floating 4.2 Aug 2025 Tooltip Badge -->
                        <div class="mgr-trend-badge" id="mgrTrendBadge">
                            <div class="mgr-trend-badge-val">4.2</div>
                            <div class="mgr-trend-badge-lbl">Aug 2025</div>
                        </div>

                        <div class="mgr-trend-chart-flex">
                            <div class="mgr-trend-y-axis">
                                <span>0</span>
                                <span>1</span>
                                <span>2</span>
                                <span>3</span>
                                <span>4</span>
                                <span>5</span>
                            </div>
                            <div class="mgr-trend-svg-wrap">
                                <svg class="mgr-trend-svg" id="mgrTrendSvg" preserveAspectRatio="none">
                                    <!-- Populated dynamically by manager-performance.js -->
                                </svg>
                            </div>
                        </div>

                        <div class="mgr-trend-x-axis" id="mgrTrendXAxis">
                            <span>Mar</span>
                            <span>Apr</span>
                            <span>May</span>
                            <span>Jun</span>
                            <span>Jul</span>
                            <span>Aug</span>
                        </div>
                    </div>
                </div>

                <!-- Card 2: Performance Distribution (Donut Chart) -->
                <div class="mgr-card" style="margin-bottom:0;">
                    <div class="mgr-card-header-flex">
                        <div class="mgr-card-header-left">
                            <div class="mgr-card-icon-badge purple">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="12" cy="12" r="10"></circle>
                                    <path d="M12 2a10 10 0 0 1 10 10"></path>
                                </svg>
                            </div>
                            <h2 class="mgr-card-title" style="margin:0;font-size:15px;font-weight:800;color:#0f172a;">Performance Distribution</h2>
                        </div>
                    </div>

                    <div class="mgr-perf-dist-body">
                        <div class="mgr-dist-donut-wrap">
                            <svg class="mgr-donut-svg" id="mgrDistDonutSvg" width="130" height="130" viewBox="0 0 130 130">
                                <!-- Populated dynamically by manager-performance.js -->
                            </svg>
                            <div class="mgr-dist-donut-text">
                                <span class="mgr-dist-donut-count" id="mgrDistTotalCount">12</span>
                                <span class="mgr-dist-donut-sub">Team Members</span>
                            </div>
                        </div>

                        <div class="mgr-dist-legend-list" id="mgrDistLegendList">
                            <!-- Populated dynamically by manager-performance.js -->
                        </div>
                    </div>
                </div>

                <!-- Card 3: Top Performers List -->
                <div class="mgr-card" style="margin-bottom:0;">
                    <div class="mgr-card-header-flex">
                        <div class="mgr-card-header-left">
                            <div class="mgr-card-icon-badge orange">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M19 5h-2V3H7v2H5c-1.1 0-2 .9-2 2v1c0 2.55 1.92 4.63 4.39 4.94.63 1.5 1.98 2.63 3.61 2.96V19H7v2h10v-2h-4v-3.1c1.63-.33 2.98-1.46 3.61-2.96C19.08 12.63 21 10.55 21 8V7c0-1.1-.9-2-2-2zM5 8V7h2v3.82C5.84 10.4 5 9.3 5 8zm14 0c0 1.3-.84 2.4-2 2.82V7h2v1z"/>
                                </svg>
                            </div>
                            <h2 class="mgr-card-title" style="margin:0;font-size:15px;font-weight:800;color:#0f172a;">Top Performers</h2>
                        </div>
                        <a href="javascript:void(0)" class="mgr-header-link" onclick="showToast('Viewing full top performers leaderboard')">View All</a>
                    </div>

                    <div class="mgr-top-performers-list" id="mgrTopPerformersList">
                        <!-- Populated dynamically by manager-performance.js -->
                    </div>
                </div>

            </section>


            <!-- -------------------------------------------------
                 ROW 3: 3 COLUMNS (Recent Reviews, Team Goals, Quick Actions)
            -------------------------------------------------- -->
            <section class="mgr-perf-row3-grid">

                <!-- Column 1: Recent Reviews Table -->
                <div class="mgr-card mgr-perf-table-card" style="margin-bottom:0;">
                    <div class="mgr-card-header-flex">
                        <div class="mgr-card-header-left">
                            <div class="mgr-card-icon-badge purple">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                    <polyline points="14 2 14 8 20 8"></polyline>
                                    <line x1="16" y1="13" x2="8" y2="13"></line>
                                    <line x1="16" y1="17" x2="8" y2="17"></line>
                                    <polyline points="10 9 9 9 8 9"></polyline>
                                </svg>
                            </div>
                            <h2 class="mgr-card-title" style="margin:0;font-size:15px;font-weight:800;color:#0f172a;">Recent Reviews</h2>
                        </div>
                    </div>

                    <div class="mgr-card-body mgr-perf-table-scroll" style="padding:0;">
                        <table class="mgr-table mgr-perf-table" style="margin:0;">
                            <thead>
                                <tr>
                                    <th>Employee</th>
                                    <th>Review Period</th>
                                    <th>Rating</th>
                                    <th>Status</th>
                                    <th style="text-align:right;">Action</th>
                                </tr>
                            </thead>
                            <tbody id="mgrReviewsTableBody">
                                <!-- Populated dynamically by manager-performance.js -->
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Column 2: Team Goals Table -->
                <div class="mgr-card mgr-perf-table-card" style="margin-bottom:0;">
                    <div class="mgr-card-header-flex">
                        <div class="mgr-card-header-left">
                            <div class="mgr-card-icon-badge orange">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="12" cy="12" r="10"></circle>
                                    <circle cx="12" cy="12" r="6"></circle>
                                    <circle cx="12" cy="12" r="2"></circle>
                                </svg>
                            </div>
                            <h2 class="mgr-card-title" style="margin:0;font-size:15px;font-weight:800;color:#0f172a;">Team Goals</h2>
                        </div>
                    </div>

                    <div class="mgr-card-body mgr-perf-table-scroll" style="padding:0;">
                        <table class="mgr-table mgr-perf-table" style="margin:0;">
                            <thead>
                                <tr>
                                    <th>Goal</th>
                                    <th>Progress</th>
                                    <th>Due Date</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody id="mgrGoalsTableBody">
                                <!-- Populated dynamically by manager-performance.js -->
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Column 3: Quick Actions (Vertical Chevrons) -->
                <div class="mgr-card" style="margin-bottom:0;">
                    <div class="mgr-card-header-flex">
                        <div class="mgr-card-header-left">
                            <div class="mgr-card-icon-badge orange">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M7 2v11h3v9l7-12h-4l4-8z"/>
                                </svg>
                            </div>
                            <h2 class="mgr-card-title" style="margin:0;font-size:15px;font-weight:800;color:#0f172a;">Quick Actions</h2>
                        </div>
                    </div>

                    <div class="mgr-action-chevron-list">
                        <!-- 1. Create Review -->
                        <button type="button" class="mgr-action-chevron-item" id="actionCreateReview">
                            <div class="mgr-chevron-left">
                                <div class="mgr-tile-icon purple" style="width:34px;height:34px;">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"></path>
                                        <rect x="8" y="2" width="8" height="4" rx="1" ry="1"></rect>
                                    </svg>
                                </div>
                                <div>
                                    <div class="mgr-tile-title">Create Review</div>
                                    <div class="mgr-tile-sub">Start a performance review</div>
                                </div>
                            </div>
                            <span class="mgr-chevron-arrow">›</span>
                        </button>

                        <!-- 2. Set Team Goal -->
                        <button type="button" class="mgr-action-chevron-item" id="actionSetGoal">
                            <div class="mgr-chevron-left">
                                <div class="mgr-tile-icon orange" style="width:34px;height:34px;">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                        <circle cx="12" cy="12" r="10"></circle>
                                        <circle cx="12" cy="12" r="6"></circle>
                                        <circle cx="12" cy="12" r="2"></circle>
                                    </svg>
                                </div>
                                <div>
                                    <div class="mgr-tile-title">Set Team Goal</div>
                                    <div class="mgr-tile-sub">Define a new team goal</div>
                                </div>
                            </div>
                            <span class="mgr-chevron-arrow">›</span>
                        </button>

                        <!-- 3. Give Feedback -->
                        <button type="button" class="mgr-action-chevron-item" id="actionGiveFeedback">
                            <div class="mgr-chevron-left">
                                <div class="mgr-tile-icon green" style="width:34px;height:34px;">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <div class="mgr-tile-title">Give Feedback</div>
                                    <div class="mgr-tile-sub">Share feedback with team member</div>
                                </div>
                            </div>
                            <span class="mgr-chevron-arrow">›</span>
                        </button>

                        <!-- 4. View Reports -->
                        <button type="button" class="mgr-action-chevron-item" id="actionViewReports">
                            <div class="mgr-chevron-left">
                                <div class="mgr-tile-icon blue" style="width:34px;height:34px;">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                        <line x1="18" y1="20" x2="18" y2="10"/>
                                        <line x1="12" y1="20" x2="12" y2="4"/>
                                        <line x1="6" y1="20" x2="6" y2="14"/>
                                    </svg>
                                </div>
                                <div>
                                    <div class="mgr-tile-title">View Reports</div>
                                    <div class="mgr-tile-sub">Detailed performance insights</div>
                                </div>
                            </div>
                            <span class="mgr-chevron-arrow">›</span>
                        </button>
                    </div>
                </div>

            </section>


            <!-- -------------------------------------------------
                 ROW 4: BOTTOM BANNER
            -------------------------------------------------- -->
            <section class="mgr-perf-bottom-banner">
                <!-- Left Info with Sprout Icon -->
                <div class="mgr-banner-info-wrap">
                    <div class="mgr-banner-sprout-icon">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M12 10a6 6 0 0 0-6-6H3v2a6 6 0 0 0 6 6h3"></path>
                            <path d="M12 14a6 6 0 0 1 6-6h3v2a6 6 0 0 1-6 6h-3"></path>
                            <line x1="12" y1="10" x2="12" y2="21"></line>
                        </svg>
                    </div>
                    <div>
                        <h3 style="margin:0 0 3px;font-size:16px;font-weight:800;color:#2e1065;">Invest in your team's growth</h3>
                        <p style="margin:0;font-size:12.5px;color:#6b21a8;">Regular feedback and clear goals lead to higher performance and stronger teams.</p>
                    </div>
                </div>

                <!-- Center CTA Button -->
                <button type="button" class="mgr-btn-purple-pill" id="bottomSetGoalBtn">
                    <span>Set a New Goal</span>
                    <span style="font-size:15px;">→</span>
                </button>

                <!-- Right Art with Person on Laptop and Quote -->
                <div style="display:flex;align-items:center;gap:18px;">
                    <!-- Illustration -->
                    <svg width="150" height="90" viewBox="0 0 200 120" fill="none">
                        <!-- Chart in background -->
                        <rect x="25" y="65" width="10" height="35" rx="2" fill="#c4b5fd"/>
                        <rect x="40" y="45" width="10" height="55" rx="2" fill="#a78bfa"/>
                        <rect x="55" y="25" width="10" height="75" rx="2" fill="#8b5cf6"/>
                        <!-- Person at desk with laptop -->
                        <!-- Potted plant right -->
                        <ellipse cx="170" cy="98" rx="8" ry="4" fill="#cbd5e1"/>
                        <path d="M170 95 C175 75 185 65 180 50" stroke="#10b981" stroke-width="2" stroke-linecap="round"/>
                        <ellipse cx="180" cy="55" rx="5" ry="9" transform="rotate(20 180 55)" fill="#34d399"/>
                        <!-- Head -->
                        <circle cx="120" cy="42" r="11" fill="#fed7aa"/>
                        <path d="M110 38 C110 30 118 28 126 28 C132 28 133 33 131 39 Z" fill="#1e293b"/>
                        <!-- Body (Blue shirt) -->
                        <path d="M106 58 C106 54 112 53 120 53 C128 53 134 54 134 58 L138 95 L102 95 Z" fill="#3b82f6"/>
                        <!-- Desk surface -->
                        <line x1="80" y1="98" x2="185" y2="98" stroke="#94a3b8" stroke-width="2.5" stroke-linecap="round"/>
                        <!-- Laptop -->
                        <polygon points="90 75 118 75 114 96 86 96" fill="#0f172a"/>
                        <polygon points="84 96 122 96 126 99 80 99" fill="#475569"/>
                    </svg>

                    <!-- Quote -->
                    <div style="font-size:12.5px;color:#581c87;font-style:italic;max-width:210px;line-height:1.35;text-align:right;">
                        "Great teams don't happen by chance, they grow with guidance."
                    </div>
                </div>
            </section>

        </main>

    </div>

</div>

<!-- =========================================================
     MODALS
========================================================== -->

<!-- 1. REVIEW DETAIL MODAL -->
<div class="mgr-modal-backdrop" id="mgrReviewDetailModal">
    <div class="mgr-modal-content">
        <div class="mgr-modal-header">
            <h3 class="mgr-modal-title">Performance Review Details</h3>
            <button class="mgr-modal-close" type="button" aria-label="Close modal">&times;</button>
        </div>
        <div id="mgrReviewModalBody">
            <!-- Populated dynamically -->
        </div>
        <div class="mgr-modal-footer">
            <button type="button" class="mgr-btn-view" onclick="closeModals()">Close</button>
        </div>
    </div>
</div>

<!-- 2. CREATE REVIEW MODAL -->
<div class="mgr-modal-backdrop" id="mgrCreateReviewModal">
    <div class="mgr-modal-content">
        <div class="mgr-modal-header">
            <h3 class="mgr-modal-title">Start Performance Review</h3>
            <button class="mgr-modal-close" type="button" aria-label="Close modal">&times;</button>
        </div>
        <form id="mgrCreateReviewForm">
            <div class="mgr-form-group">
                <label class="mgr-form-label" for="reviewEmpNameInput">Employee Name <span class="req">*</span></label>
                <input type="text" id="reviewEmpNameInput" class="mgr-form-input" placeholder="e.g. Priya Sharma" required />
            </div>
            <div class="mgr-form-group">
                <label class="mgr-form-label" for="reviewPeriodInput">Review Period</label>
                <input type="text" id="reviewPeriodInput" class="mgr-form-input" value="Q3 2025" />
            </div>
            <div class="mgr-form-group">
                <label class="mgr-form-label" for="reviewRatingInput">Preliminary Rating (1.0 – 5.0)</label>
                <input type="number" step="0.1" min="1.0" max="5.0" id="reviewRatingInput" class="mgr-form-input" value="4.2" />
            </div>
            <div class="mgr-form-group">
                <label class="mgr-form-label" for="reviewFeedbackInput">Manager Notes / Feedback</label>
                <textarea id="reviewFeedbackInput" class="mgr-form-textarea" placeholder="Add observations on quarterly goals and accomplishments..."></textarea>
            </div>
            <div class="mgr-modal-footer">
                <button type="button" class="mgr-btn-view" onclick="closeModals()">Cancel</button>
                <button type="submit" class="mgr-btn-view" style="background:#6366f1;color:#ffffff;border-color:#6366f1;">Start Review</button>
            </div>
        </form>
    </div>
</div>

<!-- 3. SET TEAM GOAL MODAL -->
<div class="mgr-modal-backdrop" id="mgrSetGoalModal">
    <div class="mgr-modal-content">
        <div class="mgr-modal-header">
            <h3 class="mgr-modal-title">Define New Team Goal</h3>
            <button class="mgr-modal-close" type="button" aria-label="Close modal">&times;</button>
        </div>
        <form id="mgrSetGoalForm">
            <div class="mgr-form-group">
                <label class="mgr-form-label" for="goalTitleInput">Goal Title <span class="req">*</span></label>
                <input type="text" id="goalTitleInput" class="mgr-form-input" placeholder="e.g. Complete mobile app sprint" required />
            </div>
            <div class="mgr-form-group">
                <label class="mgr-form-label" for="goalProgressInput">Current Progress (%)</label>
                <input type="number" min="0" max="100" id="goalProgressInput" class="mgr-form-input" value="50" />
            </div>
            <div class="mgr-form-group">
                <label class="mgr-form-label" for="goalDueDateInput">Target Due Date</label>
                <input type="text" id="goalDueDateInput" class="mgr-form-input" value="15 Nov" placeholder="e.g. 15 Nov" />
            </div>
            <div class="mgr-form-group">
                <label class="mgr-form-label" for="goalStatusInput">Status</label>
                <select id="goalStatusInput" class="mgr-form-select">
                    <option value="IN_PROGRESS">In Progress</option>
                    <option value="ON_TRACK">On Track</option>
                    <option value="AT_RISK">At Risk</option>
                    <option value="COMPLETED">Completed</option>
                </select>
            </div>
            <div class="mgr-modal-footer">
                <button type="button" class="mgr-btn-view" onclick="closeModals()">Cancel</button>
                <button type="submit" class="mgr-btn-view" style="background:#6366f1;color:#ffffff;border-color:#6366f1;">Save Goal</button>
            </div>
        </form>
    </div>
</div>

<!-- 4. GIVE FEEDBACK MODAL -->
<div class="mgr-modal-backdrop" id="mgrFeedbackModal">
    <div class="mgr-modal-content">
        <div class="mgr-modal-header">
            <h3 class="mgr-modal-title">Share Team Feedback</h3>
            <button class="mgr-modal-close" type="button" aria-label="Close modal">&times;</button>
        </div>
        <form id="mgrFeedbackForm">
            <div class="mgr-form-group">
                <label class="mgr-form-label" for="feedbackEmpInput">Select Employee</label>
                <select id="feedbackEmpInput" class="mgr-form-select">
                    <option value="Priya Sharma">Priya Sharma (UI/UX Designer)</option>
                    <option value="Rohit Kulkarni">Rohit Kulkarni (Frontend Developer)</option>
                    <option value="Sneha Iyer">Sneha Iyer (HR Executive)</option>
                    <option value="Rohan Verma">Rohan Verma (Backend Developer)</option>
                    <option value="Neha Tiwari">Neha Tiwari (Content Writer)</option>
                    <option value="Aditya Kapoor">Aditya Kapoor (QA Engineer)</option>
                </select>
            </div>
            <div class="mgr-form-group">
                <label class="mgr-form-label" for="feedbackTextInput">Feedback Message</label>
                <textarea id="feedbackTextInput" class="mgr-form-textarea" placeholder="Share positive recognition or constructive guidance..." required></textarea>
            </div>
            <div class="mgr-modal-footer">
                <button type="button" class="mgr-btn-view" onclick="closeModals()">Cancel</button>
                <button type="submit" class="mgr-btn-view" style="background:#10b981;color:#ffffff;border-color:#10b981;">Send Feedback</button>
            </div>
        </form>
    </div>
</div>

<!-- SCRIPTS -->
<script src="../../assets/js/theme.js?v=<?= time() ?>"></script>
<script src="../../assets/js/manager-performance.js?v=<?= time() ?>"></script>

</body>
</html>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mindazzle — Performance &amp; Training</title>
    <meta name="description" content="Track your progress. Build your skills. Be your best. Personal employee performance ratings, competencies, review history, goals, and training courses.">

    <!-- GOOGLE FONTS (Plus Jakarta Sans) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- STYLES -->
    <link rel="stylesheet" href="../../assets/css/employee-sidebar.css?v=<?= time() ?>">
    <link rel="stylesheet" href="../../assets/css/employee-performance.css?v=<?= time() ?>">
</head>

<body>

<!-- LOADING OVERLAY -->
<div class="emp-loading-overlay" id="empLoadingOverlay">
    <div class="emp-spinner"></div>
</div>

<!-- TOAST NOTIFICATIONS -->
<div class="emp-toast-container" id="empToastContainer"></div>

<!-- SVG GRADIENT DEFINITIONS -->
<svg style="width:0;height:0;position:absolute;" aria-hidden="true" focusable="false">
    <defs>
        <linearGradient id="empPurpleGradient" x1="0%" y1="0%" x2="100%" y2="100%">
            <stop offset="0%" stop-color="#818cf8" />
            <stop offset="100%" stop-color="#4f46e5" />
        </linearGradient>
    </defs>
</svg>

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
             PAGE CONTENT BODY
        ====================================================== -->
        <main class="emp-content-body">

            <!-- === ROW 1: 4 STAT SUMMARY CARDS === -->
            <section class="emp-kpi-grid">

                <!-- 1. Current Performance Rating -->
                <div class="emp-kpi-card">
                    <div class="emp-kpi-icon-wrap yellow">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                            <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>
                        </svg>
                    </div>
                    <div class="emp-kpi-details">
                        <div class="emp-kpi-top-row">
                            <div class="emp-kpi-value" id="kpiRatingVal">4.3 / 5</div>
                            <div class="emp-kpi-trend" id="kpiRatingTrend">
                                <span style="color:#10b981; font-weight:700;">↑ 0.3</span>
                                <span class="muted-label">vs last review</span>
                            </div>
                        </div>
                        <span class="emp-kpi-label">Current Performance Rating</span>
                    </div>
                </div>

                <!-- 2. Goals in Progress -->
                <div class="emp-kpi-card">
                    <div class="emp-kpi-icon-wrap pink">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="10"></circle>
                            <circle cx="12" cy="12" r="6"></circle>
                            <circle cx="12" cy="12" r="2"></circle>
                        </svg>
                    </div>
                    <div class="emp-kpi-details">
                        <div class="emp-kpi-value" id="kpiGoalsVal">3 / 4</div>
                        <div class="emp-kpi-progress-wrap">
                            <div class="emp-kpi-track">
                                <div class="emp-kpi-fill" id="kpiGoalsBar" style="width: 75%;"></div>
                            </div>
                            <span class="emp-kpi-sub-text" id="kpiGoalsProgress">75% completion</span>
                        </div>
                        <span class="emp-kpi-label">Goals in Progress</span>
                    </div>
                </div>

                <!-- 3. Training Courses -->
                <div class="emp-kpi-card">
                    <div class="emp-kpi-icon-wrap blue">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M22 10v6M2 10l10-5 10 5-10 5z"></path>
                            <path d="M6 12v5c3 3 9 3 12 0v-5"></path>
                        </svg>
                    </div>
                    <div class="emp-kpi-details">
                        <div class="emp-kpi-value" id="kpiTrainingVal">3</div>
                        <span class="emp-kpi-sub-text" id="kpiTrainingSub" style="margin-top:6px;">2 in progress</span>
                        <span class="emp-kpi-label">Training Courses</span>
                    </div>
                </div>

                <!-- 4. Certificates Earned -->
                <div class="emp-kpi-card">
                    <div class="emp-kpi-icon-wrap green">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="8" r="7"></circle>
                            <polyline points="8.21 13.89 7 23 12 20 17 23 15.79 13.88"></polyline>
                        </svg>
                    </div>
                    <div class="emp-kpi-details">
                        <div class="emp-kpi-value" id="kpiCertsVal">2</div>
                        <span class="emp-kpi-sub-text" id="kpiCertsSub" style="margin-top:6px;">This Year</span>
                        <span class="emp-kpi-label">Certificates Earned</span>
                    </div>
                </div>

            </section>


            <!-- === ROW 2: PERFORMANCE OVERVIEW & REVIEW HISTORY === -->
            <section class="emp-row-two-col">

                <!-- Performance Overview -->
                <div class="emp-panel-card">
                    <div class="emp-panel-header">
                        <div class="emp-panel-title-wrap">
                            <span class="emp-panel-icon">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                    <line x1="18" y1="20" x2="18" y2="10"></line>
                                    <line x1="12" y1="20" x2="12" y2="4"></line>
                                    <line x1="6" y1="20" x2="6" y2="14"></line>
                                </svg>
                            </span>
                            <h2 class="emp-panel-title">Performance Overview</h2>
                        </div>

                        <!-- Dropdown -->
                        <div class="emp-select-wrap">
                            <select id="reviewPeriodSelect" class="emp-period-select">
                                <option value="1">Last Review (Jul 2026)</option>
                                <option value="2">Last Review (Jan 2026)</option>
                                <option value="3">Last Review (Jul 2025)</option>
                                <option value="4">Last Review (Jan 2025)</option>
                            </select>
                            <svg class="emp-select-chevron" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                <polyline points="6 9 12 15 18 9"></polyline>
                            </svg>
                        </div>
                    </div>

                    <div class="emp-overview-content">
                        <!-- Left: Circular Donut Score -->
                        <div class="emp-radial-gauge-box">
                            <svg class="emp-radial-svg" viewBox="0 0 150 150">
                                <circle class="emp-radial-track" cx="75" cy="75" r="65" />
                                <circle class="emp-radial-meter" id="radialMeterCircle" cx="75" cy="75" r="65" stroke-dasharray="408.4" stroke-dashoffset="57" />
                            </svg>
                            <div class="emp-radial-center-text">
                                <span class="emp-radial-score" id="radialScoreText">4.3 / 5</span>
                                <span class="emp-radial-label">Overall Rating</span>
                            </div>
                        </div>

                        <!-- Right: Competency Progress Bars -->
                        <div class="emp-competencies-list" id="competenciesList">
                            <!-- Job Knowledge -->
                            <div class="emp-comp-row">
                                <span class="emp-comp-name">Job Knowledge</span>
                                <div class="emp-comp-bar-track">
                                    <div class="emp-comp-bar-fill purple" style="width: 90%;"></div>
                                </div>
                                <span class="emp-comp-score">4.5</span>
                            </div>
                            <!-- Quality of Work -->
                            <div class="emp-comp-row">
                                <span class="emp-comp-name">Quality of Work</span>
                                <div class="emp-comp-bar-track">
                                    <div class="emp-comp-bar-fill blue" style="width: 84%;"></div>
                                </div>
                                <span class="emp-comp-score">4.2</span>
                            </div>
                            <!-- Communication -->
                            <div class="emp-comp-row">
                                <span class="emp-comp-name">Communication</span>
                                <div class="emp-comp-bar-track">
                                    <div class="emp-comp-bar-fill cyan" style="width: 86%;"></div>
                                </div>
                                <span class="emp-comp-score">4.3</span>
                            </div>
                            <!-- Teamwork -->
                            <div class="emp-comp-row">
                                <span class="emp-comp-name">Teamwork</span>
                                <div class="emp-comp-bar-track">
                                    <div class="emp-comp-bar-fill orange" style="width: 88%;"></div>
                                </div>
                                <span class="emp-comp-score">4.4</span>
                            </div>
                            <!-- Initiative -->
                            <div class="emp-comp-row">
                                <span class="emp-comp-name">Initiative</span>
                                <div class="emp-comp-bar-track">
                                    <div class="emp-comp-bar-fill pink" style="width: 80%;"></div>
                                </div>
                                <span class="emp-comp-score">4.0</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Review History -->
                <div class="emp-panel-card">
                    <div class="emp-panel-header">
                        <div class="emp-panel-title-wrap">
                            <span class="emp-panel-icon">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                    <polyline points="14 2 14 8 20 8"></polyline>
                                    <line x1="16" y1="13" x2="8" y2="13"></line>
                                    <line x1="16" y1="17" x2="8" y2="17"></line>
                                    <polyline points="10 9 9 9 8 9"></polyline>
                                </svg>
                            </span>
                            <h2 class="emp-panel-title">Review History</h2>
                        </div>
                        <a href="javascript:void(0)" class="emp-link-action" onclick="showToast('Showing all review cycles.')">View All</a>
                    </div>

                    <div class="emp-table-wrapper">
                        <table class="emp-clean-table">
                            <thead>
                                <tr>
                                    <th>Review Period</th>
                                    <th>Rating</th>
                                    <th>Reviewer</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody id="reviewHistoryTableBody">
                                <tr>
                                    <td style="font-weight:700; color:#0f172a;">Jul 2026</td>
                                    <td style="font-weight:700; color:#334155;">4.3 / 5</td>
                                    <td>
                                        <div class="emp-reviewer-cell">
                                            <div class="emp-reviewer-avatar">
                                                <img src="../../assets/images/attendance-widget.jpg" alt="Rahul Mehta">
                                            </div>
                                            <div class="emp-reviewer-info">
                                                <span class="emp-reviewer-name">Rahul Mehta</span>
                                                <span class="emp-reviewer-role">Engineering Manager</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td><span class="emp-pill-badge green">Completed</span></td>
                                    <td><button type="button" class="emp-btn-outlined-sm" onclick="openReviewModal(1)">View</button></td>
                                </tr>
                                <tr>
                                    <td style="font-weight:700; color:#0f172a;">Jan 2026</td>
                                    <td style="font-weight:700; color:#334155;">4.0 / 5</td>
                                    <td>
                                        <div class="emp-reviewer-cell">
                                            <div class="emp-reviewer-avatar">
                                                <img src="../../assets/images/attendance-widget.jpg" alt="Rahul Mehta">
                                            </div>
                                            <div class="emp-reviewer-info">
                                                <span class="emp-reviewer-name">Rahul Mehta</span>
                                                <span class="emp-reviewer-role">Engineering Manager</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td><span class="emp-pill-badge green">Completed</span></td>
                                    <td><button type="button" class="emp-btn-outlined-sm" onclick="openReviewModal(2)">View</button></td>
                                </tr>
                                <tr>
                                    <td style="font-weight:700; color:#0f172a;">Jul 2025</td>
                                    <td style="font-weight:700; color:#334155;">3.8 / 5</td>
                                    <td>
                                        <div class="emp-reviewer-cell">
                                            <div class="emp-reviewer-avatar">
                                                <img src="../../assets/images/priya-avatar.jpg" alt="Sneha Iyer">
                                            </div>
                                            <div class="emp-reviewer-info">
                                                <span class="emp-reviewer-name">Sneha Iyer</span>
                                                <span class="emp-reviewer-role">HR Manager</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td><span class="emp-pill-badge green">Completed</span></td>
                                    <td><button type="button" class="emp-btn-outlined-sm" onclick="openReviewModal(3)">View</button></td>
                                </tr>
                                <tr>
                                    <td style="font-weight:700; color:#0f172a;">Jan 2025</td>
                                    <td style="font-weight:700; color:#334155;">3.6 / 5</td>
                                    <td>
                                        <div class="emp-reviewer-cell">
                                            <div class="emp-reviewer-avatar">
                                                <img src="../../assets/images/priya-avatar.jpg" alt="Sneha Iyer">
                                            </div>
                                            <div class="emp-reviewer-info">
                                                <span class="emp-reviewer-name">Sneha Iyer</span>
                                                <span class="emp-reviewer-role">HR Manager</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td><span class="emp-pill-badge green">Completed</span></td>
                                    <td><button type="button" class="emp-btn-outlined-sm" onclick="openReviewModal(4)">View</button></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

            </section>


            <!-- === ROW 3: MY GOALS & TRAINING PROGRESS === -->
            <section class="emp-row-two-col">

                <!-- My Goals -->
                <div class="emp-panel-card">
                    <div class="emp-panel-header">
                        <div class="emp-panel-title-wrap">
                            <span class="emp-panel-icon">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                                    <circle cx="12" cy="12" r="10"></circle>
                                    <circle cx="12" cy="12" r="6"></circle>
                                    <circle cx="12" cy="12" r="2"></circle>
                                </svg>
                            </span>
                            <h2 class="emp-panel-title">My Goals</h2>
                        </div>
                        <div style="display:flex; align-items:center; gap:12px;">
                            <a href="javascript:void(0)" class="emp-link-action" onclick="openAddGoalModal()">+ New Goal</a>
                            <a href="javascript:void(0)" class="emp-link-action" onclick="showToast('Viewing all goals.')">View All</a>
                        </div>
                    </div>

                    <div class="emp-table-wrapper">
                        <table class="emp-clean-table">
                            <thead>
                                <tr>
                                    <th class="emp-td-idx">#</th>
                                    <th>Goal</th>
                                    <th>Start Date</th>
                                    <th>Target Date</th>
                                    <th>Progress</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody id="goalsTableBody">
                                <tr>
                                    <td class="emp-td-idx">1</td>
                                    <td style="font-weight:700; color:#0f172a;">UI Redesign for Client Portal</td>
                                    <td style="color:#64748b; font-size:12px;">01 Jul 2026</td>
                                    <td style="color:#64748b; font-size:12px;">30 Sep 2026</td>
                                    <td>
                                        <div class="emp-td-progress">
                                            <div class="emp-table-prog-track">
                                                <div class="emp-table-prog-fill" style="width: 85%;"></div>
                                            </div>
                                            <span class="emp-prog-num">85%</span>
                                        </div>
                                    </td>
                                    <td><span class="emp-pill-badge green">On Track</span></td>
                                </tr>
                                <tr>
                                    <td class="emp-td-idx">2</td>
                                    <td style="font-weight:700; color:#0f172a;">Improve Design System</td>
                                    <td style="color:#64748b; font-size:12px;">01 Jul 2026</td>
                                    <td style="color:#64748b; font-size:12px;">30 Oct 2026</td>
                                    <td>
                                        <div class="emp-td-progress">
                                            <div class="emp-table-prog-track">
                                                <div class="emp-table-prog-fill" style="width: 60%;"></div>
                                            </div>
                                            <span class="emp-prog-num">60%</span>
                                        </div>
                                    </td>
                                    <td><span class="emp-pill-badge blue">In Progress</span></td>
                                </tr>
                                <tr>
                                    <td class="emp-td-idx">3</td>
                                    <td style="font-weight:700; color:#0f172a;">Complete Advanced Figma Course</td>
                                    <td style="color:#64748b; font-size:12px;">15 Aug 2026</td>
                                    <td style="color:#64748b; font-size:12px;">15 Sep 2026</td>
                                    <td>
                                        <div class="emp-td-progress">
                                            <div class="emp-table-prog-track">
                                                <div class="emp-table-prog-fill" style="width: 100%;"></div>
                                            </div>
                                            <span class="emp-prog-num">100%</span>
                                        </div>
                                    </td>
                                    <td><span class="emp-pill-badge green">Completed</span></td>
                                </tr>
                                <tr>
                                    <td class="emp-td-idx">4</td>
                                    <td style="font-weight:700; color:#0f172a;">Support 2 Product Releases</td>
                                    <td style="color:#64748b; font-size:12px;">01 Sep 2026</td>
                                    <td style="color:#64748b; font-size:12px;">31 Dec 2026</td>
                                    <td>
                                        <div class="emp-td-progress">
                                            <div class="emp-table-prog-track">
                                                <div class="emp-table-prog-fill" style="width: 25%;"></div>
                                            </div>
                                            <span class="emp-prog-num">25%</span>
                                        </div>
                                    </td>
                                    <td><span class="emp-pill-badge amber">At Risk</span></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Training Progress -->
                <div class="emp-panel-card">
                    <div class="emp-panel-header">
                        <div class="emp-panel-title-wrap">
                            <span class="emp-panel-icon">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                                    <path d="M22 10v6M2 10l10-5 10 5-10 5z"></path>
                                    <path d="M6 12v5c3 3 9 3 12 0v-5"></path>
                                </svg>
                            </span>
                            <h2 class="emp-panel-title">Training Progress</h2>
                        </div>
                        <a href="javascript:void(0)" class="emp-link-action" onclick="showToast('Viewing all enrolled training programs.')">View All</a>
                    </div>

                    <div class="emp-table-wrapper">
                        <table class="emp-clean-table">
                            <thead>
                                <tr>
                                    <th class="emp-td-idx">#</th>
                                    <th>Course Name</th>
                                    <th>Provider</th>
                                    <th>Progress</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody id="trainingTableBody">
                                <tr>
                                    <td class="emp-td-idx">1</td>
                                    <td style="font-weight:700; color:#0f172a;">Advanced Figma</td>
                                    <td style="color:#64748b; font-size:12px;">Mindazzle Academy</td>
                                    <td>
                                        <div class="emp-td-progress">
                                            <div class="emp-table-prog-track">
                                                <div class="emp-table-prog-fill" style="width: 80%;"></div>
                                            </div>
                                            <span class="emp-prog-num">80%</span>
                                        </div>
                                    </td>
                                    <td><span class="emp-pill-badge blue">In Progress</span></td>
                                    <td><button type="button" class="emp-btn-outlined-sm" onclick="openCourseModal(1, 'Advanced Figma', 80)">Continue</button></td>
                                </tr>
                                <tr>
                                    <td class="emp-td-idx">2</td>
                                    <td style="font-weight:700; color:#0f172a;">UI/UX Best Practices</td>
                                    <td style="color:#64748b; font-size:12px;">Coursera</td>
                                    <td>
                                        <div class="emp-td-progress">
                                            <div class="emp-table-prog-track">
                                                <div class="emp-table-prog-fill" style="width: 60%;"></div>
                                            </div>
                                            <span class="emp-prog-num">60%</span>
                                        </div>
                                    </td>
                                    <td><span class="emp-pill-badge blue">In Progress</span></td>
                                    <td><button type="button" class="emp-btn-outlined-sm" onclick="openCourseModal(2, 'UI/UX Best Practices', 60)">Continue</button></td>
                                </tr>
                                <tr>
                                    <td class="emp-td-idx">3</td>
                                    <td style="font-weight:700; color:#0f172a;">Agile Fundamentals</td>
                                    <td style="color:#64748b; font-size:12px;">LinkedIn Learning</td>
                                    <td>
                                        <div class="emp-td-progress">
                                            <div class="emp-table-prog-track">
                                                <div class="emp-table-prog-fill" style="width: 100%;"></div>
                                            </div>
                                            <span class="emp-prog-num">100%</span>
                                        </div>
                                    </td>
                                    <td><span class="emp-pill-badge green">Completed</span></td>
                                    <td><button type="button" class="emp-btn-outlined-sm" onclick="openCertificateModal(101, 'Agile Fundamentals', 'CERT-2026-AF-998')">View Cert</button></td>
                                </tr>
                                <tr>
                                    <td class="emp-td-idx">4</td>
                                    <td style="font-weight:700; color:#0f172a;">Design Leadership</td>
                                    <td style="color:#64748b; font-size:12px;">Udemy</td>
                                    <td>
                                        <div class="emp-td-progress">
                                            <div class="emp-table-prog-track">
                                                <div class="emp-table-prog-fill" style="width: 0%;"></div>
                                            </div>
                                            <span class="emp-prog-num">0%</span>
                                        </div>
                                    </td>
                                    <td><span class="emp-pill-badge amber">Not Started</span></td>
                                    <td><button type="button" class="emp-btn-outlined-sm" onclick="openCourseModal(4, 'Design Leadership', 0)">Start</button></td>
                                </tr>
                                <tr>
                                    <td class="emp-td-idx">5</td>
                                    <td style="font-weight:700; color:#0f172a;">Product Management</td>
                                    <td style="color:#64748b; font-size:12px;">Mindazzle Academy</td>
                                    <td>
                                        <div class="emp-td-progress">
                                            <div class="emp-table-prog-track">
                                                <div class="emp-table-prog-fill" style="width: 40%;"></div>
                                            </div>
                                            <span class="emp-prog-num">40%</span>
                                        </div>
                                    </td>
                                    <td><span class="emp-pill-badge blue">In Progress</span></td>
                                    <td><button type="button" class="emp-btn-outlined-sm" onclick="openCourseModal(5, 'Product Management', 40)">Continue</button></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

            </section>


            <!-- === ROW 4: RECOMMENDED TRAINING & MOTIVATIONAL BANNER === -->
            <section class="emp-row-bottom">

                <!-- Recommended Training -->
                <div class="emp-panel-card">
                    <div class="emp-panel-header">
                        <div class="emp-panel-title-wrap">
                            <span class="emp-panel-icon" style="color: #f59e0b;">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                                    <path d="M9 18h6"></path>
                                    <path d="M10 22h4"></path>
                                    <path d="M12 2a7 7 0 0 0-7 7c0 2.5 1.5 4.5 3 6h8c1.5-1.5 3-3.5 3-6a7 7 0 0 0-7-7z"></path>
                                </svg>
                            </span>
                            <h2 class="emp-panel-title">Recommended Training</h2>
                        </div>
                        <a href="javascript:void(0)" class="emp-link-action" onclick="showToast('Browse all catalog programs.')">View All</a>
                    </div>

                    <div class="emp-rec-courses-grid" id="recommendedCoursesGrid">
                        <!-- Card 1: Advanced Figma -->
                        <div class="emp-rec-card">
                            <div>
                                <div class="emp-rec-top">
                                    <div class="emp-rec-icon figma">
                                        <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M8 2h4a4 4 0 0 1 4 4 4 4 0 0 1-4 4H8V2zm0 8h4a4 4 0 0 1 4 4 4 4 0 0 1-4 4H8v-8zm-4 4a4 4 0 0 1 4-4v8a4 4 0 0 1-4-4zm4-12a4 4 0 0 1 4 4v4H8V2z"/></svg>
                                    </div>
                                    <div class="emp-rec-card-meta">
                                        <h4 title="Advanced Figma">Advanced Figma</h4>
                                        <p>Mindazzle Academy</p>
                                    </div>
                                </div>
                                <div class="emp-rec-info-row">
                                    <span><span class="emp-rec-star">★</span> 4.8</span>
                                    <span>
                                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                                        2h 30m
                                    </span>
                                </div>
                            </div>
                            <button type="button" class="emp-btn-enroll enrolled" disabled onclick="handleEnroll(1, 'Advanced Figma', this)">Enroll</button>
                        </div>

                        <!-- Card 2: Effective Communication -->
                        <div class="emp-rec-card">
                            <div>
                                <div class="emp-rec-top">
                                    <div class="emp-rec-icon communication">
                                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                                    </div>
                                    <div class="emp-rec-card-meta">
                                        <h4 title="Effective Communication">Effective Communication</h4>
                                        <p>Coursera</p>
                                    </div>
                                </div>
                                <div class="emp-rec-info-row">
                                    <span><span class="emp-rec-star">★</span> 4.7</span>
                                    <span>
                                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                                        3h 15m
                                    </span>
                                </div>
                            </div>
                            <button type="button" class="emp-btn-enroll" onclick="handleEnroll(6, 'Effective Communication', this)">Enroll</button>
                        </div>

                        <!-- Card 3: Project Management -->
                        <div class="emp-rec-card">
                            <div>
                                <div class="emp-rec-top">
                                    <div class="emp-rec-icon project">
                                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M8 12h8"/><path d="M12 8v8"/></svg>
                                    </div>
                                    <div class="emp-rec-card-meta">
                                        <h4 title="Project Management">Project Management</h4>
                                        <p>Udemy</p>
                                    </div>
                                </div>
                                <div class="emp-rec-info-row">
                                    <span><span class="emp-rec-star">★</span> 4.6</span>
                                    <span>
                                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                                        4h 20m
                                    </span>
                                </div>
                            </div>
                            <button type="button" class="emp-btn-enroll" onclick="handleEnroll(7, 'Project Management', this)">Enroll</button>
                        </div>

                        <!-- Card 4: Design Thinking -->
                        <div class="emp-rec-card">
                            <div>
                                <div class="emp-rec-top">
                                    <div class="emp-rec-icon thinking">
                                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M9 18h6"/><path d="M10 22h4"/><path d="M12 2a7 7 0 0 0-7 7c0 2.5 1.5 4.5 3 6h8c1.5-1.5 3-3.5 3-6a7 7 0 0 0-7-7z"/></svg>
                                    </div>
                                    <div class="emp-rec-card-meta">
                                        <h4 title="Design Thinking">Design Thinking</h4>
                                        <p>LinkedIn Learning</p>
                                    </div>
                                </div>
                                <div class="emp-rec-info-row">
                                    <span><span class="emp-rec-star">★</span> 4.5</span>
                                    <span>
                                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                                        2h 10m
                                    </span>
                                </div>
                            </div>
                            <button type="button" class="emp-btn-enroll" onclick="handleEnroll(8, 'Design Thinking', this)">Enroll</button>
                        </div>
                    </div>
                </div>

                <!-- Motivational Banner Card -->
                <div class="emp-quote-card">
                    <div class="emp-quote-content">
                        <p class="emp-quote-text">“Continuous learning is the minimum requirement for success.”</p>
                        <p class="emp-quote-author">— Brian Tracy</p>
                    </div>

                    <div class="emp-quote-footer-art">
                        <div class="emp-quote-badge-line"></div>
                        <!-- Vector illustration of learner at desk with laptop & lightbulb -->
                        <svg class="emp-quote-art-svg" viewBox="0 0 160 110" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <!-- Idea glow -->
                            <circle cx="115" cy="22" r="9" fill="#a78bfa" fill-opacity="0.35"/>
                            <path d="M115 17v4m0 6v2m-5-7h4m6 0h2" stroke="#6366f1" stroke-width="2" stroke-linecap="round"/>
                            <!-- Desk -->
                            <rect x="10" y="86" width="140" height="6" rx="3" fill="#6366f1"/>
                            <!-- Laptop -->
                            <path d="M55 64h32v18H55z" fill="#312e81"/>
                            <path d="M48 82h46l-4 4H52z" fill="#4338ca"/>
                            <!-- Person with hair & laptop -->
                            <circle cx="118" cy="50" r="10" fill="#4338ca"/>
                            <path d="M110 50c0-6 4-11 11-11s10 5 10 11v6c0 2-2 4-5 4s-6-3-6-6z" fill="#312e81"/>
                            <path d="M104 86c0-12 7-22 17-22s17 10 17 22z" fill="#6366f1"/>
                            <!-- Arm extending to keyboard -->
                            <path d="M106 72l-22 12" stroke="#4338ca" stroke-width="4" stroke-linecap="round"/>
                            <!-- Plant on desk -->
                            <rect x="22" y="74" width="12" height="12" rx="2" fill="#818cf8"/>
                            <path d="M28 74c-4-8-1-14 3-16 4 2 7 8 3 16z" fill="#34d399"/>
                            <path d="M24 74c-5-4-6-10-3-12 4 1 5 6 3 12z" fill="#10b981"/>
                        </svg>
                    </div>
                </div>

            </section>

        </main>

    </div><!-- /.emp-main-wrapper -->

</div><!-- /.emp-app-layout -->


<!-- =====================================================
     MODAL 1: REVIEW DETAILS MODAL
====================================================== -->
<div class="emp-modal-backdrop" id="reviewDetailModal" style="display:none;">
    <div class="emp-modal-dialog">
        <div class="emp-modal-header">
            <h3 id="reviewModalTitle">Performance Appraisal Details</h3>
            <button type="button" class="emp-modal-close" aria-label="Close">✕</button>
        </div>
        <div class="emp-modal-body">
            <div style="display:flex; justify-content:space-between; align-items:flex-start; padding:12px; background:#f8fafc; border-radius:12px; border:1px solid #e2e8f0;">
                <div>
                    <span style="font-size:11px; font-weight:700; color:#64748b; text-transform:uppercase;">Reviewer</span>
                    <h4 id="reviewModalReviewer" style="font-size:14px; font-weight:700; color:#0f172a; margin-top:2px;">Rahul Mehta (Engineering Manager)</h4>
                </div>
                <div style="text-align:right;">
                    <span style="font-size:11px; font-weight:700; color:#64748b; text-transform:uppercase;">Overall Rating</span>
                    <div id="reviewModalRating" style="font-size:18px; font-weight:800; color:#4f46e5; margin-top:2px;">4.3 / 5</div>
                </div>
            </div>

            <div>
                <h4 style="font-size:13px; font-weight:700; color:#0f172a; margin-bottom:10px;">Competency Score Breakdown</h4>
                <div id="reviewModalBreakdown" style="background:#ffffff; border:1px solid #e2e8f0; border-radius:12px; padding:12px 16px;">
                    <!-- Dynamically populated -->
                </div>
            </div>

            <div>
                <h4 style="font-size:13px; font-weight:700; color:#0f172a; margin-bottom:6px;">Reviewer Feedback &amp; Comments</h4>
                <p id="reviewModalComments" style="font-size:13px; color:#334155; line-height:1.6; background:#f8fafc; border-radius:12px; padding:14px; border:1px solid #e2e8f0;">
                    Outstanding technical performance and leadership on client portal redesign. Demonstrates exceptional attention to detail and proactive teamwork.
                </p>
            </div>
        </div>
        <div class="emp-modal-footer">
            <button type="button" class="emp-btn-cancel">Close</button>
        </div>
    </div>
</div>


<!-- =====================================================
     MODAL 2: CERTIFICATE PREVIEW MODAL
====================================================== -->
<div class="emp-modal-backdrop" id="certificateModal" style="display:none;">
    <div class="emp-modal-dialog" style="max-width: 640px;">
        <div class="emp-modal-header">
            <h3>Certificate of Completion</h3>
            <button type="button" class="emp-modal-close" aria-label="Close">✕</button>
        </div>
        <div class="emp-modal-body">
            <div class="emp-cert-card-preview" id="printableCert">
                <div style="display:inline-flex; width:44px; height:44px; border-radius:12px; background:linear-gradient(135deg, #7c3aed 0%, #6d28d9 100%); color:#ffffff; font-size:22px; font-weight:800; align-items:center; justify-content:center; margin-bottom:12px;">M</div>
                <h2 class="emp-cert-header-title">Mindazzle Academy</h2>
                <p class="emp-cert-sub">Certificate of Achievement</p>

                <p class="emp-cert-present">This is proudly presented to</p>
                <h1 class="emp-cert-recipient" id="certRecipientName">Priya Nair</h1>

                <p class="emp-cert-for">for successfully completing the specialized professional program</p>
                <h3 class="emp-cert-course" id="certCourseName">Agile Fundamentals</h3>

                <div class="emp-cert-meta-row">
                    <div>
                        <strong>Date:</strong> <span id="certDate">16 Sep 2026</span>
                    </div>
                    <div>
                        <strong>Credential ID:</strong> <span id="certNumber">CERT-2026-AF-998</span>
                    </div>
                    <div>
                        <strong>Status:</strong> <span style="color:#059669; font-weight:700;">Verified ✓</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="emp-modal-footer">
            <button type="button" class="emp-btn-cancel">Close</button>
            <button type="button" class="emp-btn-submit" id="btnPrintCert">Download / Print PDF</button>
        </div>
    </div>
</div>


<!-- =====================================================
     MODAL 3: COURSE PLAYER / PROGRESS MODAL
====================================================== -->
<div class="emp-modal-backdrop" id="courseProgressModal" style="display:none;">
    <div class="emp-modal-dialog">
        <div class="emp-modal-header">
            <h3 id="courseModalTitle">Course Learning Module</h3>
            <button type="button" class="emp-modal-close" aria-label="Close">✕</button>
        </div>
        <div class="emp-modal-body">
            <p style="font-size:13px; color:#64748b;">Update your course learning progress or check off completed study units.</p>

            <div style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:12px; padding:16px;">
                <label style="font-size:12px; font-weight:700; color:#334155; display:block; margin-bottom:8px;">Current Completion Progress</label>
                <div style="display:flex; align-items:center; gap:14px;">
                    <input type="range" id="courseProgressSlider" min="0" max="100" step="5" style="flex:1; cursor:pointer;">
                    <span id="courseProgressVal" style="font-size:16px; font-weight:800; color:#4f46e5; min-width:44px;">80%</span>
                </div>
            </div>

            <div style="display:flex; flex-direction:column; gap:8px;">
                <label style="font-size:12px; font-weight:700; color:#334155;">Curriculum Modules</label>
                <div style="display:flex; align-items:center; gap:10px; padding:10px 12px; background:#f8fafc; border-radius:8px; font-size:12.5px;">
                    <input type="checkbox" checked disabled>
                    <span style="font-weight:600; color:#0f172a;">Module 1: Principles &amp; Design Architecture</span>
                </div>
                <div style="display:flex; align-items:center; gap:10px; padding:10px 12px; background:#f8fafc; border-radius:8px; font-size:12.5px;">
                    <input type="checkbox" checked disabled>
                    <span style="font-weight:600; color:#0f172a;">Module 2: Advanced Prototyping &amp; Components</span>
                </div>
                <div style="display:flex; align-items:center; gap:10px; padding:10px 12px; background:#f8fafc; border-radius:8px; font-size:12.5px;">
                    <input type="checkbox">
                    <span style="font-weight:600; color:#0f172a;">Module 3: Automated Handoff &amp; Tokens Certification</span>
                </div>
            </div>
        </div>
        <div class="emp-modal-footer">
            <button type="button" class="emp-btn-cancel">Cancel</button>
            <button type="button" class="emp-btn-submit" id="btnSaveCourseProgress">Save Progress</button>
        </div>
    </div>
</div>


<!-- =====================================================
     MODAL 4: ADD NEW GOAL MODAL
====================================================== -->
<div class="emp-modal-backdrop" id="addGoalModal" style="display:none;">
    <div class="emp-modal-dialog">
        <div class="emp-modal-header">
            <h3>Add New Performance Goal</h3>
            <button type="button" class="emp-modal-close" aria-label="Close">✕</button>
        </div>
        <form id="newGoalForm">
            <div class="emp-modal-body">
                <div class="emp-form-group">
                    <label for="goalTitle">Goal Title *</label>
                    <input type="text" id="goalTitle" required placeholder="e.g. Lead Accessibility Audit for Web App">
                </div>
                <div class="emp-form-group">
                    <label for="goalDesc">Description &amp; Key Deliverables</label>
                    <textarea id="goalDesc" rows="3" placeholder="Outline specific metrics and success criteria..."></textarea>
                </div>
                <div class="emp-form-row">
                    <div class="emp-form-group">
                        <label for="goalTargetDate">Target Date</label>
                        <input type="date" id="goalTargetDate" value="<?= date('Y-m-d', strtotime('+2 months')) ?>">
                    </div>
                    <div class="emp-form-group">
                        <label for="goalPriority">Priority</label>
                        <select id="goalPriority">
                            <option value="HIGH">High Priority</option>
                            <option value="MEDIUM" selected>Medium Priority</option>
                            <option value="LOW">Low Priority</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="emp-modal-footer">
                <button type="button" class="emp-btn-cancel">Cancel</button>
                <button type="submit" class="emp-btn-submit">Create Goal</button>
            </div>
        </form>
    </div>
</div>


<!-- SCRIPTS -->
<script src="../../assets/js/theme.js?v=<?= time() ?>"></script>
<script src="../../assets/js/employee-performance.js?v=<?= time() ?>"></script>

</body>
</html>

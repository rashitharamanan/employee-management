<!DOCTYPE html>
<html lang="en" class="dash-html-fixed">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Management Suite</title>
    <link rel="stylesheet" href="../assets/css/app-theme.css?v=<?= time() ?>">
    <link rel="stylesheet" href="../assets/css/sidebar.css?v=<?= time() ?>">
    <link rel="stylesheet" href="../assets/css/topbar.css?v=<?= time() ?>">
    <link rel="stylesheet" href="../assets/css/dashboard.css?v=<?= time() ?>">
</head>
<body class="dash-fixed-page">
<div class="app">
    <?php include '../includes/sidebar.php'; ?>

    <main class="main-content">
        <?php include '../includes/topbar.php'; ?>

        <div class="dash-container">
            <!-- =========================================================
                 LEFT / MAIN COLUMN (approx 72%)
                 ========================================================= -->
            <div class="dash-main-col">



                <!-- 2. FOUR KPI PILL CARDS IN LUNA PALETTE GRADIENTS -->
                <section class="dash-kpi-row">
                    <!-- Total Tasks / Employees -->
                    <article class="dash-kpi-pill stat-luna-blue">
                        <div class="stat-wave"></div>
                        <div class="stat-icon-wrapper">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="3" y="3" width="18" height="18" rx="4" ry="4"></rect>
                                <polyline points="9 12 11 14 15 10"></polyline>
                            </svg>
                        </div>
                        <div class="kpi-pill-content">
                            <span class="kpi-pill-label">Total Tasks</span>
                            <strong class="kpi-pill-val" id="dashKpiEmployees">9</strong>
                            <span class="stat-badge">↑ 3 today</span>
                        </div>
                    </article>

                    <!-- Projects / Departments -->
                    <article class="dash-kpi-pill stat-luna-teal">
                        <div class="stat-wave"></div>
                        <div class="stat-icon-wrapper">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"></path>
                                <line x1="12" y1="11" x2="12" y2="17"></line>
                                <line x1="9" y1="14" x2="15" y2="14"></line>
                            </svg>
                        </div>
                        <div class="kpi-pill-content">
                            <span class="kpi-pill-label">Projects</span>
                            <strong class="kpi-pill-val" id="dashKpiProjects">3</strong>
                            <span class="stat-badge">Active</span>
                        </div>
                    </article>

                    <!-- Meetings / On Leave -->
                    <article class="dash-kpi-pill stat-luna-emerald">
                        <div class="stat-wave"></div>
                        <div class="stat-icon-wrapper">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M18.36 6.64a9 9 0 1 1-12.73 0"></path>
                                <line x1="12" y1="2" x2="12" y2="12"></line>
                            </svg>
                        </div>
                        <div class="kpi-pill-content">
                            <span class="kpi-pill-label">Meetings</span>
                            <strong class="kpi-pill-val" id="dashKpiMeetings">3</strong>
                            <span class="stat-badge">Today</span>
                        </div>
                    </article>

                    <!-- Hours Tracked / Attendance Rate -->
                    <article class="dash-kpi-pill stat-luna-navy">
                        <div class="stat-wave"></div>
                        <div class="stat-icon-wrapper">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="12" cy="12" r="10"></circle>
                                <polyline points="12 6 12 12 16 14"></polyline>
                            </svg>
                        </div>
                        <div class="kpi-pill-content">
                            <span class="kpi-pill-label">Hours Tracked</span>
                            <strong class="kpi-pill-val" id="dashKpiHours">6.8</strong>
                            <span class="stat-badge">↑ 1.2h</span>
                        </div>
                    </article>
                </section>

                <!-- 3. MIDDLE SECTION (PRODUCTIVITY & TEAM ACTIVITY) -->
                <section class="dash-mid-grid-2">
                    <!-- Card 1: Productivity -->
                    <article class="dash-card dash-productivity-card">
                        <div class="card-header-clean">
                            <div class="card-title-wrap">
                                <span class="card-badge-icon badge-teal">⚡</span>
                                <h3 class="card-title">Productivity & Workforce Presence</h3>
                            </div>
                            <span class="prod-trend-pill">+8% than last week</span>
                        </div>
                        <div class="productivity-body">
                            <div class="prod-donut-wrapper">
                                <svg class="prod-ring-svg" width="120" height="120" viewBox="0 0 100 100">
                                    <circle class="ring-bg" cx="50" cy="50" r="40" stroke="#d5e6f3" stroke-width="9" fill="none"></circle>
                                    <circle class="ring-fill ring-teal" cx="50" cy="50" r="40" stroke="#26658C" stroke-width="9" stroke-dasharray="251.2" stroke-dashoffset="65" stroke-linecap="round" fill="none"></circle>
                                    <circle class="ring-fill ring-aqua" cx="50" cy="50" r="40" stroke="#54ACBF" stroke-width="9" stroke-dasharray="251.2" stroke-dashoffset="190" stroke-linecap="round" fill="none"></circle>
                                </svg>
                                <div class="prod-donut-center">
                                    <strong class="prod-pct">78%</strong>
                                    <small class="prod-sub-txt">Effective</small>
                                </div>
                            </div>
                            <div class="prod-legend-list">
                                <div class="prod-legend-item">
                                    <span class="legend-dot bg-luna-dark"></span>
                                    <span class="legend-label">Focused Work</span>
                                    <span class="legend-val">4h 30m</span>
                                </div>
                                <div class="prod-legend-item">
                                    <span class="legend-dot bg-luna-teal"></span>
                                    <span class="legend-label">Team Meetings</span>
                                    <span class="legend-val">1h 20m</span>
                                </div>
                                <div class="prod-legend-item">
                                    <span class="legend-dot bg-luna-aqua"></span>
                                    <span class="legend-label">Scheduled Breaks</span>
                                    <span class="legend-val">40m</span>
                                </div>
                                <div class="prod-legend-item">
                                    <span class="legend-dot bg-luna-gray"></span>
                                    <span class="legend-label">Other Operations</span>
                                    <span class="legend-val">30m</span>
                                </div>
                            </div>
                        </div>
                    </article>

                    <!-- Card 2: Team Activity -->
                    <article class="dash-card dash-activity-card">
                        <div class="card-header-clean">
                            <div class="card-title-wrap">
                                <span class="card-badge-icon badge-blue">👥</span>
                                <h3 class="card-title">Team Activity</h3>
                            </div>
                        </div>
                        <div class="activity-top-strip">
                            <div class="avatar-stack">
                                <span class="stack-avatar av-1">SA</span>
                                <span class="stack-avatar av-2">TE</span>
                                <span class="stack-avatar av-3">MK</span>
                                <span class="stack-avatar av-4">JS</span>
                                <span class="stack-avatar stack-more">+3</span>
                            </div>
                            <span class="online-status-txt"><b class="live-pulse-dot"></b> 6 members online</span>
                        </div>
                        <div class="activity-feed-list" id="teamActivityList">
                            <div class="activity-item">
                                <span class="activity-av av-1">SA</span>
                                <div class="activity-meta">
                                    <p><strong>savitha</strong> commented on Project UI/UX</p>
                                    <small>2 hours ago</small>
                                </div>
                            </div>
                            <div class="activity-item">
                                <span class="activity-av av-2">TE</span>
                                <div class="activity-meta">
                                    <p><strong>Test Employee</strong> completed attendance check-in</p>
                                    <small>3 hours ago</small>
                                </div>
                            </div>
                            <div class="activity-item">
                                <span class="activity-av av-3">MK</span>
                                <div class="activity-meta">
                                    <p><strong>Test Employee</strong> joined the engineering team</p>
                                    <small>4 hours ago</small>
                                </div>
                            </div>
                        </div>
                    </article>
                </section>

                <!-- 4. BOTTOM ROW (RECENT PROJECTS & QUICK ACTIONS) -->
                <section class="dash-bottom-grid">
                    <!-- Recent Projects -->
                    <article class="dash-card dash-projects-card">
                        <div class="card-header-clean">
                            <div class="card-title-wrap">
                                <span class="card-badge-icon badge-amber">📁</span>
                                <h3 class="card-title">Recent Projects</h3>
                            </div>
                        </div>
                        <div class="projects-carousel" id="recentProjectsCarousel">
                            <!-- Project 1 -->
                            <div class="mini-project-card proj-theme-blue" data-proj-id="proj_static_1" data-proj-progress="85">
                                <div class="proj-top">
                                    <div class="proj-icon icon-blue">
                                        <svg width="17" height="17" viewBox="0 0 24 24" fill="currentColor"><path d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z"/></svg>
                                    </div>
                                    <div>
                                        <strong class="proj-name">Rently</strong>
                                        <small class="proj-sub">Rental Marketplace</small>
                                    </div>
                                </div>
                                <div class="proj-prog-row">
                                    <div class="mini-progress-bar"><div class="mini-progress-fill fill-blue" style="width: 85%;"></div></div>
                                    <span class="proj-prog-val">85%</span>
                                </div>
                                <div class="proj-footer">
                                    <div class="avatar-stack-tiny">
                                        <span class="tiny-av av-1">S</span>
                                        <span class="tiny-av av-2">A</span>
                                        <span class="tiny-av av-3">Z</span>
                                    </div>
                                    <span class="proj-due">Due: 30 Aug</span>
                                </div>
                            </div>

                            <!-- Project 2 -->
                            <div class="mini-project-card proj-theme-green" data-proj-id="proj_static_2" data-proj-progress="60">
                                <div class="proj-top">
                                    <div class="proj-icon icon-green">
                                        <svg width="17" height="17" viewBox="0 0 24 24" fill="currentColor"><path d="M17 8C8 10 5.9 16.17 3.82 21.34L5.71 22l1-2.3A4.49 4.49 0 0 0 8 20C19 20 22 3 22 3c-1 2-8 2.25-13 3.25S2 11.5 2 13.5s1.75 3.75 1.75 3.75C7 8 17 8 17 8z"/></svg>
                                    </div>
                                    <div>
                                        <strong class="proj-name">Green Market</strong>
                                        <small class="proj-sub">E-commerce App</small>
                                    </div>
                                </div>
                                <div class="proj-prog-row">
                                    <div class="mini-progress-bar"><div class="mini-progress-fill fill-green" style="width: 60%;"></div></div>
                                    <span class="proj-prog-val">60%</span>
                                </div>
                                <div class="proj-footer">
                                    <div class="avatar-stack-tiny">
                                        <span class="tiny-av av-2">A</span>
                                        <span class="tiny-av av-4">M</span>
                                    </div>
                                    <span class="proj-due">Due: 5 Sep</span>
                                </div>
                            </div>

                            <!-- Project 3 -->
                            <div class="mini-project-card proj-theme-teal" data-proj-id="proj_static_3" data-proj-progress="45">
                                <div class="proj-top">
                                    <div class="proj-icon icon-teal">
                                        <svg width="17" height="17" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2L4 5v6.09c0 5.05 3.41 9.76 8 10.91 4.59-1.15 8-5.86 8-10.91V5l-8-3zm1 14h-2v-3H8v-2h3V8h2v3h3v2h-3v3z"/></svg>
                                    </div>
                                    <div>
                                        <strong class="proj-name">MediLink</strong>
                                        <small class="proj-sub">Doctor Appointment</small>
                                    </div>
                                </div>
                                <div class="proj-prog-row">
                                    <div class="mini-progress-bar"><div class="mini-progress-fill fill-teal" style="width: 45%;"></div></div>
                                    <span class="proj-prog-val">45%</span>
                                </div>
                                <div class="proj-footer">
                                    <div class="avatar-stack-tiny">
                                        <span class="tiny-av av-1">S</span>
                                        <span class="tiny-av av-3">Z</span>
                                        <span class="tiny-av av-4">M</span>
                                    </div>
                                    <span class="proj-due">Due: 12 Sep</span>
                                </div>
                            </div>

                            <!-- Project 4 -->
                            <div class="mini-project-card proj-theme-purple" data-proj-id="proj_static_4" data-proj-progress="70">
                                <div class="proj-top">
                                    <div class="proj-icon icon-purple">
                                        <svg width="17" height="17" viewBox="0 0 24 24" fill="currentColor"><path d="M20.57 14.86L22 13.43 20.57 12 17 15.57 8.43 7 12 3.43 10.57 2 9.14 3.43 7.71 2 5.57 4.14 4.14 2.71 2.71 4.14l1.43 1.43L2 7.71l1.43 1.43L2 10.57 3.43 12 7 8.43 15.57 17 12 20.57 13.43 22l1.43-1.43L16.29 22l2.14-2.14 1.43 1.43 1.43-1.43-1.43-1.43L22 16.29l-1.43-1.43z"/></svg>
                                    </div>
                                    <div>
                                        <strong class="proj-name">Fitness Coach</strong>
                                        <small class="proj-sub">Web Application</small>
                                    </div>
                                </div>
                                <div class="proj-prog-row">
                                    <div class="mini-progress-bar"><div class="mini-progress-fill fill-purple" style="width: 70%;"></div></div>
                                    <span class="proj-prog-val">70%</span>
                                </div>
                                <div class="proj-footer">
                                    <div class="avatar-stack-tiny">
                                        <span class="tiny-av av-2">A</span>
                                        <span class="tiny-av av-3">Z</span>
                                    </div>
                                    <span class="proj-due">Due: 18 Sep</span>
                                </div>
                            </div>
                        </div>
                    </article>

                    <!-- Quick Actions -->
                    <article class="dash-card dash-actions-card">
                        <div class="card-header-clean">
                            <div class="card-title-wrap">
                                <span class="card-badge-icon badge-rose">🎯</span>
                                <h3 class="card-title">Quick Actions</h3>
                            </div>
                        </div>
                        <div class="actions-grid-6">
                            <button type="button" class="action-tile-btn tile-luna-blue" onclick="openNewTaskModal()">
                                <div class="tile-icon-box">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                                </div>
                                <span>New Task</span>
                            </button>
                            <button type="button" class="action-tile-btn tile-luna-purple" onclick="openNewProjectModal()">
                                <div class="tile-icon-box">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"></path></svg>
                                </div>
                                <span>New Project</span>
                            </button>
                            <button type="button" class="action-tile-btn tile-luna-teal" onclick="openUploadFileModal()">
                                <div class="tile-icon-box">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="17 8 12 3 7 8"></polyline><line x1="12" y1="3" x2="12" y2="15"></line></svg>
                                </div>
                                <span>Upload File</span>
                            </button>
                            <button type="button" class="action-tile-btn tile-luna-rose" onclick="openAddEventModal()">
                                <div class="tile-icon-box">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                                </div>
                                <span>Add Event</span>
                            </button>
                            <button type="button" class="action-tile-btn tile-luna-amber" id="quickActionTimerBtn" onclick="openTimerModal()">
                                <div class="tile-icon-box">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                                </div>
                                <span id="quickActionTimerLabel">Start Timer</span>
                            </button>
                            <button type="button" class="action-tile-btn tile-luna-dark" onclick="openUploadedFilesModal()">
                                <div class="tile-icon-box">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
                                </div>
                                <span>Uploaded Files</span>
                            </button>
                        </div>
                    </article>
                </section>

            </div>

            <!-- =========================================================
                 RIGHT RAIL (approx 28%)
                 ========================================================= -->
            <aside class="dash-right-rail">

                <!-- 1. CALENDAR WIDGET -->
                <section class="dash-card dash-calendar-card">
                    <div class="card-header-clean">
                        <div class="card-title-wrap">
                            <span class="card-badge-icon badge-teal">📅</span>
                            <h3 class="card-title">Calendar</h3>
                        </div>
                        <a href="leave-management.php" class="card-header-link">View all →</a>
                    </div>
                    <div class="cal-nav-row">
                        <button type="button" class="cal-nav-btn" id="calPrevBtn" aria-label="Previous Month">‹</button>
                        <strong class="cal-month-title" id="calMonthTitle">August 2026</strong>
                        <button type="button" class="cal-nav-btn" id="calNextBtn" aria-label="Next Month">›</button>
                    </div>
                    <div class="calendar-grid">
                        <div class="cal-day-header">Sun</div>
                        <div class="cal-day-header">Mon</div>
                        <div class="cal-day-header">Tue</div>
                        <div class="cal-day-header">Wed</div>
                        <div class="cal-day-header">Thu</div>
                        <div class="cal-day-header">Fri</div>
                        <div class="cal-day-header">Sat</div>
                    </div>
                    <div class="calendar-days-body" id="calDaysBody">
                        <!-- Populated by JavaScript -->
                    </div>
                </section>

                <!-- 2. UPCOMING TASKS -->
                <section class="dash-card dash-tasks-card">
                    <div class="card-header-clean">
                        <div class="card-title-wrap">
                            <span class="card-badge-icon badge-blue">📋</span>
                            <h3 class="card-title">Upcoming Tasks</h3>
                        </div>
                        <a href="#" class="card-header-link">View all →</a>
                    </div>
                    <div class="tasks-list" id="upcomingTasksList">
                        <!-- Task 1 -->
                        <div class="task-row" data-task-id="task_static_1" data-assignee-name="Savitha">
                            <label class="task-check-wrap">
                                <input type="checkbox" class="task-checkbox">
                                <span class="custom-checkbox"></span>
                            </label>
                            <div class="task-text-col">
                                <strong class="task-title">Design dashboard UI</strong>
                                <small class="task-time">Today • 11:00 AM</small>
                            </div>
                            <span class="task-priority priority-high">High</span>
                            <span class="task-assignee av-1" title="Savitha (SA)">SA</span>
                        </div>

                        <!-- Task 2 -->
                        <div class="task-row" data-task-id="task_static_2" data-assignee-name="Ananya Roy">
                            <label class="task-check-wrap">
                                <input type="checkbox" class="task-checkbox">
                                <span class="custom-checkbox"></span>
                            </label>
                            <div class="task-text-col">
                                <strong class="task-title">Client meeting (Zoom)</strong>
                                <small class="task-time">Today • 3:00 PM</small>
                            </div>
                            <span class="task-priority priority-med">Medium</span>
                            <span class="task-assignee av-2" title="Ananya Roy (AR)">AR</span>
                        </div>

                        <!-- Task 3 -->
                        <div class="task-row" data-task-id="task_static_3" data-assignee-name="Zack Foster">
                            <label class="task-check-wrap">
                                <input type="checkbox" class="task-checkbox">
                                <span class="custom-checkbox"></span>
                            </label>
                            <div class="task-text-col">
                                <strong class="task-title">Project presentation</strong>
                                <small class="task-time">Tomorrow • 10:00 AM</small>
                            </div>
                            <span class="task-priority priority-high">High</span>
                            <span class="task-assignee av-3" title="Zack Foster (ZF)">ZF</span>
                        </div>

                        <!-- Task 4 -->
                        <div class="task-row" data-task-id="task_static_4" data-assignee-name="Maya Kumar">
                            <label class="task-check-wrap">
                                <input type="checkbox" class="task-checkbox">
                                <span class="custom-checkbox"></span>
                            </label>
                            <div class="task-text-col">
                                <strong class="task-title">Review user feedback</strong>
                                <small class="task-time">Tomorrow • 4:00 PM</small>
                            </div>
                            <span class="task-priority priority-low">Low</span>
                            <span class="task-assignee av-4" title="Maya Kumar (MK)">MK</span>
                        </div>

                        <!-- Task 5 -->
                        <div class="task-row" data-task-id="task_static_5" data-assignee-name="Savitha">
                            <label class="task-check-wrap">
                                <input type="checkbox" class="task-checkbox">
                                <span class="custom-checkbox"></span>
                            </label>
                            <div class="task-text-col">
                                <strong class="task-title">Sync team sprint goals</strong>
                                <small class="task-time">Friday • 2:00 PM</small>
                            </div>
                            <span class="task-priority priority-med">Medium</span>
                            <span class="task-assignee av-1" title="Savitha (SA)">SA</span>
                        </div>
                    </div>
                </section>

            </aside>
        </div>
    </main>
</div>

<!-- =========================================================================
     QUICK ACTION MODALS
     ========================================================================= -->

<!-- 1. NEW TASK MODAL -->
<div class="dash-modal-overlay" id="newTaskModal" role="dialog" aria-modal="true" aria-labelledby="newTaskModalTitle">
    <div class="dash-modal-box">
        <div class="dash-modal-header">
            <div class="dash-modal-title-wrap">
                <span class="dash-modal-icon">📋</span>
                <h3 class="dash-modal-title" id="newTaskModalTitle">Create New Task</h3>
            </div>
            <button type="button" class="dash-modal-close-btn" onclick="closeDashModal('newTaskModal')" aria-label="Close modal">&times;</button>
        </div>
        <form id="newTaskForm" onsubmit="handleCreateTask(event)">
            <div class="dash-modal-body">
                <div class="dash-form-group">
                    <label for="taskTitleInput">Task Title *</label>
                    <input type="text" id="taskTitleInput" class="dash-form-input" placeholder="e.g. Audit attendance reports" required>
                </div>
                <div class="dash-form-row">
                    <div class="dash-form-group">
                        <label for="taskPriorityInput">Priority</label>
                        <select id="taskPriorityInput" class="dash-form-select">
                            <option value="High">High</option>
                            <option value="Medium" selected>Medium</option>
                            <option value="Low">Low</option>
                        </select>
                    </div>
                    <div class="dash-form-group">
                        <label for="taskDueInput">Due Time / Day</label>
                        <input type="text" id="taskDueInput" class="dash-form-input" placeholder="e.g. Today • 5:00 PM" value="Today • 4:00 PM">
                    </div>
                </div>
                <div class="dash-form-row">
                    <div class="dash-form-group">
                        <label for="taskAssigneeInput">Assignee Initials</label>
                        <input type="text" id="taskAssigneeInput" class="dash-form-input" placeholder="e.g. SA, HR, TE" maxlength="3" value="HR">
                    </div>
                    <div class="dash-form-group">
                        <label for="taskAssigneeNameInput">Assignee Name</label>
                        <input type="text" id="taskAssigneeNameInput" class="dash-form-input" placeholder="e.g. Vasundhara" value="HR Manager">
                    </div>
                </div>
                <label class="dash-checkbox-label">
                    <input type="checkbox" id="taskSyncHrCheckbox" checked>
                    <span>Also sync task to HR Portal &amp; Onboarding/Exit Tasks</span>
                </label>
            </div>
            <div class="dash-modal-footer">
                <button type="button" class="dash-btn-cancel" onclick="closeDashModal('newTaskModal')">Cancel</button>
                <button type="submit" class="dash-btn-submit">Add Task</button>
            </div>
        </form>
    </div>
</div>

<!-- 2. NEW PROJECT MODAL -->
<div class="dash-modal-overlay" id="newProjectModal" role="dialog" aria-modal="true" aria-labelledby="newProjectModalTitle">
    <div class="dash-modal-box">
        <div class="dash-modal-header">
            <div class="dash-modal-title-wrap">
                <span class="dash-modal-icon">📁</span>
                <h3 class="dash-modal-title" id="newProjectModalTitle">Create New Project</h3>
            </div>
            <button type="button" class="dash-modal-close-btn" onclick="closeDashModal('newProjectModal')" aria-label="Close modal">&times;</button>
        </div>
        <form id="newProjectForm" onsubmit="handleCreateProject(event)">
            <div class="dash-modal-body">
                <div class="dash-form-group">
                    <label for="projNameInput">Project Name *</label>
                    <input type="text" id="projNameInput" class="dash-form-input" placeholder="e.g. Core Banking Portal" required>
                </div>
                <div class="dash-form-group">
                    <label for="projSubInput">Category / Description</label>
                    <input type="text" id="projSubInput" class="dash-form-input" placeholder="e.g. Fintech Solution" value="Web Application">
                </div>
                <div class="dash-form-row">
                    <div class="dash-form-group">
                        <label for="projThemeInput">Theme Color</label>
                        <select id="projThemeInput" class="dash-form-select">
                            <option value="blue" selected>Luna Blue</option>
                            <option value="green">Emerald Green</option>
                            <option value="teal">Cyan Teal</option>
                            <option value="purple">Royal Purple</option>
                        </select>
                    </div>
                    <div class="dash-form-group">
                        <label for="projProgressInput">Initial Progress (%)</label>
                        <input type="number" id="projProgressInput" class="dash-form-input" min="0" max="100" value="25">
                    </div>
                </div>
                <div class="dash-form-group">
                    <label for="projDueInput">Due Date</label>
                    <input type="text" id="projDueInput" class="dash-form-input" placeholder="e.g. Due: 28 Oct" value="Due: 15 Oct">
                </div>
            </div>
            <div class="dash-modal-footer">
                <button type="button" class="dash-btn-cancel" onclick="closeDashModal('newProjectModal')">Cancel</button>
                <button type="submit" class="dash-btn-submit">Add Project</button>
            </div>
        </form>
    </div>
</div>

<!-- 3. ADD EVENT MODAL -->
<div class="dash-modal-overlay" id="newEventModal" role="dialog" aria-modal="true" aria-labelledby="newEventModalTitle">
    <div class="dash-modal-box">
        <div class="dash-modal-header">
            <div class="dash-modal-title-wrap">
                <span class="dash-modal-icon">📅</span>
                <h3 class="dash-modal-title" id="newEventModalTitle">Add Calendar Event</h3>
            </div>
            <button type="button" class="dash-modal-close-btn" onclick="closeDashModal('newEventModal')" aria-label="Close modal">&times;</button>
        </div>
        <form id="newEventForm" onsubmit="handleCreateEvent(event)">
            <div class="dash-modal-body">
                <div class="dash-form-group">
                    <label for="eventTitleInput">Event Title *</label>
                    <input type="text" id="eventTitleInput" class="dash-form-input" placeholder="e.g. Quarterly Team Review" required>
                </div>
                <div class="dash-form-row">
                    <div class="dash-form-group">
                        <label for="eventDayInput">Day of Month (1 - 31) *</label>
                        <input type="number" id="eventDayInput" class="dash-form-input" min="1" max="31" value="26" required>
                    </div>
                    <div class="dash-form-group">
                        <label for="eventTypeInput">Event Category</label>
                        <select id="eventTypeInput" class="dash-form-select">
                            <option value="Meeting" selected>Meeting</option>
                            <option value="Milestone">Milestone</option>
                            <option value="Review">Performance Review</option>
                            <option value="Holiday">Company Event</option>
                        </select>
                    </div>
                </div>
                <div class="dash-form-group">
                    <label for="eventTimeInput">Event Time</label>
                    <input type="text" id="eventTimeInput" class="dash-form-input" placeholder="e.g. 10:30 AM - 11:30 AM" value="11:00 AM">
                </div>
            </div>
            <div class="dash-modal-footer">
                <button type="button" class="dash-btn-cancel" onclick="closeDashModal('newEventModal')">Cancel</button>
                <button type="submit" class="dash-btn-submit">Add Event</button>
            </div>
        </form>
    </div>
</div>

<!-- 4. TASK DETAIL & COMPLETE MODAL -->
<div class="dash-modal-overlay" id="taskDetailModal" role="dialog" aria-modal="true" aria-labelledby="taskDetailModalTitle">
    <div class="dash-modal-box task-confirm-modal-box">
        <div class="dash-modal-header">
            <div class="dash-modal-title-wrap">
                <span class="dash-modal-icon" id="taskModalHeaderIcon">📋</span>
                <h3 class="dash-modal-title" id="taskDetailModalTitle">Task Details</h3>
            </div>
            <button type="button" class="dash-modal-close-btn" onclick="closeTaskDetailModal()" aria-label="Close modal">&times;</button>
        </div>

        <!-- STEP 1: Task Detail View -->
        <div id="taskDetailStep1">
            <div class="dash-modal-body">
                <div class="task-modal-detail-card">
                    <div class="task-modal-field">
                        <span class="task-modal-label">Task Name</span>
                        <h4 class="task-modal-title-val" id="taskModalTitle">-</h4>
                    </div>
                    <div class="task-modal-meta-grid">
                        <div class="task-modal-meta-item">
                            <span class="task-modal-label">Due Time</span>
                            <span class="task-modal-val" id="taskModalTime">-</span>
                        </div>
                        <div class="task-modal-meta-item">
                            <span class="task-modal-label">Priority</span>
                            <span id="taskModalPriority" class="task-priority">-</span>
                        </div>
                        <div class="task-modal-meta-item">
                            <span class="task-modal-label">Assignee</span>
                            <div class="task-modal-assignee-wrap">
                                <span class="task-assignee" id="taskModalAssigneeAv">SA</span>
                                <span class="task-modal-val" id="taskModalAssigneeName">Team Member</span>
                            </div>
                        </div>
                        <div class="task-modal-meta-item">
                            <span class="task-modal-label">Status</span>
                            <span class="task-modal-status-badge">Pending</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="dash-modal-footer">
                <button type="button" class="dash-btn-cancel" onclick="closeTaskDetailModal()">Cancel</button>
                <button type="button" class="dash-btn-submit" id="btnProceedToConfirm" onclick="proceedToTaskConfirm()">
                    Complete Task &rarr;
                </button>
            </div>
        </div>

        <!-- STEP 2: Confirmation View -->
        <div id="taskDetailStep2" style="display: none;">
            <div class="dash-modal-body text-center">
                <div class="task-confirm-prompt-icon">⚠️</div>
                <h3 class="task-confirm-heading">Are you sure you want to complete this task?</h3>
                <p class="task-confirm-desc">
                    Task: <strong id="taskConfirmTaskName" class="task-confirm-name">-</strong>
                </p>
                <p class="task-confirm-subtext">
                    Once confirmed, this task will be marked as finished and removed from your Upcoming Tasks table.
                </p>
            </div>
            <div class="dash-modal-footer">
                <button type="button" class="dash-btn-cancel" onclick="backToTaskDetail()">&larr; Back</button>
                <button type="button" class="dash-btn-submit dash-btn-confirm-complete" onclick="confirmTaskCompletion()">
                    Yes, Complete Task
                </button>
            </div>
        </div>
    </div>
</div>

<!-- 5. UPLOAD FILE MODAL -->
<div class="dash-modal-overlay" id="uploadFileModal" role="dialog" aria-modal="true" aria-labelledby="uploadFileModalTitle">
    <div class="dash-modal-box">
        <div class="dash-modal-header">
            <div class="dash-modal-title-wrap">
                <span class="dash-modal-icon">📤</span>
                <h3 class="dash-modal-title" id="uploadFileModalTitle">Upload Document / File</h3>
            </div>
            <button type="button" class="dash-modal-close-btn" onclick="closeDashModal('uploadFileModal')" aria-label="Close modal">&times;</button>
        </div>
        <form onsubmit="handleUploadFile(event)">
            <div class="dash-modal-body">
                <div class="dash-upload-dropzone" id="dashUploadDropzone" onclick="document.getElementById('dashFileInput').click()">
                    <input type="file" id="dashFileInput" style="display: none;" onchange="handleFileSelected(event)" onclick="event.stopPropagation()">
                    <div class="upload-dropzone-icon">☁️</div>
                    <h4 class="upload-dropzone-title">Click to browse or drag file here</h4>
                    <p class="upload-dropzone-sub">Supports PDF, DOCX, XLSX, PNG, JPG (Max 25MB)</p>
                    <div id="selectedFileInfo" class="selected-file-pill" style="display: none;">
                        <span id="selectedFileName">file.pdf</span>
                        <span id="selectedFileSize" class="file-size-tag">0 KB</span>
                    </div>
                </div>

                <div class="dash-form-group" style="margin-top: 14px;">
                    <label for="uploadCategorySelect">Category / Destination</label>
                    <select id="uploadCategorySelect" class="dash-form-select">
                        <option value="Policies">Company Policies</option>
                        <option value="Employees">Employee Records</option>
                        <option value="Projects">Project Documents</option>
                        <option value="Reports">Financial & Audit Reports</option>
                        <option value="General">General Documents</option>
                    </select>
                </div>

                <div class="dash-form-group">
                    <label for="uploadNotesInput">Optional Description / Notes</label>
                    <input type="text" id="uploadNotesInput" class="dash-form-input" placeholder="e.g. Updated Q3 HR Policy document">
                </div>

                <div id="uploadProgressBarWrap" class="upload-progress-wrap" style="display: none;">
                    <div class="upload-progress-info">
                        <span>Uploading...</span>
                        <span id="uploadProgressPct">0%</span>
                    </div>
                    <div class="mini-progress-bar"><div id="uploadProgressFill" class="mini-progress-fill fill-blue" style="width: 0%;"></div></div>
                </div>
            </div>
            <div class="dash-modal-footer">
                <button type="button" class="dash-btn-cancel" onclick="closeDashModal('uploadFileModal')">Cancel</button>
                <button type="submit" class="dash-btn-submit" id="btnUploadSubmit">Upload File</button>
            </div>
        </form>
    </div>
</div>

<!-- 6. START TIMER MODAL -->
<div class="dash-modal-overlay" id="timerModal" role="dialog" aria-modal="true" aria-labelledby="timerModalTitle">
    <div class="dash-modal-box timer-modal-box">
        <div class="dash-modal-header">
            <div class="dash-modal-title-wrap">
                <span class="dash-modal-icon">⏱️</span>
                <h3 class="dash-modal-title" id="timerModalTitle">Task & Work Timer</h3>
            </div>
            <button type="button" class="dash-modal-close-btn" onclick="closeDashModal('timerModal')" aria-label="Close modal">&times;</button>
        </div>
        <div class="dash-modal-body text-center">
            <div class="timer-display-box">
                <div class="timer-status-badge" id="timerStatusBadge">IDLE</div>
                <div class="timer-digits" id="timerDigitsDisplay">00:00:00</div>
                <div class="timer-sublabel" id="timerTaskLabel">Tracking: General Work</div>
            </div>

            <div class="dash-form-group" style="text-align: left; margin-top: 16px;">
                <label for="timerTaskSelect">Select Project / Task to Track</label>
                <select id="timerTaskSelect" class="dash-form-select" onchange="handleTimerTaskChange(this.value)">
                    <option value="General Work">General Admin Work</option>
                    <option value="Rently">Project: Rently</option>
                    <option value="Green Market">Project: Green Market</option>
                    <option value="MediLink">Project: MediLink</option>
                    <option value="Fitness Coach">Project: Fitness Coach</option>
                    <option value="Design dashboard UI">Task: Design dashboard UI</option>
                    <option value="Client meeting">Task: Client meeting</option>
                </select>
            </div>

            <div class="timer-controls-row">
                <button type="button" class="timer-btn timer-btn-start" id="timerStartBtn" onclick="toggleTimer()">
                    <span id="timerStartBtnIcon">▶</span> <span id="timerStartBtnText">Start Timer</span>
                </button>
                <button type="button" class="timer-btn timer-btn-reset" onclick="resetTimer()">
                    ↺ Reset
                </button>
                <button type="button" class="timer-btn timer-btn-save" onclick="saveTimerSession()">
                    💾 Log Session
                </button>
            </div>
        </div>
        <div class="dash-modal-footer">
            <button type="button" class="dash-btn-cancel" onclick="closeDashModal('timerModal')">Close</button>
        </div>
    </div>
</div>

<!-- 7. PROJECT DETAILS & UPDATE PROGRESS MODAL -->
<div class="dash-modal-overlay" id="projectDetailModal" role="dialog" aria-modal="true" aria-labelledby="projectDetailModalTitle">
    <div class="dash-modal-box">
        <div class="dash-modal-header">
            <div class="dash-modal-title-wrap">
                <span class="dash-modal-icon">📁</span>
                <h3 class="dash-modal-title" id="projectDetailModalTitle">Project Details</h3>
            </div>
            <button type="button" class="dash-modal-close-btn" onclick="closeDashModal('projectDetailModal')" aria-label="Close modal">&times;</button>
        </div>
        <div class="dash-modal-body">
            <div class="task-modal-detail-card">
                <div class="task-modal-field">
                    <span class="task-modal-label">Project Name</span>
                    <h4 class="task-modal-title-val" id="projModalName">-</h4>
                    <small id="projModalSub" style="color: #64748b; font-size: 12px;">-</small>
                </div>
                <div class="task-modal-meta-grid">
                    <div class="task-modal-meta-item">
                        <span class="task-modal-label">Current Progress</span>
                        <span class="task-modal-val" id="projModalProgVal">0%</span>
                    </div>
                    <div class="task-modal-meta-item">
                        <span class="task-modal-label">Due Date</span>
                        <span class="task-modal-val" id="projModalDue">-</span>
                    </div>
                </div>
            </div>

            <div class="dash-form-group" style="margin-top: 14px;">
                <label for="projModalProgInput">Update Progress (0 - 100%)</label>
                <div style="display: flex; gap: 10px; align-items: center;">
                    <input type="range" id="projModalProgInput" min="0" max="100" value="50" style="flex: 1;" oninput="document.getElementById('projModalProgDisplay').textContent = this.value + '%'">
                    <strong id="projModalProgDisplay" style="font-size: 14px; min-width: 45px; text-align: right; color: #023859;">50%</strong>
                </div>
                <p style="font-size: 11.5px; color: #64748b; margin-top: 4px;">
                    💡 Setting progress to <strong>100%</strong> will complete the project and remove it from the active table.
                </p>
            </div>
        </div>
        <div class="dash-modal-footer">
            <button type="button" class="dash-btn-cancel" onclick="closeDashModal('projectDetailModal')">Cancel</button>
            <button type="button" class="dash-btn-submit" onclick="saveProjectProgress()">Save Progress</button>
            <button type="button" class="dash-btn-submit dash-btn-confirm-complete" onclick="markProject100()">Mark 100% Complete</button>
        </div>
    </div>
</div>

<!-- 8. UPLOADED FILES LIST MODAL -->
<div class="dash-modal-overlay" id="uploadedFilesModal" role="dialog" aria-modal="true" aria-labelledby="uploadedFilesModalTitle">
    <div class="dash-modal-box uploaded-files-modal-box">
        <div class="dash-modal-header">
            <div class="dash-modal-title-wrap">
                <span class="dash-modal-icon">📁</span>
                <div style="display: flex; align-items: center; gap: 8px;">
                    <h3 class="dash-modal-title" id="uploadedFilesModalTitle">Uploaded Files &amp; Documents</h3>
                    <span class="files-count-badge" id="uploadedFilesCountBadge">0 files</span>
                </div>
            </div>
            <button type="button" class="dash-modal-close-btn" onclick="closeDashModal('uploadedFilesModal')" aria-label="Close modal">&times;</button>
        </div>
        <div class="dash-modal-body" style="padding: 16px 20px;">
            <div class="files-toolbar">
                <div class="files-search-wrap">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                    <input type="text" id="filesSearchInput" placeholder="Search files by name..." oninput="filterUploadedFilesList()">
                </div>
                <select id="filesCategoryFilter" class="dash-form-select files-filter-select" onchange="filterUploadedFilesList()">
                    <option value="">All Categories</option>
                    <option value="Policies">Company Policies</option>
                    <option value="Employees">Employee Records</option>
                    <option value="Projects">Project Documents</option>
                    <option value="Reports">Financial &amp; Reports</option>
                    <option value="General">General Documents</option>
                </select>
                <button type="button" class="dash-btn-submit files-upload-quick-btn" onclick="closeDashModal('uploadedFilesModal'); openUploadFileModal();">
                    + Upload
                </button>
            </div>

            <div class="uploaded-files-scroll-list" id="uploadedFilesContainer">
                <!-- Rendered dynamically -->
            </div>
        </div>
        <div class="dash-modal-footer">
            <button type="button" class="dash-btn-cancel" onclick="closeDashModal('uploadedFilesModal')">Close</button>
        </div>
    </div>
</div>

<!-- Floating Toast -->
<div class="dash-toast" id="dashToast" role="status" aria-live="polite">
    <span id="dashToastIcon">✓</span>
    <span id="dashToastMsg">Action completed successfully</span>
</div>

<script src="../assets/js/theme.js?v=1"></script>
<script src="../assets/js/dashboard.js?v=<?= time() ?>"></script>
</body>
</html>
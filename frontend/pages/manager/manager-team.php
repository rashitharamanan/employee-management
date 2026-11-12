<?php
// frontend/pages/manager/manager-team.php
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Team — Mindazzle Manager Portal</title>
    <meta name="description" content="Manage your team, assign tasks, track workload, and monitor performance progress in Mindazzle Manager Portal.">

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

                <!-- Card 1: Total Team Members -->
                <div class="mgr-kpi-card" id="kpiCardMembers">
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
                            <span class="mgr-kpi-label">Total Team Members</span>
                            <div class="mgr-kpi-value-row">
                                <span class="mgr-kpi-value" id="kpiTotalMembers">12</span>
                                <span class="mgr-trend-pill-green" id="kpiTotalTrend">▲ 2</span>
                            </div>
                            <span class="mgr-kpi-subtext" id="kpiTotalSub">Compared to last month</span>
                        </div>
                    </div>
                </div>

                <!-- Card 2: Present Today -->
                <div class="mgr-kpi-card" id="kpiCardPresent">
                    <div class="mgr-kpi-inner">
                        <div class="mgr-kpi-icon-circle kpi-circle-blue">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                <circle cx="12" cy="7" r="4"></circle>
                            </svg>
                        </div>
                        <div class="mgr-kpi-text-block">
                            <span class="mgr-kpi-label">Present Today</span>
                            <div class="mgr-kpi-value-row">
                                <span class="mgr-kpi-value" id="kpiPresentToday">9</span>
                                <span class="mgr-pill-blue" id="kpiPresentPct">▲ 75%</span>
                            </div>
                            <span class="mgr-kpi-subtext" id="kpiPresentSub">Out of 12 members</span>
                        </div>
                    </div>
                </div>

                <!-- Card 3: On Leave -->
                <div class="mgr-kpi-card" id="kpiCardLeave">
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
                                <span class="mgr-kpi-value" id="kpiOnLeave">2</span>
                            </div>
                            <span class="mgr-kpi-subtext" id="kpiOnLeaveSub">Today</span>
                        </div>
                    </div>
                </div>

                <!-- Card 4: Pending Tasks -->
                <div class="mgr-kpi-card" id="kpiCardTasks">
                    <div class="mgr-kpi-inner">
                        <div class="mgr-kpi-icon-circle kpi-circle-lavender">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                                <polyline points="22 4 12 14.01 9 11.01"></polyline>
                            </svg>
                        </div>
                        <div class="mgr-kpi-text-block">
                            <span class="mgr-kpi-label">Pending Tasks</span>
                            <div class="mgr-kpi-value-row">
                                <span class="mgr-kpi-value" id="kpiPendingTasks">8</span>
                            </div>
                            <span class="mgr-kpi-subtext" id="kpiPendingSub">Across team</span>
                        </div>
                    </div>
                </div>

            </section>


            <!-- -------------------------------------------------
                 ROW 2: TEAM MEMBERS TABLE (LEFT) & ASSIGN TASK FORM (RIGHT)
            -------------------------------------------------- -->
            <section class="mgr-team-layout-grid">

                <!-- LEFT: Team Members Card -->
                <div class="mgr-card" style="margin-bottom:0;">
                    <div class="mgr-card-header-flex">
                        <div class="mgr-card-header-left">
                            <div class="mgr-card-icon-badge blue">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
                                </svg>
                            </div>
                            <h2 class="mgr-card-title" style="margin:0;font-size:15px;font-weight:800;color:#0f172a;">Team Members</h2>
                        </div>

                        <!-- Search input for members -->
                        <div class="mgr-table-search">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#64748b" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="11" cy="11" r="8"></circle>
                                <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                            </svg>
                            <input type="text" id="memberSearchInput" placeholder="Search team members..." autocomplete="off" />
                        </div>
                    </div>

                    <div class="mgr-card-body mgr-team-table-scroll" style="padding:0;overflow-x:auto;overflow-y:auto;max-height:440px;">
                        <table class="mgr-table" style="margin:0;">
                            <thead>
                                <tr>
                                    <th style="width:40px;">#</th>
                                    <th>Name</th>
                                    <th>Designation</th>
                                    <th>Department</th>
                                    <th>Status</th>
                                    <th>Workload</th>
                                    <th>Performance</th>
                                    <th style="text-align:right;">Action</th>
                                </tr>
                            </thead>
                            <tbody id="mgrTeamTableBody">
                                <!-- Populated dynamically by manager-team.js -->
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- RIGHT: Assign Task Card -->
                <div class="mgr-assign-card">
                    <div class="mgr-card-header-left" style="margin-bottom:18px;">
                        <div class="mgr-card-icon-badge green">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M2.01 21L23 12 2.01 3 2 10l15 2-15 2z"/>
                            </svg>
                        </div>
                        <h2 class="mgr-card-title" style="margin:0;font-size:15px;font-weight:800;color:#0f172a;">Assign Task</h2>
                    </div>

                    <form id="mgrAssignTaskForm">
                        <!-- Select Employee -->
                        <div class="mgr-form-group">
                            <label class="mgr-form-label" for="taskSelectEmployee">Assign To <span class="req">*</span></label>
                            <select id="taskSelectEmployee" class="mgr-form-select" required>
                                <option value="">Choose employee or team</option>
                                <option value="TEAM" style="font-weight:700;color:#0284c7;">👥 Entire Team (All Members)</option>
                            </select>
                        </div>

                        <!-- Task Title -->
                        <div class="mgr-form-group">
                            <label class="mgr-form-label" for="taskTitle">Task Title <span class="req">*</span></label>
                            <input type="text" id="taskTitle" class="mgr-form-input" placeholder="Enter task title" required />
                        </div>

                        <!-- Description -->
                        <div class="mgr-form-group">
                            <label class="mgr-form-label" for="taskDescription">Description</label>
                            <textarea id="taskDescription" class="mgr-form-textarea" placeholder="Enter task description..."></textarea>
                        </div>

                        <!-- Priority, Start Date, Due Date -->
                        <div class="mgr-form-row-3">
                            <div class="mgr-form-group" style="margin-bottom:0;">
                                <label class="mgr-form-label" for="taskPriority">Priority</label>
                                <select id="taskPriority" class="mgr-form-select">
                                    <option value="Medium">Medium</option>
                                    <option value="High">High</option>
                                    <option value="Low">Low</option>
                                </select>
                            </div>

                            <div class="mgr-form-group" style="margin-bottom:0;">
                                <label class="mgr-form-label" for="taskStartDate">Start Date</label>
                                <div class="mgr-input-icon-wrap">
                                    <input type="date" id="taskStartDate" class="mgr-form-input" />
                                </div>
                            </div>

                            <div class="mgr-form-group" style="margin-bottom:0;">
                                <label class="mgr-form-label" for="taskDueDate">Due Date</label>
                                <div class="mgr-input-icon-wrap">
                                    <input type="date" id="taskDueDate" class="mgr-form-input" />
                                </div>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <button type="submit" id="mgrAssignTaskBtn" class="mgr-btn-primary-block">
                            <span>Assign Task</span>
                        </button>
                    </form>
                </div>

            </section>


            <!-- -------------------------------------------------
                 ROW 3: RECENT TASKS (LEFT) & EMPOWER YOUR TEAM BANNER (RIGHT)
            -------------------------------------------------- -->
            <section class="mgr-team-layout-grid">

                <!-- LEFT: Recent Tasks Card -->
                <div class="mgr-card" style="margin-bottom:0;">
                    <div class="mgr-card-header-flex">
                        <div class="mgr-card-header-left">
                            <div class="mgr-card-icon-badge green">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M19 3H5c-1.11 0-2 .9-2 2v14c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-9 14l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                                </svg>
                            </div>
                            <h2 class="mgr-card-title" style="margin:0;font-size:15px;font-weight:800;color:#0f172a;">Recent Tasks</h2>
                        </div>

                        <a href="javascript:void(0)" class="mgr-header-link" onclick="showToast('Viewing all 8 team tasks')">View All</a>
                    </div>

                    <div class="mgr-card-body mgr-tasks-table-scroll" style="padding:0;overflow-x:auto;overflow-y:auto;max-height:380px;">
                        <table class="mgr-table" style="margin:0;">
                            <thead>
                                <tr>
                                    <th style="width:40px;">#</th>
                                    <th>Task Title</th>
                                    <th>Assigned To</th>
                                    <th>Priority</th>
                                    <th>Due Date</th>
                                    <th>Status</th>
                                    <th>Progress</th>
                                    <th style="text-align:right;">Action</th>
                                </tr>
                            </thead>
                            <tbody id="mgrTasksTableBody">
                                <!-- Populated dynamically by manager-team.js -->
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- RIGHT: Empower Your Team Banner Card -->
                <div class="mgr-empower-card">
                    <div class="mgr-empower-content">
                        <h3 class="mgr-empower-title">Empower<br>Your Team</h3>
                        <p class="mgr-empower-sub">Assign meaningful work, track progress, and achieve goals together.</p>
                    </div>

                    <div class="mgr-empower-art">
                        <svg width="210" height="150" viewBox="0 0 240 170" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <!-- Background soft glow circles -->
                            <circle cx="210" cy="50" r="40" fill="#a7f3d0" opacity="0.4"/>
                            
                            <!-- Growth Bar Chart -->
                            <rect x="155" y="90" width="10" height="30" rx="3" fill="#a7f3d0"/>
                            <rect x="170" y="70" width="10" height="50" rx="3" fill="#6ee7b7"/>
                            <rect x="185" y="45" width="10" height="75" rx="3" fill="#34d399"/>
                            <rect x="200" y="25" width="10" height="95" rx="3" fill="#10b981"/>

                            <!-- Green Upward Trend Arrow -->
                            <path d="M152 75 L175 55 L190 60 L212 28" stroke="#059669" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/>
                            <polygon points="212 28 204 31 213 37" fill="#059669"/>

                            <!-- Lush Potted Plant (Right) -->
                            <ellipse cx="225" cy="135" rx="14" ry="5" fill="#e2e8f0"/>
                            <path d="M225 130 C220 100 235 90 235 70" stroke="#059669" stroke-width="2.5" stroke-linecap="round"/>
                            <path d="M225 130 C235 110 240 100 238 85" stroke="#10b981" stroke-width="2.5" stroke-linecap="round"/>
                            <!-- Plant Leaves -->
                            <ellipse cx="233" cy="80" rx="6" ry="12" transform="rotate(25 233 80)" fill="#10b981"/>
                            <ellipse cx="225" cy="98" rx="5" ry="10" transform="rotate(-30 225 98)" fill="#34d399"/>
                            <ellipse cx="238" cy="95" rx="5" ry="10" transform="rotate(35 238 95)" fill="#059669"/>

                            <!-- Desk Surface -->
                            <path d="M30 148 L230 148" stroke="#0d9488" stroke-width="3" stroke-linecap="round"/>

                            <!-- Employee Working on Laptop -->
                            <!-- Head & Hair -->
                            <circle cx="118" cy="62" r="14" fill="#fed7aa"/>
                            <path d="M106 58 C106 48 116 46 126 46 C134 46 135 52 133 60 C130 54 126 50 118 52 Z" fill="#1e293b"/>
                            <!-- Face features -->
                            <circle cx="114" cy="62" r="1.5" fill="#475569"/>
                            <path d="M112 66 Q115 69 118 67" stroke="#475569" stroke-width="1.2" stroke-linecap="round" fill="none"/>

                            <!-- Neck & Torso (Green Shirt) -->
                            <rect x="115" y="74" width="7" height="6" fill="#fed7aa"/>
                            <path d="M100 80 C100 76 108 75 118 75 C128 75 136 76 136 80 L140 120 L96 120 Z" fill="#0d9488"/>
                            <polygon points="114 75 118 82 122 75" fill="#fed7aa"/>

                            <!-- Arms & Hands -->
                            <path d="M100 84 L85 115 L102 120" stroke="#0d9488" stroke-width="7" stroke-linecap="round" stroke-linejoin="round"/>
                            <circle cx="102" cy="120" r="4" fill="#fed7aa"/>
                            <path d="M136 84 L142 112 L120 120" stroke="#0d9488" stroke-width="7" stroke-linecap="round" stroke-linejoin="round"/>
                            <circle cx="120" cy="120" r="4" fill="#fed7aa"/>

                            <!-- Laptop -->
                            <!-- Screen -->
                            <polygon points="56 94 98 94 92 124 50 124" fill="#0f172a"/>
                            <polygon points="58 96 96 96 91 122 53 122" fill="#1e293b"/>
                            <!-- Screen logo dot -->
                            <circle cx="74" cy="108" r="2.5" fill="#38bdf8"/>
                            <!-- Keyboard Base -->
                            <polygon points="46 124 104 124 116 129 34 129" fill="#334155"/>
                        </svg>
                    </div>
                </div>

            </section>

        </main>

    </div>

</div>

<!-- =========================================================
     MODALS
========================================================== -->

<!-- 1. TEAM MEMBER DETAIL MODAL -->
<div class="mgr-modal-backdrop" id="mgrMemberModal">
    <div class="mgr-modal-content">
        <div class="mgr-modal-header">
            <h3 class="mgr-modal-title">Team Member Profile</h3>
            <button class="mgr-modal-close" type="button" aria-label="Close modal">&times;</button>
        </div>
        <div id="mgrMemberModalBody">
            <!-- Populated dynamically -->
        </div>
        <div class="mgr-modal-footer">
            <button type="button" class="mgr-btn-view" onclick="closeModals()">Close</button>
        </div>
    </div>
</div>

<!-- 2. TASK DETAIL & PROGRESS MODAL -->
<div class="mgr-modal-backdrop" id="mgrTaskModal">
    <div class="mgr-modal-content">
        <div class="mgr-modal-header">
            <h3 class="mgr-modal-title">Task Details</h3>
            <button class="mgr-modal-close" type="button" aria-label="Close modal">&times;</button>
        </div>
        <div id="mgrTaskModalBody">
            <!-- Populated dynamically -->
        </div>
        <div class="mgr-modal-footer">
            <button type="button" class="mgr-btn-view" onclick="closeModals()">Done</button>
        </div>
    </div>
</div>

<!-- SCRIPTS -->
<script src="../../assets/js/theme.js?v=<?= time() ?>"></script>
<script src="../../assets/js/manager-team.js?v=<?= time() ?>"></script>

</body>
</html>

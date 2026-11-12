<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="HR Leave Management — Review, approve, and manage employee leave requests.">
    <title>Leave Management - HR Portal</title>
    <link rel="stylesheet" href="../assets/css/app-theme.css?v=<?= time() ?>">
    <link rel="stylesheet" href="../assets/css/sidebar.css?v=<?= time() ?>">
    <link rel="stylesheet" href="../assets/css/topbar.css?v=<?= time() ?>">
    <link rel="stylesheet" href="../assets/css/leave-management.css?v=<?= time() ?>">
</head>
<body>
<div class="app">
    <?php include '../includes/sidebar.php'; ?>

    <main class="main-content">
        <?php include '../includes/topbar.php'; ?>

        <section class="leave-page">

            <!-- 4 KPI Hero Cards with 3D Illustrations in LUNA Color Palette -->
            <div class="leave-kpis-grid" id="leaveKpis">
                
                <!-- Hidden fallback element for total requests counter in JS -->
                <span id="kpiTotalRequests" style="display: none;"></span>

                <!-- Card 1: Leave Types (LUNA Midnight Navy: #023859 → #011C40) -->
                <article class="kpi-hero-card kpi-card-types">
                    <div class="kpi-hero-left">
                        <div class="kpi-hero-titles">
                            <span class="kpi-hero-label">Leave Types</span>
                            <div class="kpi-hero-value" id="kpiTotalTypes">—</div>
                        </div>
                        <div class="kpi-hero-action">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6"><line x1="7" y1="17" x2="17" y2="7"/><polyline points="7 7 17 7 17 17"/></svg>
                            <span>LEARN MORE</span>
                        </div>
                    </div>
                    <div class="kpi-hero-3d">
                        <svg width="120" height="120" viewBox="0 0 120 120" fill="none" xmlns="http://www.w3.org/2000/svg" class="kpi-3d-svg">
                            <defs>
                                <filter id="f1-shadow" x="-30%" y="-30%" width="170%" height="170%">
                                    <feDropShadow dx="-6" dy="10" stdDeviation="7" flood-color="#000000" flood-opacity="0.38"/>
                                </filter>
                                <linearGradient id="f1-folder-back" x1="0" y1="0" x2="1" y2="1">
                                    <stop offset="0%" stop-color="#FFFFFF"/>
                                    <stop offset="100%" stop-color="#CBD5E1"/>
                                </linearGradient>
                                <linearGradient id="f1-sheet" x1="0" y1="0" x2="0" y2="1">
                                    <stop offset="0%" stop-color="#F8FAFC"/>
                                    <stop offset="100%" stop-color="#E2E8F0"/>
                                </linearGradient>
                                <linearGradient id="f1-folder-front" x1="0" y1="0" x2="1" y2="1">
                                    <stop offset="0%" stop-color="#FFFFFF"/>
                                    <stop offset="60%" stop-color="#E2E8F0"/>
                                    <stop offset="100%" stop-color="#94A3B8"/>
                                </linearGradient>
                            </defs>
                            <path d="M22 42C22 36.4772 26.4772 32 32 32H50L58 40H92C97.5228 40 102 44.4772 102 50V90C102 95.5228 97.5228 100 92 100H32C26.4772 100 22 95.5228 22 90V42Z" fill="url(#f1-folder-back)" filter="url(#f1-shadow)"/>
                            <rect x="30" y="24" width="64" height="60" rx="6" transform="rotate(-6 30 24)" fill="url(#f1-sheet)" filter="url(#f1-shadow)"/>
                            <rect x="36" y="32" width="40" height="4" rx="2" transform="rotate(-6 36 32)" fill="#94A3B8" opacity="0.6"/>
                            <rect x="35" y="40" width="30" height="4" rx="2" transform="rotate(-6 35 40)" fill="#94A3B8" opacity="0.4"/>
                            <path d="M18 56C18 50.4772 22.4772 46 28 46H96C101.523 46 106 50.4772 106 56L102 94C102 99.5228 97.5228 104 92 104H24C18.4772 104 14 99.5228 14 94L18 56Z" fill="url(#f1-folder-front)" filter="url(#f1-shadow)"/>
                            <rect x="28" y="58" width="28" height="6" rx="3" fill="#FFFFFF" opacity="0.8"/>
                        </svg>
                    </div>
                </article>

                <!-- Card 2: Pending (LUNA Ocean Teal: #54ACBF → #26658C) -->
                <article class="kpi-hero-card kpi-card-pending">
                    <div class="kpi-hero-left">
                        <div class="kpi-hero-titles">
                            <span class="kpi-hero-label">Pending</span>
                            <div class="kpi-hero-value" id="kpiPendingRequests">—</div>
                        </div>
                        <div class="kpi-hero-action">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6"><line x1="7" y1="17" x2="17" y2="7"/><polyline points="7 7 17 7 17 17"/></svg>
                            <span>LEARN MORE</span>
                        </div>
                    </div>
                    <div class="kpi-hero-3d">
                        <svg width="120" height="120" viewBox="0 0 120 120" fill="none" xmlns="http://www.w3.org/2000/svg" class="kpi-3d-svg">
                            <defs>
                                <filter id="f3-shadow" x="-30%" y="-30%" width="170%" height="170%">
                                    <feDropShadow dx="-6" dy="10" stdDeviation="7" flood-color="#000000" flood-opacity="0.34"/>
                                </filter>
                                <linearGradient id="f3-clock-body" x1="0" y1="0" x2="1" y2="1">
                                    <stop offset="0%" stop-color="#FFFFFF"/>
                                    <stop offset="50%" stop-color="#F1F5F9"/>
                                    <stop offset="100%" stop-color="#94A3B8"/>
                                </linearGradient>
                                <linearGradient id="f3-bell" x1="0" y1="0" x2="1" y2="1">
                                    <stop offset="0%" stop-color="#FFFFFF"/>
                                    <stop offset="100%" stop-color="#64748B"/>
                                </linearGradient>
                            </defs>
                            <path d="M38 32C34 26 42 20 48 24L52 28L42 36L38 32Z" fill="url(#f3-bell)" filter="url(#f3-shadow)"/>
                            <path d="M82 32C86 26 78 20 72 24L68 28L78 36L82 32Z" fill="url(#f3-bell)" filter="url(#f3-shadow)"/>
                            <rect x="56" y="18" width="8" height="6" rx="2" fill="#E2E8F0"/>
                            <circle cx="60" cy="62" r="38" fill="url(#f3-clock-body)" filter="url(#f3-shadow)"/>
                            <circle cx="60" cy="62" r="30" fill="#F8FAFC"/>
                            <circle cx="60" cy="38" r="2" fill="#64748B"/>
                            <circle cx="60" cy="86" r="2" fill="#64748B"/>
                            <circle cx="36" cy="62" r="2" fill="#64748B"/>
                            <circle cx="84" cy="62" r="2" fill="#64748B"/>
                            <line x1="60" y1="62" x2="60" y2="45" stroke="#334155" stroke-width="3.5" stroke-linecap="round"/>
                            <line x1="60" y1="62" x2="74" y2="68" stroke="#334155" stroke-width="3" stroke-linecap="round"/>
                            <circle cx="60" cy="62" r="4" fill="#0F172A"/>
                            <line x1="42" y1="94" x2="36" y2="104" stroke="#94A3B8" stroke-width="4" stroke-linecap="round"/>
                            <line x1="78" y1="94" x2="84" y2="104" stroke="#94A3B8" stroke-width="4" stroke-linecap="round"/>
                        </svg>
                    </div>
                </article>

                <!-- Card 3: Approved (LUNA Slate to Navy: #26658C → #023859) -->
                <article class="kpi-hero-card kpi-card-approved">
                    <div class="kpi-hero-left">
                        <div class="kpi-hero-titles">
                            <span class="kpi-hero-label">Approved</span>
                            <div class="kpi-hero-value" id="kpiApprovedRequests">—</div>
                        </div>
                        <div class="kpi-hero-action">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6"><line x1="7" y1="17" x2="17" y2="7"/><polyline points="7 7 17 7 17 17"/></svg>
                            <span>LEARN MORE</span>
                        </div>
                    </div>
                    <div class="kpi-hero-3d">
                        <svg width="120" height="120" viewBox="0 0 120 120" fill="none" xmlns="http://www.w3.org/2000/svg" class="kpi-3d-svg">
                            <defs>
                                <filter id="f4-shadow" x="-30%" y="-30%" width="170%" height="170%">
                                    <feDropShadow dx="-6" dy="10" stdDeviation="7" flood-color="#000000" flood-opacity="0.32"/>
                                </filter>
                                <linearGradient id="f4-shield" x1="0" y1="0" x2="1" y2="1">
                                    <stop offset="0%" stop-color="#FFFFFF"/>
                                    <stop offset="60%" stop-color="#F1F5F9"/>
                                    <stop offset="100%" stop-color="#94A3B8"/>
                                </linearGradient>
                                <linearGradient id="f4-inner" x1="0" y1="0" x2="0" y2="1">
                                    <stop offset="0%" stop-color="#FFFFFF"/>
                                    <stop offset="100%" stop-color="#E2E8F0"/>
                                </linearGradient>
                            </defs>
                            <path d="M60 18L94 30V62C94 82 78 98 60 106C42 98 26 82 26 62V30L60 18Z" fill="url(#f4-shield)" filter="url(#f4-shadow)"/>
                            <path d="M60 25L88 35V61C88 78 74 91 60 98C46 91 32 78 32 61V35L60 25Z" fill="url(#f4-inner)"/>
                            <!-- Checkmark in glowing Ice Cyan #A7EBF2 (No Green!) -->
                            <path d="M46 58L55 67L74 46" stroke="#A7EBF2" stroke-width="6" stroke-linecap="round" stroke-linejoin="round" filter="url(#f4-shadow)"/>
                        </svg>
                    </div>
                </article>

                <!-- Card 4: On Leave (LUNA Palette: #54ACBF → #023859 → #011C40) -->
                <article class="kpi-hero-card kpi-card-onleave">
                    <div class="kpi-hero-left">
                        <div class="kpi-hero-titles">
                            <span class="kpi-hero-label">On Leave</span>
                            <div class="kpi-hero-value" id="kpiOnLeaveToday">—</div>
                        </div>
                        <div class="kpi-hero-action">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6"><line x1="7" y1="17" x2="17" y2="7"/><polyline points="7 7 17 7 17 17"/></svg>
                            <span>LEARN MORE</span>
                        </div>
                    </div>
                    <div class="kpi-hero-3d">
                        <svg width="120" height="120" viewBox="0 0 120 120" fill="none" xmlns="http://www.w3.org/2000/svg" class="kpi-3d-svg">
                            <defs>
                                <filter id="f5-shadow" x="-30%" y="-30%" width="170%" height="170%">
                                    <feDropShadow dx="-6" dy="10" stdDeviation="7" flood-color="#000000" flood-opacity="0.35"/>
                                </filter>
                                <linearGradient id="f5-luggage" x1="0" y1="0" x2="1" y2="1">
                                    <stop offset="0%" stop-color="#FFFFFF"/>
                                    <stop offset="65%" stop-color="#E2E8F0"/>
                                    <stop offset="100%" stop-color="#94A3B8"/>
                                </linearGradient>
                            </defs>
                            <path d="M48 26V18C48 15.7909 49.7909 14 52 14H68C70.2091 14 72 15.7909 72 18V26" stroke="#64748B" stroke-width="4" stroke-linecap="round"/>
                            <rect x="30" y="26" width="60" height="74" rx="12" fill="url(#f5-luggage)" filter="url(#f5-shadow)"/>
                            <line x1="44" y1="36" x2="44" y2="90" stroke="#CBD5E1" stroke-width="3" stroke-linecap="round"/>
                            <line x1="60" y1="36" x2="60" y2="90" stroke="#CBD5E1" stroke-width="3" stroke-linecap="round"/>
                            <line x1="76" y1="36" x2="76" y2="90" stroke="#CBD5E1" stroke-width="3" stroke-linecap="round"/>
                            <ellipse cx="42" cy="103" rx="4" ry="4" fill="#334155"/>
                            <ellipse cx="78" cy="103" rx="4" ry="4" fill="#334155"/>
                            <!-- Sun Badge in LUNA Ocean Teal #54ACBF -->
                            <circle cx="78" cy="40" r="9" fill="#54ACBF" filter="url(#f5-shadow)"/>
                            <path d="M78 34V36M78 44V46M72 40H74M82 40H84M74 36L75.5 37.5M80.5 42.5L82 44M74 44L75.5 42.5M80.5 37.5L82 36" stroke="#FFFFFF" stroke-width="1.8" stroke-linecap="round"/>
                        </svg>
                    </div>
                </article>

            </div>

            <!-- Leave Requests Section (Modern Cards Design - Seamless Canvas) -->
            <section class="leave-cards-section" style="margin-bottom: 32px;">
                <div class="leave-card-head" style="flex-wrap: wrap; gap: 16px; align-items: center;">
                    <!-- Left: Leaves / Permission Tabs matching reference -->
                    <div class="l-tabs-nav" role="tablist">
                        <button type="button" class="l-tab-btn active" id="tabLeavesBtn">Leave Permission</button>
                        <!-- <button type="button" class="l-tab-btn" id="tabPermissionBtn" title="Short leave / gate pass requests">Permission</button> -->
                        <span class="leave-count-pill" id="requestsCount">Loading...</span>
                    </div>

                    <!-- Right: Search, Nav < >, Status Pills All / Approved / Pending -->
                    <div class="leave-card-filters" style="flex-wrap: wrap; gap: 10px; align-items: center;">
                        <select id="requestScope" class="lm-filter-select" aria-label="Hierarchy Scope" style="display: none;">
                            <option value="hr_manager" selected>HR &amp; Manager → Admin</option>
                            <option value="all">All Requests</option>
                            <option value="employee">Employee → HR</option>
                        </select>

                        <div class="lm-search-box">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
                            <input id="requestSearch" type="search" placeholder="Search...">
                        </div>

                        <!-- Card Navigation Arrows < > -->
                        <div class="l-nav-arrows">
                            <button type="button" class="l-arrow-btn" id="prevCardPage" aria-label="Previous Page" title="Previous page">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="15 18 9 12 15 6"/></svg>
                            </button>
                            <button type="button" class="l-arrow-btn" id="nextCardPage" aria-label="Next Page" title="Next page">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"/></svg>
                            </button>
                        </div>

                        <!-- Segmented Status Pills matching reference image -->
                        <div class="l-status-pills" id="statusPillsGroup">
                            <button type="button" class="l-pill-btn" data-status="">All</button>
                            <button type="button" class="l-pill-btn" data-status="APPROVED">Approved</button>
                            <button type="button" class="l-pill-btn active" data-status="PENDING">Pending</button>
                            <button type="button" class="l-pill-btn" data-status="REJECTED">Rejected</button>
                        </div>

                        <!-- Hidden select for compatibility with state filters -->
                        <select id="requestStatus" style="display:none;">
                            <option value="PENDING" selected>Pending</option>
                            <option value="APPROVED">Approved</option>
                            <option value="REJECTED">Rejected</option>
                            <option value="">All</option>
                        </select>
                    </div>
                </div>

                <!-- Cards Grid Container -->
                <div class="leave-cards-grid" id="requestsCardsGrid">
                    <div class="cards-empty-state">Loading leave requests...</div>
                </div>

                <!-- Fallback hidden tbody if targeted by legacy selectors -->
                <tbody id="requestsBody" style="display:none;"></tbody>

                <!-- Pagination footer -->
                <div class="ltc-footer" style="margin-top: 18px;">
                    <span id="pageInfo">—</span>
                    <div class="pagination" id="paginationControls"></div>
                    <label class="per-page-label">Cards per page:
                        <select id="perPageSelect">
                            <option value="6" selected>6</option>
                            <option value="12">12</option>
                            <option value="24">24</option>
                        </select>
                    </label>
                </div>
            </section>

            <!-- Leave Types Section with + Add Leave Type button on the right -->
            <article class="leave-card-section">
                <div class="leave-card-head">
                    <div class="leave-card-head-left">
                        <h2 class="leave-section-title">Leave Types</h2>
                        <span class="leave-count-pill" id="leaveTypesCount">Loading...</span>
                    </div>
                    <button id="addLeaveTypeBtn" class="lm-primary-btn" type="button">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                        <span>Add Leave Type</span>
                    </button>
                </div>
                <div class="lm-table-wrap">
                    <table class="lm-table">
                        <thead>
                            <tr>
                                <th>Leave Type</th>
                                <th>Days</th>
                                <th>Type</th>
                                <th>Status</th>
                                <th style="text-align: right;">Action</th>
                            </tr>
                        </thead>
                        <tbody id="leaveTypesBody">
                            <tr><td colspan="5" class="empty-cell">Loading leave types...</td></tr>
                        </tbody>
                    </table>
                </div>
            </article>

        </section>
    </main>
</div>

<!-- Add Leave Type Modal -->
<div class="lm-modal" id="leaveTypeModal" aria-hidden="true">
    <div class="lm-modal-card">
        <div class="lm-modal-header">
            <div>
                <h2 id="leaveTypeModalTitle">Add Leave Type</h2>
                <p>Configure a leave category for employees.</p>
            </div>
            <button class="lm-close-btn" data-close="leaveTypeModal" aria-label="Close">&times;</button>
        </div>
        <form id="leaveTypeForm">
            <label class="lm-field">
                Leave Type Name
                <input id="leaveTypeName" type="text" maxlength="100" required placeholder="e.g. Casual Leave">
            </label>
            <label class="lm-field">
                Description
                <textarea id="leaveTypeDescription" rows="3" maxlength="255" placeholder="Optional description"></textarea>
            </label>
            <div class="lm-form-grid">
                <label class="lm-field">
                    Days / Year
                    <input id="leaveTypeDays" type="number" min="1" max="365" step="0.5" required value="12">
                </label>
                <label class="lm-field">
                    Status
                    <select id="leaveTypeStatus">
                        <option value="ACTIVE">Active</option>
                        <option value="INACTIVE">Inactive</option>
                    </select>
                </label>
            </div>
            <label class="lm-checkbox-row">
                <input id="leaveTypePaid" type="checkbox" checked>
                <span>Paid leave</span>
            </label>
            <p class="lm-error" id="leaveTypeError"></p>
            <div class="lm-modal-actions">
                <button type="button" class="lm-secondary-btn" data-close="leaveTypeModal">Cancel</button>
                <button type="submit" class="lm-primary-btn">Save Leave Type</button>
            </div>
        </form>
    </div>
</div>

<!-- Reject Reason Modal -->
<div class="lm-modal" id="rejectModal" aria-hidden="true">
    <div class="lm-modal-card lm-modal-sm">
        <div class="lm-modal-header">
            <div>
                <h2>Reject Leave Request</h2>
                <p>Provide a reason for rejection.</p>
            </div>
            <button class="lm-close-btn" data-close="rejectModal" aria-label="Close">&times;</button>
        </div>
        <label class="lm-field">
            Rejection Reason <span style="color:var(--lm-red)">*</span>
            <textarea id="rejectReason" rows="3" placeholder="e.g. Insufficient leave balance or team bandwidth constraint."></textarea>
        </label>
        <p class="lm-error" id="rejectError"></p>
        <div class="lm-modal-actions" style="margin-top:14px">
            <button type="button" class="lm-secondary-btn" data-close="rejectModal">Cancel</button>
            <button type="button" class="lm-danger-btn" id="confirmRejectBtn">Confirm Rejection</button>
        </div>
    </div>
</div>

<!-- View Request Modal -->
<div class="lm-modal" id="viewRequestModal" aria-hidden="true">
    <div class="lm-modal-card">
        <div class="lm-modal-header">
            <div>
                <h2 id="viewRequestTitle">Leave Request</h2>
                <p id="viewRequestCode" class="lm-request-code-badge">—</p>
            </div>
            <button class="lm-close-btn" data-close="viewRequestModal" aria-label="Close">&times;</button>
        </div>
        <div id="viewRequestBody" class="view-request-body"></div>
        <div class="lm-modal-actions" style="margin-top:14px">
            <button type="button" class="lm-secondary-btn" data-close="viewRequestModal">Close</button>
            <button type="button" class="lm-success-btn" id="viewApproveBtn" style="display:none">Approve</button>
            <button type="button" class="lm-danger-btn" id="viewRejectBtn" style="display:none">Reject</button>
        </div>
    </div>
</div>

<div class="lm-toast" id="toast" role="status"></div>

<script src="../assets/js/theme.js?v=<?= time() ?>"></script>
<script src="../assets/js/leave-management.js?v=<?= time() ?>"></script>
</body>
</html>

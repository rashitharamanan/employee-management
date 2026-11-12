<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="HR Leave Management — Review, approve, and manage employee leave requests.">
    <title>Leave Management - HR Portal</title>
    <link rel="stylesheet" href="../assets/css/app-theme.css?v=<?= time() ?>">
    <link rel="stylesheet" href="../assets/css/hr-shell.css?v=<?= time() ?>">
    <link rel="stylesheet" href="../assets/css/hr-leave.css?v=<?= time() ?>">
</head>
<body>
<div class="app">
    <?php include '../includes/hr-sidebar.php'; ?>

    <main class="main-content">
        <?php include '../includes/hr-topbar.php'; ?>

        <section class="leave-page hr-leave-page">

            <!-- Page Heading -->
            <div class="leave-heading">
                <div class="leave-heading-left">
                    <h1>Leave Management</h1>
                </div>
                <button id="addLeaveTypeBtn" class="hr-primary-action lm-primary-btn" type="button">
                    <span>Add Leave Type</span>
                </button>
            </div>

            <!-- Top Metric Stat Cards (Violet & Pink Palette matching HR Dashboard & Employees) -->
            <section class="hr-kpi-row leave-kpi-row leave-stats-row">
                <!-- Total Requests (Deep Plum Violet) -->
                <article class="hr-kpi-pill stat-hr-plum">
                    <div class="stat-wave"></div>
                    <div class="stat-icon-wrapper">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                            <polyline points="14 2 14 8 20 8"/>
                            <line x1="16" y1="13" x2="8" y2="13"/>
                            <line x1="16" y1="17" x2="8" y2="17"/>
                        </svg>
                    </div>
                    <div class="kpi-pill-content">
                        <span class="kpi-pill-label">Total Requests</span>
                        <strong class="kpi-pill-val" id="kpiTotalRequests">—</strong>
                        <span class="stat-badge">This Month</span>
                    </div>
                </article>

                <!-- Pending Review (Magenta Wine) -->
                <article class="hr-kpi-pill stat-hr-magenta">
                    <div class="stat-wave"></div>
                    <div class="stat-icon-wrapper">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="10"/>
                            <polyline points="12 6 12 12 16 14"/>
                        </svg>
                    </div>
                    <div class="kpi-pill-content">
                        <span class="kpi-pill-label">Pending Review</span>
                        <strong class="kpi-pill-val" id="kpiPendingRequests">—</strong>
                        <span class="stat-badge">Awaiting Action</span>
                    </div>
                </article>

                <!-- Approved (Vibrant Pink) -->
                <article class="hr-kpi-pill stat-hr-pink">
                    <div class="stat-wave"></div>
                    <div class="stat-icon-wrapper">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                            <polyline points="22 4 12 14.01 9 11.01"/>
                        </svg>
                    </div>
                    <div class="kpi-pill-content">
                        <span class="kpi-pill-label">Approved</span>
                        <strong class="kpi-pill-val" id="kpiApprovedRequests">—</strong>
                        <span class="stat-badge">This Month</span>
                    </div>
                </article>

                <!-- On Leave Today (Royal Purple) -->
                <article class="hr-kpi-pill stat-hr-purple">
                    <div class="stat-wave"></div>
                    <div class="stat-icon-wrapper">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                            <circle cx="9" cy="7" r="4"/>
                            <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
                            <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                        </svg>
                    </div>
                    <div class="kpi-pill-content">
                        <span class="kpi-pill-label">On Leave Today</span>
                        <strong class="kpi-pill-val" id="kpiOnLeaveToday">—</strong>
                        <span class="stat-badge">Currently Absent</span>
                    </div>
                </article>
            </section>

            <!-- Leave Requests Cards Section (Card Grid matching Image 1) -->
            <section class="leave-cards-section">
                <!-- Controls Bar with Search, Segmented Status Pills, and Nav Arrows -->
                <div class="leave-controls-bar">
                    <div class="lcb-left">
                        <h2 class="lcb-title">Leave Requests</h2>
                        <span class="lcb-count-badge" id="requestsCount">Loading...</span>
                    </div>

                    <div class="lcb-right">
                        <div class="lcb-search-wrap">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
                            <input id="requestSearch" type="search" class="lcb-search-input" placeholder="Search employee, ID, reason...">
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
                            <button type="button" class="l-pill-btn active" data-status="PENDING">Pending</button>
                            <button type="button" class="l-pill-btn" data-status="APPROVED">Approved</button>
                            <button type="button" class="l-pill-btn" data-status="REJECTED">Rejected</button>
                        </div>

                        <!-- Hidden select for compatibility with state filters -->
                        <select id="requestStatus" style="display:none;">
                            <option value="PENDING" selected>Pending</option>
                            <option value="APPROVED">Approved</option>
                            <option value="REJECTED">Rejected</option>
                            <option value="">All</option>
                        </select>

                        <select id="requestScope" class="lm-filter-select" aria-label="Hierarchy Scope" style="display: none;">
                            <option value="employee" selected>Employee → HR</option>
                            <option value="all">All Requests</option>
                        </select>
                    </div>
                </div>

                <!-- Cards Grid Container (Renders cards matching Image 1) -->
                <div class="leave-cards-grid" id="requestsCardsGrid">
                    <div class="cards-empty-state">Loading leave requests...</div>
                </div>

                <!-- Hidden tbody for backward compatibility -->
                <tbody id="requestsBody" style="display:none;"></tbody>

                <!-- Pagination footer matching Image 1 -->
                <div class="ltc-footer" style="margin-top: 24px;">
                    <span id="pageInfo">—</span>
                    <div class="pagination" id="paginationControls"></div>
                    <label class="per-page-label">Cards per page:
                        <select id="perPageSelect">
                            <option value="8" selected>8</option>
                            <option value="12">12</option>
                            <option value="16">16</option>
                            <option value="24">24</option>
                        </select>
                    </label>
                </div>
            </section>

            <!-- Leave Categories Section (Modern chip cards without bulky tables) -->
            <section class="leave-types-section" style="margin-top: 36px;">
                <div class="leave-types-header">
                    <div class="lth-left">
                        <h3 class="lth-title">Leave Categories</h3>
                        <span class="lcb-count-badge" id="leaveTypesCount">Loading...</span>
                    </div>
                </div>
                <div class="leave-types-grid" id="leaveTypesGrid">
                    <div class="cards-empty-state">Loading leave categories...</div>
                </div>
                <tbody id="leaveTypesBody" style="display:none;"></tbody>
            </section>

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
            <label class="lm-field">Leave Type Name
                <input id="leaveTypeName" type="text" maxlength="100" required placeholder="e.g. Casual Leave">
            </label>
            <label class="lm-field">Description
                <textarea id="leaveTypeDescription" rows="3" maxlength="255" placeholder="Optional description"></textarea>
            </label>
            <div class="lm-form-grid">
                <label class="lm-field">Days / Year
                    <input id="leaveTypeDays" type="number" min="1" max="365" step="0.5" required value="12">
                </label>
                <label class="lm-field">Status
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
        <label class="lm-field">Rejection Reason <span style="color:var(--lm-red)">*</span>
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
            <button type="button" class="lm-success-btn" id="viewApproveBtn" style="display:none">✓ Approve</button>
            <button type="button" class="lm-danger-btn" id="viewRejectBtn" style="display:none">✗ Reject</button>
        </div>
    </div>
</div>

<div class="lm-toast" id="toast" role="status"></div>
<script src="../assets/js/theme.js?v=<?= time() ?>"></script>
<script src="../assets/js/hr-leave.js?v=<?= time() ?>"></script>
</body>
</html>

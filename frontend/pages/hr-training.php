<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Training Management — Manage training programs, sessions, and enrollments.">
    <title>Training Management - HR Portal</title>
    <link rel="stylesheet" href="../assets/css/app-theme.css?v=<?= time() ?>">
    <link rel="stylesheet" href="../assets/css/hr-shell.css?v=<?= time() ?>">
    <link rel="stylesheet" href="../assets/css/hr-employees.css?v=<?= time() ?>">
    <link rel="stylesheet" href="../assets/css/hr-performance.css?v=<?= time() ?>">
    <link rel="stylesheet" href="../assets/css/hr-training.css?v=<?= time() ?>">
</head>
<body>
<div class="app">
    <?php include '../includes/hr-sidebar.php'; ?>

    <main class="main-content">
        <?php include '../includes/hr-topbar.php'; ?>

        <section class="tr-page">

            <!-- COMPACT TOP BAR & ACTIONS -->
            <div class="tr-top-bar pf-top-bar">
                <div class="tr-top-badge pf-top-badge">
                    <span class="tr-pulse-dot pf-pulse-dot"></span>
                    <span>Training &amp; Development</span>
                </div>
                <div class="pf-header-actions tr-heading-actions">
                    <button type="button" id="newProgramBtn" class="pf-btn pf-btn-light">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                        <span>New Program</span>
                    </button>
                    <button type="button" id="newEnrollmentBtn" class="pf-btn pf-btn-primary">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                        <span>New Enrollment</span>
                    </button>
                </div>
            </div>

            <div class="tr-error" id="trError" style="display:none"></div>

            <!-- =================================================
                 1. SIX SIGNATURE HR KPI CARDS (Rich Violet & Pink Gradients)
            ================================================== -->
            <section class="employee-kpis tr-kpis" id="trainingKpis">

                <!-- 1. Total Programs (Plum Theme) -->
                <div class="employee-kpi kpi-theme-plum">
                    <div class="kpi-wave"></div>
                    <div class="kpi-icon-box">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M22 10v6M2 10l10-5 10 5-10 5z"/>
                            <path d="M6 12v5c3 3 9 3 12 0v-5"/>
                        </svg>
                    </div>
                    <div class="kpi-details">
                        <span class="kpi-label">Total Programs</span>
                        <strong class="kpi-value" id="kpiTotalPrograms">—</strong>
                        <span class="kpi-pill-badge">All Programs</span>
                    </div>
                </div>

                <!-- 2. Active Programs (Violet Theme) -->
                <div class="employee-kpi kpi-theme-violet">
                    <div class="kpi-wave"></div>
                    <div class="kpi-icon-box">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <polygon points="5 3 19 12 5 21 5 3"/>
                        </svg>
                    </div>
                    <div class="kpi-details">
                        <span class="kpi-label">Active Programs</span>
                        <strong class="kpi-value" id="kpiActivePrograms">—</strong>
                        <span class="kpi-pill-badge">Running Now</span>
                    </div>
                </div>

                <!-- 3. Draft Programs (Pink Theme) -->
                <div class="employee-kpi kpi-theme-pink">
                    <div class="kpi-wave"></div>
                    <div class="kpi-icon-box">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/>
                            <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/>
                        </svg>
                    </div>
                    <div class="kpi-details">
                        <span class="kpi-label">Draft Programs</span>
                        <strong class="kpi-value" id="kpiDraftPrograms">—</strong>
                        <span class="kpi-pill-badge">Upcoming</span>
                    </div>
                </div>

                <!-- 4. Total Enrollments (Rose Theme) -->
                <div class="employee-kpi kpi-theme-rose">
                    <div class="kpi-wave"></div>
                    <div class="kpi-icon-box">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                            <circle cx="9" cy="7" r="4"/>
                            <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
                            <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                        </svg>
                    </div>
                    <div class="kpi-details">
                        <span class="kpi-label">Enrollments</span>
                        <strong class="kpi-value" id="kpiTotalEnrollments">—</strong>
                        <span class="kpi-pill-badge"><span id="kpiInProgress">—</span> In Progress</span>
                    </div>
                </div>

                <!-- 5. Completed (Fuchsia Theme) -->
                <div class="employee-kpi kpi-theme-fuchsia">
                    <div class="kpi-wave"></div>
                    <div class="kpi-icon-box">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                            <polyline points="22 4 12 14.01 9 11.01"/>
                        </svg>
                    </div>
                    <div class="kpi-details">
                        <span class="kpi-label">Completed</span>
                        <strong class="kpi-value" id="kpiCompletedEnrollments">—</strong>
                        <span class="kpi-pill-badge">Finished</span>
                    </div>
                </div>

                <!-- 6. Certificates Issued (Magenta Theme) -->
                <div class="employee-kpi kpi-theme-magenta">
                    <div class="kpi-wave"></div>
                    <div class="kpi-icon-box">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="8" r="7"/>
                            <polyline points="8.21 13.89 7 23 12 20 17 23 15.79 13.88"/>
                        </svg>
                    </div>
                    <div class="kpi-details">
                        <span class="kpi-label">Certificates</span>
                        <strong class="kpi-value" id="kpiCertificates">—</strong>
                        <span class="kpi-pill-badge"><span id="kpiAvgScore">—</span> Avg Score</span>
                    </div>
                </div>

            </section>

            <!-- Categories Strip -->
            <div class="tr-categories" id="categoryStrip" style="display:none">
                <span class="tr-cat-title">Categories</span>
                <div id="categoryChips"></div>
            </div>

<!-- Tabs -->
            <div class="tr-tabs">
                <button type="button" class="tr-tab active" data-tab="programs">Programs</button>
                <button type="button" class="tr-tab" data-tab="enrollments">Enrollments</button>
            </div>

            <!-- Programs Panel -->
            <div class="tr-tab-panel" id="programsPanel">
                <div class="tr-toolbar">
                    <input type="text" id="programSearch" class="tr-input tr-search" placeholder="Search by title or trainer...">
                    <select id="programStatus" class="tr-input">
                        <option value="">All Statuses</option>
                        <option value="DRAFT">Draft</option>
                        <option value="ACTIVE">Active</option>
                        <option value="COMPLETED">Completed</option>
                        <option value="CANCELLED">Cancelled</option>
                    </select>
                    <select id="programCategory" class="tr-input">
                        <option value="">All Categories</option>
                    </select>
                    <select id="programDeliveryMode" class="tr-input">
                        <option value="">All Modes</option>
                        <option value="ONLINE">Online</option>
                        <option value="IN_PERSON">In Person</option>
                        <option value="HYBRID">Hybrid</option>
                    </select>
                    <button type="button" id="programReset" class="tr-ghost-btn">Reset</button>
                </div>
                <div class="tr-table-card">
                    <table class="tr-table">
                        <thead>
                            <tr>
                                <th>Program</th>
                                <th>Category</th>
                                <th>Dates</th>
                                <th>Mode</th>
                                <th>Trainer</th>
                                <th>Enrolled</th>
                                <th>Status</th>
                                <th class="tr-col-actions">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="programsTableBody">
                            <tr><td colspan="8" class="tr-loading">Loading programs...</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Enrollments Panel -->
            <div class="tr-tab-panel" id="enrollmentsPanel" style="display:none">
                <div class="tr-toolbar">
                    <input type="text" id="enrollmentSearch" class="tr-input tr-search" placeholder="Search by employee...">
                    <select id="enrollmentStatus" class="tr-input">
                        <option value="">All Statuses</option>
                        <option value="ENROLLED">Enrolled</option>
                        <option value="IN_PROGRESS">In Progress</option>
                        <option value="COMPLETED">Completed</option>
                        <option value="DROPPED">Dropped</option>
                    </select>
                    <select id="enrollmentProgram" class="tr-input">
                        <option value="">All Programs</option>
                    </select>
                    <button type="button" id="enrollmentReset" class="tr-ghost-btn">Reset</button>
                </div>
                <div class="tr-table-card">
                    <table class="tr-table">
                        <thead>
                            <tr>
                                <th>Employee</th>
                                <th>Program</th>
                                <th>Enrolled</th>
                                <th>Status</th>
                                <th>Score</th>
                                <th>Certificate</th>
                                <th class="tr-col-actions">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="enrollmentsTableBody">
                            <tr><td colspan="7" class="tr-loading">Loading enrollments...</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Pagination -->
            <div class="tr-pagination">
                <span id="paginationInfo" class="tr-pagination-info">—</span>
                <div class="tr-pagination-controls">
                    <button type="button" id="prevPage" class="tr-ghost-btn">‹ Prev</button>
                    <button type="button" id="nextPage" class="tr-ghost-btn">Next ›</button>
                </div>
            </div>

        </section>

<!-- Program Form Modal -->
<div class="tr-modal" id="programModal" aria-hidden="true">
    <div class="tr-modal-card tr-modal-lg">
        <div class="tr-modal-header">
            <div>
                <h2 id="programModalTitle">New Training Program</h2>
                <p>Set up a training program with schedule and delivery details</p>
            </div>
            <button type="button" class="tr-close-btn" data-close="programModal" aria-label="Close">&times;</button>
        </div>
        <div class="tr-modal-body">
            <input type="hidden" id="programId" value="">
            <div class="tr-form-grid">
                <label class="tr-field">Category
                    <input type="text" id="programCategoryInput" class="tr-input" placeholder="e.g. Leadership, Compliance">
                </label>
                <label class="tr-field">Trainer
                    <input type="text" id="programTrainer" class="tr-input" placeholder="Trainer name">
                </label>
                <label class="tr-field">Start Date
                    <input type="date" id="programStartDate" class="tr-input">
                </label>
                <label class="tr-field">End Date
                    <input type="date" id="programEndDate" class="tr-input">
                </label>
                <label class="tr-field">Duration (hours)
                    <input type="number" id="programDuration" class="tr-input" min="0" step="0.5" placeholder="e.g. 8">
                </label>
                <label class="tr-field">Max Participants
                    <input type="number" id="programMaxParticipants" class="tr-input" min="1" placeholder="e.g. 30">
                </label>
                <label class="tr-field">Location
                    <input type="text" id="programLocation" class="tr-input" placeholder="Room / venue / link">
                </label>
                <label class="tr-field">Delivery Mode
                    <select id="programDeliveryInput" class="tr-input">
                        <option value="IN_PERSON">In Person</option>
                        <option value="ONLINE">Online</option>
                        <option value="HYBRID">Hybrid</option>
                    </select>
                </label>
                <label class="tr-field">Status
                    <select id="programStatusInput" class="tr-input">
                        <option value="DRAFT">Draft</option>
                        <option value="ACTIVE">Active</option>
                        <option value="COMPLETED">Completed</option>
                        <option value="CANCELLED">Cancelled</option>
                    </select>
                </label>
            </div>
            <label class="tr-field">Program Title <span class="tr-req">*</span>
                <input type="text" id="programTitle" class="tr-input" placeholder="e.g. Leadership Development Program">
            </label>
            <label class="tr-field">Description
                <textarea id="programDescription" rows="3" placeholder="Program objectives, curriculum, and notes..."></textarea>
            </label>
        </div>
        <p class="tr-error" id="programFormError"></p>
        <div class="tr-modal-actions">
            <button type="button" class="tr-secondary-btn" data-close="programModal">Cancel</button>
            <button type="button" class="tr-primary-btn" id="saveProgramBtn">Save Program</button>
        </div>
    </div>
</div>

<!-- Program Detail / View Modal -->
<div class="tr-modal" id="viewProgramModal" aria-hidden="true">
    <div class="tr-modal-card tr-modal-lg">
        <div class="tr-modal-header">
            <div>
                <h2 id="viewProgramTitle">Training Program</h2>
                <p id="viewProgramSub" class="tr-modal-sub">—</p>
            </div>
            <button type="button" class="tr-close-btn" data-close="viewProgramModal" aria-label="Close">&times;</button>
        </div>
        <div class="tr-modal-body" id="viewProgramBody"></div>
        <div class="tr-modal-actions">
            <button type="button" class="tr-secondary-btn" data-close="viewProgramModal">Close</button>
            <button type="button" class="tr-primary-btn" id="editProgramBtn">Edit Program</button>
        </div>
    </div>
</div>
<!-- Session Form Modal -->
<div class="tr-modal" id="sessionModal" aria-hidden="true">
    <div class="tr-modal-card">
        <div class="tr-modal-header">
            <div>
                <h2 id="sessionModalTitle">New Session</h2>
                <p>Schedule a session for the selected program</p>
            </div>
            <button type="button" class="tr-close-btn" data-close="sessionModal" aria-label="Close">&times;</button>
        </div>
        <div class="tr-modal-body">
            <input type="hidden" id="sessionId" value="">
            <input type="hidden" id="sessionProgramId" value="">
            <div class="tr-form-grid">
                <label class="tr-field">Session Date
                    <input type="date" id="sessionDate" class="tr-input">
                </label>
                <label class="tr-field">Start Time
                    <input type="time" id="sessionStartTime" class="tr-input">
                </label>
                <label class="tr-field">End Time
                    <input type="time" id="sessionEndTime" class="tr-input">
                </label>
                <label class="tr-field">Duration (hours)
                    <input type="number" id="sessionDuration" class="tr-input" min="0" step="0.5" placeholder="e.g. 2">
                </label>
                <label class="tr-field">Location
                    <input type="text" id="sessionLocation" class="tr-input" placeholder="Room / venue / link">
                </label>
                <label class="tr-field">Trainer
                    <input type="text" id="sessionTrainer" class="tr-input" placeholder="Trainer name">
                </label>
                <label class="tr-field">Status
                    <select id="sessionStatus" class="tr-input">
                        <option value="SCHEDULED">Scheduled</option>
                        <option value="COMPLETED">Completed</option>
                        <option value="CANCELLED">Cancelled</option>
                    </select>
                </label>
            </div>
            <label class="tr-field">Session Title <span class="tr-req">*</span>
                <input type="text" id="sessionTitle" class="tr-input" placeholder="e.g. Module 1 — Foundations">
            </label>
            <label class="tr-field">Description
                <textarea id="sessionDescription" rows="3" placeholder="Session contents and notes..."></textarea>
            </label>
        </div>
        <p class="tr-error" id="sessionFormError"></p>
        <div class="tr-modal-actions">
            <button type="button" class="tr-secondary-btn" data-close="sessionModal">Cancel</button>
            <button type="button" class="tr-primary-btn" id="saveSessionBtn">Save Session</button>
        </div>
    </div>
</div>

<!-- Enrollment Form Modal -->
<div class="tr-modal" id="enrollmentModal" aria-hidden="true">
    <div class="tr-modal-card">
        <div class="tr-modal-header">
            <div>
                <h2 id="enrollmentModalTitle">New Enrollment</h2>
                <p>Enroll an employee in a training program</p>
            </div>
            <button type="button" class="tr-close-btn" data-close="enrollmentModal" aria-label="Close">&times;</button>
        </div>
        <div class="tr-modal-body">
            <input type="hidden" id="enrollmentId" value="">
            <div class="tr-form-grid">
                <label class="tr-field">Employee <span class="tr-req">*</span>
                    <select id="enrollmentEmployee" class="tr-input"></select>
                </label>
                <label class="tr-field">Program <span class="tr-req">*</span>
                    <select id="enrollmentProgramInput" class="tr-input"></select>
                </label>
                <label class="tr-field">Enrollment Date
                    <input type="date" id="enrollmentDate" class="tr-input">
                </label>
                <label class="tr-field">Status
                    <select id="enrollmentStatusInput" class="tr-input">
                        <option value="ENROLLED">Enrolled</option>
                        <option value="IN_PROGRESS">In Progress</option>
                        <option value="COMPLETED">Completed</option>
                        <option value="DROPPED">Dropped</option>
                    </select>
                </label>
                <label class="tr-field">Completion Date
                    <input type="date" id="enrollmentCompletionDate" class="tr-input">
                </label>
                <label class="tr-field">Score (0–100)
                    <input type="number" id="enrollmentScore" class="tr-input" min="0" max="100" step="0.5" placeholder="e.g. 87">
                </label>
            </div>
            <label class="tr-field">Feedback
                <textarea id="enrollmentFeedback" rows="3" placeholder="Feedback for this enrollment..."></textarea>
            </label>
            <label class="tr-checkbox">
                <input type="checkbox" id="enrollmentCertIssued">
                <span>Certificate already issued</span>
            </label>
            <div id="certificateBlock" style="display:none">
                <label class="tr-field">Certificate Number <span class="tr-req">*</span>
                    <input type="text" id="certificateNumber" class="tr-input" placeholder="e.g. CERT-2026-0001">
                </label>
            </div>
        </div>
        <p class="tr-error" id="enrollmentFormError"></p>
        <div class="tr-modal-actions">
            <button type="button" class="tr-secondary-btn" data-close="enrollmentModal">Cancel</button>
            <button type="button" class="tr-primary-btn" id="saveEnrollmentBtn">Save Enrollment</button>
        </div>
    </div>
</div>

<!-- Confirm Delete Modal -->
<div class="tr-modal" id="confirmModal" aria-hidden="true">
    <div class="tr-modal-card tr-modal-sm">
        <div class="tr-modal-header">
            <div>
                <h2>Confirm Delete</h2>
                <p>This action cannot be undone</p>
            </div>
            <button type="button" class="tr-close-btn" data-close="confirmModal" aria-label="Close">&times;</button>
        </div>
        <div class="tr-modal-body">
            <p id="confirmMessage" class="tr-confirm-message">Are you sure you want to delete this item?</p>
        </div>
        <div class="tr-modal-actions">
            <button type="button" class="tr-secondary-btn" data-close="confirmModal">Cancel</button>
            <button type="button" class="tr-danger-btn" id="confirmDeleteBtn">Delete</button>
        </div>
    </div>
</div>
    </main>
</div>

<div class="tr-toast" id="toast" role="status"></div>

<script src="../assets/js/theme.js?v=<?= time() ?>"></script>
<script src="../assets/js/training.js?v=<?= time() ?>"></script>
</body>
</html>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <meta
        name="description"
        content="Performance Management — Conduct reviews, track goals, and measure employee performance."
    >

    <title>Performance Management - HR Portal</title>

    <link rel="stylesheet" href="../assets/css/app-theme.css?v=<?= time() ?>">
    <link rel="stylesheet" href="../assets/css/hr-shell.css?v=<?= time() ?>">
    <link rel="stylesheet" href="../assets/css/hr-employees.css?v=<?= time() ?>">
    <link rel="stylesheet" href="../assets/css/performance.css?v=<?= time() ?>">
    <link rel="stylesheet" href="../assets/css/hr-performance.css?v=<?= time() ?>">
</head>

<body>

<div class="app">

    <?php include '../includes/hr-sidebar.php'; ?>

    <main class="main-content">

        <?php include '../includes/hr-topbar.php'; ?>


        <!-- =====================================================
             PERFORMANCE PAGE
        ====================================================== -->

        <section class="pf-page">


            <!-- COMPACT TOP BAR & ACTIONS -->
            <div class="pf-top-bar">
                <div class="pf-top-badge">
                    <span class="pf-pulse-dot"></span>
                    <span>Performance &amp; Appraisals</span>
                </div>

                <div class="pf-header-actions">
                    <button
                        type="button"
                        id="newGoalBtn"
                        class="pf-btn pf-btn-light"
                    >
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                        <span>New Goal</span>
                    </button>

                    <button
                        type="button"
                        id="newReviewBtn"
                        class="pf-btn pf-btn-primary"
                    >
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                        <span>New Review</span>
                    </button>
                </div>
            </div>


            <!-- ERROR -->
            <div
                class="pf-error"
                id="pfError"
                style="display:none"
            ></div>


            <!-- =================================================
                 1. SIX SIGNATURE HR KPI CARDS (Rich Violet & Pink Gradients)
            ================================================== -->
            <section class="employee-kpis pf-kpis" id="performanceKpis">

                <!-- 1. Overall Rating (Plum Theme) -->
                <div class="employee-kpi kpi-theme-plum">
                    <div class="kpi-wave"></div>
                    <div class="kpi-icon-box">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>
                        </svg>
                    </div>
                    <div class="kpi-details">
                        <span class="kpi-label">Avg Rating</span>
                        <div class="pf-rating-val-wrap">
                            <strong class="kpi-value" id="kpiAvgRating">—</strong>
                            <span class="pf-rating-scale">/ 5.0</span>
                        </div>
                        <span class="kpi-pill-badge">★ Overall Score</span>
                    </div>
                </div>

                <!-- 2. Total Reviews (Violet Theme) -->
                <div class="employee-kpi kpi-theme-violet">
                    <div class="kpi-wave"></div>
                    <div class="kpi-icon-box">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"/>
                            <rect x="8" y="2" width="8" height="4" rx="1" ry="1"/>
                            <line x1="9" y1="12" x2="15" y2="12"/>
                            <line x1="9" y1="16" x2="13" y2="16"/>
                        </svg>
                    </div>
                    <div class="kpi-details">
                        <span class="kpi-label">Total Reviews</span>
                        <strong class="kpi-value" id="kpiTotalReviews">—</strong>
                        <span class="kpi-pill-badge">All Cycles</span>
                    </div>
                </div>

                <!-- 3. Completed Reviews (Pink Theme) -->
                <div class="employee-kpi kpi-theme-pink">
                    <div class="kpi-wave"></div>
                    <div class="kpi-icon-box">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                            <polyline points="22 4 12 14.01 9 11.01"/>
                        </svg>
                    </div>
                    <div class="kpi-details">
                        <span class="kpi-label">Completed</span>
                        <strong class="kpi-value" id="kpiCompletedReviews">—</strong>
                        <span class="kpi-pill-badge">Finalized</span>
                    </div>
                </div>

                <!-- 4. Pending Reviews (Rose Theme) -->
                <div class="employee-kpi kpi-theme-rose">
                    <div class="kpi-wave"></div>
                    <div class="kpi-icon-box">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="10"/>
                            <polyline points="12 6 12 12 16 14"/>
                        </svg>
                    </div>
                    <div class="kpi-details">
                        <span class="kpi-label">Pending</span>
                        <strong class="kpi-value" id="kpiPendingReviews">—</strong>
                        <span class="kpi-pill-badge">Awaiting Action</span>
                    </div>
                </div>

                <!-- 5. Active Goals (Fuchsia Theme) -->
                <div class="employee-kpi kpi-theme-fuchsia">
                    <div class="kpi-wave"></div>
                    <div class="kpi-icon-box">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="10"/>
                            <circle cx="12" cy="12" r="6"/>
                            <circle cx="12" cy="12" r="2"/>
                        </svg>
                    </div>
                    <div class="kpi-details">
                        <span class="kpi-label">Active Goals</span>
                        <strong class="kpi-value" id="kpiActiveGoals">—</strong>
                        <span class="kpi-pill-badge"><span id="kpiTotalGoals">—</span> Tracked</span>
                    </div>
                </div>

                <!-- 6. Goal Progress (Magenta Theme) -->
                <div class="employee-kpi kpi-theme-magenta">
                    <div class="kpi-wave"></div>
                    <div class="kpi-icon-box">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="23 6 13.5 15.5 8.5 10.5 1 18"/>
                            <polyline points="17 6 23 6 23 12"/>
                        </svg>
                    </div>
                    <div class="kpi-details">
                        <span class="kpi-label">Goal Progress</span>
                        <strong class="kpi-value" id="kpiAvgProgress">—</strong>
                        <span class="kpi-pill-badge">Avg Attainment</span>
                    </div>
                </div>

            </section>


            <!-- =================================================
                 ANALYTICS
            ================================================== -->

            <section class="pf-analytics">


                <!-- RATING DISTRIBUTION -->
                <article class="pf-card pf-rating-card">

                    <div class="pf-card-header">

                        <div>
                            <h2>Rating Distribution</h2>

                            <p>
                                Employee ratings across all reviews
                            </p>
                        </div>

                    </div>

                    <div
                        class="pf-rating-dist"
                        id="ratingDist"
                    >

                        <div class="pf-empty-inline">
                            No rating data yet.
                        </div>

                    </div>

                </article>


                <!-- GOAL STATUS -->
                <article class="pf-card pf-goal-card">

                    <div class="pf-card-header">

                        <div>
                            <h2>Goal Status</h2>

                            <p>
                                Current progress of employee goals
                            </p>
                        </div>

                    </div>

                    <div
                        class="pf-goal-status"
                        id="goalStatusChart"
                    >

                        <div class="pf-empty-inline">
                            No goal data yet.
                        </div>

                    </div>

                </article>

            </section>


            <!-- =================================================
                 EMPLOYEE PERFORMANCE
            ================================================== -->

            <section class="pf-card pf-employees-section">


                <div class="pf-section-heading">

                    <div>

                        <span class="pf-section-label">
                            PERFORMANCE
                        </span>

                        <h2>
                            Employee Performance
                        </h2>

                        <p>
                            Review employee ratings, goals and current status
                        </p>

                    </div>

                </div>


                <!-- FILTERS -->

                <div class="pf-toolbar">

                    <div class="pf-search-box">

                        <span>⌕</span>

                        <input
                            type="text"
                            id="reviewSearch"
                            class="pf-input"
                            placeholder="Search employee..."
                        >

                    </div>


                    <select
                        id="reviewStatus"
                        class="pf-input"
                    >

                        <option value="">
                            All Statuses
                        </option>

                        <option value="DRAFT">
                            Draft
                        </option>

                        <option value="SUBMITTED">
                            Submitted
                        </option>

                        <option value="APPROVED">
                            Approved
                        </option>

                        <option value="COMPLETED">
                            Completed
                        </option>

                    </select>


                    <select
                        id="reviewDepartment"
                        class="pf-input"
                    >

                        <option value="">
                            All Departments
                        </option>

                    </select>


                    <button
                        type="button"
                        id="reviewReset"
                        class="pf-reset-btn"
                    >
                        Reset
                    </button>

                </div>


                <!-- REVIEWS TABLE -->

                <div class="pf-table-wrapper">

                    <table class="pf-table">

                        <thead>

                            <tr>

                                <th>
                                    Employee
                                </th>

                                <th>
                                    Review Period
                                </th>

                                <th>
                                    Review Date
                                </th>

                                <th>
                                    Rating
                                </th>

                                <th>
                                    Status
                                </th>

                                <th class="pf-actions-column">
                                    Actions
                                </th>

                            </tr>

                        </thead>


                        <tbody id="reviewsTableBody">

                            <tr>

                                <td
                                    colspan="6"
                                    class="pf-loading"
                                >
                                    Loading reviews...
                                </td>

                            </tr>

                        </tbody>

                    </table>

                </div>


                <!-- PAGINATION -->

                <div class="pf-pagination">

                    <span id="paginationInfo">
                        —
                    </span>

                    <div>

                        <button
                            type="button"
                            id="prevPage"
                            class="pf-page-btn"
                        >
                            ← Previous
                        </button>

                        <button
                            type="button"
                            id="nextPage"
                            class="pf-page-btn"
                        >
                            Next →
                        </button>

                    </div>

                </div>

            </section>


            <!-- =================================================
                 GOALS SECTION
            ================================================== -->

            <section
                class="pf-card pf-goals-section"
                id="goalsPanel"
            >

                <div class="pf-section-heading">

                    <div>

                        <span class="pf-section-label">
                            GOALS
                        </span>

                        <h2>
                            Employee Goals
                        </h2>

                        <p>
                            Track objectives and progress
                        </p>

                    </div>

                </div>


                <div class="pf-toolbar">

                    <div class="pf-search-box">

                        <span>⌕</span>

                        <input
                            type="text"
                            id="goalSearch"
                            class="pf-input"
                            placeholder="Search goal or employee..."
                        >

                    </div>


                    <select
                        id="goalStatus"
                        class="pf-input"
                    >

                        <option value="">
                            All Statuses
                        </option>

                        <option value="ACTIVE">
                            Active
                        </option>

                        <option value="COMPLETED">
                            Completed
                        </option>

                        <option value="ARCHIVED">
                            Archived
                        </option>

                    </select>


                    <select
                        id="goalPriority"
                        class="pf-input"
                    >

                        <option value="">
                            All Priorities
                        </option>

                        <option value="HIGH">
                            High
                        </option>

                        <option value="MEDIUM">
                            Medium
                        </option>

                        <option value="LOW">
                            Low
                        </option>

                    </select>


                    <button
                        type="button"
                        id="goalReset"
                        class="pf-reset-btn"
                    >
                        Reset
                    </button>

                </div>


                <div class="pf-table-wrapper">

                    <table class="pf-table">

                        <thead>

                            <tr>

                                <th>Goal</th>

                                <th>Employee</th>

                                <th>Priority</th>

                                <th>Progress</th>

                                <th>Target Date</th>

                                <th>Status</th>

                                <th class="pf-actions-column">
                                    Actions
                                </th>

                            </tr>

                        </thead>


                        <tbody id="goalsTableBody">

                            <tr>

                                <td
                                    colspan="7"
                                    class="pf-loading"
                                >
                                    Loading goals...
                                </td>

                            </tr>

                        </tbody>

                    </table>

                </div>

            </section>


        </section>


        <!-- =====================================================
             NEW REVIEW MODAL
        ====================================================== -->

        <div
            class="pf-modal"
            id="reviewModal"
            aria-hidden="true"
        >

            <div class="pf-modal-card pf-modal-large">

                <div class="pf-modal-header">

                    <div>

                        <span class="pf-modal-label">
                            PERFORMANCE
                        </span>

                        <h2 id="reviewModalTitle">
                            New Performance Review
                        </h2>

                        <p>
                            Create a performance review for an employee
                        </p>

                    </div>

                    <button
                        type="button"
                        class="pf-close-btn"
                        data-close="reviewModal"
                    >
                        ×
                    </button>

                </div>


                <div class="pf-modal-body">

                    <input
                        type="hidden"
                        id="reviewId"
                        value=""
                    >


                    <div class="pf-form-grid">

                        <label class="pf-field">
                            Employee
                            <span>*</span>

                            <select
                                id="reviewEmployee"
                                class="pf-input"
                            ></select>

                        </label>


                        <label class="pf-field">
                            Reviewer

                            <select
                                id="reviewReviewer"
                                class="pf-input"
                            ></select>

                        </label>


                        <label class="pf-field">
                            Period Start
                            <span>*</span>

                            <input
                                type="date"
                                id="reviewPeriodStart"
                                class="pf-input"
                            >

                        </label>


                        <label class="pf-field">
                            Period End
                            <span>*</span>

                            <input
                                type="date"
                                id="reviewPeriodEnd"
                                class="pf-input"
                            >

                        </label>


                        <label class="pf-field">
                            Review Date

                            <input
                                type="date"
                                id="reviewDate"
                                class="pf-input"
                            >

                        </label>


                        <label class="pf-field">
                            Overall Rating

                            <input
                                type="number"
                                id="reviewOverallRating"
                                class="pf-input"
                                min="0"
                                max="5"
                                step="0.5"
                                placeholder="0 - 5"
                            >

                        </label>


                        <label class="pf-field">
                            Status

                            <select
                                id="reviewStatusInput"
                                class="pf-input"
                            >

                                <option value="DRAFT">
                                    Draft
                                </option>

                                <option value="SUBMITTED">
                                    Submitted
                                </option>

                                <option value="APPROVED">
                                    Approved
                                </option>

                                <option value="COMPLETED">
                                    Completed
                                </option>

                            </select>

                        </label>

                    </div>


                    <label class="pf-field">
                        Overall Comments

                        <textarea
                            id="reviewOverallComments"
                            rows="4"
                            placeholder="Summary of employee performance..."
                        ></textarea>

                    </label>


                    <label class="pf-field">
                        Employee Comments

                        <textarea
                            id="reviewEmployeeComments"
                            rows="4"
                            placeholder="Employee self-assessment..."
                        ></textarea>

                    </label>


                    <div class="pf-ratings-block">

                        <h3>
                            Criteria Ratings
                        </h3>

                        <p>
                            Rate individual performance criteria
                        </p>

                        <div id="ratingsContainer"></div>

                        <button
                            type="button"
                            id="addRatingBtn"
                            class="pf-reset-btn"
                        >
                            + Add Criteria
                        </button>

                    </div>

                </div>


                <p
                    class="pf-error"
                    id="reviewFormError"
                ></p>


                <div class="pf-modal-actions">

                    <button
                        type="button"
                        class="pf-btn pf-btn-light"
                        data-close="reviewModal"
                    >
                        Cancel
                    </button>

                    <button
                        type="button"
                        class="pf-btn pf-btn-primary"
                        id="saveReviewBtn"
                    >
                        Save Review
                    </button>

                </div>

            </div>

        </div>


        <!-- =====================================================
             VIEW REVIEW MODAL
        ====================================================== -->

        <div
            class="pf-modal"
            id="viewReviewModal"
            aria-hidden="true"
        >

            <div class="pf-modal-card pf-modal-large">

                <div class="pf-modal-header">

                    <div>

                        <span class="pf-modal-label">
                            REVIEW DETAILS
                        </span>

                        <h2 id="viewReviewTitle">
                            Performance Review
                        </h2>

                        <p id="viewReviewSub">
                            —
                        </p>

                    </div>

                    <button
                        type="button"
                        class="pf-close-btn"
                        data-close="viewReviewModal"
                    >
                        ×
                    </button>

                </div>


                <div
                    class="pf-modal-body"
                    id="viewReviewBody"
                ></div>


                <div class="pf-modal-actions">

                    <button
                        type="button"
                        class="pf-btn pf-btn-light"
                        data-close="viewReviewModal"
                    >
                        Close
                    </button>

                    <button
                        type="button"
                        class="pf-btn pf-btn-primary"
                        id="editReviewBtn"
                    >
                        Edit Review
                    </button>

                </div>

            </div>

        </div>


        <!-- =====================================================
             GOAL MODAL
        ====================================================== -->

        <div
            class="pf-modal"
            id="goalModal"
            aria-hidden="true"
        >

            <div class="pf-modal-card">

                <div class="pf-modal-header">

                    <div>

                        <span class="pf-modal-label">
                            GOAL MANAGEMENT
                        </span>

                        <h2 id="goalModalTitle">
                            New Goal
                        </h2>

                        <p>
                            Define an objective for an employee
                        </p>

                    </div>

                    <button
                        type="button"
                        class="pf-close-btn"
                        data-close="goalModal"
                    >
                        ×
                    </button>

                </div>


                <div class="pf-modal-body">

                    <input
                        type="hidden"
                        id="goalId"
                        value=""
                    >


                    <div class="pf-form-grid">

                        <label class="pf-field">
                            Employee
                            <span>*</span>

                            <select
                                id="goalEmployee"
                                class="pf-input"
                            ></select>

                        </label>


                        <label class="pf-field">
                            Priority

                            <select
                                id="goalPriorityInput"
                                class="pf-input"
                            >

                                <option value="LOW">
                                    Low
                                </option>

                                <option
                                    value="MEDIUM"
                                    selected
                                >
                                    Medium
                                </option>

                                <option value="HIGH">
                                    High
                                </option>

                            </select>

                        </label>


                        <label class="pf-field">
                            Status

                            <select
                                id="goalStatusInput"
                                class="pf-input"
                            >

                                <option value="ACTIVE">
                                    Active
                                </option>

                                <option value="COMPLETED">
                                    Completed
                                </option>

                                <option value="ARCHIVED">
                                    Archived
                                </option>

                            </select>

                        </label>


                        <label class="pf-field">
                            Target Date

                            <input
                                type="date"
                                id="goalTargetDate"
                                class="pf-input"
                            >

                        </label>

                    </div>


                    <label class="pf-field">
                        Goal Title
                        <span>*</span>

                        <input
                            type="text"
                            id="goalTitle"
                            class="pf-input"
                            placeholder="Enter goal title"
                        >

                    </label>


                    <label class="pf-field">
                        Description

                        <textarea
                            id="goalDescription"
                            rows="4"
                            placeholder="Describe the objective..."
                        ></textarea>

                    </label>


                    <div class="pf-progress-row">

                        <label class="pf-field">

                            Progress

                            <input
                                type="number"
                                id="goalProgress"
                                class="pf-input"
                                min="0"
                                max="100"
                                value="0"
                            >

                        </label>


                        <div class="pf-progress-preview">

                            <div>

                                <span>
                                    Current Progress
                                </span>

                                <strong id="goalProgressPreview">
                                    0%
                                </strong>

                            </div>

                            <div class="pf-progress-track">

                                <div
                                    class="pf-progress-fill"
                                    id="goalProgressFill"
                                    style="width:0%"
                                ></div>

                            </div>

                        </div>

                    </div>

                </div>


                <p
                    class="pf-error"
                    id="goalFormError"
                ></p>


                <div class="pf-modal-actions">

                    <button
                        type="button"
                        class="pf-btn pf-btn-light"
                        data-close="goalModal"
                    >
                        Cancel
                    </button>

                    <button
                        type="button"
                        class="pf-btn pf-btn-primary"
                        id="saveGoalBtn"
                    >
                        Save Goal
                    </button>

                </div>

            </div>

        </div>


        <!-- =====================================================
             DELETE MODAL
        ====================================================== -->

        <div
            class="pf-modal"
            id="confirmModal"
            aria-hidden="true"
        >

            <div class="pf-modal-card pf-modal-small">

                <div class="pf-modal-header">

                    <div>

                        <span class="pf-modal-label">
                            CONFIRMATION
                        </span>

                        <h2>
                            Delete Item?
                        </h2>

                        <p>
                            This action cannot be undone.
                        </p>

                    </div>

                    <button
                        type="button"
                        class="pf-close-btn"
                        data-close="confirmModal"
                    >
                        ×
                    </button>

                </div>


                <div class="pf-modal-body">

                    <p
                        id="confirmMessage"
                        class="pf-confirm-message"
                    >
                        Are you sure you want to delete this item?
                    </p>

                </div>


                <div class="pf-modal-actions">

                    <button
                        type="button"
                        class="pf-btn pf-btn-light"
                        data-close="confirmModal"
                    >
                        Cancel
                    </button>

                    <button
                        type="button"
                        class="pf-btn pf-btn-danger"
                        id="confirmDeleteBtn"
                    >
                        Delete
                    </button>

                </div>

            </div>

        </div>


    </main>

</div>


<!-- TOAST -->

<div
    class="pf-toast"
    id="toast"
    role="status"
></div>


<script src="../assets/js/theme.js?v=1"></script>
<script src="../assets/js/performance.js?v=3"></script>

</body>
</html>
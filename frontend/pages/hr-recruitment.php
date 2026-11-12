<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Recruitment & Onboarding — Unified HR workspace for talent acquisition, candidate pipeline, new joiner onboarding, and exit clearances.">
    <title>Recruitment &amp; Onboarding - HR Portal</title>
    <link rel="stylesheet" href="../assets/css/app-theme.css?v=<?= time() ?>">
    <link rel="stylesheet" href="../assets/css/hr-shell.css?v=<?= time() ?>">
    <link rel="stylesheet" href="../assets/css/hr-employees.css?v=<?= time() ?>">
    <link rel="stylesheet" href="../assets/css/hr-recruitment.css?v=<?= time() ?>">
</head>
<body>
<div class="app">
    <?php include '../includes/hr-sidebar.php'; ?>

    <main class="main-content">
        <?php include '../includes/hr-topbar.php'; ?>

        <section class="content hr-employees-page hr-recruitment-page">

            <!-- 1. SIX KPI PILL CARDS (Exact 6-column match with HR Employees design) -->
            <div class="employee-kpis" id="recKpis">
                <!-- 1. Job Openings -->
                <div class="employee-kpi kpi-theme-plum">
                    <div class="kpi-wave"></div>
                    <div class="kpi-icon-box">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="2" y="7" width="20" height="14" rx="2" ry="2"/>
                            <path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/>
                        </svg>
                    </div>
                    <div class="kpi-details">
                        <span class="kpi-label">Job Openings</span>
                        <strong class="kpi-value" id="openJobs">0</strong>
                        <span class="kpi-pill-badge">Active Listings</span>
                    </div>
                </div>

                <!-- 2. Candidates -->
                <div class="employee-kpi kpi-theme-magenta">
                    <div class="kpi-wave"></div>
                    <div class="kpi-icon-box">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                            <circle cx="9" cy="7" r="4"/>
                            <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
                            <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                        </svg>
                    </div>
                    <div class="kpi-details">
                        <span class="kpi-label">Candidates</span>
                        <strong class="kpi-value" id="totalApplicants">0</strong>
                        <span class="kpi-pill-badge">Talent Pool</span>
                    </div>
                </div>

                <!-- 3. In Review -->
                <div class="employee-kpi kpi-theme-violet">
                    <div class="kpi-wave"></div>
                    <div class="kpi-icon-box">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                            <polyline points="14 2 14 8 20 8"/>
                            <line x1="16" y1="13" x2="8" y2="13"/>
                            <line x1="16" y1="17" x2="8" y2="17"/>
                            <polyline points="10 9 9 9 8 9"/>
                        </svg>
                    </div>
                    <div class="kpi-details">
                        <span class="kpi-label">In Review</span>
                        <strong class="kpi-value" id="inReviewCount">0</strong>
                        <span class="kpi-pill-badge">Screening</span>
                    </div>
                </div>

                <!-- 4. Interviews -->
                <div class="employee-kpi kpi-theme-fuchsia">
                    <div class="kpi-wave"></div>
                    <div class="kpi-icon-box">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="10"/>
                            <polyline points="12 6 12 12 16 14"/>
                        </svg>
                    </div>
                    <div class="kpi-details">
                        <span class="kpi-label">Interviews</span>
                        <strong class="kpi-value" id="interviewCount">0</strong>
                        <span class="kpi-pill-badge">Scheduled</span>
                    </div>
                </div>

                <!-- 5. Onboarding -->
                <div class="employee-kpi kpi-theme-pink">
                    <div class="kpi-wave"></div>
                    <div class="kpi-icon-box">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/>
                            <circle cx="9" cy="7" r="4"/>
                            <polyline points="16 11 18 13 22 9"/>
                        </svg>
                    </div>
                    <div class="kpi-details">
                        <span class="kpi-label">Onboarding</span>
                        <strong class="kpi-value" id="onboardingActive">0</strong>
                        <span class="kpi-pill-badge">In Progress</span>
                    </div>
                </div>

                <!-- 6. Active Exits -->
                <div class="employee-kpi kpi-theme-rose">
                    <div class="kpi-wave"></div>
                    <div class="kpi-icon-box">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/>
                            <polyline points="10 17 15 12 10 7"/>
                            <line x1="15" y1="12" x2="3" y2="12"/>
                        </svg>
                    </div>
                    <div class="kpi-details">
                        <span class="kpi-label">Active Exits</span>
                        <strong class="kpi-value" id="exitActive">0</strong>
                        <span class="kpi-pill-badge">Clearances</span>
                    </div>
                </div>
            </div>

            <!-- 2. TWO-COLUMN CRM LAYOUT (Exact match with Employee Directory) -->
            <div class="employee-layout">

                <!-- MAIN COLUMN: MASTER LIFECYCLE TABLE CARD -->
                <article class="hr-employee-card employee-directory-card">
                    <!-- Segmented Tabs -->
                    <div class="employee-tabs">
                        <button type="button" class="active" data-tab="jobs">Job Openings <span class="tab-badge" id="tabJobsBadge">0</span></button>
                        <button type="button" data-tab="candidates">Candidates <span class="tab-badge" id="tabCandidatesBadge">0</span></button>
                        <button type="button" data-tab="joiners">New Joiners <span class="tab-badge" id="tabJoinersBadge">0</span></button>
                        <button type="button" data-tab="exit">Exit Process <span class="tab-badge" id="tabExitBadge">0</span></button>
                        <div class="employee-tab-actions">
                            <button type="button" id="exportRecBtn">Export ⌄</button>
                            <button type="button" class="hr-primary-action" id="createJobBtn">＋ Post Job</button>
                        </div>
                    </div>

                    <!-- Filter Toolbar -->
                    <div class="employee-toolbar">
                        <div class="employee-search">⌕<input id="tableSearch" type="search" placeholder="Search by title, candidate name, department..."></div>
                        <select id="tableDepartment"><option value="">All Departments</option></select>
                        <select id="tableStatus">
                            <option value="">All Status</option>
                            <option value="OPEN">Open</option>
                            <option value="CLOSED">Closed</option>
                            <option value="ACTIVE">Active</option>
                            <option value="IN_PROGRESS">In Progress</option>
                        </select>
                        <span id="tableCount"></span>
                    </div>

                    <div id="tableState" class="employee-state" style="display:none;">Loading data...</div>

                    <!-- Single Responsive Table -->
                    <div class="employee-table-wrap">
                        <table>
                            <thead id="recTableHead">
                                <!-- Populated dynamically -->
                            </thead>
                            <tbody id="recTableBody">
                                <tr><td colspan="7" class="employee-state">Loading records...</td></tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Table Footer & Pagination -->
                    <div class="employee-footer">
                        <span id="tablePageInfo"></span>
                        <div id="tablePagination"></div>
                        <label>Rows per page 
                            <select id="tablePerPage">
                                <option value="10" selected>10</option>
                                <option value="25">25</option>
                                <option value="50">50</option>
                            </select>
                        </label>
                    </div>
                </article>

                <!-- RIGHT COLUMN: INSIGHTS RAIL -->
                <aside class="employee-insights">
                    <!-- Insight 1: Recruitment Pipeline Funnel -->
                    <article class="insight-card">
                        <div class="insight-heading">
                            <h2>Recruitment Funnel</h2>
                            <span id="recFunnelSummary" style="font-size:11px;font-weight:700;color:#be185d;">Pipeline</span>
                        </div>
                        <div id="pipelineFunnel" class="horizontal-chart">Loading funnel...</div>
                    </article>

                    <!-- Insight 2: Candidate Acquisition Sources -->
                    <article class="insight-card">
                        <h2>Acquisition Sources</h2>
                        <div id="sourceChart" class="horizontal-chart">Loading sources...</div>
                    </article>

                    <!-- Insight 3: Upcoming Interviews & Starts -->
                    <article class="insight-card">
                        <div class="insight-heading">
                            <h2>Upcoming Schedule</h2>
                            <span style="font-size:10px;font-weight:700;color:#7e22ce;">Interviews &amp; Starts</span>
                        </div>
                        <div id="upcomingScheduleList" class="recent-joiners">Loading schedule...</div>
                        <a class="recent-joiners-button" href="javascript:void(0)" id="scheduleInterviewBtn">＋ Schedule Interview</a>
                    </article>
                </aside>

            </div>
        </section>
    </main>
</div>

<!-- Modal for Create Job Opening -->
<div class="lm-modal" id="jobModal" aria-hidden="true" style="display:none;">
    <div class="lm-modal-card" style="background:#fff;border-radius:18px;max-width:520px;width:100%;padding:24px;border:1px solid #ebd7e7;box-shadow:0 20px 50px rgba(43,23,72,0.25);">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:18px;">
            <h2 style="margin:0;font-size:18px;font-weight:800;color:#2b1748;">Post New Job Opening</h2>
            <button type="button" id="closeJobModal" style="background:transparent;border:0;font-size:24px;color:#756b85;cursor:pointer;">&times;</button>
        </div>
        <form id="createJobForm">
            <div style="display:flex;flex-direction:column;gap:12px;">
                <label style="font-size:12.5px;font-weight:700;color:#2b1748;">Job Title
                    <input type="text" id="jobTitleInput" required placeholder="e.g. Senior Frontend Developer" style="width:100%;padding:10px 14px;border:1px solid #ebd7e7;border-radius:10px;margin-top:4px;box-sizing:border-box;">
                </label>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                    <label style="font-size:12.5px;font-weight:700;color:#2b1748;">Department
                        <input type="text" id="jobDeptInput" required placeholder="e.g. Engineering" style="width:100%;padding:10px 14px;border:1px solid #ebd7e7;border-radius:10px;margin-top:4px;box-sizing:border-box;">
                    </label>
                    <label style="font-size:12.5px;font-weight:700;color:#2b1748;">Location
                        <input type="text" id="jobLocationInput" required placeholder="e.g. Mumbai / Remote" style="width:100%;padding:10px 14px;border:1px solid #ebd7e7;border-radius:10px;margin-top:4px;box-sizing:border-box;">
                    </label>
                </div>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                    <label style="font-size:12.5px;font-weight:700;color:#2b1748;">Employment Type
                        <select id="jobTypeInput" style="width:100%;padding:10px 14px;border:1px solid #ebd7e7;border-radius:10px;margin-top:4px;box-sizing:border-box;background:#fff;">
                            <option value="Full-time">Full-time</option>
                            <option value="Part-time">Part-time</option>
                            <option value="Contract">Contract</option>
                            <option value="Internship">Internship</option>
                        </select>
                    </label>
                    <label style="font-size:12.5px;font-weight:700;color:#2b1748;">Vacancies
                        <input type="number" id="jobVacanciesInput" value="1" min="1" max="100" style="width:100%;padding:10px 14px;border:1px solid #ebd7e7;border-radius:10px;margin-top:4px;box-sizing:border-box;">
                    </label>
                </div>
                <label style="font-size:12.5px;font-weight:700;color:#2b1748;">Hiring Manager
                    <input type="text" id="jobManagerInput" placeholder="e.g. David Lee" style="width:100%;padding:10px 14px;border:1px solid #ebd7e7;border-radius:10px;margin-top:4px;box-sizing:border-box;">
                </label>
            </div>
            <div style="display:flex;justify-content:flex-end;gap:10px;margin-top:20px;">
                <button type="button" id="cancelJobModal" style="padding:10px 18px;border-radius:10px;border:1px solid #ebd7e7;background:#f8edf7;color:#2b1748;font-weight:700;cursor:pointer;">Cancel</button>
                <button type="submit" class="hr-primary-action" style="cursor:pointer;">Publish Job</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal for View & Edit Job Details (Action Button) -->
<div class="lm-modal" id="jobDetailsModal" aria-hidden="true" style="display:none;">
    <div class="lm-modal-card" style="background:#fff;border-radius:18px;max-width:560px;width:100%;padding:26px;border:1px solid #ebd7e7;box-shadow:0 24px 60px rgba(43,23,72,0.28);">
        <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:18px;border-bottom:1px solid #f2e2ee;padding-bottom:14px;">
            <div>
                <span class="employee-code" id="viewJobCode" style="font-size:11px;font-weight:800;color:#be185d;background:#fdf2f8;border-color:#fbcfe8;">JOB-101</span>
                <h2 id="viewJobHeaderTitle" style="margin:6px 0 0;font-size:19px;font-weight:800;color:#2b1748;">Role / Job Title</h2>
            </div>
            <button type="button" id="closeJobDetailsModal" style="background:transparent;border:0;font-size:24px;color:#756b85;cursor:pointer;line-height:1;">&times;</button>
        </div>
        <form id="editJobForm">
            <input type="hidden" id="editJobId">
            <div style="display:flex;flex-direction:column;gap:13px;">
                <label style="font-size:12.5px;font-weight:700;color:#2b1748;">Job Title
                    <input type="text" id="editJobTitle" required style="width:100%;padding:10px 14px;border:1px solid #ebd7e7;border-radius:10px;margin-top:4px;box-sizing:border-box;font-size:13px;color:#2b1748;outline:none;">
                </label>
                
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                    <label style="font-size:12.5px;font-weight:700;color:#2b1748;">Department
                        <input type="text" id="editJobDepartment" required style="width:100%;padding:10px 14px;border:1px solid #ebd7e7;border-radius:10px;margin-top:4px;box-sizing:border-box;font-size:13px;color:#2b1748;outline:none;">
                    </label>
                    <label style="font-size:12.5px;font-weight:700;color:#2b1748;">Location
                        <input type="text" id="editJobLocation" required style="width:100%;padding:10px 14px;border:1px solid #ebd7e7;border-radius:10px;margin-top:4px;box-sizing:border-box;font-size:13px;color:#2b1748;outline:none;">
                    </label>
                </div>

                <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                    <label style="font-size:12.5px;font-weight:700;color:#2b1748;">Employment Type
                        <select id="editJobType" style="width:100%;padding:10px 14px;border:1px solid #ebd7e7;border-radius:10px;margin-top:4px;box-sizing:border-box;font-size:13px;color:#2b1748;outline:none;background:#fff;">
                            <option value="Full-time">Full-time</option>
                            <option value="Part-time">Part-time</option>
                            <option value="Contract">Contract</option>
                            <option value="Internship">Internship</option>
                        </select>
                    </label>
                    <label style="font-size:12.5px;font-weight:700;color:#2b1748;">Vacancies
                        <input type="number" id="editJobVacancies" min="1" max="100" style="width:100%;padding:10px 14px;border:1px solid #ebd7e7;border-radius:10px;margin-top:4px;box-sizing:border-box;font-size:13px;color:#2b1748;outline:none;">
                    </label>
                </div>

                <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                    <label style="font-size:12.5px;font-weight:700;color:#2b1748;">Hiring Manager
                        <input type="text" id="editJobManager" style="width:100%;padding:10px 14px;border:1px solid #ebd7e7;border-radius:10px;margin-top:4px;box-sizing:border-box;font-size:13px;color:#2b1748;outline:none;">
                    </label>
                    <label style="font-size:12.5px;font-weight:700;color:#2b1748;">Listing Status
                        <select id="editJobStatus" style="width:100%;padding:10px 14px;border:1px solid #ebd7e7;border-radius:10px;margin-top:4px;box-sizing:border-box;font-size:13px;font-weight:700;color:#2b1748;outline:none;background:#fff;">
                            <option value="OPEN">🟢 OPEN (Active)</option>
                            <option value="CLOSED">🟣 CLOSED (Filled/Archived)</option>
                            <option value="DRAFT">⚪ DRAFT (Unpublished)</option>
                        </select>
                    </label>
                </div>

                <div style="background:#fffafd;border:1px solid #f2e2ee;border-radius:12px;padding:12px 14px;display:flex;justify-content:space-between;align-items:center;margin-top:4px;">
                    <div>
                        <span style="font-size:11px;color:#756b85;display:block;">Talent Pipeline</span>
                        <strong id="viewJobApplicantsText" style="font-size:13px;color:#be185d;">0 Applicants</strong>
                    </div>
                    <div>
                        <span style="font-size:11px;color:#756b85;display:block;text-align:right;">Date Posted</span>
                        <span id="viewJobPostedDate" style="font-size:12px;font-weight:700;color:#2b1748;">Recent</span>
                    </div>
                </div>
            </div>

            <div style="display:flex;justify-content:space-between;align-items:center;margin-top:22px;border-top:1px solid #f2e2ee;padding-top:16px;">
                <button type="button" id="quickToggleJobStatusBtn" style="padding:9px 14px;border-radius:10px;border:1px solid #ebd7e7;background:#fff;color:#7e22ce;font-size:12px;font-weight:700;cursor:pointer;">Switch Status</button>
                <div style="display:flex;gap:10px;">
                    <button type="button" id="cancelJobDetailsModal" style="padding:9px 16px;border-radius:10px;border:1px solid #ebd7e7;background:#f8edf7;color:#2b1748;font-weight:700;cursor:pointer;">Cancel</button>
                    <button type="submit" id="saveJobDetailsBtn" class="hr-primary-action" style="padding:9px 20px;border-radius:10px;cursor:pointer;font-weight:700;">Save Changes</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Modal for Review Candidate Profile -->
<div class="lm-modal" id="candidateReviewModal" aria-hidden="true" style="display:none;">
    <div class="lm-modal-card" style="background:#fff;border-radius:18px;max-width:540px;width:100%;padding:26px;border:1px solid #ebd7e7;box-shadow:0 24px 60px rgba(43,23,72,0.28);">
        <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:16px;border-bottom:1px solid #f2e2ee;padding-bottom:14px;">
            <div style="display:flex;gap:12px;align-items:center;">
                <span id="candReviewAvatar" class="candidate-avatar" style="width:42px;height:42px;font-size:16px;">CA</span>
                <div>
                    <h2 id="candReviewName" style="margin:0;font-size:18px;font-weight:800;color:#2b1748;">Candidate Name</h2>
                    <small id="candReviewEmail" style="color:#756b85;font-size:12px;">candidate@company.com</small>
                </div>
            </div>
            <button type="button" id="closeCandModal" style="background:transparent;border:0;font-size:24px;color:#756b85;cursor:pointer;line-height:1;">&times;</button>
        </div>
        <form id="candidateReviewForm">
            <input type="hidden" id="candReviewId">
            <div style="display:flex;flex-direction:column;gap:12px;">
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                    <div style="background:#fffafd;border:1px solid #f2e2ee;padding:10px 14px;border-radius:10px;">
                        <span style="font-size:11px;color:#756b85;display:block;">Applied Position</span>
                        <strong id="candReviewRole" style="font-size:13px;color:#2b1748;">Frontend Developer</strong>
                    </div>
                    <div style="background:#fffafd;border:1px solid #f2e2ee;padding:10px 14px;border-radius:10px;">
                        <span style="font-size:11px;color:#756b85;display:block;">Department</span>
                        <strong id="candReviewDept" style="font-size:13px;color:#2b1748;">Engineering</strong>
                    </div>
                </div>

                <label style="font-size:12.5px;font-weight:700;color:#2b1748;margin-top:4px;">Pipeline Stage
                    <select id="candReviewStage" style="width:100%;padding:10px 14px;border:1px solid #ebd7e7;border-radius:10px;margin-top:4px;box-sizing:border-box;font-size:13px;font-weight:700;color:#2b1748;background:#fff;">
                        <option value="APPLIED">Applied / New</option>
                        <option value="SCREENING">Screening / Review</option>
                        <option value="INTERVIEW">Interview Round</option>
                        <option value="OFFERED">Offer Extended</option>
                        <option value="HIRED">Hired / Accepted</option>
                        <option value="REJECTED">Archived / Rejected</option>
                    </select>
                </label>

                <div style="background:#fffafd;border:1px solid #f2e2ee;padding:12px;border-radius:10px;">
                    <span style="font-size:11.5px;font-weight:700;color:#581c87;display:block;margin-bottom:6px;">Applicant Timeline</span>
                    <div style="font-size:12px;color:#756b85;display:flex;justify-content:space-between;">
                        <span>Resume Submitted</span>
                        <strong id="candReviewDate" style="color:#2b1748;">Recent</strong>
                    </div>
                </div>
            </div>

            <div style="display:flex;justify-content:flex-end;gap:10px;margin-top:20px;border-top:1px solid #f2e2ee;padding-top:16px;">
                <button type="button" id="cancelCandModal" style="padding:9px 16px;border-radius:10px;border:1px solid #ebd7e7;background:#f8edf7;color:#2b1748;font-weight:700;cursor:pointer;">Cancel</button>
                <button type="submit" class="hr-primary-action" style="padding:9px 20px;border-radius:10px;cursor:pointer;font-weight:700;">Update Stage</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal for New Joiner Track Checklist -->
<div class="lm-modal" id="joinerTrackModal" aria-hidden="true" style="display:none;">
    <div class="lm-modal-card" style="background:#fff;border-radius:18px;max-width:560px;width:100%;padding:26px;border:1px solid #ebd7e7;box-shadow:0 24px 60px rgba(43,23,72,0.28);">
        <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:16px;border-bottom:1px solid #f2e2ee;padding-bottom:14px;">
            <div style="display:flex;gap:12px;align-items:center;">
                <span id="joinerAvatar" class="candidate-avatar" style="width:42px;height:42px;font-size:16px;">NJ</span>
                <div>
                    <h2 id="joinerName" style="margin:0;font-size:18px;font-weight:800;color:#2b1748;">Employee Name</h2>
                    <small id="joinerRoleDept" style="color:#756b85;font-size:12px;">Engineering · Backend Engineer</small>
                </div>
            </div>
            <button type="button" id="closeJoinerModal" style="background:transparent;border:0;font-size:24px;color:#756b85;cursor:pointer;line-height:1;">&times;</button>
        </div>
        <form id="joinerTrackForm">
            <input type="hidden" id="joinerTrackId">
            <input type="hidden" id="joinerEmpCode">
            <div style="display:flex;flex-direction:column;gap:14px;">
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                    <label style="font-size:12.5px;font-weight:700;color:#2b1748;">Onboarding Status
                        <select id="joinerStatusInput" style="width:100%;padding:10px 14px;border:1px solid #ebd7e7;border-radius:10px;margin-top:4px;box-sizing:border-box;font-size:13px;font-weight:700;color:#2b1748;background:#fff;outline:none;">
                            <option value="IN_PROGRESS">🟣 In Progress</option>
                            <option value="PENDING">🟡 Pending Setup</option>
                            <option value="COMPLETED">🟢 Completed / Active</option>
                        </select>
                    </label>
                    <label style="font-size:12.5px;font-weight:700;color:#2b1748;">Current Stage
                        <input type="text" id="joinerStageInput" placeholder="e.g. IT Setup & Documentation" style="width:100%;padding:10px 14px;border:1px solid #ebd7e7;border-radius:10px;margin-top:4px;box-sizing:border-box;font-size:13px;color:#2b1748;outline:none;">
                    </label>
                </div>

                <div style="background:#fdf2f8;border:1px solid #fbcfe8;border-radius:12px;padding:12px 16px;">
                    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:8px;">
                        <span style="font-size:12px;font-weight:700;color:#756b85;">Onboarding Progress</span>
                        <strong id="joinerProgressVal" style="font-size:14px;color:#be185d;">75% Completed</strong>
                    </div>
                    <input type="range" id="joinerProgressSlider" min="0" max="100" step="5" value="75" style="width:100%;accent-color:#be185d;cursor:pointer;">
                </div>

                <div style="display:flex;flex-direction:column;gap:8px;">
                    <span style="font-size:12px;font-weight:700;color:#581c87;">Onboarding Checklist</span>
                    <label style="display:flex;align-items:center;gap:10px;padding:9px 12px;background:#fffafd;border:1px solid #f2e2ee;border-radius:10px;font-size:12.5px;cursor:pointer;">
                        <input type="checkbox" id="joinerChk1" class="joiner-chk" checked style="accent-color:#be185d;"> 📋 Background Verification &amp; HR Records
                    </label>
                    <label style="display:flex;align-items:center;gap:10px;padding:9px 12px;background:#fffafd;border:1px solid #f2e2ee;border-radius:10px;font-size:12.5px;cursor:pointer;">
                        <input type="checkbox" id="joinerChk2" class="joiner-chk" checked style="accent-color:#be185d;"> 💻 IT Workstation &amp; Cloud Account Provisioning
                    </label>
                    <label style="display:flex;align-items:center;gap:10px;padding:9px 12px;background:#fffafd;border:1px solid #f2e2ee;border-radius:10px;font-size:12.5px;cursor:pointer;">
                        <input type="checkbox" id="joinerChk3" class="joiner-chk" checked style="accent-color:#be185d;"> 🤝 Team Welcome &amp; Orientation Session
                    </label>
                    <label style="display:flex;align-items:center;gap:10px;padding:9px 12px;background:#fffafd;border:1px solid #f2e2ee;border-radius:10px;font-size:12.5px;cursor:pointer;">
                        <input type="checkbox" id="joinerChk4" class="joiner-chk" style="accent-color:#be185d;"> 🎯 30-Day Goal Alignment Review
                    </label>
                </div>
            </div>
            <div style="display:flex;justify-content:space-between;align-items:center;margin-top:20px;border-top:1px solid #f2e2ee;padding-top:16px;">
                <button type="button" id="toggleJoinerStatusBtn" style="padding:9px 14px;border-radius:10px;border:1px solid #ebd7e7;background:#fff;color:#7e22ce;font-size:12px;font-weight:700;cursor:pointer;">Toggle Status</button>
                <div style="display:flex;gap:10px;">
                    <button type="button" id="cancelJoinerModal" style="padding:9px 16px;border-radius:10px;border:1px solid #ebd7e7;background:#f8edf7;color:#2b1748;font-weight:700;cursor:pointer;">Cancel</button>
                    <button type="submit" id="saveJoinerTrackBtn" class="hr-primary-action" style="padding:9px 20px;border-radius:10px;cursor:pointer;font-weight:700;">Save Progress</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Modal for Exit Clearance Checklist -->
<div class="lm-modal" id="exitClearanceModal" aria-hidden="true" style="display:none;">
    <div class="lm-modal-card" style="background:#fff;border-radius:18px;max-width:560px;width:100%;padding:26px;border:1px solid #ebd7e7;box-shadow:0 24px 60px rgba(43,23,72,0.28);">
        <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:16px;border-bottom:1px solid #f2e2ee;padding-bottom:14px;">
            <div style="display:flex;gap:12px;align-items:center;">
                <span id="exitAvatar" class="candidate-avatar" style="width:42px;height:42px;font-size:16px;">EX</span>
                <div>
                    <h2 id="exitName" style="margin:0;font-size:18px;font-weight:800;color:#2b1748;">Employee Name</h2>
                    <small id="exitDept" style="color:#756b85;font-size:12px;">Engineering</small>
                </div>
            </div>
            <button type="button" id="closeExitModal" style="background:transparent;border:0;font-size:24px;color:#756b85;cursor:pointer;line-height:1;">&times;</button>
        </div>
        <form id="exitClearanceForm">
            <input type="hidden" id="exitClearanceId">
            <input type="hidden" id="exitCode">
            <div style="display:flex;flex-direction:column;gap:14px;">
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                    <label style="font-size:12.5px;font-weight:700;color:#2b1748;">Clearance Status
                        <select id="exitStatusInput" style="width:100%;padding:10px 14px;border:1px solid #ebd7e7;border-radius:10px;margin-top:4px;box-sizing:border-box;font-size:13px;font-weight:700;color:#2b1748;background:#fff;outline:none;">
                            <option value="NOTICE">🟡 Notice Period</option>
                            <option value="IN_PROGRESS">🟣 Clearance In Progress</option>
                            <option value="COMPLETED">🟢 Clearance Completed</option>
                        </select>
                    </label>
                    <label style="font-size:12.5px;font-weight:700;color:#2b1748;">Last Working Day
                        <input type="text" id="exitLastDateInput" placeholder="YYYY-MM-DD" style="width:100%;padding:10px 14px;border:1px solid #ebd7e7;border-radius:10px;margin-top:4px;box-sizing:border-box;font-size:13px;color:#2b1748;outline:none;">
                    </label>
                </div>

                <label style="font-size:12.5px;font-weight:700;color:#2b1748;">Clearance Stage
                    <input type="text" id="exitStageInput" placeholder="e.g. IT Asset Handover Pending" style="width:100%;padding:10px 14px;border:1px solid #ebd7e7;border-radius:10px;margin-top:4px;box-sizing:border-box;font-size:13px;color:#2b1748;outline:none;">
                </label>

                <div style="background:#fffafd;border:1px solid #f2e2ee;border-radius:12px;padding:12px 16px;">
                    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:8px;">
                        <span style="font-size:12px;font-weight:700;color:#756b85;">Exit Clearance Progress</span>
                        <strong id="exitProgressVal" style="font-size:14px;color:#581c87;">60% Completed</strong>
                    </div>
                    <input type="range" id="exitProgressSlider" min="0" max="100" step="5" value="60" style="width:100%;accent-color:#581c87;cursor:pointer;">
                </div>

                <div style="display:flex;flex-direction:column;gap:8px;">
                    <span style="font-size:12px;font-weight:700;color:#581c87;">Department Approvals</span>
                    <label style="display:flex;align-items:center;gap:10px;padding:9px 12px;background:#fffafd;border:1px solid #f2e2ee;border-radius:10px;font-size:12.5px;cursor:pointer;">
                        <input type="checkbox" id="exitChk1" class="exit-chk" checked style="accent-color:#581c87;"> 💻 IT Assets &amp; Laptop Return Clearance
                    </label>
                    <label style="display:flex;align-items:center;gap:10px;padding:9px 12px;background:#fffafd;border:1px solid #f2e2ee;border-radius:10px;font-size:12.5px;cursor:pointer;">
                        <input type="checkbox" id="exitChk2" class="exit-chk" checked style="accent-color:#581c87;"> 💰 Finance &amp; Expense Dues Settlement
                    </label>
                    <label style="display:flex;align-items:center;gap:10px;padding:9px 12px;background:#fffafd;border:1px solid #f2e2ee;border-radius:10px;font-size:12.5px;cursor:pointer;">
                        <input type="checkbox" id="exitChk3" class="exit-chk" style="accent-color:#581c87;"> 💬 HR Exit Interview Questionnaire Completed
                    </label>
                    <label style="display:flex;align-items:center;gap:10px;padding:9px 12px;background:#fffafd;border:1px solid #f2e2ee;border-radius:10px;font-size:12.5px;cursor:pointer;">
                        <input type="checkbox" id="exitChk4" class="exit-chk" style="accent-color:#581c87;"> 🔒 System Access &amp; Email Deprovisioning
                    </label>
                </div>
            </div>
            <div style="display:flex;justify-content:space-between;align-items:center;margin-top:20px;border-top:1px solid #f2e2ee;padding-top:16px;">
                <button type="button" id="toggleExitStatusBtn" style="padding:9px 14px;border-radius:10px;border:1px solid #ebd7e7;background:#fff;color:#be185d;font-size:12px;font-weight:700;cursor:pointer;">Toggle Status</button>
                <div style="display:flex;gap:10px;">
                    <button type="button" id="cancelExitModal" style="padding:9px 16px;border-radius:10px;border:1px solid #ebd7e7;background:#f8edf7;color:#2b1748;font-weight:700;cursor:pointer;">Cancel</button>
                    <button type="submit" id="saveExitClearanceBtn" class="hr-primary-action" style="padding:9px 20px;border-radius:10px;cursor:pointer;font-weight:700;">Save Clearance</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Toast Container -->
<div id="recToastContainer" class="rec-toast-container"></div>

<script src="../assets/js/theme.js?v=<?= time() ?>"></script>
<script src="../assets/js/hr-recruitment.js?v=<?= time() ?>"></script>
</body>
</html>

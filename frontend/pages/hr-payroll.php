<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Payroll Workspace — Manage monthly employee salaries, allowances, deductions, payslips and payout runs.">
    <title>Payroll - HR Portal</title>
    <link rel="stylesheet" href="../assets/css/app-theme.css?v=<?= time() ?>">
    <link rel="stylesheet" href="../assets/css/hr-shell.css?v=<?= time() ?>">
    <link rel="stylesheet" href="../assets/css/hr-employees.css?v=<?= time() ?>">
    <link rel="stylesheet" href="../assets/css/hr-payroll.css?v=<?= time() ?>">
</head>
<body>
<div class="app">
    <?php include '../includes/hr-sidebar.php'; ?>

    <main class="main-content">
        <?php include '../includes/hr-topbar.php'; ?>

        <section class="content hr-employees-page hr-payroll-page">

            <!-- 1. SIX KPI PILL CARDS (Exact match with HR Employees design) -->
            <div class="employee-kpis" id="payrollKpis">
                <!-- 1. Total Net Pay -->
                <div class="employee-kpi kpi-theme-plum">
                    <div class="kpi-wave"></div>
                    <div class="kpi-icon-box">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="2" y="4" width="20" height="16" rx="2"/>
                            <line x1="2" y1="10" x2="22" y2="10"/>
                            <path d="M6 16h2"/>
                            <path d="M12 16h6"/>
                        </svg>
                    </div>
                    <div class="kpi-details">
                        <span class="kpi-label">Total Net Pay</span>
                        <strong class="kpi-value" id="kpiTotalNet">₹ 0</strong>
                        <span class="kpi-pill-badge" id="kpiMonthLabel">Monthly Net</span>
                    </div>
                </div>

                <!-- 2. Gross Payroll -->
                <div class="employee-kpi kpi-theme-magenta">
                    <div class="kpi-wave"></div>
                    <div class="kpi-icon-box">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="12" y1="1" x2="12" y2="23"/>
                            <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/>
                        </svg>
                    </div>
                    <div class="kpi-details">
                        <span class="kpi-label">Gross Payroll</span>
                        <strong class="kpi-value" id="kpiGrossPay">₹ 0</strong>
                        <span class="kpi-pill-badge">Before Deductions</span>
                    </div>
                </div>

                <!-- 3. Eligible Staff -->
                <div class="employee-kpi kpi-theme-violet">
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
                        <span class="kpi-label">Total Staff</span>
                        <strong class="kpi-value" id="kpiTotalStaff">0</strong>
                        <span class="kpi-pill-badge">On Payroll</span>
                    </div>
                </div>

                <!-- 4. Paid Employees -->
                <div class="employee-kpi kpi-theme-fuchsia">
                    <div class="kpi-wave"></div>
                    <div class="kpi-icon-box">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                            <polyline points="22 4 12 14.01 9 11.01"/>
                        </svg>
                    </div>
                    <div class="kpi-details">
                        <span class="kpi-label">Disbursed</span>
                        <strong class="kpi-value" id="kpiPaidCount">0</strong>
                        <span class="kpi-pill-badge">Completed</span>
                    </div>
                </div>

                <!-- 5. Pending Payment -->
                <div class="employee-kpi kpi-theme-pink">
                    <div class="kpi-wave"></div>
                    <div class="kpi-icon-box">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="10"/>
                            <polyline points="12 6 12 12 16 14"/>
                        </svg>
                    </div>
                    <div class="kpi-details">
                        <span class="kpi-label">Pending Runs</span>
                        <strong class="kpi-value" id="kpiPendingCount">0</strong>
                        <span class="kpi-pill-badge">Awaiting Payout</span>
                    </div>
                </div>

                <!-- 6. Average Net Pay -->
                <div class="employee-kpi kpi-theme-rose">
                    <div class="kpi-wave"></div>
                    <div class="kpi-icon-box">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="23 6 13.5 15.5 8.5 10.5 1 18"/>
                            <polyline points="17 6 23 6 23 12"/>
                        </svg>
                    </div>
                    <div class="kpi-details">
                        <span class="kpi-label">Average Net Pay</span>
                        <strong class="kpi-value" id="kpiAvgNet">₹ 0</strong>
                        <span class="kpi-pill-badge">Per Employee</span>
                    </div>
                </div>
            </div>

            <!-- 2. TWO-COLUMN CRM LAYOUT -->
            <div class="employee-layout">

                <!-- MAIN COLUMN: MASTER PAYROLL TABLE CARD -->
                <article class="hr-employee-card employee-directory-card">
                    <!-- Segmented Tabs -->
                    <div class="employee-tabs">
                        <button type="button" class="active" data-tab="all">Payroll Register <span class="tab-badge" id="tabAllBadge">0</span></button>
                        <button type="button" data-tab="pending">Pending Runs <span class="tab-badge" id="tabPendingBadge">0</span></button>
                        <button type="button" data-tab="paid">Disbursed <span class="tab-badge" id="tabPaidBadge">0</span></button>
                        
                        <div class="employee-tab-actions">
                            <!-- Pay Period Month Selector -->
                            <div class="payroll-month-picker">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                                    <line x1="16" y1="2" x2="16" y2="6"/>
                                    <line x1="8" y1="2" x2="8" y2="6"/>
                                    <line x1="3" y1="10" x2="21" y2="10"/>
                                </svg>
                                <input type="month" id="payrollMonth" aria-label="Pay Period Month">
                            </div>
                            <button type="button" id="exportPayrollBtn">Export ⌄</button>
                            <button type="button" class="hr-primary-action" id="runPayrollBtn">⚡ Run Payroll</button>
                        </div>
                    </div>

                    <!-- Filter Toolbar -->
                    <div class="employee-toolbar">
                        <div class="employee-search">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                                <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                            </svg>
                            <input id="payrollSearch" type="search" placeholder="Search by employee name, code, designation...">
                        </div>
                        <select id="payrollDepartment"><option value="">All Departments</option></select>
                        <select id="payrollStatus">
                            <option value="">All Status</option>
                            <option value="PAID">Paid</option>
                            <option value="PENDING">Pending</option>
                            <option value="VOID">Void / Hold</option>
                        </select>
                        <span id="payrollCount">0 record(s)</span>
                    </div>

                    <div id="payrollState" class="employee-state" style="display:none;">Loading payroll records...</div>

                    <!-- Single Responsive Table -->
                    <div class="employee-table-wrap">
                        <table>
                            <thead>
                                <tr>
                                    <th style="width:36px;">#</th>
                                    <th style="width:95px;">Emp ID</th>
                                    <th>Employee</th>
                                    <th>Department</th>
                                    <th>Basic Pay</th>
                                    <th>Allowances</th>
                                    <th>Deductions</th>
                                    <th>Net Pay</th>
                                    <th>Status</th>
                                    <th style="text-align:right;">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="payrollRows">
                                <tr><td colspan="10" class="employee-state">Loading records...</td></tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Table Footer & Pagination -->
                    <div class="employee-footer">
                        <span id="payrollPageInfo">Showing records...</span>
                        <div id="payrollPagination"></div>
                        <label>Rows per page 
                            <select id="payrollPerPage">
                                <option value="10" selected>10</option>
                                <option value="25">25</option>
                                <option value="50">50</option>
                            </select>
                        </label>
                    </div>
                </article>

                <!-- RIGHT COLUMN: INSIGHTS RAIL -->
                <aside class="employee-insights">
                    <!-- Insight 1: Cost Breakdown -->
                    <article class="insight-card">
                        <div class="insight-heading">
                            <h2>Payroll Summary</h2>
                            <span id="payrollSummaryMonth" style="font-size:11px;font-weight:700;color:#be185d;">Current Month</span>
                        </div>
                        <div id="payrollBreakdown" class="horizontal-chart">Loading breakdown...</div>
                    </article>

                    <!-- Insight 2: Department Distribution -->
                    <article class="insight-card">
                        <h2>Payroll by Department</h2>
                        <div id="payrollDepartmentChart" class="horizontal-chart">Loading distribution...</div>
                    </article>

                    <!-- Insight 3: Recent Payments / Quick Action -->
                    <article class="insight-card">
                        <div class="insight-heading">
                            <h2>Recent Payments</h2>
                            <span style="font-size:10px;font-weight:700;color:#7e22ce;">Payout History</span>
                        </div>
                        <div id="payrollRecentPayments" class="recent-joiners">Loading payments...</div>
                        <a class="recent-joiners-button" href="javascript:void(0)" id="quickRunPayrollBtn">⚡ Process All Payments →</a>
                    </article>
                </aside>

            </div>
        </section>
    </main>
</div>

<!-- LUXURY PAYSLIP MODAL -->
<div class="lm-modal" id="payslipModal" aria-hidden="true" style="display:none;">
    <div class="payslip-modal-card">
        <div class="payslip-modal-header">
            <div>
                <span class="payslip-badge">OFFICIAL SALARY SLIP</span>
                <h2 id="payslipModalTitle">Employee Payslip</h2>
            </div>
            <button type="button" id="closePayslipModal" class="payslip-close-btn">&times;</button>
        </div>
        
        <div id="payslipContent" class="payslip-modal-body">
            <!-- Populated dynamically -->
        </div>

        <div class="payslip-modal-footer">
            <button type="button" id="cancelPayslipBtn" class="rec-action-btn">Close</button>
            <button type="button" id="printPayslipBtn" class="hr-primary-action">🖨 Print Payslip</button>
        </div>
    </div>
</div>

<div class="toast-container" id="payrollToastContainer"></div>

<script src="../assets/js/theme.js?v=<?= time() ?>"></script>
<script src="../assets/js/hr-payroll.js?v=<?= time() ?>"></script>
</body>
</html>
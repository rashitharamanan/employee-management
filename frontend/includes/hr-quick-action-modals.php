<!-- =========================================================================
     HR QUICK ACTION POPUPS / MODALS
     Self-contained include for HR Dashboard
     ========================================================================= -->

<!-- Toast Notification Container -->
<div id="hrToastContainer" class="hr-toast-container"></div>

<!-- 1. REVIEW LEAVE MODAL -->
<div class="hr-modal-overlay" id="hrQuickLeaveModal" role="dialog" aria-modal="true" aria-labelledby="hrQuickLeaveTitle">
    <div class="hr-modal-card hr-modal-wide">
        <div class="hr-modal-header">
            <div class="hr-modal-header-info">
                <div class="hr-modal-icon-badge badge-pink-soft">🏖️</div>
                <div class="hr-modal-title-text">
                    <h3 id="hrQuickLeaveTitle">Review Leave Requests</h3>
                    <p>Pending employee leave requests requiring HR approval</p>
                </div>
            </div>
            <button type="button" class="hr-modal-close-btn" onclick="closeHrQuickModal('hrQuickLeaveModal')" aria-label="Close modal">✕</button>
        </div>
        <div class="hr-modal-body">
            <div class="hr-quick-leave-list" id="hrQuickLeaveList">
                <!-- Dynamically populated or rendered -->
            </div>
        </div>
        <div class="hr-modal-footer">
            <a href="hr-leave.php" class="hr-modal-footer-link">Go to Full Leave Center →</a>
            <button type="button" class="hr-modal-btn-cancel" onclick="closeHrQuickModal('hrQuickLeaveModal')">Close</button>
        </div>
    </div>
</div>

<!-- 2. RUN PAYROLL MODAL -->
<div class="hr-modal-overlay" id="hrQuickPayrollModal" role="dialog" aria-modal="true" aria-labelledby="hrQuickPayrollTitle">
    <div class="hr-modal-card hr-modal-wide">
        <div class="hr-modal-header">
            <div class="hr-modal-header-info">
                <div class="hr-modal-icon-badge badge-violet-soft">💳</div>
                <div class="hr-modal-title-text">
                    <h3 id="hrQuickPayrollTitle">Run Monthly Payroll</h3>
                    <p>Review summary and disburse salaries for active staff</p>
                </div>
            </div>
            <button type="button" class="hr-modal-close-btn" onclick="closeHrQuickModal('hrQuickPayrollModal')" aria-label="Close modal">✕</button>
        </div>
        <form id="hrQuickPayrollForm" onsubmit="handleQuickPayrollSubmit(event)">
            <div class="hr-modal-body">
                <!-- Metric snapshot -->
                <div class="hr-payroll-metric-row">
                    <div class="hr-payroll-metric-box">
                        <span class="hr-payroll-metric-label">Employees</span>
                        <span class="hr-payroll-metric-val" id="quickPayEmpCount">9 Staff</span>
                    </div>
                    <div class="hr-payroll-metric-box">
                        <span class="hr-payroll-metric-label">Gross Pay</span>
                        <span class="hr-payroll-metric-val">₹4,85,000</span>
                    </div>
                    <div class="hr-payroll-metric-box">
                        <span class="hr-payroll-metric-label">Deductions</span>
                        <span class="hr-payroll-metric-val">₹72,500</span>
                    </div>
                    <div class="hr-payroll-metric-box">
                        <span class="hr-payroll-metric-label">Net Disbursal</span>
                        <span class="hr-payroll-metric-val highlight">₹4,12,500</span>
                    </div>
                </div>

                <div class="hr-form-grid-2">
                    <div class="hr-form-group">
                        <label class="hr-form-label" for="payrollCycleSelect">Pay Period Cycle <span class="req">*</span></label>
                        <select id="payrollCycleSelect" class="hr-form-select" required>
                            <option value="2026-09" selected>September 2026 (Current Cycle)</option>
                            <option value="2026-08">August 2026 (Past Cycle)</option>
                            <option value="2026-10">October 2026 (Upcoming)</option>
                        </select>
                    </div>
                    <div class="hr-form-group">
                        <label class="hr-form-label" for="payrollDisburseDate">Disbursement Date <span class="req">*</span></label>
                        <input type="date" id="payrollDisburseDate" class="hr-form-input" value="2026-10-10" required>
                    </div>
                </div>

                <div class="hr-form-grid-2">
                    <div class="hr-form-group">
                        <label class="hr-form-label" for="payrollChannelSelect">Payment Mode</label>
                        <select id="payrollChannelSelect" class="hr-form-select">
                            <option value="NEFT" selected>Direct Bank Transfer (NEFT / RTGS)</option>
                            <option value="UPI">Corporate UPI Payout</option>
                            <option value="CHEQUE">Bank Cheque</option>
                        </select>
                    </div>
                    <div class="hr-form-group">
                        <label class="hr-form-label" for="payrollRefNote">Reference Note</label>
                        <input type="text" id="payrollRefNote" class="hr-form-input" placeholder="e.g. Sep Regular Salary Disbursal" value="Sep Regular Salary Disbursal">
                    </div>
                </div>
            </div>
            <div class="hr-modal-footer">
                <a href="hr-payroll.php" class="hr-modal-footer-link">Open Full Payroll Register →</a>
                <div class="hr-modal-btn-group">
                    <button type="button" class="hr-modal-btn-cancel" onclick="closeHrQuickModal('hrQuickPayrollModal')">Cancel</button>
                    <button type="submit" class="hr-modal-btn-primary" id="btnSubmitPayroll">
                        <span>⚡ Process &amp; Run Payroll</span>
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- 3. POST JOB OPENING MODAL -->
<div class="hr-modal-overlay" id="hrQuickJobModal" role="dialog" aria-modal="true" aria-labelledby="hrQuickJobTitle">
    <div class="hr-modal-card hr-modal-wide">
        <div class="hr-modal-header">
            <div class="hr-modal-header-info">
                <div class="hr-modal-icon-badge badge-fuchsia-soft">💼</div>
                <div class="hr-modal-title-text">
                    <h3 id="hrQuickJobTitle">Post New Job Opening</h3>
                    <p>Create and publish an open requisition to the recruitment pipeline</p>
                </div>
            </div>
            <button type="button" class="hr-modal-close-btn" onclick="closeHrQuickModal('hrQuickJobModal')" aria-label="Close modal">✕</button>
        </div>
        <form id="hrQuickJobForm" onsubmit="handleQuickJobSubmit(event)">
            <div class="hr-modal-body">
                <div class="hr-form-group">
                    <label class="hr-form-label" for="jobTitleInput">Job Requisition Title <span class="req">*</span></label>
                    <input type="text" id="jobTitleInput" class="hr-form-input" placeholder="e.g. Lead Full-Stack Engineer" required>
                </div>

                <div class="hr-form-grid-2">
                    <div class="hr-form-group">
                        <label class="hr-form-label" for="jobDeptSelect">Department <span class="req">*</span></label>
                        <select id="jobDeptSelect" class="hr-form-select" required>
                            <option value="Engineering" selected>Engineering</option>
                            <option value="Design">Design</option>
                            <option value="Product">Product</option>
                            <option value="Human Resources">Human Resources</option>
                            <option value="Marketing">Marketing</option>
                            <option value="Operations">Operations</option>
                        </select>
                    </div>
                    <div class="hr-form-group">
                        <label class="hr-form-label" for="jobTypeSelect">Employment Type <span class="req">*</span></label>
                        <select id="jobTypeSelect" class="hr-form-select" required>
                            <option value="Full-time" selected>Full-time</option>
                            <option value="Part-time">Part-time</option>
                            <option value="Contract">Contract</option>
                            <option value="Internship">Internship</option>
                        </select>
                    </div>
                </div>

                <div class="hr-form-grid-2">
                    <div class="hr-form-group">
                        <label class="hr-form-label" for="jobVacanciesInput">Number of Vacancies <span class="req">*</span></label>
                        <input type="number" id="jobVacanciesInput" class="hr-form-input" min="1" max="50" value="1" required>
                    </div>
                    <div class="hr-form-group">
                        <label class="hr-form-label" for="jobLocationInput">Work Location</label>
                        <input type="text" id="jobLocationInput" class="hr-form-input" placeholder="e.g. Bangalore (Hybrid)" value="Bangalore (Hybrid)">
                    </div>
                </div>

                <div class="hr-form-group">
                    <label class="hr-form-label" for="jobDescText">Key Requirements / Notes</label>
                    <textarea id="jobDescText" class="hr-form-textarea" placeholder="Brief role expectations, tech stack, or hiring criteria..."></textarea>
                </div>
            </div>
            <div class="hr-modal-footer">
                <a href="hr-recruitment.php" class="hr-modal-footer-link">View Recruitment Pipeline →</a>
                <div class="hr-modal-btn-group">
                    <button type="button" class="hr-modal-btn-cancel" onclick="closeHrQuickModal('hrQuickJobModal')">Cancel</button>
                    <button type="submit" class="hr-modal-btn-primary" id="btnSubmitJob">
                        <span>＋ Publish Requisition</span>
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- 4. RECORD ATTENDANCE MODAL -->
<div class="hr-modal-overlay" id="hrQuickAttendanceModal" role="dialog" aria-modal="true" aria-labelledby="hrQuickAttendanceTitle">
    <div class="hr-modal-card">
        <div class="hr-modal-header">
            <div class="hr-modal-header-info">
                <div class="hr-modal-icon-badge badge-rose-soft">🕒</div>
                <div class="hr-modal-title-text">
                    <h3 id="hrQuickAttendanceTitle">Quick Record Attendance</h3>
                    <p>Mark attendance or log corrections for today</p>
                </div>
            </div>
            <button type="button" class="hr-modal-close-btn" onclick="closeHrQuickModal('hrQuickAttendanceModal')" aria-label="Close modal">✕</button>
        </div>
        <form id="hrQuickAttendanceForm" onsubmit="handleQuickAttendanceSubmit(event)">
            <div class="hr-modal-body">
                <div class="hr-form-group">
                    <label class="hr-form-label" for="attEmployeeSelect">Select Employee <span class="req">*</span></label>
                    <select id="attEmployeeSelect" class="hr-form-select" required>
                        <option value="1">John Doe — Engineering (EMP001)</option>
                        <option value="2">Jane Smith — HR (EMP002)</option>
                        <option value="3">Savitha R. — IT Support (EMP003)</option>
                        <option value="4">Priya Sharma — Product (EMP004)</option>
                        <option value="5">Arjun Mehta — QA Automation (EMP005)</option>
                        <option value="6">Ramesh K. — Operations (EMP006)</option>
                        <option value="7">Sneha Patel — Design (EMP007)</option>
                    </select>
                </div>

                <div class="hr-form-grid-2">
                    <div class="hr-form-group">
                        <label class="hr-form-label" for="attDateInput">Date <span class="req">*</span></label>
                        <input type="date" id="attDateInput" class="hr-form-input" value="2026-10-10" required>
                    </div>
                    <div class="hr-form-group">
                        <label class="hr-form-label" for="attTimeInput">Punch Time</label>
                        <input type="time" id="attTimeInput" class="hr-form-input" value="09:15">
                    </div>
                </div>

                <div class="hr-form-group">
                    <label class="hr-form-label">Attendance Status <span class="req">*</span></label>
                    <div class="hr-status-pills">
                        <label class="hr-status-pill-opt selected">
                            <input type="radio" name="attStatus" value="PRESENT" checked>
                            <span>🟢 Present</span>
                        </label>
                        <label class="hr-status-pill-opt">
                            <input type="radio" name="attStatus" value="LATE">
                            <span>🟡 Late</span>
                        </label>
                        <label class="hr-status-pill-opt">
                            <input type="radio" name="attStatus" value="WFH">
                            <span>💻 WFH</span>
                        </label>
                        <label class="hr-status-pill-opt">
                            <input type="radio" name="attStatus" value="HALF_DAY">
                            <span>⏱️ Half Day</span>
                        </label>
                        <label class="hr-status-pill-opt">
                            <input type="radio" name="attStatus" value="ABSENT">
                            <span>🔴 Absent</span>
                        </label>
                    </div>
                </div>

                <div class="hr-form-group">
                    <label class="hr-form-label" for="attNotesInput">Remarks / Note</label>
                    <input type="text" id="attNotesInput" class="hr-form-input" placeholder="e.g. Approved manual override / Onsite meeting">
                </div>
            </div>
            <div class="hr-modal-footer">
                <a href="hr-attendance.php" class="hr-modal-footer-link">Go to Attendance Register →</a>
                <div class="hr-modal-btn-group">
                    <button type="button" class="hr-modal-btn-cancel" onclick="closeHrQuickModal('hrQuickAttendanceModal')">Cancel</button>
                    <button type="submit" class="hr-modal-btn-primary" id="btnSubmitAttendance">
                        <span>Save Attendance</span>
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- 5. HR REPORTS EXPORT MODAL -->
<div class="hr-modal-overlay" id="hrQuickReportsModal" role="dialog" aria-modal="true" aria-labelledby="hrQuickReportsTitle">
    <div class="hr-modal-card hr-modal-wide">
        <div class="hr-modal-header">
            <div class="hr-modal-header-info">
                <div class="hr-modal-icon-badge badge-mulberry-soft">📊</div>
                <div class="hr-modal-title-text">
                    <h3 id="hrQuickReportsTitle">Generate &amp; Export HR Reports</h3>
                    <p>Instant analytics report download in CSV or PDF format</p>
                </div>
            </div>
            <button type="button" class="hr-modal-close-btn" onclick="closeHrQuickModal('hrQuickReportsModal')" aria-label="Close modal">✕</button>
        </div>
        <form id="hrQuickReportsForm" onsubmit="handleQuickReportsSubmit(event)">
            <div class="hr-modal-body">
                <div class="hr-form-group">
                    <label class="hr-form-label">Select Report Dataset <span class="req">*</span></label>
                    <div class="hr-report-type-grid">
                        <label class="hr-report-type-card selected">
                            <input type="radio" name="reportType" value="ATTENDANCE" checked>
                            <span class="hr-report-type-icon">📅</span>
                            <div class="hr-report-type-text">
                                <span class="hr-report-type-name">Attendance &amp; Bio-Logs</span>
                                <span class="hr-report-type-desc">Daily logs, punch times &amp; hours</span>
                            </div>
                        </label>
                        <label class="hr-report-type-card">
                            <input type="radio" name="reportType" value="LEAVE">
                            <span class="hr-report-type-icon">🏖️</span>
                            <div class="hr-report-type-text">
                                <span class="hr-report-type-name">Leave Ledger &amp; Balances</span>
                                <span class="hr-report-type-desc">Approved, pending &amp; quota usages</span>
                            </div>
                        </label>
                        <label class="hr-report-type-card">
                            <input type="radio" name="reportType" value="PAYROLL">
                            <span class="hr-report-type-icon">💵</span>
                            <div class="hr-report-type-text">
                                <span class="hr-report-type-name">Monthly Payroll Statement</span>
                                <span class="hr-report-type-desc">Gross, PF, TDS &amp; net payouts</span>
                            </div>
                        </label>
                        <label class="hr-report-type-card">
                            <input type="radio" name="reportType" value="HEADCOUNT">
                            <span class="hr-report-type-icon">👥</span>
                            <div class="hr-report-type-text">
                                <span class="hr-report-type-name">Employee Master Directory</span>
                                <span class="hr-report-type-desc">Department, designation &amp; status</span>
                            </div>
                        </label>
                    </div>
                </div>

                <div class="hr-form-grid-2">
                    <div class="hr-form-group">
                        <label class="hr-form-label" for="reportDateRange">Time Period</label>
                        <select id="reportDateRange" class="hr-form-select">
                            <option value="CURRENT_MONTH" selected>Current Month (Sep 2026)</option>
                            <option value="LAST_MONTH">Last Month (Aug 2026)</option>
                            <option value="Q3_2026">Q3 2026 (Jul - Sep)</option>
                            <option value="YEAR_TO_DATE">Year to Date (2026)</option>
                        </select>
                    </div>
                    <div class="hr-form-group">
                        <label class="hr-form-label" for="reportDeptFilter">Department</label>
                        <select id="reportDeptFilter" class="hr-form-select">
                            <option value="ALL" selected>All Departments</option>
                            <option value="Engineering">Engineering</option>
                            <option value="Design">Design</option>
                            <option value="Product">Product</option>
                            <option value="HR">Human Resources</option>
                            <option value="Operations">Operations</option>
                        </select>
                    </div>
                </div>

                <div class="hr-form-group">
                    <label class="hr-form-label">Download Format</label>
                    <div class="hr-status-pills">
                        <label class="hr-status-pill-opt selected">
                            <input type="radio" name="reportFormat" value="CSV" checked>
                            <span>📊 CSV Spreadsheet (.csv)</span>
                        </label>
                        <label class="hr-status-pill-opt">
                            <input type="radio" name="reportFormat" value="PDF">
                            <span>📄 PDF Document (.pdf)</span>
                        </label>
                    </div>
                </div>
            </div>
            <div class="hr-modal-footer">
                <a href="hr-reports.php" class="hr-modal-footer-link">Advanced Reporting Hub →</a>
                <div class="hr-modal-btn-group">
                    <button type="button" class="hr-modal-btn-cancel" onclick="closeHrQuickModal('hrQuickReportsModal')">Cancel</button>
                    <button type="submit" class="hr-modal-btn-primary" id="btnSubmitReport">
                        <span>📥 Download Report</span>
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

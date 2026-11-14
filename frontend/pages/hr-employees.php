<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HR Employees</title>
    <link rel="stylesheet" href="../assets/css/app-theme.css?v=1">
    <link rel="stylesheet" href="../assets/css/hr-shell.css?v=1">
    <link rel="stylesheet" href="../assets/css/hr-employees.css?v=<?= time() ?>">
    <link rel="stylesheet" href="../assets/css/employee-wizard-modal.css?v=1">
</head>
<body>
<div class="app">
    <?php include '../includes/hr-sidebar.php'; ?>
    <main class="main-content">
        <?php include '../includes/hr-topbar.php'; ?>
        <section class="content hr-employees-page">
            <div class="employee-kpis" id="employeeKpis"></div>
            <div class="employee-layout">
                <article class="hr-employee-card employee-directory-card">
                    <div class="employee-tabs">
                        <button type="button" class="active" id="tabAllEmployees">All Employees</button>
                        <button type="button" id="tabEmployeeDirectory">Employee Directory</button>
                        <div class="employee-tab-actions">
                            <div class="export-dropdown-wrapper">
                                <button type="button" id="btnExportEmployees" class="export-btn pf-btn pf-btn-light">
                                    <span>Export</span>
                                    <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"/></svg>
                                </button>
                                <div id="exportDropdownMenu" class="export-dropdown-menu" style="display: none;">
                                    <button type="button" id="btnExportCsv">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
                                        Export as CSV
                                    </button>
                                    <button type="button" id="btnExportPrint">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect x="6" y="14" width="12" height="8"/></svg>
                                        Print Directory
                                    </button>
                                </div>
                            </div>
                            <a class="hr-primary-action add-employee-trigger pf-btn pf-btn-primary" id="openAddEmployeeBtn2" href="#add">
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                                <span>Add Employee</span>
                            </a>
                        </div>
                    </div>
                    <div class="employee-toolbar">
                        <div class="employee-search">⌕<input id="employeeSearch" type="search" placeholder="Search by name, ID, email, phone..."></div>
                        <select id="employeeDepartment"><option value="">All Departments</option></select>
                        <select id="employeeDesignation"><option value="">All Designations</option></select>
                        <select id="employeeStatus">
                            <option value="">All Status</option>
                            <option value="ACTIVE">Active</option>
                            <option value="INACTIVE">Inactive</option>
                            <option value="ON_LEAVE">On leave</option>
                            <option value="SUSPENDED">Suspended</option>
                        </select>
                        <span id="employeeCount"></span>
                    </div>
                    <div id="employeeState" class="employee-state">Loading employees...</div>
                    <div class="employee-table-wrap" id="employeeTableWrap">
                        <table>
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>ID</th>
                                    <th>Photo</th>
                                    <th>Name</th>
                                    <th>Department</th>
                                    <th>Designation</th>
                                    <th>Email</th>
                                    <th>Phone</th>
                                    <th>Joining Date</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="employeeRows"></tbody>
                        </table>
                    </div>
                    <div class="employee-footer">
                        <span id="employeePageInfo"></span>
                        <div id="employeePagination"></div>
                        <label>Rows per page 
                            <select id="employeePerPage">
                                <option>10</option>
                                <option>25</option>
                                <option>50</option>
                            </select>
                        </label>
                    </div>
                </article>
                <aside class="employee-insights">
                    <article class="insight-card">
                        <h2>Employees by Department</h2>
                        <div id="departmentChart" class="horizontal-chart"></div>
                    </article>
                    <article class="insight-card">
                        <h2>Employees by Employment Type</h2>
                        <div id="employmentChart" class="horizontal-chart"></div>
                    </article>
                    <article class="insight-card">
                        <div class="insight-heading">
                            <h2>Recent Joiners</h2>
                        </div>
                        <div id="recentJoiners" class="recent-joiners"></div>
                    </article>
                </aside>
            </div>
        </section>
    </main>
</div>

<!-- ADD EMPLOYEE WIZARD MODAL -->
<?php include '../includes/add-employee-modal.php'; ?>

<!-- EMPLOYEE DETAIL QUICK VIEW MODAL -->
<div id="hrEmployeeDetailModal" class="hr-modal-overlay" style="display: none;" aria-hidden="true" role="dialog">
    <div class="hr-modal-card">
        <div class="hr-modal-header">
            <div class="hr-modal-title-box">
                <h3 id="modalEmpTitle">Employee Profile</h3>
                <span id="modalEmpCode" class="employee-code"></span>
            </div>
            <button type="button" class="hr-modal-close" id="closeDetailModalBtn" aria-label="Close modal">×</button>
        </div>
        <div class="hr-modal-body" id="modalEmpBody">
            <div class="hr-modal-profile-hero">
                <div class="hr-modal-hero-avatar" id="modalHeroAvatar">?</div>
                <div class="hr-modal-hero-info">
                    <h4 id="modalHeroName">—</h4>
                    <p id="modalHeroDesignation">—</p>
                    <span id="modalHeroStatus" class="employee-status active"><i class="status-dot"></i>ACTIVE</span>
                </div>
            </div>
            <div class="hr-modal-info-grid">
                <div class="hr-modal-field">
                    <label>Department</label>
                    <span id="modalEmpDepartment">—</span>
                </div>
                <div class="hr-modal-field">
                    <label>Employment Type</label>
                    <span id="modalEmpType">—</span>
                </div>
                <div class="hr-modal-field">
                    <label>Email Address</label>
                    <span id="modalEmpEmail">—</span>
                </div>
                <div class="hr-modal-field">
                    <label>Phone Number</label>
                    <span id="modalEmpPhone">—</span>
                </div>
                <div class="hr-modal-field">
                    <label>Joining Date</label>
                    <span id="modalEmpJoiningDate">—</span>
                </div>
                <div class="hr-modal-field">
                    <label>System ID</label>
                    <span id="modalEmpId">—</span>
                </div>
            </div>
            <div class="hr-modal-qr-box">
                <img id="modalEmpQr" src="" alt="Employee QR Code" class="hr-modal-qr-img" onerror="this.style.display='none'">
                <div class="hr-modal-qr-desc">
                    <h5>Employee QR Badge</h5>
                    <p>Official QR Code assigned for daily clock-in / clock-out.</p>
                    <a id="modalEmpQrDownload" href="#" download="qr-code.png" class="hr-modal-qr-btn">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                        Download QR
                    </a>
                </div>
            </div>
        </div>
        <div class="hr-modal-footer">
            <button type="button" class="employee-footer-btn" id="modalCloseActionBtn">Close</button>
            <a id="modalSendEmailBtn" href="#" class="hr-primary-action" style="text-decoration:none;">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg>
                Send Email
            </a>
        </div>
    </div>
</div>

<script src="../assets/js/hr-employees.js?v=<?= time() ?>"></script>
<script src="../assets/js/employee-wizard-modal.js?v=3"></script>
</body>
</html>

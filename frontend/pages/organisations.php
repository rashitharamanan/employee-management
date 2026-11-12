<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Admin Portal - Organisation Management. Manage organisations, branches, and departments.">
    <title>Organisation Management – Admin Portal</title>
    <link rel="stylesheet" href="../assets/css/app-theme.css?v=<?= time() ?>">
    <link rel="stylesheet" href="../assets/css/sidebar.css?v=<?= time() ?>">
    <link rel="stylesheet" href="../assets/css/admin.css?v=<?= time() ?>">
    <link rel="stylesheet" href="../assets/css/topbar.css?v=<?= time() ?>">
</head>

<body>

<div class="app">

    <?php include '../includes/sidebar.php'; ?>

    <!-- ===================================================
         MAIN
    ==================================================== -->
    <main class="main-content">

        <?php include '../includes/topbar.php'; ?>


        <!-- CONTENT -->
        <section class="content">



            <!-- STATS GRID -->
            <div class="stats-grid">

                <div class="stat-card">
                    <div class="stat-icon teal">
                        <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
                            <polyline points="9 22 9 12 15 12 15 22"/>
                        </svg>
                    </div>
                    <div class="stat-info">
                        <span class="stat-label">Total Organisations</span>
                        <strong class="stat-number" id="statOrgs">–</strong>
                        <span class="stat-subtitle">All organisations</span>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon green">
                        <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/>
                            <circle cx="12" cy="10" r="3"/>
                        </svg>
                    </div>
                    <div class="stat-info">
                        <span class="stat-label">Total Branches</span>
                        <strong class="stat-number" id="statBranches">–</strong>
                        <span class="stat-subtitle">Across all organisations</span>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon blue">
                        <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/>
                            <path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                        </svg>
                    </div>
                    <div class="stat-info">
                        <span class="stat-label">Total Departments</span>
                        <strong class="stat-number" id="statDepts">–</strong>
                        <span class="stat-subtitle">Across all organisations</span>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon orange">
                        <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/>
                        </svg>
                    </div>
                    <div class="stat-info">
                        <span class="stat-label">Total Users</span>
                        <strong class="stat-number" id="statUsers">–</strong>
                        <span class="stat-subtitle">Across all organisations</span>
                    </div>
                </div>

            </div>


            <!-- ===================================================
                 QUICK ACTIONS (GLASS-LIKE BUTTONS ABOVE TABLE)
            ==================================================== -->
            <div class="org-quick-actions-glass">
                <div class="glass-qa-header">
                    <div class="glass-qa-badge">
                        Quick Actions
                    </div>
                </div>
                <div class="glass-qa-grid">
                    <button type="button" class="glass-qa-btn" id="qaDept" onclick="openDeptCrudModal()">
                        <div class="glass-qa-icon icon-blue">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="2" y="7" width="20" height="14" rx="2" ry="2"/>
                                <path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/>
                            </svg>
                        </div>
                        <div class="glass-qa-content">
                            <span class="glass-qa-title">Department</span>
                        </div>
                    </button>

                    <button type="button" class="glass-qa-btn" id="qaDesig" onclick="openDesigCrudModal()">
                        <div class="glass-qa-icon icon-teal">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                                <circle cx="8.5" cy="7" r="4"/>
                                <line x1="20" y1="8" x2="20" y2="14"/>
                                <line x1="23" y1="11" x2="17" y2="11"/>
                            </svg>
                        </div>
                        <div class="glass-qa-content">
                            <span class="glass-qa-title">Designation</span>
                        </div>
                    </button>

                    <button type="button" class="glass-qa-btn" id="qaRole" onclick="openRoleCrudModal()">
                        <div class="glass-qa-icon icon-purple">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                                <path d="M9 12l2 2 4-4"/>
                            </svg>
                        </div>
                        <div class="glass-qa-content">
                            <span class="glass-qa-title">Role</span>
                        </div>
                    </button>

                    <button type="button" class="glass-qa-btn" id="qaAddBranch" onclick="openAddBranchModal()">
                        <div class="glass-qa-icon icon-orange">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/>
                                <circle cx="12" cy="10" r="3"/>
                            </svg>
                        </div>
                        <div class="glass-qa-content">
                            <span class="glass-qa-title">Branch</span>
                        </div>
                    </button>

                    <button type="button" class="glass-qa-btn" id="qaExportReports" onclick="exportOrgReports()">
                        <div class="glass-qa-icon icon-indigo">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                                <polyline points="14 2 14 8 20 8"/>
                                <line x1="16" y1="13" x2="8" y2="13"/>
                                <line x1="16" y1="17" x2="8" y2="17"/>
                            </svg>
                        </div>
                        <div class="glass-qa-content">
                            <span class="glass-qa-title">Reports</span>
                        </div>
                    </button>
                </div>
            </div>


            <!-- ORGANISATIONS TABLE CARD -->
            <div class="table-card">
                <div class="table-card-header">
                    <div class="table-card-title">
                        <div class="title-icon">
                            <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
                                <polyline points="9 22 9 12 15 12 15 22"/>
                            </svg>
                        </div>
                        <h2>Organisations</h2>
                    </div>

                    <div class="table-filters">
                        <div class="search-wrapper">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                            </svg>
                            <input type="text" id="orgSearch" class="filter-input" placeholder="Search organisation...">
                        </div>

                        <select id="orgStatusFilter" class="filter-input">
                            <option value="">All Status</option>
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>

                        <select id="orgTypeFilter" class="filter-input">
                            <option value="">All Organisation Type</option>
                            <option value="company">Company</option>
                            <option value="private limited">Private Limited</option>
                            <option value="llp">LLP</option>
                            <option value="partnership">Partnership</option>
                        </select>

                        <select id="orgSortBy" class="filter-input">
                            <option value="">Sort By</option>
                            <option value="name_asc">Name A–Z</option>
                            <option value="name_desc">Name Z–A</option>
                            <option value="created_desc">Newest First</option>
                            <option value="created_asc">Oldest First</option>
                        </select>

                        <button class="btn-primary" id="addOrgBtn">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
                            </svg>
                            Add Organisation
                        </button>
                    </div>
                </div>

                <div class="table-responsive">
                    <table id="orgsTable">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Organisation Name</th>
                                <th>Code</th>
                                <th>Type</th>
                                <th>Branches</th>
                                <th>Departments</th>
                                <th>Users</th>
                                <th>Status</th>
                                <th>Created Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="orgsTableBody">
                            <tr>
                                <td colspan="10" class="loading-cell">
                                    <div class="spinner"></div>
                                    Loading organisations...
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="table-footer">
                    <span id="orgTableInfo">Showing 0 organisations</span>
                    <div class="pagination" id="orgPagination"></div>
                </div>
            </div>

        </section>

    </main>

</div>


<!-- =========================================================
     ADD / EDIT ORGANISATION MODAL
========================================================= -->
<div class="modal-overlay" id="orgModal">
    <div class="modal-box">

        <div class="modal-header">
            <div>
                <h3 id="orgModalTitle">Add Organisation</h3>
                <p id="orgModalSubtitle">Fill in the details to create a new organisation</p>
            </div>
            <button class="modal-close" id="closeOrgModal">×</button>
        </div>

        <form id="orgForm">
            <div class="modal-body">
                <div class="form-grid">

                    <div class="form-group">
                        <label for="oName">Organisation Name</label>
                        <input type="text" id="oName" placeholder="Enter organisation name" required>
                    </div>

                    <div class="form-group">
                        <label for="oCode">Organisation Code</label>
                        <input type="text" id="oCode" placeholder="e.g. ABC001" required>
                    </div>

                    <div class="form-group">
                        <label for="oType">Organisation Type</label>
                        <select id="oType" required>
                            <option value="">Select type</option>
                            <option value="Company">Company</option>
                            <option value="Private Limited">Private Limited</option>
                            <option value="LLP">LLP</option>
                            <option value="Partnership">Partnership</option>
                            <option value="Sole Proprietorship">Sole Proprietorship</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="oStatus">Status</label>
                        <select id="oStatus" required>
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>

                    <div class="form-group full">
                        <label for="oEmail">Contact Email</label>
                        <input type="email" id="oEmail" placeholder="Enter contact email">
                    </div>

                    <div class="form-group">
                        <label for="oPhone">Phone</label>
                        <input type="text" id="oPhone" placeholder="Enter phone number">
                    </div>

                    <div class="form-group">
                        <label for="oWebsite">Website</label>
                        <input type="text" id="oWebsite" placeholder="https://...">
                    </div>

                    <div class="form-group full">
                        <label for="oAddress">Address</label>
                        <textarea id="oAddress" rows="3" placeholder="Enter full address"></textarea>
                    </div>

                </div>

                <div class="form-error" id="orgFormError"></div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn-cancel" id="cancelOrgModal">Cancel</button>
                <button type="submit" class="btn-primary" id="saveOrgBtn">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="20 6 9 17 4 12"/>
                    </svg>
                    Save Organisation
                </button>
            </div>
        </form>

    </div>
</div>


<!-- =========================================================
     VIEW ORG MODAL
========================================================= -->
<div class="modal-overlay" id="viewOrgModal">
    <div class="modal-box">
        <div class="modal-header">
            <div>
                <h3>Organisation Details</h3>
                <p>View full organisation information</p>
            </div>
            <button class="modal-close" id="closeViewOrgModal">×</button>
        </div>
        <div class="modal-body" id="viewOrgBody">
            <!-- Filled dynamically -->
        </div>
        <div class="modal-footer">
            <button type="button" class="btn-cancel" id="cancelViewOrgModal">Close</button>
        </div>
    </div>
</div>


<!-- =========================================================
     ADD DEPARTMENT MODAL
========================================================= -->
<!-- =========================================================
     DEPARTMENT CRUD MODAL (LIST, CREATE, EDIT, DELETE)
========================================================= -->
<div class="modal-overlay" id="deptModal" onclick="if(event.target===this)closeModal('deptModal')">
    <div class="modal-box modal-box-lg">
        <div class="modal-header">
            <div>
                <h3 id="deptModalHeaderTitle">Department Management</h3>
                <p id="deptModalHeaderSub">View, create, edit, and manage departments</p>
            </div>
            <button class="modal-close" id="closeDeptModal" onclick="closeModal('deptModal')">×</button>
        </div>
        
        <!-- PANEL 1: LIST VIEW (READ + ACTION BUTTONS) -->
        <div class="modal-body modal-view-panel active" id="deptListView">
            <div class="modal-toolbar">
                <div class="modal-toolbar-search">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                    </svg>
                    <input type="text" id="deptSearchInput" placeholder="Search departments...">
                </div>
                <div class="modal-toolbar-filters">
                    <select id="deptStatusFilter" class="modal-filter-select">
                        <option value="">All Status</option>
                        <option value="ACTIVE">Active</option>
                        <option value="INACTIVE">Inactive</option>
                    </select>
                    <select id="deptOrgFilter" class="modal-filter-select">
                        <option value="">All Organisations</option>
                    </select>
                    <button type="button" class="btn-modal-add" id="btnShowAddDept" onclick="showDeptForm()">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
                        </svg>
                        Add Department
                    </button>
                </div>
            </div>

            <div class="modal-table-wrap">
                <table class="modal-table">
                    <thead>
                        <tr>
                            <th style="width: 45px;">#</th>
                            <th>Department Name</th>
                            <th>Organisation</th>
                            <th>Status</th>
                            <th style="text-align: right; width: 90px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="deptTableBody">
                        <tr>
                            <td colspan="5" class="modal-empty-state">Loading departments...</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- PANEL 2: FORM VIEW (CREATE & EDIT) -->
        <div class="modal-body modal-view-panel" id="deptFormView">
            <div class="modal-form-card">
                <div class="modal-form-header">
                    <h4 id="deptFormTitle">Add New Department</h4>
                    <button type="button" class="btn-cancel" onclick="showDeptList()" style="padding: 5px 12px; font-size: 12px;">← Back to List</button>
                </div>
                <form id="deptForm">
                    <input type="hidden" id="deptEditId" value="">
                    <div class="form-grid">
                        <div class="form-group" style="grid-column: 1 / -1;">
                            <label for="deptName">Department Name *</label>
                            <input type="text" id="deptName" placeholder="e.g. Engineering, Sales, Human Resources" required>
                        </div>
                        <div class="form-group">
                            <label for="deptCompanySelect">Organisation *</label>
                            <select id="deptCompanySelect" required>
                                <!-- Populated dynamically -->
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="deptStatus">Status</label>
                            <select id="deptStatus">
                                <option value="ACTIVE">Active</option>
                                <option value="INACTIVE">Inactive</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-error" id="deptFormError"></div>
                    <div class="modal-footer" style="padding: 16px 0 0; margin-top: 16px; border-top: 1px solid #e2e8f0; display: flex; justify-content: flex-end; gap: 10px;">
                        <button type="button" class="btn-cancel" onclick="showDeptList()">Cancel</button>
                        <button type="submit" class="btn-primary" id="saveDeptBtn">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                            <span id="saveDeptBtnText">Save Department</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>


<!-- =========================================================
     DESIGNATION CRUD MODAL (LIST, CREATE, EDIT, DELETE)
========================================================= -->
<div class="modal-overlay" id="desigModal" onclick="if(event.target===this)closeModal('desigModal')">
    <div class="modal-box modal-box-lg">
        <div class="modal-header">
            <div>
                <h3 id="desigModalHeaderTitle">Designation Management</h3>
                <p id="desigModalHeaderSub">View, create, edit, and manage job designations</p>
            </div>
            <button class="modal-close" id="closeDesigModal" onclick="closeModal('desigModal')">×</button>
        </div>

        <!-- PANEL 1: LIST VIEW (READ + ACTION BUTTONS) -->
        <div class="modal-body modal-view-panel active" id="desigListView">
            <div class="modal-toolbar">
                <div class="modal-toolbar-search">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                    </svg>
                    <input type="text" id="desigSearchInput" placeholder="Search designations...">
                </div>
                <div class="modal-toolbar-filters">
                    <select id="desigDeptFilter" class="modal-filter-select">
                        <option value="">All Departments</option>
                    </select>
                    <select id="desigStatusFilter" class="modal-filter-select">
                        <option value="">All Status</option>
                        <option value="ACTIVE">Active</option>
                        <option value="INACTIVE">Inactive</option>
                    </select>
                    <button type="button" class="btn-modal-add" id="btnShowAddDesig" onclick="showDesigForm()">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
                        </svg>
                        Add Designation
                    </button>
                </div>
            </div>

            <div class="modal-table-wrap">
                <table class="modal-table">
                    <thead>
                        <tr>
                            <th style="width: 45px;">#</th>
                            <th>Designation Title</th>
                            <th>Department</th>
                            <th>Status</th>
                            <th style="text-align: right; width: 90px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="desigTableBody">
                        <tr>
                            <td colspan="5" class="modal-empty-state">Loading designations...</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- PANEL 2: FORM VIEW (CREATE & EDIT) -->
        <div class="modal-body modal-view-panel" id="desigFormView">
            <div class="modal-form-card">
                <div class="modal-form-header">
                    <h4 id="desigFormTitle">Add New Designation</h4>
                    <button type="button" class="btn-cancel" onclick="showDesigList()" style="padding: 5px 12px; font-size: 12px;">← Back to List</button>
                </div>
                <form id="desigForm">
                    <input type="hidden" id="desigEditId" value="">
                    <div class="form-grid">
                        <div class="form-group" style="grid-column: 1 / -1;">
                            <label for="desigDeptSelect">Department *</label>
                            <select id="desigDeptSelect" required>
                                <option value="">-- Choose Department --</option>
                            </select>
                        </div>
                        <div class="form-group" style="grid-column: 1 / -1;">
                            <label for="desigTitle">Designation Title *</label>
                            <input type="text" id="desigTitle" placeholder="e.g. Senior Software Engineer, HR Specialist" required>
                        </div>
                        <div class="form-group" style="grid-column: 1 / -1;">
                            <label for="desigStatus">Status</label>
                            <select id="desigStatus">
                                <option value="ACTIVE">Active</option>
                                <option value="INACTIVE">Inactive</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-error" id="desigFormError"></div>
                    <div class="modal-footer" style="padding: 16px 0 0; margin-top: 16px; border-top: 1px solid #e2e8f0; display: flex; justify-content: flex-end; gap: 10px;">
                        <button type="button" class="btn-cancel" onclick="showDesigList()">Cancel</button>
                        <button type="submit" class="btn-primary" id="saveDesigBtn">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                            <span id="saveDesigBtnText">Save Designation</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>


<!-- =========================================================
     ADD BRANCH MODAL
========================================================= -->
<div class="modal-overlay" id="branchModal" onclick="if(event.target===this)closeModal('branchModal')">
    <div class="modal-box">
        <div class="modal-header">
            <div>
                <h3>Add Branch</h3>
                <p>Add a new company branch or office location</p>
            </div>
            <button class="modal-close" id="closeBranchModal" onclick="closeModal('branchModal')">×</button>
        </div>
        <form id="branchForm">
            <div class="modal-body">
                <div class="form-grid">
                    <div class="form-group" style="grid-column: 1 / -1;">
                        <label for="branchName">Branch Name *</label>
                        <input type="text" id="branchName" placeholder="e.g. Regional Tech Park / Downtown Branch" required>
                    </div>
                    <div class="form-group">
                        <label for="branchCompanySelect">Organisation *</label>
                        <select id="branchCompanySelect" required>
                            <!-- Populated dynamically -->
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="branchLocation">City / Location</label>
                        <input type="text" id="branchLocation" placeholder="e.g. Bangalore, Karnataka">
                    </div>
                    <div class="form-group">
                        <label for="branchPhone">Contact Phone</label>
                        <input type="text" id="branchPhone" placeholder="e.g. +91 98765 43210">
                    </div>
                    <div class="form-group">
                        <label for="branchStatus">Status</label>
                        <select id="branchStatus">
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                </div>
                <div class="form-error" id="branchFormError"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-cancel" id="cancelBranchModal" onclick="closeModal('branchModal')">Cancel</button>
                <button type="submit" class="btn-primary" id="saveBranchBtn">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                    Save Branch
                </button>
            </div>
        </form>
    </div>
</div>


<!-- =========================================================
     ROLE CRUD MODAL (LIST, CREATE, EDIT, DELETE)
========================================================= -->
<div class="modal-overlay" id="roleModal" onclick="if(event.target===this)closeModal('roleModal')">
    <div class="modal-box modal-box-lg">
        <div class="modal-header">
            <div>
                <h3 id="roleModalHeaderTitle">Role Management</h3>
                <p id="roleModalHeaderSub">View, create, edit, and manage access control roles</p>
            </div>
            <button class="modal-close" id="closeRoleModal" onclick="closeModal('roleModal')">×</button>
        </div>

        <!-- PANEL 1: LIST VIEW (READ + ACTION BUTTONS) -->
        <div class="modal-body modal-view-panel active" id="roleListView">
            <div class="modal-toolbar">
                <div class="modal-toolbar-search">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                    </svg>
                    <input type="text" id="roleSearchInput" placeholder="Search roles by name or description...">
                </div>
                <div class="modal-toolbar-filters">
                    <button type="button" class="btn-modal-add" id="btnShowAddRole" onclick="showRoleForm()">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
                        </svg>
                        Add Role
                    </button>
                </div>
            </div>

            <div class="modal-table-wrap">
                <table class="modal-table">
                    <thead>
                        <tr>
                            <th style="width: 45px;">#</th>
                            <th>Role Name</th>
                            <th>Description</th>
                            <th>Users</th>
                            <th style="text-align: right; width: 90px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="roleTableBody">
                        <tr>
                            <td colspan="5" class="modal-empty-state">Loading roles...</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- PANEL 2: FORM VIEW (CREATE & EDIT) -->
        <div class="modal-body modal-view-panel" id="roleFormView">
            <div class="modal-form-card">
                <div class="modal-form-header">
                    <h4 id="roleFormTitle">Add New Role</h4>
                    <button type="button" class="btn-cancel" onclick="showRoleList()" style="padding: 5px 12px; font-size: 12px;">← Back to List</button>
                </div>
                <form id="roleForm">
                    <input type="hidden" id="roleEditId" value="">
                    <div class="form-grid">
                        <div class="form-group" style="grid-column: 1 / -1;">
                            <label for="roleName">Role Name *</label>
                            <input type="text" id="roleName" placeholder="e.g. SUPERVISOR, TEAM_LEAD, AUDITOR" required>
                        </div>
                        <div class="form-group" style="grid-column: 1 / -1;">
                            <label for="roleDescription">Description</label>
                            <input type="text" id="roleDescription" placeholder="e.g. Department lead with approval privileges">
                        </div>
                    </div>
                    <div class="form-error" id="roleFormError"></div>
                    <div class="modal-footer" style="padding: 16px 0 0; margin-top: 16px; border-top: 1px solid #e2e8f0; display: flex; justify-content: flex-end; gap: 10px;">
                        <button type="button" class="btn-cancel" onclick="showRoleList()">Cancel</button>
                        <button type="submit" class="btn-primary" id="saveRoleBtn">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                            <span id="saveRoleBtnText">Save Role</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>


<!-- TOAST -->
<div class="toast-container" id="toastContainer"></div>


<script src="../assets/js/theme.js?v=<?= time() ?>"></script>
<script src="../assets/js/organisations.js?v=<?= time() ?>"></script>

</body>
</html>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Admin Portal - User Management. Manage system users, roles and access.">
    <title>User Management – Admin Portal</title>
    <link rel="stylesheet" href="../assets/css/app-theme.css?v=<?= time() ?>">
    <link rel="stylesheet" href="../assets/css/sidebar.css?v=<?= time() ?>">
    <link rel="stylesheet" href="../assets/css/admin.css?v=<?= time() ?>">
    <link rel="stylesheet" href="../assets/css/employee-wizard-modal.css?v=<?= time() ?>">
    <link rel="stylesheet" href="../assets/css/users.css?v=<?= time() ?>">
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
                    <div class="stat-icon blue">
                        <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/>
                            <path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                        </svg>
                    </div>
                    <div class="stat-info">
                        <span class="stat-label">Total Users</span>
                        <strong class="stat-number" id="statTotal">–</strong>
                        <span class="stat-subtitle">All system users</span>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon green">
                        <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/>
                            <polyline points="16 11 18 13 22 9"/>
                        </svg>
                    </div>
                    <div class="stat-info">
                        <span class="stat-label">Active Users</span>
                        <strong class="stat-number" id="statActive">–</strong>
                        <span class="stat-subtitle">Active accounts</span>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon orange">
                        <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/>
                            <line x1="18" y1="8" x2="23" y2="13"/><line x1="23" y1="8" x2="18" y2="13"/>
                        </svg>
                    </div>
                    <div class="stat-info">
                        <span class="stat-label">Inactive Users</span>
                        <strong class="stat-number" id="statInactive">–</strong>
                        <span class="stat-subtitle">Inactive accounts</span>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon red">
                        <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                            <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                        </svg>
                    </div>
                    <div class="stat-info">
                        <span class="stat-label">Locked Users</span>
                        <strong class="stat-number" id="statLocked">–</strong>
                        <span class="stat-subtitle">Locked accounts</span>
                    </div>
                </div>

            </div>

            <!-- MAIN LAYOUT: TABLE + QUICK ACTIONS SIDEBAR -->
            <div class="users-main-layout">

                <!-- USERS TABLE CARD -->
                <div class="table-card">
                    <div class="table-card-header">
                        <div class="table-card-title">
                            <div class="title-icon">
                                <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/>
                                    <path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                                </svg>
                            </div>
                            <h2>All Users</h2>
                        </div>

                        <div class="table-filters">
                            <div class="search-wrapper">
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                                </svg>
                                <input type="text" id="userSearch" class="filter-input" placeholder="Search by name, email or username...">
                            </div>

                            <select id="roleFilter" class="filter-input">
                                <option value="">All Roles</option>
                                <option value="admin">Admin</option>
                                <option value="hr">HR</option>
                                <option value="manager">Manager</option>
                                <option value="employee">Employee</option>
                            </select>

                            <select id="statusFilter" class="filter-input">
                                <option value="">All Status</option>
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                                <option value="locked">Locked</option>
                            </select>

                            <button class="filter-btn" id="filterBtn">
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"/>
                                </svg>
                                Filter
                            </button>

                            <button class="btn-primary" id="addUserBtn">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                    <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
                                </svg>
                                Add User
                            </button>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table id="usersTable">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>User ID</th>
                                    <th>Username</th>
                                    <th>Full Name</th>
                                    <th>Email</th>
                                    <th>Role</th>
                                    <th>Organisation</th>
                                    <th>Status</th>
                                    <th>Last Login</th>
                                    <th>Created Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="usersTableBody">
                                <tr>
                                    <td colspan="11" class="loading-cell">
                                        <div class="spinner"></div>
                                        Loading users...
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="table-footer">
                        <span id="tableInfo">Showing 0 users</span>
                        <div class="pagination" id="pagination"></div>
                    </div>
                </div>

                <!-- QUICK ACTIONS SIDEBAR -->
                <div class="quick-actions-sidebar">
                    <div class="qa-sidebar-header">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="color:#4f46e5">
                            <polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/>
                        </svg>
                        <h3>Quick Actions</h3>
                    </div>
                    <div class="qa-sidebar-list">
                        <a href="#" class="qa-sidebar-item" id="qaResetPwd" title="Reset Password">
                            <div class="qa-icon icon-orange">
                                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#ffffff" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round">
                                    <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                                    <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                                </svg>
                            </div>
                            <span class="qa-label">Reset Password</span>
                        </a>
                        <a href="#" class="qa-sidebar-item" id="qaLockUser" title="Lock User">
                            <div class="qa-icon icon-pink">
                                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#ffffff" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round">
                                    <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                                    <path d="M7 11V7a5 5 0 0 1 9.9-1"/>
                                </svg>
                            </div>
                            <span class="qa-label">Lock User</span>
                        </a>
                        <a href="#" class="qa-sidebar-item" id="qaDeactivate" title="Deactivate User">
                            <div class="qa-icon icon-teal">
                                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#ffffff" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="8.5" cy="7" r="4"/>
                                    <line x1="18" y1="8" x2="23" y2="13"/><line x1="23" y1="8" x2="18" y2="13"/>
                                </svg>
                            </div>
                            <span class="qa-label">Deactivate</span>
                        </a>
                        <a href="#" class="qa-sidebar-item" id="qaReport" title="Export Users Report">
                            <div class="qa-icon icon-purple">
                                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#ffffff" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                                    <polyline points="14 2 14 8 20 8"/>
                                    <line x1="16" y1="13" x2="8" y2="13"/>
                                    <line x1="16" y1="17" x2="8" y2="17"/>
                                </svg>
                            </div>
                            <span class="qa-label">Report</span>
                        </a>
                    </div>
                </div>

            </div><!-- end .users-main-layout -->

        </section>

    </main>

</div>


<!-- =========================================================
     ADD / EDIT USER WIZARD MODAL (COMPREHENSIVE HR-STYLE)
========================================================= -->
<div class="modal-overlay" id="userModal" role="dialog" aria-modal="true" aria-labelledby="modalTitle">
    <div class="modal-box wizard-modal" style="max-width: 780px;">

        <!-- MODAL HEADER -->
        <div class="modal-header">
            <div class="modal-header-left">
                <h3 id="modalTitle">Add User</h3>
                <p id="modalSubtitle" class="modal-subtitle">Fill in comprehensive details to create a new user profile &amp; system account</p>
            </div>
            <button type="button" class="close-button" id="closeUserModal" title="Close modal">&times;</button>
        </div>

        <!-- STEP INDICATOR -->
        <div class="wizard-steps" id="wizardSteps">
            <div class="step-item active" data-step="1" onclick="jumpToWizardStep(1)">
                <div class="step-circle">
                    <span class="step-num">1</span>
                    <svg class="step-check" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3">
                        <polyline points="20 6 9 17 4 12"></polyline>
                    </svg>
                </div>
                <span class="step-label">Personal</span>
            </div>

            <div class="step-line" id="stepLine1"></div>

            <div class="step-item" data-step="2" onclick="jumpToWizardStep(2)">
                <div class="step-circle">
                    <span class="step-num">2</span>
                    <svg class="step-check" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3">
                        <polyline points="20 6 9 17 4 12"></polyline>
                    </svg>
                </div>
                <span class="step-label">Employment</span>
            </div>

            <div class="step-line" id="stepLine2"></div>

            <div class="step-item" data-step="3" onclick="jumpToWizardStep(3)">
                <div class="step-circle">
                    <span class="step-num">3</span>
                    <svg class="step-check" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3">
                        <polyline points="20 6 9 17 4 12"></polyline>
                    </svg>
                </div>
                <span class="step-label">Account</span>
            </div>

            <div class="step-line" id="stepLine3"></div>

            <div class="step-item" data-step="4" onclick="jumpToWizardStep(4)">
                <div class="step-circle">
                    <span class="step-num">4</span>
                    <svg class="step-check" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3">
                        <polyline points="20 6 9 17 4 12"></polyline>
                    </svg>
                </div>
                <span class="step-label">Review</span>
            </div>
        </div>

        <!-- FORM -->
        <form id="userForm" novalidate>

            <!-- ============================================
                 STEP 1: PERSONAL INFORMATION
            ============================================ -->
            <div class="wizard-step-panel active" id="stepPanel1">
                <div class="step-section-title">
                    <div class="step-section-icon">👤</div>
                    <div>
                        <h3>Personal Information</h3>
                        <p>Basic personal details and contact information</p>
                    </div>
                </div>

                <div class="form-grid">
                    <div class="form-group">
                        <label for="uFullName">Full Name <span class="required">*</span></label>
                        <input type="text" id="uFullName" placeholder="e.g. John Doe" autocomplete="off" required>
                    </div>

                    <div class="form-group">
                        <label for="uDob">Date of Birth</label>
                        <input type="date" id="uDob">
                    </div>

                    <div class="form-group">
                        <label for="uGender">Gender</label>
                        <select id="uGender">
                            <option value="">Select gender</option>
                            <option value="MALE">Male</option>
                            <option value="FEMALE">Female</option>
                            <option value="OTHER">Other</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="uMaritalStatus">Marital Status</label>
                        <select id="uMaritalStatus">
                            <option value="">Select status</option>
                            <option value="Single">Single</option>
                            <option value="Married">Married</option>
                            <option value="Divorced">Divorced</option>
                            <option value="Widowed">Widowed</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="uPhone">Phone Number</label>
                        <input type="tel" id="uPhone" placeholder="e.g. +91 98765 43210">
                    </div>

                    <div class="form-group">
                        <label for="uPersonalEmail">Personal Email</label>
                        <input type="email" id="uPersonalEmail" placeholder="personal@gmail.com">
                    </div>

                    <div class="form-group">
                        <label for="uNationality">Nationality</label>
                        <input type="text" id="uNationality" placeholder="e.g. Indian">
                    </div>

                    <div class="form-group full-width">
                        <label for="uAddress">Residential Address</label>
                        <textarea id="uAddress" rows="2" placeholder="Street address, building, apartment..."></textarea>
                    </div>

                    <div class="form-group">
                        <label for="uCity">City</label>
                        <input type="text" id="uCity" placeholder="e.g. Mumbai">
                    </div>

                    <div class="form-group">
                        <label for="uState">State</label>
                        <input type="text" id="uState" placeholder="e.g. Maharashtra">
                    </div>

                    <div class="form-group">
                        <label for="uPincode">Pincode</label>
                        <input type="text" id="uPincode" placeholder="6-digit pincode" maxlength="6">
                    </div>
                </div>
            </div>

            <!-- ============================================
                 STEP 2: EMPLOYMENT & ORGANISATION
            ============================================ -->
            <div class="wizard-step-panel" id="stepPanel2">
                <div class="step-section-title">
                    <div class="step-section-icon">💼</div>
                    <div>
                        <h3>Employment &amp; Organisation</h3>
                        <p>Department, job designation and organizational role</p>
                    </div>
                </div>

                <div class="form-grid">
                    <div class="form-group">
                        <label for="uEmpCode">Employee / User Code</label>
                        <div class="input-with-badge">
                            <input type="text" id="uEmpCode" placeholder="Auto-generated" readonly>
                            <span class="auto-badge">Auto</span>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="uJoiningDate">Joining Date</label>
                        <input type="date" id="uJoiningDate">
                    </div>

                    <div class="form-group">
                        <label for="uDept">Department <span class="required">*</span></label>
                        <select id="uDept" required>
                            <option value="">Select department</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="uDesignation">Designation <span class="required">*</span></label>
                        <select id="uDesignation" required>
                            <option value="">Select designation</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="uOrg">Organisation</label>
                        <select id="uOrg">
                            <option value="">Select organisation</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="uEmpType">Employment Type</label>
                        <select id="uEmpType">
                            <option value="Full Time">Full Time</option>
                            <option value="Part Time">Part Time</option>
                            <option value="Contract">Contract</option>
                            <option value="Intern">Intern</option>
                            <option value="Freelance">Freelance</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="uWorkLocation">Work Location</label>
                        <input type="text" id="uWorkLocation" placeholder="e.g. Head Office, Remote">
                    </div>

                    <div class="form-group">
                        <label for="uWorkShift">Work Shift</label>
                        <input type="text" id="uWorkShift" placeholder="e.g. General, Morning (9 AM - 6 PM)">
                    </div>

                    <div class="form-group">
                        <label for="uSalary">Basic Salary (₹)</label>
                        <input type="number" id="uSalary" placeholder="e.g. 50000" min="0">
                    </div>

                    <div class="form-group">
                        <label for="uExperience">Experience (Years)</label>
                        <input type="number" id="uExperience" placeholder="e.g. 3.5" min="0" step="0.5">
                    </div>

                    <div class="form-group">
                        <label for="uQualification">Qualification</label>
                        <input type="text" id="uQualification" placeholder="e.g. B.Tech, MBA, MCA">
                    </div>

                    <div class="form-group">
                        <label for="uProbation">Probation Period (Days)</label>
                        <input type="number" id="uProbation" placeholder="e.g. 90" min="0">
                    </div>

                    <div class="form-group">
                        <label for="uNotice">Notice Period (Days)</label>
                        <input type="number" id="uNotice" placeholder="e.g. 30" min="0">
                    </div>

                    <div class="form-group full-width">
                        <label>Emergency Contact</label>
                        <div class="form-grid inner-grid">
                            <div class="form-group" style="margin:0">
                                <input type="text" id="uEmergencyName" placeholder="Contact Name">
                            </div>
                            <div class="form-group" style="margin:0">
                                <input type="tel" id="uEmergencyPhone" placeholder="10-digit Phone" maxlength="10">
                            </div>
                            <div class="form-group" style="margin:0">
                                <input type="text" id="uEmergencyRel" placeholder="Relationship (e.g. Parent, Spouse)">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ============================================
                 STEP 3: ACCOUNT & ACCESS SETUP
            ============================================ -->
            <div class="wizard-step-panel" id="stepPanel3">
                <div class="step-section-title">
                    <div class="step-section-icon">🔐</div>
                    <div>
                        <h3>Account &amp; Security Setup</h3>
                        <p>Credentials and system authorization access</p>
                    </div>
                </div>

                <div class="form-grid">
                    <div class="form-group full-width">
                        <label for="uEmail">Official Email (Login) <span class="required">*</span></label>
                        <input type="email" id="uEmail" placeholder="official@company.com" required>
                        <span class="field-hint">This will be used as the primary email for user system login</span>
                    </div>

                    <div class="form-group">
                        <label for="uUsername">Username</label>
                        <input type="text" id="uUsername" placeholder="Leave blank to auto-generate">
                        <span class="field-hint">Auto-generated from email/name if left blank</span>
                    </div>

                    <div class="form-group">
                        <label for="uRole">System Role <span class="required">*</span></label>
                        <select id="uRole" required>
                            <option value="">Select role</option>
                            <option value="admin">Admin</option>
                            <option value="hr">HR</option>
                            <option value="manager">Manager</option>
                            <option value="employee">Employee</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="uStatus">Account Status <span class="required">*</span></label>
                        <select id="uStatus" required>
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                            <option value="locked">Locked</option>
                        </select>
                    </div>

                    <div class="form-group" id="pwdGroup">
                        <div style="display:flex; justify-content:space-between; align-items:center;">
                            <label for="uPassword">Password</label>
                            <button type="button" class="btn-text-auto" onclick="generateAutoPassword()" style="border:none;background:none;color:#26658C;font-size:11px;font-weight:700;cursor:pointer;">⚡ Auto-generate</button>
                        </div>
                        <div class="password-field">
                            <input type="password" id="uPassword" placeholder="Min 8 characters" minlength="8" autocomplete="new-password">
                            <button type="button" class="toggle-pass" onclick="togglePasswordVisibility('uPassword')">👁</button>
                        </div>
                        <span class="field-hint">Leave blank to auto-generate a secure password</span>
                    </div>

                    <div class="form-group" id="confirmPwdGroup">
                        <label for="uConfirmPassword">Confirm Password</label>
                        <div class="password-field">
                            <input type="password" id="uConfirmPassword" placeholder="Confirm password" minlength="8" autocomplete="new-password">
                            <button type="button" class="toggle-pass" onclick="togglePasswordVisibility('uConfirmPassword')">👁</button>
                        </div>
                    </div>
                </div>

                <div class="info-banner" style="margin-top: 18px;">
                    <span class="info-icon">ℹ️</span>
                    <p>Accounts created by Administrators are automatically synced with employee records. The user will be able to log in immediately with their assigned role.</p>
                </div>
            </div>

            <!-- ============================================
                 STEP 4: REVIEW & CONFIRM
            ============================================ -->
            <div class="wizard-step-panel" id="stepPanel4">
                <div class="step-section-title">
                    <div class="step-section-icon">✅</div>
                    <div>
                        <h3>Review &amp; Confirm</h3>
                        <p>Verify user profile information before saving</p>
                    </div>
                </div>

                <div class="review-grid" id="reviewContent">
                    <!-- Populated dynamically by buildReview() -->
                </div>
            </div>

            <div class="form-error" id="userFormError" style="margin-top: 16px;"></div>

            <!-- WIZARD NAVIGATION FOOTER -->
            <div class="wizard-nav" style="display: flex; align-items: center; justify-content: space-between; margin-top: 24px; padding-top: 18px; border-top: 1px solid #e2e8f0;">
                <div class="nav-left">
                    <button type="button" class="btn-cancel" id="cancelUserModal">Cancel</button>
                </div>
                <div class="nav-right" style="display: flex; gap: 10px;">
                    <button type="button" class="btn-prev-step" id="prevStepBtn" style="display: none; padding: 9px 18px; border: 1px solid #cbd5e1; background: #fff; border-radius: 8px; font-weight: 600; font-size: 13px; color: #475569; cursor: pointer;">
                        &larr; Previous
                    </button>
                    <button type="button" class="btn-primary btn-next-step" id="nextStepBtn" style="padding: 9px 20px; font-weight: 600; font-size: 13px; border-radius: 8px; cursor: pointer;">
                        Next Step &rarr;
                    </button>
                    <button type="submit" class="btn-primary" id="saveUserBtn" style="display: none; padding: 9px 22px; font-weight: 700; font-size: 13px; border-radius: 8px; cursor: pointer;">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="20 6 9 17 4 12"/>
                        </svg>
                        Save User
                    </button>
                </div>
            </div>

        </form>

    </div>
</div>


<!-- =========================================================
     VIEW USER MODAL
========================================================= -->
<div class="modal-overlay" id="viewUserModal">
    <div class="modal-box">
        <div class="modal-header">
            <div>
                <h3>User Details</h3>
                <p>View user information</p>
            </div>
            <button class="modal-close" id="closeViewModal">×</button>
        </div>
        <div class="modal-body" id="viewUserBody">
            <!-- Filled dynamically -->
        </div>
        <div class="modal-footer">
            <button type="button" class="btn-cancel" id="cancelViewModal">Close</button>
        </div>
    </div>
</div>


<!-- =========================================================
     LOCK USER MODAL
========================================================= -->
<div class="modal-overlay" id="lockUserModal">
    <div class="modal-box" style="max-width: 500px;">
        <div class="modal-header" style="background: linear-gradient(135deg, #fff1f2 0%, #ffffff 100%);">
            <div style="display: flex; align-items: center; gap: 12px;">
                <div style="width: 40px; height: 40px; border-radius: 10px; background: #ffe4e6; color: #e11d48; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                        <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                    </svg>
                </div>
                <div>
                    <h3 style="color: #9f1239; margin: 0; font-size: 17px; font-weight: 800;">Lock User Account</h3>
                    <p style="margin: 2px 0 0; color: #64748b; font-size: 12px;">Fill in the employee details to lock account access</p>
                </div>
            </div>
            <button class="modal-close" id="closeLockModal" type="button">&times;</button>
        </div>
        <form id="lockUserForm">
            <div class="modal-body" style="padding: 22px 24px;">
                <div style="display: flex; flex-direction: column; gap: 16px;">
                    <div class="form-group" style="margin: 0;">
                        <label for="lockUserSelect" style="font-weight: 700; font-size: 12.5px; color: #334155; margin-bottom: 6px; display: block;">Select Employee / User</label>
                        <select id="lockUserSelect" class="filter-input" style="width: 100%; height: 40px; padding: 0 12px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 13px;">
                            <option value="">-- Choose from existing users --</option>
                        </select>
                    </div>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px;">
                        <div class="form-group" style="margin: 0;">
                            <label for="lockUserName" style="font-weight: 700; font-size: 12.5px; color: #334155; margin-bottom: 6px; display: block;">Employee Name <span style="color:#e11d48">*</span></label>
                            <input type="text" id="lockUserName" placeholder="e.g. John Doe" required style="width: 100%; height: 40px; padding: 0 12px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 13px; box-sizing: border-box;">
                        </div>
                        <div class="form-group" style="margin: 0;">
                            <label for="lockUserId" style="font-weight: 700; font-size: 12.5px; color: #334155; margin-bottom: 6px; display: block;">Employee / User ID <span style="color:#e11d48">*</span></label>
                            <input type="text" id="lockUserId" placeholder="e.g. USR001" required style="width: 100%; height: 40px; padding: 0 12px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 13px; box-sizing: border-box;">
                        </div>
                    </div>
                    <div class="form-group" style="margin: 0;">
                        <label for="lockUserEmail" style="font-weight: 700; font-size: 12.5px; color: #334155; margin-bottom: 6px; display: block;">Email Address <span style="color:#e11d48">*</span></label>
                        <input type="email" id="lockUserEmail" placeholder="e.g. john@example.com" required style="width: 100%; height: 40px; padding: 0 12px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 13px; box-sizing: border-box;">
                    </div>
                    <div class="form-group" style="margin: 0;">
                        <label for="lockUserDescription" style="font-weight: 700; font-size: 12.5px; color: #334155; margin-bottom: 6px; display: block;">Description / Reason <span style="color:#e11d48">*</span></label>
                        <textarea id="lockUserDescription" rows="3" placeholder="Provide reason for locking this user account..." required style="width: 100%; padding: 10px 12px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 13px; font-family: inherit; resize: vertical; box-sizing: border-box;"></textarea>
                    </div>
                </div>
                <div class="form-error" id="lockUserError" style="margin-top: 12px;"></div>
            </div>
            <div class="modal-footer" style="padding: 16px 24px; border-top: 1px solid #e2e8f0; display: flex; justify-content: flex-end; gap: 10px;">
                <button type="button" class="btn-cancel" id="cancelLockModal">Cancel</button>
                <button type="submit" class="btn-primary" id="submitLockBtn" style="background: linear-gradient(135deg, #e11d48 0%, #be123c 100%); border-color: #be123c; color: #fff;">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                        <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                    </svg>
                    Lock User
                </button>
            </div>
        </form>
    </div>
</div>


<!-- =========================================================
     DEACTIVATE USER MODAL
========================================================= -->
<div class="modal-overlay" id="deactivateUserModal">
    <div class="modal-box" style="max-width: 500px;">
        <div class="modal-header" style="background: linear-gradient(135deg, #f0fdfa 0%, #ffffff 100%);">
            <div style="display: flex; align-items: center; gap: 12px;">
                <div style="width: 40px; height: 40px; border-radius: 10px; background: #ccfbf1; color: #0d9488; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                        <circle cx="8.5" cy="7" r="4"/>
                        <line x1="18" y1="8" x2="23" y2="13"/>
                        <line x1="23" y1="8" x2="18" y2="13"/>
                    </svg>
                </div>
                <div>
                    <h3 style="color: #115e59; margin: 0; font-size: 17px; font-weight: 800;">Deactivate User Account</h3>
                    <p style="margin: 2px 0 0; color: #64748b; font-size: 12px;">Fill in the employee details to deactivate this account</p>
                </div>
            </div>
            <button class="modal-close" id="closeDeactivateModal" type="button">&times;</button>
        </div>
        <form id="deactivateUserForm">
            <div class="modal-body" style="padding: 22px 24px;">
                <div style="display: flex; flex-direction: column; gap: 16px;">
                    <div class="form-group" style="margin: 0;">
                        <label for="deactivateUserSelect" style="font-weight: 700; font-size: 12.5px; color: #334155; margin-bottom: 6px; display: block;">Select Employee / User</label>
                        <select id="deactivateUserSelect" class="filter-input" style="width: 100%; height: 40px; padding: 0 12px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 13px;">
                            <option value="">-- Choose from existing users --</option>
                        </select>
                    </div>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px;">
                        <div class="form-group" style="margin: 0;">
                            <label for="deactivateUserName" style="font-weight: 700; font-size: 12.5px; color: #334155; margin-bottom: 6px; display: block;">Employee Name <span style="color:#0d9488">*</span></label>
                            <input type="text" id="deactivateUserName" placeholder="e.g. John Doe" required style="width: 100%; height: 40px; padding: 0 12px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 13px; box-sizing: border-box;">
                        </div>
                        <div class="form-group" style="margin: 0;">
                            <label for="deactivateUserId" style="font-weight: 700; font-size: 12.5px; color: #334155; margin-bottom: 6px; display: block;">Employee / User ID <span style="color:#0d9488">*</span></label>
                            <input type="text" id="deactivateUserId" placeholder="e.g. USR001" required style="width: 100%; height: 40px; padding: 0 12px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 13px; box-sizing: border-box;">
                        </div>
                    </div>
                    <div class="form-group" style="margin: 0;">
                        <label for="deactivateUserEmail" style="font-weight: 700; font-size: 12.5px; color: #334155; margin-bottom: 6px; display: block;">Email Address <span style="color:#0d9488">*</span></label>
                        <input type="email" id="deactivateUserEmail" placeholder="e.g. john@example.com" required style="width: 100%; height: 40px; padding: 0 12px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 13px; box-sizing: border-box;">
                    </div>
                    <div class="form-group" style="margin: 0;">
                        <label for="deactivateUserDescription" style="font-weight: 700; font-size: 12.5px; color: #334155; margin-bottom: 6px; display: block;">Description / Reason <span style="color:#0d9488">*</span></label>
                        <textarea id="deactivateUserDescription" rows="3" placeholder="Provide reason for deactivating this user account..." required style="width: 100%; padding: 10px 12px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 13px; font-family: inherit; resize: vertical; box-sizing: border-box;"></textarea>
                    </div>
                </div>
                <div class="form-error" id="deactivateUserError" style="margin-top: 12px;"></div>
            </div>
            <div class="modal-footer" style="padding: 16px 24px; border-top: 1px solid #e2e8f0; display: flex; justify-content: flex-end; gap: 10px;">
                <button type="button" class="btn-cancel" id="cancelDeactivateModal">Cancel</button>
                <button type="submit" class="btn-primary" id="submitDeactivateBtn" style="background: linear-gradient(135deg, #0d9488 0%, #0f766e 100%); border-color: #0f766e; color: #fff;">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="20 6 9 17 4 12"/>
                    </svg>
                    Deactivate User
                </button>
            </div>
        </form>
    </div>
</div>


<!-- TOAST -->
<div class="toast-container" id="toastContainer"></div>


<script src="../assets/js/theme.js?v=2"></script>
<script src="../assets/js/users.js?v=<?= time() ?>"></script>

</body>
</html>

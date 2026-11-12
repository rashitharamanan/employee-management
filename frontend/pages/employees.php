<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Employees - Employee QR System</title>

    <link
        rel="stylesheet"
        href="../assets/css/app-theme.css?v=1"
    >

    <link
        rel="stylesheet"
        href="../assets/css/sidebar.css?v=<?= time() ?>"
    >

    <link
        rel="stylesheet"
        href="../assets/css/dashboard.css?v=10"
    >

    <link
        rel="stylesheet"
        href="../assets/css/employees.css?v=6"
    >

    <link rel="stylesheet" href="../assets/css/topbar.css?v=1">

</head>


<body>

<div class="app">


    <?php include '../includes/sidebar.php'; ?>


    <!-- =====================================================
         MAIN CONTENT
    ====================================================== -->

    <main class="main-content">


        <?php include '../includes/topbar.php'; ?>



        <!-- =================================================
             PAGE CONTENT
        ================================================== -->

        <section class="content">


            <!-- PAGE HEADER -->

            <div class="page-header">


            </div>



            <!-- =================================================
                 SEARCH + ADD
            ================================================== -->

            <div class="toolbar">


                <!-- <div class="search-box">

                    <span class="search-icon">
                        🔍
                    </span>

                    <input
                        type="text"
                        id="searchInput"
                        placeholder="Search employees..."
                    >

                </div> -->



                <button
                    type="button"
                    id="addEmployeeButton"
                    class="add-button"
                >

                    <span>
                        +
                    </span>

                    Add Employee

                </button>


            </div>



            <!-- =================================================
                 EMPLOYEE COUNT
            ================================================== -->

            <div class="employee-heading">

                <h2>
                    Employee List
                </h2>

                <span
                    id="employeeCount"
                    class="employee-count"
                >
                    0 Employees
                </span>

            </div>



            <!-- =================================================
                 EMPLOYEE TABLE
            ================================================== -->

            <div class="employee-card">

                <div class="table-wrapper">

                    <table class="employee-table">

                        <thead>

                            <tr>

                                <th>
                                    ID
                                </th>

                                <th>
                                    Name
                                </th>

                                <th>
                                    Department
                                </th>

                                <th>
                                    Designation
                                </th>

                                <th>
                                    Action
                                </th>

                            </tr>

                        </thead>


                        <tbody id="employeeTableBody">

                            <tr>

                                <td
                                    colspan="5"
                                    class="loading-row"
                                >
                                    Loading employees...
                                </td>

                            </tr>

                        </tbody>

                    </table>

                </div>

            </div>


        </section>

    </main>

</div>



<!-- =========================================================
     ADD EMPLOYEE WIZARD MODAL
========================================================== -->

<div id="employeeModal" class="modal">

    <div class="modal-content wizard-modal">


        <!-- MODAL HEADER -->

        <div class="modal-header">

            <div class="modal-header-left">
                <h2 id="modalTitle">Add Employee</h2>
                <p id="modalSubtitle" class="modal-subtitle">Fill in the details to create a new employee</p>
            </div>

            <button
                type="button"
                id="closeModalButton"
                class="close-button"
                title="Close"
            >
                ×
            </button>

        </div>


        <!-- STEP INDICATOR -->

        <div class="wizard-steps" id="wizardSteps">

            <div class="step-item active" data-step="1">
                <div class="step-circle">
                    <span class="step-num">1</span>
                    <svg class="step-check" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3">
                        <polyline points="20 6 9 17 4 12"></polyline>
                    </svg>
                </div>
                <span class="step-label">Personal</span>
            </div>

            <div class="step-line"></div>

            <div class="step-item" data-step="2">
                <div class="step-circle">
                    <span class="step-num">2</span>
                    <svg class="step-check" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3">
                        <polyline points="20 6 9 17 4 12"></polyline>
                    </svg>
                </div>
                <span class="step-label">Employment</span>
            </div>

            <div class="step-line"></div>

            <div class="step-item" data-step="3">
                <div class="step-circle">
                    <span class="step-num">3</span>
                    <svg class="step-check" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3">
                        <polyline points="20 6 9 17 4 12"></polyline>
                    </svg>
                </div>
                <span class="step-label">Account</span>
            </div>

            <div class="step-line"></div>

            <div class="step-item" data-step="4">
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

        <form id="employeeForm" novalidate>


            <!-- ============================================
                 STEP 1: PERSONAL INFORMATION
            ============================================ -->

            <div class="wizard-step-panel active" id="stepPanel1">

                <div class="step-section-title">
                    <div class="step-section-icon">👤</div>
                    <div>
                        <h3>Personal Information</h3>
                        <p>Basic personal details of the employee</p>
                    </div>
                </div>

                <div class="form-grid">

                    <div class="form-group">
                        <label for="fullName">Full Name <span class="required">*</span></label>
                        <input type="text" id="fullName" placeholder="e.g. John Doe" autocomplete="off">
                    </div>

                    <div class="form-group">
                        <label for="dateOfBirth">Date of Birth</label>
                        <input type="date" id="dateOfBirth">
                    </div>

                    <div class="form-group">
                        <label for="gender">Gender</label>
                        <select id="gender">
                            <option value="">Select gender</option>
                            <option value="MALE">Male</option>
                            <option value="FEMALE">Female</option>
                            <option value="OTHER">Other</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="maritalStatus">Marital Status</label>
                        <select id="maritalStatus">
                            <option value="">Select status</option>
                            <option value="Single">Single</option>
                            <option value="Married">Married</option>
                            <option value="Divorced">Divorced</option>
                            <option value="Widowed">Widowed</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="phone">Phone Number</label>
                        <input type="tel" id="phone" placeholder="e.g. +91 98765 43210">
                    </div>

                    <div class="form-group">
                        <label for="personalEmail">Personal Email</label>
                        <input type="email" id="personalEmail" placeholder="personal@gmail.com">
                    </div>

                    <div class="form-group">
                        <label for="nationality">Nationality</label>
                        <input type="text" id="nationality" placeholder="e.g. Indian">
                    </div>

                    <div class="form-group full-width">
                        <label for="address">Address</label>
                        <textarea id="address" rows="2" placeholder="Street address..."></textarea>
                    </div>

                    <div class="form-group">
                        <label for="city">City</label>
                        <input type="text" id="city" placeholder="e.g. Mumbai">
                    </div>

                    <div class="form-group">
                        <label for="state">State</label>
                        <input type="text" id="state" placeholder="e.g. Maharashtra">
                    </div>

                    <div class="form-group">
                        <label for="pincode">Pincode</label>
                        <input type="text" id="pincode" placeholder="6-digit pincode" maxlength="6">
                    </div>

                </div>

            </div>


            <!-- ============================================
                 STEP 2: EMPLOYMENT DETAILS
            ============================================ -->

            <div class="wizard-step-panel" id="stepPanel2">

                <div class="step-section-title">
                    <div class="step-section-icon">💼</div>
                    <div>
                        <h3>Employment Details</h3>
                        <p>Job role and organizational information</p>
                    </div>
                </div>

                <div class="form-grid">

                    <div class="form-group">
                        <label for="employeeCode">Employee Code</label>
                        <div class="input-with-badge">
                            <input type="text" id="employeeCode" placeholder="Loading..." readonly>
                            <span class="auto-badge">Auto</span>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="joiningDate">Joining Date</label>
                        <input type="date" id="joiningDate">
                    </div>

                    <div class="form-group">
                        <label for="department">Department <span class="required">*</span></label>
                        <select id="department">
                            <option value="">Select department</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="designation">Designation <span class="required">*</span></label>
                        <select id="designation">
                            <option value="">Select designation</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="employmentType">Employment Type</label>
                        <select id="employmentType">
                            <option value="Full Time">Full Time</option>
                            <option value="Part Time">Part Time</option>
                            <option value="Contract">Contract</option>
                            <option value="Intern">Intern</option>
                            <option value="Freelance">Freelance</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="workLocation">Work Location</label>
                        <input type="text" id="workLocation" placeholder="e.g. Head Office, Remote">
                    </div>

                    <div class="form-group">
                        <label for="workShift">Work Shift</label>
                        <input type="text" id="workShift" placeholder="e.g. Morning, General">
                    </div>

                    <div class="form-group">
                        <label for="basicSalary">Basic Salary (₹)</label>
                        <input type="number" id="basicSalary" placeholder="e.g. 50000" min="0">
                    </div>

                    <div class="form-group">
                        <label for="experienceYears">Experience (Years)</label>
                        <input type="number" id="experienceYears" placeholder="e.g. 3" min="0" step="0.5">
                    </div>

                    <div class="form-group">
                        <label for="qualification">Qualification</label>
                        <input type="text" id="qualification" placeholder="e.g. B.Tech, MBA">
                    </div>

                    <div class="form-group">
                        <label for="probationPeriod">Probation Period (Days)</label>
                        <input type="number" id="probationPeriod" placeholder="e.g. 90" min="0">
                    </div>

                    <div class="form-group">
                        <label for="noticePeriod">Notice Period (Days)</label>
                        <input type="number" id="noticePeriod" placeholder="e.g. 30" min="0">
                    </div>

                    <div class="form-group full-width">
                        <label>Emergency Contact</label>
                        <div class="form-grid inner-grid">
                            <div class="form-group" style="margin:0">
                                <input type="text" id="emergencyContactName" placeholder="Contact name">
                            </div>
                            <div class="form-group" style="margin:0">
                                <input type="text" id="emergencyContactPhone" placeholder="10-digit phone" maxlength="10">
                            </div>
                            <div class="form-group" style="margin:0">
                                <input type="text" id="emergencyContactRelationship" placeholder="Relationship (e.g. Parent)">
                            </div>
                        </div>
                    </div>

                </div>

            </div>


            <!-- ============================================
                 STEP 3: ACCOUNT SETUP
            ============================================ -->

            <div class="wizard-step-panel" id="stepPanel3">

                <div class="step-section-title">
                    <div class="step-section-icon">🔐</div>
                    <div>
                        <h3>Account Setup</h3>
                        <p>Login credentials for the employee portal</p>
                    </div>
                </div>

                <div class="form-grid">

                    <div class="form-group full-width">
                        <label for="loginEmail">Official Email (Login) <span class="required">*</span></label>
                        <input type="email" id="loginEmail" placeholder="official@company.com">
                        <span class="field-hint">This will be used as the employee's login email</span>
                    </div>

                    <div class="form-group">
                        <label for="username">Username</label>
                        <input type="text" id="username" placeholder="Leave blank to auto-generate">
                        <span class="field-hint">Optional — auto-generated if left blank</span>
                    </div>

                    <div class="form-group">
                        <label for="roleId">Role</label>
                        <select id="roleId">
                            <option value="4">Employee</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="password">Password</label>
                        <div class="password-field">
                            <input type="password" id="password" placeholder="Min 8 characters">
                            <button type="button" class="toggle-pass" onclick="togglePassword('password')">👁</button>
                        </div>
                        <span class="field-hint">Leave blank to auto-generate a temporary password</span>
                    </div>

                    <div class="form-group">
                        <label for="confirmPassword">Confirm Password</label>
                        <div class="password-field">
                            <input type="password" id="confirmPassword" placeholder="Re-enter password">
                            <button type="button" class="toggle-pass" onclick="togglePassword('confirmPassword')">👁</button>
                        </div>
                    </div>

                </div>

                <div class="info-banner">
                    <span class="info-icon">ℹ️</span>
                    <p>If no password is provided, a secure temporary password will be auto-generated. The employee can change it after first login.</p>
                </div>

            </div>


            <!-- ============================================
                 STEP 4: REVIEW & SUBMIT
            ============================================ -->

            <div class="wizard-step-panel" id="stepPanel4">

                <div class="step-section-title">
                    <div class="step-section-icon">✅</div>
                    <div>
                        <h3>Review & Submit</h3>
                        <p>Verify all details before creating the employee</p>
                    </div>
                </div>

                <div id="reviewContent" class="review-grid">
                    <!-- Filled dynamically -->
                </div>

            </div>


            <!-- FORM ERROR -->

            <div id="formError" class="form-error"></div>


            <!-- WIZARD NAVIGATION -->

            <div class="wizard-nav">

                <button
                    type="button"
                    id="prevStepBtn"
                    class="nav-btn prev-btn"
                    style="display:none"
                >
                    ← Back
                </button>

                <div class="wizard-nav-right">

                    <button
                        type="button"
                        id="cancelButton"
                        class="cancel-button"
                    >
                        Cancel
                    </button>

                    <button
                        type="button"
                        id="nextStepBtn"
                        class="nav-btn next-btn"
                    >
                        Next →
                    </button>

                    <button
                        type="submit"
                        id="saveEmployeeButton"
                        class="save-button"
                        style="display:none"
                    >
                        <span id="saveButtonText">Create Employee</span>
                    </button>

                </div>

            </div>


        </form>

    </div>

</div>



<!-- =========================================================
     SIMPLE EDIT EMPLOYEE MODAL (Quick Edit)
========================================================== -->

<div id="editModal" class="modal">

    <div class="modal-content">


        <div class="modal-header">

            <h2>Edit Employee</h2>

            <button
                type="button"
                id="closeEditModalButton"
                class="close-button"
            >
                ×
            </button>

        </div>


        <form id="editEmployeeForm">


            <div class="form-grid">


                <div class="form-group">

                    <label for="editFullName">
                        Full Name
                    </label>

                    <input
                        type="text"
                        id="editFullName"
                        required
                    >

                </div>



                <div class="form-group">

                    <label for="editEmail">
                        Email
                    </label>

                    <input
                        type="email"
                        id="editEmail"
                    >

                </div>



                <div class="form-group">

                    <label for="editPhone">
                        Phone
                    </label>

                    <input
                        type="text"
                        id="editPhone"
                    >

                </div>



                <div class="form-group">

                    <label for="editDepartment">
                        Department
                    </label>

                    <input
                        type="text"
                        id="editDepartment"
                    >

                </div>



                <div class="form-group">

                    <label for="editDesignation">
                        Designation
                    </label>

                    <input
                        type="text"
                        id="editDesignation"
                    >

                </div>



                <div class="form-group full-width">

                    <label for="editAddress">
                        Address
                    </label>

                    <textarea
                        id="editAddress"
                        rows="3"
                    ></textarea>

                </div>

            </div>



            <div
                id="editFormError"
                class="form-error"
            ></div>



            <div class="modal-actions">

                <button
                    type="button"
                    id="cancelEditButton"
                    class="cancel-button"
                >
                    Cancel
                </button>


                <button
                    type="submit"
                    id="saveEditEmployeeButton"
                    class="save-button"
                >
                    Update Employee
                </button>

            </div>


        </form>

    </div>

</div>



<script src="../assets/js/theme.js?v=1"></script>
<script src="../assets/js/employees.js?v=7"></script>

</body>

</html>
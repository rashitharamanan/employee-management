/*
|==========================================================================
| EMPLOYEE QR SYSTEM — EMPLOYEES PAGE JS (v5)
|==========================================================================
*/

const API_BASE_URL = "http://localhost/employee-qr-system/backend/public";

let employees = [];
let editingId  = null;

function getAuthHeaders(extra = {}) {
    const token = localStorage.getItem('auth_token') || localStorage.getItem('token') || '';
    const headers = { "Accept": "application/json", ...extra };
    if (token) {
        headers["Authorization"] = `Bearer ${token}`;
    }
    return headers;
}


/* -----------------------------------------------------------------------
   DOM REFERENCES — TABLE
----------------------------------------------------------------------- */

const employeeTableBody =
    document.getElementById("employeeTableBody") ||
    document.getElementById("employeeList");

const employeeList  = employeeTableBody;
const employeeCount = document.getElementById("employeeCount");
const searchInput   = document.getElementById("searchInput");
const logoutButton  = document.getElementById("logoutButton");

/* -----------------------------------------------------------------------
   DOM REFERENCES — ADD WIZARD MODAL
----------------------------------------------------------------------- */

const addEmployeeButton = document.getElementById("addEmployeeButton");
const modal             = document.getElementById("employeeModal");
const closeModalButton  = document.getElementById("closeModalButton");
const cancelButton      = document.getElementById("cancelButton");
const employeeForm      = document.getElementById("employeeForm");
const formError         = document.getElementById("formError");
const prevStepBtn       = document.getElementById("prevStepBtn");
const nextStepBtn       = document.getElementById("nextStepBtn");
const saveEmployeeButton = document.getElementById("saveEmployeeButton");

/* -----------------------------------------------------------------------
   DOM REFERENCES — EDIT MODAL
----------------------------------------------------------------------- */

const editModal             = document.getElementById("editModal");
const closeEditModalButton  = document.getElementById("closeEditModalButton");
const cancelEditButton      = document.getElementById("cancelEditButton");
const editEmployeeForm      = document.getElementById("editEmployeeForm");
const editFormError         = document.getElementById("editFormError");
const saveEditEmployeeButton = document.getElementById("saveEditEmployeeButton");


/* =======================================================================
   WIZARD STATE
======================================================================= */

let currentStep   = 1;
const TOTAL_STEPS = 4;

/* Cached dropdown options */
let formOptions = { departments: [], designations: [], roles: [] };


/*
|--------------------------------------------------------------------------
| LOAD EMPLOYEES
|--------------------------------------------------------------------------
*/

async function loadEmployees() {

    if (employeeTableBody) {
        employeeTableBody.innerHTML = `
            <tr>
                <td colspan="5" class="loading-row">
                    Loading employees...
                </td>
            </tr>
        `;
    }

    if (employeeCount) {
        employeeCount.textContent = "Loading...";
    }

    try {

        const response = await fetch(
            `${API_BASE_URL}/api/employees`,
            {
                method: "GET",
                credentials: "include",
                headers: getAuthHeaders()
            }
        );

        const text = await response.text();

        console.log("Employees API response:", text);

        let result;

        try {
            result = JSON.parse(text);
        } catch (e) {
            throw new Error("Server returned an invalid response.");
        }

        if (!response.ok) {
            throw new Error(result.message || "Unable to load employees.");
        }

        if (result.success === false) {
            throw new Error(result.message || "Unable to load employees.");
        }

        if (Array.isArray(result.data)) {
            employees = result.data;
        } else if (result.data && Array.isArray(result.data.employees)) {
            employees = result.data.employees;
        } else if (Array.isArray(result.employees)) {
            employees = result.employees;
        } else {
            employees = [];
        }

        console.log("Employees loaded:", employees);

        renderEmployees(employees);

    } catch (error) {

        console.error("Employee loading error:", error);

        if (employeeCount) {
            employeeCount.textContent = "Unable to load employees";
        }

        if (employeeTableBody) {
            employeeTableBody.innerHTML = `
                <tr>
                    <td colspan="5" class="loading-row" style="color:#ef4444;">
                        ${escapeHtml(error.message)}
                        <br><br>
                        <button
                            onclick="loadEmployees()"
                            style="padding:6px 14px;border:none;border-radius:6px;background:#078b8d;color:white;cursor:pointer;"
                        >
                            Retry
                        </button>
                    </td>
                </tr>
            `;
        }

    }

}


/*
|--------------------------------------------------------------------------
| RENDER EMPLOYEES
|--------------------------------------------------------------------------
*/

function renderEmployees(list) {

    if (employeeCount) {
        employeeCount.textContent = `${list.length} ${list.length === 1 ? "Employee" : "Employees"}`;
    }

    if (!employeeTableBody) { return; }

    if (list.length === 0) {
        employeeTableBody.innerHTML = `
            <tr>
                <td colspan="5" class="loading-row">
                    No employees found.
                </td>
            </tr>
        `;
        return;
    }

    employeeTableBody.innerHTML = list.map(employee => {

        const id          = employee.id ?? "-";
        const name        = employee.full_name ?? employee.name ?? "-";
        const department  = employee.department ?? "-";
        const designation = employee.designation ?? "-";
        const initials    = getInitials(name);

        return `
            <tr>
                <td>${escapeHtml(id)}</td>

                <td>
                    <div class="employee-name-cell">
                        <span class="employee-avatar">${escapeHtml(initials)}</span>
                        <span>${escapeHtml(name)}</span>
                    </div>
                </td>

                <td>
                    <span class="department-badge">${escapeHtml(department)}</span>
                </td>

                <td>${escapeHtml(designation)}</td>

                <td>
                    <div class="action-buttons">
                        <button
                            type="button"
                            class="action-button edit-button"
                            title="Edit Employee"
                            onclick="editEmployee(${Number(id)})"
                        >✏️</button>

                        <button
                            type="button"
                            class="action-button download-button"
                            title="Download QR"
                            onclick="downloadQR(${Number(id)})"
                        >⬇️</button>

                        <button
                            type="button"
                            class="action-button delete-button"
                            title="Delete Employee"
                            onclick="deleteEmployee(${Number(id)})"
                        >🗑️</button>
                    </div>
                </td>
            </tr>
        `;

    }).join("");

}


/*
|--------------------------------------------------------------------------
| SEARCH
|--------------------------------------------------------------------------
*/

if (searchInput) {
    searchInput.addEventListener("input", function () {

        const keyword = this.value.trim().toLowerCase();

        if (!keyword) {
            renderEmployees(employees);
            return;
        }

        const filtered = employees.filter(employee =>
            [
                employee.id,
                employee.employee_code,
                employee.full_name,
                employee.email,
                employee.phone,
                employee.department,
                employee.designation,
                employee.address
            ].some(value =>
                String(value ?? "").toLowerCase().includes(keyword)
            )
        );

        renderEmployees(filtered);

    });
}


/*
|==========================================================================
| ADD EMPLOYEE WIZARD
|==========================================================================
*/

/*
|--------------------------------------------------------------------------
| OPEN WIZARD
|--------------------------------------------------------------------------
*/

if (addEmployeeButton) {
    addEmployeeButton.addEventListener("click", async function () {

        editingId = null;
        currentStep = 1;

        if (employeeForm) { employeeForm.reset(); }

        hideFormError();

        updateWizardUI();

        if (modal) { modal.classList.add("show"); }

        /* Load options and next code in parallel */
        await Promise.all([
            loadFormOptions(),
            loadNextCode()
        ]);

    });
}


/*
|--------------------------------------------------------------------------
| LOAD FORM OPTIONS (departments, designations, roles)
|--------------------------------------------------------------------------
*/

async function loadFormOptions() {

    try {

        const response = await fetch(
            `${API_BASE_URL}/api/employees/options`,
            {
                credentials: "include",
                headers: getAuthHeaders()
            }
        );

        const text = await response.text();

        let result;
        try { result = JSON.parse(text); } catch (e) { return; }

        if (!result || result.success === false) { return; }

        const data = result.data || {};

        /* Departments */
        const departmentSel = document.getElementById("department");
        if (departmentSel && Array.isArray(data.departments)) {
            formOptions.departments = data.departments;
            const current = departmentSel.value;
            departmentSel.innerHTML = `<option value="">Select department</option>`;
            data.departments.forEach(d => {
                const name = d.name || d;
                const opt  = document.createElement("option");
                opt.value = name;
                opt.textContent = name;
                if (name === current) { opt.selected = true; }
                departmentSel.appendChild(opt);
            });
            departmentSel.onchange = function () {
                renderDesignationOptions(this.value, false);
            };
        }

        /* Designations */
        const designationSel = document.getElementById("designation");
        if (designationSel && Array.isArray(data.designations)) {
            formOptions.designations = data.designations;
            renderDesignationOptions(departmentSel ? departmentSel.value : "", true);
        }

        /* Roles */
        const roleSel = document.getElementById("roleId");
        if (roleSel && Array.isArray(data.roles)) {
            formOptions.roles = data.roles;
            roleSel.innerHTML = "";
            data.roles.forEach(r => {
                const opt = document.createElement("option");
                opt.value = r.id;
                opt.textContent = r.name;
                if (Number(r.id) === 4) { opt.selected = true; }
                roleSel.appendChild(opt);
            });
            if (roleSel.options.length === 0) {
                roleSel.innerHTML = `<option value="4">Employee</option>`;
            }
        }

    } catch (e) {
        console.warn("Could not load form options:", e);
    }

}

function renderDesignationOptions(selectedDept = "", keepCurrent = true) {
    const designationSel = document.getElementById("designation");
    if (!designationSel || !Array.isArray(formOptions.designations)) { return; }

    const current = keepCurrent ? designationSel.value : "";
    designationSel.innerHTML = `<option value="">Select designation</option>`;

    let list = formOptions.designations;
    if (selectedDept) {
        const filtered = list.filter(d => {
            if (typeof d === "string") { return true; }
            const dName = d.department_name || "";
            return dName.toLowerCase() === selectedDept.toLowerCase();
        });
        if (filtered.length > 0) {
            list = filtered;
        }
    }

    list.forEach(d => {
        const name = typeof d === "string" ? d : (d.name || d.designation || d.title || "");
        if (!name) { return; }
        const opt = document.createElement("option");
        opt.value = name;
        opt.textContent = name;
        if (name === current) { opt.selected = true; }
        designationSel.appendChild(opt);
    });
}


/*
|--------------------------------------------------------------------------
| LOAD NEXT EMPLOYEE CODE
|--------------------------------------------------------------------------
*/

async function loadNextCode() {

    const codeInput = document.getElementById("employeeCode");
    if (!codeInput) { return; }

    codeInput.value = "Loading...";

    try {

        const response = await fetch(
            `${API_BASE_URL}/api/employees/next-code`,
            {
                credentials: "include",
                headers: getAuthHeaders()
            }
        );

        const result = await response.json();

        if (result.success && result.data && result.data.employee_code) {
            codeInput.value = result.data.employee_code;
        } else {
            codeInput.value = "Auto generated";
        }

    } catch (e) {
        codeInput.value = "Auto generated";
    }

}


/*
|--------------------------------------------------------------------------
| WIZARD: STEP NAVIGATION
|--------------------------------------------------------------------------
*/

function updateWizardUI() {

    /* Update panels */
    document.querySelectorAll(".wizard-step-panel").forEach((panel, idx) => {
        panel.classList.toggle("active", idx + 1 === currentStep);
    });

    /* Update step indicators */
    document.querySelectorAll(".step-item").forEach((item, idx) => {
        const step = idx + 1;
        item.classList.remove("active", "completed");
        if (step === currentStep)     { item.classList.add("active"); }
        else if (step < currentStep)  { item.classList.add("completed"); }
    });

    /* Update step lines */
    document.querySelectorAll(".step-line").forEach((line, idx) => {
        const step = idx + 1;
        line.classList.toggle("completed", step < currentStep);
    });

    /* Update nav buttons */
    if (prevStepBtn)       { prevStepBtn.style.display       = currentStep > 1           ? "flex" : "none"; }
    if (nextStepBtn)       { nextStepBtn.style.display       = currentStep < TOTAL_STEPS ? "flex" : "none"; }
    if (saveEmployeeButton){ saveEmployeeButton.style.display = currentStep === TOTAL_STEPS ? "flex" : "none"; }

    /* Build review on step 4 */
    if (currentStep === TOTAL_STEPS) {
        buildReview();
    }

    hideFormError();

}


/* ---- NEXT ---- */

if (nextStepBtn) {
    nextStepBtn.addEventListener("click", function () {

        if (!validateStep(currentStep)) { return; }

        if (currentStep < TOTAL_STEPS) {
            currentStep++;
            updateWizardUI();
        }

    });
}


/* ---- PREV ---- */

if (prevStepBtn) {
    prevStepBtn.addEventListener("click", function () {

        if (currentStep > 1) {
            currentStep--;
            updateWizardUI();
        }

    });
}


/*
|--------------------------------------------------------------------------
| STEP VALIDATION
|--------------------------------------------------------------------------
*/

function validateStep(step) {

    hideFormError();

    if (step === 1) {

        const fullName = val("fullName");
        if (!fullName) {
            showFormError("Full Name is required.");
            return false;
        }

        const pincode = val("pincode");
        if (pincode && !/^\d{6}$/.test(pincode)) {
            showFormError("Pincode must be exactly 6 digits.");
            return false;
        }

        const personalEmail = val("personalEmail");
        if (personalEmail && !isValidEmail(personalEmail)) {
            showFormError("Personal email address is invalid.");
            return false;
        }

    }

    if (step === 2) {

        const department = val("department");
        if (!department) {
            showFormError("Department is required.");
            return false;
        }

        const designation = val("designation");
        if (!designation) {
            showFormError("Designation is required.");
            return false;
        }

        const emergencyPhone = val("emergencyContactPhone");
        if (emergencyPhone && !/^\d{10}$/.test(emergencyPhone)) {
            showFormError("Emergency contact phone must be exactly 10 digits.");
            return false;
        }

    }

    if (step === 3) {

        const loginEmail = val("loginEmail");
        if (!loginEmail) {
            showFormError("Official email (login) is required.");
            return false;
        }

        if (!isValidEmail(loginEmail)) {
            showFormError("Official email address is invalid.");
            return false;
        }

        const password = val("password");
        const confirmPassword = val("confirmPassword");

        if (password && password.length < 8) {
            showFormError("Password must be at least 8 characters.");
            return false;
        }

        if (password && password !== confirmPassword) {
            showFormError("Password and confirm password do not match.");
            return false;
        }

    }

    return true;

}


/*
|--------------------------------------------------------------------------
| BUILD REVIEW (Step 4)
|--------------------------------------------------------------------------
*/

function buildReview() {

    const reviewContent = document.getElementById("reviewContent");
    if (!reviewContent) { return; }

    const disp = (v) => v ? escapeHtml(v) : `<span class="review-empty">—</span>`;

    reviewContent.innerHTML = `

        <!-- PERSONAL -->
        <div class="review-card">
            <div class="review-card-header">
                <span class="card-icon">👤</span>
                <h4>Personal Information</h4>
            </div>
            <div class="review-row"><span class="review-label">Full Name</span>     <span class="review-value">${disp(val("fullName"))}</span></div>
            <div class="review-row"><span class="review-label">Date of Birth</span> <span class="review-value">${disp(val("dateOfBirth"))}</span></div>
            <div class="review-row"><span class="review-label">Gender</span>        <span class="review-value">${disp(val("gender"))}</span></div>
            <div class="review-row"><span class="review-label">Phone</span>         <span class="review-value">${disp(val("phone"))}</span></div>
            <div class="review-row"><span class="review-label">Personal Email</span><span class="review-value">${disp(val("personalEmail"))}</span></div>
            <div class="review-row"><span class="review-label">Nationality</span>   <span class="review-value">${disp(val("nationality"))}</span></div>
        </div>

        <!-- EMPLOYMENT -->
        <div class="review-card">
            <div class="review-card-header">
                <span class="card-icon">💼</span>
                <h4>Employment Details</h4>
            </div>
            <div class="review-row"><span class="review-label">Employee Code</span>  <span class="review-value">${disp(val("employeeCode"))}</span></div>
            <div class="review-row"><span class="review-label">Department</span>     <span class="review-value">${disp(val("department"))}</span></div>
            <div class="review-row"><span class="review-label">Designation</span>    <span class="review-value">${disp(val("designation"))}</span></div>
            <div class="review-row"><span class="review-label">Joining Date</span>   <span class="review-value">${disp(val("joiningDate"))}</span></div>
            <div class="review-row"><span class="review-label">Employment Type</span><span class="review-value">${disp(val("employmentType"))}</span></div>
            <div class="review-row"><span class="review-label">Basic Salary</span>   <span class="review-value">${val("basicSalary") ? "₹ " + escapeHtml(val("basicSalary")) : "<span class='review-empty'>—</span>"}</span></div>
        </div>

        <!-- ACCOUNT -->
        <div class="review-card">
            <div class="review-card-header">
                <span class="card-icon">🔐</span>
                <h4>Account Setup</h4>
            </div>
            <div class="review-row"><span class="review-label">Login Email</span><span class="review-value">${disp(val("loginEmail"))}</span></div>
            <div class="review-row"><span class="review-label">Username</span>    <span class="review-value">${val("username") ? escapeHtml(val("username")) : "<span class='review-empty'>Auto-generated</span>"}</span></div>
            <div class="review-row"><span class="review-label">Password</span>    <span class="review-value">${val("password") ? "••••••••" : "<span class='review-empty'>Auto-generated</span>"}</span></div>
        </div>

        <!-- ADDRESS -->
        <div class="review-card">
            <div class="review-card-header">
                <span class="card-icon">📍</span>
                <h4>Address</h4>
            </div>
            <div class="review-row"><span class="review-label">Address</span><span class="review-value">${disp(val("address"))}</span></div>
            <div class="review-row"><span class="review-label">City</span>   <span class="review-value">${disp(val("city"))}</span></div>
            <div class="review-row"><span class="review-label">State</span>  <span class="review-value">${disp(val("state"))}</span></div>
            <div class="review-row"><span class="review-label">Pincode</span><span class="review-value">${disp(val("pincode"))}</span></div>
        </div>

    `;

}


/*
|--------------------------------------------------------------------------
| SUBMIT (CREATE EMPLOYEE)
|--------------------------------------------------------------------------
*/

if (employeeForm) {
    employeeForm.addEventListener("submit", async function (event) {

        event.preventDefault();

        if (!validateStep(3)) { return; }

        const payload = {
            /* Personal */
            full_name:           val("fullName"),
            date_of_birth:       val("dateOfBirth") || null,
            gender:              val("gender") || null,
            marital_status:      val("maritalStatus") || null,
            phone:               val("phone") || null,
            personal_email:      val("personalEmail") || null,
            nationality:         val("nationality") || null,
            address:             val("address") || null,
            city:                val("city") || null,
            state:               val("state") || null,
            pincode:             val("pincode") || null,

            /* Employment */
            employee_code:              val("employeeCode") || null,
            joining_date:               val("joiningDate") || null,
            department:                 val("department") || null,
            designation:                val("designation") || null,
            employment_type:            val("employmentType") || "Full Time",
            work_location:              val("workLocation") || null,
            work_shift:                 val("workShift") || null,
            basic_salary:               val("basicSalary") ? parseFloat(val("basicSalary")) : null,
            experience_years:           val("experienceYears") ? parseFloat(val("experienceYears")) : null,
            qualification:              val("qualification") || null,
            probation_period:           val("probationPeriod") ? parseInt(val("probationPeriod")) : null,
            notice_period:              val("noticePeriod") ? parseInt(val("noticePeriod")) : null,
            emergency_contact_name:     val("emergencyContactName") || null,
            emergency_contact_phone:    val("emergencyContactPhone") || null,
            emergency_contact_relationship: val("emergencyContactRelationship") || null,

            /* Account */
            email:            val("loginEmail"),
            username:         val("username") || null,
            role_id:          val("roleId") ? parseInt(val("roleId")) : 4,
            password:         val("password") || null,
            confirm_password: val("confirmPassword") || val("password") || null,
            status:           "ACTIVE"
        };

        console.log("Submitting employee payload:", payload);

        saveEmployeeButton.disabled = true;
        document.getElementById("saveButtonText").textContent = "Creating...";

        try {

            const response = await fetch(
                `${API_BASE_URL}/api/employees`,
                {
                    method: "POST",
                    credentials: "include",
                    headers: getAuthHeaders({
                        "Content-Type": "application/json"
                    }),
                    body: JSON.stringify(payload)
                }
            );

            const text = await response.text();

            let result;
            try { result = JSON.parse(text); } catch (e) {
                throw new Error("Invalid server response.");
            }

            if (!response.ok || result.success === false) {
                throw new Error(result.message || "Unable to create employee.");
            }

            closeWizardModal();

            await loadEmployees();

            showSuccessToast("Employee created successfully! 🎉");

        } catch (error) {

            console.error(error);
            showFormError(error.message);

        } finally {

            saveEmployeeButton.disabled = false;
            document.getElementById("saveButtonText").textContent = "Create Employee";

        }

    });
}


/*
|--------------------------------------------------------------------------
| CLOSE WIZARD MODAL
|--------------------------------------------------------------------------
*/

function closeWizardModal() {
    if (modal) { modal.classList.remove("show"); }
    if (employeeForm) { employeeForm.reset(); }
    currentStep = 1;
    editingId   = null;
    hideFormError();
    updateWizardUI();
}

if (closeModalButton) {
    closeModalButton.addEventListener("click", closeWizardModal);
}

if (cancelButton) {
    cancelButton.addEventListener("click", closeWizardModal);
}

if (modal) {
    modal.addEventListener("click", function (e) {
        if (e.target === modal) { closeWizardModal(); }
    });
}


/*
|==========================================================================
| EDIT EMPLOYEE (Quick Edit Modal)
|==========================================================================
*/

function editEmployee(id) {

    const employee = employees.find(item => Number(item.id) === Number(id));

    if (!employee) {
        alert("Employee not found.");
        return;
    }

    editingId = Number(id);

    /* Populate edit form */
    setVal("editFullName",   employee.full_name || "");
    setVal("editEmail",      employee.email     || "");
    setVal("editPhone",      employee.phone     || "");
    setVal("editDepartment", employee.department || "");
    setVal("editDesignation",employee.designation || "");
    setVal("editAddress",    employee.address   || "");

    hideEditFormError();

    if (editModal) { editModal.classList.add("show"); }

}


/* EDIT FORM SUBMIT */

if (editEmployeeForm) {
    editEmployeeForm.addEventListener("submit", async function (event) {

        event.preventDefault();

        hideEditFormError();

        const fullName = val("editFullName");
        if (!fullName) {
            showEditFormError("Full Name is required.");
            return;
        }

        const data = {
            full_name:   fullName,
            email:       val("editEmail") || null,
            phone:       val("editPhone") || null,
            department:  val("editDepartment") || null,
            designation: val("editDesignation") || null,
            address:     val("editAddress") || null
        };

        saveEditEmployeeButton.disabled = true;

        try {

            const response = await fetch(
                `${API_BASE_URL}/api/employees/update?id=${editingId}`,
                {
                    method: "PUT",
                    credentials: "include",
                    headers: {
                        "Content-Type": "application/json",
                        "Accept":       "application/json"
                    },
                    body: JSON.stringify(data)
                }
            );

            const text = await response.text();

            let result;
            try { result = JSON.parse(text); } catch (e) {
                throw new Error("Invalid server response.");
            }

            if (!response.ok || result.success === false) {
                throw new Error(result.message || "Unable to update employee.");
            }

            closeEditModal();

            await loadEmployees();

            showSuccessToast("Employee updated successfully!");

        } catch (error) {

            console.error(error);
            showEditFormError(error.message);

        } finally {

            saveEditEmployeeButton.disabled = false;

        }

    });
}


function closeEditModal() {
    if (editModal)        { editModal.classList.remove("show"); }
    if (editEmployeeForm) { editEmployeeForm.reset(); }
    editingId = null;
    hideEditFormError();
}

if (closeEditModalButton) {
    closeEditModalButton.addEventListener("click", closeEditModal);
}

if (cancelEditButton) {
    cancelEditButton.addEventListener("click", closeEditModal);
}

if (editModal) {
    editModal.addEventListener("click", function (e) {
        if (e.target === editModal) { closeEditModal(); }
    });
}


/*
|--------------------------------------------------------------------------
| DELETE EMPLOYEE
|--------------------------------------------------------------------------
*/

async function deleteEmployee(id) {

    const employee = employees.find(item => Number(item.id) === Number(id));

    if (!employee) {
        alert("Employee not found.");
        return;
    }

    const confirmed = confirm(`Are you sure you want to delete ${employee.full_name}?`);

    if (!confirmed) { return; }

    try {

        const response = await fetch(
            `${API_BASE_URL}/api/employees/delete?id=${id}`,
            {
                method: "DELETE",
                credentials: "include",
                headers: { "Accept": "application/json" }
            }
        );

        const text = await response.text();

        let result;
        try { result = JSON.parse(text); } catch (e) {
            throw new Error("Invalid server response.");
        }

        if (!response.ok || result.success === false) {
            throw new Error(result.message || "Unable to delete employee.");
        }

        await loadEmployees();

        showSuccessToast("Employee deleted successfully.");

    } catch (error) {

        console.error(error);
        alert(error.message);

    }

}


/*
|--------------------------------------------------------------------------
| DOWNLOAD QR
|--------------------------------------------------------------------------
*/

async function downloadQR(id) {

    try {

        const response = await fetch(
            `${API_BASE_URL}/api/employees/${id}/qr`,
            {
                method: "GET",
                credentials: "include"
            }
        );

        if (!response.ok) {

            let message = "Unable to download QR code.";

            try {
                const result = await response.json();
                message = result.message || message;
            } catch (e) {}

            throw new Error(message);

        }

        const blob = await response.blob();
        const url  = window.URL.createObjectURL(blob);
        const link = document.createElement("a");

        link.href     = url;
        link.download = `employee-${id}-qr.png`;

        document.body.appendChild(link);
        link.click();
        link.remove();

        window.URL.revokeObjectURL(url);

    } catch (error) {

        console.error(error);
        alert(error.message);

    }

}


/*
|--------------------------------------------------------------------------
| LOGOUT
|--------------------------------------------------------------------------
*/

if (logoutButton) {
    logoutButton.addEventListener("click", async function (event) {

        event.preventDefault();

        if (!confirm("Are you sure you want to logout?")) { return; }

        try {
            await fetch(
                `${API_BASE_URL}/api/logout`,
                { method: "POST", credentials: "include" }
            );
        } catch (e) {
            console.error(e);
        }

        sessionStorage.clear();
        localStorage.clear();
        window.location.href = "login.php";

    });
}


/*
|--------------------------------------------------------------------------
| MOBILE MENU
|--------------------------------------------------------------------------
*/

const mobileMenuButton = document.getElementById("mobileMenuButton");
const sidebarElement   = document.querySelector(".sidebar");

if (mobileMenuButton && sidebarElement) {
    mobileMenuButton.addEventListener("click", () => {
        sidebarElement.classList.toggle("mobile-open");
    });
}


/*
|--------------------------------------------------------------------------
| PASSWORD TOGGLE
|--------------------------------------------------------------------------
*/

function togglePassword(id) {
    const input = document.getElementById(id);
    if (!input) { return; }
    input.type = input.type === "password" ? "text" : "password";
}


/*
|--------------------------------------------------------------------------
| SUCCESS TOAST
|--------------------------------------------------------------------------
*/

function showSuccessToast(message) {

    /* Remove existing toast if any */
    const existing = document.getElementById("successToast");
    if (existing) { existing.remove(); }

    const toast = document.createElement("div");
    toast.id = "successToast";
    toast.style.cssText = `
        position: fixed;
        bottom: 28px;
        right: 28px;
        z-index: 9999;
        background: #078b8b;
        color: white;
        padding: 14px 22px;
        border-radius: 12px;
        font-size: 15px;
        font-weight: 600;
        box-shadow: 0 8px 24px rgba(7,139,139,0.35);
        display: flex;
        align-items: center;
        gap: 10px;
        animation: slideInToast 0.3s ease;
    `;

    toast.innerHTML = `✅ ${escapeHtml(message)}`;
    document.body.appendChild(toast);

    if (!document.getElementById("toastStyle")) {
        const style = document.createElement("style");
        style.id = "toastStyle";
        style.textContent = `
            @keyframes slideInToast {
                from { transform: translateY(20px); opacity: 0; }
                to   { transform: translateY(0);    opacity: 1; }
            }
        `;
        document.head.appendChild(style);
    }

    setTimeout(() => {
        if (toast.parentNode) { toast.remove(); }
    }, 3500);

}


/*
|--------------------------------------------------------------------------
| HELPERS
|--------------------------------------------------------------------------
*/

/** Get trimmed value of an input/select/textarea by ID */
function val(id) {
    const el = document.getElementById(id);
    return el ? el.value.trim() : "";
}

/** Set value of input/textarea by ID */
function setVal(id, v) {
    const el = document.getElementById(id);
    if (el) { el.value = v; }
}

function isValidEmail(email) {
    return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
}

function getInitials(name) {
    if (!name) { return "?"; }
    const words = name.trim().split(/\s+/);
    if (words.length === 1) { return words[0].substring(0, 2).toUpperCase(); }
    return (words[0][0] + words[words.length - 1][0]).toUpperCase();
}

function escapeHtml(value) {
    return String(value ?? "")
        .replace(/&/g, "&amp;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;")
        .replace(/"/g, "&quot;")
        .replace(/'/g, "&#039;");
}

function showFormError(message) {
    if (!formError) { return; }
    formError.textContent = message;
    formError.style.display = "block";
    formError.scrollIntoView({ behavior: "smooth", block: "nearest" });
}

function hideFormError() {
    if (formError) { formError.style.display = "none"; }
}

function showEditFormError(message) {
    if (!editFormError) { return; }
    editFormError.textContent = message;
    editFormError.style.display = "block";
}

function hideEditFormError() {
    if (editFormError) { editFormError.style.display = "none"; }
}


/*
|--------------------------------------------------------------------------
| START
|--------------------------------------------------------------------------
*/

updateWizardUI();
loadEmployees();
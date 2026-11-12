/**
 * =========================================================
 * ADD EMPLOYEE WIZARD MODAL CONTROLLER
 * Handles wizard steps, validation, dynamic options, API submit
 * =========================================================
 */
(function () {
    'use strict';

    const API_HOST = 'http://localhost/employee-qr-system/backend/public';

    let currentStep = 1;
    const TOTAL_STEPS = 4;
    let formOptions = { departments: [], designations: [], roles: [] };

    function getToken() {
        return localStorage.getItem('auth_token') || localStorage.getItem('token') || '';
    }

    function getAuthHeaders(extraHeaders = {}) {
        const headers = {
            'Accept': 'application/json',
            ...extraHeaders
        };
        const token = getToken();
        if (token) {
            headers['Authorization'] = `Bearer ${token}`;
        }
        return headers;
    }

    function escapeHtml(str) {
        return String(str ?? '').replace(/[&<>"']/g, function (m) {
            return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[m];
        });
    }

    function getVal(id) {
        const el = document.getElementById(id);
        return el ? el.value.trim() : '';
    }

    function setVal(id, val) {
        const el = document.getElementById(id);
        if (el) el.value = val ?? '';
    }

    function isValidEmail(email) {
        return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
    }

    function showFormError(msg) {
        const errEl = document.getElementById('formError');
        if (!errEl) return;
        errEl.textContent = msg;
        errEl.style.display = 'block';
        errEl.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    }

    function hideFormError() {
        const errEl = document.getElementById('formError');
        if (errEl) {
            errEl.textContent = '';
            errEl.style.display = 'none';
        }
    }

    function showSuccessToast(message) {
        const existing = document.querySelector('.wizard-toast');
        if (existing) existing.remove();

        const toast = document.createElement('div');
        toast.className = 'wizard-toast';
        toast.innerHTML = `<span>✓</span> <span>${escapeHtml(message)}</span>`;
        document.body.appendChild(toast);

        setTimeout(() => {
            toast.style.transition = 'opacity 0.4s ease, transform 0.4s ease';
            toast.style.opacity = '0';
            toast.style.transform = 'translateY(20px)';
            setTimeout(() => toast.remove(), 400);
        }, 3500);
    }

    /* ----------------------------------------------------
       OPEN & CLOSE MODAL
    ---------------------------------------------------- */
    async function openModal() {
        const modal = document.getElementById('employeeModal');
        const form = document.getElementById('employeeForm');
        if (!modal) return;

        currentStep = 1;
        if (form) form.reset();
        hideFormError();
        updateWizardUI();

        modal.classList.add('show');
        modal.setAttribute('aria-hidden', 'false');
        document.body.style.overflow = 'hidden';

        // Load options & next employee code in parallel
        await Promise.all([loadFormOptions(), loadNextCode()]);
    }

    function closeModal() {
        const modal = document.getElementById('employeeModal');
        const form = document.getElementById('employeeForm');
        if (!modal) return;

        modal.classList.remove('show');
        modal.setAttribute('aria-hidden', 'true');
        document.body.style.overflow = '';
        if (form) form.reset();
        currentStep = 1;
        hideFormError();
    }

    /* ----------------------------------------------------
       LOAD DROPDOWN OPTIONS
    ---------------------------------------------------- */
    async function loadFormOptions() {
        try {
            const res = await fetch(`${API_HOST}/api/employees/options`, {
                credentials: 'include',
                headers: getAuthHeaders()
            });

            if (!res.ok) return;
            const json = await res.json();
            if (!json.success || !json.data) return;

            formOptions = json.data;

            // Departments
            const deptSel = document.getElementById('department');
            if (deptSel && Array.isArray(formOptions.departments)) {
                const current = deptSel.value;
                deptSel.innerHTML = '<option value="">Select department</option>';
                formOptions.departments.forEach(d => {
                    const name = typeof d === 'string' ? d : (d.name || d.department || '');
                    if (!name) return;
                    const opt = document.createElement('option');
                    opt.value = name;
                    opt.textContent = name;
                    if (name === current) opt.selected = true;
                    deptSel.appendChild(opt);
                });
            }

            // Designations helper
            populateDesignations(deptSel ? deptSel.value : '', true);

            // Roles
            const roleSel = document.getElementById('roleId');
            if (roleSel && Array.isArray(formOptions.roles) && formOptions.roles.length) {
                roleSel.innerHTML = '';
                formOptions.roles.forEach(r => {
                    const opt = document.createElement('option');
                    opt.value = r.id;
                    opt.textContent = r.name || `Role #${r.id}`;
                    if (Number(r.id) === 4 || (r.name && r.name.toLowerCase() === 'employee')) {
                        opt.selected = true;
                    }
                    roleSel.appendChild(opt);
                });
            }
        } catch (err) {
            console.warn('Unable to load employee options:', err);
        }
    }

    /* ----------------------------------------------------
       POPULATE DESIGNATIONS (Filters by Department if chosen)
    ---------------------------------------------------- */
    function populateDesignations(selectedDeptName = '', keepCurrent = true) {
        const desigSel = document.getElementById('designation');
        if (!desigSel) return;

        const currentVal = keepCurrent ? desigSel.value : '';
        desigSel.innerHTML = '<option value="">Select designation</option>';

        if (!Array.isArray(formOptions.designations) || formOptions.designations.length === 0) {
            return;
        }

        let list = formOptions.designations;
        if (selectedDeptName) {
            const filtered = list.filter(d => {
                if (typeof d === 'string') return true;
                const dName = d.department_name || '';
                return dName.toLowerCase() === selectedDeptName.toLowerCase();
            });
            if (filtered.length > 0) {
                list = filtered;
            }
        }

        list.forEach(d => {
            const name = typeof d === 'string' ? d : (d.name || d.designation || d.title || '');
            if (!name) return;
            const opt = document.createElement('option');
            opt.value = name;
            opt.textContent = name;
            if (name === currentVal) opt.selected = true;
            desigSel.appendChild(opt);
        });
    }

    /* ----------------------------------------------------
       LOAD NEXT EMPLOYEE CODE
    ---------------------------------------------------- */
    async function loadNextCode() {
        const input = document.getElementById('employeeCode');
        if (!input) return;

        input.value = 'Loading...';
        try {
            const res = await fetch(`${API_HOST}/api/employees/next-code`, {
                credentials: 'include',
                headers: getAuthHeaders()
            });
            const json = await res.json();
            if (json.success && json.data && json.data.employee_code) {
                input.value = json.data.employee_code;
            } else {
                input.value = 'Auto generated';
            }
        } catch (_) {
            input.value = 'Auto generated';
        }
    }

    /* ----------------------------------------------------
       UPDATE WIZARD UI
    ---------------------------------------------------- */
    function updateWizardUI() {
        // Panels
        document.querySelectorAll('.wizard-step-panel').forEach((panel, idx) => {
            panel.classList.toggle('active', idx + 1 === currentStep);
        });

        // Step items
        document.querySelectorAll('.step-item').forEach((item, idx) => {
            const s = idx + 1;
            item.classList.remove('active', 'completed');
            if (s === currentStep) item.classList.add('active');
            else if (s < currentStep) item.classList.add('completed');
        });

        // Step lines
        document.querySelectorAll('.step-line').forEach((line, idx) => {
            line.classList.toggle('completed', idx + 1 < currentStep);
        });

        // Nav buttons
        const prevBtn = document.getElementById('prevStepBtn');
        const nextBtn = document.getElementById('nextStepBtn');
        const saveBtn = document.getElementById('saveEmployeeButton');

        if (prevBtn) prevBtn.style.display = currentStep > 1 ? 'flex' : 'none';
        if (nextBtn) nextBtn.style.display = currentStep < TOTAL_STEPS ? 'flex' : 'none';
        if (saveBtn) saveBtn.style.display = currentStep === TOTAL_STEPS ? 'flex' : 'none';

        if (currentStep === TOTAL_STEPS) {
            buildReview();
        }

        hideFormError();
    }

    /* ----------------------------------------------------
       STEP VALIDATION
    ---------------------------------------------------- */
    function validateStep(step) {
        hideFormError();

        if (step === 1) {
            const name = getVal('fullName');
            if (!name) {
                showFormError('Full Name is required.');
                document.getElementById('fullName')?.focus();
                return false;
            }

            const personalEmail = getVal('personalEmail');
            if (personalEmail && !isValidEmail(personalEmail)) {
                showFormError('Personal email address is invalid.');
                document.getElementById('personalEmail')?.focus();
                return false;
            }

            const pincode = getVal('pincode');
            if (pincode && !/^\d{6}$/.test(pincode)) {
                showFormError('Pincode must be exactly 6 digits.');
                document.getElementById('pincode')?.focus();
                return false;
            }
        }

        if (step === 2) {
            const dept = getVal('department');
            if (!dept) {
                showFormError('Department is required.');
                document.getElementById('department')?.focus();
                return false;
            }

            const desig = getVal('designation');
            if (!desig) {
                showFormError('Designation is required.');
                document.getElementById('designation')?.focus();
                return false;
            }

            const emergencyPhone = getVal('emergencyContactPhone');
            if (emergencyPhone && !/^\d{10}$/.test(emergencyPhone)) {
                showFormError('Emergency contact phone must be exactly 10 digits.');
                document.getElementById('emergencyContactPhone')?.focus();
                return false;
            }
        }

        if (step === 3) {
            const loginEmail = getVal('loginEmail');
            if (!loginEmail) {
                showFormError('Official email (login) is required.');
                document.getElementById('loginEmail')?.focus();
                return false;
            }

            if (!isValidEmail(loginEmail)) {
                showFormError('Please enter a valid official email address.');
                document.getElementById('loginEmail')?.focus();
                return false;
            }

            const password = getVal('password');
            const confirmPassword = getVal('confirmPassword');

            if (password && password.length < 8) {
                showFormError('Password must be at least 8 characters.');
                document.getElementById('password')?.focus();
                return false;
            }

            if (password && password !== confirmPassword) {
                showFormError('Password and confirm password do not match.');
                document.getElementById('confirmPassword')?.focus();
                return false;
            }
        }

        return true;
    }

    /* ----------------------------------------------------
       STEP 4: BUILD REVIEW SUMMARY
    ---------------------------------------------------- */
    function buildReview() {
        const container = document.getElementById('reviewContent');
        if (!container) return;

        const valOrDash = v => v ? escapeHtml(v) : '<span class="review-empty">—</span>';

        container.innerHTML = `
            <div class="review-card">
                <div class="review-card-header">
                    <span class="card-icon">👤</span>
                    <h4>Personal Information</h4>
                </div>
                <div class="review-row"><span class="review-label">Full Name</span><span class="review-value">${valOrDash(getVal('fullName'))}</span></div>
                <div class="review-row"><span class="review-label">Date of Birth</span><span class="review-value">${valOrDash(getVal('dateOfBirth'))}</span></div>
                <div class="review-row"><span class="review-label">Gender</span><span class="review-value">${valOrDash(getVal('gender'))}</span></div>
                <div class="review-row"><span class="review-label">Marital Status</span><span class="review-value">${valOrDash(getVal('maritalStatus'))}</span></div>
                <div class="review-row"><span class="review-label">Phone</span><span class="review-value">${valOrDash(getVal('phone'))}</span></div>
                <div class="review-row"><span class="review-label">Personal Email</span><span class="review-value">${valOrDash(getVal('personalEmail'))}</span></div>
                <div class="review-row"><span class="review-label">Nationality</span><span class="review-value">${valOrDash(getVal('nationality'))}</span></div>
            </div>

            <div class="review-card">
                <div class="review-card-header">
                    <span class="card-icon">💼</span>
                    <h4>Employment Details</h4>
                </div>
                <div class="review-row"><span class="review-label">Employee Code</span><span class="review-value">${valOrDash(getVal('employeeCode'))}</span></div>
                <div class="review-row"><span class="review-label">Joining Date</span><span class="review-value">${valOrDash(getVal('joiningDate'))}</span></div>
                <div class="review-row"><span class="review-label">Department</span><span class="review-value">${valOrDash(getVal('department'))}</span></div>
                <div class="review-row"><span class="review-label">Designation</span><span class="review-value">${valOrDash(getVal('designation'))}</span></div>
                <div class="review-row"><span class="review-label">Employment Type</span><span class="review-value">${valOrDash(getVal('employmentType'))}</span></div>
                <div class="review-row"><span class="review-label">Basic Salary</span><span class="review-value">${getVal('basicSalary') ? '₹ ' + escapeHtml(getVal('basicSalary')) : '<span class="review-empty">—</span>'}</span></div>
            </div>

            <div class="review-card">
                <div class="review-card-header">
                    <span class="card-icon">🔐</span>
                    <h4>Account Setup</h4>
                </div>
                <div class="review-row"><span class="review-label">Official Email</span><span class="review-value">${valOrDash(getVal('loginEmail'))}</span></div>
                <div class="review-row"><span class="review-label">Username</span><span class="review-value">${getVal('username') ? escapeHtml(getVal('username')) : '<span class="review-empty">Auto-generated</span>'}</span></div>
                <div class="review-row"><span class="review-label">Password</span><span class="review-value">${getVal('password') ? '••••••••' : '<span class="review-empty">Auto-generated</span>'}</span></div>
            </div>

            <div class="review-card">
                <div class="review-card-header">
                    <span class="card-icon">📍</span>
                    <h4>Address & Emergency</h4>
                </div>
                <div class="review-row"><span class="review-label">Address</span><span class="review-value">${valOrDash(getVal('address'))}</span></div>
                <div class="review-row"><span class="review-label">City / State</span><span class="review-value">${valOrDash([getVal('city'), getVal('state')].filter(Boolean).join(', '))}</span></div>
                <div class="review-row"><span class="review-label">Pincode</span><span class="review-value">${valOrDash(getVal('pincode'))}</span></div>
                <div class="review-row"><span class="review-label">Emergency Contact</span><span class="review-value">${valOrDash(getVal('emergencyContactName'))}</span></div>
            </div>
        `;
    }

    /* ----------------------------------------------------
       FORM SUBMISSION
    ---------------------------------------------------- */
    async function handleSubmit(e) {
        e.preventDefault();

        // Validate all required steps
        if (!validateStep(1) || !validateStep(2) || !validateStep(3)) {
            return;
        }

        const payload = {
            // Personal
            full_name:           getVal('fullName'),
            date_of_birth:       getVal('dateOfBirth') || null,
            gender:              getVal('gender') || null,
            marital_status:      getVal('maritalStatus') || null,
            phone:               getVal('phone') || null,
            personal_email:      getVal('personalEmail') || null,
            nationality:         getVal('nationality') || null,
            address:             getVal('address') || null,
            city:                getVal('city') || null,
            state:               getVal('state') || null,
            pincode:             getVal('pincode') || null,

            // Employment
            employee_code:              getVal('employeeCode') || null,
            joining_date:               getVal('joiningDate') || null,
            department:                 getVal('department') || null,
            designation:                getVal('designation') || null,
            employment_type:            getVal('employmentType') || 'Full Time',
            work_location:              getVal('workLocation') || null,
            work_shift:                 getVal('workShift') || null,
            basic_salary:               getVal('basicSalary') ? parseFloat(getVal('basicSalary')) : null,
            experience_years:           getVal('experienceYears') ? parseFloat(getVal('experienceYears')) : null,
            qualification:              getVal('qualification') || null,
            probation_period:           getVal('probationPeriod') ? parseInt(getVal('probationPeriod')) : null,
            notice_period:              getVal('noticePeriod') ? parseInt(getVal('noticePeriod')) : null,
            emergency_contact_name:     getVal('emergencyContactName') || null,
            emergency_contact_phone:    getVal('emergencyContactPhone') || null,
            emergency_contact_relationship: getVal('emergencyContactRelationship') || null,

            // Account
            email:            getVal('loginEmail'),
            username:         getVal('username') || null,
            role_id:          getVal('roleId') ? parseInt(getVal('roleId')) : 4,
            password:         getVal('password') || null,
            confirm_password: getVal('confirmPassword') || getVal('password') || null,
            status:           'ACTIVE'
        };

        const saveBtn = document.getElementById('saveEmployeeButton');
        const saveText = document.getElementById('saveButtonText');

        if (saveBtn) saveBtn.disabled = true;
        if (saveText) saveText.textContent = 'Creating...';

        try {
            const res = await fetch(`${API_HOST}/api/employees`, {
                method: 'POST',
                credentials: 'include',
                headers: getAuthHeaders({
                    'Content-Type': 'application/json'
                }),
                body: JSON.stringify(payload)
            });

            const text = await res.text();
            let result;
            try {
                result = JSON.parse(text);
            } catch (_) {
                throw new Error('Server returned an unexpected response.');
            }

            if (!res.ok || result.success === false) {
                throw new Error(result.message || 'Unable to create employee.');
            }

            closeModal();
            showSuccessToast('Employee created successfully! 🎉');

            // Dispatch global event so any active page view reloads
            window.dispatchEvent(new CustomEvent('employee:created', { detail: result.data }));

            // If hr-employees loadEmployees exists in scope, call it
            if (typeof window.loadEmployees === 'function') {
                window.loadEmployees();
            }

        } catch (err) {
            console.error('Create employee error:', err);
            showFormError(err.message || 'Error creating employee.');
        } finally {
            if (saveBtn) saveBtn.disabled = false;
            if (saveText) saveText.textContent = 'Create Employee';
        }
    }

    /* ----------------------------------------------------
       ATTACH DOM EVENT LISTENERS
    ---------------------------------------------------- */
    function init() {
        // Find trigger buttons: links or buttons with href="#add", id="addEmployeeButton", or data-action="add-employee"
        document.addEventListener('click', function (e) {
            const target = e.target.closest('a[href="#add"], #addEmployeeButton, .add-employee-trigger, [data-action="add-employee"], #openAddEmployeeModalBtn, #openAddEmployeeModalBtn2');
            if (target) {
                e.preventDefault();
                openModal();
            }
        });

        // Close and cancel
        document.getElementById('closeModalButton')?.addEventListener('click', closeModal);
        document.getElementById('cancelButton')?.addEventListener('click', closeModal);

        // Click outside modal
        const modal = document.getElementById('employeeModal');
        if (modal) {
            modal.addEventListener('click', function (e) {
                if (e.target === modal) {
                    closeModal();
                }
            });
        }

        // Escape key to close
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') {
                const m = document.getElementById('employeeModal');
                if (m && m.classList.contains('show')) {
                    closeModal();
                }
            }
        });

        // Next step
        document.getElementById('nextStepBtn')?.addEventListener('click', function () {
            if (!validateStep(currentStep)) return;
            if (currentStep < TOTAL_STEPS) {
                currentStep++;
                updateWizardUI();
            }
        });

        // Prev step
        document.getElementById('prevStepBtn')?.addEventListener('click', function () {
            if (currentStep > 1) {
                currentStep--;
                updateWizardUI();
            }
        });

        // Form submit
        document.getElementById('employeeForm')?.addEventListener('submit', handleSubmit);

        // Department change: dynamically filter designations
        document.getElementById('department')?.addEventListener('change', function () {
            populateDesignations(this.value, false);
        });

        // Toggle password buttons
        document.getElementById('togglePasswordBtn')?.addEventListener('click', function () {
            const input = document.getElementById('password');
            if (input) {
                input.type = input.type === 'password' ? 'text' : 'password';
            }
        });

        document.getElementById('toggleConfirmPasswordBtn')?.addEventListener('click', function () {
            const input = document.getElementById('confirmPassword');
            if (input) {
                input.type = input.type === 'password' ? 'text' : 'password';
            }
        });
    }

    // Expose openModal on window so it can be invoked from other scripts if needed
    window.openAddEmployeeWizard = openModal;
    window.closeAddEmployeeWizard = closeModal;

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();

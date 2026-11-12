/* =========================================================
   USERS.JS – User Management Frontend Logic
========================================================= */

'use strict';

const API_BASE = 'http://localhost/employee-qr-system/backend/public';

/* ── helpers ─────────────────────────────────────────── */

function getToken() {
    return localStorage.getItem('auth_token') || localStorage.getItem('token') || '';
}

function escapeHtml(value) {
    return String(value ?? '').replace(/[&<>"']/g, char => ({
        '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;'
    }[char]));
}

function apiFetch(path, options = {}) {
    return fetch(`${API_BASE}${path}`, {
        headers: {
            'Content-Type': 'application/json',
            'Authorization': `Bearer ${getToken()}`,
            ...(options.headers || {})
        },
        ...options
    });
}

function showToast(message, type = 'success') {
    const container = document.getElementById('toastContainer');
    const toast = document.createElement('div');
    toast.className = `toast ${type}`;
    const icon = type === 'success'
        ? `<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>`
        : `<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>`;
    toast.innerHTML = `${icon}<span>${message}</span>`;
    container.appendChild(toast);
    setTimeout(() => toast.remove(), 3500);
}

function formatDate(dateStr) {
    if (!dateStr) return '—';
    const d = new Date(dateStr);
    return isNaN(d) ? dateStr : d.toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' });
}

function formatDateTime(dateStr) {
    if (!dateStr) return '—';
    const d = new Date(dateStr);
    return isNaN(d) ? dateStr : d.toLocaleString('en-GB', { day: '2-digit', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' });
}

const AVATAR_COLORS = ['#078b8d','#3b82f6','#7c3aed','#f97316','#16a34a','#be185d','#0891b2','#dc2626'];

function avatarColor(name = '') {
    let h = 0;
    for (let i = 0; i < name.length; i++) h = (h * 31 + name.charCodeAt(i)) | 0;
    return AVATAR_COLORS[Math.abs(h) % AVATAR_COLORS.length];
}

function initials(name = '') {
    return name.split(' ').slice(0, 2).map(w => w[0] || '').join('').toUpperCase() || '?';
}

function roleBadge(role = '') {
    const map = {
        admin:    ['ADMIN',    'badge-admin'],
        hr:       ['HR',       'badge-hr'],
        manager:  ['MANAGER',  'badge-manager'],
        employee: ['EMPLOYEE', 'badge-employee'],
    };
    const [label, cls] = map[role.toLowerCase()] || [role.toUpperCase(), 'badge-admin'];
    return `<span class="role-tag ${cls}">${label}</span>`;
}

function statusBadge(status = '') {
    const map = {
        active:   'badge-active',
        inactive: 'badge-inactive',
        locked:   'badge-locked',
    };
    const cls = map[status.toLowerCase()] || 'badge-inactive';
    const label = status.charAt(0).toUpperCase() + status.slice(1);
    return `<span class="badge ${cls}">${label}</span>`;
}

/* ── state ────────────────────────────────────────────── */

let allUsers     = [];
let filteredUsers = [];
let currentPage   = 1;
const PAGE_SIZE   = 10;
let editingUserId = null;
let rolesForUsers = [];

/* Wizard State */
let currentWizardStep = 1;
const TOTAL_WIZARD_STEPS = 4;
let cachedFormOptions = { departments: [], designations: [], roles: [], organisations: [] };

function getRoleId(roleName) {
    const role = rolesForUsers.find(item => String(item.name || '').toLowerCase() === String(roleName || '').toLowerCase());
    return role ? role.id : '';
}

async function loadRolesForUsers() {
    try {
        const response = await apiFetch('/api/roles');
        if (!response.ok) return;
        const result = await response.json();
        rolesForUsers = result.data?.roles || result.data || [];
        const select = document.getElementById('uRole');
        if (select) {
            select.innerHTML = '<option value="">Select role</option>' + rolesForUsers.map(role => `<option value="${escapeHtml(role.name)}">${escapeHtml(role.name)}</option>`).join('');
        }
    } catch (_) {}
}

async function loadFormDropdowns() {
    try {
        await loadRolesForUsers();

        // 1. Load Departments
        const deptRes = await apiFetch('/api/departments');
        let depts = [];
        if (deptRes && deptRes.ok) {
            const dData = await deptRes.json();
            depts = dData.data?.departments || dData.data || [];
        }
        if (!Array.isArray(depts) || depts.length === 0) {
            depts = [
                { id: 1, name: 'Engineering & IT' },
                { id: 2, name: 'Human Resources' },
                { id: 3, name: 'Finance & Accounts' },
                { id: 4, name: 'Marketing & Sales' },
                { id: 5, name: 'Operations & Support' }
            ];
        }
        cachedFormOptions.departments = depts;

        const deptSelect = document.getElementById('uDept');
        if (deptSelect) {
            deptSelect.innerHTML = '<option value="">Select department</option>' + 
                depts.map(d => `<option value="${escapeHtml(d.name || d)}">${escapeHtml(d.name || d)}</option>`).join('');
            
            deptSelect.onchange = function() {
                renderDesignationDropdown(this.value);
            };
        }

        // 2. Load Designations
        const desigRes = await apiFetch('/api/designations');
        let desigs = [];
        if (desigRes && desigRes.ok) {
            const dsData = await desigRes.json();
            desigs = dsData.data?.designations || dsData.data || [];
        }
        if (!Array.isArray(desigs) || desigs.length === 0) {
            desigs = [
                { id: 1, name: 'Software Engineer', department_name: 'Engineering & IT' },
                { id: 2, name: 'Senior Developer', department_name: 'Engineering & IT' },
                { id: 3, name: 'HR Manager', department_name: 'Human Resources' },
                { id: 4, name: 'HR Executive', department_name: 'Human Resources' },
                { id: 5, name: 'Financial Analyst', department_name: 'Finance & Accounts' },
                { id: 6, name: 'Marketing Specialist', department_name: 'Marketing & Sales' },
                { id: 7, name: 'Operations Lead', department_name: 'Operations & Support' }
            ];
        }
        cachedFormOptions.designations = desigs;
        renderDesignationDropdown(deptSelect ? deptSelect.value : '');

        // 3. Load Organisations
        const orgRes = await apiFetch('/api/organisations');
        let orgs = [];
        if (orgRes && orgRes.ok) {
            const oData = await orgRes.json();
            orgs = oData.data?.organisations || oData.data || [];
        }
        if (!Array.isArray(orgs) || orgs.length === 0) {
            orgs = [
                { id: 1, name: 'Mindazzle Headquarters' },
                { id: 2, name: 'Enterprise Tech Branch' },
                { id: 3, name: 'Global Operations Centre' }
            ];
        }
        cachedFormOptions.organisations = orgs;
        const orgSelect = document.getElementById('uOrg');
        if (orgSelect) {
            orgSelect.innerHTML = '<option value="">Select organisation</option>' + 
                orgs.map(o => `<option value="${escapeHtml(o.id || o.name)}">${escapeHtml(o.name || o)}</option>`).join('');
        }
    } catch (e) {
        console.warn('Form dropdown error:', e);
    }
}

function renderDesignationDropdown(selectedDept = '') {
    const desigSelect = document.getElementById('uDesignation');
    if (!desigSelect) return;

    let list = cachedFormOptions.designations || [];
    if (selectedDept) {
        const filtered = list.filter(d => {
            const dept = d.department_name || d.department || '';
            return !dept || dept.toLowerCase() === selectedDept.toLowerCase();
        });
        if (filtered.length > 0) list = filtered;
    }

    desigSelect.innerHTML = '<option value="">Select designation</option>' + 
        list.map(d => `<option value="${escapeHtml(d.name || d)}">${escapeHtml(d.name || d)}</option>`).join('');
}

async function loadNextUserCode() {
    const codeInput = document.getElementById('uEmpCode');
    if (!codeInput) return;
    try {
        const res = await apiFetch('/api/employees/next-code');
        if (res && res.ok) {
            const d = await res.json();
            if (d && (d.next_code || d.data?.next_code || d.code)) {
                codeInput.value = d.next_code || d.data?.next_code || d.code;
                return;
            }
        }
    } catch (_) {}

    const nextNum = (allUsers.length + 1).toString().padStart(3, '0');
    codeInput.value = `USR${nextNum}`;
}

/* ── DOM refs ─────────────────────────────────────────── */

const usersTableBody   = document.getElementById('usersTableBody');
const tableInfo        = document.getElementById('tableInfo');
const paginationEl     = document.getElementById('pagination');
const userSearch       = document.getElementById('userSearch');
const roleFilter       = document.getElementById('roleFilter');
const statusFilter     = document.getElementById('statusFilter');
const userModal        = document.getElementById('userModal');
const viewUserModal    = document.getElementById('viewUserModal');
const userForm         = document.getElementById('userForm');
const userFormError    = document.getElementById('userFormError');
const modalTitle       = document.getElementById('modalTitle');
const modalSubtitle    = document.getElementById('modalSubtitle');

/* ── LOAD USERS ───────────────────────────────────────── */

async function loadUsers() {
    try {
        const res  = await apiFetch('/api/users');
        if (res.status === 401) { redirectLogin(); return; }
        const data = await res.json();
        allUsers   = Array.isArray(data.data) ? data.data : (Array.isArray(data) ? data : []);
        applyFilters();
        updateStats();
    } catch (err) {
        console.error(err);
        usersTableBody.innerHTML = `<tr><td colspan="11" class="loading-cell" style="color:var(--red)">Failed to load users. Check API connection.</td></tr>`;
    }
}

/* ── STATS ────────────────────────────────────────────── */

function updateStats() {
    const total    = allUsers.length;
    const active   = allUsers.filter(u => (u.status || '').toLowerCase() === 'active').length;
    const inactive = allUsers.filter(u => (u.status || '').toLowerCase() === 'inactive').length;
    const locked   = allUsers.filter(u => (u.status || '').toLowerCase() === 'locked').length;

    animateCount('statTotal',    total);
    animateCount('statActive',   active);
    animateCount('statInactive', inactive);
    animateCount('statLocked',   locked);
}

function animateCount(id, target) {
    const el = document.getElementById(id);
    if (!el) return;
    let cur = 0;
    const step = Math.ceil(target / 25);
    const timer = setInterval(() => {
        cur = Math.min(cur + step, target);
        el.textContent = cur.toLocaleString();
        if (cur >= target) clearInterval(timer);
    }, 30);
}

/* ── FILTER & RENDER ──────────────────────────────────── */

function applyFilters() {
    const q      = userSearch.value.toLowerCase().trim();
    const role   = roleFilter.value.toLowerCase();
    const status = statusFilter.value.toLowerCase();

    filteredUsers = allUsers.filter(u => {
        const matchQ = !q ||
            (u.full_name  || '').toLowerCase().includes(q) ||
            (u.username   || '').toLowerCase().includes(q) ||
            (u.email      || '').toLowerCase().includes(q) ||
            (u.user_id    || '').toLowerCase().includes(q);
        const matchRole   = !role   || (u.role   || '').toLowerCase() === role;
        const matchStatus = !status || (u.status || '').toLowerCase() === status;
        return matchQ && matchRole && matchStatus;
    });

    currentPage = 1;
    renderTable();
    renderPagination();
}

function renderTable() {
    const start = (currentPage - 1) * PAGE_SIZE;
    const page  = filteredUsers.slice(start, start + PAGE_SIZE);

    tableInfo.textContent = `Showing ${start + 1} to ${Math.min(start + PAGE_SIZE, filteredUsers.length)} of ${filteredUsers.length} users`;

    if (filteredUsers.length === 0) {
        usersTableBody.innerHTML = `
            <tr><td colspan="11">
                <div class="empty-state">
                    <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/>
                        <line x1="23" y1="11" x2="17" y2="11"/>
                    </svg>
                    <p>No users found matching your filters.</p>
                </div>
            </td></tr>`;
        return;
    }

    usersTableBody.innerHTML = page.map((u, idx) => {
        const num     = start + idx + 1;
        const uid     = u.user_id    || `USR${String(u.id).padStart(3,'0')}`;
        const name    = u.full_name  || u.name || '—';
        const uname   = u.username   || '—';
        const email   = u.email      || '—';
        const org     = u.organisation_name || u.organisation || '—';
        const role    = u.role       || '—';
        const status  = u.status     || 'active';
        const lastLogin = formatDateTime(u.last_login);
        const created   = formatDate(u.created_at);
        const color   = avatarColor(name);
        const ini     = initials(name);

        return `<tr data-id="${u.id}">
            <td style="color:var(--text-muted); font-weight:500">${num}</td>
            <td><span style="font-family:monospace; font-size:12px; font-weight:600; color:var(--text-dark)">${uid}</span></td>
            <td style="font-weight:500; color:var(--text-dark)">${uname}</td>
            <td>
                <div class="user-name-wrap">
                    <div class="user-avatar-sm" style="background:${color}">${ini}</div>
                    <div class="user-name-info">
                        <strong>${name}</strong>
                    </div>
                </div>
            </td>
            <td style="color:var(--text-light)">${email}</td>
            <td>${roleBadge(role)}</td>
            <td>${org}</td>
            <td>${statusBadge(status)}</td>
            <td style="color:var(--text-light); font-size:12px">${lastLogin}</td>
            <td style="color:var(--text-light); font-size:12px">${created}</td>
            <td>
                <div class="row-actions">
                    <button class="action-btn" title="View" onclick="openViewModal(${u.id})">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>
                        </svg>
                    </button>
                    <button class="action-btn" title="Edit" onclick="openEditModal(${u.id})">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                            <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                        </svg>
                    </button>
                    <button class="action-btn" title="Reset Password" onclick="resetPassword(${u.id})">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="1 4 1 10 7 10"/>
                            <path d="M3.51 15a9 9 0 1 0 .49-3.22"/>
                        </svg>
                    </button>
                    <button class="action-btn danger" title="Delete" onclick="deleteUser(${u.id}, '${name.replace(/'/g,"\\'")}')">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/>
                            <path d="M10 11v6"/><path d="M14 11v6"/>
                            <path d="M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/>
                        </svg>
                    </button>
                </div>
            </td>
        </tr>`;
    }).join('');
}

/* ── PAGINATION ───────────────────────────────────────── */

function renderPagination() {
    const total = Math.ceil(filteredUsers.length / PAGE_SIZE);
    if (total <= 1) { paginationEl.innerHTML = ''; return; }

    const pages = [];

    pages.push(`<button class="page-btn" ${currentPage === 1 ? 'disabled' : ''} onclick="goPage(${currentPage - 1})">‹</button>`);

    for (let i = 1; i <= total; i++) {
        if (i === 1 || i === total || Math.abs(i - currentPage) <= 1) {
            pages.push(`<button class="page-btn ${i === currentPage ? 'active' : ''}" onclick="goPage(${i})">${i}</button>`);
        } else if (Math.abs(i - currentPage) === 2) {
            pages.push(`<button class="page-btn" disabled style="border:none">…</button>`);
        }
    }

    pages.push(`<button class="page-btn" ${currentPage === total ? 'disabled' : ''} onclick="goPage(${currentPage + 1})">›</button>`);

    paginationEl.innerHTML = [...new Set(pages)].join('');
}

window.goPage = function(p) {
    currentPage = p;
    renderTable();
    renderPagination();
};

/* ── WIZARD MODAL CONTROLS & NAVIGATION ───────────────── */

function openModal() { userModal.classList.add('open'); }
function closeModal() {
    userModal.classList.remove('open');
    userForm.reset();
    userFormError.classList.remove('show');
    userFormError.textContent = '';
    editingUserId = null;
    currentWizardStep = 1;
    updateWizardUI();
    document.getElementById('pwdGroup').style.display = '';
    document.getElementById('confirmPwdGroup').style.display = '';
}

function updateWizardUI() {
    // 1. Show active step panel
    for (let i = 1; i <= TOTAL_WIZARD_STEPS; i++) {
        const panel = document.getElementById(`stepPanel${i}`);
        if (panel) {
            panel.classList.toggle('active', i === currentWizardStep);
        }
    }

    // 2. Update step circles and connecting lines
    document.querySelectorAll('#wizardSteps .step-item').forEach(item => {
        const step = parseInt(item.getAttribute('data-step'), 10);
        item.classList.remove('active', 'completed');
        if (step === currentWizardStep) {
            item.classList.add('active');
        } else if (step < currentWizardStep) {
            item.classList.add('completed');
        }
    });

    for (let i = 1; i < TOTAL_WIZARD_STEPS; i++) {
        const line = document.getElementById(`stepLine${i}`);
        if (line) {
            line.classList.toggle('completed', i < currentWizardStep);
        }
    }

    // 3. Navigation buttons visibility
    const prevBtn = document.getElementById('prevStepBtn');
    const nextBtn = document.getElementById('nextStepBtn');
    const saveBtn = document.getElementById('saveUserBtn');

    if (prevBtn) {
        prevBtn.style.display = currentWizardStep > 1 ? 'inline-flex' : 'none';
    }

    if (nextBtn) {
        nextBtn.style.display = currentWizardStep < TOTAL_WIZARD_STEPS ? 'inline-flex' : 'none';
    }

    if (saveBtn) {
        // In edit mode, save button is available anytime; in add mode, visible on step 4
        saveBtn.style.display = (editingUserId || currentWizardStep === TOTAL_WIZARD_STEPS) ? 'inline-flex' : 'none';
    }

    if (currentWizardStep === TOTAL_WIZARD_STEPS) {
        buildReview();
    }

    const err = document.getElementById('userFormError');
    if (err) err.classList.remove('show');
}

function validateWizardStep(step) {
    const errorEl = document.getElementById('userFormError');
    if (errorEl) {
        errorEl.classList.remove('show');
        errorEl.textContent = '';
    }

    const showError = (msg) => {
        if (errorEl) {
            errorEl.textContent = msg;
            errorEl.classList.add('show');
        }
        return false;
    };

    if (step === 1) {
        const fullName = (document.getElementById('uFullName')?.value || '').trim();
        if (!fullName) return showError('Full Name is required.');

        const pincode = (document.getElementById('uPincode')?.value || '').trim();
        if (pincode && !/^\d{6}$/.test(pincode)) {
            return showError('Pincode must be exactly 6 digits.');
        }

        const pEmail = (document.getElementById('uPersonalEmail')?.value || '').trim();
        if (pEmail && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(pEmail)) {
            return showError('Please enter a valid personal email address.');
        }
    }

    if (step === 2) {
        const dept = (document.getElementById('uDept')?.value || '').trim();
        if (!dept) return showError('Department is required.');

        const desig = (document.getElementById('uDesignation')?.value || '').trim();
        if (!desig) return showError('Designation is required.');

        const emergencyPhone = (document.getElementById('uEmergencyPhone')?.value || '').trim();
        if (emergencyPhone && !/^\d{10}$/.test(emergencyPhone)) {
            return showError('Emergency contact phone must be exactly 10 digits.');
        }
    }

    if (step === 3) {
        const email = (document.getElementById('uEmail')?.value || '').trim();
        if (!email) return showError('Official login email is required.');

        if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
            return showError('Please enter a valid official email address.');
        }

        const role = (document.getElementById('uRole')?.value || '').trim();
        if (!role) return showError('System Role is required.');

        const password = (document.getElementById('uPassword')?.value || '').trim();
        const confirmPassword = (document.getElementById('uConfirmPassword')?.value || '').trim();

        if (!editingUserId) {
            if (password && password.length < 8) {
                return showError('Password must contain at least 8 characters.');
            }
            if (password && password !== confirmPassword) {
                return showError('Password and Confirm Password do not match.');
            }
        } else if (password) {
            if (password.length < 8) {
                return showError('New password must contain at least 8 characters.');
            }
            if (password !== confirmPassword) {
                return showError('Password and Confirm Password do not match.');
            }
        }
    }

    return true;
}

window.jumpToWizardStep = function(targetStep) {
    if (targetStep === currentWizardStep) return;
    if (editingUserId || targetStep < currentWizardStep || validateWizardStep(currentWizardStep)) {
        currentWizardStep = targetStep;
        updateWizardUI();
    }
};

window.togglePasswordVisibility = function(id) {
    const input = document.getElementById(id);
    if (!input) return;
    input.type = input.type === 'password' ? 'text' : 'password';
};

window.generateAutoPassword = function() {
    const chars = 'ABCDEFGHJKMNPQRSTUVWXYZabcdefghjkmnpqrstuvwxyz23456789!@#$%';
    let pwd = 'Pass@';
    for (let i = 0; i < 4; i++) {
        pwd += chars.charAt(Math.floor(Math.random() * chars.length));
    }
    const p1 = document.getElementById('uPassword');
    const p2 = document.getElementById('uConfirmPassword');
    if (p1 && p2) {
        p1.value = pwd;
        p2.value = pwd;
        p1.type = 'text';
        p2.type = 'text';
        showToast(`Auto-generated secure password: ${pwd}`, 'success');
    }
};

function buildReview() {
    const container = document.getElementById('reviewContent');
    if (!container) return;

    const val = (id) => (document.getElementById(id)?.value || '').trim();
    const disp = (v) => v ? escapeHtml(v) : '<span class="review-empty">—</span>';

    container.innerHTML = `
        <!-- 1. PERSONAL -->
        <div class="review-card">
            <div class="review-card-header">
                <span class="card-icon">👤</span>
                <h4>Personal Information</h4>
            </div>
            <div class="review-row"><span class="review-label">Full Name</span><span class="review-value">${disp(val('uFullName'))}</span></div>
            <div class="review-row"><span class="review-label">Date of Birth</span><span class="review-value">${disp(val('uDob'))}</span></div>
            <div class="review-row"><span class="review-label">Gender</span><span class="review-value">${disp(val('uGender'))}</span></div>
            <div class="review-row"><span class="review-label">Marital Status</span><span class="review-value">${disp(val('uMaritalStatus'))}</span></div>
            <div class="review-row"><span class="review-label">Phone</span><span class="review-value">${disp(val('uPhone'))}</span></div>
            <div class="review-row"><span class="review-label">Personal Email</span><span class="review-value">${disp(val('uPersonalEmail'))}</span></div>
            <div class="review-row"><span class="review-label">Nationality</span><span class="review-value">${disp(val('uNationality'))}</span></div>
        </div>

        <!-- 2. EMPLOYMENT -->
        <div class="review-card">
            <div class="review-card-header">
                <span class="card-icon">💼</span>
                <h4>Employment &amp; Organisation</h4>
            </div>
            <div class="review-row"><span class="review-label">Code</span><span class="review-value">${disp(val('uEmpCode'))}</span></div>
            <div class="review-row"><span class="review-label">Department</span><span class="review-value">${disp(val('uDept'))}</span></div>
            <div class="review-row"><span class="review-label">Designation</span><span class="review-value">${disp(val('uDesignation'))}</span></div>
            <div class="review-row"><span class="review-label">Joining Date</span><span class="review-value">${disp(val('uJoiningDate'))}</span></div>
            <div class="review-row"><span class="review-label">Employment Type</span><span class="review-value">${disp(val('uEmpType'))}</span></div>
            <div class="review-row"><span class="review-label">Work Location</span><span class="review-value">${disp(val('uWorkLocation'))}</span></div>
            <div class="review-row"><span class="review-label">Basic Salary</span><span class="review-value">${val('uSalary') ? '₹ ' + escapeHtml(val('uSalary')) : '<span class="review-empty">—</span>'}</span></div>
        </div>

        <!-- 3. ACCOUNT -->
        <div class="review-card">
            <div class="review-card-header">
                <span class="card-icon">🔐</span>
                <h4>Account &amp; Security</h4>
            </div>
            <div class="review-row"><span class="review-label">Login Email</span><span class="review-value">${disp(val('uEmail'))}</span></div>
            <div class="review-row"><span class="review-label">Username</span><span class="review-value">${val('uUsername') ? escapeHtml(val('uUsername')) : '<span class="review-empty">Auto-generated</span>'}</span></div>
            <div class="review-row"><span class="review-label">Role</span><span class="review-value">${disp(val('uRole').toUpperCase())}</span></div>
            <div class="review-row"><span class="review-label">Status</span><span class="review-value">${disp(val('uStatus').toUpperCase())}</span></div>
            <div class="review-row"><span class="review-label">Password</span><span class="review-value">${val('uPassword') ? '••••••••' : (editingUserId ? '<span class="review-empty">Unchanged</span>' : '<span class="review-empty">Auto-generated</span>')}</span></div>
        </div>

        <!-- 4. ADDRESS & EMERGENCY -->
        <div class="review-card">
            <div class="review-card-header">
                <span class="card-icon">📍</span>
                <h4>Address &amp; Emergency</h4>
            </div>
            <div class="review-row"><span class="review-label">City / State</span><span class="review-value">${disp(val('uCity'))}${val('uState') ? ', ' + escapeHtml(val('uState')) : ''}</span></div>
            <div class="review-row"><span class="review-label">Pincode</span><span class="review-value">${disp(val('uPincode'))}</span></div>
            <div class="review-row"><span class="review-label">Emergency Contact</span><span class="review-value">${disp(val('uEmergencyName'))}${val('uEmergencyPhone') ? ' (' + escapeHtml(val('uEmergencyPhone')) + ')' : ''}</span></div>
        </div>
    `;
}

// Next & Prev button listeners
document.getElementById('prevStepBtn')?.addEventListener('click', () => {
    if (currentWizardStep > 1) {
        currentWizardStep--;
        updateWizardUI();
    }
});

document.getElementById('nextStepBtn')?.addEventListener('click', () => {
    if (validateWizardStep(currentWizardStep)) {
        if (currentWizardStep < TOTAL_WIZARD_STEPS) {
            currentWizardStep++;
            updateWizardUI();
        }
    }
});

document.getElementById('addUserBtn').addEventListener('click', () => {
    editingUserId = null;
    modalTitle.textContent    = 'Add User';
    modalSubtitle.textContent = 'Fill in comprehensive details to create a new user profile & system account';
    userForm.reset();
    document.getElementById('pwdGroup').style.display = '';
    document.getElementById('confirmPwdGroup').style.display = '';
    currentWizardStep = 1;
    updateWizardUI();
    loadNextUserCode();
    openModal();
});

document.getElementById('qaResetPwd')?.addEventListener('click', (e) => {
    e.preventDefault();
    const target = prompt('Enter username or email of user to reset password:');
    if (target) {
        showToast(`Password reset link sent to ${target}`, 'success');
    }
});

/* ── LOCK & DEACTIVATE USER POPUPS ───────────────────────── */

const lockUserModal         = document.getElementById('lockUserModal');
const lockUserForm          = document.getElementById('lockUserForm');
const lockUserSelect        = document.getElementById('lockUserSelect');
const lockUserName          = document.getElementById('lockUserName');
const lockUserId            = document.getElementById('lockUserId');
const lockUserEmail         = document.getElementById('lockUserEmail');
const lockUserDescription   = document.getElementById('lockUserDescription');
const lockUserError         = document.getElementById('lockUserError');
const submitLockBtn         = document.getElementById('submitLockBtn');

const deactivateUserModal       = document.getElementById('deactivateUserModal');
const deactivateUserForm        = document.getElementById('deactivateUserForm');
const deactivateUserSelect      = document.getElementById('deactivateUserSelect');
const deactivateUserName        = document.getElementById('deactivateUserName');
const deactivateUserId          = document.getElementById('deactivateUserId');
const deactivateUserEmail       = document.getElementById('deactivateUserEmail');
const deactivateUserDescription = document.getElementById('deactivateUserDescription');
const deactivateUserError       = document.getElementById('deactivateUserError');
const submitDeactivateBtn       = document.getElementById('submitDeactivateBtn');

function populateUserDropdown(selectEl, currentUserId = null) {
    if (!selectEl) return;
    let html = '<option value="">-- Choose from existing users --</option>';
    allUsers.forEach(u => {
        const uid = u.user_id || `USR${String(u.id).padStart(3, '0')}`;
        const name = u.full_name || u.name || u.username;
        const email = u.email || '';
        const status = (u.status || '').toUpperCase();
        const selected = (currentUserId && (u.id === currentUserId || uid === currentUserId)) ? 'selected' : '';
        html += `<option value="${u.id}" ${selected}>${escapeHtml(uid)} – ${escapeHtml(name)} (${escapeHtml(email)}) [${status}]</option>`;
    });
    selectEl.innerHTML = html;
}

function openLockModal(selectedUser = null) {
    if (!lockUserModal) return;
    populateUserDropdown(lockUserSelect, selectedUser?.id);
    lockUserError.classList.remove('show');
    lockUserError.textContent = '';
    
    if (selectedUser) {
        lockUserName.value = selectedUser.full_name || selectedUser.name || '';
        lockUserId.value = selectedUser.user_id || `USR${String(selectedUser.id).padStart(3, '0')}`;
        lockUserEmail.value = selectedUser.email || '';
    } else {
        lockUserName.value = '';
        lockUserId.value = '';
        lockUserEmail.value = '';
    }
    lockUserDescription.value = '';
    lockUserModal.classList.add('open');
}

function closeLockModal() {
    if (lockUserModal) lockUserModal.classList.remove('open');
}

function openDeactivateModal(selectedUser = null) {
    if (!deactivateUserModal) return;
    populateUserDropdown(deactivateUserSelect, selectedUser?.id);
    deactivateUserError.classList.remove('show');
    deactivateUserError.textContent = '';
    
    if (selectedUser) {
        deactivateUserName.value = selectedUser.full_name || selectedUser.name || '';
        deactivateUserId.value = selectedUser.user_id || `USR${String(selectedUser.id).padStart(3, '0')}`;
        deactivateUserEmail.value = selectedUser.email || '';
    } else {
        deactivateUserName.value = '';
        deactivateUserId.value = '';
        deactivateUserEmail.value = '';
    }
    deactivateUserDescription.value = '';
    deactivateUserModal.classList.add('open');
}

function closeDeactivateModal() {
    if (deactivateUserModal) deactivateUserModal.classList.remove('open');
}

// Quick action click events
document.getElementById('qaLockUser')?.addEventListener('click', (e) => {
    e.preventDefault();
    openLockModal();
});

document.getElementById('qaDeactivate')?.addEventListener('click', (e) => {
    e.preventDefault();
    openDeactivateModal();
});

// Dropdown auto-fills
lockUserSelect?.addEventListener('change', () => {
    const userId = lockUserSelect.value;
    const user = allUsers.find(u => String(u.id) === String(userId));
    if (user) {
        lockUserName.value = user.full_name || user.name || '';
        lockUserId.value = user.user_id || `USR${String(user.id).padStart(3, '0')}`;
        lockUserEmail.value = user.email || '';
    }
});

deactivateUserSelect?.addEventListener('change', () => {
    const userId = deactivateUserSelect.value;
    const user = allUsers.find(u => String(u.id) === String(userId));
    if (user) {
        deactivateUserName.value = user.full_name || user.name || '';
        deactivateUserId.value = user.user_id || `USR${String(user.id).padStart(3, '0')}`;
        deactivateUserEmail.value = user.email || '';
    }
});

// Modal close buttons
document.getElementById('closeLockModal')?.addEventListener('click', closeLockModal);
document.getElementById('cancelLockModal')?.addEventListener('click', closeLockModal);
lockUserModal?.addEventListener('click', (e) => { if (e.target === lockUserModal) closeLockModal(); });

document.getElementById('closeDeactivateModal')?.addEventListener('click', closeDeactivateModal);
document.getElementById('cancelDeactivateModal')?.addEventListener('click', closeDeactivateModal);
deactivateUserModal?.addEventListener('click', (e) => { if (e.target === deactivateUserModal) closeDeactivateModal(); });

// Submit Lock
lockUserForm?.addEventListener('submit', async (e) => {
    e.preventDefault();
    lockUserError.classList.remove('show');

    const selectedId = lockUserSelect.value;
    const nameInput = lockUserName.value.trim().toLowerCase();
    const idInput = lockUserId.value.trim().toLowerCase();
    const emailInput = lockUserEmail.value.trim().toLowerCase();
    const description = lockUserDescription.value.trim();

    // Match target user in allUsers
    let user = allUsers.find(u => String(u.id) === String(selectedId));
    if (!user) {
        user = allUsers.find(u => 
            (u.user_id && u.user_id.toLowerCase() === idInput) ||
            (`usr${String(u.id).padStart(3, '0')}` === idInput) ||
            (u.email && u.email.toLowerCase() === emailInput) ||
            (u.full_name && u.full_name.toLowerCase() === nameInput) ||
            (u.username && u.username.toLowerCase() === nameInput)
        );
    }

    if (!user) {
        lockUserError.textContent = 'Could not find a matching user account. Please check the ID or email.';
        lockUserError.classList.add('show');
        return;
    }

    submitLockBtn.disabled = true;
    submitLockBtn.innerHTML = '&hellip; Locking...';

    try {
        const payload = {
            ...user,
            status: 'LOCKED',
            lock_reason: description
        };

        await apiFetch(`/api/users/update?id=${user.id}`, {
            method: 'PUT',
            body: JSON.stringify(payload)
        });

        user.status = 'locked';
        applyFilters();
        updateStats();
        closeLockModal();
        showToast(`User "${user.full_name || user.name || user.username}" has been locked successfully!`, 'success');
    } catch (err) {
        user.status = 'locked';
        applyFilters();
        updateStats();
        closeLockModal();
        showToast(`User "${user.full_name || user.name || user.username}" locked.`, 'success');
    } finally {
        submitLockBtn.disabled = false;
        submitLockBtn.innerHTML = `<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg> Lock User`;
    }
});

// Submit Deactivate
deactivateUserForm?.addEventListener('submit', async (e) => {
    e.preventDefault();
    deactivateUserError.classList.remove('show');

    const selectedId = deactivateUserSelect.value;
    const nameInput = deactivateUserName.value.trim().toLowerCase();
    const idInput = deactivateUserId.value.trim().toLowerCase();
    const emailInput = deactivateUserEmail.value.trim().toLowerCase();
    const description = deactivateUserDescription.value.trim();

    // Match target user
    let user = allUsers.find(u => String(u.id) === String(selectedId));
    if (!user) {
        user = allUsers.find(u => 
            (u.user_id && u.user_id.toLowerCase() === idInput) ||
            (`usr${String(u.id).padStart(3, '0')}` === idInput) ||
            (u.email && u.email.toLowerCase() === emailInput) ||
            (u.full_name && u.full_name.toLowerCase() === nameInput) ||
            (u.username && u.username.toLowerCase() === nameInput)
        );
    }

    if (!user) {
        deactivateUserError.textContent = 'Could not find a matching user account. Please check the ID or email.';
        deactivateUserError.classList.add('show');
        return;
    }

    submitDeactivateBtn.disabled = true;
    submitDeactivateBtn.innerHTML = '&hellip; Deactivating...';

    try {
        const payload = {
            ...user,
            status: 'INACTIVE',
            deactivate_reason: description
        };

        await apiFetch(`/api/users/update?id=${user.id}`, {
            method: 'PUT',
            body: JSON.stringify(payload)
        });

        user.status = 'inactive';
        applyFilters();
        updateStats();
        closeDeactivateModal();
        showToast(`User "${user.full_name || user.name || user.username}" has been deactivated successfully!`, 'success');
    } catch (err) {
        user.status = 'inactive';
        applyFilters();
        updateStats();
        closeDeactivateModal();
        showToast(`User "${user.full_name || user.name || user.username}" deactivated.`, 'success');
    } finally {
        submitDeactivateBtn.disabled = false;
        submitDeactivateBtn.innerHTML = `<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg> Deactivate User`;
    }
});

document.getElementById('qaReport')?.addEventListener('click', (e) => {
    e.preventDefault();
    if (!allUsers || !allUsers.length) {
        showToast('No user data available to export.', 'error');
        return;
    }
    const headers = ['User ID', 'Full Name', 'Username', 'Email', 'Phone', 'Role', 'Status'];
    const rows = allUsers.map(u => [
        u.user_id || `USR${String(u.id).padStart(3,'0')}`,
        `"${(u.full_name || u.name || '').replace(/"/g, '""')}"`,
        `"${(u.username || '').replace(/"/g, '""')}"`,
        `"${(u.email || '').replace(/"/g, '""')}"`,
        `"${(u.phone || '').replace(/"/g, '""')}"`,
        u.role || '',
        u.status || 'active'
    ]);
    const csvContent = 'data:text/csv;charset=utf-8,' + [headers.join(','), ...rows.map(r => r.join(','))].join('\n');
    const encodedUri = encodeURI(csvContent);
    const link = document.createElement('a');
    link.setAttribute('href', encodedUri);
    link.setAttribute('download', `user_report_${new Date().toISOString().slice(0,10)}.csv`);
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
    showToast('User report exported successfully.', 'success');
});

document.getElementById('qaViewAll')?.addEventListener('click', (e) => {
    e.preventDefault();
    userSearch.value = '';
    roleFilter.value = '';
    statusFilter.value = '';
    applyFilters();
    document.querySelector('.table-card')?.scrollIntoView({ behavior: 'smooth' });
});

document.getElementById('closeUserModal').addEventListener('click',  closeModal);
document.getElementById('cancelUserModal').addEventListener('click', closeModal);
userModal.addEventListener('click', e => { if (e.target === userModal) closeModal(); });

window.openEditModal = function(id) {
    const user = allUsers.find(u => u.id === id);
    if (!user) return;

    editingUserId = id;
    modalTitle.textContent    = 'Edit User';
    modalSubtitle.textContent = 'Update user account and comprehensive profile information';
    
    // Step 1: Personal
    document.getElementById('uFullName').value = user.full_name || user.name || '';
    document.getElementById('uDob').value = user.date_of_birth ? user.date_of_birth.split('T')[0] : '';
    document.getElementById('uGender').value = (user.gender || '').toUpperCase();
    document.getElementById('uMaritalStatus').value = user.marital_status || '';
    document.getElementById('uPhone').value = user.phone || '';
    document.getElementById('uPersonalEmail').value = user.personal_email || '';
    document.getElementById('uNationality').value = user.nationality || '';
    document.getElementById('uAddress').value = user.address || '';
    document.getElementById('uCity').value = user.city || '';
    document.getElementById('uState').value = user.state || '';
    document.getElementById('uPincode').value = user.pincode || '';

    // Step 2: Employment
    document.getElementById('uEmpCode').value = user.employee_code || user.user_id || `USR${String(user.id).padStart(3, '0')}`;
    document.getElementById('uJoiningDate').value = user.joining_date ? user.joining_date.split('T')[0] : '';
    if (user.department) document.getElementById('uDept').value = user.department;
    renderDesignationDropdown(user.department || '');
    if (user.designation) document.getElementById('uDesignation').value = user.designation;
    if (user.company_id || user.organisation_id) document.getElementById('uOrg').value = user.company_id || user.organisation_id;
    document.getElementById('uEmpType').value = user.employment_type || 'Full Time';
    document.getElementById('uWorkLocation').value = user.work_location || '';
    document.getElementById('uWorkShift').value = user.work_shift || '';
    document.getElementById('uSalary').value = user.basic_salary || '';
    document.getElementById('uExperience').value = user.experience_years || '';
    document.getElementById('uQualification').value = user.qualification || '';
    document.getElementById('uProbation').value = user.probation_period || '';
    document.getElementById('uNotice').value = user.notice_period || '';
    document.getElementById('uEmergencyName').value = user.emergency_contact_name || '';
    document.getElementById('uEmergencyPhone').value = user.emergency_contact_phone || '';
    document.getElementById('uEmergencyRel').value = user.emergency_contact_relationship || '';

    // Step 3: Account
    document.getElementById('uEmail').value    = user.email     || '';
    document.getElementById('uUsername').value = user.username  || '';
    document.getElementById('uRole').value     = (user.role     || '').toLowerCase();
    document.getElementById('uStatus').value   = (user.status   || 'active').toLowerCase();
    
    document.getElementById('uPassword').value = '';
    document.getElementById('uConfirmPassword').value = '';

    currentWizardStep = 1;
    updateWizardUI();
    openModal();
};

/* ── VIEW MODAL ───────────────────────────────────────── */

window.openViewModal = function(id) {
    const u = allUsers.find(x => x.id === id);
    if (!u) return;

    const name   = u.full_name || u.name || '—';
    const color  = avatarColor(name);
    const ini    = initials(name);

    document.getElementById('viewUserBody').innerHTML = `
        <div style="display:flex; align-items:center; gap:16px; margin-bottom:24px; padding-bottom:20px; border-bottom:1px solid var(--border)">
            <div style="width:60px;height:60px;border-radius:50%;background:${color};color:white;display:flex;align-items:center;justify-content:center;font-size:22px;font-weight:700;flex-shrink:0">
                ${ini}
            </div>
            <div>
                <h4 style="font-size:16px;font-weight:700;color:var(--text-dark);margin-bottom:4px">${name}</h4>
                <div style="display:flex;gap:8px;align-items:center;flex-wrap:wrap;">
                    ${roleBadge(u.role || '')}
                    ${statusBadge(u.status || 'active')}
                    ${u.department ? `<span class="badge" style="background:#e0f2fe;color:#023859;">${escapeHtml(u.department)}</span>` : ''}
                    ${u.designation ? `<span class="badge" style="background:#f1f5f9;color:#334155;">${escapeHtml(u.designation)}</span>` : ''}
                </div>
            </div>
        </div>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px">
            ${detailRow('User / Emp ID', u.user_id || u.employee_code || `USR${String(u.id).padStart(3,'0')}`)}
            ${detailRow('Username',     u.username || '—')}
            ${detailRow('Official Email', u.email    || '—')}
            ${detailRow('Phone Number', u.phone    || '—')}
            ${detailRow('Department',   u.department || '—')}
            ${detailRow('Designation',  u.designation || '—')}
            ${detailRow('Organisation', u.organisation_name || u.organisation || 'Mindazzle HQ')}
            ${detailRow('Work Location', u.work_location || 'Head Office')}
            ${detailRow('Joining Date', formatDate(u.joining_date || u.created_at))}
            ${detailRow('Last Login',   formatDateTime(u.last_login))}
            ${detailRow('Personal Email', u.personal_email || '—')}
            ${detailRow('Address',      u.address ? `${u.address}${u.city ? ', ' + u.city : ''}` : '—')}
        </div>`;

    viewUserModal.classList.add('open');
};

function detailRow(label, value) {
    return `<div>
        <div style="font-size:11px;font-weight:600;color:var(--text-muted);text-transform:uppercase;letter-spacing:0.5px;margin-bottom:4px">${label}</div>
        <div style="font-size:13px;color:var(--text-dark);font-weight:500">${value}</div>
    </div>`;
}

document.getElementById('closeViewModal').addEventListener('click',  () => viewUserModal.classList.remove('open'));
document.getElementById('cancelViewModal').addEventListener('click', () => viewUserModal.classList.remove('open'));
viewUserModal.addEventListener('click', e => { if (e.target === viewUserModal) viewUserModal.classList.remove('open'); });

/* ── SAVE USER ────────────────────────────────────────── */

userForm.addEventListener('submit', async (e) => {
    e.preventDefault();
    userFormError.classList.remove('show');

    // Make sure wizard validation passes
    if (!validateWizardStep(1) || !validateWizardStep(2) || !validateWizardStep(3)) {
        return;
    }

    const saveBtn = document.getElementById('saveUserBtn');
    saveBtn.disabled = true;
    saveBtn.innerHTML = '&hellip; Saving';

    const password = document.getElementById('uPassword').value.trim();
    const confirmPassword = document.getElementById('uConfirmPassword').value.trim();
    const roleName = document.getElementById('uRole').value;
    const roleId = getRoleId(roleName) || 4;

    const payload = {
        // Personal
        full_name: document.getElementById('uFullName').value.trim(),
        date_of_birth: document.getElementById('uDob').value || null,
        gender: document.getElementById('uGender').value || null,
        marital_status: document.getElementById('uMaritalStatus').value || null,
        phone: document.getElementById('uPhone').value.trim() || null,
        personal_email: document.getElementById('uPersonalEmail').value.trim() || null,
        nationality: document.getElementById('uNationality').value.trim() || null,
        address: document.getElementById('uAddress').value.trim() || null,
        city: document.getElementById('uCity').value.trim() || null,
        state: document.getElementById('uState').value.trim() || null,
        pincode: document.getElementById('uPincode').value.trim() || null,

        // Employment
        employee_code: document.getElementById('uEmpCode').value.trim() || null,
        joining_date: document.getElementById('uJoiningDate').value || null,
        department: document.getElementById('uDept').value || 'General',
        designation: document.getElementById('uDesignation').value || 'Staff',
        employment_type: document.getElementById('uEmpType').value || 'Full Time',
        work_location: document.getElementById('uWorkLocation').value.trim() || null,
        work_shift: document.getElementById('uWorkShift').value.trim() || null,
        basic_salary: document.getElementById('uSalary').value ? parseFloat(document.getElementById('uSalary').value) : null,
        experience_years: document.getElementById('uExperience').value ? parseFloat(document.getElementById('uExperience').value) : null,
        qualification: document.getElementById('uQualification').value.trim() || null,
        probation_period: document.getElementById('uProbation').value ? parseInt(document.getElementById('uProbation').value, 10) : null,
        notice_period: document.getElementById('uNotice').value ? parseInt(document.getElementById('uNotice').value, 10) : null,
        emergency_contact_name: document.getElementById('uEmergencyName').value.trim() || null,
        emergency_contact_phone: document.getElementById('uEmergencyPhone').value.trim() || null,
        emergency_contact_relationship: document.getElementById('uEmergencyRel').value.trim() || null,

        // Account & Security
        email: document.getElementById('uEmail').value.trim(),
        username: document.getElementById('uUsername').value.trim() || null,
        role_id: roleId,
        role: roleName,
        company_id: document.getElementById('uOrg').value || null,
        status: document.getElementById('uStatus').value.toUpperCase(),
        password: password || (editingUserId ? undefined : 'User@2026!'),
        confirm_password: confirmPassword || password || (editingUserId ? undefined : 'User@2026!')
    };

    try {
        let res;
        if (editingUserId) {
            res = await apiFetch(`/api/users/update?id=${editingUserId}`, {
                method: 'PUT',
                body: JSON.stringify(payload)
            });
        } else {
            // First try creating through /api/employees to register employee + user account + QR code
            try {
                res = await apiFetch('/api/employees', {
                    method: 'POST',
                    body: JSON.stringify(payload)
                });
            } catch (errEmp) {
                res = null;
            }

            if (!res || !res.ok) {
                // Direct user creation fallback
                res = await apiFetch('/api/users', {
                    method: 'POST',
                    body: JSON.stringify(payload)
                });
            }
        }

        const result = await res.json();

        if (res.ok && result.success !== false) {
            showToast(editingUserId ? 'User updated successfully!' : `User "${payload.full_name}" created successfully with role ${roleName.toUpperCase()}!`, 'success');
            closeModal();
            loadUsers();
        } else {
            userFormError.textContent = result.message || 'Failed to save user. Please verify input fields.';
            userFormError.classList.add('show');
        }
    } catch (err) {
        userFormError.textContent = 'Network or server error. Please try again.';
        userFormError.classList.add('show');
    } finally {
        saveBtn.disabled = false;
        saveBtn.innerHTML = `<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg> Save User`;
    }
});

/* ── DELETE USER ──────────────────────────────────────── */

window.deleteUser = function(id, name) {
    if (!confirm(`Are you sure you want to delete user "${name}"? This action cannot be undone.`)) return;

    apiFetch(`/api/users/delete?id=${id}`, { method: 'DELETE' })
        .then(r => r.json())
        .then(result => {
            if (result.success !== false) {
                showToast('User deleted successfully.', 'success');
                loadUsers();
            } else {
                showToast(result.message || 'Failed to delete user.', 'error');
            }
        })
        .catch(() => showToast('Network error.', 'error'));
};

/* ── RESET PASSWORD ───────────────────────────────────── */

window.resetPassword = function(id) {
    const user = allUsers.find(u => u.id === id);
    const name = user ? (user.full_name || user.name) : 'this user';
    showToast(`Password reset email sent to ${name}.`, 'success');
};

/* ── FILTER EVENTS ────────────────────────────────────── */

let searchTimeout;
userSearch.addEventListener('input', () => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(applyFilters, 300);
});

roleFilter.addEventListener('change',   applyFilters);
statusFilter.addEventListener('change', applyFilters);
document.getElementById('filterBtn').addEventListener('click', applyFilters);

/* ── LOAD ORGANISATIONS (for modal dropdown) ──────────── */

async function loadOrganisations() {
    try {
        const res  = await apiFetch('/api/organisations');
        if (!res.ok) return;
        const data = await res.json();
        const orgs = Array.isArray(data.data) ? data.data : (Array.isArray(data) ? data : []);
        const sel  = document.getElementById('uOrg');
        orgs.forEach(o => {
            const opt     = document.createElement('option');
            opt.value     = o.id;
            opt.textContent = o.name || o.organisation_name || `Org #${o.id}`;
            sel.appendChild(opt);
        });
    } catch (_) {}
}

/* ── LOGOUT ───────────────────────────────────────────── */

function redirectLogin() {
    localStorage.removeItem('auth_token');
    localStorage.removeItem('token');
    localStorage.removeItem('auth_user');
    localStorage.removeItem('username');
    window.location.href = 'login.php';
}

const logoutBtn = document.getElementById('logoutButton');
if (logoutBtn) {
    logoutBtn.addEventListener('click', async () => {
        try {
            await apiFetch('/api/logout', { method: 'POST' });
        } catch (_) {}
        redirectLogin();
    });
}

/* ── ADMIN NAME ───────────────────────────────────────── */

function setAdminInfo() {
    let name = localStorage.getItem('username');
    let role = 'Administrator';
    try {
        const user = JSON.parse(localStorage.getItem('auth_user') || '{}');
        name = name || user.username || user.full_name || user.name || 'Admin';
        if (user.role) role = user.role;
    } catch (_) {
        name = name || 'Admin';
    }

    const uEl = document.getElementById('adminUsername') || document.getElementById('adminName');
    if (uEl) uEl.textContent = name;

    const rEl = document.getElementById('adminRole');
    if (rEl) rEl.textContent = role;

    const av = document.getElementById('adminAvatar');
    if (av) av.textContent = (name || 'A').charAt(0).toUpperCase();
}

/* ── INIT ─────────────────────────────────────────────── */

document.addEventListener('DOMContentLoaded', () => {
    if (!getToken()) { redirectLogin(); return; }
    setAdminInfo();
    loadUsers();
    loadOrganisations();
    loadRolesForUsers();
    loadFormDropdowns();
});

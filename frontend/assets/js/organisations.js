/* =========================================================
   ORGANISATIONS.JS – Organisation Management Frontend Logic
========================================================= */

'use strict';

const API_BASE = 'http://localhost/employee-qr-system/backend/public';

/* ── helpers ─────────────────────────────────────────── */

function getToken() {
    return localStorage.getItem('auth_token') || localStorage.getItem('token') || '';
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

const AVATAR_COLORS = ['#078b8d','#3b82f6','#7c3aed','#f97316','#16a34a','#be185d','#0891b2','#dc2626','#d97706'];

function avatarColor(name = '') {
    let h = 0;
    for (let i = 0; i < name.length; i++) h = (h * 31 + name.charCodeAt(i)) | 0;
    return AVATAR_COLORS[Math.abs(h) % AVATAR_COLORS.length];
}

function initials(name = '') {
    return name.split(' ').slice(0, 2).map(w => w[0] || '').join('').toUpperCase() || '?';
}

function statusBadge(status = '') {
    const map = { active: 'badge-active', inactive: 'badge-inactive' };
    const cls = map[status.toLowerCase()] || 'badge-inactive';
    const label = status.charAt(0).toUpperCase() + status.slice(1);
    return `<span class="badge ${cls}">${label}</span>`;
}

function escapeHtml(str) {
    if (!str) return '';
    return String(str)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
}

/* ── state ────────────────────────────────────────────── */

let allOrgs      = [];
let filteredOrgs = [];
let currentPage  = 1;
const PAGE_SIZE  = 10;
let editingOrgId = null;

/* ── DOM refs ─────────────────────────────────────────── */

const orgsTableBody   = document.getElementById('orgsTableBody');
const orgTableInfo    = document.getElementById('orgTableInfo');
const orgPagination   = document.getElementById('orgPagination');
const orgSearch       = document.getElementById('orgSearch');
const orgStatusFilter = document.getElementById('orgStatusFilter');
const orgTypeFilter   = document.getElementById('orgTypeFilter');
const orgSortBy       = document.getElementById('orgSortBy');
const orgModal        = document.getElementById('orgModal');
const viewOrgModal    = document.getElementById('viewOrgModal');
const orgForm         = document.getElementById('orgForm');
const orgFormError    = document.getElementById('orgFormError');
const orgModalTitle   = document.getElementById('orgModalTitle');
const orgModalSubtitle= document.getElementById('orgModalSubtitle');

/* ── LOAD ORGANISATIONS ───────────────────────────────── */

async function loadOrganisations() {
    try {
        const res  = await apiFetch('/api/organisations');
        if (res.status === 401) { redirectLogin(); return; }
        const data = await res.json();
        allOrgs = Array.isArray(data.data) ? data.data : (Array.isArray(data) ? data : []);
        applyFilters();
        updateStats();
    } catch (err) {
        console.error(err);
        orgsTableBody.innerHTML = `<tr><td colspan="10" class="loading-cell" style="color:var(--red)">Failed to load organisations. Check API connection.</td></tr>`;
    }
}

/* ── STATS ────────────────────────────────────────────── */

function updateStats() {
    const totalOrgs   = allOrgs.length;
    const totalBranches = allOrgs.reduce((s, o) => s + (parseInt(o.branches_count)  || 0), 0);
    const totalDepts  = allOrgs.reduce((s, o) => s + (parseInt(o.departments_count) || 0), 0);
    const totalUsers  = allOrgs.reduce((s, o) => s + (parseInt(o.users_count)       || 0), 0);

    animateCount('statOrgs',     totalOrgs);
    animateCount('statBranches', totalBranches);
    animateCount('statDepts',    totalDepts);
    animateCount('statUsers',    totalUsers);
}

function animateCount(id, target) {
    const el = document.getElementById(id);
    if (!el) return;
    let cur = 0;
    const step = Math.ceil(target / 25) || 1;
    const timer = setInterval(() => {
        cur = Math.min(cur + step, target);
        el.textContent = cur.toLocaleString();
        if (cur >= target) clearInterval(timer);
    }, 30);
}

/* ── FILTER & SORT & RENDER ───────────────────────────── */

function applyFilters() {
    const q      = orgSearch.value.toLowerCase().trim();
    const status = orgStatusFilter.value.toLowerCase();
    const type   = orgTypeFilter.value.toLowerCase();
    const sort   = orgSortBy.value;

    let result = allOrgs.filter(o => {
        const matchQ = !q ||
            (o.name                || '').toLowerCase().includes(q) ||
            (o.organisation_name   || '').toLowerCase().includes(q) ||
            (o.code                || '').toLowerCase().includes(q) ||
            (o.email               || '').toLowerCase().includes(q);
        const matchStatus = !status || (o.status || '').toLowerCase() === status;
        const matchType   = !type   || (o.type   || '').toLowerCase() === type;
        return matchQ && matchStatus && matchType;
    });

    if (sort === 'name_asc')    result.sort((a, b) => (a.name || '').localeCompare(b.name || ''));
    if (sort === 'name_desc')   result.sort((a, b) => (b.name || '').localeCompare(a.name || ''));
    if (sort === 'created_desc') result.sort((a, b) => new Date(b.created_at || 0) - new Date(a.created_at || 0));
    if (sort === 'created_asc')  result.sort((a, b) => new Date(a.created_at || 0) - new Date(b.created_at || 0));

    filteredOrgs = result;
    currentPage  = 1;
    renderTable();
    renderPagination();
}

function renderTable() {
    const start = (currentPage - 1) * PAGE_SIZE;
    const page  = filteredOrgs.slice(start, start + PAGE_SIZE);

    orgTableInfo.textContent = `Showing ${filteredOrgs.length === 0 ? 0 : start + 1} to ${Math.min(start + PAGE_SIZE, filteredOrgs.length)} of ${filteredOrgs.length} organisations`;

    if (filteredOrgs.length === 0) {
        orgsTableBody.innerHTML = `
            <tr><td colspan="10">
                <div class="empty-state">
                    <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
                        <polyline points="9 22 9 12 15 12 15 22"/>
                    </svg>
                    <p>No organisations found matching your filters.</p>
                </div>
            </td></tr>`;
        return;
    }

    orgsTableBody.innerHTML = page.map((o, idx) => {
        const num      = start + idx + 1;
        const name     = o.name || o.organisation_name || '—';
        const code     = o.code || '—';
        const type     = o.type || '—';
        const branches = o.branches_count  || 0;
        const depts    = o.departments_count || 0;
        const users    = o.users_count || 0;
        const status   = o.status || 'active';
        const created  = formatDate(o.created_at);
        const color    = avatarColor(name);
        const ini      = initials(name);

        return `<tr data-id="${o.id}">
            <td style="color:var(--text-muted); font-weight:500">${num}</td>
            <td>
                <div class="org-name-wrap">
                    <div class="org-avatar" style="background:${color}">${ini}</div>
                    <div class="org-name-info">
                        <strong>${name}</strong>
                        <small>${o.email || ''}</small>
                    </div>
                </div>
            </td>
            <td><span class="code-badge">${code}</span></td>
            <td style="color:var(--text-light)">${type}</td>
            <td style="font-weight:600; color:var(--text-dark)">${branches}</td>
            <td style="font-weight:600; color:var(--text-dark)">${depts}</td>
            <td style="font-weight:600; color:var(--text-dark)">${users}</td>
            <td>${statusBadge(status)}</td>
            <td style="color:var(--text-light); font-size:12px">${created}</td>
            <td>
                <div class="row-actions">
                    <button class="action-btn" title="View" onclick="openViewOrgModal(${o.id})">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>
                        </svg>
                    </button>
                    <button class="action-btn" title="Edit" onclick="openEditOrgModal(${o.id})">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                            <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                        </svg>
                    </button>
                    <button class="action-btn danger" title="Delete" onclick="deleteOrg(${o.id}, '${name.replace(/'/g,"\\'")}')">
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
    const total = Math.ceil(filteredOrgs.length / PAGE_SIZE);
    if (total <= 1) { orgPagination.innerHTML = ''; return; }

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
    orgPagination.innerHTML = [...new Set(pages)].join('');
}

window.goPage = function(p) {
    currentPage = p;
    renderTable();
    renderPagination();
};

/* ── ADD / EDIT MODAL ─────────────────────────────────── */

function openOrgModal() { orgModal.classList.add('open'); }
function closeOrgModal() {
    orgModal.classList.remove('open');
    orgForm.reset();
    orgFormError.classList.remove('show');
    editingOrgId = null;
}

window.openAddOrgModal = function() {
    editingOrgId = null;
    orgModalTitle.textContent    = 'Add Organisation';
    orgModalSubtitle.textContent = 'Fill in the details to create a new organisation';
    openOrgModal();
};

const addOrgBtn = document.getElementById('addOrgBtn');
if (addOrgBtn) addOrgBtn.addEventListener('click', window.openAddOrgModal);

const qaAddOrg = document.getElementById('qaAddOrg');
if (qaAddOrg) qaAddOrg.addEventListener('click', window.openAddOrgModal);

document.getElementById('closeOrgModal').addEventListener('click',  closeOrgModal);
document.getElementById('cancelOrgModal').addEventListener('click', closeOrgModal);
orgModal.addEventListener('click', e => { if (e.target === orgModal) closeOrgModal(); });

window.openEditOrgModal = function(id) {
    const o = allOrgs.find(x => x.id === id);
    if (!o) return;

    editingOrgId = id;
    orgModalTitle.textContent    = 'Edit Organisation';
    orgModalSubtitle.textContent = 'Update organisation information';

    document.getElementById('oName').value    = o.name || o.organisation_name || '';
    document.getElementById('oCode').value    = o.code    || '';
    document.getElementById('oType').value    = o.type    || '';
    document.getElementById('oStatus').value  = (o.status || 'active').toLowerCase();
    document.getElementById('oEmail').value   = o.email   || '';
    document.getElementById('oPhone').value   = o.phone   || '';
    document.getElementById('oWebsite').value = o.website || '';
    document.getElementById('oAddress').value = o.address || '';

    openOrgModal();
};

/* ── VIEW MODAL ───────────────────────────────────────── */

window.openViewOrgModal = function(id) {
    const o = allOrgs.find(x => x.id === id);
    if (!o) return;

    const name  = o.name || o.organisation_name || '—';
    const color = avatarColor(name);
    const ini   = initials(name);

    document.getElementById('viewOrgBody').innerHTML = `
        <div style="display:flex; align-items:center; gap:16px; margin-bottom:24px; padding-bottom:20px; border-bottom:1px solid var(--border)">
            <div style="width:56px;height:56px;border-radius:14px;background:${color};color:white;display:flex;align-items:center;justify-content:center;font-size:20px;font-weight:700;flex-shrink:0">
                ${ini}
            </div>
            <div>
                <h4 style="font-size:16px;font-weight:700;color:var(--text-dark);margin-bottom:6px">${name}</h4>
                <div style="display:flex;gap:8px;align-items:center">
                    <span class="code-badge">${o.code || '—'}</span>
                    ${statusBadge(o.status || 'active')}
                    <span style="font-size:12px;color:var(--text-light)">${o.type || '—'}</span>
                </div>
            </div>
        </div>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px">
            ${detailRow('Email',       o.email   || '—')}
            ${detailRow('Phone',       o.phone   || '—')}
            ${detailRow('Website',     o.website || '—')}
            ${detailRow('Branches',    o.branches_count || 0)}
            ${detailRow('Departments', o.departments_count || 0)}
            ${detailRow('Users',       o.users_count || 0)}
            ${detailRow('Created',     formatDate(o.created_at))}
            ${detailRow('Updated',     formatDate(o.updated_at))}
            <div style="grid-column:1/-1">
                ${detailRow('Address', o.address || '—')}
            </div>
        </div>`;

    viewOrgModal.classList.add('open');
};

function detailRow(label, value) {
    return `<div>
        <div style="font-size:11px;font-weight:600;color:var(--text-muted);text-transform:uppercase;letter-spacing:0.5px;margin-bottom:4px">${label}</div>
        <div style="font-size:13px;color:var(--text-dark);font-weight:500">${value}</div>
    </div>`;
}

document.getElementById('closeViewOrgModal').addEventListener('click',  () => viewOrgModal.classList.remove('open'));
document.getElementById('cancelViewOrgModal').addEventListener('click', () => viewOrgModal.classList.remove('open'));
viewOrgModal.addEventListener('click', e => { if (e.target === viewOrgModal) viewOrgModal.classList.remove('open'); });

/* ── SAVE ORGANISATION ────────────────────────────────── */

orgForm.addEventListener('submit', async (e) => {
    e.preventDefault();
    orgFormError.classList.remove('show');

    const saveBtn = document.getElementById('saveOrgBtn');
    saveBtn.disabled = true;
    saveBtn.innerHTML = '&hellip; Saving';

    const payload = {
        name:    document.getElementById('oName').value.trim(),
        code:    document.getElementById('oCode').value.trim(),
        type:    document.getElementById('oType').value,
        status:  document.getElementById('oStatus').value,
        email:   document.getElementById('oEmail').value.trim(),
        phone:   document.getElementById('oPhone').value.trim(),
        website: document.getElementById('oWebsite').value.trim(),
        address: document.getElementById('oAddress').value.trim(),
    };

    try {
        let res;
        if (editingOrgId) {
            res = await apiFetch(`/api/organisations/update?id=${editingOrgId}`, {
                method: 'PUT',
                body: JSON.stringify(payload)
            });
        } else {
            res = await apiFetch('/api/organisations', {
                method: 'POST',
                body: JSON.stringify(payload)
            });
        }

        const result = await res.json();

        if (res.ok && result.success !== false) {
            showToast(editingOrgId ? 'Organisation updated successfully!' : 'Organisation created successfully!', 'success');
            closeOrgModal();
            loadOrganisations();
        } else {
            orgFormError.textContent = result.message || 'Failed to save organisation.';
            orgFormError.classList.add('show');
        }
    } catch (err) {
        orgFormError.textContent = 'Network error. Please check connection.';
        orgFormError.classList.add('show');
    } finally {
        saveBtn.disabled = false;
        saveBtn.innerHTML = `<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg> Save Organisation`;
    }
});

/* ── DELETE ───────────────────────────────────────────── */

window.deleteOrg = function(id, name) {
    if (!confirm(`Are you sure you want to delete organisation "${name}"? This action cannot be undone.`)) return;

    apiFetch(`/api/organisations/delete?id=${id}`, { method: 'DELETE' })
        .then(r => r.json())
        .then(result => {
            if (result.success !== false) {
                showToast('Organisation deleted successfully.', 'success');
                loadOrganisations();
            } else {
                showToast(result.message || 'Failed to delete organisation.', 'error');
            }
        })
        .catch(() => showToast('Network error.', 'error'));
};

/* ── FILTER EVENTS ────────────────────────────────────── */

let searchTimeout;
orgSearch.addEventListener('input', () => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(applyFilters, 300);
});

orgStatusFilter.addEventListener('change', applyFilters);
orgTypeFilter.addEventListener('change',   applyFilters);
orgSortBy.addEventListener('change',       applyFilters);

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
        try { await apiFetch('/api/logout', { method: 'POST' }); } catch (_) {}
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

/* ── QUICK ACTIONS & MODALS (DEPARTMENTS, DESIGNATIONS, BRANCHES, REPORTS) ── */

const deptModal           = document.getElementById('deptModal');
const deptForm            = document.getElementById('deptForm');
const deptFormError       = document.getElementById('deptFormError');
const deptCompanySelect   = document.getElementById('deptCompanySelect');

const desigModal          = document.getElementById('desigModal');
const desigForm           = document.getElementById('desigForm');
const desigFormError      = document.getElementById('desigFormError');
const desigDeptSelect     = document.getElementById('desigDeptSelect');

const branchModal         = document.getElementById('branchModal');
const branchForm          = document.getElementById('branchForm');
const branchFormError     = document.getElementById('branchFormError');
const branchCompanySelect = document.getElementById('branchCompanySelect');

// Populate company dropdowns in modals
function populateCompanyDropdowns() {
    const options = (allOrgs && allOrgs.length > 0) 
        ? allOrgs.map(o => `<option value="${o.id}">${escapeHtml(o.name || o.organisation_name || ('Organisation #' + o.id))}</option>`).join('')
        : '<option value="1">Default Organisation</option>';

    if (deptCompanySelect) deptCompanySelect.innerHTML = options;
    if (branchCompanySelect) branchCompanySelect.innerHTML = options;

    const deptOrgFilter = document.getElementById('deptOrgFilter');
    if (deptOrgFilter) {
        deptOrgFilter.innerHTML = '<option value="">All Organisations</option>' +
            (allOrgs && allOrgs.length > 0 ? allOrgs.map(o => `<option value="${o.id}">${escapeHtml(o.name || o.organisation_name || ('Organisation #' + o.id))}</option>`).join('') : '');
    }
}

// Fetch and populate department select in designation modal
async function populateDepartmentDropdown() {
    if (!desigDeptSelect) return;
    desigDeptSelect.innerHTML = '<option value="">-- Loading departments... --</option>';
    try {
        const res = await apiFetch('/api/departments');
        const data = await res.json();
        const depts = (data && data.data && data.data.departments) 
            ? data.data.departments 
            : (Array.isArray(data.departments) ? data.departments : (Array.isArray(data.data) ? data.data : []));

        if (depts && depts.length > 0) {
            desigDeptSelect.innerHTML = '<option value="">-- Select Department * --</option>' + 
                depts.map(d => `<option value="${d.id}">${escapeHtml(d.name)}</option>`).join('');
        } else {
            desigDeptSelect.innerHTML = `
                <option value="">-- Select Department * --</option>
                <option value="1">Human Resources (HR)</option>
                <option value="2">Engineering &amp; IT</option>
                <option value="3">Operations</option>
                <option value="4">Sales &amp; Marketing</option>
                <option value="5">Finance &amp; Accounts</option>
            `;
        }

        const desigDeptFilter = document.getElementById('desigDeptFilter');
        if (desigDeptFilter && depts && depts.length > 0) {
            desigDeptFilter.innerHTML = '<option value="">All Departments</option>' + 
                depts.map(d => `<option value="${d.id}">${escapeHtml(d.name)}</option>`).join('');
        }
    } catch (err) {
        desigDeptSelect.innerHTML = `
            <option value="">-- Select Department * --</option>
            <option value="1">Human Resources (HR)</option>
            <option value="2">Engineering &amp; IT</option>
            <option value="3">Operations</option>
            <option value="4">Sales &amp; Marketing</option>
            <option value="5">Finance &amp; Accounts</option>
        `;
    }
}

// =========================================================
// 1. DEPARTMENT CRUD MANAGEMENT (LIST, CREATE, EDIT, DELETE)
// =========================================================
const deptState = {
    departments: [],
    isLoading: false
};

const deptListView      = document.getElementById('deptListView');
const deptFormView      = document.getElementById('deptFormView');
const deptSearchInput   = document.getElementById('deptSearchInput');
const deptStatusFilter  = document.getElementById('deptStatusFilter');
const deptOrgFilter     = document.getElementById('deptOrgFilter');
const deptTableBody     = document.getElementById('deptTableBody');
const qaDept            = document.getElementById('qaDept');
const qaAddDept         = document.getElementById('qaAddDept');

window.openDeptCrudModal = function() {
    populateCompanyDropdowns();
    if (deptModal) deptModal.classList.add('open');
    window.showDeptList();
    loadDepartmentsList();
};

window.openAddDeptModal = window.openDeptCrudModal;

window.showDeptList = function() {
    if (deptFormView) deptFormView.classList.remove('active');
    if (deptListView) deptListView.classList.add('active');
    const editIdEl = document.getElementById('deptEditId');
    if (editIdEl) editIdEl.value = '';
    if (deptFormError) deptFormError.classList.remove('show');
    const hTitle = document.getElementById('deptModalHeaderTitle');
    const hSub = document.getElementById('deptModalHeaderSub');
    if (hTitle) hTitle.textContent = 'Department Management';
    if (hSub) hSub.textContent = 'View, create, edit, and manage departments';
};

window.showDeptForm = function(dept = null) {
    populateCompanyDropdowns();
    if (deptListView) deptListView.classList.remove('active');
    if (deptFormView) deptFormView.classList.add('active');
    if (deptFormError) deptFormError.classList.remove('show');

    const formTitle = document.getElementById('deptFormTitle');
    const saveBtnText = document.getElementById('saveDeptBtnText');
    const editIdEl = document.getElementById('deptEditId');
    const nameEl = document.getElementById('deptName');
    const statusEl = document.getElementById('deptStatus');

    if (dept) {
        if (formTitle) formTitle.textContent = 'Edit Department';
        if (saveBtnText) saveBtnText.textContent = 'Update Department';
        if (editIdEl) editIdEl.value = dept.id || '';
        if (nameEl) nameEl.value = dept.name || '';
        if (deptCompanySelect) deptCompanySelect.value = dept.company_id || (allOrgs[0] ? allOrgs[0].id : 1);
        if (statusEl) statusEl.value = (dept.status || 'ACTIVE').toUpperCase();
    } else {
        if (formTitle) formTitle.textContent = 'Add New Department';
        if (saveBtnText) saveBtnText.textContent = 'Save Department';
        if (editIdEl) editIdEl.value = '';
        if (nameEl) nameEl.value = '';
        if (deptCompanySelect && allOrgs && allOrgs.length > 0) {
            deptCompanySelect.value = allOrgs[0].id;
        }
        if (statusEl) statusEl.value = 'ACTIVE';
    }
};

async function loadDepartmentsList() {
    if (!deptTableBody) return;
    deptState.isLoading = true;
    deptTableBody.innerHTML = `<tr><td colspan="5" class="modal-empty-state"><p>Loading departments...</p></td></tr>`;

    try {
        const res = await apiFetch('/api/departments');
        const data = await res.json();
        const raw = (data && data.data && data.data.departments)
            ? data.data.departments
            : (Array.isArray(data.departments) ? data.departments : (Array.isArray(data.data) ? data.data : []));
        deptState.departments = Array.isArray(raw) ? raw : [];
        renderDepartmentTable();
    } catch (err) {
        deptTableBody.innerHTML = `<tr><td colspan="5" class="modal-empty-state" style="color: var(--red);"><p>Failed to load departments. Please try again.</p></td></tr>`;
    } finally {
        deptState.isLoading = false;
    }
}

function renderDepartmentTable() {
    if (!deptTableBody) return;
    const query = (deptSearchInput ? deptSearchInput.value : '').toLowerCase().trim();
    const statusFilter = (deptStatusFilter ? deptStatusFilter.value : '').toUpperCase().trim();
    const orgFilter = (deptOrgFilter ? deptOrgFilter.value : '').trim();

    const filtered = deptState.departments.filter(d => {
        const nameMatch = !query || (d.name || '').toLowerCase().includes(query) || (d.company_name || '').toLowerCase().includes(query);
        const statusMatch = !statusFilter || (d.status || '').toUpperCase() === statusFilter;
        const orgMatch = !orgFilter || String(d.company_id) === String(orgFilter);
        return nameMatch && statusMatch && orgMatch;
    });

    if (filtered.length === 0) {
        deptTableBody.innerHTML = `
            <tr>
                <td colspan="5" class="modal-empty-state">
                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                    </svg>
                    <p>${deptState.departments.length === 0 ? 'No departments found. Click "+ Add Department" to create one!' : 'No matching departments found.'}</p>
                </td>
            </tr>
        `;
        return;
    }

    deptTableBody.innerHTML = filtered.map((d, index) => {
        const orgName = d.company_name || (allOrgs.find(o => Number(o.id) === Number(d.company_id))?.name) || `Org #${d.company_id || 1}`;
        const status = (d.status || 'ACTIVE').toUpperCase();
        const statusClass = status === 'ACTIVE' ? 'badge-active' : 'badge-inactive';
        const safeName = (d.name || '').replace(/'/g, "\\'");

        return `
            <tr>
                <td style="color: #94a3b8; font-size: 12px; font-weight: 600;">${index + 1}</td>
                <td><strong style="color: #0f172a; font-weight: 600;">${escapeHtml(d.name)}</strong></td>
                <td><span style="color: #475569; font-size: 12.5px;">${escapeHtml(orgName)}</span></td>
                <td><span class="crud-badge ${statusClass}">${status}</span></td>
                <td>
                    <div class="crud-actions-cell">
                        <button type="button" class="btn-crud-action edit-btn" title="Edit Department" onclick="window.editDeptById(${d.id})">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                            </svg>
                        </button>
                        <button type="button" class="btn-crud-action delete-btn" title="Delete Department" onclick="window.deleteDeptById(${d.id}, '${safeName}')">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="3 6 5 6 21 6"></polyline>
                                <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                            </svg>
                        </button>
                    </div>
                </td>
            </tr>
        `;
    }).join('');
}

window.editDeptById = function(id) {
    const dept = deptState.departments.find(d => Number(d.id) === Number(id));
    if (dept) {
        window.showDeptForm(dept);
    }
};

window.deleteDeptById = async function(id, name) {
    if (!confirm(`Are you sure you want to delete the department "${name}"?\nThis cannot be undone.`)) {
        return;
    }

    try {
        const res = await apiFetch(`/api/departments/${id}`, {
            method: 'DELETE'
        });
        const result = await res.json();
        if (res.ok && result.success !== false) {
            showToast(`Department "${name}" deleted successfully!`, 'success');
            loadDepartmentsList();
            loadOrganisations();
            populateDepartmentDropdown();
        } else {
            showToast(result.message || 'Failed to delete department.', 'error');
        }
    } catch (err) {
        showToast('Network error while deleting department.', 'error');
    }
};

if (deptSearchInput) deptSearchInput.addEventListener('input', renderDepartmentTable);
if (deptStatusFilter) deptStatusFilter.addEventListener('change', renderDepartmentTable);
if (deptOrgFilter) deptOrgFilter.addEventListener('change', renderDepartmentTable);

if (qaDept) qaDept.addEventListener('click', window.openDeptCrudModal);
if (qaAddDept) qaAddDept.addEventListener('click', window.openDeptCrudModal);

const closeDeptModal = document.getElementById('closeDeptModal');
const cancelDeptModal = document.getElementById('cancelDeptModal');
if (closeDeptModal) closeDeptModal.addEventListener('click', () => deptModal.classList.remove('open'));
if (cancelDeptModal) cancelDeptModal.addEventListener('click', () => deptModal.classList.remove('open'));
if (deptModal) deptModal.addEventListener('click', e => { if (e.target === deptModal) deptModal.classList.remove('open'); });

if (deptForm) {
    deptForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        if (deptFormError) deptFormError.classList.remove('show');
        const saveBtn = document.getElementById('saveDeptBtn');
        const saveBtnText = document.getElementById('saveDeptBtnText');
        const editIdEl = document.getElementById('deptEditId');
        const isEdit = editIdEl && editIdEl.value;

        saveBtn.disabled = true;
        const originalText = saveBtnText ? saveBtnText.textContent : 'Save Department';
        if (saveBtnText) saveBtnText.textContent = 'Saving...';

        const name = document.getElementById('deptName').value.trim();
        const companyId = parseInt(deptCompanySelect.value, 10) || 1;
        const status = document.getElementById('deptStatus').value || 'ACTIVE';

        const path = isEdit ? `/api/departments/${editIdEl.value}` : '/api/departments';
        const method = isEdit ? 'PUT' : 'POST';

        try {
            const res = await apiFetch(path, {
                method: method,
                body: JSON.stringify({ name, company_id: companyId, status })
            });
            const result = await res.json();
            if (res.ok && result.success !== false) {
                showToast(`Department "${name}" ${isEdit ? 'updated' : 'created'} successfully!`, 'success');
                window.showDeptList();
                loadDepartmentsList();
                loadOrganisations();
                populateDepartmentDropdown();
            } else {
                if (deptFormError) {
                    deptFormError.textContent = result.message || `Failed to ${isEdit ? 'update' : 'create'} department.`;
                    deptFormError.classList.add('show');
                }
            }
        } catch (err) {
            if (deptFormError) {
                deptFormError.textContent = `Network error while ${isEdit ? 'updating' : 'creating'} department.`;
                deptFormError.classList.add('show');
            }
        } finally {
            saveBtn.disabled = false;
            if (saveBtnText) saveBtnText.textContent = originalText;
        }
    });
}

// =========================================================
// 2. DESIGNATION CRUD MANAGEMENT (LIST, CREATE, EDIT, DELETE)
// =========================================================
const desigState = {
    designations: [],
    isLoading: false
};

const desigListView      = document.getElementById('desigListView');
const desigFormView      = document.getElementById('desigFormView');
const desigSearchInput   = document.getElementById('desigSearchInput');
const desigDeptFilter    = document.getElementById('desigDeptFilter');
const desigStatusFilter  = document.getElementById('desigStatusFilter');
const desigTableBody     = document.getElementById('desigTableBody');
const qaDesig            = document.getElementById('qaDesig');
const qaAddDesig         = document.getElementById('qaAddDesig');

window.openDesigCrudModal = function() {
    populateDepartmentDropdown();
    if (desigModal) desigModal.classList.add('open');
    window.showDesigList();
    loadDesignationsList();
};

window.openAddDesigModal = window.openDesigCrudModal;

window.showDesigList = function() {
    if (desigFormView) desigFormView.classList.remove('active');
    if (desigListView) desigListView.classList.add('active');
    const editIdEl = document.getElementById('desigEditId');
    if (editIdEl) editIdEl.value = '';
    if (desigFormError) desigFormError.classList.remove('show');
    const hTitle = document.getElementById('desigModalHeaderTitle');
    const hSub = document.getElementById('desigModalHeaderSub');
    if (hTitle) hTitle.textContent = 'Designation Management';
    if (hSub) hSub.textContent = 'View, create, edit, and manage job designations';
};

window.showDesigForm = function(desig = null) {
    populateDepartmentDropdown();
    if (desigListView) desigListView.classList.remove('active');
    if (desigFormView) desigFormView.classList.add('active');
    if (desigFormError) desigFormError.classList.remove('show');

    const formTitle = document.getElementById('desigFormTitle');
    const saveBtnText = document.getElementById('saveDesigBtnText');
    const editIdEl = document.getElementById('desigEditId');
    const titleEl = document.getElementById('desigTitle');
    const deptSelect = document.getElementById('desigDeptSelect');
    const statusEl = document.getElementById('desigStatus');

    if (desig) {
        if (formTitle) formTitle.textContent = 'Edit Designation';
        if (saveBtnText) saveBtnText.textContent = 'Update Designation';
        if (editIdEl) editIdEl.value = desig.id || '';
        if (titleEl) titleEl.value = desig.name || desig.title || '';
        if (deptSelect) deptSelect.value = desig.department_id || '';
        if (statusEl) statusEl.value = (desig.status || 'ACTIVE').toUpperCase();
    } else {
        if (formTitle) formTitle.textContent = 'Add New Designation';
        if (saveBtnText) saveBtnText.textContent = 'Save Designation';
        if (editIdEl) editIdEl.value = '';
        if (titleEl) titleEl.value = '';
        if (statusEl) statusEl.value = 'ACTIVE';
    }
};

async function loadDesignationsList() {
    if (!desigTableBody) return;
    desigState.isLoading = true;
    desigTableBody.innerHTML = `<tr><td colspan="5" class="modal-empty-state"><p>Loading designations...</p></td></tr>`;

    try {
        const res = await apiFetch('/api/designations');
        const data = await res.json();
        const raw = (data && data.data && data.data.designations)
            ? data.data.designations
            : (Array.isArray(data.designations) ? data.designations : (Array.isArray(data.data) ? data.data : []));
        desigState.designations = Array.isArray(raw) ? raw : [];
        renderDesignationTable();
    } catch (err) {
        desigTableBody.innerHTML = `<tr><td colspan="5" class="modal-empty-state" style="color: var(--red);"><p>Failed to load designations. Please try again.</p></td></tr>`;
    } finally {
        desigState.isLoading = false;
    }
}

function renderDesignationTable() {
    if (!desigTableBody) return;
    const query = (desigSearchInput ? desigSearchInput.value : '').toLowerCase().trim();
    const deptFilter = (desigDeptFilter ? desigDeptFilter.value : '').trim();
    const statusFilter = (desigStatusFilter ? desigStatusFilter.value : '').toUpperCase().trim();

    const filtered = desigState.designations.filter(d => {
        const nameMatch = !query || (d.name || d.title || '').toLowerCase().includes(query) || (d.department_name || '').toLowerCase().includes(query);
        const deptMatch = !deptFilter || String(d.department_id) === String(deptFilter);
        const statusMatch = !statusFilter || (d.status || '').toUpperCase() === statusFilter;
        return nameMatch && deptMatch && statusMatch;
    });

    if (filtered.length === 0) {
        desigTableBody.innerHTML = `
            <tr>
                <td colspan="5" class="modal-empty-state">
                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                    </svg>
                    <p>${desigState.designations.length === 0 ? 'No designations found. Click "+ Add Designation" to create one!' : 'No matching designations found.'}</p>
                </td>
            </tr>
        `;
        return;
    }

    desigTableBody.innerHTML = filtered.map((d, index) => {
        const deptName = d.department_name || (deptState.departments.find(dept => Number(dept.id) === Number(d.department_id))?.name) || `Dept #${d.department_id || 1}`;
        const title = d.name || d.title || 'Untitled';
        const status = (d.status || 'ACTIVE').toUpperCase();
        const statusClass = status === 'ACTIVE' ? 'badge-active' : 'badge-inactive';
        const safeName = title.replace(/'/g, "\\'");

        return `
            <tr>
                <td style="color: #94a3b8; font-size: 12px; font-weight: 600;">${index + 1}</td>
                <td><strong style="color: #0f172a; font-weight: 600;">${escapeHtml(title)}</strong></td>
                <td><span style="color: #475569; font-size: 12.5px;">${escapeHtml(deptName)}</span></td>
                <td><span class="crud-badge ${statusClass}">${status}</span></td>
                <td>
                    <div class="crud-actions-cell">
                        <button type="button" class="btn-crud-action edit-btn" title="Edit Designation" onclick="window.editDesigById(${d.id})">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                            </svg>
                        </button>
                        <button type="button" class="btn-crud-action delete-btn" title="Delete Designation" onclick="window.deleteDesigById(${d.id}, '${safeName}')">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="3 6 5 6 21 6"></polyline>
                                <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                            </svg>
                        </button>
                    </div>
                </td>
            </tr>
        `;
    }).join('');
}

window.editDesigById = function(id) {
    const desig = desigState.designations.find(d => Number(d.id) === Number(id));
    if (desig) {
        window.showDesigForm(desig);
    }
};

window.deleteDesigById = async function(id, name) {
    if (!confirm(`Are you sure you want to delete the designation "${name}"?\nThis cannot be undone.`)) {
        return;
    }

    try {
        const res = await apiFetch(`/api/designations/${id}`, {
            method: 'DELETE'
        });
        const result = await res.json();
        if (res.ok && result.success !== false) {
            showToast(`Designation "${name}" deleted successfully!`, 'success');
            loadDesignationsList();
            loadOrganisations();
        } else {
            showToast(result.message || 'Failed to delete designation.', 'error');
        }
    } catch (err) {
        showToast('Network error while deleting designation.', 'error');
    }
};

if (desigSearchInput) desigSearchInput.addEventListener('input', renderDesignationTable);
if (desigDeptFilter) desigDeptFilter.addEventListener('change', renderDesignationTable);
if (desigStatusFilter) desigStatusFilter.addEventListener('change', renderDesignationTable);

if (qaDesig) qaDesig.addEventListener('click', window.openDesigCrudModal);
if (qaAddDesig) qaAddDesig.addEventListener('click', window.openDesigCrudModal);

const closeDesigModal = document.getElementById('closeDesigModal');
const cancelDesigModal = document.getElementById('cancelDesigModal');
if (closeDesigModal) closeDesigModal.addEventListener('click', () => desigModal.classList.remove('open'));
if (cancelDesigModal) cancelDesigModal.addEventListener('click', () => desigModal.classList.remove('open'));
if (desigModal) desigModal.addEventListener('click', e => { if (e.target === desigModal) desigModal.classList.remove('open'); });

if (desigForm) {
    desigForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        if (desigFormError) desigFormError.classList.remove('show');

        const deptId = parseInt(desigDeptSelect.value, 10);
        if (!deptId) {
            if (desigFormError) {
                desigFormError.textContent = 'Please select a department for this designation.';
                desigFormError.classList.add('show');
            }
            return;
        }

        const name = document.getElementById('desigTitle').value.trim();
        const status = document.getElementById('desigStatus').value || 'ACTIVE';

        const saveBtn = document.getElementById('saveDesigBtn');
        const saveBtnText = document.getElementById('saveDesigBtnText');
        const editIdEl = document.getElementById('desigEditId');
        const isEdit = editIdEl && editIdEl.value;

        saveBtn.disabled = true;
        const originalText = saveBtnText ? saveBtnText.textContent : 'Save Designation';
        if (saveBtnText) saveBtnText.textContent = 'Saving...';

        const path = isEdit ? `/api/designations/${editIdEl.value}` : '/api/designations';
        const method = isEdit ? 'PUT' : 'POST';

        try {
            const res = await apiFetch(path, {
                method: method,
                body: JSON.stringify({ name, department_id: deptId, status })
            });
            const result = await res.json();
            if (res.ok && result.success !== false) {
                showToast(`Designation "${name}" ${isEdit ? 'updated' : 'added'} successfully!`, 'success');
                window.showDesigList();
                loadDesignationsList();
                loadOrganisations();
            } else {
                if (desigFormError) {
                    desigFormError.textContent = result.message || `Failed to ${isEdit ? 'update' : 'create'} designation.`;
                    desigFormError.classList.add('show');
                }
            }
        } catch (err) {
            if (desigFormError) {
                desigFormError.textContent = `Network error while ${isEdit ? 'updating' : 'saving'} designation.`;
                desigFormError.classList.add('show');
            }
        } finally {
            saveBtn.disabled = false;
            if (saveBtnText) saveBtnText.textContent = originalText;
        }
    });
}

// 3. ADD BRANCH
window.openAddBranchModal = function() {
    populateCompanyDropdowns();
    const bName = document.getElementById('branchName');
    const bLoc  = document.getElementById('branchLocation');
    const bPh   = document.getElementById('branchPhone');
    if (bName) bName.value = '';
    if (bLoc)  bLoc.value = '';
    if (bPh)   bPh.value = '';
    if (branchFormError) branchFormError.classList.remove('show');
    if (branchModal) branchModal.classList.add('open');
};

const qaAddBranch = document.getElementById('qaAddBranch');
if (qaAddBranch) {
    qaAddBranch.addEventListener('click', window.openAddBranchModal);
}

const closeBranchModal = document.getElementById('closeBranchModal');
const cancelBranchModal = document.getElementById('cancelBranchModal');
if (closeBranchModal) closeBranchModal.addEventListener('click', () => branchModal.classList.remove('open'));
if (cancelBranchModal) cancelBranchModal.addEventListener('click', () => branchModal.classList.remove('open'));
if (branchModal) branchModal.addEventListener('click', e => { if (e.target === branchModal) branchModal.classList.remove('open'); });

if (branchForm) {
    branchForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        if (branchFormError) branchFormError.classList.remove('show');

        const saveBtn = document.getElementById('saveBranchBtn');
        saveBtn.disabled = true;
        saveBtn.innerHTML = '&hellip; Saving';

        const bName     = document.getElementById('branchName').value.trim();
        const bLocation = document.getElementById('branchLocation').value.trim();
        const bPhone    = document.getElementById('branchPhone').value.trim();
        const bStatus   = document.getElementById('branchStatus').value || 'active';
        const bCompany  = parseInt(branchCompanySelect.value, 10) || 1;

        try {
            const res = await apiFetch('/api/branches', {
                method: 'POST',
                body: JSON.stringify({
                    name: bName,
                    company_id: bCompany,
                    location: bLocation,
                    phone: bPhone,
                    status: bStatus
                })
            });
            const result = await res.json();
            if (res.ok && result.success !== false) {
                showToast(`Branch "${bName}" registered successfully!`, 'success');
                branchModal.classList.remove('open');
                loadOrganisations();
            } else {
                if (branchFormError) {
                    branchFormError.textContent = result.message || 'Failed to create branch.';
                    branchFormError.classList.add('show');
                }
            }
        } catch (err) {
            if (branchFormError) {
                branchFormError.textContent = 'Network error while saving branch.';
                branchFormError.classList.add('show');
            }
        } finally {
            saveBtn.disabled = false;
            saveBtn.innerHTML = `<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg> Save Branch`;
        }
    });
}

// =========================================================
// 4. ROLE CRUD MANAGEMENT (LIST, CREATE, EDIT, DELETE)
// =========================================================
const roleState = {
    roles: [],
    isLoading: false
};

const roleModal       = document.getElementById('roleModal');
const roleForm        = document.getElementById('roleForm');
const roleFormError   = document.getElementById('roleFormError');
const roleListView    = document.getElementById('roleListView');
const roleFormView    = document.getElementById('roleFormView');
const roleSearchInput = document.getElementById('roleSearchInput');
const roleTableBody   = document.getElementById('roleTableBody');
const qaRole          = document.getElementById('qaRole');
const qaAddRole       = document.getElementById('qaAddRole');

window.openRoleCrudModal = function() {
    if (roleModal) roleModal.classList.add('open');
    window.showRoleList();
    loadRolesList();
};

window.openAddRoleModal = window.openRoleCrudModal;

window.showRoleList = function() {
    if (roleFormView) roleFormView.classList.remove('active');
    if (roleListView) roleListView.classList.add('active');
    const editIdEl = document.getElementById('roleEditId');
    if (editIdEl) editIdEl.value = '';
    if (roleFormError) roleFormError.classList.remove('show');
    const hTitle = document.getElementById('roleModalHeaderTitle');
    const hSub = document.getElementById('roleModalHeaderSub');
    if (hTitle) hTitle.textContent = 'Role Management';
    if (hSub) hSub.textContent = 'View, create, edit, and manage access control roles';
};

window.showRoleForm = function(role = null) {
    if (roleListView) roleListView.classList.remove('active');
    if (roleFormView) roleFormView.classList.add('active');
    if (roleFormError) roleFormError.classList.remove('show');

    const formTitle = document.getElementById('roleFormTitle');
    const saveBtnText = document.getElementById('saveRoleBtnText');
    const editIdEl = document.getElementById('roleEditId');
    const nameEl = document.getElementById('roleName');
    const descEl = document.getElementById('roleDescription');

    if (role) {
        if (formTitle) formTitle.textContent = 'Edit Role';
        if (saveBtnText) saveBtnText.textContent = 'Update Role';
        if (editIdEl) editIdEl.value = role.id || '';
        if (nameEl) nameEl.value = role.name || '';
        if (descEl) descEl.value = role.description || '';
    } else {
        if (formTitle) formTitle.textContent = 'Add New Role';
        if (saveBtnText) saveBtnText.textContent = 'Save Role';
        if (editIdEl) editIdEl.value = '';
        if (nameEl) nameEl.value = '';
        if (descEl) descEl.value = '';
    }
};

async function loadRolesList() {
    if (!roleTableBody) return;
    roleState.isLoading = true;
    roleTableBody.innerHTML = `<tr><td colspan="5" class="modal-empty-state"><p>Loading roles...</p></td></tr>`;

    try {
        const res = await apiFetch('/api/roles');
        const data = await res.json();
        const raw = (data && data.roles)
            ? data.roles
            : (data && data.data && data.data.roles ? data.data.roles : (Array.isArray(data.data) ? data.data : []));
        roleState.roles = Array.isArray(raw) ? raw : [];
        renderRoleTable();
    } catch (err) {
        roleTableBody.innerHTML = `<tr><td colspan="5" class="modal-empty-state" style="color: var(--red);"><p>Failed to load roles. Please try again.</p></td></tr>`;
    } finally {
        roleState.isLoading = false;
    }
}

function renderRoleTable() {
    if (!roleTableBody) return;
    const query = (roleSearchInput ? roleSearchInput.value : '').toLowerCase().trim();

    const filtered = roleState.roles.filter(r => {
        return !query || (r.name || '').toLowerCase().includes(query) || (r.description || '').toLowerCase().includes(query);
    });

    if (filtered.length === 0) {
        roleTableBody.innerHTML = `
            <tr>
                <td colspan="5" class="modal-empty-state">
                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                    </svg>
                    <p>${roleState.roles.length === 0 ? 'No roles found. Click "+ Add Role" to create one!' : 'No matching roles found.'}</p>
                </td>
            </tr>
        `;
        return;
    }

    roleTableBody.innerHTML = filtered.map((r, index) => {
        const safeName = (r.name || '').replace(/'/g, "\\'");
        const userCount = r.users_count || 0;

        return `
            <tr>
                <td style="color: #94a3b8; font-size: 12px; font-weight: 600;">${index + 1}</td>
                <td><span class="crud-badge badge-role">${escapeHtml(r.name)}</span></td>
                <td><span style="color: #475569; font-size: 12.5px;">${escapeHtml(r.description || '—')}</span></td>
                <td><span class="crud-badge badge-count">${userCount} ${userCount === 1 ? 'user' : 'users'}</span></td>
                <td>
                    <div class="crud-actions-cell">
                        <button type="button" class="btn-crud-action edit-btn" title="Edit Role" onclick="window.editRoleById(${r.id})">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                            </svg>
                        </button>
                        <button type="button" class="btn-crud-action delete-btn" title="Delete Role" onclick="window.deleteRoleById(${r.id}, '${safeName}')">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="3 6 5 6 21 6"></polyline>
                                <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                            </svg>
                        </button>
                    </div>
                </td>
            </tr>
        `;
    }).join('');
}

window.editRoleById = function(id) {
    const role = roleState.roles.find(r => Number(r.id) === Number(id));
    if (role) {
        window.showRoleForm(role);
    }
};

window.deleteRoleById = async function(id, name) {
    if (['ADMIN', 'SUPERADMIN'].includes((name || '').toUpperCase())) {
        showToast(`System core role "${name}" cannot be deleted.`, 'error');
        return;
    }

    if (!confirm(`Are you sure you want to delete the role "${name}"?\nThis cannot be undone.`)) {
        return;
    }

    try {
        const res = await apiFetch(`/api/roles/delete?id=${id}`, {
            method: 'DELETE'
        });
        const result = await res.json();
        if (res.ok && result.success !== false) {
            showToast(`Role "${name}" deleted successfully!`, 'success');
            loadRolesList();
        } else {
            showToast(result.message || 'Failed to delete role.', 'error');
        }
    } catch (err) {
        showToast('Network error while deleting role.', 'error');
    }
};

if (roleSearchInput) roleSearchInput.addEventListener('input', renderRoleTable);

if (qaRole) qaRole.addEventListener('click', window.openRoleCrudModal);
if (qaAddRole) qaAddRole.addEventListener('click', window.openRoleCrudModal);

const closeRoleModal  = document.getElementById('closeRoleModal');
const cancelRoleModal = document.getElementById('cancelRoleModal');
if (closeRoleModal) closeRoleModal.addEventListener('click', () => roleModal.classList.remove('open'));
if (cancelRoleModal) cancelRoleModal.addEventListener('click', () => roleModal.classList.remove('open'));
if (roleModal) roleModal.addEventListener('click', e => { if (e.target === roleModal) roleModal.classList.remove('open'); });

if (roleForm) {
    roleForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        if (roleFormError) roleFormError.classList.remove('show');

        const saveBtn = document.getElementById('saveRoleBtn');
        const saveBtnText = document.getElementById('saveRoleBtnText');
        const editIdEl = document.getElementById('roleEditId');
        const isEdit = editIdEl && editIdEl.value;

        saveBtn.disabled = true;
        const originalText = saveBtnText ? saveBtnText.textContent : 'Save Role';
        if (saveBtnText) saveBtnText.textContent = 'Saving...';

        const name        = document.getElementById('roleName').value.trim();
        const description = document.getElementById('roleDescription').value.trim();

        const path = isEdit ? `/api/roles/update?id=${editIdEl.value}` : '/api/roles';
        const method = isEdit ? 'PUT' : 'POST';

        try {
            const res = await apiFetch(path, {
                method: method,
                body: JSON.stringify({ name, description })
            });
            const result = await res.json();
            if (res.ok && result.success !== false) {
                showToast(`Role "${name}" ${isEdit ? 'updated' : 'created'} successfully!`, 'success');
                window.showRoleList();
                loadRolesList();
            } else {
                if (roleFormError) {
                    roleFormError.textContent = result.message || `Failed to ${isEdit ? 'update' : 'create'} role.`;
                    roleFormError.classList.add('show');
                }
            }
        } catch (err) {
            if (roleFormError) {
                roleFormError.textContent = `Network error while ${isEdit ? 'updating' : 'creating'} role.`;
                roleFormError.classList.add('show');
            }
        } finally {
            saveBtn.disabled = false;
            if (saveBtnText) saveBtnText.textContent = originalText;
        }
    });
}

// 5. EXPORT REPORTS (QUICK ACTION)
window.exportOrgReports = function() {
    if (!allOrgs || allOrgs.length === 0) {
        showToast('No organisation data available to export.', 'error');
        return;
    }

        const headers = ['ID', 'Name', 'Code', 'Type', 'Status', 'Email', 'Phone', 'Departments', 'Users', 'Address', 'Created Date'];
        const csvRows = [headers.join(',')];

        allOrgs.forEach(o => {
            const row = [
                o.id || '',
                `"${(o.name || o.organisation_name || '').replace(/"/g, '""')}"`,
                `"${(o.code || '').replace(/"/g, '""')}"`,
                `"${(o.type || 'Company').replace(/"/g, '""')}"`,
                `"${(o.status || 'Active').replace(/"/g, '""')}"`,
                `"${(o.email || '').replace(/"/g, '""')}"`,
                `"${(o.phone || '').replace(/"/g, '""')}"`,
                o.departments_count || 0,
                o.users_count || 0,
                `"${(o.address || '').replace(/"/g, '""')}"`,
                `"${formatDate(o.created_at)}"`
            ];
            csvRows.push(row.join(','));
        });

        const csvString = csvRows.join('\n');
        const blob = new Blob([csvString], { type: 'text/csv;charset=utf-8;' });
        const url = URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = `organisations-report-${new Date().toISOString().slice(0, 10)}.csv`;
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
        URL.revokeObjectURL(url);
        showToast('Organisation report exported successfully!', 'success');
};

const qaExportReports = document.getElementById('qaExportReports');
if (qaExportReports) {
    qaExportReports.addEventListener('click', window.exportOrgReports);
}

/* ── INIT ─────────────────────────────────────────────── */

document.addEventListener('DOMContentLoaded', () => {
    if (!getToken()) { redirectLogin(); return; }
    setAdminInfo();
    loadOrganisations();
});

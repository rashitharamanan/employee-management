'use strict';

/* =========================================================
   Leave Management JS
   Connects to:
     - GET /api/leave-types (leave categories)
     - POST /api/leave-types (create category)
     - PUT /api/leave-types/{id} (update category)
     - GET /api/hr/leave (overview, KPI summary, and requests)
     - POST /api/leave-requests/approve (approve request)
     - POST /api/leave-requests/reject (reject request)
   ========================================================= */

const API_BASE = 'http://localhost/employee-qr-system/backend/public';

const state = {
    currentPage: 1,
    perPage: 8,
    totalPages: 1,
    totalItems: 0,
    overview: null,
    leaveTypes: [],
    pendingRejectId: null,
    pendingViewRequest: null,
};

const leaveTypesCache = {};

const token = () => localStorage.getItem('auth_token') || localStorage.getItem('token') || '';

function getAuthUser() {
    try {
        return JSON.parse(localStorage.getItem('auth_user') || '{}');
    } catch {
        return {};
    }
}

function getUserRole() {
    const user = getAuthUser();
    return String(user.role || user.role_name || user.roleName || '').trim().toUpperCase();
}

function isCurrentUserAdmin() {
    const role = getUserRole();
    return role === 'ADMIN' || role === 'SUPER_ADMIN' || !role || role.includes('ADMIN');
}

/* ── API helper ─────────────────────────────────────── */

async function api(path, options = {}) {
    const response = await fetch(`${API_BASE}${path}`, {
        ...options,
        headers: {
            'Content-Type': 'application/json',
            Authorization: `Bearer ${token()}`,
            ...(options.headers || {}),
        },
    });

    if (response.status === 401) {
        localStorage.clear();
        window.location.href = 'login.php';
        return null;
    }

    const result = await response.json();
    if (!response.ok || result.success === false) {
        throw new Error(result.message || 'Request failed.');
    }

    return result.data || {};
}

/* ── Utilities ──────────────────────────────────────── */

const esc = value =>
    String(value ?? '').replace(/[&<>'"]/g, c => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', "'": '&#39;', '"': '&quot;' }[c]));

function showToast(message, isError = false) {
    const toast = document.getElementById('toast');
    if (!toast) return;
    toast.textContent = message;
    toast.style.background = isError ? 'rgba(220,38,38,0.95)' : 'rgba(15,23,42,0.94)';
    toast.classList.add('show');
    setTimeout(() => toast.classList.remove('show'), 3000);
}

function openModal(id) {
    const modal = document.getElementById(id);
    if (!modal) return;
    modal.setAttribute('aria-hidden', 'false');
    modal.classList.add('open');
}

function closeModal(id) {
    const modal = document.getElementById(id);
    if (!modal) return;
    modal.setAttribute('aria-hidden', 'true');
    modal.classList.remove('open');
}

function buildAvatarColor(name) {
    const colors = ['#42134f', '#be185d', '#581c87', '#7e22ce', '#db2777', '#831843', '#9d174d'];
    let hash = 0;
    for (let i = 0; i < (name || '').length; i++) {
        hash = (name.charCodeAt(i) + ((hash << 5) - hash));
    }
    const idx = Math.abs(hash) % colors.length;
    return colors[idx];
}

function statusBadgeClass(status) {
    const map = { PENDING: 'badge-pending', APPROVED: 'badge-approved', REJECTED: 'badge-rejected' };
    return map[(status || '').toUpperCase()] || 'badge-pending';
}

/* ── Leave Types Section ────────────────────────────── */

function renderLeaveTypes(types) {
    const grid = document.getElementById('leaveTypesGrid');
    const tbody = document.getElementById('leaveTypesBody');
    const counter = document.getElementById('leaveTypesCount');
    const totalTypesEl = document.getElementById('kpiTotalTypes');

    if (counter) counter.textContent = `${types ? types.length : 0} Types`;
    if (totalTypesEl) totalTypesEl.textContent = types ? types.length : 0;

    if (grid) {
        if (!types || types.length === 0) {
            grid.innerHTML = '<div class="cards-empty-state">No leave types configured.</div>';
        } else {
            grid.innerHTML = types.map(t => {
                const daysText = (t.annual_days !== null && t.annual_days !== undefined && Number(t.annual_days) > 0)
                    ? `${Number(t.annual_days)} Days/yr`
                    : 'Flexible';
                const isPaid = Boolean(t.is_paid == 1 || t.is_paid === true || t.is_paid === '1');
                const isActive = String(t.status || '').toUpperCase() === 'ACTIVE';

                return `
                    <div class="leave-type-chip-card" data-edit-type="${t.id}">
                        <div class="ltc-info">
                            <span class="ltc-name" title="${esc(t.name)}">${esc(t.name)}</span>
                            <div class="ltc-meta">
                                <span>${esc(daysText)}</span>
                                <span class="ltc-badge ${isPaid ? 'badge-paid' : 'badge-unpaid'}">${isPaid ? 'Paid' : 'Unpaid'}</span>
                                <span class="ltc-status-dot ${isActive ? 'dot-active' : 'dot-inactive'}" title="${isActive ? 'Active' : 'Inactive'}"></span>
                            </div>
                        </div>
                        <button type="button" class="elc-btn-secondary ltc-edit-btn" data-edit-type="${t.id}">Edit</button>
                    </div>
                `;
            }).join('');
        }
    }

    if (tbody) {
        if (!types || types.length === 0) {
            tbody.innerHTML = '<tr><td colspan="5" class="empty-cell">No leave types configured.</td></tr>';
            return;
        }

        tbody.innerHTML = types.map(t => {
            const daysText = (t.annual_days !== null && t.annual_days !== undefined && Number(t.annual_days) > 0)
                ? `${Number(t.annual_days)} Days`
                : '—';
            const isPaid = Boolean(t.is_paid == 1 || t.is_paid === true || t.is_paid === '1');
            const isActive = String(t.status || '').toUpperCase() === 'ACTIVE';

            return `
                <tr>
                    <td>
                        <div class="leave-type-name-cell">
                            <strong>${esc(t.name)}</strong>
                            ${t.description ? `<small>${esc(t.description)}</small>` : ''}
                        </div>
                    </td>
                    <td><span class="leave-days-cell">${esc(daysText)}</span></td>
                    <td>
                        ${isPaid ? '<span class="type-badge-paid">Paid</span>' : '<span class="type-badge-no">No</span>'}
                    </td>
                    <td>
                        ${isActive ? '<span class="type-badge-active">Active</span>' : '<span class="type-badge-inactive">Inactive</span>'}
                    </td>
                    <td style="text-align: right;">
                        <button type="button" class="lm-btn-edit" data-edit-type="${t.id}">Edit</button>
                    </td>
                </tr>
            `;
        }).join('');
    }
}

async function loadLeaveTypes() {
    try {
        const data = await api('/api/leave-types');
        const types = data.leave_types || [];
        state.leaveTypes = types;
        types.forEach(t => { leaveTypesCache[t.id] = t; });
        renderLeaveTypes(types);
    } catch (e) {
        console.error('Failed to load leave types:', e);
        const tbody = document.getElementById('leaveTypesBody');
        if (tbody) tbody.innerHTML = `<tr><td colspan="5" class="empty-cell">${esc(e.message)}</td></tr>`;
    }
}

/* ── Leave Requests Cards Grid (matching reference design) ── */

function renderTable(requests, pagination) {
    const grid = document.getElementById('requestsCardsGrid');
    const tbody = document.getElementById('requestsBody');
    if (!grid && !tbody) return;

    const statusVal = document.getElementById('requestStatus')?.value || '';

    if (!requests || requests.length === 0) {
        const emptyMsg = statusVal === 'PENDING'
            ? 'No pending leave requests awaiting approval.'
            : 'No leave requests match your criteria.';
        if (grid) grid.innerHTML = `<div class="cards-empty-state">${emptyMsg}</div>`;
        if (tbody) tbody.innerHTML = `<tr><td colspan="7" class="empty-cell">${emptyMsg}</td></tr>`;
        renderPagination(pagination || { current_page: 1, per_page: 8, total_items: 0, total_pages: 0 });
        return;
    }

    const cardsHtml = requests.map(req => {
        const emp = req.employee || {};
        const lt = req.leave_type || {};
        const color = buildAvatarColor(emp.name);
        const statusLower = (req.status || 'pending').toLowerCase();
        
        // Formatted dates and remaining days
        const fromDate = req.from_date_formatted || req.from_date || '';
        const toDate = req.to_date_formatted || req.to_date || '';
        const daysText = req.days_formatted || `${req.total_days || 1} Days`;
        const remainingDays = req.remaining_days ?? (Math.max(0, (lt.annual_days || 12) - (req.total_days || 1)));

        // Reason text
        const reasonText = req.reason ? req.reason.trim() : 'Personal matter to attend.';

        // Photo or Initial Avatar matching Image 1
        const avatarHtml = emp.photo
            ? `<img src="${esc(emp.photo)}" alt="${esc(emp.name)}" class="elc-avatar">`
            : `<div class="elc-avatar-initials" style="background:${color}">${esc(emp.initial || (emp.name ? emp.name.charAt(0).toUpperCase() : '?'))}</div>`;

        // Contact info / Fallbacks for the rounded info box matching Image 1
        const emailText = emp.email || `${lt.name || 'General Leave'} (${remainingDays}d balance)`;
        const phoneOrReason = emp.phone || reasonText;

        // Bottom dual action buttons matching Image 1
        let actionsHtml = '';
        if (req.status === 'PENDING') {
            actionsHtml = `
                <button type="button" class="elc-btn elc-btn-reject" data-action="reject" data-id="${req.id}" title="Reject Leave Request">Reject</button>
                <button type="button" class="elc-btn elc-btn-approve" data-action="approve" data-id="${req.id}" title="Approve Leave Request">Approve</button>
            `;
        } else {
            actionsHtml = `
                <button type="button" class="elc-btn elc-btn-secondary" data-action="view" data-id="${req.id}" title="Edit / Add Notes">Edit</button>
                <button type="button" class="elc-btn elc-btn-primary" data-action="view" data-id="${req.id}" title="View Details">View</button>
            `;
        }

        // Status badge display label
        let displayStatus = req.status || 'PENDING';

        return `
            <div class="emp-leave-card l-req-card" data-req-id="${req.id}">
                <!-- Top Row: Status Pill & Action Menu -->
                <div class="elc-top-bar">
                    <span class="elc-status-pill status-${statusLower}">${esc(displayStatus)}</span>
                    <button type="button" class="elc-menu-btn" data-action="view" data-id="${req.id}" aria-label="More options" title="Quick actions">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="currentColor">
                            <circle cx="12" cy="5" r="2.2"/>
                            <circle cx="12" cy="12" r="2.2"/>
                            <circle cx="12" cy="19" r="2.2"/>
                        </svg>
                    </button>
                </div>

                <!-- Profile Row -->
                <div class="elc-profile">
                    ${avatarHtml}
                    <div class="elc-profile-details">
                        <h4 class="elc-name" title="${esc(emp.name)}">${esc(emp.name)}</h4>
                        <p class="elc-designation" title="${esc(emp.designation || 'Staff')}">${esc(emp.designation || 'Staff')}</p>
                    </div>
                </div>

                <!-- 2-Column Metadata Grid -->
                <div class="elc-meta-grid">
                    <div class="elc-meta-col">
                        <span class="elc-meta-label">DEPARTMENT</span>
                        <span class="elc-meta-val" title="${esc(emp.department || 'General')}">${esc(emp.department || 'General')}</span>
                    </div>
                    <div class="elc-meta-col">
                        <span class="elc-meta-label">LEAVE PERIOD</span>
                        <span class="elc-meta-val" title="${esc(daysText)} (${esc(fromDate)} - ${esc(toDate)})">${esc(daysText)}</span>
                    </div>
                </div>

                <!-- Light Gray Rounded Info Box matching reference -->
                <div class="elc-info-box" data-action="view" data-id="${req.id}" title="Click to view full details">
                    <div class="elc-info-row">
                        <svg class="elc-info-icon" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                        <span class="elc-info-text">${esc(emailText)}</span>
                    </div>
                    <div class="elc-info-row">
                        <svg class="elc-info-icon" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                        <span class="elc-info-text">${esc(phoneOrReason)}</span>
                    </div>
                </div>

                <!-- Dual Action Buttons matching Image 1 -->
                <div class="elc-actions">
                    ${actionsHtml}
                </div>
            </div>
        `;
    }).join('');

    if (grid) grid.innerHTML = cardsHtml;
    renderPagination(pagination);
}

function renderPagination(pagination) {
    if (!pagination) return;

    state.currentPage = pagination.current_page;
    state.totalPages = pagination.total_pages;
    state.totalItems = pagination.total_items;

    const pageInfo = document.getElementById('pageInfo');
    const start = (pagination.current_page - 1) * pagination.per_page + 1;
    const end = Math.min(pagination.current_page * pagination.per_page, pagination.total_items);
    if (pageInfo) {
        pageInfo.textContent = pagination.total_items > 0
            ? `Showing ${start}–${end} of ${pagination.total_items} requests`
            : 'No requests';
    }

    const countEl = document.getElementById('requestsCount');
    if (countEl) {
        countEl.textContent = `${pagination.total_items} request${pagination.total_items !== 1 ? 's' : ''}`;
    }

    // Update < and > arrow states
    const prevBtn = document.getElementById('prevCardPage');
    const nextBtn = document.getElementById('nextCardPage');
    if (prevBtn) prevBtn.disabled = pagination.current_page <= 1;
    if (nextBtn) nextBtn.disabled = pagination.current_page >= pagination.total_pages;

    const container = document.getElementById('paginationControls');
    if (!container) return;
    const total = pagination.total_pages;
    const cur = pagination.current_page;

    if (total <= 1) { container.innerHTML = ''; return; }

    let buttons = '';
    buttons += `<button class="page-btn" data-page="${cur - 1}" ${cur <= 1 ? 'disabled' : ''}>‹</button>`;

    for (let p = 1; p <= total; p++) {
        if (p === 1 || p === total || (p >= cur - 2 && p <= cur + 2)) {
            buttons += `<button class="page-btn ${p === cur ? 'active' : ''}" data-page="${p}">${p}</button>`;
        } else if (p === cur - 3 || p === cur + 3) {
            buttons += `<span style="padding:0 4px;color:var(--lm-muted)">…</span>`;
        }
    }

    buttons += `<button class="page-btn" data-page="${cur + 1}" ${cur >= total ? 'disabled' : ''}>›</button>`;
    container.innerHTML = buttons;
}

/* ── Main Load Overview Function ─────────────────────── */

async function loadOverview(page = 1) {
    const search = document.getElementById('requestSearch')?.value.trim() || '';
    const status = document.getElementById('requestStatus')?.value || (isCurrentUserAdmin() ? 'PENDING' : '');
    const scope = isCurrentUserAdmin() ? 'hr_manager' : (document.getElementById('requestScope')?.value || 'all');
    const perPage = parseInt(document.getElementById('perPageSelect')?.value || '6', 10);

    const params = new URLSearchParams({
        page,
        per_page: perPage,
        search,
        status,
        scope,
    });

    try {
        const tbody = document.getElementById('requestsBody');
        if (tbody) {
            tbody.innerHTML = '<tr><td colspan="7" class="empty-cell">Loading requests...</td></tr>';
        }

        const data = await api(`/api/hr/leave?${params.toString()}`);
        if (!data) return;

        state.overview = data;

        // Populate KPI Metrics
        const summary = data.summary || {};
        const totalReqs = summary.total_requests_month ?? data.requests_data?.pagination?.total_items ?? 0;
        const totalEl = document.getElementById('kpiTotalRequests') || document.getElementById('kpiTotal');
        if (totalEl) totalEl.textContent = totalReqs;

        const pendingEl = document.getElementById('kpiPendingRequests') || document.getElementById('kpiPending');
        if (pendingEl) pendingEl.textContent = summary.pending_requests ?? 0;

        const approvedEl = document.getElementById('kpiApprovedRequests') || document.getElementById('kpiApproved');
        if (approvedEl) approvedEl.textContent = summary.approved_requests_month ?? 0;

        const onLeaveEl = document.getElementById('kpiOnLeaveToday') || document.getElementById('kpiOnLeave');
        if (onLeaveEl) onLeaveEl.textContent = summary.on_leave_today ?? 0;

        const { requests, pagination } = data.requests_data || {};

        // If current page is empty after actioning items, go back to previous page
        if ((!requests || requests.length === 0) && page > 1) {
            return loadOverview(page - 1);
        }

        renderTable(requests || [], pagination);

    } catch (error) {
        const tbody = document.getElementById('requestsBody');
        if (tbody) {
            tbody.innerHTML = `<tr><td colspan="7" class="empty-cell">${esc(error.message)}</td></tr>`;
        }
        showToast(error.message, true);
    }
}

/* ── Approve / Reject Actions ───────────────────────── */

async function approveRequest(id) {
    const card = document.querySelector(`.l-req-card[data-req-id="${id}"]`) || document.querySelector(`tr[data-req-id="${id}"]`);
    if (card) {
        card.style.transition = 'all 0.35s ease';
        card.style.opacity = '0';
        card.style.transform = 'scale(0.88)';
    }

    try {
        await api(`/api/leave-requests/approve?id=${encodeURIComponent(id)}`, { method: 'POST', body: '{}' });
        showToast('✅ Leave request approved. Request completed.');
        await loadOverview(state.currentPage);
    } catch (error) {
        if (card) {
            card.style.opacity = '1';
            card.style.transform = 'none';
        }
        showToast(error.message, true);
        await loadOverview(state.currentPage);
    }
}

async function rejectRequest(id, reason) {
    try {
        await api(`/api/leave-requests/reject?id=${encodeURIComponent(id)}`, {
            method: 'POST',
            body: JSON.stringify({ reason }),
        });
        showToast('Leave request rejected.');
        await loadOverview(state.currentPage);
    } catch (error) {
        showToast(error.message, true);
    }
}

/* ── View Request Modal ─────────────────────────────── */

function openViewModal(reqId) {
    const data = state.overview?.requests_data?.requests || [];
    const req = data.find(r => Number(r.id) === Number(reqId));
    if (!req) return;

    state.pendingViewRequest = req;

    document.getElementById('viewRequestTitle').textContent = req.employee?.name || 'Leave Request';
    document.getElementById('viewRequestCode').textContent = req.request_code || '—';

    const body = document.getElementById('viewRequestBody');
    body.innerHTML = `
        <div class="vr-field"><span class="vr-label">Employee</span><span class="vr-value">${esc(req.employee?.name)}</span></div>
        <div class="vr-field"><span class="vr-label">Employee Code</span><span class="vr-value">${esc(req.employee?.code)}</span></div>
        <div class="vr-field"><span class="vr-label">Department</span><span class="vr-value">${esc(req.employee?.department)}</span></div>
        <div class="vr-field"><span class="vr-label">Designation</span><span class="vr-value">${esc(req.employee?.designation)}</span></div>
        <div class="vr-field"><span class="vr-label">Leave Type</span><span class="vr-value">${esc(req.leave_type?.name)}</span></div>
        <div class="vr-field"><span class="vr-label">Duration</span><span class="vr-value">${esc(req.days_formatted)}</span></div>
        <div class="vr-field"><span class="vr-label">From</span><span class="vr-value">${esc(req.from_date_formatted)}</span></div>
        <div class="vr-field"><span class="vr-label">To</span><span class="vr-value">${esc(req.to_date_formatted)}</span></div>
        <div class="vr-field"><span class="vr-label">Status</span><span class="vr-value"><span class="lm-badge ${statusBadgeClass(req.status)}">${esc(req.status)}</span></span></div>
        <div class="vr-field"><span class="vr-label">Applied On</span><span class="vr-value">${esc(req.applied_on)}</span></div>
        <div class="vr-field"><span class="vr-label">Approval Route</span><span class="vr-value"><span class="emp-route-badge ${req.submitted_to === 'Admin' ? 'route-to-admin' : 'route-to-hr'}">${esc(req.requester_role || 'EMPLOYEE')} → Directed to ${esc(req.submitted_to || 'HR')}</span></span></div>
        ${req.reason ? `<div class="vr-field vr-full"><span class="vr-label">Reason</span><span class="vr-value">${esc(req.reason)}</span></div>` : ''}
        ${req.rejection_reason ? `<div class="vr-field vr-full"><span class="vr-label">Rejection Reason</span><span class="vr-value" style="color:var(--lm-red)">${esc(req.rejection_reason)}</span></div>` : ''}
        ${req.approved_by ? `<div class="vr-field"><span class="vr-label">Actioned By</span><span class="vr-value">${esc(req.approved_by)}</span></div>` : ''}
    `;

    const approveBtn = document.getElementById('viewApproveBtn');
    const rejectBtn = document.getElementById('viewRejectBtn');
    const isPending = req.status === 'PENDING';
    approveBtn.style.display = isPending ? 'inline-flex' : 'none';
    rejectBtn.style.display = isPending ? 'inline-flex' : 'none';
    approveBtn.dataset.id = reqId;
    rejectBtn.dataset.id = reqId;

    openModal('viewRequestModal');
}

/* ── Leave Type Form (Add/Edit) ─────────────────────── */

let editingTypeId = null;

function openLeaveTypeModal(type = null) {
    editingTypeId = type ? type.id : null;
    document.getElementById('leaveTypeModalTitle').textContent = type ? 'Edit Leave Type' : 'Add Leave Type';
    document.getElementById('leaveTypeName').value = type?.name || '';
    document.getElementById('leaveTypeDescription').value = type?.description || '';
    document.getElementById('leaveTypeDays').value = type ? Number(type.annual_days || 0) : 12;
    document.getElementById('leaveTypeStatus').value = type?.status || 'ACTIVE';
    document.getElementById('leaveTypePaid').checked = type ? Boolean(type.is_paid == 1 || type.is_paid === true) : true;
    document.getElementById('leaveTypeError').textContent = '';
    openModal('leaveTypeModal');
}

async function saveLeaveType(event) {
    event.preventDefault();
    const payload = {
        name: document.getElementById('leaveTypeName').value.trim(),
        description: document.getElementById('leaveTypeDescription').value.trim(),
        annual_days: Number(document.getElementById('leaveTypeDays').value || 0),
        status: document.getElementById('leaveTypeStatus').value,
        is_paid: document.getElementById('leaveTypePaid').checked ? 1 : 0,
    };

    try {
        if (editingTypeId) {
            await api(`/api/leave-types/${editingTypeId}`, { method: 'PUT', body: JSON.stringify(payload) });
        } else {
            await api('/api/leave-types', { method: 'POST', body: JSON.stringify(payload) });
        }
        closeModal('leaveTypeModal');
        showToast('Leave type saved successfully.');
        await loadLeaveTypes();
        await loadOverview(state.currentPage);
    } catch (error) {
        document.getElementById('leaveTypeError').textContent = error.message;
    }
}

/* ── Event Wiring ───────────────────────────────────── */

let searchDebounceTimer = null;

document.getElementById('requestSearch')?.addEventListener('input', () => {
    clearTimeout(searchDebounceTimer);
    searchDebounceTimer = setTimeout(() => loadOverview(1), 350);
});

document.getElementById('requestScope')?.addEventListener('change', () => loadOverview(1));
document.getElementById('requestStatus')?.addEventListener('change', () => loadOverview(1));
document.getElementById('perPageSelect')?.addEventListener('change', () => loadOverview(1));

// Leave Types Edit button click
document.getElementById('leaveTypesBody')?.addEventListener('click', event => {
    const btn = event.target.closest('[data-edit-type]');
    if (!btn) return;
    const id = Number(btn.dataset.editType);
    const type = leaveTypesCache[id];
    if (type) openLeaveTypeModal(type);
});

// Card grid action buttons (approve / reject / view)
function handleActionClick(event) {
    const btn = event.target.closest('[data-action]');
    if (!btn) return;
    const id = Number(btn.dataset.id);
    const action = btn.dataset.action;

    if (action === 'approve') {
        if (confirm('Approve this leave request?')) approveRequest(id);
    }

    if (action === 'reject') {
        state.pendingRejectId = id;
        document.getElementById('rejectReason').value = '';
        document.getElementById('rejectError').textContent = '';
        openModal('rejectModal');
    }

    if (action === 'view') openViewModal(id);
}

document.getElementById('requestsCardsGrid')?.addEventListener('click', handleActionClick);
document.getElementById('requestsBody')?.addEventListener('click', handleActionClick);

// Status Pills segmented buttons
document.getElementById('statusPillsGroup')?.addEventListener('click', event => {
    const btn = event.target.closest('.l-pill-btn');
    if (!btn) return;
    document.querySelectorAll('#statusPillsGroup .l-pill-btn').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
    const statusVal = btn.dataset.status;
    const statusSelect = document.getElementById('requestStatus');
    if (statusSelect) statusSelect.value = statusVal;
    loadOverview(1);
});

// Card Navigation Arrows < >
document.getElementById('prevCardPage')?.addEventListener('click', () => {
    if (state.currentPage > 1) loadOverview(state.currentPage - 1);
});

document.getElementById('nextCardPage')?.addEventListener('click', () => {
    if (state.currentPage < state.totalPages) loadOverview(state.currentPage + 1);
});

// Leaves / Permission Tabs
document.getElementById('tabLeavesBtn')?.addEventListener('click', () => {
    document.getElementById('tabLeavesBtn')?.classList.add('active');
    document.getElementById('tabPermissionBtn')?.classList.remove('active');
    loadOverview(1);
});

document.getElementById('tabPermissionBtn')?.addEventListener('click', () => {
    document.getElementById('tabPermissionBtn')?.classList.add('active');
    document.getElementById('tabLeavesBtn')?.classList.remove('active');
    showToast('Permission requests (gate passes) view.');
});

// Pagination
document.getElementById('paginationControls')?.addEventListener('click', event => {
    const btn = event.target.closest('[data-page]');
    if (!btn || btn.disabled) return;
    const page = Number(btn.dataset.page);
    if (page >= 1 && page <= state.totalPages) loadOverview(page);
});

// Confirm rejection modal
document.getElementById('confirmRejectBtn')?.addEventListener('click', async () => {
    const reason = document.getElementById('rejectReason').value.trim();
    if (!reason) {
        document.getElementById('rejectError').textContent = 'Rejection reason is required.';
        return;
    }
    closeModal('rejectModal');
    await rejectRequest(state.pendingRejectId, reason);
    state.pendingRejectId = null;
});

// View modal approve/reject quick actions
document.getElementById('viewApproveBtn')?.addEventListener('click', async () => {
    const id = Number(document.getElementById('viewApproveBtn').dataset.id);
    closeModal('viewRequestModal');
    if (confirm('Approve this leave request?')) await approveRequest(id);
});

document.getElementById('viewRejectBtn')?.addEventListener('click', () => {
    const id = Number(document.getElementById('viewRejectBtn').dataset.id);
    closeModal('viewRequestModal');
    state.pendingRejectId = id;
    document.getElementById('rejectReason').value = '';
    document.getElementById('rejectError').textContent = '';
    openModal('rejectModal');
});

// Add Leave Type button
document.getElementById('addLeaveTypeBtn')?.addEventListener('click', () => openLeaveTypeModal(null));

// Leave Type Form submit
document.getElementById('leaveTypeForm')?.addEventListener('submit', saveLeaveType);

// Close buttons (all modals)
document.querySelectorAll('[data-close]').forEach(btn => {
    btn.addEventListener('click', () => closeModal(btn.dataset.close));
});

// Click outside modal to close
document.querySelectorAll('.lm-modal').forEach(modal => {
    modal.addEventListener('click', event => {
        if (event.target === modal) closeModal(modal.id);
    });
});

// Escape key
document.addEventListener('keydown', event => {
    if (event.key === 'Escape') {
        document.querySelectorAll('.lm-modal.open').forEach(m => closeModal(m.id));
    }
});

/* ── Init ───────────────────────────────────────────── */

document.addEventListener('DOMContentLoaded', async () => {
    if (!token()) {
        window.location.href = 'login.php';
        return;
    }

    const scopeSelect = document.getElementById('requestScope');
    if (scopeSelect && isCurrentUserAdmin()) {
        // Admin strictly reviews HR & Manager leave requests
        scopeSelect.innerHTML = '<option value="hr_manager" selected>HR &amp; Manager → Admin</option>';
        scopeSelect.disabled = true;
        scopeSelect.title = 'Admin reviews and grants leaves for HR and Managers only';
    }

    const statusSelect = document.getElementById('requestStatus');
    if (statusSelect && !statusSelect.value) {
        statusSelect.value = 'PENDING';
    }

    await loadLeaveTypes();
    await loadOverview(1);
});

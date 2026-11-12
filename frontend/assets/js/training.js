'use strict';

/* =========================================================
   Training Management JS
   Connects to GET/POST/PUT/DELETE /api/training/*
========================================================= */

const API_BASE = 'http://localhost/employee-qr-system/backend/public';

const state = {
    activeTab: 'programs',
    programPage: 1,
    enrollmentPage: 1,
    programPagination: { total_items: 0, total_pages: 0, current_page: 1 },
    enrollmentPagination: { total_items: 0, total_pages: 0, current_page: 1 },
    programFilters: {},
    enrollmentFilters: {},
    employees: [],
    programs: [],
    categories: [],
    pendingDelete: null,
    viewingProgramId: null,
};

const token = () => localStorage.getItem('auth_token') || localStorage.getItem('token') || '';

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

    const result = await response.json().catch(() => ({}));
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
    toast.textContent = message;
    toast.style.background = isError ? 'rgba(220,38,38,0.95)' : 'rgba(15,23,42,0.94)';
    toast.classList.add('show');
    setTimeout(() => toast.classList.remove('show'), 3200);
}

function showError(message) {
    const el = document.getElementById('trError');
    el.textContent = message;
    el.style.display = 'block';
    setTimeout(() => { el.style.display = 'none'; }, 6000);
}

function fmtDate(value) {
    if (!value) return '—';
    const parts = String(value).split(' ')[0].split('-');
    if (parts.length !== 3) return value;
    const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
    return `${months[parseInt(parts[1], 10) - 1]} ${parts[2]}, ${parts[0]}`;
}

function fmtTime(value) {
    if (!value) return '—';
    const [h, m] = String(value).split(':');
    const hour = Number(h);
    const suffix = hour >= 12 ? 'PM' : 'AM';
    const display = ((hour + 11) % 12) + 1;
    return `${display}:${m} ${suffix}`;
}

function today() {
    return new Date().toISOString().slice(0, 10);
}

function programStatusBadgeClass(status) {
    const s = String(status || '').toUpperCase();
    if (s === 'ACTIVE') return 'active';
    if (s === 'COMPLETED') return 'active';
    if (s === 'DRAFT') return 'on_leave';
    if (s === 'CANCELLED') return 'inactive';
    return 'on_leave';
}

function enrollmentStatusBadgeClass(status) {
    const s = String(status || '').toUpperCase();
    if (s === 'COMPLETED') return 'active';
    if (s === 'IN_PROGRESS' || s === 'ENROLLED') return 'on_leave';
    if (s === 'DROPPED') return 'inactive';
    return 'on_leave';
}

async function changeProgramStatus(id, newStatus, selectEl) {
    const prevStatus = selectEl?.getAttribute('data-status') || 'DRAFT';
    const numId = parseInt(id, 10);

    try {
        await api(`/api/training/programs/update?id=${numId}`, {
            method: 'PUT',
            body: JSON.stringify({ status: newStatus })
        });

        if (selectEl) {
            selectEl.className = `rec-status-select ${programStatusBadgeClass(newStatus)}`;
            selectEl.setAttribute('data-status', newStatus.toUpperCase());
        }

        const prog = state.programs.find(p => p.id == numId);
        if (prog) prog.status = newStatus.toUpperCase();

        loadSummary();
        showToast(`Program status changed to ${newStatus.toUpperCase()}`);
    } catch (err) {
        console.error('Program status change error:', err);
        if (selectEl) {
            selectEl.value = prevStatus;
            selectEl.className = `rec-status-select ${programStatusBadgeClass(prevStatus)}`;
        }
        showToast(err.message || 'Failed to update program status.', true);
    }
}

async function changeEnrollmentStatus(id, newStatus, selectEl) {
    const prevStatus = selectEl?.getAttribute('data-status') || 'ENROLLED';
    const numId = parseInt(id, 10);

    try {
        await api(`/api/training/enrollments/update?id=${numId}`, {
            method: 'PUT',
            body: JSON.stringify({ status: newStatus })
        });

        if (selectEl) {
            selectEl.className = `rec-status-select ${enrollmentStatusBadgeClass(newStatus)}`;
            selectEl.setAttribute('data-status', newStatus.toUpperCase());
        }

        loadSummary();
        showToast(`Enrollment status changed to ${newStatus.toUpperCase()}`);
    } catch (err) {
        console.error('Enrollment status change error:', err);
        if (selectEl) {
            selectEl.value = prevStatus;
            selectEl.className = `rec-status-select ${enrollmentStatusBadgeClass(prevStatus)}`;
        }
        showToast(err.message || 'Failed to update enrollment status.', true);
    }
}

window.changeProgramStatus = changeProgramStatus;
window.changeEnrollmentStatus = changeEnrollmentStatus;
window.programStatusBadgeClass = programStatusBadgeClass;
window.enrollmentStatusBadgeClass = enrollmentStatusBadgeClass;

function statusBadge(status) {
    const s = String(status || '').toUpperCase();
    const map = {
        DRAFT: '<span class="tr-badge tr-badge-muted">Draft</span>',
        ACTIVE: '<span class="tr-badge tr-badge-primary">Active</span>',
        COMPLETED: '<span class="tr-badge tr-badge-success">Completed</span>',
        CANCELLED: '<span class="tr-badge tr-badge-danger">Cancelled</span>',
        ENROLLED: '<span class="tr-badge tr-badge-info">Enrolled</span>',
        IN_PROGRESS: '<span class="tr-badge tr-badge-warning">In Progress</span>',
        DROPPED: '<span class="tr-badge tr-badge-danger">Dropped</span>',
        SCHEDULED: '<span class="tr-badge tr-badge-info">Scheduled</span>',
        ONLINE: '<span class="tr-badge tr-badge-info">Online</span>',
        IN_PERSON: '<span class="tr-badge tr-badge-primary">In Person</span>',
        HYBRID: '<span class="tr-badge tr-badge-purple">Hybrid</span>',
    };
    return map[s] || `<span class="tr-badge tr-badge-muted">${esc(s)}</span>`;
}

function employeeAvatar(row) {
    const initial = esc((row.full_name || row.employee_name || '?').charAt(0).toUpperCase());
    if (row.profile_photo) {
        return `<img class="tr-avatar" src="${esc(row.profile_photo)}" alt="">`;
    }
    return `<span class="tr-avatar">${initial}</span>`;
}

function empCell(row) {
    const name = row.employee_name || row.full_name || 'Unknown';
    const sub = [row.employee_code, row.department, row.designation].filter(Boolean).join(' · ');
    return `<div class="tr-emp-cell">${employeeAvatar(row)}<div><span class="tr-emp-main">${esc(name)}</span><span class="tr-emp-sub">${esc(sub)}</span></div></div>`;
}

/* ── Modal helpers ───────────────────────────────────── */

function openModal(id) {
    const modal = document.getElementById(id);
    if (modal) modal.classList.add('open');
}

function closeModal(id) {
    const modal = document.getElementById(id);
    if (modal) modal.classList.remove('open');
}

document.addEventListener('click', (e) => {
    if (e.target.classList && e.target.classList.contains('tr-modal')) {
        e.target.classList.remove('open');
    }
    const closer = e.target.closest('[data-close]');
    if (closer) closeModal(closer.getAttribute('data-close'));
});

/* ── Reference data ──────────────────────────────────── */

async function loadReferenceData() {
    try {
        const empData = await api('/api/employees');
        state.employees = empData.employees || [];
        populateEmpSelect('enrollmentEmployee', '');
    } catch (err) { /* dropdown stays empty */ }

    try {
        const progData = await api('/api/training/programs?per_page=100');
        state.programs = progData.programs || [];
        state.categories = [...new Set(state.programs.map(p => p.category).filter(Boolean))].sort();
        populateProgramSelects();
        populateCategoryFilter();
        renderCategoryChips();
    } catch (err) { showToast('Could not load programs.', true); }
}

function populateEmpSelect(id, selectedValue) {
    const select = document.getElementById(id);
    if (!select) return;
    const options = ['<option value="">— Select Employee —</option>',
        ...state.employees.map(emp =>
            `<option value="${emp.id}" ${String(emp.id) === String(selectedValue || '') ? 'selected' : ''}>${esc(emp.full_name)} (${esc(emp.employee_code)})</option>`)].join('');
    select.innerHTML = options;
}

function populateProgramSelects() {
    const options = ['<option value="">— Select Program —</option>',
        ...state.programs.map(p => `<option value="${p.id}">${esc(p.title)}</option>`)].join('');
    const enr = document.getElementById('enrollmentProgramInput');
    if (enr) enr.innerHTML = options;

    const filter = document.getElementById('enrollmentProgram');
    if (filter) {
        filter.innerHTML = '<option value="">All Programs</option>' +
            state.programs.map(p => `<option value="${p.id}">${esc(p.title)}</option>`).join('');
    }
}

function populateCategoryFilter() {
    const select = document.getElementById('programCategory');
    if (!select) return;
    select.innerHTML = '<option value="">All Categories</option>' +
        state.categories.map(cat => `<option value="${esc(cat)}">${esc(cat)}</option>`).join('');
}

function renderCategoryChips() {
    const strip = document.getElementById('categoryStrip');
    const chips = document.getElementById('categoryChips');
    if (!state.categories.length) { strip.style.display = 'none'; return; }
    strip.style.display = 'flex';
    chips.innerHTML = `<button type="button" class="tr-cat-chip ${!state.programFilters.category ? 'active' : ''}" data-cat="">All</button>` +
        state.categories.map(cat =>
            `<button type="button" class="tr-cat-chip ${state.programFilters.category === cat ? 'active' : ''}" data-cat="${esc(cat)}">${esc(cat)}</button>`).join('');

    chips.querySelectorAll('.tr-cat-chip').forEach(btn => {
        btn.addEventListener('click', () => {
            state.programFilters.category = btn.getAttribute('data-cat');
            state.programPage = 1;
            renderCategoryChips();
            loadPrograms();
        });
    });
}

/* ── Summary / KPIs ──────────────────────────────────── */

async function loadSummary() {
    try {
        const data = await api('/api/training/summary');
        const programs = data.programs || {};
        const enrollments = data.enrollments || {};

        document.getElementById('kpiTotalPrograms').textContent = programs.total || 0;
        document.getElementById('kpiActivePrograms').textContent = programs.active || 0;
        document.getElementById('kpiDraftPrograms').textContent = programs.draft || 0;
        document.getElementById('kpiTotalEnrollments').textContent = enrollments.total || 0;
        document.getElementById('kpiInProgress').textContent = enrollments.in_progress || 0;
        document.getElementById('kpiCompletedEnrollments').textContent = enrollments.completed || 0;
        document.getElementById('kpiCertificates').textContent = data.certificates_issued || 0;
        document.getElementById('kpiAvgScore').textContent = Number(enrollments.average_score || 0).toFixed(1);
    } catch (err) {
        showError('Unable to load training summary.');
    }
}

/* ── Programs ────────────────────────────────────────── */

function programQueryString() {
    const params = new URLSearchParams();
    const f = state.programFilters;
    if (f.search) params.set('search', f.search);
    if (f.status) params.set('status', f.status);
    if (f.category) params.set('category', f.category);
    if (f.delivery_mode) params.set('delivery_mode', f.delivery_mode);
    params.set('page', state.programPage);
    params.set('per_page', 10);
    return params.toString();
}

async function loadPrograms() {
    const tbody = document.getElementById('programsTableBody');
    try {
        const data = await api(`/api/training/programs?${programQueryString()}`);
        state.programPagination = data.pagination || state.programPagination;
        const rows = data.programs || [];

        if (!rows.length) {
            tbody.innerHTML = '<tr><td colspan="8" class="tr-empty">No programs found. Click “New Program” to create one.</td></tr>';
            updateProgramPagination();
            return;
        }

        tbody.innerHTML = rows.map(row => `
            <tr>
                <td><strong>${esc(row.title)}</strong></td>
                <td>${esc(row.category || '—')}</td>
                <td>${fmtDate(row.start_date)} → ${fmtDate(row.end_date)}</td>
                <td>${statusBadge(row.delivery_mode)}</td>
                <td>${esc(row.trainer || '—')}</td>
                <td><strong>${row.enrolled_count || 0}</strong>${row.max_participants ? ` / ${row.max_participants}` : ''}</td>
                <td>
                    <div class="rec-status-wrap">
                        <select class="rec-status-select ${programStatusBadgeClass(row.status)}" data-status="${esc(row.status || 'DRAFT')}" onchange="changeProgramStatus(${row.id}, this.value, this)">
                            <option value="ACTIVE" ${(row.status || '').toUpperCase() === 'ACTIVE' ? 'selected' : ''}>● ACTIVE</option>
                            <option value="DRAFT" ${(row.status || '').toUpperCase() === 'DRAFT' ? 'selected' : ''}>● DRAFT</option>
                            <option value="COMPLETED" ${(row.status || '').toUpperCase() === 'COMPLETED' ? 'selected' : ''}>● COMPLETED</option>
                            <option value="CANCELLED" ${(row.status || '').toUpperCase() === 'CANCELLED' ? 'selected' : ''}>● CANCELLED</option>
                        </select>
                    </div>
                </td>
                <td class="tr-col-actions">
                    <button class="tr-link-btn" data-view-program="${row.id}">View</button>
                    <button class="tr-link-btn" data-edit-program="${row.id}">Edit</button>
                    <button class="tr-link-btn danger" data-delete-program="${row.id}">Delete</button>
                </td>
            </tr>`).join('');

        tbody.querySelectorAll('[data-view-program]').forEach(btn =>
            btn.addEventListener('click', () => viewProgram(Number(btn.getAttribute('data-view-program')))));
        tbody.querySelectorAll('[data-edit-program]').forEach(btn =>
            btn.addEventListener('click', () => openProgramForm(Number(btn.getAttribute('data-edit-program')))));
        tbody.querySelectorAll('[data-delete-program]').forEach(btn =>
            btn.addEventListener('click', () => askDelete('program', Number(btn.getAttribute('data-delete-program')))));

        updateProgramPagination();
    } catch (err) {
        tbody.innerHTML = `<tr><td colspan="8" class="tr-empty">${esc(err.message)}</td></tr>`;
    }
}

function updateProgramPagination() {
    const p = state.programPagination;
    document.getElementById('paginationInfo').textContent =
        `Showing ${p.total_items || 0} program(s) — Page ${p.current_page || 1} of ${Math.max(1, p.total_pages || 1)}`;
    document.getElementById('prevPage').disabled = (p.current_page || 1) <= 1;
    document.getElementById('nextPage').disabled = (p.current_page || 1) >= (p.total_pages || 1);
}

/* ── Program form ────────────────────────────────────── */

function resetProgramForm() {
    document.getElementById('programId').value = '';
    document.getElementById('programTitle').value = '';
    document.getElementById('programDescription').value = '';
    document.getElementById('programCategoryInput').value = '';
    document.getElementById('programTrainer').value = '';
    document.getElementById('programStartDate').value = '';
    document.getElementById('programEndDate').value = '';
    document.getElementById('programDuration').value = '';
    document.getElementById('programMaxParticipants').value = '';
    document.getElementById('programLocation').value = '';
    document.getElementById('programDeliveryInput').value = 'IN_PERSON';
    document.getElementById('programStatusInput').value = 'DRAFT';
    document.getElementById('programFormError').textContent = '';
}

function openProgramForm(id = null) {
    resetProgramForm();
    document.getElementById('programModalTitle').textContent = id ? 'Edit Training Program' : 'New Training Program';

    if (id) {
        api(`/api/training/programs/show?id=${id}`).then(data => {
            const p = data.program || {};
            document.getElementById('programId').value = p.id;
            document.getElementById('programTitle').value = p.title || '';
            document.getElementById('programDescription').value = p.description || '';
            document.getElementById('programCategoryInput').value = p.category || '';
            document.getElementById('programTrainer').value = p.trainer || '';
            document.getElementById('programStartDate').value = (p.start_date || '').slice(0, 10);
            document.getElementById('programEndDate').value = (p.end_date || '').slice(0, 10);
            document.getElementById('programDuration').value = p.duration_hours;
            document.getElementById('programMaxParticipants').value = p.max_participants;
            document.getElementById('programLocation').value = p.location || '';
            document.getElementById('programDeliveryInput').value = p.delivery_mode || 'IN_PERSON';
            document.getElementById('programStatusInput').value = p.status || 'DRAFT';
            openModal('programModal');
        }).catch(err => showToast(err.message, true));
        return;
    }

    openModal('programModal');
}

async function saveProgram() {
    const errEl = document.getElementById('programFormError');
    const payload = {
        title: document.getElementById('programTitle').value.trim(),
        description: document.getElementById('programDescription').value || null,
        category: document.getElementById('programCategoryInput').value || null,
        trainer: document.getElementById('programTrainer').value || null,
        start_date: document.getElementById('programStartDate').value || null,
        end_date: document.getElementById('programEndDate').value || null,
        duration_hours: document.getElementById('programDuration').value || null,
        max_participants: document.getElementById('programMaxParticipants').value || null,
        location: document.getElementById('programLocation').value || null,
        delivery_mode: document.getElementById('programDeliveryInput').value,
        status: document.getElementById('programStatusInput').value,
    };

    if (!payload.title) {
        errEl.textContent = 'Program title is required.';
        return;
    }

    const id = document.getElementById('programId').value;
    try {
        if (id) {
            await api(`/api/training/programs/update?id=${id}`, { method: 'PUT', body: JSON.stringify(payload) });
            showToast('Program updated successfully.');
        } else {
            await api('/api/training/programs', { method: 'POST', body: JSON.stringify(payload) });
            showToast('Program created successfully.');
        }
        closeModal('programModal');
        loadPrograms();
        loadSummary();
        loadReferenceData();
    } catch (err) {
        errEl.textContent = err.message;
    }
}

/* ── Program detail view + sessions ──────────────────── */

async function viewProgram(id) {
    state.viewingProgramId = id;
    try {
        const data = await api(`/api/training/programs/show?id=${id}`);
        const p = data.program || {};
        document.getElementById('viewProgramTitle').textContent = p.title || 'Training Program';
        document.getElementById('viewProgramSub').textContent =
            `${esc(p.category || 'Uncategorised')} · ${statusBadge(p.delivery_mode)} · ${statusBadge(p.status)}`;

        const sessions = (p.sessions || []).map(s => `
            <div class="tr-session-row">
                <div>
                    <div class="s-title">${esc(s.title)}</div>
                    <div class="s-meta">${fmtDate(s.session_date)} · ${fmtTime(s.start_time)}–${fmtTime(s.end_time)} · ${esc(s.location || 'No location')}</div>
                </div>
                <div class="s-actions">
                    ${statusBadge(s.status)}
                    <button class="tr-link-btn" data-edit-session="${s.id}">Edit</button>
                    <button class="tr-link-btn danger" data-delete-session="${s.id}">Delete</button>
                </div>
            </div>`).join('') || '<div class="tr-empty-note">No sessions scheduled yet.</div>';

        document.getElementById('viewProgramBody').innerHTML = `
            <div class="tr-detail-grid">
                <div class="tr-detail-item"><small>Trainer</small><strong>${esc(p.trainer || '—')}</strong></div>
                <div class="tr-detail-item"><small>Dates</small><strong>${fmtDate(p.start_date)} → ${fmtDate(p.end_date)}</strong></div>
                <div class="tr-detail-item"><small>Duration</small><strong>${Number(p.duration_hours || 0).toFixed(1)} hrs</strong></div>
                <div class="tr-detail-item"><small>Capacity</small><strong>${p.enrolled_count || 0} / ${p.max_participants || '∞'}</strong></div>
                <div class="tr-detail-item"><small>Location</small><strong>${esc(p.location || '—')}</strong></div>
            </div>
            <div class="tr-desc-block"><h4>Description</h4><p>${esc(p.description || 'No description provided.')}</p></div>
            <div class="tr-sessions-block">
                <div class="tr-sessions-head">
                    <h4>Sessions (${(p.sessions || []).length})</h4>
                    <button type="button" class="tr-secondary-btn" id="addSessionBtn" style="padding:7px 12px">+ Add Session</button>
                </div>
                ${sessions}
            </div>`;

        document.getElementById('addSessionBtn').addEventListener('click', () => openSessionForm(null, id));

        const viewBody = document.getElementById('viewProgramBody');
        viewBody.querySelectorAll('[data-edit-session]').forEach(btn =>
            btn.addEventListener('click', e => {
                e.stopPropagation();
                openSessionForm(Number(btn.getAttribute('data-edit-session')), id);
            }));
        viewBody.querySelectorAll('[data-delete-session]').forEach(btn =>
            btn.addEventListener('click', e => {
                e.stopPropagation();
                askDelete('session', Number(btn.getAttribute('data-delete-session')));
            }));

        openModal('viewProgramModal');
    } catch (err) {
        showToast(err.message, true);
    }
}

/* ── Session form ────────────────────────────────────── */

function resetSessionForm() {
    document.getElementById('sessionId').value = '';
    document.getElementById('sessionProgramId').value = '';
    document.getElementById('sessionTitle').value = '';
    document.getElementById('sessionDescription').value = '';
    document.getElementById('sessionDate').value = '';
    document.getElementById('sessionStartTime').value = '';
    document.getElementById('sessionEndTime').value = '';
    document.getElementById('sessionDuration').value = '';
    document.getElementById('sessionLocation').value = '';
    document.getElementById('sessionTrainer').value = '';
    document.getElementById('sessionStatus').value = 'SCHEDULED';
    document.getElementById('sessionFormError').textContent = '';
}

function openSessionForm(id = null, programId = null) {
    resetSessionForm();
    document.getElementById('sessionProgramId').value = programId || state.viewingProgramId || '';
    document.getElementById('sessionModalTitle').textContent = id ? 'Edit Session' : 'New Session';

    if (id) {
        api(`/api/training/sessions?program_id=${document.getElementById('sessionProgramId').value}`)
            .then(data => {
                const session = (data.sessions || []).find(s => Number(s.id) === Number(id));
                if (!session) { showToast('Session not found.', true); return; }
                document.getElementById('sessionId').value = session.id;
                document.getElementById('sessionTitle').value = session.title || '';
                document.getElementById('sessionDescription').value = session.description || '';
                document.getElementById('sessionDate').value = (session.session_date || '').slice(0, 10);
                document.getElementById('sessionStartTime').value = (session.start_time || '').slice(0, 5);
                document.getElementById('sessionEndTime').value = (session.end_time || '').slice(0, 5);
                document.getElementById('sessionDuration').value = session.duration_hours;
                document.getElementById('sessionLocation').value = session.location || '';
                document.getElementById('sessionTrainer').value = session.trainer || '';
                document.getElementById('sessionStatus').value = session.status || 'SCHEDULED';
                openModal('sessionModal');
            }).catch(err => showToast(err.message, true));
        return;
    }

    openModal('sessionModal');
}

async function saveSession() {
    const errEl = document.getElementById('sessionFormError');
    const payload = {
        program_id: document.getElementById('sessionProgramId').value,
        title: document.getElementById('sessionTitle').value.trim(),
        description: document.getElementById('sessionDescription').value || null,
        session_date: document.getElementById('sessionDate').value || null,
        start_time: document.getElementById('sessionStartTime').value || null,
        end_time: document.getElementById('sessionEndTime').value || null,
        duration_hours: document.getElementById('sessionDuration').value || null,
        location: document.getElementById('sessionLocation').value || null,
        trainer: document.getElementById('sessionTrainer').value || null,
        status: document.getElementById('sessionStatus').value,
    };

    if (!payload.program_id || !payload.title) {
        errEl.textContent = 'Program and session title are required.';
        return;
    }

    const id = document.getElementById('sessionId').value;
    try {
        if (id) {
            await api(`/api/training/sessions/update?id=${id}`, { method: 'PUT', body: JSON.stringify(payload) });
            showToast('Session updated successfully.');
        } else {
            await api('/api/training/sessions', { method: 'POST', body: JSON.stringify(payload) });
            showToast('Session created successfully.');
        }
        closeModal('sessionModal');
        if (state.viewingProgramId) viewProgram(state.viewingProgramId);
    } catch (err) {
        errEl.textContent = err.message;
    }
}

/* ── Enrollments ─────────────────────────────────────── */

function enrollmentQueryString() {
    const params = new URLSearchParams();
    const f = state.enrollmentFilters;
    if (f.search) params.set('search', f.search);
    if (f.status) params.set('status', f.status);
    if (f.program_id) params.set('program_id', f.program_id);
    params.set('page', state.enrollmentPage);
    params.set('per_page', 10);
    return params.toString();
}

async function loadEnrollments() {
    const tbody = document.getElementById('enrollmentsTableBody');
    try {
        const data = await api(`/api/training/enrollments?${enrollmentQueryString()}`);
        state.enrollmentPagination = data.pagination || state.enrollmentPagination;
        const rows = data.enrollments || [];

        if (!rows.length) {
            tbody.innerHTML = '<tr><td colspan="7" class="tr-empty">No enrollments found. Click “New Enrollment” to enroll someone.</td></tr>';
            updateEnrollmentPagination();
            return;
        }

        tbody.innerHTML = rows.map(row => `
            <tr>
                <td>${empCell(row)}</td>
                <td><strong>${esc(row.program_title)}</strong></td>
                <td>${fmtDate(row.enrollment_date)}</td>
                <td>
                    <div class="rec-status-wrap">
                        <select class="rec-status-select ${enrollmentStatusBadgeClass(row.status)}" data-status="${esc(row.status || 'ENROLLED')}" onchange="changeEnrollmentStatus(${row.id}, this.value, this)">
                            <option value="ENROLLED" ${(row.status || '').toUpperCase() === 'ENROLLED' ? 'selected' : ''}>● ENROLLED</option>
                            <option value="IN_PROGRESS" ${(row.status || '').toUpperCase() === 'IN_PROGRESS' ? 'selected' : ''}>● IN PROGRESS</option>
                            <option value="COMPLETED" ${(row.status || '').toUpperCase() === 'COMPLETED' ? 'selected' : ''}>● COMPLETED</option>
                            <option value="DROPPED" ${(row.status || '').toUpperCase() === 'DROPPED' ? 'selected' : ''}>● DROPPED</option>
                        </select>
                    </div>
                </td>
                <td>${row.score !== null ? Number(row.score).toFixed(1) : '—'}</td>
                <td>${Number(row.certificate_issued) ? '<span class="tr-badge tr-badge-success">Issued</span>' : '<span class="tr-badge tr-badge-muted">No</span>'}</td>
                <td class="tr-col-actions">
                    <button class="tr-link-btn" data-edit-enrollment="${row.id}">Update</button>
                    <button class="tr-link-btn danger" data-delete-enrollment="${row.id}">Delete</button>
                </td>
            </tr>`).join('');

        tbody.querySelectorAll('[data-edit-enrollment]').forEach(btn =>
            btn.addEventListener('click', () => openEnrollmentForm(Number(btn.getAttribute('data-edit-enrollment')))));
        tbody.querySelectorAll('[data-delete-enrollment]').forEach(btn =>
            btn.addEventListener('click', () => askDelete('enrollment', Number(btn.getAttribute('data-delete-enrollment')))));

        updateEnrollmentPagination();
    } catch (err) {
        tbody.innerHTML = `<tr><td colspan="7" class="tr-empty">${esc(err.message)}</td></tr>`;
    }
}

function updateEnrollmentPagination() {
    const p = state.enrollmentPagination;
    if (state.activeTab === 'enrollments') {
        document.getElementById('paginationInfo').textContent =
            `Showing ${p.total_items || 0} enrollment(s) — Page ${p.current_page || 1} of ${Math.max(1, p.total_pages || 1)}`;
    }
    document.getElementById('prevPage').disabled = (p.current_page || 1) <= 1;
    document.getElementById('nextPage').disabled = (p.current_page || 1) >= (p.total_pages || 1);
}

/* ── Enrollment form ─────────────────────────────────── */

function resetEnrollmentForm() {
    document.getElementById('enrollmentId').value = '';
    document.getElementById('enrollmentEmployee').value = '';
    document.getElementById('enrollmentProgramInput').value = '';
    document.getElementById('enrollmentDate').value = today();
    document.getElementById('enrollmentStatusInput').value = 'ENROLLED';
    document.getElementById('enrollmentCompletionDate').value = '';
    document.getElementById('enrollmentScore').value = '';
    document.getElementById('enrollmentFeedback').value = '';
    document.getElementById('enrollmentCertIssued').checked = false;
    document.getElementById('certificateBlock').style.display = 'none';
    document.getElementById('certificateNumber').value = '';
    document.getElementById('enrollmentFormError').textContent = '';
}

function openEnrollmentForm(id = null) {
    resetEnrollmentForm();
    document.getElementById('enrollmentModalTitle').textContent = id ? 'Update Enrollment' : 'New Enrollment';

    if (id) {
        api('/api/training/enrollments?per_page=100').then(data => {
            const row = (data.enrollments || []).find(e => Number(e.id) === Number(id));
            if (!row) { showToast('Enrollment not found.', true); return; }
            document.getElementById('enrollmentId').value = row.id;
            populateEmpSelect('enrollmentEmployee', row.employee_id);
            document.getElementById('enrollmentProgramInput').value = row.program_id;
            document.getElementById('enrollmentDate').value = (row.enrollment_date || '').slice(0, 10);
            document.getElementById('enrollmentStatusInput').value = row.status || 'ENROLLED';
            document.getElementById('enrollmentCompletionDate').value = (row.completion_date || '').slice(0, 10);
            document.getElementById('enrollmentScore').value = row.score;
            document.getElementById('enrollmentFeedback').value = row.feedback || '';
            document.getElementById('enrollmentCertIssued').checked = Number(row.certificate_issued) === 1;
            document.getElementById('certificateBlock').style.display = Number(row.certificate_issued) === 1 ? 'block' : 'none';
            openModal('enrollmentModal');
        }).catch(err => showToast(err.message, true));
        return;
    }

    openModal('enrollmentModal');
}

async function saveEnrollment() {
    const errEl = document.getElementById('enrollmentFormError');
    const base = {
        program_id: document.getElementById('enrollmentProgramInput').value,
        employee_id: document.getElementById('enrollmentEmployee').value,
        enrollment_date: document.getElementById('enrollmentDate').value || today(),
        status: document.getElementById('enrollmentStatusInput').value,
        completion_date: document.getElementById('enrollmentCompletionDate').value || null,
        score: document.getElementById('enrollmentScore').value || null,
        feedback: document.getElementById('enrollmentFeedback').value || null,
        certificate_issued: document.getElementById('enrollmentCertIssued').checked ? 1 : 0,
    };

    if (!base.employee_id || !base.program_id) {
        errEl.textContent = 'Employee and program are required.';
        return;
    }

    const id = document.getElementById('enrollmentId').value;
    try {
        if (id) {
            await api(`/api/training/enrollments/update?id=${id}`, { method: 'PUT', body: JSON.stringify(base) });
            showToast('Enrollment updated successfully.');
        } else {
            const createData = await api('/api/training/enrollments', { method: 'POST', body: JSON.stringify(base) });
            const issueCert = document.getElementById('enrollmentCertIssued').checked;
            if (issueCert) {
                const certNumber = document.getElementById('certificateNumber').value.trim();
                const enrollmentId = createData.id;
                if (enrollmentId && certNumber) {
                    await api('/api/training/certificates', {
                        method: 'POST',
                        body: JSON.stringify({ enrollment_id: enrollmentId, certificate_number: certNumber }),
                    });
                }
            }
            showToast('Enrollment created successfully.');
        }
        closeModal('enrollmentModal');
        loadEnrollments();
        loadSummary();
    } catch (err) {
        errEl.textContent = err.message;
    }
}

/* ── Delete confirm ──────────────────────────────────── */

function askDelete(type, id) {
    state.pendingDelete = { type, id };
    const messages = {
        program: 'Are you sure you want to delete this program? Its sessions, enrollments, and certificates will also be removed.',
        session: 'Are you sure you want to delete this session?',
        enrollment: 'Are you sure you want to delete this enrollment?',
    };
    document.getElementById('confirmMessage').textContent = messages[type] || 'Are you sure you want to delete this item?';
    openModal('confirmModal');
}

async function confirmDelete() {
    if (!state.pendingDelete) return;
    const { type, id } = state.pendingDelete;
    const path = `/api/training/${type}s/delete?id=${id}`;
    try {
        await api(path, { method: 'DELETE' });
        showToast(`${type.charAt(0).toUpperCase() + type.slice(1)} deleted.`);
        closeModal('confirmModal');
        state.pendingDelete = null;
        if (type === 'program') { loadPrograms(); loadReferenceData(); }
        if (type === 'enrollment') loadEnrollments();
        if (type === 'session' && state.viewingProgramId) viewProgram(state.viewingProgramId);
        loadSummary();
    } catch (err) {
        showToast(err.message, true);
    }
}

/* ── Event wiring & init ─────────────────────────────── */

function wireEvents() {
    // Tabs
    document.querySelectorAll('.tr-tab').forEach(tab => {
        tab.addEventListener('click', () => {
            document.querySelectorAll('.tr-tab').forEach(t => t.classList.remove('active'));
            tab.classList.add('active');
            state.activeTab = tab.getAttribute('data-tab');
            const showPrograms = state.activeTab === 'programs';
            document.getElementById('programsPanel').style.display = showPrograms ? '' : 'none';
            document.getElementById('enrollmentsPanel').style.display = showPrograms ? 'none' : '';
            if (showPrograms) updateProgramPagination(); else updateEnrollmentPagination();
        });
    });

    // Header action buttons
    document.getElementById('newProgramBtn').addEventListener('click', () => openProgramForm());
    document.getElementById('newEnrollmentBtn').addEventListener('click', () => openEnrollmentForm());

    // Program filters
    let pSearchTimer = null;
    document.getElementById('programSearch').addEventListener('input', e => {
        clearTimeout(pSearchTimer);
        pSearchTimer = setTimeout(() => {
            state.programFilters.search = e.target.value.trim();
            state.programPage = 1;
            loadPrograms();
        }, 350);
    });
    document.getElementById('programStatus').addEventListener('change', e => {
        state.programFilters.status = e.target.value;
        state.programPage = 1;
        loadPrograms();
    });
    document.getElementById('programCategory').addEventListener('change', e => {
        state.programFilters.category = e.target.value;
        state.programPage = 1;
        renderCategoryChips();
        loadPrograms();
    });
    document.getElementById('programDeliveryMode').addEventListener('change', e => {
        state.programFilters.delivery_mode = e.target.value;
        state.programPage = 1;
        loadPrograms();
    });
    document.getElementById('programReset').addEventListener('click', () => {
        state.programFilters = {};
        state.programPage = 1;
        document.getElementById('programSearch').value = '';
        document.getElementById('programStatus').value = '';
        document.getElementById('programCategory').value = '';
        document.getElementById('programDeliveryMode').value = '';
        renderCategoryChips();
        loadPrograms();
    });

    // Enrollment filters
    let eSearchTimer = null;
    document.getElementById('enrollmentSearch').addEventListener('input', e => {
        clearTimeout(eSearchTimer);
        eSearchTimer = setTimeout(() => {
            state.enrollmentFilters.search = e.target.value.trim();
            state.enrollmentPage = 1;
            loadEnrollments();
        }, 350);
    });
    document.getElementById('enrollmentStatus').addEventListener('change', e => {
        state.enrollmentFilters.status = e.target.value;
        state.enrollmentPage = 1;
        loadEnrollments();
    });
    document.getElementById('enrollmentProgram').addEventListener('change', e => {
        state.enrollmentFilters.program_id = e.target.value;
        state.enrollmentPage = 1;
        loadEnrollments();
    });
    document.getElementById('enrollmentReset').addEventListener('click', () => {
        state.enrollmentFilters = {};
        state.enrollmentPage = 1;
        document.getElementById('enrollmentSearch').value = '';
        document.getElementById('enrollmentStatus').value = '';
        document.getElementById('enrollmentProgram').value = '';
        loadEnrollments();
    });

    // Pagination
    document.getElementById('prevPage').addEventListener('click', () => {
        if (state.activeTab === 'programs') {
            if (state.programPage > 1) { state.programPage--; loadPrograms(); }
        } else if (state.enrollmentPage > 1) { state.enrollmentPage--; loadEnrollments(); }
    });
    document.getElementById('nextPage').addEventListener('click', () => {
        if (state.activeTab === 'programs') {
            if (state.programPage < (state.programPagination.total_pages || 1)) { state.programPage++; loadPrograms(); }
        } else if (state.enrollmentPage < (state.enrollmentPagination.total_pages || 1)) { state.enrollmentPage++; loadEnrollments(); }
    });

    // Program form
    document.getElementById('saveProgramBtn').addEventListener('click', saveProgram);

    // Session form
    document.getElementById('saveSessionBtn').addEventListener('click', saveSession);

    // Enrollment form
    document.getElementById('enrollmentCertIssued').addEventListener('change', e => {
        document.getElementById('certificateBlock').style.display = e.target.checked ? 'block' : 'none';
    });
    document.getElementById('saveEnrollmentBtn').addEventListener('click', saveEnrollment);

    // Edit from view modal
    document.getElementById('editProgramBtn').addEventListener('click', () => {
        if (!state.viewingProgramId) return;
        closeModal('viewProgramModal');
        openProgramForm(state.viewingProgramId);
    });

    // Confirm delete
    document.getElementById('confirmDeleteBtn').addEventListener('click', confirmDelete);
}

function init() {
    try {
        wireEvents();
        loadReferenceData();
        loadSummary();
        loadPrograms();
    } catch (err) {
        const banner = document.getElementById('trError');
        if (banner) { banner.textContent = 'Page error: ' + err.message; banner.style.display = 'block'; }
        console.error('Training init failed:', err);
    }
}

document.addEventListener('DOMContentLoaded', init);
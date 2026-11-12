'use strict';

/* =========================================================
   Performance Management JS
   Connects to GET/POST/PUT/DELETE /api/performance/*
========================================================= */

const API_BASE = 'http://localhost/employee-qr-system/backend/public';

const state = {
    activeTab: 'reviews',
    reviewPage: 1,
    goalPage: 1,
    reviewPagination: { total_items: 0, total_pages: 0, current_page: 1 },
    goalPagination: { total_items: 0, total_pages: 0, current_page: 1 },
    reviewFilters: {},
    goalFilters: {},
    employees: [],
    departments: [],
    pendingDelete: null,
    editingReviewId: null,
    viewingReviewId: null,
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
    const el = document.getElementById('pfError');
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

function statusBadge(status) {
    const s = String(status || '').toUpperCase();
    const map = {
        DRAFT: '<span class="pf-badge pf-badge-muted">Draft</span>',
        SUBMITTED: '<span class="pf-badge pf-badge-info">Submitted</span>',
        APPROVED: '<span class="pf-badge pf-badge-primary">Approved</span>',
        COMPLETED: '<span class="pf-badge pf-badge-success">Completed</span>',
        ACTIVE: '<span class="pf-badge pf-badge-primary">Active</span>',
        ARCHIVED: '<span class="pf-badge pf-badge-muted">Archived</span>',
        ENROLLED: '<span class="pf-badge pf-badge-info">Enrolled</span>',
        IN_PROGRESS: '<span class="pf-badge pf-badge-warning">In Progress</span>',
        DROPPED: '<span class="pf-badge pf-badge-danger">Dropped</span>',
        SCHEDULED: '<span class="pf-badge pf-badge-info">Scheduled</span>',
        CANCELLED: '<span class="pf-badge pf-badge-danger">Cancelled</span>',
        ONLINE: '<span class="pf-badge pf-badge-info">Online</span>',
        IN_PERSON: '<span class="pf-badge pf-badge-primary">In Person</span>',
        HYBRID: '<span class="pf-badge pf-badge-purple">Hybrid</span>',
        LOW: '<span class="pf-badge pf-badge-muted">Low</span>',
        MEDIUM: '<span class="pf-badge pf-badge-info">Medium</span>',
        HIGH: '<span class="pf-badge pf-badge-danger">High</span>',
    };
    return map[s] || `<span class="pf-badge pf-badge-muted">${esc(s)}</span>`;
}

function employeeAvatar(row) {
    const initial = esc((row.full_name || row.employee_name || '?').charAt(0).toUpperCase());
    if (row.profile_photo) {
        return `<img class="pf-avatar" src="${esc(row.profile_photo)}" alt="">`;
    }
    return `<span class="pf-avatar">${initial}</span>`;
}

function empCell(row) {
    const name = row.employee_name || row.full_name || 'Unknown';
    const sub = [row.employee_code, row.department, row.designation].filter(Boolean).join(' · ');
    return `<div class="pf-emp-cell">${employeeAvatar(row)}<div><span class="pf-emp-main">${esc(name)}</span><span class="pf-emp-sub">${esc(sub)}</span></div></div>`;
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
    if (e.target.classList && e.target.classList.contains('pf-modal')) {
        e.target.classList.remove('open');
    }
    const closer = e.target.closest('[data-close]');
    if (closer) closeModal(closer.getAttribute('data-close'));
});

/* ── Employees & departments ─────────────────────────── */

async function loadEmployees() {
    try {
        const data = await api('/api/employees');
        state.employees = data.employees || [];
        state.departments = [...new Set(state.employees.map(emp => emp.department).filter(Boolean))].sort();
        populateEmpSelect('reviewEmployee', '');
        populateEmpSelect('reviewReviewer', '');
        populateEmpSelect('goalEmployee', '');
        populateDepartmentFilter();
    } catch (err) {
        showToast('Could not load employees.', true);
    }
}

function populateEmpSelect(id, selectedValue) {
    const select = document.getElementById(id);
    if (!select) return;
    const options = ['<option value="">— Select Employee —</option>',
        ...state.employees.map(emp =>
            `<option value="${emp.id}" ${String(emp.id) === String(selectedValue || '') ? 'selected' : ''}>${esc(emp.full_name)} (${esc(emp.employee_code)})</option>`)].join('');
    select.innerHTML = options;
}

function populateDepartmentFilter() {
    const select = document.getElementById('reviewDepartment');
    if (!select) return;
    select.innerHTML = '<option value="">All Departments</option>' +
        state.departments.map(dep => `<option value="${esc(dep)}">${esc(dep)}</option>`).join('');
}

/* ── Summary / KPIs ──────────────────────────────────── */

async function loadSummary() {
    try {
        const data = await api('/api/performance/summary');
        const reviews = data.reviews || {};
        const goals = data.goals || {};

        document.getElementById('kpiTotalReviews').textContent = reviews.total || 0;
        document.getElementById('kpiCompletedReviews').textContent = reviews.completed || 0;
        document.getElementById('kpiPendingReviews').textContent = reviews.pending || 0;
        document.getElementById('kpiAvgRating').textContent = Number(reviews.average_rating || 0).toFixed(2);
        document.getElementById('kpiActiveGoals').textContent = goals.active || 0;
        document.getElementById('kpiTotalGoals').textContent = goals.total || 0;
        document.getElementById('kpiAvgProgress').textContent = `${Number(goals.average_progress || 0).toFixed(0)}%`;

        renderRatingDistribution(data.rating_distribution || {});
        renderGoalStatus(goals);
    } catch (err) {
        showError('Unable to load performance summary.');
    }
}

function renderRatingDistribution(distribution) {
    const container = document.getElementById('ratingDist');
    const entries = Object.entries(distribution).map(([rating, count]) => ({ rating: Number(rating), count: Number(count) }));
    if (!entries.length) {
        container.innerHTML = '<div class="pf-empty-inline">No rating data yet.</div>';
        return;
    }
    const max = Math.max(...entries.map(e => e.count), 1);
    const colors = {
        1: 'linear-gradient(180deg, #ed4c83 0%, #be185d 100%)',
        2: 'linear-gradient(180deg, #db2777 0%, #9d174d 100%)',
        3: 'linear-gradient(180deg, #c084fc 0%, #7e22ce 100%)',
        4: 'linear-gradient(180deg, #a855f7 0%, #581c87 100%)',
        5: 'linear-gradient(180deg, #701a75 0%, #42134f 100%)'
    };
    container.innerHTML = entries.map(e => {
        const height = Math.max(8, Math.round((e.count / max) * 110));
        const color = colors[e.rating] || 'linear-gradient(180deg, #ed4c83 0%, #be185d 100%)';
        return `<div class="pf-rating-bar">
            <span class="bar-num">${e.count}</span>
            <div class="bar" style="height:${height}px;background:${color}"></div>
            <span class="bar-label">${e.rating}.0</span>
        </div>`;
    }).join('');
}

function renderGoalStatus(goals) {
    const container = document.getElementById('goalStatus');
    const total = goals.total || 0;
    const active = goals.active || 0;
    const completed = goals.completed || 0;
    const archived = Math.max(0, total - active - completed);
    const avg = Math.min(100, Number(goals.average_progress || 0));

    if (!total) {
        container.innerHTML = '<div class="pf-empty-inline">No goal data yet.</div>';
        return;
    }

    const row = (label, count, pct, color) => `
        <div class="pf-goal-row">
            <span class="g-label">${label}</span>
            <div class="g-track"><div class="g-fill" style="width:${Math.min(100, pct)}%;background:${color}"></div></div>
            <span class="g-num">${count}</span>
        </div>`;

    container.innerHTML = row('Active', active, total ? (active / total) * 100 : 0, 'linear-gradient(90deg, #7e22ce 0%, #581c87 100%)') +
        row('Completed', completed, total ? (completed / total) * 100 : 0, 'linear-gradient(90deg, #be185d 0%, #ed4c83 100%)') +
        row('Archived', archived, total ? (archived / total) * 100 : 0, '#ebd7e7') +
        `<div class="pf-goal-row" style="margin-top:6px">
            <span class="g-label" style="width:auto;flex:none;font-weight:800;color:#be185d">Avg Progress</span>
            <div class="g-track" style="flex:1"><div class="g-fill" style="width:${avg}%;background:linear-gradient(90deg, #be185d 0%, #ed4c83 100%)"></div></div>
            <span class="g-num">${avg.toFixed(0)}%</span>
        </div>`;
}

/* ── Reviews ─────────────────────────────────────────── */

function reviewQueryString() {
    const params = new URLSearchParams();
    const f = state.reviewFilters;
    if (f.search) params.set('search', f.search);
    if (f.status) params.set('status', f.status);
    if (f.department) params.set('department', f.department);
    params.set('page', state.reviewPage);
    params.set('per_page', 10);
    return params.toString();
}

async function loadReviews() {
    const tbody = document.getElementById('reviewsTableBody');
    try {
        const data = await api(`/api/performance/reviews?${reviewQueryString()}`);
        state.reviewPagination = data.pagination || state.reviewPagination;
        const rows = data.reviews || [];

        if (!rows.length) {
            tbody.innerHTML = '<tr><td colspan="6" class="pf-empty">No reviews found. Click “New Review” to create one.</td></tr>';
            updateReviewPagination();
            return;
        }

        tbody.innerHTML = rows.map(row => `
            <tr>
                <td>${empCell(row)}</td>
                <td>${fmtDate(row.review_period_start)} → ${fmtDate(row.review_period_end)}</td>
                <td>${fmtDate(row.review_date)}</td>
                <td>${row.overall_rating !== null ? `${Number(row.overall_rating).toFixed(1)} ${stars(row.overall_rating)}` : '<span class="pf-badge pf-badge-muted">Not rated</span>'}</td>
                <td>${statusBadge(row.status)}</td>
                <td class="pf-col-actions">
                    <button class="pf-link-btn" data-view-review="${row.id}">View</button>
                    <button class="pf-link-btn" data-edit-review="${row.id}">Edit</button>
                    <button class="pf-link-btn danger" data-delete-review="${row.id}">Delete</button>
                </td>
            </tr>`).join('');

        tbody.querySelectorAll('[data-view-review]').forEach(btn =>
            btn.addEventListener('click', () => viewReview(Number(btn.getAttribute('data-view-review')))));
        tbody.querySelectorAll('[data-edit-review]').forEach(btn =>
            btn.addEventListener('click', () => openReviewForm(Number(btn.getAttribute('data-edit-review')))));
        tbody.querySelectorAll('[data-delete-review]').forEach(btn =>
            btn.addEventListener('click', () => askDelete('review', Number(btn.getAttribute('data-delete-review')))));

        updateReviewPagination();
    } catch (err) {
        tbody.innerHTML = `<tr><td colspan="6" class="pf-empty">${esc(err.message)}</td></tr>`;
    }
}

function updateReviewPagination() {
    const p = state.reviewPagination;
    document.getElementById('paginationInfo').textContent =
        `Showing ${p.total_items || 0} review(s) — Page ${p.current_page || 1} of ${Math.max(1, p.total_pages || 1)}`;
    document.getElementById('prevPage').disabled = (p.current_page || 1) <= 1;
    document.getElementById('nextPage').disabled = (p.current_page || 1) >= (p.total_pages || 1);
}

/* ── Goals ───────────────────────────────────────────── */

function goalQueryString() {
    const params = new URLSearchParams();
    const f = state.goalFilters;
    if (f.search) params.set('search', f.search);
    if (f.status) params.set('status', f.status);
    if (f.priority) params.set('priority', f.priority);
    params.set('page', state.goalPage);
    params.set('per_page', 10);
    return params.toString();
}

function progressCell(progress) {
    const p = Math.max(0, Math.min(100, Number(progress || 0)));
    const color = p < 30 ? 'linear-gradient(90deg, #db2777 0%, #be185d 100%)' : p < 70 ? 'linear-gradient(90deg, #7e22ce 0%, #a855f7 100%)' : 'linear-gradient(90deg, #be185d 0%, #ed4c83 100%)';
    return `<div style="min-width:150px">
        <div style="display:flex;justify-content:space-between;font-size:11px;color:#756b85;margin-bottom:3px;"><span>Progress</span><strong style="color:#2b1748">${p.toFixed(0)}%</strong></div>
        <div class="pf-progress-track" style="height:8px"><div class="pf-progress-fill" style="width:${p}%;background:${color}"></div></div>
    </div>`;
}

async function loadGoals() {
    const tbody = document.getElementById('goalsTableBody');
    try {
        const data = await api(`/api/performance/goals?${goalQueryString()}`);
        state.goalPagination = data.pagination || state.goalPagination;
        const rows = data.goals || [];

        if (!rows.length) {
            tbody.innerHTML = '<tr><td colspan="7" class="pf-empty">No goals found. Click “New Goal” to create one.</td></tr>';
            updateGoalPagination();
            return;
        }

        tbody.innerHTML = rows.map(row => `
            <tr>
                <td><strong>${esc(row.title)}</strong></td>
                <td>${empCell(row)}</td>
                <td>${statusBadge(row.priority)}</td>
                <td>${progressCell(row.progress)}</td>
                <td>${fmtDate(row.target_date)}</td>
                <td>${statusBadge(row.status)}</td>
                <td class="pf-col-actions">
                    <button class="pf-link-btn" data-edit-goal="${row.id}">Edit</button>
                    <button class="pf-link-btn danger" data-delete-goal="${row.id}">Delete</button>
                </td>
            </tr>`).join('');

        tbody.querySelectorAll('[data-edit-goal]').forEach(btn =>
            btn.addEventListener('click', () => openGoalForm(Number(btn.getAttribute('data-edit-goal')))));
        tbody.querySelectorAll('[data-delete-goal]').forEach(btn =>
            btn.addEventListener('click', () => askDelete('goal', Number(btn.getAttribute('data-delete-goal')))));

        updateGoalPagination();
    } catch (err) {
        tbody.innerHTML = `<tr><td colspan="7" class="pf-empty">${esc(err.message)}</td></tr>`;
    }
}

function updateGoalPagination() {
    const p = state.goalPagination;
    if (state.activeTab === 'goals') {
        document.getElementById('paginationInfo').textContent =
            `Showing ${p.total_items || 0} goal(s) — Page ${p.current_page || 1} of ${Math.max(1, p.total_pages || 1)}`;
    }
    document.getElementById('prevPage').disabled = (p.current_page || 1) <= 1;
    document.getElementById('nextPage').disabled = (p.current_page || 1) >= (p.total_pages || 1);
}

/* ── Review form ─────────────────────────────────────── */

function addRatingRow(data = {}) {
    const container = document.getElementById('ratingsContainer');
    const idx = container.children.length + 1;
    const row = document.createElement('div');
    row.className = 'pf-rating-row';
    row.innerHTML = `
        <span class="idx">${idx}.</span>
        <input class="criteria" placeholder="Criteria (e.g. Teamwork, Quality)" value="${esc(data.criteria)}">
        <select class="rating">
            <option value="1" ${Number(data.rating) === 1 ? 'selected' : ''}>1</option>
            <option value="2" ${Number(data.rating) === 2 ? 'selected' : ''}>2</option>
            <option value="3" ${Number(data.rating) === 3 ? 'selected' : ''}>3</option>
            <option value="4" ${Number(data.rating) === 4 ? 'selected' : ''}>4</option>
            <option value="5" ${Number(data.rating) === 5 ? 'selected' : ''}>5</option>
        </select>
        <input class="weight" type="number" step="0.25" min="0" max="5" placeholder="Wt." value="${esc(data.weight || 1)}">
        <button type="button" class="rm-rating" title="Remove">&times;</button>`;
    container.appendChild(row);
    row.querySelector('.rm-rating').addEventListener('click', () => {
        container.removeChild(row);
        reindexRatings();
    });
}

function reindexRatings() {
    const rows = document.querySelectorAll('#ratingsContainer .pf-rating-row');
    rows.forEach((row, i) => {
        row.querySelector('.idx').textContent = `${i + 1}.`;
    });
}

function resetReviewForm() {
    document.getElementById('reviewId').value = '';
    document.getElementById('reviewEmployee').value = '';
    document.getElementById('reviewReviewer').value = '';
    document.getElementById('reviewPeriodStart').value = '';
    document.getElementById('reviewPeriodEnd').value = '';
    document.getElementById('reviewDate').value = '';
    document.getElementById('reviewOverallRating').value = '';
    document.getElementById('reviewOverallComments').value = '';
    document.getElementById('reviewEmployeeComments').value = '';
    document.getElementById('reviewStatusInput').value = 'DRAFT';
    document.getElementById('reviewFormError').textContent = '';
    document.getElementById('ratingsContainer').innerHTML = '';
    addRatingRow();
    addRatingRow();
    addRatingRow();
}

async function openReviewForm(id = null) {
    state.editingReviewId = id;
    resetReviewForm();
    document.getElementById('reviewModalTitle').textContent = id ? 'Edit Performance Review' : 'New Performance Review';

    if (id) {
        try {
            const data = await api(`/api/performance/reviews/show?id=${id}`);
            const r = data.review || {};
            document.getElementById('reviewId').value = r.id;
            populateEmpSelect('reviewEmployee', r.employee_id);
            populateEmpSelect('reviewReviewer', r.reviewer_id);
            document.getElementById('reviewPeriodStart').value = (r.review_period_start || '').slice(0, 10);
            document.getElementById('reviewPeriodEnd').value = (r.review_period_end || '').slice(0, 10);
            document.getElementById('reviewDate').value = (r.review_date || '').slice(0, 10);
            document.getElementById('reviewOverallRating').value = r.overall_rating;
            document.getElementById('reviewOverallComments').value = r.overall_comments || '';
            document.getElementById('reviewEmployeeComments').value = r.employee_comments || '';
            document.getElementById('reviewStatusInput').value = r.status || 'DRAFT';
            document.getElementById('ratingsContainer').innerHTML = '';
            const ratings = r.ratings && r.ratings.length ? r.ratings : [{}, {}, {}];
            ratings.forEach(rating => addRatingRow(rating));
        } catch (err) {
            showToast(err.message, true);
            return;
        }
    }

    openModal('reviewModal');
}

function collectReviewPayload() {
    const payload = {
        employee_id: document.getElementById('reviewEmployee').value,
        reviewer_id: document.getElementById('reviewReviewer').value || null,
        review_period_start: document.getElementById('reviewPeriodStart').value,
        review_period_end: document.getElementById('reviewPeriodEnd').value,
        review_date: document.getElementById('reviewDate').value || null,
        overall_rating: document.getElementById('reviewOverallRating').value || null,
        overall_comments: document.getElementById('reviewOverallComments').value || null,
        employee_comments: document.getElementById('reviewEmployeeComments').value || null,
        status: document.getElementById('reviewStatusInput').value,
        ratings: [],
    };

    const rows = document.querySelectorAll('#ratingsContainer .pf-rating-row');
    for (const row of rows) {
        const criteria = row.querySelector('.criteria').value.trim();
        if (!criteria) continue;
        payload.ratings.push({
            criteria,
            rating: Number(row.querySelector('.rating').value),
            weight: Number(row.querySelector('.weight').value || 1),
        });
    }

    return payload;
}

async function saveReview() {
    const payload = collectReviewPayload();
    const errEl = document.getElementById('reviewFormError');
    if (!payload.employee_id || !payload.review_period_start || !payload.review_period_end) {
        errEl.textContent = 'Employee and review period dates are required.';
        return;
    }

    const id = document.getElementById('reviewId').value;
    try {
        if (id) {
            await api(`/api/performance/reviews/update?id=${id}`, { method: 'PUT', body: JSON.stringify(payload) });
            showToast('Review updated successfully.');
        } else {
            await api('/api/performance/reviews', { method: 'POST', body: JSON.stringify(payload) });
            showToast('Review created successfully.');
        }
        closeModal('reviewModal');
        loadReviews();
        loadSummary();
    } catch (err) {
        errEl.textContent = err.message;
    }
}

/* ── Review detail view ──────────────────────────────── */

async function viewReview(id) {
    state.viewingReviewId = id;
    try {
        const data = await api(`/api/performance/reviews/show?id=${id}`);
        const r = data.review || {};
        document.getElementById('viewReviewTitle').textContent =
            `Review — ${r.employee_name || 'Employee'}`;
        document.getElementById('viewReviewSub').innerHTML =
            `${fmtDate(r.review_period_start)} → ${fmtDate(r.review_period_end)} · ${statusBadge(r.status)}`;

        const ratingRows = (r.ratings || []).map(rt => `
            <tr>
                <td>${esc(rt.criteria)}</td>
                <td>${Number(rt.rating).toFixed(1)} ${stars(rt.rating)}</td>
                <td>${Number(rt.weight).toFixed(2)}</td>
                <td>${esc(rt.comments || '—')}</td>
            </tr>`).join('');

        document.getElementById('viewReviewBody').innerHTML = `
            <div class="pf-detail-grid">
                <div class="pf-detail-item"><small>Employee</small><strong>${esc(r.employee_name || '—')}</strong></div>
                <div class="pf-detail-item"><small>Department</small><strong>${esc(r.department || '—')}</strong></div>
                <div class="pf-detail-item"><small>Review Date</small><strong>${fmtDate(r.review_date)}</strong></div>
                <div class="pf-detail-item"><small>Overall Rating</small><strong>${Number(r.overall_rating || 0).toFixed(2)} / 5.00</strong></div>
            </div>
            <div class="pf-comment-block">
                <h4>Overall Comments</h4>
                <p>${esc(r.overall_comments || 'No overall comments provided.')}</p>
            </div>
            <div class="pf-comment-block">
                <h4>Employee Comments</h4>
                <p>${esc(r.employee_comments || 'No employee comments provided.')}</p>
            </div>
            <div class="pf-comment-block">
                <h4>Criteria Ratings</h4>
                ${ratingRows
                    ? `<table class="pf-ratings-table"><thead><tr><th>Criteria</th><th>Rating</th><th>Weight</th><th>Comments</th></tr></thead><tbody>${ratingRows}</tbody></table>`
                    : '<p>No criteria ratings for this review.</p>'}
            </div>`;

        openModal('viewReviewModal');
    } catch (err) {
        showToast(err.message, true);
    }
}

/* ── Goal form ───────────────────────────────────────── */

function resetGoalForm() {
    document.getElementById('goalId').value = '';
    document.getElementById('goalEmployee').value = '';
    document.getElementById('goalPriorityInput').value = 'MEDIUM';
    document.getElementById('goalStatusInput').value = 'ACTIVE';
    document.getElementById('goalTargetDate').value = '';
    document.getElementById('goalTitle').value = '';
    document.getElementById('goalDescription').value = '';
    document.getElementById('goalProgress').value = 0;
    document.getElementById('goalFormError').textContent = '';
    updateGoalPreview();
}

function updateGoalPreview() {
    const value = Math.max(0, Math.min(100, Number(document.getElementById('goalProgress').value || 0)));
    document.getElementById('goalProgressPreview').textContent = `${value.toFixed(0)}%`;
    document.getElementById('goalProgressFill').style.width = `${value}%`;
}

function openGoalForm(id = null) {
    resetGoalForm();
    document.getElementById('goalModalTitle').textContent = id ? 'Edit Goal' : 'New Goal';

    if (id) {
        api(`/api/performance/goals/show?id=${id}`).then(data => {
            const g = data.goal || {};
            document.getElementById('goalId').value = g.id;
            populateEmpSelect('goalEmployee', g.employee_id);
            document.getElementById('goalPriorityInput').value = g.priority || 'MEDIUM';
            document.getElementById('goalStatusInput').value = g.status || 'ACTIVE';
            document.getElementById('goalTargetDate').value = (g.target_date || '').slice(0, 10);
            document.getElementById('goalTitle').value = g.title || '';
            document.getElementById('goalDescription').value = g.description || '';
            document.getElementById('goalProgress').value = g.progress || 0;
            updateGoalPreview();
            openModal('goalModal');
        }).catch(err => showToast(err.message, true));
        return;
    }

    openModal('goalModal');
}

async function saveGoal() {
    const errEl = document.getElementById('goalFormError');
    const payload = {
        employee_id: document.getElementById('goalEmployee').value,
        title: document.getElementById('goalTitle').value.trim(),
        description: document.getElementById('goalDescription').value || null,
        target_date: document.getElementById('goalTargetDate').value || null,
        progress: Number(document.getElementById('goalProgress').value || 0),
        status: document.getElementById('goalStatusInput').value,
        priority: document.getElementById('goalPriorityInput').value,
    };

    if (!payload.employee_id || !payload.title) {
        errEl.textContent = 'Employee and goal title are required.';
        return;
    }

    const id = document.getElementById('goalId').value;
    try {
        if (id) {
            await api(`/api/performance/goals/update?id=${id}`, { method: 'PUT', body: JSON.stringify(payload) });
            showToast('Goal updated successfully.');
        } else {
            await api('/api/performance/goals', { method: 'POST', body: JSON.stringify(payload) });
            showToast('Goal created successfully.');
        }
        closeModal('goalModal');
        loadGoals();
        loadSummary();
    } catch (err) {
        errEl.textContent = err.message;
    }
}

/* ── Delete confirm ──────────────────────────────────── */

function askDelete(type, id) {
    state.pendingDelete = { type, id };
    document.getElementById('confirmMessage').textContent =
        type === 'review'
            ? 'Are you sure you want to delete this performance review? Its ratings will also be removed.'
            : 'Are you sure you want to delete this goal?';
    openModal('confirmModal');
}

async function confirmDelete() {
    if (!state.pendingDelete) return;
    const { type, id } = state.pendingDelete;
    const path = type === 'review' ? `/api/performance/reviews/delete?id=${id}` : `/api/performance/goals/delete?id=${id}`;
    try {
        await api(path, { method: 'DELETE' });
        showToast(type === 'review' ? 'Review deleted.' : 'Goal deleted.');
        closeModal('confirmModal');
        state.pendingDelete = null;
        if (type === 'review') loadReviews(); else loadGoals();
        loadSummary();
    } catch (err) {
        showToast(err.message, true);
    }
}

/* ── Event wiring & init ─────────────────────────────── */

function wireEvents() {
    // Tabs
    document.querySelectorAll('.pf-tab').forEach(tab => {
        tab.addEventListener('click', () => {
            document.querySelectorAll('.pf-tab').forEach(t => t.classList.remove('active'));
            tab.classList.add('active');
            state.activeTab = tab.getAttribute('data-tab');
            const showReviews = state.activeTab === 'reviews';
            document.getElementById('reviewsPanel').style.display = showReviews ? '' : 'none';
            document.getElementById('goalsPanel').style.display = showReviews ? 'none' : '';
            if (showReviews) { updateReviewPagination(); } else { updateGoalPagination(); }
        });
    });

    // Header action buttons
    document.getElementById('newReviewBtn').addEventListener('click', () => openReviewForm());
    document.getElementById('newGoalBtn').addEventListener('click', () => openGoalForm());

    // Review filters
    let rSearchTimer = null;
    document.getElementById('reviewSearch').addEventListener('input', e => {
        clearTimeout(rSearchTimer);
        rSearchTimer = setTimeout(() => {
            state.reviewFilters.search = e.target.value.trim();
            state.reviewPage = 1;
            loadReviews();
        }, 350);
    });
    document.getElementById('reviewStatus').addEventListener('change', e => {
        state.reviewFilters.status = e.target.value;
        state.reviewPage = 1;
        loadReviews();
    });
    document.getElementById('reviewDepartment').addEventListener('change', e => {
        state.reviewFilters.department = e.target.value;
        state.reviewPage = 1;
        loadReviews();
    });
    document.getElementById('reviewReset').addEventListener('click', () => {
        state.reviewFilters = {};
        state.reviewPage = 1;
        document.getElementById('reviewSearch').value = '';
        document.getElementById('reviewStatus').value = '';
        document.getElementById('reviewDepartment').value = '';
        loadReviews();
    });

    // Goal filters
    let gSearchTimer = null;
    document.getElementById('goalSearch').addEventListener('input', e => {
        clearTimeout(gSearchTimer);
        gSearchTimer = setTimeout(() => {
            state.goalFilters.search = e.target.value.trim();
            state.goalPage = 1;
            loadGoals();
        }, 350);
    });
    document.getElementById('goalStatus').addEventListener('change', e => {
        state.goalFilters.status = e.target.value;
        state.goalPage = 1;
        loadGoals();
    });
    document.getElementById('goalPriority').addEventListener('change', e => {
        state.goalFilters.priority = e.target.value;
        state.goalPage = 1;
        loadGoals();
    });
    document.getElementById('goalReset').addEventListener('click', () => {
        state.goalFilters = {};
        state.goalPage = 1;
        document.getElementById('goalSearch').value = '';
        document.getElementById('goalStatus').value = '';
        document.getElementById('goalPriority').value = '';
        loadGoals();
    });

    // Pagination
    document.getElementById('prevPage').addEventListener('click', () => {
        if (state.activeTab === 'reviews') {
            if (state.reviewPage > 1) { state.reviewPage--; loadReviews(); }
        } else if (state.goalPage > 1) { state.goalPage--; loadGoals(); }
    });
    document.getElementById('nextPage').addEventListener('click', () => {
        if (state.activeTab === 'reviews') {
            if (state.reviewPage < (state.reviewPagination.total_pages || 1)) { state.reviewPage++; loadReviews(); }
        } else if (state.goalPage < (state.goalPagination.total_pages || 1)) { state.goalPage++; loadGoals(); }
    });

    // Review form
    document.getElementById('addRatingBtn').addEventListener('click', () => addRatingRow());
    document.getElementById('saveReviewBtn').addEventListener('click', saveReview);

    // Goal form
    document.getElementById('goalProgress').addEventListener('input', updateGoalPreview);
    document.getElementById('saveGoalBtn').addEventListener('click', saveGoal);

    // Edit from view modal
    document.getElementById('editReviewBtn').addEventListener('click', () => {
        if (!state.viewingReviewId) return;
        closeModal('viewReviewModal');
        openReviewForm(state.viewingReviewId);
    });

    // Confirm delete
    document.getElementById('confirmDeleteBtn').addEventListener('click', confirmDelete);
}

function init() {
    try {
        wireEvents();
        loadEmployees();
        loadSummary();
        loadReviews();
        loadGoals();
    } catch (err) {
        const banner = document.getElementById('pfError');
        if (banner) { banner.textContent = 'Page error: ' + err.message; banner.style.display = 'block'; }
        console.error('Performance init failed:', err);
    }
}

document.addEventListener('DOMContentLoaded', init);

function stars(rating) {
    const r = Number(rating || 0);
    const filled = Math.round(r);
    let out = '';
    for (let i = 1; i <= 5; i++) {
        out += `<span class="${i <= filled ? '' : 'empty'}">★</span>`;
    }
    return `<span class="pf-stars">${out}</span>`;
}
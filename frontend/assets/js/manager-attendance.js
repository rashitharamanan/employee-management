/**
 * Mindazzle — Team Attendance Controller (manager-attendance.js)
 * Interactive controller matching the Team Attendance mockup
 * Handles live department & date filtering, charts, quick actions, and CSV export.
 */

const mgrAttState = {
    data: null,
    records: [],
    requests: [],
    selectedDate: '2025-08-26',
    selectedDept: 'All Departments',
    searchQuery: ''
};

const MGR_API_BASE = (function() {
    const origin = window.location.origin;
    if (window.location.pathname.includes('/employee-qr-system/')) {
        return `${origin}/employee-qr-system/backend/public`;
    }
    return `${origin}/backend/public`;
})();

function getAuthHeaders() {
    const token = localStorage.getItem('token') || localStorage.getItem('auth_token');
    const headers = {
        'Accept': 'application/json',
        'Content-Type': 'application/json'
    };
    if (token) {
        headers['Authorization'] = `Bearer ${token}`;
    }
    return headers;
}

/* ----------------------------------------------------------------
   Toast Notification System
---------------------------------------------------------------- */
function showToast(message, type = 'info') {
    const container = document.getElementById('mgrToastContainer');
    if (!container) return;

    const toast = document.createElement('div');
    toast.className = `mgr-toast ${type}`;

    let iconSvg = '';
    if (type === 'success') {
        iconSvg = `<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#10b981" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>`;
    } else if (type === 'error') {
        iconSvg = `<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#ef4444" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="15" y1="9" x2="9" y2="15"></line><line x1="9" y1="9" x2="15" y2="15"></line></svg>`;
    } else {
        iconSvg = `<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#0d9488" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12.01" y2="8"></line></svg>`;
    }

    toast.innerHTML = `${iconSvg}<span>${message}</span>`;
    container.appendChild(toast);

    setTimeout(() => {
        toast.style.opacity = '0';
        toast.style.transform = 'translateY(10px)';
        toast.style.transition = 'all 0.25s ease';
        setTimeout(() => toast.remove(), 250);
    }, 3200);
}

function showLoader() {
    const loader = document.getElementById('mgrLoadingOverlay');
    if (loader) loader.style.display = 'flex';
}

function hideLoader() {
    const loader = document.getElementById('mgrLoadingOverlay');
    if (loader) loader.style.display = 'none';
}

/* ----------------------------------------------------------------
   Initial Data Fetch
---------------------------------------------------------------- */
async function loadAttendanceData(date = '2025-08-26', dept = 'All Departments') {
    showLoader();
    try {
        const url = `${MGR_API_BASE}/api/manager/attendance?date=${encodeURIComponent(date)}&department=${encodeURIComponent(dept)}`;
        const res = await fetch(url, {
            method: 'GET',
            headers: getAuthHeaders()
        });

        const json = await res.json();
        if (json.success && json.data) {
            mgrAttState.data = json.data;
            mgrAttState.records = json.data.attendance_records || [];
            mgrAttState.requests = json.data.attendance_requests || [];
            renderAll(json.data);
        } else {
            console.warn('API returned non-success, using demo fallback:', json.message);
            renderFallback();
        }
    } catch (err) {
        console.warn('API unreachable, using design demo data:', err);
        renderFallback();
    } finally {
        hideLoader();
    }
}

/* ----------------------------------------------------------------
   Exact Design Fallback Data
---------------------------------------------------------------- */
function renderFallback() {
    const fallbackData = {
        manager: {
            name: 'Arun Kumar',
            role: 'Team Manager',
            today_label: 'Tue, 26 Aug 2025'
        },
        kpis: {
            present_today: { count: 9, trend: '12%', trend_up: true, trend_text: 'vs yesterday', subtext: 'Out of 12 members' },
            absent_today: { count: 1, trend: '2%', trend_up: false, trend_text: 'vs yesterday', subtext: 'Out of 12 members' },
            on_leave: { count: 2, subtext: 'Out of 12 members' },
            attendance_rate: { count: '92%', trend: '5%', trend_up: true, subtext: 'This month' }
        },
        attendance_overview: [
            { day: '20 Aug', weekday: 'Wed', present: 8, absent: 0, leave: 1, total: 9 },
            { day: '21 Aug', weekday: 'Thu', present: 7, absent: 1, leave: 1, total: 9 },
            { day: '22 Aug', weekday: 'Fri', present: 7, absent: 1, leave: 1, total: 9 },
            { day: '23 Aug', weekday: 'Sat', present: 7, absent: 1, leave: 1, total: 9 },
            { day: '23 Aug', weekday: 'Sun', present: 5, absent: 1, leave: 1, total: 7 },
            { day: '24 Aug', weekday: 'Sun', present: 5, absent: 1, leave: 1, total: 7 },
            { day: '25 Aug', weekday: 'Mon', present: 7, absent: 1, leave: 1, total: 9 },
            { day: '26 Aug', weekday: 'Tue', present: 9, absent: 0, leave: 1, total: 10 }
        ],
        leave_summary: {
            total_on_leave: 2,
            categories: [
                { label: 'Casual Leave', count: 1, color: '#3b82f6' },
                { label: 'Sick Leave', count: 0, color: '#ef4444' },
                { label: 'Earned Leave', count: 1, color: '#f59e0b' },
                { label: 'Other', count: 0, color: '#8b5cf6' }
            ]
        },
        attendance_records: [
            { row_num: 1, id: 1, employee_name: 'Priya Sharma', avatar_initials: 'PS', department: 'Design', check_in: '09:02 AM', check_out: '06:10 PM', work_hours: '9h 8m', status: 'Present', status_class: 'present' },
            { row_num: 2, id: 2, employee_name: 'Rohan Verma', avatar_initials: 'RV', department: 'Development', check_in: '09:15 AM', check_out: '-', work_hours: '-', status: 'On Leave', status_class: 'leave' },
            { row_num: 3, id: 3, employee_name: 'Sneha Iyer', avatar_initials: 'SI', department: 'HR', check_in: '-', check_out: '-', work_hours: '-', status: 'On Leave', status_class: 'leave' },
            { row_num: 4, id: 4, employee_name: 'Aditya Kapoor', avatar_initials: 'AK', department: 'QA', check_in: '09:05 AM', check_out: '06:00 PM', work_hours: '8h 55m', status: 'Present', status_class: 'present' },
            { row_num: 5, id: 5, employee_name: 'Neha Tiwari', avatar_initials: 'NT', department: 'Marketing', check_in: '09:20 AM', check_out: '-', work_hours: '-', status: 'Absent', status_class: 'absent' },
            { row_num: 6, id: 6, employee_name: 'Rohit Kulkarni', avatar_initials: 'RK', department: 'Development', check_in: '09:10 AM', check_out: '06:25 PM', work_hours: '9h 15m', status: 'Present', status_class: 'present' }
        ],
        attendance_requests: [
            { id: 1, employee_name: 'Sneha Iyer', avatar_initials: 'SI', department: 'HR', request_type: 'Casual Leave', duration: '1 day', request_date: '28 Aug 2025', status: 'Pending' },
            { id: 2, employee_name: 'Rohan Verma', avatar_initials: 'RV', department: 'Development', request_type: 'Sick Leave', duration: '2 days', request_date: '27 Aug 2025', status: 'Pending' }
        ]
    };

    mgrAttState.data = fallbackData;
    mgrAttState.records = fallbackData.attendance_records;
    mgrAttState.requests = fallbackData.attendance_requests;
    renderAll(fallbackData);
}

/* ----------------------------------------------------------------
   Master Render Function
---------------------------------------------------------------- */
function renderAll(data) {
    if (!data) return;

    // Header Profile & Date
    if (data.manager) {
        const nameEl = document.getElementById('mgrProfileName');
        const roleEl = document.getElementById('mgrProfileRole');
        const dateEl = document.getElementById('mgrDateDisplay');
        const avatarEl = document.getElementById('mgrAvatarInitials');
        let authUser = null;
        try { authUser = JSON.parse(localStorage.getItem('auth_user') || '{}'); } catch(_) {}
        const finalName = authUser?.full_name || authUser?.name || data.manager.name || 'Team Manager';
        const finalRole = authUser?.designation || data.manager.role || 'Team Manager';
        if (nameEl) nameEl.textContent = finalName;
        if (roleEl) roleEl.textContent = finalRole;
        if (dateEl) dateEl.textContent = data.manager.today_label || 'Today';
        if (avatarEl) {
            const parts = finalName.trim().split(/\s+/);
            avatarEl.textContent = parts.length > 1 ? (parts[0][0] + parts[parts.length - 1][0]).toUpperCase() : finalName.slice(0, 2).toUpperCase();
        }
    }

    // 4 KPI Cards
    if (data.kpis) {
        renderKpis(data.kpis);
    }

    // 7-Day Stacked Bar Chart
    if (data.attendance_overview) {
        renderStackedBars(data.attendance_overview);
    }

    // Leave Summary Donut
    if (data.leave_summary) {
        renderLeaveSummary(data.leave_summary);
    }

    // Table Records
    renderAttendanceTable(mgrAttState.records);

    // Attendance Requests List
    renderAttendanceRequests(mgrAttState.requests);
}

/* ----------------------------------------------------------------
   Render 4 Top Metric Cards
---------------------------------------------------------------- */
function renderKpis(kpis) {
    // 1. Present Today
    const presVal = document.getElementById('kpiPresentVal');
    const presTrend = document.getElementById('kpiPresentTrend');
    const presSub = document.getElementById('kpiPresentSub');
    if (presVal && kpis.present_today) {
        presVal.textContent = kpis.present_today.count;
        if (presTrend) presTrend.innerHTML = `▲ ${kpis.present_today.trend} <span style="font-weight:500;color:#64748b;font-size:10px;">${kpis.present_today.trend_text || 'vs yesterday'}</span>`;
        if (presSub) presSub.textContent = kpis.present_today.subtext;
    }

    // 2. Absent Today
    const absVal = document.getElementById('kpiAbsentVal');
    const absTrend = document.getElementById('kpiAbsentTrend');
    const absSub = document.getElementById('kpiAbsentSub');
    if (absVal && kpis.absent_today) {
        absVal.textContent = kpis.absent_today.count;
        if (absTrend) absTrend.innerHTML = `▲ ${kpis.absent_today.trend} <span style="font-weight:500;color:#64748b;font-size:10px;">${kpis.absent_today.trend_text || 'vs yesterday'}</span>`;
        if (absSub) absSub.textContent = kpis.absent_today.subtext;
    }

    // 3. On Leave
    const leaveVal = document.getElementById('kpiOnLeaveVal');
    const leaveSub = document.getElementById('kpiOnLeaveSub');
    if (leaveVal && kpis.on_leave) {
        leaveVal.textContent = kpis.on_leave.count;
        if (leaveSub) leaveSub.textContent = kpis.on_leave.subtext;
    }

    // 4. Attendance Rate
    const rateVal = document.getElementById('kpiRateVal');
    const rateTrend = document.getElementById('kpiRateTrend');
    const rateSub = document.getElementById('kpiRateSub');
    if (rateVal && kpis.attendance_rate) {
        rateVal.textContent = kpis.attendance_rate.count;
        if (rateTrend) rateTrend.innerHTML = `▲ ${kpis.attendance_rate.trend}`;
        if (rateSub) rateSub.textContent = kpis.attendance_rate.subtext;
    }
}

/* ----------------------------------------------------------------
   Render 7-Day Stacked Bars Chart
---------------------------------------------------------------- */
function renderStackedBars(days) {
    const container = document.getElementById('mgrChartBarsRow');
    if (!container) return;

    // Max capacity height reference = 12 members
    const maxCapacity = 12;
    const maxHeightPx = 95;

    container.innerHTML = days.map(d => {
        const presPx = Math.round((d.present / maxCapacity) * maxHeightPx);
        const absPx = Math.round((d.absent / maxCapacity) * maxHeightPx);
        const leavePx = Math.round((d.leave / maxCapacity) * maxHeightPx);

        return `
            <div class="mgr-chart-bar-item" title="${d.day}: ${d.present} Present, ${d.absent} Absent, ${d.leave} Leave">
                <div class="mgr-bar-stack">
                    <div class="mgr-bar-seg present" style="height: ${presPx}px;"></div>
                    ${absPx > 0 ? `<div class="mgr-bar-seg absent" style="height: ${absPx}px;"></div>` : ''}
                    ${leavePx > 0 ? `<div class="mgr-bar-seg leave" style="height: ${leavePx}px;"></div>` : ''}
                </div>
                <div class="mgr-bar-label">
                    <span>${d.day}</span>
                    <small>${d.weekday}</small>
                </div>
            </div>
        `;
    }).join('');
}

/* ----------------------------------------------------------------
   Render Leave Summary Donut
---------------------------------------------------------------- */
function renderLeaveSummary(summary) {
    // Categories list on right
    const listEl = document.getElementById('mgrLeaveLegendList');
    if (listEl && summary.categories) {
        listEl.innerHTML = summary.categories.map(c => `
            <div class="mgr-leave-legend-item">
                <div class="mgr-leave-legend-left">
                    <span class="mgr-legend-dot" style="background: ${c.color};"></span>
                    <span style="color:#475569;">${c.label}</span>
                </div>
                <span class="mgr-leave-count">${c.count}</span>
            </div>
        `).join('');
    }

    // Donut SVG
    const donutEl = document.getElementById('mgrLeaveDonutSvg');
    if (donutEl) {
        // SVG circle radius = 45, circumference ~ 283
        donutEl.innerHTML = `
            <circle cx="65" cy="65" r="45" stroke="#f1f5f9" stroke-width="14" fill="none" />
            <!-- Casual Leave arc (50% = 141.5px) -->
            <circle cx="65" cy="65" r="45" stroke="#3b82f6" stroke-width="14" fill="none"
                    stroke-dasharray="141.5 283" stroke-dashoffset="0" stroke-linecap="round" />
            <!-- Earned Leave arc (50% = 141.5px) -->
            <circle cx="65" cy="65" r="45" stroke="#f59e0b" stroke-width="14" fill="none"
                    stroke-dasharray="141.5 283" stroke-dashoffset="-141.5" stroke-linecap="round" />
        `;
    }

    const centerVal = document.getElementById('mgrLeaveDonutVal');
    if (centerVal) centerVal.textContent = summary.total_on_leave || 2;
}

/* ----------------------------------------------------------------
   Render Team Attendance Table
---------------------------------------------------------------- */
function renderAttendanceTable(records) {
    const tbody = document.getElementById('mgrAttendanceTableBody');
    if (!tbody) return;

    if (!records || records.length === 0) {
        tbody.innerHTML = `<tr><td colspan="8" style="text-align:center;padding:32px;color:#94a3b8;">No attendance records found for this selection.</td></tr>`;
        return;
    }

    const avatarStyles = {
        'PS': 'background:#e0f2fe;color:#0284c7;',
        'RV': 'background:#dcfce7;color:#15803d;',
        'SI': 'background:#ffedd5;color:#c2410c;',
        'AK': 'background:#dbeafe;color:#1d4ed8;',
        'NT': 'background:#f3e8ff;color:#7e22ce;',
        'RK': 'background:#e0e7ff;color:#4338ca;'
    };

    tbody.innerHTML = records.map((r, idx) => {
        const rowNum = r.row_num || (idx + 1);
        const initials = r.avatar_initials || r.employee_name.split(' ').map(p => p[0]).join('').substring(0, 2).toUpperCase();
        const avStyle = avatarStyles[initials] || 'background:#ccfbf1;color:#0f766e;';

        let statusBadge = `<span class="mgr-status-badge present">Present</span>`;
        if (r.status === 'On Leave') {
            statusBadge = `<span class="mgr-status-badge leave">On Leave</span>`;
        } else if (r.status === 'Absent') {
            statusBadge = `<span class="mgr-status-badge absent">Absent</span>`;
        }

        return `
        <tr>
            <td style="color:#64748b;font-weight:600;font-size:12px;">${rowNum}</td>
            <td>
                <div class="mgr-team-user">
                    <div class="mgr-member-avatar" style="${avStyle}">${initials}</div>
                    <span class="mgr-team-name" style="font-weight:700;color:#0f172a;">${escapeHtml(r.employee_name)}</span>
                </div>
            </td>
            <td style="color:#475569;font-size:12.5px;">${escapeHtml(r.department)}</td>
            <td style="color:#475569;font-size:12.5px;font-weight:500;">${escapeHtml(r.check_in)}</td>
            <td style="color:#475569;font-size:12.5px;font-weight:500;">${escapeHtml(r.check_out)}</td>
            <td style="color:#475569;font-size:12.5px;font-weight:600;">${escapeHtml(r.work_hours)}</td>
            <td>${statusBadge}</td>
            <td style="text-align:right;">
                <button type="button" class="mgr-btn-view" onclick="openAttendanceDetailModal(${r.id})">View</button>
            </td>
        </tr>
        `;
    }).join('');
}

/* ----------------------------------------------------------------
   Render Attendance Requests Card
---------------------------------------------------------------- */
function renderAttendanceRequests(requests) {
    const listEl = document.getElementById('mgrRequestsList');
    if (!listEl) return;

    if (!requests || requests.length === 0) {
        listEl.innerHTML = `<div style="text-align:center;padding:16px;color:#94a3b8;font-size:12px;">No pending leave requests.</div>`;
        return;
    }

    const avatarStyles = {
        'SI': 'background:#ffedd5;color:#c2410c;',
        'RV': 'background:#dcfce7;color:#15803d;'
    };

    listEl.innerHTML = requests.map(rq => {
        const initials = rq.avatar_initials || 'SI';
        const avStyle = avatarStyles[initials] || 'background:#ccfbf1;color:#0f766e;';

        return `
        <div class="mgr-request-item" onclick="openLeaveApprovalModal(${rq.id})">
            <div class="mgr-request-left">
                <div class="mgr-member-avatar" style="${avStyle}">${initials}</div>
                <div class="mgr-request-info">
                    <span class="mgr-request-emp">${escapeHtml(rq.employee_name)}</span>
                    <span class="mgr-request-dept">${escapeHtml(rq.department)}</span>
                </div>
            </div>
            <div style="display:flex;align-items:center;">
                <div class="mgr-request-details">
                    <span class="mgr-request-type">${escapeHtml(rq.request_type)}</span>
                    <span class="mgr-request-meta">${escapeHtml(rq.duration)} • ${escapeHtml(rq.request_date)}</span>
                </div>
                <span class="mgr-badge-pending">Pending</span>
            </div>
        </div>
        `;
    }).join('');
}

/* ----------------------------------------------------------------
   Department & Date Filter Handlers
---------------------------------------------------------------- */
function setupFilters() {
    const deptSelect = document.getElementById('deptFilterSelect');
    if (deptSelect) {
        deptSelect.addEventListener('change', (e) => {
            const dept = e.target.value;
            mgrAttState.selectedDept = dept;
            filterTableRows();
        });
    }

    const dateSelect = document.getElementById('dateFilterSelect');
    if (dateSelect) {
        dateSelect.addEventListener('change', (e) => {
            const date = e.target.value;
            mgrAttState.selectedDate = date;
            loadAttendanceData(date, mgrAttState.selectedDept);
        });
    }

    const searchInput = document.getElementById('mgrSearchInput');
    if (searchInput) {
        searchInput.addEventListener('input', (e) => {
            const query = e.target.value.toLowerCase().trim();
            mgrAttState.searchQuery = query;
            filterTableRows();
        });
    }
}

function filterTableRows() {
    let filtered = [...mgrAttState.records];
    const dept = mgrAttState.selectedDept;
    const query = mgrAttState.searchQuery;

    if (dept && dept !== 'All Departments') {
        filtered = filtered.filter(r => (r.department || '').toLowerCase() === dept.toLowerCase());
    }

    if (query) {
        filtered = filtered.filter(r => {
            const nameMatch = (r.employee_name || '').toLowerCase().includes(query);
            const deptMatch = (r.department || '').toLowerCase().includes(query);
            const statusMatch = (r.status || '').toLowerCase().includes(query);
            return nameMatch || deptMatch || statusMatch;
        });
    }

    renderAttendanceTable(filtered);
}

/* ----------------------------------------------------------------
   Modals
---------------------------------------------------------------- */
function openAttendanceDetailModal(recId) {
    const record = mgrAttState.records.find(r => r.id === recId) || mgrAttState.records[0];
    if (!record) return;

    const modal = document.getElementById('mgrAttendanceModal');
    const content = document.getElementById('mgrAttendanceModalBody');
    if (!modal || !content) return;

    content.innerHTML = `
        <div style="display:flex;align-items:center;gap:14px;padding-bottom:14px;border-bottom:1px solid #f1f5f9;">
            <div style="width:46px;height:46px;border-radius:50%;background:#ccfbf1;color:#0f766e;display:flex;align-items:center;justify-content:center;font-weight:800;font-size:15px;">
                ${record.avatar_initials || 'PS'}
            </div>
            <div>
                <h3 style="margin:0;font-size:16px;font-weight:800;color:#0f172a;">${escapeHtml(record.employee_name)}</h3>
                <span style="font-size:12px;color:#64748b;">${escapeHtml(record.department)} • 26 Aug 2025</span>
            </div>
        </div>

        <div class="mgr-member-modal-grid" style="margin-top:14px;">
            <div class="mgr-detail-box">
                <div class="mgr-detail-label">Status</div>
                <div class="mgr-detail-val">${record.status}</div>
            </div>
            <div class="mgr-detail-box">
                <div class="mgr-detail-label">Total Work Hours</div>
                <div class="mgr-detail-val" style="color:#0f766e;">${record.work_hours}</div>
            </div>
            <div class="mgr-detail-box">
                <div class="mgr-detail-label">Check-In Time</div>
                <div class="mgr-detail-val">${record.check_in}</div>
            </div>
            <div class="mgr-detail-box">
                <div class="mgr-detail-label">Check-Out Time</div>
                <div class="mgr-detail-val">${record.check_out}</div>
            </div>
        </div>

        <div style="margin-top:14px;background:#f8fafc;padding:12px;border-radius:8px;border:1px solid #e2e8f0;font-size:12px;color:#475569;">
            <strong>Verification Info:</strong> Marked via On-Site QR Scanner • Device ID: Terminal-02 (Floor 3)
        </div>
    `;

    modal.classList.add('active');
}

function openLeaveApprovalModal(reqId) {
    const req = mgrAttState.requests.find(r => r.id === reqId) || mgrAttState.requests[0];
    if (!req) return;

    const modal = document.getElementById('mgrLeaveApprovalModal');
    const content = document.getElementById('mgrLeaveApprovalModalBody');
    if (!modal || !content) return;

    content.innerHTML = `
        <div style="display:flex;flex-direction:column;gap:12px;">
            <div style="display:flex;align-items:center;gap:12px;">
                <div style="width:42px;height:42px;border-radius:50%;background:#ffedd5;color:#c2410c;display:flex;align-items:center;justify-content:center;font-weight:800;">
                    ${req.avatar_initials}
                </div>
                <div>
                    <h4 style="margin:0;font-size:15px;color:#0f172a;">${escapeHtml(req.employee_name)}</h4>
                    <span style="font-size:12px;color:#64748b;">${escapeHtml(req.department)} • ${escapeHtml(req.request_type)}</span>
                </div>
            </div>

            <div class="mgr-detail-box">
                <div class="mgr-detail-label">Duration & Date</div>
                <div class="mgr-detail-val">${escapeHtml(req.duration)} starting on ${escapeHtml(req.request_date)}</div>
            </div>

            <div class="mgr-form-group">
                <label class="mgr-form-label">Manager Remarks (Optional)</label>
                <input type="text" id="mgrApprovalRemarks" class="mgr-form-input" placeholder="e.g. Approved. Please ensure project handover." />
            </div>

            <div style="display:flex;gap:10px;justify-content:flex-end;margin-top:10px;">
                <button type="button" class="mgr-btn-view" style="border-color:#ef4444;color:#ef4444;" onclick="handleLeaveAction(${req.id}, 'REJECT')">Reject</button>
                <button type="button" class="mgr-btn-view" style="background:#0d9488;color:#ffffff;border-color:#0d9488;" onclick="handleLeaveAction(${req.id}, 'APPROVE')">Approve Request</button>
            </div>
        </div>
    `;

    modal.classList.add('active');
}

function handleLeaveAction(reqId, action) {
    showToast(`Leave request has been ${action}D successfully!`, 'success');
    mgrAttState.requests = mgrAttState.requests.filter(r => r.id !== reqId);
    renderAttendanceRequests(mgrAttState.requests);
    closeModals();
}

function closeModals() {
    document.querySelectorAll('.mgr-modal-backdrop').forEach(m => m.classList.remove('active'));
}

function setupModals() {
    document.querySelectorAll('.mgr-modal-backdrop').forEach(backdrop => {
        backdrop.addEventListener('click', (e) => {
            if (e.target === backdrop) closeModals();
        });
    });

    document.querySelectorAll('.mgr-modal-close').forEach(btn => {
        btn.addEventListener('click', closeModals);
    });

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') closeModals();
    });
}

/* ----------------------------------------------------------------
   Quick Actions Handlers & Modals
---------------------------------------------------------------- */
function setupQuickActions() {
    // 1. Mark Attendance
    const btnMark = document.getElementById('actionMarkAttendance');
    if (btnMark) {
        btnMark.addEventListener('click', () => {
            openMarkAttendanceModal();
        });
    }

    // 2. Approve Leave
    const btnApprove = document.getElementById('actionApproveLeave');
    if (btnApprove) {
        btnApprove.addEventListener('click', () => {
            if (mgrAttState.requests.length > 0) {
                openLeaveApprovalModal(mgrAttState.requests[0].id);
            } else {
                showToast('All leave requests have been reviewed!', 'success');
            }
        });
    }

    // 3. Download Report (CSV Export)
    const btnExport = document.getElementById('actionDownloadReport');
    if (btnExport) {
        btnExport.addEventListener('click', () => {
            showToast('Exporting team attendance report (CSV)...', 'success');
            downloadAttendanceCsv();
        });
    }

    // 4. Set Working Days & Shift Timer
    const btnSchedule = document.getElementById('actionSetWorkingDays');
    if (btnSchedule) {
        btnSchedule.addEventListener('click', () => {
            openSetWorkingDaysModal();
        });
    }
}

function openMarkAttendanceModal() {
    const modal = document.getElementById('mgrMarkAttendanceModal');
    if (!modal) return;
    
    // Set default date
    const dateInput = document.getElementById('markAttDate');
    if (dateInput) {
        dateInput.value = mgrAttState.selectedDate || '2025-08-26';
    }

    calculateWorkHours();
    modal.classList.add('active');
}

function openSetWorkingDaysModal() {
    const modal = document.getElementById('mgrSetWorkingDaysModal');
    if (!modal) return;
    modal.classList.add('active');
}

function handleStatusChange(status) {
    const timeRow = document.getElementById('markAttTimeRow');
    const workHours = document.getElementById('markAttWorkHours');
    if (!timeRow || !workHours) return;

    if (status === 'Absent' || status === 'On Leave') {
        timeRow.style.opacity = '0.4';
        timeRow.style.pointerEvents = 'none';
        workHours.value = '-';
    } else {
        timeRow.style.opacity = '1';
        timeRow.style.pointerEvents = 'auto';
        calculateWorkHours();
    }
}

function calculateWorkHours() {
    const inVal = document.getElementById('markAttCheckIn')?.value;
    const outVal = document.getElementById('markAttCheckOut')?.value;
    const workHoursEl = document.getElementById('markAttWorkHours');
    if (!workHoursEl) return;

    const status = document.getElementById('markAttStatus')?.value;
    if (status === 'Absent' || status === 'On Leave') {
        workHoursEl.value = '-';
        return;
    }

    if (!inVal || !outVal) {
        workHoursEl.value = '9h 0m';
        return;
    }

    const [inH, inM] = inVal.split(':').map(Number);
    const [outH, outM] = outVal.split(':').map(Number);

    let startMins = inH * 60 + inM;
    let endMins = outH * 60 + outM;

    if (endMins < startMins) endMins += 24 * 60; // Next day wrap

    const diff = endMins - startMins;
    const diffH = Math.floor(diff / 60);
    const diffM = diff % 60;
    workHoursEl.value = `${diffH}h ${diffM}m`;
}

function formatTime12(time24) {
    if (!time24 || time24 === '-') return '-';
    const parts = time24.split(':');
    let h = parseInt(parts[0], 10);
    const m = parts[1] || '00';
    const ampm = h >= 12 ? 'PM' : 'AM';
    h = h % 12 || 12;
    const hFormatted = h < 10 ? '0' + h : String(h);
    return `${hFormatted}:${m} ${ampm}`;
}

function handleSaveMarkAttendance(e) {
    e.preventDefault();

    const empSelect = document.getElementById('markAttEmployee');
    if (!empSelect || !empSelect.value) {
        showToast('Please select a team member', 'error');
        return;
    }

    const [empId, empName, dept, initials] = empSelect.value.split('|');
    const date = document.getElementById('markAttDate')?.value || '2025-08-26';
    const status = document.getElementById('markAttStatus')?.value || 'Present';
    const checkInRaw = document.getElementById('markAttCheckIn')?.value;
    const checkOutRaw = document.getElementById('markAttCheckOut')?.value;
    const workHours = document.getElementById('markAttWorkHours')?.value || '-';

    let checkIn = '-';
    let checkOut = '-';
    if (status !== 'Absent' && status !== 'On Leave') {
        checkIn = formatTime12(checkInRaw);
        checkOut = formatTime12(checkOutRaw);
    }

    // Find if record exists in mgrAttState.records
    const existingIdx = mgrAttState.records.findIndex(r => r.employee_name === empName);
    if (existingIdx !== -1) {
        mgrAttState.records[existingIdx].status = status;
        mgrAttState.records[existingIdx].status_class = status === 'Present' ? 'present' : (status === 'On Leave' ? 'leave' : 'absent');
        mgrAttState.records[existingIdx].check_in = checkIn;
        mgrAttState.records[existingIdx].check_out = checkOut;
        mgrAttState.records[existingIdx].work_hours = workHours;
    } else {
        const newRecord = {
            row_num: mgrAttState.records.length + 1,
            id: parseInt(empId, 10),
            employee_name: empName,
            avatar_initials: initials || empName.substring(0, 2).toUpperCase(),
            department: dept,
            check_in: checkIn,
            check_out: checkOut,
            work_hours: workHours,
            status: status,
            status_class: status === 'Present' ? 'present' : (status === 'On Leave' ? 'leave' : 'absent')
        };
        mgrAttState.records.push(newRecord);
    }

    // Update KPI metrics dynamically
    const total = mgrAttState.records.length;
    const presCount = mgrAttState.records.filter(r => r.status === 'Present' || r.status === 'Half Day').length;
    const absCount = mgrAttState.records.filter(r => r.status === 'Absent').length;
    const leaveCount = mgrAttState.records.filter(r => r.status === 'On Leave').length;
    const rate = total > 0 ? Math.round((presCount / total) * 100) : 92;

    const presVal = document.getElementById('kpiPresentVal');
    if (presVal) presVal.textContent = presCount;

    const absVal = document.getElementById('kpiAbsentVal');
    if (absVal) absVal.textContent = absCount;

    const leaveVal = document.getElementById('kpiOnLeaveVal');
    if (leaveVal) leaveVal.textContent = leaveCount;

    const rateVal = document.getElementById('kpiRateVal');
    if (rateVal) rateVal.textContent = `${rate}%`;

    // Re-render table
    renderAttendanceTable(mgrAttState.records);

    showToast(`Attendance marked successfully for ${empName} (${status})!`, 'success');
    closeModals();
}

function toggleDayPill(checkbox) {
    const label = checkbox.closest('.mgr-day-pill');
    if (label) {
        if (checkbox.checked) {
            label.classList.add('active');
        } else {
            label.classList.remove('active');
        }
    }

    const checkedPills = Array.from(document.querySelectorAll('input[name="workday"]:checked')).map(cb => cb.value);
    const labelEl = document.getElementById('selectedDaysLabel');
    if (labelEl) {
        if (checkedPills.length === 0) {
            labelEl.innerHTML = `Currently selected: <span style="color:#ef4444;font-weight:700;">No working days selected</span>`;
        } else if (checkedPills.length === 5 && !checkedPills.includes('Sat') && !checkedPills.includes('Sun')) {
            labelEl.innerHTML = `Currently selected: <strong>5 Days / Week (Mon – Fri)</strong>`;
        } else if (checkedPills.length === 6 && !checkedPills.includes('Sun')) {
            labelEl.innerHTML = `Currently selected: <strong>6 Days / Week (Mon – Sat)</strong>`;
        } else {
            labelEl.innerHTML = `Currently selected: <strong>${checkedPills.length} Days / Week (${checkedPills.join(', ')})</strong>`;
        }
    }
}

function handleSaveWorkingDays(e) {
    e.preventDefault();

    const checkedDays = Array.from(document.querySelectorAll('input[name="workday"]:checked')).map(cb => cb.value);
    if (checkedDays.length === 0) {
        showToast('Please select at least one working day', 'error');
        return;
    }

    const startTime = document.getElementById('timerShiftStart')?.value || '09:00';
    const endTime = document.getElementById('timerShiftEnd')?.value || '18:00';
    const graceMins = document.getElementById('timerGracePeriod')?.value || '15';
    const halfDayHours = document.getElementById('timerHalfDay')?.value || '4.5';

    const start12 = formatTime12(startTime).replace(':00', '');
    const end12 = formatTime12(endTime).replace(':00', '');

    let daySummary = checkedDays.join(', ');
    if (checkedDays.length === 5 && !checkedDays.includes('Sat') && !checkedDays.includes('Sun')) {
        daySummary = 'Mon - Fri';
    } else if (checkedDays.length === 6 && !checkedDays.includes('Sun')) {
        daySummary = 'Mon - Sat';
    }

    // Update Quick Action Subtitle
    const sub = document.getElementById('workingDaysSubtitle');
    if (sub) {
        sub.textContent = `${daySummary} • ${start12}-${end12}`;
    }

    showToast(`Schedule & Timer saved: ${daySummary} (${start12} - ${end12}, ${graceMins}m grace)`, 'success');
    closeModals();
}

// Expose handlers globally
window.openMarkAttendanceModal = openMarkAttendanceModal;
window.openSetWorkingDaysModal = openSetWorkingDaysModal;
window.handleStatusChange = handleStatusChange;
window.calculateWorkHours = calculateWorkHours;
window.handleSaveMarkAttendance = handleSaveMarkAttendance;
window.toggleDayPill = toggleDayPill;
window.handleSaveWorkingDays = handleSaveWorkingDays;

function downloadAttendanceCsv() {
    const records = mgrAttState.records || [];
    let csvContent = "data:text/csv;charset=utf-8,#,Employee Name,Department,Check In,Check Out,Work Hours,Status\n";

    records.forEach(r => {
        csvContent += `"${r.row_num}","${r.employee_name}","${r.department}","${r.check_in}","${r.check_out}","${r.work_hours}","${r.status}"\n`;
    });

    const encodedUri = encodeURI(csvContent);
    const link = document.createElement("a");
    link.setAttribute("href", encodedUri);
    link.setAttribute("download", `team_attendance_${mgrAttState.selectedDate}.csv`);
    document.body.appendChild(link);
    link.click();
    link.remove();
}

function escapeHtml(str) {
    if (!str) return '';
    return String(str)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;');
}

/* ----------------------------------------------------------------
   DOM Content Loaded
---------------------------------------------------------------- */
document.addEventListener('DOMContentLoaded', () => {
    loadAttendanceData();
    setupFilters();
    setupModals();
    setupQuickActions();
});

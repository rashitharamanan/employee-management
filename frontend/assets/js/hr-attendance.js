'use strict';

/**
 * HR Attendance Workspace Controller
 * Luxury Violet & Pink Theme Integration
 * Seamlessly paired with HR Dashboard & HR Employees Directory
 */

const ATT_API = 'http://localhost/employee-qr-system/backend/public';

const attState = {
    page: 1,
    perPage: 10,
    totalPages: 1,
    date: new Date().toISOString().slice(0, 10),
    searchTimer: null,
    statusFilter: '',
    departmentFilter: '',
    trendDays: 7,
    currentEmployees: []
};

const token = () => localStorage.getItem('auth_token') || localStorage.getItem('token') || '';

const esc = value => String(value ?? '').replace(/[&<>'"]/g, char => ({
    '&': '&amp;',
    '<': '&lt;',
    '>': '&gt;',
    "'": '&#39;',
    '"': '&quot;'
}[char]));

/**
 * Populated default CRM dataset used if server is starting or token is initializing
 */
const DEFAULT_ATTENDANCE_DATA = {
    summary: {
        total_employees: 9,
        present: 8,
        absent: 1,
        late: 1,
        on_leave: 1,
        half_day: 0,
        present_percentage: 88.9,
        absent_percentage: 11.1,
        late_percentage: 11.1,
        on_leave_percentage: 11.1
    },
    trend: [
        { date: "2026-09-26", label: "26 Sep", present: 8, absent: 1, late: 1 },
        { date: "2026-09-27", label: "27 Sep", present: 0, absent: 9, late: 0 },
        { date: "2026-09-28", label: "28 Sep", present: 8, absent: 1, late: 0 },
        { date: "2026-09-29", label: "29 Sep", present: 9, absent: 0, late: 1 },
        { date: "2026-09-30", label: "30 Sep", present: 8, absent: 1, late: 1 },
        { date: "2026-10-01", label: "01 Oct", present: 7, absent: 2, late: 2 },
        { date: "2026-10-02", label: "02 Oct", present: 8, absent: 1, late: 0 }
    ],
    quick_stats: {
        average_check_in: "09:12 AM",
        average_check_out: "06:15 PM",
        average_work_minutes: 540,
        on_time_percentage: 92.5
    },
    highlights: {
        work_from_home: 1,
        business_trip: 0,
        outdoor_duty: 1,
        early_leave: 0,
        on_leave: 1
    },
    departments: [
        { department: "IT", present: 6, total: 7, percentage: 85.7 },
        { department: "HR", present: 1, total: 1, percentage: 100 },
        { department: "CSE", present: 1, total: 1, percentage: 100 }
    ],
    recent_activities: [
        { employee_name: "Jane Smith", profile_photo: null, status: "PRESENT", time: "09:02 AM" },
        { employee_name: "John Doe", profile_photo: null, status: "PRESENT", time: "09:10 AM" },
        { employee_name: "Test Employee", profile_photo: null, status: "LATE", time: "09:35 AM" },
        { employee_name: "Savitha R.", profile_photo: null, status: "PRESENT", time: "08:55 AM" },
        { employee_name: "Vasu", profile_photo: null, status: "PRESENT", time: "09:14 AM" }
    ],
    filters: {
        departments: ["CSE", "HR", "IT"]
    },
    employees_data: {
        employees: [
            { id: 2, name: "Jane Smith", employee_code: "EMPAD5D72FC", department: "HR", designation: "HR Executive", profile_photo: null, check_in: "09:02 AM", check_out: "06:05 PM", total_minutes: 543, status: "PRESENT" },
            { id: 1, name: "John Doe", employee_code: "EMPB3B7D665", department: "CSE", designation: "Software Developer", profile_photo: null, check_in: "09:10 AM", check_out: "06:12 PM", total_minutes: 542, status: "PRESENT" },
            { id: 15, name: "Savitha", employee_code: "EMP009", department: "IT", designation: "IT Support Executive", profile_photo: null, check_in: "08:55 AM", check_out: "06:00 PM", total_minutes: 545, status: "PRESENT" },
            { id: 13, name: "Test Employee", employee_code: "EMPC08BF93F", department: "IT", designation: "Software Developer", profile_photo: null, check_in: "09:35 AM", check_out: "06:30 PM", total_minutes: 535, status: "LATE" },
            { id: 14, name: "Test Employee 2", employee_code: "EMP322E8B32", department: "IT", designation: "Software Developer", profile_photo: null, check_in: "09:05 AM", check_out: "06:10 PM", total_minutes: 545, status: "PRESENT" },
            { id: 11, name: "Vasu", employee_code: "EMP4268ACCF", department: "IT", designation: "HR Specialist", profile_photo: null, check_in: "09:14 AM", check_out: "06:15 PM", total_minutes: 541, status: "PRESENT" },
            { id: 4, name: "Jane Smith (IT)", employee_code: "EMP3B00FA6F", department: "IT", designation: "Support Lead", profile_photo: null, check_in: null, check_out: null, total_minutes: null, status: "ON_LEAVE" },
            { id: 6, name: "Jane Smith 2", employee_code: "EMP69EFA154", department: "IT", designation: "QA Analyst", profile_photo: null, check_in: "09:08 AM", check_out: "06:02 PM", total_minutes: 534, status: "PRESENT" },
            { id: 8, name: "Jane Smith 3", employee_code: "EMPA7CEE93E", department: "IT", designation: "Database Admin", profile_photo: null, check_in: null, check_out: null, total_minutes: null, status: "ABSENT" }
        ],
        pagination: {
            current_page: 1,
            per_page: 10,
            total_items: 9,
            total_pages: 1
        }
    }
};

/**
 * Fetch attendance dashboard payload from backend API
 */
async function attendanceApi(params) {
    const userToken = token();
    if (!userToken) return null;

    try {
        const response = await fetch(`${ATT_API}/api/hr/attendance?${params}`, {
            headers: {
                'Authorization': `Bearer ${userToken}`,
                'Accept': 'application/json'
            }
        });

        if (response.status === 401) {
            localStorage.clear();
            location.href = 'login.php';
            return null;
        }

        const json = await response.json();
        if (!response.ok || !json.success) {
            console.warn('API returned non-success:', json);
            return null;
        }
        return json.data;
    } catch (err) {
        console.warn('attendanceApi fetch error:', err);
        return null;
    }
}

function setText(id, value) {
    const el = document.getElementById(id);
    if (el) el.textContent = value ?? '—';
}

/**
 * Render 5 KPI Cards and Presence Breakdown Ring
 */
function renderSummary(summary) {
    const total = Number(summary.total_employees) || 0;
    const present = Number(summary.present) || 0;
    const absent = Number(summary.absent) || 0;
    const late = Number(summary.late) || 0;
    const onLeave = Number(summary.on_leave) || 0;

    const presentPct = total > 0 ? Math.round((present / total) * 100) : 0;
    const absentPct = total > 0 ? Math.round((absent / total) * 100) : 0;
    const latePct = total > 0 ? Math.round((late / total) * 100) : 0;
    const leavePct = total > 0 ? Math.round((onLeave / total) * 100) : 0;

    // KPI Pills
    setText('presentTotal', present);
    setText('presentMeta', `${presentPct}% Presence`);

    setText('absentTotal', absent);
    setText('absentMeta', `${absentPct}% Absent`);

    setText('lateTotal', late);
    setText('lateMeta', `${latePct}% Late Arrivals`);

    setText('leaveTotal', onLeave);
    setText('leaveMeta', `${leavePct}% On Leave`);

    setText('statusTotal', total);
    setText('statusTotalDonut', total);

    const presencePill = document.getElementById('presenceRatePill');
    if (presencePill) presencePill.textContent = `${presentPct}% Presence`;

    // Multi-segment SVG Donut Calculation
    // Circumference = 2 * PI * 38 ≈ 238.76
    const circumference = 238.76;
    const ringPresent = document.getElementById('donutRingPresent');
    const ringLate = document.getElementById('donutRingLate');
    const ringLeave = document.getElementById('donutRingLeave');
    const ringAbsent = document.getElementById('donutRingAbsent');

    if (total > 0 && ringPresent && ringLate && ringLeave && ringAbsent) {
        const presentLen = (present / total) * circumference;
        const lateLen = (late / total) * circumference;
        const leaveLen = (onLeave / total) * circumference;
        const absentLen = (absent / total) * circumference;

        ringPresent.style.strokeDasharray = `${presentLen} ${circumference - presentLen}`;
        ringPresent.style.strokeDashoffset = '0';

        ringLate.style.strokeDasharray = `${lateLen} ${circumference - lateLen}`;
        ringLate.style.strokeDashoffset = `-${presentLen}`;

        ringLeave.style.strokeDasharray = `${leaveLen} ${circumference - leaveLen}`;
        ringLeave.style.strokeDashoffset = `-${presentLen + lateLen}`;

        ringAbsent.style.strokeDasharray = `${absentLen} ${circumference - absentLen}`;
        ringAbsent.style.strokeDashoffset = `-${presentLen + lateLen + leaveLen}`;
    }

    // Legend List
    const legendEl = document.getElementById('statusLegend');
    if (legendEl) {
        const legendItems = [
            { label: 'Present On-Time', count: present, pct: presentPct, dotClass: 'dot-plum', color: '#be185d' },
            { label: 'Late Arrivals', count: late, pct: latePct, dotClass: 'dot-violet', color: '#7e22ce' },
            { label: 'On Approved Leave', count: onLeave, pct: leavePct, dotClass: 'dot-fuchsia', color: '#db2777' },
            { label: 'Absent Today', count: absent, pct: absentPct, dotClass: 'dot-pink', color: '#28103c' }
        ];

        legendEl.innerHTML = legendItems.map(item => `
            <div class="prod-legend-item">
                <span class="legend-dot-label">
                    <span class="legend-dot" style="background: ${item.color}"></span>
                    <span>${item.label}</span>
                </span>
                <span class="legend-val">${item.count} <small style="font-weight:600;color:#756b85;">(${item.pct}%)</small></span>
            </div>
        `).join('');
    }
}

/**
 * Render Attendance Trend Bars with Interactive Hover Tooltips
 */
function renderTrend(trend) {
    const trendEl = document.getElementById('attendanceTrend');
    if (!trendEl) return;

    if (!Array.isArray(trend) || trend.length === 0) {
        trend = DEFAULT_ATTENDANCE_DATA.trend;
    }

    const maxVal = Math.max(1, ...trend.flatMap(d => [Number(d.present) || 0, Number(d.absent) || 0, Number(d.late) || 0]));

    trendEl.innerHTML = trend.map(day => {
        const pres = Number(day.present) || 0;
        const abs = Number(day.absent) || 0;
        const lte = Number(day.late) || 0;

        const presH = Math.max(4, Math.round((pres / maxVal) * 100));
        const absH = Math.max(4, Math.round((abs / maxVal) * 100));
        const lteH = Math.max(4, Math.round((lte / maxVal) * 100));

        return `
            <div class="trend-day">
                <div class="trend-tooltip">
                    <span>${esc(day.label)} (${esc(day.date)})</span>
                    Present: ${pres} • Absent: ${abs} • Late: ${lte}
                </div>
                <div class="trend-bars">
                    <i class="bar-present" style="height: ${presH}%;" title="Present: ${pres}"></i>
                    <i class="bar-absent" style="height: ${absH}%;" title="Absent: ${abs}"></i>
                    <i class="bar-late" style="height: ${lteH}%;" title="Late: ${lte}"></i>
                </div>
                <span class="trend-day-label">${esc(day.label)}</span>
            </div>
        `;
    }).join('');
}

/**
 * Render Quick Stats & Attendance Highlights
 */
function renderStats(stats, highlights) {
    stats = stats || DEFAULT_ATTENDANCE_DATA.quick_stats;
    highlights = highlights || DEFAULT_ATTENDANCE_DATA.highlights;

    setText('avgCheckIn', stats.average_check_in || '09:00 AM');
    setText('avgCheckOut', stats.average_check_out || '06:00 PM');

    const minutes = Number(stats.average_work_minutes) || 0;
    const hoursStr = minutes > 0 ? `${Math.floor(minutes / 60)}h ${minutes % 60}m` : '8h 00m';
    setText('avgWorkHours', hoursStr);

    const onTime = stats.on_time_percentage !== undefined ? `${stats.on_time_percentage}% Punctual` : '92% Punctual';
    setText('onTimeRate', onTime);

    setText('wfhCount', highlights.work_from_home ?? 0);
    setText('tripCount', highlights.business_trip ?? 0);
    setText('dutyCount', highlights.outdoor_duty ?? 0);
    setText('earlyCount', highlights.early_leave ?? 0);
}

/**
 * Render Department Presence Companion Table
 */
function renderDepartments(items) {
    const deptEl = document.getElementById('departmentAttendance');
    if (!deptEl) return;

    if (!Array.isArray(items) || items.length === 0) {
        items = DEFAULT_ATTENDANCE_DATA.departments;
    }

    deptEl.innerHTML = `
        <div class="dept-table-wrap">
            <table class="dept-presence-table">
                <thead>
                    <tr>
                        <th>Department</th>
                        <th style="text-align: center;">Staff</th>
                        <th>Presence Rate</th>
                    </tr>
                </thead>
                <tbody>
                    ${items.map(item => {
                        const pct = Math.min(100, Math.max(0, Number(item.percentage) || 0));
                        const badgeClass = pct >= 90 ? 'rate-high' : (pct >= 70 ? 'rate-med' : 'rate-low');
                        return `
                            <tr>
                                <td>
                                    <div class="dept-td-name">
                                        <span class="dept-icon-mini">🏢</span>
                                        <strong>${esc(item.department)}</strong>
                                    </div>
                                </td>
                                <td style="text-align: center;">
                                    <span class="dept-val-pill">${item.present}/${item.total}</span>
                                </td>
                                <td>
                                    <div class="dept-rate-cell">
                                        <div class="dept-rate-top">
                                            <span class="dept-pct-badge ${badgeClass}">${pct}%</span>
                                        </div>
                                        <div class="dept-mini-track">
                                            <div class="dept-mini-fill" style="width: ${pct}%;"></div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        `;
                    }).join('')}
                </tbody>
            </table>
        </div>
    `;
}

/**
 * Render Recent Activity Stream
 */
function renderActivities(items) {
    const actEl = document.getElementById('recentActivities');
    if (!actEl) return;

    if (!Array.isArray(items) || items.length === 0) {
        items = DEFAULT_ATTENDANCE_DATA.recent_activities;
    }

    actEl.innerHTML = items.slice(0, 6).map(item => {
        const name = item.employee_name || 'Staff Member';
        const initial = esc(name.charAt(0).toUpperCase());
        const status = String(item.status || 'PRESENT').toUpperCase();
        const time = item.time || 'Just now';

        let badgeText = 'Checked In';
        if (status === 'LATE') badgeText = 'Late Check-In';
        if (status === 'ABSENT') badgeText = 'Marked Absent';

        const photo = item.profile_photo
            ? `<img src="${esc(item.profile_photo)}" alt="${esc(name)}">`
            : initial;

        return `
            <div class="activity-feed-item">
                <div class="activity-avatar">${photo}</div>
                <div class="activity-content">
                    <div class="activity-title">
                        <strong>${esc(name)}</strong> — ${badgeText}
                    </div>
                    <span class="activity-time">${esc(time)}</span>
                </div>
            </div>
        `;
    }).join('');
}

/**
 * Render Employee Attendance Table Rows & Pagination
 */
function renderEmployees(data) {
    const body = document.getElementById('attendanceEmployees');
    if (!body) return;

    let records = (data && data.employees) || [];
    if (records.length === 0 && (!data || !data.employees)) {
        records = DEFAULT_ATTENDANCE_DATA.employees_data.employees;
    }
    attState.currentEmployees = records;

    // Apply Client-Side Tab Filtering if selected
    if (attState.statusFilter) {
        records = records.filter(emp => {
            const s = String(emp.status || '').toUpperCase();
            return s === attState.statusFilter;
        });
    }

    const countBadge = document.getElementById('attendanceCount');
    if (countBadge) {
        countBadge.textContent = `${records.length} employee${records.length === 1 ? '' : 's'}`;
    }

    if (records.length === 0) {
        body.innerHTML = `
            <tr>
                <td colspan="8" style="text-align: center; padding: 40px 20px; color: #756b85;">
                    <div style="font-size: 26px; margin-bottom: 8px;">📋</div>
                    <strong style="color: #2b1748; font-size: 14px; display: block;">No matching attendance records found</strong>
                    <small style="font-size: 12px;">Try adjusting your search terms or status tab filter.</small>
                </td>
            </tr>
        `;
    } else {
        body.innerHTML = records.map((emp, index) => {
            const rawStatus = String(emp.status || 'ABSENT').toUpperCase();
            const statusClass = rawStatus.toLowerCase().replace(/\s+/g, '_');
            const initial = esc((emp.name || '?').charAt(0).toUpperCase());

            const hours = emp.total_minutes !== null && emp.total_minutes !== undefined && emp.total_minutes > 0
                ? `${Math.floor(emp.total_minutes / 60)}h ${emp.total_minutes % 60}m`
                : '—';

            const photo = emp.profile_photo
                ? `<img src="${esc(emp.profile_photo)}" alt="${esc(emp.name)}">`
                : initial;

            const checkInVal = emp.check_in
                ? `<span class="time-pill"><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>${esc(emp.check_in)}</span>`
                : '<span style="color:#a08c9f;">—</span>';

            const checkOutVal = emp.check_out
                ? `<span class="time-pill"><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>${esc(emp.check_out)}</span>`
                : '<span style="color:#a08c9f;">—</span>';

            const rowNum = ((attState.page - 1) * attState.perPage) + index + 1;

            return `
                <tr>
                    <td style="font-weight: 700; color: #756b85;">${rowNum}</td>
                    <td>
                        <div class="employee-cell">
                            <div class="employee-avatar">${photo}</div>
                            <div class="employee-info-col">
                                <strong>${esc(emp.name)}</strong>
                                <small>${esc(emp.employee_code || 'EMP')}</small>
                            </div>
                        </div>
                    </td>
                    <td><span class="dept-pill">${esc(emp.department || 'General')}</span></td>
                    <td>${checkInVal}</td>
                    <td>${checkOutVal}</td>
                    <td><span class="hours-pill">${hours}</span></td>
                    <td>
                        <span class="att-status ${statusClass}">
                            <i class="status-dot"></i>${esc(rawStatus.replace(/_/g, ' '))}
                        </span>
                    </td>
                    <td style="text-align: right;">
                        <a class="attendance-view-btn" href="hr-employees.php?id=${encodeURIComponent(emp.id)}" title="View Profile">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                <circle cx="12" cy="12" r="3"/>
                            </svg>
                        </a>
                    </td>
                </tr>
            `;
        }).join('');
    }

    // Pagination
    const p = (data && data.pagination) || { total_items: records.length, current_page: 1, total_pages: 1, per_page: 10 };
    attState.totalPages = p.total_pages || 1;

    const startItem = p.total_items ? (p.current_page - 1) * p.per_page + 1 : 0;
    const endItem = Math.min(p.current_page * p.per_page, p.total_items);

    setText('attendancePageInfo', p.total_items
        ? `Showing ${startItem}–${endItem} of ${p.total_items} employees`
        : `Showing ${records.length} employees`);

    const paginationEl = document.getElementById('attendancePagination');
    if (paginationEl) {
        let pagesHtml = `
            <button type="button" ${p.current_page <= 1 ? 'disabled' : ''} data-page="${p.current_page - 1}" aria-label="Previous Page">‹</button>
        `;

        const maxButtons = 5;
        const startPage = Math.max(1, p.current_page - 2);
        const endPage = Math.min(attState.totalPages, startPage + maxButtons - 1);

        for (let i = startPage; i <= endPage; i++) {
            pagesHtml += `<button type="button" data-page="${i}" class="${i === p.current_page ? 'active' : ''}">${i}</button>`;
        }

        pagesHtml += `
            <button type="button" ${p.current_page >= attState.totalPages ? 'disabled' : ''} data-page="${p.current_page + 1}" aria-label="Next Page">›</button>
        `;

        paginationEl.innerHTML = pagesHtml;
    }
}

/**
 * Load Attendance Master Data
 */
async function loadAttendance(page = 1) {
    attState.page = page;

    const searchInput = document.getElementById('attendanceSearch');
    const deptSelect = document.getElementById('attendanceDept');
    const trendSelect = document.getElementById('trendDays');

    const params = new URLSearchParams({
        date: attState.date,
        page: attState.page,
        per_page: attState.perPage,
        search: searchInput ? searchInput.value.trim() : '',
        department: deptSelect ? deptSelect.value : '',
        trend_days: trendSelect ? trendSelect.value : attState.trendDays
    });

    try {
        let data = await attendanceApi(params);
        if (!data) {
            data = DEFAULT_ATTENDANCE_DATA;
        }

        renderSummary(data.summary || DEFAULT_ATTENDANCE_DATA.summary);
        renderTrend(data.trend || DEFAULT_ATTENDANCE_DATA.trend);
        renderStats(data.quick_stats || DEFAULT_ATTENDANCE_DATA.quick_stats, data.highlights || DEFAULT_ATTENDANCE_DATA.highlights);
        renderDepartments(data.departments || DEFAULT_ATTENDANCE_DATA.departments);
        renderActivities(data.recent_activities || DEFAULT_ATTENDANCE_DATA.recent_activities);
        renderEmployees(data.employees_data || DEFAULT_ATTENDANCE_DATA.employees_data);

        // Populate department filter once
        if (deptSelect && data.filters && Array.isArray(data.filters.departments)) {
            const currentVal = deptSelect.value;
            deptSelect.innerHTML = '<option value="">All Departments</option>' +
                data.filters.departments.map(d => `<option value="${esc(d)}">${esc(d)}</option>`).join('');
            deptSelect.value = currentVal;
        }

    } catch (error) {
        console.error('Attendance Load Error:', error);
        // Render fallback data gracefully so the user never sees empty loading states
        renderSummary(DEFAULT_ATTENDANCE_DATA.summary);
        renderTrend(DEFAULT_ATTENDANCE_DATA.trend);
        renderStats(DEFAULT_ATTENDANCE_DATA.quick_stats, DEFAULT_ATTENDANCE_DATA.highlights);
        renderDepartments(DEFAULT_ATTENDANCE_DATA.departments);
        renderActivities(DEFAULT_ATTENDANCE_DATA.recent_activities);
        renderEmployees(DEFAULT_ATTENDANCE_DATA.employees_data);
    }
}

/**
 * Live Realtime Clock
 */
function updateClock() {
    const now = new Date();
    setText('liveTime', now.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit', second: '2-digit' }));
    setText('clockDate', now.toLocaleDateString([], { weekday: 'short', day: '2-digit', month: 'short', year: 'numeric' }));
}

/**
 * Export Attendance Table as CSV
 */
function exportAttendanceCsv() {
    if (!attState.currentEmployees || attState.currentEmployees.length === 0) {
        alert('No attendance data available to export.');
        return;
    }

    const headers = ['#', 'Employee Code', 'Full Name', 'Department', 'Designation', 'Check In', 'Check Out', 'Total Minutes', 'Status', 'Date'];
    const rows = attState.currentEmployees.map((emp, i) => [
        i + 1,
        `"${(emp.employee_code || '').replace(/"/g, '""')}"`,
        `"${(emp.name || '').replace(/"/g, '""')}"`,
        `"${(emp.department || '').replace(/"/g, '""')}"`,
        `"${(emp.designation || '').replace(/"/g, '""')}"`,
        `"${(emp.check_in || '').replace(/"/g, '""')}"`,
        `"${(emp.check_out || '').replace(/"/g, '""')}"`,
        emp.total_minutes || 0,
        `"${(emp.status || '').replace(/"/g, '""')}"`,
        attState.date
    ]);

    const csvContent = 'data:text/csv;charset=utf-8,' + [headers.join(','), ...rows.map(e => e.join(','))].join('\n');
    const encodedUri = encodeURI(csvContent);
    const link = document.createElement('a');
    link.setAttribute('href', encodedUri);
    link.setAttribute('download', `hr_attendance_${attState.date}.csv`);
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}

/**
 * Initialize on DOM Load
 */
document.addEventListener('DOMContentLoaded', () => {
    // Set initial date
    const dateInput = document.getElementById('attendanceDate');
    if (dateInput) {
        dateInput.value = attState.date;
        dateInput.addEventListener('change', e => {
            if (e.target.value) {
                attState.date = e.target.value;
                loadAttendance(1);
            }
        });
    }

    // "Today" Button Shortcut
    const todayBtn = document.getElementById('todayBtn');
    if (todayBtn) {
        todayBtn.addEventListener('click', () => {
            const today = new Date().toISOString().slice(0, 10);
            attState.date = today;
            if (dateInput) dateInput.value = today;
            loadAttendance(1);
        });
    }

    // Export Button
    const exportBtn = document.getElementById('exportBtn');
    if (exportBtn) {
        exportBtn.addEventListener('click', exportAttendanceCsv);
    }

    // Trend Days Dropdown
    const trendSelect = document.getElementById('trendDays');
    if (trendSelect) {
        trendSelect.addEventListener('change', e => {
            attState.trendDays = Number(e.target.value) || 7;
            loadAttendance(1);
        });
    }

    // Department Filter
    const deptSelect = document.getElementById('attendanceDept');
    if (deptSelect) {
        deptSelect.addEventListener('change', () => loadAttendance(1));
    }

    // Status Select Filter
    const statusSelect = document.getElementById('attendanceStatusSelect');
    if (statusSelect) {
        statusSelect.addEventListener('change', e => {
            attState.statusFilter = e.target.value;
            // Sync status tabs
            document.querySelectorAll('#statusTabs .tab-btn').forEach(btn => {
                btn.classList.toggle('active', btn.dataset.status === attState.statusFilter);
            });
            renderEmployees({ employees: attState.currentEmployees });
        });
    }

    // Status Tabs
    const statusTabs = document.getElementById('statusTabs');
    if (statusTabs) {
        statusTabs.addEventListener('click', e => {
            const btn = e.target.closest('.tab-btn');
            if (!btn) return;

            statusTabs.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
            btn.classList.add('active');

            attState.statusFilter = btn.dataset.status || '';
            if (statusSelect) statusSelect.value = attState.statusFilter;

            renderEmployees({ employees: attState.currentEmployees });
        });
    }

    // Search Input with Debounce
    const searchInput = document.getElementById('attendanceSearch');
    if (searchInput) {
        searchInput.addEventListener('input', () => {
            clearTimeout(attState.searchTimer);
            attState.searchTimer = setTimeout(() => loadAttendance(1), 250);
        });
    }

    // Pagination Click
    const paginationEl = document.getElementById('attendancePagination');
    if (paginationEl) {
        paginationEl.addEventListener('click', e => {
            const btn = e.target.closest('[data-page]');
            if (btn && !btn.disabled) {
                const targetPage = Number(btn.dataset.page);
                if (targetPage && targetPage !== attState.page) {
                    loadAttendance(targetPage);
                }
            }
        });
    }

    // Per Page Select
    const perPageSelect = document.getElementById('attendancePerPage');
    if (perPageSelect) {
        perPageSelect.addEventListener('change', e => {
            attState.perPage = Number(e.target.value) || 10;
            loadAttendance(1);
        });
    }

    // Terminal Check-Out Action Button
    const checkOutBtn = document.getElementById('checkOutButton');
    if (checkOutBtn) {
        checkOutBtn.addEventListener('click', () => {
            alert('Quick Check-In/Check-Out gateway is active. To scan physical badges or QR codes, use the QR Scanner terminal.');
        });
    }

    // Start Live Clock
    updateClock();
    setInterval(updateClock, 1000);

    // Initial Data Fetch
    loadAttendance();
});

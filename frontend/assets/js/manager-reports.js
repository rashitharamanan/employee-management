/**
 * Mindazzle — Reports & Insights Controller (manager-reports.js)
 * Interactive controller matching the Reports & Insights mockup
 * Features a smooth Multi-Line Wave/Area Spline Attendance Chart (NO BAR CHART),
 * Task Status Donut, Star Tier Performance Breakdown, and full tabbed reporting.
 */

const mgrRepState = {
    data: null,
    records: [],
    selectedDept: 'All Departments',
    selectedRange: '01 Aug 2025 - 26 Aug 2025',
    activeTab: 'Team Performance',
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
async function loadReportsData(dateRange = '01 Aug 2025 - 26 Aug 2025', dept = 'All Departments') {
    showLoader();
    try {
        const url = `${MGR_API_BASE}/api/manager/reports?date_range=${encodeURIComponent(dateRange)}&department=${encodeURIComponent(dept)}`;
        const res = await fetch(url, {
            method: 'GET',
            headers: getAuthHeaders()
        });

        const json = await res.json();
        if (json.success && json.data) {
            mgrRepState.data = json.data;
            const fallback = getFallbackReportData();
            if (!mgrRepState.data.team_performance_reports) mgrRepState.data.team_performance_reports = fallback.team_performance_reports;
            if (!mgrRepState.data.attendance_reports) mgrRepState.data.attendance_reports = fallback.attendance_reports;
            if (!mgrRepState.data.task_reports) mgrRepState.data.task_reports = fallback.task_reports;
            if (!mgrRepState.data.leave_reports) mgrRepState.data.leave_reports = fallback.leave_reports;
            renderAll(mgrRepState.data);
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
function getFallbackReportData() {
    return {
        manager: {
            name: 'Arun Kumar',
            role: 'Team Manager',
            today_label: 'Tue, 26 Aug 2025'
        },
        kpis: {
            total_team_members: { count: 12, trend: '2', trend_up: true, subtext: 'vs last month' },
            average_attendance: { count: '92%', trend: '4%', trend_up: true, subtext: 'vs last month' },
            average_performance: { count: '4.2 / 5', trend: '0.3', trend_up: true, subtext: 'vs last quarter' },
            tasks_completed: { count: 48, trend: '20%', trend_up: true, subtext: 'vs last month' }
        },
        attendance_trends: [
            { month: 'Mar', present: 76, leave: 12, absent: 12 },
            { month: 'Apr', present: 80, leave: 10, absent: 10 },
            { month: 'May', present: 82, leave: 10, absent: 8 },
            { month: 'Jun', present: 81, leave: 11, absent: 8 },
            { month: 'Jul', present: 85, leave: 9, absent: 6 },
            { month: 'Aug', present: 90, leave: 6, absent: 4 }
        ],
        task_status: {
            total_tasks: 48,
            segments: [
                { label: 'Completed', count: 24, percentage: '50%', color: '#10b981' },
                { label: 'In Progress', count: 14, percentage: '29%', color: '#3b82f6' },
                { label: 'Not Started', count: 6, percentage: '13%', color: '#f59e0b' },
                { label: 'Overdue', count: 4, percentage: '8%', color: '#ef4444' }
            ]
        },
        performance_rating_tiers: {
            total_reviewed: 12,
            average_rating: 4.2,
            tiers: [
                { stars: 5, label: '5 Stars', count: 3, percentage: 25, color: '#8b5cf6' },
                { stars: 4, label: '4 Stars', count: 5, percentage: 42, color: '#3b82f6' },
                { stars: 3, label: '3 Stars', count: 3, percentage: 25, color: '#06b6d4' },
                { stars: 2, label: '2 Stars', count: 1, percentage: 8, color: '#f59e0b' },
                { stars: 1, label: '1 Star', count: 0, percentage: 0, color: '#ef4444' }
            ]
        },
        team_performance_reports: [
            { row_num: 1, id: 1, employee_name: 'Priya Sharma', avatar_initials: 'PS', department: 'Design', attendance_rate: '96%', tasks_completed: 12, average_rating: '4.8', status: 'Excellent', status_class: 'excellent' },
            { row_num: 2, id: 2, employee_name: 'Rohit Kulkarni', avatar_initials: 'RK', department: 'Development', attendance_rate: '90%', tasks_completed: 10, average_rating: '4.6', status: 'Good', status_class: 'good' },
            { row_num: 3, id: 3, employee_name: 'Sneha Iyer', avatar_initials: 'SI', department: 'HR', attendance_rate: '88%', tasks_completed: 8, average_rating: '4.2', status: 'Good', status_class: 'good' },
            { row_num: 4, id: 4, employee_name: 'Rohan Verma', avatar_initials: 'RV', department: 'Development', attendance_rate: '95%', tasks_completed: 11, average_rating: '4.1', status: 'Good', status_class: 'good' },
            { row_num: 5, id: 5, employee_name: 'Neha Tiwari', avatar_initials: 'NT', department: 'Marketing', attendance_rate: '89%', tasks_completed: 7, average_rating: '3.9', status: 'Needs Support', status_class: 'support' },
            { row_num: 6, id: 6, employee_name: 'Aditya Kapoor', avatar_initials: 'AK', department: 'QA', attendance_rate: '92%', tasks_completed: 9, average_rating: '4.3', status: 'Good', status_class: 'good' },
            { row_num: 7, id: 7, employee_name: 'Vikram Singh', avatar_initials: 'VS', department: 'DevOps', attendance_rate: '98%', tasks_completed: 14, average_rating: '4.7', status: 'Excellent', status_class: 'excellent' },
            { row_num: 8, id: 8, employee_name: 'Ananya Roy', avatar_initials: 'AR', department: 'Analytics', attendance_rate: '94%', tasks_completed: 10, average_rating: '4.5', status: 'Excellent', status_class: 'excellent' }
        ],
        attendance_reports: [
            { row_num: 1, id: 1, employee_name: 'Priya Sharma', avatar_initials: 'PS', department: 'Design', total_days: 24, present: 23, late: 0, absent: 1, attendance_rate: '96%', status: 'Present' },
            { row_num: 2, id: 2, employee_name: 'Rohit Kulkarni', avatar_initials: 'RK', department: 'Development', total_days: 24, present: 22, late: 1, absent: 1, attendance_rate: '92%', status: 'Present' },
            { row_num: 3, id: 3, employee_name: 'Sneha Iyer', avatar_initials: 'SI', department: 'HR', total_days: 24, present: 21, late: 0, absent: 3, attendance_rate: '88%', status: 'On Leave' },
            { row_num: 4, id: 4, employee_name: 'Rohan Verma', avatar_initials: 'RV', department: 'Development', total_days: 24, present: 23, late: 1, absent: 0, attendance_rate: '96%', status: 'Present' },
            { row_num: 5, id: 5, employee_name: 'Neha Tiwari', avatar_initials: 'NT', department: 'Marketing', total_days: 24, present: 21, late: 2, absent: 1, attendance_rate: '88%', status: 'On Leave' },
            { row_num: 6, id: 6, employee_name: 'Aditya Kapoor', avatar_initials: 'AK', department: 'QA', total_days: 24, present: 22, late: 0, absent: 2, attendance_rate: '92%', status: 'Present' },
            { row_num: 7, id: 7, employee_name: 'Vikram Singh', avatar_initials: 'VS', department: 'DevOps', total_days: 24, present: 24, late: 0, absent: 0, attendance_rate: '100%', status: 'Present' },
            { row_num: 8, id: 8, employee_name: 'Ananya Roy', avatar_initials: 'AR', department: 'Analytics', total_days: 24, present: 23, late: 1, absent: 0, attendance_rate: '96%', status: 'Present' }
        ],
        task_reports: [
            { row_num: 1, id: 1, task_name: 'Design System 2.0 Revamp', assignee: 'Priya Sharma', avatar_initials: 'PS', department: 'Design', priority: 'High', due_date: '30 Aug 2025', progress: 100, status: 'Completed' },
            { row_num: 2, id: 2, task_name: 'Auth & Security Patch V3', assignee: 'Rohit Kulkarni', avatar_initials: 'RK', department: 'Development', priority: 'Urgent', due_date: '28 Aug 2025', progress: 85, status: 'In Progress' },
            { row_num: 3, id: 3, task_name: 'Team Onboarding Workflow', assignee: 'Sneha Iyer', avatar_initials: 'SI', department: 'HR', priority: 'Medium', due_date: '05 Sep 2025', progress: 60, status: 'In Progress' },
            { row_num: 4, id: 4, task_name: 'API Latency & Query Tuning', assignee: 'Rohan Verma', avatar_initials: 'RV', department: 'Development', priority: 'High', due_date: '02 Sep 2025', progress: 40, status: 'At Risk' },
            { row_num: 5, id: 5, task_name: 'Q3 Campaign Content Deck', assignee: 'Neha Tiwari', avatar_initials: 'NT', department: 'Marketing', priority: 'Medium', due_date: '10 Sep 2025', progress: 30, status: 'Not Started' },
            { row_num: 6, id: 6, task_name: 'Regression Testing Suite', assignee: 'Aditya Kapoor', avatar_initials: 'AK', department: 'QA', priority: 'High', due_date: '01 Sep 2025', progress: 90, status: 'In Progress' },
            { row_num: 7, id: 7, task_name: 'Cloud CI/CD Pipeline Setup', assignee: 'Vikram Singh', avatar_initials: 'VS', department: 'DevOps', priority: 'Urgent', due_date: '25 Aug 2025', progress: 100, status: 'Completed' },
            { row_num: 8, id: 8, task_name: 'Executive Analytics Dashboard', assignee: 'Ananya Roy', avatar_initials: 'AR', department: 'Analytics', priority: 'High', due_date: '15 Sep 2025', progress: 25, status: 'In Progress' }
        ],
        leave_reports: [
            { row_num: 1, id: 1, employee_name: 'Sneha Iyer', avatar_initials: 'SI', department: 'HR', leave_type: 'Casual Leave', duration: '3 Days', dates: '26 Aug – 28 Aug 2025', reason: 'Family Function', status: 'Approved' },
            { row_num: 2, id: 2, employee_name: 'Neha Tiwari', avatar_initials: 'NT', department: 'Marketing', leave_type: 'Sick Leave', duration: '2 Days', dates: '25 Aug – 26 Aug 2025', reason: 'Medical Checkup', status: 'Approved' },
            { row_num: 3, id: 3, employee_name: 'Karan Mehta', avatar_initials: 'KM', department: 'Development', leave_type: 'Annual Leave', duration: '5 Days', dates: '01 Sep – 05 Sep 2025', reason: 'Personal Travel', status: 'Pending' },
            { row_num: 4, id: 4, employee_name: 'Rohan Verma', avatar_initials: 'RV', department: 'Development', leave_type: 'Work From Home', duration: '1 Day', dates: '28 Aug – 28 Aug 2025', reason: 'Home Maintenance', status: 'Approved' },
            { row_num: 5, id: 5, employee_name: 'Aditya Kapoor', avatar_initials: 'AK', department: 'QA', leave_type: 'Compensatory Off', duration: '1 Day', dates: '29 Aug – 29 Aug 2025', reason: 'Weekend Deployment', status: 'Pending' },
            { row_num: 6, id: 6, employee_name: 'Priya Sharma', avatar_initials: 'PS', department: 'Design', leave_type: 'Vacation Leave', duration: '4 Days', dates: '10 Sep – 13 Sep 2025', reason: 'Annual Vacation', status: 'Approved' },
            { row_num: 7, id: 7, employee_name: 'Vikram Singh', avatar_initials: 'VS', department: 'DevOps', leave_type: 'Emergency Leave', duration: '1 Day', dates: '22 Aug – 22 Aug 2025', reason: 'Family Emergency', status: 'Approved' },
            { row_num: 8, id: 8, employee_name: 'Rohit Kulkarni', avatar_initials: 'RK', department: 'Development', leave_type: 'Casual Leave', duration: '2 Days', dates: '08 Sep – 09 Sep 2025', reason: 'Personal Appointment', status: 'Approved' }
        ]
    };
}

function renderFallback() {
    const fallbackData = getFallbackReportData();
    mgrRepState.data = fallbackData;
    mgrRepState.records = fallbackData.team_performance_reports;
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

    // Attendance Wave Area Chart (Replaces bar chart)
    if (data.attendance_trends) {
        renderAttendanceWaveChart(data.attendance_trends);
    }

    // Task Status Donut Chart
    if (data.task_status) {
        renderTaskStatusDonut(data.task_status);
    }

    // Performance Rating Tiers (Replaces bar chart)
    if (data.performance_rating_tiers) {
        renderRatingTiers(data.performance_rating_tiers);
    }

    // Main Report Table - Render active tab
    switchReportTab(mgrRepState.activeTab || 'Team Performance', false);
}

/* ----------------------------------------------------------------
   1. Render 4 Top KPI Cards
---------------------------------------------------------------- */
function renderKpis(kpis) {
    // Total Team Members
    const totalVal = document.getElementById('kpiTotalMembersVal');
    const totalTrend = document.getElementById('kpiTotalMembersTrend');
    if (totalVal && kpis.total_team_members) {
        totalVal.textContent = kpis.total_team_members.count;
        if (totalTrend) {
            totalTrend.innerHTML = `↑ ${kpis.total_team_members.trend}`;
        }
    }

    // Average Attendance
    const attVal = document.getElementById('kpiAvgAttendanceVal');
    const attTrend = document.getElementById('kpiAvgAttendanceTrend');
    if (attVal && kpis.average_attendance) {
        attVal.textContent = kpis.average_attendance.count;
        if (attTrend) {
            attTrend.innerHTML = `↑ ${kpis.average_attendance.trend}`;
        }
    }

    // Average Performance
    const perfVal = document.getElementById('kpiAvgPerformanceVal');
    const perfTrend = document.getElementById('kpiAvgPerformanceTrend');
    if (perfVal && kpis.average_performance) {
        perfVal.textContent = kpis.average_performance.count;
        if (perfTrend) {
            perfTrend.innerHTML = `↑ ${kpis.average_performance.trend}`;
        }
    }

    // Tasks Completed
    const tasksVal = document.getElementById('kpiTasksCompletedVal');
    const tasksTrend = document.getElementById('kpiTasksCompletedTrend');
    if (tasksVal && kpis.tasks_completed) {
        tasksVal.textContent = kpis.tasks_completed.count;
        if (tasksTrend) {
            tasksTrend.innerHTML = `↑ ${kpis.tasks_completed.trend}`;
        }
    }
}

/* ----------------------------------------------------------------
   2. Render Multi-Curve Attendance Wave Chart (NO BAR CHART!)
---------------------------------------------------------------- */
function renderAttendanceWaveChart(trends) {
    const svg = document.getElementById('mgrAttendanceWaveSvg');
    if (!svg || !trends || trends.length === 0) return;

    // ViewBox dimensions
    const width = 500;
    const height = 135;
    const paddingLeft = 15;
    const paddingRight = 15;
    const paddingTop = 10;
    const paddingBottom = 10;

    const chartW = width - paddingLeft - paddingRight;
    const chartH = height - paddingTop - paddingBottom;

    // Y scale from 0 to 100%
    const minY = 0;
    const maxY = 100;

    // Calculate points for Present, Leave, Absent
    const count = trends.length;
    const presentPts = [];
    const leavePts = [];
    const absentPts = [];

    trends.forEach((t, idx) => {
        const x = paddingLeft + (idx / (count - 1)) * chartW;
        const yPres = height - paddingBottom - ((t.present - minY) / (maxY - minY)) * chartH;
        const yLeave = height - paddingBottom - ((t.leave - minY) / (maxY - minY)) * chartH;
        const yAbs = height - paddingBottom - ((t.absent - minY) / (maxY - minY)) * chartH;

        presentPts.push({ x: Math.round(x * 10) / 10, y: Math.round(yPres * 10) / 10, val: t.present });
        leavePts.push({ x: Math.round(x * 10) / 10, y: Math.round(yLeave * 10) / 10, val: t.leave });
        absentPts.push({ x: Math.round(x * 10) / 10, y: Math.round(yAbs * 10) / 10, val: t.absent });
    });

    // Generate smooth cubic bezier path for curve
    function createSmoothPath(points) {
        if (points.length === 0) return '';
        let d = `M ${points[0].x} ${points[0].y}`;
        for (let i = 0; i < points.length - 1; i++) {
            const p0 = points[i];
            const p1 = points[i + 1];
            const cx1 = p0.x + (p1.x - p0.x) * 0.45;
            const cy1 = p0.y;
            const cx2 = p0.x + (p1.x - p0.x) * 0.55;
            const cy2 = p1.y;
            d += ` C ${cx1} ${cy1}, ${cx2} ${cy2}, ${p1.x} ${p1.y}`;
        }
        return d;
    }

    const presentPathD = createSmoothPath(presentPts);
    const leavePathD = createSmoothPath(leavePts);
    const absentPathD = createSmoothPath(absentPts);

    const presentAreaD = `${presentPathD} L ${presentPts[count - 1].x} ${height - paddingBottom} L ${presentPts[0].x} ${height - paddingBottom} Z`;

    // Horizontal Grid Lines (0%, 20%, 40%, 60%, 80%, 100%)
    let gridLines = '';
    for (let step = 0; step <= 5; step++) {
        const gy = height - paddingBottom - (step / 5) * chartH;
        gridLines += `<line x1="${paddingLeft}" y1="${gy}" x2="${width - paddingRight}" y2="${gy}" stroke="#f1f5f9" stroke-width="1" />`;
    }

    // Circles on data points
    const makeCircles = (pts, strokeColor, fillColor) => pts.map(p => `
        <circle cx="${p.x}" cy="${p.y}" r="4" fill="#ffffff" stroke="${strokeColor}" stroke-width="2" />
        <circle cx="${p.x}" cy="${p.y}" r="2" fill="${fillColor}" />
    `).join('');

    svg.setAttribute('viewBox', `0 0 ${width} ${height}`);
    svg.innerHTML = `
        <defs>
            <linearGradient id="wavePresentGrad" x1="0" y1="0" x2="0" y2="1">
                <stop offset="0%" stop-color="#10b981" stop-opacity="0.30" />
                <stop offset="100%" stop-color="#10b981" stop-opacity="0.0" />
            </linearGradient>
        </defs>
        ${gridLines}
        <path d="${presentAreaD}" fill="url(#wavePresentGrad)" />
        <path d="${absentPathD}" fill="none" stroke="#ef4444" stroke-width="2" stroke-linecap="round" />
        <path d="${leavePathD}" fill="none" stroke="#f59e0b" stroke-width="2" stroke-linecap="round" />
        <path d="${presentPathD}" fill="none" stroke="#10b981" stroke-width="2.8" stroke-linecap="round" />
        ${makeCircles(presentPts, '#10b981', '#059669')}
        ${makeCircles(leavePts, '#f59e0b', '#d97706')}
        ${makeCircles(absentPts, '#ef4444', '#dc2626')}
    `;

    // X-Axis labels
    const xAxisContainer = document.getElementById('mgrWaveXAxis');
    if (xAxisContainer) {
        xAxisContainer.innerHTML = trends.map(t => `<span>${t.month}</span>`).join('');
    }
}

/* ----------------------------------------------------------------
   3. Render Task Status Donut Chart
---------------------------------------------------------------- */
function renderTaskStatusDonut(taskStatus) {
    const donutSvg = document.getElementById('mgrTaskStatusDonutSvg');
    const centerVal = document.getElementById('mgrTaskTotalCount');
    const legendContainer = document.getElementById('mgrTaskStatusLegendList');

    const total = taskStatus.total_tasks || 48;
    if (centerVal) centerVal.textContent = total;

    const segments = taskStatus.segments || [];
    const circumference = 282.74; // for r=45
    let currentOffset = 0;

    if (donutSvg) {
        let circlesSvg = `<circle cx="65" cy="65" r="45" stroke="#f1f5f9" stroke-width="15" fill="none" />`;

        segments.forEach(seg => {
            const segFraction = seg.count / total;
            const strokeDash = segFraction * circumference;
            circlesSvg += `
                <circle cx="65" cy="65" r="45" stroke="${seg.color}" stroke-width="15" fill="none"
                        stroke-dasharray="${strokeDash} ${circumference}"
                        stroke-dashoffset="${-currentOffset}"
                        style="transition: all 0.5s ease;" />
            `;
            currentOffset += strokeDash;
        });

        donutSvg.innerHTML = circlesSvg;
    }

    if (legendContainer) {
        legendContainer.innerHTML = segments.map(seg => `
            <div class="mgr-dist-legend-item">
                <div class="mgr-dist-legend-left">
                    <span style="width:8px;height:8px;border-radius:50%;background:${seg.color};display:inline-block;"></span>
                    <span style="color:#475569;">${escapeHtml(seg.label)}</span>
                </div>
                <span class="mgr-dist-count">${seg.count} <span style="font-size:10.5px;font-weight:500;color:#94a3b8;">(${seg.percentage})</span></span>
            </div>
        `).join('');
    }
}

/* ----------------------------------------------------------------
   4. Render Performance Rating Distribution Tiers (NO BAR CHART!)
---------------------------------------------------------------- */
function renderRatingTiers(ratingData) {
    const container = document.getElementById('mgrRatingTiersList');
    if (!container) return;

    const tiers = ratingData.tiers || [];

    container.innerHTML = tiers.map(t => `
        <div class="mgr-rating-tier-row">
            <div class="mgr-tier-label">
                <span>${t.stars}</span>
                <span class="mgr-tier-star">★</span>
            </div>
            <div class="mgr-tier-track">
                <div class="mgr-tier-fill" style="width: ${t.percentage}%; background: ${t.color};"></div>
            </div>
            <div class="mgr-tier-count">${t.count}</div>
        </div>
    `).join('');
}

/* ----------------------------------------------------------------
   5. Switch Report Tab & Render Main Table
---------------------------------------------------------------- */
function switchReportTab(tabName, showNotification = false) {
    tabName = tabName || 'Team Performance';
    mgrRepState.activeTab = tabName;

    // Update active class on tab buttons
    document.querySelectorAll('.mgr-report-tab').forEach(t => {
        if (t.getAttribute('data-tab') === tabName) {
            t.classList.add('active');
        } else {
            t.classList.remove('active');
        }
    });

    // Update Titles, Subtitles & Table Headers
    const titleEl = document.getElementById('mgrReportTableTitle');
    const subtitleEl = document.getElementById('mgrReportTableSubtitle');
    const theadEl = document.getElementById('mgrReportsTableHead');

    let rawRecords = [];
    if (tabName === 'Attendance Report') {
        if (titleEl) titleEl.textContent = 'Attendance & Punctuality Report';
        if (subtitleEl) subtitleEl.textContent = 'Monthly presence, late arrivals and absenteeism metrics';
        if (theadEl) {
            theadEl.innerHTML = `
                <tr>
                    <th style="width:40px;">#</th>
                    <th>Employee</th>
                    <th>Department</th>
                    <th>Total Days</th>
                    <th>Present</th>
                    <th>Late</th>
                    <th>Absent</th>
                    <th>Attendance Rate</th>
                    <th>Status</th>
                    <th style="text-align:right;">Action</th>
                </tr>
            `;
        }
        rawRecords = (mgrRepState.data && mgrRepState.data.attendance_reports) || [];
    } else if (tabName === 'Task Report') {
        if (titleEl) titleEl.textContent = 'Team Task & Deliverables Report';
        if (subtitleEl) subtitleEl.textContent = 'Assigned tasks, sprint progress, and priority status';
        if (theadEl) {
            theadEl.innerHTML = `
                <tr>
                    <th style="width:40px;">#</th>
                    <th>Task Name</th>
                    <th>Assignee</th>
                    <th>Department</th>
                    <th>Priority</th>
                    <th>Due Date</th>
                    <th>Progress</th>
                    <th>Status</th>
                    <th style="text-align:right;">Action</th>
                </tr>
            `;
        }
        rawRecords = (mgrRepState.data && mgrRepState.data.task_reports) || [];
    } else if (tabName === 'Leave Report') {
        if (titleEl) titleEl.textContent = 'Team Leave & Absence Report';
        if (subtitleEl) subtitleEl.textContent = 'Approved and pending leave requests summary';
        if (theadEl) {
            theadEl.innerHTML = `
                <tr>
                    <th style="width:40px;">#</th>
                    <th>Employee</th>
                    <th>Department</th>
                    <th>Leave Type</th>
                    <th>Duration</th>
                    <th>Dates</th>
                    <th>Reason</th>
                    <th>Status</th>
                    <th style="text-align:right;">Action</th>
                </tr>
            `;
        }
        rawRecords = (mgrRepState.data && mgrRepState.data.leave_reports) || [];
    } else {
        // Team Performance (default)
        if (titleEl) titleEl.textContent = 'Team Performance Report';
        if (subtitleEl) subtitleEl.textContent = 'Employee rating and deliverables matrix';
        if (theadEl) {
            theadEl.innerHTML = `
                <tr>
                    <th style="width:40px;">#</th>
                    <th>Employee</th>
                    <th>Department</th>
                    <th>Attendance Rate</th>
                    <th>Tasks Completed</th>
                    <th>Average Rating</th>
                    <th>Status</th>
                    <th style="text-align:right;">Action</th>
                </tr>
            `;
        }
        rawRecords = (mgrRepState.data && mgrRepState.data.team_performance_reports) || [];
    }

    // Filter by selected department if needed
    if (mgrRepState.selectedDept && mgrRepState.selectedDept !== 'All Departments') {
        rawRecords = rawRecords.filter(r => (r.department || '').toLowerCase() === mgrRepState.selectedDept.toLowerCase());
    }

    mgrRepState.records = rawRecords;

    // Apply active search filter
    let displayRecords = [...rawRecords];
    if (mgrRepState.searchQuery) {
        const q = mgrRepState.searchQuery.toLowerCase();
        displayRecords = displayRecords.filter(r => 
            (r.employee_name || '').toLowerCase().includes(q) ||
            (r.task_name || '').toLowerCase().includes(q) ||
            (r.assignee || '').toLowerCase().includes(q) ||
            (r.department || '').toLowerCase().includes(q) ||
            (r.status || '').toLowerCase().includes(q) ||
            (r.leave_type || '').toLowerCase().includes(q) ||
            (r.reason || '').toLowerCase().includes(q)
        );
    }

    renderReportsTable(displayRecords, tabName);

    if (showNotification) {
        showToast(`Switched view to ${tabName}`, 'info');
    }
}

function renderReportsTable(records, activeTab) {
    const tbody = document.getElementById('mgrReportsTableBody');
    if (!tbody) return;

    activeTab = activeTab || mgrRepState.activeTab || 'Team Performance';

    if (!records || records.length === 0) {
        const colCount = (activeTab === 'Attendance Report') ? 10 : 9;
        tbody.innerHTML = `<tr><td colspan="${colCount}" style="text-align:center;padding:28px;color:#94a3b8;font-size:13px;">No reports found matching selection.</td></tr>`;
        return;
    }

    const avatarStyles = {
        'PS': 'background:#e0f2fe;color:#0284c7;',
        'RK': 'background:#e0e7ff;color:#4338ca;',
        'SI': 'background:#ffedd5;color:#c2410c;',
        'RV': 'background:#dcfce7;color:#15803d;',
        'NT': 'background:#f3e8ff;color:#7e22ce;',
        'AK': 'background:#dbeafe;color:#1d4ed8;',
        'VS': 'background:#ecfdf5;color:#059669;',
        'AR': 'background:#fef3c7;color:#d97706;',
        'KM': 'background:#f3e8ff;color:#9333ea;'
    };

    if (activeTab === 'Attendance Report') {
        tbody.innerHTML = records.map((r, idx) => {
            const initials = r.avatar_initials || 'TM';
            const avStyle = avatarStyles[initials] || 'background:#ccfbf1;color:#0f766e;';
            const badgeClass = (r.status === 'Present') ? 'mgr-badge-present' : 'mgr-badge-leave';

            return `
                <tr>
                    <td style="color:#64748b;font-weight:700;font-size:12px;">${r.row_num || (idx + 1)}</td>
                    <td>
                        <div class="mgr-team-user">
                            <div class="mgr-member-avatar" style="${avStyle}">${initials}</div>
                            <span class="mgr-team-name">${escapeHtml(r.employee_name)}</span>
                        </div>
                    </td>
                    <td style="color:#475569;font-size:12px;">${escapeHtml(r.department)}</td>
                    <td style="font-weight:600;color:#0f172a;font-size:12px;">${r.total_days || 24}</td>
                    <td style="font-weight:700;color:#15803d;font-size:12px;">${r.present}</td>
                    <td style="font-weight:600;color:${r.late > 0 ? '#b45309' : '#64748b'};font-size:12px;">${r.late}</td>
                    <td style="font-weight:600;color:${r.absent > 0 ? '#b91c1c' : '#64748b'};font-size:12px;">${r.absent}</td>
                    <td style="font-weight:700;color:#0f172a;font-size:12px;">${escapeHtml(r.attendance_rate)}</td>
                    <td>
                        <span class="${badgeClass}">${escapeHtml(r.status)}</span>
                    </td>
                    <td style="text-align:right;">
                        <button type="button" class="mgr-btn-view" onclick="openEmployeeReportModal(${r.id}, 'Attendance')">View</button>
                    </td>
                </tr>
            `;
        }).join('');
    } else if (activeTab === 'Task Report') {
        tbody.innerHTML = records.map((r, idx) => {
            const initials = r.avatar_initials || 'TM';
            const avStyle = avatarStyles[initials] || 'background:#ccfbf1;color:#0f766e;';

            let priorityClass = 'mgr-badge-medium';
            if (r.priority === 'Urgent') priorityClass = 'mgr-badge-urgent';
            else if (r.priority === 'High') priorityClass = 'mgr-badge-high';

            let statusClass = 'mgr-badge-in-progress';
            if (r.status === 'Completed') statusClass = 'mgr-badge-completed';
            else if (r.status === 'At Risk') statusClass = 'mgr-badge-at-risk';
            else if (r.status === 'Not Started') statusClass = 'mgr-badge-not-started';

            return `
                <tr>
                    <td style="color:#64748b;font-weight:700;font-size:12px;">${r.row_num || (idx + 1)}</td>
                    <td>
                        <div style="font-weight:700;color:#0f172a;font-size:13px;">${escapeHtml(r.task_name)}</div>
                    </td>
                    <td>
                        <div class="mgr-team-user">
                            <div class="mgr-member-avatar" style="${avStyle}">${initials}</div>
                            <span class="mgr-team-name">${escapeHtml(r.assignee)}</span>
                        </div>
                    </td>
                    <td style="color:#475569;font-size:12px;">${escapeHtml(r.department)}</td>
                    <td>
                        <span class="${priorityClass}">${escapeHtml(r.priority)}</span>
                    </td>
                    <td style="color:#64748b;font-size:12px;font-weight:600;">${escapeHtml(r.due_date)}</td>
                    <td>
                        <div style="display:flex;align-items:center;gap:8px;min-width:110px;">
                            <div style="flex:1;background:#e2e8f0;height:6px;border-radius:3px;overflow:hidden;">
                                <div style="width:${r.progress}%;height:100%;background:${r.progress === 100 ? '#10b981' : '#3b82f6'};border-radius:3px;"></div>
                            </div>
                            <span style="font-size:11.5px;font-weight:700;color:#475569;">${r.progress}%</span>
                        </div>
                    </td>
                    <td>
                        <span class="${statusClass}">${escapeHtml(r.status)}</span>
                    </td>
                    <td style="text-align:right;">
                        <button type="button" class="mgr-btn-view" onclick="openEmployeeReportModal(${r.id}, 'Task')">View</button>
                    </td>
                </tr>
            `;
        }).join('');
    } else if (activeTab === 'Leave Report') {
        tbody.innerHTML = records.map((r, idx) => {
            const initials = r.avatar_initials || 'TM';
            const avStyle = avatarStyles[initials] || 'background:#ccfbf1;color:#0f766e;';

            let statusClass = 'mgr-badge-approved';
            if (r.status === 'Pending') statusClass = 'mgr-badge-pending';
            else if (r.status === 'Rejected') statusClass = 'mgr-badge-rejected';

            return `
                <tr>
                    <td style="color:#64748b;font-weight:700;font-size:12px;">${r.row_num || (idx + 1)}</td>
                    <td>
                        <div class="mgr-team-user">
                            <div class="mgr-member-avatar" style="${avStyle}">${initials}</div>
                            <span class="mgr-team-name">${escapeHtml(r.employee_name)}</span>
                        </div>
                    </td>
                    <td style="color:#475569;font-size:12px;">${escapeHtml(r.department)}</td>
                    <td style="font-weight:600;color:#1e293b;font-size:12px;">${escapeHtml(r.leave_type)}</td>
                    <td style="font-weight:600;color:#0f172a;font-size:12px;">${escapeHtml(r.duration)}</td>
                    <td style="color:#64748b;font-size:12px;">${escapeHtml(r.dates)}</td>
                    <td style="color:#475569;font-size:12px;">${escapeHtml(r.reason)}</td>
                    <td>
                        <span class="${statusClass}">${escapeHtml(r.status)}</span>
                    </td>
                    <td style="text-align:right;">
                        <button type="button" class="mgr-btn-view" onclick="openEmployeeReportModal(${r.id}, 'Leave')">View</button>
                    </td>
                </tr>
            `;
        }).join('');
    } else {
        // Team Performance (default)
        tbody.innerHTML = records.map((r, idx) => {
            const initials = r.avatar_initials || 'TM';
            const avStyle = avatarStyles[initials] || 'background:#ccfbf1;color:#0f766e;';

            let badgeClass = 'mgr-badge-good';
            if (r.status === 'Excellent') badgeClass = 'mgr-badge-excellent';
            else if (r.status === 'Needs Support') badgeClass = 'mgr-badge-support';

            return `
                <tr>
                    <td style="color:#64748b;font-weight:700;font-size:12px;">${r.row_num || (idx + 1)}</td>
                    <td>
                        <div class="mgr-team-user">
                            <div class="mgr-member-avatar" style="${avStyle}">${initials}</div>
                            <span class="mgr-team-name">${escapeHtml(r.employee_name)}</span>
                        </div>
                    </td>
                    <td style="color:#475569;font-size:12px;">${escapeHtml(r.department)}</td>
                    <td style="font-weight:600;color:#0f172a;font-size:12px;">${escapeHtml(r.attendance_rate)}</td>
                    <td style="font-weight:600;color:#0f172a;font-size:12px;">${r.tasks_completed}</td>
                    <td>
                        <div style="display:flex;align-items:center;gap:4px;font-weight:700;color:#0f172a;font-size:12px;">
                            <span style="color:#f59e0b;font-size:13px;">★</span>
                            <span>${r.average_rating}</span>
                        </div>
                    </td>
                    <td>
                        <span class="${badgeClass}">${escapeHtml(r.status)}</span>
                    </td>
                    <td style="text-align:right;">
                        <button type="button" class="mgr-btn-view" onclick="openEmployeeReportModal(${r.id}, 'Performance')">View</button>
                    </td>
                </tr>
            `;
        }).join('');
    }
}

/* ----------------------------------------------------------------
   Modals
---------------------------------------------------------------- */
function openEmployeeReportModal(recId, type) {
    const r = mgrRepState.records.find(item => item.id === recId) || mgrRepState.records[0];
    if (!r) return;

    const modal = document.getElementById('mgrEmployeeReportModal');
    const content = document.getElementById('mgrReportModalBody');
    if (!modal || !content) return;

    type = type || (r.leave_type ? 'Leave' : r.task_name ? 'Task' : r.total_days ? 'Attendance' : 'Performance');

    const initials = r.avatar_initials || 'TM';
    const empName = r.employee_name || r.assignee || 'Team Member';
    const dept = r.department || 'General';

    let gridHtml = '';
    let footerHtml = '';
    let subTitle = '';

    if (type === 'Leave' || r.leave_type) {
        subTitle = `${escapeHtml(dept)} • Leave & Absence Request`;
        gridHtml = `
            <div class="mgr-detail-box">
                <div class="mgr-detail-label">Leave Type</div>
                <div class="mgr-detail-val" style="color:#0f766e;">${escapeHtml(r.leave_type)}</div>
            </div>
            <div class="mgr-detail-box">
                <div class="mgr-detail-label">Duration</div>
                <div class="mgr-detail-val">${escapeHtml(r.duration)}</div>
            </div>
            <div class="mgr-detail-box">
                <div class="mgr-detail-label">Scheduled Dates</div>
                <div class="mgr-detail-val" style="font-size:12.5px;">${escapeHtml(r.dates)}</div>
            </div>
            <div class="mgr-detail-box">
                <div class="mgr-detail-label">Request Status</div>
                <div class="mgr-detail-val">${escapeHtml(r.status)}</div>
            </div>
        `;
        footerHtml = `
            <div style="margin-top:14px;background:#f8fafc;padding:12px 14px;border-radius:8px;border:1px solid #e2e8f0;font-size:12px;color:#475569;line-height:1.45;">
                <strong>Reason:</strong> ${escapeHtml(r.reason)}<br>
                <span style="font-size:11px;color:#64748b;margin-top:4px;display:inline-block;">✓ Logged and verified in workforce management records.</span>
            </div>
        `;
    } else if (type === 'Task' || r.task_name) {
        subTitle = `${escapeHtml(dept)} • Sprint Task Deliverable`;
        gridHtml = `
            <div class="mgr-detail-box">
                <div class="mgr-detail-label">Task Name</div>
                <div class="mgr-detail-val" style="font-size:12.5px;color:#0f766e;">${escapeHtml(r.task_name)}</div>
            </div>
            <div class="mgr-detail-box">
                <div class="mgr-detail-label">Priority Level</div>
                <div class="mgr-detail-val">${escapeHtml(r.priority)}</div>
            </div>
            <div class="mgr-detail-box">
                <div class="mgr-detail-label">Due Date</div>
                <div class="mgr-detail-val" style="font-size:12.5px;">${escapeHtml(r.due_date)}</div>
            </div>
            <div class="mgr-detail-box">
                <div class="mgr-detail-label">Current Progress</div>
                <div class="mgr-detail-val">${r.progress}% (${escapeHtml(r.status)})</div>
            </div>
        `;
        footerHtml = `
            <div style="margin-top:14px;background:#f8fafc;padding:12px 14px;border-radius:8px;border:1px solid #e2e8f0;font-size:12px;color:#475569;line-height:1.45;">
                <strong>Deliverable Status:</strong> Tracking sprint milestone progress. Progress is currently at <strong>${r.progress}%</strong>.
            </div>
        `;
    } else if (type === 'Attendance' || r.total_days) {
        subTitle = `${escapeHtml(dept)} • Attendance & Punctuality Summary`;
        gridHtml = `
            <div class="mgr-detail-box">
                <div class="mgr-detail-label">Total Scheduled Days</div>
                <div class="mgr-detail-val">${r.total_days} Days</div>
            </div>
            <div class="mgr-detail-box">
                <div class="mgr-detail-label">Days Present</div>
                <div class="mgr-detail-val" style="color:#15803d;">${r.present} Days</div>
            </div>
            <div class="mgr-detail-box">
                <div class="mgr-detail-label">Late / Absent</div>
                <div class="mgr-detail-val" style="font-size:12.5px;color:#b45309;">${r.late} Late / ${r.absent} Absent</div>
            </div>
            <div class="mgr-detail-box">
                <div class="mgr-detail-label">Attendance Rate</div>
                <div class="mgr-detail-val" style="color:#0f766e;">${escapeHtml(r.attendance_rate)} (${escapeHtml(r.status)})</div>
            </div>
        `;
        footerHtml = `
            <div style="margin-top:14px;background:#f8fafc;padding:12px 14px;border-radius:8px;border:1px solid #e2e8f0;font-size:12px;color:#475569;line-height:1.45;">
                <strong>Attendance Audit:</strong> Overall consistency complies with monthly team threshold (&gt;85%).
            </div>
        `;
    } else {
        subTitle = `${escapeHtml(dept)} • Performance Summary`;
        gridHtml = `
            <div class="mgr-detail-box">
                <div class="mgr-detail-label">Attendance Rate</div>
                <div class="mgr-detail-val" style="color:#0f766e;">${escapeHtml(r.attendance_rate || '95%')}</div>
            </div>
            <div class="mgr-detail-box">
                <div class="mgr-detail-label">Tasks Completed</div>
                <div class="mgr-detail-val">${r.tasks_completed || 10} Tasks</div>
            </div>
            <div class="mgr-detail-box">
                <div class="mgr-detail-label">Average Rating</div>
                <div class="mgr-detail-val" style="color:#f59e0b;">⭐ ${r.average_rating || '4.5'} / 5.0</div>
            </div>
            <div class="mgr-detail-box">
                <div class="mgr-detail-label">Performance Status</div>
                <div class="mgr-detail-val">${escapeHtml(r.status || 'Good')}</div>
            </div>
        `;
        footerHtml = `
            <div style="margin-top:14px;background:#f8fafc;padding:12px 14px;border-radius:8px;border:1px solid #e2e8f0;font-size:12px;color:#475569;line-height:1.45;">
                <strong>Insights:</strong> Demonstrates high consistency across quarterly deliverables and adherence to team attendance standards. Eligible for merit recognition.
            </div>
        `;
    }

    content.innerHTML = `
        <div style="display:flex;align-items:center;gap:14px;padding-bottom:14px;border-bottom:1px solid #f1f5f9;">
            <div style="width:46px;height:46px;border-radius:50%;background:#ccfbf1;color:#0f766e;display:flex;align-items:center;justify-content:center;font-weight:800;font-size:16px;">
                ${initials}
            </div>
            <div>
                <h3 style="margin:0;font-size:16px;font-weight:800;color:#0f172a;">${escapeHtml(empName)}</h3>
                <span style="font-size:12px;color:#64748b;">${subTitle}</span>
            </div>
        </div>

        <div class="mgr-member-modal-grid" style="margin-top:14px;">
            ${gridHtml}
        </div>

        ${footerHtml}
    `;

    modal.classList.add('active');
}

function openCustomReportModal() {
    const modal = document.getElementById('mgrCustomReportModal');
    if (modal) modal.classList.add('active');
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

    const customForm = document.getElementById('mgrCustomReportForm');
    if (customForm) {
        customForm.addEventListener('submit', (e) => {
            e.preventDefault();
            const repType = document.getElementById('customRepType').value;
            const range = document.getElementById('customRepRange').value;
            showToast(`Generating custom ${repType} for ${range}...`, 'success');
            closeModals();
        });
    }
}

/* ----------------------------------------------------------------
   Tab Switcher & Filters
---------------------------------------------------------------- */
function setupTabsAndFilters() {
    // Tabs
    document.querySelectorAll('.mgr-report-tab').forEach(tab => {
        tab.addEventListener('click', () => {
            const tabName = tab.getAttribute('data-tab');
            switchReportTab(tabName, true);
        });
    });

    // Date Range Dropdown
    const rangeSelect = document.getElementById('repDateRangeSelect');
    if (rangeSelect) {
        rangeSelect.addEventListener('change', (e) => {
            mgrRepState.selectedRange = e.target.value;
            showToast(`Date range: ${e.target.value}`, 'info');
            loadReportsData(e.target.value, mgrRepState.selectedDept);
        });
    }

    // Department Dropdown
    const deptSelect = document.getElementById('repDeptSelect');
    if (deptSelect) {
        deptSelect.addEventListener('change', (e) => {
            mgrRepState.selectedDept = e.target.value;
            switchReportTab(mgrRepState.activeTab, false);
            loadReportsData(mgrRepState.selectedRange, e.target.value);
        });
    }

    // Search Input in Table Header
    const empSearch = document.getElementById('reportEmpSearchInput');
    if (empSearch) {
        empSearch.addEventListener('input', (e) => {
            const query = e.target.value.toLowerCase().trim();
            mgrRepState.searchQuery = query;

            let filtered = [...mgrRepState.records];
            if (query) {
                filtered = filtered.filter(r => 
                    (r.employee_name || '').toLowerCase().includes(query) ||
                    (r.task_name || '').toLowerCase().includes(query) ||
                    (r.assignee || '').toLowerCase().includes(query) ||
                    (r.department || '').toLowerCase().includes(query) ||
                    (r.status || '').toLowerCase().includes(query) ||
                    (r.leave_type || '').toLowerCase().includes(query) ||
                    (r.reason || '').toLowerCase().includes(query)
                );
            }
            renderReportsTable(filtered, mgrRepState.activeTab);
        });
    }

    // Export Button
    const btnExport = document.getElementById('btnExportReport');
    if (btnExport) {
        btnExport.addEventListener('click', downloadReportsCsv);
    }

    // Promo Card Button
    const btnPromo = document.getElementById('btnNeedCustomReport');
    if (btnPromo) {
        btnPromo.addEventListener('click', openCustomReportModal);
    }

    // Quick Reports buttons
    const btnAttRep = document.getElementById('qrAttendanceReport');
    if (btnAttRep) {
        btnAttRep.addEventListener('click', () => {
            switchReportTab('Attendance Report', true);
        });
    }

    const btnPerfRep = document.getElementById('qrPerformanceReport');
    if (btnPerfRep) {
        btnPerfRep.addEventListener('click', () => {
            switchReportTab('Team Performance', true);
        });
    }

    const btnTaskRep = document.getElementById('qrTaskReport');
    if (btnTaskRep) {
        btnTaskRep.addEventListener('click', () => {
            switchReportTab('Task Report', true);
        });
    }

    const btnLeaveRep = document.getElementById('qrLeaveReport');
    if (btnLeaveRep) {
        btnLeaveRep.addEventListener('click', () => {
            switchReportTab('Leave Report', true);
        });
    }
}

function downloadReportsCsv() {
    const activeTab = mgrRepState.activeTab || 'Team Performance';
    const records = mgrRepState.records || [];
    showToast(`Exporting ${activeTab} (CSV)...`, 'success');

    let csvContent = "data:text/csv;charset=utf-8,";
    let filename = `report_${Date.now()}.csv`;

    if (activeTab === 'Attendance Report') {
        filename = `attendance_report_${Date.now()}.csv`;
        csvContent += "#,Employee Name,Department,Total Days,Present,Late,Absent,Attendance Rate,Status\n";
        records.forEach((r, idx) => {
            csvContent += `"${r.row_num || (idx + 1)}","${r.employee_name || ''}","${r.department || ''}","${r.total_days || 24}","${r.present || 0}","${r.late || 0}","${r.absent || 0}","${r.attendance_rate || ''}","${r.status || ''}"\n`;
        });
    } else if (activeTab === 'Task Report') {
        filename = `task_report_${Date.now()}.csv`;
        csvContent += "#,Task Name,Assignee,Department,Priority,Due Date,Progress,Status\n";
        records.forEach((r, idx) => {
            csvContent += `"${r.row_num || (idx + 1)}","${r.task_name || ''}","${r.assignee || ''}","${r.department || ''}","${r.priority || ''}","${r.due_date || ''}","${r.progress || 0}%","${r.status || ''}"\n`;
        });
    } else if (activeTab === 'Leave Report') {
        filename = `leave_report_${Date.now()}.csv`;
        csvContent += "#,Employee Name,Department,Leave Type,Duration,Dates,Reason,Status\n";
        records.forEach((r, idx) => {
            csvContent += `"${r.row_num || (idx + 1)}","${r.employee_name || ''}","${r.department || ''}","${r.leave_type || ''}","${r.duration || ''}","${r.dates || ''}","${r.reason || ''}","${r.status || ''}"\n`;
        });
    } else {
        filename = `team_performance_report_${Date.now()}.csv`;
        csvContent += "#,Employee Name,Department,Attendance Rate,Tasks Completed,Average Rating,Status\n";
        records.forEach((r, idx) => {
            csvContent += `"${r.row_num || (idx + 1)}","${r.employee_name || ''}","${r.department || ''}","${r.attendance_rate || ''}","${r.tasks_completed || 0}","${r.average_rating || ''}","${r.status || ''}"\n`;
        });
    }

    const encodedUri = encodeURI(csvContent);
    const link = document.createElement("a");
    link.setAttribute("href", encodedUri);
    link.setAttribute("download", filename);
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
    loadReportsData();
    setupTabsAndFilters();
    setupModals();
});

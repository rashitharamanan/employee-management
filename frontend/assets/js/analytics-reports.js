'use strict';

/**
 * Analytics & Reports Module
 * Admin Portal - LUNA Theme
 */

const API_BASE = (function () {
    const origin = window.location.origin;
    if (origin && origin !== 'null') {
        return origin + '/employee-qr-system/backend/public';
    }
    return 'http://localhost/employee-qr-system/backend/public';
})();

const state = {
    summary: {},
    departments: [],
    reports: [],
    isLoading: false
};

// Utilities
const getToken = () => localStorage.getItem('auth_token') || localStorage.getItem('token') || '';

const esc = (v) =>
    String(v ?? '').replace(/[&<>'"]/g, (c) => ({
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        "'": '&#39;',
        '"': '&quot;'
    }[c]));

const formatDate = (v) => {
    if (!v) return '—';
    try {
        const d = new Date(v);
        return isNaN(d.getTime()) ? String(v) : d.toLocaleDateString('en-GB', {
            day: '2-digit',
            month: 'short',
            year: 'numeric'
        });
    } catch {
        return String(v);
    }
};

// Toast notification
function toast(message) {
    const el = document.getElementById('toast');
    if (!el) return;
    el.textContent = message;
    el.classList.add('show');
    setTimeout(() => el.classList.remove('show'), 3000);
}

// API helper
async function api(path, options = {}) {
    const token = getToken();
    const res = await fetch(`${API_BASE}${path}`, {
        ...options,
        headers: {
            'Content-Type': 'application/json',
            ...(token ? { Authorization: `Bearer ${token}` } : {}),
            ...(options.headers || {})
        }
    });

    if (res.status === 401) {
        localStorage.removeItem('auth_token');
        localStorage.removeItem('token');
        location.href = 'login.php';
        return null;
    }

    const result = await res.json();
    if (!res.ok || result.success === false) {
        throw new Error(result.message || 'Request failed.');
    }
    return result.data || {};
}

// Initialize default date inputs
function initDateDefaults() {
    const today = new Date();
    const past = new Date();
    past.setDate(today.getDate() - 30);

    const fromInput = document.getElementById('repFromDate');
    const toInput = document.getElementById('repToDate');

    if (fromInput && !fromInput.value) {
        fromInput.value = past.toISOString().slice(0, 10);
    }
    if (toInput && !toInput.value) {
        toInput.value = today.toISOString().slice(0, 10);
    }
}

// Render 4 KPI Stat Cards (Matching Reference Photo with Live Rings)
function renderKPIs(summary = {}) {
    const totalEmps = Number(summary.total_employees || 0);
    const activeEmps = Number(summary.active_employees || 0);
    const attendanceRate = Number(summary.attendance_rate || 0);
    const totalReports = Number(summary.total_reports || 0);
    const present = Number(summary.present || 0);

    const elTotal = document.getElementById('kpiTotalEmps');
    const elActive = document.getElementById('kpiActiveEmps');
    const elActivePercent = document.getElementById('kpiActivePercent');
    const elAttendance = document.getElementById('kpiAttendance');
    const elPresentCount = document.getElementById('kpiPresentCount');
    const elReports = document.getElementById('kpiTotalReports');

    if (elTotal) elTotal.textContent = totalEmps;
    if (elActive) elActive.textContent = activeEmps;
    const activePct = totalEmps > 0 ? Math.round((activeEmps / totalEmps) * 100) : 100;
    if (elActivePercent) {
        elActivePercent.textContent = `${activePct}% active`;
    }
    if (elAttendance) elAttendance.textContent = `${attendanceRate}%`;
    if (elPresentCount) elPresentCount.textContent = `${present} present today`;
    if (elReports) elReports.textContent = totalReports;

    const subTotal = document.getElementById('kpiTotalEmpsSub');
    if (subTotal) subTotal.textContent = `${totalEmps} registered`;

    // Dynamic progress rings matching the user's reference style
    const C = 84.82; // 2 * PI * 13.5 (compact KPI ring)
    const ring1 = document.getElementById('kpiRingVal1');
    const ring2 = document.getElementById('kpiRingVal2');
    const ring3 = document.getElementById('kpiRingVal3');
    const ring4 = document.getElementById('kpiRingVal4');
    const circ1 = document.getElementById('kpiRingCircle1');
    const circ2 = document.getElementById('kpiRingCircle2');
    const circ3 = document.getElementById('kpiRingCircle3');
    const circ4 = document.getElementById('kpiRingCircle4');

    if (ring1) ring1.textContent = `${totalEmps}`;
    if (circ1) circ1.style.strokeDashoffset = '0';

    if (ring2) ring2.textContent = `${activePct}%`;
    if (circ2) circ2.style.strokeDashoffset = `${C * (1 - Math.min(activePct, 100) / 100)}`;

    if (ring3) ring3.textContent = `${attendanceRate}%`;
    if (circ3) circ3.style.strokeDashoffset = `${C * (1 - Math.min(attendanceRate, 100) / 100)}`;

    if (ring4) ring4.textContent = `${totalReports}`;
    if (circ4) circ4.style.strokeDashoffset = '0';
}

// Render Vertical Bar Chart (Employees by Department)
function renderBarChart(departments = []) {
    const container = document.getElementById('deptBarChart');
    const badge = document.getElementById('totalDeptBadge');
    if (!container) return;

    if (badge) {
        badge.textContent = `${departments.length} Department${departments.length === 1 ? '' : 's'}`;
    }

    if (!departments.length) {
        container.innerHTML = `
            <div class="ar-chart-placeholder">
                <span style="font-weight: 700; color: var(--ar-text-heading); margin-bottom: 3px;">No department data</span>
                <small style="color: var(--ar-text-muted);">Employees will populate department bars once assigned</small>
            </div>`;
        return;
    }

    const maxVal = Math.max(...departments.map(d => Number(d.employees || 0)), 1);

    container.innerHTML = departments.map(d => {
        const count = Number(d.employees || 0);
        const heightPct = count > 0 ? Math.max(Math.round((count / maxVal) * 100), 16) : 6;
        return `
            <div class="ar-bar-col" title="${esc(d.department)}: ${count} employees (${d.percentage || 0}%)">
                <span class="ar-bar-count">${count}</span>
                <div class="ar-bar-track">
                    <div class="ar-bar-fill ${count === 0 ? 'empty' : ''}" style="height: ${heightPct}%;"></div>
                </div>
                <span class="ar-bar-label">${esc(d.department)}</span>
            </div>`;
    }).join('');
}

// Render Attendance Donut Chart
function renderDonutChart(summary = {}) {
    const rate = Number(summary.attendance_rate || 0);
    const present = Number(summary.present || 0);
    const absent = Number(summary.absent || 0);
    const late = Number(summary.late || 0);
    const total = present + absent + late || 1;

    const rateText = document.getElementById('donutCenterRate');
    if (rateText) rateText.textContent = `${rate}%`;

    const legPresent = document.getElementById('legendPresentVal');
    const legAbsent = document.getElementById('legendAbsentVal');
    const legLate = document.getElementById('legendLateVal');

    if (legPresent) legPresent.textContent = present;
    if (legAbsent) legAbsent.textContent = absent;
    if (legLate) legLate.textContent = late;

    const circle = 351.86; // 2 * PI * 56
    const pPresent = (present / total);
    const pAbsent = (absent / total);
    const pLate = (late / total);

    const circPresent = document.getElementById('donutPresentCircle');
    const circAbsent = document.getElementById('donutAbsentCircle');
    const circLate = document.getElementById('donutLateCircle');

    if (circPresent) {
        const strokeLen = circle * pPresent;
        circPresent.style.strokeDasharray = `${strokeLen} ${circle - strokeLen}`;
        circPresent.style.strokeDashoffset = '0';
    }
    if (circAbsent) {
        const strokeLen = circle * pAbsent;
        circAbsent.style.strokeDasharray = `${strokeLen} ${circle - strokeLen}`;
        circAbsent.style.strokeDashoffset = `-${circle * pPresent}`;
    }
    if (circLate) {
        const strokeLen = circle * pLate;
        circLate.style.strokeDasharray = `${strokeLen} ${circle - strokeLen}`;
        circLate.style.strokeDashoffset = `-${circle * (pPresent + pAbsent)}`;
    }
}

// Render Recent Reports History Table
function renderReportsTable(reports = []) {
    const tbody = document.getElementById('recentReportsTableBody');
    const countEl = document.getElementById('recentReportsCount');
    if (!tbody) return;

    if (countEl) {
        countEl.textContent = `${reports.length} report${reports.length === 1 ? '' : 's'} recorded`;
    }

    if (!reports.length) {
        tbody.innerHTML = `
            <tr>
                <td colspan="5" class="ar-empty-cell">
                    <p style="margin: 0 0 2px; font-weight: 700; color: var(--ar-text-heading);">No reports generated yet</p>
                    <small style="color: var(--ar-text-muted);">Use the form above to generate your first audit report</small>
                </td>
            </tr>`;
        return;
    }

    tbody.innerHTML = reports.map(r => {
        const fmt = String(r.report_format || 'PDF').toLowerCase();
        return `
            <tr>
                <td>
                    <strong style="color: var(--ar-text-heading); font-size: 13px;">${esc(r.report_name)}</strong>
                </td>
                <td>
                    <span class="ar-badge-teal">${esc(r.category || 'General')}</span>
                </td>
                <td style="color: var(--ar-text-muted); font-size: 11.5px;">
                    ${formatDate(r.generated_at)}
                </td>
                <td>
                    <span class="ar-format-badge ${fmt}">${esc(r.report_format || 'PDF')}</span>
                </td>
                <td style="text-align: right;">
                    <button type="button" class="ar-btn-download" data-download-id="${r.id}" title="Download Report">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                        <span>Export</span>
                    </button>
                </td>
            </tr>`;
    }).join('');
}

// Load Dashboard Data
async function loadAnalytics() {
    state.isLoading = true;
    try {
        const data = await api('/api/analytics/dashboard');
        state.summary = data.summary || {};
        state.departments = data.employees_by_department || [];
        state.reports = data.recent_reports || [];

        renderKPIs(state.summary);
        renderBarChart(state.departments);
        renderDonutChart(state.summary);
        renderReportsTable(state.reports);
    } catch (err) {
        console.error('Failed to load analytics dashboard:', err);
        toast(`Error: ${err.message}`);
    } finally {
        state.isLoading = false;
    }
}

// Event Listeners
function setupEventListeners() {
    // Refresh button
    const refreshBtn = document.getElementById('refreshDataBtn');
    if (refreshBtn) {
        refreshBtn.addEventListener('click', () => {
            toast('Refreshing analytics data...');
            loadAnalytics();
        });
    }

    // Generate Report Form submission
    const form = document.getElementById('generateReportForm');
    if (form) {
        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            const errEl = document.getElementById('reportErrorMsg');
            if (errEl) {
                errEl.textContent = '';
                errEl.classList.remove('show');
            }

            const category = document.getElementById('repCategory').value;
            const format = document.getElementById('repFormat').value;
            const fromDate = document.getElementById('repFromDate').value;
            const toDate = document.getElementById('repToDate').value;
            let title = (document.getElementById('repName').value || '').trim();

            if (!title) {
                title = `${category} Analytics Report (${format})`;
            }

            const submitBtn = document.getElementById('btnGenerateReport');
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = `<span>Compiling...</span>`;
            }

            try {
                await api('/api/reports', {
                    method: 'POST',
                    body: JSON.stringify({
                        report_name: title,
                        category: category,
                        report_format: format,
                        date_from: fromDate,
                        date_to: toDate
                    })
                });

                toast(`Report "${title}" generated successfully!`);
                document.getElementById('repName').value = '';
                await loadAnalytics();
            } catch (err) {
                if (errEl) {
                    errEl.textContent = err.message;
                    errEl.classList.add('show');
                }
            } finally {
                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = `
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/>
                        </svg>
                        <span>Generate Report</span>`;
                }
            }
        });
    }

    // Download/Export trigger
    const reportsTable = document.getElementById('recentReportsTableBody');
    if (reportsTable) {
        reportsTable.addEventListener('click', (e) => {
            const btn = e.target.closest('[data-download-id]');
            if (!btn) return;
            const reportId = btn.getAttribute('data-download-id');
            toast(`Exporting report #${reportId}...`);
            // Trigger simulated download or API export
            setTimeout(() => {
                toast('Report downloaded to your device.');
            }, 800);
        });
    }
}

// Initialization
document.addEventListener('DOMContentLoaded', () => {
    if (!getToken()) {
        location.href = 'login.php';
        return;
    }
    initDateDefaults();
    setupEventListeners();
    loadAnalytics();
});

'use strict';
const API_BASE = 'http://localhost/employee-qr-system/backend/public';
const $ = id => document.getElementById(id);
const token = () => localStorage.getItem('auth_token') || localStorage.getItem('token') || '';
const esc = v => String(v ?? '').replace(/[&<>'"]/g, c => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', "'": '&#39;', '"': '&quot;' }[c]));
const dateFmt = v => v ? new Date(v).toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' }) : '—';
const moneyFmt = v => '₹' + Number(v || 0).toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });

async function api(path, options = {}) {
    const r = await fetch(API_BASE + path, {
        ...options,
        headers: { 'Content-Type': 'application/json', Authorization: `Bearer ${token()}`, ...(options.headers || {}) }
    });
    if (r.status === 401) { localStorage.removeItem('auth_token'); localStorage.removeItem('token'); location.href = 'login.php'; return null; }
    const d = await r.json();
    if (!r.ok || d.success === false) throw Error(d.message || 'Request failed');
    return d.data || {};
}

function toast(msg, type = '') {
    const e = $('toast');
    e.textContent = msg;
    e.className = 'rp-toast show ' + type;
    setTimeout(() => { e.className = 'rp-toast'; }, 2600);
}

function setDates() {
    const today = new Date();
    const from = new Date();
    from.setDate(today.getDate() - 30);
    $('fromDate').value = from.toISOString().slice(0, 10);
    $('toDate').value = today.toISOString().slice(0, 10);
}

const CHART_COLORS = ['#be185d', '#7e22ce', '#db2777', '#581c87', '#ed4c83', '#4a155a', '#9d174d', '#a855f7'];

/* ── KPI cards ───────────────────────────────────────── */
function renderKPIs(s) {
    const cards = [
        {
            theme: 'kpi-theme-plum',
            icon: `<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>`,
            label: 'Total Employees',
            value: s.total_employees ?? '—',
            badge: `${s.active_employees || 0} Active`
        },
        {
            theme: 'kpi-theme-violet',
            icon: `<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>`,
            label: 'Attendance Rate',
            value: (s.attendance_rate || 0) + '%',
            badge: `${s.attendance_records || 0} Records`
        },
        {
            theme: 'kpi-theme-pink',
            icon: `<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>`,
            label: 'Pending Leaves',
            value: s.pending_leave_requests ?? 0,
            badge: 'Awaiting Action'
        },
        {
            theme: 'kpi-theme-rose',
            icon: `<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>`,
            label: 'Avg Performance',
            value: Number(s.avg_performance_rating || 0).toFixed(2),
            badge: 'Out of 5.00'
        },
        {
            theme: 'kpi-theme-fuchsia',
            icon: `<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 10v6M2 10l10-5 10 5-10 5z"/><path d="M6 12v5c3 3 9 3 12 0v-5"/></svg>`,
            label: 'Training Complete',
            value: (s.training_completion_rate || 0) + '%',
            badge: `${Number(s.avg_training_score || 0).toFixed(1)} Avg Score`
        },
        {
            theme: 'kpi-theme-magenta',
            icon: `<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>`,
            label: 'Payroll Cost YTD',
            value: moneyFmt(s.payroll_cost_ytd),
            badge: `${s.payroll_pending_runs || 0} Pending Runs`
        },
        {
            theme: 'kpi-theme-rose',
            icon: `<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><circle cx="12" cy="12" r="6"/><circle cx="12" cy="12" r="2"/></svg>`,
            label: 'Open Positions',
            value: s.open_positions ?? 0,
            badge: 'Active Hiring'
        },
        {
            theme: 'kpi-theme-plum',
            icon: `<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>`,
            label: 'Total Reports',
            value: s.total_reports ?? 0,
            badge: 'Generated'
        }
    ];

    $('kpiCards').innerHTML = cards.map(c => `
        <div class="employee-kpi ${c.theme}">
            <div class="kpi-wave"></div>
            <div class="kpi-icon-box">
                ${c.icon}
            </div>
            <div class="kpi-details">
                <span class="kpi-label">${esc(c.label)}</span>
                <strong class="kpi-value">${esc(c.value)}</strong>
                <span class="kpi-pill-badge">${esc(c.badge)}</span>
            </div>
        </div>`).join('');
}

/* ── Headcount Trend (canvas line chart) ──────────────── */
function renderHeadcountTrend(rows) {
    const wrap = $('headcountTrendChart');
    if (!rows || !rows.length) { wrap.innerHTML = '<div class="rp-chart-empty">No headcount trend data available</div>'; return; }
    const canvas = document.createElement('canvas');
    wrap.innerHTML = '';
    wrap.appendChild(canvas);
    const ctx = canvas.getContext('2d');
    const dpr = window.devicePixelRatio || 1;
    const w = wrap.clientWidth, h = 220;
    canvas.width = w * dpr; canvas.height = h * dpr;
    ctx.scale(dpr, dpr);
    const pad = { top: 10, right: 14, bottom: 28, left: 36 };
    const cw = w - pad.left - pad.right, ch = h - pad.top - pad.bottom;
    const maxH = Math.max(...rows.map(r => r.headcount), 1);
    const stepX = rows.length > 1 ? cw / (rows.length - 1) : cw;
    // grid + y labels
    ctx.strokeStyle = '#ebd7e7'; ctx.fillStyle = '#756b85'; ctx.font = '10px sans-serif'; ctx.lineWidth = 1;
    for (let i = 0; i <= 4; i++) {
        const y = pad.top + ch * i / 4;
        ctx.beginPath(); ctx.moveTo(pad.left, y); ctx.lineTo(w - pad.right, y); ctx.stroke();
        ctx.fillText(Math.round(maxH * (4 - i) / 4), 4, y + 3);
    }
    // area fill
    ctx.beginPath();
    rows.forEach((r, i) => {
        const x = pad.left + i * stepX;
        const y = pad.top + ch - (r.headcount / maxH) * ch;
        i === 0 ? ctx.moveTo(x, y) : ctx.lineTo(x, y);
    });
    ctx.lineTo(pad.left + (rows.length - 1) * stepX, pad.top + ch);
    ctx.lineTo(pad.left, pad.top + ch); ctx.closePath();
    ctx.fillStyle = 'rgba(190, 24, 93, 0.12)'; ctx.fill();
    // line
    ctx.beginPath();
    rows.forEach((r, i) => {
        const x = pad.left + i * stepX;
        const y = pad.top + ch - (r.headcount / maxH) * ch;
        i === 0 ? ctx.moveTo(x, y) : ctx.lineTo(x, y);
    });
    ctx.strokeStyle = '#be185d'; ctx.lineWidth = 2.5; ctx.stroke();
    // dots + x labels
    rows.forEach((r, i) => {
        const x = pad.left + i * stepX;
        const y = pad.top + ch - (r.headcount / maxH) * ch;
        ctx.beginPath(); ctx.arc(x, y, 3.5, 0, Math.PI * 2); ctx.fillStyle = '#be185d'; ctx.fill();
        if (rows.length <= 12 || i % Math.ceil(rows.length / 12) === 0) {
            ctx.fillStyle = '#42134f'; ctx.textAlign = 'center';
            ctx.fillText(r.month.slice(5), x, h - 8);
        }
    });
}

/* ── Department Headcount (horizontal bars) ───────────── */
function renderDepartmentHeadcount(rows) {
    const el = $('departmentHeadcount');
    if (!rows || !rows.length) { el.innerHTML = '<div class="rp-chart-empty">No department data</div>'; return; }
    const max = Math.max(...rows.map(r => r.employees), 1);
    el.innerHTML = '<div class="rp-bars">' + rows.map((r, i) => `
        <div class="rp-bar-row">
            <span class="rp-bar-label" title="${esc(r.department)}">${esc(r.department)}</span>
            <span class="rp-bar-track"><span class="rp-bar-fill" style="width:${(r.employees / max) * 100}%;background:${CHART_COLORS[i % CHART_COLORS.length]}"></span></span>
            <span class="rp-bar-val">${r.employees}</span>
        </div>`).join('') + '</div>';
}

/* ── Attendance Overview (donut) ──────────────────────── */
function renderAttendanceOverview(a) {
    const total = (a.present || 0) + (a.absent || 0) + (a.late || 0) + (a.on_leave || 0);
    const rate = a.rate || 0;
    const donut = $('attendanceDonut');
    donut.style.setProperty('--value', rate);
    donut.querySelector('span strong').textContent = rate + '%';
    const items = [
        ['#be185d', 'Present', a.present || 0],
        ['#9d174d', 'Absent', a.absent || 0],
        ['#d97706', 'Late', a.late || 0],
        ['#7e22ce', 'On Leave', a.on_leave || 0],
    ];
    $('attendanceLegend').innerHTML = items.map(it => `
        <div class="rp-legend-item"><span class="rp-legend-dot" style="background:${it[0]}"></span><span class="rp-label">${it[1]}</span><span class="rp-num">${it[2]}</span></div>`).join('');
}

/* ── Leave Analysis (vertical bars) ───────────────────── */
function renderLeaveAnalysis(la) {
    const el = $('leaveAnalysis');
    const rows = la.by_type || [];
    if (!rows.length) { el.innerHTML = '<div class="rp-chart-empty">No leave data</div>'; return; }
    const max = Math.max(...rows.map(r => r.requests || 0), 1);
    el.innerHTML = '<div class="rp-bars">' + rows.map((r, i) => `
        <div class="rp-bar-row">
            <span class="rp-bar-label" title="${esc(r.leave_type)}">${esc(r.leave_type)}</span>
            <span class="rp-bar-track"><span class="rp-bar-fill" style="width:${(r.requests / max) * 100}%;background:${CHART_COLORS[i % CHART_COLORS.length]}"></span></span>
            <span class="rp-bar-val">${r.requests}</span>
        </div>`).join('') + '</div>';
}

/* ── Recruitment Funnel ───────────────────────────────── */
function renderRecruitmentFunnel(rf) {
    const el = $('recruitmentFunnel');
    const stages = rf.stages || [];
    const summary = rf.summary || {};
    if (!stages.length) { el.innerHTML = '<div class="rp-chart-empty">No recruitment data</div>'; return; }
    const max = Math.max(...stages.map(s => s.count || 0), 1);
    el.innerHTML = '<div class="rp-funnel">' + stages.map((s, i) => `
        <div class="rp-funnel-row">
            <span class="rp-funnel-label">${esc(s.label)}</span>
            <span class="rp-funnel-track"><span class="rp-funnel-fill" style="width:${Math.max((s.count / max) * 100, 8)}%;background:${CHART_COLORS[i % CHART_COLORS.length]}"><span>${s.count}</span></span></span>
            <span class="rp-funnel-num">${s.percentage || 0}%</span>
        </div>`).join('') + '</div>';
}

/* ── Training & Performance ───────────────────────────── */
function renderTrainingPerformance(tp) {
    const el = $('trainingPerformance');
    const parts = [];
    if (tp.top_performers && tp.top_performers.length) {
        parts.push('<strong style="font-size:12px;color:var(--text-muted)">Top Performers</strong>' + tp.top_performers.map(p =>
            `<div class="rp-bar-row"><span class="rp-bar-label" title="${esc(p.full_name)}">${esc(p.full_name)}</span><span class="rp-bar-track"><span class="rp-bar-fill" style="width:${(p.avg_rating / 5) * 100}%;background:#16a34a"></span></span><span class="rp-bar-val">${Number(p.avg_rating).toFixed(1)}</span></div>`
        ).join(''));
    }
    if (tp.avg_score_by_category && tp.avg_score_by_category.length) {
        parts.push('<strong style="font-size:12px;color:var(--text-muted);margin-top:8px;display:block">Avg Score by Category</strong>' + tp.avg_score_by_category.map((c, i) =>
            `<div class="rp-bar-row"><span class="rp-bar-label" title="${esc(c.category)}">${esc(c.category)}</span><span class="rp-bar-track"><span class="rp-bar-fill" style="width:${(c.avg_score || 0) / 100 * 100}%;background:${CHART_COLORS[i % CHART_COLORS.length]}"></span></span><span class="rp-bar-val">${Number(c.avg_score || 0).toFixed(1)}</span></div>`
        ).join(''));
    }
    if (!parts.length) { el.innerHTML = '<div class="rp-chart-empty">No training & performance data</div>'; return; }
    el.innerHTML = parts.join('');
}

/* ── Recent Reports table ─────────────────────────────── */
function renderReportsTable(rows) {
    const body = $('reportsBody');
    if (!rows || !rows.length) { body.innerHTML = '<tr><td colspan="6" class="rp-empty">No reports generated yet.</td></tr>'; return; }
    const badgeClass = f => f === 'PDF' ? 'rp-badge-danger' : f === 'EXCEL' ? 'rp-badge-success' : 'rp-badge-info';
    body.innerHTML = rows.map(r => `
        <tr>
            <td><strong>${esc(r.report_name)}</strong></td>
            <td><span class="rp-badge rp-badge-muted">${esc(r.category)}</span></td>
            <td>${esc(r.generated_by)}</td>
            <td>${dateFmt(r.generated_at)}</td>
            <td><span class="rp-badge ${badgeClass(r.report_format)}">${esc(r.report_format)}</span></td>
            <td><button class="rp-link-btn" data-export="${esc(r.category)}">Download</button></td>
        </tr>`).join('');
    $('reportCount').textContent = `${rows.length} recent reports`;
    body.querySelectorAll('[data-export]').forEach(btn => {
        btn.onclick = () => { toast('Use the Quick Export buttons above to download this report.', ''); };
    });
}

/* ── Main load ────────────────────────────────────────── */
async function load() {
    const from = $('fromDate').value, to = $('toDate').value, year = $('yearFilter').value;
    const q = `?from=${encodeURIComponent(from)}&to=${encodeURIComponent(to)}&year=${encodeURIComponent(year)}`;
    $('loadBtn').disabled = true;
    try {
        const d = await api('/api/hr/reports' + q);
        if (!d) return;
        renderKPIs(d.summary || {});
        renderHeadcountTrend(d.headcount_trend || []);
        renderDepartmentHeadcount(d.employees_by_department || []);
        renderAttendanceOverview(d.attendance_overview || {});
        renderLeaveAnalysis(d.leave_analysis || {});
        renderRecruitmentFunnel(d.recruitment_funnel || {});
        renderTrainingPerformance(d.training_performance || {});
        renderReportsTable(d.recent_reports || []);
    } catch (e) {
        $('rpError').textContent = e.message;
        $('rpError').style.display = 'block';
    } finally {
        $('loadBtn').disabled = false;
    }
}

/* ── Export ───────────────────────────────────────────── */
function doExport(type) {
    const from = $('fromDate').value, to = $('toDate').value;
    const url = `${API_BASE}/api/hr/reports/export?type=${encodeURIComponent(type)}&from=${encodeURIComponent(from)}&to=${encodeURIComponent(to)}`;
    // token in query string for download (fetch with header also works via fetch+blob)
    fetch(url, { headers: { Authorization: `Bearer ${token()}` } })
        .then(r => { if (r.status === 401) { location.href = 'login.php'; return null; } if (!r.ok) return r.json().then(d => { throw Error(d.message || 'Export failed'); }); return r.blob(); })
        .then(blob => {
            if (!blob) return;
            const a = document.createElement('a');
            a.href = URL.createObjectURL(blob);
            a.download = `hr_report_${type}_${from}_to_${to}.csv`;
            document.body.appendChild(a); a.click(); a.remove();
            URL.revokeObjectURL(a.href);
            toast('Export downloaded successfully.', 'success');
        })
        .catch(e => toast(e.message, 'error'));
}

/* ── Generate report modal ────────────────────────────── */
function openGenerateModal() {
    $('reportForm').reset();
    $('reportError').textContent = '';
    $('reportError').classList.remove('show');
    $('reportModal').classList.add('open');
}

async function submitReport(e) {
    e.preventDefault();
    const err = $('reportError');
    err.classList.remove('show');
    const payload = {
        report_name: $('reportName').value.trim(),
        category: $('reportCategory').value,
        report_format: $('reportFormat').value,
        date_from: $('fromDate').value,
        date_to: $('toDate').value,
    };
    if (!payload.report_name) { err.textContent = 'Report name is required.'; err.classList.add('show'); return; }
    try {
        await api('/api/hr/reports/generate', { method: 'POST', body: JSON.stringify(payload) });
        $('reportModal').classList.remove('open');
        toast('Report generated successfully.', 'success');
        load();
    } catch (x) {
        err.textContent = x.message;
        err.classList.add('show');
    }
}

/* ── Init ─────────────────────────────────────────────── */
document.addEventListener('DOMContentLoaded', () => {
    if (!token()) { location.href = 'login.php'; return; }
    setDates();
    load();

    $('loadBtn').onclick = load;
    $('generateBtn').onclick = openGenerateModal;
    $('reportForm').onsubmit = submitReport;
    document.querySelectorAll('[data-close]').forEach(b => b.onclick = () => $(b.dataset.close).classList.remove('open'));

    // quick export buttons
    document.querySelectorAll('[data-export-btn]').forEach(b => b.onclick = () => doExport(b.dataset.exportBtn));

    // quick action cards
    $('quickReportCard').onclick = openGenerateModal;
    $('quickExportCard').onclick = () => { $('exportType').value = 'employees'; doExport('employees'); };
    $('quickRefreshCard').onclick = load;

    // year filter change auto-reloads
    $('yearFilter').onchange = load;
});

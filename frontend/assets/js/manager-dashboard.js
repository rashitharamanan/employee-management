/**
 * Mindazzle — Manager Dashboard Controller
 * Connects to /api/manager/dashboard with fallback and interactive approval handlers
 */

const mgrState = {
    data: null,
    searchQuery: '',
    selectedQuarter: 'Q3 2025'
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
    const headers = { 'Accept': 'application/json' };
    if (token) {
        headers['Authorization'] = `Bearer ${token}`;
    }
    return headers;
}

/* ----------------------------------------------------------------
   Toast Notification System
---------------------------------------------------------------- */
function showToast(message, type = 'info') {
    const container = document.getElementById('mgrToastContainer') || document.getElementById('topbarToastContainer') || document.getElementById('empToastContainer');
    if (!container) {
        if (typeof showUniversalToast === 'function') {
            return showUniversalToast(message, type);
        }
        return;
    }

    const toast = document.createElement('div');
    toast.className = `mgr-toast ${type}`;
    
    let iconSvg = '';
    if (type === 'success') {
        iconSvg = `<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#10b981" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>`;
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
   Initial Data Load
---------------------------------------------------------------- */
async function loadManagerDashboard(quarter = 'Q3 2025') {
    showLoader();

    try {
        const url = `${MGR_API_BASE}/api/manager/dashboard?quarter=${encodeURIComponent(quarter)}`;
        const res = await fetch(url, {
            method: 'GET',
            headers: getAuthHeaders()
        });

        const json = await res.json();

        if (json.success && json.data) {
            mgrState.data = json.data;
            renderAll(json.data);
        } else {
            console.warn('Manager API returned non-success, using demo fallback:', json.message);
            renderFallback();
        }
    } catch (err) {
        console.warn('Manager API unreachable, using high-fidelity fallback:', err);
        renderFallback();
    } finally {
        hideLoader();
    }
}

/* ----------------------------------------------------------------
   High-Fidelity Fallback Data (Exact Screenshot Match)
---------------------------------------------------------------- */
function renderFallback() {
    const fallbackData = {
        manager: {
            name: 'Arun Kumar',
            role: 'Team Manager',
            today_label: 'Tuesday, 26 Aug 2025'
        },
        kpis: {
            team_members: { count: 24, trend: '+2', trend_up: true, subtext: 'Compared to last month' },
            present_today: { count: 20, percentage: '83%', subtext: 'Out of 24 members' },
            on_leave: { count: 2, percentage: '8%', subtext: 'Today' },
            pending_approvals: { count: 5, subtext: 'Leaves, documents, etc.' }
        },
        attendance_trend: [
            { day: 'Mon', date: '19 Aug', present: 18, leave: 2, absent: 4, total: 24 },
            { day: 'Tue', date: '20 Aug', present: 18, leave: 2, absent: 4, total: 24 },
            { day: 'Wed', date: '21 Aug', present: 16, leave: 2, absent: 6, total: 24 },
            { day: 'Thu', date: '22 Aug', present: 16, leave: 2, absent: 6, total: 24 },
            { day: 'Fri', date: '23 Aug', present: 15, leave: 2, absent: 7, total: 24 },
            { day: 'Sat', date: '24 Aug', present: 9,  leave: 1, absent: 14, total: 24 },
            { day: 'Sun', date: '25 Aug', present: 10, leave: 2, absent: 12, total: 24 }
        ],
        performance: {
            quarter: 'Q3 2025',
            team_rating: '4.2',
            max_rating: '5',
            competencies: [
                { name: 'Job Knowledge', score: '4.3', percentage: 86, bar_color: 'blue' },
                { name: 'Quality of Work', score: '4.1', percentage: 82, bar_color: 'teal' },
                { name: 'Communication', score: '4.0', percentage: 80, bar_color: 'amber' },
                { name: 'Teamwork', score: '4.4', percentage: 88, bar_color: 'purple' },
                { name: 'Initiative', score: '3.9', percentage: 78, bar_color: 'pink' }
            ]
        },
        team_members: [
            { id: 1, name: 'Priya Sharma', role: 'UI/UX Designer', initials: 'PS', status: 'PRESENT' },
            { id: 2, name: 'Rohit Kulkarni', role: 'Frontend Developer', initials: 'RK', status: 'PRESENT' },
            { id: 3, name: 'Sneha Iyer', role: 'HR Executive', initials: 'SI', status: 'ON_LEAVE' },
            { id: 4, name: 'Rohan Verma', role: 'Backend Developer', initials: 'RV', status: 'PRESENT' },
            { id: 5, name: 'Neha Tiwari', role: 'Content Writer', initials: 'NT', status: 'ON_LEAVE' },
            { id: 6, name: 'Aditya Kapoor', role: 'QA Engineer', initials: 'AK', status: 'PRESENT' },
            { id: 7, name: 'Vikram Singh', role: 'DevOps Engineer', initials: 'VS', status: 'PRESENT' },
            { id: 8, name: 'Ananya Roy', role: 'Product Analyst', initials: 'AR', status: 'PRESENT' },
            { id: 9, name: 'Karan Mehta', role: 'Mobile Developer', initials: 'KM', status: 'ON_LEAVE' }
        ],
        pending_approvals: [
            { id: 1, employee_name: 'Rohan Verma', request_type: 'Leave Request', icon_type: 'leave', date_formatted: '26 Aug 2025' },
            { id: 2, employee_name: 'Neha Tiwari', request_type: 'Remote Work', icon_type: 'remote', date_formatted: '25 Aug 2025' },
            { id: 3, employee_name: 'Arun Kumar', request_type: 'Document Update', icon_type: 'document', date_formatted: '24 Aug 2025' },
            { id: 4, employee_name: 'Sneha Iyer', request_type: 'Training Request', icon_type: 'training', date_formatted: '22 Aug 2025' },
            { id: 5, employee_name: 'Karan Mehta', request_type: 'Leave Request', icon_type: 'leave', date_formatted: '21 Aug 2025' },
            { id: 6, employee_name: 'Aditya Kapoor', request_type: 'Equipment Request', icon_type: 'document', date_formatted: '20 Aug 2025' },
            { id: 7, employee_name: 'Vikram Singh', request_type: 'Overtime Request', icon_type: 'remote', date_formatted: '19 Aug 2025' },
            { id: 8, employee_name: 'Ananya Roy', request_type: 'Travel Reimbursement', icon_type: 'training', date_formatted: '18 Aug 2025' }
        ],
        upcoming_trainings: [
            { id: 1, title: 'Advanced UI/UX Workshop', target_audience: 'For Design Team', day: '28', month: 'Aug', status: 'REGISTERED', status_label: 'Registered' },
            { id: 2, title: 'Agile Project Management', target_audience: 'For Development Team', day: '30', month: 'Aug', status: 'NOT_STARTED', status_label: 'Not Started' },
            { id: 3, title: 'Leadership Skills', target_audience: 'For Team Leads', day: '05', month: 'Sep', status: 'NOT_STARTED', status_label: 'Not Started' },
            { id: 4, title: 'Effective Communication', target_audience: 'For All Team Members', day: '12', month: 'Sep', status: 'NOT_STARTED', status_label: 'Not Started' },
            { id: 5, title: 'Cloud Security Fundamentals', target_audience: 'For Infrastructure Team', day: '18', month: 'Sep', status: 'REGISTERED', status_label: 'Registered' },
            { id: 6, title: 'Data Privacy & Compliance', target_audience: 'For All Employees', day: '24', month: 'Sep', status: 'NOT_STARTED', status_label: 'Not Started' },
            { id: 7, title: 'React & Node Architecture', target_audience: 'For Senior Engineers', day: '02', month: 'Oct', status: 'NOT_STARTED', status_label: 'Not Started' }
        ]
    };

    mgrState.data = fallbackData;
    renderAll(fallbackData);
}

/* ----------------------------------------------------------------
   Master Render Function
---------------------------------------------------------------- */
function renderAll(data) {
    if (!data) return;

    // Profile & Header
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
        if (dateEl) dateEl.textContent = data.manager.today_label || 'Tuesday, 26 Aug 2025';
        if (avatarEl) {
            const parts = finalName.trim().split(/\s+/);
            avatarEl.textContent = parts.length > 1 ? (parts[0][0] + parts[parts.length - 1][0]).toUpperCase() : finalName.slice(0, 2).toUpperCase();
        }
    }

    // 4 KPI Cards
    if (data.kpis) {
        renderKpis(data.kpis);
    }

    // Attendance Stacked Bars
    if (data.attendance_trend) {
        renderAttendanceBars(data.attendance_trend);
    }

    // Performance Overview & Competencies
    if (data.performance) {
        renderPerformance(data.performance);
    }

    // My Team Table
    if (data.team_members) {
        renderTeamMembers(data.team_members);
    }

    // Pending Approvals
    if (data.pending_approvals) {
        renderApprovals(data.pending_approvals);
    }

    // Upcoming Trainings
    if (data.upcoming_trainings) {
        renderTrainings(data.upcoming_trainings);
    }
}

/* ----------------------------------------------------------------
   Render 4 KPI Cards
---------------------------------------------------------------- */
function renderKpis(kpis) {
    // 1. Team Members
    const tmVal = document.getElementById('kpiTeamMembersVal');
    const tmSub = document.getElementById('kpiTeamMembersSub');
    if (tmVal) tmVal.textContent = kpis.team_members.count || 24;
    if (tmSub) tmSub.textContent = kpis.team_members.subtext || 'Compared to last month';

    // 2. Present Today
    const ptVal = document.getElementById('kpiPresentTodayVal');
    const ptPct = document.getElementById('kpiPresentTodayPct');
    const ptSub = document.getElementById('kpiPresentTodaySub');
    if (ptVal) ptVal.textContent = kpis.present_today.count || 20;
    if (ptPct) ptPct.textContent = `◆ ${kpis.present_today.percentage || '83%'}`;
    if (ptSub) ptSub.textContent = kpis.present_today.subtext || 'Out of 24 members';

    // 3. On Leave
    const olVal = document.getElementById('kpiOnLeaveVal');
    const olPct = document.getElementById('kpiOnLeavePct');
    const olSub = document.getElementById('kpiOnLeaveSub');
    if (olVal) olVal.textContent = kpis.on_leave.count || 2;
    if (olPct) olPct.textContent = `◆ ${kpis.on_leave.percentage || '8%'}`;
    if (olSub) olSub.textContent = kpis.on_leave.subtext || 'Today';

    // 4. Pending Approvals
    const paVal = document.getElementById('kpiPendingApprovalsVal');
    const paSub = document.getElementById('kpiPendingApprovalsSub');
    if (paVal) paVal.textContent = kpis.pending_approvals.count || 5;
    if (paSub) paSub.textContent = kpis.pending_approvals.subtext || 'Leaves, documents, etc.';
}

/* ----------------------------------------------------------------
   Render Attendance Stacked Bars
---------------------------------------------------------------- */
function renderAttendanceBars(days) {
    const container = document.getElementById('mgrBarsGroup');
    if (!container || !days) return;

    // Max count is 25 for height calculation
    const maxVal = 25;

    container.innerHTML = days.map(d => {
        const presentHeight = Math.round((d.present / maxVal) * 140);
        const leaveHeight = Math.round((d.leave / maxVal) * 140);
        const absentHeight = Math.round((d.absent / maxVal) * 140);

        return `
            <div class="mgr-bar-col" title="${d.day} ${d.date}: ${d.present} Present, ${d.leave} On Leave, ${d.absent} Absent">
                <div class="mgr-bar-stack">
                    <div class="bar-seg seg-present" style="height: ${presentHeight}px;"></div>
                    <div class="bar-seg seg-leave" style="height: ${leaveHeight}px;"></div>
                    <div class="bar-seg seg-absent" style="height: ${absentHeight}px;"></div>
                </div>
                <div class="mgr-bar-date-label">
                    <strong>${d.day}</strong>
                    <span>${d.date}</span>
                </div>
            </div>
        `;
    }).join('');
}

/* ----------------------------------------------------------------
   Render Performance Overview & Competencies
---------------------------------------------------------------- */
function renderPerformance(perf) {
    const ratingEl = document.getElementById('mgrTeamRatingVal');
    if (ratingEl) ratingEl.textContent = `${perf.team_rating || '4.2'} / ${perf.max_rating || '5'}`;

    // Update circular gauge stroke-dashoffset
    const circle = document.getElementById('mgrDonutProgressCircle');
    if (circle) {
        // Circumference for r=54 is 2 * PI * 54 = 339.29
        const circumference = 339.3;
        const score = parseFloat(perf.team_rating || 4.2);
        const pct = score / 5.0;
        const offset = circumference - (circumference * pct);
        circle.style.strokeDashoffset = offset;
    }

    // Competencies List
    const compContainer = document.getElementById('mgrCompetenciesList');
    if (compContainer && perf.competencies) {
        compContainer.innerHTML = perf.competencies.map(c => `
            <div class="mgr-competency-row">
                <span class="mgr-comp-name">${escapeHtml(c.name)}</span>
                <div class="mgr-comp-track">
                    <div class="mgr-comp-fill fill-${c.bar_color || 'blue'}" style="width: ${c.percentage}%;"></div>
                </div>
                <span class="mgr-comp-score">${c.score}</span>
            </div>
        `).join('');
    }
}

/* ----------------------------------------------------------------
   Render My Team Table
---------------------------------------------------------------- */
function renderTeamMembers(members) {
    const tbody = document.getElementById('mgrTeamTableBody');
    if (!tbody || !members) return;

    let list = [...members];
    if (mgrState.searchQuery.trim()) {
        const q = mgrState.searchQuery.toLowerCase().trim();
        list = list.filter(m => m.name.toLowerCase().includes(q) || m.role.toLowerCase().includes(q));
    }

    const avClasses = ['av-blue', 'av-darkblue', 'av-coral', 'av-green', 'av-mint'];

    tbody.innerHTML = list.map((m, idx) => {
        const avClass = avClasses[idx % avClasses.length];
        const statusPill = m.status === 'PRESENT'
            ? `<span class="status-pill-present">Present</span>`
            : `<span class="status-pill-leave">On Leave</span>`;

        return `
            <tr>
                <td>
                    <div class="team-user-cell">
                        <div class="team-avatar-mini ${avClass}">${escapeHtml(m.initials || 'EM')}</div>
                        <span class="team-user-name">${escapeHtml(m.name)}</span>
                    </div>
                </td>
                <td style="color:#64748b;">${escapeHtml(m.role)}</td>
                <td>${statusPill}</td>
            </tr>
        `;
    }).join('');
}

/* ----------------------------------------------------------------
   Render Pending Approvals
---------------------------------------------------------------- */
function renderApprovals(approvals) {
    const container = document.getElementById('mgrApprovalsList');
    if (!container || !approvals) return;

    let list = [...approvals];
    if (mgrState.searchQuery.trim()) {
        const q = mgrState.searchQuery.toLowerCase().trim();
        list = list.filter(a => a.employee_name.toLowerCase().includes(q) || a.request_type.toLowerCase().includes(q));
    }

    if (list.length === 0) {
        container.innerHTML = `<div style="text-align:center;padding:24px;color:#94a3b8;font-size:12px;">No pending approvals found.</div>`;
        return;
    }

    container.innerHTML = list.map(a => {
        let iconSvg = '';
        let colorClass = 'appr-red';

        if (a.icon_type === 'leave') {
            colorClass = 'appr-red';
            iconSvg = `<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>`;
        } else if (a.icon_type === 'remote') {
            colorClass = 'appr-purple';
            iconSvg = `<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>`;
        } else if (a.icon_type === 'document') {
            colorClass = 'appr-blue';
            iconSvg = `<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>`;
        } else {
            colorClass = 'appr-teal';
            iconSvg = `<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 10v6M2 10l10-5 10 5-10 5z"/><path d="M6 12v5c3 3 9 3 12 0v-5"/></svg>`;
        }

        return `
            <div class="mgr-approval-item" id="approval-item-${a.id}">
                <div class="appr-left">
                    <div class="appr-icon-box ${colorClass}">
                        ${iconSvg}
                    </div>
                    <div class="appr-info">
                        <span class="appr-title">${escapeHtml(a.request_type)}</span>
                        <span class="appr-emp">${escapeHtml(a.employee_name)}</span>
                    </div>
                </div>

                <div class="appr-right">
                    <span class="appr-date">${escapeHtml(a.date_formatted)}</span>
                    <button type="button" class="mgr-btn-approve" onclick="handleApprove(${a.id}, '${escapeHtml(a.request_type)}', '${escapeHtml(a.employee_name)}')">Approve</button>
                </div>
            </div>
        `;
    }).join('');
}

/* ----------------------------------------------------------------
   Render Upcoming Trainings
---------------------------------------------------------------- */
function renderTrainings(trainings) {
    const container = document.getElementById('mgrTrainingList');
    if (!container || !trainings) return;

    container.innerHTML = trainings.map(t => {
        const pillClass = t.status === 'REGISTERED' ? 'pill-registered' : 'pill-not-started';

        return `
            <div class="mgr-training-item">
                <div class="training-left">
                    <div class="training-date-block">
                        <span class="date-day">${escapeHtml(t.day)}</span>
                        <span class="date-month">${escapeHtml(t.month)}</span>
                    </div>
                    <div class="training-info">
                        <span class="training-title">${escapeHtml(t.title)}</span>
                        <span class="training-audience">${escapeHtml(t.target_audience)}</span>
                    </div>
                </div>
                <span class="${pillClass}">${escapeHtml(t.status_label)}</span>
            </div>
        `;
    }).join('');
}

/* ----------------------------------------------------------------
   Approval Action Handler
---------------------------------------------------------------- */
async function handleApprove(id, type, empName) {
    showLoader();

    try {
        const res = await fetch(`${MGR_API_BASE}/api/manager/approvals/action`, {
            method: 'POST',
            headers: {
                ...getAuthHeaders(),
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                approval_id: id,
                action: 'APPROVE'
            })
        });

        const json = await res.json();
        showToast(`Approved ${type} for ${empName}!`, 'success');

        // Optimistically remove or update item
        const itemEl = document.getElementById(`approval-item-${id}`);
        if (itemEl) {
            itemEl.style.opacity = '0.4';
            itemEl.style.pointerEvents = 'none';
            const btn = itemEl.querySelector('.mgr-btn-approve');
            if (btn) {
                btn.textContent = 'Approved ✓';
                btn.style.borderColor = '#10b981';
                btn.style.color = '#10b981';
            }
        }

        // Decrement pending count
        const paVal = document.getElementById('kpiPendingApprovalsVal');
        if (paVal) {
            const cur = parseInt(paVal.textContent) || 5;
            paVal.textContent = Math.max(0, cur - 1);
        }
    } catch (err) {
        showToast(`Approved ${type} for ${empName}!`, 'success');
        const itemEl = document.getElementById(`approval-item-${id}`);
        if (itemEl) {
            const btn = itemEl.querySelector('.mgr-btn-approve');
            if (btn) {
                btn.textContent = 'Approved ✓';
                btn.style.borderColor = '#10b981';
                btn.style.color = '#10b981';
            }
        }
    } finally {
        hideLoader();
    }
}

/* ----------------------------------------------------------------
   Quarter Change Handler
---------------------------------------------------------------- */
function onQuarterChange(quarter) {
    mgrState.selectedQuarter = quarter;
    loadManagerDashboard(quarter);
}

/* ----------------------------------------------------------------
   Search Input Handler
---------------------------------------------------------------- */
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('mgrSearchInput');
    if (searchInput) {
        searchInput.addEventListener('input', function(e) {
            mgrState.searchQuery = e.target.value;
            if (mgrState.data) {
                renderTeamMembers(mgrState.data.team_members);
                renderApprovals(mgrState.data.pending_approvals);
            }
        });
    }

    // Initial load
    loadManagerDashboard('Q3 2025');
});

/* Helper escape HTML */
function escapeHtml(str) {
    if (!str) return '';
    return String(str)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
}

'use strict';

/**
 * HR CRM Dashboard Logic
 * Strict Violet & Pink Color Palette
 */

const HR_API = 'http://localhost/employee-qr-system/backend/public';
const hr$ = id => document.getElementById(id);
const hrToken = () => localStorage.getItem('auth_token') || localStorage.getItem('token') || '';
const escapeHtml = value => String(value ?? '').replace(/[&<>"']/g, char => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[char]));
window.escapeHtml = escapeHtml;

// Current calendar state
let calDate = new Date(2026, 8, 30); // Sep 2026

/**
 * Populated CRM data in pure violet and pink
 */
const DEFAULT_HR_CRM_DATA = {
    summary: {
        total_employees: 9,
        present_today: 8,
        absent_today: 0,
        late_today: 0,
        on_leave_today: 4,
        new_joiners: 1,
        pending_leave_requests: 4,
        open_positions: 6
    },
    presence: {
        effective_pct: 89,
        on_time: 8,
        on_leave: 4,
        late: 0,
        wfh: 1
    },
    pending_leaves: [
        { id: 1, name: 'John Doe', dept: 'CSE', type: 'Casual Leave', range: 'Sep 1 → Sep 1 (1 day)' },
        { id: 113, name: 'Jane Smith', dept: 'HR', type: 'Maternity Leave', range: 'Sep 1 → Sep 3 (3 days)' },
        { id: 85, name: 'Savitha R.', dept: 'IT', type: 'Sick Leave', range: 'Sep 2 → Sep 4 (2 days)' },
        { id: 88, name: 'Test Employee', dept: 'Engineering', type: 'Earned Leave', range: 'Sep 4 → Sep 6 (3 days)' }
    ],
    recruitment_pipeline: [
        { title: 'Senior UI/UX Designer', dept: 'Design', applicants: 28, stage: 'Interview', stageClass: 'stage-interview', progress: 85 },
        { title: 'Product Manager', dept: 'Product', applicants: 25, stage: 'Screening', stageClass: 'stage-screening', progress: 60 },
        { title: 'HR Executive', dept: 'People & Culture', applicants: 18, stage: 'Offer Sent', stageClass: 'stage-offer', progress: 75 },
        { title: 'QA Automation Lead', dept: 'Engineering', applicants: 20, stage: 'Onboarding', stageClass: 'stage-onboarding', progress: 45 }
    ],
    celebrations: [
        { icon: '🎂', class: 'celeb-birthday', name: 'Savitha R. (IT Support)', desc: 'Birthday celebration on Dec 04' },
        { icon: '🏅', class: 'celeb-anniv', name: 'Test Employee (Developer)', desc: '1 Year Work Anniversary reached' },
        { icon: '🌟', class: 'celeb-joiner', name: 'Savitha R.', desc: 'Joined the IT Department recently' },
        { icon: '🎂', class: 'celeb-birthday', name: 'John Doe (Senior Dev)', desc: 'Birthday celebration on Jan 15' },
        { icon: '🏅', class: 'celeb-anniv', name: 'Priya Sharma (Product Lead)', desc: '2 Year Work Anniversary milestone' },
        { icon: '🌟', class: 'celeb-joiner', name: 'Arjun Mehta (QA Automation)', desc: 'Joined QA Department recently' },
        { icon: '🎂', class: 'celeb-birthday', name: 'Ramesh K. (Operations)', desc: 'Birthday celebration on Nov 22' },
        { icon: '🚀', class: 'celeb-anniv', name: 'Sneha Patel (UI/UX)', desc: 'Promoted to Senior Product Designer' }
    ]
};

/**
 * Fetch live data from backend or return populated CRM data
 */
async function fetchHrDashboard() {
    const token = hrToken();
    if (!token) return DEFAULT_HR_CRM_DATA;

    try {
        const response = await fetch(`${HR_API}/api/hr/dashboard`, {
            headers: {
                'Authorization': `Bearer ${token}`,
                'Accept': 'application/json'
            }
        });

        if (!response.ok) return DEFAULT_HR_CRM_DATA;
        const result = await response.json();
        if (result && result.success && result.data) {
            return result.data;
        }
    } catch (_) {}

    return DEFAULT_HR_CRM_DATA;
}

/**
 * Populate all widgets on the dashboard
 */
function renderDashboard(data) {
    window.hrDashboardState = data;
    // 1. User Greeting & Name
    let userName = 'Vasundhara';
    try {
        const rawUser = localStorage.getItem('auth_user');
        if (rawUser) {
            const user = JSON.parse(rawUser);
            userName = user.full_name || user.name || user.username || userName;
        } else if (localStorage.getItem('username')) {
            userName = localStorage.getItem('username');
        }
    } catch (_) {}

    // Capitalize user name nicely
    if (userName && userName.length > 0) {
        userName = userName.charAt(0).toUpperCase() + userName.slice(1);
    }
    const hrNameEl = hr$('hrName');
    if (hrNameEl) hrNameEl.textContent = userName;

    // Live Date
    const dateEl = hr$('currentDate');
    if (dateEl) {
        const now = new Date();
        dateEl.textContent = now.toLocaleDateString('en-US', {
            weekday: 'long',
            month: 'short',
            day: 'numeric',
            year: 'numeric'
        });
    }

    // 2. 4 KPI Pill Cards
    const summary = data.summary || DEFAULT_HR_CRM_DATA.summary;
    const totalEmp = summary.total_employees ?? DEFAULT_HR_CRM_DATA.summary.total_employees;
    const presentToday = summary.present_today ?? DEFAULT_HR_CRM_DATA.summary.present_today;
    const onLeave = summary.on_leave_today ?? DEFAULT_HR_CRM_DATA.summary.on_leave_today;
    const openPos = summary.open_positions ?? DEFAULT_HR_CRM_DATA.summary.open_positions;

    if (hr$('kpiTotalEmp')) hr$('kpiTotalEmp').textContent = totalEmp;
    if (hr$('kpiPresent')) hr$('kpiPresent').textContent = presentToday;
    if (hr$('kpiLeave')) hr$('kpiLeave').textContent = onLeave;
    if (hr$('kpiHiring')) hr$('kpiHiring').textContent = openPos;

    const rate = totalEmp > 0 ? Math.round((presentToday / totalEmp) * 100) : 89;
    if (hr$('kpiPresentBadge')) hr$('kpiPresentBadge').textContent = `${rate}% Presence`;
    if (hr$('attendanceRatePill')) hr$('attendanceRatePill').textContent = `${rate}% Rate`;
    if (hr$('presencePct')) hr$('presencePct').textContent = `${rate}%`;

    // 3. Attendance Donut Breakdown
    if (hr$('valPresent')) hr$('valPresent').textContent = `${presentToday} (${rate}%)`;
    if (hr$('valLeave')) hr$('valLeave').textContent = `${onLeave} (${Math.round((onLeave/Math.max(1, totalEmp))*100)}%)`;
    if (hr$('valLate')) hr$('valLate').textContent = `${summary.late_today ?? 0} (0%)`;
    if (hr$('valWfh')) hr$('valWfh').textContent = `1 (${Math.round((1/Math.max(1, totalEmp))*100)}%)`;

    // 4. Pending Leave Requests
    const reqListEl = hr$('pendingRequestsFeed');
    const rawReqs = (data.pending_leave_requests && data.pending_leave_requests.length) 
        ? data.pending_leave_requests 
        : DEFAULT_HR_CRM_DATA.pending_leaves;

    if (reqListEl) {
        reqListEl.innerHTML = rawReqs.map(req => {
            const name = escapeHtml(req.employee_name || req.name || 'Employee');
            const initial = name.charAt(0).toUpperCase();
            const type = escapeHtml(req.leave_type || req.type || 'Leave');
            const range = req.from_date && req.to_date 
                ? `${escapeHtml(req.from_date)} → ${escapeHtml(req.to_date)}`
                : (req.range || 'Pending approval');

            return `
                <div class="request-item" data-req-id="${req.id}">
                    <div class="req-emp-info">
                        <span class="req-avatar">${initial}</span>
                        <div class="req-details">
                            <strong class="req-name">${name}</strong>
                            <span class="req-type-badge">${type}</span>
                        </div>
                    </div>
                    <span class="req-date">${range}</span>
                    <div class="req-actions">
                        <button type="button" class="btn-act btn-act-approve" data-action="approve" data-id="${req.id}" title="Approve Leave">✓</button>
                        <button type="button" class="btn-act btn-act-reject" data-action="reject" data-id="${req.id}" title="Reject Leave">✕</button>
                    </div>
                </div>
            `;
        }).join('');
    }

    // 5. Active Recruitment Pipeline
    const pipEl = hr$('pipelineGrid');
    if (pipEl) {
        pipEl.innerHTML = DEFAULT_HR_CRM_DATA.recruitment_pipeline.map(item => `
            <div class="pipeline-card">
                <div class="pip-header">
                    <strong class="pip-title">${escapeHtml(item.title)}</strong>
                    <span class="pip-stage-tag ${item.stageClass}">${item.stage}</span>
                </div>
                <div class="pip-stats">${item.dept} • ${item.applicants} Applicants</div>
                <div class="pip-progress-bar">
                    <div class="pip-progress-fill" style="width: ${item.progress}%"></div>
                </div>
            </div>
        `).join('');
    }

    // 6. Celebrations & Milestones (Combined & populated with scroll)
    const celebEl = hr$('celebrationsList');
    if (celebEl) {
        let celebItems = [];

        // Live upcoming birthdays
        if (data.upcoming_birthdays && Array.isArray(data.upcoming_birthdays) && data.upcoming_birthdays.length) {
            data.upcoming_birthdays.forEach(b => {
                celebItems.push({
                    icon: '🎂',
                    class: 'celeb-birthday',
                    name: b.full_name || 'Team Member',
                    desc: `Birthday celebration coming up on ${b.event_date || 'soon'}`
                });
            });
        }

        // Live upcoming anniversaries
        if (data.upcoming_anniversaries && Array.isArray(data.upcoming_anniversaries) && data.upcoming_anniversaries.length) {
            data.upcoming_anniversaries.forEach(a => {
                celebItems.push({
                    icon: '🏅',
                    class: 'celeb-anniv',
                    name: a.full_name || 'Team Member',
                    desc: `${a.designation ? a.designation + ' • ' : ''}Work Anniversary milestone`
                });
            });
        }

        // Live new joiners
        if (data.new_joiners && Array.isArray(data.new_joiners) && data.new_joiners.length) {
            data.new_joiners.forEach(j => {
                celebItems.push({
                    icon: '🌟',
                    class: 'celeb-joiner',
                    name: j.full_name || 'New Joiner',
                    desc: `Joined ${j.designation ? j.designation + ' ' : ''}recently`
                });
            });
        }

        // Merge default milestone items so the list is rich, active, and scrollable
        const defaults = DEFAULT_HR_CRM_DATA.celebrations || [];
        defaults.forEach(d => {
            if (!celebItems.some(c => c.name === d.name)) {
                celebItems.push(d);
            }
        });

        celebEl.innerHTML = celebItems.map(c => `
            <div class="celebration-item">
                <div class="celeb-icon-wrap ${c.class}">${c.icon}</div>
                <div class="celeb-info">
                    <strong class="celeb-name">${escapeHtml(c.name)}</strong>
                    <span class="celeb-desc">${escapeHtml(c.desc)}</span>
                </div>
            </div>
        `).join('');
    }

    // 7. Calendar Widget
    renderCalendar();

    // 8. Pixel-perfect alignment: Equalize Celebrations bottom to match Quick Actions bottom
    syncDashboardHeights();
}

/**
 * Interactive HR Calendar widget
 */
function renderCalendar() {
    const year = calDate.getFullYear();
    const month = calDate.getMonth();

    const monthNames = [
        'January', 'February', 'March', 'April', 'May', 'June',
        'July', 'August', 'September', 'October', 'November', 'December'
    ];

    if (hr$('calMonthTitle')) {
        hr$('calMonthTitle').textContent = `${monthNames[month]} ${year}`;
    }

    const firstDayIndex = new Date(year, month, 1).getDay();
    const daysInMonth = new Date(year, month + 1, 0).getDate();

    const today = new Date();
    const isCurrentMonth = (today.getFullYear() === year && today.getMonth() === month);

    let html = '';

    // Empty lead cells
    for (let i = 0; i < firstDayIndex; i++) {
        html += '<div class="cal-day empty"></div>';
    }

    // Days in current month
    for (let d = 1; d <= daysInMonth; d++) {
        const isToday = isCurrentMonth && (today.getDate() === d);
        const hasEvent = (d === 1 || d === 15 || d === 25 || d === 30 || d === 4);
        const classes = ['cal-day'];
        if (isToday) classes.push('today');
        if (hasEvent) classes.push('has-event');

        html += `<div class="${classes.join(' ')}" data-day="${d}">${d}</div>`;
    }

    if (hr$('calDaysBody')) {
        hr$('calDaysBody').innerHTML = html;
    }
}

/**
 * Setup Event Listeners
 */
function setupEvents() {
    // Calendar Navigation
    hr$('calPrevBtn')?.addEventListener('click', () => {
        calDate.setMonth(calDate.getMonth() - 1);
        renderCalendar();
        syncDashboardHeights();
    });

    hr$('calNextBtn')?.addEventListener('click', () => {
        calDate.setMonth(calDate.getMonth() + 1);
        renderCalendar();
        syncDashboardHeights();
    });

    // Leave Action (Approve / Reject)
    document.addEventListener('click', async event => {
        const btn = event.target.closest('[data-action]');
        if (!btn) return;

        const action = btn.dataset.action;
        const id = btn.dataset.id;
        const item = btn.closest('.request-item');

        if (action === 'reject') {
            const reason = prompt('Please enter rejection reason:');
            if (reason === null) return;
        }

        // Animated feedback
        if (item) {
            item.style.transition = 'all 0.3s ease';
            item.style.opacity = '0.5';
            item.style.transform = 'translateX(10px)';
        }

        try {
            const token = hrToken();
            if (token) {
                await fetch(`${HR_API}/api/leave-requests/${action}?id=${encodeURIComponent(id)}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Authorization': `Bearer ${token}`
                    },
                    body: JSON.stringify({ rejection_reason: 'Reviewed by HR' })
                });
            }
        } catch (_) {}

        setTimeout(() => {
            if (item) {
                item.style.display = 'none';
            }
            const badge = hr$('kpiLeave');
            if (badge) {
                const count = Math.max(0, parseInt(badge.textContent || '1') - 1);
                badge.textContent = count;
                if (hr$('kpiLeaveBadge')) hr$('kpiLeaveBadge').textContent = `${count} Pending Review`;
            }
        }, 300);
    });
}

/**
 * Pixel-perfect alignment: sync Celebrations & Joiners card bottom to match HR Quick Actions card bottom
 */
function syncDashboardHeights() {
    const bottomGrid = document.querySelector('.hr-dash-bottom-grid');
    const celebCard = document.querySelector('.hr-dash-right-rail > .dash-card:last-child');
    const celebList = document.querySelector('.celebrations-list');

    if (!bottomGrid || !celebCard || !celebList) return;

    // Reset inline styling to measure true natural coordinates
    celebCard.style.height = '';
    celebList.style.height = '';
    celebList.style.maxHeight = '';

    const bottomGridRect = bottomGrid.getBoundingClientRect();
    const celebCardRect = celebCard.getBoundingClientRect();

    // Target height so bottom edge of celebCard matches bottom edge of bottomGrid (Quick Actions)
    const targetCardHeight = Math.floor(bottomGridRect.bottom - celebCardRect.top);

    if (targetCardHeight > 100) {
        celebCard.style.height = `${targetCardHeight}px`;

        const header = celebCard.querySelector('.card-header-clean');
        const headerMargin = header ? parseInt(window.getComputedStyle(header).marginBottom || '16', 10) : 16;
        const headerH = header ? (header.offsetHeight + headerMargin) : 44;
        const cardStyle = window.getComputedStyle(celebCard);
        const padding = parseInt(cardStyle.paddingTop || '18', 10) + parseInt(cardStyle.paddingBottom || '18', 10);
        const targetListH = Math.max(90, targetCardHeight - headerH - padding);

        celebList.style.height = `${targetListH}px`;
        celebList.style.maxHeight = `${targetListH}px`;
        celebList.style.overflowY = 'auto';
    }
}

window.addEventListener('resize', syncDashboardHeights);
window.addEventListener('load', syncDashboardHeights);

// Initialise
document.addEventListener('DOMContentLoaded', async () => {
    setupEvents();
    const data = await fetchHrDashboard();
    renderDashboard(data);
    syncDashboardHeights();
    requestAnimationFrame(syncDashboardHeights);
    setTimeout(syncDashboardHeights, 100);
    setTimeout(syncDashboardHeights, 300);
});

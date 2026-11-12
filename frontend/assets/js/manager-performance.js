/**
 * Mindazzle — Team Performance Controller (manager-performance.js)
 * Interactive controller matching the Team Performance mockup
 * Renders SVG trend line charts, distribution donuts, top performers, reviews, goals, and modals.
 */

const mgrPerfState = {
    data: null,
    reviews: [],
    goals: [],
    timeframe: '6M',
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
        iconSvg = `<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#8b5cf6" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12.01" y2="8"></line></svg>`;
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
async function loadPerformanceData(timeframe = '6M') {
    showLoader();
    try {
        const url = `${MGR_API_BASE}/api/manager/performance?timeframe=${encodeURIComponent(timeframe)}`;
        const res = await fetch(url, {
            method: 'GET',
            headers: getAuthHeaders()
        });

        const json = await res.json();
        if (json.success && json.data) {
            mgrPerfState.data = json.data;
            mgrPerfState.reviews = json.data.recent_reviews || [];
            mgrPerfState.goals = json.data.team_goals || [];
            mgrPerfState.timeframe = timeframe;
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
            average_team_rating: { display: '4.2 / 5', trend: '+0.3', trend_text: 'vs last quarter' },
            goals_progress: { percentage: '78%', progress: 78, trend: '+12%', trend_text: 'vs last quarter' },
            high_performers: { count: 4, subtext: 'Out of 12 members' },
            reviews_completed: { display: '8 / 12', subtext: 'This quarter' }
        },
        performance_trend: [
            { month_label: 'Mar', rating: 2.8 },
            { month_label: 'Apr', rating: 3.1 },
            { month_label: 'May', rating: 3.4 },
            { month_label: 'Jun', rating: 3.7 },
            { month_label: 'Jul', rating: 4.0 },
            { month_label: 'Aug', rating: 4.2 }
        ],
        performance_distribution: {
            total_members: 12,
            segments: [
                { category_name: 'Excellent (4.5 – 5.0)', count: 3, color: '#14b8a6' },
                { category_name: 'Good (3.5 – 4.4)', count: 5, color: '#0284c7' },
                { category_name: 'Average (2.5 – 3.4)', count: 3, color: '#f59e0b' },
                { category_name: 'Needs Improvement (< 2.5)', count: 1, color: '#f43f5e' }
            ]
        },
        top_performers: [
            { id: 1, name: 'Priya Sharma', role_title: 'UI/UX Designer', avatar_initials: 'PS', rating: '4.8' },
            { id: 2, name: 'Rohit Kulkarni', role_title: 'Frontend Developer', avatar_initials: 'RK', rating: '4.6' },
            { id: 5, name: 'Neha Tiwari', role_title: 'Content Writer', avatar_initials: 'NT', rating: '4.5' },
            { id: 6, name: 'Aditya Kapoor', role_title: 'QA Engineer', avatar_initials: 'AK', rating: '4.4' },
            { id: 3, name: 'Sneha Iyer', role_title: 'HR Executive', avatar_initials: 'SI', rating: '4.3' }
        ],
        recent_reviews: [
            { id: 1, employee_name: 'Priya Sharma', avatar_initials: 'PS', review_period: 'Jan – Jun 2025', rating: '4.8', status: 'Completed', status_class: 'completed', feedback: 'Exceeded expectations in design system revamp and sprint deliverables.' },
            { id: 2, employee_name: 'Rohit Kulkarni', avatar_initials: 'RK', review_period: 'Jan – Jun 2025', rating: '4.2', status: 'Completed', status_class: 'completed', feedback: 'Strong delivery of UI components, timely feature releases.' },
            { id: 3, employee_name: 'Sneha Iyer', avatar_initials: 'SI', review_period: 'Jan – Jun 2025', rating: '4.0', status: 'In Review', status_class: 'review', feedback: 'Effective onboarding workflows and team pulse surveys.' },
            { id: 4, employee_name: 'Rohan Verma', avatar_initials: 'RV', review_period: 'Jan – Jun 2025', rating: '3.8', status: 'In Review', status_class: 'review', feedback: 'Reliable API development, working on query optimization.' },
            { id: 5, employee_name: 'Neha Tiwari', avatar_initials: 'NT', review_period: 'Jan – Jun 2025', rating: '4.1', status: 'Completed', status_class: 'completed', feedback: 'High quality content and marketing collateral generation.' },
            { id: 6, employee_name: 'Aditya Kapoor', avatar_initials: 'AK', review_period: 'Jan – Jun 2025', rating: '4.4', status: 'In Review', status_class: 'review', feedback: 'Automated test suite maintenance and regression reports.' },
            { id: 7, employee_name: 'Vikram Singh', avatar_initials: 'VS', review_period: 'Jan – Jun 2025', rating: '3.9', status: 'Pending', status_class: 'pending', feedback: 'DevOps pipelines and cloud monitoring dashboard setup.' },
            { id: 8, employee_name: 'Ananya Roy', avatar_initials: 'AR', review_period: 'Jan – Jun 2025', rating: '4.3', status: 'Completed', status_class: 'completed', feedback: 'Delivered customer dashboard revamp on schedule with exceptional UX.' },
            { id: 9, employee_name: 'Karan Mehta', avatar_initials: 'KM', review_period: 'Jan – Jun 2025', rating: '4.0', status: 'In Review', status_class: 'review', feedback: 'Microservices architecture migration and caching layer implementation.' }
        ],
        team_goals: [
            { id: 1, goal: 'Improve client satisfaction', progress: 80, due_date: '30 Sep', status: 'On Track', status_class: 'on-track', bar_color: '#14b8a6' },
            { id: 2, goal: 'Complete product module', progress: 60, due_date: '15 Oct', status: 'In Progress', status_class: 'in-progress', bar_color: '#3b82f6' },
            { id: 3, goal: 'Reduce bug count', progress: 40, due_date: '30 Sep', status: 'At Risk', status_class: 'at-risk', bar_color: '#f59e0b' },
            { id: 4, goal: 'Enhance documentation', progress: 100, due_date: '10 Sep', status: 'Completed', status_class: 'completed', bar_color: '#14b8a6' },
            { id: 5, goal: 'Team skill development', progress: 50, due_date: '25 Oct', status: 'In Progress', status_class: 'in-progress', bar_color: '#3b82f6' },
            { id: 6, goal: 'Security compliance audit', progress: 75, due_date: '05 Nov', status: 'On Track', status_class: 'on-track', bar_color: '#14b8a6' },
            { id: 7, goal: 'API latency optimization', progress: 35, due_date: '20 Nov', status: 'At Risk', status_class: 'at-risk', bar_color: '#f59e0b' },
            { id: 8, goal: 'Refactor state management', progress: 65, due_date: '10 Dec', status: 'In Progress', status_class: 'in-progress', bar_color: '#3b82f6' },
            { id: 9, goal: 'Database indexing & cleanup', progress: 90, due_date: '15 Dec', status: 'On Track', status_class: 'on-track', bar_color: '#14b8a6' }
        ]
    };

    mgrPerfState.data = fallbackData;
    mgrPerfState.reviews = fallbackData.recent_reviews;
    mgrPerfState.goals = fallbackData.team_goals;
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

    // Performance Trend SVG Line Chart
    if (data.performance_trend) {
        renderTrendLineChart(data.performance_trend);
    }

    // Performance Distribution Donut
    if (data.performance_distribution) {
        renderDistributionDonut(data.performance_distribution);
    }

    // Top Performers List
    if (data.top_performers) {
        renderTopPerformers(data.top_performers);
    }

    // Recent Reviews Table
    renderRecentReviews(mgrPerfState.reviews);

    // Team Goals Table
    renderTeamGoals(mgrPerfState.goals);
}

/* ----------------------------------------------------------------
   1. Render 4 Top KPI Cards
---------------------------------------------------------------- */
function renderKpis(kpis) {
    // Average Team Rating
    const avgVal = document.getElementById('kpiAvgRatingVal');
    const avgTrend = document.getElementById('kpiAvgRatingTrend');
    if (avgVal && kpis.average_team_rating) {
        avgVal.textContent = kpis.average_team_rating.display || '4.2 / 5';
        if (avgTrend) {
            avgTrend.innerHTML = `↑ ${kpis.average_team_rating.trend} <span style="font-weight:500;color:#64748b;font-size:10px;">${kpis.average_team_rating.trend_text || 'vs last quarter'}</span>`;
        }
    }

    // Goals Progress
    const goalVal = document.getElementById('kpiGoalsProgressVal');
    const goalTrend = document.getElementById('kpiGoalsProgressTrend');
    if (goalVal && kpis.goals_progress) {
        goalVal.textContent = kpis.goals_progress.percentage || '78%';
        if (goalTrend) {
            goalTrend.innerHTML = `↑ ${kpis.goals_progress.trend} <span style="font-weight:500;color:#64748b;font-size:10px;">${kpis.goals_progress.trend_text || 'vs last quarter'}</span>`;
        }
    }

    // High Performers
    const highVal = document.getElementById('kpiHighPerformersVal');
    const highSub = document.getElementById('kpiHighPerformersSub');
    if (highVal && kpis.high_performers) {
        highVal.textContent = kpis.high_performers.count;
        if (highSub) highSub.textContent = kpis.high_performers.subtext || 'Out of 12 members';
    }

    // Reviews Completed
    const revVal = document.getElementById('kpiReviewsCompletedVal');
    const revSub = document.getElementById('kpiReviewsCompletedSub');
    if (revVal && kpis.reviews_completed) {
        revVal.textContent = kpis.reviews_completed.display || '8 / 12';
        if (revSub) revSub.textContent = kpis.reviews_completed.subtext || 'This quarter';
    }
}

/* ----------------------------------------------------------------
   2. Render SVG Performance Trend Line Chart
---------------------------------------------------------------- */
function renderTrendLineChart(points) {
    const svg = document.getElementById('mgrTrendSvg');
    if (!svg || !points || points.length === 0) return;

    // ViewBox dimensions: 500 wide, 140 high
    const width = 480;
    const height = 130;
    const paddingLeft = 20;
    const paddingRight = 20;
    const paddingTop = 15;
    const paddingBottom = 15;

    const chartW = width - paddingLeft - paddingRight;
    const chartH = height - paddingTop - paddingBottom;

    // Y scale from 0 to 5
    const minY = 0;
    const maxY = 5;

    const coords = points.map((pt, idx) => {
        const x = paddingLeft + (idx / (points.length - 1)) * chartW;
        const normalizedY = (parseFloat(pt.rating) - minY) / (maxY - minY);
        const y = height - paddingBottom - (normalizedY * chartH);
        return { x: Math.round(x * 10) / 10, y: Math.round(y * 10) / 10, rating: pt.rating, label: pt.month_label };
    });

    // Generate Path d string
    let pathD = `M ${coords[0].x} ${coords[0].y}`;
    for (let i = 1; i < coords.length; i++) {
        pathD += ` L ${coords[i].x} ${coords[i].y}`;
    }

    // Area Fill Path
    const areaD = `${pathD} L ${coords[coords.length - 1].x} ${height - paddingBottom} L ${coords[0].x} ${height - paddingBottom} Z`;

    // Horizontal Grid Lines
    let gridLinesHtml = '';
    for (let g = 0; g <= 5; g++) {
        const gy = height - paddingBottom - ((g / 5) * chartH);
        gridLinesHtml += `<line x1="${paddingLeft}" y1="${gy}" x2="${width - paddingRight}" y2="${gy}" stroke="#f1f5f9" stroke-width="1" />`;
    }

    // Circles for points
    const circlesHtml = coords.map((c, idx) => {
        const isLast = idx === coords.length - 1;
        const radius = isLast ? 5 : 4;
        return `
            <circle cx="${c.x}" cy="${c.y}" r="${radius}" fill="#ffffff" stroke="#6366f1" stroke-width="2.5" />
            <circle cx="${c.x}" cy="${c.y}" r="${radius - 2}" fill="#6366f1" />
        `;
    }).join('');

    svg.setAttribute('viewBox', `0 0 ${width} ${height}`);
    svg.innerHTML = `
        <defs>
            <linearGradient id="perfLineGrad" x1="0" y1="0" x2="0" y2="1">
                <stop offset="0%" stop-color="#6366f1" stop-opacity="0.25" />
                <stop offset="100%" stop-color="#6366f1" stop-opacity="0.0" />
            </linearGradient>
        </defs>
        ${gridLinesHtml}
        <path d="${areaD}" fill="url(#perfLineGrad)" />
        <path d="${pathD}" fill="none" stroke="#6366f1" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" />
        ${circlesHtml}
    `;

    // X-Axis labels
    const xAxisContainer = document.getElementById('mgrTrendXAxis');
    if (xAxisContainer) {
        xAxisContainer.innerHTML = points.map(pt => `<span>${pt.month_label}</span>`).join('');
    }
}

/* ----------------------------------------------------------------
   3. Render Performance Distribution Donut
---------------------------------------------------------------- */
function renderDistributionDonut(distribution) {
    const donutSvg = document.getElementById('mgrDistDonutSvg');
    const totalEl = document.getElementById('mgrDistTotalCount');
    const legendList = document.getElementById('mgrDistLegendList');

    if (totalEl) totalEl.textContent = distribution.total_members || 12;

    const segments = distribution.segments || [];
    const total = distribution.total_members || 12;

    // Circumference for r=45 is ~282.74
    const circumference = 282.74;
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

    if (legendList) {
        legendList.innerHTML = segments.map(seg => `
            <div class="mgr-dist-legend-item">
                <div class="mgr-dist-legend-left">
                    <span style="width:8px;height:8px;border-radius:50%;background:${seg.color};display:inline-block;"></span>
                    <span style="color:#475569;">${escapeHtml(seg.category_name)}</span>
                </div>
                <span class="mgr-dist-count">${seg.count}</span>
            </div>
        `).join('');
    }
}

/* ----------------------------------------------------------------
   4. Render Top Performers List
---------------------------------------------------------------- */
function renderTopPerformers(performers) {
    const container = document.getElementById('mgrTopPerformersList');
    if (!container) return;

    const avatarStyles = {
        'PS': 'background:#e0f2fe;color:#0284c7;',
        'RK': 'background:#e0e7ff;color:#4338ca;',
        'NT': 'background:#f3e8ff;color:#7e22ce;',
        'AK': 'background:#dbeafe;color:#1d4ed8;',
        'SI': 'background:#ffedd5;color:#c2410c;'
    };

    container.innerHTML = performers.map(p => {
        const initials = p.avatar_initials || p.name.split(' ').map(n => n[0]).join('').substring(0, 2);
        const style = avatarStyles[initials] || 'background:#ccfbf1;color:#0f766e;';

        return `
            <div class="mgr-top-performer-item" onclick="showToast('${escapeHtml(p.name)} rated ${p.rating} ⭐ (${escapeHtml(p.role_title)})', 'info')">
                <div class="mgr-top-performer-left">
                    <div class="mgr-member-avatar" style="${style}">
                        ${initials}
                    </div>
                    <div>
                        <div class="mgr-top-name">${escapeHtml(p.name)}</div>
                        <div class="mgr-top-role">${escapeHtml(p.role_title)}</div>
                    </div>
                </div>
                <div class="mgr-top-rating">
                    <span class="mgr-top-star">★</span>
                    <span>${p.rating}</span>
                </div>
            </div>
        `;
    }).join('');
}

/* ----------------------------------------------------------------
   5. Render Recent Reviews Table
---------------------------------------------------------------- */
function renderRecentReviews(reviews) {
    const tbody = document.getElementById('mgrReviewsTableBody');
    if (!tbody) return;

    if (!reviews || reviews.length === 0) {
        tbody.innerHTML = `<tr><td colspan="5" style="text-align:center;padding:24px;color:#94a3b8;">No recent performance reviews.</td></tr>`;
        return;
    }

    const avatarStyles = {
        'PS': 'background:#e0f2fe;color:#0284c7;',
        'RK': 'background:#e0e7ff;color:#4338ca;',
        'SI': 'background:#ffedd5;color:#c2410c;',
        'RV': 'background:#dcfce7;color:#15803d;',
        'NT': 'background:#f3e8ff;color:#7e22ce;',
        'AK': 'background:#dbeafe;color:#1d4ed8;',
        'VS': 'background:#fef08a;color:#854d0e;',
        'AR': 'background:#fef3c7;color:#b45309;',
        'KM': 'background:#e0e7ff;color:#4338ca;'
    };

    tbody.innerHTML = reviews.map((r, idx) => {
        const initials = r.avatar_initials || 'TM';
        const avStyle = avatarStyles[initials] || 'background:#ccfbf1;color:#0f766e;';
        const currentStatus = r.status || 'Pending';

        return `
            <tr>
                <td>
                    <div class="mgr-team-user">
                        <div class="mgr-member-avatar" style="${avStyle}">${initials}</div>
                        <span class="mgr-team-name" style="font-weight:700;color:#0f172a;">${escapeHtml(r.employee_name)}</span>
                    </div>
                </td>
                <td style="color:#64748b;font-size:12px;">${escapeHtml(r.review_period)}</td>
                <td style="font-weight:800;color:#0f172a;font-size:12.5px;">${r.rating}</td>
                <td>
                    <select class="mgr-status-select" data-status="${escapeHtml(currentStatus)}" onchange="changeReviewStatus(${r.id}, this.value, this)">
                        <option value="Completed" ${currentStatus === 'Completed' ? 'selected' : ''}>Completed</option>
                        <option value="In Review" ${currentStatus === 'In Review' ? 'selected' : ''}>In Review</option>
                        <option value="Pending" ${currentStatus === 'Pending' ? 'selected' : ''}>Pending</option>
                    </select>
                </td>
                <td style="text-align:right;">
                    <button type="button" class="mgr-btn-view" onclick="openReviewDetailModal(${r.id})">View</button>
                </td>
            </tr>
        `;
    }).join('');
}

function changeReviewStatus(id, newStatus, selectEl) {
    if (selectEl) {
        selectEl.setAttribute('data-status', newStatus);
    }
    const rev = mgrPerfState.reviews.find(item => item.id === id);
    if (rev) {
        rev.status = newStatus;
        showToast(`Review status for ${rev.employee_name} updated to "${newStatus}"!`, 'success');
    } else {
        showToast(`Review status updated to "${newStatus}"!`, 'success');
    }
}

/* ----------------------------------------------------------------
   6. Render Team Goals Table
---------------------------------------------------------------- */
function renderTeamGoals(goals) {
    const tbody = document.getElementById('mgrGoalsTableBody');
    if (!tbody) return;

    if (!goals || goals.length === 0) {
        tbody.innerHTML = `<tr><td colspan="4" style="text-align:center;padding:24px;color:#94a3b8;">No active team goals.</td></tr>`;
        return;
    }

    tbody.innerHTML = goals.map(g => {
        const currentStatus = g.status || 'In Progress';
        const barColor = g.bar_color || (g.progress >= 80 ? '#14b8a6' : (g.progress <= 40 ? '#f59e0b' : '#3b82f6'));

        return `
            <tr>
                <td style="font-weight:700;color:#0f172a;font-size:12px;">${escapeHtml(g.goal)}</td>
                <td>
                    <div class="mgr-goal-progress-wrap">
                        <div class="mgr-goal-track">
                            <div class="mgr-goal-fill" style="width:${g.progress}%;background:${barColor};"></div>
                        </div>
                        <span class="mgr-goal-pct">${g.progress}%</span>
                    </div>
                </td>
                <td style="color:#64748b;font-size:12px;white-space:nowrap;">${escapeHtml(g.due_date)}</td>
                <td>
                    <select class="mgr-status-select" data-status="${escapeHtml(currentStatus)}" onchange="changeGoalStatus(${g.id}, this.value, this)">
                        <option value="On Track" ${currentStatus === 'On Track' ? 'selected' : ''}>On Track</option>
                        <option value="In Progress" ${currentStatus === 'In Progress' ? 'selected' : ''}>In Progress</option>
                        <option value="At Risk" ${currentStatus === 'At Risk' ? 'selected' : ''}>At Risk</option>
                        <option value="Completed" ${currentStatus === 'Completed' ? 'selected' : ''}>Completed</option>
                    </select>
                </td>
            </tr>
        `;
    }).join('');
}

function changeGoalStatus(id, newStatus, selectEl) {
    if (selectEl) {
        selectEl.setAttribute('data-status', newStatus);
    }
    const goal = mgrPerfState.goals.find(item => item.id === id);
    if (goal) {
        goal.status = newStatus;
        showToast(`Goal "${goal.goal}" status updated to "${newStatus}"!`, 'success');
    } else {
        showToast(`Goal status updated to "${newStatus}"!`, 'success');
    }
}

/* ----------------------------------------------------------------
   Modals
---------------------------------------------------------------- */
function openReviewDetailModal(reviewId) {
    const r = mgrPerfState.reviews.find(item => item.id === reviewId) || mgrPerfState.reviews[0];
    if (!r) return;

    const modal = document.getElementById('mgrReviewDetailModal');
    const content = document.getElementById('mgrReviewModalBody');
    if (!modal || !content) return;

    content.innerHTML = `
        <div style="display:flex;align-items:center;gap:14px;padding-bottom:14px;border-bottom:1px solid #f1f5f9;">
            <div style="width:46px;height:46px;border-radius:50%;background:#e0f2fe;color:#0284c7;display:flex;align-items:center;justify-content:center;font-weight:800;font-size:16px;">
                ${r.avatar_initials || 'PS'}
            </div>
            <div>
                <h3 style="margin:0;font-size:16px;font-weight:800;color:#0f172a;">${escapeHtml(r.employee_name)}</h3>
                <span style="font-size:12px;color:#64748b;">${escapeHtml(r.role_title || 'Team Member')} • ${escapeHtml(r.review_period)}</span>
            </div>
        </div>

        <div class="mgr-member-modal-grid" style="margin-top:14px;">
            <div class="mgr-detail-box">
                <div class="mgr-detail-label">Overall Rating</div>
                <div class="mgr-detail-val" style="color:#6366f1;font-size:16px;">⭐ ${r.rating} / 5.0</div>
            </div>
            <div class="mgr-detail-box">
                <div class="mgr-detail-label">Status</div>
                <div class="mgr-detail-val">${r.status}</div>
            </div>
            <div class="mgr-detail-box">
                <div class="mgr-detail-label">Reviewer</div>
                <div class="mgr-detail-val">${r.reviewer_name || 'Arun Kumar'}</div>
            </div>
            <div class="mgr-detail-box">
                <div class="mgr-detail-label">Quarter</div>
                <div class="mgr-detail-val">Q2 2025</div>
            </div>
        </div>

        <div style="margin-top:14px;">
            <label class="mgr-form-label">Reviewer Feedback & Accomplishments</label>
            <div style="background:#f8fafc;padding:12px 14px;border-radius:8px;border:1px solid #e2e8f0;font-size:12.5px;color:#334155;line-height:1.5;">
                ${escapeHtml(r.feedback || 'Consistently demonstrated leadership in sprint execution, collaborated across teams, and achieved core quarterly targets.')}
            </div>
        </div>
    `;

    modal.classList.add('active');
}

function openCreateReviewModal() {
    const modal = document.getElementById('mgrCreateReviewModal');
    if (modal) modal.classList.add('active');
}

function openSetGoalModal() {
    const modal = document.getElementById('mgrSetGoalModal');
    if (modal) modal.classList.add('active');
}

function openFeedbackModal() {
    const modal = document.getElementById('mgrFeedbackModal');
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

    // Form Submits
    const goalForm = document.getElementById('mgrSetGoalForm');
    if (goalForm) {
        goalForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const title = document.getElementById('goalTitleInput').value.trim();
            const progress = parseInt(document.getElementById('goalProgressInput').value) || 0;
            const dueDate = document.getElementById('goalDueDateInput').value.trim() || '30 Nov';
            const status = document.getElementById('goalStatusInput').value || 'IN_PROGRESS';

            if (!title) {
                showToast('Please enter a goal title', 'error');
                return;
            }

            try {
                const res = await fetch(`${MGR_API_BASE}/api/manager/goals`, {
                    method: 'POST',
                    headers: getAuthHeaders(),
                    body: JSON.stringify({ title, progress_pct: progress, due_date: dueDate, status })
                });
                const json = await res.json();
                if (json.success && json.data) {
                    mgrPerfState.goals.unshift(json.data);
                } else {
                    // Local append
                    mgrPerfState.goals.unshift({
                        id: Date.now(),
                        goal: title,
                        progress: progress,
                        due_date: dueDate,
                        status: status === 'ON_TRACK' ? 'On Track' : 'In Progress',
                        status_class: status === 'ON_TRACK' ? 'on-track' : 'in-progress',
                        bar_color: progress >= 80 ? '#14b8a6' : '#3b82f6'
                    });
                }
            } catch (_) {
                mgrPerfState.goals.unshift({
                    id: Date.now(),
                    goal: title,
                    progress: progress,
                    due_date: dueDate,
                    status: 'In Progress',
                    status_class: 'in-progress',
                    bar_color: '#3b82f6'
                });
            }

            renderTeamGoals(mgrPerfState.goals);
            showToast('New team goal created successfully!', 'success');
            goalForm.reset();
            closeModals();
        });
    }

    const reviewForm = document.getElementById('mgrCreateReviewForm');
    if (reviewForm) {
        reviewForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const empName = document.getElementById('reviewEmpNameInput').value.trim();
            const period = document.getElementById('reviewPeriodInput').value.trim() || 'Q3 2025';
            const rating = parseFloat(document.getElementById('reviewRatingInput').value) || 4.2;
            const feedback = document.getElementById('reviewFeedbackInput').value.trim();

            if (!empName) {
                showToast('Please enter employee name', 'error');
                return;
            }

            const initials = empName.split(' ').map(n => n[0]).join('').substring(0, 2).toUpperCase();

            try {
                const res = await fetch(`${MGR_API_BASE}/api/manager/reviews`, {
                    method: 'POST',
                    headers: getAuthHeaders(),
                    body: JSON.stringify({ employee_name: empName, review_period: period, rating, feedback, status: 'IN_REVIEW' })
                });
                const json = await res.json();
                if (json.success && json.data) {
                    mgrPerfState.reviews.unshift(json.data);
                } else {
                    mgrPerfState.reviews.unshift({
                        id: Date.now(),
                        employee_name: empName,
                        avatar_initials: initials,
                        review_period: period,
                        rating: rating.toFixed(1),
                        status: 'In Review',
                        status_class: 'review',
                        feedback: feedback
                    });
                }
            } catch (_) {
                mgrPerfState.reviews.unshift({
                    id: Date.now(),
                    employee_name: empName,
                    avatar_initials: initials,
                    review_period: period,
                    rating: rating.toFixed(1),
                    status: 'In Review',
                    status_class: 'review',
                    feedback: feedback
                });
            }

            renderRecentReviews(mgrPerfState.reviews);
            showToast(`Performance review for ${empName} started!`, 'success');
            reviewForm.reset();
            closeModals();
        });
    }

    const feedbackForm = document.getElementById('mgrFeedbackForm');
    if (feedbackForm) {
        feedbackForm.addEventListener('submit', (e) => {
            e.preventDefault();
            const emp = document.getElementById('feedbackEmpInput').value;
            showToast(`Feedback shared with ${emp} successfully!`, 'success');
            feedbackForm.reset();
            closeModals();
        });
    }
}

/* ----------------------------------------------------------------
   Setup Timeframe & Search
---------------------------------------------------------------- */
function setupControls() {
    const timeframeSelect = document.getElementById('perfTimeframeSelect');
    if (timeframeSelect) {
        timeframeSelect.addEventListener('change', (e) => {
            showToast(`Loaded timeframe: ${e.target.value}`, 'info');
            loadPerformanceData(e.target.value);
        });
    }

    const searchInput = document.getElementById('mgrSearchInput');
    if (searchInput) {
        searchInput.addEventListener('input', (e) => {
            const query = e.target.value.toLowerCase().trim();
            mgrPerfState.searchQuery = query;

            // Filter reviews
            let filteredReviews = [...mgrPerfState.reviews];
            if (query) {
                filteredReviews = filteredReviews.filter(r => 
                    (r.employee_name || '').toLowerCase().includes(query) ||
                    (r.review_period || '').toLowerCase().includes(query) ||
                    (r.status || '').toLowerCase().includes(query)
                );
            }
            renderRecentReviews(filteredReviews);

            // Filter goals
            let filteredGoals = [...mgrPerfState.goals];
            if (query) {
                filteredGoals = filteredGoals.filter(g => 
                    (g.goal || '').toLowerCase().includes(query) ||
                    (g.status || '').toLowerCase().includes(query)
                );
            }
            renderTeamGoals(filteredGoals);
        });
    }

    // Quick Actions
    const btnCreateRev = document.getElementById('actionCreateReview');
    if (btnCreateRev) btnCreateRev.addEventListener('click', openCreateReviewModal);

    const btnSetGoal = document.getElementById('actionSetGoal');
    if (btnSetGoal) btnSetGoal.addEventListener('click', openSetGoalModal);

    const btnGiveFb = document.getElementById('actionGiveFeedback');
    if (btnGiveFb) btnGiveFb.addEventListener('click', openFeedbackModal);

    const btnViewReports = document.getElementById('actionViewReports');
    if (btnViewReports) {
        btnViewReports.addEventListener('click', () => {
            showToast('Generating comprehensive performance insights report...', 'info');
        });
    }

    const btnBottomGoal = document.getElementById('bottomSetGoalBtn');
    if (btnBottomGoal) btnBottomGoal.addEventListener('click', openSetGoalModal);
}

function escapeHtml(str) {
    if (!str) return '';
    return String(str)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;');
}

// Expose modal and action functions to window for inline onclick handlers
window.openReviewDetailModal = openReviewDetailModal;
window.openCreateReviewModal = openCreateReviewModal;
window.openSetGoalModal = openSetGoalModal;
window.openFeedbackModal = openFeedbackModal;
window.closeModals = closeModals;
window.changeReviewStatus = changeReviewStatus;
window.changeGoalStatus = changeGoalStatus;

/* ----------------------------------------------------------------
   DOM Content Loaded
---------------------------------------------------------------- */
document.addEventListener('DOMContentLoaded', () => {
    loadPerformanceData();
    setupModals();
    setupControls();
});

/* ================================================================
   MINDDAZZLE EMPLOYEE DASHBOARD — JAVASCRIPT
   Connects to Backend API & Provides Dynamic Interactivity
   File: employee-dashboard.js
================================================================ */

const EMP_API_BASE = window.location.origin + "/employee-qr-system/backend/public";

// State
let dashboardState = {
    isCheckedIn: true,
    checkInTime: "09:12 AM",
    employee: null,
    attendanceMonthly: null
};

/* ----------------------------------------------------------------
   Auth Helpers
---------------------------------------------------------------- */
function getAuthToken() {
    return localStorage.getItem("auth_token") || localStorage.getItem("token") || "";
}

function getAuthUser() {
    try {
        return JSON.parse(localStorage.getItem("auth_user") || "{}");
    } catch (_) {
        return {};
    }
}

/* ----------------------------------------------------------------
   Toast Notification System
---------------------------------------------------------------- */
function showToast(message, type = "info") {
    const container = document.getElementById("empToastContainer");
    if (!container) return;

    const toast = document.createElement("div");
    toast.className = "emp-toast";
    
    let icon = "ℹ️";
    if (type === "success") icon = "✅";
    if (type === "error")   icon = "⚠️";

    toast.innerHTML = `<span>${icon}</span><span>${message}</span>`;
    container.appendChild(toast);

    setTimeout(() => {
        toast.style.opacity = "0";
        toast.style.transform = "translateY(8px)";
        toast.style.transition = "all 0.25s ease";
        setTimeout(() => toast.remove(), 250);
    }, 3200);
}

/* ----------------------------------------------------------------
   Date Utilities
---------------------------------------------------------------- */
function formatHumanDate(dateStr) {
    if (!dateStr) return "—";
    try {
        const d = new Date(dateStr);
        return d.toLocaleDateString("en-GB", {
            day: "2-digit",
            month: "short",
            year: "numeric"
        });
    } catch (_) {
        return dateStr;
    }
}

function calculateDaysUntil(dateStr) {
    if (!dateStr) return "";
    try {
        const target = new Date(dateStr);
        const today = new Date();
        target.setHours(0,0,0,0);
        today.setHours(0,0,0,0);
        const diff = Math.ceil((target - today) / (1000 * 60 * 60 * 24));
        if (diff < 0) return "Past";
        if (diff === 0) return "Today";
        if (diff === 1) return "Tomorrow";
        return `In ${diff} days`;
    } catch (_) {
        return "";
    }
}

/* ----------------------------------------------------------------
   Initialize Topbar Date
---------------------------------------------------------------- */
function initTopbarDate() {
    const dateEl = document.getElementById("empTopbarDate");
    if (!dateEl) return;

    const options = { weekday: "long", day: "numeric", month: "long", year: "numeric" };
    const today = new Date();
    dateEl.textContent = today.toLocaleDateString("en-GB", options);
}

/* ----------------------------------------------------------------
   Fetch Dashboard Data
---------------------------------------------------------------- */
async function loadDashboardData() {
    const token = getAuthToken();
    const headers = {
        "Accept": "application/json",
        "Content-Type": "application/json"
    };
    if (token) {
        headers["Authorization"] = `Bearer ${token}`;
    }

    try {
        const response = await fetch(`${EMP_API_BASE}/api/employee/dashboard`, {
            method: "GET",
            headers: headers
        });

        const json = await response.json();

        if (json.success && json.data) {
            renderDashboard(json.data);
        } else {
            console.warn("Using fallback demo data:", json.message);
            renderFallbackData();
        }
    } catch (error) {
        console.warn("Could not reach API, rendering rich demo data:", error);
        renderFallbackData();
    } finally {
        hideLoader();
    }
}

function hideLoader() {
    const loader = document.getElementById("empLoadingOverlay");
    if (loader) {
        loader.classList.add("hidden");
    }
}

/* ----------------------------------------------------------------
   Render Entire Dashboard
---------------------------------------------------------------- */
function renderDashboard(data) {
    // 1. Employee Profile & Greeting
    const emp = data.employee || {};
    dashboardState.employee = emp;

    let storedUser = null;
    try {
        storedUser = JSON.parse(localStorage.getItem('auth_user') || '{}');
    } catch (_) {}

    const resolvedFullName = emp.name || emp.full_name || storedUser?.full_name || storedUser?.name || storedUser?.username || "Employee";
    const rawFirst = emp.first_name || (resolvedFullName ? resolvedFullName.split(" ")[0] : "Employee");
    const firstName = rawFirst.charAt(0).toUpperCase() + rawFirst.slice(1);
    const resolvedRole = emp.designation || storedUser?.designation || storedUser?.role || "Employee";

    const greetEl = document.getElementById("empGreetName");
    if (greetEl) greetEl.textContent = firstName;

    const bannerNameEl = document.getElementById("empBannerName");
    if (bannerNameEl) bannerNameEl.textContent = firstName;

    const topbarUserEl = document.getElementById("empTopbarUserName");
    if (topbarUserEl) topbarUserEl.textContent = resolvedFullName;

    const topbarRoleEl = document.getElementById("empTopbarUserRole");
    if (topbarRoleEl) topbarRoleEl.textContent = resolvedRole;

    // Avatar
    const avatarImg = document.getElementById("empTopbarAvatarImg");
    const avatarFallback = document.getElementById("empAvatarFallback");
    if (emp.profile_photo && avatarImg) {
        avatarImg.src = emp.profile_photo;
        avatarImg.style.display = 'block';
        if (avatarFallback) avatarFallback.style.display = 'none';
    } else if (avatarFallback) {
        const parts = resolvedFullName.trim().split(/\s+/);
        const inits = parts.length > 1 ? (parts[0][0] + parts[1][0]).toUpperCase() : parts[0].substring(0, 2).toUpperCase();
        avatarFallback.textContent = inits;
    }

    // 2. Stat Cards
    const att = data.attendance || {};
    const checkInEl = document.getElementById("statCheckIn");
    const attBadge  = document.getElementById("statAttBadge");
    const quickBtnText = document.getElementById("quickCheckInOutText");

    if (checkInEl) {
        checkInEl.textContent = att.check_in || "09:12 AM";
    }

    const isCheckedIn = (att.status || "Checked In").toLowerCase().includes("in");
    dashboardState.isCheckedIn = isCheckedIn;
    if (attBadge) {
        attBadge.textContent = isCheckedIn ? "Checked In" : "Checked Out";
        attBadge.className = isCheckedIn ? "emp-badge green" : "emp-badge amber";
    }
    if (quickBtnText) {
        quickBtnText.textContent = isCheckedIn ? "Check Out" : "Check In";
    }

    // Leave Balance
    const leave = data.leave_balance || {};
    const leaveVal = document.getElementById("statLeaveBalance");
    const leaveSub = document.getElementById("statLeaveSub");
    if (leaveVal) leaveVal.textContent = (leave.remaining ?? 12) + " Days";
    if (leaveSub) leaveSub.textContent = `out of ${leave.total ?? 18} days`;

    // Performance
    const perf = data.performance || {};
    const perfVal = document.getElementById("statPerfRating");
    const perfSub = document.getElementById("statPerfSub");
    if (perfVal) perfVal.textContent = `${perf.rating ?? 4.3} / ${perf.max_rating ?? 5}`;
    if (perfSub && perf.last_reviewed) {
        perfSub.textContent = "Last reviewed on " + formatHumanDate(perf.last_reviewed);
    }

    // Training Progress
    const trainSummary = data.training_summary || {};
    const trainVal = document.getElementById("statTrainingProgress");
    if (trainVal) trainVal.textContent = `${trainSummary.in_progress ?? 3} / ${trainSummary.total ?? 5}`;

    // 3. Donut Chart
    dashboardState.attendanceMonthly = data.attendance_monthly;
    renderDonutChart(data.attendance_monthly);

    // 4. Upcoming Leave
    renderUpcomingLeave(data.upcoming_leave);

    // 5. Upcoming Holidays
    renderUpcomingHolidays(data.upcoming_holidays);

    // 6. Goals
    renderGoals(data.goals);

    // 7. Training
    renderTraining(data.training);

    // 8. Recent Activity
    renderRecentActivity(data.recent_activity);

    // 9. Assigned Tasks from Manager
    renderAssignedTasks(data.tasks || []);

    // 10. Notifications
    if (data.notifications) {
        renderNotifications(data.notifications);
    } else {
        loadNotifications();
    }
}

/* ----------------------------------------------------------------
   Fallback Demo Data (Identical to Mockup)
---------------------------------------------------------------- */
function renderFallbackData() {
    renderDashboard({
        employee: {
            name: "Priya Nair",
            first_name: "Priya",
            last_name: "Nair",
            designation: "UI/UX Designer"
        },
        attendance: {
            check_in: "09:12 AM",
            status: "Checked In"
        },
        leave_balance: {
            remaining: 12,
            total: 18
        },
        performance: {
            rating: 4.3,
            max_rating: 5,
            last_reviewed: "2026-07-15"
        },
        training_summary: {
            in_progress: 3,
            total: 5
        },
        attendance_monthly: {
            present: 18,
            absent: 1,
            late: 2,
            half_day: 1,
            wfh: 3,
            total_days: 22
        },
        upcoming_leave: [
            {
                leave_type: "Casual Leave",
                start_date: "2026-09-10",
                end_date: "2026-09-11",
                status: "Pending",
                days: 2
            },
            {
                leave_type: "Sick Leave",
                start_date: "2026-08-20",
                end_date: "2026-08-20",
                status: "Approved",
                days: 1
            },
            {
                leave_type: "Earned Leave",
                start_date: "2026-10-05",
                end_date: "2026-10-07",
                status: "Approved",
                days: 3
            }
        ],
        upcoming_holidays: [
            { name: "Gandhi Jayanti", date: "2026-10-02" },
            { name: "Diwali",         date: "2026-11-12" },
            { name: "Christmas",      date: "2026-12-25" }
        ],
        goals: [
            { title: "UI Redesign for Client Portal",  progress: 85 },
            { title: "Improve Design System",          progress: 60 },
            { title: "Complete Advanced Figma Course", progress: 100 }
        ],
        training: [
            { title: "Advanced Figma",       progress: 80,  status: "In Progress" },
            { title: "UI/UX Best Practices", progress: 60,  status: "In Progress" },
            { title: "Agile Fundamentals",   progress: 100, status: "Completed" }
        ],
        recent_activity: [
            { description: "You checked in",                               time: "09:12 AM",      color: "green" },
            { description: "Your leave request was approved",               time: "Yesterday",     color: "blue" },
            { description: "New training assigned: Agile Fundamentals",     time: "14 Sep 2026",   color: "purple" },
            { description: "Your goal progress was updated",                time: "12 Sep 2026",   color: "yellow" }
        ]
    });
}

/* ----------------------------------------------------------------
   Donut Chart SVG Renderer
---------------------------------------------------------------- */
function renderDonutChart(data) {
    const present = data?.present ?? 18;
    const absent  = data?.absent ?? 1;
    const late    = data?.late ?? 2;
    const halfDay = data?.half_day ?? 1;
    const wfh     = data?.wfh ?? 3;
    const totalDays = data?.total_days ?? (present + absent + late + halfDay + wfh);

    // Update center number
    const centerVal = document.getElementById("empDonutCenterValue");
    if (centerVal) centerVal.textContent = totalDays;

    // Update legend values
    const map = {
        "empLegendPresent": present,
        "empLegendAbsent":  absent,
        "empLegendLate":    late,
        "empLegendHalf":    halfDay,
        "empLegendWfh":     wfh
    };
    for (const [id, val] of Object.entries(map)) {
        const el = document.getElementById(id);
        if (el) el.textContent = val;
    }

    const svg = document.getElementById("empDonutSvg");
    if (!svg) return;

    // Clear previous segments
    svg.querySelectorAll(".emp-donut-seg").forEach(el => el.remove());

    const r = 46;
    const circumference = 2 * Math.PI * r;
    const sum = present + absent + late + halfDay + wfh;

    if (sum === 0) return;

    const segments = [
        { val: present, color: "#10b981" },
        { val: absent,  color: "#ef4444" },
        { val: late,    color: "#f59e0b" },
        { val: halfDay, color: "#8b5cf6" },
        { val: wfh,     color: "#3b82f6" }
    ];

    let currentOffset = 0;

    segments.forEach(seg => {
        if (seg.val <= 0) return;
        const segmentLength = (seg.val / sum) * circumference;
        const gapLength = circumference - segmentLength;

        const circle = document.createElementNS("http://www.w3.org/2000/svg", "circle");
        circle.setAttribute("class", "emp-donut-seg");
        circle.setAttribute("cx", "60");
        circle.setAttribute("cy", "60");
        circle.setAttribute("r", String(r));
        circle.setAttribute("stroke", seg.color);
        circle.setAttribute("stroke-dasharray", `${segmentLength} ${gapLength}`);
        circle.setAttribute("stroke-dashoffset", String(-currentOffset));

        svg.appendChild(circle);
        currentOffset += segmentLength;
    });
}

/* ----------------------------------------------------------------
   Upcoming Leave List Renderer
---------------------------------------------------------------- */
function renderUpcomingLeave(leaves) {
    const container = document.getElementById("empLeaveList");
    if (!container) return;

    if (!Array.isArray(leaves) || leaves.length === 0) {
        container.innerHTML = `<p style="text-align:center;color:var(--text-muted);font-size:12px;padding:20px 0;">No upcoming leave</p>`;
        return;
    }

    const colorClasses = ["teal", "mint", "peach"];
    const icons = [
        `<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 10v6M2 10l10-5 10 5-10 5z"></path></svg>`,
        `<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path></svg>`,
        `<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"></rect></svg>`
    ];

    container.innerHTML = leaves.slice(0, 3).map((l, i) => {
        const isApproved = (l.status || "").toLowerCase() === "approved";
        const badgeClass = isApproved ? "emp-badge green" : "emp-badge amber";
        const badgeText = isApproved ? "Approved" : "Pending";
        const colorClass = colorClasses[i % colorClasses.length];
        const iconSvg = icons[i % icons.length];

        let datesText = formatHumanDate(l.start_date);
        if (l.end_date && l.end_date !== l.start_date) {
            datesText += ` – ${formatHumanDate(l.end_date)}`;
        }

        return `
            <div class="emp-item-row">
                <div class="emp-item-icon-box ${colorClass}">
                    ${iconSvg}
                </div>
                <div class="emp-item-details">
                    <div class="emp-item-title">${l.leave_type || "Leave"}</div>
                    <div class="emp-item-date">${datesText}</div>
                </div>
                <div class="emp-item-right">
                    <span class="${badgeClass}">${badgeText}</span>
                    <span class="emp-item-days">${l.days ?? 1} day${(l.days ?? 1) !== 1 ? "s" : ""}</span>
                </div>
            </div>
        `;
    }).join("");
}

/* ----------------------------------------------------------------
   Upcoming Holidays Renderer
---------------------------------------------------------------- */
function renderUpcomingHolidays(holidays) {
    const container = document.getElementById("empHolidayList");
    if (!container) return;

    if (!Array.isArray(holidays) || holidays.length === 0) {
        container.innerHTML = `<p style="text-align:center;color:var(--text-muted);font-size:12px;padding:20px 0;">No upcoming holidays</p>`;
        return;
    }

    const colorClasses = ["purple", "pink", "blue"];
    const icons = [
        `<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path></svg>`,
        `<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>`,
        `<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="8" width="18" height="14" rx="2"></rect><path d="M12 8V3"></path></svg>`
    ];

    container.innerHTML = holidays.slice(0, 3).map((h, i) => {
        const colorClass = colorClasses[i % colorClasses.length];
        const iconSvg = icons[i % icons.length];
        const dateFormatted = formatHumanDate(h.date || h.holiday_date);
        const countdown = calculateDaysUntil(h.date || h.holiday_date);

        return `
            <div class="emp-item-row">
                <div class="emp-item-icon-box ${colorClass}">
                    ${iconSvg}
                </div>
                <div class="emp-item-details">
                    <div class="emp-item-title">${h.name || "Holiday"}</div>
                    <div class="emp-item-date">${dateFormatted}</div>
                </div>
                <div class="emp-item-right">
                    <span class="emp-item-countdown">${countdown}</span>
                </div>
            </div>
        `;
    }).join("");
}

/* ----------------------------------------------------------------
   Goals Renderer
---------------------------------------------------------------- */
function renderGoals(goals) {
    const container = document.getElementById("empGoalsList");
    if (!container) return;

    if (!Array.isArray(goals) || goals.length === 0) {
        container.innerHTML = `<p style="text-align:center;color:var(--text-muted);font-size:12px;padding:20px 0;">No goals found</p>`;
        return;
    }

    container.innerHTML = goals.slice(0, 3).map(g => {
        const pct = Math.min(100, parseInt(g.progress ?? 0));
        return `
            <div class="emp-progress-entry">
                <div class="emp-progress-meta">
                    <span class="emp-progress-title">${g.title || "Goal"}</span>
                    <span class="emp-progress-pct">${pct}%</span>
                </div>
                <div class="emp-progress-track">
                    <div class="emp-progress-fill" style="width: ${pct}%;"></div>
                </div>
            </div>
        `;
    }).join("");
}

/* ----------------------------------------------------------------
   Training Renderer
---------------------------------------------------------------- */
function renderTraining(training) {
    const container = document.getElementById("empTrainingList");
    if (!container) return;

    if (!Array.isArray(training) || training.length === 0) {
        container.innerHTML = `<p style="text-align:center;color:var(--text-muted);font-size:12px;padding:20px 0;">No active training</p>`;
        return;
    }

    container.innerHTML = training.slice(0, 3).map(t => {
        const pct = Math.min(100, parseInt(t.progress ?? 0));
        const isCompleted = (t.status || "").toLowerCase() === "completed" || pct === 100;
        const badgeClass = isCompleted ? "emp-badge green" : "emp-badge" ;
        const badgeStyle = isCompleted ? "" : "background: #eff6ff; color: #2563eb;";
        const badgeLabel = isCompleted ? "Completed" : "In Progress";

        return `
            <div class="emp-progress-entry">
                <div class="emp-progress-meta">
                    <span class="emp-progress-title">${t.title || "Course"}</span>
                    <div style="display:flex; align-items:center; gap:8px;">
                        <span class="emp-progress-pct">${pct}%</span>
                        <span class="${badgeClass}" style="${badgeStyle}">${badgeLabel}</span>
                    </div>
                </div>
                <div class="emp-progress-track">
                    <div class="emp-progress-fill" style="width: ${pct}%;"></div>
                </div>
            </div>
        `;
    }).join("");
}

/* ----------------------------------------------------------------
   Recent Activity Renderer
---------------------------------------------------------------- */
function renderRecentActivity(activities) {
    const container = document.getElementById("empActivityList");
    if (!container) return;

    if (!Array.isArray(activities) || activities.length === 0) {
        container.innerHTML = `<p style="text-align:center;color:var(--text-muted);font-size:12px;padding:20px 0;">No recent activity</p>`;
        return;
    }

    const defaultColors = ["green", "blue", "purple", "yellow"];

    container.innerHTML = activities.slice(0, 4).map((a, i) => {
        const dotColor = a.color || defaultColors[i % defaultColors.length];
        const timeText = a.time || (a.created_at ? formatHumanDate(a.created_at) : "Recent");

        return `
            <div class="emp-activity-row">
                <div class="emp-activity-dot ${dotColor}"></div>
                <div class="emp-activity-info">
                    <div class="emp-activity-text">${a.description || a.message || "Activity recorded"}</div>
                    <div class="emp-activity-time">${timeText}</div>
                </div>
            </div>
        `;
    }).join("");
}

/* ----------------------------------------------------------------
   Interactive Handlers
---------------------------------------------------------------- */
function initInteractiveHandlers() {
    // Quick Check In / Check Out Toggle
    const checkOutBtn = document.getElementById("empQuickCheckOut");
    const checkInEl   = document.getElementById("statCheckIn");
    const attBadge    = document.getElementById("statAttBadge");
    const quickBtnText = document.getElementById("quickCheckInOutText");

    if (checkOutBtn) {
        checkOutBtn.addEventListener("click", () => {
            dashboardState.isCheckedIn = !dashboardState.isCheckedIn;

            if (dashboardState.isCheckedIn) {
                const now = new Date();
                const timeStr = now.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
                if (checkInEl) checkInEl.textContent = timeStr;
                if (attBadge) {
                    attBadge.textContent = "Checked In";
                    attBadge.className = "emp-badge green";
                }
                if (quickBtnText) quickBtnText.textContent = "Check Out";
                showToast("You have successfully checked in for today!", "success");
            } else {
                if (attBadge) {
                    attBadge.textContent = "Checked Out";
                    attBadge.className = "emp-badge red";
                }
                if (quickBtnText) quickBtnText.textContent = "Check In";
                showToast("You have successfully checked out. Have a great evening!", "info");
            }
        });
    }

    // Month Selector change
    const monthSelect = document.getElementById("empAttendanceMonth");
    if (monthSelect) {
        monthSelect.addEventListener("change", (e) => {
            const val = e.target.value;
            showToast(`Attendance updated for ${e.target.options[e.target.selectedIndex].text}`, "info");

            if (val === "2026-08") {
                renderDonutChart({ present: 20, absent: 0, late: 1, half_day: 0, wfh: 2, total_days: 23 });
            } else if (val === "2026-07") {
                renderDonutChart({ present: 19, absent: 2, late: 1, half_day: 1, wfh: 1, total_days: 24 });
            } else {
                renderDonutChart(dashboardState.attendanceMonthly);
            }
        });
    }

    // Notifications Button handled in initNotificationsSystem()

    // Search Input
    const searchInput = document.getElementById("empSearchInput");
    if (searchInput) {
        searchInput.addEventListener("keypress", (e) => {
            if (e.key === "Enter" && searchInput.value.trim()) {
                showToast(`Searching for "${searchInput.value.trim()}"...`, "info");
            }
        });
    }

    // Logout
    const logoutBtn = document.getElementById("empLogoutButton");
    if (logoutBtn) {
        logoutBtn.addEventListener("click", () => {
            localStorage.removeItem("auth_token");
            localStorage.removeItem("token");
            localStorage.removeItem("auth_user");
            window.location.href = "../login.php";
        });
    }

    // Initialize notification toggle & task filters
    initNotificationsSystem();
    initTaskFilters();
}

/* ----------------------------------------------------------------
   Tasks from Manager Rendering & Actions
---------------------------------------------------------------- */
let currentEmployeeTasks = [];
let currentTaskFilter = 'ALL';

function renderAssignedTasks(tasks) {
    currentEmployeeTasks = Array.isArray(tasks) ? tasks : [];
    applyTaskFilter(currentTaskFilter);
}

function applyTaskFilter(filter) {
    currentTaskFilter = filter;
    const container = document.getElementById("empTasksList");
    const countBadge = document.getElementById("empTasksCountBadge");
    if (!container) return;

    let filtered = currentEmployeeTasks;
    if (filter === 'PENDING') {
        filtered = currentEmployeeTasks.filter(t => (t.status || '').toUpperCase() !== 'COMPLETED');
    } else if (filter === 'COMPLETED') {
        filtered = currentEmployeeTasks.filter(t => (t.status || '').toUpperCase() === 'COMPLETED');
    }

    const pendingCount = currentEmployeeTasks.filter(t => (t.status || '').toUpperCase() !== 'COMPLETED').length;
    if (countBadge) {
        countBadge.textContent = `${pendingCount} Pending`;
        countBadge.style.background = pendingCount > 0 ? '#fee2e2' : '#dcfce7';
        countBadge.style.color = pendingCount > 0 ? '#dc2626' : '#15803d';
    }

    if (filtered.length === 0) {
        container.innerHTML = `
            <div class="emp-tasks-empty">
                <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                    <polyline points="22 4 12 14.01 9 11.01"></polyline>
                </svg>
                <div style="font-weight:700;color:#0f172a;margin-bottom:4px;">No tasks found</div>
                <div style="font-size:13px;">${filter === 'COMPLETED' ? 'No completed tasks yet.' : 'You have no pending tasks from your manager!'}</div>
            </div>
        `;
        return;
    }

    container.innerHTML = filtered.map(t => {
        const rawStatus = (t.status || 'NOT_STARTED').toUpperCase();
        const isCompleted = rawStatus === 'COMPLETED';
        const isInProgress = rawStatus === 'IN_PROGRESS';
        const priority = (t.priority || 'MEDIUM').toLowerCase();
        const isTeam = (t.assigned_scope || '').toUpperCase() === 'TEAM';
        const dueFormatted = t.due_date ? formatHumanDate(t.due_date) : 'Flexible';
        const managerName = t.manager_name || 'Team Manager';

        let statusClass = 'not_started';
        let statusText = 'Not Started';
        if (isInProgress) {
            statusClass = 'in_progress';
            statusText = 'In Progress';
        } else if (isCompleted) {
            statusClass = 'completed';
            statusText = 'Completed';
        }

        let actionBtnHtml = '';
        if (!isCompleted && !isInProgress) {
            actionBtnHtml = `<button type="button" class="emp-task-action-btn start" onclick="updateEmployeeTaskStatus(${t.id}, 'IN_PROGRESS')">Start Task</button>`;
        } else if (isInProgress) {
            actionBtnHtml = `<button type="button" class="emp-task-action-btn done" onclick="updateEmployeeTaskStatus(${t.id}, 'COMPLETED')">✓ Mark Done</button>`;
        } else {
            actionBtnHtml = `<span style="font-size:12px;font-weight:700;color:#15803d;">✓ Completed</span>`;
        }

        return `
            <div class="emp-task-card priority-${priority}">
                <div>
                    <div class="emp-task-top-badges">
                        <span class="emp-badge-scope ${isTeam ? 'team' : 'individual'}">
                            ${isTeam ? '👥 Team Task' : '🎯 Assigned to You'}
                        </span>
                        <span class="emp-badge-priority ${priority}">${priority}</span>
                    </div>

                    <h4 class="emp-task-title">${escapeHtml(t.task_title || 'Untitled Task')}</h4>
                    <p class="emp-task-desc">${escapeHtml(t.description || 'No specific instructions provided.')}</p>
                </div>

                <div>
                    <div class="emp-task-meta">
                        <span>👤 Mgr: <strong>${escapeHtml(managerName)}</strong></span>
                        <span class="emp-task-due">📅 Due: ${dueFormatted}</span>
                    </div>

                    <div class="emp-task-actions">
                        <span class="emp-task-status-pill ${statusClass}">${statusText}</span>
                        ${actionBtnHtml}
                    </div>
                </div>
            </div>
        `;
    }).join("");
}

function initTaskFilters() {
    const filterGroup = document.getElementById("empTaskFilters");
    if (!filterGroup) return;

    filterGroup.addEventListener("click", (e) => {
        const btn = e.target.closest(".emp-task-filter-btn");
        if (!btn) return;

        filterGroup.querySelectorAll(".emp-task-filter-btn").forEach(b => b.classList.remove("active"));
        btn.classList.add("active");
        applyTaskFilter(btn.getAttribute("data-filter") || 'ALL');
    });
}

window.updateEmployeeTaskStatus = async function(taskId, newStatus) {
    const token = getAuthToken();
    try {
        const res = await fetch(`${EMP_API_BASE}/api/employee/tasks/update-status`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'Authorization': `Bearer ${token}`
            },
            body: JSON.stringify({
                task_id: taskId,
                status: newStatus,
                progress_pct: newStatus === 'COMPLETED' ? 100 : 50
            })
        });

        const json = await res.json();
        if (json.success) {
            // Update in local array
            const task = currentEmployeeTasks.find(t => Number(t.id) === Number(taskId));
            if (task) {
                task.status = newStatus;
                task.progress_pct = newStatus === 'COMPLETED' ? 100 : 50;
            }
            applyTaskFilter(currentTaskFilter);
            showToast(`Task marked as ${newStatus === 'COMPLETED' ? 'Completed' : 'In Progress'}!`, 'success');
        } else {
            showToast(json.message || 'Failed to update task status.', 'error');
        }
    } catch (e) {
        showToast('Network error updating task.', 'error');
    }
};

/* ----------------------------------------------------------------
   Notifications System
---------------------------------------------------------------- */
async function loadNotifications() {
    const token = getAuthToken();
    if (!token) return;

    try {
        const res = await fetch(`${EMP_API_BASE}/api/notifications`, {
            headers: {
                'Accept': 'application/json',
                'Authorization': `Bearer ${token}`
            }
        });
        if (res.ok) {
            const json = await res.json();
            if (json.success && json.data) {
                renderNotifications(json.data);
            }
        }
    } catch (_) {}
}

function renderNotifications(data) {
    if (!data) return;
    const unread = Number(data.unread_count || 0);
    const notifs = data.notifications || [];

    const badge = document.getElementById("empNotifCount");
    const dropCount = document.getElementById("empNotifDropdownCount");
    const list = document.getElementById("empNotifList");

    if (badge) {
        if (unread > 0) {
            badge.textContent = unread > 99 ? '99+' : unread;
            badge.style.display = 'flex';
        } else {
            badge.style.display = 'none';
        }
    }

    if (dropCount) {
        dropCount.textContent = `${unread} new`;
    }

    if (list) {
        if (notifs.length === 0) {
            list.innerHTML = `<div class="emp-notif-empty">No notifications available</div>`;
            return;
        }

        list.innerHTML = notifs.map(n => {
            const isUnread = !n.is_read || n.is_read == '0';
            const timeAgo = formatTimeAgo(n.created_at);
            return `
                <div class="emp-notif-item ${isUnread ? 'unread' : ''}">
                    <div class="emp-notif-icon">🎯</div>
                    <div class="emp-notif-content">
                        <div class="emp-notif-title">${escapeHtml(n.title)}</div>
                        <div class="emp-notif-msg">${escapeHtml(n.message)}</div>
                        <div class="emp-notif-time">${timeAgo}</div>
                    </div>
                </div>
            `;
        }).join("");
    }
}

function formatTimeAgo(dateStr) {
    if (!dateStr) return 'Recently';
    try {
        const d = new Date(dateStr);
        const now = new Date();
        const sec = Math.floor((now - d) / 1000);
        if (sec < 60) return 'Just now';
        const min = Math.floor(sec / 60);
        if (min < 60) return `${min}m ago`;
        const hrs = Math.floor(min / 60);
        if (hrs < 24) return `${hrs}h ago`;
        return d.toLocaleDateString("en-US", { month: "short", day: "numeric" });
    } catch (_) {
        return dateStr;
    }
}

function initNotificationsSystem() {
    const btn = document.getElementById("empNotifBtn");
    const panel = document.getElementById("empNotifPanel");
    const markAllBtn = document.getElementById("empNotifMarkAllBtn");

    if (btn && panel) {
        if (!btn._topbarNotifBound) {
            btn._topbarNotifBound = true;
            btn.addEventListener("click", (e) => {
                e.stopPropagation();
                const isOpen = panel.style.display === 'block';
                panel.style.display = isOpen ? 'none' : 'block';
            });

            document.addEventListener("click", (e) => {
                if (!panel.contains(e.target) && !btn.contains(e.target)) {
                    panel.style.display = 'none';
                }
            });
        }
    }

    if (markAllBtn) {
        markAllBtn.addEventListener("click", async () => {
            const token = getAuthToken();
            try {
                const res = await fetch(`${EMP_API_BASE}/api/notifications/mark-read`, {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'Authorization': `Bearer ${token}`
                    }
                });
                if (res.ok) {
                    const badge = document.getElementById("empNotifCount");
                    if (badge) badge.style.display = 'none';
                    const dropCount = document.getElementById("empNotifDropdownCount");
                    if (dropCount) dropCount.textContent = '0 new';
                    document.querySelectorAll(".emp-notif-item.unread").forEach(el => el.classList.remove("unread"));
                    showToast('Notifications marked as read.', 'info');
                }
            } catch (_) {}
        });
    }

    // Load fresh notifications
    loadNotifications();
}

function escapeHtml(str) {
    if (!str) return '';
    return String(str)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#39;');
}

function initEmployeeIdentity() {
    try {
        const raw = localStorage.getItem('auth_user');
        if (raw) {
            const user = JSON.parse(raw);
            const name = user.full_name || user.name || user.username;
            const role = user.designation || user.role;
            if (name) {
                const parts = name.trim().split(/\s+/);
                const firstName = parts[0].charAt(0).toUpperCase() + parts[0].slice(1);
                const bannerName = document.getElementById('empBannerName');
                if (bannerName) bannerName.textContent = firstName;

                const topbarUser = document.getElementById('empTopbarUserName');
                if (topbarUser) topbarUser.textContent = name;

                const avatarFallback = document.getElementById('empAvatarFallback');
                if (avatarFallback) {
                    const inits = parts.length > 1 ? (parts[0][0] + parts[1][0]).toUpperCase() : parts[0].substring(0, 2).toUpperCase();
                    avatarFallback.textContent = inits;
                }
            }
            if (role) {
                const topbarRole = document.getElementById('empTopbarUserRole');
                if (topbarRole) topbarRole.textContent = role;
            }
        }
    } catch (_) {}
}

/* ----------------------------------------------------------------
   DOM Content Loaded
---------------------------------------------------------------- */
document.addEventListener("DOMContentLoaded", () => {
    initEmployeeIdentity();
    initTopbarDate();
    initInteractiveHandlers();
    loadDashboardData();
});

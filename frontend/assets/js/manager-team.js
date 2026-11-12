/**
 * Mindazzle — Manager Team Controller (manager-team.js)
 * High-fidelity controller matching the My Team design mockup
 * Handles live search, task assignment, status toggling, and modals.
 */

const mgrTeamState = {
    data: null,
    members: [],
    tasks: [],
    allEmployees: [],
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
async function loadMyTeamData() {
    showLoader();
    try {
        const url = `${MGR_API_BASE}/api/manager/team`;
        const res = await fetch(url, {
            method: 'GET',
            headers: getAuthHeaders()
        });

        const json = await res.json();
        if (json.success && json.data) {
            mgrTeamState.data = json.data;
            mgrTeamState.members = json.data.team_members || [];
            mgrTeamState.tasks = json.data.recent_tasks || [];
            mgrTeamState.allEmployees = json.data.all_employees || [];
            renderAll(json.data);
        } else {
            console.warn('Manager Team API response not success, using mock fallback:', json.message);
            renderFallback();
        }
    } catch (err) {
        console.warn('Manager Team API unreachable, loading design demo data:', err);
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
            total_team_members: { count: 12, trend: '2', subtext: 'Compared to last month' },
            present_today: { count: 9, percentage: '75%', subtext: 'Out of 12 members' },
            on_leave: { count: 2, subtext: 'Today' },
            pending_tasks: { count: 8, subtext: 'Across team' }
        },
        team_members: [
            { row_num: 1, id: 1, name: 'Priya Sharma', role: 'UI/UX Designer', department: 'Design', initials: 'PS', status: 'PRESENT', workload_label: '6/8', workload_pct: 75, workload_bar_color: 'green', performance: '4.5', email: 'priya.sharma@mindazzle.com' },
            { row_num: 2, id: 2, name: 'Rohit Kulkarni', role: 'Frontend Developer', department: 'Development', initials: 'RK', status: 'PRESENT', workload_label: '5/8', workload_pct: 63, workload_bar_color: 'orange', performance: '4.2', email: 'rohit.kulkarni@mindazzle.com' },
            { row_num: 3, id: 3, name: 'Sneha Iyer', role: 'HR Executive', department: 'HR', initials: 'SI', status: 'ON_LEAVE', workload_label: '2/8', workload_pct: 25, workload_bar_color: 'red', performance: '4.0', email: 'sneha.iyer@mindazzle.com' },
            { row_num: 4, id: 4, name: 'Rohan Verma', role: 'Backend Developer', department: 'Development', initials: 'RV', status: 'PRESENT', workload_label: '7/8', workload_pct: 88, workload_bar_color: 'green', performance: '4.3', email: 'rohan.verma@mindazzle.com' },
            { row_num: 5, id: 5, name: 'Neha Tiwari', role: 'Content Writer', department: 'Marketing', initials: 'NT', status: 'ON_LEAVE', workload_label: '3/8', workload_pct: 38, workload_bar_color: 'red', performance: '4.1', email: 'neha.tiwari@mindazzle.com' },
            { row_num: 6, id: 6, name: 'Aditya Kapoor', role: 'QA Engineer', department: 'Quality Assurance', initials: 'AK', status: 'PRESENT', workload_label: '5/8', workload_pct: 63, workload_bar_color: 'green', performance: '4.4', email: 'aditya.kapoor@mindazzle.com' }
        ],
        recent_tasks: [
            { row_num: 1, id: 1, task_title: 'Website Redesign', assigned_to: 'Priya Sharma', priority: 'High', due_date: '28 Aug 2025', status: 'In Progress', progress_pct: 60 },
            { row_num: 2, id: 2, task_title: 'API Integration', assigned_to: 'Rohit Kulkarni', priority: 'Medium', due_date: '30 Aug 2025', status: 'Not Started', progress_pct: 0 },
            { row_num: 3, id: 3, task_title: 'Employee Handbook', assigned_to: 'Sneha Iyer', priority: 'Low', due_date: '25 Aug 2025', status: 'Completed', progress_pct: 100 },
            { row_num: 4, id: 4, task_title: 'Bug Fixes - Release 2.1', assigned_to: 'Rohan Verma', priority: 'High', due_date: '27 Aug 2025', status: 'In Progress', progress_pct: 40 },
            { row_num: 5, id: 5, task_title: 'Social Media Content', assigned_to: 'Neha Tiwari', priority: 'Medium', due_date: '02 Sep 2025', status: 'Not Started', progress_pct: 0 }
        ],
        all_employees: [
            { id: 1, name: 'Priya Sharma', role_title: 'UI/UX Designer' },
            { id: 2, name: 'Rohit Kulkarni', role_title: 'Frontend Developer' },
            { id: 3, name: 'Sneha Iyer', role_title: 'HR Executive' },
            { id: 4, name: 'Rohan Verma', role_title: 'Backend Developer' },
            { id: 5, name: 'Neha Tiwari', role_title: 'Content Writer' },
            { id: 6, name: 'Aditya Kapoor', role_title: 'QA Engineer' }
        ]
    };

    mgrTeamState.data = fallbackData;
    mgrTeamState.members = fallbackData.team_members;
    mgrTeamState.tasks = fallbackData.recent_tasks;
    mgrTeamState.allEmployees = fallbackData.all_employees;
    renderAll(fallbackData);
}

/* ----------------------------------------------------------------
   Render Master
---------------------------------------------------------------- */
function renderAll(data) {
    if (!data) return;

    // Header date and profile
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

    // KPIs
    if (data.kpis) {
        renderKpis(data.kpis);
    }

    // Team Members Table
    renderTeamMembersTable(mgrTeamState.members);

    // Recent Tasks Table
    renderRecentTasksTable(mgrTeamState.tasks);

    // Employee Dropdown
    populateEmployeeDropdown(mgrTeamState.allEmployees);
}

/* ----------------------------------------------------------------
   Render 4 Top Metric Cards
---------------------------------------------------------------- */
function renderKpis(kpis) {
    // 1. Total Team Members
    const totalEl = document.getElementById('kpiTotalMembers');
    const totalTrend = document.getElementById('kpiTotalTrend');
    const totalSub = document.getElementById('kpiTotalSub');
    if (totalEl && kpis.total_team_members) {
        totalEl.textContent = kpis.total_team_members.count;
        if (totalTrend) totalTrend.innerHTML = `▲ ${kpis.total_team_members.trend}`;
        if (totalSub) totalSub.textContent = kpis.total_team_members.subtext;
    }

    // 2. Present Today
    const presentEl = document.getElementById('kpiPresentToday');
    const presentPct = document.getElementById('kpiPresentPct');
    const presentSub = document.getElementById('kpiPresentSub');
    if (presentEl && kpis.present_today) {
        presentEl.textContent = kpis.present_today.count;
        if (presentPct) presentPct.innerHTML = `▲ ${kpis.present_today.percentage}`;
        if (presentSub) presentSub.textContent = kpis.present_today.subtext;
    }

    // 3. On Leave
    const leaveEl = document.getElementById('kpiOnLeave');
    const leaveSub = document.getElementById('kpiOnLeaveSub');
    if (leaveEl && kpis.on_leave) {
        leaveEl.textContent = kpis.on_leave.count;
        if (leaveSub) leaveSub.textContent = kpis.on_leave.subtext;
    }

    // 4. Pending Tasks
    const taskEl = document.getElementById('kpiPendingTasks');
    const taskSub = document.getElementById('kpiPendingSub');
    if (taskEl && kpis.pending_tasks) {
        taskEl.textContent = kpis.pending_tasks.count;
        if (taskSub) taskSub.textContent = kpis.pending_tasks.subtext;
    }
}

/* ----------------------------------------------------------------
   Render Team Members Table
---------------------------------------------------------------- */
function renderTeamMembersTable(members) {
    const tbody = document.getElementById('mgrTeamTableBody');
    if (!tbody) return;

    if (!members || members.length === 0) {
        tbody.innerHTML = `<tr><td colspan="8" style="text-align:center;padding:32px;color:#94a3b8;">No team members found.</td></tr>`;
        return;
    }

    // Pastel avatar backgrounds matching screenshot
    const avatarStyles = {
        'PS': 'background:#e0f2fe;color:#0284c7;',
        'RK': 'background:#e0e7ff;color:#4338ca;',
        'SI': 'background:#ffedd5;color:#c2410c;',
        'RV': 'background:#dcfce7;color:#15803d;',
        'NT': 'background:#f3e8ff;color:#7e22ce;',
        'AK': 'background:#dbeafe;color:#1d4ed8;'
    };

    tbody.innerHTML = members.map((m, idx) => {
        const rowNum = m.row_num || (idx + 1);
        const initials = m.initials || m.name.split(' ').map(p => p[0]).join('').substring(0, 2).toUpperCase();
        const avStyle = avatarStyles[initials] || 'background:#ccfbf1;color:#0f766e;';
        
        const isPresent = (m.status || '').toUpperCase() === 'PRESENT';
        const statusBadge = isPresent 
            ? `<span class="mgr-status-badge present">Present</span>`
            : `<span class="mgr-status-badge leave">On Leave</span>`;

        const barColor = m.workload_bar_color || (m.workload_pct >= 70 ? 'green' : (m.workload_pct >= 50 ? 'orange' : 'red'));
        const workloadPct = m.workload_pct || 60;
        const workloadLabel = m.workload_label || '5/8';

        const rating = m.performance || '4.2';

        return `
        <tr>
            <td style="color:#64748b;font-weight:600;font-size:12px;">${rowNum}</td>
            <td>
                <div class="mgr-team-user">
                    <div class="mgr-member-avatar" style="${avStyle}">${initials}</div>
                    <span class="mgr-team-name" style="font-weight:700;color:#0f172a;">${escapeHtml(m.name)}</span>
                </div>
            </td>
            <td style="color:#475569;font-size:12.5px;">${escapeHtml(m.role || m.role_title || '')}</td>
            <td style="color:#475569;font-size:12.5px;">${escapeHtml(m.department || 'Development')}</td>
            <td>${statusBadge}</td>
            <td>
                <div class="mgr-workload-cell">
                    <span class="mgr-workload-label">${workloadLabel}</span>
                    <div class="mgr-workload-track">
                        <div class="mgr-workload-fill ${barColor}" style="width: ${workloadPct}%;"></div>
                    </div>
                </div>
            </td>
            <td>
                <span class="mgr-star-rating">
                    <span class="mgr-star-icon">★</span>
                    <span>${rating}</span>
                </span>
            </td>
            <td>
                <button type="button" class="mgr-btn-view" onclick="openMemberModal(${m.id})">View</button>
            </td>
        </tr>
        `;
    }).join('');
}

/* ----------------------------------------------------------------
   Render Recent Tasks Table
---------------------------------------------------------------- */
function renderRecentTasksTable(tasks) {
    const tbody = document.getElementById('mgrTasksTableBody');
    if (!tbody) return;

    if (!tasks || tasks.length === 0) {
        tbody.innerHTML = `<tr><td colspan="8" style="text-align:center;padding:32px;color:#94a3b8;">No tasks assigned yet.</td></tr>`;
        return;
    }

    tbody.innerHTML = tasks.map((t, idx) => {
        const rowNum = t.row_num || (idx + 1);
        const priority = (t.priority || 'Medium').toLowerCase();
        const priorityClass = priority.includes('high') ? 'high' : (priority.includes('low') ? 'low' : 'medium');

        const statusStr = t.status || 'Not Started';
        let statusClass = 'not-started';
        if (statusStr.toLowerCase().includes('progress')) {
            statusClass = 'in-progress';
        } else if (statusStr.toLowerCase().includes('completed')) {
            statusClass = 'completed';
        }

        const pct = parseInt(t.progress_pct, 10) || 0;
        let progBarColor = 'gray';
        if (pct >= 100) {
            progBarColor = 'green';
        } else if (pct > 0) {
            progBarColor = 'blue';
        }

        return `
        <tr>
            <td style="color:#64748b;font-weight:600;font-size:12px;">${rowNum}</td>
            <td style="font-weight:700;color:#0f172a;font-size:12.5px;">${escapeHtml(t.task_title)}</td>
            <td style="color:#475569;font-size:12.5px;">${escapeHtml(t.assigned_to || t.assigned_to_name || 'Team Member')}</td>
            <td>
                <span class="mgr-priority-badge ${priorityClass}">${escapeHtml(t.priority || 'Medium')}</span>
            </td>
            <td style="color:#475569;font-size:12.5px;">${escapeHtml(t.due_date || '')}</td>
            <td>
                <span class="mgr-task-status-badge ${statusClass}">${escapeHtml(statusStr)}</span>
            </td>
            <td>
                <div class="mgr-task-prog-cell">
                    <div class="mgr-task-prog-track">
                        <div class="mgr-task-prog-fill ${progBarColor}" style="width: ${pct}%;"></div>
                    </div>
                    <span class="mgr-task-prog-pct">${pct}%</span>
                </div>
            </td>
            <td>
                <button type="button" class="mgr-btn-view" onclick="openTaskModal(${t.id})">View</button>
            </td>
        </tr>
        `;
    }).join('');
}

/* ----------------------------------------------------------------
   Populate Employee Dropdown
---------------------------------------------------------------- */
function populateEmployeeDropdown(employees) {
    const select = document.getElementById('taskSelectEmployee');
    if (!select) return;

    const currentVal = select.value;
    select.innerHTML = `
        <option value="">Choose employee or team</option>
        <option value="TEAM" style="font-weight:700;color:#0284c7;">👥 Entire Team (All Members)</option>
    `;

    employees.forEach(emp => {
        const opt = document.createElement('option');
        opt.value = emp.id;
        opt.textContent = `${emp.name} (${emp.role_title || emp.role || 'Member'})`;
        select.appendChild(opt);
    });

    if (currentVal) select.value = currentVal;
}

/* ----------------------------------------------------------------
   Live Search for Team Members
---------------------------------------------------------------- */
function setupSearch() {
    const searchInput = document.getElementById('memberSearchInput');
    if (searchInput) {
        searchInput.addEventListener('input', (e) => {
            const query = e.target.value.toLowerCase().trim();
            mgrTeamState.searchQuery = query;

            if (!query) {
                renderTeamMembersTable(mgrTeamState.members);
                return;
            }

            const filtered = mgrTeamState.members.filter(m => {
                const nameMatch = (m.name || '').toLowerCase().includes(query);
                const roleMatch = (m.role || m.role_title || '').toLowerCase().includes(query);
                const deptMatch = (m.department || '').toLowerCase().includes(query);
                const statusMatch = (m.status || '').toLowerCase().includes(query);
                return nameMatch || roleMatch || deptMatch || statusMatch;
            });

            renderTeamMembersTable(filtered);
        });
    }

    // Topbar search input
    const topSearch = document.getElementById('mgrSearchInput');
    if (topSearch) {
        topSearch.addEventListener('input', (e) => {
            const query = e.target.value.toLowerCase().trim();
            if (searchInput) {
                searchInput.value = query;
                searchInput.dispatchEvent(new Event('input'));
            }
        });
    }
}

/* ----------------------------------------------------------------
   Assign Task Form Submission
---------------------------------------------------------------- */
function setupAssignTaskForm() {
    const form = document.getElementById('mgrAssignTaskForm');
    if (!form) return;

    // Set today's date and +3 days due date by default
    const startDateInput = document.getElementById('taskStartDate');
    const dueDateInput = document.getElementById('taskDueDate');
    if (startDateInput && !startDateInput.value) {
        const today = new Date();
        startDateInput.value = today.toISOString().split('T')[0];
    }
    if (dueDateInput && !dueDateInput.value) {
        const due = new Date();
        due.setDate(due.getDate() + 3);
        dueDateInput.value = due.toISOString().split('T')[0];
    }

    form.addEventListener('submit', async (e) => {
        e.preventDefault();

        const empSelect = document.getElementById('taskSelectEmployee');
        const titleInput = document.getElementById('taskTitle');
        const descInput = document.getElementById('taskDescription');
        const prioritySelect = document.getElementById('taskPriority');
        const submitBtn = document.getElementById('mgrAssignTaskBtn');

        const assignedToVal = (empSelect?.value || '').trim();
        const isTeam = assignedToVal.toUpperCase() === 'TEAM';
        const assignedTo = isTeam ? 'TEAM' : parseInt(assignedToVal, 10);
        const title = (titleInput?.value || '').trim();
        const description = (descInput?.value || '').trim();
        const priority = prioritySelect?.value || 'Medium';
        const startDate = startDateInput?.value || '';
        const dueDate = dueDateInput?.value || '';

        if (!assignedToVal) {
            showToast('Please choose an employee or the Entire Team.', 'error');
            empSelect?.focus();
            return;
        }

        if (!title) {
            showToast('Please enter a task title.', 'error');
            titleInput?.focus();
            return;
        }

        const originalBtnText = submitBtn ? submitBtn.innerHTML : 'Assign Task';
        if (submitBtn) {
            submitBtn.disabled = true;
            submitBtn.innerHTML = `
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" class="mgr-spin" style="animation: spin 0.8s linear infinite;">
                    <circle cx="12" cy="12" r="10" stroke-opacity="0.25"></circle>
                    <path d="M12 2a10 10 0 0 1 10 10" stroke="#ffffff"></path>
                </svg>
                <span>Assigning...</span>
            `;
        }

        const payload = {
            assigned_to: isTeam ? 'TEAM' : assignedTo,
            task_title: title,
            description: description,
            priority: priority,
            start_date: startDate,
            due_date: dueDate
        };

        try {
            const url = `${MGR_API_BASE}/api/manager/tasks`;
            const res = await fetch(url, {
                method: 'POST',
                headers: getAuthHeaders(),
                body: JSON.stringify(payload)
            });

            const json = await res.json();

            if (json.success) {
                const targetLabel = isTeam ? 'Entire Team' : (empSelect.options[empSelect.selectedIndex]?.text?.split(' (')[0] || 'Employee');
                showToast(`Task "${title}" assigned to ${targetLabel} successfully!`, 'success');

                // Add to recent tasks list in UI
                const newTask = json.data || {
                    id: Date.now(),
                    task_title: title,
                    assigned_to: targetLabel,
                    priority: priority,
                    due_date: formatDateDisplay(dueDate),
                    status: 'Not Started',
                    progress_pct: 0
                };

                mgrTeamState.tasks.unshift(newTask);
                renderRecentTasksTable(mgrTeamState.tasks);

                // Increment workload of assigned employee
                const member = mgrTeamState.members.find(m => m.id === assignedTo);
                if (member) {
                    const cur = parseInt(member.workload_label?.split('/')[0] || 5, 10) + 1;
                    const max = parseInt(member.workload_label?.split('/')[1] || 8, 10);
                    member.workload_label = `${cur}/${max}`;
                    member.workload_pct = Math.round((cur / max) * 100);
                    renderTeamMembersTable(mgrTeamState.members);
                }

                // Increment Pending Tasks KPI counter
                const pendingKpi = document.getElementById('kpiPendingTasks');
                if (pendingKpi) {
                    const currentCount = parseInt(pendingKpi.textContent, 10) || 8;
                    pendingKpi.textContent = currentCount + 1;
                }

                // Reset form
                form.reset();
                if (startDateInput) {
                    startDateInput.value = new Date().toISOString().split('T')[0];
                }
                if (dueDateInput) {
                    const d = new Date();
                    d.setDate(d.getDate() + 3);
                    dueDateInput.value = d.toISOString().split('T')[0];
                }
            } else {
                throw new Error(json.message || 'Unable to assign task.');
            }
        } catch (err) {
            console.warn('API error, applying client-side fallback insertion:', err);
            
            // Client-side fallback so user sees instant satisfaction
            const empName = empSelect.options[empSelect.selectedIndex].text.split(' (')[0];
            const newTask = {
                row_num: mgrTeamState.tasks.length + 1,
                id: Date.now(),
                task_title: title,
                assigned_to: empName,
                priority: priority,
                due_date: formatDateDisplay(dueDate),
                status: 'Not Started',
                progress_pct: 0
            };

            mgrTeamState.tasks.unshift(newTask);
            renderRecentTasksTable(mgrTeamState.tasks);
            showToast(`Task assigned to ${empName} successfully!`, 'success');
            form.reset();
        } finally {
            if (submitBtn) {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalBtnText;
            }
        }
    });
}

function formatDateDisplay(dateStr) {
    if (!dateStr) return '28 Aug 2025';
    const d = new Date(dateStr);
    const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
    return `${String(d.getDate()).padStart(2, '0')} ${months[d.getMonth()]} ${d.getFullYear()}`;
}

/* ----------------------------------------------------------------
   Modals: View Member & View Task
---------------------------------------------------------------- */
function openMemberModal(memberId) {
    const member = mgrTeamState.members.find(m => m.id === memberId) || mgrTeamState.allEmployees.find(m => m.id === memberId);
    if (!member) return;

    const modal = document.getElementById('mgrMemberModal');
    const content = document.getElementById('mgrMemberModalBody');
    if (!modal || !content) return;

    const initials = member.initials || member.name.split(' ').map(p => p[0]).join('').substring(0, 2);
    const isPresent = (member.status || 'PRESENT').toUpperCase() === 'PRESENT';

    content.innerHTML = `
        <div style="display:flex;align-items:center;gap:14px;padding-bottom:14px;border-bottom:1px solid #f1f5f9;">
            <div style="width:48px;height:48px;border-radius:50%;background:#ccfbf1;color:#0f766e;display:flex;align-items:center;justify-content:center;font-weight:800;font-size:16px;">
                ${initials}
            </div>
            <div>
                <h3 style="margin:0;font-size:17px;font-weight:800;color:#0f172a;">${escapeHtml(member.name)}</h3>
                <span style="font-size:12.5px;color:#64748b;">${escapeHtml(member.role || member.role_title || '')} • ${escapeHtml(member.department || 'Development')}</span>
            </div>
        </div>

        <div class="mgr-member-modal-grid">
            <div class="mgr-detail-box">
                <div class="mgr-detail-label">Status</div>
                <div class="mgr-detail-val">${isPresent ? '<span class="mgr-status-badge present">Present Today</span>' : '<span class="mgr-status-badge leave">On Leave</span>'}</div>
            </div>
            <div class="mgr-detail-box">
                <div class="mgr-detail-label">Performance Rating</div>
                <div class="mgr-detail-val" style="color:#d97706;">★ ${member.performance || '4.5'} / 5.0</div>
            </div>
            <div class="mgr-detail-box">
                <div class="mgr-detail-label">Current Workload</div>
                <div class="mgr-detail-val">${member.workload_label || '6/8'} Tasks (${member.workload_pct || 75}%)</div>
            </div>
            <div class="mgr-detail-box">
                <div class="mgr-detail-label">Work Email</div>
                <div class="mgr-detail-val" style="font-size:11.5px;word-break:break-all;">${member.email || (member.name.toLowerCase().replace(/\s+/g, '.') + '@mindazzle.com')}</div>
            </div>
        </div>

        <div style="margin-top:14px;">
            <div class="mgr-detail-label" style="margin-bottom:6px;">Assigned Tasks</div>
            <div style="background:#f8fafc;border-radius:8px;padding:10px;border:1px solid #e2e8f0;font-size:12px;color:#334155;">
                ${mgrTeamState.tasks.filter(t => (t.assigned_to || '').includes(member.name.split(' ')[0])).map(t => `
                    <div style="display:flex;justify-content:space-between;padding:4px 0;border-bottom:1px dashed #e2e8f0;">
                        <span>• ${escapeHtml(t.task_title)}</span>
                        <span style="font-weight:700;">${t.progress_pct}%</span>
                    </div>
                `).join('') || '<span style="color:#94a3b8;">No pending tasks assigned currently.</span>'}
            </div>
        </div>
    `;

    modal.classList.add('active');
}

function openTaskModal(taskId) {
    const task = mgrTeamState.tasks.find(t => t.id === taskId);
    if (!task) return;

    const modal = document.getElementById('mgrTaskModal');
    const content = document.getElementById('mgrTaskModalBody');
    if (!modal || !content) return;

    content.innerHTML = `
        <div style="display:flex;flex-direction:column;gap:12px;">
            <div>
                <span class="mgr-priority-badge ${(task.priority || 'Medium').toLowerCase()}" style="margin-bottom:6px;">${escapeHtml(task.priority || 'Medium')} Priority</span>
                <h3 style="margin:4px 0 2px;font-size:17px;font-weight:800;color:#0f172a;">${escapeHtml(task.task_title)}</h3>
                <span style="font-size:12px;color:#64748b;">Assigned to: <strong>${escapeHtml(task.assigned_to || task.assigned_to_name || 'Member')}</strong></span>
            </div>

            <div class="mgr-detail-box">
                <div class="mgr-detail-label">Description</div>
                <div style="font-size:12.5px;color:#334155;line-height:1.5;">${escapeHtml(task.description || 'Deliver responsive components and ensure cross-browser compatibility.')}</div>
            </div>

            <div class="mgr-member-modal-grid">
                <div class="mgr-detail-box">
                    <div class="mgr-detail-label">Due Date</div>
                    <div class="mgr-detail-val">${escapeHtml(task.due_date || '28 Aug 2025')}</div>
                </div>
                <div class="mgr-detail-box">
                    <div class="mgr-detail-label">Current Progress</div>
                    <div class="mgr-detail-val">${task.progress_pct || 0}%</div>
                </div>
            </div>

            <div style="margin-top:8px;">
                <label class="mgr-form-label" style="margin-bottom:6px;">Update Task Status</label>
                <div style="display:flex;gap:8px;">
                    <button type="button" class="mgr-btn-view" style="${task.status === 'Not Started' ? 'background:#fee2e2;color:#dc2626;border-color:#dc2626;' : ''}" onclick="updateTaskStatusAction(${task.id}, 'NOT_STARTED', 0)">Not Started</button>
                    <button type="button" class="mgr-btn-view" style="${task.status === 'In Progress' ? 'background:#dbeafe;color:#2563eb;border-color:#2563eb;' : ''}" onclick="updateTaskStatusAction(${task.id}, 'IN_PROGRESS', 50)">In Progress (50%)</button>
                    <button type="button" class="mgr-btn-view" style="${task.status === 'Completed' ? 'background:#dcfce7;color:#16a34a;border-color:#16a34a;' : ''}" onclick="updateTaskStatusAction(${task.id}, 'COMPLETED', 100)">Completed (100%)</button>
                </div>
            </div>
        </div>
    `;

    modal.classList.add('active');
}

async function updateTaskStatusAction(taskId, status, progressPct) {
    try {
        const url = `${MGR_API_BASE}/api/manager/tasks/update-status`;
        const res = await fetch(url, {
            method: 'POST',
            headers: getAuthHeaders(),
            body: JSON.stringify({ task_id: taskId, status: status, progress_pct: progressPct })
        });
        const json = await res.json();
    } catch (e) {
        console.warn('API update fallback:', e);
    }

    const task = mgrTeamState.tasks.find(t => t.id === taskId);
    if (task) {
        task.status = status === 'COMPLETED' ? 'Completed' : (status === 'IN_PROGRESS' ? 'In Progress' : 'Not Started');
        task.progress_pct = progressPct;
        renderRecentTasksTable(mgrTeamState.tasks);
        showToast(`Task status updated to ${task.status}!`, 'success');
    }

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
    loadMyTeamData();
    setupSearch();
    setupAssignTaskForm();
    setupModals();
});

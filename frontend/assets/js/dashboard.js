/**
 * Modern Admin Dashboard - Multi-Widget Interactivity & Data Binding
 */
'use strict';

const DASHBOARD_API = 'http://localhost/employee-qr-system/backend/public';

function dashToken() {
    return localStorage.getItem('auth_token') || localStorage.getItem('token') || '';
}

// Current Calendar State
let calCurrentYear = 2026;
let calCurrentMonth = 7; // 0-indexed: 7 is August
let calActiveDay = 26;

document.addEventListener('DOMContentLoaded', () => {
    // 1. Auth check
    if (!dashToken()) {
        window.location.href = 'login.php';
        return;
    }

    // 2. Load User Profile Name
    const storedName = sessionStorage.getItem('admin_username') || localStorage.getItem('user_name') || 'Ayesha Khan';
    const heroNameEl = document.getElementById('heroAdminName');
    if (heroNameEl && storedName) {
        heroNameEl.textContent = storedName.charAt(0).toUpperCase() + storedName.slice(1);
    }

    // 3. Format Date
    setHeroLiveDate();

    // 4. Initialize Calendar
    initCalendar();

    // 5. Initialize Task Checkboxes
    initTaskCheckboxes();

    // 6. Initialize AI Assistant
    initAiAssistant();

    // 7. Initialize Project Cards
    initProjectCards();

    // 8. Fetch Live Dashboard Data
    loadLiveDashboardData();

    // 9. Load Stored Quick Action items (Tasks, Projects, Events)
    initStoredDashboardData();

    // 10. Initialize Drag & Drop for file uploads
    initUploadDropzone();
});

/**
 * Format and set the live hero date
 */
function setHeroLiveDate() {
    const now = new Date();
    // Default to matching reference date format "Tue, 26 Aug 2026"
    const options = { weekday: 'short', day: 'numeric', month: 'short', year: 'numeric' };
    const dateStr = now.toLocaleDateString('en-GB', options);
    const heroLiveDate = document.getElementById('heroLiveDate');
    if (heroLiveDate) {
        heroLiveDate.textContent = dateStr;
    }
}

/**
 * Fetch and bind live metrics from backend API
 */
async function loadLiveDashboardData() {
    try {
        const response = await fetch(`${DASHBOARD_API}/api/dashboard`, {
            headers: {
                Authorization: `Bearer ${dashToken()}`,
                Accept: 'application/json'
            }
        });

        if (response.status === 401) {
            localStorage.removeItem('auth_token');
            localStorage.removeItem('token');
            window.location.href = 'login.php';
            return;
        }

        const result = await response.json();
        if (result.success && result.data) {
            const summary = result.data.summary || {};
            
            // Total Tasks / Employees
            const kpiEmployees = document.getElementById('dashKpiEmployees');
            if (kpiEmployees && summary.total_employees !== undefined) {
                kpiEmployees.textContent = summary.total_employees || '12';
            }

            // Projects / Departments
            const kpiProjects = document.getElementById('dashKpiProjects');
            if (kpiProjects && summary.total_departments !== undefined) {
                kpiProjects.textContent = summary.total_departments || '4';
            }

            // Meetings / On Leave
            const kpiMeetings = document.getElementById('dashKpiMeetings');
            if (kpiMeetings && summary.on_leave_employees !== undefined) {
                kpiMeetings.textContent = summary.on_leave_employees || '3';
            }

            // Hours Tracked / Active employees
            const kpiHours = document.getElementById('dashKpiHours');
            if (kpiHours && summary.active_employees !== undefined) {
                // if active employees exists, format realistic hours or rate
                kpiHours.textContent = (summary.active_employees * 0.75).toFixed(1);
            }

            // Populate recent activity if available
            if (Array.isArray(result.data.recent_employees) && result.data.recent_employees.length > 0) {
                renderLiveActivities(result.data.recent_employees);
            }
        }
    } catch (e) {
        console.warn('Using enriched dashboard preset metrics:', e.message);
    }
}

/**
 * Render activity items from real employees
 */
function renderLiveActivities(employees) {
    const list = document.getElementById('teamActivityList');
    if (!list || !employees.length) return;

    const actions = [
        'commented on Project UI/UX',
        'completed attendance check-in',
        'joined the engineering team',
        'updated monthly milestone',
        'submitted leave request'
    ];

    const avColors = ['av-1', 'av-2', 'av-3', 'av-4'];

    const html = employees.slice(0, 3).map((emp, i) => {
        const initials = (emp.full_name || 'User').split(' ').map(x => x[0]).slice(0, 2).join('').toUpperCase();
        const action = actions[i % actions.length];
        const timeAgo = `${i + 2} hours ago`;
        const colorClass = avColors[i % avColors.length];

        return `
            <div class="activity-item">
                <span class="activity-av ${colorClass}">${initials}</span>
                <div class="activity-meta">
                    <p><strong>${escapeHtml(emp.full_name)}</strong> ${action}</p>
                    <small>${timeAgo}</small>
                </div>
            </div>
        `;
    }).join('');

    list.innerHTML = html;
}

/**
 * Initialize Interactive Calendar
 */
function initCalendar() {
    const monthNames = [
        'January', 'February', 'March', 'April', 'May', 'June',
        'July', 'August', 'September', 'October', 'November', 'December'
    ];

    const monthTitle = document.getElementById('calMonthTitle');
    const daysBody = document.getElementById('calDaysBody');
    const prevBtn = document.getElementById('calPrevBtn');
    const nextBtn = document.getElementById('calNextBtn');

    function renderCal() {
        if (!daysBody || !monthTitle) return;

        monthTitle.textContent = `${monthNames[calCurrentMonth]} ${calCurrentYear}`;

        // Days in current month
        const firstDayIndex = new Date(calCurrentYear, calCurrentMonth, 1).getDay();
        const lastDate = new Date(calCurrentYear, calCurrentMonth + 1, 0).getDate();
        const prevLastDate = new Date(calCurrentYear, calCurrentMonth, 0).getDate();

        let daysHtml = '';

        // Previous month trailing days
        for (let x = firstDayIndex; x > 0; x--) {
            const dayNum = prevLastDate - x + 1;
            daysHtml += `<div class="cal-day-cell other-month">${dayNum}</div>`;
        }

        // Current month days
        // Days with scheduled event dots matching reference (e.g. 5, 12, 18, 22, 26, 28)
        const eventDays = [5, 8, 12, 17, 18, 22, 26, 28];

        for (let i = 1; i <= lastDate; i++) {
            const isActive = (i === calActiveDay && calCurrentMonth === 7 && calCurrentYear === 2026);
            const hasDot = eventDays.includes(i);

            daysHtml += `
                <div class="cal-day-cell ${isActive ? 'active-day' : ''}" data-day="${i}">
                    ${i}
                    ${hasDot ? '<span class="cal-event-dot"></span>' : ''}
                </div>
            `;
        }

        // Next month leading days to complete grid
        const totalCells = firstDayIndex + lastDate;
        const nextDays = (7 - (totalCells % 7)) % 7;
        for (let j = 1; j <= nextDays; j++) {
            daysHtml += `<div class="cal-day-cell other-month">${j}</div>`;
        }

        daysBody.innerHTML = daysHtml;

        // Day click listeners
        daysBody.querySelectorAll('.cal-day-cell:not(.other-month)').forEach(cell => {
            cell.onclick = () => {
                daysBody.querySelectorAll('.cal-day-cell').forEach(c => c.classList.remove('active-day'));
                cell.classList.add('active-day');
                calActiveDay = parseInt(cell.dataset.day, 10);
            };
        });
    }

    if (prevBtn) {
        prevBtn.onclick = () => {
            calCurrentMonth--;
            if (calCurrentMonth < 0) {
                calCurrentMonth = 11;
                calCurrentYear--;
            }
            renderCal();
        };
    }

    if (nextBtn) {
        nextBtn.onclick = () => {
            calCurrentMonth++;
            if (calCurrentMonth > 11) {
                calCurrentMonth = 0;
                calCurrentYear++;
            }
            renderCal();
        };
    }

    renderCal();
}

/**
 * Interactive Task Completion Flow (Detail Modal -> Confirmation -> Removal)
 */
let currentTaskPendingCompletion = null;

function initTaskCheckboxes() {
    const list = document.getElementById('upcomingTasksList');
    if (!list) return;

    // Delegated click on the task checkbox or its wrapper
    list.addEventListener('click', (e) => {
        const checkWrap = e.target.closest('.task-check-wrap');
        if (!checkWrap) return;

        // Prevent immediate toggle of checkbox check state
        e.preventDefault();

        const row = checkWrap.closest('.task-row');
        if (!row) return;

        const checkbox = row.querySelector('.task-checkbox');
        openTaskCompletionModal(row, checkbox);
    });
}

function openTaskCompletionModal(row, checkbox) {
    if (!row) return;

    const taskId = row.getAttribute('data-task-id') || ('task_static_' + Math.random().toString(36).substr(2, 7));
    const title = row.querySelector('.task-title')?.textContent?.trim() || 'Untitled Task';
    const time = row.querySelector('.task-time')?.textContent?.trim() || 'Today • Pending';
    const priorityEl = row.querySelector('.task-priority');
    const priority = priorityEl?.textContent?.trim() || 'Medium';
    const priorityClass = priorityEl ? (Array.from(priorityEl.classList).find(c => c.startsWith('priority-')) || 'priority-med') : 'priority-med';
    const assigneeEl = row.querySelector('.task-assignee');
    const assigneeInitials = assigneeEl?.textContent?.trim() || 'SA';
    const assigneeName = row.getAttribute('data-assignee-name') || assigneeEl?.getAttribute('title') || 'Team Member';
    const assigneeAvClass = assigneeEl ? (Array.from(assigneeEl.classList).find(c => c.startsWith('av-')) || 'av-1') : 'av-1';

    currentTaskPendingCompletion = {
        row,
        checkbox,
        taskId,
        title,
        time,
        priority,
        priorityClass,
        assigneeInitials,
        assigneeName,
        assigneeAvClass
    };

    // Populate Modal Step 1 (Task Details)
    const titleEl = document.getElementById('taskModalTitle');
    if (titleEl) titleEl.textContent = title;

    const timeEl = document.getElementById('taskModalTime');
    if (timeEl) timeEl.textContent = time;

    const priBadge = document.getElementById('taskModalPriority');
    if (priBadge) {
        priBadge.textContent = priority;
        priBadge.className = `task-priority ${priorityClass}`;
    }

    const avBadge = document.getElementById('taskModalAssigneeAv');
    if (avBadge) {
        avBadge.textContent = assigneeInitials;
        avBadge.className = `task-assignee ${assigneeAvClass}`;
        avBadge.setAttribute('title', assigneeName);
    }

    const nameEl = document.getElementById('taskModalAssigneeName');
    if (nameEl) nameEl.textContent = assigneeName;

    // Populate Modal Step 2 (Confirmation)
    const confirmNameEl = document.getElementById('taskConfirmTaskName');
    if (confirmNameEl) confirmNameEl.textContent = `"${title}"`;

    // Always reset to step 1 (Task Details) first
    showTaskDetailStep(1);

    // Open Modal
    const modal = document.getElementById('taskDetailModal');
    if (modal) {
        modal.classList.add('active');
    }
}

function showTaskDetailStep(step) {
    const step1 = document.getElementById('taskDetailStep1');
    const step2 = document.getElementById('taskDetailStep2');
    const headerTitle = document.getElementById('taskDetailModalTitle');
    const headerIcon = document.getElementById('taskModalHeaderIcon');

    if (step === 1) {
        if (step1) step1.style.display = 'block';
        if (step2) step2.style.display = 'none';
        if (headerTitle) headerTitle.textContent = 'Task Details';
        if (headerIcon) headerIcon.textContent = '📋';
    } else {
        if (step1) step1.style.display = 'none';
        if (step2) step2.style.display = 'block';
        if (headerTitle) headerTitle.textContent = 'Confirm Completion';
        if (headerIcon) headerIcon.textContent = '⚠️';
    }
}

window.proceedToTaskConfirm = function() {
    showTaskDetailStep(2);
};

window.backToTaskDetail = function() {
    showTaskDetailStep(1);
};

window.closeTaskDetailModal = function() {
    const modal = document.getElementById('taskDetailModal');
    if (modal) {
        modal.classList.remove('active');
    }
    if (currentTaskPendingCompletion && currentTaskPendingCompletion.checkbox) {
        currentTaskPendingCompletion.checkbox.checked = false;
    }
    currentTaskPendingCompletion = null;
    showTaskDetailStep(1);
};

window.confirmTaskCompletion = function() {
    if (!currentTaskPendingCompletion) return;

    const { row, checkbox, taskId } = currentTaskPendingCompletion;

    if (checkbox) {
        checkbox.checked = true;
    }

    // Close the modal
    const modal = document.getElementById('taskDetailModal');
    if (modal) {
        modal.classList.remove('active');
    }

    // Play smooth leave animation and remove from DOM
    if (row) {
        row.classList.add('task-completing-out');
        setTimeout(() => {
            row.remove();
            checkUpcomingTasksEmpty();
        }, 360);
    }

    // Persist removal:
    // 1. Remove from dynamic tasks stored in localStorage
    const stored = JSON.parse(localStorage.getItem('admin_upcoming_tasks') || '[]');
    const updated = stored.filter(t => t.id !== taskId);
    localStorage.setItem('admin_upcoming_tasks', JSON.stringify(updated));

    // 2. Add to completed tasks IDs so static tasks also stay removed across reload
    const completedIds = JSON.parse(localStorage.getItem('admin_completed_task_ids') || '[]');
    if (!completedIds.includes(taskId)) {
        completedIds.push(taskId);
        localStorage.setItem('admin_completed_task_ids', JSON.stringify(completedIds));
    }

    // 3. Remove from synced HR tasks if present
    const hrTasks = JSON.parse(localStorage.getItem('hr_synced_tasks') || '[]');
    const updatedHr = hrTasks.filter(t => t.id !== taskId);
    localStorage.setItem('hr_synced_tasks', JSON.stringify(updatedHr));

    // 4. Update KPI tasks counter
    const kpiTasks = document.getElementById('dashKpiEmployees');
    if (kpiTasks) {
        const currentCount = parseInt(kpiTasks.textContent, 10) || 0;
        if (currentCount > 0) kpiTasks.textContent = currentCount - 1;
    }

    // 5. Toast feedback
    showDashToast('Task completed and removed from list! ✨', '✓');

    currentTaskPendingCompletion = null;
    showTaskDetailStep(1);
};

function checkUpcomingTasksEmpty() {
    const list = document.getElementById('upcomingTasksList');
    if (!list) return;

    const remainingRows = list.querySelectorAll('.task-row');
    let emptyMsg = list.querySelector('.tasks-empty-placeholder');

    if (remainingRows.length === 0) {
        if (!emptyMsg) {
            emptyMsg = document.createElement('div');
            emptyMsg.className = 'tasks-empty-placeholder';
            emptyMsg.innerHTML = `
                <div class="tasks-empty-icon">🎉</div>
                <div class="tasks-empty-text">
                    <strong>All caught up!</strong>
                    <span>No pending tasks remaining</span>
                </div>
            `;
            list.appendChild(emptyMsg);
        }
    } else if (emptyMsg) {
        emptyMsg.remove();
    }
}

/**
 * Interactive Flowora AI Assistant
 */
function initAiAssistant() {
    window.handleAiSend = () => {
        const input = document.getElementById('aiInput');
        const bubble = document.getElementById('aiChatBubble');
        if (!input || !bubble) return;

        const query = input.value.trim();
        if (!query) return;

        bubble.textContent = `Analyzing "${query}"...`;
        input.value = '';

        setTimeout(() => {
            const replies = [
                "Everything is operating smoothly. All 9 team members have clocked in today!",
                "I've updated the project milestone for Green Market. Deadline is in 5 days.",
                "Here are the attendance statistics: 83.3% rate across all 5 departments.",
                "I've saved that query. Let me know if you need automated report generation!"
            ];
            const reply = replies[Math.floor(Math.random() * replies.length)];
            bubble.textContent = reply;
        }, 800);
    };
}

function escapeHtml(str) {
    return String(str || '').replace(/[&<>'"]/g, tag => ({
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        "'": '&#39;',
        '"': '&quot;'
    }[tag]));
}

/* ==========================================================================
   QUICK ACTION MODALS & DYNAMIC CREATION (TASK, PROJECT, EVENT)
   ========================================================================== */

/**
 * Open/Close Modals
 */
window.openNewTaskModal = function() {
    const modal = document.getElementById('newTaskModal');
    if (modal) {
        modal.classList.add('active');
        setTimeout(() => document.getElementById('taskTitleInput')?.focus(), 50);
    }
};

window.openNewProjectModal = function() {
    const modal = document.getElementById('newProjectModal');
    if (modal) {
        modal.classList.add('active');
        setTimeout(() => document.getElementById('projNameInput')?.focus(), 50);
    }
};

window.openAddEventModal = function() {
    const modal = document.getElementById('newEventModal');
    if (modal) {
        modal.classList.add('active');
        setTimeout(() => document.getElementById('eventTitleInput')?.focus(), 50);
    }
};

window.closeDashModal = function(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.remove('active');
    }
};

// Close modal when clicking on overlay background
document.addEventListener('click', (e) => {
    if (e.target.classList && e.target.classList.contains('dash-modal-overlay')) {
        if (e.target.id === 'taskDetailModal') {
            closeTaskDetailModal();
        } else {
            e.target.classList.remove('active');
        }
    }
});

// Close modal on Escape key
document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') {
        const detailModal = document.getElementById('taskDetailModal');
        if (detailModal && detailModal.classList.contains('active')) {
            closeTaskDetailModal();
        } else {
            document.querySelectorAll('.dash-modal-overlay.active').forEach(m => m.classList.remove('active'));
        }
    }
});

/**
 * Toast Notification Utility
 */
function showDashToast(msg, icon = '✓') {
    const toast = document.getElementById('dashToast');
    const toastMsg = document.getElementById('dashToastMsg');
    const toastIcon = document.getElementById('dashToastIcon');
    if (!toast || !toastMsg) return;

    toastMsg.textContent = msg;
    if (toastIcon) toastIcon.textContent = icon;

    toast.classList.add('show');
    clearTimeout(toast._timer);
    toast._timer = setTimeout(() => {
        toast.classList.remove('show');
    }, 3200);
}

/**
 * Handle New Task Creation
 */
window.handleCreateTask = function(e) {
    e.preventDefault();
    const titleInput = document.getElementById('taskTitleInput');
    const priorityInput = document.getElementById('taskPriorityInput');
    const dueInput = document.getElementById('taskDueInput');
    const assigneeInput = document.getElementById('taskAssigneeInput');
    const assigneeNameInput = document.getElementById('taskAssigneeNameInput');
    const syncHrCheckbox = document.getElementById('taskSyncHrCheckbox');

    const title = titleInput?.value.trim() || 'New Task';
    const priority = priorityInput?.value || 'Medium';
    const due = dueInput?.value.trim() || 'Today • 4:00 PM';
    const initials = (assigneeInput?.value.trim() || 'HR').toUpperCase().slice(0, 3);
    const assigneeName = assigneeNameInput?.value.trim() || 'HR Team';
    const syncToHr = syncHrCheckbox ? syncHrCheckbox.checked : false;

    const taskObj = {
        id: 'task_' + Date.now(),
        title,
        priority,
        due,
        initials,
        assigneeName,
        completed: false,
        syncToHr
    };

    // 1. Render in Upcoming Tasks list (at top)
    renderTaskInList(taskObj, true);

    // 2. Persist in localStorage
    const stored = JSON.parse(localStorage.getItem('admin_upcoming_tasks') || '[]');
    stored.unshift(taskObj);
    localStorage.setItem('admin_upcoming_tasks', JSON.stringify(stored));

    // 3. If synced to HR, also store for HR views
    if (syncToHr) {
        const hrTasks = JSON.parse(localStorage.getItem('hr_synced_tasks') || '[]');
        hrTasks.unshift({
            id: taskObj.id,
            task_name: title,
            assigned_to: assigneeName + ` (${initials})`,
            due_date: due,
            status: 'IN_PROGRESS',
            priority: priority
        });
        localStorage.setItem('hr_synced_tasks', JSON.stringify(hrTasks));
    }

    // 4. Update KPI Pill Counter
    const kpiTasks = document.getElementById('dashKpiEmployees');
    if (kpiTasks) {
        const currentCount = parseInt(kpiTasks.textContent, 10) || 0;
        kpiTasks.textContent = currentCount + 1;
    }

    // 5. Feedback & Reset
    showDashToast(syncToHr ? 'Task added to Upcoming Tasks & synced with HR Portal!' : 'Task added to Upcoming Tasks!');
    titleInput.value = '';
    closeDashModal('newTaskModal');
};

/**
 * Render a single task item in #upcomingTasksList
 */
function renderTaskInList(task, prepend = false) {
    const list = document.getElementById('upcomingTasksList');
    if (!list) return;

    const priorityClass = task.priority.toLowerCase() === 'high' ? 'priority-high' :
                         (task.priority.toLowerCase() === 'low' ? 'priority-low' : 'priority-med');

    const avClasses = ['av-1', 'av-2', 'av-3', 'av-4'];
    const avClass = avClasses[Math.abs(task.title.charCodeAt(0) || 0) % avClasses.length];

    const row = document.createElement('div');
    row.className = `task-row ${task.completed ? 'completed' : ''}`;
    row.setAttribute('data-task-id', task.id);
    row.setAttribute('data-assignee-name', task.assigneeName || task.initials);
    row.innerHTML = `
        <label class="task-check-wrap">
            <input type="checkbox" class="task-checkbox" ${task.completed ? 'checked' : ''}>
            <span class="custom-checkbox"></span>
        </label>
        <div class="task-text-col">
            <strong class="task-title">${escapeHtml(task.title)}</strong>
            <small class="task-time">${escapeHtml(task.due)}</small>
        </div>
        <span class="task-priority ${priorityClass}">${escapeHtml(task.priority)}</span>
        <span class="task-assignee ${avClass}" title="${escapeHtml(task.assigneeName || task.initials)}">${escapeHtml(task.initials)}</span>
    `;

    if (prepend && list.firstChild) {
        list.insertBefore(row, list.firstChild);
    } else {
        list.appendChild(row);
    }
}

function updateStoredTaskStatus(taskId, isCompleted) {
    const stored = JSON.parse(localStorage.getItem('admin_upcoming_tasks') || '[]');
    const item = stored.find(t => t.id === taskId);
    if (item) {
        item.completed = isCompleted;
        localStorage.setItem('admin_upcoming_tasks', JSON.stringify(stored));
    }
}

/**
 * Handle New Project Creation
 */
window.handleCreateProject = function(e) {
    e.preventDefault();
    const nameInput = document.getElementById('projNameInput');
    const subInput = document.getElementById('projSubInput');
    const themeInput = document.getElementById('projThemeInput');
    const progressInput = document.getElementById('projProgressInput');
    const dueInput = document.getElementById('projDueInput');

    const name = nameInput?.value.trim() || 'New Project';
    const sub = subInput?.value.trim() || 'Web Application';
    const theme = themeInput?.value || 'blue';
    const progress = Math.min(100, Math.max(0, parseInt(progressInput?.value, 10) || 20));
    const due = dueInput?.value.trim() || 'Due: 30 Oct';

    const projObj = {
        id: 'proj_' + Date.now(),
        name,
        sub,
        theme,
        progress,
        due
    };

    if (progress >= 100) {
        showDashToast('Project created at 100% (Completed) and moved to archive!');
        nameInput.value = '';
        closeDashModal('newProjectModal');
        return;
    }

    // 1. Render in Recent Projects
    renderProjectInCarousel(projObj, true);

    // 2. Persist in localStorage
    const stored = JSON.parse(localStorage.getItem('admin_recent_projects') || '[]');
    stored.unshift(projObj);
    localStorage.setItem('admin_recent_projects', JSON.stringify(stored));

    // 3. Update KPI Pill Counter
    const kpiProjects = document.getElementById('dashKpiProjects');
    if (kpiProjects) {
        const currentCount = parseInt(kpiProjects.textContent, 10) || 0;
        kpiProjects.textContent = currentCount + 1;
    }

    // 4. Feedback & Reset
    showDashToast('New Project created and added to Recent Projects!');
    nameInput.value = '';
    closeDashModal('newProjectModal');
};

function renderProjectInCarousel(proj, prepend = false) {
    const carousel = document.getElementById('recentProjectsCarousel');
    if (!carousel) return;

    if (parseInt(proj.progress, 10) >= 100) {
        return; // Completed 100% projects are gone from the active table
    }

    const themeClass = `proj-theme-${proj.theme}`;
    const iconClass = `icon-${proj.theme}`;
    const fillClass = `fill-${proj.theme}`;

    const card = document.createElement('div');
    card.className = `mini-project-card ${themeClass}`;
    card.setAttribute('data-proj-id', proj.id);
    card.setAttribute('data-proj-progress', proj.progress);
    card.innerHTML = `
        <div class="proj-top">
            <div class="proj-icon ${iconClass}">
                <svg width="17" height="17" viewBox="0 0 24 24" fill="currentColor"><path d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z"/></svg>
            </div>
            <div>
                <strong class="proj-name">${escapeHtml(proj.name)}</strong>
                <small class="proj-sub">${escapeHtml(proj.sub)}</small>
            </div>
        </div>
        <div class="proj-prog-row">
            <div class="mini-progress-bar"><div class="mini-progress-fill ${fillClass}" style="width: ${proj.progress}%;"></div></div>
            <span class="proj-prog-val">${proj.progress}%</span>
        </div>
        <div class="proj-footer">
            <div class="avatar-stack-tiny">
                <span class="tiny-av av-1">A</span>
                <span class="tiny-av av-2">H</span>
            </div>
            <span class="proj-due">${escapeHtml(proj.due)}</span>
        </div>
    `;

    if (prepend && carousel.firstChild) {
        carousel.insertBefore(card, carousel.firstChild);
    } else {
        carousel.appendChild(card);
    }
}

/**
 * Handle Add Event Creation
 */
window.handleCreateEvent = function(e) {
    e.preventDefault();
    const titleInput = document.getElementById('eventTitleInput');
    const dayInput = document.getElementById('eventDayInput');
    const typeInput = document.getElementById('eventTypeInput');
    const timeInput = document.getElementById('eventTimeInput');

    const title = titleInput?.value.trim() || 'Team Meeting';
    const day = parseInt(dayInput?.value, 10) || 26;
    const type = typeInput?.value || 'Meeting';
    const time = timeInput?.value.trim() || '11:00 AM';

    const eventObj = {
        id: 'event_' + Date.now(),
        title,
        day,
        type,
        time
    };

    // 1. Add dot to calendar day
    addCalendarDotToDay(day);

    // 2. Persist in localStorage
    const stored = JSON.parse(localStorage.getItem('admin_calendar_events') || '[]');
    stored.push(eventObj);
    localStorage.setItem('admin_calendar_events', JSON.stringify(stored));

    // 3. Increment Meetings KPI counter if event is today
    const kpiMeetings = document.getElementById('dashKpiMeetings');
    if (kpiMeetings && day === calActiveDay) {
        const currentCount = parseInt(kpiMeetings.textContent, 10) || 0;
        kpiMeetings.textContent = currentCount + 1;
    }

    // 4. Feedback & Reset
    showDashToast(`Event "${title}" scheduled for Day ${day} on the calendar!`);
    titleInput.value = '';
    closeDashModal('newEventModal');
};

function addCalendarDotToDay(dayNum) {
    const dayCell = document.querySelector(`.cal-day-cell:not(.other-month)[data-day="${dayNum}"]`);
    if (dayCell && !dayCell.querySelector('.cal-event-dot')) {
        const dot = document.createElement('span');
        dot.className = 'cal-event-dot';
        dayCell.appendChild(dot);
    }
}

/**
 * Load and render any saved items from localStorage
 */
function initStoredDashboardData() {
    // 0. Filter out static or dynamic tasks previously marked completed
    const completedIds = JSON.parse(localStorage.getItem('admin_completed_task_ids') || '[]');
    if (Array.isArray(completedIds) && completedIds.length > 0) {
        completedIds.forEach(id => {
            const row = document.querySelector(`.task-row[data-task-id="${id}"]`);
            if (row) row.remove();
        });
    }

    // 1. Render any custom user tasks from localStorage
    const storedTasks = JSON.parse(localStorage.getItem('admin_upcoming_tasks') || '[]');
    if (Array.isArray(storedTasks) && storedTasks.length > 0) {
        storedTasks.slice().reverse().forEach(task => {
            if (!completedIds.includes(task.id)) {
                renderTaskInList(task, true);
            }
        });
        const kpiTasks = document.getElementById('dashKpiEmployees');
        if (kpiTasks) {
            kpiTasks.textContent = (parseInt(kpiTasks.textContent, 10) || 9) + storedTasks.filter(t => !completedIds.includes(t.id)).length;
        }
    }
    checkUpcomingTasksEmpty();

    // 2. Render any custom user projects from localStorage
    const completedProjIds = JSON.parse(localStorage.getItem('admin_completed_project_ids') || '[]');
    const storedProjects = JSON.parse(localStorage.getItem('admin_recent_projects') || '[]');
    if (Array.isArray(storedProjects) && storedProjects.length > 0) {
        storedProjects.slice().reverse().forEach(proj => {
            if (!completedProjIds.includes(proj.id) && parseInt(proj.progress, 10) < 100) {
                renderProjectInCarousel(proj, true);
            }
        });
        const kpiProjects = document.getElementById('dashKpiProjects');
        if (kpiProjects) {
            kpiProjects.textContent = (parseInt(kpiProjects.textContent, 10) || 3) + storedProjects.filter(p => !completedProjIds.includes(p.id) && parseInt(p.progress, 10) < 100).length;
        }
    }
    filterCompletedProjects();

    // 3. Mark any stored calendar events
    const storedEvents = JSON.parse(localStorage.getItem('admin_calendar_events') || '[]');
    if (Array.isArray(storedEvents)) {
        storedEvents.forEach(evt => {
            if (evt.day) addCalendarDotToDay(evt.day);
        });
    }
}

/**
 * -------------------------------------------------------------------------
 * PROJECT CARDS INTERACTIVITY & 100% COMPLETION REMOVAL
 * -------------------------------------------------------------------------
 */
let activeProjectPendingUpdate = null;

function initProjectCards() {
    const carousel = document.getElementById('recentProjectsCarousel');
    if (!carousel) return;

    // Delegated click on any project card to open details / update progress
    carousel.addEventListener('click', (e) => {
        const card = e.target.closest('.mini-project-card');
        if (!card) return;
        openProjectDetailModal(card);
    });

    // Filter out any projects that have progress >= 100
    filterCompletedProjects();
}

function filterCompletedProjects() {
    const completedProjIds = JSON.parse(localStorage.getItem('admin_completed_project_ids') || '[]');
    document.querySelectorAll('.mini-project-card').forEach(card => {
        const id = card.getAttribute('data-proj-id');
        const progVal = parseInt(card.getAttribute('data-proj-progress') || card.querySelector('.proj-prog-val')?.textContent || '0', 10);
        if (completedProjIds.includes(id) || progVal >= 100) {
            card.remove();
        }
    });
    checkProjectsEmpty();
}

function openProjectDetailModal(card) {
    if (!card) return;

    const id = card.getAttribute('data-proj-id') || ('proj_' + Math.random().toString(36).substr(2, 7));
    const name = card.querySelector('.proj-name')?.textContent?.trim() || 'Project';
    const sub = card.querySelector('.proj-sub')?.textContent?.trim() || 'Application';
    const progVal = parseInt(card.getAttribute('data-proj-progress') || card.querySelector('.proj-prog-val')?.textContent || '0', 10);
    const due = card.querySelector('.proj-due')?.textContent?.trim() || 'No due date';

    activeProjectPendingUpdate = { card, id, name, sub, progress: progVal, due };

    document.getElementById('projModalName').textContent = name;
    document.getElementById('projModalSub').textContent = sub;
    document.getElementById('projModalProgVal').textContent = progVal + '%';
    document.getElementById('projModalDue').textContent = due;

    const slider = document.getElementById('projModalProgInput');
    if (slider) {
        slider.value = progVal;
        document.getElementById('projModalProgDisplay').textContent = progVal + '%';
    }

    const modal = document.getElementById('projectDetailModal');
    if (modal) modal.classList.add('active');
}

window.saveProjectProgress = function() {
    if (!activeProjectPendingUpdate) return;

    const slider = document.getElementById('projModalProgInput');
    const newProg = Math.min(100, Math.max(0, parseInt(slider?.value, 10) || 0));

    if (newProg >= 100) {
        completeProject(activeProjectPendingUpdate.id, activeProjectPendingUpdate.card);
        return;
    }

    // Update the card visually
    const { card, id } = activeProjectPendingUpdate;
    card.setAttribute('data-proj-progress', newProg);
    const fillEl = card.querySelector('.mini-progress-fill');
    if (fillEl) fillEl.style.width = newProg + '%';
    const progValEl = card.querySelector('.proj-prog-val');
    if (progValEl) progValEl.textContent = newProg + '%';

    // Update stored projects
    const stored = JSON.parse(localStorage.getItem('admin_recent_projects') || '[]');
    const item = stored.find(p => p.id === id);
    if (item) {
        item.progress = newProg;
        localStorage.setItem('admin_recent_projects', JSON.stringify(stored));
    }

    closeDashModal('projectDetailModal');
    showDashToast(`Project progress updated to ${newProg}%!`, '✓');
    activeProjectPendingUpdate = null;
};

window.markProject100 = function() {
    if (!activeProjectPendingUpdate) return;
    completeProject(activeProjectPendingUpdate.id, activeProjectPendingUpdate.card);
};

function completeProject(projId, card) {
    if (card) {
        card.classList.add('proj-completing-out');
        setTimeout(() => {
            card.remove();
            checkProjectsEmpty();
        }, 360);
    }

    // Persist completed ID so it remains gone across reload
    const completedIds = JSON.parse(localStorage.getItem('admin_completed_project_ids') || '[]');
    if (!completedIds.includes(projId)) {
        completedIds.push(projId);
        localStorage.setItem('admin_completed_project_ids', JSON.stringify(completedIds));
    }

    // Remove from active projects if in admin_recent_projects
    const stored = JSON.parse(localStorage.getItem('admin_recent_projects') || '[]');
    const updated = stored.filter(p => p.id !== projId);
    localStorage.setItem('admin_recent_projects', JSON.stringify(updated));

    // Update KPI projects count
    const kpiProjects = document.getElementById('dashKpiProjects');
    if (kpiProjects) {
        const count = parseInt(kpiProjects.textContent, 10) || 0;
        if (count > 0) kpiProjects.textContent = count - 1;
    }

    closeDashModal('projectDetailModal');
    showDashToast('Project reached 100% and removed from active table! 🎉', '✓');
    activeProjectPendingUpdate = null;
}

function checkProjectsEmpty() {
    const carousel = document.getElementById('recentProjectsCarousel');
    if (!carousel) return;

    const remainingCards = carousel.querySelectorAll('.mini-project-card');
    let emptyEl = carousel.querySelector('.projects-empty-placeholder');

    if (remainingCards.length === 0) {
        if (!emptyEl) {
            emptyEl = document.createElement('div');
            emptyEl.className = 'projects-empty-placeholder';
            emptyEl.textContent = '🎉 All recent projects are 100% completed!';
            carousel.appendChild(emptyEl);
        }
    } else if (emptyEl) {
        emptyEl.remove();
    }
}

/**
 * -------------------------------------------------------------------------
 * QUICK ACTION: UPLOAD FILE MODAL & HANDLERS
 * -------------------------------------------------------------------------
 */

/**
 * -------------------------------------------------------------------------
 * INDEXEDDB STORAGE FOR LARGE FILES (NO LOCALSTORAGE 5MB LIMIT)
 * -------------------------------------------------------------------------
 */
const DB_NAME = 'MindazzleFilesDB';
const DB_STORE = 'uploaded_files';

function openFilesDB() {
    return new Promise((resolve) => {
        if (typeof indexedDB === 'undefined') {
            resolve(null);
            return;
        }
        try {
            const req = indexedDB.open(DB_NAME, 1);
            req.onupgradeneeded = (e) => {
                const db = e.target.result;
                if (!db.objectStoreNames.contains(DB_STORE)) {
                    db.createObjectStore(DB_STORE, { keyPath: 'id' });
                }
            };
            req.onsuccess = () => resolve(req.result);
            req.onerror = () => resolve(null);
            req.onblocked = () => resolve(null);
            setTimeout(() => resolve(null), 1500);
        } catch (e) {
            resolve(null);
        }
    });
}

async function saveFileToIndexedDB(id, fileBlob) {
    try {
        const db = await openFilesDB();
        if (!db) return false;
        return new Promise((res) => {
            try {
                const tx = db.transaction(DB_STORE, 'readwrite');
                tx.oncomplete = () => res(true);
                tx.onerror = () => res(false);
                tx.onabort = () => res(false);
                tx.objectStore(DB_STORE).put({ id, file: fileBlob });
            } catch (err) {
                res(false);
            }
        });
    } catch (e) {
        return false;
    }
}

async function getFileFromIndexedDB(id) {
    try {
        const db = await openFilesDB();
        if (!db) return null;
        return new Promise((res) => {
            try {
                const tx = db.transaction(DB_STORE, 'readonly');
                const req = tx.objectStore(DB_STORE).get(id);
                req.onsuccess = () => res(req.result ? req.result.file : null);
                req.onerror = () => res(null);
            } catch (err) {
                res(null);
            }
        });
    } catch (e) {
        return null;
    }
}

async function deleteFileFromIndexedDB(id) {
    try {
        const db = await openFilesDB();
        if (!db) return;
        const tx = db.transaction(DB_STORE, 'readwrite');
        tx.objectStore(DB_STORE).delete(id);
    } catch (e) {}
}

/**
 * -------------------------------------------------------------------------
 * QUICK ACTION: UPLOAD FILE MODAL & HANDLERS
 * -------------------------------------------------------------------------
 */
let selectedUploadFile = null;

function displaySelectedFileInfo(file) {
    if (!file) return;
    const fileInfo = document.getElementById('selectedFileInfo');
    const nameEl = document.getElementById('selectedFileName');
    const sizeEl = document.getElementById('selectedFileSize');

    if (fileInfo && nameEl && sizeEl) {
        nameEl.textContent = file.name;
        const kb = (file.size / 1024).toFixed(1);
        sizeEl.textContent = kb > 1024 ? (kb / 1024).toFixed(1) + ' MB' : kb + ' KB';
        fileInfo.style.display = 'inline-flex';
    }
}

function initUploadDropzone() {
    const dropzone = document.getElementById('dashUploadDropzone');
    const fileInput = document.getElementById('dashFileInput');
    if (!dropzone) return;

    ['dragenter', 'dragover'].forEach(name => {
        dropzone.addEventListener(name, (e) => {
            e.preventDefault();
            e.stopPropagation();
            dropzone.classList.add('drag-active');
        }, false);
    });

    ['dragleave', 'drop'].forEach(name => {
        dropzone.addEventListener(name, (e) => {
            e.preventDefault();
            e.stopPropagation();
            dropzone.classList.remove('drag-active');
        }, false);
    });

    dropzone.addEventListener('drop', (e) => {
        e.preventDefault();
        e.stopPropagation();
        if (e.dataTransfer && e.dataTransfer.files && e.dataTransfer.files.length > 0) {
            const file = e.dataTransfer.files[0];
            selectedUploadFile = file;
            displaySelectedFileInfo(file);
        }
    }, false);
}

window.openUploadFileModal = function() {
    selectedUploadFile = null;
    const fileInfo = document.getElementById('selectedFileInfo');
    if (fileInfo) fileInfo.style.display = 'none';
    const fileInput = document.getElementById('dashFileInput');
    if (fileInput) fileInput.value = '';
    const progressWrap = document.getElementById('uploadProgressBarWrap');
    if (progressWrap) progressWrap.style.display = 'none';
    const progressFill = document.getElementById('uploadProgressFill');
    if (progressFill) progressFill.style.width = '0%';
    const progressPct = document.getElementById('uploadProgressPct');
    if (progressPct) progressPct.textContent = '0%';
    const submitBtn = document.getElementById('btnUploadSubmit');
    if (submitBtn) {
        submitBtn.disabled = false;
        submitBtn.textContent = 'Upload File';
    }
    const notesInput = document.getElementById('uploadNotesInput');
    if (notesInput) notesInput.value = '';

    const modal = document.getElementById('uploadFileModal');
    if (modal) modal.classList.add('active');
};

window.handleFileSelected = function(e) {
    const file = e.target.files && e.target.files[0];
    if (!file) return;
    selectedUploadFile = file;
    displaySelectedFileInfo(file);
};

window.handleUploadFile = async function(e) {
    if (e && e.preventDefault) e.preventDefault();

    const fileInput = document.getElementById('dashFileInput');
    const file = selectedUploadFile || (fileInput?.files && fileInput?.files[0]);
    if (!file) {
        showDashToast('Please select a file to upload first.', '⚠️');
        return;
    }

    const category = document.getElementById('uploadCategorySelect')?.value || 'General';
    const notes = document.getElementById('uploadNotesInput')?.value.trim() || '';

    const progressWrap = document.getElementById('uploadProgressBarWrap');
    const progressFill = document.getElementById('uploadProgressFill');
    const progressPct = document.getElementById('uploadProgressPct');
    const submitBtn = document.getElementById('btnUploadSubmit');

    if (progressWrap) progressWrap.style.display = 'block';
    if (submitBtn) {
        submitBtn.disabled = true;
        submitBtn.textContent = 'Uploading...';
    }

    const kb = (file.size / 1024).toFixed(1);
    const sizeFormatted = kb > 1024 ? (kb / 1024).toFixed(1) + ' MB' : kb + ' KB';

    const now = new Date();
    const dateFormatted = now.toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' }) + ', ' + now.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit', hour12: true });

    const fileId = 'file_' + Date.now();

    try {
        // Step 1: Smooth visual progress
        if (progressFill) progressFill.style.width = '35%';
        if (progressPct) progressPct.textContent = '35%';
        await new Promise(r => setTimeout(r, 90));

        if (progressFill) progressFill.style.width = '75%';
        if (progressPct) progressPct.textContent = '75%';
        await new Promise(r => setTimeout(r, 110));

        // Step 2: Store actual binary blob in IndexedDB (timeout protected)
        try {
            await Promise.race([
                saveFileToIndexedDB(fileId, file),
                new Promise(r => setTimeout(() => r(false), 2000))
            ]);
        } catch (dbErr) {
            console.warn('IndexedDB save notice:', dbErr);
        }

        // Step 3: Build metadata record
        const fileRecord = {
            id: fileId,
            name: file.name,
            size: file.size,
            sizeFormatted: sizeFormatted,
            type: file.type || 'application/octet-stream',
            category: category,
            notes: notes,
            uploaded_at: now.toISOString(),
            uploaded_date: dateFormatted
        };

        // Step 4: Safely save metadata in localStorage, cleaning any bloated legacy items
        try {
            let stored = JSON.parse(localStorage.getItem('admin_uploaded_files') || '[]');
            if (!Array.isArray(stored)) stored = [];
            // Remove any legacy huge base64 strings if present from previous runs
            stored = stored.map(item => {
                if (item && item.dataUrl) {
                    const { dataUrl, ...rest } = item;
                    return rest;
                }
                return item;
            });
            stored.unshift(fileRecord);
            if (stored.length > 50) stored = stored.slice(0, 50);
            localStorage.setItem('admin_uploaded_files', JSON.stringify(stored));
        } catch (storageErr) {
            console.warn('LocalStorage notice:', storageErr);
            try {
                localStorage.setItem('admin_uploaded_files', JSON.stringify([fileRecord]));
            } catch (e2) {}
        }

        // Step 5: Optional backend sync
        if (typeof FormData !== 'undefined') {
            try {
                const formData = new FormData();
                formData.append('document', file);
                formData.append('document_type', category);
                fetch(`${DASHBOARD_API}/api/employees/upload-document`, {
                    method: 'POST',
                    headers: { Authorization: `Bearer ${dashToken()}` },
                    body: formData
                }).catch(() => {});
            } catch (apiErr) {}
        }

        // Step 6: Complete progress bar
        if (progressFill) progressFill.style.width = '100%';
        if (progressPct) progressPct.textContent = '100%';
        await new Promise(r => setTimeout(r, 180));

    } catch (err) {
        console.error('File upload error:', err);
    } finally {
        if (submitBtn) {
            submitBtn.disabled = false;
            submitBtn.textContent = 'Upload File';
        }

        closeDashModal('uploadFileModal');
        showDashToast(`"${file.name}" saved & uploaded to ${category}!`, '✓');

        // Reset state
        selectedUploadFile = null;
        if (fileInput) fileInput.value = '';
        const fileInfo = document.getElementById('selectedFileInfo');
        if (fileInfo) fileInfo.style.display = 'none';
        if (progressWrap) progressWrap.style.display = 'none';
        if (progressFill) progressFill.style.width = '0%';
        if (progressPct) progressPct.textContent = '0%';
        const notesInput = document.getElementById('uploadNotesInput');
        if (notesInput) notesInput.value = '';

        renderUploadedFilesList();
    }
};

/**
 * -------------------------------------------------------------------------
 * UPLOADED FILES LIST MODAL & ACTIONS
 * -------------------------------------------------------------------------
 */
function getInitialSampleFiles() {
    return [
        {
            id: 'file_sample_1',
            name: 'Company_Employee_Handbook_2026.pdf',
            size: 420000,
            sizeFormatted: '410 KB',
            type: 'application/pdf',
            category: 'Policies',
            notes: 'Official enterprise HR regulations and compliance',
            uploaded_at: new Date(Date.now() - 3600000 * 2).toISOString(),
            uploaded_date: 'Today, 02:30 PM'
        },
        {
            id: 'file_sample_2',
            name: 'Q3_Department_Performance_Audit.xlsx',
            size: 185000,
            sizeFormatted: '180 KB',
            type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            category: 'Reports',
            notes: 'Quarterly team KPI review report',
            uploaded_at: new Date(Date.now() - 3600000 * 24).toISOString(),
            uploaded_date: 'Yesterday, 11:15 AM'
        }
    ];
}

window.openUploadedFilesModal = function() {
    renderUploadedFilesList();
    const modal = document.getElementById('uploadedFilesModal');
    if (modal) modal.classList.add('active');
};

window.filterUploadedFilesList = function() {
    renderUploadedFilesList();
};

function renderUploadedFilesList() {
    const container = document.getElementById('uploadedFilesContainer');
    const badge = document.getElementById('uploadedFilesCountBadge');
    if (!container) return;

    let files = JSON.parse(localStorage.getItem('admin_uploaded_files') || 'null');
    if (!files) {
        files = getInitialSampleFiles();
        localStorage.setItem('admin_uploaded_files', JSON.stringify(files));
    }

    const searchQuery = (document.getElementById('filesSearchInput')?.value || '').trim().toLowerCase();
    const categoryFilter = document.getElementById('filesCategoryFilter')?.value || '';

    const filtered = files.filter(f => {
        const matchesSearch = !searchQuery || f.name.toLowerCase().includes(searchQuery) || (f.notes && f.notes.toLowerCase().includes(searchQuery));
        const matchesCategory = !categoryFilter || f.category.toLowerCase() === categoryFilter.toLowerCase();
        return matchesSearch && matchesCategory;
    });

    if (badge) {
        badge.textContent = `${filtered.length} file${filtered.length === 1 ? '' : 's'}`;
    }

    if (filtered.length === 0) {
        container.innerHTML = `
            <div class="files-empty-state">
                <div class="files-empty-icon">📁</div>
                <h4>No uploaded files found</h4>
                <p>Upload documents, guidelines, or spreadsheets using the Upload button.</p>
                <button type="button" class="dash-btn-submit" onclick="closeDashModal('uploadedFilesModal'); openUploadFileModal();" style="margin-top: 12px;">
                    Upload File Now
                </button>
            </div>
        `;
        return;
    }

    container.innerHTML = filtered.map(f => {
        const ext = (f.name.split('.').pop() || '').toLowerCase();
        let iconEmoji = '📄';
        let iconClass = 'file-icon-teal';

        if (['pdf'].includes(ext)) {
            iconEmoji = '📕';
            iconClass = 'file-icon-red';
        } else if (['xls', 'xlsx', 'csv'].includes(ext)) {
            iconEmoji = '📊';
            iconClass = 'file-icon-green';
        } else if (['doc', 'docx', 'txt', 'rtf'].includes(ext)) {
            iconEmoji = '📝';
            iconClass = 'file-icon-blue';
        } else if (['png', 'jpg', 'jpeg', 'svg', 'gif', 'webp'].includes(ext)) {
            iconEmoji = '🖼️';
            iconClass = 'file-icon-purple';
        }

        const tagClass = `tag-${(f.category || 'general').toLowerCase()}`;

        return `
            <div class="file-item-card" data-file-id="${f.id}">
                <div class="file-icon-box ${iconClass}">
                    ${iconEmoji}
                </div>
                <div class="file-info-col">
                    <strong class="file-item-name" title="${escapeHtml(f.name)}">${escapeHtml(f.name)}</strong>
                    <div class="file-meta-row">
                        <span class="file-category-tag ${tagClass}">${escapeHtml(f.category)}</span>
                        <span class="file-size-txt">${escapeHtml(f.sizeFormatted || '120 KB')}</span>
                        <span class="file-date-txt">• ${escapeHtml(f.uploaded_date || 'Today')}</span>
                        ${f.notes ? `<span class="file-notes-txt" title="${escapeHtml(f.notes)}">• "${escapeHtml(f.notes)}"</span>` : ''}
                    </div>
                </div>
                <div class="file-actions-col">
                    <button type="button" class="file-action-btn btn-file-download" onclick="downloadUploadedFile('${f.id}')" title="Download file">
                        📥 Download
                    </button>
                    <button type="button" class="file-action-btn btn-file-delete" onclick="deleteUploadedFile('${f.id}')" title="Delete file">
                        🗑️
                    </button>
                </div>
            </div>
        `;
    }).join('');
}

window.downloadUploadedFile = async function(fileId) {
    try {
        const stored = JSON.parse(localStorage.getItem('admin_uploaded_files') || '[]');
        const fileMeta = stored.find(f => f.id === fileId);
        const fileName = fileMeta ? fileMeta.name : 'downloaded_file';

        // 1. Try to get original blob from IndexedDB
        const blob = await getFileFromIndexedDB(fileId);
        if (blob) {
            const url = URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = fileName;
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
            setTimeout(() => URL.revokeObjectURL(url), 1000);
            showDashToast(`Downloading "${fileName}"...`, '📥');
            return;
        }

        // 2. Fallback to simulated document
        downloadSimulatedFile(fileName, fileMeta ? fileMeta.category : 'General');
    } catch (err) {
        showDashToast('Could not download file.', '⚠️');
    }
};

window.deleteUploadedFile = async function(fileId) {
    if (!confirm('Are you sure you want to remove this uploaded file?')) return;

    let stored = JSON.parse(localStorage.getItem('admin_uploaded_files') || '[]');
    stored = stored.filter(f => f.id !== fileId);
    localStorage.setItem('admin_uploaded_files', JSON.stringify(stored));

    await deleteFileFromIndexedDB(fileId);

    showDashToast('File deleted successfully.', '✓');
    renderUploadedFilesList();
};

window.downloadSimulatedFile = function(fileName, category) {
    const content = `Mindazzle Enterprise Document\n\nFile Name: ${fileName}\nCategory: ${category}\nExported on: ${new Date().toLocaleString()}\nStatus: Verified system file`;
    const blob = new Blob([content], { type: 'text/plain;charset=utf-8' });
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = fileName.endsWith('.txt') ? fileName : (fileName.replace(/\.[^.]+$/, '') + '.txt');
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    URL.revokeObjectURL(url);
    showDashToast(`Downloading ${fileName}...`, '📥');
};

/**
 * -------------------------------------------------------------------------
 * QUICK ACTION: TIMER WIDGET & STATE
 * -------------------------------------------------------------------------
 */
let timerSeconds = 0;
let timerInterval = null;
let timerRunning = false;
let timerCurrentTask = 'General Work';

window.openTimerModal = function() {
    updateTimerDisplay();
    const modal = document.getElementById('timerModal');
    if (modal) modal.classList.add('active');
};

function formatTimerTime(totalSec) {
    const hrs = String(Math.floor(totalSec / 3600)).padStart(2, '0');
    const mins = String(Math.floor((totalSec % 3600) / 60)).padStart(2, '0');
    const secs = String(totalSec % 60).padStart(2, '0');
    return `${hrs}:${mins}:${secs}`;
}

function updateTimerDisplay() {
    const display = document.getElementById('timerDigitsDisplay');
    if (display) display.textContent = formatTimerTime(timerSeconds);

    const badge = document.getElementById('timerStatusBadge');
    if (badge) {
        if (timerRunning) {
            badge.textContent = 'RUNNING';
            badge.className = 'timer-status-badge running';
        } else if (timerSeconds > 0) {
            badge.textContent = 'PAUSED';
            badge.className = 'timer-status-badge paused';
        } else {
            badge.textContent = 'IDLE';
            badge.className = 'timer-status-badge';
        }
    }

    const startBtnText = document.getElementById('timerStartBtnText');
    const startBtnIcon = document.getElementById('timerStartBtnIcon');
    const startBtn = document.getElementById('timerStartBtn');
    if (startBtnText && startBtnIcon && startBtn) {
        if (timerRunning) {
            startBtnText.textContent = 'Pause';
            startBtnIcon.textContent = '⏸';
            startBtn.classList.add('running');
        } else {
            startBtnText.textContent = timerSeconds > 0 ? 'Resume' : 'Start Timer';
            startBtnIcon.textContent = '▶';
            startBtn.classList.remove('running');
        }
    }

    // Update Quick Action Tile in dashboard
    const qaLabel = document.getElementById('quickActionTimerLabel');
    if (qaLabel) {
        if (timerRunning) {
            qaLabel.textContent = `● ${formatTimerTime(timerSeconds)}`;
        } else if (timerSeconds > 0) {
            qaLabel.textContent = `⏸ ${formatTimerTime(timerSeconds)}`;
        } else {
            qaLabel.textContent = 'Start Timer';
        }
    }
}

window.toggleTimer = function() {
    if (timerRunning) {
        clearInterval(timerInterval);
        timerRunning = false;
        updateTimerDisplay();
    } else {
        timerRunning = true;
        updateTimerDisplay();
        timerInterval = setInterval(() => {
            timerSeconds++;
            updateTimerDisplay();
        }, 1000);
    }
};

window.resetTimer = function() {
    if (timerInterval) clearInterval(timerInterval);
    timerRunning = false;
    timerSeconds = 0;
    updateTimerDisplay();
};

window.handleTimerTaskChange = function(task) {
    timerCurrentTask = task;
    const label = document.getElementById('timerTaskLabel');
    if (label) label.textContent = 'Tracking: ' + task;
};

window.saveTimerSession = function() {
    if (timerSeconds === 0) {
        showDashToast('No elapsed time to log yet.', 'ℹ️');
        return;
    }

    const formatted = formatTimerTime(timerSeconds);
    const sessions = JSON.parse(localStorage.getItem('admin_logged_timer_sessions') || '[]');
    sessions.unshift({
        id: 'timer_' + Date.now(),
        task: timerCurrentTask,
        durationSeconds: timerSeconds,
        durationFormatted: formatted,
        date: new Date().toISOString()
    });
    localStorage.setItem('admin_logged_timer_sessions', JSON.stringify(sessions));

    showDashToast(`Logged ${formatted} for ${timerCurrentTask}!`, '✓');
    resetTimer();
    closeDashModal('timerModal');
};
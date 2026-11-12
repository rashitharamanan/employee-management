'use strict';

const HR_EMPLOYEE_API = 'http://localhost/employee-qr-system/backend/public';
const employee$ = id => document.getElementById(id);
const employeeToken = () => localStorage.getItem('auth_token') || localStorage.getItem('token') || '';
const employeeEscape = value => String(value ?? '').replace(/[&<>"']/g, char => ({
    '&': '&amp;',
    '<': '&lt;',
    '>': '&gt;',
    '"': '&quot;',
    "'": '&#39;'
}[char]));

let employees = [];
let currentPage = 1;
let totalPages = 1;
let perPage = 10;
let currentView = 'table'; // 'table' or 'directory'

function formatEmpType(type) {
    if (!type) return '';
    return type.replace(/_/g, ' ').toLowerCase().replace(/\b\w/g, l => l.toUpperCase());
}

function showProfile() {
    let user = {};
    try {
        user = JSON.parse(localStorage.getItem('auth_user') || '{}');
    } catch (_) {}
    const name = user.username || user.full_name || 'HR Manager';
    if (employee$('adminUsername')) employee$('adminUsername').textContent = name;
    if (employee$('adminRole')) employee$('adminRole').textContent = user.role || 'HR Department';
    if (employee$('adminAvatar')) employee$('adminAvatar').textContent = name.charAt(0).toUpperCase();
}

function switchView(view) {
    currentView = view;
    const tabTable = employee$('tabAllEmployees');
    const tabDir = employee$('tabEmployeeDirectory');
    const tableWrap = employee$('employeeTableWrap');
    const dirWrap = employee$('employeeDirectoryWrap');

    if (view === 'directory') {
        if (tabTable) tabTable.classList.remove('active');
        if (tabDir) tabDir.classList.add('active');
        if (tableWrap) tableWrap.style.display = 'none';
        if (dirWrap) dirWrap.style.display = 'grid';
    } else {
        if (tabTable) tabTable.classList.add('active');
        if (tabDir) tabDir.classList.remove('active');
        if (tableWrap) tableWrap.style.display = 'block';
        if (dirWrap) dirWrap.style.display = 'none';
    }
}

function renderDirectoryGrid(rows) {
    const dirWrap = employee$('employeeDirectoryWrap');
    if (!dirWrap) return;

    if (!rows.length) {
        dirWrap.innerHTML = `
            <div class="dir-empty-state">
                <svg width="44" height="44" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="11" cy="11" r="8"></circle>
                    <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                </svg>
                <strong>No employees found</strong>
                <p>Try adjusting your search query or filter criteria.</p>
            </div>
        `;
        return;
    }

    dirWrap.innerHTML = rows.map(employee => {
        const rawStatus = String(employee.status || 'ACTIVE').toUpperCase();
        const statusClass = rawStatus.toLowerCase().replace(/\s+/g, '_');
        const initial = employeeEscape((employee.full_name || '?').charAt(0).toUpperCase());
        const empType = formatEmpType(employee.employment_type);
        const photoUrl = employee.profile_photo ? (employee.profile_photo.startsWith('http') ? employee.profile_photo : `${HR_EMPLOYEE_API}/${employee.profile_photo.replace(/^\/+/, '')}`) : '';

        return `
            <div class="dir-card" data-id="${employee.id}">
                <div class="dir-card-header">
                    <span class="employee-code">${employeeEscape(employee.employee_code || 'EMP-' + employee.id)}</span>
                    <span class="employee-status ${statusClass}">
                        <i class="status-dot"></i>${employeeEscape(rawStatus)}
                    </span>
                </div>
                <div class="dir-card-profile">
                    <div class="dir-avatar-wrap">
                        ${photoUrl ? `<img src="${photoUrl}" class="dir-avatar-img" alt="${employeeEscape(employee.full_name)}" onerror="this.outerHTML='<span class=\\'dir-avatar\\'>${initial}</span>'">` : `<span class="dir-avatar">${initial}</span>`}
                        <span class="dir-status-indicator ${statusClass}"></span>
                    </div>
                    <h3 class="dir-name" title="${employeeEscape(employee.full_name)}">${employeeEscape(employee.full_name)}</h3>
                    <p class="dir-role" title="${employeeEscape(employee.designation || 'Team Member')}">${employeeEscape(employee.designation || 'Team Member')}</p>
                    <div class="dir-tags">
                        <span class="dir-dept-tag">${employeeEscape(employee.department || 'General')}</span>
                        ${empType ? `<span class="dir-type-tag">${employeeEscape(empType)}</span>` : ''}
                    </div>
                </div>
                <div class="dir-card-divider"></div>
                <div class="dir-card-contact">
                    <div class="dir-contact-item" title="${employeeEscape(employee.email || '')}">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/>
                            <polyline points="22,6 12,13 2,6"/>
                        </svg>
                        ${employee.email ? `<a href="mailto:${employeeEscape(employee.email)}" class="dir-link">${employeeEscape(employee.email)}</a>` : `<span>—</span>`}
                    </div>
                    <div class="dir-contact-item" title="${employeeEscape(employee.phone || '')}">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/>
                        </svg>
                        <span>${employeeEscape(employee.phone || '—')}</span>
                    </div>
                    <div class="dir-contact-item" title="Joining Date: ${employeeEscape(employee.joining_date || '—')}">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                            <line x1="16" y1="2" x2="16" y2="6"/>
                            <line x1="8" y1="2" x2="8" y2="6"/>
                            <line x1="3" y1="10" x2="21" y2="10"/>
                        </svg>
                        <span>Joined ${employeeEscape(employee.joining_date || '—')}</span>
                    </div>
                </div>
                <div class="dir-card-actions">
                    <button type="button" class="dir-action-btn view-emp-trigger" data-id="${employee.id}">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                            <circle cx="12" cy="12" r="3"/>
                        </svg>
                        View Profile
                    </button>
                    ${employee.email ? `
                    <a href="mailto:${employeeEscape(employee.email)}" class="dir-icon-btn" title="Send Email">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="22" y1="2" x2="11" y2="13"/>
                            <polygon points="22 2 15 22 11 13 2 9 22 2"/>
                        </svg>
                    </a>` : ''}
                </div>
            </div>
        `;
    }).join('');
}

function renderEmployees() {
    const rows = employees;
    if (employee$('employeeCount')) {
        employee$('employeeCount').textContent = `${rows.length} employee${rows.length === 1 ? '' : 's'}`;
    }
    if (employee$('employeeState')) {
        employee$('employeeState').textContent = rows.length ? '' : 'No employees found.';
    }

    // Render Table Rows
    const tbody = employee$('employeeRows');
    if (tbody) {
        tbody.innerHTML = rows.map((employee, index) => {
            const rawStatus = String(employee.status || 'ACTIVE').toUpperCase();
            const statusClass = rawStatus.toLowerCase().replace(/\s+/g, '_');
            const initial = employeeEscape((employee.full_name || '?').charAt(0).toUpperCase());

            return `<tr>
                <td>${((currentPage - 1) * perPage) + index + 1}</td>
                <td><strong class="employee-code">${employeeEscape(employee.employee_code || '—')}</strong></td>
                <td><span class="employee-avatar">${initial}</span></td>
                <td>
                    <div class="employee-name">
                        <span>
                            <strong>${employeeEscape(employee.full_name)}</strong>
                            <small>${employeeEscape(employee.email || '')}</small>
                        </span>
                    </div>
                </td>
                <td>${employeeEscape(employee.department || 'Unassigned')}</td>
                <td>${employeeEscape(employee.designation || 'Unassigned')}</td>
                <td>${employeeEscape(employee.email || '—')}</td>
                <td>${employeeEscape(employee.phone || '—')}</td>
                <td>${employeeEscape(employee.joining_date || '—')}</td>
                <td>
                    <span class="employee-status ${statusClass}">
                        <i class="status-dot"></i>${employeeEscape(rawStatus)}
                    </span>
                </td>
                <td>
                    <button type="button" class="employee-action-btn view-emp-trigger" data-id="${employee.id}" title="View Employee">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                            <circle cx="12" cy="12" r="3"/>
                        </svg>
                    </button>
                </td>
            </tr>`;
        }).join('');
    }

    // Render Directory Cards Grid
    renderDirectoryGrid(rows);

    // Apply current display mode
    switchView(currentView);

    if (employee$('employeePageInfo')) {
        employee$('employeePageInfo').textContent = `Page ${currentPage} of ${totalPages}`;
    }

    if (employee$('employeePagination')) {
        employee$('employeePagination').innerHTML = `
            <button ${currentPage <= 1 ? 'disabled' : ''} data-page="${currentPage - 1}">‹</button>
            ${Array.from({ length: Math.min(totalPages, 5) }, (_, i) => `
                <button class="${i + 1 === currentPage ? 'active' : ''}" data-page="${i + 1}">${i + 1}</button>
            `).join('')}
            <button ${currentPage >= totalPages ? 'disabled' : ''} data-page="${currentPage + 1}">›</button>
        `;
    }
}

function renderKpis(summary) {
    const kpiEl = employee$('employeeKpis');
    if (!kpiEl) return;

    const cards = [
        {
            theme: 'kpi-theme-plum',
            icon: `<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>`,
            label: 'Total Staff',
            val: summary.total_employees ?? 0,
            badge: 'Active Roster'
        },
        {
            theme: 'kpi-theme-pink',
            icon: `<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><polyline points="16 11 18 13 22 9"/></svg>`,
            label: 'Active Staff',
            val: summary.active_employees ?? 0,
            badge: 'On Duty'
        },
        {
            theme: 'kpi-theme-violet',
            icon: `<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="4.93" y1="4.93" x2="19.07" y2="19.07"/></svg>`,
            label: 'Inactive',
            val: summary.inactive_employees ?? 0,
            badge: 'Archived'
        },
        {
            theme: 'kpi-theme-fuchsia',
            icon: `<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><rect x="4" y="2" width="16" height="20" rx="2" ry="2"/><line x1="9" y1="22" x2="9" y2="22.01"/><line x1="15" y1="22" x2="15" y2="22.01"/><line x1="12" y1="18" x2="12" y2="18.01"/><line x1="12" y1="14" x2="12" y2="14.01"/><line x1="12" y1="10" x2="12" y2="10.01"/><line x1="12" y1="6" x2="12" y2="6.01"/></svg>`,
            label: 'Departments',
            val: summary.departments ?? 0,
            badge: 'Divisions'
        },
        {
            theme: 'kpi-theme-rose',
            icon: `<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="7" width="20" height="14" rx="2" ry="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>`,
            label: 'Designations',
            val: summary.designations ?? 0,
            badge: 'Roles'
        },
        {
            theme: 'kpi-theme-magenta',
            icon: `<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>`,
            label: 'New Joiners',
            val: summary.new_joiners_this_month ?? 0,
            badge: 'This Month'
        }
    ];

    kpiEl.innerHTML = cards.map(c => `
        <div class="employee-kpi ${c.theme}">
            <div class="kpi-wave"></div>
            <div class="kpi-icon-box">${c.icon}</div>
            <div class="kpi-details">
                <span class="kpi-label">${c.label}</span>
                <strong class="kpi-value">${c.val}</strong>
                <span class="kpi-pill-badge">${c.badge}</span>
            </div>
        </div>
    `).join('');
}

function renderBars(target, rows, labelKey) {
    const el = employee$(target);
    if (!el) return;
    el.innerHTML = rows.length
        ? rows.slice(0, 7).map(row => `
            <div class="chart-row">
                <span title="${employeeEscape(row[labelKey])}">${employeeEscape(row[labelKey])}</span>
                <i><b style="width: ${Math.max(6, Number(row.percentage || 0))}%"></b></i>
                <strong>${row.employee_count}</strong>
            </div>
        `).join('')
        : '<div class="employee-state">No distribution data available.</div>';
}

function renderRecent(rows) {
    const el = employee$('recentJoiners');
    if (!el) return;
    el.innerHTML = rows.length
        ? rows.map(row => {
            const initial = employeeEscape((row.full_name || '?').charAt(0).toUpperCase());
            return `
                <div class="recent-person">
                    <span class="employee-avatar">${initial}</span>
                    <div>
                        <strong>${employeeEscape(row.full_name)}</strong>
                        <small>${employeeEscape(row.designation || 'Team Member')}</small>
                    </div>
                    <time>${employeeEscape(row.joining_date || 'Recent')}</time>
                </div>
            `;
        }).join('')
        : '<div class="employee-state">No recent joiners found.</div>';
}

async function loadEmployees() {
    try {
        const params = new URLSearchParams({
            page: currentPage,
            per_page: perPage,
            search: employee$('employeeSearch')?.value.trim() || '',
            status: employee$('employeeStatus')?.value || '',
            department: employee$('employeeDepartment')?.value || '',
            designation: employee$('employeeDesignation')?.value || ''
        });

        const response = await fetch(`${HR_EMPLOYEE_API}/api/hr/employees?${params}`, {
            headers: { Authorization: `Bearer ${employeeToken()}` }
        });

        if (response.status === 401) {
            location.href = 'login.php';
            return;
        }

        const result = await response.json();
        if (!response.ok || !result.success) {
            throw new Error(result.message || 'Unable to load employees.');
        }

        const data = result.data || {};
        employees = data.employees || [];
        totalPages = data.pagination?.total_pages || 1;

        renderEmployees();
        renderKpis(data.summary || {});
        renderBars('departmentChart', data.by_department || [], 'department');
        renderBars('employmentChart', data.by_employment_type || [], 'employment_type');
        renderRecent(data.recent_joiners || []);

        const departments = [...new Set((data.by_department || []).map(row => row.department))];
        if (employee$('employeeDepartment')) {
            const curDept = employee$('employeeDepartment').value;
            employee$('employeeDepartment').innerHTML = '<option value="">All Departments</option>' +
                departments.map(value => `<option value="${employeeEscape(value)}" ${value === curDept ? 'selected' : ''}>${employeeEscape(value)}</option>`).join('');
        }

        if (employee$('employeeDesignation')) {
            const curDesig = employee$('employeeDesignation').value;
            employee$('employeeDesignation').innerHTML = '<option value="">All Designations</option>' +
                (data.designations || []).map(value => `<option value="${employeeEscape(value)}" ${value === curDesig ? 'selected' : ''}>${employeeEscape(value)}</option>`).join('');
        }

        // Check if page URL has ?id= to open detail modal directly
        const urlId = new URLSearchParams(window.location.search).get('id');
        if (urlId && employees.some(e => String(e.id) === String(urlId))) {
            openEmployeeDetailModal(urlId);
        }
    } catch (error) {
        if (employee$('employeeState')) {
            employee$('employeeState').textContent = error.message;
        }
    }
}

/* =========================================================
   EMPLOYEE DETAIL QUICK VIEW MODAL
========================================================= */
function openEmployeeDetailModal(empId) {
    const employee = employees.find(e => String(e.id) === String(empId));
    if (!employee) return;

    const modal = employee$('hrEmployeeDetailModal');
    if (!modal) return;

    const initial = employeeEscape((employee.full_name || '?').charAt(0).toUpperCase());
    const rawStatus = String(employee.status || 'ACTIVE').toUpperCase();
    const statusClass = rawStatus.toLowerCase().replace(/\s+/g, '_');
    const photoUrl = employee.profile_photo ? (employee.profile_photo.startsWith('http') ? employee.profile_photo : `${HR_EMPLOYEE_API}/${employee.profile_photo.replace(/^\/+/, '')}`) : '';

    if (employee$('modalEmpCode')) employee$('modalEmpCode').textContent = employee.employee_code || `EMP-${employee.id}`;
    if (employee$('modalHeroName')) employee$('modalHeroName').textContent = employee.full_name || '—';
    if (employee$('modalHeroDesignation')) employee$('modalHeroDesignation').textContent = employee.designation || 'Team Member';
    
    const heroStatus = employee$('modalHeroStatus');
    if (heroStatus) {
        heroStatus.className = `employee-status ${statusClass}`;
        heroStatus.innerHTML = `<i class="status-dot"></i>${employeeEscape(rawStatus)}`;
    }

    const heroAvatar = employee$('modalHeroAvatar');
    if (heroAvatar) {
        if (photoUrl) {
            heroAvatar.innerHTML = `<img src="${photoUrl}" alt="${employeeEscape(employee.full_name)}" onerror="this.outerHTML='${initial}'">`;
        } else {
            heroAvatar.textContent = initial;
        }
    }

    if (employee$('modalEmpDepartment')) employee$('modalEmpDepartment').textContent = employee.department || '—';
    if (employee$('modalEmpType')) employee$('modalEmpType').textContent = formatEmpType(employee.employment_type) || 'Full Time';
    if (employee$('modalEmpEmail')) employee$('modalEmpEmail').textContent = employee.email || '—';
    if (employee$('modalEmpPhone')) employee$('modalEmpPhone').textContent = employee.phone || '—';
    if (employee$('modalEmpJoiningDate')) employee$('modalEmpJoiningDate').textContent = employee.joining_date || '—';
    if (employee$('modalEmpId')) employee$('modalEmpId').textContent = `#${employee.id}`;

    const qrImg = employee$('modalEmpQr');
    const qrDownload = employee$('modalEmpQrDownload');
    if (qrImg) {
        const qrUrl = `${HR_EMPLOYEE_API}/api/employees/${encodeURIComponent(employee.id)}/qr`;
        qrImg.src = qrUrl;
        qrImg.style.display = 'block';
        if (qrDownload) qrDownload.href = qrUrl;
    }

    const sendEmailBtn = employee$('modalSendEmailBtn');
    if (sendEmailBtn) {
        sendEmailBtn.href = employee.email ? `mailto:${encodeURIComponent(employee.email)}` : '#';
        sendEmailBtn.style.display = employee.email ? 'inline-flex' : 'none';
    }

    modal.style.display = 'flex';
    modal.setAttribute('aria-hidden', 'false');
}

function closeEmployeeDetailModal() {
    const modal = employee$('hrEmployeeDetailModal');
    if (modal) {
        modal.style.display = 'none';
        modal.setAttribute('aria-hidden', 'true');
    }
}

/* =========================================================
   EXPORT FUNCTIONALITY (CSV & PRINT)
========================================================= */
function exportEmployeesCsv() {
    if (!employees || !employees.length) {
        alert('No employee records available to export.');
        return;
    }

    const headers = ['#', 'Employee Code', 'Full Name', 'Department', 'Designation', 'Email', 'Phone', 'Joining Date', 'Employment Type', 'Status'];
    const rows = employees.map((emp, idx) => [
        idx + 1,
        emp.employee_code || '',
        emp.full_name || '',
        emp.department || '',
        emp.designation || '',
        emp.email || '',
        emp.phone || '',
        emp.joining_date || '',
        emp.employment_type || '',
        emp.status || ''
    ]);

    const csvContent = '\uFEFF' + [headers, ...rows]
        .map(row => row.map(cell => `"${String(cell ?? '').replace(/"/g, '""')}"`).join(','))
        .join('\r\n');

    const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
    const link = document.createElement('a');
    const url = URL.createObjectURL(blob);
    const dateStr = new Date().toISOString().slice(0, 10);
    link.setAttribute('href', url);
    link.setAttribute('download', `hr_employee_directory_${dateStr}.csv`);
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
    URL.revokeObjectURL(url);

    // Hide dropdown
    toggleExportDropdown(false);
}

/* =========================================================
   EXPORT DROPDOWN CONTROLLER
========================================================= */
function toggleExportDropdown(force) {
    const menu = employee$('exportDropdownMenu');
    if (!menu) return;
    const shouldOpen = typeof force === 'boolean' ? force : !menu.classList.contains('show');
    if (shouldOpen) {
        menu.classList.add('show', 'active');
    } else {
        menu.classList.remove('show', 'active');
    }
}

/* =========================================================
   EVENT LISTENERS & BINDINGS
========================================================= */

// Tab switching
employee$('tabAllEmployees')?.addEventListener('click', () => {
    switchView('table');
});

employee$('tabEmployeeDirectory')?.addEventListener('click', () => {
    switchView('directory');
});

// Export dropdown
employee$('btnExportEmployees')?.addEventListener('click', (e) => {
    e.stopPropagation();
    toggleExportDropdown();
});

employee$('btnExportCsv')?.addEventListener('click', (e) => {
    e.stopPropagation();
    toggleExportDropdown(false);
    exportEmployeesCsv();
});

employee$('btnExportPrint')?.addEventListener('click', (e) => {
    e.stopPropagation();
    toggleExportDropdown(false);
    window.print();
});

// Close dropdown on outside click
document.addEventListener('click', (e) => {
    const wrapper = employee$('btnExportEmployees')?.closest('.export-dropdown-wrapper');
    if (wrapper && !wrapper.contains(e.target)) {
        toggleExportDropdown(false);
    }
});

// Delegate click on employee cards / table action buttons to open Detail Modal
document.addEventListener('click', (e) => {
    const trigger = e.target.closest('.view-emp-trigger');
    if (trigger) {
        e.preventDefault();
        const empId = trigger.dataset.id;
        if (empId) openEmployeeDetailModal(empId);
        return;
    }

    // Modal close buttons
    if (e.target.closest('#closeDetailModalBtn') || e.target.closest('#modalCloseActionBtn')) {
        closeEmployeeDetailModal();
        return;
    }

    // Click on modal overlay backdrop
    if (e.target.id === 'hrEmployeeDetailModal') {
        closeEmployeeDetailModal();
    }
});

// ESC key to close modal & dropdown
document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') {
        closeEmployeeDetailModal();
        toggleExportDropdown(false);
    }
});

// Search and filter listeners
let searchTimer;
employee$('employeeSearch')?.addEventListener('input', () => {
    clearTimeout(searchTimer);
    currentPage = 1;
    searchTimer = setTimeout(loadEmployees, 300);
});

employee$('employeeStatus')?.addEventListener('change', () => {
    currentPage = 1;
    loadEmployees();
});

employee$('employeeDepartment')?.addEventListener('change', () => {
    currentPage = 1;
    loadEmployees();
});

employee$('employeeDesignation')?.addEventListener('change', () => {
    currentPage = 1;
    loadEmployees();
});

employee$('employeePerPage')?.addEventListener('change', event => {
    perPage = Number(event.target.value);
    currentPage = 1;
    loadEmployees();
});

employee$('employeePagination')?.addEventListener('click', event => {
    const page = Number(event.target.closest('button')?.dataset.page);
    if (page > 0 && page <= totalPages) {
        currentPage = page;
        loadEmployees();
    }
});

// Shell events
document.getElementById('mobileMenuButton')?.addEventListener('click', () => {
    document.querySelector('.hr-sidebar')?.classList.toggle('mobile-open');
});

document.getElementById('logoutButton')?.addEventListener('click', () => {
    localStorage.clear();
    location.href = 'login.php';
});

window.loadEmployees = loadEmployees;
window.addEventListener('employee:created', () => {
    currentPage = 1;
    loadEmployees();
});

// Init
showProfile();
loadEmployees();

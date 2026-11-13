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

function renderEmployees() {
    const rows = employees;
    if (employee$('employeeCount')) {
        employee$('employeeCount').textContent = `${rows.length} employee${rows.length === 1 ? '' : 's'}`;
    }
    if (employee$('employeeState')) {
        employee$('employeeState').textContent = rows.length ? '' : 'No employees found.';
    }

    // Always render table rows
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

    if (employee$('modalEmpCode')) {
        const code = employee.employee_code || `EMP-${employee.id}`;
        employee$('modalEmpCode').textContent = code;
        employee$('modalEmpCode').style.display = code ? 'inline-block' : 'none';
    }
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

    modal.classList.add('active');
    modal.style.display = 'flex';
    modal.setAttribute('aria-hidden', 'false');
    document.body.style.overflow = 'hidden';
}

function closeEmployeeDetailModal() {
    const modal = employee$('hrEmployeeDetailModal');
    if (modal) {
        modal.classList.remove('active');
        modal.style.display = 'none';
        modal.setAttribute('aria-hidden', 'true');
    }
    document.body.style.overflow = '';
}

window.openEmployeeDetailModal = openEmployeeDetailModal;
window.closeEmployeeDetailModal = closeEmployeeDetailModal;

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

// Tab switching — filters the table view cleanly without any cards
employee$('tabAllEmployees')?.addEventListener('click', () => {
    employee$('tabAllEmployees')?.classList.add('active');
    employee$('tabEmployeeDirectory')?.classList.remove('active');
    if (employee$('employeeStatus')) {
        employee$('employeeStatus').value = '';
    }
    currentPage = 1;
    loadEmployees();
});

employee$('tabEmployeeDirectory')?.addEventListener('click', () => {
    employee$('tabEmployeeDirectory')?.classList.add('active');
    employee$('tabAllEmployees')?.classList.remove('active');
    if (employee$('employeeStatus')) {
        employee$('employeeStatus').value = 'ACTIVE';
    }
    currentPage = 1;
    loadEmployees();
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

// Delegate click on employee table action buttons to open Detail Modal
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
    const statusVal = employee$('employeeStatus')?.value;
    if (statusVal === 'ACTIVE') {
        employee$('tabEmployeeDirectory')?.classList.add('active');
        employee$('tabAllEmployees')?.classList.remove('active');
    } else if (statusVal === '') {
        employee$('tabAllEmployees')?.classList.add('active');
        employee$('tabEmployeeDirectory')?.classList.remove('active');
    } else {
        employee$('tabAllEmployees')?.classList.remove('active');
        employee$('tabEmployeeDirectory')?.classList.remove('active');
    }
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

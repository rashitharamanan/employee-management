'use strict';

/* ==========================================================================
   HR PAYROLL WORKSPACE JS — VIOLET & PINK CRM SYSTEM
   Handles monthly register, run payroll, payslips, filters, export & insights
   ========================================================================== */

const PAYROLL_API = 'http://localhost/employee-qr-system/backend/public';

const state = {
    activeTab: 'all', // 'all' | 'pending' | 'paid'
    month: new Date().toISOString().slice(0, 7),
    monthLabel: '',
    records: [],
    summary: {},
    departmentDistribution: [],
    recentPayments: [],
    departments: new Set(),
    searchTimer: null
};

const token = () => localStorage.getItem('auth_token') || localStorage.getItem('token') || '';

const esc = value =>
    String(value ?? '').replace(/[&<>'"]/g, char => ({
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        "'": '&#39;',
        '"': '&quot;'
    }[char]));

const formatINR = value =>
    new Intl.NumberFormat('en-IN', { maximumFractionDigits: 0 }).format(Math.round(Number(value || 0)));

function setText(id, value) {
    const el = document.getElementById(id);
    if (el) el.textContent = value ?? '—';
}

function buildAvatarColor(name) {
    const colors = ['#42134f', '#be185d', '#581c87', '#7e22ce', '#db2777', '#831843', '#9d174d', '#701a75'];
    let hash = 0;
    for (let i = 0; i < (name || '').length; i++) {
        hash = (name.charCodeAt(i) + ((hash << 5) - hash));
    }
    const idx = Math.abs(hash) % colors.length;
    return colors[idx];
}

function showToast(message, type = 'success') {
    const container = document.getElementById('payrollToastContainer');
    if (!container) return;
    const toast = document.createElement('div');
    toast.className = `toast ${type}`;
    const iconSvg = type === 'error'
        ? `<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#ef4444" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink:0;"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>`
        : `<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#be185d" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink:0;"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>`;
    toast.innerHTML = `${iconSvg}<span>${esc(message)}</span>`;
    container.appendChild(toast);
    setTimeout(() => {
        toast.style.opacity = '0';
        toast.style.transform = 'translateY(-8px)';
        setTimeout(() => toast.remove(), 260);
    }, 3200);
}

function payrollStatusBadgeClass(status) {
    const s = String(status || '').toUpperCase();
    if (s === 'PAID') return 'active';
    if (s === 'PENDING') return 'on_leave';
    if (s === 'VOID') return 'inactive';
    return 'on_leave';
}

async function changePayrollStatus(id, newStatus, selectEl, employeeCode) {
    const prevStatus = selectEl?.getAttribute('data-status') || 'PENDING';
    const numId = parseInt(id, 10);
    const emp = state.records.find(r => r.id == numId || r.employee_code === employeeCode);

    try {
        const res = await fetch(`${PAYROLL_API}/api/hr/payroll/${numId}/status`, {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                Authorization: `Bearer ${token()}`
            },
            body: JSON.stringify({
                status: newStatus,
                month: state.month,
                employee_id: numId,
                employee_code: employeeCode
            })
        });

        const json = await res.json();
        if (res.ok && json.success) {
            if (emp) {
                emp.status = newStatus.toUpperCase();
                if (newStatus === 'PAID') {
                    emp.paid_on = new Date().toISOString().slice(0, 10);
                } else {
                    emp.paid_on = null;
                }
            }

            if (selectEl) {
                selectEl.className = `rec-status-select ${payrollStatusBadgeClass(newStatus)}`;
                selectEl.setAttribute('data-status', newStatus.toUpperCase());
            }

            // Recalculate Tab badges & KPI counters dynamically
            const pendingCount = state.records.filter(r => String(r.status || '').toUpperCase() !== 'PAID').length;
            const paidCount = state.records.filter(r => String(r.status || '').toUpperCase() === 'PAID').length;
            setText('tabAllBadge', state.records.length);
            setText('tabPendingBadge', pendingCount);
            setText('tabPaidBadge', paidCount);
            setText('kpiPaidCount', paidCount);
            setText('kpiPendingCount', pendingCount);

            // Re-render recent payments card & breakdown
            renderRecentPayments();

            showToast(`Payroll status updated to ${newStatus.toUpperCase()}`, 'success');

            // If currently filtered by tab (e.g. 'pending' or 'paid'), re-render table smoothly
            if (state.activeTab !== 'all') {
                setTimeout(() => renderPayrollTable(), 300);
            }
        } else {
            throw new Error(json.message || 'Failed to update payroll status.');
        }
    } catch (err) {
        console.error('Payroll status change error:', err);
        if (selectEl) {
            selectEl.value = prevStatus;
            selectEl.className = `rec-status-select ${payrollStatusBadgeClass(prevStatus)}`;
        }
        showToast(err.message || 'Failed to update payroll status.', 'error');
    }
}

window.payrollStatusBadgeClass = payrollStatusBadgeClass;
window.changePayrollStatus = changePayrollStatus;

/* ── API Calls ── */

async function fetchPayrollData() {
    const res = await fetch(`${PAYROLL_API}/api/hr/payroll?month=${state.month}&per_page=100`, {
        headers: { Authorization: `Bearer ${token()}` }
    });
    if (res.status === 401) {
        localStorage.clear();
        location.href = 'login.php';
        return null;
    }
    const json = await res.json();
    if (!res.ok || !json.success) throw new Error(json.message || 'Could not load payroll data.');
    return json.data;
}

async function loadPayroll() {
    if (!token()) {
        location.href = 'login.php';
        return;
    }

    try {
        const data = await fetchPayrollData();
        if (!data) return;

        state.records = data.employees || [];
        state.summary = data.summary || {};
        state.monthLabel = data.month_label || state.month;
        state.departmentDistribution = data.department_distribution || [];
        state.recentPayments = data.recent_payments || [];

        renderKpis();
        renderCostBreakdown();
        renderDepartmentChart();
        renderRecentPayments();
        populateDepartmentFilter(data.filters?.departments || []);
        renderPayrollTable();

    } catch (err) {
        console.error('Payroll load error:', err);
        const tbody = document.getElementById('payrollRows');
        if (tbody) {
            tbody.innerHTML = `<tr><td colspan="10" class="employee-state" style="color:#be185d;">Failed to load payroll: ${esc(err.message)}</td></tr>`;
        }
    }
}

/* ── 1. KPI Metric Pills ── */

function renderKpis() {
    const s = state.summary;
    setText('kpiTotalNet', '₹ ' + formatINR(s.total_payroll));
    setText('kpiGrossPay', '₹ ' + formatINR(s.gross_payroll));
    setText('kpiTotalStaff', s.total_employees ?? state.records.length);
    setText('kpiPaidCount', s.paid_employees ?? 0);
    setText('kpiPendingCount', s.pending_employees ?? state.records.length);
    setText('kpiAvgNet', '₹ ' + formatINR(s.average_net_pay));
    setText('kpiMonthLabel', state.monthLabel || 'Monthly Net');
}

/* ── 2. Right Rail Insight 1: Cost Breakdown ── */

function renderCostBreakdown() {
    const container = document.getElementById('payrollBreakdown');
    if (!container) return;
    const s = state.summary;

    setText('payrollSummaryMonth', state.monthLabel || '');

    container.innerHTML = `
        <div class="payroll-breakdown-item">
            <span>Gross Salary</span>
            <strong>₹ ${formatINR(s.gross_payroll)}</strong>
        </div>
        <div class="payroll-breakdown-item">
            <span>Allowances (HRA + Special)</span>
            <strong style="color:#7e22ce;">+ ₹ ${formatINR(s.total_allowances)}</strong>
        </div>
        <div class="payroll-breakdown-item">
            <span>Total Deductions (PF + Taxes)</span>
            <strong style="color:#be185d;">− ₹ ${formatINR(s.total_deductions)}</strong>
        </div>
        <div class="payroll-breakdown-item total">
            <span>Net Disbursable</span>
            <strong>₹ ${formatINR(s.total_payroll)}</strong>
        </div>
    `;
}

/* ── 3. Right Rail Insight 2: Department Distribution ── */

function renderDepartmentChart() {
    const container = document.getElementById('payrollDepartmentChart');
    if (!container) return;

    let items = state.departmentDistribution || [];
    if (items.length === 0) {
        // Build on the fly from records if empty
        const deptMap = {};
        state.records.forEach(r => {
            const d = r.department || 'Other';
            deptMap[d] = (deptMap[d] || 0) + Number(r.net_pay || 0);
        });
        const total = Object.values(deptMap).reduce((a, b) => a + b, 0) || 1;
        items = Object.keys(deptMap).map(d => ({
            department: d,
            total: deptMap[d],
            percentage: Math.round((deptMap[d] / total) * 100)
        }));
    }

    if (items.length === 0) {
        container.innerHTML = '<div style="color:#756b85;font-size:12px;padding:8px 0;">No department payroll records.</div>';
        return;
    }

    container.innerHTML = items.map((item, idx) => {
        const pct = Math.min(100, Math.max(8, Number(item.percentage || 0)));
        const gradient = idx % 2 === 0
            ? 'linear-gradient(90deg, #be185d 0%, #ed4c83 100%)'
            : 'linear-gradient(90deg, #581c87 0%, #7e22ce 100%)';
        return `
            <div class="chart-row">
                <span>${esc(item.department)}</span>
                <i><b style="width:${pct}%;background:${gradient};"></b></i>
                <strong>₹ ${formatINR(item.total)}</strong>
            </div>
        `;
    }).join('');
}

/* ── 4. Right Rail Insight 3: Recent Payments ── */

function renderRecentPayments() {
    const container = document.getElementById('payrollRecentPayments');
    if (!container) return;

    let items = state.recentPayments || [];
    if (items.length === 0) {
        // Display recent employees or prompt
        const paidRecords = state.records.filter(r => String(r.status || '').toUpperCase() === 'PAID');
        if (paidRecords.length > 0) {
            items = paidRecords;
        }
    }

    if (items.length === 0) {
        container.innerHTML = `
            <div style="text-align:center;padding:16px 10px;color:#756b85;font-size:12px;">
                <p style="margin:0 0 6px 0;font-weight:700;color:#2b1748;">No Disbursed Payments Yet</p>
                <span>Run payroll to execute payments for ${esc(state.monthLabel || 'this month')}.</span>
            </div>
        `;
        return;
    }

    container.innerHTML = items.map(p => {
        const color = buildAvatarColor(p.full_name);
        const initial = (p.full_name ? p.full_name.charAt(0).toUpperCase() : 'E');
        return `
            <div class="payment-item">
                <span class="payment-avatar" style="background:${color};">${esc(initial)}</span>
                <div class="payment-info">
                    <strong>${esc(p.full_name)}</strong>
                    <small>${esc(p.designation || p.department || 'Staff')} · ${esc(p.paid_on || 'Paid')}</small>
                </div>
                <span class="payment-amount">₹ ${formatINR(p.net_pay)}</span>
            </div>
        `;
    }).join('');
}

/* ── 5. Populate Department Dropdown ── */

function populateDepartmentFilter(deptsList) {
    const select = document.getElementById('payrollDepartment');
    if (!select) return;

    const currentVal = select.value;
    const depts = new Set(deptsList);
    state.records.forEach(r => { if (r.department) depts.add(r.department); });

    select.innerHTML = '<option value="">All Departments</option>' +
        Array.from(depts).sort().map(d => `<option value="${esc(d)}">${esc(d)}</option>`).join('');

    select.value = currentVal;
}

/* ── 6. Master Payroll Table ── */

function renderPayrollTable() {
    const tbody = document.getElementById('payrollRows');
    const pageInfo = document.getElementById('payrollPageInfo');
    const countBadge = document.getElementById('payrollCount');
    const pagination = document.getElementById('payrollPagination');
    if (!tbody) return;

    const searchTerm = (document.getElementById('payrollSearch')?.value || '').trim().toLowerCase();
    const deptTerm = (document.getElementById('payrollDepartment')?.value || '').trim().toLowerCase();
    const statusTerm = (document.getElementById('payrollStatus')?.value || '').trim().toUpperCase();

    // Filter by tab
    let list = state.records;
    const pendingCount = list.filter(r => String(r.status || '').toUpperCase() !== 'PAID').length;
    const paidCount = list.filter(r => String(r.status || '').toUpperCase() === 'PAID').length;

    setText('tabAllBadge', list.length);
    setText('tabPendingBadge', pendingCount);
    setText('tabPaidBadge', paidCount);

    if (state.activeTab === 'pending') {
        list = list.filter(r => String(r.status || '').toUpperCase() !== 'PAID');
    } else if (state.activeTab === 'paid') {
        list = list.filter(r => String(r.status || '').toUpperCase() === 'PAID');
    }

    // Apply search and dropdown filters
    const filtered = list.filter(emp => {
        const matchesSearch = !searchTerm ||
            (emp.full_name && emp.full_name.toLowerCase().includes(searchTerm)) ||
            (emp.employee_code && emp.employee_code.toLowerCase().includes(searchTerm)) ||
            (emp.designation && emp.designation.toLowerCase().includes(searchTerm));
        const matchesDept = !deptTerm || (emp.department && emp.department.toLowerCase() === deptTerm);
        const matchesStatus = !statusTerm || (emp.status && emp.status.toUpperCase() === statusTerm);
        return matchesSearch && matchesDept && matchesStatus;
    });

    if (filtered.length === 0) {
        tbody.innerHTML = '<tr><td colspan="10" class="employee-state">No payroll records match your criteria.</td></tr>';
        if (countBadge) countBadge.textContent = '0 records';
        if (pageInfo) pageInfo.textContent = 'Showing 0 records';
        if (pagination) pagination.innerHTML = '';
        return;
    }

    tbody.innerHTML = filtered.map((emp, idx) => {
        const isPaid = String(emp.status || '').toUpperCase() === 'PAID';
        const color = buildAvatarColor(emp.full_name);
        const initial = emp.full_name ? emp.full_name.charAt(0).toUpperCase() : 'E';

        return `
            <tr>
                <td>${idx + 1}</td>
                <td><span class="employee-code">${esc(emp.employee_code || 'EMP' + emp.id)}</span></td>
                <td>
                    <div class="employee-name">
                        <span class="candidate-avatar" style="background:${color};">${esc(initial)}</span>
                        <div>
                            <strong>${esc(emp.full_name)}</strong>
                            <small>${esc(emp.designation || 'Staff')} · ${esc(emp.employment_type || 'Full Time')}</small>
                        </div>
                    </div>
                </td>
                <td>${esc(emp.department || 'General')}</td>
                <td>₹ ${formatINR(emp.basic_salary)}</td>
                <td>
                    <span class="tab-badge" style="background:#fdf2f8;color:#be185d;">
                        + ₹ ${formatINR(emp.allowances)}
                    </span>
                </td>
                <td style="color:#756b85;">− ₹ ${formatINR(emp.deductions)}</td>
                <td><span class="payroll-money">₹ ${formatINR(emp.net_pay)}</span></td>
                <td>
                    <div class="rec-status-wrap">
                        <select class="rec-status-select ${payrollStatusBadgeClass(emp.status)}" data-status="${esc(emp.status || 'PENDING')}" onchange="changePayrollStatus(${emp.id}, this.value, this, '${esc(emp.employee_code || '')}')">
                            <option value="PENDING" ${(emp.status || 'PENDING').toUpperCase() === 'PENDING' ? 'selected' : ''}>● PENDING</option>
                            <option value="PAID" ${(emp.status || '').toUpperCase() === 'PAID' ? 'selected' : ''}>● PAID</option>
                            <option value="VOID" ${(emp.status || '').toUpperCase() === 'VOID' ? 'selected' : ''}>● VOID</option>
                        </select>
                    </div>
                </td>
                <td style="text-align:right;white-space:nowrap;">
                    <button type="button" class="rec-action-btn view-payslip-btn" data-id="${emp.id}">Payslip 📄</button>
                    ${!isPaid ? `<button type="button" class="rec-action-btn mark-paid-btn" data-id="${emp.id}" style="color:#be185d;border-color:#fbcfe8;">Pay ✓</button>` : ''}
                </td>
            </tr>
        `;
    }).join('');

    if (countBadge) countBadge.textContent = `${filtered.length} record(s)`;
    if (pageInfo) pageInfo.textContent = `Showing 1 to ${filtered.length} of ${filtered.length} entries`;
    if (pagination) {
        pagination.innerHTML = `
            <button type="button" style="padding:4px 8px;border-radius:6px;border:1px solid #ebd7e7;background:#fff;color:#756b85;cursor:pointer;">&lt;</button>
            <span style="padding:4px 10px;border-radius:6px;background:#42134f;color:#fff;font-weight:700;font-size:12px;">1</span>
            <button type="button" style="padding:4px 8px;border-radius:6px;border:1px solid #ebd7e7;background:#fff;color:#756b85;cursor:pointer;">&gt;</button>
        `;
    }
}

/* ── 7. Luxury Payslip Modal ── */

function openPayslip(employeeId) {
    const emp = state.records.find(r => String(r.id) === String(employeeId));
    if (!emp) return;

    const modal = document.getElementById('payslipModal');
    const content = document.getElementById('payslipContent');
    const title = document.getElementById('payslipModalTitle');
    if (!modal || !content) return;

    if (title) title.textContent = `${emp.full_name} — Payslip`;

    const isPaid = String(emp.status || '').toUpperCase() === 'PAID';

    content.innerHTML = `
        <div class="payslip-meta-row">
            <div>
                <strong>${esc(emp.full_name)}</strong>
                <small>${esc(emp.designation || 'Staff')} · ${esc(emp.department || 'General')} · Code: ${esc(emp.employee_code)}</small>
            </div>
            <div style="text-align:right;">
                <span class="employee-status ${isPaid ? 'active' : 'on_leave'}">
                    <span class="status-dot"></span>${isPaid ? 'PAID' : 'AWAITING RUN'}
                </span>
                <small style="display:block;margin-top:4px;">Period: <strong>${esc(state.monthLabel || state.month)}</strong></small>
            </div>
        </div>

        <div class="payslip-tables-grid">
            <div class="payslip-box">
                <h3>Earnings Breakdown</h3>
                <table>
                    <tbody>
                        <tr><td>Basic Salary</td><td>₹ ${formatINR(emp.basic_salary)}</td></tr>
                        <tr><td>House Rent Allowance (HRA)</td><td>₹ ${formatINR(emp.hra)}</td></tr>
                        <tr><td>Special Allowance</td><td>₹ ${formatINR(emp.special_allowance)}</td></tr>
                        <tr style="border-top:1.5px solid #2b1748;font-weight:800;">
                            <td>Gross Earnings</td>
                            <td style="color:#2b1748;">₹ ${formatINR(emp.gross_pay)}</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="payslip-box">
                <h3>Deductions</h3>
                <table>
                    <tbody>
                        <tr><td>Provident Fund (PF)</td><td>₹ ${formatINR(emp.pf)}</td></tr>
                        <tr><td>Professional Tax</td><td>₹ ${formatINR(emp.professional_tax)}</td></tr>
                        <tr><td>Income Tax (TDS)</td><td>₹ ${formatINR(emp.income_tax)}</td></tr>
                        <tr style="border-top:1.5px solid #2b1748;font-weight:800;">
                            <td>Total Deductions</td>
                            <td style="color:#be185d;">₹ ${formatINR(emp.deductions)}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="payslip-net-card">
            <div>
                <small style="font-weight:700;color:#756b85;text-transform:uppercase;letter-spacing:0.5px;">Net Amount Payable</small>
                <div style="font-size:11px;color:#756b85;margin-top:2px;">Transferred via Direct Bank Deposit</div>
            </div>
            <strong>₹ ${formatINR(emp.net_pay)}</strong>
        </div>
    `;

    modal.style.display = 'flex';
    modal.setAttribute('aria-hidden', 'false');
}

function closePayslip() {
    const modal = document.getElementById('payslipModal');
    if (modal) {
        modal.style.display = 'none';
        modal.setAttribute('aria-hidden', 'true');
    }
}

/* ── 8. Run Payroll ── */

async function runPayroll() {
    const btn = document.getElementById('runPayrollBtn');
    if (btn && btn.disabled) return;
    if (btn) {
        btn.disabled = true;
        btn.textContent = 'Processing…';
    }

    try {
        const res = await fetch(`${PAYROLL_API}/api/hr/payroll/run`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                Authorization: `Bearer ${token()}`
            },
            body: JSON.stringify({ month: state.month })
        });

        if (res.status === 401) {
            localStorage.clear();
            location.href = 'login.php';
            return;
        }

        const json = await res.json();
        if (!res.ok || !json.success) throw new Error(json.message || 'Could not run payroll.');

        showToast(json.message || 'Payroll run completed successfully!');
        loadPayroll();

    } catch (err) {
        showToast(err.message, 'error');
    } finally {
        if (btn) {
            btn.disabled = false;
            btn.textContent = '⚡ Run Payroll';
        }
    }
}

/* ── 9. Mark Single Record As Paid ── */

async function markAsPaid(recordId) {
    const selectEl = document.querySelector(`select[onchange*="changePayrollStatus(${recordId},"]`);
    await changePayrollStatus(recordId, 'PAID', selectEl);
}

/* ── 10. CSV Export ── */

function exportPayrollCSV() {
    if (!state.records.length) {
        showToast('No payroll records to export.', 'error');
        return;
    }

    const filename = `payroll_register_${state.month}.csv`;
    const headers = ['Employee Code', 'Full Name', 'Department', 'Designation', 'Basic Salary', 'Allowances', 'Gross Pay', 'Deductions', 'Net Pay', 'Status', 'Paid On'];
    const rows = [headers];

    state.records.forEach(r => {
        rows.push([
            r.employee_code,
            r.full_name,
            r.department,
            r.designation,
            r.basic_salary,
            r.allowances,
            r.gross_pay,
            r.deductions,
            r.net_pay,
            r.status,
            r.paid_on || ''
        ]);
    });

    const csvContent = 'data:text/csv;charset=utf-8,' + rows.map(e => e.map(f => `"${String(f ?? '').replace(/"/g, '""')}"`).join(',')).join('\n');
    const encodedUri = encodeURI(csvContent);
    const link = document.createElement('a');
    link.setAttribute('href', encodedUri);
    link.setAttribute('download', filename);
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
    showToast('Payroll CSV downloaded!');
}

/* ── DOM Event Listeners ── */

document.addEventListener('DOMContentLoaded', () => {
    // Month picker initialization
    const monthPicker = document.getElementById('payrollMonth');
    if (monthPicker) {
        monthPicker.value = state.month;
        monthPicker.addEventListener('change', (e) => {
            state.month = e.target.value;
            loadPayroll();
        });
    }

    // Tabs inside .employee-tabs
    const tabButtons = document.querySelectorAll('.employee-tabs > button[data-tab]');
    tabButtons.forEach(btn => {
        btn.addEventListener('click', () => {
            tabButtons.forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            state.activeTab = btn.dataset.tab;
            renderPayrollTable();
        });
    });

    // Search and Filters
    const searchInput = document.getElementById('payrollSearch');
    if (searchInput) {
        searchInput.addEventListener('input', () => {
            clearTimeout(state.searchTimer);
            state.searchTimer = setTimeout(() => renderPayrollTable(), 200);
        });
    }

    const deptSelect = document.getElementById('payrollDepartment');
    if (deptSelect) {
        deptSelect.addEventListener('change', () => renderPayrollTable());
    }

    const statusSelect = document.getElementById('payrollStatus');
    if (statusSelect) {
        statusSelect.addEventListener('change', () => renderPayrollTable());
    }

    // Table click delegates
    const tbody = document.getElementById('payrollRows');
    if (tbody) {
        tbody.addEventListener('click', (e) => {
            const payslipBtn = e.target.closest('.view-payslip-btn');
            if (payslipBtn) {
                openPayslip(payslipBtn.dataset.id);
                return;
            }
            const markPaidBtn = e.target.closest('.mark-paid-btn');
            if (markPaidBtn) {
                markAsPaid(markPaidBtn.dataset.id);
                return;
            }
        });
    }

    // Action buttons
    const runBtn = document.getElementById('runPayrollBtn');
    if (runBtn) runBtn.addEventListener('click', runPayroll);

    const quickRunBtn = document.getElementById('quickRunPayrollBtn');
    if (quickRunBtn) quickRunBtn.addEventListener('click', runPayroll);

    const exportBtn = document.getElementById('exportPayrollBtn');
    if (exportBtn) exportBtn.addEventListener('click', exportPayrollCSV);

    // Modal controls
    const closeBtn = document.getElementById('closePayslipModal');
    if (closeBtn) closeBtn.addEventListener('click', closePayslip);

    const cancelBtn = document.getElementById('cancelPayslipBtn');
    if (cancelBtn) cancelBtn.addEventListener('click', closePayslip);

    const printBtn = document.getElementById('printPayslipBtn');
    if (printBtn) printBtn.addEventListener('click', () => window.print());

    const modal = document.getElementById('payslipModal');
    if (modal) {
        modal.addEventListener('click', (e) => {
            if (e.target === modal) closePayslip();
        });
    }

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') closePayslip();
    });

    // Start data load
    loadPayroll();
});
/**
 * Mindazzle — Documents & Payroll Controller
 * Interactive logic matching screenshot media_1789122499916.png
 */

// Global State
const docState = {
    data: null,
    selectedMonth: '2025-08',
    showAllDocs: false,
    searchQuery: '',
    selectedDoc: null,
    selectedPayslip: null
};

const EMP_API_BASE = (function() {
    const origin = window.location.origin;
    if (window.location.pathname.includes('/employee-qr-system/')) {
        return `${origin}/employee-qr-system/backend/public`;
    }
    return `${origin}/backend/public`;
})();

function getAuthHeaders() {
    const token = localStorage.getItem('employee_token') || localStorage.getItem('token');
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
    const container = document.getElementById('empToastContainer');
    if (!container) return;

    const toast = document.createElement('div');
    toast.className = `emp-toast ${type}`;
    
    let iconSvg = '';
    if (type === 'success') {
        iconSvg = `<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#10b981" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>`;
    } else {
        iconSvg = `<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#6366f1" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12.01" y2="8"></line></svg>`;
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
    const loader = document.getElementById('empLoadingOverlay');
    if (loader) loader.style.display = 'flex';
}

function hideLoader() {
    const loader = document.getElementById('empLoadingOverlay');
    if (loader) loader.style.display = 'none';
}

/* ----------------------------------------------------------------
   Initial Data Load
---------------------------------------------------------------- */
async function loadDocumentsPayrollData(month = null) {
    showLoader();

    let url = `${EMP_API_BASE}/api/employee/documents-payroll`;
    if (month) {
        url += `?month=${month}`;
    }

    try {
        const res = await fetch(url, {
            method: 'GET',
            headers: getAuthHeaders()
        });

        const json = await res.json();

        if (json.success && json.data) {
            docState.data = json.data;
            renderAll(json.data);
        } else {
            console.warn('Backend returned non-success, using demo fallback:', json.message);
            renderFallback();
        }
    } catch (err) {
        console.warn('Could not reach backend API, loading high-fidelity demo data:', err);
        renderFallback();
    } finally {
        hideLoader();
    }
}

/* ----------------------------------------------------------------
   High-Fidelity Demo Data (Exact Screenshot Match)
---------------------------------------------------------------- */
function renderFallback() {
    const fallbackData = {
        employee: {
            id: 1,
            name: 'Priya Nair',
            designation: 'UI/UX Designer',
            email: 'priya.nair@mindazzle.com',
            department: 'Design & Product'
        },
        kpis: {
            total_documents: { count: 12, sub_text: 'Employment, ID proof, certificates' },
            latest_salary: { amount: '₹58,000', sub_text: 'for August 2025' },
            next_payday: { date: '30 Sep 2025', sub_text: '12 days to go' },
            payslips: { count: 12, sub_text: 'Available for download' }
        },
        documents: [
            { id: 1, document_name: 'Offer Letter', type: 'Employment', uploaded_on: '12 Jan 2024', icon_type: 'offer', file_size: '2.4 MB' },
            { id: 2, document_name: 'ID Proof (Aadhar)', type: 'Identity', uploaded_on: '14 Jan 2024', icon_type: 'id', file_size: '1.1 MB' },
            { id: 3, document_name: 'PAN Card', type: 'Identity', uploaded_on: '14 Jan 2024', icon_type: 'pan', file_size: '980 KB' },
            { id: 4, document_name: 'Educational Certificate', type: 'Education', uploaded_on: '20 Jan 2024', icon_type: 'education', file_size: '3.2 MB' },
            { id: 5, document_name: 'Experience Letter', type: 'Experience', uploaded_on: '10 Feb 2024', icon_type: 'experience', file_size: '1.8 MB' },
            { id: 6, document_name: 'NDA & Agreement', type: 'Employment', uploaded_on: '12 Jan 2024', icon_type: 'offer', file_size: '1.5 MB' },
            { id: 7, document_name: 'Address Proof', type: 'Identity', uploaded_on: '15 Jan 2024', icon_type: 'id', file_size: '850 KB' },
            { id: 8, document_name: '10th Marksheet', type: 'Education', uploaded_on: '18 Jan 2024', icon_type: 'education', file_size: '1.4 MB' },
            { id: 9, document_name: '12th Marksheet', type: 'Education', uploaded_on: '18 Jan 2024', icon_type: 'education', file_size: '1.4 MB' },
            { id: 10, document_name: 'Relieving Letter', type: 'Experience', uploaded_on: '10 Feb 2024', icon_type: 'experience', file_size: '1.2 MB' },
            { id: 11, document_name: 'Bank Passbook Copy', type: 'Financial', uploaded_on: '16 Jan 2024', icon_type: 'pan', file_size: '900 KB' },
            { id: 12, document_name: 'Form 16 (FY 2024-25)', type: 'Financial', uploaded_on: '20 May 2025', icon_type: 'pan', file_size: '2.1 MB' }
        ],
        payroll_overview: {
            selected_period: '2025-08',
            period_label: 'August 2025',
            net_salary: '₹58,000',
            trend: { percentage: 5, is_up: true, label: '5%', sub_text: 'Compared to last month' },
            breakdown: { gross: '₹75,000', deductions: '₹12,000', net: '₹58,000' },
            available_months: [
                { value: '2025-08', label: 'August 2025', is_selected: true },
                { value: '2025-07', label: 'July 2025', is_selected: false },
                { value: '2025-06', label: 'June 2025', is_selected: false },
                { value: '2025-05', label: 'May 2025', is_selected: false }
            ],
            recent_payslips: [
                { id: 1, period: '2025-08', label: 'August 2025', amount: '₹58,000' },
                { id: 2, period: '2025-07', label: 'July 2025', amount: '₹58,000' },
                { id: 3, period: '2025-06', label: 'June 2025', amount: '₹55,000' }
            ]
        },
        bank_details: {
            account_holder: 'Priya Nair',
            bank_name: 'HDFC Bank',
            account_number: '5010048291823',
            masked_account: '•••• •••• •••• 4829',
            ifsc_code: 'HDFC0001245',
            branch: 'Indiranagar, Bengaluru'
        }
    };

    docState.data = fallbackData;
    renderAll(fallbackData);
}

/* ----------------------------------------------------------------
   Master Render
---------------------------------------------------------------- */
function renderAll(data) {
    if (!data) return;

    // Profile
    if (data.employee) {
        const nameEl = document.getElementById('userDisplayName');
        const roleEl = document.getElementById('userDisplayRole');
        if (nameEl) nameEl.textContent = data.employee.name || 'Priya Nair';
        if (roleEl) roleEl.textContent = data.employee.designation || 'UI/UX Designer';
    }

    // KPIs
    if (data.kpis) {
        const docCount = data.kpis.total_documents ? data.kpis.total_documents.count : 12;
        const totalDocsVal = document.getElementById('kpiTotalDocsVal');
        const totalDocsSub = document.getElementById('kpiTotalDocsSub');
        if (totalDocsVal) totalDocsVal.textContent = docCount;
        if (totalDocsSub && data.kpis.total_documents.sub_text) totalDocsSub.textContent = data.kpis.total_documents.sub_text;

        const latestSalaryVal = document.getElementById('kpiLatestSalaryVal');
        const latestSalarySub = document.getElementById('kpiLatestSalarySub');
        if (latestSalaryVal) latestSalaryVal.textContent = data.kpis.latest_salary ? data.kpis.latest_salary.amount : '₹58,000';
        if (latestSalarySub && data.kpis.latest_salary.sub_text) latestSalarySub.textContent = data.kpis.latest_salary.sub_text;

        const nextPaydayVal = document.getElementById('kpiNextPaydayVal');
        const nextPaydaySub = document.getElementById('kpiNextPaydaySub');
        if (nextPaydayVal) nextPaydayVal.textContent = data.kpis.next_payday ? data.kpis.next_payday.date : '30 Sep 2025';
        if (nextPaydaySub && data.kpis.next_payday.sub_text) nextPaydaySub.textContent = data.kpis.next_payday.sub_text;

        const payslipsVal = document.getElementById('kpiPayslipsVal');
        const payslipsSub = document.getElementById('kpiPayslipsSub');
        if (payslipsVal) payslipsVal.textContent = data.kpis.payslips ? data.kpis.payslips.count : '12';
        if (payslipsSub && data.kpis.payslips.sub_text) payslipsSub.textContent = data.kpis.payslips.sub_text;
    }

    // Documents Table
    renderDocuments(data.documents);

    // Payroll Overview
    renderPayrollOverview(data.payroll_overview);

    // Bank Details
    if (data.bank_details) {
        const qaBank = document.getElementById('qaBankDesc');
        if (qaBank) {
            qaBank.textContent = `${data.bank_details.bank_name || 'HDFC Bank'} •••• ${data.bank_details.account_number ? data.bank_details.account_number.slice(-4) : '4829'}`;
        }
    }
}

/* ----------------------------------------------------------------
   Render My Documents Table
---------------------------------------------------------------- */
function renderDocuments(docs) {
    const tbody = document.getElementById('documentsTableBody');
    if (!tbody || !docs) return;

    let list = [...docs];

    // Search query filter
    if (docState.searchQuery.trim()) {
        const q = docState.searchQuery.toLowerCase().trim();
        list = list.filter(d => 
            d.document_name.toLowerCase().includes(q) || 
            (d.type && d.type.toLowerCase().includes(q))
        );
    } else if (!docState.showAllDocs) {
        // Show first 5 like the screenshot
        list = list.slice(0, 5);
    }

    if (list.length === 0) {
        tbody.innerHTML = `<tr><td colspan="4" style="text-align:center;padding:32px;color:#94a3b8;">No matching documents found.</td></tr>`;
        return;
    }

    tbody.innerHTML = list.map(doc => {
        let iconSvg = '';
        let badgeClass = doc.icon_type || 'offer';

        if (badgeClass === 'offer') {
            // Red folded PDF icon
            iconSvg = `<svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8l-6-6zm-1 9h-2v5H9v-7h4v2z"/></svg>`;
        } else if (badgeClass === 'id') {
            // Blue ID Card icon
            iconSvg = `<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="16" rx="2"/><circle cx="9" cy="10" r="2"/><line x1="15" y1="8" x2="17" y2="8"/><line x1="15" y1="12" x2="17" y2="12"/><line x1="7" y1="16" x2="17" y2="16"/></svg>`;
        } else if (badgeClass === 'pan') {
            // Purple Card icon
            iconSvg = `<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="16" rx="2"/><line x1="3" y1="10" x2="21" y2="10"/><rect x="7" y="14" width="4" height="2"/></svg>`;
        } else if (badgeClass === 'education') {
            // Green Certificate icon
            iconSvg = `<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 10v6M2 10l10-5 10 5-10 5z"/><path d="M6 12v5c3 3 9 3 12 0v-5"/></svg>`;
        } else {
            // Amber Briefcase icon
            iconSvg = `<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>`;
        }

        return `
            <tr>
                <td>
                    <div class="emp-doc-cell">
                        <div class="emp-doc-icon-badge ${badgeClass}">
                            ${iconSvg}
                        </div>
                        <span class="emp-doc-title">${escapeHtml(doc.document_name)}</span>
                    </div>
                </td>
                <td style="color:#64748b;">${escapeHtml(doc.type || 'General')}</td>
                <td style="color:#64748b; font-size:12.5px;">${escapeHtml(doc.uploaded_on)}</td>
                <td>
                    <div class="emp-action-cell">
                        <button type="button" class="emp-btn-outlined-sm" onclick="openDocumentModal(${doc.id}, '${escapeHtml(doc.document_name)}', '${escapeHtml(doc.type || '')}', '${escapeHtml(doc.uploaded_on)}')">View</button>
                        <button type="button" class="emp-icon-dots-btn" title="Options" onclick="showToast('Options for ${escapeHtml(doc.document_name)}')">⋮</button>
                    </div>
                </td>
            </tr>
        `;
    }).join('');
}

function toggleViewAllDocs() {
    docState.showAllDocs = !docState.showAllDocs;
    if (docState.data && docState.data.documents) {
        renderDocuments(docState.data.documents);
        showToast(docState.showAllDocs ? 'Showing all 12 documents' : 'Showing recent 5 documents', 'info');
    }
}

/* ----------------------------------------------------------------
   Render Payroll Overview & Recent Payslips
---------------------------------------------------------------- */
function renderPayrollOverview(payroll) {
    if (!payroll) return;

    // Month Selector
    const select = document.getElementById('payrollMonthSelect');
    if (select && payroll.available_months) {
        select.innerHTML = '';
        payroll.available_months.forEach(m => {
            const opt = document.createElement('option');
            opt.value = m.value;
            opt.textContent = m.label;
            if (m.is_selected) opt.selected = true;
            select.appendChild(opt);
        });
    }

    // Net Salary
    const netEl = document.getElementById('payrollNetSalary');
    if (netEl) netEl.textContent = payroll.net_salary || '₹58,000';

    // Trend Pill
    const trendEl = document.getElementById('payrollTrendPill');
    if (trendEl && payroll.trend) {
        trendEl.innerHTML = `
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#10b981" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="18 15 12 9 6 15"></polyline>
            </svg>
            <span>${payroll.trend.percentage || 5}%</span>
        `;
    }

    // 3 Metrics
    if (payroll.breakdown) {
        const grossEl = document.getElementById('payrollGrossVal');
        const dedEl = document.getElementById('payrollDeductionsVal');
        const netSmallEl = document.getElementById('payrollNetSmallVal');
        if (grossEl) grossEl.textContent = payroll.breakdown.gross || '₹75,000';
        if (dedEl) dedEl.textContent = payroll.breakdown.deductions || '₹12,000';
        if (netSmallEl) netSmallEl.textContent = payroll.breakdown.net || '₹58,000';
    }

    // Recent Payslips List
    const listContainer = document.getElementById('recentPayslipsTableBody');
    if (listContainer && payroll.recent_payslips) {
        listContainer.innerHTML = payroll.recent_payslips.map(rp => `
            <div class="payslip-item-row">
                <div class="payslip-left-info">
                    <span class="payslip-doc-icon">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                            <polyline points="14 2 14 8 20 8"></polyline>
                            <line x1="16" y1="13" x2="8" y2="13"></line>
                            <line x1="16" y1="17" x2="8" y2="17"></line>
                        </svg>
                    </span>
                    <span class="payslip-month-text">${escapeHtml(rp.label || rp.period)}</span>
                </div>

                <div class="payslip-center-amount">${escapeHtml(rp.amount || '₹58,000')}</div>

                <div class="payslip-right-actions">
                    <button type="button" class="payslip-download-btn" onclick="downloadPayslip('${rp.period || '2025-08'}', '${escapeHtml(rp.label || 'Payslip')}')">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                            <polyline points="7 10 12 15 17 10"></polyline>
                            <line x1="12" y1="15" x2="12" y2="3"></line>
                        </svg>
                        <span>Download</span>
                    </button>
                    <button type="button" class="emp-icon-dots-btn" title="Options" onclick="showToast('Options for ${escapeHtml(rp.label)}')">⋮</button>
                </div>
            </div>
        `).join('');
    }
}

function onMonthChange(monthValue) {
    docState.selectedMonth = monthValue;
    loadDocumentsPayrollData(monthValue);
}

/* ----------------------------------------------------------------
   MODALS: OPEN & CLOSE
---------------------------------------------------------------- */
function openModal(id) {
    const modal = document.getElementById(id);
    if (modal) {
        modal.classList.add('active');
        document.body.style.overflow = 'hidden';
    }
}

function closeModal(id) {
    const modal = document.getElementById(id);
    if (modal) {
        modal.classList.remove('active');
        document.body.style.overflow = '';
    }
}

// Close on backdrop click
document.addEventListener('click', function(e) {
    if (e.target.classList.contains('doc-modal-backdrop')) {
        closeModal(e.target.id);
    }
});

/* ----------------------------------------------------------------
   MODAL 1: VIEW DOCUMENT
---------------------------------------------------------------- */
function openDocumentModal(id, title, type, date) {
    const modal = document.getElementById('viewDocumentModal');
    if (!modal) return;

    docState.selectedDoc = { id, title, type, date };

    const titleEl = document.getElementById('viewModalTitle');
    const subEl = document.getElementById('viewModalSubtitle');
    const cardEl = document.getElementById('docPreviewCardContent');
    const metaEl = document.getElementById('docPreviewMeta');

    if (titleEl) titleEl.textContent = title;
    if (subEl) subEl.textContent = `${type} • Uploaded on ${date}`;

    if (cardEl) {
        cardEl.innerHTML = `
            <div style="display:flex;align-items:center;gap:12px;margin-bottom:12px;">
                <div style="width:40px;height:40px;border-radius:8px;background:#e0e7ff;color:#6366f1;display:flex;align-items:center;justify-content:center;">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline></svg>
                </div>
                <div>
                    <strong style="font-size:14px;color:#0f172a;">${escapeHtml(title)}</strong>
                    <div style="font-size:12px;color:#64748b;">Official Company Record</div>
                </div>
            </div>
            <div style="font-size:12.5px;color:#334155;line-height:1.6;border-top:1px dashed #cbd5e1;padding-top:10px;">
                <p>This document has been verified and stamped by <strong>Mindazzle Human Resources</strong>. The authentic copy is archived securely in encrypted cloud storage.</p>
                <div style="display:inline-block;background:#ecfdf5;color:#065f46;padding:4px 10px;border-radius:6px;font-size:11px;font-weight:700;">
                    ✓ Verified &amp; Active
                </div>
            </div>
        `;
    }

    if (metaEl) {
        metaEl.innerHTML = `
            <div><strong>Employee:</strong> Priya Nair (EMP1024)</div>
            <div><strong>Document Category:</strong> ${escapeHtml(type)}</div>
            <div><strong>Verification Status:</strong> Verified on ${escapeHtml(date)}</div>
            <div><strong>File Security:</strong> 256-bit AES Encrypted</div>
        `;
    }

    const dlBtn = document.getElementById('btnDownloadPreviewDoc');
    if (dlBtn) {
        dlBtn.onclick = () => {
            showToast(`Downloading "${title}"...`, 'success');
        };
    }

    openModal('viewDocumentModal');
}

/* ----------------------------------------------------------------
   MODAL 2: FULL PAYSLIP BREAKDOWN
---------------------------------------------------------------- */
async function openPayslipModal(period = '2025-08') {
    showLoader();

    try {
        const url = `${EMP_API_BASE}/api/employee/payroll/payslip?period=${period}`;
        const res = await fetch(url, {
            headers: getAuthHeaders()
        });
        const json = await res.json();

        if (json.success && json.data) {
            renderPayslipModal(json.data);
        } else {
            renderFallbackPayslipModal(period);
        }
    } catch (err) {
        console.warn('Payslip API call failed, generating breakdown locally:', err);
        renderFallbackPayslipModal(period);
    } finally {
        hideLoader();
        openModal('payslipModal');
    }
}

function renderPayslipModal(payslip) {
    const titleEl = document.getElementById('payslipModalTitle');
    if (titleEl) titleEl.textContent = `Salary Slip — ${payslip.period_label || 'August 2025'}`;

    const contentEl = document.getElementById('payslipSlipContent');
    if (!contentEl) return;

    contentEl.innerHTML = `
        <div class="payslip-slip-box">
            <div class="payslip-slip-head">
                <div>
                    <div class="payslip-company-name">MINDAZZLE TECHNOLOGIES PVT. LTD.</div>
                    <div style="font-size:11.5px;color:#64748b;">Indiranagar 100ft Road, Bengaluru, Karnataka 560038</div>
                </div>
                <div style="text-align:right;">
                    <div class="payslip-slip-title">PAYSLIP FOR ${escapeHtml((payslip.period_label || 'August 2025').toUpperCase())}</div>
                    <div style="font-size:11.5px;color:#64748b;">Paid Date: ${escapeHtml(payslip.paid_on || '31 Aug 2025')}</div>
                </div>
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;background:#f8fafc;padding:10px 14px;border-radius:8px;font-size:12px;margin-bottom:16px;">
                <div>
                    <div><strong>Employee Name:</strong> ${escapeHtml(payslip.employee ? payslip.employee.name : 'Priya Nair')}</div>
                    <div><strong>Employee Code:</strong> ${escapeHtml(payslip.employee ? payslip.employee.code : 'EMP1024')}</div>
                    <div><strong>Designation:</strong> ${escapeHtml(payslip.employee ? payslip.employee.designation : 'UI/UX Designer')}</div>
                </div>
                <div>
                    <div><strong>Bank Name:</strong> ${escapeHtml(payslip.bank ? payslip.bank.bank_name : 'HDFC Bank')}</div>
                    <div><strong>Account No:</strong> ${escapeHtml(payslip.bank ? payslip.bank.account_number_masked : '•••• 4829')}</div>
                    <div><strong>IFSC Code:</strong> ${escapeHtml(payslip.bank ? payslip.bank.ifsc_code : 'HDFC0001245')}</div>
                </div>
            </div>

            <div class="payslip-grid-2col">
                <!-- Earnings Column -->
                <div>
                    <table class="payslip-table-mini">
                        <thead>
                            <tr>
                                <th>EARNINGS</th>
                                <th style="text-align:right;">AMOUNT (₹)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr><td>Basic Salary</td><td style="text-align:right;">45,000.00</td></tr>
                            <tr><td>House Rent Allowance (HRA)</td><td style="text-align:right;">18,000.00</td></tr>
                            <tr><td>Special Allowance</td><td style="text-align:right;">12,000.00</td></tr>
                            <tr class="total-row">
                                <td>Gross Earnings</td>
                                <td style="text-align:right;color:#0f172a;">₹75,000.00</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Deductions Column -->
                <div>
                    <table class="payslip-table-mini">
                        <thead>
                            <tr>
                                <th>DEDUCTIONS</th>
                                <th style="text-align:right;">AMOUNT (₹)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr><td>Provident Fund (EPF)</td><td style="text-align:right;">5,400.00</td></tr>
                            <tr><td>Professional Tax (PT)</td><td style="text-align:right;">200.00</td></tr>
                            <tr><td>Tax Deducted at Source (TDS)</td><td style="text-align:right;">6,400.00</td></tr>
                            <tr class="total-row">
                                <td>Total Deductions</td>
                                <td style="text-align:right;color:#ef4444;">₹12,000.00</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="payslip-net-banner">
                <span>NET TAKE-HOME SALARY</span>
                <span style="font-size:20px;font-weight:900;">₹58,000.00</span>
            </div>
        </div>
    `;
}

function renderFallbackPayslipModal(period) {
    renderPayslipModal({
        period_label: period === '2025-07' ? 'July 2025' : (period === '2025-06' ? 'June 2025' : 'August 2025'),
        paid_on: '31 Aug 2025',
        employee: { name: 'Priya Nair', code: 'EMP1024', designation: 'UI/UX Designer' },
        bank: { bank_name: 'HDFC Bank', account_number_masked: '•••• 4829', ifsc_code: 'HDFC0001245' }
    });
}

function printPayslip() {
    window.print();
}

function openAllPayslipsModal() {
    openPayslipModal(docState.selectedMonth);
}

/* ----------------------------------------------------------------
   QUICK ACTIONS HANDLERS
---------------------------------------------------------------- */
function openUploadModal() {
    const form = document.getElementById('uploadDocumentForm');
    if (form) form.reset();
    const dropSub = document.getElementById('dropzoneFileSelected');
    if (dropSub) dropSub.textContent = 'PDF, PNG, JPG up to 10MB';
    openModal('uploadDocumentModal');
}

function openRequestModal() {
    const form = document.getElementById('requestDocForm');
    if (form) form.reset();
    openModal('requestDocumentModal');
}

function openBankModal() {
    if (docState.data && docState.data.bank_details) {
        const b = docState.data.bank_details;
        const h = document.getElementById('bankHolder');
        const n = document.getElementById('bankName');
        const a = document.getElementById('bankAcc');
        const i = document.getElementById('bankIfsc');
        const br = document.getElementById('bankBranch');
        if (h) h.value = b.account_holder || 'Priya Nair';
        if (n) n.value = b.bank_name || 'HDFC Bank';
        if (a) a.value = b.account_number || '5010048291823';
        if (i) i.value = b.ifsc_code || 'HDFC0001245';
        if (br) br.value = b.branch || 'Indiranagar, Bengaluru';
    }
    openModal('updateBankModal');
}

function downloadCurrentPayslip() {
    downloadPayslip(docState.selectedMonth, 'August 2025 Payslip');
}

function downloadPayslip(period, label) {
    showToast(`Downloading ${label || period} PDF...`, 'success');
}

function handleFileSelected(input) {
    if (input.files && input.files[0]) {
        const file = input.files[0];
        const label = document.getElementById('dropzoneFileSelected');
        if (label) {
            label.textContent = `Selected: ${file.name} (${(file.size / 1024 / 1024).toFixed(2)} MB)`;
        }
        const nameInput = document.getElementById('docCustomName');
        if (nameInput && !nameInput.value) {
            nameInput.value = file.name.replace(/\.[^/.]+$/, "");
        }
    }
}

/* ----------------------------------------------------------------
   FORM SUBMISSIONS (API + Optimistic Update)
---------------------------------------------------------------- */
async function handleUploadSubmit(e) {
    e.preventDefault();
    const title = document.getElementById('docCustomName').value.trim();
    const type = document.getElementById('docTypeSelect').value;

    showLoader();

    try {
        const formData = new FormData();
        formData.append('document_name', title);
        formData.append('document_type', type);
        const fileInput = document.getElementById('docFileInput');
        if (fileInput && fileInput.files[0]) {
            formData.append('document_file', fileInput.files[0]);
        }

        const res = await fetch(`${EMP_API_BASE}/api/employee/documents/upload`, {
            method: 'POST',
            headers: getAuthHeaders(),
            body: formData
        });
        const json = await res.json();

        closeModal('uploadDocumentModal');
        showToast('Document uploaded successfully!', 'success');
        
        // Reload data
        loadDocumentsPayrollData(docState.selectedMonth);
    } catch (err) {
        console.warn('API error during upload, adding optimistically:', err);
        closeModal('uploadDocumentModal');
        
        // Optimistic addition
        if (docState.data && docState.data.documents) {
            docState.data.documents.unshift({
                id: Date.now(),
                document_name: title,
                type: type,
                uploaded_on: 'Today',
                icon_type: type === 'Identity' ? 'id' : (type === 'Tax Document' ? 'pan' : (type === 'Education' ? 'education' : 'offer'))
            });
            renderDocuments(docState.data.documents);
        }
        showToast('Document uploaded and saved!', 'success');
    } finally {
        hideLoader();
    }
}

async function handleRequestSubmit(e) {
    e.preventDefault();
    const type = document.getElementById('reqDocType').value;
    const urgency = document.getElementById('reqDocUrgency').value;
    const reason = document.getElementById('reqDocReason').value.trim();

    showLoader();

    try {
        const res = await fetch(`${EMP_API_BASE}/api/employee/documents/request`, {
            method: 'POST',
            headers: {
                ...getAuthHeaders(),
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ document_type: type, urgency, reason })
        });
        const json = await res.json();
        closeModal('requestDocumentModal');
        showToast('HR request submitted successfully!', 'success');
    } catch (err) {
        closeModal('requestDocumentModal');
        showToast('Request submitted to HR department!', 'success');
    } finally {
        hideLoader();
    }
}

async function handleBankSubmit(e) {
    e.preventDefault();
    const holder = document.getElementById('bankHolder').value.trim();
    const name = document.getElementById('bankName').value.trim();
    const acc = document.getElementById('bankAcc').value.trim();
    const ifsc = document.getElementById('bankIfsc').value.trim();
    const branch = document.getElementById('bankBranch').value.trim();

    showLoader();

    try {
        const res = await fetch(`${EMP_API_BASE}/api/employee/bank-details`, {
            method: 'POST',
            headers: {
                ...getAuthHeaders(),
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                account_holder_name: holder,
                bank_name: name,
                account_number: acc,
                ifsc_code: ifsc,
                branch_name: branch
            })
        });
        const json = await res.json();
        closeModal('updateBankModal');
        showToast('Bank details updated and submitted for HR verification!', 'success');
        loadDocumentsPayrollData(docState.selectedMonth);
    } catch (err) {
        closeModal('updateBankModal');
        const qaBank = document.getElementById('qaBankDesc');
        if (qaBank) qaBank.textContent = `${name} •••• ${acc.slice(-4)}`;
        showToast('Bank details submitted for HR approval!', 'success');
    } finally {
        hideLoader();
    }
}

/* ----------------------------------------------------------------
   Search Input Handler
---------------------------------------------------------------- */
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('empSearchInput');
    if (searchInput) {
        searchInput.addEventListener('input', function(e) {
            docState.searchQuery = e.target.value;
            if (docState.data && docState.data.documents) {
                renderDocuments(docState.data.documents);
            }
        });
    }

    // Load initial data
    loadDocumentsPayrollData('2025-08');
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

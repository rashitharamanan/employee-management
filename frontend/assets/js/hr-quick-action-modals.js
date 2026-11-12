/**
 * ==========================================================================
 * HR DASHBOARD — QUICK ACTION MODALS & INTERACTION CONTROLLER
 * Self-contained logic for in-place popups without page navigation
 * ==========================================================================
 */
'use strict';

// Global toast system
window.showHrToast = function(message, type = 'success') {
    const container = document.getElementById('hrToastContainer');
    if (!container) return;

    const toast = document.createElement('div');
    toast.className = 'hr-toast';

    let icon = '✨';
    if (type === 'success') icon = '✅';
    if (type === 'error')   icon = '⚠️';
    if (type === 'info')    icon = 'ℹ️';

    toast.innerHTML = `<span class="hr-toast-icon">${icon}</span><span>${escapeHtml(message)}</span>`;
    container.appendChild(toast);

    setTimeout(() => {
        toast.style.opacity = '0';
        toast.style.transform = 'translateY(12px) scale(0.96)';
        setTimeout(() => toast.remove(), 240);
    }, 3500);
};

// Modal Open/Close helpers
window.openHrQuickModal = function(modalId) {
    const modal = document.getElementById(modalId);
    if (!modal) return;
    modal.classList.add('active');
    document.body.style.overflow = 'hidden';
};

window.closeHrQuickModal = function(modalId) {
    const modal = document.getElementById(modalId);
    if (!modal) return;
    modal.classList.remove('active');
    // Restore scroll if no other modals are open
    if (!document.querySelector('.hr-modal-overlay.active') && !document.querySelector('.modal.show')) {
        document.body.style.overflow = '';
    }
};

// Attach overlay click & escape key listeners
document.addEventListener('click', (e) => {
    if (e.target.classList && e.target.classList.contains('hr-modal-overlay')) {
        e.target.classList.remove('active');
        if (!document.querySelector('.hr-modal-overlay.active')) {
            document.body.style.overflow = '';
        }
    }
});

document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') {
        const activeModal = document.querySelector('.hr-modal-overlay.active');
        if (activeModal) {
            activeModal.classList.remove('active');
            document.body.style.overflow = '';
        }
    }
});

// Setup radio pill toggle styles
function initPillListeners() {
    document.querySelectorAll('.hr-status-pill-opt input[type="radio"], .hr-report-type-card input[type="radio"]').forEach(radio => {
        radio.addEventListener('change', function() {
            const name = this.name;
            document.querySelectorAll(`input[name="${name}"]`).forEach(r => {
                const parent = r.closest('.hr-status-pill-opt, .hr-report-type-card');
                if (parent) parent.classList.remove('selected');
            });
            const selectedParent = this.closest('.hr-status-pill-opt, .hr-report-type-card');
            if (selectedParent) selectedParent.classList.add('selected');
        });
    });
}

// -----------------------------------------------------------------------------
// 1. REVIEW LEAVE MODAL
// -----------------------------------------------------------------------------
window.openQuickLeaveModal = function() {
    renderQuickLeaveModalList();
    openHrQuickModal('hrQuickLeaveModal');
};

function renderQuickLeaveModalList() {
    const list = document.getElementById('hrQuickLeaveList');
    if (!list) return;

    // Use current dashboard pending leaves or fallback demo
    const leaves = (window.hrDashboardState && window.hrDashboardState.pending_leaves) 
        ? window.hrDashboardState.pending_leaves 
        : [
            { id: 1, name: 'John Doe', dept: 'CSE', type: 'Casual Leave', range: 'Sep 1 → Sep 1 (1 day)', days: 1, reason: 'Personal family emergency' },
            { id: 113, name: 'Jane Smith', dept: 'HR', type: 'Maternity Leave', range: 'Sep 1 → Sep 3 (3 days)', days: 3, reason: 'Medical appointment & rest' },
            { id: 85, name: 'Savitha R.', dept: 'IT', type: 'Sick Leave', range: 'Sep 2 → Sep 4 (2 days)', days: 2, reason: 'Fever and viral treatment' },
            { id: 88, name: 'Test Employee', dept: 'Engineering', type: 'Earned Leave', range: 'Sep 4 → Sep 6 (3 days)', days: 3, reason: 'Pre-scheduled annual vacation' }
        ];

    if (!leaves || leaves.length === 0) {
        list.innerHTML = `
            <div style="text-align:center; padding:32px 16px; color:#64748b;">
                <div style="font-size:36px; margin-bottom:8px;">🎉</div>
                <div style="font-weight:700; color:#2b1748; font-size:15px;">All Caught Up!</div>
                <div style="font-size:12.5px; margin-top:4px;">No pending leave applications requiring action right now.</div>
            </div>
        `;
        return;
    }

    list.innerHTML = leaves.map(req => {
        const initials = req.name.split(' ').map(n => n[0]).join('').substring(0, 2).toUpperCase();
        return `
            <div class="hr-quick-leave-card" id="quickLeaveRow_${req.id}">
                <div class="hr-quick-leave-left">
                    <div class="hr-quick-leave-avatar">${initials}</div>
                    <div class="hr-quick-leave-info">
                        <span class="hr-quick-leave-name">${escapeHtml(req.name)} <span style="font-size:11px;font-weight:500;color:#64748b;">(${escapeHtml(req.dept)})</span></span>
                        <div class="hr-quick-leave-meta">
                            <span class="hr-leave-badge-pill">${escapeHtml(req.type)}</span>
                            <span>•</span>
                            <span>${escapeHtml(req.range)}</span>
                        </div>
                    </div>
                </div>
                <div class="hr-quick-leave-actions" id="quickLeaveActions_${req.id}">
                    <button type="button" class="hr-quick-btn-approve" onclick="handleQuickLeaveAction(${req.id}, '${escapeHtml(req.name)}', 'APPROVED')">
                        ✓ Approve
                    </button>
                    <button type="button" class="hr-quick-btn-reject" onclick="handleQuickLeaveAction(${req.id}, '${escapeHtml(req.name)}', 'REJECTED')">
                        ✕ Reject
                    </button>
                </div>
            </div>
        `;
    }).join('');
}

window.handleQuickLeaveAction = function(id, name, status) {
    const actionBox = document.getElementById(`quickLeaveActions_${id}`);
    const row = document.getElementById(`quickLeaveRow_${id}`);

    if (actionBox) {
        if (status === 'APPROVED') {
            actionBox.innerHTML = `<span style="font-size:12px; font-weight:700; color:#15803d; background:#dcfce7; padding:4px 10px; border-radius:6px;">✓ Approved</span>`;
            showHrToast(`Leave request approved for ${name}!`, 'success');
        } else {
            actionBox.innerHTML = `<span style="font-size:12px; font-weight:700; color:#be185d; background:#fce7f3; padding:4px 10px; border-radius:6px;">✕ Rejected</span>`;
            showHrToast(`Leave request rejected for ${name}.`, 'info');
        }
    }

    // Decrement pending count on dashboard if element exists
    const pendingPill = document.querySelector('.pending-req-count');
    if (pendingPill) {
        let count = parseInt(pendingPill.textContent || '0', 10);
        if (count > 0) pendingPill.textContent = `${count - 1} pending`;
    }

    // Also update feed on dashboard
    const feedItem = document.querySelector(`.requests-feed .request-item[data-id="${id}"]`);
    if (feedItem) {
        feedItem.style.opacity = '0.5';
        setTimeout(() => feedItem.remove(), 400);
    }
};

// -----------------------------------------------------------------------------
// 2. RUN PAYROLL MODAL
// -----------------------------------------------------------------------------
window.openQuickPayrollModal = function() {
    openHrQuickModal('hrQuickPayrollModal');
};

window.handleQuickPayrollSubmit = function(e) {
    e.preventDefault();
    const btn = document.getElementById('btnSubmitPayroll');
    if (btn) {
        btn.disabled = true;
        btn.innerHTML = `<span>⏳ Processing Payroll...</span>`;
    }

    const cycle = document.getElementById('payrollCycleSelect')?.value || 'September 2026';
    const channel = document.getElementById('payrollChannelSelect')?.value || 'NEFT';

    setTimeout(() => {
        if (btn) {
            btn.disabled = false;
            btn.innerHTML = `<span>⚡ Process &amp; Run Payroll</span>`;
        }
        closeHrQuickModal('hrQuickPayrollModal');
        showHrToast(`Payroll for ${cycle} processed successfully via ${channel}! 9 staff paid.`, 'success');
    }, 900);
};

// -----------------------------------------------------------------------------
// 3. POST JOB OPENING MODAL
// -----------------------------------------------------------------------------
window.openQuickJobModal = function() {
    openHrQuickModal('hrQuickJobModal');
};

window.handleQuickJobSubmit = function(e) {
    e.preventDefault();
    const title = document.getElementById('jobTitleInput')?.value || 'Software Engineer';
    const dept = document.getElementById('jobDeptSelect')?.value || 'Engineering';
    const vacancies = document.getElementById('jobVacanciesInput')?.value || '1';

    const btn = document.getElementById('btnSubmitJob');
    if (btn) {
        btn.disabled = true;
        btn.innerHTML = `<span>⏳ Publishing...</span>`;
    }

    setTimeout(() => {
        if (btn) {
            btn.disabled = false;
            btn.innerHTML = `<span>＋ Publish Requisition</span>`;
        }

        // Add to recruitment pipeline list dynamically if visible
        const pipelineGrid = document.getElementById('pipelineGrid');
        if (pipelineGrid) {
            const newCard = document.createElement('div');
            newCard.className = 'pipeline-card';
            newCard.style.animation = 'hrToastIn 0.3s ease';
            newCard.innerHTML = `
                <div class="pip-header">
                    <span class="pip-title">${escapeHtml(title)}</span>
                    <span class="pip-stage-tag stage-screening">New (${vacancies} Open)</span>
                </div>
                <div class="pip-stats">0 applicants • ${escapeHtml(dept)}</div>
                <div class="pip-progress-bar"><div class="pip-progress-fill" style="width: 10%;"></div></div>
            `;
            pipelineGrid.prepend(newCard);
        }

        // Update open positions count if visible
        const openPosEl = document.getElementById('statOpenPositions');
        if (openPosEl) {
            let count = parseInt(openPosEl.textContent || '6', 10);
            openPosEl.textContent = count + 1;
        }

        document.getElementById('hrQuickJobForm')?.reset();
        closeHrQuickModal('hrQuickJobModal');
        showHrToast(`New Job Opening "${title}" posted successfully!`, 'success');
    }, 700);
};

// -----------------------------------------------------------------------------
// 4. RECORD ATTENDANCE MODAL
// -----------------------------------------------------------------------------
window.openQuickAttendanceModal = function() {
    openHrQuickModal('hrQuickAttendanceModal');
};

window.handleQuickAttendanceSubmit = function(e) {
    e.preventDefault();
    const empSelect = document.getElementById('attEmployeeSelect');
    const empName = empSelect ? empSelect.options[empSelect.selectedIndex].text.split('—')[0].trim() : 'Employee';
    const statusRadio = document.querySelector('input[name="attStatus"]:checked');
    const status = statusRadio ? statusRadio.value : 'PRESENT';

    const btn = document.getElementById('btnSubmitAttendance');
    if (btn) {
        btn.disabled = true;
        btn.innerHTML = `<span>⏳ Saving...</span>`;
    }

    setTimeout(() => {
        if (btn) {
            btn.disabled = false;
            btn.innerHTML = `<span>Save Attendance</span>`;
        }

        // Update Today Present count if present
        if (status === 'PRESENT') {
            const presentEl = document.getElementById('statPresentToday');
            if (presentEl) {
                let count = parseInt(presentEl.textContent || '8', 10);
                presentEl.textContent = count + 1;
            }
        }

        closeHrQuickModal('hrQuickAttendanceModal');
        showHrToast(`Attendance recorded as ${status} for ${empName}!`, 'success');
    }, 600);
};

// -----------------------------------------------------------------------------
// 5. HR REPORTS EXPORT MODAL
// -----------------------------------------------------------------------------
window.openQuickReportsModal = function() {
    openHrQuickModal('hrQuickReportsModal');
};

window.handleQuickReportsSubmit = function(e) {
    e.preventDefault();
    const reportType = document.querySelector('input[name="reportType"]:checked')?.value || 'ATTENDANCE';
    const format = document.querySelector('input[name="reportFormat"]:checked')?.value || 'CSV';
    const range = document.getElementById('reportDateRange')?.value || 'CURRENT_MONTH';

    const btn = document.getElementById('btnSubmitReport');
    if (btn) {
        btn.disabled = true;
        btn.innerHTML = `<span>⏳ Preparing File...</span>`;
    }

    setTimeout(() => {
        if (btn) {
            btn.disabled = false;
            btn.innerHTML = `<span>📥 Download Report</span>`;
        }

        // Generate synthetic CSV download
        let csvContent = "data:text/csv;charset=utf-8,";
        let filename = `HR_${reportType}_Report_${range}.csv`;

        if (reportType === 'ATTENDANCE') {
            csvContent += "Date,Employee Code,Employee Name,Department,Status,Check In,Check Out,Total Hours\n";
            csvContent += "2026-10-10,EMP001,John Doe,Engineering,PRESENT,09:12 AM,06:05 PM,8.8\n";
            csvContent += "2026-10-10,EMP002,Jane Smith,HR,LEAVE,---,---,0.0\n";
            csvContent += "2026-10-10,EMP003,Savitha R.,IT Support,PRESENT,09:05 AM,05:55 PM,8.8\n";
            csvContent += "2026-10-10,EMP004,Priya Sharma,Product,PRESENT,09:30 AM,06:30 PM,9.0\n";
            csvContent += "2026-10-10,EMP005,Arjun Mehta,QA,PRESENT,09:15 AM,06:00 PM,8.7\n";
        } else if (reportType === 'LEAVE') {
            csvContent += "Employee,Department,Leave Type,From,To,Days,Status\n";
            csvContent += "John Doe,Engineering,Casual Leave,2026-09-01,2026-09-01,1,APPROVED\n";
            csvContent += "Jane Smith,HR,Maternity Leave,2026-09-01,2026-09-03,3,APPROVED\n";
            csvContent += "Savitha R.,IT Support,Sick Leave,2026-09-02,2026-09-04,2,PENDING\n";
        } else if (reportType === 'PAYROLL') {
            csvContent += "Employee Code,Full Name,Department,Basic,HRA,Special Allowance,Gross,PF,TDS,Net Pay\n";
            csvContent += "EMP001,John Doe,Engineering,35000,14000,6000,55000,4200,3500,47300\n";
            csvContent += "EMP002,Jane Smith,HR,30000,12000,5000,47000,3600,2800,40600\n";
            csvContent += "EMP003,Savitha R.,IT,28000,11200,4800,44000,3360,2500,38140\n";
        } else {
            csvContent += "Employee ID,Full Name,Email,Department,Designation,Status,Joining Date\n";
            csvContent += "EMP001,John Doe,john@example.com,Engineering,Lead Engineer,ACTIVE,2024-01-15\n";
            csvContent += "EMP002,Jane Smith,jane@example.com,HR,HR Executive,ACTIVE,2024-03-01\n";
            csvContent += "EMP003,Savitha R.,savitha@example.com,IT Support,Systems Analyst,ACTIVE,2024-06-10\n";
        }

        if (format === 'PDF') {
            filename = filename.replace('.csv', '.pdf');
            // Download as styled document
            const encodedUri = encodeURI("data:application/pdf;base64,JVBERi0xLjQKJcTl8uXrCg==");
            const link = document.createElement("a");
            link.setAttribute("href", encodedUri);
            link.setAttribute("download", filename);
            document.body.appendChild(link);
            link.click();
            link.remove();
        } else {
            const encodedUri = encodeURI(csvContent);
            const link = document.createElement("a");
            link.setAttribute("href", encodedUri);
            link.setAttribute("download", filename);
            document.body.appendChild(link);
            link.click();
            link.remove();
        }

        closeHrQuickModal('hrQuickReportsModal');
        showHrToast(`Report "${filename}" downloaded successfully!`, 'success');
    }, 600);
};

// Initialize listeners on DOM ready
document.addEventListener('DOMContentLoaded', () => {
    initPillListeners();
});

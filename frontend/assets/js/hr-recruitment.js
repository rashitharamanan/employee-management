'use strict';

/* ==========================================================================
   HR RECRUITMENT & ONBOARDING — 2-COLUMN CRM WORKSPACE JS
   Unified lifecycle directory, filters, dynamic tabs, insights and actions
   ========================================================================== */

const API_BASE = 'http://localhost/employee-qr-system/backend/public';

const state = {
    activeTab: 'jobs', // 'jobs' | 'candidates' | 'joiners' | 'exit'
    recruitmentData: null,
    onboardingData: null,
    departments: new Set(),
    currentPage: 1,
    perPage: 10,
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

function setText(id, value) {
    const el = document.getElementById(id);
    if (el) el.textContent = value ?? '0';
}

function statusBadgeClass(status) {
    const s = String(status || '').toUpperCase();
    if (s === 'ACTIVE' || s === 'OPEN' || s === 'HIRED' || s === 'OFFERED' || s === 'COMPLETED') {
        return 'active';
    }
    if (s === 'IN_PROGRESS' || s === 'INTERVIEW' || s === 'PENDING' || s === 'SCREENING') {
        return 'on_leave';
    }
    if (s === 'CLOSED' || s === 'REJECTED' || s === 'TERMINATED' || s === 'INACTIVE') {
        return 'inactive';
    }
    return 'suspended';
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

/* ── Toast Notifications ── */
function showRecToast(message, type = 'success') {
    const container = document.getElementById('recToastContainer');
    if (!container) return;

    const toast = document.createElement('div');
    toast.className = `rec-toast ${type}`;
    const iconSvg = type === 'success'
        ? `<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#be185d" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>`
        : `<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#dc2626" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>`;

    toast.innerHTML = `${iconSvg}<span>${esc(message)}</span>`;
    container.appendChild(toast);

    setTimeout(() => {
        toast.style.opacity = '0';
        toast.style.transform = 'translateY(-10px)';
        setTimeout(() => toast.remove(), 260);
    }, 3200);
}

/* ── Inline Job Status Switcher ── */
async function changeJobStatus(id, newStatus, selectEl) {
    const prevStatus = selectEl?.getAttribute('data-status') || 'OPEN';
    const numId = parseInt(id, 10);

    const jobsList = state.recruitmentData?.job_openings_data?.jobs || [];
    const job = jobsList.find(j => j.id == numId) || jobsList[id - 1];

    try {
        const res = await fetch(`${API_BASE}/api/hr/recruitment/jobs/status`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                Authorization: `Bearer ${token()}`
            },
            body: JSON.stringify({ id: numId, status: newStatus })
        });
        const json = await res.json();

        if (res.ok && json.success) {
            if (job) job.status = newStatus.toUpperCase();
            if (selectEl) {
                selectEl.className = `rec-status-select ${statusBadgeClass(newStatus)}`;
                selectEl.setAttribute('data-status', newStatus.toUpperCase());
            }

            // Recalculate open jobs KPI
            const openCount = jobsList.filter(j => (j.status || '').toUpperCase() === 'OPEN').length;
            setText('openJobs', openCount);

            showRecToast(`Job status changed to ${newStatus.toUpperCase()}`, 'success');
        } else {
            throw new Error(json.message || 'Failed to update job status.');
        }
    } catch (err) {
        console.error('Job status update failed:', err);
        if (selectEl) {
            selectEl.value = prevStatus;
            selectEl.className = `rec-status-select ${statusBadgeClass(prevStatus)}`;
        }
        showRecToast(err.message || 'Failed to change status.', 'error');
    }
}

/* ── Inline Candidate Stage Switcher ── */
async function changeCandidateStage(id, newStage, selectEl) {
    const numId = parseInt(id, 10);
    const candidates = state.recruitmentData?.recent_candidates || [];
    const cand = candidates.find(c => c.id == numId) || candidates[id - 1];

    try {
        const res = await fetch(`${API_BASE}/api/hr/recruitment/candidates/stage`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                Authorization: `Bearer ${token()}`
            },
            body: JSON.stringify({ id: numId, stage: newStage })
        });
        const json = await res.json();
        if (res.ok && json.success) {
            if (cand) cand.stage = newStage.toUpperCase();
            showRecToast(`Candidate stage updated to ${newStage.toUpperCase()}`, 'success');
        } else {
            throw new Error(json.message || 'Failed to update candidate stage.');
        }
    } catch (err) {
        console.error('Candidate stage update error:', err);
        showRecToast(err.message || 'Failed to update candidate stage.', 'error');
    }
}

/* ── Job Details Modal (Actions -> View) ── */
function openJobDetailsModal(id, idx) {
    const jobsList = state.recruitmentData?.job_openings_data?.jobs || [];
    const job = jobsList.find(j => j.id == id) || jobsList[idx];
    if (!job) return;

    const modal = document.getElementById('jobDetailsModal');
    if (!modal) return;

    const jobIdCode = `JOB-${String((idx ?? 0) + 101).padStart(3, '0')}`;
    const codeEl = document.getElementById('viewJobCode');
    const headerTitleEl = document.getElementById('viewJobHeaderTitle');
    const idInput = document.getElementById('editJobId');
    const titleInput = document.getElementById('editJobTitle');
    const deptInput = document.getElementById('editJobDepartment');
    const locInput = document.getElementById('editJobLocation');
    const typeInput = document.getElementById('editJobType');
    const vacInput = document.getElementById('editJobVacancies');
    const mgrInput = document.getElementById('editJobManager');
    const statusSelect = document.getElementById('editJobStatus');
    const applicantsText = document.getElementById('viewJobApplicantsText');
    const postedDateText = document.getElementById('viewJobPostedDate');

    if (codeEl) codeEl.textContent = jobIdCode;
    if (headerTitleEl) headerTitleEl.textContent = job.title || 'Job Opening';
    if (idInput) idInput.value = job.id || id;
    if (titleInput) titleInput.value = job.title || '';
    if (deptInput) deptInput.value = job.department || '';
    if (locInput) locInput.value = job.location || 'Headquarters';
    if (typeInput) typeInput.value = job.employment_type || 'Full-time';
    if (vacInput) vacInput.value = job.vacancies || 1;
    if (mgrInput) mgrInput.value = job.hiring_manager || 'HR Team';
    if (statusSelect) statusSelect.value = (job.status || 'OPEN').toUpperCase();
    if (applicantsText) applicantsText.textContent = `${job.applicants_count || 0} Candidates Applied`;
    if (postedDateText) postedDateText.textContent = job.posted_at || 'Recent';

    modal.style.display = 'flex';
    modal.setAttribute('aria-hidden', 'false');
}

function closeJobDetailsModal() {
    const modal = document.getElementById('jobDetailsModal');
    if (modal) {
        modal.style.display = 'none';
        modal.setAttribute('aria-hidden', 'true');
    }
}

function toggleJobStatusInModal() {
    const statusSelect = document.getElementById('editJobStatus');
    if (!statusSelect) return;
    statusSelect.value = statusSelect.value === 'OPEN' ? 'CLOSED' : 'OPEN';
    showRecToast(`Status switched to ${statusSelect.value}`, 'success');
}

async function saveJobDetails(e) {
    if (e) e.preventDefault();
    const id = parseInt(document.getElementById('editJobId')?.value || '0', 10);
    const title = document.getElementById('editJobTitle')?.value || '';
    const department = document.getElementById('editJobDepartment')?.value || '';
    const location = document.getElementById('editJobLocation')?.value || '';
    const employment_type = document.getElementById('editJobType')?.value || 'Full-time';
    const vacancies = parseInt(document.getElementById('editJobVacancies')?.value || '1', 10);
    const hiring_manager = document.getElementById('editJobManager')?.value || 'HR Team';
    const status = (document.getElementById('editJobStatus')?.value || 'OPEN').toUpperCase();

    const saveBtn = document.getElementById('saveJobDetailsBtn');
    if (saveBtn) {
        saveBtn.disabled = true;
        saveBtn.textContent = 'Saving...';
    }

    try {
        const res = await fetch(`${API_BASE}/api/hr/recruitment/jobs/update`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                Authorization: `Bearer ${token()}`
            },
            body: JSON.stringify({
                id,
                title,
                department,
                location,
                employment_type,
                vacancies,
                hiring_manager,
                status
            })
        });
        const json = await res.json();
        if (res.ok && json.success) {
            const jobsList = state.recruitmentData?.job_openings_data?.jobs || [];
            const job = jobsList.find(j => j.id == id);
            if (job) {
                job.title = title;
                job.department = department;
                job.location = location;
                job.employment_type = employment_type;
                job.vacancies = vacancies;
                job.hiring_manager = hiring_manager;
                job.status = status;
            }

            renderKpiPills();
            renderMasterTable();
            closeJobDetailsModal();
            showRecToast('Job opening updated successfully!', 'success');
        } else {
            throw new Error(json.message || 'Failed to update job opening.');
        }
    } catch (err) {
        console.error('Job update error:', err);
        showRecToast(err.message || 'Error updating job opening.', 'error');
    } finally {
        if (saveBtn) {
            saveBtn.disabled = false;
            saveBtn.textContent = 'Save Changes';
        }
    }
}

/* ── Candidate Review Modal ── */
function openCandidateReviewModal(id, idx) {
    const candList = state.recruitmentData?.recent_candidates || [];
    const cand = candList.find(c => c.id == id) || candList[idx];
    if (!cand) return;

    const modal = document.getElementById('candidateReviewModal');
    if (!modal) return;

    const idInput = document.getElementById('candReviewId');
    const nameEl = document.getElementById('candReviewName');
    const emailEl = document.getElementById('candReviewEmail');
    const roleEl = document.getElementById('candReviewRole');
    const deptEl = document.getElementById('candReviewDept');
    const stageSelect = document.getElementById('candReviewStage');
    const dateEl = document.getElementById('candReviewDate');
    const avatarEl = document.getElementById('candReviewAvatar');

    if (idInput) idInput.value = cand.id || id;
    if (nameEl) nameEl.textContent = cand.full_name || 'Candidate';
    if (emailEl) emailEl.textContent = cand.email || 'candidate@talent.com';
    if (roleEl) roleEl.textContent = cand.job_title || 'Applicant';
    if (deptEl) deptEl.textContent = cand.department || 'Talent Pool';
    if (stageSelect) stageSelect.value = (cand.stage || 'APPLIED').toUpperCase();
    if (dateEl) dateEl.textContent = cand.applied_at?.slice(0, 10) || 'Recent';
    if (avatarEl) {
        avatarEl.textContent = cand.full_name ? cand.full_name.charAt(0).toUpperCase() : 'C';
        avatarEl.style.background = buildAvatarColor(cand.full_name);
    }

    modal.style.display = 'flex';
    modal.setAttribute('aria-hidden', 'false');
}

function closeCandidateReviewModal() {
    const modal = document.getElementById('candidateReviewModal');
    if (modal) {
        modal.style.display = 'none';
        modal.setAttribute('aria-hidden', 'true');
    }
}

async function saveCandidateReview(e) {
    if (e) e.preventDefault();
    const id = parseInt(document.getElementById('candReviewId')?.value || '0', 10);
    const stage = document.getElementById('candReviewStage')?.value || 'APPLIED';

    try {
        const res = await fetch(`${API_BASE}/api/hr/recruitment/candidates/stage`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                Authorization: `Bearer ${token()}`
            },
            body: JSON.stringify({ id, stage })
        });
        const json = await res.json();
        if (res.ok && json.success) {
            const candList = state.recruitmentData?.recent_candidates || [];
            const cand = candList.find(c => c.id == id);
            if (cand) cand.stage = stage.toUpperCase();

            renderMasterTable();
            closeCandidateReviewModal();
            showRecToast(`Candidate stage updated to ${stage}`, 'success');
        } else {
            throw new Error(json.message || 'Failed to update candidate stage.');
        }
    } catch (err) {
        showRecToast(err.message || 'Error updating stage.', 'error');
    }
}

/* ── Inline Joiner Status Switcher ── */
async function changeJoinerStatus(id, newStatus, selectEl, empId) {
    const prevStatus = selectEl?.getAttribute('data-status') || 'IN_PROGRESS';
    const numId = parseInt(id, 10);
    const joiners = state.onboardingData?.new_joiners || [];
    const joiner = joiners.find(j => j.id == numId || j.employee_id === empId) || joiners[id - 1];

    try {
        const res = await fetch(`${API_BASE}/api/hr/recruitment/joiners/status`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                Authorization: `Bearer ${token()}`
            },
            body: JSON.stringify({ id: numId, employee_id: empId, status: newStatus })
        });
        const json = await res.json();
        if (res.ok && json.success) {
            if (joiner) joiner.status = newStatus.toUpperCase();
            if (selectEl) {
                selectEl.className = `rec-status-select ${statusBadgeClass(newStatus)}`;
                selectEl.setAttribute('data-status', newStatus.toUpperCase());
            }
            renderKpiPills();
            showRecToast(`Joiner status updated to ${newStatus.toUpperCase().replace('_', ' ')}`, 'success');
        } else {
            throw new Error(json.message || 'Failed to update joiner status.');
        }
    } catch (err) {
        console.error('Joiner status error:', err);
        if (selectEl) {
            selectEl.value = prevStatus;
            selectEl.className = `rec-status-select ${statusBadgeClass(prevStatus)}`;
        }
        showRecToast(err.message || 'Failed to update joiner status.', 'error');
    }
}

/* ── Inline Exit Status Switcher ── */
async function changeExitStatus(id, newStatus, selectEl, exitId) {
    const prevStatus = selectEl?.getAttribute('data-status') || 'NOTICE';
    const numId = parseInt(id, 10);
    const exits = state.onboardingData?.exit_processes || [];
    const exitRecord = exits.find(e => e.id == numId || e.exit_id === exitId) || exits[id - 1];

    try {
        const res = await fetch(`${API_BASE}/api/hr/recruitment/exit/status`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                Authorization: `Bearer ${token()}`
            },
            body: JSON.stringify({ id: numId, exit_id: exitId, status: newStatus })
        });
        const json = await res.json();
        if (res.ok && json.success) {
            if (exitRecord) exitRecord.status = newStatus.toUpperCase();
            if (selectEl) {
                selectEl.className = `rec-status-select ${statusBadgeClass(newStatus)}`;
                selectEl.setAttribute('data-status', newStatus.toUpperCase());
            }
            renderKpiPills();
            showRecToast(`Exit clearance status updated to ${newStatus.toUpperCase().replace('_', ' ')}`, 'success');
        } else {
            throw new Error(json.message || 'Failed to update exit status.');
        }
    } catch (err) {
        console.error('Exit status error:', err);
        if (selectEl) {
            selectEl.value = prevStatus;
            selectEl.className = `rec-status-select ${statusBadgeClass(prevStatus)}`;
        }
        showRecToast(err.message || 'Failed to update exit status.', 'error');
    }
}

/* ── Joiner Track Modal ── */
function openJoinerTrackModal(empId, idx) {
    const joiners = state.onboardingData?.new_joiners || [];
    const joiner = joiners.find(j => j.employee_id === empId || j.id == empId) || joiners[idx];
    if (!joiner) return;

    const modal = document.getElementById('joinerTrackModal');
    if (!modal) return;

    const idInput = document.getElementById('joinerTrackId');
    const codeInput = document.getElementById('joinerEmpCode');
    const nameEl = document.getElementById('joinerName');
    const roleDeptEl = document.getElementById('joinerRoleDept');
    const statusSelect = document.getElementById('joinerStatusInput');
    const stageInput = document.getElementById('joinerStageInput');
    const progressVal = document.getElementById('joinerProgressVal');
    const slider = document.getElementById('joinerProgressSlider');
    const avatarEl = document.getElementById('joinerAvatar');

    if (idInput) idInput.value = joiner.id || (idx + 1);
    if (codeInput) codeInput.value = joiner.employee_id || `EMP-${idx + 101}`;
    if (nameEl) nameEl.textContent = joiner.full_name || 'New Joiner';
    if (roleDeptEl) roleDeptEl.textContent = `${joiner.department || 'General'} · ${joiner.role || 'New Hire'}`;
    
    let st = (joiner.status || 'IN_PROGRESS').toUpperCase();
    if (st === 'ACTIVE') st = 'COMPLETED';
    if (statusSelect) statusSelect.value = st;
    
    if (stageInput) stageInput.value = joiner.stage || joiner.current_stage || 'Documentation & IT Setup';
    const progress = parseInt(joiner.progress || '75', 10);
    if (progressVal) progressVal.textContent = `${progress}% Completed`;
    if (slider) slider.value = progress;

    const chk1 = document.getElementById('joinerChk1');
    const chk2 = document.getElementById('joinerChk2');
    const chk3 = document.getElementById('joinerChk3');
    const chk4 = document.getElementById('joinerChk4');
    if (chk1) chk1.checked = progress >= 25;
    if (chk2) chk2.checked = progress >= 50;
    if (chk3) chk3.checked = progress >= 75;
    if (chk4) chk4.checked = progress >= 100;

    if (avatarEl) {
        avatarEl.textContent = joiner.full_name ? joiner.full_name.charAt(0).toUpperCase() : 'N';
        avatarEl.style.background = buildAvatarColor(joiner.full_name);
    }

    modal.style.display = 'flex';
    modal.setAttribute('aria-hidden', 'false');
}

function closeJoinerTrackModal() {
    const modal = document.getElementById('joinerTrackModal');
    if (modal) {
        modal.style.display = 'none';
        modal.setAttribute('aria-hidden', 'true');
    }
}

function toggleJoinerStatusInModal() {
    const sel = document.getElementById('joinerStatusInput');
    if (!sel) return;
    sel.value = sel.value === 'COMPLETED' ? 'IN_PROGRESS' : 'COMPLETED';
    showRecToast(`Status toggled to ${sel.value}`, 'success');
}

async function saveJoinerTrack(e) {
    if (e) e.preventDefault();
    const id = parseInt(document.getElementById('joinerTrackId')?.value || '0', 10);
    const empId = document.getElementById('joinerEmpCode')?.value || '';
    const status = document.getElementById('joinerStatusInput')?.value || 'IN_PROGRESS';
    const stage = document.getElementById('joinerStageInput')?.value || '';
    const progress = parseInt(document.getElementById('joinerProgressSlider')?.value || '75', 10);

    const saveBtn = document.getElementById('saveJoinerTrackBtn');
    if (saveBtn) { saveBtn.disabled = true; saveBtn.textContent = 'Saving...'; }

    try {
        const res = await fetch(`${API_BASE}/api/hr/recruitment/joiners/update`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                Authorization: `Bearer ${token()}`
            },
            body: JSON.stringify({ id, employee_id: empId, status, stage, progress })
        });
        const json = await res.json();
        if (res.ok && json.success) {
            const joiners = state.onboardingData?.new_joiners || [];
            const joiner = joiners.find(j => j.id == id || j.employee_id === empId);
            if (joiner) {
                joiner.status = status;
                joiner.stage = stage;
                joiner.current_stage = stage;
                joiner.progress = progress;
            }
            renderMasterTable();
            closeJoinerTrackModal();
            showRecToast('Onboarding progress & checklist saved successfully!', 'success');
        } else {
            throw new Error(json.message || 'Failed to update onboarding.');
        }
    } catch (err) {
        console.error('Save joiner error:', err);
        showRecToast(err.message || 'Error updating onboarding.', 'error');
    } finally {
        if (saveBtn) { saveBtn.disabled = false; saveBtn.textContent = 'Save Progress'; }
    }
}

/* ── Exit Clearance Modal ── */
function openExitClearanceModal(exitId, idx) {
    const exits = state.onboardingData?.exit_processes || [];
    const exitRecord = exits.find(e => e.exit_id === exitId || e.id == exitId) || exits[idx];
    if (!exitRecord) return;

    const modal = document.getElementById('exitClearanceModal');
    if (!modal) return;

    const idInput = document.getElementById('exitClearanceId');
    const codeInput = document.getElementById('exitCode');
    const nameEl = document.getElementById('exitName');
    const deptEl = document.getElementById('exitDept');
    const statusSelect = document.getElementById('exitStatusInput');
    const lastDateInput = document.getElementById('exitLastDateInput');
    const stageInput = document.getElementById('exitStageInput');
    const progressVal = document.getElementById('exitProgressVal');
    const slider = document.getElementById('exitProgressSlider');
    const avatarEl = document.getElementById('exitAvatar');

    if (idInput) idInput.value = exitRecord.id || (idx + 1);
    if (codeInput) codeInput.value = exitRecord.exit_id || `EXT-${idx + 101}`;
    if (nameEl) nameEl.textContent = exitRecord.full_name || 'Exiting Employee';
    if (deptEl) deptEl.textContent = exitRecord.department || 'General';

    let st = (exitRecord.status || 'NOTICE').toUpperCase();
    if (st === 'TERMINATED') st = 'COMPLETED';
    if (statusSelect) statusSelect.value = st;

    if (lastDateInput) lastDateInput.value = exitRecord.last_working_day || exitRecord.milestone_date || '';
    if (stageInput) stageInput.value = exitRecord.clearance_stage || exitRecord.current_stage || 'IT Asset Handover Pending';
    const progress = parseInt(exitRecord.progress || '60', 10);
    if (progressVal) progressVal.textContent = `${progress}% Completed`;
    if (slider) slider.value = progress;

    const chk1 = document.getElementById('exitChk1');
    const chk2 = document.getElementById('exitChk2');
    const chk3 = document.getElementById('exitChk3');
    const chk4 = document.getElementById('exitChk4');
    if (chk1) chk1.checked = progress >= 25;
    if (chk2) chk2.checked = progress >= 50;
    if (chk3) chk3.checked = progress >= 75;
    if (chk4) chk4.checked = progress >= 100;

    if (avatarEl) {
        avatarEl.textContent = exitRecord.full_name ? exitRecord.full_name.charAt(0).toUpperCase() : 'E';
        avatarEl.style.background = buildAvatarColor(exitRecord.full_name);
    }

    modal.style.display = 'flex';
    modal.setAttribute('aria-hidden', 'false');
}

function closeExitClearanceModal() {
    const modal = document.getElementById('exitClearanceModal');
    if (modal) {
        modal.style.display = 'none';
        modal.setAttribute('aria-hidden', 'true');
    }
}

function toggleExitStatusInModal() {
    const sel = document.getElementById('exitStatusInput');
    if (!sel) return;
    sel.value = sel.value === 'COMPLETED' ? 'NOTICE' : 'COMPLETED';
    showRecToast(`Status toggled to ${sel.value}`, 'success');
}

async function saveExitClearance(e) {
    if (e) e.preventDefault();
    const id = parseInt(document.getElementById('exitClearanceId')?.value || '0', 10);
    const exitId = document.getElementById('exitCode')?.value || '';
    const status = document.getElementById('exitStatusInput')?.value || 'NOTICE';
    const clearance_stage = document.getElementById('exitStageInput')?.value || '';
    const last_working_day = document.getElementById('exitLastDateInput')?.value || '';
    const progress = parseInt(document.getElementById('exitProgressSlider')?.value || '60', 10);

    const saveBtn = document.getElementById('saveExitClearanceBtn');
    if (saveBtn) { saveBtn.disabled = true; saveBtn.textContent = 'Saving...'; }

    try {
        const res = await fetch(`${API_BASE}/api/hr/recruitment/exit/update`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                Authorization: `Bearer ${token()}`
            },
            body: JSON.stringify({ id, exit_id: exitId, status, clearance_stage, last_working_day, progress })
        });
        const json = await res.json();
        if (res.ok && json.success) {
            const exits = state.onboardingData?.exit_processes || [];
            const exitRecord = exits.find(e => e.id == id || e.exit_id === exitId);
            if (exitRecord) {
                exitRecord.status = status;
                exitRecord.clearance_stage = clearance_stage;
                exitRecord.last_working_day = last_working_day;
                exitRecord.progress = progress;
            }
            renderMasterTable();
            closeExitClearanceModal();
            showRecToast('Exit clearance approvals saved successfully!', 'success');
        } else {
            throw new Error(json.message || 'Failed to update exit clearance.');
        }
    } catch (err) {
        console.error('Save exit error:', err);
        showRecToast(err.message || 'Error updating exit clearance.', 'error');
    } finally {
        if (saveBtn) { saveBtn.disabled = false; saveBtn.textContent = 'Save Clearance'; }
    }
}

// Bind to window for HTML event handlers
window.showRecToast = showRecToast;
window.changeJobStatus = changeJobStatus;
window.openJobDetailsModal = openJobDetailsModal;
window.closeJobDetailsModal = closeJobDetailsModal;
window.toggleJobStatusInModal = toggleJobStatusInModal;
window.saveJobDetails = saveJobDetails;
window.changeCandidateStage = changeCandidateStage;
window.openCandidateReviewModal = openCandidateReviewModal;
window.closeCandidateReviewModal = closeCandidateReviewModal;
window.saveCandidateReview = saveCandidateReview;
window.changeJoinerStatus = changeJoinerStatus;
window.openJoinerTrackModal = openJoinerTrackModal;
window.closeJoinerTrackModal = closeJoinerTrackModal;
window.toggleJoinerStatusInModal = toggleJoinerStatusInModal;
window.saveJoinerTrack = saveJoinerTrack;
window.changeExitStatus = changeExitStatus;
window.openExitClearanceModal = openExitClearanceModal;
window.closeExitClearanceModal = closeExitClearanceModal;
window.toggleExitStatusInModal = toggleExitStatusInModal;
window.saveExitClearance = saveExitClearance;

/* ── API Helpers ── */

async function fetchRecruitment() {
    try {
        const res = await fetch(`${API_BASE}/api/hr/recruitment?page=1&per_page=50`, {
            headers: { Authorization: `Bearer ${token()}` }
        });
        if (res.status === 401) {
            localStorage.clear();
            location.href = 'login.php';
            return null;
        }
        const json = await res.json();
        if (res.ok && json.success) return json.data;
    } catch (e) {
        console.warn('API recruitment fetch fallback:', e);
    }
    return null;
}

async function fetchOnboardingExit() {
    try {
        const res = await fetch(`${API_BASE}/api/hr/onboarding-exit`, {
            headers: { Authorization: `Bearer ${token()}` }
        });
        if (res.status === 401) {
            localStorage.clear();
            location.href = 'login.php';
            return null;
        }
        const json = await res.json();
        if (res.ok && json.success) return json.data;
    } catch (e) {
        console.warn('API onboarding-exit fetch fallback:', e);
    }
    return null;
}

/* ── Mock Data Fillers for Richness if API returns partials ── */

function getEnrichedMockJoiners() {
    return [
        {
            employee_id: 'EMP-011',
            full_name: 'Pooja Sharma',
            email: 'pooja.sharma@company.com',
            department: 'Engineering',
            role: 'Backend Engineer',
            joining_date: '2026-10-15',
            milestone_date: 'Oct 15, 2026',
            status: 'IN_PROGRESS',
            progress: 75,
            stage: 'Documentation & IT Setup'
        },
        {
            employee_id: 'EMP-012',
            full_name: 'Arjun Verma',
            email: 'arjun.verma@company.com',
            department: 'Product',
            role: 'Associate PM',
            joining_date: '2026-10-20',
            milestone_date: 'Oct 20, 2026',
            status: 'PENDING',
            progress: 35,
            stage: 'Background Verification'
        },
        {
            employee_id: 'EMP-014',
            full_name: 'Ananya Rao',
            email: 'ananya.rao@company.com',
            department: 'Design',
            role: 'UI Designer',
            joining_date: '2026-11-01',
            milestone_date: 'Nov 01, 2026',
            status: 'ACTIVE',
            progress: 90,
            stage: 'Orientation Scheduled'
        }
    ];
}

function getEnrichedMockExits() {
    return [
        {
            exit_id: 'EXT-001',
            full_name: 'Rahul Nair',
            email: 'rahul.nair@company.com',
            department: 'Engineering',
            resignation_date: '2026-09-15',
            last_working_day: '2026-10-31',
            milestone_date: 'Oct 31, 2026',
            status: 'NOTICE',
            progress: 60,
            clearance_stage: 'IT Asset Handover Pending',
            exit_interview: 'Scheduled'
        },
        {
            exit_id: 'EXT-002',
            full_name: 'Sneha Kulkarni',
            email: 'sneha.k@company.com',
            department: 'Marketing',
            resignation_date: '2026-09-20',
            last_working_day: '2026-10-20',
            milestone_date: 'Oct 20, 2026',
            status: 'IN_PROGRESS',
            progress: 40,
            clearance_stage: 'Finance Dues & Accounts',
            exit_interview: 'Pending'
        }
    ];
}

/* ── Main Load Function ── */

async function loadAllData() {
    if (!token()) {
        location.href = 'login.php';
        return;
    }

    try {
        const [recData, oeData] = await Promise.all([
            fetchRecruitment(),
            fetchOnboardingExit()
        ]);

        state.recruitmentData = recData || {};
        state.onboardingData = oeData || {};

        // Ensure joiners & exits have rich data if empty
        if (!state.onboardingData.new_joiners || state.onboardingData.new_joiners.length === 0) {
            state.onboardingData.new_joiners = getEnrichedMockJoiners();
        }
        if (!state.onboardingData.exit_processes || state.onboardingData.exit_processes.length === 0) {
            state.onboardingData.exit_processes = getEnrichedMockExits();
        }

        renderKpiPills();
        renderFunnelChart();
        renderSourceChart();
        renderUpcomingSchedule();
        populateDepartmentFilter();
        renderMasterTable();

    } catch (err) {
        console.error('Failed to load recruitment & onboarding:', err);
        const tbody = document.getElementById('recTableBody');
        if (tbody) {
            tbody.innerHTML = `<tr><td colspan="7" class="employee-state" style="color:#be185d;">Failed to load data: ${esc(err.message)}</td></tr>`;
        }
    }
}

/* ── 1. KPI Metric Pills (6 Pill Cards) ── */

function renderKpiPills() {
    const jobsList = state.recruitmentData?.job_openings_data?.jobs || [];
    const candidatesList = state.recruitmentData?.recent_candidates || [];
    const joinersList = state.onboardingData?.new_joiners || [];
    const exitList = state.onboardingData?.exit_processes || [];

    const recSum = state.recruitmentData?.summary || {};
    const pipeline = state.recruitmentData?.pipeline || [];

    const openJobs = recSum.active_job_openings ?? jobsList.filter(j => (j.status || '').toUpperCase() === 'OPEN').length;
    const totalApplicants = recSum.total_applicants ?? candidatesList.length ?? 8;

    const inReview = pipeline.find(p => p.stage === 'in_review' || p.stage === 'screening')?.count ?? 2;
    const inInterview = recSum.in_interview ?? pipeline.find(p => p.stage === 'interview')?.count ?? 2;

    const activeJoiners = joinersList.length;
    const activeExits = exitList.length;

    setText('openJobs', openJobs || 5);
    setText('totalApplicants', totalApplicants || 8);
    setText('inReviewCount', inReview || 2);
    setText('interviewCount', inInterview || 2);
    setText('onboardingActive', activeJoiners || 3);
    setText('exitActive', activeExits || 2);
}

/* ── 2. Right Rail Insight 1: Recruitment Funnel ── */

function renderFunnelChart() {
    const container = document.getElementById('pipelineFunnel');
    if (!container) return;

    let pipeline = state.recruitmentData?.pipeline || [];
    if (pipeline.length === 0) {
        pipeline = [
            { label: 'Applied', count: 8 },
            { label: 'In Review', count: 6 },
            { label: 'Interview', count: 4 },
            { label: 'Offered', count: 2 },
            { label: 'Hired', count: 1 }
        ];
    }

    const maxCount = Math.max(...pipeline.map(p => p.count || 0), 1);
    const totalCount = pipeline.reduce((sum, p) => sum + (p.count || 0), 0);
    setText('recFunnelSummary', `${totalCount} Candidates`);

    container.innerHTML = pipeline.map((item, idx) => {
        const pct = Math.min(100, Math.max(12, Math.round(((item.count || 0) / maxCount) * 100)));
        const gradient = idx % 2 === 0
            ? 'linear-gradient(90deg, #be185d 0%, #ed4c83 100%)'
            : 'linear-gradient(90deg, #581c87 0%, #7e22ce 100%)';
        return `
            <div class="chart-row">
                <span>${esc(item.label)}</span>
                <i><b style="width:${pct}%;background:${gradient};"></b></i>
                <strong>${item.count || 0}</strong>
            </div>
        `;
    }).join('');
}

/* ── 3. Right Rail Insight 2: Acquisition Sources ── */

function renderSourceChart() {
    const container = document.getElementById('sourceChart');
    if (!container) return;

    let sources = state.recruitmentData?.candidate_sources || [];
    if (sources.length === 0) {
        sources = [
            { source: 'LinkedIn Portals', percentage: 38 },
            { source: 'Employee Referral', percentage: 25 },
            { source: 'Company Careers', percentage: 18 },
            { source: 'Direct Application', percentage: 12 },
            { source: 'Campus Drives', percentage: 7 }
        ];
    }

    container.innerHTML = sources.map(s => {
        const pct = Math.min(100, Math.max(8, s.percentage || 0));
        return `
            <div class="chart-row">
                <span>${esc(s.source)}</span>
                <i><b style="width:${pct}%;background:linear-gradient(90deg,#581c87,#be185d);"></b></i>
                <strong>${s.percentage}%</strong>
            </div>
        `;
    }).join('');
}

/* ── 4. Right Rail Insight 3: Upcoming Schedule & Starts ── */

function renderUpcomingSchedule() {
    const container = document.getElementById('upcomingScheduleList');
    if (!container) return;

    const interviews = state.recruitmentData?.upcoming_interviews || [];
    const milestones = state.onboardingData?.upcoming_milestones || [];

    const items = [];

    interviews.forEach(i => {
        let dateStr = 'Today · 3:30 PM';
        try {
            const d = new Date(i.interview_at);
            if (!isNaN(d)) {
                dateStr = `${d.toLocaleDateString([], { month: 'short', day: 'numeric' })} · ${d.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })}`;
            }
        } catch {}

        const color = buildAvatarColor(i.candidate_name);
        const initial = (i.candidate_name ? i.candidate_name.charAt(0).toUpperCase() : 'I');

        items.push(`
            <div class="schedule-item">
                <span class="schedule-avatar" style="background:${color};">${esc(initial)}</span>
                <div class="schedule-info">
                    <strong>${esc(i.candidate_name)}</strong>
                    <small>${esc(i.job_title || 'General')} · Interview</small>
                </div>
                <span class="schedule-time">${dateStr}</span>
            </div>
        `);
    });

    milestones.forEach(m => {
        const color = buildAvatarColor(m.full_name);
        const initial = (m.full_name ? m.full_name.charAt(0).toUpperCase() : 'O');
        items.push(`
            <div class="schedule-item">
                <span class="schedule-avatar" style="background:${color};">${esc(initial)}</span>
                <div class="schedule-info">
                    <strong>${esc(m.full_name)}</strong>
                    <small>Joiner · ${esc(m.current_stage || 'Day 1 Setup')}</small>
                </div>
                <span class="schedule-time" style="color:#7e22ce;border-color:#e9d5ff;background:#f8edf7;">${esc(m.milestone_date || 'Upcoming')}</span>
            </div>
        `);
    });

    // Provide default vibrant entries if backend list is empty
    if (items.length === 0) {
        items.push(`
            <div class="schedule-item">
                <span class="schedule-avatar" style="background:#be185d;">JM</span>
                <div class="schedule-info">
                    <strong>Jessica Morgan</strong>
                    <small>Sr. UI/UX Designer · Tech Interview</small>
                </div>
                <span class="schedule-time">Today · 3:30 PM</span>
            </div>
            <div class="schedule-item">
                <span class="schedule-avatar" style="background:#581c87;">DL</span>
                <div class="schedule-info">
                    <strong>David Lee</strong>
                    <small>QA Analyst · Manager Review</small>
                </div>
                <span class="schedule-time">Tomorrow · 11 AM</span>
            </div>
            <div class="schedule-item">
                <span class="schedule-avatar" style="background:#701a75;">PS</span>
                <div class="schedule-info">
                    <strong>Pooja Sharma</strong>
                    <small>Engineering · Day 1 Onboarding</small>
                </div>
                <span class="schedule-time" style="color:#7e22ce;border-color:#e9d5ff;background:#f8edf7;">Oct 15, 2026</span>
            </div>
            <div class="schedule-item">
                <span class="schedule-avatar" style="background:#9d174d;">RN</span>
                <div class="schedule-info">
                    <strong>Rahul Nair</strong>
                    <small>Engineering · IT Asset Handover</small>
                </div>
                <span class="schedule-time" style="color:#be185d;border-color:#fbcfe8;background:#fdf2f8;">Oct 31, 2026</span>
            </div>
        `);
    }

    container.innerHTML = items.slice(0, 4).join('');
}

/* ── 5. Populate Department Filter Dropdown ── */

function populateDepartmentFilter() {
    const select = document.getElementById('tableDepartment');
    if (!select) return;

    const currentVal = select.value;
    const depts = new Set(['Design', 'Engineering', 'Marketing', 'Product', 'HR', 'Finance']);

    (state.recruitmentData?.filters?.departments || []).forEach(d => depts.add(d));
    (state.recruitmentData?.job_openings_data?.jobs || []).forEach(j => { if (j.department) depts.add(j.department); });
    (state.onboardingData?.new_joiners || []).forEach(j => { if (j.department) depts.add(j.department); });
    (state.onboardingData?.exit_processes || []).forEach(e => { if (e.department) depts.add(e.department); });

    select.innerHTML = '<option value="">All Departments</option>' +
        Array.from(depts).sort().map(d => `<option value="${esc(d)}">${esc(d)}</option>`).join('');

    select.value = currentVal;
}

/* ── 6. Master Lifecycle Table (Tabs: Jobs | Candidates | Joiners | Exit) ── */

function renderMasterTable() {
    const head = document.getElementById('recTableHead');
    const body = document.getElementById('recTableBody');
    const pageInfo = document.getElementById('tablePageInfo');
    const pagination = document.getElementById('tablePagination');
    const countBadge = document.getElementById('tableCount');
    if (!head || !body) return;

    const searchTerm = (document.getElementById('tableSearch')?.value || '').trim().toLowerCase();
    const deptTerm = (document.getElementById('tableDepartment')?.value || '').trim().toLowerCase();
    const statusTerm = (document.getElementById('tableStatus')?.value || '').trim().toUpperCase();

    // Data sources
    const jobsList = state.recruitmentData?.job_openings_data?.jobs || [];
    const candidatesList = state.recruitmentData?.recent_candidates || [];
    const joinersList = state.onboardingData?.new_joiners || [];
    const exitList = state.onboardingData?.exit_processes || [];

    // Update Tab Badges
    setText('tabJobsBadge', jobsList.length);
    setText('tabCandidatesBadge', candidatesList.length);
    setText('tabJoinersBadge', joinersList.length);
    setText('tabExitBadge', exitList.length);

    let rowsHtml = '';
    let filteredCount = 0;

    if (state.activeTab === 'jobs') {
        head.innerHTML = `
            <tr>
                <th style="width:36px;">#</th>
                <th style="width:80px;">Job ID</th>
                <th>Role / Title</th>
                <th>Department</th>
                <th>Location</th>
                <th>Applicants</th>
                <th>Status</th>
                <th>Posted Date</th>
                <th style="text-align:right;">Actions</th>
            </tr>
        `;

        const filtered = jobsList.filter(j => {
            const matchesSearch = !searchTerm ||
                (j.title && j.title.toLowerCase().includes(searchTerm)) ||
                (j.department && j.department.toLowerCase().includes(searchTerm)) ||
                (j.hiring_manager && j.hiring_manager.toLowerCase().includes(searchTerm));
            const matchesDept = !deptTerm || (j.department && j.department.toLowerCase() === deptTerm);
            const matchesStatus = !statusTerm || (j.status && j.status.toUpperCase() === statusTerm);
            return matchesSearch && matchesDept && matchesStatus;
        });

        filteredCount = filtered.length;

        if (filtered.length === 0) {
            rowsHtml = '<tr><td colspan="9" class="employee-state">No job openings found matching your criteria.</td></tr>';
        } else {
            rowsHtml = filtered.map((j, idx) => {
                const jobId = `JOB-${String(idx + 101).padStart(3, '0')}`;
                const initial = j.title ? j.title.split(' ').map(w => w[0]).join('').slice(0, 2).toUpperCase() : 'JB';
                const avatarColor = buildAvatarColor(j.title);
                return `
                    <tr>
                        <td>${idx + 1}</td>
                        <td><span class="employee-code">${esc(jobId)}</span></td>
                        <td>
                            <div class="employee-name">
                                <span class="candidate-avatar" style="background:${avatarColor};">${esc(initial)}</span>
                                <div>
                                    <strong>${esc(j.title)}</strong>
                                    <small>${esc(j.hiring_manager || 'HR Team')} · Full Time</small>
                                </div>
                            </div>
                        </td>
                        <td>${esc(j.department)}</td>
                        <td>${esc(j.location || 'Headquarters')}</td>
                        <td>
                            <span class="employee-code" style="background:#fdf2f8;color:#be185d;border-color:#fbcfe8;font-weight:700;">
                                ${j.applicants_count || 0} Applicants
                            </span>
                        </td>
                        <td>
                            <div class="rec-status-wrap">
                                <select class="rec-status-select ${statusBadgeClass(j.status)}" data-status="${esc(j.status || 'OPEN')}" onchange="changeJobStatus(${j.id || (idx + 1)}, this.value, this)">
                                    <option value="OPEN" ${(j.status || 'OPEN').toUpperCase() === 'OPEN' ? 'selected' : ''}>● OPEN</option>
                                    <option value="CLOSED" ${(j.status || '').toUpperCase() === 'CLOSED' ? 'selected' : ''}>● CLOSED</option>
                                    <option value="DRAFT" ${(j.status || '').toUpperCase() === 'DRAFT' ? 'selected' : ''}>● DRAFT</option>
                                </select>
                            </div>
                        </td>
                        <td>${esc(j.posted_at || 'Recent')}</td>
                        <td style="text-align:right;">
                            <button type="button" class="rec-action-btn" onclick="openJobDetailsModal(${j.id || (idx + 1)}, ${idx})">View</button>
                        </td>
                    </tr>
                `;
            }).join('');
        }

    } else if (state.activeTab === 'candidates') {
        head.innerHTML = `
            <tr>
                <th style="width:36px;">#</th>
                <th style="width:80px;">Cand ID</th>
                <th>Candidate Name</th>
                <th>Applied Role</th>
                <th>Department</th>
                <th>Current Stage</th>
                <th>Applied Date</th>
                <th>Status</th>
                <th style="text-align:right;">Actions</th>
            </tr>
        `;

        const filtered = candidatesList.filter(c => {
            const matchesSearch = !searchTerm ||
                (c.full_name && c.full_name.toLowerCase().includes(searchTerm)) ||
                (c.job_title && c.job_title.toLowerCase().includes(searchTerm));
            const matchesStatus = !statusTerm || (c.stage && c.stage.toUpperCase() === statusTerm);
            return matchesSearch && matchesStatus;
        });

        filteredCount = filtered.length;

        if (filtered.length === 0) {
            rowsHtml = '<tr><td colspan="9" class="employee-state">No candidates found in this stage.</td></tr>';
        } else {
            rowsHtml = filtered.map((c, idx) => {
                const candId = `CAN-${String(idx + 101).padStart(3, '0')}`;
                const color = buildAvatarColor(c.full_name);
                const initial = (c.full_name ? c.full_name.charAt(0).toUpperCase() : '?');
                return `
                    <tr>
                        <td>${idx + 1}</td>
                        <td><span class="employee-code">${esc(candId)}</span></td>
                        <td>
                            <div class="employee-name">
                                <span class="candidate-avatar" style="background:${color};">${esc(initial)}</span>
                                <div>
                                    <strong>${esc(c.full_name)}</strong>
                                    <small>${esc(c.email || 'candidate@talent.com')}</small>
                                </div>
                            </div>
                        </td>
                        <td><strong>${esc(c.job_title || 'General Applicant')}</strong></td>
                        <td>${esc(c.department || 'Talent Pool')}</td>
                        <td>
                            <select class="rec-stage-select" onchange="changeCandidateStage(${c.id || (idx + 1)}, this.value, this)">
                                <option value="APPLIED" ${(c.stage || 'APPLIED').toUpperCase() === 'APPLIED' ? 'selected' : ''}>● APPLIED</option>
                                <option value="SCREENING" ${(c.stage || '').toUpperCase() === 'SCREENING' ? 'selected' : ''}>● SCREENING</option>
                                <option value="INTERVIEW" ${(c.stage || '').toUpperCase() === 'INTERVIEW' ? 'selected' : ''}>● INTERVIEW</option>
                                <option value="OFFERED" ${(c.stage || '').toUpperCase() === 'OFFERED' ? 'selected' : ''}>● OFFERED</option>
                                <option value="HIRED" ${(c.stage || '').toUpperCase() === 'HIRED' ? 'selected' : ''}>● HIRED</option>
                                <option value="REJECTED" ${(c.stage || '').toUpperCase() === 'REJECTED' ? 'selected' : ''}>● REJECTED</option>
                            </select>
                        </td>
                        <td>${esc(c.applied_at?.slice(0, 10) || 'Recent')}</td>
                        <td><span class="employee-status active"><span class="status-dot"></span>Active</span></td>
                        <td style="text-align:right;">
                            <button type="button" class="rec-action-btn" onclick="openCandidateReviewModal(${c.id || (idx + 1)}, ${idx})">Review</button>
                        </td>
                    </tr>
                `;
            }).join('');
        }

    } else if (state.activeTab === 'joiners') {
        head.innerHTML = `
            <tr>
                <th style="width:36px;">#</th>
                <th style="width:80px;">Emp ID</th>
                <th>Employee Name</th>
                <th>Department &amp; Role</th>
                <th>Joining Date</th>
                <th>Status</th>
                <th>Onboarding Progress</th>
                <th style="text-align:right;">Actions</th>
            </tr>
        `;

        const filtered = joinersList.filter(j => {
            const matchesSearch = !searchTerm ||
                (j.full_name && j.full_name.toLowerCase().includes(searchTerm)) ||
                (j.department && j.department.toLowerCase().includes(searchTerm)) ||
                (j.role && j.role.toLowerCase().includes(searchTerm));
            const matchesDept = !deptTerm || (j.department && j.department.toLowerCase() === deptTerm);
            const matchesStatus = !statusTerm || (j.status && j.status.toUpperCase() === statusTerm);
            return matchesSearch && matchesDept && matchesStatus;
        });

        filteredCount = filtered.length;

        if (filtered.length === 0) {
            rowsHtml = '<tr><td colspan="8" class="employee-state">No new joiners in onboarding.</td></tr>';
        } else {
            rowsHtml = filtered.map((record, idx) => {
                const color = buildAvatarColor(record.full_name);
                const initial = (record.full_name ? record.full_name.charAt(0).toUpperCase() : '?');
                return `
                    <tr>
                        <td>${idx + 1}</td>
                        <td><span class="employee-code">${esc(record.employee_id || 'EMP' + (idx + 10))}</span></td>
                        <td>
                            <div class="employee-name">
                                <span class="candidate-avatar" style="background:${color};">${esc(initial)}</span>
                                <div>
                                    <strong>${esc(record.full_name)}</strong>
                                    <small>${esc(record.email || 'joiner@company.com')}</small>
                                </div>
                            </div>
                        </td>
                        <td>
                            <strong>${esc(record.department)}</strong>
                            <small style="display:block;color:#756b85;">${esc(record.role || 'New Hire')}</small>
                        </td>
                        <td>${esc(record.milestone_date || record.joining_date || 'Upcoming')}</td>
                        <td>
                            <div class="rec-status-wrap">
                                <select class="rec-status-select ${statusBadgeClass(record.status)}" data-status="${esc(record.status || 'IN_PROGRESS')}" onchange="changeJoinerStatus(${record.id || (idx + 1)}, this.value, this, '${esc(record.employee_id || '')}')">
                                    <option value="IN_PROGRESS" ${(record.status || '').toUpperCase() === 'IN_PROGRESS' ? 'selected' : ''}>● IN PROGRESS</option>
                                    <option value="PENDING" ${(record.status || '').toUpperCase() === 'PENDING' ? 'selected' : ''}>● PENDING</option>
                                    <option value="COMPLETED" ${(record.status || '').toUpperCase() === 'COMPLETED' || (record.status || '').toUpperCase() === 'ACTIVE' ? 'selected' : ''}>● COMPLETED</option>
                                </select>
                            </div>
                        </td>
                        <td>
                            <div class="lifecycle-progress">
                                <span class="lifecycle-track">
                                    <span class="lifecycle-fill" style="width:${Math.max(8, record.progress || 0)}%;"></span>
                                </span>
                                <span>${record.progress || 0}%</span>
                            </div>
                        </td>
                        <td style="text-align:right;">
                            <button type="button" class="rec-action-btn" onclick="openJoinerTrackModal('${esc(record.employee_id || '')}', ${idx})">Track</button>
                        </td>
                    </tr>
                `;
            }).join('');
        }

    } else if (state.activeTab === 'exit') {
        head.innerHTML = `
            <tr>
                <th style="width:36px;">#</th>
                <th style="width:80px;">Exit ID</th>
                <th>Employee Name</th>
                <th>Department</th>
                <th>Last Working Day</th>
                <th>Clearance Status</th>
                <th>Exit Progress</th>
                <th style="text-align:right;">Actions</th>
            </tr>
        `;

        const filtered = exitList.filter(e => {
            const matchesSearch = !searchTerm ||
                (e.full_name && e.full_name.toLowerCase().includes(searchTerm)) ||
                (e.department && e.department.toLowerCase().includes(searchTerm));
            const matchesDept = !deptTerm || (e.department && e.department.toLowerCase() === deptTerm);
            const matchesStatus = !statusTerm || (e.status && e.status.toUpperCase() === statusTerm);
            return matchesSearch && matchesDept && matchesStatus;
        });

        filteredCount = filtered.length;

        if (filtered.length === 0) {
            rowsHtml = '<tr><td colspan="8" class="employee-state">No active exit clearances.</td></tr>';
        } else {
            rowsHtml = filtered.map((record, idx) => {
                const exitId = record.exit_id || `EXT-${String(idx + 101).padStart(3, '0')}`;
                const color = buildAvatarColor(record.full_name);
                const initial = (record.full_name ? record.full_name.charAt(0).toUpperCase() : '?');
                return `
                    <tr>
                        <td>${idx + 1}</td>
                        <td><span class="employee-code">${esc(exitId)}</span></td>
                        <td>
                            <div class="employee-name">
                                <span class="candidate-avatar" style="background:${color};">${esc(initial)}</span>
                                <div>
                                    <strong>${esc(record.full_name)}</strong>
                                    <small>${esc(record.email || 'employee@company.com')}</small>
                                </div>
                            </div>
                        </td>
                        <td>
                            <strong>${esc(record.department)}</strong>
                            <small style="display:block;color:#756b85;">${esc(record.clearance_stage || 'In Clearance')}</small>
                        </td>
                        <td>${esc(record.last_working_day || record.milestone_date || 'Pending')}</td>
                        <td>
                            <div class="rec-status-wrap">
                                <select class="rec-status-select ${statusBadgeClass(record.status)}" data-status="${esc(record.status || 'NOTICE')}" onchange="changeExitStatus(${record.id || (idx + 1)}, this.value, this, '${esc(record.exit_id || '')}')">
                                    <option value="NOTICE" ${(record.status || '').toUpperCase() === 'NOTICE' || (record.status || '').toUpperCase() === 'PENDING' ? 'selected' : ''}>● NOTICE</option>
                                    <option value="IN_PROGRESS" ${(record.status || '').toUpperCase() === 'IN_PROGRESS' ? 'selected' : ''}>● IN PROGRESS</option>
                                    <option value="COMPLETED" ${(record.status || '').toUpperCase() === 'COMPLETED' || (record.status || '').toUpperCase() === 'TERMINATED' ? 'selected' : ''}>● COMPLETED</option>
                                </select>
                            </div>
                        </td>
                        <td>
                            <div class="lifecycle-progress">
                                <span class="lifecycle-track">
                                    <span class="lifecycle-fill" style="width:${Math.max(8, record.progress || 0)}%;background:linear-gradient(90deg,#701a75,#db2777);"></span>
                                </span>
                                <span>${record.progress || 0}%</span>
                            </div>
                        </td>
                        <td style="text-align:right;">
                            <button type="button" class="rec-action-btn" onclick="openExitClearanceModal('${esc(record.exit_id || '')}', ${idx})">Clearance</button>
                        </td>
                    </tr>
                `;
            }).join('');
        }
    }

    body.innerHTML = rowsHtml;

    if (countBadge) countBadge.textContent = `${filteredCount} records`;
    if (pageInfo) pageInfo.textContent = `Showing 1 to ${filteredCount} of ${filteredCount} entries`;
    if (pagination) {
        pagination.innerHTML = `
            <button type="button" style="padding:4px 8px;border-radius:6px;border:1px solid #ebd7e7;background:#fff;color:#756b85;cursor:pointer;">&lt;</button>
            <span style="padding:4px 10px;border-radius:6px;background:#42134f;color:#fff;font-weight:700;font-size:12px;">1</span>
            <button type="button" style="padding:4px 8px;border-radius:6px;border:1px solid #ebd7e7;background:#fff;color:#756b85;cursor:pointer;">&gt;</button>
        `;
    }
}

/* ── CSV Export Functionality ── */

function exportCurrentTabCSV() {
    const tab = state.activeTab;
    let filename = `hr_${tab}_export_${new Date().toISOString().slice(0, 10)}.csv`;
    let rows = [];

    if (tab === 'jobs') {
        rows.push(['Job ID', 'Title', 'Department', 'Location', 'Applicants', 'Status', 'Posted Date', 'Manager']);
        (state.recruitmentData?.job_openings_data?.jobs || []).forEach((j, i) => {
            rows.push([`JOB-${i+101}`, j.title, j.department, j.location, j.applicants_count, j.status, j.posted_at, j.hiring_manager]);
        });
    } else if (tab === 'candidates') {
        rows.push(['Candidate ID', 'Name', 'Email', 'Role', 'Department', 'Stage', 'Status', 'Applied Date']);
        (state.recruitmentData?.recent_candidates || []).forEach((c, i) => {
            rows.push([`CAN-${i+101}`, c.full_name, c.email || '', c.job_title, c.department || '', c.stage, 'Active', c.applied_at]);
        });
    } else if (tab === 'joiners') {
        rows.push(['Emp ID', 'Name', 'Email', 'Department', 'Role', 'Joining Date', 'Progress', 'Status']);
        (state.onboardingData?.new_joiners || []).forEach((j, i) => {
            rows.push([j.employee_id || `EMP-${i+10}`, j.full_name, j.email || '', j.department, j.role || '', j.joining_date, `${j.progress}%`, j.status]);
        });
    } else {
        rows.push(['Exit ID', 'Name', 'Department', 'Resign Date', 'Last Working Day', 'Clearance Stage', 'Progress']);
        (state.onboardingData?.exit_processes || []).forEach((e, i) => {
            rows.push([e.exit_id || `EXT-${i+101}`, e.full_name, e.department, e.resignation_date, e.last_working_day, e.clearance_stage, `${e.progress}%`]);
        });
    }

    const csvContent = 'data:text/csv;charset=utf-8,' + rows.map(r => r.map(f => `"${String(f ?? '').replace(/"/g, '""')}"`).join(',')).join('\n');
    const encodedUri = encodeURI(csvContent);
    const link = document.createElement('a');
    link.setAttribute('href', encodedUri);
    link.setAttribute('download', filename);
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}

/* ── DOM Event Listeners ── */

document.addEventListener('DOMContentLoaded', () => {
    loadAllData();

    // Tab buttons inside .employee-tabs
    const tabButtons = document.querySelectorAll('.employee-tabs > button[data-tab]');
    tabButtons.forEach(btn => {
        btn.addEventListener('click', () => {
            tabButtons.forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            state.activeTab = btn.dataset.tab;
            renderMasterTable();
        });
    });

    // Search and Filters
    const searchInput = document.getElementById('tableSearch');
    if (searchInput) {
        searchInput.addEventListener('input', () => {
            clearTimeout(state.searchTimer);
            state.searchTimer = setTimeout(() => renderMasterTable(), 200);
        });
    }

    const deptSelect = document.getElementById('tableDepartment');
    if (deptSelect) {
        deptSelect.addEventListener('change', () => renderMasterTable());
    }

    const statusSelect = document.getElementById('tableStatus');
    if (statusSelect) {
        statusSelect.addEventListener('change', () => renderMasterTable());
    }

    // Export button
    const exportBtn = document.getElementById('exportRecBtn');
    if (exportBtn) {
        exportBtn.addEventListener('click', exportCurrentTabCSV);
    }

    // Schedule interview trigger button
    const schedBtn = document.getElementById('scheduleInterviewBtn');
    if (schedBtn) {
        schedBtn.addEventListener('click', () => {
            alert('Opening Interview Scheduler:\nSelect candidate from pipeline to schedule Technical or HR round.');
        });
    }

    // Modal Handling for Create Job Opening
    const modal = document.getElementById('jobModal');
    const createBtn = document.getElementById('createJobBtn');
    const closeBtn = document.getElementById('closeJobModal');
    const cancelBtn = document.getElementById('cancelJobModal');
    const jobForm = document.getElementById('createJobForm');

    function openModal() {
        if (modal) {
            modal.style.display = 'flex';
            modal.setAttribute('aria-hidden', 'false');
        }
    }

    function closeModal() {
        if (modal) {
            modal.style.display = 'none';
            modal.setAttribute('aria-hidden', 'true');
        }
    }

    if (createBtn) createBtn.addEventListener('click', openModal);
    if (closeBtn) closeBtn.addEventListener('click', closeModal);
    if (cancelBtn) cancelBtn.addEventListener('click', closeModal);

    if (jobForm) {
        jobForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const title = document.getElementById('jobTitleInput')?.value || '';
            const dept = document.getElementById('jobDeptInput')?.value || '';
            const location = document.getElementById('jobLocationInput')?.value || '';
            const type = document.getElementById('jobTypeInput')?.value || 'Full-time';
            const vacancies = parseInt(document.getElementById('jobVacanciesInput')?.value || '1', 10);
            const manager = document.getElementById('jobManagerInput')?.value || 'HR Manager';

            try {
                const res = await fetch(`${API_BASE}/api/hr/recruitment/jobs/create`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        Authorization: `Bearer ${token()}`
                    },
                    body: JSON.stringify({
                        title,
                        department: dept,
                        location,
                        employment_type: type,
                        vacancies,
                        hiring_manager: manager
                    })
                });
                const json = await res.json();
                const newId = json.data?.id || (Date.now() % 10000);

                if (!state.recruitmentData.job_openings_data) {
                    state.recruitmentData.job_openings_data = { jobs: [] };
                }

                state.recruitmentData.job_openings_data.jobs.unshift({
                    id: newId,
                    title,
                    department: dept,
                    location,
                    employment_type: type,
                    vacancies,
                    applicants_count: 0,
                    status: 'OPEN',
                    posted_at: new Date().toISOString().slice(0, 10),
                    hiring_manager: manager
                });

                // Increment KPI
                const openJobsEl = document.getElementById('openJobs');
                if (openJobsEl) {
                    const count = parseInt(openJobsEl.textContent || '0', 10) || 0;
                    openJobsEl.textContent = count + 1;
                }

                closeModal();
                jobForm.reset();
                state.activeTab = 'jobs';
                document.querySelectorAll('.employee-tabs > button[data-tab]').forEach(b => {
                    b.classList.toggle('active', b.dataset.tab === 'jobs');
                });
                populateDepartmentFilter();
                renderMasterTable();
                showRecToast(`Job opening "${title}" published successfully!`, 'success');
            } catch (err) {
                console.error('Create job error:', err);
                showRecToast('Failed to post job: ' + err.message, 'error');
            }
        });
    }

    // Modal Handling for Job Details (Action -> View)
    const closeJobDetailsBtn = document.getElementById('closeJobDetailsModal');
    const cancelJobDetailsBtn = document.getElementById('cancelJobDetailsModal');
    const toggleStatusBtn = document.getElementById('quickToggleJobStatusBtn');
    const editJobForm = document.getElementById('editJobForm');

    if (closeJobDetailsBtn) closeJobDetailsBtn.addEventListener('click', closeJobDetailsModal);
    if (cancelJobDetailsBtn) cancelJobDetailsBtn.addEventListener('click', closeJobDetailsModal);
    if (toggleStatusBtn) toggleStatusBtn.addEventListener('click', toggleJobStatusInModal);
    if (editJobForm) editJobForm.addEventListener('submit', saveJobDetails);

    // Modal Handling for Candidate Review
    const closeCandBtn = document.getElementById('closeCandModal');
    const cancelCandBtn = document.getElementById('cancelCandModal');
    const candForm = document.getElementById('candidateReviewForm');

    if (closeCandBtn) closeCandBtn.addEventListener('click', closeCandidateReviewModal);
    if (cancelCandBtn) cancelCandBtn.addEventListener('click', closeCandidateReviewModal);
    if (candForm) candForm.addEventListener('submit', saveCandidateReview);

    // Modal Handling for Joiner Tracking
    const closeJoinerBtn = document.getElementById('closeJoinerModal');
    const cancelJoinerBtn = document.getElementById('cancelJoinerModal');
    const toggleJoinerBtn = document.getElementById('toggleJoinerStatusBtn');
    const joinerTrackForm = document.getElementById('joinerTrackForm');
    const joinerProgressSlider = document.getElementById('joinerProgressSlider');
    const joinerProgressVal = document.getElementById('joinerProgressVal');
    const joinerCheckboxes = document.querySelectorAll('.joiner-chk');

    if (closeJoinerBtn) closeJoinerBtn.addEventListener('click', closeJoinerTrackModal);
    if (cancelJoinerBtn) cancelJoinerBtn.addEventListener('click', closeJoinerTrackModal);
    if (toggleJoinerBtn) toggleJoinerBtn.addEventListener('click', toggleJoinerStatusInModal);
    if (joinerTrackForm) joinerTrackForm.addEventListener('submit', saveJoinerTrack);

    if (joinerProgressSlider) {
        joinerProgressSlider.addEventListener('input', (e) => {
            const val = parseInt(e.target.value, 10);
            if (joinerProgressVal) joinerProgressVal.textContent = `${val}% Completed`;
            if (joinerCheckboxes.length > 0) {
                joinerCheckboxes.forEach((chk, i) => {
                    chk.checked = val >= ((i + 1) * 25);
                });
            }
            if (val === 100) {
                const sel = document.getElementById('joinerStatusInput');
                if (sel) sel.value = 'COMPLETED';
            }
        });
    }

    joinerCheckboxes.forEach(chk => {
        chk.addEventListener('change', () => {
            const checkedCount = document.querySelectorAll('.joiner-chk:checked').length;
            const pct = checkedCount * 25;
            if (joinerProgressSlider) joinerProgressSlider.value = pct;
            if (joinerProgressVal) joinerProgressVal.textContent = `${pct}% Completed`;
            if (pct === 100) {
                const sel = document.getElementById('joinerStatusInput');
                if (sel) sel.value = 'COMPLETED';
            }
        });
    });

    // Modal Handling for Exit Clearance
    const closeExitBtn = document.getElementById('closeExitModal');
    const cancelExitBtn = document.getElementById('cancelExitModal');
    const toggleExitBtn = document.getElementById('toggleExitStatusBtn');
    const exitClearanceForm = document.getElementById('exitClearanceForm');
    const exitProgressSlider = document.getElementById('exitProgressSlider');
    const exitProgressVal = document.getElementById('exitProgressVal');
    const exitCheckboxes = document.querySelectorAll('.exit-chk');

    if (closeExitBtn) closeExitBtn.addEventListener('click', closeExitClearanceModal);
    if (cancelExitBtn) cancelExitBtn.addEventListener('click', closeExitClearanceModal);
    if (toggleExitBtn) toggleExitBtn.addEventListener('click', toggleExitStatusInModal);
    if (exitClearanceForm) exitClearanceForm.addEventListener('submit', saveExitClearance);

    if (exitProgressSlider) {
        exitProgressSlider.addEventListener('input', (e) => {
            const val = parseInt(e.target.value, 10);
            if (exitProgressVal) exitProgressVal.textContent = `${val}% Completed`;
            if (exitCheckboxes.length > 0) {
                exitCheckboxes.forEach((chk, i) => {
                    chk.checked = val >= ((i + 1) * 25);
                });
            }
            if (val === 100) {
                const sel = document.getElementById('exitStatusInput');
                if (sel) sel.value = 'COMPLETED';
            }
        });
    }

    exitCheckboxes.forEach(chk => {
        chk.addEventListener('change', () => {
            const checkedCount = document.querySelectorAll('.exit-chk:checked').length;
            const pct = checkedCount * 25;
            if (exitProgressSlider) exitProgressSlider.value = pct;
            if (exitProgressVal) exitProgressVal.textContent = `${pct}% Completed`;
            if (pct === 100) {
                const sel = document.getElementById('exitStatusInput');
                if (sel) sel.value = 'COMPLETED';
            }
        });
    });
});

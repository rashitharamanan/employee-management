'use strict';
const OE_API = 'http://localhost/employee-qr-system/backend/public';
const token = () => localStorage.getItem('auth_token') || localStorage.getItem('token') || '';
const esc = value => String(value ?? '').replace(/[&<>'"]/g, char => ({ '&':'&amp;', '<':'&lt;', '>':'&gt;', "'":'&#39;', '"':'&quot;' }[char]));

async function loadDashboard() {
    const response = await fetch(`${OE_API}/api/hr/onboarding-exit`, { headers: { Authorization: `Bearer ${token()}` } });
    if (response.status === 401) { localStorage.clear(); location.href = 'login.php'; return null; }
    const result = await response.json();
    if (!response.ok || !result.success) throw new Error(result.message || 'Could not load onboarding data.');
    return result.data;
}

const setText = (id, value) => { document.getElementById(id).textContent = value ?? '—'; };
const statusClass = value => String(value || 'PENDING').toLowerCase();

function renderPipeline(id, stages) { document.getElementById(id).innerHTML = stages.map(stage => `<div class="pipe ${stage.count ? 'active' : ''}"><i>${stage.step}</i><span>${esc(stage.stage)}</span></div>`).join(''); }
function renderRecords(id, records, label) { document.getElementById(id).innerHTML = records.map((record, index) => `<tr><td>${index + 1}</td><td>${esc(record.full_name)}</td><td>${esc(record.department)}</td><td>${esc(record.milestone_date)}</td><td><i class="oe-status ${statusClass(record.status)}">${esc(record.status.replace('_', ' '))}</i></td><td><span class="progress"><i style="--p:${record.progress}%"></i>${record.progress}%</span></td></tr>`).join('') || `<tr><td colspan="6">No ${label} records in the database.</td></tr>`; }
function renderTasks(id, tasks) { document.getElementById(id).innerHTML = tasks.map(task => `<div class="task"><span><b>${esc(task.task_name)}</b><small>${esc(task.assigned_to || 'Unassigned')} · ${esc(task.due_date || 'No due date')}</small></span><i class="oe-status ${statusClass(task.status)}">${esc(task.status.replace('_', ' '))}</i></div>`).join('') || 'No tasks available.'; }

function renderDashboard(data) {
    const summary = data.summary;
    setText('onboardingActive', summary.onboarding_in_progress); setText('onboardingDone', summary.onboarding_completed_month); setText('pendingTasks', summary.pending_tasks); setText('exitActive', summary.exit_in_progress); setText('exitDone', summary.exit_completed_month); setText('clearancePending', summary.final_clearance_pending);
    renderPipeline('onboardingPipeline', data.onboarding_pipeline); renderPipeline('exitPipeline', data.exit_pipeline); renderRecords('newJoiners', data.new_joiners, 'onboarding'); renderRecords('exitProcesses', data.exit_processes, 'exit'); renderTasks('onboardingTasks', data.onboarding_tasks); renderTasks('exitTasks', data.exit_tasks);
    const onboarded = summary.onboarding_in_progress + summary.onboarding_completed_month, exited = summary.exit_in_progress + summary.exit_completed_month, total = onboarded + exited || 1;
    document.getElementById('oeDonut').style.setProperty('--on', `${onboarded / total * 100}%`); document.getElementById('oeDonut').style.setProperty('--in', `${(onboarded + summary.onboarding_in_progress) / total * 100}%`); document.querySelector('#oeDonut b').textContent = total;
    document.getElementById('oeLegend').innerHTML = `<div><span>Onboarded</span><b>${onboarded}</b></div><div><span>In Onboarding</span><b>${summary.onboarding_in_progress}</b></div><div><span>Exited</span><b>${exited}</b></div>`;
    document.getElementById('milestones').innerHTML = data.upcoming_milestones.map(milestone => `<div class="milestone"><time>${esc(milestone.milestone_date)}</time><span><b>${esc(milestone.full_name)}</b><small>${esc(milestone.current_stage || milestone.workflow_type)}</small></span></div>`).join('') || 'No upcoming milestones.';
    document.getElementById('documents').innerHTML = data.documents.map(document => `<div class="document"><span>▤ ${esc(document.name)}</span>${document.file_path ? `<a href="${esc(document.file_path)}" download>⇩</a>` : ''}</div>`).join('') || 'No active templates.';
}

document.addEventListener('DOMContentLoaded', async () => { if (!token()) { location.href = 'login.php'; return; } try { const data = await loadDashboard(); if (data) renderDashboard(data); } catch (error) { document.getElementById('newJoiners').innerHTML = `<tr><td colspan="6">${esc(error.message)}</td></tr>`; } });

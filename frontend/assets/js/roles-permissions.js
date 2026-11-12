'use strict';

const API_BASE = 'http://localhost/employee-qr-system/backend/public';
const state = { roles: [], permissions: [], editingRoleId: null, selectedRoleId: null };
const token = () => localStorage.getItem('auth_token') || localStorage.getItem('token') || '';

async function api(path, options = {}) {
    const response = await fetch(`${API_BASE}${path}`, { ...options, headers: { 'Content-Type': 'application/json', Authorization: `Bearer ${token()}`, ...(options.headers || {}) } });
    if (response.status === 401) { localStorage.removeItem('auth_token'); localStorage.removeItem('token'); window.location.href = 'login.php'; return null; }
    const result = await response.json();
    if (!response.ok || result.success === false) throw new Error(result.message || 'Request failed.');
    return result.data || {};
}

const esc = value => String(value ?? '').replace(/[&<>'"]/g, char => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', "'": '&#39;', '"': '&quot;' }[char]));
const moduleName = key => { const part = String(key).split('.')[0] || 'other'; return part.charAt(0).toUpperCase() + part.slice(1); };
const showToast = message => { const toast = document.getElementById('toast'); toast.textContent = message; toast.classList.add('show'); setTimeout(() => toast.classList.remove('show'), 2600); };
const showError = (id, message) => { const error = document.getElementById(id); error.textContent = message; error.classList.toggle('show', Boolean(message)); };

function updateStats() {
    const active = state.roles.filter(role => Number(role.users_count) > 0).length;
    document.getElementById('totalRoles').textContent = state.roles.length;
    document.getElementById('activeRoles').textContent = active;
    document.getElementById('inactiveRoles').textContent = state.roles.length - active;
    document.getElementById('totalPermissions').textContent = state.permissions.length;
}

function renderRoles() {
    const query = document.getElementById('roleSearch').value.toLowerCase().trim();
    const status = document.getElementById('roleStatus').value;
    const rows = state.roles.filter(role => {
        const active = Number(role.users_count) > 0;
        return (!query || `${role.name} ${role.description}`.toLowerCase().includes(query)) && (!status || status === (active ? 'active' : 'inactive'));
    });
    document.getElementById('rolesBody').innerHTML = rows.length ? rows.map((role, index) => {
        const active = Number(role.users_count) > 0;
        return `<tr><td>${index + 1}</td><td><span class="role-label">${esc(role.name)}</span></td><td>${esc(role.description || '—')}</td><td>${role.users_count || 0}</td><td>${role.permissions_count || 0}</td><td><span class="status ${active ? '' : 'inactive'}">${active ? 'Active' : 'Inactive'}</span></td><td>${role.created_at ? new Date(role.created_at).toLocaleDateString('en-GB') : '—'}</td><td><div class="row-actions"><button class="icon-button" title="Assign permissions" data-action="permissions" data-id="${role.id}">⚿</button><button class="icon-button" title="Edit role" data-action="edit" data-id="${role.id}">✎</button><button class="icon-button danger" title="Delete role" data-action="delete" data-id="${role.id}">⌫</button></div></td></tr>`;
    }).join('') : '<tr><td colspan="8" class="empty-cell">No roles found.</td></tr>';
    document.getElementById('rolesCount').textContent = `${rows.length} of ${state.roles.length} roles`;
    document.getElementById('rolesFooter').textContent = `Showing ${rows.length} role${rows.length === 1 ? '' : 's'}`;
}

function renderPermissions() {
    const query = document.getElementById('permissionSearch').value.toLowerCase().trim();
    const module = document.getElementById('moduleFilter').value;
    const rows = state.permissions.filter(permission => (!query || `${permission.name} ${permission.description}`.toLowerCase().includes(query)) && (!module || moduleName(permission.name) === module));
    document.getElementById('permissionsBody').innerHTML = rows.length ? rows.map((permission, index) => `<tr><td>${index + 1}</td><td><span class="permission-key">${esc(permission.name)}</span></td><td>${esc(moduleName(permission.name))}</td><td>${esc(permission.description || '—')}</td><td>${permission.assigned_roles || 0}</td><td><span class="status">Active</span></td><td><button class="icon-button" title="View assignment count">⌁</button></td></tr>`).join('') : '<tr><td colspan="7" class="empty-cell">No permissions found.</td></tr>';
    document.getElementById('permissionsCount').textContent = `${rows.length} of ${state.permissions.length} permissions`;
    document.getElementById('permissionsFooter').textContent = `Showing ${rows.length} permission${rows.length === 1 ? '' : 's'}`;
}

async function loadData() {
    try {
        const [roles, permissions] = await Promise.all([api('/api/roles'), api('/api/permissions')]);
        state.roles = roles.roles || []; state.permissions = permissions.permissions || [];
        const modules = [...new Set(state.permissions.map(permission => moduleName(permission.name)))].sort();
        document.getElementById('moduleFilter').innerHTML = '<option value="">All Modules</option>' + modules.map(name => `<option value="${esc(name)}">${esc(name)}</option>`).join('');
        updateStats(); renderRoles(); renderPermissions();
    } catch (error) { document.getElementById('rolesBody').innerHTML = `<tr><td colspan="8" class="empty-cell">${esc(error.message)}</td></tr>`; }
}

function openRoleModal(role = null) { state.editingRoleId = role?.id || null; document.getElementById('roleModalTitle').textContent = role ? 'Edit Role' : 'Add Role'; document.getElementById('roleName').value = role?.name || ''; document.getElementById('roleDescription').value = role?.description || ''; showError('roleError', ''); document.getElementById('roleModal').classList.add('open'); }
function closeModal(id) { document.getElementById(id).classList.remove('open'); }

async function openPermissionModal(role) {
    state.selectedRoleId = role.id; document.getElementById('permissionRoleName').textContent = `Choose permissions for ${role.name}.`; document.getElementById('permissionChecklist').innerHTML = '<span class="muted">Loading permissions...</span>'; document.getElementById('permissionModal').classList.add('open');
    try { const data = await api(`/api/role-permissions?role_id=${role.id}`); const selected = new Set((data.permissions || []).map(permission => Number(permission.id))); document.getElementById('permissionChecklist').innerHTML = state.permissions.map(permission => `<label class="permission-option"><input type="checkbox" value="${permission.id}" ${selected.has(Number(permission.id)) ? 'checked' : ''}><span><strong>${esc(permission.name)}</strong><small>${esc(permission.description || moduleName(permission.name))}</small></span></label>`).join(''); } catch (error) { showError('permissionError', error.message); }
}

document.querySelectorAll('.tab').forEach(tab => tab.addEventListener('click', () => { document.querySelectorAll('.tab').forEach(item => { item.classList.toggle('active', item === tab); item.setAttribute('aria-selected', item === tab); }); document.getElementById('rolesPanel').classList.toggle('hidden', tab.dataset.tab !== 'roles'); document.getElementById('permissionsPanel').classList.toggle('hidden', tab.dataset.tab !== 'permissions'); }));
document.getElementById('addRoleButton').addEventListener('click', () => openRoleModal());
document.getElementById('addPermissionButton').addEventListener('click', () => showToast('Permissions are managed by the system catalog.'));
document.getElementById('roleSearch').addEventListener('input', renderRoles); document.getElementById('roleStatus').addEventListener('change', renderRoles); document.getElementById('permissionSearch').addEventListener('input', renderPermissions); document.getElementById('moduleFilter').addEventListener('change', renderPermissions);
document.querySelectorAll('[data-close]').forEach(button => button.addEventListener('click', () => closeModal(button.dataset.close)));
document.getElementById('rolesBody').addEventListener('click', async event => { const button = event.target.closest('[data-action]'); if (!button) return; const role = state.roles.find(item => Number(item.id) === Number(button.dataset.id)); if (!role) return; if (button.dataset.action === 'edit') openRoleModal(role); if (button.dataset.action === 'permissions') openPermissionModal(role); if (button.dataset.action === 'delete' && confirm(`Delete role ${role.name}?`)) { try { await api(`/api/roles/delete?id=${role.id}`, { method: 'DELETE' }); showToast('Role deleted successfully.'); loadData(); } catch (error) { showToast(error.message); } } });
document.getElementById('roleForm').addEventListener('submit', async event => { event.preventDefault(); const payload = { name: document.getElementById('roleName').value.trim(), description: document.getElementById('roleDescription').value.trim() }; try { await api(state.editingRoleId ? `/api/roles/update?id=${state.editingRoleId}` : '/api/roles', { method: state.editingRoleId ? 'PUT' : 'POST', body: JSON.stringify(payload) }); closeModal('roleModal'); showToast('Role saved successfully.'); loadData(); } catch (error) { showError('roleError', error.message); } });
document.getElementById('permissionForm').addEventListener('submit', async event => { event.preventDefault(); const permissionIds = [...document.querySelectorAll('#permissionChecklist input:checked')].map(input => Number(input.value)); try { await api(`/api/role-permissions?role_id=${state.selectedRoleId}`, { method: 'POST', body: JSON.stringify({ permission_ids: permissionIds }) }); closeModal('permissionModal'); showToast('Permissions updated successfully.'); loadData(); } catch (error) { showError('permissionError', error.message); } });

document.addEventListener('DOMContentLoaded', () => { if (!token()) { window.location.href = 'login.php'; return; } loadData(); });

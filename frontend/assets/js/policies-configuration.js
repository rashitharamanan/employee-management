'use strict';

/**
 * Enterprise Policies & Configuration Management
 * Admin Portal - LUNA Theme
 */

const API_BASE = (function () {
    const origin = window.location.origin;
    if (origin && origin !== 'null') {
        return origin + '/employee-qr-system/backend/public';
    }
    return 'http://localhost/employee-qr-system/backend/public';
})();

const state = {
    activeTab: 'policies', // 'policies' | 'configuration'
    policies: [],
    configurations: [],
    policyId: null,
    configurationId: null
};

// Utilities
const getToken = () => localStorage.getItem('auth_token') || localStorage.getItem('token') || '';

const esc = (v) =>
    String(v ?? '').replace(/[&<>'"]/g, (c) => ({
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        "'": '&#39;',
        '"': '&quot;'
    }[c]));

const formatDate = (v) => {
    if (!v) return '—';
    try {
        const d = new Date(v);
        return isNaN(d.getTime()) ? String(v) : d.toLocaleDateString('en-GB', {
            day: '2-digit',
            month: 'short',
            year: 'numeric'
        });
    } catch {
        return String(v);
    }
};

// API Fetch Helper
async function api(path, options = {}) {
    const token = getToken();
    const res = await fetch(`${API_BASE}${path}`, {
        ...options,
        headers: {
            'Content-Type': 'application/json',
            ...(token ? { Authorization: `Bearer ${token}` } : {}),
            ...(options.headers || {})
        }
    });

    if (res.status === 401) {
        localStorage.removeItem('auth_token');
        localStorage.removeItem('token');
        location.href = 'login.php';
        return null;
    }

    const result = await res.json();
    if (!res.ok || result.success === false) {
        throw new Error(result.message || 'Request failed.');
    }
    return result.data || {};
}

// Toast notification
function toast(message) {
    const el = document.getElementById('toast');
    if (!el) return;
    el.textContent = message;
    el.classList.add('show');
    setTimeout(() => el.classList.remove('show'), 3000);
}

// Form error handler
function setFormError(id, message) {
    const el = document.getElementById(id);
    if (!el) return;
    el.textContent = message || '';
    el.classList.toggle('show', !!message);
}

// Icon templates
const ICONS = {
    shield: `<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>`,
    sliders: `<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.7 1.7 0 0 0 .34 1.88l.06.06-1.4 1.4-.06-.06A1.7 1.7 0 0 0 16.46 18a1.7 1.7 0 0 0-1 .98V19a1.7 1.7 0 0 0-1.7 1.7v.08h-2v-.08A1.7 1.7 0 0 0 10.06 19h-.08a1.7 1.7 0 0 0-1-.98 1.7 1.7 0 0 0-1.88.34l-.06.06-1.4-1.4.06-.06A1.7 1.7 0 0 0 6 15.04a1.7 1.7 0 0 0-.98-1H5A1.7 1.7 0 0 0 3.3 12.3H3.2v-2h.1A1.7 1.7 0 0 0 5 9.06v-.08a1.7 1.7 0 0 0-.34-1.88L4.6 7.04 6 5.64l.06.06A1.7 1.7 0 0 0 7.94 6a1.7 1.7 0 0 0 1-.98V5A1.7 1.7 0 0 0 10.64 3.3h2A1.7 1.7 0 0 0 14.34 5v.02a1.7 1.7 0 0 0 1 .98 1.7 1.7 0 0 0 1.88-.34l.06-.06 1.4 1.4-.06.06A1.7 1.7 0 0 0 18.28 9v.08a1.7 1.7 0 0 0 .98 1h.1v2h-.1a1.7 1.7 0 0 0-1.7 1.7v.08a1.7 1.7 0 0 0 1.84 1.14z"/></svg>`,
    edit: `<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"/></svg>`,
    trash: `<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/><line x1="10" y1="11" x2="10" y2="17"/><line x1="14" y1="11" x2="14" y2="17"/></svg>`
};

// Render Policies Table
function renderPolicies() {
    const searchEl = document.getElementById('unifiedSearch');
    const q = (searchEl ? searchEl.value : '').trim().toLowerCase();
    const statusFilter = document.getElementById('unifiedStatusFilter')?.value || '';
    const categoryFilter = document.getElementById('unifiedCategoryFilter')?.value || '';

    const filtered = state.policies.filter(p => {
        const matchesQuery = !q || `${p.name || ''} ${p.category || ''} ${p.description || ''} ${p.policy_value || ''}`.toLowerCase().includes(q);
        const matchesStatus = !statusFilter || p.status === statusFilter;
        const matchesCategory = !categoryFilter || p.category === categoryFilter;
        return matchesQuery && matchesStatus && matchesCategory;
    });

    const tbody = document.getElementById('policiesTableBody');
    if (!tbody) return;

    if (!filtered.length) {
        tbody.innerHTML = `
            <tr>
                <td colspan="8" class="pc-empty-cell">
                    <p style="margin: 0 0 4px; font-weight: 700; color: var(--pc-text-heading);">No policies found</p>
                    <span style="font-size: 12px; color: var(--pc-text-muted);">Try adjusting your search terms or filters</span>
                </td>
            </tr>`;
    } else {
        tbody.innerHTML = filtered.map((p, i) => {
            const isActive = p.status === 'ACTIVE';
            return `
                <tr>
                    <td class="pc-row-index">${i + 1}</td>
                    <td>
                        <div class="pc-item-name">
                            <span>${esc(p.name)}</span>
                        </div>
                    </td>
                    <td>
                        <span class="pc-category-tag">${esc(p.category || 'General')}</span>
                    </td>
                    <td>
                        <div class="pc-desc-cell" title="${esc(p.description || '')}">${esc(p.description || '—')}</div>
                    </td>
                    <td>
                        ${p.policy_value ? `<span class="pc-val-cell" title="${esc(p.policy_value)}">${esc(p.policy_value)}</span>` : '<span class="text-muted">—</span>'}
                    </td>
                    <td>
                        <span class="pc-status-pill ${isActive ? 'active' : 'inactive'}">
                            <span class="pc-status-dot"></span>
                            ${isActive ? 'Active' : 'Inactive'}
                        </span>
                    </td>
                    <td class="pc-date-cell">${formatDate(p.updated_at || p.created_at)}</td>
                    <td>
                        <div class="pc-actions-cell">
                            <button type="button" class="pc-action-btn" title="Edit Policy" data-action="edit-policy" data-id="${p.id}">
                                ${ICONS.edit}
                            </button>
                            <button type="button" class="pc-action-btn danger" title="Delete Policy" data-action="delete-policy" data-id="${p.id}">
                                ${ICONS.trash}
                            </button>
                        </div>
                    </td>
                </tr>`;
        }).join('');
    }

    // Update count badge & footer
    const countBadge = document.getElementById('activeCountBadge');
    if (countBadge && state.activeTab === 'policies') {
        countBadge.textContent = `${filtered.length} of ${state.policies.length} Policies`;
    }
    const footerCount = document.getElementById('policiesFooterCount');
    if (footerCount) {
        footerCount.textContent = `Showing ${filtered.length} of ${state.policies.length} total recorded policies`;
    }
}

// Render Configurations Table
function renderConfigurations() {
    const searchEl = document.getElementById('unifiedSearch');
    const q = (searchEl ? searchEl.value : '').trim().toLowerCase();
    const statusFilter = document.getElementById('unifiedStatusFilter')?.value || '';
    const categoryFilter = document.getElementById('unifiedCategoryFilter')?.value || '';

    const filtered = state.configurations.filter(c => {
        const matchesQuery = !q || `${c.config_key || ''} ${c.category || ''} ${c.description || ''} ${c.config_value || ''}`.toLowerCase().includes(q);
        const matchesStatus = !statusFilter || c.status === statusFilter;
        const matchesCategory = !categoryFilter || c.category === categoryFilter;
        return matchesQuery && matchesStatus && matchesCategory;
    });

    const tbody = document.getElementById('configurationsTableBody');
    if (!tbody) return;

    if (!filtered.length) {
        tbody.innerHTML = `
            <tr>
                <td colspan="7" class="pc-empty-cell">
                    <p style="margin: 0 0 4px; font-weight: 700; color: var(--pc-text-heading);">No configurations found</p>
                    <span style="font-size: 12px; color: var(--pc-text-muted);">Try adjusting your search terms or filters</span>
                </td>
            </tr>`;
    } else {
        tbody.innerHTML = filtered.map((c, i) => {
            const isActive = c.status === 'ACTIVE';
            return `
                <tr>
                    <td class="pc-row-index">${i + 1}</td>
                    <td>
                        <span class="pc-code-badge">${esc(c.config_key)}</span>
                    </td>
                    <td>
                        <span class="pc-category-tag">${esc(c.category || 'General')}</span>
                    </td>
                    <td>
                        <span class="pc-val-cell" title="${esc(c.config_value || '')}">${esc(c.config_value || '—')}</span>
                    </td>
                    <td>
                        <div class="pc-desc-cell" title="${esc(c.description || '')}">${esc(c.description || '—')}</div>
                    </td>
                    <td>
                        <span class="pc-status-pill ${isActive ? 'active' : 'inactive'}">
                            <span class="pc-status-dot"></span>
                            ${isActive ? 'Active' : 'Inactive'}
                        </span>
                    </td>
                    <td>
                        <div class="pc-actions-cell">
                            <button type="button" class="pc-action-btn" title="Edit Configuration" data-action="edit-config" data-id="${c.id}">
                                ${ICONS.edit}
                            </button>
                            <button type="button" class="pc-action-btn danger" title="Delete Configuration" data-action="delete-config" data-id="${c.id}">
                                ${ICONS.trash}
                            </button>
                        </div>
                    </td>
                </tr>`;
        }).join('');
    }

    // Update count badge & footer
    const countBadge = document.getElementById('activeCountBadge');
    if (countBadge && state.activeTab === 'configuration') {
        countBadge.textContent = `${filtered.length} of ${state.configurations.length} Configurations`;
    }
    const footerCount = document.getElementById('configurationsFooterCount');
    if (footerCount) {
        footerCount.textContent = `Showing ${filtered.length} of ${state.configurations.length} total configuration parameters`;
    }
}

// Update category filter dropdown based on active dataset
function updateCategoryFilterOptions() {
    const select = document.getElementById('unifiedCategoryFilter');
    if (!select) return;

    const previousVal = select.value;
    const items = state.activeTab === 'policies' ? state.policies : state.configurations;
    const categories = Array.from(new Set(items.map(x => x.category).filter(Boolean))).sort();

    select.innerHTML = `<option value="">All Categories</option>` +
        categories.map(cat => `<option value="${esc(cat)}">${esc(cat)}</option>`).join('');

    if (categories.includes(previousVal)) {
        select.value = previousVal;
    } else {
        select.value = '';
    }
}

// Switch between Policies and Configuration views (Bottom 2-Button Toggle)
function switchTab(targetTab) {
    if (targetTab !== 'policies' && targetTab !== 'configuration') return;
    state.activeTab = targetTab;

    // Toggle button active classes
    const btnPolicies = document.getElementById('btnTabPolicies');
    const btnConfig = document.getElementById('btnTabConfig');
    if (btnPolicies) btnPolicies.classList.toggle('active', targetTab === 'policies');
    if (btnConfig) btnConfig.classList.toggle('active', targetTab === 'configuration');

    // Toggle view panels
    const policiesView = document.getElementById('policiesView');
    const configView = document.getElementById('configurationsView');
    if (policiesView) {
        policiesView.classList.toggle('hidden', targetTab !== 'policies');
        policiesView.classList.toggle('active', targetTab === 'policies');
    }
    if (configView) {
        configView.classList.toggle('hidden', targetTab !== 'configuration');
        configView.classList.toggle('active', targetTab === 'configuration');
    }

    // Update header controls
    const titleEl = document.getElementById('activeSectionTitle');
    const iconEl = document.getElementById('sectionIcon');
    const searchInput = document.getElementById('unifiedSearch');
    const addBtnLabel = document.getElementById('addBtnLabel');

    if (targetTab === 'policies') {
        if (titleEl) titleEl.textContent = 'Policies';
        if (iconEl) iconEl.innerHTML = ICONS.shield;
        if (searchInput) searchInput.placeholder = 'Search policies...';
        if (addBtnLabel) addBtnLabel.textContent = 'Add Policy';
    } else {
        if (titleEl) titleEl.textContent = 'Configuration';
        if (iconEl) iconEl.innerHTML = ICONS.sliders;
        if (searchInput) searchInput.placeholder = 'Search configuration...';
        if (addBtnLabel) addBtnLabel.textContent = 'Add Configuration';
    }

    updateCategoryFilterOptions();

    if (targetTab === 'policies') {
        renderPolicies();
    } else {
        renderConfigurations();
    }
}

// Initial Data Load
async function loadData() {
    try {
        const data = await api('/api/policies/dashboard');
        state.policies = data.policies || [];
        state.configurations = data.configurations || [];

        const pCount = document.getElementById('policiesPillCount');
        const cCount = document.getElementById('configPillCount');
        if (pCount) pCount.textContent = state.policies.length;
        if (cCount) cCount.textContent = state.configurations.length;

        updateCategoryFilterOptions();

        if (state.activeTab === 'policies') {
            renderPolicies();
        } else {
            renderConfigurations();
        }
    } catch (err) {
        console.error('Failed to load policies/config data:', err);
        const policiesBody = document.getElementById('policiesTableBody');
        if (policiesBody) {
            policiesBody.innerHTML = `
                <tr>
                    <td colspan="8" class="pc-empty-cell" style="color: #dc2626;">
                        Failed to load data: ${esc(err.message)}
                    </td>
                </tr>`;
        }
    }
}

// Modals
function openPolicyModal(policy = null) {
    state.policyId = policy ? policy.id : null;
    const title = document.getElementById('policyModalTitle');
    if (title) title.textContent = policy ? 'Edit Policy' : 'Add Policy';

    document.getElementById('policyName').value = policy ? policy.name || '' : '';
    document.getElementById('policyCategory').value = policy ? policy.category || 'General' : 'General';
    document.getElementById('policyDescription').value = policy ? policy.description || '' : '';
    document.getElementById('policyValue').value = policy ? policy.policy_value || '' : '';
    document.getElementById('policyFormStatus').value = policy ? policy.status || 'ACTIVE' : 'ACTIVE';

    setFormError('policyError', '');
    const modal = document.getElementById('policyModal');
    if (modal) {
        modal.classList.add('open');
        modal.setAttribute('aria-hidden', 'false');
    }
}

function openConfigurationModal(config = null) {
    state.configurationId = config ? config.id : null;
    const title = document.getElementById('configurationModalTitle');
    if (title) title.textContent = config ? 'Edit Configuration' : 'Add Configuration';

    document.getElementById('configurationKey').value = config ? config.config_key || '' : '';
    document.getElementById('configurationCategory').value = config ? config.category || 'General' : 'General';
    document.getElementById('configurationValue').value = config ? config.config_value || '' : '';
    document.getElementById('configurationDescription').value = config ? config.description || '' : '';
    document.getElementById('configurationFormStatus').value = config ? config.status || 'ACTIVE' : 'ACTIVE';

    setFormError('configurationError', '');
    const modal = document.getElementById('configurationModal');
    if (modal) {
        modal.classList.add('open');
        modal.setAttribute('aria-hidden', 'false');
    }
}

function closeModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.remove('open');
        modal.setAttribute('aria-hidden', 'true');
    }
}

// Event Listeners Setup
function setupEventListeners() {
    // Bottom 2-button switch
    const btnTabPolicies = document.getElementById('btnTabPolicies');
    const btnTabConfig = document.getElementById('btnTabConfig');
    if (btnTabPolicies) {
        btnTabPolicies.addEventListener('click', () => switchTab('policies'));
    }
    if (btnTabConfig) {
        btnTabConfig.addEventListener('click', () => switchTab('configuration'));
    }

    // Add button in card header
    const openAddBtn = document.getElementById('openAddModalBtn');
    if (openAddBtn) {
        openAddBtn.addEventListener('click', () => {
            if (state.activeTab === 'policies') {
                openPolicyModal();
            } else {
                openConfigurationModal();
            }
        });
    }

    // Modal close triggers
    document.querySelectorAll('[data-close]').forEach(btn => {
        btn.addEventListener('click', () => {
            const targetId = btn.getAttribute('data-close');
            if (targetId) closeModal(targetId);
        });
    });

    // Unified search and filters
    const searchInput = document.getElementById('unifiedSearch');
    const statusSelect = document.getElementById('unifiedStatusFilter');
    const categorySelect = document.getElementById('unifiedCategoryFilter');

    const handleFilterChange = () => {
        if (state.activeTab === 'policies') {
            renderPolicies();
        } else {
            renderConfigurations();
        }
    };

    if (searchInput) searchInput.addEventListener('input', handleFilterChange);
    if (statusSelect) statusSelect.addEventListener('change', handleFilterChange);
    if (categorySelect) categorySelect.addEventListener('change', handleFilterChange);

    // Table action clicks (Edit / Delete) via event delegation
    const policiesTable = document.getElementById('policiesTable');
    if (policiesTable) {
        policiesTable.addEventListener('click', async (e) => {
            const btn = e.target.closest('[data-action]');
            if (!btn) return;
            const id = Number(btn.dataset.id);
            const policy = state.policies.find(p => Number(p.id) === id);

            if (btn.dataset.action === 'edit-policy') {
                openPolicyModal(policy);
            } else if (btn.dataset.action === 'delete-policy') {
                if (!policy) return;
                if (confirm(`Are you sure you want to delete policy "${policy.name}"?`)) {
                    try {
                        await api(`/api/policies/delete?id=${id}`, { method: 'DELETE' });
                        toast(`Policy "${policy.name}" deleted.`);
                        loadData();
                    } catch (err) {
                        toast(`Failed to delete: ${err.message}`);
                    }
                }
            }
        });
    }

    const configsTable = document.getElementById('configurationsTable');
    if (configsTable) {
        configsTable.addEventListener('click', async (e) => {
            const btn = e.target.closest('[data-action]');
            if (!btn) return;
            const id = Number(btn.dataset.id);
            const config = state.configurations.find(c => Number(c.id) === id);

            if (btn.dataset.action === 'edit-config') {
                openConfigurationModal(config);
            } else if (btn.dataset.action === 'delete-config') {
                if (!config) return;
                if (confirm(`Are you sure you want to delete configuration "${config.config_key}"?`)) {
                    try {
                        await api(`/api/configurations/delete?id=${id}`, { method: 'DELETE' });
                        toast(`Configuration "${config.config_key}" deleted.`);
                        loadData();
                    } catch (err) {
                        toast(`Failed to delete: ${err.message}`);
                    }
                }
            }
        });
    }

    // Policy Form Submit
    const policyForm = document.getElementById('policyForm');
    if (policyForm) {
        policyForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const name = document.getElementById('policyName').value.trim();
            const category = document.getElementById('policyCategory').value.trim();
            const description = document.getElementById('policyDescription').value.trim();
            const policy_value = document.getElementById('policyValue').value;
            const status = document.getElementById('policyFormStatus').value;

            const payload = { name, category, description, policy_value, status };

            try {
                if (state.policyId) {
                    await api(`/api/policies/update?id=${state.policyId}`, {
                        method: 'PUT',
                        body: JSON.stringify(payload)
                    });
                    toast('Policy updated successfully.');
                } else {
                    await api('/api/policies', {
                        method: 'POST',
                        body: JSON.stringify(payload)
                    });
                    toast('Policy created successfully.');
                }
                closeModal('policyModal');
                loadData();
            } catch (err) {
                setFormError('policyError', err.message);
            }
        });
    }

    // Configuration Form Submit
    const configForm = document.getElementById('configurationForm');
    if (configForm) {
        configForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const config_key = document.getElementById('configurationKey').value.trim();
            const category = document.getElementById('configurationCategory').value.trim();
            const config_value = document.getElementById('configurationValue').value;
            const description = document.getElementById('configurationDescription').value.trim();
            const status = document.getElementById('configurationFormStatus').value;

            const payload = { config_key, category, config_value, description, status };

            try {
                if (state.configurationId) {
                    await api(`/api/configurations/update?id=${state.configurationId}`, {
                        method: 'PUT',
                        body: JSON.stringify(payload)
                    });
                    toast('Configuration updated successfully.');
                } else {
                    await api('/api/configurations', {
                        method: 'POST',
                        body: JSON.stringify(payload)
                    });
                    toast('Configuration created successfully.');
                }
                closeModal('configurationModal');
                loadData();
            } catch (err) {
                setFormError('configurationError', err.message);
            }
        });
    }
}

// Initialization
document.addEventListener('DOMContentLoaded', () => {
    setupEventListeners();
    loadData();
});

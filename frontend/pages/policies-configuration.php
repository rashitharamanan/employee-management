<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Policies & Configuration - Admin Portal. Manage enterprise system policies and global configurations.">
    <title>Policies &amp; Configuration - Admin Portal</title>
    <link rel="stylesheet" href="../assets/css/app-theme.css?v=<?= time() ?>">
    <link rel="stylesheet" href="../assets/css/sidebar.css?v=<?= time() ?>">
    <link rel="stylesheet" href="../assets/css/topbar.css?v=<?= time() ?>">
    <link rel="stylesheet" href="../assets/css/policies-configuration.css?v=<?= time() ?>">
</head>
<body>
<div class="app">
    <?php include '../includes/sidebar.php'; ?>

    <main class="main-content">
        <?php include '../includes/topbar.php'; ?>

        <div class="pc-container">

            <!-- MAIN DATA CARD (Seamless elevated container matching sketch) -->
            <section class="pc-card">
                <!-- CARD HEADER CONTROLS -->
                <div class="pc-card-header">
                    <!-- Left: Policies & Configuration Toggle Buttons -->
                    <div class="pc-toggle-pill-bar" role="tablist">
                        <button type="button" class="pc-tab-pill active" id="btnTabPolicies" data-tab="policies">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                            </svg>
                            <span>Policies</span>
                            <span class="pc-pill-count" id="policiesPillCount">0</span>
                        </button>
                        <button type="button" class="pc-tab-pill" id="btnTabConfig" data-tab="configuration">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="12" cy="12" r="3"/>
                                <path d="M19.4 15a1.7 1.7 0 0 0 .34 1.88l.06.06-1.4 1.4-.06-.06A1.7 1.7 0 0 0 16.46 18a1.7 1.7 0 0 0-1 .98V19a1.7 1.7 0 0 0-1.7 1.7v.08h-2v-.08A1.7 1.7 0 0 0 10.06 19h-.08a1.7 1.7 0 0 0-1-.98 1.7 1.7 0 0 0-1.88.34l-.06.06-1.4-1.4.06-.06A1.7 1.7 0 0 0 6 15.04a1.7 1.7 0 0 0-.98-1H5A1.7 1.7 0 0 0 3.3 12.3H3.2v-2h.1A1.7 1.7 0 0 0 5 9.06v-.08a1.7 1.7 0 0 0-.34-1.88L4.6 7.04 6 5.64l.06.06A1.7 1.7 0 0 0 7.94 6a1.7 1.7 0 0 0 1-.98V5A1.7 1.7 0 0 0 10.64 3.3h2A1.7 1.7 0 0 0 14.34 5v.02a1.7 1.7 0 0 0 1 .98 1.7 1.7 0 0 0 1.88-.34l.06-.06 1.4 1.4-.06.06A1.7 1.7 0 0 0 18.28 9v.08a1.7 1.7 0 0 0 .98 1h.1v2h-.1a1.7 1.7 0 0 0-1.7 1.7v.08a1.7 1.7 0 0 0 1.84 1.14z"/>
                            </svg>
                            <span>Configuration</span>
                            <span class="pc-pill-count" id="configPillCount">0</span>
                        </button>
                    </div>

                    <!-- Right: Filters, Count, & Add Button -->
                    <div class="pc-card-filters">
                        <span class="pc-count-badge" id="activeCountBadge">Loading...</span>

                        <div class="pc-filter-select-wrap">
                            <select id="unifiedStatusFilter" class="pc-select" aria-label="Filter by status">
                                <option value="">All Status</option>
                                <option value="ACTIVE">Active</option>
                                <option value="INACTIVE">Inactive</option>
                            </select>
                        </div>

                        <div class="pc-filter-select-wrap">
                            <select id="unifiedCategoryFilter" class="pc-select" aria-label="Filter by category">
                                <option value="">All Categories</option>
                            </select>
                        </div>

                        <button type="button" class="pc-btn-primary" id="openAddModalBtn">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
                            </svg>
                            <span id="addBtnLabel">Add Policy</span>
                        </button>
                    </div>
                </div>

                <!-- POLICIES TABLE PANEL -->
                <div class="pc-table-view active" id="policiesView">
                    <div class="pc-table-wrapper">
                        <table class="pc-table" id="policiesTable">
                            <thead>
                                <tr>
                                    <th style="width: 50px;">#</th>
                                    <th>Policy Name</th>
                                    <th>Category</th>
                                    <th>Description</th>
                                    <th>Policy Value</th>
                                    <th>Status</th>
                                    <th>Last Updated</th>
                                    <th style="width: 100px; text-align: right;">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="policiesTableBody">
                                <tr>
                                    <td colspan="8" class="pc-empty-cell">
                                        <div class="pc-loading-spinner"></div>
                                        <span>Loading system policies...</span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="pc-table-footer">
                        <span id="policiesFooterCount">—</span>
                    </div>
                </div>

                <!-- CONFIGURATION TABLE PANEL -->
                <div class="pc-table-view hidden" id="configurationsView">
                    <div class="pc-table-wrapper">
                        <table class="pc-table" id="configurationsTable">
                            <thead>
                                <tr>
                                    <th style="width: 50px;">#</th>
                                    <th>Config Key</th>
                                    <th>Category</th>
                                    <th>Configuration Value</th>
                                    <th>Description</th>
                                    <th>Status</th>
                                    <th style="width: 100px; text-align: right;">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="configurationsTableBody">
                                <tr>
                                    <td colspan="7" class="pc-empty-cell">
                                        <div class="pc-loading-spinner"></div>
                                        <span>Loading configurations...</span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="pc-table-footer">
                        <span id="configurationsFooterCount">—</span>
                    </div>
                </div>
            </section>
        </div>
    </main>
</div>

<!-- POLICY MODAL -->
<div class="pc-modal" id="policyModal" aria-hidden="true">
    <div class="pc-modal-backdrop" data-close="policyModal"></div>
    <div class="pc-modal-card">
        <div class="pc-modal-header">
            <div class="pc-modal-title-wrap">
                <div class="pc-modal-icon">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                </div>
                <div>
                    <h2 id="policyModalTitle">Add Policy</h2>
                    <p>Create or update enterprise governance policy</p>
                </div>
            </div>
            <button type="button" class="pc-modal-close" data-close="policyModal" aria-label="Close modal">&times;</button>
        </div>
        <form id="policyForm" class="pc-form">
            <div class="pc-form-group">
                <label for="policyName">Policy Name <span class="req">*</span></label>
                <input id="policyName" type="text" required maxlength="120" placeholder="e.g. Remote Work Compliance">
            </div>
            <div class="pc-form-row">
                <div class="pc-form-group">
                    <label for="policyCategory">Category <span class="req">*</span></label>
                    <input id="policyCategory" type="text" required maxlength="60" value="General" placeholder="e.g. Attendance, Security">
                </div>
                <div class="pc-form-group">
                    <label for="policyFormStatus">Status</label>
                    <select id="policyFormStatus" class="pc-select">
                        <option value="ACTIVE">Active</option>
                        <option value="INACTIVE">Inactive</option>
                    </select>
                </div>
            </div>
            <div class="pc-form-group">
                <label for="policyDescription">Description</label>
                <textarea id="policyDescription" rows="2" maxlength="255" placeholder="Brief explanation of the policy scope..."></textarea>
            </div>
            <div class="pc-form-group">
                <label for="policyValue">Policy Value / Rule Payload <small>(Optional JSON or text)</small></label>
                <textarea id="policyValue" rows="3" class="pc-code-input" placeholder='e.g. {"grace_period_mins": 15, "requires_approval": true}'></textarea>
            </div>
            <p class="pc-form-error" id="policyError"></p>
            <div class="pc-modal-actions">
                <button type="button" class="pc-btn-secondary" data-close="policyModal">Cancel</button>
                <button type="submit" class="pc-btn-primary" id="savePolicyBtn">Save Policy</button>
            </div>
        </form>
    </div>
</div>

<!-- CONFIGURATION MODAL -->
<div class="pc-modal" id="configurationModal" aria-hidden="true">
    <div class="pc-modal-backdrop" data-close="configurationModal"></div>
    <div class="pc-modal-card">
        <div class="pc-modal-header">
            <div class="pc-modal-title-wrap">
                <div class="pc-modal-icon">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                        <circle cx="12" cy="12" r="3"/>
                        <path d="M19.4 15a1.7 1.7 0 0 0 .34 1.88l.06.06-1.4 1.4-.06-.06A1.7 1.7 0 0 0 16.46 18a1.7 1.7 0 0 0-1 .98V19a1.7 1.7 0 0 0-1.7 1.7v.08h-2v-.08A1.7 1.7 0 0 0 10.06 19h-.08a1.7 1.7 0 0 0-1-.98 1.7 1.7 0 0 0-1.88.34l-.06.06-1.4-1.4.06-.06A1.7 1.7 0 0 0 6 15.04a1.7 1.7 0 0 0-.98-1H5A1.7 1.7 0 0 0 3.3 12.3H3.2v-2h.1A1.7 1.7 0 0 0 5 9.06v-.08a1.7 1.7 0 0 0-.34-1.88L4.6 7.04 6 5.64l.06.06A1.7 1.7 0 0 0 7.94 6a1.7 1.7 0 0 0 1-.98V5A1.7 1.7 0 0 0 10.64 3.3h2A1.7 1.7 0 0 0 14.34 5v.02a1.7 1.7 0 0 0 1 .98 1.7 1.7 0 0 0 1.88-.34l.06-.06 1.4 1.4-.06.06A1.7 1.7 0 0 0 18.28 9v.08a1.7 1.7 0 0 0 .98 1h.1v2h-.1a1.7 1.7 0 0 0-1.7 1.7v.08a1.7 1.7 0 0 0 1.84 1.14z"/>
                    </svg>
                </div>
                <div>
                    <h2 id="configurationModalTitle">Add Configuration</h2>
                    <p>Create or update global system parameter</p>
                </div>
            </div>
            <button type="button" class="pc-modal-close" data-close="configurationModal" aria-label="Close modal">&times;</button>
        </div>
        <form id="configurationForm" class="pc-form">
            <div class="pc-form-group">
                <label for="configurationKey">Configuration Key <span class="req">*</span></label>
                <input id="configurationKey" type="text" required maxlength="120" placeholder="e.g. system.session_timeout">
            </div>
            <div class="pc-form-row">
                <div class="pc-form-group">
                    <label for="configurationCategory">Category <span class="req">*</span></label>
                    <input id="configurationCategory" type="text" required maxlength="60" value="General" placeholder="e.g. Security, Email, System">
                </div>
                <div class="pc-form-group">
                    <label for="configurationFormStatus">Status</label>
                    <select id="configurationFormStatus" class="pc-select">
                        <option value="ACTIVE">Active</option>
                        <option value="INACTIVE">Inactive</option>
                    </select>
                </div>
            </div>
            <div class="pc-form-group">
                <label for="configurationValue">Configuration Value</label>
                <textarea id="configurationValue" rows="3" class="pc-code-input" placeholder="Value string, boolean or payload..."></textarea>
            </div>
            <div class="pc-form-group">
                <label for="configurationDescription">Description</label>
                <textarea id="configurationDescription" rows="2" maxlength="255" placeholder="Purpose and behavior of this parameter..."></textarea>
            </div>
            <p class="pc-form-error" id="configurationError"></p>
            <div class="pc-modal-actions">
                <button type="button" class="pc-btn-secondary" data-close="configurationModal">Cancel</button>
                <button type="submit" class="pc-btn-primary" id="saveConfigurationBtn">Save Configuration</button>
            </div>
        </form>
    </div>
</div>

<!-- TOAST NOTIFICATION -->
<div class="pc-toast" id="toast"></div>

<!-- SCRIPTS -->
<script src="../assets/js/theme.js?v=1"></script>
<script src="../assets/js/policies-configuration.js?v=<?= time() ?>"></script>
</body>
</html>

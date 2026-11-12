<?php
header('Location: organisations.php');
exit;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Manage roles and permissions for the Employee QR System.">
    <title>Roles &amp; Permissions - Admin Portal</title>
    <link rel="stylesheet" href="../assets/css/app-theme.css?v=<?= time() ?>">
    <link rel="stylesheet" href="../assets/css/sidebar.css?v=<?= time() ?>">
    <link rel="stylesheet" href="../assets/css/topbar.css?v=<?= time() ?>">
    <link rel="stylesheet" href="../assets/css/roles-permissions.css?v=<?= time() ?>">
</head>
<body>
<div class="app">
    <?php include '../includes/sidebar.php'; ?>

    <main class="main-content">
        <?php include '../includes/topbar.php'; ?>

        <section class="permissions-page">
            <div class="permissions-heading">
                <div>
                    <p class="eyebrow">ACCESS CONTROL</p>
                    <h1>Roles &amp; Permissions</h1>
                    <p>Manage roles and permissions to control system access</p>
                </div>
            </div>

            <div class="tabs" role="tablist" aria-label="Access control views">
                <button class="tab active" data-tab="roles" role="tab" aria-selected="true">Roles</button>
                <button class="tab" data-tab="permissions" role="tab" aria-selected="false">Permissions</button>
            </div>

            <div class="permission-stats">
                <article class="permission-stat stat-violet"><span class="stat-mark">●</span><div><small>Total Roles</small><strong id="totalRoles">—</strong><em>All system roles</em></div></article>
                <article class="permission-stat stat-green"><span class="stat-mark">●</span><div><small>Active Roles</small><strong id="activeRoles">—</strong><em>Currently assigned</em></div></article>
                <article class="permission-stat stat-orange"><span class="stat-mark">●</span><div><small>Inactive Roles</small><strong id="inactiveRoles">—</strong><em>Not currently assigned</em></div></article>
                <article class="permission-stat stat-blue"><span class="stat-mark">◆</span><div><small>Total Permissions</small><strong id="totalPermissions">—</strong><em>Across all roles</em></div></article>
            </div>

            <section class="data-panel" id="rolesPanel">
                <div class="panel-heading">
                    <div><h2>Roles List</h2><span id="rolesCount">Loading roles...</span></div>
                    <div class="panel-actions"><input id="roleSearch" type="search" placeholder="Search roles..."><select id="roleStatus"><option value="">All Status</option><option value="active">Active</option><option value="inactive">Inactive</option></select><button class="primary-button" id="addRoleButton">+ Add Role</button></div>
                </div>
                <div class="table-scroll"><table><thead><tr><th>#</th><th>Role Name</th><th>Description</th><th>Users</th><th>Permissions</th><th>Status</th><th>Created Date</th><th>Actions</th></tr></thead><tbody id="rolesBody"><tr><td colspan="8" class="empty-cell">Loading roles...</td></tr></tbody></table></div>
                <div class="panel-footer"><span id="rolesFooter">—</span></div>
            </section>

            <section class="data-panel hidden" id="permissionsPanel">
                <div class="panel-heading">
                    <div><h2>Permissions List</h2><span id="permissionsCount">Loading permissions...</span></div>
                    <div class="panel-actions"><input id="permissionSearch" type="search" placeholder="Search permissions..."><select id="moduleFilter"><option value="">All Modules</option></select><button class="primary-button" id="addPermissionButton">+ Add Permission</button></div>
                </div>
                <div class="table-scroll"><table><thead><tr><th>#</th><th>Permission Key</th><th>Module</th><th>Description</th><th>Assigned Roles</th><th>Status</th><th>Actions</th></tr></thead><tbody id="permissionsBody"><tr><td colspan="7" class="empty-cell">Loading permissions...</td></tr></tbody></table></div>
                <div class="panel-footer"><span id="permissionsFooter">—</span></div>
            </section>
        </section>
    </main>
</div>

<div class="modal" id="roleModal" aria-hidden="true"><div class="modal-card"><div class="modal-header"><div><h2 id="roleModalTitle">Add Role</h2><p>Create a role for your access model.</p></div><button class="close-button" data-close="roleModal" aria-label="Close">&times;</button></div><form id="roleForm"><label>Role name<input id="roleName" required maxlength="80" placeholder="e.g. ACCOUNTANT"></label><label>Description<textarea id="roleDescription" maxlength="255" rows="3" placeholder="Describe this role"></textarea></label><p class="form-error" id="roleError"></p><div class="modal-actions"><button type="button" class="secondary-button" data-close="roleModal">Cancel</button><button type="submit" class="primary-button">Save Role</button></div></form></div></div>
<div class="modal" id="permissionModal" aria-hidden="true"><div class="modal-card permission-modal-card"><div class="modal-header"><div><h2>Assign Permissions</h2><p id="permissionRoleName">Choose what this role can access.</p></div><button class="close-button" data-close="permissionModal" aria-label="Close">&times;</button></div><form id="permissionForm"><div class="permission-checklist" id="permissionChecklist"></div><p class="form-error" id="permissionError"></p><div class="modal-actions"><button type="button" class="secondary-button" data-close="permissionModal">Cancel</button><button type="submit" class="primary-button">Save Permissions</button></div></form></div></div>
<div class="toast" id="toast" role="status"></div>
<script src="../assets/js/theme.js?v=1"></script>
<script src="../assets/js/roles-permissions.js?v=1"></script>
</body>
</html>

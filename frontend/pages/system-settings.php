<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>System Settings - Admin Portal</title>
    <link rel="stylesheet" href="../assets/css/app-theme.css?v=<?= time() ?>">
    <link rel="stylesheet" href="../assets/css/sidebar.css?v=<?= time() ?>">
    <link rel="stylesheet" href="../assets/css/topbar.css?v=<?= time() ?>">
    <link rel="stylesheet" href="../assets/css/system-settings.css?v=<?= time() ?>">
</head>
<body>
<div class="app">
    <?php include '../includes/sidebar.php'; ?>
    <main class="main-content">
        <?php include '../includes/topbar.php'; ?>
        <section class="settings-page">
            <!-- 4 KPI Stat Cards in LUNA Theme Palette -->
            <div class="settings-stats">
                <article class="stat-luna-card stat-luna-blue">
                    <div class="stat-wave"></div>
                    <div class="stat-icon-wrapper">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="3"></circle>
                            <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path>
                        </svg>
                    </div>
                    <div class="stat-info">
                        <small>System Version</small>
                        <strong>v1.0.0 <em class="stat-badge">Latest</em></strong>
                        <i>Released on 20 May 2025</i>
                    </div>
                </article>
                <article class="stat-luna-card stat-luna-teal">
                    <div class="stat-wave"></div>
                    <div class="stat-icon-wrapper">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="10"></circle>
                            <polyline points="12 6 12 12 16 14"></polyline>
                        </svg>
                    </div>
                    <div class="stat-info">
                        <small>Server Time</small>
                        <strong id="serverTime">—</strong>
                        <i id="serverTimezone">—</i>
                    </div>
                </article>
                <article class="stat-luna-card stat-luna-emerald">
                    <div class="stat-wave"></div>
                    <div class="stat-icon-wrapper">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <ellipse cx="12" cy="5" rx="9" ry="3"></ellipse>
                            <path d="M21 12c0 1.66-4 3-9 3s-9-1.34-9-3"></path>
                            <path d="M3 5v14c0 1.66 4 3 9 3s9-1.34 9-3V5"></path>
                        </svg>
                    </div>
                    <div class="stat-info">
                        <small>Database Status</small>
                        <strong id="databaseStatus">—</strong>
                        <i>All services running well</i>
                    </div>
                </article>
                <article class="stat-luna-card stat-luna-navy">
                    <div class="stat-wave"></div>
                    <div class="stat-icon-wrapper">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path>
                            <polyline points="17 21 17 13 7 13 7 21"></polyline>
                            <polyline points="7 3 7 8 15 8"></polyline>
                        </svg>
                    </div>
                    <div class="stat-info">
                        <small>Last Backup</small>
                        <strong id="lastBackup">Not configured</strong>
                        <i>Backup status</i>
                    </div>
                </article>
            </div>

            <!-- Settings Grid -->
            <div class="settings-grid">
                <section class="settings-card">
                    <div class="card-heading">
                        <div>
                            <h2>General Settings</h2>
                            <p>Configure general system preferences</p>
                        </div>
                    </div>
                    <form id="settingsForm">
                        <label>System Name
                            <small>Enter the system name
                                <input id="system_name" name="system_name" placeholder="Employee QR Management Suite">
                            </small>
                        </label>
                        <label>Date Format
                            <small>Select the default date format
                                <select id="date_format" name="date_format">
                                    <option>DD MMM YYYY</option>
                                    <option>YYYY-MM-DD</option>
                                    <option>MM/DD/YYYY</option>
                                </select>
                            </small>
                        </label>
                        <label>Time Format
                            <small>Select the default time format
                                <select id="time_format" name="time_format">
                                    <option>12 Hour</option>
                                    <option>24 Hour</option>
                                </select>
                            </small>
                        </label>
                        <label>Timezone
                            <small>Select the system timezone
                                <select id="timezone" name="timezone">
                                    <option>Asia/Kolkata</option>
                                    <option>UTC</option>
                                    <option>America/New_York</option>
                                    <option>Europe/London</option>
                                </select>
                            </small>
                        </label>
                        <label>Language
                            <small>Select the default language
                                <select id="language" name="language">
                                    <option>English (US)</option>
                                    <option>English (UK)</option>
                                </select>
                            </small>
                        </label>
                        <label>Items Per Page
                            <small>Select default items per page
                                <select id="items_per_page" name="items_per_page">
                                    <option>10</option>
                                    <option>25</option>
                                    <option>50</option>
                                    <option>100</option>
                                </select>
                            </small>
                        </label>
                        <label class="toggle-row">Maintenance Mode
                            <small>Enable to put system in maintenance mode
                                <label class="toggle">
                                    <input type="checkbox" id="maintenance_mode">
                                    <span></span>
                                </label>
                                <b id="maintenanceLabel">System is live</b>
                            </small>
                        </label>
                        <p class="form-error" id="settingsError"></p>
                        <button class="primary-button" type="submit">▣ Save Changes</button>
                    </form>
                </section>

                <aside>
                    <section class="settings-card info-card">
                        <div class="card-heading">
                            <div>
                                <h2>System Information</h2>
                                <p>Live application and server details</p>
                            </div>
                        </div>
                        <dl id="systemInfo">
                            <dt>Application Name</dt><dd>—</dd>
                            <dt>Environment</dt><dd>—</dd>
                            <dt>PHP Version</dt><dd>—</dd>
                            <dt>Database</dt><dd>—</dd>
                            <dt>Web Server</dt><dd>—</dd>
                            <dt>Server OS</dt><dd>—</dd>
                            <dt>Total Users</dt><dd>—</dd>
                            <dt>Total Employees</dt><dd>—</dd>
                            <dt>Total Organisations</dt><dd>—</dd>
                            <dt>Storage Used</dt><dd>—</dd>
                        </dl>
                    </section>
                    <section class="settings-card quick-card">
                        <div class="card-heading">
                            <h2>Quick Actions</h2>
                        </div>
                        <div class="quick-settings">
                            <button data-action="clear-cache" type="button">◉<span>Clear Cache<small>Clear system cache</small></span></button>
                            <button data-action="system-logs" type="button">◷<span>System Logs<small>View system logs</small></span></button>
                            <button data-action="check-updates" type="button">⇧<span>Check Updates<small>Check for updates</small></span></button>
                            <button data-action="system-info" type="button">◈<span>System Info<small>View system info</small></span></button>
                        </div>
                    </section>
                </aside>
            </div>
        </section>
    </main>
</div>
<div class="toast" id="toast"></div>
<script src="../assets/js/theme.js?v=1"></script>
<script src="../assets/js/system-settings.js?v=1"></script>
</body>
</html>

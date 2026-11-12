<!-- =====================================================
     UNIVERSAL USER PROFILE MODAL & SYNC COMPONENT
     Works across all portals: Admin, HR, Manager, Employee
====================================================== -->
<div id="userProfileModal" class="up-modal-backdrop" aria-hidden="true" style="display:none;">
    <div class="up-modal-card" role="dialog" aria-modal="true" aria-labelledby="upModalName">
        
        <!-- MODAL HEADER / BANNER -->
        <div class="up-modal-header">
            <button type="button" class="up-close-btn" id="upCloseModalBtn" aria-label="Close Profile">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="18" y1="6" x2="6" y2="18"/>
                    <line x1="6" y1="6" x2="18" y2="18"/>
                </svg>
            </button>

            <div class="up-header-avatar-wrap">
                <div class="up-modal-avatar" id="upModalAvatar">--</div>
                <span class="up-status-dot" title="Active"></span>
            </div>

            <h2 class="up-modal-name" id="upModalName">Loading User...</h2>
            <div class="up-modal-badges">
                <span class="up-role-badge" id="upModalRole">User</span>
                <span class="up-status-badge" id="upModalStatus">ACTIVE</span>
            </div>
            <p class="up-modal-email" id="upModalEmail">user@company.com</p>
        </div>

        <!-- MODAL BODY: USER DETAILS GRID -->
        <div class="up-modal-body">
            <h3 class="up-section-title">Account & Employment Details</h3>

            <div class="up-details-grid">
                <!-- Username -->
                <div class="up-detail-item">
                    <span class="up-detail-label">Username</span>
                    <strong class="up-detail-val" id="upDetailUsername">—</strong>
                </div>

                <!-- Role / Access -->
                <div class="up-detail-item">
                    <span class="up-detail-label">System Role</span>
                    <strong class="up-detail-val" id="upDetailSystemRole">—</strong>
                </div>

                <!-- Designation -->
                <div class="up-detail-item">
                    <span class="up-detail-label">Designation / Title</span>
                    <strong class="up-detail-val" id="upDetailDesignation">—</strong>
                </div>

                <!-- Department -->
                <div class="up-detail-item">
                    <span class="up-detail-label">Department</span>
                    <strong class="up-detail-val" id="upDetailDepartment">—</strong>
                </div>

                <!-- Employee Code -->
                <div class="up-detail-item">
                    <span class="up-detail-label">Employee Code</span>
                    <strong class="up-detail-val" id="upDetailEmpCode">—</strong>
                </div>

                <!-- Phone -->
                <div class="up-detail-item">
                    <span class="up-detail-label">Contact Phone</span>
                    <strong class="up-detail-val" id="upDetailPhone">—</strong>
                </div>

                <!-- Official Email -->
                <div class="up-detail-item up-grid-full">
                    <span class="up-detail-label">Official Email</span>
                    <strong class="up-detail-val" id="upDetailEmail">—</strong>
                </div>

                <!-- Last Login -->
                <div class="up-detail-item up-grid-full">
                    <span class="up-detail-label">Last Session Login</span>
                    <span class="up-detail-val up-val-muted" id="upDetailLastLogin">Recent Session Active</span>
                </div>
            </div>
        </div>

        <!-- MODAL FOOTER -->
        <div class="up-modal-footer">
            <button type="button" class="up-btn up-btn-secondary" id="upModalCloseFooterBtn">
                Close
            </button>
            <button type="button" class="up-btn up-btn-logout" id="upModalLogoutBtn">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
                    <polyline points="16 17 21 12 16 7"/>
                    <line x1="21" y1="12" x2="9" y2="12"/>
                </svg>
                Sign Out
            </button>
        </div>

    </div>
</div>

<!-- UNIVERSAL TOAST FEEDBACK CONTAINER -->
<div id="topbarToastContainer" class="topbar-toast-container" aria-live="polite"></div>

<style>
/* =====================================================
   UNIVERSAL TOPBAR ACTION DROPDOWNS & TOASTS
====================================================== */
.topbar-action-wrapper,
.emp-notif-wrapper {
    position: relative !important;
    display: inline-flex !important;
    align-items: center !important;
}

.topbar-dropdown-panel,
.emp-notif-panel {
    position: absolute !important;
    top: calc(100% + 12px) !important;
    right: -6px !important;
    width: 350px !important;
    max-width: calc(100vw - 32px) !important;
    background: #ffffff !important;
    border: 1px solid #e2e8f0 !important;
    border-radius: 16px !important;
    box-shadow: 0 20px 45px -10px rgba(2, 56, 89, 0.22), 0 0 0 1px rgba(2, 56, 89, 0.05) !important;
    z-index: 10000 !important;
    overflow: hidden !important;
    animation: topbarDropdownFadeIn 0.22s cubic-bezier(0.16, 1, 0.3, 1) !important;
    transform-origin: top right !important;
    text-align: left !important;
    box-sizing: border-box !important;
}

@keyframes topbarDropdownFadeIn {
    from { opacity: 0; transform: translateY(-8px) scale(0.97); }
    to { opacity: 1; transform: translateY(0) scale(1); }
}

.topbar-dropdown-header,
.emp-notif-header {
    display: flex !important;
    align-items: center !important;
    justify-content: space-between !important;
    padding: 13px 18px !important;
    background: #f8fafc !important;
    border-bottom: 1px solid #e2e8f0 !important;
}

.topbar-dropdown-title,
.emp-notif-header-title {
    display: flex !important;
    align-items: center !important;
    gap: 8px !important;
    font-size: 13.5px !important;
    font-weight: 700 !important;
    color: #0f172a !important;
}

.topbar-count-pill,
.emp-notif-count-pill {
    background: #ef4444 !important;
    color: #ffffff !important;
    font-size: 10.5px !important;
    font-weight: 700 !important;
    padding: 2px 7px !important;
    border-radius: 10px !important;
    line-height: 1.2 !important;
}

.topbar-count-pill.pill-blue {
    background: #0284c7 !important;
}

.topbar-mark-all-btn,
.emp-notif-mark-all {
    border: none !important;
    background: transparent !important;
    color: #0284c7 !important;
    font-size: 11.5px !important;
    font-weight: 600 !important;
    cursor: pointer !important;
    padding: 2px 4px !important;
    transition: color 0.15s ease !important;
}

.topbar-mark-all-btn:hover,
.emp-notif-mark-all:hover {
    color: #0369a1 !important;
    text-decoration: underline !important;
}

.topbar-dropdown-body,
.emp-notif-body {
    max-height: 360px !important;
    overflow-y: auto !important;
}

.topbar-notif-item,
.emp-notif-item {
    display: flex !important;
    align-items: flex-start !important;
    gap: 12px !important;
    padding: 13px 18px !important;
    border-bottom: 1px solid #f1f5f9 !important;
    transition: background 0.15s ease !important;
    cursor: pointer !important;
    text-decoration: none !important;
}

.topbar-notif-item:hover,
.emp-notif-item:hover {
    background: #f8fafc !important;
}

.topbar-notif-item.unread,
.emp-notif-item.unread {
    background: #f0f9ff !important;
    border-left: 3px solid #0284c7 !important;
}

.topbar-notif-icon,
.emp-notif-icon {
    width: 32px !important;
    height: 32px !important;
    border-radius: 50% !important;
    background: #e0f2fe !important;
    color: #0284c7 !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    flex-shrink: 0 !important;
    font-size: 14px !important;
}

.topbar-notif-content,
.emp-notif-content {
    flex: 1 !important;
    min-width: 0 !important;
}

.topbar-notif-title,
.emp-notif-title {
    font-size: 13px !important;
    font-weight: 700 !important;
    color: #0f172a !important;
    margin: 0 0 3px 0 !important;
    line-height: 1.3 !important;
}

.topbar-notif-msg,
.emp-notif-msg {
    font-size: 12px !important;
    color: #64748b !important;
    margin: 0 0 4px 0 !important;
    line-height: 1.35 !important;
    word-break: break-word !important;
}

.topbar-notif-time,
.emp-notif-time {
    font-size: 10.5px !important;
    color: #94a3b8 !important;
    font-weight: 500 !important;
}

.topbar-dropdown-empty,
.emp-notif-empty {
    text-align: center !important;
    padding: 32px 18px !important;
    color: #64748b !important;
    font-size: 13px !important;
    line-height: 1.4 !important;
}

/* UNIVERSAL TOAST CONTAINER */
.topbar-toast-container {
    position: fixed !important;
    top: 24px !important;
    right: 24px !important;
    z-index: 100001 !important;
    display: flex !important;
    flex-direction: column !important;
    gap: 10px !important;
    pointer-events: none !important;
}

.topbar-toast {
    display: inline-flex !important;
    align-items: center !important;
    gap: 10px !important;
    background: #0f172a !important;
    color: #ffffff !important;
    padding: 11px 18px !important;
    border-radius: 12px !important;
    font-size: 13px !important;
    font-weight: 500 !important;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.25) !important;
    pointer-events: auto !important;
    animation: topbarToastIn 0.25s cubic-bezier(0.16, 1, 0.3, 1) !important;
    max-width: 360px !important;
}

.topbar-toast.toast-success {
    background: #065f46 !important;
}

.topbar-toast.toast-error {
    background: #991b1b !important;
}

.topbar-toast.toast-info {
    background: #011C40 !important;
}

@keyframes topbarToastIn {
    from { opacity: 0; transform: translateY(-12px) scale(0.96); }
    to { opacity: 1; transform: translateY(0) scale(1); }
}

/* =====================================================
   UNIVERSAL PROFILE MODAL STYLES
====================================================== */
.up-modal-backdrop {
    position: fixed !important;
    inset: 0 !important;
    background: rgba(15, 23, 42, 0.65) !important;
    backdrop-filter: blur(8px) !important;
    -webkit-backdrop-filter: blur(8px) !important;
    z-index: 100000 !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    padding: 16px !important;
    opacity: 0;
    pointer-events: none;
    transition: opacity 0.25s ease-out !important;
    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif !important;
    box-sizing: border-box !important;
}

.up-modal-backdrop.up-show {
    opacity: 1 !important;
    pointer-events: auto !important;
}

.up-modal-card {
    background: #ffffff !important;
    width: 100% !important;
    max-width: 480px !important;
    border-radius: 20px !important;
    overflow: hidden !important;
    box-shadow: 0 25px 60px -15px rgba(2, 56, 89, 0.3), 0 0 0 1px rgba(2, 56, 89, 0.08) !important;
    transform: scale(0.92) translateY(12px) !important;
    transition: transform 0.25s cubic-bezier(0.16, 1, 0.3, 1) !important;
    display: flex !important;
    flex-direction: column !important;
    box-sizing: border-box !important;
}

.up-modal-backdrop.up-show .up-modal-card {
    transform: scale(1) translateY(0) !important;
}

/* HEADER */
.up-modal-header {
    background: linear-gradient(135deg, #011C40 0%, #023859 65%, #0d5c75 100%) !important;
    padding: 30px 24px 24px !important;
    text-align: center !important;
    position: relative !important;
    color: #ffffff !important;
}

.up-close-btn {
    position: absolute !important;
    top: 16px !important;
    right: 16px !important;
    width: 34px !important;
    height: 34px !important;
    border-radius: 50% !important;
    background: rgba(255, 255, 255, 0.15) !important;
    border: none !important;
    color: #ffffff !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    cursor: pointer !important;
    transition: all 0.2s ease !important;
    padding: 0 !important;
}

.up-close-btn:hover {
    background: rgba(255, 255, 255, 0.3) !important;
    transform: rotate(90deg) !important;
}

.up-header-avatar-wrap {
    position: relative !important;
    display: inline-block !important;
    margin-bottom: 12px !important;
}

.up-modal-avatar {
    width: 76px !important;
    height: 76px !important;
    border-radius: 50% !important;
    background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%) !important;
    color: #ffffff !important;
    font-size: 26px !important;
    font-weight: 800 !important;
    letter-spacing: 0.5px !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    margin: 0 auto !important;
    border: 3.5px solid rgba(255, 255, 255, 0.95) !important;
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.25) !important;
    text-transform: uppercase !important;
}

.up-status-dot {
    position: absolute !important;
    bottom: 4px !important;
    right: 4px !important;
    width: 14px !important;
    height: 14px !important;
    background: #10b981 !important;
    border: 2.5px solid #ffffff !important;
    border-radius: 50% !important;
    box-shadow: 0 0 8px rgba(16, 185, 129, 0.8) !important;
}

.up-modal-name {
    font-size: 20px !important;
    font-weight: 700 !important;
    margin: 0 0 6px 0 !important;
    color: #ffffff !important;
    letter-spacing: -0.3px !important;
}

.up-modal-badges {
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    gap: 8px !important;
    margin-bottom: 6px !important;
}

.up-role-badge {
    background: rgba(255, 255, 255, 0.2) !important;
    color: #e0f2fe !important;
    font-size: 11.5px !important;
    font-weight: 600 !important;
    padding: 3px 10px !important;
    border-radius: 20px !important;
    border: 1px solid rgba(255, 255, 255, 0.25) !important;
    text-transform: capitalize !important;
}

.up-status-badge {
    background: rgba(16, 185, 129, 0.2) !important;
    color: #6ee7b7 !important;
    font-size: 11px !important;
    font-weight: 700 !important;
    padding: 3px 9px !important;
    border-radius: 20px !important;
    border: 1px solid rgba(16, 185, 129, 0.4) !important;
    letter-spacing: 0.4px !important;
}

.up-modal-email {
    margin: 0 !important;
    font-size: 13px !important;
    color: #bae6fd !important;
    word-break: break-all !important;
}

/* BODY */
.up-modal-body {
    padding: 22px 24px !important;
    background: #f8fafc !important;
    max-height: calc(85vh - 240px) !important;
    overflow-y: auto !important;
}

.up-section-title {
    font-size: 11px !important;
    font-weight: 700 !important;
    text-transform: uppercase !important;
    letter-spacing: 0.8px !important;
    color: #64748b !important;
    margin: 0 0 14px 0 !important;
}

.up-details-grid {
    display: grid !important;
    grid-template-columns: 1fr 1fr !important;
    gap: 12px !important;
}

.up-grid-full {
    grid-column: 1 / -1 !important;
}

.up-detail-item {
    background: #ffffff !important;
    padding: 10px 14px !important;
    border-radius: 12px !important;
    border: 1px solid #e2e8f0 !important;
    display: flex !important;
    flex-direction: column !important;
    gap: 3px !important;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02) !important;
}

.up-detail-label {
    font-size: 11px !important;
    font-weight: 600 !important;
    color: #94a3b8 !important;
    text-transform: uppercase !important;
    letter-spacing: 0.3px !important;
}

.up-detail-val {
    font-size: 13.5px !important;
    font-weight: 700 !important;
    color: #0f172a !important;
    word-break: break-word !important;
}

.up-val-muted {
    font-size: 12px !important;
    font-weight: 500 !important;
    color: #64748b !important;
}

/* FOOTER */
.up-modal-footer {
    display: flex !important;
    align-items: center !important;
    justify-content: flex-end !important;
    gap: 10px !important;
    padding: 16px 24px !important;
    background: #ffffff !important;
    border-top: 1px solid #e2e8f0 !important;
}

.up-btn {
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    gap: 6px !important;
    padding: 9px 18px !important;
    border-radius: 10px !important;
    font-size: 13.5px !important;
    font-weight: 600 !important;
    cursor: pointer !important;
    border: none !important;
    transition: all 0.2s ease !important;
}

.up-btn-secondary {
    background: #f1f5f9 !important;
    color: #334155 !important;
    border: 1px solid #cbd5e1 !important;
}

.up-btn-secondary:hover {
    background: #e2e8f0 !important;
    color: #0f172a !important;
}

.up-btn-logout {
    background: #ef4444 !important;
    color: #ffffff !important;
    box-shadow: 0 2px 8px rgba(239, 68, 68, 0.25) !important;
}

.up-btn-logout:hover {
    background: #dc2626 !important;
    transform: translateY(-1px) !important;
    box-shadow: 0 4px 12px rgba(239, 68, 68, 0.35) !important;
}

@media (max-width: 480px) {
    .up-details-grid {
        grid-template-columns: 1fr !important;
    }
}

/* =====================================================
   PORTAL THEME OVERRIDES FOR USER PROFILE MODAL
====================================================== */
/* 1. HR PORTAL THEME (Plum, Purple, Pink & Lilac) */
.up-modal-backdrop.up-theme-hr .up-modal-card,
body:has(.hr-topbar) .up-modal-card,
body:has(.hr-sidebar) .up-modal-card {
    box-shadow: 0 25px 65px -15px rgba(40, 16, 60, 0.4), 0 0 0 1px rgba(66, 19, 79, 0.14) !important;
}

.up-modal-backdrop.up-theme-hr .up-modal-header,
body:has(.hr-topbar) .up-modal-header,
body:has(.hr-sidebar) .up-modal-header {
    background: linear-gradient(135deg, #28103c 0%, #42134f 55%, #701a75 100%) !important;
}

.up-modal-backdrop.up-theme-hr .up-modal-avatar,
body:has(.hr-topbar) .up-modal-avatar,
body:has(.hr-sidebar) .up-modal-avatar {
    background: linear-gradient(135deg, #be185d 0%, #ed4c83 100%) !important;
    box-shadow: 0 8px 24px rgba(190, 24, 93, 0.38) !important;
}

.up-modal-backdrop.up-theme-hr .up-status-dot,
body:has(.hr-topbar) .up-status-dot,
body:has(.hr-sidebar) .up-status-dot {
    background: #10b981 !important;
    box-shadow: 0 0 8px rgba(16, 185, 129, 0.8) !important;
}

.up-modal-backdrop.up-theme-hr .up-role-badge,
body:has(.hr-topbar) .up-role-badge,
body:has(.hr-sidebar) .up-role-badge {
    background: rgba(255, 255, 255, 0.22) !important;
    color: #fce7f3 !important;
    border-color: rgba(255, 255, 255, 0.3) !important;
}

.up-modal-backdrop.up-theme-hr .up-status-badge,
body:has(.hr-topbar) .up-status-badge,
body:has(.hr-sidebar) .up-status-badge {
    background: rgba(16, 185, 129, 0.24) !important;
    color: #6ee7b7 !important;
    border-color: rgba(16, 185, 129, 0.45) !important;
}

.up-modal-backdrop.up-theme-hr .up-modal-email,
body:has(.hr-topbar) .up-modal-email,
body:has(.hr-sidebar) .up-modal-email {
    color: #fbcfe8 !important;
}

.up-modal-backdrop.up-theme-hr .up-modal-body,
body:has(.hr-topbar) .up-modal-body,
body:has(.hr-sidebar) .up-modal-body {
    background: #fffafd !important;
}

.up-modal-backdrop.up-theme-hr .up-section-title,
body:has(.hr-topbar) .up-section-title,
body:has(.hr-sidebar) .up-section-title {
    color: #701a75 !important;
}

.up-modal-backdrop.up-theme-hr .up-detail-label,
body:has(.hr-topbar) .up-detail-label,
body:has(.hr-sidebar) .up-detail-label {
    color: #a21caf !important;
}

.up-modal-backdrop.up-theme-hr .up-detail-item,
body:has(.hr-topbar) .up-detail-item,
body:has(.hr-sidebar) .up-detail-item {
    background: #ffffff !important;
    border-color: #f0e1eb !important;
}

.up-modal-backdrop.up-theme-hr .up-detail-val,
body:has(.hr-topbar) .up-detail-val,
body:has(.hr-sidebar) .up-detail-val {
    color: #2b1748 !important;
}

.up-modal-backdrop.up-theme-hr .up-val-muted,
body:has(.hr-topbar) .up-val-muted,
body:has(.hr-sidebar) .up-val-muted {
    color: #756b85 !important;
}

.up-modal-backdrop.up-theme-hr .up-btn-secondary,
body:has(.hr-topbar) .up-btn-secondary,
body:has(.hr-sidebar) .up-btn-secondary {
    background: #fdf4f9 !important;
    color: #42134f !important;
    border-color: #f0e1eb !important;
}

.up-modal-backdrop.up-theme-hr .up-btn-secondary:hover,
body:has(.hr-topbar) .up-btn-secondary:hover,
body:has(.hr-sidebar) .up-btn-secondary:hover {
    background: #fce7f3 !important;
    color: #2b1748 !important;
}

.up-modal-backdrop.up-theme-hr .up-btn-logout,
body:has(.hr-topbar) .up-btn-logout,
body:has(.hr-sidebar) .up-btn-logout {
    background: linear-gradient(135deg, #be185d 0%, #e11d48 100%) !important;
    box-shadow: 0 4px 14px rgba(190, 24, 93, 0.35) !important;
}

.up-modal-backdrop.up-theme-hr .up-btn-logout:hover,
body:has(.hr-topbar) .up-btn-logout:hover,
body:has(.hr-sidebar) .up-btn-logout:hover {
    background: #9d174d !important;
    box-shadow: 0 6px 18px rgba(190, 24, 93, 0.45) !important;
}

/* 2. MANAGER PORTAL THEME (Forest, Emerald & Teal) */
.up-modal-backdrop.up-theme-manager .up-modal-header,
body:has(.mgr-topbar) .up-modal-header,
body:has(.mgr-sidebar) .up-modal-header {
    background: linear-gradient(135deg, #064e3b 0%, #065f46 55%, #0d9488 100%) !important;
}

.up-modal-backdrop.up-theme-manager .up-modal-avatar,
body:has(.mgr-topbar) .up-modal-avatar,
body:has(.mgr-sidebar) .up-modal-avatar {
    background: linear-gradient(135deg, #059669 0%, #10b981 100%) !important;
    box-shadow: 0 8px 24px rgba(5, 150, 105, 0.35) !important;
}

.up-modal-backdrop.up-theme-manager .up-role-badge,
body:has(.mgr-topbar) .up-role-badge,
body:has(.mgr-sidebar) .up-role-badge {
    background: rgba(255, 255, 255, 0.22) !important;
    color: #ccfbf1 !important;
    border-color: rgba(255, 255, 255, 0.3) !important;
}

.up-modal-backdrop.up-theme-manager .up-modal-email,
body:has(.mgr-topbar) .up-modal-email,
body:has(.mgr-sidebar) .up-modal-email {
    color: #a7f3d0 !important;
}

.up-modal-backdrop.up-theme-manager .up-modal-body,
body:has(.mgr-topbar) .up-modal-body,
body:has(.mgr-sidebar) .up-modal-body {
    background: #f0fdfa !important;
}

.up-modal-backdrop.up-theme-manager .up-section-title,
body:has(.mgr-topbar) .up-section-title,
body:has(.mgr-sidebar) .up-section-title {
    color: #065f46 !important;
}

.up-modal-backdrop.up-theme-manager .up-detail-label,
body:has(.mgr-topbar) .up-detail-label,
body:has(.mgr-sidebar) .up-detail-label {
    color: #0d9488 !important;
}

.up-modal-backdrop.up-theme-manager .up-detail-item,
body:has(.mgr-topbar) .up-detail-item,
body:has(.mgr-sidebar) .up-detail-item {
    border-color: #ccfbf1 !important;
}

/* 3. EMPLOYEE PORTAL THEME (Midnight Indigo & Electric Blue) */
.up-modal-backdrop.up-theme-employee .up-modal-header,
body:has(.emp-topbar) .up-modal-header,
body:has(.employee-sidebar) .up-modal-header {
    background: linear-gradient(135deg, #1e1b4b 0%, #312e81 55%, #4338ca 100%) !important;
}

.up-modal-backdrop.up-theme-employee .up-modal-avatar,
body:has(.emp-topbar) .up-modal-avatar,
body:has(.employee-sidebar) .up-modal-avatar {
    background: linear-gradient(135deg, #4f46e5 0%, #6366f1 100%) !important;
    box-shadow: 0 8px 24px rgba(79, 70, 229, 0.35) !important;
}

.up-modal-backdrop.up-theme-employee .up-role-badge,
body:has(.emp-topbar) .up-role-badge,
body:has(.employee-sidebar) .up-role-badge {
    background: rgba(255, 255, 255, 0.22) !important;
    color: #e0e7ff !important;
    border-color: rgba(255, 255, 255, 0.3) !important;
}

.up-modal-backdrop.up-theme-employee .up-modal-email,
body:has(.emp-topbar) .up-modal-email,
body:has(.employee-sidebar) .up-modal-email {
    color: #c7d2fe !important;
}
</style>

<script>
(function() {
    'use strict';

    // Helper: Compute 2-letter uppercase initials
    function getInitials(name) {
        if (!name || typeof name !== 'string') return 'AD';
        const clean = name.replace(/[^a-zA-Z0-9\s]/g, '').trim();
        if (!clean) return 'AD';
        const parts = clean.split(/\s+/).filter(Boolean);
        if (parts.length === 1) return parts[0].slice(0, 2).toUpperCase();
        return (parts[0][0] + parts[parts.length - 1][0]).toUpperCase();
    }

    // Helper: Format raw role name to human readable title
    function formatRoleTitle(role, designation) {
        if (designation && designation.trim() && designation !== 'Team Member') {
            return designation;
        }
        const r = String(role || '').toUpperCase();
        if (r === 'ADMIN' || r === 'SUPER_ADMIN') return 'Super Admin';
        if (r === 'HR' || r.includes('HUMAN')) return 'HR Manager';
        if (r === 'MANAGER' || r.includes('MANAGER')) return 'Team Manager';
        if (r === 'EMPLOYEE' || r === 'STAFF') return 'Staff Member';
        return r ? r.charAt(0) + r.slice(1).toLowerCase() : 'User';
    }

    // Helper: Escape HTML
    function escapeHtml(str) {
        if (!str) return '';
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    // Helper: Format time ago
    function formatTimeAgo(dateStr) {
        if (!dateStr) return 'Recently';
        try {
            const d = new Date(String(dateStr).replace(/-/g, '/'));
            const now = new Date();
            const sec = Math.floor((now - d) / 1000);
            if (isNaN(sec) || sec < 60) return 'Just now';
            const min = Math.floor(sec / 60);
            if (min < 60) return `${min}m ago`;
            const hrs = Math.floor(min / 60);
            if (hrs < 24) return `${hrs}h ago`;
            return d.toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
        } catch (_) {
            return 'Recently';
        }
    }

    // Helper: Determine Notification Icon
    function getNotifIcon(type) {
        const t = String(type || '').toUpperCase();
        if (t.includes('TASK')) return '📋';
        if (t.includes('LEAVE') || t.includes('APPROVAL')) return '📝';
        if (t.includes('ATTENDANCE') || t.includes('CHECK')) return '⏰';
        if (t.includes('ALERT') || t.includes('URGENT')) return '⚠️';
        if (t.includes('SYSTEM')) return '⚙️';
        return '🔔';
    }

    // Helper: Resolve Base API URL
    function getApiBase() {
        const origin = window.location.origin;
        return origin.includes('localhost') 
            ? `${origin}/employee-qr-system/backend/public` 
            : `${origin}/backend/public`;
    }

    // Helper: Get user object from localStorage
    function getStoredUser() {
        try {
            const raw = localStorage.getItem('auth_user');
            if (raw) return JSON.parse(raw);
        } catch (_) {}
        const username = localStorage.getItem('username');
        if (username) {
            return {
                full_name: username,
                name: username,
                username: username,
                role: 'User'
            };
        }
        return null;
    }

    // Helper: Save user object to localStorage
    function saveStoredUser(user) {
        if (!user) return;
        try {
            localStorage.setItem('auth_user', JSON.stringify(user));
            const name = user.full_name || user.name || user.username;
            if (name) localStorage.setItem('username', name);
        } catch (_) {}
    }

    // Universal Toast Utility
    function showUniversalToast(message, type = 'info') {
        let container = document.getElementById('topbarToastContainer') || document.getElementById('empToastContainer');
        if (!container) {
            container = document.createElement('div');
            container.id = 'topbarToastContainer';
            container.className = 'topbar-toast-container';
            document.body.appendChild(container);
        }

        const toast = document.createElement('div');
        toast.className = `topbar-toast toast-${type}`;
        
        let icon = 'ℹ️';
        if (type === 'success') icon = '✓';
        if (type === 'error') icon = '⚠️';

        toast.innerHTML = `<span>${icon}</span><span>${escapeHtml(message)}</span>`;
        container.appendChild(toast);

        setTimeout(() => {
            toast.style.transition = 'opacity 0.25s ease, transform 0.25s ease';
            toast.style.opacity = '0';
            toast.style.transform = 'translateY(-10px)';
            setTimeout(() => toast.remove(), 260);
        }, 3200);
    }

    // Expose showToast globally if not already provided
    window.showUniversalToast = showUniversalToast;
    if (typeof window.showToast !== 'function') {
        window.showToast = showUniversalToast;
    }

    // Update all Topbar instances on the page
    function syncTopbarProfile(user) {
        if (!user) return;

        const displayName = user.full_name || user.name || user.username || 'Admin';
        const displayRole = formatRoleTitle(user.role, user.designation);
        const initials = getInitials(displayName);

        // 1. Manager Topbar elements
        const mgrName = document.getElementById('mgrProfileName');
        const mgrRole = document.getElementById('mgrProfileRole');
        const mgrAvatar = document.getElementById('mgrAvatarInitials');
        if (mgrName) mgrName.textContent = displayName;
        if (mgrRole) mgrRole.textContent = displayRole;
        if (mgrAvatar) mgrAvatar.textContent = initials;

        // 2. Admin / HR Topbar elements
        const adminName = document.getElementById('adminUsername') || document.getElementById('adminName');
        const adminRole = document.getElementById('adminRole');
        const adminAvatar = document.getElementById('adminAvatar');
        if (adminName) adminName.textContent = displayName;
        if (adminRole) adminRole.textContent = displayRole;
        if (adminAvatar) adminAvatar.textContent = initials;

        // 3. Employee Topbar elements
        const empName = document.getElementById('empTopbarUserName') || document.getElementById('userDisplayName');
        const empRole = document.getElementById('empTopbarUserRole') || document.getElementById('userDisplayRole');
        const empAvatar = document.getElementById('empAvatarFallback') || document.getElementById('empAvatarInitials');
        if (empName) empName.textContent = displayName;
        if (empRole) empRole.textContent = displayRole;
        if (empAvatar) empAvatar.textContent = initials;
    }

    // Helper: Detect Current Portal Theme
    function detectCurrentPortal() {
        const path = window.location.pathname.toLowerCase();
        const hasHrElem = !!document.querySelector('.hr-topbar, .hr-sidebar, [class*="hr-"]');
        const hasMgrElem = !!document.querySelector('.mgr-topbar, .mgr-sidebar, [class*="mgr-"]');
        const hasEmpElem = !!document.querySelector('.emp-topbar, .employee-sidebar, [class*="emp-"]');

        if (path.includes('hr-') || hasHrElem) {
            return 'hr';
        }
        if (path.includes('/manager/') || path.includes('manager-') || hasMgrElem) {
            return 'manager';
        }
        if (path.includes('/employee/') || path.includes('employee-') || hasEmpElem) {
            return 'employee';
        }

        try {
            const raw = localStorage.getItem('auth_user');
            if (raw) {
                const u = JSON.parse(raw);
                const role = String(u.role || '').toUpperCase();
                if (role === 'HR' || role.includes('HUMAN')) return 'hr';
                if (role === 'MANAGER' || role.includes('MANAGER')) return 'manager';
                if (role === 'EMPLOYEE' || role === 'STAFF') return 'employee';
            }
        } catch (_) {}

        return 'admin';
    }

    // Populate and open the Profile Modal
    function openProfileModal(user) {
        const u = user || getStoredUser() || {};
        const portal = detectCurrentPortal();
        const r = String(u.role || '').toUpperCase();
        const isHr = (portal === 'hr' || r === 'HR' || r.includes('HUMAN'));
        const isMgr = (portal === 'manager' || r === 'MANAGER' || r.includes('MANAGER'));
        const isEmp = (portal === 'employee' || r === 'EMPLOYEE' || r === 'STAFF');

        const defaultName = isHr ? 'HR Manager' : (isMgr ? 'Team Manager' : (isEmp ? 'Staff Member' : 'Admin'));
        const displayName = u.full_name || u.name || u.username || defaultName;
        const roleTitle = formatRoleTitle(u.role, u.designation);
        const initials = getInitials(displayName);
        const email = u.email || (u.username ? (u.username + '@company.com') : 'user@company.com');
        const username = u.username || (isHr ? 'hr.user' : 'user');

        let defaultDept = 'Administration';
        if (isHr) defaultDept = 'Human Resources';
        else if (isMgr) defaultDept = 'Management';
        else if (isEmp) defaultDept = 'Operations';
        const dept = u.department || defaultDept;

        let defaultCode = 'ADM-001';
        if (isHr) defaultCode = 'HR-001';
        else if (isMgr) defaultCode = 'MGR-001';
        else if (isEmp) defaultCode = 'EMP-001';
        const empCode = u.employee_code || (u.employee_id ? ('EMP-' + u.employee_id) : defaultCode);

        const phone = u.phone || u.contact_number || '+1 (555) 019-2834';
        const status = (u.status || 'ACTIVE').toUpperCase();

        let lastLogin = 'Recent session active';
        if (u.last_login_at) {
            try {
                const d = new Date(String(u.last_login_at).replace(/-/g, '/'));
                if (!isNaN(d.getTime())) {
                    lastLogin = d.toLocaleDateString('en-US', {
                        day: '2-digit', month: 'short', year: 'numeric',
                        hour: '2-digit', minute: '2-digit', hour12: true
                    });
                }
            } catch (_) {}
        }

        const elAvatar = document.getElementById('upModalAvatar');
        const elName = document.getElementById('upModalName');
        const elRole = document.getElementById('upModalRole');
        const elStatus = document.getElementById('upModalStatus');
        const elEmail = document.getElementById('upModalEmail');
        const elUsername = document.getElementById('upDetailUsername');
        const elSysRole = document.getElementById('upDetailSystemRole');
        const elDesignation = document.getElementById('upDetailDesignation');
        const elDept = document.getElementById('upDetailDepartment');
        const elEmpCode = document.getElementById('upDetailEmpCode');
        const elPhone = document.getElementById('upDetailPhone');
        const elFullEmail = document.getElementById('upDetailEmail');
        const elLastLogin = document.getElementById('upDetailLastLogin');

        if (elAvatar) elAvatar.textContent = initials;
        if (elName) elName.textContent = displayName;
        if (elRole) elRole.textContent = roleTitle;
        if (elStatus) elStatus.textContent = status;
        if (elEmail) elEmail.textContent = email;
        if (elUsername) elUsername.textContent = username;
        if (elSysRole) elSysRole.textContent = (u.role || (isHr ? 'HR' : 'Admin')).toUpperCase();
        if (elDesignation) elDesignation.textContent = roleTitle;
        if (elDept) elDept.textContent = dept;
        if (elEmpCode) elEmpCode.textContent = empCode;
        if (elPhone) elPhone.textContent = phone;
        if (elFullEmail) elFullEmail.textContent = email;
        if (elLastLogin) elLastLogin.textContent = lastLogin;

        const modal = document.getElementById('userProfileModal');
        if (modal) {
            modal.classList.remove('up-theme-hr', 'up-theme-manager', 'up-theme-employee', 'up-theme-admin');
            modal.classList.add(`up-theme-${portal}`);
            modal.style.display = 'flex';
            modal.setAttribute('aria-hidden', 'false');
            setTimeout(() => modal.classList.add('up-show'), 10);
        }
    }

    // Close the Profile Modal
    function closeProfileModal() {
        const modal = document.getElementById('userProfileModal');
        if (!modal) return;
        modal.classList.remove('up-show');
        modal.setAttribute('aria-hidden', 'true');
        setTimeout(() => {
            if (!modal.classList.contains('up-show')) {
                modal.style.display = 'none';
            }
        }, 250);
    }

    // Perform clean logout
    function handleLogout() {
        localStorage.removeItem('auth_token');
        localStorage.removeItem('token');
        localStorage.removeItem('auth_user');
        localStorage.removeItem('username');

        const path = window.location.pathname;
        if (path.includes('/manager/') || path.includes('/employee/')) {
            window.location.href = '../login.php';
        } else {
            window.location.href = 'login.php';
        }
    }

    // Fetch fresh user profile from backend API (/api/me)
    async function fetchFreshProfile() {
        const token = localStorage.getItem('auth_token') || localStorage.getItem('token');
        if (!token) return;

        try {
            const apiBase = getApiBase();
            const res = await fetch(`${apiBase}/api/me`, {
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Accept': 'application/json'
                }
            });

            if (res.ok) {
                const json = await res.json();
                if (json && json.success && json.data && json.data.user) {
                    saveStoredUser(json.data.user);
                    syncTopbarProfile(json.data.user);
                }
            }
        } catch (err) {
            console.debug('Background profile sync info:', err);
        }
    }

    // =========================================================
    // UNIVERSAL NOTIFICATIONS SYSTEM
    // =========================================================
    function updateNotifBadges(unreadCount) {
        const count = Number(unreadCount || 0);
        const badges = document.querySelectorAll(
            '#topbarNotifCount, #empNotifCount, .topbar-notif-badge'
        );
        const dropCounts = document.querySelectorAll(
            '#topbarNotifDropdownCount, #empNotifDropdownCount'
        );

        badges.forEach(badge => {
            if (count > 0) {
                badge.textContent = count > 99 ? '99+' : count;
                badge.style.display = 'flex';
            } else {
                badge.textContent = '0';
                badge.style.display = 'none';
            }
        });

        dropCounts.forEach(el => {
            el.textContent = `${count} new`;
        });
    }

    function renderNotifItems(notifications) {
        const lists = document.querySelectorAll('#topbarNotifList, #empNotifList');
        if (!lists.length) return;

        const notifs = Array.isArray(notifications) ? notifications : [];

        lists.forEach(list => {
            if (notifs.length === 0) {
                list.innerHTML = `
                    <div class="topbar-dropdown-empty">
                        <div style="font-size:24px; margin-bottom:6px;">🔔</div>
                        <div>No notifications</div>
                    </div>`;
                return;
            }

            list.innerHTML = notifs.map(n => {
                const isUnread = !n.is_read || n.is_read == '0';
                const timeAgo = formatTimeAgo(n.created_at);
                const icon = getNotifIcon(n.type);
                return `
                    <div class="topbar-notif-item ${isUnread ? 'unread' : ''}" data-id="${n.id || ''}">
                        <div class="topbar-notif-icon">${icon}</div>
                        <div class="topbar-notif-content">
                            <div class="topbar-notif-title">${escapeHtml(n.title || 'Notification')}</div>
                            <div class="topbar-notif-msg">${escapeHtml(n.message || '')}</div>
                            <div class="topbar-notif-time">${timeAgo}</div>
                        </div>
                    </div>
                `;
            }).join('');
        });
    }

    async function fetchUniversalNotifications() {
        const token = localStorage.getItem('auth_token') || localStorage.getItem('token');
        if (!token) {
            updateNotifBadges(0);
            return;
        }

        try {
            const apiBase = getApiBase();
            const res = await fetch(`${apiBase}/api/notifications`, {
                headers: {
                    'Accept': 'application/json',
                    'Authorization': `Bearer ${token}`
                }
            });

            if (res.ok) {
                const json = await res.json();
                if (json && json.success && json.data) {
                    const unread = json.data.unread_count || 0;
                    const items = json.data.notifications || [];
                    updateNotifBadges(unread);
                    renderNotifItems(items);
                    return;
                }
            }
        } catch (_) {}

        updateNotifBadges(0);
        renderNotifItems([]);
    }

    async function markUniversalNotificationsRead() {
        const token = localStorage.getItem('auth_token') || localStorage.getItem('token');
        try {
            if (token) {
                const apiBase = getApiBase();
                await fetch(`${apiBase}/api/notifications/mark-read`, {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'Authorization': `Bearer ${token}`
                    }
                });
            }
        } catch (_) {}

        updateNotifBadges(0);
        document.querySelectorAll('.topbar-notif-item.unread, .emp-notif-item.unread').forEach(el => {
            el.classList.remove('unread');
        });
        showUniversalToast('All notifications marked as read', 'info');
    }

    // =========================================================
    // UNIVERSAL MESSAGES SYSTEM
    // =========================================================
    function updateMsgBadges(unreadCount) {
        const count = Number(unreadCount || 0);
        const badges = document.querySelectorAll('#topbarMsgCount, .topbar-msg-badge');
        const dropCounts = document.querySelectorAll('#topbarMsgDropdownCount');

        badges.forEach(badge => {
            if (count > 0) {
                badge.textContent = count > 99 ? '99+' : count;
                badge.style.display = 'flex';
            } else {
                badge.textContent = '0';
                badge.style.display = 'none';
            }
        });

        dropCounts.forEach(el => {
            el.textContent = `${count} new`;
        });
    }

    function markUniversalMessagesRead() {
        updateMsgBadges(0);
        showUniversalToast('Messages marked as read', 'info');
    }

    // =========================================================
    // INITIALIZATION & EVENT HANDLERS
    // =========================================================
    function initUniversalTopbar() {
        // 0. Detect and Apply Portal Theme to Profile Modal
        const portal = detectCurrentPortal();
        const modal = document.getElementById('userProfileModal');
        if (modal) {
            modal.classList.remove('up-theme-hr', 'up-theme-manager', 'up-theme-employee', 'up-theme-admin');
            modal.classList.add(`up-theme-${portal}`);
        }

        // 1. Initial Sync User Profile
        const user = getStoredUser();
        if (user) {
            syncTopbarProfile(user);
        } else {
            // Default initial avatar/name based on portal
            const defName = portal === 'hr' ? 'HR Manager' : (portal === 'manager' ? 'Team Manager' : 'Admin');
            const defRole = portal === 'hr' ? 'HR Department' : (portal === 'manager' ? 'Team Manager' : 'Super Admin');
            syncTopbarProfile({ full_name: defName, role: defRole });
        }
        fetchFreshProfile();

        // 2. Initial Sync Notifications & Messages Badges
        updateNotifBadges(0);
        updateMsgBadges(0);
        fetchUniversalNotifications();

        // 3. Setup Notification Toggle
        const notifBtns = document.querySelectorAll(
            '#notifBtn, #topbarNotifBtn, #empNotifBtn, #mgrNotifBtn, [data-action="toggle-notifications"]'
        );
        const notifPanels = document.querySelectorAll('#topbarNotifPanel, #empNotifPanel');

        notifBtns.forEach(btn => {
            if (btn._topbarNotifBound) return;
            btn._topbarNotifBound = true;

            btn.addEventListener('click', function(e) {
                e.stopPropagation();
                // Close any open message panels
                const msgPanels = document.querySelectorAll('#topbarMsgPanel');
                msgPanels.forEach(p => p.style.display = 'none');

                notifPanels.forEach(panel => {
                    const isOpen = panel.style.display === 'block';
                    panel.style.display = isOpen ? 'none' : 'block';
                    if (!isOpen) {
                        fetchUniversalNotifications();
                    }
                });
            });
        });

        // 4. Setup Notification Mark Read Buttons
        const notifMarkBtns = document.querySelectorAll(
            '#topbarNotifMarkAllBtn, #empNotifMarkAllBtn, .topbar-notif-mark-all'
        );
        notifMarkBtns.forEach(btn => {
            if (btn._topbarBound) return;
            btn._topbarBound = true;
            btn.addEventListener('click', function(e) {
                e.stopPropagation();
                markUniversalNotificationsRead();
            });
        });

        // 5. Setup Messages Toggle
        const msgBtns = document.querySelectorAll(
            '#topbarMsgBtn, [title="Messages"], [aria-label="Messages"], [data-action="toggle-messages"]'
        );
        const msgPanels = document.querySelectorAll('#topbarMsgPanel');

        msgBtns.forEach(btn => {
            if (btn._topbarMsgBound) return;
            btn._topbarMsgBound = true;

            btn.addEventListener('click', function(e) {
                e.stopPropagation();
                // Close any open notification panels
                notifPanels.forEach(p => p.style.display = 'none');

                if (msgPanels.length > 0) {
                    msgPanels.forEach(panel => {
                        const isOpen = panel.style.display === 'block';
                        panel.style.display = isOpen ? 'none' : 'block';
                    });
                } else {
                    showUniversalToast('No new direct messages. You are all caught up!', 'info');
                }
            });
        });

        // 6. Setup Messages Mark Read Buttons
        const msgMarkBtns = document.querySelectorAll('#topbarMsgMarkAllBtn');
        msgMarkBtns.forEach(btn => {
            if (btn._topbarBound) return;
            btn._topbarBound = true;
            btn.addEventListener('click', function(e) {
                e.stopPropagation();
                markUniversalMessagesRead();
            });
        });

        // 7. Global Click-Outside to close all dropdowns
        document.addEventListener('click', function(e) {
            notifPanels.forEach(panel => {
                const isBtn = Array.from(notifBtns).some(b => b.contains(e.target));
                if (!panel.contains(e.target) && !isBtn) {
                    panel.style.display = 'none';
                }
            });

            msgPanels.forEach(panel => {
                const isBtn = Array.from(msgBtns).some(b => b.contains(e.target));
                if (!panel.contains(e.target) && !isBtn) {
                    panel.style.display = 'none';
                }
            });
        });

        // 8. Attach click triggers to topbar profile elements
        const triggers = document.querySelectorAll(
            '.topbar-profile, #mgrProfilePill, #adminMenu, #empProfilePill, [data-action="open-profile"]'
        );

        triggers.forEach(el => {
            if (el._topbarProfileBound) return;
            el._topbarProfileBound = true;
            el.style.cursor = 'pointer';
            el.setAttribute('title', 'Click to view profile details');
            el.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                openProfileModal(getStoredUser());
            });
        });

        // 9. Profile Modal Close & Logout triggers
        const closeBtn = document.getElementById('upCloseModalBtn');
        const closeFooterBtn = document.getElementById('upModalCloseFooterBtn');
        const modalBackdrop = document.getElementById('userProfileModal');
        const logoutBtn = document.getElementById('upModalLogoutBtn');

        if (closeBtn && !closeBtn._bound) {
            closeBtn._bound = true;
            closeBtn.addEventListener('click', closeProfileModal);
        }
        if (closeFooterBtn && !closeFooterBtn._bound) {
            closeFooterBtn._bound = true;
            closeFooterBtn.addEventListener('click', closeProfileModal);
        }
        if (logoutBtn && !logoutBtn._bound) {
            logoutBtn._bound = true;
            logoutBtn.addEventListener('click', handleLogout);
        }

        // Global sidebar logout buttons
        const sidebarLogoutBtns = document.querySelectorAll('#logoutButton, #empLogoutButton, .logout-button');
        sidebarLogoutBtns.forEach(btn => {
            if (btn && !btn._universalLogoutBound) {
                btn._universalLogoutBound = true;
                btn.addEventListener('click', handleLogout);
            }
        });

        if (modalBackdrop && !modalBackdrop._bound) {
            modalBackdrop._bound = true;
            modalBackdrop.addEventListener('click', function(e) {
                if (e.target === modalBackdrop) {
                    closeProfileModal();
                }
            });
        }

        // Keyboard Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeProfileModal();
                notifPanels.forEach(p => p.style.display = 'none');
                msgPanels.forEach(p => p.style.display = 'none');
            }
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initUniversalTopbar);
    } else {
        initUniversalTopbar();
    }
})();
</script>

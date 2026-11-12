/* =========================================================
   THEME & LAYOUT HELPER (Runs on every page)
========================================================= */

(function () {
    'use strict';

    function initLayout() {
        // 1. Set Logged-In User Profile Info in Topbar
        let name = '';
        let role = 'Administrator';
        let rawUser = null;

        try {
            rawUser = JSON.parse(localStorage.getItem('auth_user') || 'null');
        } catch (_) {}

        if (rawUser) {
            name = rawUser.full_name || rawUser.name || rawUser.username || '';
            if (rawUser.designation) {
                role = rawUser.designation;
            } else if (rawUser.role) {
                const r = String(rawUser.role).toUpperCase();
                role = r === 'ADMIN' ? 'Super Admin' : (r === 'HR' ? 'HR Manager' : (r === 'MANAGER' ? 'Team Manager' : 'Employee'));
            }
        }

        if (!name) name = localStorage.getItem('username') || 'User';

        function computeInitials(str) {
            if (!str) return 'U';
            const parts = str.trim().split(/\s+/).filter(Boolean);
            if (parts.length === 1) return parts[0].slice(0, 2).toUpperCase();
            return (parts[0][0] + parts[parts.length - 1][0]).toUpperCase();
        }

        const initials = computeInitials(name);

        // Admin / HR elements
        const nameEl = document.getElementById('adminUsername') || document.getElementById('adminName');
        if (nameEl) nameEl.textContent = name;
        const roleEl = document.getElementById('adminRole');
        if (roleEl) roleEl.textContent = role;
        const avatarEl = document.getElementById('adminAvatar');
        if (avatarEl) avatarEl.textContent = initials;

        // Manager elements
        const mgrName = document.getElementById('mgrProfileName');
        if (mgrName) mgrName.textContent = name;
        const mgrRole = document.getElementById('mgrProfileRole');
        if (mgrRole) mgrRole.textContent = role;
        const mgrAvatar = document.getElementById('mgrAvatarInitials');
        if (mgrAvatar) mgrAvatar.textContent = initials;

        // Employee elements
        const empName = document.getElementById('empTopbarUserName') || document.getElementById('userDisplayName');
        if (empName) empName.textContent = name;
        const empRole = document.getElementById('empTopbarUserRole') || document.getElementById('userDisplayRole');
        if (empRole) empRole.textContent = role;
        const empAvatar = document.getElementById('empAvatarFallback') || document.getElementById('empAvatarInitials');
        if (empAvatar) empAvatar.textContent = initials;

        // 2. Mobile Sidebar Toggle
        const mobileBtn = document.getElementById('mobileMenuButton');
        const sidebar = document.querySelector('.sidebar') || 
                        document.querySelector('.hr-sidebar') || 
                        document.querySelector('.mgr-sidebar') || 
                        document.querySelector('.employee-sidebar');
        if (mobileBtn && sidebar) {
            mobileBtn.addEventListener('click', function (e) {
                e.stopPropagation();
                sidebar.classList.toggle('mobile-open');
            });

            document.addEventListener('click', function (e) {
                if (!sidebar.contains(e.target) && !mobileBtn.contains(e.target)) {
                    sidebar.classList.remove('mobile-open');
                }
            });
        }
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initLayout);
    } else {
        initLayout();
    }
})();

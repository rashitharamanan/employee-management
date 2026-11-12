'use strict';
const moduleSidebar = document.querySelector('.hr-sidebar');
document.getElementById('mobileMenuButton')?.addEventListener('click', () => moduleSidebar?.classList.toggle('mobile-open'));
document.getElementById('logoutButton')?.addEventListener('click', () => { localStorage.clear(); location.href = 'login.php'; });
try { const user = JSON.parse(localStorage.getItem('auth_user') || '{}'); const name = user.username || user.full_name || 'HR Manager'; document.getElementById('adminUsername').textContent = name; document.getElementById('adminRole').textContent = user.role || 'HR Department'; document.getElementById('adminAvatar').textContent = name.charAt(0).toUpperCase(); } catch (_) {}

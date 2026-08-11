const dashboard = document.querySelector('.dashboard');
const menuToggle = document.getElementById('menuToggle');
const sidebarOverlay = document.getElementById('sidebarOverlay');

function toggleSidebar() {
  const isOpen = dashboard.classList.toggle('sidebar-open');

  menuToggle.setAttribute('aria-expanded', isOpen);
}

function closeSidebar() {
  dashboard.classList.remove('sidebar-open');
  menuToggle.setAttribute('aria-expanded', 'false');
}

menuToggle.addEventListener('click', toggleSidebar);
sidebarOverlay.addEventListener('click', closeSidebar);
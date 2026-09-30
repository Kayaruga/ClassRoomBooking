async function loadCurrentUser() {
  try {
    const response = await fetch('../api/session.php');
    const data = await response.json();
    if (!data.loggedIn) throw new Error('ไม่ได้เข้าสู่ระบบ');
    document.querySelectorAll('[data-user-name]').forEach((element) => { element.textContent = data.user.displayName; });
    if (data.user.role === 'admin') {
      document.querySelectorAll('.sidebar-nav ul').forEach((menu) => {
        if (!menu.querySelector('[data-admin-link]')) menu.lastElementChild.insertAdjacentHTML('beforebegin', '<li><a href="admin.html" data-admin-link><i class="fa-solid fa-user-shield"></i>อนุมัติการจอง</a></li><li><a href="manage.html" data-admin-manage><i class="fa-solid fa-gears"></i>จัดการระบบ</a></li>');
      });
    }
  } catch {
    window.location.replace('login.html');
  }
}

document.querySelectorAll('[data-logout]').forEach((link) => {
  link.addEventListener('click', async (event) => {
    event.preventDefault();
    await fetch('../api/logout.php', { method: 'POST' });
    window.location.replace('login.html');
  });
});

loadCurrentUser();

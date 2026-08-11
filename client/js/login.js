

const loginBtn = document.getElementById('login');


function loginPage(even) {
  event.preventDefault();
  const username = document.getElementById('name').value;
  const password = document.getElementById('password').value;
  if (username === 'student' && password === '1234') {
    window.location.href = 'dashboard.html'
  } else {
    alert('ชื่อหรือรหัสผ่านไม่ถูกต้อง');
  }
}







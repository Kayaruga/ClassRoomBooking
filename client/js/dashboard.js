const recentBookings = document.getElementById('recentBookings');
const recentMessage = document.getElementById('recentMessage');
const timeSlots = document.getElementById('timeSlots');
const todayRoomList = document.getElementById('todayRoomList');
const availableTodayDate = document.getElementById('availableTodayDate');

const formatDate = (date) => new Intl.DateTimeFormat('th-TH', { dateStyle: 'medium' }).format(new Date(`${date}T00:00:00`));
const today = new Date();
const todayIso = `${today.getFullYear()}-${String(today.getMonth() + 1).padStart(2, '0')}-${String(today.getDate()).padStart(2, '0')}`;
const slots = [
  { start: '08:30', end: '10:30', label: '08:30 – 10:30' },
  { start: '10:30', end: '12:30', label: '10:30 – 12:30' },
  { start: '13:00', end: '15:00', label: '13:00 – 15:00' },
  { start: '15:00', end: '17:00', label: '15:00 – 17:00' },
];
let activeSlot = slots[0];

function roomLink(roomCode) {
  return `rooms.html?date=${todayIso}&startTime=${activeSlot.start}&endTime=${activeSlot.end}&room=${roomCode}`;
}

function renderSlots() {
  timeSlots.innerHTML = slots.map((slot) => `<button type="button" class="time-slot ${slot.start === activeSlot.start ? 'active' : ''}" data-start="${slot.start}"><i class="fa-regular fa-clock"></i> ${slot.label} น.</button>`).join('');
}

async function loadAvailableToday() {
  todayRoomList.innerHTML = '<p class="today-loading"><i class="fa-solid fa-spinner fa-spin"></i> กำลังตรวจสอบห้องว่าง...</p>';
  try {
    const query = new URLSearchParams({ date: todayIso, startTime: activeSlot.start, endTime: activeSlot.end });
    const response = await fetch(`../api/rooms.php?${query}`);
    const data = await response.json();
    if (!response.ok) throw new Error(data.error);
    const rooms = data.rooms.filter((room) => room.available);
    todayRoomList.innerHTML = rooms.length
      ? rooms.slice(0, 4).map((room) => `<article class="today-room"><div class="today-room-icon"><i class="fa-solid fa-door-open"></i></div><div><h4>ห้อง ${room.code}</h4><p>${room.building} ชั้น ${room.floor}</p><span><i class="fa-solid fa-circle-check"></i> ว่างช่วงนี้</span></div><a href="${roomLink(room.code)}">จองช่วงนี้</a></article>`).join('')
      : `<p class="today-loading"><i class="fa-regular fa-face-frown"></i> ไม่มีห้องว่างในช่วงเวลา ${activeSlot.label} น. ลองเลือกช่วงอื่นดู</p>`;
  } catch {
    todayRoomList.innerHTML = '<p class="today-loading"><i class="fa-solid fa-triangle-exclamation"></i> โหลดห้องว่างไม่สำเร็จ</p>';
  }
}

timeSlots.addEventListener('click', (event) => {
  const button = event.target.closest('[data-start]');
  if (!button) return;
  activeSlot = slots.find((slot) => slot.start === button.dataset.start);
  renderSlots();
  loadAvailableToday();
});

async function loadDashboard() {
  try {
    const response = await fetch('../api/dashboard.php');
    const data = await response.json();
    if (!response.ok) throw new Error(data.error);
    document.getElementById('totalRooms').textContent = data.totalRooms;
    document.getElementById('todayBookings').textContent = data.todayBookings;
    document.getElementById('myBookings').textContent = data.myBookings;
    recentMessage.textContent = data.recentBookings.length ? 'รายการล่าสุดที่คุณจองไว้' : 'ยังไม่มีรายการจอง';
    recentBookings.innerHTML = data.recentBookings.length
      ? data.recentBookings.map((booking) => `<div class="recent-item"><i class="fa-solid fa-calendar-check"></i><div><strong>ห้อง ${booking.code}</strong><span>${formatDate(booking.booking_date)} · ${booking.start_time.slice(0, 5)}-${booking.end_time.slice(0, 5)} น.</span></div></div>`).join('')
      : '<p class="dashboard-empty">ยังไม่มีการจอง ลองค้นหาห้องว่างได้เลย</p>';
  } catch {
    recentMessage.textContent = 'เชื่อมต่อฐานข้อมูลไม่สำเร็จ';
    recentBookings.innerHTML = '<p class="dashboard-empty">กรุณาเปิดผ่าน localhost ของ XAMPP</p>';
  }
}

availableTodayDate.textContent = `เลือกช่วงเวลาที่ว่างสำหรับ ${formatDate(todayIso)}`;
renderSlots();
loadDashboard();
loadAvailableToday();

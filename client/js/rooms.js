const roomFilters = document.getElementById('roomFilters');
const roomSearch = document.getElementById('roomSearch');
const buildingFilter = document.getElementById('buildingFilter');
const bookingDate = document.getElementById('bookingDate');
const startTime = document.getElementById('startTime');
const endTime = document.getElementById('endTime');
const capacityFilter = document.getElementById('capacityFilter');
const roomResults = document.getElementById('roomResults');
const resultCount = document.getElementById('resultCount');
const emptyState = document.getElementById('emptyState');
const bookingDialog = document.getElementById('bookingDialog');
const bookingForm = document.getElementById('bookingForm');
let selectedRoomCode = null;
let latestRooms = [];

const roomParams = new URLSearchParams(window.location.search);
const localToday = new Date();
const localTodayIso = `${localToday.getFullYear()}-${String(localToday.getMonth() + 1).padStart(2, '0')}-${String(localToday.getDate()).padStart(2, '0')}`;
bookingDate.value = roomParams.get('date') || localTodayIso;
startTime.value = roomParams.get('startTime') || '08:30';
endTime.value = roomParams.get('endTime') || '10:30';
roomSearch.value = roomParams.get('room') || '';

function renderRooms() {
  const search = roomSearch.value.trim().toLowerCase();
  const rooms = latestRooms.filter((room) => room.code.toLowerCase().includes(search) && (!buildingFilter.value || room.building === buildingFilter.value));
  const available = rooms.filter((room) => room.available).length;
  resultCount.innerHTML = `พบ <strong>${available} ห้องว่าง</strong> จาก ${rooms.length} ห้อง`;
  roomResults.innerHTML = rooms.map((room) => {
    const status = room.available ? '<span class="room-status available">ว่าง</span>' : '<span class="room-status busy">ไม่ว่าง</span>';
    const note = room.conflict ? `<p class="conflict-note"><i class="fa-solid fa-circle-info"></i> ${room.conflict.type === 'class' ? `มีเรียน: ${room.conflict.subject}` : `มีผู้จอง: ${room.conflict.purpose}`}</p>` : '<p class="conflict-note is-free"><i class="fa-solid fa-circle-check"></i> พร้อมจองในช่วงเวลานี้</p>';
    const equipment = room.equipment.map((item) => `<span><i class="fa-solid fa-${item === 'Wi-Fi' ? 'wifi' : item === 'โปรเจกเตอร์' ? 'display' : 'chalkboard'}"></i> ${item}</span>`).join('');
    return `<article class="room-card"><img src="imges/Room.png" alt="ภาพห้อง ${room.code}"><div class="room-card-body"><div class="room-card-head"><div><h4>ห้อง ${room.code}</h4><p>${room.building} ชั้น ${room.floor}</p></div>${status}</div><div class="room-meta"><span><i class="fa-solid fa-users"></i> ${room.capacity} คน</span>${equipment}</div>${note}<button class="booking-button ${room.available ? '' : 'disabled'}" type="button" data-room-code="${room.code}" ${room.available ? '' : 'disabled'}>${room.available ? 'จองห้องนี้' : 'ห้องไม่ว่าง'} <i class="fa-solid fa-arrow-right"></i></button></div></article>`;
  }).join('');
  emptyState.hidden = rooms.length > 0;
  if (!rooms.length) emptyState.innerHTML = '<i class="fa-regular fa-face-frown"></i> ไม่พบห้องที่ตรงกับเงื่อนไข';
}

async function searchRooms(event) {
  event?.preventDefault();
  if (!roomFilters.reportValidity()) return;
  if (startTime.value >= endTime.value) { resultCount.textContent = 'เวลาเริ่มต้องมาก่อนเวลาสิ้นสุด'; roomResults.innerHTML = ''; emptyState.hidden = false; return; }
  resultCount.textContent = 'กำลังตรวจสอบตารางห้อง...';
  try {
    const query = new URLSearchParams({ date: bookingDate.value, startTime: startTime.value, endTime: endTime.value, minCapacity: capacityFilter.value || '0' });
    const response = await fetch(`../api/rooms.php?${query}`); const data = await response.json();
    if (!response.ok) throw new Error(data.error);
    latestRooms = data.rooms; renderRooms();
  } catch (error) { roomResults.innerHTML = ''; resultCount.textContent = error.message || 'เชื่อมต่อระบบไม่สำเร็จ'; emptyState.hidden = false; emptyState.innerHTML = '<i class="fa-solid fa-triangle-exclamation"></i> เปิดผ่าน http://localhost/ClassRoomBooking/client/rooms.html เพื่อใช้ฐานข้อมูล'; }
}

roomFilters.addEventListener('submit', searchRooms);
roomFilters.addEventListener('reset', () => window.setTimeout(() => { bookingDate.value = localTodayIso; startTime.value = '08:30'; endTime.value = '10:30'; capacityFilter.value = ''; latestRooms = []; roomResults.innerHTML = ''; resultCount.textContent = 'เลือกวันและเวลา แล้วกด “ค้นหาห้องว่าง”'; emptyState.hidden = false; emptyState.innerHTML = '<i class="fa-regular fa-calendar"></i> ยังไม่ได้ค้นหาห้อง'; }, 0));
[roomSearch, buildingFilter].forEach((input) => input.addEventListener('input', renderRooms));

roomResults.addEventListener('click', (event) => { const button = event.target.closest('[data-room-code]'); if (!button) return; selectedRoomCode = button.dataset.roomCode; document.getElementById('dialogRoomName').textContent = `ห้อง ${selectedRoomCode}`; document.getElementById('dialogBookingTime').textContent = `${bookingDate.value} เวลา ${startTime.value} - ${endTime.value} น.`; document.getElementById('formMessage').textContent = ''; bookingDialog.showModal(); });
const closeDialog = () => bookingDialog.close();
document.getElementById('closeDialog').addEventListener('click', closeDialog); document.getElementById('cancelBooking').addEventListener('click', closeDialog);
bookingForm.addEventListener('submit', async (event) => { event.preventDefault(); const button = document.getElementById('confirmBooking'); const message = document.getElementById('formMessage'); button.disabled = true; message.textContent = 'กำลังบันทึกการจอง...'; try { const response = await fetch('../api/bookings.php', { method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify({ roomCode: selectedRoomCode, date: bookingDate.value, startTime: startTime.value, endTime: endTime.value, purpose: document.getElementById('purpose').value }) }); const data = await response.json(); if (!response.ok) throw new Error(data.error); message.className = 'form-message success'; message.textContent = `จองห้อง ${selectedRoomCode} สำเร็จแล้ว`; await searchRooms(); window.setTimeout(closeDialog, 1000); } catch (error) { message.className = 'form-message error'; message.textContent = error.message || 'จองห้องไม่สำเร็จ'; } finally { button.disabled = false; } });

if (roomParams.has('date') && roomParams.has('startTime') && roomParams.has('endTime')) searchRooms();

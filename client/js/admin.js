const approvalList = document.getElementById('approvalList');
const adminCount = document.getElementById('adminCount');
const adminTabs = document.getElementById('adminTabs');
let selectedStatus = 'pending';

const statusText = { pending: 'รออนุมัติ', approved: 'อนุมัติแล้ว', rejected: 'ปฏิเสธแล้ว' };
const thaiDate = (date) => new Intl.DateTimeFormat('th-TH', { dateStyle: 'medium' }).format(new Date(`${date}T00:00:00`));

function renderBookings(bookings) {
  adminCount.textContent = `พบ ${bookings.length} รายการ`;
  approvalList.innerHTML = bookings.length ? bookings.map((booking) => `<article class="approval-card"><div class="approval-icon"><i class="fa-solid fa-calendar-check"></i></div><div class="approval-main"><h3>ห้อง ${booking.code} <span class="status-pill status-${booking.status}">${statusText[booking.status]}</span></h3><p>${booking.purpose}</p><div class="approval-meta"><span><i class="fa-solid fa-user"></i> ${booking.booked_by}</span><span><i class="fa-regular fa-calendar"></i> ${thaiDate(booking.booking_date)}</span><span><i class="fa-regular fa-clock"></i> ${booking.start_time.slice(0, 5)}–${booking.end_time.slice(0, 5)} น.</span></div></div>${booking.status === 'pending' ? `<div class="approval-actions"><button class="approve-button" data-id="${booking.id}" data-action="approved"><i class="fa-solid fa-check"></i> อนุมัติ</button><button class="reject-button" data-id="${booking.id}" data-action="rejected"><i class="fa-solid fa-xmark"></i> ปฏิเสธ</button></div>` : '<div></div>'}</article>`).join('') : '<p class="admin-empty"><i class="fa-regular fa-circle-check"></i>ไม่มีรายการในสถานะนี้</p>';
}

async function loadBookings() {
  approvalList.innerHTML = '<p class="admin-empty"><i class="fa-solid fa-spinner fa-spin"></i>กำลังโหลดรายการ...</p>';
  try {
    const response = await fetch(`../api/admin_bookings.php?status=${selectedStatus}`);
    const data = await response.json();
    if (response.status === 403) { window.location.replace('dashboard.html'); return; }
    if (!response.ok) throw new Error(data.error);
    renderBookings(data.bookings);
  } catch (error) { approvalList.innerHTML = `<p class="admin-empty"><i class="fa-solid fa-triangle-exclamation"></i>${error.message || 'โหลดรายการไม่สำเร็จ'}</p>`; }
}

adminTabs.addEventListener('click', (event) => { const button = event.target.closest('[data-status]'); if (!button) return; selectedStatus = button.dataset.status; adminTabs.querySelectorAll('button').forEach((tab) => tab.classList.toggle('active', tab === button)); loadBookings(); });
approvalList.addEventListener('click', async (event) => { const button = event.target.closest('[data-action]'); if (!button) return; const reason = button.dataset.action === 'rejected' ? prompt('ระบุเหตุผลที่ปฏิเสธคำขอนี้') : ''; if (button.dataset.action === 'rejected' && (!reason || reason.trim().length < 3)) { if (reason !== null) alert('กรุณาระบุเหตุผลอย่างน้อย 3 ตัวอักษร'); return; } button.disabled = true; try { const response = await fetch('../api/admin_bookings.php', { method: 'PATCH', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify({ id: Number(button.dataset.id), status: button.dataset.action, reason: reason?.trim() || '' }) }); const data = await response.json(); if (!response.ok) throw new Error(data.error); loadBookings(); } catch (error) { alert(error.message || 'บันทึกไม่สำเร็จ'); button.disabled = false; } });
loadBookings();

# Requirement Traceability

| Requirement | สถานะ | ส่วนที่รองรับ |
|---|---|---|
| FR-01 ค้นหาห้องตามวัน เวลา ความจุ | ผ่าน | `rooms.html`, `api/rooms.php` |
| FR-02 ส่งคำขอพร้อมวัตถุประสงค์ | ผ่าน | `rooms.html`, `api/bookings.php` |
| FR-03 ยกเลิกก่อนเวลาเริ่ม | ผ่าน | `my-bookings.html`, `api/my_bookings.php` |
| FR-04 อนุมัติ/ปฏิเสธพร้อมเหตุผล | ผ่าน | `admin.html`, `api/admin_bookings.php` |
| FR-05 ดูสถานะ/ประวัติ | ผ่าน | `my-bookings.html`, `api/my_bookings.php` |
| FR-06 จัดการข้อมูลห้องและบัญชี | ผ่าน | `manage.html`, `api/admin_rooms.php`, `api/admin_users.php` |
| NFR-01 ใช้งานง่าย | ผ่าน | UI ภาษาไทย, ข้อความแจ้งผล, ปุ่มสถานะ |
| NFR-02 ตอบสนองภายใน 3 วินาที | ต้องทดสอบเครื่องจริง | TC-03, TC-06 ใน `test-cases.md` |
| NFR-03 พร้อมใช้งาน 95% | ต้องวัดระยะยาว | นอกขอบเขตการทดสอบเดโม |
| NFR-04 ยืนยันตัวตน/สิทธิ์ | ผ่าน | Session, `requireLogin()`, `requireAdmin()` |
| NFR-05 รองรับมือถือ | ผ่าน | CSS responsive และ TC-16 |

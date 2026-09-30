# University Classroom Booking System

ระบบจองห้องเรียนออนไลน์สำหรับนักศึกษา/อาจารย์และผู้ดูแลระบบ พัฒนาด้วย HTML, CSS, JavaScript, PHP และ MySQL (XAMPP)

## ฟีเจอร์ที่ทำแล้ว

- สมัครสมาชิก, เข้าสู่ระบบ และออกจากระบบด้วย PHP Session
- ค้นหาห้องว่างจากวัน เวลา และจำนวนผู้ใช้
- ตรวจชนกับตารางเรียนและคำขอจองเดิม
- ส่งคำขอจองพร้อมวัตถุประสงค์ (สถานะเริ่มต้น: รออนุมัติ)
- ดูสถานะและประวัติการจองของตนเอง
- ยกเลิกได้ก่อนถึงเวลาเริ่มใช้งานเท่านั้น
- ผู้ดูแลอนุมัติ/ปฏิเสธคำขอ พร้อมเหตุผลเมื่อปฏิเสธ
- ผู้ดูแลเพิ่ม แก้ไข หรือลบข้อมูลห้อง และจัดการบทบาทผู้ใช้
- หน้า Dashboard แสดงข้อมูลสรุปและห้องว่างวันนี้
- Responsive สำหรับมือถือและเดสก์ท็อป

## การติดตั้ง

1. เปิด Apache และ MySQL จาก XAMPP
2. Import `database/classroom_booking.sql` ใน phpMyAdmin เพื่อเริ่มฐานข้อมูลใหม่
3. หากมีฐานข้อมูลเดิมอยู่แล้ว:
   - Import `database/auth_migration.sql` หากยังไม่มีตาราง `users`
   - Import `database/admin_migration.sql` หากยังไม่มี role, status และ rejection reason
   - ถ้าเคย Import `admin_migration.sql` เวอร์ชันเก่าไปแล้ว ให้ Import เพิ่มเฉพาะ `database/requirements_migration.sql`
4. เปิดผ่าน Apache:

   `http://localhost/ClassRoomBooking/client/login.html`

> ห้ามเปิดผ่าน `file:///...` เพราะระบบ PHP, MySQL และ Session จะไม่ทำงาน

## บทบาทผู้ใช้

- `user` - ค้นหา ส่งคำขอ ดูสถานะ และยกเลิกการจองของตน
- `admin` - อนุมัติ/ปฏิเสธคำขอ จัดการห้อง และจัดการบทบาทผู้ใช้

## ทดสอบระบบ

ดูกรณีทดสอบตาม Requirement ทั้งหมดได้ที่ [docs/test-cases.md](docs/test-cases.md)

## โครงสร้างหลัก

```text
client/     หน้าเว็บและ JavaScript
api/        PHP API และการตรวจสิทธิ์
database/   SQL สำหรับสร้าง/อัปเกรดฐานข้อมูล
docs/       เอกสาร test cases
```

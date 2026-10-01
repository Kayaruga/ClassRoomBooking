-- เพิ่ม/แทนที่ตารางเรียนห้อง IT-402 และ IT-404 จากตารางเรียนที่ได้รับ
-- คาบ 1-4 = 08:30-12:30, คาบ 6-9 = 13:30-17:30
USE classroom_booking;

DELETE class_schedules
FROM class_schedules
JOIN rooms ON rooms.id = class_schedules.room_id
WHERE rooms.code IN ('IT-402', 'IT-404');

INSERT INTO class_schedules (room_id, day_of_week, start_time, end_time, subject, teacher) VALUES
((SELECT id FROM rooms WHERE code='IT-402'),1,'08:30','12:30','รายวิชา INF0235-02',NULL),
((SELECT id FROM rooms WHERE code='IT-402'),1,'13:30','17:30','เทคโนโลยีแพลตฟอร์มคอมพิวเตอร์ (INF0111-01)',NULL),
((SELECT id FROM rooms WHERE code='IT-402'),2,'13:30','17:30','รายวิชา INF0366-01',NULL),
((SELECT id FROM rooms WHERE code='IT-402'),3,'08:30','12:30','รายวิชา INF0211-03 / INF0366-02',NULL),
((SELECT id FROM rooms WHERE code='IT-402'),4,'08:30','12:30','ปฏิบัติการระบบฐานข้อมูล (INF0232-01)',NULL),
((SELECT id FROM rooms WHERE code='IT-402'),4,'13:30','17:30','เทคโนโลยีแพลตฟอร์มคอมพิวเตอร์ (INF0111-02)',NULL),
((SELECT id FROM rooms WHERE code='IT-402'),5,'08:30','12:30','รายวิชา INF0121-01',NULL),
((SELECT id FROM rooms WHERE code='IT-402'),5,'13:30','17:30','เทคโนโลยีแพลตฟอร์มคอมพิวเตอร์ (INF0111-03)',NULL),
((SELECT id FROM rooms WHERE code='IT-404'),1,'08:30','12:30','รายวิชา INF0271-01',NULL),
((SELECT id FROM rooms WHERE code='IT-404'),1,'13:30','17:30','รายวิชา INF0241-01',NULL),
((SELECT id FROM rooms WHERE code='IT-404'),2,'08:30','12:30','รายวิชา INF0254-01',NULL),
((SELECT id FROM rooms WHERE code='IT-404'),3,'08:30','12:30','ตามตารางเรียน IT-404 (ช่วงเช้า)',NULL),
((SELECT id FROM rooms WHERE code='IT-404'),3,'13:30','17:30','รายวิชา INF0142-02 / INF0254-02',NULL),
((SELECT id FROM rooms WHERE code='IT-404'),4,'08:30','12:30','ปัญหาพิเศษเทคโนโลยีสารสนเทศ (INF0492)',NULL),
((SELECT id FROM rooms WHERE code='IT-404'),5,'13:30','17:30','รายวิชา INF0141-02',NULL);

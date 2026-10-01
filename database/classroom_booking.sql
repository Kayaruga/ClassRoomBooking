CREATE DATABASE IF NOT EXISTS classroom_booking CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE classroom_booking;

CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(30) NOT NULL UNIQUE,
  display_name VARCHAR(80) NOT NULL,
  password_hash VARCHAR(255) NOT NULL,
  role ENUM('user', 'admin') NOT NULL DEFAULT 'user',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE rooms (
  id INT AUTO_INCREMENT PRIMARY KEY,
  code VARCHAR(20) NOT NULL UNIQUE,
  building VARCHAR(100) NOT NULL,
  floor INT NOT NULL,
  capacity INT NOT NULL,
  equipment VARCHAR(255) NOT NULL
) ENGINE=InnoDB;

CREATE TABLE class_schedules (
  id INT AUTO_INCREMENT PRIMARY KEY,
  room_id INT NOT NULL,
  day_of_week TINYINT NOT NULL COMMENT '1=จันทร์, 7=อาทิตย์',
  start_time TIME NOT NULL,
  end_time TIME NOT NULL,
  subject VARCHAR(255) NOT NULL,
  teacher VARCHAR(255),
  CONSTRAINT fk_schedule_room FOREIGN KEY (room_id) REFERENCES rooms(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE bookings (
  id INT AUTO_INCREMENT PRIMARY KEY,
  room_id INT NOT NULL,
  booking_date DATE NOT NULL,
  start_time TIME NOT NULL,
  end_time TIME NOT NULL,
  booked_by VARCHAR(100) NOT NULL,
  purpose VARCHAR(255) NOT NULL,
  status ENUM('pending', 'approved', 'rejected') NOT NULL DEFAULT 'pending',
  rejection_reason VARCHAR(255) NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_booking_room FOREIGN KEY (room_id) REFERENCES rooms(id) ON DELETE CASCADE
) ENGINE=InnoDB;

INSERT INTO rooms (code, building, floor, capacity, equipment) VALUES
('IT-401','อาคาร IT',4,30,'โปรเจกเตอร์,Wi-Fi,กระดาน'),('IT-402','อาคาร IT',4,40,'โปรเจกเตอร์,Wi-Fi'),('IT-403','อาคาร IT',4,30,'โปรเจกเตอร์,Wi-Fi,กระดาน'),('IT-404','อาคาร IT',4,50,'โปรเจกเตอร์,Wi-Fi'),('IT-405','อาคาร IT',4,35,'Wi-Fi,กระดาน'),('IT-406','อาคาร IT',4,25,'โปรเจกเตอร์,Wi-Fi,กระดาน');

INSERT INTO class_schedules (room_id, day_of_week, start_time, end_time, subject, teacher) VALUES
((SELECT id FROM rooms WHERE code='IT-401'),1,'08:30','10:30','การพัฒนาโปรแกรมประยุกต์บนเว็บ','ผศ.ดร.ปาณิสรา'),
((SELECT id FROM rooms WHERE code='IT-401'),1,'10:30','12:30','ระบบฐานข้อมูล','รศ.ดร.ไรวรรณ์'),
((SELECT id FROM rooms WHERE code='IT-401'),1,'13:30','15:30','ความซับซ้อนและขั้นตอนวิธี','ผศ.ดร.พงษ์เทพ'),
((SELECT id FROM rooms WHERE code='IT-401'),1,'15:30','17:30','การพัฒนาโปรแกรมประยุกต์','ดร.เลิศสรรค์'),
((SELECT id FROM rooms WHERE code='IT-401'),2,'08:30','10:30','คอมพิวเตอร์กราฟิกส์','ผศ.อรุมา'),
((SELECT id FROM rooms WHERE code='IT-401'),2,'10:30','12:30','การพัฒนาโปรแกรมประยุกต์บนเว็บ','ผศ.ดร.ปาณิสรา'),
((SELECT id FROM rooms WHERE code='IT-401'),3,'08:30','10:30','ระบบปฏิบัติการ','ผศ.อรุมา'),
((SELECT id FROM rooms WHERE code='IT-401'),3,'10:30','12:30','การสื่อสารข้อมูลเครือข่าย','ผศ.ดร.พิมพ์ทร์'),
((SELECT id FROM rooms WHERE code='IT-401'),4,'10:30','12:30','การพัฒนาโปรแกรมประยุกต์','ดร.เลิศสรรค์'),
((SELECT id FROM rooms WHERE code='IT-401'),4,'13:30','15:30','การคิดเชิงอัลกอริทึม','ดร.รติพร'),
((SELECT id FROM rooms WHERE code='IT-401'),4,'15:30','17:30','การเขียนโปรแกรมคอมพิวเตอร์','ดร.รติพร'),
((SELECT id FROM rooms WHERE code='IT-401'),5,'08:30','10:30','วิศวกรรมซอฟต์แวร์เบื้องต้น','ผศ.ดร.ชุติพันธ์'),
((SELECT id FROM rooms WHERE code='IT-401'),5,'13:30','15:30','วิศวกรรมซอฟต์แวร์เบื้องต้น','ผศ.ดร.ชุติพันธ์'),
((SELECT id FROM rooms WHERE code='IT-403'),1,'08:30','10:30','ระบบฐานข้อมูล','รศ.ดร.ไรวรรณ์'),
((SELECT id FROM rooms WHERE code='IT-403'),1,'10:30','12:30','ความซับซ้อนและขั้นตอนวิธี','ผศ.ดร.พงษ์เทพ'),
((SELECT id FROM rooms WHERE code='IT-403'),1,'13:30','15:30','การคิดเชิงอัลกอริทึม','ดร.รติพร'),
((SELECT id FROM rooms WHERE code='IT-403'),1,'15:30','17:30','การเขียนโปรแกรมคอมพิวเตอร์','ดร.รติพร'),
((SELECT id FROM rooms WHERE code='IT-403'),2,'08:30','10:30','ระบบฐานข้อมูล','รศ.ดร.ไรวรรณ์'),
((SELECT id FROM rooms WHERE code='IT-403'),2,'10:30','12:30','คอมพิวเตอร์กราฟิกส์','ผศ.อรุมา'),
((SELECT id FROM rooms WHERE code='IT-403'),2,'13:30','17:30','การพัฒนาโปรแกรมประยุกต์บนเว็บ','ผศ.ดร.ปาณิสรา'),
((SELECT id FROM rooms WHERE code='IT-403'),4,'13:30','15:30','ความซับซ้อนและขั้นตอนวิธี','ผศ.ดร.พงษ์เทพ'),
((SELECT id FROM rooms WHERE code='IT-403'),4,'15:30','17:30','คอมพิวเตอร์กราฟิกส์','ผศ.อรุมา'),
((SELECT id FROM rooms WHERE code='IT-403'),5,'08:30','10:30','การพัฒนาโปรแกรมประยุกต์บนเว็บ','รศ.ดร.ไรวรรณ์'),
((SELECT id FROM rooms WHERE code='IT-403'),5,'10:30','12:30','การพัฒนาโปรแกรมประยุกต์บนเว็บ','รศ.ดร.ไรวรรณ์'),
((SELECT id FROM rooms WHERE code='IT-405'),1,'13:30','17:30','ระบบปฏิบัติการเครือข่าย','ผศ.ดร.ปาณิสรา'),
((SELECT id FROM rooms WHERE code='IT-405'),2,'09:30','12:30','ปัญหาพิเศษ','ดร.เลิศสรรค์'),
((SELECT id FROM rooms WHERE code='IT-405'),3,'09:30','12:30','ปัญหาพิเศษ','ดร.เลิศสรรค์'),
((SELECT id FROM rooms WHERE code='IT-405'),3,'13:30','15:30','เตรียมฝึกประสบการณ์วิชาชีพ','ดร.เลิศสรรค์'),
((SELECT id FROM rooms WHERE code='IT-406'),1,'08:30','15:30','คอมพิวเตอร์กราฟิกส์','ผศ.อรุมา'),
((SELECT id FROM rooms WHERE code='IT-406'),2,'08:30','15:30','คอมพิวเตอร์กราฟิกส์','ผศ.อรุมา'),
((SELECT id FROM rooms WHERE code='IT-406'),4,'13:30','15:30','คอมพิวเตอร์กราฟิกส์','ผศ.อรุมา'),
-- ตารางเรียนจากภาพ: คาบ 1-4 = 08:30-12:30, คาบ 6-9 = 13:30-17:30
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

USE classroom_booking;

ALTER TABLE users
  ADD COLUMN role ENUM('user', 'admin') NOT NULL DEFAULT 'user' AFTER password_hash;

ALTER TABLE bookings
  ADD COLUMN status ENUM('pending', 'approved', 'rejected') NOT NULL DEFAULT 'pending' AFTER purpose;

ALTER TABLE bookings
  ADD COLUMN rejection_reason VARCHAR(255) NULL AFTER status;

INSERT INTO users (username, display_name, password_hash, role)
VALUES ('admin', 'ผู้ดูแลระบบ', '$2y$10$Lbm/luLDrUZ8WxmbtV2XPeUzChW6vB6a95DW8y0qrvkM.8agexJl.', 'admin')
ON DUPLICATE KEY UPDATE display_name = VALUES(display_name), role = 'admin';

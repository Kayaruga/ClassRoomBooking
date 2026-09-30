USE classroom_booking;

ALTER TABLE bookings
  ADD COLUMN rejection_reason VARCHAR(255) NULL AFTER status;

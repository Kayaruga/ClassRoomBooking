<?php
require_once __DIR__ . '/config.php';
$pdo = database();
$user = requireLogin();
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
  $query = $pdo->prepare('SELECT b.id, r.code, r.building, r.floor, b.booking_date, b.start_time, b.end_time, b.booked_by, b.purpose, b.status, b.rejection_reason FROM bookings b JOIN rooms r ON r.id = b.room_id WHERE b.booked_by = ? ORDER BY b.booking_date ASC, b.start_time ASC');
  $query->execute([$user['username']]);
  respond(200, ['bookings' => $query->fetchAll()]);
}
if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
  $data = requestBody();
  $id = (int) ($data['id'] ?? 0);
  if ($id <= 0) respond(400, ['error' => 'ไม่พบรายการที่ต้องการยกเลิก']);
  $find = $pdo->prepare('SELECT booking_date, start_time FROM bookings WHERE id = ? AND booked_by = ?');
  $find->execute([$id, $user['username']]);
  $booking = $find->fetch();
  if (!$booking) respond(404, ['error' => 'ไม่พบรายการจองนี้']);
  $bookingTime = new DateTime($booking['booking_date'] . ' ' . $booking['start_time'], new DateTimeZone('Asia/Bangkok'));
  $now = new DateTime('now', new DateTimeZone('Asia/Bangkok'));
  if ($bookingTime <= $now) respond(409, ['error' => 'ไม่สามารถยกเลิกได้ เนื่องจากเลยเวลาเริ่มใช้งานแล้ว']);
  $cancel = $pdo->prepare('DELETE FROM bookings WHERE id = ? AND booked_by = ?');
  $cancel->execute([$id, $user['username']]);
  respond(200, ['message' => 'ยกเลิกการจองเรียบร้อย']);
}
respond(405, ['error' => 'Method not allowed']);

<?php
require_once __DIR__ . '/config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') respond(405, ['error' => 'Method not allowed']);
$data = requestBody();
$user = requireLogin();
$roomCode = trim($data['roomCode'] ?? '');
$date = $data['date'] ?? '';
$startTime = $data['startTime'] ?? '';
$endTime = $data['endTime'] ?? '';
$purpose = trim($data['purpose'] ?? '');

if (!$roomCode || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $date) || !validTime($startTime) || !validTime($endTime) || $startTime >= $endTime || !$purpose) {
  respond(400, ['error' => 'กรอกข้อมูลการจองให้ครบ และเวลาเริ่มต้องก่อนเวลาสิ้นสุด']);
}

$pdo = database();
$roomStatement = $pdo->prepare('SELECT id FROM rooms WHERE code = ?');
$roomStatement->execute([$roomCode]);
$room = $roomStatement->fetch();
if (!$room) respond(404, ['error' => 'ไม่พบห้องที่เลือก']);

$dayOfWeek = (int) date('N', strtotime($date));
$classConflict = $pdo->prepare('SELECT subject FROM class_schedules WHERE room_id = ? AND day_of_week = ? AND start_time < ? AND end_time > ? LIMIT 1');
$classConflict->execute([$room['id'], $dayOfWeek, $endTime, $startTime]);
if ($class = $classConflict->fetch()) respond(409, ['error' => 'ไม่สามารถจองได้ เนื่องจากมีตารางเรียน: ' . $class['subject']]);

$bookingConflict = $pdo->prepare("SELECT purpose FROM bookings WHERE room_id = ? AND booking_date = ? AND status IN ('pending', 'approved') AND start_time < ? AND end_time > ? LIMIT 1");
$bookingConflict->execute([$room['id'], $date, $endTime, $startTime]);
if ($bookingConflict->fetch()) respond(409, ['error' => 'ไม่สามารถจองได้ เนื่องจากมีผู้จองห้องนี้แล้ว']);

$createBooking = $pdo->prepare("INSERT INTO bookings (room_id, booking_date, start_time, end_time, booked_by, purpose, status) VALUES (?, ?, ?, ?, ?, ?, 'pending')");
$createBooking->execute([$room['id'], $date, $startTime, $endTime, $user['username'], $purpose]);
respond(201, ['booking' => ['id' => (int) $pdo->lastInsertId(), 'roomCode' => $roomCode, 'date' => $date, 'startTime' => $startTime, 'endTime' => $endTime, 'status' => 'pending']]);

<?php
require_once __DIR__ . '/config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') respond(405, ['error' => 'Method not allowed']);

$date = $_GET['date'] ?? '';
$startTime = $_GET['startTime'] ?? '';
$endTime = $_GET['endTime'] ?? '';
$minCapacity = (int) ($_GET['minCapacity'] ?? 0);
if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date) || !validTime($startTime) || !validTime($endTime) || $startTime >= $endTime) {
  respond(400, ['error' => 'กรุณาระบุวันที่และช่วงเวลาที่ถูกต้อง']);
}
if ($minCapacity < 0 || $minCapacity > 500) respond(400, ['error' => 'จำนวนผู้ใช้ไม่ถูกต้อง']);

$dayOfWeek = (int) date('N', strtotime($date));
$pdo = database();
$roomQuery = $pdo->prepare('SELECT id, code, building, floor, capacity, equipment FROM rooms WHERE capacity >= ? ORDER BY code');
$roomQuery->execute([$minCapacity]);
$rooms = $roomQuery->fetchAll();
$classConflict = $pdo->prepare('SELECT subject, start_time, end_time FROM class_schedules WHERE room_id = :roomId AND day_of_week = :day AND start_time < :endTime AND end_time > :startTime LIMIT 1');
$bookingConflict = $pdo->prepare("SELECT booked_by, purpose, start_time, end_time FROM bookings WHERE room_id = :roomId AND booking_date = :bookingDate AND status IN ('pending', 'approved') AND start_time < :endTime AND end_time > :startTime LIMIT 1");

foreach ($rooms as &$room) {
  $classConflict->execute(['roomId' => $room['id'], 'day' => $dayOfWeek, 'startTime' => $startTime, 'endTime' => $endTime]);
  $conflict = $classConflict->fetch();
  $type = 'class';
  if (!$conflict) {
    $bookingConflict->execute(['roomId' => $room['id'], 'bookingDate' => $date, 'startTime' => $startTime, 'endTime' => $endTime]);
    $conflict = $bookingConflict->fetch();
    $type = 'booking';
  }
  $room['id'] = (int) $room['id'];
  $room['floor'] = (int) $room['floor'];
  $room['capacity'] = (int) $room['capacity'];
  $room['equipment'] = explode(',', $room['equipment']);
  $room['available'] = !$conflict;
  $room['conflict'] = $conflict ? array_merge(['type' => $type], $conflict) : null;
}

respond(200, ['rooms' => $rooms]);

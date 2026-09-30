<?php
require_once __DIR__ . '/config.php';

$pdo = database();
requireAdmin();

function roomPayload(array $data): array {
  $code = strtoupper(trim($data['code'] ?? ''));
  $building = trim($data['building'] ?? '');
  $floor = (int) ($data['floor'] ?? 0);
  $capacity = (int) ($data['capacity'] ?? 0);
  $equipment = trim($data['equipment'] ?? '');
  if (!preg_match('/^[A-Z]{2,10}-\d{2,4}$/', $code) || !$building || $floor < 1 || $floor > 100 || $capacity < 1 || $capacity > 500 || !$equipment) {
    respond(400, ['error' => 'กรอกข้อมูลห้องให้ครบและถูกต้อง']);
  }
  return compact('code', 'building', 'floor', 'capacity', 'equipment');
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
  respond(200, ['rooms' => $pdo->query('SELECT id, code, building, floor, capacity, equipment FROM rooms ORDER BY code')->fetchAll()]);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $room = roomPayload(requestBody());
  try {
    $add = $pdo->prepare('INSERT INTO rooms (code, building, floor, capacity, equipment) VALUES (?, ?, ?, ?, ?)');
    $add->execute([$room['code'], $room['building'], $room['floor'], $room['capacity'], $room['equipment']]);
    respond(201, ['message' => 'เพิ่มห้องเรียบร้อย']);
  } catch (PDOException) { respond(409, ['error' => 'รหัสห้องนี้มีอยู่แล้ว']); }
}

if ($_SERVER['REQUEST_METHOD'] === 'PATCH') {
  $data = requestBody(); $id = (int) ($data['id'] ?? 0); if ($id <= 0) respond(400, ['error' => 'ไม่พบห้องที่ต้องการแก้ไข']);
  $room = roomPayload($data);
  try {
    $update = $pdo->prepare('UPDATE rooms SET code = ?, building = ?, floor = ?, capacity = ?, equipment = ? WHERE id = ?');
    $update->execute([$room['code'], $room['building'], $room['floor'], $room['capacity'], $room['equipment'], $id]);
    if ($update->rowCount() === 0) respond(404, ['error' => 'ไม่พบห้องที่ต้องการแก้ไข']);
    respond(200, ['message' => 'แก้ไขข้อมูลห้องเรียบร้อย']);
  } catch (PDOException) { respond(409, ['error' => 'รหัสห้องนี้มีอยู่แล้ว']); }
}

if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
  $id = (int) (requestBody()['id'] ?? 0); if ($id <= 0) respond(400, ['error' => 'ไม่พบห้องที่ต้องการลบ']);
  $used = $pdo->prepare('SELECT (SELECT COUNT(*) FROM bookings WHERE room_id = ?) + (SELECT COUNT(*) FROM class_schedules WHERE room_id = ?)');
  $used->execute([$id, $id]);
  if ((int) $used->fetchColumn() > 0) respond(409, ['error' => 'ลบห้องนี้ไม่ได้ เพราะมีตารางเรียนหรือประวัติการจองอยู่']);
  $delete = $pdo->prepare('DELETE FROM rooms WHERE id = ?'); $delete->execute([$id]);
  if ($delete->rowCount() === 0) respond(404, ['error' => 'ไม่พบห้องที่ต้องการลบ']);
  respond(200, ['message' => 'ลบห้องเรียบร้อย']);
}

respond(405, ['error' => 'Method not allowed']);

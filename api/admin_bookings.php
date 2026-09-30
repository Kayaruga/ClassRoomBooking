<?php
require_once __DIR__ . '/config.php';

$pdo = database();
requireAdmin();

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
  $status = $_GET['status'] ?? 'pending';
  if (!in_array($status, ['pending', 'approved', 'rejected', 'all'], true)) respond(400, ['error' => 'สถานะไม่ถูกต้อง']);
  $sql = 'SELECT b.id, r.code, r.building, b.booking_date, b.start_time, b.end_time, b.booked_by, b.purpose, b.status, b.rejection_reason, b.created_at FROM bookings b JOIN rooms r ON r.id = b.room_id';
  $params = [];
  if ($status !== 'all') { $sql .= ' WHERE b.status = ?'; $params[] = $status; }
  $sql .= ' ORDER BY FIELD(b.status, \'pending\', \'approved\', \'rejected\'), b.booking_date ASC, b.start_time ASC';
  $query = $pdo->prepare($sql);
  $query->execute($params);
  respond(200, ['bookings' => $query->fetchAll()]);
}

if ($_SERVER['REQUEST_METHOD'] === 'PATCH') {
  $data = requestBody();
  $id = (int) ($data['id'] ?? 0);
  $status = $data['status'] ?? '';
  $reason = trim($data['reason'] ?? '');
  if ($id <= 0 || !in_array($status, ['approved', 'rejected'], true)) respond(400, ['error' => 'ข้อมูลการอนุมัติไม่ถูกต้อง']);
  if ($status === 'rejected' && mb_strlen($reason) < 3) respond(400, ['error' => 'กรุณาระบุเหตุผลการปฏิเสธอย่างน้อย 3 ตัวอักษร']);
  $update = $pdo->prepare("UPDATE bookings SET status = ?, rejection_reason = ? WHERE id = ? AND status = 'pending'");
  $update->execute([$status, $status === 'rejected' ? $reason : null, $id]);
  if ($update->rowCount() === 0) respond(409, ['error' => 'รายการนี้ถูกดำเนินการไปแล้ว หรือไม่พบรายการ']);
  respond(200, ['message' => $status === 'approved' ? 'อนุมัติการจองเรียบร้อย' : 'ปฏิเสธการจองเรียบร้อย']);
}

respond(405, ['error' => 'Method not allowed']);

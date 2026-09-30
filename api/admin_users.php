<?php
require_once __DIR__ . '/config.php';

$pdo = database();
$admin = requireAdmin();

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
  respond(200, ['users' => $pdo->query('SELECT id, username, display_name, role, created_at FROM users ORDER BY created_at DESC')->fetchAll()]);
}

if ($_SERVER['REQUEST_METHOD'] === 'PATCH') {
  $data = requestBody(); $id = (int) ($data['id'] ?? 0); $displayName = trim($data['displayName'] ?? ''); $role = $data['role'] ?? '';
  if ($id <= 0 || mb_strlen($displayName) < 2 || mb_strlen($displayName) > 80 || !in_array($role, ['user', 'admin'], true)) respond(400, ['error' => 'ข้อมูลบัญชีไม่ถูกต้อง']);
  if ($id === (int) $admin['id'] && $role !== 'admin') respond(409, ['error' => 'ไม่สามารถลดสิทธิ์ของบัญชีที่กำลังใช้งานได้']);
  $update = $pdo->prepare('UPDATE users SET display_name = ?, role = ? WHERE id = ?'); $update->execute([$displayName, $role, $id]);
  if ($update->rowCount() === 0) respond(404, ['error' => 'ไม่พบผู้ใช้งาน หรือไม่มีข้อมูลเปลี่ยนแปลง']);
  respond(200, ['message' => 'บันทึกบัญชีผู้ใช้เรียบร้อย']);
}

if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
  $id = (int) (requestBody()['id'] ?? 0); if ($id <= 0) respond(400, ['error' => 'ไม่พบบัญชีที่ต้องการลบ']);
  if ($id === (int) $admin['id']) respond(409, ['error' => 'ไม่สามารถลบบัญชีที่กำลังใช้งานได้']);
  $delete = $pdo->prepare('DELETE FROM users WHERE id = ?'); $delete->execute([$id]);
  if ($delete->rowCount() === 0) respond(404, ['error' => 'ไม่พบบัญชีที่ต้องการลบ']);
  respond(200, ['message' => 'ลบบัญชีผู้ใช้เรียบร้อย']);
}

respond(405, ['error' => 'Method not allowed']);

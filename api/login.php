<?php
require_once __DIR__ . '/config.php';
if ($_SERVER['REQUEST_METHOD'] !== 'POST') respond(405, ['error' => 'Method not allowed']);
$data = requestBody();
$username = trim($data['username'] ?? '');
$password = $data['password'] ?? '';
$findUser = database()->prepare('SELECT id, username, display_name, password_hash, role FROM users WHERE username = ?');
$findUser->execute([$username]);
$user = $findUser->fetch();
if (!$user || !password_verify($password, $user['password_hash'])) respond(401, ['error' => 'ชื่อผู้ใช้หรือรหัสผ่านไม่ถูกต้อง']);
startSession();
session_regenerate_id(true);
$_SESSION['user'] = ['id' => (int) $user['id'], 'username' => $user['username'], 'displayName' => $user['display_name'], 'role' => $user['role']];
respond(200, ['user' => $_SESSION['user']]);

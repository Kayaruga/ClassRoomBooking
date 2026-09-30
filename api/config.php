<?php
declare(strict_types=1);

const DB_HOST = '127.0.0.1';
const DB_NAME = 'classroom_booking';
const DB_USER = 'root';
const DB_PASSWORD = '';

function database(): PDO {
  static $connection = null;
  if ($connection === null) {
    $connection = new PDO(
      'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4',
      DB_USER,
      DB_PASSWORD,
      [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC]
    );
  }
  return $connection;
}

function respond(int $status, array $data): never {
  http_response_code($status);
  header('Content-Type: application/json; charset=utf-8');
  echo json_encode($data, JSON_UNESCAPED_UNICODE);
  exit;
}

function requestBody(): array {
  $data = json_decode(file_get_contents('php://input'), true);
  return is_array($data) ? $data : [];
}

function validTime(string $time): bool {
  return (bool) preg_match('/^\d{2}:\d{2}$/', $time);
}

function startSession(): void {
  if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start(['cookie_httponly' => true, 'cookie_samesite' => 'Lax']);
  }
}

function currentUser(): ?array {
  startSession();
  return $_SESSION['user'] ?? null;
}

function requireLogin(): array {
  $user = currentUser();
  if (!$user) respond(401, ['error' => 'กรุณาเข้าสู่ระบบก่อนใช้งาน']);
  return $user;
}

function requireAdmin(): array {
  $user = requireLogin();
  if (($user['role'] ?? 'user') !== 'admin') respond(403, ['error' => 'หน้านี้สำหรับผู้ดูแลระบบเท่านั้น']);
  return $user;
}

<?php
require_once __DIR__ . '/config.php';
if ($_SERVER['REQUEST_METHOD'] !== 'POST') respond(405, ['error' => 'Method not allowed']);
startSession();
$_SESSION = [];
session_destroy();
respond(200, ['message' => 'ออกจากระบบแล้ว']);

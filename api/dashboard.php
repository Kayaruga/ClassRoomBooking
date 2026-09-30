<?php
require_once __DIR__ . '/config.php';
if ($_SERVER['REQUEST_METHOD'] !== 'GET') respond(405, ['error' => 'Method not allowed']);
$pdo = database();
$today = date('Y-m-d');
$user = requireLogin();
$totalRooms = (int) $pdo->query('SELECT COUNT(*) FROM rooms')->fetchColumn();
$todayBookings = $pdo->prepare('SELECT COUNT(*) FROM bookings WHERE booking_date = ?');
$todayBookings->execute([$today]);
$myBookings = $pdo->prepare('SELECT COUNT(*) FROM bookings WHERE booked_by = ?');
$myBookings->execute([$user['username']]);
$recent = $pdo->prepare('SELECT b.id, r.code, b.booking_date, b.start_time, b.end_time, b.purpose FROM bookings b JOIN rooms r ON r.id = b.room_id WHERE b.booked_by = ? ORDER BY b.booking_date ASC, b.start_time ASC LIMIT 4');
$recent->execute([$user['username']]);
respond(200, ['date' => $today, 'totalRooms' => $totalRooms, 'todayBookings' => (int) $todayBookings->fetchColumn(), 'myBookings' => (int) $myBookings->fetchColumn(), 'recentBookings' => $recent->fetchAll()]);

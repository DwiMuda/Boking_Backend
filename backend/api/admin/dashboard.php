<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/auth_middleware.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    json_response(null, 'Method tidak diizinkan', 405);
}

$admin = require_admin($pdo);

$stats = [];

$stmt = $pdo->query("SELECT COUNT(*) as total FROM bookings");
$stats['total_bookings'] = (int)$stmt->fetch()['total'];

$stmt = $pdo->query("SELECT COUNT(*) as total FROM bookings WHERE status = 'pending'");
$stats['pending_bookings'] = (int)$stmt->fetch()['total'];

$stmt = $pdo->query("SELECT COUNT(*) as total FROM bookings WHERE status = 'confirmed'");
$stats['confirmed_bookings'] = (int)$stmt->fetch()['total'];

$stmt = $pdo->query("SELECT COUNT(*) as total FROM bookings WHERE status = 'completed'");
$stats['completed_bookings'] = (int)$stmt->fetch()['total'];

$stmt = $pdo->query("SELECT COUNT(*) as total FROM users WHERE role = 'user'");
$stats['total_users'] = (int)$stmt->fetch()['total'];

$stmt = $pdo->query("SELECT COUNT(*) as total FROM services WHERE is_active = 1");
$stats['total_services'] = (int)$stmt->fetch()['total'];

$stmt = $pdo->query("SELECT COALESCE(SUM(total_harga), 0) as total FROM bookings WHERE status IN ('confirmed', 'completed')");
$stats['total_revenue'] = (float)$stmt->fetch()['total'];

$stmt = $pdo->query("SELECT DATE(tgl_booking) as tgl, COUNT(*) as total FROM bookings WHERE tgl_booking >= CURDATE() AND tgl_booking < CURDATE() + INTERVAL 7 DAY GROUP BY tgl_booking ORDER BY tgl_booking");
$stats['weekly_bookings'] = $stmt->fetchAll();

foreach ($stats['weekly_bookings'] as &$wb) {
    $wb['total'] = (int)$wb['total'];
}

json_response($stats, 'Data dashboard berhasil diambil');

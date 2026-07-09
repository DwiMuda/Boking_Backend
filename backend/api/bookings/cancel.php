<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/auth_middleware.php';
require_once __DIR__ . '/../../includes/mailer.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_response(null, 'Method tidak diizinkan', 405);
}

$user = get_user_by_token($pdo);
$input = get_input();
$id = (int)($input['id'] ?? 0);

if ($id <= 0) {
    json_response(null, 'ID booking tidak valid', 400);
}

$stmt = $pdo->prepare("SELECT * FROM bookings WHERE id = ?");
$stmt->execute([$id]);
$booking = $stmt->fetch();

if (!$booking) {
    json_response(null, 'Booking tidak ditemukan', 404);
}

if ($booking['user_id'] != $user['id']) {
    json_response(null, 'Akses ditolak', 403);
}

if ($booking['status'] !== 'pending') {
    json_response(null, 'Hanya booking dengan status pending yang bisa dibatalkan', 400);
}

$stmt = $pdo->prepare("UPDATE bookings SET status = 'cancelled' WHERE id = ?");
$stmt->execute([$id]);

$stmt = $pdo->prepare("SELECT email, nama FROM users WHERE id = ?");
$stmt->execute([$user['id']]);
$user_data = $stmt->fetch();

email_status_update($user_data['email'], $user_data['nama'], $id, 'cancelled');

json_response(null, 'Booking berhasil dibatalkan');

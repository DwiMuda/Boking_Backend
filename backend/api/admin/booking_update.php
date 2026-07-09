<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/auth_middleware.php';
require_once __DIR__ . '/../../includes/mailer.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_response(null, 'Method tidak diizinkan', 405);
}

$admin = require_admin($pdo);
$input = get_input();

$id = (int)($input['id'] ?? 0);
$status = trim($input['status'] ?? '');

if ($id <= 0 || empty($status)) {
    json_response(null, 'ID booking dan status harus diisi', 400);
}

$valid_status = ['pending', 'confirmed', 'completed', 'cancelled'];
if (!in_array($status, $valid_status)) {
    json_response(null, 'Status tidak valid. Pilihan: ' . implode(', ', $valid_status), 400);
}

$stmt = $pdo->prepare("SELECT b.id, b.user_id, u.email, u.nama FROM bookings b JOIN users u ON b.user_id = u.id WHERE b.id = ?");
$stmt->execute([$id]);
$booking = $stmt->fetch();

if (!$booking) {
    json_response(null, 'Booking tidak ditemukan', 404);
}

$stmt = $pdo->prepare("UPDATE bookings SET status = ? WHERE id = ?");
$stmt->execute([$status, $id]);

email_status_update($booking['email'], $booking['nama'], $id, $status);

json_response(null, 'Status booking berhasil diupdate');

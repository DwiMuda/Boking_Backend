<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/auth_middleware.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    json_response(null, 'Method tidak diizinkan', 405);
}

$user = get_user_by_token($pdo);
$id = (int)($_GET['id'] ?? 0);

if ($id <= 0) {
    json_response(null, 'ID booking tidak valid', 400);
}

$stmt = $pdo->prepare("SELECT b.*, s.nama as service_nama, s.deskripsi as service_deskripsi, s.durasi_menit, u.nama as user_nama, u.email as user_email, u.no_telp as user_no_telp FROM bookings b JOIN services s ON b.service_id = s.id JOIN users u ON b.user_id = u.id WHERE b.id = ?");
$stmt->execute([$id]);
$booking = $stmt->fetch();

if (!$booking) {
    json_response(null, 'Booking tidak ditemukan', 404);
}

if ($booking['user_id'] != $user['id'] && $user['role'] !== 'admin') {
    json_response(null, 'Akses ditolak', 403);
}

$booking['id'] = (int)$booking['id'];
$booking['user_id'] = (int)$booking['user_id'];
$booking['service_id'] = (int)$booking['service_id'];
$booking['total_harga'] = (float)$booking['total_harga'];
$booking['durasi_menit'] = (int)$booking['durasi_menit'];

json_response($booking, 'Detail booking berhasil diambil');

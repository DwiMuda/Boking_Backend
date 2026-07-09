<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/auth_middleware.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    json_response(null, 'Method tidak diizinkan', 405);
}

$user = get_user_by_token($pdo);

$stmt = $pdo->prepare("SELECT b.*, s.nama as service_nama, s.durasi_menit FROM bookings b JOIN services s ON b.service_id = s.id WHERE b.user_id = ? ORDER BY b.created_at DESC");
$stmt->execute([$user['id']]);
$bookings = $stmt->fetchAll();

foreach ($bookings as &$b) {
    $b['id'] = (int)$b['id'];
    $b['user_id'] = (int)$b['user_id'];
    $b['service_id'] = (int)$b['service_id'];
    $b['total_harga'] = (float)$b['total_harga'];
    $b['durasi_menit'] = (int)$b['durasi_menit'];
}

json_response($bookings, 'Daftar booking berhasil diambil');

<?php
require_once __DIR__ . '/../../config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    json_response(null, 'Method tidak diizinkan', 405);
}

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) {
    json_response(null, 'ID layanan tidak valid', 400);
}

$stmt = $pdo->prepare("SELECT * FROM services WHERE id = ?");
$stmt->execute([$id]);
$service = $stmt->fetch();

if (!$service) {
    json_response(null, 'Layanan tidak ditemukan', 404);
}

$service['id'] = (int)$service['id'];
$service['harga'] = (float)$service['harga'];
$service['durasi_menit'] = (int)$service['durasi_menit'];
$service['is_active'] = (bool)$service['is_active'];

json_response($service, 'Detail layanan berhasil diambil');

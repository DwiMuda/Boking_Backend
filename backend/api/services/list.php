<?php
require_once __DIR__ . '/../../config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    json_response(null, 'Method tidak diizinkan', 405);
}

$stmt = $pdo->query("SELECT * FROM services WHERE is_active = 1 ORDER BY nama ASC");
$services = $stmt->fetchAll();

foreach ($services as &$s) {
    $s['id'] = (int)$s['id'];
    $s['harga'] = (float)$s['harga'];
    $s['durasi_menit'] = (int)$s['durasi_menit'];
    $s['is_active'] = (bool)$s['is_active'];
}

json_response($services, 'Daftar layanan berhasil diambil');

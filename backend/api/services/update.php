<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/auth_middleware.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_response(null, 'Method tidak diizinkan', 405);
}

$admin = require_admin($pdo);
$input = get_input();

$id = (int)($input['id'] ?? 0);
if ($id <= 0) {
    json_response(null, 'ID layanan tidak valid', 400);
}

$stmt = $pdo->prepare("SELECT id FROM services WHERE id = ?");
$stmt->execute([$id]);
if (!$stmt->fetch()) {
    json_response(null, 'Layanan tidak ditemukan', 404);
}

$fields = [];
$params = [];

if (isset($input['nama'])) {
    $fields[] = 'nama = ?';
    $params[] = trim($input['nama']);
}
if (isset($input['deskripsi'])) {
    $fields[] = 'deskripsi = ?';
    $params[] = trim($input['deskripsi']);
}
if (isset($input['harga'])) {
    $fields[] = 'harga = ?';
    $params[] = (float)$input['harga'];
}
if (isset($input['durasi_menit'])) {
    $fields[] = 'durasi_menit = ?';
    $params[] = (int)$input['durasi_menit'];
}
if (isset($input['gambar'])) {
    $fields[] = 'gambar = ?';
    $params[] = trim($input['gambar']);
}
if (isset($input['is_active'])) {
    $fields[] = 'is_active = ?';
    $params[] = (int)$input['is_active'];
}

if (empty($fields)) {
    json_response(null, 'Tidak ada data yang diupdate', 400);
}

$params[] = $id;
$sql = "UPDATE services SET " . implode(', ', $fields) . " WHERE id = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);

$stmt = $pdo->prepare("SELECT * FROM services WHERE id = ?");
$stmt->execute([$id]);
$service = $stmt->fetch();

$service['id'] = (int)$service['id'];
$service['harga'] = (float)$service['harga'];
$service['durasi_menit'] = (int)$service['durasi_menit'];
$service['is_active'] = (bool)$service['is_active'];

json_response($service, 'Layanan berhasil diupdate');

<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/auth_middleware.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_response(null, 'Method tidak diizinkan', 405);
}

$admin = require_admin($pdo);
$input = get_input();

$nama = trim($input['nama'] ?? '');
$deskripsi = trim($input['deskripsi'] ?? '');
$harga = (float)($input['harga'] ?? 0);
$durasi_menit = (int)($input['durasi_menit'] ?? 60);
$gambar = trim($input['gambar'] ?? '');

if (empty($nama) || $harga <= 0) {
    json_response(null, 'Nama dan harga harus diisi', 400);
}

$stmt = $pdo->prepare("INSERT INTO services (nama, deskripsi, harga, durasi_menit, gambar) VALUES (?, ?, ?, ?, ?)");
$stmt->execute([$nama, $deskripsi, $harga, $durasi_menit, $gambar]);

$service = [
    'id' => (int)$pdo->lastInsertId(),
    'nama' => $nama,
    'deskripsi' => $deskripsi,
    'harga' => $harga,
    'durasi_menit' => $durasi_menit,
    'gambar' => $gambar,
    'is_active' => true
];

json_response($service, 'Layanan berhasil ditambahkan', 201);

<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/auth_middleware.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    json_response(null, 'Method tidak diizinkan', 405);
}

$admin = require_admin($pdo);

$status_filter = $_GET['status'] ?? '';
$search = $_GET['search'] ?? '';

$sql = "SELECT b.*, s.nama as service_nama, s.durasi_menit, u.nama as user_nama, u.email as user_email, u.no_telp as user_no_telp FROM bookings b JOIN services s ON b.service_id = s.id JOIN users u ON b.user_id = u.id WHERE 1=1";
$params = [];

if (!empty($status_filter)) {
    $sql .= " AND b.status = ?";
    $params[] = $status_filter;
}

if (!empty($search)) {
    $sql .= " AND (u.nama LIKE ? OR s.nama LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

$sql .= " ORDER BY b.created_at DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$bookings = $stmt->fetchAll();

foreach ($bookings as &$b) {
    $b['id'] = (int)$b['id'];
    $b['user_id'] = (int)$b['user_id'];
    $b['service_id'] = (int)$b['service_id'];
    $b['total_harga'] = (float)$b['total_harga'];
    $b['durasi_menit'] = (int)$b['durasi_menit'];
}

json_response($bookings, 'Daftar booking berhasil diambil');

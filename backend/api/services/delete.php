<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/auth_middleware.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_response(null, 'Method tidak diizinkan', 405);
}

$admin = require_admin($pdo);
$input = get_input();

$id = (int)($input['id'] ?? ($_GET['id'] ?? 0));
if ($id <= 0) {
    json_response(null, 'ID layanan tidak valid', 400);
}

$stmt = $pdo->prepare("SELECT id FROM services WHERE id = ?");
$stmt->execute([$id]);
if (!$stmt->fetch()) {
    json_response(null, 'Layanan tidak ditemukan', 404);
}

$stmt = $pdo->prepare("DELETE FROM services WHERE id = ?");
$stmt->execute([$id]);

json_response(null, 'Layanan berhasil dihapus');

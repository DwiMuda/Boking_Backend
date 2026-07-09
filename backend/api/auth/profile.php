<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/auth_middleware.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    json_response(null, 'Method tidak diizinkan', 405);
}

$user = get_user_by_token($pdo);
json_response($user, 'Data profil berhasil diambil');

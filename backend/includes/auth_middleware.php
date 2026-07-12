<?php
function get_user_by_token($pdo) {
    $headers = getallheaders();
    $token = null;

    if (isset($headers['Authorization'])) {
        $parts = explode(' ', $headers['Authorization']);
        if (count($parts) === 2) {
            $token = $parts[1];
        }
    }

    if (!$token) {
        json_response(null, 'Token tidak ditemukan. Silakan login.', 401);
    }

    $stmt = $pdo->prepare("SELECT id, nama, email, no_telp, role FROM users WHERE token = ?");
    $stmt->execute([$token]);
    $user = $stmt->fetch();

    if (!$user) {
        json_response(null, 'Token tidak valid. Silakan login ulang.', 401);
    }

    return $user;
}

function require_admin($pdo) {
    $user = get_user_by_token($pdo);
    if ($user['role'] !== 'admin') {
        json_response(null, 'Akses ditolak. Hanya admin.', 403);
    }
    return $user;
}

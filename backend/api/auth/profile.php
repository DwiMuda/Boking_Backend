<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/auth_middleware.php';

try {
    require_method('GET');
    $user = get_user_by_token($pdo);
    json_response($user, 'Data profil berhasil diambil');
} catch (AppException $e) {
    json_response(null, $e->getMessage(), $e->getCode() ?: 400);
}

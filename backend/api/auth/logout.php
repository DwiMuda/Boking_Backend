<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/auth_middleware.php';
require_once __DIR__ . '/../../services/AuthService.php';

try {
    require_method('POST');
    $user = get_user_by_token($pdo);
    AuthService::logout($pdo, $user['id']);
    json_response(null, 'Logout berhasil');
} catch (AppException $e) {
    json_response(null, $e->getMessage(), $e->getCode() ?: 400);
}

<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/auth_middleware.php';
require_once __DIR__ . '/../../services/AuthService.php';

try {
    require_method('POST');
    $user = get_user_by_token($pdo);
    $input = get_input();
    $result = AuthService::updateProfile($pdo, $user['id'], $input);
    json_response($result, 'Profil berhasil diupdate');
} catch (AppException $e) {
    json_response(null, $e->getMessage(), $e->getCode() ?: 400);
}

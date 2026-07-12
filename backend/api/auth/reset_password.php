<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../services/AuthService.php';

try {
    require_method('POST');
    $input = get_input();
    AuthService::resetPassword($pdo, $input['token'] ?? '', $input['password'] ?? '');
    json_response(null, 'Password berhasil diubah. Silakan login.');
} catch (AppException $e) {
    json_response(null, $e->getMessage(), $e->getCode() ?: 400);
}

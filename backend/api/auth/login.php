<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/auth_middleware.php';
require_once __DIR__ . '/../../services/AuthService.php';

try {
    require_method('POST');
    $input = get_input();
    $result = AuthService::login($pdo, $input['email'] ?? '', $input['password'] ?? '');
    json_response($result, 'Login berhasil');
} catch (AppException $e) {
    json_response(null, $e->getMessage(), $e->getCode() ?: 400);
}

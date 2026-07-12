<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../services/AuthService.php';

try {
    require_method('POST');
    $input = get_input();
    $result = AuthService::verifyEmail($pdo, $input['email'] ?? '', $input['otp'] ?? '');
    json_response($result, 'Email berhasil diverifikasi');
} catch (AppException $e) {
    json_response(null, $e->getMessage(), $e->getCode() ?: 400);
}

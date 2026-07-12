<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../services/AuthService.php';

try {
    require_method('POST');
    $input = get_input();
    AuthService::resendOtp($pdo, $input['email'] ?? '');
    json_response(null, 'Kode verifikasi dikirim ulang ke email Anda');
} catch (AppException $e) {
    json_response(null, $e->getMessage(), $e->getCode() ?: 400);
}

<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../services/AuthService.php';

try {
    require_method('POST');
    $input = get_input();
    $result = AuthService::register($pdo, $input['nama'] ?? '', $input['email'] ?? '', $input['password'] ?? '', $input['no_telp'] ?? '');
    json_response($result, 'Kode verifikasi dikirim ke email Anda', 201);
} catch (AppException $e) {
    json_response(null, $e->getMessage(), $e->getCode() ?: 400);
}

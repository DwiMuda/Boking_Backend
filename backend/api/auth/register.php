<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../services/AuthService.php';
require_once __DIR__ . '/../../includes/mailer.php';

try {
    require_method('POST');
    $input = get_input();
    $result = AuthService::register($pdo, $input['nama'] ?? '', $input['email'] ?? '', $input['password'] ?? '', $input['no_telp'] ?? '');
    $email = $input['email'] ?? '';

    $stmt = $pdo->prepare("SELECT otp_code FROM users WHERE email = ? AND email_verified_at IS NULL");
    $stmt->execute([$email]);
    $row = $stmt->fetch();
    if ($row) {
        $result['otp'] = $row['otp_code'];
    }

    json_response($result, 'Kode verifikasi dikirim ke email Anda', 201);
} catch (AppException $e) {
    json_response(null, $e->getMessage(), $e->getCode() ?: 400);
}

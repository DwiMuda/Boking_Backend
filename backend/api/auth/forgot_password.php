<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/mailer.php';
require_once __DIR__ . '/../../services/AuthService.php';

try {
    require_method('POST');
    $input = get_input();
    AuthService::forgotPassword($pdo, $input['email'] ?? '');
    json_response(null, 'Jika email terdaftar, link reset password akan dikirim');
} catch (AppException $e) {
    json_response(null, $e->getMessage(), $e->getCode() ?: 400);
}

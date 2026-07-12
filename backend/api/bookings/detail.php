<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/auth_middleware.php';
require_once __DIR__ . '/../../services/BookingService.php';

try {
    require_method('GET');
    $user = get_user_by_token($pdo);
    $result = BookingService::getDetail($pdo, $user, $_GET['id'] ?? 0);
    json_response($result, 'Detail booking berhasil diambil');
} catch (AppException $e) {
    json_response(null, $e->getMessage(), $e->getCode() ?: 400);
}

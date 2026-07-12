<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/auth_middleware.php';
require_once __DIR__ . '/../../services/BookingService.php';

try {
    require_method('POST');
    $user = get_user_by_token($pdo);
    $input = get_input();
    BookingService::cancel($pdo, $user, $input['id'] ?? 0);
    json_response(null, 'Booking berhasil dibatalkan');
} catch (AppException $e) {
    json_response(null, $e->getMessage(), $e->getCode() ?: 400);
}

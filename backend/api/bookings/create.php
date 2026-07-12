<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/auth_middleware.php';
require_once __DIR__ . '/../../services/BookingService.php';

try {
    require_method('POST');
    $user = get_user_by_token($pdo);
    $input = get_input();
    $result = BookingService::create($pdo, $user, $input);
    json_response($result, 'Booking berhasil dibuat', 201);
} catch (AppException $e) {
    json_response(null, $e->getMessage(), $e->getCode() ?: 400);
}

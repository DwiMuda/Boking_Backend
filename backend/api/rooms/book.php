<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/auth_middleware.php';
require_once __DIR__ . '/../../services/RoomService.php';

try {
    require_method('POST');
    $user = get_user_by_token($pdo);
    $input = get_input();
    $result = RoomService::bookRoom($pdo, $user, $input);
    json_response($result, 'Booking kamar berhasil dibuat', 201);
} catch (AppException $e) {
    json_response(null, $e->getMessage(), $e->getCode() ?: 400);
}

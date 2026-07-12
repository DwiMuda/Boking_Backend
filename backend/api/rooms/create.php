<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/auth_middleware.php';
require_once __DIR__ . '/../../services/RoomService.php';

try {
    require_method('POST');
    require_admin($pdo);
    $input = get_input();
    $result = RoomService::createRoomType($pdo, $input);
    json_response($result, 'Tipe kamar berhasil ditambahkan', 201);
} catch (AppException $e) {
    json_response(null, $e->getMessage(), $e->getCode() ?: 400);
}

<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/auth_middleware.php';
require_once __DIR__ . '/../../services/RoomService.php';

try {
    require_method('POST');
    require_admin($pdo);
    $input = get_input();
    RoomService::deleteRoomType($pdo, $input['id'] ?? 0);
    json_response(null, 'Tipe kamar berhasil dihapus');
} catch (AppException $e) {
    json_response(null, $e->getMessage(), $e->getCode() ?: 400);
}

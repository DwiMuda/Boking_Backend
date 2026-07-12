<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../services/RoomService.php';

try {
    require_method('GET');
    $result = RoomService::getRoomTypeById($pdo, $_GET['id'] ?? 0);
    json_response($result, 'Detail tipe kamar berhasil diambil');
} catch (AppException $e) {
    json_response(null, $e->getMessage(), $e->getCode() ?: 400);
}

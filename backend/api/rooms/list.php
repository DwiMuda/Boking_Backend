<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../services/RoomService.php';

try {
    require_method('GET');
    $result = RoomService::getRoomTypes($pdo);
    json_response($result, 'Daftar tipe kamar berhasil diambil');
} catch (AppException $e) {
    json_response(null, $e->getMessage(), $e->getCode() ?: 400);
}

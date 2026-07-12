<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../services/RoomService.php';

try {
    require_method('GET');
    $result = RoomService::getAvailableRooms(
        $pdo,
        $_GET['check_in'] ?? '',
        $_GET['check_out'] ?? '',
        (int)($_GET['kapasitas'] ?? 0)
    );
    json_response($result, 'Ketersediaan kamar berhasil diambil');
} catch (AppException $e) {
    json_response(null, $e->getMessage(), $e->getCode() ?: 400);
}

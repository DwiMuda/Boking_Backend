<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../services/BookingService.php';

try {
    require_method('GET');
    $result = BookingService::checkAvailability($pdo, $_GET['service_id'] ?? 0, $_GET['tgl'] ?? '');
    json_response($result, 'Ketersediaan berhasil dicek');
} catch (AppException $e) {
    json_response(null, $e->getMessage(), $e->getCode() ?: 400);
}

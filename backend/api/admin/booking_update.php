<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/auth_middleware.php';
require_once __DIR__ . '/../../services/BookingService.php';

try {
    require_method('POST');
    require_admin($pdo);
    $input = get_input();
    BookingService::updateStatus($pdo, $input['id'] ?? 0, $input['status'] ?? '');
    json_response(null, 'Status booking berhasil diupdate');
} catch (AppException $e) {
    json_response(null, $e->getMessage(), $e->getCode() ?: 400);
}

<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/auth_middleware.php';
require_once __DIR__ . '/../../services/ServiceService.php';

try {
    require_method('POST');
    require_admin($pdo);
    $input = get_input();
    $result = ServiceService::update($pdo, $input['id'] ?? 0, $input);
    json_response($result, 'Layanan berhasil diupdate');
} catch (AppException $e) {
    json_response(null, $e->getMessage(), $e->getCode() ?: 400);
}

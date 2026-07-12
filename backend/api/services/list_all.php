<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/auth_middleware.php';
require_once __DIR__ . '/../../services/ServiceService.php';

try {
    require_method('GET');
    require_admin($pdo);
    $result = ServiceService::getAllAdmin($pdo);
    json_response($result, 'Semua layanan berhasil diambil');
} catch (AppException $e) {
    json_response(null, $e->getMessage(), $e->getCode() ?: 400);
}

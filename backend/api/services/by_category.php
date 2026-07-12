<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../services/ServiceService.php';

try {
    require_method('GET');
    $result = ServiceService::getByCategory($pdo);
    json_response($result, 'Layanan per kategori berhasil diambil');
} catch (AppException $e) {
    json_response(null, $e->getMessage(), $e->getCode() ?: 400);
}

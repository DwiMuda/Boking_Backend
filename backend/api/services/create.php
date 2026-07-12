<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/auth_middleware.php';
require_once __DIR__ . '/../../services/ServiceService.php';

try {
    require_method('POST');
    require_admin($pdo);
    $input = get_input();
    $result = ServiceService::create($pdo, $input);
    json_response($result, 'Layanan berhasil ditambahkan', 201);
} catch (AppException $e) {
    json_response(null, $e->getMessage(), $e->getCode() ?: 400);
}

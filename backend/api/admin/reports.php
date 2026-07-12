<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/auth_middleware.php';
require_once __DIR__ . '/../../services/ReportService.php';

try {
    require_method('GET');
    require_admin($pdo);
    $from = $_GET['from'] ?? '';
    $to = $_GET['to'] ?? '';
    $result = ReportService::getReport($pdo, $from, $to);
    json_response($result, 'Laporan berhasil diambil');
} catch (AppException $e) {
    json_response(null, $e->getMessage(), $e->getCode() ?: 400);
}

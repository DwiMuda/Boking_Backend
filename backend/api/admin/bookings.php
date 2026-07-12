<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/auth_middleware.php';
require_once __DIR__ . '/../../services/BookingService.php';

try {
    require_method('GET');
    require_admin($pdo);
    $page = (int)($_GET['page'] ?? 1);
    $filters = [
        'status' => $_GET['status'] ?? '',
        'search' => $_GET['search'] ?? '',
        'tipe' => $_GET['tipe'] ?? ''
    ];
    $result = BookingService::getAllBookings($pdo, $filters, $page);
    $page = $result['page'];
    $perPage = $result['per_page'];
    json_response([
        'items' => $result['items'],
        'pagination' => [
            'total' => (int)$result['total'],
            'page' => $page,
            'per_page' => $perPage,
            'total_pages' => max(1, (int)ceil($result['total'] / max($perPage, 1)))
        ]
    ], 'Daftar booking berhasil diambil');
} catch (AppException $e) {
    json_response(null, $e->getMessage(), $e->getCode() ?: 400);
}

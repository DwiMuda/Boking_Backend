<?php
class AppException extends Exception {}

function json_response($data, $message = '', $status_code = 200) {
    http_response_code($status_code);
    echo json_encode([
        'success' => $status_code >= 200 && $status_code < 300,
        'data' => $data,
        'message' => $message
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

function get_input() {
    $input = json_decode(file_get_contents('php://input'), true);
    return $input !== null ? $input : $_POST;
}

function require_method($method) {
    if ($_SERVER['REQUEST_METHOD'] !== strtoupper($method)) {
        json_response(null, 'Method tidak diizinkan', 405);
    }
}

function find_or_fail($pdo, $table, $id, $columns = '*', $extra_cond = '') {
    $allowed = ['users', 'services', 'bookings'];
    if (!in_array($table, $allowed)) {
        json_response(null, 'Internal error', 500);
    }
    $labels = ['users' => 'User', 'services' => 'Layanan', 'bookings' => 'Booking'];
    $label = $labels[$table] ?? 'Data';
    $sql = "SELECT {$columns} FROM {$table} WHERE id = ?";
    if ($extra_cond) $sql .= " AND {$extra_cond}";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id]);
    $row = $stmt->fetch();
    if (!$row) {
        json_response(null, "{$label} tidak ditemukan", 404);
    }
    return $row;
}

function cast_types(&$row, $types) {
    foreach ($types as $col => $type) {
        if (isset($row[$col])) {
            settype($row[$col], $type);
        }
    }
}

function paginate($page, $per_page = 10) {
    $page = max(1, (int)$page);
    $per_page = max(1, min(100, (int)$per_page));
    return ['limit' => $per_page, 'offset' => ($page - 1) * $per_page, 'page' => $page];
}

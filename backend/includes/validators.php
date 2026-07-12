<?php
function validate_email($email) {
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        throw new AppException('Format email tidak valid');
    }
    return $email;
}

function validate_password($password) {
    if (strlen($password) < 6) {
        throw new AppException('Password minimal 6 karakter');
    }
    return $password;
}

function validate_required($value, $field_name) {
    $value = is_string($value) ? trim($value) : $value;
    if (empty($value) && $value !== 0 && $value !== '0') {
        throw new AppException("{$field_name} harus diisi");
    }
    return $value;
}

function validate_positive_int($value, $field_name) {
    $val = (int)$value;
    if ($val <= 0) {
        throw new AppException("{$field_name} tidak valid");
    }
    return $val;
}

function validate_date_not_past($date) {
    if ($date < date('Y-m-d')) {
        throw new AppException('Tanggal tidak boleh sudah lewat');
    }
    return $date;
}

function validate_status_transition($current, $next) {
    $allowed = [
        'pending' => ['confirmed', 'cancelled'],
        'confirmed' => ['completed', 'cancelled'],
        'completed' => [],
        'cancelled' => [],
    ];
    if (!isset($allowed[$current])) {
        throw new AppException('Status saat ini tidak valid', 400);
    }
    if (!in_array($next, $allowed[$current])) {
        $labels = ['pending' => 'Menunggu', 'confirmed' => 'Dikonfirmasi', 'completed' => 'Selesai', 'cancelled' => 'Dibatalkan'];
        throw new AppException(
            "Tidak bisa mengubah status dari '" . ($labels[$current] ?? $current) . "' ke '" . ($labels[$next] ?? $next) . "'",
            400
        );
    }
}

function validate_booking_status($status) {
    $valid = ['pending', 'confirmed', 'completed', 'cancelled'];
    if (!in_array($status, $valid)) {
        throw new AppException('Status tidak valid. Pilihan: ' . implode(', ', $valid), 400);
    }
}

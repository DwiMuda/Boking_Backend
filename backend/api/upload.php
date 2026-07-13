<?php
require_once __DIR__ . '/../config/database.php';

try {
    require_method('POST');

    if (!isset($_FILES['file']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK) {
        throw new AppException('File tidak valid atau gagal upload', 400);
    }

    $file = $_FILES['file'];
    $allowed = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];

    if (!in_array($file['type'], $allowed)) {
        throw new AppException('Tipe file harus JPG, PNG, GIF, atau WebP', 400);
    }

    $max_size = 5 * 1024 * 1024;
    if ($file['size'] > $max_size) {
        throw new AppException('Ukuran file maksimal 5MB', 400);
    }

    $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = bin2hex(random_bytes(16)) . '.' . $ext;
    $upload_dir = __DIR__ . '/../uploads/bookings/';

    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }

    $dest = $upload_dir . $filename;
    if (!move_uploaded_file($file['tmp_name'], $dest)) {
        throw new AppException('Gagal menyimpan file', 500);
    }

    json_response(['foto' => 'uploads/bookings/' . $filename], 'File berhasil diupload', 201);
} catch (AppException $e) {
    json_response(null, $e->getMessage(), $e->getCode() ?: 400);
}

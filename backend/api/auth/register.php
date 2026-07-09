<?php
require_once __DIR__ . '/../../config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_response(null, 'Method tidak diizinkan', 405);
}

$input = get_input();
$nama = trim($input['nama'] ?? '');
$email = trim($input['email'] ?? '');
$password = $input['password'] ?? '';
$no_telp = trim($input['no_telp'] ?? '');

if (empty($nama) || empty($email) || empty($password)) {
    json_response(null, 'Nama, email, dan password harus diisi', 400);
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    json_response(null, 'Format email tidak valid', 400);
}

if (strlen($password) < 6) {
    json_response(null, 'Password minimal 6 karakter', 400);
}

$stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
$stmt->execute([$email]);
if ($stmt->fetch()) {
    json_response(null, 'Email sudah terdaftar', 409);
}

$hashed = password_hash($password, PASSWORD_BCRYPT);
$token = bin2hex(random_bytes(32));

$stmt = $pdo->prepare("INSERT INTO users (nama, email, password, no_telp, token) VALUES (?, ?, ?, ?, ?)");
$stmt->execute([$nama, $email, $hashed, $no_telp, $token]);

$user = [
    'id' => (int)$pdo->lastInsertId(),
    'nama' => $nama,
    'email' => $email,
    'no_telp' => $no_telp,
    'role' => 'user',
    'token' => $token
];

json_response($user, 'Registrasi berhasil', 201);

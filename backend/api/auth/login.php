<?php
require_once __DIR__ . '/../../config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_response(null, 'Method tidak diizinkan', 405);
}

$input = get_input();
$email = trim($input['email'] ?? '');
$password = $input['password'] ?? '';

if (empty($email) || empty($password)) {
    json_response(null, 'Email dan password harus diisi', 400);
}

$stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
$stmt->execute([$email]);
$user = $stmt->fetch();

if (!$user || !password_verify($password, $user['password'])) {
    json_response(null, 'Email atau password salah', 401);
}

$token = bin2hex(random_bytes(32));
$stmt = $pdo->prepare("UPDATE users SET token = ? WHERE id = ?");
$stmt->execute([$token, $user['id']]);

$response = [
    'id' => (int)$user['id'],
    'nama' => $user['nama'],
    'email' => $user['email'],
    'no_telp' => $user['no_telp'],
    'role' => $user['role'],
    'token' => $token
];

json_response($response, 'Login berhasil');

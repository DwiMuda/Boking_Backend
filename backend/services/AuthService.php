<?php
require_once __DIR__ . '/../includes/validators.php';

class AuthService {
    public static function login($pdo, $email, $password) {
        $email = validate_required($email, 'Email');
        $password = validate_required($password, 'Password');

        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if (!$user || !password_verify($password, $user['password'])) {
            throw new AppException('Email atau password salah', 401);
        }

        $token = bin2hex(random_bytes(32));
        $stmt = $pdo->prepare("UPDATE users SET token = ? WHERE id = ?");
        $stmt->execute([$token, $user['id']]);

        return [
            'id' => (int)$user['id'],
            'nama' => $user['nama'],
            'email' => $user['email'],
            'no_telp' => $user['no_telp'],
            'role' => $user['role'],
            'token' => $token
        ];
    }

    public static function register($pdo, $nama, $email, $password, $no_telp) {
        $nama = validate_required($nama, 'Nama');
        $email = validate_email(trim($email));
        $password = validate_password($password);
        $no_telp = trim($no_telp ?? '');

        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            throw new AppException('Email sudah terdaftar', 409);
        }

        $hashed = password_hash($password, PASSWORD_BCRYPT);
        $token = bin2hex(random_bytes(32));

        $stmt = $pdo->prepare("INSERT INTO users (nama, email, password, no_telp, token) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$nama, $email, $hashed, $no_telp, $token]);

        return [
            'id' => (int)$pdo->lastInsertId(),
            'nama' => $nama,
            'email' => $email,
            'no_telp' => $no_telp,
            'role' => 'user',
            'token' => $token
        ];
    }

    public static function logout($pdo, $user_id) {
        $stmt = $pdo->prepare("UPDATE users SET token = NULL WHERE id = ?");
        $stmt->execute([$user_id]);
    }

    public static function updateProfile($pdo, $user_id, $data) {
        $fields = [];
        $params = [];

        if (isset($data['nama'])) {
            $fields[] = 'nama = ?';
            $params[] = trim($data['nama']);
        }
        if (isset($data['no_telp'])) {
            $fields[] = 'no_telp = ?';
            $params[] = trim($data['no_telp']);
        }

        if (empty($fields)) {
            throw new AppException('Tidak ada data yang diupdate', 400);
        }

        $params[] = $user_id;
        $sql = "UPDATE users SET " . implode(', ', $fields) . " WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);

        $stmt = $pdo->prepare("SELECT id, nama, email, no_telp, role FROM users WHERE id = ?");
        $stmt->execute([$user_id]);
        $user = $stmt->fetch();
        $user['id'] = (int)$user['id'];
        return $user;
    }
}

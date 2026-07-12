<?php
require_once __DIR__ . '/../includes/validators.php';
require_once __DIR__ . '/../includes/mailer.php';

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

        if ($user['email_verified_at'] === null) {
            throw new AppException('Email belum diverifikasi. Silakan cek email Anda.', 403);
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
        $otp = str_pad((string)random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $expires = date('Y-m-d H:i:s', strtotime('+10 minutes'));

        $stmt = $pdo->prepare("INSERT INTO users (nama, email, password, no_telp, otp_code, otp_expires, email_verified_at) VALUES (?, ?, ?, ?, ?, ?, NULL)");
        $stmt->execute([$nama, $email, $hashed, $no_telp, $otp, $expires]);

        email_verification_otp($email, $nama, $otp);

        return ['email' => $email];
    }

    public static function verifyEmail($pdo, $email, $otp) {
        $email = validate_email(trim($email));
        $otp = trim($otp);

        $stmt = $pdo->prepare("SELECT id, nama FROM users WHERE email = ? AND otp_code = ? AND otp_expires > NOW() AND email_verified_at IS NULL");
        $stmt->execute([$email, $otp]);
        $user = $stmt->fetch();

        if (!$user) {
            throw new AppException('Kode verifikasi tidak valid atau sudah kadaluarsa', 400);
        }

        $token = bin2hex(random_bytes(32));
        $stmt = $pdo->prepare("UPDATE users SET email_verified_at = NOW(), otp_code = NULL, otp_expires = NULL, token = ? WHERE id = ?");
        $stmt->execute([$token, $user['id']]);

        $stmt = $pdo->prepare("SELECT id, nama, email, no_telp, role FROM users WHERE id = ?");
        $stmt->execute([$user['id']]);
        $updated = $stmt->fetch();
        $updated['id'] = (int)$updated['id'];
        $updated['token'] = $token;

        return $updated;
    }

    public static function resendOtp($pdo, $email) {
        $email = validate_email(trim($email));

        $stmt = $pdo->prepare("SELECT id, nama, email FROM users WHERE email = ? AND email_verified_at IS NULL");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if (!$user) {
            throw new AppException('Email tidak ditemukan atau sudah diverifikasi', 400);
        }

        $otp = str_pad((string)random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $expires = date('Y-m-d H:i:s', strtotime('+10 minutes'));

        $stmt = $pdo->prepare("UPDATE users SET otp_code = ?, otp_expires = ? WHERE id = ?");
        $stmt->execute([$otp, $expires, $user['id']]);

        email_verification_otp($user['email'], $user['nama'], $otp);
    }

    public static function logout($pdo, $user_id) {
        $stmt = $pdo->prepare("UPDATE users SET token = NULL WHERE id = ?");
        $stmt->execute([$user_id]);
    }

    public static function forgotPassword($pdo, $email) {
        $email = validate_email(trim($email));

        $stmt = $pdo->prepare("SELECT id, nama, email FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if (!$user) {
            return;
        }

        $token = bin2hex(random_bytes(32));
        $expires = date('Y-m-d H:i:s', strtotime('+60 minutes'));

        $stmt = $pdo->prepare("UPDATE users SET reset_token = ?, reset_expires = ? WHERE id = ?");
        $stmt->execute([$token, $expires, $user['id']]);

        email_reset_password($user['email'], $user['nama'], $token);
    }

    public static function resetPassword($pdo, $token, $new_password) {
        $token = trim($token);
        $new_password = validate_password($new_password);

        $stmt = $pdo->prepare("SELECT id, email FROM users WHERE reset_token = ? AND reset_expires > NOW()");
        $stmt->execute([$token]);
        $user = $stmt->fetch();

        if (!$user) {
            throw new AppException('Token tidak valid atau sudah kadaluarsa', 400);
        }

        $hashed = password_hash($new_password, PASSWORD_BCRYPT);
        $stmt = $pdo->prepare("UPDATE users SET password = ?, reset_token = NULL, reset_expires = NULL WHERE id = ?");
        $stmt->execute([$hashed, $user['id']]);

        $subject = "Password Berhasil Diubah - Sistem Booking";
        $body = "<h2>Halo,</h2><p>Password akun kamu telah berhasil diubah.</p><p>Jika kamu tidak melakukan ini, segera hubungi admin.</p>";
        send_email($user['email'], $subject, $body);
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

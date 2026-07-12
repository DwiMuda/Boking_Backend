<?php
require_once __DIR__ . '/../includes/validators.php';

class ServiceService {
    public static function getAll($pdo) {
        $stmt = $pdo->prepare("SELECT * FROM services WHERE is_active = 1 ORDER BY nama ASC");
        $stmt->execute();
        $services = $stmt->fetchAll();
        foreach ($services as &$s) {
            cast_types($s, ['id' => 'integer', 'harga' => 'double', 'durasi_menit' => 'integer', 'is_active' => 'boolean']);
        }
        return $services;
    }

    public static function getById($pdo, $id) {
        $id = validate_positive_int($id, 'ID layanan');
        $stmt = $pdo->prepare("SELECT * FROM services WHERE id = ?");
        $stmt->execute([$id]);
        $service = $stmt->fetch();
        if (!$service) {
            throw new AppException('Layanan tidak ditemukan', 404);
        }
        cast_types($service, ['id' => 'integer', 'harga' => 'double', 'durasi_menit' => 'integer', 'is_active' => 'boolean']);
        return $service;
    }

    public static function create($pdo, $data) {
        $nama = validate_required($data['nama'] ?? '', 'Nama layanan');
        $harga = (float)($data['harga'] ?? 0);
        if ($harga < 1) {
            throw new AppException('Harga harus lebih dari 0', 400);
        }
        $durasi_menit = (int)($data['durasi_menit'] ?? 60);
        if ($durasi_menit < 1) {
            throw new AppException('Durasi harus lebih dari 0', 400);
        }
        $deskripsi = trim($data['deskripsi'] ?? '');
        $gambar = trim($data['gambar'] ?? '');

        $stmt = $pdo->prepare("INSERT INTO services (nama, deskripsi, harga, durasi_menit, gambar) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$nama, $deskripsi, $harga, $durasi_menit, $gambar]);

        return [
            'id' => (int)$pdo->lastInsertId(),
            'nama' => $nama,
            'deskripsi' => $deskripsi,
            'harga' => $harga,
            'durasi_menit' => $durasi_menit,
            'gambar' => $gambar,
            'is_active' => true
        ];
    }

    public static function update($pdo, $id, $data) {
        $id = validate_positive_int($id, 'ID layanan');
        find_or_fail($pdo, 'services', $id, 'id');

        $fields = [];
        $params = [];

        if (isset($data['nama'])) {
            $fields[] = 'nama = ?';
            $params[] = trim($data['nama']);
        }
        if (isset($data['deskripsi'])) {
            $fields[] = 'deskripsi = ?';
            $params[] = trim($data['deskripsi']);
        }
        if (isset($data['harga'])) {
            $fields[] = 'harga = ?';
            $params[] = (float)$data['harga'];
        }
        if (isset($data['durasi_menit'])) {
            $fields[] = 'durasi_menit = ?';
            $params[] = (int)$data['durasi_menit'];
        }
        if (isset($data['gambar'])) {
            $fields[] = 'gambar = ?';
            $params[] = trim($data['gambar']);
        }
        if (isset($data['is_active'])) {
            $fields[] = 'is_active = ?';
            $params[] = (int)$data['is_active'];
        }

        if (empty($fields)) {
            throw new AppException('Tidak ada data yang diupdate', 400);
        }

        $params[] = $id;
        $sql = "UPDATE services SET " . implode(', ', $fields) . " WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);

        return self::getById($pdo, $id);
    }

    public static function delete($pdo, $id) {
        $id = validate_positive_int($id, 'ID layanan');
        find_or_fail($pdo, 'services', $id, 'id');

        $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM bookings WHERE service_id = ?");
        $stmt->execute([$id]);
        $count = (int)$stmt->fetch()['total'];

        if ($count > 0) {
            $stmt = $pdo->prepare("UPDATE services SET is_active = 0 WHERE id = ?");
            $stmt->execute([$id]);
            return;
        }

        $stmt = $pdo->prepare("DELETE FROM services WHERE id = ?");
        $stmt->execute([$id]);
    }
}

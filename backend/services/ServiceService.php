<?php
require_once __DIR__ . '/../includes/validators.php';

class ServiceService {
    public static function getAll($pdo, $kategori = '') {
        $sql = "SELECT * FROM services WHERE is_active = 1";
        $params = [];
        if (!empty($kategori)) {
            $sql .= " AND kategori = ?";
            $params[] = $kategori;
        }
        $sql .= " ORDER BY nama ASC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $services = $stmt->fetchAll();
        foreach ($services as &$s) {
            cast_types($s, ['id' => 'integer', 'harga' => 'double', 'durasi_menit' => 'integer', 'is_active' => 'boolean']);
        }
        return $services;
    }

    public static function getAllAdmin($pdo) {
        $stmt = $pdo->prepare("SELECT * FROM services ORDER BY nama ASC");
        $stmt->execute();
        $services = $stmt->fetchAll();
        foreach ($services as &$s) {
            cast_types($s, ['id' => 'integer', 'harga' => 'double', 'durasi_menit' => 'integer', 'is_active' => 'boolean']);
        }
        return $services;
    }

    public static function getByCategory($pdo) {
        $stmt = $pdo->prepare("SELECT * FROM services WHERE is_active = 1 ORDER BY FIELD(kategori, 'spa', 'dining', 'other'), nama ASC");
        $stmt->execute();
        $services = $stmt->fetchAll();
        $grouped = ['spa' => [], 'dining' => [], 'other' => []];
        foreach ($services as $s) {
            cast_types($s, ['id' => 'integer', 'harga' => 'double', 'durasi_menit' => 'integer', 'is_active' => 'boolean']);
            $cat = $s['kategori'] ?? 'other';
            $grouped[$cat][] = $s;
        }
        return $grouped;
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
        if ($harga < 1) throw new AppException('Harga harus lebih dari 0', 400);
        $durasi_menit = (int)($data['durasi_menit'] ?? 60);
        if ($durasi_menit < 1) throw new AppException('Durasi harus lebih dari 0', 400);
        $kategori = trim($data['kategori'] ?? 'spa');
        if (!in_array($kategori, ['spa', 'dining', 'other'])) {
            throw new AppException('Kategori tidak valid. Pilihan: spa, dining, other', 400);
        }

        $stmt = $pdo->prepare("INSERT INTO services (nama, deskripsi, harga, durasi_menit, kategori, fasilitas, gambar) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $nama,
            trim($data['deskripsi'] ?? ''),
            $harga,
            $durasi_menit,
            $kategori,
            trim($data['fasilitas'] ?? ''),
            trim($data['gambar'] ?? '')
        ]);

        return [
            'id' => (int)$pdo->lastInsertId(),
            'nama' => $nama,
            'deskripsi' => trim($data['deskripsi'] ?? ''),
            'harga' => $harga,
            'durasi_menit' => $durasi_menit,
            'kategori' => $kategori,
            'gambar' => trim($data['gambar'] ?? ''),
            'fasilitas' => trim($data['fasilitas'] ?? ''),
            'is_active' => true
        ];
    }

    public static function update($pdo, $id, $data) {
        $id = validate_positive_int($id, 'ID layanan');
        find_or_fail($pdo, 'services', $id, 'id');

        $fields = [];
        $params = [];

        foreach (['nama' => 'trim', 'deskripsi' => 'trim', 'gambar' => 'trim', 'fasilitas' => 'trim'] as $field => $fn) {
            if (isset($data[$field])) {
                $fields[] = "$field = ?";
                $params[] = $fn($data[$field]);
            }
        }
        if (isset($data['harga'])) {
            $fields[] = 'harga = ?';
            $params[] = (float)$data['harga'];
        }
        if (isset($data['durasi_menit'])) {
            $fields[] = 'durasi_menit = ?';
            $params[] = (int)$data['durasi_menit'];
        }
        if (isset($data['is_active'])) {
            $fields[] = 'is_active = ?';
            $params[] = (int)$data['is_active'];
        }
        if (isset($data['kategori'])) {
            if (!in_array($data['kategori'], ['spa', 'dining', 'other'])) {
                throw new AppException('Kategori tidak valid', 400);
            }
            $fields[] = 'kategori = ?';
            $params[] = $data['kategori'];
        }

        if (empty($fields)) throw new AppException('Tidak ada data yang diupdate', 400);

        $params[] = $id;
        $stmt = $pdo->prepare("UPDATE services SET " . implode(', ', $fields) . " WHERE id = ?");
        $stmt->execute($params);

        return self::getById($pdo, $id);
    }

    public static function delete($pdo, $id) {
        $id = validate_positive_int($id, 'ID layanan');
        find_or_fail($pdo, 'services', $id, 'id');

        $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM bookings WHERE service_id = ?");
        $stmt->execute([$id]);
        if ((int)$stmt->fetch()['total'] > 0) {
            $stmt = $pdo->prepare("UPDATE services SET is_active = 0 WHERE id = ?");
            $stmt->execute([$id]);
            return;
        }

        $stmt = $pdo->prepare("DELETE FROM services WHERE id = ?");
        $stmt->execute([$id]);
    }
}

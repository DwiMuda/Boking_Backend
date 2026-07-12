<?php
require_once __DIR__ . '/../includes/validators.php';
require_once __DIR__ . '/../includes/mailer.php';

class RoomService {
    public static function getRoomTypes($pdo) {
        $stmt = $pdo->prepare("SELECT rt.*, (SELECT COUNT(*) FROM rooms r WHERE r.room_type_id = rt.id AND r.status = 'available') as available_rooms FROM room_types rt WHERE rt.is_active = 1 ORDER BY rt.harga_per_malam ASC");
        $stmt->execute();
        $types = $stmt->fetchAll();
        foreach ($types as &$t) {
            cast_types($t, ['id' => 'integer', 'harga_per_malam' => 'double', 'kapasitas' => 'integer', 'is_active' => 'boolean', 'available_rooms' => 'integer']);
        }
        return $types;
    }

    public static function getRoomTypeById($pdo, $id) {
        $id = validate_positive_int($id, 'ID tipe kamar');
        $stmt = $pdo->prepare("SELECT * FROM room_types WHERE id = ?");
        $stmt->execute([$id]);
        $type = $stmt->fetch();
        if (!$type) {
            throw new AppException('Tipe kamar tidak ditemukan', 404);
        }
        cast_types($type, ['id' => 'integer', 'harga_per_malam' => 'double', 'kapasitas' => 'integer', 'is_active' => 'boolean']);
        return $type;
    }

    public static function getAvailableRooms($pdo, $check_in, $check_out, $kapasitas = 0) {
        $check_in = validate_required($check_in, 'Check-in');
        $check_out = validate_required($check_out, 'Check-out');

        if ($check_in <= date('Y-m-d')) {
            throw new AppException('Check-in harus setelah hari ini', 400);
        }
        if ($check_out <= $check_in) {
            throw new AppException('Check-out harus setelah check-in', 400);
        }

        $sql = "SELECT rt.*, (SELECT COUNT(*) FROM rooms r WHERE r.room_type_id = rt.id AND r.status = 'available'
                AND r.id NOT IN (
                    SELECT b.room_id FROM bookings b
                    WHERE b.room_id IS NOT NULL AND b.status NOT IN ('cancelled')
                    AND b.check_in < ? AND b.check_out > ?
                )) as available_rooms
                FROM room_types rt WHERE rt.is_active = 1";
        $params = [$check_out, $check_in];

        if ($kapasitas > 0) {
            $sql .= " AND rt.kapasitas >= ?";
            $params[] = $kapasitas;
        }

        $sql .= " ORDER BY rt.harga_per_malam ASC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $types = $stmt->fetchAll();

        foreach ($types as &$t) {
            cast_types($t, ['id' => 'integer', 'harga_per_malam' => 'double', 'kapasitas' => 'integer', 'is_active' => 'boolean', 'available_rooms' => 'integer']);
            $nights = max(1, (int)ceil((strtotime($check_out) - strtotime($check_in)) / 86400));
            $t['total_harga'] = $t['harga_per_malam'] * $nights;
            $t['malam'] = $nights;
        }

        return $types;
    }

    public static function bookRoom($pdo, $user, $data) {
        $room_type_id = validate_positive_int($data['room_type_id'] ?? 0, 'Tipe kamar');
        $check_in = validate_required($data['check_in'] ?? '', 'Check-in');
        $check_out = validate_required($data['check_out'] ?? '', 'Check-out');
        $jumlah_tamu = (int)($data['jumlah_tamu'] ?? 1);
        $catatan = trim($data['catatan'] ?? '');

        if ($check_in <= date('Y-m-d')) {
            throw new AppException('Check-in harus setelah hari ini', 400);
        }
        if ($check_out <= $check_in) {
            throw new AppException('Check-out harus setelah check-in', 400);
        }

        $room_type = find_or_fail($pdo, 'room_types', $room_type_id, '*', "is_active = 1");

        if ($jumlah_tamu > (int)$room_type['kapasitas']) {
            throw new AppException("Kapasitas maksimal tipe kamar ini adalah {$room_type['kapasitas']} tamu", 400);
        }

        $nights = max(1, (int)ceil((strtotime($check_out) - strtotime($check_in)) / 86400));
        $total_harga = (float)$room_type['harga_per_malam'] * $nights;

        $stmt = $pdo->prepare("SELECT r.id FROM rooms r
            WHERE r.room_type_id = ? AND r.status = 'available'
            AND r.id NOT IN (
                SELECT b.room_id FROM bookings b
                WHERE b.room_id IS NOT NULL AND b.status NOT IN ('cancelled')
                AND b.check_in < ? AND b.check_out > ?
            )
            LIMIT 1");
        $stmt->execute([$room_type_id, $check_out, $check_in]);
        $available_room = $stmt->fetch();

        if (!$available_room) {
            throw new AppException('Tidak ada kamar tersedia untuk tanggal tersebut', 409);
        }

        $stmt = $pdo->prepare("INSERT INTO bookings (user_id, room_id, tipe_booking, check_in, check_out, jumlah_tamu, total_harga, catatan)
            VALUES (?, ?, 'room', ?, ?, ?, ?, ?)");
        $stmt->execute([$user['id'], $available_room['id'], $check_in, $check_out, $jumlah_tamu, $total_harga, $catatan]);
        $booking_id = (int)$pdo->lastInsertId();

        $stmt = $pdo->prepare("SELECT b.*, rt.nama as room_type_nama, r.nomor_kamar, rt.harga_per_malam
            FROM bookings b
            JOIN rooms r ON b.room_id = r.id
            JOIN room_types rt ON r.room_type_id = rt.id
            WHERE b.id = ?");
        $stmt->execute([$booking_id]);
        $booking = $stmt->fetch();
        cast_types($booking, ['id' => 'integer', 'user_id' => 'integer', 'total_harga' => 'double', 'jumlah_tamu' => 'integer']);

        $stmt = $pdo->prepare("SELECT email, nama FROM users WHERE id = ?");
        $stmt->execute([$user['id']]);
        $user_data = $stmt->fetch();

        $email_data = [
            'id' => $booking['id'],
            'service_nama' => 'Room: ' . $booking['room_type_nama'] . ' #' . $booking['nomor_kamar'],
            'tgl_booking' => $booking['check_in'] . ' s/d ' . $booking['check_out'],
            'jam_booking' => 'Check-in: 14:00',
            'total_harga' => $booking['total_harga'],
        ];
        email_booking_created($user_data['email'], $user_data['nama'], $email_data);

        $stmt = $pdo->prepare("SELECT email FROM users WHERE role = 'admin' LIMIT 1");
        $stmt->execute();
        $admin = $stmt->fetch();
        if ($admin) {
            email_booking_created($admin['email'], 'Admin', $email_data);
        }

        return $booking;
    }

    public static function createRoomType($pdo, $data) {
        $nama = validate_required($data['nama'] ?? '', 'Nama tipe kamar');
        $harga = (float)($data['harga_per_malam'] ?? 0);
        if ($harga < 1) throw new AppException('Harga harus lebih dari 0', 400);
        $kapasitas = (int)($data['kapasitas'] ?? 2);
        if ($kapasitas < 1) throw new AppException('Kapasitas harus lebih dari 0', 400);

        $stmt = $pdo->prepare("INSERT INTO room_types (nama, deskripsi, harga_per_malam, kapasitas, gambar, fasilitas) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $nama,
            trim($data['deskripsi'] ?? ''),
            $harga,
            $kapasitas,
            trim($data['gambar'] ?? ''),
            trim($data['fasilitas'] ?? '')
        ]);

        return ['id' => (int)$pdo->lastInsertId(), 'nama' => $nama, 'harga_per_malam' => $harga, 'kapasitas' => $kapasitas, 'is_active' => true];
    }

    public static function updateRoomType($pdo, $id, $data) {
        $id = validate_positive_int($id, 'ID tipe kamar');
        find_or_fail($pdo, 'room_types', $id, 'id');

        $fields = [];
        $params = [];
        foreach (['nama' => 'trim', 'deskripsi' => 'trim', 'gambar' => 'trim', 'fasilitas' => 'trim'] as $field => $fn) {
            if (isset($data[$field])) {
                $fields[] = "$field = ?";
                $params[] = $fn($data[$field]);
            }
        }
        if (isset($data['harga_per_malam'])) {
            $fields[] = 'harga_per_malam = ?';
            $params[] = (float)$data['harga_per_malam'];
        }
        if (isset($data['kapasitas'])) {
            $fields[] = 'kapasitas = ?';
            $params[] = (int)$data['kapasitas'];
        }
        if (isset($data['is_active'])) {
            $fields[] = 'is_active = ?';
            $params[] = (int)$data['is_active'];
        }

        if (empty($fields)) throw new AppException('Tidak ada data yang diupdate', 400);

        $params[] = $id;
        $stmt = $pdo->prepare("UPDATE room_types SET " . implode(', ', $fields) . " WHERE id = ?");
        $stmt->execute($params);

        return self::getRoomTypeById($pdo, $id);
    }

    public static function deleteRoomType($pdo, $id) {
        $id = validate_positive_int($id, 'ID tipe kamar');
        find_or_fail($pdo, 'room_types', $id, 'id');

        $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM bookings WHERE room_id IN (SELECT id FROM rooms WHERE room_type_id = ?)");
        $stmt->execute([$id]);
        $count = (int)$stmt->fetch()['total'];

        if ($count > 0) {
            $stmt = $pdo->prepare("UPDATE room_types SET is_active = 0 WHERE id = ?");
            $stmt->execute([$id]);
            return;
        }

        $stmt = $pdo->prepare("DELETE FROM rooms WHERE room_type_id = ?");
        $stmt->execute([$id]);
        $stmt = $pdo->prepare("DELETE FROM room_types WHERE id = ?");
        $stmt->execute([$id]);
    }
}

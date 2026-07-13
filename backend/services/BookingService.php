<?php
require_once __DIR__ . '/../includes/validators.php';
require_once __DIR__ . '/../includes/mailer.php';

class BookingService {
    public static function create($pdo, $user, $data) {
        $service_id = validate_positive_int($data['service_id'] ?? 0, 'Layanan');
        $tgl = validate_required($data['tgl_booking'] ?? '', 'Tanggal booking');
        $jam = validate_required($data['jam_booking'] ?? '', 'Jam booking');
        $catatan = trim($data['catatan'] ?? '');

        validate_date_not_past($tgl);

        $service = find_or_fail($pdo, 'services', $service_id, '*', "is_active = 1");
        $durasi = (int)$service['durasi_menit'];

        $pdo->beginTransaction();
        try {
            // Cek overlap waktu: booking baru tidak boleh bertabrakan dengan booking yang sudah ada
            // Menggunakan SELECT ... FOR UPDATE untuk mencegah race condition
            $stmt = $pdo->prepare("
                SELECT b.jam_booking, s.durasi_menit
                FROM bookings b
                JOIN services s ON b.service_id = s.id
                WHERE b.service_id = ? AND b.tgl_booking = ? AND b.status != 'cancelled'
                FOR UPDATE");
            $stmt->execute([$service_id, $tgl]);
            $existing = $stmt->fetchAll();

            // Hitung waktu mulai dan selesai booking baru
            $new_start = strtotime($jam);
            $new_end = $new_start + ($durasi * 60);

            foreach ($existing as $ex) {
                $ex_start = strtotime($ex['jam_booking']);
                $ex_end = $ex_start + ((int)$ex['durasi_menit'] * 60);

                // Cek apakah ada overlap: booking baru mulai sebelum yang lama selesai DAN selesai setelah yang lama mulai
                if ($new_start < $ex_end && $new_end > $ex_start) {
                    $pdo->rollBack();
                    throw new AppException('Jadwal bertabrakan dengan booking lain. Silakan pilih jam lain.', 409);
                }
            }

            $stmt = $pdo->prepare("INSERT INTO bookings (user_id, service_id, tipe_booking, tgl_booking, jam_booking, total_harga, catatan) VALUES (?, ?, 'service', ?, ?, ?, ?)");
            $stmt->execute([$user['id'], $service_id, $tgl, $jam, $service['harga'], $catatan]);

            $booking_id = (int)$pdo->lastInsertId();
            $pdo->commit();
        } catch (AppException $e) {
            if ($pdo->inTransaction()) $pdo->rollBack();
            throw $e;
        } catch (\Exception $e) {
            if ($pdo->inTransaction()) $pdo->rollBack();
            throw new AppException('Gagal membuat booking: ' . $e->getMessage(), 500);
        }

        $stmt = $pdo->prepare("SELECT b.*, s.nama as service_nama, s.deskripsi as service_deskripsi, s.durasi_menit, s.kategori
            FROM bookings b JOIN services s ON b.service_id = s.id WHERE b.id = ?");
        $stmt->execute([$booking_id]);
        $booking = $stmt->fetch();
        cast_types($booking, ['id' => 'integer', 'user_id' => 'integer', 'service_id' => 'integer', 'total_harga' => 'double', 'durasi_menit' => 'integer', 'jumlah_tamu' => 'integer']);

        $stmt = $pdo->prepare("SELECT email, nama FROM users WHERE id = ?");
        $stmt->execute([$user['id']]);
        $user_data = $stmt->fetch();

        email_booking_created($user_data['email'], $user_data['nama'], $booking);

        $stmt = $pdo->prepare("SELECT email FROM users WHERE role = 'admin' LIMIT 1");
        $stmt->execute();
        $admin = $stmt->fetch();
        if ($admin) {
            email_booking_created($admin['email'], 'Admin', $booking);
        }

        return $booking;
    }


    public static function checkAvailability($pdo, $service_id, $tgl) {
        $service_id = validate_positive_int($service_id, 'ID layanan');
        $tgl = validate_required($tgl, 'Tanggal');

        $stmt = $pdo->prepare("SELECT durasi_menit FROM services WHERE id = ? AND is_active = 1");
        $stmt->execute([$service_id]);
        $service = $stmt->fetch();
        if (!$service) {
            throw new AppException('Layanan tidak ditemukan', 404);
        }

        $service_durasi = (int)$service['durasi_menit'];
        $slots_needed = max(1, ceil($service_durasi / 30));

        $stmt = $pdo->prepare("
            SELECT b.jam_booking, s.durasi_menit
            FROM bookings b
            JOIN services s ON b.service_id = s.id
            WHERE b.service_id = ? AND b.tgl_booking = ? AND b.status != 'cancelled'");
        $stmt->execute([$service_id, $tgl]);
        $booked = $stmt->fetchAll();

        $booked_slots = [];
        foreach ($booked as $b) {
            $booked_durasi = (int)$b['durasi_menit'];
            $slots = max(1, ceil($booked_durasi / 30));
            $start = $b['jam_booking'];
            $start_ts = strtotime($start);
            for ($i = 0; $i < $slots; $i++) {
                $booked_slots[] = date('H:i:s', $start_ts + $i * 1800);
            }
        }

        $slot_start = (int)($_ENV['SLOT_START'] ?? 8);
        $slot_end = (int)($_ENV['SLOT_END'] ?? 18);
        $all_slots = [];
        for ($h = $slot_start; $h < $slot_end; $h++) {
            $all_slots[] = sprintf('%02d:00:00', $h);
            $all_slots[] = sprintf('%02d:30:00', $h);
        }

        $available = [];
        $unavailable = [];
        foreach ($all_slots as $slot) {
            $blocked = false;
            for ($i = 0; $i < $slots_needed; $i++) {
                $check_slot = date('H:i:s', strtotime($slot) + $i * 1800);
                if (in_array($check_slot, $booked_slots)) {
                    $blocked = true;
                    break;
                }
            }
            if ($blocked || in_array($slot, $booked_slots)) {
                $unavailable[] = $slot;
            } else {
                $available[] = $slot;
            }
        }

        return [
            'tanggal' => $tgl,
            'service_id' => $service_id,
            'available_slots' => $available,
            'booked_slots' => $unavailable
        ];
    }

    public static function cancel($pdo, $user, $id) {
        $id = validate_positive_int($id, 'ID booking');
        $booking = find_or_fail($pdo, 'bookings', $id);

        if ($booking['user_id'] != $user['id']) {
            throw new AppException('Akses ditolak', 403);
        }
        if ($booking['status'] !== 'pending') {
            throw new AppException('Hanya booking dengan status pending yang bisa dibatalkan', 400);
        }

        $stmt = $pdo->prepare("UPDATE bookings SET status = 'cancelled' WHERE id = ?");
        $stmt->execute([$id]);

        email_status_update($user['email'], $user['nama'], $id, 'cancelled');
    }

    public static function getDetail($pdo, $user, $id) {
        $id = validate_positive_int($id, 'ID booking');
        $stmt = $pdo->prepare("SELECT b.*, s.nama as service_nama, s.deskripsi as service_deskripsi, s.durasi_menit, s.kategori,
            u.nama as user_nama, u.email as user_email, u.no_telp as user_no_telp,
            rt.nama as room_type_nama, r.nomor_kamar, rt.harga_per_malam as room_harga_per_malam
            FROM bookings b
            LEFT JOIN services s ON b.service_id = s.id
            LEFT JOIN rooms r ON b.room_id = r.id
            LEFT JOIN room_types rt ON r.room_type_id = rt.id
            JOIN users u ON b.user_id = u.id WHERE b.id = ?");
        $stmt->execute([$id]);
        $booking = $stmt->fetch();

        if (!$booking) {
            throw new AppException('Booking tidak ditemukan', 404);
        }
        if ($booking['user_id'] != $user['id'] && $user['role'] !== 'admin') {
            throw new AppException('Akses ditolak', 403);
        }

        cast_types($booking, ['id' => 'integer', 'user_id' => 'integer', 'service_id' => 'integer', 'total_harga' => 'double', 'durasi_menit' => 'integer', 'jumlah_tamu' => 'integer']);
        return $booking;
    }

    public static function getMyBookings($pdo, $user_id, $page = 1, $tipe = '') {
        $p = paginate($page);
        $count_sql = "SELECT COUNT(*) as total FROM bookings WHERE user_id = ?";
        $sql = "SELECT b.*, s.nama as service_nama, s.durasi_menit, s.kategori,
            rt.nama as room_type_nama, r.nomor_kamar
            FROM bookings b
            LEFT JOIN services s ON b.service_id = s.id
            LEFT JOIN rooms r ON b.room_id = r.id
            LEFT JOIN room_types rt ON r.room_type_id = rt.id
            WHERE b.user_id = ?";
            
        $params = [$user_id];
        
        if (!empty($tipe)) {
            $count_sql .= " AND tipe_booking = ?";
            $sql .= " AND b.tipe_booking = ?";
            $params[] = $tipe;
        }
        
        $stmt = $pdo->prepare($count_sql);
        $stmt->execute($params);
        $total = (int)$stmt->fetch()['total'];

        $sql .= " ORDER BY b.created_at DESC LIMIT ? OFFSET ?";
        $params[] = $p['limit'];
        $params[] = $p['offset'];
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $bookings = $stmt->fetchAll();

        foreach ($bookings as &$b) {
            cast_types($b, ['id' => 'integer', 'user_id' => 'integer', 'service_id' => 'integer', 'total_harga' => 'double', 'durasi_menit' => 'integer', 'jumlah_tamu' => 'integer']);
        }

        return ['items' => $bookings, 'total' => $total, 'page' => $p['page'], 'per_page' => $p['limit']];
    }

    public static function getAllBookings($pdo, $filters = [], $page = 1) {
        $p = paginate($page);
        $sql = "SELECT b.*, s.nama as service_nama, s.durasi_menit, s.kategori,
            u.nama as user_nama, u.email as user_email, u.no_telp as user_no_telp,
            rt.nama as room_type_nama, r.nomor_kamar
            FROM bookings b
            LEFT JOIN services s ON b.service_id = s.id
            LEFT JOIN rooms r ON b.room_id = r.id
            LEFT JOIN room_types rt ON r.room_type_id = rt.id
            JOIN users u ON b.user_id = u.id WHERE 1=1";
        $count_sql = "SELECT COUNT(*) as total FROM bookings b
            LEFT JOIN services s ON b.service_id = s.id
            LEFT JOIN rooms r ON b.room_id = r.id
            LEFT JOIN room_types rt ON r.room_type_id = rt.id
            JOIN users u ON b.user_id = u.id WHERE 1=1";
        $params = [];

        if (!empty($filters['status'])) {
            $sql .= " AND b.status = ?";
            $count_sql .= " AND b.status = ?";
            $params[] = $filters['status'];
        }
        if (!empty($filters['search'])) {
            $sql .= " AND (u.nama LIKE ? OR s.nama LIKE ? OR rt.nama LIKE ?)";
            $count_sql .= " AND (u.nama LIKE ? OR s.nama LIKE ? OR rt.nama LIKE ?)";
            $search = '%' . $filters['search'] . '%';
            $params[] = $search;
            $params[] = $search;
            $params[] = $search;
        }
        if (!empty($filters['tipe'])) {
            $sql .= " AND b.tipe_booking = ?";
            $count_sql .= " AND b.tipe_booking = ?";
            $params[] = $filters['tipe'];
        }

        $stmt = $pdo->prepare($count_sql);
        $stmt->execute($params);
        $total = (int)$stmt->fetch()['total'];

        $sql .= " ORDER BY b.created_at DESC LIMIT ? OFFSET ?";
        $params[] = $p['limit'];
        $params[] = $p['offset'];

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $bookings = $stmt->fetchAll();

        foreach ($bookings as &$b) {
            cast_types($b, ['id' => 'integer', 'user_id' => 'integer', 'service_id' => 'integer', 'total_harga' => 'double', 'durasi_menit' => 'integer', 'jumlah_tamu' => 'integer']);
        }

        return ['items' => $bookings, 'total' => $total, 'page' => $p['page'], 'per_page' => $p['limit']];
    }

    public static function updateStatus($pdo, $id, $status) {
        $id = validate_positive_int($id, 'ID booking');
        $status = trim($status);
        validate_booking_status($status);

        $stmt = $pdo->prepare("SELECT b.id, b.status, b.user_id, u.email, u.nama FROM bookings b JOIN users u ON b.user_id = u.id WHERE b.id = ?");
        $stmt->execute([$id]);
        $booking = $stmt->fetch();

        if (!$booking) {
            throw new AppException('Booking tidak ditemukan', 404);
        }

        validate_status_transition($booking['status'], $status);

        $stmt = $pdo->prepare("UPDATE bookings SET status = ? WHERE id = ?");
        $stmt->execute([$status, $id]);

        email_status_update($booking['email'], $booking['nama'], $id, $status);
    }
}

<?php
require_once __DIR__ . '/config/database.php';

try {
    echo "Memulai proses penyisipan data asli ke database...\n";

    $stmt = $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'user'");
    $currentUserCount = $stmt->fetchColumn();
    $neededUsers = 16 - $currentUserCount;
    
    for ($i = 0; $i < $neededUsers; $i++) {
        $pdo->exec("INSERT INTO users (nama, email, password, role) VALUES ('User Asli $i', 'user_asli_$i@test.com', 'password', 'user')");
    }

    $stmt = $pdo->query("SELECT COUNT(*) FROM room_types WHERE is_active = 1");
    $currentRoomTypes = $stmt->fetchColumn();
    $neededRoomTypes = 7 - $currentRoomTypes;

    for ($i = 0; $i < $neededRoomTypes; $i++) {
        $pdo->exec("INSERT INTO room_types (nama, deskripsi, harga_per_malam, kapasitas, is_active) VALUES ('Tipe Kamar Tambahan $i', 'Deskripsi kamar', 500000, 2, 1)");
    }

    $stmt = $pdo->query("SELECT COUNT(*) FROM rooms WHERE status = 'available'");
    $currentRooms = $stmt->fetchColumn();
    $neededRooms = 23 - $currentRooms;
    
    $roomTypeId = $pdo->query("SELECT id FROM room_types LIMIT 1")->fetchColumn();
    for ($i = 0; $i < $neededRooms; $i++) {
        $pdo->exec("INSERT INTO rooms (room_type_id, nomor_kamar, lantai, status) VALUES ($roomTypeId, 'X-$i', 1, 'available')");
    }

    $pdo->exec("DELETE FROM bookings");

    $userId = $pdo->query("SELECT id FROM users WHERE role = 'user' LIMIT 1")->fetchColumn();
    $serviceId = $pdo->query("SELECT id FROM services LIMIT 1")->fetchColumn();

    for ($i = 0; $i < 9; $i++) {
        $pdo->exec("INSERT INTO bookings (user_id, service_id, tipe_booking, tgl_booking, total_harga, status) 
                    VALUES ($userId, $serviceId, 'service', CURDATE(), 0, 'pending')");
    }

    $pdo->exec("INSERT INTO bookings (user_id, service_id, tipe_booking, tgl_booking, total_harga, status) 
                VALUES ($userId, $serviceId, 'service', CURDATE(), 200000, 'confirmed')");
    $pdo->exec("INSERT INTO bookings (user_id, service_id, tipe_booking, tgl_booking, total_harga, status) 
                VALUES ($userId, $serviceId, 'service', CURDATE(), 150000, 'confirmed')");

    for ($i = 0; $i < 2; $i++) {
        $pdo->exec("INSERT INTO bookings (user_id, service_id, tipe_booking, tgl_booking, total_harga, status) 
                    VALUES ($userId, $serviceId, 'service', CURDATE(), 0, 'cancelled')");
    }

    echo "Selesai! Data berhasil disisipkan ke dalam database.\nSilakan refresh Dashboard Anda.\n";
} catch (Exception $e) {
    echo "Terjadi kesalahan: " . $e->getMessage() . "\n";
}

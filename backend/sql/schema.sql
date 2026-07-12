CREATE DATABASE IF NOT EXISTS booking_system CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE booking_system;

CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    no_telp VARCHAR(20) DEFAULT '',
    role ENUM('user', 'admin') DEFAULT 'user',
    token VARCHAR(64) DEFAULT NULL,
    otp_code VARCHAR(6) DEFAULT NULL,
    otp_expires DATETIME DEFAULT NULL,
    email_verified_at DATETIME DEFAULT NULL,
    reset_token VARCHAR(64) DEFAULT NULL,
    reset_expires DATETIME DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS services (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(100) NOT NULL,
    deskripsi TEXT,
    harga DECIMAL(10,2) NOT NULL,
    durasi_menit INT NOT NULL DEFAULT 60,
    kategori ENUM('spa', 'dining', 'other') NOT NULL DEFAULT 'spa',
    fasilitas TEXT,
    gambar VARCHAR(255) DEFAULT '',
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS room_types (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(100) NOT NULL,
    deskripsi TEXT,
    harga_per_malam DECIMAL(10,2) NOT NULL,
    kapasitas INT NOT NULL DEFAULT 2,
    gambar VARCHAR(255) DEFAULT '',
    fasilitas TEXT,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS rooms (
    id INT AUTO_INCREMENT PRIMARY KEY,
    room_type_id INT NOT NULL,
    nomor_kamar VARCHAR(10) NOT NULL,
    lantai INT NOT NULL DEFAULT 1,
    status ENUM('available', 'occupied', 'maintenance') DEFAULT 'available',
    FOREIGN KEY (room_type_id) REFERENCES room_types(id) ON DELETE RESTRICT,
    INDEX idx_room_type (room_type_id),
    INDEX idx_room_status (status)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS bookings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    service_id INT DEFAULT NULL,
    room_id INT DEFAULT NULL,
    tipe_booking ENUM('service', 'room', 'package') NOT NULL DEFAULT 'service',
    tgl_booking DATE DEFAULT NULL,
    jam_booking TIME DEFAULT NULL,
    check_in DATE DEFAULT NULL,
    check_out DATE DEFAULT NULL,
    jumlah_tamu INT DEFAULT 1,
    total_harga DECIMAL(10,2) NOT NULL,
    status ENUM('pending','confirmed','completed','cancelled') DEFAULT 'pending',
    catatan TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE RESTRICT,
    FOREIGN KEY (service_id) REFERENCES services(id) ON DELETE RESTRICT,
    FOREIGN KEY (room_id) REFERENCES rooms(id) ON DELETE RESTRICT,
    INDEX idx_booking_slot (service_id, tgl_booking, jam_booking, status),
    INDEX idx_booking_room (room_id, check_in, check_out, status),
    INDEX idx_user_bookings (user_id, created_at),
    INDEX idx_status_date (status, tgl_booking),
    INDEX idx_tipe_booking (tipe_booking)
) ENGINE=InnoDB;

INSERT INTO users (nama, email, password, role, email_verified_at) VALUES
('Admin', 'admin@admin.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', NOW());

INSERT INTO services (nama, deskripsi, harga, durasi_menit, kategori) VALUES
('Classic Massage', 'Pijat relaksasi 60 menit untuk melepas penat setelah liburan', 150000, 60, 'spa'),
('Aromatherapy', 'Terapi aroma dengan minyak esensial pilihan', 200000, 75, 'spa'),
('Body Scrub', 'Lulur badan tradisional dengan bahan alami', 180000, 60, 'spa'),
('Facial Treatment', 'Perawatan wajah untuk kulit cerah dan sehat', 175000, 50, 'spa'),
('Fine Dining Set Menu', 'Pengalaman makan malam 3-course dengan pemandangan pantai', 350000, 120, 'dining'),
('Breakfast Buffet', 'Sarapan prasmanan dengan menu internasional dan lokal', 100000, 60, 'dining'),
('Afternoon Tea', 'Teh sore dengan kue tradisional dan view sunset', 125000, 60, 'dining'),
('Private Dinner', 'Makan malam privat di tepi kolam renang', 500000, 150, 'dining');

INSERT INTO room_types (nama, deskripsi, harga_per_malam, kapasitas, fasilitas) VALUES
('Standard Room', 'Kamar nyaman dengan fasilitas standar, cocok untuk solo traveler', 500000, 2, 'AC, TV, WiFi, Kamar Mandi Dalam, Breakfast'),
('Deluxe Room', 'Kamar lebih luas dengan balkon pribadi dan view taman', 850000, 2, 'AC, TV, WiFi, Kamar Mandi Dalam, Bathtub, Minibar, Breakfast'),
('Suite Room', 'Suite mewah dengan ruang tamu terpisah dan view laut', 1500000, 3, 'AC, TV, WiFi, Kamar Mandi Dalam, Bathtub, Living Room, Minibar, Breakfast, Airport Transfer'),
('Family Room', 'Kamar besar untuk keluarga dengan 2 kamar tidur', 1200000, 4, '2 AC, 2 TV, WiFi, 2 Kamar Mandi, Living Room, Dapur Mini, Breakfast'),
('Villa', 'Villa pribadi dengan kolam renang dan taman', 3000000, 6, 'AC, TV, WiFi, Kolam Renang Pribadi, Taman, Dapur, 2 Kamar Mandi, Breakfast, Private Chef, Airport Transfer');

INSERT INTO rooms (room_type_id, nomor_kamar, lantai) VALUES
(1, '101', 1), (1, '102', 1), (1, '103', 1), (1, '104', 1), (1, '105', 1),
(1, '106', 1), (1, '107', 1), (1, '108', 1),
(2, '201', 2), (2, '202', 2), (2, '203', 2), (2, '204', 2), (2, '205', 2),
(2, '206', 2), (2, '207', 2), (2, '208', 2),
(3, '301', 3), (3, '302', 3), (3, '303', 3),
(4, '401', 4), (4, '402', 4),
(5, 'V01', 0), (5, 'V02', 0);

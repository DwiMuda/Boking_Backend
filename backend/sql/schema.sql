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
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS services (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(100) NOT NULL,
    deskripsi TEXT,
    harga DECIMAL(10,2) NOT NULL,
    durasi_menit INT NOT NULL DEFAULT 60,
    gambar VARCHAR(255) DEFAULT '',
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS bookings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    service_id INT NOT NULL,
    tgl_booking DATE NOT NULL,
    jam_booking TIME NOT NULL,
    total_harga DECIMAL(10,2) NOT NULL,
    status ENUM('pending','confirmed','completed','cancelled') DEFAULT 'pending',
    catatan TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE RESTRICT,
    FOREIGN KEY (service_id) REFERENCES services(id) ON DELETE RESTRICT,
    INDEX idx_booking_slot (service_id, tgl_booking, jam_booking, status),
    INDEX idx_user_bookings (user_id, created_at),
    INDEX idx_status_date (status, tgl_booking)
) ENGINE=InnoDB;

INSERT INTO users (nama, email, password, role) VALUES
('Admin', 'admin@admin.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');

INSERT INTO services (nama, deskripsi, harga, durasi_menit) VALUES
('Potong Rambut', 'Potong rambut sesuai keinginan anda', 50000, 45),
('Creambath', 'Treatment creambath untuk rambut sehat', 75000, 60),
('Catok Rambut', 'Catok rambut dengan alat berkualitas', 60000, 30),
('Hair Coloring', 'Pewarnaan rambut dengan warna pilihan', 150000, 90),
('Styling', 'Styling rambut untuk acara spesial', 80000, 45);

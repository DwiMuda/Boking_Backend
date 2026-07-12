-- Migration: Fix CASCADE, add indexes, add updated_at
-- Jalankan jika sudah ada tabel lama

ALTER TABLE bookings DROP FOREIGN KEY bookings_ibfk_1;
ALTER TABLE bookings DROP FOREIGN KEY bookings_ibfk_2;

ALTER TABLE bookings ADD CONSTRAINT fk_bookings_user
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE RESTRICT;

ALTER TABLE bookings ADD CONSTRAINT fk_bookings_service
    FOREIGN KEY (service_id) REFERENCES services(id) ON DELETE RESTRICT;

ALTER TABLE bookings ADD COLUMN IF NOT EXISTS updated_at TIMESTAMP
    DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP AFTER created_at;

ALTER TABLE bookings ADD INDEX IF NOT EXISTS idx_booking_slot (service_id, tgl_booking, jam_booking, status);
ALTER TABLE bookings ADD INDEX IF NOT EXISTS idx_user_bookings (user_id, created_at);
ALTER TABLE bookings ADD INDEX IF NOT EXISTS idx_status_date (status, tgl_booking);

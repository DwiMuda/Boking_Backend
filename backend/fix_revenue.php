<?php
require_once __DIR__ . '/config/database.php';

try {
    // Ambil harga layanan untuk update
    $stmt = $pdo->query("SELECT id, harga FROM services");
    $services = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);

    $updatedCount = 0;

    foreach ($services as $service_id => $harga) {
        $stmt = $pdo->prepare("UPDATE bookings SET total_harga = ? WHERE service_id = ? AND total_harga = 0 AND tipe_booking = 'service'");
        $stmt->execute([$harga, $service_id]);
        $updatedCount += $stmt->rowCount();
    }

    echo "Berhasil memperbarui total_harga untuk $updatedCount booking layanan.\n";

} catch (Exception $e) {
    echo "Terjadi kesalahan: " . $e->getMessage() . "\n";
}

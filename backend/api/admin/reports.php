<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/auth_middleware.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    json_response(null, 'Method tidak diizinkan', 405);
}

$admin = require_admin($pdo);

$from = $_GET['from'] ?? date('Y-m-d', strtotime('-30 days'));
$to = $_GET['to'] ?? date('Y-m-d');

$stmt = $pdo->prepare("SELECT DATE(b.tgl_booking) as tanggal, s.nama as service_nama, COUNT(*) as total_booking, SUM(b.total_harga) as total_pendapatan FROM bookings b JOIN services s ON b.service_id = s.id WHERE b.tgl_booking >= ? AND b.tgl_booking <= ? AND b.status IN ('confirmed', 'completed') GROUP BY tanggal, s.nama ORDER BY tanggal ASC");
$stmt->execute([$from, $to]);
$report_data = $stmt->fetchAll();

foreach ($report_data as &$rd) {
    $rd['total_booking'] = (int)$rd['total_booking'];
    $rd['total_pendapatan'] = (float)$rd['total_pendapatan'];
}

$stmt = $pdo->prepare("SELECT s.nama, COUNT(*) as total, SUM(b.total_harga) as revenue FROM bookings b JOIN services s ON b.service_id = s.id WHERE b.tgl_booking >= ? AND b.tgl_booking <= ? AND b.status IN ('confirmed', 'completed') GROUP BY s.id, s.nama ORDER BY total DESC");
$stmt->execute([$from, $to]);
$service_summary = $stmt->fetchAll();

foreach ($service_summary as &$ss) {
    $ss['total'] = (int)$ss['total'];
    $ss['revenue'] = (float)$ss['revenue'];
}

$stmt = $pdo->prepare("SELECT COUNT(*) as total, SUM(total_harga) as revenue FROM bookings WHERE tgl_booking >= ? AND tgl_booking <= ? AND status IN ('confirmed', 'completed')");
$stmt->execute([$from, $to]);
$summary = $stmt->fetch();
$summary['total'] = (int)$summary['total'];
$summary['revenue'] = (float)$summary['revenue'];

json_response([
    'from' => $from,
    'to' => $to,
    'summary' => $summary,
    'daily' => $report_data,
    'by_service' => $service_summary
], 'Laporan berhasil diambil');

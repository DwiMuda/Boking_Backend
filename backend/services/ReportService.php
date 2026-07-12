<?php
class ReportService {
    public static function getDashboard($pdo) {
        $stats = [];
        $queries = [
            'total_bookings' => "SELECT COUNT(*) as total FROM bookings",
            'pending_bookings' => "SELECT COUNT(*) as total FROM bookings WHERE status = 'pending'",
            'confirmed_bookings' => "SELECT COUNT(*) as total FROM bookings WHERE status = 'confirmed'",
            'completed_bookings' => "SELECT COUNT(*) as total FROM bookings WHERE status = 'completed'",
            'total_users' => "SELECT COUNT(*) as total FROM users WHERE role = 'user'",
            'total_services' => "SELECT COUNT(*) as total FROM services WHERE is_active = 1",
            'total_revenue' => "SELECT COALESCE(SUM(total_harga), 0) as total FROM bookings WHERE status IN ('confirmed', 'completed')",
        ];

        foreach ($queries as $key => $sql) {
            $stmt = $pdo->prepare($sql);
            $stmt->execute();
            $result = $stmt->fetch();
            $stats[$key] = in_array($key, ['total_bookings', 'pending_bookings', 'confirmed_bookings', 'completed_bookings', 'total_users', 'total_services'])
                ? (int)$result['total']
                : (float)$result['total'];
        }

        $stmt = $pdo->prepare("SELECT DATE(tgl_booking) as tgl, COUNT(*) as total FROM bookings
            WHERE tgl_booking >= CURDATE() AND tgl_booking < CURDATE() + INTERVAL 7 DAY
            GROUP BY tgl_booking ORDER BY tgl_booking");
        $stmt->execute();
        $stats['weekly_bookings'] = $stmt->fetchAll();
        foreach ($stats['weekly_bookings'] as &$wb) {
            $wb['total'] = (int)$wb['total'];
        }

        return $stats;
    }

    public static function getReport($pdo, $from, $to) {
        $from = $from ?: date('Y-m-d', strtotime('-30 days'));
        $to = $to ?: date('Y-m-d');

        $stmt = $pdo->prepare("SELECT DATE(b.tgl_booking) as tanggal, s.nama as service_nama,
            COUNT(*) as total_booking, SUM(b.total_harga) as total_pendapatan
            FROM bookings b JOIN services s ON b.service_id = s.id
            WHERE b.tgl_booking >= ? AND b.tgl_booking <= ? AND b.status IN ('confirmed', 'completed')
            GROUP BY tanggal, s.nama ORDER BY tanggal ASC");
        $stmt->execute([$from, $to]);
        $report_data = $stmt->fetchAll();
        foreach ($report_data as &$rd) {
            cast_types($rd, ['total_booking' => 'integer', 'total_pendapatan' => 'double']);
        }

        $stmt = $pdo->prepare("SELECT s.nama, COUNT(*) as total, SUM(b.total_harga) as revenue
            FROM bookings b JOIN services s ON b.service_id = s.id
            WHERE b.tgl_booking >= ? AND b.tgl_booking <= ? AND b.status IN ('confirmed', 'completed')
            GROUP BY s.id, s.nama ORDER BY total DESC");
        $stmt->execute([$from, $to]);
        $service_summary = $stmt->fetchAll();
        foreach ($service_summary as &$ss) {
            cast_types($ss, ['total' => 'integer', 'revenue' => 'double']);
        }

        $stmt = $pdo->prepare("SELECT COUNT(*) as total, SUM(total_harga) as revenue FROM bookings
            WHERE tgl_booking >= ? AND tgl_booking <= ? AND status IN ('confirmed', 'completed')");
        $stmt->execute([$from, $to]);
        $summary = $stmt->fetch();
        cast_types($summary, ['total' => 'integer', 'revenue' => 'double']);

        return [
            'from' => $from,
            'to' => $to,
            'summary' => $summary,
            'daily' => $report_data,
            'by_service' => $service_summary
        ];
    }
}

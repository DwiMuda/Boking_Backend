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
            'total_room_types' => "SELECT COUNT(*) as total FROM room_types WHERE is_active = 1",
            'total_rooms' => "SELECT COUNT(*) as total FROM rooms WHERE status = 'available'",
            'total_revenue' => "SELECT COALESCE(SUM(total_harga), 0) as total FROM bookings WHERE status IN ('confirmed', 'completed')",
        ];

        foreach ($queries as $key => $sql) {
            $stmt = $pdo->prepare($sql);
            $stmt->execute();
            $result = $stmt->fetch();
            $stats[$key] = in_array($key, ['total_bookings', 'pending_bookings', 'confirmed_bookings', 'completed_bookings', 'total_users', 'total_services', 'total_room_types', 'total_rooms'])
                ? (int)$result['total']
                : (float)$result['total'];
        }

        $stmt = $pdo->prepare("SELECT DATE(COALESCE(tgl_booking, check_in)) as tgl, COUNT(*) as total FROM bookings
            WHERE COALESCE(tgl_booking, check_in) >= CURDATE() AND COALESCE(tgl_booking, check_in) < CURDATE() + INTERVAL 7 DAY
            GROUP BY tgl ORDER BY tgl");
        $stmt->execute();
        $stats['weekly_bookings'] = $stmt->fetchAll();
        foreach ($stats['weekly_bookings'] as &$wb) {
            $wb['total'] = (int)$wb['total'];
        }

        return $stats;
    }

    public static function getReport($pdo, $from, $to) {
        $from = $from ?: '2000-01-01';
        $to = $to ?: date('Y-m-d');

        $stmt = $pdo->prepare("SELECT COALESCE(b.tgl_booking, b.check_in) as tanggal,
            COALESCE(s.nama, rt.nama) as service_nama,
            COUNT(*) as total_booking, SUM(b.total_harga) as total_pendapatan
            FROM bookings b
            LEFT JOIN services s ON b.service_id = s.id
            LEFT JOIN rooms r ON b.room_id = r.id
            LEFT JOIN room_types rt ON r.room_type_id = rt.id
            WHERE COALESCE(b.tgl_booking, b.check_in) >= ? AND COALESCE(b.tgl_booking, b.check_in) <= ? AND b.status IN ('confirmed', 'completed')
            GROUP BY tanggal, COALESCE(s.nama, rt.nama) ORDER BY tanggal ASC");
        $stmt->execute([$from, $to]);
        $report_data = $stmt->fetchAll();
        foreach ($report_data as &$rd) {
            cast_types($rd, ['total_booking' => 'integer', 'total_pendapatan' => 'double']);
        }

        $stmt = $pdo->prepare("SELECT COALESCE(s.nama, rt.nama) as nama, COUNT(*) as total, SUM(b.total_harga) as revenue
            FROM bookings b
            LEFT JOIN services s ON b.service_id = s.id
            LEFT JOIN rooms r ON b.room_id = r.id
            LEFT JOIN room_types rt ON r.room_type_id = rt.id
            WHERE COALESCE(b.tgl_booking, b.check_in) >= ? AND COALESCE(b.tgl_booking, b.check_in) <= ? AND b.status IN ('confirmed', 'completed')
            GROUP BY COALESCE(s.nama, rt.nama) ORDER BY total DESC");
        $stmt->execute([$from, $to]);
        $service_summary = $stmt->fetchAll();
        foreach ($service_summary as &$ss) {
            cast_types($ss, ['total' => 'integer', 'revenue' => 'double']);
        }

        $stmt = $pdo->prepare("SELECT COUNT(*) as total, SUM(total_harga) as revenue FROM bookings
            WHERE COALESCE(tgl_booking, check_in) >= ? AND COALESCE(tgl_booking, check_in) <= ? AND status IN ('confirmed', 'completed')");
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

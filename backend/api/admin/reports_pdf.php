<?php
require_once __DIR__ . '/../../vendor/autoload.php'; // Memuat autoload composer termasuk dompdf
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/auth_middleware.php';
require_once __DIR__ . '/../../services/ReportService.php';

use Dompdf\Dompdf;
use Dompdf\Options;

try {
    require_method('GET');
    require_admin($pdo);
    
    $from = $_GET['from'] ?? '';
    $to = $_GET['to'] ?? '';
    
    // Ambil data laporan dari ReportService
    $result = ReportService::getReport($pdo, $from, $to);
    
    // Siapkan HTML
    $html = '
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="utf-8">
        <title>Laporan Booking</title>
        <style>
            body { font-family: "Helvetica Neue", Helvetica, Arial, sans-serif; font-size: 14px; color: #333; margin: 0; padding: 20px; }
            h2, h3 { color: #2c3e50; margin-bottom: 10px; }
            .header { text-align: center; border-bottom: 2px solid #2c3e50; padding-bottom: 10px; margin-bottom: 20px; }
            .summary { margin-bottom: 30px; }
            .summary-item { display: inline-block; width: 45%; background: #f8f9fa; padding: 15px; border-radius: 5px; margin-right: 2%; box-sizing: border-box; }
            table { width: 100%; border-collapse: collapse; margin-bottom: 30px; }
            th, td { padding: 12px; text-align: left; border-bottom: 1px solid #ddd; }
            th { background-color: #2c3e50; color: white; }
            tr:nth-child(even) { background-color: #f2f2f2; }
            .total { font-weight: bold; }
        </style>
    </head>
    <body>
        <div class="header">
            <h2>Laporan Layanan & Booking</h2>
            <p>Periode: <strong>' . ($from ?: 'Semua Waktu') . '</strong> s/d <strong>' . ($to ?: date('Y-m-d')) . '</strong></p>
        </div>

        <div class="summary">
            <div class="summary-item">
                <h3>Total Booking</h3>
                <p style="font-size: 24px; font-weight: bold; color: #e67e22;">' . number_format($result['summary']['total'], 0, ',', '.') . '</p>
            </div>
            <div class="summary-item">
                <h3>Total Pendapatan</h3>
                <p style="font-size: 24px; font-weight: bold; color: #27ae60;">Rp ' . number_format($result['summary']['revenue'], 0, ',', '.') . '</p>
            </div>
        </div>

        <h3>Rincian Per Layanan/Kamar</h3>
        <table>
            <thead>
                <tr>
                    <th>Nama Layanan / Kamar</th>
                    <th>Jumlah Booking</th>
                    <th>Total Pendapatan</th>
                </tr>
            </thead>
            <tbody>';
            
    if (count($result['by_service']) > 0) {
        foreach ($result['by_service'] as $s) {
            $html .= '
                <tr>
                    <td>' . htmlspecialchars($s['nama']) . '</td>
                    <td>' . number_format($s['total'], 0, ',', '.') . '</td>
                    <td>Rp ' . number_format($s['revenue'], 0, ',', '.') . '</td>
                </tr>';
        }
    } else {
        $html .= '<tr><td colspan="3" style="text-align:center;">Belum ada data untuk periode ini</td></tr>';
    }

    $html .= '
            </tbody>
        </table>

        <h3>Rincian Harian</h3>
        <table>
            <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>Layanan / Kamar</th>
                    <th>Booking</th>
                    <th>Pendapatan</th>
                </tr>
            </thead>
            <tbody>';

    if (count($result['daily']) > 0) {
        foreach ($result['daily'] as $d) {
            $html .= '
                <tr>
                    <td>' . htmlspecialchars($d['tanggal']) . '</td>
                    <td>' . htmlspecialchars($d['service_nama']) . '</td>
                    <td>' . number_format($d['total_booking'], 0, ',', '.') . '</td>
                    <td>Rp ' . number_format($d['total_pendapatan'], 0, ',', '.') . '</td>
                </tr>';
        }
    } else {
        $html .= '<tr><td colspan="4" style="text-align:center;">Belum ada data harian</td></tr>';
    }

    $html .= '
            </tbody>
        </table>
        
        <div style="margin-top: 50px; text-align: right; color: #7f8c8d; font-size: 12px;">
            <p>Dicetak pada: ' . date('Y-m-d H:i:s') . '</p>
        </div>
    </body>
    </html>';

    // Inisialisasi Dompdf
    $options = new Options();
    $options->set('isHtml5ParserEnabled', true);
    $options->set('isRemoteEnabled', true);
    
    $dompdf = new Dompdf($options);
    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'portrait');
    $dompdf->render();
    
    // Output ke browser, Attachment => 1 untuk download langsung, 0 untuk preview di tab baru
    $filename = 'Laporan_Booking_' . $from . '_to_' . $to . '.pdf';
    $dompdf->stream($filename, ["Attachment" => 1]);

} catch (Exception $e) {
    http_response_code($e->getCode() ?: 500);
    echo "Gagal membuat PDF: " . $e->getMessage();
}

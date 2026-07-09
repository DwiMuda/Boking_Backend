<?php
require_once __DIR__ . '/../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

function send_email($to, $subject, $body) {
    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'emailkamu@gmail.com';
        $mail->Password   = 'password aplikasi gmail';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        $mail->setFrom('emailkamu@gmail.com', 'Sistem Booking');
        $mail->addAddress($to);
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $body;

        $mail->send();
        return true;
    } catch (Exception $e) {
        return false;
    }
}

function email_booking_created($user_email, $user_nama, $data) {
    $subject = "Konfirmasi Booking #{$data['id']} - Sistem Booking";
    $body = "
        <h2>Halo $user_nama,</h2>
        <p>Booking kamu berhasil dibuat!</p>
        <table border='1' cellpadding='8' cellspacing='0' style='border-collapse:collapse;width:100%'>
            <tr><td><strong>ID Booking</strong></td><td>#{$data['id']}</td></tr>
            <tr><td><strong>Layanan</strong></td><td>{$data['service_nama']}</td></tr>
            <tr><td><strong>Tanggal</strong></td><td>{$data['tgl_booking']}</td></tr>
            <tr><td><strong>Jam</strong></td><td>{$data['jam_booking']}</td></tr>
            <tr><td><strong>Total</strong></td><td>Rp " . number_format($data['total_harga'], 0, ',', '.') . "</td></tr>
            <tr><td><strong>Status</strong></td><td>Menunggu Konfirmasi</td></tr>
        </table>
        <p>Silakan tunggu konfirmasi dari admin.</p>
    ";
    return send_email($user_email, $subject, $body);
}

function email_status_update($user_email, $user_nama, $booking_id, $status) {
    $label = ['pending' => 'Menunggu', 'confirmed' => 'Dikonfirmasi', 'completed' => 'Selesai', 'cancelled' => 'Dibatalkan'];
    $subject = "Status Booking #{$booking_id} - " . ($label[$status] ?? $status);
    $body = "
        <h2>Halo $user_nama,</h2>
        <p>Status booking <strong>#$booking_id</strong> telah berubah menjadi: <strong>{$label[$status]}</strong></p>
        <p>Silakan cek halaman booking untuk detail lebih lanjut.</p>
    ";
    return send_email($user_email, $subject, $body);
}

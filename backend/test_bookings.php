<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/services/BookingService.php';

try {
    $result = BookingService::getAllBookings($pdo, [], 1);
    print_r($result);
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}

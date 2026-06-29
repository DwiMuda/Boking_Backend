<?php

use App\Controllers\Admin\DashboardController;

$app->register('GET', '/admin', [DashboardController::class, 'index']);
$app->register('GET', '/admin/villa', [DashboardController::class, 'villa']);
$app->register('GET', '/admin/booking', [DashboardController::class, 'booking']);

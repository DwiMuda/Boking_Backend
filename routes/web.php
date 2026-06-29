<?php

use App\Controllers\HomeController;

$app->register('GET', '/', [HomeController::class, 'index']);
$app->register('GET', '/villa/{id}', [HomeController::class, 'detail']);
$app->register('POST', '/booking', [HomeController::class, 'booking']);

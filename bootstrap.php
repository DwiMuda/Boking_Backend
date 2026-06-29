<?php

declare(strict_types=1);

session_start();

require_once __DIR__ . '/vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->safeLoad();

require_once __DIR__ . '/app/Config/database.php';
require_once __DIR__ . '/app/Config/app.php';

use App\Core\ErrorHandler;

ErrorHandler::register();

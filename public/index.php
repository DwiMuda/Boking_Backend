<?php

require_once __DIR__ . '/../bootstrap.php';

use App\Core\App;

$app = new App();

require_once BASE_PATH . '/routes/web.php';
require_once BASE_PATH . '/routes/admin.php';

$app->run();

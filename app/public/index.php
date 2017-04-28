<?php

ini_set('display_errors', 'errorlog');

require_once __DIR__ . "/../bootstrap/app.php";

use Mg\JmesPathServer\ApiApplication;

$apiApplication = new ApiApplication();
$apiApplication->run();


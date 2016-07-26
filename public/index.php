<?php 

define('BASE', dirname(__DIR__));

require BASE . '/bootstrap/autoload.php';

$app = require_once BASE . '/bootstrap/app.php';

$app->run();
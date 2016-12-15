<?php

date_default_timezone_set('America/Sao_Paulo');

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../app/config/environment.php';

$app = new \App\LocalApplication();
$app->run();
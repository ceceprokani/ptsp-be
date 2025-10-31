<?php
declare(strict_types=1);
require __DIR__ . '/../src/App/App.php';

if (strpos($_SERVER['HTTP_HOST'], 'dev') !== false || strpos($_SERVER['HTTP_HOST'], 'localhost') !== false) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}

$app->run();

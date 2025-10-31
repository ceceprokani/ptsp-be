<?php

declare(strict_types=1);

use Symfony\Component\Dotenv\Dotenv;

$baseDir = __DIR__ . '/../../';

$dotenv = new Dotenv();
if (file_exists($baseDir . '.env')) {
    $dotenv->load($baseDir.'.env');
}
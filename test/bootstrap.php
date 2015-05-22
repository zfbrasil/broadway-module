<?php

if (!file_exists(__DIR__ . '/../vendor/autoload.php')) {
    throw new RuntimeException(
        'vendor/autoload.php could not be found. Did you run `composer install`?'
    );
}

require __DIR__ . '/../vendor/autoload.php';

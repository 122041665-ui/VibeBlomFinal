<?php

// Docker injecta variables de desarrollo en $_SERVER. PHPUnit debe fijar el
// entorno antes de cargar Laravel para que CSRF, sesiones y BD sean aislados.
$testingEnvironment = [
    'APP_ENV' => 'testing',
    'CACHE_STORE' => 'array',
    'DB_CONNECTION' => 'sqlite',
    'DB_DATABASE' => ':memory:',
    'MAIL_MAILER' => 'array',
    'QUEUE_CONNECTION' => 'sync',
    'SESSION_DRIVER' => 'array',
];

foreach ($testingEnvironment as $key => $value) {
    putenv("{$key}={$value}");
    $_ENV[$key] = $value;
    $_SERVER[$key] = $value;
}

require dirname(__DIR__).'/vendor/autoload.php';

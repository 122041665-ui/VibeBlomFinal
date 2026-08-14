<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Mailer Predeterminado
    |--------------------------------------------------------------------------
    |
    | Define qué mailer se usará por defecto para enviar correos desde la
    | aplicación. Puedes cambiarlo desde el archivo .env fácilmente.
    |
    */

    // El proyecto no depende de un proveedor SMTP externo. Los correos se
    // registran localmente y nunca bloquean una operación del usuario.
    'default' => 'log',

    /*
    |--------------------------------------------------------------------------
    | Configuración de Mailers
    |--------------------------------------------------------------------------
    |
    | Aquí puedes configurar todos los "mailers" que usará tu aplicación.
    | Los transportes externos quedan disponibles para una integración futura.
    |
    */

    'mailers' => [

        // --- SMTP general de Laravel (no lo quitamos)
        'smtp' => [
            'transport' => 'smtp',
            'host' => env('MAIL_HOST', '127.0.0.1'),
            'port' => env('MAIL_PORT', 2525),
            'username' => env('MAIL_USERNAME'),
            'password' => env('MAIL_PASSWORD'),
            'timeout' => null,
            'local_domain' => env('MAIL_EHLO_DOMAIN', parse_url((string) env('APP_URL', 'http://localhost'), PHP_URL_HOST)),
        ],

        'ses' => [
            'transport' => 'ses',
        ],

        'postmark' => [
            'transport' => 'postmark',
        ],

        'resend' => [
            'transport' => 'resend',
        ],

        'sendmail' => [
            'transport' => 'sendmail',
            'path' => env('MAIL_SENDMAIL_PATH', '/usr/sbin/sendmail -bs -i'),
        ],

        'log' => [
            'transport' => 'log',
            'channel' => env('MAIL_LOG_CHANNEL'),
        ],

        'array' => [
            'transport' => 'array',
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Dirección Global "From"
    |--------------------------------------------------------------------------
    |
    | Esto define el remitente predeterminado para TODOS los correos.
    | Puedes cambiarlo desde .env.
    |
    */

    'from' => [
        'address' => env('MAIL_FROM_ADDRESS', 'no-reply@vibebloom.com'),
        'name' => env('MAIL_FROM_NAME', 'VibeBloom'),
    ],
];

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

    'default' => env('MAIL_MAILER', 'brevo'),

    /*
    |--------------------------------------------------------------------------
    | Configuración de Mailers
    |--------------------------------------------------------------------------
    |
    | Aquí puedes configurar todos los "mailers" que usará tu aplicación.
    | Se agrega un mailer especial para Brevo, usando su servidor SMTP.
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


        // --- Brevo (Sendinblue) — SMTP oficial
        'brevo' => [
            'transport' => 'smtp',
            'host' => 'smtp-relay.brevo.com', // Servidor SMTP de Brevo
            'port' => 587,                     // Puerto recomendado (TLS)
            'username' => env('BREVO_LOGIN'),  // Puede ser tu correo o el que Brevo te indique
            'password' => env('BREVO_API_KEY'),// API Key de Brevo
            'encryption' => 'tls',
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

        'failover' => [
            'transport' => 'failover',
            'mailers' => ['brevo', 'log'],
            'retry_after' => 60,
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
    'brevo' => [
    'transport' => 'smtp',
    'host' => env('MAIL_HOST', 'smtp-relay.brevo.com'),
    'port' => env('MAIL_PORT', 587),
    'username' => env('BREVO_LOGIN'),
    'password' => env('BREVO_API_KEY'),
    'encryption' => env('MAIL_ENCRYPTION', 'tls'),
],

];

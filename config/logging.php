<?php

use Monolog\Handler\NullHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\SyslogUdpHandler;
use Monolog\Processor\PsrLogMessageProcessor;

return [
    'default' => env('LOG_CHANNEL', 'dev'),

    'deprecations' => [
        'channel' => env('LOG_DEPRECATIONS_CHANNEL', 'null'),
        'trace' => false,
    ],

    'channels' => [
        'stack' => [
            'driver' => 'stack',
            'channels' => ['daily'],
            'ignore_exceptions' => false,
        ],

        'daily' => [
            'driver' => 'daily',
            'path' => storage_path('logs/laravel.log'),
            'level' => env('LOG_LEVEL', 'debug'),
            'days' => 1,
            'permission' => 0664,
        ],
        'dev' => [
            'driver' => 'single',
            'path' => storage_path('logs/dev.log'),
            'level' => 'debug',
            'permission' => 0664,
        ],
        'action_log' => [
            'driver' => 'daily',
            'path' => storage_path('logs/actions.log'),
            'level' => 'debug',
            'days' => 14,
            'permission' => 0664,
        ],
        'client_error_log' => [
            'driver' => 'daily',
            'path' => storage_path('logs/client-errors.log'),
            'level' => 'error', 
            'days' => 30,
            'permission' => 0664,
        ],
    ],
];
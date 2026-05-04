<?php

namespace {
    use Knuckles\Scribe\Config\AuthIn;

    return [
        'type' => 'static',
        'static' => [
            'output_path' => 'public/docs',
        ],
        'auth' => [
            'enabled' => false,
            'default_visibility' => true,
            'in' => 'bearer',
            'name' => 'Authorization',
            'use_value' => env('SCRIBE_AUTH_KEY'),
            'placeholder' => '{YOUR_AUTH_KEY}',
            'extra_info' => 'You can retrieve your token by logging in.',
        ],
        'routes' => [
            [
                'match' => [
                    'prefixes' => ['api/*'],
                    'domains' => ['*'],
                ],
                'include' => ['*'],
                'exclude' => [],
                'apply' => [
                    'headers' => [
                        'Content-Type' => 'application/json',
                        'Accept' => 'application/json',
                    ],
                ],
            ],
        ],
        'base_url' => env('APP_URL'),
    ];
}

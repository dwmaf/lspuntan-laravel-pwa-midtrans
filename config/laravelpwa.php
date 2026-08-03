<?php

return [
    'name' => 'LaravelPWA',
    'manifest' => [
        'name' => env('APP_NAME', 'LSP UNTAN'),
        'short_name' => 'LSP UNTAN',
        'start_url' => '/',
        'background_color' => '#ffffff',
        'theme_color' => '#000000',
        'display' => 'standalone',
        'orientation'=> 'portrait',
        'status_bar'=> 'black',
        'icons' => [
            '72x72' => [
                'path' => '/images/icons/icon-72x72.png',
                'purpose' => 'any'
            ],
            '96x96' => [
                'path' => '/images/icons/icon-96x96.png',
                'purpose' => 'any'
            ],
            '128x128' => [
                'path' => '/images/icons/icon-128x128.png',
                'purpose' => 'any'
            ],
            '144x144' => [
                'path' => '/images/icons/icon-144x144.png',
                'purpose' => 'any'
            ],
            '152x152' => [
                'path' => '/images/icons/icon-152x152.png',
                'purpose' => 'any'
            ],
            '192x192' => [
                'path' => '/images/icons/icon-192x192.png',
                'purpose' => 'any'
            ],
            '384x384' => [
                'path' => '/images/icons/icon-384x384.png',
                'purpose' => 'any'
            ],
            '512x512' => [
                'path' => '/images/icons/icon-512x512.png',
                'purpose' => 'any'
            ],
        ],
        'splash' => [
            
        ],
        'shortcuts' => [],
        'screenshots' => [],
        'custom' => [
            'screenshots' => [
                [
                    'src' => '/images/screenshot-desktop.png',
                    'sizes' => '1280x720',
                    'form_factor' => 'wide',
                    'label' => 'Tampilan Desktop LSP UNTAN',
                ],
                [
                    'src' => '/images/screenshot-mobile.png',
                    'sizes' => '720x1280',
                    'label' => 'Tampilan Mobile LSP UNTAN',
                ],
            ],
            'id' => '/',
        ]
    ]
];

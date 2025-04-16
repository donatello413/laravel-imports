<?php

declare(strict_types=1);

return [
    'website_config' => [
        'url' => env('WEBSITE_IMPORT_URL'),
        'key' => env('WEBSITE_IMPORT_KEY'),
    ],

    'import_entities' => [
        'categories' => [
//            'dto' => CategoryResponseDto::class,
//            'endpoint' => 'categories',
//            'event' => CategoryImportEvent::class,
        ],
    ],
];

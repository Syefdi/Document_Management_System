<?php

return [

    'driver' => env('SCOUT_DRIVER', 'tntsearch'),

    'tntsearch' => [
        'storage'  => storage_path('app/search_index'),
        'fuzziness' => env('TNTSEARCH_FUZZINESS', false),
        'fuzzy' => [
            'prefix_length' => 2,
            'max_expansions' => 50,
            'distance' => 2
        ],
        'asYouType' => false,
    ],
];

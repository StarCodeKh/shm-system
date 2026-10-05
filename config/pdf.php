<?php

/*
|--------------------------------------------------------------------------
| PDF (mPDF) settings
|--------------------------------------------------------------------------
| Fonts are read from the same folder the web app uses (resources/assets/fonts).
| mPDF font keys must be lowercase. Khmer needs 'useOTL' => 0xFF for correct shaping.
*/

return [
    'temp_dir' => storage_path('temp'),

    'font_dir' => resource_path('assets/fonts'),

    'default_font' => 'battambang',

    'fonts' => [
        'battambang' => [
            'R' => 'KhmerOSBattambang.ttf',
            'useOTL' => 0xFF,
        ],
    ],
];

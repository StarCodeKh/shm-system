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
        // Headings of official documents: national motto, organisation, report title.
        // Google's Moul (SIL OFL) plus an empty zero-width U+200B glyph: mPDF inserts U+200B
        // between Khmer words, and the original font draws it as a box.
        'moul' => [
            'R' => 'Moul-zwsp.ttf',
            'useOTL' => 0xFF,
        ],
    ],
];

<?php

/*
|--------------------------------------------------------------------------
| Report settings (on-screen viewer + PDF export)
|--------------------------------------------------------------------------
| Texts live in resources/lang/{kh,en}/report.php; organisation name, logo
| and footer come from the system settings (configuration table).
*/

return [
    // PDF export row cap; the viewer pages through everything
    'max_rows' => 20000,

    // Minutes a built PDF is reused when the same report is printed or downloaded again
    // with unchanged data (building a large PDF takes several seconds)
    'pdf_cache_minutes' => 10,

    // On-screen page sizes
    'page_sizes' => [25, 50, 100],
    'default_page_size' => 25,

    // PDFs with this many columns or fewer print in portrait, wider ones in landscape
    'portrait_max_columns' => 6,

    // Column-name patterns (regex) that decide formatting
    'columns' => [
        'date' => '/(^|_)(date|at)$|^dob$/',
        // Numbers that are identifiers or codes: shown as-is, never formatted or totalled
        'not_numeric' => '/(^|_)(id|no|code|phone|tel|telephone_\d|status|type|gender)$/',
        // Money columns get a totals row in the PDF
        'money' => '/amount|total|price|balance|salary|fee|cost|paid|budget|ceiling/i',
    ],

    // Accent colour of printed list pages (reports have their own colour)
    'default_color' => '#28C76F',

    // Print button of list pages: most rows it prints (the list's search caps a page at 1000)
    'print_max_rows' => 1000,

    // Fallback logo when the settings have none
    'default_logo' => 'images/logo.png',

    // PDF letterhead: the national motto above the organisation, as on official documents
    'national_header' => true,

    // Signature block under the table. Roles print left to right (labels in report.php → signatures);
    // the place and date line sits above the last one. An empty list hides the block.
    'signature' => [
        'roles' => ['approved_by', 'checked_by', 'prepared_by'],
        'place' => env('REPORT_SIGN_PLACE', ''),
    ],
];

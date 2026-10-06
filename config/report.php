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

    // Fallback logo when the settings have none
    'default_logo' => 'images/logo.png',
];

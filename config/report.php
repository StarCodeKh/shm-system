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
    'default_logo' => 'images/logo/logo.png',

    // Text sizes (pt) of the printed documents, one place for all of them.
    // PDF uses them as they are; Excel (read on screen) multiplies them by excel_scale.
    'typography' => [
        'sizes' => [
            'body' => 8.5,
            'kingdom' => 11,         // ព្រះរាជាណាចក្រកម្ពុជា
            'motto' => 9.5,          // ជាតិ សាសនា ព្រះមហាក្សត្រ
            'org' => 9,              // organisation name
            'system' => 7.8,         // system name under it
            'title' => 14,
            'subtitle' => 8.5,
            'info_label' => 7,       // info bar: "printed by", ...
            'info_value' => 8.5,
            'summary_label' => 7.5,
            'summary_value' => 15,
            'th' => 7.8,             // table header
            'td' => 8,               // table cells and totals
            'empty' => 10,
            'empty_hint' => 8,
            'note' => 7.8,
            'date_line' => 8,
            'signature' => 9,
            'footer' => 6.8,
        ],
        // Wide tables get smaller table text (th / td) and tighter cells so the columns fit:
        // up to N columns => factor. Letterhead, title and footer keep their size.
        'table_scale' => [6 => 1.0, 9 => 0.94, 12 => 0.86, 16 => 0.78, 1000 => 0.7],
        'excel_scale' => 1.2,
    ],

    // Excel font. Arial is a system font on Windows and Mac (and mapped in LibreOffice / Google Sheets), so
    // Latin text and numbers look the same in Excel, Numbers and file previews; Khmer letters are drawn by the
    // system's own Khmer font (Khmer UI on Windows, Khmer Sangam MN on Mac). Calibri exists only inside Office
    // on a Mac, and 'Khmer OS Battambang' only where it is installed: elsewhere both fall back to a serif font.
    'excel_font' => env('REPORT_EXCEL_FONT', 'Arial'),

    // PDF letterhead: the national motto above the organisation, as on official documents
    'national_header' => true,

    // Signature block under the table. Roles print left to right (labels in report.php → signatures);
    // the place and date line sits above the last one. An empty list hides the block.
    'signature' => [
        'roles' => ['approved_by', 'checked_by', 'prepared_by'],
        'place' => env('REPORT_SIGN_PLACE', ''),
    ],
];

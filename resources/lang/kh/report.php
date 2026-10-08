<?php

return [
    // Labels used by the viewer and the PDF
    'printed_at' => 'កាលបរិច្ឆេទបោះពុម្ព',
    'printed_by' => 'បោះពុម្ពដោយ',
    'rows' => 'ចំនួនជួរ',
    'total' => 'ចំនួនសរុប',
    'total_row' => 'សរុប',
    'no_data' => 'មិនមានទិន្នន័យ',
    'truncated' => 'បង្ហាញតែ :count ជួរដំបូងប៉ុណ្ណោះ។ សូមប្រើតម្រងដើម្បីបង្រួមលទ្ធផល។',
    'page' => 'ទំព័រ',
    'error' => 'មិនអាចបង្កើតរបាយការណ៍បានទេ សូមព្យាយាមម្តងទៀត។',

    // PDF letterhead, info bar and signature block
    'kingdom' => 'ព្រះរាជាណាចក្រកម្ពុជា',
    'motto' => 'ជាតិ សាសនា ព្រះមហាក្សត្រ',
    'records' => 'ចំនួនកំណត់ត្រា',
    'filters' => 'តម្រង',
    'no_filter' => 'ទិន្នន័យទាំងអស់',
    'no_data_hint' => 'មិនមានទិន្នន័យត្រូវនឹងលក្ខខណ្ឌដែលបានជ្រើសរើសទេ',
    'date_line' => ':place ថ្ងៃទី:day ខែ:month ឆ្នាំ:year',
    'months' => ['មករា', 'កុម្ភៈ', 'មីនា', 'មេសា', 'ឧសភា', 'មិថុនា', 'កក្កដា', 'សីហា', 'កញ្ញា', 'តុលា', 'វិច្ឆិកា', 'ធ្នូ'],
    'signatures' => [
        'approved_by' => 'បានឃើញ និងឯកភាព',
        'checked_by' => 'អ្នកត្រួតពិនិត្យ',
        'prepared_by' => 'អ្នករៀបចំ',
    ],

    // Report catalogue: title, description and summary labels per report key
    'types' => [
        'officers' => [
            'title' => 'របាយការណ៍សមាជិក',
            'description' => 'បញ្ជីសមាជិកទាំងអស់ រួមទាំងចំនួនប្រុស និងស្រី',
            'summary' => ['total' => 'សមាជិកសរុប', 'male' => 'ប្រុស', 'female' => 'ស្រី'],
        ],
        'income' => [
            'title' => 'របាយការណ៍ចំណូល',
            'description' => 'ចំណូលទាំងអស់តាមគណនី និងអង្គភាព',
        ],
        'expense' => [
            'title' => 'របាយការណ៍ចំណាយ',
            'description' => 'ចំណាយទាំងអស់តាមគណនី និងអង្គភាព',
        ],
        'officer_incentive' => [
            'title' => 'របាយការណ៍បើកផ្តល់ប្រាក់ឧបត្ថម្ភ',
            'description' => 'ប្រាក់ឧបត្ថម្ភដែលបានផ្តល់ជូនសមាជិក',
        ],
        'officers_payment' => [
            'title' => 'របាយការណ៍បង់ប្រាក់',
            'description' => 'សមាជិកដែលបានបង់ប្រាក់រួច',
        ],
        'officers_payable' => [
            'title' => 'របាយការណ៍មិនទាន់បង់ប្រាក់',
            'description' => 'សមាជិកដែលមិនទាន់បង់ប្រាក់',
        ],
        'officers_request' => [
            'title' => 'របាយការណ៍សំណើ',
            'description' => 'សំណើរបស់សមាជិក',
        ],
    ],
];

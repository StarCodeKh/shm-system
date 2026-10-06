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

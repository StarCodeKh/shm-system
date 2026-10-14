<?php

// tbl_letter_type: Letter types of incentives and requests.
// Columns as in its import template. Matched on name_kh; php artisan db:seed-data --tables=letter_type writes this file.
return [
    ['id' => 1, 'name_en' => 'លិខិតផ្ទៃក្នុង', 'name_kh' => 'លិខិតផ្ទៃក្នុង', 'status' => 13, 'order_level' => 1],
    ['id' => 2, 'name_en' => 'លិខិតខាងក្រៅ', 'name_kh' => 'លិខិតខាងក្រៅ', 'status' => 13, 'order_level' => 2],
    ['id' => 3, 'name_en' => 'លិខិតផ្លាស់ប្តូរសមាសភាព', 'name_kh' => 'លិខិតផ្លាស់ប្តូរសមាសភាព', 'status' => 13, 'order_level' => 3],
    ['id' => 4, 'name_en' => 'លិខិតតែងតាំង', 'name_kh' => 'លិខិតតែងតាំង', 'status' => 13, 'order_level' => 4],
    ['id' => 5, 'name_en' => 'លិខិតសរសើរ', 'name_kh' => 'លិខិតសរសើរ', 'status' => 13, 'order_level' => 5],
    ['id' => 6, 'name_en' => 'លិខិតបើកផ្តល់ប្រាក់ឧបត្ថម្ភ', 'name_kh' => 'លិខិតបើកផ្តល់ប្រាក់ឧបត្ថម្ភ', 'status' => 13, 'order_level' => 6],
];

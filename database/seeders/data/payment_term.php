<?php

// tbl_payment_term: Contribution schedules; the default one is given to each new member.
// Columns as in its import template. Matched on type; php artisan db:seed-data --tables=payment_term writes this file.
return [
    ['id' => 1, 'type' => 'Yearly', 'name_kh' => 'បង់ប្រចាំឆ្នាំ', 'name_en' => 'Yearly Payment', 'execute_number' => 1, 'recurring_amount' => 60000, 'order_level' => 1, 'currency_id' => 1, 'status' => 13, 'is_default' => 1],
    ['id' => 2, 'type' => 'Monthly', 'name_kh' => 'បង់ប្រចាំខែ', 'name_en' => 'Monthly Payment', 'execute_number' => 12, 'recurring_amount' => 5000, 'order_level' => 2, 'currency_id' => 1, 'status' => 13, 'is_default' => 0],
];

<?php

// tbl_currency: Currencies: KHR (the default) and USD.
// Columns as in its import template. Matched on abbreviation; php artisan db:seed-data --tables=currency writes this file.
return [
    ['id' => 1, 'name_en' => 'Riel', 'name_kh' => 'រៀល', 'abbreviation' => 'KHR', 'symbol_left' => '៛', 'is_default' => 1, 'status' => 13, 'order_level' => 1],
    ['id' => 2, 'name_en' => 'Dollar', 'name_kh' => 'ដុល្លារ', 'abbreviation' => 'USD', 'symbol_left' => '$', 'is_default' => 0, 'status' => 13, 'order_level' => 2],
];

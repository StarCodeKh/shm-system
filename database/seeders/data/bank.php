<?php

// tbl_bank: Banks of the member's bank account.
// Columns as in its import template. Matched on name_kh; php artisan db:seed-data --tables=bank writes this file.
return [
    ['id' => 1, 'name_en' => 'ACELIDA', 'name_kh' => 'ធនាគារអេស៊ីលីដាភីអិលស៊ី', 'order_level' => 1, 'status' => 13],
    ['id' => 2, 'name_en' => 'Canadia', 'name_kh' => 'ធនាគារកាណាឌីយ៉ា', 'order_level' => 2, 'status' => 13],
    ['id' => 3, 'name_en' => 'Wing', 'name_kh' => 'ធនាគារវីង', 'code' => '008', 'order_level' => 3, 'status' => 13],
    ['id' => 4, 'name_en' => 'ABA', 'name_kh' => 'ធនាគារអេប៊ីអេ(ABA)', 'order_level' => 4, 'status' => 13],
    ['id' => 5, 'name_en' => 'GDNT', 'name_kh' => 'រតនាគារជាតិ(GDNT)', 'order_level' => 5, 'status' => 13],
];

<?php

// tbl_payment_method: How a member pays.
// Columns as in its import template. Matched on name_kh; php artisan db:seed-data --tables=payment_method writes this file.
return [
    ['id' => 1, 'name_kh' => 'បង់តាមរយៈធនាគារជាតិនៃកម្ពុជា', 'name_en' => 'National Bank of Cambodia', 'status' => 13, 'order_level' => 1],
    ['id' => 2, 'name_kh' => 'បង់តាមរយៈរតនាគាជាតិ', 'name_en' => 'National Treasury', 'status' => 13, 'order_level' => 2],
    ['id' => 3, 'name_kh' => 'បង់តាមធនាគារអេស៊ីលីដា', 'name_en' => 'ACLEDA Bank', 'status' => 13, 'order_level' => 3],
];

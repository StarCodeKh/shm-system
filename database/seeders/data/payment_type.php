<?php

// tbl_payment_type: Benefits paid to members, in riel.
// Columns as in its import template. Matched on name_kh; php artisan db:seed-data --tables=payment_type writes this file.
return [
    ['id' => 1, 'name_en' => 'Death of a member\'s legal spouse', 'name_kh' => 'សហព័ទ្ធស្របច្បាប់របស់សមាជិកមរណភាព', 'amount' => 2500000, 'currency_id' => 1, 'order_level' => 1, 'status' => 13],
    ['id' => 2, 'name_en' => 'Death of a member couple (both spouses are members)', 'name_kh' => 'សហព័ទ្ធទាំងពីរជាសមាជិកមរណភាព', 'amount' => 7000000, 'currency_id' => 1, 'order_level' => 2, 'status' => 13],
    ['id' => 3, 'name_en' => 'Death of a member\'s child or parent', 'name_kh' => 'កូន ឪពុក ម្តាយបង្កើតរបស់សមាជិកទទួលមរណភាព', 'amount' => 1000000, 'currency_id' => 1, 'order_level' => 3, 'status' => 13],
    ['id' => 4, 'name_en' => 'Childbirth', 'name_kh' => 'សម្រាប់សមាជិកដែលសម្រាលកូន', 'amount' => 600000, 'currency_id' => 1, 'order_level' => 4, 'status' => 13],
    ['id' => 5, 'name_en' => 'Retirement', 'name_kh' => 'សម្រាប់សមាជិកដែលចូលនិវត្តន៍', 'amount' => 600000, 'currency_id' => 1, 'order_level' => 5, 'status' => 13],
    ['id' => 6, 'name_en' => 'Serious or chronic illness', 'name_kh' => 'សម្រាប់សមាជិកដែលមានជំងឺធ្ងន់ធ្វរនិងរ៉ាំរ៉ៃ', 'amount' => 500000, 'currency_id' => 1, 'order_level' => 6, 'status' => 13],
    ['id' => 7, 'name_en' => 'Accident causing disability', 'name_kh' => 'សម្រាប់សមាជិកដែលទទួលរងគ្រោះថ្នាក់រហូតដល់ពិការ', 'amount' => 1000000, 'currency_id' => 1, 'order_level' => 7, 'status' => 13],
    ['id' => 8, 'name_en' => 'House destroyed by fire', 'name_kh' => 'សម្រាប់ករណីភ្លើងឆេះផ្ទះអស់', 'amount' => 4000000, 'currency_id' => 1, 'order_level' => 8, 'status' => 13],
    ['id' => 9, 'name_en' => 'Leaving work after losing professional capacity', 'name_kh' => 'សមាជិកដែលត្រូវចាកចេញពីការងារដោយមូលហេតុបាត់បង់សម្បទាវិជ្ជាជីវៈ', 'amount' => 4000000, 'currency_id' => 1, 'order_level' => 9, 'status' => 13],
    ['id' => 10, 'name_en' => 'Death of a member', 'name_kh' => 'សមាជិកទទួលមរណភាព', 'amount' => 4500000, 'currency_id' => 1, 'order_level' => 10, 'status' => 13],
];

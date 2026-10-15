<?php

// tbl_account: Chart of accounts, from the old SHM system's categories (contributions and benefit types).
// Columns as in its import template. Matched on code; php artisan db:seed-data --tables=account writes this file.
return [
    ['account_type' => 'INC', 'code' => '4000', 'name_en' => 'Member contributions', 'name_kh' => 'វិភាគទានសមាជិក', 'order_level' => 1, 'status' => 13],
    ['account_type' => 'INC', 'parent_code' => '4000', 'code' => '4100', 'name_en' => 'Yearly contribution', 'name_kh' => 'វិភាគទានប្រចាំឆ្នាំ', 'order_level' => 2, 'status' => 13],
    ['account_type' => 'INC', 'parent_code' => '4000', 'code' => '4200', 'name_en' => 'Member registration fee', 'name_kh' => 'ថ្លៃចុះឈ្មោះសមាជិក', 'order_level' => 3, 'status' => 13],
    ['account_type' => 'EXP', 'code' => '5000', 'name_en' => 'Benefits paid to members', 'name_kh' => 'ប្រាក់ឧបត្ថម្ភសមាជិក', 'order_level' => 4, 'status' => 13],
    ['account_type' => 'EXP', 'parent_code' => '5000', 'code' => '5101', 'name_en' => 'Death of a member\'s legal spouse', 'name_kh' => 'សហព័ទ្ធស្របច្បាប់របស់សមាជិកមរណភាព', 'order_level' => 5, 'status' => 13],
    ['account_type' => 'EXP', 'parent_code' => '5000', 'code' => '5102', 'name_en' => 'Death of a member couple (both spouses are members)', 'name_kh' => 'សហព័ទ្ធទាំងពីរជាសមាជិកមរណភាព', 'order_level' => 6, 'status' => 13],
    ['account_type' => 'EXP', 'parent_code' => '5000', 'code' => '5103', 'name_en' => 'Death of a member\'s child or parent', 'name_kh' => 'កូន ឪពុក ម្តាយបង្កើតរបស់សមាជិកទទួលមរណភាព', 'order_level' => 7, 'status' => 13],
    ['account_type' => 'EXP', 'parent_code' => '5000', 'code' => '5104', 'name_en' => 'Childbirth', 'name_kh' => 'សម្រាប់សមាជិកដែលសម្រាលកូន', 'order_level' => 8, 'status' => 13],
    ['account_type' => 'EXP', 'parent_code' => '5000', 'code' => '5105', 'name_en' => 'Retirement', 'name_kh' => 'សម្រាប់សមាជិកដែលចូលនិវត្តន៍', 'order_level' => 9, 'status' => 13],
    ['account_type' => 'EXP', 'parent_code' => '5000', 'code' => '5106', 'name_en' => 'Serious or chronic illness', 'name_kh' => 'សម្រាប់សមាជិកដែលមានជំងឺធ្ងន់ធ្វរនិងរ៉ាំរ៉ៃ', 'order_level' => 10, 'status' => 13],
    ['account_type' => 'EXP', 'parent_code' => '5000', 'code' => '5107', 'name_en' => 'Accident causing disability', 'name_kh' => 'សម្រាប់សមាជិកដែលទទួលរងគ្រោះថ្នាក់រហូតដល់ពិការ', 'order_level' => 11, 'status' => 13],
    ['account_type' => 'EXP', 'parent_code' => '5000', 'code' => '5108', 'name_en' => 'House destroyed by fire', 'name_kh' => 'សម្រាប់ករណីភ្លើងឆេះផ្ទះអស់', 'order_level' => 12, 'status' => 13],
    ['account_type' => 'EXP', 'parent_code' => '5000', 'code' => '5109', 'name_en' => 'Leaving work after losing professional capacity', 'name_kh' => 'សមាជិកដែលត្រូវចាកចេញពីការងារដោយមូលហេតុបាត់បង់សម្បទាវិជ្ជាជីវៈ', 'order_level' => 13, 'status' => 13],
    ['account_type' => 'EXP', 'parent_code' => '5000', 'code' => '5110', 'name_en' => 'Death of a member', 'name_kh' => 'សមាជិកទទួលមរណភាព', 'order_level' => 14, 'status' => 13],
];

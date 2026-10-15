<?php

// tbl_account_type: Account types of the chart of accounts (account.account_type).
// Columns as in its import template. Matched on type_code; php artisan db:seed-data --tables=account_type writes this file.
return [
    ['type_code' => 'INC', 'name_en' => 'Income', 'name_kh' => 'ចំណូល', 'order_level' => 1, 'status' => 13],
    ['type_code' => 'EXP', 'name_en' => 'Expense', 'name_kh' => 'ចំណាយ', 'order_level' => 2, 'status' => 13],
];

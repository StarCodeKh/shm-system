<?php

// tbl_taxable_salary: Salary tax brackets incentives are taxed with.
// Columns as in its import template. Matched on taxable_salary_monthly_from; php artisan db:seed-data --tables=taxable_salary writes this file.
return [
    ['id' => 1, 'taxable_salary_monthly_from' => 0, 'taxable_salary_monthly_to' => 1300000, 'rate' => 0, 'currency_symbol' => '៛', 'over_class_tax' => 0, 'title' => 'ភាគបៀវត្សជាប់ពន្ធចន្លោះពី 0 - 1300000', 'order_level' => 1],
    ['id' => 2, 'taxable_salary_monthly_from' => 1300001, 'taxable_salary_monthly_to' => 2000000, 'rate' => 5, 'currency_symbol' => '៛', 'over_class_tax' => 60000, 'title' => 'ភាគបៀវត្សជាប់ពន្ធចន្លោះពី 1300001 - 2000000', 'order_level' => 2],
    ['id' => 3, 'taxable_salary_monthly_from' => 2000001, 'taxable_salary_monthly_to' => 8500000, 'rate' => 10, 'currency_symbol' => '៛', 'over_class_tax' => 160000, 'title' => 'ភាគបៀវត្សជាប់ពន្ធចន្លោះពី 2000001 - 8500000', 'order_level' => 3],
    ['id' => 4, 'taxable_salary_monthly_from' => 8500001, 'taxable_salary_monthly_to' => 12500000, 'rate' => 15, 'currency_symbol' => '៛', 'over_class_tax' => 585000, 'title' => 'ភាគបៀវត្សជាប់ពន្ធចន្លោះពី 8500001 - 12500000', 'order_level' => 4],
    ['id' => 5, 'taxable_salary_monthly_from' => 12500001, 'taxable_salary_monthly_to' => 12500001, 'rate' => 20, 'currency_symbol' => '៛', 'over_class_tax' => 1210000, 'title' => 'ភាគបៀវត្សជាប់ពន្ធចន្លោះពី 12500001 - n', 'order_level' => 5],
];

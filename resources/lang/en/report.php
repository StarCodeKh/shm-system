<?php

return [
    // Labels used by the viewer and the PDF
    'printed_at' => 'Printed',
    'printed_by' => 'Printed by',
    'rows' => 'Rows',
    'total' => 'Total',
    'total_row' => 'Total',
    'no_data' => 'No data',
    'truncated' => 'Showing the first :count rows only. Use filters to narrow the result.',
    'page' => 'Page',
    'error' => 'Could not build the report. Please try again.',

    // Report catalogue: title, description and summary labels per report key
    'types' => [
        'officers' => [
            'title' => 'Members report',
            'description' => 'All members, with the number of men and women',
            'summary' => ['total' => 'Total members', 'male' => 'Male', 'female' => 'Female'],
        ],
        'income' => [
            'title' => 'Income report',
            'description' => 'All income by account and entity',
        ],
        'expense' => [
            'title' => 'Expense report',
            'description' => 'All expenses by account and entity',
        ],
        'officer_incentive' => [
            'title' => 'Incentive report',
            'description' => 'Incentives paid to members',
        ],
        'officers_payment' => [
            'title' => 'Payments report',
            'description' => 'Members who have paid',
        ],
        'officers_payable' => [
            'title' => 'Unpaid report',
            'description' => 'Members who have not paid yet',
        ],
        'officers_request' => [
            'title' => 'Requests report',
            'description' => 'Requests from members',
        ],
    ],
];

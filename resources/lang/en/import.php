<?php

// Import preview (App\Http\Controllers\ImportController)
return [
    'unreadable' => 'This file can\'t be read. Use an Excel (.xlsx, .xls) or CSV file.',
    'empty' => 'The file has no data: row 1 holds the column names, data starts on row 2.',
    'required' => 'A value is required',
    'not_number' => 'Must be a number',
    'not_date' => 'Not a valid date',
    'not_in_list' => 'Not one of the choices',
    'not_found' => 'Not found in :table',
    'duplicate_row' => 'Same as row :line',
    'exists' => 'Already exists: :record',
    'link' => 'Fill in :id to update that record',
    'failed' => 'The import stopped on a row the table could not take, so nothing was saved. Check the file and try again.',
    'not_supported' => "This list can't be imported from a file yet.",
];

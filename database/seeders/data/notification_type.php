<?php

// tbl_notification_type: Types a notification is created with.
// Columns as in its import template. Matched on notification_type; php artisan db:seed-data --tables=notification_type writes this file.
return [
    ['id' => 1, 'notification_type' => 'Announcement', 'status' => 13, 'order_level' => 1],
    ['id' => 2, 'notification_type' => 'News', 'status' => 13, 'order_level' => 2],
    ['id' => 3, 'notification_type' => 'Incentive', 'status' => 13, 'order_level' => 3],
];

<?php

/*
|--------------------------------------------------------------------------
| Feature access
|--------------------------------------------------------------------------
| Which permission (spatie permission name) opens each feature. The same names are
| sent to the app (role_menus) so screens can hide what the API would refuse.
| Change a feature's guard here instead of in controllers.
*/

return [
    // Role that always has every permission, including ones added later (checked by name)
    'super_admin_role' => 'Super Admin',

    // Report catalogue, on-screen viewer and PDF export (each report also needs its module's list permission)
    'reports' => 'report_sale',

    // Settings › General: see / save the system settings; Settings › Backup
    'setting_view' => 'list_setting',
    'setting_update' => 'update_setting',
    'setting_backup' => 'backup_setting',

    // Settings › Notifications: see / save the notification switches (config_noti_* settings)
    'setting_notifications_view' => 'list_notifications',
    'setting_notifications_update' => 'update_notifications',

    // Settings › Audit log
    'audit' => 'permission_roles',

    // Settings › Users & roles: change which permissions a role has
    'role_permissions' => 'permission_roles',

    // File manager: browse / upload
    'file_manager_list' => 'list_file_manager',
    'file_manager_upload' => 'create_file_manager',
];

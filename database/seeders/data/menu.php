<?php

// The sidebar menu as it was in navMenuItems.js, plus Administration › Menus.
// [type, slug, name, i18n, icon, url, permission, children]
return [
    ['link', 'dashboard', 'Dashboard', 'Dashboard', 'HomeIcon', '/dashboard', null],

    ['section', 'section-system', 'System', 'menu_system', null, null, null, [
        ['group', 'group-members', 'Members', 'menu_members', 'UsersIcon', null, null, [
            ['link', 'data-officer-list', 'Officer List', 'OfficerList', 'ListIcon', '/officer', 'list_officer'],
            ['link', 'data-officer-form', 'Officer Form', 'officerForm', 'UserPlusIcon', '/officer/form', 'create_officer'],
            ['link', 'data-notification-list', 'Notifications', 'NotificationList', 'BellIcon', '/nofication', 'list_notifications'],
        ]],
        ['group', 'group-transactions', 'Transactions', 'Transactions', 'DollarSignIcon', null, null, [
            ['link', 'account-income', 'Income', 'Income', 'TrendingUpIcon', '/account/income', 'list_income'],
            ['link', 'account-expense', 'Expense', 'Expense', 'TrendingDownIcon', '/account/expense', 'list_expense'],
            ['link', 'account-chart-account', 'Chart of Accounts', 'chart_account', 'BookOpenIcon', '/account/chart-account', 'list_account'],
            ['link', 'account-officer-payment', 'Payments', 'paymentList', 'CheckCircleIcon', '/account/officer-payment', 'list_officer_payable'],
            ['link', 'account-officer-payable-form', 'Payable Form', 'repayForm', 'CreditCardIcon', '/account/officer-payable-form', 'create_officer_payable'],
            ['link', 'data-incentive-list', 'Incentives', 'IncentiveList', 'GiftIcon', '/incentive', 'list_officer_incentive'],
            ['link', 'data-request', 'Requests', 'requestList', 'InboxIcon', '/request', 'list_officer_request'],
        ]],
    ]],

    ['section', 'section-admin', 'Administration', 'menu_admin', null, null, null, [
        ['group', 'group-user', 'User', 'User', 'ShieldIcon', null, null, [
            ['link', 'app-user-list', 'Users', 'UserList', 'UserIcon', '/user', 'list_users'],
            ['link', 'app-user-role', 'Roles', 'RoleList', 'KeyIcon', '/role', 'list_roles'],
            ['link', 'app-menu', 'Menus', 'menu_manage', 'MenuIcon', '/menu', 'list_menu'],
            ['link', 'app-auditrail', 'Audit Trail', 'auditrail', 'ActivityIcon', '/auditrail', 'permission_roles'],
        ]],
        ['link', 'file-manager', 'File Manager', 'FileManager', 'FolderIcon', '/file-manager', 'list_file_manager'],
        ['link', 'report', 'Reports', 'Report', 'BarChart2Icon', '/report', 'report_sale'],
    ]],
];

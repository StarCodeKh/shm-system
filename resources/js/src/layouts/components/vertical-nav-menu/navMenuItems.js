/*=========================================================================================
  File Name: navMenuItems.js
  Description: Sidebar menu.

  Item fields:
    url         route path (items) — omit for groups
    name        unique name, also the fallback label
    slug        unique key, used to highlight the active item
    i18n        translation key for the label
    icon        feather icon name (https://feathericons.com), e.g. "UsersIcon"
    permission  permission the user needs to see the item (from /api/v1/role_menu/permission/index).
                Omit to show the item to everyone.
    submenu     child items (makes the entry a collapsible group)
    header      starts a section; its `items` are listed under it

  Groups and sections with no visible items are hidden automatically.
==========================================================================================*/

export default [
  {
    url: "/dashboard",
    name: "Dashboard",
    slug: "dashboard",
    i18n: "Dashboard",
    icon: "HomeIcon",
  },

  // ---------------------------------------------------------------- System
  {
    header: "System",
    i18n: "មុខងារប្រព័ន្ធ",
    items: [
      {
        name: "Members",
        i18n: "គ្រប់គ្រងសមាជិក",
        icon: "UsersIcon",
        submenu: [
          {
            url: "/officer",
            name: "Officer List",
            slug: "data-officer-list",
            i18n: "OfficerList",
            icon: "ListIcon",
            permission: "list_officer",
          },
          {
            url: "/officer/form",
            name: "Officer Form",
            slug: "data-officer-form",
            i18n: "officerForm",
            icon: "UserPlusIcon",
            permission: "create_officer",
          },
          {
            url: "/nofication",
            name: "Notifications",
            slug: "data-notification-list",
            i18n: "NotificationList",
            icon: "BellIcon",
            permission: "list_notifications",
          },
        ],
      },
      {
        name: "Transactions",
        i18n: "Transactions",
        icon: "DollarSignIcon",
        submenu: [
          {
            url: "/account/income",
            name: "Income",
            slug: "account-income",
            i18n: "Income",
            icon: "TrendingUpIcon",
            permission: "list_income",
          },
          {
            url: "/account/expense",
            name: "Expense",
            slug: "account-expense",
            i18n: "Expense",
            icon: "TrendingDownIcon",
            permission: "list_expense",
          },
          {
            url: "/account/chart-account",
            name: "Chart of Accounts",
            slug: "account-chart-account",
            i18n: "chart_account",
            icon: "BookOpenIcon",
            permission: "list_account",
          },
          {
            url: "/account/officer-payment",
            name: "Payments",
            slug: "account-officer-payment",
            i18n: "paymentList",
            icon: "CheckCircleIcon",
            permission: "list_officer_payment",
          },
          {
            url: "/account/officer-payable-form",
            name: "Payable Form",
            slug: "account-officer-payable-form",
            i18n: "repayForm",
            icon: "CreditCardIcon",
            permission: "list_officer_payable",
          },
          {
            url: "/incentive",
            name: "Incentives",
            slug: "data-incentive-list",
            i18n: "IncentiveList",
            icon: "GiftIcon",
            permission: "list_officer_incentive",
          },
          {
            url: "/request",
            name: "Requests",
            slug: "data-request",
            i18n: "requestList",
            icon: "InboxIcon",
            permission: "list_officer_request",
          },
        ],
      },
    ],
  },

  // ---------------------------------------------------------------- Administration
  {
    header: "Administration",
    i18n: "រដ្ឋបាល",
    items: [
      {
        name: "User",
        i18n: "User",
        icon: "ShieldIcon",
        submenu: [
          {
            url: "/user",
            name: "Users",
            slug: "app-user-list",
            i18n: "UserList",
            icon: "UserIcon",
            permission: "list_users",
          },
          {
            url: "/role",
            name: "Roles",
            slug: "app-user-role",
            i18n: "RoleList",
            icon: "KeyIcon",
            permission: "list_roles",
          },
          {
            url: "/auditrail",
            name: "Audit Trail",
            slug: "app-auditrail",
            i18n: "auditrail",
            icon: "ActivityIcon",
          },
        ],
      },
      {
        url: "/file-manager",
        name: "File Manager",
        slug: "file-manager",
        i18n: "FileManager",
        icon: "FolderIcon",
        permission: "list_file_manager",
      },
      {
        url: "/report",
        name: "Reports",
        slug: "report",
        i18n: "Report",
        icon: "BarChart2Icon",
        permission: "report_sale",
      },
    ],
  },
]

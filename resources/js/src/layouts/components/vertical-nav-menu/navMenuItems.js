export default [
  {
    url: null,
    name: "Dashboard",
    tagColor: "warning",
    icon: "HomeIcon",
    i18n: "Dashboard",
    url: '/dashboard',
    slug: "dashboard",
  },
  // =============================================================================
  // Program Management - គ្រប់គ្រងកម្មវិធី
  // =============================================================================
  // {
  //   url: null,
  //   name: "ProgramManagement",
  //   tagColor: "warning",
  //   icon: "AirplayIcon",
  //   i18n: "ProgramManagement",
  //   url: '/module/program-arrangement',
  //   slug: "module-program-arrangement",
  //   submenu: [
  //     {
  //       url: '/module/program-arrangement',
  //       slug: 'program-arrangement',
  //       name: "program-arrangement",
  //       icon: "UserIcon",
  //       i18n: "ProgramArrangement",
  //     },
  //     {
  //       url: '/module/program-arrangement/objective',
  //       slug: 'objective',
  //       name: "objective",
  //       icon: "UserIcon",
  //       i18n: "ProgramObjective",
  //     },
  //     {
  //       url: '/module/program-arrangement/program',
  //       slug: 'program',
  //       name: "program",
  //       icon: "UserIcon",
  //       i18n: "Program",
  //     },
  //     {
  //       url: '/module/program-arrangement/subprogram',
  //       slug: 'subprogram',
  //       name: "subprogram",
  //       icon: "UserIcon",
  //       i18n: "ProgramSubProgram",
  //     },
  //     {
  //       url: '/module/program-arrangement/cluster-activity',
  //       slug: 'cluster-activity',
  //       name: "cluster-activity",
  //       icon: "UserIcon",
  //       i18n: "ProgramClusterActivity",
  //     },
  //     {
  //       url: '/module/program-arrangement/activity',
  //       slug: 'activity',
  //       name: "activity",
  //       icon: "UserIcon",
  //       i18n: "ProgramActivity",
  //     },
  //     {
  //       url: '/module/program-arrangement/task',
  //       slug: 'task',
  //       name: "task",
  //       icon: "UserIcon",
  //       i18n: "ProgramTask",
  //     },
  //   ]
  // },
  // =============================================================================
  // Budget Arrangement - រៀបចំថវិកា
  // =============================================================================
  // {
  //   url: 'module',
  //   name: 'BudgetArrangement',
  //   tagColor: "primary",
  //   icon: "DollarSignIcon",
  //   i18n: "BudgetArrangement",
  //   submenu: [
  //     {
  //       url: '/module/budget-arrangement/credit-revenue-rule',
  //       slug: 'credit-revenue-rule',
  //       name: "CreditRevenueRule",
  //       icon: "UserIcon",
  //       i18n: "EntryCreditRevenueRule",
  //     },
  //     {
  //       url: '/module/budget-arrangement/credit-expense-rule',
  //       slug: 'credit-expense-rule',
  //       name: "CreditExpenseRule",
  //       icon: "UserIcon",
  //       i18n: "EntryCreditExpenseRule",
  //     },
  //     {
  //       url: '/module/budget-arrangement/budget-ceiling',
  //       slug: 'budget-ceiling',
  //       name: "BudgetCeiling",
  //       icon: "UserIcon",
  //       i18n: "CeilingBudget",
  //     },
  //     {
  //       url: '/module/budget-arrangement/pip',
  //       slug: 'pip',
  //       name: "PIP",
  //       icon: "UserIcon",
  //       i18n: "PublicInvestmentPlan",
  //     },
  //     {
  //       url: '/module/budget-arrangement/bsp',
  //       slug: 'bps',
  //       name: "BSP",
  //       icon: "UserIcon",
  //       i18n: "BudgetStrategyPlan",
  //     },
  //     {
  //       url: '/module/budget-arrangement/pb',
  //       slug: 'pb',
  //       name: "PB",
  //       icon: "UserIcon",
  //       i18n: "ProjectBudgetPlan",
  //     },
  //   ]
  // },
  {
    header: "IncentiveMgr",
    icon: "LayersIcon",
    i18n: "មុខងារប្រព័ន្ធ",
    items: [
      {
        url: null,
        name: "member",
        tagColor: "primary",
        icon: "FileTextIcon",
        i18n: "គ្រប់គ្រងសមាជិក",
        submenu: [
          {
            url: '/officer',
            name: "List View",
            slug: "data-officer-list",
            i18n: "OfficerList",
          },
          {
            url: '/officer/form',
            name: "Officer Form",
            slug: "data-officer-form",
            i18n: "officerForm",
          },
          {
            url: '/nofication',
            name: "List View",
            slug: "data-notification-list",
            i18n: "NotificationList",
          },
          // {
          //   url: '/incentive/form',
          //   name: "Incentive Form",
          //   slug: "data-incentive-form-list",
          //   i18n: "FormIncentive",
          // },
          // {
          //   url: '/incentive/composition-request',
          //   name: "Composition Request View",
          //   slug: "data-composition-request-list",
          //   i18n: "CompositionRequest",
          // },
        ]
      },
      {
        url: null,
        name: "Transactions",
        icon: "DollarSignIcon",
        i18n: "Transactions",
        submenu: [
          {
            url: '/account/income',
            name: "Select",
            icon: "PocketIcon",
            slug: "account-income",
            i18n: "Income",
          },
          {
            url: '/account/expense',
            name: "Select",
            icon: "PocketIcon",
            slug: "account-expense",
            i18n: "Expense",
          },
          {
            url: '/account/chart-account',
            name: "Select",
            icon: "PocketIcon",
            slug: "account-chart-account",
            i18n: "chart_account",
          },
          {
            url: '/account/officer-payment',
            name: "Select",
            icon: "PocketIcon",
            slug: "account-officer-payment",
            i18n: "paymentList",
          },
          {
            url: '/account/officer-payable-form',
            name: "Select",
            icon: "PocketIcon",
            slug: "account-officer-payable-form",
            i18n: "repayForm",
          },
          {
            url: '/incentive',
            name: "List View",
            slug: "data-incentive-list",
            i18n: "IncentiveList",
          },
          {
            url: '/request',
            name: "request",
            slug: "data-request",
            i18n: "requestList",
          },
          // {
          //   url: '/account/category',
          //   name: "Select",
          //   icon: "PocketIcon",
          //   slug: "account-category",
          //   i18n: "Category",
          // },
          // {
          //   url: '/account/bank-account',
          //   name: "Select",
          //   icon: "PocketIcon",
          //   slug: "account-bank-account",
          //   i18n: "BankAccount",
          // },
          // {
          //   url: '/account/account-group',
          //   name: "Select",
          //   icon: "PocketIcon",
          //   slug: "account-account-group",
          //   i18n: "AccountGroup",
          // },
          // {
          //   url: '/account/account',
          //   name: "Select",
          //   icon: "PocketIcon",
          //   slug: "account-account",
          //   i18n: "Account",
          // },
          // {
          //   url: '/account/account-type',
          //   name: "Select",
          //   icon: "PocketIcon",
          //   slug: "account-account-type",
          //   i18n: "AccountType",
          // },
          // {
          //   url: '/account/payment-method',
          //   name: "Select",
          //   icon: "PocketIcon",
          //   slug: "account-payment-method",
          //   i18n: "PaymentMethod",
          // },
          // {
          //   url: '/account/taxable-salary',
          //   name: "Select",
          //   icon: "PocketIcon",
          //   slug: "account-taxable-salary",
          //   i18n: "TaxableSalary",
          // },
        ]
      },
    ]
  },
  {
    url: null,
    name: "User",
    icon: "UserIcon",
    i18n: "User",
    submenu: [
      {
        url: '/user',
        name: "app-user-list",
        slug: "app-user-list",
        i18n: "UserList",
      },
      {
        url: '/role',
        name: "app-user-role",
        slug: "app-user-role",
        i18n: "RoleList",
      },
      {
        url: '/auditrail',
        name: "app-auditrail",
        slug: "app-auditrail",
        i18n: "auditrail",
      },
    ]
  },
  // =============================================================================
  // Budget Monitoring - តាមដានអនុវត្តថវិកា
  // =============================================================================
//   {
//     url: null,
//     name: 'BudgetMonitoring',
//     tagColor: "primary",
//     icon: "BarChart2Icon",
//     i18n: "BudgetMonitoring",
//     submenu: [
//       {
//         url: '/module/budget-monitoring/income-arrangement',
//         slug: 'income-expense-arrangement',
//         name: "IncomeExpenseArrangement",
//         icon: "UserIcon",
//         i18n: "IncomeProgram",
//       },
//       {
//         url: '/module/budget-monitoring/expense-arrangement',
//         slug: 'expense-arrangement',
//         name: "ExpenseArrangement",
//         icon: "UserIcon",
//         i18n: "ExpenseProgram",
//       },
//       {
//         url: '/module/budget-monitoring/income-implementing-result',
//         slug: 'income-implementing-result',
//         name: "IncomeResult",
//         icon: "UserIcon",
//         i18n: "IncomeResult",
//       },
//       {
//         url: '/module/budget-monitoring/expense-implementing-result',
//         slug: 'expense-implementing-result',
//         name: "ExpenseResult",
//         icon: "UserIcon",
//         i18n: "ExpenseResult",
//       },
//       {
//         url: '/module/budget-monitoring/expenditure-certificate',
//         slug: 'expenditure-certificate',
//         name: "ExpenditureCertificate",
//         icon: "UserIcon",
//         i18n: "ExpendCard",
//       },
//       {
//         url: '/module/budget-monitoring/credit-movement',
//         slug: 'credit-movement',
//         name: "CreditMovement",
//         icon: "UserIcon",
//         i18n: "CreditMovement",
//       },
//     ]
// },

// =============================================================================
  // File Management - ឃាំ្លងឯកសារ
  // =============================================================================
  {
    url: null,
    name: "Library",
    icon: "FileIcon",
    i18n: "Library",
    submenu: [
      {
        url: '/file-manager',
        name: "FileManager",
        slug: "file-manager",
        tagColor: "warning",
        icon: "FileTextIcon",
        i18n: "FileManager",
      },
    ]
  },
  
  // =============================================================================
  // Report - គ្រប់គ្រងរបាយការណ៍
  // =============================================================================
  {
    url: null,
    name: "ReportManagement",
    icon: "FileIcon",
    i18n: "ReportManagement",
    submenu: [
      {
        url: '/report',
        name: 'Report',
        slug: "report",
        tagColor: "primary",
        icon: "BarChart2Icon",
        i18n: "Report"
      },
    ]
  },
]
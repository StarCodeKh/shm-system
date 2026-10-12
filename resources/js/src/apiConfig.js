export default {
    _apiConfiguration: "/api/v1/configuration",
    _apiSite: "/api/v1/site", // public branding: name, organisation, website (site.js)
    _apiLanguages: "/api/v1/languages", // public: active languages for the language switch (i18n/languages.js)
    _apiLanding: "/api/v1/landing", // public: landing page content in the reader's language (/welcome)
    _apiPlanningYear: "/api/v1/configuration/planning",
    // Program Management
    // Notification
    _apiUnreadNotification: "/api/v1/nofication/unreadnotification",
    _apiReadNotification: "/api/v1/notification/read",
    _apiReadAllNotifications: "/api/v1/notification/read_all",
    _apiNotification: "/api/v1/notification",
    _apiSendNotification: "/api/v1/send_notification",
    // Accounting Management'
    _apiChartAccount: "/api/v1/account/chart_account",
    _apiIncome: "/api/v1/income",
    _apiExpense: "/api/v1/expense",
    // Budget Monitoring
    // Indicator
    // Setting
    _apiRole: "/api/v1/setting/role",
    _apiUser: "/api/v1/user",
    _apiUserDevice: "/api/v1/user/device",
    _apiUserProfile: "/api/v1/user/profile",
    _apiAccountTypeGroup: "/api/v1/setting/account_type_group",
    _apiAccountType: "/api/v1/setting/account_type",
    _apiAccountGroup: "/api/v1/setting/account_group",
    _apiAccount: "/api/v1/setting/account",
    _apiDepartment: "/api/v1/setting/department",
    _apiEntity: "/api/v1/setting/entity",
    _apiEntityByDept: "/api/v1/setting/entity/get_entity_by_dept",
    _apiEntityMember: "/api/v1/setting/entity_member",
    _apiTitle: "/api/v1/setting/title",
    _apiPosition: "/api/v1/setting/position",
    _apiBank: "/api/v1/setting/bank",
    _apiLetterType: "/api/v1/setting/letter_type",
    _apiCurrency: "/api/v1/setting/currency",
    _apiCurrencyExchange: "/api/v1/setting/currency_exchange",
    _apiLanguage: "/api/v1/setting/language",
    _apiLandingContent: "/api/v1/setting/landing_content",
    _apiPlanning: "/api/v1/setting/planning",
    _apiPaymentMethod: "/api/v1/setting/payment_method",
    _apiPaymentTerm: "/api/v1/setting/payment_term",
    _apiPaymentType: "/api/v1/setting/payment_type",
    _apiPaymentTypeGetAmount: "/api/v1/setting/payment_type/get_amount_payment_type",
    // File Manager
    _apiFileManager: "/api/v1/file_manager",
    // Officer Management
    _apiOfficer: "/api/v1/officer",
    _apiOfficerRequest: "/api/v1/officer/request",
    _apiOfficerPayable: "/api/v1/officer/payable",
     _apiOfficerGeneralDataForPayable: "/api/v1/officer/payable/officer_data_payable",
    _apiOfficerPayableInfo: "/api/v1/officer/payable/officer_info",
    _apiOfficerPayment: "/api/v1/officer/payment",
    _apiOfficerIncentive: "/api/v1/officer/incentive",
    _apiOfficerPaymentRecurring: "/api/v1/officer/payment_recurring",
    _apiOfficerExecutePaymentRecurring: "/api/v1/officer/execute_payment_recurring",
    _apiOfficerDocument: "/api/v1/officer/document",
    // Incentive
    // PIP
    _apiAudit: "/api/v1/setting/audit",
    // Budget Arrangement
    // Report
    // Role Menu
    _apiRoleMenu: "/api/v1/role_menu/permission/index",
    _apiMenu: "/api/v1/menu",
    _apiMenuTree: "/api/v1/menu/tree",
}

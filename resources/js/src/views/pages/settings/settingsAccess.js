import { can } from "@/acl/access.js"

// Which permission opens each part of Settings: one list for the page, its sections' sub-tabs,
// the navbar gear and the /setting route. Names are spatie permissions or feature keys
// (acl/access.js ⇄ config/access.php); the API checks the same permissions.
// A section shows when the role may open at least one of its sub-tabs.
export const SETTINGS_ACCESS = {
    general: ["setting_view"],
    roles: {
        users: "list_users",
        roles: "list_roles",
    },
    account: {
        payment_type: "list_payment_type",
        payment_term: "list_payment_term",
        payment_method: "list_payment_method",
        account_type: "list_account_type",
        letter_type: "list_letter_type",
        bank: "list_bank",
        currency: "list_currency",
        currency_exchange: "list_currency_exchange",
        planning: "list_mef_planning",
        account_type_group: "list_account_type_group",
        account_group: "list_account_group",
        account: "list_account",
    },
    entity: {
        department: "list_department",
        entity: "list_entity",
        title: "list_title",
        position: "list_position",
    },
    backup: ["setting_backup"],
    notification: ["setting_notifications_view"],
    audit: ["audit"],
}

const permissionsOf = section => {
    const rule = SETTINGS_ACCESS[section]
    return Array.isArray(rule) ? rule : Object.values(rule || {})
}

// May the user open this section of Settings?
export function canSection(section) {
    return permissionsOf(section).some(permission => can(permission))
}

// Sub-tabs of a section the user may open: { users: true, roles: false, ... }
export function allowedTabs(section) {
    return Object.fromEntries(Object.entries(SETTINGS_ACCESS[section] || {}).map(([tab, permission]) => [tab, can(permission)]))
}

// Is there anything in Settings for this user (navbar gear, profile menu, /setting route)?
export function canOpenSettings() {
    return Object.keys(SETTINGS_ACCESS).some(canSection)
}

import axios from "@/axios.js"
import apiConfig from "@/apiConfig.js"
import { firstMenuUrl } from "./menuActive.js"

// Same key the login page and settings pages already use.
const STORAGE_KEY = "role_menus"
// The last sidebar menu received, drawn straight away on the next page load (then refreshed)
const MENU_KEY = "sidebar_menu"

// Permissions saved at login: { list_officer: true, ... }, or null if unknown.
export function storedPermissions() {
    try {
        const value = JSON.parse(localStorage.getItem(STORAGE_KEY))
        return value === null ? null : (value || {})
    } catch (e) {
        return null
    }
}

// Fetch the current user's permissions and keep localStorage in sync.
export async function fetchPermissions() {
    const response = await axios.post(apiConfig._apiRoleMenu, { sort: "id", order: "desc", page_number: 1, limit: 300 })
    const permissions = response.data.permissions || {}
    localStorage.setItem(STORAGE_KEY, JSON.stringify(permissions))
    return permissions
}

// The sidebar menu cached from the last visit ([] when none)
export function storedMenu() {
    try {
        return JSON.parse(localStorage.getItem(MENU_KEY)) || []
    } catch (e) {
        return []
    }
}

// Where a signed-in user starts: the first page of their menu, so every role lands on a page it may open
export const DEFAULT_HOME = "/dashboard"
export function homePath(menu = storedMenu()) {
    return firstMenuUrl(menu) || DEFAULT_HOME
}

// The sidebar menu from Administration › Menus, already limited by the server to what this user may open.
// Items without a translation key show their name.
export async function fetchMenu() {
    const response = await axios.get(apiConfig._apiMenuTree)
    const label = item => ({
        ...item,
        i18n: item.i18n || item.header || item.name,
        ...(item.items ? { items: item.items.map(label) } : {}),
        ...(item.submenu ? { submenu: item.submenu.map(label) } : {}),
    })
    const menu = (response.data.data || []).map(label)
    try { localStorage.setItem(MENU_KEY, JSON.stringify(menu)) } catch (e) { /* storage unavailable */ }
    return menu
}

import axios from "@/axios.js"
import apiConfig from "@/apiConfig.js"

// Same key the login page and settings pages already use.
const STORAGE_KEY = "role_menus"

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

// Keep only the items the user may open. Groups and sections left empty are removed.
// `permissions === null` (not loaded yet) shows everything; the API still enforces access.
export function filterMenu(items, permissions) {
  const allowed = (item) => !item.permission || permissions === null || Boolean(permissions[item.permission])

  return items.reduce((list, item) => {
    if (item.header) {
      const children = filterMenu(item.items || [], permissions)
      if (children.length) list.push({ ...item, items: children })
    } else if (item.submenu) {
      const children = filterMenu(item.submenu, permissions)
      if (children.length) list.push({ ...item, submenu: children })
    } else if (allowed(item)) {
      list.push(item)
    }
    return list
  }, [])
}

import { storedPermissions } from "@/layouts/components/vertical-nav-menu/menuPermissions.js"

// Feature → permission, mirroring config/access.php on the server (keep both in sync).
export const FEATURES = {
    reports: "report_sale",
    audit: "permission_roles",
    role_permissions: "permission_roles",
    file_manager_list: "list_file_manager",
    file_manager_upload: "create_file_manager",
}

// Whether the signed-in user has a permission (by name or by feature key).
// Before permissions are loaded (null) everything shows; the API still refuses what isn't allowed.
export function can(permission) {
    const permissions = storedPermissions()
    if (permissions === null || permissions['*']) return true // '*': Super Admin has everything
    return Boolean(permissions[FEATURES[permission] || permission])
}

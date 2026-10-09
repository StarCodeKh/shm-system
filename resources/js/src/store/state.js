import themeConfig from "@/../themeConfig.js"
import colors from "@/../themeConfig.js"

// Sidebar collapsed/expanded as the user last chose it (saved by the menu's collapse button)
// Light / dark: the user's saved choice, else the device setting, else the project default
export const THEME_KEY = "theme"
export function savedTheme() {
    try {
        const saved = localStorage.getItem(THEME_KEY)
        if (saved === "light" || saved === "dark") return saved
    } catch (e) { /* storage unavailable */ }
    return null
}
// "Light" keeps the project's standard look (semi-dark: dark sidebar, light content)
export const LIGHT_THEME = themeConfig.theme && themeConfig.theme !== "dark" ? themeConfig.theme : "light"
export function themeFor(mode) {
    return mode === "dark" ? "dark" : LIGHT_THEME
}
export function deviceTheme() {
    return typeof window !== "undefined" && window.matchMedia && window.matchMedia("(prefers-color-scheme: dark)").matches ? "dark" : "light"
}

// From this window width up the side menu stays open beside the page; below it, it opens over the page
export const NAV_MENU_BREAKPOINT = 1200

export const SIDEBAR_COLLAPSED_KEY = "sidebarCollapsed"
const savedSidebarCollapsed = (() => {
    try {
        const saved = localStorage.getItem(SIDEBAR_COLLAPSED_KEY)
        return saved === null ? themeConfig.sidebarCollapsed : saved === "true"
    } catch (e) {
        return themeConfig.sidebarCollapsed
    }
})()

// /////////////////////////////////////////////
// Helper
// /////////////////////////////////////////////

// *From Auth - Data will be received from auth provider
const userDefaults = {
    uid         : 0,          // From Auth
    displayName : "Administrator", // From Auth
    userRole    : "admin"
}

const userInfoLocalStorage = JSON.parse(localStorage.getItem("userInfo")) || {}

// Set default values for active-user
// More data can be added by auth provider or other plugins/packages
const getUserInfo = () => {
    let userInfo = {}

    // Update property in user
    Object.keys(userDefaults).forEach((key) => {
        userInfo[key] = userInfoLocalStorage[key] ?  userInfoLocalStorage[key] : userDefaults[key]
    })

    // Include properties from localStorage
    Object.keys(userInfoLocalStorage).forEach((key) => {
        if(userInfo[key] == undefined && userInfoLocalStorage[key] != null) userInfo[key] = userInfoLocalStorage[key]
    })

    return userInfo
}

// /////////////////////////////////////////////
// State
// /////////////////////////////////////////////

const state = {
    AppActiveUser           : getUserInfo(),
    bodyOverlay             : false,
    isVerticalNavMenuActive : true,
    mainLayoutType          : "vertical", // the only layout (horizontal was removed)
    reduceButton            : savedSidebarCollapsed,
    verticalNavMenuWidth    : "default",
    verticalNavMenuItemsMin : false,
    scrollY                 : 0,
    theme                   : themeFor(savedTheme() || deviceTheme()),
    themePrimaryColor       : colors.primary,

    // Can be used to get current window with
    // Note: Above breakpoint state is for internal use of sidebar & navbar component
    windowWidth: typeof window !== "undefined" ? window.innerWidth : null,
}

export default state

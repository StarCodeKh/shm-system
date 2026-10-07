import logo from '@assets/images/logo/logo.png'
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
  about       : "Dessert chocolate cake lemon drops jujubes. Biscuit cupcake ice cream bear claw brownie brownie marshmallow.",
  photoURL    : logo, // From Auth
  status      : "online",
  userRole    : "admin"
}

const userInfoLocalStorage = JSON.parse(localStorage.getItem("userInfo")) || {}

// Set default values for active-user
// More data can be added by auth provider or other plugins/packages
const getUserInfo = () => {
  let userInfo = {}

  // Update property in user
  Object.keys(userDefaults).forEach((key) => {
    // If property is defined in localStorage => Use that
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
    windowWidth: null,
}

export default state

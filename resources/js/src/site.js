import { reactive } from "vue"
import axios from "@/axios.js"
import apiConfig from "@/apiConfig.js"

// The system's public branding from Settings › General (GET /api/v1/site): name, owner (organisation),
// website, logo, telephone, email. Shared by the footer and any screen that shows them.
// The last answer is kept, so the next visit shows the real names at once, then refreshes.
const STORAGE_KEY = "site"

function saved() {
    try {
        return JSON.parse(localStorage.getItem(STORAGE_KEY)) || {}
    } catch (e) {
        return {}
    }
}

export const site = reactive({ name: null, owner: null, website: null, logo: null, telephone: null, email: null, ...saved() })

let request = null

// Load the branding (once per page load unless forced, e.g. after Settings › General is saved)
export function loadSite(force = false) {
    if (request && !force) return request
    request = axios.get(apiConfig._apiSite)
        .then(({ data }) => {
            Object.assign(site, data.data || {})
            try { localStorage.setItem(STORAGE_KEY, JSON.stringify(data.data || {})) } catch (e) { /* storage unavailable */ }
            return site
        })
        .catch(() => {
            request = null // try again next time
            return site
        })
    return request
}

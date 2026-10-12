import axios from "../../../axios/index.js"
import { setAuthHeader } from "@/axios.js"
import router from "../../../../router.js"
import { clearSession } from "../session.js"

// A signed-in request answered 401 means the session expired or the token was revoked:
// clear it and go to the login page once, coming back to this page after signing in.
// (The template "refreshed" the token through a fake endpoint, which hung or looped.)
let redirecting = false

export default {
    init() {
        axios.interceptors.response.use(response => response, error => {
            const { config, response } = error
            const isLogin = config && /\/api\/auth\/(login|device_login)/.test(config.url || '')
            if (response && response.status === 401 && !isLogin && !redirecting) {
                redirecting = true
                clearSession()
                setAuthHeader(null)
                const current = router.currentRoute.value
                if (current.path !== '/pages/login') {
                    router.push({ path: '/pages/login', query: { to: current.fullPath, expired: '1' } })
                        .catch(() => {})
                        .finally(() => { redirecting = false })
                } else {
                    redirecting = false
                }
            }
            return Promise.reject(error)
        })
    },
    // Every sign-in lasts the same time (auth.token_lifetime.days on the server)
    login(email, pwd) {
        return axios.post("/api/auth/login", {email: email, password: pwd})
    },
    registerUser(name, email, pwd) {
        return axios.post("/api/auth/register", {displayName: name, email: email, password: pwd})
    },
}

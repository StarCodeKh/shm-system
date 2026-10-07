import axios from "../../../axios/index.js"
import { setAuthHeader } from "@/axios.js"
import router from "../../../../router.js"

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
                localStorage.removeItem('accessToken')
                setAuthHeader(null)
                localStorage.removeItem('userInfo')
                localStorage.removeItem('role_menus')
                const current = router.currentRoute.value
                if (current.path !== '/pages/login') {
                    router.push({ path: '/pages/login', query: { to: current.fullPath } })
                        .catch(() => {})
                        .finally(() => { redirecting = false })
                } else {
                    redirecting = false
                }
            }
            return Promise.reject(error)
        })
    },
    login(email, pwd, rememberMe = false) {
        return axios.post("/api/auth/login", {email: email, password: pwd, remember_me: !!rememberMe})
    },
    registerUser(name, email, pwd) {
        return axios.post("/api/auth/register", {displayName: name, email: email, password: pwd})
    },
    refreshToken() {
        return axios.post("/api/auth/refresh-token", {accessToken: localStorage.getItem("accessToKen")})
    }
}

import jwt from "../../http/requests/auth/jwt/index.js"
import router from '@/router'
import { saveSessionExpiry } from '@/http/requests/auth/session.js'
import { fetchMenu, homePath, DEFAULT_HOME } from '@/layouts/components/vertical-nav-menu/menuPermissions.js'

export default {
    // Sign in with email / password against /api/auth/login (Laravel Passport)
    loginJWT({ commit }, payload) {
        return new Promise((resolve, reject) => {
            jwt.login(payload.userDetails.email, payload.userDetails.password)
                .then(response => {
                    if (response.data.userData) {
                        localStorage.setItem("accessToken", response.data.accessToken)
                        saveSessionExpiry(response.data.expires_at) // 3 months from now
                        commit('UPDATE_USER_INFO', response.data.userData, {root: true})
                        commit("SET_BEARER", response.data.accessToken)

                        // Back to the page that sent the user here, else the first page of this user's menu
                        // (fetched now: the cached one may belong to the previous user)
                        const back = router.currentRoute.value.query.to
                        const home = back ? Promise.resolve(back) : fetchMenu().then(homePath, () => DEFAULT_HOME)
                        home.then(path => router.push(path)).finally(() => resolve(response))
                    } else {
                        reject({message: "Wrong Email or Password"})
                    }
                })
                .catch(error => reject(error))
        })
    },
}

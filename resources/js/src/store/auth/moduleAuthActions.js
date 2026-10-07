import jwt from "../../http/requests/auth/jwt/index.js"
import router from '@/router'

export default {
    // Sign in with email / password against /api/auth/login (Laravel Passport)
    loginJWT({ commit }, payload) {
      return new Promise((resolve, reject) => {
        jwt.login(payload.userDetails.email, payload.userDetails.password, payload.checkbox_remember_me)
          .then(response => {
            if (response.data.userData) {
              // Back to the page that sent the user here, else home
              router.push(router.currentRoute.value.query.to || '/')

              localStorage.setItem("accessToken", response.data.accessToken)
              commit('UPDATE_USER_INFO', response.data.userData, {root: true})
              commit("SET_BEARER", response.data.accessToken)
              resolve(response)
            } else {
              reject({message: "Wrong Email or Password"})
            }
          })
          .catch(error => reject(error))
      })
    },
}

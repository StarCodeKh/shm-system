/*=========================================================================================
    Page access by role (was vue-acl).
    Routes say who may open them with meta.rule ('editor' | 'admin'); the side menu hides items
    the role can't open ($acl.check). Others go to /pages/not-authorized.
==========================================================================================*/
import { reactive } from 'vue'

// Which roles each rule lets in
const RULES = {
    admin: ['admin'],
    editor: ['editor', 'admin'],
}

const userInfo = (() => {
    try { return JSON.parse(localStorage.getItem('userInfo')) } catch (e) { return null }
})()

const state = reactive({ role: (userInfo && userInfo.userRole) || 'admin' })

export const acl = {
    get role() { return state.role },
    check(rule) {
        if (!rule) return true
        const allowed = RULES[rule]
        return allowed ? allowed.includes(state.role) : state.role === rule
    },
    change(role) {
        state.role = role
    },
}

export default {
    install(app, { router } = {}) {
        app.config.globalProperties.$acl = acl
        if (router) {
            router.beforeEach((to) => {
                const rule = to.meta && to.meta.rule
                if (rule && !acl.check(rule)) return '/pages/not-authorized'
                return true
            })
        }
    },
}

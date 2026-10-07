import { createStore } from 'vuex'

import state from "./state"
import getters from "./getters"
import mutations from "./mutations"
import actions from "./actions"


import moduleAuth from './auth/moduleAuth.js'


export default createStore({
    getters,
    mutations,
    state,
    actions,
    modules: {
        auth: moduleAuth,
    },
    // strict: process.env.NODE_ENV !== 'production'
})

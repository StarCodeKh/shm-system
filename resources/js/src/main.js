import Vue from 'vue'
import App from './App.vue'

// Vuesax Component Framework
import Vuesax from 'vuesax'

Vue.use(Vuesax)

// <vs-input> hides its placeholder only for a "truthy" value, so a number field holding 0 showed "00"
// (grey placeholder 0 + the value 0). Only an empty field shows the placeholder.
Object.defineProperty(Vue.component('VsInput').prototype, 'isValue', {
  configurable: true,
  get() {
    return this.labelPlaceholder ? true : (this.value === undefined || this.value === null || this.value === '')
  },
})

// axios
import axios from "./axios.js"
Vue.prototype.$http = axios

// API Calls
import "./http/requests"


// Theme Configurations
import '../themeConfig.js'

// Firebase
import '@/firebase/firebaseConfig'


// Auth0 Plugin
import AuthPlugin from "./plugins/auth"
Vue.use(AuthPlugin);


// ACL
import acl from './acl/acl'

// Globally Registered Components
import './globalComponents.js'

// Vue Router
import router from './router'

// Vuex Store
import store from './store/store'

// i18n
import i18n, { installPhraseTranslation } from './i18n/i18n'
installPhraseTranslation(Vue)

// One full-page loading spinner at a time
import { installSingleLoading } from './plugins/loading'
installSingleLoading(Vue)

// Date picker defaults (flatpickr) shared by every date field
import './plugins/datePicker'

// Textareas grow with their content
import './plugins/autosize'

// Clicked tab scrolls into view in scrollable tab bars
import './plugins/tabsScroll'

// Opened tabs keep their content and refresh quietly when shown again
import './plugins/keepTabs'

// Vuesax Admin Filters
import './filters/filters'

// Clipboard
import VueClipboard from 'vue-clipboard2'
Vue.use(VueClipboard);


// Tour
import VueTour from 'vue-tour'
Vue.use(VueTour)
require('vue-tour/dist/vue-tour.css')


// VeeValidate, with messages in the user's language
import './plugins/validation'

// Year labels that follow the planning year ($planText)
import './plugins/planningYear'


// Google Maps
// import * as VueGoogleMaps from 'vue2-google-maps'
// Vue.use(VueGoogleMaps, {
//     load: {
//         // Add your API key here
//         key: 'AIzaSyB4DDathvvwuwlwnUu7F4Sow3oU22y5T1Y',
//         libraries: 'places', // This is required if you use the Auto complete plug-in
//     },
// })

// Vuejs - Vue wrapper for hammerjs
import { VueHammer } from 'vue2-hammer'
Vue.use(VueHammer)

// PrismJS
import 'prismjs'
import 'prismjs/themes/prism-tomorrow.css'

// Feather font icon
require('@assets/css/iconfont.css')

Vue.config.productionTip = false

new Vue({
    router,
    store,
    i18n,
    acl,
    render: h => h(App)
}).$mount('#app')

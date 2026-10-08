import { createApp } from 'vue'
import App from './App.vue'

// API client ($http) and the auth requests (token, 401 handling)
import axios from './axios.js'
import './http/requests'

// Theme configuration
import themeConfig, { colors } from '../themeConfig.js'

import router from './router'
import store from './store/store'
import i18n, { installPhraseTranslation } from './i18n/i18n'

// The app's UI components (<vs-input>, <vs-popup>, ...) and $vs (loading, notify, dialog)
import ui from './ui'

// Page access by role (meta.rule on routes, $acl in components)
import acl from './acl/acl'

// Globally registered components (feather-icon, vx-card, v-select, ...)
import globalComponents from './globalComponents.js'

// Date picker defaults (flatpickr) shared by every date field, and <flat-pickr>
import { installDatePicker } from './plugins/datePicker'

// Form validation (v-validate, errors, $validator) with messages in the user's language
import validation from './plugins/validation'

// Year labels that follow the planning year ($planText)
import { installPlanningYear } from './plugins/planningYear'

// Display helpers for templates ($filters.date(value), ...)
import filters from './filters/filters'

// Swipe gestures (the side menu on touch screens)
import hammer from './plugins/hammer'

// Textareas grow with their content
import './plugins/autosize'

// Clicked tab scrolls into view in scrollable tab bars
import './plugins/tabsScroll'
import tabPill from './plugins/tabPill'

// Feather font icons
import '@assets/css/iconfont.css'

const app = createApp(App)

app.config.globalProperties.$http = axios
app.config.globalProperties.$filters = filters

app.use(router)
app.use(store)
app.use(i18n)
app.use(ui, { rtl: themeConfig.rtl, colors })
app.use(acl, { router })
app.use(globalComponents)
app.use(validation)
app.use(hammer)
app.use(tabPill) // v-tab-pill: sliding pill for tab bars and segmented switches
installPhraseTranslation(app)
installDatePicker(app)
installPlanningYear(app)

app.mount('#app')

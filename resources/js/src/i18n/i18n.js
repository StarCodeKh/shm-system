import Vue from 'vue'
import VueI18n from 'vue-i18n'
import i18nData from './i18nData'
import phrases from './phrases'

Vue.use(VueI18n)

export const LOCALE_STORAGE_KEY = 'locale'

const savedLocale = (() => {
  try { return localStorage.getItem(LOCALE_STORAGE_KEY) } catch (e) { return null }
})()

const i18n = new VueI18n({
  locale: i18nData[savedLocale] ? savedLocale : 'kh', // set default locale
  messages: i18nData,
})

document.documentElement.setAttribute('lang', i18n.locale === 'kh' ? 'km' : i18n.locale)

export default i18n

export function setLocale(i18nInstance, locale) {
  i18nInstance.locale = locale
  // Screen readers, spell check and fonts follow the current language
  document.documentElement.setAttribute('lang', locale === 'kh' ? 'km' : locale)
  try { localStorage.setItem(LOCALE_STORAGE_KEY, locale) } catch (e) { /* storage unavailable */ }
}

// Translate inline source text (route titles, toast messages) via phrases.js.
// Unknown text is returned unchanged.
export function translatePhrase(text) {
  if (typeof text !== 'string') return text
  const entry = phrases[text]
  return (entry && entry[i18n.locale]) || text
}

// Form field label: its translation, or a readable fallback ("account_group_id" -> "Account Group")
export function fieldLabel(name) {
  if (!name) return ''
  if (i18n.te(name)) return i18n.t(name)
  return String(name).replace(/_id$/, '').replace(/_/g, ' ').replace(/\b\w/g, c => c.toUpperCase())
}

// "Required" message for a field: its own required_<field> text, or the standard one
export function requiredMessage(name) {
  const key = 'required_' + name
  return i18n.te(key) ? i18n.t(key) : i18n.t('validation_required', { field: fieldLabel(name) })
}

// Every toast gets the look of its type, whatever the calling screen passed:
// one color, icon, title and display time per type, always in the top-right corner.
const NOTIFY_TYPES = {
  success: { color: 'success', icon: 'icon-check-circle', title: 'Success', time: 3500 },
  danger: { color: 'danger', icon: 'icon-x-circle', title: 'Error', time: 6000 },
  warning: { color: 'warning', icon: 'icon-alert-triangle', title: 'Warning', time: 5000 },
  info: { color: 'primary', icon: 'icon-info', title: 'Info', time: 3500 },
}
const GENERIC_TITLES = ['Message', 'Success', 'Error', 'Warning', 'Info']
const INFO_MESSAGES = ['msg.loading']

function notifyType(options) {
  if (INFO_MESSAGES.includes(options.text)) return 'info'
  if (options.color === 'danger' || options.color === 'warning') return options.color
  return 'success' // screens send success as 'primary' or 'success'
}

function translateNotify(options) {
  if (!options || typeof options !== 'object') return options
  const type = NOTIFY_TYPES[notifyType(options)]
  const title = !options.title || GENERIC_TITLES.includes(options.title) ? type.title : options.title
  return {
    ...options,
    color: type.color,
    icon: type.icon,
    iconPack: 'feather',
    position: 'top-right',
    time: options.fixed ? options.time : type.time,
    title: translatePhrase(title),
    text: translatePhrase(options.text),
  }
}

function translateDialog(options) {
  if (!options || typeof options !== 'object') return options
  return {
    ...options,
    title: translatePhrase(options.title),
    text: translatePhrase(options.text),
    acceptText: translatePhrase(options.acceptText || 'Accept'),
    cancelText: translatePhrase(options.cancelText || 'Cancel'),
  }
}

function wrap(fn, translate) {
  if (!fn || fn.__translated) return fn
  const wrapped = function (options) { return fn.call(this, translate(options)) }
  wrapped.__translated = true
  return wrapped
}

// The same toast (type, title and text) shown again within this time is skipped,
// e.g. when several requests fail at once
const REPEAT_WINDOW = 2500
const recentToasts = {}

function notOnRepeat(notify) {
  if (!notify || notify.__deduped) return notify
  const wrapped = function (options) {
    if (options && typeof options === 'object') {
      const key = [options.color, options.title, options.text].join('|')
      const now = Date.now()
      if (recentToasts[key] && now - recentToasts[key] < REPEAT_WINDOW) return
      recentToasts[key] = now
    }
    return notify.call(this, options)
  }
  wrapped.__deduped = true
  wrapped.__translated = true // already wraps the translated notify
  return wrapped
}

// Adds `$tt(text)` and makes every $vs.notify / $vs.dialog follow the current language.
// Must be installed after Vuesax: Vuesax re-assigns its functions in each component's
// beforeCreate, so they are re-wrapped right after.
export function installPhraseTranslation(VueCtor) {
  VueCtor.prototype.$tt = translatePhrase
  VueCtor.prototype.$tField = fieldLabel
  VueCtor.prototype.$tRequired = requiredMessage
  VueCtor.mixin({
    beforeCreate() {
      const vs = this.$vs
      if (!vs) return
      vs.notify = notOnRepeat(wrap(vs.notify, translateNotify))
      vs.dialog = wrap(vs.dialog, translateDialog)
    }
  })
}

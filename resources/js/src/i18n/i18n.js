import Vue from 'vue'
import VueI18n from 'vue-i18n'
import i18nData from './i18nData'
import phrases from './phrases'
import vSelect from 'vue-select'

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

// Form field label in the current language. A field without its own translation borrows a related one:
//   bank_id -> bank, bank -> bank_id, is_active -> active, title_kh -> title + " (ខ្មែរ)".
// Only when nothing matches: a readable name from the field itself ("account_group_id" -> "Account Group").
const LANGUAGE_SUFFIX = { kh: { kh: 'ខ្មែរ', en: 'អង់គ្លេស' }, en: { kh: 'Khmer', en: 'English' } }
function translatedLabel(name) {
  if (i18n.te(name)) return i18n.t(name)
  let match
  if ((match = /^(.+)_id$/.exec(name)) && i18n.te(match[1])) return i18n.t(match[1])
  if (i18n.te(name + '_id')) return i18n.t(name + '_id')
  if ((match = /^(?:is|has)_(.+)$/.exec(name))) return translatedLabel(match[1])
  if ((match = /^(.+)_(kh|en)$/.exec(name))) {
    const base = translatedLabel(match[1])
    const suffix = (LANGUAGE_SUFFIX[i18n.locale] || LANGUAGE_SUFFIX.en)[match[2]]
    return base && `${base} (${suffix})`
  }
  return ''
}
export function fieldLabel(name) {
  if (!name) return ''
  return translatedLabel(String(name))
    || String(name).replace(/_id$/, '').replace(/_/g, ' ').replace(/\b\w/g, c => c.toUpperCase())
}

// Placeholder for a field: its own "hint_<field>" text, else the standard one by kind
// ("Enter <label>", "Select <label>", "Pick a date"). Never the label again: it is already above the field.
export function fieldHint(name, kind = 'text') {
  if (name && i18n.te('hint_' + name)) return i18n.t('hint_' + name)
  if (kind === 'number') return '0'
  if (kind === 'password') return '••••••••'
  if (kind === 'date') return i18n.t('form_pick_date')
  const label = fieldLabel(name)
  // "Enter member name" reads better than "Enter Member name"; acronyms (NSSF) keep their case
  const field = /^[A-Z][a-z]/.test(label) ? label.charAt(0).toLowerCase() + label.slice(1) : label
  return i18n.t(kind === 'select' ? 'form_select_field' : 'form_enter_field', { field })
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
  // Dropdown options follow the language too: a label written in the code (or sent by the server)
  // shows its phrases.js translation. Every <v-select> shares this component, so one hook covers all.
  const optionLabel = vSelect.props.getOptionLabel.default
  vSelect.props.getOptionLabel.default = function (option) {
    return translatePhrase(optionLabel.call(this, option))
  }
  VueCtor.prototype.$tField = fieldLabel
  VueCtor.prototype.$tRequired = requiredMessage
  VueCtor.prototype.$tHint = fieldHint
  VueCtor.mixin({
    beforeCreate() {
      const vs = this.$vs
      if (!vs) return
      vs.notify = notOnRepeat(wrap(vs.notify, translateNotify))
      vs.dialog = wrap(vs.dialog, translateDialog)
    }
  })
}

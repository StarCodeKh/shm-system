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

// Generic toast titles are replaced by one that matches the toast's color
const TITLE_BY_COLOR = { danger: 'Error', success: 'Success', primary: 'Success', warning: 'Warning' }

function translateNotify(options) {
  if (!options || typeof options !== 'object') return options
  const title = options.title === 'Message' && TITLE_BY_COLOR[options.color] || options.title
  return { ...options, title: translatePhrase(title), text: translatePhrase(options.text) }
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

// Adds `$tt(text)` and makes every $vs.notify / $vs.dialog follow the current language.
// Must be installed after Vuesax: Vuesax re-assigns its functions in each component's
// beforeCreate, so they are re-wrapped right after.
export function installPhraseTranslation(VueCtor) {
  VueCtor.prototype.$tt = translatePhrase
  VueCtor.mixin({
    beforeCreate() {
      const vs = this.$vs
      if (!vs) return
      vs.notify = wrap(vs.notify, translateNotify)
      vs.dialog = wrap(vs.dialog, translateDialog)
    }
  })
}

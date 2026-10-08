import { createI18n } from 'vue-i18n'
import i18nData from './i18nData'
import phrases from './phrases'
import vSelect from 'vue-select'

export const LOCALE_STORAGE_KEY = 'locale'

const savedLocale = (() => {
    try { return localStorage.getItem(LOCALE_STORAGE_KEY) } catch (e) { return null }
})()

// Legacy mode keeps the Options API: $t / $te in templates and this.$i18n.locale in components.
// Outside components use i18n.global (i18n.global.locale, i18n.global.t(...)).
// "officer_document" → "Officer document": the last resort for a key no language has
function keyAsWords(key) {
    return key.replace(/_/g, ' ').replace(/^\w/, c => c.toUpperCase())
}

const i18n = createI18n({
    legacy: true,
    locale: i18nData[savedLocale] ? savedLocale : 'kh', // set default locale
    messages: i18nData,
    warnHtmlMessage: false, // a few labels carry <small>/<br>, rendered with v-html
    // A text missing in one language shows in English rather than as its key ...
    fallbackLocale: 'en',
    silentFallbackWarn: true,
    // ... and a key with no text at all reads as words ("officer_document" → "Officer document"), never as the raw key
    postTranslation: (text, key) => (text === key && /^[a-z][a-z0-9]*(_[a-z0-9]+)+$/.test(key) ? keyAsWords(key) : text),
})
const t = i18n.global

document.documentElement.setAttribute('lang', t.locale === 'kh' ? 'km' : t.locale)

export default i18n

// Called after the language changes (e.g. validation messages re-word themselves, lists reload their
// server-made labels). Returns a function that stops listening: call it when the component goes away.
const localeListeners = []
export function onLocaleChange(listener) {
    localeListeners.push(listener)
    return () => {
        const i = localeListeners.indexOf(listener)
        if (i >= 0) localeListeners.splice(i, 1)
    }
}

// vue-select prints "Sorry, no matching options." as fixed English text: it is hidden by CSS (custom.scss)
// and this translated text is shown in its place, kept in step with the language.
function applySelectTexts() {
    document.documentElement.style.setProperty('--vs-no-options', JSON.stringify(t.t('select_no_options')))
}
applySelectTexts()
onLocaleChange(applySelectTexts)

export function setLocale(i18nInstance, locale) {
    i18nInstance.locale = locale
    localeListeners.forEach(listener => listener(locale))
    // Screen readers, spell check and fonts follow the current language
    document.documentElement.setAttribute('lang', locale === 'kh' ? 'km' : locale)
    try { localStorage.setItem(LOCALE_STORAGE_KEY, locale) } catch (e) { /* storage unavailable */ }
}

// Translate inline source text (route titles, toast messages) via phrases.js.
// Unknown text is returned unchanged.
export function translatePhrase(text) {
    if (typeof text !== 'string') return text
    const entry = phrases[text]
    return (entry && entry[t.locale]) || text
}

// Form field label in the current language. A field without its own translation borrows a related one:
//   bank_id -> bank, bank -> bank_id, is_active -> active, title_kh -> title + " (ខ្មែរ)".
// Only when nothing matches: a readable name from the field itself ("account_group_id" -> "Account Group").
const LANGUAGE_SUFFIX = { kh: { kh: 'ខ្មែរ', en: 'អង់គ្លេស' }, en: { kh: 'Khmer', en: 'English' } }
function translatedLabel(name) {
    if (t.te(name)) return t.t(name)
    let match
    if ((match = /^(.+)_id$/.exec(name)) && t.te(match[1])) return t.t(match[1])
    if (t.te(name + '_id')) return t.t(name + '_id')
    if ((match = /^(?:is|has)_(.+)$/.exec(name))) return translatedLabel(match[1])
    if ((match = /^(.+)_(kh|en)$/.exec(name))) {
        const base = translatedLabel(match[1])
        const suffix = (LANGUAGE_SUFFIX[t.locale] || LANGUAGE_SUFFIX.en)[match[2]]
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
    if (name && t.te('hint_' + name)) return t.t('hint_' + name)
    if (kind === 'number') return '0'
    if (kind === 'password') return '••••••••'
    if (kind === 'date') return t.t('form_pick_date')
    const label = fieldLabel(name)
    // "Enter member name" reads better than "Enter Member name"; acronyms (NSSF) keep their case
    const field = /^[A-Z][a-z]/.test(label) ? label.charAt(0).toLowerCase() + label.slice(1) : label
    return t.t(kind === 'select' ? 'form_select_field' : 'form_enter_field', { field })
}

// "Required" message for a field, built from its label so it always matches the label above it
export function requiredMessage(name) {
    return t.t('form_required', { field: fieldLabel(name) })
}

// Permissions are named "action_module" (create_account, list_users): shown as "Create · Accounts" in the current language.
// The module is the permission group (role editor section); without it, the part after the action is used.
// A name can have its own text as perm_<name> (e.g. perm_report_sale).
export const PERMISSION_ACTIONS = ['list', 'show', 'create', 'edit', 'update', 'delete', 'download', 'import', 'export', 'print', 'execute', 'view_report', 'notify', 'permission', 'filemanager']

const readable = text => String(text).replace(/_/g, ' ').replace(/^\w/, c => c.toUpperCase())

export function permissionActionLabel(action) {
    return t.te('perm_action_' + action) ? t.t('perm_action_' + action) : readable(action)
}

export function permissionModuleLabel(module) {
    if (t.te('perm_module_' + module)) return t.t('perm_module_' + module)
    return translatedLabel(String(module)) || readable(module)
}

export function permissionLabel(name, module = null) {
    if (!name) return ''
    if (t.te('perm_' + name)) return t.t('perm_' + name)
    const action = PERMISSION_ACTIONS.find(a => name.startsWith(a + '_'))
    if (!action) return module ? permissionModuleLabel(module) : readable(name)
    return `${permissionActionLabel(action)} · ${permissionModuleLabel(module || name.slice(action.length + 1))}`
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

// Used by $vs.notify (ui/vs.js)
export function translateNotify(options) {
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

// Used by $vs.dialog (ui/vs.js)
export function translateDialog(options) {
    if (!options || typeof options !== 'object') return options
    return {
        ...options,
        title: translatePhrase(options.title),
        text: translatePhrase(options.text),
        acceptText: translatePhrase(options.acceptText || 'Accept'),
        cancelText: translatePhrase(options.cancelText || 'Cancel'),
    }
}

// Adds $tt(text), $tField, $tRequired and $tHint, and makes <v-select> option labels follow the language.
// ($vs.notify / $vs.dialog are translated in ui/vs.js.)
export function installPhraseTranslation(app) {
    const g = app.config.globalProperties
    g.$tPermission = permissionLabel
    g.$tt = translatePhrase
    // Dropdown options follow the language too: a label written in the code (or sent by the server)
    // shows its phrases.js translation. Every <v-select> shares this component, so one hook covers all.
    const optionLabel = vSelect.props.getOptionLabel.default
    vSelect.props.getOptionLabel.default = function (option) {
        return translatePhrase(optionLabel.call(this, option))
    }
    g.$tField = fieldLabel
    g.$tRequired = requiredMessage
    g.$tHint = fieldHint
}

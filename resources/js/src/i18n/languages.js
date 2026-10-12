import { reactive } from "vue"
import axios from "@/axios.js"
import apiConfig from "@/apiConfig.js"
import i18n, { setLocale } from "./i18n"
import flagKh from "@assets/images/flags/kh.svg"
import flagEn from "@assets/images/flags/en.svg"

// The languages of the language switch (top bar and sign-in page), from Settings › Language
// (GET /api/v1/languages): active ones only, default first. The last answer is kept, so the
// switch shows at once on the next visit, then refreshes.
export const LANGUAGES_STORAGE_KEY = "languages"

// Flags that come with the app; a language added in Settings can bring its own image
const BUNDLED_FLAGS = { kh: flagKh, en: flagEn }

const BUILT_IN = [
    { code: "kh", name: "ខ្មែរ", image: null },
    { code: "en", name: "English", image: null },
]

// { code, name, flag, label }: label is what the switch shows ("ខ្មែរ", "EN"), flag may be null (a code badge then)
export function describe({ code, name, image }) {
    const latin = /^[\u0000-\u024F]*$/.test(name || "") // Latin letters, accents included
    return {
        code,
        name: name || code.toUpperCase(),
        flag: image || BUNDLED_FLAGS[code] || null,
        // Latin-script names are long ("English", "Français"): the code reads better in a pill
        label: !name || latin ? code.toUpperCase() : name,
    }
}

function saved() {
    try {
        const list = JSON.parse(localStorage.getItem(LANGUAGES_STORAGE_KEY))
        return Array.isArray(list) && list.length ? list : BUILT_IN
    } catch (e) {
        return BUILT_IN
    }
}

export const languages = reactive({ list: saved().map(describe) })

// Default language: the first active one
export function defaultLocale() {
    return (languages.list[0] || {}).code || "kh"
}

let request = null

// Load the languages (once per page load unless forced, e.g. after Settings › Language is saved).
// When the language in use was turned off, the page moves to the default one.
export function loadLanguages(force = false) {
    if (request && !force) return request
    request = axios.get(apiConfig._apiLanguages)
        .then(({ data }) => {
            const list = (data && data.data) || []
            if (!list.length) return languages
            languages.list = list.map(describe)
            try { localStorage.setItem(LANGUAGES_STORAGE_KEY, JSON.stringify(list)) } catch (e) { /* storage unavailable */ }
            if (!languages.list.some(lang => lang.code === i18n.global.locale)) setLocale(i18n.global, defaultLocale())
            return languages
        })
        .catch(() => {
            request = null // try again next time
            return languages
        })
    return request
}

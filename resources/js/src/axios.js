// axios
import axios from 'axios'
import i18n, { translatePhrase } from './i18n/i18n'

// API calls go to the same site the app is served from (/api/...), so no base URL is set
const axiosApi = axios.create()

export const setAuthHeader = (token) => {
    if (token) axiosApi.defaults.headers.common['Authorization'] = `Bearer ${token}`
    else delete axiosApi.defaults.headers.common['Authorization']
}

setAuthHeader(localStorage.getItem('accessToken'))

// Tell the API which language the user is reading (reports, labels): kh | en
axiosApi.interceptors.request.use(config => {
    config.headers['X-Locale'] = i18n.global.locale || 'kh'
    return config
})

// One save at a time: the same write sent again while the first is still running (double click,
// Enter + click) joins the first request instead of saving the record twice. Reads are never merged,
// and uploads (FormData) always go through since two of them can look alike.
const WRITE_METHODS = ['post', 'put', 'patch', 'delete']
const inFlight = new Map()
// axios 1.x keeps adapters as a list of names: resolve it to the function that sends the request
const send = axios.getAdapter(axiosApi.defaults.adapter || axios.defaults.adapter)
axiosApi.defaults.adapter = config => {
    const body = config.data
    if (!WRITE_METHODS.includes(config.method) || (body != null && typeof body !== 'string')) return send(config)
    const key = `${config.method} ${config.url} ${body || ''}`
    if (!inFlight.has(key)) {
        inFlight.set(key, send(config).finally(() => inFlight.delete(key)))
    }
    return inFlight.get(key)
}

// Why the last request failed, in the user's language. Screens catch errors with a general
// "action failed" toast; $vs.notify swaps in this reason instead (see ui/vs.js).
// 4xx: the API's own translated message. 5xx: a standard phrase (the raw error can hold SQL or a stack trace).
let lastError = null

// Laravel's built-in messages are English only: these show the standard phrase for the status instead
const FRAMEWORK_MESSAGES = ['Unauthenticated.', 'This action is unauthorized.', 'Not Found', 'Not Found.', 'CSRF token mismatch.',
    'Too Many Attempts.', 'Server Error', 'The given data was invalid.', 'Forbidden', 'Unauthorized', 'Method Not Allowed']

// Seconds to wait after a 429, from the server's Retry-After header (seconds or a date); 60 when it sends none
export function retryAfterSeconds(error) {
    const value = error && error.response && error.response.headers && error.response.headers['retry-after']
    const seconds = Number(value)
    if (Number.isFinite(seconds) && seconds > 0) return Math.ceil(seconds)
    const date = Date.parse(value)
    return Number.isFinite(date) ? Math.max(1, Math.ceil((date - Date.now()) / 1000)) : 60
}

// data: the response body, or for a download (responseType blob) the JSON the server sent instead of the file
function errorReason(error, data) {
    if (axios.isCancel(error)) return null
    const { response } = error
    if (!response) return { text: 'msg.network_error' }
    const { status } = response
    if (status >= 500) return { text: 'msg.server_error' }
    // Too many requests: the toast counts down to the moment the server accepts requests again
    if (status === 429) return { text: 'msg.too_many_requests_wait', until: Date.now() + retryAfterSeconds(error) * 1000 }
    if (status === 422 && data && data.errors) {
        const first = Object.values(data.errors).flat()[0]
        if (typeof first === 'string') return { text: first }
    }
    const message = data && typeof data.message === 'string' ? data.message.trim() : ''
    if (message && !FRAMEWORK_MESSAGES.includes(message)) return { text: message }
    if (status === 401 || status === 419) return { text: 'msg.session_expired' }
    if (status === 403) return { text: 'msg.no_permission' }
    if (status === 404) return { text: 'msg.not_found' }
    return null
}

axiosApi.interceptors.response.use(response => response, async error => {
    let data = error.response && error.response.data
    if (typeof Blob !== 'undefined' && data instanceof Blob && /json/.test(data.type)) {
        try { data = JSON.parse(await data.text()) } catch (e) { data = null }
    }
    const reason = errorReason(error, data)
    error.reason = reason
    lastError = reason ? { ...reason, at: Date.now() } : null
    return Promise.reject(error)
})

// A failed request's reason as text in the user's language, for a screen that shows it itself
// (inline message, or a thrown message): the same text as the standard error toast.
export function errorMessage(error, fallback = 'msg.action_failed') {
    const reason = error && error.reason
    if (!reason) return translatePhrase(fallback)
    const text = translatePhrase(reason.text)
    return reason.until ? text.replace('{seconds}', Math.max(1, Math.ceil((reason.until - Date.now()) / 1000))) : text
}

// Why a request failed within the last moment ({ text, until? }), or null
export const recentErrorReason = (within = 1500) =>
    lastError && Date.now() - lastError.at < within ? lastError : null

export default axiosApi;

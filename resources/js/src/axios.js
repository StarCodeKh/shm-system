// axios
import axios from 'axios'
import i18n from './i18n/i18n'

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

export default axiosApi;

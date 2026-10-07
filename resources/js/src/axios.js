// axios
import axios from 'axios'
import i18n from './i18n/i18n'

// let domain = "http://209.97.167.222"
let domain = "http://localhost:9003"

// export default axios.create({
//   domain
//   // You can add your headers here
// })

const axiosApi = axios.create({
  domain//(process.env.VUE_APP_BASE_URL !== undefined) ? process.env.VUE_APP_BASE_URL : '//trackerapp.local/'
})

export const setAuthHeader = (token) => {
  axiosApi.defaults.headers.common['Authorization'] = `Bearer ${token}`
}

setAuthHeader(localStorage.getItem('accessToken'))

// Tell the API which language the user is reading (reports, labels): kh | en
axiosApi.interceptors.request.use(config => {
  config.headers['X-Locale'] = i18n.locale || 'kh'
  return config
})

// One save at a time: the same write sent again while the first is still running (double click,
// Enter + click) joins the first request instead of saving the record twice. Reads are never merged,
// and uploads (FormData) always go through since two of them can look alike.
const WRITE_METHODS = ['post', 'put', 'patch', 'delete']
const inFlight = new Map()
const send = axiosApi.defaults.adapter || axios.defaults.adapter
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

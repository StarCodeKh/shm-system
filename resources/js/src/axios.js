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

export default axiosApi;

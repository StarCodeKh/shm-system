// axios
import axios from 'axios'

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

export default axiosApi;

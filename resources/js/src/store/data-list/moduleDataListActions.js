import axios from "@/axios.js"

export default {
  fetchDataListItems({ commit }) {
    return new Promise((resolve, reject) => {
      axios.get("/api/data-list/products")
        .then((response) => {
          commit('SET_PRODUCTS', response.data)
          resolve(response)
        })
        .catch((error) => { reject(error) })
    })
  },
  fetchDataListIncentiveItems({ commit }) {
    return new Promise((resolve, reject) => {
      axios.get("/api/data-list/incentive-officer")
        .then((response) => {
          commit('SET_PRODUCTS', response.data)
          // resolve(response)
        })
        .catch((error) => { reject(error) })
    })
  },
  fetchDataListSectorItems({ commit }) {
    return new Promise((resolve, reject) => {
      axios.get("/api/data-list/sector")
        .then((response) => {
          commit('SET_PRODUCTS', response.data)
          // resolve(response)
        })
        .catch((error) => { reject(error) })
    })
  },
  // fetchEventLabels({ commit }) {
  //   return new Promise((resolve, reject) => {
  //     axios.get("/api/apps/calendar/labels")
  //       .then((response) => {
  //         commit('SET_LABELS', response.data)
  //         resolve(response)
  //       })
  //       .catch((error) => { reject(error) })
  //   })
  // },
  fetchDataList({ commit }, api) {
    return new Promise((resolve, reject) => {
      axios.get(api + '?offset=0&limit=1000&sort=id&order=desc')
        .then((response) => {
          commit('SET_DATA', response.data.data)
          // resolve(response)
        })
        .catch((error) => { reject(error) })
    })
  },
  updateItem({ commit }, item) {
    return new Promise((resolve, reject) => {
      let data = {
        name: item.name
      }
      axios.put(item.api + `/${item.id}`, {name: item.name})
        .then((response) => {
          commit('UPDATE_DATA', response.data.data)
          resolve(response)
        })
        .catch((error) => { reject(error) })
    })
  },
  updateDuty({ commit }, item) {
    return new Promise((resolve, reject) => {
      let data = {
        name: item.name,
        order_level: item.order_level,
        incentive: item.incentive
      }
      axios.put(item.api + `/${item.id}`, data)
        .then((response) => {
          commit('UPDATE_DATA', response.data.data)
          resolve(response)
        })
        .catch((error) => { reject(error) })
    })
  },
  
  updateMasterItem({ commit }, item) {
    return new Promise((resolve, reject) => {
      let data = {
        taxable_salary_monthly_from: parseFloat(item.taxable_salary_monthly_from),
        taxable_salary_monthly_to: parseFloat(item.taxable_salary_monthly_to),
        currency_symbol: item.currency_symbol,
        rate: parseFloat(item.rate),
        over_class_tax: parseFloat(item.over_class_tax),
        order_level: parseInt(item.order_level),
        remark: item.remark,
      }
      axios.put(item.api + `/${item.id}`, data)
        .then((response) => {
          commit('UPDATE_DATA', response.data.data)
          resolve(response)
        })
        .catch((error) => { reject(error) })
    })
  },
  updateExchangeRate({ commit }, item) {
    return new Promise((resolve, reject) => {
      let data = {
        name: item.name,
        code: item.code,
        rate: item.rate,
        abbreviation: item.abbreviation,
        symbol_left: item.symbol_left,
        symbol_right: item.symbol_right,
        symbol_right: item.symbol_right,
        remark: item.remark,
      }
      axios.put(item.api + `/${item.id}`, data)
        .then((response) => {
          commit('UPDATE_DATA', response.data.data)
          resolve(response)
        })
        .catch((error) => { reject(error) })
    })
  },
  updateEntity({ commit }, item) {
    return new Promise((resolve, reject) => {
      let data = {
        name: item.name,
        limit_member: item.limit_member,
        sector: item.sector,
        department: item.department,
        code: item.code,
        is_active: item.is_active,
        color: item.color,
        order_level: item.order_level,
        abbreviation: item.abbreviation,
        deputy_commissionerofficer: item.deputy_commissionerofficer,
        professional_offier: item.professional_offier,
      }
      axios.put(item.api + `/${item.id}`, data)
        .then((response) => {
          commit('UPDATE_DATA', response.data.data)
          resolve(response)
        })
        .catch((error) => { reject(error) })
    })
  },
  updateBankAccount({ commit }, item) {
    return new Promise((resolve, reject) => {
      let data = {
        name: item.name,
        name_kh: item.name_kh,
        account_group: item.account_group,
        account_name: item.account_name,
        account_type: item.account_type,
        account_number: item.account_number,
        open_balance: item.open_balance,
        currency: item.currency,
        code: item.code,
        address: item.address,
        remark: item.remark,
        issue_date: item.issue_date,
      }
      axios.put(item.api + `/${item.id}`, data)
        .then((response) => {
          commit('UPDATE_DATA', response.data.data)
          resolve(response)
        })
        .catch((error) => { reject(error) })
    })
  },
  addItem({ commit }, item) {
    console.log("item", item);
    return new Promise((resolve, reject) => {
      axios.post(item.api, item)
        .then((response) => {
          commit('ADD_DATA', Object.assign(item, {id: response.data.data.id}))
          resolve(response)
        })
        .catch((error) => { reject(error) })
    })
  },
  removeItem({ commit }, data) {
    return new Promise((resolve, reject) => {
      axios.delete(data.api + `/` + data.id)
        .then((response) => {
          commit('REMOVE_DATA', data.id)
          resolve(response)
        })
        .catch((error) => { reject(error) })
    })
  },
  // eventDragged({ commit }, payload) {
  //   return new Promise((resolve, reject) => {
  //     axios.post(`/api/apps/calendar/event/dragged/${payload.event.id}`, {payload: payload})
  //       .then((response) => {

  //         // Convert Date String to Date Object
  //         let event = response.data
  //         event.startDate = new Date(event.startDate)
  //         event.endDate = new Date(event.endDate)

  //         commit('UPDATE_EVENT', event)
  //         resolve(response)
  //       })
  //       .catch((error) => { reject(error) })
  //   })
  // },
}

/*=========================================================================================
  File Name: planningYear.js
  Description: Year labels that follow the planning year (Settings > config_planning_year).

  Write years relative to the planning year P, and render the text with $planText():
    "គ្រោងឆ្នាំ{P+1}"            -> "គ្រោងឆ្នាំ២០២៤"   (planning year 2023)
    "ច្បាប់ហិរញ្ញវត្ថុឆ្នាំ{P-1}" -> "ច្បាប់ហិរញ្ញវត្ថុឆ្នាំ២០២២"
    "{P} plan"                   -> "2023 plan"
  Text that contains Khmer gets Khmer digits. The year is loaded once, on first use;
  screens update when it arrives.
==========================================================================================*/

import { reactive } from 'vue'
import axios from '@/axios.js'
import apiConfig from '@/apiConfig.js'

const state = reactive({ year: null })
let request = null

export function loadPlanningYear() {
  if (!request) {
    request = axios.get(apiConfig._apiPlanningYear)
      .then(response => {
        const year = Number(response.data && response.data.data && response.data.data.year)
        state.year = year || null
        return state.year
      })
      .catch(() => { request = null }) // try again next time
  }
  return request
}

const KHMER_DIGITS = '០១២៣៤៥៦៧៨៩'
const toKhmerDigits = value => String(value).replace(/\d/g, d => KHMER_DIGITS[d])

export function planText(text) {
  if (typeof text !== 'string' || text.indexOf('{P') === -1) return text
  if (state.year === null) loadPlanningYear()
  const khmer = /[ក-៿]/.test(text)
  return text.replace(/\{P([+-]\d+)?\}/g, (_, offset) => {
    if (state.year === null) return '…'
    const year = state.year + Number(offset || 0)
    return khmer ? toKhmerDigits(year) : String(year)
  })
}

export function installPlanningYear(app) {
  app.config.globalProperties.$planText = planText
  app.config.globalProperties.$planYear = offset => (state.year === null ? null : state.year + Number(offset || 0))
}

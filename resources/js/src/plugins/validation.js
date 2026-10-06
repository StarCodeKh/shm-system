/*=========================================================================================
  File Name: validation.js
  Description: VeeValidate messages in the user's language (kh | en).

  Field names in messages come from the app's translations (i18nData), so an error reads
  "សូមបំពេញ នាមត្រកូល។" instead of "The first_name field is required."
  The messages follow the language switch in the navbar.
==========================================================================================*/

import Vue from 'vue'
import VeeValidate, { Validator } from 'vee-validate'
import en from 'vee-validate/dist/locale/en'
import i18n from '@/i18n/i18n'
import i18nData from '@/i18n/i18nData'

const kh = {
  name: 'kh',
  messages: {
    _default: field => `${field} មិនត្រឹមត្រូវ។`,
    required: field => `សូមបំពេញ ${field}។`,
    email: field => `${field} ត្រូវតែជាអ៊ីមែលត្រឹមត្រូវ។`,
    numeric: field => `${field} ត្រូវតែជាលេខ។`,
    integer: field => `${field} ត្រូវតែជាចំនួនគត់។`,
    decimal: (field, [decimals = '*'] = []) => decimals === '*'
      ? `${field} ត្រូវតែជាលេខ។`
      : `${field} ត្រូវតែជាលេខ ដែលមានទសភាគមិនលើស ${decimals} ខ្ទង់។`,
    digits: (field, [length]) => `${field} ត្រូវតែជាលេខ ${length} ខ្ទង់។`,
    min: (field, [length]) => `${field} ត្រូវមានយ៉ាងតិច ${length} តួអក្សរ។`,
    max: (field, [length]) => `${field} មិនអាចលើសពី ${length} តួអក្សរ។`,
    length: (field, [length]) => `${field} ត្រូវមាន ${length} តួអក្សរ។`,
    min_value: (field, [min]) => `${field} ត្រូវតែធំជាង ឬស្មើ ${min}។`,
    max_value: (field, [max]) => `${field} ត្រូវតែតូចជាង ឬស្មើ ${max}។`,
    between: (field, [min, max]) => `${field} ត្រូវនៅចន្លោះ ${min} និង ${max}។`,
    confirmed: field => `${field} មិនដូចគ្នាទេ។`,
    date_format: (field, [format]) => `${field} ត្រូវតែមានទម្រង់ ${format}។`,
    alpha: field => `${field} អាចមានតែអក្សរប៉ុណ្ណោះ។`,
    alpha_num: field => `${field} អាចមានតែអក្សរ និងលេខប៉ុណ្ណោះ។`,
    alpha_dash: field => `${field} អាចមានតែអក្សរ លេខ សញ្ញា - និង _ ប៉ុណ្ណោះ។`,
    regex: field => `ទម្រង់ ${field} មិនត្រឹមត្រូវ។`,
    url: field => `${field} ត្រូវតែជាតំណភ្ជាប់ត្រឹមត្រូវ។`,
    image: field => `${field} ត្រូវតែជារូបភាព។`,
    mimes: field => `ប្រភេទឯកសារ ${field} មិនត្រឹមត្រូវ។`,
    size: (field, [size]) => `${field} មិនអាចធំជាង ${size} KB។`,
  },
}

// Field labels: every string translation is a possible field name
const labels = locale => Object.entries(i18nData[locale] || {})
  .reduce((out, [key, value]) => (typeof value === 'string' ? { ...out, [key]: value } : out), {})

Vue.use(VeeValidate, {
  locale: i18n.locale,
  dictionary: {
    en: { ...en, attributes: labels('en') },
    kh: { ...kh, attributes: labels('kh') },
  },
})

// Follow the language switch
new Vue({
  computed: { locale: () => i18n.locale },
  watch: { locale: locale => Validator.localize(locale) },
})

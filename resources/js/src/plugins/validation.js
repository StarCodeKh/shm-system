/*=========================================================================================
  File Name: validation.js
  Description: Form validation with the API the forms already use (was vee-validate 2):
    <vs-input name="first_name" v-model="x" v-validate="'required|min:3'" />
    {{ errors.first('first_name') }}   errors.has(...)   errors.any()   errors.items
    await this.$validator.validateAll()   this.$validator.reset()
    <form data-vv-scope="step-1"> ... errors.first('step-1.issue_date') / validateAll('step-1')
  A field is checked when its value changes or it loses focus (data-vv-validate-on="blur": only then),
  and by validateAll(). Messages are in the user's language (kh | en) and name the field with its
  label from the app's translations ("សូមបំពេញ នាមត្រកូល។"), following the language switch.
==========================================================================================*/
import { reactive } from 'vue'
import i18n, { onLocaleChange } from '@/i18n/i18n'
import i18nData from '@/i18n/i18nData'

const MESSAGES = {
  kh: {
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
    alpha: field => `${field} អាចមានតែអក្សរប៉ុណ្ណោះ។`,
    alpha_num: field => `${field} អាចមានតែអក្សរ និងលេខប៉ុណ្ណោះ។`,
    alpha_dash: field => `${field} អាចមានតែអក្សរ លេខ សញ្ញា - និង _ ប៉ុណ្ណោះ។`,
    regex: field => `ទម្រង់ ${field} មិនត្រឹមត្រូវ។`,
    url: field => `${field} ត្រូវតែជាតំណភ្ជាប់ត្រឹមត្រូវ។`,
  },
  en: {
    _default: field => `The ${field} value is not valid`,
    required: field => `The ${field} field is required`,
    email: field => `The ${field} field must be a valid email`,
    numeric: field => `The ${field} field may only contain numeric characters`,
    integer: field => `The ${field} field must be an integer`,
    decimal: (field, [decimals = '*'] = []) => `The ${field} field must be numeric and may contain${decimals && decimals !== '*' ? ' ' + decimals : ''} decimal points`,
    digits: (field, [length]) => `The ${field} field must be numeric and contains exactly ${length} digits`,
    min: (field, [length]) => `The ${field} field must be at least ${length} characters`,
    max: (field, [length]) => `The ${field} field may not be greater than ${length} characters`,
    length: (field, [length]) => `The ${field} length must be ${length}`,
    min_value: (field, [min]) => `The ${field} field must be ${min} or more`,
    max_value: (field, [max]) => `The ${field} field must be ${max} or less`,
    between: (field, [min, max]) => `The ${field} field must be between ${min} and ${max}`,
    alpha: field => `The ${field} field may only contain alphabetic characters`,
    alpha_num: field => `The ${field} field may only contain alpha-numeric characters`,
    alpha_dash: field => `The ${field} field may contain alpha-numeric characters as well as dashes and underscores`,
    regex: field => `The ${field} field format is invalid`,
    url: field => `The ${field} field is not a valid URL`,
  },
}

// ---------------------------------------------------------------- rules
const isEmpty = value => value === undefined || value === null || value === '' || value === false || (Array.isArray(value) && value.length === 0)
const text = value => (value === undefined || value === null ? '' : String(value))
const RULES = {
  required: value => !isEmpty(typeof value === 'string' ? value.trim() : value),
  email: value => /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(text(value)),
  numeric: value => /^[0-9]+$/.test(text(value)),
  integer: value => /^-?[0-9]+$/.test(text(value)),
  decimal: (value, [decimals = '*'] = []) => new RegExp(`^[-+]?\\d*(\\.\\d${decimals === '*' ? '+' : `{1,${decimals}}`})?$`).test(text(value)),
  digits: (value, [length]) => /^[0-9]*$/.test(text(value)) && text(value).length === Number(length),
  min: (value, [length]) => text(value).length >= Number(length),
  max: (value, [length]) => text(value).length <= Number(length),
  length: (value, [length]) => text(value).length === Number(length),
  min_value: (value, [min]) => Number(value) >= Number(min),
  max_value: (value, [max]) => Number(value) <= Number(max),
  between: (value, [min, max]) => Number(value) >= Number(min) && Number(value) <= Number(max),
  alpha: value => /^[\p{L}]*$/u.test(text(value)),
  alpha_num: value => /^[\p{L}\p{N}]*$/u.test(text(value)),
  alpha_dash: value => /^[\p{L}\p{N}_-]*$/u.test(text(value)),
  regex: (value, [pattern]) => (pattern instanceof RegExp ? pattern : new RegExp(pattern)).test(text(value)),
  url: value => /^(https?:\/\/)[^\s]+$/.test(text(value)),
}

// "required|min:3" or { required: true, regex: /.../ } -> [{ name, params }]
function parseRules(spec) {
  if (!spec) return []
  if (typeof spec === 'string') {
    return spec.split('|').filter(Boolean).map((part) => {
      const at = part.indexOf(':')
      return at === -1 ? { name: part, params: [] } : { name: part.slice(0, at), params: part.slice(at + 1).split(',') }
    })
  }
  return Object.entries(spec)
    .filter(([, param]) => param !== false && param !== null && param !== undefined)
    .map(([name, param]) => ({ name, params: param === true ? [] : Array.isArray(param) ? param : [param] }))
}

// A value is only checked by the other rules when it is filled in (as vee-validate did)
function check(value, rules) {
  for (const rule of rules) {
    const test = RULES[rule.name]
    if (!test) continue
    if (rule.name !== 'required' && isEmpty(value)) continue
    if (!test(value, rule.params)) return rule
  }
  return null
}

// Field label in the current language: its translation, else the field name
function displayName(field) {
  const labels = i18nData[i18n.global.locale] || {}
  return typeof labels[field] === 'string' ? labels[field] : field
}

function message(rule, field) {
  const messages = MESSAGES[i18n.global.locale] || MESSAGES.kh
  const build = messages[rule.name] || messages._default
  return build(displayName(field), rule.params)
}

// ---------------------------------------------------------------- error bag
class ErrorBag {
  constructor() {
    this.items = []
  }
  // "scope.field" or "field"
  match(key, scope) {
    let field = key
    if (scope === undefined && key && key.indexOf('.') !== -1) {
      const at = key.indexOf('.')
      scope = key.slice(0, at)
      field = key.slice(at + 1)
    }
    return this.items.filter(item => item.field === field && (scope === undefined || item.scope === scope))
  }
  has(key, scope) { return this.match(key, scope).length > 0 }
  first(key, scope) {
    const item = this.match(key, scope)[0]
    return item ? item.msg : undefined
  }
  any(scope) { return scope === undefined ? this.items.length > 0 : this.items.some(item => item.scope === scope) }
  collect(key) { return this.items.filter(item => !key || item.field === key).map(item => item.msg) }
  count() { return this.items.length }
  clear(scope) { this.items = scope === undefined ? [] : this.items.filter(item => item.scope !== scope) }
  remove(field, scope) { this.items = this.items.filter(item => !(item.field === field && (scope === undefined || item.scope === scope))) }
  add(error) { this.remove(error.field, error.scope); this.items.push(error) }
}

// ---------------------------------------------------------------- validator (one per component)
class Validator {
  constructor(getErrors) {
    this.getErrors = getErrors
    this.fields = new Set()
  }
  get errors() { return this.getErrors() }
  attach(field) { this.fields.add(field) }
  detach(field) {
    this.fields.delete(field)
    this.errors.remove(field.name, field.scope)
  }
  validateField(field) {
    if (!field.name) return true
    const failed = check(field.read(), field.rules)
    if (failed) this.errors.add({ id: `${field.scope || ''}.${field.name}`, field: field.name, scope: field.scope, rule: failed.name, msg: message(failed, field.name) })
    else this.errors.remove(field.name, field.scope)
    field.validated = true
    return !failed
  }
  // Resolves true when every field (of the scope) is valid; their messages show either way
  validateAll(scope) {
    let valid = true
    this.fields.forEach((field) => {
      if (typeof scope === 'string' && field.scope !== scope) return
      if (!this.validateField(field)) valid = false
    })
    return Promise.resolve(valid)
  }
  validate(key) {
    if (!key) return this.validateAll()
    let valid = true
    this.fields.forEach((field) => {
      const full = field.scope ? `${field.scope}.${field.name}` : field.name
      if (full === key || field.name === key) valid = this.validateField(field) && valid
    })
    return Promise.resolve(valid)
  }
  // Clear the messages and treat the fields as untouched again
  reset(scope) {
    this.errors.clear(typeof scope === 'string' ? scope : undefined)
    this.fields.forEach((field) => { field.validated = false })
    return Promise.resolve()
  }
  // Messages follow the language switch
  relabel() {
    this.fields.forEach((field) => { if (this.errors.has(field.name, field.scope)) this.validateField(field) })
  }
}

// ---------------------------------------------------------------- directive
// The value of the control: a component's v-model (modelValue / value prop), else the element's own value
function reader(el, vnode, owner) {
  const instance = vnode.ctx
  if (instance && instance !== owner && instance.props) {
    return () => ('modelValue' in instance.props ? instance.props.modelValue : instance.props.value)
  }
  return () => (el.type === 'checkbox' ? el.checked : el.value)
}

function nameOf(el, vnode, owner) {
  const instance = vnode.ctx && vnode.ctx !== owner ? vnode.ctx : null
  const fromComponent = instance && ((instance.attrs && instance.attrs.name) || (instance.props && instance.props.name))
  if (fromComponent) return fromComponent
  if (el.getAttribute('name')) return el.getAttribute('name')
  const inner = el.querySelector && el.querySelector('[name]')
  return inner ? inner.getAttribute('name') : null
}

const vValidate = {
  mounted(el, binding, vnode) {
    const owner = binding.instance && binding.instance.$
    const validator = binding.instance && binding.instance.$validator
    if (!validator) return
    const scopeEl = el.closest && el.closest('[data-vv-scope]')
    const blurOnly = (el.getAttribute('data-vv-validate-on') || '') === 'blur'
    const field = {
      name: nameOf(el, vnode, owner),
      scope: scopeEl ? scopeEl.getAttribute('data-vv-scope') : undefined,
      rules: parseRules(binding.value),
      read: reader(el, vnode, owner),
      validated: false,
    }
    field.last = field.read()
    const run = () => validator.validateField(field)
    field.onInput = () => { if (!blurOnly) run() }
    field.onBlur = () => run()
    field.blurOnly = blurOnly
    el.addEventListener('input', field.onInput)
    el.addEventListener('change', field.onInput)
    el.addEventListener('focusout', field.onBlur)
    el.__vvField = field
    validator.attach(field)
  },
  updated(el, binding, vnode) {
    const field = el.__vvField
    if (!field) return
    field.rules = parseRules(binding.value)
    field.read = reader(el, vnode, binding.instance && binding.instance.$)
    // A value set by the control itself (date picker, select, ...) is checked like typing
    const value = field.read()
    if (value !== field.last) {
      field.last = value
      if (!field.blurOnly && binding.instance && binding.instance.$validator) binding.instance.$validator.validateField(field)
    }
  },
  beforeUnmount(el, binding) {
    const field = el.__vvField
    if (!field) return
    el.removeEventListener('input', field.onInput)
    el.removeEventListener('change', field.onInput)
    el.removeEventListener('focusout', field.onBlur)
    if (binding.instance && binding.instance.$validator) binding.instance.$validator.detach(field)
    delete el.__vvField
  },
}

// Components that declare their own `errors` prop (e.g. DFormTabs gets its form's errors) keep it
const ownsErrors = options => {
  const props = options.props
  return !!props && (Array.isArray(props) ? props.includes('errors') : 'errors' in props)
}

const validators = new Set()

// Visible messages are re-worded after a language switch
onLocaleChange(() => validators.forEach(validator => validator.relabel()))

export default {
  install(app) {
    app.directive('validate', vValidate)
    app.mixin({
      data() {
        return ownsErrors(this.$options) ? {} : { errors: reactive(new ErrorBag()) }
      },
      beforeCreate() {
        if (ownsErrors(this.$options)) return
        this.$validator = new Validator(() => this.errors)
        validators.add(this.$validator)
      },
      beforeUnmount() {
        if (this.$validator) validators.delete(this.$validator)
      },
    })
  },
}

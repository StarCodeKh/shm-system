/*=========================================================================================
  $vs: the app's loading spinner, toasts and confirm box (same API as Vuesax 3 had).
    this.$vs.loading()                 full-page spinner (one at a time)
    this.$vs.loading({ container })    spinner inside an element
    this.$vs.loading.close(el?)        close the page spinner, or the one inside el
    this.$vs.notify({ title, text, color, ... })   toast, translated (see i18n.js)
    this.$vs.dialog({ type: 'confirm', title, text, accept, ... })
    this.$vs.rtl                       right-to-left layout (reactive)
==========================================================================================*/
import { createVNode, reactive, render } from 'vue'
import VsLoading from './VsLoading.vue'
import VsNotification from './VsNotification.vue'
import VsDialog from './VsDialog.vue'
import { translateNotify, translateDialog } from '@/i18n/i18n'

let appContext = null

// Render a component outside the page tree, with the app's components available to it
function mount(component, props) {
  const holder = document.createElement('div')
  const vnode = createVNode(component, props)
  vnode.appContext = appContext
  render(vnode, holder)
  return { vnode, unmount: () => render(null, holder) }
}

// ---------------------------------------------------------------- loading
// A page that opens a spinner on create while its table opens another showed two spinners:
// now a second page spinner while one is showing does nothing. While the startup screen
// (#loading-bg) is up, a page spinner waits until it is gone, unless the page closed it first.
const pageSpinnerShown = () => Array.from(document.querySelectorAll('body > .con-vs-loading')).some(el => !el.classList.contains('beforeRemove'))
const startupScreen = () => document.getElementById('loading-bg')
const unmounts = new WeakMap()
let pending = null

function openSpinner(options = {}) {
  let container = document.body
  if (options.container) container = options.container instanceof Element ? options.container : document.querySelector(options.container)
  if (!container) return
  const { vnode, unmount } = mount(VsLoading, {
    type: options.type || 'default',
    background: options.background,
    color: options.color,
    scale: options.scale,
    text: options.text,
    clickEffect: options.clickEffect,
  })
  const el = vnode.el
  if (!el) return unmount()
  unmounts.set(el, unmount)
  container.insertBefore(el, container.firstChild)
}

function whenStartupGone(callback) {
  const screen = startupScreen()
  if (!screen || !screen.parentNode) return callback()
  const observer = new MutationObserver(() => {
    if (!startupScreen()) { observer.disconnect(); callback() }
  })
  observer.observe(screen.parentNode, { childList: true })
}

function loading(options) {
  if (options && options.container) return openSpinner(options)
  if (pageSpinnerShown() || pending) return
  if (startupScreen()) {
    pending = { options }
    whenStartupGone(() => {
      const request = pending
      pending = null
      if (request && !pageSpinnerShown()) openSpinner(request.options)
    })
    return
  }
  openSpinner(options)
}

loading.close = function close(elx) {
  if (!elx) pending = null // the page finished before the startup screen was gone
  const spinners = elx instanceof Element
    ? Array.from(elx.children).filter(el => el.classList.contains('con-vs-loading'))
    : document.querySelectorAll(elx || 'body > .con-vs-loading')
  spinners.forEach((spinner) => {
    spinner.classList.add('beforeRemove')
    setTimeout(() => {
      const unmount = unmounts.get(spinner)
      unmount ? unmount() : spinner.remove()
      unmounts.delete(spinner)
    }, 300)
  })
}

// ---------------------------------------------------------------- notify
// The same toast (type, title and text) shown again within this time is skipped,
// e.g. when several requests fail at once
const REPEAT_WINDOW = 2500
const recentToasts = {}

function notify(parameters) {
  const options = translateNotify(parameters)
  if (!options || typeof options !== 'object') return
  const key = [options.color, options.title, options.text].join('|')
  const now = Date.now()
  if (recentToasts[key] && now - recentToasts[key] < REPEAT_WINDOW) return
  recentToasts[key] = now

  if (options.fullWidth && options.position) options.position = options.position.replace('right', 'left')
  let done = null
  const { vnode, unmount } = mount(VsNotification, { ...options, onGone: () => done && done() })
  done = unmount
  if (vnode.el) document.body.insertBefore(vnode.el, document.body.firstChild)
}

// ---------------------------------------------------------------- dialog
function dialog(parameters) {
  const options = translateDialog(parameters)
  let close = null
  const { unmount } = mount(VsDialog, {
    isPrompt: false,
    text: options.text,
    title: options.title || 'Dialog',
    color: options.color,
    type: options.type || 'alert',
    buttonAccept: options.buttonAccept || 'filled',
    buttonCancel: options.buttonCancel || 'flat',
    acceptText: options.acceptText || 'Accept',
    cancelText: options.cancelText || 'Cancel',
    closeIcon: options.closeIcon || 'close',
    iconPack: options.iconPack || 'material-icons',
    isValid: options.isValid || 'none',
    accept: options.accept || null,
    cancel: options.cancel || null,
    parameters: options.parameters,
    // Leave time for the closing animation
    'onUpdate:active': open => { if (!open) setTimeout(() => close && close(), 400) },
  })
  close = unmount
}

// Theme colors ({ primary: '#28C76F', ... }) as the "r,g,b" CSS variables vuesax.css reads (--vs-primary, ...)
function setColors(colors = {}) {
  Object.entries(colors).forEach(([name, hex]) => {
    const value = parseInt(hex.replace('#', ''), 16)
    document.documentElement.style.setProperty(`--vs-${name}`, `${value >> 16 & 255},${value >> 8 & 255},${value & 255}`)
  })
}

export function installVs(app, options = {}) {
  appContext = app._context
  setColors(options.colors)
  const vs = reactive({ rtl: !!options.rtl, theme: options.theme || null })
  vs.loading = loading
  vs.notify = notify
  vs.dialog = dialog
  app.config.globalProperties.$vs = vs
  return vs
}

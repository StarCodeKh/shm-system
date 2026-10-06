/*=========================================================================================
  File Name: loading.js
  Description: One full-page loading spinner at a time.

  Vuesax adds a new overlay on every $vs.loading() call, so a page that opens one on
  create while its table opens another showed two spinners stacked. Now a second call
  while one is showing does nothing, and $vs.loading.close() still closes it.
  Spinners inside a container ($vs.loading({ container })) are unchanged.

  While the app's startup screen (#loading-bg) is still showing, a page spinner waits:
  it opens when the startup screen is gone, unless the page already closed it.
==========================================================================================*/

// A page-level spinner that is showing and not already closing
const pageSpinnerShown = () =>
  Array.from(document.querySelectorAll('body > .con-vs-loading'))
    .some(el => !el.classList.contains('beforeRemove'))

// Startup screen from application.blade.php, removed by router.afterEach
const startupScreen = () => document.getElementById('loading-bg')

// A spinner asked for during the startup screen: opened once the screen is gone
let pending = null

function whenStartupGone(callback) {
  const screen = startupScreen()
  if (!screen || !screen.parentNode) return callback()
  const observer = new MutationObserver(() => {
    if (!startupScreen()) { observer.disconnect(); callback() }
  })
  observer.observe(screen.parentNode, { childList: true })
}

function singleLoading(open) {
  if (!open || open.__single) return open
  const wrapped = function (options) {
    if (options && options.container) return open.call(this, options)
    if (pageSpinnerShown() || pending) return
    if (startupScreen()) {
      pending = { options }
      whenStartupGone(() => {
        const request = pending
        pending = null
        if (request && !pageSpinnerShown()) open.call(this, request.options)
      })
      return
    }
    return open.call(this, options)
  }
  wrapped.close = function (elx) {
    if (!elx) pending = null // the page finished before the startup screen was gone
    return open.close && open.close(elx)
  }
  wrapped.__single = true
  return wrapped
}

// Must be installed after Vuesax: Vuesax re-assigns $vs.loading in each component's
// beforeCreate, so it is wrapped again right after.
export function installSingleLoading(VueCtor) {
  VueCtor.mixin({
    beforeCreate() {
      const vs = this.$vs
      if (!vs || !vs.loading) return
      vs.loading = singleLoading(vs.loading)
    }
  })
}

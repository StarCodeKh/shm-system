// Auto-grow fallback for browsers without CSS `field-sizing: content` (e.g. Firefox):
// textareas without a `rows` attribute grow with their text, up to the CSS max-height.
const supported = typeof CSS !== 'undefined' && CSS.supports && CSS.supports('field-sizing', 'content')

function fit(el) {
  if (!el || el.tagName !== 'TEXTAREA' || el.hasAttribute('rows')) return
  el.style.height = 'auto'
  el.style.height = el.scrollHeight + 2 + 'px'
}

if (!supported && typeof document !== 'undefined') {
  document.addEventListener('input', e => fit(e.target))
  // Values loaded from the API arrive without an input event: fit on focus and after renders
  document.addEventListener('focusin', e => fit(e.target))
  new MutationObserver(() => {
    document.querySelectorAll('textarea:not([rows])').forEach(fit)
  }).observe(document.documentElement, { childList: true, subtree: true })
}

// Textareas without `rows` show one line at rest and expand to their text while focused.
// CSS does it with `field-sizing: content`; this is the fallback for browsers without it (e.g. Firefox).
const supported = typeof CSS !== 'undefined' && CSS.supports && CSS.supports('field-sizing', 'content')

const isAuto = el => el && el.tagName === 'TEXTAREA' && !el.hasAttribute('rows')

function fit(el) {
    el.style.height = 'auto'
    el.style.height = el.scrollHeight + 2 + 'px'
}

if (!supported && typeof document !== 'undefined') {
    document.addEventListener('focusin', e => isAuto(e.target) && fit(e.target))
    document.addEventListener('input', e => isAuto(e.target) && document.activeElement === e.target && fit(e.target))
    // Back to one line when leaving the field
    document.addEventListener('focusout', e => isAuto(e.target) && (e.target.style.height = ''))
}

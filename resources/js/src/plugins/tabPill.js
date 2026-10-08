/*=========================================================================================
    File Name: tabPill.js
    Description: v-tab-pill — one pill that slides under the active item of a tab bar or a
    segmented switch, with the same motion as every other tab (--tab-move / --tab-ease).

    <div v-tab-pill class="my-tabs">
        <button :class="{ 'is-active': ... }">…</button>
    </div>

    - The active item is the child matching ".is-active", ".active" or [aria-selected="true"]
      (or the selector given: v-tab-pill="'.selected'").
    - The pill is the bar's ::before (no extra element for Vue to manage), placed from
      --pill-x / --pill-y / --pill-w / --pill-h. Its look comes from the bar's CSS:
      --pill-bg, --pill-radius, --pill-shadow (see .tab-pill-host in custom.scss).
    - It follows clicks, resizes, late fonts and tabs added or removed; the first placement
      doesn't slide in from the corner.
==========================================================================================*/

const ACTIVE = '.is-active, .active, [aria-selected="true"]'

function place(el) {
    const state = el._tabPill
    if (!state) return
    const active = Array.from(el.children).find(child => child.matches(state.selector))
    if (!active || !el.offsetParent) {
        el.style.setProperty('--pill-on', '0')
        return
    }
    el.style.setProperty('--pill-x', `${active.offsetLeft}px`)
    el.style.setProperty('--pill-y', `${active.offsetTop}px`)
    el.style.setProperty('--pill-w', `${active.offsetWidth}px`)
    el.style.setProperty('--pill-h', `${active.offsetHeight}px`)
    el.style.setProperty('--pill-on', '1')
    if (!el.classList.contains('is-ready')) requestAnimationFrame(() => el.classList.add('is-ready'))
}

export const tabPill = {
    mounted(el, binding) {
        const state = { selector: binding.value || ACTIVE }
        el._tabPill = state
        el.classList.add('tab-pill-host')
        const update = () => place(el)
        // The active item changed (class / aria-selected), or items were added or removed
        state.mutations = new MutationObserver(update)
        state.mutations.observe(el, { subtree: true, childList: true, attributes: true, attributeFilter: ['class', 'aria-selected'] })
        // Size changes: window, a hidden bar becoming visible, labels in another language
        if (typeof ResizeObserver !== 'undefined') {
            state.resize = new ResizeObserver(update)
            state.resize.observe(el)
        }
        if (document.fonts && document.fonts.ready) document.fonts.ready.then(update)
        update()
    },
    updated(el, binding) {
        if (el._tabPill && binding.value) el._tabPill.selector = binding.value
        place(el)
    },
    unmounted(el) {
        const state = el._tabPill
        if (!state) return
        state.mutations.disconnect()
        if (state.resize) state.resize.disconnect()
        delete el._tabPill
    },
}

export default {
    install(app) {
        app.directive('tab-pill', tabPill)
    },
}

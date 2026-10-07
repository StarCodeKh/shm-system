/*=========================================================================================
    File Name: tabsScroll.js
    Description: Top tab bars (see custom.scss): a white pill slides to the active tab, and
    the track scrolls sideways when the tabs don't fit.

    - The pill is Vuesax's .line-vs-tabs, placed from --tab-pill-x / --tab-pill-w measured
        here; it follows tab changes, resizes and late-loading fonts.
    - A clicked tab that is partly hidden scrolls fully into view, and a tab bar that opens
        on a tab further along shows that tab.
==========================================================================================*/

const TRACK = '.vs-tabs-position-top > .con-ul-tabs'

// Position of the tab inside the track's scrollable content
const measure = (track, tab) => {
    const left = tab.getBoundingClientRect().left - track.getBoundingClientRect().left - track.clientLeft + track.scrollLeft
    return { left, right: left + tab.offsetWidth }
}

// Scroll only the track (not the page) so the tab is fully visible
const reveal = (track, tab, smooth) => {
    const { left, right } = measure(track, tab)
    let target = null
    if (left < track.scrollLeft) target = left - 4
    else if (right > track.scrollLeft + track.clientWidth) target = right - track.clientWidth + 4
    if (target !== null) track.scrollTo({ left: target, behavior: smooth ? 'smooth' : 'auto' })
}

const placePill = (track) => {
    const active = track.querySelector('.vs-tabs--li.activeChild')
    if (!active || !track.offsetParent) return
    const { left } = measure(track, active)
    track.style.setProperty('--tab-pill-x', `${left}px`)
    track.style.setProperty('--tab-pill-w', `${active.offsetWidth}px`)
    // First placement happens without the slide; later changes animate
    if (!track.classList.contains('has-tab-pill')) requestAnimationFrame(() => track.classList.add('has-tab-pill'))
}

document.addEventListener('click', (event) => {
    const tab = event.target.closest && event.target.closest(`${TRACK} .vs-tabs--li`)
    if (tab) reveal(tab.closest(TRACK), tab, true)
})

// Watch each tab bar once it appears
const seen = new WeakSet()
const resize = typeof ResizeObserver !== 'undefined'
    ? new ResizeObserver(entries => entries.forEach(entry => {
        const track = entry.target.closest(TRACK)
        if (track) placePill(track)
    }))
    : null

const setup = (track) => {
    seen.add(track)
    // Active tab changed (Vuesax toggles .activeChild), or tabs were added/removed
    new MutationObserver(() => placePill(track))
        .observe(track, { subtree: true, childList: true, attributes: true, attributeFilter: ['class'] })
    if (resize) {
        resize.observe(track) // also fires when a hidden tab bar becomes visible
        const list = track.querySelector('.vs-tabs--ul')
        if (list) resize.observe(list) // tab widths change when fonts finish loading
    }
    placePill(track)
    const active = track.querySelector('.vs-tabs--li.activeChild')
    if (active && track.offsetParent) reveal(track, active, false)
}

let queued = false
new MutationObserver(() => {
    if (queued) return
    queued = true
    requestAnimationFrame(() => {
        queued = false
        document.querySelectorAll(TRACK).forEach(track => { if (!seen.has(track)) setup(track) })
    })
}).observe(document.documentElement, { childList: true, subtree: true })

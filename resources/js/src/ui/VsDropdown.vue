<template>
    <!-- A plain wrapper: the trigger inside (a <button>) is the control, so buttons are never nested -->
    <div
        ref="dropdown"
        v-bind="$attrs"
        class="vs-con-dropdown parent-dropdown"
        @click="onClick"
        @contextmenu="onContextmenu"
        @mouseover="onHover('over', $event)"
        @mouseout="onHover('out', $event)">
        <slot />
    </div>
</template>

<script>
// Below this width, menus of the top bar open as a full-width sheet (see changePositionMenu)
const SHEET_BELOW = 640

// What counts as choosing something inside a menu (closes it)
const CHOICE = 'a, button, [role="menuitem"], .vs-dropdown--item'

// Trigger + <vs-dropdown-menu> (+ <vs-dropdown-item>s). The menu is drawn in <body>, placed under the trigger
// (above it near the bottom of the screen), and closes on an outside click or when an item is chosen.
export default {
    name: 'VsDropdown',
    inheritAttrs: false,
    emits: ['click', 'focus', 'blur'],
    props: {
        vsTriggerClick: { default: false, type: Boolean },
        vsTriggerContextmenu: { default: false, type: Boolean },
        color: { default: 'primary', type: String },
        vsCustomContent: { default: false, type: Boolean },
        vsDropRight: { default: false, type: Boolean },
    },
    provide() {
        return { vsDropdown: this }
    },
    data() {
        return { vsDropdownVisible: false, rightx: false, menu: null }
    },
    watch: {
        // Going to another page closes any open menu
        '$route.fullPath'() {
            if (this.vsDropdownVisible) this.close()
        },
        vsDropdownVisible(visible) {
            this.changePositionMenu()
            this.$emit(visible ? 'focus' : 'blur')
            // Esc closes an open menu
            if (visible) document.addEventListener('keydown', this.onKeydown)
            else document.removeEventListener('keydown', this.onKeydown)
            // An open menu follows the window (phone rotated, window resized)
            if (visible) window.addEventListener('resize', this.changePositionMenu)
            else window.removeEventListener('resize', this.changePositionMenu)
        },
    },
    mounted() {
        document.addEventListener('click', this.clickOutside)
    },
    beforeUnmount() {
        document.removeEventListener('click', this.clickOutside)
        document.removeEventListener('keydown', this.onKeydown)
        window.removeEventListener('resize', this.changePositionMenu)
    },
    methods: {
        setVisible(visible) {
            this.vsDropdownVisible = visible
        },
        close() {
            this.vsDropdownVisible = false
        },
        onKeydown(evt) {
            if (evt.key === 'Escape') this.close()
        },
        clickOutside(evt) {
            if (!(this.vsTriggerClick || this.vsCustomContent) || !this.vsDropdownVisible) return
            if (this.$refs.dropdown && this.$refs.dropdown.contains(evt.target)) return
            const options = this.menu && this.menu.$refs.options
            if (options && options.contains(evt.target)) {
                // Choosing something in the menu closes it (it fades out while the action runs);
                // controls marked data-keep-open (e.g. "mark all as read") leave it open
                const choice = evt.target.closest(CHOICE)
                if (choice && options.contains(choice) && !evt.target.closest('[data-keep-open]')) this.close()
                return
            }
            if (evt.target.closest && evt.target.closest('.vs-dropdown--menu')) return
            this.close()
        },
        onClick(evt) {
            if (!this.vsTriggerContextmenu) this.toggle(evt)
            if (this.$el === evt.target) this.$emit('click')
        },
        onContextmenu(evt) {
            if (this.vsTriggerContextmenu) {
                evt.preventDefault()
                this.toggle(evt)
            }
        },
        toggle(evt) {
            if (this.vsTriggerClick || this.vsTriggerContextmenu) {
                const inMenu = evt.target.closest && evt.target.closest('.vs-dropdown--menu')
                this.setVisible(!(this.vsDropdownVisible && !inMenu))
            }
            this.$emit('click')
        },
        // Without vs-trigger-click the menu opens on hover
        onHover(type, evt) {
            if (this.vsTriggerClick || this.vsTriggerContextmenu) return
            if (type == 'over') this.setVisible(true)
            else if (!(evt.relatedTarget && evt.relatedTarget.classList && evt.relatedTarget.classList.contains('vs-dropdown-menu'))) this.setVisible(false)
        },
        changePositionMenu() {
            const menu = this.menu
            const trigger = this.$refs.dropdown
            if (!menu || !trigger) return
            const scrollTop = window.pageYOffset || document.documentElement.scrollTop
            if (trigger.getBoundingClientRect().top + 300 >= window.innerHeight) {
                this.$nextTick(() => {
                    const el = menu.$refs.options
                    menu.topx = trigger.getBoundingClientRect().top - (el ? el.clientHeight : 0) - 7 + scrollTop
                    menu.notHeight = true
                })
            } else {
                menu.notHeight = false
                // Menus in the top bar open just below the bar instead of over it
                const bar = trigger.closest('.vx-navbar')
                menu.topx = (bar ? bar.getBoundingClientRect().bottom + 8 : trigger.getBoundingClientRect().top + trigger.clientHeight - 5) + scrollTop
            }
            // Line the menu up with the trigger's right edge (left edge with vs-drop-right), keep it 8px inside
            // the screen, and point the arrow at the middle of the trigger. leftx is the menu's right edge
            // (vuesax.css shifts the menu left by its own width).
            // Phones: top-bar menus keep their content width but all line up with the screen's right edge
            // (8px in), so every one of them (notifications, profile…) sits the same way whatever icon opened it.
            this.$nextTick(() => {
                const el = menu.$refs.options
                if (!el) return
                const rect = trigger.getBoundingClientRect()
                const gap = 8
                const screen = document.documentElement.clientWidth
                const sheet = !!trigger.closest('.vx-navbar') && screen < SHEET_BELOW
                el.classList.toggle('is-sheet', sheet)
                const width = el.offsetWidth
                let left = sheet ? screen - width - gap : this.vsDropRight ? rect.left : rect.right - width
                left = Math.max(gap, Math.min(left, screen - width - gap))
                menu.leftx = left + width + (window.pageXOffset || document.documentElement.scrollLeft)
                menu.arrowx = rect.left + rect.width / 2 - left
                this.rightx = false
            })
        },
    },
}
</script>

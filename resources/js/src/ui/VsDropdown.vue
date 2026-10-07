<template>
    <button
        ref="dropdown"
        v-bind="$attrs"
        type="button"
        class="vs-con-dropdown parent-dropdown"
        @click="onClick"
        @contextmenu="onContextmenu"
        @mouseover="onHover('over', $event)"
        @mouseout="onHover('out', $event)">
        <slot />
    </button>
</template>

<script>
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
        vsDropdownVisible(visible) {
            this.changePositionMenu()
            this.$emit(visible ? 'focus' : 'blur')
        },
    },
    mounted() {
        document.addEventListener('click', this.clickOutside)
    },
    beforeUnmount() {
        document.removeEventListener('click', this.clickOutside)
    },
    methods: {
        setVisible(visible) {
            this.vsDropdownVisible = visible
        },
        close() {
            this.vsDropdownVisible = false
        },
        clickOutside(evt) {
            if (!(this.vsTriggerClick || this.vsCustomContent) || !this.vsDropdownVisible) return
            if (this.$refs.dropdown && this.$refs.dropdown.contains(evt.target)) return
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
                menu.topx = trigger.getBoundingClientRect().top + trigger.clientHeight + scrollTop - 5
            }
            // Line the menu up with the trigger's right edge (left edge with vs-drop-right), keep it 8px inside
            // the screen, and point the arrow at the middle of the trigger. leftx is the menu's right edge
            // (vuesax.css shifts the menu left by its own width).
            this.$nextTick(() => {
                const el = menu.$refs.options
                if (!el) return
                const rect = trigger.getBoundingClientRect()
                const width = el.offsetWidth
                const gap = 8
                const screen = document.documentElement.clientWidth
                let left = this.vsDropRight ? rect.left : rect.right - width
                left = Math.max(gap, Math.min(left, screen - width - gap))
                menu.leftx = left + width + (window.pageXOffset || document.documentElement.scrollLeft)
                menu.arrowx = rect.left + rect.width / 2 - left
                this.rightx = false
            })
        },
    },
}
</script>

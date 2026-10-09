<template>
    <!-- No <transition>: the content fades up with the same motion as every tab in the app -->
    <!-- (tab-content-in in custom.css restarts each time the tab is shown). Vuesax's sideways slide -->
    <!-- ran on top of it, so switching tabs jumped sideways and briefly overlapped the old tab. -->
    <div v-if="active || opened" v-show="active" class="con-tab vs-tabs--content">
        <slot />
    </div>
</template>

<script>
import { refreshLists } from './utils'

// A tab builds its content the first time it is shown and keeps it after that (hidden while another
// tab is active), so switching back is instant. On coming back its lists reload quietly in the background.

export default {
    name: 'VsTab',
    inheritAttrs: false,
    inject: { vsTabs: { default: null } },
    props: {
        label: { default: 'Label', type: String },
        icon: { default: '', type: String },
        tag: { default: '', type: String },
        iconPack: { type: String, default: 'material-icons' },
    },
    data() {
        return { active: false, opened: false }
    },
    computed: {
        uid() {
            return this.$.uid
        },
    },
    watch: {
        active(now) {
            if (!now) return
            if (this.opened) this.$nextTick(() => refreshLists(this))
            this.opened = true
        },
    },
    created() {
        if (this.vsTabs) this.vsTabs.register(this)
    },
    beforeUnmount() {
        if (this.vsTabs) this.vsTabs.unregister(this)
    },
}
</script>

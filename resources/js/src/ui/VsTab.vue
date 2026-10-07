<template>
  <transition :name="transitionName">
    <div v-if="active || opened" v-show="active" class="con-tab vs-tabs--content">
      <slot />
    </div>
  </transition>
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
    return { vertical: false, active: false, invert: false, opened: false }
  },
  computed: {
    uid() {
      return this.$.uid
    },
    transitionName() {
      return `fade-tab${this.vertical ? '-vertical' : ''}${this.invert ? '-invert' : ''}`
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

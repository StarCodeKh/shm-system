/*=========================================================================================
  File Name: keepTabs.js
  Description: Tabs open instantly after the first visit.

  - Vuesax builds a tab's content only while it is active and throws it away on leaving,
    so every switch reloaded the tab (spinner, empty table, new request). Here a tab
    keeps its content once opened and is only hidden when another tab is active.
  - Coming back shows the kept content at once, then its lists reload quietly in the
    background, so the data stays current (e.g. a type added in one tab appears in
    another tab's list).
==========================================================================================*/

import Vue from 'vue'

// A list in this app loads its rows with getData() -> getDataTable() (see views/pages/settings/_Bank.vue)
const isList = vm => typeof vm.getData === 'function' && typeof vm.getDataTable === 'function'

// Reload every list inside `vm` without the full-screen spinner; the old rows stay until the new ones arrive
export function refreshLists(vm) {
  if (!vm) return
  vm.$children.forEach(child => (isList(child) ? child.getData() : refreshLists(child)))
}

const VsTab = Vue.options.components.VsTab

Vue.component('VsTab', VsTab.extend({
  data: () => ({ opened: false }),
  watch: {
    active(now) {
      if (!now) return
      if (this.opened) this.$nextTick(() => refreshLists(this))
      this.opened = true
    },
  },
  // Same markup and transitions as Vuesax, but v-show instead of v-if once the tab has been opened
  render(h) {
    const name = `fade-tab${this.vertical ? '-vertical' : ''}${this.invert ? '-invert' : ''}`
    const content = this.active || this.opened
      ? [h('div', { staticClass: 'con-tab vs-tabs--content', directives: [{ name: 'show', value: this.active }] }, this.$slots.default)]
      : []
    return h('transition', { attrs: { name } }, content)
  },
}))

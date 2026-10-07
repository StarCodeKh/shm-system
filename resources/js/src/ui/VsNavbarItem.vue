<template>
  <li
    class="vs-navbar--item"
    :class="[{ 'is-active-item': isActiveItem }, 'vs-navbar-item-' + activeTextColor]"
    :style="styleHover"
    @click="vsNavbar && vsNavbar.changeIndex(index)"
    @mouseout="hover = false"
    @mouseover="hover = true">
    <slot />
    <span class="vs-navbar-after" :style="{ background: getColor(activeTextColor) }" />
  </li>
</template>

<script>
import { color as vsColor } from './utils'

export default {
  name: 'VsNavbarItem',
  inject: { vsNavbar: { default: null } },
  props: {
    index: { type: [Number, String], default: null },
  },
  data() {
    return { hover: false }
  },
  computed: {
    activeTextColor() {
      return this.vsNavbar ? this.vsNavbar.activeTextColor : 'primary'
    },
    isActiveItem() {
      return !!this.vsNavbar && this.vsNavbar.modelValue == this.index
    },
    styleHover() {
      const active = this.isActiveItem || this.hover
      return { color: this.getColor(active ? this.activeTextColor : (this.vsNavbar && this.vsNavbar.textColor)) }
    },
  },
  methods: {
    getColor(value) {
      return vsColor.getColor(value)
    },
  },
}
</script>

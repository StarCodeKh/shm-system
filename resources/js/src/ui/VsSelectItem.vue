<template>
  <li class="vs-component vs-select--option" :data-text="text" @mouseover="hoverx = true" @mouseout="hoverx = false">
    <button
      ref="item"
      v-bind="$attrs"
      class="vs-select--item"
      :class="{ activex: isActive }"
      :style="styles"
      :disabled="disabled"
      type="button"
      name="button"
      @click="choose"
      @blur="onBlur"
      @keydown.down.prevent="move('nextElementSibling')"
      @keydown.up.prevent="move('previousElementSibling')"
      @keydown.enter.prevent="choose">
      <span>{{ text }}</span>
    </button>
  </li>
</template>

<script>
import { color as vsColor } from './utils'

export default {
  name: 'VsSelectItem',
  inheritAttrs: false,
  inject: { vsSelect: { default: null } },
  props: {
    disabled: { type: Boolean, default: false },
    value: { default: null },
    text: { default: null },
  },
  data() {
    return { hoverx: false }
  },
  computed: {
    isActive() {
      return !!this.vsSelect && this.vsSelect.modelValue == this.value
    },
    styles() {
      const color = this.vsSelect ? this.vsSelect.color : 'primary'
      return {
        background: this.isActive ? vsColor.getColor(color, 0.1) : null,
        color: this.isActive ? vsColor.getColor(color, 1) : null,
        fontWeight: this.isActive ? 'bold' : null,
      }
    },
  },
  created() {
    if (this.vsSelect) this.vsSelect.register(this)
  },
  beforeUnmount() {
    if (this.vsSelect) this.vsSelect.unregister(this)
  },
  methods: {
    choose() {
      if (!this.disabled && this.vsSelect) this.vsSelect.select(this.value)
    },
    // Leaving the list with Tab closes it
    onBlur(event) {
      const next = event.relatedTarget
      if (!(next && next.closest && next.closest('.vs-select--options')) && this.vsSelect) this.vsSelect.closeOptions()
    },
    move(direction) {
      const li = this.$el[direction]
      const button = li && li.querySelector && li.querySelector('.vs-select--item')
      if (button) button.focus()
    },
  },
}
</script>

<template>
  <button
    v-bind="innerAttrs"
    type="button"
    class="vs-component vs-switch"
    :class="[rootAttrs.class, 'vs-switch-' + color, { 'vs-switch-active': isChecked || innerAttrs.checked }]"
    :style="[rootAttrs.style, style]">
    <input
      ref="inputCheckbox"
      class="input-switch vs-switch--input"
      :disabled="innerAttrs.disabled"
      type="checkbox"
      :value="modelValue"
      @input="toggleValue">
    <span ref="on" class="text-on text-switch vs-switch--text" :class="{ 'active-text': isChecked || innerAttrs.checked }">
      <slot name="on" />
      <vs-icon class="icons-switch vs-switch--icon" :icon-pack="iconPack" :icon="vsIconOn || vsIcon" />
    </span>
    <span ref="off" class="text-off text-switch vs-switch--text" :class="{ 'active-text': !isChecked && !innerAttrs.checked }">
      <slot name="off" />
      <vs-icon class="icons-switch vs-switch--icon" :icon-pack="iconPack" :icon="vsIconOff || vsIcon" />
    </span>
    <span class="vs-circle-switch vs-switch--circle" />
  </button>
</template>

<script>
import { color as vsColor, splitAttrs } from './utils'

export default {
  name: 'VsSwitch',
  mixins: [splitAttrs],
  inheritAttrs: false,
  emits: ['update:modelValue', 'change'],
  props: {
    modelValue: {},
    color: { default: 'primary', type: String },
    vsIcon: { default: null, type: String },
    vsIconOn: { default: null, type: String },
    vsIconOff: { default: null, type: String },
    iconPack: { default: 'material-icons', type: String },
    vsValue: {},
  },
  data() {
    return { widthx: 42 }
  },
  computed: {
    style() {
      return { background: this.modelValue ? vsColor.getColor(this.color, 1) : null, width: `${this.widthx}px` }
    },
    isChecked() {
      return Array.isArray(this.modelValue) ? this.modelValue.includes(this.vsValue) : this.modelValue
    },
  },
  mounted() {
    // Wide enough for the longer of the on/off labels
    this.$nextTick(() => {
      const { on, off } = this.$refs
      if (on && off) this.widthx = Math.max(on.clientWidth, off.clientWidth) + 24
    })
  },
  methods: {
    toggleValue(evt) {
      if (Array.isArray(this.modelValue)) {
        const value = this.modelValue.slice(0)
        if (this.isChecked) value.splice(value.indexOf(this.vsValue), 1)
        else value.push(this.vsValue)
        this.$emit('update:modelValue', value)
      } else {
        this.$emit('update:modelValue', !this.modelValue)
      }
      this.$emit('change', evt)
    },
  },
}
</script>

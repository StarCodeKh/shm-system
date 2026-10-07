<template>
  <label class="vs-component con-vs-radio" :class="[rootAttrs.class, 'vs-radio-' + color]" :style="rootAttrs.style">
    <input
      v-bind="innerAttrs"
      class="vs-radio--input"
      :name="vsName || modelValue"
      type="radio"
      :checked="isChecked"
      :value="modelValue"
      @input="$emit('update:modelValue', vsValue)"
      @click="$emit('update:modelValue', vsValue)">
    <span class="vs-radio">
      <span class="vs-radio--borde" :style="styles" />
      <span class="vs-radio--circle" :style="styleCircle" />
    </span>
    <span class="vs-radio--label"><slot /></span>
  </label>
</template>

<script>
import { color as vsColor, splitAttrs } from './utils'

export default {
  name: 'VsRadio',
  mixins: [splitAttrs],
  inheritAttrs: false,
  emits: ['update:modelValue'],
  props: {
    modelValue: {},
    vsValue: {},
    vsName: {},
    color: { default: 'primary', type: String },
  },
  computed: {
    isChecked() {
      return this.vsValue == this.modelValue
    },
    styles() {
      return { border: `2px solid ${this.isChecked ? vsColor.getColor(this.color, 1) : 'rgb(200, 200, 200)'}` }
    },
    styleCircle() {
      return { background: vsColor.getColor(this.color, 1), 'box-shadow': `0px 3px 12px 0px ${vsColor.getColor(this.color, 0.4)}` }
    },
  },
}
</script>

<template>
  <div
    class="vs-component vs-con-textarea"
    :class="[rootAttrs.class, 'vs-textarea-' + color, { 'textarea-danger': counter ? (modelValue && modelValue.length > counter) : false, focusx: isFocus }]"
    :style="[rootAttrs.style, style]">
    <h4 v-if="label">{{ label }}</h4>
    <textarea
      v-bind="innerAttrs"
      class="vs-textarea"
      :value="modelValue"
      @input="$emit('update:modelValue', $event.target.value)"
      @focus="isFocus = true; $emit('focus')"
      @blur="isFocus = false; $emit('blur')" />
    <div v-if="counter" class="count vs-textarea--count">{{ modelValue ? modelValue.length : 0 }} / {{ counter }}</div>
  </div>
</template>

<script>
import { color as vsColor, splitAttrs } from './utils'

export default {
  name: 'VsTextarea',
  mixins: [splitAttrs],
  inheritAttrs: false,
  emits: ['update:modelValue', 'update:counterDanger', 'focus', 'blur'],
  props: {
    modelValue: {},
    label: { default: null, type: String },
    color: { default: 'primary', type: String },
    counter: { default: null, type: [Number, String] },
    counterDanger: { default: false, type: Boolean },
    height: { default: null, type: String },
    width: { default: null, type: String },
  },
  data() {
    return { isFocus: false }
  },
  computed: {
    style() {
      return { border: `1px solid ${this.isFocus ? vsColor.getColor(this.color, 1) : 'rgba(0, 0, 0,.08)'}`, height: this.height, width: this.width }
    },
  },
  watch: {
    modelValue(value) {
      this.$emit('update:counterDanger', !!(value && value.length > this.counter))
    },
  },
}
</script>

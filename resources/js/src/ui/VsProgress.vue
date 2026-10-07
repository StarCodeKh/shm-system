<template>
  <div class="vs-progress--background" :class="['vs-progress-' + color, { indeterminate }]" :style="{ background: getColor(0.1), height: `${height}px` }">
    <div class="vs-progress--foreground" :style="barStyle" />
    <div v-if="indeterminate" class="vs-progress--indeterminate" :style="barStyle" />
  </div>
</template>

<script>
import { color as vsColor } from './utils'

export default {
  name: 'VsProgress',
  props: {
    height: { type: [Number, String], default: 5 },
    indeterminate: { type: Boolean, default: false },
    percent: { type: Number, default: 0 },
    color: { type: String, default: 'primary' },
  },
  data() {
    return { percentx: 0 }
  },
  computed: {
    barStyle() {
      return { background: this.getColor(), width: `${this.percentx}%` }
    },
  },
  watch: {
    percent(value) {
      this.percentx = value
    },
  },
  mounted() {
    // Start from 0 so the bar animates to its value
    setTimeout(() => { this.percentx = this.percent }, 600)
  },
  methods: {
    getColor(alpha) {
      return alpha === undefined ? vsColor.getColor(this.color) : vsColor.getColor(this.color, alpha)
    },
  },
}
</script>

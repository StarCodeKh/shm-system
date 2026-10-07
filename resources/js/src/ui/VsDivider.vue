<template>
  <div class="vs-component vs-divider">
    <span class="vs-divider-border after" :class="borderClass" :style="lineStyle(widths[0])" />
    <span v-if="icon || $slots.default" class="vs-divider--text" :class="textAndBackgroundClass" :style="{ color: textColor, background: backgroundColor }">
      <slot v-if="!icon" />
      <vs-icon v-else class="icon-divider notranslate vs-divider--icon" :icon-pack="iconPack" :icon="icon" />
    </span>
    <span class="vs-divider-border before" :class="borderClass" :style="lineStyle(widths[1])" />
  </div>
</template>

<script>
import { color as vsColor } from './utils'

// Width of the line before / after the text for each position
const WIDTHS = { left: ['0%', '100%'], 'left-center': ['25%', '75%'], 'right-center': ['75%', '25%'], right: ['100%', '0%'] }

export default {
  name: 'VsDivider',
  props: {
    color: { type: String, default: 'rgba(0, 0, 0,.1)' },
    background: { type: String, default: 'transparent' },
    icon: { default: null, type: String },
    borderStyle: { default: 'solid', type: String },
    borderHeight: { default: '1px', type: String },
    position: { default: 'center', type: String },
    iconPack: { default: 'material-icons', type: String },
  },
  computed: {
    widths() {
      return WIDTHS[this.position] || ['100%', '100%']
    },
    borderClass() {
      return { [`vs-divider-border-${vsColor.isColor(this.color) ? this.color : 'default'}`]: true }
    },
    textColor() {
      if (!vsColor.isColor(this.color)) return vsColor.getColor(this.color !== 'rgba(0, 0, 0,.1)' ? this.color : null)
      return undefined
    },
    backgroundColor() {
      return vsColor.isColor(this.background) ? undefined : vsColor.getColor(this.background)
    },
    textAndBackgroundClass() {
      return {
        [`vs-divider-text-${vsColor.isColor(this.color) ? this.color : 'default'}`]: true,
        [`vs-divider-background-${vsColor.isColor(this.background) ? this.background : 'default'}`]: true,
      }
    },
  },
  methods: {
    lineStyle(width) {
      const style = { width, 'border-top-width': this.borderHeight, 'border-top-style': this.borderStyle }
      if (!vsColor.isColor(this.color)) style['border-top-color'] = vsColor.getColor(this.color)
      return style
    },
  },
}
</script>

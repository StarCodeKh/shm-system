<template>
  <div class="con-vs-avatar" :class="avatarClass" :style="avatarStyle">
    <div v-if="badge && badge > 0" class="dot-count vs-avatar--count" :class="badgeClass" :style="badgeStyle">
      {{ typeof badge != 'boolean' ? badge : null }}
    </div>
    <div v-if="src" class="con-img vs-avatar--con-img">
      <img :src="src" :alt="text">
    </div>
    <span v-else class="vs-avatar--text notranslate" :class="[text ? '' : iconPack, text ? '' : icon, textClass]" :style="textStyle" :title="text" translate="no">
      {{ text ? shortText : iconPack == 'material-icons' ? icon : '' }}
    </span>
  </div>
</template>

<script>
import { color as vsColor } from './utils'

export default {
  name: 'VsAvatar',
  props: {
    badge: { type: [Boolean, String, Number], default: false },
    badgeColor: { default: 'danger', type: String },
    size: { type: String, default: null },
    src: { type: String, default: null },
    icon: { type: String, default: 'person' },
    iconPack: { type: String, default: 'material-icons' },
    textColor: { type: String, default: 'rgb(255, 255, 255)' },
    text: { type: [String, Number], default: null },
    color: { type: String, default: 'rgb(195, 195, 195)' },
  },
  computed: {
    avatarClass() {
      return { [this.size]: true, [`con-vs-avatar-${this.color}`]: vsColor.isColor(this.color) }
    },
    avatarStyle() {
      const sized = /[px]/.test(this.size || '')
      const style = { width: sized ? this.size : null, height: sized ? this.size : null }
      if (!vsColor.isColor(this.color)) style.background = vsColor.getColor(this.color)
      return style
    },
    badgeClass() {
      return { badgeNumber: typeof this.badge != 'boolean', [`dot-count-${this.badgeColor}`]: vsColor.isColor(this.badgeColor) }
    },
    badgeStyle() {
      return vsColor.isColor(this.badgeColor) ? {} : { background: vsColor.getColor(this.badgeColor) }
    },
    textClass() {
      return { 'material-icons': !this.text, [`vs-avatar-text-${this.textColor}`]: vsColor.isColor(this.textColor) }
    },
    textStyle() {
      const style = { transform: `translate(-50%,-50%) scale(${this.scale})` }
      if (!vsColor.isColor(this.textColor)) style.color = vsColor.getColor(this.textColor)
      return style
    },
    // Up to 5 characters as given; longer text becomes its initials
    shortText() {
      const text = String(this.text)
      if (text.length <= 5) return text
      const letters = /\s/.test(text) ? text.split(/\s/g).map(word => (word[0] || '').toUpperCase()).join('') : text[0].toUpperCase()
      return letters.length > 5 ? letters[0] : letters
    },
    scale() {
      if (!this.text) return 1
      const length = this.shortText.length
      return length <= 5 && length > 1 ? length / (length * 1.5) : 1
    },
  },
}
</script>

<template>
  <button
    ref="btn"
    v-bind="$attrs"
    :type="button"
    name="button"
    class="vs-component vs-button"
    :class="['vs-button-' + (isColor ? color : null), 'vs-button-' + type, {
      isActive: isActive,
      includeIcon: icon,
      includeIconOnly: icon && !$slots.default,
      'vs-radius': radius,
    }, size, size ? 'vs-button-' + size : null]"
    :style="[styles, { width: /[px]/.test(size) ? size : null, height: /[px]/.test(size) ? size : null }]"
    @click="clickButton"
    @blur="blurButton"
    @mouseover="hoverx = true; $emit('mouseover', $event)"
    @mouseout="hoverx = false; $emit('mouseout', $event)">
    <span v-if="!is('line') && !is('gradient') && !is('relief')" ref="backgroundx" class="vs-button-backgroundx vs-button--background" :style="stylesBackGround" />
    <vs-icon v-if="icon" class="vs-button--icon" :style="iconStyle" :icon-pack="iconPack" :icon="icon" />
    <span v-if="$slots.default" class="vs-button-text vs-button--text"><slot /></span>
    <span ref="linex" class="vs-button-linex" :style="styleLine" />
  </button>
</template>

<script>
import { color } from './utils'

export default {
  name: 'VsButton',
  inheritAttrs: false,
  emits: ['click', 'blur', 'mouseover', 'mouseout', 'routeErr'],
  props: {
    type: { default: 'filled', type: String },
    color: { default: 'primary', type: String },
    textColor: { default: null, type: String },
    lineOrigin: { default: 'center', type: String },
    linePosition: { default: 'bottom', type: String },
    gradientDirection: { default: '30deg', type: String },
    gradientColorSecondary: { default: 'primary', type: String },
    size: { type: String, default: null },
    icon: { type: String, default: null },
    iconPack: { type: String, default: 'material-icons' },
    iconAfter: { default: false, type: Boolean },
    radius: { default: false, type: Boolean },
    to: { default: false, type: [String, Object, Boolean] },
    href: { default: '', type: [String, Object] },
    target: { default: false, type: [Boolean, String] },
    button: { default: 'button', type: String },
  },
  data() {
    return { isActive: false, hoverx: false, leftBackgorund: 20, topBackgorund: 20, radio: 0, time: 0.3, timeOpacity: 0.3, opacity: 1 }
  },
  computed: {
    isColor() {
      return color.isColor(this.color)
    },
    styles() {
      if (this.is('filled')) {
        return {
          color: color.getColor(this.textColor, 1),
          background: color.getColor(this.color, 1),
          boxShadow: this.hoverx ? `0px 8px 25px -8px ${color.getColor(this.color, 1)}` : null,
        }
      }
      if (this.is('border') || this.is('flat')) {
        return {
          border: `${this.is('flat') ? 0 : 1}px solid ${color.getColor(this.color, 1)}`,
          background: this.hoverx ? color.getColor(this.color, 0.1) : 'transparent',
          color: color.getColor(this.textColor, 1) || color.getColor(this.color, 1),
        }
      }
      if (this.is('line')) {
        return {
          color: color.getColor(this.textColor, 1) || color.getColor(this.color, 1),
          borderBottomWidth: this.linePosition == 'bottom' ? '2px' : null,
          borderColor: `${color.getColor(this.color, 0.2)}`,
          borderTopWidth: this.linePosition == 'top' ? '2px' : null,
        }
      }
      if (this.is('gradient')) {
        return { background: `linear-gradient(${this.gradientDirection}, ${color.getColor(this.color)} 0%, ${color.getColor(this.gradientColorSecondary, 1)} 100%)` }
      }
      if (this.is('relief')) {
        return { background: color.getColor(this.color, 1), boxShadow: `0 3px 0 0 ${color.getColor(this.color, 1)}` }
      }
      return {}
    },
    stylesBackGround() {
      return {
        background: this.is('flat') || this.is('border') ? color.getColor(this.color, 1, false) : null,
        opacity: this.opacity,
        left: `${this.leftBackgorund}px`,
        top: `${this.topBackgorund}px`,
        width: `${this.radio}px`,
        height: `${this.radio}px`,
        transition: `width ${this.time}s ease, height ${this.time}s ease, opacity ${this.timeOpacity}s ease`,
      }
    },
    styleLine() {
      let lineOrigin = '50%'
      if (this.lineOrigin == 'left') lineOrigin = '0%'
      else if (this.lineOrigin == 'right') lineOrigin = 'auto'
      return {
        top: this.linePosition == 'top' ? '-2px' : 'auto',
        bottom: this.linePosition == 'bottom' ? '-2px' : 'auto',
        background: color.getColor(this.color, 1),
        left: lineOrigin,
        right: lineOrigin == 'auto' ? '0px' : null,
        transform: lineOrigin == '50%' ? 'translate(-50%)' : null,
      }
    },
    iconStyle() {
      const hasText = !!this.$slots.default
      return {
        order: this.iconAfter ? 2 : 0,
        ['margin-' + this.side('left')]: hasText && !this.iconAfter ? '5px' : '0px',
        ['margin-' + this.side('right')]: hasText && this.iconAfter ? '5px' : '0px',
      }
    },
  },
  methods: {
    // Vuesax swapped left/right outside RTL for the icon gap
    side(value) {
      if (this.$vs.rtl) return value
      return value === 'right' ? 'left' : value === 'left' ? 'right' : value
    },
    is(which) {
      return this.type == which
    },
    blurButton(event) {
      this.$emit('blur', event)
      this.$nextTick(() => {
        if (this.type == 'border' || this.type == 'flat') {
          this.opacity = 0
          setTimeout(() => { this.radio = 0 }, 150)
          this.isActive = false
        }
      })
    },
    clickButton(event) {
      this.$emit('click', event)
      this.$nextTick(() => {
        if (this.isActive) return
        if (this.to) this.$router.push(this.to).catch(err => this.$emit('routeErr', err))
        if (this.href) {
          const url = typeof this.href == 'string' ? this.href : this.href.url
          this.target ? window.open(url) : (window.location.href = url)
        }
        if (this.type == 'border' || this.type == 'flat') this.isActive = true

        // Ripple from the click point
        const btn = this.$refs.btn
        if (!btn) return
        let xEvent = event.offsetX
        let yEvent = event.offsetY
        this.time = btn.clientWidth / (btn.clientWidth + (this.is('border') || this.is('flat') ? 70 : 20))
        if (this.is('filled')) this.timeOpacity = this.time
        if (event.target && event.target != btn) {
          xEvent += event.target.offsetLeft
          yEvent += event.target.offsetTop
        }
        this.leftBackgorund = xEvent
        this.topBackgorund = yEvent
        this.radio = btn.clientWidth * 3
        this.opacity = this.is('filled') ? 0 : 1
        if (this.is('filled')) {
          setTimeout(() => {
            this.time = this.timeOpacity = this.radio = 0
            this.opacity = 1
            this.isActive = false
          }, this.time * 1100)
        } else {
          setTimeout(() => { this.timeOpacity = 0.15 }, this.time * 1100)
        }
      })
    },
  },
}
</script>

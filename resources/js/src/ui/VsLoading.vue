<template>
  <transition name="fade" appear>
    <div v-if="active" class="con-vs-loading" :class="['vs-loading-background-' + background, 'vs-loading-color-' + color, { textAfter }]" :style="{ background: getColor(background) }" @click="effectClick">
      <transition name="effect-click">
        <div v-if="activeEffectClick && clickEffect" class="effect-click" :style="{ left: `${leftx}px`, top: `${topx}px` }" />
      </transition>
      <h4 v-if="text" class="title-loading">{{ text }}</h4>
      <div class="vs-loading" :class="[type]" :style="{ transform: `scale(${scale})` }">
        <div v-if="type != 'material'" class="effect-1 effects" :style="effectStyle(1)" />
        <div v-if="type != 'material'" class="effect-2 effects" :style="effectStyle(2)" />
        <div v-if="type != 'material'" class="effect-3 effects" :style="effectStyle(3)" />
        <img :src="src">
        <svg v-if="type == 'material'" class="spinner" width="50px" height="50px" viewBox="0 0 66 66" xmlns="http://www.w3.org/2000/svg">
          <circle class="path" fill="none" stroke-width="5" stroke-linecap="round" cx="33" cy="33" r="30" />
        </svg>
      </div>
    </div>
  </transition>
</template>

<script>
import { color as vsColor } from './utils'

// The spinner behind $vs.loading() (see vs.js)
export default {
  name: 'VsLoading',
  props: {
    type: { default: 'default' },
    color: { default: null },
    background: { default: 'rgba(255,255,255,.6)' },
    src: { default: '' },
    clickEffect: { default: false },
    scale: { default: 1 },
    textAfter: { default: false },
    text: { default: null },
  },
  data() {
    // Shown from the first render (fading in), so $vs.loading.close() always finds the element
    return { active: true, leftx: 0, topx: 0, activeEffectClick: false }
  },
  methods: {
    getColor(value, alpha = 1) {
      return vsColor.getColor(value, alpha)
    },
    effectStyle(n) {
      const c = alpha => this.getColor(this.color, alpha)
      switch (this.type) {
        case 'border': return { borderLeft: `1px solid ${c(1)}` }
        case 'point': return { background: c(0.4) }
        case 'radius': return { border: `3px solid ${c(1)}` }
        case 'corners': return n == 1 ? { border: `3px solid ${c(1)}` } : {}
        case 'sound': return { background: c(1) }
        default: return { borderLeft: `3px solid ${c(1)}` }
      }
    },
    effectClick(evt) {
      this.leftx = evt.offsetX
      this.topx = evt.offsetY
      this.activeEffectClick = true
      setTimeout(() => { this.activeEffectClick = false }, 50)
    },
  },
}
</script>

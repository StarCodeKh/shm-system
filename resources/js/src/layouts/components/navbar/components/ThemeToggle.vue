<template>
  <button type="button" class="nav-icon-btn nav-theme" :class="{ 'is-dark': isDark }" role="switch" :aria-checked="isDark"
    :title="$t(isDark ? 'nav_theme_light' : 'nav_theme_dark')" @click="toggle">
    <!-- Sun and moon swap with a turn -->
    <span class="nav-theme__icon nav-theme__icon--sun"><feather-icon icon="SunIcon" svgClasses="h-5 w-5" /></span>
    <span class="nav-theme__icon nav-theme__icon--moon"><feather-icon icon="MoonIcon" svgClasses="h-5 w-5" /></span>
  </button>
</template>

<script>
import { THEME_KEY, savedTheme, themeFor } from "@/store/state.js"

export default {
  name: 'theme-toggle',
  computed: {
    isDark() {
      return this.$store.state.theme === 'dark'
    },
  },
  mounted() {
    // Until the user picks a theme, follow the device's light/dark setting
    if (!window.matchMedia) return
    this.media = window.matchMedia('(prefers-color-scheme: dark)')
    this.onDeviceChange = event => {
      if (!savedTheme()) this.$store.dispatch('updateTheme', themeFor(event.matches ? 'dark' : 'light'))
    }
    if (this.media.addEventListener) this.media.addEventListener('change', this.onDeviceChange)
    else if (this.media.addListener) this.media.addListener(this.onDeviceChange)
  },
  beforeUnmount() {
    if (!this.media) return
    if (this.media.removeEventListener) this.media.removeEventListener('change', this.onDeviceChange)
    else if (this.media.removeListener) this.media.removeListener(this.onDeviceChange)
  },
  methods: {
    toggle() {
      const theme = this.isDark ? 'light' : 'dark'
      try { localStorage.setItem(THEME_KEY, theme) } catch (e) { /* storage unavailable */ }
      document.documentElement.setAttribute('data-theme', theme)
      const apply = () => {
        this.$store.dispatch('updateTheme', themeFor(theme))
        return this.$nextTick() // App.vue swaps the body class on this tick
      }

      const reduceMotion = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches
      if (!document.startViewTransition || reduceMotion) {
        apply()
        return
      }

      // One GPU snapshot instead of animating every element: the new theme grows as a circle from the button
      const rect = this.$el.getBoundingClientRect()
      const x = rect.left + rect.width / 2
      const y = rect.top + rect.height / 2
      const radius = Math.hypot(Math.max(x, window.innerWidth - x), Math.max(y, window.innerHeight - y))
      const transition = document.startViewTransition(apply)
      transition.ready.then(() => {
        document.documentElement.animate(
          { clipPath: [`circle(0px at ${x}px ${y}px)`, `circle(${radius}px at ${x}px ${y}px)`] },
          { duration: 450, easing: 'cubic-bezier(.22, 1, .36, 1)', pseudoElement: '::view-transition-new(root)' }
        )
      }).catch(() => { /* skipped, theme is already applied */ })
    },
  },
}
</script>

<style lang="scss">
.nav-theme {
  overflow: hidden;

  &__icon {
    position: absolute;
    inset: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: transform .4s cubic-bezier(.22, 1, .36, 1), opacity .25s ease;

    .feather-icon {
      display: flex;
    }
  }

  // Light: show the moon (switch to dark); dark: show the sun
  &__icon--sun {
    opacity: 0;
    transform: rotate(-90deg) scale(.5);
  }

  &.is-dark &__icon--sun {
    opacity: 1;
    transform: none;
    color: #ffb547;
  }

  &.is-dark &__icon--moon {
    opacity: 0;
    transform: rotate(90deg) scale(.5);
  }
}

// Theme switch: the browser's snapshot cross-fade is replaced by the circle reveal above
::view-transition-old(root),
::view-transition-new(root) {
  animation: none;
  mix-blend-mode: normal;
}

@media (prefers-reduced-motion: reduce) {
  .nav-theme__icon {
    transition: none !important;
  }
}
</style>

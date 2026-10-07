<!-- =========================================================================================
  Scroll area replacing vue-perfect-scrollbar (Vue 2 only): a natively scrolling box with a thin scrollbar.
  Keeps its API for the templates: :settings (accepted, not needed), @ps-scroll-y, $refs.x.$el.scrollTop
========================================================================================== -->
<template>
  <div class="scroll-area" @scroll="onScroll"><slot /></div>
</template>

<script>
export default {
  name: 'VuePerfectScrollbar',
  props: {
    settings: { type: Object, default: () => ({}) },
  },
  emits: ['ps-scroll-y', 'ps-scroll-x'],
  data: () => ({ top: 0, left: 0 }),
  methods: {
    onScroll(event) {
      const { scrollTop, scrollLeft } = event.target
      if (scrollTop !== this.top) this.$emit('ps-scroll-y', event)
      if (scrollLeft !== this.left) this.$emit('ps-scroll-x', event)
      this.top = scrollTop
      this.left = scrollLeft
    },
    // Same call as the old component; native scrolling needs no recalculation
    update() {},
  },
}
</script>

<style>
.scroll-area {
  position: relative;
  overflow: auto;
  scrollbar-width: thin;
}
</style>

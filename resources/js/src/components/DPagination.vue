<template>
  <div class="d-pagination" ref="box">
    <vs-pagination :total="total" :value="value" :max="max" @input="$emit('input', $event)" />
  </div>
</template>

<script>
// Pager that fits its space: as many page buttons as the width allows (9, 7 or 5),
// so it never runs past the card on small screens or with 4-digit page numbers.
// Use like <vs-pagination>: <d-pagination :total="pages" v-model="page" />
export default {
  name: 'd-pagination',
  props: {
    total: { type: Number, default: 1 },
    value: { type: Number, default: 1 },
  },
  data: () => ({
    width: 0,
  }),
  computed: {
    max() {
      if (!this.width) return 7
      const digits = String(this.total || 1).length
      const button = Math.max(34, 18 + digits * 9) + 4 // button width + gap
      const arrows = 2 * (34 + 4) // previous / next
      const fit = Math.floor((this.width - arrows) / button)
      return fit >= 9 ? 9 : fit >= 7 ? 7 : 5
    },
  },
  mounted() {
    this.measure()
    if (typeof ResizeObserver !== 'undefined') {
      this.observer = new ResizeObserver(() => this.measure())
      this.observer.observe(this.$refs.box)
    } else {
      window.addEventListener('resize', this.measure)
    }
  },
  beforeDestroy() {
    if (this.observer) this.observer.disconnect()
    window.removeEventListener('resize', this.measure)
  },
  methods: {
    measure() {
      if (this.$refs.box) this.width = this.$refs.box.clientWidth
    },
  },
}
</script>

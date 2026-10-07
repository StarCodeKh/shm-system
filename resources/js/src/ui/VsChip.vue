<template>
  <div class="con-vs-chip" :class="['vs-chip-' + color, { closable, 'con-color': color, 'bg-chip-transparent': transparent }]" :style="styleChip">
    <span class="text-chip vs-chip--text"><slot /></span>
    <button v-if="closable" class="btn-close vs-chip--close" type="button" @click="closeChip">
      <vs-icon :icon-pack="iconPack" :icon="closeIcon" />
    </button>
  </div>
</template>

<script>
import { color as vsColor } from './utils'

export default {
  name: 'VsChip',
  // "click" comes only from the close button, as in Vuesax (a click on the chip itself does nothing)
  emits: ['update:modelValue', 'click'],
  props: {
    modelValue: {},
    closable: { type: [Boolean, String], default: false },
    color: { type: String, default: null },
    iconPack: { type: String, default: 'material-icons' },
    closeIcon: { type: String, default: 'clear' },
    transparent: { type: Boolean, default: false },
  },
  computed: {
    styleChip() {
      return {
        background: this.transparent ? vsColor.getColor(this.color, 0.15) : vsColor.getColor(this.color, 1),
        color: this.transparent ? vsColor.getColor(this.color, 1) : this.color ? 'rgba(255,255,255,.9)' : 'rgba(0,0,0,.7)',
      }
    },
  },
  methods: {
    closeChip() {
      this.$emit('update:modelValue', false)
      this.$emit('click')
    },
  },
}
</script>

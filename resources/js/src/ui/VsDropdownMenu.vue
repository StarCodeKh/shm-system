<template>
  <teleport to="body">
    <transition name="dropdownx">
      <div
        v-if="visible"
        ref="options"
        v-bind="$attrs"
        class="con-vs-dropdown--menu vs-dropdown-menu"
        :class="{ rightx: dropRight || (vsDropdown && vsDropdown.rightx), notHeight }"
        :style="{ left: `${leftx}px`, top: `${topx}px` }"
        @mouseleave="onHover(false)"
        @mouseenter="onHover(true)">
        <ul v-if="!customContent" class="vs-component vs-dropdown--menu"><slot /></ul>
        <div v-else class="vs-dropdown--custom vs-dropdown--menu"><slot /></div>
        <div ref="menuAfter" :class="[dropRight ? 'vs-dropdown-right--menu--after' : 'vs-dropdown--menu--after']"
          :style="arrowx === null ? null : { left: `${arrowx}px`, right: 'auto' }" />
      </div>
    </transition>
  </teleport>
</template>

<script>
export default {
  name: 'VsDropdownMenu',
  inheritAttrs: false,
  inject: { vsDropdown: { default: null } },
  data() {
    return { leftx: 0, topx: 0, notHeight: false, arrowx: null }
  },
  computed: {
    visible() { return !!(this.vsDropdown && this.vsDropdown.vsDropdownVisible) },
    customContent() { return !!(this.vsDropdown && this.vsDropdown.vsCustomContent) },
    dropRight() { return !!(this.vsDropdown && this.vsDropdown.vsDropRight) },
    triggerClick() { return !!(this.vsDropdown && this.vsDropdown.vsTriggerClick) },
  },
  watch: {
    visible(open) {
      if (open) this.setDirection()
      else if (this.vsDropdown) this.vsDropdown.rightx = false
    },
  },
  created() {
    if (this.vsDropdown) this.vsDropdown.menu = this
  },
  beforeUnmount() {
    if (this.vsDropdown && this.vsDropdown.menu === this) this.vsDropdown.menu = null
  },
  methods: {
    onHover(inside) {
      if (!this.triggerClick && this.vsDropdown) this.vsDropdown.setVisible(inside)
    },
    // The little arrow points up, or down when the menu opens above the trigger
    setDirection() {
      setTimeout(() => {
        const trigger = this.vsDropdown && this.vsDropdown.$refs.dropdown
        const menuAfter = this.$refs.menuAfter
        if (!menuAfter) return
        if (trigger && trigger.getBoundingClientRect().top + 300 >= window.innerHeight) {
          menuAfter.style.bottom = '-5px'
          menuAfter.style.transform = 'rotate(225deg)'
          return
        }
        menuAfter.style.top = '10px'
      }, 100)
    },
  },
}
</script>

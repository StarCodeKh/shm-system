<template>
  <vs-dropdown vs-custom-content vs-trigger-click class="cursor-pointer">

    <!-- Selected -->
    <span class="cursor-pointer flex items-center">
      <span class="ml-2">
        {{ selected.year }} - {{ selected.quarter }}
      </span>
    </span>

    <!-- Dropdown -->
    <vs-dropdown-menu class="w-48 vx-navbar-dropdown px-3 py-2">

      <!-- Year -->
      <div class="flex items-center justify-between mb-3">
        <vs-button
          size="small"
          icon="icon-chevron-left"
          icon-pack="feather"
          type="border"
          @click="prevYear"
        />
        <strong>{{ selected.year }}</strong>
        <vs-button
          class="wrapper_btns"
          size="small"
          icon="icon-chevron-right"
          icon-pack="feather"
          type="border"
          @click="nextYear"
        />
      </div>

      <!-- Quarters -->
      <div>
        <vs-dropdown-item
          v-for="q in quarters"
          :key="q"
          @click="selectQuarter(q)"
        >
          {{ q }}
        </vs-dropdown-item>
      </div>

    </vs-dropdown-menu>
  </vs-dropdown>
</template>

<script>
export default {
  name: 'QuarterDropDown',

  props: {
    value: {
      type: Object,
      default: () => ({
        year: new Date().getFullYear(),
        quarter: 'Q1'
      })
    }
  },

  data() {
    return {
      selected: { ...this.value },
      quarters: ['Q1', 'Q2', 'Q3', 'Q4']
    }
  },

  watch: {
    value(val) {
      this.selected = { ...val }
    }
  },

  methods: {
    prevYear() {
      this.selected.year--
      this.emitChange()
    },

    nextYear() {
      this.selected.year++
      this.emitChange()
    },

    selectQuarter(q) {
      this.selected.quarter = q
      this.emitChange()
    },

    emitChange() {
      this.$emit('input', this.selected)   // v-model
      this.$emit('change', this.selected)  // optional
    }
  }
}
</script>

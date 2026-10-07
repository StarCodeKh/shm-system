<template>
  <tr
    ref="tableTr"
    v-bind="$attrs"
    class="tr-values vs-table--tr"
    :class="['tr-table-state-' + state, { 'is-selected': isSelected, selected: data, 'is-expand': expanded, 'tr-expandedx': expanded, hoverFlat: vsTable && vsTable.hoverFlat }]"
    @dblclick="vsTable && vsTable.dblclicktr(data, true)"
    @click="clicktr">
    <td v-if="(vsTable && vsTable.multiple) || $slots.expand" class="td-check" :class="{ 'active-expanded': expanded }">
      <vs-checkbox v-if="vsTable && vsTable.multiple" :checked="isSelected" size="small" @change="vsTable.handleCheckbox(data)" />
      <vs-icon v-if="$slots.expand">keyboard_arrow_down</vs-icon>
    </td>
    <slot />
  </tr>
  <!-- Detail row under this one (the #expand slot), opened by clicking the row -->
  <transition name="tr-expand">
    <tr v-if="expanded" class="tr-expand">
      <td :colspan="colspan">
        <div class="content-tr-expand"><slot name="expand" /></div>
      </td>
    </tr>
  </transition>
</template>

<script>
export default {
  name: 'VsTr',
  inheritAttrs: false,
  inject: { vsTable: { default: null } },
  props: {
    state: { type: String, default: null },
    data: { default: null },
  },
  data() {
    return { colspan: 0, expanded: false }
  },
  computed: {
    isSelected() {
      if (!this.vsTable || !this.data) return false
      if (this.vsTable.multiple && Array.isArray(this.vsTable.modelValue)) {
        return this.vsTable.modelValue.some(item => JSON.stringify(item) === JSON.stringify(this.data))
      }
      return this.vsTable.modelValue == this.data
    },
  },
  watch: {
    // New page / search: close an open detail row
    'vsTable.datax'() {
      this.expanded = false
    },
  },
  created() {
    if (this.$slots.expand && this.vsTable) this.vsTable.hasExpadableData = true
  },
  mounted() {
    this.$nextTick(() => {
      const thead = this.vsTable && this.vsTable.$refs.thead
      this.colspan = (thead ? thead.querySelectorAll('th').length : 0) + (this.$slots.expand ? 1 : 0)
    })
  },
  methods: {
    clicktr() {
      if (this.vsTable) this.vsTable.clicktr(this.data, true)
      if (this.$slots.expand && !(this.vsTable && this.vsTable.multiple)) this.expanded = !this.expanded
    },
  },
}
</script>

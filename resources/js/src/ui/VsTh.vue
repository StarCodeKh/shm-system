<template>
  <th colspan="1" rowspan="1" @click="sortValue">
    <div class="vs-table-text">
      <slot />
      <span v-if="isColumnSelectedForSort && currentSort != 0" class="sort-th">
        <vs-icon class="th-sort-icon" :icon="currentSort == 1 ? 'expand_less' : 'expand_more'" />
      </span>
    </div>
  </th>
</template>

<script>
const SORTS = [null, 'asc', 'desc']

export default {
  name: 'VsTh',
  inject: { vsTable: { default: null } },
  props: {
    sortKey: { default: null, type: String },
  },
  data() {
    return { currentSort: 0 }
  },
  computed: {
    isColumnSelectedForSort() {
      return !!this.sortKey && !!this.vsTable && this.vsTable.currentSortKey == this.sortKey
    },
  },
  watch: {
    // Sorting by another column resets this one
    'vsTable.currentSortKey'(key) {
      if (key != this.sortKey) this.currentSort = 0
    },
  },
  methods: {
    sortValue() {
      this.currentSort = this.currentSort !== 2 ? this.currentSort + 1 : 0
      if (this.vsTable) this.vsTable.sort(this.sortKey, SORTS[this.currentSort])
    },
  },
}
</script>

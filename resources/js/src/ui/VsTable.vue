<template>
    <div class="vs-component vs-con-table" :class="[{ stripe, hoverFlat }, 'vs-table-' + color]">
        <header class="header-table vs-table--header">
            <slot name="header" />
            <div v-if="search" class="con-input-search vs-table--search">
                <input v-model="searchx" class="input-search vs-table--search-input" type="text">
                <vs-icon icon="search" />
            </div>
        </header>
        <div class="con-tablex vs-table--content">
            <div class="vs-con-tbody vs-table--tbody" :style="{ maxHeight, overflow: maxHeight != 'auto' ? 'auto' : null }">
                <table ref="table" class="vs-table vs-table--tbody-table">
                    <thead ref="thead" class="vs-table--thead">
                        <tr>
                            <th v-if="multiple || hasExpadableData" class="td-check">
                                <span v-if="multiple" class="con-td-check">
                                    <vs-checkbox :key="isCheckedLine ? 'remove' : 'check'" :icon="isCheckedLine ? 'remove' : 'check'" :checked="isCheckedMultiple" size="small" @change="changeCheckedMultiple" />
                                </span>
                            </th>
                            <slot name="thead" />
                        </tr>
                    </thead>
                    <slot :data="datax" />
                </table>
            </div>
            <div v-if="isNoData" class="not-data-table vs-table--not-data">{{ noDataText }}</div>
            <div v-if="pagination" class="con-pagination-table vs-table--pagination">
                <vs-pagination
                    v-model="currentx"
                    :total="searchx && !sst ? totalPagesSearch : totalPages"
                    :description-items="descriptionItems"
                    :max-items="maxItemsx"
                    :size-array="queriedResults.length"
                    :description="description"
                    :description-title="descriptionTitle"
                    :description-connector="descriptionConnector"
                    :description-body="descriptionBody"
                    @changeMaxItems="index => (maxItemsx = descriptionItems[index])" />
            </div>
        </div>
    </div>
</template>

<script>
// Table with client-side search, sort (vs-th sort-key), paging and row selection (v-model).
// Rows come back through the default slot: <template #default="{ data }"> <vs-tr v-for="tr in data" :data="tr">
const flattenDeep = val => Object.values(val || []).reduce((acc, item) => (item && typeof item === 'object' ? acc.concat(flattenDeep(item)) : acc.concat(item)), [])
const normalize = text => String(text).normalize('NFD').replace(/[̀-ͯ]/g, '').toLowerCase()

export default {
    name: 'VsTable',
    emits: ['update:modelValue', 'selected', 'dblSelection', 'change-page', 'search', 'sort'],
    props: {
        modelValue: {},
        color: { default: 'primary', type: String },
        noDataText: { default: 'No data Available', type: String },
        stripe: { default: false, type: Boolean },
        hoverFlat: { default: false, type: Boolean },
        maxHeight: { default: 'auto', type: String },
        multiple: { default: false, type: Boolean },
        data: { default: null },
        search: { default: false, type: Boolean },
        maxItems: { default: 5, type: [Number, String] },
        pagination: { default: false, type: Boolean },
        description: { default: false, type: Boolean },
        descriptionItems: { default: () => [], type: Array },
        descriptionTitle: { type: String },
        descriptionConnector: { type: String },
        descriptionBody: { type: String },
        currentPage: { default: 1, type: [Number, String] },
        sst: { default: false, type: Boolean },
        total: { type: Number, default: 0 },
        onlyClickCheckbox: { type: Boolean, default: false },
    },
    provide() {
        return { vsTable: this }
    },
    data() {
        return { datax: [], searchx: null, currentx: 1, maxItemsx: 5, hasExpadableData: false, currentSortKey: null, currentSortType: null }
    },
    computed: {
        rows() {
            return this.data || []
        },
        totalPages() {
            const length = this.sst && this.total ? this.total : this.rows.length
            return Math.ceil(length / this.maxItemsx)
        },
        totalPagesSearch() {
            return Math.ceil(this.queriedResults.length / this.maxItems)
        },
        queriedResults() {
            if (!(this.searchx && this.search)) return this.rows
            return this.rows.filter(tr => this.getValues(tr).toString().toLowerCase().indexOf(this.searchx.toLowerCase()) != -1)
        },
        isNoData() {
            return this.datax ? this.datax.length == 0 : false
        },
        selected() {
            return Array.isArray(this.modelValue) ? this.modelValue : []
        },
        isCheckedLine() {
            return this.rows.length !== this.selected.length
        },
        isCheckedMultiple() {
            return this.selected.length > 0
        },
    },
    watch: {
        currentPage(page) { this.currentx = page },
        currentx() { this.sst ? this.$emit('change-page', this.currentx) : this.loadData() },
        maxItems(val) { this.maxItemsx = val; this.loadData() },
        maxItemsx() { this.loadData() },
        data() { this.loadData() },
        searchx() {
            if (this.sst) return this.$emit('search', this.searchx)
            this.loadData()
            this.currentx = 1
        },
    },
    mounted() {
        this.maxItemsx = this.maxItems
        this.loadData()
    },
    methods: {
        loadData() {
            const max = Math.ceil(this.currentx * this.maxItemsx)
            const min = max - this.maxItemsx
            if (!this.searchx || this.sst) this.datax = this.pagination ? this.getItems(min, max) : this.sortItems(this.rows) || []
            else this.datax = this.getItemsSearch(min, max) || []
        },
        getItems(min, max) {
            return this.sortItems(this.rows).filter((item, index) => index >= min && index < max)
        },
        sortItems(data) {
            const key = this.currentSortKey
            const type = this.currentSortType
            const compare = (a, b) => (a[key] < b[key] ? (type == 'desc' ? 1 : -1) : a[key] > b[key] ? (type == 'desc' ? -1 : 1) : 0)
            return type !== null ? [...data].sort(compare) : [...data]
        },
        getItemsSearch(min, max) {
            const search = normalize(this.searchx)
            return this.sortItems(this.rows)
                .filter(tr => normalize(this.getValues(tr).toString()).indexOf(search) != -1)
                .filter((_, index) => index >= min && index < max)
        },
        sort(key, sortType) {
            this.currentSortKey = key
            this.currentSortType = sortType
            if (this.sst) return this.$emit('sort', key, sortType)
            this.loadData()
        },
        getValues(obj) {
            return flattenDeep(obj).filter(item => typeof item === 'string' || typeof item === 'number')
        },
        changeCheckedMultiple() {
            this.$emit('update:modelValue', this.rows.length - this.selected.length == 0 ? [] : this.rows)
        },
        toggleRow(tr) {
            const val = this.selected.slice(0)
            if (val.includes(tr)) val.splice(val.indexOf(tr), 1)
            else val.push(tr)
            this.$emit('update:modelValue', val)
            this.$emit('selected', tr)
        },
        handleCheckbox(tr) {
            if (this.multiple && this.onlyClickCheckbox) this.toggleRow(tr)
        },
        clicktr(tr, isTr) {
            if (!isTr || this.onlyClickCheckbox) return
            if (this.multiple) return this.toggleRow(tr)
            this.$emit('update:modelValue', tr)
            this.$emit('selected', tr)
        },
        dblclicktr(tr, isTr) {
            if (isTr) this.$emit('dblSelection', tr)
        },
    },
}
</script>

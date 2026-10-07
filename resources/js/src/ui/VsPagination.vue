<template>
    <!-- Same row/column markup as Vuesax's vs-row / vs-col -->
    <div class="vs-row" :style="{ display: 'flex', 'justify-content': description ? 'space-between' : 'center', width: '100%' }">
        <div v-if="description" class="vs-col vs-xs-12 vs-sm-12 vs-lg-6 vs-pagination--mb" style="display: flex; justify-content: flex-start; align-items: center; margin-left: 0%; width: 100%;">
            <div>
                <span style="margin-right: 5px">{{ descriptionTitle }}: {{ minRows }} - {{ maxRows }} {{ descriptionConnector }} {{ sizeArray }} | {{ descriptionBody }}: </span>
                <ul class="vs-pagination--array">
                    <li v-for="(row, index) in descriptionItems" :key="index">
                        <span :class="['vs-description-' + color, { 'vs-pagination--bold': index == indexRows }]" style="cursor: pointer" @click="changeRowMaxItems(index)">{{ row }}</span>
                        <span v-if="index != descriptionItems.length - 1"> , </span>
                    </li>
                </ul>
            </div>
        </div>
        <div class="vs-col vs-xs-12 vs-sm-12 vs-pagination--mb" :class="'vs-lg-' + (description ? 6 : 12)" style="display: flex; justify-content: flex-end; align-items: center; margin-left: 0%; width: 100%;">
            <div class="con-vs-pagination" :class="['vs-pagination-' + color]" :style="stylePagination">
                <nav class="vs-pagination--nav">
                    <button class="vs-pagination--buttons btn-prev-pagination vs-pagination--button-prev" :class="{ disabled: current <= 1 }" :disabled="current === 1" @click="prevPage">
                        <vs-icon :icon-pack="iconPack" :icon="prevIcon || ($vs.rtl ? 'chevron_right' : 'chevron_left')" />
                    </button>
                    <ul class="vs-pagination--ul">
                        <li v-for="(page, index) in pages" :key="index" class="item-pagination vs-pagination--li" :class="{ 'is-current': page == current }" @click="goTo(page)">
                            <span>{{ page }}</span>
                            <div class="effect" />
                        </li>
                    </ul>
                    <button class="vs-pagination--buttons btn-next-pagination vs-pagination--button-next" :class="{ disabled: current === total }" :disabled="current === total" @click="nextPage">
                        <vs-icon :icon-pack="iconPack" :icon="nextIcon || ($vs.rtl ? 'chevron_left' : 'chevron_right')" />
                    </button>
                    <input v-if="goto" v-model="go" class="vs-pagination--input-goto" :max="total" min="1" type="number" @change="goTo">
                </nav>
            </div>
        </div>
    </div>
</template>

<script>
import { color as vsColor } from './utils'

const range = (start, end) => {
    const pages = []
    for (let page = Math.max(start, 1); page <= end; page++) pages.push(page)
    return pages
}

export default {
    name: 'VsPagination',
    emits: ['update:modelValue', 'input', 'change', 'changeMaxItems'],
    props: {
        color: { type: String, default: 'primary' },
        total: { type: Number, required: true },
        sizeArray: { type: Number, required: false },
        maxItems: { type: [Number, String], required: false },
        modelValue: { type: Number, default: undefined },
        value: { type: Number, default: 1 },
        max: { type: Number, default: 9 },
        goto: { type: Boolean },
        prevIcon: { type: String },
        nextIcon: { type: String },
        iconPack: { type: String, default: 'material-icons' },
        description: { default: false, type: Boolean },
        descriptionItems: { default: () => [], type: Array },
        descriptionTitle: { type: String, default: 'Registries' },
        descriptionConnector: { type: String, default: 'of' },
        descriptionBody: { type: String, default: 'Pages' },
    },
    data() {
        return { pages: [], current: 0, go: 0, minRows: 0, maxRows: 0, indexRows: 0 }
    },
    computed: {
        requested() {
            return this.modelValue !== undefined ? this.modelValue : this.value
        },
        stylePagination() {
            if (vsColor.isColor(this.color)) return {}
            return { '--vs-color-pagination': vsColor.getColor(this.color), '--vs-color-pagination-alpha': vsColor.getColor(this.color, 0.5) }
        },
    },
    watch: {
        current(val) {
            this.getPages()
            this.calculateMinMax(val)
            this.$emit('update:modelValue', val)
            this.$emit('input', val)
            this.$emit('change', val)
        },
        total() { this.getPages() },
        max() { this.getPages() },
        requested(val) {
            this.goTo(val < 1 ? 1 : val <= this.total ? val : this.total)
        },
    },
    mounted() {
        this.current = this.go = this.requested
        this.calculateMinMax(this.current)
        this.indexRows = this.descriptionItems.indexOf(this.maxItems)
        this.getPages()
    },
    methods: {
        changeRowMaxItems(index) {
            this.indexRows = index
            this.$emit('changeMaxItems', index)
            this.calculateMinMax(this.current)
            this.current = 1
        },
        calculateMinMax(val) {
            this.maxRows = val * this.maxItems <= this.sizeArray ? val * this.maxItems : this.sizeArray
            this.minRows = val * this.maxItems <= this.sizeArray ? this.maxRows - this.maxItems + 1 : (this.current - 1) * this.maxItems + 1
        },
        goTo(page) {
            if (page === '...') return
            if (page && page.target !== undefined) {
                const value = parseInt(page.target.value, 10)
                this.go = value < 1 ? 1 : value <= this.total ? value : this.total
                this.current = this.go
            } else {
                this.current = page
            }
        },
        // 1 … 4 5 6 … 20 around the current page
        getPages() {
            if (this.total <= this.max) this.pages = range(1, this.total)
            const even = this.max % 2 === 0 ? 1 : 0
            const prevRange = this.total < 6 ? Math.floor(this.max / (this.max / 2)) : Math.floor(this.max / 2)
            const nextRange = this.total - prevRange + 1 + even
            if (this.current >= prevRange && this.current <= nextRange) {
                this.pages = [1, '...', ...range(this.current - prevRange + 2, this.current + prevRange - 2 - even), '...', this.total]
            } else if (this.total < 6) {
                this.pages = range(1, this.total)
            } else {
                this.pages = [...range(1, prevRange), '...', ...range(nextRange, this.total)]
            }
        },
        nextPage() {
            if (this.current < this.total) this.current++
        },
        prevPage() {
            if (this.current > 1) this.current--
        },
    },
}
</script>

<template>
    <div
        class="con-select"
        :class="[rootAttrs.class, { activeOptions: active, 'input-select-validate-success': success, 'input-select-validate-danger': danger, 'input-select-validate-warning': warning }]"
        :style="[rootAttrs.style, width ? { width } : null]">
        <label v-if="label" ref="inputSelectLabel" class="vs-select--label" :class="{ ['input-select-label-' + color + '--active']: active }">{{ label }}</label>
        <div class="input-select-con">
            <input
                ref="inputselect"
                v-bind="innerAttrs"
                class="input-select vs-select--input"
                readonly
                type="text"
                :value="displayText"
                @keydown.esc.stop.prevent="closeOptions"
                @keyup="onKeyup"
                @mouseup="open"
                @focus="onFocus"
                @blur="$emit('blur', $event)">
            <button class="icon-select-clear vs-select--icon-clear" type="button"><i class="material-icons"> close </i></button>
            <vs-icon class="icon-select vs-select--icon" :icon-pack="iconPack" :icon="icon" />
            <teleport to="body">
                <transition name="fadeselect">
                    <div v-show="active" ref="vsSelectOptions" class="vs-select--options" :class="['vs-select-' + color, { scrollx }]" :style="cords">
                        <ul ref="ulx"><slot /></ul>
                        <ul v-show="!items.length"><li>{{ noData }}</li></ul>
                    </div>
                </transition>
            </teleport>
        </div>
        <div v-if="success" class="con-text-validation"><span class="span-text-validation span-text-validation-success">{{ successText }}</span></div>
        <div v-else-if="danger" class="con-text-validation span-text-validation-danger"><span class="span-text-validation">{{ dangerText }}</span></div>
        <div v-else-if="warning" class="con-text-validation span-text-validation-warning"><span class="span-text-validation">{{ warningText }}</span></div>
        <div v-if="descriptionText" class="con-text-validation span-text-validation"><span class="span-text-validation">{{ descriptionText }}</span></div>
    </div>
</template>

<script>
import { markRaw } from 'vue'
import { splitAttrs } from './utils'

// Single-choice select with <vs-select-item :value :text> options (the only way the app uses it)
export default {
    name: 'VsSelect',
    mixins: [splitAttrs],
    inheritAttrs: false,
    emits: ['update:modelValue', 'change', 'focus', 'blur'],
    props: {
        modelValue: {},
        noData: { default: 'No data available', type: String },
        color: { default: 'primary', type: String },
        label: { default: null, type: String },
        success: { default: false, type: Boolean },
        danger: { default: false, type: Boolean },
        warning: { default: false, type: Boolean },
        successText: { default: null, type: String },
        dangerText: { default: null, type: String },
        warningText: { default: null, type: String },
        descriptionText: { default: null, type: String },
        iconPack: { default: 'material-icons', type: String },
        icon: { default: 'keyboard_arrow_down', type: String },
        width: { default: null, type: String },
    },
    provide() {
        return { vsSelect: this }
    },
    data() {
        return { active: false, scrollx: false, cords: {}, items: [] }
    },
    computed: {
        displayText() {
            const item = this.items.find(option => option.value == this.modelValue)
            return item ? item.text : ''
        },
    },
    watch: {
        modelValue(value) {
            this.$emit('change', value)
        },
        active(open) {
            if (!open) return
            this.$nextTick(() => {
                this.cords = this.changePosition()
                setTimeout(() => {
                    // Focus the chosen option (or the first) so arrow keys work at once
                    const chosen = this.items.find(option => option.value == this.modelValue) || this.items[0]
                    if (chosen && chosen.$refs.item) chosen.$refs.item.focus()
                    if (this.$refs.ulx && this.$refs.ulx.scrollHeight >= 260) this.scrollx = true
                }, 100)
            })
        },
    },
    beforeUnmount() {
        document.removeEventListener('click', this.clickBlur)
    },
    methods: {
        register(item) {
            this.items.push(markRaw(item))
        },
        unregister(item) {
            const at = this.items.indexOf(item)
            if (at !== -1) this.items.splice(at, 1)
        },
        select(value) {
            this.closeOptions()
            this.$emit('update:modelValue', value)
        },
        onFocus(event) {
            this.$emit('focus', event)
            if (event.keyCode || event.which) this.open()
        },
        onKeyup(event) {
            if (event.key == 'ArrowDown' || event.key == 'ArrowUp') {
                event.preventDefault()
                const first = this.items[0]
                if (first && first.$refs.item) first.$refs.item.focus()
            }
        },
        open() {
            this.active = true
            document.addEventListener('click', this.clickBlur)
        },
        clickBlur(event) {
            if (event.target === this.$refs.inputselect) return
            if (!(event.target.closest && event.target.closest('.vs-select--options'))) this.closeOptions()
        },
        closeOptions() {
            this.active = false
            document.removeEventListener('click', this.clickBlur)
        },
        // Under the field, or above it when there is no room below
        changePosition() {
            const elx = this.$refs.inputselect
            const content = this.$refs.vsSelectOptions
            if (!elx || !content) return {}
            const rect = elx.getBoundingClientRect()
            const scrollTop = window.pageYOffset || document.documentElement.scrollTop
            const top = rect.top + content.scrollHeight + 20 >= window.innerHeight
                ? rect.top + elx.clientHeight + scrollTop - content.scrollHeight
                : rect.top + scrollTop
            return { left: `${rect.left}px`, top: `${top}px`, width: `${elx.offsetWidth}px` }
        },
    },
}
</script>

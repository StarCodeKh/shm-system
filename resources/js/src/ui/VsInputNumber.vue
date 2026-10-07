<template>
    <div class="vs-input-number" :class="[rootAttrs.class, 'vs-input-number-size-' + size, 'vs-input-number-' + color, { isChangeValue }]" :style="rootAttrs.style">
        <button
            v-repeat-click="less"
            class="btn-less vs-input-number--button-less"
            :class="{ limit: modelValue <= min }"
            :style="{ background: getColor }"
            :disabled="innerAttrs.disabled"
            type="button">
            <vs-icon :icon-pack="iconPack" :icon="iconDec" />
        </button>
        <span v-if="label">{{ label }}</span>
        <input
            ref="input"
            v-bind="innerAttrs"
            class="vs-input-number--input"
            :style="{ width: `${getLength}px` }"
            :disabled="isDisabled"
            :min="min"
            :max="max"
            type="number"
            :value="modelValue"
            @input="$emit('update:modelValue', $event.target.value)"
            @blur="onBlur">
        <button
            v-repeat-click="plus"
            class="btn-plus vs-input-number--button-plus"
            :class="{ limit: modelValue >= max && max !== null }"
            :style="{ background: getColor }"
            :disabled="innerAttrs.disabled"
            type="button">
            <vs-icon :icon-pack="iconPack" :icon="iconInc" />
        </button>
    </div>
</template>

<script>
import { color as vsColor, splitAttrs } from './utils'

// Hold the button to keep stepping; a quick click steps once
const repeatClick = {
    mounted(el, binding) {
        let interval = null
        let start
        const step = () => binding.value()
        const stop = () => {
            if (new Date() - start < 100) step()
            clearInterval(interval)
            interval = null
        }
        el.addEventListener('mousedown', (e) => {
            if (e.button !== 0) return
            start = new Date()
            const up = () => { stop(); el.removeEventListener('mouseup', up) }
            el.addEventListener('mouseleave', up, { once: true })
            el.addEventListener('mouseup', up)
            clearInterval(interval)
            interval = setInterval(step, 100)
        })
    },
}

export default {
    name: 'VsInputNumber',
    directives: { repeatClick },
    mixins: [splitAttrs],
    inheritAttrs: false,
    emits: ['update:modelValue', 'blur'],
    props: {
        modelValue: {},
        color: { default: 'primary', type: String },
        label: { default: null, type: String },
        max: { default: null, type: [Number, String] },
        min: { default: 0, type: [Number, String] },
        size: { default: null, type: String },
        iconPack: { default: 'material-icons', type: String },
        iconDec: { default: 'remove', type: String },
        iconInc: { default: 'add', type: String },
        step: { default: 1, type: [Number, String] },
        isDisabled: { type: Boolean, default: false },
    },
    data() {
        return { isChangeValue: false }
    },
    computed: {
        getLength() {
            return this.modelValue ? this.modelValue.toString().length * 9.1 : 0
        },
        getColor() {
            return vsColor.getColor(this.color, 1)
        },
    },
    watch: {
        modelValue() {
            this.isChangeValue = true
            setTimeout(() => { this.isChangeValue = false }, 200)
        },
    },
    methods: {
        onBlur(evt) {
            if (parseFloat(this.modelValue) > parseFloat(this.max)) {
                this.$emit('update:modelValue', this.max)
            } else if (parseFloat(this.modelValue) < parseFloat(this.min)) {
                this.$emit('update:modelValue', this.min)
                this.$emit('blur', evt)
            }
        },
        plus() {
            if (this.modelValue === '') return this.$emit('update:modelValue', this.fixPrecision(0))
            if (this.max ? parseFloat(this.modelValue) < parseFloat(this.max) : true) {
                this.$emit('update:modelValue', this.fixPrecision(parseFloat(this.modelValue) + parseFloat(this.step)))
            }
        },
        less() {
            if (this.modelValue === '') return this.$emit('update:modelValue', this.fixPrecision(0))
            if (this.min ? parseFloat(this.modelValue) > parseFloat(this.min) : true) {
                this.$emit('update:modelValue', this.fixPrecision(parseFloat(this.modelValue) - parseFloat(this.step)))
            }
        },
        fixPrecision(n) {
            const precision = (this.step + '').split('.')[1]
            return n.toFixed(precision ? precision.length : 0)
        },
    },
}
</script>

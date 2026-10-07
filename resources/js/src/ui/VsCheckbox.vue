<template>
    <div class="vs-component con-vs-checkbox" :class="[rootAttrs.class, 'vs-checkbox-' + color, 'vs-checkbox-' + size]" :style="rootAttrs.style">
        <input
            v-bind="innerAttrs"
            class="vs-checkbox--input"
            type="checkbox"
            :checked="isChecked || innerAttrs.checked"
            :value="modelValue"
            @change="toggleValue">
        <span class="checkbox_x vs-checkbox" :style="style">
            <span class="vs-checkbox--check" :style="styleCheck">
                <vs-icon class="vs-checkbox--icon" :icon="icon" :icon-pack="iconPack" />
            </span>
        </span>
        <span class="con-slot-label"><slot /></span>
    </div>
</template>

<script>
import { color as vsColor, splitAttrs } from './utils'

export default {
    name: 'VsCheckbox',
    mixins: [splitAttrs],
    inheritAttrs: false,
    emits: ['update:modelValue', 'change'],
    props: {
        color: { default: 'primary', type: String },
        modelValue: {},
        icon: { default: 'check', type: String },
        iconPack: { default: 'material-icons', type: String },
        vsValue: { type: [Boolean, Array, String, Number, Object], default: false },
        size: { default: 'default', type: String },
    },
    computed: {
        styleCheck() {
            return { background: this.isChecked ? vsColor.getColor(this.color, 1) : null }
        },
        style() {
            return { border: `2px solid ${this.isChecked ? vsColor.getColor(this.color, 1) : 'rgb(180, 180, 180)'}` }
        },
        // A list model holds the checked values; otherwise the model is true/false (or the vs-value string)
        isChecked() {
            return Array.isArray(this.modelValue) ? this.modelValue.includes(this.vsValue) : this.modelValue
        },
    },
    methods: {
        toggleValue(evt) {
            if (Array.isArray(this.modelValue)) {
                const value = this.modelValue.slice(0)
                if (this.isChecked) value.splice(value.indexOf(this.vsValue), 1)
                else value.push(this.vsValue)
                this.$emit('update:modelValue', value)
                this.$emit('change', value)
            } else if (typeof this.vsValue == 'string') {
                const value = this.modelValue == this.vsValue ? null : this.vsValue
                this.$emit('update:modelValue', value)
                this.$emit('change', value)
            } else {
                this.$emit('update:modelValue', !this.modelValue)
                this.$emit('change', evt)
            }
        },
    },
}
</script>

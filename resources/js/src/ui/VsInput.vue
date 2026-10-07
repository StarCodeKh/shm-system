<template>
    <div
        ref="coninput"
        class="vs-component vs-con-input-label vs-input"
        :class="[rootClass, 'vs-input-' + color, {
            isFocus,
            'input-icon-validate-success': success,
            'input-icon-validate-danger': danger,
            'input-icon-validate-warning': warning,
            'is-label-placeholder': labelPlaceholder,
        }]"
        :style="[rootStyle, styleLabel]">
        <label v-if="labelPlaceholder ? false : label" class="vs-input--label" for="" @click="focusInput">{{ label }}</label>
        <div class="vs-con-input">
            <input
                ref="vsinput"
                v-bind="inputAttrs"
                class="vs-inputx vs-input--input"
                :class="[size, { hasValue: modelValue !== '', hasIcon: icon, 'icon-after-input': iconAfter }]"
                :style="style"
                :autofocus="autofocus"
                :placeholder="null"
                :type="$attrs.type || 'text'"
                :value="modelValue"
                @input="$emit('update:modelValue', $event.target.value)"
                @focus="isFocus = true; $emit('focus', $event)"
                @blur="isFocus = false; $emit('blur', $event)">
            <transition name="placeholderx">
                <span
                    v-if="isEmpty && (labelPlaceholder || $attrs.placeholder)"
                    ref="spanplaceholder"
                    class="input-span-placeholder vs-input--placeholder"
                    :class="[labelPlaceholder && size, size, { 'vs-placeholder-label': labelPlaceholder }]"
                    :style="styleLabel"
                    @click="focusInput">
                    {{ $attrs.placeholder || labelPlaceholder }}
                </span>
            </transition>
            <vs-icon
                v-if="icon"
                class="icon-inputx notranslate vs-input--icon"
                :class="{ 'icon-after': iconAfter, 'icon-no-border': iconNoBorder }"
                :icon-pack="iconPack"
                :icon="icon"
                @click="focusInput(); $emit('icon-click')" />
            <transition name="icon-validate">
                <span v-if="success || danger || warning" class="input-icon-validate vs-input--icon-validate" :class="{ 'icon-before': iconAfter }">
                    <vs-icon :class="{ 'icon-before': iconAfter }" :icon-pack="valIconPack" :icon="validIcon" />
                </span>
            </transition>
        </div>
        <div v-if="success" class="con-text-validation vs-input--text-validation">
            <span class="span-text-validation span-text-validation-success vs-input--text-validation-span">{{ successText }}</span>
        </div>
        <div v-else-if="danger" class="con-text-validation span-text-validation-danger vs-input--text-validation-span">
            <span class="span-text-validation">{{ dangerText }}</span>
        </div>
        <div v-else-if="warning" class="con-text-validation span-text-validation-warning vs-input--text-validation-span">
            <span class="span-text-validation">{{ warningText }}</span>
        </div>
        <div v-if="descriptionText" class="con-text-validation span-text-validation vs-input--text-validation-span">
            <span class="span-text-validation">{{ descriptionText }}</span>
        </div>
    </div>
</template>

<script>
import { color as vsColor } from './utils'

export default {
    name: 'VsInput',
    inheritAttrs: false,
    emits: ['update:modelValue', 'focus', 'blur', 'icon-click'],
    props: {
        modelValue: { default: '', type: [String, Number] },
        labelPlaceholder: { default: null, type: [String, Number] },
        label: { default: null, type: [String, Number] },
        autofocus: { default: false, type: [Boolean, String] },
        icon: { default: null, type: String },
        iconAfter: { default: false, type: [Boolean, String] },
        iconNoBorder: { default: false, type: Boolean },
        iconPack: { default: 'material-icons', type: String },
        color: { default: 'primary', type: String },
        success: { default: false, type: Boolean },
        danger: { default: false, type: Boolean },
        warning: { default: false, type: Boolean },
        successText: { default: null, type: String },
        dangerText: { default: null, type: String },
        warningText: { default: null, type: String },
        descriptionText: { default: null, type: String },
        size: { default: 'normal', type: String },
        valIconPack: { default: 'material-icons', type: String },
        valIconSuccess: { default: null, type: String },
        valIconDanger: { default: null, type: String },
        valIconWarning: { default: null, type: String },
    },
    data() {
        return { isFocus: false }
    },
    computed: {
        // Vue 3 puts class/style in $attrs: they belong on the wrapper, everything else on the <input>
        rootClass() { return this.$attrs.class },
        rootStyle() { return this.$attrs.style },
        inputAttrs() {
            const { class: _c, style: _s, placeholder: _p, type: _t, ...rest } = this.$attrs
            return rest
        },
        style() {
            return { border: `1px solid ${this.isFocus ? vsColor.getColor(this.color, 1) : 'rgba(0, 0, 0,.2)'}` }
        },
        styleLabel() {
            return { color: this.isFocus ? vsColor.getColor(this.color, 1) : null }
        },
        // Only an empty field shows its placeholder (0 is a value)
        isEmpty() {
            if (this.labelPlaceholder) return true
            return this.modelValue === undefined || this.modelValue === null || this.modelValue === ''
        },
        validIcon() {
            return this.danger ? this.valIconDanger : this.warning ? this.valIconWarning : this.success ? this.valIconSuccess : ''
        },
    },
    methods: {
        focusInput() {
            this.$refs.vsinput.focus()
        },
    },
}
</script>

<template>
    <header class="vs-navbar" :class="['vs-navbar-' + type, 'vs-navbar-color-' + color, { collapse }]" :style="styleNavbar">
        <div class="vs-navbar--header">
            <button class="vs-navbar--btn-responsive" :class="{ 'active-menu': activeMenuResponsive }" @click="activeMenuResponsive = !activeMenuResponsive">
                <span class="btn-responsive-line line--1" />
                <span class="btn-responsive-line line--2" />
                <span class="btn-responsive-line line--3" />
            </button>
            <slot name="title" />
        </div>
        <div class="vs-con-items" :class="{ activeMenuResponsive }"><slot /></div>
    </header>
</template>

<script>
import { color as vsColor } from './utils'

export default {
    name: 'VsNavbar',
    emits: ['update:modelValue'],
    props: {
        modelValue: {},
        type: { default: null, type: String },
        collapse: { default: false, type: Boolean },
        color: { type: String, default: 'transparent' },
        activeTextColor: { type: String, default: 'primary' },
        textColor: { type: String, default: 'rgb(40,40,40)' },
    },
    provide() {
        return { vsNavbar: this }
    },
    data() {
        return { activeMenuResponsive: false }
    },
    computed: {
        styleNavbar() {
            return { background: vsColor.isColor(this.color) ? `rgb(var(--vs-${this.color}))` : vsColor.getColor(this.color) }
        },
    },
    methods: {
        changeIndex(index) {
            this.$emit('update:modelValue', index)
        },
    },
}
</script>

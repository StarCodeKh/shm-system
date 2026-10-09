<template>
    <transition name="vs-sidebar-animate">
        <div v-show="staticPosition || modelValue" ref="sidebarbackground" v-bind="$attrs" class="vs-content-sidebar">
            <div v-if="!hiddenBackground" class="vs-sidebar--background" />
            <div
                ref="sidebarContainer"
                class="vs-sidebar"
                :class="['vs-sidebar-' + color, {
                    'vs-sidebar-parent': parent != 'body',
                    'vs-sidebar-staticPosition': staticPosition,
                    'vs-sidebar-position-right': positionRight,
                    'vs-sidebar-reduce': reduce,
                    'vs-sidebar-reduceNotRebound': reduceNotRebound,
                    'vs-sidebar-reduceNotHoverExpand': reduceNotHoverExpand,
                }]">
                <header v-if="$slots.header" class="vs-sidebar--header"><slot name="header" /></header>
                <div class="vs-sidebar--items"><slot /></div>
                <vs-spacer v-if="spacer" />
                <footer v-if="$slots.footer" class="vs-sidebar--footer"><slot name="footer" /></footer>
            </div>
        </div>
    </transition>
</template>

<script>
// Slide-in panel, drawn where it is used (the panel itself is position: fixed).
// A click outside the panel (inside `parent`, a selector such as ".layout--main") closes it, unless click-not-close is set.
export default {
    name: 'VsSidebar',
    inheritAttrs: false,
    emits: ['update:modelValue'],
    props: {
        modelValue: { default: false },
        defaultIndex: { default: null, type: [String, Number] },
        color: { default: 'primary', type: String },
        parent: { default: null, type: [String, Object] },
        spacer: { default: false, type: Boolean },
        staticPosition: { default: false, type: Boolean },
        positionRight: { default: false, type: Boolean },
        clickNotClose: { default: false, type: Boolean },
        reduce: { default: false, type: Boolean },
        reduceNotRebound: { default: false, type: Boolean },
        reduceNotHoverExpand: { default: false, type: Boolean },
        hiddenBackground: { default: false, type: Boolean },
    },
    data() {
        return { currentIndex: this.defaultIndex }
    },
    watch: {
        modelValue(open) {
            if (open && !this.clickNotClose) this.listenForOutsideClick()
        },
    },
    beforeUnmount() {
        this.stopListening()
    },
    methods: {
        clickArea() {
            return (typeof this.parent == 'string' ? document.querySelector(this.parent) : this.parent) || window
        },
        listenForOutsideClick() {
            // After the click that opened the panel has finished
            setTimeout(() => this.clickArea().addEventListener('click', this.closeSidebar), 300)
        },
        stopListening() {
            this.clickArea().removeEventListener('click', this.closeSidebar)
        },
        closeSidebar(evt) {
            if (evt.target.closest && evt.target.closest('.vs-sidebar')) return
            this.$emit('update:modelValue', false)
            this.stopListening()
        },
    },
}
</script>

<template>
    <teleport to="body">
        <transition name="popup-t">
            <div
                v-show="active"
                ref="con"
                v-bind="$attrs"
                class="vs-component con-vs-popup"
                :class="['vs-popup-' + color, { fullscreen }]"
                :style="zIndex ? { zIndex } : null"
                @click="onBackgroundClick">
                <div class="vs-popup--background" :style="{ background: getColor(backgroundColor) }" />
                <div ref="popupx" class="vs-popup" :style="stylePopup">
                    <header class="vs-popup--header" :style="{ color: getColor(color) }">
                        <div class="vs-popup--title">
                            <h3>{{ title }}</h3>
                            <slot name="subtitle" />
                        </div>
                        <vs-icon v-if="!buttonCloseHidden" class="vs-popup--close vs-popup--close--icon" :style="stylePopup" :icon-pack="iconPack" :icon="iconClose" @click="close" />
                    </header>
                    <div class="vs-popup--content" :class="classContent" :style="styleContent">
                        <slot />
                    </div>
                </div>
            </div>
        </transition>
    </teleport>
</template>

<script>
import { color as vsColor } from './utils'

// The popup opened last is drawn on top, also when opened from inside another popup
let topZIndex = 0

export default {
    name: 'VsPopup',
    inheritAttrs: false,
    emits: ['update:active', 'close'],
    props: {
        color: { default: 'primary', type: String },
        active: { default: false, type: Boolean },
        title: { default: 'popup', type: String },
        buttonCloseHidden: { default: false, type: Boolean },
        fullscreen: { default: false, type: Boolean },
        backgroundColor: { default: null, type: String },
        backgroundColorPopup: { default: 'rgb(255,255,255)', type: String },
        styleContent: { default: null, type: String },
        classContent: { default: null, type: String },
        iconPack: { default: 'material-icons', type: String },
        iconClose: { default: 'close', type: String },
    },
    data() {
        return { zIndex: null }
    },
    computed: {
        stylePopup() {
            return { background: vsColor.getColor(this.backgroundColorPopup, 1) }
        },
    },
    watch: {
        active: {
            immediate: true,
            handler(open) {
                if (!open) return
                this.$nextTick(() => {
                    const el = this.$refs.con
                    if (!el) return
                    this.zIndex = null
                    const own = parseInt(getComputedStyle(el).zIndex, 10) || 0
                    topZIndex = Math.max(topZIndex, own) + 1
                    this.zIndex = topZIndex
                })
            },
        },
    },
    methods: {
        getColor(value) {
            return vsColor.getColor(value, 1)
        },
        close() {
            this.$emit('update:active', false)
            this.$emit('close', false)
        },
        // Clicking the dimmed background closes the popup
        onBackgroundClick(event) {
            if (event.target.classList && event.target.classList.contains('vs-popup--background')) this.close()
        },
    },
}
</script>

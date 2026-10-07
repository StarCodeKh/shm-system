<template>
    <i class="vs-icon notranslate icon-scale" :class="[iconPack, iconPack != 'material-icons' ? icon : '', iconClass, bgClass, bgSizeClass, { round }]" :style="iconStyle">
        <slot>{{ iconPack == 'material-icons' ? icon : '' }}</slot>
    </i>
</template>

<script>
import { color as vsColor } from './utils'

const isLength = size => /(px|em)/.test(size || '') ? size : null

export default {
    name: 'VsIcon',
    props: {
        icon: { default: null, type: String },
        iconPack: { default: 'material-icons', type: String },
        color: { default: null, type: String },
        bg: { default: null, type: String },
        size: { default: null, type: String },
        round: { default: false, type: Boolean },
    },
    computed: {
        iconClass() {
            return { [this.size]: true, [`vs-icon-${this.color}`]: vsColor.isColor(this.color) }
        },
        iconStyle() {
            return { width: isLength(this.size), height: isLength(this.size), 'font-size': isLength(this.size), color: this.color, background: this.bg }
        },
        bgClass() {
            return { [`con-vs-icon-bg-${this.bg}`]: vsColor.isColor(this.bg) }
        },
        bgSizeClass() {
            const sized = ['small', 'medium', 'large'].includes(this.size)
            return { [`bg-${this.size}`]: sized, 'vs-icon-bg': sized }
        },
    },
}
</script>

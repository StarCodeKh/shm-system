<template>
    <li
        class="vs-component vs-dropdown--item"
        :class="{ divider }"
        :style="{ color: hoverx ? giveColor() + ' !important' : null, background: hoverx ? giveColor(0.01) + ' !important' : null }"
        @click="closeParent"
        @mouseover="hoverx = true"
        @mouseout="hoverx = false">
        <router-link v-if="to" v-bind="$attrs" class="vs-dropdown--item-link" :class="{ disabled }" :to="to"><slot /></router-link>
        <a v-else v-bind="$attrs" class="vs-dropdown--item-link" :class="{ disabled }"><slot /></a>
    </li>
</template>

<script>
import { color as vsColor } from './utils'

export default {
    name: 'VsDropdownItem',
    inheritAttrs: false,
    inject: { vsDropdown: { default: null } },
    props: {
        to: {},
        disabled: { default: false, type: Boolean },
        divider: { default: false, type: Boolean },
    },
    data() {
        return { hoverx: false }
    },
    methods: {
        // Choosing an item closes its menu
        closeParent() {
            if (!this.disabled && this.vsDropdown) this.vsDropdown.close()
        },
        giveColor(opacity = 1) {
            return vsColor.rColor(this.vsDropdown ? this.vsDropdown.color : null, opacity)
        },
    },
}
</script>

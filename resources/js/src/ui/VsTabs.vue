<template>
    <div class="con-vs-tabs vs-tabs" :class="['vs-tabs-' + color, 'vs-tabs-position-' + position]">
        <div class="con-ul-tabs">
            <ul ref="ul" class="ul-tabs vs-tabs--ul" :class="['ul-tabs-' + alignment]">
                <li
                    v-for="(tab, index) in tabs"
                    :key="tab.uid"
                    :ref="el => setLi(index, el)"
                    class="vs-tabs--li"
                    :class="{ activeChild: childActive == index }"
                    :style="childActive == index ? styleTab : {}"
                    @mouseover="hover = true"
                    @mouseout="hover = false">
                    <button v-bind="tab.$attrs" type="button" class="vs-tabs--btn" :style="tab.icon ? 'display:flex;align-items:center' : ''" @click="activeChild(index)">
                        <vs-icon v-if="tab.icon" class="vs-tabs--btn-icon" :icon-pack="tab.iconPack" :icon="tab.icon" :color="color" />
                        <span v-if="tab.label">{{ tab.label }}</span>
                    </button>
                    <button v-if="tab.tag" class="vs-tabs--btn-tag" @click="$emit('click-tag', tab)">
                        <vs-icon :icon-pack="tab.iconPack" :icon="tab.tag" :color="tab.tagColor" />
                    </button>
                </li>
            </ul>
            <span class="line-vs-tabs" :style="stylex" />
        </div>
        <div class="con-slot-tabs"><slot /></div>
    </div>
</template>

<script>
import { markRaw } from 'vue'
import { color as vsColor } from './utils'

export default {
    name: 'VsTabs',
    emits: ['update:modelValue', 'input', 'click-tag'],
    props: {
        modelValue: { default: undefined, type: [Number, String] },
        value: { default: 0, type: [Number, String] },
        color: { default: 'primary', type: String },
        tagColor: { default: 'primary', type: String },
        alignment: { default: 'left', type: String },
        position: { default: 'top', type: String },
    },
    provide() {
        return { vsTabs: this }
    },
    data() {
        return { topx: 'auto', heightx: 2, hover: false, tabs: [], childActive: 0, leftx: 0, widthx: 0, these: false }
    },
    computed: {
        requested() {
            return this.modelValue !== undefined ? this.modelValue : this.value
        },
        styleTab() {
            return { color: vsColor.getColor(this.color, 1) }
        },
        stylex() {
            return {
                top: `${this.topx}px`,
                left: `${this.leftx}px`,
                width: `${this.widthx}px`,
                height: `${this.heightx}px`,
                background: `linear-gradient(30deg, ${vsColor.getColor(this.color, 1)} 0%, ${vsColor.getColor(this.color, 0.5)} 100%)`,
                boxShadow: `0px 0px 8px 0px ${vsColor.getColor(this.color, 0.5)}`,
                transform: `scaleX(${this.these ? 1.3 : 1})`,
            }
        },
    },
    watch: {
        requested(index) {
            this.activeChild(this.parseIndex(index))
        },
    },
    created() {
        this.lis = []
    },
    mounted() {
        const index = this.parseIndex(this.requested)
        this.childActive = index
        this.$nextTick(() => this.activeChild(index, true))
    },
    methods: {
        // Called by each <vs-tab> as it appears (in order) / goes away
        register(tab) {
            this.tabs.push(markRaw(tab))
            if (this.tabs.length - 1 === this.childActive) tab.active = true
        },
        unregister(tab) {
            const at = this.tabs.indexOf(tab)
            if (at !== -1) this.tabs.splice(at, 1)
        },
        setLi(index, el) {
            this.lis[index] = el
        },
        parseIndex(index) {
            let active = this.childActive
            if (index < 0) active = 0
            else if (index >= this.tabs.length) active = Math.max(0, this.tabs.length - 1)
            else if (this.tabs[index] && typeof this.tabs[index].$attrs.disabled === 'undefined') active = parseInt(index, 10)
            return active
        },
        activeChild(index, initialAnimation = false) {
            const elem = this.lis[index]
            const tab = this.tabs[index]
            if (!tab) return
            if (this.childActive == index && !initialAnimation && elem) {
                this.these = true
                elem.classList.add('isActive')
                setTimeout(() => { elem.classList.remove('isActive'); this.these = false }, 200)
            }
            this.tabs.forEach((item, i) => { if (i != index) item.active = false })
            const previous = this.tabs[this.childActive]
            if (this.childActive > index) {
                tab.invert = true
                if (previous) previous.invert = false
            } else {
                if (previous) previous.invert = true
                tab.invert = false
            }
            tab.active = true
            this.childActive = index
            this.$emit('update:modelValue', index)
            this.$emit('input', index)
            if (this.position == 'left' || this.position == 'right') tab.vertical = true
            if (elem) this.changePositionLine(elem, initialAnimation)
        },
        changePositionLine(elem, initialAnimation) {
            if (this.position == 'left' || this.position == 'right') {
                this.topx = elem.offsetTop
                this.heightx = elem.offsetHeight
                this.widthx = 2
                return
            }
            const update = () => {
                this.leftx = elem.offsetLeft
                this.widthx = elem.offsetWidth
                this.topx = elem.offsetHeight + (elem.getBoundingClientRect().top - this.$refs.ul.getBoundingClientRect().top)
            }
            initialAnimation ? setTimeout(update, 100) : update()
        },
    },
}
</script>

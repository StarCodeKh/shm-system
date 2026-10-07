<template>
    <transition name="noti" @before-enter="el => (el.style.opacity = 0)" @enter="(el, done) => { el.style.opacity = 1; done() }" @leave="leave" @after-leave="$emit('gone')">
        <div
            v-if="active"
            ref="noti"
            class="vs-component vs-notifications"
            :class="['vs-noti-' + position, 'vs-noti-' + color, { activeNoti: active }]"
            :style="stylex"
            @click="clickNoti">
            <div class="content-noti">
                <div class="con-text-noti">
                    <h3 v-html="title" />
                    <p v-html="text" />
                </div>
                <vs-icon v-if="icon" class="vs-icon-noti" :icon="icon" :icon-pack="iconPack" />
            </div>
            <span class="filling" :style="fillingStyle" />
        </div>
    </transition>
</template>

<script>
// One toast from $vs.notify({ title, text, color, icon, position, time, fixed, click }) (see vs.js)
export default {
    name: 'VsNotification',
    emits: ['gone'],
    props: {
        title: { default: null },
        text: { default: null },
        color: { default: 'primary' },
        colorText: { default: 'rgb(255, 255, 255)' },
        icon: { default: null },
        iconPack: { default: 'material-icons' },
        position: { default: 'bottom-right' },
        time: { default: 3000 },
        fixed: { default: false },
        fullWidth: { default: false },
        click: { type: Function, default: null },
    },
    data() {
        return { active: true, widthx: 0, cords: { top: null, left: null, right: null, bottom: null } }
    },
    computed: {
        fillingStyle() {
            return {
                left: this.cords.left ? '-100px' : null,
                right: this.cords.right ? '-100px' : null,
                background: this.color,
                height: `${this.widthx * 2}px`,
                width: `${this.widthx * 2}px`,
            }
        },
        stylex() {
            return { ...this.cords, color: this.colorText, width: this.fullWidth ? 'calc(100% - 14px)' : '', maxWidth: this.fullWidth ? 'none' : '350px' }
        },
    },
    created() {
        const has = side => this.position.split('-').includes(side)
        // Top corners start below the fixed navbar (it sets --navbar-bottom), so a toast never covers the profile / theme buttons
        if (has('top')) this.cords.top = has('center') ? '0px' : 'var(--navbar-bottom, 0px)'
        if (has('bottom')) this.cords.bottom = '0px'
        if (has('right')) this.cords.right = '0px'
        if (has('left')) this.cords.left = '0px'
        if (has('center')) this.cords.left = '50%'
        setTimeout(() => this.stack(), 0)
    },
    mounted() {
        setTimeout(() => { if (this.$refs.noti) this.widthx = this.$refs.noti.clientWidth }, 0)
        if (!this.fixed) setTimeout(() => { this.active = false }, this.time)
    },
    methods: {
        clickNoti() {
            this.active = false
            if (this.click) this.click()
        },
        leave(el, done) {
            el.style.opacity = 0
            let transform = el.style.transform
            if (this.cords.left == '50%' || this.fullWidth) transform += ` translateY(${this.cords.top ? '-' : ''}100%)`
            else transform += ` translateX(${this.cords.left ? '-' : ''}100%)`
            el.style.transform = transform
            setTimeout(() => { done(); this.stack() }, 150)
        },
        // Toasts in the same corner stack on top of each other
        stack() {
            const notis = document.querySelectorAll('.vs-noti-' + this.position)
            notis.forEach((noti, i) => {
                let offset = 10
                for (let j = 0; j < i; j++) offset += notis[j].clientHeight + 6
                const centered = this.position.includes('center')
                if (!centered && this.position.includes('top')) noti.style.transform = `translatey(${offset}px)`
                else if (!centered && this.position.includes('bottom')) noti.style.transform = `translatey(-${offset}px)`
                if (centered) {
                    noti.style.transform = `translate(-50%,${this.position.includes('top') ? '' : '-'}${offset}px)`
                    noti.style.zIndex = 10000 - i
                }
            })
        },
    },
}
</script>

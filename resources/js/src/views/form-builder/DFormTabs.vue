<template>
    <div class="d-tabform">
        <!-- Header -->
        <div v-if="title" class="d-tabform__head">
            <span class="d-tabform__icon">
                <feather-icon :icon="icon" svgClasses="h-5 w-5" />
            </span>
            <div class="d-tabform__heading">
                <h2>{{ title }}</h2>
                <p v-if="subtitle">{{ subtitle }}</p>
            </div>
            <span class="d-tabform__step">{{ activeIndex + 1 }} / {{ sections.length }}</span>
        </div>

        <!-- Tabs: one per section; a badge counts the validation errors inside it -->
        <nav ref="nav" class="d-tabform__tabs" role="tablist">
            <!-- Highlight that slides to the active tab -->
            <span class="d-tabform__indicator" :class="{ 'is-ready': indicator.width }" :style="indicatorStyle" />
            <button v-for="(section, i) in sections" :key="section.key" ref="tab" type="button" role="tab" class="d-tabform__tab"
                :class="{ 'is-active': section.key === current, 'is-done': i < activeIndex, 'has-error': errorCount[section.key] }"
                :aria-selected="section.key === current" @click="select(section.key)">
                <span class="d-tabform__tab-icon">
                    <feather-icon :icon="section.icon || 'CircleIcon'" svgClasses="h-4 w-4" />
                </span>
                <span class="d-tabform__tab-title">{{ section.title }}</span>
                <transition name="d-tabform-pop">
                    <span v-if="errorCount[section.key]" class="d-tabform__tab-badge">{{ errorCount[section.key] }}</span>
                </transition>
            </button>
        </nav>

        <!-- Sections: the parent marks each block with data-section="key" and v-show -->
        <div ref="body" class="d-tabform__body" :data-dir="direction">
            <slot />
        </div>

        <!-- Footer -->
        <div class="d-tabform__footer">
            <button v-if="activeIndex > 0" type="button" class="d-tabform__btn" @click="go(-1)">
                <feather-icon icon="ChevronLeftIcon" svgClasses="h-4 w-4" />
                <span>{{ $t('form_prev') }}</span>
            </button>
            <span class="d-tabform__spacer" />
            <slot name="actions" />
            <button v-if="activeIndex < sections.length - 1" type="button" class="d-tabform__btn d-tabform__btn--next"
                @click="go(1)">
                <span>{{ $t('form_next') }}</span>
                <feather-icon icon="ChevronRightIcon" svgClasses="h-4 w-4" />
            </button>
        </div>
    </div>
</template>

<script>
// Tabbed shell for long forms. Usage:
//   <d-form-tabs v-model="tab" :sections="[{ key, title, icon }]" :errors="errors" title="…">
//     <div data-section="personal" v-show="tab === 'personal'">…fields…</div>
//     <template #actions>…save button…</template>
//   </d-form-tabs>
// Sections stay in the DOM (v-show), so vee-validate checks every tab on submit;
// call `focusFirstError()` after a failed validation to jump to the first tab with an error.
export default {
    name: 'd-form-tabs',
    props: {
        // v-model (Vue 3), or :value + @input
        modelValue: { type: String, default: undefined },
        value: { type: String, default: '' },
        sections: { type: Array, required: true },
        errors: { type: Object, default: null }, // vee-validate ErrorBag of the parent form
        title: { type: String, default: '' },
        subtitle: { type: String, default: '' },
        icon: { type: String, default: 'FileTextIcon' },
    },
    data: () => ({
        ready: false,
        direction: 'next', // which way the content slides when the tab changes
        indicator: { left: 0, width: 0 },
    }),
    emits: ['update:modelValue', 'input'],
    computed: {
        current() {
            return this.modelValue !== undefined ? this.modelValue : this.value
        },
        activeIndex() {
            return Math.max(0, this.sections.findIndex(section => section.key === this.current))
        },
        indicatorStyle() {
            return { width: `${this.indicator.width}px`, transform: `translateX(${this.indicator.left}px)` }
        },
        errorCount() {
            const counts = {}
            if (!this.ready || !this.errors || !this.errors.items) return counts
            this.errors.items.forEach(item => {
                const key = this.sectionOf(item.field)
                if (key) counts[key] = (counts[key] || 0) + 1
            })
            return counts
        },
    },
    methods: {
        select(key) {
            if (key !== this.current) {
                this.$emit('update:modelValue', key)
                this.$emit('input', key)
            }
        },
        go(step) {
            const next = this.sections[this.activeIndex + step]
            if (next) {
                this.select(next.key)
                this.$el.scrollIntoView({ behavior: 'smooth', block: 'start' })
            }
        },
        sectionOf(field) {
            const body = this.$refs.body
            if (!body || !field) return null
            const el = body.querySelector(`[name="${String(field).replace(/"/g, '\\"')}"]`)
            const section = el && el.closest('[data-section]')
            return section ? section.getAttribute('data-section') : null
        },
        // Measure the active tab so the highlight can slide to it, and keep that tab scrolled into view
        updateIndicator(scroll = true) {
            const tab = (this.$refs.tab || [])[this.activeIndex]
            const nav = this.$refs.nav
            if (!tab || !nav) return
            this.indicator = { left: tab.offsetLeft, width: tab.offsetWidth }
            if (scroll) {
                const target = tab.offsetLeft - (nav.clientWidth - tab.offsetWidth) / 2
                nav.scrollTo({ left: Math.max(0, target), behavior: 'smooth' })
            }
        },
        // Re-measure when the bar or any tab changes width (an error badge appears, the language changes)
        observeTabs() {
            if (!this.resizeObserver) return
            this.resizeObserver.disconnect()
            this.resizeObserver.observe(this.$refs.nav);
            (this.$refs.tab || []).forEach(tab => this.resizeObserver.observe(tab))
        },
        focusFirstError() {
            const first = this.errors && this.errors.items && this.errors.items.find(item => this.sectionOf(item.field))
            if (first) this.select(this.sectionOf(first.field))
        },
    },
    watch: {
        current(next, prev) {
            const index = key => this.sections.findIndex(section => section.key === key)
            this.direction = index(next) < index(prev) ? 'prev' : 'next'
            this.$nextTick(() => this.updateIndicator())
        },
        sections() {
            this.$nextTick(() => {
                this.observeTabs()
                this.updateIndicator(false)
            })
        },
    },
    mounted() {
        this.ready = true
        if (!this.current && this.sections.length) this.select(this.sections[0].key)
        this.$nextTick(() => this.updateIndicator(false))
        // Tab widths change with fonts, language and screen size
        this.onResize = () => this.updateIndicator(false)
        window.addEventListener('resize', this.onResize)
        if (typeof ResizeObserver !== 'undefined') {
            this.resizeObserver = new ResizeObserver(this.onResize)
            this.observeTabs()
        }
        if (document.fonts && document.fonts.ready) document.fonts.ready.then(this.onResize)
    },
    beforeUnmount() {
        window.removeEventListener('resize', this.onResize)
        if (this.resizeObserver) this.resizeObserver.disconnect()
    },
}
</script>

<style lang="scss">
.d-tabform {
    border: 1px solid #eceef4;
    border-radius: 16px;
    background: #fff;
    box-shadow: 0 4px 24px -10px rgba(23, 23, 74, .12);
    scroll-margin-top: 90px; // keeps the header below the sticky navbar when scrolled into view

    // ---------------------------------------------------------------- Header
    &__head {
        display: flex;
        align-items: center;
        gap: 14px;
        padding: 20px 24px;
        border-bottom: 1px solid #eef0f4;
    }

    &__icon {
        flex-shrink: 0;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 44px;
        height: 44px;
        border-radius: 12px;
        color: rgb(var(--vs-primary));
        background: rgba(var(--vs-primary), .12);

        .feather-icon {
            display: flex;
        }
    }

    &__heading {
        flex: 1;
        min-width: 0;

        h2 {
            font-size: 1.2rem;
            font-weight: 700;
            color: #2c2e45;
        }

        p {
            margin-top: 2px;
            font-size: .82rem;
            color: #8a8ca5;
        }
    }

    &__step {
        flex-shrink: 0;
        padding: 4px 10px;
        font-size: .8rem;
        font-weight: 700;
        color: #6b6d7a;
        border-radius: 999px;
        background: #f3f4f7;
    }

    // ---------------------------------------------------------------- Tabs
    &__tabs {
        position: relative;
        display: flex;
        gap: 6px;
        padding: 12px 16px;
        border-bottom: 1px solid #eef0f4;
        background: #fbfbfd;
        overflow-x: auto;
        scrollbar-width: none;

        &::-webkit-scrollbar {
            display: none;
        }
    }

    // Sliding highlight behind the active tab
    &__indicator {
        position: absolute;
        top: 12px;
        left: 0;
        height: 42px;
        border: 1px solid rgba(var(--vs-primary), .35);
        border-radius: 999px;
        background: rgba(var(--vs-primary), .1);
        box-shadow: 0 6px 16px -10px rgba(var(--vs-primary), .9);
        opacity: 0;
        pointer-events: none;

        &.is-ready {
            opacity: 1;
            transition: transform var(--tab-move) var(--tab-ease), width var(--tab-move) var(--tab-ease), opacity var(--tab-tap) ease;
        }
    }

    &__tab {
        position: relative;
        z-index: 1;
        flex: 0 0 auto;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 5px 14px 5px 5px;
        font-family: inherit;
        font-size: .86rem;
        font-weight: 600;
        color: #5f6170;
        border: 1px solid transparent;
        border-radius: 999px;
        background: transparent;
        cursor: pointer;
        transition: background var(--tab-tap) ease, border-color var(--tab-tap) ease, color var(--tab-tap) ease;

        &:hover {
            background: #fff;
            border-color: #e6e8ee;
        }

        &.is-active {
            color: rgb(var(--vs-primary));
            background: transparent;
            border-color: transparent;

            .d-tabform__tab-icon {
                color: #fff;
                background: rgb(var(--vs-primary));
                transform: scale(1.06);
                box-shadow: 0 4px 10px -4px rgba(var(--vs-primary), .8);

                svg {
                    animation: d-tabform-icon var(--tab-move) var(--tab-ease);
                }
            }
        }

        &.is-done .d-tabform__tab-icon {
            color: rgb(var(--vs-primary));
            background: rgba(var(--vs-primary), .12);
        }

        &.has-error:not(.is-active) {
            color: rgb(var(--vs-danger));
        }
    }

    &__tab-icon {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 30px;
        height: 30px;
        border-radius: 999px;
        color: #6b6d7a;
        background: #eef0f4;
        transition: background var(--tab-move) var(--tab-ease), color var(--tab-move) var(--tab-ease), transform var(--tab-move) var(--tab-ease), box-shadow var(--tab-move) var(--tab-ease);

        .feather-icon {
            display: flex;
        }
    }

    &__tab-badge {
        min-width: 18px;
        height: 18px;
        padding: 0 5px;
        font-size: .7rem;
        font-weight: 700;
        line-height: 18px;
        text-align: center;
        color: #fff;
        border-radius: 999px;
        background: rgb(var(--vs-danger));
    }

    // ---------------------------------------------------------------- Body
    &__body {
        padding: 8px 24px 24px;
    }

    // ---------------------------------------------------------------- Footer
    &__footer {
        position: sticky;
        bottom: 0;
        z-index: 5;
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 10px;
        padding: 14px 24px;
        border-top: 1px solid #eef0f4;
        border-radius: 0 0 16px 16px;
        background: rgba(255, 255, 255, .96);
        backdrop-filter: blur(6px);
    }

    &__spacer {
        flex: 1;
    }

    &__btn {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        height: var(--btn-height);
        padding: 0 var(--btn-padding-x);
        font-family: inherit;
        font-size: var(--btn-font-size);
        font-weight: var(--btn-font-weight);
        color: #4b4d68;
        border: 1px solid var(--input-border, #d8d9e3);
        border-radius: var(--btn-radius);
        background: #fff;
        cursor: pointer;

        .feather-icon {
            display: flex;
        }

        &:hover {
            color: rgb(var(--vs-primary));
            border-color: rgba(var(--vs-primary), .5);
            background: rgba(var(--vs-primary), .05);
        }

        &--primary,
        &--next {
            color: #fff;
            border-color: rgb(var(--vs-primary));
            background: rgb(var(--vs-primary));
            box-shadow: 0 6px 14px -8px rgba(var(--vs-primary), .9);

            &:hover {
                color: #fff;
                background: rgba(var(--vs-primary), .9);
            }
        }

        &--next {
            color: rgb(var(--vs-primary));
            background: rgba(var(--vs-primary), .1);
            border-color: transparent;
            box-shadow: none;

            &:hover {
                color: rgb(var(--vs-primary));
                background: rgba(var(--vs-primary), .16);
            }
        }
    }

    @media (max-width: 640px) {
        &__head,
        &__body,
        &__footer {
            padding-left: 16px;
            padding-right: 16px;
        }

        &__tab:not(.is-active) .d-tabform__tab-title {
            display: none;
        }

        &__tab:not(.is-active) {
            padding-right: 5px;
        }
    }
}

// ---------------------------------------------------------------- Field grid inside tabs (.form-grid / .form-group markup)
.d-tabform__body {
    // Content slides in from the side the user is moving towards
    .form-section {
        padding-top: 16px;
        // "backwards", not "both": a kept end transform makes the section a stacking context, and its
        // open dropdowns then slide under the sticky footer
        animation: d-tabform-next var(--tab-fade) var(--tab-ease) backwards;
    }

    &[data-dir="prev"] .form-section {
        animation-name: d-tabform-prev;
    }

    .form-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: var(--field-gap, 20px) 24px;
    }

    .form-group {
        display: flex;
        flex-direction: column;
        min-width: 0;

        &.full-width {
            grid-column: 1 / -1;
        }

        &.half-width {
            grid-column: span 2;
        }

        > label {
            display: flex;
            align-items: center;
            gap: 6px;
            margin-bottom: 6px;
            font-size: .875rem;
            font-weight: 600;
            color: #4b4d68;
        }

        .required::after {
            content: '*';
            color: rgb(var(--vs-danger));
        }

        .badge {
            margin-left: 2px;
            padding: 1px 8px;
            font-size: .7rem;
            font-weight: 600;
            color: #8a8ca5;
            border-radius: 6px;
            background: #f1f2f5;
        }

        // Controls share one size; old icon wrappers and margins are neutralised
        .vs-input,
        .v-select,
        .vs-con-textarea,
        .flatpickr-input,
        .d-file-picker {
            margin-top: 0 !important;
            margin-bottom: 0 !important;
        }

        .input-icon {
            position: static;

            > i {
                display: none;
            }

            input {
                padding-left: 14px;
            }
        }

        .text-danger {
            display: block;
            margin-top: 4px;
            font-size: .78rem;
        }
    }

    @media (max-width: 1024px) {
        .form-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 640px) {
        .form-grid {
            grid-template-columns: minmax(0, 1fr);
        }

        .form-group.half-width {
            grid-column: auto;
        }
    }
}

@keyframes d-tabform-next {
    from { opacity: 0; transform: translateX(12px); }
    to { opacity: 1; transform: none; }
}

@keyframes d-tabform-prev {
    from { opacity: 0; transform: translateX(-12px); }
    to { opacity: 1; transform: none; }
}

@keyframes d-tabform-icon {
    from { transform: scale(.7); opacity: .4; }
    to { transform: none; opacity: 1; }
}

.d-tabform-pop-enter-active,
.d-tabform-pop-leave-active {
    transition: transform .25s cubic-bezier(.34, 1.56, .64, 1), opacity .2s ease;
}

.d-tabform-pop-enter-from,
.d-tabform-pop-leave-to {
    opacity: 0;
    transform: scale(.4);
}

@media (prefers-reduced-motion: reduce) {
    .d-tabform *,
    .d-tabform *::before {
        animation: none !important;
        transition: none !important;
    }
}

.theme-dark .d-tabform__body .form-group > label {
    color: #c2c6dc;
}

.theme-dark .d-tabform {
    background: #262c49;
    border-color: #414561;

    &__head,
    &__tabs,
    &__footer {
        border-color: #414561;
    }

    &__tabs {
        background: #1e2442;
    }

    &__footer {
        background: rgba(38, 44, 73, .96);
    }

    &__heading h2 {
        color: #fff;
    }

    &__tab {
        color: #c2c6dc;
    }

    &__tab-icon,
    &__step {
        background: #10163a;
        color: #c2c6dc;
    }

    &__btn {
        color: #c2c6dc;
        background: #10163a;
        border-color: #414561;
    }
}
</style>

<template>
    <div class="nav-lang-switch" :class="{ 'is-switching': switching }" role="group" :aria-label="$t('nav_language')"
        :style="{ '--lang-index': activeIndex }">
        <!-- Pill slides under the active language -->
        <span class="nav-lang-switch__pill" aria-hidden="true"></span>
        <button v-for="lang in languages" :key="lang.code" type="button"
            :class="{ active: lang.code === current }" :aria-pressed="lang.code === current"
            :title="lang.name" @click="updateLocale(lang.code)">
            <img :src="lang.flag" :alt="lang.code" />
            <span>{{ lang.label }}</span>
        </button>
    </div>
</template>

<script>
import { setLocale } from '@/i18n/i18n'
import flagKh from '@assets/images/flags/kh.svg'
import flagEn from '@assets/images/flags/en.svg'

const LANGUAGES = [
    { code: 'kh', label: 'ខ្មែរ', name: 'ភាសាខ្មែរ', flag: flagKh },
    { code: 'en', label: 'EN', name: 'English', flag: flagEn },
]

export default {
    data() {
        return {
            languages: LANGUAGES,
            // Moves first so the pill reacts on the click, before the page re-renders
            current: this.$i18n.locale,
            switching: false,
        }
    },
    computed: {
        activeIndex() {
            return Math.max(0, this.languages.findIndex(lang => lang.code === this.current))
        },
    },
    watch: {
        // Locale changed elsewhere (e.g. login page)
        '$i18n.locale'(locale) {
            this.current = locale
        },
    },
    methods: {
        updateLocale(locale) {
            if (locale === this.current) return
            this.current = locale
            this.switching = true
            // Let the pill start moving, then swap every text on the page in one go
            requestAnimationFrame(() => requestAnimationFrame(() => {
                setLocale(this.$i18n, locale)
                setTimeout(() => { this.switching = false }, 250)
            }))
        },
    }
}
</script>

<style lang="scss">
// Same segmented style as the Dashboard currency switch, with a sliding pill
.nav-lang-switch {
    position: relative;
    display: inline-grid;
    grid-template-columns: repeat(2, 1fr);
    padding: 3px;
    margin-right: 6px;
    border-radius: 999px;
    background: #eef0f4;

    &__pill {
        position: absolute;
        top: 3px;
        bottom: 3px;
        left: 3px;
        width: calc(50% - 3px);
        border-radius: 999px;
        background: #fff;
        box-shadow: 0 2px 6px rgba(0, 0, 0, .1);
        transform: translateX(calc(var(--lang-index, 0) * 100%));
        transition: transform var(--tab-move) var(--tab-ease); // the same slide as every tab
        will-change: transform;
    }

    button {
        position: relative;
        z-index: 1;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        padding: .35rem .8rem;
        font-family: inherit;
        font-size: .875rem;
        color: #626262;
        border: 0;
        border-radius: 999px;
        background: transparent;
        cursor: pointer;
        transition: color var(--tab-tap) ease;

        img {
            width: 18px;
            height: 13px;
            border-radius: 2px;
            object-fit: cover;
            box-shadow: 0 0 0 1px rgba(0, 0, 0, .08);
            opacity: .6;
            transition: opacity .2s ease, transform .3s cubic-bezier(.34, 1.56, .64, 1);
        }

        &:hover:not(.active) {
            color: #2c2e45;

            img {
                opacity: .85;
            }
        }

        &:active img {
            transform: scale(.88);
        }

        &.active {
            color: rgba(var(--vs-primary), 1);
            font-weight: 600;

            img {
                opacity: 1;
                transform: scale(1.08);
            }
        }
    }

    // Small pop on the label that was just picked
    &.is-switching button.active span {
        animation: nav-lang-pop .3s cubic-bezier(.34, 1.56, .64, 1);
    }

    // Flags only on small screens
    @media (max-width: 640px) {
        button {
            padding: .4rem .6rem;

            span {
                display: none;
            }
        }
    }
}

@keyframes nav-lang-pop {
    from {
        opacity: .4;
        transform: translateY(3px);
    }
}

.theme-dark .nav-lang-switch {
    background: #10163a;

    .nav-lang-switch__pill {
        background: #262c49;
        box-shadow: 0 2px 6px rgba(0, 0, 0, .3);
    }

    button {
        color: #c2c6dc;

        &:hover:not(.active) {
            color: #fff;
        }

        &.active {
            color: #fff;
        }
    }
}

@media (prefers-reduced-motion: reduce) {
    .nav-lang-switch__pill,
    .nav-lang-switch button,
    .nav-lang-switch button img,
    .nav-lang-switch.is-switching button.active span {
        transition: none;
        animation: none;
    }
}
</style>

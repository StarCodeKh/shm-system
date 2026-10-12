<template>
    <div class="landing">
        <!-- Top bar: clear over the hero, frosted once the page scrolls -->
        <header class="landing-nav" :class="{ 'is-scrolled': scrolled }">
            <a href="#top" class="landing-nav__brand" @click.prevent="go('top')">
                <logo :src="site.logo" :alt="siteName" class="landing-nav__logo" />
                <span>{{ siteName }}</span>
            </a>
            <nav class="landing-nav__links">
                <a v-if="page.features.length" href="#features" @click.prevent="go('features')">{{ $t('landing_nav_features') }}</a>
                <a v-if="page.stats.length" href="#numbers" @click.prevent="go('numbers')">{{ $t('landing_nav_numbers') }}</a>
                <a href="#contact" @click.prevent="go('contact')">{{ $t('landing_nav_contact') }}</a>
            </nav>
            <div class="landing-nav__end">
                <i18n />
                <router-link :to="ctaPath" class="landing-btn landing-btn--primary landing-btn--sm">
                    {{ ctaText }}
                </router-link>
            </div>
        </header>

        <!-- Hero -->
        <section id="top" class="landing-hero">
            <span class="landing-hero__blob landing-hero__blob--a" aria-hidden="true" />
            <span class="landing-hero__blob landing-hero__blob--b" aria-hidden="true" />
            <span class="landing-hero__grid" aria-hidden="true" />

            <div class="landing-hero__inner">
                <div class="landing-hero__text">
                    <span v-if="site.owner" class="landing-eyebrow">
                        <i class="landing-eyebrow__dot" />{{ site.owner }}
                    </span>
                    <template v-if="page.hero">
                        <h1 :key="'h' + $i18n.locale">{{ page.hero.title }}</h1>
                        <p v-if="page.hero.description" :key="'p' + $i18n.locale">{{ page.hero.description }}</p>
                    </template>
                    <template v-else-if="loading">
                        <span class="landing-skeleton landing-skeleton--title" />
                        <span class="landing-skeleton landing-skeleton--line" />
                        <span class="landing-skeleton landing-skeleton--line is-short" />
                    </template>
                    <h1 v-else>{{ $t('welcome', { name: siteName }) }}</h1>

                    <div class="landing-hero__actions">
                        <router-link :to="ctaPath" class="landing-btn landing-btn--light">
                            {{ ctaText }}
                            <feather-icon icon="ArrowRightIcon" svgClasses="h-4 w-4" />
                        </router-link>
                        <a v-if="page.features.length" href="#features" class="landing-btn landing-btn--ghost" @click.prevent="go('features')">
                            {{ $t('landing_explore') }}
                        </a>
                    </div>
                </div>

                <!-- Picture from Settings, or a small animated preview of the system -->
                <div class="landing-hero__visual">
                    <img v-if="page.hero && page.hero.image" :src="page.hero.image" alt="" class="landing-hero__image" />
                    <div v-else class="landing-mock" aria-hidden="true">
                        <div class="landing-mock__bar"><i /><i /><i /></div>
                        <div class="landing-mock__body">
                            <div class="landing-mock__side">
                                <span v-for="n in 5" :key="n" :class="{ 'is-on': n === 2 }" />
                            </div>
                            <div class="landing-mock__main">
                                <div class="landing-mock__tiles">
                                    <span v-for="n in 3" :key="n" />
                                </div>
                                <div class="landing-mock__chart">
                                    <span v-for="(h, n) in bars" :key="n" :style="{ '--h': h + '%', '--i': n }" />
                                </div>
                            </div>
                        </div>
                        <div v-for="(stat, n) in page.stats.slice(0, 2)" :key="stat.title" class="landing-mock__chip"
                            :class="'landing-mock__chip--' + n">
                            <span class="landing-mock__chip-icon"><feather-icon :icon="stat.icon || 'activity'" svgClasses="h-4 w-4" /></span>
                            <span><b>{{ number(stat.value) }}</b><small>{{ stat.title }}</small></span>
                        </div>
                    </div>
                </div>
            </div>

            <button type="button" class="landing-hero__scroll" :aria-label="$t('landing_explore')" @click="go(page.stats.length ? 'numbers' : 'features')">
                <feather-icon icon="ChevronDownIcon" svgClasses="h-5 w-5" />
            </button>
        </section>

        <!-- Live numbers, counted up when they come into view -->
        <section v-if="page.stats.length" id="numbers" ref="numbers" class="landing-section landing-numbers">
            <div class="landing-numbers__grid">
                <div v-for="(stat, n) in page.stats" :key="stat.title" class="landing-stat" data-reveal :style="{ '--delay': n * 80 + 'ms' }">
                    <span class="landing-stat__icon"><feather-icon :icon="stat.icon || 'activity'" svgClasses="h-5 w-5" /></span>
                    <b>{{ number(shown[n] ?? 0) }}</b>
                    <span>{{ stat.title }}</span>
                </div>
            </div>
            <p class="landing-numbers__hint"><i class="landing-eyebrow__dot" />{{ $t('landing_numbers_hint') }}</p>
        </section>

        <!-- Features -->
        <section v-if="page.features.length" id="features" class="landing-section">
            <div class="landing-section__head" data-reveal>
                <h2>{{ $t('landing_features_title') }}</h2>
                <p>{{ $t('landing_features_subtitle') }}</p>
            </div>
            <div class="landing-features">
                <article v-for="(feature, n) in page.features" :key="feature.title + n" class="landing-feature" data-reveal
                    :style="{ '--delay': (n % 3) * 90 + 'ms' }">
                    <span class="landing-feature__icon"><feather-icon :icon="feature.icon || 'star'" svgClasses="h-6 w-6" /></span>
                    <h3>{{ feature.title }}</h3>
                    <p v-if="feature.description">{{ feature.description }}</p>
                </article>
            </div>
        </section>

        <!-- Call to action -->
        <section class="landing-section">
            <div class="landing-cta" data-reveal>
                <span class="landing-hero__blob landing-hero__blob--a" aria-hidden="true" />
                <h2>{{ $t('landing_cta_title') }}</h2>
                <p>{{ $t('landing_cta_text') }}</p>
                <router-link :to="ctaPath" class="landing-btn landing-btn--light">
                    {{ ctaText }}
                    <feather-icon icon="ArrowRightIcon" svgClasses="h-4 w-4" />
                </router-link>
            </div>
        </section>

        <!-- Contact, from Settings › General -->
        <footer id="contact" class="landing-footer">
            <div class="landing-footer__inner">
                <div class="landing-footer__brand">
                    <logo :src="site.logo" :alt="siteName" class="landing-nav__logo" />
                    <div>
                        <b>{{ siteName }}</b>
                        <span v-if="site.owner">{{ site.owner }}</span>
                    </div>
                </div>
                <div class="landing-footer__contact">
                    <h4>{{ $t('landing_nav_contact') }}</h4>
                    <a v-if="site.telephone" :href="'tel:' + site.telephone.replace(/\s+/g, '')">
                        <feather-icon icon="PhoneIcon" svgClasses="h-4 w-4" />{{ site.telephone }}
                    </a>
                    <a v-if="site.email" :href="'mailto:' + site.email">
                        <feather-icon icon="MailIcon" svgClasses="h-4 w-4" />{{ site.email }}
                    </a>
                    <a v-if="site.website" :href="site.website" target="_blank" rel="noopener">
                        <feather-icon icon="GlobeIcon" svgClasses="h-4 w-4" />{{ site.website.replace(/^https?:\/\//, '') }}
                    </a>
                </div>
            </div>
            <div class="landing-footer__bottom">
                <span>{{ site.footer || '© ' + year + ' ' + siteName }}</span>
                <a href="#top" @click.prevent="go('top')">
                    {{ $t('landing_back_top') }} <feather-icon icon="ArrowUpIcon" svgClasses="h-3 w-3" />
                </a>
            </div>
        </footer>
    </div>
</template>

<script>
import axios from "@/axios.js"
import apiConfig from "@/apiConfig.js"
import { localDigits, onLocaleChange } from "@/i18n/i18n"
import { site, loadSite } from "@/site.js"
import I18n from "@/layouts/components/navbar/components/I18n.vue"
import Logo from "@/layouts/components/Logo.vue"

const EMPTY = { hero: null, features: [], stats: [] }
const cacheKey = locale => "landing." + locale

// Public landing page (/welcome): hero, live numbers and features from Settings › Landing page
// (GET /api/v1/landing, in the reader's language), contact details from Settings › General.
// The last answer per language is kept, so the page shows at once on the next visit, then refreshes.
export default {
    components: { I18n, Logo },
    data() {
        return {
            site,
            page: { ...EMPTY },
            loading: true,
            scrolled: false,
            shown: [], // numbers as they count up
            counted: false,
            bars: [42, 68, 55, 80, 62, 92, 74],
            year: new Date().getFullYear(),
        }
    },
    computed: {
        siteName() {
            return this.site.name || this.$t("brand_name_fallback")
        },
        signedIn() {
            return Boolean(localStorage.getItem("accessToken") && localStorage.getItem("userInfo"))
        },
        // Signed in: straight to the system; otherwise the sign-in page
        ctaPath() {
            return this.signedIn ? "/" : "/pages/login"
        },
        ctaText() {
            return this.signedIn ? this.$t("landing_open_system") : this.$t("login")
        },
    },
    methods: {
        load() {
            const locale = this.$i18n.locale
            try {
                const saved = JSON.parse(localStorage.getItem(cacheKey(locale)))
                if (saved) this.show(saved)
            } catch (e) { /* storage unavailable */ }
            return axios.get(apiConfig._apiLanding)
                .then(({ data }) => {
                    if (locale !== this.$i18n.locale) return // the language changed meanwhile
                    this.show(data.data || EMPTY)
                    try { localStorage.setItem(cacheKey(locale), JSON.stringify(data.data || EMPTY)) } catch (e) { /* storage unavailable */ }
                })
                .catch(() => {})
                .finally(() => { this.loading = false })
        },
        show(page) {
            this.page = { ...EMPTY, ...page }
            // Already counted once: new numbers (other language, refresh) show as they are
            this.shown = this.page.stats.map(stat => (this.counted ? stat.value : 0))
            this.$nextTick(this.watchReveal)
        },
        number(value) {
            return localDigits(Math.round(value).toLocaleString("en-US"))
        },
        go(id) {
            const el = document.getElementById(id)
            if (el) el.scrollIntoView({ behavior: "smooth", block: "start" })
        },
        onScroll() {
            this.scrolled = window.scrollY > 12
        },
        // Count every number up from 0 in about a second, easing out
        countUp() {
            if (this.counted) return
            this.counted = true
            // Read each frame: fresher numbers from the server may arrive while counting
            const targets = () => this.page.stats.map(stat => Number(stat.value) || 0)
            if (window.matchMedia("(prefers-reduced-motion: reduce)").matches) {
                this.shown = targets()
                return
            }
            const start = performance.now()
            const step = now => {
                const t = Math.min(1, (now - start) / 1200)
                const eased = 1 - Math.pow(1 - t, 3)
                this.shown = targets().map(target => target * eased)
                if (t < 1) this.frame = requestAnimationFrame(step)
            }
            this.frame = requestAnimationFrame(step)
        },
        // Blocks rise in as they come into view; the numbers start counting
        watchReveal() {
            if (!this.observer) return
            this.$el.querySelectorAll("[data-reveal]:not(.is-visible)").forEach(el => this.observer.observe(el))
            if (this.$refs.numbers) this.observer.observe(this.$refs.numbers)
        },
    },
    created() {
        loadSite()
        this.load()
        this.stopLocale = onLocaleChange(() => this.load())
    },
    mounted() {
        this.onScroll()
        window.addEventListener("scroll", this.onScroll, { passive: true })
        if ("IntersectionObserver" in window) {
            this.observer = new IntersectionObserver(entries => entries.forEach(entry => {
                if (!entry.isIntersecting) return
                if (entry.target === this.$refs.numbers) this.countUp()
                else entry.target.classList.add("is-visible")
                this.observer.unobserve(entry.target)
            }), { threshold: 0.15 })
            this.watchReveal()
        } else {
            this.$el.classList.add("no-reveal")
            this.countUp()
        }
    },
    beforeUnmount() {
        window.removeEventListener("scroll", this.onScroll)
        if (this.observer) this.observer.disconnect()
        if (this.stopLocale) this.stopLocale()
        cancelAnimationFrame(this.frame)
    },
}
</script>

<style>
/* Same blues as the sign-in page and the sidebar */
.landing {
    --landing-deep: 50, 70, 147;
    --landing-soft: 89, 114, 172;
    --landing-ink: #1f2440;
    --landing-muted: #6b7088;
    --landing-surface: #fff;
    --landing-page: #f5f6fa;
    --landing-line: #e8eaf1;

    min-height: 100vh;
    color: var(--landing-ink);
    background: var(--landing-page);
    overflow-x: hidden;
    scroll-behavior: smooth;
}

/* ---------- Buttons ---------- */
.landing-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    padding: .8rem 1.4rem;
    font-weight: 600;
    font-size: .95rem;
    border-radius: 12px;
    text-decoration: none;
    white-space: nowrap;
    transition: transform .2s ease, box-shadow .25s ease, background .2s ease, color .2s ease;

    &:hover {
        transform: translateY(-2px);
    }

    &:active {
        transform: translateY(0) scale(.97);
    }

    .feather-icon {
        transition: transform .2s ease;
    }

    &:hover .feather-icon {
        transform: translateX(3px);
    }

    &--sm {
        padding: .5rem 1rem;
        font-size: .875rem;
        border-radius: 10px;
    }

    &--primary {
        color: #fff !important;
        background: linear-gradient(135deg, rgb(var(--landing-soft)), rgb(var(--landing-deep)));
        box-shadow: 0 6px 16px rgba(var(--landing-deep), .3);
    }

    &--light {
        color: rgb(var(--landing-deep)) !important;
        background: #fff;
        box-shadow: 0 10px 24px rgba(10, 16, 60, .25);

        &:hover {
            box-shadow: 0 14px 30px rgba(10, 16, 60, .32);
        }
    }

    &--ghost {
        color: #fff !important;
        border: 1px solid rgba(255, 255, 255, .35);
        background: rgba(255, 255, 255, .08);
        backdrop-filter: blur(6px);

        &:hover {
            background: rgba(255, 255, 255, .16);
        }
    }
}

/* ---------- Top bar ---------- */
.landing-nav {
    position: fixed;
    inset: 0 0 auto;
    z-index: 50;
    display: flex;
    align-items: center;
    gap: 1.5rem;
    height: 68px;
    padding: 0 max(16px, calc((100vw - 1180px) / 2));
    color: #fff;
    transition: background .3s ease, box-shadow .3s ease, color .3s ease, height .3s ease;

    &.is-scrolled {
        height: 60px;
        color: var(--landing-ink);
        background: rgba(255, 255, 255, .82);
        backdrop-filter: saturate(180%) blur(14px);
        box-shadow: 0 1px 0 var(--landing-line), 0 8px 24px rgba(20, 24, 60, .06);
    }

    &__brand {
        display: flex;
        align-items: center;
        gap: 10px;
        min-width: 0;
        font-weight: 700;
        font-size: 1.05rem;
        color: inherit !important;
        text-decoration: none;

        span {
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
    }

    &__logo {
        flex-shrink: 0;
        width: 36px;
        padding: 4px;
        border-radius: 10px;
        background: #fff;
        box-shadow: 0 2px 8px rgba(0, 0, 0, .12);
    }

    &__links {
        display: flex;
        gap: 1.5rem;
        margin: 0 auto;

        a {
            position: relative;
            font-size: .925rem;
            font-weight: 500;
            color: inherit !important;
            opacity: .85;
            text-decoration: none;
            transition: opacity .2s ease;

            &::after {
                content: "";
                position: absolute;
                left: 0;
                right: 0;
                bottom: -6px;
                height: 2px;
                border-radius: 2px;
                background: currentColor;
                transform: scaleX(0);
                transition: transform .25s ease;
            }

            &:hover {
                opacity: 1;

                &::after {
                    transform: scaleX(1);
                }
            }
        }
    }

    &__end {
        display: flex;
        align-items: center;
        gap: .75rem;
        margin-left: auto;

        .nav-lang-switch {
            margin-right: 0;
        }
    }

    &__links + &__end {
        margin-left: 0;
    }
}

/* ---------- Hero ---------- */
.landing-hero {
    position: relative;
    display: flex;
    align-items: center;
    min-height: 100vh;
    padding: 110px max(16px, calc((100vw - 1180px) / 2)) 90px;
    color: #fff;
    overflow: hidden;
    background: linear-gradient(150deg, rgb(var(--landing-soft)) 0%, rgb(var(--landing-deep)) 55%, #1f2c6b 100%);

    &__blob {
        position: absolute;
        border-radius: 50%;
        filter: blur(60px);
        opacity: .55;
        pointer-events: none;
        animation: landing-float 14s ease-in-out infinite alternate;

        &--a {
            width: 480px;
            height: 480px;
            top: -140px;
            right: -100px;
            background: radial-gradient(circle, rgba(129, 160, 255, .9), transparent 70%);
        }

        &--b {
            width: 420px;
            height: 420px;
            bottom: -160px;
            left: -120px;
            background: radial-gradient(circle, rgba(95, 220, 200, .55), transparent 70%);
            animation-duration: 18s;
            animation-direction: alternate-reverse;
        }
    }

    &__grid {
        position: absolute;
        inset: 0;
        pointer-events: none;
        background-image:
            linear-gradient(rgba(255, 255, 255, .06) 1px, transparent 1px),
            linear-gradient(90deg, rgba(255, 255, 255, .06) 1px, transparent 1px);
        background-size: 44px 44px;
        mask-image: radial-gradient(ellipse at 50% 40%, #000 20%, transparent 75%);
    }

    &__inner {
        position: relative;
        display: grid;
        grid-template-columns: minmax(0, 1.05fr) minmax(0, 1fr);
        align-items: center;
        gap: 3rem;
        width: 100%;
    }

    &__text {
        min-width: 0;
        overflow-wrap: anywhere;

        h1 {
            margin: 1rem 0 1.1rem;
            font-size: clamp(2rem, 4.2vw, 3.3rem);
            font-weight: 800;
            line-height: 1.25;
            letter-spacing: -.01em;
            color: #fff;
            animation: landing-rise .7s cubic-bezier(.22, 1, .36, 1) both;
        }

        p {
            max-width: 34rem;
            margin: 0 0 2rem;
            font-size: 1.1rem;
            line-height: 1.8;
            color: rgba(255, 255, 255, .82);
            animation: landing-rise .7s .1s cubic-bezier(.22, 1, .36, 1) both;
        }
    }

    &__actions {
        display: flex;
        flex-wrap: wrap;
        gap: .8rem;
        animation: landing-rise .7s .2s cubic-bezier(.22, 1, .36, 1) both;
    }

    &__visual {
        position: relative;
        animation: landing-rise .9s .15s cubic-bezier(.22, 1, .36, 1) both;
    }

    &__image {
        width: 100%;
        border-radius: 20px;
        box-shadow: 0 30px 60px rgba(8, 12, 50, .45);
        transform: perspective(1200px) rotateY(-8deg) rotateX(3deg);
        transition: transform .5s ease;

        &:hover {
            transform: perspective(1200px) rotateY(0) rotateX(0);
        }
    }

    &__scroll {
        position: absolute;
        left: 50%;
        bottom: 24px;
        display: grid;
        place-items: center;
        width: 40px;
        height: 40px;
        color: #fff;
        border: 1px solid rgba(255, 255, 255, .3);
        border-radius: 50%;
        background: rgba(255, 255, 255, .08);
        cursor: pointer;
        transform: translateX(-50%);
        animation: landing-bounce 2s ease-in-out infinite;
    }
}

.landing-eyebrow {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: .35rem .85rem;
    font-size: .8rem;
    font-weight: 500;
    border: 1px solid rgba(255, 255, 255, .25);
    border-radius: 999px;
    background: rgba(255, 255, 255, .1);
    backdrop-filter: blur(6px);
    animation: landing-rise .6s cubic-bezier(.22, 1, .36, 1) both;

    &__dot {
        width: 7px;
        height: 7px;
        border-radius: 50%;
        background: #5fe0b4;
        box-shadow: 0 0 0 0 rgba(95, 224, 180, .7);
        animation: landing-pulse 1.8s ease-out infinite;
    }
}

.landing-skeleton {
    display: block;
    border-radius: 10px;
    background: linear-gradient(90deg, rgba(255, 255, 255, .12), rgba(255, 255, 255, .26), rgba(255, 255, 255, .12)) 0 0 / 200% 100%;
    animation: landing-shimmer 1.4s linear infinite;

    &--title {
        height: 3rem;
        width: 85%;
        margin: 1rem 0 1.4rem;
    }

    &--line {
        height: 1rem;
        width: 75%;
        margin-bottom: .7rem;

        &.is-short {
            width: 50%;
            margin-bottom: 2rem;
        }
    }
}

/* Animated preview of the system, when no picture is set */
.landing-mock {
    position: relative;
    border-radius: 18px;
    background: rgba(255, 255, 255, .96);
    box-shadow: 0 40px 80px rgba(8, 12, 50, .45);
    transform: perspective(1200px) rotateY(-10deg) rotateX(4deg);
    transition: transform .6s cubic-bezier(.22, 1, .36, 1);

    &:hover {
        transform: perspective(1200px) rotateY(-3deg) rotateX(1deg);
    }

    &__bar {
        display: flex;
        gap: 6px;
        padding: 12px 14px;
        border-bottom: 1px solid #eef0f5;

        i {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            background: #ff6b6b;

            &:nth-child(2) {
                background: #ffc94d;
            }

            &:nth-child(3) {
                background: #4cd17a;
            }
        }
    }

    &__body {
        display: flex;
        gap: 14px;
        padding: 16px;
    }

    &__side {
        display: flex;
        flex-direction: column;
        gap: 10px;
        width: 22%;

        span {
            height: 12px;
            border-radius: 6px;
            background: #eef0f6;

            &.is-on {
                background: rgba(var(--landing-soft), .35);
            }
        }
    }

    &__main {
        flex: 1;
        display: flex;
        flex-direction: column;
        gap: 14px;
    }

    &__tiles {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 10px;

        span {
            height: 46px;
            border-radius: 10px;
            background: linear-gradient(135deg, #f1f3fa, #e7eaf5);

            &:first-child {
                background: linear-gradient(135deg, rgb(var(--landing-soft)), rgb(var(--landing-deep)));
            }
        }
    }

    &__chart {
        display: flex;
        align-items: flex-end;
        gap: 8px;
        height: 130px;
        padding: 12px;
        border-radius: 12px;
        background: #f7f8fc;

        span {
            flex: 1;
            height: var(--h);
            border-radius: 6px 6px 3px 3px;
            background: linear-gradient(180deg, rgb(var(--landing-soft)), rgba(var(--landing-deep), .65));
            transform-origin: bottom;
            animation: landing-grow 1s calc(.4s + var(--i) * .08s) cubic-bezier(.34, 1.56, .64, 1) both,
                landing-breathe 4s calc(1.5s + var(--i) * .3s) ease-in-out infinite alternate;
        }
    }

    /* Floating cards with real numbers */
    &__chip {
        position: absolute;
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 10px 14px;
        color: var(--landing-ink);
        border-radius: 14px;
        background: #fff;
        box-shadow: 0 16px 36px rgba(8, 12, 50, .25);
        animation: landing-bob 5s ease-in-out infinite;

        b {
            display: block;
            font-size: 1.1rem;
            line-height: 1.2;
        }

        small {
            font-size: .75rem;
            color: var(--landing-muted);
        }

        &--0 {
            left: -28px;
            bottom: 28px;
        }

        &--1 {
            right: -22px;
            top: 56px;
            animation-delay: -2.5s;
        }
    }

    &__chip-icon {
        display: grid;
        place-items: center;
        width: 34px;
        height: 34px;
        color: #fff;
        border-radius: 10px;
        background: linear-gradient(135deg, #5fd0b0, #2fa38a);
    }

    &__chip--1 &__chip-icon {
        background: linear-gradient(135deg, #ffb35c, #f08a24);
    }
}

/* ---------- Sections ---------- */
.landing-section {
    padding: 90px max(16px, calc((100vw - 1180px) / 2));

    &__head {
        max-width: 640px;
        margin: 0 auto 3rem;
        text-align: center;

        h2 {
            margin: 0 0 .75rem;
            font-size: clamp(1.6rem, 3vw, 2.3rem);
            font-weight: 800;
            line-height: 1.3;
            color: var(--landing-ink);
        }

        p {
            margin: 0;
            font-size: 1.05rem;
            color: var(--landing-muted);
        }
    }
}

/* Numbers: overlap the end of the hero */
.landing-numbers {
    position: relative;
    z-index: 2;
    margin-top: -70px;
    padding-top: 0;
    padding-bottom: 20px;

    &__grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1rem;
        padding: 1.5rem;
        border: 1px solid var(--landing-line);
        border-radius: 22px;
        background: var(--landing-surface);
        box-shadow: 0 24px 50px rgba(20, 24, 60, .1);
    }

    &__hint {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        margin: 1rem 0 0;
        font-size: .8rem;
        color: var(--landing-muted);
    }
}

.landing-stat {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: .35rem;
    padding: 1rem .5rem;
    text-align: center;
    border-radius: 16px;
    transition: background .25s ease;

    &:hover {
        background: #f6f7fb;
    }

    &__icon {
        display: grid;
        place-items: center;
        width: 44px;
        height: 44px;
        margin-bottom: .35rem;
        color: rgb(var(--landing-deep));
        border-radius: 14px;
        background: rgba(var(--landing-soft), .12);
    }

    b {
        font-size: 2.2rem;
        font-weight: 800;
        line-height: 1.1;
        font-variant-numeric: tabular-nums;
        background: linear-gradient(135deg, rgb(var(--landing-soft)), rgb(var(--landing-deep)));
        -webkit-background-clip: text;
        background-clip: text;
        color: transparent;
    }

    > span:last-child {
        font-size: .9rem;
        color: var(--landing-muted);
    }
}

/* Feature cards */
.landing-features {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(290px, 1fr));
    gap: 1.25rem;
}

.landing-feature {
    position: relative;
    padding: 1.75rem;
    overflow: hidden;
    border: 1px solid var(--landing-line);
    border-radius: 20px;
    background: var(--landing-surface);
    transition: transform .3s ease, box-shadow .3s ease, border-color .3s ease;

    /* Soft glow that follows the hover */
    &::before {
        content: "";
        position: absolute;
        inset: 0;
        opacity: 0;
        background: radial-gradient(circle at 0% 0%, rgba(var(--landing-soft), .12), transparent 60%);
        transition: opacity .3s ease;
    }

    &:hover {
        transform: translateY(-6px);
        border-color: rgba(var(--landing-soft), .35);
        box-shadow: 0 22px 44px rgba(20, 24, 60, .1);

        &::before {
            opacity: 1;
        }

        .landing-feature__icon {
            transform: rotate(-8deg) scale(1.08);
        }
    }

    &__icon {
        position: relative;
        display: grid;
        place-items: center;
        width: 54px;
        height: 54px;
        margin-bottom: 1.2rem;
        color: #fff;
        border-radius: 16px;
        background: linear-gradient(135deg, rgb(var(--landing-soft)), rgb(var(--landing-deep)));
        box-shadow: 0 10px 20px rgba(var(--landing-deep), .28);
        transition: transform .4s cubic-bezier(.34, 1.56, .64, 1);
    }

    h3 {
        position: relative;
        margin: 0 0 .5rem;
        font-size: 1.15rem;
        font-weight: 700;
        color: var(--landing-ink);
    }

    p {
        position: relative;
        margin: 0;
        line-height: 1.75;
        color: var(--landing-muted);
    }
}

/* Call to action band */
.landing-cta {
    position: relative;
    padding: 3.5rem 2rem;
    overflow: hidden;
    color: #fff;
    text-align: center;
    border-radius: 28px;
    background: linear-gradient(135deg, rgb(var(--landing-soft)), rgb(var(--landing-deep)) 60%, #1f2c6b);
    box-shadow: 0 30px 60px rgba(var(--landing-deep), .3);

    h2 {
        position: relative;
        margin: 0 0 .6rem;
        font-size: clamp(1.5rem, 3vw, 2.1rem);
        font-weight: 800;
        color: #fff;
    }

    p {
        position: relative;
        margin: 0 0 1.75rem;
        color: rgba(255, 255, 255, .8);
    }

    .landing-btn {
        position: relative;
    }
}

/* Footer */
.landing-footer {
    padding: 60px max(16px, calc((100vw - 1180px) / 2)) 28px;
    border-top: 1px solid var(--landing-line);
    background: var(--landing-surface);

    &__inner {
        display: flex;
        flex-wrap: wrap;
        justify-content: space-between;
        gap: 2rem;
        padding-bottom: 2rem;
    }

    &__brand {
        display: flex;
        align-items: center;
        gap: 14px;
        max-width: 420px;

        .landing-nav__logo {
            width: 48px;
        }

        b {
            display: block;
            font-size: 1.1rem;
        }

        span {
            font-size: .875rem;
            color: var(--landing-muted);
        }
    }

    &__contact {
        display: flex;
        flex-direction: column;
        gap: .6rem;

        h4 {
            margin: 0 0 .25rem;
            font-size: .8rem;
            font-weight: 700;
            letter-spacing: .08em;
            text-transform: uppercase;
            color: var(--landing-muted);
        }

        a {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            color: var(--landing-ink) !important;
            text-decoration: none;
            transition: color .2s ease, transform .2s ease;

            &:hover {
                color: rgb(var(--landing-deep)) !important;
                transform: translateX(3px);
            }
        }
    }

    &__bottom {
        display: flex;
        flex-wrap: wrap;
        justify-content: space-between;
        gap: 1rem;
        padding-top: 1.25rem;
        font-size: .85rem;
        color: var(--landing-muted);
        border-top: 1px solid var(--landing-line);

        a {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            color: var(--landing-muted) !important;
            text-decoration: none;

            &:hover {
                color: rgb(var(--landing-deep)) !important;
            }
        }
    }
}

/* Blocks rise in when scrolled into view */
.landing [data-reveal] {
    opacity: 0;
    transform: translateY(24px);
    transition: opacity .7s cubic-bezier(.22, 1, .36, 1), transform .7s cubic-bezier(.22, 1, .36, 1);
    transition-delay: var(--delay, 0ms);

    &.is-visible {
        opacity: 1;
        transform: none;
    }
}

.landing.no-reveal [data-reveal] {
    opacity: 1;
    transform: none;
}

/* Language switch over the blue hero */
.landing-nav:not(.is-scrolled) .nav-lang-switch {
    background: rgba(255, 255, 255, .14);

    button:not(.active) {
        color: rgba(255, 255, 255, .85);
    }
}

@keyframes landing-rise {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
}

@keyframes landing-float {
    to {
        transform: translate(40px, 30px) scale(1.1);
    }
}

@keyframes landing-bob {
    50% {
        transform: translateY(-10px);
    }
}

@keyframes landing-bounce {
    50% {
        transform: translate(-50%, 6px);
    }
}

@keyframes landing-grow {
    from {
        transform: scaleY(0);
    }
}

@keyframes landing-breathe {
    to {
        transform: scaleY(.82);
    }
}

@keyframes landing-pulse {
    to {
        box-shadow: 0 0 0 8px rgba(95, 224, 180, 0);
    }
}

@keyframes landing-shimmer {
    to {
        background-position: -200% 0;
    }
}

/* ---------- Small screens ---------- */
@media (max-width: 960px) {
    .landing-hero__inner {
        grid-template-columns: minmax(0, 1fr);
        text-align: center;
    }

    .landing-hero__text p {
        margin-left: auto;
        margin-right: auto;
    }

    .landing-hero__actions {
        justify-content: center;
    }

    .landing-hero__visual {
        width: 100%;
        max-width: 520px;
        margin: 0 auto;
    }

    .landing-skeleton {
        margin-left: auto;
        margin-right: auto;
    }
}

@media (max-width: 768px) {
    .landing-nav {
        gap: .75rem;

        &__links {
            display: none;
        }
    }

    .landing-mock__chip {
        display: none;
    }

    .landing-section {
        padding-top: 64px;
        padding-bottom: 64px;
    }

    .landing-numbers {
        padding-top: 0;
    }
}

@media (max-width: 480px) {
    .landing-nav__brand span {
        display: none;
    }

    .landing-hero__actions .landing-btn {
        width: 100%;
    }
}

@media (prefers-reduced-motion: reduce) {
    .landing *,
    .landing *::before,
    .landing *::after {
        animation: none !important;
        transition: none !important;
    }

    .landing [data-reveal] {
        opacity: 1;
        transform: none;
    }
}
</style>

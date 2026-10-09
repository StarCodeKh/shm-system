<template>
    <div class="setting-page">

        <!-- Settings navigation -->
        <aside class="setting-nav">
            <div class="setting-nav__header">
                <span class="setting-tile setting-tile--brand">
                    <feather-icon icon="SlidersIcon" svgClasses="h-5 w-5" />
                </span>
                <div class="min-w-0">
                    <h3>{{ $t('setting_title') }}</h3>
                    <p>{{ $t('setting_subtitle') }}</p>
                </div>
            </div>

            <nav ref="navList" class="setting-nav__list" role="tablist" aria-orientation="vertical" @keydown="onNavKey">
                <!-- One highlight shared by all items: it slides to the active one and takes its color -->
                <span class="setting-nav__indicator" :class="{ 'is-ready': indicator.ready }" :style="indicatorStyle" aria-hidden="true" />
                <button v-for="(tab, i) in visibleTabs" :key="tab.key" :ref="el => setItemRef(tab.key, el)" type="button"
                    class="setting-nav__item" role="tab" :aria-selected="tab.key === activeKey" :tabindex="tab.key === activeKey ? 0 : -1"
                    :class="{ 'is-active': tab.key === activeKey }" :style="{ '--tile': tab.color, '--delay': (i * 40) + 'ms' }"
                    @click="select(tab.key)" @mouseenter="preload(tab)" @focus="preload(tab)">
                    <span class="setting-tile">
                        <feather-icon :icon="tab.icon" svgClasses="h-4 w-4" />
                    </span>
                    <span class="setting-nav__text">
                        <span class="setting-nav__title">{{ $t(tab.title) }}</span>
                        <span class="setting-nav__desc">{{ $t(tab.desc) }}</span>
                    </span>
                    <feather-icon icon="ChevronRightIcon" svgClasses="h-4 w-4" class="setting-nav__chevron" />
                </button>
            </nav>
        </aside>

        <!-- Active section; accents follow the section's color -->
        <section class="setting-content" :style="{ '--tile': activeTab ? activeTab.color : null }">
            <div v-if="activeTab" class="setting-content__inner">
                <transition name="setting-swap" mode="out-in">
                    <div :key="activeTab.key" class="setting-content__head">
                        <span class="setting-tile" :style="{ '--tile': activeTab.color }">
                            <feather-icon :icon="activeTab.icon" svgClasses="h-5 w-5" />
                        </span>
                        <div>
                            <h2>{{ $t(activeTab.title) }}</h2>
                            <p>{{ $t(activeTab.desc) }}</p>
                        </div>
                    </div>
                </transition>

                <!-- Sections stay alive once opened: coming back is instant, then their lists refresh quietly -->
                <transition name="setting-swap" mode="out-in" @after-enter="onShown">
                    <keep-alive>
                        <vx-card v-if="activeTab.card" :key="activeTab.key" ref="section">
                            <component :is="activeTab.component" />
                        </vx-card>
                        <component v-else :is="activeTab.component" :key="activeTab.key" ref="section" />
                    </keep-alive>
                </transition>
            </div>
        </section>
    </div>
</template>

<script>
import { defineAsyncComponent } from "vue";
import { canSection } from "./settingsAccess.js";
import { refreshLists } from "@/ui/utils";

// Each section's code loads the first time it is needed (or when the pointer reaches its menu item)
const SECTIONS = {
    "setting-general": () => import("./SettingGeneral.vue"),
    "setting-role": () => import("./SettingRole.vue"),
    "setting-account-admin": () => import("./SettingAccountAdmin.vue"),
    "setting-entity": () => import("./SettingEntity.vue"),
    "setting-backup": () => import("./SettingBackup.vue"),
    "setting-notifications": () => import("./SettingNotifications.vue"),
    "setting-audit": () => import("./SettingAudit.vue"),
};

// Vue 3 needs async components wrapped; the plain loaders above are kept for preload()
const components = Object.fromEntries(
    Object.entries(SECTIONS).map(([name, load]) => [name, defineAsyncComponent(load)])
);

export default {
    components,
    data() {
        return {
            // Position and size of the sliding highlight (px, relative to the list)
            indicator: { x: 0, y: 0, w: 0, h: 0, ready: false },
        }
    },
    watch: {
        activeKey() {
            this.$nextTick(() => this.moveIndicator(true))
        },
        // Fewer or more tabs (permissions loaded later) shift the items
        visibleTabs() {
            this.$nextTick(this.moveIndicator)
        },
        // Titles change width with the language (matters in the horizontal mobile strip)
        '$i18n.locale'() {
            this.$nextTick(this.moveIndicator)
        },
    },
    methods: {
        select(key) {
            if (key === this.activeKey) return
            this.$router.replace({ query: { ...this.$route.query, tab: key } }).catch(() => {})
        },
        setItemRef(key, el) {
            if (el) this.itemEls[key] = el
            else delete this.itemEls[key]
        },
        // Put the highlight over the active item. The first placement is instant (no slide in from the corner).
        moveIndicator(reveal = false) {
            const el = this.itemEls[this.activeKey]
            if (!el) return
            this.indicator = { ...this.indicator, x: el.offsetLeft, y: el.offsetTop, w: el.offsetWidth, h: el.offsetHeight }
            if (!this.indicator.ready) requestAnimationFrame(() => { this.indicator.ready = true })
            // After a selection, keep the active item in view when the strip / list scrolls (mobile, many tabs)
            if (reveal === true) el.scrollIntoView({ block: 'nearest', inline: 'nearest', behavior: 'smooth' })
        },
        // Arrow keys / Home / End move between tabs, as in any tab list
        onNavKey(event) {
            const keys = this.visibleTabs.map(tab => tab.key)
            const index = keys.indexOf(this.activeKey)
            const step = { ArrowDown: 1, ArrowRight: 1, ArrowUp: -1, ArrowLeft: -1 }[event.key]
            let next = null
            if (step) next = keys[(index + step + keys.length) % keys.length]
            else if (event.key === 'Home') next = keys[0]
            else if (event.key === 'End') next = keys[keys.length - 1]
            if (!next) return
            event.preventDefault()
            this.select(next)
            this.$nextTick(() => this.itemEls[next] && this.itemEls[next].focus())
        },
        preload(tab) {
            SECTIONS[tab.component]().catch(() => {})
        },
        // A section shown again comes from the cache; reload its lists so the data is current
        onShown() {
            if (this.opened[this.activeKey]) refreshLists(this.$refs.section)
            this.opened[this.activeKey] = true
        },
    },
    computed: {
        // Sections in display order; who sees each one is decided in settingsAccess.js
        tabs() {
            return [
                { key: 'general', icon: 'SettingsIcon', color: '31, 32, 103', title: 'setting_tab_general', desc: 'setting_tab_general_desc', component: 'setting-general' },
                { key: 'roles', icon: 'UsersIcon', color: '62, 91, 169', title: 'setting_tab_roles', desc: 'setting_tab_roles_desc', component: 'setting-role' },
                { key: 'account', icon: 'BriefcaseIcon', color: '47, 164, 73', title: 'setting_tab_account', desc: 'setting_tab_account_desc', component: 'setting-account-admin' },
                { key: 'entity', icon: 'GridIcon', color: '31, 138, 122', title: 'setting_tab_entity', desc: 'setting_tab_entity_desc', component: 'setting-entity', card: true },
                { key: 'backup', icon: 'DatabaseIcon', color: '138, 106, 18', title: 'setting_tab_backup', desc: 'setting_tab_backup_desc', component: 'setting-backup' },
                { key: 'notification', icon: 'BellIcon', color: '192, 57, 43', title: 'setting_tab_notification', desc: 'setting_tab_notification_desc', component: 'setting-notifications' },
                { key: 'audit', icon: 'ClockIcon', color: '92, 99, 120', title: 'setting_tab_audit', desc: 'setting_tab_audit_desc', component: 'setting-audit' },
            ]
        },
        // Sections the user's role may open (at least one of the section's permissions, see settingsAccess.js)
        visibleTabs() {
            return this.tabs.filter(tab => canSection(tab.key))
        },
        // Active section comes from ?tab=… so it survives reloads and can be linked to
        activeKey() {
            const wanted = this.$route.query.tab
            return this.visibleTabs.some(tab => tab.key === wanted) ? wanted : (this.visibleTabs[0] || {}).key
        },
        activeTab() {
            return this.visibleTabs.find(tab => tab.key === this.activeKey)
        },
        indicatorStyle() {
            const { x, y, w, h } = this.indicator
            return {
                '--tile': this.activeTab ? this.activeTab.color : null,
                width: w + 'px',
                height: h + 'px',
                transform: `translate3d(${x}px, ${y}px, 0)`,
                opacity: w ? 1 : 0,
            }
        },
    },
    created() {
        this.opened = {} // sections already shown once (not reactive)
        this.itemEls = {} // nav buttons by tab key (not reactive)
    },
    mounted() {
        if (this.activeKey) this.opened[this.activeKey] = true
        this.$nextTick(this.moveIndicator)
        // Item sizes change with the window (vertical list ⇄ horizontal strip) and with fonts loading
        this.resizeObserver = new ResizeObserver(() => this.moveIndicator())
        this.resizeObserver.observe(this.$refs.navList)
        if (document.fonts && document.fonts.ready) document.fonts.ready.then(() => this.moveIndicator())
    },
    beforeUnmount() {
        if (this.resizeObserver) this.resizeObserver.disconnect()
    },
}
</script>

<style>
.setting-page {
    display: flex;
    min-height: calc(100vh - 220px);
    border-radius: 16px;
    overflow: clip; /* rounds the corners without breaking the sticky nav */
    background: #fff;
    border: 1px solid rgba(23, 23, 74, .05);
    box-shadow: 0 4px 24px -8px rgba(23, 23, 74, .12);
}

/* Colored icon square; --tile is an "r, g, b" triple inherited from the nav item / section */
.setting-tile {
    flex-shrink: 0;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 36px;
    height: 36px;
    border-radius: 10px;
    color: rgb(var(--tile, 99, 102, 241));
    background: rgba(var(--tile, 99, 102, 241), .12);
    transition: transform .2s ease, background .2s ease, color .2s ease;

    .feather-icon {
        display: flex;
    }

    &--brand {
        width: 42px;
        height: 42px;
        color: #fff;
        background: linear-gradient(135deg, rgb(var(--brand-navy)), #3E5BA9);
        box-shadow: 0 8px 18px -8px rgba(var(--brand-navy), .8);
    }
}

/* ---------------------------------------------------------------- Left navigation */
.setting-nav {
    flex: 0 0 290px;
    border-right: 1px solid #eef0f5;
    background: #fbfbfd;

    &__header {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 20px 18px;
        border-bottom: 1px solid #eef0f5;

        h3 {
            font-size: 1.05rem;
            font-weight: 700;
            color: #2c2e45;
        }

        p {
            font-size: .78rem;
            color: #8a8ca5;
        }
    }

    &__list {
        position: sticky;
        top: 90px; /* below the sticky navbar */
        display: flex;
        flex-direction: column;
        gap: 2px;
        max-height: calc(100vh - 110px);
        padding: 10px;
        overflow-y: auto;
        scrollbar-width: thin;
    }

    &__item {
        position: relative;
        display: flex;
        align-items: center;
        gap: 12px;
        width: 100%;
        padding: 10px 12px;
        font-family: inherit;
        text-align: left;
        border: none;
        border-radius: 12px;
        background: transparent;
        cursor: pointer;
        z-index: 1; /* above the sliding highlight */
        animation: setting-item-in .35s ease backwards;
        animation-delay: var(--delay);
        transition: background var(--tab-tap) ease, box-shadow var(--tab-tap) ease;

        &:hover:not(.is-active) {
            background: #fff;
            box-shadow: 0 4px 14px -8px rgba(23, 23, 74, .25);

            .setting-tile {
                transform: scale(1.06);
            }
        }

        &:focus-visible {
            outline: 2px solid rgba(var(--tile), .5);
            outline-offset: 2px;
        }

        /* Active: the highlight underneath gives the tint; the icon fills with the section color and pops */
        &.is-active {
            .setting-tile {
                color: #fff;
                background: rgb(var(--tile));
                box-shadow: 0 6px 14px -6px rgba(var(--tile), .8);
                animation: setting-tile-pop .45s cubic-bezier(.34, 1.56, .64, 1);
            }

            .setting-nav__title {
                color: #2c2e45;
                font-weight: 700;
            }

            .setting-nav__chevron {
                opacity: 1;
                transform: translateX(0);
                color: rgb(var(--tile));
            }
        }
    }

    /* Sliding highlight: soft tint of the section color, thin outline, accent bar on the left */
    &__indicator {
        position: absolute;
        top: 0;
        left: 0;
        border-radius: 12px;
        pointer-events: none;
        background: rgba(var(--tile), .09);
        box-shadow: inset 0 0 0 1px rgba(var(--tile), .2), 0 6px 16px -10px rgba(var(--tile), .6);

        &::before {
            content: "";
            position: absolute;
            top: 22%;
            bottom: 22%;
            left: 0;
            width: 3px;
            border-radius: 0 3px 3px 0;
            background: rgb(var(--tile));
        }

        /* Instant on first placement, then the same slide as every tab (--tab-move / --tab-ease), */
        /* fading its color between sections */
        &.is-ready {
            transition:
                transform var(--tab-move) var(--tab-ease),
                width var(--tab-move) var(--tab-ease),
                height var(--tab-move) var(--tab-ease),
                background-color var(--tab-move) ease,
                box-shadow var(--tab-move) ease,
                opacity var(--tab-tap) ease;
        }
    }

    &__text {
        display: flex;
        flex-direction: column;
        flex: 1;
        min-width: 0;
        line-height: 1.35;
    }

    &__title {
        font-size: .9rem;
        font-weight: 600;
        color: #3f4157;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    &__desc {
        font-size: .74rem;
        color: #9a9cb3;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    &__chevron {
        display: flex;
        color: #b4b6c8;
        opacity: 0;
        transform: translateX(-4px);
        transition: opacity .2s ease, transform .2s ease;
    }
}

/* ---------------------------------------------------------------- Content */
.setting-content {
    --tile: 31, 32, 103;
    position: relative;
    flex: 1;
    min-width: 0;
    padding: 24px 28px 28px;
    /* Plain surface: the section's color shows only in its header icon */
    background: #f7f8fb;

    &__head {
        display: flex;
        align-items: center;
        gap: 14px;
        margin-bottom: 20px;
        padding-bottom: 18px;
        border-bottom: 1px solid #e9ebf2;

        .setting-tile {
            width: 44px;
            height: 44px;
            border-radius: 12px;
            color: rgb(var(--tile));
            background: rgba(var(--tile), .12);
        }

        h2 {
            font-size: 1.25rem;
            font-weight: 700;
            color: #2c2e45;
        }

        p {
            margin-top: 2px;
            font-size: .85rem;
            color: #8a8ca5;
        }
    }

    /* Inner tabs and cards sit on white panels */
    .vx-card,
    .vs-tabs {
        border-radius: 14px;
    }

    .vx-card {
        border: 1px solid #eceef4;
        box-shadow: 0 2px 10px -4px rgba(23, 23, 74, .08);
    }

    > .setting-content__inner > .vs-tabs,
    .setting-content__inner > div > .vs-tabs {
        padding: 8px 16px 16px;
        background: #fff;
        border: 1px solid #eceef4;
        box-shadow: 0 2px 10px -4px rgba(23, 23, 74, .08);
    }
}

/* Same motion as every other tab: the old section fades out quickly, the new one rises in */
.setting-swap-enter-active {
    animation: tab-content-in var(--tab-fade) var(--tab-ease) backwards;
}
.setting-swap-leave-active {
    transition: opacity var(--tab-tap) ease;
}
.setting-swap-leave-to {
    opacity: 0;
}

/* Read-only sections (role may see but not save): a plain wrapper that disables every field, and a note */
.setting-fieldset {
    min-width: 0;
    margin: 0;
    padding: 0;
    border: 0;

    &:disabled {
        opacity: .75;
    }
}

.setting-readonly-note {
    margin-bottom: 16px;
    padding: 10px 14px;
    font-size: .85rem;
    color: #9a5b0a;
    border-radius: 10px;
    background: #fff6e6;
}

@keyframes setting-item-in {
    from { opacity: 0; transform: translateX(-8px); }
    to { opacity: 1; transform: none; }
}

@keyframes setting-tile-pop {
    0% { transform: scale(.82); }
    60% { transform: scale(1.08); }
    100% { transform: scale(1); }
}

/* ---------------------------------------------------------------- Small screens: nav becomes a scrollable strip */
@media (max-width: 900px) {
    .setting-page {
        flex-direction: column;
    }

    .setting-nav {
        flex: none;
        border-right: none;
        border-bottom: 1px solid #eef0f5;
        background: #fff;

        &__header {
            display: none;
        }

        &__list {
            position: relative; /* the highlight scrolls with the strip */
            top: auto; /* drop the desktop sticky offset, which would push the strip 90px down */
            flex-direction: row;
            gap: 8px;
            max-height: none;
            padding: 12px;
            overflow-x: auto;
            overflow-y: hidden;
            scroll-snap-type: x proximity;
            scrollbar-width: none;

            &::-webkit-scrollbar {
                display: none;
            }
        }

        &__item {
            flex: 0 0 auto;
            width: auto;
            gap: 8px;
            padding: 5px 14px 5px 5px;
            border: 1px solid #eceef3;
            border-radius: 999px;
            background: #fff;
            scroll-snap-align: start;

            .setting-tile {
                width: 30px;
                height: 30px;
                border-radius: 999px;
            }

            &:hover {
                box-shadow: none;
                border-color: rgba(var(--tile), .35);
            }

            &.is-active {
                border-color: rgba(var(--tile), .4);
                background: transparent;

                .setting-nav__title {
                    color: rgb(var(--tile));
                }
            }
        }

        &__indicator {
            border-radius: 999px;

            &::before {
                display: none;
            }
        }

        &__title {
            font-size: .85rem;
        }

        &__desc,
        &__chevron {
            display: none;
        }
    }

    .setting-content {
        padding: 16px;
    }
}

@media (prefers-reduced-motion: reduce) {
    .setting-nav__item,
    .setting-nav__item.is-active .setting-tile,
    .setting-swap-enter-active {
        animation: none;
    }

    .setting-nav__indicator.is-ready {
        transition: none;
    }
}

.theme-dark {
    .setting-page { background: #262c49; border-color: #414561; }
    .setting-nav { background: #10163a; border-color: #414561; }
    .setting-nav__header { border-color: #414561; }
    .setting-nav__header h3,
    .setting-nav__item.is-active .setting-nav__title { color: #fff; }
    .setting-nav__title { color: #c2c6dc; }
    .setting-nav__item:hover:not(.is-active) { background: #262c49; }
    @media (max-width: 900px) {
        .setting-nav { background: #10163a; }
        .setting-nav__item { background: #10163a; border-color: #414561; }
        .setting-nav__item.is-active { background: transparent; }
    }
    .setting-content { background: #1b2142; }
    .setting-content__head { border-color: #414561; }
    .setting-content__head h2 { color: #fff; }
    .setting-content__head p { color: #a8abc4; }
}
</style>

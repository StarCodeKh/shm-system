<template>
    <div class="parentx sidebar-menu">

        <vs-sidebar
            class="v-nav-menu items-no-padding"
            v-model="isVerticalNavMenuActive"
            ref="verticalNavMenu"
            default-index="-1"
            :click-not-close="clickNotClose"
            :reduce-not-rebound="reduceNotRebound"
            :parent="parent"
            :hiddenBackground="clickNotClose"
            :reduce="reduce"
            v-hammer:swipe.left="onSwipeLeft">

            <div class="nav-shell" @mouseenter="mouseEnter" @mouseleave="mouseLeave">

                <!-- Header -->
                <div class="header-sidebar nav-brand" :class="{ 'nav-brand--compact': reduce && !isMouseEnter }">

                    <!-- Logo -->
                    <router-link to="/" class="nav-brand__link" :title="title">
                        <span class="nav-brand__logo">
                            <logo :src="logo" :alt="title" />
                        </span>
                        <span class="nav-brand__text" v-show="isMouseEnter || !reduce">
                            <span class="nav-brand__title" v-if="title">{{ title }}</span>
                            <span class="nav-brand__subtitle">{{ $t('brand_subtitle') }}</span>
                        </span>
                    </router-link>
                    <!-- /Logo -->

                    <!-- Close (small screens) -->
                    <button v-if="showCloseButton" type="button" class="nav-brand__btn"
                        @click="$store.commit('TOGGLE_IS_VERTICAL_NAV_MENU_ACTIVE', false)">
                        <feather-icon icon="XIcon" svgClasses="h-4 w-4" />
                    </button>

                    <!-- Collapse / expand -->
                    <button v-else-if="!verticalNavMenuItemsMin" v-show="isMouseEnter || !reduce" id="btnVNavMenuMinToggler"
                        type="button" class="nav-brand__btn" :class="{ 'is-reduced': reduce }"
                        :title="$t(reduce ? 'brand_expand' : 'brand_collapse')" @click="toggleReduce(!reduce)">
                        <feather-icon icon="ChevronsLeftIcon" svgClasses="h-4 w-4" />
                    </button>
                </div>
                <!-- /Header -->

                <!-- Header Shadow -->
                <div class="shadow-bottom" v-show="showShadowBottom" />

                <!-- Menu Items -->
                <VuePerfectScrollbar ref="verticalNavMenuPs" class="scroll-area-v-nav-menu pt-2" :settings="settings" @ps-scroll-y="psSectionScroll" :key="$vs.rtl">
                    <template v-for="(item, index) in menuItemsUpdated" :key="`header-${index}`">

                        <!-- Group Header -->
                        <span v-if="item.header && !verticalNavMenuItemsMin" class="navigation-header truncate">
                            {{ $t(item.i18n) || item.header }}
                        </span>
                        <!-- /Group Header -->

                        <template v-else-if="!item.header">

                            <!-- Nav-Item -->
                            <v-nav-menu-item
                                v-if="!item.submenu"
                                :key="`item-${index}`"
                                :index="index"
                                :to="item.slug !== 'external' ? item.url : null"
                                :href="item.slug === 'external' ? item.url : null"
                                :icon="item.icon" :target="item.target"
                                :isDisabled="item.isDisabled"
                                :slug="item.slug">
                                    <span v-show="!verticalNavMenuItemsMin" class="truncate">{{ $t(item.i18n) || item.name }}</span>
                                    <vs-chip class="ml-auto" :color="item.tagColor" v-if="item.tag && (isMouseEnter || !reduce)">{{ item.tag }}</vs-chip>
                            </v-nav-menu-item>

                            <!-- Nav-Group -->
                            <template v-else>
                                <v-nav-menu-group
                                    :key="`group-${index}`"
                                    :openHover="openGroupHover"
                                    :group="item"
                                    :groupIndex="index"
                                    :open="isGroupActive(item)" />
                            </template>
                            <!-- /Nav-Group -->
                        </template>
                    </template>
                </VuePerfectScrollbar>
                <!-- /Menu Items -->
            </div>
        </vs-sidebar>

        <!-- Swipe Gesture -->
        <div
            v-if="!isVerticalNavMenuActive"
            class="v-nav-menu-swipe-area"
            v-hammer:swipe.right="onSwipeAreaSwipeRight" />
        <!-- /Swipe Gesture -->
    </div>
</template>

<script>
import VuePerfectScrollbar from '@/components/PerfectScrollbar.vue'
import { containsActive, setMenu } from './menuActive'
import VNavMenuGroup from './VerticalNavMenuGroup.vue'
import VNavMenuItem from './VerticalNavMenuItem.vue'

import Logo from "../Logo.vue"
import { fetchPermissions, storedMenu, fetchMenu } from './menuPermissions.js'
import { SIDEBAR_COLLAPSED_KEY } from "@/store/state.js"

export default {
    name: 'v-nav-menu',
    components: {
        VNavMenuGroup,
        VNavMenuItem,
        VuePerfectScrollbar,
        Logo
    },
    props: {
        logo:             { type: String },
        openGroupHover:   { type: Boolean, default: false },
        parent:           { type: String },
        reduceNotRebound: { type: Boolean, default: true },
        title:            { type: String },
    },
    data: () => ({
        clickNotClose       : false, // disable close navMenu on outside click
        isMouseEnter        : false,
        reduce              : false, // determines if navMenu is reduce - component property
        showCloseButton     : false, // show close button in smaller devices
        settings            : {      // perfectScrollbar settings
            maxScrollbarLength: 60,
            wheelSpeed        : 1,
            swipeEasing       : true
        },
        showShadowBottom    : false,
        menu                : storedMenu(), // the user's menu from Administration › Menus (cached, refreshed in created())
    }),
    computed: {
        isGroupActive() {
            return (item) => containsActive(item, this.$route.path)
        },
        // The user's menu (the server leaves out what they may not open), each section header followed by its items.
        menuItemsUpdated() {
            return this.menu
                .reduce((list, item) => item.header ? list.concat(item, item.items) : list.concat(item), [])
        },
        isVerticalNavMenuActive: {
            get()    { return this.$store.state.isVerticalNavMenuActive },
            set(val) { this.$store.commit('TOGGLE_IS_VERTICAL_NAV_MENU_ACTIVE', val) }
        },
        layoutType() { return this.$store.state.mainLayoutType },
        reduceButton: {
            get()    { return this.$store.state.reduceButton },
            set(val) { this.$store.commit('TOGGLE_REDUCE_BUTTON', val) }
        },
        isVerticalNavMenuReduced()   { return Boolean(this.reduce && this.reduceButton) },
        verticalNavMenuItemsMin() { return this.$store.state.verticalNavMenuItemsMin },
        windowWidth()     { return this.$store.state.windowWidth }
    },
    watch: {
        '$route'() {
            if (this.isVerticalNavMenuActive && this.showCloseButton) this.$store.commit('TOGGLE_IS_VERTICAL_NAV_MENU_ACTIVE', false)
        },
        reduce(val) {
            const verticalNavMenuWidth = val ? "reduced" : this.$store.state.windowWidth < 1200 ? "no-nav-menu" : "default"
            this.$store.dispatch('updateVerticalNavMenuWidth', verticalNavMenuWidth)

            setTimeout(function() {
                window.dispatchEvent(new Event('resize'))
            }, 100)
        },
        layoutType()   { this.setVerticalNavMenuWidth() },
        reduceButton() { this.setVerticalNavMenuWidth() },
        windowWidth()  { this.setVerticalNavMenuWidth() }
    },
    beforeUnmount() {
        window.removeEventListener('menu-changed', this.loadMenu)
        clearTimeout(this.hoverTimer)
    },
    methods: {
        loadMenu() {
            fetchMenu()
                .then(menu => { this.menu = menu; setMenu(menu) })
                .catch(() => {})
        },
        onSwipeLeft() {
            if (this.isVerticalNavMenuActive && this.showCloseButton) this.isVerticalNavMenuActive = false
        },
        onSwipeAreaSwipeRight() {
            if (!this.isVerticalNavMenuActive && this.showCloseButton) this.isVerticalNavMenuActive = true
        },
        psSectionScroll() {
            this.showShadowBottom = this.$refs.verticalNavMenuPs.$el.scrollTop > 0 ? true : false
        },
        // Hovering a collapsed menu opens it like the pin button does: the page moves aside instead of being covered.
        // A short delay ignores the pointer just passing over the menu.
        mouseEnter() {
            clearTimeout(this.hoverTimer)
            if (!this.reduce) {
                this.isMouseEnter = true
                return
            }
            this.hoverTimer = setTimeout(() => {
                this.$store.commit('UPDATE_VERTICAL_NAV_MENU_ITEMS_MIN', false)
                this.isMouseEnter = true
                this.$store.dispatch('updateVerticalNavMenuWidth', 'default')
            }, 120)
        },
        mouseLeave() {
            clearTimeout(this.hoverTimer)
            if (this.reduce && this.isMouseEnter) {
                this.$store.commit('UPDATE_VERTICAL_NAV_MENU_ITEMS_MIN', true)
                this.$store.dispatch('updateVerticalNavMenuWidth', 'reduced')
            }
            this.isMouseEnter = false
        },
        setVerticalNavMenuWidth() {

            if(this.windowWidth > 1200) {
                if(this.layoutType === 'vertical') {

                    // Set reduce
                    this.reduce = this.reduceButton ? true : false

                    // Open NavMenu
                    this.$store.commit('TOGGLE_IS_VERTICAL_NAV_MENU_ACTIVE', true)

                    // Set Menu Items Only Icon Mode
                    const verticalNavMenuItemsMin = (this.reduceButton && !this.isMouseEnter) ? true : false
                    this.$store.commit('UPDATE_VERTICAL_NAV_MENU_ITEMS_MIN', verticalNavMenuItemsMin)

                    // Menu Action buttons
                    this.clickNotClose   = true
                    this.showCloseButton = false

                    const verticalNavMenuWidth   = this.isVerticalNavMenuReduced ? "reduced" : "default"
                    this.$store.dispatch('updateVerticalNavMenuWidth', verticalNavMenuWidth)

                    return
                }
            }

            // Close NavMenu
            this.$store.commit('TOGGLE_IS_VERTICAL_NAV_MENU_ACTIVE', false)

            // Reduce button
            if (this.reduceButton) this.reduce = false

            // Menu Action buttons
            this.showCloseButton = true
            this.clickNotClose   = false

            // Update NavMenu Width
            this.$store.dispatch('updateVerticalNavMenuWidth', 'no-nav-menu')

            // Remove Only Icon in Menu
            this.$store.commit('UPDATE_VERTICAL_NAV_MENU_ITEMS_MIN', false)

            //   // Close NavMenu
            //   this.$store.commit('TOGGLE_IS_VERTICAL_NAV_MENU_ACTIVE', false)

            //   // Reduce button
            //   if (this.reduceButton) this.reduce = false

            //   // Update NavMenu Width
            //   this.$store.dispatch('updateVerticalNavMenuWidth', 'no-nav-menu')

            //   // Remove Only Icon in Menu
            //   this.$store.commit('UPDATE_VERTICAL_NAV_MENU_ITEMS_MIN', false)

            //   // Set reduce
            //   this.reduce = this.reduceButton ? true : false

            //   // Open NavMenu
            //   this.$store.commit('TOGGLE_IS_VERTICAL_NAV_MENU_ACTIVE', true)

        },
        toggleReduce(val) {
            this.reduceButton = val
            this.setVerticalNavMenuWidth()
            // Remember the user's choice across reloads (pages that collapse the menu temporarily don't save)
            try { localStorage.setItem(SIDEBAR_COLLAPSED_KEY, String(val)) } catch (e) { /* storage unavailable */ }
        },
    },
    created() {
        setMenu(this.menu)
        this.loadMenu()
        // Keep the permissions used by screens (buttons, tabs) in step with the user's current role.
        fetchPermissions().catch(() => {})
        // Administration › Menus changed something: show it without a reload
        window.addEventListener('menu-changed', this.loadMenu)
    },
    mounted() {
        this.setVerticalNavMenuWidth()
    },
}

</script>

<style lang="scss">
.vs-sidebar-group, .navigation-header, .vs-sidebar--item, ul.vs-sidebar-group-items li,.content-wrapper, select, textarea, p, article, b, ul, li, button {
    font-family: var(--font-base);
}
@import "@sass/shm/components/verticalNavMenu.scss";

// Header keeps its height, the menu scrolls in whatever space is left
.v-nav-menu {
    .vs-sidebar--items {
        flex: 1;
        min-height: 0;
        overflow: hidden;
    }

    .nav-shell {
        display: flex;
        flex-direction: column;
        height: 100%;
    }

    .header-sidebar.nav-brand {
        flex-shrink: 0;
    }

    .scroll-area-v-nav-menu {
        flex: 1;
        min-height: 0;
        height: auto;
        padding-bottom: 16px;
    }
}

.v-nav-menu .header-sidebar.nav-brand {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 8px;
    padding: 18px 14px 16px 18px;
    box-shadow: none;
    border-bottom: 1px solid rgba(255, 255, 255, .12);

    &.nav-brand--compact {
        justify-content: center;
        padding-left: 14px;
    }
}

.nav-brand {
    &__link {
        display: flex;
        align-items: center;
        gap: 12px;
        min-width: 0;
        color: #fff;
    }

    &__logo {
        flex-shrink: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        width: 46px;
        height: 46px;
        padding: 3px;
        border-radius: 50%;
        background: #fff;
        box-shadow: 0 0 0 3px rgba(255, 255, 255, .18), 0 6px 14px rgba(0, 0, 0, .22);
        transition: transform .25s ease;

        div, img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }
    }

    &__link:hover &__logo {
        transform: rotate(-8deg) scale(1.05);
    }

    &__text {
        display: flex;
        flex-direction: column;
        min-width: 0;
        line-height: 1.25;
    }

    &__title {
        font-size: 1.2rem;
        font-weight: 700;
        letter-spacing: .01em;
        color: #fff;
        white-space: nowrap;
    }

    &__subtitle {
        margin-top: 2px;
        font-size: .72rem;
        color: rgba(255, 255, 255, .7);
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    &__btn {
        flex-shrink: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        width: var(--btn-height-sm);
        height: var(--btn-height-sm);
        padding: 0;
        border: none;
        border-radius: var(--btn-radius);
        color: #fff;
        background: rgba(255, 255, 255, .12);
        cursor: pointer;

        .feather-icon {
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 !important; // the theme adds margin-right to every sidebar icon
            transition: transform .25s ease;

            svg {
                color: #fff !important;
            }
        }

        &:hover {
            background: rgba(255, 255, 255, .22);
        }

        // Collapsed: point the arrows outward
        &.is-reduced .feather-icon {
            transform: rotate(180deg);
        }
    }
}
// ---------------------------------------------------------------- Menu items (blue sidebar)
// Dark mode keeps the same blue sidebar as the standard (semi-dark) look
.theme-dark .v-nav-menu {
    .vs-sidebar {
        border-radius: 30px;
    }

    .scroll-area-v-nav-menu {
        .feather-icon,
        span:not(.vs-chip--text),
        a:not(.router-link-active) .feather-icon svg {
            color: #fff;
        }
    }
}

$nav-active-text: #34468a;

.theme-semi-dark .v-nav-menu,
.theme-dark .v-nav-menu {
    .scroll-area-v-nav-menu {
        > .vs-sidebar--item,
        > .vs-sidebar-group {
            padding: 0 12px;
        }
    }

    // Section titles
    .navigation-header {
        margin: 1.2rem 0 .4rem 1.5rem;
        font-size: .72rem;
        font-weight: 500;
        letter-spacing: .06em;
        color: rgba(255, 255, 255, .55);

        &:first-child {
            margin-top: .6rem;
        }
    }

    // Links and group headers share one row style: icon box + label, vertically centered
    .vs-sidebar--item a,
    .vs-sidebar-group > .group-header {
        position: relative; // anchors the group arrow to its own row
        display: flex;
        align-items: center;
        min-height: 42px;
        margin-bottom: 2px;
        padding: 0 12px;
        border-radius: 10px;
        color: rgba(255, 255, 255, .88);
        transition: background .15s ease, color .15s ease;

        > *:not(.feather-grp-header-arrow) {
            transform: none !important; // no sideways jump on hover
        }

        &:hover {
            background: rgba(255, 255, 255, .1);
        }
    }

    .vs-sidebar-group > .group-header > span:not(.feather-grp-header-arrow) {
        display: flex;
        align-items: center;
        min-width: 0;
        padding-right: 20px; // room for the arrow
    }

    // Fixed-size icon box so every label lines up
    .vs-sidebar--item a > .feather-icon,
    .vs-sidebar-group > .group-header > span > .feather-icon {
        flex-shrink: 0;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 32px;
        height: 32px;
        margin-right: 12px !important;
        border-radius: 8px;
        transition: background .15s ease;

        .feather {
            width: 19px;
            height: 19px;
            stroke-width: 1.8;
        }
    }

    .vs-sidebar--item a span,
    .vs-sidebar-group > .group-header span {
        font-size: 14.5px;
        line-height: 1.5;
    }

    // Active page: white pill, icon on a soft tinted square
    .vs-sidebar--item .router-link-active {
        background: #fff !important;
        box-shadow: 0 6px 16px -6px rgba(0, 0, 0, .35);

        span,
        .feather-icon,
        .feather-icon svg {
            color: $nav-active-text !important;
            font-weight: 600;
        }

        > .feather-icon {
            background: rgba(52, 70, 138, .1);
        }
    }

    // Open group, or group that holds the active page
    .vs-sidebar-group {
        &.vs-sidebar-group-open > .group-header,
        &.vs-sidebar-group-active > .group-header {
            background: rgba(255, 255, 255, .08) !important;
            box-shadow: none;
            border: none;
        }

        &.vs-sidebar-group-active > .group-header span {
            color: #fff;
            font-weight: 500;
        }

        &.vs-sidebar-group-active > .group-header > span > .feather-icon {
            background: rgba(255, 255, 255, .14);
        }

        > .group-header .feather-grp-header-arrow {
            position: absolute !important;
            top: 50%;
            right: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 22px;
            height: 22px;
            margin: -11px 0 0 !important;
            border-radius: 6px;
            background: rgba(255, 255, 255, .1);
            transform: rotate(0deg);
            transition: transform .25s ease, background .15s ease;

            .feather {
                width: 14px;
                height: 14px;
                stroke-width: 2.2;
            }

            &.is-open {
                transform: rotate(90deg);
            }
        }

        > .group-header:hover .feather-grp-header-arrow {
            background: rgba(255, 255, 255, .2);
        }
    }

    // Sub-items hang off a thin guide line under the group icon
    .vs-sidebar-group-items {
        width: auto;
        margin-left: 27px;
        padding-left: 8px !important;
        border-left: 1px solid rgba(255, 255, 255, .18);

        .vs-sidebar--item a {
            min-height: 38px;
            padding: 0 10px;

            > .feather-icon {
                width: 26px;
                height: 26px;
                margin-right: 10px !important;

                .feather {
                    width: 16px;
                    height: 16px;
                }
            }
        }
    }
}
// ---------------------------------------------------------------- Motion
// Row transforms are locked (no sideways jump from the theme), so motion lives on the svg, pseudo-elements and list items.
.theme-semi-dark .v-nav-menu,
.theme-dark .v-nav-menu {
    // Light sweep across a link on hover
    .vs-sidebar--item a {
        overflow: hidden;

        &::after {
            content: '';
            position: absolute;
            top: 0;
            bottom: 0;
            left: -60%;
            width: 50%;
            background: linear-gradient(100deg, transparent, rgba(255, 255, 255, .16), transparent);
            transform: skewX(-20deg);
            pointer-events: none;
            opacity: 0;
        }

        &:hover::after {
            animation: nav-shine .7s ease forwards;
        }
    }

    // Icons lift and tilt slightly on hover
    .vs-sidebar--item a,
    .vs-sidebar-group > .group-header {
        .feather-icon .feather {
            transition: transform .25s cubic-bezier(.34, 1.56, .64, 1);
        }

        &:hover .feather-icon:not(.feather-grp-header-arrow) .feather {
            transform: translateY(-1px) scale(1.12) rotate(-6deg);
        }
    }

    // Active page: pill settles in and its icon pops
    .vs-sidebar--item .router-link-active {
        animation: nav-pill-in .35s cubic-bezier(.22, 1, .36, 1);

        > .feather-icon .feather {
            animation: nav-icon-pop .45s cubic-bezier(.34, 1.56, .64, 1);
        }

        &::after {
            background: linear-gradient(100deg, transparent, rgba(52, 70, 138, .08), transparent);
        }
    }

    // Opening a group: sub-items cascade in
    .vs-sidebar-group-open > .vs-sidebar-group-items > li {
        animation: nav-item-in .32s cubic-bezier(.22, 1, .36, 1) both;

        @for $i from 1 through 10 {
            &:nth-child(#{$i}) {
                animation-delay: #{($i - 1) * 40}ms;
            }
        }
    }

    // Guide line grows down with the list
    .vs-sidebar-group-open > .vs-sidebar-group-items {
        animation: nav-guide-in .35s ease both;
    }

    // Section titles and top-level rows fade up once when the menu first renders
    .scroll-area-v-nav-menu > .navigation-header,
    .scroll-area-v-nav-menu > .vs-sidebar--item,
    .scroll-area-v-nav-menu > .vs-sidebar-group {
        animation: nav-item-in .4s cubic-bezier(.22, 1, .36, 1) both;

        @for $i from 1 through 12 {
            &:nth-child(#{$i}) {
                animation-delay: #{$i * 30}ms;
            }
        }
    }
}

@keyframes nav-shine {
    0% { left: -60%; opacity: 0; }
    20% { opacity: 1; }
    100% { left: 130%; opacity: 0; }
}

@keyframes nav-pill-in {
    from { opacity: .4; transform: scale(.94); }
    to { opacity: 1; transform: none; }
}

@keyframes nav-icon-pop {
    0% { transform: scale(.6) rotate(-15deg); }
    60% { transform: scale(1.18) rotate(4deg); }
    100% { transform: none; }
}

@keyframes nav-item-in {
    from { opacity: 0; transform: translateX(-10px); }
    to { opacity: 1; transform: none; }
}

@keyframes nav-guide-in {
    from { border-left-color: transparent; }
}

@media (prefers-reduced-motion: reduce) {
    .v-nav-menu *,
    .v-nav-menu *::after {
        animation: none !important;
        transition: none !important;
    }
}
</style>

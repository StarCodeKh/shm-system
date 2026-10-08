<template>
    <div class="layout--main" :class="[layoutTypeClass, navbarClasses, footerClasses, {'no-scroll': isAppPage}]">

        <!-- Name and logo from Settings › General (site.js) -->
        <v-nav-menu
            :title        = "site.name || $t('brand_name_fallback')"
            :logo         = "site.logo"
            parent        = ".layout--main" />

        <div id="content-area" :class="[contentAreaClass, {'show-overlay': bodyOverlay}]">
            <div id="content-overlay" />

            <the-navbar-vertical
                :navbarColor="navbarColor"
                :class="[
                    {'text-white' : isNavbarDark  && !isThemeDark},
                    {'text-base'  : !isNavbarDark && isThemeDark}
                ]" />

            <div class="content-wrapper">

                <div class="router-view">
                    <div class="router-content">

                        <transition :name="routerTransition">

                            <div v-if="($route.meta.breadcrumb || $route.meta.pageTitle) && !$route.meta.hideHeader" class="router-header flex flex-wrap items-center mb-6">
                                <div
                                    class="content-area__heading"
                                    :class="{'pr-4 border-0 md:border-r border-solid border-grey-light' : $route.meta.breadcrumb}">
                                    <h2 class="mb-1">{{ $tt(routeTitle) }}</h2>
                                </div>

                                <!-- BREADCRUMB -->
                                <vx-breadcrumb class="ml-4 md:block hidden" v-if="$route.meta.breadcrumb" :route="$route" :isRTL="$vs.rtl" />


                            </div>
                        </transition>

                        <div class="content-area__content">

                            <!-- Vue Router 4: the page animates inside the transition. The wrapper div is always there,
                                so pages that render nothing until their data arrives (v-if) still finish fading in. -->
                            <router-view v-slot="{ Component, route }">
                                <transition :name="routerTransition" mode="out-in">
                                    <div :key="route.name || route.path" class="router-page">
                                        <component :is="Component" @changeRouteTitle="changeRouteTitle" @changeDocumentTitle="(title) => $emit('changeDocumentTitle', title)" @setAppClasses="(classesStr) => $emit('setAppClasses', classesStr)" />
                                    </div>
                                </transition>
                            </router-view>
                        </div>
                    </div>
                </div>
            </div>
            <the-footer />
        </div>
    </div>
</template>


<script>
import TheNavbarVertical   from '@/layouts/components/navbar/TheNavbarVertical.vue'
import TheFooter           from '@/layouts/components/TheFooter.vue'
import themeConfig         from '@/../themeConfig.js'
import { site, loadSite }  from '@/site.js'
import VNavMenu            from '@/layouts/components/vertical-nav-menu/VerticalNavMenu.vue'

export default {
    components: {
        TheFooter,
        TheNavbarVertical,
        VNavMenu,
    },
    data() {
        return {
            site,
            footerType        : themeConfig.footerType  || 'static',
            isNavbarDark      : false,
            navbarColor       : themeConfig.navbarColor || '#fff',
            navbarType        : themeConfig.navbarType  || 'floating',
            routerTransition  : themeConfig.routerTransition || 'none',
            routeTitle        : this.$route.meta.pageTitle,
        }
    },
    watch: {
        "$route"() {
            this.routeTitle = this.$route.meta.pageTitle
        },
        isThemeDark(val) {
            const color = this.navbarColor == "#fff" && val ? "#10163a" : "#fff"
            this.updateNavbarColor(color)
        },
    },
    computed: {
        bodyOverlay() { return this.$store.state.bodyOverlay },
        contentAreaClass() {
            if      (this.verticalNavMenuWidth == "default") return "content-area-reduced"
            else if (this.verticalNavMenuWidth == "reduced") return "content-area-lg"
            else return "content-area-full"
        },
        footerClasses() {
            return {
                'footer-hidden': this.footerType == 'hidden',
                'footer-sticky': this.footerType == 'sticky',
                'footer-static': this.footerType == 'static',
            }
        },
        isAppPage() {
            return this.$route.meta.no_scroll
        },
        isThemeDark()     { return this.$store.state.theme == 'dark' },
        layoutTypeClass() { return 'main-vertical' },
        navbarClasses()   {
            return {
                'navbar-hidden'   : this.navbarType == 'hidden',
                'navbar-sticky'   : this.navbarType == 'sticky',
                'navbar-static'   : this.navbarType == 'static',
                'navbar-floating' : this.navbarType == 'floating',
            }
        },
        verticalNavMenuWidth() { return this.$store.state.verticalNavMenuWidth },
        windowWidth()          { return this.$store.state.windowWidth }
    },
    methods: {
        changeRouteTitle(title) {
            this.routeTitle = title
            this.$emit('changeRouteTitle', title)
        },
        updateNavbarColor(val) {
            this.navbarColor = val
            this.isNavbarDark = val != "#fff"
        },
    },
    created() {
        loadSite()
        const color = this.navbarColor == "#fff" && this.isThemeDark ? "#10163a" : this.navbarColor
        this.updateNavbarColor(color)
        // Below 1200px the side menu starts closed and opens over the page
        if (this.windowWidth < 1200) {
            this.$store.commit('TOGGLE_IS_VERTICAL_NAV_MENU_ACTIVE', false)
            this.$store.dispatch('updateVerticalNavMenuWidth', 'no-nav-menu')
        } else {
            this.$store.commit('TOGGLE_IS_VERTICAL_NAV_MENU_ACTIVE', true)
        }
    },
}
</script>

<template>
	<div id="app" :class="vueAppClasses">
		<router-view @setAppClasses="setAppClasses" @changeRouteTitle="(title) => titleOverride = title" @changeDocumentTitle="(title) => titleOverride = title" />
	</div>
</template>

<script>

import themeConfig from '@/../themeConfig.js'
import jwt         from "@/http/requests/auth/jwt/index.js"

const APP_NAME = 'SHM-System'

export default {
  data() {
    return {
      vueAppClasses: [],
      titleOverride: null, // set by pages whose title is only known after loading (e.g. reports)
    }
  },
  computed: {
    // Browser tab title: "<page title> | SHM-System", following the current language
    documentTitle() {
      const meta = this.$route.meta || {}
      const crumbs = meta.breadcrumb || []
      const last = crumbs.length ? crumbs[crumbs.length - 1].title : null
      const title = this.titleOverride || meta.title || meta.pageTitle || last
      // Depend on the locale so the title is re-translated when the language changes
      return title && this.$i18n.locale ? `${this.$tt(title)} | ${APP_NAME}` : APP_NAME
    }
  },
  watch: {
    '$route'() {
      this.titleOverride = null
    },
    documentTitle: {
      handler(val) { document.title = val },
      immediate: true
    },
    '$store.state.theme'(val) {
      this.toggleClassInBody(val)
    },
    '$vs.rtl'(val) {
      document.documentElement.setAttribute("dir", val ? "rtl" : "ltr")
    }
  },
  methods: {
    toggleClassInBody(className) {
      if (className == 'dark') {
        if (document.body.className.match('theme-semi-dark')) document.body.classList.remove('theme-semi-dark')
        document.body.classList.add('theme-dark')
      }
      else if (className == 'semi-dark') {
        if (document.body.className.match('theme-dark')) document.body.classList.remove('theme-dark')
        document.body.classList.add('theme-semi-dark')
      }
      else {
        if (document.body.className.match('theme-dark'))      document.body.classList.remove('theme-dark')
        if (document.body.className.match('theme-semi-dark')) document.body.classList.remove('theme-semi-dark')
      }
    },
    setAppClasses(classesStr) {
      this.vueAppClasses.push(classesStr)
    },
    handleWindowResize() {
      this.$store.commit('UPDATE_WINDOW_WIDTH', window.innerWidth)

      // Set --vh property
      document.documentElement.style.setProperty('--vh', `${window.innerHeight * 0.01}px`);
    },
    handleScroll() {
      this.$store.commit('UPDATE_WINDOW_SCROLL_Y', window.scrollY)
    }
  },
  mounted() {
    this.toggleClassInBody(this.$store.state.theme)
    this.$store.commit('UPDATE_WINDOW_WIDTH', window.innerWidth)

    let vh = window.innerHeight * 0.01;
    // Then we set the value in the --vh custom property to the root of the document
    document.documentElement.style.setProperty('--vh', `${vh}px`);
  },
  async created() {

    // jwt
    jwt.init()

    let dir = this.$vs.rtl ? "rtl" : "ltr"
    document.documentElement.setAttribute("dir", dir)

    window.addEventListener('resize', this.handleWindowResize)
    window.addEventListener('scroll', this.handleScroll)

    // Auth0
    try       { await this.$auth.renewTokens() }
    catch (e) { console.error(e) }

  },
  destroyed() {
    window.removeEventListener('resize', this.handleWindowResize)
    window.removeEventListener('scroll', this.handleScroll)
  },
}

</script>

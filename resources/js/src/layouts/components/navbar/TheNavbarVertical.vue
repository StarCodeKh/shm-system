<template>
  <div class="relative">

    <div class="vx-navbar-wrapper" :class="classObj">

      <vs-navbar class="vx-navbar navbar-custom navbar-skelton" :color="navbarColorLocal" :class="textColor">

        <!-- Open sidebar (below xl the sidebar is hidden) -->
        <button type="button" class="nav-icon-btn xl:hidden mr-2" :title="$t('nav_open_menu')" @click.stop="showSidebar">
          <feather-icon icon="MenuIcon" svgClasses="h-5 w-5" />
        </button>

        <!-- Greeting + today's date -->
        <div class="nav-greeting">
          <p class="nav-greeting__title">
            {{ greeting }}<template v-if="userName">, <span>{{ userName }}</span></template>
          </p>
          <p class="nav-greeting__date">
            <feather-icon icon="CalendarIcon" svgClasses="h-3 w-3" />
            {{ today }}
          </p>
        </div>

        <vs-spacer />

        <div class="nav-actions">
          <i18n />
          <theme-toggle />
          <notification-drop-down />
          <setting />
          <span class="nav-actions__divider"></span>
          <profile-drop-down />
        </div>

      </vs-navbar>
    </div>
  </div>
</template>


<script>
import I18n from "./components/I18n.vue"
import NotificationDropDown from "./components/NotificationDropDown.vue"
import ThemeToggle from "./components/ThemeToggle.vue"
import ProfileDropDown from "./components/ProfileDropDown.vue"
import Setting from "./components/Setting.vue";

const INTL_LOCALES = { kh: 'km-KH', en: 'en-GB' }

export default {
  name: "the-navbar-vertical",
  props: {
    navbarColor: {
      type: String,
      default: "#fff",
    },
  },
  components: {
    I18n,
    NotificationDropDown,
    ThemeToggle,
    ProfileDropDown,
    Setting
  },
  data() {
    return {
      now: new Date(),
      clock: null
    }
  },
  computed: {
    navbarColorLocal() {
      return this.$store.state.theme === "dark" && this.navbarColor === "#fff" ? "#10163a" : this.navbarColor
    },
    verticalNavMenuWidth() {
      return this.$store.state.verticalNavMenuWidth
    },
    textColor() {
      return { 'text-white': (this.navbarColor != '#10163a' && this.$store.state.theme === 'dark') || (this.navbarColor != '#fff' && this.$store.state.theme !== 'dark') }
    },
    userName() {
      return this.$store.getters.activeUserName(this.$i18n.locale)
    },
    greeting() {
      const hour = this.now.getHours()
      if (hour < 12) return this.$t('nav_good_morning')
      if (hour < 17) return this.$t('nav_good_afternoon')
      return this.$t('nav_good_evening')
    },
    today() {
      return this.now.toLocaleDateString(INTL_LOCALES[this.$i18n.locale] || 'en-GB', {
        weekday: 'long', day: 'numeric', month: 'long', year: 'numeric'
      })
    },

    // NAVBAR STYLE
    classObj() {
      if (this.verticalNavMenuWidth == "default") return "navbar-default"
      else if (this.verticalNavMenuWidth == "reduced") return "navbar-reduced"
      else if (this.verticalNavMenuWidth) return "navbar-full"
    },
  },
  mounted() {
    // Keep greeting and date current if the tab stays open
    this.clock = setInterval(() => { this.now = new Date() }, 60 * 1000)
  },
  beforeUnmount() {
    clearInterval(this.clock)
  },
  methods: {
    showSidebar() {
      this.$store.commit('TOGGLE_IS_VERTICAL_NAV_MENU_ACTIVE', true);
    }
  }
}
</script>

<style lang="scss">
.vx-navbar.navbar-custom {
  .vs-con-items {
    align-items: center;
  }
}

.nav-greeting {
  min-width: 0;
  line-height: 1.4;

  &__title {
    font-size: 1rem;
    font-weight: 600;
    color: #2c2e45;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;

    span {
      color: rgb(var(--vs-primary));
    }
  }

  &__date {
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: .8rem;
    color: #8a8ca5;

    .feather-icon {
      display: flex;
    }
  }

  @media (max-width: 640px) {
    &__date {
      display: none;
    }
  }
}

.nav-actions {
  display: flex;
  align-items: center;
  gap: 4px;

  &__divider {
    width: 1px;
    height: 28px;
    margin: 0 8px;
    background: #e6e7ef;
  }
}

// Round icon button used across navbar actions
.nav-icon-btn {
  position: relative;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  width: var(--btn-height);
  height: var(--btn-height);
  padding: 0;
  border: none;
  border-radius: var(--btn-radius);
  background: transparent;
  color: #5e6078;
  cursor: pointer;

  .feather-icon {
    display: flex;
  }

  &:hover,
  &.is-active {
    background: rgba(var(--vs-primary), .1);
    color: rgb(var(--vs-primary));
  }
}

.theme-dark {
  .nav-greeting__title {
    color: #fff;
  }
  .nav-icon-btn {
    color: #c2c6dc;
  }
  .nav-actions__divider {
    background: #414561;
  }
}
</style>

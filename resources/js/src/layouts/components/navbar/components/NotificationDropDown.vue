<template>
  <!-- NOTIFICATIONS -->
  <vs-dropdown vs-custom-content vs-trigger-click class="cursor-pointer">
    <button type="button" class="nav-icon-btn" :title="$t('nav_notifications')">
      <feather-icon icon="BellIcon" svgClasses="h-5 w-5" :class="{ 'nav-bell--ring': countNot }" />
      <span v-if="countNot" class="nav-badge">{{ countNot > 99 ? '99+' : countNot }}</span>
    </button>

    <vs-dropdown-menu class="nav-notify-menu dropdown-custom vx-navbar-dropdown">
      <div class="nav-notify-menu__header">
        <h4>{{ $t('nav_notifications') }}</h4>
        <span v-if="countNot" class="nav-notify-menu__count">{{ $t('nav_new', { n: countNot }) }}</span>
      </div>

      <VuePerfectScrollbar v-if="unreadNotifications.length" ref="mainSidebarPs" class="nav-notify-menu__list"
        :settings="settings" :key="$vs.rtl">
        <ul>
          <li v-for="ntf in unreadNotifications" :key="ntf.id" class="nav-notify-item" @click="openNotification(ntf)">
            <span class="nav-notify-item__icon">
              <feather-icon icon="BellIcon" svgClasses="h-4 w-4" />
            </span>
            <div class="nav-notify-item__body">
              <p class="nav-notify-item__title">{{ ntf.data && ntf.data.title }}</p>
              <p class="nav-notify-item__text">{{ ntf.data && ntf.data.message }}</p>
              <p class="nav-notify-item__time">{{ elapsedTime(ntf.created_at) }}</p>
            </div>
          </li>
        </ul>
      </VuePerfectScrollbar>

      <div v-else class="nav-notify-menu__empty">
        <span><feather-icon icon="BellOffIcon" svgClasses="h-6 w-6" /></span>
        <p class="font-semibold">{{ $t('nav_no_notifications') }}</p>
        <small>{{ $t('nav_all_caught_up') }}</small>
      </div>

      <button type="button" class="nav-notify-menu__footer" @click="initViewNotification()">
        {{ $t('nav_view_all') }}
      </button>
    </vs-dropdown-menu>
  </vs-dropdown>
</template>

<script>
  import axios from "@/axios.js"
  import apiConfig from "@/apiConfig.js"
  import VuePerfectScrollbar from 'vue-perfect-scrollbar'

  const REFRESH_MS = 60 * 1000

  export default {
    components: {
      VuePerfectScrollbar
    },
    data() {
      return {
        api: apiConfig._apiUnreadNotification,
        unreadNotifications: [],
        countNot: 0,
        poller: null,
        settings: {
          maxScrollbarLength: 60,
          wheelSpeed: .60,
        },
      }
    },
    created() {
      this.getData();
      this.poller = setInterval(this.getData, REFRESH_MS)
    },
    beforeDestroy() {
      clearInterval(this.poller)
    },
    methods: {
      getData() {
        return axios.get(this.api)
          .then((response) => {
            this.unreadNotifications = response.data.unread_notifications || [];
            this.countNot = response.data.unread_count || 0;
          }).catch(() => {
            // Background refresh: keep the last known list rather than toasting every minute
          })
      },
      openNotification(ntf) {
        const url = ntf.data && ntf.data.action_url
        // Only follow in-app paths; absolute URLs from older notifications fall back to the list
        if (url && url.startsWith('/')) this.$router.push(url).catch(() => { })
        else this.initViewNotification()
      },
      initViewNotification(){
        this.$router.push("/nofication").catch(() => { });
      },
      elapsedTime(startTime) {
        const seconds = Math.max(0, Math.floor((new Date() - new Date(startTime)) / 1000))
        const minutes = Math.floor(seconds / 60)
        const hours = Math.floor(minutes / 60)
        const days = Math.floor(hours / 24)
        const years = Math.floor(days / 365)

        if (years > 0) return this.$t('time_years_ago', { n: years })
        if (days > 0) return this.$t('time_days_ago', { n: days })
        if (hours > 0) return this.$t('time_hours_ago', { n: hours })
        if (minutes > 0) return this.$t('time_minutes_ago', { n: minutes })
        return this.$t('time_just_now')
      },
    }
  }
</script>

<style lang="scss">
.nav-badge {
  position: absolute;
  top: 4px;
  right: 3px;
  min-width: 17px;
  height: 17px;
  padding: 0 4px;
  font-size: .65rem;
  font-weight: 700;
  line-height: 17px;
  text-align: center;
  color: #fff;
  border-radius: 9px;
  background: rgb(var(--vs-danger));
  box-shadow: 0 0 0 2px #fff;
}

.nav-bell--ring {
  animation: nav-bell-ring 2.4s ease-in-out infinite;
  transform-origin: top center;
}

@keyframes nav-bell-ring {
  0%, 80%, 100% { transform: rotate(0); }
  84% { transform: rotate(14deg); }
  88% { transform: rotate(-12deg); }
  92% { transform: rotate(8deg); }
  96% { transform: rotate(-4deg); }
}

.nav-notify-menu {
  .vs-dropdown--custom {
    padding: 0;
    width: 360px;
    max-width: calc(100vw - 32px);
    overflow: hidden;
  }

  &__header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 14px 16px;
    border-bottom: 1px solid #eef0f5;

    h4 {
      font-size: 1rem;
      font-weight: 600;
    }
  }

  &__count {
    padding: 2px 10px;
    font-size: .75rem;
    font-weight: 600;
    border-radius: 999px;
    color: rgb(var(--vs-primary));
    background: rgba(var(--vs-primary), .12);
  }

  &__list {
    position: relative;
    max-height: 340px;
  }

  &__empty {
    display: flex;
    flex-direction: column;
    align-items: center;
    padding: 32px 16px;
    text-align: center;
    color: #8a8ca5;

    span {
      display: flex;
      align-items: center;
      justify-content: center;
      width: 52px;
      height: 52px;
      margin-bottom: 10px;
      border-radius: 50%;
      background: #f4f5f9;
    }

    p {
      color: #2c2e45;
    }
  }

  &__footer {
    display: block;
    width: 100%;
    padding: 12px;
    font-family: inherit;
    font-size: .875rem;
    font-weight: 600;
    color: rgb(var(--vs-primary));
    border: none;
    border-top: 1px solid #eef0f5;
    background: transparent;
    cursor: pointer;

    &:hover {
      background: rgba(var(--vs-primary), .05);
    }
  }
}

.nav-notify-item {
  display: flex;
  gap: 12px;
  padding: 12px 16px;
  cursor: pointer;
  transition: background .15s;

  & + & {
    border-top: 1px solid #f2f3f7;
  }

  &:hover {
    background: #f8f9fb;
  }

  &__icon {
    flex-shrink: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    width: 34px;
    height: 34px;
    border-radius: 50%;
    color: rgb(var(--vs-primary));
    background: rgba(var(--vs-primary), .12);

    .feather-icon {
      display: flex;
    }
  }

  &__body {
    min-width: 0;
  }

  &__title {
    font-size: .875rem;
    font-weight: 600;
    color: #2c2e45;
  }

  &__text {
    font-size: .8rem;
    color: #6e7091;
    overflow: hidden;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
  }

  &__time {
    margin-top: 2px;
    font-size: .72rem;
    color: #a3a5bb;
  }
}

.theme-dark {
  .nav-badge {
    box-shadow: 0 0 0 2px #10163a;
  }
  .nav-notify-menu__header,
  .nav-notify-menu__footer {
    border-color: #414561;
  }
  .nav-notify-menu__empty span {
    background: #262c49;
  }
  .nav-notify-menu__empty p,
  .nav-notify-item__title {
    color: #fff;
  }
  .nav-notify-item:hover {
    background: #262c49;
  }
}
</style>

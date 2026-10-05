<template>
  <!-- NOTIFICATIONS -->
  <vs-dropdown vs-custom-content vs-trigger-click class="cursor-pointer">
    <feather-icon icon="BellIcon" class="cursor-pointer mt-1 sm:mr-6 -mr-1" :badge="countNot" />
    <vs-dropdown-menu class="notification-dropdown dropdown-custom vx-navbar-dropdown">
      <div class="notification-top text-center p-5 bg-primary text-white">
        <h3 class="text-white">{{ countNot }} New</h3>
        <p class="opacity-75">Notifications</p>
      </div>
      <VuePerfectScrollbar ref="mainSidebarPs" class="scroll-area--nofications-dropdown p-0 mb-10" :settings="settings" :key="$vs.rtl">
        <ul class="bordered-items">
          <li v-for="ntf in unreadNotifications" :key="ntf.id" class="flex justify-between px-4 py-4 notification cursor-pointer">
            <div class="flex items-start">
              <!-- <feather-icon :icon="ntf.icon" :svgClasses="[`text-${ntf.category}`, 'stroke-current mr-1 h-6 w-6']"></feather-icon> -->
              <div class="mx-2">
                <span class="font-medium block notification-title" :class="[`text-${ntf.notifiable_type.category}`]">{{ ntf.title }}</span>
                <small>{{ ntf.message }}</small>
              </div>
            </div>
            <small class="mt-1 whitespace-no-wrap">{{ elapsedTime(ntf.created_at) }}</small>
          </li>
        </ul>
      </VuePerfectScrollbar>
      <div v-if="countNot" class="
        checkout-footer
        fixed
        bottom-0
        rounded-b-lg
        text-primary
        w-full
        p-2
        font-semibold
        text-center
        border
        border-b-0
        border-l-0
        border-r-0
        border-solid
        d-theme-border-grey-light
        cursor-pointer">
        <span class="show-notification-title" @click="initViewNotification()">បង្ហាញការជូនដំណឹងទាំងអស់</span>
      </div>
      <div v-else class="
        checkout-footer
        fixed
        bottom-0
        rounded-b-lg
        text-danger
        w-full
        p-2
        font-semibold
        text-center
        border
        border-b-0
        border-l-0
        border-r-0
        border-solid
        d-theme-border-grey-light
        cursor-pointer">
        <span class="show-notification-title">ពុំមានការជូនដំណឹងទេ!</span>
      </div>
    </vs-dropdown-menu>
  </vs-dropdown>
</template>

<script>
  import axios from "@/axios.js"
  import apiConfig from "@/apiConfig.js"
  import { ref } from 'vue';
  import VuePerfectScrollbar from 'vue-perfect-scrollbar'

  export default {
    components: {
      VuePerfectScrollbar
    },
    data() {
      return {
        api: apiConfig._apiUnreadNotification,
        data: {},
        page_number: 1,
        unreadNotifications: [
            {
              "id": 4,
              "type": "App\\Notifications\\DailyReminderNotification",
              "notifiable_id": 1,
              "notifiable_type": "App\\User",
              "data": {
                  "title": "Daily Reminder",
                  "message": "Don’t forget to check your tasks today!",
                  "action_url": "http://localhost/dashboard",
                  "image": null
              },
              "read_at": null,
              "created_at": "2025-08-31T08:48:32.000000Z",
              "updated_at": "2025-08-31T08:48:32.000000Z"
              // index    : 0,
              // title    : 'សំណើរផ្លាស់ប្តូរគណនីយ ពីក្រសួងអប់រំ យុវជននិងកីឡា',
              // message      : 'ស្នើសុំដោះដូមន្ត្រីចំនួន ២រូប',
              // icon     : 'MessageSquareIcon',
              // time     : this.randomDate({sec: 10}),
              // category : 'primary'
            },
            // { index    : 1,
            //   title    : 'សំណើរផ្លាស់ប្តូរគណនីយ',
            //   msg      : 'ស្នើសុំដោះដូមន្ត្រីចំនួន ២រូប',
            //   icon     : 'PackageIcon',
            //   time     : this.randomDate({sec: 40}),
            //   category : 'success'
            // },
            // { index    : 2,
            //   title    : 'សំណើរផ្លាស់ប្តូរគណនីយ!',
            //   msg      : 'ស្នើសុំដោះដូមន្ត្រីចំនួន ២រូប',
            //   icon     : 'AlertOctagonIcon',
            //   time     : this.randomDate({min: 1}),
            //   category : 'danger'
            // },
            // { index    : 3,
            //   title    : 'សំណើរផ្លាស់ប្តូរគណនីយ',
            //   msg      : 'ស្នើសុំដោះដូមន្ត្រីចំនួន ២រូប',
            //   icon     : 'MailIcon',
            //   time     : this.randomDate({min: 6}),
            //   category : 'primary'
            // },
            // { index    : 4,
            //   title    : 'សំណើរផ្លាស់ប្តូរគណនីយ',
            //   msg      : 'ស្នើសុំដោះដូមន្ត្រីចំនួន ២រូប',
            //   icon     : 'CalendarIcon',
            //   time     : this.randomDate({hr: 2}),
            //   category : 'warning'
            // },
        ],
        countNot: 0,
        settings: {
          maxScrollbarLength: 60,
          wheelSpeed: .60,
        },
      }
    },
    created(){
      // if(this.unreadNotifications.length > 0){
      //   this.countNot = this.unreadNotifications.length
      // }else{
      //   this.countNot = 0
      // }
      this.getData();
    },
    methods: {
      getDataTable(_search_criteria) {
        return new Promise((resolve, reject) => {
          axios.get(this.api)
              .then((response) => {
                this.unreadNotifications = response.data.unread_notifications;
                this.countNot = response.data.unread_count;
                this.$vs.loading.close();
              }).catch((error) => {
              // reject(error)
              this.$vs.notify({
                title: 'Message',
                text: "Error, while trying to do action or no permission allow!",
                iconPack: 'feather',
                icon: 'icon-check-circle',
                color: 'danger'
              })
              this.$vs.loading.close();
          })
        })
      },
      getData() {
        let _search_criteria = {
          sort: "",
          order: "",
          page_number: this.page_number,
          search_field: ""
        }
        this.getDataTable(_search_criteria);
      },
      initViewNotification(){
        this.$router.push("/nofication").catch(() => { });
      },
      elapsedTime(startTime) {
        let x        = new Date(startTime)
        let now      = new Date()
        var timeDiff = now - x
        timeDiff    /= 1000

        var seconds = Math.round(timeDiff)
        timeDiff    = Math.floor(timeDiff / 60)

        var minutes = Math.round(timeDiff % 60)
        timeDiff    = Math.floor(timeDiff / 60)

        var hours   = Math.round(timeDiff % 24)
        timeDiff    = Math.floor(timeDiff / 24)

        var days    = Math.round(timeDiff % 365)
        timeDiff    = Math.floor(timeDiff / 365)

        var years   = timeDiff

        if (years > 0) {
          return years + (years > 1 ? ' Years ' : ' Year ') + 'ago'
        } else if (days > 0) {
          return days + (days > 1 ? ' Days ' : ' Day ') + 'ago'
        } else if (hours > 0) {
          return hours + (hours > 1 ? ' Hrs ' : ' Hour ') + 'ago'
        } else if (minutes > 0) {
          return minutes + (minutes > 1 ? ' Mins ' : ' Min ') + 'ago'
        } else if (seconds > 0) {
          return seconds + (seconds > 1 ? ' sec ago' : 'just now')
        }

        return 'Just Now'
      },
      // Method for creating dummy notification time
      randomDate({ hr, min, sec }) {
        let date = new Date()

        if (hr) date.setHours(date.getHours() - hr)
        if (min) date.setMinutes(date.getMinutes() - min)
        if (sec) date.setSeconds(date.getSeconds() - sec)

        return date
      }
    }
  }
</script>

<style lang="scss">
  span.show-notification-title{
    font-family: "Khmer MEF1";
  }
</style>
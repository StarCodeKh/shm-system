<template>
  <div class="the-navbar__user-meta flex items-center" v-if="activeUserInfo.username">

    <div class="text-right leading-tight hidden sm:block">
      <p class="font-semibold">{{ activeUserInfo.username }}</p>
      <small>Available</small>
    </div>

    <vs-dropdown vs-custom-content vs-trigger-click class="cursor-pointer">

      <div class="con-img ml-3">
        <span v-if="activeUserInfo.photo != null">
          <img key="onlineImg" :src="activeUserInfo.photo" alt="user-img" width="40" height="40"
            class="rounded-full shadow-md cursor-pointer block" />
        </span>
        <span v-else>
          <img key="onlineImg" :src="noImage" alt="user-img" width="40" height="40"
            class="rounded-full shadow-md cursor-pointer block" />
        </span>
      </div>

      <vs-dropdown-menu class="vx-navbar-dropdown">
        <ul style="min-width: 9rem">

          <li class="flex py-2 px-4 cursor-pointer hover:bg-primary hover:text-white"
            @click="$router.push('/user/edit/' + activeUserInfo.id).catch(() => { })">
            <feather-icon icon="UserIcon" svgClasses="w-4 h-4" />
            <span class="ml-2">{{ $t('Profile') }}</span>
          </li>

          <!-- <li
            class="flex py-2 px-4 cursor-pointer hover:bg-primary hover:text-white"
            @click="$router.push('/apps/email').catch(() => {})">
            <feather-icon icon="MailIcon" svgClasses="w-4 h-4" />
            <span class="ml-2">Inbox</span>
          </li>

          <li
            class="flex py-2 px-4 cursor-pointer hover:bg-primary hover:text-white"
            @click="$router.push('/apps/todo').catch(() => {})">
            <feather-icon icon="CheckSquareIcon" svgClasses="w-4 h-4" />
            <span class="ml-2">Tasks</span>
          </li> -->

          <!-- <li
            class="flex py-2 px-4 cursor-pointer hover:bg-primary hover:text-white"
            @click="$router.push('/apps/chat').catch(() => {})">
            <feather-icon icon="MessageSquareIcon" svgClasses="w-4 h-4" />
            <span class="ml-2">Chat</span>
          </li>

          <li
            class="flex py-2 px-4 cursor-pointer hover:bg-primary hover:text-white"
            @click="$router.push('/apps/eCommerce/wish-list').catch(() => {})">
            <feather-icon icon="HeartIcon" svgClasses="w-4 h-4" />
            <span class="ml-2">Wish List</span>
          </li> -->

          <vs-divider class="m-1" />

          <li class="flex py-2 px-4 cursor-pointer hover:bg-primary hover:text-white" @click="logout">
            <feather-icon icon="LogOutIcon" svgClasses="w-4 h-4" />
            <span class="ml-2">Logout</span>
          </li>
        </ul>
      </vs-dropdown-menu>
    </vs-dropdown>
  </div>
</template>

<script>
import firebase from 'firebase/app'
import 'firebase/auth'

export default {
  data() {
    return {
      noImage: require("@assets/images/no-account.png")
    }
  },
  computed: {
    activeUserInfo() {
      return this.$store.state.AppActiveUser
    }
  },
  created() {
    console.log("profile info", JSON.parse(localStorage.getItem("userInfo")))
  },
  methods: {
    logout() {

      // if user is logged in via auth0
      if (this.$auth.profile) this.$auth.logOut();

      // if user is logged in via firebase
      const firebaseCurrentUser = firebase.auth().currentUser

      if (firebaseCurrentUser) {
        firebase.auth().signOut().then(() => {
          this.$router.push('/pages/login').catch(() => { })
        })
      }
      // If JWT login
      if (localStorage.getItem("accessToken")) {
        localStorage.removeItem("accessToken")
        this.$router.push('/pages/login').catch(() => { })
      }

      // Change role on logout. Same value as initialRole of acj.js
      this.$acl.change('admin')
      localStorage.removeItem('userInfo')

      // This is just for demo Purpose. If user clicks on logout -> redirect
      this.$router.push('/pages/login').catch(() => { })
    },
  }
}
</script>

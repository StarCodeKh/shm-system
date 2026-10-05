<template>
  <div class="login-font">
    <vs-input v-validate="'required|email|min:3'" data-vv-validate-on="blur" name="email" icon-no-border
      icon="icon icon-user" icon-pack="feather" :label-placeholder="$t('email')" v-model="email" class="w-full" />
    <span class="text-danger text-sm">{{ errors.first('email') }}</span>

    <vs-input data-vv-validate-on="blur" v-validate="'required|min:3|max:50'" type="password" name="password"
      icon-no-border icon="icon icon-lock" icon-pack="feather" :label-placeholder="$t('password')" v-model="password"
      v-bind:class="vs - inputx" class="w-full mt-6" />
    <span class="text-danger login-font text-sm">{{ errors.first('password') }}</span>

    <div class="flex flex-wrap justify-between my-5">
      <vs-checkbox v-model="checkbox_remember_me" class="mb-3">{{ $t('remember_me') }}</vs-checkbox>
      <!-- <router-link to="/pages/forgot-password">Forgot Password?</router-link> -->
    </div>
    <div class="flex flex-wrap justify-between mb-3">
      <!-- <vs-button  type="border" @click="registerUser">Register</vs-button> -->
      <vs-button :disabled="!validateForm" @click="loginJWT">{{ $t('login') }}</vs-button>
    </div>
  </div>
</template>

<script>
import axios from "@/axios.js";
export default {
  data() {
    return {
      email: 'admin@admin.com',
      password: 'Ibs@dmin123',
      checkbox_remember_me: true,
      api: "/api/v1/role_menu/permission/index",
      data: {
        data: [
          {}
        ],
        limit: 10,
        total: 3,
      }
    }
  },
  computed: {
    validateForm() {
      return !this.errors.any() && this.email != '' && this.password != '';
    },
  },
  methods: {
    checkLogin() {
      // If user is already logged in notify
      if (this.$store.state.auth.isUserLoggedIn()) {
        // Close animation if passed as payload
        this.$vs.loading.close()
        this.$vs.notify({
          title: 'Login Attempt',
          text: 'You are already logged in!',
          iconPack: 'feather',
          icon: 'icon-alert-circle',
          color: 'warning'
        })
        return false
      }
      return true
    },
    loginJWT() {

      if (!this.checkLogin()) return

      // Loading
      this.$vs.loading()

      const payload = {
        checkbox_remember_me: this.checkbox_remember_me,
        userDetails: {
          email: this.email,
          password: this.password
        }
      }

      this.$store.dispatch('auth/loginJWT', payload)
        .then((res) => {
          if (res.data.accessToken) {
            this.retireveRoleMenu();
          }
          this.$vs.loading.close();
        }).catch((error) => {
          this.$vs.loading.close()
          this.$vs.notify({
            title: 'Error',
            text: error.message,
            iconPack: 'feather',
            icon: 'icon-alert-circle',
            color: 'danger'
          })
        })
    },
    retireveRoleMenu() {
      const params = {
        sort: "id",
        order: "desc",
        page_number: 1,
        limit : 300
      };

      return new Promise(() => {
        axios.post(this.api, params)
          .then((response) => {
            localStorage.setItem("role_menus",  JSON.stringify(response.data.permissions));
            this.$vs.loading.close();
          }).catch(() => {
            this.$vs.notify({
              title: 'Message',
              text: "Error, while trying to do action or no permission allow!",
              iconPack: 'feather',
              icon: 'icon-check-circle',
              color: 'danger'
            })
            this.$vs.loading.close();
          })
      });
    },
    registerUser() {
      if (!this.checkLogin()) return
      this.$router.push('/pages/register').catch(() => { })
    }
  }
}

</script>

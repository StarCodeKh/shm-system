<template>
  <form class="login-jwt" @submit.prevent="loginJWT" novalidate>
    <!-- Alert -->
    <transition name="login-alert">
      <div v-if="alert" :key="alert.type + alert.title" class="login-alert" :class="'login-alert--' + alert.type" role="alert">
        <span class="login-alert__icon"><feather-icon :icon="alertIcon" svgClasses="h-4 w-4" /></span>
        <div class="login-alert__body">
          <p class="login-alert__title">{{ $t(alert.title) }}</p>
          <p class="login-alert__text">{{ $t(alert.text) }}</p>
        </div>
        <button type="button" class="login-alert__close" @click="alert = null">
          <feather-icon icon="XIcon" svgClasses="h-3 w-3" />
        </button>
      </div>
    </transition>

    <!-- Email -->
    <div class="login-field" :class="{ 'has-error': errors.has('email') }">
      <label for="login-email">{{ $t('email') }}</label>
      <div class="login-field__control">
        <span class="login-field__icon"><feather-icon icon="MailIcon" svgClasses="h-4 w-4" /></span>
        <input id="login-email" v-model.trim="email" v-validate="'required|email|min:3'" data-vv-validate-on="blur"
          name="email" type="email" autocomplete="username" placeholder="name@example.com">
      </div>
      <span v-if="errors.has('email')" class="login-field__error">{{ fieldError('email') }}</span>
    </div>

    <!-- Password -->
    <div class="login-field" :class="{ 'has-error': errors.has('password') }">
      <label for="login-password">{{ $t('password') }}</label>
      <div class="login-field__control">
        <span class="login-field__icon"><feather-icon icon="LockIcon" svgClasses="h-4 w-4" /></span>
        <input id="login-password" v-model="password" v-validate="'required|min:3|max:50'" data-vv-validate-on="blur"
          name="password" :type="showPassword ? 'text' : 'password'" autocomplete="current-password"
          placeholder="••••••••">
        <button type="button" class="login-field__toggle" tabindex="-1" @click="showPassword = !showPassword">
          <feather-icon :icon="showPassword ? 'EyeOffIcon' : 'EyeIcon'" svgClasses="h-4 w-4" />
        </button>
      </div>
      <span v-if="errors.has('password')" class="login-field__error">{{ fieldError('password') }}</span>
    </div>

    <div class="flex items-center justify-between mt-2 mb-6">
      <vs-checkbox v-model="checkbox_remember_me" class="ml-0">{{ $t('remember_me') }}</vs-checkbox>
      <!-- <router-link to="/pages/forgot-password">Forgot Password?</router-link> -->
    </div>

    <button type="submit" class="login-submit" :disabled="!validateForm || loading">
      <span v-if="loading" class="login-submit__spinner"></span>
      <span>{{ loading ? $t('login_signing_in') : $t('login') }}</span>
    </button>
  </form>
</template>

<script>
import axios from "@/axios.js";

// i18n keys for each failure kind shown in the inline alert
const ALERTS = {
  credentials: { type: 'danger', title: 'login_err_title_credentials', text: 'login_err_credentials' },
  network: { type: 'danger', title: 'login_err_title_network', text: 'login_err_network' },
  server: { type: 'danger', title: 'login_err_title_server', text: 'login_err_server' },
  throttle: { type: 'warning', title: 'login_err_title_throttle', text: 'login_err_throttle' },
  loggedIn: { type: 'warning', title: 'login_warn_title_logged_in', text: 'login_warn_logged_in' },
}

export default {
  data() {
    return {
      email: 'admin@admin.com',
      password: 'Ibs@dmin123',
      checkbox_remember_me: true,
      showPassword: false,
      loading: false,
      alert: null,
      api: "/api/v1/role_menu/permission/index",
    }
  },
  computed: {
    validateForm() {
      return !this.errors.any() && this.email != '' && this.password != '';
    },
    alertIcon() {
      return this.alert && this.alert.type === 'warning' ? 'AlertTriangleIcon' : 'AlertCircleIcon'
    },
  },
  watch: {
    // Hide the alert once the user starts correcting their input
    email() { this.alert = null },
    password() { this.alert = null },
  },
  methods: {
    // Translated replacement for vee-validate's English-only messages
    fieldError(field) {
      const error = this.errors.items.find(e => e.field === field)
      if (!error) return ''
      const rules = {
        email: {
          required: this.$t('login_val_email_required'),
          email: this.$t('login_val_email_invalid'),
          min: this.$t('login_val_email_invalid'),
        },
        password: {
          required: this.$t('login_val_password_required'),
          min: this.$t('login_val_password_min', { n: 3 }),
          max: this.$t('login_val_password_max', { n: 50 }),
        },
      }
      return rules[field][error.rule] || error.msg
    },
    alertFor(error) {
      const response = error && error.response
      if (response) {
        if (response.status === 429) return ALERTS.throttle
        if (response.status === 401 || response.status === 422) return ALERTS.credentials
        return ALERTS.server
      }
      // Axios errors without a response never reached the server
      if (error && (error.isAxiosError || error.request)) return ALERTS.network
      // The store rejects with a plain object when the API returns no user
      return ALERTS.credentials
    },
    checkLogin() {
      if (this.$store.state.auth.isUserLoggedIn()) {
        this.alert = ALERTS.loggedIn
        return false
      }
      return true
    },
    loginJWT() {
      if (!this.validateForm || this.loading) return
      if (!this.checkLogin()) return

      this.alert = null
      this.loading = true

      const payload = {
        checkbox_remember_me: this.checkbox_remember_me,
        userDetails: {
          email: this.email,
          password: this.password
        }
      }

      this.$store.dispatch('auth/loginJWT', payload)
        .then((res) => {
          this.loading = false
          if (res.data.accessToken) {
            const user = res.data.userData || {}
            this.$vs.notify({
              title: this.$t('login_success_title'),
              text: this.$t('login_success_text', { name: user.username || user.name || user.email || '' }),
              iconPack: 'feather',
              icon: 'icon-check-circle',
              color: 'success',
              position: 'top-right',
              time: 3000
            })
            this.retireveRoleMenu();
          }
        }).catch((error) => {
          this.loading = false
          this.alert = this.alertFor(error)
        })
    },
    retireveRoleMenu() {
      const params = {
        sort: "id",
        order: "desc",
        page_number: 1,
        limit : 300
      };

      // This page is already navigated away from, so report failures as a toast
      return axios.post(this.api, params)
        .then((response) => {
          localStorage.setItem("role_menus",  JSON.stringify(response.data.permissions));
        }).catch(() => {
          this.$vs.notify({
            title: this.$t('login_err_title_permission'),
            text: this.$t('login_err_permission'),
            iconPack: 'feather',
            icon: 'icon-alert-circle',
            color: 'danger',
            position: 'top-right'
          })
        })
    },
  }
}

</script>

<style lang="scss">
.login-jwt {
  .login-field {
    margin-bottom: 16px;

    label {
      display: block;
      font-size: .85rem;
      font-weight: 500;
      color: #5e6078;
      margin-bottom: 6px;
    }

    &__control {
      position: relative;
    }

    // Wrapper span (not the feather-icon itself): Tailwind's `.relative` is
    // !important and would otherwise un-pin the icon.
    &__icon {
      position: absolute;
      top: 0;
      bottom: 0;
      left: 12px;
      display: flex;
      align-items: center;
      color: #a3a5bb;
      pointer-events: none;
      transition: color .15s;

      .feather-icon {
        display: flex;
      }
    }

    input {
      display: block;
      width: 100%;
      height: 42px;
      padding: 0 40px 0 38px;
      font-size: .95rem;
      font-family: inherit;
      color: #2c2e45;
      background: #fff;
      border: 1px solid #d8d9e3;
      border-radius: 8px;
      outline: none;
      transition: border-color .15s, box-shadow .15s;

      &::placeholder {
        color: #b4b6c8;
      }

      &:hover {
        border-color: #c3c5d4;
      }

      &:focus {
        border-color: rgb(var(--vs-primary));
        box-shadow: 0 0 0 3px rgba(var(--vs-primary), .15);
      }
    }

    &__control:focus-within &__icon {
      color: rgb(var(--vs-primary));
    }

    &__toggle {
      position: absolute;
      top: 50%;
      right: 6px;
      transform: translateY(-50%);
      width: 30px;
      height: 30px;
      padding: 0;
      border: none;
      background: transparent;
      color: #a3a5bb;
      cursor: pointer;
      display: flex;
      align-items: center;
      justify-content: center;
      border-radius: 6px;

      .feather-icon {
        display: flex;
      }

      &:hover {
        color: #5e6078;
        background: #f1f2f6;
      }
    }

    &__error {
      display: block;
      margin-top: 4px;
      font-size: .78rem;
      color: rgb(var(--vs-danger));
    }

    &.has-error {
      input {
        border-color: rgb(var(--vs-danger));
        &:focus {
          box-shadow: 0 0 0 3px rgba(var(--vs-danger), .15);
        }
      }
      .login-field__icon {
        color: rgb(var(--vs-danger));
      }
    }
  }

  .login-submit {
    width: 100%;
    height: 42px;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    font-size: .95rem;
    font-weight: 500;
    font-family: inherit;
    color: #fff;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    background: rgb(var(--vs-primary));
    transition: filter .15s, box-shadow .15s, opacity .15s;

    &:hover:not(:disabled) {
      filter: brightness(.95);
      box-shadow: 0 4px 12px -4px rgba(var(--vs-primary), .6);
    }

    &:disabled {
      opacity: .6;
      cursor: not-allowed;
    }

    &__spinner {
      width: 16px;
      height: 16px;
      border-radius: 50%;
      border: 2px solid rgba(255, 255, 255, .4);
      border-top-color: #fff;
      animation: login-spin .7s linear infinite;
    }
  }
}

.theme-dark .login-jwt {
  .login-field label {
    color: #c2c6dc;
  }
  .login-field input {
    color: #fff;
    background: #10163a;
    border-color: #414561;
  }
  .login-field__toggle:hover {
    background: #343a5c;
  }
}

@keyframes login-spin {
  to { transform: rotate(360deg); }
}

.login-alert {
  --alert-color: var(--vs-danger);
  display: flex;
  align-items: flex-start;
  gap: 10px;
  padding: 12px 12px 12px 14px;
  margin-bottom: 20px;
  border-radius: 8px;
  border: 1px solid rgba(var(--alert-color), .25);
  border-left: 3px solid rgb(var(--alert-color));
  background: rgba(var(--alert-color), .07);

  &--warning {
    --alert-color: var(--vs-warning);
  }

  &__icon {
    display: flex;
    flex-shrink: 0;
    margin-top: 2px;
    color: rgb(var(--alert-color));

    .feather-icon {
      display: flex;
    }
  }

  &__body {
    flex: 1;
    min-width: 0;
  }

  &__title {
    font-size: .875rem;
    font-weight: 600;
    color: #2c2e45;
    line-height: 1.5;
  }

  &__text {
    font-size: .8rem;
    color: #5e6078;
    line-height: 1.6;
    margin-top: 2px;
  }

  &__close {
    flex-shrink: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    width: 22px;
    height: 22px;
    padding: 0;
    border: none;
    border-radius: 6px;
    background: transparent;
    color: #8a8ca5;
    cursor: pointer;

    .feather-icon {
      display: flex;
    }

    &:hover {
      background: rgba(var(--alert-color), .12);
      color: rgb(var(--alert-color));
    }
  }
}

.login-alert-enter-active,
.login-alert-leave-active {
  transition: opacity .2s, transform .2s;
}
.login-alert-enter,
.login-alert-leave-to {
  opacity: 0;
  transform: translateY(-6px);
}

// Shake the alert in when it appears so a repeated failure is still noticed
.login-alert-enter-active {
  animation: login-shake .35s ease;
}

@keyframes login-shake {
  20%, 60% { transform: translateX(-4px); }
  40%, 80% { transform: translateX(4px); }
}

.theme-dark .login-alert {
  &__title { color: #fff; }
  &__text { color: #c2c6dc; }
}
</style>

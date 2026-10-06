<template>
  <div class="dt-list user-form d-form">
    <div class="user-form__section">
      <feather-icon icon="LockIcon" svgClasses="h-4 w-4" />
      <span>{{ $t('profile_tab_password') }}</span>
    </div>
    <p class="user-form__hint">{{ $t('profile_password_rule') }}</p>

    <div class="vx-row">
      <div :class="grid" class="d-field">
        <label>{{ $t('profile_username') }}</label>
        <input :value="profile && profile.username" type="text" readonly>
      </div>

      <div :class="grid" class="d-field">
        <label>{{ $t('profile_password_new') }} <span>*</span></label>
        <vs-input v-model="password" type="password" name="password" autocomplete="new-password" class="w-full"
          v-validate="'required|min:6'" />
        <span class="text-danger" v-show="errors.has('password')">{{ $t('profile_password_short') }}</span>
      </div>

      <div :class="grid" class="d-field">
        <label>{{ $t('profile_password_confirm') }} <span>*</span></label>
        <vs-input v-model="confirmPassword" type="password" name="confirm_password" autocomplete="new-password"
          class="w-full" v-validate="'required'" />
        <span class="text-danger" v-show="mismatch">{{ $t('profile_password_mismatch') }}</span>
      </div>
    </div>

    <div class="user-form__actions">
      <button type="button" class="dt-btn dt-btn--primary" :disabled="saving || !profile" @click="save">
        <feather-icon :icon="saving ? 'LoaderIcon' : 'SaveIcon'" svgClasses="h-4 w-4" />
        <span>{{ $t('profile_password_change') }}</span>
      </button>
    </div>
  </div>
</template>

<script>
import axios from "@/axios.js"
import apiConfig from "@/apiConfig.js"
import { gridClass } from "@/formGrid.js"

export default {
  props: {
    profile: { type: Object, default: null },
    userId: { type: [String, Number], required: true },
  },
  data() {
    return {
      grid: gridClass('3grid'),
      password: '',
      confirmPassword: '',
      saving: false,
      touched: false,
    }
  },
  computed: {
    // Shown once the user tries to save (or both fields are filled)
    mismatch() {
      return (this.touched || this.confirmPassword.length > 0) && this.password !== this.confirmPassword
    },
  },
  methods: {
    async save() {
      this.touched = true
      if (!(await this.$validator.validateAll()) || this.password !== this.confirmPassword) return
      // Same list-of-fields shape as the general form; the API checks password === password_confirmation
      const payload = [
        { username: this.profile.username },
        { email: this.profile.email },
        { password: this.password },
        { password_confirmation: this.confirmPassword },
      ]
      this.saving = true
      axios.put(`${apiConfig._apiUser}/${this.userId}`, payload)
        .then(({ data }) => {
          const ok = data && data.success !== false
          const message = data && data.message
          const text = message && typeof message === 'object' ? Object.values(message).flat().join(' ') : message
          this.$vs.notify({ title: 'Message', text, iconPack: 'feather', icon: 'icon-check-circle', color: ok ? 'primary' : 'danger' })
          if (ok) {
            this.password = ''
            this.confirmPassword = ''
            this.touched = false
            this.$nextTick(() => this.$validator.reset())
          }
        })
        .catch(() => {
          this.$vs.notify({ title: 'Message', text: 'msg.action_failed', iconPack: 'feather', icon: 'icon-alert-circle', color: 'danger' })
        })
        .finally(() => { this.saving = false })
    },
  },
}
</script>

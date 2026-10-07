<template>
  <div class="dt-list user-form d-form">
    <div class="user-form__section">
      <feather-icon icon="UserIcon" svgClasses="h-4 w-4" />
      <span>{{ $t('profile_section_info') }}</span>
    </div>

    <div class="vx-row">
      <!-- Photo -->
      <div v-if="userId" :class="grid" class="d-field">
        <label>{{ $t('profile_photo') }} <span class="badge">{{ $t('form_optional') }}</span></label>
        <d-file-picker name="avatar" :value="form.avatar || ''" @browse="$refs.fileManager.initFileManager('avatar')"
          @clear="form.avatar = ''" />
      </div>

      <div :class="grid" class="d-field">
        <label>{{ $t('profile_fullname') }} <span>*</span></label>
        <vs-input v-model="form.fullname" name="fullname" autocomplete="off" class="w-full" v-validate="'required'" />
        <span class="text-danger" v-show="errors.has('fullname')">{{ $t('profile_required') }}</span>
      </div>

      <div :class="grid" class="d-field">
        <label>{{ $t('profile_latin') }} <span>*</span></label>
        <vs-input v-model="form.latin" name="latin" autocomplete="off" class="w-full" v-validate="'required'" />
        <span class="text-danger" v-show="errors.has('latin')">{{ $t('profile_required') }}</span>
      </div>

      <div v-for="field in selects" :key="field.key" :class="grid" class="d-field">
        <label>{{ $t(field.label) }} <span>*</span></label>
        <v-select v-model="form[field.key]" :name="field.key" :options="options[field.options] || []"
          :dir="$vs.rtl ? 'rtl' : 'ltr'" v-validate="'required'" />
        <span class="text-danger" v-show="errors.has(field.key)">{{ $t('profile_required') }}</span>
      </div>

      <div :class="grid" class="d-field">
        <label>{{ $t('profile_phone') }}</label>
        <vs-input v-model="form.telephone_1" name="telephone_1" class="w-full" />
      </div>

      <div :class="grid" class="d-field">
        <label>{{ $t('profile_email') }}</label>
        <vs-input v-model="form.email" type="email" name="email" class="w-full" v-validate="'email'" />
        <span class="text-danger" v-show="errors.has('email')">{{ $t('profile_email_invalid') }}</span>
      </div>

      <!-- New user only: password -->
      <template v-if="!userId">
        <div :class="grid" class="d-field">
          <label>{{ $t('profile_password') }} <span>*</span></label>
          <vs-input v-model="form.password" type="password" name="password" autocomplete="new-password" class="w-full"
            v-validate="'required'" />
          <span class="text-danger" v-show="errors.has('password')">{{ $t('profile_required') }}</span>
        </div>
        <div :class="grid" class="d-field">
          <label>{{ $t('profile_password_confirm') }} <span>*</span></label>
          <vs-input v-model="form.confirmPassword" type="password" name="confirmPassword" autocomplete="new-password"
            class="w-full" v-validate="'required'" />
          <span class="text-danger" v-show="form.confirmPassword && form.password !== form.confirmPassword">{{ $t('profile_password_mismatch') }}</span>
        </div>
      </template>
    </div>

    <div class="user-form__actions">
      <button type="button" class="dt-btn dt-btn--primary" :disabled="saving || loading" @click="save">
        <feather-icon :icon="saving ? 'LoaderIcon' : 'SaveIcon'" svgClasses="h-4 w-4" />
        <span>{{ $t('perm_save') }}</span>
      </button>
    </div>

    <d-file-manager ref="fileManager" @clicked="onFilePicked" />
  </div>
</template>

<script>
import vSelect from 'vue-select'
import axios from "@/axios.js"
import apiConfig from "@/apiConfig.js"
import DFileManager from '@/views/form-builder/DFileManager.vue'
import { gridClass } from "@/formGrid.js"

// Option values are stored as text on the user (title, position, department, role); entity by id
const asOption = (value, label = value) => (value === null || value === undefined || value === '' ? null : { label, value })

export default {
  components: {
    'v-select': vSelect,
    DFileManager,
  },
  props: {
    profile: { type: Object, default: null },
    options: { type: Object, default: () => ({}) },
    loading: { type: Boolean, default: false },
    userId: { type: [String, Number], default: null },
  },
  data() {
    return {
      grid: gridClass('3grid'),
      saving: false,
      form: this.blankForm(),
      selects: [
        { key: 'title', label: 'profile_title', options: 'titles' },
        { key: 'department', label: 'profile_department', options: 'departments' },
        { key: 'entity', label: 'profile_entity', options: 'entities' },
        { key: 'position', label: 'profile_position', options: 'positions' },
        { key: 'role', label: 'profile_role', options: 'roles' },
      ],
    }
  },
  watch: {
    profile: {
      immediate: true,
      handler(profile) {
        if (!profile) return
        const entity = profile.entity && typeof profile.entity === 'object' ? profile.entity : null
        this.form = {
          ...this.blankForm(),
          fullname: profile.fullname || '',
          latin: profile.latin || '',
          email: profile.email || '',
          username: profile.username || '',
          telephone_1: profile.telephone_1 || '',
          telephone_2: profile.telephone_2 || '',
          avatar: profile.avatar || '',
          title: asOption(profile.title),
          department: asOption(profile.department),
          position: asOption(profile.position),
          role: asOption(profile.role),
          entity: entity ? asOption(entity.id, [entity.code, entity.name_kh].filter(Boolean).join('-')) : null,
        }
        this.$nextTick(() => this.$validator.reset())
      },
    },
  },
  methods: {
    blankForm() {
      return {
        fullname: '', latin: '', email: '', username: '', telephone_1: '', telephone_2: '', avatar: '',
        title: null, department: null, position: null, role: null, entity: null, password: '', confirmPassword: '',
      }
    },
    onFilePicked({ data, props }) {
      if (props === 'avatar') this.form.avatar = data.path
    },
    label(option) {
      return option && typeof option === 'object' ? option.label : option
    },
    value(option) {
      return option && typeof option === 'object' ? option.value : option
    },
    async save() {
      if (!(await this.$validator.validateAll())) return
      const f = this.form
      if (!this.userId && f.password !== f.confirmPassword) return
      // The API expects a list of single-field objects
      const payload = [
        { latin: f.latin },
        { username: f.username },
        { email: f.email },
        { fullname: f.fullname },
        { title: this.label(f.title) },
        { position: this.label(f.position) },
        { role: this.value(f.role) },
        { department: this.label(f.department) },
        { entity_id: this.value(f.entity) },
        { telephone_1: f.telephone_1 },
        { telephone_2: f.telephone_2 },
      ]
      if (this.userId) payload.push({ avatar: f.avatar || null })
      else payload.push({ password: f.password }, { password_confirmation: f.confirmPassword })

      this.saving = true
      const request = this.userId
        ? axios.put(`${apiConfig._apiUser}/${this.userId}`, payload)
        : axios.post('/api/auth/signup', payload)
      request
        .then(({ data }) => {
          const ok = data && data.success !== false
          this.$vs.notify({ title: 'Message', text: this.messageOf(data), iconPack: 'feather', icon: 'icon-check-circle', color: ok ? 'primary' : 'danger' })
          if (ok) this.$emit('saved')
        })
        .catch(() => {
          this.$vs.notify({ title: 'Message', text: 'msg.action_failed', iconPack: 'feather', icon: 'icon-alert-circle', color: 'danger' })
        })
        .finally(() => { this.saving = false })
    },
    // Validation errors come back as { field: [messages] }
    messageOf(data) {
      const message = data && data.message
      if (message && typeof message === 'object') return Object.values(message).flat().join(' ')
      return message
    },
  },
}
</script>

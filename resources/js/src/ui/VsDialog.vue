<template>
  <teleport to="body">
    <transition name="dialog-t">
      <div v-if="shown" ref="con" v-bind="$attrs" class="vs-component con-vs-dialog" :class="['vs-dialog-' + color]">
        <div class="vs-dialog-dark" @click="clickBackground" />
        <div ref="dialogx" class="vs-dialog">
          <header class="vs-dialog-header" :style="{ color: getColor(color) }">
            <div class="con-title-after">
              <span class="after" :style="{ background: getColor(color) }" />
              <h3 class="dialog-title">{{ title }}</h3>
            </div>
            <vs-icon v-if="type == 'alert'" class="vs-dialog-cancel vs-dialog-cancel--icon notranslate" :icon="closeIcon" :icon-pack="iconPack" @click="closeAlert" />
          </header>
          <div class="vs-dialog-text"><slot />{{ text }}</div>
          <footer v-if="buttonsHidden ? false : isPrompt || type == 'confirm'">
            <vs-button class="vs-dialog-accept-button" :disabled="isValid == 'none' ? false : !isValid" :color="color" :type="buttonAccept" @click="acceptDialog">{{ acceptText }}</vs-button>
            <vs-button class="vs-dialog-cancel-button" text-color="rgba(0,0,0,.5)" :type="buttonCancel" @click="cancelClose">{{ cancelText }}</vs-button>
          </footer>
          <footer v-if="type == 'alert' && !isPrompt">
            <vs-button class="vs-dialog-accept-button" :color="color" :type="buttonAccept" @click="acceptDialog">{{ acceptText }}</vs-button>
          </footer>
        </div>
      </div>
    </transition>
  </teleport>
</template>

<script>
import { color as vsColor } from './utils'

// <vs-prompt :active.sync> in templates, and the box behind $vs.dialog({ ... }) (see vs.js)
export default {
  name: 'VsPrompt',
  inheritAttrs: false,
  emits: ['update:active', 'accept', 'cancel', 'cancle', 'close'],
  props: {
    color: { default: 'primary', type: String },
    active: { default: false, type: Boolean },
    buttonAccept: { default: 'filled', type: String },
    buttonCancel: { default: 'flat', type: String },
    isValid: { default: 'none', type: [Boolean, String] },
    buttonsHidden: { default: false, type: Boolean },
    acceptText: { default: 'Accept', type: String },
    cancelText: { default: 'Cancel', type: String },
    iconPack: { default: 'material-icons', type: String },
    closeIcon: { default: 'close', type: String },
    text: { default: null, type: String },
    title: { default: 'Dialog', type: String },
    type: { default: 'alert', type: String },
    // $vs.dialog: not a prompt; opened once, with these callbacks
    isPrompt: { default: true, type: Boolean },
    accept: { type: Function, default: null },
    cancel: { type: Function, default: null },
    parameters: { default: null },
  },
  data() {
    return { fActive: false }
  },
  computed: {
    shown() {
      return this.isPrompt ? this.active : this.fActive
    },
  },
  mounted() {
    if (!this.isPrompt) this.$nextTick(() => { this.fActive = true })
  },
  methods: {
    getColor(value) {
      return vsColor.getColor(value, 1)
    },
    hide() {
      this.fActive = false
      this.$emit('update:active', false)
    },
    acceptDialog() {
      if (this.isPrompt && !(this.isValid || this.isValid == 'none')) return
      if (this.accept) this.accept(this.parameters)
      this.hide()
      this.$emit('accept', this.parameters)
    },
    cancelClose() {
      this.hide()
      this.$emit('cancel')
      this.$emit('cancle') // the template's spelling, still listened to in DTableList
      if (this.cancel) this.cancel(this.parameters)
    },
    closeAlert() {
      this.hide()
      this.$emit('close')
    },
    // An alert closes on a background click; a confirm/prompt shakes instead
    clickBackground() {
      if (this.type == 'alert') {
        this.hide()
      } else if (this.$refs.dialogx) {
        this.$refs.dialogx.classList.add('locked')
        setTimeout(() => this.$refs.dialogx && this.$refs.dialogx.classList.remove('locked'), 200)
      }
      this.$emit('close')
    },
  },
}
</script>

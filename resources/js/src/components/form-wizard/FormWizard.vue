<!-- =========================================================================================
  Step wizard replacing vue-form-wizard (Vue 2 only), with the same props, events and HTML classes,
  so the templates and vue-form-wizard.min.css work unchanged:
    <form-wizard color="..." errorColor="..." :title="null" :subtitle="null" finishButtonText="Submit"
                 @on-complete="save" @on-change="(prev, next) => ...">
      <tab-content title="..." icon="feather icon-home" :before-change="validateStep1"> ... </tab-content>
    </form-wizard>
  before-change may return true/false or a Promise; a false/rejected step shows in errorColor and stays.
========================================================================================== -->
<template>
  <div class="vue-form-wizard" :class="stepSize">
    <div class="wizard-header" v-if="title || subtitle || $slots.title">
      <slot name="title">
        <h4 class="wizard-title">{{ title }}</h4>
        <p class="category">{{ subtitle }}</p>
      </slot>
    </div>

    <div class="wizard-navigation">
      <div class="wizard-progress-with-circle">
        <div class="wizard-progress-bar" :style="{ backgroundColor: color, color, width: progress + '%' }"></div>
      </div>
      <ul class="wizard-nav wizard-nav-pills" role="tablist">
        <li v-for="(tab, index) in tabs" :key="tab.uid" :class="{ active: index === activeIndex }">
          <a href="javascript:void(0)" :class="{ disabled: index > maxStep }" @click="navigateTo(index)">
            <div class="wizard-icon-circle md" role="tab" :class="{ checked: index <= maxStep, square_shape: shape === 'square', tab_shape: shape === 'tab' }"
                 :style="[index <= maxStep ? { borderColor: color } : {}, tab.error ? errorStyle : {}]">
              <transition name="wizard-icon">
                <div v-if="index === activeIndex" class="wizard-icon-container"
                     :class="{ square_shape: shape === 'square', tab_shape: shape === 'tab' }"
                     :style="[{ backgroundColor: color }, tab.error ? errorStyle : {}]">
                  <i v-if="tab.icon" class="wizard-icon" :class="tab.icon"></i>
                  <i v-else class="wizard-icon">{{ index + 1 }}</i>
                </div>
              </transition>
              <template v-if="index !== activeIndex">
                <i v-if="tab.icon" class="wizard-icon" :class="tab.icon"></i>
                <i v-else class="wizard-icon">{{ index + 1 }}</i>
              </template>
            </div>
            <span class="stepTitle" :class="{ active: index === activeIndex, has_error: tab.error }"
                  :style="index === activeIndex ? { color: tab.error ? errorColor : color } : {}">{{ tab.title }}</span>
          </a>
        </li>
      </ul>
      <div class="wizard-tab-content">
        <slot />
      </div>
    </div>

    <div class="wizard-card-footer clearfix" v-if="!hideButtons">
      <div class="wizard-footer-left">
        <span v-if="activeIndex > 0" role="button" tabindex="0" @click="prevTab">
          <button type="button" class="wizard-btn" tabindex="-1" :style="buttonStyle">{{ backButtonText }}</button>
        </span>
      </div>
      <div class="wizard-footer-right">
        <span role="button" tabindex="0" @click="nextTab">
          <button v-if="isLastStep" type="button" class="wizard-btn wizard-footer-right finish-button" tabindex="-1" :style="buttonStyle">{{ finishButtonText }}</button>
          <button v-else type="button" class="wizard-btn wizard-footer-right" tabindex="-1" :style="buttonStyle">{{ nextButtonText }}</button>
        </span>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'FormWizard',
  provide() {
    return {
      wizard: {
        register: tab => { this.tabs.push(tab); this.sortTabs() },
        unregister: tab => { this.tabs = this.tabs.filter(t => t !== tab) },
        isActive: tab => this.tabs[this.activeIndex] === tab,
      },
    }
  },
  props: {
    title: { type: String, default: 'Awesome Wizard' },
    subtitle: { type: String, default: 'Split a complicated flow in multiple steps' },
    nextButtonText: { type: String, default: 'Next' },
    backButtonText: { type: String, default: 'Back' },
    finishButtonText: { type: String, default: 'Finish' },
    hideButtons: { type: Boolean, default: false },
    color: { type: String, default: '#e74c3c' },
    errorColor: { type: String, default: '#8b0000' },
    shape: { type: String, default: 'circle' },
    stepSize: { type: String, default: 'md' },
    startIndex: { type: Number, default: 0 },
  },
  emits: ['on-complete', 'on-change', 'on-loading', 'on-validate', 'on-error'],
  data() {
    return { tabs: [], activeIndex: this.startIndex, maxStep: this.startIndex }
  },
  computed: {
    isLastStep() {
      return this.activeIndex === this.tabs.length - 1
    },
    progress() {
      return this.tabs.length ? ((this.activeIndex + 1) * 100) / this.tabs.length : 0
    },
    errorStyle() {
      return { borderColor: this.errorColor, backgroundColor: this.errorColor }
    },
    buttonStyle() {
      return { backgroundColor: this.color, borderColor: this.color, color: 'white' }
    },
  },
  methods: {
    // Tabs register as they mount; keep them in the order they appear on the page
    sortTabs() {
      this.$nextTick(() => {
        const order = [...this.$el.querySelectorAll('.wizard-tab-container')]
        this.tabs.sort((a, b) => order.indexOf(a.el()) - order.indexOf(b.el()))
      })
    },
    // Runs the current step's before-change; resolves true when the wizard may leave it
    async validateCurrent() {
      const tab = this.tabs[this.activeIndex]
      if (!tab || !tab.beforeChange) return true
      this.$emit('on-loading', true)
      try {
        const ok = (await tab.beforeChange()) !== false
        tab.error = !ok
        this.$emit('on-validate', ok, this.activeIndex)
        return ok
      } catch (error) {
        tab.error = true
        this.$emit('on-error', error)
        this.$emit('on-validate', false, this.activeIndex)
        return false
      } finally {
        this.$emit('on-loading', false)
      }
    },
    changeTab(index) {
      if (index === this.activeIndex) return
      this.$emit('on-change', this.activeIndex, index)
      this.activeIndex = index
      this.maxStep = Math.max(this.maxStep, index)
    },
    async nextTab() {
      if (!(await this.validateCurrent())) return
      if (this.isLastStep) this.$emit('on-complete')
      else this.changeTab(this.activeIndex + 1)
    },
    prevTab() {
      if (this.activeIndex > 0) this.changeTab(this.activeIndex - 1)
    },
    // Steps already reached can be clicked; going forward still checks the current step first
    async navigateTo(index) {
      if (index > this.maxStep) return
      if (index > this.activeIndex && !(await this.validateCurrent())) return
      this.changeTab(index)
    },
    reset() {
      this.tabs.forEach(tab => { tab.error = false })
      this.activeIndex = 0
      this.maxStep = 0
    },
  },
}
</script>

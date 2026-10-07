<!-- One step of <form-wizard> (see FormWizard.vue) -->
<template>
  <div v-show="active" ref="el" class="wizard-tab-container" role="tabpanel">
    <slot :active="active" />
  </div>
</template>

<script>
export default {
  name: 'TabContent',
  inject: ['wizard'],
  props: {
    title: { type: String, default: '' },
    icon: { type: String, default: '' },
    beforeChange: { type: Function, default: null },
  },
  data() {
    // Reactive record the wizard reads for the step's title, icon, check and error state
    return {
      tab: {
        uid: Symbol('tab'),
        title: this.title,
        icon: this.icon,
        beforeChange: this.beforeChange,
        error: false,
        el: () => this.$refs.el,
      },
    }
  },
  computed: {
    active() {
      return this.wizard.isActive(this.tab)
    },
  },
  watch: {
    title(value) { this.tab.title = value },
    icon(value) { this.tab.icon = value },
    beforeChange(value) { this.tab.beforeChange = value },
  },
  mounted() {
    this.wizard.register(this.tab)
  },
  beforeUnmount() {
    this.wizard.unregister(this.tab)
  },
}
</script>

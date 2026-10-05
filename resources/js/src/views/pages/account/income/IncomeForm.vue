<template>
  <vs-tabs :position="isSmallerScreen ? 'top' : 'left'" class="tabs-shadow-none" id="profile-tabs" :key="isSmallerScreen">

    <!-- GENERAL -->
    <!-- <vs-tab icon-pack="feather" icon="icon-user" :label="!isSmallerScreen ? 'ពត៌មានទូទៅ' : ''">
      <div class="tab-general md:ml-4 md:mt-0 mt-4 ml-0">
        <income-general />
      </div>

    </vs-tab> -->
    <vs-tab icon-pack="feather" icon="icon-lock" :label="!isSmallerScreen ? 'Generate តាម File' : ''">
      <div class="tab-change-pwd md:ml-4 md:mt-0 mt-4 ml-0">
        <income-upload />
      </div>
    </vs-tab>
  </vs-tabs>
</template>

<script>
import IncomeGeneral from "./IncomeGeneral.vue";
import IncomeUpload from "./IncomeUpload.vue";
export default {
  components: {
    IncomeGeneral,
    IncomeUpload
  },
  data() {
    return {
      
    }
  },
  computed: {
    isSmallerScreen() {
      return this.$store.state.windowWidth < 768
    }
  }
}
</script>

<style lang="scss">
#profile-tabs {
  .vs-tabs--content {
    padding: 0;
  }
}
</style>

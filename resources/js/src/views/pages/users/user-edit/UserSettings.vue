<template>
  <vs-tabs :position="isSmallerScreen ? 'top' : 'left'" class="tabs-shadow-none" id="profile-tabs" :key="isSmallerScreen">

    <!-- GENERAL -->
    <vs-tab icon-pack="feather" icon="icon-user" :label="!isSmallerScreen ? 'ពត៌មានទូទៅ' : ''">
      <div class="tab-general md:ml-4 md:mt-0 mt-4 ml-0">
        <user-settings-general />
      </div>
    </vs-tab>
    <vs-tab v-if="$route.params.userId" icon-pack="feather" icon="icon-lock" :label="!isSmallerScreen ? 'ប្តូរលេខសម្ងាត់' : ''">
      <div class="tab-change-pwd md:ml-4 md:mt-0 mt-4 ml-0">
        <user-settings-change-password />
      </div>
    </vs-tab>
    <!-- <vs-tab v-if="$route.params.userId" icon-pack="feather" icon="icon-info" :label="!isSmallerScreen ? 'ពត៌មានបន្ថែម' : ''">
      <div class="tab-info md:ml-4 md:mt-0 mt-4 ml-0">
        <user-settings-info />
      </div>
    </vs-tab> -->
    <!-- <vs-tab v-if="$route.params.userId" icon-pack="feather" icon="icon-github" :label="!isSmallerScreen ? 'បណ្តាញសង្គម' : ''">
      <div class="tab-social-links md:ml-4 md:mt-0 mt-4 ml-0">
        <user-settings-social-links />
      </div>
    </vs-tab>
    <vs-tab v-if="$route.params.userId" icon-pack="feather" icon="icon-bell" :label="!isSmallerScreen ? 'ជូនដំណឹង' : ''">
      <div class="tab-text md:ml-4 md:mt-0 mt-4 ml-0">
        <user-settings-notifications />
      </div>
    </vs-tab> -->
  </vs-tabs>
</template>

<script>
import UserSettingsGeneral from "./UserSettingsGeneral.vue"
import UserSettingsChangePassword from "./UserSettingsChangePassword.vue"
import UserSettingsInfo from "@/views/pages/settings/SettingInfo.vue"
import UserSettingsSocialLinks from "./UserSettingsSocialLinks.vue"
import UserSettingsNotifications from "./UserSettingsNotifications.vue"

export default {
  components: {
    UserSettingsGeneral,
    UserSettingsChangePassword,
    UserSettingsInfo,
    UserSettingsSocialLinks,
    UserSettingsNotifications,
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

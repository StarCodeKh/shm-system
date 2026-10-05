<template>
  <vs-tabs position="top" class="tabs-shadow-none" id="profile-tabs" :key="isSmallerScreen">

    <!-- 1 -->
    <vs-tab v-if="tab_general" icon-pack="feather" icon="icon-edit" :label="!isSmallerScreen ? 'ពត៌មានទូទៅ' : ''">
      <div class="tab-text md:ml-4 md:mt-0 mt-4 ml-0 pt-2">
        <setting-general />
      </div>
    </vs-tab>

    <!-- 3 -->
    <vs-tab v-if="tab_roles" icon-pack="feather" icon="icon-lock" :label="!isSmallerScreen ? 'អ្នកប្រើប្រាស់ និងសិទ្ធ' : ''">
      <div class="tab-text md:ml-4 md:mt-0 mt-4 ml-0 pt-2">
        <setting-role />
      </div>
    </vs-tab>

    <!-- 4 -->
    <vs-tab v-if="tab_account_admin" icon-pack="feather" icon="icon-settings" :label="!isSmallerScreen ? 'គណនេយ្យ និងរដ្ឋបាល' : ''">
      <div class="tab-text md:ml-4 md:mt-0 mt-4 ml-0 pt-2">
        <setting-account-admin />
      </div>
    </vs-tab>

    <!-- 5 -->
    <vs-tab v-if="tab_budget_info" icon-pack="feather" icon="icon-edit" :label="!isSmallerScreen ? 'ព័ត៌មានរៀបចំថវិកា' : ''">
      <div class="tab-text md:ml-4 md:mt-0 mt-4 ml-0 pt-2">
        <setting-budget-arrangement />
      </div>
    </vs-tab>

    <!-- 6 -->
    <vs-tab v-if="tab_entity" icon-pack="feather" icon="icon-user" :label="!isSmallerScreen ? 'អង្គភាព' : ''">
      <div class="tab-text md:ml-4 md:mt-0 mt-4 ml-0 pt-2">
        <vx-card>
          <setting-entity />
        </vx-card>
      </div>
    </vs-tab>

    <!-- 2 -->
    <!-- <vs-tab icon-pack="feather" icon="icon-users" :label="!isSmallerScreen ? 'រចនាសម្ព័ន្ធ' : ''">
        <div class="tab-text md:ml-4 md:mt-0 mt-4 ml-0 pt-2">
          <setting-structure />
        </div>
      </vs-tab> -->

    <!-- 6 -->
    <vs-tab v-if="tab_back_up" icon-pack="feather" icon="icon-calendar" :label="!isSmallerScreen ? 'រក្សាទុកទិន្នន័យ' : ''">
      <div class="tab-text md:ml-4 md:mt-0 mt-4 ml-0 pt-2">
        <setting-backup />1
      </div>
    </vs-tab>

    <!-- 7 -->
    <vs-tab v-if="tab_notification" icon-pack="feather" icon="icon-bell" :label="!isSmallerScreen ? 'កំណត់ការជូនដំណឹង' : ''">
      <div class="tab-text md:ml-4 md:mt-0 mt-4 ml-0 pt-2">
        <setting-notifications />
      </div>
    </vs-tab>

    <!-- 8 -->
    <!-- <vs-tab icon-pack="feather" icon="icon-lock" :label="!isSmallerScreen ? 'ប្តូរលេខសម្ងាត់' : ''">
      <div class="tab-change-pwd md:ml-4 md:mt-0 mt-4 ml-0">
        <setting-change-password />
      </div>
    </vs-tab> -->

    <!-- <vs-tab icon-pack="feather" icon="icon-info" :label="!isSmallerScreen ? 'ពត៌មានបន្ថែម' : ''">
      <div class="tab-info md:ml-4 md:mt-0 mt-4 ml-0">
        <setting-info />
      </div>
    </vs-tab> -->

    <!-- <vs-tab icon-pack="feather" icon="icon-github" :label="!isSmallerScreen ? 'បណ្តាញសង្គម' : ''">
      <div class="tab-social-links md:ml-4 md:mt-0 mt-4 ml-0">
        <setting-social-links />
      </div>
    </vs-tab> -->

    <!-- 9 -->
    <vs-tab v-if="tab_autdit" icon-pack="feather" icon="icon-clock" :label="!isSmallerScreen ? 'កំណត់ហេតុ' : ''">
      <div class="tab-text md:ml-4 md:mt-0 mt-4 ml-0 pt-2">
        <setting-audit />
      </div>
    </vs-tab>

  </vs-tabs>
</template>

<script>
import axios from "@/axios.js";
import SettingGeneral from "./SettingGeneral.vue";
import SettingChangePassword from "./SettingChangePassword.vue";
import SettingBackup from "./SettingBackup.vue";
import SettingStructure from "./SettingStructure.vue";
import SettingInfo from "./SettingInfo.vue";
import SettingRole from "./SettingRole.vue";
import SettingAccountAdmin from "./SettingAccountAdmin.vue";
import SettingSocialLinks from "./SettingSocialLinks.vue";
import SettingNotifications from "./SettingNotifications.vue";
import SettingEntity from "./SettingEntity.vue";
import SettingBudgetArrangement from "./SettingBudgetArrangement.vue";
import SettingAudit from "./SettingAudit.vue";
import apiConfig from "../../../apiConfig";

export default {
  data() {
    return {
      api: apiConfig._apiRoleMenu,
      data: {
        data: [
          {}
        ],
        limit: 10,
        total: 3,
      },
      dataFields: [],
      dataInfo: {},
      isVisible: true,
      tab_account_admin: true,
      tab_roles: true,
      tab_general: true,
      tab_budget_info: true,
      tab_entity: true,
      tab_back_up: true,
      tab_notification: true,
      tab_autdit: true,
      roleMenuTab: ""
    }
  },
  components: {
    SettingGeneral,
    SettingChangePassword,
    SettingBackup,
    SettingAccountAdmin,
    SettingStructure,
    SettingInfo,
    SettingRole,
    SettingSocialLinks,
    SettingNotifications,
    SettingEntity,
    SettingBudgetArrangement,
    SettingAudit
  },
  methods: {
    getDataTable(_search_criteria) {
      let _params = {};
      if (_search_criteria.search_field) {
        this.dataFields = [];
        this.formAttributes.forEach(_formAttribute => {
          if (_search_criteria.search_field[_formAttribute["name"]]) {
            let _d = {
              [_formAttribute["name"]]: _search_criteria.search_field[_formAttribute["name"]]
            }
            this.dataFields.push(_d);
          }
        });
        _params = {
          sort: _search_criteria.sort,
          order: _search_criteria.order,
          page_number: _search_criteria.page_number,
          search_field: this.dataFields,
        };
      } else {
        _params = {
          sort: _search_criteria.sort,
          order: _search_criteria.order,
          page_number: 1,
        };
      }

      return new Promise(() => {
        axios.post(this.api, _params)
          .then((response) => {
            this.data = response.data;
            this.tab_account_admin = true;
            this.tab_roles = true;
            this.tab_general = true;
            this.tab_budget_info = true;
            this.tab_entity = true;
            this.tab_back_up = true;
            this.tab_notification = true;
            this.tab_autdit = true;
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
    getData() {
      let _search_criteria = {
        sort: "id",
        order: "",
        page_number: ""
      }
      this.getDataTable(_search_criteria);
    },
  },
  computed: {
    isSmallerScreen() {
      return this.$store.state.windowWidth < 768
    }
  },
  mounted() {

  },
  created() {
    this.$vs.loading();
    // Parse data from localStorage
    const stored = JSON.parse(localStorage.getItem("role_menus")) || [];
    if (stored) {
      // try {
      //   this.roleMenuTab = stored;
      //   this.tab_account_admin = !!this.roleMenuTab.list_account_adminstraion;
      //   this.tab_roles = !!this.roleMenuTab.list_roles;
      //   this.tab_general = !!this.roleMenuTab.list_general;
      //   this.tab_budget_info = !!this.roleMenuTab.list_budget_information;
      //   this.tab_entity = !!this.roleMenuTab.list_unit;
      //   this.tab_back_up = !!this.roleMenuTab.list_back_up;
      //   this.tab_notification = !!this.roleMenuTab.list_notification;
      //   this.tab_autdit = !!this.roleMenuTab.list_audit;
      //   console.log(this.tab_account_admin);
      // } catch (e) {
      //   this.tab_account_admin = false;
      //   this.tab_roles = false;
      //   console.error("Invalid localStorage format for role_menus_1", e);
      // }
    }
    this.$vs.loading.close();
  },
}
</script>

<style lang="scss">
  #profile-tabs {
    .vs-tabs--content {
      padding: 0;
    }
  }
</style>
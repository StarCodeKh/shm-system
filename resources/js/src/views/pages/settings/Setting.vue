<template>
  <div class="setting-page">

    <!-- Settings navigation -->
    <aside class="setting-nav">
      <div class="setting-nav__header">
        <span class="setting-tile setting-tile--brand">
          <feather-icon icon="SlidersIcon" svgClasses="h-5 w-5" />
        </span>
        <div class="min-w-0">
          <h3>{{ $t('setting_title') }}</h3>
          <p>{{ $t('setting_subtitle') }}</p>
        </div>
      </div>

      <nav class="setting-nav__list">
        <button v-for="(tab, i) in visibleTabs" :key="tab.key" type="button" class="setting-nav__item"
          :class="{ 'is-active': tab.key === activeKey }" :style="{ '--tile': tab.color, '--delay': (i * 40) + 'ms' }"
          @click="select(tab.key)">
          <span class="setting-tile">
            <feather-icon :icon="tab.icon" svgClasses="h-4 w-4" />
          </span>
          <span class="setting-nav__text">
            <span class="setting-nav__title">{{ $t(tab.title) }}</span>
            <span class="setting-nav__desc">{{ $t(tab.desc) }}</span>
          </span>
          <feather-icon icon="ChevronRightIcon" svgClasses="h-4 w-4" class="setting-nav__chevron" />
        </button>
      </nav>
    </aside>

    <!-- Active section; accents follow the section's color -->
    <section class="setting-content" :style="{ '--tile': activeTab ? activeTab.color : null }">
      <transition name="setting-swap" mode="out-in">
          <div v-if="activeTab" :key="activeTab.key" class="setting-content__inner">
            <div class="setting-content__head">
              <span class="setting-tile" :style="{ '--tile': activeTab.color }">
                <feather-icon :icon="activeTab.icon" svgClasses="h-5 w-5" />
              </span>
              <div>
                <h2>{{ $t(activeTab.title) }}</h2>
                <p>{{ $t(activeTab.desc) }}</p>
              </div>
            </div>

            <vx-card v-if="activeTab.card">
              <component :is="activeTab.component" />
            </vx-card>
            <component v-else :is="activeTab.component" />
          </div>
      </transition>
    </section>
  </div>
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
    select(key) {
      if (key === this.activeKey) return
      this.$router.replace({ query: { ...this.$route.query, tab: key } }).catch(() => {})
    },
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
    },
    // Sections in display order; `flag` is the permission switch kept in data()
    tabs() {
      return [
        { key: 'general', flag: this.tab_general, icon: 'SettingsIcon', color: '31, 32, 103', title: 'setting_tab_general', desc: 'setting_tab_general_desc', component: 'setting-general' },
        { key: 'roles', flag: this.tab_roles, icon: 'UsersIcon', color: '62, 91, 169', title: 'setting_tab_roles', desc: 'setting_tab_roles_desc', component: 'setting-role' },
        { key: 'account', flag: this.tab_account_admin, icon: 'BriefcaseIcon', color: '47, 164, 73', title: 'setting_tab_account', desc: 'setting_tab_account_desc', component: 'setting-account-admin' },
        { key: 'budget', flag: this.tab_budget_info, icon: 'PieChartIcon', color: '196, 150, 28', title: 'setting_tab_budget', desc: 'setting_tab_budget_desc', component: 'setting-budget-arrangement' },
        { key: 'entity', flag: this.tab_entity, icon: 'GridIcon', color: '31, 138, 122', title: 'setting_tab_entity', desc: 'setting_tab_entity_desc', component: 'setting-entity', card: true },
        { key: 'backup', flag: this.tab_back_up, icon: 'DatabaseIcon', color: '138, 106, 18', title: 'setting_tab_backup', desc: 'setting_tab_backup_desc', component: 'setting-backup' },
        { key: 'notification', flag: this.tab_notification, icon: 'BellIcon', color: '192, 57, 43', title: 'setting_tab_notification', desc: 'setting_tab_notification_desc', component: 'setting-notifications' },
        { key: 'audit', flag: this.tab_autdit, icon: 'ClockIcon', color: '92, 99, 120', title: 'setting_tab_audit', desc: 'setting_tab_audit_desc', component: 'setting-audit' },
      ]
    },
    visibleTabs() {
      return this.tabs.filter(tab => tab.flag)
    },
    // Active section comes from ?tab=… so it survives reloads and can be linked to
    activeKey() {
      const wanted = this.$route.query.tab
      return this.visibleTabs.some(tab => tab.key === wanted) ? wanted : (this.visibleTabs[0] || {}).key
    },
    activeTab() {
      return this.visibleTabs.find(tab => tab.key === this.activeKey)
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
.setting-page {
  display: flex;
  min-height: calc(100vh - 220px);
  border-radius: 16px;
  overflow: clip; // rounds the corners without breaking the sticky nav
  background: #fff;
  border: 1px solid rgba(23, 23, 74, .05);
  box-shadow: 0 4px 24px -8px rgba(23, 23, 74, .12);
}

// Colored icon square; --tile is an "r, g, b" triple
.setting-tile {
  --tile: 99, 102, 241;
  flex-shrink: 0;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  width: 36px;
  height: 36px;
  border-radius: 10px;
  color: rgb(var(--tile));
  background: rgba(var(--tile), .12);
  transition: transform .2s ease, background .2s ease, color .2s ease;

  .feather-icon {
    display: flex;
  }

  &--brand {
    width: 42px;
    height: 42px;
    color: #fff;
    background: linear-gradient(135deg, rgb(var(--brand-navy)), #3E5BA9);
    box-shadow: 0 8px 18px -8px rgba(var(--brand-navy), .8);
  }
}

// ---------------------------------------------------------------- Left navigation
.setting-nav {
  flex: 0 0 290px;
  border-right: 1px solid #eef0f5;
  background: #fbfbfd;

  &__header {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 20px 18px;
    border-bottom: 1px solid #eef0f5;

    h3 {
      font-size: 1.05rem;
      font-weight: 700;
      color: #2c2e45;
    }

    p {
      font-size: .78rem;
      color: #8a8ca5;
    }
  }

  &__list {
    position: sticky;
    top: 90px; // below the sticky navbar
    display: flex;
    flex-direction: column;
    gap: 2px;
    max-height: calc(100vh - 110px);
    padding: 10px;
    overflow-y: auto;
    scrollbar-width: thin;
  }

  &__item {
    position: relative;
    display: flex;
    align-items: center;
    gap: 12px;
    width: 100%;
    padding: 10px 12px;
    font-family: inherit;
    text-align: left;
    border: none;
    border-radius: 12px;
    background: transparent;
    cursor: pointer;
    animation: setting-item-in .35s ease both;
    animation-delay: var(--delay);
    transition: background .2s ease, box-shadow .2s ease;

    &::before {
      content: '';
      position: absolute;
      left: 0;
      top: 10px;
      bottom: 10px;
      width: 3px;
      border-radius: 3px;
      background: rgb(var(--tile));
      transform: scaleY(0);
      transition: transform .25s ease;
    }

    &:hover {
      background: #fff;
      box-shadow: 0 4px 14px -8px rgba(23, 23, 74, .25);

      .setting-tile {
        transform: scale(1.06);
      }
    }

    &.is-active {
      background: rgba(var(--tile), .09);
      box-shadow: none;

      &::before {
        transform: scaleY(1);
      }

      .setting-tile {
        color: #fff;
        background: rgb(var(--tile));
      }

      .setting-nav__title {
        color: #2c2e45;
        font-weight: 700;
      }

      .setting-nav__chevron {
        opacity: 1;
        transform: translateX(0);
        color: rgb(var(--tile));
      }
    }
  }

  &__text {
    display: flex;
    flex-direction: column;
    flex: 1;
    min-width: 0;
    line-height: 1.35;
  }

  &__title {
    font-size: .9rem;
    font-weight: 600;
    color: #3f4157;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
  }

  &__desc {
    font-size: .74rem;
    color: #9a9cb3;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
  }

  &__chevron {
    display: flex;
    color: #b4b6c8;
    opacity: 0;
    transform: translateX(-4px);
    transition: opacity .2s ease, transform .2s ease;
  }
}

// ---------------------------------------------------------------- Content
.setting-content {
  --tile: 31, 32, 103;
  position: relative;
  flex: 1;
  min-width: 0;
  padding: 24px 28px 28px;
  // Soft tint of the active section's color fading into the page background
  background:
    linear-gradient(180deg, rgba(var(--tile), .07), rgba(var(--tile), 0) 220px),
    #f7f8fb;
  transition: background .3s ease;

  // Thin accent bar along the top edge
  &::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 3px;
    background: linear-gradient(90deg, rgb(var(--tile)), rgba(var(--tile), .35));
    transition: background .3s ease;
  }

  &__head {
    display: flex;
    align-items: center;
    gap: 14px;
    margin-bottom: 20px;
    padding-bottom: 18px;
    border-bottom: 1px solid #e9ebf2;

    .setting-tile {
      width: 46px;
      height: 46px;
      border-radius: 12px;
      color: #fff;
      background: rgb(var(--tile));
      box-shadow: 0 8px 18px -10px rgba(var(--tile), .9);
    }

    h2 {
      font-size: 1.25rem;
      font-weight: 700;
      color: #2c2e45;
    }

    p {
      margin-top: 2px;
      font-size: .85rem;
      color: #8a8ca5;
    }
  }

  // Inner tabs and cards sit on white panels
  .vx-card,
  .vs-tabs {
    border-radius: 14px;
  }

  .vx-card {
    border: 1px solid #eceef4;
    box-shadow: 0 2px 10px -4px rgba(23, 23, 74, .08);
  }

  > .setting-content__inner > .vs-tabs,
  .setting-content__inner > div > .vs-tabs {
    padding: 8px 16px 16px;
    background: #fff;
    border: 1px solid #eceef4;
    box-shadow: 0 2px 10px -4px rgba(23, 23, 74, .08);
  }
}

.setting-swap-enter-active,
.setting-swap-leave-active {
  transition: opacity .25s ease, transform .25s ease;
}
.setting-swap-enter {
  opacity: 0;
  transform: translateY(12px);
}
.setting-swap-leave-to {
  opacity: 0;
  transform: translateY(-8px);
}

@keyframes setting-item-in {
  from { opacity: 0; transform: translateX(-8px); }
  to { opacity: 1; transform: none; }
}

// ---------------------------------------------------------------- Small screens: nav becomes a scrollable strip
@media (max-width: 900px) {
  .setting-page {
    flex-direction: column;
  }

  .setting-nav {
    flex: none;
    border-right: none;
    border-bottom: 1px solid #eef0f5;

    &__list {
      position: static;
      flex-direction: row;
      max-height: none;
      overflow-x: auto;
      overflow-y: hidden;
    }

    &__item {
      flex: 0 0 auto;
      width: auto;

      &::before {
        top: auto;
        bottom: 0;
        left: 12px;
        right: 12px;
        width: auto;
        height: 3px;
        transform: scaleX(0);
      }

      &.is-active::before {
        transform: scaleX(1);
      }
    }

    &__desc,
    &__chevron {
      display: none;
    }
  }

  .setting-content {
    padding: 16px;
  }
}

@media (prefers-reduced-motion: reduce) {
  .setting-nav__item {
    animation: none;
  }
}

.theme-dark {
  .setting-page { background: #262c49; border-color: #414561; }
  .setting-nav { background: #10163a; border-color: #414561; }
  .setting-nav__header { border-color: #414561; }
  .setting-nav__header h3,
  .setting-nav__item.is-active .setting-nav__title { color: #fff; }
  .setting-nav__title { color: #c2c6dc; }
  .setting-nav__item:hover { background: #262c49; }
  .setting-content {
    background: linear-gradient(180deg, rgba(var(--tile), .18), rgba(var(--tile), 0) 220px), #1b2142;
  }
  .setting-content__head { border-color: #414561; }
  .setting-content__head h2 { color: #fff; }
  .setting-content__head p { color: #a8abc4; }
}
</style>

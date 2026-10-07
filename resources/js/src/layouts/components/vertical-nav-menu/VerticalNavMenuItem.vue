<template>
  <div
    v-if="canSee"
    class="vs-sidebar--item"
    :class="[
      {'vs-sidebar-item-active'            : activeLink},
      {'disabled-item pointer-events-none' : isDisabled}
    ]" >

      <router-link
        tabindex="-1"
        v-if="to"
        exact
        :class="[{'router-link-active': activeLink}]"
        :to="to"
        :target="target" >
          <vs-icon v-if="!featherIcon" :icon-pack="iconPack" :icon="icon" />
          <feather-icon v-else :class="{'w-3 h-3': iconSmall}" :icon="icon" />
          <slot />
      </router-link>

      <a v-else :target="target" :href="href" tabindex="-1">
        <vs-icon v-if="!featherIcon" :icon-pack="iconPack" :icon="icon" />
        <feather-icon v-else :class="{'w-3 h-3': iconSmall}" :icon="icon" />
        <slot />
      </a>
  </div>
</template>

<script>
import { activeMenuUrl } from './menuActive'

export default {
  name: 'v-nav-menu-item',
  props: {
    icon        : { type: String,                 default: ""               },
    iconSmall   : { type: Boolean,                default: false            },
    iconPack    : { type: String,                 default: 'material-icons' },
    href        : { type: [String, null],         default: '#'              },
    to          : { type: [String, Object, null], default: null             },
    slug        : { type: String,                 default: null             },
    index       : { type: [String, Number],       default: null             },
    featherIcon : { type: Boolean,                default: true             },
    target      : { type: String,                 default: '_self'          },
    isDisabled  : { type: Boolean,                default: false            },
  },
  computed: {
    canSee() {
      this.$acl.check(this.$store.state.AppActiveUser.userRole)
      return this.to ? this.$acl.check(this.$router.resolve(this.to).meta.rule) : true
    },
    activeLink() {
      return Boolean(this.to) && activeMenuUrl(this.$route.path) === this.to
    }
  }
}

</script>


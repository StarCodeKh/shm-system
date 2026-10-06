<template>
  <div id="user-edit-tab-info">

    <!-- Permissions -->
    <vx-card class="mt-base" no-shadow card-border>

      <div class="vx-row">
        <div class="vx-col w-full">
          <div class="flex items-end px-3">
            <feather-icon svgClasses="w-6 h-6" icon="LockIcon" class="mr-2" />
            <span class="font-medium text-lg leading-none">Permissions</span>
          </div>
          <vs-divider />
        </div>
      </div>

      <div class="block overflow-x-auto">
        <table class="w-full">
          <tr>
            <!--
              You can also use `Object.keys(Object.values(data_local.permissions)[0])` this logic if you consider,
              our data structure. You just have to loop over above variable to get table headers.
              Below we made it simple. So, everyone can understand.
             -->
            <th class="font-semibold text-base text-left px-3 py-2" v-for="heading in ['Module', 'Read', 'Write', 'Create', 'Delete']" :key="heading">{{ heading }}</th>
          </tr>

          <tr v-for="(val, name) in permissions" :key="name">
            <td class="px-3 py-2">{{ name }}</td>
            <td v-for="(permission, name) in val" class="px-3 py-2" :key="name+permission">
              <vs-checkbox v-model="val[name]" />
            </td>
          </tr>
        </table>
      </div>

    </vx-card>

    <!-- Save & Reset Button -->
    <div class="vx-row">
      <div class="vx-col w-full">
        <div class="mt-8 flex flex-wrap items-center justify-end">
          <vs-button color="primary" type="filled" class="ml-auto mt-2" @click="save_changes" :disabled="!validateForm">{{ $t('btn_save_changes') }}</vs-button>
          <vs-button color="warning" type="border" class="ml-4 mt-2" @click="reset_data">{{ $t('btn_reset') }}</vs-button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import vSelect from 'vue-select'
// Store Module
import moduleUserManagement from '@/store/user-management/moduleUserManagement.js'
export default {
  components: {
    vSelect
  },
//   props: {
//     data: {
//       type: Object,
//       required: true,
//     },
//   },
  data() {
    return {
        data_local: [],
    //   data_local: JSON.parse(JSON.stringify(this.data)),

      statusOptions: [
        { label: "Active",  value: "active" },
        { label: "Blocked",  value: "blocked" },
        { label: "Deactivated",  value: "deactivated" },
      ],
      roleOptions: [
        { label: "Admin",  value: "admin" },
        { label: "User",  value: "user" },
        { label: "Staff",  value: "staff" },
      ],

      permissions: {
        "users": {
          "read": true,
          "write": false,
          "create": false,
          "delete": false
        },
        "posts": {
          "read": true,
          "write": true,
          "create": true,
          "delete": true
        },
        "comments": {
          "read": true,
          "write": false,
          "create": true,
          "delete": false
        },

      }
    }
  },
    computed: {
        validateForm() {
            return !this.errors.any()
        }
    },
  methods: {
    save_changes(){

    },
    reset_data(){
        this.permissions
    }
  }

}
</script>

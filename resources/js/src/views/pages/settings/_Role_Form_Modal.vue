<template>
    <div>
        <vs-popup fullscreen classContent="popup-example" title="កំណត់សិទ្ធ" :active.sync="showModalForm">
            <div id="user-edit-tab-info">
                <!-- Permissions -->
                <vx-card class="mt-base" no-shadow card-border>
                <div class="vx-row">
                    <div class="vx-col w-full">
                    <div class="flex items-end px-3">
                        <feather-icon svgClasses="w-6 h-6" icon="LockIcon" class="mr-2" />
                        <span class="font-medium text-lg leading-none">កំណត់សិទ្ធប្រើប្រាស់: {{ data.data.data.name }}</span>
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
                        <th class="font-semibold text-base text-left px-3 py-2" v-for="heading in ['Module', 'View List', 'Create', 'Show', 'Edit', 'Update', 'Delete']" :key="heading">{{ heading }}</th>
                    </tr>
                    
                    <tr v-if="data.data.permission_lists" v-for="(tr, index) in data.data.permission_lists" :key="index">
                        <td class="px-3 py-2">{{ tr.name }}</td>
                        <td v-for="(ctr, cindex) in tr.permission" class="px-3 py-2" :key="index+cindex">
                          <!-- { ctr.name }} -->
                          <span>
                            <vs-checkbox v-model="form.permission_by_roles[ctr.name]">
                              {{ $t(ctr.name) }}
                            </vs-checkbox>
                          </span>
                          <!-- <vs-checkbox v-model="val[ctr.name]" /> {{ ctr.name }} -->
                        </td>
                    </tr>
                    </table>
                </div>

                </vx-card>

                <!-- Save & Reset Button -->
                <div class="vx-row">
                  <div class="vx-col w-full">
                      <div class="mt-8 flex flex-wrap items-center justify-end">
                      <vs-button class="ml-auto mt-2" @click="save_changes" :disabled="!validateForm">Save Changes</vs-button>
                      <!-- <vs-button class="ml-4 mt-2" type="border" color="warning" @click="reset_data">Reset</vs-button> -->
                      </div>
                  </div>
                </div>
            </div>
        </vs-popup>
    </div>
</template>

<script>
import axios from "@/axios.js"
import apiConfig from "@/apiConfig.js"
import { ref } from 'vue';
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
      title: "setting_role",
      api: apiConfig._apiRole,
      showModalForm: false,
      data: {
        data: [
            {}
        ],
        limit: 10,
        total: 3,
      },
      data_local: [],
      // data_local: JSON.parse(JSON.stringify(this.data)),
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
      _checkPermissions: "",
      form: {permission_by_roles: {}},
      permission_list: [

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
    created() {
        this.$vs.loading();
    },
    watch: {
    },
    methods: {
        showForm(data) {
          this.form.permission_by_roles = [];
          this.showModalForm = true;
            return new Promise((resolve, reject) => {
              axios.get(this.api + "/" + data.id)
              .then((response) => {
                if (response.data) {
                  this.data = response.data;
                  let _checkPermissions;
                  // let index;
                  this.form.permission_by_roles = response.data.data.permission_by_roles;
                } else {
                  this.data = data;
                }
                this.$vs.loading.close();
            }).catch((error) => {
                // reject(error)
                this.$vs.notify({
                  title: 'Message',
                  text: "Error, while trying to do action or no permission allow!",
                  iconPack: 'feather',
                  icon: 'icon-check-circle',
                  color: 'danger'
                })
                this.$vs.loading.close();
            })
          })
          // this.data = data;
        },
        save_changes(){
          this.$vs.loading();
          let _data = {
            "role_info": this.data.data.data,
            "permission": this.form.permission_by_roles
          }
          this.showModalForm = true;
          return new Promise((resolve, reject) => {
            axios.post(this.api + '/set_permission', _data)
                .then((response) => {
                  console.log("message", response.data);
                    if (response.data.success == false) {
                        this.$vs.notify({
                            title: 'Message',
                            text: response.data.message,
                            iconPack: 'feather',
                            icon: 'icon-check-circle',
                            color: 'danger',
                            position: 'top-right'
                        })
                        this.showModalForm = true;
                    } else {
                        this.$vs.notify({
                            title: 'Message',
                            text: response.data.message,
                            iconPack: 'feather',
                            icon: 'icon-check-circle',
                            color: 'primary',
                            position: 'top-right'
                        })
                        // this.$emit('clickForm', '');
                        this.showModalForm = true;
                    }
                    this.$vs.loading.close();
                    // this.$router.push('/account/expense').catch(() => { })
                }).catch((error) => {
                    reject(error)
                    this.$vs.notify({
                        title: 'Message',
                        text: 'Error, while trying to do action or no permission allow!',
                        iconPack: 'feather',
                        icon: 'icon-check-circle',
                        color: 'danger',
                        position: 'top-right'
                    })
                    this.$vs.loading.close();
                })
        })
        },
        reset_data(){
            this.permissions
        }
    }

}
</script>

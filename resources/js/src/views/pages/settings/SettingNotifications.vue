<template>
  <vx-card no-shadow>

    <div class="mb-base">
      <h6 class="mb-4">Syncronize Data</h6>
      <div class="flex items-center mb-4">
        <vs-switch v-model="form.attribute.config_noti_weekly_syn_data_pdmef"/>
        <span class="ml-4">Weekly Syncronize Data with Personel Department of MEF</span>
      </div>
      <div class="flex items-center mb-4">
        <vs-switch v-model="form.attribute.config_noti_daily_syn_data_pdmef"/>
        <span class="ml-4">Daily Syncronize Data with Personel Department of MEF</span>
      </div>
    </div>

    <div>
      <h6 class="mb-4">Application</h6>
      <div class="flex items-center mb-4">
        <vs-switch v-model="form.attribute.config_noti_receiving_news"/>
        <span class="ml-4">News and announcements</span>
      </div>
      <div class="flex items-center mb-4">
        <vs-switch v-model="form.attribute.config_noti_allow_receive_email"/>
        <span class="ml-4">Email me when someone do actions on system.</span>
      </div>
      <div class="flex items-center">
        <vs-switch v-model="form.attribute.config_noti_allow_to_user"/>
        <span class="ml-4">Allow Receiving Notification to all users</span>
      </div>
    </div>

    <!-- Save & Reset Button -->
    <div class="flex flex-wrap items-center justify-end mt-base">
      <span class="pull-right">
        <vs-button type="filled" @click.prevent="submitForm" class="mt-5 block">{{ $t("btn_save") }}</vs-button>
      </span>
    </div>

  </vx-card>
</template>

<script>
import axios from "@/axios.js"
import apiConfig from "@/apiConfig.js"
import { ref } from 'vue';
export default {
  data() {
    return {
      api: apiConfig._apiConfiguration,
      form: {
        attribute: { 
          config_noti_weekly_syn_data_pdmef: false,
          config_noti_daily_syn_data_pdmef: false,
          config_noti_manual_syn_data_pdmef: false,
          config_noti_receiving_news: false,
          config_noti_allow_receive_email: false,
          config_noti_allow_to_user: false,
        },
      },
      formAttributes: [
        {
          name: "config_noti_weekly_syn_data_pdmef",
          type: "text",
          required: false,
        },
        {
          name: "config_noti_daily_syn_data_pdmef",
          type: "text",
          required: true
        },
        {
          name: "config_noti_receiving_news",
          type: "text",
          required: true
        },
        {
          name: "config_noti_allow_receive_email",
          type: "text",
          required: true,
        },
        {
          name: "config_noti_allow_to_user",
          type: "text",
          required: true
        },
      ],
      dataAttributes: [{
        page_number: ""
      }]
    }
  },
  methods: {
    getDataTable(_search_criteria) {
      let _params = {};
      if (_search_criteria.search_field) {
        let _formAttribute = this.formAttributes;
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
          page_number: _search_criteria.page_number,
        };
      }
      return new Promise((resolve, reject) => {
        axios.get(this.api, {
          params: _params
        }).then((response) => {
          if (response.data) {
            this.data = response.data;
          } else {
            this.data = this.data;
          }
          this.formAttributes.forEach(_formAttribute => {
            if (_formAttribute["type"] == "select") {
              this.form.attribute[_formAttribute["name"]] = (this.data.data[_formAttribute["name"]]);
            } else if (_formAttribute["type"] == "hidden") {
              this.form.attribute[_formAttribute["name"]] = (this.data.data[_formAttribute["name"]]);
            } else if (_formAttribute["type"] == "file") {
              this.form.attribute[_formAttribute["name"]] = (this.data.data[_formAttribute["name"]]);
              this.iconThumnail = (this.data.data[_formAttribute["name"]]);
            }else {
              if(this.data.data[_formAttribute["name"]] == 0){
                this.form.attribute[_formAttribute["name"]] = false;
              }else if(this.data.data[_formAttribute["name"]] == 1){
                this.form.attribute[_formAttribute["name"]] = true;
              }
            }
            this.$validator.validateAll().then(result => {
            })
          });

          this.$vs.loading.close();
        }).catch((error) => {
          // reject(error)
          this.$vs.notify({
                    title: 'Message',
                    text: 'msg.action_failed',
                    iconPack: 'feather',
                    icon: 'icon-check-circle',
                    color: 'danger'
                  })
          this.$vs.loading.close();
        })
      })
    },
    getData() {
      let _search_criteria = {
        sort: "id",
        order: "",
        page_number: this.dataAttributes.page_number,
        search_field: ""
      }
      this.getDataTable(_search_criteria);
    },
    submitForm() {
      let _d;
      this.dataFields = [];
      let _formAttribute = this.formAttributes;
      this.formAttributes.forEach(_formAttribute => {
        if (_formAttribute["type"] == "select") {
          try {
            _d = {
              [_formAttribute["name"]]: this.form.attribute[_formAttribute["name"]].value
            }
          } catch (e) {
            _d = {
              [_formAttribute["name"]]: this.form.attribute[_formAttribute["name"]]
            }
          }
        } else {
          _d = {
            [_formAttribute["name"]]: this.form.attribute[_formAttribute["name"]]
          }
        }
        this.dataFields.push(_d);
      });
      
      this.$validator.validateAll().then(result => {
        if (result) {
          let _data = this.dataFields;
          return new Promise((resolve, reject) => {
            axios.put(this.api + '/' + "update", _data)
              .then((response) => {
                if (response.data.success == false) {
                  this.$vs.notify({
                    title: 'Message',
                    text: response.data.message,
                    iconPack: 'feather',
                    icon: 'icon-check-circle',
                    color: 'danger',
                    position: 'top-right'
                  })
                } else {
                  this.$vs.notify({
                    title: 'Message',
                    text: response.data.message,
                    iconPack: 'feather',
                    icon: 'icon-check-circle',
                    color: 'primary',
                    position: 'top-right'
                  })
                  this.$emit('clickForm', '');
                }
                // this.$router.push('/account/expense').catch(() => { })
              }).catch((error) => {
                reject(error)
                this.$vs.notify({
                  title: 'Message',
                  text: 'msg.action_failed',
                  iconPack: 'feather',
                  icon: 'icon-check-circle',
                  color: 'danger',
                  position: 'top-right'
                })
                this.$vs.loading.close();
              })
          })
        }
        this.$vs.loading.close();
      })
    }
  },
  created(){
    this.$vs.loading();
    this.getData();
  },
  computed: {
    activeUserInfo() {
      return this.$store.state.AppActiveUser
    },
  }
}
</script>

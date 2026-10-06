<template>
  <vx-card no-shadow>

    <div class="vx-row">
      <div class="vx-col md:w-1/2 w-full mt-5">
        <vs-input aria-disabled="true" :label="$t('setting_user')" autocomplete="off" name="username" v-model="username" class="w-full" v-validate="'required'"/>
        <span class="text-danger">{{ errors.first('username') }}</span>
      </div>

      <div class="vx-col md:w-1/2 w-full mt-5">
        <vs-input type="password" :label="$t('setting_password')" autocomplete="off" name="password" v-model="password" class="w-full" v-validate="'required'"/>
        <span class="text-danger">{{ errors.first('password') }}</span>
      </div>

      <div class="vx-col md:w-1/2 w-full mt-5">
        <vs-input type="password" :label="$t('setting_conf_password')" autocomplete="off" name="confirm_password" v-model="confirm_password" class="w-full" v-validate="'required'"/>
        <span class="text-danger">{{ errors.first('confirm_password') }}</span>
      </div>
    </div>
    <!-- <vs-input class="w-full mb-base" label="អ្នកប្រើប្រាស់" v-model="username" />
    <span class="text-danger">{{ errors.first('first_name') }}</span>
    <vs-input class="w-full mb-base" label="លេខសម្ងាត់" v-model="password" />
    <vs-input class="w-full mb-base" label="បញ្ជាក់លេខសម្ងាត់" v-model="confirm_password" /> -->

    <!-- Save & Reset Button -->
    <div class="flex flex-wrap items-center justify-end">
      <vs-button class="ml-auto mt-2" @click="saveGeneral()">{{ $t("btn_save")}}</vs-button>
      <!-- <vs-button color="warning" type="border" class="ml-4 mt-2">{{ $t('btn_reset') }}</vs-button> -->
    </div>
  </vx-card>
</template>

<script>
import vSelect from 'vue-select'
import { Validator } from 'vee-validate';
import axios from "@/axios.js"
const dict = {
  custom: {
    username: {
      required: 'សំុបញ្ជូលឈ្មោះអ្នកប្រើប្រព័ន្ធ'
    },
    new_password: {
      required: 'សំុបញ្ជូលលេខសម្ងាត់'
    },
    confirm_password: {
      required: 'សំុបញ្ជាក់លេខសម្ងាត់'
    },
  }
};
// Field messages for Khmer; merged without changing the app's validation language
Validator.localize({ kh: dict });
export default {
  data() {
    return {
      username: "",
      password: "",
      confirm_password: "",
      user_id: ""
    }
  },
  created(){
    if(this.$route.params.userId){
      let _id = this.$route.params.userId
      return new Promise((resolve, reject) => {
        axios.get("/api/v1/user/" + _id)
            .then((response) => {
            let _data = response.data
            this.username = _data.data.username
        }).catch((error) => { 
          reject(error)
          this.$vs.notify({
            title: 'Message',
            text: 'msg.action_failed',
            iconPack: 'feather',
            icon: 'icon-check-circle',
            color: 'danger'
          })
        })
      })
    }
  },
  methods: {
    saveGeneral(){
      let _id = this.$route.params.userId;
      this.$validator.validateAll().then(result => {
        if(result){
          let _data = {
            id: _id,
            username: this.username,
            password: this.password
          }
          if(this.$route.params.userId){
            return new Promise((resolve, reject) => {
                axios.put("/api/v1/user/" + _id, _data)
                    .then((response) => {
                    this.$vs.notify({
                      title: 'Message',
                      text: response.data.message,
                      iconPack: 'feather',
                      icon: 'icon-check-circle',
                      color: 'primary'
                    })
                    this.$router.push('/user/list').catch(() => {})
                }).catch((error) => { 
                  reject(error)
                  this.$vs.notify({
                    title: 'Message',
                    text: 'msg.update_failed',
                    iconPack: 'feather',
                    icon: 'icon-check-circle',
                    color: 'danger'
                  })
                })
            })
          }
        }
      });
    }
  },
  computed: {
    activeUserInfo() {
      return this.$store.state.AppActiveUser
    },
  }
}
</script>

<template>
  <vx-card no-shadow>
    <!-- Info -->
    <!-- <vs-input class="w-full mb-base" label="Username" v-model="username"></vs-input> -->
    <!-- <vs-input class="w-full" name="username" :label="$tt('Name')" v-model="username" v-validate="'required'" />
    <span class="text-danger">{{ errors.first('username') }}</span>
    <vs-input class="w-full" name="name" :label="$tt('Name')" v-model="name" v-validate="'required'"></vs-input>
    <span class="text-danger">{{ errors.first('name') }}</span>
    <vs-input class="w-full" name="email" :label="$tt('Email')" v-model="email" v-validate="'required'"></vs-input>
    <span class="text-danger">{{ errors.first('email') }}</span> -->

    <!-- <vs-alert icon-pack="feather" icon="icon-info" class="h-full my-4" color="warning">
      <span>Your account is not verified. <a href="#" class="hover:underline">Resend Confirmation</a></span>
    </vs-alert> -->

    <!-- Img Row -->
    <div v-if="$route.params.userId" class="flex flex-wrap items-center mb-base">
      <vs-avatar :src="activeUserInfo.photoURL" size="70px" class="mr-4 mb-4" />
      <div>
        <vs-button color="primary" type="filled" class="mr-4 sm:mb-0 mb-2">{{ $t('btn_upload_photo') }}</vs-button>
        <vs-button color="danger" type="border">{{ $t('btn_remove') }}</vs-button>
        <p class="text-sm mt-2">Allowed JPG, GIF or PNG. Max size of 800kB</p>
      </div>
    </div>

    <div class="vx-row">
      <div class="vx-col md:w-1/2 w-full mt-5">
        <vs-input :label="$tt('ចំណងជើង')" autocomplete="off" name="title" v-model="title" class="w-full" v-validate="'required'"/>
        <span class="text-danger">{{ errors.first('title') }}</span>
      </div>
      <div class="vx-col md:w-1/2 w-full mt-5">
        <vs-input :label="$tt('លេខយោង')" autocomplete="off" name="reference" v-model="reference" class="w-full"/>
      </div>
      <div class="vx-col md:w-1/2 w-full mt-5">
        <label for="" class="vs-input--label">ថ្ងៃចំណាយ</label>
        <flat-pickr class="w-full" v-validate="'required'" :placeholder="$tt('ថ្ងៃចំណាយ')" name="issue_date" v-model="issue_date" />
        <span class="text-danger">{{ errors.first('issue_date') }}</span>
      </div>
      <div class="vx-col md:w-1/2 w-full mt-5">
        <label for="" class="vs-input--label">គណនី</label>
        <v-select name="account" v-model="account" :options="accounts" :dir="$vs.rtl ? 'rtl' : 'ltr'"/>
      </div>
      <div class="vx-col md:w-1/2 w-full mt-5">
        <label for="" class="vs-input--label">ប្រភេទ</label>
        <v-select name="category" v-model="category" :options="categories" :dir="$vs.rtl ? 'rtl' : 'ltr'" />
      </div>
      <div class="vx-col md:w-1/2 w-full mt-5">
        <label for="" class="vs-input--label">អង្គភាព</label>
        <v-select name="entity" v-model="entity" :options="entities" :dir="$vs.rtl ? 'rtl' : 'ltr'" v-validate="'required'" />
        <span class="text-danger">{{ errors.first('entity') }}</span>
      </div>
      <div class="vx-col md:w-1/2 w-full mt-5">
        <label for="" class="vs-input--label">រូបបីយវត្ថុទូទាត់</label>
        <v-select name="currency" v-model="currency" :options="currencies" :dir="$vs.rtl ? 'rtl' : 'ltr'" v-validate="'required'" />
        <span class="text-danger">{{ errors.first('currency') }}</span>
      </div>
      <div class="vx-col md:w-1/2 w-full mt-5">
        <label for="" class="vs-input--label">អត្រាប្តូរប្រាក់</label>
        <vs-input name="exchange_rate" v-model="exchange_rate" :dir="$vs.rtl ? 'rtl' : 'ltr'" v-validate="'required'" class="w-full" />
        <span class="text-danger">{{ errors.first('exchange_rate') }}</span>
      </div>
      <div class="vx-col md:w-1/2 w-full mt-5">
        <vs-input :label="$tt('ចំនួនទឹកប្រាក់')" autocomplete="off" name="amount" v-model="amount" class="w-full" v-validate="'required'"/>
        <span class="text-danger">{{ errors.first('amount') }}</span>
      </div>
      <div class="vx-col md:w-1/2 w-full mt-5">
        <label for="" class="vs-input--label">វិធីទូទាត់</label>
        <v-select name="payment_method" v-model="payment_method" :options="paymentMethods" :dir="$vs.rtl ? 'rtl' : 'ltr'" v-validate="'required'" />
        <span class="text-danger">{{ errors.first('payment_method') }}</span>
      </div>
      <div class="vx-col md:w-1/2 w-full mt-5">
        <vs-textarea :label="$tt('ពិពណ៌នា')" class="w-full" v-model="description" />
      </div>
    </div>
  
    <!-- Save & Reset Button -->
    <div class="flex flex-wrap items-center justify-end">
      <vs-button color="primary" type="filled" class="ml-auto mt-2" @click="saveGeneral()">{{ $t('btn_save_changes') }}</vs-button>
    </div>
  </vx-card>
</template>

<script>
import vSelect from 'vue-select'
import { Validator } from 'vee-validate';
import axios from "@/axios.js"

export default {
  components: {
    'v-select': vSelect
  },
  data() {
    return {
      title: "",
      account: "",
      description: "",
      issue_date: "",
      category: "",
      payment_method: "",
      amount: "",
      exchange_rate: 4000,
      reference: "",
      entity: "",
      currency: "",
      entities: [],
      categories: [],
      accounts: [],
      currencies: [],
      paymentMethods: []
    }
  },
  created(){
    this.$vs.loading();
    setTimeout(() => {
        this.$vs.loading.close()
    }, 2000);
    if(this.$route.params.id){
      let _id = this.$route.params.id;
      return new Promise((resolve, reject) => {
          axios.get("/api/v1/account/chart_account/" + _id)
              .then((response) => {
              let _data = response.data.data
              this.title = _data.title,
              this.account = _data.account,
              this.description = _data.description,
              this.issue_date = _data.issue_date,
              this.category = _data.category,
              this.payment_method = _data.payment_method,
              this.amount = parseFloat(_data.amount),
              this.exchange_rate = _data.exchange_rate,
              this.reference = _data.reference,
              this.entity = _data.entity,
              this.currency = _data.currency
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
      this.$validator.validateAll().then(result => {
        if(result){
          let _data = {
            title: this.title,
            account: this.account.label,
            description: this.description,
            issue_date: this.issue_date,
            category: this.category.label,
            payment_method: this.payment_method.label,
            amount: parseFloat(this.amount),
            exchange_rate: parseInt(this.exchange_rate),
            reference: this.reference,
            entity: this.entity,
            currency: this.currency.label
          }
          if(this.$route.params.id){
            let _id = this.$route.params.id;
            return new Promise((resolve, reject) => {
                axios.put("/api/v1/account/chart_account/" + _id, _data)
                    .then((response) => {
                    this.$vs.notify({
                      title: 'Message',
                      text: response.data.message,
                      iconPack: 'feather',
                      icon: 'icon-check-circle',
                      color: 'primary'
                    })
                    this.$router.push('/account/coa').catch(() => {})
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
          }else{
            return new Promise((resolve, reject) => {
                axios.post("/api/v1/account/chart_account", _data)
                    .then((response) => {
                      console.log("message", response);
                    this.$vs.notify({
                      title: 'Message',
                      text: response.data.message,
                      iconPack: 'feather',
                      icon: 'icon-check-circle',
                      color: 'primary'
                    })
                    this.$router.push('/account/chart-account').catch(() => {})
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

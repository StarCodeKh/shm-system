<template>
  <vx-card no-shadow>
    <!-- Info -->
    <!-- <vs-input class="w-full mb-base" label="Username" v-model="username"></vs-input> -->
    <!-- <vs-input class="w-full" name="username" label="Name" v-model="username" v-validate="'required'" />
    <span class="text-danger">{{ errors.first('username') }}</span>
    <vs-input class="w-full" name="name" label="Name" v-model="name" v-validate="'required'"></vs-input>
    <span class="text-danger">{{ errors.first('name') }}</span>
    <vs-input class="w-full" name="email" label="Email" v-model="email" v-validate="'required'"></vs-input>
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
        <vs-input label="ចំណងជើង" autocomplete="off" name="title" v-model="title" class="w-full" v-validate="'required'"/>
        <span class="text-danger">{{ errors.first('title') }}</span>
      </div>
      <div class="vx-col md:w-1/2 w-full mt-5">
        <label for="" class="vs-input--label">រូបបីយវត្ថុ</label>
        <v-select name="currency_symbol" v-model="currency_symbol" :options="currencies" :dir="$vs.rtl ? 'rtl' : 'ltr'" v-validate="'required'" />
        <span class="text-danger">{{ errors.first('currency_symbol') }}</span>
      </div>
      <div class="vx-col md:w-1/2 w-full mt-5">
        <vs-input type="number" label="ភាគបៀវត្សជាប់ពន្ធចន្លោះពី" autocomplete="off" name="taxable_salary_monthly_from" v-model="taxable_salary_monthly_from" class="w-full" v-validate="'required'"/>
        <span class="text-danger">{{ errors.first('taxable_salary_monthly_from') }}</span>
      </div>
      <div class="vx-col md:w-1/2 w-full mt-5">
        <vs-input type="number" label="ភាគបៀវត្សជាប់ពន្ធរហូតដល់" autocomplete="off" name="taxable_salary_monthly_to" v-model="taxable_salary_monthly_to" class="w-full" v-validate="'required'"/>
        <span class="text-danger">{{ errors.first('taxable_salary_monthly_to') }}</span>
      </div>
      
      <div class="vx-col md:w-1/2 w-full mt-5">
        <label for="" class="vs-input--label">អត្រា(%)</label>
        <vs-input type="number" name="rate" v-model="rate" :dir="$vs.rtl ? 'rtl' : 'ltr'" v-validate="'required'" class="w-full" />
        <span class="text-danger">{{ errors.first('rate') }}</span>
      </div>
      <div class="vx-col md:w-1/2 w-full mt-5">
        <vs-input type="number" label="លម្អៀងលើសពន្ធតាមថ្នាក់" autocomplete="off" name="over_class_tax" v-model="over_class_tax" class="w-full" v-validate="'required'"/>
        <span class="text-danger">{{ errors.first('over_class_tax') }}</span>
      </div>
      <div class="vx-col md:w-1/2 w-full mt-5">
        <vs-input label="លំដាប់លេខ" autocomplete="off" name="order_level" v-model="order_level" class="w-full" v-validate="'required'"/>
        <span class="text-danger">{{ errors.first('order_level') }}</span>
      </div>
      <div class="vx-col md:w-1/2 w-full mt-5">
        <vs-textarea label="ពិពណ៌នា" class="w-full" v-model="remark" />
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
const dict = {
  custom: {
      title: {
        required: 'សូមបញ្ចូលចំណងជើង',
        alpha: "First name may only contain alphabetic characters"
      },
      taxable_salary_monthly_from: {
        required: 'សូមបញ្ជូលភាគបៀវត្សជាប់ពន្ធចន្លោះពី',
        alpha: "First name may only contain alphabetic characters"
      },
      taxable_salary_monthly_to: {
        required: 'សូមបញ្ជូលភាគបៀវត្សជាប់ពន្ធរហូតដល់',
        alpha: "First name may only contain alphabetic characters"
      },
      rate: {
        required: 'សូមបញ្ចូលអត្រា',
        alpha: "First name may only contain alphabetic characters"
      },
      currency_symbol: {
        required: 'សូមជ្រើសរើសរូបបីយវត្ថុ',
        alpha: "First name may only contain alphabetic characters"
      },
      over_class_tax: {
        required: 'សូមបញ្ចូលចំនួលទឹកប្រាក់លម្អៀងលើសប្រាក់ពន្ធ',
        alpha: "First name may only contain alphabetic characters"
      },
      order_level: {
        required: 'សូមបញ្ជូលលំដាប់លេខរៀង',
        alpha: "First name may only contain alphabetic characters"
      }
  }
};
Validator.localize('en', dict);
export default {
  components: {
    'v-select': vSelect
  },
  data() {
    return {
      title: "",
      taxable_salary_monthly_from: "",
      taxable_salary_monthly_to: "",
      rate: "",
      currency_symbol: "",
      over_class_tax: "",
      order_level: "",
      remark: "",
      currencies: [],
    }
  },
  created(){
    this.getCurrency()
    this.$vs.loading();
    setTimeout(() => {
        this.$vs.loading.close()
    }, 2000);
    if(this.$route.params.id){
      let _id = this.$route.params.id;
      return new Promise((resolve, reject) => {
          axios.get("/api/v1/taxable_salary/" + _id)
              .then((response) => {
              let _data = response.data.data
              this.title = _data.title
              this.taxable_salary_monthly_from = _data.taxable_salary_monthly_from
              this.taxable_salary_monthly_to = _data.taxable_salary_monthly_to
              this.rate = _data.rate
              this.currency_symbol = _data.currency_symbol
              this.over_class_tax = _data.over_class_tax
              this.order_level = _data.order_level
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
          })
      })
    }
  },
  methods: {
    getCurrency(){
      return new Promise((resolve, reject) => {
        axios.get("/api/v1/currency?offset=0&limit=100&sort=id&order=desc")
            .then((response) => {
            let currencies = response["data"]["data"];
            for(let i = 0; i < currencies.length; i++){
              let currency_symbol
              if(currencies[i]["symbol_left"] == null){
                currency_symbol = currencies[i]["symbol_right"]
              }else{
                currency_symbol = currencies[i]["symbol_left"]
              }
              const _data = {
                id: currency_symbol,
                label: currency_symbol
              };
              this.currencies.push(_data);
            }
        }).catch((error) => { reject(error) })
      })
    },
    saveGeneral(){
      this.$validator.validateAll().then(result => {
        if(result){
          let _data = {
            title: this.title,
            taxable_salary_monthly_from: this.taxable_salary_monthly_from,
            taxable_salary_monthly_to: this.taxable_salary_monthly_to,
            rate: this.rate,
            currency_symbol: this.currency_symbol.label,
            over_class_tax: this.over_class_tax,
            order_level: this.order_level
          }
          if(this.$route.params.id){
            let _id = this.$route.params.id;
            return new Promise((resolve, reject) => {
                axios.put("/api/v1/taxable_salary/" + _id, _data)
                    .then((response) => {
                    this.$vs.notify({
                      title: 'Message',
                      text: response.data.message,
                      iconPack: 'feather',
                      icon: 'icon-check-circle',
                      color: 'primary'
                    })
                    this.$router.push('/account/taxable-salary').catch(() => {})
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
                })
            })
          }else{
            return new Promise((resolve, reject) => {
                axios.post("/api/v1/taxable_salary", _data)
                    .then((response) => {
                      console.log("message", response);
                    this.$vs.notify({
                      title: 'Message',
                      text: response.data.message,
                      iconPack: 'feather',
                      icon: 'icon-check-circle',
                      color: 'primary'
                    })
                    this.$router.push('/account/taxable-salary').catch(() => {})
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

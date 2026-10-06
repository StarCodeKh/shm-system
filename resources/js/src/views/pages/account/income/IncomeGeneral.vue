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
        <vs-input label="លេខយោង" autocomplete="off" name="reference" v-model="reference" class="w-full"/>
      </div>
      <div class="vx-col md:w-1/2 w-full mt-5">
        <label for="" class="vs-input--label">ថ្ងៃបង់ចំណូល</label>
        <flat-pickr class="w-full" v-validate="'required'" placeholder="ថ្ងៃបង់ចំណូល" name="issue_date" v-model="issue_date" />
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
        <vs-input label="ចំនួនទឹកប្រាក់" autocomplete="off" name="amount" v-model="amount" class="w-full" v-validate="'required'"/>
        <span class="text-danger">{{ errors.first('amount') }}</span>
      </div>
      <div class="vx-col md:w-1/2 w-full mt-5">
        <label for="" class="vs-input--label">វិធីទូទាត់</label>
        <v-select name="payment_method" v-model="payment_method" :options="paymentMethods" :dir="$vs.rtl ? 'rtl' : 'ltr'" v-validate="'required'" />
        <span class="text-danger">{{ errors.first('payment_method') }}</span>
      </div>
      <div class="vx-col md:w-1/2 w-full mt-5">
        <vs-textarea label="ពិពណ៌នា" class="w-full" v-model="description" />
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
        account: {
          required: 'សូមជ្រើសរើសគណនី',
          alpha: "First name may only contain alphabetic characters"
        },
        issue_date: {
          required: 'សូមជ្រើសថ្ងៃដែលបានទទួល',
          alpha: "First name may only contain alphabetic characters"
        },
        category: {
          required: 'សូមជ្រើសរើសប្រភេទចំនាយ',
          alpha: "First name may only contain alphabetic characters"
        },
        currency: {
          required: 'សូមជ្រើសរើសរូបបីយវត្ថុ',
          alpha: "First name may only contain alphabetic characters"
        },
        amount: {
          required: 'សូមបញ្ចូលចំនួនទឹកប្រាក់',
          alpha: "First name may only contain alphabetic characters"
        },
        entity: {
          required: 'សូមជ្រើសរើសអង្គភាពចំនាយ',
          alpha: "First name may only contain alphabetic characters"
        },
        payment_method: {
          required: 'សូមជ្រើសរើសវិធីបង់ប្រាក់',
          alpha: "First name may only contain alphabetic characters"
        },
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
    this.getAccount()
    this.getEntity()
    this.getCurrency()
    this.getCategory()
    this.getPaymentMethod()
    if(this.$route.params.id){
      let _id = this.$route.params.id;
      return new Promise((resolve, reject) => {
          axios.get("/api/v1/income/" + _id)
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
    getEntity(){
      return new Promise((resolve, reject) => {
        axios.get("/api/v1/entity?offset=0&limit=100&sort=id&order=desc")
            .then((response) => {
            let entities = response["data"]["data"];
            for(let i = 0; i < entities.length; i++){
                const _data = {
                    id: entities[i]["id"],
                    label: entities[i]["name"]
                };
                this.entities.push(_data);
            }
        }).catch((error) => { reject(error) })
      })
    },
    getAccount(){
      return new Promise((resolve, reject) => {
        axios.get("/api/v1/account?offset=0&limit=100&sort=id&order=desc")
            .then((response) => {
            let accounts = response["data"]["data"];
            for(let i = 0; i < accounts.length; i++){
                const _data = {
                    id: accounts[i]["id"],
                    label: accounts[i]["name"]
                };
                this.accounts.push(_data);
            }
        }).catch((error) => { reject(error) })
      })
    },
    getCategory(){
      return new Promise((resolve, reject) => {
        axios.get("/api/v1/category?offset=0&limit=100&sort=id&order=desc")
            .then((response) => {
            let categories = response["data"]["data"];
            for(let i = 0; i < categories.length; i++){
                const _data = {
                    id: categories[i]["id"],
                    label: categories[i]["name"]
                };
                this.categories.push(_data);
            }
        }).catch((error) => { reject(error) })
      })
    },
    getCurrency(){
      return new Promise((resolve, reject) => {
        axios.get("/api/v1/currency?offset=0&limit=100&sort=id&order=desc")
            .then((response) => {
            let currencies = response["data"]["data"];
            for(let i = 0; i < currencies.length; i++){
                const _data = {
                    id: currencies[i]["id"],
                    label: currencies[i]["name"]
                };
                this.currencies.push(_data);
            }
        }).catch((error) => { reject(error) })
      })
    },
    getPaymentMethod(){
      return new Promise((resolve, reject) => {
        axios.get("/api/v1/payment_method?offset=0&limit=100&sort=id&order=desc")
            .then((response) => {
            let paymentMethods = response["data"]["data"];
            for(let i = 0; i < paymentMethods.length; i++){
                const _data = {
                    id: paymentMethods[i]["id"],
                    label: paymentMethods[i]["name"]
                };
                this.paymentMethods.push(_data);
            }
        }).catch((error) => { reject(error) })
      })
    },
    letterTypeList() {
      return [
      'លិខិតផ្ទៃក្នុង',
      'លិខិតចេញ',
      'លិខិតចូល'
      ]
    },
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
                axios.put("/api/v1/income/" + _id, _data)
                    .then((response) => {
                    this.$vs.notify({
                      title: 'Message',
                      text: response.data.message,
                      iconPack: 'feather',
                      icon: 'icon-check-circle',
                      color: 'primary'
                    })
                    this.$router.push('/account/income').catch(() => {})
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
                axios.post("/api/v1/income", _data)
                    .then((response) => {
                      console.log("message", response);
                    this.$vs.notify({
                      title: 'Message',
                      text: response.data.message,
                      iconPack: 'feather',
                      icon: 'icon-check-circle',
                      color: 'primary'
                    })
                    this.$router.push('/account/income').catch(() => {})
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

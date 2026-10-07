
<template>
  <vs-sidebar click-not-close position-right parent="body" default-index="1" color="primary" class="add-new-data-sidebar items-no-padding" spacer v-model="isSidebarActiveLocal">
    <div class="mt-6 flex items-center justify-between px-6">
        <h4>{{ Object.entries(this.data).length === 0 ? "ADD NEW" : "UPDATE" }}</h4>
        <feather-icon icon="XIcon" @click.stop="isSidebarActiveLocal = false" class="cursor-pointer"></feather-icon>
    </div>
    <vs-divider class="mb-0"></vs-divider>

    <VuePerfectScrollbar class="scroll-area--data-list-add-new" :settings="settings" :key="$vs.rtl">
      <div class="p-6">

        <vs-input :label="$tt('ឈ្មោះ')" v-model="name" class="mt-5 w-full" name="name" v-validate="'required'" />
        <span class="text-danger text-sm" v-show="errors.has('name')">{{ $tt(errors.first('name')) }}</span>

        <vs-input :label="$tt('ឈ្មោះជាភាសារខ្មែរ')" v-model="name_kh" class="mt-5 w-full" name="name_kh" v-validate="'required'" />
        <span class="text-danger text-sm" v-show="errors.has('name_kh')">{{ $tt(errors.first('name_kh')) }}</span>
        
        <br/><label for="" class="vs-input--label">ក្រុមគណនី</label>
        <v-select name="account_group" v-model="account_group" :options="accountGroups" :dir="$vs.rtl ? 'rtl' : 'ltr'" v-validate="'required'" />
        <span class="text-danger">{{ $tt(errors.first('account_group')) }}</span>
      
        <br/><label for="" class="vs-input--label">ប្រភេទគណនី</label>
        <v-select name="account_type" v-model="account_type" :options="accountTypes" :dir="$vs.rtl ? 'rtl' : 'ltr'" v-validate="'required'" />
        <span class="text-danger">{{ $tt(errors.first('account_type')) }}</span>
      
        <vs-input :label="$tt('ឈ្មោះគណនី')" v-model="account_name" class="mt-5 w-full" name="account_name" v-validate="'required'" />
        <span class="text-danger text-sm" v-show="errors.has('account_name')">{{ $tt(errors.first('account_name')) }}</span>
        
        <vs-input :label="$tt('លេខគណនី')" v-model="account_number" class="mt-5 w-full" name="account_number" />

        <vs-input :label="$tt('ទឹកប្រាក់')" v-model="open_balance" class="mt-5 w-full" name="open_balance" v-validate="'required'" />
        <span class="text-danger text-sm" v-show="errors.has('open_balance')">{{ $tt(errors.first('open_balance')) }}</span>

        <br/><label for="" class="vs-input--label">{{ $t('currency') }}</label>
        <v-select name="currency" v-model="currency" :options="currencies" :dir="$vs.rtl ? 'rtl' : 'ltr'" v-validate="'required'" />
        <span class="text-danger">{{ $tt(errors.first('currency')) }}</span>
      
        <vs-input :label="$tt('កូដ')" v-model="code" class="mt-5 w-full" name="code" />

        <vs-input :label="$tt('អាស័យដ្ឋាន')" v-model="address" class="mt-5 w-full" name="address" />

        <br/><label for="" class="vs-input--label">ថ្ងៃដាក់ដំណើរការ</label>
        <flat-pickr class="w-full" v-validate="'required'" :placeholder="$tt('ថ្ងៃដាក់ដំណើរការ')" name="issue_date" v-model="issue_date" />
        <span class="text-danger">{{ $tt(errors.first('step-1.issue_date')) }}</span>
        
        <vs-input :label="$tt('សំគាល់')" v-model="remark" class="mt-5 w-full" name="remark" />

      </div>
    </VuePerfectScrollbar>

    <div class="flex flex-wrap items-center p-6" slot="footer">
      <vs-button color="primary" type="filled" class="mr-6" @click="submitData" :disabled="!isFormValid">{{ $t('btn_save') }}</vs-button>
      <vs-button color="dark" type="border" @click="isSidebarActiveLocal = false">{{ $t('btn_cancel') }}</vs-button>
    </div>
  </vs-sidebar>
</template>

<script>
import VuePerfectScrollbar from 'vue-perfect-scrollbar'
import vSelect from 'vue-select'
import axios from "@/axios.js"

export default {
  props: {
    isSidebarActive: {
      type: Boolean,
      required: true
    },
    data: {
      type: Object,
      default: () => {},
    }
  },
  watch: {
    isSidebarActive(val) {
      if(!val) return
      if(Object.entries(this.data).length === 0) {
        this.initValues()
        this.$validator.reset()
      }else {
        let { id, name, name_kh, account_group, account_name, account_type, account_number, open_balance, currency, code, address, remark, issue_date  } = JSON.parse(JSON.stringify(this.data))
        this.dataId = id
        this.name = name,
        this.name_kh = name_kh,
        this.account_group = account_group,
        this.account_name = account_name,
        this.account_type = account_type,
        this.account_number = account_number,
        this.open_balance = open_balance,
        this.currency = currency,
        this.code = code,
        this.address = address,
        this.remark = remark,
        this.issue_date = issue_date,
        this.initValues()
      }
      // Object.entries(this.data).length === 0 ? this.initValues() : { this.dataId, this.dataName, this.dataCategory, this.dataOrder_status, this.dataPrice } = JSON.parse(JSON.stringify(this.data))
    }
  },
  data() {
    return {
      api: "/api/v1/bank_account",
      dataId: null,
      name: "",
      name_kh: "",
      account_group: "",
      account_name: "",
      account_type: "",
      account_number: "",
      open_balance: "",
      currency: "",
      code: "",
      address: "",
      remark: "",
      issue_date: "",
      category_choices: [
        {text:'Audio',value:'audio'},
        {text:'Computers',value:'computers'},
        {text:'Fitness',value:'fitness'},
        {text:'Appliance',value:'appliance'}
      ],

      order_status_choices: [
        {text:'Pending',value:'pending'},
        {text:'Canceled',value:'canceled'},
        {text:'Delivered',value:'delivered'},
        {text:'On Hold',value:'on_hold'}
      ],
      settings: { // perfectscrollbar settings
          maxScrollbarLength: 60,
          wheelSpeed: .60,
      },
      accountGroups: [],
      accountTypes: [],
      currencies: []
    }
  },
  computed: {
    isSidebarActiveLocal: {
      get() {
        return this.isSidebarActive
      },
      set(val) {
        if(!val) {
          this.$emit('closeSidebar')
          // this.$validator.reset()
          // this.initValues()
        }
      }
    },
    isFormValid() {
      return !this.errors.any() && this.name && this.name_kh && this.account_group && this.account_type && this.currency && this.open_balance
    }
  },
  created(){
    this.getAccountGroup()
    this.getAccountType()
    this.getCurrency()
  },
  methods: {
    getAccountGroup(){
      return new Promise((resolve, reject) => {
        axios.get("/api/v1/account_group?offset=0&limit=100&sort=id&order=desc")
            .then((response) => {
            let accountGroups = response["data"]["data"];
            for(let i = 0; i < accountGroups.length; i++){
                const _data = {
                    id: accountGroups[i]["id"],
                    label: accountGroups[i]["name"]
                };
                this.accountGroups.push(_data);
            }
        }).catch((error) => { reject(error) })
      })
    },
    getAccountType(){
      return new Promise((resolve, reject) => {
        axios.get("/api/v1/account_type?offset=0&limit=100&sort=id&order=desc")
            .then((response) => {
            let accountTypes = response["data"]["data"];
            for(let i = 0; i < accountTypes.length; i++){
                const _data = {
                    id: accountTypes[i]["id"],
                    label: accountTypes[i]["name"]
                };
                this.accountTypes.push(_data);
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
    initValues() {
      if(this.data.id) return
        this.dataId = null
        this.dataName = ""
    },
    submitData() {
      this.$validator.validateAll().then(result => {
          if (result) {
            const obj = {
              id: this.dataId,
              name: this.name,
              name_kh: this.name_kh,
              account_group: this.account_group.label,
              account_name: this.account_name,
              account_type: this.account_type.label,
              account_number: this.account_number,
              open_balance: this.open_balance,
              currency: this.currency.label,
              code: this.code,
              address: this.address,
              remark: this.remark,
              issue_date: this.issue_date,
              api: this.api
            }

            const api = this.api;
            
            if(this.dataId !== null && this.dataId >= 0) {
              this.$vs.notify({
                title: 'Message',
                text: 'msg.updated',
                iconPack: 'feather',
                icon: 'icon-check-circle',
                color: 'primary'
              })
              this.$store.dispatch("dataList/updateBankAccount", obj).catch(err => { console.error(err) })
            }else{
              delete obj.id
              obj.popularity = 0
              this.$store.dispatch("dataList/addItem", obj).catch(err => { console.error(err) })
              this.$vs.notify({
                title: 'Message',
                text: 'msg.saved',
                iconPack: 'feather',
                icon: 'icon-check-circle',
                color: 'primary'
              })
            }
            this.$emit('closeSidebar')
            this.initValues()
          }
      })
    }
  },
  components: {
    'v-select': vSelect,
    VuePerfectScrollbar,
  }
}
</script>

<style lang="scss" scoped>
.add-new-data-sidebar {
  font-family: var(--font-base);
  ::v-deep .vs-sidebar--background {
    z-index: 52010;
  }

  ::v-deep .vs-sidebar {
    z-index: 52010;
    width: 400px;
    max-width: 90vw;

    .img-upload {
      margin-top: 2rem;

      .con-img-upload {
        padding: 0;
      }

      .con-input-upload {
        width: 100%;
        margin: 0;
      }
    }
  }
}

.scroll-area--data-list-add-new {
    // height: calc(var(--vh, 1vh) * 100 - 4.3rem);
    height: calc(var(--vh, 1vh) * 100 - 16px - 45px - 82px);
}
</style>
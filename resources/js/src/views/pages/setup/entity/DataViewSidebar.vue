<template>
  <vs-sidebar click-not-close position-right parent="body" default-index="1" color="primary" class="add-new-data-sidebar items-no-padding" spacer v-model="isSidebarActiveLocal">
    <div class="mt-6 flex items-center justify-between px-6">
        <h4>{{ Object.entries(this.data).length === 0 ? "ADD NEW" : "UPDATE" }}</h4>
        <feather-icon icon="XIcon" @click.stop="isSidebarActiveLocal = false" class="cursor-pointer"></feather-icon>
    </div>
    <vs-divider class="mb-0"></vs-divider>

    <VuePerfectScrollbar class="scroll-area--data-list-add-new" :settings="settings" :key="$vs.rtl">
      <div class="p-6">

        <!-- NAME -->
      <vs-input :label="$tt('ឈ្មោះអង្គភាព')" v-model="dataName" class="mt-5 w-full" name="name" v-validate="'required'" />
        <span class="text-danger text-sm" v-show="errors.has('name')">{{ $tt(errors.first('name')) }}</span>

        <vs-input :label="$tt('លេខ​កូដ')" v-model="code" class="mt-5 w-full" name="code" v-validate="'required'" />
        <span class="text-danger text-sm" v-show="errors.has('code')">{{ $tt(errors.first('code')) }}</span>

        <vs-input :label="$tt('ពណ៌តំណាង')" v-model="color" class="mt-5 w-full" name="color" v-validate="'required'" />
        <span class="text-danger text-sm" v-show="errors.has('color')">{{ $tt(errors.first('color')) }}</span>

        <vs-input :label="$tt('អក្សកាត់')" v-model="abbreviation" class="mt-5 w-full" name="abbreviation" v-validate="'required'" />
        <span class="text-danger text-sm" v-show="errors.has('abbreviation')">{{ $tt(errors.first('abbreviation')) }}</span>

        <vs-input type="number" :label="$tt('លំដាប់លេខរៀង')" v-model="order_level" class="mt-5 w-full" name="order_level" v-validate="'required'" />
        <span class="text-danger text-sm" v-show="errors.has('order_level')">{{ $tt(errors.first('order_level')) }}</span>

        <vs-input type="number" :label="$tt('កំណត់ចំនួនសមាជិកបើកផ្តល់')" v-model="limit_member" class="mt-5 w-full" name="limit_member" v-validate="'required'" />
        <span class="text-danger text-sm" v-show="errors.has('limit_member')">{{ $tt(errors.first('limit_member')) }}</span>
        
        <!-- <vs-input label="ផ្នែក" v-model="department_id" class="mt-5 w-full" name="department_id" v-validate="'required'" />
        <span class="text-danger text-sm" v-show="errors.has('department_id')">{{ $tt(errors.first('department_id')) }}</span> -->
        <!-- <vs-input label="វិស័យ" v-model="sector_id" class="mt-5 w-full" name="sector_id" v-validate="'required'" />
        <span class="text-danger text-sm" v-show="errors.has('sector_id')">{{ $tt(errors.first('sector_id')) }}</span> -->
        
        <div class="vx-col md:w-1/1 w-full mt-5">
          <label for="" class="vs-input--label">{{ $tt('ផ្នែកទទួលខុសត្រូវ') }}</label>
          <v-select name="department" v-model="department" :options="departments" :dir="$vs.rtl ? 'rtl' : 'ltr'" v-validate="'required'" />
          <span class="text-danger">{{ $tt(errors.first('department')) }}</span>
        </div>

        <div class="vx-col md:w-1/1 w-full mt-5">
          <label for="" class="vs-input--label">វិស័យ</label>
          <v-select name="sector" v-model="sector" :options="sectors" :dir="$vs.rtl ? 'rtl' : 'ltr'" v-validate="'required'" />
          <span class="text-danger">{{ $tt(errors.first('sector')) }}</span>
        </div>
        
        <div class="vx-col md:w-1/1 w-full mt-5">
          <label class="text-sm">ស្ថានភាព</label>
          <div class="mt-2">
          <vs-radio v-model="is_active" vs-value="1" class="mr-4">{{ $tt('សកម្ម') }}</vs-radio>
          <vs-radio v-model="is_active" vs-value="0" class="mr-4">{{ $tt('អសកម្ម') }}</vs-radio>
          </div>
        </div>
        <!-- <span>
          <vs-input​ type="number" :label="$tt('លំដាប់លេខរៀង')" v-model="order_level" class="mt-5 w-full" name="order_level" v-validate="'required'" />
          <span class="text-danger text-sm" v-show="errors.has('order_level')">{{ $tt(errors.first('order_level')) }}</span>
        </span> -->


        <!-- <div class="vx-col md:w-1/1 w-full mt-5">
          <label for="" class="vs-input--label">អគ្គរងទទួលបន្ទុក</label>
          <v-select name="deputy_commissionerofficer" multiple v-model="deputy_commissionerofficer" :options="departments" :dir="$vs.rtl ? 'rtl' : 'ltr'" v-validate="'required'" />
          <span class="text-danger">{{ $tt(errors.first('deputy_commissionerofficer')) }}</span>
        </div>

        <div class="vx-col md:w-1/1 w-full mt-5">
          <label for="" class="vs-input--label">មន្ត្រីជំនាញ</label>
          <v-select name="professional_offier" multiple v-model="professional_offier" :options="sectors" :dir="$vs.rtl ? 'rtl' : 'ltr'" v-validate="'required'" />
          <span class="text-danger">{{ $tt(errors.first('professional_offier')) }}</span>
        </div> -->


      </div>
    </VuePerfectScrollbar>

    <template #footer><div class="flex flex-wrap items-center p-6">
      <vs-button color="primary" type="filled" class="mr-6" @click="submitData" :disabled="!isFormValid">{{ $t('btn_save') }}</vs-button>
      <vs-button color="dark" type="border" @click="isSidebarActiveLocal = false">{{ $t('btn_cancel') }}</vs-button>
    </div></template>
  </vs-sidebar>
</template>

<script>
import VuePerfectScrollbar from '@/components/PerfectScrollbar.vue'
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
        let { id, name, sector, is_active, department, limit_member, code, color, abbreviation, deputy_commissionerofficer, professional_offier, order_level } = JSON.parse(JSON.stringify(this.data))
        this.dataId = id
        this.dataName = name
        this.is_active = is_active
        this.sector = sector
        this.department = department
        this.limit_member = limit_member
        this.code = code
        this.color = color
        this.order_level = order_level
        this.abbreviation = abbreviation
        this.deputy_commissionerofficer = this.data.deputy_commissionerofficer
        this.professional_offier = this.data.professional_offier
        this.initValues()
      }
      // Object.entries(this.data).length === 0 ? this.initValues() : { this.dataId, this.dataName, this.dataCategory, this.dataOrder_status, this.dataPrice } = JSON.parse(JSON.stringify(this.data))
    }
  },
  data() {
    return {
      api: "/api/v1/entity",
      dataId: null,
      dataName: "",
      sector: "",
      department: "",
      code: "",
      color: "",
      order_level: "",
      abbreviation: "",
      is_active: 0,
      limit_member: 0,
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
      departments: [],
      sectors: [],
      deputy_commissionerofficer: [],
      professional_offier: []
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
      return !this.errors.any() && this.dataName
    }
  },
  created() {
    this.getDepartments();
    this.getSectors();
  },
  methods: {
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
              name: this.dataName,
              limit_member: this.limit_member,
              sector: this.sector,
              department: this.department,
              code: this.code,
              color: this.color,
              order_level: this.order_level,
              abbreviation: this.abbreviation,
              is_active: this.is_active,
              // deputy_commissionerofficer: this.deputy_commissionerofficer,
              // professional_offier: this.professional_offier,
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
              this.$store.dispatch("dataList/updateEntity", obj).catch(err => { console.error(err) })
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
    },
    getDepartments(){
      return new Promise((resolve, reject) => {
        axios.get("/api/v1/department?offset=0&limit=100&sort=id&order=desc")
            .then((response) => {
            let departments = response["data"]["data"];
            for(let i = 0; i < departments.length; i++){
                const _data = {
                    id: departments[i]["id"],
                    label: departments[i]["name"]
                };
                this.departments.push(_data);
            }
        }).catch((error) => { reject(error) })
      })
    },
    getSectors(){
      return new Promise((resolve, reject) => {
        axios.get("/api/v1/sector?offset=0&limit=100&sort=id&order=desc")
            .then((response) => {
            let sectors = response["data"]["data"];
            for(let i = 0; i < sectors.length; i++){
                const _data = {
                    id: sectors[i]["id"],
                    label: sectors[i]["name"]
                };
                this.sectors.push(_data);
            }
        }).catch((error) => { reject(error) })
      })
    },
  },
  components: {
    vSelect,
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
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
        <vs-input label="ឈ្មោះរូបីយវត្ថុ" v-model="name" class="mt-5 w-full" name="name" v-validate="'required'" />
        <span class="text-danger text-sm" v-show="errors.has('name')">{{ errors.first('name') }}</span>

        <!-- Code -->
        <vs-input label="កូដ" v-model="code" class="mt-5 w-full" name="code" />

        <!-- Rate -->
        <vs-input label="អត្រាប្តូរប្រាក់" v-model="rate" class="mt-5 w-full" name="rate" v-validate="'required'" />
        <span class="text-danger text-sm" v-show="errors.has('rate')">{{ errors.first('rate') }}</span>

        <!-- Abbreviation -->
        <vs-input label="អក្សរកាត់" v-model="abbreviation" class="mt-5 w-full" name="abbreviation"/>

        <!-- Symbol Left -->
        <vs-input label="និបិត្តសញ្ញាខាងឆ្វេង" v-model="symbol_left" class="mt-5 w-full" name="symbol_left"/>

        <!-- Symbol Right -->
        <vs-input label="និបិត្តសញ្ញាខាងស្តាំ" v-model="symbol_right" class="mt-5 w-full" name="symbol_right"/>

        <!-- Remark -->
        <vs-input label="សំគាល់" v-model="remark" class="mt-5 w-full" name="remark" />

      </div>
    </VuePerfectScrollbar>

    <div class="flex flex-wrap items-center p-6" slot="footer">
      <vs-button class="mr-6" @click="submitData" :disabled="!isFormValid">Save</vs-button>
      <vs-button type="border" color="danger" @click="isSidebarActiveLocal = false">Cancel</vs-button>
    </div>
  </vs-sidebar>
</template>

<script>
import VuePerfectScrollbar from 'vue-perfect-scrollbar'

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
      console.log("sidarbar", this.data);
      if(!val) return
      if(Object.entries(this.data).length === 0) {
        this.initValues()
        this.$validator.reset()
      }else {
        let { id, name, code, rate, symbol_left, symbol_right, abbreviation, remark } = JSON.parse(JSON.stringify(this.data))
        this.dataId = id
        this.name = name
        this.rate = rate
        this.code = code
        this.symbol_left = symbol_left
        this.symbol_right = symbol_right
        this.abbreviation = abbreviation
        this.remark = remark
        this.initValues()
      }
      // Object.entries(this.data).length === 0 ? this.initValues() : { this.dataId, this.name, this.dataCategory, this.dataOrder_status, this.dataPrice } = JSON.parse(JSON.stringify(this.data))
    }
  },
  data() {
    return {
      api: "/api/v1/currency",
      dataId: null,
      name: "",
      code: "",
      rate: "",
      abbreviation: "",
      symbol_left: "",
      symbol_right: "",
      remark: "",
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
      return !this.errors.any() && this.name
    }
  },
  methods: {
    initValues() {
      if(this.data.id) return
        this.dataId = null
        this.name = ""
        this.rate = ""
        this.code = ""
        this.symbol_left = ""
        this.symbol_right = ""
        this.abbreviation = ""
        this.remark = ""
    },
    submitData() {
      this.$validator.validateAll().then(result => {
          if (result) {
            const obj = {
              id: this.dataId,
              name: this.name,
              code: this.code,
              rate: this.rate,
              abbreviation: this.abbreviation,
              symbol_left: this.symbol_left,
              symbol_right: this.symbol_right,
              remark: this.remark,
              api: this.api
            }

            const api = this.api;
            
            if(this.dataId !== null && this.dataId >= 0) {
              this.$vs.notify({
                title: 'Message',
                text: 'ពត៌មានត្រូវបានកែប្រែ.',
                iconPack: 'feather',
                icon: 'icon-check-circle',
                color: 'primary'
              })
              this.$store.dispatch("dataList/updateExchangeRate", obj).catch(err => { console.error(err) })
            }else{
              delete obj.id
              obj.popularity = 0
              this.$store.dispatch("dataList/addItem", obj).catch(err => { console.error(err) })
              this.$vs.notify({
                title: 'Message',
                text: 'ពត៌មានត្រូវបានបង្កើត.',
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
    VuePerfectScrollbar,
  }
}
</script>

<style lang="scss" scoped>
.add-new-data-sidebar {
font-family: 'Khmer MEF1';
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
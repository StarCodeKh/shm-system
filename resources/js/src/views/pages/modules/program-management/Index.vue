<template>
  <div id="invoice-page">
    <!-- <modal-objective ref="refModalObjective"></modal-objective>
    <modal-program ref="refModalProgram"></modal-program> -->
    <d-modal-form @clicked="initTableData" ref="refModalForm" :data="data" :api="api" :formAttributes="formAttributes"
      :rowDisplay="rowDisplay" :dataAttributes="dataAttributes" :title="$t(title)"></d-modal-form>

    <!-- <vs-popup classContent="popup-example" title="Lorem ipsum dolor sit amet" :active.sync="popupActive2">
      <vs-input class="inputx mb-3" placeholder="Placeholder" />
      <vs-input disabled class="inputx mb-3" placeholder="Disabled" />
      <vs-button @click="popupActive3=true" color="primary" type="filled">Open Inner Popup</vs-button>
      <vs-popup title="Inner popup" :active.sync="popupActive3">
        <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.</p>
      </vs-popup>
    </vs-popup> -->

    <div class="flex flex-wrap items-center justify-between">
      <vx-input-group class="mb-base mr-3">
        <!-- <vs-input v-model="mailTo" /> -->
        <template slot="append">
          <div class="append-text btn-addon">
            <vs-button type="border" @click="mailTo = ''" class="whitespace-no-wrap">{{ $t("Search") }}</vs-button>
          </div>
        </template>
      </vx-input-group>

      <div class="items-center pt-4 pb-3">
        <div class="vx-row mr-1">
          <div
            class="btn-add-new p-3 mr-2 rounded-sm cursor-pointer text-right flex items-center justify-center text-lg font-medium text-base text-primary border border-solid border-default">
            <feather-icon icon="FilterIcon" svgClasses="h-4 w-4" />
          </div>

          <div
            class="btn-add-new p-3 mr-2 rounded-sm cursor-pointer text-right flex items-center justify-center text-lg font-medium text-base text-primary border border-solid border-default">
            <feather-icon icon="FileIcon" svgClasses="h-4 w-4" />
          </div>

          <div
            class="btn-add-new p-3 mr-1 rounded-sm cursor-pointer text-right flex items-center justify-center text-lg font-medium text-base text-primary border border-solid border-primary"
            @click=openForm(1)>
            <vx-tooltip color="success" text="បង្កើតគោលបំណង">
              <feather-icon icon="PlusIcon" svgClasses="h-4 w-4" />
              <span class="ml-2 text-base text-primary">{{ $t("AddNew") }}</span>
            </vx-tooltip>
          </div>
        </div>
      </div>
    </div>

    <!-- Policy objectives, each with its programs (loaded from the database) -->
    <section v-for="objective in visibleObjectives" :key="objective.id" class="pm-objective">
      <header class="pm-objective__head">
        <div class="pm-objective__title">
          <span class="pm-objective__code">{{ objective.code }}</span>
          <h4>{{ localName(objective) }}</h4>
        </div>
        <vs-button size="small" color="primary" type="border" icon-pack="feather" icon="icon-plus" @click="openForm(2)">
          {{ $t('AddNew') }}
        </vs-button>
      </header>

      <div class="pm-programs">
        <button v-for="program in objective.programs" :key="program.id" type="button" class="pm-program"
          :class="{ 'is-active': isVisible }" @click="isVisible = !isVisible">
          <span class="pm-program__code">{{ program.code }}</span>
          <span class="pm-program__name">{{ localName(program) }}</span>
          <span class="pm-program__count">
            <feather-icon icon="LayersIcon" svgClasses="h-4 w-4" />
            {{ $tt('អនុកម្មវិធី') }}: <b>{{ program.subprogram_count }}</b>
          </span>
        </button>
      </div>
    </section>

    <div v-if="loaded && !visibleObjectives.length" class="pm-empty">{{ $t('dt_empty') }}</div>

    <table-expandable-data v-show="isVisible"></table-expandable-data>

    <vs-navbar text-color="rgba(255,255,255,.6)" active-text-color="rgba(255,255,255,1)" :type="type" color="#F4F4F4"
      v-model="activeItem" style="box-shadow: 1px 5px 15px;" class="p-2 mb-6">

      <div slot="title">
        <vs-navbar-title>
          <span>គោលបំណងនយោបាយទី២ៈ ការគ្រប់គ្រងប្រព័ន្ធហិរញ្ញវត្ថុ</span>
        </vs-navbar-title>
      </div>

      <vs-navbar-item index="0">
        <div class="vx-row mr-1">
          <div
            class="btn-add-new p-3 mr-2 rounded-sm cursor-pointer text-right flex items-center justify-center text-lg font-medium text-base text-primary border border-solid border-primary">
            <feather-icon icon="EditIcon" svgClasses="h-4 w-4" />
          </div>

          <div
            class="btn-add-new p-3 mr-1 rounded-sm cursor-pointer text-right flex items-center justify-center text-lg font-medium text-base text-primary border border-solid border-primary"
            @click=openForm(2)>
            <feather-icon icon="PlusIcon" svgClasses="h-4 w-4" />
          </div>
        </div>
      </vs-navbar-item>
    </vs-navbar>

    <div class="vx-row">
      <!-- Background Color -->
      <div class="vx-col w-full sm:w-1/4 md:w-1/4 lg:w-1/4 xl:w-1/4 mb-base">
        <vx-card title="ចំណាត់ថ្នាក់ៈ 103" title-color="#fff" card-background="#FEC8FF" content-color="#fff"
          @click="isVisible = !isVisible">
          <center>
            <h5 class="pb-4" style="line-height: 30px;"><b>កម្មវិធីទី៣ <br />ការគ្រប់គ្រងចំណូលសាធារណៈ
                និងទ្រព្យសម្បត្តិរដ្ឋ</b></h5>
          </center>
          <p class="mb-3​ text-right">សរុបអនុកម្មវិធី</p>
        </vx-card>
      </div>
      <!-- Background Color -->
      <div class="vx-col w-full sm:w-1/4 md:w-1/4 lg:w-1/4 xl:w-1/4 mb-base">
        <vx-card title="ចំណាត់ថ្នាក់ៈ 104" title-color="#fff" card-background="#AFC817" content-color="#fff"
          @click="isVisible = !isVisible">
          <center>
            <h5 class="pb-4" style="line-height: 30px;"><b>កម្មវិធីទី៤ <br />ការគ្រប់គ្រងចំណាយសាធារណៈ
                និងប្រព័ន្ធហិរញ្ញវត្ថុសាធារណៈ</b></h5>
          </center>
          <p class="mb-3​ text-right">សរុបអនុកម្មវិធី</p>
        </vx-card>
      </div>

      <!-- Background Color -->
      <div class="vx-col w-full sm:w-1/4 md:w-1/4 lg:w-1/4 xl:w-1/4 mb-base">
        <vx-card title="ចំណាត់ថ្នាក់ៈ 105" title-color="#fff" card-background="#7A97FB" content-color="#fff"
          @click="isVisible = !isVisible">
          <center>
            <h5 class="pb-4" style="line-height: 30px;"><b>កម្មវិធីទី៥ៈ <br />កិច្ចការរដ្ឋបាល
                និងអភិវឌ្ឍស្ថាប័ន</b><br /><br /></h5>
          </center>
          <p class="mb-3​ text-right">សរុបអនុកម្មវិធី</p>
        </vx-card>
      </div>
    </div>

    <table-expandable-data v-show="isVisible"></table-expandable-data>
  </div>
</template>

<script>
import TableExpandableData from './_TableExpandableData.vue'
import DModalForm from '@/views/form-builder/DModalForm.vue';
import axios from "@/axios.js";
import apiConfig from "@/apiConfig.js";
import { ref } from 'vue';

export default {
  data() {
    return {
      popupActive2: false,
      popupActive3: false,
      activeItem: "",
      type: "",
      value1: "",
      value2: "",
      isVisible: false,
      objectives: [],
      loaded: false,
      colorx: '#5A8DEE',
      mailTo: "",
      selected: [],
      itemsPerPage: 4,
      isMounted: false,
      addNewDataSidebar: false,
      sidebarData: {},
      title: "",
      api: apiConfig._apiProgram,
      dataAttributes: {
        backgroundColor: "warning",
        tableStyle: 2,
        page_number: 1,
        offset: 0,
        dataGrid: "row",
        popupFullscreen: false
      },
      data: {
        data: [
          {
            id: 1,
            name_en: "#120 - គោលបំណងទី១",
            name_kh: "#120 - គោលបំណងទី១",
            children: [
              {
                id: 1,
                code: "001",
                name_en: "កម្មវិធីទី១",
                name_kh: "កម្មវិធីទី១",
                // objective: "គោលបំណងគោលនយោបាយទី១",
                entity_id: "ឯកឧត្តម ទទួលបន្ទុក",
                entity_member_id: "ឈ្មោះអង្គភាពទទួលបន្ទុក",
                order_level: 1
              },
              {
                id: 2,
                code: "002",
                name_en: "កម្មវិធីទី១",
                name_kh: "កម្មវិធីទី១",
                // objective: "គោលបំណងគោលនយោបាយទី១",
                entity_id: "ឯកឧត្តម ទទួលបន្ទុក",
                entity_member_id: "ឈ្មោះអង្គភាពទទួលបន្ទុក",
                order_level: 1
              },
              {
                id: 3,
                code: "003",
                name_en: "កម្មវិធីទី១",
                name_kh: "កម្មវិធីទី១",
                // objective: "គោលបំណងគោលនយោបាយទី១",
                entity_id: "ឈ្មោះអង្គភាពទទួលបន្ទុក",
                entity_member_id: "ឯកឧត្តម ទទួលបន្ទុក",
                order_level: 1
              },
            ]
          }
        ],
        objective_id: [
          {
            "label": "គោលបំណងទី១",
            "value": 1,
          },
          {
            "label": "គោលបំណងទី២",
            "value": 2,
          }
        ],
        entity_id: [
          {
            "label": "អង្គភាពទី១",
            "value": 1,
          },
          {
            "label": "អង្គភាពទី២",
            "value": 2,
          }
        ],
        entity_member_id: [
          {
            "label": "សមាជិកទី១",
            "value": 1,
          },
          {
            "label": "សមាជិកទី២",
            "value": 2,
          }
        ],
        limit: 10,
        total: 3
      },
      formAttributes: {},
      formAttributeObjectives: [
        {
          name: "name_en",
          type: "text",
          required: true
        },
        {
          name: "name_kh",
          type: "text",
          required: true
        },
        {
          name: "entity_id",
          type: "select",
          required: true,
          options: [
            {
              "label": "អង្គភាពទី១",
              "value": 1,
            },
            {
              "label": "អង្គភាពទី២",
              "value": 2,
            }
          ],
        },
        {
          name: "entity_member_id",
          type: "select",
          required: true,
          options: [
            {
              "label": "បុគ្គលទិ១",
              "value": 1,
            },
            {
              "label": "បុគ្គលទិ២",
              "value": 2,
            }
          ],
        },
        {
          name: "order_level",
          type: "number",
          required: false
        },
        {
          name: "remark",
          type: "textarea",
          required: false
        },
      ],
      formAttributePrograms: [
        {
          name: "objective_id",
          type: "select",
          required: true,
          options: []
        },
        {
          name: "name_en",
          type: "text",
          required: true
        },
        {
          name: "name_kh",
          type: "text",
          required: true
        },
        {
          name: "entity_id",
          type: "select",
          required: true,
          options: [
            {
              "label": "អង្គភាពទី១",
              "value": 1,
            },
            {
              "label": "អង្គភាពទី២",
              "value": 2,
            }
          ],
        },
        {
          name: "entity_member_id",
          type: "select",
          required: true,
          options: [
            {
              "label": "បុគ្គលទិ១",
              "value": 1,
            },
            {
              "label": "បុគ្គលទិ២",
              "value": 2,
            }
          ],
        },
        {
          name: "order_level",
          type: "number",
          required: false
        },
        {
          name: "remark",
          type: "textarea",
          required: false
        },
      ],
      formAttributeSubPrograms: [
        {
          name: "objective_id",
          type: "select",
          required: true,
          options: []
        },
        {
          name: "name_en",
          type: "text",
          required: true
        },
        {
          name: "name_kh",
          type: "text",
          required: true
        },
        {
          name: "entity_id",
          type: "select",
          required: true,
          options: [
            {
              "label": "អង្គភាពទី១",
              "value": 1,
            },
            {
              "label": "អង្គភាពទី២",
              "value": 2,
            }
          ],
        },
        {
          name: "entity_member_id",
          type: "select",
          required: true,
          options: [
            {
              "label": "បុគ្គលទិ១",
              "value": 1,
            },
            {
              "label": "បុគ្គលទិ២",
              "value": 2,
            }
          ],
        },
        {
          name: "order_level",
          type: "number",
          required: false
        },
        {
          name: "remark",
          type: "textarea",
          required: false
        },
      ],
      formAttributeClusterActivies: [
        {
          name: "objective_id",
          type: "select",
          required: true,
          options: []
        },
        {
          name: "name_en",
          type: "text",
          required: true
        },
        {
          name: "name_kh",
          type: "text",
          required: true
        },
        {
          name: "entity_id",
          type: "select",
          required: true,
          options: [
            {
              "label": "អង្គភាពទី១",
              "value": 1,
            },
            {
              "label": "អង្គភាពទី២",
              "value": 2,
            }
          ],
        },
        {
          name: "entity_member_id",
          type: "select",
          required: true,
          options: [
            {
              "label": "បុគ្គលទិ១",
              "value": 1,
            },
            {
              "label": "បុគ្គលទិ២",
              "value": 2,
            }
          ],
        },
        {
          name: "order_level",
          type: "number",
          required: false
        },
        {
          name: "remark",
          type: "textarea",
          required: false
        },
      ],
      formAttributeActivies: [
        {
          name: "objective_id",
          type: "select",
          required: true,
          options: []
        },
        {
          name: "name_en",
          type: "text",
          required: true
        },
        {
          name: "name_kh",
          type: "text",
          required: true
        },
        {
          name: "entity_id",
          type: "select",
          required: true,
          options: [
            {
              "label": "អង្គភាពទី១",
              "value": 1,
            },
            {
              "label": "អង្គភាពទី២",
              "value": 2,
            }
          ],
        },
        {
          name: "entity_member_id",
          type: "select",
          required: true,
          options: [
            {
              "label": "បុគ្គលទិ១",
              "value": 1,
            },
            {
              "label": "បុគ្គលទិ២",
              "value": 2,
            }
          ],
        },
        {
          name: "order_level",
          type: "number",
          required: false
        },
        {
          name: "remark",
          type: "textarea",
          required: false
        },
      ],
      rowDisplay: "2grid", //1grid, 2grid, 3grid, 4grid
      dataFields: [],
      dataInfo: {}
    }
  },
  computed: {
    currentPage() {
      if (this.isMounted) {
        return this.$refs.table.currentx
      }
      return 0
    },
    products() {
      return this.$store.state.dataList.products
    },
    queriedItems() {
      return this.$refs.table ? this.$refs.table.queriedResults.length : this.products.length
    }
  },
  methods: {
    loadOverview() {
      return axios.get(apiConfig._apiProgramOverview)
        .then(response => { this.objectives = (response.data && response.data.data) || [] })
        .catch(() => this.$vs.notify({ title: 'Message', text: 'msg.load_failed', color: 'danger' }))
        .finally(() => { this.loaded = true })
    },
    // Name in the current language, falling back to the other one
    localName(item) {
      return (this.$i18n.locale === 'en' ? item.name_en || item.name_kh : item.name_kh || item.name_en) || ''
    },
    openForm(param) {
      // If param 1=Objective, 2=Program, 3=SubProgram, 4=ClusterActivity, 5=Activity
      switch (param) {
        case 1:
          this.title = "ProgramObjectiveForm";
          this.formAttributes = this.formAttributeObjectives;
          break;
        case 2:
          this.title = "ProgramForm";
          this.formAttributes = this.formAttributePrograms;
          break;
        case 3:
          this.title = "ProgramSubProgramForm";
          this.formAttributes = this.formAttributeSubPrograms;
          break;
        case 4:
          this.title = "ProgramClusterActivityForm";
          this.formAttributes = this.formAttributeClusterActivies;
          break;
        case 5:
          this.title = "ProgramActivityForm";
          this.formAttributes = this.formAttributeActivies;
          break;
        default:
          this.title = "ProgramObjectiveForm";
          this.formAttributes = this.formAttributeObjectives;
          break;
      }
      if (param == 1) {
        this.formAttributes = this.formAttributeObjectives;
      } else {
        this.formAttributes = this.formAttributePrograms;
      }
      this.$refs.refModalForm.openNewForm();
      this.$emit("emitDataForm", "test change form attribute");
    },
    operFormObjective() {
      this.$refs.refModalObjective.initObjective();
    },
    operFormProgram() {
      this.$refs.refModalProgram.initProgram();
    },
    printInvoice() {
      window.print()
    },
    // addNewData() {
    // this.sidebarData = {}
    // this.toggleDataSidebar(true)
    // },
    deleteData(id) {
      this.$store.dispatch("dataList/removeItem", id).catch(err => { console.error(err) })
    },
    editData(data) {
      // this.sidebarData = JSON.parse(JSON.stringify(this.blankData))
      this.sidebarData = data
      this.toggleDataSidebar(true)
    },
    getOrderStatusColor(status) {
      if (status == 'on_hold') return "warning"
      if (status == 'delivered') return "success"
      if (status == 'canceled') return "danger"
      return "primary"
    },
    getPopularityColor(num) {
      if (num > 90) return "success"
      if (num > 70) return "primary"
      if (num >= 50) return "warning"
      if (num < 50) return "danger"
      return "primary"
    },
    toggleDataSidebar(val = false) {
      this.addNewDataSidebar = val
    },

    initTableData() {
      let _search_params = {
        pageNum: this.current_page,
        searchFields: []
      }
      this.$emit('clicked', _search_params);
    },
    searchQuery(_search_fields) {
      let _search_params = {
        pageNum: this.current_page,
        searchFields: _search_fields
      }
      this.$emit('clicked', _search_params);
    },
  },
  components: {
    TableExpandableData,
    DModalForm
  },
  computed: {
    // Objectives without a name and without programs are left out
    visibleObjectives() {
      return this.objectives.filter(o => (o.name_kh || o.name_en) || (o.programs && o.programs.length))
    },
  },
  mounted() {
    this.$emit("setAppClasses", "invoice-page")
    this.loadOverview()
  }
}
</script>

<style lang="scss">
@media print {
  .invoice-page {
    * {
      visibility: hidden;
    }

    #content-area {
      margin: 0 !important;
    }

    #invoice-container,
    #invoice-container * {
      visibility: visible;
    }

    #invoice-container {
      position: absolute;
      left: 0;
      top: 0;
      box-shadow: none;
    }
  }
}
</style>
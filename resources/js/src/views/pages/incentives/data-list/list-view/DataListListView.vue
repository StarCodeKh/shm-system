<template>
  <div id="data-list-list-view" class="data-list-container">

    <data-view-sidebar :isSidebarActive="addNewDataSidebar" @closeSidebar="toggleDataSidebar" :data="sidebarData" />

    <vs-table ref="table" multiple v-model="selected" pagination :max-items="itemsPerPage" search :data="dataObjects">

      <div slot="header" class="flex flex-wrap-reverse items-center flex-grow justify-between">

        <div class="flex flex-wrap-reverse items-center data-list-btn-container">

          <!-- ACTION - DROPDOWN -->
          <vs-dropdown vs-trigger-click class="dd-actions cursor-pointer mr-4 mb-4">

            <div class="p-4 shadow-drop rounded-lg d-theme-dark-bg cursor-pointer flex items-center justify-center text-lg font-medium w-32 w-full">
              <span class="mr-2">Actions</span>
              <feather-icon icon="ChevronDownIcon" svgClasses="h-4 w-4" />
            </div>

            <vs-dropdown-menu>

              <vs-dropdown-item>
                <span class="flex items-center">
                  <feather-icon icon="TrashIcon" svgClasses="h-4 w-4" class="mr-2" />
                  <span>Delete</span>
                </span>
              </vs-dropdown-item>

              <vs-dropdown-item>
                <span class="flex items-center">
                  <feather-icon icon="ArchiveIcon" svgClasses="h-4 w-4" class="mr-2" />
                  <span>Archive</span>
                </span>
              </vs-dropdown-item>

              <vs-dropdown-item>
                <span class="flex items-center">
                  <feather-icon icon="FileIcon" svgClasses="h-4 w-4" class="mr-2" />
                  <span>Print</span>
                </span>
              </vs-dropdown-item>

              <vs-dropdown-item>
                <span class="flex items-center">
                  <feather-icon icon="SaveIcon" svgClasses="h-4 w-4" class="mr-2" />
                  <span>Another Action</span>
                </span>
              </vs-dropdown-item>

            </vs-dropdown-menu>
          </vs-dropdown>

          <!-- ADD NEW -->
          <div class="btn-add-new p-3 mb-4 mr-4 rounded-lg cursor-pointer flex items-center justify-center text-lg font-medium text-base text-primary border border-solid border-primary" @click="addNewData">
              <feather-icon icon="PlusIcon" svgClasses="h-4 w-4" />
              <span class="ml-2 text-base text-primary">បង្កើតថ្មី</span>
          </div>
        </div>

        <!-- ITEMS PER PAGE -->
        <vs-dropdown vs-trigger-click class="cursor-pointer mb-4 mr-4 items-per-page-handler">
          <div class="p-4 border border-solid d-theme-border-grey-light rounded-full d-theme-dark-bg cursor-pointer flex items-center justify-between font-medium">
            <span class="mr-2">{{ currentPage * itemsPerPage - (itemsPerPage - 1) }} - {{ dataObjects.length - currentPage * itemsPerPage > 0 ? currentPage * itemsPerPage : dataObjects.length }} of {{ queriedItems }}</span>
            <feather-icon icon="ChevronDownIcon" svgClasses="h-4 w-4" />
          </div>
          <!-- <vs-button class="btn-drop" type="line" color="primary" icon-pack="feather" icon="icon-chevron-down"></vs-button> -->
          <vs-dropdown-menu>

            <vs-dropdown-item @click="itemsPerPage=4">
              <span>4</span>
            </vs-dropdown-item>
            <vs-dropdown-item @click="itemsPerPage=10">
              <span>10</span>
            </vs-dropdown-item>
            <vs-dropdown-item @click="itemsPerPage=15">
              <span>15</span>
            </vs-dropdown-item>
            <vs-dropdown-item @click="itemsPerPage=20">
              <span>20</span>
            </vs-dropdown-item>
          </vs-dropdown-menu>
        </vs-dropdown>
      </div>

      <template slot="thead">
        <vs-th sort-key="no">ល.រ</vs-th>
        <vs-th sort-key="name">ចំណងជើង</vs-th>
        <vs-th sort-key="name">យោង</vs-th>
        <vs-th sort-key="role">ប្រភេទលិខិត</vs-th>
        <vs-th sort-key="bank">ថ្ងៃលិខិតចូល</vs-th>
        <!-- <vs-th sort-key="bank_acc">ថ្ងៃដាក់ចេញ</vs-th> -->
        <!-- <vs-th sort-key="total_incentive">ចំនួនប្រាក់ឧបត្ថម្ភសរុប</vs-th>
        <vs-th sort-key="total_incentive">ពន្ធសរុប</vs-th> -->
        <vs-th></vs-th>
      </template>

      <template slot-scope="{data}">
        <tbody>
          <vs-tr :data="tr" :key="indextr" v-for="(tr, indextr) in data">

            <vs-td>
              <p class="product-name font-medium truncate">{{ indextr + 1 }}</p>
            </vs-td>

            <vs-td>
              <p class="product-name font-medium truncate">{{ tr.title }} {{ tr.last_name }}</p>
            </vs-td>

            <vs-td>
              <p class="product-name font-medium truncate">{{ tr.reference }}</p>
            </vs-td>

            <vs-td>
              <p class="product-role">{{ tr.letter_type }}</p>
            </vs-td>

            <vs-td>
              <p class="product-bank">{{ new Date(tr.issue_date) | date }}</p>
            </vs-td>

            <!-- <vs-td>
              <p class="product-bank">{{tr.total_incentive}} ៛</p>
            </vs-td>

            <vs-td>
              <p class="product-bank">{{tr.total_tax}} ៛</p>
            </vs-td> -->

            <!-- <vs-td class="whitespace-no-wrap">
              <vx-tooltip text="បង្ហាញឯកសារ">
                <feather-icon icon="FileTextIcon" @click="viewReport" svgClasses="w-5 h-5 hover:text-primary stroke-current" />
              </vx-tooltip>
              <vx-tooltip text="ដោនឡូតរបាយការណ៍">
                <feather-icon icon="DownloadIcon" svgClasses="w-5 h-5 hover:text-primary stroke-current" />
              </vx-tooltip>
              <vx-tooltip text="បោះពុម្ភរបាយការណ៍">
                <feather-icon icon="PrinterIcon" svgClasses="w-5 h-5 hover:text-primary stroke-current" />
              </vx-tooltip>
            </vs-td> -->

            <!-- ACTION - DROPDOWN -->
            <vs-dropdown vs-trigger-click class="dd-actions dropdown-option cursor-pointer mr-4 mb-4">

              <div class="p-3 rounded-lg d-theme-dark-bg cursor-pointer flex items-center justify-center text-lg font-medium w-32 w-full">
                <span class="mr-2"><small>Action</small></span>
                <feather-icon icon="ChevronDownIcon" svgClasses="h-4 w-4" />
              </div>

              <vs-dropdown-menu style="width: 200px">
                <vs-dropdown-item>
                  <span class="flex items-center">
                    <feather-icon icon="BellIcon" svgClasses="h-4 w-4" class="mr-2" />
                    <span>Push Notification</span>
                  </span>
                </vs-dropdown-item>

                <vs-dropdown-item @click.stop="editData(tr)">
                  <span class="flex items-center">
                    <feather-icon icon="EditIcon" svgClasses="h-4 w-4" class="mr-2" />
                    <span>Edit</span>
                  </span>
                </vs-dropdown-item>

                <vs-dropdown-item @click.stop="popupActive=true">
                  <span class="flex items-center">
                    <feather-icon icon="TrashIcon" svgClasses="h-4 w-4" class="mr-2" />
                    <span>Delete</span>
                  </span>
                  <vs-popup class="holamundo"  title="Are you sure?" :active.sync="popupActive">
                    <p>
                      <center>
                        <vs-button @click="deleteData(tr.id); popupActive=false" color="primary" type="filled">Yes</vs-button>
                        <vs-button @click="popupActive=false" color="danger" type="filled">No</vs-button>
                      </center>
                    </p>
                  </vs-popup>
                </vs-dropdown-item>

                <vs-dropdown-item @click="viewReportIncentiveToLM(tr.id)">
                  <span class="flex items-center">
                    <feather-icon icon="FileTextIcon" svgClasses="h-4 w-4" class="mr-2" />
                    <span>Letter to LM</span>
                  </span>
                </vs-dropdown-item>

                <vs-dropdown-item @click="viewReportIncentiveAnnexToLM(tr.id)">
                  <span class="flex items-center">
                    <feather-icon icon="FileTextIcon" svgClasses="h-4 w-4" class="mr-2" />
                    <span>Annex to LM</span>
                  </span>
                </vs-dropdown-item>

                <vs-dropdown-item @click="viewReportLetterToBank(tr.id)">
                  <span class="flex items-center">
                    <feather-icon icon="FileTextIcon" svgClasses="h-4 w-4" class="mr-2" />
                    <span>Letter to Bank</span>
                  </span>
                </vs-dropdown-item>
                
                <!-- <vs-dropdown-item @click="viewReportIncentiveToBank(tr.id)">
                  <span class="flex items-center">
                    <feather-icon icon="FileTextIcon" svgClasses="h-4 w-4" class="mr-2" />
                    <span>Incentive To Bank</span>
                  </span>
                </vs-dropdown-item> -->

                <vs-dropdown-item @click="viewReportAnnex(tr.id)">
                  <span class="flex items-center">
                    <feather-icon icon="FileTextIcon" svgClasses="h-4 w-4" class="mr-2" />
                    <span>Annex to Bank</span>
                  </span>
                </vs-dropdown-item>

                <vs-dropdown-item @click="viewReportLetterToGDNT(tr.id)">
                  <span class="flex items-center">
                    <feather-icon icon="FileTextIcon" svgClasses="h-4 w-4" class="mr-2" />
                    <span>Letter to GDNT</span>
                  </span>
                </vs-dropdown-item>

                <vs-dropdown-item @click="viewReportAnnexToGDNT(tr.id)">
                  <span class="flex items-center">
                    <feather-icon icon="FileTextIcon" svgClasses="h-4 w-4" class="mr-2" />
                    <span>Annex to GDNT</span>
                  </span>
                </vs-dropdown-item>

              </vs-dropdown-menu>
            </vs-dropdown>

          </vs-tr>
        </tbody>
      </template>
    </vs-table>
  </div>
</template>

<script>
import DataViewSidebar from '../DataViewSidebar.vue'
import moduleDataList from "@/store/data-list/moduleDataList.js"

export default {
  components: {
    DataViewSidebar
  },
  data() {
    return {
      api: "/api/v1/incentive",
      selected: [],
      // dataObjects: [],
      itemsPerPage: 10,
      isMounted: false,
      popupActive: false,
      // Data Sidebar
      addNewDataSidebar: false,
      sidebarData: {},
    }
  },
  computed: {
    currentPage() {
      if(this.isMounted) {
        return this.$refs.table.currentx
      }
      return 0
    },
    dataObjects() {
      return this.$store.state.dataList.dataObjects
    },
    queriedItems() {
      return this.$refs.table ? this.$refs.table.queriedResults.length : this.dataObjects.length
    }
  },
  methods: {
    viewReportLetterToBank(incentiveId){
      this.$router.push('/report/incentive/letter_to_bank/' + incentiveId).catch(() => {})
    },
    viewReportLetterToGDNT(incentiveId){
      this.$router.push('/report/incentive/letter_to_GDNT/' + incentiveId).catch(() => {})
    },
    viewReportAnnexToGDNT(incentiveId){
      this.$router.push('/report/incentive/annex_to_GDNT/' + incentiveId).catch(() => {})
    },
    viewReportIncentiveToBank(incentiveId){
      this.$router.push('/report/incentive/incentive_to_bank/' + incentiveId).catch(() => {})
    },
    viewReportIncentiveToLM(incentiveId){
      this.$router.push('/report/incentive/incentive_to_lm/' + incentiveId).catch(() => {})
    },
    viewReportIncentiveAnnexToLM(incentiveId){
      this.$router.push('/report/incentive/incentive_annex_to_lm/' + incentiveId).catch(() => {})
    },
    viewReportAnnex(incentiveId){
      this.$router.push('/report/incentive/annex/' + incentiveId).catch(() => {})
    },
    addNewData() {
      // this.sidebarData = {}
      // this.toggleDataSidebar(true)
      this.$router.push('/incentive/form').catch(() => {})
    },
    deleteData(id) {
      const obj = {
        id: id,
        api: this.api
      }
      this.$store.dispatch("dataList/removeItem", obj).catch(err => { console.error(err) })
      this.$vs.notify({
        title: 'Message',
        text: 'ពត៌មានត្រូវបានលុប.',
        iconPack: 'feather',
        icon: 'icon-check-circle',
        color: 'primary'
      })
    },
    editData(data) {
      this.$router.push('/incentive/edit/' + data.id).catch(() => {})
      // this.sidebarData = JSON.parse(JSON.stringify(this.blankData))
      // this.sidebarData = data
      // this.toggleDataSidebar(true)
    },
    getOrderStatusColor(status) {
      if(status == 'on_hold') return "warning"
      if(status == 'delivered') return "success"
      if(status == 'canceled') return "danger"
      return "primary"
    },
    getPopularityColor(num) {
      if(num > 90) return "success"
      if(num >70) return "primary"
      if(num >= 50) return "warning"
      if(num < 50) return "danger"
      return "primary"
    },
    toggleDataSidebar(val=false) {
      this.addNewDataSidebar = val
    }
  },
  created() {
    this.$vs.loading();
    setTimeout(() => {
      this.$vs.loading.close()
    }, 700);
    // alert(this.dataObjects.length)
    if(!moduleDataList.isRegistered) {
      this.$store.registerModule('dataList', moduleDataList)
      moduleDataList.isRegistered = true
    }
    this.$store.dispatch("dataList/fetchDataList", this.api)
  },
  mounted() {
    this.isMounted = true;
    this.wasSidebarOpen = this.$store.state.reduceButton;
    this.$store.commit('TOGGLE_REDUCE_BUTTON', true)
  },
  beforeDestroy() {
    if (!this.wasSidebarOpen) this.$store.commit('TOGGLE_REDUCE_BUTTON', false)
  }
}
</script>

<style lang="scss">
.dropdown-option{
  padding-top: 10px;
}
#data-list-list-view {
  font-family: var(--font-base);
  .vs-con-table {

    /*
      Below media-queries is fix for responsiveness of action buttons
      Note: If you change action buttons or layout of this page, Please remove below style
    */
    @media (max-width: 689px) {
      .vs-table--search {
        margin-left: 0;
        max-width: unset;
        width: 100%;

        .vs-table--search-input {
          width: 100%;
        }
      }
    }

    @media (max-width: 461px) {
      .items-per-page-handler {
        display: none;
      }
    }

    @media (max-width: 341px) {
      .data-list-btn-container {
        width: 100%;

        .dd-actions,
        .btn-add-new {
          width: 100%;
          margin-right: 0 !important;
        }
      }
    }

    .product-name {
      // max-width: 23rem;
      max-width: 100% !important;
    }

    .vs-table--header {
      display: flex;
      flex-wrap: wrap;
      margin-left: 1.5rem;
      margin-right: 1.5rem;
      > span {
        display: flex;
        flex-grow: 1;
      }

      .vs-table--search{
        padding-top: 0;

        .vs-table--search-input {
          padding: 0.9rem 2.5rem;
          font-size: 1rem;

          &+i {
            left: 1rem;
          }

          &:focus+i {
            left: 1rem;
          }
        }
      }
    }

    .vs-table {
      border-collapse: separate;
      border-spacing: 0 1.3rem;
      padding: 0 1rem;

      tr{
          box-shadow: 0 4px 20px 0 rgba(0,0,0,.05);
          td{
            padding: 20px;
            &:first-child{
              border-top-left-radius: .5rem;
              border-bottom-left-radius: .5rem;
            }
            &:last-child{
              border-top-right-radius: .5rem;
              border-bottom-right-radius: .5rem;
            }
          }
          td.td-check{
            padding: 20px !important;
          }
      }
    }

    .vs-table--thead{
      th {
        padding-top: 0;
        padding-bottom: 0;

        .vs-table-text{
          text-transform: uppercase;
          font-weight: 600;
        }
      }
      th.td-check{
        padding: 0 15px !important;
      }
      tr{
        background: none;
        box-shadow: none;
      }
    }

    .vs-table--pagination {
      justify-content: center;
    }
  }
}
</style>

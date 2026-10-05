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

          <div class="btn-add-new p-3 mb-4 mr-4 rounded-lg cursor-pointer flex items-center justify-center text-lg font-medium text-base text-primary border border-solid border-primary">
            <input type="file" id="files" name="file" ref="files" multiple v-on:change="handleFileUploads($event)"/>
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
        <vs-th sort-key="name">គោត្តនាម និង នាម</vs-th>
        <!-- <vs-th sort-key="role">តួនាទី</vs-th> -->
        <vs-th sort-key="role">អង្គភាព</vs-th>
        <vs-th sort-key="bank">ធនាគារ</vs-th>
        <!-- <vs-th sort-key="bank_acc">លេខគណនី</vs-th> -->
        <vs-th sort-key="status">ស្ថានភាព</vs-th>
        <vs-th sort-key="status">គណនីយទូរស័ព្ទ</vs-th>
        <!-- <vs-th sort-key="total_incentive">ប្រាក់ឧបត្ថម្ភសរុប</vs-th> -->
        <vs-th>សកម្មភាព</vs-th>
      </template>

        <template slot-scope="{data}">
          <tbody>
            <vs-tr :data="tr" :key="indextr" v-for="(tr, indextr) in data">

              <vs-td>
                <p class="product-name font-medium truncate" @click.stop="viewOffierDetail(tr)">{{ indextr + 1 }}</p>
              </vs-td>

              <vs-td>
                <p class="product-name font-medium truncate" @click.stop="viewOffierDetail(tr)">{{ tr.first_name }} {{ tr.last_name }}​ ({{ tr.duty }})</p>
              </vs-td>

              <!-- <vs-td>
                <p class="product-role" @click.stop="viewOffierDetail(tr)">{{ tr.duty }}</p>
              </vs-td> -->

              <vs-td>
                <p class="product-role" @click.stop="viewOffierDetail(tr)">{{ tr.entity }}</p>
              </vs-td>

              <vs-td>
                <p class="product-bank" @click.stop="viewOffierDetail(tr)">
                  {{ tr.bank_name | title }}<br/>
                  <vs-chip :color="getOrderStatusColor(tr.order_status)" class="product-order-status" @click.stop="viewOffierDetail(tr)">
                    <span v-if="tr.updated_bank_acc != null">
                      {{ tr.updated_bank_acc }}
                    </span>
                    <span v-else>
                      {{ tr.bank_account_no }}
                    </span>
                  </vs-chip>
                </p>
              </vs-td>

              <!-- <vs-td>
                <vs-chip :color="getOrderStatusColor(tr.order_status)" class="product-order-status" @click.stop="viewOffierDetail(tr)">
                  <span v-if="tr.updated_bank_acc != null">
                    {{ tr.updated_bank_acc }}
                  </span>
                  <span v-else>
                    {{ tr.bank_account_no }}
                  </span>
                </vs-chip>
              </vs-td> -->

              <vs-td>
                <vs-chip v-if="tr.status == 1" :color="getOrderStatusColor('success')" class="product-order-status">សកម្ម</vs-chip>
                <vs-chip v-else :color="getOrderStatusColor('inactive')" class="product-order-status">អសកម្ម</vs-chip>
                <!-- <vs-progress :percent="Number(tr.popularity)" :color="getPopularityColor(Number(tr.popularity))" class="shadow-md" /> -->
                <!-- <p class="product-bank">សកម្ម</p> -->
              </vs-td>

              <vs-td>
                <vs-chip v-if="tr.user_id != null" :color="getOrderStatusColor('success')" class="product-order-status">មាន</vs-chip>
                <vs-chip v-else :color="getOrderStatusColor('inactive')" class="product-order-status">មិនទាន់មាន</vs-chip>
              </vs-td>

              <!-- <vs-td>
                <p class="product-price">{{ tr.price }} ១,០០០,០០០៛</p>
              </vs-td> -->

              <vs-td class="whitespace-no-wrap">
                <feather-icon icon="ActivityIcon" svgClasses="w-5 h-5 hover:text-primary stroke-current" @click.stop="viewOffierDetail(tr)" />
                <feather-icon icon="EditIcon" svgClasses="w-5 h-5 hover:text-primary stroke-current" @click.stop="editData(tr.id)" />
                <feather-icon icon="TrashIcon" svgClasses="w-5 h-5 hover:text-danger stroke-current" class="ml-2" @click.stop="popupActive=true" />
                <vs-popup class="holamundo"  title="Are you sure?" :active.sync="popupActive">
                    <p>
                      <center>
                        <vs-button @click="deleteData(tr.id); popupActive=false" color="primary" type="filled">Yes</vs-button>
                        <vs-button @click="popupActive=false" color="danger" type="filled">No</vs-button>
                      </center>
                    </p>
                </vs-popup>
              </vs-td>

            </vs-tr>
          </tbody>
        </template>
    </vs-table>
  </div>
</template>

<script>
import DataViewSidebar from '../DataViewSidebar.vue'
import moduleDataList from "@/store/data-list/moduleDataList.js"
import axios from "@/axios.js"
import vSelect from 'vue-select'
export default {
  components: {
    DataViewSidebar,
    vSelect
  },
  data() {
    return {
      api: "/api/v1/incentive_officer",
      selected: [],
      // dataObjects: [],
      itemsPerPage: 10,
      isMounted: false,
      popupActive: false,
      // Data Sidebar
      addNewDataSidebar: false,
      sidebarData: {},
      fileName: "",
      formats:["xlsx", "csv", "txt"] ,
      cellAutoWidth: true,
      selectedFormat: "xlsx",
      activePrompt: false,
      headerTitle: ["ល.រ", "គោត្តនាម និង នាម", "តួនាទី","ធនាគារ", "លេខគណនី","ស្ថានភាព",],
      headerVal: ["first_name", "first_name", "last_name","duty", "bank_name", "status"],
      users: [
        {
          "id": 1,
          "name": "Leanne Graham",
          "username": "Bret",
          "email": "Sincere@april.biz",
          "website": "hildegard.org",
        },
        {
          "id": 2,
          "name": "Ervin Howell",
          "username": "Antonette",
          "email": "Shanna@melissa.tv",
          "website": "anastasia.net",
        },
        {
          "id": 3,
          "name": "Clementine Bauch",
          "username": "Samantha",
          "email": "Nathan@yesenia.net",
          "website": "ramiro.info",
        },
        {
          "id": 4,
          "name": "Patricia Lebsack",
          "username": "Karianne",
          "email": "Julianne.OConner@kory.org",
          "website": "kale.biz",
        },
        {
          "id": 5,
          "name": "Chelsey Dietrich",
          "username": "Kamren",
          "email": "Lucio_Hettinger@annie.ca",
          "website": "demarco.info",
        },
        {
          "id": 6,
          "name": "Mrs. Dennis Schulist",
          "username": "Leopoldo_Corkery",
          "email": "Karley_Dach@jasper.info",
          "website": "ola.org",
        },
        {
          "id": 7,
          "name": "Kurtis Weissnat",
          "username": "Elwyn.Skiles",
          "email": "Telly.Hoeger@billy.biz",
          "website": "elvis.io",
        },
        {
          "id": 8,
          "name": "Nicholas Runolfsdottir V",
          "username": "Maxime_Nienow",
          "email": "Sherwood@rosamond.me",
          "website": "jacynthe.com",
        },
        {
          "id": 9,
          "name": "Glenna Reichert",
          "username": "Delphine",
          "email": "Chaim_McDermott@dana.io",
          "website": "conrad.com",
        },
        {
          "id": 10,
          "name": "Clementina DuBuque",
          "username": "Moriah.Stanton",
          "email": "Rey.Padberg@karina.biz",
          "website": "ambrose.net",
        },
      ],
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
    exportToExcel() {
      import('@/vendor/Export2Excel').then(excel => {
        const list = this.data
        const data = this.formatJson(this.headerVal, list)
        excel.export_json_to_excel({
          header: this.headerTitle,
          data,
          filename: this.fileName,
          autoWidth: this.cellAutoWidth,
          bookType: this.selectedFormat
        })
        this.clearFields()
      })
    },
    formatJson(filterVal, jsonData) {
      return jsonData.map(v => filterVal.map(j => {
        // Add col name which needs to be translated
        // if (j === 'timestamp') {
        //   return parseTime(v[j])
        // } else {
        //   return v[j]
        // }

        return v[j]
      }))
    },
    clearFields() {
      this.filename = "",
      this.cellAutoWidth = true,
      this.selectedFormat = "xlsx"
    },
    handleFileUploads(event){
      // let formData = new FormData();
      // let files = this.$refs.files.files;
      // for( var i = 0; i < files.length; i++ ){
      //     let file = files[i];
      //     formData.append('files[' + i + ']', file);
      // }
      const form = event.target;
      const file = event.target.files[0];
      const fd = new FormData();
      fd.append('file', file);
      return new Promise((resolve, reject) => {
          axios.post("/api/v1/file_upload", fd)
              .then((response) => {
              console.log("upload success");
              this.$vs.notify({
                title: 'Message',
                text: 'ឯកសារត្រូវបានរក្សារទុក',
                iconPack: 'feather',
                icon: 'icon-check-circle',
                color: 'primary'
              })
          }).catch((error) => { reject(error) })
      })
    },
    addNewData() {
      // this.sidebarData = {}
      // this.toggleDataSidebar(true)
      this.$router.push('/officer/form').catch(() => {})
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
    editData(id) {
      this.$router.push('/officer/edit/' + id).catch(() => {})
      // this.sidebarData = JSON.parse(JSON.stringify(this.blankData))
      // this.sidebarData = data
      // this.toggleDataSidebar(true)
    },
    getOrderStatusColor(status) {
      if(status == 'on_hold') return "warning"
      if(status == 'delivered') return "success"
      if(status == 'canceled') return "danger"
      if(status == 'inactive') return "danger"
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
    },
    viewOffierDetail(data){
      this.$router.push('/officer/activity/' + data.id)
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
#data-list-list-view {
  font-family: 'Khmer MEF1';
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
      max-width: 23rem;
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

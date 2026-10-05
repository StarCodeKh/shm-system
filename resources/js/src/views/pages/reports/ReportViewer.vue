<template>
    <div>
        <div class="flex flex-wrap items-center justify-between">
            <div class="mr-3">
                <h3>{{ $t(reportTitle) }}</h3>
            </div>

            <div class="items-center pt-4 pb-3">
                <div class="vx-row mr-1">
                    <div @click="isHidden = !isHidden" class="btn-add-new p-3 mr-2 rounded-sm cursor-pointer text-right flex items-center justify-center
                        text-lg font-medium text-base text-primary border border-solid border-default">
                        <feather-icon icon="FilterIcon" svgClasses="h-4 w-4" />
                    </div>
                    <!-- <div @click="initDowlonadElsx" class="btn-add-new p-3 mr-2 rounded-sm cursor-pointer text-right flex items-center justify-center
                        text-lg font-medium text-base text-dander border border-solid border-default">
                        <feather-icon icon="DownloadIcon" svgClasses="h-4 w-4" />
                    </div> -->
                </div>
            </div>

        </div>
        <template v-if="!isHidden">
            <d-search v-if="!dataAttributes.hideSearchBar" @searchQuery="searchQuery" @clicked="initTableData"
                :data="dataTables" :api="api" :formAttributes="formAttributes" :rowDisplay="'4grid'"
                :dataInfo="dataInfo"></d-search>
        </template>
        <div v-if="!pdfUrl">Loading Report...</div>
        <embed
        v-else
        :src="pdfUrl"
        type="application/pdf"
        width="100%"
        height="600px"
        />
    </div>
</template>

<script>
import Vue from "vue";
import axios from "@/axios.js";
import apiConfig from "@/apiConfig.js"
import { ref } from 'vue';
import DSearch from '@/views/form-builder/DSearch.vue';
export default {
  data() {
    return {
      title: "Report Viewer",
      api: "",
      apiGetField: apiConfig._apiGetOfficerField,
      apiGetFieldIncome: apiConfig._apiGetIncomeField,
      apiGetFieldExpense: apiConfig._apiGetExpenseField,
      apiGetExpenseField: apiConfig._apiGetExpenseField,
      apiGetOfficerPaymentField: apiConfig._apiGetOfficerPaymentField,
      apiGetOfficerRequestField: apiConfig._apiGetOfficerRequestField,
      pdfUrl: null,
      isHidden: false,
      dataTables:{},
      dataInfo: {},
      reportTitle: null,
      dataAttributes: {
        tableStyle: 1,
        page_number: 1,
        offset: 0,
        dataGrid: "row",
        actionButton: [
            // {
            //     icon: "DollarSignIcon",
            //     path: "/module/budget-arrangement/budget-ceiling/list",
            //     method: "View"
            // }
        ]
      },
      formAttributes: [
          // {
          //   name: "account_type_group_id",
          //   type: "select",
          //   required: true,
          //   options: []
          // },
          // {
          //   name: "code",
          //   type: "text",
          //   required: true
          // },
          // {
          //   name: "name_en",
          //   type: "text",
          //   required: true
          // },
          // {
          //   name: "name_kh",
          //   type: "text",
          //   required: true
          // },
          // {
          //   name: "order_level",
          //   type: "number",
          //   required: false
          // },
          // {
          //   name: "description",
          //   type: "textarea",
          //   required: false
          // },
      ],
      rowDisplay: "2grid", //1grid, 2grid, 3grid, 4grid
    };
  },
  async mounted() {
    let _search_criteria = {
      sort: "id",
      order: "",
      page_number: this.dataAttributes.page_number
    }
    await this.getDataTable(_search_criteria);
  },
  components: {
    DSearch,
  },
  created(){
        
  },
  methods: {
    async loadPdf() {
      let data = {

      };
      try {
        const token = localStorage.getItem('accessToken'); // e.g., Bearer token
        const response = await fetch('/api/v1/report/sales', {
          method: 'POST',
          data: {},
          headers: {
            'Authorization': `Bearer ${token}`,
            // Only if using API routes
            // 'X-Requested-With': 'XMLHttpRequest'
          },
          // If using Sanctum SPA auth, skip Authorization and use withCredentials
          // credentials: 'include'
        });

        if (!response.ok) {
          const errorText = await response.text();
          console.error('PDF fetch failed:', errorText);
          this.$vs.notify({
            title: 'Message',
            text: "Error, while trying to do action or no permission allow!",
            iconPack: 'feather',
            icon: 'icon-check-circle',
            color: 'danger'
          })
          // alert('Failed to load PDF: ' + response.status);
          return;
        }

        const blob = await response.blob();
        this.pdfUrl = URL.createObjectURL(blob);
      } catch (error) {
        console.error('Error loading PDF:', error);
        alert('Network error or PDF generation failed.');
      }
    },
    async getDataTable(_search_criteria) {
      this.$vs.loading();
      let _params = {};
      if (_search_criteria.search_field) {
        let _formAttribute = this.formAttributes;
        this.dataFields = [];
        this.formAttributes.forEach(_formAttribute => {
          if (_search_criteria.search_field[_formAttribute["name"]]) {
            let _d = {
              [_formAttribute["name"]]: _search_criteria.search_field[_formAttribute["name"]]
            }
            this.dataFields.push(_d);
          }
        });
        _params = {
          sort: _search_criteria.sort,
          order: _search_criteria.order,
          page_number: _search_criteria.page_number,
          search_field: this.dataFields,
        };
      } else {
        _params = {
          sort: _search_criteria.sort,
          order: _search_criteria.order,
          page_number: _search_criteria.page_number,
        };
      }
      try {
        const token = localStorage.getItem('accessToken'); // e.g., Bearer token
        let _report_type = this.$route.params.report_type;
        this.reportTitle = _report_type;
        switch (_report_type) {
          case "officers":
            this.getFields(this.apiGetField);
            break;
          case "income":
            this.getFields(this.apiGetFieldIncome);
            break;
          case "expense":
            this.getFields(this.apiGetFieldExpense);
            break;
          case "officer_incentive":
            this.getFields(this.apiGetExpenseField);
            break;
          case "officers_payment":
            this.getFields(this.apiGetOfficerPaymentField);
            break;
          case "officers_payable":
            this.getFields(this.apiGetOfficerPaymentField);
            break;
          case "officers_request":
            this.getFields(this.apiGetOfficerRequestField);
            break;
        
          default:
            break;
        }
        
        const response = await fetch('/api/v1/report/'+_report_type, {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'Authorization': `Bearer ${token}`,
            // Only if using API routes
            // 'X-Requested-With': 'XMLHttpRequest'
          },
          body: JSON.stringify({
            data: this.dataFields,
          })
          // If using Sanctum SPA auth, skip Authorization and use withCredentials
          // credentials: 'include'
        });

        if (!response.ok) {
          const errorText = await response.text();
          console.error('PDF fetch failed:', errorText);
          this.$vs.notify({
            title: 'Message',
            text: "Error, while trying to do action or no permission allow!",
            iconPack: 'feather',
            icon: 'icon-check-circle',
            color: 'danger'
          })
          this.$vs.loading.close();
          // alert('Failed to load PDF: ' + response.status);
          return;
        }

        const blob = await response.blob();
        this.pdfUrl = URL.createObjectURL(blob);
      } catch (error) {
        console.error('Error loading PDF:', error);
        alert('Network error or PDF generation failed.');
        this.$vs.loading.close();
      }
      this.$vs.loading.close();
      // return new Promise((resolve, reject) => {
      //   axios.post("/api/v1/report/sales", _params)
      //     .then((response) => {
      //       if (response.data) {
      //         this.data = response.data;
      //       } else {
      //         this.data = this.data;
      //       }
      //       this.$vs.loading.close();
      //     }).catch((error) => {
      //       // reject(error)
      //     this.$vs.notify({
      //         title: 'Message',
      //         text: "Error, while trying to do action or no permission allow!",
      //         iconPack: 'feather',
      //         icon: 'icon-check-circle',
      //         color: 'danger'
      //     });
      //     this.$vs.loading.close();
      //   })
      // })
    },
    getFields(apiGetField){
      return new Promise(() => {
                axios.get(apiGetField)
              .then((response) => {
                  if (response.data) {
                    this.dataTables = response.data;
                    this.formAttributes = response.data.form_attributes;
                  } else {
                    this.data = this.data;
                  }
                  // this.isVisible = response.data.is_display;
                  this.$vs.loading.close();
              }).catch(() => {
                  this.$vs.notify({
                      title: 'Message',
                      text: "Error, while trying to do action or no permission allow!",
                      iconPack: 'feather',
                      icon: 'icon-check-circle',
                      color: 'danger'
                  });
                  this.$vs.loading.close();
              })
      })
    },
    getData() {
      let _search_criteria = {
        sort: "id",
        order: "",
        page_number: this.dataAttributes.page_number
      }
      this.getDataTable(_search_criteria);
    },
    initTableData(searchQuery) {
      this.$vs.loading();
      let _search_criteria = {
          sort: "id",
          order: "",
          page_number: searchQuery.pageNum,
          search_field: searchQuery.searchFields
      }
      this.getDataTable(_search_criteria);
    },
    initDowlonadElsx(){
        let _params = {};
        return new Promise((resolve, reject) => {
            axios({
                url: '/api/v1/setting/account_type/export',
                method: 'GET',
                responseType: 'blob', // Important: binary data
                // headers: {
                // 'Authorization': 'Bearer ' + localStorage.getItem('token') // if using tokens
                // }
            })
            .then((response) => {
                    const url = window.URL.createObjectURL(new Blob([response.data]));
                    const link = document.createElement('a');
                    const contentDisposition = response.headers['content-disposition'];
                    const filename = contentDisposition
                    ? contentDisposition.split('filename=')[1].split(';')[0].replace(/"/g, '')
                    : 'AccountType.xlsx';

                    link.href = url;
                    link.setAttribute('download', filename);
                    document.body.appendChild(link);
                    link.click();
                    link.remove();
                    window.URL.revokeObjectURL(url);
                    
                    this.$vs.loading.close();
                }).catch((error) => {
                // reject(error)
                this.$vs.notify({
                    title: 'Message',
                    text: "Error, while trying to do action or no permission allow!",
                    iconPack: 'feather',
                    icon: 'icon-check-circle',
                    color: 'danger'
                })
                this.$vs.loading.close();
            })
        })
    },
    searchQuery(_search_fields) {
      let _search_params = {
        sort: "id",
        order: "",
        page_number: this.current_page,
        search_field: _search_fields.formAttribute,
        dataInfo: _search_fields.dataInfo
      }
      this.getDataTable(_search_params);
    },
    initTableData(){

    }
  },
  unmounted() {
    if (this.pdfUrl) {
      URL.revokeObjectURL(this.pdfUrl);
    }
  }
};
</script>
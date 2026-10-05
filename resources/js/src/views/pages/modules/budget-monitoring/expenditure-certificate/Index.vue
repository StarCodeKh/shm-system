<template>
  <d-table-list @clicked="initTableData" :api="api" ref="refInitPage" :allowDel="true" :title="title" :dataInfo="dataInfo"
    :dataAttributes="dataAttributes" :dataHeaders="dataHeaders" :dataTables="data" :formAttributes="formAttributes"
    :rowDisplay="rowDisplay"></d-table-list>
</template>

<script>
import axios from "@/axios.js";
import apiConfig from "@/apiConfig.js";
import { ref } from 'vue';

import DTableList from '@/views/form-builder/DTableList.vue';

export default {
  data() {
    return {
      title: "តារាងសាលាកបត្រចំណាយ",
      api: apiConfig._apiGetCreditMovement,
      dataAttributes: {
        backgroundColor: "warning",
        tableStyle: 1,
        page_number: 1,
        offset: 0,
        dataGrid: "row",
      },
      dataHeaders: {
        header1: "chapter",
        header2: "year",
        header3: "sub_program",
        header5: "doc",
        header6: "date",
        header7: "credit_movement_type",
        header8: "expense_type",
        header9: "sub_account",
        header10: "cluster_activity_code",
        header11: "increase",
        header12: "decrease",
      },
      data: {
        data: [
          // {
          //   // id: 1,
          //   // code: "",
          //   // name_en: "#120 - ចង្កោមសកម្មភាព",
          //   // name_kh: "#120 - ចង្កោមសកម្មភាព",
          //   // children: [
          //   //     {
          //   //         id: 1,
          //   //         credit_movement_type: 1,
          //   //         code: "001",
          //   //         name_en: "#001 - សកម្មភាពទី ១",
          //   //         name_kh: "#001 - សកម្មភាពទី ១",
          //   //         entity_id: 1,
          //   //         entity_member_id: 2,
          //   //         order_level: 1,
          //   //         indicator: {
          //   //             data: [
          //   //                 {
          //   //                     id: 1,
          //   //                     code: "#1.3.10-1",
          //   //                     kpi_name_en: "ឈ្មោះសូចនាករ",
          //   //                     kpi_name_kh: "ឈ្មោះសូចនាករ",
          //   //                     order_level: 1,
          //   //                     status: "Active"
          //   //                 }, {
          //   //                     id: 2,
          //   //                     code: "#1.3.10-2",
          //   //                     kpi_name_en: "ឈ្មោះសូចនាករ",
          //   //                     kpi_name_kh: "ឈ្មោះសូចនាករ",
          //   //                     order_level: 1,
          //   //                     status: "Active"
          //   //                 }, {
          //   //                     id: 3,
          //   //                     code: "#1.3.10-3",
          //   //                     kpi_name_en: "ឈ្មោះសូចនាករជាភាសាអង់គ្លេស",
          //   //                     kpi_name_kh: "ឈ្មោះសូចនាករជាភាសាអង់គ្លេស",
          //   //                     order_level: 1,
          //   //                     status: "InActive"
          //   //                 }
          //   //             ]
          //   //         },
          //   //     }

          //   // ]
          // }
        ],
        cluster_activity: [],
        credit_movement_type: [
          {
            "label": "ផ្ទេរឥណទានពី៦៩",
            "value": 1,
          },
          {
            "label": "បំពេញបន្ថែម",
            "value": 2,
          },
          {
            "label": "និយ័តភាព",
            "value": 3,
          },
          {
            "label": "កែឧបសម្ព័ន្ធរជ្ជទេយ្យបុរេប្រទាន",
            "value": 4,
          },
        ],
        account_group: [
          {
            "label": "ការទិញ",
            "value": 1,
          },
          {
            "label": "អចលកម្មរូបី",
            "value": 2,
          },
          {
            "label": "ទ្រព្យសម្បត្តិវិនិយោគរយៈពេលវែង",
            "value": 3,
          },
          {
            "label": "ការវិនិយោគ",
            "value": 4,
          },
        ],
        sub_program: [
          {
            "label": "ការទិញ",
            "value": 1,
          },
          {
            "label": "អចលកម្មរូបី",
            "value": 2,
          },
          {
            "label": "ទ្រព្យសម្បត្តិវិនិយោគរយៈពេលវែង",
            "value": 3,
          },
          {
            "label": "ការវិនិយោគ",
            "value": 4,
          },
        ],
        account: [
          {
            "label": "សោហ៊ុយលើការសិក្សាស្រាវជ្រាវនិងអភិវឌ្ឍន៍",
            "value": 1,
          },
          {
            "label": "ដីធ្លី ដីរ៉ែ និងសម្បត្តិធម្មជាតិផ្សេងៗ",
            "value": 2,
          },
          {
            "label": "ឋបនកម្មបច្ចេកទេស សម្ភារ និងហត្ថូបករណ៍",
            "value": 3,
          },
          {
            "label": "អចលកម្មរូបីផ្សេងទៀត",
            "value": 4,
          },
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
      formAttributes: [
        {
          name: "credit_movement_type",
          type: "select",
          required: true,
          options: [],
        },
        {
          name: "account_group_id",
          type: "select",
          required: true,
          options: [],
        },
        {
          name: "program_id",
          type: "select",
          required: true,
          hasFilter: true,
          filterObj: "sub_program_id",
          api: apiConfig._apiSubProgramByPro
        },
        {
          name: "sub_program_id",
          type: "select",
          required: true,
          hasFilter: true,
          filterObj: "cluster_activity_id",
          api: apiConfig._apiEntityBySubPro
        },
        {
          name: "cluster_activity_id",
          type: "select",
          required: true,
          hasFilter: false,
        },
        {
          name: "year",
          type: "date",
          required: true
        },
        {
          name: "account",
          type: "text",
          required: true
        },
        {
          name: "doc",
          type: "text",
          required: true
        },
        {
          name: "date",
          type: "date",
          required: true
        },
        {
          name: "increase",
          type: "number",
          required: false
        },
        {
          name: "decrease",
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
  components: {
    DTableList,
  },
  methods: {
    getDataTable(_search_criteria) {
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

      return new Promise((resolve, reject) => {
        axios.post(this.api + "/search", _params)
          .then((response) => {
            if (response.data) {
              this.data = response.data;
            } else {
              this.data = this.data;
            }
            this.$vs.loading.close();
          }).catch((error) => {
            // reject(error)
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
      return false;
    }

  },
  created() {
    this.$vs.loading();
    this.getData();
  },
  watch: {
  }
}
</script>
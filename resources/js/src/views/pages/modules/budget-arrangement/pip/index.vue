<template>
  <div id="table-demo">
    <d-table-list @clicked="initTableData" :api="api" ref="refInitPage" :allowDel="true" :title="title"
      :dataInfo="dataInfo" :dataAttributes="dataAttributes" :dataHeaders="dataHeaders" :dataTables="data"
      :formAttributes="formAttributes" :rowDisplay="rowDisplay" @initDownload="initDownload"></d-table-list>
  </div>
</template>

<script>
import axios from "@/axios.js"
import apiConfig from "@/apiConfig.js"
import { ref } from 'vue';
import DTableList from '@/views/form-builder/DTableList.vue'

export default {
  data() {
    return {
      title: "pip_form_title",
      api: apiConfig._apiPIP,
      dataAttributes: {
        tableStyle: 10,
        customizeForm: true,
        popupFullscreen: true,
        page_number: 1,
        offset: 0,
        dataGrid: "row",
        enableDownload: true,
        actionButton: [
          {
            icon: "ViewIcon",
            path: "/module/budget-arrangement/budget-ceiling/list",
            method: "Edit"
          },
          {
            icon: "EditIcon",
            path: "/module/budget-arrangement/budget-ceiling/list",
            method: "View"
          },
          {
            icon: "TrashIcon",
            path: "/module/budget-arrangement/budget-ceiling/list",
            method: "Delete"
          },
        ]
      },
      dataHeaders: {
        header1: {
          width: 350,
          label: "group_parent_program",
          rowspan: 2,
          colspan: 0,
        },
        // header1: {
        //   width: 350,
        //   label: "sub_program_id",
        //   // label: "ឈ្មោះកម្មវិធី/ ឈ្មោះអនុកម្មវិធី /ឈ្មោះគម្រោង",
        //   rowspan: 2,
        //   colspan: 0,
        // },
        header2: {
          label: "pip_no",
          rowspan: 2,
          colspan: 0,
        },
        header3: {
          label: "project_status_id",
          // label: "ស្ថានភាពគម្រោង",
          rowspan: 2,
          colspan: 0,
        },
        header4: {
          label: "project_type_id",
          rowspan: 2,
          colspan: 0,
        },
        header5: {
          label: "pip_estimate_classification", //ចំណាត់ថ្នាក់ប៉ាន់ស្ថានសរុប
          rowspan: 0,
          colspan: 4,
        },
        header6: {
          label: "pip_sponsor_finance", //ហិរញ្ញប្បទានសន្យាផ្តល់
          rowspan: 0,
          colspan: 4,
        },
        header7: {
          label: "pip_adjustment_budget", // តម្រូវការហិរញ្ញប្បទានបំពេញបន្ថែម
          rowspan: 0,
          colspan: 4,
        },
        header8: {
          label: "pip_source_funding", //ប្រភពហិរញ្ញប្បទាន
          rowspan: 2,
          colspan: 0,
        },
        header9: {
          label: "remark", // សម្គាល់
          rowspan: 2,
          colspan: 0,
        },
        header10: {
          width: 80,
          label: "action_button", //ប៊ូតុងសកម្ម
          rowspan: 2,
          colspan: 0,
        }
      },
      data: {
        dataHeaders: {
          header1: {
            label: "pip_year_2023",
            rowspan: 0,
            colspan: 0,
          },
          header2: {
            label: "pip_year_2024",
            rowspan: 0,
            colspan: 0,
          },
          header3: {
            label: "pip_year_2025",
            rowspan: 0,
            colspan: 0,
          },
          header4: {
            label: "pip_total_year", //សរុប៣ឆ្នាំ
            rowspan: 0,
            colspan: 0,
          },
          header5: {
            label: "pip_year_2023",
            rowspan: 0,
            colspan: 0,
          },
          header6: {
            label: "pip_year_2024",
            rowspan: 0,
            colspan: 0,
          },
          header7: {
            label: "pip_year_2025",
            rowspan: 0,
            colspan: 0,
          },
          header8: {
            label: "pip_total_year",
            rowspan: 0,
            colspan: 0,
          },
          header9: {
            label: "pip_year_2023",
            rowspan: 0,
            colspan: 0,
          },
          header10: {
            label: "pip_year_2024",
            rowspan: 0,
            colspan: 0,
          },
          header11: {
            label: "pip_year_2025",
            rowspan: 0,
            colspan: 0,
          },
          header12: {
            label: "pip_total_year",
            rowspan: 0,
            colspan: 0,
          },
        },
        dataSubHeaders: {
          header1: {
            label: "សរុប",
            rowspan: 0,
            colspan: 0,
          },
          header2: {
            label: "%ច្ប.២២",
            rowspan: 0,
            colspan: 0,
          },
          header3: {
            label: "សរុប​",
            rowspan: 0,
            colspan: 0,
          },
          header4: {
            label: "%ច្ប.២២",
            rowspan: 0,
            colspan: 0,
          }
        },
        data: [
          {
            id: 1,
            name: "សរុបរួមក្រសួង​សេដ្ឋ​កិច្ច​និង​ហិរញ្ញវត្ថ",
            name_kh: "សរុបរួមក្រសួង​សេដ្ឋ​កិច្ច​និង​ហិរញ្ញវត្ថ",
            remark: "",
            status: 1,
            order_level: "",
            hasColspan: false,
            values: [0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, "", ""],
            data: [
              {
                summary: {
                  id: 1,
                  name: "កម្មវិធីទី១៖ ការគ្រប់គ្រងចំណូលសាធារណៈ និងទ្រព្យសម្បត្តិរដ្ឋ",
                  values: [0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, "", ""],
                },
                children: [
                  {
                    id: 1,
                    name: "អនុកម្មវិធី ៣.១ ៖ អគ្គនាយកដ្ឋានគយ និងរដ្ឋាករកម្ពុជា",
                    values: [0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, "", ""],
                    data: [
                    ],
                    dataDetails: [
                      {
                        id: 1,
                        name: "១២២. គម្រោងសាងសង់អគារសាខាពន្ធដារខណ្ឌចំនួន០១ និងអគារការិយាល័យពន្ធដារស្រុកស្ទឹងហាវ ០១",
                        entity: {},
                        values: ["659", "គម្រោងស្នើសុំថ្មី", "វិនិយោគជាទុន", 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, "", ""],
                        data: [
                          {
                            id: 1,
                            name: "- Element",
                            entity: {},
                            values: ["659", "គម្រោងស្នើសុំថ្មី", "វិនិយោគជាទុន", 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, "", ""],
                          }
                        ]
                      },
                      {
                        id: 2,
                        name: "២. គម្រោងជួសជុលនិងកែលម្អអគារសាខាពន្ធដារខណ្ឌចំនួន០១ ការិយាល័យពន្ធដារក្រុងចំនួន០១ និងស្រុកចំនួន ០១  ",
                        entity: {},
                        values: [0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, "", ""],
                        data: [
                        ]
                      },
                    ]
                  },
                ]
              }
            ]
          }
        ],
        project_type_id: [],
        project_status_id: [],
        sub_program_id: [],
        entity_id: [],
        limit: 10,
        total: 3,
      },
      formAttributes: [
        {
          name: "program_id",
          type: "select",
          required: true,
          hasFilter: true,
          filterObj: "sub_program_id",
          api: apiConfig._apiSubProgramByPro,
          // hasDefault: true,
          // defaultOptions: {
          //   label: "គោលបំណងទី១",
          //   value: 1,
          // },
          options: [],
        },
        {
          name: "sub_program_id",
          type: "select",
          required: true,
          hasFilter: true,
          filterObj: "entity_id",
          api: apiConfig._apiEntityBySubPro,
          // hasDefault: true,
          // defaultOptions: {
          //   label: "គោលបំណងទី១",
          //   value: 1,
          // },
          options: [],
        },
        {
          name: "entity_id",
          type: "select",
          required: true,
          options: [],
        },
        {
          name: "project_name_en",
          type: "text",
          required: true
        },
        {
          name: "project_name_kh",
          type: "text",
          required: true
        },

        {
          name: "pip_no",
          type: "text",
          required: true
        },
        {
          name: "project_status_id",
          type: "select",
          required: true,
          options: [],
        },
        {
          name: "project_type_id",
          type: "select",
          required: true,
          options: [],
        },
        {
          name: "finance_resource_id",
          type: "select",
          required: true,
          options: [],
        },

        {
          name: "grading_no",
          type: "text",
          required: false
        },
        {
          name: "order_level",
          type: "number",
          required: false
        },
        {
          name: "pip_total_est_expense", //ចំណាយប៉ាន់ស្មានសរុប",
          type: "grid",
          required: false
        },
        {
          name: "est_costing_year_0",
          type: "number",
          required: false
        },
        {
          name: "est_costing_year_1",
          type: "number",
          required: false
        },
        {
          name: "est_costing_year_2",
          type: "number",
          required: false
        },
        {
          name: "pip_sponsor_finance",
          type: "grid",
          required: false
        },
        {
          name: "s_fin_year_0",
          type: "number",
          required: false
        },
        {
          name: "s_fin_year_1",
          type: "number",
          required: false
        },
        {
          name: "s_fin_year_2",
          type: "number",
          required: false
        },
        {
          name: "pip_adjustment_budget",
          type: "grid",
          required: false
        },
        {
          name: "add_req_fin_year_0",
          type: "number",
          required: false
        },
        {
          name: "add_req_fin_year_1",
          type: "number",
          required: false
        },
        {
          name: "add_req_fin_year_2",
          type: "number",
          required: false
        },
        {
          name: "pip_data_detail", //ទិន្នន័យលម្អិត",
          type: "grid",
          required: false
        },
        // {
        //   name: "pip_source_funding",
        //   type: "checkbox",
        //   required: false,
        //   attributes: [
        //     {
        //       name: "pip_national_budget",//ថវិកាជាតិ",
        //       value: "1"
        //     },
        //     {
        //       name: "pip_non_refund_budget",//ជំនួយឥតសំណង",
        //       value: "2"
        //     },
        //     {
        //       name: "pip_non_refund_credit",//ឥណទានមិនមែនសម្បទាន",
        //       value: "3"
        //     }
        //   ]
        // },
        {
          name: "pip_4_strategy",//មុំនៃគោលនយោបាយក្នុងយុទ្ធសាស្រ្តចតុកោណ ដំណាក់កាលទី៤ ដែលគម្រោងស្ថិតនៅ៖ ",
          type: "textarea",
          required: false
        },
        {
          name: "pip_participant_project",//ការចូលរួមចំណែករបស់គម្រោងក្នុងការសម្រេចបានគោលនយោបាយអាទិភាពខាងលើ ",
          type: "textarea",
          required: false
        },
        {
          name: "pip_field",//វិស័យ (សូមប្រើវិស័យ អនុវិស័យ ដែលមានចែងក្នុង ផ.យ.អ.ជ. ២០១៩-២០២៣)",
          type: "textarea",
          required: false
        }, {
          name: "pip_subproject_location",//ទីតាំងអនុវត្តគម្រោង (ពិពណ៌នាពីទីតាំងដែលត្រូវអនុវត្តគម្រោង- ឈ្មោះខេត្ត)",
          type: "textarea",
          required: false
        }, {
          name: "pip_project_aim",//គោលបំណងរបស់គម្រោង (ពិពណ៌នាគោលបំណងចម្បងៗរបស់គម្រោង)",
          type: "textarea",
          required: false
        }, {
          name: "pip_project_description",//ការពិពណ៌នាអំពីគម្រោង (ពិពណ៌នាអំពីសមាសភាគទាំងអស់របស់គម្រោង)",
          type: "textarea",
          required: false
        }, {
          name: "pip_resonable_project",//យុត្តិកម្មលើគម្រោង (សូមផ្តល់ហេតុផលដែលគម្រោងនេះត្រូវបានចាត់ទុកថាមានប្រយោជន៍)",
          type: "textarea",
          required: false
        }, {
          name: "pip_benefit_project", //ផលប្រយោជន៍របស់គម្រោង (រៀបរាប់ពីអ្នកដែលនឹងទទួលប្រយោជន៍ផ្ទាល់និងមិនផ្ទាល់ពីការអនុវត្តគម្រោង)",
          type: "textarea",
          required: false
        }, {
          name: "pip_effectivenes_environment", //ផលប៉ះពាល់ផ្នែកបរិស្ថាននិងសង្គម (ពណ៌នាសង្ខេបពីផលប៉ះពាល់របស់គម្រោងលើប្រជាជននិងបរិស្ថានជុំវិញ ប្រសិនបើមាន។ តើគម្រោងនេះនឹងជួយដល់ការកាត់បន្ថយភាពក្រីក្រដែរឬទេ?)",
          type: "textarea",
          required: false
        }, {
          name: "pip_gender_analysis",//ការវិភាគយេនឌ័រ (តើគម្រោងនេះមានផលប៉ះពាល់ដល់តួនាទីរបស់បុរសនិងស្រ្តីក្នុងទីតាំងរបស់គម្រោងនេះដូចម្តេច? តើស្រ្តីនឹងត្រូវចូលរួមយ៉ាងសកម្មក្នុងការអនុវត្តគម្រោងនេះដែរឬទេ?)",
          type: "textarea",
          required: false
        }, {
          name: "pip_project_imple_capacity",//សមត្ថភាពអនុវត្តគម្រោង (តើក្រសួងមានជំនាញនិងបទពិសោធន៍គ្រប់គ្រាន់សម្រាប់អនុវត្តគម្រោងដែរឬទេ?)",
          type: "textarea",
          required: false
        }, {
          name: "pip_project_imple_status",//ស្ថានភាពនៃការអនុវត្តគម្រោង (ពិពណ៌នាសង្ខេបអំពីវឌ្ឍនភាពរបស់គម្រោងនិងបង្ហាញអំពីភាពប្រឈមនានាដែលបណ្តាលឲ្យមានការពន្យារពេលក្នុងការអនុវត្តគម្រោង។)",
          type: "textarea",
          required: false
        }, {
          name: "pip_partner_participation",//ការចូលរួមរបស់ដៃគូអភិវឌ្ឍ (ផ្តល់ព័ត៌មានអំពីដៃគូអភិវឌ្ឍសក្តានុពល ដែលពាក់ព័ន្ធនឹងគម្រោងនេះ)",
          type: "textarea",
          required: false
        },
      ],
      rowDisplay: "3grid", //1grid, 2grid, 3grid, 4grid
      dataFields: [],
      dataInfo: {}
    }
  },
  components: {
    DTableList,
  },
  methods: {
    initDownload() {
      const _base_url = window.location.origin;
      window.location = _base_url + '/download/PIP/2023/2.4-MEF-PIP Status 2023-2025.xlsx';
    },
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
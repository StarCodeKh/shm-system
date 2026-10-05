<template>
  <div id="table-demo">
      <!-- <table-state></table-state> -->
      <div v-if="enableToggleForm" class="items-center data-list-btn-container">
          <div class="p-3 mb-4" style="overflow: scroll;">
              <d-form @clicked="initPushDataTable" @clickForm="initTableData" :data="data" :dataInfo="dataInfo"
              :formAttributes="formAttributes" :api="api" :rowDisplay="rowDisplay"></d-form>
              <form-income-program></form-income-program>
          </div>
      </div>
      <div class="p-3 mr-4">
          <vs-button @click="enableToggleForm = !enableToggleForm" color="primary" type="filled">{{ $t("បន្ថែមគំរូប្រភេទចំណាត់ថ្នាក់")
          }}</vs-button>
      </div>
      
      <d-table-list @clicked="initTableData" :api="api" ref="refInitPage" :allowDel="true" :title="title"
          :dataInfo="dataInfo" :dataAttributes="dataAttributes" :dataHeaders="dataHeaders" :dataTables="data"
          :formAttributes="formAttributes" :rowDisplay="rowDisplay"></d-table-list>
  </div>
</template>

<script>
import axios from "@/axios.js"
import apiConfig from "@/apiConfig.js"
import { ref } from 'vue';
import DForm from '@/views/form-builder/DForm.vue'
import DTableList from '@/views/form-builder/DTableList.vue'
import FormIncomeProgram from '@/views/pages/modules/budget-monitoring/income-expense-arrangement/_form_income_program.vue'
export default {
  data() {
      return {
          title: "CreditRevenueRule",
          enableToggleForm: false,
          api: apiConfig._apiGetIncomeArrangement,
          dataAttributes: {
              tableStyle: 8,
              page_number: 1,
              offset: 0,
              dataGrid: "row",
              hasHeadingReport: false,
              headingReport: "",
              popupFullscreen: true,
              hideSearchBar: false,
              enableToggleForm: true // if allow to show form by toggling but no popup
          },
          dataHeaders: {
              header1: {
                  // width: 150,
                  label: "account_group_id",
                  rowspan: 3,
                  colspan: 0,
              },
              header2: {
                  label: "account_id",
                  rowspan: 3,
                  colspan: 0,
              },
              header3: {
                  label: "sub_account_id",
                  rowspan: 3,
                  colspan: 0,
              },
              header4: {
                  width: 300,
                  label: "line_ministry",
                  rowspan: 3,
                  colspan: 0,
              },
              header5: {
                  width: 100,
                  label: "finance_rule",
                  rowspan: 3,
                  colspan: 0,
              },
              header6: {
                  width: 150,
                  label: "total_annual_program_project",
                  rowspan: 2,
                  colspan: 2,
              },
              header7: {
                  label: "semester1",
                  rowspan: 0,
                  colspan: 8,
              },
              header8: {
                  label: "semester2",
                  rowspan: 0,
                  colspan: 8,
              },
              header9: {
                  label: "semester3",
                  rowspan: 0,
                  colspan: 8,
              },
              header10: {
                  label: "semester4",
                  rowspan: 0,
                  colspan: 8,
              },
              header11: {
                  label: "other",
                  rowspan: 3,
                  colspan: 0,
              }
          },
          data: {
              dataHeaders: {
                  header1: {
                      label: "គ្រោងដើមឆ្នាំ",
                      rowspan: 0,
                      colspan: 4,
                  },
                  header2: {
                      label: "ប៉ាន់ស្មានអនុវត្ត",
                      rowspan: 0,
                      colspan: 4,
                  },
                  header3: {
                      label: "គ្រោងដើមឆ្នាំ",
                      rowspan: 0,
                      colspan: 4,
                  },
                  header4: {
                      label: "ប៉ាន់ស្មានអនុវត្ត",
                      rowspan: 0,
                      colspan: 4,
                  },
                  header5: {
                      label: "គ្រោងដើមឆ្នាំ",
                      rowspan: 0,
                      colspan: 4,
                  },
                  header6: {
                      label: "ប៉ាន់ស្មានអនុវត្ត",
                      rowspan: 0,
                      colspan: 4,
                  },
                  header7: {
                      label: "គ្រោងដើមឆ្នាំ",
                      rowspan: 0,
                      colspan: 4,
                  },
                  header8: {
                      label: "ប៉ាន់ស្មានអនុវត្ត",
                      rowspan: 0,
                      colspan: 4,
                  }
              },
              dataSubHeaders: {
                  header1: {
                      label: "ទឹកប្រាក់",
                      rowspan: 0,
                      colspan: 0,
                  },
                  header2: {
                      label: "%ច្បាប់",
                      rowspan: 0,
                      colspan: 0,
                  },
                  header3: {
                      label: "សរុប",
                      rowspan: 0,
                      colspan: 0,
                  },
                  header4: {
                      label: "ខែទី១",
                      rowspan: 0,
                      colspan: 0,
                  },
                  header5: {
                      label: "ខែទី២",
                      rowspan: 0,
                      colspan: 0,
                  },
                  header6: {
                      label: "ខែទី៣",
                      rowspan: 0,
                      colspan: 0,
                  },
                  header7: {
                      label: "សរុប",
                      rowspan: 0,
                      colspan: 0,
                  },
                  header8: {
                      label: "អនុ.ខែទី១",
                      rowspan: 0,
                      colspan: 0,
                  },
                  header9: {
                      label: "អនុ.ខែទី២",
                      rowspan: 0,
                      colspan: 0,
                  },
                  header10: {
                      label: "អនុ.ខែទី៣",
                      rowspan: 0,
                      colspan: 0,
                  },
                  header11: {
                      label: "សរុប",
                      rowspan: 0,
                      colspan: 0,
                  },
                  header12: {
                      label: "ខែទី៤",
                      rowspan: 0,
                      colspan: 0,
                  },
                  header13: {
                      label: "ខែទី៥",
                      rowspan: 0,
                      colspan: 0,
                  },
                  header14: {
                      label: "ខែទី៦",
                      rowspan: 0,
                      colspan: 0,
                  },
                  header15: {
                      label: "សរុប",
                      rowspan: 0,
                      colspan: 0,
                  },
                  header16: {
                      label: "អនុ.ខែទី៤",
                      rowspan: 0,
                      colspan: 0,
                  },
                  header17: {
                      label: "អនុ.ខែទី៥",
                      rowspan: 0,
                      colspan: 0,
                  },
                  header18: {
                      label: "អនុ.ខែទី៦",
                      rowspan: 0,
                      colspan: 0,
                  },
                  header19: {
                      label: "សរុប",
                      rowspan: 0,
                      colspan: 0,
                  },
                  header20: {
                      label: "ខែទី៧",
                      rowspan: 0,
                      colspan: 0,
                  },
                  header21: {
                      label: "ខែទី៨",
                      rowspan: 0,
                      colspan: 0,
                  },
                  header22: {
                      label: "ខែទី៩",
                      rowspan: 0,
                      colspan: 0,
                  },
                  header23: {
                      label: "សរុប",
                      rowspan: 0,
                      colspan: 0,
                  },
                  header24: {
                      label: "អនុ.ខែទី៧",
                      rowspan: 0,
                      colspan: 0,
                  },
                  header25: {
                      label: "អនុ.ខែទី៨",
                      rowspan: 0,
                      colspan: 0,
                  },
                  header26: {
                      label: "អនុ.ខែទី៩",
                      rowspan: 0,
                      colspan: 0,
                  },
                  header27: {
                      label: "សរុប",
                      rowspan: 0,
                      colspan: 0,
                  },
                  header28: {
                      label: "ខែទី១០",
                      rowspan: 0,
                      colspan: 0,
                  },
                  header29: {
                      label: "ខែទី១១",
                      rowspan: 0,
                      colspan: 0,
                  },
                  header30: {
                      label: "ខែទី១២",
                      rowspan: 0,
                      colspan: 0,
                  },
                  header31: {
                      label: "សរុប",
                      rowspan: 0,
                      colspan: 0,
                  },
                  header32: {
                      label: "អនុ.ខែទី១០",
                      rowspan: 0,
                      colspan: 0,
                  },
                  header33: {
                      label: "អនុ.ខែទី១១",
                      rowspan: 0,
                      colspan: 0,
                  },
                  header34: {
                      label: "អនុ.ខែទី១២",
                      rowspan: 0,
                      colspan: 0,
                  }
              },
              dataFillables: {
                  field1: "id",
                  field2: "entity",
                  field3: "summary_budget",
                  field4: {
                      childField: {
                          field1: "account_group",
                          field2: "account",
                          field3: "sub_account",
                          field4: "department",
                          field5: "finance_rule",
                          field6: "amount",
                          field7: "percentage_implementing_rule",
                          fiedl8: "costing_plan_semester1",
                          fiedl9: "costing_plan_semester2",
                          fiedl10: "costing_plan_semester3",
                          fiedl11: "costing_plan_semester4",
                          fiedl12: "remark"
                      }
                  }
              },
              // dataFillables: {
              //     field1: "name",
              //     field2: "name_kh",
              //     field3: "remark",
              //     field4: "status",
              //     field5: "order_level",
              //     field6: "implementing_year",
              //     field7: "current_year"
              // },
              // group_fields: { field1: "summary_costing", field2: "rev_type" },
              summary: [],
              data: [],
              // data: [
              //     {
              //         id: 1,
              //         account_group: "",
              //         account: "",
              //         sub_account: "",
              //         department: "អគ្គនាយកដ្ឋានគយ និងរដ្ឋាករកម្ពុជា",
              //         total_finance_rule: "12",
              //         total_amount: "12",
              //         total_percentage_implementing_rule: "12",
              //         costing_plan_semester1: {
              //             // គ្រោងដើមឆ្នាំ
              //             year_planning: {
              //                 total_amount: "555",
              //                 amount_plan_month_1: "1",
              //                 amount_plan_month_2: "2",
              //                 amount_plan_month_3: "3",
              //             },
              //             impl_est: {
              //                 impl_est_amount: "5554",
              //                 imple_amount_plan_month_1: "1", // imple = implementing_amount ប៉ាន់ស្មានអនុវត្តខែ១
              //                 imple_amount_plan_month_2: "2", // imple = implementing_amount ប៉ាន់ស្មានអនុវត្តខែ២
              //                 imple_amount_plan_month_3: "3", // imple = implementing_amount ប៉ាន់ស្មានអនុវត្តខែ៣
              //             }
              //         },
              //         costing_plan_semester2: {
              //             // គ្រោងដើមឆ្នាំ
              //             year_planning: {
              //                 total_amount: "2222",
              //                 amount_plan_month_4: "1",
              //                 amount_plan_month_5: "2",
              //                 amount_plan_month_6: "3",
              //             },
              //             impl_est: {
              //                 impl_est_amount: "3333",
              //                 imple_amount_plan_month_4: "1", // imple = implementing_amount ប៉ាន់ស្មានអនុវត្តខែ១
              //                 imple_amount_plan_month_5: "2", // imple = implementing_amount ប៉ាន់ស្មានអនុវត្តខែ២
              //                 imple_amount_plan_month_6: "3", // imple = implementing_amount ប៉ាន់ស្មានអនុវត្តខែ៣
              //             }
              //         },
              //         costing_plan_semester3: {
              //             // គ្រោងដើមឆ្នាំ
              //             year_planning: {
              //                 total_amount: "555",
              //                 amount_plan_month_7: "1",
              //                 amount_plan_month_8: "2",
              //                 amount_plan_month_9: "3",
              //             },
              //             impl_est: {
              //                 impl_est_amount: "5554",
              //                 imple_amount_plan_month_7: "1", // imple = implementing_amount ប៉ាន់ស្មានអនុវត្តខែ១
              //                 imple_amount_plan_month_8: "2", // imple = implementing_amount ប៉ាន់ស្មានអនុវត្តខែ២
              //                 imple_amount_plan_month_9: "3", // imple = implementing_amount ប៉ាន់ស្មានអនុវត្តខែ៣
              //             }
              //         },
              //         costing_plan_semester4: {
              //             // គ្រោងដើមឆ្នាំ
              //             year_planning: {
              //                 total_amount: "555",
              //                 amount_plan_month_10: "1",
              //                 amount_plan_month_11: "2",
              //                 amount_plan_month_12: "3",
              //             },
              //             impl_est: {
              //                 impl_est_amount: "5554",
              //                 imple_amount_plan_month_10: "1", // imple = implementing_amount ប៉ាន់ស្មានអនុវត្តខែ១
              //                 imple_amount_plan_month_11: "2", // imple = implementing_amount ប៉ាន់ស្មានអនុវត្តខែ២
              //                 imple_amount_plan_month_12: "3", // imple = implementing_amount ប៉ាន់ស្មានអនុវត្តខែ៣
              //             }
              //         },
              //         remark: "កំណត់ត្រា",
              //         order_level: 1,
              //         children: [
              //             {
              //                 id: 2,
              //                 account_group: "",
              //                 account: "",
              //                 sub_account: "",
              //                 department: "ប្រតិបត្ដិការមិនឆ្លងកាត់អគ្គនាយកដ្ឋានរតនាគារជាតិ",
              //                 total_finance_rule: "12",
              //                 total_amount: "12",
              //                 total_percentage_implementing_rule: "12",
              //                 order_level: 2,
              //                 costing_plan_semester1: {
              //                     // គ្រោងដើមឆ្នាំ
              //                     year_planning: {
              //                         total_amount: "555",
              //                         amount_plan_month_1: "1",
              //                         amount_plan_month_2: "2",
              //                         amount_plan_month_3: "3",
              //                     },
              //                     impl_est: {
              //                         impl_est_amount: "5554",
              //                         imple_amount_plan_month_1: "1", // imple = implementing_amount ប៉ាន់ស្មានអនុវត្តខែ១
              //                         imple_amount_plan_month_2: "2", // imple = implementing_amount ប៉ាន់ស្មានអនុវត្តខែ២
              //                         imple_amount_plan_month_3: "3", // imple = implementing_amount ប៉ាន់ស្មានអនុវត្តខែ៣
              //                     }
              //                 },
              //                 costing_plan_semester2: {
              //                     // គ្រោងដើមឆ្នាំ
              //                     year_planning: {
              //                         total_amount: "2222",
              //                         amount_plan_month_4: "1",
              //                         amount_plan_month_5: "2",
              //                         amount_plan_month_6: "3",
              //                     },
              //                     impl_est: {
              //                         impl_est_amount: "3333",
              //                         imple_amount_plan_month_4: "1", // imple = implementing_amount ប៉ាន់ស្មានអនុវត្តខែ១
              //                         imple_amount_plan_month_5: "2", // imple = implementing_amount ប៉ាន់ស្មានអនុវត្តខែ២
              //                         imple_amount_plan_month_6: "3", // imple = implementing_amount ប៉ាន់ស្មានអនុវត្តខែ៣
              //                     }
              //                 },
              //                 costing_plan_semester3: {
              //                     // គ្រោងដើមឆ្នាំ
              //                     year_planning: {
              //                         total_amount: "555",
              //                         amount_plan_month_7: "1",
              //                         amount_plan_month_8: "2",
              //                         amount_plan_month_9: "3",
              //                     },
              //                     impl_est: {
              //                         impl_est_amount: "5554",
              //                         imple_amount_plan_month_7: "1", // imple = implementing_amount ប៉ាន់ស្មានអនុវត្តខែ១
              //                         imple_amount_plan_month_8: "2", // imple = implementing_amount ប៉ាន់ស្មានអនុវត្តខែ២
              //                         imple_amount_plan_month_9: "3", // imple = implementing_amount ប៉ាន់ស្មានអនុវត្តខែ៣
              //                     }
              //                 },
              //                 costing_plan_semester4: {
              //                     // គ្រោងដើមឆ្នាំ
              //                     year_planning: {
              //                         total_amount: "555",
              //                         amount_plan_month_10: "1",
              //                         amount_plan_month_11: "2",
              //                         amount_plan_month_12: "3",
              //                     },
              //                     impl_est: {
              //                         impl_est_amount: "5554",
              //                         imple_amount_plan_month_10: "1", // imple = implementing_amount ប៉ាន់ស្មានអនុវត្តខែ១
              //                         imple_amount_plan_month_11: "2", // imple = implementing_amount ប៉ាន់ស្មានអនុវត្តខែ២
              //                         imple_amount_plan_month_12: "3", // imple = implementing_amount ប៉ាន់ស្មានអនុវត្តខែ៣
              //                     }
              //                 },
              //                 remark: "កំណត់ត្រា",
              //                 children: [
              //                     {
              //                         id: 1,
              //                         account_group: { id: 1, code: "001", name: "Account Group" },
              //                         account: { id: 1, code: "001", name: "Account" },
              //                         sub_account: { id: 1, code: "001", name: "sub_Account" },
              //                         department: { id: 1, name: "អគ្គនាយកដ្ឋាន" },
              //                         total_finance_rule: "12",
              //                         total_amount: "12",
              //                         total_percentage_implementing_rule: "12",
              //                         costing_plan_semester1: {
              //                             // គ្រោងដើមឆ្នាំ
              //                             year_planning: {
              //                                 total_amount: "555",
              //                                 amount_plan_month_1: "1",
              //                                 amount_plan_month_2: "2",
              //                                 amount_plan_month_3: "3",
              //                             },
              //                             impl_est: {
              //                                 impl_est_amount: "5554",
              //                                 imple_amount_plan_month_1: "1", // imple = implementing_amount ប៉ាន់ស្មានអនុវត្តខែ១
              //                                 imple_amount_plan_month_2: "2", // imple = implementing_amount ប៉ាន់ស្មានអនុវត្តខែ២
              //                                 imple_amount_plan_month_3: "3", // imple = implementing_amount ប៉ាន់ស្មានអនុវត្តខែ៣
              //                             }
              //                         },
              //                         costing_plan_semester2: {
              //                             // គ្រោងដើមឆ្នាំ
              //                             year_planning: {
              //                                 total_amount: "2222",
              //                                 amount_plan_month_4: "1",
              //                                 amount_plan_month_5: "2",
              //                                 amount_plan_month_6: "3",
              //                             },
              //                             impl_est: {
              //                                 impl_est_amount: "3333",
              //                                 imple_amount_plan_month_4: "1", // imple = implementing_amount ប៉ាន់ស្មានអនុវត្តខែ១
              //                                 imple_amount_plan_month_5: "2", // imple = implementing_amount ប៉ាន់ស្មានអនុវត្តខែ២
              //                                 imple_amount_plan_month_6: "3", // imple = implementing_amount ប៉ាន់ស្មានអនុវត្តខែ៣
              //                             }
              //                         },
              //                         costing_plan_semester3: {
              //                             // គ្រោងដើមឆ្នាំ
              //                             year_planning: {
              //                                 total_amount: "555",
              //                                 amount_plan_month_7: "1",
              //                                 amount_plan_month_8: "2",
              //                                 amount_plan_month_9: "3",
              //                             },
              //                             impl_est: {
              //                                 impl_est_amount: "5554",
              //                                 imple_amount_plan_month_7: "1", // imple = implementing_amount ប៉ាន់ស្មានអនុវត្តខែ១
              //                                 imple_amount_plan_month_8: "2", // imple = implementing_amount ប៉ាន់ស្មានអនុវត្តខែ២
              //                                 imple_amount_plan_month_9: "3", // imple = implementing_amount ប៉ាន់ស្មានអនុវត្តខែ៣
              //                             }
              //                         },
              //                         costing_plan_semester4: {
              //                             // គ្រោងដើមឆ្នាំ
              //                             year_planning: {
              //                                 total_amount: "555",
              //                                 amount_plan_month_10: "1",
              //                                 amount_plan_month_11: "2",
              //                                 amount_plan_month_12: "3",
              //                             },
              //                             impl_est: {
              //                                 impl_est_amount: "5554",
              //                                 imple_amount_plan_month_10: "1", // imple = implementing_amount ប៉ាន់ស្មានអនុវត្តខែ១
              //                                 imple_amount_plan_month_11: "2", // imple = implementing_amount ប៉ាន់ស្មានអនុវត្តខែ២
              //                                 imple_amount_plan_month_12: "3", // imple = implementing_amount ប៉ាន់ស្មានអនុវត្តខែ៣
              //                             }
              //                         },
              //                         remark: "កំណត់ត្រា",
              //                     }
              //                 ]
              //             }
              //         ],
              //     },
              // ],
              account_group_id: [
                  {
                      value: 1,
                      label: "70-"
                  }, {
                      value: 2,
                      label: "73-"
                  },
              ],
              account_id: [
                  {
                      value: 2,
                      label: "7302"
                  },
              ],
              sub_account_id: [
                  {
                      value: 2,
                      label: "73023"
                  },
              ],
              entity_id: [
                  {
                      value: 2,
                      label: "ក្រសួង-ស្ថាប័ន"
                  }
              ],
              finance_rule: [
                  {
                      value: 1,
                      label: "2023"
                  }
              ],
              limit: 10,
              total: 3,
          },
          formAttributes: [
              {
                  name: "account_group_id",
                  type: "select",
                  options: [],
                  required: true,
                  hasFilter: true,
                  filterObj: "account_id",
                  api: apiConfig._apiGetAccountByGroup
              },
              {
                  name: "account_id",
                  type: "select",
                  options: [],
                  required: true,
                  hasFilter: true,
                  filterObj: "sub_account_id",
                  api: apiConfig._apiGetSubAccount
              },
              {
                  name: "sub_account_id",
                  type: "select",
                  options: [],
                  required: true
              },
              {
                  name: "entity_id",
                  type: "select",
                  options: [],
                  required: true
              }
          ],
          rowDisplay: "3grid", //1grid, 2grid, 3grid, 4grid
          dataFields: [],
          dataInfo: {},
          dataInfo: {
              indicator: {
                  data: {

                  }
              }
          },
      }
  },
  components: {
      DTableList,
      FormIncomeProgram,
      DForm
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
      },
      initPushDataTable(obj) {
          this.dataElements = [];
          let _data = [
              {
                  id: 1,
                  name: "tests"
              },
              {
                  id: 2,
                  name: "test2"
              }
          ];
          _data.forEach(_obj => {
              this.dataElements.push(_obj);
          });
      },
      openToggleForm() {
          this.enableToggleForm = true
      },
  },
  created() {
      this.$vs.loading();
      this.getData();
  },
  watch: {
  }
}
</script>
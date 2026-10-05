<template>
    <div id="table-demo">
      <d-table-list @clicked="initTableData" :api="api" ref="refInitPage" :allowDel="true" :title="title"
        :dataInfo="dataInfo" :dataAttributes="dataAttributes" :dataHeaders="dataHeaders" :dataTables="data"
        :formAttributes="formAttributes" :rowDisplay="rowDisplay"></d-table-list>
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
        title: "ExpenseResult",
        api: apiConfig._apiGetIncomeImpleResult,
        dataAttributes: {
          tableStyle: 7,
          page_number: 1,
          offset: 0,
          dataGrid: "row",
          hasHeadingReport: false,
          popupFullscreen: true,
          headingReport: "CreditExpenseRule",
          actionButton: [
            {
              icon: "EditIcon",
              path: "/module/budget-arrangement/budget-ceiling/list",
              method: "Edit"
            },
            {
              icon: "TrashIcon",
              path: "/module/budget-arrangement/budget-ceiling/list",
              method: "Delete"
            }
          ]
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
                label: "ចំណាត់ថ្នាក់ចំណាយ",
                rowspan: 3,
                colspan: 0,
            },
            header5: {
                label: "ច្បាប់ហិរញ្ញវត្ថុ",
                rowspan: 0,
                colspan: 0,
            },
            header6: {
                label: "អនុវត្ត",
                rowspan: 0,
                colspan: 0,
            },
            header7: {
                label: "អនុ/ច្បាប់",
                rowspan: 0,
                colspan: 0,
            },
        },
        data: {
          dataHeaders: {
            // header1: {
            //   label: "មូលដ្ឋានគិតចំណូល",
            //   rowspan: 2,
            //   colspan: 0,
            // },
            // header2: {
            //   label: "បរិមាណ",
            //   rowspan: 2,
            //   colspan: 0,
            // },
          },
          dataSubHeaders: {
            // header1: {
            //   label: "សរុប",
            //   rowspan: 0,
            //   colspan: 0,
            // },
            // header2: {
            //   label: "%ច្ប.២២",
            //   rowspan: 0,
            //   colspan: 0,
            // },
            // header3: {
            //   label: "សរុប​",
            //   rowspan: 0,
            //   colspan: 0,
            // },
            // header4: {
            //   label: "%ច្ប.២២",
            //   rowspan: 0,
            //   colspan: 0,
            // }
          },
          dataFillables: {
            field1: "chapter",
            field2: "account",
            field3: "sub_account",
            field4: "cost_classification",
            field5: "income_year_minus_n",
            field6: "f_rule_year_n_basic_cal_income", // f_rule_year_n_basic_cal_income => Finance Rule By Current Year (n=number of the curent year) (cal_income = calculated Income")
          },
          group_fields: { field1: "rev_group", field2: "rev_type", field3: "entity" },
          summary: [
            {
              chapter: "",
              account: "",
              sub_account: "",
              cost_classification: "សរុប​រួមចំណូល​ (ក+ខ)",
              income_year_minus_n: "0.00",
              f_rule_year_n_basic_cal_income: "0.00", // f_rule_year_n_basic_cal_income => Finance Rule By Current Year (n=number of the curent year) (cal_income = calculated Income)
              f_rule_year_n_quantity: "0.00",
              f_rule_year_n_credit_budget: "0.00",
              est_budget_year_n_semester_1: "0.00",
              est_budget_year_n_semester_2: "0.00",
              est_cal_year_n_total: "0.00",
              est__percentage_f_rule_year_n: "0.00",
              f_plan_year_n1_basic_cal_income: "0.00",
              f_plan_year_n1_quantity: "0.00",
              f_plan_total_income: "0.00",
              f_plan_percentage_year_n: "0.00",
            },
            {
              chapter: "",
              account: "",
              sub_account: "",
              cost_classification: "សរុប​រួមចំណូល​ (ក+ខ)",
              income_year_minus_n: "0.00",
            },
            {
              chapter: "",
              account: "",
              sub_account: "",
              cost_classification: "សរុប​រួមចំណូល​ (ក+ខ)",
              income_year_minus_n: "0.00",
            }
          ],
          data: [
            {
                entity: {
                    id: "",
                    name: "ឈ្មោះអង្គភាព",
                },
                children: [
                    {
                        chapter: "70",
                        account: "",
                        sub_account: "",
                        cost_classification: "ចំណូលសារពើពន្ធក្នុងស្រុក",
                        income_year_minus_n: "0.00",
                        f_rule_year_n_basic_cal_income: "0.00", // f_rule_year_n_basic_cal_income => Finance Rule By Current Year (n=number of the curent year) (cal_income = calculated Income)
                        has_child: true,
                        children: [
                        {
                            chapter: "",
                            account: "7001",
                            sub_account: "",
                            cost_classification: "ពន្ធផ្ទាល់ (ពន្ធលើផលទុន ប្រាក់ចំណូល)",
                            income_year_minus_n: "0.00",
                            f_rule_year_n_basic_cal_income: "0.00", // f_rule_year_n_basic_cal_income => Finance Rule By Current Year (n=number of the curent year) (cal_income = calculated Income)
                            has_child: true,
                            children: [
                            {
                                chapter: "",
                                account: "",
                                sub_account: "70011",
                                cost_classification: "ពន្ធលើប្រាក់ចំណូល (រូបវន្តបុគ្គល)",
                                income_year_minus_n: "0.00",
                                f_rule_year_n_basic_cal_income: "0.00", // f_rule_year_n_basic_cal_income => Finance Rule By Current Year (n=number of the curent year) (cal_income = calculated Income)
                            },
                            {
                                chapter: "",
                                account: "",
                                sub_account: "70012",
                                cost_classification: "ពន្ធលើប្រាក់ចំណូល (នីតិបុគ្គល)",
                                income_year_minus_n: "0.00",
                                f_rule_year_n_basic_cal_income: "0.00", // f_rule_year_n_basic_cal_income => Finance Rule By Current Year (n=number of the curent year) (cal_income = calculated Income)
                            },
                            {
                                chapter: "",
                                account: "",
                                sub_account: "70013",
                                cost_classification: "ពន្ធលើប្រាក់បៀវត្ស",
                                income_year_minus_n: "0.00",
                                f_rule_year_n_basic_cal_income: "0.00", // f_rule_year_n_basic_cal_income => Finance Rule By Current Year (n=number of the curent year) (cal_income = calculated Income)
                            }
                            ]
                        }
                        ]
                    }
                ]
            },
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
        },
          {
            name: "ច្បាប់ហិរញ្ញវត្ថុ",
            type: "grid",
            flag: "y_n_minus_1",
            required: false,
          },
          {
            name: "ច្បាប់ហិរញ្ញវត្ថុ",
            type: "number",
            required: false,
          },
          {
            name: "អនុវត្ត",
            type: "number",
            required: false,
          },
          {
            name: "អនុវត្ត/ច្បាប់%",
            type: "number",
            required: false,
          }
        ],
        rowDisplay: "3grid", //1grid, 2grid, 3grid, 4grid
        dataFields: [],
        dataInfo: {}
      }
    },
    components: {
      DTableList
    },
    methods: {
      initRequest() {
        this.$vs.loading();
        this.getData();
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
  
<template>
    <div id="table-demo">
        <!-- <table-state></table-state> -->
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
            title: "pb_heading_pb_expense",
            api: apiConfig._apiUnit,
            dataAttributes: {
                enableDownload: true,
                tableStyle: 6,
                page_number: 1,
                offset: 0,
                dataGrid: "row",
                hasHeadingReport: true,
                headingReport: "pb_heading_pb_expense",
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
                    label: "ជំពូក",
                    rowspan: 3,
                    colspan: 0,
                },
                header2: {
                    label: "គណនី",
                    rowspan: 3,
                    colspan: 0,
                },
                header3: {
                    label: "អនុគណនី",
                    rowspan: 3,
                    colspan: 0,
                },
                header4: {
                    label: "ចំណាត់ថ្នាក់ចំណាយ(តាមតារាងមាតិកាថវិកា) ",
                    rowspan: 3,
                    colspan: 0,
                },
                header5: {
                    label: "ចំណាយឆ្នាំ{P-2}(អនុវត្ត) ",
                    rowspan: 3,
                    colspan: 0,
                },
                header6: {
                    label: "ច្បាប់ហិរញ្ញវត្ថុឆ្នាំ{P-1}",
                    rowspan: 0,
                    colspan: 3,
                },
                header7: {
                    label: "ប៉ាន់ស្មាន​អនុវត្ត​ឆ្នាំ​{P-1}",
                    rowspan: 0,
                    colspan: 4,
                },
                header8: {
                    label: "គម្រោងថវិកា​ឆ្នាំ​{P}",
                    rowspan: 0,
                    colspan: 4,
                }
            },
            data: {
                dataHeaders: {
                    header1: {
                        label: "ឥណទានដើមឆ្នាំ",
                        rowspan: 2,
                        colspan: 0,
                    },
                    header2: {
                        label: "ឥណទានវិចារណកម្ម",
                        rowspan: 2,
                        colspan: 0,
                    },
                    header3: {
                        label: "ឥណទានថ្មី",
                        rowspan: 2,
                        colspan: 0,
                    },
                    header4: {
                        label: "អនុវត្ដជាក់ស្តែងឆមាសទី១",
                        rowspan: 2,
                        colspan: 0,
                    },
                    header5: {
                        label: "ប៉ាន់ស្មានឆមាសទី២",
                        rowspan: 2,
                        colspan: 0,
                    },
                    header6: {
                        label: "ប៉ាន់ស្មានឆមាសទី២",
                        rowspan: 0,
                        colspan: 2,
                    },
                    header7: {
                        label: "មូលដ្ឋានគិតចំណាយ",
                        rowspan: 2,
                        colspan: 0,
                    },
                    header8: {
                        label: "មូលដ្ឋានគិតចំណាយ",
                        rowspan: 2,
                        colspan: 0,
                    },
                    header9: {
                        label: "សរុប​ការ​គ្រោ​ង​ចំណាយ",
                        rowspan: 0,
                        colspan: 2,
                    }
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
                dataFillables: {
                    field1: "chapter",
                    field2: "account",
                    field3: "sub_account",
                    field4: "cost_classification",
                    field5: "income_year_minus_n",
                    field6: "f_rule_year_n_basic_cal_income", // f_rule_year_n_basic_cal_income => Finance Rule By Current Year (n=number of the curent year) (cal_income = calculated Income")
                    field7: "f_rule_year_n_quantity",
                    field8: "f_rule_year_n_credit_budget",
                    field9: "est_budget_year_n_semester_1",
                    field10: "est_budget_year_n_semester_2",
                    field11: "est_cal_year_n_total",
                    field12: "est__percentage_f_rule_year_n",
                    field13: "f_plan_year_n1_basic_cal_income",
                    field14: "f_plan_year_n1_quantity",
                    field15: "f_plan_total_income",
                    field16: "f_plan_percentage_year_n"
                },
                group_fields: { field1: "rev_group", field2: "rev_type" },
                summary: [
                    {
                        chapter: "",
                        account: "",
                        sub_account: "",
                        cost_classification: "សរុប​រួមចំណាយ​ (ក+ខ)",
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
                        cost_classification: "សរុប​រួមចំណាយ​ (ក+ខ)",
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
                        cost_classification: "សរុប​រួមចំណាយ​ (ក+ខ)",
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
                    }
                ],
                data: [
                    {
                        rev_group: {
                            chapter: "",
                            account: "",
                            sub_account: "",
                            cost_classification: "ក្រុមទី១: ចំណាយ​ពិត (ប្រភេទទី១+ប្រភេទទី២)",
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
                            f_plan_percentage_year_n: "0.00"
                        },
                        rev_type: {
                            chapter: "",
                            account: "",
                            sub_account: "",
                            cost_classification: "ប្រភេទទី១: ចំណាយ​សារពើពន្ធ",
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
                            f_plan_percentage_year_n: "0.00"
                        },
                        children: [
                            {
                                chapter: "70",
                                account: "",
                                sub_account: "",
                                cost_classification: "ចំណាយសារពើពន្ធក្នុងស្រុក",
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
                                has_child: true,
                                children: [
                                    {
                                        chapter: "",
                                        account: "7001",
                                        sub_account: "",
                                        cost_classification: "ពន្ធផ្ទាល់ (ពន្ធលើផលទុន ប្រាក់ចំណាយ)",
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
                                        has_child: true,
                                        children: [
                                            {
                                                chapter: "",
                                                account: "",
                                                sub_account: "70011",
                                                cost_classification: "ពន្ធលើប្រាក់ចំណាយ (រូបវន្តបុគ្គល)",
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
                                                sub_account: "70012",
                                                cost_classification: "ពន្ធលើប្រាក់ចំណាយ (នីតិបុគ្គល)",
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
                                                sub_account: "70013",
                                                cost_classification: "ពន្ធលើប្រាក់បៀវត្ស",
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
                                            }
                                        ]
                                    }
                                ]
                            }
                        ]
                    }
                ],
                limit: 10,
                total: 3,
            },
            formAttributes: [
                {
                    name: "objective",
                    type: "select",
                    required: true,
                    data: [
                        {
                            "id": 1,
                            "name": "គោលបំណងទី១",
                            "name_kh": ""
                        },
                        {
                            "id": 2,
                            "name": "គោលបំណងទី២",
                            "name_kh": ""
                        }
                    ],
                },
                {
                    name: "name",
                    type: "text",
                    required: true
                },
                {
                    name: "name_kh",
                    type: "text",
                    required: true
                },
                {
                    name: "responsible_person",
                    type: "text",
                    required: true
                },
                {
                    name: "responsible_entity",
                    type: "text",
                    required: true
                },
                {
                    name: "order_level",
                    type: "number",
                    required: false
                },
                {
                    name: "remark",
                    type: "text",
                    required: false
                }
            ],
            rowDisplay: "2grid", //1grid, 2grid, 3grid, 4grid
            dataFields: [],
            dataInfo: {}
        }
    },
    components: {
        DTableList
    },
    methods: {
        initDownload() {
            const _base_url = window.location.origin;
            window.location = _base_url + '/download/PB/2023/PB-Cosolidate-File.xlsx';
        },
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
                        // this.data = response.data;
                        this.data = this.data;
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
        // this.$vs.loading();
        // this.getData();
    },
    watch: {
    }
}
</script>

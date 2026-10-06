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
            title: "pb_heading_staff_expense",
            api: apiConfig._apiUnit,
            dataAttributes: {
                enableDownload: true,
                tableStyle: 6,
                page_number: 1,
                offset: 0,
                dataGrid: "row",
                hasHeadingReport: true,
                headingReport: "pb_heading_staff_expense"
            },
            dataHeaders: {
                header1: {
                    label: "គណនី/អនុគណនី",
                    rowspan: 2,
                    colspan: 0,
                },
                header3: {
                    width: 350,
                    label: "បរិយាយ",
                    rowspan: 2,
                    colspan: 0,
                },
                header4: {
                    label: "ច្បាប់ហិរញ្ញវត្ថុឆ្នាំ{P-1}",
                    rowspan: 2,
                    colspan: 0,
                },
                header5: {
                    label: "ឥណទានថ្មី",
                    rowspan: 2,
                    colspan: 0,
                },
                header6: {
                    label: "ប៉ាន់ស្មានអនុវត្ត ប្រចាំឆ្នាំ{P-1}",
                    rowspan: 0,
                    colspan: 4,
                },
                header7: {
                    label: "ស្ថានភាពក្របខណ្ឌ",
                    rowspan: 0,
                    colspan: 6,
                },
                header8: {
                    label: "មូលដ្ឋាននៃការគ្រោង",
                    rowspan: 0,
                    colspan: 5,
                },
                header9: {
                    label: "គម្រោងបៀវត្ស១២ខែ",
                    rowspan: 0,
                    colspan: 3,
                },
            },
            data: {
                dataHeaders: {
                    header1: {
                        label: "អនុវត្ត ៦ខែដើមឆ្នាំ",
                        rowspan: 0,
                        colspan: 0,
                    },
                    header2: {
                        label: "ប៉ាន់អនុវត្ត៦ខែចុងឆ្នាំ",
                        rowspan: 0,
                        colspan: 0,
                    },
                    header3: {
                        label: "ប៉ាន់អនុវត្តប្រចាំឆ្នាំ",
                        rowspan: 0,
                        colspan: 0,
                    },
                    header4: {
                        label: "%ឥ.ច្ប",
                        rowspan: 0,
                        colspan: 0,
                    },
                    header5: {
                        label: "ចំនួនក្របខណ្ឌខែ៦ ​'២២",
                        rowspan: 0,
                        colspan: 0,
                    },
                    header6: {
                        label: "ក្របខណ្ឌបញ្ចូលថ្មី ចាប់ពីខែ៧",
                        rowspan: 0,
                        colspan: 2,
                    },
                    header7: {
                        label: "ក្របខណ្ឌដកចេញ ចាប់ពីខែ៧",
                        rowspan: 0,
                        colspan: 0,
                    },
                    header8: {
                        label: "ក្របខណ្ឌបញ្ចូលថ្មី ឆ្នាំ{P}",
                        rowspan: 0,
                        colspan: 0,
                    },
                    header9: {
                        width: 150,
                        label: "ក្របខណ្ឌ ដកចេញឆ្នាំ{P}",
                        rowspan: 0,
                        colspan: 0,
                    },
                    header10: {
                        label: "ក្របខណ្ឌរួមមូលដ្ឋានគ្រោង '២៣",
                        rowspan: 0,
                        colspan: 0,
                    },
                    header11: {
                        label: "ចំនួនសន្ទស្សន៍",
                        rowspan: 0,
                        colspan: 0,
                    },
                    header12: {
                        label: "តម្លៃឯកតា សទ.ឆ្នាំ{P-1}",
                        rowspan: 0,
                        colspan: 0,
                    },
                    header13: {
                        label: "តម្លៃសទ. ត្រូវគ្រោង '២៣",
                        rowspan: 0,
                        colspan: 0,
                    },
                    // header14: {
                    //     label: "ទឹកប្រាក់មុខងារ ២០២២",
                    //     rowspan: 0,
                    //     colspan: 0,
                    // },
                    header15: {
                        label: "ប្រាក់មុខងារគ្រោង '២៣",
                        rowspan: 0,
                        colspan: 0,
                    },
                    header16: {
                        label: "គម្រោងបៀវត្សឆ្នាំ{P}",
                        rowspan: 0,
                        colspan: 0,
                    },
                    header17: {
                        label: "%.ច្ប {P-1}",
                        rowspan: 0,
                        colspan: 0,
                    },
                },
                dataSubHeaders: {
                    header1: {
                        label: "",
                        rowspan: 0,
                        colspan: 0,
                    },
                    header2: {
                        label: "",
                        rowspan: 0,
                        colspan: 0,
                    },
                    header3: {
                        label: "1",
                        rowspan: 0,
                        colspan: 0,
                    },
                    header4: {
                        label: "2",
                        rowspan: 0,
                        colspan: 0,
                    },
                    header5: {
                        label: "3",
                        rowspan: 0,
                        colspan: 0,
                    },
                    header6: {
                        label: "4",
                        rowspan: 0,
                        colspan: 0,
                    },
                    header7: {
                        label: "5=3+4",
                        rowspan: 0,
                        colspan: 0,
                    },
                    header8: {
                        label: "6=5/1",
                        rowspan: 0,
                        colspan: 0,
                    },
                    header9: {
                        label: "7",
                        rowspan: 0,
                        colspan: 0,
                    },
                    header10: {
                        label: "8",
                        rowspan: 0,
                        colspan: 0,
                    },
                    header11: {
                        label: "9",
                        rowspan: 0,
                        colspan: 0,
                    },
                    header12: {
                        label: "10",
                        rowspan: 0,
                        colspan: 0,
                    },
                    header13: {
                        label: "11",
                        rowspan: 0,
                        colspan: 0,
                    },
                    header14: {
                        label: "12=7+8+9+10-11",
                        rowspan: 0,
                        colspan: 0,
                    },
                    header15: {
                        label: "13",
                        rowspan: 0,
                        colspan: 0,
                    },
                    header16: {
                        label: "14",
                        rowspan: 0,
                        colspan: 0,
                    },
                    header17: {
                        label: "15",
                        rowspan: 0,
                        colspan: 0,
                    },
                    header18: {
                        label: "16",
                        rowspan: 0,
                        colspan: 0,
                    },
                    header19: {
                        label: "17",
                        rowspan: 0,
                        colspan: 0,
                    },
                    header20: {
                        label: "18",
                        rowspan: 0,
                        colspan: 0,
                    },
                    header21: {
                        label: "19=18/1",
                        rowspan: 0,
                        colspan: 0,
                    },
                },
                dataFillables: {
                    field1: "sub_account",
                    field2: "description",
                    field3: "income_year_minus_n",
                    field4: "f_rule_year_n", // f_rule_year_n_basic_cal_income => Finance Rule By Current Year (n=number of the curent year) (cal_income = calculated Income")
                    field5: "new_credit",
                    field6: "month_implementing",
                    field7: "est_budget",
                    field8: "est_budget_year",
                    field9: "est_implementing_year_n",
                    field10: "est_percentage_f_rule_year_n",
                    field11: "n_new_framwork_6_month",
                    field12: "data_field_a",
                    field13: "data_field_b",
                    field14: "data_field_c",
                    field15: "data_field_d",
                    field16: "data_field_e",
                    field17: "data_field_f",
                    field18: "data_field_g",
                    field19: "data_field_h",
                    field20: "data_field_i",
                    field21: "data_field_j",
                },
                // group_fields: { field1: "rev_group", field2: "rev_type" },
                summary: [
                    {
                        sub_account: "",
                        description: "សរុប​រួមចំណាយ​(ក+ខ)",
                        income_year_minus_n: "0.00",
                        f_rule_year_n: "0.00",
                        new_credit: "0.00",
                        month_implementing: "0.00", // f_rule_year_n_basic_cal_income => Finance Rule By Current Year (n=number of the curent year) (cal_income = calculated Income)
                        est_budget: "0.00",
                        est_budget_year: "0.00",
                        est_implementing_year_n: "0.00",
                        est_percentage_f_rule_year_n: "0.00",
                        n_new_framwork_6_month: "0.00",
                        data_field_a: "0.00",
                        data_field_b: "0.00",
                        data_field_c: "0.00",
                        data_field_d: "0.00",
                        data_field_e: "0.00",
                        data_field_f: "0.00",
                        data_field_g: "0.00",
                        data_field_h: "0.00",
                        data_field_i: "0.00",
                        data_field_j: "0.00"
                    }
                ],
                data: [
                    {
                        children: [
                            {
                                sub_account: "64011",
                                description: "ខុទ្ទកាល័យព្រះបរមរាជវាំង",
                                income_year_minus_n: "0.00",
                                f_rule_year_n: "0.00",
                                new_credit: "0.00",
                                month_implementing: "0.00", // f_rule_year_n_basic_cal_income => Finance Rule By Current Year (n=number of the curent year) (cal_income = calculated Income)
                                est_budget: "0.00",
                                est_budget_year: "0.00",
                                est_implementing_year_n: "0.00",
                                est_percentage_f_rule_year_n: "0.00",
                                n_new_framwork_6_month: "0.00",
                                data_field_a: "0.00",
                                data_field_b: "0.00",
                                data_field_c: "0.00",
                                data_field_d: "0.00",
                                data_field_e: "0.00",
                                data_field_f: "0.00",
                                data_field_g: "0.00",
                                data_field_h: "0.00",
                                data_field_i: "0.00",
                                data_field_j: "0.00",
                                has_child: true,
                                children: [

                                ]
                            }
                        ]
                    },
                    {
                        children: [
                            {
                                sub_account: "64012",
                                description: "ប្រាក់បំណាច់ ប្រធាន អនុប្រធាន និងសមាជិកស្ថាប័នកំពូល",
                                income_year_minus_n: "0.00",
                                f_rule_year_n: "0.00",
                                new_credit: "0.00",
                                month_implementing: "0.00", // f_rule_year_n_basic_cal_income => Finance Rule By Current Year (n=number of the curent year) (cal_income = calculated Income)
                                est_budget: "0.00",
                                est_budget_year: "0.00",
                                est_implementing_year_n: "0.00",
                                est_percentage_f_rule_year_n: "0.00",
                                n_new_framwork_6_month: "0.00",
                                data_field_a: "0.00",
                                data_field_b: "0.00",
                                data_field_c: "0.00",
                                data_field_d: "0.00",
                                data_field_e: "0.00",
                                data_field_f: "0.00",
                                data_field_g: "0.00",
                                data_field_h: "0.00",
                                data_field_i: "0.00",
                                data_field_j: "0.00",
                                has_child: true,
                                children: [

                                ]
                            }
                        ]
                    },
                    {
                        children: [
                            {
                                sub_account: "64013",
                                description: "ប្រាក់បំណាច់នាយករដ្ឋមន្រ្តី និងឧបនាយករដ្ឋមន្រ្តី",
                                income_year_minus_n: "0.00",
                                f_rule_year_n: "0.00",
                                new_credit: "0.00",
                                month_implementing: "0.00", // f_rule_year_n_basic_cal_income => Finance Rule By Current Year (n=number of the curent year) (cal_income = calculated Income)
                                est_budget: "0.00",
                                est_budget_year: "0.00",
                                est_implementing_year_n: "0.00",
                                est_percentage_f_rule_year_n: "0.00",
                                n_new_framwork_6_month: "0.00",
                                data_field_a: "0.00",
                                data_field_b: "0.00",
                                data_field_c: "0.00",
                                data_field_d: "0.00",
                                data_field_e: "0.00",
                                data_field_f: "0.00",
                                data_field_g: "0.00",
                                data_field_h: "0.00",
                                data_field_i: "0.00",
                                data_field_j: "0.00",
                                has_child: true,
                                children: [
                                    {
                                        sub_account: "",
                                        description: "នាយករដ្ឋមន្ដ្រី",
                                        income_year_minus_n: "0.00",
                                        f_rule_year_n: "0.00",
                                        new_credit: "0.00",
                                        month_implementing: "0.00", // f_rule_year_n_basic_cal_income => Finance Rule By Current Year (n=number of the curent year) (cal_income = calculated Income)
                                        est_budget: "0.00",
                                        est_budget_year: "0.00",
                                        est_implementing_year_n: "0.00",
                                        est_percentage_f_rule_year_n: "0.00",
                                        n_new_framwork_6_month: "0.00",
                                        data_field_a: "0.00",
                                        data_field_b: "0.00",
                                        data_field_c: "0.00",
                                        data_field_d: "0.00",
                                        data_field_e: "0.00",
                                        data_field_f: "0.00",
                                        data_field_g: "0.00",
                                        data_field_h: "0.00",
                                        data_field_i: "0.00",
                                        data_field_j: "0.00",
                                        has_child: false,
                                        children: [

                                        ]
                                    },
                                    {
                                        sub_account: "",
                                        description: "ឧបនាយករដ្ឋមន្ដ្រី",
                                        income_year_minus_n: "0.00",
                                        f_rule_year_n: "0.00",
                                        new_credit: "0.00",
                                        month_implementing: "0.00", // f_rule_year_n_basic_cal_income => Finance Rule By Current Year (n=number of the curent year) (cal_income = calculated Income)
                                        est_budget: "0.00",
                                        est_budget_year: "0.00",
                                        est_implementing_year_n: "0.00",
                                        est_percentage_f_rule_year_n: "0.00",
                                        n_new_framwork_6_month: "0.00",
                                        data_field_a: "0.00",
                                        data_field_b: "0.00",
                                        data_field_c: "0.00",
                                        data_field_d: "0.00",
                                        data_field_e: "0.00",
                                        data_field_f: "0.00",
                                        data_field_g: "0.00",
                                        data_field_h: "0.00",
                                        data_field_i: "0.00",
                                        data_field_j: "0.00",
                                        has_child: false,
                                        children: [

                                        ]
                                    },
                                    {
                                        sub_account: "",
                                        description: "ទេសរដ្ឋមន្ដ្រី",
                                        income_year_minus_n: "0.00",
                                        f_rule_year_n: "0.00",
                                        new_credit: "0.00",
                                        month_implementing: "0.00", // f_rule_year_n_basic_cal_income => Finance Rule By Current Year (n=number of the curent year) (cal_income = calculated Income)
                                        est_budget: "0.00",
                                        est_budget_year: "0.00",
                                        est_implementing_year_n: "0.00",
                                        est_percentage_f_rule_year_n: "0.00",
                                        n_new_framwork_6_month: "0.00",
                                        data_field_a: "0.00",
                                        data_field_b: "0.00",
                                        data_field_c: "0.00",
                                        data_field_d: "0.00",
                                        data_field_e: "0.00",
                                        data_field_f: "0.00",
                                        data_field_g: "0.00",
                                        data_field_h: "0.00",
                                        data_field_i: "0.00",
                                        data_field_j: "0.00",
                                        has_child: false,
                                        children: [

                                        ]
                                    },
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
            window.location = _base_url + '/download/PB/2023/4-GDP_BSP_ចំណាយបន្ទុកបុគ្គលិក.xlsx';
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

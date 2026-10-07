<template>
    <div>
        <task></task>
    </div>
</template>

<script>
import axios from "@/axios.js"
import apiConfig from "@/apiConfig.js"
import { ref } from 'vue';
import Task from '@/views/pages/modules/program-management/_Task.vue';

export default {
    data() {
        return {
            title: "pb_heading_costing",
            api: apiConfig._apiUnit,
            dataAttributes: {
                tableStyle: 11,
                page_number: 1,
                offset: 0,
                dataGrid: "row",
                hasHeadingReport: false,
                popupFullscreen: true,
                enableToggleForm: true,
                headingReport: "pb_heading_costing",
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

                header8: {
                    label: "បរិយាយមុខសញ្ញា",
                    width: "400",
                    // flag: "y_n_1",
                    rowspan: 0,
                    colspan: 0,
                },
                header1: {
                    label: "កូដកម្មវិធី",
                    width: 0,
                    rowspan: 0,
                    colspan: 0,
                },
                header2: {
                    label: "ជំពូក",
                    width: 0,
                    rowspan: 0,
                    colspan: 0,
                },
                header3: {
                    label: "លេខ",
                    width: 0,
                    rowspan: 0,
                    colspan: 0,
                },
                header4: {
                    label: "ល.រ.",
                    width: 0,
                    rowspan: 0,
                    colspan: 0,
                },
                header5: {
                    label: "កូដចង្កោមសកម្មភាព",
                    flag: "y_n_minus_1",
                    rowspan: 0,
                    colspan: 0,
                },
                header6: {
                    label: "កូដសកម្មភាព",
                    flag: "y_n",
                    rowspan: 0,
                    colspan: 0,
                },
                header7: {
                    label: "អនុគណនី",
                    rowspan: 0,
                    colspan: 0,
                },
                header9: {
                    label: "ប្រចាំ/មិនប្រចាំ",
                    rowspan: 0,
                    colspan: 0,
                },
                header10: {
                    label: "ឯកតា",
                    rowspan: 0,
                    colspan: 0,
                },
                header11: {
                    label: "បរិមាណ",
                    rowspan: 0,
                    colspan: 0,
                },
                header12: {
                    label: "រូបិយប័ណ្ណ",
                    rowspan: 0,
                    colspan: 0,
                },
                header13: {
                    label: "តម្លៃឯកតា",
                    rowspan: 0,
                    colspan: 0,
                },
                header14: {
                    label: "តម្លៃ១លើក",
                    rowspan: 0,
                    colspan: 0,
                },
                header15: {
                    label: "លើក/ឆ្នាំ",
                    flag: "y_n_1",
                    rowspan: 0,
                    colspan: 0,
                },
                header16: {
                    label: "សរុបប្រចាំឆ្នាំ",
                    rowspan: 0,
                    colspan: 0,
                },
                header17: {
                    label: "ខែ",
                    rowspan: 0,
                    colspan: 0,
                },
                header18: {
                    label: "មុខសញ្ញា",
                    rowspan: 0,
                    colspan: 0,
                },
                header19: {
                    label: "ផ្សេងៗ",
                    rowspan: 0,
                    colspan: 0,
                },
                header20: {
                    label: "សកម្ម",
                    rowspan: 0,
                    colspan: 0,
                }
            },
            data: {
                dataHeaders: {
                    header8: {
                        label: "item_description",
                        // flag: "y_n_1",
                        rowspan: 0,
                        colspan: 0,
                    },
                    header1: {
                        label: "",
                        width: 0,
                        rowspan: 0,
                        colspan: 0,
                    },
                    header2: {
                        label: "",
                        width: 0,
                        rowspan: 0,
                        colspan: 0,
                    },
                    header3: {
                        label: "Line#",
                        width: 0,
                        rowspan: 0,
                        colspan: 0,
                    },
                    header4: {
                        label: "",
                        width: 0,
                        rowspan: 0,
                        colspan: 0,
                    },
                    header5: {
                        label: "Act.Cluster",
                        flag: "y_n_minus_1",
                        rowspan: 0,
                        colspan: 0,
                    },
                    header6: {
                        label: "Pro.Activity",
                        flag: "y_n",
                        rowspan: 0,
                        colspan: 0,
                    },
                    header7: {
                        label: "Sub-Account",
                        rowspan: 0,
                        colspan: 0,
                    },
                    header9: {
                        label: "regular_irregular",
                        rowspan: 0,
                        colspan: 0,
                    },
                    header10: {
                        label: "unit",
                        rowspan: 0,
                        colspan: 0,
                    },
                    header11: {
                        label: "quantity",
                        rowspan: 0,
                        colspan: 0,
                    },
                    header12: {
                        label: "currency",
                        rowspan: 0,
                        colspan: 0,
                    },
                    header13: {
                        label: "cost_unit",
                        rowspan: 0,
                        colspan: 0,
                    },
                    header14: {
                        label: "cost_per_time", //តម្លៃ១លើក
                        rowspan: 0,
                        colspan: 0,
                    },
                    header15: {
                        label: "per_annual", //លើក/ឆ្នាំ
                        flag: "y_n_1",
                        rowspan: 0,
                        colspan: 0,
                    },
                    header16: {
                        label: "total_annual", //សរុបប្រចាំឆ្នាំ
                        rowspan: 0,
                        colspan: 0,
                    },
                    header17: {
                        label: "month",
                        rowspan: 0,
                        colspan: 0,
                    },
                    header18: {
                        label: "expense_type",
                        rowspan: 0,
                        colspan: 0,
                    },
                    header19: {
                        label: "Misc", //ផ្សេងៗ
                        rowspan: 0,
                        colspan: 0,
                    },
                    header20: {
                        label: "Action", //ផ្សេងៗ
                        rowspan: 0,
                        colspan: 0,
                    }
                },
                dataSubHeaders: {
                    header1: {
                        label: "1",
                        width: 0,
                        rowspan: 0,
                        colspan: 0,
                    },
                    header2: {
                        label: "22",
                        width: 0,
                        rowspan: 0,
                        colspan: 0,
                    },
                    header2: {
                        label: "2",
                        width: 0,
                        rowspan: 0,
                        colspan: 0,
                    },
                    header3: {
                        label: "3",
                        width: 0,
                        rowspan: 0,
                        colspan: 0,
                    },
                    header4: {
                        label: "4",
                        width: "200",
                        rowspan: 0,
                        colspan: 0,
                    },
                    header5: {
                        label: "5",
                        flag: "y_n_minus_1",
                        rowspan: 0,
                        colspan: 0,
                    },
                    header6: {
                        label: "6",
                        flag: "y_n",
                        rowspan: 0,
                        colspan: 0,
                    },
                    header7: {
                        label: "7",
                        rowspan: 0,
                        colspan: 0,
                    },
                    header8: {
                        label: "8",
                        // flag: "y_n_1",
                        rowspan: 0,
                        colspan: 0,
                    },
                    header9: {
                        label: "9",
                        rowspan: 0,
                        colspan: 0,
                    },
                    header10: {
                        label: "10",
                        rowspan: 0,
                        colspan: 0,
                    },
                    header11: {
                        label: "11",
                        rowspan: 0,
                        colspan: 0,
                    },
                    header12: {
                        label: "12",
                        rowspan: 0,
                        colspan: 0,
                    },
                    header13: {
                        label: "13",
                        rowspan: 0,
                        colspan: 0,
                    },
                    header14: {
                        label: "14", //តម្លៃ១លើក
                        rowspan: 0,
                        colspan: 0,
                    },
                    header15: {
                        label: "15", //លើក/ឆ្នាំ
                        flag: "y_n_1",
                        rowspan: 0,
                        colspan: 0,
                    },
                    header16: {
                        label: "16", //សរុបប្រចាំឆ្នាំ
                        rowspan: 0,
                        colspan: 0,
                    },
                    header17: {
                        label: "17", //
                        rowspan: 0,
                        colspan: 0,
                    },
                    header18: {
                        label: "18", //
                        rowspan: 0,
                        colspan: 0,
                    },
                    header19: {
                        label: "19", //ផ្សេងៗ
                        rowspan: 0,
                        colspan: 0,
                    },
                    header20: {
                        label: "", //ផ្សេងៗ
                        rowspan: 0,
                        colspan: 0,
                    }
                },
                dataFillables: {
                    field1: "desc",
                    field2: "code_pro",
                    field3: "chapter",
                    field4: "no",
                    field5: "line",
                    field6: "code_cluster_activity",
                    field7: "code_activity",
                    field8: "sub_account",
                    field9: "regular_irregular",
                    field10: "unit",
                    field11: "quantity",
                    field12: "currency",
                    field13: "unit_price",
                    field14: "total_amount",
                    field15: "time_year",
                    field16: "annual_amount",
                    field17: "month",
                    field18: "expense_type",
                    field19: "remark",
                    // field20: "action",
                },
                group_fields: { field1: "rev_group", field2: "rev_type" },
                summary: [
                    // {
                    //     chapter: "",
                    //     account: "",
                    //     sub_account: "",
                    //     cost_classification: "សរុប​រួមចំណូល​ (ក+ខ)",
                    //     income_year_minus_n: "0.00",
                    //     f_rule_year_n_basic_cal_income: "0.00", // f_rule_year_n_basic_cal_income => Finance Rule By Current Year (n=number of the curent year) (cal_income = calculated Income)
                    //     f_rule_year_n_quantity: "0.00",
                    //     f_rule_year_n_credit_budget: "0.00",
                    //     est_budget_year_n_semester_1: "0.00",
                    //     est_budget_year_n_semester_2: "0.00",
                    //     est_cal_year_n_total: "0.00",
                    //     est__percentage_f_rule_year_n: "0.00",
                    //     f_plan_year_n1_basic_cal_income: "0.00",
                    //     f_plan_year_n1_quantity: "0.00",
                    //     f_plan_total_income: "0.00",
                    //     f_plan_percentage_year_n: "0.00",
                    // },
                    // {
                    //     chapter: "",
                    //     account: "",
                    //     sub_account: "",
                    //     cost_classification: "សរុប​រួមចំណូល​ (ក+ខ)",
                    //     income_year_minus_n: "0.00",
                    //     f_rule_year_n_basic_cal_income: "0.00", // f_rule_year_n_basic_cal_income => Finance Rule By Current Year (n=number of the curent year) (cal_income = calculated Income)
                    //     f_rule_year_n_quantity: "0.00",
                    //     f_rule_year_n_credit_budget: "0.00",
                    //     est_budget_year_n_semester_1: "0.00",
                    //     est_budget_year_n_semester_2: "0.00",
                    //     est_cal_year_n_total: "0.00",
                    //     est__percentage_f_rule_year_n: "0.00",
                    //     f_plan_year_n1_basic_cal_income: "0.00",
                    //     f_plan_year_n1_quantity: "0.00",
                    //     f_plan_total_income: "0.00",
                    //     f_plan_percentage_year_n: "0.00",
                    // },
                    // {
                    //     chapter: "",
                    //     account: "",
                    //     sub_account: "",
                    //     cost_classification: "សរុប​រួមចំណូល​ (ក+ខ)",
                    //     income_year_minus_n: "0.00",
                    //     f_rule_year_n_basic_cal_income: "0.00", // f_rule_year_n_basic_cal_income => Finance Rule By Current Year (n=number of the curent year) (cal_income = calculated Income)
                    //     f_rule_year_n_quantity: "0.00",
                    //     f_rule_year_n_credit_budget: "0.00",
                    //     est_budget_year_n_semester_1: "0.00",
                    //     est_budget_year_n_semester_2: "0.00",
                    //     est_cal_year_n_total: "0.00",
                    //     est__percentage_f_rule_year_n: "0.00",
                    //     f_plan_year_n1_basic_cal_income: "0.00",
                    //     f_plan_year_n1_quantity: "0.00",
                    //     f_plan_total_income: "0.00",
                    //     f_plan_percentage_year_n: "0.00",
                    // }
                ],
                data: [
                    {
                        // rev_group: {
                        //     chapter: "",
                        //     account: "",
                        //     sub_account: "",
                        //     cost_classification: "ក្រុមទី១: ចំណូល​ពិត (ប្រភេទទី១+ប្រភេទទី២)",
                        //     income_year_minus_n: "0.00",
                        //     f_rule_year_n_basic_cal_income: "0.00", // f_rule_year_n_basic_cal_income => Finance Rule By Current Year (n=number of the curent year) (cal_income = calculated Income)
                        //     f_rule_year_n_quantity: "0.00",
                        //     f_rule_year_n_credit_budget: "0.00",
                        //     est_budget_year_n_semester_1: "0.00",
                        //     est_budget_year_n_semester_2: "0.00",
                        //     est_cal_year_n_total: "0.00",
                        //     est__percentage_f_rule_year_n: "0.00",
                        //     f_plan_year_n1_basic_cal_income: "0.00",
                        //     f_plan_year_n1_quantity: "0.00",
                        //     f_plan_total_income: "0.00",
                        //     f_plan_percentage_year_n: "0.00"
                        // },
                        // rev_type: {
                        //     chapter: "",
                        //     account: "",
                        //     sub_account: "",
                        //     cost_classification: "ប្រភេទទី១: ចំណូល​សារពើពន្ធ",
                        //     income_year_minus_n: "0.00",
                        //     f_rule_year_n_basic_cal_income: "0.00", // f_rule_year_n_basic_cal_income => Finance Rule By Current Year (n=number of the curent year) (cal_income = calculated Income)
                        //     f_rule_year_n_quantity: "0.00",
                        //     f_rule_year_n_credit_budget: "0.00",
                        //     est_budget_year_n_semester_1: "0.00",
                        //     est_budget_year_n_semester_2: "0.00",
                        //     est_cal_year_n_total: "0.00",
                        //     est__percentage_f_rule_year_n: "0.00",
                        //     f_plan_year_n1_basic_cal_income: "0.00",
                        //     f_plan_year_n1_quantity: "0.00",
                        //     f_plan_total_income: "0.00",
                        //     f_plan_percentage_year_n: "0.00"
                        // },
                        children: [
                            {
                                desc: "អនុកម្មវិធីទី១.១ : អគ្គនាយកដ្ឋានគោលនយោបាយ",
                                code_pro: "",
                                chapter: "",
                                no: "",
                                line: "",
                                code_cluster_activity: "",
                                code_activity: "",
                                sub_account: "",
                                regular_irregular: "",
                                unit: "",
                                quantity: "",
                                currency: "",
                                unit_price: "",
                                total_amount: "",
                                time_year: "",
                                annual_amount: "",
                                month: "",
                                expense_type: "",
                                remark: "",
                                has_child: true,
                                colspan: 8,
                                children: [
                                    {
                                        desc: "ចង្កោមសកម្មភាពទី១៖ លេខាធិការដ្ឋាន",
                                        code_pro: "",
                                        chapter: "",
                                        no: "",
                                        line: "",
                                        code_cluster_activity: "",
                                        code_activity: "",
                                        sub_account: "",
                                        regular_irregular: "",
                                        unit: "",
                                        quantity: "",
                                        currency: "",
                                        unit_price: "",
                                        total_amount: "",
                                        time_year: "",
                                        annual_amount: "",
                                        month: "",
                                        expense_type: "",
                                        remark: "",
                                        has_child: true,
                                        children: [
                                            {
                                                desc: "សកម្មភាពទី១ នៃចង្កោមសកម្មភាពទី១៖ បំពេញមុខងារជាលេខាធិការដ្ឋាន",
                                                code_pro: "",
                                                chapter: "",
                                                no: "",
                                                line: "",
                                                code_cluster_activity: "",
                                                code_activity: "",
                                                sub_account: "",
                                                regular_irregular: "",
                                                unit: "",
                                                quantity: "",
                                                currency: "",
                                                unit_price: "",
                                                total_amount: "",
                                                time_year: "",
                                                annual_amount: "",
                                                month: "",
                                                expense_type: "",
                                                remark: "",
                                                children: [
                                                    {
                                                        desc: "TSK00012: ១.បន្ទុកបុគ្គលិក",
                                                        code_pro: "",
                                                        chapter: "",
                                                        no: "",
                                                        line: "",
                                                        code_cluster_activity: "",
                                                        code_activity: "",
                                                        sub_account: "",
                                                        regular_irregular: "",
                                                        unit: "",
                                                        quantity: "",
                                                        currency: "",
                                                        unit_price: "",
                                                        total_amount: "",
                                                        time_year: "",
                                                        annual_amount: "",
                                                        month: "",
                                                        expense_type: "",
                                                        remark: "",
                                                        children: [
                                                            {
                                                                desc: "ប្រាក់បៀវត្សមូលដ្ឋានមន្ត្រីរាជការ ចំនួន ៥៦នាក់",
                                                                code_pro: "10101",
                                                                chapter: "64",
                                                                no: "1",
                                                                line: "LN0001",
                                                                code_cluster_activity: "1010101",
                                                                code_activity: "101010101",
                                                                sub_account: "64021",
                                                                regular_irregular: "Regular",
                                                                unit: "ខែ",
                                                                quantity: "1",
                                                                currency: "KHR",
                                                                unit_price: "50,216,000",
                                                                total_amount: "50,216,000",
                                                                time_year: "12",
                                                                annual_amount: "602,592,000",
                                                                month: "",
                                                                expense_type: "",
                                                                remark: "",
                                                            }
                                                        ]
                                                    }
                                                ]
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
                    name: "entity_id",
                    type: "select",
                    required: true,
                    data: [],
                },
                {
                    name: "sub_program_id",
                    type: "select",
                    required: true,
                    data: [],
                },
                {
                    name: "cluster_activity_id",
                    type: "select",
                    required: true,
                    data: [],
                },
                {
                    name: "group_chapter",
                    type: "select",
                    required: true,
                    data: [],
                },
                {
                    name: "account_id",
                    type: "select",
                    required: true,
                    data: [],
                },
                {
                    name: "sub_account_id",
                    type: "select",
                    required: true,
                    data: [],
                },
                {
                    name: "ទិន្នន័យ",
                    type: "grid",
                    flag: "y_n_minus_1",
                    required: false,
                },
                {
                    name: "រង្វាស់ឯកតា",
                    type: "text",
                    required: false,
                },
                {
                    name: "បរិមាណ",
                    type: "text",
                    required: false,
                },
                {
                    name: "ចំនួនក្នុង១ឯកតារ",
                    type: "text",
                    required: false,
                },
                {
                    name: "ចំនួនលើក/១ឆ្នាំ",
                    type: "number",
                    required: false,
                },
                {
                    name: "ប្រភេទចំណាយប្រចាំ/មិនប្រចាំ",
                    type: "text",
                    required: false,
                },
                {
                    name: "បរិមាណ",
                    type: "number",
                    required: false,
                },
                {
                    name: "ផ្សេងៗ",
                    type: "text",
                    required: false,
                }
            ],
            rowDisplay: "3grid", //1grid, 2grid, 3grid, 4grid
            dataFields: [],
            dataInfo: {}
        }
    },
    components: {
        Task,
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
        this.$vs.loading();
        this.getData();
    },
    watch: {
    }
}
</script>
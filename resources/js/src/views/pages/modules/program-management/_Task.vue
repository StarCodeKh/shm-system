<template>
    <div>
        <form-task ref="refOpenPrivatePopupForm" :api="dataAttributes.api" :dataAttributes="dataAttributes"
            :dataInfo="dataInfo"></form-task>
        <d-table-list @clicked="initTableData" :api="api" ref="refInitPage" :allowDel="true" :title="title"
            :dataInfo="dataInfo" :dataAttributes="dataAttributes" :dataHeaders="dataHeaders" :dataTables="data"
            :formAttributes="formAttributes" :rowDisplay="rowDisplay" @clickPrivateForm="initOpenForm"
            @initDownload="initDownload"></d-table-list>
    </div>
</template>

<script>
import axios from "@/axios.js"
import apiConfig from "@/apiConfig.js"
import { ref } from 'vue';
import FormTask from '@/views/pages/modules/program-management/_FormTask.vue';
import FormActivity from '@/views/pages/modules/program-management/_FormActivity.vue';
import DTableList from '@/views/form-builder/DTableList.vue';

export default {
    data() {
        return {
            title: "task",
            api: apiConfig._apiTask,
            dataAttributes: {
                enableDownload: true,
                api: apiConfig._apiGetTaskDetail,
                tableStyle: 12,
                page_number: 1,
                offset: 0,
                dataGrid: "row",
                hasHeadingReport: false,
                popupFullscreen: true,
                enableToggleForm: false,
                hideSearchBar: false,
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
                header1: {
                    label: "code_program",
                    width: "50",
                    rowspan: 0,
                    colspan: 0,
                },
                header8: {
                    label: "description",
                    width: "400",
                    // flag: "y_n_1",
                    rowspan: 0,
                    colspan: 0,
                },
                header2: {
                    label: "account_group",
                    width: 0,
                    rowspan: 0,
                    colspan: 0,
                },
                header3: {
                    label: "line_no",
                    width: 0,
                    rowspan: 0,
                    colspan: 0,
                },
                header4: {
                    label: "no",
                    width: 0,
                    rowspan: 0,
                    colspan: 0,
                },
                header5: {
                    label: "code_cluster_activity",
                    flag: "y_n_minus_1",
                    rowspan: 0,
                    colspan: 0,
                },
                header6: {
                    label: "code_activity",
                    flag: "y_n",
                    rowspan: 0,
                    colspan: 0,
                },
                header7: {
                    label: "sub_account",
                    rowspan: 0,
                    colspan: 0,
                },
                header9: {
                    label: "is_reg_exp",
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
                    label: "unit_price",
                    rowspan: 0,
                    colspan: 0,
                },
                header14: {
                    label: "unit_price",
                    rowspan: 0,
                    colspan: 0,
                },
                header15: {
                    label: "time_annual",
                    flag: "y_n_1",
                    rowspan: 0,
                    colspan: 0,
                },
                header16: {
                    label: "total_annual_amount",
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
                    label: "misc",
                    rowspan: 0,
                    colspan: 0,
                },
                header20: {
                    label: "Action",
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
                        label: "is_reg_exp",
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
                    field1: "name",
                    field2: "code",
                    field3: "group_chapter",
                    field4: "no",
                    field5: "line",
                    field6: "code_cluster_activity",
                    field7: "code_activity",
                    field8: "sub_account",
                    field9: "is_reg_exp",
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
                summary: [],
                data:
                    [
                        // {
                        //     'id': 1,
                        //     'code': "001",
                        //     'group_chapter': '',
                        //     "name": "test1",
                        //     'cluster_activity_id': [
                        //         {
                        //             "label": "tes",
                        //             "value": 12
                        //         },
                        //     ],
                        //     'field': 'cluster_activity_id',
                        //     "children": []
                        // },
                        // {
                        //     'id': 1,
                        //     'code': "001",
                        //     'group_chapter': '',
                        //     "name": "test2",
                        //     'cluster_activity_id': [
                        //         {
                        //             "label": "tes",
                        //             "value": 12
                        //         },
                        //     ],
                        //     'field': 'cluster_activity_id',
                        //     "children": []
                        // },
                        {
                            id: 307,
                            code: "1050525",
                            group_chapter: "",
                            name: "៥.៥.២៥. ចង្កោមសកម្មភាពទី២៥ : ការគាំទ្រទូទៅលើកិច្ចដំណើរការរបស់មន្ទីរសេដ្ឋកិច្ចនិងហិរញ្ញវត្ថុខេត្តត្បូងឃ្មុំ",
                            name_en: "៥.៥.២៥. ចង្កោមសកម្មភាពទី២៥ : ការគាំទ្រទូទៅលើកិច្ចដំណើរការរបស់មន្ទីរសេដ្ឋកិច្ចនិងហិរញ្ញវត្ថុខេត្តត្បូងឃ្មុំ",
                            name_kh: "៥.៥.២៥. ចង្កោមសកម្មភាពទី២៥ : ការគាំទ្រទូទៅលើកិច្ចដំណើរការរបស់មន្ទីរសេដ្ឋកិច្ចនិងហិរញ្ញវត្ថុខេត្តត្បូងឃ្មុំ",
                            cluster_activity_id: {
                                label: "៥.៥.២៥. ចង្កោមសកម្មភាពទី២៥ : ការគាំទ្រទូទៅលើកិច្ចដំណើរការរបស់មន្ទីរសេដ្ឋកិច្ចនិងហិរញ្ញវត្ថុខេត្តត្បូងឃ្មុំ",
                                value: 307
                            },
                            field: "cluster_activity_id",
                            children: [
                                {
                                    id: 718,
                                    code: "105052501",
                                    cluster_activity_id: 307,
                                    name: "សកម្មភាពទី១-ការគាំទ្រទូទៅលើកិច្ចដំណើរការរបស់មន្ទីរសេដ្ឋកិច្ចនិងហិរញ្ញវត្ថុខេត្តត្បូងឃ្មុំ",
                                    name_en: "សកម្មភាពទី១-ការគាំទ្រទូទៅលើកិច្ចដំណើរការរបស់មន្ទីរសេដ្ឋកិច្ចនិងហិរញ្ញវត្ថុខេត្តត្បូងឃ្មុំ",
                                    name_kh: "សកម្មភាពទី១-ការគាំទ្រទូទៅលើកិច្ចដំណើរការរបស់មន្ទីរសេដ្ឋកិច្ចនិងហិរញ្ញវត្ថុខេត្តត្បូងឃ្មុំ",
                                    entity: "មន្ទីរ សហវ. ខេត្តត្បូងឃ្មុំ",
                                    // name: "សកម្មភាពទី១ នៃចង្កោមសកម្មភាពទី១៖ បំពេញមុខងារជាលេខាធិការដ្ឋាន",
                                    group_chapter: "",
                                    no: "",
                                    line: "",
                                    code_cluster_activity: "",
                                    code_activity: "",
                                    sub_account: "",
                                    is_reg_exp: "",
                                    unit: "",
                                    quantity: "",
                                    currency: "",
                                    unit_price: "",
                                    total_amount: "50,216,000",
                                    time_year: "12",
                                    annual_amount: "602,592,000",
                                    month: "",
                                    expense_type: "",
                                    remark: "",
                                    has_child: true,
                                    children: [
                                        {
                                            id: 1,
                                            code: "C-002",
                                            name: "C-002: ចំណាយលេខាធិការដ្ឋាន",
                                            order_level: null,
                                            // name: "TSK00012: ១.បន្ទុកបុគ្គលិក",
                                            group_chapter: "",
                                            no: "",
                                            line: "",
                                            code_cluster_activity: "",
                                            code_activity: "",
                                            sub_account: "",
                                            is_reg_exp: "",
                                            unit: "",
                                            quantity: "",
                                            currency: "",
                                            unit_price: "",
                                            total_amount: "50,216,000",
                                            time_year: "12",
                                            annual_amount: "602,592,000",
                                            month: "",
                                            expense_type: "",
                                            remark: "",
                                            children: [
                                                {
                                                    name: "ប្រាក់បៀវត្សមូលដ្ឋានមន្ត្រីរាជការ ចំនួន ៥៦នាក់",
                                                    code: "10101",
                                                    group_chapter: "64",
                                                    no: "1",
                                                    line: "LN0001",
                                                    code_cluster_activity: "1010101",
                                                    code_activity: "101010101",
                                                    sub_account: "64021",
                                                    is_reg_exp: "Regular",
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
                    ],
                limit: 10,
                total: 3,
            },
            formAttributes: [
                // {
                //     name: "entity_id",
                //     type: "select",
                //     required: true,
                //     hasFilter: true,
                //     filterObj: "entity_member_id",
                //     api: apiConfig._apiMemberByEntity,
                // },
                // {
                //     name: "program_id",
                //     type: "select",
                //     required: true,
                //     hasFilter: true,
                //     filterObj: "sub_program_id",
                //     api: apiConfig._apiMemberByEntity,
                // },
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
                    api: apiConfig._apiClusActBySprog
                },
                {
                    name: "cluster_activity_id",
                    type: "select",
                    required: true,
                    hasFilter: true,
                    filterObj: "activity_id",
                    api: apiConfig._apiGetActivity
                },
                {
                    name: "activity_id",
                    type: "select",
                    required: true,
                    hasFilter: false,
                    // filterObj: "",
                },
                // {
                //     name: "task_id",
                //     type: "select",
                //     required: true,
                //     hasFilter: false,
                //     // filterObj: "",
                // },
                // {
                //     name: "group_chapter",
                //     type: "select",
                //     required: true,
                //     data: [],
                // },
                // {
                //     name: "account_id",
                //     type: "select",
                //     required: true,
                //     data: [],
                // },
                // {
                //     name: "sub_account_id",
                //     type: "select",
                //     required: true,
                //     data: [],
                // },
                {
                    name: "code",
                    type: "text",
                    required: true,
                    data: [],
                },
                {
                    name: "name_en",
                    type: "text",
                    required: true,
                    data: [],
                },
                {
                    name: "name_kh",
                    type: "text",
                    required: true,
                    data: [],
                },
                {
                    name: "start_date",
                    type: "date",
                    required: true,
                    data: [],
                },
                {
                    name: "end_date",
                    type: "date",
                    required: true,
                    data: [],
                },
                {
                    name: "remark",
                    type: "textarea",
                    required: false,
                }
            ],
            dataTables: [],
            rowDisplay: "3grid", //1grid, 2grid, 3grid, 4grid
            dataFields: [],
            dataInfo: {},
            showForm: false
        }
    },
    components: {
        FormTask,
        FormActivity,
        DTableList
    },
    methods: {
        initDownload() {
            const _base_url = window.location.origin;
            window.location = _base_url + '/download/PB/2023/Costings.xlsx';
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
                        // this.data = this.data;
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
        initOpenForm(data) {
            this.$refs.refOpenPrivatePopupForm.showForm(data);
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
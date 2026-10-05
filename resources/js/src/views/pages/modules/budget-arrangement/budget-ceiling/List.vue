<template>
    <div id="table-demo">
        <!-- Here to create popup form for ceiling data by entity -->
        <Form ref="refOpenPrivatePopupForm" :data="data"></Form>
        <d-table-list @clickPricateForm="initPopupPrivateForm" @clicked="initTableData" :api="api" ref="refInitPage"
            :allowDel="true" :title="title" :dataAttributes="dataAttributes" :dataHeaders="dataHeaders" :dataTables="data"
            :formAttributes="formAttributes" :rowDisplay="rowDisplay"></d-table-list>
    </div>
</template>

<script>
import axios from "@/axios.js"
import apiConfig from "@/apiConfig.js"
import { ref } from 'vue';
import Form from '@/views/pages/modules/budget-arrangement/budget-ceiling/_Form.vue';
import TableState from './TableState.vue'
import DTableList from '@/views/form-builder/DTableList.vue'


export default {
    data() {
        return {
            title: "CeilingBudget",
            api: apiConfig._apiCeilingEntity,
            dataAttributes: {
                tableStyle: 3,
                page_number: 1,
                offset: 0,
                dataGrid: "row",
                popupFullscreen: true,
                enableToggleForm: true,
                hasPrivateButton: true,
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
                    width: 350,
                    label: "បរិយាយមុខសញ្ញា",
                    rowspan: 3,
                    colspan: 0,
                },
                header2: {
                    label: "ច្បាប់ឆ្នាំ២០២២",
                    rowspan: 2,
                    colspan: 0,
                },
                header3: {
                    label: "ចំណាយមិនប្រចាំ",
                    rowspan: 2,
                    colspan: 0,
                },
                header4: {
                    label: "ចំណាយប្រចាំ",
                    rowspan: 2,
                    colspan: 0,
                },
                header5: {
                    label: "ពិដានចំណាយចរន្ត",
                    rowspan: 0,
                    colspan: 3,
                },
                header6: {
                    label: "អាត្រាកំណើនប្រចាំឆ្នាំ",
                    rowspan: 0,
                    colspan: 3,
                },
                header7: {
                    label: "សម្គាល់",
                    rowspan: 2,
                    colspan: 0,
                },
                header8: {
                    label: "ប៊ូតុងសកម្ម",
                    rowspan: 3,
                    colspan: 0,
                }
            },
            data: {
                dataHeaders: {
                    header1: {
                        label: "២០២៣",
                        rowspan: 0,
                        colspan: 0,
                    },
                    header2: {
                        label: "២០២៤",
                        rowspan: 0,
                        colspan: 0,
                    },
                    header3: {
                        label: "២០២៥",
                        rowspan: 0,
                        colspan: 0,
                    },
                    header4: {
                        label: "២០២៣",
                        rowspan: 0,
                        colspan: 0,
                    },
                    header5: {
                        label: "២០២៤",
                        rowspan: 0,
                        colspan: 0,
                    },
                    header6: {
                        label: "២០២៥",
                        rowspan: 0,
                        colspan: 0,
                    },
                    // header7: {
                    //     label: "",
                    //     rowspan: 0,
                    //     colspan: 0,
                    // }
                },
                dataSubHeaders: {
                    header1: {
                        label: "1",
                        rowspan: 0,
                        colspan: 0,
                    },
                    header2: {
                        label: "2",
                        rowspan: 0,
                        colspan: 0,
                    },
                    header3: {
                        label: "3",
                        rowspan: 0,
                        colspan: 0,
                    },
                    header4: {
                        label: "4",
                        rowspan: 0,
                        colspan: 0,
                    },
                    header5: {
                        label: "5",
                        rowspan: 0,
                        colspan: 0,
                    },
                    header6: {
                        label: "6",
                        rowspan: 0,
                        colspan: 0,
                    },
                    header7: {
                        label: "7=(4-1)/1",
                        rowspan: 0,
                        colspan: 0,
                    },
                    header8: {
                        label: "8=(5-4)/4",
                        rowspan: 0,
                        colspan: 0,
                    },
                    header9: {
                        label: "9=(6-5)/5",
                        rowspan: 0,
                        colspan: 0,
                    },
                    header10: {
                        label: "10",
                        rowspan: 0,
                        colspan: 0,
                    },
                    // header11: {
                    //     label: "",
                    //     rowspan: 0,
                    //     colspan: 0,
                    // },
                },
                data: [
                    {
                        id: 1,
                        name: "ផ្នែកទី១៖ ពិដានចំណាយចរន្ត ឆ្នាំ២០២៣-២០២៥ សរុបរួមក្រសួង (យោងលេខ ០៣ សរណន ចុះថ្ងៃទី៨ ខែមិថុនា ឆ្នាំ២០២២",
                        name_kh: "ផ្នែកទី១៖ ពិដានចំណាយចរន្ត ឆ្នាំ២០២៣-២០២៥ សរុបរួមក្រសួង (យោងលេខ ០៣ សរណន ចុះថ្ងៃទី៨ ខែមិថុនា ឆ្នាំ២០២២",
                        remark: "",
                        status: 1,
                        hasColspan: true,
                        colspan: 12,
                        order_level: "",
                        data: {
                            summary: {
                                id: 1,
                                name: "សរុបរួមក្រសួង",
                                values: [1.00, 2.00, 3.00, 4.00, 5.00, 6.00, 7.00, 8.00, 9.00, ""]
                            },
                            children: [
                                {
                                    id: 1,
                                    name: "I.រដ្ឋបាលកណ្តាល",
                                    values: [0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, ""],
                                    data: [
                                        {
                                            id: 1,
                                            name: "- បន្ទុកបុគ្គលិក",
                                            values: [0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, ""]
                                        },
                                        {
                                            id: 2,
                                            name: "- ក្រៅបន្ទុកបុគ្គលិក",
                                            values: [0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, ""]
                                        }
                                    ],
                                    dataDetails: [
                                        {
                                            id: 1,
                                            name: "១. អនុកម្មវិធីទី១.១ អគ្គនាយកដ្ឋាន​គោលនយោបាយ",
                                            entity: {},
                                            values: [0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, ""],
                                            data: [
                                                {
                                                    id: 1,
                                                    name: "- បន្ទុកបុគ្គលិក",
                                                    values: [0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, ""]
                                                },
                                                {
                                                    id: 1,
                                                    name: "- ក្រៅបន្ទុកបុគ្គលិក",
                                                    values: [0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, ""]
                                                }
                                            ],
                                            ceiling_entities: [
                                                {
                                                    id: 1,
                                                    name: "- បន្ទុកបុគ្គលិក",
                                                    values: [0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, ""]
                                                },
                                                {
                                                    id: 1,
                                                    name: "- ក្រៅបន្ទុកបុគ្គលិក",
                                                    values: [0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, ""]
                                                }
                                            ]
                                        },
                                        {
                                            id: 2,
                                            name: "២. អនុកម្មវិធីទី១.២ អគ្គលេខាធិការដ្ឋាន​គណៈកម្មាធិការជាតិ​ផលិតភាពសេដ្ឋកិច្ច​កម្ពុជា",
                                            entity: {},
                                            values: [0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, ""],
                                            data: [
                                                {
                                                    id: 1,
                                                    name: "- បន្ទុកបុគ្គលិក",
                                                    values: [0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, ""]
                                                },
                                                {
                                                    id: 1,
                                                    name: "- ក្រៅបន្ទុកបុគ្គលិក",
                                                    values: [0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, ""]
                                                }
                                            ]
                                        },
                                    ]
                                },
                                {
                                    id: 2,
                                    name: "II.មន្ទីរសេដ្ឋកិច្ច​និងហិរញ្ញវត្ថុ​ រាជធានី ខេត្ត",
                                    values: [0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, ""],
                                    data: [
                                        {
                                            id: 1,
                                            name: "- បន្ទុកបុគ្គលិក",
                                            values: [0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, ""]
                                        },
                                        {
                                            id: 1,
                                            name: "- ក្រៅបន្ទុកបុគ្គលិក",
                                            values: [0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, ""]
                                        }
                                    ],
                                    dataDetails: [
                                        {
                                            id: 1,
                                            name: "១. មន្ទីរសេដ្ឋកិច្ចនិងហិរញ្ញវត្ថុខេត្តបាត់ដំបង",
                                            entity: {},
                                            values: [0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, ""],
                                            data: [
                                                {
                                                    id: 1,
                                                    name: "- បន្ទុកបុគ្គលិក",
                                                    values: [0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, ""]
                                                },
                                                {
                                                    id: 1,
                                                    name: "- ក្រៅបន្ទុកបុគ្គលិក",
                                                    values: [0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, ""]
                                                }
                                            ]
                                        },
                                        {
                                            id: 2,
                                            name: "២. មន្ទីរសេដ្ឋកិច្ចនិងហិរញ្ញវត្ថុខេត្តបន្ទាយមានជ័យ",
                                            entity: {},
                                            values: [0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, ""],
                                            data: [
                                                {
                                                    id: 1,
                                                    name: "- បន្ទុកបុគ្គលិក",
                                                    values: [0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, ""]
                                                },
                                                {
                                                    id: 1,
                                                    name: "- ក្រៅបន្ទុកបុគ្គលិក",
                                                    values: [0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, ""]
                                                }
                                            ]
                                        },
                                    ]
                                },
                            ]
                        }
                    }
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
                target_type_id: [
                    {
                        "label": "បន្ទុកបុគ្គលិក",
                        "value": 1,
                    },
                    {
                        "label": "ក្រៅបន្ទុកបុគ្គលិក",
                        "value": 2,
                    }
                ],
                rule_year: [
                    {
                        "label": "2022",
                        "value": 1,
                    }
                ],
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
                    options: [],
                },
                {
                    name: "sub_program_id",
                    type: "select",
                    required: true,
                    hasFilter: true,
                    filterObj: "entity_id",
                    api: apiConfig._apiEntityBySubPro,
                    options: [],
                },
                {
                    name: "entity_id", // អង្គភាព
                    type: "select",
                    required: true,
                    options: [],
                },
                {
                    name: "tab_budget_ceiling",
                    type: "tab_budget_ceiling_group",
                    required: false,
                },
                // {
                //     name: "ceiling_exp_group", // ប្រភេទមុខសញ្ញា
                //     type: "select",
                //     required: true,
                //     options: [],
                // },
                // {
                //     name: "ceiling_rule", //ច្បាប់ពិដាន
                //     type: "text",
                //     required: true,
                //     options: [],
                // },
                // {
                //     name: "irregular_expense", // ចំណាយមិនប្រចាំ
                //     type: "text",
                //     required: false,
                // },
                // {
                //     name: "regular_expense", // ចំណាយប្រចាំ
                //     type: "text",
                //     required: false,
                // },

                // {
                //     name: "ceiling_tran_year_0", // ពិដានចំណាយចរន្ត
                //     type: "text",
                //     required: false
                // },
                // {
                //     name: "ceiling_tran_year_1", // ពិដានចំណាយចរន្ត
                //     type: "text",
                //     required: false
                // },
                // {
                //     name: "ceiling_tran_year_2", // ពិដានចំណាយចរន្ត
                //     type: "text",
                //     required: false
                // },
                // {
                //     name: "increase_rate_year_0", // អត្រាកំណើនប្រចាំឆ្នាំ
                //     type: "text",
                //     required: false
                // },
                // {
                //     name: "increase_rate_year_1", // អត្រាកំណើនប្រចាំឆ្នាំ
                //     type: "text",
                //     required: false
                // },
                // {
                //     name: "increase_rate_year_2", // អត្រាកំណើនប្រចាំឆ្នាំ
                //     type: "text",
                //     required: false
                // },

            ],
            rowDisplay: "3grid", //1grid, 2grid, 3grid, 4grid
            dataFields: [],
            dataInfo: {}
        }
    },
    components: {
        DTableList,
        TableState,
        Form
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
        initPopupPrivateForm(data) {
            this.$refs.refOpenPrivatePopupForm.showForm(data);
            // alert("welcome back");
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

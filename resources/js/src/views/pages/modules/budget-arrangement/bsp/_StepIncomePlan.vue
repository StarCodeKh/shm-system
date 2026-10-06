<template>
    <div class="flex">
        <d-table-list @clicked="initTableData" :api="api" ref="refInitPage" :allowDel="true" :title="title"
            :dataInfo="dataInfo" :dataAttributes="dataAttributes" :dataHeaders="dataHeaders" :dataTables="data"
            :formAttributes="formAttributes" :rowDisplay="rowDisplay" @emitDataForm="initDataForm"
            @initDownload="initDownload"></d-table-list>
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
            title: "bsp_heading_revenue_plan",
            api: apiConfig._apiObjective,
            dataAttributes: {
                enableDownload: true,
                tableStyle: 3,
                page_number: 1,
                offset: 0,
                dataGrid: "row",
                popupFullscreen: true,
                hasHeadingReport: true,
                headingReport: "bsp_heading_revenue_plan",
                actionButton: [
                    {
                        icon: "DollarSignIcon",
                        path: "/module/budget-arrangement/budget-ceiling/list",
                        method: "View"
                    }
                ]
            },
            dataHeaders: {
                header1: {
                    width: 350,
                    label: "ការគ្រោងចំណូល តាមប្រភេទចំណូល",
                    rowspan: 2,
                    colspan: 0,
                },
                header2: {
                    label: "ច្បាប់ហិរញ្ញវត្ថុឆ្នាំ{P-1}",
                    rowspan: 0,
                    colspan: 2,
                },
                header3: {
                    label: "គ្រោងឆ្នាំ{P}",
                    rowspan: 0,
                    colspan: 2,
                },
                header4: {
                    label: "គ្រោងឆ្នាំ{P+1}",
                    rowspan: 0,
                    colspan: 2,
                },
                header5: {
                    label: "គ្រោងឆ្នាំ{P+2}",
                    rowspan: 0,
                    colspan: 2,
                },
                header6: {
                    label: "Action",
                    rowspan: 2,
                    colspan: 0,
                },
            },
            data: {
                dataHeaders: {
                    header1: {
                        label: "ថវិការដ្ឋ",
                        rowspan: 0,
                        colspan: 0,
                    },
                    header2: {
                        label: "ថវិកាដៃគូអភិវឌ្ឍន៍",
                        rowspan: 0,
                        colspan: 0,
                    },
                    header3: {
                        label: "ថវិការដ្ឋ",
                        rowspan: 0,
                        colspan: 0,
                    },
                    header4: {
                        label: "ថវិកាដៃគូអភិវឌ្ឍន៍",
                        rowspan: 0,
                        colspan: 0,
                    },
                    header5: {
                        label: "ថវិការដ្ឋ",
                        rowspan: 0,
                        colspan: 0,
                    },
                    header6: {
                        label: "ថវិកាដៃគូអភិវឌ្ឍន៍",
                        rowspan: 0,
                        colspan: 0,
                    },
                    header7: {
                        label: "ថវិការដ្ឋ",
                        rowspan: 0,
                        colspan: 0,
                    },
                    header8: {
                        label: "ថវិកាដៃគូអភិវឌ្ឍន៍",
                        rowspan: 0,
                        colspan: 0,
                    },
                },
                dataSubHeaders: {
                },
                data: [
                    {
                        id: 1,
                        name: "ច្បាប់ហិរញ្ញវត្ថុ",
                        name_kh: "ច្បាប់ហិរញ្ញវត្ថុ",
                        remark: "",
                        status: 1,
                        hasColspan: true,
                        colspan: 10,
                        order_level: "",
                        data: {
                            summary: {
                                id: 1,
                                name: "សរុបរួមក្រសួង",
                                values: [1.00, 2.00, 3.00, 4.00, 5.00, 6.00, 7.00, 8.00]
                            },
                            children: [
                                {
                                    id: 1,
                                    name: "១.ចំណូលពីប្រភពក្នុងស្រុក",
                                    values: [1.00, 2.00, 3.00, 4.00, 5.00, 6.00, 7.00, 8.00],
                                    data: [
                                        {
                                            id: 1,
                                            name: "- ចំណូលសារពើពន្ធ",
                                            values: [1.00, 2.00, 3.00, 4.00, 5.00, 6.00, 7.00, 8.00]
                                        },
                                        {
                                            id: 1,
                                            name: "- ចំណូលមិនមែនសារពើពន្ធ",
                                            values: [1.00, 2.00, 3.00, 4.00, 5.00, 6.00, 7.00, 8.00]
                                        },
                                        {
                                            id: 1,
                                            name: "- ចំណូលមូលធនក្នុងស្រុក (កម្ចីបន្ត)",
                                            values: [1.00, 2.00, 3.00, 4.00, 5.00, 6.00, 7.00, 8.00]
                                        }
                                    ],
                                    // dataDetails: [
                                    //     {
                                    //         id: 1,
                                    //         name: "១. អនុកម្មវិធីទី១.១ អគ្គនាយកដ្ឋាន​គោលនយោបាយ",
                                    //         entity: {},
                                    //         values: [1.00, 2.00, 3.00, 4.00, 5.00, 6.00, 7.00, 8.00],
                                    //         data: [
                                    //             {
                                    //                 id: 1,
                                    //                 name: "- បន្ទុកបុគ្គលិក",
                                    //                 values: [1.00, 2.00, 3.00, 4.00, 5.00, 6.00, 7.00, 8.00]
                                    //             },
                                    //             {
                                    //                 id: 1,
                                    //                 name: "- បន្ទុកបុគ្គលិក",
                                    //                 values: [1.00, 2.00, 3.00, 4.00, 5.00, 6.00, 7.00, 8.00]
                                    //             }
                                    //         ],
                                    //         ceiling_entities: [
                                    //             {
                                    //                 id: 1,
                                    //                 name: "- បន្ទុកបុគ្គលិក",
                                    //                 values: [1.00, 2.00, 3.00, 4.00, 5.00, 6.00, 7.00, 8.00]
                                    //             },
                                    //             {
                                    //                 id: 1,
                                    //                 name: "- បន្ទុកបុគ្គលិក",
                                    //                 values: [1.00, 2.00, 3.00, 4.00, 5.00, 6.00, 7.00, 8.00]
                                    //             }
                                    //         ]
                                    //     },
                                    //     {
                                    //         id: 2,
                                    //         name: "២. អនុកម្មវិធីទី១.២ អគ្គលេខាធិការដ្ឋាន​គណៈកម្មាធិការជាតិ​ផលិតភាពសេដ្ឋកិច្ច​កម្ពុជា",
                                    //         entity: {},
                                    //         values: [1.00, 2.00, 3.00, 4.00, 5.00, 6.00, 7.00, 8.00],
                                    //         data: [
                                    //             {
                                    //                 id: 1,
                                    //                 name: "- បន្ទុកបុគ្គលិក",
                                    //                 values: [1.00, 2.00, 3.00, 4.00, 5.00, 6.00, 7.00, 8.00]
                                    //             },
                                    //             {
                                    //                 id: 1,
                                    //                 name: "- បន្ទុកបុគ្គលិក",
                                    //                 values: [1.00, 2.00, 3.00, 4.00, 5.00, 6.00, 7.00, 8.00]
                                    //             }
                                    //         ]
                                    //     },
                                    // ]
                                },
                                {
                                    id: 2,
                                    name: "២.ចំណូលពីការអនុវត្តគម្រោង",
                                    values: [1.00, 2.00, 3.00, 4.00, 5.00, 6.00, 7.00, 8.00],
                                    data: [
                                        {
                                            id: 1,
                                            name: "- គម្រោងវិនិយោគ (មានហិរញ្ញប្បទាន)",
                                            values: [1.00, 2.00, 3.00, 4.00, 5.00, 6.00, 7.00, 8.00]
                                        },
                                        {
                                            id: 1,
                                            name: "- ជំនួយបច្ចេកទេស (មានហិរញ្ញប្បទាន)",
                                            values: [1.00, 2.00, 3.00, 4.00, 5.00, 6.00, 7.00, 8.00]
                                        }
                                    ],
                                    // dataDetails: [
                                    //     {
                                    //         id: 1,
                                    //         name: "១. មន្ទីរសេដ្ឋកិច្ចនិងហិរញ្ញវត្ថុខេត្តបាត់ដំបង",
                                    //         entity: {},
                                    //         values: [1.00, 2.00, 3.00, 4.00, 5.00, 6.00, 7.00, 8.00],
                                    //         data: [
                                    //             {
                                    //                 id: 1,
                                    //                 name: "- បន្ទុកបុគ្គលិក",
                                    //                 values: [1.00, 2.00, 3.00, 4.00, 5.00, 6.00, 7.00, 8.00]
                                    //             },
                                    //             {
                                    //                 id: 1,
                                    //                 name: "- បន្ទុកបុគ្គលិក",
                                    //                 values: [1.00, 2.00, 3.00, 4.00, 5.00, 6.00, 7.00, 8.00]
                                    //             }
                                    //         ]
                                    //     },
                                    //     {
                                    //         id: 2,
                                    //         name: "២. មន្ទីរសេដ្ឋកិច្ចនិងហិរញ្ញវត្ថុខេត្តបន្ទាយមានជ័យ",
                                    //         entity: {},
                                    //         values: [1.00, 2.00, 3.00, 4.00, 5.00, 6.00, 7.00, 8.00],
                                    //         data: [
                                    //             {
                                    //                 id: 1,
                                    //                 name: "- បន្ទុកបុគ្គលិក",
                                    //                 values: [1.00, 2.00, 3.00, 4.00, 5.00, 6.00, 7.00, 8.00]
                                    //             },
                                    //             {
                                    //                 id: 1,
                                    //                 name: "- បន្ទុកបុគ្គលិក",
                                    //                 values: [1.00, 2.00, 3.00, 4.00, 5.00, 6.00, 7.00, 8.00]
                                    //             }
                                    //         ]
                                    //     },
                                    // ]
                                },
                            ]
                        }
                    }
                ],
                bsp_source_income_group: [
                    {
                        "label": "ចំណូលពីប្រភពក្នុងស្រុក ",
                        "value": 1,
                    },
                    {
                        "label": "ចំណូលពីការអនុវត្តគម្រោង",
                        "value": 2,
                    }
                ],
                bsp_source_income: [

                    {
                        "label": "ចំណូលសារពើពន្ធ",
                        "value": 1,
                    },
                    {
                        "label": "ចំណូលមិនមែនសារពើពន្ធ",
                        "value": 2,
                    }, {
                        "label": "ចំណូលមូលធនក្នុងស្រុក",
                        "value": 3,
                    },

                ],
                bsp_source_income_level: [
                    {
                        "label": "មានហិរញ្ញ ",
                        "value": 1,
                    },
                    {
                        "label": "ចំណូលពីការអនុវត្តគម្រោង",
                        "value": 2,
                    }
                ],
                limit: 10,
                total: 3,
            },
            formAttributes: [
                {
                    name: "bsp_source_income_group",
                    type: "select",
                    required: true,
                    options: [],
                },
                {
                    name: "bsp_source_income",
                    type: "select",
                    required: true,
                    options: [],
                },
                {
                    name: "bsp_source_income_level",
                    type: "select",
                    required: true,
                    options: [],
                }
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
            window.location = _base_url + '/download/BSP/2023/BSP.xlsx';
        },
        initRequest() {
            this.$vs.loading();
            this.getData();
        },
        initDataForm() {
            // alert("testing");
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
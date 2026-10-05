<template>
    <!-- ###################### -->
    <div class="flex">
        <d-table-list @clicked="initTableData" :api="api" ref="refInitPage" :allowDel="true" :title="title"
            :dataInfo="dataInfo" :dataAttributes="dataAttributes" :dataHeaders="dataHeaders" :dataTables="data"
            :formAttributes="formAttributes" :rowDisplay="rowDisplay" @emitDataForm="initDataForm"
            @initDownload="initDownload"></d-table-list>

        <!-- <vs-table :data="[]">

            <template slot="thead">
            <vs-th rowspan="3" class="rotate">ជំពូក</vs-th>
            <vs-th class="rotate">គណនីយ</vs-th>
            <vs-th class="rotate">អនុគណនី</vs-th>
            <vs-th class="th-width-300">ចំណាត់ថ្នាក់ចំណាយ</vs-th>
            <vs-th class="th-width-300">ចំណូលឆ្នាំ២០២១(អនុវត្ត)</vs-th>
            <vs-th class="th-width-300 text-center" colspan="3"><center>ច្បាប់ហិរញ្ញវត្ថុឆ្នាំ២០២២</center></vs-th>
            <vs-th class="th-width-300" colspan="4">ប៉ាន់ស្មានអនុវត្តឆ្នាំ២០២២</vs-th>
            <vs-th class="th-width-300" colspan="4">គម្រោងឆ្នាំ២០២៣</vs-th>
            </template>

            <template>
                
                <vs-tr :state="'success'">
                    <vs-td></vs-td>
                    <vs-td></vs-td>
                    <vs-td></vs-td>
                    <vs-td></vs-td>
                    <vs-td></vs-td>

                    <vs-td></vs-td>
                    <vs-td></vs-td>
                    <vs-td></vs-td>

                    <vs-td></vs-td>
                    <vs-td></vs-td>
                    <vs-td colspan="2">ប៉ាន់​ស្មាន​ប្រចាំ​ឆ្នាំ</vs-td>
                    <vs-td></vs-td>
                    <vs-td></vs-td>
                    <vs-td colspan="2">សរុប​ការ​គ្រោ​ងចំណាយ</vs-td>
                </vs-tr>

                <vs-tr :state="'success'">
                    <vs-td></vs-td>
                    <vs-td></vs-td>
                    <vs-td></vs-td>
                    <vs-td>សរុប​រួមចំណាយ​ (ក+ខ)</vs-td>
                    
                    <vs-td></vs-td>
                    <vs-td class="td-width-150">មូលដ្ឋានគិតចំណាយ</vs-td>
                    <vs-td class="td-width-150">បរិមាណ</vs-td>
                    <vs-td class="td-width-150">សរុបឥណទានថវិកា</vs-td>

                    <vs-td class="td-width-150">អនុវត្តឆមាសទី១</vs-td>
                    <vs-td class="td-width-150">ប៉ាន់ស្មានឆមាសទី២</vs-td>
                    <vs-td class="td-width-150">សរុប</vs-td>
                    <vs-td class="td-width-150">%ច្ប.២២</vs-td>

                    <vs-td class="td-width-150">មូលដ្ឋានគិតចំណាយ</vs-td>
                    <vs-td class="td-width-150">បរិមាណ</vs-td>
                    <vs-td class="td-width-150">សរុប</vs-td>
                    <vs-td class="td-width-150">%ច្ប.២២</vs-td>
                </vs-tr>

                <vs-tr :state="'warning'">
                    <vs-td></vs-td>
                    <vs-td></vs-td>
                    <vs-td></vs-td>
                    <vs-td>ក្នុងនោះចំណាយ​ក្នុងប្រទេស</vs-td>

                    <vs-td>0.0</vs-td>
                    <vs-td>0.0</vs-td>
                    <vs-td>0.0</vs-td>
                    <vs-td>0.0</vs-td>

                    <vs-td>0.0</vs-td>
                    <vs-td>0.0</vs-td>
                    <vs-td>0.0</vs-td>
                    <vs-td>0.0</vs-td>

                    <vs-td>0.0</vs-td>
                    <vs-td>0.0</vs-td>
                    <vs-td>0.0</vs-td>
                    <vs-td>0.0</vs-td>
                </vs-tr>

                <vs-tr :state="'warning'">
                    <vs-td></vs-td>
                    <vs-td></vs-td>
                    <vs-td></vs-td>
                    <vs-td>ក. សរុបចំណាយ​ចរន្ត (ក្រុមទី១+ក្រុមទី២)</vs-td>

                    <vs-td>0.0</vs-td>
                    <vs-td>0.0</vs-td>
                    <vs-td>0.0</vs-td>
                    <vs-td>0.0</vs-td>

                    <vs-td>0.0</vs-td>
                    <vs-td>0.0</vs-td>
                    <vs-td>0.0</vs-td>
                    <vs-td>0.0</vs-td>

                    <vs-td>0.0</vs-td>
                    <vs-td>0.0</vs-td>
                    <vs-td>0.0</vs-td>
                    <vs-td>0.0</vs-td>
                </vs-tr>

                <vs-tr :state="'warning'">
                    <vs-td></vs-td>
                    <vs-td></vs-td>
                    <vs-td></vs-td>
                    <vs-td>ក. សរុបចំណាយ​ចរន្ត (ក្រុមទី១+ក្រុមទី២)</vs-td>

                    <vs-td>0.0</vs-td>
                    <vs-td>0.0</vs-td>
                    <vs-td>0.0</vs-td>
                    <vs-td>0.0</vs-td>

                    <vs-td>0.0</vs-td>
                    <vs-td>0.0</vs-td>
                    <vs-td>0.0</vs-td>
                    <vs-td>0.0</vs-td>

                    <vs-td>0.0</vs-td>
                    <vs-td>0.0</vs-td>
                    <vs-td>0.0</vs-td>
                    <vs-td>0.0</vs-td>
                </vs-tr>

                <vs-tr :state="'warning'">
                    <vs-td></vs-td>
                    <vs-td></vs-td>
                    <vs-td></vs-td>
                    <vs-td>ក. សរុបចំណាយ​ចរន្ត (ក្រុមទី១+ក្រុមទី២)</vs-td>

                    <vs-td>0.0</vs-td>
                    <vs-td>0.0</vs-td>
                    <vs-td>0.0</vs-td>
                    <vs-td>0.0</vs-td>

                    <vs-td>0.0</vs-td>
                    <vs-td>0.0</vs-td>
                    <vs-td>0.0</vs-td>
                    <vs-td>0.0</vs-td>

                    <vs-td>0.0</vs-td>
                    <vs-td>0.0</vs-td>
                    <vs-td>0.0</vs-td>
                    <vs-td>0.0</vs-td>
                </vs-tr>

                <vs-tr :state="'warning'">
                    <vs-td></vs-td>
                    <vs-td></vs-td>
                    <vs-td></vs-td>
                    <vs-td>ប្រភេទទី១: ចំណាយ​សារពើពន្ធ</vs-td>

                    <vs-td>0.0</vs-td>
                    <vs-td>0.0</vs-td>
                    <vs-td>0.0</vs-td>

                    <vs-td>0.0</vs-td>
                    <vs-td>0.0</vs-td>
                    <vs-td>0.0</vs-td>
                    <vs-td>0.0</vs-td>

                    <vs-td>0.0</vs-td>
                    <vs-td>0.0</vs-td>
                    <vs-td>0.0</vs-td>
                    <vs-td>0.0</vs-td>
                    <vs-td>0.0</vs-td>
                </vs-tr>
                
                <vs-tr>
                    <vs-td>70</vs-td>
                    <vs-td></vs-td>
                    <vs-td></vs-td>
                    <vs-td>ចំណាយសារពើពន្ធក្នុងស្រុក</vs-td>

                    <vs-td>0.0</vs-td>
                    <vs-td>0.0</vs-td>
                    <vs-td>0.0</vs-td>
                    <vs-td>0.0</vs-td>

                    <vs-td>0.0</vs-td>
                    <vs-td>0.0</vs-td>
                    <vs-td>0.0</vs-td>
                    <vs-td>0.0</vs-td>

                    <vs-td>0.0</vs-td>
                    <vs-td>0.0</vs-td>
                    <vs-td>0.0</vs-td>
                    <vs-td>0.0</vs-td>
                </vs-tr>

                
                <vs-tr>
                    <vs-td></vs-td>
                    <vs-td>7001</vs-td>
                    <vs-td></vs-td>
                    <vs-td>ពន្ធផ្ទាល់ (ពន្ធលើផលទុន ប្រាក់ចំណាយ)</vs-td>
                    <vs-td></vs-td>

                    <vs-td>0.0</vs-td>
                    <vs-td>0.0</vs-td>
                    <vs-td>0.0</vs-td>

                    <vs-td>0.0</vs-td>
                    <vs-td>0.0</vs-td>
                    <vs-td>0.0</vs-td>
                    <vs-td>0.0</vs-td>

                    <vs-td>0.0</vs-td>
                    <vs-td>0.0</vs-td>
                    <vs-td>0.0</vs-td>
                    <vs-td>0.0</vs-td>
                </vs-tr>

                <vs-tr>
                    <vs-td></vs-td>
                    <vs-td></vs-td>
                    <vs-td>70011</vs-td>
                    <vs-td>ពន្ធលើប្រាក់ចំណាយ (រូបវន្តបុគ្គល)</vs-td>
                    <vs-td></vs-td>

                    <vs-td>0.0</vs-td>
                    <vs-td>0.0</vs-td>
                    <vs-td>0.0</vs-td>

                    <vs-td>0.0</vs-td>
                    <vs-td>0.0</vs-td>
                    <vs-td>0.0</vs-td>
                    <vs-td>0.0</vs-td>

                    <vs-td>0.0</vs-td>
                    <vs-td>0.0</vs-td>
                    <vs-td>0.0</vs-td>
                    <vs-td>0.0</vs-td>
                </vs-tr>

                <vs-tr>
                    <vs-td></vs-td>
                    <vs-td>7002</vs-td>
                    <vs-td></vs-td>
                    <vs-td>ពន្ធផ្ទាល់ (ពន្ធលើផលទុន ប្រាក់ចំណាយ)</vs-td>
                    <vs-td></vs-td>

                    <vs-td>0.0</vs-td>
                    <vs-td>0.0</vs-td>
                    <vs-td>0.0</vs-td>

                    <vs-td>0.0</vs-td>
                    <vs-td>0.0</vs-td>
                    <vs-td>0.0</vs-td>
                    <vs-td>0.0</vs-td>

                    <vs-td>0.0</vs-td>
                    <vs-td>0.0</vs-td>
                    <vs-td>0.0</vs-td>
                    <vs-td>0.0</vs-td>
                </vs-tr>

                <vs-tr>
                    <vs-td></vs-td>
                    <vs-td></vs-td>
                    <vs-td>70012</vs-td>
                    <vs-td>ពន្ធលើប្រាក់ចំណាយ (រូបវន្តបុគ្គល)</vs-td>
                    <vs-td></vs-td>

                    <vs-td>0.0</vs-td>
                    <vs-td>0.0</vs-td>
                    <vs-td>0.0</vs-td>

                    <vs-td>0.0</vs-td>
                    <vs-td>0.0</vs-td>
                    <vs-td>0.0</vs-td>
                    <vs-td>0.0</vs-td>

                    <vs-td>0.0</vs-td>
                    <vs-td>0.0</vs-td>
                    <vs-td>0.0</vs-td>
                    <vs-td>0.0</vs-td>
                </vs-tr>

            </template>
        </vs-table> -->
    </div>
    <!-- ###################### -->
</template>

<script>
import axios from "@/axios.js"
import apiConfig from "@/apiConfig.js"
import { ref } from 'vue';

import DTableList from '@/views/form-builder/DTableList.vue'

export default {
    data() {
        return {
            title: "bsp_heading_expend_demand_subpro",
            api: apiConfig._apiObjective,
            dataAttributes: {
                enableDownload: true,
                tableStyle: 3,
                page_number: 1,
                offset: 0,
                dataGrid: "row",
                hasHeadingReport: true,
                headingReport: "bsp_heading_expend_demand_subpro",
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
                    label: "តម្រូវការឥណទាន តាមប្រភេទចំណាយ",
                    rowspan: 2,
                    colspan: 0,
                },
                header2: {
                    label: "ច្បាប់ហិរញ្ញវត្ថុឆ្នាំ២០២២",
                    rowspan: 0,
                    colspan: 2,
                },
                header3: {
                    label: "គ្រោងឆ្នាំ២០២៣",
                    rowspan: 0,
                    colspan: 2,
                },
                header4: {
                    label: "គ្រោងឆ្នាំ២០២៤",
                    rowspan: 0,
                    colspan: 2,
                },
                header5: {
                    label: "គ្រោងឆ្នាំ២០២៥",
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
                        name: "១.១. អនុកម្មវិធីទី១.១ : ការគ្រប់គ្រងគោលនយោបាយសេដ្ឋកិច្ច ហិរញ្ញវត្ថុ និងគោលនយោបាយតាមវិស័យផ្សេងៗ",
                        name_kh: "១.១. អនុកម្មវិធីទី១.១ : ការគ្រប់គ្រងគោលនយោបាយសេដ្ឋកិច្ច ហិរញ្ញវត្ថុ និងគោលនយោបាយតាមវិស័យផ្សេងៗ",
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
                                    name: "១.១.១. ចង្កោមសកម្មភាពទី១ : គាំទ្រ សម្របសម្រួល និងគ្រប់គ្រងការងាររដ្ឋបាលរបស់អគ្គនាយកដ្ឋាន",
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
                            ]
                        }
                    }
                ],
                limit: 10,
                total: 3,
            },
            formAttributes: [
                {
                    name: "title",
                    type: "text",
                    required: true
                },
                {
                    name: "title_kh",
                    type: "text",
                    required: true
                },
                {
                    name: "ceiling_year",
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
                    name: "status",
                    type: "select",
                    required: true,
                    data: [
                        {
                            "id": 1,
                            "name": "In Progress",
                            "name_kh": "កំពុងដំណើរការ"
                        },
                        {
                            "id": 2,
                            "name": "Complete",
                            "name_kh": "បញ្ចប់"
                        }
                    ],
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
        initDownload() {
            const _base_url = window.location.origin;
            window.location = _base_url + '/download/BSP/2023/BSP.xlsx';
        },
        initRequest() {
            this.$vs.loading();
            this.getData();
        },
        initDataForm() {
            alert("testing");
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
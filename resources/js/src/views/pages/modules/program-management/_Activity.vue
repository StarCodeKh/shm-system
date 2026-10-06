<template>
    <d-table-list @clicked="initTableData" :api="api" ref="refInitPage" :allowDel="true" :title="title"
        :dataInfo="dataInfo" :dataAttributes="dataAttributes" :dataHeaders="dataHeaders" :dataTables="data"
        :formAttributes="formAttributes" :rowDisplay="rowDisplay"></d-table-list>
</template>

<script>
import axios from "@/axios.js";
import apiConfig from "@/apiConfig.js";
import { ref } from 'vue';

import DTableList from '@/views/form-builder/DTableList.vue';

export default {
    data() {
        return {
            title: "activity",
            api: apiConfig._apiActivity,
            dataAttributes: {
                popupFullscreen: true,
                backgroundColor: "warning",
                tableStyle: 2,
                page_number: 1,
                offset: 0,
                dataGrid: "row",
                allowDel: false,
                actionButton: [
                    {
                        icon: "EyeIcon",
                        path: "#",
                        method: "View",
                        allow: true
                    },
                    {
                        icon: "EditIcon",
                        path: "#",
                        method: "Edit",
                        allow: true
                    },
                ]
            },
            dataHeaders: {
                header1: "code",
                header2: "name_en",
                header3: "name_kh",
                // header4: "program",
                // header5: "indicator",
                header5: "entity",
                header6: "entity_member",
                header7: "order_level"
            },
            data: {
                data: [
                    // {
                    //     id: 1,
                    //     code: "",
                    //     name_en: "#120 - អនុកម្មវិធី",
                    //     name_kh: "#120 - អនុកម្មវិធី",
                    //     children: [
                    //         {
                    //             id: 1,
                    //             sub_program_id: 1,
                    //             code: "001",
                    //             name_en: "#001 - ចង្កោមសកម្មភាពទី ១",
                    //             name_kh: "#001 - ចង្កោមសកម្មភាពទី ១",
                    //             entity_id: 1,
                    //             entity_member_id: 2,
                    //             order_level: 1,
                    //             indicator: {
                    //                 data: [
                    //                     {
                    //                         id: 1,
                    //                         code: "#1.3.10-1",
                    //                         kpi_name_en: "ឈ្មោះសូចនាករ",
                    //                         kpi_name_kh: "ឈ្មោះសូចនាករ",
                    //                         order_level: 1,
                    //                         status: "Active"
                    //                     }, {
                    //                         id: 2,
                    //                         code: "#1.3.10-2",
                    //                         kpi_name_en: "ឈ្មោះសូចនាករ",
                    //                         kpi_name_kh: "ឈ្មោះសូចនាករ",
                    //                         order_level: 1,
                    //                         status: "Active"
                    //                     }, {
                    //                         id: 3,
                    //                         code: "#1.3.10-3",
                    //                         kpi_name_en: "ឈ្មោះសូចនាករជាភាសាអង់គ្លេស",
                    //                         kpi_name_kh: "ឈ្មោះសូចនាករជាភាសាអង់គ្លេស",
                    //                         order_level: 1,
                    //                         status: "InActive"
                    //                     }
                    //                 ]
                    //             },
                    //         }
                    //         // {
                    //         //     id: 1,
                    //         //     // code_cluster_activity: "001",
                    //         //     name_en: "ចង្កោមសកម្មភាពទី ១",
                    //         //     name_kh: "ចង្កោមសកម្មភាពទី ១",
                    //         //     sub_program: "អនុកម្មវិធីទី១",
                    //         //     responsible_person: "ឯកឧត្តម ទទួលបន្ទុក",
                    //         //     responsible_entity: "ឈ្មោះអង្គភាពទទួលបន្ទុក",
                    //         //     order_level: 1
                    //         // },
                    //         // {
                    //         //     id: 2,
                    //         //     // code_cluster_activity: "001",
                    //         //     name_en: "ចង្កោមសកម្មភាពទី ២",
                    //         //     name_kh: "ចង្កោមសកម្មភាពទី ២",
                    //         //     sub_program: "អនុកម្មវិធីទី២",
                    //         //     responsible_person: "ឯកឧត្តម ទទួលបន្ទុក",
                    //         //     responsible_entity: "ឈ្មោះអង្គភាពទទួលបន្ទុក",
                    //         //     order_level: 2
                    //         // },
                    //         // {
                    //         //     id: 3,
                    //         //     // code_cluster_activity: "001",
                    //         //     name_en: "ចង្កោមសកម្មភាពទី ៣",
                    //         //     name_kh: "ចង្កោមសកម្មភាពទី ៣",
                    //         //     sub_program: "អនុកម្មវិធីទី៣",
                    //         //     responsible_person: "ឯកឧត្តម ទទួលបន្ទុក",
                    //         //     responsible_entity: "ឈ្មោះអង្គភាពទទួលបន្ទុក",
                    //         //     order_level: 3
                    //         // },
                    //     ]
                    // }
                ],
                sub_program_id: [
                    {
                        "label": "អនុកម្មវិធីទី១",
                        "value": 1,
                    },
                    {
                        "label": "អនុកម្មវិធីទី២",
                        "value": 2,
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
                entity_member_id: [
                    {
                        "label": "សមាជិកទី១",
                        "value": 1,
                    },
                    {
                        "label": "សមាជិកទី២",
                        "value": 2,
                    }
                ],
                limit: 10,
                total: 3
            },
            formAttributes: [
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
                    hasFilter: false,
                },
                {
                    name: "entity_id",
                    type: "select",
                    hasFilter: true,
                    filterObj: "entity_member_id",
                    api: apiConfig._apiMemberByEntity,
                    required: true,
                    options: [
                        {
                            "label": "អង្គភាពទី១",
                            "value": 1,
                        },
                        {
                            "label": "អង្គភាពទី២",
                            "value": 2,
                        }
                    ],
                },
                {
                    name: "entity_member_id",
                    type: "select",
                    required: true,
                    options: [
                        {
                            "label": "បុគ្គលទិ១",
                            "value": 1,
                        },
                        {
                            "label": "បុគ្គលទិ២",
                            "value": 2,
                        }
                    ],
                },
                {
                    name: "name_en",
                    type: "text",
                    required: true
                },
                {
                    name: "name_kh",
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
<template>
    <div class="flex items-center">
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
            title: "bsp_heading_subpro_res_entity",
            api: apiConfig._apiObjective,
            dataAttributes: {
                enableDownload: true,
                tableStyle: 4,
                page_number: 1,
                offset: 0,
                dataGrid: "row",
                hasHeadingReport: true,
                headingReport: "bsp_heading_subpro_res_entity",
                actionButton: [
                    {
                        icon: "DollarSignIcon",
                        path: "/module/budget-arrangement/budget-ceiling/list",
                        method: "View"
                    }
                ],
                popupFullscreen: true
            },
            dataHeaders: {
                header1: {
                    width: 200,
                    label: "bsp_text_object_pro_sub_clusact_act",//"bsp_title_objective",
                    rowspan: 0,
                    colspan: 0,
                },

                header2: {
                    label: "entity",
                    rowspan: 0,
                    colspan: 0,
                },
                header3: {
                    label: "entity_member",
                    rowspan: 0,
                    colspan: 0,
                },
                header4: {
                    label: "action",
                    rowspan: 0,
                    colspan: 0,
                }
            },
            data: {
                data: [
                    {
                        id: 1,
                        name: "គោលបំណងគោលនយោបាយទី ១",
                        name_kh: "គោលបំណងគោលនយោបាយទី ១",
                        remark: "",
                        status: 1,
                        hasColspan: true,
                        colspan: 5,
                        rowspan: 0,
                        order_level: "",
                        indicator: [
                            {
                                id: 1,
                                code: "",
                                indicator: "",
                                indicator_kh: "",
                                status: "active"
                            }
                        ],
                        children: [
                            {
                                id: 1,
                                name: "កម្មវិធីទី១ : ការគ្រប់គ្រងវិស័យសេដ្ឋកិច្ច",
                                entity_member: "បុគ្គលទទួលខុសត្រូវ",
                                entity: "ឈ្មោះអង្គភាព",
                                order_level: 1,
                                indicator: [
                                    {
                                        id: 1,
                                        code: "",
                                        indicator: "",
                                        indicator_kh: "",
                                        status: "active"
                                    }
                                ],
                                children: [
                                    {
                                        id: 1,
                                        name: "១.១. អនុកម្មវិធីទី១.១ : ការគ្រប់គ្រងគោលនយោបាយសេដ្ឋកិច្ច ហិរញ្ញវត្ថុ និងគោលនយោបាយតាមវិស័យផ្សេងៗ",
                                        entity: "ឈ្មោះអង្គភាព",
                                        entity_member: "បុគ្គលទទួលខុសត្រូវ",
                                        order_level: 1,
                                        indicator: [
                                            {
                                                id: 1,
                                                code: "",
                                                indicator: "",
                                                indicator_kh: "",
                                                status: "active"
                                            }
                                        ],
                                        children: [
                                            {
                                                id: 1,
                                                name: "១.១.១. ចង្កោមសកម្មភាពទី១ : គាំទ្រ សម្របសម្រួល និងគ្រប់គ្រងការងាររដ្ឋបាលរបស់អគ្គនាយកដ្ឋាន",
                                                entity: "ឈ្មោះអង្គភាព",
                                                entity_member: "បុគ្គលទទួលខុសត្រូវ",
                                                order_level: 1,
                                                indicator: [
                                                    {
                                                        id: 1,
                                                        code: "",
                                                        indicator: "",
                                                        indicator_kh: "",
                                                        status: "active"
                                                    }
                                                ],
                                                children: [
                                                    {
                                                        id: 1,
                                                        name: "១.១.១.១. សកម្មភាពទី១ : បំពេញមុខជាលេខាធិការដ្ឋាន",
                                                        entity: "ឈ្មោះអង្គភាព",
                                                        entity_member: "បុគ្គលទទួលខុសត្រូវ",
                                                        order_level: 1,
                                                        indicator: [
                                                            {
                                                                id: 1,
                                                                code: "",
                                                                indicator: "",
                                                                indicator_kh: "",
                                                                status: "active"
                                                            }
                                                        ],
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
                objective_id: [
                    {
                        "label": "គោលបំណងទី១",
                        "value": 1,
                    },
                    {
                        "label": "គោលបំណងទី២",
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
                program_id: [
                    {
                        "label": "កម្មវិធីទី១",
                        "value": 1,
                    },
                    {
                        "label": "កម្មវិធីទី២",
                        "value": 2,
                    }
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
                cluster_activity_id: [
                    {
                        "label": "ចង្កោមសកម្មភាពទី១",
                        "value": 1,
                    },
                    {
                        "label": "ចង្កោមសកម្មភាពទី២",
                        "value": 2,
                    }
                ],
                limit: 10,
                total: 3,
            },
            formAttributes: [
                {
                    name: "objective_id",
                    type: "select",
                    required: true,
                    hasFilter: true,
                    filterObj: "program_id",
                    api: apiConfig._apiProgramByObj
                },
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
            window.location = _base_url + '/download/Program/Program.xlsx';
        },
        initDataForm() {
            // alert("testing");
        },
        getDataTable(_search_criteria) {
            this.$vs.loading.close();
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
    },
    created() {
        this.$vs.loading();
        this.getData();
    },
    watch: {
    }
}
</script>
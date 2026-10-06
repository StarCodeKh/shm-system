<template>
    <div>
        <form-indicator ref="refOpenPrivatePopupForm" :api="dataAttributes.api" :dataAttributes="dataAttributes"
            :dataInfo="dataInfo"></form-indicator>
        <d-table-list @clicked="initTableData" :api="api" ref="refInitPage" :allowDel="true" :title="title"
            :dataInfo="dataInfo" :dataAttributes="dataAttributes" :dataHeaders="dataHeaders" :dataTables="data"
            :formAttributes="formAttributes" :rowDisplay="rowDisplay" @clickPrivateForm="initOpenForm"
            @initDownload="initDownload"></d-table-list>
    </div>
</template>

<script>
import axios from "@/axios.js";
import apiConfig from "@/apiConfig.js";
import { ref } from 'vue';
import FormIndicator from '@/views/pages/modules/program-management/_FormProIndicator.vue';
import DTableList from '@/views/form-builder/DTableList.vue';

export default {
    data() {
        return {
            title: "Program",
            api: apiConfig._apiProgram,
            dataAttributes: {
                api: apiConfig._apiKPIProgram,
                popupFullscreen: true,
                backgroundColor: "warning",
                tableStyle: 2,
                page_number: 1,
                offset: 0,
                dataGrid: "row",
                enableToggleForm: false,
                enableDownload: true,
                // hideFormData: true,
                // hasIndicatorSubPro: true,
                allowDel: true,
                actionButton: [
                    {
                        icon: "ListIcon",
                        path: "#",
                        method: "PrivateForm",
                        allow: true
                    },
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
                    {
                        icon: "TrashIcon",
                        path: "#",
                        method: "Delete",
                        allow: true
                    },
                ]
            },
            dataHeaders: {
                // header1: "code_subprogram",
                header1: "code",
                header2: "name_en",
                header3: "name_kh",
                // header4: "program",
                indicator: "indicator",
                header6: "entity",
                header7: "entity_member",
                header8: "order_level"
            },
            data: {
                data: [
                    {
                        id: 1,
                        name_en: "12-កម្មវិធីទី១",
                        name_kh: "12-កម្មវិធីទី១",
                        code: "",
                        children: [
                            {
                                id: 1,
                                program_id: 1,
                                code: "001",
                                name_en: "#001 - អនុកម្មវិធីទី ១",
                                name_kh: "#001 - អនុកម្មវិធីទី ១",
                                entity_id: 1,
                                entity_member_id: 2,
                                order_level: 1,
                                indicator: {
                                    data: [
                                        {
                                            id: 1,
                                            code: "#1.3.10-1",
                                            kpi_name_en: "ឈ្មោះសូចនាករ",
                                            kpi_name_kh: "ឈ្មោះសូចនាករ",
                                            order_level: 1,
                                            status: "Active"
                                        }, {
                                            id: 2,
                                            code: "#1.3.10-2",
                                            kpi_name_en: "ឈ្មោះសូចនាករ",
                                            kpi_name_kh: "ឈ្មោះសូចនាករ",
                                            order_level: 1,
                                            status: "Active"
                                        }, {
                                            id: 3,
                                            code: "#1.3.10-3",
                                            kpi_name_en: "ឈ្មោះសូចនាករជាភាសាអង់គ្លេស",
                                            kpi_name_kh: "ឈ្មោះសូចនាករជាភាសាអង់គ្លេស",
                                            order_level: 1,
                                            status: "InActive"
                                        }
                                    ]
                                },
                            }
                        ]
                    }
                ],
                objective_id: [
                    {
                        "label": "កម្មវិធីទី១",
                        "value": 1,
                    },
                    {
                        "label": "កម្មវិធីទី២",
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
                    name: "objective_id",
                    type: "select",
                    required: true,
                    hasDefault: false,
                    defaultOptions: {},
                    options: [
                        {
                            value: 1,
                            label: "កម្មវិធីទី១"
                        },
                        {
                            value: 2,
                            label: "កម្មវិធីទី២"
                        }
                    ]
                },
                {
                    name: "code",
                    type: "text",
                    required: true
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
                    name: "entity_id",
                    type: "select",
                    required: true,
                    hasFilter: true,
                    filterObj: "entity_member_id",
                    api: apiConfig._apiMemberByEntity,
                    options: [],
                },
                {
                    name: "entity_member_id",
                    type: "select",
                    required: true,
                    options: [],
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
                {
                    name: "is_active",
                    type: "checkbox",
                    required: false,
                    attributes: [
                        {
                            name: "is_active",
                            value: "1"
                        }
                    ]
                }
            ],
            rowDisplay: "2grid", //1grid, 2grid, 3grid, 4grid
            dataFields: [],
            dataInfo: {}
        }
    },
    components: {
        FormIndicator,
        DTableList,
    },
    methods: {
        initDownload() {
            const _base_url = window.location.origin;
            window.location = _base_url + '/download/Program/Program.xlsx';
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
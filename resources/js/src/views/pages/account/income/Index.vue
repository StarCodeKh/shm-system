<template>
    <d-table-list @clicked="initTableData" :api="api" ref="refInitPage" :allowDel="true" :title="title" :dataInfo="dataInfo"
        :dataAttributes="dataAttributes" :dataHeaders="dataHeaders" :dataTables="data" :formAttributes="formAttributes"
        :rowDisplay="rowDisplay"></d-table-list>
</template>

<script>
import axios from "@/axios.js"
import apiConfig from "@/apiConfig.js"
import { ref } from 'vue';
import DTableList from '@/views/form-builder/DTableList.vue'

    export default {
    data() {
        return {
            title: "setting_Income",
            api: apiConfig._apiIncome,
            dataAttributes: {
                tableStyle: 1,
                page_number: 1,
                offset: 0,
                dataGrid: "row",
                popupFullscreen: true,
                actionButton: [
                    // {
                    //     icon: "ListIcon",
                    //     path: "/officer/activity",
                    //     method: "PrivateLink",
                    //     allow: true, 
                    // },
                ],
                enableImport: true,
                enableDownload: true
            },
            dataHeaders: {},
            data: {
                data: [],
                limit: 10,
                total: 3,
            },
            formAttributes: [
                {
                    "name": "title_kh",
                    "type": "textarea",
                    "required": false
                },
                {
                    "name": "title_en",
                    "type": "textarea",
                    "required": true
                },
                {
                    "name": "department_id",
                    "type": "select",
                    "required": true,
                    "hasFilter": true,
                    "hasFormData": false,
                    "filterObj": "entity_id",
                    "api": apiConfig._apiEntityByDept,
                },
                {
                    "name": "entity_id",
                    "type": "select",
                    "required": true
                },
                {
                    "name": "account_code",
                    "type": "select",
                    "required": true
                },
                {
                    "name": "issue_date",
                    "type": "date",
                    "required": true
                },
                {
                    "name": "category",
                    "type": "text",
                    "required": false
                },
                {
                    "name": "amount",
                    "type": "number",
                    "required": true
                },
                {
                    "name": "currency_id",
                    "type": "select",
                    "required": true
                },
                {
                    "name": "exchange_rate",
                    "type": "number",
                    "required": true
                },
                {
                    "name": "payment_method_id",
                    "type": "select",
                    "required": true
                },
                {
                    "name": "reference",
                    "type": "text",
                    "required": false
                },
                {
                    "name": "remark",
                    "type": "textarea",
                    "required": false
                },
                {
                    "name": "status",
                    "type": "select",
                    "required": true
                }
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
                            this.dataHeaders = response.data.data_fields; 
                            // this.formAttributes = response.data.form_attributes;
                        } else {
                            this.data = this.data;
                        }
                        this.$vs.loading.close();
                    }).catch((error) => {
                        this.$vs.notify({
                            title: 'Message',
                            text: "Error, while trying to do action or no permission allow!",
                            iconPack: 'feather',
                            icon: 'icon-check-circle',
                            color: 'danger'
                        })
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
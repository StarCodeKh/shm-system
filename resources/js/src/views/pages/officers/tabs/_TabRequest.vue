<template>
    <d-table-list @clicked="initTableData" :api="api" ref="refInitPage" :allowDel="true" :title="title" :dataInfo="dataInfo"
        :dataAttributes="dataAttributes" :dataHeaders="dataHeaders" :dataTables="data" :formAttributes="formAttributes"
        :rowDisplay="rowDisplay" @initImport="initImport"></d-table-list>
</template>

<script>
    import axios from "@/axios.js";
    import apiConfig from "@/apiConfig.js";
    import DTableList from '@/views/form-builder/DTableList.vue';

    export default {
        data() {
            return {
                title: "setting_request",
                api: apiConfig._apiOfficerRequest,
                dataAttributes: {
                    tableStyle: 1,
                    page_number: 1,
                    offset: 0,
                    dataGrid: "row",
                    actionButton: [
                    ],
                    enableImport: true,
                    enableDownload: true,
                    enableDashboardRequest: true
                },
                dataHeaders: {
                    header1: "request_title",
                    header2: "request_from",
                    header3: "letter_type",
                },
                data: {
                    data: [
                    ],
                    limit: 10,
                    total: 3,
                },
                formAttributes: [
                    {
                        "name": "officer_id",
                        "type": "select",
                        "required": true
                    },
                    {
                        "name": "code",
                        "type": "text",
                        "required": false
                    },
                    {
                        "name": "request_title",
                        "type": "select",
                        "required": true,
                        "hasFilter": true,
                        "hasFormData": false,
                        "filterObj": "amount",
                        "api": apiConfig._apiPaymentTypeGetAmount,
                    },
                    {
                        "name": "amount",
                        "type": "text",
                        "required": true
                    },
                    {
                        "name": "currency_id",
                        "type": "select",
                        "required": true
                    },
                    {
                        "name": "payment_method_id",
                        "type": "select",
                        "required": true
                    },
                    {
                        "name": "request_date",
                        "type": "date",
                        "required": true
                    },
                    {
                        "name": "issue_date",
                        "type": "date",
                        "required": false
                    },
                    {
                        "name": "bank",
                        "type": "select",
                        "required": false
                    },
                    {
                        "name": "bank_account_no",
                        "type": "text",
                        "required": false
                    },
                    {
                        "name": "bank_code",
                        "type": "text",
                        "required": false
                    },
                    {
                        "name": "reference",
                        "type": "text",
                        "required": false
                    },
                    {
                        "name": "tax",
                        "type": "number",
                        "required": false
                    },
                    {
                        "name": "amount_after_tax",
                        "type": "text",
                        "required": false
                    },
                    {
                        "name": "order_level",
                        "type": "number",
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
                        limit: _search_criteria.limit,
                        search_field: this.dataFields,
                        officer_id: this.$route.params.id,
                    };
                } else {
                    _params = {
                        sort: _search_criteria.sort,
                        order: _search_criteria.order,
                        page_number: _search_criteria.page_number,
                        limit: _search_criteria.limit,
                        officer_id: this.$route.params.id,
                    };
                }

                return new Promise((resolve, reject) => {
                    axios.post(this.api + "/search", _params)
                    .then((response) => {
                        if (response.data) {
                            this.data = response.data;
                            this.dataHeaders = response.data.data_fields;
                        } else {
                            this.data = this.data;
                        }
                        this.$vs.loading.close();
                    }).catch((error) => {
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
                    limit: searchQuery.limit,
                    search_field: searchQuery.searchFields
                }
                this.getDataTable(_search_criteria);
                return false;
            },
            initImport(params){
            }
        },
        created() {
            this.$vs.loading();
            this.getData();
        },
    }
</script>

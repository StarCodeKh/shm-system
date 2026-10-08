<template>
    <d-table-list @clicked="initTableData" :api="api" ref="refInitPage" :allowDel="true" :title="$t(title)" :dataInfo="dataInfo"
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
                title: 'payment_history', // i18n key: translated in the template, so it follows a language switch
                api: apiConfig._apiOfficerPayment,
                dataAttributes: {
                    tableStyle: 1,
                    page_number: 1,
                    offset: 0,
                    dataGrid: "row",
                    actionButton: [
                    ],
                    enableImport: true,
                    enableDownload: true,
                    enablePaymentHistory: true
                },
                dataHeaders: {
                    header1: "officer_id",
                    header2: "amount",
                    header3: "payment_date",
                    header4: "payment_issue",
                    header5: "currency",
                    header6: "status"
                },
                data: {
                    data: [
                    ],
                    limit: 10,
                    total: 3,
                },
                formAttributes: [
                    {
                        name: "officer_id",
                        type: "select",
                        required: true
                    },
                    {
                        name: "amount",
                        type: "text",
                        required: true
                    },
                    {
                        name: "payment_date",
                        type: "date",
                        required: false
                    },
                    {
                        name: "payment_issue",
                        type: "date",
                        required: false
                    },
                    {
                        name: "currency_id",
                        type: "select",
                        required: false
                    },
                    {
                        name: "status",
                        type: "checkbox",
                        required: false
                    }
                ],
                rowDisplay: "2grid",
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

                return new Promise(() => {
                    axios.post(this.api + "/search", _params)
                        .then((response) => {
                            if (response.data) {
                                this.data = response.data;
                                this.dataHeaders = response.data.data_fields;
                                this.formAttributes = response.data.form_attributes;
                            } else {
                                this.data = this.data;
                            }
                            this.$vs.loading.close();
                        }).catch((error) => {
                            this.$vs.notify({
                                title: 'Message',
                                text: 'msg.action_failed',
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
                    limit: searchQuery.limit,
                    search_field: searchQuery.searchFields
                }
                this.getDataTable(_search_criteria);
                return false;
            },
            initImport(params){}
        },
        created() {
            this.$vs.loading();
            this.getData();
        },
        watch: {
        }
    }
</script>

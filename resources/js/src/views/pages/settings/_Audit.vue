<template>
    <d-table-list @clicked="initTableData" :api="api" ref="refInitPage" :allowDel="true" :title="title" :dataInfo="dataInfo"
        :dataAttributes="dataAttributes" :dataHeaders="dataHeaders" :dataTables="data" :formAttributes="formAttributes"
        :rowDisplay="rowDisplay" @initImport="initImport"></d-table-list>
</template>

<script>
    import axios from "@/axios.js";
    import apiConfig from "@/apiConfig.js";
    import { ref } from 'vue';
    import DTableList from '@/views/form-builder/DTableList.vue';

    export default {
        data() {
            return {
                title: "setting_audit",
                api: apiConfig._apiAudit,
                allowDel: false,
                dataAttributes: {
                    tableStyle: 1,
                    page_number: 1,
                    offset: 0,
                    dataGrid: "row",
                    actionButton: [
                        // {
                        //     icon: "DollarSignIcon",
                        //     path: "/module/budget-arrangement/budget-ceiling/list",
                        //     method: "View"
                        // }
                    ],
                    enableImport: true,
                    enableDownload: true
                },
                dataHeaders: {
                    header1: "user_type",
                    header2: "user_id",
                    header3: "event",
                    header4: "auditable_type",
                    header5: "auditable_id",
                    header6: "old_values",
                    header7: "new_values",
                    header8: "url",
                    header9: "ip_address",
                    header10: "user_agent",
                    header11: "tags",
                    header12: "created_at",
                    header13: "updated_at",
                },
                data: {
                    data: [
                    ],
                    limit: 10,
                    total: 3,
                },
                formAttributes: [
                    {
                        name: "user_id",
                        type: "text",
                        required: true
                    },
                    {
                        name: "user_type",
                        type: "text",
                        required: true
                    },
                    {
                        name: "event",
                        type: "text",
                        required: false
                    },
                    {
                        name: "auditable_type",
                        type: "text",
                        required: false
                    },
                    {
                        name: "auditable_id",
                        type: "text",
                        required: false
                    },
                    {
                        name: "old_values",
                        type: "text",
                        required: false
                    },
                    {
                        name: "new_values",
                        type: "text",
                        required: false
                    },
                    {
                        name: "url",
                        type: "text",
                        required: false
                    },
                    {
                        name: "ip_address",
                        type: "text",
                        required: false
                    },
                    {
                        name: "user_agent",
                        type: "text",
                        required: false
                    },
                    {
                        name: "tags",
                        type: "text",
                        required: false
                    },
                    {
                        name: "created_at",
                        type: "text",
                        required: false
                    },
                    {
                        name: "updated_at",
                        type: "text",
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
            },
            initImport(params){
                // alert("testing");
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
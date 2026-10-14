<template>
    <d-table-list @clicked="initTableData" :api="api" ref="refInitPage" :allowDel="true" :title="title"
        :dataInfo="dataInfo" :dataAttributes="dataAttributes" :dataHeaders="dataHeaders" :dataTables="data"
        :formAttributes="formAttributes" :rowDisplay="rowDisplay"></d-table-list>
</template>

<script>
import axios from "@/axios.js";
import apiConfig from "@/apiConfig.js";
import DTableList from '@/views/form-builder/DTableList.vue';

export default {
    data() {
        return {
            title: "setting_officer",
            api: apiConfig._apiOfficer,
            dataAttributes: {
                tableStyle: 1,
                page_number: 1,
                offset: 0,
                dataGrid: "row",
                popupFullscreen: true,
                actionButton: [
                    {
                        icon: "ListIcon",
                        path: "/officer/activity",
                        method: "PrivateLink",
                        permission: "show",
                        allow: true,
                    },
                ],
                enableImport: true,
                enableDashboardOfficer: true
            },
            dataHeaders: {
                header1: "first_name",
                header2: "last_name",
                header3: "latin",
                header4: "email",
                header5: "position",
                header6: "status",
            },
            data: {
                data: [
                    {}
                ],
                limit: 10,
                total: 3,
            },
            formAttributes: [
                {
                    "name": "user_id",
                    "type": "hidden",
                    "required": false
                },
                {
                    "name": "photo",
                    "type": "file",
                    "required": true
                },
                {
                    "name": "identity_no",
                    "type": "text",
                    "required": false
                },
                {
                    "name": "identity_member_no",
                    "type": "text",
                    "required": true
                },
                {
                    "name": "first_name",
                    "type": "text",
                    "required": true
                },
                {
                    "name": "last_name",
                    "type": "text",
                    "required": true
                },
                {
                    "name": "latin",
                    "type": "text",
                    "required": true
                },
                {
                    "name": "gender",
                    "type": "select",
                    "required": false
                },
                {
                    "name": "dob",
                    "type": "date",
                    "required": true
                },
                {
                    "name": "email",
                    "type": "text",
                    "required": true
                },

                {
                    "name": "nssf_card_no",
                    "type": "text",
                    "required": false
                },
                {
                    "name": "register_date",
                    "type": "date",
                    "required": false
                },
                {
                    "name": "nationality",
                    "type": "text",
                    "required": false
                },
                {
                    "name": "title",
                    "type": "select",
                    "required": false
                },
                {
                    "name": "duty",
                    "type": "text",
                    "required": false
                },
                {
                    "name": "position",
                    "type": "select",
                    "required": false
                },
                {
                    "name": "entity_id",
                    "type": "select",
                    "required": true
                },
                {
                    "name": "department_id",
                    "type": "select",
                    "required": true
                },
                {
                    "name": "address",
                    "type": "textarea",
                    "required": false
                },
                {
                    "name": "bank",
                    "type": "select",
                    "required": false
                },
                {
                    "name": "bank_code",
                    "type": "text",
                    "required": false
                },
                {
                    "name": "bank_account_no",
                    "type": "text",
                    "required": false
                },
                {
                    "name": "updated_bank_acc",
                    "type": "text",
                    "required": false
                },
                {
                    "name": "telephone_1",
                    "type": "text",
                    "required": false
                },
                {
                    "name": "telephone_2",
                    "type": "text",
                    "required": false
                },
                {
                    "name": "status",
                    "type": "select",
                    "required": true
                },
                {
                    "name": "is_nssf_member",
                    "type": "select",
                    "required": true
                },
                {
                    "name": "order_level",
                    "type": "number",
                    "required": false
                }
            ],
            rowDisplay: "3grid",
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
                };
            } else {
                _params = {
                    sort: _search_criteria.sort,
                    order: _search_criteria.order,
                    page_number: _search_criteria.page_number,
                    limit: _search_criteria.limit,
                };
            }

            return new Promise(() => {
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
                        this.$vs.notify({
                            title: 'Message',
                            text: 'msg.action_failed',
                            iconPack: 'feather',
                            icon: 'icon-check-circle',
                            color: 'danger',
                            position: 'top-right'
                        });
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
        }
    },
    created() {
        this.$vs.loading();
        this.getData();
    },
}
</script>

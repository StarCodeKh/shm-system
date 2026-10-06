<template>
    <d-table-list v-if="isVisible" @clicked="initTableData" :api="api" ref="refInitPage" :allowDel="true" :title="title"
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
            title: "setting_user",
            api: apiConfig._apiUser,
            dataAttributes: {
                tableStyle: 1,
                page_number: 1,
                offset: 0,
                dataGrid: "row",
                popupFullscreen: true,
                actionButton: [
                    {
                        icon: "LockIcon",
                        path: "/module/budget-arrangement/budget-ceiling/list",
                        method: "View"
                    }
                ]
            },
            dataHeaders: {
                header1: "entity",
                header2: "role",
                header3: "name",
                header4: "email",
                header5: "position_name",
                header6: "is_active",
            },
            data: {
                data: [
                    {}
                ],

                limit: 10,
                total: 3,
            },
            formAttributes: [
                // {
                //     name: "entity_id",
                //     type: "select",
                //     required: true,
                //     hasFormData: true,
                //     filterObj: "entity",
                //     // options: [],
                // },
                {
                    name: "fullname",
                    type: "text",
                    required: true,
                },
                {
                    name: "latin",
                    type: "text",
                    required: true,
                },
                {
                    name: "role",
                    type: "select",
                    required: true,
                    // hasFormData: true,
                    // filterObj: "role",
                    // api: apiConfig._apiMemberByEntity,
                    // options: [],
                },
                // {
                //     name: "entity_member_id",
                //     type: "select",
                //     required: true,
                //     options: [],
                // },
                // {
                //     name: "title",
                //     type: "select",
                //     required: true,
                //     options: []
                // },
                {
                    name: "gender",
                    type: "select",
                    required: false,
                    options: []
                },
                // {
                //     name: "position",
                //     type: "select",
                //     required: true,
                //     options: []
                // },
                {
                    name: "email",
                    type: "text",
                    required: true
                },
                {
                    name: "telephone_1",
                    type: "text",
                    required: true
                },
                {
                    name: "username",
                    type: "text",
                    required: true
                },
                {
                    name: "password",
                    type: "password",
                    required: false
                },
                {
                    name: "password_confirmation",
                    type: "password",
                    required: false
                },
                {
                    name: "status",
                    type: "select",
                    required: true,
                    options: []
                },
                {
                    name: "receive_daily_notifications",
                    type: "checkbox",
                    required: false
                },
                {
                    name: "receive_all_notification",
                    type: "checkbox",
                    required: true,
                },
                // {
                //     name: "role",
                //     type: "hidden",
                //     required: true
                // },
            ],
            rowDisplay: "3grid", //1grid, 2grid, 3grid, 4grid
            dataFields: [],
            dataInfo: {},
            isVisible: false
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
                axios.post(this.api + "_index", _params)
                    .then((response) => {
                        if (response.data) {
                            this.data = response.data;
                            this.dataHeaders = response.data.data_fields; 
                            // this.formAttributes = response.data.form_attributes;
                        } else {
                            this.data = this.data;
                        }
                        this.isVisible = response.data.is_display;
                        this.$vs.loading.close();
                    }).catch((error) => {
                        // reject(error)
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
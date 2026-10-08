<template>
    <div>
        <role-form-modal ref="refOpenPrivatePopupForm"></role-form-modal>
        <d-table-list v-if="isVisible" @clicked="initTableData" :api="api" ref="refInitPage" :allowDel="false" :title="title" :dataInfo="dataInfo"
            :dataAttributes="dataAttributes" :dataHeaders="dataHeaders" :dataTables="data" :formAttributes="formAttributes"
            :rowDisplay="rowDisplay" @clickPrivateForm="initOpenForm"></d-table-list>
    </div>
</template>

<script>
import axios from "@/axios.js";
import apiConfig from "@/apiConfig.js";
import RoleFormModal from './_Role_Form_Modal.vue';
import DTableList from '@/views/form-builder/DTableList.vue';

export default {
    data() {
        return {
            title: "setting_role",
            api: apiConfig._apiRole,
            dataAttributes: {
                tableStyle: 1,
                page_number: 1,
                offset: 0,
                dataGrid: "row",
                popupFullscreen: true,
                actionButton: [
                    {
                        icon: "LockIcon",
                        path: "/role/form",
                        method: "PrivateForm",
                        permission: "permission",
                        allow: true
                    }
                ]
            },
            dataHeaders: {
                header1: "name",
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
                    name: "name",
                    type: "text",
                    required: true
                },
                {
                    name: "is_active",
                    type: "checkbox",
                    required: false
                },
            ],
            rowDisplay: "3grid", //1grid, 2grid, 3grid, 4grid
            dataFields: [],
            dataInfo: {},
            isVisible: false
        }
    },
    components: {
        RoleFormModal,
        DTableList
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
                        this.isVisible = response.data.is_display !== false; // hidden only when the server says no access
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

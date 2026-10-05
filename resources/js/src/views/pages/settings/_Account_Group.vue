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
            title: "setting_group_chapter",
            api: apiConfig._apiAccountGroup,
            dataAttributes: {
                tableStyle: 1,
                page_number: 1,
                offset: 0,
                dataGrid: "row",
                actionButton: []
            },
            dataHeaders: {
                header1: "account_type_group",
                header2: "code",
                header3: "name_en",
                header4: "name_kh",
                header5: "order_level",
            },
            data: {
                data: [],
                limit: 10,
                total: 3,
            },
            formAttributes: [
                {
                    name: "account_type_group_id",
                    type: "select",
                    required: true,
                    options: []
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
                    name: "order_level",
                    type: "number",
                    required: false
                },
                {
                    name: "description",
                    type: "textarea",
                    required: false
                },
            ],
            rowDisplay: "2grid", //1grid, 2grid, 3grid, 4grid
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

            return new Promise(() => {
                axios.post(this.api + "/search", _params)
                    .then((response) => {
                        if (response.data) {
                            this.data = response.data;
                        } else {
                            this.data = this.data;
                        }
                        this.isVisible = response.data.is_display;
                        this.$vs.loading.close();
                    }).catch(() => {
                        this.$vs.notify({
                            title: 'Message',
                            text: "Error, while trying to do action or no permission allow!",
                            iconPack: 'feather',
                            icon: 'icon-check-circle',
                            color: 'danger'
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
<template>
    <div>
        <table-budget-template ref="refOpenPrivatePopupForm" :childrenAttributes="childrenAttributes"
            :dataInfo="dataInfo"></table-budget-template>
        <d-table-list v-if="isVisible" @clicked="initTableData" :api="api" ref="refInitPage" :allowDel="true"
            :title="title" :dataInfo="dataInfo" :dataAttributes="dataAttributes" :dataHeaders="dataHeaders"
            :dataTables="data" :formAttributes="formAttributes" :rowDisplay="rowDisplay"
            @clickPrivateForm="initOpenForm"></d-table-list>
    </div>
</template>

<script>
import axios from "@/axios.js";
import apiConfig from "@/apiConfig.js";
import TableBudgetTemplate from '@/views/pages/settings/include_budget_template/_TableBudgetTemplate.vue';
import DTableList from '@/views/form-builder/DTableList.vue';

export default {
    data() {
        return {
            title: "setting_budget_template",
            api: apiConfig._apiBudgetTemplate,
            dataAttributes: {
                tableStyle: 1,
                page_number: 1,
                offset: 0,
                dataGrid: "row",
                popupFullscreen: true,
                actionButton: [
                    {
                        icon: "ListIcon",
                        path: "#",
                        method: "PrivateForm",
                        allow: true
                    }
                ],
            },
            childrenAttributes: {
                title: "តារាងទិន្នន័យពុម្ភថវិកា",
                api: apiConfig._apiBudgetTemplateItemCost,
                dataAttributes: {
                    tableStyle: 1,
                    page_number: 1,
                    offset: 0,
                    dataGrid: "row",
                    popupFullscreen: true,
                    hideSearchBar: false
                },
            },
            dataHeaders: {
                header1: "name_en",
                header2: "name_kh",
                header3: "order_level",
            },
            data: {
                data: [
                    {
                        name_en: "C007-វគ្គបណ្តុះបណ្តាលក្រៅប្រទេស ជាបុគ្គល (រយៈពេល៥ថ្ងៃ នៅអឺរ៉ុប)",
                        name_kh: "C007-វគ្គបណ្តុះបណ្តាលក្រៅប្រទេស ជាបុគ្គល (រយៈពេល៥ថ្ងៃ នៅអឺរ៉ុប)",
                        description: "",
                        order_level: 1,
                        is_active: 1
                    },
                    {
                        name_en: "O004-បេសកម្មក្នុងប្រទេស (សម្រាប់មន្រ្តី អ.គ.ហ.-រយះពេល៣ថ្ងៃ)",
                        name_kh: "O004-បេសកម្មក្នុងប្រទេស (សម្រាប់មន្រ្តី អ.គ.ហ.-រយះពេល៣ថ្ងៃ)",
                        description: "",
                        order_level: 1,
                        is_active: 1
                    },
                    {
                        name_en: "O003-បេសកម្មក្នុងប្រទេស (សម្រាប់ថ្នាក់ដឹកនាំ-រយះពេល៣ថ្ងៃ)",
                        name_kh: "O003-បេសកម្មក្នុងប្រទេស (សម្រាប់ថ្នាក់ដឹកនាំ-រយះពេល៣ថ្ងៃ)",
                        description: "",
                        order_level: 1,
                        is_active: 1
                    },
                ],
                account_type_id: [
                    {
                        "label": "សិក្ខាសាលា /បណ្តុះបណ្តាល និងកិច្ចប្រជុំ ក្នុងប្រទេស",
                        "value": 1,
                    },
                    {
                        "label": "សម្ភារៈផ្គត់ផ្គង់ថែទាំ",
                        "value": 2,
                    },
                    {
                        "label": "លទ្ធកម្មទំនិញ",
                        "value": 3,
                    }
                ],
                limit: 10,
                total: 3,
            },
            formAttributes: [
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
        TableBudgetTemplate,
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
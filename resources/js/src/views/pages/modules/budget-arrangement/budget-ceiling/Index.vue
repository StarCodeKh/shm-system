<template>
    <div>
        <form-ceiling-entity ref="refOpenPrivatePopupForm" :api="dataAttributes.api" :dataAttributes="dataAttributes"
        :dataInfo="dataInfo" />
        <DTableList @clicked="initTableData" :api="api" ref="refInitPage" :allowDel="true" :title="title" 
        :dataInfo="dataInfo" :dataAttributes="dataAttributes" :dataHeaders="dataHeaders" :dataTables="data" 
        :formAttributes="formAttributes" :rowDisplay="rowDisplay" @clickPrivateForm="initOpenForm" @initDownload="initDownload"/>
    </div>
</template>

<script>
import axios from "@/axios.js"
import apiConfig from "@/apiConfig.js"
import { ref } from 'vue';
import DTableList from '@/views/form-builder/DTableList.vue'
import DForm from '@/views/form-builder/DForm.vue'
import FormCeilingEntity from '@/views/pages/modules/budget-arrangement/budget-ceiling/_FormCeilingEntity.vue';

export default {
    data() {
        return {
            title: "CeilingBudget",
            api: apiConfig._apiCeilingRule,
            dataAttributes: {
                popupFullscreen: true,
                enableDownload: true,
                api: apiConfig._apiCeilingEntity,
                tableStyle: 1,
                page_number: 1,
                offset: 0,
                dataGrid: "row",
                actionButton: [
                    {
                        icon: "ListIcon",
                        path: "#",
                        method: "PrivateForm",
                        allow: true
                    },
                ]
            },
            dataHeaders: {
                // header1: "id",
                header1: "name_en",
                header2: "name_kh",
                header3: "issue_date",
                header4: "status",
            },
            data: {
                data: [
                    {
                        id: 1,
                        name_en: "ពិដានប្រចាំឆ្នាំ២០១៩",
                        name_kh: "ពិដានប្រចាំឆ្នាំ២០១៩",
                        issue_date: "2022",
                        status: "បានអនុម័ត",
                    },
                ],
                status: [
                    {
                        label: "កំពុងដំណើរការ",
                        value: 1
                    },
                    {
                        label: "បញ្ចប់",
                        value: 2,
                    },
                    {
                        label: "បានអនុម័ត",
                        value: 3,
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
                    name: "issue_date",
                    type: "date",
                    required: true
                },
                {
                    name: "status",
                    type: "select",
                    required: true,
                    hasDefault: true,
                    defaultOptions: {
                        label: "កំពុងដំណើរការ",
                        value: 1,
                    },
                    options: [
                    ],
                },
            ],
            rowDisplay: "2grid", //1grid, 2grid, 3grid, 4grid
            dataFields: [],
            dataInfo: []
        }
    },
    components: {
        DTableList,
        DForm,
        FormCeilingEntity,
    },
    methods: {
        initDownload() {
            const _base_url = window.location.origin;
            window.location = _base_url + '/download/Ceiling/2023/BSP2023-2025-PB2023-Ceilings-data.xlsx';
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
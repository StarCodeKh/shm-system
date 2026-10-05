<template>
    <!-- <d-table-list @clicked="initTableData" :api="api" ref="refInitPage" :allowDel="true" :title="title" :dataInfo="dataInfo"
        :dataAttributes="dataAttributes" :dataHeaders="dataHeaders" :dataTables="data" :formAttributes="formAttributes"
        :rowDisplay="rowDisplay" @initImport="initImport"></d-table-list> -->
        <div>
            <vs-tabs position="top" class="tabs-shadow-none" id="profile-tabs" :key="isSmallerScreen">
                <!-- 7 -->
                <vs-tab icon-pack="feather" icon="icon-bell" :label="!isSmallerScreen ? 'Assets' : ''">
                    <div class="tab-text md:ml-4 md:mt-0 mt-4 ml-0 pt-2">
                        <d-table-list @clicked="initTableData" :api="api" ref="refInitPage" :allowDel="true" :title="title" :dataInfo="dataInfo"
        :dataAttributes="dataAttributes" :dataHeaders="dataHeaders" :dataTables="data" :formAttributes="formAttributes"
        :rowDisplay="rowDisplay" @initImport="initImport"></d-table-list>
                    </div>
                </vs-tab>
                
                <!-- 3 -->
                <vs-tab icon-pack="feather" icon="icon-lock" :label="!isSmallerScreen ? 'Liabilities' : ''">
                    <div class="tab-text md:ml-4 md:mt-0 mt-4 ml-0 pt-2">
                        <d-table-list @clicked="initTableData" :api="api" ref="refInitPage" :allowDel="true" :title="title" :dataInfo="dataInfo"
        :dataAttributes="dataAttributes" :dataHeaders="dataHeaders" :dataTables="data" :formAttributes="formAttributes"
        :rowDisplay="rowDisplay" @initImport="initImport"></d-table-list>
                    </div>
                </vs-tab>

                <!-- 3 -->
                <vs-tab icon-pack="feather" icon="icon-lock" :label="!isSmallerScreen ? 'Income' : ''">
                    <div class="tab-text md:ml-4 md:mt-0 mt-4 ml-0 pt-2">
                        
                    </div>
                </vs-tab>

                <!-- 3 -->
                <vs-tab icon-pack="feather" icon="icon-lock" :label="!isSmallerScreen ? 'Expense' : ''">
                    <div class="tab-text md:ml-4 md:mt-0 mt-4 ml-0 pt-2">
                        
                    </div>
                </vs-tab>

                <!-- 3 -->
                <vs-tab icon-pack="feather" icon="icon-lock" :label="!isSmallerScreen ? 'Enquity' : ''">
                    <div class="tab-text md:ml-4 md:mt-0 mt-4 ml-0 pt-2">
                        
                    </div>
                </vs-tab>
            </vs-tabs>
        </div>
</template>

<script>
    import axios from "@/axios.js";
    import apiConfig from "@/apiConfig.js";
    import { ref } from 'vue';
    import DTableList from '@/views/form-builder/DTableList.vue';

    export default {
        data() {
            return {
                title: "setting_chart_account",
                api: apiConfig._apiChartAccount,
                dataAttributes: {
                    tableStyle: 2,
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
                    header1: "title",
                    header2: "letter_type",
                    header3: "order_level",
                },
                data: {
                    data: [
                        
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
                        name: "remark",
                        type: "textarea",
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
                                this.dataHeaders = response.data.data_fields; 
                                this.formAttributes = response.data.form_attributes;
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
                                color: 'danger',
                                position: 'top-right'
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
<template>
    <div class="demo-alignment">
        <vs-popup fullscreen classContent="popup-example" :title="title"
            :active.sync="showModalForm">
            <form name="file">
                <input type="file" name="file" @change="selectedFile($event)"/>
                <input type="button" value="Upload" @click="initUploadFile()"/>
            </form>
            
             <!-- <d-table-list @clicked="initTableData" :api="api" ref="refInitPage" :allowDel="true" :title="title" :dataInfo="dataInfo"
        :dataAttributes="dataAttributes" :dataHeaders="dataHeaders" :dataTables="data" :formAttributes="formAttributes"
        :rowDisplay="rowDisplay"></d-table-list> -->
        </vs-popup>
    </div>
</template>

<script>
import axios from "@/axios.js"
import { ref } from 'vue';
import Vue from "vue";
import apiConfig from "@/apiConfig.js"
import DTableList from '@/views/form-builder/DTableList.vue'

export default {
    components: {
        DTableList
    },
    props: {
        // data: {
        //     required: true,
        // }
    },
    data() {
        return {
            showModalForm: false,
            dataInfo: {},
            dataIndicators: [
                {
                    id: "23",
                    code: "12-",
                    indicator_name: "ឈ្មោះសូចនាករ",
                }
            ],
            file: null,

            title: "Upload File",
            api: apiConfig._apiFileManager,
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
                ]
            },
            dataHeaders: {
                header1: "account_type_group_id",
                header2: "name_en",
                header3: "name_kh",
                header4: "order_level",
            },
            data: {
                data: [{}],
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
            dataInfo: {}
        }
    },
    methods: {
        initImport(){
            this.showModalForm = true;
            this.$vs.loading();
            this.getData();
        },
        selectedFile(event) {
  	        console.log(event);
  	        this.file = event.target.files[0];
  	    },
        initUploadFile(){
            let formData = new FormData();
            formData.append('file', this.file);
            for(var pair of formData.entries()) {
                console.log(pair[0]+ ', '+ pair[1]);
            }

            console.log('uploadTodos', formData);
            return new Promise((resolve, reject) => {
                axios.post("/api/v1/file_manager/upload", formData)
                    .then((response) => {
                        if (response.data.message) {
                            this.$vs.notify({
                                title: 'Message',
                                text: response.data.message,
                                iconPack: 'feather',
                                icon: 'icon-check-circle',
                                color: 'primary',
                                position: 'top-right'
                            })
                        } else {
                            this.$vs.notify({
                                title: 'Message',
                                text: response.data.message,
                                iconPack: 'feather',
                                icon: 'icon-check-circle',
                                color: 'danger',
                                position: 'top-right'
                            })
                            this.$vs.loading.close();
                        }
                    }).catch((error) => {
                        reject(error)
                        this.$vs.notify({
                            title: 'Message',
                            text: "មិនអាចដំណើរកាបានទេ,​ សូមត្រួតពិនិត្យពត៌មានឡើងវិញ។",
                            iconPack: 'feather',
                            icon: 'icon-check-circle',
                            color: 'danger',
                            position: 'top-right',
                        })
                        this.$vs.loading.close();
                    })
            })
            // this.$store.dispatch('uploadTodos', formData);
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
                            this.dataHeaders = response.data.data_fields; 
                            this.formAttributes = response.data.form_attributes;
                        } else {
                            this.data = this.data;
                        }
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
        }
    },
    created() {
        // console.log("check data form objects", this.data);
    }
}
</script>
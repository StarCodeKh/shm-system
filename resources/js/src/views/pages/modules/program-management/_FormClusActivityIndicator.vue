<template>
    <vs-popup style="z-index: 52000;" class="d-modal d-modal--xl" classContent="popup-example" :title="$t('indicator')"
        v-model:active="showModalForm">
        <!-- <vx-card title="ទិន្នន័យការងារលម្អិត" class="mb-base">
            <vs-divider />
            <div class="vx-row">
                <d-form @clickForm="initTableData" ref="refModalForm" :data="data" :dataInfo="dataInfo"
                    :formAttributes="formAttributes" :api="api" :rowDisplay="rowDisplay"></d-form>
            </div>
        </vx-card> -->
        <d-table-list @clicked="initTableData" :api="api" ref="refInitPage" :allowDel="true" :title="title"
            :dataInfo="dataInfo" :dataAttributes="dataAttributes" :dataHeaders="dataHeaders" :dataTables="data"
            :formAttributes="formAttributes" :rowDisplay="rowDisplay"></d-table-list>
    </vs-popup>
</template>
<script>
import { gridClass } from "@/formGrid.js";
import axios from "@/axios.js"
import apiConfig from "@/apiConfig.js"
import vSelect from 'vue-select'
import flatPickr from 'vue-flatpickr-component';
import 'flatpickr/dist/flatpickr.css';
import DTableList from '@/views/form-builder/DTableList.vue'
import { ref } from 'vue';

export default {
    props: {
        api: { type: String },
    },
    data() {
        return {
            date: null,
            form: {
                attribute: {}
            },
            showModalForm: false,
            // Data
            title: "indicator",
            // api: apiConfig._apiKPISubProgram,
            dataAttributes: {
                tableStyle: 1,
                page_number: 1,
                offset: 0,
                dataGrid: "row",
                popupFullscreen: true,
                hideSearchBar: false
            },
            dataHeaders: {
                header1: "code",
                header2: "kpi_name_en",
                header3: "kpi_name_kh",
                header4: "order_level",
                // header5: "remark",//"ប្រចាំ/មិនប្រចាំ",
            },
            // dataInfo: {
            //     entity: "entity",
            //     entity_member: "entity_member",
            //     cluster_activity_id: "cluster_activity_id",
            //     activity_id: "activity_id",
            //     name_en: "name_en",
            //     name_kh: "name_kh",
            //     start_date: "start_date",
            //     end_date: "end_date",
            //     assign_to: ""
            // },
            data: {},
            formAttributes: [
                {
                    name: "cluster_activity_id",
                    type: "select",
                    required: true,
                    data: [],
                },
                // {
                //     name: "cluster_activity_id",
                //     type: "select",
                //     required: true,
                //     data: [],
                // },
                // {
                //     name: "activity_id",
                //     type: "select",
                //     required: true,
                //     data: [],
                // },
                {
                    name: "code",
                    type: "text",
                    required: true
                },
                {
                    name: "kpi_name_en",
                    type: "text",
                    required: true,
                    data: [],
                },
                {
                    name: "kpi_name_kh",
                    type: "text",
                    required: true,
                    data: [],
                },
                {
                    name: "order_level",
                    type: "number",
                    required: false,
                },
                {
                    name: "lbl_detail",
                    type: "grid",
                    flag: "y_n_minus_1",
                    required: false,
                },
                {
                    name: "remark",
                    type: "textarea",
                    required: false,
                }
            ],
            rowDisplay: "3grid", //1grid, 2grid, 3grid, 4grid
            dataFields: [],
            dataInfo: {}
        }
    },
    methods: {
        showForm(data) {
            this.dataInfo = data;
            this.showModalForm = true;
            let _params = {
                sort: "",
                order: "",
                page_number: 1,
                search_field: [
                    // This ID will belong to the parent ID of this KPI
                    // data
                ],
                data_info: this.dataInfo
            }

            return new Promise((resolve, reject) => {
                axios.post(this.api + "/get_kpi", _params)
                    .then((response) => {
                        this.$vs.notify({
                            title: 'Message',
                            text: response.data.message,
                            iconPack: 'feather',
                            icon: 'icon-check-circle',
                            color: 'primary',
                            position: 'top-right'
                        })
                        if (response.data.data) {
                            this.data = response.data;
                        } else {
                            this.data = this.data;
                        }
                        this.showModalForm = true;
                    }).catch((error) => {
                        reject(error)
                        this.$vs.notify({
                            title: 'Message',
                            text: 'msg.action_failed',
                            iconPack: 'feather',
                            icon: 'icon-check-circle',
                            color: 'danger',
                            position: 'top-right'
                        })
                        this.$vs.loading.close();
                    })
            })
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
                    data_info: _search_criteria.dataInfo
                };
            } else {
                _params = {
                    sort: _search_criteria.sort,
                    order: _search_criteria.order,
                    page_number: _search_criteria.page_number,
                };
            }

            return new Promise((resolve, reject) => {
                axios.post(this.api + "/get_kpi", _params)
                    .then((response) => {
                        if (response.data) {
                            this.data = response.data;
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
                search_field: searchQuery.searchFields,
                dataInfo: searchQuery.dataInfo
            }
            this.getDataTable(_search_criteria);
            return false;
        }
    },
    created() {
        // this.$vs.loading();
        // this.getData();
        this.styleClass = gridClass(this.rowDisplay);
    },
    computed: {
        isSmallerScreen() {
            return this.$store.state.windowWidth < 768
        }
    },
    components: {
        vSelect,
        DTableList
    },
}
</script>
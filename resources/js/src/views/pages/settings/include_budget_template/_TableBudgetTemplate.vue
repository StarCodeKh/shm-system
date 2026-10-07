<template>
    <vs-popup style="z-index: 52000;" class="d-modal d-modal--xl" classContent="popup-example" :title="$t('តារាងទិន្នន័យពុម្ពថវិកា')"
        :active.sync="showModalForm">
        <!-- <vx-card title="ទិន្នន័យការងារលម្អិត" class="mb-base">
            <vs-divider />
            <div class="vx-row">
                <d-form @clickForm="initTableData" ref="refModalForm" :data="data" :dataInfo="dataInfo"
                    :formAttributes="formAttributes" :api="api" :rowDisplay="rowDisplay"></d-form>
            </div>
        </vx-card> -->
        <d-table-list v-if="showTblList" @clicked="initTableData" :api="api" ref="refInitPage" :allowDel="true" :title="title"
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
        // api: { type: String },
        // dataInfo: { required: true },
        childrenAttributes: {required: true}
    },
    components: {
        vSelect,
        DTableList
    },
    data() {
        return {
            showTblList: false,
            date: null,
            columnField: "sub_program_id",
            form: {
                attribute: {}
            },
            showModalForm: false,
            // Data
            api: this.childrenAttributes.api,
            title: this.childrenAttributes.title,
            dataAttributes: this.childrenAttributes.dataAttributes,
            dataHeaders: {
                header12222: "budget_template_id",
                header111: "account_group",//"ប្រភេទ​តារាងតម្លៃ",
                header1: "account",//"ប្រភេទ​តារាងតម្លៃ",
                header2: "sub_account",//"តារាង​តម្លៃ",
                header22: "code",//"តារាង​តម្លៃ",
                //header3: "title_en",//"ចំណងជើង",
                header4: "name_kh",//"ចំណងជើង",
                header5: "is_reg_exp",//"ប្រចាំ/មិនប្រចាំ",
                header6: "qty",//"ចំនួន",
                header55: "unit",//"តម្លៃ១លើក",
                header7: "unit_price", //តម្លៃ/ឯកតា
                header8: "total_amount", // តម្លៃសរុប
                header9: "time_annual",//"លើក/ឆ្នាំ",
                header10: "total_annual_amount",//"សរុបប្រចាំឆ្នាំ",
                header11: "month",//"ខែ",
                header12: "expense_type",//"មុខសញ្ញា",
                // header13: "remark",//"ផ្សេងៗ",
            },
            formAttributes: [
                {
                    name: "budget_template_id",
                    type: "hidden",
                    required: true
                },
                {
                    name: "account_group_id",
                    type: "select",
                    required: true,
                    hasFilter: true,
                    filterObj: "account_id",
                    options: [],
                    api: apiConfig._apiGetAccountByGroup,
                },
                {
                    name: "account_id",
                    type: "select",
                    required: true,
                    hasFilter: true,
                    filterObj: "sub_account_id",
                    options: [],
                    api: apiConfig._apiGetSubAccount,
                },
                {
                    name: "sub_account_id",
                    type: "select",
                    required: true,
                    hasFilter: false,
                },
                {
                    name: "code",
                    type: "text",
                    required: true,
                },
                {
                    name: "name_en",
                    type: "text",
                    required: true,
                },
                {
                    name: "name_kh",
                    type: "text",
                    required: true,
                },
                {
                    name: "lbl_detail",
                    type: "grid",
                    flag: "y_n_minus_1",
                    required: false,
                },
                {
                    name: "unit_id",
                    type: "select",
                    required: true,
                },
                {
                    name: "qty",
                    type: "number",
                    required: true,
                },
                {
                    name: "unit_price",
                    type: "number",
                    required: true,
                },
                {
                    name: "time_annual",
                    type: "number",
                    required: true,
                },
                {
                    name: "is_reg_exp",
                    type: "select",
                    required: true,
                },
                {
                    name: "misc",
                    type: "textarea",
                    required: false,
                }
            ],
            rowDisplay: "3grid", //1grid, 2grid, 3grid, 4grid
            data: {},
            dataFields: [],
            dataInfo: {}
        }
    },
    methods: {
        showForm(data) {
            // console.log("data info", data);
            this.showTblList = true;
            this.dataInfo = data;
            if(this.showTblList){
                const _params = {
                    sort: "",
                    order: "",
                    page_number: 1,
                    search_field: [],
                    data_info: this.dataInfo
                }

                // return new Promise((resolve, reject) => {
                //     axios.get(this.api , {
                //             params: _params
                //             }).then((response) => {
                //             this.$vs.notify({
                //                 title: 'Message',
                //                 text: response.data.message,
                //                 iconPack: 'feather',
                //                 icon: 'icon-check-circle',
                //                 color: 'primary',
                //                 position: 'top-right'
                //             })
                //             if (response.data.data) {
                //                 this.data = response.data;
                //             } else {
                //                 this.data = this.data;
                //             }
                //             this.showModalForm = true;
                //         }).catch((error) => {
                //             reject(error)
                //             this.$vs.notify({
                //                 title: 'Message',
                //                 text: 'msg.action_failed',
                //                 iconPack: 'feather',
                //                 icon: 'icons-check-circle',
                //                 color: 'danger',
                //                 position: 'top-right'
                //             })
                //             this.$vs.loading.close();
                //         })
                // })
                
                return new Promise((resolve, reject) => {
                    axios.post(this.api + "/get_item_cost", _params)
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
            }
        },
        getDataTable(_search_criteria) {
            if(this.showTblList){
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
                        data_info: _search_criteria.dataInfo
                    };
                }

                return new Promise((resolve, reject) => {
                    axios.post(this.api + "/get_item_cost", _params)
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
                    text: 'msg.action_failed',
                    iconPack: 'feather',
                    icon: 'icon-check-circle',
                    color: 'danger'
                  })
                            this.$vs.loading.close();
                        })
                })
            }
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
        this.getData();
        this.styleClass = gridClass(this.rowDisplay);
    },
    computed: {
        isSmallerScreen() {
            return this.$store.state.windowWidth < 768
        }
    }
}
</script>
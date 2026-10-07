<template>
    <vs-popup class="d-modal d-modal--xl" classContent="popup-example" title="បញ្ជីគ្រោងថវិកាចំណាយ" :active.sync="showModalForm">
        <vx-card title="ទិន្នន័យការងារលម្អិត" class="mb-base">
            <vs-divider />
            <!-- Avatar -->
            <div class="vx-row">
                <!-- Form -->
                <!-- <d-form @clickForm="initTableData" ref="refModalForm" :data="data" :dataInfo="dataInfo"
                    :formAttributes="formAttributes" :api="api" :rowDisplay="rowDisplay"></d-form> -->

                <!-- <div class="vx-col flex-1" id="account-info-col-1">
                    <table>
                        <tr>
                            <td width="200px" class="font-semibold">{{ $t("entity") }} :</td>
                            <td>{{ dataInfo.entity }} អគ្គនាយកដ្ឋានគោលនយោបាយ</td>
                        </tr>
                        <tr>
                            <td width="200px" class="font-semibold">{{ $t("entity_member") }} : </td>
                            <td>{{ dataInfo.entity_member }} មន្ត្រីបង្គោល</td>
                        </tr>
                        <tr>
                            <td width="200px" class="font-semibold">{{ $t("cluster_activity_id") }} : </td>
                            <td>{ dataInfo.cluster_activity_id.label }}</td>
                        </tr>
                        <tr>
                            <td width="200px" class="font-semibold">{{ $t("activity_id") }} : </td>
                            <td>{ dataInfo.activity_id.label }}</td>
                        </tr>
                    </table>
                </div> -->

                <!-- Information - Col 2 -->
                <!-- <div class="vx-col flex-1" id="account-info-col-2">
                    <table>
                        <tr>
                            <td width="200px" class="font-semibold">{{ $t("code") }} : </td>
                            <td>{{ dataInfo.code }}</td>
                        </tr>
                        <tr>
                            <td width="200px" class="font-semibold">{{ $t("name_kh") }} : </td>
                            <td>{{ dataInfo.name_kh }}</td>
                        </tr>
                        <tr>
                            <td width="200px" class="font-semibold">{{ $t("name_en") }} : </td>
                            <td>{{ dataInfo.name_en }}</td>
                        </tr>
                        <tr>
                            <td width="200px" class="font-semibold">{{ $t("start_date") }} : </td>
                            <td>{{ dataInfo.start_date }}</td>
                        </tr>
                        <tr>
                            <td width="200px" class="font-semibold">{{ $t("end_date") }} : </td>
                            <td>{{ dataInfo.end_date }}</td>
                        </tr>
                    </table>
                </div> -->
                <!-- /Information - Col 2 -->
                <!-- <div class="vx-col w-full flex" id="account-manage-buttons">
                    <vs-button icon-pack="feather" icon="icon-edit" class="mr-4"
                        :to="{ name: 'app-user-edit', params: { userId: $route.params.userId } }">Edit</vs-button>
                    <vs-button type="border" color="danger" icon-pack="feather" icon="icon-trash"
                        @click="confirmDeleteRecord">Delete</vs-button>
                </div> -->
            </div>
        </vx-card>
        <FormCostingDetail ref="refOpenPrivatePopupForm" :api="dataAttributes.api" :dataAttributes="dataAttributes"
            :dataInfo="dataInfo" />
        <d-table-list @clicked="initTableData" :api="api" ref="refInitPage" :allowDel="true" :title="title"
            :dataInfo="dataInfo" :dataAttributes="dataAttributes" :dataHeaders="dataHeaders" :dataTables="data"
            :formAttributes="formAttributes" :rowDisplay="rowDisplay" @clickPrivateForm="initOpenForm"></d-table-list>
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
import FormCostingDetail from '@/views/pages/modules/program-management/_FormCostingDetail.vue';
import { ref } from 'vue';

export default {
    props: {
        api: { type: String },
    },
    data() {
        return {
            date: null,
            program_id: "",
            sub_program_id: "",
            entity_id: "",
            form: {
                attribute: {}
            },
            showModalForm: true,
            // Data
            title: "plan_budget",
            dataAttributes: {
                tableStyle: 2,
                page_number: 1,
                offset: 0,
                dataGrid: "row",
                popupFullscreen: true,
                hideSearchBar: false
            },
            dataInfo: {
                entity: "entity",
                entity_member: "entity_member",
                cluster_activity_id: "cluster_activity_id",
                activity_id: "activity_id",
                name_en: "name_en",
                name_kh: "name_kh",
                start_date: "start_date",
                end_date: "end_date",
                assign_to: ""
            },
            dataHeaders: {
                header1: "account_code",//"ប្រភេទ​តារាងតម្លៃ",
                header2: "sub_account_code",//"តារាង​តម្លៃ",
                header22: "line_no",//"តារាង​តម្លៃ",
                //header3: "title_en",//"ចំណងជើង",
                header34: "name_kh",//"ចំណងជើង",
                header4: "name_en",//"ចំណងជើង",
                header5: "flag_type",//"ប្រចាំ/មិនប្រចាំ",
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
            data: {},
            formAttributes: [
                // {
                //     name: "sub_program_id",
                //     type: "select",
                //     required: true,
                //     data: [],
                // },
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
                    name: "cluster_activity_id",
                    type: "select",
                    required: true,
                    hasFilter: true,
                    filterObj: "activity_id",
                    api: apiConfig._apiGetActivity,
                },
                {
                    name: "activity_id",
                    type: "select",
                    required: true,
                    hasFilter: true,
                    filterObj: "task_id",
                    api: apiConfig._apiGetTaskByAct,
                },
                {
                    name: "task_id",
                    type: "select",
                    required: true,
                    data: [],
                },
                {
                    name: "code",
                    type: "text",
                    required: true,
                    data: [],
                },
                {
                    name: "name_en",
                    type: "text",
                    required: true,
                    data: [],
                },
                {
                    name: "name_kh",
                    type: "text",
                    required: true,
                    data: [],
                },
                {
                    name: "order_level",
                    type: "number",
                    required: false,
                    data: [],
                },
                {
                    name: "remark",
                    type: "textarea",
                    required: false,
                    data: [],
                },
                // {
                //     name: "lbl_detail",
                //     type: "grid",
                //     flag: "y_n_minus_1",
                //     required: false,
                // },
                // {
                //     name: "unit_id",
                //     type: "select",
                //     required: false,
                // },
                // {
                //     name: "quantity",
                //     type: "number",
                //     required: false,
                // },
                // {
                //     name: "unit_price",
                //     type: "number",
                //     required: false,
                // },
                // {
                //     name: "time_annual",
                //     type: "number",
                //     required: false,
                // },
                // {
                //     name: "is_reg_exp",
                //     type: "select",
                //     required: false,
                // },
                // {
                //     name: "misc",
                //     type: "textarea",
                //     required: false,
                // }
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
                axios.post(this.api, _params)
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
                axios.post(this.api, _params)
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
        },
        initOpenForm(data) {
            this.$refs.refOpenPrivatePopupForm.showForm(data);
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
        FormCostingDetail,
        vSelect,
        DTableList
    },
}
</script>
<template>
    <vs-popup fullscreen classContent="popup-example" title="បញ្ជីគ្រោងថវិកាចំណាយ" :active.sync="showModalForm">
        <vx-card title="ទិន្នន័យការងារលម្អិត" class="mb-base">
            <vs-divider />
            <!-- Avatar -->
            <div class="vx-row">

                <div class="vx-col flex-1" id="account-info-col-1">
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
                            <!-- <td>{ dataInfo.cluster_activity_id.label }}</td> -->
                        </tr>
                        <tr>
                            <td width="200px" class="font-semibold">{{ $t("activity_id") }} : </td>
                            <!-- <td>{ dataInfo.activity_id.label }}</td> -->
                        </tr>
                    </table>
                </div>

                <!-- Information - Col 2 -->
                <div class="vx-col flex-1" id="account-info-col-2">
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
                        <!-- <tr>
                            <td width="200px" class="font-semibold">{{ $t("assign_to") }} : </td>
                            <td>{{ dataInfo.assign_to }}</td>
                        </tr> -->
                    </table>
                </div>
                <!-- /Information - Col 2 -->
                <!-- <div class="vx-col w-full flex" id="account-manage-buttons">
                    <vs-button icon-pack="feather" icon="icon-edit" class="mr-4"
                        :to="{ name: 'app-user-edit', params: { userId: $route.params.userId } }">Edit</vs-button>
                    <vs-button type="border" color="danger" icon-pack="feather" icon="icon-trash"
                        @click="confirmDeleteRecord">Delete</vs-button>
                </div> -->
            </div>
        </vx-card>

        <d-table-list @clicked="initTableData" :api="api" ref="refInitPage" :allowDel="true" :title="title"
            :dataInfo="dataInfo" :dataAttributes="dataAttributes" :dataHeaders="dataHeaders" :dataTables="data"
            :formAttributes="formAttributes" :rowDisplay="rowDisplay"></d-table-list>
    </vs-popup>
</template>
<script>
import axios from "@/axios.js"
import apiConfig from "@/apiConfig.js"
import DFormInput from '@/views/form-builder/form-control/DFormInput.vue'
import vSelect from 'vue-select'
import flatPickr from 'vue-flatpickr-component';
import 'flatpickr/dist/flatpickr.css';
import DTableList from '@/views/form-builder/DTableList.vue'
import { ref } from 'vue';

export default {
    data() {
        return {
            date: null,
            program_id: "",
            sub_program_id: "",
            entity_id: "",
            form: {
                attribute: {}
            },
            showModalForm: false,
            // Data
            title: "ថវិកាគ្រោង",
            api: apiConfig._apiGetTaskDetail,
            dataAttributes: {
                tableStyle: 1,
                page_number: 1,
                offset: 0,
                dataGrid: "row",
                popupFullscreen: true,
                hideSearchBar: true
            },
            dataHeaders: {
                header1: "account_code",//"ប្រភេទ​តារាងតម្លៃ",
                header2: "sub_account_code",//"តារាង​តម្លៃ",
                header22: "line_no",//"តារាង​តម្លៃ",
                //header3: "title_en",//"ចំណងជើង",
                header4: "title_kh",//"ចំណងជើង",
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
            data: {
                // data:
                //     [
                //         {
                //             account_code: "001",
                //             sub_account_code: "0011",
                //             title_en: "ចំណងជើងជាភាសាខ្មែរ",
                //             title_kh: "ចំណងជើងជាភាសាអង់គ្លេស",
                //             flag_type: "flag_type",
                //             qty: "qty",
                //             unit_price: "unit_price",
                //             total_amount: "total_amount",
                //             time_annual: "time_annual",
                //             total_annual_amount: "total_annual_amount",
                //             month: "month",
                //             expense_type: "expense_type",
                //             remark: "remark",
                //         }
                //     ],
            },
            formAttributes: [
                {
                    name: "account_id",
                    type: "select",
                    required: true,
                    data: [
                        {
                            label: "គណនី",
                            value: "1",
                        },
                        {
                            label: "គណនី",
                            value: "1",
                        },
                    ],
                },
                {
                    name: "sub_account_id",
                    type: "select",
                    required: true,
                    data: [
                        {
                            label: "អនុគណនី",
                            value: "1",
                        },
                        {
                            label: "អនុគណនី",
                            value: "1",
                        },
                    ],
                },
                {
                    name: "flag_type",
                    type: "select",
                    required: true,
                    data: [
                        {
                            label: 1,
                            label: "ប្រចាំ"
                        }, {
                            label: 1,
                            label: "មិនប្រចាំ"
                        }
                    ],
                },
                {
                    name: "expense_type",
                    type: "select",
                    required: true,
                    data: [
                        {
                            label: 1,
                            label: "ប្រភេទចំណាយ"
                        }, {
                            label: 1,
                            label: "ប្រភេទចំណាយ"
                        }
                    ],
                },
                {
                    name: "title_kh",
                    type: "text",
                    required: true,
                },
                {
                    name: "title_en",
                    type: "text",
                    required: true,
                },
                {
                    name: "unit",
                    type: "select",
                    required: false,
                    data: [
                        {
                            label: 1,
                            label: "ឆ្នាំ"
                        }, {
                            label: 2,
                            label: "ខែ"
                        }, {
                            label: 3,
                            label: "ត្រីមាស"
                        }
                    ],
                },
                {
                    name: "quantity",
                    type: "number",
                    required: false
                },
                {
                    name: "unit_price",
                    type: "number",
                    required: false
                },
                {
                    name: "time_peryear",
                    type: "number",
                    required: false
                },
                {
                    name: "remark",
                    type: "textarea",
                    required: false
                }
            ],
            rowDisplay: "3grid", //1grid, 2grid, 3grid, 4grid
            dataFields: [],
            dataInfo: {}
        }
    },
    methods: {
        showForm(data) {
            console.log("form data", data);
            this.dataInfo = data;
            let _param = { task_id: 1 };
            return new Promise((resolve, reject) => {
                axios.post(this.api, _param)
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
                            // console.log("my", response.data.data);
                            this.data = response.data.data;
                        } else {
                            alert("no");
                            this.data = this.data;
                        }
                        this.showModalForm = true;
                        // this.$emit('clickForm');
                        // this.$router.push('/account/expense').catch(() => { })
                    }).catch((error) => {
                        reject(error)
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
        submitForm() {
            this.dataFields = [];
            let _formAttribute = this.formAttributes;
            this.formAttributes.forEach(_formAttribute => {
                let _d = {
                    [_formAttribute["name"]]: this.form.attribute[_formAttribute["name"]]
                }
                this.dataFields.push(_d);
            });
            // for (let i = 0; i < this.form.attribute.length > 0; i++) {
            //     let _d = {
            //         [this.form.attribute[i]]: this.form.attribute[_formAttribute["name"]]
            //     }
            //     this.dataFields.push(_d);
            // }
            // console.log("Here is form data===>", this.dataFields);
            // return false;
            this.$validator.validateAll().then(result => {
                if (result) {
                    let _data = this.dataFields;
                    // this.$vs.loading();
                    if (this.dataInfo.id) {
                        // alert("Edit");
                        let _id = this.dataInfo.id;
                        // this.$vs.loading.close();
                        return new Promise((resolve, reject) => {
                            axios.put(this.api + '/' + _id, _data)
                                .then((response) => {
                                    this.$vs.notify({
                                        title: 'Message',
                                        text: response.data.message,
                                        iconPack: 'feather',
                                        icon: 'icon-check-circle',
                                        color: 'primary',
                                        position: 'top-right'
                                    })
                                    this.$emit('clickForm');
                                    // this.$router.push('/account/expense').catch(() => { })
                                }).catch((error) => {
                                    reject(error)
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
                    } else {
                        return new Promise((resolve, reject) => {
                            axios.post(this.api, _data)
                                .then((response) => {
                                    this.$vs.notify({
                                        title: 'Message',
                                        text: response.data.message,
                                        iconPack: 'feather',
                                        icon: 'icon-check-circle',
                                        color: 'primary',
                                        position: 'top-right'
                                    })
                                    // this.$emit('clickForm');
                                    // this.$router.push('/account/expense').catch(() => { })
                                }).catch((error) => {
                                    reject(error)
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
                    }

                }
                this.$vs.loading.close();
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
                search_field: searchQuery.searchFields
            }
            this.getDataTable(_search_criteria);
            return false;
        }
    },
    created() {
        // this.$vs.loading();
        // this.getData();

        if (this.rowDisplay == "1grid") {
            this.styleClass = "vx-col lg:w-1/1 w-full mt-4";
        }
        if (this.rowDisplay == "2grid") {
            this.styleClass = "vx-col lg:w-1/2 w-full mt-4";
        }
        if (this.rowDisplay == "3grid") {
            this.styleClass = "vx-col lg:w-1/3 w-full mt-4";
        }
        if (this.rowDisplay == "4grid") {
            this.styleClass = "vx-col lg:w-1/4 w-full mt-4";
        }
        if (this.rowDisplay == "full-grid") {
            this.styleClass = "vx-col lg:w-1/1 w-full";
        }
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
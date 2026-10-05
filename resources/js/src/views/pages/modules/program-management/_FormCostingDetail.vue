<template>
    <vs-popup class="d-modal d-modal--xl" classContent="popup-example" title="បញ្ជីគ្រោងថវិកាចំណាយ" :active.sync="showModalForm">
        <!-- <vx-card title="ទិន្នន័យការងារលម្អិត" class="mb-base">
            <vs-divider />
            <div class="vx-row">
                <d-form @clickForm="initTableData" ref="refModalForm" :data="data" :dataInfo="dataInfo"
                    :parentDataInfo="parentDataInfo" :formAttributes="formAttributes" :api="api"
                    :rowDisplay="rowDisplay" />
            </div>
        </vx-card> -->
        <vx-card :title="''" code-toggler>
            <div class="p-3 mb-4" style="overflow: scroll;">
                <vs-table style="min-width: 500px;min-width: 200%;">
                    <template slot="thead" style="background: #28c76f;">
                        <vs-th>{{ $t("ល.រ") }}</vs-th>
                        <vs-th>{{ $t("គណនី") }}</vs-th>
                        <vs-th>{{ $t("អនុគណនី") }}</vs-th>
                        <vs-th>{{ $t("លេខ") }}</vs-th>
                        <vs-th>{{ $t("ឈ្មោះ") }}</vs-th>
                        <vs-th>{{ $t("ប្រចាំ/មិនប្រចាំ") }}</vs-th>
                        <vs-th>{{ $t("ចំនួន") }}</vs-th>
                        <vs-th>{{ $t("ឯកតា/រង្វាស់") }}</vs-th>
                        <vs-th>{{ $t("ចំនួនសរុប") }}</vs-th>
                        <vs-th>{{ $t("លើក/ឆ្នាំ") }}</vs-th>
                        <vs-th>{{ $t("សរុបប្រចាំឆ្នាំ") }}</vs-th>
                        <vs-th>{{ $t("ខែ") }}</vs-th>
                        <vs-th>{{ $t("ប្រភេទចំណាយតាមមុខសញ្ញា") }}</vs-th>
                        <vs-th>{{ $t("Action") }}</vs-th>
                    </template>
                    <tbody>
                        <tr v-for="(  ptr, index  ) in attribute">
                            <td> <b>{{ index + 1 }}</b></td>
                            <td>
                                <vs-select
                                    :key="index" v-on:input="onInitChange('sub_account_id', index)"
                                    name="" v-model="form.attribute['account_id'][index]" :dir="$vs.rtl ? 'rtl' : 'ltr'" v-validate="'required'" class="mt-1 w-full">
                                    <vs-select-item :key="index" :value="item.value" :text="item.label"
                                        v-for="(item, index) in dataAccounts" />
                                </vs-select>
                            </td>
                            <td>
                                {{ 'sub_account_id'+index }}
                                <vs-select :key="index" name="" v-model="form.attribute['sub_account_id'][index]" :dir="$vs.rtl ? 'rtl' : 'ltr'" v-validate="'required'" class="mt-1 w-full">
                                    <vs-select-item :key="index" :value="item.value" :text="item.label"
                                        v-for="(item, i) in dataSubAccounts.sub_account_id[index]" />
                                </vs-select>
                            </td>
                            <td style="width: 200px">
                                <vs-input :key="index" type="number" v-model="form.attribute['no'][index]" name="attribute[a][index]" v-validate="'required'" size="small"
                                    class="w-full" />
                            </td>
                            <td style="width: 200px">
                                <vs-input :key="index" type="number" v-model="form.attribute['name'][index]" name="attribute[a][index]" v-validate="'required'" size="small"
                                    class="w-full" />
                            </td>
                            <!-- <td style="width: 200px">
                                <vs-input type="number" name="attribute[a][index]" size="small" class="w-full" />
                            </td>
                            <td style="width: 200px">
                                <vs-input type="number" name="attribute[a][index]" size="small" class="w-full" />
                            </td>
                            <td style="width: 200px">
                                <vs-input type="number" name="attribute[a][index]" size="small" class="w-full" />
                            </td>
                            <td style="width: 200px">
                                <vs-input type="number" name="attribute[a][index]" size="small" class="w-full" />
                            </td>
                            <td style="width: 200px">
                                <vs-input type="number" name="attribute[a][index]" size="small" class="w-full" />
                            </td>
                            <td style="width: 200px">
                                <vs-input type="number" name="attribute[a][index]" size="small" class="w-full" />
                            </td>
                            <td style="width: 200px">
                                <vs-input type="number" name="attribute[a][index]" size="small" class="w-full" />
                            </td>
                            <td style="width: 200px">
                                <vs-input v-validate="'required'" size="small" class="mb-2 pb-2 w-full" />
                            </td> -->
                            <td><button type="button" @click="removeElement(index)">Remove</button></td>
                        </tr>
                        <tr>
                            <td><button type="button" @click="addElement()">Add</button></td>
                            <td colspan="4"></td>
                            <td><button type="button" @click="addElement()">Add</button></td>
                        </tr>
                    </tbody>
                </vs-table>
            </div>

            <div class="my-5">
                <!-- <vs-pagination :total="calPaginNumber(dataTables.total / dataTables.limit)" name="current_page"></vs-pagination> -->
            </div>

        </vx-card>
        <!-- <d-table-list @clicked="initTableData" :api="api" ref="refInitPage" :allowDel="true" :title="title"
            :dataInfo="dataInfo" :dataAttributes="dataAttributes" :dataHeaders="dataHeaders" :dataTables="data"
            :formAttributes="formAttributes" :rowDisplay="rowDisplay" @clickPrivateForm="initOpenForm"></d-table-list> -->
    </vs-popup>
</template>

<script>
    import { gridClass } from "@/formGrid.js";
    import axios from "@/axios.js"
    import apiConfig from "@/apiConfig.js"
    import DForm from '@/views/form-builder/DForm.vue'
    import DFormInput from '@/views/form-builder/form-control/DFormInput.vue'
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
                program_id: "",
                sub_program_id: "",
                entity_id: "",
                form: {
                    attribute: {}
                },
                showModalForm: true,
                // Data
                title: "plan_budget",
                dataAccounts: [
                    {
                        label: "test",
                        value: "1"
                    },
                    {
                        label: "test",
                        value: "1"
                    },
                ],
                dataSubAccounts: {
                    sub_account_id: {

                    }
                },
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
                attribute: [
                    {
                        a: "",
                        b: "",
                        c: "",
                        d: "",
                        e: "",
                        f: "",
                        g: "",
                        h: "",
                        i: "",
                        j: "",
                        k: "",
                    }
                ],
                data: {},
                formAttributes: [
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
                console.log("testing", _params);
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
            },
            addElement() {
                let _attribute = {
                    a: "",
                    b: "",
                    c: "",
                    d: "",
                    e: "",
                    f: "",
                    g: "",
                    h: "",
                    i: "",
                    j: "",
                    k: "",
                };
                this.attribute.push(_attribute);
            },
            removeElement(index) {
                this.attribute.splice(index, 1);
            },
            onInitChange(_formAttribute, index) {
                // alert(_formAttribute);
                // let _data = {
                //     type: "autocomplete",
                //     param: this.form.attribute[_formName]
                // };
                // this.form.attribute[_formAttribute] = null;
                this.dataSubAccounts.sub_account_id[index] = {
                    "label": "sdf",
                    "value": "1"
                }
                console.log("test===>", _formAttribute);
                // this.dataSubAccounts[_formAttribute] = [];
                // this.dataSubAccounts[_formAttribute].push([
                //     {
                //         "label": "sdf",
                //         "value": "1"
                //     }
                // ]);
                // return new Promise((resolve, reject) => {
                //     axios.post(_apiRequest, _data)
                //         .then((response) => {
                //             this.$vs.notify({
                //                 title: 'Message',
                //                 text: "Loading...",
                //                 iconPack: 'feather',
                //                 icon: 'icon-check-circle',
                //                 color: 'primary',
                //                 position: 'top-right'
                //             });
                //             console.log(response["data"]["data"]);
                //             for (let i = 0; i < response["data"]["data"].length; i++) {
                //                 // let _d = {
                //                 //     "label": response["data"]["data"][i]["label"],
                //                 //     "value": response["data"]["data"][i]["value"],
                //                 // }
                //                 // this.data[_formAttribute].push(_d);
                //                 this.data[_formAttribute].push(response["data"]["data"][i]);
                //             }
                //         }).catch((error) => {
                //             reject(error)
                //             this.$vs.notify({
                //                 title: 'Message',
                //                 text: "Error, while trying to do action or no permission allow!",
                //                 iconPack: 'feather',
                //                 icon: 'icon-check-circle',
                //                 color: 'danger',
                //                 position: 'top-right'
                //             })
                //             this.$vs.loading.close();
                //         })
                // })
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
            DForm,
            vSelect,
            DTableList
        },
    }
</script>
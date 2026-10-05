 <!-- ###### If data attribute has indicator mean can be able to allow create form data  -->
<template>
    <div class="mt-6" v-if="dataInfo.indicator.data">
        <h3>{{ $t("indicator") }}</h3>
        <!-- ADD NEW -->
        <vs-popup v-if="dataAttributes.popupFullscreen" fullscreen classContent="popup-example" :title="title"
            :active.sync="showModalForm">
            <d-form @clickForm="initTableData" ref="refModalForm" :data="data" :dataInfo="dataInfo"
                :formAttributes="formAttributes" :api="api" :rowDisplay="rowDisplay"></d-form>\
        </vs-popup>
        <vs-popup v-else classContent="popup-example" :title="title" :active.sync="showModalForm">
            <d-form @clickForm="initTableData" ref="refModalForm" :data="data" :dataInfo="dataInfo"
                :formAttributes="formAttributes" :api="api" :rowDisplay="rowDisplay"></d-form>\
        </vs-popup>

        <div v-if="!dataInfo.indicator || dataInfo.indicator"
            class="flex flex-wrap-reverse items-center data-list-btn-container">
            <div class="btn-add-new p-3 mb-4 mr-4 rounded-lg cursor-pointer flex items-center justify-center text-lg font-medium text-base text-primary border border-solid border-primary"
                @click="openForm">
                <feather-icon icon="PlusIcon" svgClasses="h-4 w-4" />
                <span class="ml-2 text-base text-primary">{{ $t("AddNew") }}</span>
            </div>
        </div>

        <!-- <template v-if="showIndicatorForm">
            <d-form @clickForm="initTableData" ref="refModalForm" :data="data" :dataInfo="dataInfo"
                :formAttributes="formAttributes" :api="api" :rowDisplay="rowDisplay"></d-form>
        </template> -->

        <!-- <vs-table v-if="dataInfo.indicator" :data="dataInfo.indicator.data"> -->
        <vs-table>
            <template slot="thead">
                <vs-th style="background-color: #28C76F; color: #ffffff; font-weight: bold;">{{ $t("no") }}</vs-th>
                <vs-th style="background-color: #28C76F; color: #ffffff; font-weight: bold;" :key="i"
                    v-for="(header, i) in dataHeaders">{{ $t(header) }}</vs-th>
                <vs-th style="background-color: #28C76F; color: #ffffff; font-weight: bold;">{{ $t("Action") }}</vs-th>
            </template>
            <template slot-scope="{data}" v-if="dataInfo.indicator.data.length > 0">
                <template>
                    <vs-tr :key="indextr" v-for="(tr, indextr) in data">
                        <vs-td>{{ indextr + 1 }}</vs-td>
                        <vs-td v-for="header in dataHeaders" :key="header.indextr">{{ tr[header] }}</vs-td>
                        <vs-td>
                            <template v-if="dataAttributes.actionButton">
                                <feather-icon style="cursor: pointer;" v-for="rowBtnAction in dataAttributes.actionButton"
                                    :key="rowBtnAction.indextr" :icon="rowBtnAction.icon"
                                    svgClasses="mr-2 w-5 h-5 hover:text-primary stroke-current"
                                    @click.stop="viewUrl(rowBtnAction)" />
                            </template>
                            <feather-icon style="cursor: pointer;" icon="EditIcon"
                                svgClasses="w-5 h-5 hover:text-primary stroke-current" @click.stop="initEdit(tr)" />
                            <feather-icon style="cursor: pointer;" v-if="dataAttributes.allowDel" icon="TrashIcon"
                                svgClasses="w-5 h-5 hover:text-danger stroke-current" class="ml-2"
                                @click.stop="initDel(tr.id)" />
                        </vs-td>
                    </vs-tr>
                </template>
                <!-- <vs-tr>
                    <vs-td :colspan="(Object.keys(data[0]).length)"></vs-td>
                    <vs-td>
                        <feather-icon style="cursor: pointer;" icon="PlusIcon" @click="addRow()"
                            svgClasses="mr-2 w-5 h-5 hover:text-primary stroke-current" /> {{ $t("button_add_more") }}
                    </vs-td>
                </vs-tr> -->
            </template>
            <!-- <template v-else>
                <vs-tr>
                    <vs-td :colspan="(Object.keys(data[0]).length)"></vs-td>
                    <vs-td>
                        <feather-icon style="cursor: pointer;" icon="PlusIcon" @click="addRow()"
                            svgClasses="mr-2 w-5 h-5 hover:text-primary stroke-current" /> {{ $t("button_add_more") }}
                    </vs-td>
                </vs-tr>
            </template> -->
        </vs-table>
    </div>
</template>

<script>
import axios from "@/axios.js";
import apiConfig from "@/apiConfig.js";
import { ref } from 'vue';
import DForm from '@/views/form-builder/DForm.vue';

export default {
    props: {
        dataInfo: {
            required: true,
        },
        dataAttributes: {
            required: true
        },
    },
    data() {
        return {
            title: "inidicator",
            api: apiConfig._apiIndicatorSubPro,
            dataHeaders: {
                header1: "code",
                header2: "kpi_name_en",
                header3: "kpi_name_kh",
                header4: "order_level",
                header5: "status"
            },
            showModalForm: false,
            data: {
                data: [{}],
                limit: 10,
                total: 3
            },
            formAttributes: [
                {
                    name: "code",
                    type: "text",
                    required: true
                },
                {
                    name: "kpi_name_en",
                    type: "text",
                    required: true
                },
                {
                    name: "kpi_name_kh",
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
                }
            ],
            rowDisplay: "3grid", //1grid, 2grid, 3grid, 4grid
            dataFields: [],

            dataIndicators: {},
            dataTables: {},
            showIndicatorForm: false
        }
    },
    components: {
        DForm
    },
    methods: {
        addRow() {
        },
        openIndicatorForm() {
            this.showIndicatorForm = true;
        },
        openForm() {
            this.showModalForm = true;
            // this.$refs.refModalForm.openNewForm();
            this.$emit("emitDataForm", "test change form attribute");
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
                        // this.data = response.data;
                        this.data = this.data;
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
        // console.log("found data", this.dataInfo);
        // this.dataIndicators = this.dataInfo.indicator;
        // this.$vs.loading();
        // this.getData();
    },
    watch: {
    }
}
</script>
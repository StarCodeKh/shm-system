<template>
    <div class="demo-alignment">
        <vs-popup fullscreen classContent="popup-example" :title="title"
            :active.sync="showModalForm">
            <form name="file">
                <input type="file" name="file" @change="selectedFile($event)"/>
                <input type="button" value="Upload" @click="initUploadFile()"/>
            </form>
            
            <div class="flex flex-wrap items-center justify-between">
                <div class="mr-3">
                    <h3>{{ $t(title) }}</h3>
                </div>

                <div class="items-center pt-4 pb-3">
                    <div class="vx-row mr-1">
                        <div @click="isHidden = !isHidden" class="btn-add-new p-3 mr-2 rounded-sm cursor-pointer text-right flex items-center justify-center
                            text-lg font-medium text-base text-primary border border-solid border-default">
                            <feather-icon icon="FilterIcon" svgClasses="h-4 w-4" />
                        </div>
                    </div>
                </div>
            </div>

            <template v-if="!isHidden">
                <vx-card>
                    <h3 class="pb-4">{{ $t('text_search') }}</h3>
                    <div class="vx-row">
                        <template v-for="(formAttribute, i) in formAttributes">
                            <!-- Form Input Text -->
                            <div v-if="formAttribute.type == 'text' && formAttribute.required" :class="styleClass" dclass="mt-4">
                                <label>{{ $t(formAttribute.name) }}</label>
                                <vs-input :placeholder="$t(formAttribute.name)" v-model="form.attribute[formAttribute.name]"
                                    :name="formAttribute.name" size="small" class="mt-2 w-full" />
                            </div>

                            <!-- Form Input Number -->
                            <div v-if="formAttribute.type == 'number' && formAttribute.required" :class="styleClass" dclass="mt-4">
                                <label>{{ $t(formAttribute.name) }}</label>
                                <vs-input type="number" :placeholder="$t(formAttribute.name)"
                                    v-model="form.attribute[formAttribute.name]" :name="formAttribute.name" size="small"
                                    class="mt-2 w-full" />
                            </div>

                            <!-- Form Input Number -->
                            <div v-if="formAttribute.type == 'hidden' && formAttribute.required" :class="styleClass" dclass="mt-4">
                                <label>{{ $t(formAttribute.name) }} -- {{data[formAttribute.name]}} - {{form.attribute[formAttribute.name]}}</label>
                                <vs-input type="number" :placeholder="$t(formAttribute.name)"
                                    v-model="form.attribute[formAttribute.name]" 
                                    :name="formAttribute.name" size="small"
                                    class="mt-2 w-full" />
                            </div>

                            <!-- Form Select -->
                            <div v-if="formAttribute.type == 'select' && formAttribute.required" :class="styleClass" dclass="mt-4">
                                <label class="mb-2">{{ $t(formAttribute.name) }} <span v-if="formAttribute.required">*</span></label>
                                <span v-if="formAttribute.required">
                                    <template v-if="formAttribute.hasFilter">
                                        <v-select size="small"
                                            v-on:input="onInitChange($event, formAttribute.filterObj, formAttribute.name, formAttribute.api)"
                                            v-validate="''" v-model="form.attribute[formAttribute.name]" :name="formAttribute.name"
                                            :options="data[formAttribute.name]" :dir="$vs.rtl ? 'rtl' : 'ltr'" class="mt-2 w-full" />
                                        <span class="text-danger text-sm" v-show="errors.has(formAttribute.name)">{{
                                            $t("required_" + formAttribute.name)
                                        }}</span>
                                    </template>
                                    <template v-else>
                                        <v-select size="small" v-validate="''" v-model="form.attribute[formAttribute.name]"
                                            :name="formAttribute.name" :options="data[formAttribute.name]"
                                            :dir="$vs.rtl ? 'rtl' : 'ltr'" class="mt-2 w-full" />
                                        <span class="text-danger text-sm" v-show="errors.has(formAttribute.name)">{{
                                            $t("required_" + formAttribute.name)
                                        }}</span>
                                    </template>
                                </span>
                                <span v-else>
                                    <template v-if="formAttribute.hasFilter">
                                        <v-select size="small" v-validate="''"
                                            v-on:input="onInitChange($event, formAttribute.filterObj, formAttribute.name, formAttribute.api)"
                                            v-model="form.attribute[formAttribute.name]" :name="formAttribute.name"
                                            :options="data[formAttribute.name]" :dir="$vs.rtl ? 'rtl' : 'ltr'" class="mt-2 w-full" />
                                    </template>
                                    <template v-else>
                                        <v-select size="small" v-validate="''" v-model="form.attribute[formAttribute.name]"
                                            :name="formAttribute.name" :options="data[formAttribute.name]"
                                            :dir="$vs.rtl ? 'rtl' : 'ltr'" class="mt-2 w-full" />
                                    </template>
                                </span>
                            </div>

                            <!-- Form Textarea -->
                            <!-- <template v-if="formAttribute.type == 'textarea' && formAttribute.required" :class="styleClass"
                                dclass="mt-4">
                                <label>{{ $t(formAttribute.name) }}</label>
                                <vs-textarea label="Label in Textarea" v-model="form.attribute[formAttribute.name]"
                                    class="mt-2 w-full" />
                            </template> -->

                            <!-- Form Checkbox -->
                            <!-- <template v-if="formAttribute.type == 'checkbox' && formAttribute.required" :class="styleClass"
                                dclass="mt-4">
                                <label>{{ $t(formAttribute.name) }}</label>
                                <vs-checkbox v-model="form.attribute[formAttribute.name]"> {{ $t(formAttribute.name) }}</vs-checkbox>
                            </template> -->

                            <!-- Form Date -->
                            <div v-if="formAttribute.type == 'date' && formAttribute.required" :class="styleClass" dclass="mt-4">
                                <label class="mb-2">{{ $t(formAttribute.name) }}</label>
                                <span v-if="formAttribute.required">
                                    <flat-pickr v-validate="'required'" size="small" :name="formAttribute.name"
                                        v-model="form.attribute[formAttribute.name]" class="mt-1 w-full" />
                                    <span class="text-danger text-sm" v-show="errors.has(formAttribute.name)">{{ $t("required_" +
                                        formAttribute.name)
                                    }}</span>
                                </span>
                            </div>
                        </template>
                    </div>
                    <span class="pull-right">
                        <vs-button type="filled" @click.prevent="initSearch" class="mt-2 block">{{ $t("btn_search") }}</vs-button>
                    </span>
                </vx-card>
                <!-- <d-search v-if="!dataAttributes.hideSearchBar" @searchQuery="searchQuery" @clicked="initTableData"
                :data="data" :api="api" :formAttributes="formAttributes" :rowDisplay="'4grid'"
                :dataInfo="dataInfo"></d-search> -->
            </template>
            <vs-table v-if="dataObj.data && dataAttributes.tableStyle == 1" :max-items="dataObj.limit"
                :data="dataObj.data">
                <template slot="thead">
                    <!-- 28C76F -->
                    <vs-th style="background-color: #7367f0; color: #ffffff; font-weight: bold;">{{ $t("no") }}</vs-th>
                    <vs-th style="background-color: #7367f0; color: #ffffff; font-weight: bold;" :key="i"
                        v-for="(  header, i  ) in dataHeaders  ">{{ $t(header) }}</vs-th>
                    <vs-th style="background-color: #7367f0; color: #ffffff; font-weight: bold;">{{ $t("Action")
                        }}</vs-th>
                </template>
                <template slot-scope="{data}">
                    <tbody :key="indextr" v-for="(  tr, indextr  ) in data  ">
                        <vs-tr :state="dataAttributes.backgroundColor">
                            <vs-td>{{ calPageIncreaseNumber(dataObj.limit, indextr) }}</vs-td>
                            <vs-td v-for="header in dataHeaders" :key="header.indextr">
                                <template v-if="header == 'photo'">
                                    <img :src="tr[header]" @error="loadErrorImage" style="width: 100px" />
                                </template>
                                <template v-else-if="header == 'image'">
                                    <a href="javascript:void(0);" @click.stop="initPassData(tr)"><img :src="tr[header]" @error="loadErrorImage" style="width: 100px" /></a>
                                </template>
                                <template v-else-if="header == 'thumbnail'">
                                    <img :src="tr[header]" @error="loadErrorImage" style="width: 100px" />
                                </template>
                                <template v-else-if="header == 'attach'">
                                    <a target="_blank" :href="tr[header]"><img src="/images/attach.png" width="100px"/></a>
                                    <!-- <img :src="tr[header]" @error="loadErrorImage" style="width: 100px" /> -->
                                </template>
                                <template v-else-if="header == 'file'">
                                    <!-- <img :src="tr[header]" @error="loadErrorImage" style="width: 100px" /> -->
                                     <a target="_blank" :href="tr[header]"><img src="/images/attach.png" width="100px"/></a>
                                </template>
                                <template v-else>
                                    <template>{{ customField(tr, header) }}</template>
                                    <!-- <template v-if="header == 'status'">
                                        <template v-if="tr[header]!=null && tr[header]['title'] != undefined">{{ tr[header]["title"] }} </template>
                                        <template v-else>{{ tr[header] }} </template>
                                    </template>
                                    <template v-else>
                                        {{ tr[header] }}    
                                    </template> -->
                                </template>
                            </vs-td>
                            <vs-td>
                                <template v-if="dataAttributes.actionButton">
                                    <template v-for="  rowBtnAction in dataAttributes.actionButton  ">
                                        <feather-icon v-if="rowBtnAction.allow" style="cursor: pointer;"
                                            :icon="rowBtnAction.icon"
                                            svgClasses="mr-2 w-5 h-5 hover:text-primary stroke-current"
                                            @click.stop="initAction(rowBtnAction, tr, rowBtnAction.method)" />
                                    </template>
                                </template>
                                <template v-else>
                                    <!-- <center>
                                    <feather-icon style="cursor: pointer;" :key="sindex" icon="EditIcon"
                                        svgClasses="mr-2 w-5 h-5 hover:text-primary stroke-current"
                                        @click.stop="initAction(rowBtnAction, tr, 'Edit')" />
                                </center> -->
                                    <!-- <feather-icon style="cursor: pointer;" :key="sindex" icon="DollarSignIcon"
                                svgClasses="mr-2 w-5 h-5 hover:text-primary stroke-current"
                                @click.stop="initAction(rowBtnAction, tr, 'PrivateForm')" /> -->
                                </template>
                                <!-- <template v-if="dataAttributes.actionButton">
                                <feather-icon style="cursor: pointer;" v-for="rowBtnAction in dataAttributes.actionButton"
                                    :key="rowBtnAction.indextr" :icon="rowBtnAction.icon"
                                    svgClasses="mr-2 w-5 h-5 hover:text-primary stroke-current"
                                    @click.stop="viewUrl(rowBtnAction)" />
                                </template> -->
                                <feather-icon style="cursor: pointer;" icon="EditIcon"
                                    svgClasses="w-5 h-5 hover:text-primary stroke-current" @click.stop="initPassData(tr)" />
                                <feather-icon style="cursor: pointer;" v-if="allowDel" icon="TrashIcon"
                                    svgClasses="w-5 h-5 hover:text-danger stroke-current" class="ml-2"
                                    @click.stop="initDel(tr.id)" />
                            </vs-td>
                        </vs-tr>
                    </tbody>
                </template>
            </vs-table>

            <div class="my-5">
                <vs-pagination :total="calPaginNumber(dataObj.total / dataObj.limit)"
                    v-model="current_page"></vs-pagination>
                <!-- <p class="mb-3">Current Page: {{ current_page }}</p>
                <vs-button @click="current_page++">Increment</vs-button>
                <vs-button class="ml-4 mb-4" @click="current_page--">Decrement</vs-button> -->
            </div>
             <!-- <d-table-list @clicked="initTableData" :api="api" ref="refInitPage" :allowDel="true" :title="title" :dataInfo="dataInfo"
        :dataAttributes="dataAttributes" :dataHeaders="dataHeaders" :dataTables="data" :formAttributes="formAttributes"
        :rowDisplay="rowDisplay"></d-table-list> -->
        </vs-popup>
    </div>
</template>

<script>
import axios from "@/axios.js";
import { ref } from 'vue';
import Vue from "vue";
import apiConfig from "@/apiConfig.js";
import DSearch from '@/views/form-builder/DSearch.vue';

export default {
    data() {
        return {
            isHidden: true,
            showModalForm: false,
            current_page: 1,
            dataIndicators: [
                {
                    id: "23",
                    code: "12-",
                    indicator_name: "ឈ្មោះសូចនាករ",
                }
            ],
            file: null,
            title: "File Manager",
            api: apiConfig._apiFileManager,
            allowDel: false,
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
            dataObj: {
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
             _search_fields: {},
            form: {
                attribute: []
            },
            rowDisplay: "2grid", //1grid, 2grid, 3grid, 4grid
            dataFields: [],
            dataInfo: {},
            props: "",
        }
    },
    components: {
        DSearch,
    },
    methods: {
        loadErrorImage(event) {
            event.target.src = this.notFoundFile;
        },
        // Customize on Data Display
        customField(row, column) {
            switch (column) {
                case "status":
                    return row[column] ? row[column]["title_en"] : null;
                    break;
                case "officer_id":
                    return row["officer"] ? row["officer"]["first_name"]+ ' ' +row["officer"]["last_name"]+ '-' +row["officer"]["identity_member_no"] : null;
                    break;
                case "payment_method_id":
                    return row["payment_method"] ? row["payment_method"]["name_en"] : null;
                    break;
                case "currency_id":
                    return row["currency"] ? row["currency"]["name_en"] : null;
                    break;
                case "entity_id":
                    return row["entity"] ? row["entity"]["name_en"] : null;
                    break;
                case "department_id":
                    return row["department"] ? row["department"]["name_en"] : null;
                    break;
                default:
                    return row[column];
                    break;
            }
            return columnHeader;
        },
        initPassData(data) {
            this.dataInfo = data;
            this.showModalForm = false;
            this.$emit('clicked', {"data": data, "props": this.props});
        },
        initFileManager(props){
            this.props = props;
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

                        this.$vs.loading();
                        this.getData();
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
            this.$vs.loading();
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
                            this.dataObj = response.data;
                            this.dataHeaders = response.data.data_fields; 
                            this.formAttributes = response.data.form_attributes;
                        } else {
                            this.dataObj = this.data;
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
        searchQuery(_search_fields) {
            let _search_params = {
                pageNum: this.current_page,
                searchFields: _search_fields.formAttribute,
                dataInfo: _search_fields.dataInfo
            }
            this.$emit('clicked', _search_params);
            
            this.$vs.loading();
            let _search_criteria = {
                sort: "id",
                order: "",
                page_number: 1,
                search_field: _search_params.searchFields
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
        calPaginNumber(n) {
            // alert(n);
            let _newVal = 0;
            if (Number(n) === n && n % 1 === 0) {
                _newVal = n;
            } else {
                _newVal = Math.floor(n + 1);
            }
            return _newVal;
        },
        calPageIncreaseNumber(_page_limit, index) {
            let _offset = (this.current_page - 1) * _page_limit;
            let _result = _offset + (index + 1);
            return _result;
        },
        onInitChange($event, _formAttribute, _formName, _apiRequest) {
            let _data = {
                autocomplete: true,
                param: this.form.attribute[_formName]
            };
            this.form.attribute[_formAttribute] = null;
            this.data[_formAttribute] = [];
            return new Promise((resolve, reject) => {
                axios.post(_apiRequest, _data)
                    .then((response) => {
                        this.$vs.notify({
                            title: 'Message',
                            text: response.data.message,
                            iconPack: 'feather',
                            icon: 'icon-check-circle',
                            color: 'primary',
                            position: 'top-right'
                        });
                        for (let i = 0; i < response["data"]["data"].length; i++) {
                            let _d = {
                                "label": response["data"]["data"][i]["label"],
                                "value": response["data"]["data"][i]["value"],
                            }
                            this.data[_formAttribute].push(_d);
                        }

                        this.data[_formAttribute].push();
                        // response["data"].forEach(_formAttribute => {
                        //     alert("testing");
                        //     // let _d = {
                        //     //     [_formAttribute["name"]]: this.form.attribute[_formAttribute["name"]]
                        //     // }
                        //     // this.data[_formAttribute].push(_d);
                        // });
                        // this.data[_formAttribute] = [
                        //     {
                        //         "label": "tet",
                        //         "value": 1,
                        //     },
                        //     {
                        //         "label": "tet2",
                        //         "value": 3
                        //     },
                        // ];
                    }).catch((error) => {
                        reject(error)
                        this.$vs.notify({
                            title: 'Message',
                            text: "មិនអាចដំណើរកាបានទេ,​ សូមត្រួតពិនិត្យពត៌មានឡើងវិញ។",
                            iconPack: 'feather',
                            icon: 'icon-check-circle',
                            color: 'danger',
                            position: 'top-right'
                        })
                        this.$vs.loading.close();
                    })
            })

        },
        initSearch() {
            this._search_fields = [];
            let _search_fields = {
                "formAttribute": this.form.attribute,
                "dataInfo": this.dataInfo
            };
            this.searchQuery(_search_fields);
            // console.log("data Form", _params);
            // this.$emit('searchQuery', _params);
        }
    },
    created() {
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
        // console.log("check data form objects", this.data);
    },
    watch: {
        current_page: function (val) {
            let _search_params = {
                pageNum: this.current_page,
                searchFields: [],
                dataInfo: this.dataInfo
            }
            let _search_criteria = {
                sort: "id",
                order: "",
                page_number: this.current_page,
                search_field: _search_params.searchFields
            }
            this.getDataTable(_search_criteria);
        }
    }
}
</script>
<template>
    <div class="dt-filter-panel d-form" @keydown.enter.prevent="initSearch">
        <div class="dt-filter-panel__head">
            <span class="dt-filter-panel__icon"><feather-icon icon="FilterIcon" svgClasses="h-4 w-4" /></span>
            <h4>{{ $t('text_search') }}</h4>
        </div>
            <div class="vx-row">
                <template v-for="(formAttribute, i) in formAttributes">
                    <!-- Form Input Text -->
                    <div v-if="formAttribute.type == 'text' && formAttribute.required" :class="styleClass" class="d-field" dclass="mt-4">
                        <label>{{ $t(formAttribute.name) }}</label>
                        <vs-input :placeholder="$t(formAttribute.name)" v-model="form.attribute[formAttribute.name]"
                            :name="formAttribute.name" class="mt-2 w-full" />
                    </div>

                    <!-- Form Input Number -->
                    <div v-if="formAttribute.type == 'number' && formAttribute.required" :class="styleClass" class="d-field" dclass="mt-4">
                        <label>{{ $t(formAttribute.name) }}</label>
                        <vs-input type="number" :placeholder="$t(formAttribute.name)"
                            v-model="form.attribute[formAttribute.name]" :name="formAttribute.name"
                            class="mt-2 w-full" />
                    </div>

                    <!-- Form Input Number -->
                    <div v-if="formAttribute.type == 'hidden' && formAttribute.required" :class="styleClass" class="d-field" dclass="mt-4">
                        <label>{{ $t(formAttribute.name) }}</label>
                        <vs-input type="number" :placeholder="$t(formAttribute.name)"
                            v-model="form.attribute[formAttribute.name]" 
                            :name="formAttribute.name"
                            class="mt-2 w-full" />
                    </div>

                    <!-- Form Select -->
                    <div v-if="formAttribute.type == 'select' && formAttribute.required" :class="styleClass" class="d-field" dclass="mt-4">
                        <label class="mb-2">{{ $t(formAttribute.name) }}</label>
                        <span v-if="formAttribute.required">
                            <template v-if="formAttribute.hasFilter">
                                <v-select
                                    v-on:input="onInitChange($event, formAttribute.filterObj, formAttribute.name, formAttribute.api)"
                                    v-validate="''" v-model="form.attribute[formAttribute.name]" :name="formAttribute.name"
                                    :options="data[formAttribute.name]" :dir="$vs.rtl ? 'rtl' : 'ltr'" class="mt-2 w-full" />
                                <span class="text-danger text-sm" v-show="errors.has(formAttribute.name)">{{
                                    $t("required_" + formAttribute.name)
                                }}</span>
                            </template>
                            <template v-else>
                                <v-select v-validate="''" v-model="form.attribute[formAttribute.name]"
                                    :name="formAttribute.name" :options="data[formAttribute.name]"
                                    :dir="$vs.rtl ? 'rtl' : 'ltr'" class="mt-2 w-full" />
                                <span class="text-danger text-sm" v-show="errors.has(formAttribute.name)">{{
                                    $t("required_" + formAttribute.name)
                                }}</span>
                            </template>
                        </span>
                        <span v-else>
                            <template v-if="formAttribute.hasFilter">
                                <v-select v-validate="''"
                                    v-on:input="onInitChange($event, formAttribute.filterObj, formAttribute.name, formAttribute.api)"
                                    v-model="form.attribute[formAttribute.name]" :name="formAttribute.name"
                                    :options="data[formAttribute.name]" :dir="$vs.rtl ? 'rtl' : 'ltr'" class="mt-2 w-full" />
                            </template>
                            <template v-else>
                                <v-select v-validate="''" v-model="form.attribute[formAttribute.name]"
                                    :name="formAttribute.name" :options="data[formAttribute.name]"
                                    :dir="$vs.rtl ? 'rtl' : 'ltr'" class="mt-2 w-full" />
                            </template>
                        </span>
                    </div>

                    <!-- Form Textarea -->
                    <template v-if="formAttribute.type == 'textarea' && formAttribute.required" :class="styleClass"
                        dclass="mt-4">
                        <label>{{ $t(formAttribute.name) }}</label>
                        <vs-textarea label="Label in Textarea" v-model="form.attribute[formAttribute.name]"
                            class="mt-2 w-full" />
                    </template>

                    <!-- Form Checkbox -->
                    <!-- <template v-if="formAttribute.type == 'checkbox' && formAttribute.required" :class="styleClass"
                        dclass="mt-4">
                        <label>{{ $t(formAttribute.name) }}</label>
                        <vs-checkbox v-model="form.attribute[formAttribute.name]"> {{ $t(formAttribute.name) }}</vs-checkbox>
                    </template> -->

                    <!-- Form Date -->
                    <div v-if="formAttribute.type == 'date' && formAttribute.required" :class="styleClass" class="d-field" dclass="mt-4">
                        <label class="mb-2">{{ $t(formAttribute.name) }}</label>
                        <span v-if="formAttribute.required">
                            <flat-pickr v-validate="'required'" :name="formAttribute.name"
                                v-model="form.attribute[formAttribute.name]" class="mt-1 w-full" />
                            <span class="text-danger text-sm" v-show="errors.has(formAttribute.name)">{{ $t("required_" +
                                formAttribute.name)
                            }}</span>
                        </span>
                    </div>
                </template>
            </div>
            <div class="dt-filter-panel__actions">
                <button type="button" class="dt-btn" @click="resetSearch">
                    <feather-icon icon="RotateCcwIcon" svgClasses="h-4 w-4" />
                    <span>{{ $t('audit_clear') }}</span>
                </button>
                <button type="button" class="dt-btn dt-btn--primary" @click.prevent="initSearch">
                    <feather-icon icon="SearchIcon" svgClasses="h-4 w-4" />
                    <span>{{ $t("btn_search") }}</span>
                </button>
            </div>
    </div>
</template>

<script>
import { gridClass } from "@/formGrid.js";

import axios from "@/axios.js"
import DForm from '@/views/form-builder/DForm.vue'
import { AgGridVue } from "ag-grid-vue"
import '@sass/vuexy/extraComponents/agGridStyleOverride.scss'
import vSelect from 'vue-select'
import flatPickr from 'vue-flatpickr-component';
import 'flatpickr/dist/flatpickr.css';

export default {
    props: {
        data: {
            required: true,
        },
        dataInfo: {
            required: true,
        },
        api: { type: String },
        formAttributes: {
            required: true
        },
        rowDisplay: { type: String }
    },

    components: {
        AgGridVue,
        vSelect,
        DForm,
        flatPickr
    },
    data() {
        return {
            _search_fields: {},
            form: {
                attribute: []
            },
        }
    },
    watch: {

    },
    computed: {
        validateForm() {
            return !this.errors.any()
        },
        usersData() {
            return this.$store.state.userManagement.users
        },
        paginationPageSize() {
            if (this.gridApi) return this.gridApi.paginationGetPageSize()
            else return 10
        },
        totalPages() {
            if (this.gridApi) return this.gridApi.paginationGetTotalPages()
            else return 0
        },
        currentPage: {
            get() {
                if (this.gridApi) return this.gridApi.paginationGetCurrentPage() + 1
                else return 1
            },
            set(val) {
                this.gridApi.paginationGoToPage(val - 1)
            }
        }
    },
    methods: {
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
        resetSearch() {
            Object.keys(this.form.attribute).forEach(key => this.$set(this.form.attribute, key, null));
            this.initSearch();
        },
        initSearch() {
            this._search_fields = [];
            // let _formAttribute = this.form.attribute;
            // this.$emit('searchQuery', _formAttribute);
            let _params = {
                "formAttribute": this.form.attribute,
                "dataInfo": this.dataInfo
            };
            this.$emit('searchQuery', _params);

            // for (let i = 0; i < _formAttribute.length; i++) {
            //     let _nameField = _formAttribute[i]["name"] + ':' + this.form.attribute[_formAttribute[i]["name"]];
            //     this._search_fields.push(_nameField);
            // }
        }
    },
    mounted() {
        // this.formAttributes.forEach((formAttribute, i) => {
        //     if(formAttribute.type == 'hidden'){
        //     }
        //     this.form.attribute[formAttribute.name] = this.data[formAttribute.name];
        //     // arr.push(value);
        //     // console.log(value);
        //     // console.log(index);
        // });
    },
    created() {
        this.styleClass = gridClass(this.rowDisplay);
    }
}

</script>

<style lang="scss">
    #page-user-list {
        .user-list-filters {
            .vs__actions {
                position: absolute;
                right: 0;
                top: 50%;
                transform: translateY(-58%);
            }
        }
    }
</style>
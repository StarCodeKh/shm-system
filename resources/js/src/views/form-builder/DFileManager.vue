<template>
    <div class="demo-alignment">
        <vs-popup class="d-fm d-modal d-modal--lg" classContent="d-fm__content" :title="$t('fm_title')"
            :active.sync="showModalForm">

            <!-- Upload: click or drop a file, it uploads right away -->
            <label v-if="canUpload" class="d-fm__drop" :class="{ 'is-over': dragOver, 'is-busy': uploading }"
                @dragover.prevent="dragOver = true" @dragleave.prevent="dragOver = false" @drop.prevent="onDrop">
                <input type="file" name="file" class="d-fm__input" :disabled="uploading" @change="onPick">
                <span class="d-fm__drop-icon">
                    <feather-icon :icon="uploading ? 'LoaderIcon' : 'UploadCloudIcon'" svgClasses="h-5 w-5" />
                </span>
                <span class="d-fm__drop-text">
                    <span class="d-fm__drop-title">{{ uploading ? $t('fm_uploading') : $t('fm_drop') }}</span>
                    <span class="d-fm__drop-hint">{{ uploading && file ? file.name : $t('upload_hint_file') }}</span>
                </span>
                <span class="d-fm__drop-btn">{{ $t('upload_browse') }}</span>
            </label>

            <!-- Toolbar -->
            <div class="d-fm__toolbar">
                <div class="d-fm__count">
                    <feather-icon icon="FolderIcon" svgClasses="h-4 w-4" />
                    <span>{{ $t('fm_files', { total: dataObj.total || 0 }) }}</span>
                </div>
                <button type="button" class="d-fm__tool-btn" :class="{ 'is-active': !isHidden }" @click="isHidden = !isHidden">
                    <feather-icon icon="FilterIcon" svgClasses="h-4 w-4" />
                    <span>{{ $t('fm_filter') }}</span>
                </button>
            </div>

            <template v-if="!isHidden">
                <div class="d-fm__filter d-form">
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
                                <label class="mb-2">{{ $t(formAttribute.name) }} <span v-if="formAttribute.required">*</span></label>
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
                    <div class="d-fm__filter-actions">
                        <vs-button type="filled" icon-pack="feather" icon="icon-search" @click.prevent="initSearch">{{ $t("btn_search") }}</vs-button>
                    </div>
                </div>
                <!-- <d-search v-if="!dataAttributes.hideSearchBar" @searchQuery="searchQuery" @clicked="initTableData"
                :data="data" :api="api" :formAttributes="formAttributes" :rowDisplay="'4grid'"
                :dataInfo="dataInfo"></d-search> -->
            </template>
            <!-- File list -->
            <div class="d-fm__list">
                <table v-if="rows.length" class="d-fm__table">
                    <thead>
                        <tr>
                            <th class="d-fm__col-no">{{ $t("no") }}</th>
                            <th>{{ $t('fm_file') }}</th>
                            <th class="d-fm__col-type">{{ $t('fm_type') }}</th>
                            <th class="d-fm__col-date">{{ $t('fm_created') }}</th>
                            <th class="d-fm__col-date">{{ $t('fm_updated') }}</th>
                            <th class="d-fm__col-action"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(tr, indextr) in rows" :key="tr.id || indextr" @click="initPassData(tr)">
                            <td class="d-fm__col-no">{{ calPageIncreaseNumber(dataObj.limit, indextr) }}</td>
                            <td>
                                <div class="d-fm__file">
                                    <a class="d-fm__thumb" :href="fileUrl(tr)" target="_blank" rel="noopener"
                                        :title="$t('upload_view')" @click.stop>
                                        <img v-if="isImage(tr) && !brokenImages[tr.id]" :src="fileUrl(tr)" alt=""
                                            loading="lazy" @error="$set(brokenImages, tr.id, true)">
                                        <span v-else class="d-fm__badge" :style="{ '--type': typeColor(tr) }">
                                            {{ fileType(tr) || 'file' }}
                                        </span>
                                    </a>
                                    <div class="d-fm__file-text">
                                        <span class="d-fm__name" :title="fileName(tr)">{{ fileName(tr) }}</span>
                                        <span class="d-fm__path" :title="tr.path">{{ tr.path }}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="d-fm__col-type">
                                <span class="d-fm__chip" :style="{ '--type': typeColor(tr) }">{{ fileType(tr) }}</span>
                            </td>
                            <td class="d-fm__col-date">{{ shortDate(tr.created_at) }}</td>
                            <td class="d-fm__col-date">{{ shortDate(tr.updated_at) }}</td>
                            <td class="d-fm__col-action">
                                <button type="button" class="d-fm__select" @click.stop="initPassData(tr)">
                                    <feather-icon icon="CheckIcon" svgClasses="h-4 w-4" />
                                    <span>{{ $t('fm_select') }}</span>
                                </button>
                                <button v-if="allowDel" type="button" class="d-fm__icon-btn is-danger"
                                    @click.stop="initDel(tr.id)">
                                    <feather-icon icon="TrashIcon" svgClasses="h-4 w-4" />
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>

                <div v-else class="d-fm__empty">
                    <span class="d-fm__empty-icon"><feather-icon icon="FolderIcon" svgClasses="h-6 w-6" /></span>
                    <strong>{{ $t('fm_empty') }}</strong>
                    <span>{{ $t('fm_empty_hint') }}</span>
                </div>
            </div>

            <div v-if="calPaginNumber(dataObj.total / dataObj.limit) > 1" class="d-fm__pager">
                <vs-pagination :total="calPaginNumber(dataObj.total / dataObj.limit)"
                    v-model="current_page"></vs-pagination>
            </div>
             <!-- <d-table-list @clicked="initTableData" :api="api" ref="refInitPage" :allowDel="true" :title="title" :dataInfo="dataInfo"
        :dataAttributes="dataAttributes" :dataHeaders="dataHeaders" :dataTables="data" :formAttributes="formAttributes"
        :rowDisplay="rowDisplay"></d-table-list> -->
        </vs-popup>
    </div>
</template>

<script>
import { can } from "@/acl/access.js";
import { gridClass } from "@/formGrid.js";
import axios from "@/axios.js";
import { ref } from 'vue';
import apiConfig from "@/apiConfig.js";
import DSearch from '@/views/form-builder/DSearch.vue';

const IMAGE_EXT = ['jpg', 'jpeg', 'png', 'gif', 'svg', 'webp', 'bmp']
const TYPE_COLORS = {
    pdf: '220, 53, 69',
    doc: '43, 87, 154',
    docx: '43, 87, 154',
    xls: '33, 115, 70',
    xlsx: '33, 115, 70',
    csv: '33, 115, 70',
    image: '115, 103, 240',
}

export default {
    data() {
        return {
            isHidden: true,
            showModalForm: false,
            uploading: false,
            dragOver: false,
            brokenImages: {},
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
    computed: {
        canUpload() {
            return can('file_manager_upload')
        },
        rows() {
            return (this.dataObj.data || []).filter(row => row && Object.keys(row).length)
        },
    },
    methods: {
        onPick(event) {
            const file = event.target.files[0]
            event.target.value = ''
            if (file) this.uploadFile(file)
        },
        onDrop(event) {
            this.dragOver = false
            const file = event.dataTransfer.files[0]
            if (file && !this.uploading) this.uploadFile(file)
        },
        uploadFile(file) {
            this.file = file
            this.initUploadFile()
        },
        fileType(row) {
            const fromPath = (row.path || '').split(/[?#]/)[0].split('.').pop()
            return String(row.type || (fromPath !== row.path ? fromPath : '') || '').toLowerCase()
        },
        isImage(row) {
            return IMAGE_EXT.includes(this.fileType(row))
        },
        fileUrl(row) {
            return row.path || row.image || ''
        },
        fileName(row) {
            const clean = (row.path || '').split(/[?#]/)[0]
            return row.name || decodeURIComponent(clean.split('/').pop() || clean)
        },
        typeColor(row) {
            const type = this.fileType(row)
            return TYPE_COLORS[type] || (IMAGE_EXT.includes(type) ? TYPE_COLORS.image : '92, 99, 120')
        },
        shortDate(value) {
            return value ? String(value).slice(0, 10) : ''
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
        initUploadFile(){
            let formData = new FormData();
            formData.append('file', this.file);
            this.uploading = true;
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

                        this.uploading = false;
                        this.current_page = 1;
                        this.$vs.loading();
                        this.getData();
                    }).catch((error) => {
                        this.uploading = false;
                        reject(error)
                        this.$vs.notify({
                            title: 'Message',
                            text: 'msg.save_failed',
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
                            text: 'msg.save_failed',
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
        this.styleClass = gridClass(this.rowDisplay);
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

<style lang="scss">
// ---------------------------------------------------------------- File manager popup
.d-fm {
    .d-fm__content {
        padding: 20px 24px 24px;
        background: #f8f9fb;
    }
}

// ---------------------------------------------------------------- Upload drop zone
.d-fm__drop {
    position: relative;
    display: flex;
    align-items: center;
    gap: 14px;
    min-height: 72px;
    padding: 12px 16px;
    border: 1.5px dashed #c9ccd6;
    border-radius: calc(var(--input-radius, 10px) + 4px);
    background: #fff;
    cursor: pointer;
    transition: border-color .2s ease, background .2s ease, box-shadow .2s ease;

    &:hover,
    &.is-over {
        border-color: rgb(var(--vs-primary));
        background: rgba(var(--vs-primary), .04);

        .d-fm__drop-icon {
            color: #fff;
            background: rgb(var(--vs-primary));
        }
    }

    &.is-over {
        box-shadow: 0 0 0 4px rgba(var(--vs-primary), .12);
    }

    &.is-busy {
        cursor: progress;

        .d-fm__drop-icon svg {
            animation: d-fm-spin 1s linear infinite;
        }
    }
}

input.d-fm__input {
    position: absolute;
    width: 1px !important;
    height: 1px !important;
    min-height: 0 !important;
    padding: 0 !important;
    border: 0 !important;
    opacity: 0;
    pointer-events: none;
}

.d-fm__drop-icon {
    flex-shrink: 0;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 44px;
    height: 44px;
    border-radius: 12px;
    color: rgb(var(--vs-primary));
    background: rgba(var(--vs-primary), .1);
    transition: background .2s ease, color .2s ease;

    .feather-icon {
        display: flex;
    }
}

.d-fm__drop-text {
    display: flex;
    flex-direction: column;
    flex: 1;
    min-width: 0;
    line-height: 1.45;
}

.d-fm__drop-title {
    font-size: .95rem;
    font-weight: 600;
    color: #2c2e45;
}

.d-fm__drop-hint {
    font-size: .8rem;
    color: #8a8ca5;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.d-fm__drop-btn {
    flex-shrink: 0;
    padding: 9px 18px;
    font-size: .9rem;
    font-weight: 600;
    color: #fff;
    border-radius: var(--input-radius, 10px);
    background: rgb(var(--vs-primary));
    box-shadow: 0 6px 14px -8px rgba(var(--vs-primary), .9);
}

// ---------------------------------------------------------------- Toolbar + filter
.d-fm__toolbar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    margin: 20px 0 12px;
}

.d-fm__count {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    font-size: .9rem;
    font-weight: 600;
    color: #4b4d68;

    .feather-icon {
        display: flex;
        color: rgb(var(--vs-primary));
    }
}

.d-fm__tool-btn {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    height: var(--input-height, 42px);
    padding: 0 16px;
    font-family: inherit;
    font-size: .875rem;
    font-weight: 600;
    color: #4b4d68;
    border: 1px solid var(--input-border, #d8d9e3);
    border-radius: var(--input-radius, 10px);
    background: #fff;
    cursor: pointer;
    transition: all .15s ease;

    .feather-icon {
        display: flex;
    }

    &:hover,
    &.is-active {
        color: rgb(var(--vs-primary));
        border-color: rgba(var(--vs-primary), .5);
        background: rgba(var(--vs-primary), .06);
    }
}

.d-fm__filter {
    margin-bottom: 12px;
    padding: 4px 20px 20px;
    border: 1px solid #eceef3;
    border-radius: 14px;
    background: #fff;
}

.d-fm__filter-actions {
    display: flex;
    justify-content: flex-end;
    margin-top: 16px;
}

// ---------------------------------------------------------------- File list
.d-fm__list {
    border: 1px solid #eceef3;
    border-radius: 14px;
    background: #fff;
    overflow: hidden;
}

.d-fm__table {
    width: 100%;
    border-collapse: collapse;

    th {
        padding: 12px 16px;
        font-size: .78rem;
        font-weight: 700;
        letter-spacing: .02em;
        text-align: left;
        text-transform: none;
        color: #6b6d7a;
        background: #f7f8fa;
        border-bottom: 1px solid #eceef3;
        white-space: nowrap;
    }

    td {
        padding: 10px 16px;
        font-size: .875rem;
        color: #2c2e45;
        border-bottom: 1px solid #f1f2f5;
        vertical-align: middle;
    }

    tbody tr {
        cursor: pointer;
        transition: background-color .12s ease;

        &:last-child td {
            border-bottom: 0;
        }

        &:hover {
            background: rgba(var(--vs-primary), .04);

            .d-fm__select {
                color: #fff;
                background: rgb(var(--vs-primary));
            }
        }
    }
}

.d-fm__col-no {
    width: 56px;
    color: #8a8ca5 !important;
}

.d-fm__col-type {
    width: 90px;
}

.d-fm__col-date {
    width: 120px;
    color: #6b6d7a !important;
    white-space: nowrap;
}

.d-fm__col-action {
    width: 1%;
    text-align: right !important;
    white-space: nowrap;
}

.d-fm__file {
    display: flex;
    align-items: center;
    gap: 12px;
    min-width: 0;
}

.d-fm__thumb {
    flex-shrink: 0;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 44px;
    height: 44px;
    border: 1px solid #eceef3;
    border-radius: 10px;
    background: #f7f8fa;
    overflow: hidden;
    cursor: zoom-in;

    img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform .25s ease;
    }

    &:hover img {
        transform: scale(1.08);
    }
}

.d-fm__badge {
    --type: 92, 99, 120;
    display: flex;
    align-items: center;
    justify-content: center;
    width: 100%;
    height: 100%;
    font-size: .62rem;
    font-weight: 800;
    letter-spacing: .04em;
    text-transform: uppercase;
    color: rgb(var(--type));
    background: rgba(var(--type), .1);
}

.d-fm__file-text {
    display: flex;
    flex-direction: column;
    min-width: 0;
    line-height: 1.45;
}

.d-fm__name {
    font-weight: 600;
    color: #2c2e45;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.d-fm__path {
    font-size: .75rem;
    color: #8a8ca5;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.d-fm__table td:nth-child(2) {
    max-width: 0; // lets the name/path ellipsis inside a fluid column
    width: 100%;
}

.d-fm__chip {
    --type: 92, 99, 120;
    display: inline-block;
    padding: 2px 8px;
    font-size: .72rem;
    font-weight: 700;
    text-transform: uppercase;
    color: rgb(var(--type));
    border-radius: 6px;
    background: rgba(var(--type), .1);
}

.d-fm__select {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    height: 34px;
    padding: 0 14px;
    font-family: inherit;
    font-size: .85rem;
    font-weight: 600;
    color: rgb(var(--vs-primary));
    border: 0;
    border-radius: 9px;
    background: rgba(var(--vs-primary), .1);
    cursor: pointer;
    transition: all .15s ease;

    .feather-icon {
        display: flex;
    }
}

.d-fm__icon-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 34px;
    height: 34px;
    margin-left: 6px;
    padding: 0;
    color: #8a8ca5;
    border: 0;
    border-radius: 9px;
    background: transparent;
    cursor: pointer;

    &.is-danger:hover {
        color: #dc3545;
        background: rgba(220, 53, 69, .08);
    }
}

.d-fm__empty {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 6px;
    padding: 56px 16px;
    text-align: center;
    color: #8a8ca5;

    strong {
        font-size: 1rem;
        color: #2c2e45;
    }
}

.d-fm__empty-icon {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 56px;
    height: 56px;
    margin-bottom: 6px;
    border-radius: 16px;
    color: rgb(var(--vs-primary));
    background: rgba(var(--vs-primary), .1);
}

.d-fm__pager {
    display: flex;
    justify-content: flex-end;
    margin-top: 16px;
}

@keyframes d-fm-spin {
    to { transform: rotate(360deg); }
}

@media (max-width: 768px) {
    .d-fm .d-fm__content {
        padding: 16px;
    }

    .d-fm__col-date,
    .d-fm__col-type,
    .d-fm__col-no {
        display: none;
    }

    .d-fm__select span,
    .d-fm__drop-btn {
        display: none;
    }
}

// ---------------------------------------------------------------- Dark theme
.theme-dark {
    .d-fm__drop,
    .d-fm__tool-btn,
    .d-fm__filter,
    .d-fm__list {
        background: #10163a;
        border-color: #414561;
    }

    .d-fm .d-fm__content,
    .d-fm__table th {
        background: #262c49;
    }

    .d-fm__drop-title,
    .d-fm__name,
    .d-fm__table td,
    .d-fm__empty strong {
        color: #fff;
    }

    .d-fm__table td {
        border-bottom-color: #2c3254;
    }
}
</style>

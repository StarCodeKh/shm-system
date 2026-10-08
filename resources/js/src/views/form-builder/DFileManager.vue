<template>
    <div class="demo-alignment">
        <vs-popup class="d-fm d-modal d-modal--lg" classContent="d-fm__content" :title="$t('fm_title')"
            v-model:active="showModalForm">

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

            <!-- Search panel: the fields the API lists for searching (name, type), options from the API too -->
            <div v-if="!isHidden" class="d-fm__filter d-form">
                <div class="vx-row">
                    <div v-for="field in filterFields" :key="field.name" :class="styleClass" class="d-field">
                        <label>{{ $tField(field.name) }}</label>
                        <v-select v-if="field.type === 'select'" v-model="form.attribute[field.name]" :name="field.name"
                            :options="(dataObj && dataObj[field.name]) || []" :placeholder="$tHint(field.name, 'select')"
                            :dir="$vs.rtl ? 'rtl' : 'ltr'" class="mt-2 w-full" />
                        <flat-pickr v-else-if="field.type === 'date'" v-model="form.attribute[field.name]" :name="field.name"
                            class="mt-2 w-full" />
                        <vs-input v-else v-model="form.attribute[field.name]" :name="field.name" :placeholder="$tHint(field.name)"
                            class="mt-2 w-full" @keyup.enter="initSearch" />
                    </div>
                </div>
                <div class="d-fm__filter-actions">
                    <vs-button type="filled" icon-pack="feather" icon="icon-search" @click.prevent="initSearch">{{ $t("btn_search") }}</vs-button>
                </div>
            </div>
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
                        <tr v-for="(tr, indextr) in rows" :key="tr.id || indextr" :class="{ 'is-editing': editingId === tr.id }"
                            @click="editingId !== tr.id && initPassData(tr)">
                            <td class="d-fm__col-no">{{ calPageIncreaseNumber(dataObj.limit, indextr) }}</td>
                            <td>
                                <div class="d-fm__file">
                                    <a class="d-fm__thumb" :href="fileUrl(tr)" target="_blank" rel="noopener"
                                        :title="$t('upload_view')" @click.stop>
                                        <img v-if="isImage(tr) && !brokenImages[tr.id]" :src="fileUrl(tr)" alt=""
                                            loading="lazy" @error="brokenImages[tr.id] = true">
                                        <span v-else class="d-fm__badge" :style="{ '--type': typeColor(tr) }">
                                            {{ fileType(tr) || 'file' }}
                                        </span>
                                    </a>
                                    <!-- Editing: name and caption in place (Enter saves, Esc cancels) -->
                                    <div v-if="editingId === tr.id" class="d-fm__edit" @click.stop>
                                        <input v-model.trim="editForm.file_name" class="d-fm__edit-input" type="text" maxlength="255"
                                            :placeholder="$tField('file_name')" :aria-label="$tField('file_name')"
                                            @keydown.enter.prevent="saveEdit(tr)" @keydown.esc.prevent="cancelEdit">
                                        <input v-model.trim="editForm.caption" class="d-fm__edit-input is-small" type="text" maxlength="1000"
                                            :placeholder="$tField('caption')" :aria-label="$tField('caption')"
                                            @keydown.enter.prevent="saveEdit(tr)" @keydown.esc.prevent="cancelEdit">
                                    </div>
                                    <div v-else class="d-fm__file-text">
                                        <span class="d-fm__name" :title="fileName(tr)">{{ fileName(tr) }}</span>
                                        <span class="d-fm__path" :title="tr.caption || tr.path">{{ tr.caption || tr.path }}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="d-fm__col-type">
                                <span class="d-fm__chip" :style="{ '--type': typeColor(tr) }">{{ fileType(tr) }}</span>
                            </td>
                            <td class="d-fm__col-date">{{ shortDate(tr.created_at) }}</td>
                            <td class="d-fm__col-date">{{ shortDate(tr.updated_at) }}</td>
                            <td class="d-fm__col-action">
                                <template v-if="editingId === tr.id">
                                    <button type="button" class="d-fm__select" :disabled="savingEdit || !editForm.file_name" @click.stop="saveEdit(tr)">
                                        <feather-icon icon="CheckIcon" svgClasses="h-4 w-4" />
                                        <span>{{ $t('btn_save') }}</span>
                                    </button>
                                    <button type="button" class="d-fm__icon-btn" :title="$t('btn_cancel')" @click.stop="cancelEdit">
                                        <feather-icon icon="XIcon" svgClasses="h-4 w-4" />
                                    </button>
                                </template>
                                <template v-else>
                                    <button type="button" class="d-fm__select" @click.stop="initPassData(tr)">
                                        <feather-icon icon="CheckIcon" svgClasses="h-4 w-4" />
                                        <span>{{ $t('fm_select') }}</span>
                                    </button>
                                    <!-- Shown by the user's permissions (sent with the list) -->
                                    <button v-if="can('update')" type="button" class="d-fm__icon-btn" :title="$t('dt_edit')" @click.stop="startEdit(tr)">
                                        <feather-icon icon="Edit2Icon" svgClasses="h-4 w-4" />
                                    </button>
                                    <button v-if="can('delete')" type="button" class="d-fm__icon-btn is-danger" :title="$t('dt_delete')" @click.stop="confirmDelete(tr)">
                                        <feather-icon icon="TrashIcon" svgClasses="h-4 w-4" />
                                    </button>
                                </template>
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
                <d-pagination :total="calPaginNumber(dataObj.total / dataObj.limit)" v-model="current_page" />
            </div>
        </vs-popup>
    </div>
</template>

<script>
import { can } from "@/acl/access.js";
import { gridClass } from "@/formGrid.js";
import axios, { errorMessage } from "@/axios.js";
import apiConfig from "@/apiConfig.js";

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
            editingId: null,                           // row being renamed
            editForm: { file_name: '', caption: '' },
            savingEdit: false,
            dataAttributes: {
                tableStyle: 1,
                page_number: 1,
                offset: 0,
                dataGrid: "row",
                actionButton: [
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
            searchAttributes: [], // search panel fields from the API
            dataInfo: {},
            props: "",
        }
    },
    components: {
    },
    computed: {
        // Search panel fields (older APIs without search_attributes: the required form fields)
        filterFields() {
            return this.searchAttributes.length ? this.searchAttributes : this.formAttributes.filter(field => field && field.required)
        },
        canUpload() {
            return can('file_manager_upload')
        },
        rows() {
            return (this.dataObj.data || []).filter(row => row && Object.keys(row).length)
        },
    },
    methods: {
        // "update_file_manager" etc. from the permissions the list response carries
        can(action) {
            const permissions = (this.dataObj && this.dataObj.permissions) || {}
            const model = (this.dataObj && this.dataObj.model_name) || 'file_manager'
            return Boolean(permissions['*'] || permissions[action + '_' + model])
        },
        startEdit(row) {
            this.editingId = row.id
            this.editForm = { file_name: row.file_name || this.fileName(row), caption: row.caption || '' }
            this.$nextTick(() => { const input = document.querySelector('.d-fm__edit-input'); if (input) input.focus() })
        },
        cancelEdit() {
            this.editingId = null
        },
        saveEdit(row) {
            if (this.savingEdit || !this.editForm.file_name) return
            this.savingEdit = true
            axios.put(this.api + '/' + row.id, { file_name: this.editForm.file_name, caption: this.editForm.caption || null })
                .then(({ data }) => {
                    Object.assign(row, data.data || this.editForm)
                    this.editingId = null
                    this.$vs.notify({ text: data.message, color: 'success' })
                })
                .catch(error => this.$vs.notify({ text: this.errorText(error), color: 'danger' }))
                .finally(() => { this.savingEdit = false })
        },
        confirmDelete(row) {
            this.$vs.dialog({
                type: 'confirm',
                color: 'danger',
                title: this.$t('dt_delete_title'),
                text: this.$t('fm_delete_text', { name: this.fileName(row) }),
                acceptText: this.$t('dt_delete'),
                cancelText: this.$t('dt_cancel'),
                accept: () => this.deleteFile(row),
            })
        },
        deleteFile(row) {
            axios.delete(this.api + '/' + row.id)
                .then(({ data }) => {
                    this.$vs.notify({ text: data.message, color: 'success' })
                    this.getData() // reloads the list and the type counts
                })
                .catch(error => this.$vs.notify({ text: this.errorText(error), color: 'danger' }))
        },
        // Why the request failed, in the user's language (see errorMessage in axios.js)
        errorText(error) {
            return errorMessage(error)
        },
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
            // The name it was uploaded with (file_name); the stored file name otherwise
            return row.file_name || row.name || decodeURIComponent(clean.split('/').pop() || clean)
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
        },
        getDataTable(_search_criteria) {
            this.$vs.loading();
            let _params = {};
            if (_search_criteria.search_field) {
                this.dataFields = [];
                this.filterFields.forEach(_formAttribute => {
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
                            this.searchAttributes = response.data.search_attributes || [];
                        }
                        this.$vs.loading.close();
                    }).catch((error) => {
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
            // (no event: "clicked" means a file was picked; the search runs here)

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
            this.dataObj[_formAttribute] = [];
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
                            this.dataObj[_formAttribute].push(_d);
                        }

                        this.dataObj[_formAttribute].push();
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
        }
    },
    created() {
        this.styleClass = gridClass(this.rowDisplay);
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
    display: inline-flex;
    align-items: center;
    height: var(--btn-height);
    padding: 0 var(--btn-padding-x);
    font-size: var(--btn-font-size);
    font-weight: var(--btn-font-weight);
    color: #fff;
    border-radius: var(--btn-radius);
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
    height: var(--btn-height);
    padding: 0 var(--btn-padding-x);
    font-family: inherit;
    font-size: var(--btn-font-size);
    font-weight: var(--btn-font-weight);
    color: #4b4d68;
    border: 1px solid var(--input-border, #d8d9e3);
    border-radius: var(--btn-radius);
    background: #fff;
    cursor: pointer;

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

/* Renaming a file in place */
.d-fm__edit {
    display: flex;
    flex: 1;
    flex-direction: column;
    gap: 6px;
    min-width: 0;
}

.d-fm__edit-input {
    width: 100%;
    height: 34px;
    padding: 0 10px;
    font: inherit;
    font-size: 14px;
    color: #2b2d42;
    background: #fff;
    border: 1px solid var(--input-border, #d8dce6);
    border-radius: 8px;
    outline: none;
    transition: border-color .15s ease, box-shadow .15s ease;

    &.is-small {
        height: 30px;
        font-size: 13px;
    }

    &:focus {
        border-color: rgb(var(--vs-primary));
        box-shadow: 0 0 0 3px rgba(var(--vs-primary), .15);
    }
}

.d-fm__table tr.is-editing {
    background: rgba(var(--vs-primary), .04);
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
    width: var(--btn-height-sm);
    height: var(--btn-height-sm);
    margin-left: 6px;
    padding: 0;
    color: #8a8ca5;
    border: 0;
    border-radius: var(--btn-radius);
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

<template>
    <div class="d-form">
        <!-- <vs-popup fullscreen classContent="popup-example" :title="title"
            v-model:active="showFileManager">
            <form name="file">
                <input type="file" name="file" @change="selectedFile($event)"/>
                <input type="button" value="Upload" @click="initUploadFile()"/>
            </form>
        </vs-popup> -->
            <d-file-manager ref="refModelFileManager" @clicked="initPassProps"></d-file-manager>

        <!-- "+ Add" next to a dropdown: the same standard form, for the table that dropdown lists -->
        <vs-popup ref="quickAddPopup" style="z-index: 5200000;" class="d-modal" classContent="popup-example"
            :title="quickAdd ? $t('form_quick_add', { field: fieldLabel(quickAdd.field) }) : ''"
            v-model:active="quickAddOpen">
            <d-form v-if="quickAdd && quickAdd.fields" :key="quickAdd.key" :data="quickAdd.data" :dataInfo="{}"
                :parentDataInfo="{}" :formAttributes="quickAdd.fields" :api="quickAdd.api" :rowDisplay="rowDisplay"
                @saved="onQuickAdded" @cancel="quickAddOpen = false" />
        </vs-popup>
        <div class="vx-row">
            <template v-for="(formAttribute, i) in formAttributes">
                <!-- Form Input Text -->
                <div v-if="formAttribute.type == 'text'" :class="styleClass" class="d-field" dclass="mt-4">
                    <label class="mb-2">{{ fieldLabel(formAttribute.name) }} <span v-if="formAttribute.required">*</span></label>
                    <span v-if="formAttribute.required">
                        <vs-input v-validate="'required'" v-model="form.attribute[formAttribute.name]"
                            :placeholder="fieldHint(formAttribute)" :name="formAttribute.name" class="mt-1 w-full" />
                        <span class="text-danger text-sm" v-show="errors.has(formAttribute.name)">{{
                            requiredMessage(formAttribute.name)
                            }}</span>
                    </span>
                    <vs-input v-else v-validate="''" :placeholder="fieldHint(formAttribute)"
                        v-model="form.attribute[formAttribute.name]" :name="formAttribute.name" class="mt-1 w-full" />
                </div>

                <!-- Form Input Number -->
                <input v-if="formAttribute.type == 'hidden'" type="hidden"
                    v-model="form.attribute[formAttribute.name]" :name="formAttribute.name" />

                <!-- Form Input Text -->
                <div v-if="formAttribute.type == 'password'" :class="styleClass" class="d-field" dclass="mt-4">
                    <label class="mb-2">{{ fieldLabel(formAttribute.name) }} <span v-if="formAttribute.required">*</span></label>
                    <span v-if="formAttribute.required">
                        <vs-input type="password" v-validate="'required'"
                            v-model="form.attribute[formAttribute.name]" :placeholder="fieldHint(formAttribute, 'password')"
                            :name="formAttribute.name" class="mt-1 w-full" />
                        <span class="text-danger text-sm" v-show="errors.has(formAttribute.name)">{{
                            requiredMessage(formAttribute.name)
                            }}</span>
                    </span>
                    <vs-input type="password" v-else v-validate="''" :placeholder="fieldHint(formAttribute, 'password')"
                        v-model="form.attribute[formAttribute.name]" :name="formAttribute.name" class="mt-1 w-full" />
                </div>

                <!-- Form Input Number -->
                <div v-if="formAttribute.type == 'number'" :class="styleClass" class="d-field" dclass="mt-4">
                    <label class="mb-2">{{ fieldLabel(formAttribute.name) }} <span v-if="formAttribute.required">*</span></label>
                    <span v-if="formAttribute.required">
                        <vs-input :placeholder="fieldHint(formAttribute, 'number')" type="number" v-validate="'required'" :name="formAttribute.name"
                            v-model="form.attribute[formAttribute.name]" icon-inc="expand_less" icon-dec="expand_more"
                            class="mt-1 w-full" />
                        <span class="text-danger text-sm" v-show="errors.has(formAttribute.name)">
                            {{ requiredMessage(formAttribute.name) }}
                        </span>
                    </span>
                    <vs-input :placeholder="fieldHint(formAttribute, 'number')" type="number" v-validate="''" v-else :name="formAttribute.name"
                        v-model="form.attribute[formAttribute.name]" icon-inc="expand_less" icon-dec="expand_more"
                        class="mt-1 w-full" />
                </div>

                <div v-if="formAttribute.type == 'select'" :class="styleClass" class="d-field" dclass="mt-4">
                    <label class="mb-2">{{ fieldLabel(formAttribute.name) }} <span
                            v-if="formAttribute.required">*</span></label>
                    <button v-if="canQuickAdd(formAttribute.name)" type="button" class="d-quick-add"
                        :title="$t('form_quick_add', { field: fieldLabel(formAttribute.name) })"
                        @click.stop="openQuickAdd(formAttribute.name)">
                        <feather-icon icon="PlusIcon" svgClasses="h-3 w-3" />
                        <span>{{ $t('form_add') }}</span>
                    </button>
                    <span v-if="formAttribute.required">
                        <template v-if="formAttribute.hasFilter">
                            <v-select :placeholder="fieldHint(formAttribute, 'select')"
                                v-on:input="onInitChange($event, formAttribute.filterObj, formAttribute.name, formAttribute.api)"
                                v-validate="'required'" v-model="form.attribute[formAttribute.name]"
                                :name="formAttribute.name" :options="data[formAttribute.name]"
                                :dir="$vs.rtl ? 'rtl' : 'ltr'" class="mt-2 w-full" />
                            <span class="text-danger text-sm" v-show="errors.has(formAttribute.name)">{{
                                requiredMessage(formAttribute.name)
                                }}</span>
                        </template>
                        <template v-else>
                            <v-select :placeholder="fieldHint(formAttribute, 'select')" v-validate="'required'" :name="formAttribute.name"
                                v-model="form.attribute[formAttribute.name]" :options="data[formAttribute.name]"
                                :dir="$vs.rtl ? 'rtl' : 'ltr'" class="mt-1 w-full" />
                            <span class="text-danger text-sm" v-show="errors.has(formAttribute.name)">{{
                                requiredMessage(formAttribute.name)
                                }}</span>
                        </template>
                        <template v-if="formAttribute.hasFormData">
                            <v-select :placeholder="fieldHint(formAttribute, 'select')" v-validate="''"
                                v-on:input="onInitFormData($event, formAttribute.filterObj, formAttribute.name, formAttribute.api)"
                                v-model="form.attribute[formAttribute.name]" :name="formAttribute.name"
                                :options="data[formAttribute.name]" :dir="$vs.rtl ? 'rtl' : 'ltr'"
                                class="mt-2 w-full" />
                        </template>
                    </span>
                    <span v-else>
                        <template v-if="formAttribute.hasFilter">
                            <v-select :placeholder="fieldHint(formAttribute, 'select')" v-validate="''"
                                v-on:input="onInitChange($event, formAttribute.filterObj, formAttribute.name, formAttribute.api)"
                                v-model="form.attribute[formAttribute.name]" :name="formAttribute.name"
                                :options="data[formAttribute.name]" :dir="$vs.rtl ? 'rtl' : 'ltr'"
                                class="mt-2 w-full" />
                        </template>
                        <template v-else>
                            <v-select :placeholder="fieldHint(formAttribute, 'select')" v-validate="''" :name="formAttribute.name"
                                v-model="form.attribute[formAttribute.name]" :options="data[formAttribute.name]"
                                :dir="$vs.rtl ? 'rtl' : 'ltr'" class="mt-1 w-full" />
                        </template>
                    </span>
                </div>

                <div v-if="formAttribute.type == 'select_autocomplete'" :class="styleClass" class="d-field" dclass="mt-4">
                    <label class="mb-2">{{ fieldLabel(formAttribute.name) }} <span
                            v-if="formAttribute.required">*</span></label>
                    <span v-if="formAttribute.required">
                        <vs-select :name="formAttribute.name" @change="onChangeElement($event, formAttribute.name)"
                            v-model="form.attribute[formAttribute.name]" :dir="$vs.rtl ? 'rtl' : 'ltr'"
                            v-validate="'required'" class="mt-1 w-full">
                            <vs-select-item :key="index" :value="item.value" :text="item.label"
                                v-for="(item, index) in data[formAttribute.name]" />
                        </vs-select>
                        <span class="text-danger text-sm" v-show="errors.has(formAttribute.name)">{{ requiredMessage(formAttribute.name)
                        }}</span>
                    </span>
                    <template v-if="formAttribute.hasFormData">
                        <v-select :placeholder="fieldHint(formAttribute, 'select')" v-validate="''"
                            v-on:input="onInitFormData($event, formAttribute.filterObj, formAttribute.name, formAttribute.api)"
                            v-model="form.attribute[formAttribute.name]" :name="formAttribute.name"
                            :options="data[formAttribute.name]" :dir="$vs.rtl ? 'rtl' : 'ltr'" class="mt-2 w-full" />
                    </template>
                    <span v-else>
                        <vs-select :name="formAttribute.name" @change="onChangeElement($event, formAttribute.name)"
                            v-model="form.attribute[formAttribute.name]" class="mt-1 w-full">
                            <vs-select-item :key="index" :value="item.value" :text="item.label"
                                v-for="(item, index) in data[formAttribute.name]" />
                        </vs-select>
                    </span>
                </div>

                <!-- Form Check Box -->
                <div v-if="formAttribute.type == 'checkbox'" :class="styleClass" class="d-field" dclass="mt-4">
                    <label class="mb-2">{{ fieldLabel(formAttribute.name) }} <span
                            v-if="formAttribute.required">*</span></label>
                    <!-- The label above names the field; the box shows its current value -->
                    <span>
                        <vs-checkbox v-model="form.attribute[formAttribute.name]">
                            {{ form.attribute[formAttribute.name] ? $t('btn_yes') : $t('btn_no') }}
                        </vs-checkbox>
                    </span>
                </div>

                <!-- Form Textarea -->
                <div v-if="formAttribute.type == 'textarea'" :class="styleClass" class="d-field d-field--textarea" dclass="mt-4">
                    <label class="mb-2">{{ fieldLabel(formAttribute.name) }} <span v-if="formAttribute.required">*</span></label>
                    <span v-if="formAttribute.required">
                        <vs-textarea v-validate="'required'" class="mt-1 w-full"
                            v-model="form.attribute[formAttribute.name]" />
                        <span class="text-danger text-sm" v-show="errors.has(formAttribute.name)">{{ requiredMessage(formAttribute.name)
                        }}</span>
                    </span>
                    <span v-else>
                        <vs-textarea v-validate="''" class="mt-1 w-full" v-model="form.attribute[formAttribute.name]" />
                    </span>
                </div>

                <!-- Form Date -->
                <div v-if="formAttribute.type == 'date'" :class="styleClass" class="d-field" dclass="mt-4">
                    <label class="mb-2">{{ fieldLabel(formAttribute.name) }} <span v-if="formAttribute.required">*</span></label>
                    <span v-if="formAttribute.required">
                        <flat-pickr :placeholder="fieldHint(formAttribute, 'date')" v-validate="'required'" :name="formAttribute.name"
                            v-model="form.attribute[formAttribute.name]" class="mt-1 w-full" />
                        <span class="text-danger text-sm" v-show="errors.has(formAttribute.name)">{{ requiredMessage(formAttribute.name)
                        }}</span>
                    </span>
                    <span v-else>
                        <flat-pickr :placeholder="fieldHint(formAttribute, 'date')" v-validate="''" v-model="form.attribute[formAttribute.name]" class="mt-1 w-full" />
                    </span>
                </div>

                <!-- Form Group -->
                <div v-if="formAttribute.type == 'text_group'" class="mt-4 vx-col lg:w-1/1 w-full" d>
                    <div>
                        <label class="mb-2">{{ fieldLabel(formAttribute.name) }}</label>
                        <div :key="j" v-for="(childformAttribute, j) in formAttribute.child_form"
                            class="vx-col lg:w-1/1 w-full">
                            <!-- Form Dynamic -->
                            <!-- Form Input Text -->
                            <div v-if="childformAttribute.type == 'text'" class="mt-4 d-field">
                                <label class="mb-2">{{ fieldLabel(childformAttribute.name) }}</label>
                                <span v-if="childformAttribute.required">
                                    <vs-input v-validate="'required'"
                                        v-model="form.attribute[childformAttribute.name]"
                                        :placeholder="fieldHint(childformAttribute)" :name="childformAttribute.name"
                                        class="mt-1 w-full" />
                                    <span class="text-danger text-sm"
                                        v-show="errors.has(childformAttribute.name)">{{
                                            requiredMessage(childformAttribute.name)
                                        }}</span>
                                </span>
                                <vs-input v-else :placeholder="fieldHint(childformAttribute)"
                                    v-model="form.attribute[childformAttribute.name]" :name="childformAttribute.name"
                                    class="mt-1 w-full" />
                            </div>

                            <!-- Form Input Number -->
                            <div v-if="childformAttribute.type == 'number'" class="mt-4 d-field">
                                <label class="mb-2">{{ fieldLabel(childformAttribute.name) }}</label>
                                <span v-if="childformAttribute.required">
                                    <vs-input-number :name="childformAttribute.name"
                                        v-model="form.attribute[childformAttribute.name]" icon-inc="expand_less"
                                        icon-dec="expand_more" class="mt-1 w-full" />
                                    <span class="text-danger text-sm" v-show="errors.has(childformAttribute.name)">{{
                                        requiredMessage(childformAttribute.name)
                                        }}</span>
                                </span>
                                <vs-input-number v-else :name="childformAttribute.name"
                                    v-model="form.attribute[childformAttribute.name]" icon-inc="expand_less"
                                    icon-dec="expand_more" class="mt-1 w-full" />
                            </div>

                            <div v-if="childformAttribute.type == 'select'" class="mt-4 d-field">
                                <label class="mb-2">{{ fieldLabel(childformAttribute.name) }}</label>
                                <span v-if="childformAttribute.required">
                                    <vs-select :name="childformAttribute.name"
                                        v-model="form.attribute[childformAttribute.name]" :dir="$vs.rtl ? 'rtl' : 'ltr'"
                                        v-validate="'required'" class="mt-1 w-full">
                                        <vs-select-item :key="index" :value="item.value" :text="item.label"
                                            v-for="(item, index) in data[childformAttribute.name]" />
                                    </vs-select>
                                    <span class="text-danger text-sm" v-show="errors.has(childformAttribute.name)">{{
                                        requiredMessage(childformAttribute.name)
                                        }}</span>
                                </span>
                                <span v-else>
                                    <vs-select :name="childformAttribute.name"
                                        v-model="form.attribute[childformAttribute.name]" class="mt-1 w-full">
                                        <vs-select-item :key="index" :value="item.value" :text="item.label"
                                            v-for="(item, index) in data[childformAttribute.name]" />
                                    </vs-select>
                                </span>
                            </div>

                            <!-- Form Check Box -->
                            <div v-if="childformAttribute.type == 'checkbox'" class="mt-4 d-field">
                                <label class="mb-2">{{ fieldLabel(childformAttribute.name) }}</label>
                                <span v-if="childformAttribute.required">
                                    <vs-checkbox :key="index" v-model="form.attribute[childformAttribute.name]">
                                        <!-- {{fieldLabel(childformAttribute.name) }}--></vs-checkbox>
                                </span>
                                <span v-else>
                                    <vs-checkbox :key="index" v-model="form.attribute[childformAttribute.name]"
                                        v-for="(rowf, index) in childformAttribute.attributes"> {{ $t(rowf.name)
                                        }}</vs-checkbox>
                                </span>
                            </div>

                            <!-- Form Textarea -->
                            <div v-if="childformAttribute.type == 'textarea'" class="mt-4 d-field">
                                <label class="mb-2">{{ fieldLabel(childformAttribute.name) }}</label>
                                <span v-if="childformAttribute.required">
                                    <vs-textarea v-validate="'required'" class="mt-1 w-full"
                                        v-model="form.attribute[childformAttribute.name]" />
                                    <span class="text-danger text-sm" v-show="errors.has(childformAttribute.name)">{{
                                        requiredMessage(childformAttribute.name)
                                        }}</span>
                                </span>
                                <span v-else>
                                    <vs-textarea class="mt-1 w-full"
                                        v-model="form.attribute[childformAttribute.name]" />
                                </span>
                            </div>

                            <!-- Form Date -->
                            <div v-if="childformAttribute.type == 'date'" class="mt-4 d-field">
                                <label class="mb-2">{{ fieldLabel(childformAttribute.name) }}</label>
                                <span v-if="childformAttribute.required">
                                    <flat-pickr :placeholder="fieldHint(childformAttribute, 'date')" v-validate="'required'" :name="childformAttribute.name"
                                        v-model="form.attribute[childformAttribute.name]" class="mt-1 w-full" />
                                    <span class="text-danger text-sm" v-show="errors.has(childformAttribute.name)">{{
                                        requiredMessage(childformAttribute.name)
                                        }}</span>
                                </span>
                                <span v-else>
                                    <flat-pickr :placeholder="fieldHint(childformAttribute, 'date')" v-model="form.attribute[childformAttribute.name]" class="mt-1 w-full" />
                                </span>
                            </div>
                            <!-- #End Form Dynamic -->

                        </div>
                    </div>
                </div>

                <!-- Form Grid -->
                <div v-if="formAttribute.type == 'grid'" class="mt-4 vx-col lg:w-1/1 w-full">
                    <div style="background-color:#253358; color: #ffffff; padding: 5px;"><span>{{ fieldLabel(formAttribute.name)
                    }}</span></div>
                </div>

                <!-- Form Grid -->
                <!-- Form Input Text -->
                <div v-if="formAttribute.type == 'file'" :class="styleClass" class="d-field d-field--file">
                    <label class="mb-2">{{ fieldLabel(formAttribute.name) }} <span v-if="formAttribute.required">*</span></label>
                    <d-file-picker class="mt-1" :name="formAttribute.name" :value="form.attribute[formAttribute.name]"
                        @browse="initOpenFileManager(formAttribute.name)"
                        @view="initViewFile"
                        @clear="form.attribute[formAttribute.name] = ''" />
                </div>

            </template>
        </div>
        <div class="d-form__footer">
            <p class="d-form__hint"><span>*</span> {{ $t('form_required_hint') }}</p>
            <div class="d-form__buttons">
                <button v-if="$attrs.onCancel" type="button" class="d-form__btn" @click="$emit('cancel')">
                    {{ $t('dt_cancel') }}
                </button>
                <button type="button" class="d-form__btn d-form__btn--primary" :disabled="saving" @click.prevent="submitForm">
                    <feather-icon :icon="saving ? 'LoaderIcon' : (submitIcon || 'CheckIcon')" svgClasses="h-4 w-4" :class="{ 'd-form__spin': saving }" />
                    <span>{{ submitLabel || (dataInfo && dataInfo.id ? $t('form_save_changes') : $t('form_save')) }}</span>
                </button>
            </div>
        </div>
    </div>
</template>

<script>
import { gridClass } from "@/formGrid.js";
import axios from "@/axios.js";
import { fieldLabel, fieldHint, requiredMessage } from "@/i18n/i18n.js";

// Dropdown choices fetched by field name (GET /api/v1/lookup/{field}), shared by every form for the session.
// Used only when the page didn't send a list for that field; fields without a lookup resolve to null.
// Cached per language: labels come back in the language the app sends (X-Locale).
const lookupRequests = {}
function lookupChoices(field, locale) {
    const key = `${locale}:${field}`
    if (!(key in lookupRequests)) {
        lookupRequests[key] = axios.get(`/api/v1/lookup/${field}`)
            .then(({ data }) => (Array.isArray(data) ? data : null))
            .catch(() => null)
    }
    return lookupRequests[key]
}
// Forget a field's cached list (every language), so the next form sees a newly added choice
function forgetLookup(field) {
    Object.keys(lookupRequests).filter(key => key.endsWith(`:${field}`)).forEach(key => delete lookupRequests[key])
}
import { quickAddSource, savedRecord, optionForRecord, optionFromRecord } from "@/quickAdd.js"
import vSelect from 'vue-select';
import flatPickr from 'vue-flatpickr-component';
import 'flatpickr/dist/flatpickr.css';
import DFileManager from '@/views/form-builder/DFileManager.vue';
import DFilePicker from '@/views/form-builder/DFilePicker.vue';
export default {
    name: 'DForm', // its "+ Add" popup is a DForm too
    props: {
        // Main button text and icon when the form does something other than save (e.g. "Send")
        submitLabel: { type: String, default: '' },
        submitIcon: { type: String, default: '' },
        formAttributes: {
            required: Array
        },
        data: {
            required: true
        },
        rowDisplay: {
            required: true
        },
        api: {
            type: String
        },
        dataInfo: {
            required: true
        },
        parentDataInfo: {
            required: true
        },
    },
    data() {
        return {
            saving: false, // a save is in flight: further clicks / Enter are ignored
            quickAdd: null, // { field, api, fields, data } while the "+ Add" popup is up
            quickAddOpen: false,
            dataTables: [],
            dataAttributes: "",
            formAttribute: "",
            dataFields: [],
            showFileManager: false,
            notFoundFile: "/images/not_found.png",
            iconThumnail: "",
            iconDoc: "/images/file-icons/doc.png",
            iconPDF: "/images/file-icons/pdf.png",
            iconExcel: "/images/file-icons/xls.png",
            iconFile: "/images/attach.png",
            form: {
                attribute: {
                    photo: "",
                    image: "",
                    thumbnail: "",
                    file: "",
                    attach: "",
                },
            },
            form_filter: [],
            date: null,
        }
    },
    components: {
        DFileManager,
        DFilePicker,
        vSelect,
        flatPickr,
    },
    computed: {
        // Column classes for every field; follows rowDisplay when a popup picks its layout
        styleClass() {
            return gridClass(this.rowDisplay)
        },
        validateForm() {
            return !this.errors.any()
        }
    },
    methods: {
        loadErrorImage(form_name) {
            this.form.attribute["photo"] = this.notFoundFile;
            this.form.attribute["image"] = this.notFoundFile;
            this.form.attribute["thumbnail"] = this.notFoundFile;
            this.form.attribute["attach"] = this.iconFile;
            this.form.attribute["file"] = this.iconFile;
        },
        initPassProps(props) {
            switch (props.data.type) {
                case "xlsx":
                    this.form.attribute[props.props] = props.data.path;
                    this.iconThumnail = this.iconExcel;
                    break;
                case "xls":
                    this.form.attribute[props.props] = props.data.path;
                    this.iconThumnail = this.iconExcel;
                    break;
                case "doc":
                    this.form.attribute[props.props] = props.data.path;
                    this.iconThumnail = this.iconDoc;
                    break;
                case "docx":
                    this.form.attribute[props.props] = props.data.path;
                    this.iconThumnail = this.iconDoc;
                    break;
                case "pdf":
                    this.form.attribute[props.props] = props.data.path;
                    this.iconThumnail = this.iconPDF;
                    break;
                default:
                    this.form.attribute[props.props] = props.data.path;
                    this.iconThumnail = props.data.path;
                    break;
            }
        },
        initOpenFileManager(props) {
            this.$refs.refModelFileManager.initFileManager(props);
        },
        initViewFile(filePath) {
            let route = this.$router.resolve({ path: filePath });
            window.open(route.href, '_blank');
        },
        canQuickAdd(field) {
            return !!quickAddSource(field)
        },
        // Open the add form for the table behind a dropdown: built from that table's columns
        // (or the fixed form the field names in quickAdd.js)
        async openQuickAdd(field) {
            const source = quickAddSource(field)
            if (!source) return
            let fields = source.form ? source.form(this.data) : null
            if (!Array.isArray(fields) || !fields.length) {
                try {
                    const { data } = await axios.get(`/api/v1/lookup/form/${source.table}`)
                    fields = data
                } catch (error) {
                    const data = error.response && error.response.data
                    this.notify(this.serverMessage(data) || this.$t('form_save_failed'), 'danger')
                    return
                }
            }
            // Its own dropdowns start from this form's lists; missing ones come from the shared lookup
            this.quickAdd = { field, api: source.api, fields, data: { ...(this.data || {}) }, key: Date.now() } // a blank form each time
            // Vuesax puts every popup first in <body> when it is created, and equal z-index stacks by page order:
            // a popup opened from inside another one (3rd level and up) would sit behind it. The one opening goes last.
            const popup = this.$refs.quickAddPopup && this.$refs.quickAddPopup.$refs.con
            if (popup && popup.parentNode === document.body) document.body.appendChild(popup)
            this.quickAddOpen = true
        },
        // A new choice was saved: reload the dropdown's list, pick the new choice, close the popup
        async onQuickAdded(answer) {
            const { field } = this.quickAdd || {}
            this.quickAddOpen = false
            if (!field) return
            const row = savedRecord(answer)
            forgetLookup(field)
            const current = Array.isArray(this.data[field]) ? this.data[field] : []
            let choices = (await lookupChoices(field, this.$i18n.locale)) || current.slice()
            // What was just created is always offered here, even when the shared list leaves it out
            // (e.g. departments are limited to the user's own): the one who added it gets to use it
            if (row && !optionForRecord(choices, row)) choices = [...choices, optionFromRecord(field, row, choices.length ? choices : current)]
            this.data[field] = choices
            const option = optionForRecord(choices, row)
            if (option) this.form.attribute[field] = option
        },
        onInitChange($event, _formAttribute, _formName, _apiRequest) {
            let _data = {
                type: "autocomplete",
                param: this.form.attribute[_formName]
            };
            this.form.attribute[_formAttribute] = null;
            this.data[_formAttribute] = [];

            return new Promise((resolve, reject) => {
                axios.post(_apiRequest, _data)
                    .then((response) => {
                        this.$vs.notify({
                            title: 'Message',
                            text: 'msg.loading',
                            iconPack: 'feather',
                            icon: 'icon-check-circle',
                            color: 'primary',
                            position: 'top-right'
                        });

                        switch (_formAttribute) {
                            case "amount":
                                this.form.attribute["amount"] = response["data"]["data"];
                            break;
                            default:
                                for (let i = 0; i < response["data"]["data"].length; i++) {
                                    this.data[_formAttribute].push(response["data"]["data"][i]);
                                }
                            break;
                        }

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
        onInitFormData($event, _formAttribute, _formName, _apiRequest) {
            let _data = {
                type: "autocomplete",
                param: this.form.attribute[_formName]
            };
            this.form.attribute[_formAttribute] = this.form.attribute[_formName]["label"];
        },
        emitValue(e) {
            let value = e.target.value
            if (this.modelModifiers.capitalize) {
                value = value.charAt(0).toUpperCase() + value.slice(1)
            }
            this.$emit('update:modelValue', value)
        },
        // Just test to create dynamic data table on form
        onChangeElement(e, form_name) {
            this.$emit('clicked', e)
        },
        showNewForm(data) {
            // Show Form
            this.form.attribute = [];
            this.formAttributes.forEach(_formAttribute => {
                if ('default' in _formAttribute) {
                    this.form.attribute[_formAttribute.name] = _formAttribute.default; // starting value, e.g. "active" on
                } else if (_formAttribute["hasDefault"]) {
                    this.form.attribute[_formAttribute["name"]] = _formAttribute["defaultOptions"]["value"];
                } else if (_formAttribute["type"] == "hidden") {
                    this.form.attribute[_formAttribute["name"]] = data["id"];
                }
            });
        },
        showNewFormByParent(obj) {
            this.form.attribute = [];
            this.formAttributes.forEach(_formAttribute => {
                this.form.attribute[_formAttribute["name"]] = "";
                if (_formAttribute["type"] == "select") {
                    this.form.attribute[_formAttribute["name"]] = obj[_formAttribute["name"]];
                }
                if (_formAttribute["name"] == obj.field) {
                    this.form.attribute[_formAttribute["name"]] = obj[_formAttribute["name"]];
                }
            });
        },
        // This function use when you want to edit or show data detail as in popup form
        showDataForm(data) {
            this.formAttributes.forEach(_formAttribute => {
                this.form.attribute[_formAttribute["name"]] = data[_formAttribute["name"]];
                if (_formAttribute["type"] == "select") {
                    // Customize on Data Selection
                    switch (_formAttribute["name"]) {
                        case "officer_id":
                            this.form.attribute[_formAttribute["name"]] = {
                                "label": data["officer"]["first_name"] + " " + data["officer"]["last_name"] + "-" + data["officer"]["identity_member_no"],
                                "value": data[_formAttribute["name"]],
                            };
                            break;
                        case "payment_method_id":
                            if(data["payment_method"]!=null){
                                this.form.attribute[_formAttribute["name"]] = {
                                    "label": data["payment_method"]?data["payment_method"]["name_kh"]:null,
                                    "value": data["payment_method"]?data["payment_method"]["id"]:null,
                                };
                            }
                            break;
                        case "currency_id":
                            this.form.attribute[_formAttribute["name"]] = {
                                "label": data["currency"]?data["currency"]["name_kh"]:null,
                                "value": data["currency"]?data["currency"]["id"]:null,
                            };
                            break;
                        case "status":
                            this.form.attribute[_formAttribute["name"]] = {
                                "label": data["status"]?data["status"]["title_kh"]:null,
                                "value": data["status"]?data["status"]["id"]:null,
                            };
                            break;
                        case "department_id":
                            this.form.attribute[_formAttribute["name"]] = {
                                "label": data["department"]?data["department"]["code"]+":"+data["department"]["name_kh"]:null,
                                "value": data["department"]?data["department"]["id"]:null,
                            };
                            break;
                        case "entity_id":
                            this.form.attribute[_formAttribute["name"]] = {
                                "label": data["entity"]?data["entity"]["code"]+":"+data["entity"]["name_kh"]:null,
                                "value": data["entity"]?data["entity"]["id"]:null,
                            };
                            break;
                        case "payment_term":
                            this.form.attribute[_formAttribute["name"]] = data[_formAttribute["name"]];
                            break;
                        case "is_nssf_member":
                            this.form.attribute[_formAttribute["name"]] = {
                                "label": data["nssf"]?data["nssf"]["title_kh"]:null,
                                "value": data["nssf"]?data["nssf"]["id"]:null,
                            };
                            break;
                        case "role":
                            this.form.attribute[_formAttribute["name"]] = {
                                "label": data["role"]?data["role"]:null,
                                "value": data["role"]?data["role"]:null,
                            };
                            break;
                        default: {
                            // The saved value shows as its option (label) when the choices are loaded, e.g. list_officer -> "List · Members"
                            const raw = data[_formAttribute["name"]];
                            const choices = this.data && Array.isArray(this.data[_formAttribute["name"]]) ? this.data[_formAttribute["name"]] : [];
                            const option = raw !== null && raw !== undefined && raw !== ""
                                ? choices.find(choice => choice && typeof choice === "object" && String(choice.value) === String(raw))
                                : null;
                            this.form.attribute[_formAttribute["name"]] = option || raw;
                            break;
                        }
                    }
                }else if (_formAttribute["type"] == "hidden") {
                    this.form.attribute[_formAttribute["name"]] = data[_formAttribute["name"]];
                } else if (_formAttribute["type"] == "file") {
                    this.form.attribute[_formAttribute["name"]] = data[_formAttribute["name"]] || "";
                    switch (_formAttribute["name"]) {
                        case "photo":
                            this.iconThumnail = data[_formAttribute["name"]];
                            break;
                        case "image":
                            this.iconThumnail = data[_formAttribute["name"]];
                            break;
                        case "thumbnail":
                            this.iconThumnail = data[_formAttribute["name"]];
                            break;
                        case "attach":
                            this.iconThumnail = "";
                            break;
                        default:
                            break;
                    }
                } else {
                    this.form.attribute[_formAttribute["name"]] = data[_formAttribute["name"]];
                }

                if (this.form.attribute[_formAttribute["required"]] == true && data[_formAttribute["name"]] == null) {
                    this.$validator.validateAll().then(result => {
                    })
                }
            });
            this.tidyLoadedValues();
        },
        // Values from the database shown the way a person would type them:
        // a link id of 0 means "none" (placeholder instead of "0"), and decimals drop
        // the database's trailing zeros ("5000.0000" -> 5000)
        tidyLoadedValues() {
            this.formAttributes.forEach(({ name, type }) => {
                const value = this.form.attribute[name]
                if (type === "select" && /_id$/.test(name) && (value === 0 || value === "0" || value === "")) {
                    this.form.attribute[name] = null
                } else if (type === "number" && value !== null && value !== "" && value !== undefined && !isNaN(value)) {
                    this.form.attribute[name] = Number(value)
                }
            })
        },
        onChange(e) {
            this.$validator.validateAll().then(result => {
            });
        },
        // Translated label, borrowing a related field's translation, else a readable name (see i18n.js)
        fieldLabel(name) {
            return fieldLabel(name) // shared with every screen ($tField): see i18n.js
        },
        // Placeholder: the field's own "hint_<name>" text when translated, otherwise a hint by kind.
        // Never the label again (it's already shown above the field).
        fieldHint(attribute, kind = 'text') {
            return fieldHint(attribute && attribute.name, kind) // shared with every screen ($tHint): see i18n.js
        },
        requiredMessage(name) {
            return requiredMessage(name) // shared with every screen ($tRequired): see i18n.js
        },
        // Save the form: validate, send once, keep the spinner until the server answers,
        // then show the server's message (or its validation errors) in the user's language.
        async submitForm() {
            if (this.saving) return
            const payload = this.formAttributes.map(({ type, name }) => {
                const value = this.form.attribute[name]
                return { [name]: type == "select" && value ? value.value : value }
            })
            this.dataFields = payload

            if (!(await this.$validator.validateAll())) {
                this.notify(this.$t('form_check_fields'), 'danger')
                this.$nextTick(() => {
                    const field = this.$el.querySelector('.d-field input[aria-invalid="true"], .d-field .is-invalid, .d-field .text-danger:not([style*="display: none"])')
                    if (field) field.closest('.d-field').scrollIntoView({ behavior: 'smooth', block: 'center' })
                })
                return
            }

            this.saving = true
            this.$vs.loading()
            try {
                const id = this.dataInfo && this.dataInfo.id
                const { data } = id
                    ? await axios.put(this.api + '/' + id, payload)
                    : await axios.post(this.api, payload)
                if (data && data.success === false) {
                    this.notify(this.serverMessage(data) || this.$t('form_save_failed'), 'danger')
                } else {
                    this.notify(this.serverMessage(data) || this.$t('form_save'), 'primary')
                    this.$emit('saved', data) // the server's answer, with the saved row
                    this.$emit('clickForm', '')
                }
            } catch (error) {
                const data = error.response && error.response.data
                this.notify(this.serverMessage(data) || this.$t('form_save_failed'), 'danger')
            } finally {
                this.saving = false
                this.$vs.loading.close()
            }
        },
        serverMessage(data) {
            const message = data && (data.errors || data.message)
            if (!message) return ''
            if (typeof message === 'object') return Object.values(message).flat().join(' ')
            return String(message)
        },
        notify(text, color) {
            this.$vs.notify({ title: 'Message', text, iconPack: 'feather', icon: color === 'danger' ? 'icon-alert-circle' : 'icon-check-circle', color, position: 'top-right' })
        }
    },
    mounted() {
    },
    created() {
    },
    watch: {
        // Dropdowns the page sent no choices for get them from the shared lookup
        formAttributes: {
            immediate: true,
            handler(attributes) {
                // Fields with a starting value (e.g. "active" on) start there on a new form
                (attributes || []).forEach(field => {
                    if (field && 'default' in field && this.form.attribute[field.name] === undefined) {
                        this.form.attribute[field.name] = field.default
                    }
                });
                (attributes || []).filter(field => field && field.type === 'select').forEach(({ name }) => {
                    if (!this.data || Array.isArray(this.data[name])) return
                    lookupChoices(name, this.$i18n.locale).then(choices => {
                        if (choices && this.data && !Array.isArray(this.data[name])) this.data[name] = choices
                    })
                })
            },
        },
    }
}
</script>
<style lang="scss">
// ---------------------------------------------------------------- Standard form layout
.d-form {
    // Field rows, quick-add button and control sizes are shared app-wide: see .d-form in resources/sass/custom.scss

    // Sticky footer
    &__footer {
        position: sticky;
        bottom: -1px;
        z-index: 5;
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        margin: 28px -24px -16px;
        padding: 14px 24px;
        background: #fff;
        border-top: 1px solid #eceef4;
    }

    &__hint {
        font-size: .8rem;
        color: #8a8ca5;

        span {
            color: rgb(var(--vs-danger));
        }
    }

    &__buttons {
        display: flex;
        gap: 10px;
        margin-left: auto;
    }

    &__btn:disabled {
        opacity: .7;
        cursor: progress;
    }

    &__spin svg {
        animation: d-form-spin .8s linear infinite;
    }

    &__btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        height: var(--btn-height);
        min-width: 100px;
        padding: 0 var(--btn-padding-x);
        font-family: inherit;
        font-size: var(--btn-font-size);
        font-weight: var(--btn-font-weight);
        color: #5e6078;
        border: 1px solid #e3e5ee;
        border-radius: var(--btn-radius);
        background: #fff;
        cursor: pointer;

        .feather-icon {
            display: flex;
        }

        &:hover {
            background: #f6f7fb;
        }

        &--primary {
            color: #fff;
            border-color: rgb(var(--vs-primary));
            background: rgb(var(--vs-primary));
            box-shadow: 0 6px 14px -6px rgba(var(--vs-primary), .7);

            &:hover {
                background: rgb(var(--vs-primary));
                filter: brightness(.95);
            }
        }
    }
}

// Popup frame and sizes (.d-modal--sm/md/lg/xl) are shared app-wide: see "POPUPS" in resources/sass/custom.scss

.theme-dark {
    .d-form .d-field > label { color: #c2c6dc; }
    .d-form__footer { background: #262c49; border-color: #414561; }
    .d-form__btn:not(.d-form__btn--primary) { background: #262c49; border-color: #414561; color: #c2c6dc; }
}

@keyframes d-form-spin {
    to { transform: rotate(360deg); }
}
</style>

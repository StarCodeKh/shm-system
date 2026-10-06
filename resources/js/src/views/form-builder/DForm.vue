<template>
    <div class="d-form">
        <!-- <vs-popup fullscreen classContent="popup-example" :title="title"
            :active.sync="showFileManager">
            <form name="file">
                <input type="file" name="file" @change="selectedFile($event)"/>
                <input type="button" value="Upload" @click="initUploadFile()"/>
            </form>
        </vs-popup> -->
        <template title="modal" class="modal">
            <d-file-manager ref="refModelFileManager" @clicked="initPassProps"></d-file-manager>
        </template>

        <vs-popup style="z-index: 5200000;" class="d-modal" classContent="popup-example" :title="$t(title)"
            :active.sync="showModalForm">
            <DFormAttribute :data="data" :formAttributes="formAttributeData" :rowDisplay="rowDisplay" :api="newAPi"
                :dataInfo="dataInfo" :parentDataInfo="parentDataInfo" @clickForm="initTableData" />
        </vs-popup>
        <div class="vx-row">
            <template v-for="(formAttribute, i) in formAttributes">
                <!-- Form Input Text -->
                <div v-if="formAttribute.type == 'text'" :class="styleClass" class="d-field" dclass="mt-4">
                    <label class="mb-2">{{ fieldLabel(formAttribute.name) }} <span v-if="formAttribute.required">*</span></label>
                    <span v-if="formAttribute.required">
                        <vs-input v-validate="'required'" v-model="form.attribute[formAttribute.name]"
                            :placeholder="fieldLabel(formAttribute.name)" :name="formAttribute.name" class="mt-1 w-full" />
                        <span class="text-danger text-sm" v-show="errors.has(formAttribute.name)">{{
                            requiredMessage(formAttribute.name)
                            }}</span>
                    </span>
                    <vs-input v-else v-validate="''" :placeholder="fieldLabel(formAttribute.name)"
                        v-model="form.attribute[formAttribute.name]" :name="formAttribute.name" class="mt-1 w-full" />
                </div>

                <!-- Form Input Number -->
                <!-- <div v-if="formAttribute.type == 'hidden' && formAttribute.required" :class="styleClass" class="d-field" dclass="mt-4"> -->
                <!-- <label>{{ fieldLabel(formAttribute.name) }} -- {{ data[formAttribute.name] }}</label> -->
                <vs-input v-if="formAttribute.type == 'hidden'" type="hidden"
                    v-model="form.attribute[formAttribute.name]" :value="data[formAttribute.name]"
                    :name="formAttribute.name" class="mt-2 w-full" />
                <!-- </div> -->

                <!-- Form Input Text -->
                <div v-if="formAttribute.type == 'password'" :class="styleClass" class="d-field" dclass="mt-4">
                    <label class="mb-2">{{ fieldLabel(formAttribute.name) }} <span v-if="formAttribute.required">*</span></label>
                    <span v-if="formAttribute.required">
                        <vs-input type="password" v-validate="'required'"
                            v-model="form.attribute[formAttribute.name]" :placeholder="fieldLabel(formAttribute.name)"
                            :name="formAttribute.name" class="mt-1 w-full" />
                        <span class="text-danger text-sm" v-show="errors.has(formAttribute.name)">{{
                            requiredMessage(formAttribute.name)
                            }}</span>
                    </span>
                    <vs-input type="password" v-else v-validate="''" :placeholder="fieldLabel(formAttribute.name)"
                        v-model="form.attribute[formAttribute.name]" :name="formAttribute.name" class="mt-1 w-full" />
                </div>

                <!-- Form Input Number -->
                <div v-if="formAttribute.type == 'number'" :class="styleClass" class="d-field" dclass="mt-4">
                    <label class="mb-2">{{ fieldLabel(formAttribute.name) }} <span v-if="formAttribute.required">*</span></label>
                    <span v-if="formAttribute.required">
                        <vs-input type="number" v-validate="'required'" :name="formAttribute.name"
                            v-model="form.attribute[formAttribute.name]" icon-inc="expand_less" icon-dec="expand_more"
                            class="mt-1 w-full" />
                        <span class="text-danger text-sm" v-show="errors.has(formAttribute.name)">
                            {{ requiredMessage(formAttribute.name) }}
                        </span>
                    </span>
                    <vs-input type="number" v-validate="''" v-else :name="formAttribute.name"
                        v-model="form.attribute[formAttribute.name]" icon-inc="expand_less" icon-dec="expand_more"
                        class="mt-1 w-full" />
                </div>

                <div v-if="formAttribute.type == 'select'" :class="styleClass" class="d-field" dclass="mt-4">
                    <label class="mb-2">{{ fieldLabel(formAttribute.name) }} <span
                            v-if="formAttribute.required">*</span></label>
                    <button v-if="variableReturn(formAttribute.name)" type="button" class="d-quick-add"
                        :title="$t('form_quick_add', { field: fieldLabel(formAttribute.name) })"
                        @click.stop="initNewForm(formAttribute.name)">
                        <feather-icon icon="PlusIcon" svgClasses="h-3 w-3" />
                        <span>{{ $t('form_add') }}</span>
                    </button>
                    <span v-if="formAttribute.required">
                        <template v-if="formAttribute.hasFilter">
                            <v-select
                                v-on:input="onInitChange($event, formAttribute.filterObj, formAttribute.name, formAttribute.api)"
                                v-validate="'required'" v-model="form.attribute[formAttribute.name]"
                                :name="formAttribute.name" :options="data[formAttribute.name]"
                                :dir="$vs.rtl ? 'rtl' : 'ltr'" class="mt-2 w-full" />
                            <span class="text-danger text-sm" v-show="errors.has(formAttribute.name)">{{
                                requiredMessage(formAttribute.name)
                                }}</span>
                        </template>
                        <template v-else>
                            <v-select v-validate="'required'" :name="formAttribute.name"
                                v-model="form.attribute[formAttribute.name]" :options="data[formAttribute.name]"
                                :dir="$vs.rtl ? 'rtl' : 'ltr'" class="mt-1 w-full" />
                            <span class="text-danger text-sm" v-show="errors.has(formAttribute.name)">{{
                                requiredMessage(formAttribute.name)
                                }}</span>
                        </template>
                        <template v-if="formAttribute.hasFormData">
                            <v-select v-validate="''"
                                v-on:input="onInitFormData($event, formAttribute.filterObj, formAttribute.name, formAttribute.api)"
                                v-model="form.attribute[formAttribute.name]" :name="formAttribute.name"
                                :options="data[formAttribute.name]" :dir="$vs.rtl ? 'rtl' : 'ltr'"
                                class="mt-2 w-full" />
                        </template>
                    </span>
                    <span v-else>
                        <template v-if="formAttribute.hasFilter">
                            <v-select v-validate="''"
                                v-on:input="onInitChange($event, formAttribute.filterObj, formAttribute.name, formAttribute.api)"
                                v-model="form.attribute[formAttribute.name]" :name="formAttribute.name"
                                :options="data[formAttribute.name]" :dir="$vs.rtl ? 'rtl' : 'ltr'"
                                class="mt-2 w-full" />
                        </template>
                        <template v-else>
                            <v-select v-validate="''" :name="formAttribute.name"
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
                        <v-select v-validate="''"
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
                    <span v-if="formAttribute.required">
                        <vs-checkbox v-model="form.attribute[formAttribute.name]">
                            {{ fieldLabel(formAttribute.name) }}
                        </vs-checkbox>
                    </span>
                    <span v-else>
                        <!-- <vs-checkbox v-model="form.attribute[formAttribute.name]"> {{fieldLabel(formAttribute.name)}}</vs-checkbox> -->
                        <!-- <vs-checkbox v-model="form.attribute[rowf.value]" v-for="(rowf, index) in formAttribute.attributes"> {{ $t(rowf.name)}}</vs-checkbox> -->
                        <vs-checkbox v-model="form.attribute[formAttribute.name]">
                            {{ fieldLabel(formAttribute.name) }}
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
                        <flat-pickr v-validate="'required'" :name="formAttribute.name"
                            v-model="form.attribute[formAttribute.name]" class="mt-1 w-full" />
                        <span class="text-danger text-sm" v-show="errors.has(formAttribute.name)">{{ requiredMessage(formAttribute.name)
                        }}</span>
                    </span>
                    <span v-else>
                        <flat-pickr v-validate="''" v-model="form.attribute[formAttribute.name]" class="mt-1 w-full" />
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
                                        :placeholder="fieldLabel(childformAttribute.name)" :name="childformAttribute.name"
                                        class="mt-1 w-full" />
                                    <span class="text-danger text-sm"
                                        v-show="errors.has(childformAttribute.name)">{{
                                            requiredMessage(childformAttribute.name)
                                        }}</span>
                                </span>
                                <vs-input v-else :placeholder="fieldLabel(childformAttribute.name)"
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
                                    <!-- <vs-checkbox :key="index" v-model="form.attribute[childformAttribute.name]"> {{fieldLabel(childformAttribute.name)}}</vs-checkbox> -->
                                    <!-- <vs-checkbox :key="index" v-model="form.attribute[rowf.value]" v-for="(rowf, index) in childformAttribute.attributes"> {{ $t(rowf.name)}}</vs-checkbox> -->
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
                                    <flat-pickr v-validate="'required'" :name="childformAttribute.name"
                                        v-model="form.attribute[childformAttribute.name]" class="mt-1 w-full" />
                                    <span class="text-danger text-sm" v-show="errors.has(childformAttribute.name)">{{
                                        requiredMessage(childformAttribute.name)
                                        }}</span>
                                </span>
                                <span v-else>
                                    <flat-pickr v-model="form.attribute[childformAttribute.name]" class="mt-1 w-full" />
                                </span>
                            </div>
                            <!-- #End Form Dynamic -->

                        </div>
                    </div>
                </div>

                <!-- Form Grid -->
                <div v-if="formAttribute.type == 'grid'" class="mt-4 vx-col lg:w-1/1 w-full">
                    <!-- <vs-alert icon="warning" active="true" color="primary" class="mt-5"> -->
                    <div style="background-color:#253358; color: #ffffff; padding: 5px;"><span>{{ fieldLabel(formAttribute.name)
                    }}</span></div>
                    <!-- </vs-alert> -->
                </div>

                <!-- Form Grid -->
                <!-- Form Input Text -->
                <div v-if="formAttribute.type == 'file'" class="vx-col lg:w-1/1 w-full mt-4" dclass="mt-4">
                    <label class="mb-2">{{ fieldLabel(formAttribute.name) }} <span v-if="formAttribute.required">*</span></label>
                    <d-file-picker class="mt-1" :name="formAttribute.name" :value="form.attribute[formAttribute.name]"
                        @browse="initOpenFileManager(formAttribute.name)"
                        @view="initViewFile"
                        @clear="$set(form.attribute, formAttribute.name, '')" />
                    <vs-input type="hidden" :placeholder="fieldLabel(formAttribute.name)"
                        v-model="form.attribute[formAttribute.name]" :value="data[formAttribute.name]"
                        class="mt-2 w-full" />
                    <!-- <span v-if="formAttribute.required">
                        <vs-input v-validate="'required'" v-model="form.attribute[formAttribute.name]"
                            :placeholder="fieldLabel(formAttribute.name)" :name="formAttribute.name" class="mt-1 w-full" />
                        <span class="text-danger text-sm" v-show="errors.has(formAttribute.name)">{{
                            requiredMessage(formAttribute.name)
                        }}</span>
                    </span>
                    <vs-input v-else v-validate="''" :placeholder="fieldLabel(formAttribute.name)"
                        v-model="form.attribute[formAttribute.name]" :name="formAttribute.name" class="mt-1 w-full" /> -->
                </div>

            </template>
        </div>
        <div class="d-form__footer">
            <p class="d-form__hint"><span>*</span> {{ $t('form_required_hint') }}</p>
            <div class="d-form__buttons">
                <button v-if="$listeners.cancel" type="button" class="d-form__btn" @click="$emit('cancel')">
                    {{ $t('dt_cancel') }}
                </button>
                <button type="button" class="d-form__btn d-form__btn--primary" @click.prevent="submitForm">
                    <feather-icon icon="CheckIcon" svgClasses="h-4 w-4" />
                    <span>{{ dataInfo && dataInfo.id ? $t('form_save_changes') : $t('form_save') }}</span>
                </button>
            </div>
        </div>
    </div>
</template>

<script>
import { gridClass } from "@/formGrid.js";
import apiConfig from "@/apiConfig.js"
import axios from "@/axios.js";
import formAttr from "@/formAttributes.js"
import vSelect from 'vue-select';
import flatPickr from 'vue-flatpickr-component';
import 'flatpickr/dist/flatpickr.css';
import DFileManager from '@/views/form-builder/DFileManager.vue';
import DFilePicker from '@/views/form-builder/DFilePicker.vue';
import DFormAttribute from '@/views/form-builder/DFormAttribute.vue';
export default {
    props: {
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
            newAPi: null,
            formAttributeData: [],
            dataTables: [],
            dataAttributes: "",
            title: "ទម្រង់បង្កើត",
            styleClass: "",
            formAttribute: "",
            dataFields: [],
            showModalForm: false,
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
        DFormAttribute,
        formAttr
    },
    computed: {
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
                    this.$set(this.form.attribute, props.props, props.data.path);
                    this.iconThumnail = this.iconExcel;
                    break;
                case "xls":
                    this.$set(this.form.attribute, props.props, props.data.path);
                    this.iconThumnail = this.iconExcel;
                    break;
                case "doc":
                    this.$set(this.form.attribute, props.props, props.data.path);
                    this.iconThumnail = this.iconDoc;
                    break;
                case "docx":
                    this.$set(this.form.attribute, props.props, props.data.path);
                    this.iconThumnail = this.iconDoc;
                    break;
                case "pdf":
                    this.$set(this.form.attribute, props.props, props.data.path);
                    this.iconThumnail = this.iconPDF;
                    break;
                default:
                    this.$set(this.form.attribute, props.props, props.data.path);
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
        initTableData(data) {
            var _flag = 1; // mean to enable close modal form
            this.showModalForm = false;
            this.$emit('clickForm', _flag);
        },
        initNewForm(param) {
            switch (param) {
                case "account_group_id":
                    this.title = "account_group_id";
                    this.newAPi = apiConfig._apiAccountGroup;
                    this.formAttributeData = formAttr.formAttributeAccountGroup;
                    break;

                case "account_type_group_id":
                    this.title = "account_group_id";
                    this.newAPi = apiConfig._apiAccountTypeGroup;
                    this.formAttributeData = formAttr.formAttributeAccountTypeGroup;
                    break;
                case "account_type_id":
                    this.title = "account_type_id";
                    this.newAPi = apiConfig._apiAccountType;
                    this.formAttributeData = formAttr.formAttributeAccountType;
                    break;
                case "account_id":
                    this.title = "account_id";
                    this.newAPi = apiConfig._apiAccount;
                    this.formAttributeData = formAttr.formAttributeAccount;
                    break;
                case "sub_account_id":
                    this.title = "sub_account_id";
                    this.newAPi = "";
                    this.formAttributeData = formAttr.formAttributeAccount;
                    break;
                case "category_id":
                    this.title = "category_id";
                    this.newAPi = apiConfig._apiItemCategory;
                    this.formAttributeData = formAttr.formAttributeItemCategory;
                    break;
                case "item_id":
                    this.title = "item_id";
                    this.newAPi = apiConfig._apiItem;
                    this.formAttributeData = formAttr.formAttributeItem;
                    break;
                case "role_id":
                    this.title = "role_id";
                    this.newAPi = apiConfig._apiRole;
                    this.formAttributeData = formAttr.formAttributeRole;
                    break;
                case "unit_id":
                    this.title = "unit_id";
                    this.newAPi = apiConfig._apiUnit;
                    this.formAttributeData = formAttr.formAttributeUnit;
                    break;
                case "user_id":
                    this.title = "user_id";
                    this.newAPi = apiConfig._apiUser;
                    this.formAttributeData = formAttr.formAttributeUser;
                    break;
                case "title":
                    this.title = "title";
                    this.newAPi = apiConfig._apiTitle;
                    this.formAttributeData = formAttr.formAttributeTitle;
                    break;
                case "department":
                    this.title = "department";
                    this.newAPi = apiConfig._apiDepartment;
                    this.formAttributeData = formAttr.formAttributeDepartment;
                    break;
                case "department_id":
                    this.title = "department_id";
                    this.newAPi = apiConfig._apiDepartment;
                    this.formAttributeData = formAttr.formAttributeDepartment;
                    break;
                case "entity_id":
                    this.title = "entity_id";
                    this.newAPi = apiConfig._apiEntity;
                    this.formAttributeData = formAttr.formAttributeEntity;
                    break;
                case "entity_member_id":
                    this.title = "entity_member_id";
                    this.newAPi = apiConfig._apiEntityMember;
                    this.formAttributeData = formAttr.formAttributeEntityMember;
                    break;
                case "position":
                    this.title = "position";
                    this.newAPi = apiConfig._apiPosition;
                    this.formAttributeData = formAttr.formAttributePosition;
                    break;
                case "ceiling_group_id":
                    this.title = "ceiling_group_id";
                    this.newAPi = apiConfig._apiCeilingGroup;
                    this.formAttributeData = formAttr.formAttributeCeilingGroup;
                    break;
                case "ceiling_type_id":
                    this.title = "ceiling_type_id";
                    this.newAPi = apiConfig._apiCeilingType;
                    this.formAttributeData = formAttr.formAttributeCeilingType;
                    break;
                case "objective_id":
                    this.title = "objective_id";
                    this.newAPi = apiConfig._apiObjective;
                    this.formAttributeData = formAttr.formAttributeObjective;
                    break;

                case "program_id":
                    this.title = "program_id";
                    this.newAPi = apiConfig._apiProgram;
                    this.formAttributeData = formAttr.formAttributeProgram;
                    break;
                case "sub_program_id":
                    this.title = "sub_program_id";
                    this.newAPi = apiConfig._apiSubProgram;
                    this.formAttributeData = formAttr.formAttributeSubProgram;
                    break;
                case "cluster_activity_id":
                    this.title = "cluster_activity_id";
                    this.newAPi = apiConfig._apiClusterActivity;
                    this.formAttributeData = formAttr.formAttributeClusterActivity;
                    break;
                case "activity_id":
                    this.title = "activity_id";
                    this.newAPi = apiConfig._apiActivity;
                    this.formAttributeData = formAttr.formAttributeActivity;
                    break;

                case "currency":
                    this.title = "currency";
                    this.newAPi = apiConfig._apiCurrency;
                    this.formAttributeData = formAttr.formAttributeCurrency;
                    break;
                case "request_title":
                    this.title = "request_title";
                    this.newAPi = apiConfig._apiPaymentType;
                    this.formAttributeData = this.data.form_payment_type;
                    break;
                default:
                    this.formAttributeData = null;
                    break;
            }
            this.$emit('clickOpenNewPop', param);
            // this.$ref.refModalOpenNewPop.initOpenNewPop();
            this.showModalForm = true;
        },
        onInitChange($event, _formAttribute, _formName, _apiRequest) {
            let _data = {
                type: "autocomplete",
                param: this.form.attribute[_formName]
            };
            this.form.attribute[_formAttribute] = null;
            this.data[_formAttribute] = [];
        
            // this.data[_formAttribute].push([
            //     {
            //         "label": "sdf",
            //         "value": "1"
            //     }
            // ]);
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
                                    // let _d = {
                                    //     "label": response["data"]["data"][i]["label"],
                                    //     "value": response["data"]["data"][i]["value"],
                                    // }
                                    // this.data[_formAttribute].push(_d);
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
            console.log("welcome", this.form.attribute[_formName]);
            let _data = {
                type: "autocomplete",
                param: this.form.attribute[_formName]
            };
            this.form.attribute[_formAttribute] = this.form.attribute[_formName]["label"];
            // this.data[_formAttribute] = [];
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
            // this.form.attribute[form_name] = "";
            this.$emit('clicked', e)
            // this.$refs.refInitPushDataTable.initPushDataTable(form_name);
        },
        showNewForm(data) {
            // Show Form 
            this.form.attribute = [];
            this.formAttributes.forEach(_formAttribute => {
                if (_formAttribute["hasDefault"]) {
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
                    // this.form.attribute[_formAttribute["name"]] = "";
                }
                if (_formAttribute["name"] == obj.field) {
                    this.form.attribute[_formAttribute["name"]] = obj[_formAttribute["name"]];
                }
                // if (_formAttribute["type"] == "select") {
                //     this.form.attribute[_formAttribute["name"]] = "";
                // }
                // if (_formAttribute["name"] == obj.field) {
                //     this.form.attribute[obj.field] = obj.id;
                // }
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
                        default:
                            this.form.attribute[_formAttribute["name"]] = data[_formAttribute["name"]];
                            break;
                    }
                    // alert(_formAttribute["name"]);
                }else if (_formAttribute["type"] == "hidden") {
                    this.form.attribute[_formAttribute["name"]] = data[_formAttribute["name"]];
                } else if (_formAttribute["type"] == "file") {
                    this.$set(this.form.attribute, _formAttribute["name"], data[_formAttribute["name"]] || "");
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
            // this.$emit('clickForm', "e") // Temporary Close Command
        },
        onChange(e) {
            this.$validator.validateAll().then(result => {
            });
        },
        // Translated label, or a readable fallback for keys without a translation (parent_code -> Parent code)
        fieldLabel(name) {
            if (!name) return ''
            if (this.$te(name)) return this.$t(name)
            const text = String(name).replace(/_id$/, '').replace(/_/g, ' ').trim()
            return text.charAt(0).toUpperCase() + text.slice(1)
        },
        requiredMessage(name) {
            const key = 'required_' + name
            return this.$te(key) ? this.$t(key) : this.$t('form_required', { field: this.fieldLabel(name) })
        },
        submitForm() {
            let _d;
            this.dataFields = [];
            let _formAttribute = this.formAttributes;
            this.formAttributes.forEach(_formAttribute => {
                if (_formAttribute["type"] == "select") {
                    if (typeof this.form.attribute[_formAttribute["name"]] !== 'undefined' && this.form.attribute[_formAttribute["name"]]) {
                        _d = {
                            [_formAttribute["name"]]: this.form.attribute[_formAttribute["name"]].value
                        }
                    } else {
                        _d = {
                            [_formAttribute["name"]]: this.form.attribute[_formAttribute["name"]]
                        }
                    }
                } else {
                    _d = {
                        [_formAttribute["name"]]: this.form.attribute[_formAttribute["name"]]
                    }
                }
                this.dataFields.push(_d);
            });
            this.$validator.validateAll().then(result => {
                if (result) {
                    let _data = this.dataFields;
                    this.$vs.loading();
                    if (this.dataInfo.id) {
                        let _id = this.dataInfo.id;
                        // this.$vs.loading.close();
                        return new Promise((resolve, reject) => {
                            axios.put(this.api + '/' + _id, _data)
                                .then((response) => {
                                    if (response.data.success == false) {
                                        this.$vs.notify({
                                            title: 'Message',
                                            text: response.data.message,
                                            iconPack: 'feather',
                                            icon: 'icon-check-circle',
                                            color: 'danger',
                                            position: 'top-right'
                                        })
                                    } else {
                                        this.$vs.notify({
                                            title: 'Message',
                                            text: response.data.message,
                                            iconPack: 'feather',
                                            icon: 'icon-check-circle',
                                            color: 'primary',
                                            position: 'top-right'
                                        })
                                        this.$emit('clickForm', '');
                                    }
                                    this.$vs.loading.close();
                                    // this.$router.push('/account/expense').catch(() => { })
                                }).catch((error) => {
                                    reject(error)
                                    this.$vs.notify({
                                        title: 'Message',
                                        text: 'msg.check_criteria',
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
                                    if (response.data.success) {
                                        this.$vs.notify({
                                            title: 'Message',
                                            text: response.data.message,
                                            iconPack: 'feather',
                                            icon: 'icon-check-circle',
                                            color: 'primary',
                                            position: 'top-right'
                                        })
                                        this.$vs.loading.close();
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
                                        return false;
                                    }

                                    this.$emit('clickForm', '');
                                    // this.$router.push('/account/expense').catch(() => { })
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
                }
                this.$vs.loading.close();
            })
        },
        variableReturn(condition) {
            switch (condition) {
                case "status":
                    return false;
                    break;
                case "title":
                    return false;
                    break;
                case "gender":
                    return false;
                    break;
                case "year":
                    return false;
                    break;
                case "officer_id":
                    return false;
                    break;
                case "currency_id":
                    return false;
                    break;
                case "payment_method_id":
                    return false;
                    break;
                case "department_id":
                    return false;
                    break;
                case "entity_id":
                    return false;
                    break;
                case "account_code":
                    return false;
                    break;
                default:
                    return true;
                    break;
            }
        }
    },
    mounted() {
    },
    created() {
        // this.form.attribute["budget_template_id"] = 11;
        this.styleClass = gridClass(this.rowDisplay);
    },
    watch: {

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

  &__btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    height: 42px;
    min-width: 110px;
    padding: 0 18px;
    font-family: inherit;
    font-size: .92rem;
    font-weight: 600;
    color: #5e6078;
    border: 1px solid #e3e5ee;
    border-radius: 10px;
    background: #fff;
    cursor: pointer;
    transition: background .15s, filter .15s;

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
</style>

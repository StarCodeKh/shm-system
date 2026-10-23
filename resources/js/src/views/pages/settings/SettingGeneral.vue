<template>
    <vx-card no-shadow class="d-form">

        <!-- Img Row -->

        <!-- <DForm @clickForm="initTableData" ref="refModalForm" :data="data" :dataInfo="dataInfo"
            :parentDataInfo="parentDataInfo" :formAttributes="formAttributes" :api="api"
            :rowDisplay="rowDisplay"/> -->

            <d-file-manager ref="refModelFileManager" @clicked="initPassProps"></d-file-manager>
        <p v-if="!canUpdate" class="setting-readonly-note">{{ $t('setting_read_only') }}</p>
        <fieldset class="setting-fieldset" :disabled="!canUpdate">
        <div class="vx-row">
            <template v-for="(formAttribute, i) in formAttributes">
                <!-- Form Input Text -->
                <div v-if="formAttribute.type == 'text'" :class="styleClass" class="d-field" dclass="mt-4">
                    <label class="mb-2">{{ $tField(formAttribute.name) }}</label>
                    <span v-if="formAttribute.required">
                        <vs-input v-validate="'required'" v-model="form.attribute[formAttribute.name]"
                            :placeholder="$tField(formAttribute.name)" :name="formAttribute.name" class="mt-1 w-full" />
                        <span class="text-danger text-sm" v-show="errors.has(formAttribute.name)">{{
                            errors.first(formAttribute.name)
                        }}</span>
                    </span>
                    <vs-input v-else v-validate="''" :placeholder="$tField(formAttribute.name)"
                        v-model="form.attribute[formAttribute.name]" :name="formAttribute.name" class="mt-1 w-full" />
                </div>

                <!-- Social link: brand icon inside the input -->
                <div v-if="formAttribute.type == 'social'" :class="styleClass" class="d-field" dclass="mt-4">
                    <label class="mb-2">{{ $tField(formAttribute.name) }}</label>
                    <div class="d-social">
                        <span class="d-social__icon" :style="{ color: formAttribute.color }">
                            <feather-icon :icon="formAttribute.icon" svgClasses="h-5 w-5" />
                        </span>
                        <vs-input type="url" v-validate="''" v-model="form.attribute[formAttribute.name]"
                            :placeholder="formAttribute.placeholder" :name="formAttribute.name" class="w-full" />
                    </div>
                </div>

                <!-- Form Input Number -->
                    <input v-if="formAttribute.type == 'hidden' && formAttribute.required" type="hidden"
                        v-model="form.attribute[formAttribute.name]" :name="formAttribute.name" />

                <!-- Form Input Text -->
                <div v-if="formAttribute.type == 'password'" :class="styleClass" class="d-field" dclass="mt-4">
                    <label class="mb-2">{{ $tField(formAttribute.name) }}</label>
                    <span v-if="formAttribute.required">
                        <vs-input type="password" v-validate="'required'"
                            v-model="form.attribute[formAttribute.name]" :placeholder="secretHint(formAttribute.name)"
                            autocomplete="new-password" :name="formAttribute.name" class="mt-1 w-full" />
                        <span class="text-danger text-sm" v-show="errors.has(formAttribute.name)">{{
                            errors.first(formAttribute.name)
                        }}</span>
                    </span>
                    <vs-input type="password" v-else v-validate="''" :placeholder="secretHint(formAttribute.name)"
                        autocomplete="new-password" v-model="form.attribute[formAttribute.name]" :name="formAttribute.name" class="mt-1 w-full" />
                </div>

                <!-- Form Input Number -->
                <div v-if="formAttribute.type == 'number'" :class="styleClass" class="d-field" dclass="mt-4">
                    <label class="mb-2">{{ $tField(formAttribute.name) }}</label>
                    <span v-if="formAttribute.required">
                        <vs-input type="number" v-validate="'required'" :name="formAttribute.name"
                            v-model="form.attribute[formAttribute.name]" icon-inc="expand_less" icon-dec="expand_more"
                            class="mt-1 w-full" />
                        <span class="text-danger text-sm" v-show="errors.has(formAttribute.name)">
                            {{ errors.first(formAttribute.name) }}
                        </span>
                    </span>
                    <vs-input type="number" v-validate="''" v-else :name="formAttribute.name"
                        v-model="form.attribute[formAttribute.name]" icon-inc="expand_less" icon-dec="expand_more"
                        class="mt-1 w-full" />
                </div>

                <div v-if="formAttribute.type == 'select'" :class="styleClass" class="d-field" dclass="mt-4">
                    <label class="mb-2">{{ $tField(formAttribute.name) }} <span
                        v-if="formAttribute.required">*</span></label>
                    <button v-if="formAttribute.name != 'year' && formAttribute.name != 'gender'" type="button"
                        class="d-quick-add" :title="$t('form_quick_add', { field: $tField(formAttribute.name) })"
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
                                errors.first(formAttribute.name)
                            }}</span>
                        </template>
                        <template v-if="formAttribute.hasFormData">
                            <v-select v-validate="''"
                                v-on:input="onInitFormData($event, formAttribute.filterObj, formAttribute.name, formAttribute.api)"
                                v-model="form.attribute[formAttribute.name]" :name="formAttribute.name"
                                :options="data[formAttribute.name]" :dir="$vs.rtl ? 'rtl' : 'ltr'"
                                class="mt-2 w-full" />
                        </template>
                        <template v-else>
                            <v-select v-validate="'required'" :name="formAttribute.name"
                                v-model="form.attribute[formAttribute.name]" :options="data[formAttribute.name]"
                                :dir="$vs.rtl ? 'rtl' : 'ltr'" class="mt-1 w-full" />
                            <span class="text-danger text-sm" v-show="errors.has(formAttribute.name)">{{
                                errors.first(formAttribute.name)
                            }}</span>
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
                    <label class="mb-2">{{ $tField(formAttribute.name) }} - {{ form.attribute[formAttribute.name] }}</label>
                    <span v-if="formAttribute.required">
                        <vs-select :name="formAttribute.name" @change="onChangeElement($event, formAttribute.name)"
                            v-model="form.attribute[formAttribute.name]" :dir="$vs.rtl ? 'rtl' : 'ltr'"
                            v-validate="'required'" class="mt-1 w-full">
                            <vs-select-item :key="index" :value="item.value" :text="item.label"
                                v-for="(item, index) in data[formAttribute.name]" />
                        </vs-select>
                        <span class="text-danger text-sm" v-show="errors.has(formAttribute.name)">{{ errors.first(formAttribute.name)
                            }}</span>
                    </span>
                    <template v-if="formAttribute.hasFormData">
                        <v-select v-validate="''"
                            v-on:input="onInitFormData($event, formAttribute.filterObj, formAttribute.name, formAttribute.api)"
                            v-model="form.attribute[formAttribute.name]" :name="formAttribute.name"
                            :options="data[formAttribute.name]" :dir="$vs.rtl ? 'rtl' : 'ltr'"
                            class="mt-2 w-full" />
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
                    <label class="mb-2">{{ $tField(formAttribute.name) }}</label>
                    <span v-if="formAttribute.required">
                        <vs-checkbox v-model="form.attribute[formAttribute.name]">
                            {{ $tField(formAttribute.name) }}
                        </vs-checkbox>
                    </span>
                    <span v-else>
                        <vs-checkbox v-model="form.attribute[formAttribute.name]">
                            {{ $tField(formAttribute.name) }}
                        </vs-checkbox>
                    </span>
                </div>

                <!-- Form Textarea -->
                <div v-if="formAttribute.type == 'textarea'" :class="styleClass" class="d-field d-field--textarea" dclass="mt-4">
                    <label class="mb-2">{{ $tField(formAttribute.name) }}</label>
                    <span v-if="formAttribute.required">
                        <vs-textarea v-validate="'required'" class="mt-1 w-full"
                            v-model="form.attribute[formAttribute.name]" />
                        <span class="text-danger text-sm" v-show="errors.has(formAttribute.name)">{{ errors.first(formAttribute.name)
                            }}</span>
                    </span>
                    <span v-else>
                        <vs-textarea v-validate="''" class="mt-1 w-full" v-model="form.attribute[formAttribute.name]" />
                    </span>
                </div>

                <!-- Form Date -->
                <div v-if="formAttribute.type == 'date'" :class="styleClass" class="d-field" dclass="mt-4">
                    <label class="mb-2">{{ $tField(formAttribute.name) }}</label>
                    <span v-if="formAttribute.required">
                        <flat-pickr v-validate="'required'" :name="formAttribute.name"
                            v-model="form.attribute[formAttribute.name]" class="mt-1 w-full" />
                        <span class="text-danger text-sm" v-show="errors.has(formAttribute.name)">{{ errors.first(formAttribute.name)
                            }}</span>
                    </span>
                    <span v-else>
                        <flat-pickr v-validate="''" v-model="form.attribute[formAttribute.name]" class="mt-1 w-full" />
                    </span>
                </div>

                <!-- Form Group -->
                <div v-if="formAttribute.type == 'text_group'" class="mt-4 vx-col lg:w-1/1 w-full" d>
                    <div>
                        <label class="mb-2">{{ $tField(formAttribute.name) }}</label>
                        <div :key="j" v-for="(childformAttribute, j) in formAttribute.child_form"
                            class="vx-col lg:w-1/1 w-full">

                            <!-- Form Dynamic -->
                            <!-- Form Input Text -->
                            <div v-if="childformAttribute.type == 'text'" class="mt-4 d-field">
                                <label class="mb-2">{{ $tField(childformAttribute.name) }}</label>
                                <span v-if="childformAttribute.required">
                                    <vs-input v-validate="'required'"
                                        v-model="form.attribute[childformAttribute.name]"
                                        :placeholder="$tField(childformAttribute.name)" :name="childformAttribute.name"
                                        class="mt-1 w-full" />
                                    <span class="text-danger text-sm"
                                        v-show="errors.has(childformAttribute.name)">{{
                                            errors.first(childformAttribute.name)
                                        }}</span>
                                </span>
                                <vs-input v-else :placeholder="$tField(childformAttribute.name)"
                                    v-model="form.attribute[childformAttribute.name]" :name="childformAttribute.name"
                                    class="mt-1 w-full" />
                            </div>

                            <!-- Form Input Number -->
                            <div v-if="childformAttribute.type == 'number'" class="mt-4 d-field">
                                <label class="mb-2">{{ $tField(childformAttribute.name) }}</label>
                                <span v-if="childformAttribute.required">
                                    <vs-input-number :name="childformAttribute.name"
                                        v-model="form.attribute[childformAttribute.name]" icon-inc="expand_less"
                                        icon-dec="expand_more" class="mt-1 w-full" />
                                    <span class="text-danger text-sm" v-show="errors.has(childformAttribute.name)">{{
                                        errors.first(childformAttribute.name)
                                    }}</span>
                                </span>
                                <vs-input-number v-else :name="childformAttribute.name"
                                    v-model="form.attribute[childformAttribute.name]" icon-inc="expand_less"
                                    icon-dec="expand_more" class="mt-1 w-full" />
                            </div>

                            <div v-if="childformAttribute.type == 'select'" class="mt-4 d-field">
                                <label class="mb-2">{{ $tField(childformAttribute.name) }}</label>
                                <span v-if="childformAttribute.required">
                                    <vs-select :name="childformAttribute.name"
                                        v-model="form.attribute[childformAttribute.name]" :dir="$vs.rtl ? 'rtl' : 'ltr'"
                                        v-validate="'required'" class="mt-1 w-full">
                                        <vs-select-item :key="index" :value="item.value" :text="item.label"
                                            v-for="(item, index) in data[childformAttribute.name]" />
                                    </vs-select>
                                    <span class="text-danger text-sm" v-show="errors.has(childformAttribute.name)">{{
                                        errors.first(childformAttribute.name)
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
                                <label class="mb-2">{{ $tField(childformAttribute.name) }}</label>
                                <span v-if="childformAttribute.required">
                                    <vs-checkbox :key="index" v-model="form.attribute[childformAttribute.name]">
                                        <!-- {{$tField(childformAttribute.name) }}--></vs-checkbox>
                                </span>
                                <span v-else>
                                    <vs-checkbox :key="index" v-model="form.attribute[childformAttribute.name]"
                                        v-for="(rowf, index) in childformAttribute.attributes"> {{ $t(rowf.name)
                                        }}</vs-checkbox>
                                </span>
                            </div>

                            <!-- Form Textarea -->
                            <div v-if="childformAttribute.type == 'textarea'" class="mt-4 d-field">
                                <label class="mb-2">{{ $tField(childformAttribute.name) }}</label>
                                <span v-if="childformAttribute.required">
                                    <vs-textarea v-validate="'required'" class="mt-1 w-full"
                                        v-model="form.attribute[childformAttribute.name]" />
                                    <span class="text-danger text-sm" v-show="errors.has(childformAttribute.name)">{{
                                        errors.first(childformAttribute.name)
                                    }}</span>
                                </span>
                                <span v-else>
                                    <vs-textarea class="mt-1 w-full"
                                        v-model="form.attribute[childformAttribute.name]" />
                                </span>
                            </div>

                            <!-- Form Date -->
                            <div v-if="childformAttribute.type == 'date'" class="mt-4 d-field">
                                <label class="mb-2">{{ $tField(childformAttribute.name) }}</label>
                                <span v-if="childformAttribute.required">
                                    <flat-pickr v-validate="'required'" :name="childformAttribute.name"
                                        v-model="form.attribute[childformAttribute.name]" class="mt-1 w-full" />
                                    <span class="text-danger text-sm" v-show="errors.has(childformAttribute.name)">{{
                                        errors.first(childformAttribute.name)
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
                    <div style="background-color:#253358; color: #ffffff; padding: 5px;"><span>{{ $tField(formAttribute.name)
                        }}</span></div>
                </div>

                <!-- Form Grid -->
                <!-- Form Input Text -->
                <div v-if="formAttribute.type == 'file'" :class="styleClass" class="d-field" dclass="mt-4">
                    <label class="mb-2">{{ $tField(formAttribute.name) }}</label>
                    <d-file-picker class="mt-1" :name="formAttribute.name" :value="form.attribute[formAttribute.name]"
                        @browse="initOpenFileManager(formAttribute.name)"
                        @view="initViewFile"
                        @clear="form.attribute[formAttribute.name] = ''" />
                </div>

            </template>
        </div>

        </fieldset>

        <span v-if="canUpdate" class="pull-right">
            <vs-button type="filled" :disabled="saving" @click.prevent="submitForm" class="mt-5 block">{{
                $t("btn_save") }}</vs-button>
        </span>

        <!-- Save & Reset Button -->

    </vx-card>
</template>

<script>
import { gridClass } from "@/formGrid.js";
import axios from "@/axios.js"
import { saveForm, formPayload } from "@/formSave.js"
import { loadSite } from "@/site.js"
import vSelect from 'vue-select';
import apiConfig from "@/apiConfig.js"
import DFileManager from '@/views/form-builder/DFileManager.vue';
import DFilePicker from '@/views/form-builder/DFilePicker.vue';
export default {
    components: {
        vSelect,
        DFileManager,
        DFilePicker
    },
    data() {
        return {
            saving: false, // a save is in flight: further clicks are ignored
            data: {
                config_country: []
            },
            dataInfo: null,
            parentDataInfo: null,
            api: apiConfig._apiConfiguration,
            rowDisplay: "3grid",
            formAttributes: [
                {
                    name: "config_logo",
                    type: "file",
                    required: false,
                },
                {
                    name: "config_owner",
                    type: "textarea",
                    required: true
                },
                {
                    name: "config_address",
                    type: "textarea",
                    required: true
                },

                {
                    name: "config_planning_year",
                    type: "select",
                    required: false,
                    options: [
                        {
                            label: "",
                            value: ""
                        }
                    ]
                },

                {
                    name: "config_currency",
                    type: "select",
                    required: false,
                    options: []
                },
                {
                    name: "config_date_format",
                    type: "select",
                    required: false,
                    options: []
                },
                {
                    name: "config_allow_file_extension",
                    type: "textarea",
                    required: false
                },
                {
                    name: "config_comment",
                    type: "textarea",
                    required: false
                },
                {
                    name: "config_language",
                    type: "select",
                    required: false,
                    options: []
                },
                {
                    name: "config_limit",
                    type: "number",
                    required: true,
                },
                {
                    name: "config_default_page",
                    type: "number",
                    required: false
                },
                {
                    name: "config_email",
                    type: "textarea",
                    required: false
                },
                {
                    name: "config_fax",
                    type: "textarea",
                    required: false
                },
                {
                    name: "config_FIRE_BASE_SERVER_API_KEY",
                    type: "password",
                    required: false
                },
                {
                    name: "config_FIRE_BASE_SERVER_URL",
                    type: "textarea",
                    required: false
                },
                {
                    name: "config_footer",
                    type: "textarea",
                    required: false
                },
                {
                    name: "config_footer_template",
                    type: "textarea",
                    required: false
                },
                {
                    name: "config_google_map",
                    type: "textarea",
                    required: false
                },

                {
                    name: "config_header_template",
                    type: "textarea",
                    required: false
                },
                {
                    name: "config_khmerdate",
                    type: "textarea",
                    required: false
                },
                {
                    name: "config_mail_engine",
                    type: "textarea",
                    required: false
                },
                {
                    name: "config_dir_image",
                    type: "textarea",
                    required: false
                },
                {
                    name: "config_mail_parameter",
                    type: "textarea",
                    required: false
                },
                {
                    name: "config_mail_smtp_hostname",
                    type: "textarea",
                    required: false
                },
                {
                    name: "config_mail_smtp_password",
                    type: "password",
                    required: false
                },
                {
                    name: "config_mail_smtp_port",
                    type: "textarea",
                    required: false
                },
                {
                    name: "config_mail_smtp_time_out",
                    type: "textarea",
                    required: false
                },
                {
                    name: "config_mail_smtp_username",
                    type: "textarea",
                    required: false
                },
                {
                    name: "config_max_file_size",
                    type: "textarea",
                    required: false
                },
                {
                    name: "config_menu",
                    type: "textarea",
                    required: false
                },
                {
                    name: "config_meta_tag_description",
                    type: "textarea",
                    required: false
                },
                {
                    name: "config_meta_tag_keyword",
                    type: "textarea",
                    required: false
                },
                {
                    name: "config_meta_title",
                    type: "textarea",
                    required: false
                },
                {
                    name: "config_name",
                    type: "textarea",
                    required: false
                },
                {
                    name: "config_offset",
                    type: "textarea",
                    required: false
                },
                {
                    name: "config_open_time",
                    type: "textarea",
                    required: false
                },
                // Social links: brand icon inside the input, one row of three
                { name: "config_social_facebook", type: "social", icon: "FacebookIcon", color: "#1877F2", placeholder: "https://facebook.com/...", required: false },
                { name: "config_social_telegram", type: "social", icon: "SendIcon", color: "#229ED9", placeholder: "https://t.me/...", required: false },
                { name: "config_social_youtube", type: "social", icon: "YoutubeIcon", color: "#FF0000", placeholder: "https://youtube.com/@...", required: false },
            ],
            showFileManager: false,
            notFoundFile: "/images/not_found.png",
            iconThumnail: "",
            canUpdate: true, // from the API: may this role save the settings
            secretKeys: [], // secrets set on the server; their value is never sent here
            iconDoc: "/images/file-icons/doc.png",
            iconPDF: "/images/file-icons/pdf.png",
            iconExcel: "/images/file-icons/xls.png",
            form: {
                attribute: {
                    photo: "",
                    image: "",
                    thumbnail: "",
                    file: "",
                    attach: "",
                    config_logo: "",
                },
                attribute: { config_address: "" },
                attribute: { config_allow_file_extension: "" },
                attribute: { config_comment: "" },
                attribute: { config_planning_year: "" },
                attribute: {
                    config_country: [
                        {
                            label: "",
                            value: ""
                        }
                    ]
                },
                attribute: { config_currency: "" },
                attribute: { config_date_format: "" },
                attribute: { config_default_page: "" },
                attribute: { config_dir_image: "" },
                attribute: { config_email: "" },
                attribute: { config_fax: "" },
                attribute: { config_FIRE_BASE_SERVER_API_KEY: "" },
                attribute: { config_FIRE_BASE_SERVER_URL: "" },
                attribute: { config_footer: "" },
                attribute: { config_footer_template: "" },
                attribute: { config_header_template: "" },
                attribute: { config_icon: "" },
                attribute: { config_owner: "" },
                attribute: { config_khmerdate: "" },
                attribute: {
                    config_language: {
                        "label": "En",
                        "value": "En"
                    }
                },
                attribute: {
                    config_limit: [
                        {
                            label: "10",
                            value: "10"
                        }
                    ]
                },
                attribute: { config_mail_engine: "" },
                attribute: { config_mail_parameter: "" },
                attribute: { config_mail_smtp_hostname: "" },
                attribute: { config_mail_smtp_password: "" },
                attribute: { config_mail_smtp_port: "" },
                attribute: { config_mail_smtp_time_out: "" },
                attribute: { config_mail_smtp_username: "" },
                attribute: { config_max_file_size: "" },
                attribute: { config_menu: "" },
                attribute: { config_meta_tag_description: "" },
                attribute: { config_meta_tag_keyword: "" },
                attribute: { config_meta_title: "" },
                attribute: { config_name: "" },
                attribute: { config_offset: "" },
                attribute: { config_open_time: "" },
                attribute: { config_social_facebook: "" },
                attribute: { config_social_youtube: "" },
                attribute: { config_social_telegram: "" },
                attribute: { config_google_map: "" },
            },
            dataAttributes: [{
                page_number: ""
            }
            ]
        }
    },
    methods: {
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
                axios.get(this.api, {
                    params: _params
                }).then((response) => {
                    if (response.data) {
                        this.data = response.data;
                    } else {
                        this.data = this.data;
                    }
                    this.canUpdate = this.data.can_update !== false;
                    this.secretKeys = this.data.secret_keys || [];
                    const saved = this.data.data || {};
                    this.formAttributes.forEach(_formAttribute => {
                        const name = _formAttribute["name"];
                        let value = saved[name];
                        if (_formAttribute["type"] == "select") {
                            // The API stores the option value (e.g. 1); v-select needs the matching {label, value} option
                            const options = this.data[name] || [];
                            value = options.find(option => option && String(option.value) === String(value)) || value;
                        } else if (_formAttribute["type"] == "file") {
                            this.iconThumnail = value;
                        }
                        // Set key by key so v-model and the validation see the loaded values
                        this.form.attribute[name] = value;
                    });
                    // Loaded values are valid until edited; drop errors from the empty initial state
                    this.$nextTick(() => this.$validator.reset());

                    this.$vs.loading.close();
                }).catch((error) => {
this.$vs.notify({
    title: 'Message',
    text: 'msg.action_failed',
    iconPack: 'feather',
    icon: 'icon-check-circle',
    color: 'danger'
    })
    this.$vs.loading.close();
    })
    })
    },
    getData() {
        let _search_criteria = {
            sort: "id",
            order: "",
            page_number: this.dataAttributes.page_number,
            search_field: ""
        }
        this.getDataTable(_search_criteria);
    },
    loadErrorImage(form_name){
        this.form.attribute["photo"] = this.notFoundFile;
        this.form.attribute["image"] = this.notFoundFile;
        this.form.attribute["thumbnail"] = this.notFoundFile;
        this.form.attribute["attach"] = this.notFoundFile;
        this.form.attribute["file"] = this.notFoundFile;
        this.form.attribute["config_logo"] = this.notFoundFile;
    },
    initPassProps(props){
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
    initOpenFileManager(props){
        this.$refs.refModelFileManager.initFileManager(props);
    },
    initViewFile(filePath){
        let route = this.$router.resolve({path: filePath});
        window.open(route.href, '_blank');
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
    // Secrets come back empty: say that leaving them blank keeps the value saved on the server
    secretHint(name) {
        return this.secretKeys.includes(name) ? this.$t('setting_secret_keep') : this.$tField(name);
    },
    // The standard save (formSave.js): every field checked, one save at a time, the server's field errors under their fields
    submitForm() {
        const payload = formPayload(this.formAttributes.map(({ name }) => name), this.form.attribute)
        this.dataFields = payload
        return saveForm(this, () => axios.put(this.api + '/update', payload), {
            onSaved: () => {
                loadSite(true) // the footer shows the new name / organisation right away
                this.$emit('clickForm', '')
            },
        })
    }
    },
    created() {
        this.styleClass = gridClass(this.rowDisplay);
        this.$vs.loading();
        this.getData();
    },
    computed: {
    }
}
</script>

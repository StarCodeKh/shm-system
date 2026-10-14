<template>
    <div class="officer-form">
            <d-file-manager ref="refModelFileManager" @clicked="initPassProps"></d-file-manager>
        <d-form-tabs ref="tabs" v-model="tab" :sections="tabSections" :errors="errors" icon="UserPlusIcon"
            :title="$t('officer_form_title')" :subtitle="$t('officer_form_subtitle')">
            <form id="memberForm" class="officer-form__form" @submit.prevent="submitForm">
                <!-- Personal Information Section -->
                <div class="form-section" data-section="personal" v-show="tab === 'personal'">
                    <div class="form-grid">

                        <div class="form-group">
                            <label>{{ $t('of_photo') }}</label>
                            <d-file-picker name="photo" :value="officer.photo || ''"
                                @browse="initOpenFileManager('photo')"
                                @clear="officer.photo = null; iconThumnail = null" />
                        </div>

                        <div class="form-group">
                            <label>{{ $t('first_name') }} <span class="required"></span></label>
                            <div class="input-icon">
                                <i class="fas fa-user"></i>
                                <vs-input v-validate="'required'" v-model="officer.first_name" :placeholder="$tHint('first_name')" :name="'first_name'" class="mt-1 w-full" />
                                <span class="text-danger text-sm" v-show="errors.has('first_name')">{{ errors.first('first_name') }}</span>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>{{ $t('last_name') }} <span class="required"></span></label>
                            <div class="input-icon">
                                <i class="fas fa-user"></i>
                                 <vs-input v-validate="'required'" v-model="officer.last_name" :placeholder="$tHint('last_name')" :name="'last_name'" class="mt-1 w-full" />
                                <span class="text-danger text-sm" v-show="errors.has('last_name')">{{ errors.first('last_name') }}</span>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>{{ $t('latin') }} <span class="required"></span></label>
                            <div class="input-icon">
                                <i class="fas fa-user"></i>
                                 <vs-input v-validate="'required'" v-model="officer.latin" :placeholder="$tHint('latin')" :name="'latin'" class="mt-1 w-full" />
                                <span class="text-danger text-sm" v-show="errors.has('latin')">{{ errors.first('latin') }}</span>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>{{ $t('gender') }} <span class="required"></span></label>
                            <v-select :placeholder="$tHint('gender', 'select')"
                                    v-validate="'required'" v-model="officer.gender"
                                    name="gender" :options="translated(genders)"
                                    :dir="$vs.rtl ? 'rtl' : 'ltr'" class="mt-2 w-full" />
                                    <span class="text-danger text-sm" v-show="errors.has('gender')">{{ errors.first('gender') }}</span>
                        </div>

                        <div class="form-group">
                            <label>{{ $t('of_dob') }} <span class="required"></span></label>
                             <flat-pickr :placeholder="$tHint('of_dob', 'date')" v-validate="'required'" name="dob"
                                v-model="officer.dob" class="mt-1 w-full" />
                            <span class="text-danger text-sm" v-show="errors.has('dob')">{{ errors.first('dob') }}</span>
                        </div>

                        <div class="form-group">
                            <label>{{ $t('of_identity_no') }}</label>
                            <div class="input-icon">
                                <i class="fas fa-id-card"></i>
                                <vs-input v-model="officer.identity_no" :placeholder="$tHint('of_identity_no')" :name="'identity_no'" class="mt-1 w-full" />
                            </div>
                        </div>

                        <div class="form-group">
                            <label>{{ $tField('nationality') }}</label>
                            <vs-input v-model="officer.nationality" :placeholder="$tHint('nationality')" :name="'nationality'" class="mt-1 w-full" />
                        </div>

                        <div class="form-group">
                            <label>{{ $t('of_married_status') }}</label>
                            <v-select :placeholder="$tHint('of_married_status', 'select')" v-model="officer.married_status"
                                    name="married_status" :options="translated(marriedStatus)"
                                    :dir="$vs.rtl ? 'rtl' : 'ltr'" class="mt-2 w-full" />
                        </div>

                    </div>
                </div>

                <!-- Contact Information Section -->
                <div class="form-section" data-section="work" v-show="tab === 'work'">
                    <div class="form-grid">

                        <div class="form-group">
                            <label>{{ $t('of_entity') }} <span class="required"></span></label>
                            <div class="input-icon">
                                <i class="fas fa-phone"></i>
                                <v-select :placeholder="$tHint('of_entity', 'select')"
                                    v-validate="'required'" v-model="officer.entity_id"
                                    name="entity_id" :options="entities"
                                    :dir="$vs.rtl ? 'rtl' : 'ltr'" class="mt-2 w-full" />
                                    <span class="text-danger text-sm" v-show="errors.has('entity_id')">{{ errors.first('entity_id') }}</span>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>{{ $tField('department_id') }} <span class="required"></span></label>
                            <div class="input-icon">
                                <i class="fas fa-envelope"></i>
                                <v-select :placeholder="officer.entity_id ? $tHint('department_id', 'select') : $t('form_select_parent_first', { field: $tField('entity_id') })"
                                    v-validate="'required'" v-model="officer.department_id"
                                    name="department_id" :options="dependentOptions.department_id || []"
                                    :dir="$vs.rtl ? 'rtl' : 'ltr'" class="mt-2 w-full" />
                                    <span class="text-danger text-sm" v-show="errors.has('department_id')">{{ errors.first('department_id') }}</span>
                                <!-- <input type="email" placeholder="example@email.com"> -->
                            </div>
                        </div>
                        <div class="form-group">
                            <label>{{ $tField('title') }} <span class="required"></span></label>
                            <div class="input-icon">
                                <i class="fas fa-phone"></i>
                                <v-select :placeholder="$tHint('title', 'select')"
                                    v-validate="'required'" v-model="officer.title"
                                    name="title" :options="titles"
                                    :dir="$vs.rtl ? 'rtl' : 'ltr'" class="mt-2 w-full" />
                                    <span class="text-danger text-sm" v-show="errors.has('title')">{{ errors.first('title') }}</span>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>{{ $t('of_duty') }} <span class="required"></span></label>
                            <div class="input-icon">
                                <i class="fas fa-envelope"></i>
                                <v-select :placeholder="$tHint('of_duty', 'select')"
                                    v-validate="'required'" v-model="officer.position"
                                    name="position" :options="positions"
                                    :dir="$vs.rtl ? 'rtl' : 'ltr'" class="mt-2 w-full" />
                                    <span class="text-danger text-sm" v-show="errors.has('position')">{{ errors.first('position') }}</span>
                            </div>
                        </div>

                    </div>
                </div>

                <!-- Contact Information Section -->
                <div class="form-section" data-section="contact" v-show="tab === 'contact'">
                    <div class="form-grid">
                        <div class="form-group">
                            <label>{{ $t('of_phone') }} <span class="required"></span></label>
                            <div class="input-icon">
                                <i class="fas fa-phone"></i>
                                <vs-input v-validate="'required'" v-model="officer.telephone_1" :placeholder="$tHint('of_phone')" :name="'telephone_1'" class="mt-1 w-full" />
                                <span class="text-danger text-sm" v-show="errors.has('telephone_1')">{{ errors.first('telephone_1') }}</span>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>{{ $t('of_email') }}</label>
                            <div class="input-icon">
                                <i class="fas fa-envelope"></i>
                                <vs-input v-model="officer.email" :placeholder="$tHint('of_email')" :name="'email'" class="mt-1 w-full" />
                                <!-- <input type="email" placeholder="example@email.com" v-model="officer.email"> -->
                            </div>
                        </div>

                        <div class="form-group">
                            <label>{{ $t('social_media_fb') }}</label>
                            <div class="input-icon">
                                <i class="fab fa-facebook"></i>
                                <vs-input v-model="officer.social_media_fb" :placeholder="$tHint('social_media_fb')" :name="'social_media_fb'" class="mt-1 w-full" />
                            </div>
                        </div>

                        <div class="form-group">
                            <label>{{ $t('social_media_telegram') }}</label>
                            <div class="input-icon">
                                <i class="fab fa-telegram"></i>
                                 <vs-input v-model="officer.social_media_telegram" :placeholder="$tHint('social_media_telegram')" :name="'social_media_telegram'" class="mt-1 w-full" />
                            </div>
                        </div>

                        <div class="form-group">
                            <label>{{ $t('social_media_whatsapp') }}</label>
                            <div class="input-icon">
                                <i class="fab fa-whatsapp"></i>
                                <vs-input v-model="officer.social_media_whatsapp" :placeholder="$tHint('social_media_whatsapp')" :name="'social_media_whatsapp'" class="mt-1 w-full" />
                            </div>
                        </div>

                        <div class="form-group">
                            <label>{{ $t('of_social') }}</label>
                            <vs-input v-model="officer.social_media_alt" :placeholder="$tHint('of_social')" :name="'social_media_alt'" class="mt-1 w-full" />
                        </div>
                    </div>
                </div>

                <!-- Emergency Contact Section -->
                <div class="form-section" data-section="emergency" v-show="tab === 'emergency'">
                    <div class="form-grid">
                        <div class="form-group">
                            <label>{{ $t('of_emergency_person') }}</label>
                            <vs-input v-model="officer.emergency_person" :placeholder="$tHint('of_emergency_person')" :name="'emergency_person'" class="mt-1 w-full" />
                        </div>

                        <div class="form-group">
                            <label>{{ $t('of_emergency_relative') }}</label>
                            <v-select :placeholder="$tHint('of_emergency_relative', 'select')" v-model="officer.emergency_relative"
                                    name="emergencyRelatives" :options="translated(emergencyRelatives)"
                                    :dir="$vs.rtl ? 'rtl' : 'ltr'" class="mt-2 w-full" />
                        </div>

                        <div class="form-group">
                            <label>{{ $t('of_emergency_phone') }}</label>
                            <vs-input v-model="officer.telephone_2" :placeholder="$tHint('of_emergency_phone')" :name="'telephone_2'" class="mt-1 w-full" />
                        </div>
                    </div>
                </div>

                <!-- Additional Information -->
                <div class="form-section" data-section="additional" v-show="tab === 'additional'">
                    <div class="form-grid">
                        <div class="form-group full-width">
                            <label>{{ $t('of_remark') }}</label>
                             <vs-textarea v-model="officer.remark" :placeholder="$tHint('of_remark')" :name="'remark'" class="mt-1 w-full" />
                        </div>

                    </div>
                </div>

                <!-- Status Section -->
                <div class="form-section" data-section="status" v-show="tab === 'status'">

                    <div class="form-grid">
                        <div class="form-group">
                            <label>{{ $t('of_register_date') }} <span class="required"></span></label>
                                <flat-pickr :placeholder="$tHint('of_register_date', 'date')" v-validate="'required'" name="register_date"
                                v-model="officer.register_date" class="mt-1 w-full" />
                            <span class="text-danger text-sm" v-show="errors.has('register_date')">{{ errors.first('register_date') }}</span>
                        </div>
                        <div class="form-group">
                            <label>{{ $t('of_nssf') }} <span class="required"></span></label>
                            <v-select :placeholder="$tHint('of_nssf', 'select')"
                                    v-validate="'required'" v-model="officer.is_nssf_member"
                                    name="is_nssf_member" :options="nssfs"
                                    :dir="$vs.rtl ? 'rtl' : 'ltr'" class="mt-2 w-full" />
                                    <span class="text-danger text-sm" v-show="errors.has('is_nssf_member')">{{ errors.first('is_nssf_member') }}</span>
                        </div>

                        <div class="form-group">
                            <label>{{ $tField('status') }} <span class="required"></span></label>
                            <v-select :placeholder="$tHint('status', 'select')"
                                    v-validate="'required'" v-model="officer.status"
                                    name="status" :options="statuses"
                                    :dir="$vs.rtl ? 'rtl' : 'ltr'" class="mt-2 w-full" />
                                    <span class="text-danger text-sm" v-show="errors.has('status')">{{ errors.first('status') }}</span>
                        </div>

                    </div>
                </div>

            </form>
            <template #actions>
                <button type="button" class="d-tabform__btn" @click="clearForm()">
                    <feather-icon icon="RotateCcwIcon" svgClasses="h-4 w-4" />
                    <span>{{ $t('form_reset') }}</span>
                </button>
                <button type="button" class="d-tabform__btn d-tabform__btn--primary" :disabled="saving" @click="submitForm">
                    <feather-icon icon="SaveIcon" svgClasses="h-4 w-4" />
                    <span>{{ $t("btn_process") }}</span>
                </button>
            </template>
        </d-form-tabs>
    </div>
</template>
<script>
import axios from "@/axios.js";
import apiConfig from "@/apiConfig.js";
import flatPickr from 'vue-flatpickr-component';
import 'flatpickr/dist/flatpickr.css';
import vSelect from 'vue-select';
import DFileManager from '@/views/form-builder/DFileManager.vue';
import { watchDependents } from "@/dependentFields.js";
import { saveForm, formPayload } from "@/formSave.js";

export default {
    components: {
        vSelect,
        flatPickr,
        DFileManager
        // DataViewSidebar
    },
    data() {
        return {
            tab: 'personal',
            sections: [
                { key: 'personal', icon: 'UserIcon', titleKey: 'of_tab_personal' },
                { key: 'work', icon: 'BriefcaseIcon', titleKey: 'of_tab_work' },
                { key: 'contact', icon: 'PhoneIcon', titleKey: 'of_tab_contact' },
                { key: 'emergency', icon: 'AlertCircleIcon', titleKey: 'of_tab_emergency' },
                { key: 'additional', icon: 'InfoIcon', titleKey: 'of_tab_additional' },
                { key: 'status', icon: 'CheckCircleIcon', titleKey: 'of_tab_status' },
            ],
            api: apiConfig._apiOfficer,
            selected: [],
            products: [],
            itemsPerPage: 4,
            isMounted: false,
            // Every field of the form, sent as it is (a picked choice sends its value)
            officer: {
                photo: null,
                first_name: null,
                last_name: null,
                latin: null,
                gender: null,
                dob: null,
                identity_no: null,
                register_date: null,
                nationality: null,
                title: null,
                duty: null,
                department_id: null,
                entity_id: null,
                address: null,
                email: null,
                bank: null,
                bank_code: null,
                bank_account_no: null,
                updated_bank_acc: null,
                position: null,
                telephone_1: null,
                emergency_person: null,
                emergency_relative: null,
                telephone_2: null,
                is_nssf_member: null,
                nssf_card_no: null,
                order_level: null,
                married_status: null,
                status: null,
                social_media_fb: null,
                social_media_telegram: null,
                social_media_whatsapp: null,
                social_media_alt: null,
                remark: null,
            },
            saving: false, // a save is in flight: further clicks are ignored
                iconExcel: "../images/file-icons/xls.png",
                iconDoc: "../images/file-icons/doc.png",
                iconPDF: "../images/file-icons/pdf.png",
            iconThumnail: null,
            genders: [],
            titles: [],
            entities: [],
            dependentOptions: {}, // departments of the chosen unit (dependentFields.js)
            positions: [],
            nssfs: [],
            statuses: [],
            // Loaded from the officer form endpoint (mef_status lookups)
            marriedStatus: [],
            emergencyRelatives: [],
            // Data Sidebar
        }
    },
    computed: {
        // Tab titles follow the current language
        tabSections() {
            return this.sections.map(section => ({ ...section, title: this.$t(section.titleKey) }))
        },
        validateForm() {
            return !this.errors.any()
        },
    },
    methods: {
        // Option labels in the current language; the stored value stays the same
        translated(options) {
            const en = this.$i18n.locale === 'en'
            return (options || []).map(option => en && option.label_en ? { ...option, label: option.label_en } : option)
        },
        // Empty form again: every field, no messages, back at the first step
        clearForm() {
            Object.keys(this.officer).forEach(name => { this.officer[name] = null })
            this.iconThumnail = null
            this.tab = this.sections[0].key
            this.$nextTick(() => this.$validator.reset())
        },
        initPassProps(props) {
            switch (props.data.type) {
                case "xlsx":
                    this.officer.photo = props.data.path;
                    this.iconThumnail = this.iconExcel;
                    break;
                case "xls":
                    this.officer.photo = props.data.path;
                    this.iconThumnail = this.iconExcel;
                    break;
                case "doc":
                    this.officer.photo = props.data.path;
                    this.iconThumnail = this.iconDoc;
                    break;
                case "docx":
                    this.officer.photo = props.data.path;
                    this.iconThumnail = this.iconDoc;
                    break;
                case "pdf":
                    this.officer.photo = props.data.path;
                    this.iconThumnail = this.iconPDF;
                    break;
                default:
                    this.officer.photo = props.data.path;
                    this.iconThumnail = props.data.path;
                    break;
            }
        },
        initOpenFileManager(props) {
            this.$refs.refModelFileManager.initFileManager(props);
        },
        getData(){
            return new Promise((resolve, reject) => {
                axios.get(this.api+"/form")
                    .then((response) => {
                        this.entities = response.data.data.entity_id;
                        this.positions = response.data.data.position;
                        this.titles = response.data.data.title;
                        this.genders = response.data.data.gender;
                        this.marriedStatus = response.data.data.married_status || [];
                        this.emergencyRelatives = response.data.data.emergency_relative || [];
                        this.nssfs = response.data.data.is_nssf_member;
                        this.statuses = response.data.data.status;
                        // console.log("Payable Form", response.data);
                        // resolve(response.data);   // return data
                    })
                    .catch((error) => {
                        console.error(error);
                        reject(error);            // return error
                    });
            });
        },
        // The standard save (formSave.js): a step with a problem is opened, the form empties after saving
        submitForm() {
            const payload = formPayload(Object.keys(this.officer), this.officer)
            return saveForm(this, () => axios.post(this.api, payload), {
                onInvalid: () => this.$refs.tabs.focusFirstError(),
                onSaved: () => this.clearForm(),
            })
        },
    },
    created() {
        this.getData();
        watchDependents(this, ['entity_id', 'department_id'], () => this.officer);
    },
    mounted() {
        this.isMounted = true;
    }
}
</script>

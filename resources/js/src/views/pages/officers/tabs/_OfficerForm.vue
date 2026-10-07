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
                                <!-- <input type="text" ​name="first_name" :placeholder="$tHint('first_name')" v-model="officer.first_name" v-validate="'required'"> -->
                                <vs-input v-validate="'required'" v-model="officer.first_name" :placeholder="$tHint('first_name')" :name="'first_name'" class="mt-1 w-full" />
                                <span class="text-danger text-sm" v-show="errors.has('first_name')">{{ $tRequired('first_name')
                                }}</span>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>{{ $t('last_name') }} <span class="required"></span></label>
                            <div class="input-icon">
                                <i class="fas fa-user"></i>
                                <!-- <input type="text" :placeholder="$tHint('last_name')" v-model="officer.last_name" required> -->
                                 <vs-input v-validate="'required'" v-model="officer.last_name" :placeholder="$tHint('last_name')" :name="'last_name'" class="mt-1 w-full" />
                                <span class="text-danger text-sm" v-show="errors.has('last_name')">{{ $tRequired('last_name')
                                }}</span>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>{{ $t('latin') }} <span class="required"></span></label>
                            <div class="input-icon">
                                <i class="fas fa-user"></i>
                                <!-- <input type="text" :placeholder="$tHint('latin')" v-model="officer.latin" required> -->
                                 <vs-input v-validate="'required'" v-model="officer.latin" :placeholder="$tHint('latin')" :name="'latin'" class="mt-1 w-full" />
                                <span class="text-danger text-sm" v-show="errors.has('latin')">{{ $tRequired('latin')
                                }}</span>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>{{ $t('gender') }} <span class="required"></span></label>
                            <v-select :placeholder="$tHint('gender', 'select')"
                                    v-validate="'required'" v-model="officer.gender"
                                    name="gender" :options="translated(genders)"
                                    :dir="$vs.rtl ? 'rtl' : 'ltr'" class="mt-2 w-full" />
                                    <span class="text-danger text-sm" v-show="errors.has('gender')">{{
                                        $tRequired('gender')
                                        }}</span>
                            <!-- <select required  v-model="officer.gender">
                                <option value="">ជ្រើសរើសភេទ</option>
                                <option value="male">ប្រុស (Male)</option>
                                <option value="female">ស្រី (Female)</option>
                            </select> -->
                        </div>

                        <div class="form-group">
                            <label>{{ $t('of_dob') }} <span class="required"></span></label>
                            <!-- <input type="date" v-model="officer.dob" required> -->
                             <flat-pickr :placeholder="$tHint('of_dob', 'date')" v-validate="'required'" name="dob"
                                v-model="officer.dob" class="mt-1 w-full" />
                            <span class="text-danger text-sm" v-show="errors.has('dob')">{{ $tRequired('dob')
                            }}</span>
                        </div>

                        <div class="form-group">
                            <label>{{ $t('of_identity_no') }}</label>
                            <div class="input-icon">
                                <i class="fas fa-id-card"></i>
                                <vs-input v-model="officer.identity_no" :placeholder="$tHint('of_identity_no')" :name="'identity_no'" class="mt-1 w-full" />
                                <!-- <input type="text" placeholder="បញ្ចូលលេខអត្តសញ្ញាណប័ណ្ណ" v-model="officer.identity_no" required> -->
                            </div>
                        </div>

                        <div class="form-group">
                            <label>{{ $t('of_nationality') }}</label>
                            <vs-input v-model="officer.nationality" :placeholder="$tHint('of_nationality')" :name="'nationality'" class="mt-1 w-full" />
                            <!-- <input type="text" placeholder="សញ្ជាតិ" v-model="officer.nationality" required> -->
                            <!-- <select required>
                                <option value="">ជ្រើសរើសសញ្ជាតិ</option>
                                <option value="khmer">ខ្មែរ</option>
                                <option value="other">ផ្សេងៗ</option>
                            </select> -->
                        </div>

                        <!-- <div class="form-group">
                            <label>សាសនា</label>
                            <select>
                                <option value="">ជ្រើសរើសសាសនា</option>
                                <option value="buddhism">ព្រះពុទ្ធ</option>
                                <option value="islam">ឥស្ាម</option>
                                <option value="christianity">គ្រិស្ត</option>
                                <option value="other">ផ្សេងៗ</option>
                            </select>
                        </div> -->

                        <div class="form-group">
                            <label>{{ $t('of_married_status') }}</label>
                            <v-select :placeholder="$tHint('of_married_status', 'select')" v-model="officer.married_status"
                                    name="married_status" :options="translated(marriedStatus)"
                                    :dir="$vs.rtl ? 'rtl' : 'ltr'" class="mt-2 w-full" />
                        </div>

                        <!-- <div class="form-group">
                            <label>កម្រិតវប្បធម៌</label>
                            <select>
                                <option value="">ជ្រើសរើសកម្រិត</option>
                                <option value="primary">បឋមសិក្សា</option>
                                <option value="secondary">មធ្យមសិក្សា</option>
                                <option value="highschool">វិទ្យាល័យ</option>
                                <option value="bachelor">បរិញ្ញាបត្រ</option>
                                <option value="master">បរិញ្ញាបត្រជាន់ខ្ពស់</option>
                                <option value="phd">បណ្ឌិត</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>មុខរបរ</label>
                            <input type="text" placeholder="បញ្ចូលមុខរបរ">
                        </div>

                        <div class="form-group">
                            <label>អាសយដ្ឋានបច្ចុប្បន្ន</label>
                            <textarea placeholder="បញ្ចូលអាសយដ្ឋាន"></textarea>
                        </div> -->
                    </div>
                </div>

                <!-- Contact Information Section -->
                <div class="form-section" data-section="work" v-show="tab === 'work'">
                    <div class="form-grid">

                        <div class="form-group">
                            <label>{{ $t('of_department') }} <span class="required"></span></label>
                            <div class="input-icon">
                                <i class="fas fa-envelope"></i>
                                <v-select :placeholder="$tHint('of_department', 'select')"
                                    v-on:input="onInitChange($event, '', '', apiEntityByDept)"
                                    v-validate="'required'" v-model="officer.department_id"
                                    name="department_id" :options="departments"
                                    :dir="$vs.rtl ? 'rtl' : 'ltr'" class="mt-2 w-full" />
                                    <span class="text-danger text-sm" v-show="errors.has('department_id')">{{
                                        $tRequired('department_id')
                                        }}</span>
                                <!-- <input type="email" placeholder="example@email.com"> -->
                            </div>
                        </div>

                        <div class="form-group">
                            <label>{{ $t('of_entity') }} <span class="required"></span></label>
                            <div class="input-icon">
                                <i class="fas fa-phone"></i>
                                <v-select :placeholder="$tHint('of_entity', 'select')"
                                    v-validate="'required'" v-model="officer.entity_id"
                                    name="entity_id" :options="entities"
                                    :dir="$vs.rtl ? 'rtl' : 'ltr'" class="mt-2 w-full" />
                                    <span class="text-danger text-sm" v-show="errors.has('entity_id')">{{
                                        $tRequired('entity_id')
                                        }}</span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>{{ $t('of_title') }} <span class="required"></span></label>
                            <div class="input-icon">
                                <i class="fas fa-phone"></i>
                                <v-select :placeholder="$tHint('of_title', 'select')"
                                    v-validate="'required'" v-model="officer.title"
                                    name="title" :options="titles"
                                    :dir="$vs.rtl ? 'rtl' : 'ltr'" class="mt-2 w-full" />
                                    <span class="text-danger text-sm" v-show="errors.has('title')">{{
                                        $tRequired('title')
                                        }}</span>
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
                                    <span class="text-danger text-sm" v-show="errors.has('position')">{{
                                        $tRequired('position')
                                        }}</span>
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
                                <span class="text-danger text-sm" v-show="errors.has('telephone_1')">{{ $tRequired('telephone_1')
                                }}</span>
                                <!-- <input type="tel" placeholder="012 xxx xxx" v-model="officer.telephone_1" required> -->
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
                                <!-- <input type="text" placeholder="Facebook Profile" v-model="officer.social_media_fb"> -->
                                <vs-input v-model="officer.social_media_fb" :placeholder="$tHint('social_media_fb')" :name="'social_media_fb'" class="mt-1 w-full" />
                            </div>
                        </div>

                        <div class="form-group">
                            <label>{{ $t('social_media_telegram') }}</label>
                            <div class="input-icon">
                                <i class="fab fa-telegram"></i>
                                <!-- <input type="text" placeholder="Telegram Username" v-model="officer.social_media_telegram"> -->
                                 <vs-input v-model="officer.social_media_telegram" :placeholder="$tHint('social_media_telegram')" :name="'social_media_telegram'" class="mt-1 w-full" />
                            </div>
                        </div>

                        <div class="form-group">
                            <label>{{ $t('social_media_whatsapp') }}</label>
                            <div class="input-icon">
                                <i class="fab fa-whatsapp"></i>
                                <!-- <input type="tel" placeholder="WhatsApp Number" v-model="officer.social_media_whatsapp"> -->
                                <vs-input v-model="officer.social_media_whatsapp" :placeholder="$tHint('social_media_whatsapp')" :name="'social_media_whatsapp'" class="mt-1 w-full" />
                            </div>
                        </div>

                        <div class="form-group">
                            <label>{{ $t('of_social') }}</label>
                            <!-- <input type="text" placeholder="បញ្ចូលបណ្តាញសង្គមផ្សេង" v-model="officer.social_media_alt"> -->
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
                            <!-- <input type="text" placeholder="ឈ្មោះអ្នកទំនាក់ទំនង" v-model="officer.emergency_person"> -->
                        </div>

                        <div class="form-group">
                            <label>{{ $t('of_emergency_relative') }}</label>
                            <v-select :placeholder="$tHint('of_emergency_relative', 'select')" v-model="officer.emergency_relative"
                                    name="emergencyRelatives" :options="translated(emergencyRelatives)"
                                    :dir="$vs.rtl ? 'rtl' : 'ltr'" class="mt-2 w-full" />
                        </div>

                        <div class="form-group">
                            <label>{{ $t('of_emergency_phone') }}</label>
                            <!-- <input type="tel" placeholder="012 xxx xxx" v-model="officer.telephone_2"> -->
                            <vs-input v-model="officer.telephone_2" :placeholder="$tHint('of_emergency_phone')" :name="'telephone_2'" class="mt-1 w-full" />
                        </div>
                    </div>
                </div>

                <!-- Additional Information -->
                <div class="form-section" data-section="additional" v-show="tab === 'additional'">
                    <div class="form-grid">
                        <div class="form-group full-width">
                            <label>{{ $t('of_remark') }}</label>
                            <!-- <textarea placeholder="បញ្ចូលព័ត៌មានបន្ថែម..." rows="3" v-model="officer.remark"></textarea> -->
                             <vs-textarea v-model="officer.remark" :placeholder="$tHint('of_remark')" :name="'remark'" class="mt-1 w-full" />
                        </div>

                        <!-- <div class="form-group full-width">
                            <div class="checkbox-group">
                                <input type="checkbox" id="agree" required>
                                <label for="agree">ខ្ញុំយល់ព្រមនឹងលក្ខខណ្ឌ និង គោលការណ៍ឯកជនភាព <span class="required"></span></label>
                            </div>
                        </div> -->
                    </div>
                </div>

                <!-- Status Section -->
                <div class="form-section" data-section="status" v-show="tab === 'status'">
                    
                    
                    <div class="form-grid">
                        <div class="form-group">
                            <label>{{ $t('of_register_date') }} <span class="required"></span></label>
                            <!-- <input type="date" v-model="officer.dob" required> -->
                                <flat-pickr :placeholder="$tHint('of_register_date', 'date')" v-validate="'required'" name="register_date"
                                v-model="officer.register_date" class="mt-1 w-full" />
                            <span class="text-danger text-sm" v-show="errors.has('register_date')">{{ $tRequired('register_date')
                            }}</span>
                        </div>
                        <div class="form-group">
                            <label>{{ $t('of_nssf') }} <span class="required"></span></label>
                            <v-select :placeholder="$tHint('of_nssf', 'select')"
                                    v-validate="'required'" v-model="officer.is_nssf_member"
                                    name="is_nssf_member" :options="nssfs"
                                    :dir="$vs.rtl ? 'rtl' : 'ltr'" class="mt-2 w-full" />
                                    <span class="text-danger text-sm" v-show="errors.has('is_nssf_member')">{{
                                        $tRequired('is_nssf_member')
                                        }}</span>
                        </div>

                        <div class="form-group">
                            <label>{{ $t('of_status') }} <span class="required"></span></label>
                            <v-select :placeholder="$tHint('of_status', 'select')"
                                    v-validate="'required'" v-model="officer.status"
                                    name="status" :options="statuses"
                                    :dir="$vs.rtl ? 'rtl' : 'ltr'" class="mt-2 w-full" />
                                    <span class="text-danger text-sm" v-show="errors.has('status')">{{
                                        $tRequired('status')
                                        }}</span>
                        </div>
                        
                    </div>
                </div>

            </form>
            <template #actions>
                <button type="button" class="d-tabform__btn" @click="clearForm()">
                    <feather-icon icon="RotateCcwIcon" svgClasses="h-4 w-4" />
                    <span>{{ $t('form_reset') }}</span>
                </button>
                <button type="button" class="d-tabform__btn d-tabform__btn--primary" @click="submitForm">
                    <feather-icon icon="SaveIcon" svgClasses="h-4 w-4" />
                    <span>{{ $t("btn_process") }}</span>
                </button>
            </template>
        </d-form-tabs>
    </div>
</template>
<script>
// import DataViewSidebar from '@/views/components/ui-elements/data-list/DataViewSidebar.vue'
import axios from "@/axios.js";
import apiConfig from "@/apiConfig.js";
import { ref } from 'vue';
import flatPickr from 'vue-flatpickr-component';
import 'flatpickr/dist/flatpickr.css';
import vSelect from 'vue-select';
import moduleDataList from "@/store/data-list/moduleDataList.js";
import DFileManager from '@/views/form-builder/DFileManager.vue';

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
      apiEntityByDept: apiConfig._apiEntityByDept,
      selected: [],
      products: [],
      itemsPerPage: 4,
      isMounted: false,
      officer: [
        { photo: null},
        { first_name: null},
        { last_name: null},
        { latin: null},
        { gender: null},
        { dob: null},
        { identity_no: null},
        { register_date: null},
        { nationality: null},
        { title: null},
        { duty: null},
        { department_id: null},
        { entity_id: null},
        { address: null},
        { email: null},
        { bank: null},
        { bank_code: null},
        { bank_account_no: null},
        { updated_bank_acc: null},
        { position: null},
        { telephone_1: null},
        { emergency_person: null},
        { emergency_relative: null},
        { telephone_2: null},
        { is_nssf_member: null},
        { nssf_card_no: null},
        { order_level: null},
        { married_status: null},
        { status: null},
        { social_media_fb: null},
        { social_media_telegram: null},
        { social_media_whatsapp: null},
        { social_media_alt: null},
        { remark: null},
      ],
        iconExcel: "../images/file-icons/xls.png",
        iconDoc: "../images/file-icons/doc.png",
        iconPDF: "../images/file-icons/pdf.png",
      iconThumnail: null,
      genders: [],
      titles: [],
      entities: [],
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
    currentPage() {
      if(this.isMounted) {
        return this.$refs.table.currentx
      }
      return 0
    },
    // products() {
    //   return this.$store.state.dataList.products
    // },
    queriedItems() {
      return this.$refs.table ? this.$refs.table.queriedResults.length : this.products.length
    }
  },
  methods: {
    // Option labels in the current language; the stored value stays the same
    translated(options) {
      const en = this.$i18n.locale === 'en'
      return (options || []).map(option => en && option.label_en ? { ...option, label: option.label_en } : option)
    },
    clearForm(){
        this.iconThumnail = null;
        this.officer.photo = null;
        this.officer.first_name = null;
        this.officer.last_name = null;
        this.officer.latin = null;
        this.officer.gender = null;
        this.officer.dob = null;
        this.officer.identity_no = null;
        this.officer.register_date = null;
        this.officer.nationality = null;
        this.officer.title = null;
        this.officer.duty = null;
        this.officer.department_id = null;
        this.officer.entity_id = null;
        this.officer.address = null;
        this.officer.email = null;
        this.officer.bank = null;
        this.officer.bank_code = null;
        this.officer.bank_account_no = null;
        this.officer.updated_bank_acc = null;
        this.officer.position = null;
        this.officer.telephone_1 = null;
        this.officer.emergency_person = null;
        this.officer.emergency_relative = null;
        this.officer.telephone_2 = null;
        this.officer.is_nssf_member = null;
        this.officer.nssf_card_no = null;
        this.officer.order_level = null;
        this.officer.married_status = null;
        this.officer.status = null;
        this.officer.social_media_fb = null;
        this.officer.social_media_telegram = null;
        this.officer.social_media_whatsapp = null;
        this.officer.social_media_alt = null;
        this.officer.remark = null;
    },
    onInitChange($event, _formAttribute, _formName, _apiRequest) {
        this.entities = [];
        this.officer.entity_id = null;
        let _data = {
            type: "autocomplete",
            param: $event
        };
        // this.form.attribute[_formAttribute] = null;
        // this.data[_formAttribute] = [];
    
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
                                this.entities.push(response["data"]["data"][i]);
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
                    // this.data = response.data.officers;
                    // this.paymentMethods = response.data.payment_method_id;
                    // this.currencies = response.data.currency_id;
                    this.departments = response.data.data.department_id;
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
    submitForm() {
        let _data = [
            {photo: this.officer.photo},    
            {first_name: this.officer.first_name},
            {last_name: this.officer.last_name},
            {latin: this.officer.latin},
            {gender: this.officer.gender?this.officer.gender.value: null},
            {dob: this.officer.dob},
            {identity_no: this.officer.identity_no},
            {register_date: this.officer.register_date},
            {nationality: this.officer.nationality},
            {title: this.officer.title?this.officer.title.value:null},
            {duty: this.officer.duty},
            {department_id: this.officer.department_id?this.officer.department_id.value:null},
            {entity_id: this.officer.entity_id?this.officer.entity_id.value:null},
            {address: this.officer.address},
            {email: this.officer.email},
            {bank: this.officer.bank},
            {bank_code: this.officer.bank_code},
            {bank_account_no: this.officer.bank_account_no},
            {updated_bank_acc: this.officer.updated_bank_acc},
            {position: this.officer.position?this.officer.position.value:null},
            {telephone_1: this.officer.telephone_1},
            {emergency_person: this.officer.emergency_person},
            {emergency_relative: this.officer.emergency_relative?this.officer.emergency_relative.value:null},
            {telephone_2: this.officer.telephone_2},
            {is_nssf_member: this.officer.is_nssf_member?this.officer.is_nssf_member.value:null},
            {nssf_card_no: this.officer.nssf_card_no},
            {order_level: this.officer.order_level},
            {married_status: this.officer.married_status?this.officer.married_status.value:null},
            {status: this.officer.status?this.officer.status.value:null},
            {social_media_fb: this.officer.social_media_fb},
            {social_media_telegram: this.officer.social_media_telegram},
            {social_media_whatsapp: this.officer.social_media_whatsapp},
            {social_media_alt: this.officer.social_media_alt},
            {remark: this.officer.remark},
        ];
        this.$validator.validateAll().then(result => {
            setTimeout(() => {
                window.scrollTo({ top: 0, behavior: 'smooth' });
            }, 100); // ពន្យារពេល ១០០
            if (result) {
                this.$vs.loading();
                return new Promise((resolve, reject) => {
                    axios.post(this.api, _data)
                        .then((response) => {
                            if (response.data.success) {
                                this.$vs.notify({
                                    title: 'ការជូនដំណឹង',
                                    text: response.data.message,
                                    iconPack: 'feather',
                                    icon: 'icon-check-circle',
                                    color: 'primary',
                                    position: 'top-right'
                                });
                                this.clearForm();
                                this.officer_id = null;
                                this.paymentHistories = [];
                                this.$vs.loading.close();
                                // this.$router.push('/account/expense').catch(() => { })
                            }else{
                                this.$vs.notify({
                                    title: 'ការជូនដំណឹង',
                                    text: response.data.message,
                                    iconPack: 'feather',
                                    icon: 'icon-check-circle',
                                    color: 'danger',
                                    position: 'top-right'
                                })
                                this.$vs.loading.close();
                            }
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
                    });
            }else{
                this.$refs.tabs.focusFirstError();
                this.$vs.notify({
                    title: 'ការជូនដំណឹង',
                    text: 'msg.incomplete',
                    iconPack: 'feather',
                    icon: 'icon-check-circle',
                    color: 'danger',
                    position: 'top-right'
                });
                this.$vs.loading.close();
            }
        });
    }
  },
  created() {
    this.getData();
    if(!moduleDataList.isRegistered) {
      this.$store.registerModule('dataList', moduleDataList)
      moduleDataList.isRegistered = true
    }
  },
  mounted() {
    this.isMounted = true;
  }
}
</script>
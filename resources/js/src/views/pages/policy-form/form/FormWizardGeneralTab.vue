<template>
    <!-- <vx-card title="Form wizard with validation" title="ទម្រង់បង្កើតសំណើរ" code-toggler> -->
    <vx-card title="ទម្រង់បង្កើតសំណើរ" code-toggler>

        <p>សូមបំពេញពត៌មានខាងក្រោម៖</p>

        <div class="mt-5">
            <!-- <form-wizard color="rgba(var(--vs-primary), 4)" :title="null" :subtitle="null" finishButtonText="Submit" @on-complete="formSubmitted"> -->
            <form-wizard color="rgba(var(--vs-primary), 1)" errorColor="rgba(var(--vs-danger), 1)" :title="null" :subtitle="null" finishButtonText="Submit" @on-complete="formSubmitted">
                <!-- tab 2 content -->
                <tab-content title="តារាងលម្អិតតម្រូវការថវិកា" class="mb-6" :before-change="validateStep2">
                    
                    <!-- tab 1 content -->
                    <div class="vx-row">
                        <div class="vx-col md:w-1/6 w-full mt-5">
                            <!-- ADD NEW -->
                            <div @click="addRow()" class="btn-add-new p-2 mb-4 mr-4 rounded-lg cursor-pointer flex items-center justify-center text-lg font-medium text-base text-primary border border-solid border-primary">
                                <feather-icon icon="PlusIcon" svgClasses="h-4 w-4" />
                                <span class="ml-2 text-base text-primary">ជ្រើសរើសពុម្ភថវិកា</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="vx-col md:w-1/1 w-full">
                        <br/>
                        <br/>
                        <br/>
                        <strong><center><h4><b>រាយនាមតារាងលម្អិតតម្រូវការថវិកា</b></h4></center></strong>

                        <div id="data-list-list-view" class="data-list-container">

                            <vs-table ref="table" multiple v-model="selected" pagination :max-items="itemsPerPage" search :data="products">
                                <div slot="header" class="flex flex-wrap-reverse items-center flex-grow justify-between">

                                    <div class="flex flex-wrap-reverse items-center data-list-btn-container">
                                        <!-- <div class="btn-add-new p-3 mb-4 mr-4 rounded-lg cursor-pointer flex items-center justify-center text-lg font-medium text-base text-primary border border-solid border-primary" @click="addNewData">
                                            <feather-icon icon="PlusIcon" svgClasses="h-4 w-4" />
                                            <span class="ml-2 text-base text-primary">បង្កើតថ្មី</span>
                                        </div> -->
                                    </div>

                                    <!-- ITEMS PER PAGE -->
                                    <vs-dropdown vs-trigger-click class="cursor-pointer mb-4 mr-4 items-per-page-handler">
                                        <div class="p-4 border border-solid d-theme-border-grey-light rounded-full d-theme-dark-bg cursor-pointer flex items-center justify-between font-medium">
                                            <span class="mr-2">{{ currentPage * itemsPerPage - (itemsPerPage - 1) }} - {{ products.length - currentPage * itemsPerPage > 0 ? currentPage * itemsPerPage : products.length }} of {{ queriedItems }}</span>
                                            <feather-icon icon="ChevronDownIcon" svgClasses="h-4 w-4" />
                                        </div>
                                        <!-- <vs-button class="btn-drop" type="line" color="primary" icon-pack="feather" icon="icon-chevron-down"></vs-button> -->
                                        <vs-dropdown-menu>
                                            <vs-dropdown-item @click="itemsPerPage=4">
                                            <span>4</span>
                                            </vs-dropdown-item>
                                            <vs-dropdown-item @click="itemsPerPage=10">
                                            <span>10</span>
                                            </vs-dropdown-item>
                                            <vs-dropdown-item @click="itemsPerPage=15">
                                            <span>15</span>
                                            </vs-dropdown-item>
                                            <vs-dropdown-item @click="itemsPerPage=20">
                                            <span>20</span>
                                            </vs-dropdown-item>
                                        </vs-dropdown-menu>
                                    </vs-dropdown>
                                </div>
                                <template slot="thead">
                                    <vs-th sort-key="no">ល.រ</vs-th>
                                    <vs-th sort-key="name">កូដថវិកា</vs-th>
                                    <vs-th sort-key="role">បរិយាយ</vs-th>
                                    <vs-th sort-key="role">អនុគណនី</vs-th>
                                    <vs-th sort-key="bank">ឯកតា</vs-th>
                                    <vs-th sort-key="bank_acc">ចំនួននាក់</vs-th>
                                    <vs-th sort-key="bank_acc">តម្លៃឯកតា</vs-th>
                                    <vs-th sort-key="bank_acc">សរុប</vs-th>
                                </template>
                                <template slot-scope="">
                                    <tbody>
                                        <vs-tr :data="to" :key="indextr" v-for="(to, indextr) in rows">
                                            
                                            <vs-td>
                                                <p class="product-name font-medium truncate">{{ indextr + 1 }}</p>
                                            </vs-td>

                                            <vs-td>
                                                <p class="product-name font-medium truncate">{{ to.budgetCode }}</p>
                                            </vs-td>

                                            <vs-td>
                                                <p class="product-name font-medium truncate">{{ to.description }}</p>
                                            </vs-td>

                                            <vs-td>
                                                <p class="product-name font-medium truncate">{{ to.codeSubAccount }}</p>
                                            </vs-td>

                                            <vs-td>
                                                <p class="product-name font-medium truncate">{{ to.unit }}</p>
                                            </vs-td>

                                            <vs-td>
                                                <p class="product-name font-medium truncate">{{ to.num }}</p>
                                            </vs-td>

                                            <vs-td>
                                                <p class="product-name font-medium truncate">{{ to.unit_price }}</p>
                                            </vs-td>

                                            <vs-td>
                                                <p class="product-name font-medium truncate">{{ to.total_price }}</p>
                                            </vs-td>

                                            <!-- <vs-td>
                                                <vs-chip v-if="to.to_officer.to_acc_no" :color="getOrderStatusColor('status')" class="product-order-status" @click="viewOffierDetail(1)">{{ to.to_officer.to_acc_no }}</vs-chip>
                                                <vs-chip v-else :color="getOrderStatusColor('status')" class="product-order-status" @click="viewOffierDetail(1)">{{ to.to_officer.acc_no }}</vs-chip>
                                            </vs-td> -->

                                        </vs-tr>
                                    </tbody>
                                </template>
                            </vs-table>
                        </div>
                    </div>
                </tab-content>

                <tab-content title="ពត៌មានទូទៅ" class="mb-5" icon="feather icon-home" :before-change="validateStep1">
                    <!-- tab 1 content -->
                    <form data-vv-scope="step-1">
                        <div class="vx-row">
                            <div class="vx-col md:w-1/2 w-full mt-5">
                                <vs-input label="ចំណងជើង" v-model="request_title" class="w-full" name="request_title" v-validate="'required'" />
                                <span class="text-danger">{{ errors.first('step-1.request_title') }}</span>
                            </div>

                            <div class="vx-col md:w-1/2 w-full mt-5">
                                <label for="" class="vs-input--label">អង្គភាព</label>
                                <v-select name="entity" v-model="entity" :options="entities" :dir="$vs.rtl ? 'rtl' : 'ltr'" v-validate="'required'" />
                                <span class="text-danger">{{ errors.first('step-1.entity') }}</span>
                            </div>

                            <div class="vx-col md:w-1/2 w-full mt-5">
                                <label for="" class="vs-input--label">ប្រភេទលិខិត</label>
                                <v-select name="letter_type" v-model="letter_type" :options="letterTypes" :dir="$vs.rtl ? 'rtl' : 'ltr'" v-validate="'required'" />
                                <span class="text-danger">{{ errors.first('step-1.letter_type') }}</span>
                            </div>
                            
                            <div class="vx-col md:w-1/2 w-full mt-5">
                                <label for="" class="vs-input--label">ថ្ងៃដាក់សំណើរ</label>
                                <datepicker v-validate="'required'" placeholder="ថ្ងៃដាក់លិខិត" name="request_date" v-model="request_date"></datepicker>
                                <span class="text-danger">{{ errors.first('step-1.request_date') }}</span>
                            </div>

                            <div class="vx-col md:w-1/2 w-full mt-5">
                                <label for="" class="vs-input--label">ដាក់សំណើរតាមរយះ</label>
                                <v-select name="request_from" v-model="request_from" :options="duties" :dir="$vs.rtl ? 'rtl' : 'ltr'"/>
                            </div>
                            
                            <div class="vx-col md:w-1/2 w-full mt-5">
                                <vs-textarea label="យោង" v-model="reference" name="reference" class="w-full" />
                            </div>

                            <div class="vx-col md:w-1/2 w-full mt-5">
                                <vs-textarea row="20" label="ពិពណ៌នា" v-model="description" class="w-full" />
                            </div>

                            <div class="vx-col md:w-1/2 w-full mt-5">
                                <div class="vx-col md:w-1/1 w-full mt-5">
                                    <input accept=".xlsx" type="file" id="files" ref="files" v-on:change="handleFileUploads($event)"/>
                                    <vs-button type="border" color="danger">Remove</vs-button>
                                    <p class="text-sm mt-2">File Allowed(word, excel, pdf, image, etc..,) <a target="_blank" href="/sample/sample-income.xlsx">Download Sample</a></p>
                                </div>
                            </div>

                        </div>
                    </form>
                </tab-content>

                
                <!-- tab 3 content -->
                <tab-content title="ផ្ទៀងផ្ទាត់" class="mb-​4">
                    <br/>
                    <br/>
                    <div class="vx-row">
                        <br/>
                        <div class="vx-col md:w-1/3 w-full">
                            <strong>ផ្ទៀងផ្ទាត់</strong>
                            <!-- <vs-input label="Event Name" v-model="eventName" class="w-full mt-5" /> -->
                            <!-- OTEHR DATA -->
                            <div class="mt-5">
                                <h6>ចំនងជើងសំណើរ: {{ request_title }}</h6>
                            </div>
                            <div class="mt-5">
                                <h6>យោង: {{ reference }}</h6>
                            </div>
                            <div class="mt-5">
                                <h6 v-if="entity.label">អង្គភាព: {{ entity.label }}</h6>
                                <h6 v-else>អង្គភាព: {{ entity }}</h6>
                            </div>
                            <div class="mt-5">
                                <h6 v-if="letter_type.label">ប្រភេទ: {{ letter_type.label }}</h6>
                                <h6 v-else>ប្រភេទ: {{ letter_type }}</h6>
                            </div>
                            <div class="mt-5">
                                <h6>ថ្ងៃដាក់សំណើរ: {{ request_date | date }}</h6>
                            </div>
                            <div class="mt-5">
                                <h6>ពិពណ៌នា: {{ description }}</h6>
                            </div>
                        </div>
                        <br/>
                        <div class="vx-col md:w-1/1 w-full">
                            <br/>
                            <br/>
                            <br/>
                            <strong><center><h4><b>រាយនាមតារាងលម្អិតតម្រូវការថវិកា</b></h4></center></strong>

                            <div id="data-list-list-view" class="data-list-container">

                                <vs-table ref="table" multiple v-model="selected" pagination :max-items="itemsPerPage" search :data="products">
                                    <div slot="header" class="flex flex-wrap-reverse items-center flex-grow justify-between">

                                        <div class="flex flex-wrap-reverse items-center data-list-btn-container">
                                            <!-- <div class="btn-add-new p-3 mb-4 mr-4 rounded-lg cursor-pointer flex items-center justify-center text-lg font-medium text-base text-primary border border-solid border-primary" @click="addNewData">
                                                <feather-icon icon="PlusIcon" svgClasses="h-4 w-4" />
                                                <span class="ml-2 text-base text-primary">បង្កើតថ្មី</span>
                                            </div> -->
                                        </div>

                                        <!-- ITEMS PER PAGE -->
                                        <vs-dropdown vs-trigger-click class="cursor-pointer mb-4 mr-4 items-per-page-handler">
                                            <div class="p-4 border border-solid d-theme-border-grey-light rounded-full d-theme-dark-bg cursor-pointer flex items-center justify-between font-medium">
                                                <span class="mr-2">{{ currentPage * itemsPerPage - (itemsPerPage - 1) }} - {{ products.length - currentPage * itemsPerPage > 0 ? currentPage * itemsPerPage : products.length }} of {{ queriedItems }}</span>
                                                <feather-icon icon="ChevronDownIcon" svgClasses="h-4 w-4" />
                                            </div>
                                            <!-- <vs-button class="btn-drop" type="line" color="primary" icon-pack="feather" icon="icon-chevron-down"></vs-button> -->
                                            <vs-dropdown-menu>
                                                <vs-dropdown-item @click="itemsPerPage=4">
                                                <span>4</span>
                                                </vs-dropdown-item>
                                                <vs-dropdown-item @click="itemsPerPage=10">
                                                <span>10</span>
                                                </vs-dropdown-item>
                                                <vs-dropdown-item @click="itemsPerPage=15">
                                                <span>15</span>
                                                </vs-dropdown-item>
                                                <vs-dropdown-item @click="itemsPerPage=20">
                                                <span>20</span>
                                                </vs-dropdown-item>
                                            </vs-dropdown-menu>
                                        </vs-dropdown>
                                    </div>
                                    <template slot="thead">
                                        <vs-th sort-key="name">ដោះដូពីម​ន្ត្រីឈ្មោះ</vs-th>
                                        <vs-th sort-key="role">លេខគណនី</vs-th>
                                        <vs-th sort-key="bank">ទៅមន្ត្រីឈ្មោះ</vs-th>
                                        <vs-th sort-key="bank_acc">លេខគណនី</vs-th>
                                    </template>
                                    <template slot-scope="">
                                        <!-- <tbody>
                                            <vs-tr :data="to" :key="indextr" v-for="(to, indextr) in rows">
                                            
                                                <vs-td>
                                                    <p class="product-name font-medium truncate" @click="viewOffierDetail(1)">{{ to.from_officer.label }}</p>
                                                </vs-td>

                                                <vs-td>
                                                    <vs-chip v-if="to.from_officer.from_acc_no" :color="getOrderStatusColor('status')" class="product-order-status" @click="viewOffierDetail(1)">{{ to.from_officer.from_acc_no }}</vs-chip>
                                                    <vs-chip v-else :color="getOrderStatusColor('status')" class="product-order-status" @click="viewOffierDetail(1)">{{ to.from_officer.acc_no }}</vs-chip>
                                                </vs-td>

                                                <vs-td>
                                                    <p class="product-bank" @click="viewOffierDetail(1)">{{ to.to_officer.label }}</p>
                                                </vs-td>

                                                <vs-td>
                                                    <vs-chip v-if="to.to_officer.to_acc_no" :color="getOrderStatusColor('status')" class="product-order-status" @click="viewOffierDetail(1)">{{ to.to_officer.to_acc_no }}</vs-chip>
                                                    <vs-chip v-else :color="getOrderStatusColor('status')" class="product-order-status" @click="viewOffierDetail(1)">{{ to.to_officer.acc_no }}</vs-chip>
                                                </vs-td>

                                            </vs-tr>
                                        </tbody> -->
                                    </template>
                                </vs-table>
                            </div>
                        </div>
                    </div>
                </tab-content>
            </form-wizard>
        </div>
    </vx-card>
</template>

<script>
import {FormWizard, TabContent} from 'vue-form-wizard'
import CreateOfficerPopup from '../../officers/form/CreateOfficerPopup.vue'
import 'vue-form-wizard/dist/vue-form-wizard.min.css'
import DataViewSidebar from '@/views/pages/compositions-request/data-list/DataViewSidebar.vue'
import moduleDataList from "@/store/data-list/moduleDataList.js"
import Datepicker from 'vuejs-datepicker';
import VueSimpleSuggest from 'vue-simple-suggest';
import 'vue-simple-suggest/dist/styles.css'
import { Validator } from 'vee-validate';
import vSelect from 'vue-select'
import axios from "@/axios.js"
const dict = {
    custom: {
        request_title: {
            required: 'សូមបំពេញចំណងជើង',
            alpha: "First name may only contain alphabetic characters"
        },
        entity: {
            required: 'សូមជ្រើសរើសអង្គភាពដោះដូរ',
            alpha: "Last name may only contain alphabetic characters"
        },
        letter_type: {
            required: 'សូមជ្រើសរើសប្រភេទលិខិត',
            email: "Please enter valid email"
        },
        request_date: {
            required: 'សូមជ្រើរើសថ្ងៃដាក់សំណើរដោះដូរ',
            alpha: "Job title may only contain alphabetic characters"
        },
        effective_date: {
            required: 'សូមជ្រើរើសថ្ងៃសុពលភាព'
        }
    }
};
Validator.localize('en', dict);
export default {
    components: {
        CreateOfficerPopup,
        FormWizard,
        TabContent,
        Datepicker,
        VueSimpleSuggest,
        'v-select': vSelect
    },
    data() {
        return {
            file: "",
            api: "/api/v1/composition_request",
            request_title: "",
            request_from: "",
            description: "",
            status: "",
            entity: "",
            entity_id: "",
            attach_file: "",
            letter_type: "",
            request_date: "",
            effective_date: "",
            is_approved: "",
            reference: "",
            officer: null,
            activePop: false,
            eventLocation: "san-francisco",
            cityOptions: [
                {text: "លិខិតជូនដំណឹង", value:"new-york"},
                {text: "Chicago", value:"chicago"},
                {text: "San Francisco", value:"san-francisco"},
                {text: "Boston", value:"boston"},
            ],
            statusOptions: [
                {text: "Plannning", value:"plannning"},
                {text: "In Progress", value:"in progress"},
                {text: "Finished", value:"finished"},
            ],
            LocationOptions: [
                {text: "New York", value:"new-york"},
                {text: "Chicago", value:"chicago"},
                {text: "San Francisco", value:"san-francisco"},
                {text: "Boston", value:"boston"},
            ],
            selected: [],
            // products: [],
            itemsPerPage: 10,
            isMounted: false,

            // Data Sidebar
            addNewDataSidebar: false,
            sidebarData: {},
            entities: [],
            letterTypes: [],
            duties: [],
            officerEntities: [],
            dataOfficers: [],
            from_officer: "",
            to_officer: "",
            rows: [
                {
                    budgetCode: "01",
                    codeSubAccount: "61103-3T-6IF-E",
                    description: "បន្ទប់ប្រជុំ (កម្រិត២-១ថ្ងៃ)",
                    unit: "នាក់",
                    num: "75",
                    num_day: "25",
                    unit_price: "200",
                    total_price: "296,2893,000",
                },
                {
                    budgetCode: "01",
                    codeSubAccount: "61103-3T-6IF-E",
                    description: "ប្រាក់បេសកម្ម",
                    unit: "នាក់",
                    num: "75",
                    num_day: "25",
                    unit_price: "200",
                    total_price: "296,2893,000",
                },
                {
                    budgetCode: "01",
                    codeSubAccount: "61103-3T-6IF-E",
                    description: "ប្រាក់ស្នាក់នៅ",
                    unit: "នាក់",
                    num: "75",
                    num_day: "25",
                    unit_price: "200",
                    total_price: "296,2893,000",
                },
                {
                    budgetCode: "01",
                    codeSubAccount: "61103-3T-6IF-E",
                    description: "សោហុយធ្វើដំណើរ",
                    unit: "នាក់",
                    num: "75",
                    num_day: "25",
                    unit_price: "200",
                    total_price: "296,2893,000",
                },
            ]
        }
    },
    computed: {
        currentPage() {
        if(this.isMounted) {
            return this.$refs.table.currentx
        }
            return 0
        },
        products() {
            return this.$store.state.dataList.products
        },
        queriedItems() {
            return this.$refs.table ? this.$refs.table.queriedResults.length : this.products.length
        },
        isFormValid() {
            return !this.errors.any() && this.dataName
        }
    },
    watch: {
        from_officer() {
            console.log(this.from_officer);
        }
    },
    methods: {
        handleFileUploads($event){
            this.file = event.target.files[0];
        },
        addRow(){
           this.rows.push({from_officer: "", from_acc_no: "", to_officer: "", to_acc_no:""}); // what to push unto the rows array?
        },
        popupOfficerForm(){
            this.$refs.CreateOfficerPopup.setValue(true);

        },
        onChange(){
            alert("test");
        },
        addMore() {
            this.dataOfficers.push({from_officer: "", from_acc_no: "", to_officer: "", to_acc_no: ""})
            console.log("data", this.dataOfficers);
        },
        removeOffier(index){
            this.rows.splice(index, 1)
        },
        validateStep1() {
            return new Promise((resolve, reject) => {
                this.$validator.validateAll('step-1').then(result => {
                    if (result) {
                        this.officerEntities = []
                        resolve(true)
                        // for(let i = 0; i < this.entity.length; i++){
                        //     this.entity_id += this.entity[i]["id"] + ',';
                        // }
                        let _entityId = this.entity.id ? this.entity.id : this.entity_id
                        axios.get("/api/v1/officer_list_by_entities?entity_id=" + _entityId)
                            .then((response) => {
                            let data = response.data.data
                            for(let i = 0; i < data.length; i++){
                                const _data = {
                                    id: data[i]["id"],
                                    label: data[i]["first_name"] + ' ' + data[i]["last_name"],
                                    acc_no: data[i]["bank_account_no"]
                                }
                                this.officerEntities.push(_data)
                            }
                        }).catch((error) => { reject(error) })
                    } else {
                        reject("correct all values");
                    }
                })
            })
        },
        validateStep2() {
            return new Promise((resolve, reject) => {
                this.$validator.validateAll("step-2").then(result => {
                    if (result) {
                        resolve(true)
                    } else {
                        reject("correct all values");
                    }
                })
            })
        },
        validateStep3() {
            return new Promise((resolve, reject) => {
                this.$validator.validateAll("step-3").then(result => {
                    if (result) {
                        alert("Form submitted!");
                        resolve(true)
                    } else {
                        reject("correct all values");
                    }
                })
            })
        },
        simpleSuggestionList() {
            return [
            'ក្រសួងសេដ្ខកិច្ច និងហិរញ្ញវត្ថុ',
            'ក្រសួងអប់រំយុវជន និងកីឡា',
            'ក្រសួងប្រៃសនីយ'
            ]
        },
        letterTypeList() {
            return [
            'លិខិតផ្ទៃក្នុង',
            'លិខិតចេញ',
            'លិខិតចូល'
            ]
        },
        getUser(){
            return new Promise((resolve, reject) => {
                axios.get("/api/v1/duty?offset=0&limit=1000&sort=id&order=desc")
                    .then((response) => {
                    let duties = response["data"]["data"];
                    for(let i = 0; i < duties.length; i++){
                        const _data = {
                            id: duties[i]["id"],
                            label: duties[i]["name"]
                        };
                        this.duties.push(_data);
                    }
                }).catch((error) => { reject(error) })
            })
        },
        getLetterType(){
            return new Promise((resolve, reject) => {
                axios.get("/api/v1/letter_type?offset=0&limit=100&sort=id&order=desc")
                    .then((response) => {
                    let letterTypes = response["data"]["data"];
                    for(let i = 0; i < letterTypes.length; i++){
                        const _data = {
                            id: letterTypes[i]["id"],
                            label: letterTypes[i]["name"]
                        };
                        this.letterTypes.push(_data);
                    }
                }).catch((error) => { reject(error) })
            })
        },
        getEntity(){
            return new Promise((resolve, reject) => {
                axios.get("/api/v1/entity?offset=0&limit=100&sort=id&order=desc")
                    .then((response) => {
                    let entities = response["data"]["data"];
                    for(let i = 0; i < entities.length; i++){
                        const _data = {
                            id: entities[i]["id"],
                            label: entities[i]["name"]
                        };
                        this.entities.push(_data);
                    }
                }).catch((error) => { reject(error) })
            })
        },
        formSubmitted() {
            // return false;
            this.$validator.validateAll().then(result => {
            if (result) {
                let obj;
                if(this.$route.params.id){
                    obj = {
                        request_title: this.request_title,
                        request_from: this.request_from.label ? this.request_from.label : this.request_from,
                        description: this.description,
                        reference: this.reference,
                        // status: this.status,
                        entity: this.entity,
                        entity_id: this.entity_id,
                        letter_type: this.letter_type.label ? this.letter_type.label : this.letter_type,
                        attach_file: this.attach_file,
                        request_date: this.request_date,
                        effective_date: this.effective_date,
                        officerRequest: this.rows,
                        // is_approved: this.is_approved,
                        // api: this.api
                    }
                }else{
                    obj = {
                        request_title: this.request_title,
                        request_from: this.request_from,
                        description: this.description,
                        reference: this.reference,
                        // status: this.status,
                        entity: this.entity,
                        letter_type: this.letter_type.label,
                        attach_file: this.attach_file,
                        request_date: this.request_date,
                        effective_date: this.effective_date,
                        officerRequest: this.rows,
                        // is_approved: this.is_approved,
                        api: this.api
                    }
                }

                const api = this.api;
                
                if(this.$route.params.id){
                    let _id = this.$route.params.id;
                    return new Promise((resolve, reject) => {
                        axios.put("/api/v1/composition_request/" + _id, obj)
                            .then((response) => {
                            this.$vs.notify({
                            title: 'Message',
                            text: response.data.message,
                            iconPack: 'feather',
                            icon: 'icon-check-circle',
                            color: 'primary'
                            })
                            this.$router.push('/incentive/composition-request').catch(() => {})
                        }).catch((error) => { 
                        reject(error)
                        this.$vs.notify({
                            title: 'Message',
                            text: "មិនអាចធ្វើបច្ចុប្បន្នភាពបានទេ,​ សូមត្រួតពិនិត្យពត៌មានឡើងវិញ។",
                            iconPack: 'feather',
                            icon: 'icon-check-circle',
                            color: 'danger'
                        })
                        })
                    })
                }else{
                    delete obj.id
                    obj.popularity = 0
                    this.$router.push('/incentive/composition-request').catch(() => {})
                    this.$store.dispatch("dataList/addItem", obj).catch(err => { console.error(err) })
                        this.$vs.notify({
                            title: 'Message',
                            text: 'ពត៌មានត្រូវបានបង្កើត.',
                            iconPack: 'feather',
                            icon: 'icon-check-circle',
                            color: 'primary'
                        })
                    }            
                    this.$emit('closeSidebar')
                    this.initValues()
                }
            })
        },
        addNewData() {
            this.sidebarData = {}
            this.toggleDataSidebar(true)
        },
        deleteData(id) {
            this.$store.dispatch("dataList/removeItem", id).catch(err => { console.error(err) })
        },
        getOrderStatusColor(status) {
            if(status == 'on_hold') return "warning"
            if(status == 'delivered') return "success"
            if(status == 'canceled') return "danger"
            return "primary"
        },
        getPopularityColor(num) {
            if(num > 90) return "success"
            if(num >70) return "primary"
            if(num >= 50) return "warning"
            if(num < 50) return "danger"
            return "primary"
        },
        toggleDataSidebar(val=false) {
            this.addNewDataSidebar = val
        },
        viewOffierDetail(offierId){
            // this.$router.push('/pages/profile')
        }
    },
    created() {
        this.getEntity()
        this.getLetterType()
        this.getUser()
        if(!moduleDataList.isRegistered) {
        this.$store.registerModule('dataList', moduleDataList)
        moduleDataList.isRegistered = true
        }
        this.$store.dispatch("dataList/fetchDataListIncentiveItems")

        if(this.$route.params.id){
            this.$vs.loading();
            setTimeout(() => {
            this.$vs.loading.close()
            }, 2000);
            let _id = this.$route.params.id
            return new Promise((resolve, reject) => {
                axios.get("/api/v1/composition_request/" + _id)
                    .then((response) => {
                    let _data = response.data.data
                    let _incentiveComposition = response.data.incentiveComposition
                    this.request_title = _data.request_title
                    this.request_from = _data.request_from
                    this.attach_file = _data.attach_file
                    this.reference = _data.reference
                    this.entity = _data.entity
                    this.entity_id = _data.entity_id
                    this.letter_type = _data.letter_type
                    this.description = _data.description
                    this.request_date = _data.request_date
                    this.effective_date = _data.effective_date
                    this.incentive_officers = _data.incentive_officers
                    if(response.data.incentiveComposition != ""){
                        this.rows = _incentiveComposition
                    }
                }).catch((error) => { 
                reject(error)
                this.$vs.notify({
                    title: 'Message',
                    text: "Error, while trying to do action or no permission allow!",
                    iconPack: 'feather',
                    icon: 'icon-check-circle',
                    color: 'danger'
                })
                })
            })
        }
    },
    mounted() {
        this.isMounted = true;
        this.wasSidebarOpen = this.$store.state.reduceButton;
        this.$store.commit('TOGGLE_REDUCE_BUTTON', true)
    },
    beforeDestroy() {
        if (!this.wasSidebarOpen) this.$store.commit('TOGGLE_REDUCE_BUTTON', false)
    },
}
</script>

<style lang="scss">
#data-list-list-view {
  font-family: var(--font-base);
  .vs-con-table {

    /*
      Below media-queries is fix for responsiveness of action buttons
      Note: If you change action buttons or layout of this page, Please remove below style
    */
    @media (max-width: 689px) {
      .vs-table--search {
        margin-left: 0;
        max-width: unset;
        width: 100%;

        .vs-table--search-input {
          width: 100%;
        }
      }
    }

    @media (max-width: 461px) {
      .items-per-page-handler {
        display: none;
      }
    }

    @media (max-width: 341px) {
      .data-list-btn-container {
        width: 100%;

        .dd-actions,
        .btn-add-new {
          width: 100%;
          margin-right: 0 !important;
        }
      }
    }

    .product-name {
      max-width: 23rem;
    }

    .vs-table--header {
      display: flex;
      flex-wrap: wrap;
      margin-left: 1.5rem;
      margin-right: 1.5rem;
      > span {
        display: flex;
        flex-grow: 1;
      }

      .vs-table--search{
        padding-top: 0;

        .vs-table--search-input {
          padding: 0.9rem 2.5rem;
          font-size: 1rem;

          &+i {
            left: 1rem;
          }

          &:focus+i {
            left: 1rem;
          }
        }
      }
    }

    .vs-table {
      border-collapse: separate;
      border-spacing: 0 1.3rem;
      padding: 0 1rem;

      tr{
          box-shadow: 0 4px 20px 0 rgba(0,0,0,.05);
          td{
            padding: 20px;
            &:first-child{
              border-top-left-radius: .5rem;
              border-bottom-left-radius: .5rem;
            }
            &:last-child{
              border-top-right-radius: .5rem;
              border-bottom-right-radius: .5rem;
            }
          }
          td.td-check{
            padding: 20px !important;
          }
      }
    }

    .vs-table--thead{
      th {
        padding-top: 0;
        padding-bottom: 0;

        .vs-table-text{
          text-transform: uppercase;
          font-weight: 600;
        }
      }
      th.td-check{
        padding: 0 15px !important;
      }
      tr{
        background: none;
        box-shadow: none;
      }
    }

    .vs-table--pagination {
      justify-content: center;
    }
  }
}
</style>

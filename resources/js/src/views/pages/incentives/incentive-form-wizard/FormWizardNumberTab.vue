<template>
    <vx-card title="ទម្រង់បង្កើតប្រាក់ឧបត្ថម្ភ">

        <p>សូមបង្កើតការបើកផ្តល់ប្រាក់ឧបត្ថម្ភតាមជំហានដូចខាងក្រោម៖</p>

        <div class="mt-5">
            <!-- <form-wizard color="rgba(var(--vs-primary), 4)" :title="null" :subtitle="null" finishButtonText="Submit" @on-complete="formSubmitted"> -->
            <!-- <form-wizard color="rgba(var(--vs-primary), 1)" errorColor="rgba(var(--vs-danger), 1)" :title="null" :subtitle="null" finishButtonText="Submit" @on-complete="formSubmitted"> -->
            <form-wizard color="rgba(var(--vs-primary), 1)" errorColor="rgba(var(--vs-danger), 1)" :title="null" :subtitle="null" finishButtonText="Submit" @on-complete="formSubmitted">
                <!-- tab 2 content -->
                <!-- <tab-content title="ឯកសាភ្ជាប់" class="mb-5" :before-change="validateStep1">
                    <div class="vx-row">
                        <div class="mt-12">
                            <vs-upload multiple text="Upload Multiple" action="http://localhost:9000/api/v1/incentive" @on-success="successUpload" />
                            <input type="file" id="files" name="image" ref="files" multiple v-on:change="handleFileUploads($event)"/>
                        </div>
                    </div>
                </tab-content> -->

                <!-- tab 1 content -->
                <tab-content title="ពត៌មានទូទៅ" class="mb-5" icon="feather icon-home" :before-change="validateStep1">
                    <form data-vv-scope="step-1">
                        <!-- tab 1 content -->
                        <div class="vx-row">
                            <div class="vx-col md:w-1/2 w-full mt-5">
                                <vs-input label="ចំណងជើង" v-model="title" class="w-full" name="title" v-validate="'required'"/>
                                <span class="text-danger">{{ errors.first('step-1.title') }}</span>
                            </div>
                            <div class="vx-col md:w-1/2 w-full mt-5">
                                <vs-input label="យោង" v-model="reference" name="reference" class="w-full" />
                            </div>
                            <div class="vx-col md:w-1/2 w-full mt-5">
                                <vs-input label="ឆ្នាំខ្មែរ​ (Eg.ជូត ទោស័ក ព.ស. ២៥៦៤)"  v-model="khmer_date" class="w-full" />
                            </div>
                            <div class="vx-col md:w-1/2 w-full mt-5">
                                <vs-input label="ថ្ងៃខ្មែរ (Eg.អង្គារ ០៧ រោច  ខែចេត្រ)"  v-model="khmer_day" class="w-full" />
                            </div>
                            <div class="vx-col md:w-1/2 w-full mt-5">
                                <label for="" class="vs-input--label">ប្រភេទលិខិត</label>
                                <v-select name="letter_type" v-model="letter_type" :options="letterTypes" :dir="$vs.rtl ? 'rtl' : 'ltr'" v-validate="'required'" />
                                <span class="text-danger">{{ errors.first('step-1.letter_type') }}</span>
                            </div>
                            <div class="vx-col md:w-1/2 w-full mt-5">
                                <label for="" class="vs-input--label">ថ្ងៃបង្កើតបើកផ្តល់</label>
                                <flat-pickr class="w-full" v-validate="'required'" placeholder="ថ្ងៃបង្កើតបើកផ្តល់" name="issue_date" v-model="issue_date" />
                                <span class="text-danger">{{ errors.first('step-1.issue_date') }}</span>
                            </div>
                            <div class="vx-col md:w-1/2 w-full mt-5">
                                <label for="" class="vs-input--label">បើកពីថ្ងៃ</label>
                                <flat-pickr class="w-full" v-validate="'required'" placeholder="បើកពីថ្ងៃ" name="issue_from_date" v-model="issue_from_date" />
                                <span class="text-danger">{{ errors.first('step-1.issue_from_date') }}</span>
                            </div>
                            <div class="vx-col md:w-1/2 w-full mt-5">
                                <label for="" class="vs-input--label">បើកដល់ថ្ងៃ</label>
                                <flat-pickr class="w-full" v-validate="'required'" placeholder="បើកដល់ថ្ងៃ" name="issue_to_date" v-model="issue_to_date" />
                                <span class="text-danger">{{ errors.first('step-1.issue_to_date') }}</span>
                            </div>
                            <div class="vx-col md:w-1/2 w-full mt-5">
                                <label for="" class="vs-input--label">គោរពជូនដ៍ខ្ពង់ខ្ពស់</label>
                                <v-select name="sent_to" v-model="sent_to" :options="users" :dir="$vs.rtl ? 'rtl' : 'ltr'" v-validate="'required'" />
                                <span class="text-danger">{{ errors.first('step-1.sent_to') }}</span>
                            </div>
                            <div class="vx-col md:w-1/2 w-full mt-5">
                                <label for="" class="vs-input--label">រៀបចំដោយ</label>
                                <v-select name="prepare_by" v-model="prepared_by" :options="users" :dir="$vs.rtl ? 'rtl' : 'ltr'" v-validate="'required'" />
                                <span class="text-danger">{{ errors.first('step-1.prepare_by') }}</span>
                            </div>
                            <div class="vx-col md:w-1/1 w-full mt-5">
                                <label for="" class="vs-input--label">គណនាពន្ធតាមថ្នាក់</label>
                                <vs-checkbox v-model="allow_cal_tax" color="success" />
                            </div>
                            <div class="vx-col md:w-1/2 w-full mt-5">
                                <label for="" class="vs-input--label">បានឃើញ និងពីនិត្យដោយ</label>
                                <v-select name="seen_by" v-model="seen_by" :options="users" :dir="$vs.rtl ? 'rtl' : 'ltr'" v-validate="'required'" />
                                <span class="text-danger">{{ errors.first('step-1.seen_by') }}</span>
                            </div>
                            <div class="vx-col md:w-1/2 w-full mt-5">
                                <label for="" class="vs-input--label">បានឃើញ និងឯកភាព</label>
                                <v-select name="approved_by" v-model="approved_by" :options="users" :dir="$vs.rtl ? 'rtl' : 'ltr'" v-validate="'required'" />
                                <span class="text-danger">{{ errors.first('step-1.approved_by') }}</span>
                            </div>

                            <div class="vx-col md:w-1/2 w-full mt-5">
                                <label for="" class="vs-input--label">ជ្រើសរើសក្រសួងវិស័យ ដែលផ្តល់ប្រាក់ឧបត្ថម្ភ</label>
                                <v-select multiple :closeOnSelect="true" name="entity" v-validate="'required'" v-model="entity" :options="entities" :dir="$vs.rtl ? 'rtl' : 'ltr'" />
                                <span class="text-danger">{{ errors.first('step-1.entity') }}</span>
                            </div>
                            <div class="vx-col md:w-1/2 w-full mt-5">
                                <label for="" class="vs-input--label">ផ្ញើជូនធនាគារ</label>
                                <v-select multiple :closeOnSelect="true" name="bank" v-validate="'required'" v-model="bank" :options="banks" :dir="$vs.rtl ? 'rtl' : 'ltr'" />
                                <span class="text-danger">{{ errors.first('step-1.bank') }}</span>
                            </div>
                            
                            
                            <div class="vx-col md:w-1/2 w-full mt-5">
                                <br/>
                                <label for="" class="vs-input--label">ឯកសារភ្ជាប់</label>
                                <input type="file" id="files" name="image" ref="files" multiple v-on:change="handleFileUploads($event)"/>
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
                                <h6>ឈ្មោះលិខិត: {{title}}</h6>
                            </div>
                            <div class="mt-5">
                                <h6>លិខិតយោង: {{reference}}</h6>
                            </div>
                            <div class="mt-5">
                                <h6 v-if="prepared_by.label">រៀបចំដោយ: {{prepared_by.label}}</h6>
                                <h6 v-else>រៀបចំដោយ: {{prepared_by}}</h6>
                            </div>
                            <div class="mt-5">
                                <h6>ថ្ងៃបង្កើតបើកផ្តល់: {{issue_date | date}}</h6>
                            </div>
                            <div class="mt-5">
                                <h6>ថ្ងៃបើកផ្តល់ប្រាក់ឧបត្ថម្ភ: {{issue_from_date | date}} - {{issue_to_date | date}}</h6>
                            </div>
                            <div class="mt-5">
                                <h6>សំគាល់: </h6>
                            </div>
                        </div>
                        <br/>
                        <div class="vx-col md:w-1/3 w-full">
                            <strong>គោរពជូនដ៍ខ្ពង់ខ្ពស់</strong>
                            <!-- OTEHR DATA -->
                            <div class="mt-5">
                                <h6 v-if="sent_to.label">{{sent_to.label}}</h6>
                                <h6 v-else>{{sent_to}}</h6>
                            </div>
                        </div>
                        <br/>
                        <div class="vx-col md:w-1/3 w-full">
                            <strong>គោរពជូនឯកឧត្តមអគ្គលេខាធិការ</strong>
                            <!-- OTEHR DATA -->
                            <div class="mt-5">
                                <h6 v-if="seen_by.label">បានឃើញ និងពិនិត្យត្រឹមត្រូវ : {{seen_by.label}}</h6>
                                <h6 v-else>បានឃើញ និងពិនិត្យត្រឹមត្រូវ : {{seen_by}}</h6>
                            </div>
                            <div class="mt-5">
                                <h6 v-if="approved_by.label">បានឃើញ និងឯកភាព : {{approved_by.label}}</h6>
                                <h6 v-else>បានឃើញ និងឯកភាព : {{approved_by}}</h6>
                            </div>
                        </div>
                        <br/>
                        <div class="vx-col md:w-1/3 w-full">
                            <br/><br/>
                            <!-- <vs-input label="Event Name" v-model="eventName" class="w-full mt-5" /> -->
                            <strong>ផ្ញើជូនធនាគា</strong>
                            <!-- OTEHR DATA -->
                            <div :data="tr" :key="indextr" v-for="(tr, indextr) in bank" class="mt-5">
                                <h6>{{ tr.label}}</h6>
                            </div>
                        </div>
                        <br/>
                        <div class="vx-col md:w-1/3 w-full">
                            <br/><br/>
                            <strong>សង្ខេប ក្រសួង‍~ស្ថាប័ន</strong>
                            <!-- OTEHR DATA -->
                            <div class="mt-5">
                                <h6>ចំនួនសរុបក្រសួង‍~ស្ថាប័ន : {{ entity.length }}</h6>
                                <div :data="tr" :key="indextr" v-for="(tr, indextr) in entity" class="mt-5">
                                    <h6>{{ indextr + 1 }}. {{ tr.label}} </h6>
                                </div>
                            </div>
                            <div class="mt-5">
                                <h6>ចំនួនសរុបមន្ត្រីបើកប្រាក់ឧបត្ថម្ភ : {{totalOfficer}} នាក់</h6>
                            </div>
                        </div>
                        <br/>
                        <div class="vx-col md:w-1/1 w-full">
                            <br/>
                            <br/>
                            <br/>
                            <strong><center><h4><b>រាយនាមឈ្មោះមន្ត្រីបើកប្រាក់ឧបត្ថម្ភ តាមក្រសួង‍~ស្ថាប័ន</b></h4></center></strong>
                            <!-- ############################### -->
                            <div>
                                <table class="table" border="1">
                                    <tr>
                                        <!-- <thead​> -->
                                            <th class="border-dash"><center>ល.រ</center></th>
                                            <th class="border-dash"><center>គោត្តនាម និង នាម</center></th>
                                            <th class="border-dash"><center>តួនាទី</center></th>
                                            <th class="border-dash"><center>ឈ្មោះធនាគារ</center></th>
                                            <th class="border-dash"><center>លេខគណនី</center></th>
                                            <th class="border-dash"><center>ប្រាក់ឧបត្ថម្ភ</center></th>
                                            <th class="border-dash"><center>ប្រាក់បង្វែរ</center></th>
                                            <th class="border-dash"><center>ប្រាក់រំលឹក</center></th>
                                            <th class="border-dash"><center>ប្រាក់ឧបត្ថម្ភជាប់ពន្ធ</center></th>
                                            <th class="border-dash"><center>ពន្ធត្រូវបង់</center></th>
                                            <th class="border-dash"><center>ប្រាក់ត្រូវបើក</center></th>
                                        <!-- </thead> -->
                                    </tr>
                                    <tr>
                                        <td style="font-size: 8pt;padding: 0;"><i><center>១</center></i></td>
                                        <td style="font-size: 8pt;padding: 0;"><i><center>២</center></i></td>
                                        <td style="font-size: 8pt;padding: 0;"><i><center>៣</center></i></td>
                                        <td style="font-size: 8pt;padding: 0;"><i><center>៤</center></i></td>
                                        <td style="font-size: 8pt;padding: 0;"><i><center>៥</center></i></td>
                                        <td style="font-size: 8pt;padding: 0;"><i><center>៦</center></i></td>

                                        <td style="font-size: 8pt;padding: 0;"><i><center>៧</center></i></td>
                                        <td style="font-size: 8pt;padding: 0;"><i><center>៨</center></i></td>
                                        <td style="font-size: 8pt;padding: 0;"><i><center>៩=៦-៧+៨</center></i></td>
                                        <td style="font-size: 8pt;padding: 0;"><i><center>១០=<small>គណនាពន្ធតាមថ្នាក់</small></center></i></td>
                                        <td style="font-size: 8pt;padding: 0;"><i><center>១១=៩-១០</center></i></td>
                                    </tr>
                                    <tbody :data="tr" :key="indextr" v-for="(tr, indextr) in incentiveByEntity" style="width:100%">
                                        <tr style="background: rgba(189, 215, 238, 1)">
                                            <td colspan="11">
                                                <b>{{tr.entity}}</b>
                                            </td>
                                        </tr>
                                        <tr :data="ctr" :key="indextr" v-for="(ctr, indextr) in tr.officers">
                                            <td><center>{{ctr.no}}</center></td>
                                            <td>{{ctr.first_name}} {{ctr.last_name}}</td>
                                            <td><center>{{ctr.duty}}</center></td>
                                            <td><center>{{ctr.bank_name}}</center></td>
                                            <td>
                                                <center v-if="ctr.updated_bank_acc != null">{{ctr.updated_bank_acc}}</center>
                                                <center v-else>{{ctr.bank_account_no}}</center>
                                            </td>
                                            <td><center>{{ctr.incentive_kh}}</center></td>

                                            <td><center>{{ctr.return_amount}}</center></td>
                                            <td><center>{{ctr.reminder_amount}}</center></td>
                                            <td><center>{{ctr.total_incentive}}</center></td>
                                            <td><center>{{ctr.tax}}</center></td>
                                            <td><center>{{ctr.total_incentive_after_tax}}</center></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <!-- ############################### -->

                        </div>
                    </div>
                </tab-content>
            </form-wizard>
        </div>
    </vx-card>
</template>

<script>
import {FormWizard, TabContent} from 'vue-form-wizard'
import 'vue-form-wizard/dist/vue-form-wizard.min.css'
import moduleDataList from "@/store/data-list/moduleDataList.js"
import { Validator } from 'vee-validate';
import vSelect from 'vue-select'
import axios from "@/axios.js"
const dict = {
    custom: {
        title: {
            required: 'សូមបំពេញចំណងជើង'
        },
        reference: {
            required: 'សូមបំពេញឯកសារយោង'
        },
        letter_type: {
            required: 'សូមជ្រើសរើសប្រភេទលិខិត'
        },
        issue_date: {
            required: 'សូមជ្រើសរើសថ្ងៃបង្កើត'
        },
        issue_from_date: {
            required: 'សូមជ្រើសរើសថ្ងៃដាក់សំណើរពីថ្ងៃ'
        },
        issue_to_date: {
            required: 'សូមជ្រើសរើសថ្ងៃដាក់សំណើរដល់ថ្ងៃ'
        },
        sent_to: {
            required: 'ផ្ញើជូនអគ្គនាយករូបណា?'
        },
        prepare_by: {
            required: 'សូមជ្រើសរើសអ្នករៀបចំ'
        },
        seen_by: {
            required: 'បានយើញនិង ពិនិត្យដោយអ្នកណា?'
        },
        approved_by: {
            required: 'បានយើញនិង ឯកភាពដោយអ្នកណា?'
        },
        entity: {
            required: 'សូមជ្រើសរើសអង្គភាពសម្រាប់ប្រាក់ឧ​បត្ថម្ភ'
        },
        bank: {
            required: 'សូមជ្រើសរើសធនាគារ'
        }
    }
};
// Field messages for Khmer; merged without changing the app's validation language
Validator.localize({ kh: dict });
export default {
    components: {
        FormWizard,
        TabContent,
        'v-select': vSelect
    },
    data() {
        return {
            api: "/api/v1/incentive",
            title: 'បើកផ្តល់ប្រាក់ឧបត្ថម្ភប្រចាំខែ',
            sent_to: "",
            prepared_by: "",
            seen_by: "",
            entity: "",
            bank: "",
            khmer_date: "ជូត ទោស័ក ព.ស. ២៥៦៤",
            khmer_day: "អង្គារ ០៧ រោច  ខែចេត្រ",
            approved_by: "",
            letter_type: "",
            reference: 'លិខិតលេខ០២០ /សហវ. ចុះថ្ងៃទី ២៤ ខែមីនា ឆ្នាំ២០២០',
            issue_date: "",
            issue_from_date: "",
            issue_to_date: "",
            effective_date: "",
            attach_fille: "",
            eventLocation: "san-francisco",
            email: "",
            city: "new-york",
            proposalTitle: "",
            jobTitle: "",
            textarea: "",
            eventName: "",
            entity_id: "",
            status: "plannning",
            allow_cal_tax: false, //If Allow Calculation Tax = 1 means will calculate officer incentive base on tax term 
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
            // entities: [
            //     {id: 1, label: 'ក្រសួងអប់រំយុរជន និងកីឡា'},
            //     {id: 3, label: 'ក្រសួងបរិស្ថាន'},
            //     {id: 2, label: 'ក្រសួងសាធារណៈការ និងដឹងជញ្ជូន'},
            // ],
            options: [
                {id: 1, label: 'ក្រសួងអប់រំយុរជន និងកីឡា'},
                {id: 3, label: 'ក្រសួងបរិស្ថាន'},
                {id: 2, label: 'ក្រសួងសាធារណៈការ និងដឹងជញ្ជូន'},
            ],
            // banks: [
            //     {id: 1, label: 'ធនាគារ វីង'},
            //     {id: 3, label: 'ធនាគារ អេស៊ីលីដា'},
            //     {id: 2, label: 'រតនាគាជាតិ'},
            // ],
            selected: [],
            // products: [],
            itemsPerPage: 10,
            isMounted: false,

            // Data Sidebar
            addNewDataSidebar: false,
            sidebarData: {},
            entities: [],
            banks: [],
            officerEntities: [],
            incentiveByEntity: [],
            letterTypes: [],
            users: [],
            totalOfficer: 0,
            khmerMonth: [
                {
                    id: "1",
                    label: "បុស្ស"
                },
                {
                    id: "2",
                    label: "មាឃ"
                },
                {
                    id: "3",
                    label: "ផល្គុន"
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
        activeUserInfo() {
            return this.$store.state.AppActiveUser
        }
    },
    methods: {
        handleFileUploads(event){
            // let formData = new FormData();
            // let files = this.$refs.files.files;
            // for( var i = 0; i < files.length; i++ ){
            //     let file = files[i];
            //     formData.append('files[' + i + ']', file);
            // }
            const form = event.target;
            const file = event.target.files[0];
            const fd = new FormData();
            fd.append('image', file);
            return new Promise((resolve, reject) => {
                axios.post("/api/v1/incentive", fd)
                    .then((response) => {
                    console.log("upload success");
                }).catch((error) => { reject(error) })
            })
        },
        getUser(){
            return new Promise((resolve, reject) => {
                axios.get("/api/v1/user?offset=0&limit=1000&sort=id&order=desc")
                    .then((response) => {
                    let users = response["data"]["data"];
                    for(let i = 0; i < users.length; i++){
                        const _data = {
                            id: users[i]["id"],
                            label: users[i]["name"]
                            // label: users[i]["title"] + ' ' + users[i]["name"]
                        };
                        this.users.push(_data);
                    }
                }).catch((error) => { reject(error) })
            })
        },
        validateStep1() {
            return new Promise((resolve, reject) => {
                this.$validator.validateAll('step-1').then(result => {
                    if (result) {
                        resolve(true)
                        let entity_id = '';
                        for(let i = 0; i < this.entity.length; i++){
                            if(this.entity[i]["id"] != null){
                                entity_id += this.entity[i]["id"] + ',';
                            }
                        }
                        this.$vs.loading();
                        const formatYmd = date => (date instanceof Date ? date.toISOString() : String(date)).slice(0, 10);
                        let _issueFromdate = formatYmd(this.issue_from_date);
                        let _issueTodate = formatYmd(this.issue_to_date);
                        axios.get("/api/v1/officer_group_by_entities?entity_id=" + entity_id + '&issue_from_date=' + _issueFromdate + '&issue_to_date=' + _issueTodate + '&allow_cal_tax=' + this.allow_cal_tax)
                            .then((response) => {
                            this.officerEntities = response.data.data
                            this.incentiveByEntity = response.data.data
                            this.totalOfficer = response.data.total
                            if(response){
                                this.$vs.loading.close()
                            }
                            console.log("officer and entity", response.data.data);
                        }).catch((error) => { 
                            reject(error) 
                            this.$vs.loading.close()
                            this.$vs.notify({
                                title: 'Message',
                                text: 'msg.load_failed',
                                iconPack: 'feather',
                                icon: 'icon-check-circle',
                                color: 'danger'
                            })
                        })
                    } else {
                        reject("correct all values");
                        this.$vs.notify({
                            title: 'Message',
                            text: 'msg.incomplete',
                            iconPack: 'feather',
                            icon: 'icon-check-circle',
                            color: 'danger'
                        })
                    }
                })
            })
        },
        // validateStep2() {
        //     return new Promise((resolve, reject) => {
        //         this.$validator.validateAll("step-2").then(result => {
        //             if (result) {
        //                 resolve(true)
        //                 for(let i = 0; i < this.entity.length; i++){
        //                     this.entity_id += this.entity[i]["id"] + ',';
        //                 }
        //                 axios.get("/api/v1/officer_by_entities?entity_id=" + this.entity_id)
        //                     .then((response) => {
        //                     this.officerEntities = response.data.data
        //                 }).catch((error) => { reject(error) })
        //             } else {
        //                 reject("correct all values");
        //             }
        //         })
        //     })
        // },
        // validateStep3() {
        //     return new Promise((resolve, reject) => {
        //         this.$validator.validateAll("step-3").then(result => {
        //             if (result) {
        //                 resolve(true)
        //             } else {
        //                 reject("correct all values");
        //             }
        //         })
        //     })
        // },
        successUpload() {
            this.$vs.notify({ color: 'success', title: 'Upload Success', text: 'msg.file_uploaded' })
        },
        formSubmitted() {
            this.$vs.loading()
            this.$validator.validateAll().then(result => {
                if (result) {
                    const obj = {
                        title: this.title,
                        sent_to: this.sent_to.label ? this.sent_to.label : this.sent_to,
                        prepared_by: this.prepared_by.label ? this.prepared_by.label : this.prepared_by,
                        seen_by: this.seen_by.label ? this.seen_by.label : this.seen_by,
                        approved_by: this.approved_by.label ? this.approved_by.label : this.approved_by,
                        reference: this.reference,
                        letter_type: this.letter_type.label ? this.letter_type.label : this.letter_type,
                        khmer_date: this.khmer_date,
                        khmer_day: this.khmer_day,
                        issue_date: this.issue_date,
                        issue_from_date: this.issue_from_date,
                        issue_to_date: this.issue_to_date,
                        effective_date: this.effective_date,
                        bank: this.bank,
                        entity: this.entity,
                        attach_fille: "",
                        officerEntities: this.officerEntities,
                        allow_cal_tax: this.allow_cal_tax,
                        api: this.api
                    }

                    const api = this.api;
                    if(this.$route.params.id){
                        let _id = this.$route.params.id;
                        return new Promise((resolve, reject) => {
                            axios.put("/api/v1/incentive/" + _id, obj)
                                .then((response) => {
                                if(response.data.success == true){
                                    this.$vs.notify({
                                        title: 'Message',
                                        text: response.data.message,
                                        iconPack: 'feather',
                                        icon: 'icon-check-circle',
                                        color: 'primary'
                                    })
                                    this.$router.push('/incentive/list').catch(() => {})
                                }else{
                                    this.$vs.loading.close()
                                    this.$vs.notify({
                                        title: 'Message',
                                        text: 'msg.update_failed',
                                        iconPack: 'feather',
                                        icon: 'icon-check-circle',
                                        color: 'danger'
                                    })
                                }
                            }).catch((error) => { 
                                reject(error)
                                this.$vs.loading.close()
                                this.$vs.notify({
                                    title: 'Message',
                                    text: 'msg.update_failed',
                                    iconPack: 'feather',
                                    icon: 'icon-check-circle',
                                    color: 'danger'
                                })
                            })
                        })
                    }else{
                        delete obj.id
                        obj.popularity = 0

                        return new Promise((resolve, reject) => {
                            axios.post("/api/v1/incentive", obj)
                                .then((response) => {
                                if(response.data.success == true){
                                    this.$vs.notify({
                                        title: 'Message',
                                        text: response.data.message,
                                        iconPack: 'feather',
                                        icon: 'icon-check-circle',
                                        color: 'primary'
                                    })
                                    this.$router.push('/incentive/list').catch(() => {})
                                }else{
                                    this.$vs.loading.close()
                                    this.$vs.notify({
                                        title: 'Message',
                                        text: 'msg.update_failed',
                                        iconPack: 'feather',
                                        icon: 'icon-check-circle',
                                        color: 'danger'
                                    })
                                }
                            }).catch((error) => { 
                                reject(error)
                                this.$vs.notify({
                                    title: 'Message',
                                    text: 'msg.update_failed',
                                    iconPack: 'feather',
                                    icon: 'icon-check-circle',
                                    color: 'danger'
                                })
                                this.$vs.loading.close()
                            })
                        })
                        
                        // this.$store.dispatch("dataList/addItem", obj).catch(err => { console.error(err) })
                        //     this.$vs.notify({
                        //         title: 'Message',
                        //         text: 'msg.saved',
                        //         iconPack: 'feather',
                        //         icon: 'icon-check-circle',
                        //         color: 'primary'
                        //     })
                        // }            
                        this.$emit('closeSidebar')
                        this.initValues()
                    }
                }else{
                    this.$vs.notify({
                        title: 'Message',
                        text: 'msg.incomplete',
                        iconPack: 'feather',
                        icon: 'icon-check-circle',
                        color: 'danger'
                    })
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
        editData(data) {
            // this.sidebarData = JSON.parse(JSON.stringify(this.blankData))
            this.sidebarData = data
            this.toggleDataSidebar(true)
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
                            label: entities[i]["code"] + '-' + entities[i]["name"]
                        };
                        if(entities[i]['is_active'] == 1){
                            this.entities.push(_data);
                        }
                    }
                    this.entity = this.entities
                    if(entities){
                        // this.$vs.loading.close()
                    }
                }).catch((error) => { reject(error) })
            })
        },
        getBank(){
            return new Promise((resolve, reject) => {
                axios.get("/api/v1/bank?offset=0&limit=100&sort=id&order=desc")
                    .then((response) => {
                    let banks = response["data"]["data"];
                    for(let i = 0; i < banks.length; i++){
                        const _data = {
                            id: banks[i]["id"],
                            label: banks[i]["name"]
                        };
                        this.banks.push(_data);
                    }
                    this.bank = this.banks
                }).catch((error) => { reject(error) })
            })
        }
    },
    created() {
        
        if(!moduleDataList.isRegistered) {
            this.$store.registerModule('dataList', moduleDataList)
            moduleDataList.isRegistered = true
        }
        this.$store.dispatch("dataList/fetchDataListIncentiveItems");
        this.getEntity();
        this.getBank();
        this.getLetterType();
        this.getUser();
        if(this.$route.params.id){
            this.$vs.loading();
            let _id = this.$route.params.id;
            return new Promise((resolve, reject) => {
                axios.get("/api/v1/incentive/" + _id)
                    .then((response) => {
                    let _data = response.data.data
                    if(_data){
                        this.$vs.loading.close();
                    }
                    this.title = _data.general.title
                    this.khmer_date = _data.general.khmer_date
                    this.khmer_day = _data.general.khmer_day
                    this.sent_to = _data.general.sent_to
                    this.prepared_by = _data.general.prepared_by
                    this.seen_by = _data.general.seen_by
                    this.approved_by = _data.general.approved_by
                    this.reference = _data.general.reference
                    this.letter_type = _data.general.letter_type
                    this.issue_date = new Date(_data.general.issue_date)
                    this.issue_from_date = new Date(_data.general.issue_from_date)
                    this.issue_to_date = new Date(_data.general.issue_to_date)
                    this.effective_date = new Date(_data.general.effective_date)
                    this.bank = _data.incentiveByBanks
                    this.entity = _data.incentiveByEntities
                    this.banks.push(_data.incentiveByBanks);
                    this.entities.push(_data.incentiveByEntities);
                    this.prepared_by = _data.general.prepared_by //this.users.find(i => i.id == _data.general.prepared_by)
                }).catch((error) => { reject(error) })
            })
        }else{
            this.prepared_by = this.activeUserInfo.name //this.users.find(i => i.id == this.activeUserInfo.id)
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
    @import "@sass/vuexy/pages/report.scss";
</style>

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

<template>
    <div class="flex items-center">
        <!-- ###################### -->
        <div>
            <div class="clearfix"></div>
            <div class="_container-inner">
                <center>
                    <span class="font-mef2">
                        ព្រះរាជាណាចក្រកម្ពុជា
                        <br/>
                        ជាតិ សាសនា ព្រះមហាក្សត្រ
                        <br/><center>***</center>
                    </span>
                </center>
                <div class="font-mef2" style="float: left;">
                <!-- <img width="120px" src="@assets/images/logo/logo.png"/> -->
                ក្រសួងសេដ្ឋកិច្ច និងហិរញ្ញវត្ថុ<br/>
                អគ្គលេខាធិការដ្ឋានក្រសួងសេដ្ឋកិច្ចនិងហិរញ្ញវត្ថុ<br/>
                នាយកដា្ឋនរដ្ឋបាលនិងហិរញ្ញវត្ថុ<br/>
                ការិយាល័យផែនការថវិកា
                </div><br/><br/>
                <div class="clearfix"></div>
                <center>    
                    <!-- <span class="font-mef2" style="color: #0d72a2; font-size: 12pt">
                        បញ្ជីសរុប<br/>
                    </span> -->
                    <br/>
                    <span class="font-mef2" style="color: #0d72a2; font-size: 12pt">
                        របាយការណ៍លទ្ធផលអនុវត្តចំណូល
                    </span>
                    <div class="clearfix"></div>
                    <br/>
                </center>
            </div>
            <div class="clearfix"></div>
            <vs-table :data="[]">

            <template slot="thead">
                <vs-th>ឈ្មោះគោលបំណងគោលនយោបាយ</vs-th>
                <vs-th>ឈ្មោះកម្មវិធី និងអនុកម្មវិធី</vs-th>
                <vs-th>អ្នកគ្រប់គ្រង</vs-th>
                <vs-th>អ្នកទទួលខុសត្រូវ</vs-th>
            </template>

            <template>
                <vs-tr :state="'success'">
                <vs-td colspan="4" >ផ្នែកទី១៖ ពិដានចំណាយចរន្ត ឆ្នាំ២០២៣-២០២៥ សរុបរួមក្រសួង (យោងលេខ ០៣ សរណន ចុះថ្ងៃទី៨ ខែមិថុនា ឆ្នាំ២០២២)</vs-td>
                </vs-tr>
                <vs-tr style="background-color: #28C76F; color: #ffffff; font-weight: bold;">
                <vs-td>គោលបំណងគោលនយោបាយទី១</vs-td>
                <vs-td>កម្មវិធីទី១ៈ ការគ្រប់គ្រងវិស័យសេដ្ឋកិច្ច</vs-td>
                <vs-td>ឯកឧត្តម/លោកជំទាវ</vs-td>
                <vs-td></vs-td>
                </vs-tr>
                
                <vs-tr :state="'warning'">
                <vs-td></vs-td>
                <vs-td>១.‌១.អនុកម្មវិធីទី១.១. ការគ្រប់គ្រងគោលនយោបាយសេដ្ឋកិច្ច ហិរញ្ញវត្ថុ និងគោលនយោបាយតាមវិស័យផ្សេងៗ</vs-td>
                <vs-td>ឯកឧត្តម/លោកជំទាវ</vs-td>
                <vs-td>អគ្គនាយកដ្ឋានគោលនយោបាយ</vs-td>
                </vs-tr>

                <vs-tr>
                <vs-td></vs-td>
                <vs-td>- ១.១.‌១.ចង្កោមសកម្មភាពទី១ៈ គាំទ្រសម្របសម្រួល និងគ្រប់គ្រងការងាររដ្ឋបាលរបស់នាយកដ្ឋាន</vs-td>
                <vs-td>លោក/លោកស្រី</vs-td>
                <vs-td>លេខាធិការដ្ឋាន</vs-td>
                </vs-tr>
                <vs-tr>
                <vs-td></vs-td>
                <vs-td>- ១.១.‌១.១.សកម្មភាពទី១ៈ បំពេញមុខងារជាលេខាធិការដ្ឋនា</vs-td>
                <vs-td>លោក/លោកស្រី</vs-td>
                <vs-td>លេខាធិការដ្ឋាន</vs-td>
                </vs-tr>
            </template>
            </vs-table>
        </div>
    <!-- ###################### -->
    </div>
</template>


<script>
    import { FormWizard, TabContent } from 'vue-form-wizard'
    import 'vue-form-wizard/dist/vue-form-wizard.min.css'

    // For custom error message
    import { Validator } from 'vee-validate';
    const dict = {
        custom: {
            first_name: {
                required: 'First name is required',
                alpha: "First name may only contain alphabetic characters"
            },
            last_name: {
                required: 'Last name is required',
                alpha: "Last name may only contain alphabetic characters"
            },
            email: {
                required: 'Email is required',
                email: "Please enter valid email"
            },
            job_title: {
                required: 'Job title name is required',
                alpha: "Job title may only contain alphabetic characters"
            },
            proposal_title: {
                required: 'Proposal title name is required',
                alpha: "Proposal title may only contain alphabetic characters"
            },
            event_name: {
                required: 'Event name is required',
                alpha: "Event name may only contain alphabetic characters"
            },
        }
    };

    // register custom messages
    Validator.localize('en', dict);

    export default {
        data() {
            return {
                firstName: "",
                lastName: "",
                email: "",
                city: "new-york",
                proposalTitle: "",
                jobTitle: "",
                textarea: "",
                eventName: "",
                eventLocation: "san-francisco",
                status: "plannning",
                cityOptions: [
                    { text: "New York", value: "new-york" },
                    { text: "Chicago", value: "chicago" },
                    { text: "San Francisco", value: "san-francisco" },
                    { text: "Boston", value: "boston" },
                ],
                statusOptions: [
                    { text: "Plannning", value: "plannning" },
                    { text: "In Progress", value: "in progress" },
                    { text: "Finished", value: "finished" },
                ],
                LocationOptions: [
                    { text: "New York", value: "new-york" },
                    { text: "Chicago", value: "chicago" },
                    { text: "San Francisco", value: "san-francisco" },
                    { text: "Boston", value: "boston" },
                ],
            }
        },
        methods: {
            validateStep1() {
                return new Promise((resolve, reject) => {
                    this.$validator.validateAll('step-1').then(result => {
                        if (result) {
                            resolve(true)
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
            }
        },
        components: {
            FormWizard,
            TabContent
        }
    }
</script>
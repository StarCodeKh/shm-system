<template>
    <div id="invoice-page">

        <div class="flex flex-wrap items-center justify-between">
          <vx-input-group class="mb-base mr-5">
            <!-- <vs-input v-model="mailTo" placeholder="Email" /> -->
            <template slot="append">
                <div class="append-text btn-addon">
                    <vs-button type="border" @click="mailTo = ''" class="whitespace-no-wrap">ជ្រើសរើសធនាគារ</vs-button>
                </div>
                <v-select style="width: 200px" @change="selectBank()" name="bank_name" v-model="bank_name" :options="banks" :dir="$vs.rtl ? 'rtl' : 'ltr'" v-validate="'required'" />
            </template>
          </vx-input-group>
            
          <div class="flex items-center">
            <vs-button class="mb-base mr-3" type="border" icon-pack="feather" icon="icon icon-download">Download</vs-button>
            <vs-button class="mb-base mr-3" icon-pack="feather" icon="icon icon-file" @click="printInvoice">Print</vs-button>
          </div>
        </div>

        <vx-card id="invoice-container">
            <div class="print-move-header-from-top">
                <div class="container">
                    <section>
                        <div class="pull-right italic">
                            ថ្ងៃ​​ <span style="font-family: arial !important">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span> 
                            ខែ <span style="font-family: arial !important">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span> 
                            ឆ្នាំ{{ incentiveInfo.khmer_date }} <br/> 
                            <span style="font-family: arial !important">&nbsp;&nbsp;&nbsp;</span> 
                            រាជធានីភ្នំពេញ, ថ្ងៃទី  <span style="font-family: arial !important">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>  
                            ខែ  
                            <span style="font-family: arial !important">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span> 
                            ឆ្នាំ{{ incentiveInfo.khmer_current_year }}
                        </div>
                        <div class="clearfix"></div>
                    </section>
                    <center>
                        <span class="font-mef2">
                            សូមគោរពជូន<br/>
                            <!-- Bank ACELIDA -->
                            <span v-if="bank_id == 5">លោកនាយកប្រត្តិបត្តិនៃ {{bank_name.label ? bank_name.label : bank_name}}</span>
                            <!-- Bank Canadia -->
                            <span v-if="bank_id == 9">លោកឧកញ្ញ៉ា ប្រធានក្រុមប្រឹក្សាភិបាលនៃ {{bank_name.label ? bank_name.label : bank_name}}</span>
                            <!-- Bank Wing -->
                            <span v-if="bank_id == 2">{{bank_name.label ? bank_name.label : bank_name}}</span>
                        </span>
                        <div class="clearfix"></div>
                    </center>
                    <section class="objective">
                        <div class="list-box">
                            <span class="font-mef2">កម្មវត្ថុ</span> ៖	
                        </div>
                        <div class="paragraph-list">
                            ការបើកផ្តល់ប្រាក់ឧបត្ថម្ភមុខងារក្រុមការងារកែទម្រង់ការគ្រប់គ្រងហិរញ្ញវត្ថុសាធារណៈ 
                            <b>
                                <span v-if="incentiveInfo.count_month > 1">
                                    ប្រចាំខែ {{incentiveInfo.khmer_from_month}} ដល់ {{incentiveInfo.khmer_to_month}}
                                </span>
                                <span v-else>
                                    ប្រចាំខែ{{incentiveInfo.khmer_month}} 
                                </span>
                                ឆ្នាំ{{incentiveInfo.khmer_year}}</b> 
                            ជូន ថ្នាក់ដឹកនាំ និងមន្រ្តីរាជការ គ្រប់ក្រសួង~ស្ថាប័ន ដែលមានគណនីនៅ
                            {{bank_name.label ? bank_name.label : bank_name}}។
                        </div>
                    </section>
                    <section class="reference">
                        <div class="list-box">
                            <span class="font-mef2">យោង</span> ៖
                        </div>
                        <div class="paragraph-list">
                            {{incentiveInfo.reference}}
                            សេចក្តីសម្រេចស្តីពីការធ្វើបច្ចុប្បន្នភាពទទួលស្គាល់សមាសភាពក្រុមការងារកែទម្រង់ការគ្រប់គ្រងហិរញ្ញវត្ថុសាធារណៈ នៅតាមក្រសួង-ស្ថាប័ន សម្រាប់ទទួលប្រាក់ឧបត្ថម្ភមុខងារឆ្នាំ{{incentiveInfo.khmer_year}}។
                        </div>
                    </section>
                    <section class="description">
                        <p>
                            <div class="text-indent">
                                សេចក្ដីដូចមានចែងក្នុងកម្មវត្ថុនិងយោងខាងលើ សូម 
                                <!-- Bank ACELIDA -->
                                <span v-if="bank_id == 5" class="font-mef2">លោកនាយក</span> 
                                <!-- Bank Canadia -->
                                <span v-if="bank_id == 9" class="font-mef2"><b>លោកឧកញ្ញ៉ា</b></span> 
                                <!-- Bank Wing -->
                                <span v-if="bank_id == 2" class="font-mef2">{{bank_name.label ? bank_name.label : bank_name}}</span> 

                                មេត្តាចាត់ចែងបើកប្រាក់ឧបត្ថម្ភមុខងារ ក្រុមការងារកែទម្រង់ការគ្រប់គ្រងហិរញ្ញវត្ថុសាធារណៈ 
                                <b>
                                    <span v-if="incentiveInfo.count_month > 1">
                                        ប្រចាំខែ {{incentiveInfo.khmer_from_month}} ដល់ {{incentiveInfo.khmer_to_month}}
                                    </span>
                                    <span v-else>
                                        ប្រចាំខែ{{incentiveInfo.khmer_month}} 
                                    </span>
                                    ឆ្នាំ{{incentiveInfo.khmer_year}} </b>
                                ជូនថ្នាក់ដឹកនាំ   និងមន្រ្តីរាជការ ក្រសួង-ស្ថាប័ន ដែលមានគណនីនៅ
                                {{bank_name.label ? bank_name.label : bank_name}} 
                                តាមតារាងទូទាត់ ដែលភ្ជាប់មកជាមួយក្នុងទឹកប្រាក់សរុប 
                                <b>{{sum_incentive_after_tax}} រៀល ({{sum_incentive_after_tax_inword}}រៀលគត់)</b>។
                                <span v-if="bank_id ==2">
                                    ជូនក្រសួង-ស្ថាប័ន <b>ចំនួន {{totalEntity}}</b> ដូចខាងក្រោម៖
                                </span>
                            </div>
                        </p>

                        <!-- Bank ACELIDA -->
                        <div class="text-indent" v-if="bank_id == 5">
                            <i>
                                សូមជូនភ្ជាប់នូវតារាងឈ្មោះថ្នាក់ដឹកនាំ និងមន្រ្តីរាជការដែលត្រូវទទួលប្រាក់ឧបត្ថម្ភមុខងារក្រុមការងារ
                                កែទម្រង់ការគ្រប់គ្រងហិរញ្ញវត្ថុសាធារណៈ ចំនួន០១ច្បាប់។
                            </i>
                        </div>
                        <!-- Bank Canadia -->
                        <div class="text-indent" v-if="bank_id == 9">
                            <i>
                                សូមជូនភ្ជាប់នូវតារាងឈ្មោះថ្នាក់ដឹកនាំ និងមន្រ្តីរាជការដែលត្រូវទទួលប្រាក់ឧបត្ថម្ភមុខងារក្រុមការងារ
                                កែទម្រង់ការគ្រប់គ្រងហិរញ្ញវត្ថុសាធារណៈ ចំនួន០៣ច្បាប់។
                            </i>
                        </div>
                        <!-- Bank Wing -->
                        <ul class="list" v-if="bank_id == 2">
                            <li class="text-indent" :data="tr" :key="indextr" v-for="(tr, indextr) in incentiveByEntity">{{tr.no}}. {{tr.entity}} ចំនួនថវិកា {{tr.total_incentive_after_tax}} រៀល</li>
                        </ul>

                        <p>
                            <div class="text-indent">
                                អាស្រ័យដូចបានជម្រាបជូនខាងលើ សូម 
                                <!-- Bank ACELIDA -->
                                <span v-if="bank_id == 5" class="font-mef2">លោកនាយក</span> 
                                <!-- Bank Canadia -->
                                <span v-if="bank_id == 9" class="font-mef2"><b>លោកឧកញ្ញ៉ា</b></span> 
                                <!-- Bank Wing -->
                                <span v-if="bank_id == 2" class="font-mef2">{{bank_name.label ? bank_name.label : bank_name}}</span> 

                                មេត្តាចាត់ចែង បើកជូនតាមសំណើខាងលើឱ្យបានឆាប់រហ័សតាមការគួរ។ 
                            </div>
                        </p>
                    </section>
                    <section class="description">
                        <p>
                            <div class="text-indent">
                                សូម 
                                <!-- Bank ACELIDA -->
                                <span v-if="bank_id == 5" class="font-mef2">លោកនាយក</span> 
                                <!-- Bank Canadia -->
                                <span v-if="bank_id == 9" class="font-mef2"><b>លោកឧកញ្ញ៉ា</b></span> 
                                <!-- Bank Wing -->
                                <span v-if="bank_id == 2" class="font-mef2">{{bank_name.label ? bank_name.label : bank_name}}</span> 

                                ទទួលនូវការគោរពរាប់អានដ៏ជ្រាលជ្រៅអំពីខ្ញុំ
                            </div>
                        </p>
                    </section>
                    <section>
                        <div class="pull-right">
                            <center>
                                <span class="font-mef2">
                                    រដ្ឋលេខាធិការ<br/>
                                    និងជាអគ្គលេខាធិការ នៃអគ្គលេខាធិការដ្ឋាន<br/>
                                    គណៈកម្មាធិការដឹកនាំការងារកែទម្រង់ការគ្រប់គ្រងហិរញ្ញវត្ថុសាធារណៈ
                                </span>
                            </center>
                        </div>
                    </section>
                </div>
                <div class="clearfix"></div>
            </div>
        </vx-card>
    </div>
</template>

<script>
import vSelect from 'vue-select'
import axios from "@/axios.js"
export default{
    data() {
        return {
            mailTo: "",
            companyDetails: {
                name: 'Microsion Technologies Pvt. Ltd.',
                addressLine1: '9 N. Sherwood Court',
                addressLine2: 'Elyria, OH',
                zipcode: '94203',
                mailId: 'hello@pixinvent.net',
                mobile: '+91 999 999 9999',
            },
            recipientDetails: {
                fullName: 'Peter Stark',
                addressLine1: '8577 West West Drive ',
                addressLine2: 'Holbrook, NY',
                zipcode: '90001',
                mailId: 'peter@mail.com',
                mobile: '+91 988 888 8888',
            },
            invoiceDetails: {
                invoiceNo: '001/2019',
                invoiceDate: 'Mon Dec 10 2018 07:46:00 GMT+0000 (GMT)',
            },
            invoiceData: {
                tasks: [
                    {
                        id: 1,
                        task: 'Website Redesign',
                        hours: 60,
                        rate: 15,
                        amount: 90000,
                    },
                    {
                        id: 2,
                        task: 'Newsletter template design',
                        hours: 20,
                        rate: 12,
                        amount: 24000,
                    },
                ],
                subtotal: 114000,
                discountPercentage: 5,
                discountedAmount: 5700,
                total: 108300,
            },
            sum_incentive_after_tax: 0,
            sum_incentive_after_tax_inword: '',
            totalEntity: 0,
            incentiveBanks: "",
            incentiveInfo: "",
            incentiveByEntity: "",
            banks: [],
            bank_name: "",
            bank_id: 9
        }
    },
    watch: {
        bank_name(val){
            if(val.id){
                this.getIncentiveByBank(val.id)
                this.bank_id = val.id
            }
        }
    },
    computed: {

    },
    created(){
        this.$vs.loading();
        this.getIncentiveInfo();
        this.getBank();
        this.getIncentiveByBank(this.bank_id);
    },
    methods: {
      selectBank(){
        alert("testing");
      },
      printInvoice() {
        window.print()
      },
      getIncentiveInfo(){
        let _incentiveId = this.$route.params.id;
        return new Promise((resolve, reject) => {
            axios.get("/api/v1/incentive/" + _incentiveId)
                .then((response) => {
                let _data = response.data
                this.incentiveInfo = _data.data.general
                // this.incentiveByEntity = _data.data.incentiveByEntities
            }).catch((error) => { reject(error) })
        })
      },
      getIncentiveByBank(bank_id){
        let _incentiveId = this.$route.params.id;
        let _data = {
            incentiveId : _incentiveId,
            bankId: bank_id
        }
        return new Promise((resolve, reject) => {
            axios.post("/api/v1/incentive/officer_by_bank", _data)
                .then((response) => {
                let _data = response.data
                this.incentiveBanks = _data.incentiveByBanks
                this.incentiveByEntity = _data.incentiveByEntities
                this.totalEntity = _data.totalBank
                this.sum_incentive_after_tax = _data.sum_incentive_after_tax
                this.sum_incentive_after_tax_inword = _data.sum_incentive_after_tax_inword
                this.$vs.loading.close();
            }).catch((error) => {
                reject(error)
                alert("Error")
            })
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
                    this.bank_name = this.banks[0]["label"]
                }).catch((error) => { reject(error) })
            })
        },
    },
    components: {
        'v-select': vSelect
    },
    mounted() {
      this.$emit("setAppClasses", "invoice-page")
    }
}
</script>

<style lang="scss">
    @import "@sass/vuexy/pages/report.scss";
</style>

<style lang="scss">
@media print {
  .invoice-page {
    font-family: 'Khmer MEF1';
    * {
      visibility: hidden;
    }

    #content-area {
      margin: 0 !important;
    }

    #invoice-container,
    #invoice-container * {
      visibility: visible;
    }
    #invoice-container {
      position: absolute;
      left: 0;
      top: 0;
      box-shadow: none;
    }
  }
    
}
</style>

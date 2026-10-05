<template>
    <div id="invoice-page">

        <div class="flex flex-wrap items-center justify-between">
          <vx-input-group class="mb-base mr-5">
            <!-- <vs-input v-model="mailTo" placeholder="Email" /> -->
            <template slot="append">
                <div class="append-text btn-addon">
                    <vs-button type="border" @click="mailTo = ''" class="whitespace-no-wrap">ជ្រើសរើសធនាគារ</vs-button>
                </div>
                <v-select style="width: 200px" name="bank_name" v-model="bank_name" :options="banks" :dir="$vs.rtl ? 'rtl' : 'ltr'" v-validate="'required'" />
            </template>
          </vx-input-group>
            
          <div class="flex items-center">
            <vs-button class="mb-base mr-3" type="border" icon-pack="feather" icon="icon icon-download" @click="onDownloadFile">Download</vs-button>
            <vs-button class="mb-base mr-3" icon-pack="feather" icon="icon icon-file" @click="printInvoice">Print</vs-button>
          </div>
        </div>

        <vx-card v-if="bank_name.id == 2" id="invoice-container">
            <div>
                <div class="clearfix"></div>
                <div class="container" style="margin-top: 50px">
                    <center>
                        <span class="font-mef2" style="font-size: 10pt">
                            តារាងឈ្មោះថ្នាក់ដឹកនាំំំ និងមន្រ្តីរាជការ ដែលត្រូវទទួលប្រាក់ឧបត្ថម្ភមុខងារក្រុមការងារកែទម្រង់ការគ្រប់គ្រង 
                            <br/> ហិរញ្ញវត្ថុសាធារណៈ របស់ក្រសួង-ស្ថាប័ន
                            <br/>ប្រចាំខែ {{incentiveInfo.khmer_month}} ឆ្នាំ{{incentiveInfo.khmer_year}}  តាមរយៈ{{bank_name.label ? bank_name.label : bank_name}} <br/>
                        </span>
                        <div class="clearfix"></div>
                    </center>
                    
                    <div>
                        <table class="table" border="1">
                            <thead>
                                <tr style="background-color: #dadada">
                                    <!-- <thead​> -->
                                        <th class="border-dash">ល.រ</th>
                                        <!-- <th class="border-dash">គោត្តនាម និង នាម អក្សរឡាតាំង</th> -->
                                        <th class="border-dash">គោត្តនាម និង នាម</th>
                                        <th class="border-dash">តួនាទី</th>
                                        <th class="border-dash">ឈ្មោះធនាគារ</th>
                                        <th class="border-dash">លេខគណនី</th>
                                        <th class="border-dash">ប្រាក់ត្រូវបើក</th>
                                    <!-- </thead> -->
                                </tr>
                                <tr class="row-line" style="background-color: #dadada">
                                    <td style="font-size: 6pt;padding: 0;"><i><center>១</center></i></td>
                                    <!-- <td style="font-size: 6pt;padding: 0;"><i><center></center></i></td> -->
                                    <td style="font-size: 6pt;padding: 0;"><i><center>២</center></i></td>
                                    <td style="font-size: 6pt;padding: 0;"><i><center>៣</center></i></td>
                                    <td style="font-size: 6pt;padding: 0;"><i><center>៤</center></i></td>
                                    <td style="font-size: 6pt;padding: 0;"><i><center>៥</center></i></td>
                                    <td style="font-size: 6pt;padding: 0;"><i><center>៦</center></i></td>
                                </tr>
                            </thead>
                            <tbody :data="tr" :key="indextr" v-for="(tr, indextr) in incentiveByEntity" style="width:100%">
                                <tr style="background: rgba(189, 215, 238, 1)">
                                    <td colspan="3">
                                        <b>{{tr.entity}}</b>
                                    </td>
                                    <td></td>
                                    <td></td>
                                    <td>
                                        <center><b>{{tr.total_incentive_after_tax}}</b></center>
                                    </td>
                                </tr>
                                <tr :data="ctr" :key="indextr" v-for="(ctr, indextr) in tr.officers">
                                    <td><center>{{ctr.no}}</center></td>
                                    <td>{{ctr.title}} {{ctr.first_name}} {{ctr.last_name}}</td>
                                    <td><center>{{ctr.duty}}</center></td>
                                    <td><center>{{ctr.bank}}</center></td>
                                    <td><center>&#8203;{{ctr.bank_account_no}}</center></td>
                                    <td><center>{{ctr.incentive_after_tax}}</center></td>
                                </tr>
                            </tbody>
                                <!-- <tfooter> -->
                                <tr>
                                    <td></td>
                                    <td colspan="2"><span class="font-mef2"><center>សរុបថវិកាត្រូវបង្វែ</center></span></td>
                                    <td></td>
                                    <td></td>
                                    <td><span class="font-mef2" style="text-decoration: underline;"><center>{{sum_incentive_after_tax}}</center></span></td>
                                </tr>
                            <!-- </tfooter> -->
                        </table>
                    </div>

                    <span style="position: relative;font-size: 9pt;">
                        <br/>
                        <div class="pull-right" style="position: relative; page-break-inside: avoid;">
                            <span>
                                <center>
                                    <!-- ថ្ងៃអង្គារ ០៧ រោច  ខែចេត្រ -->
                                   ថ្ងៃ{{ incentiveInfo.khmer_day }} ឆ្នាំ{{ incentiveInfo.khmer_date }}<br/>
                                    រាជធានីភ្នំពេញ ថ្ងៃទី{{incentiveInfo.khmer_current_day}}  
                                    ខែ{{incentiveInfo.khmer_current_month}} 
                                    ឆ្នាំ{{incentiveInfo.khmer_current_year}}<br/>
                                    <span class="font-mef2">រៀបចំដោយ <br/>មន្ត្រីជំនាញថវិកា<br/></span>
                                    <br/><br/>
                                    <span class="font-mef2">{{incentiveInfo.prepared_by}}</span>
                                </center>
                            </span>
                        </div>
                        <div class="clearfix"></div>
                        <div class="pull-left" style="position: relative; page-break-inside: avoid;">
                            <span>
                                <center>
                                    បានឃើញ និងពិនិត្យត្រឹមត្រូវ<br/>
                                    សូមគោរពជូន ឯកឧត្តមអគ្គលេខាធិការ <br/>
                                    មេត្តាពិនិត្យ និងសម្រេចដ៏ខ្ពង់ខ្ពស់ <br/>
                                    ថ្ងៃ <span style="font-family: arial !important">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span> 
                                    ខែ <span style="font-family: arial !important">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span> 
                                    ឆ្នាំ{{incentiveInfo.khmer_current_year}} <br/>
                                    រាជធានីភ្នំពេញ 
                                    ថ្ងៃ <span style="font-family: arial !important">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span> 
                                    ខែ <span style="font-family: arial !important">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span> 
                                    ឆ្នាំ{{incentiveInfo.khmer_current_year}} <br/>
                                    <span class="font-mef2">អគ្គលេខាធិការរង</span>
                                </center>
                            </span>
                        </div>
                        <div class="clearfix"></div>
                        <div class="pull-right" style="position: relative; page-break-inside: avoid;">
                            <span>
                                <center>
                                    បានឃើញ និងឯកភាព<br/>
                                    ថ្ងៃ <span style="font-family: arial !important">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span> 
                                    ខែ <span style="font-family: arial !important">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span> 
                                    ឆ្នាំ{{incentiveInfo.khmer_current_year}} <br/>
                                    រាជធានីភ្នំពេញ 
                                    ថ្ងៃ <span style="font-family: arial !important">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span> 
                                    ខែ <span style="font-family: arial !important">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span> 
                                    ឆ្នាំ{{incentiveInfo.khmer_current_year}} <br/>
                                    <span class="font-mef2">រដ្ឋលេខាធិការ<br/>
                                    និងជាអគ្គលេខាធិការ នៃអគ្គលេខាធិការដ្ឋាន<br/>
                                    គណៈកម្មាធិការដឹកនាំការងារកែទម្រង់ការគ្រប់គ្រងហិរញ្ញវត្ថុសាធារណៈ</span>
                                </center>
                            </span>
                        </div>
                    </span>
                </div>
                <div class="clearfix"></div>
                <div class="container"></div>
                <!-- #################### -->
            </div>
        </vx-card>

        <vx-card v-else id="invoice-container">
            <div>
                <div class="clearfix"></div>
                <div class="container" style="margin-top: 50px">
                    <center>
                        <span class="font-mef2" style="font-size: 10pt">
                            <span v-if="this.bank_id == 9">
                                មន្ត្រីរាជការទទួលប្រាក់ឧត្ថម្ភមុខងារក្រុមការងារកែទម្រង់ការគ្រប់គ្រងហិរញ្ញវត្ថុសារធារណៈ<br/>
                            </span>
                            <span v-else>
                                មន្ត្រីរាជការទទួលប្រាក់ឧត្ថម្ភមុខងារក្រុមការងារកែទម្រង់ការគ្រប់គ្រងហិរញ្ញវត្ថុសារធារណៈរបស់<br/>
                                ក្រសួង‍‍​​~ស្ថាប័ន<br/>
                            </span>
                            <span v-if="incentiveInfo.count_month > 1">
                                ប្រចាំខែ {{incentiveInfo.khmer_from_month}} ដល់ {{incentiveInfo.khmer_to_month}}
                            </span>
                            <span v-else>
                                ប្រចាំខែ{{incentiveInfo.khmer_month}} 
                            </span>
                            ឆ្នាំ{{incentiveInfo.khmer_year}} តាមរយៈ{{bank_name.label ? bank_name.label : bank_name}}
                            <br/>
                        </span>
                        <div class="clearfix"></div>
                    </center>
                    
                    <div>
                        <table class="table" border="1">
                            <thead>
                                <tr style="background-color: #dadada">
                                    <!-- <thead​> -->
                                        <th class="border-dash">ល.រ</th>
                                        <th class="border-dash">គោត្តនាម និង នាម</th>
                                        <th class="border-dash">តួនាទី</th>
                                        <th class="border-dash">ឈ្មោះធនាគារ</th>
                                        <th class="border-dash">លេខគណនី</th>
                                        <th class="border-dash">ប្រាក់ត្រូវបើក</th>
                                    <!-- </thead> -->
                                </tr>
                                <tr class="row-line" style="background-color: #dadada">
                                    <td style="font-size: 6pt;padding: 0;"><i><center>១</center></i></td>
                                    <td style="font-size: 6pt;padding: 0;"><i><center>២</center></i></td>
                                    <td style="font-size: 6pt;padding: 0;"><i><center>៣</center></i></td>
                                    <td style="font-size: 6pt;padding: 0;"><i><center>៤</center></i></td>
                                    <td style="font-size: 6pt;padding: 0;"><i><center>៥</center></i></td>
                                    <td style="font-size: 6pt;padding: 0;"><i><center>៦</center></i></td>
                                </tr>
                            </thead>
                            <tbody :data="tr" :key="indextr" v-for="(tr, indextr) in incentiveOfficerListByEntities" style="width:100%">
                                <!-- <tr style="background: rgba(189, 215, 238, 1)">
                                    <td colspan="3">
                                        <b>{{tr.entity}}</b>
                                    </td>
                                    <td></td>
                                    <td></td>
                                    <td>
                                        <center><b>{{tr.total_incentive}}</b></center>
                                    </td>
                                </tr> -->
                                <!-- <tr :data="ctr" :key="indextr" v-for="(ctr, indextr) in tr.officers"> -->
                                <tr>
                                    <td><center>{{tr.no}}</center></td>
                                    <td>{{tr.title}} {{tr.first_name}} {{tr.last_name}}</td>
                                    <td><center>{{tr.duty}}</center></td>
                                    <td><center>{{tr.bank}}</center></td>
                                    <td><center>&#8203;{{tr.bank_account_no}}</center></td>
                                    <td><center>{{tr.incentive_after_tax}}</center></td>
                                </tr>
                            </tbody>
                                <!-- <tfooter> -->
                                <tr>
                                    <td></td>
                                    <td colspan="2">
                                        <span v-if="this.bank_id == 9" class="font-mef2"><center>សរុបប្រាក់ឧបត្ថម្ភត្រូវបើកផ្តល់</center></span>
                                        <span v-else class="font-mef2"><center>សរុបថវិកាត្រូវបង្វែ</center></span>
                                    </td>
                                    <td></td>
                                    <td></td>
                                    <td><span class="font-mef2" style="text-decoration: underline;"><center>{{sum_incentive_after_tax}}</center></span></td>
                                </tr>
                            <!-- </tfooter> -->
                        </table>
                    </div>
                    <div class="clearfix"></div>
                    <div style="font-size: 9pt;">
                        <br/>
                        <div class="pull-right" style="position: relative; page-break-inside: avoid;">
                            <span>
                                <center>
                                    <!-- ថ្ងៃអង្គារ ០៧ រោច  ខែចេត្រ -->
                                    ថ្ងៃ{{ incentiveInfo.khmer_day }} ឆ្នាំ{{ incentiveInfo.khmer_date }}<br/>
                                    រាជធានីភ្នំពេញ ថ្ងៃទី{{incentiveInfo.khmer_current_day}}  
                                    ខែ{{incentiveInfo.khmer_current_month}} 
                                    ឆ្នាំ{{incentiveInfo.khmer_current_year}}<br/>
                                    <span class="font-mef2">រៀបចំដោយ <br/>មន្ត្រីជំនាញថវិកា<br/></span>
                                    <br/><br/>
                                    <span class="font-mef2">{{incentiveInfo.prepared_by}}</span>
                                </center>
                            </span>
                        </div>
                        <div class="clearfix"></div>
                        <div class="pull-left" style="position: relative; page-break-inside: avoid;">
                            <span>
                                <center>
                                    បានឃើញ និងពិនិត្យត្រឹមត្រូវ<br/>
                                    សូមគោរពជូន ឯកឧត្តមអគ្គលេខាធិការ <br/>
                                    មេត្តាពិនិត្យ និងសម្រេចដ៏ខ្ពង់ខ្ពស់ <br/>
                                    ថ្ងៃ <span style="font-family: arial !important"><span style="font-family: arial !important">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span></span> 
                                    ខែ <span style="font-family: arial !important">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>  
                                    ឆ្នាំ{{ incentiveInfo.khmer_date }} <br/>
                                    រាជធានីភ្នំពេញ 
                                    ថ្ងៃ <span style="font-family: arial !important">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span> 
                                    ខែ <span style="font-family: arial !important">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span> 
                                    ឆ្នាំ{{incentiveInfo.khmer_current_year}} <br/>
                                    <span class="font-mef2">អគ្គលេខាធិការរង</span>
                                </center>
                            </span>
                        </div>
                        <div class="clearfix"></div>
                        
                        <div class="pull-right" style="position: relative; page-break-inside: avoid;">
                            <span>
                                <center>
                                    បានឃើញ និងឯកភាព<br/>
                                    ថ្ងៃ <span style="font-family: arial !important"><span style="font-family: arial !important">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span></span> 
                                    ខែ <span style="font-family: arial !important">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>  
                                    ឆ្នាំ{{ incentiveInfo.khmer_date }} <br/>
                                    រាជធានីភ្នំពេញ 
                                    ថ្ងៃ <span style="font-family: arial !important">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span> 
                                    ខែ <span style="font-family: arial !important">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span> 
                                    ឆ្នាំ{{incentiveInfo.khmer_current_year}} <br/>
                                    <span class="font-mef2">រដ្ឋលេខាធិការ<br/>
                                    និងជាអគ្គលេខាធិការ នៃអគ្គលេខាធិការដ្ឋាន<br/>
                                    គណៈកម្មាធិការដឹកនាំការងារកែទម្រង់ការគ្រប់គ្រងហិរញ្ញវត្ថុសាធារណៈ</span>
                                </center>
                            </span>
                        </div>
                    </div>
                </div>
                <div class="clearfix"></div>
                <div class="container"></div>
                <!-- #################### -->
            </div>
        </vx-card>
        
    </div>
</template>

<script>
import axios from "@/axios.js"
import vSelect from 'vue-select'
export default{
    data() {
        return {
            mailTo: "",
            banks: [],
            bank_name: "",
            bank: "",
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
            incentiveInfo: "",
            incentiveByEntity: "",
            incentiveOfficerListByEntities: "",
            sum_incentive_after_tax: 0,
            bank_id: 9
        }
    },
    computed: {

    },
    watch: {
        bank_name(val){
            if(val.id){
                this.getIncentiveByBank(val.id)
                this.bank_id = val.id
            }
        }
    },
    created(){
        this.$vs.loading();
        this.getIncentiveInfo()
        this.getBank()
        this.getIncentiveByBank(this.bank_id)
    },
    methods: {
      printInvoice() {
        window.print()
      },
      onDownloadFile(){
        let _incentiveId = this.$route.params.id;
        let _data = {
          bank_id: 1,
          incentive_id: 12  
        };
        return new Promise((resolve, reject) => {
            axios.post("/report/convert/convert_annex_bank_to_excel", _data)
                .then((response) => {
                let _data = response.data;
                window.open(_data.data.url);
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
                    this.bank_name = this.banks[0]["label"]
                    // this.bank_name = this.bank
                }).catch((error) => { reject(error) })
            })
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
        this.$vs.loading();
        let _incentiveId = this.$route.params.id;
        let _data = {
            incentiveId : _incentiveId,
            bankId: bank_id
        }
        return new Promise((resolve, reject) => {
            axios.post("/api/v1/incentive/officer_by_bank", _data)
                .then((response) => {
                let _data = response.data;
                this.incentiveBanks = _data.data;
                this.totalEntity = _data.total;
                this.incentiveByEntity = _data.incentiveByEntities;
                this.incentiveOfficerListByEntities = _data.incentiveOfficerListByEntities;
                this.sum_incentive_after_tax = _data.sum_incentive_after_tax;
                this.$vs.loading.close();
            }).catch((error) => { this.$vs.loading.close(); this.sum_incentive_after_tax = 0; this.incentiveByEntity=[]; this.incentiveOfficerListByEntities = []; reject(error) })
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
    tr.row-line td {
        line-height: 18px;
    }
    @media print {
        .breakpage-footer{
            clear: both;
            page-break-after: always;
        }
        .invoice-page {
            font-family: var(--font-document);
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
                mso-number-format:General;
                position: absolute;
                left: 0;
                top: 0;
                box-shadow: none;
            }
        }
    }
</style>


<template>
    <div id="invoice-page">

        <div class="flex flex-wrap items-center justify-between">
          <vx-input-group class="mb-base mr-3">
            <vs-input v-model="mailTo" placeholder="Email" />

            <template slot="append">
              <div class="append-text btn-addon">
                <vs-button type="border" @click="mailTo = ''" class="whitespace-no-wrap">Send To Cabinate</vs-button>
              </div>
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
                            ខែ  <span style="font-family: arial !important">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span> ឆ្នាំ{{incentiveInfo.khmer_current_year}}
                        </div>
                        <div class="clearfix"></div>
                    </section>
                    <center>
                        <span class="font-mef2">
                            សូមគោរពជូន<br/>
                            ឯកឧត្តមប្រតិភូរាជរដ្ឋាភិបាលទទួលបន្ទុកជាអគ្គនាយកដ្ឋាន នៃអគ្គនាយកដ្ឋានរតនាគារជាតិ<br/>
                        </span>
                        <div class="clearfix"></div>
                    </center>
                    <section class="objective">
                        <div class="list-box">
                            <span class="font-mef2">កម្មវត្ថុ</span> ៖	
                        </div>
                        <div class="paragraph-list">
                            សំណើសុំបង្វែរថវិកា <b>{{incentiveInfo.total_incentive_with_reminder_in_khmer}} រៀល ({{incentiveInfo.total_incentive_with_reminder_in_word}}រៀលគត់)</b> សម្រាប់ឧបត្ថម្ភមុខងារ ក្រុមការងារកែទម្រង់ការគ្រប់គ្រងហិរញ្ញវត្ថុសាធារណៈ 
                            <b>
                                <span v-if="incentiveInfo.count_month > 1">
                                    ប្រចាំខែ {{incentiveInfo.khmer_from_month}} ដល់ {{incentiveInfo.khmer_to_month}}
                                </span>
                                <span v-else>
                                    ប្រចាំខែ{{incentiveInfo.khmer_month}} 
                                </span>
                                ឆ្នាំ{{incentiveInfo.khmer_year}}</b> 
                            ជូនថ្នាក់ដឹកនាំ និងមន្រ្តីរាជការគ្រប់ក្រសួង-ស្ថាប័ន។
                        </div>
                    </section>
                    <div class="clearfix"></div>
                    <section class="reference">
                        <div class="list-box">
                            <span class="font-mef2">យោង</span> ៖
                        </div>
                        <div class="paragraph-list">
                            លិខិតលេខ០២០ /សហវ. ចុះថ្ងៃទី  ២៤ ខែមីនា ឆ្នាំ២០២០  សេចក្តីសម្រេចស្តីពីការធ្វើបច្ចុប្បន្នភាពទទួលស្គាល់សមាសភាពក្រុមការងារកែទម្រង់ការគ្រប់គ្រងហិរញ្ញវត្ថុសាធារណៈ នៅតាមក្រសួង-ស្ថាប័ន សម្រាប់ទទួលប្រាក់ឧបត្ថម្ភ- មុខងារឆ្នាំ{{incentiveInfo.khmer_year}}។
                        </div>
                    </section>
                    <div class="clearfix"></div>
                    <section class="reference">
                        <div class="text-indent">
                            សេចក្ដីដូចមានចែងក្នុងកម្មវត្ថុនិងយោងខាងលើ សូម <span class="font-mef2">ឯកឧត្តមប្រតិភូ</span> មេត្តាបង្វែរថវិកាចំនួនថវិកា <b>{{incentiveInfo.total_incentive_with_reminder_in_khmer}} រៀល ({{incentiveInfo.total_incentive_with_reminder_in_word}}រៀលគត់)</b>  
                            សម្រាប់ឧបត្ថម្ភមុខងារក្រុមការងារកែទម្រង់ការគ្រប់គ្រងហិរញ្ញវត្ថុសាធារណៈ 
                            <b>
                                <span v-if="incentiveInfo.count_month > 1">
                                    ប្រចាំខែ {{incentiveInfo.khmer_from_month}} ដល់ {{incentiveInfo.khmer_to_month}}
                                </span>
                                <span v-else>
                                    ប្រចាំខែ{{incentiveInfo.khmer_month}} 
                                </span>
                                ឆ្នាំ{{incentiveInfo.khmer_year}}
                            </b> 
                            ជូនថ្នាក់ដឹកនាំ និងមន្រ្តីរាជការ ក្រសួង-ស្ថាប័ន តាមប្រភេទធនាគារ ដូចខាងក្រោម៖
                        </div>
                    </section>
                    <section class="description">
                        <div class="clearfix"></div>
                        <p>
                            <div class="list-box"></div>
                            <div class="paragraph-list">
                                <ul class="list">
                                    <li class="text-indent" :data="tr" :key="indextr" v-for="(tr, indextr) in incentiveByBanks"><i><b>{{tr.no}}.</b></i> {{tr.label}} ចំនួនថវិកា<b> {{tr.total_incentive}} រៀល ({{tr.total_incentive_in_word}} រៀលគត់)</b></li>
                                    <li class="text-indent"><i><b>{{totalBank}}.</b></i> ថវិកាត្រូវបង់ពន្ធចំនួន <b>{{incentiveInfo.total_tax_in_khmer}} រៀល ({{incentiveInfo.total_tax_in_word}}រៀលគត់)</b></li>
                                </ul>
                            </div>
                        </p>
                        <div class="clearfix"></div>
                        <p>
                            <div class="text-indent">
                                អាស្រ័យដូចបានជម្រាបជូនខាងលើ សូម <span class="font-mef2">ឯកឧត្តមប្រតិភូ</span> មេត្តាពិនិត្យនិងចាត់ចែង តាមការគួរ។ 
                                <br/>
                            </div>
                            <div class="text-indent">
                                សូម <span class="font-mef2">ឯកឧត្តមប្រតិភូ</span> ទទួលនូវការគោរពរាប់អានដ៏ជ្រាលជ្រៅអំពីខ្ញុំ
                            </div>
                        </p>
                    </section>
                    <div class="clearfix"></div>
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
                <!-- #################### -->
                <div class="consolidate-payroll-container rotate-screen" style="margin: 0 auto; display: none;">
                    <center>
                        <span class="font-mef2" style="font-size: 10pt">
                            បញ្ជីរាយនាម <br/>
                            មន្រ្តីរាជការទទួលប្រាក់ឧបត្ថម្ភមុខងារក្រុមការងារកែទម្រង់ការគ្រប់គ្រងហិរញ្ញវត្ថុសាធារណៈ សម្រាប់ខែមិថុនា ឆ្នាំ{{incentiveInfo.khmer_current_year}} <br/><br/>
                        </span>
                        <div class="clearfix"></div>
                    </center>
                    <div id="content">
                        <table class="table" border="1">
                            <thead>
                                <tr>
                                    <th style="width: 50px;" colspan="2" class="border-dash">ល.រ</th>
                                    <th class="border-dash">អត្ត.</th>
                                    <th class="border-dash">គោត្តនាម និង នាម</th>
                                    <th class="border-dash">តួនាទី</th>
                                    <th class="border-dash">ឈ្មោះធនាគា</th>
                                    <th class="border-dash">លេខគណនី</th>
                                    <th class="border-dash">សន្ទស្សន៍</th>
                                    <th class="border-dash">ប្រាក់ឧបត្ថម្ភ</th>
                                    <th class="border-dash">ប្រាក់បង្វែរ</th>
                                    <th class="border-dash">ប្រាក់រំលឹក</th>
                                    <th class="border-dash">ប្រាក់ឧបត្ថម្ភជាប់ពន្ធ</th>
                                    <th class="border-dash">ពន្ធត្រូវបង់</th>
                                    <th class="border-dash">ប្រាក់ត្រូវបើក</th>
                                    <th class="border-dash">ផ្សេងៗ</th>
                                </tr>
                            </thead>
                            <tr>
                                <td style="font-size: 7pt;padding: 0;"><i><center>1</center></i></td>
                                <td style="font-size: 7pt;padding: 0;"><i><center>2</center></i></td>
                                <td style="font-size: 7pt;padding: 0;"><i><center>3</center></i></td>
                                <td style="font-size: 7pt;padding: 0;"><i><center>4</center></i></td>
                                <td style="font-size: 7pt;padding: 0;"><i><center>5</center></i></td>
                                <td style="font-size: 7pt;padding: 0;"><i><center>6</center></i></td>
                                <td style="font-size: 7pt;padding: 0;"><i><center>7</center></i></td>
                                <td style="font-size: 7pt;padding: 0;"><i><center>8</center></i></td>
                                <td style="font-size: 7pt;padding: 0;"><i><center>9</center></i></td>
                                <td style="font-size: 7pt;padding: 0;"><i><center>10</center></i></td>
                                <td style="font-size: 7pt;padding: 0;"><i><center>11</center></i></td>
                                <td style="font-size: 7pt;padding: 0;"><i><center>12=9+10+11</center></i></td>
                                <td style="font-size: 7pt;padding: 0;"><i><center>13=គណនាពន្ធតាមថ្នាក់</center></i></td>
                                <td style="font-size: 7pt;padding: 0;"><i><center>14=12-13</center></i></td>
                                <td style="font-size: 7pt;padding: 0;"><i><center>15    </center></i></td>
                            </tr>
                            <tbody :data="tr" :key="indextr" v-for="(tr, indextr) in incentiveByEntities" style="width:100%">
                                <tr style="background: rgba(189, 215, 238, 1)">
                                    <td colspan="6">
                                        <b>{{tr.entity}}</b>
                                    </td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                </tr>
                                <tr :data="ctr" :key="indextr" v-for="(ctr, indextr) in tr.officers">
                                    <td><center></center></td>
                                    <td><center>{{ctr.no}}</center></td>
                                    <td><center>{{ctr.identity_no}}</center></td>
                                    <td>{{ctr.first_name}} {{ctr.last_name}}</td>
                                    <td>{{ctr.duty}}</td>
                                    <td>{{ctr.bank}}</td>
                                    <td>{{ctr.bank_account_no}}</td>
                                    <td>{{ctr.index}}</td>
                                    <td>{{ctr.incentive}}</td>
                                    <td>{{ctr.return_amount}}</td>
                                    <td>{{ctr.reminder_amount}}</td>
                                    <td>{{ctr.incentive}}</td>
                                    <td>{{ctr.tax}}</td>
                                    <td>{{ctr.incentive_after_tax}}</td>
                                    <td></td>
                                </tr>
                            </tbody>
                            <!-- <tfooter> -->
                            <tr style="background:rgba(198, 224, 180, 1)">
                                <td></td>
                                <td></td>
                                <td></td>
                                <td><span class="font-mef2">សរុប</span></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td><span class="font-mef2">{{incentiveInfo.total_incentive_with_reminder_in_khmer}}</span></td>
                                <td>០</td>
                                <td>០</td>
                                <td>០</td>
                                <td>០</td>
                                <td>០</td>
                                <td></td>
                            </tr>
                            <!-- </tfooter> -->
                        </table>

                    </div>
                   
                </div>
                <div class="clearfix"></div>
                <!-- #################### -->
            </div>
            <footer>
                <div id="footer-content"></div>
            </footer>
        </vx-card>
    </div>
</template>

<script>
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
            incentiveInfo: "",
            totalBank: "",
            incentiveByBanks: "",
            incentiveByEntities: []
        }
    },
    computed: {

    },
    created(){
        this.getIncentiveInfo()
    },
    methods: {
      printInvoice() {
        window.print()
      },
      getIncentiveInfo(){
        this.$vs.loading();
        let _incentiveId = this.$route.params.id;
        return new Promise((resolve, reject) => {
            axios.get("/api/v1/incentive/" + _incentiveId)
                .then((response) => {
                let _data = response.data
                this.incentiveInfo = _data.data.general
                this.totalBank = _data.data.total_bank
                this.incentiveByBanks = _data.data.incentiveByBanks
                this.incentiveByEntities = _data.data.incentiveByEntities
                this.$vs.loading.close();
            }).catch((error) => { reject(error) })
        })
      }
    },
    components: {},
    mounted() {
      this.$emit("setAppClasses", "invoice-page")
    }
}
</script>

<style>
    @page{
        counter-increment: page;
    }
</style>

<style lang="scss">
    @import "@sass/vuexy/pages/report.scss";
</style>

<style lang="scss">
html body{
    background-color: #ffffff;
}
.hidden-space, span.hidden-space{
    visibility: hidden;
}
@media print {
    .hidden-space, span.hidden-space{
        visibility: hidden;
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
      position: absolute;
      left: 0;
      top: 0;
      box-shadow: none;
    }
  }
    
}
</style>

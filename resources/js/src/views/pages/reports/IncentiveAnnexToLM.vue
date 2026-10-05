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
            <vs-button class="mb-base mr-3" type="border" icon-pack="feather" icon="icon icon-download" @click="onDownloadFile()">Download Excel</vs-button>
            <vs-button class="mb-base mr-3" icon-pack="feather" icon="icon icon-file" @click="printInvoice">Print</vs-button>
          </div>
        </div>

        <vx-card id="invoice-container">
            <div class="print-move-header-from-top">
                <div class="container" style="display: none;">
                    <section>
                        <div class="pull-right italic">
                            ថ្ងៃ​​ <span style="font-family: arial !important">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span> 
                            ខែ <span style="font-family: arial !important">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span> 
                            ឆ្នាំ{{ incentiveInfo.khmer_date }} <br/> 
                            <span style="font-family: arial !important">&nbsp;&nbsp;&nbsp;</span> 
                            រាជធានីភ្នំពេញ, ថ្ងៃទី  <span style="font-family: arial !important">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>  
                            ខែ  <span style="font-family: arial !important">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span> ឆ្នាំ{{ incentiveInfo.khmer_current_year }}
                        </div>
                        <div class="clearfix"></div>
                    </section>
                    <center>
                        <span class="font-mef2">
                            <b>
                                សូមគោរពជូន<br/>
                                ឯកឧត្តមអគ្គបណ្ឌិតសភាចារ្យ ឧបនាយករដ្ឋមន្ត្រី រដ្ឋមន្ត្រីក្រសួងសេដ្ឋកិច្ចនិងហិរញ្ញវត្ថុ<br/>
                            </b>
                        </span>
                        <div class="clearfix"></div>
                    </center>
                    <section class="objective">
                        <div class="list-box">
                            <span class="font-mef2">កម្មវត្ថុ</span> ៖	
                        </div>
                        <div class="paragraph-list">
                            សំណើសុំបង្វែរថវិកា <b>{{incentiveInfo.total_incentive_in_khmer}} រៀល ({{incentiveInfo.total_incentive_in_word}}រៀលគត់)</b>
                            សម្រាប់ឧបត្ថម្ភក្រុមការងារកែទម្រង់ការគ្រប់គ្រងហិរញ្ញវត្ថុជាសាធារណៈ 
                            <b>
                                <span v-if="incentiveInfo.count_month > 1">
                                    ប្រចាំខែ {{incentiveInfo.khmer_from_month}} ដល់ {{incentiveInfo.khmer_to_month}}
                                </span>
                                <span v-else>
                                    ប្រចាំខែ{{incentiveInfo.khmer_month}} 
                                </span>
                                ឆ្នាំ{{incentiveInfo.khmer_year}}</b>
                            ជូនថ្នាក់ដឹកនាំ និងមន្ត្រីរាជការគ្រប់ក្រសួង‍​~ស្ថាប័ន។
                        </div>
                    </section>
                    <section class="reference">
                        <div class="list-box">
                            <span class="font-mef2">យោង</span> ៖
                        </div>
                        <div class="paragraph-list">
                            <p class="padding-bottom-p">
                                លិខិតលេខ០២០ /សហវ. ចុះថ្ងៃទី  ២៤ ខែមីនា ឆ្នាំ2020  សេចក្តីសម្រេចស្តីពីការធ្វើបច្ចុប្បន្នភាពទទួលស្គាល់សមាសភាពក្រុមការងារកែទម្រង់ការគ្រប់គ្រងហិរញ្ញវត្ថុសាធារណៈ នៅតាមក្រសួង-ស្ថាប័ន សម្រាប់ទទួលប្រាក់ឧបត្ថម្ភមុខងារឆ្នាំ{{incentiveInfo.khmer_current_year}}។    
                            </p>
                        </div>
                    </section>

                    <section>
                        <p class="padding-bottom-p">
                            <div class="text-indent">
                                សេចក្ដីដូចមានចែងក្នុងកម្មវត្ថុនិងយោងខាងលើ ខ្ញុំសូមគោរពជម្រាបជូន <span class="font-mef2">ឯកឧត្តមអគ្គបណ្ឌិតសភាចារ្យ</span> មេត្តាជ្រាបដ៏ខ្ពង់ខ្ពស់ថា អគ្គលេខាធិការដ្ឋានគណៈកម្មាធិការដឹកនាំការងារកែទម្រង់ការគ្រប់គ្រងហិរញ្ញវត្ថុសាធារណៈ (អ.គ.ហ.) បានរៀបចំ បញ្ជីសរុបមន្រ្តីរាជការទទួលប្រាក់ឧបត្ថម្ភមុខងារក្រុមការងារកែទម្រង់ការគ្រប់គ្រងហិរញ្ញវត្ថុសាធារណៈដោយបង្វែរ ថវិកាចំនួន <b>{{incentiveInfo.total_incentive_in_khmer}} រៀល ({{incentiveInfo.total_incentive_in_word}}រៀលគត់)  
                                    <span v-if="incentiveInfo.count_month > 1">
                                        ប្រចាំខែ {{incentiveInfo.khmer_from_month}} ដល់ {{incentiveInfo.khmer_to_month}}
                                    </span>
                                    <span v-else>
                                        ប្រចាំខែ{{incentiveInfo.khmer_month}} 
                                    </span>  
                                    ឆ្នាំ{{incentiveInfo.khmer_year}} </b>សម្រាប់ឧបត្ថម្ភមុខងារក្រុមការងារកែទម្រង់ការគ្រប់គ្រងហិរញ្ញវត្ថុសាធារណៈ ជូនថ្នាក់ដឹកនាំ និងមន្រ្តីរាជការក្រសួង-ស្ថាប័ន ដោយក្តីអនុគ្រោះ។
                            </div>
                        </p>
                        <p class="padding-bottom-p">
                            <div class="text-indent">
                                អាស្រ័យដូចបានជម្រាបជូនខាងលើ សូម <span class="font-mef2">ឯកឧត្តមអគ្គបណ្ឌិតសភាចារ្យ</span> មេត្តាពិនិត្យ និងសម្រេចចុះហត្ថលេខាលើបញ្ជីសរុបមន្រ្តីរាជការទទួលប្រាក់សម្រាប់ឧបត្ថម្ភមុខងារក្រុមការងារកែទម្រង់ការគ្រប់គ្រង់ហិរញ្ញវត្ថុសាធារណៈ។
                            </div>
                        </p>
                    </section>

                    <section class="reference">
                        <p class="padding-bottom-p">
                            <div class="text-indent">
                                សូម <span class="font-mef2">ឯកឧត្តមអគ្គបណ្ឌិតសភាចារ្យ</span> មេត្តាទទួលនូវការគោរពគាវកិច្ចដ៏ខ្ពង់ខ្ពស់អំពីខ្ញុំ
                            </div>
                        </p>
                    </section>

                    <section>
                        <div class="pull-right">
                            <center>
                                <br/>
                                <span class="font-mef2">
                                    អគ្គលេខាធិការ<br/>
                                    អគ្គលេខាធិការដ្ឋានគណៈកម្មាធិការដឹកនាំ<br/>
                                    ការងារកែទម្រង់ការគ្រប់គ្រងហិរញ្ញវត្ថុសាធារណៈ
                                </span>
                            </center>
                        </div>
                    </section>
                </div>
                <div class="clearfix"></div>
                <div class="container-inner">
                    <center>
                        <span class="font-mef2" style="color: #0d72a2; font-size: 12pt">
                            បញ្ជីសរុប<br/>
                        </span>
                        <span class="font-mef2" style="color: #0d72a2; font-size: 10pt">
                            មន្ត្រីរាជការទទួលប្រាក់ឧបត្ថម្ភលើកទឹកចិត្ត<br/>
                                <span v-if="incentiveInfo.count_month > 1">
                                    ប្រចាំខែ {{incentiveInfo.khmer_from_month}} ដល់ {{incentiveInfo.khmer_to_month}}
                                </span>
                                <span v-else>
                                    ប្រចាំខែ{{incentiveInfo.khmer_month}} 
                                </span>
                            <br/>
                        </span>
                        <div class="clearfix"></div>
                        <br/>
                    </center>
                    
                    <div class="content">
                        <table class="table" border="1">
                            <thead>
                                <tr style="background: rgb(234 227 34)">
                                    <th style="width: 4%"><center>ល.រ</center></th>
                                    <th style="width: 46%"><center>អង្គភាព</center></th>
                                    <th style="width: 12%"><center>ចំនួនមន្ត្រី</center></th>
                                    <th style="width: 20%"><center>ប្រាក់ឧមត្ថម្ភត្រូវបើក</center></th>
                                    <th style="width: 18%"><center>ផ្សេងៗ</center></th>
                                </tr>
                            </thead>
                            <tr class="line-height-tr" :data="tr" :key="indextr" v-for="(tr, indextr) in incentiveByEntity">
                                <td><center>{{tr.no}}</center></td>
                                <td>
                                    <!-- <b v-if="tr.entity.length < 43">{{tr.entity}}</b>
                                    <b v-else><small>{{tr.entity}}</small></b> -->
                                    <b>{{tr.entity}}</b>
                                </td>
                                <td><center>{{tr.total_officer}}</center></td>
                                <td>
                                    <span class="pull-right"><b>{{tr.total_incentive_with_reminder}}</b></span>
                                </td>
                                <td></td>
                            </tr>
                            
                            <!-- <tfooter> -->
                            <tr style="background-color: rgb(179 230 255 / 1);">
                                <td colspan="2"><span class="font-mef2"><center>សរុបរួម</center></span></td>
                                <td><center><b style="text-decoration: underline">{{incentiveInfo.total_officer}}</b></center></td>
                                <td><span style="text-decoration: underline" class="pull-right font-mef2">{{incentiveInfo.total_incentive_with_reminder_in_khmer}}</span></td>
                                <td></td>
                            </tr>
                            <!-- </tfooter> -->
                        </table>
                    </div>

                    <div class="sign-move-from-top" style="position: relative; font-size: 9pt">
                        <!-- <br/> -->
                        <div class="pull-right" style="position: relative; page-break-inside: avoid;">
                            <span>
                                <center>
                                    <!-- ថ្ងៃអង្គារ ០៧ រោច  ខែចេត្រ -->
                                    ថ្ងៃ{{ incentiveInfo.khmer_day }} ឆ្នាំ{{ incentiveInfo.khmer_date }}<br/>
                                    រាជធានីភ្នំពេញ ថ្ងៃទី{{incentiveInfo.khmer_current_day}}  
                                    ខែ{{incentiveInfo.khmer_current_month}} 
                                    ឆ្នាំ{{incentiveInfo.khmer_current_year}}<br/>
                                    <span class="font-mef2">រៀបចំដោយ <br/>មន្ត្រីជំនាញថវិកា<br/></span>
                                    <!-- <br/> -->
                                    <div style="padding-top: 33px" class="font-mef2">{{incentiveInfo.prepared_by}}</div>
                                </center>
                            </span>
                        </div>
                        <div class="clearfix"></div>
                        <div class="pull-left" style="margin-top: -20px;position: relative; page-break-inside: avoid;">
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
                        <div class="pull-right" style="margin-top: -30px;position: relative; page-break-inside: avoid;">
                            <span>
                                <center>
                                    បានឃើញ និងសូមគោរពជូនសម្រេចដ៍ខ្ពង់ខ្ពស់<br/>
                                    ឯកឧត្តម្ភអគ្គបណ្ឌិតសភាចារ្យ ឧបនាយករដ្ឋមន្ត្រី<br/>
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
                        <div class="clearfix"></div>
                        <div class="pull-left" style="margin-top: -20px;position: relative; page-break-inside: avoid;">
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
                                    <span class="font-mef2">ឧបនាយករដ្ឋមន្ត្រី<br/>
                                    រដ្ឋមន្ត្រីក្រសួងសេដ្ឋកិច្ចនិងហិរញ្ញវត្ថុ</span>
                                </center>
                            </span>
                        </div>
                    </div>
                </div>
                <div class="clearfix"></div>
                <!-- #################### -->
            </div>
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
            incentiveByEntity: ""
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
      onDownloadFile(){
          let _id = this.$route.params.id;
          
          const obj = {
              incentive_id: _id
          };
          this.$vs.loading()
            this.$vs.notify({
                title: 'Message',
                text: "កំពុងទាញយកទិន្នន័យ",
                iconPack: 'feather',
                icon: 'icon-check-circle',
                color: 'primary'
            });
          axios.post("/api/v1/report/convert/convert_annex_lm_to_excel", obj)
            .then((response) => {
            if(response.data.success == true){
                this.$vs.loading.close()
                window.open(response.data.data.url);
            }else{
                this.$vs.loading.close()
                this.$vs.notify({
                    title: 'Message',
                    text: "មានបញ្ហា,​ សូមត្រួតពិនិត្យពត៌មានឡើងវិញ។",
                    iconPack: 'feather',
                    icon: 'icon-check-circle',
                    color: 'danger'
                })
            }
        }).catch((error) => { 
            reject(error)
            this.$vs.notify({
                title: 'Message',
                text: "មានបញ្ហា,​ សូមត្រួតពិនិត្យពត៌មានឡើងវិញ។",
                iconPack: 'feather',
                icon: 'icon-check-circle',
                color: 'danger'
            })
            this.$vs.loading.close()
        })
      },
      getIncentiveInfo(){
        let _incentiveId = this.$route.params.id;
        return new Promise((resolve, reject) => {
            this.$vs.loading()
            axios.get("/api/v1/incentive/" + _incentiveId)
                .then((response) => {
                let _data = response.data
                this.incentiveInfo = _data.data.general
                this.incentiveByEntity = _data.data.incentiveByEntities
                if(_data.data){
                    this.$vs.loading.close()
                }
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

<style lang="scss">
    @import "@sass/vuexy/pages/report.scss";
</style>

<style lang="scss">
    @media print {
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

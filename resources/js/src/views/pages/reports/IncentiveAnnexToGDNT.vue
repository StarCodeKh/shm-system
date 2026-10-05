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
            <span>
                <div class="clearfix"></div>
                <!-- #################### -->
                <div class="consolidate-payroll-container rotate-screen" style="margin: 0 auto;">
                    <center>
                        <span class="font-mef2" style="color: #0d72a2; font-size: 10pt">
                            បញ្ជីរាយនាម <br/>
                            មន្រ្តីរាជការទទួលប្រាក់ឧបត្ថម្ភមុខងារក្រុមការងារកែទម្រង់ការគ្រប់គ្រងហិរញ្ញវត្ថុសាធារណៈ 
                            <span v-if="incentiveInfo.count_month > 1">
                                សម្រាប់ខែ {{incentiveInfo.khmer_from_month}} ដល់ {{incentiveInfo.khmer_to_month}}
                            </span>
                            <span v-else>
                                សម្រាប់ខែ{{incentiveInfo.khmer_month}} 
                            </span>
                             ឆ្នាំ{{incentiveInfo.khmer_year}} <br/><br/>
                        </span>
                        <div class="clearfix"></div>
                    </center>
                    <div id="content">
                        <table class="table" border="1">
                            <thead>
                                <tr style="background-color: #ece241">
                                    <th style="width: 50px;" colspan="2" class="border-dash">ល.រ</th>
                                    <!-- <th class="border-dash">អត្ត.</th> -->
                                    <th class="border-dash">គោត្តនាម និង នាម</th>
                                    <th class="border-dash">តួនាទី</th>
                                    <th class="border-dash">ឈ្មោះធនាគា</th>
                                    <th class="border-dash">លេខគណនី</th>
                                    <th class="border-dash">សន្ទស្សន៍</th>
                                    <th class="border-dash">ប្រាក់ឧបត្ថម្ភ</th>
                                    <th class="border-dash">ប្រាក់បង្វែរ</th>
                                    <th class="border-dash" style="background-color: #eac6cc">ប្រាក់រំលឹក</th>
                                    <th class="border-dash">ប្រាក់ឧបត្ថម្ភជាប់ពន្ធ</th>
                                    <th class="border-dash">ពន្ធត្រូវបង់</th>
                                    <th class="border-dash">ប្រាក់ត្រូវបើក</th>
                                    <th class="border-dash">ផ្សេងៗ</th>
                                </tr>
                                <tr style="background: #ac9e27; color: #ffffff;">
                                    <td style="font-size: 7pt;padding: 0;"><i><center>1</center></i></td>
                                    <!-- <td style="font-size: 7pt;padding: 0;"><i><center>2</center></i></td> -->
                                    <td style="font-size: 7pt;padding: 0;"><i><center>2</center></i></td>
                                    <td style="font-size: 7pt;padding: 0;"><i><center>3</center></i></td>
                                    <td style="font-size: 7pt;padding: 0;"><i><center>4</center></i></td>
                                    <td style="font-size: 7pt;padding: 0;"><i><center>5</center></i></td>
                                    <td style="font-size: 7pt;padding: 0;"><i><center>6</center></i></td>
                                    <td style="font-size: 7pt;padding: 0;"><i><center>7</center></i></td>
                                    <td style="font-size: 7pt;padding: 0;"><i><center>8</center></i></td>
                                    <td style="font-size: 7pt;padding: 0;"><i><center>9</center></i></td>
                                    <td style="font-size: 7pt;padding: 0;background-color: #eac6cc;"><i><center>10</center></i></td>
                                    <td style="font-size: 7pt;padding: 0;"><i><center>11=8+9+10</center></i></td>
                                    <td style="font-size: 7pt;padding: 0;"><i><center>12=គណនាពន្ធតាមថ្នាក់</center></i></td>
                                    <td style="font-size: 7pt;padding: 0;"><i><center>13=11-12</center></i></td>
                                    <td style="font-size: 7pt;padding: 0;"><i><center>14</center></i></td>
                                </tr>
                            </thead>
                            <tbody :data="tr" :key="indextr" v-for="(tr, indextr) in incentiveByEntities" style="width:100%">
                                <tr style="background: rgba(189, 215, 238, 1)">
                                    <td colspan="5">
                                        <b>{{tr.code}}-{{tr.entity}}</b>
                                    </td>
                                    <td></td>
                                    <td><center>សរុប {{tr.total_officer}}រូប</center></td>
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
                                    <!-- <td><center>{{ctr.identity_no}}</center></td> -->
                                    <td>{{ctr.first_name}} {{ctr.last_name}}</td>
                                    <td>{{ctr.duty}}</td>
                                    <td>{{ctr.bank}}</td>
                                    <td>&#8203;{{ctr.bank_account_no}}</td>
                                    <td>{{ctr.index}}</td>
                                    <td>{{ctr.incentive}}</td>
                                    <td>{{ctr.return_amount}}</td>
                                    <td>{{ctr.reminder_amount}}</td>
                                    <td>{{(ctr.total_incentive_with_reminder)}}</td>
                                    <td>{{ctr.tax}}</td>
                                    <td>{{ctr.incentive_after_tax}}</td>
                                    <td></td>
                                </tr>
                            </tbody>
                            <!-- <tfooter> -->
                            <tr style="background:rgba(198, 224, 180, 1)">
                                <td></td>
                                <td></td>
                                <!-- <td></td> -->
                                <td><span class="font-mef2">សរុប</span></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td><span class="font-mef2">{{incentiveInfo.total_incentive_in_khmer}}</span></td>
                                <td>{{incentiveInfo.total_return_amount_in_khmer}}</td>
                                <td>{{incentiveInfo.total_reminder_amount_in_khmer}}</td>
                                <td>{{(incentiveInfo.total_incentive_with_reminder_in_khmer)}}</td>
                                <td>{{incentiveInfo.total_tax_in_khmer}}</td>
                                <td>{{incentiveInfo.total_incentive_after_tax_in_khmer}}</td>
                                <td></td>
                            </tr>
                            <!-- </tfooter> -->
                        </table>
                    </div>
                    
                    <article>
                        <div>
                            <div class="pull-right" style="margin-right: 150px; font-size: 8pt;padding: 20px 0; line-height: 25px;">
                                <center>
                                    ថ្ងៃ <span style="font-family: arial !important">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span> 
                                    ខែ <span style="font-family: arial !important">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>  
                                    ឆ្នាំ{{incentiveInfo.khmer_date}}<br/>
                                    រាជធានីភ្នំពេញ 
                                    ថ្ងៃទី <span style="font-family: arial !important">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>
                                    ខែ <span style="font-family: arial !important">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span> 
                                    ឆ្នាំ{{incentiveInfo.khmer_current_year}}
                                </center>
                            </div>
                            <br/>
                        </div>
                        <div class="clearfix"></div>
                        <span style="position: relative;">
                            <div class="col-2">
                                <span class="font-mef2">
                                    <center>
                                        ពិនិត្យ និងសម្រេចដោយ<br/>
                                        អគ្គលេខាធិការ
                                        <br/>
                                        <br/>
                                        <br/>
                                        រស់ សីលវ៉ា
                                    </center>
                                </span>
                            </div>
                            <div class="col-2">
                                <span class="font-mef2">
                                    <center>
                                        ពិនិត្យ​ដោយ<br/>
                                        អគ្គលេខាធិការរង
                                        <br/>
                                        <br/>
                                        <br/>
                                        យ៉េត វីណែល
                                    </center>
                                </span>
                            </div>
                            <div class="col-22">
                                <span class="font-mef2">
                                    <center>
                                        ពិនិត្យដោយ<br/>
                                        ប្រធានផ្នែករដ្ឋបាល និងហិរញ្ញវត្ថុ និងត្រួតពិនិត្យនិងវាយតម្លៃ
                                        <br/>
                                        <br/>
                                        <br/>
                                        អ៊ុំ យុទ្ធី
                                    </center>
                                </span>
                            </div>
                            <div class="col-2">
                                <span class="font-mef2">
                                    <center>
                                        ពិនិត្យដោយ<br/>
                                        មន្ត្រីត្រួតពិនិត្យហិរញ្ញវត្ថុ
                                        <br/>
                                        <br/>
                                        <br/>
                                        តិប បូរិតា
                                    </center>
                                </span>
                            </div>
                            <div class="col-2">
                                <span class="font-mef2">
                                    <center>
                                        រៀបចំដោយ<br/>
                                        មន្ត្រីជំនាញថវិកា
                                        <br/>
                                        <br/>
                                        <br/>
                                        លឿង សុវណ្ណឌី
                                    </center>
                                </span>
                            </div>
                        </span>
                    </article>

                    <!-- <footer>
                        <div id="page-number">Page</div>
                    </footer> -->
                </div>
                <div class="clearfix"></div>
                <!-- #################### -->
            </span>
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
          axios.post("/api/v1/report/convert/convert_annex_gdnt_excel", obj)
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
                this.incentiveByBanks = _data.data.incentiveByBanks
                this.incentiveByEntities = _data.data.incentiveByEntities
                this.$vs.loading.close()
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

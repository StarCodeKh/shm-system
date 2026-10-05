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
            <div class="_print-move-header-from-top">
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
                        <center>
                            <img width="120px" src="@assets/images/logo/logo.png"/>
                            ក្រសួងសេដ្ឋកិច្ច និងហិរញ្ញវត្ថុ<br/>
                            អគ្គលេខាធិការដ្ឋានគណៈកម្មាធិការ<br/>
                            ដឹកនាំការងារកែទម្រង់ការគ្រប់គ្រងហិរញ្ញវត្ថុសាធារណៈ
                        </center>
                    </div><br/><br/>
                    <div class="clearfix"></div>
                    <center>    
                        <!-- <span class="font-mef2" style="color: #0d72a2; font-size: 12pt">
                            បញ្ជីសរុប<br/>
                        </span> -->
                        <br/>
                        <span class="font-mef2" style="color: #0d72a2; font-size: 10pt">
                            របាយការណ៍សង្ខេបនៃការប្រើប្រាស់ចំណូលមិនមែនសារពើពន្ធរបស់ក្រសួង-ស្ថាប័ន<br/>
                            ប្រមូលសម្រាប់ការផ្តល់ការលើកទឹកចិត្តលើការងារកែទម្រង់ការគ្រប់គ្រងហិរញ្ញវត្ថុសាធារណៈ ឆ្នាំ{{this.summaryInfo.currentYear}}<br/>
                        </span>
                        <div class="clearfix"></div>
                        <br/>
                    </center>
                    
                    <div class="first content">
                        <!-- <center>
                            <div class="font-mef2 data-amount border-green">
                                ចំណូលសរុប(គិតជាលានរៀល)<br/>
                                <b style="font-size: 18px">24,449​</b><br/>
                                (6.11 លានដុល្លា)
                            </div>
                            <div class="font-mef2 data-amount border-red">
                                ចំណាយសរុប(គិតជាលានរៀល)<br/>
                                <b style="font-size: 18px">24,449​</b><br/>
                                (6.11 លានដុល្លា)
                            </div>
                            <div class="font-mef2 data-amount border-orange">
                                សន្និធិសាច់ប្រាក់<br/>
                                <b style="font-size: 18px">24,449​</b><br/>
                                (6.11 លានដុល្លា)
                            </div>
                        </center> -->
                        <div class="vx-row move-left">
                            <!-- <div class="vx-col w-full sm:w-1/4 md:w-1/4 lg:w-1/4 xl:w-1/4 mb-base"> -->
                            <div class="font-mef2 data-amount border-blue">
                                <summary-data color="success" :flag="1" :chartOptions="chartOptionOfficers" :chartData="serieOfficers" :totalDollar="''" :statistic="summaryInfo.totalOfficer" statisticTitle="ចំនួនមន្ត្រីជាមធ្យមប្រចាំឆ្នាំ" :resultTitle="summaryInfo.currentDateOfficer" icon="UsersIcon" type='line'></summary-data>
                            </div>
                            <!-- </div> -->
                            <!-- <div class="vx-col w-full sm:w-1/4 md:w-1/4 lg:w-1/4 xl:w-1/4 mb-base"> -->
                            <div class="font-mef2 data-amount border-green">
                                <summary-data color="primary" :flag="2" :chartOptions="chartOptionIncomes" :chartData="incomeCompareSeries" :totalDollar="summaryInfo.totalIncomeDollar" :statistic="summaryInfo.totalIncome" statisticTitle="ចំណូលសរុប(គិតជាលានរៀល)" :resultTitle="summaryInfo.lastUpdatedDateIncome" icon="ArrowDownIcon" type='area'></summary-data>
                            </div>
                            <!-- </div> -->

                            <!-- <div class="vx-col w-full sm:w-1/4 md:w-1/4 lg:w-1/4 xl:w-1/4 mb-base"> -->
                            <div class="font-mef2 data-amount border-red">
                                <summary-data color="danger" :flag="3" :chartOptions="chartOptionExpenses" :chartData="expenseCompareSeries" :totalDollar="summaryInfo.totalExpenseDollar" :statistic="summaryInfo.totalExpense" statisticTitle="ចំណាយសរុប(គិតជាលានរៀល)" :resultTitle="summaryInfo.lastUpdatedDateExp" icon="ArrowUpIcon" type='area'></summary-data>
                            </div>
                            <!-- </div> -->

                            <!-- <div class="vx-col w-full sm:w-1/4 md:w-1/4 lg:w-1/4 xl:w-1/4 mb-base"> -->
                            <div class="font-mef2 data-amount border-orange">
                                <summary-data color="warning" :flag="4" :chartOptions="chartOptionRemains" :chartData="remainCompareSeries" :totalDollar="summaryInfo.totalRemainDollar" :statistic="summaryInfo.totalRemain" statisticTitle="សន្និធិសាច់ប្រាក់សរុប(គិតជាលានរៀល)" :resultTitle="issueDateOfficer" icon="BarChart2Icon" type='area'></summary-data>
                            </div>
                            <!-- </div> -->
                        </div>
                        <div class="clearfix"></div>
                        <br/>
                        <table class="table" border="1">
                            <thead>
                                <tr style="background: #e3e3e3">
                                    <th><center>ល.រ</center></th>
                                    <th><center>កូដ+អង្គភាព</center></th>
                                    <th colspan="2"><center>បណ្តាឆ្នាំមុន <span class="color-blue">*</span></center></th>
                                    <th colspan="2"><center>ក្នុងឆ្នាំ <span class="color-blue">**</span></center></th>
                                    <th colspan="2"><center>បូកយោង</center></th>
                                    <th rowspan="1"><center>សមតុល្យ</center></th>
                                </tr>
                                <tr>
                                    <th colspan="2"><center></center></th>
                                    <th style="color: green"><center>ចំណូល</center></th>
                                    <th style="color: red"><center>ចំណាយ</center></th>
                                    
                                    <th style="color: green"><center>ចំណូល</center></th>
                                    <th style="color: red"><center>ចំណាយ</center></th>

                                    <th style="color: green"><center>ចំណូល</center></th>
                                    <th style="color: red"><center>ចំណាយ</center></th>

                                    <th></th>
                                </tr>
                            </thead>
                            
                            <!-- <tr>
                                <td colspan="2"><b>ខ.វិស័យរដ្ឋបាលទូទៅ</b></td>
                                <td>$192,098,00</td>
                                <td>$192,098,00</td>
                                
                                <td>$192,098,00</td>
                                <td>$192,098,00</td>
                                
                                <td>$192,098,00</td>
                                <td>$192,098,00</td>
                                <td></td>
                            </tr> -->

                            <tr :data="tr" :key="indextr" v-for="(tr, indextr) in incentiveInfo">
                                <td><center>{{indextr + 1}}</center></td>
                                <td><b>{{tr.entity_code}}-{{tr.entity}}</b></td>
                                <td>
                                    <span v-if="Number(tr.last_total_income) == 0"><center>-</center></span>
                                    <span v-else>{{Number(tr.last_total_income).toLocaleString(2)}}</span>
                                </td>
                                <td>
                                    <span v-if="Number(tr.last_total_expense) == 0"><center>-</center></span>
                                    <span v-else>{{Number(tr.last_total_expense).toLocaleString(2)}}</span>
                                </td>
                                <td>
                                    <span v-if="Number(tr.total_income) == 0"><center>-</center></span>
                                    <span v-else>{{Number(tr.total_income).toLocaleString(2)}}</span>
                                </td>
                                <td>
                                    <span v-if="Number(tr.total_expense) == 0"><center>-</center></span>
                                    <span v-else>{{Number(tr.total_expense).toLocaleString(2)}}</span>
                                </td>
                                
                                <td>
                                    <span v-if="Number(tr.total_income + tr.last_total_income) == 0"><center>-</center></span>
                                    <span v-else>{{Number(tr.total_income + tr.last_total_income).toLocaleString(2)}}</span>
                                </td>
                                <td>
                                    <span v-if="Number(tr.total_expense + tr.last_total_expense) == 0"><center>-</center></span>
                                    <span v-else>{{Number(tr.total_expense + tr.last_total_expense).toLocaleString(2)}}</span>
                                </td>
                                <td>
                                    <span v-if="(tr.total_income + tr.last_total_income) - (tr.total_expense + tr.last_total_expense) < 0" style="color: red">
                                        {{Number((tr.total_income + tr.last_total_income) - (tr.total_expense + tr.last_total_expense)).toLocaleString(2)}}
                                    </span>
                                    <span v-else style="color: green">
                                        {{Number((tr.total_income + tr.last_total_income) - (tr.total_expense + tr.last_total_expense)).toLocaleString(2)}}
                                    </span>
                                </td>
                            </tr>


                            <!-- <tr class="line-height-tr" :data="tr" :key="indextr" v-for="(tr, indextr) in incentiveByEntity">
                                <td><center>{{tr.no}}</center></td>
                                <td>
                                    <b>{{tr.entity}}</b>
                                </td>
                                <td><center>{{tr.total_officer}}</center></td>
                                <td>
                                    <span class="pull-right"><b>{{tr.total_incentive}}</b></span>
                                </td>
                                <td></td>
                            </tr> -->
                            
                            <!-- <tfooter> -->
                            <tr style="background-color: rgb(179 230 255 / 1);">
                                <td colspan="2"><span class="font-mef2"><center>សរុប(គិតជារៀល)</center></span></td>
                                <td><b style="text-decoration: underline">{{Number(totalLastYearIncome).toLocaleString(2)}}</b></td>
                                <td><b style="text-decoration: underline">{{Number(totalLastYearExpense).toLocaleString(2)}}</b></td>
                                <td><b style="text-decoration: underline">{{Number(totalThisYearIncome).toLocaleString(2)}}</b></td>
                                <td><b style="text-decoration: underline">{{Number(totalThisYearExpense).toLocaleString(2)}}</b></td>
                                <td><b style="text-decoration: underline">{{Number(totalLastYearIncome + totalThisYearIncome).toLocaleString(2)}}</b></td>
                                <td><b style="text-decoration: underline">{{Number(totalLastYearExpense + totalThisYearExpense).toLocaleString(2)}}</b></td>
                                <td><b style="text-decoration: underline">{{Number((totalLastYearIncome + totalThisYearIncome) - (totalLastYearExpense + totalThisYearExpense)).toLocaleString(2)}}</b></td>
                            </tr>
                            <tr style="background-color: rgb(179 230 255 / 1);">
                                <td colspan="2"><span class="font-mef2"><center>សរុប(គិតជាដុល្លា)</center></span></td>
                                <td><b style="text-decoration: underline">$ {{Number(totalLastYearIncome / exchangeRate).toLocaleString(2)}}</b></td>
                                <td><b style="text-decoration: underline">$ {{Number(totalLastYearExpense / exchangeRate).toLocaleString(2)}}</b></td>
                                <td><b style="text-decoration: underline">$ {{Number(totalThisYearIncome / exchangeRate).toLocaleString(2)}}</b></td>
                                <td><b style="text-decoration: underline">$ {{Number(totalThisYearExpense / exchangeRate).toLocaleString(2)}}</b></td>
                                <td><b style="text-decoration: underline">$ {{Number((totalLastYearIncome + totalThisYearIncome)/ exchangeRate).toLocaleString(2)}}</b></td>
                                <td><b style="text-decoration: underline">$ {{Number((totalLastYearExpense + totalThisYearExpense) / exchangeRate).toLocaleString(2)}}</b></td>
                                <td><b style="text-decoration: underline">$ {{Number(((totalLastYearIncome + totalThisYearIncome) - (totalLastYearExpense + totalThisYearExpense)) / exchangeRate).toLocaleString(2)}}</b></td>
                            </tr>
                            <!-- </tfooter> -->
                        </table>
                    </div>

                    <div class="sign-move-from-top" style="position: relative; font-size: 9pt">
                        <span style="text-decoration: underline;">សម្គាល់៖</span>
                        <br/>
                        <span class="color-blue">*</span> បណ្តាឆ្នាំមុន ៖ ២០១៨,​២០១៩,២០២០<br/>
                        <span class="color-blue">**</span> ក្នុងឆ្នាំ៖ {{this.summaryInfo.currentYear}}
                        <br/>
                        <div class="pull-right">
                            <span>
                                <center>
                                    <!-- ថ្ងៃអង្គារ ០៧ រោច  ខែចេត្រ -->
                                    <!-- ថ្ងៃ{{this.summaryInfo.currentDay}} {{this.summaryInfo.currentDate}} រោច  ខែ{{this.summaryInfo.khmer_date}}<br/>-->
                                    រាជធានីភ្នំពេញ, ថ្ងៃទី​ {{this.summaryInfo.currentDate}}
                                    ខែ {{this.summaryInfo.currentMonth}}
                                    ឆ្នាំ {{this.summaryInfo.currentYear}}<br/>
                                    <!-- <span class="font-mef2">រៀបចំដោយ <br/>មន្ត្រីជំនាញថវិកា<br/></span>
                                    <br/><br/>
                                    <span class="font-mef2">លឿង សុវណ្ណឌី</span> -->
                                </center>
                            </span>
                        </div>
                        <div class="clearfix"></div>
                        <!-- <div class="pull-left" style="margin-top: -20px;">
                            <span>
                                <center>
                                    បានឃើញ និងពិនិត្យត្រឹមត្រូវ<br/>
                                    សូមគោរពជូន ឯកឧត្តមអគ្គលេខាធិការ <br/>
                                    មេត្តាពិនិត្យ និងសម្រេចដ៏ខ្ពង់ខ្ពស់ <br/>
                                    ថ្ងៃ <span style="font-family: arial !important"><span style="font-family: arial !important">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span></span> 
                                    ខែ <span style="font-family: arial !important">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>  
                                    ឆ្នាំ២០២០ <br/>
                                    រាជធានីភ្នំពេញ 
                                    ថ្ងៃ <span style="font-family: arial !important">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span> 
                                    ខែ <span style="font-family: arial !important">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span> 
                                    ឆ្នាំ២០២០ <br/>
                                    <span class="font-mef2">អគ្គលេខាធិការរង</span>
                                </center>
                            </span>
                        </div>
                        <div class="clearfix"></div>
                        <div class="pull-right" style="margin-top: 5px;">
                            <span>
                                <center>
                                    បានឃើញ និងសូមគោរពជូនសម្រេចដ៍ខ្ពង់ខ្ពស់<br/>
                                    ឯកឧត្តម្ភអគ្គបណ្ឌិតសភាចារ្យ ឧបនាយករដ្ឋមន្ត្រី<br/>
                                    ថ្ងៃ <span style="font-family: arial !important"><span style="font-family: arial !important">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span></span> 
                                    ខែ <span style="font-family: arial !important">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>  
                                    ឆ្នាំ២០២០ <br/>
                                    រាជធានីភ្នំពេញ 
                                    ថ្ងៃ <span style="font-family: arial !important">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span> 
                                    ខែ <span style="font-family: arial !important">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span> 
                                    ឆ្នាំ២០២០ <br/>
                                    <span class="font-mef2">អគ្គលេខាធិការ<br/>
                                    អគ្គលេខាធិការដ្ឋានគណៈកម្មាធិការ<br/>
                                    ដឹកនាំការងារកែទម្រង់ការគ្រប់គ្រងហិរញ្ញវត្ថុសាធារណៈ</span>
                                </center>
                            </span>
                        </div>
                        <div class="clearfix"></div>
                        <div class="pull-left" style="margin-top: 60px;">
                            <span>
                                <center>
                                    បានឃើញ និងឯកភាព<br/>
                                    ថ្ងៃ <span style="font-family: arial !important"><span style="font-family: arial !important">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span></span> 
                                    ខែ <span style="font-family: arial !important">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>  
                                    ឆ្ឆ្នាំ២០២០ <br/>
                                    រាជធានីភ្នំពេញ 
                                    ថ្ងៃ <span style="font-family: arial !important">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span> 
                                    ខែ <span style="font-family: arial !important">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span> 
                                    ឆ្នាំ២០២០ <br/>
                                    <span class="font-mef2">ឧបនាយករដ្ឋមន្ត្រី<br/>
                                    រដ្ឋមន្ត្រីក្រសួងសេដ្ឋកិច្ចនិងហិរញ្ញវត្ថុ</span>
                                </center>
                            </span>
                        </div> -->
                    </div>
                </div>
                <div class="clearfix"></div>
                <!-- <div style="position: fixed; bottom: 0; left: 50%; right: 0">
                    ទំព័រទី​​ 1/4
                </div> -->
                <!-- #################### -->
            </div>
        </vx-card>
    </div>
</template>

<script>
import axios from "@/axios.js"
import SummaryData from '@/views/components/charts-and-maps/charts/SummaryData.vue'
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
            incentiveByEntity: "",
            chartOptionOfficers: {
                chart: {
                    toolbar: { show: false },
                    height: 350,
                    type: 'line',
                    stacked: false
                },
                legend: {
                    show: true,
                },
                dataLabels: {
                    enabled: true
                },
                stroke: {
                    width: [1, 1, 4]
                },
                title: {
                    text: '',
                    align: 'left',
                    offsetX: 110
                },
                xaxis: {
                    categories: [2018, 2019, 2020],
                },
                yaxis: {
                    show: false,
                },
                tooltip: {
                    x: { show: false }
                },
                legend: {
                    horizontalAlign: 'left',
                    offsetX: 40
                }
            },
            
            chartOptionIncomes: {
                chart: {
                    toolbar: { show: false },
                    dropShadow: {
                        enabled: false,
                        top: 5,
                        left: 0,
                        blur: 4,
                        opacity: 0.10,
                    },
                },
                stroke: {
                    curve: 'smooth',
                    dashArray: [0, 8],
                    width: [4, 2],
                },
                grid: {
                    borderColor: '#e7e7e7',
                },
                legend: {
                    show: false,
                },
                colors: ['#28C76F', '#999999'],
                fill: {
                    type: 'gradient',
                    gradient: {
                        shade: 'dark',
                        inverseColors: false,
                        // gradientToColors: ['#7367F0', '#b8c2cc'],
                        shadeIntensity: 1,
                        type: 'horizontal',
                        opacityFrom: 0.7,
                        opacityTo: 0.45,
                        // stops: [0, 100, 100, 100]
                    },
                },
                markers: {
                    size: 0,
                    hover: {
                        size: 5
                    }
                },
                xaxis: {
                    labels: {
                        style: {
                            cssClass: 'text-grey fill-current',
                        }
                    },
                    axisTicks: {
                        show: false,
                    },
                    categories: ['Q1', 'Q2', 'Q3', 'Q4'],
                    axisBorder: {
                        show: false,
                    },
                },
                yaxis: {
                    show: false,
                },
                tooltip: {
                    x: { show: false }
                }
            },
            chartOptionExpenses: {
                chart: {
                    toolbar: { show: false },
                    dropShadow: {
                        enabled: true,
                        top: 5,
                        left: 0,
                        blur: 4,
                        opacity: 0.10,
                    },
                },
                stroke: {
                    curve: 'smooth',
                    dashArray: [0, 8],
                    width: [4, 2],
                },
                grid: {
                    borderColor: '#e7e7e7',
                },
                legend: {
                    show: false,
                },
                colors: ['#EA5455', '#999999'],
                fill: {
                    type: 'gradient',
                    gradient: {
                        shade: 'dark',
                        inverseColors: false,
                        // gradientToColors: ['#7367F0', '#b8c2cc'],
                        shadeIntensity: 1,
                        type: 'horizontal',
                        opacityFrom: 0.7,
                        opacityTo: 0.45,
                        // stops: [0, 100, 100, 100]
                    },
                },
                markers: {
                    size: 0,
                    hover: {
                        size: 5
                    }
                },
                xaxis: {
                    labels: {
                        style: {
                            cssClass: 'text-grey fill-current',
                        }
                    },
                    axisTicks: {
                        show: false,
                    },
                    categories: ['Q1', 'Q2', 'Q3', 'Q4'],
                    axisBorder: {
                        show: false,
                    },
                },
                yaxis: {
                    show: false,
                },
                tooltip: {
                    x: { show: false }
                }
            },
            chartOptionRemains: {
                chart: {
                    toolbar: { show: false },
                    dropShadow: {
                        enabled: true,
                        top: 5,
                        left: 0,
                        blur: 4,
                        opacity: 0.10,
                    },
                },
                stroke: {
                    curve: 'smooth',
                    dashArray: [0, 8],
                    width: [4, 2],
                },
                grid: {
                    borderColor: '#e7e7e7',
                },
                legend: {
                    show: false,
                },
                colors: ['#FF9F43', '#999999'],
                fill: {
                    type: 'gradient',
                    gradient: {
                        shade: 'dark',
                        inverseColors: false,
                        // gradientToColors: ['#7367F0', '#b8c2cc'],
                        shadeIntensity: 1,
                        type: 'horizontal',
                        opacityFrom: 0.7,
                        opacityTo: 0.45,
                        // stops: [0, 100, 100, 100]
                    },
                },
                markers: {
                    size: 0,
                    hover: {
                        size: 5
                    }
                },
                xaxis: {
                    labels: {
                        style: {
                            cssClass: 'text-grey fill-current',
                        }
                    },
                    axisTicks: {
                        show: false,
                    },
                    categories: ['Q1', 'Q2', 'Q3', 'Q4'],
                    axisBorder: {
                        show: false,
                    },
                },
                yaxis: {
                    show: false,
                },
                tooltip: {
                    x: { show: false }
                }
            },
            serieOfficers: [
                {
                    name: 'Number of Officer',
                    type: 'column',
                    data: [0, 0, 0]
                }
            ],
            incomeCompareSeries: [
                {
                    name: ['This year'],
                    data: [0, 0, 0, 0]
                },
                // {
                //     name: ['Last Year'],
                //     data: [0, 0, 0, 0]
                // }
            ],
            expenseCompareSeries: [
                {
                    name: ['This year'],
                    data: [0, 0, 0, 0]
                },
                // {
                //     name: ['Last Year'],
                //     data: [0, 0, 0, 0]
                // }
            ],
            remainCompareSeries: [
                {
                    name: ['This year'],
                    data: [0, 0, 0, 0]
                },
                // {
                //     name: ['Last Year'],
                //     data: [0, 0, 0, 0]
                // }
            ],
            summaryInfo: {
                totalIncentive: 0
            },
            officers: [],
            years: [],
            issueDateOfficer: '',
            exchangeRate: 4000,
            currentYear: new Date().getFullYear()
        }
    },
    computed: {
        totalLastYearIncome() {
            return this.incentiveInfo.reduce((total, item) => {
                return total + item.last_total_income;
            }, 0);
        },
        totalThisYearIncome() {
            return this.incentiveInfo.reduce((total, item) => {
                return Number(total + item.total_income);
            }, 0);
        },
        totalLastYearExpense() {
            return this.incentiveInfo.reduce((total, item) => {
                return total + item.last_total_expense;
            }, 0);
        },
        totalThisYearExpense() {
            return this.incentiveInfo.reduce((total, item) => {
                return total + item.total_expense;
            }, 0);
        }
    },
    created(){
        this.getSummary(this.currentYear);
        this.getIncentiveInfo();
    },
    methods: {
      printInvoice() {
        window.print()
      },
      getIncentiveInfo(){
        let _incentiveId = this.$route.params.id;
        return new Promise((resolve, reject) => {
            this.$vs.loading()
            axios.get("/api/v1/report/summary_income_outcome")
                .then((response) => {
                let _data = response.data
                this.incentiveInfo = _data.data
                // this.incentiveByEntity = _data.data.incentiveByEntities
                if(_data.data){
                    this.$vs.loading.close()
                }
            }).catch((error) => { reject(error) })
        })
      },
      getSummary(currentYear){
            return new Promise((resolve, reject) => {
                axios.get("/api/v1/report/summary?filter=" + currentYear)
                    .then((response) => {
                    let _data = response["data"]["data"];
                    this.summaryInfo = response["data"]["data"];
                    this.issueDateOfficer = 'គិតត្រឹមថ្ងៃទី' + _data.currentDate + 'ខែ' + _data.currentMonth + 'ឆ្នាំ' + _data.currentYear;
                    this.incomeCompareSeries = [
                        {
                            name: ['This year'],
                            data: response["data"]["data"]["quaterThisYearIncome"]
                        },
                        {
                            name: ['Last Year'],
                            data: response["data"]["data"]["quaterLastYearIncome"]
                        }
                    ]

                    this.expenseCompareSeries = [
                        {
                            name: ['This year'],
                            data: response["data"]["data"]["quaterThisYearExpense"]
                        },
                        {
                            name: ['Last Year'],
                            data: response["data"]["data"]["quaterLastYearExpense"]
                        }
                    ]
                    this.remainCompareSeries = [
                        {
                            name: ['This year'],
                            data: response["data"]["data"]["remainAmountThisYear"]
                        },
                        {
                            name: ['Last Year'],
                            data: response["data"]["data"]["remainAmountLastYear"]
                        }
                    ]
                    
                    // Officer Records All Years
                    for(let j = 0; j < response["data"]["data"]["officerByYears"].length; j++){
                        const _num_officer = response["data"]["data"]["officerByYears"][j]["number_officer"];
                        const _year = response["data"]["data"]["officerByYears"][j]["year"];
                        this.officers.push(_num_officer);
                        this.years.push(_year);
                    }
                    this.serieOfficers = [
                        {
                            name: 'Number of Officer',
                            type: 'column',
                            data: this.officers
                        }
                    ]

                    this.chartOptionOfficers = {
                        chart: {
                            toolbar: { show: false },
                            height: 200,
                            type: 'line',
                            stacked: false
                        },
                        legend: {
                            show: false,
                        },
                        dataLabels: {
                            enabled: true
                        },
                        stroke: {
                            width: [1, 1, 4]
                        },
                        title: {
                            text: '',
                            align: 'left',
                            offsetX: 110
                        },
                        xaxis: {
                            categories: this.years,
                        },
                        yaxis: {
                            show: false,
                        },
                        tooltip: {
                            x: { show: false }
                        },
                        legend: {
                            horizontalAlign: 'left',
                            offsetX: 40
                        }
                    };
                }).catch((error) => { reject(error) })
            })
        },
    },
    components: {
        SummaryData
    },
    mounted() {
      this.$emit("setAppClasses", "invoice-page")
    }
}
</script>
<style>
    /* body {counter-reset:page}
    div.page {page-break-before:always; counter-increment:page}
    div.page:after {display:block; text-align:right; content:counter(page)}
    div.first.page {page-break-before:avoid} */
    @print{
        @page :header {display: none;}
        /* @page {
            @bottom-right {
                content: counter(page) " Page of 23" counter(pages);
            }
        } */
    }
</style>
</style>
<style lang="scss">
    @import "@sass/vuexy/pages/report.scss";
    .border-green{border: 1px solid green; color: green}
    .border-red{border: 1px solid red; color: red}
    .border-orange{border: 1px solid orange; color: orange;}
    .border-blue{border: 1px solid orange; color: blue;}
    .color-blue{
        color: blue;
    }
    .move-left{margin-left: 85px}
    .data-amount{
        border-radius: 4px;
        padding: 0px;
        // height: 120px;
        width: 246.5px;
        // float: left;
        margin: 2px;
        box-shadow: 2;
    }
    table.table tr td{
        line-height: 30px;
        text-indent: 10px;
    }
</style>

<style lang="scss">
    @media print {
        .move-left{margin-left: 0px !important}
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

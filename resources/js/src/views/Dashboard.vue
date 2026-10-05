<template>
    <div class="main-dashboard">
        <div class="switch-currency-block">
            <CurrencyDropDown
            :value="cardCurrency.currency"
            @change="cardCurrency.currency = $event" class="ml-2"/>

            <flat-pickr
                v-model="dateRange"
                :config="config"
                class="vs-input ml-2 date-rang-width"
                placeholder="Select Date Range"/>
        </div>
        
        <div class="vx-row">
            <div class="vx-col w-full sm:w-1/2 md:w-1/2 lg:w-1/4 xl:w-1/4 mb-base">
                <!-- <div class="line-area-chart">
                    <h3>ការប្រៀបធៀបចំនួនមន្ត្រី</h3>
                    <vue-apex-charts ref="apexChart" type="line" height=200 width='100%' :options="chartOptionOfficers" :series="serieOfficers" />
                </div> -->
                <OfficerStatistic :data="data"></OfficerStatistic>
                <!-- <statistics-card-line
                  icon="UsersIcon"
                  :statistic=" totalMembers + ' នាក់'"
                  statisticTitle="ចំនួនសមាជិកសរុប"
                  :chartData="totalOfficerLatest5YearsSeries.series"
                  :showCurrency="false"
                  type='area' /> -->
            </div>

            <div class="vx-col w-full sm:w-1/2 md:w-1/2 lg:w-1/4 xl:w-1/4 mb-base">
                <statistics-card-line
                    icon="DollarSignIcon"
                    :currency="cardCurrency.currency"
                    @currency-change="cardCurrency.currency = $event"
                    :showCurrency="false"
                    :statistic="cardCurrency.currency === 'KHR'
                        ? totalIncomeKHR
                        : totalIncomeUSD"
                    :statisticTitle="cardCurrency.currency === 'KHR'
                        ? 'ចំណូលសរុប (គិតជារៀល)'
                        : 'ចំណូលសរុប (គិតជាដុល្លារ)'"
                    :chartData="revenueGenerated.series"
                    color="success"
                    type="area"
                />
            </div>

            <div class="vx-col w-full sm:w-1/2 md:w-1/2 lg:w-1/4 xl:w-1/4 mb-base">
                <statistics-card-line
                    icon="DollarSignIcon"
                    :currency="cardCurrency.currency"
                    @currency-change="cardCurrency.currency = $event"
                    :showCurrency="false"
                    :statistic="cardCurrency.currency === 'KHR'
                        ? totalExpenseKHR
                        : totalExpenseUSD"
                    :statisticTitle="cardCurrency.currency === 'KHR'
                        ? 'ចំណាយសរុប (គិតជារៀល)'
                        : 'ចំណាយសរុប (គិតជាដុល្លារ)'"
                    :chartData="quarterlySales.series"
                    color="danger"
                    type="area"
                />
            </div>

            <div class="vx-col w-full sm:w-1/2 md:w-1/2 lg:w-1/4 xl:w-1/4 mb-base">
                <statistics-card-line
                    icon="DollarSignIcon"
                    :currency="cardCurrency.currency"
                    @currency-change="cardCurrency.currency = $event"
                    :showCurrency="false"
                    :statistic="cardCurrency.currency === 'KHR'
                        ? totalRemainingBalanceKHR
                        : totalRemainingBalanceUSD"
                    :statisticTitle="cardCurrency.currency === 'KHR'
                        ? 'សន្និធិសាច់ប្រាក់សរុប (គិតជារៀល)'
                        : 'សន្និធិសាច់ប្រាក់សរុប (គិតជាដុល្លារ)'"
                    :chartData="ordersRecevied.series"
                    color="warning"
                    type="area"
                />
            </div>

        </div>

         <div class="vx-row">
            
            <!-- ប្រៀបធៀបចំណូល/ចំណាយ -->
            <div class="vx-col w-full md:w-2/3 mb-base">
                <vx-card title="ប្រៀបធៀបចំណូល/ចំណាយ">
                    <template slot="actions">
                         <QuarterDropDown
                            v-model="quarterFilter"
                            @change="onQuarterChange"
                            class="ml-2 quarter-block"
                        />
                    </template>
                    <div slot="no-body" class="p-6 pb-0">
                        <div class="flex">
                        <!-- Income -->
                        <div class="mr-6">
                            <p class="mb-1 font-semibold">
                                {{ cardCurrency.currency === 'KHR' ? 'ចំណូល' : 'Income' }}
                            </p>

                            <p class="text-3xl text-success">
                                <sup class="text-base mr-1">
                                 {{ cardCurrency.currency === 'KHR' ? '៛' : '$' }}
                                </sup>
                                {{ cardCurrency.currency === 'KHR'
                                    ? quarterly_total_income_khr
                                    : quarterly_total_income_usd
                                }}
                            </p>
                         </div>

                        <!-- Expense -->
                        <div>
                        <p class="mb-1 font-semibold">
                            {{ cardCurrency.currency === 'KHR' ? 'ចំណាយ' : 'Expense' }}
                        </p>

                        <p class="text-3xl">
                            <sup class="text-base mr-1">
                            {{ cardCurrency.currency === 'KHR' ? '៛' : '$' }}
                            </sup>
                            {{ cardCurrency.currency === 'KHR'
                                ? quarterly_total_expense_khr
                                : quarterly_total_expense_usd
                            }}
                        </p>
                        </div>
                    </div>
                        <IncomeExpenseChart :data="data"></IncomeExpenseChart>
                    </div>
                </vx-card>
            </div>

            <!-- %នៃការបង់ប្រាក់សមាជិក -->
            <div class="vx-col w-full md:w-1/3 mb-base">
                <vx-card title="%នៃការបង់ប្រាក់សមាជិក">
                    <template slot="actions">
                        <feather-icon icon="HelpCircleIcon" svgClasses="w-6 h-6 text-grey"></feather-icon>
                    </template>

                    <!-- CHART -->
                    <template slot="no-body">
                        <div class="mt-10">
                            <vue-apex-charts type=radialBar height=240 :options="analyticsData.goalOverviewRadialBar.chartOptions" 
                            :series="officerPaymentHistory.percent_paid" />
                        </div>
                    </template>

                    <!-- DATA -->
                    <div class="flex justify-between text-center mt-6" slot="no-body-bottom">
                        <div class="w-1/2 border border-solid d-theme-border-grey-light border-r-0 border-b-0 border-l-0">
                            <p class="mt-4">បានបង់រួច</p>
                            <p class="mb-4 text-3xl font-semibold">{{officerPaymentHistory.paid}}</p>
                        </div>
                        <div class="w-1/2 border border-solid d-theme-border-grey-light border-r-0 border-b-0">
                            <p class="mt-4">ពុំទាន់បង់</p>
                            <p class="mb-4 text-3xl font-semibold">{{officerPaymentHistory.unpaid}}</p>
                        </div>
                    </div>
                </vx-card>
            </div>

        </div>
        
        <div class="vx-row">
            <!-- ស្ថានភាពសមាជិក -->
            <div class="vx-col w-full lg:w-1/2 mb-base">
                <OfficerStatusChart :data="data"></OfficerStatusChart>
            </div>

            <!-- មុខសញ្ញានៃប្រាក់ឧបត្ថម្ភ -->
             <div class="vx-col w-full md:w-1/2 mb-base">
                <vx-card title="មុខសញ្ញានៃប្រាក់ឧបត្ថម្ភ">
                    <!-- SLOT = ACTIONS -->
                    <!-- <template slot="actions">
                        <CurrencyDropDown
                            :value="cardCurrency.currency"
                            @change="cardCurrency.currency = $event"
                        />
                    </template> -->

                    <div slot="no-body">
                        <!-- CHART -->
                        <vue-apex-charts type=pie height=345 class="mt-10 mb-10" :options="IncentivePie.chartOptions" :series="cardCurrency.currency === 'KHR'
                        ? officerIncentive.series_khr
                        : officerIncentive.series_usd" />

                        <!-- CHART DATA -->
                        <ul class="mb-1">
                            <li v-for="incentive in officerIncentive.totalIncentive" :key="incentive.payment_type_name" class="flex justify-between py-3 px-6 border d-theme-border-grey-light border-solid border-r-0 border-l-0 border-b-0">
                                <span class="flex items-center">
                                    <span class="inline-block h-3 w-3 rounded-full mr-2" :class="`bg-primary`"></span>
                                    <span class="font-semibold">{{ incentive.payment_type_name }}</span>
                                </span>
                                <span>
                                    {{ cardCurrency.currency === 'KHR'
                                        ? `៛ ${incentive.total_amount_khr_kh}`
                                        : `$ ${incentive.total_amount_usd}`
                                    }}
                                </span>
                            </li>
                        </ul>
                    </div>

                </vx-card>
            </div>
        </div>

        <!-- ចំណូលតាមស្ថាប័ន -->
        <div class="vx-row">
            <div class="vx-col md:w-1/1 w-full mb-base">
                <vx-card title="ចំណូលតាមស្ថាប័ន" code-toggler>
                    <!-- <vue-apex-charts type="bar" height="350" :options="apexChatData.barChart.chartOptions" :series="apexChatData.barChart.series"></vue-apex-charts>
                    <template slot="codeContainer">
                        {{ apexChatData.barChartCode }}
                    </template> -->
                    <EntityIncomeChart :data="data"></EntityIncomeChart>
                </vx-card>
            </div>
            <div class="vx-col md:w-1/1 w-full">
                <!-- Monthly Income -->
                 <MonthlyIncomeChart :data="data"></MonthlyIncomeChart>
                <!-- <vx-card title="ចំណូលប្រចាំខែ" class="mb-base">
                    <template slot="actions">
                        <feather-icon icon="SettingsIcon" svgClasses="w-6 h-6 text-grey"></feather-icon>
                    </template>
                    <div slot="no-body" class="p-6 pb-0">
                        <vue-apex-charts type=line height=266 :options="analyticsData.salesLine.chartOptions" :series="salesLine.series" />
                    </div>
                </vx-card> -->
            </div>
        </div>

    </div>
</template>




<script>
import VuePerfectScrollbar from 'vue-perfect-scrollbar'
import VueApexCharts from 'vue-apexcharts'
import StatisticsCardLine from '@/components/statistics-cards/StatisticsCardLine.vue'
import analyticsData from './components/ui-elements/card/analyticsData.js'
import apexChatData from './components/charts-and-maps/charts/apex-charts/apexChartData.js'
import ChangeTimeDurationDropdown from '@/components/ChangeTimeDurationDropdown.vue'
import IncomeSector from '@/views/components/charts-and-maps/charts/IncomeSector.vue'
import ExpenseSector from '@/views/components/charts-and-maps/charts/ExpenseSector.vue'
import IncomeQuarter from '@/views/components/charts-and-maps/charts/IncomeQuarter.vue'
import OutcomeQuarter from '@/views/components/charts-and-maps/charts/OutcomeQuarter.vue'
import TopEntity from '@/views/components/charts-and-maps/charts/TopEntity.vue'
import SummaryQuarter from '@/views/components/charts-and-maps/charts/SummaryQuarter.vue'
import StatisticPercentage from '@/views/components/charts-and-maps/charts/StatisticPercentage.vue'
import SummaryData from '@/views/components/charts-and-maps/charts/SummaryData.vue'
import TotalIncomeOutcomeQuarter from '@/views/components/charts-and-maps/charts/TotalIncomeOutcomeQuarter.vue'
import IncentiveTaxMonthly from '@/views/components/charts-and-maps/charts/IncentiveTaxMonthly.vue'
import TopRankIncomeEntity from '@/views/components/charts-and-maps/charts/TopRankIncomeEntity.vue'
import TopRankExpenseEntity from '@/views/components/charts-and-maps/charts/TopRankExpenseEntity.vue'

import OfficerStatistic from './_OfficerStatistic.vue'
import OfficerStatusChart from './_OfficerStatusChart.vue'
import EntityIncomeChart from './_EntityIncomeChart.vue'
import MonthlyIncomeChart from './_MonthlyIncomeChart.vue'
import IncomeExpenseChart from './_IncomeExpenseChart.vue'

import EchartsBarChart from './EchartsBarChart.vue'
import EchartsLineChart from './EchartsLineChart.vue'
import EchartsPieChart from './EchartsPieChart.vue'
import EchartsPolarChart from './EchartsPolarChart.vue'
import EchartsScatterChart from './EchartsScatterChart.vue'
import EchartsRadarChart from './EchartsRadarChart.vue'
import CurrencyDropDown from "../layouts/components/navbar/components/CurrencyDropDown.vue"
import QuarterDropDown from "../layouts/components/navbar/components/QuarterDropDown.vue"
import flatPickr from 'vue-flatpickr-component'
import 'flatpickr/dist/flatpickr.css'
import vSelect from 'vue-select'

import router from '@/router'
import axios from "@/axios.js"
export default{
    data() {
        const now = new Date();
        const year = new Date().getFullYear();
        const start = `${year}-01-01`;
        const end   = `${year}-12-31`;
        return {
            chartOptionOfficers: {
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
                colors: ['#7961F9', '#FF9F43', '#EA5455'],
                markers: {
                    size: 0,
                    hover: {
                        size: 5
                    }
                },
                xaxis: {
                    labels: {
                        style: {
                            cssClass: 'text-black fill-current',
                        }
                    },
                    axisTicks: {
                        show: true,
                    },
                    categories: ['2024', '2025', '2026'],
                    axisBorder: {
                        show: true,
                    },
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
                },
                grid: {
                    show: false,
                    padding: {
                        left: 0,
                        right: 0
                    },
                    // row: {
                    //     colors: ['#f3f3f3', 'transparent'], // takes an array which will be repeated on columns
                    //     opacity: 0.5
                    // },
                },
            },
            serieOfficers: [
                {
                    name: 'ចំនួន',
                    type: 'column',
                    data: [340, 342, 353]
                }
            ],

            dateRange: [start, end],
            config: {
                mode: 'range',
                dateFormat: 'Y-m-d',
                allowInput: true,
                altInput: false
            },
            startDate:`${year}-01-01`,
            endDate:`${year}-12-31`,
            cardCurrency: {
                income: 'KHR',
                expense: 'KHR',
                balance: 'KHR',
                incentive: 'KHR',
                quarter: 'KHR',
                currency:'KHR'
            },
            quarterFilter: {
                year: now.getFullYear(),
                quarter: `Q${Math.floor(now.getMonth() / 3) + 1}`
            },
            data: null,
            totalMembers:0,
            totalMales:0,
            totalFemales:0,
            totalExpenseKHR:0,
            totalExpenseUSD:0,
            totalIncomeKHR:0,
            totalIncomeUSD:0,
            totalRemainingBalanceKHR:0,
            totalRemainingBalanceUSD:0,
            quarterly_total_income_khr:0,
            quarterly_total_income_usd:0,
            quarterly_total_expense_khr:0,
            quarterly_total_expense_usd:0,
            officerPaymentHistory:{},
            officerIncentive:{},
            IncentivePie:{},
            
            totalMemberLatest5Years:{},
            subscribersGained: {},
            revenueGenerated: {},
            quarterlySales: {},
            ordersRecevied: {},
            
            totalOfficerLatest5YearsSeries:{},
            officers: [],
            years: [],
            revenueComparisonLine: {
                analyticsData: {
                thisMonth: 86589,
                lastMonth: 73683
                },
                series: [{
                        name: "ចំណូល",
                        data: [45000, 47000, 44800, 47500, 45500, 48000, 46500, 48600, 45000, 47000, 44800, 47500]
                    },
                    {
                        name: "ចំណាយ",
                        data: [46000, 48000, 45500, 46600, 44500, 46500, 45000, 47000, 45000, 47000, 44800, 47500]
                    }
                ],
            },
            goalOverview: {},

            browserStatistics: [],
            clientRetentionBar: {},

            salesLine: {
                series: [{
                    name: "Sales",
                    data: [140, 180, 150, 205, 160, 295, 125, 255, 205, 305, 240, 295]
                }],
            },
            funding: {},

            apexChatData: apexChatData,

            sessionsData: {},
            chatLog: [],
            chatMsgInput: '',
            customersData: {},
            productsOrder: {},
            analyticsData: analyticsData,
            settings: { // perfectscrollbar settings
                maxScrollbarLength: 60,
                wheelSpeed: .60,
            },
            totalData: 100000,
            // serieOfficers: [
                // {
                //     name: 'Number of Officer',
                //     type: 'column',
                //     data: [340, 342, 353]
                // }
            // ],
            series: [{
                name: [''],
                data: [0, 0, 0, 0]
            }],
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
            // chartOptionOfficers: {},
            chartOptionIncomes: {
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
            __chartOptionExpenses: {
                yaxis: {
                    show: false,
                },
                chart: {
                    height: 350,
                    type: 'line',
                    zoom: {
                        enabled: true
                    },
                    dropShadow: {
                        enabled: false,
                        top: 5,
                        left: 0,
                        blur: 4,
                        opacity: 0.10,
                    },
                    toolbar: {
                        show: false,
                    },
                    sparkline: {
                        enabled: false
                    }
                },
                dataLabels: {
                enabled: false
                },
                stroke: {
                    width: 3,
                    curve: 'smooth'
                },
                title: {
                text: '',
                align: 'left'
                },
                colors: ['#EA5455'],
                grid: {
                    show: false,
                    padding: {
                        left: 0,
                        right: 0
                    },
                    // row: {
                    //     colors: ['#f3f3f3', 'transparent'], // takes an array which will be repeated on columns
                    //     opacity: 0.5
                    // },
                },
                xaxis: {
                    categories: ['Q1', 'Q2', 'Q3', 'Q4'],
                },
                markers: {
                    size: 0,
                    hover: {
                        size: 5
                    }
                },
                tooltip: {
                    x: { show: false },
                    y: { show: false }
                },
            },
            __chartOptionRemains: {
                yaxis: {
                    show: false,
                },
                chart: {
                    height: 350,
                    type: 'line',
                    zoom: {
                        enabled: true
                    },
                    dropShadow: {
                        enabled: false,
                        top: 5,
                        left: 0,
                        blur: 4,
                        opacity: 0.10,
                    },
                    toolbar: {
                        show: false,
                    },
                    sparkline: {
                        enabled: false
                    }
                },
                dataLabels: {
                enabled: false
                },
                stroke: {
                    width: 3,
                    curve: 'smooth'
                },
                title: {
                text: '',
                align: 'left'
                },
                colors: ['#FF9F43'],
                grid: {
                    show: false,
                    padding: {
                        left: 0,
                        right: 0
                    },
                    // row: {
                    //     colors: ['#f3f3f3', 'transparent'], // takes an array which will be repeated on columns
                    //     opacity: 0.5
                    // },
                },
                xaxis: {
                    categories: ['Q1', 'Q2', 'Q3', 'Q4'],
                },
                markers: {
                    size: 0,
                    hover: {
                        size: 5
                    }
                },
                tooltip: {
                    x: { show: false },
                    y: { show: false }
                },
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
            summaryInfo: {
                totalIncentive: ''
            },

            // Income Outcome Quarter
            serieInOutQuarters: [
                {
                    name: "ចំណូល",
                    data: [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0]
                },
                {
                    name: "ចំណាយ",
                    data: [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0]
                }
            ],
            summaryInOutQuarter: {},
            // #End 
            issueDateOfficer: '',
            issueDateIncome: '',
            issueDateOutcome: '',
            issueDateRemain: '',
            filterOption: 'This Year',
            currentYear: new Date().getFullYear()
        }
    },
    components: {
        VueApexCharts,
        apexchart: VueApexCharts,
        StatisticsCardLine,
        VuePerfectScrollbar,
        ChangeTimeDurationDropdown,
        IncomeSector,
        ExpenseSector,
        OutcomeQuarter,
        IncomeQuarter,
        TopEntity,
        SummaryQuarter,
        StatisticPercentage,
        SummaryData,
        TotalIncomeOutcomeQuarter,
        IncentiveTaxMonthly,
        TopRankIncomeEntity,
        TopRankExpenseEntity,
        StatisticsCardLine,
        CurrencyDropDown,
        QuarterDropDown,
        EchartsBarChart,
        EchartsLineChart,
        EchartsPieChart,
        EchartsPolarChart,
        EchartsScatterChart,
        EchartsRadarChart,
        OfficerStatusChart,
        EntityIncomeChart,
        MonthlyIncomeChart,
        IncomeExpenseChart,
        OfficerStatistic,
        vSelect,
        flatPickr
    },  
    watch:{
        dateRange(val) {
            if (!val) return

            let start = ''
            let end = ''

            if (Array.isArray(val)) {
                ;[start, end] = val
            } else {
                ;[start, end] = val.split(' to ')
            }

            if (!start || !end) return

            this.getDashboardSummary(
                this.quarterFilter.year,
                this.quarterFilter.quarter,
                start,
                end
            );
        }
    },
    mounted() {
        if(!localStorage.getItem("accessToken")){
            router.push(router.currentRoute.query.to || '/pages/login')
        }
        // this.$refs.chatLogPS.$el.scrollTop = this.$refs.chatLog.scrollHeight;
        this.wasSidebarOpen = this.$store.state.reduceButton
        this.$store.commit('TOGGLE_REDUCE_BUTTON', true)
    },
    beforeDestroy() {
        if (!this.wasSidebarOpen) this.$store.commit('TOGGLE_REDUCE_BUTTON', false)
    },
    methods: {
        onQuarterChange(val) {
            const quarterNumber = this.quarterFilter.quarter
                ? Number(this.quarterFilter.quarter.slice(1))
                : null;
            this.getDashboardSummary(this.quarterFilter.year,quarterNumber,this.startDate,this.endDate);
        },
        getSummary(option){
            // this.resetForm();
            //this.$vs.loading();
            return new Promise((resolve, reject) => {
                axios.get("api/v1/report/summary?filter=" + option)
                    .then((response) => {
                    let _data = response["data"]["data"];
                    this.summaryInfo = response["data"]["data"];
                    this.issueDateOfficer = 'គិតត្រឹមថ្ងៃទី ' + _data.currentDate + ' ខែ ' + _data.currentMonth + ' ឆ្នាំ ' + _data.currentYear;
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

                    this.$vs.loading.close();
                    
                }).catch((error) => { 
                    reject(error) 
                })
            })
            
        },

        getIncomeOutcomeByQuarter(year){
          return new Promise((resolve, reject) => {
              axios.get("/api/v1/report/income_outcome_by_quarter?year=" + year)
                  .then((response) => {
                  this.summaryInOutQuarter = response["data"]["data"]
                  let dataIncome = response["data"]["data"]["dataIncome"]
                  let dataOutcome = response["data"]["data"]["dataOutcome"]
                  this.serieInOutQuarters = [
                    {
                        name: "ចំណូល",
                        data: dataIncome
                    },
                    {
                        name: "ចំណាយ",
                        data: dataOutcome
                    }
                  ]
              }).catch((error) => { reject(error) })
          })
        },


        getDashboardSummary(year, quarter,startDate, endDate) {
        //this.$vs.loading();
        axios.post("/api/v1/mobile/dashboard-summary", {
                year: year,
                quarter: quarter !== undefined && quarter !== null ? quarter : null,
                start_date: startDate,
                end_date: endDate
            }).then((response) => {

                if (response.data.errorCode === "000") {

                    const members = response.data.data.totalMember;
                    const officersSeries = {
                        series: [{
                            name: ['Yearly'],
                            // data: [
                            //     response.data.data.totalMemberLatest5Years[0]?.["total_officers"] ?? 0,
                            //     response.data.data.totalMemberLatest5Years[1]?.["total_officers"] ?? 0,
                            //     response.data.data.totalMemberLatest5Years[2]?.["total_officers"] ?? 0,
                            //     response.data.data.totalMemberLatest5Years[3]?.["total_officers"] ?? 0,
                            //     response.data.data.totalMemberLatest5Years[4]?.["total_officers"] ?? 0,
                            // ]
                            data: [
                                response.data.data.totalMemberLatest5Years && response.data.data.totalMemberLatest5Years[0]
                                    ? response.data.data.totalMemberLatest5Years[0]["total_officers"]
                                    : 0,
                                response.data.data.totalMemberLatest5Years && response.data.data.totalMemberLatest5Years[1]
                                    ? response.data.data.totalMemberLatest5Years[1]["total_officers"]
                                    : 0,
                                response.data.data.totalMemberLatest5Years && response.data.data.totalMemberLatest5Years[2]
                                    ? response.data.data.totalMemberLatest5Years[2]["total_officers"]
                                    : 0,
                                response.data.data.totalMemberLatest5Years && response.data.data.totalMemberLatest5Years[3]
                                    ? response.data.data.totalMemberLatest5Years[3]["total_officers"]
                                    : 0,
                                response.data.data.totalMemberLatest5Years && response.data.data.totalMemberLatest5Years[4]
                                    ? response.data.data.totalMemberLatest5Years[4]["total_officers"]
                                    : 0
                            ]
                        }]
                    };
                    this.totalOfficerLatest5YearsSeries = officersSeries;

                    const expenses= response.data.data.totalExpense;
                    const incomes = response.data.data.totalIncome;
                    const remains = response.data.data.totalRemainingBalance;
                    
                    this.data = response.data.data;
                    this.totalMembers = members.totalMembers;
                    this.totalMales = members.totalMales;
                    this.totalFemales = members.totalFemales;

                    this.totalExpenseKHR = expenses.total_in_khr_kh;
                    this.totalExpenseUSD = expenses.total_in_usd;

                    this.totalIncomeKHR = incomes.total_in_khr_kh;
                    this.totalIncomeUSD = incomes.total_in_usd;

                    this.totalRemainingBalanceKHR = remains.remaining_balance_khr_kh;
                    this.totalRemainingBalanceUSD = remains.remaining_balance_usd;

            
                    var summary = response.data.data.quarterlySummary || [];
                    this.quarterly_total_income_khr =
                    summary[0] && summary[0]["total_income_khr_kh"] != null
                        ? summary[0]["total_income_khr_kh"]
                        : 0;

                    this.quarterly_total_income_usd =
                    summary[0] && summary[0]["total_income_usd"] != null
                        ? summary[0]["total_income_usd"]
                        : 0;

                    this.quarterly_total_expense_khr =
                    summary[0] && summary[0]["total_expense_khr_kh"] != null
                        ? summary[0]["total_expense_khr_kh"]
                        : 0;

                    this.quarterly_total_expense_usd =
                    summary[0] && summary[0]["total_expense_usd"] != null
                        ? summary[0]["total_expense_usd"]
                        : 0;


                    const paymentHistory = {
                        paid:response.data.data.officerPaymentInfo.officers_paid,
                        unpaid:response.data.data.officerPaymentInfo.officers_unpaid,
                        percent_paid:[response.data.data.officerPaymentInfo.percent_paid],
                    } 
                    this.officerPaymentHistory = paymentHistory;


                    // Incentive. ======================================
                    const incentives = response.data.data.totalIncentive;
                    const series_khr = incentives.map(i => {
                        return Number((i.total_amount_khr || '0').toString().replace(/,/g, ''));
                    });
                    const series_usd = incentives.map(i => {
                        return Number((i.total_amount_usd || '0').toString().replace(/,/g, ''));
                    });
                    const IncentiveGraphInfo = {
                        totalIncentive : incentives,
                        series_khr: series_khr,
                        series_usd: series_usd
                    };
                    this.officerIncentive = IncentiveGraphInfo;

                     this.IncentivePie=  {
                        chartOptions: {
                            labels: IncentiveGraphInfo.totalIncentive.map(i => i.payment_type_name),
                            dataLabels: {
                                enabled: false
                            },
                            legend: { show: false },
                            chart: {
                                type: 'pie',
                                offsetY: 0,
                                dropShadow: {
                                    enabled: false,
                                    blur: 5,
                                    left: 1,
                                    top: 1,
                                    opacity: 0.2
                                },
                                toolbar: {
                                    show: false
                                }
                            },
                            stroke: {
                                width: 5
                            },
                            colors: ['#7961F9', '#FF9F43', '#EA5455'],
                            fill: {
                                type: 'gradient',
                                gradient: {
                                    gradientToColors: ['#9c8cfc', '#FFC085', '#f29292']
                                }
                            }
                        }
                    };

                } else {
                    console.error("API Error:", response.data.errorMessage);
                }

                })
                .catch((error) => {
                console.error("Request Error:", error);
            });
        },

        
        onSelectFilter(val, label){
            this.serieOfficers = [];
            this.officers = [];
            this.filterOption = label;
            this.currentYear = val;
            this.getSummary(val);
            this.getIncomeOutcomeByQuarter(val);
            // this.$emit('send', 'bye');
            // this.$emit('clicked-show-detail', product);
        },
        resetForm(){
            this.incomeCompareSeries = [];
            this.expenseCompareSeries = [];
            this.remainCompareSeries = [];
            this.serieOfficers = [];
        }
    },
    created() {
        //this.getSummary(this.currentYear);
        //this.getIncomeOutcomeByQuarter(this.currentYear)
        this.$vs.loading();
        setTimeout(() => {
            this.$vs.loading.close()
        }, 10000);
        const quarterNumber = this.quarterFilter.quarter
            ? Number(this.quarterFilter.quarter.slice(1))
            : null;
        this.getDashboardSummary(this.quarterFilter.year,quarterNumber, this.startDate,this.endDate);

      // Subscribers gained - Statistics
    //   this.$http.get("/api/card/card-statistics/subscribers")
    //     .then((response) => { this.subscribersGained = response.data })
    //     .catch((error) => { console.log(error) })

      // Revenue Generated
      this.$http.get("/api/card/card-statistics/revenue")
        .then((response) => { this.revenueGenerated = response.data })
        .catch((error) => { console.log(error) })

      // Sales
      this.$http.get("/api/card/card-statistics/sales")
        .then((response) => { this.quarterlySales = response.data })
        .catch((error) => { console.log(error) })

      // Orders - Statistics
      this.$http.get("/api/card/card-statistics/orders")
        .then((response) => { this.ordersRecevied = response.data })
        .catch((error) => { console.log(error) })

      // Revenue Comparison
    //   this.$http.get("/api/card/card-analytics/revenue-comparison")
    //     .then((response) => { this.revenueComparisonLine = response.data })
    //     .catch((error) => { console.log(error) })

    // Sales line
    //   this.$http.get("/api/card/card-analytics/sales/line")
    //     .then((response) => { this.salesLine = response.data })
    //     .catch((error) => { console.log(error) })

    // Products Order
    this.$http.get("/api/card/card-analytics/products-orders")
    .then((response) => { this.productsOrder = response.data })
    .catch((error) => { console.log(error) })

      // Goal Overview
    //   this.$http.get("/api/card/card-analytics/goal-overview")
    //     .then((response) => { this.goalOverview = response.data })
    //     .catch((error) => { console.log(error) })

      // Browser Analytics
    //   this.$http.get("/api/card/card-analytics/browser-analytics")
    //     .then((response) => { this.browserStatistics = response.data })
    //     .catch((error) => { console.log(error) })

      // Client Retention
    //   this.$http.get("/api/card/card-analytics/client-retention")
    //     .then((response) => { this.clientRetentionBar = response.data })
    //     .catch((error) => { console.log(error) })

      // Sessions By Device
    //   this.$http.get("/api/card/card-analytics/session-by-device")
    //     .then((response) => { this.sessionsData = response.data })
    //     .catch((error) => { console.log(error) })

      // Chat Log
    //   this.$http.get("/api/chat/demo-1/log")
    //     .then((response) => { this.chatLog = response.data })
    //     .catch((error) => { console.log(error) })

    //   Customers
      this.$http.get("/api/card/card-analytics/customers")
        .then((response) => { this.customersData = response.data })
        .catch((error) => { console.log(error) })
    
    },
    
}
</script>

<style lang="scss">
    .main-dashboard{
        font-family: 'Khmer MEF2';
        font-size: 13px;
    }
    h4{
        font-size: 16px;
    }
    .chat-card-log {
        height: 400px;

        .chat-sent-msg {
            background-color: #f2f4f7 !important;
        }
    }
    .vx-card {
        min-height: 200px ; // 306px
    }
    .B {
        float: right;
        margin-top: -15%;
    }
    .quarter-block{
        float: right;
    }
    .switch-currency-block{
        padding-bottom: 20px;
        display: flex;
        justify-content: flex-end;
        width: 100%;
    }
    .switch-currency{
        padding-right: 5px;
    }
    .date-rang-width{
        min-width: 15%;
    }

</style>

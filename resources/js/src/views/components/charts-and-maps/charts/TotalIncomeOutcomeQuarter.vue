<template>
  <vx-card title="ចំណូល‍​~ចំណាយសរុបប្រចាំខែ(គិតជាលានរៀល)">
    <!-- <template slot="actions">
        <feather-icon icon="SettingsIcon" svgClasses="w-6 h-6 text-grey"></feather-icon>
    </template> -->
    <!-- SLOT = ACTIONS -->
    <!-- <template slot="actions">
        <change-time-duration-dropdown />
    </template> -->
    <div slot="no-body" class="p-6 pb-0">
        <div class="flex" v-if="analyticsData">
            <div class="mr-6">
                <p class="mb-1 font-semibold">ចំណូល</p>
                <p class="text-3xl text-success"><sup class="text-base mr-1"></sup>{{ summaryInfo.totalIncome }}</p>
            </div>
            <div>
                <p class="mb-1 font-semibold">ចំណាយ</p>
                <p class="text-3xl"><sup class="text-base mr-1"></sup>{{ summaryInfo.totalExpense }}</p>
            </div>
        </div>
        <!-- <vue-apex-charts
          type=line
          height=266
          :options="analyticsData.revenueComparisonLine.chartOptions"
          :series="revenueComparisonLine.series" /> -->
        <apexchart type="line" height="266" :options="chartOptions" :series="series"></apexchart>
    </div>
</vx-card>
</template>

<script>
import VuePerfectScrollbar from 'vue-perfect-scrollbar'
import VueApexCharts from 'vue-apexcharts'
import StatisticsCardLine from '@/components/statistics-cards/StatisticsCardLine.vue'
import ChangeTimeDurationDropdown from '@/components/ChangeTimeDurationDropdown.vue'
import router from '@/router'
import axios from "@/axios.js"
export default{
    props:{
        series: {
          // type: Array,
          required: true
        },
        summaryInfo:{
            required: true
        }
    },
    data() {
        return {
            analyticsData: {
                thisMonth: 0,
                lastMonth: 0
            },
            // series: [
            //     {
            //         name: "ចំណូល",
            //         data: [680978614, 365880372, 770834829, 292221639, 344463040, 0, 0, 0, 0, 0, 0, 0]
            //     },
            //     {
            //         name: "ចំណាយ",
            //         data: [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0]
            //     }
            // ],
            chartOptions: {
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
                colors: ['#28C76F', '#EA5455', '#28C76F', '#EA5455'],
                fill: {
                    type: 'gradient',
                    gradient: {
                        shade: 'dark',
                        inverseColors: false,
                        // gradientToColors: ['#7367F0', '#b8c2cc'],
                        shadeIntensity: 1,
                        type: 'horizontal',
                        opacityFrom: 1,
                        opacityTo: 1,
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
                    categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                    axisBorder: {
                        show: false,
                    },
                },
                yaxis: {
                    tickAmount: 5,
                    labels: {
                        style: {
                            cssClass: 'text-grey fill-current',
                        },
                        formatter: function(val) {
                            return val > 999 ? (val / 1000).toFixed(1) : val;
                        }
                    }
                },
                tooltip: {
                    x: { show: false }
                }
            },
            // summaryInfo: "" 
      }
    },
    
    created(){
    //   this.getIncomeOutcomeByQuarter();
    },
    methods:{
        getIncomeOutcomeByQuarter(){
          return new Promise((resolve, reject) => {
              axios.get("/api/v1/report/income_outcome_by_quarter?year=" + this.year)
                  .then((response) => {
                  this.summaryInfo = response["data"]["data"]
                  let dataIncome = response["data"]["data"]["dataIncome"]
                  let dataOutcome = response["data"]["data"]["dataOutcome"]
                  this.series = [
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
    },
    components: {
      apexchart: VueApexCharts,
      ChangeTimeDurationDropdown
    },
}
</script>

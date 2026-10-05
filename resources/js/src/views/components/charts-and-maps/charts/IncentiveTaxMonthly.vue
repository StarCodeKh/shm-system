<template>
  <vx-card title="% ប្រាក់បើកផ្តល់ និងប្រាក់ពន្ធប្រចាំខែ">
    <template slot="actions">
        <change-time-duration-dropdown />
    </template>
    <div class="flex">
        <span class="flex items-center"><div style="background-color: #005AE1 !important" class="h-3 w-3 rounded-full mr-1 bg-primary"></div><span>ប្រាក់បើកផ្តល់</span></span>
        <span class="flex items-center ml-4"><div class="h-3 w-3 rounded-full mr-1 bg-danger"></div><span>ប្រាក់ពន្ធ</span> </span>
    </div>
    <apexchart type=bar height=277 :options="clientRetentionBar.chartOptions" :series="clientRetentionBar.series" />
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
    data() {
        return {
          clientRetentionBar: {
              series: [
                  {
                  name: 'ពន្ធ',
                  data: [15, 20, 20, 20, 20, 20, 20, 20, 20, 20, 20, 20]
                    },
                    {
                  name: 'បើកផ្តល់',
                  data: [85, 80, 80, 80, 80, 80, 80, 80, 80, 80, 80, 80]
                    }
                ],
              chartOptions: {
                  grid: {
                      borderColor: '#ebebeb',
                      padding: {
                          left: 0,
                          right: 0
                      }
                  },
                  legend: {
                      show: false,
                  },
                  dataLabels: {
                      enabled: false
                  },
                  chart: {
                      stacked: true,
                      type: 'bar',
                      toolbar: { show: false },
                  },
                  colors: ['#EA5455', '#005AE1'],
                  plotOptions: {
                      bar: {
                          columnWidth: '10%'
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
                          }
                      }
                  },
                  tooltip: {
                      x: { show: false }
                  },
              }
          },
          summaryInfo: ""
        }
    },
    created() {
      this.getIncomeByEntity()
    },
    methods: {
      getIncomeByEntity(){
        return new Promise((resolve, reject) => {
            axios.get("/api/v1/report/income_by_entity")
                .then((response) => {
              this.browserAnalytics = response.data
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

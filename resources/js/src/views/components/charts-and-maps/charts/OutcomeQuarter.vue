<template>
  <vx-card title="ចំណាយសរុបប្រចាំត្រីមាស">
    <template slot="actions">
        <change-time-duration-dropdown />
    </template>
    <div id="chart">
        <apexchart type="bar" height="380" :options="chartOptions" :series="series"></apexchart>
    </div>
  </vx-card>
</template>

<script>
import VuePerfectScrollbar from 'vue-perfect-scrollbar'
import VueApexCharts from 'vue-apexcharts'
import StatisticsCardLine from '@/components/statistics-cards/StatisticsCardLine.vue'
import ChangeTimeDurationDropdown from '@/components/ChangeTimeDurationDropdown.vue'
import router from '@/router'

export default{
    data() {
        return {
          series: [
            {
              name: 'Quarter I',
              data: [44333333, 44333333, 44333333]
            }, {
              name: 'Quarter II',
              data: [34656537, 44683679, 24636754]
            }, {
              name: 'Quarter III',
              data: [45684368, 46847479, 45374264]
            }
            , {
              name: 'Quarter IV',
              data: [47368646, 47374843, 47436824]
            }
          ],
          chartOptions: {
            chart: {
              type: 'bar',
              height: 350,
              stacked: true,
            },
            plotOptions: {
              bar: {
                horizontal: true,
              },
            },
            stroke: {
              width: 1,
              colors: ['#fff']
            },
            title: {
              text: 'Outcome By Quarter'
            },
            xaxis: {
              categories: [2018, 2019, 2020],
              labels: {
                formatter: function (val) {
                  return val + " ៛"
                }
              }
            },
            yaxis: {
              title: {
                text: undefined
              },
            },
            tooltip: {
              y: {
                formatter: function (val) {
                  return val + " ៛"
                }
              }
            },
            fill: {
              opacity: 1
            },
            legend: {
              position: 'top',
              horizontalAlign: 'left',
              offsetX: 40
            }
          }, 
      }
    },
    components: {
      apexchart: VueApexCharts,
      ChangeTimeDurationDropdown
    },
}
</script>

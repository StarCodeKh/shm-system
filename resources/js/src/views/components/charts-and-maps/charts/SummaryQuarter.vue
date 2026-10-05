<template>
  <vx-card title="% ប្រាក់ចំណូល ប្រាក់បើកផ្តល់ និងប្រាក់ពន្ធប្រចាំខែ">
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
        
          series: [{
            name: 'Income',
            data: [178456783, 937649300, 473628300, 203947400, 304938200, 302394600, 304938200]
          }, {
            name: 'Incentive',
            data: [473628300, 937649300, 304938200, 203947400, 302394600, 10384600, 502837200]
          }, {
            name: 'Tax',
            data: [304938200, 304938200, 502837200, 937649300, 502837200, 473628300, 304938200]
          }],
          chartOptions: {
            chart: {
              type: 'bar',
              height: 350
            },
            plotOptions: {
              bar: {
                horizontal: false,
                columnWidth: '55%',
                endingShape: 'rounded'
              },
            },
            dataLabels: {
              enabled: false
            },
            stroke: {
              show: true,
              width: 2,
              colors: ['transparent']
            },
            xaxis: {
              categories: ['Jan', 'Feb', 'Mar','Apr' , 'May', 'Jun', 'Jul'],
            },
            yaxis: {
              title: {
                text: 'Reil'
              }
            },
            fill: {
              opacity: 1
            },
            tooltip: {
              y: {
                formatter: function (val) {
                  return val + "៛"
                }
              }
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

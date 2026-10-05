<template>
  <vx-card title="ចំណាត់ថ្នាក់អង្គភាពទាំង១០">
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
          data: [400, 430, 448, 470, 540, 580, 690, 1100, 1200, 1380]
        }],
        chartOptions: {
          chart: {
            type: 'bar',
            height: 350
          },
          plotOptions: {
            bar: {
              horizontal: true,
            }
          },
          dataLabels: {
            enabled: false
          },
          xaxis: {
            categories: ['MFWG', 'GS-MEF', 'GDT', 'GDEPFP', 'GDNT', 'GDPP', 'GDCE',
              'MEF', 'MoE', 'MoDG'
            ],
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

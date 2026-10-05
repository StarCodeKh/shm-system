<template>
  <vx-card title="ស្ថិតិការចំណាយតាមវិស័យធៀបជា%">
    <template slot="actions">
        <change-time-duration-dropdown />
    </template>
    <div id="chart">
      <apexchart type="donut" :options="chartOptions" :series="series"></apexchart>
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
      series: [44, 55, 41, 17, 15],
      chartOptions: {
        chart: {
          type: 'donut',
        },
        responsive: [{
          breakpoint: 480,
          options: {
            chart: {
              width: 200
            },
            legend: {
              position: 'bottom'
            }
          }
        }]
      },
    }
  },
  components: {
    apexchart: VueApexCharts,
    ChangeTimeDurationDropdown
  },
}
</script>
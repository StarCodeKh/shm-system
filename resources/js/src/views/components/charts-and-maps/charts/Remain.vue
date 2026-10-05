<template>
  <vx-card title="ចំណូលតាមក្រសួង~ស្ថាប័ន(គិតជាលានរៀល)">
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
            data: [222, 430, 448, 470, 540, 580, 690, 1100, 1200, 1380]
          }],
          chartOptions: {
            chart: {
              type: 'bar',
              height: 380
            },
            plotOptions: {
              bar: {
                barHeight: '100%',
                distributed: true,
                horizontal: true,
                dataLabels: {
                  position: 'bottom'
                },
              }
            },
            colors: ['#33b2df', '#546E7A', '#d4526e', '#13d8aa', '#A5978B', '#2b908f', '#f9a3a4', '#90ee7e',
              '#f48024', '#69d2e7'
            ],
            dataLabels: {
              enabled: true,
              textAnchor: 'start',
              style: {
                colors: ['#fff']
              },
              formatter: function (val, opt) {
                return opt.w.globals.labels[opt.dataPointIndex] + ":  " + val
              },
              offsetX: 0,
              dropShadow: {
                enabled: true
              }
            },
            stroke: {
              width: 1,
              colors: ['#fff']
            },
            xaxis: {
              categories: ['MEF', 'Canada', 'United Kingdom', 'Netherlands', 'Italy', 'France', 'Japan',
                'United States', 'China', 'India'
              ],
            },
            yaxis: {
              labels: {
                show: false
              },
              title: {
                text: undefined
              },
            },
            title: {
              text: 'Income By Entity',
              align: 'center',
              floating: true
            },
            subtitle: {
                text: '',
                align: 'center',
            },
            tooltip: {
              theme: 'dark',
              x: {
                show: false
              },
              y: {
                title: {
                  formatter: function () {
                    return '៛'
                  }
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

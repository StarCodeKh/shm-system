<template>
    <vx-card class="overflow-hidden">
        <div class="main-dashboard" slot="no-body">
            <div class="p-6" :class="{
              'flex justify-between flex-row-reverse items-center': iconRight,
              'text-center': !iconRight && hideChart,
              'pb-0': !hideChart
            }">
                <div class="truncate">
                    <h3 class="mb-1 font-bold"><feather-icon :icon="icon" class="p-3 inline-flex rounded-full" :class="[`text-${color}`, {'mb-4': !iconRight}]" :style="{background: `rgba(var(--vs-${color}),.15)`}"></feather-icon> {{statistic}}</h3>
                    <span>{{statisticTitle}}</span>
                    <div v-if="totalDollar != ''" class="mb-1"><b>{{totalDollar}}</b> លានដុល្លា</div>
                    <div v-else class="mb-1"><b>{{totalDollar}}<br/></b></div>
                    <!-- <span>សន្និធិចាយបានរហូតដល់ {{ statisticTitle }} ខែ</span> -->
                </div>
            </div>

            <div class="line-area-chart" v-if="!hideChart">
                <vue-apex-charts ref="apexChart" :type="type" height=200 width='100%' :options="chartOptions" :series="chartData" />
            </div>
            <div style="padding: 1px 5px; color: rgb(13, 114, 162)">
              <small>{{resultTitle}}</small>
            </div>
            <!-- <div id="chart">
                <apexchart type="line" height="350" :options="chartOptionBarcharts" :series="serieBarcharts"></apexchart>
            </div> -->
        </div>
    </vx-card>
</template>

<script>
import VueApexCharts from 'vue-apexcharts'
import ApexCharts from 'apexcharts'
import chartConfigs from "./chartConfigs.js"
import _color from '@assets/utils/color.js'

export default{
    props: {
        icon: {
            type: String,
            required: true
        },
        flag: {
          type: Number,
        },
        statistic: {
            // type: [String],
            // type: [Number, String],
            required: true,
        },
        totalDollar: {
            // type: [Number, String],
            // type: [String],
            required: true,
        },
        statisticTitle: {
            type: String,
        },
        resultTitle: {
            type: String,
        },
        chartOptions: {
          // type: Array,
          required: true
        },
        chartData: {
            // type: String,
            // type: Array,
            required: true
        },
        color: {
            type: String,
            default: 'primary',
        },
        colorTo: {
          type: String
        },
        chartType: {
            type: String,
            default: 'line-chart',
        },
        type: {
            type: String,
            default: 'line'
        },
        // iconRight: {
        //     type: Boolean,
        //     default: false
        // },
        hideChart: {
          type: Boolean,
          default: false,
        }
    },
    data() {
      return {
        series: [{
          name: ['Monthly'],
          data: [28, 40, 36]
        }],
        analyticsData: {
          subscribers: 8765678
        },

        serieBarcharts: [{
            name: 'Income',
            type: 'column',
            data: [1.4, 2, 2.5]
          }, 
        //   {
        //     name: 'Cashflow',
        //     type: 'column',
        //     data: [1.1, 3, 3.1]
        //   }, {
        //     name: 'Revenue',
        //     type: 'line',
        //     data: [20, 29, 37]
        //   }
          ],
          chartOptionBarcharts: {
            chart: {
              height: 350,
              type: 'line',
              stacked: false
            },
            dataLabels: {
              hideOverflowingLabels: true,
              enabled: true
            },
            stroke: {
              width: [1, 1, 4]
            },
            title: {
              text: 'XYZ - Stock Analysis (2009 - 2016)',
              align: 'left',
              offsetX: 110
            },
            xaxis: {
              categories: [2018, 2019, 2020],
            },
            yaxis: [
              {
                axisTicks: {
                  show: true,
                },
                axisBorder: {
                  show: true,
                  color: '#008FFB'
                },
                labels: {
                  style: {
                    colors: '#008FFB',
                  }
                },
                title: {
                  text: "Income (thousand crores)",
                  style: {
                    color: '#008FFB',
                  }
                },
                tooltip: {
                  enabled: true
                }
              },
            //   {
            //     seriesName: 'Income',
            //     opposite: true,
            //     axisTicks: {
            //       show: true,
            //     },
            //     axisBorder: {
            //       show: true,
            //       color: '#00E396'
            //     },
            //     labels: {
            //       style: {
            //         colors: '#00E396',
            //       }
            //     },
            //     title: {
            //       text: "Operating Cashflow (thousand crores)",
            //       style: {
            //         color: '#00E396',
            //       }
            //     },
            //   },
            //   {
            //     seriesName: 'Revenue',
            //     opposite: true,
            //     axisTicks: {
            //       show: true,
            //     },
            //     axisBorder: {
            //       show: true,
            //       color: '#FEB019'
            //     },
            //     labels: {
            //       style: {
            //         colors: '#FEB019',
            //       },
            //     },
            //     title: {
            //       text: "Revenue (thousand crores)",
            //       style: {
            //         color: '#FEB019',
            //       }
            //     }
            //   },
            ],
            tooltip: {
              fixed: {
                enabled: true,
                position: 'topLeft', // topRight, topLeft, bottomRight, bottomLeft
                offsetY: 30,
                offsetX: 60
              },
            },
            legend: {
              horizontalAlign: 'left',
              offsetX: 40
            }
        },

        // chartOptions: null,
        iconRight: "",
        data: ""
      }
    },
    watch: {
        themePrimaryColor() {
            this.$refs.apexChart.updateOptions({ theme: { monochrome: { color: this.getHex(this.color) } } });
        }
    },
    computed: {
        themePrimaryColor() {
            return this.$store.state.themePrimaryColor;
        }
    },
    methods: {
        getHex(color) {
            if(_color.isColor(color)) {
              let rgb  = window.getComputedStyle(document.documentElement).getPropertyValue(`--vs-${color}`);
              rgb = rgb.split(",");
              return "#" + ((1 << 24) + (Number(rgb[0]) << 16) + (Number(rgb[1]) << 8) + Number(rgb[2])).toString(16).slice(1);
            }
            return color
        },
        gradientToColor(color) {
          let gradientToColors = {
            "primary": "#f00",
            "success": "#55DD92",
            "warning": "#ffc085",
            "danger": "#F97794"
          }

          return gradientToColors[color] ? gradientToColors[color] : gradientToColors["primary"]
        }
    },
    components: {
      VueApexCharts,
      apexchart: VueApexCharts
    },
    created() {
        // if(this.type == 'area') {
        //     // assign chart options
        //     this.chartOptions = Object.assign({}, chartConfigs.areaChartOptions)

        //     this.chartOptions['theme'] = {
        //         yaxis: {
        //           show: true,
        //         },
        //         monochrome: {
        //           enabled: true,
        //           color: this.getHex(this.color),
        //           shadeTo: 'light',
        //           shadeIntensity: 0.65
        //         }
        //     }
        // }
        // else if(this.type == "line") {
        //   // Assign chart options
        //   this.chartOptions = JSON.parse(JSON.stringify(chartConfigs.lineChartOptions))

        //   this.chartOptions.fill.gradient.gradientToColors = [this.gradientToColor(this.colorTo || this.color)]
        //   this.chartOptions.colors = [this.getHex(this.color)]
        //   this.chartOptions.xaxis = {
        //     categories: ['Q1', 'Q2', 'Q3', 'Q4']
        //   }
        //   this.chartOptions.chart= {
        //       height: 350,
        //       type: 'line',
        //       zoom: {
        //           enabled: false
        //       },
        //       toolbar: {
        //           show: false,
        //       },
        //       // sparkline: {
        //       //     enabled: false
        //       // }
        //   }
        // }
    }
}
</script>
<style lang="scss">
    .main-dashboard{
        font-family: 'Khmer MEF2';
        font-size: 11.5px;
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
</style>

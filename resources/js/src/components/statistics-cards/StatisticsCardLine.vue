<template>
    <vx-card class="overflow-hidden">
        <template #no-body><div>
            <div class="p-6" :class="{
              'flex justify-between flex-row-reverse items-center': iconRight,
              'text-center': !iconRight && hideChart,
              'pb-0': !hideChart
            }">
                <div class="header-card">
                    <div class="A"><feather-icon :icon="icon" class="stat-card-line__icon p-3 inline-flex rounded-full" :class="['stat-card-line__icon--' + color, {'mb-4': !iconRight}]" :style="{ '--tone': toneVar }"></feather-icon></div>
                    <div class="B" v-if="showCurrency">
                        <CurrencyDropDown
                            :value="currency"
                            @change="$emit('currency-change', $event)"
                        />
                    </div>
                </div>
                
                <div class="truncate stat-card-line__body">
                    <h2 class="mb-1 font-bold">{{ displayStatistic }}</h2>
                    <span>{{ statisticTitle }}</span>
                </div>
            
            </div>

            <!-- <div class="line-area-chart" v-if="!hideChart">
                <vue-apex-charts ref="apexChart" :type="type" height=100 width='100%' :options="chartOptions" :series="chartData" />
            </div> -->
            
            <!-- <div id="chart">
                <apexchart type="line" height="350" :options="chartOptions" :series="series"></apexchart>
            </div> -->
        </div></template>
    </vx-card>
</template>

<script>
import VueApexCharts from 'vue3-apexcharts'
import ApexCharts from 'apexcharts'
import chartConfigs from "./chartConfigs.js"
import _color from '@assets/utils/color.js'
import CurrencyDropDown from "../../layouts/components/navbar/components/CurrencyDropDown.vue"

export default{
    props: {
        icon: {
            type: String,
            required: true
        },
        statistic: {
            type: [Number, String],
            required: true,
        },
        statisticTitle: {
            type: String,
        },
        chartData: {
            // type: String,
            type: Array,
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
        iconRight: {
            type: Boolean,
            default: false
        },
        hideChart: {
          type: Boolean,
          default: false,
        },
        currency: {
            type: String,
            default: 'KHR'
        },
        showCurrency: {
            type: Boolean,
            default: false
        },
    },
    data() {
      return {
        chartOptions: "null"
      }
    },
    watch: {
        themePrimaryColor() {
            this.$refs.apexChart.updateOptions({ theme: { monochrome: { color: this.getHex(this.color) } } });
        }
    },
    computed: {
        // "r, g, b" of the card's colour: a theme colour (primary, success, …) or the brand navy
        toneVar() {
            return this.color === 'navy' ? 'var(--brand-navy)' : `var(--vs-${this.color})`
        },
        // Counts arrive as plain numbers: group thousands and use Khmer digits in Khmer, like the
        // amounts the server already formats. Formatted text (e.g. "៦១៥,០០០") is shown as it is.
        displayStatistic() {
            const value = this.statistic
            if (value === null || value === undefined || value === '') return '0'
            if (typeof value === 'number' || /^-?\d+(\.\d+)?$/.test(String(value))) {
                const text = Number(value).toLocaleString('en-US')
                return this.$i18n.locale === 'en' ? text : text.replace(/\d/g, d => '០១២៣៤៥៦៧៨៩'[d])
            }
            return value
        },
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
            "primary": "#A9A2F6",
            "success": "#55DD92",
            "warning": "#ffc085",
            "danger": "#F97794"
          }

          return gradientToColors[color] ? gradientToColors[color] : gradientToColors["primary"]
        }
    },
    components: {
        VueApexCharts,
        apexchart: VueApexCharts,
        CurrencyDropDown,
    },
    created() {
        if(this.type == 'area') {
            // assign chart options
            this.chartOptions = Object.assign({}, chartConfigs.areaChartOptions)

            this.chartOptions['theme'] = {
                monochrome: {
                    enabled: true,
                    color: this.getHex(this.color),
                    shadeTo: 'light',
                    shadeIntensity: 0.65
                }
            }
        }
        else if(this.type == "line") {
          // Assign chart options
          this.chartOptions = JSON.parse(JSON.stringify(chartConfigs.lineChartOptions))

          this.chartOptions.fill.gradient.gradientToColors = [this.gradientToColor(this.colorTo || this.color)]
          this.chartOptions.colors = [this.getHex(this.color)]
        }
    }
}
</script>

<style lang="scss">
// Icon in a soft circle of its own colour (same recipe for every tone)
.stat-card-line__icon {
    color: rgb(var(--tone));
    background: rgba(var(--tone), .12);
}
// Navy is too dark on the dark theme: a lighter shade of it
.theme-dark .stat-card-line__icon--navy {
    color: #a9b0ff;
    background: rgba(169, 176, 255, .14);
}

    // Scoped to this card: a bare .truncate rule also pushed down every sidebar label
    .stat-card-line__body {
        padding-top: 5%;
    }
</style>

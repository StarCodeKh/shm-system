<template>
	<vx-card title="ចំណូលប្រចាំខែ" class="mb-base">
        សរុប {{ data.monthlyIncomes.length }}
        <template slot="actions">
            <feather-icon icon="SettingsIcon" svgClasses="w-6 h-6 text-grey"></feather-icon>
        </template>
        <div slot="no-body" class="p-6 pb-0">
            <vue-apex-charts type=line height=266 :options="chartOptions" :series="series" />
        </div>
    </vx-card>
</template>

<script>
import VueApexCharts from 'vue-apexcharts'
import analyticsData from './components/ui-elements/card/analyticsData.js'
export default {
    props: {
        data: {
            type: Object,
            default: () => {},
        }
    },
    data() {
        return {
            // Sales monthly
            // salesLine: {
            //     series: [{
            //         name: "Sales",
            //         data: [140, 180, 150, 205, 160, 295, 125, 255, 205, 305, 240, 295]
            //     }],
            //     chartOptions: {
            //         chart: {
            //             toolbar: { show: false },
            //             dropShadow: {
            //                 enabled: true,
            //                 top: 20,
            //                 left: 2,
            //                 blur: 6,
            //                 opacity: 0.20
            //             }
            //         },
            //         stroke: {
            //             curve: 'smooth',
            //             width: 4,
            //         },
            //         grid: {
            //             borderColor: '#ebebeb',
            //         },
            //         legend: {
            //             show: false,
            //         },
            //         colors: ['#df87f2'],
            //         fill: {
            //             type: 'gradient',
            //             gradient: {
            //                 shade: 'dark',
            //                 inverseColors: false,
            //                 gradientToColors: ['#7367F0'],
            //                 shadeIntensity: 1,
            //                 type: 'horizontal',
            //                 opacityFrom: 1,
            //                 opacityTo: 1,
            //                 stops: [0, 100, 100, 100]
            //             },
            //         },
            //         markers: {
            //             size: 0,
            //             hover: {
            //                 size: 5
            //             }
            //         },
            //         xaxis: {
            //             labels: {
            //                 style: {
            //                     cssClass: 'text-grey fill-current',
            //                 }
            //             },
            //             axisTicks: {
            //                 show: false,
            //             },
            //             categories: ['កមរា', 'កុម្ភៈ', 'មីនា', 'មេសា', 'ឧសភា', 'មិថុនា', 'កក្កដា', 'សីហា', 'កញ្ញា', 'តុលា', 'វិច្ឆិកា', 'ធ្នូ'],
            //             axisBorder: {
            //                 show: false,
            //             },
            //         },
            //         yaxis: {
            //             tickAmount: 5,
            //             labels: {
            //                 style: {
            //                     cssClass: 'text-grey fill-current',
            //                 },
            //                 formatter: function(val) {
            //                     return val > 999 ? (val / 1000).toFixed(1) + 'k' : val;
            //                 }
            //             }
            //         },
            //         tooltip: {
            //             x: { show: false }
            //         }
            //     }
            // },
        }
    },
    components: {
        VueApexCharts,
        analyticsData
    },
    computed: {
        chartOptions(){
            return {
                chart: {
                    toolbar: { show: false },
                    dropShadow: {
                        enabled: true,
                        top: 20,
                        left: 2,
                        blur: 6,
                        opacity: 0.20
                    }
                },
                stroke: {
                    curve: 'smooth',
                    width: 4,
                },
                grid: {
                    borderColor: '#ebebeb',
                },
                legend: {
                    show: false,
                },
                colors: ['#df87f2'],
                fill: {
                    type: 'gradient',
                    gradient: {
                        shade: 'dark',
                        inverseColors: false,
                        gradientToColors: ['#7367F0'],
                        shadeIntensity: 1,
                        type: 'horizontal',
                        opacityFrom: 1,
                        opacityTo: 1,
                        stops: [0, 100, 100, 100]
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
                    categories: ['មករា', 'កុម្ភៈ', 'មីនា', 'មេសា', 'ឧសភា', 'មិថុនា', 'កក្កដា', 'សីហា', 'កញ្ញា', 'តុលា', 'វិច្ឆិកា', 'ធ្នូ'],
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
                            return val > 999 ? (val / 1000).toFixed(1) + 'k' : val;
                        }
                    }
                },
                tooltip: {
                    x: { show: false }
                }
            }
        },
        series(){
            return [{
                name: "ចំណូល",
                data: this.data.monthlyIncomes
            }]
        }
    },
    mounted() {
        
    }
}
</script>

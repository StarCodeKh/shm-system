<template>
    <div>
        <div style="display: none;">{{ data.incomeExpComparations.income | json }}</div>
        <vue-apex-charts
            type=line
            height=266
            :options="chartOptions"
            :series="series" />
    </div>
</template>

<script>
    import VueApexCharts from 'vue-apexcharts'
    import analyticsData from './components/ui-elements/card/analyticsData.js'
    import router from '@/router'
    import axios from "@/axios.js"

    export default{
        props: {
            data: {
                type: Object,
                default: () => {},
            }
        },
        data() {
            return {
                analyticsData: {
                    revenueComparisonLine: {
                        analyticsData: {
                            thisMonth: 86589,
                            lastMonth: 73683
                        },
                        series: [
                            {
                                name: "ចំណូល",
                                data: [45000, 47000, 44800, 47500, 45500, 48000, 46500, 48600, 45000, 47000, 44800, 47500]
                            },
                            {
                                name: "ចំណាយ",
                                data: [46000, 48000, 45500, 46600, 44500, 46500, 45000, 47000, 45000, 47000, 44800, 47500]
                            }
                        ],
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
                            colors: ['#0cc328', '#f44542'],
                            fill: {
                                type: 'gradient',
                                gradient: {
                                    shade: 'dark',
                                    inverseColors: false,
                                    gradientToColors: ['#0cc328', '#f44542'],
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
                                categories: ['មករា', 'កម្ភៈ', 'មីនា', 'មេសា', 'ឧសភា', 'មិថុនា', 'កក្កដា', 'សីហា', 'កញ្ញា', 'តុលា', 'វិច្ឆិកា', 'ធ្នូ'],
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
                },
            }
        },
        components: {
            VueApexCharts,
            analyticsData
        },
        created(){
            
        },
        computed: {
            chartOptions(){
                return {
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
                    colors: ['#0cc328', '#f44542'],
                    fill: {
                        type: 'gradient',
                        gradient: {
                            shade: 'dark',
                            inverseColors: false,
                            gradientToColors: ['#0cc328', '#f44542'],
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
                        categories: ['មករា', 'កម្ភៈ', 'មីនា', 'មេសា', 'ឧសភា', 'មិថុនា', 'កក្កដា', 'សីហា', 'កញ្ញា', 'តុលា', 'វិច្ឆិកា', 'ធ្នូ'],
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
                return [
                    {
                        name: "ចំណូល",
                        data: this.data.incomeExpComparations.income
                    },
                    {
                        name: "ចំណាយ",
                        data: this.data.incomeExpComparations.expense
                    }
                ]
            }
        },
    }
</script>
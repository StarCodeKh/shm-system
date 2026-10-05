<template>
	<div class="line-area-chart">
        <h3>ការប្រៀបធៀបចំនួនសមាជិក </h3>
        <vue-apex-charts ref="apexChart" type="line" height=200 width='100%' :options="chartOptions" :series="series" />
    </div>
</template>

<script>
import VueApexCharts from 'vue-apexcharts'
export default {
    props: {
        data: {
            type: Object,
            default: () => {},
        }
    },
    data() {
        return {
            series: [],
            chartOptions: {}
        }
    },
    components: {
        VueApexCharts,
    },
    watch: {
        data: {
            immediate: true,
            deep: true,
            handler(val) {
                if (!this.data.officerStatisticSeries) return

                    this.series = [
                        {
                            name: 'ចំនួន',
                            type: 'column',
                            data: this.data.officerStatisticSeries
                        }
                    ]

                    this.chartOptions = {
                        chart: {
                            toolbar: { show: false },
                            height: 200,
                            type: 'line',
                            stacked: false
                        },
                        legend: {
                            show: false,
                        },
                        dataLabels: {
                            enabled: true
                        },
                        stroke: {
                            width: [1, 1, 4]
                        },
                        title: {
                            text: '',
                            align: 'left',
                            offsetX: 110
                        },
                        colors: ['#7961F9', '#FF9F43', '#EA5455'],
                        markers: {
                            size: 0,
                            hover: {
                                size: 5
                            }
                        },
                        xaxis: {
                            labels: {
                                style: {
                                    cssClass: 'text-black fill-current',
                                }
                            },
                            axisTicks: {
                                show: true,
                            },
                            categories: this.data.officerStatisticYears,
                            axisBorder: {
                                show: true,
                            },
                        },
                        yaxis: {
                            show: false,
                        },
                        tooltip: {
                            x: { show: false }
                        },
                        legend: {
                            horizontalAlign: 'left',
                            offsetX: 40
                        },
                        grid: {
                            show: false,
                            padding: {
                                left: 0,
                                right: 0
                            },
                            // row: {
                            //     colors: ['#f3f3f3', 'transparent'], // takes an array which will be repeated on columns
                            //     opacity: 0.5
                            // },
                        },
                        // chart: {
                        //     type: 'line',
                        //     height: 200,
                        //     toolbar: { show: false }
                        // },
                        // dataLabels: {
                        //     enabled: true
                        // },
                        // stroke: {
                        //     width: 2
                        // },
                        // colors: ['#7961F9'],
                        // xaxis: {
                        //     categories: this.data.officerStatisticYears
                        // },
                        // yaxis: {
                        //     show: false
                        // },
                        // grid: {
                        //     show: false
                        // }
                    }
            }
        }
    }
}
</script>

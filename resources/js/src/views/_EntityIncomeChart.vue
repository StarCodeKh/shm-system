<template>
    <div>
        <div>
            សរុប {{ data.incomeByDepartments.departments.length }}
        </div>
        <vue-apex-charts type="bar" height="350" :options="barChartOptions" :series="series"></vue-apex-charts>
        <!-- <template slot="codeContainer">
            {{ apexChatData.barChartCode }}
        </template> -->
    </div>
</template>

<script>
    import VueApexCharts from 'vue-apexcharts'
    import apexChatData from './components/charts-and-maps/charts/apex-charts/apexChartData.js'
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
                apexChatData: apexChatData,
                _series: [{
                    data: [400, 430, 448, 470, 540, 580, 690, 1100, 1200, 1380]
                }],
                _chartOptions: {
                    colors: ['#7367F0', '#28C76F', '#EA5455', '#FF9F43', '#1E1E1E'],
                    plotOptions: {
                        bar: {
                            horizontal: true,
                        }
                    },
                    dataLabels: {
                        enabled: false
                    },
                    xaxis: {
                        categories: ['អគ្គលេខាធិការដ្ឋានកែទម្រង់', 'មន្ទីរសេដ្ឋកិច្ចនិងហិរញ្ញវត្ថុ', 'ក្រសួងព្រះបរមរាជវាំង', 'ក្រសួងប្រៃសនីយ', 'ក្រួសួងសុខាភិបាល', 'ក្រសួងអភិវឌ្ឍជនបទ', 'ក្រសួងទូរគមនាគមន៍កម្ពុជា',
                            'ក្រសួងកសិកម្ម', 'ក្រសួងរ៉ែនិងថាមពល', 'ក្រសួងពាពិញ្ជកម្ម'
                        ],
                    }
                }
            }
        },
        components: {
            VueApexCharts,
            apexChatData
        },
        computed: {
            barChartOptions(){
                return {
                    colors: ['#7367F0', '#28C76F', '#EA5455', '#FF9F43', '#1E1E1E'],
                    plotOptions: {
                        bar: {
                            horizontal: true,
                        }
                    },
                    dataLabels: {
                        enabled: false
                    },
                    xaxis: {
                        categories: this.data.incomeByDepartments.departments,
                    }
                }
            },
            series(){
                return [{
                    data: this.data.incomeByDepartments.incomes
                }]
            }
        },
    }
</script>
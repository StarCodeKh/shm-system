<template>
	<vx-card title="ស្ថានភាពសមាជិក" class="mb-base" code-toggler>
    សមាជិកសរុបៈ {{this.data.totalMember.totalMembers}} នាក់, ក្នុងនោះ ប្រុស ({{this.data.totalMember.totalMales}}), ស្រី ({{this.data.totalMember.totalFemales}})
		<div class="mt-5">
			<e-charts autoresize :options="pieOptions" ref="pie" auto-resize />
		</div>
	</vx-card>
</template>

<script>
import ECharts from 'vue-echarts/components/ECharts'
import 'echarts/lib/component/tooltip'
import 'echarts/lib/component/legend'
import 'echarts/lib/chart/pie'

export default {
    props: {
        data: {
            type: Object,
            default: () => {},
        }
    },
    data() {
        return {
            pie: {}
        }
    },
    components: {
        ECharts
    },
    computed: {
        pieOptions(){
            return {
                tooltip: {
                    trigger: 'item',
                    formatter: '{a} <br/>{b} : {c} ({d}%)'
                },
                legend: {
                    orient: 'vertical',
                    left: 'left',
                    data: ['សមាជិក', 'ឈប់', 'ស្លាប់', 'ចូលនិវត្ត']
                },
                series: [{
                    name: 'ប្រភព',
                    type: 'pie',
                    radius: '55%',
                    center: ['50%', '60%'],
                    color: ['#87ceeb', '#7367f0', '#EA5455', '#FF9F43', '#7367F0'],
                    // data: [],
                    data: [
                        { value: this.data.totalMember.activeMembers, name: 'សមាជិក' },
                        { value: this.data.totalMember.inactiveMembers, name: 'ឈប់' },
                        { value: this.data.totalMember.deadMembers, name: 'ស្លាប់' },
                        { value: this.data.totalMember.retiredMembers, name: 'ចូលនិវត្ត' },
                    ],
                    itemStyle: {
                        emphasis: {
                            shadowBlur: 10,
                            shadowOffsetX: 0,
                            shadowColor: 'rgba(0, 0, 0, 0.5)'
                        }
                    }
                }]
            }
        }
    },
    mounted() {
        let dataIndex = -1
        let pie = this.$refs.pie
        let dataLen = pie.options.series[0].data.length
        setInterval(() => {
            pie.dispatchAction({
                type: 'downplay',
                seriesIndex: 0,
                dataIndex
            })
            dataIndex = (dataIndex + 1) % dataLen
            pie.dispatchAction({
                type: 'highlight',
                seriesIndex: 0,
                dataIndex
            })
            pie.dispatchAction({
                type: 'showTip',
                seriesIndex: 0,
                dataIndex
            })
        }, 1000)
    }
}
</script>

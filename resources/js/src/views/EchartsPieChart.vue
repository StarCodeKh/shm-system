<template>
	<vx-card title="ស្ថាភាពសមាជិក" class="mb-base" code-toggler>
    សមាជិកសរុបៈ 100 នាក់, ក្នុងនោះ ស្រី (៧៣), ប្រុស (២៧)
		<div class="mt-5">
			<e-charts autoresize :options="pie" ref="pie" auto-resize />
		</div>

	</vx-card>
</template>

<script>
import ECharts from 'vue-echarts/components/ECharts'
import 'echarts/lib/component/tooltip'
import 'echarts/lib/component/legend'
import 'echarts/lib/chart/pie'

export default {
    data() {
        return {
            pie: {
                tooltip: {
                    trigger: 'item',
                    formatter: '{a} <br/>{b} : {c} ({d}%)'
                },
                legend: {
                    orient: 'vertical',
                    left: 'left',
                    data: ['សមាជិក', 'ស្លាប់', 'ចូលនិវត្ត', 'ឈប់']
                },
                series: [{
                    name: 'ប្រភព',
                    type: 'pie',
                    radius: '55%',
                    center: ['50%', '60%'],
                    color: ['#FF9F43', '#28C76F', '#EA5455', '#87ceeb', '#7367F0'],
                    data: [
                        { value: 335, name: 'សមាជិក' },
                        { value: 310, name: 'ស្លាប់' },
                        { value: 234, name: 'ចូលនិវត្ត' },
                        { value: 135, name: 'ឈប់' },
                    ],
                    itemStyle: {
                        emphasis: {
                            shadowBlur: 10,
                            shadowOffsetX: 0,
                            shadowColor: 'rgba(0, 0, 0, 0.5)'
                        }
                    }
                }]
            },
        }
    },
    components: {
        ECharts
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

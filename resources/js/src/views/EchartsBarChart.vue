<template>
    <vx-card title="ការប្រើធៀបការអនុវត្តចំណូល" class="mb-base" code-toggler>

        <div class="mt-5">
            <e-charts autoresize :options="bar" ref="bar" theme="ovilia-green" auto-resize />
        </div>
    </vx-card>
</template>

<script>
import ECharts from 'vue-echarts/components/ECharts'
import 'echarts/lib/component/tooltip'
import 'echarts/lib/component/legend'
import 'echarts/lib/chart/bar'
import theme from './theme.json'

ECharts.registerTheme('ovilia-green', theme)

export default {
    data() {
        return {
            bar: {
                legend: {},
                tooltip: {},
                dataset: {
                    // Provide data.
                    source: [
                        ['Product', '2015', '2016', '2017'],
                        ['សរុបចំណូលរួមពន្ធ.ក្រោមជាតិ', ...this.randomize()],
                        ['សរុបចំណូលក្រសួង', ...this.randomize()],
                        ['ថ្នាក់កណ្តាល', ...this.randomize()],
                        ['អគ្គ.គយ និងរដ្ឋាករកម្ពុជា', ...this.randomize()]
                    ]
                },
                // Declare X axis, which is a category axis, mapping
                // to the first column by default.
                xAxis: { type: 'category' },
                // Declare Y axis, which is a value axis.
                yAxis: {},
                // Declare several series, each of them mapped to a
                // column of the dataset by default.
                series: [{ type: 'bar' }, { type: 'bar' }, { type: 'bar' }]

            },
        }
    },
    methods: {
        randomize() {
            return [
                Math.round(300 + Math.random() * 700) / 10,
                Math.round(300 + Math.random() * 700) / 10,
                Math.round(300 + Math.random() * 700) / 10
            ]
        }
    },
    components: {
        ECharts
    }
}
</script>

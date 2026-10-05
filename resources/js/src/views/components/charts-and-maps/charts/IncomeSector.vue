<template>
  <vx-card title="សមិទ្ធិកម្មចំណូលធៀបឆ្នាំមុន %">
    <!-- <template slot="actions">
      <change-time-duration-dropdown />
    </template> -->
    <div v-for="(browser, index) in browserAnalytics" :key="browser.no" :class="{'mt-4': index}">
      <div class="flex justify-between">
          <div class="flex flex-col">
            <span class="mb-1">{{ browser.sector_name }}</span>
            <h4>{{ browser.percentage }}%</h4>
          </div>
          <!-- <div class="flex flex-col text-right">
            <span class="flex -mr-1">
              <span class="mr-1">{{ browser.comparedResult }}</span>
              <feather-icon :icon=" browser.comparedResult < 0 ? 'ArrowDownIcon' : 'ArrowUpIcon'" :svgClasses="[browser.comparedResult < 0 ? 'text-danger' : 'text-success'  ,'stroke-current h-4 w-4 mb-1 mr-1']"></feather-icon>
            </span>
            <span class="text-grey">{{ browser.time | time(true) }}</span>
          </div> -->
      </div>
      <vs-progress :percent="browser.percentage"></vs-progress>
    </div>
  </vx-card>
</template>

<script>
import VuePerfectScrollbar from 'vue-perfect-scrollbar'
import VueApexCharts from 'vue-apexcharts'
import StatisticsCardLine from '@/components/statistics-cards/StatisticsCardLine.vue'
import ChangeTimeDurationDropdown from '@/components/ChangeTimeDurationDropdown.vue'
import router from '@/router'
import axios from "@/axios.js"
export default{
    props:{
        year:{
            type: Number,
            required: true
        }
    },
    data() {
      return {
        browserAnalytics: 
        [
          {
            no: 1,
            sector_name: 'វិស័យសេវាសេដ្ឋកិច្ច',
            percentage: 73,
            // time: 'Mon Dec 10 2018 07:46:05 GMT+0000 (GMT)',
            comparedResult: '800'
          },
          {
            no: 3,
            sector_name: 'វិស័យសេវាសង្គមកិច្ច',
            percentage: 8,
            // time: 'Mon Dec 10 2018 07:46:05 GMT+0000 (GMT)',
            comparedResult: '-200'
          },
          {
            no: 2,
            sector_name: 'វិស័យសាធារណៈទូទៅ',
            percentage: 19,
            // time: 'Mon Dec 10 2018 07:46:05 GMT+0000 (GMT)',
            comparedResult: '100'
          },
          {
            no: 4,
            sector_name: 'វិស័យការពារជាតិ សន្តិសុខ',
            percentage: 27,
            // time: 'Mon Dec 10 2018 07:46:05 GMT+0000 (GMT)',
            comparedResult: '-450'
          },
        ],
        summaryInfo: ""
      }
    },
    created() {
      this.getIncomeByEntity()
    },
    methods: {
      getIncomeByEntity(){
        return new Promise((resolve, reject) => {
            axios.get("/api/v1/report/income_by_sector?year=" + this.year)
                .then((response) => {
              this.browserAnalytics = response.data.data
            }).catch((error) => { reject(error) })
        })
      },
    },
    components: {
      apexchart: VueApexCharts,
      ChangeTimeDurationDropdown
    },
}
</script>

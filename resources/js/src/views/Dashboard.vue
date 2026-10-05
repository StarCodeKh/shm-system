<template>
    <div class="dashboard">
        <!-- Toolbar -->
        <div class="dashboard-toolbar">
            <div class="mr-4 mb-3">
                <h3 class="mb-1">ផ្ទាំងគ្រប់គ្រង</h3>
                <p class="text-grey">
                    ទិន្នន័យពី {{ formatDate(range.start) }} ដល់ {{ formatDate(range.end) }}
                    <span v-if="updatedAt"> · ធ្វើបច្ចុប្បន្នភាព {{ updatedAt }}</span>
                </p>
            </div>
            <div class="flex flex-wrap items-center mb-3">
                <flat-pickr
                    v-model="dateRange"
                    :config="dateConfig"
                    class="vs-input dashboard-date mr-3 mb-2"
                    placeholder="ជ្រើសរើសកាលបរិច្ឆេទ" />
                <div class="currency-switch mr-3 mb-2">
                    <button v-for="c in ['KHR', 'USD']" :key="c" :class="{ active: currency === c }" @click="currency = c">
                        {{ c === 'KHR' ? '៛ រៀល' : '$ ដុល្លារ' }}
                    </button>
                </div>
                <vx-tooltip text="ផ្ទុកឡើងវិញ" position="top">
                    <div class="toolbar-btn mb-2" :class="{ spinning: loading }" @click="load">
                        <feather-icon icon="RefreshCwIcon" svgClasses="h-5 w-5" />
                    </div>
                </vx-tooltip>
            </div>
        </div>

        <!-- Error -->
        <vx-card v-if="error" class="text-center py-8 mb-base">
            <feather-icon icon="AlertTriangleIcon" svgClasses="h-10 w-10 text-danger" />
            <p class="mt-3 mb-4">មិនអាចទាញយកទិន្នន័យបានទេ។</p>
            <vs-button type="border" @click="load">ព្យាយាមម្តងទៀត</vs-button>
        </vx-card>

        <!-- KPI cards -->
        <div class="vx-row">
            <div v-for="card in kpiCards" :key="card.key" class="vx-col w-full sm:w-1/2 xl:w-1/4 mb-base">
                <div class="kpi-card" :class="{ skeleton: !summary }">
                    <template v-if="summary">
                        <div class="flex items-start justify-between">
                            <div>
                                <p class="kpi-card__label">{{ card.label }}</p>
                                <h2 class="kpi-card__value">{{ card.value }}</h2>
                                <p class="kpi-card__sub">{{ card.sub }}</p>
                            </div>
                            <div class="kpi-card__icon" :style="{ backgroundColor: card.color + '1f', color: card.color }">
                                <feather-icon :icon="card.icon" svgClasses="h-6 w-6" />
                            </div>
                        </div>
                        <vue-apex-charts type="area" height="70" :options="sparkOptions(card.color)" :series="[{ name: card.label, data: card.trend }]" />
                    </template>
                </div>
            </div>
        </div>

        <!-- Monthly income vs expense + payment progress -->
        <div class="vx-row">
            <div class="vx-col w-full lg:w-2/3 mb-base">
                <vx-card title="ចំណូល និង ចំណាយប្រចាំខែ" class="h-full">
                    <div v-if="summary">
                        <vue-apex-charts type="bar" height="300" :options="monthlyOptions" :series="monthlySeries" />
                        <div class="quarter-strip">
                            <div v-for="q in quarters" :key="q.label" class="quarter-strip__item">
                                <span class="quarter-strip__label">{{ q.label }}</span>
                                <span class="text-success">+{{ money(q.income) }}</span>
                                <span class="text-danger">−{{ money(q.expense) }}</span>
                            </div>
                        </div>
                    </div>
                    <div v-else class="skeleton-block" style="height: 360px"></div>
                </vx-card>
            </div>

            <div class="vx-col w-full lg:w-1/3 mb-base">
                <vx-card title="ការបង់ប្រាក់សមាជិក" class="h-full">
                    <template v-if="summary">
                        <vue-apex-charts type="radialBar" height="240" :options="paymentOptions" :series="[payment.percent]" />
                        <div class="flex text-center payment-split">
                            <div class="w-1/2">
                                <p class="text-grey">បានបង់រួច</p>
                                <h3 class="text-success">{{ number(payment.paid) }}</h3>
                            </div>
                            <div class="w-1/2">
                                <p class="text-grey">មិនទាន់បង់</p>
                                <h3 class="text-danger">{{ number(payment.unpaid) }}</h3>
                            </div>
                        </div>
                        <p v-if="payment.unpaidAmount" class="text-center text-grey mt-3">
                            ប្រាក់មិនទាន់បង់: <b class="text-danger">{{ money(payment.unpaidAmount) }}</b>
                        </p>
                    </template>
                    <div v-else class="skeleton-block" style="height: 330px"></div>
                </vx-card>
            </div>
        </div>

        <!-- Members -->
        <div class="vx-row">
            <div class="vx-col w-full lg:w-1/2 mb-base">
                <vx-card title="ស្ថានភាពសមាជិក" class="h-full">
                    <template v-if="summary">
                        <vue-apex-charts type="donut" height="300" :options="memberStatusOptions" :series="memberStatus.map(s => s.value)" />
                    </template>
                    <div v-else class="skeleton-block" style="height: 300px"></div>
                </vx-card>
            </div>
            <div class="vx-col w-full lg:w-1/2 mb-base">
                <vx-card title="សមាជិកថ្មីតាមឆ្នាំ" class="h-full">
                    <vue-apex-charts v-if="summary" type="bar" height="300" :options="growthOptions" :series="[{ name: 'សមាជិកថ្មី', data: summary.officerStatisticSeries || [] }]" />
                    <div v-else class="skeleton-block" style="height: 300px"></div>
                </vx-card>
            </div>
        </div>

        <!-- Incentives + income by entity -->
        <div class="vx-row">
            <div class="vx-col w-full lg:w-1/3 mb-base">
                <vx-card title="ប្រាក់ឧបត្ថម្ភតាមប្រភេទ" class="h-full">
                    <template v-if="summary">
                        <template v-if="incentives.length">
                            <vue-apex-charts type="donut" height="240" :options="incentiveOptions" :series="incentives.map(i => i.amount)" />
                            <ul class="legend-list">
                                <li v-for="(item, i) in incentives" :key="item.name">
                                    <span class="flex items-center">
                                        <span class="legend-dot" :style="{ backgroundColor: palette[i % palette.length] }"></span>
                                        {{ item.name }}
                                    </span>
                                    <b>{{ money(item.amount) }}</b>
                                </li>
                            </ul>
                        </template>
                        <div v-else class="empty-state">
                            <feather-icon icon="GiftIcon" svgClasses="h-8 w-8" />
                            <p>មិនមានប្រាក់ឧបត្ថម្ភក្នុងរយៈពេលនេះ</p>
                        </div>
                    </template>
                    <div v-else class="skeleton-block" style="height: 300px"></div>
                </vx-card>
            </div>
            <div class="vx-col w-full lg:w-2/3 mb-base">
                <vx-card title="ចំណូលតាមស្ថាប័ន (៛)" class="h-full">
                    <template v-if="summary">
                        <vue-apex-charts v-if="entityIncome.length" type="bar" :height="Math.max(220, entityIncome.length * 42)" :options="entityOptions" :series="[{ name: 'ចំណូល', data: entityIncome.map(e => e.amount) }]" />
                        <div v-else class="empty-state">
                            <feather-icon icon="BarChart2Icon" svgClasses="h-8 w-8" />
                            <p>មិនមានទិន្នន័យចំណូល</p>
                        </div>
                    </template>
                    <div v-else class="skeleton-block" style="height: 300px"></div>
                </vx-card>
            </div>
        </div>
    </div>
</template>

<script>
import VueApexCharts from "vue-apexcharts"
import flatPickr from "vue-flatpickr-component"
import "flatpickr/dist/flatpickr.css"
import axios from "@/axios.js"
import router from "@/router"

const KH_MONTHS = ["មករា", "កុម្ភៈ", "មីនា", "មេសា", "ឧសភា", "មិថុនា", "កក្កដា", "សីហា", "កញ្ញា", "តុលា", "វិច្ឆិកា", "ធ្នូ"]
const PALETTE = ["#7367F0", "#28C76F", "#FF9F43", "#EA5455", "#00CFE8", "#F35588", "#4A5F9D"]
const toNumber = (value) => Number(String(value || 0).replace(/,/g, "")) || 0

export default {
    components: { VueApexCharts, flatPickr },
    data() {
        const year = new Date().getFullYear()
        return {
            summary: null,
            loading: false,
            error: false,
            updatedAt: "",
            currency: "KHR",
            dateRange: [`${year}-01-01`, `${year}-12-31`],
            dateConfig: { mode: "range", dateFormat: "Y-m-d", allowInput: true },
            palette: PALETTE,
        }
    },
    computed: {
        range() {
            const value = Array.isArray(this.dateRange) ? this.dateRange : String(this.dateRange || "").split(" to ")
            // While picking, flatpickr reports only the start date; wait for the end date.
            return { start: value[0], end: value[1] || null }
        },
        isKhr() {
            return this.currency === "KHR"
        },
        trend() {
            const t = (this.summary && this.summary.monthlyTrend) || { months: [], income_khr: [], income_usd: [], expense_khr: [], expense_usd: [] }
            const income = this.isKhr ? t.income_khr : t.income_usd
            const expense = this.isKhr ? t.expense_khr : t.expense_usd
            let running = 0
            const balance = income.map((value, i) => (running += value - expense[i]))
            return { months: t.months, income, expense, balance }
        },
        kpiCards() {
            const s = this.summary
            if (!s) return [1, 2, 3, 4].map(key => ({ key }))
            const pick = (obj, khr, usd) => toNumber(this.isKhr ? obj[khr] : obj[usd])
            const members = s.totalMember || {}
            return [
                {
                    key: "members", label: "សមាជិកសរុប", icon: "UsersIcon", color: "#7367F0",
                    value: this.number(members.totalMembers),
                    sub: `ប្រុស ${this.number(members.totalMales)} · ស្រី ${this.number(members.totalFemales)}`,
                    trend: s.officerStatisticSeries || [],
                },
                {
                    key: "income", label: "ចំណូលសរុប", icon: "TrendingUpIcon", color: "#28C76F",
                    value: this.money(pick(s.totalIncome, "total_in_khr", "total_in_usd")),
                    sub: "ចំណូល + ការបង់ប្រាក់សមាជិក", trend: this.trend.income,
                },
                {
                    key: "expense", label: "ចំណាយសរុប", icon: "TrendingDownIcon", color: "#EA5455",
                    value: this.money(pick(s.totalExpense, "total_in_khr", "total_in_usd")),
                    sub: "ចំណាយ + ប្រាក់ឧបត្ថម្ភ", trend: this.trend.expense,
                },
                {
                    key: "balance", label: "សន្និធិសាច់ប្រាក់", icon: "DollarSignIcon", color: "#FF9F43",
                    value: this.money(pick(s.totalRemainingBalance, "remaining_balance_khr", "remaining_balance_usd")),
                    sub: "ចំណូល − ចំណាយ", trend: this.trend.balance,
                },
            ]
        },
        monthLabels() {
            const multiYear = new Set(this.trend.months.map(m => m.slice(0, 4))).size > 1
            return this.trend.months.map(m => KH_MONTHS[Number(m.slice(5, 7)) - 1] + (multiYear ? ` ${m.slice(2, 4)}` : ""))
        },
        monthlySeries() {
            return [
                { name: "ចំណូល", data: this.trend.income },
                { name: "ចំណាយ", data: this.trend.expense },
            ]
        },
        monthlyOptions() {
            return {
                chart: { toolbar: { show: false }, fontFamily: "inherit" },
                colors: ["#28C76F", "#EA5455"],
                plotOptions: { bar: { columnWidth: "45%", borderRadius: 4 } },
                dataLabels: { enabled: false },
                legend: { position: "top", horizontalAlign: "right" },
                grid: { borderColor: "#eef0f4", strokeDashArray: 4 },
                xaxis: { categories: this.monthLabels, axisBorder: { show: false }, axisTicks: { show: false } },
                yaxis: { labels: { formatter: this.compact } },
                tooltip: { y: { formatter: this.money } },
            }
        },
        quarters() {
            const result = []
            this.trend.months.forEach((month, i) => {
                const label = `Q${Math.ceil(Number(month.slice(5, 7)) / 3)} ${month.slice(0, 4)}`
                let q = result.find(r => r.label === label)
                if (!q) result.push((q = { label, income: 0, expense: 0 }))
                q.income += this.trend.income[i]
                q.expense += this.trend.expense[i]
            })
            return result
        },
        payment() {
            const p = (this.summary && this.summary.officerPaymentInfo) || {}
            return {
                percent: toNumber(p.percent_paid),
                paid: toNumber(p.officers_paid),
                unpaid: toNumber(p.officers_unpaid),
                unpaidAmount: toNumber(this.isKhr ? p.total_unpaid_khr : p.total_unpaid_usd),
            }
        },
        paymentOptions() {
            return {
                chart: { fontFamily: "inherit", sparkline: { enabled: true } },
                colors: ["#28C76F"],
                plotOptions: {
                    radialBar: {
                        hollow: { size: "68%" },
                        track: { background: "#eef0f4" },
                        dataLabels: {
                            name: { show: true, offsetY: 22, color: "#8a90a0", fontSize: "13px" },
                            value: { offsetY: -12, fontSize: "34px", fontWeight: 600, formatter: v => `${v}%` },
                        },
                    },
                },
                fill: { type: "gradient", gradient: { shade: "light", type: "horizontal", gradientToColors: ["#7367F0"], stops: [0, 100] } },
                stroke: { lineCap: "round" },
                labels: ["បានបង់"],
            }
        },
        memberStatus() {
            const m = (this.summary && this.summary.totalMember) || {}
            return [
                { label: "សកម្ម", value: toNumber(m.activeMembers) },
                { label: "អសកម្ម", value: toNumber(m.inactiveMembers) },
                { label: "ចូលនិវត្តន៍", value: toNumber(m.retiredMembers) },
                { label: "ទទួលមរណភាព", value: toNumber(m.deadMembers) },
            ]
        },
        memberStatusOptions() {
            return {
                chart: { fontFamily: "inherit" },
                labels: this.memberStatus.map(s => s.label),
                colors: ["#28C76F", "#FF9F43", "#00CFE8", "#82868B"],
                legend: { position: "bottom" },
                dataLabels: { enabled: false },
                stroke: { width: 2 },
                plotOptions: {
                    pie: {
                        donut: {
                            size: "70%",
                            labels: { show: true, total: { show: true, label: "សរុប", formatter: () => this.number(this.memberStatus.reduce((a, s) => a + s.value, 0)) } },
                        },
                    },
                },
                tooltip: { y: { formatter: v => `${this.number(v)} នាក់` } },
            }
        },
        growthOptions() {
            return {
                chart: { toolbar: { show: false }, fontFamily: "inherit" },
                colors: ["#7367F0"],
                plotOptions: { bar: { columnWidth: "45%", borderRadius: 4, dataLabels: { position: "top" } } },
                dataLabels: { enabled: true, offsetY: -20, style: { colors: ["#6e7485"], fontWeight: 500 } },
                grid: { borderColor: "#eef0f4", strokeDashArray: 4 },
                xaxis: { categories: (this.summary && this.summary.officerStatisticYears) || [], axisBorder: { show: false }, axisTicks: { show: false } },
                yaxis: { show: false },
                tooltip: { y: { formatter: v => `${this.number(v)} នាក់` } },
            }
        },
        incentives() {
            const list = (this.summary && this.summary.totalIncentive) || []
            return list
                .map(i => ({ name: i.payment_type_name, amount: toNumber(this.isKhr ? i.total_amount_khr : i.total_amount_usd) }))
                .filter(i => i.amount > 0)
        },
        incentiveOptions() {
            return {
                chart: { fontFamily: "inherit" },
                labels: this.incentives.map(i => i.name),
                colors: PALETTE,
                legend: { show: false },
                dataLabels: { enabled: false },
                plotOptions: { pie: { donut: { size: "65%" } } },
                tooltip: { y: { formatter: this.money } },
            }
        },
        entityIncome() {
            const data = (this.summary && this.summary.incomeByDepartments) || {}
            const names = data.departments || []
            return (data.incomes || [])
                .map((amount, i) => ({ name: names[i] || "-", amount: toNumber(amount) }))
                .sort((a, b) => b.amount - a.amount)
                .slice(0, 10)
        },
        entityOptions() {
            return {
                chart: { toolbar: { show: false }, fontFamily: "inherit" },
                colors: ["#4A5F9D"],
                plotOptions: { bar: { horizontal: true, barHeight: "55%", borderRadius: 4 } },
                dataLabels: { enabled: true, formatter: this.compact, style: { fontWeight: 500 } },
                grid: { borderColor: "#eef0f4", strokeDashArray: 4 },
                xaxis: { categories: this.entityIncome.map(e => e.name), labels: { formatter: this.compact } },
                // The API reports income by entity in riel only.
                tooltip: { y: { formatter: v => `៛ ${Math.round(toNumber(v)).toLocaleString("en-US")}` } },
            }
        },
    },
    watch: {
        range(value, old) {
            if (value.start && value.end && (value.start !== old.start || value.end !== old.end)) this.load()
        },
    },
    created() {
        if (!localStorage.getItem("accessToken")) {
            router.push(router.currentRoute.query.to || "/pages/login")
            return
        }
        this.load()
    },
    mounted() {
        this.wasSidebarOpen = this.$store.state.reduceButton
        this.$store.commit("TOGGLE_REDUCE_BUTTON", true)
    },
    beforeDestroy() {
        if (!this.wasSidebarOpen) this.$store.commit("TOGGLE_REDUCE_BUTTON", false)
    },
    methods: {
        async load() {
            this.loading = true
            this.error = false
            try {
                const end = new Date(this.range.end)
                const response = await axios.post("/api/v1/mobile/dashboard-summary", {
                    year: end.getFullYear(),
                    quarter: Math.floor(end.getMonth() / 3) + 1,
                    start_date: this.range.start,
                    end_date: this.range.end,
                })
                if (response.data.errorCode !== "000") throw new Error(response.data.errorMessage)
                this.summary = response.data.data
                this.updatedAt = new Date().toLocaleTimeString("en-GB", { hour: "2-digit", minute: "2-digit" })
            } catch (e) {
                this.error = true
            }
            this.loading = false
        },
        number(value) {
            return toNumber(value).toLocaleString("en-US")
        },
        money(value) {
            const amount = toNumber(value)
            return this.isKhr
                ? `៛ ${Math.round(amount).toLocaleString("en-US")}`
                : `$ ${amount.toLocaleString("en-US", { maximumFractionDigits: 2 })}`
        },
        compact(value) {
            const amount = toNumber(value)
            const units = [[1e9, "B"], [1e6, "M"], [1e3, "K"]]
            const unit = units.find(([size]) => Math.abs(amount) >= size)
            return unit ? `${+(amount / unit[0]).toFixed(1)}${unit[1]}` : `${Math.round(amount)}`
        },
        formatDate(value) {
            if (!value) return "-"
            const [y, m, d] = value.split("-")
            return `${d}/${m}/${y}`
        },
        sparkOptions(color) {
            return {
                chart: { sparkline: { enabled: true }, fontFamily: "inherit" },
                colors: [color],
                stroke: { curve: "smooth", width: 2.5 },
                fill: { type: "gradient", gradient: { opacityFrom: 0.45, opacityTo: 0.05 } },
                tooltip: { enabled: false },
            }
        },
    },
}
</script>

<style lang="scss" scoped>
.dashboard-toolbar {
    display: flex;
    flex-wrap: wrap;
    align-items: flex-end;
    justify-content: space-between;
    margin-bottom: 1rem;
}

.dashboard-date {
    min-width: 230px;
}

.currency-switch {
    display: inline-flex;
    padding: 3px;
    border-radius: 999px;
    background: #eef0f4;

    button {
        border: 0;
        background: transparent;
        padding: 0.4rem 0.9rem;
        border-radius: 999px;
        cursor: pointer;
        color: #626262;
        font-family: inherit;
        transition: all 0.2s ease;

        &.active {
            background: #fff;
            color: rgba(var(--vs-primary), 1);
            font-weight: 600;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
        }
    }
}

.toolbar-btn {
    padding: 0.6rem;
    border-radius: 50%;
    background: #fff;
    color: #626262;
    cursor: pointer;
    display: flex;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    transition: color 0.2s ease;

    &:hover {
        color: rgba(var(--vs-primary), 1);
    }

    &.spinning svg {
        animation: dashboard-spin 0.8s linear infinite;
    }
}

.kpi-card {
    height: 100%;
    min-height: 170px;
    padding: 1.25rem 1.25rem 0;
    border-radius: 1.25rem;
    background: #fff;
    box-shadow: 0 4px 24px rgba(34, 41, 47, 0.1);
    overflow: hidden;
    transition: transform 0.2s ease, box-shadow 0.2s ease;

    &:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 30px rgba(34, 41, 47, 0.15);
    }

    &__label {
        color: #8a90a0;
        margin-bottom: 0.25rem;
    }

    &__value {
        font-size: 1.6rem;
        font-weight: 700;
        margin-bottom: 0.15rem;
        white-space: nowrap;
    }

    &__sub {
        font-size: 0.85rem;
        color: #8a90a0;
    }

    &__icon {
        width: 3rem;
        height: 3rem;
        border-radius: 0.75rem;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }
}

.quarter-strip {
    display: flex;
    flex-wrap: wrap;
    gap: 0.75rem;
    margin-top: 0.5rem;

    &__item {
        flex: 1 1 140px;
        display: flex;
        flex-direction: column;
        padding: 0.6rem 0.8rem;
        border-radius: 0.75rem;
        background: #f6f7fb;
        font-size: 0.85rem;
    }

    &__label {
        font-weight: 600;
        margin-bottom: 0.2rem;
    }
}

.payment-split {
    border-top: 1px solid #eef0f4;
    padding-top: 1rem;
}

.legend-list {
    li {
        display: flex;
        justify-content: space-between;
        padding: 0.6rem 0;
        border-top: 1px solid #eef0f4;
    }
}

.legend-dot {
    width: 0.7rem;
    height: 0.7rem;
    border-radius: 50%;
    margin-right: 0.5rem;
}

.empty-state {
    height: 260px;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    color: #b0b5c1;

    p {
        margin-top: 0.75rem;
    }
}

.skeleton,
.skeleton-block {
    background: linear-gradient(90deg, #f3f4f7 25%, #e9ebf0 37%, #f3f4f7 63%);
    background-size: 400% 100%;
    animation: dashboard-skeleton 1.4s ease infinite;
    border-radius: 1rem;
}

@keyframes dashboard-skeleton {
    0% { background-position: 100% 50%; }
    100% { background-position: 0 50%; }
}

@keyframes dashboard-spin {
    to { transform: rotate(360deg); }
}
</style>

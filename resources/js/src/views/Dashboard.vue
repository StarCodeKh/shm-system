<template>
    <div class="dashboard">
        <!-- Toolbar -->
        <div class="dashboard-toolbar">
            <div class="mr-4 mb-3">
                <h3 class="mb-1">{{ $t('Dashboard') }}</h3>
                <p class="text-grey">
                    {{ $t('dash_data_range', { start: formatDate(range.start), end: formatDate(range.end) }) }}
                    <span v-if="updatedAt"> · {{ $t('dash_updated_at', { time: updatedAt }) }}</span>
                </p>
            </div>
            <div class="flex flex-wrap items-center mb-3">
                <flat-pickr
                    v-model="dateRange"
                    :config="dateConfig"
                    class="vs-input dashboard-date mr-3 mb-2"
                    :placeholder="$t('dash_pick_date')" />
                <div v-tab-pill class="currency-switch mr-3 mb-2">
                    <button v-for="c in ['KHR', 'USD']" :key="c" :class="{ active: currency === c }" @click="currency = c">
                        {{ c === 'KHR' ? '៛ ' + $t('dash_riel') : '$ ' + $t('dash_dollar') }}
                    </button>
                </div>
                <vx-tooltip :text="$t('dash_reload')" position="top">
                    <button type="button" class="toolbar-btn mb-2" :class="{ spinning: loading }" :aria-label="$t('dash_reload')" @click="load">
                        <feather-icon icon="RefreshCwIcon" svgClasses="h-4 w-4" />
                    </button>
                </vx-tooltip>
            </div>
        </div>

        <!-- Error -->
        <vx-card v-if="error" class="text-center py-8 mb-base">
            <feather-icon icon="AlertTriangleIcon" svgClasses="h-10 w-10 text-danger" />
            <p class="mt-3 mb-4">{{ $t('dash_load_error') }}</p>
            <vs-button type="border" @click="load">{{ $t('dash_retry') }}</vs-button>
        </vx-card>

        <!-- KPI cards -->
        <div class="vx-row">
            <div v-for="card in kpiCards" :key="card.key" class="vx-col w-full sm:w-1/2 mb-base" :class="kpiWidth">
                <dash-link v-if="summary" :to="card.to" tag="div" class="kpi-card" :class="{ 'is-link': card.to }" :aria-label="card.to ? `${card.label} · ${$t('dash_view_details')}` : null">
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
                    <span v-if="card.to" class="kpi-card__more" :style="{ color: card.color }">
                        {{ $t('dash_view_details') }}
                        <feather-icon icon="ArrowRightIcon" svgClasses="h-4 w-4" />
                    </span>
                </dash-link>
                <div v-else class="kpi-card skeleton"></div>
            </div>
        </div>

        <!-- Monthly income vs expense + payment progress -->
        <div v-if="access.balance || access.payments" class="vx-row">
            <div v-if="access.balance" class="vx-col w-full mb-base" :class="access.payments ? 'lg:w-2/3' : ''">
                <vx-card :title="$t(byYear ? 'dash_yearly_income_expense' : 'dash_monthly_income_expense')" class="h-full">
                    <template v-if="targets.income" #actions>
                        <router-link :to="targets.income" class="card-link">
                            {{ $t('dash_view_all') }}
                            <feather-icon icon="ChevronRightIcon" svgClasses="h-4 w-4" />
                        </router-link>
                    </template>
                    <div v-if="summary && hasMonthly">
                        <vue-apex-charts type="bar" height="300" :class="{ 'is-clickable': links.payments || links.allowances }" :options="monthlyOptions" :series="monthlySeries" />
                        <div v-if="!byYear" class="quarter-strip">
                            <div v-for="q in quarters" :key="q.label" class="quarter-strip__item">
                                <span class="quarter-strip__label">{{ q.label }}</span>
                                <dash-link :to="q.incomeTo" class="text-success">+{{ money(q.income) }}</dash-link>
                                <dash-link :to="q.expenseTo" class="text-danger">−{{ money(q.expense) }}</dash-link>
                            </div>
                        </div>
                    </div>
                    <div v-else-if="summary" class="empty-state" style="height: 360px">
                        <span class="empty-state__icon"><feather-icon icon="BarChartIcon" svgClasses="h-7 w-7" /></span>
                        <p class="empty-state__title">{{ $t('dash_no_data_period') }}</p>
                        <small>{{ $t('dash_empty_hint') }}</small>
                    </div>
                    <div v-else class="skeleton-block" style="height: 360px"></div>
                </vx-card>
            </div>

            <div v-if="access.payments" class="vx-col w-full mb-base" :class="access.balance ? 'lg:w-1/3' : ''">
                <vx-card :title="$t('dash_member_payments')" class="h-full">
                    <template v-if="targets.payables" #actions>
                        <router-link :to="targets.payables" class="card-link">
                            {{ $t('dash_view_all') }}
                            <feather-icon icon="ChevronRightIcon" svgClasses="h-4 w-4" />
                        </router-link>
                    </template>
                    <template v-if="summary && hasPayables">
                        <dash-link :to="targets.payables" tag="div" class="block">
                            <vue-apex-charts type="radialBar" height="240" :class="{ 'is-clickable': links.payments }" :options="paymentOptions" :series="[payment.percent]" />
                        </dash-link>
                        <div class="flex text-center payment-split">
                            <dash-link :to="targets.paid" tag="div" class="w-1/2 stat-link">
                                <p class="text-grey">{{ $t('dash_paid') }}</p>
                                <h3 class="text-success">{{ number(payment.paid) }}</h3>
                            </dash-link>
                            <dash-link :to="targets.unpaid" tag="div" class="w-1/2 stat-link">
                                <p class="text-grey">{{ $t('dash_unpaid') }}</p>
                                <h3 class="text-danger">{{ number(payment.unpaid) }}</h3>
                            </dash-link>
                        </div>
                        <p v-if="payment.unpaidAmount" class="text-center text-grey mt-3">
                            {{ $t('dash_unpaid_amount') }}: <dash-link :to="targets.unpaidAmount" tag="b" class="text-danger">{{ money(payment.unpaidAmount) }}</dash-link>
                        </p>
                    </template>
                    <div v-else-if="summary" class="empty-state" style="height: 330px">
                        <span class="empty-state__icon"><feather-icon icon="CreditCardIcon" svgClasses="h-7 w-7" /></span>
                        <p class="empty-state__title">{{ $t('dash_no_payables') }}</p>
                        <small>{{ $t('dash_empty_hint') }}</small>
                    </div>
                    <div v-else class="skeleton-block" style="height: 330px"></div>
                </vx-card>
            </div>
        </div>

        <!-- Members + requests -->
        <div v-if="access.members || access.requests" class="vx-row">
            <div v-if="access.members" class="vx-col w-full mb-base" :class="memberColWidth">
                <vx-card :title="$t('dash_member_status')" class="h-full">
                    <template v-if="links.members" #actions>
                        <router-link :to="links.members" class="card-link">
                            {{ $t('dash_view_all') }}
                            <feather-icon icon="ChevronRightIcon" svgClasses="h-4 w-4" />
                        </router-link>
                    </template>
                    <template v-if="summary && memberTotal">
                        <vue-apex-charts type="donut" height="300" :class="{ 'is-clickable': links.members }" :options="memberStatusOptions" :series="memberStatus.map(s => s.value)" />
                    </template>
                    <div v-else-if="summary" class="empty-state" style="height: 300px">
                        <span class="empty-state__icon"><feather-icon icon="UsersIcon" svgClasses="h-7 w-7" /></span>
                        <p class="empty-state__title">{{ $t('dash_no_members') }}</p>
                    </div>
                    <div v-else class="skeleton-block" style="height: 300px"></div>
                </vx-card>
            </div>
            <div v-if="access.members" class="vx-col w-full mb-base" :class="memberColWidth">
                <vx-card :title="$t('dash_new_members_by_year')" class="h-full">
                    <template v-if="links.members" #actions>
                        <router-link :to="links.members" class="card-link">
                            {{ $t('dash_view_all') }}
                            <feather-icon icon="ChevronRightIcon" svgClasses="h-4 w-4" />
                        </router-link>
                    </template>
                    <vue-apex-charts v-if="summary && hasGrowth" type="bar" height="300" :class="{ 'is-clickable': links.members }" :options="growthOptions" :series="[{ name: $t('dash_new_members'), data: summary.officerStatisticSeries || [] }]" />
                    <div v-else-if="summary" class="empty-state" style="height: 300px">
                        <span class="empty-state__icon"><feather-icon icon="UserPlusIcon" svgClasses="h-7 w-7" /></span>
                        <p class="empty-state__title">{{ $t('dash_no_new_members') }}</p>
                    </div>
                    <div v-else class="skeleton-block" style="height: 300px"></div>
                </vx-card>
            </div>
            <div v-if="access.requests" class="vx-col w-full mb-base" :class="memberColWidth">
                <vx-card :title="$t('dash_requests')" class="h-full">
                    <template v-if="links.requests" #actions>
                        <router-link :to="links.requests" class="card-link">
                            {{ $t('dash_view_all') }}
                            <feather-icon icon="ChevronRightIcon" svgClasses="h-4 w-4" />
                        </router-link>
                    </template>
                    <template v-if="summary">
                        <div v-if="requests.pending || requests.approved" class="request-stats">
                            <dash-link :to="links.requests" tag="div" class="request-stat stat-link">
                                <span class="request-stat__icon is-warning"><feather-icon icon="ClockIcon" svgClasses="h-6 w-6" /></span>
                                <div>
                                    <p class="text-grey">{{ $t('dash_request_pending') }}</p>
                                    <h3 class="text-warning">{{ number(requests.pending) }}</h3>
                                </div>
                            </dash-link>
                            <dash-link :to="links.requests" tag="div" class="request-stat stat-link">
                                <span class="request-stat__icon is-success"><feather-icon icon="CheckCircleIcon" svgClasses="h-6 w-6" /></span>
                                <div>
                                    <p class="text-grey">{{ $t('dash_request_approved') }}</p>
                                    <h3 class="text-success">{{ number(requests.approved) }}</h3>
                                </div>
                            </dash-link>
                        </div>
                        <div v-else class="empty-state" style="height: 300px">
                            <span class="empty-state__icon"><feather-icon icon="InboxIcon" svgClasses="h-7 w-7" /></span>
                            <p class="empty-state__title">{{ $t('dash_no_requests') }}</p>
                            <small>{{ $t('dash_no_requests_hint') }}</small>
                        </div>
                    </template>
                    <div v-else class="skeleton-block" style="height: 300px"></div>
                </vx-card>
            </div>
        </div>

        <!-- Incentives + income by entity -->
        <div v-if="access.incentives || access.income" class="vx-row">
            <div v-if="access.incentives" class="vx-col w-full mb-base" :class="access.income ? 'lg:w-1/3' : ''">
                <vx-card :title="$t('dash_allowance_by_type')" class="h-full">
                    <template v-if="targets.expense" #actions>
                        <router-link :to="targets.expense" class="card-link">
                            {{ $t('dash_view_all') }}
                            <feather-icon icon="ChevronRightIcon" svgClasses="h-4 w-4" />
                        </router-link>
                    </template>
                    <template v-if="summary">
                        <template v-if="incentives.length">
                            <vue-apex-charts type="donut" height="240" :class="{ 'is-clickable': links.allowances }" :options="incentiveOptions" :series="incentives.map(i => i.amount)" />
                            <ul class="legend-list">
                                <li v-for="item in incentives" :key="item.name">
                                    <dash-link :to="item.to" tag="div" class="legend-row">
                                        <span class="flex items-center">
                                            <span class="legend-dot" :style="{ backgroundColor: item.color }"></span>
                                            {{ item.name }}
                                        </span>
                                        <span class="legend-value"><b>{{ money(item.amount) }}</b><small>{{ item.share }}%</small></span>
                                    </dash-link>
                                </li>
                            </ul>
                        </template>
                        <div v-else class="empty-state">
                            <span class="empty-state__icon"><feather-icon icon="GiftIcon" svgClasses="h-7 w-7" /></span>
                            <p class="empty-state__title">{{ $t('dash_no_allowance') }}</p>
                            <small>{{ $t('dash_empty_hint') }}</small>
                        </div>
                    </template>
                    <div v-else class="skeleton-block" style="height: 300px"></div>
                </vx-card>
            </div>
            <div v-if="access.income" class="vx-col w-full mb-base" :class="access.incentives ? 'lg:w-2/3' : ''">
                <vx-card :title="$t('dash_income_by_entity') + ' (៛)'" class="h-full">
                    <template v-if="targets.income" #actions>
                        <router-link :to="targets.income" class="card-link">
                            {{ $t('dash_view_all') }}
                            <feather-icon icon="ChevronRightIcon" svgClasses="h-4 w-4" />
                        </router-link>
                    </template>
                    <template v-if="summary">
                        <vue-apex-charts v-if="entityIncome.length" type="bar" :class="{ 'is-clickable': links.payments }" :height="Math.max(220, entityIncome.length * 42)" :options="entityOptions" :series="[{ name: $t('dash_income'), data: entityIncome.map(e => e.amount) }]" />
                        <div v-else class="empty-state">
                            <span class="empty-state__icon"><feather-icon icon="BarChart2Icon" svgClasses="h-7 w-7" /></span>
                            <p class="empty-state__title">{{ $t('dash_no_income') }}</p>
                            <small>{{ $t('dash_empty_hint') }}</small>
                        </div>
                    </template>
                    <div v-else class="skeleton-block" style="height: 300px"></div>
                </vx-card>
            </div>
        </div>
    </div>
</template>

<script>
import { h } from "vue"
import { RouterLink } from "vue-router"
import VueApexCharts from "vue3-apexcharts"
import flatPickr from "vue-flatpickr-component"
import "flatpickr/dist/flatpickr.css"
import axios from "@/axios.js"
import router from "@/router"
import { hasMenuUrl } from "@/layouts/components/vertical-nav-menu/menuActive.js"
import { linkRoute } from "@/listLink.js"

const MONTHS = {
    kh: ["មករា", "កុម្ភៈ", "មីនា", "មេសា", "ឧសភា", "មិថុនា", "កក្កដា", "សីហា", "កញ្ញា", "តុលា", "វិច្ឆិកា", "ធ្នូ"],
    en: ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"],
}
// Chart colors come from the theme (see tone), so they match the app and follow dark mode
const FALLBACK = { primary: "#28C76F", success: "#28C76F", danger: "#EA5455", warning: "#FF9F43" }
const cssColor = (name, fallback) => {
    const value = typeof document !== "undefined" ? getComputedStyle(document.documentElement).getPropertyValue(name).trim() : ""
    return value ? `rgb(${value})` : fallback
}
const toNumber = (value) => Number(String(value || 0).replace(/,/g, "")) || 0
// A local calendar day as Y-m-d (toISOString would move it to the UTC day)
const ymd = (date) => `${date.getFullYear()}-${String(date.getMonth() + 1).padStart(2, "0")}-${String(date.getDate()).padStart(2, "0")}`
// Quick ranges at the top of the date picker, counted from today. The current ones end today:
// no days that have not happened yet.
const PRESETS = {
    dash_range_this_month: (t) => [new Date(t.getFullYear(), t.getMonth(), 1), t],
    dash_range_last_month: (t) => [new Date(t.getFullYear(), t.getMonth() - 1, 1), new Date(t.getFullYear(), t.getMonth(), 0)],
    dash_range_this_quarter: (t) => [new Date(t.getFullYear(), Math.floor(t.getMonth() / 3) * 3, 1), t],
    dash_range_this_year: (t) => [new Date(t.getFullYear(), 0, 1), t],
    dash_range_last_year: (t) => [new Date(t.getFullYear() - 1, 0, 1), new Date(t.getFullYear() - 1, 11, 31)],
    // From the first record (firstDate, sent by the API) to today; this year until that is known
    dash_range_all_time: (t, first) => [first ? parseYmd(first) : new Date(t.getFullYear(), 0, 1), t],
}
// A Y-m-d as a local calendar day (new Date("Y-m-d") would read it as UTC)
const parseYmd = (value) => {
    const [y, m, d] = value.split("-").map(Number)
    return new Date(y, m - 1, d)
}
const presetRange = (key, first = null) => PRESETS[key](new Date(), first).map(ymd)
// The last day of a "YYYY-MM" month as Y-m-d
const monthEnd = (month) => {
    const [y, m] = month.split("-").map(Number)
    return ymd(new Date(y, m, 0))
}
// Member status ids (App\Models\Settings\Status, fixed by StatusSeeder) of the member status chart
const MEMBER_STATUS = { active: 13, inactive: 14, retired: 27, dead: 26 }
// The dashboard opens on this range, and goes back to it when the picker is cleared
const DEFAULT_PRESET = "dash_range_this_year"
// The quick-range row scrolls sideways: fade the edge on each side that has more buttons
const updatePresetFades = (bar) => {
    bar.classList.toggle("fade-start", bar.scrollLeft > 1)
    bar.classList.toggle("fade-end", bar.scrollLeft + bar.clientWidth < bar.scrollWidth - 1)
}
// Where each dashboard item leads. A target only becomes a link when the user can see
// that page in the sidebar menu, so the dashboard follows the same role permissions.
// Income / expense totals include member payments and benefits (most of the money), so those
// cards, the monthly chart and the income-by-institution chart open the payment and benefit lists.
const TARGETS = {
    members: "/officer",
    balance: "/account/chart-account",
    payments: "/account/officer-payment",
    allowances: "/incentive",
    requests: "/request",
}

// A router link when `to` is set, otherwise the same content in a plain `tag`
const DashLink = (props, { attrs, slots }) => props.to
    ? h(RouterLink, { ...attrs, to: props.to }, slots)
    : h(props.tag, attrs, slots.default && slots.default())
DashLink.props = { to: String, tag: { type: String, default: "span" } }
DashLink.inheritAttrs = false

export default {
    components: { VueApexCharts, flatPickr, DashLink },
    data() {
        return {
            summary: null,
            loading: false,
            error: false,
            updatedAt: "",
            currency: "KHR",
            dateRange: presetRange(DEFAULT_PRESET),
            firstDate: null, // first money record, where "All time" starts (from the API)
            dateConfig: {
                mode: "range",
                dateFormat: "Y-m-d",
                allowInput: true,
                onReady: [(dates, text, fp) => this.addPresets(fp)],
                onOpen: [(dates, text, fp) => this.markPresets(fp)],
            },
        }
    },
    computed: {
        // Target path per item, or null when the page is not in the user's menu
        links() {
            return Object.keys(TARGETS).reduce((links, key) => {
                links[key] = hasMenuUrl(TARGETS[key]) ? TARGETS[key] : null
                return links
            }, {})
        },
        tone() {
            const dark = this.$store.state.theme === "dark"
            return {
                primary: cssColor("--vs-primary", FALLBACK.primary),
                success: cssColor("--vs-success", FALLBACK.success),
                danger: cssColor("--vs-danger", FALLBACK.danger),
                warning: cssColor("--vs-warning", FALLBACK.warning),
                navy: cssColor("--brand-navy", "#1F2067"),
                gold: cssColor("--brand-gold", "#C4961C"),
                info: "#00CFE8",
                muted: dark ? "#676d7d" : "#b8c2cc",
                grid: dark ? "#414561" : "#eef0f4",
                text: dark ? "#c2c6dc" : "#6e6b7b",
                mode: dark ? "dark" : "light",
            }
        },
        // Categorical charts (e.g. allowance by type)
        // Categorical colors in a fixed order, checked with the dataviz palette validator (lightness band,
        // color-blind and normal-vision separation of neighbours, incl. the donut's wrap-around) for each mode's
        // card. Six slots: charts show at most six categories, the rest fold into "Other" (tone.muted).
        palette() {
            return this.tone.mode === "dark"
                ? ["#00A953", "#636DD7", "#D16E00", "#009FB5", "#B18400", "#9B59D0"]
                : ["#28C76F", "#3F42A8", "#F08A24", "#00AFC6", "#C4961C", "#9B59D0"]
        },
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
        // Blocks this role may see (sent by the API); everything while the first answer is loading
        access() {
            const all = { members: true, income: true, expense: true, balance: true, incentives: true, payments: true, requests: true }
            return (this.summary && this.summary.access) || all
        },
        // Member charts and the request card share their row: two or three columns
        memberColWidth() {
            return this.access.members && this.access.requests ? 'lg:w-1/3' : 'lg:w-1/2'
        },
        // Each chart shows an empty state instead of a blank chart when it has nothing to draw
        hasMonthly() {
            return this.trend.income.some(v => v) || this.trend.expense.some(v => v)
        },
        hasPayables() {
            return this.payment.paid + this.payment.unpaid > 0 || this.payment.unpaidAmount > 0
        },
        memberTotal() {
            return this.memberStatus.reduce((total, s) => total + s.value, 0)
        },
        hasGrowth() {
            return ((this.summary && this.summary.officerStatisticSeries) || []).some(v => v)
        },
        // Requests waiting for a decision (in review / in progress) and approved, all time (App\Models\Dashboard::getRequestInfo)
        requests() {
            const r = (this.summary && this.summary.requestInfo) || {}
            return { pending: toNumber(r.pending_request), approved: toNumber(r.completed_request) }
        },
        // KPI cards share the row evenly whatever their number
        kpiWidth() {
            return { 1: 'xl:w-full', 2: 'xl:w-1/2', 3: 'xl:w-1/3' }[this.kpiCards.length] || 'xl:w-1/4'
        },
        // Where each figure leads: the list filtered to exactly the records it counts (App\Services\ListLink
        // mirrors each dashboard query), so the list shows the same number. null when the role can't open that list.
        targets() {
            const p = this.period()
            return {
                income: this.to("payments", { status: "paid", ...p }, this.periodLabel(this.$t("dash_total_income"), p)),
                expense: this.to("allowances", p, this.periodLabel(this.$t("dash_total_expense"), p)),
                payables: this.to("payments", p, this.periodLabel(this.$t("dash_member_payments"), p)),
                paid: this.to("members", { payment: "paid", ...p }, this.periodLabel(this.$t("dash_paid"), p)),
                unpaid: this.to("members", { payment: "unpaid", ...p }, this.periodLabel(this.$t("dash_unpaid"), p)),
                unpaidAmount: this.to("payments", { status: "unpaid", ...p }, this.periodLabel(this.$t("dash_unpaid_amount"), p)),
            }
        },
        kpiCards() {
            const s = this.summary
            if (!s) return [1, 2, 3, 4].map(key => ({ key }))
            const pick = (obj, khr, usd) => obj ? toNumber(this.isKhr ? obj[khr] : obj[usd]) : 0 // block not sent for this role
            const members = s.totalMember || {}
            return [
                {
                    key: "members", label: this.$t("dash_total_members"), icon: "UsersIcon", color: this.tone.navy,
                    value: this.number(members.totalMembers),
                    sub: `${this.$t("dash_male")} ${this.number(members.totalMales)} · ${this.$t("dash_female")} ${this.number(members.totalFemales)}`,
                    trend: s.officerStatisticSeries || [], to: this.links.members,
                },
                {
                    key: "income", label: this.$t("dash_total_income"), icon: "TrendingUpIcon", color: this.tone.success,
                    value: this.money(pick(s.totalIncome, "total_in_khr", "total_in_usd")),
                    sub: this.$t("dash_total_income_sub"), trend: this.trend.income, to: this.targets.income,
                },
                {
                    key: "expense", label: this.$t("dash_total_expense"), icon: "TrendingDownIcon", color: this.tone.danger,
                    value: this.money(pick(s.totalExpense, "total_in_khr", "total_in_usd")),
                    sub: this.$t("dash_total_expense_sub"), trend: this.trend.expense, to: this.targets.expense,
                },
                {
                    key: "balance", label: this.$t("dash_cash_balance"), icon: "DollarSignIcon", color: this.tone.warning,
                    value: this.money(pick(s.totalRemainingBalance, "remaining_balance_khr", "remaining_balance_usd")),
                    sub: this.$t("dash_cash_balance_sub"), trend: this.trend.balance, to: this.links.balance,
                },
            ].filter(card => this.access[card.key])
        },
        // Long ranges (all time) would draw hundreds of thin monthly bars: past two years, one bar per year
        byYear() {
            return this.trend.months.length > 24
        },
        // The bars of the income / expense chart: one per month, or per year (byYear), with the months each covers
        bars() {
            const multiYear = new Set(this.trend.months.map(m => m.slice(0, 4))).size > 1
            const names = MONTHS[this.$i18n.locale] || MONTHS.en
            const bars = []
            this.trend.months.forEach((month, i) => {
                const label = this.byYear ? month.slice(0, 4) : names[Number(month.slice(5, 7)) - 1] + (multiYear ? ` ${month.slice(2, 4)}` : "")
                let bar = bars[bars.length - 1]
                if (!bar || bar.label !== label) bars.push((bar = { label, first: month, income: 0, expense: 0 }))
                bar.last = month
                bar.income += this.trend.income[i]
                bar.expense += this.trend.expense[i]
            })
            return bars
        },
        monthlySeries() {
            return [
                { name: this.$t("dash_income"), data: this.bars.map(b => b.income) },
                { name: this.$t("dash_expense"), data: this.bars.map(b => b.expense) },
            ]
        },
        monthlyOptions() {
            return {
                chart: {
                    foreColor: this.tone.text, toolbar: { show: false }, fontFamily: "inherit",
                    // A bar opens the income (payments) or expense (benefits) of its month or year
                    events: { dataPointSelection: (e, chart, { seriesIndex, dataPointIndex }) => this.go(this.barTarget(seriesIndex, this.bars[dataPointIndex])) },
                },
                colors: [this.tone.success, this.tone.danger],
                plotOptions: { bar: { columnWidth: "45%", borderRadius: 4 } },
                dataLabels: { enabled: false },
                legend: { position: "top", horizontalAlign: "right" },
                grid: { borderColor: this.tone.grid, strokeDashArray: 4 },
                xaxis: { categories: this.bars.map(b => b.label), axisBorder: { show: false }, axisTicks: { show: false } },
                yaxis: { labels: { formatter: this.compact } },
                tooltip: { theme: this.tone.mode, y: { formatter: this.money } },
            }
        },
        quarters() {
            const result = []
            this.trend.months.forEach((month, i) => {
                const label = `Q${Math.ceil(Number(month.slice(5, 7)) / 3)} ${month.slice(0, 4)}`
                let q = result.find(r => r.label === label)
                if (!q) result.push((q = { label, income: 0, expense: 0, first: month }))
                q.last = month
                q.income += this.trend.income[i]
                q.expense += this.trend.expense[i]
            })
            return result.map(q => {
                const p = this.period(`${q.first}-01`, monthEnd(q.last))
                return {
                    ...q,
                    incomeTo: this.to("payments", { status: "paid", ...p }, this.periodLabel(`${this.$t("dash_income")} ${q.label}`, p)),
                    expenseTo: this.to("allowances", p, this.periodLabel(`${this.$t("dash_expense")} ${q.label}`, p)),
                }
            })
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
                chart: { foreColor: this.tone.text, fontFamily: "inherit", sparkline: { enabled: true } },
                colors: [this.tone.success],
                plotOptions: {
                    radialBar: {
                        hollow: { size: "68%" },
                        track: { background: this.tone.grid },
                        dataLabels: {
                            name: { show: true, offsetY: 22, color: this.tone.text, fontSize: "13px" },
                            value: { offsetY: -12, fontSize: "34px", fontWeight: 600, formatter: v => `${v}%` },
                        },
                    },
                },
                fill: { type: "gradient", gradient: { shade: "light", type: "horizontal", gradientToColors: [this.tone.primary], stops: [0, 100] } },
                stroke: { lineCap: "round" },
                labels: [this.$t("dash_paid")],
            }
        },
        memberStatus() {
            const m = (this.summary && this.summary.totalMember) || {}
            return [
                { label: this.$t("dash_status_active"), value: toNumber(m.activeMembers), status: MEMBER_STATUS.active },
                { label: this.$t("dash_status_inactive"), value: toNumber(m.inactiveMembers), status: MEMBER_STATUS.inactive },
                { label: this.$t("dash_status_retired"), value: toNumber(m.retiredMembers), status: MEMBER_STATUS.retired },
                { label: this.$t("dash_status_deceased"), value: toNumber(m.deadMembers), status: MEMBER_STATUS.dead },
            ].map(s => ({ ...s, to: this.to("members", { status: s.status }, `${this.$t("dash_member_status")} · ${s.label}`) }))
        },
        memberStatusOptions() {
            return {
                chart: { foreColor: this.tone.text, fontFamily: "inherit", events: { dataPointSelection: (e, chart, { dataPointIndex }) => this.go((this.memberStatus[dataPointIndex] || {}).to) } },
                labels: this.memberStatus.map(s => s.label),
                colors: [this.tone.success, this.tone.warning, this.tone.info, this.tone.muted],
                legend: { position: "bottom" },
                dataLabels: { enabled: false },
                stroke: { width: 2 },
                plotOptions: {
                    pie: {
                        donut: {
                            size: "70%",
                            labels: { show: true, total: { show: true, label: this.$t("dash_total"), formatter: () => this.number(this.memberStatus.reduce((a, s) => a + s.value, 0)) } },
                        },
                    },
                },
                tooltip: { theme: this.tone.mode, y: { formatter: v => this.$t("dash_people", { n: this.number(v) }) } },
            }
        },
        growthOptions() {
            return {
                chart: {
                    foreColor: this.tone.text, toolbar: { show: false }, fontFamily: "inherit",
                    events: {
                        dataPointSelection: (e, chart, { dataPointIndex }) => {
                            const year = ((this.summary && this.summary.officerStatisticYears) || [])[dataPointIndex]
                            if (year) this.go(this.to("members", { registered: year }, `${this.$t("dash_new_members")} ${year}`))
                        },
                    },
                },
                colors: [this.tone.primary],
                plotOptions: { bar: { columnWidth: "45%", borderRadius: 4, dataLabels: { position: "top" } } },
                dataLabels: { enabled: true, offsetY: -20, style: { colors: [this.tone.text], fontWeight: 500 } },
                grid: { borderColor: this.tone.grid, strokeDashArray: 4 },
                xaxis: { categories: (this.summary && this.summary.officerStatisticYears) || [], axisBorder: { show: false }, axisTicks: { show: false } },
                yaxis: { show: false },
                tooltip: { theme: this.tone.mode, y: { formatter: v => this.$t("dash_people", { n: this.number(v) }) } },
            }
        },
        incentives() {
            const list = (this.summary && this.summary.totalIncentive) || []
            const items = list
                .map(i => ({ name: i.payment_type_name, keys: [i.payment_type_key], amount: toNumber(this.isKhr ? i.total_amount_khr : i.total_amount_usd) }))
                .filter(i => i.amount > 0)
                .sort((a, b) => b.amount - a.amount)
            // At most six slices: past that, the five largest stay and the rest fold into "Other"
            const max = this.palette.length
            const shown = items.length > max
                ? [...items.slice(0, max - 1), {
                    name: this.$t("other"), other: true,
                    keys: items.slice(max - 1).flatMap(i => i.keys),
                    amount: items.slice(max - 1).reduce((sum, i) => sum + i.amount, 0),
                }]
                : items
            const total = shown.reduce((sum, i) => sum + i.amount, 0)
            const p = this.period()
            return shown.map((item, i) => ({
                ...item,
                color: item.other ? this.tone.muted : this.palette[i],
                share: total ? Math.round((item.amount / total) * 100) : 0,
                to: this.to("allowances", { ...p, type: item.keys }, this.periodLabel(item.name, p)),
            }))
        },
        incentiveOptions() {
            return {
                chart: { foreColor: this.tone.text, fontFamily: "inherit", events: { dataPointSelection: (e, chart, { dataPointIndex }) => this.go((this.incentives[dataPointIndex] || {}).to) } },
                labels: this.incentives.map(i => i.name),
                colors: this.incentives.map(i => i.color),
                legend: { show: false },
                dataLabels: { enabled: false },
                stroke: { width: 2, colors: [this.tone.mode === "dark" ? "#10163A" : "#ffffff"] }, // 2px gap in the card's color
                plotOptions: {
                    pie: {
                        donut: {
                            size: "68%",
                            // The total in the middle; a hovered slice shows its own name and amount there
                            labels: {
                                show: true,
                                name: { color: this.tone.text, fontSize: "13px" },
                                value: { color: this.tone.mode === "dark" ? "#EBEEFD" : "#2c2c2c", fontSize: "18px", fontWeight: 700, formatter: v => this.money(toNumber(v)) },
                                total: { show: true, label: this.$t("dash_total"), color: this.tone.text, fontSize: "13px", formatter: () => this.money(this.incentives.reduce((sum, i) => sum + i.amount, 0)) },
                            },
                        },
                    },
                },
                tooltip: { theme: this.tone.mode, y: { formatter: this.money } },
            }
        },
        entityIncome() {
            const data = (this.summary && this.summary.incomeByDepartments) || {}
            const names = data.departments || []
            const ids = data.ids || []
            return (data.incomes || [])
                .map((amount, i) => ({ id: ids[i], name: names[i] || "-", amount: toNumber(amount) }))
                .sort((a, b) => b.amount - a.amount)
                .slice(0, 10)
        },
        entityOptions() {
            return {
                chart: {
                    foreColor: this.tone.text, toolbar: { show: false }, fontFamily: "inherit",
                    events: {
                        dataPointSelection: (e, chart, { dataPointIndex }) => {
                            const entity = this.entityIncome[dataPointIndex]
                            const p = this.period()
                            if (entity) this.go(this.to("payments", { status: "paid", ...p, entity: entity.id }, this.periodLabel(entity.name, p)))
                        },
                    },
                },
                colors: [this.palette[1]], // one series: the brand navy step that reads on this mode's card
                plotOptions: { bar: { horizontal: true, barHeight: "55%", borderRadius: 4 } },
                dataLabels: { enabled: true, formatter: this.compact, style: { fontWeight: 500 } },
                grid: { borderColor: this.tone.grid, strokeDashArray: 4 },
                xaxis: { categories: this.entityIncome.map(e => e.name), labels: { formatter: this.compact } },
                // The API reports income by entity in riel only.
                tooltip: { theme: this.tone.mode, y: { formatter: v => `៛ ${Math.round(toNumber(v)).toLocaleString("en-US")}` } },
            }
        },
    },
    watch: {
        range(value, old) {
            // Cleared: back to the default range rather than a dashboard with no dates
            if (!value.start && !value.end) {
                this.dateRange = presetRange(DEFAULT_PRESET)
                return
            }
            if (value.start && value.end && (value.start !== old.start || value.end !== old.end)) this.load()
        },
    },
    created() {
        if (!localStorage.getItem("accessToken")) {
            router.push(router.currentRoute.value.query.to || "/pages/login")
            return
        }
        this.load()
    },
    mounted() {
        this.wasSidebarOpen = this.$store.state.reduceButton
        this.$store.commit("TOGGLE_REDUCE_BUTTON", true)
    },
    beforeUnmount() {
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
                this.firstDate = this.summary.firstDate || null
                this.updatedAt = new Date().toLocaleTimeString("en-GB", { hour: "2-digit", minute: "2-digit" })
            } catch (e) {
                this.error = true
            }
            this.loading = false
        },
        // Quick range buttons above the calendar (the calendar is outside this component, so built here)
        addPresets(fp) {
            const calendar = fp.calendarContainer
            if (!calendar || calendar.querySelector(".dash-presets")) return
            const bar = document.createElement("div")
            bar.className = "dash-presets"
            Object.keys(PRESETS).forEach((key) => {
                const button = document.createElement("button")
                button.type = "button"
                button.dataset.preset = key
                // Keep focus in the input so flatpickr doesn't treat the click as "outside"
                button.addEventListener("mousedown", (e) => e.preventDefault())
                button.addEventListener("click", () => {
                    fp.setDate(presetRange(key, this.firstDate), true)
                    fp.close()
                })
                bar.appendChild(button)
            })
            bar.addEventListener("scroll", () => updatePresetFades(bar), { passive: true })
            // A mouse wheel (up / down) scrolls the row sideways too
            bar.addEventListener("wheel", (e) => {
                if (Math.abs(e.deltaY) <= Math.abs(e.deltaX) || bar.scrollWidth <= bar.clientWidth) return
                e.preventDefault()
                bar.scrollLeft += e.deltaY
            }, { passive: false })
            calendar.insertBefore(bar, calendar.firstChild)
            this.markPresets(fp)
        },
        // Labels in the current language, and the quick range that matches the chosen dates marked
        // and scrolled into the middle of the row
        markPresets(fp) {
            const bar = fp.calendarContainer.querySelector(".dash-presets")
            if (!bar) return
            const [start, end] = fp.selectedDates.map(ymd)
            let active = null
            bar.querySelectorAll("button").forEach((button) => {
                const [presetStart, presetEnd] = presetRange(button.dataset.preset, this.firstDate)
                const on = presetStart === start && presetEnd === end
                button.textContent = this.$t(button.dataset.preset)
                button.classList.toggle("active", on)
                if (on) active = button
            })
            // After the calendar is laid out (it is only measurable once open)
            requestAnimationFrame(() => {
                if (active) bar.scrollLeft = active.offsetLeft - (bar.clientWidth - active.offsetWidth) / 2
                updatePresetFades(bar)
            })
        },
        go(to) {
            if (to) this.$router.push(to).catch(() => {})
        },
        // The list of `target` filtered by `link`, or null when the role can't open that list
        to(target, link = {}, label = "") {
            return this.links[target] ? linkRoute(this.links[target], link, label) : null
        },
        // The dashboard's period, or part of it (a month, a quarter), as list filter dates
        period(from = this.range.start, to = this.range.end) {
            return { from: from < this.range.start ? this.range.start : from, to: to > this.range.end ? this.range.end : to }
        },
        periodLabel(title, p) {
            return `${title} · ${this.formatDate(p.from)} – ${this.formatDate(p.to)}`
        },
        // A bar of the income / expense chart: series 0 is income (paid payments), 1 is expense (benefits)
        barTarget(series, bar) {
            if (!bar || series < 0) return null
            const p = this.period(`${bar.first}-01`, monthEnd(bar.last))
            return series === 1
                ? this.to("allowances", p, this.periodLabel(this.$t("dash_expense"), p))
                : this.to("payments", { status: "paid", ...p }, this.periodLabel(this.$t("dash_income"), p))
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
                chart: { foreColor: this.tone.text, sparkline: { enabled: true }, fontFamily: "inherit" },
                colors: [color],
                stroke: { curve: "smooth", width: 2.5 },
                fill: { type: "gradient", gradient: { opacityFrom: 0.45, opacityTo: 0.05 } },
                tooltip: { theme: this.tone.mode, enabled: false },
            }
        },
    },
}
</script>

<style scoped>
.dashboard-toolbar {
    display: flex;
    flex-wrap: wrap;
    align-items: flex-end;
    justify-content: space-between;
    margin-bottom: 1rem;
}

/* The visible input is flatpickr's copy (outside this component's scope): wide enough for the whole range */
.dashboard-toolbar :deep(.dashboard-date) {
    width: 290px;
    max-width: 100%;
}

/* Segmented switch: the white pill slides to the chosen currency (v-tab-pill) */
.currency-switch {
    --pill-bg: #fff;
    --pill-radius: calc(var(--btn-radius) - 2px);
    --pill-shadow: 0 2px 6px rgba(0, 0, 0, .1);
    display: inline-flex;
    height: var(--btn-height);
    padding: 3px;
    border-radius: var(--btn-radius);
    background: #eef0f4;

    button {
        border: 0;
        background: transparent;
        padding: 0 12px;
        border-radius: calc(var(--btn-radius) - 2px);
        cursor: pointer;
        color: #626262;
        font-family: inherit;
        font-size: var(--btn-font-size);
        transition: color var(--tab-tap) ease;

        &.active {
            color: rgba(var(--vs-primary), 1);
            font-weight: 600;
        }
    }
}

.toolbar-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: var(--btn-height);
    height: var(--btn-height);
    border: 1px solid var(--input-border, #d8d9e3);
    border-radius: var(--btn-radius);
    background: #fff;
    color: #5e6078;
    cursor: pointer;

    &:hover {
        color: rgb(var(--vs-primary));
        border-color: rgba(var(--vs-primary), .5);
        background: rgba(var(--vs-primary), .06);
    }

    &.spinning svg {
        animation: dashboard-spin 0.8s linear infinite;
    }
}

.kpi-card {
    position: relative;
    display: block;
    color: inherit;
    height: 100%;
    min-height: 170px;
    padding: 1.25rem 1.25rem 0;
    border-radius: 1.25rem;
    background: #fff;
    box-shadow: 0 4px 24px rgba(34, 41, 47, 0.1);
    overflow: hidden;
    transition: transform 0.2s ease, box-shadow 0.2s ease;

    &.is-link:hover,
    &.is-link:focus-visible {
        transform: translateY(-4px);
        box-shadow: 0 12px 32px rgba(34, 41, 47, 0.16);

        .kpi-card__more {
            opacity: 1;
            transform: translateX(0);
        }
    }

    &.is-link:focus-visible {
        outline: 2px solid rgba(var(--vs-primary), .6);
        outline-offset: 2px;
    }

    &__more {
        position: absolute;
        right: 1.25rem;
        bottom: 0.85rem;
        display: inline-flex;
        align-items: center;
        gap: 0.25rem;
        padding: 0.2rem 0.6rem;
        border-radius: 999px;
        background: rgba(255, 255, 255, 0.85);
        font-size: 0.8rem;
        font-weight: 600;
        opacity: 0;
        transform: translateX(-6px);
        transition: opacity 0.2s ease, transform 0.2s ease;
        pointer-events: none;
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

.card-link {
    display: inline-flex;
    align-items: center;
    gap: 0.15rem;
    padding: 0.3rem 0.5rem 0.3rem 0.75rem;
    border-radius: 999px;
    font-size: 0.85rem;
    font-weight: 600;
    color: rgb(var(--vs-primary));
    background: rgba(var(--vs-primary), .08);
    transition: background 0.2s ease, gap 0.2s ease;

    &:hover {
        gap: 0.4rem;
        background: rgba(var(--vs-primary), .16);
    }
}

.is-clickable ::v-deep svg {
    cursor: pointer;
}

.stat-link {
    display: block;
    padding: 0.5rem 0;
    border-radius: 0.75rem;
    transition: background 0.2s ease;

    &[href]:hover {
        background: rgba(var(--vs-primary), .06);
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

        a:hover {
            text-decoration: underline;
        }
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
        border-top: 1px solid #eef0f4;
    }

    .legend-row {
        display: flex;
        justify-content: space-between;
        padding: 0.6rem 0.5rem;
        margin: 0 -0.5rem;
        border-radius: 0.5rem;
        color: inherit;
        transition: background 0.2s ease;
    }

    a.legend-row:hover {
        background: rgba(var(--vs-primary), .06);
    }
}

.legend-value {
    display: flex;
    align-items: baseline;
    gap: 0.5rem;
    white-space: nowrap;

    small {
        min-width: 2.5rem;
        text-align: right;
        color: #8a8d93;
    }
}

.legend-dot {
    flex-shrink: 0;
    width: 0.7rem;
    height: 0.7rem;
    border-radius: 50%;
    margin-right: 0.5rem;
}

/* A card with nothing to show: soft icon, message, and a hint when another date range may help */
.empty-state {
    height: 260px;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 0 1rem;
    text-align: center;
    color: #b0b5c1;

    &__icon {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 64px;
        height: 64px;
        border-radius: 50%;
        background: rgba(var(--vs-primary), .08);
        color: rgb(var(--vs-primary));
    }

    &__title {
        margin-top: 0.9rem;
        font-weight: 600;
        color: #626262;
    }

    small {
        margin-top: 0.25rem;
    }
}

.request-stats {
    height: 300px;
    display: flex;
    flex-direction: column;
    justify-content: center;
    gap: 0.75rem;
}

.request-stat {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1rem;
    border: 1px solid #eef0f4;

    h3 {
        margin: 0;
    }

    &__icon {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 48px;
        height: 48px;
        border-radius: 50%;

        &.is-warning {
            background: rgba(255, 159, 67, .12);
            color: #FF9F43;
        }

        &.is-success {
            background: rgba(40, 199, 111, .12);
            color: #28C76F;
        }
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

/* Dark mode: same layout, dark surfaces and light text */
.theme-dark {
    .currency-switch { background: #1e2340; }
    .currency-switch button { color: #c2c6dc; }
    .currency-switch { --pill-bg: #262c49; }
    .currency-switch button.active { color: #fff; }
    .toolbar-btn { background: #262c49; color: #c2c6dc; border-color: #414561; }
    .kpi-card { background: #262c49; }
    .kpi-card__label,
    .kpi-card__sub { color: #c2c6dc; }
    .kpi-card__value { color: #fff; }
    .kpi-card__more { background: rgba(38, 44, 73, 0.9); }
    .stat-link[href]:hover,
    .legend-list a.legend-row:hover { background: rgba(255, 255, 255, 0.05); }
    .quarter-strip__item { background: #1e2340; }
    .payment-split,
    .legend-list li { border-color: #414561; }
    .empty-state { color: #676d7d; }
    .empty-state__title { color: #c2c6dc; }
    .request-stat { border-color: #414561; }
    .skeleton,
    .skeleton-block { background: linear-gradient(90deg, #262c49 25%, #2f365a 37%, #262c49 63%); background-size: 400% 100%; }
}
</style>

<style>
/* Quick ranges on the dashboard's date picker (the calendar is added to the page body):
   one row that scrolls sideways, edges fading where more buttons are hidden */
.flatpickr-calendar .dash-presets {
    --fade-start: 0px;
    --fade-end: 0px;
    position: relative;
    display: flex;
    gap: 6px;
    padding: 12px 12px 6px;
    overflow-x: auto;
    overflow-y: hidden;
    overscroll-behavior-x: contain;
    scroll-snap-type: x proximity;
    scroll-padding: 0 12px;
    -webkit-mask-image: linear-gradient(to right, transparent 0, #000 var(--fade-start), #000 calc(100% - var(--fade-end)), transparent 100%);
    mask-image: linear-gradient(to right, transparent 0, #000 var(--fade-start), #000 calc(100% - var(--fade-end)), transparent 100%);
    /* Thin scrollbar, only noticeable on hover (same as the tabs) */
    scrollbar-width: thin;
    scrollbar-color: transparent transparent;
}
.flatpickr-calendar .dash-presets.fade-start { --fade-start: 24px; }
.flatpickr-calendar .dash-presets.fade-end { --fade-end: 24px; }
.flatpickr-calendar .dash-presets:hover { scrollbar-color: rgba(110, 112, 145, .35) transparent; }
.flatpickr-calendar .dash-presets::-webkit-scrollbar { height: 4px; }
.flatpickr-calendar .dash-presets::-webkit-scrollbar-thumb { border-radius: 4px; background: transparent; }
.flatpickr-calendar .dash-presets:hover::-webkit-scrollbar-thumb { background: rgba(110, 112, 145, .35); }
.flatpickr-calendar .dash-presets button {
    flex: none;
    white-space: nowrap;
    scroll-snap-align: start;
    padding: 5px 12px;
    font-family: inherit;
    font-size: 13px;
    line-height: 1.4;
    color: #4b4d5c;
    background: #fff;
    border: 1px solid #e3e5ea;
    border-radius: 999px;
    cursor: pointer;
    transition: all .15s ease;
}
.flatpickr-calendar .dash-presets button:hover {
    color: rgb(var(--vs-primary));
    border-color: rgba(var(--vs-primary), .5);
    background: rgba(var(--vs-primary), .06);
}
.flatpickr-calendar .dash-presets button.active {
    color: #fff;
    background: rgb(var(--vs-primary));
    border-color: rgb(var(--vs-primary));
}
.theme-dark .flatpickr-calendar .dash-presets button:not(.active) {
    color: #c2c6dc;
    background: #10163a;
    border-color: #414561;
}
</style>

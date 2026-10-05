<template>
    <div class="dt-list d-audit">
        <!-- Toolbar: same as every list page -->
        <div class="dt-toolbar">
            <div class="dt-toolbar__title">
                <h3>{{ $t('setting_audit') }}</h3>
                <p>{{ $t('dt_records', { n: number(total) }) }}</p>
            </div>
            <div class="dt-toolbar__actions">
                <button type="button" class="dt-btn dt-btn--icon" :title="$t('audit_refresh')" :disabled="loading" @click="load">
                    <feather-icon icon="RefreshCwIcon" svgClasses="h-4 w-4" :class="{ 'd-audit__spin': loading }" />
                </button>
            </div>
        </div>

        <!-- Filters -->
        <div class="dt-filters">
            <div class="dt-cell dt-search">
                <span class="dt-search__icon"><feather-icon icon="SearchIcon" svgClasses="h-4 w-4" /></span>
                <input v-model.trim="filters.keyword" type="text" :placeholder="$t('audit_search')" @input="searchSoon">
            </div>
            <div class="dt-cell">
                <v-select v-model="filters.type" :options="typeOptions" :placeholder="$t('audit_all_records')"
                    :dir="$vs.rtl ? 'rtl' : 'ltr'" @input="search" />
            </div>
            <div class="dt-cell">
                <flat-pickr v-model="filters.dates" :placeholder="$t('audit_date_range')"
                    :config="{ mode: 'range' }" @on-close="search" />
            </div>
        </div>

        <!-- Action chips double as the event filter -->
        <div class="dt-chipbar">
            <div class="dt-chipbar__chips">
                <button v-for="ev in eventFilters" :key="ev.key" type="button" class="dt-chip"
                    :class="{ 'is-active': filters.event === ev.key }" :style="{ '--tone': ev.color }" @click="setEvent(ev.key)">
                    <span v-if="ev.key" class="dt-chip__dot" />
                    {{ ev.label }}
                </button>
            </div>
            <div class="dt-chipbar__summary">
                <button v-if="hasFilters" type="button" class="dt-chip dt-chip--clear" @click="clearFilters">
                    <feather-icon icon="XIcon" svgClasses="h-3 w-3" />
                    <span>{{ $t('audit_clear') }}</span>
                </button>
            </div>
        </div>

        <!-- Log -->
        <div class="dt-table-wrap">
            <table class="dt-table d-audit__table">
                <thead>
                    <tr>
                        <th class="d-audit__col-event">{{ $t('audit_action') }}</th>
                        <th>{{ $t('audit_record') }}</th>
                        <th>{{ $t('audit_user') }}</th>
                        <th>{{ $t('audit_changes') }}</th>
                        <th class="d-audit__col-time">{{ $t('audit_time') }}</th>
                        <th class="d-audit__col-toggle"></th>
                    </tr>
                </thead>

                <tbody v-if="loading && !rows.length">
                    <tr v-for="n in 6" :key="'s' + n" class="dt-skeleton">
                        <td v-for="c in 6" :key="c"><span /></td>
                    </tr>
                </tbody>

                <tbody v-else-if="rows.length" :class="{ 'is-loading': loading }">
                    <template v-for="row in rows">
                        <tr :key="row.id" class="is-clickable d-audit__row" :class="{ 'is-open': openId === row.id }"
                            @click="toggle(row.id)">
                            <td class="d-audit__col-event">
                                <span class="dt-badge dt-badge--tone" :style="{ '--tone': eventInfo(row.event).color }">
                                    {{ eventInfo(row.event).label }}
                                </span>
                            </td>
                            <td class="d-audit__col-record">
                                <div class="d-audit__record">
                                    <strong>{{ modelName(row.auditable_type) }}</strong>
                                    <span>#{{ row.auditable_id }}</span>
                                </div>
                            </td>
                            <td class="d-audit__col-user">
                                <div class="d-audit__user">
                                    <span class="d-audit__avatar">{{ initial(row) }}</span>
                                    <span>{{ userName(row) }}</span>
                                </div>
                            </td>
                            <td class="d-audit__col-fields">
                                <span class="d-audit__fields" :title="changedFields(row).join(', ')">
                                    <template v-if="changedFields(row).length">
                                        <b>{{ changedFields(row).length }}</b>
                                        {{ changedFields(row).slice(0, 3).join(', ') }}<template v-if="changedFields(row).length > 3">…</template>
                                    </template>
                                    <template v-else>—</template>
                                </span>
                            </td>
                            <td class="d-audit__col-time">
                                <span class="d-audit__time" :title="row.created_at">{{ relativeTime(row.created_at) }}</span>
                            </td>
                            <td class="d-audit__col-toggle">
                                <span class="d-audit__caret"><feather-icon icon="ChevronDownIcon" svgClasses="h-4 w-4" /></span>
                            </td>
                        </tr>

                        <!-- Detail: field-by-field before → after -->
                        <tr v-if="openId === row.id" :key="row.id + '-detail'" class="d-audit__detail-row">
                            <td colspan="6">
                                <div class="d-audit__detail">
                                    <div v-if="diff(row).length" class="d-audit__diff">
                                        <div class="d-audit__diff-head">
                                            <span>{{ $t('audit_field') }}</span>
                                            <span>{{ $t('audit_before') }}</span>
                                            <span>{{ $t('audit_after') }}</span>
                                        </div>
                                        <div v-for="item in diff(row)" :key="item.field" class="d-audit__diff-row">
                                            <span class="d-audit__diff-field">{{ item.field }}</span>
                                            <span class="d-audit__diff-old">{{ item.old }}</span>
                                            <span class="d-audit__diff-new">{{ item.new }}</span>
                                        </div>
                                    </div>
                                    <p v-else class="d-audit__no-diff">{{ $t('audit_no_changes') }}</p>

                                    <dl class="d-audit__meta">
                                        <div><dt>{{ $t('audit_time') }}</dt><dd>{{ row.created_at || '—' }}</dd></div>
                                        <div><dt>IP</dt><dd>{{ row.ip_address || '—' }}</dd></div>
                                        <div class="is-wide"><dt>URL</dt><dd>{{ row.url || '—' }}</dd></div>
                                        <div class="is-wide"><dt>{{ $t('audit_device') }}</dt><dd>{{ row.user_agent || '—' }}</dd></div>
                                    </dl>
                                </div>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>

            <div v-if="!loading && !rows.length" class="dt-empty">
                <span class="dt-empty__icon"><feather-icon icon="ActivityIcon" svgClasses="h-6 w-6" /></span>
                <strong>{{ $t('audit_empty') }}</strong>
                <span>{{ hasFilters ? $t('audit_empty_filtered') : $t('audit_empty_hint') }}</span>
            </div>
        </div>

        <!-- Footer: same as every list page -->
        <div v-if="total" class="dt-footer">
            <span class="dt-footer__info">{{ $t('dt_showing', { from: number(showing.from), to: number(showing.to), total: number(total) }) }}</span>
            <vs-pagination v-if="pages > 1" :total="pages" v-model="page" :max="7" />
        </div>
    </div>
</template>

<script>
import axios from "@/axios.js";
import apiConfig from "@/apiConfig.js";
import vSelect from "vue-select";

// Colour (r, g, b) and icon per audit event
const EVENTS = {
    created: { color: '40, 199, 111', icon: 'PlusIcon' },
    updated: { color: '0, 143, 251', icon: 'Edit2Icon' },
    deleted: { color: '234, 84, 85', icon: 'Trash2Icon' },
    restored: { color: '255, 159, 67', icon: 'RotateCcwIcon' },
}
// Bookkeeping columns that don't help reading a change
const HIDDEN_FIELDS = ['created_at', 'updated_at', 'created_by', 'updated_by', 'modified_by', 'id']
// Secrets are listed as changed but never displayed
const SECRET_FIELDS = /password|token|secret|api_key/i

function parse(values) {
    if (!values) return {}
    if (typeof values === 'object') return values
    try {
        const parsed = JSON.parse(values)
        return parsed && typeof parsed === 'object' ? parsed : {}
    } catch (e) {
        return {}
    }
}

function show(value) {
    if (value === null || value === undefined || value === '') return '—'
    if (typeof value === 'object') return JSON.stringify(value)
    return String(value)
}

export default {
    components: { 'v-select': vSelect },
    data() {
        return {
            api: apiConfig._apiAudit,
            rows: [],
            total: 0,
            limit: 10,
            page: 1,
            loading: false,
            openId: null,
            types: [],
            filters: { keyword: '', event: '', type: null, dates: '' },
            searchTimer: null,
        }
    },
    computed: {
        showing() {
            if (!this.total) return { from: 0, to: 0 }
            const from = (this.page - 1) * this.limit + 1
            return { from, to: Math.min(this.total, from + this.rows.length - 1) }
        },
        pages() {
            return Math.max(1, Math.ceil(this.total / (this.limit || 10)))
        },
        typeOptions() {
            return this.types.map(type => ({ label: this.modelName(type), value: type }))
        },
        eventFilters() {
            return [{ key: '', label: this.$t('audit_all'), color: 'var(--vs-primary)' }]
                .concat(Object.keys(EVENTS).map(key => ({ key, label: this.$t('audit_event_' + key), color: EVENTS[key].color })))
        },
        hasFilters() {
            const f = this.filters
            return !!(f.keyword || f.event || f.type || f.dates)
        },
        // Search fields in the shape the API expects: [{ field: value }, …]
        searchFields() {
            const f = this.filters
            const fields = []
            if (f.keyword) fields.push({ keyword: f.keyword })
            if (f.event) fields.push({ event: f.event })
            if (f.type) fields.push({ auditable_type: f.type.value })
            // Range text uses the locale's separator ("to" / "ដល់"): pick the dates out directly
            const [from, to] = String(f.dates || '').match(/\d{4}-\d{2}-\d{2}/g) || []
            if (from) fields.push({ date_from: from })
            if (from) fields.push({ date_to: to || from })
            return fields
        },
    },
    watch: {
        page() {
            this.load()
        },
    },
    methods: {
        load() {
            this.loading = true
            this.openId = null
            const params = { sort: 'id', order: 'desc', page_number: this.page }
            if (this.searchFields.length) params.search_field = this.searchFields
            return axios.post(this.api + '/search', params)
                .then(({ data }) => {
                    this.rows = data.data || []
                    this.total = Number(data.total) || 0
                    this.limit = Number(data.limit) || this.limit
                    if (Array.isArray(data.types)) this.types = data.types.filter(Boolean)
                })
                .catch(() => {
                    this.$vs.notify({
                        title: 'Message',
                        text: 'Error, while trying to do action or no permission allow!',
                        iconPack: 'feather',
                        icon: 'icon-alert-circle',
                        color: 'danger',
                    })
                })
                .finally(() => {
                    this.loading = false
                })
        },
        search() {
            clearTimeout(this.searchTimer)
            if (this.page !== 1) this.page = 1 // the page watcher reloads
            else this.load()
        },
        searchSoon() {
            clearTimeout(this.searchTimer)
            this.searchTimer = setTimeout(this.search, 400)
        },
        setEvent(key) {
            this.filters.event = key
            this.search()
        },
        clearFilters() {
            this.filters = { keyword: '', event: '', type: null, dates: '' }
            this.search()
        },
        toggle(id) {
            this.openId = this.openId === id ? null : id
        },
        eventInfo(event) {
            const info = EVENTS[event] || { color: '92, 99, 120', icon: 'ActivityIcon' }
            const key = 'audit_event_' + event
            return { ...info, label: this.$te(key) ? this.$t(key) : (event || '—') }
        },
        modelName(type) {
            return String(type || '').split('\\').pop() || '—'
        },
        userName(row) {
            return (row.user && (row.user.username || row.user.name)) || (row.user_id ? '#' + row.user_id : this.$t('audit_system'))
        },
        initial(row) {
            return this.userName(row).replace('#', '').charAt(0).toUpperCase() || '?'
        },
        changedFields(row) {
            const keys = new Set([...Object.keys(parse(row.old_values)), ...Object.keys(parse(row.new_values))])
            return [...keys].filter(key => !HIDDEN_FIELDS.includes(key))
        },
        diff(row) {
            const before = parse(row.old_values)
            const after = parse(row.new_values)
            return this.changedFields(row).map(field => SECRET_FIELDS.test(field)
                ? { field, old: before[field] ? '••••••' : '—', new: after[field] ? '••••••' : '—' }
                : { field, old: show(before[field]), new: show(after[field]) })
        },
        // "5 minutes ago" for the last four weeks, the date itself after that
        relativeTime(value) {
            if (!value) return '—'
            const date = new Date(String(value).replace(' ', 'T'))
            if (isNaN(date) || typeof Intl === 'undefined' || !Intl.RelativeTimeFormat) return String(value).slice(0, 16)
            const seconds = Math.max(0, Math.round((Date.now() - date.getTime()) / 1000))
            if (seconds > 4 * 604800) return String(value).slice(0, 16)
            const rtf = new Intl.RelativeTimeFormat(this.$i18n.locale === 'en' ? 'en' : 'km', { numeric: 'auto' })
            if (seconds < 60) return rtf.format(-seconds, 'second')
            if (seconds < 3600) return rtf.format(-Math.floor(seconds / 60), 'minute')
            if (seconds < 86400) return rtf.format(-Math.floor(seconds / 3600), 'hour')
            if (seconds < 604800) return rtf.format(-Math.floor(seconds / 86400), 'day')
            return rtf.format(-Math.floor(seconds / 604800), 'week')
        },
        number(value) {
            return Number(value || 0).toLocaleString('en-US')
        },
    },
    created() {
        this.load()
    },
}
</script>

<style lang="scss">
// Audit-specific cells; the toolbar, filters, chips, table, badges, empty state and footer
// come from the shared list standard (LIST PAGES in resources/sass/custom.scss).
.d-audit {
    &__table {
        min-width: 720px;
    }

    &__col-event {
        width: 120px;
    }

    &__col-time {
        width: 150px;
        white-space: nowrap;
    }

    &__col-toggle {
        width: 40px;
        text-align: right !important;
    }

    &__caret {
        display: inline-flex;
        color: #b0b2bd;
        transition: transform .25s ease, color .2s ease;

        .feather-icon {
            display: flex;
        }
    }

    &__row.is-open &__caret {
        transform: rotate(180deg);
        color: rgb(var(--vs-primary));
    }

    &__spin svg {
        animation: d-audit-spin .8s linear infinite;
    }

    &__record {
        display: flex;
        align-items: baseline;
        gap: 6px;

        strong {
            font-weight: 600;
            color: #2c2e45;
        }

        span {
            font-size: .78rem;
            color: #8a8ca5;
            font-variant-numeric: tabular-nums;
        }
    }

    &__user {
        display: flex;
        align-items: center;
        gap: 8px;
        min-width: 0;
    }

    &__avatar {
        flex-shrink: 0;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 26px;
        height: 26px;
        font-size: .72rem;
        font-weight: 700;
        color: rgb(var(--vs-primary));
        border-radius: 999px;
        background: rgba(var(--vs-primary), .12);
    }

    &__fields {
        display: inline-block;
        max-width: 280px;
        font-size: .8rem;
        color: #6b6d7a;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        vertical-align: middle;

        b {
            display: inline-block;
            min-width: 20px;
            margin-right: 4px;
            padding: 0 6px;
            font-size: .72rem;
            text-align: center;
            color: #4b4d68;
            border-radius: 6px;
            background: #f1f2f5;
        }
    }

    &__time {
        font-size: .8rem;
        color: #6b6d7a;
    }

    // ---------------------------------------------------------------- Detail
    &__detail-row > td {
        padding: 0 !important;
        background: #fafbfd;
    }

    &__detail {
        display: grid;
        grid-template-columns: minmax(0, 2fr) minmax(0, 1fr);
        gap: 16px;
        padding: 4px 14px 18px;
        animation: d-audit-open .25s ease;
    }

    &__diff {
        border: 1px solid #eceef4;
        border-radius: 10px;
        background: #fff;
        overflow: hidden;
    }

    &__diff-head,
    &__diff-row {
        display: grid;
        grid-template-columns: minmax(110px, .8fr) minmax(0, 1fr) minmax(0, 1fr);
        gap: 12px;
        padding: 8px 12px;
        font-size: .8rem;
    }

    &__diff-head {
        font-size: .72rem;
        font-weight: 700;
        color: #8a8ca5;
        background: #f6f7fb;
    }

    &__diff-row + &__diff-row {
        border-top: 1px solid #f1f2f6;
    }

    &__diff-field {
        font-weight: 600;
        color: #4b4d68;
        word-break: break-word;
    }

    &__diff-old,
    &__diff-new {
        padding: 1px 6px;
        border-radius: 5px;
        word-break: break-word;
        font-variant-numeric: tabular-nums;
    }

    &__diff-old {
        color: #b4232c;
        background: rgba(234, 84, 85, .08);
        text-decoration: line-through;
        text-decoration-color: rgba(234, 84, 85, .4);
    }

    &__diff-new {
        color: #1b7a43;
        background: rgba(40, 199, 111, .1);
    }

    &__no-diff {
        padding: 16px;
        font-size: .82rem;
        color: #8a8ca5;
        border: 1px dashed #e3e5ec;
        border-radius: 10px;
    }

    &__meta {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 10px 14px;
        align-content: start;
        margin: 0;

        .is-wide {
            grid-column: 1 / -1;
        }

        dt {
            font-size: .7rem;
            font-weight: 700;
            color: #8a8ca5;
        }

        dd {
            margin: 2px 0 0;
            font-size: .8rem;
            color: #2c2e45;
            word-break: break-all;
        }
    }

    @media (max-width: 900px) {
        &__detail {
            grid-template-columns: minmax(0, 1fr);
        }
    }

    // ---------------------------------------------------------------- Phones: each log row becomes a card
    @media (max-width: 768px) {
        &__table {
            min-width: 0;

            thead {
                display: none;
            }

            tbody,
            tr,
            td {
                display: block;
            }
        }

        &__row {
            display: grid !important;
            grid-template-columns: minmax(0, 1fr) auto;
            grid-template-areas:
                "event time"
                "record record"
                "user fields";
            gap: 8px 12px;
            padding: 14px 16px;
            border-bottom: 1px solid #f1f2f6;

            > td {
                padding: 0 !important;
                border: 0 !important;
            }
        }

        &__col-event { grid-area: event; width: auto; }
        &__col-time { grid-area: time; width: auto; text-align: right; }
        &__col-record { grid-area: record; }
        &__col-user { grid-area: user; min-width: 0; }
        &__col-fields { grid-area: fields; justify-self: end; min-width: 0; }
        &__col-toggle { display: none !important; }

        &__fields {
            max-width: 160px;
        }

        .dt-skeleton {
            display: grid !important;
            grid-template-columns: 1fr 1fr;
            gap: 8px;
            padding: 14px 16px;

            td {
                padding: 0 !important;
                border: 0 !important;
            }
        }

        &__detail {
            padding: 12px 16px 16px;
        }

        &__diff-head,
        &__diff-row {
            grid-template-columns: minmax(0, 1fr) minmax(0, 1fr);

            > :first-child {
                grid-column: 1 / -1;
            }
        }

        &__meta {
            grid-template-columns: minmax(0, 1fr);
        }
    }
}

@keyframes d-audit-open {
    from { opacity: 0; transform: translateY(-4px); }
    to { opacity: 1; transform: none; }
}

@keyframes d-audit-spin {
    to { transform: rotate(360deg); }
}

.theme-dark .d-audit {
    &__record strong,
    &__meta dd {
        color: #fff;
    }

    &__diff {
        background: #10163a;
        border-color: #414561;
    }

    &__diff-head,
    &__detail-row > td {
        background: #1b2142;
    }
}
</style>

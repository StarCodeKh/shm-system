<template>
    <vx-card class="dt-list report-viewer" :style="{ '--accent': accent }">
        <!-- Toolbar -->
        <div class="dt-toolbar">
            <div class="report-viewer__title">
                <button type="button" class="dt-btn dt-btn--icon" :title="$t('report_back')" @click="$router.push('/report')">
                    <feather-icon icon="ArrowLeftIcon" svgClasses="h-4 w-4" />
                </button>
                <span v-if="report" class="report-viewer__icon"><feather-icon :icon="report.icon" svgClasses="h-5 w-5" /></span>
                <div class="dt-toolbar__title">
                    <h3>{{ report ? report.title : reportType }}</h3>
                    <p>
                        {{ total !== null ? $t('dt_records', { n: number(total) }) : '' }}
                        <template v-if="filters.length"> · {{ $t('report_filtered', { n: filters.length }) }}</template>
                    </p>
                </div>
            </div>

            <div class="dt-toolbar__actions">
                <button v-if="formAttributes.length" type="button" class="dt-btn dt-btn--icon" :class="{ 'is-active': showFilter }"
                    :title="$t('dt_filter')" @click="showFilter = !showFilter">
                    <feather-icon icon="FilterIcon" svgClasses="h-4 w-4" />
                </button>
                <button type="button" class="dt-btn dt-btn--icon" :title="$t('report_refresh')" :disabled="loading" @click="load(page)">
                    <feather-icon icon="RefreshCwIcon" svgClasses="h-4 w-4" :class="{ 'report-spin': loading }" />
                </button>
                <button type="button" class="dt-btn" :disabled="!!pdfBusy" @click="openPdf">
                    <feather-icon :icon="pdfBusy === 'view' ? 'LoaderIcon' : 'PrinterIcon'" svgClasses="h-4 w-4"
                        :class="{ 'report-spin': pdfBusy === 'view' }" />
                    <span>{{ $t('report_print') }}</span>
                </button>
                <button type="button" class="dt-btn dt-btn--primary" :disabled="!!pdfBusy" @click="downloadPdf">
                    <feather-icon :icon="pdfBusy === 'download' ? 'LoaderIcon' : 'DownloadIcon'" svgClasses="h-4 w-4"
                        :class="{ 'report-spin': pdfBusy === 'download' }" />
                    <span>{{ $t('report_download_pdf') }}</span>
                </button>
            </div>
        </div>

        <!-- Filters -->
        <d-search v-if="showFilter && formAttributes.length" :data="fieldData" :dataInfo="{}" :api="''"
            :formAttributes="formAttributes" :rowDisplay="'3grid'" @searchQuery="applyFilters" />

        <!-- Summary tiles -->
        <div v-if="summaryTiles.length" class="report-viewer__summary">
            <div v-for="tile in summaryTiles" :key="tile.label" class="report-viewer__tile">
                <span>{{ tile.label }}</span>
                <strong>{{ number(tile.value) }}</strong>
            </div>
        </div>

        <!-- Error -->
        <div v-if="error" class="dt-table-wrap">
            <div class="dt-empty">
                <span class="dt-empty__icon"><feather-icon icon="AlertTriangleIcon" svgClasses="h-6 w-6" /></span>
                <strong>{{ error }}</strong>
                <button type="button" class="dt-btn mt-3" @click="load(page)">
                    <feather-icon icon="RefreshCwIcon" svgClasses="h-4 w-4" />
                    <span>{{ $t('report_retry') }}</span>
                </button>
            </div>
        </div>

        <!-- Table -->
        <div v-else class="dt-table-wrap">
            <table class="dt-table dt-table--cards">
                <thead>
                    <tr>
                        <th class="report-viewer__no">{{ $t('no') }}</th>
                        <th v-for="col in columns" :key="col.key" :class="{ 'dt-num': numericColumns[col.key] }">{{ col.label }}</th>
                    </tr>
                </thead>

                <tbody v-if="loading && !rows.length">
                    <tr v-for="n in 8" :key="'s' + n" class="dt-skeleton">
                        <td v-for="c in (columns.length || 6) + 1" :key="c"><span /></td>
                    </tr>
                </tbody>

                <tbody v-else :class="{ 'is-loading': loading }">
                    <tr v-for="(row, i) in rows" :key="row.id || i">
                        <td class="report-viewer__no" :data-label="$t('no')">{{ (page - 1) * limit + i + 1 }}</td>
                        <td v-for="col in columns" :key="col.key" :data-label="col.label"
                            :class="{ 'dt-num': col.numeric, 'dt-date': col.date }">
                            {{ cell(row, col.key) }}
                        </td>
                    </tr>
                </tbody>
            </table>

            <div v-if="!loading && !rows.length" class="dt-empty">
                <span class="dt-empty__icon"><feather-icon icon="InboxIcon" svgClasses="h-6 w-6" /></span>
                <strong>{{ $t('report_empty') }}</strong>
                <span>{{ filters.length ? $t('report_empty_filtered') : '' }}</span>
            </div>
        </div>

        <!-- Footer -->
        <div v-if="total" class="dt-footer">
            <span class="dt-footer__info">{{ $t('dt_showing', { from: number(showing.from), to: number(showing.to), total: number(total) }) }}</span>
            <div class="report-viewer__pager">
                <v-select v-model="limit" :options="[25, 50, 100]" :clearable="false" :searchable="false"
                    class="report-viewer__limit is-compact" @input="load(1)" />
                <vs-pagination v-if="pages > 1" :total="pages" :value="page" :max="7" @input="load" />
            </div>
        </div>
    </vx-card>
</template>

<script>
import axios from "@/axios.js";
import vSelect from "vue-select";
import DSearch from "@/views/form-builder/DSearch.vue";
import { fetchReport, fetchReportPage, fetchReportTypes, saveBlob } from "./reportApi.js";

export default {
    components: {
        DSearch,
        "v-select": vSelect,
    },
    data() {
        return {
            report: null,
            formAttributes: [],
            fieldData: {},
            filters: [],
            showFilter: false,
            loading: false,
            error: null,
            columns: [],
            rows: [],
            total: null,
            summary: null,
            page: 1,
            limit: 25,
            pdfBusy: null, // 'view' | 'download' while the PDF is being built
            requestId: 0,
        };
    },
    computed: {
        reportType() {
            return this.$route.params.report_type;
        },
        accent() {
            const value = String((this.report && this.report.color) || "").replace("#", "");
            return value.length === 6 ? [0, 2, 4].map((i) => parseInt(value.slice(i, i + 2), 16)).join(", ") : "var(--vs-primary)";
        },
        pages() {
            return this.total ? Math.max(1, Math.ceil(this.total / this.limit)) : 1;
        },
        showing() {
            const from = (this.page - 1) * this.limit + 1;
            return { from, to: Math.min(this.total || 0, from + this.rows.length - 1) };
        },
        summaryTiles() {
            return Object.entries(this.summary || {}).map(([label, value]) => ({ label, value }));
        },
        // Numeric / date flags come from the server, so the table and the PDF format columns the same way
        numericColumns() {
            return Object.fromEntries(this.columns.map((col) => [col.key, !!col.numeric]));
        },
    },
    watch: {
        reportType: {
            immediate: true,
            handler() {
                this.init();
            },
        },
    },
    methods: {
        async init() {
            this.report = null;
            this.filters = [];
            this.formAttributes = [];
            this.showFilter = false;
            this.columns = [];
            this.rows = [];
            this.total = null;
            this.load(1);

            try {
                const types = await fetchReportTypes();
                this.report = types.find((report) => report.key === this.reportType) || null;
                if (this.report && this.report.title) this.$emit("changeRouteTitle", this.report.title);
                if (this.report && this.report.field_api) {
                    const fields = await axios.get(this.report.field_api);
                    this.fieldData = fields.data || {};
                    this.formAttributes = (fields.data && fields.data.form_attributes) || [];
                }
            } catch (e) {
                // The report still works without its title or filter fields.
            }
        },
        async load(page = 1) {
            const id = ++this.requestId; // ignore answers to older requests
            this.loading = true;
            this.error = null;
            try {
                const data = await fetchReportPage(this.reportType, this.filters, page, this.limit);
                if (id !== this.requestId) return;
                this.columns = data.columns || [];
                this.rows = data.rows || [];
                this.total = data.total;
                this.page = data.page || page;
                if (data.summary) this.summary = data.summary;
            } catch (message) {
                if (id === this.requestId) this.error = message;
            }
            if (id === this.requestId) this.loading = false;
        },
        applyFilters({ formAttribute }) {
            this.filters = this.formAttributes
                .filter((field) => {
                    const value = formAttribute[field.name];
                    return value !== undefined && value !== null && value !== "";
                })
                .map((field) => ({ [field.name]: formAttribute[field.name] }));
            this.summary = null;
            this.load(1);
        },
        // The PDF is only built when asked for (it is the slow part)
        async downloadPdf() {
            this.pdfBusy = "download";
            try {
                const { blob, filename } = await fetchReport(this.reportType, this.filters, true);
                saveBlob(blob, filename);
            } catch (message) {
                this.notifyError(message);
            }
            this.pdfBusy = null;
        },
        async openPdf() {
            // Open the tab now (inside the click) so popup blockers allow it; fill it when the PDF is ready
            const tab = window.open("", "_blank");
            if (tab) tab.document.write(`<p style="font-family:sans-serif;padding:24px;color:#666">${this.$t("report_preparing")}</p>`);
            this.pdfBusy = "view";
            try {
                const { blob } = await fetchReport(this.reportType, this.filters);
                const url = URL.createObjectURL(blob);
                if (tab) tab.location.href = url;
                else window.open(url, "_blank");
                setTimeout(() => URL.revokeObjectURL(url), 60000);
            } catch (message) {
                if (tab) tab.close();
                this.notifyError(message);
            }
            this.pdfBusy = null;
        },
        notifyError(message) {
            this.$vs.notify({ title: "Message", text: message, iconPack: "feather", icon: "icon-alert-circle", color: "danger" });
        },
        cell(row, key) {
            const value = row[key];
            if (value === null || value === undefined || value === "") return "—";
            if (typeof value === "object") return value.title_kh || value.name_kh || value.label || JSON.stringify(value);
            if (this.numericColumns[key]) return this.number(value);
            return value;
        },
        number(value) {
            const n = Number(String(value === null || value === undefined ? 0 : value).replace(/,/g, ""));
            return isNaN(n) ? value : n.toLocaleString("en-US", { maximumFractionDigits: 2 });
        },
    },
};
</script>

<style lang="scss">
.report-viewer {
    --accent: var(--vs-primary);

    &__title {
        display: flex;
        align-items: center;
        gap: 12px;
        min-width: 0;
    }

    &__icon {
        flex-shrink: 0;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 44px;
        height: 44px;
        border-radius: 12px;
        color: rgb(var(--accent));
        background: rgba(var(--accent), .12);

        .feather-icon {
            display: flex;
        }
    }

    &__summary {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
        gap: 12px;
        margin-bottom: 16px;
    }

    &__tile {
        display: flex;
        flex-direction: column;
        gap: 2px;
        padding: 14px 16px;
        border: 1px solid #eceef4;
        border-left: 3px solid rgb(var(--accent));
        border-radius: 12px;
        background: #fff;

        span {
            font-size: .8rem;
            color: #8a8ca5;
        }

        strong {
            font-size: 1.4rem;
            font-weight: 700;
            color: #2c2e45;
            font-variant-numeric: tabular-nums;
        }
    }

    &__no {
        width: 1%;
        color: #8a8ca5 !important;
        white-space: nowrap;
    }

    &__pager {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-left: auto;
    }

    &__limit {
        width: 76px;
    }

    .dt-table th {
        position: sticky;
        top: 0;
        z-index: 1;
    }

    .dt-btn:disabled {
        opacity: .6;
        cursor: progress;
    }
}

.report-spin svg {
    animation: report-rotate .8s linear infinite;
}

@keyframes report-rotate {
    to { transform: rotate(360deg); }
}

@media (max-width: 640px) {
    .report-viewer {
        .dt-toolbar__actions {
            width: 100%;

            .dt-btn:not(.dt-btn--icon) {
                flex: 1;
            }
        }

        &__pager {
            width: 100%;
            justify-content: space-between;
        }
    }
}

.theme-dark .report-viewer__tile {
    background: #10163a;
    border-color: #414561;

    strong {
        color: #fff;
    }
}
</style>

<template>
    <vx-card class="dt-list report-list">
        <!-- Toolbar: same as every list page -->
        <div class="dt-toolbar">
            <div class="dt-toolbar__title">
                <h3>{{ $t('report_title') }}</h3>
                <p>{{ $t('report_subtitle') }}</p>
            </div>
            <div class="dt-toolbar__actions report-list__search">
                <div class="dt-cell dt-search">
                    <span class="dt-search__icon"><feather-icon icon="SearchIcon" svgClasses="h-4 w-4" /></span>
                    <input v-model="keyword" type="text" :placeholder="$t('report_search')">
                </div>
            </div>
        </div>

        <!-- Loading -->
        <div v-if="loading" class="report-grid">
            <div v-for="n in 6" :key="n" class="report-card report-card--skeleton" />
        </div>

        <!-- Error -->
        <div v-else-if="error" class="dt-empty">
            <span class="dt-empty__icon"><feather-icon icon="AlertTriangleIcon" svgClasses="h-6 w-6" /></span>
            <strong>{{ $t('report_load_error') }}</strong>
            <button type="button" class="dt-btn mt-3" @click="loadReports">
                <feather-icon icon="RefreshCwIcon" svgClasses="h-4 w-4" />
                <span>{{ $t('report_retry') }}</span>
            </button>
        </div>

        <!-- Reports -->
        <template v-else>
            <div v-if="filteredReports.length" class="report-grid">
                <button v-for="(report, i) in filteredReports" :key="report.key" type="button" class="report-card"
                    :style="{ '--accent': hexToRgb(report.color), '--delay': (i * 40) + 'ms' }" @click="openReport(report)"
                    @mouseenter="prefetch(report)" @focus="prefetch(report)">
                    <span class="report-card__icon"><feather-icon :icon="report.icon" svgClasses="h-5 w-5" /></span>
                    <span class="report-card__text">
                        <strong>{{ report.title }}</strong>
                        <span>{{ report.description }}</span>
                    </span>
                    <span class="report-card__actions">
                        <span class="report-card__download" :title="$t('report_download_pdf')" @click.stop="download(report)">
                            <feather-icon :icon="downloading === report.key ? 'LoaderIcon' : 'DownloadIcon'" svgClasses="h-4 w-4"
                                :class="{ 'report-spin': downloading === report.key }" />
                        </span>
                        <span class="report-card__open"><feather-icon icon="ArrowRightIcon" svgClasses="h-4 w-4" /></span>
                    </span>
                </button>
            </div>

            <div v-else class="dt-empty">
                <span class="dt-empty__icon"><feather-icon icon="SearchIcon" svgClasses="h-6 w-6" /></span>
                <strong>{{ $t('report_not_found', { keyword }) }}</strong>
            </div>
        </template>
    </vx-card>
</template>

<script>
import { downloadReport, fetchReportTypes, prefetchReportPage } from "./reportApi.js";

export default {
    data() {
        return {
            reports: [],
            keyword: "",
            loading: true,
            error: false,
            downloading: null,
        };
    },
    computed: {
        filteredReports() {
            const keyword = this.keyword.trim().toLowerCase();
            if (!keyword) return this.reports;
            return this.reports.filter((report) =>
                (report.title + " " + report.description).toLowerCase().includes(keyword)
            );
        },
    },
    created() {
        this.loadReports();
    },
    methods: {
        async loadReports() {
            this.loading = true;
            this.error = false;
            try {
                this.reports = await fetchReportTypes();
            } catch (e) {
                this.error = true;
            }
            this.loading = false;
        },
        // Start loading the first page while the pointer is on the card, so the report opens ready
        prefetch(report) {
            prefetchReportPage(report.key);
        },
        openReport(report) {
            this.$router.push({ name: "report", params: { report_type: report.key } });
        },
        async download(report) {
            if (this.downloading) return;
            this.downloading = report.key;
            try {
                await downloadReport(report.key);
            } catch (message) {
                this.$vs.notify({ title: "Message", text: message, iconPack: "feather", icon: "icon-alert-circle", color: "danger" });
            }
            this.downloading = null;
        },
        // "#28C76F" -> "40, 199, 111" for rgba(var(--accent), …)
        hexToRgb(hex) {
            const value = String(hex || "").replace("#", "");
            if (value.length !== 6) return "var(--vs-primary)";
            return [0, 2, 4].map((i) => parseInt(value.slice(i, i + 2), 16)).join(", ");
        },
    },
};
</script>

<style>
.report-list {
    &__search {
        flex: 0 1 320px;

        .dt-search {
            width: 100%;
        }
    }
}

.report-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 14px;
}

.report-card {
    --accent: var(--vs-primary);
    display: flex;
    align-items: center;
    gap: 14px;
    min-height: 88px;
    padding: 16px;
    font-family: inherit;
    text-align: left;
    border: 1px solid #eceef4;
    border-radius: 14px;
    background: #fff;
    cursor: pointer;
    animation: report-card-in .35s ease backwards;
    animation-delay: var(--delay);
    transition: border-color .2s ease, box-shadow .2s ease, transform .2s ease;

    &:hover {
        border-color: rgba(var(--accent), .45);
        box-shadow: 0 10px 24px -14px rgba(var(--accent), .7);
        transform: translateY(-2px);

        .report-card__open {
            color: #fff;
            background: rgb(var(--accent));
        }
    }

    &:focus-visible {
        outline: none;
        border-color: rgb(var(--accent));
    }

    &__icon {
        flex-shrink: 0;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 46px;
        height: 46px;
        border-radius: 12px;
        color: rgb(var(--accent));
        background: rgba(var(--accent), .12);

        .feather-icon {
            display: flex;
        }
    }

    &__text {
        display: flex;
        flex-direction: column;
        flex: 1;
        min-width: 0;
        line-height: 1.45;

        strong {
            font-size: .95rem;
            font-weight: 700;
            color: #2c2e45;
        }

        span {
            font-size: .8rem;
            color: #8a8ca5;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
    }

    &__actions {
        display: flex;
        align-items: center;
        gap: 6px;
        flex-shrink: 0;
    }

    &__download,
    &__open {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 34px;
        height: 34px;
        border-radius: 10px;
        transition: background .15s ease, color .15s ease;

        .feather-icon {
            display: flex;
        }
    }

    &__download {
        color: #8a8ca5;

        &:hover {
            color: rgb(var(--accent));
            background: rgba(var(--accent), .1);
        }
    }

    &__open {
        color: rgb(var(--accent));
        background: rgba(var(--accent), .1);
    }

    &--skeleton {
        cursor: default;
        border-color: transparent;
        background: linear-gradient(90deg, #f1f2f5 25%, #e7e9ee 50%, #f1f2f5 75%);
        background-size: 200% 100%;
        animation: dt-shimmer 1.2s infinite;
    }
}

.report-spin svg {
    animation: report-rotate .8s linear infinite;
}

@keyframes report-card-in {
    from { opacity: 0; transform: translateY(6px); }
    to { opacity: 1; transform: none; }
}

@keyframes report-rotate {
    to { transform: rotate(360deg); }
}

@media (max-width: 640px) {
    .report-list__search {
        flex: 1 1 100%;
    }

    .report-grid {
        grid-template-columns: minmax(0, 1fr);
    }
}

.theme-dark .report-card {
    background: #10163a;
    border-color: #414561;

    &__text strong {
        color: #fff;
    }
}
</style>

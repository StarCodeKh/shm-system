<template>
    <div class="report-list">
        <div class="flex flex-wrap items-end justify-between mb-6">
            <div class="mb-3 mr-4">
                <h3 class="mb-1">គ្រប់គ្រងរបាយការណ៍</h3>
                <p class="text-grey">ជ្រើសរើសរបាយការណ៍ដើម្បីមើល ត្រង និងទាញយកជា PDF</p>
            </div>
            <vs-input
                v-model="keyword"
                icon-pack="feather"
                icon="icon-search"
                placeholder="ស្វែងរករបាយការណ៍..."
                class="mb-3 report-search" />
        </div>

        <!-- Loading -->
        <div v-if="loading" class="vx-row">
            <div v-for="n in 6" :key="n" class="vx-col w-full sm:w-1/2 lg:w-1/3 mb-base">
                <div class="report-card report-card--skeleton"></div>
            </div>
        </div>

        <!-- Error -->
        <vx-card v-else-if="error" class="text-center py-8">
            <feather-icon icon="AlertTriangleIcon" svgClasses="h-10 w-10 text-danger" />
            <p class="mt-3 mb-4">មិនអាចទាញយកបញ្ជីរបាយការណ៍បានទេ។</p>
            <vs-button color="primary" type="border" @click="loadReports">ព្យាយាមម្តងទៀត</vs-button>
        </vx-card>

        <!-- Reports -->
        <div v-else class="vx-row">
            <div v-for="report in filteredReports" :key="report.key" class="vx-col w-full sm:w-1/2 lg:w-1/3 mb-base">
                <div class="report-card" :style="{ '--accent': report.color }" @click="openReport(report)">
                    <div class="flex items-start justify-between">
                        <div class="report-card__icon" :style="{ backgroundColor: report.color + '1f', color: report.color }">
                            <feather-icon :icon="report.icon" svgClasses="h-6 w-6" />
                        </div>
                        <vx-tooltip text="ទាញយក PDF" position="top">
                            <div class="report-card__download" @click.stop="download(report)">
                                <feather-icon icon="DownloadIcon" svgClasses="h-5 w-5" />
                            </div>
                        </vx-tooltip>
                    </div>
                    <h5 class="mt-4 mb-2">{{ report.title }}</h5>
                    <p class="report-card__desc">{{ report.description }}</p>
                    <div class="report-card__footer">
                        <span>មើលរបាយការណ៍</span>
                        <feather-icon icon="ArrowRightIcon" svgClasses="h-4 w-4" />
                    </div>
                </div>
            </div>

            <div v-if="!filteredReports.length" class="vx-col w-full text-center text-grey py-8">
                រកមិនឃើញរបាយការណ៍ "{{ keyword }}"
            </div>
        </div>
    </div>
</template>

<script>
import axios from "@/axios.js";
import { downloadReport } from "./reportApi.js";

export default {
    data() {
        return {
            reports: [],
            keyword: "",
            loading: true,
            error: false,
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
                const response = await axios.get("/api/v1/report/types");
                this.reports = response.data;
            } catch (e) {
                this.error = true;
            }
            this.loading = false;
        },
        openReport(report) {
            this.$router.push({ name: "report", params: { report_type: report.key } });
        },
        async download(report) {
            this.$vs.loading();
            try {
                await downloadReport(report.key);
            } catch (message) {
                this.$vs.notify({ title: "Message", text: message, iconPack: "feather", icon: "icon-alert-circle", color: "danger" });
            }
            this.$vs.loading.close();
        },
    },
};
</script>

<style lang="scss" scoped>
.report-search {
    width: 280px;
    max-width: 100%;
}

.report-card {
    position: relative;
    height: 100%;
    min-height: 210px;
    padding: 1.5rem;
    border-radius: 0.75rem;
    background: #fff;
    box-shadow: 0 4px 24px 0 rgba(34, 41, 47, 0.08);
    border-top: 3px solid var(--accent);
    cursor: pointer;
    transition: transform 0.2s ease, box-shadow 0.2s ease;
    display: flex;
    flex-direction: column;

    &:hover {
        transform: translateY(-4px);
        box-shadow: 0 10px 30px 0 rgba(34, 41, 47, 0.15);

        .report-card__footer {
            color: var(--accent);
        }
    }

    &__icon {
        width: 3rem;
        height: 3rem;
        border-radius: 0.6rem;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    &__download {
        padding: 0.5rem;
        border-radius: 50%;
        color: #9aa0ac;
        transition: background 0.2s ease, color 0.2s ease;

        &:hover {
            background: rgba(var(--vs-primary), 0.1);
            color: rgba(var(--vs-primary), 1);
        }
    }

    &__desc {
        color: #8a90a0;
        font-size: 0.9rem;
        flex-grow: 1;
    }

    &__footer {
        margin-top: 1rem;
        display: flex;
        align-items: center;
        gap: 0.4rem;
        font-weight: 600;
        color: #626262;
        transition: color 0.2s ease;
    }

    &--skeleton {
        border-top-color: #eee;
        cursor: default;
        background: linear-gradient(90deg, #f3f4f7 25%, #e9ebf0 37%, #f3f4f7 63%);
        background-size: 400% 100%;
        animation: report-skeleton 1.4s ease infinite;

        &:hover {
            transform: none;
        }
    }
}

@keyframes report-skeleton {
    0% { background-position: 100% 50%; }
    100% { background-position: 0 50%; }
}
</style>

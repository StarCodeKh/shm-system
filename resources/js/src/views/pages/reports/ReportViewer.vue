<template>
    <div class="report-viewer">
        <!-- Toolbar -->
        <vx-card class="mb-4">
            <div class="flex flex-wrap items-center justify-between">
                <div class="flex items-center mr-4 my-1">
                    <div class="toolbar-btn mr-3" @click="$router.push('/report')">
                        <feather-icon icon="ArrowLeftIcon" svgClasses="h-5 w-5" />
                    </div>
                    <div v-if="report" class="report-icon mr-3" :style="{ backgroundColor: report.color + '1f', color: report.color }">
                        <feather-icon :icon="report.icon" svgClasses="h-5 w-5" />
                    </div>
                    <div>
                        <h4 class="mb-0">{{ report ? report.title : reportType }}</h4>
                        <small class="text-grey">
                            {{ activeFilterCount ? `បានត្រង ${activeFilterCount} លក្ខខណ្ឌ` : "ទិន្នន័យទាំងអស់" }}
                        </small>
                    </div>
                </div>

                <div class="flex items-center my-1">
                    <vx-tooltip text="តម្រង" position="top">
                        <div class="toolbar-btn mr-2" :class="{ 'is-active': showFilter }" @click="showFilter = !showFilter">
                            <feather-icon icon="FilterIcon" svgClasses="h-5 w-5" />
                        </div>
                    </vx-tooltip>
                    <vx-tooltip text="ផ្ទុកឡើងវិញ" position="top">
                        <div class="toolbar-btn mr-2" @click="generate">
                            <feather-icon icon="RefreshCwIcon" svgClasses="h-5 w-5" />
                        </div>
                    </vx-tooltip>
                    <vx-tooltip text="បើកក្នុងផ្ទាំងថ្មី" position="top">
                        <div class="toolbar-btn mr-2" :class="{ 'is-disabled': !pdfUrl }" @click="openInNewTab">
                            <feather-icon icon="ExternalLinkIcon" svgClasses="h-5 w-5" />
                        </div>
                    </vx-tooltip>
                    <vs-button icon-pack="feather" icon="icon-download" :disabled="!pdfBlob" @click="download">
                        ទាញយក PDF
                    </vs-button>
                </div>
            </div>
        </vx-card>

        <!-- Filters -->
        <transition name="fade">
            <div v-show="showFilter && formAttributes.length" class="mb-4">
                <d-search
                    :data="fieldData"
                    :dataInfo="{}"
                    :api="''"
                    :formAttributes="formAttributes"
                    :rowDisplay="'4grid'"
                    @searchQuery="applyFilters" />
            </div>
        </transition>

        <!-- Report -->
        <div class="report-frame">
            <div v-if="loading" class="report-frame__state">
                <div class="report-spinner"></div>
                <p class="mt-4">កំពុងរៀបចំរបាយការណ៍...</p>
                <small class="text-grey">របាយការណ៍ធំអាចចំណាយពេលបន្តិច</small>
            </div>
            <div v-else-if="error" class="report-frame__state">
                <feather-icon icon="AlertTriangleIcon" svgClasses="h-10 w-10 text-danger" />
                <p class="mt-3 mb-4">{{ error }}</p>
                <vs-button type="border" @click="generate">ព្យាយាមម្តងទៀត</vs-button>
            </div>
            <iframe v-else-if="pdfUrl" :src="pdfUrl" title="Report"></iframe>
        </div>
    </div>
</template>

<script>
import axios from "@/axios.js";
import DSearch from "@/views/form-builder/DSearch.vue";
import { fetchReport, saveBlob } from "./reportApi.js";

export default {
    components: {
        DSearch,
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
            pdfBlob: null,
            pdfUrl: null,
            filename: "",
        };
    },
    computed: {
        reportType() {
            return this.$route.params.report_type;
        },
        activeFilterCount() {
            return this.filters.length;
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
    beforeDestroy() {
        this.clearPdf();
    },
    methods: {
        async init() {
            this.report = null;
            this.filters = [];
            this.formAttributes = [];
            this.showFilter = false;
            this.generate();

            try {
                const types = await axios.get("/api/v1/report/types");
                this.report = types.data.find((report) => report.key === this.reportType) || null;
                if (this.report && this.report.field_api) {
                    const fields = await axios.get(this.report.field_api);
                    this.fieldData = fields.data || {};
                    this.formAttributes = (fields.data && fields.data.form_attributes) || [];
                }
            } catch (e) {
                // The report still works without its title or filter fields.
            }
        },
        async generate() {
            this.loading = true;
            this.error = null;
            try {
                const { blob, filename } = await fetchReport(this.reportType, this.filters);
                this.clearPdf();
                this.pdfBlob = blob;
                this.pdfUrl = URL.createObjectURL(blob);
                this.filename = filename;
            } catch (message) {
                this.error = message;
            }
            this.loading = false;
        },
        applyFilters({ formAttribute }) {
            this.filters = this.formAttributes
                .filter((field) => {
                    const value = formAttribute[field.name];
                    return value !== undefined && value !== null && value !== "";
                })
                .map((field) => ({ [field.name]: formAttribute[field.name] }));
            this.generate();
        },
        download() {
            if (this.pdfBlob) saveBlob(this.pdfBlob, this.filename);
        },
        openInNewTab() {
            if (this.pdfUrl) window.open(this.pdfUrl, "_blank");
        },
        clearPdf() {
            if (this.pdfUrl) URL.revokeObjectURL(this.pdfUrl);
            this.pdfUrl = null;
            this.pdfBlob = null;
        },
    },
};
</script>

<style lang="scss" scoped>
.toolbar-btn {
    padding: 0.6rem;
    border-radius: 0.5rem;
    border: 1px solid #e4e6ec;
    color: #626262;
    cursor: pointer;
    display: flex;
    transition: all 0.2s ease;

    &:hover,
    &.is-active {
        color: rgba(var(--vs-primary), 1);
        border-color: rgba(var(--vs-primary), 1);
        background: rgba(var(--vs-primary), 0.08);
    }

    &.is-disabled {
        opacity: 0.4;
        pointer-events: none;
    }
}

.report-icon {
    width: 2.5rem;
    height: 2.5rem;
    border-radius: 0.5rem;
    display: flex;
    align-items: center;
    justify-content: center;
}

.report-frame {
    height: calc(100vh - 280px);
    min-height: 520px;
    border-radius: 0.75rem;
    overflow: hidden;
    background: #fff;
    box-shadow: 0 4px 24px 0 rgba(34, 41, 47, 0.08);

    iframe {
        width: 100%;
        height: 100%;
        border: 0;
    }

    &__state {
        height: 100%;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        text-align: center;
        padding: 2rem;
    }
}

.report-spinner {
    width: 44px;
    height: 44px;
    border: 4px solid rgba(var(--vs-primary), 0.15);
    border-top-color: rgba(var(--vs-primary), 1);
    border-radius: 50%;
    animation: report-spin 0.8s linear infinite;
}

@keyframes report-spin {
    to { transform: rotate(360deg); }
}

.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.2s ease;
}
.fade-enter,
.fade-leave-to {
    opacity: 0;
}
</style>

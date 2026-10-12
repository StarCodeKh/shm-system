<template>
    <vx-card>
        <!-- What this section edits, and a shortcut to see the result -->
        <div class="landing-settings__head">
            <div class="min-w-0">
                <h4>{{ $t('setting_tab_landing') }}</h4>
                <p>{{ $t('landing_settings_hint') }}</p>
            </div>
            <a href="/welcome" target="_blank" rel="noopener" class="landing-settings__open">
                <feather-icon icon="ExternalLinkIcon" svgClasses="h-4 w-4" />
                <span>{{ $t('landing_open_page') }}</span>
            </a>
        </div>

        <d-table-list v-if="isVisible" @clicked="initTableData" :api="api" :allowDel="true" :title="title"
            :dataInfo="{}" :dataAttributes="dataAttributes" :dataHeaders="dataHeaders" :dataTables="data"
            :formAttributes="formAttributes" :rowDisplay="rowDisplay"></d-table-list>
    </vx-card>
</template>

<script>
import axios from "@/axios.js"
import apiConfig from "@/apiConfig.js"
import DTableList from '@/views/form-builder/DTableList.vue'

// Settings › Landing page: blocks of the public page (/welcome), one row per block and language.
// Form fields come from the table's columns (app/Services/FormSchema.php).
export default {
    components: { DTableList },
    data() {
        return {
            title: "setting_landing_content",
            api: apiConfig._apiLandingContent,
            dataAttributes: {
                tableStyle: 1,
                page_number: 1,
                offset: 0,
                dataGrid: "row",
                actionButton: [],
            },
            dataHeaders: {
                header1: "headline",
                header2: "landing_section",
                header3: "language_code",
                header4: "order_level",
            },
            data: { data: [], limit: 10, total: 0 },
            formAttributes: [],
            rowDisplay: "2grid",
            isVisible: false,
        }
    },
    methods: {
        getDataTable(criteria) {
            const params = {
                sort: criteria.sort,
                order: criteria.order,
                page_number: criteria.page_number,
                limit: criteria.limit,
            }
            if (criteria.search_field) {
                params.search_field = this.formAttributes
                    .filter(field => criteria.search_field[field.name])
                    .map(field => ({ [field.name]: criteria.search_field[field.name] }))
            }
            return axios.post(this.api + "/search", params)
                .then(({ data }) => {
                    this.data = data
                    if (data.form_attributes) this.formAttributes = data.form_attributes
                    this.isVisible = data.is_display !== false // hidden only when the server says no access
                })
                .catch(() => {
                    this.$vs.notify({ title: 'Message', text: 'msg.action_failed', color: 'danger' })
                })
                .finally(() => this.$vs.loading.close())
        },
        initTableData(searchQuery) {
            this.$vs.loading()
            this.getDataTable({
                sort: "language_code",
                order: "",
                page_number: searchQuery.pageNum,
                limit: searchQuery.limit,
                search_field: searchQuery.searchFields,
            })
        },
    },
    created() {
        this.$vs.loading()
        this.getDataTable({ sort: "language_code", order: "", page_number: this.dataAttributes.page_number })
    },
}
</script>

<style>
.landing-settings__head {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 1rem;
    margin-bottom: 1rem;

    h4 {
        margin: 0 0 .25rem;
        font-weight: 600;
    }

    p {
        margin: 0;
        font-size: .875rem;
        color: #8a8d9f;
    }
}

.landing-settings__open {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    flex-shrink: 0;
    padding: .5rem .9rem;
    font-size: .875rem;
    font-weight: 600;
    color: rgba(var(--vs-primary), 1) !important;
    border-radius: 10px;
    background: rgba(var(--vs-primary), .1);
    transition: background .2s ease, transform .2s ease;

    &:hover {
        background: rgba(var(--vs-primary), .16);
        transform: translateY(-1px);
    }
}

.theme-dark .landing-settings__head p {
    color: #b0b4cc;
}

@media (max-width: 640px) {
    .landing-settings__head {
        flex-direction: column;
    }
}
</style>

<template>
    <div class="demo-alignment">
        <!-- The popup grows with the file: wider for more columns -->
        <vs-popup class="d-modal imp" :class="sizeClass" :title="$t('imp_title')" v-model:active="showModalForm">
            <!-- 1. Choose a file -->
            <div v-if="!preview" class="imp__pick">
                <label class="imp__drop" :class="{ 'is-over': dragging, 'is-busy': checking }"
                    @dragover.prevent="dragging = true" @dragleave.prevent="dragging = false" @drop.prevent="onDrop">
                    <input ref="input" type="file" accept=".xlsx,.xls,.csv" @change="onPick" />
                    <span class="imp__drop-icon">
                        <feather-icon :icon="checking ? 'LoaderIcon' : 'UploadCloudIcon'" svgClasses="h-7 w-7" :class="{ 'imp__spin': checking }" />
                    </span>
                    <strong>{{ checking ? $t('imp_checking') : $t('imp_drop') }}</strong>
                    <small>{{ $t('imp_formats') }}</small>
                </label>
                <p v-if="error" class="imp__error"><feather-icon icon="AlertCircleIcon" svgClasses="h-4 w-4" />{{ error }}</p>
                <button v-if="table" type="button" class="imp__link" @click="downloadTemplate">
                    <feather-icon icon="DownloadIcon" svgClasses="h-4 w-4" />{{ $t('imp_template') }}
                </button>
            </div>

            <!-- 2. Preview and check -->
            <div v-else class="imp__preview">
                <div class="imp__file">
                    <span class="imp__file-icon"><feather-icon icon="FileTextIcon" svgClasses="h-5 w-5" /></span>
                    <div class="min-w-0">
                        <strong>{{ file.name }}</strong>
                        <small>{{ size(file.size) }} · {{ $t('imp_rows', { n: number(preview.summary.total) }) }}</small>
                    </div>
                    <button type="button" class="dt-btn" @click="reset">
                        <feather-icon icon="RefreshCwIcon" svgClasses="h-4 w-4" /><span>{{ $t('imp_change_file') }}</span>
                    </button>
                </div>

                <!-- Counts double as filters -->
                <div class="imp__stats">
                    <button v-for="s in stats" :key="s.key" type="button" class="imp__stat" :class="['is-' + s.key, { 'is-active': filter === s.key }]"
                        :disabled="!s.count && s.key !== 'all'" @click="filter = s.key">
                        <span>{{ s.label }}</span>
                        <strong>{{ number(s.count) }}</strong>
                    </button>
                </div>

                <p v-if="preview.missing.length" class="imp__banner is-error">
                    <feather-icon icon="AlertTriangleIcon" svgClasses="h-4 w-4" />
                    <span>{{ $t('imp_missing', { cols: preview.missing.map(label).join(', ') }) }}</span>
                </p>
                <p v-if="ignored.length" class="imp__banner">
                    <feather-icon icon="InfoIcon" svgClasses="h-4 w-4" />
                    <span>{{ $t('imp_ignored', { cols: ignored.join(', ') }) }}</span>
                </p>
                <p v-if="preview.truncated" class="imp__banner is-error">
                    <feather-icon icon="AlertTriangleIcon" svgClasses="h-4 w-4" />
                    <span>{{ $t('imp_too_many') }}</span>
                </p>

                <div class="imp__table-wrap">
                    <table class="imp__table">
                        <thead>
                            <tr>
                                <th class="imp__line">#</th>
                                <th>{{ $t('imp_status') }}</th>
                                <th v-for="c in preview.columns" :key="c.name" :class="{ 'is-ignored': !c.known }" :title="c.name">
                                    {{ label(c.name) }}<span v-if="c.required" class="imp__req">*</span>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="row in visibleRows" :key="row.line" :class="'is-' + row.status">
                                <td class="imp__line">{{ row.line }}</td>
                                <td><span class="imp__badge" :class="'is-' + row.status">{{ $t('imp_status_' + row.status) }}</span></td>
                                <td v-for="c in preview.columns" :key="c.name"
                                    :class="{ 'is-ignored': !c.known, 'is-changed': row.changed.includes(c.name), 'is-bad': row.errors[c.name] }"
                                    :title="row.errors[c.name] || (row.changed.includes(c.name) ? $t('imp_changed') : null)">
                                    <span>{{ show(row.values[c.name]) }}</span>
                                    <small v-if="row.errors[c.name]">{{ row.errors[c.name] }}</small>
                                </td>
                            </tr>
                            <tr v-if="!visibleRows.length">
                                <td :colspan="preview.columns.length + 2" class="imp__none">{{ $t('imp_none') }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <small v-if="preview.shown < preview.summary.total" class="imp__note">
                    {{ $t('imp_shown', { shown: number(preview.shown), total: number(preview.summary.total) }) }}
                </small>

                <div class="imp__actions">
                    <span class="imp__hint" :class="{ 'is-error': !preview.ready }">
                        <feather-icon :icon="preview.ready ? 'CheckCircleIcon' : 'XCircleIcon'" svgClasses="h-4 w-4" />
                        {{ preview.ready ? $t('imp_ready') : $t('imp_fix_first') }}
                    </span>
                    <button type="button" class="dt-btn" @click="showModalForm = false">{{ $t('dt_cancel') }}</button>
                    <button type="button" class="dt-btn dt-btn--primary" :disabled="!preview.ready || importing || !importable" @click="importFile">
                        <feather-icon :icon="importing ? 'LoaderIcon' : 'UploadIcon'" svgClasses="h-4 w-4" :class="{ 'imp__spin': importing }" />
                        <span>{{ $t('imp_import', { n: number(importable) }) }}</span>
                    </button>
                </div>
            </div>
        </vs-popup>
    </div>
</template>

<script>
import axios from "@/axios.js";

// Import: pick a file → the server checks every row (POST /api/v1/import/preview, nothing saved) →
// review new / changed / unchanged / wrong rows → import through the list's own /import endpoint.
export default {
    data() {
        return {
            showModalForm: false,
            api: "",
            table: "",
            file: null,
            preview: null,
            filter: "all",
            dragging: false,
            checking: false,
            importing: false,
            error: "",
        }
    },
    computed: {
        // Width follows the number of columns in the file
        sizeClass() {
            if (!this.preview) return "d-modal--md"
            const n = this.preview.columns.length
            return n <= 3 ? "d-modal--md" : n <= 6 ? "d-modal--lg" : "d-modal--xl"
        },
        stats() {
            const s = this.preview ? this.preview.summary : {}
            return [
                { key: "all", label: this.$t("imp_all"), count: s.total },
                { key: "new", label: this.$t("imp_status_new"), count: s.new },
                { key: "update", label: this.$t("imp_status_update"), count: s.update },
                { key: "same", label: this.$t("imp_status_same"), count: s.same },
                { key: "error", label: this.$t("imp_status_error"), count: s.error },
            ]
        },
        visibleRows() {
            const rows = this.preview ? this.preview.rows : []
            return this.filter === "all" ? rows : rows.filter(row => row.status === this.filter)
        },
        ignored() {
            return this.preview ? this.preview.columns.filter(c => !c.known).map(c => c.name) : []
        },
        // Rows the import will add or change
        importable() {
            return this.preview ? this.preview.summary.new + this.preview.summary.update : 0
        },
    },
    methods: {
        // Called by the list's Import button
        initFileUploader(dataTables, api) {
            this.api = api
            this.table = (dataTables && dataTables.model_name) || ""
            this.reset()
            this.showModalForm = true
        },
        reset() {
            this.file = null
            this.preview = null
            this.filter = "all"
            this.error = ""
            if (this.$refs.input) this.$refs.input.value = ""
        },
        onPick(event) {
            const file = event.target.files[0]
            if (file) this.check(file)
        },
        onDrop(event) {
            this.dragging = false
            const file = event.dataTransfer.files[0]
            if (file) this.check(file)
        },
        async check(file) {
            this.file = file
            this.error = ""
            this.checking = true
            const form = new FormData()
            form.append("file", file)
            form.append("table", this.table)
            try {
                const { data } = await axios.post("/api/v1/import/preview", form)
                this.preview = data
                this.filter = data.summary.error ? "error" : "all"
            } catch (error) {
                this.error = this.message(error)
                this.file = null
            }
            this.checking = false
        },
        async importFile() {
            this.importing = true
            const form = new FormData()
            form.append("file", this.file)
            try {
                const { data } = await axios.post(this.api + "/import", form)
                this.$vs.notify({ text: data.message || this.$t("imp_done", { n: this.number(this.importable) }), color: data.success === false ? "danger" : "success" })
                if (data.success !== false) {
                    this.$emit("clicked", "")
                    this.showModalForm = false
                }
            } catch (error) {
                this.$vs.notify({ text: this.message(error), color: "danger" })
            }
            this.importing = false
        },
        async downloadTemplate() {
            try {
                const response = await axios.get("/api/v1/import/template", { params: { table: this.table }, responseType: "blob" })
                const url = URL.createObjectURL(new Blob([response.data], { type: response.headers["content-type"] }))
                const link = document.createElement("a")
                link.href = url
                link.download = `${this.table}-template.xlsx`
                document.body.appendChild(link)
                link.click()
                link.remove()
                URL.revokeObjectURL(url)
            } catch (error) {
                this.$vs.notify({ text: this.$t("report_error"), color: "danger" })
            }
        },
        message(error) {
            const data = error && error.response && error.response.data
            if (data && data.errors) return Object.values(data.errors).flat().join(" ")
            return (data && typeof data.message === "string" && data.message) || this.$t("report_error")
        },
        // Column title in the user's language when the app knows the field, else its name
        label(name) {
            return this.$tField ? this.$tField(name) : name
        },
        show(value) {
            return value === null || value === undefined || value === "" ? "—" : value
        },
        number(value) {
            return Number(value || 0).toLocaleString("en-US")
        },
        size(bytes) {
            return bytes < 1024 * 1024 ? `${Math.max(1, Math.round(bytes / 1024))} KB` : `${(bytes / 1024 / 1024).toFixed(1)} MB`
        },
    },
}
</script>

<style lang="scss">
.imp {
    &__pick {
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

    &__drop {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 6px;
        padding: 36px 20px;
        text-align: center;
        border: 2px dashed #d8d9e3;
        border-radius: 16px;
        background: #fafbfc;
        cursor: pointer;
        transition: border-color var(--tab-tap) ease, background var(--tab-tap) ease;

        input {
            display: none;
        }

        strong {
            font-size: 1rem;
            color: #2c2e45;
        }

        small {
            font-size: .8rem;
            color: #8a8ca5;
        }

        &:hover,
        &.is-over {
            border-color: rgba(var(--vs-primary), .6);
            background: rgba(var(--vs-primary), .05);
        }

        &.is-busy {
            pointer-events: none;
            opacity: .75;
        }
    }

    &__drop-icon {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 56px;
        height: 56px;
        margin-bottom: 4px;
        color: rgb(var(--vs-primary));
        border-radius: 16px;
        background: rgba(var(--vs-primary), .1);

        .feather-icon {
            display: flex;
        }
    }

    &__spin svg {
        animation: imp-spin .9s linear infinite;
    }

    &__link {
        display: inline-flex;
        align-self: flex-start;
        align-items: center;
        gap: 6px;
        padding: 0;
        font-family: inherit;
        font-size: .85rem;
        color: rgb(var(--vs-primary));
        border: none;
        background: none;
        cursor: pointer;

        .feather-icon {
            display: flex;
        }

        &:hover {
            text-decoration: underline;
        }
    }

    &__error,
    &__banner {
        display: flex;
        align-items: flex-start;
        gap: 8px;
        margin: 0;
        padding: 10px 14px;
        font-size: .85rem;
        border-radius: 10px;
        color: #4b4d68;
        background: #f3f4f7;

        .feather-icon {
            display: flex;
            flex-shrink: 0;
            margin-top: 2px;
        }
    }

    &__error,
    &__banner.is-error {
        color: rgb(234, 84, 85);
        background: rgba(234, 84, 85, .08);
    }

    &__preview {
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

    &__file {
        display: flex;
        align-items: center;
        gap: 12px;

        > div {
            flex: 1;
        }

        strong {
            display: block;
            font-size: .95rem;
            color: #2c2e45;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        small {
            font-size: .8rem;
            color: #8a8ca5;
        }
    }

    &__file-icon {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 42px;
        height: 42px;
        flex-shrink: 0;
        color: rgb(33, 115, 70);
        border-radius: 12px;
        background: rgba(33, 115, 70, .1);

        .feather-icon {
            display: flex;
        }
    }

    &__stats {
        display: grid;
        grid-template-columns: repeat(5, minmax(0, 1fr));
        gap: 8px;
    }

    &__stat {
        --tone: 92, 99, 120;
        display: flex;
        flex-direction: column;
        align-items: flex-start;
        gap: 2px;
        padding: 10px 12px;
        font-family: inherit;
        text-align: left;
        border: 1px solid #eceef4;
        border-radius: 12px;
        background: #fff;
        cursor: pointer;
        transition: border-color var(--tab-tap) ease, background var(--tab-tap) ease;

        span {
            font-size: .78rem;
            color: #6b6d7a;
        }

        strong {
            font-size: 1.2rem;
            color: rgb(var(--tone));
            font-variant-numeric: tabular-nums;
        }

        &.is-new { --tone: 40, 199, 111; }
        &.is-update { --tone: 0, 143, 251; }
        &.is-error { --tone: 234, 84, 85; }

        &.is-active {
            border-color: rgba(var(--tone), .5);
            background: rgba(var(--tone), .06);
        }

        &:disabled {
            opacity: .45;
            cursor: default;
        }
    }

    &__table-wrap {
        max-height: min(52vh, 520px);
        overflow: auto;
        border: 1px solid #eceef4;
        border-radius: 12px;
    }

    &__table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
        font-size: .82rem;

        th,
        td {
            padding: 8px 12px;
            text-align: left;
            white-space: nowrap;
            border-bottom: 1px solid #f0f1f5;
        }

        th {
            position: sticky;
            top: 0;
            z-index: 1;
            font-weight: 700;
            color: #4b4d68;
            background: #f6f7fb;
        }

        td small {
            display: block;
            font-size: .72rem;
        }

        .is-ignored {
            color: #b0b2bd;
            text-decoration: line-through;
        }

        td.is-changed {
            color: rgb(0, 112, 200);
            background: rgba(0, 143, 251, .08);
            font-weight: 600;
        }

        td.is-bad {
            color: rgb(234, 84, 85);
            background: rgba(234, 84, 85, .08);
        }
    }

    &__line {
        width: 1%;
        color: #9a9cb3;
        font-variant-numeric: tabular-nums;
    }

    &__req {
        margin-left: 2px;
        color: rgb(234, 84, 85);
    }

    &__badge {
        --tone: 92, 99, 120;
        display: inline-block;
        padding: 2px 10px;
        font-size: .72rem;
        font-weight: 600;
        border-radius: 999px;
        color: rgb(var(--tone));
        background: rgba(var(--tone), .1);

        &.is-new { --tone: 40, 199, 111; }
        &.is-update { --tone: 0, 143, 251; }
        &.is-error { --tone: 234, 84, 85; }
    }

    &__none {
        padding: 24px !important;
        text-align: center !important;
        color: #8a8ca5;
    }

    &__note {
        font-size: .78rem;
        color: #8a8ca5;
    }

    &__actions {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: flex-end;
        gap: 8px;
    }

    &__hint {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        margin-right: auto;
        font-size: .85rem;
        color: rgb(40, 160, 90);

        .feather-icon {
            display: flex;
        }

        &.is-error {
            color: rgb(234, 84, 85);
        }
    }

    @media (max-width: 640px) {
        &__stats {
            grid-template-columns: repeat(3, minmax(0, 1fr));
        }

        &__hint {
            width: 100%;
        }
    }
}

@keyframes imp-spin {
    to { transform: rotate(360deg); }
}

.theme-dark .imp {
    &__drop { background: #1b2142; border-color: #414561; }
    &__drop strong,
    &__file strong { color: #fff; }
    &__stat { background: #10163a; border-color: #414561; }
    &__table-wrap { border-color: #414561; }
    &__table th { background: #1b2142; color: #c2c6dc; }
    &__table td { border-color: #2c3155; }
    &__banner { background: #262c49; color: #c2c6dc; }
}
</style>

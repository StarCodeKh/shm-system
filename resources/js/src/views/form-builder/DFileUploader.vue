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

                <!-- Columns with problems: a click goes to the first wrong cell, ready to fix -->
                <div v-if="problemColumns.length" class="imp__problems">
                    <span>{{ $t('imp_problems') }}</span>
                    <button v-for="p in problemColumns" :key="p.name" type="button" class="imp__chip" @click="goToProblem(p.name)">
                        {{ label(p.name) }}<strong>{{ number(p.count) }}</strong>
                    </button>
                </div>
                <!-- Names of choices typed instead of their id ("active" for Active): fixed in one click -->
                <transition name="imp-pop">
                    <p v-if="suggestions.length" class="imp__banner is-auto">
                        <feather-icon icon="StarIcon" svgClasses="h-4 w-4" />
                        <span>{{ $t('imp_auto', { n: number(suggestions.length), from: show(suggestions[0].from), to: suggestions[0].label }) }}</span>
                        <button type="button" class="dt-btn dt-btn--primary" @click="applySuggestions">{{ $t('imp_auto_apply') }}</button>
                    </p>
                </transition>
                <!-- Rows that repeat an earlier row of the file: removed in one click -->
                <transition name="imp-pop">
                    <p v-if="repeats.length" class="imp__banner is-repeat">
                        <feather-icon icon="CopyIcon" svgClasses="h-4 w-4" />
                        <span>{{ $t('imp_repeats', { n: number(repeats.length) }) }}</span>
                        <button type="button" class="dt-btn" @click="removeRepeats">
                            <feather-icon icon="Trash2Icon" svgClasses="h-4 w-4" /><span>{{ $t('imp_repeats_remove') }}</span>
                        </button>
                    </p>
                </transition>
                <!-- One fix, many rows: the same wrong value in the same column -->
                <transition name="imp-pop">
                <p v-if="bulk" class="imp__banner is-bulk">
                    <feather-icon icon="ZapIcon" svgClasses="h-4 w-4" />
                    <span>{{ $t('imp_bulk', { n: number(bulk.lines.length), col: label(bulk.name), from: bulk.from || '—', to: bulk.to || '—' }) }}</span>
                    <button type="button" class="dt-btn dt-btn--primary" @click="applyBulk">{{ $t('imp_bulk_apply') }}</button>
                    <button type="button" class="imp__close" :aria-label="$t('dt_cancel')" @click="bulk = null">
                        <feather-icon icon="XIcon" svgClasses="h-4 w-4" />
                    </button>
                </p>
                </transition>

                <div ref="tableWrap" class="imp__table-wrap">
                    <table class="imp__table">
                        <thead>
                            <tr>
                                <th class="imp__line">#</th>
                                <th>{{ $t('imp_status') }}</th>
                                <th v-for="c in preview.columns" :key="c.name" :class="{ 'is-ignored': !c.known }" :title="c.name">
                                    {{ label(c.name) }}<span v-if="c.required" class="imp__req">*</span>
                                </th>
                                <th class="imp__remove"></th>
                            </tr>
                        </thead>
                        <transition-group tag="tbody" name="imp-row">
                            <tr v-for="row in visibleRows" :key="row.line" :class="'is-' + row.status">
                                <td class="imp__line">{{ row.line }}</td>
                                <td><span class="imp__badge" :class="'is-' + row.status">{{ $t('imp_status_' + row.status) }}</span></td>
                                <td v-for="c in preview.columns" :key="c.name"
                                    :class="{ 'is-ignored': !c.known, 'is-changed': row.changed.includes(c.name), 'is-bad': row.errors[c.name],
                                        'is-editable': c.known, 'is-edited': isEdited(row, c), 'is-editing': isEditing(row, c) }"
                                    :title="row.errors[c.name] || (row.changed.includes(c.name) ? $t('imp_changed') : c.known ? $t('imp_click_edit') : null)"
                                    @click="startEdit(row, c)">
                                    <!-- Choice columns: the app's own dropdown, searchable by name or id; its menu sits on the page, not clipped by the table -->
                                    <v-select v-if="isEditing(row, c) && c.options" ref="cellSelect" :model-value="draft" :options="choiceOptions(c)"
                                        :reduce="o => o.value" label="label" :clearable="false" :placeholder="show(draft)"
                                        :filter-by="filterChoice" :map-keydown="choiceKeys" append-to-body :calculate-position="placeMenu"
                                        class="imp__select is-compact" @update:model-value="value => pickChoice(value)" @search:blur="cancelEdit">
                                        <template #option="o">
                                            <span class="imp__opt">{{ o.label }}<small v-if="o.value !== ''">{{ o.value }}</small></span>
                                        </template>
                                        <template #no-options>{{ $t('imp_no_choice') }}</template>
                                    </v-select>
                                    <input v-else-if="isEditing(row, c)" ref="cellInput" v-model="draft" class="imp__input" :class="{ 'is-invalid': !looksValid(c, draft) }"
                                        :inputmode="c.type === 'number' ? 'decimal' : null" :placeholder="c.type === 'date' ? 'YYYY-MM-DD' : null"
                                        @keydown.enter.prevent="commitEdit()" @keydown.tab.prevent="commitEdit($event.shiftKey ? -1 : 1)"
                                        @keydown.esc.stop.prevent="cancelEdit" @blur="commitEdit()" />
                                    <template v-else>
                                        <span>{{ display(row, c) }}</span>
                                        <small v-if="row.errors[c.name]">
                                            {{ row.errors[c.name] }}
                                            <button v-if="hasSuggestion(row, c)" type="button" class="imp__suggest" @click.stop="applySuggestion(row, c)">
                                                → {{ optionLabel(c, row.suggest[c.name]) }}
                                            </button>
                                        </small>
                                    </template>
                                </td>
                                <td class="imp__remove">
                                    <button type="button" :title="$t('imp_remove_row')" :aria-label="$t('imp_remove_row')" @click="removeRow(row)">
                                        <feather-icon icon="XIcon" svgClasses="h-4 w-4" />
                                    </button>
                                </td>
                            </tr>
                            <tr v-if="!visibleRows.length" key="none">
                                <td :colspan="preview.columns.length + 3" class="imp__none">{{ $t('imp_none') }}</td>
                            </tr>
                        </transition-group>
                    </table>
                </div>
                <small v-if="preview.shown < preview.summary.total" class="imp__note">
                    {{ $t('imp_shown', { shown: number(preview.shown), total: number(preview.summary.total) }) }}
                </small>

                <div class="imp__actions">
                    <span v-if="pending" class="imp__hint is-busy">
                        <feather-icon icon="LoaderIcon" svgClasses="h-4 w-4" class="imp__spin" />{{ $t('imp_rechecking') }}
                    </span>
                    <span v-else class="imp__hint" :class="{ 'is-error': !preview.ready }">
                        <feather-icon :icon="preview.ready ? 'CheckCircleIcon' : 'XCircleIcon'" svgClasses="h-4 w-4" />
                        {{ preview.ready ? $t('imp_ready') : $t('imp_fix_first') }}
                    </span>
                    <span v-if="editCount" class="imp__edits">
                        {{ $t('imp_edited', { n: number(editCount) }) }}
                        <button type="button" class="imp__link" @click="undoEdits">
                            <feather-icon icon="RotateCcwIcon" svgClasses="h-4 w-4" />{{ $t('imp_undo') }}
                        </button>
                    </span>
                    <button type="button" class="dt-btn" @click="showModalForm = false">{{ $t('dt_cancel') }}</button>
                    <button type="button" class="dt-btn dt-btn--primary" :disabled="!preview.ready || importing || pending || !importable" @click="importFile">
                        <feather-icon :icon="importing ? 'LoaderIcon' : 'UploadIcon'" svgClasses="h-4 w-4" :class="{ 'imp__spin': importing }" />
                        <span>{{ $t('imp_import', { n: number(importable) }) }}</span>
                    </button>
                </div>
            </div>
        </vs-popup>
    </div>
</template>

<script>
import axios, { errorMessage } from "@/axios.js";

// Import: pick a file → the server checks every row (POST /api/v1/import/preview, nothing saved) →
// review new / changed / unchanged / wrong rows, fix cells or remove rows in place (checked again on the server) →
// import through the list's own /import endpoint (with fixes: the fixed file from POST /api/v1/import/file).
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
            // Fixes made in the preview, by line (row number in the file): { line: { column: value } }, and removed lines
            edits: {},
            removed: [],
            editing: null, // the cell being edited: { line, name }
            draft: "",
            rechecking: false,
            recheckTimer: null,
            checkSeq: 0, // only the latest check's answer is shown
            bulk: null, // offer to fix the same value in other rows: { name, from, to, lines }
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
        // Columns with wrong values, most problems first (every wrong row is in the preview)
        problemColumns() {
            if (!this.preview) return []
            const counts = {}
            this.preview.rows.forEach(row => Object.keys(row.errors).forEach(name => { counts[name] = (counts[name] || 0) + 1 }))
            return Object.entries(counts).map(([name, count]) => ({ name, count })).sort((a, b) => b.count - a.count)
        },
        // Cells the server knows the right choice for, not fixed yet
        suggestions() {
            if (!this.preview) return []
            const list = []
            this.preview.rows.forEach(row => Object.entries(row.suggest || {}).forEach(([name, value]) => {
                const column = this.preview.columns.find(c => c.name === name)
                if (column && row.errors[name] && !this.isEdited(row, column)) {
                    list.push({ row, column, value, from: row.values[name], label: this.optionLabel(column, value) })
                }
            }))
            return list
        },
        // Rows repeating an earlier row (same code / number / email, or the very same values)
        repeats() {
            return this.preview ? this.preview.rows.filter(row => row.duplicate_of) : []
        },
        editCount() {
            return Object.values(this.edits).reduce((n, cells) => n + Object.keys(cells).length, 0) + this.removed.length
        },
        // A fix waits to be checked: the preview on screen is not the final word yet
        pending() {
            return this.rechecking || !!this.recheckTimer
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
            this.edits = {}
            this.removed = []
            this.editing = null
            this.bulk = null
            clearTimeout(this.recheckTimer)
            this.recheckTimer = null
            this.checkSeq++
            this.rechecking = false
            this.checking = false
            if (this.$refs.input) this.$refs.input.value = ""
        },
        onPick(event) {
            const file = event.target.files[0]
            if (file) this.pick(file)
        },
        onDrop(event) {
            this.dragging = false
            const file = event.dataTransfer.files[0]
            if (file) this.pick(file)
        },
        // A new file starts without fixes
        pick(file) {
            this.reset()
            this.check(file)
        },
        // The file and the fixes made so far, as the server reads them
        form(file) {
            const form = new FormData()
            form.append("file", file)
            form.append("table", this.table)
            if (this.editCount) form.append("edits", JSON.stringify({ cells: this.edits, removed: this.removed }))
            return form
        },
        async check(file) {
            this.file = file
            this.error = ""
            this.checking = true
            const seq = ++this.checkSeq
            try {
                const { data } = await axios.post("/api/v1/import/preview", this.form(file))
                if (seq !== this.checkSeq) return
                this.preview = data
                this.filter = data.summary.error ? "error" : "all"
            } catch (error) {
                if (seq !== this.checkSeq) return
                this.error = this.message(error)
                this.file = null
            }
            this.checking = false
        },
        // Check again with the fixes, a moment after the last one (typing through cells sends one request)
        recheck() {
            clearTimeout(this.recheckTimer)
            this.recheckTimer = setTimeout(async () => {
                this.recheckTimer = null
                this.rechecking = true
                const seq = ++this.checkSeq
                try {
                    const { data } = await axios.post("/api/v1/import/preview", this.form(this.file))
                    if (seq !== this.checkSeq) return
                    this.preview = data
                    // Stay on the chosen group while it still has rows (all fixed → show everything)
                    if (this.filter !== "all" && !data.summary[this.filter]) this.filter = "all"
                } catch (error) {
                    if (seq !== this.checkSeq) return
                    this.$vs.notify({ text: this.message(error), color: "danger" })
                }
                this.rechecking = false
            }, 250)
        },
        // A choice is shown by its name; anything else as it is
        display(row, column) {
            const value = row.values[column.name]
            return column.options && !row.errors[column.name] && value !== null && value !== undefined ? this.optionLabel(column, value) : this.show(value)
        },
        optionLabel(column, value) {
            const option = (column.options || []).find(o => String(o.value) === String(value))
            return option ? option.label : this.show(value)
        },
        // Quick check while typing (the server has the last word): numbers and dates
        looksValid(column, value) {
            const text = String(value || "").trim()
            if (!text) return true
            if (column.type === "number") return /^-?[\d,\s]*\.?\d+$/.test(text)
            if (column.type === "date") return !Number.isNaN(Date.parse(text))
            return true
        },
        hasSuggestion(row, column) {
            return !!row.suggest && row.suggest[column.name] !== undefined && !this.isEdited(row, column)
        },
        // Fix one or many cells (fixes are kept as edits, so Undo brings the file's values back)
        setCells(cells) {
            const edits = { ...this.edits }
            cells.forEach(({ row, column, value }) => {
                const text = value === null || value === undefined ? "" : String(value)
                edits[row.line] = { ...(edits[row.line] || {}), [column.name]: text }
                row.values[column.name] = text === "" ? null : text
                if (this.looksValid(column, text) && (!column.options || column.options.some(o => String(o.value) === text))) delete row.errors[column.name]
            })
            this.edits = edits
            this.recheck()
        },
        applySuggestion(row, column) {
            this.setCells([{ row, column, value: row.suggest[column.name] }])
        },
        applySuggestions() {
            this.bulk = null
            this.setCells(this.suggestions)
        },
        isEditing(row, column) {
            return !!this.editing && this.editing.line === row.line && this.editing.name === column.name
        },
        isEdited(row, column) {
            return !!this.edits[row.line] && column.name in this.edits[row.line]
        },
        startEdit(row, column) {
            if (!column.known || this.isEditing(row, column)) return
            this.commitEdit()
            const value = row.values[column.name]
            this.draft = value === null || value === undefined ? "" : String(value)
            this.editing = { line: row.line, name: column.name }
            this.$nextTick(() => {
                // A dropdown opens on focus; a text box selects its text, ready to be typed over
                const select = [].concat(this.$refs.cellSelect || [])[0]
                if (select) return select.$refs.search && select.$refs.search.focus()
                const input = [].concat(this.$refs.cellInput || [])[0]
                if (input) { input.focus(); input.select() }
            })
        },
        // The open editor (text box or dropdown), to scroll it into view
        editorEl() {
            const select = [].concat(this.$refs.cellSelect || [])[0]
            return select ? select.$el : [].concat(this.$refs.cellInput || [])[0]
        },
        // A column's choices for the dropdown; an optional column can also be emptied
        choiceOptions(column) {
            const options = column.options.map(o => ({ label: o.label, value: String(o.value) }))
            return column.required ? options : [{ label: "—", value: "" }, ...options]
        },
        filterChoice(option, label, search) {
            return `${label} ${option.value}`.toLowerCase().includes(search.toLowerCase())
        },
        pickChoice(value, step) {
            if (!this.editing) return
            this.draft = value === null || value === undefined ? "" : String(value)
            this.commitEdit(step)
        },
        // Tab keeps the highlighted choice and moves on (Shift+Tab back); Esc leaves the cell as it was
        choiceKeys(map, select) {
            return {
                ...map,
                9: event => {
                    event.preventDefault()
                    const option = select.filteredOptions[select.typeAheadPointer]
                    this.pickChoice(option && select.search ? option.value : this.draft, event.shiftKey ? -1 : 1)
                },
                27: event => {
                    event.preventDefault()
                    event.stopPropagation()
                    this.cancelEdit()
                },
            }
        },
        // The menu is moved to the page (the table would clip it): place it under the cell, or above when the
        // popup has no room below it (measured inside the popup, so the menu never hangs out of it)
        placeMenu(menu, select) {
            const box = select.$el.getBoundingClientRect()
            const popup = select.$el.closest(".vs-popup")
            const frame = popup ? popup.getBoundingClientRect() : { top: 0, bottom: window.innerHeight }
            const below = frame.bottom - box.bottom
            const above = box.top - frame.top
            const up = below < 260 && above > below
            menu.classList.add("imp__menu")
            menu.style.setProperty("--imp-left", `${Math.round(box.left)}px`)
            menu.style.setProperty("--imp-width", `${Math.round(Math.max(box.width, 200))}px`)
            menu.style.setProperty("--imp-top", up ? "auto" : `${Math.round(box.bottom + 4)}px`)
            menu.style.setProperty("--imp-bottom", up ? `${Math.round(window.innerHeight - box.top + 4)}px` : "auto")
            // As tall as the room it has (it scrolls), never past the popup's edge
            menu.style.setProperty("--imp-max", `${Math.round(Math.max(120, Math.min(260, (up ? above : below) - 12)))}px`)
        },
        // Keep the typed value (Enter, Tab, or leaving the cell); step moves to the next (1) or previous (-1) cell
        commitEdit(step) {
            if (!this.editing) return
            const { line, name } = this.editing
            this.editing = null
            const row = this.preview.rows.find(r => r.line === line)
            if (!row) return
            const before = row.values[name] === null || row.values[name] === undefined ? "" : String(row.values[name])
            const value = this.draft.trim()
            if (value !== before) {
                const wrong = !!row.errors[name]
                // Shown at once (a right value loses its red straight away); the check confirms it
                this.setCells([{ row, column: this.preview.columns.find(c => c.name === name), value }])
                if (wrong) this.offerBulk(name, before, value)
            }
            if (step) this.moveEdit(row, name, step)
        },
        // Other rows wrong in the same column with the same value can take the same fix in one click
        offerBulk(name, from, to) {
            const lines = this.preview.rows
                .filter(r => r.errors[name] && !this.isEdited(r, { name }) && String(r.values[name] ?? "") === from)
                .map(r => r.line)
            const column = this.preview.columns.find(c => c.name === name)
            this.bulk = lines.length ? { name, from, to: column && column.options ? this.optionLabel(column, to) : to, value: to, lines } : null
        },
        applyBulk() {
            const { name, value, lines } = this.bulk
            const column = this.preview.columns.find(c => c.name === name)
            this.bulk = null
            this.setCells(this.preview.rows.filter(r => lines.includes(r.line)).map(row => ({ row, column, value })))
        },
        // Show the wrong rows and open the first wrong cell of the column
        goToProblem(name) {
            this.filter = "error"
            this.$nextTick(() => {
                const row = this.visibleRows.find(r => r.errors[name])
                const column = this.preview.columns.find(c => c.name === name)
                if (!row || !column) return
                this.startEdit(row, column)
                this.$nextTick(() => {
                    const editor = this.editorEl()
                    if (editor) editor.scrollIntoView({ block: "nearest", inline: "center", behavior: "smooth" })
                })
            })
        },
        cancelEdit() {
            this.editing = null
        },
        // Next editable cell along the row, then on to the next row
        moveEdit(row, name, step) {
            const cells = []
            this.visibleRows.forEach(r => this.preview.columns.forEach(c => { if (c.known) cells.push([r, c]) }))
            const at = cells.findIndex(([r, c]) => r.line === row.line && c.name === name)
            const next = cells[at + step]
            if (at >= 0 && next) this.startEdit(next[0], next[1])
        },
        removeRow(row) {
            this.removeRows([row.line])
        },
        removeRepeats() {
            this.removeRows(this.repeats.map(row => row.line))
        },
        removeRows(lines) {
            if (this.editing && lines.includes(this.editing.line)) this.editing = null
            this.removed = [...this.removed, ...lines]
            if (this.bulk) {
                this.bulk.lines = this.bulk.lines.filter(line => !lines.includes(line))
                if (!this.bulk.lines.length) this.bulk = null
            }
            this.preview.rows = this.preview.rows.filter(r => !lines.includes(r.line))
            this.recheck()
        },
        undoEdits() {
            this.editing = null
            this.bulk = null
            this.edits = {}
            this.removed = []
            this.recheck()
        },
        async importFile() {
            if (this.pending) return
            this.importing = true
            try {
                // With fixes, the server writes them into the file first; the list's import reads that file
                let file = this.file
                if (this.editCount) {
                    const response = await axios.post("/api/v1/import/file", this.form(this.file), { responseType: "blob" })
                    file = new File([response.data], this.file.name.replace(/\.[^.]+$/, "") + ".xlsx", { type: response.headers["content-type"] })
                }
                const form = new FormData()
                form.append("file", file)
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
                this.$vs.notify({ text: this.message(error), color: "danger" })
            }
        },
        // Why the request failed, in the user's language (see errorMessage in axios.js)
        message(error) {
            return errorMessage(error, this.$t("report_error"))
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

<style>
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

        /* Fixing in place: a known cell shows it can be clicked; a fixed one keeps a mark */
        td {
            transition: background-color .35s ease, color .35s ease, box-shadow .2s ease;
        }

        td.is-editable {
            cursor: text;

            &:hover {
                box-shadow: inset 0 0 0 1px rgba(var(--vs-primary), .45);
            }
        }

        td.is-edited {
            box-shadow: inset 3px 0 0 rgb(var(--vs-primary));
        }

        td.is-editing {
            padding: 3px 4px;
            box-shadow: none;
        }
    }

    &__input {
        width: 100%;
        min-width: 110px;
        padding: 5px 8px;
        font: inherit;
        color: #2c2e45;
        border: 1px solid rgb(var(--vs-primary));
        border-radius: 7px;
        outline: none;
        background: #fff;
        box-shadow: 0 0 0 3px rgba(var(--vs-primary), .15);
    }

    &__select {
        min-width: 160px;
    }

    &__opt {
        display: flex;
        align-items: baseline;
        justify-content: space-between;
        gap: 12px;

        small {
            font-size: .72rem;
            color: #9a9cb3;
            font-variant-numeric: tabular-nums;
        }
    }

    &__input.is-invalid {
        border-color: rgb(234, 84, 85);
        box-shadow: 0 0 0 3px rgba(234, 84, 85, .15);
    }

    &__suggest {
        margin-left: 4px;
        padding: 0 6px;
        font: inherit;
        font-weight: 600;
        color: rgb(0, 112, 200);
        border: none;
        border-radius: 999px;
        background: rgba(0, 143, 251, .12);
        cursor: pointer;

        &:hover {
            background: rgba(0, 143, 251, .22);
        }
    }

    &__banner.is-repeat {
        align-items: center;
        color: rgb(180, 110, 0);
        background: rgba(255, 159, 67, .12);

        > span {
            flex: 1;
        }

        .feather-icon {
            margin-top: 0;
        }
    }

    &__banner.is-auto {
        align-items: center;
        color: rgb(40, 140, 80);
        background: rgba(40, 199, 111, .1);

        > span {
            flex: 1;
        }

        .feather-icon {
            margin-top: 0;
        }
    }

    &__remove {
        width: 1%;
        padding: 4px 6px !important;
        text-align: center !important;

        button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 26px;
            height: 26px;
            padding: 0;
            color: #9a9cb3;
            border: none;
            border-radius: 7px;
            background: none;
            cursor: pointer;

            &:hover {
                color: rgb(234, 84, 85);
                background: rgba(234, 84, 85, .1);
            }
        }

        .feather-icon {
            display: flex;
        }
    }

    &__problems {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 6px;
        font-size: .82rem;
        color: #6e7091;
    }

    &__chip {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 4px 6px 4px 10px;
        font-family: inherit;
        font-size: .8rem;
        font-weight: 600;
        color: rgb(234, 84, 85);
        border: 1px solid rgba(234, 84, 85, .3);
        border-radius: 999px;
        background: rgba(234, 84, 85, .06);
        cursor: pointer;
        transition: background var(--tab-tap) ease;

        strong {
            min-width: 20px;
            padding: 0 6px;
            font-size: .72rem;
            line-height: 18px;
            text-align: center;
            color: #fff;
            border-radius: 999px;
            background: rgb(234, 84, 85);
        }

        &:hover {
            background: rgba(234, 84, 85, .12);
        }
    }

    &__banner.is-bulk {
        align-items: center;
        color: rgb(0, 112, 200);
        background: rgba(0, 143, 251, .08);

        > span {
            flex: 1;
        }

        .feather-icon {
            margin-top: 0;
        }
    }

    &__close {
        display: inline-flex;
        padding: 4px;
        color: inherit;
        border: none;
        border-radius: 6px;
        background: none;
        cursor: pointer;

        &:hover {
            background: rgba(0, 143, 251, .12);
        }
    }

    &__edits {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        font-size: .85rem;
        color: #6e7091;
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

        &.is-busy {
            color: #8a8ca5;
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

/* Bars slide in, rows fade out (removed, or fixed out of the "problems" group) */
.imp-pop-enter-active,
.imp-pop-leave-active {
    transition: opacity .25s ease, transform .25s ease;
}

.imp-pop-enter-from,
.imp-pop-leave-to {
    opacity: 0;
    transform: translateY(-6px);
}

.imp-row-leave-active {
    transition: opacity .3s ease;
}

.imp-row-leave-to {
    opacity: 0;
}

/* A cell's dropdown menu lives on the page (moved out of the scrolling table), placed by placeMenu() */
.vs__dropdown-menu.imp__menu {
    position: fixed !important;
    top: var(--imp-top) !important;
    bottom: var(--imp-bottom) !important;
    left: var(--imp-left) !important;
    width: var(--imp-width) !important;
    min-width: 0;
    max-height: var(--imp-max, 260px) !important;
    margin: 0;
    z-index: 530001; /* above popups (.con-vs-popup is 53000), like the app's other popup menus */
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
    &__input { background: #10163a; color: #fff; }
}
</style>

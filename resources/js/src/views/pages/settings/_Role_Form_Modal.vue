<template>
    <div>
        <vs-popup class="d-modal d-modal--xl perm-modal" classContent="perm-modal__content" :title="$t('perm_title')"
            v-model:active="showModalForm">
            <div class="dt-list perm">
                <!-- Toolbar -->
                <div class="dt-toolbar">
                    <div class="perm__title">
                        <span class="perm__icon"><feather-icon icon="ShieldIcon" svgClasses="h-5 w-5" /></span>
                        <div class="dt-toolbar__title">
                            <h3>{{ roleName || '—' }}</h3>
                            <p v-if="superAdmin">{{ $t('perm_super_admin') }}</p>
                            <p v-else>{{ $t('perm_granted', { n: grantedCount, total: allNames.length }) }}</p>
                        </div>
                    </div>
                    <div class="dt-toolbar__actions perm__tools">
                        <div class="dt-cell dt-search">
                            <span class="dt-search__icon"><feather-icon icon="SearchIcon" svgClasses="h-4 w-4" /></span>
                            <input v-model.trim="keyword" type="text" :placeholder="$t('perm_search')">
                        </div>
                        <button v-if="!superAdmin" type="button" class="dt-btn" @click="setMany(visibleNames, !allVisibleOn)">
                            <feather-icon :icon="allVisibleOn ? 'XSquareIcon' : 'CheckSquareIcon'" svgClasses="h-4 w-4" />
                            <span>{{ allVisibleOn ? $t('perm_clear_all') : $t('perm_select_all') }}</span>
                        </button>
                    </div>
                </div>

                <!-- Matrix: modules × actions, built from the permission names (action_module) -->
                <div class="dt-table-wrap perm__wrap">
                    <table v-if="!loading && rows.length" class="dt-table perm__table">
                        <thead>
                            <tr>
                                <th class="perm__module-col">{{ $t('perm_module') }}</th>
                                <th v-for="action in actions" :key="action" class="perm__action-col">
                                    <label class="perm__head" :title="$t('perm_toggle_column')">
                                        <input type="checkbox" class="perm__check" :checked="columnState(action) === 'all'" :disabled="superAdmin"
                                            :indeterminate.prop="columnState(action) === 'some'"
                                            @change="setMany(columnNames(action), $event.target.checked)">
                                        <span>{{ actionLabel(action) }}</span>
                                    </label>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="row in rows" :key="row.module">
                                <td class="perm__module-col">
                                    <label class="perm__module">
                                        <input type="checkbox" class="perm__check" :checked="rowState(row) === 'all'" :disabled="superAdmin"
                                            :indeterminate.prop="rowState(row) === 'some'"
                                            @change="setMany(row.names, $event.target.checked)">
                                        <span class="perm__module-text">
                                            <strong>{{ moduleLabel(row.module) }}</strong>
                                            <small>{{ rowCount(row) }} / {{ row.names.length }}</small>
                                        </span>
                                    </label>
                                </td>
                                <td v-for="action in actions" :key="action" class="perm__action-col">
                                    <input v-if="row.cells[action]" type="checkbox" class="perm__check" :disabled="superAdmin"
                                        :checked="!!granted[row.cells[action]]" :title="row.cells[action]"
                                        @change="setOne(row.cells[action], $event.target.checked)">
                                    <span v-else class="perm__none">—</span>
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    <div v-else-if="loading" class="perm__loading">
                        <span v-for="n in 6" :key="n" />
                    </div>

                    <div v-else class="dt-empty">
                        <span class="dt-empty__icon"><feather-icon icon="SearchIcon" svgClasses="h-6 w-6" /></span>
                        <strong>{{ $t('perm_empty') }}</strong>
                    </div>
                </div>

                <!-- Footer -->
                <div class="perm__footer">
                    <span class="perm__changes">
                        <template v-if="changedCount">{{ $t('perm_changed', { n: changedCount }) }}</template>
                    </span>
                    <button type="button" class="dt-btn" @click="showModalForm = false">{{ $t('perm_cancel') }}</button>
                    <button v-if="canSave && !superAdmin" type="button" class="dt-btn dt-btn--primary" :disabled="saving || loading" @click="save_changes">
                        <feather-icon :icon="saving ? 'LoaderIcon' : 'SaveIcon'" svgClasses="h-4 w-4" />
                        <span>{{ $t('perm_save') }}</span>
                    </button>
                </div>
            </div>
        </vs-popup>
    </div>
</template>

<script>
import { permissionActionLabel, permissionModuleLabel } from "@/i18n/i18n.js"
import { can } from "@/acl/access.js"
import axios from "@/axios.js"
import apiConfig from "@/apiConfig.js"

// Column order for known actions; any other action found in the data is appended after these
const ACTION_ORDER = ['list', 'show', 'create', 'edit', 'update', 'delete', 'download', 'import', 'export', 'print', 'execute']

export default {
    data() {
        return {
            api: apiConfig._apiRole,
            showModalForm: false,
            loading: false,
            saving: false,
            role: null,
            superAdmin: false, // Super Admin always has every permission: shown all on and locked
            modules: [],     // [{ name, permission: [{ name }] }] from the API
            granted: {},     // { permission_name: true|false }
            original: {},    // granted as loaded, to count unsaved changes
            keyword: '',
        }
    },
    computed: {
        canSave() {
            return can('role_permissions')
        },
        roleName() {
            return this.role && this.role.name
        },
        // One row per module; each permission name "action_module" lands in its action's column
        allRows() {
            return this.modules.map(module => {
                const cells = {}
                const names = []
                ;(module.permission || []).forEach(({ name }) => {
                    if (!name) return
                    const suffix = '_' + module.name
                    const action = name.endsWith(suffix) ? name.slice(0, -suffix.length) : name.split('_')[0]
                    cells[action] = name
                    names.push(name)
                })
                return { module: module.name, cells, names }
            })
        },
        rows() {
            const keyword = this.keyword.toLowerCase()
            if (!keyword) return this.allRows
            return this.allRows.filter(row =>
                (row.module + ' ' + this.moduleLabel(row.module)).toLowerCase().includes(keyword))
        },
        actions() {
            const found = new Set()
            this.allRows.forEach(row => Object.keys(row.cells).forEach(action => found.add(action)))
            const known = ACTION_ORDER.filter(action => found.has(action))
            const others = [...found].filter(action => !ACTION_ORDER.includes(action)).sort()
            return known.concat(others)
        },
        allNames() {
            return this.allRows.reduce((list, row) => list.concat(row.names), [])
        },
        visibleNames() {
            return this.rows.reduce((list, row) => list.concat(row.names), [])
        },
        allVisibleOn() {
            return this.visibleNames.length > 0 && this.visibleNames.every(name => this.granted[name])
        },
        grantedCount() {
            return this.allNames.filter(name => this.granted[name]).length
        },
        changedCount() {
            return this.allNames.filter(name => !!this.granted[name] !== !!this.original[name]).length
        },
    },
    methods: {
        showForm(data) {
            this.showModalForm = true
            this.loading = true
            this.keyword = ''
            this.role = data
            this.superAdmin = false
            axios.get(this.api + "/" + data.id)
                .then(({ data: response }) => {
                    const payload = response && response.data ? response.data : {}
                    this.role = payload.data || data
                    this.modules = payload.permission_lists || []
                    this.superAdmin = !!payload.is_super_admin
                    // The API sends an object of name → boolean (or an empty array when nothing is granted)
                    const granted = payload.permission_by_roles && !Array.isArray(payload.permission_by_roles)
                        ? payload.permission_by_roles : {}
                    const normalized = {}
                    Object.keys(granted).forEach(name => { normalized[name] = !!granted[name] && granted[name] !== '0' })
                    if (this.superAdmin) this.modules.forEach(m => (m.permission || []).forEach(({ name }) => { normalized[name] = true }))
                    this.granted = normalized
                    this.original = { ...normalized }
                })
                .catch(() => this.notify('Error, while trying to do action or no permission allow!', 'danger'))
                .finally(() => { this.loading = false })
        },
        setOne(name, value) {
            this.granted[name] = value
        },
        setMany(names, value) {
            names.forEach(name => this.granted[name] = value)
        },
        columnNames(action) {
            return this.rows.map(row => row.cells[action]).filter(Boolean)
        },
        state(names) {
            const on = names.filter(name => this.granted[name]).length
            return on === 0 ? 'none' : on === names.length ? 'all' : 'some'
        },
        columnState(action) {
            return this.state(this.columnNames(action))
        },
        rowState(row) {
            return this.state(row.names)
        },
        rowCount(row) {
            return row.names.filter(name => this.granted[name]).length
        },
        // Shared with every screen that shows permissions (see i18n.js)
        actionLabel: permissionActionLabel,
        moduleLabel: permissionModuleLabel,
        save_changes() {
            this.saving = true
            // Send every known permission: ones not shown in the matrix keep their loaded value
            const permission = { ...this.original }
            Object.keys(this.granted).forEach(name => { permission[name] = !!this.granted[name] })
            axios.post(this.api + '/set_permission', { role_info: this.role, permission })
                .then(({ data }) => {
                    if (data.success === false) {
                        this.notify(data.message, 'danger')
                    } else {
                        this.original = { ...this.granted }
                        this.notify(data.message, 'primary')
                    }
                })
                .catch(() => this.notify('Error, while trying to do action or no permission allow!', 'danger'))
                .finally(() => { this.saving = false })
        },
        notify(text, color) {
            this.$vs.notify({ title: 'Message', text, iconPack: 'feather', icon: 'icon-check-circle', color, position: 'top-right' })
        },
    },
}
</script>

<style>
.perm-modal .perm-modal__content {
    padding-bottom: 0 !important;
}

.perm {
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
        color: rgb(var(--vs-primary));
        background: rgba(var(--vs-primary), .12);

        .feather-icon {
            display: flex;
        }
    }

    &__tools {
        flex: 1 1 420px;
        justify-content: flex-end;

        .dt-search {
            flex: 1 1 220px;
            max-width: 300px;
        }
    }

    /* Matrix scrolls inside the popup; header row and module column stay in view */
    &__wrap {
        max-height: calc(100vh - 300px);
        overflow: auto;
    }

    &__table {
        border-collapse: separate;
        border-spacing: 0;

        th {
            position: sticky;
            top: 0;
            z-index: 2;
        }

        th,
        td {
            border-bottom: 1px solid #f1f2f6;
        }

        tbody tr:hover td {
            background: #fafbfd;
        }
    }

    &__module-col {
        position: sticky;
        left: 0;
        z-index: 1;
        min-width: 220px;
        background: #fff;
        box-shadow: 1px 0 0 #eceef4;

        th & {
            z-index: 3;
            background: #f6f7fb;
        }
    }

    &__action-col {
        min-width: 86px;
        text-align: center !important;
    }

    &__head {
        display: inline-flex;
        flex-direction: column;
        align-items: center;
        gap: 6px;
        font-size: .75rem;
        font-weight: 700;
        text-transform: capitalize;
        cursor: pointer;
    }

    &__module {
        display: flex;
        align-items: center;
        gap: 10px;
        cursor: pointer;
    }

    &__module-text {
        display: flex;
        flex-direction: column;
        line-height: 1.35;

        strong {
            font-size: .88rem;
            font-weight: 600;
            color: #2c2e45;
            text-transform: capitalize;
        }

        small {
            font-size: .72rem;
            color: #8a8ca5;
            font-variant-numeric: tabular-nums;
        }
    }

    /* Native checkbox restyled to the theme colour (supports the indeterminate state for "some") */
    &__check {
        appearance: none;
        -webkit-appearance: none;
        position: relative;
        flex-shrink: 0;
        width: 20px;
        height: 20px;
        margin: 0;
        padding: 0 !important;
        border: 1.5px solid #c9ccd6 !important;
        border-radius: 6px !important;
        background: #fff !important;
        cursor: pointer;
        transition: background .15s ease, border-color .15s ease, box-shadow .15s ease;
        vertical-align: middle;

        &:hover {
            border-color: rgb(var(--vs-primary)) !important;
        }

        &:focus-visible {
            outline: 2px solid rgb(var(--vs-primary));
            outline-offset: 2px;
        }

        &:checked,
        &:indeterminate {
            border-color: rgb(var(--vs-primary)) !important;
            background: rgb(var(--vs-primary)) !important;
        }

        &:checked::after {
            content: '';
            position: absolute;
            left: 6px;
            top: 2px;
            width: 5px;
            height: 10px;
            border: solid #fff;
            border-width: 0 2px 2px 0;
            transform: rotate(45deg);
        }

        &:indeterminate::after {
            content: '';
            position: absolute;
            left: 4px;
            right: 4px;
            top: 8px;
            height: 2px;
            border-radius: 2px;
            background: #fff;
        }
    }

    &__none {
        color: #d3d5dc;
    }

    &__loading {
        display: grid;
        gap: 10px;
        padding: 16px;

        span {
            height: 18px;
            border-radius: 6px;
            background: linear-gradient(90deg, #f1f2f5 25%, #e7e9ee 50%, #f1f2f5 75%);
            background-size: 200% 100%;
            animation: dt-shimmer 1.2s infinite;
        }
    }

    /* Sticky footer inside the popup */
    &__footer {
        position: sticky;
        bottom: 0;
        z-index: 4;
        display: flex;
        align-items: center;
        gap: 10px;
        margin: 16px -24px 0;
        padding: 14px 24px;
        border-top: 1px solid #eef0f4;
        background: rgba(255, 255, 255, .96);
        backdrop-filter: blur(6px);
    }

    &__changes {
        flex: 1;
        font-size: .82rem;
        color: #d97706;
    }
}

.theme-dark .perm {
    &__module-col {
        background: #10163a;

        th & {
            background: #262c49;
        }
    }

    &__module-text strong {
        color: #fff;
    }

    &__check {
        background: #10163a !important;
        border-color: #414561 !important;
    }

    &__footer {
        background: rgba(38, 44, 73, .96);
        border-color: #414561;
    }
}
</style>

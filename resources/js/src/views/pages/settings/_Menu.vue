<template>
    <!-- dt-list: the shared list-page styles (buttons, empty state), as on every other list page -->
    <div class="mm dt-list">
        <!-- Header -->
        <div class="mm__head">
            <div class="min-w-0">
                <h2>{{ $t('mm_title') }}</h2>
                <p>{{ $t('mm_subtitle') }}</p>
                <small v-if="items.length">{{ $t('mm_counts', { shown: counts.shown, hidden: counts.hidden }) }}</small>
            </div>
            <button v-if="can.create" type="button" class="dt-btn dt-btn--primary" @click="openForm()">
                <feather-icon icon="MenuIcon" svgClasses="h-4 w-4" />
                <span>{{ $t('mm_add') }}</span>
            </button>
        </div>

        <div class="mm__layout">
            <!-- Tree -->
            <section class="mm__tree" :class="{ 'is-busy': busy }">
                <template v-if="loading && !items.length">
                    <div v-for="n in 6" :key="'s' + n" class="mm-row mm-row--skeleton"><span /><span /><span /></div>
                </template>

                <div v-else-if="!items.length" class="dt-empty">
                    <span class="dt-empty__icon"><feather-icon icon="MenuIcon" svgClasses="h-6 w-6" /></span>
                    <strong>{{ $t('mm_empty') }}</strong>
                </div>

                <template v-for="row in rows" :key="row.key">
                    <!-- "+ Add" under a section / group, after its last item -->
                    <div v-if="row.add" class="mm-add" :style="{ '--depth': row.depth }">
                        <button type="button" @click="openForm({ parent_id: row.parent.id })">
                            + {{ $t('mm_add_child', { name: label(row.parent) }) }}
                        </button>
                    </div>

                    <div v-else class="mm-row" :class="['mm-row--' + row.node.type, { 'is-off': row.off }]" :style="{ '--depth': row.depth }">
                        <span class="mm-row__no">{{ row.index + 1 }}</span>
                        <span class="mm-row__icon"><feather-icon :icon="iconOf(row.node)" svgClasses="h-5 w-5" /></span>

                        <div class="mm-row__body">
                            <div class="mm-row__title">
                                <strong>{{ label(row.node) }}</strong>
                                <span v-if="row.node.type !== 'link'" class="mm-badge">{{ $t('menu_type_' + row.node.type) }}</span>
                                <span v-if="!row.node.is_active" class="mm-badge mm-badge--warn">
                                    <feather-icon icon="EyeOffIcon" svgClasses="h-3 w-3" />{{ $t('mm_hidden') }}
                                </span>
                                <span v-else-if="row.hiddenByParent" class="mm-badge">{{ $t('mm_hidden_by_parent') }}</span>
                                <span v-if="isAlwaysOn(row.node)" class="mm-badge" :title="$t('mm_always_on_hint')">
                                    <feather-icon icon="LockIcon" svgClasses="h-3 w-3" />{{ $t('mm_always_on') }}
                                </span>
                                <span v-if="row.node.permission" class="mm-badge mm-badge--access" :title="$t('mm_visible_to_hint') + ' (' + row.node.permission + ')'">
                                    <feather-icon icon="LockIcon" svgClasses="h-3 w-3" />{{ permissionText(row.node.permission) }}
                                </span>
                            </div>
                            <div class="mm-row__meta">
                                <code>{{ row.node.slug }}</code>
                                <code v-if="row.node.url">{{ row.node.url }}</code>
                                <span v-if="row.node.type !== 'link'">{{ $t('mm_children', { n: childrenOf(row.node.id).length }) }}</span>
                            </div>
                        </div>

                        <div class="mm-row__actions">
                            <template v-if="can.update">
                                <button type="button" class="mm-act" :title="$t('mm_move_up')" :disabled="row.index === 0 || busy" @click="move(row, -1)">
                                    <feather-icon icon="ChevronUpIcon" svgClasses="h-4 w-4" />
                                </button>
                                <button type="button" class="mm-act" :title="$t('mm_move_down')" :disabled="row.index === row.count - 1 || busy" @click="move(row, 1)">
                                    <feather-icon icon="ChevronDownIcon" svgClasses="h-4 w-4" />
                                </button>
                                <button type="button" class="mm-act" :title="$t('mm_edit')" @click="openForm(row.node)">
                                    <feather-icon icon="Edit3Icon" svgClasses="h-4 w-4" />
                                </button>
                            </template>
                            <button v-if="can.delete" type="button" class="mm-act mm-act--danger" :disabled="isLocked(row.node) || childrenOf(row.node.id).length > 0"
                                :title="isLocked(row.node) ? lockedHint(row.node) : childrenOf(row.node.id).length ? $t('mm_delete_blocked') : $t('mm_delete')" @click="confirmDelete(row.node)">
                                <feather-icon icon="Trash2Icon" svgClasses="h-4 w-4" />
                            </button>
                            <vs-switch v-if="can.update" :modelValue="!!row.node.is_active" :disabled="isLocked(row.node)"
                                :class="{ 'is-locked': isLocked(row.node) }" :title="isLocked(row.node) ? lockedHint(row.node) : $t('mm_show_hide')"
                                @update:modelValue="toggle(row.node, $event)" />
                        </div>
                    </div>
                </template>
            </section>

            <!-- Preview: the sidebar as a role sees it -->
            <aside class="mm__preview">
                <h3>{{ $t('mm_preview') }}</h3>
                <p>{{ $t('mm_preview_sub') }}</p>
                <label>{{ $t('mm_view_as') }}</label>
                <v-select v-model="previewRole" :options="roleOptions" :clearable="false" :dir="$vs.rtl ? 'rtl' : 'ltr'" @update:modelValue="loadPreview">
                    <template #selected-option="{ label: roleName }">
                        <span class="mm-role"><feather-icon icon="UserIcon" svgClasses="h-4 w-4" />{{ roleName }}</span>
                    </template>
                </v-select>

                <!-- Colors read from the real sidebar (readSidebarColors), so the preview always matches the theme -->
                <div ref="side" class="mm-side" :class="{ 'is-loading': previewLoading }" :style="sideStyle">
                    <p v-if="!preview.length && !previewLoading" class="mm-side__empty">{{ $t('mm_preview_empty') }}</p>
                    <template v-for="item in preview" :key="item.slug">
                        <template v-if="item.header !== undefined">
                            <span class="mm-side__header">{{ label(item) }}</span>
                            <template v-for="child in item.items" :key="child.slug">
                                <div class="mm-side__item" :class="{ 'is-active': isCurrent(child) }"><feather-icon :icon="child.icon || defaultIcon(child)" svgClasses="h-4 w-4" />{{ label(child) }}</div>
                                <div v-for="sub in child.submenu || []" :key="sub.slug" class="mm-side__item mm-side__item--sub" :class="{ 'is-active': isCurrent(sub) }">{{ label(sub) }}</div>
                            </template>
                        </template>
                        <template v-else>
                            <div class="mm-side__item" :class="{ 'is-active': isCurrent(item) }"><feather-icon :icon="item.icon || defaultIcon(item)" svgClasses="h-4 w-4" />{{ label(item) }}</div>
                            <div v-for="sub in item.submenu || []" :key="sub.slug" class="mm-side__item mm-side__item--sub" :class="{ 'is-active': isCurrent(sub) }">{{ label(sub) }}</div>
                        </template>
                    </template>
                </div>
                <small>{{ $t('mm_preview_note') }}</small>
            </aside>
        </div>

        <!-- Add / edit -->
        <vs-popup class="d-modal d-modal--md" :title="form.id ? $t('mm_form_edit') : $t('mm_form_add')" v-model:active="form.open">
            <div class="mm-form">
                <label>{{ $t('mm_field_type') }}</label>
                <div class="mm-seg">
                    <button v-for="type in types" :key="type" type="button" :class="{ 'is-active': form.type === type }" @click="form.type = type">
                        <feather-icon :icon="defaultIcon({ type })" svgClasses="h-4 w-4" />{{ $t('menu_type_' + type) }}
                    </button>
                </div>
                <small class="mm-form__hint">{{ $t('mm_type_hint_' + form.type) }}</small>

                <label>{{ $t('mm_field_parent') }}</label>
                <v-select v-model="form.parent" :options="parentOptions" :placeholder="$t('mm_parent_none')" :dir="$vs.rtl ? 'rtl' : 'ltr'" />

                <label>{{ $t('mm_field_name') }} <span class="text-danger">*</span></label>
                <vs-input v-model="form.name" class="w-full" />

                <label>{{ $t('mm_field_i18n') }}</label>
                <vs-input v-model="form.i18n" class="w-full" placeholder="e.g. menu_members" />
                <small v-if="form.i18n" class="mm-form__hint">{{ $te(form.i18n) ? $t(form.i18n) : $t('mm_i18n_missing') }}</small>

                <template v-if="form.type === 'link'">
                    <label>{{ $t('mm_field_url') }} <span class="text-danger">*</span></label>
                    <vs-input v-model="form.url" class="w-full" placeholder="/officer" />
                </template>

                <label>{{ $t('mm_field_icon') }}</label>
                <div class="mm-icon-field">
                    <span class="mm-row__icon"><feather-icon :icon="form.icon || defaultIcon(form)" svgClasses="h-5 w-5" /></span>
                    <vs-input v-model.trim="form.icon" class="w-full" placeholder="HomeIcon" />
                </div>
                <div class="mm-icons">
                    <button v-for="icon in ICONS" :key="icon" type="button" :class="{ 'is-active': form.icon === icon }" :title="icon" @click="form.icon = icon">
                        <feather-icon :icon="icon" svgClasses="h-4 w-4" />
                    </button>
                </div>

                <label>{{ $t('mm_field_permission') }}</label>
                <v-select v-model="form.permission" :options="permissionOptions" :placeholder="$t('mm_permission_none')" :dir="$vs.rtl ? 'rtl' : 'ltr'" />

                <div class="mm-form__switch">
                    <vs-switch v-model="form.is_active" />
                    <span>{{ $t('mm_field_active') }}</span>
                </div>

                <div class="mm-form__actions">
                    <button type="button" class="dt-btn" @click="form.open = false">{{ $t('dt_cancel') }}</button>
                    <button type="button" class="dt-btn dt-btn--primary" :disabled="busy || !form.name" @click="save">{{ $t('btn_save') }}</button>
                </div>
            </div>
        </vs-popup>
    </div>
</template>

<script>
import axios from "@/axios.js";
import apiConfig from "@/apiConfig.js";
import vSelect from "vue-select";
import { permissionLabel } from "@/i18n/i18n.js";

// Quick picks in the icon field (any Feather icon name works, e.g. "HomeIcon")
const ICONS = ['HomeIcon', 'GridIcon', 'UsersIcon', 'UserIcon', 'FileTextIcon', 'FolderIcon', 'DollarSignIcon', 'CreditCardIcon',
    'TrendingUpIcon', 'TrendingDownIcon', 'CheckCircleIcon', 'BellIcon', 'BarChart2Icon', 'ActivityIcon', 'SettingsIcon',
    'ShieldIcon', 'LayersIcon', 'MenuIcon', 'BookIcon', 'BriefcaseIcon', 'CalendarIcon', 'ArchiveIcon', 'MailIcon', 'HelpCircleIcon']

// Where an item may sit (same rules as the API): sections at the top; groups at the top or in a section; links anywhere but in a link
const PARENT_TYPES = { section: [], group: ['section'], link: ['section', 'group'] }

// Administration › Menus: the sidebar as a tree (order, show / hide, add, edit, delete) with a live preview per role
export default {
    components: { 'v-select': vSelect },
    data() {
        return {
            api: apiConfig._apiMenu,
            ICONS,
            items: [],
            types: ['section', 'group', 'link'],
            permissions: [],
            roles: [],
            locked: [], // always-on items and the sections / groups holding them (from the API)
            can: { create: false, update: false, delete: false },
            loading: false,
            busy: false,
            previewRole: null,
            preview: [],
            previewLoading: false,
            sideStyle: {}, // the real sidebar's colors as CSS variables
            form: this.emptyForm(),
        }
    },
    computed: {
        byParent() {
            const map = {}
            this.items.forEach(item => (map[item.parent_id || 0] = map[item.parent_id || 0] || []).push(item))
            Object.values(map).forEach(list => list.sort((a, b) => a.order_level - b.order_level || a.id - b.id))
            return map
        },
        // The tree flattened in sidebar order, with an "+ Add" row closing every section / group
        rows() {
            const rows = []
            const walk = (parentId, depth, parentOff) => {
                const list = this.byParent[parentId || 0] || []
                list.forEach((node, index) => {
                    const off = parentOff || !node.is_active
                    rows.push({ key: 'n' + node.id, node, depth, index, count: list.length, off, hiddenByParent: parentOff && node.is_active })
                    if (node.type !== 'link') {
                        walk(node.id, depth + 1, off)
                        if (this.can.create) rows.push({ key: 'a' + node.id, add: true, parent: node, depth: depth + 1 })
                    }
                })
            }
            walk(null, 0, false)
            return rows
        },
        counts() {
            const hidden = this.rows.filter(row => row.node && row.off).length
            return { shown: this.items.length - hidden, hidden }
        },
        roleOptions() {
            return this.roles.map(role => ({ label: role.name, value: role.id }))
        },
        permissionOptions() {
            return this.permissions.map(p => ({ label: permissionLabel(p.value, p.module), value: p.value }))
        },
        // Parents allowed for the type being edited (never the item itself or one of its own children)
        parentOptions() {
            const allowed = PARENT_TYPES[this.form.type] || []
            const own = new Set(this.form.id ? [this.form.id, ...this.descendants(this.form.id)] : [])
            return this.items
                .filter(item => allowed.includes(item.type) && !own.has(item.id))
                .map(item => {
                    const parent = this.items.find(p => p.id === item.parent_id)
                    return { label: (parent ? this.label(parent) + ' › ' : '') + this.label(item), value: item.id }
                })
        },
    },
    watch: {
        // The sidebar changes color with the theme: read it again once the new styles are applied
        '$store.state.theme'() {
            setTimeout(this.readSidebarColors, 350)
        },
        '$store.state.themePrimaryColor'() {
            setTimeout(this.readSidebarColors, 350)
        },
        // A type that can't sit under the chosen parent clears the parent
        'form.type'() {
            if (this.form.parent && !this.parentOptions.some(o => o.value === this.form.parent.value)) this.form.parent = null
        },
    },
    methods: {
        emptyForm() {
            return { open: false, id: null, type: 'link', parent: null, name: '', i18n: '', url: '', icon: '', permission: null, is_active: true }
        },
        isLocked(node) {
            return this.locked.includes(node.id)
        },
        // The locked page itself (a link); its sections / groups are locked only because they hold it
        isAlwaysOn(node) {
            return node.type === 'link' && this.isLocked(node)
        },
        lockedHint(node) {
            return this.isAlwaysOn(node) ? this.$t('mm_always_on_hint') : this.$t('mm_locked_parent')
        },
        // The page being viewed is shown active, as the real sidebar does
        isCurrent(item) {
            return !!item.url && item.url === this.$route.path
        },
        /**
         * Copy the colors of the sidebar on screen (background, text, section titles, active page pill, corners)
         * into CSS variables of the preview. Whatever the theme or a later restyle, the preview looks the same;
         * when the sidebar isn't on screen the preview keeps its own fallback colors.
         */
        readSidebarColors() {
            const side = document.querySelector('.v-nav-menu .vs-sidebar')
            if (!side) return
            const css = el => (el ? getComputedStyle(el) : null)
            const empty = value => !value || value === 'none' || value === 'transparent' || value === 'rgba(0, 0, 0, 0)'
            const style = {}

            // Background: the first layer of the sidebar that actually paints one (image / gradient, else color)
            for (const el of [side, side.querySelector('.nav-shell'), side.querySelector('.vs-sidebar--items')]) {
                const c = css(el)
                if (!c) continue
                const bg = !empty(c.backgroundImage) ? c.backgroundImage : c.backgroundColor
                if (!empty(bg)) { style['--mm-side-bg'] = bg; break }
            }
            const sideCss = css(side)
            if (sideCss.borderRadius && sideCss.borderRadius !== '0px') style['--mm-side-radius'] = sideCss.borderRadius

            const link = css(side.querySelector('.vs-sidebar--item a:not(.router-link-active) span') || side.querySelector('.vs-sidebar--item a'))
            if (link) style['--mm-side-text'] = link.color
            const header = css(side.querySelector('.navigation-header'))
            if (header) style['--mm-side-muted'] = header.color

            const active = side.querySelector('.vs-sidebar--item .router-link-active')
            const activeCss = css(active)
            if (activeCss) {
                if (!empty(activeCss.backgroundColor)) style['--mm-side-active-bg'] = activeCss.backgroundColor
                if (!empty(activeCss.boxShadow)) style['--mm-side-active-shadow'] = activeCss.boxShadow
                const activeText = css(active.querySelector('span') || active)
                if (activeText) style['--mm-side-active-text'] = activeText.color
            }
            this.sideStyle = style
        },
        label(item) {
            return item.i18n && this.$te(item.i18n) ? this.$t(item.i18n) : (item.name || item.header || '')
        },
        defaultIcon(item) {
            return { section: 'LayersIcon', group: 'FolderIcon' }[item.type] || (item.submenu ? 'FolderIcon' : 'CircleIcon')
        },
        iconOf(node) {
            return node.icon || this.defaultIcon(node)
        },
        childrenOf(id) {
            return this.byParent[id] || []
        },
        descendants(id) {
            return this.childrenOf(id).flatMap(child => [child.id, ...this.descendants(child.id)])
        },
        permissionText(name) {
            const p = this.permissions.find(x => x.value === name)
            return permissionLabel(name, p && p.module)
        },
        notify(text, color = 'success') {
            this.$vs.notify({ text, color })
        },
        errorText(error) {
            const message = error && error.response && error.response.data && error.response.data.message
            if (message && typeof message === 'object') return Object.values(message).flat().join(' ')
            return message || this.$t('report_error')
        },
        // After a change: reload the tree and the preview, and tell the real sidebar to refresh
        async refresh() {
            await this.load()
            this.loadPreview()
            window.dispatchEvent(new Event('menu-changed'))
        },
        async load() {
            this.loading = true
            try {
                const { data } = await axios.get(this.api + '/manage')
                this.items = data.data || []
                this.types = data.types || this.types
                this.permissions = data.permission || []
                this.roles = data.roles || []
                this.locked = data.locked || []
                this.can = data.can || this.can
                if (!this.previewRole && this.roleOptions.length) this.previewRole = this.roleOptions[0]
            } catch (error) {
                this.notify(this.errorText(error), 'danger')
            }
            this.loading = false
        },
        async loadPreview() {
            if (!this.previewRole) return
            this.previewLoading = true
            try {
                const { data } = await axios.get(this.api + '/preview', { params: { role_id: this.previewRole.value } })
                this.preview = data.data || []
            } catch (error) {
                this.preview = []
            }
            this.previewLoading = false
        },
        // Up / down: swap with the neighbour, then save the new order of that level
        async move(row, step) {
            const list = [...this.childrenOf(row.node.parent_id)]
            const to = row.index + step
            if (to < 0 || to >= list.length) return
            ;[list[row.index], list[to]] = [list[to], list[row.index]]
            list.forEach((item, i) => (item.order_level = (i + 1) * 10)) // shows the new order straight away
            this.busy = true
            try {
                await axios.post(this.api + '/reorder', { parent_id: row.node.parent_id, ids: list.map(item => item.id) })
                await this.refresh()
            } catch (error) {
                this.notify(this.errorText(error), 'danger')
                await this.load()
            }
            this.busy = false
        },
        async toggle(node, value) {
            node.is_active = value
            try {
                await axios.post(`${this.api}/${node.id}/active`, { is_active: value })
                this.refresh()
            } catch (error) {
                node.is_active = !value
                this.notify(this.errorText(error), 'danger')
            }
        },
        openForm(node = {}) {
            const parent = node.parent_id ? this.items.find(item => item.id === node.parent_id) : null
            this.form = {
                ...this.emptyForm(),
                ...(node.id ? node : {}),
                // "+ Add" under a group adds a link there; under a section, a link too (pick "group" to nest deeper)
                type: node.type || 'link',
                parent: parent ? { label: this.label(parent), value: parent.id } : null,
                permission: node.permission ? { label: this.permissionText(node.permission), value: node.permission } : null,
                is_active: node.id ? !!node.is_active : true,
                open: true,
            }
        },
        async save() {
            const f = this.form
            const body = {
                type: f.type,
                parent_id: f.parent ? f.parent.value : null,
                name: f.name,
                i18n: f.i18n || null,
                url: f.type === 'link' ? f.url : null,
                icon: f.icon || null,
                permission: f.permission ? f.permission.value : null,
                is_active: f.is_active,
                slug: f.id ? f.slug : undefined,
                // A new item goes to the end of its level
                order_level: f.id ? f.order_level : ((this.childrenOf(f.parent ? f.parent.value : null).length + 1) * 10),
            }
            this.busy = true
            try {
                const { data } = f.id ? await axios.put(`${this.api}/${f.id}`, body) : await axios.post(this.api, body)
                this.notify(data.message)
                this.form.open = false
                await this.refresh()
            } catch (error) {
                this.notify(this.errorText(error), 'danger')
            }
            this.busy = false
        },
        confirmDelete(node) {
            this.$vs.dialog({
                type: 'confirm',
                color: 'danger',
                title: this.$t('mm_delete_title'),
                text: this.$t('mm_delete_text', { name: this.label(node) }),
                acceptText: this.$t('dt_delete'),
                cancelText: this.$t('dt_cancel'),
                accept: () => this.remove(node),
            })
        },
        async remove(node) {
            this.busy = true
            try {
                const { data } = await axios.delete(`${this.api}/${node.id}`)
                this.notify(data.message)
                await this.refresh()
            } catch (error) {
                this.notify(this.errorText(error), 'danger')
            }
            this.busy = false
        },
    },
    async created() {
        await this.load()
        this.loadPreview()
    },
    mounted() {
        this.readSidebarColors()
        // Fonts and the sidebar's own entrance can still change its styles right after the page opens
        setTimeout(this.readSidebarColors, 600)
    },
}
</script>

<style lang="scss">
.mm {
    &__head {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 16px;
        margin-bottom: 20px;

        h2 {
            font-size: 1.35rem;
            font-weight: 700;
            color: #2c2e45;
        }

        p {
            margin-top: 4px;
            font-size: .88rem;
            color: #6b6d7a;
        }

        small {
            display: block;
            margin-top: 2px;
            font-size: .8rem;
            color: #8a8ca5;
        }
    }

    &__layout {
        display: grid;
        grid-template-columns: minmax(0, 1fr) 340px;
        gap: 20px;
        align-items: start;
    }

    &__tree,
    &__preview {
        border: 1px solid #eceef4;
        border-radius: 16px;
        background: #fff;
        box-shadow: 0 2px 10px -4px rgba(23, 23, 74, .08);
    }

    &__tree {
        overflow: hidden;
        transition: opacity .2s ease;

        &.is-busy {
            opacity: .7;
            pointer-events: none;
        }
    }

    &__preview {
        position: sticky;
        top: 90px;
        padding: 20px;

        h3 {
            font-size: 1rem;
            font-weight: 700;
            color: #2c2e45;
        }

        > p {
            margin: 2px 0 16px;
            font-size: .8rem;
            color: #8a8ca5;
        }

        > label {
            display: block;
            margin-bottom: 6px;
            font-size: .85rem;
            font-weight: 600;
            color: #4b4d68;
        }

        > small {
            display: block;
            margin-top: 12px;
            font-size: .78rem;
            color: #8a8ca5;
        }
    }
}

// One item of the tree; --depth indents children
.mm-row {
    display: flex;
    align-items: center;
    gap: 14px;
    padding: 14px 18px 14px calc(18px + var(--depth, 0) * 32px);
    border-bottom: 1px solid #f0f1f5;
    transition: background .15s ease;

    &:hover {
        background: #fafbfc;
    }

    &--section {
        background: #fbfbfd;
    }

    &.is-off {
        .mm-row__icon,
        .mm-row__title strong,
        .mm-row__meta {
            opacity: .55;
        }
    }

    &__no {
        flex-shrink: 0;
        width: 18px;
        text-align: center;
        font-size: .8rem;
        color: #9a9cb3;
        font-variant-numeric: tabular-nums;
    }

    &__icon {
        flex-shrink: 0;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 42px;
        height: 42px;
        border-radius: 12px;
        color: rgb(var(--vs-primary));
        background: rgba(var(--vs-primary), .1);

        .feather-icon {
            display: flex;
        }
    }

    &--section &__icon {
        color: #5e6078;
        background: #eef0f5;
    }

    &__body {
        flex: 1;
        min-width: 0;
    }

    &__title {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 8px;

        strong {
            font-size: .98rem;
            font-weight: 700;
            color: #2c2e45;
        }
    }

    &__meta {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 4px 12px;
        margin-top: 4px;
        font-size: .8rem;
        color: #8a8ca5;

        code {
            font-family: ui-monospace, SFMono-Regular, Menlo, monospace;
            font-size: .78rem;
            color: #6b6d7a;
        }
    }

    &__actions {
        display: flex;
        align-items: center;
        gap: 4px;
        flex-shrink: 0;

        .con-vs-switch {
            margin-left: 12px;
        }
    }

    &--skeleton span {
        height: 14px;
        border-radius: 6px;
        background: linear-gradient(90deg, #f0f1f5, #f8f9fb, #f0f1f5);
        background-size: 200% 100%;
        animation: mm-shimmer 1.2s linear infinite;

        &:nth-child(1) { width: 42px; height: 42px; border-radius: 12px; }
        &:nth-child(2) { flex: 1; }
        &:nth-child(3) { width: 140px; }
    }
}

.mm-badge {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    padding: 2px 10px;
    font-size: .72rem;
    font-weight: 600;
    color: #6b6d7a;
    border-radius: 999px;
    background: #f0f1f5;

    .feather-icon {
        display: flex;
    }

    &--warn {
        color: #9a5b0a;
        background: #fff3e0;
    }

    // Who sees the item (its permission)
    &--access {
        color: rgb(var(--vs-primary));
        background: rgba(var(--vs-primary), .1);
        box-shadow: inset 0 0 0 1px rgba(var(--vs-primary), .25);
    }
}

// Always-on switch: stays on, faded, not clickable
.mm-row__actions .con-vs-switch.is-locked {
    opacity: .5;
    cursor: not-allowed;

    * {
        pointer-events: none;
    }
}


.mm-act {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 34px;
    height: 34px;
    color: #6b6d7a;
    border: none;
    border-radius: 8px;
    background: transparent;
    cursor: pointer;
    transition: background .15s ease, color .15s ease;

    .feather-icon {
        display: flex;
    }

    &:hover:not(:disabled) {
        color: rgb(var(--vs-primary));
        background: rgba(var(--vs-primary), .08);
    }

    &:disabled {
        opacity: .3;
        cursor: default;
    }

    &--danger {
        color: rgb(234, 84, 85);

        &:hover:not(:disabled) {
            color: rgb(234, 84, 85);
            background: rgba(234, 84, 85, .08);
        }
    }
}

// "+ Add" link under a section / group
.mm-add {
    padding: 10px 18px 12px calc(18px + 32px + var(--depth, 1) * 32px);
    border-bottom: 1px solid #f0f1f5;
    background: #fcfcfd;

    button {
        padding: 0;
        font-family: inherit;
        font-size: .85rem;
        color: #6b6d7a;
        border: none;
        background: none;
        text-decoration: underline;
        text-underline-offset: 3px;
        cursor: pointer;

        &:hover {
            color: rgb(var(--vs-primary));
        }
    }
}

.mm-role {
    display: inline-flex;
    align-items: center;
    gap: 8px;

    .feather-icon {
        display: flex;
        color: #8a8ca5;
    }
}

// Sidebar preview, in the sidebar's own colors
.mm-side {
    margin-top: 16px;
    padding: 14px 10px;
    border-radius: var(--mm-side-radius, 14px);
    color: var(--mm-side-text, #fff);
    background: var(--mm-side-bg, rgb(var(--vs-primary)));
    transition: opacity .2s ease, background .3s ease, color .3s ease;

    &.is-loading {
        opacity: .6;
    }

    &__header {
        display: block;
        margin: 14px 12px 6px;
        font-size: .72rem;
        font-weight: 700;
        letter-spacing: .04em;
        text-transform: uppercase;
        color: var(--mm-side-muted, rgba(255, 255, 255, .75));

        &:first-child {
            margin-top: 0;
        }
    }

    &__item {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 9px 12px;
        font-size: .88rem;
        border-radius: 8px;

        .feather-icon {
            display: flex;
            opacity: .9;
        }

        &--sub {
            padding-left: 44px;
            font-size: .82rem;
            opacity: .85;
        }

        // Current page: the same pill as the real sidebar
        &.is-active {
            font-weight: 600;
            opacity: 1;
            color: var(--mm-side-active-text, #34468a);
            background: var(--mm-side-active-bg, #fff);
            box-shadow: var(--mm-side-active-shadow, 0 6px 16px -6px rgba(0, 0, 0, .35));
        }
    }

    &__empty {
        padding: 12px;
        font-size: .85rem;
        opacity: .85;
    }
}

// Add / edit form
.mm-form {
    display: flex;
    flex-direction: column;
    gap: 6px;

    > label {
        margin-top: 10px;
        font-size: .85rem;
        font-weight: 600;
        color: #4b4d68;
    }

    &__hint {
        font-size: .78rem;
        color: #8a8ca5;
    }

    &__switch {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-top: 14px;
        font-size: .88rem;
        color: #4b4d68;
    }

    &__actions {
        display: flex;
        justify-content: flex-end;
        gap: 8px;
        margin-top: 20px;
    }
}

.mm-seg {
    display: flex;
    gap: 6px;

    button {
        display: inline-flex;
        flex: 1;
        align-items: center;
        justify-content: center;
        gap: 8px;
        padding: 9px 12px;
        font-family: inherit;
        font-size: .85rem;
        color: #5e6078;
        border: 1px solid #e3e5ec;
        border-radius: 10px;
        background: #fff;
        cursor: pointer;

        .feather-icon {
            display: flex;
        }

        &.is-active {
            color: rgb(var(--vs-primary));
            border-color: rgba(var(--vs-primary), .5);
            background: rgba(var(--vs-primary), .08);
        }
    }
}

.mm-icon-field {
    display: flex;
    align-items: center;
    gap: 10px;
}

.mm-icons {
    display: flex;
    flex-wrap: wrap;
    gap: 6px;
    margin-top: 6px;

    button {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 34px;
        height: 34px;
        color: #6b6d7a;
        border: 1px solid #eceef4;
        border-radius: 8px;
        background: #fff;
        cursor: pointer;

        .feather-icon {
            display: flex;
        }

        &.is-active,
        &:hover {
            color: rgb(var(--vs-primary));
            border-color: rgba(var(--vs-primary), .5);
        }
    }
}

@keyframes mm-shimmer {
    to { background-position: -200% 0; }
}

// Narrow screens: preview under the tree, actions wrap under the text
@media (max-width: 1100px) {
    .mm__layout {
        grid-template-columns: 1fr;
    }

    .mm__preview {
        position: static;
    }
}

@media (max-width: 640px) {
    .mm__head {
        flex-direction: column;
    }

    .mm-row {
        flex-wrap: wrap;
        padding-left: calc(14px + var(--depth, 0) * 16px);

        &__no {
            display: none;
        }

        &__actions {
            width: 100%;
            justify-content: flex-end;
        }
    }

    .mm-add {
        padding-left: calc(14px + var(--depth, 1) * 16px);
    }
}

.theme-dark {
    .mm__head h2,
    .mm__preview h3,
    .mm-row__title strong { color: #fff; }
    .mm__tree,
    .mm__preview { background: #10163a; border-color: #414561; }
    .mm-row { border-color: #2c3155; }
    .mm-row:hover,
    .mm-row--section,
    .mm-add { background: #1b2142; }
    .mm-badge { background: #262c49; color: #c2c6dc; }
}
</style>

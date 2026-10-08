<template>
    <d-table-list v-if="isVisible" @clicked="initTableData" :api="api" ref="refInitPage" :allowDel="true" :title="title"
        :dataInfo="dataInfo" :dataAttributes="dataAttributes" :dataHeaders="headers" :dataTables="data"
        :formAttributes="formAttributes" :rowDisplay="rowDisplay"></d-table-list>
</template>

<script>
import axios from "@/axios.js";
import apiConfig from "@/apiConfig.js";
import DTableList from '@/views/form-builder/DTableList.vue';
import { permissionLabel } from "@/i18n/i18n.js";

// Administration › Menus: the sidebar menu (columns and form come from the API: Menu::dataFields() / formAttributes())
export default {
    components: {
        DTableList,
    },
    data() {
        return {
            title: "setting_menu",
            api: apiConfig._apiMenu,
            dataAttributes: {
                tableStyle: 1,
                page_number: 1,
                offset: 0,
                dataGrid: "row",
                popupFullscreen: true,
            },
            dataHeaders: [],
            formAttributes: [],
            raw: { data: [], limit: 10, total: 0 },
            rowDisplay: "3grid",
            dataInfo: {},
            isVisible: false,
            loaded: false,
        }
    },
    computed: {
        // The API sends plain values; labels follow the current language (re-computed when it changes)
        data() {
            this.$i18n.locale // eslint-disable-line no-unused-expressions
            const raw = this.raw
            const modules = Object.fromEntries((raw.permission || []).map(p => [p.value, p.module]))
            const typeLabel = type => this.$t('menu_type_' + type)
            return {
                ...raw,
                type: (raw.type || []).map(o => ({ ...o, label: typeLabel(o.value) })),
                permission: (raw.permission || []).map(o => ({ ...o, label: permissionLabel(o.value, o.module) })),
                data: (raw.data || []).map(row => ({
                    ...row,
                    type_label: typeLabel(row.type),
                    permission_label: row.permission ? permissionLabel(row.permission, modules[row.permission]) : '',
                })),
            }
        },
        // Show the translated columns in place of the raw ones
        headers() {
            return this.dataHeaders.map(h => ({ type: 'type_label', permission: 'permission_label' }[h] || h))
        },
    },
    methods: {
        getDataTable({ page_number, search_field }) {
            const params = { sort: "id", order: "", page_number };
            if (search_field) {
                params.search_field = this.formAttributes
                    .filter(attr => search_field[attr.name])
                    .map(attr => ({ [attr.name]: search_field[attr.name] }));
            }

            axios.post(this.api + "/search", params)
                .then((response) => {
                    this.raw = response.data;
                    this.dataHeaders = response.data.data_fields;
                    this.formAttributes = response.data.form_attributes;
                    this.isVisible = response.data.is_display !== false; // hidden only when the server says no access
                    // The list reloads after every save or delete: refresh the sidebar too
                    if (this.loaded) window.dispatchEvent(new Event('menu-changed'));
                    this.loaded = true;
                }).catch(() => {
                    this.$vs.notify({ text: 'msg.action_failed', color: 'danger' })
                }).finally(() => {
                    this.$vs.loading.close();
                })
        },
        initTableData(searchQuery) {
            this.$vs.loading();
            this.getDataTable({ page_number: searchQuery.pageNum, search_field: searchQuery.searchFields });
        }
    },
    created() {
        this.$vs.loading();
        this.getDataTable({ page_number: this.dataAttributes.page_number });
    },
}
</script>

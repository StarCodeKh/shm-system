<template>
    <d-table-list v-if="isVisible" @clicked="initTableData" :api="api" ref="refInitPage" :allowDel="true" :title="title"
        :dataInfo="dataInfo" :dataAttributes="dataAttributes" :dataHeaders="dataHeaders" :dataTables="data"
        :formAttributes="formAttributes" :rowDisplay="rowDisplay"></d-table-list>
</template>

<script>
import axios from "@/axios.js";
import apiConfig from "@/apiConfig.js";
import DTableList from '@/views/form-builder/DTableList.vue';

export default {
    components: {
        DTableList,
    },
    data() {
        return {
            title: "setting_user",
            api: apiConfig._apiUser,
            dataAttributes: {
                tableStyle: 1,
                page_number: 1,
                offset: 0,
                dataGrid: "row",
                popupFullscreen: true,
                // Lock: change this user's password (as on the roles list, where it opens the role's permissions)
                actionButton: [
                    {
                        icon: "LockIcon",
                        title: "profile_tab_password",
                        method: "PrivateLink",
                        path: "/user/edit",
                        query: { tab: "password" },
                        permission: "edit",
                        allow: true
                    }
                ]
            },
            dataHeaders: [],
            formAttributes: [],
            data: { data: [], limit: 10, total: 0 },
            rowDisplay: "3grid", //1grid, 2grid, 3grid, 4grid
            dataInfo: {},
            isVisible: false
        }
    },
    methods: {
        getDataTable({ page_number, search_field }) {
            const params = { sort: "id", order: "", page_number };
            // Search only by fields the form knows, as [{ field: value }, ...]
            if (search_field) {
                params.search_field = this.formAttributes
                    .filter(attr => search_field[attr.name])
                    .map(attr => ({ [attr.name]: search_field[attr.name] }));
            }

            axios.post(this.api + "_index", params)
                .then((response) => {
                    this.data = response.data;
                    this.dataHeaders = response.data.data_fields;
                    this.formAttributes = response.data.form_attributes;
                    this.isVisible = response.data.is_display;
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

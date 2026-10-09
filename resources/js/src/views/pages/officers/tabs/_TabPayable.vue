<template>
    <div>
        <DModalForm @clicked="initTableData" ref="refModalForm" :data="data" :api="apiNotification"
            :formAttributes="formNotifyAttributes" :parentDataInfo="dataInfo" :rowDisplay="rowDisplay"
            :dataAttributes="dataAttributes" :title="$t(title_notify)"
            :fixedTitle="$t('notify_send_title')" :submitLabel="$t('notify_send')" submitIcon="SendIcon" />
        <DModalForm @clicked="initTableData" ref="refModalPaymentForm" :data="data" :api="apiNotification"
            :formAttributes="formNotifyAttributes" :parentDataInfo="dataInfo" :rowDisplay="rowDisplay"
            :dataAttributes="dataAttributes" :title="$t(title_payment)" />
        <d-table-list @clicked="initTableData" :api="api" ref="refInitPage" :allowDel="true" :title="$t(title)" :dataInfo="dataInfo"
            :dataAttributes="dataAttributes" :dataHeaders="dataHeaders" :dataTables="data" :formAttributes="formAttributes"
            :rowDisplay="rowDisplay" @initImport="initImport" @clickPrivateForm="initOpenForm" @clickNotify="initNotify" @clickPayment="initPayment"></d-table-list>
    </div>
</template>

<script>
    import axios from "@/axios.js";
    import apiConfig from "@/apiConfig.js";
    import DTableList from '@/views/form-builder/DTableList.vue';
    import DModalForm from '@/views/form-builder/DModalForm.vue';
    export default {
        data() {
            return {
                title: 'repay', // i18n key: translated in the template, so it follows a language switch
                title_notify: 'notify',
                title_payment: 'payment',
                api: apiConfig._apiOfficerPayable,
                apiNotification: apiConfig._apiSendNotification,
                dataAttributes: {
                    tableStyle: 1,
                    page_number: 1,
                    offset: 0,
                    dataGrid: "row",
                    actionButton: [
                        {
                            icon: "BellIcon",
                            path: "/",
                            method: "sendNotification",
                            allow: true,
                            permission: "notify"
                        },
                    ],
                    enableImport: true,
                    enableDownload: true,
                    enableDashboardPayable: true
                },
                dataHeaders: {
                    header1: "name_en",
                    header2: "name_kh",
                    header3: "order_level",
                },
                data: {
                    data: [
                    ],
                    limit: 10,
                    total: 3,
                },
                formAttributes:[],
                formNotifyAttributes: [
                    {
                        name: "officer_id",
                        type: "select",
                        required: false
                    },
                    {
                        name: "remark",
                        type: "textarea",
                        required: false
                    },
                ],
                formPaymentAttributes: [
                    {
                        name: "remark",
                        type: "textarea",
                        required: false
                    },
                ],
                rowDisplay: "1grid", //1grid, 2grid, 3grid, 4grid
                dataFields: [],
                dataInfo: {}
            }
        },
        components: {
            DTableList,
            DModalForm
        },
        methods: {
            getDataTable(_search_criteria) {
                let _params = {};
                if (_search_criteria.search_field) {
                    let _formAttribute = this.formAttributes;
                    this.dataFields = [];
                    this.formAttributes.forEach(_formAttribute => {
                        if (_search_criteria.search_field[_formAttribute["name"]]) {
                            let _d = {
                                [_formAttribute["name"]]: _search_criteria.search_field[_formAttribute["name"]]
                            }
                            this.dataFields.push(_d);
                        }
                    });
                    _params = {
                        sort: _search_criteria.sort,
                        order: _search_criteria.order,
                        page_number: _search_criteria.page_number,
                        limit: _search_criteria.limit,
                        search_field: this.dataFields,
                        officer_id: this.$route.params.id,
                    };
                } else {
                    _params = {
                        sort: _search_criteria.sort,
                        order: _search_criteria.order,
                        page_number: _search_criteria.page_number,
                        limit: _search_criteria.limit,
                        officer_id: this.$route.params.id,
                    };
                }

                return new Promise((resolve, reject) => {
                    axios.post(this.api + "/search", _params)
                        .then((response) => {
                            if (response.data) {
                                this.data = response.data;
                                this.dataHeaders = response.data.data_fields;
                                this.formAttributes = response.data.form_attributes;
                            } else {
                                this.data = this.data;
                            }
                            this.$vs.loading.close();
                        }).catch((error) => {
                            this.$vs.notify({
                                title: 'Message',
                                text: 'msg.action_failed',
                                iconPack: 'feather',
                                icon: 'icon-check-circle',
                                color: 'danger'
                            })
                            this.$vs.loading.close();
                        })
                })
            },
            getData() {
                let _search_criteria = {
                    sort: "id",
                    order: "",
                    page_number: this.dataAttributes.page_number
                }
                this.getDataTable(_search_criteria);
            },
            initTableData(searchQuery) {
                this.$vs.loading();
                let _search_criteria = {
                    sort: "id",
                    order: "",
                    page_number: searchQuery.pageNum?searchQuery.pageNum:1,
                    limit: searchQuery.limit,
                    search_field: searchQuery.searchFields
                }
                this.getDataTable(_search_criteria);
                return false;
            },
            initImport(params){
            },
            initOpenForm(data) {
                this.$refs.refModalForm.openNewForm(data);
            },
            initPayment(data){
                this.$refs.refModalPaymentForm.openNewForm(data);
            },
            initNotify(data){
                this.$refs.refModalForm.initForm(data);
            }
        },
        created() {
            this.$vs.loading();
            this.getData();
        },
    }
</script>

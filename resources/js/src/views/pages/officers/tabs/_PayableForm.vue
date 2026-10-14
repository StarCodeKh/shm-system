<template>
    <div class="vx-row">
        <!-- <d-form @clickForm="initTableData" ref="refModalForm" :data="data" :dataInfo="dataInfo"
            :parentDataInfo="parentDataInfo" :formAttributes="formAttributes" :api="api"
            :rowDisplay="rowDisplay"/> -->
            <d-file-manager ref="refModelFileManager" @clicked="initPassProps"></d-file-manager>
        <div class="vx-col lg:w-2/3 w-full mt-5">
            <d-form-tabs ref="tabs" v-model="tab" :sections="tabSections" :errors="errors" icon="CreditCardIcon"
                :title="$t('payable_form_title')" :subtitle="$t('payable_form_subtitle')">
                <form id="paymentForm" @submit.prevent="submitForm">
                    <!-- Member -->
                    <div class="form-section" data-section="member" v-show="tab === 'member'">
                        <div class="form-grid">
                            <!-- Search on its own row; the member's details fill the row below once picked -->
                            <div class="form-group full-width">
                                <label>{{ $t('pay_search_member') }} <span class="required"></span></label>
                                <v-select label="label" name="officer_id" v-validate="'required'" :options="data"
                                    v-model="officer_id" @search="onSearchOfficer" @input="onSelectOfficer($event)"
                                    :filterable="false" :placeholder="$t('pay_search_member_hint')" :dir="$vs.rtl ? 'rtl' : 'ltr'">
                                    <template #no-options>{{ $t('pay_search_member_empty') }}</template>
                                </v-select>
                                <span class="text-danger" v-show="errors.has('officer_id')">{{ serverError(errors, 'officer_id') || $t('payable_required_officer') }}</span>
                            </div>
                            <div class="form-group">
                                <label>{{ $t('pay_member_no') }}</label>
                                <input readonly tabindex="-1" v-model="identity_member_no" type="text" placeholder="—">
                            </div>
                            <div class="form-group">
                                <label>{{ $t('pay_department') }}</label>
                                <input readonly tabindex="-1" v-model="department" type="text" placeholder="—">
                            </div>
                            <div class="form-group">
                                <label>{{ $t('pay_entity') }}</label>
                                <input readonly tabindex="-1" v-model="entity" type="text" placeholder="—">
                            </div>
                        </div>
                    </div>

                    <!-- Payment -->
                    <div class="form-section" data-section="payment" v-show="tab === 'payment'">
                        <div class="form-grid">
                            <div class="form-group">
                                <label>{{ $t('pay_year_owed') }} <span class="required"></span></label>
                                <v-select label="label" name="payable" v-validate="'required'" :options="payables"
                                    v-model="payable" @input="onSelectPayable($event)" :filterable="false"
                                    :placeholder="$t('form_select_field', { field: $t('pay_year_owed') })"
                                    :dir="$vs.rtl ? 'rtl' : 'ltr'" />
                                <span class="text-danger" v-show="errors.has('payable')">{{ serverError(errors, 'payable') || $t('payable_required_year') }}</span>
                            </div>
                            <div class="form-group">
                                <label>{{ $t('pay_amount_total') }}</label>
                                <input type="number" v-model="amount" placeholder="0.00" readonly>
                            </div>
                            <div class="form-group">
                                <label>{{ $t('pay_date') }} <span class="required"></span></label>
                                <flat-pickr v-model="payment_date" name="payment_date" v-validate="'required'" class="w-full"
                                    :placeholder="$t('form_pick_date')" />
                                <span class="text-danger" v-show="errors.has('payment_date')">{{ serverError(errors, 'payment_date') || $t('payable_required_date') }}</span>
                            </div>
                            <div class="form-group">
                                <label>{{ $t('pay_method') }} <span class="required"></span></label>
                                <v-select label="label" name="payment_method_id" v-validate="'required'"
                                    :options="paymentMethods" v-model="payment_method_id" :filterable="false"
                                    :placeholder="$t('form_select_field', { field: $t('pay_method') })"
                                    :dir="$vs.rtl ? 'rtl' : 'ltr'" />
                                <span class="text-danger" v-show="errors.has('payment_method_id')">{{ serverError(errors, 'payment_method_id') || $t('payable_required_method') }}</span>
                            </div>
                            <div class="form-group">
                                <label>{{ $t('pay_status') }} <span class="required"></span></label>
                                <v-select label="label" name="status" v-validate="'required'" :options="paymentStatus"
                                    v-model="status" :filterable="false" :dir="$vs.rtl ? 'rtl' : 'ltr'"
                                    :placeholder="$t('form_select_field', { field: $t('pay_status') })" />
                                <span class="text-danger" v-show="errors.has('status')">{{ serverError(errors, 'status') || $t('payable_required_status') }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Note & attachment -->
                    <div class="form-section" data-section="extra" v-show="tab === 'extra'">
                        <div class="form-grid">
                            <div class="form-group half-width">
                                <label>{{ $t('pay_remark') }}</label>
                                <textarea v-model="remark" :placeholder="$t('pay_remark_placeholder')"></textarea>
                            </div>
                            <div class="form-group">
                                <label>{{ $t('pay_attachment') }} <span class="badge">{{ $t('form_optional') }}</span></label>
                                <d-file-picker name="attach" :value="attach || ''"
                                    @browse="initOpenFileManager('attach')"
                                    @clear="attach = null; iconThumnail = null" />
                            </div>
                        </div>
                    </div>
                </form>
                <template #actions>
                    <button type="button" class="d-tabform__btn" @click="resetAll()">
                        <feather-icon icon="RotateCcwIcon" svgClasses="h-4 w-4" />
                        <span>{{ $t('form_reset') }}</span>
                    </button>
                    <button type="button" class="d-tabform__btn d-tabform__btn--primary" :disabled="saving" @click="submitForm">
                        <feather-icon icon="SaveIcon" svgClasses="h-4 w-4" />
                        <span>{{ $t("btn_process") }}</span>
                    </button>
                </template>
            </d-form-tabs>
        </div>

        <div class="vx-col lg:w-1/3 w-full mt-5">
            <div class="payable-history">
                <div class="payable-history__head">
                    <span class="payable-history__icon"><feather-icon icon="ClockIcon" svgClasses="h-4 w-4" /></span>
                    <h3>{{ $t('pay_history') }}</h3>
                </div>

                    <!-- Empty: no member chosen yet, or no payments recorded -->
                    <div v-if="!historyRows.length" class="payable-history__empty">
                        <span class="payable-history__empty-icon"><feather-icon icon="InboxIcon" svgClasses="h-6 w-6" /></span>
                        <strong>{{ $t('payable_history_empty') }}</strong>
                        <span>{{ officer_id ? $t('payable_history_none') : $t('payable_history_pick') }}</span>
                    </div>

                    <template v-else>
                        <ul class="payable-history__list">
                            <li v-for="(row, i) in historyRows" :key="row.id || i" class="payable-history__item"
                                :style="{ '--delay': (i * 40) + 'ms' }">
                                <span class="payable-history__dot"><feather-icon icon="CheckIcon" svgClasses="h-3 w-3" /></span>
                                <span class="payable-history__date">{{ row.payment_date || '—' }}</span>
                                <span class="payable-history__amount">{{ formatAmount(row.amount) }}</span>
                            </li>
                        </ul>
                        <div class="payable-history__total">
                            <span>{{ $t('payable_history_total', { count: historyRows.length }) }}</span>
                            <strong>{{ formatAmount(historyTotal) }}</strong>
                        </div>
                    </template>
            </div>
        </div>
    </div>
</template>
<script>
import axios from "@/axios.js";
import apiConfig from "@/apiConfig.js";
import vSelect from 'vue-select';
import DFileManager from '@/views/form-builder/DFileManager.vue';
import { saveForm, serverError } from "@/formSave.js";
export default {
    data() {
        return {
            tab: 'member',
            saving: false, // a save is in flight: further clicks are ignored
            sections: [
                { key: 'member', icon: 'UserIcon', titleKey: 'pay_tab_member' },
                { key: 'payment', icon: 'CreditCardIcon', titleKey: 'pay_tab_payment' },
                { key: 'extra', icon: 'PaperclipIcon', titleKey: 'pay_tab_extra' },
            ],
            apiPayable: apiConfig._apiOfficerPayable,
            api: apiConfig._apiOfficerPayableInfo,
            apiOfficerGeneralDataForPayable: apiConfig._apiOfficerGeneralDataForPayable,
            paymentHistories: [],
            isMounted: false,
            iconThumnail: null,
            officer_id: null,
            identity_member_no: null,
            entity: null,
            department: null,
            payment_date: null,
            payment_method_id: null,
            currency_id: null,
            amount: 0.00,
            attach: null,
            payable: null,
            payable_id: null,
            remark: null,
            status: {
                label: "បានបង់រួច (Paid)",
                value: 19,
            },
            searchTimeout: null,
            // Data Sidebar
            payables: [],
            currencies: [],
            paymentMethods: [],
            paymentStatus: [

            ],
            data: [],
            iconExcel: "../images/file-icons/xls.png",
            iconDoc: "../images/file-icons/doc.png",
            iconPDF: "../images/file-icons/pdf.png",
        }
    },
    components: {
        DFileManager,
        'v-select': vSelect,
        },
    computed: {
        // Tab titles follow the current language
        tabSections() {
            return this.sections.map(section => ({ ...section, title: this.$t(section.titleKey) }))
        },
        historyRows() {
            return Array.isArray(this.paymentHistories) ? this.paymentHistories : []
        },
        historyTotal() {
            return this.historyRows.reduce((sum, row) => sum + (Number(String(row.amount || 0).replace(/,/g, '')) || 0), 0)
        },
    },
    methods: {
        formatAmount(value) {
            const number = Number(String(value || 0).replace(/,/g, ''))
            return isNaN(number) ? value : number.toLocaleString('en-US', { maximumFractionDigits: 2 })
        },
        initPassProps(props) {
            switch (props.data.type) {
                case "xlsx":
                    this.attach = props.data.path;
                    this.iconThumnail = this.iconExcel;
                    break;
                case "xls":
                    this.attach = props.data.path;
                    this.iconThumnail = this.iconExcel;
                    break;
                case "doc":
                    this.attach = props.data.path;
                    this.iconThumnail = this.iconDoc;
                    break;
                case "docx":
                    this.attach = props.data.path;
                    this.iconThumnail = this.iconDoc;
                    break;
                case "pdf":
                    this.attach = props.data.path;
                    this.iconThumnail = this.iconPDF;
                    break;
                default:
                    this.attach = props.data.path;
                    this.iconThumnail = props.data.path;
                    break;
            }
        },
        initOpenFileManager(props) {
            this.$refs.refModelFileManager.initFileManager(props);
        },
        getGeneralDataForPayable(_search){
            let _keySearch = {"keyword": _search}
            // Lookup only: the payment fields are validated on submit
            return axios.post(this.apiOfficerGeneralDataForPayable, _keySearch)
                .then((response) => {
                    this.data = response.data.officers;
                    this.paymentMethods = response.data.payment_method_id;
                    this.currencies = response.data.currency_id;
                    this.payables = response.data.payables;
                    this.paymentStatus = response.data.status;
                    return response.data;
                })
                .catch((error) => {
                    console.error(error);
                });
        },
        onSearchOfficer(search) {
            // console.log("User searching:", search);
            // clear previous typing timer
            clearTimeout(this.searchTimeout);
            // wait 500ms after user stops typing
            this.searchTimeout = setTimeout(() => {
            if (search.length < 2) return;
                this.getGeneralDataForPayable(search);
            }, 500);
        },
        onSelectOfficer(value) {
            this.clearForm();
            this.paymentHistories = [];
            // Clearing the payment fields is not a user mistake: drop the errors it would trigger
            this.$nextTick(() => this.$validator.reset());
            if (!value) return;
            axios.post(this.api, value)
                .then((response) => {
                    if (response.data.success) {
                        const detail = response.data.officerDetail || {};
                        this.entity = detail.entity ? detail.entity.name_kh : null;
                        this.department = detail.department ? detail.department.name_kh : null;
                        this.identity_member_no = detail.identity_member_no;
                        this.paymentHistories = response.data.paymentHistories;
                        this.payables = response.data.payables;
                    }
                })
                .catch((error) => console.error(error));
        },
        clearForm(){
            this.identity_member_no = null;
            this.entity = null;
            this.department = null;
            this.payment_date = null;
            this.payment_method_id = null;
            this.currency_id = null;
            this.attach = null;
            this.payable = null;
            this.searchTimeout = null;
            this.amount = null;
            this.payable_id = null;
            this.remark = null;
        },
        onSelectPayable(obj){
            this.amount = obj ? obj.value : null;
            this.payable_id = obj ? obj.id : null;
        },
        resetAll() {
            this.clearForm();
            this.officer_id = null;
            this.paymentHistories = [];
            this.tab = 'member';
            this.$nextTick(() => this.$validator.reset());
        },
        // The standard save (formSave.js): a step with a problem is opened, the form empties after saving
        submitForm() {
            const payload = [
                { officer_id: this.officer_id && this.officer_id.value },
                { account_id: 0 },
                { payment_method_id: this.payment_method_id && this.payment_method_id.value },
                { payment_date: this.payment_date },
                { status: this.status && this.status.value },
                { remark: this.remark },
            ]
            return saveForm(this, () => axios.put(this.apiPayable + "/" + this.payable.id, payload), {
                onInvalid: () => this.$refs.tabs.focusFirstError(),
                onSaved: () => this.resetAll(),
            })
        },
        serverError,
    },
    created() {
        let _search = null;
        this.getGeneralDataForPayable(_search);
    },
    mounted() {
        this.isMounted = true;
    }
}
</script>

<style>
.payable-history {
    border: 1px solid #eceef4;
    border-radius: 16px;
    background: #fff;
    box-shadow: 0 4px 24px -10px rgba(23, 23, 74, .12);
    overflow: hidden;

    &__head {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 16px 20px;
        border-bottom: 1px solid #eef0f4;

        h3 {
            font-size: 1rem;
            font-weight: 700;
            color: #2c2e45;
        }
    }

    &__icon {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 32px;
        height: 32px;
        border-radius: 10px;
        color: rgb(var(--vs-primary));
        background: rgba(var(--vs-primary), .12);

        .feather-icon {
            display: flex;
        }
    }

    &__empty {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 4px;
        padding: 40px 20px;
        text-align: center;
        font-size: .82rem;
        color: #8a8ca5;

        strong {
            font-size: .95rem;
            color: #2c2e45;
        }
    }

    &__empty-icon {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 52px;
        height: 52px;
        margin-bottom: 8px;
        border-radius: 16px;
        color: rgb(var(--vs-primary));
        background: rgba(var(--vs-primary), .1);
    }

    &__list {
        max-height: 360px;
        margin: 0;
        padding: 8px;
        list-style: none;
        overflow-y: auto;
    }

    &__item {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 10px 12px;
        border-radius: 10px;
        animation: payable-history-in .3s ease backwards;
        animation-delay: var(--delay);
        transition: background .15s ease;

        &:hover {
            background: #f7f8fa;
        }

        & + & {
            border-top: 1px solid #f1f2f5;
        }
    }

    &__dot {
        flex-shrink: 0;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 24px;
        height: 24px;
        border-radius: 999px;
        color: rgb(var(--vs-primary));
        background: rgba(var(--vs-primary), .12);

        .feather-icon {
            display: flex;
        }
    }

    &__date {
        flex: 1;
        min-width: 0;
        font-size: .875rem;
        color: #4b4d68;
    }

    &__amount {
        font-size: .9rem;
        font-weight: 700;
        color: #2c2e45;
        font-variant-numeric: tabular-nums;
    }

    &__total {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 14px 20px;
        font-size: .82rem;
        color: #6b6d7a;
        border-top: 1px solid #eef0f4;
        background: #fbfbfd;

        strong {
            font-size: 1rem;
            color: rgb(var(--vs-primary));
            font-variant-numeric: tabular-nums;
        }
    }
}

@keyframes payable-history-in {
    from { opacity: 0; transform: translateY(4px); }
    to { opacity: 1; transform: none; }
}

.theme-dark .payable-history {
    background: #262c49;
    border-color: #414561;

    &__total {
        background: #1e2442;
        border-color: #414561;
    }

    &__date,
    &__empty strong,
    &__amount {
        color: #fff;
    }

    &__head {
        border-color: #414561;

        h3 {
            color: #fff;
        }
    }
}
</style>

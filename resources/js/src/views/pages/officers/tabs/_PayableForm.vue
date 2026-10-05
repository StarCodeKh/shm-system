<template>
    <div class="vx-row">
        <!-- <d-form @clickForm="initTableData" ref="refModalForm" :data="data" :dataInfo="dataInfo"
                    :parentDataInfo="parentDataInfo" :formAttributes="formAttributes" :api="api"
                    :rowDisplay="rowDisplay"/> -->
        <template title="modal" class="modal">
            <d-file-manager ref="refModelFileManager" @clicked="initPassProps"></d-file-manager>
        </template>
        <div class="vx-col lg:w-2/3 w-full mt-5">
            <d-form-tabs ref="tabs" v-model="tab" :sections="sections" :errors="errors" icon="CreditCardIcon"
                :title="$t('payable_form_title')" :subtitle="$t('payable_form_subtitle')">
                <form id="paymentForm" @submit.prevent="submitForm">
                    <!-- Member -->
                    <div class="form-section" data-section="member" v-show="tab === 'member'">
                        <div class="form-grid">
                            <div class="form-group half-width">
                                <label>ស្វែងរកឈ្មោះសមាជិក <span class="required"></span></label>
                                <v-select label="label" name="officer_id" v-validate="'required'" :options="data"
                                    v-model="officer_id" @search="onSearchOfficer" @input="onSelectOfficer($event)"
                                    :filterable="false" :dir="$vs.rtl ? 'rtl' : 'ltr'" />
                                <span class="text-danger" v-show="errors.has('officer_id')">{{ $t('payable_required_officer') }}</span>
                            </div>
                            <div class="form-group">
                                <label>អត្ថលេខសមាជិក</label>
                                <input readonly v-model="identity_member_no" type="text" placeholder="អត្ថលេខសមាជិក">
                            </div>
                            <div class="form-group">
                                <label>អង្គភាព</label>
                                <input readonly v-model="department" type="text" placeholder="អង្គភាព">
                            </div>
                            <div class="form-group">
                                <label>នាយកដ្ឋាន</label>
                                <input readonly v-model="entity" type="text" placeholder="នាយកដ្ឋាន">
                            </div>
                        </div>
                    </div>

                    <!-- Payment -->
                    <div class="form-section" data-section="payment" v-show="tab === 'payment'">
                        <div class="form-grid">
                            <div class="form-group">
                                <label>ប្រាក់នៅជំពាក់តាមឆ្នាំ <span class="required"></span></label>
                                <v-select label="label" name="payable" v-validate="'required'" :options="payables"
                                    v-model="payable" @input="onSelectPayable($event)" :filterable="false"
                                    :dir="$vs.rtl ? 'rtl' : 'ltr'" />
                                <span class="text-danger" v-show="errors.has('payable')">{{ $t('payable_required_year') }}</span>
                            </div>
                            <div class="form-group">
                                <label>ចំនួនទឹកប្រាក់ (សរុប)</label>
                                <input type="number" v-model="amount" placeholder="0.00" readonly>
                            </div>
                            <div class="form-group">
                                <label>កាលបរិច្ឆេទបង់ប្រាក់ <span class="required"></span></label>
                                <flat-pickr v-model="payment_date" name="payment_date" v-validate="'required'" class="w-full" />
                                <span class="text-danger" v-show="errors.has('payment_date')">{{ $t('payable_required_date') }}</span>
                            </div>
                            <div class="form-group">
                                <label>វិធីបង់ប្រាក់ <span class="required"></span></label>
                                <v-select label="label" name="payment_method_id" v-validate="'required'"
                                    :options="paymentMethods" v-model="payment_method_id" :filterable="false"
                                    :dir="$vs.rtl ? 'rtl' : 'ltr'" />
                                <span class="text-danger" v-show="errors.has('payment_method_id')">{{ $t('payable_required_method') }}</span>
                            </div>
                            <div class="form-group">
                                <label>ស្ថានភាពបង់ប្រាក់ <span class="required"></span></label>
                                <v-select label="label" name="status" v-validate="'required'" :options="paymentStatus"
                                    v-model="status" :filterable="false" :dir="$vs.rtl ? 'rtl' : 'ltr'" />
                                <span class="text-danger" v-show="errors.has('status')">{{ $t('payable_required_status') }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Note & attachment -->
                    <div class="form-section" data-section="extra" v-show="tab === 'extra'">
                        <div class="form-grid">
                            <div class="form-group half-width">
                                <label>ការកំណត់សម្គាល់</label>
                                <textarea v-model="remark" placeholder="បញ្ចូលការកំណត់សម្គាល់ (បើមាន)"></textarea>
                            </div>
                            <div class="form-group">
                                <label>ឯកសារភ្ជាប់ <span class="badge">Optional</span></label>
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
                    <button type="button" class="d-tabform__btn d-tabform__btn--primary" @click="submitForm">
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
                <h3>ប្រវត្តិបង់ប្រាក់</h3>
            </div>

            <!-- <div id="data-list-list-view" class="data-list-container"> -->
                <div style="display: none;">{{ paymentHistories | json }}</div>
                <vs-table multiple v-model="selected" pagination :max-items="itemsPerPage" search :data="paymentHistories">
                    <template slot="thead">
                        <vs-th sort-key="category">ឆ្នាំ</vs-th>
                        <vs-th sort-key="popularity">ចំនួន</vs-th>
                        <!-- <vs-th style="background: rgb(96 113 173); color: #ffffff; font-weight: bold;">សកម្ម</vs-th> -->
                    </template>

                    <template slot-scope="{data}">
                        <tbody>
                            <vs-tr :data="tr" :key="indextr" v-for="(tr, indextr) in data">

                            <vs-td>
                                <p class="product-name font-medium truncate">{{ tr.payment_date }}</p>
                            </vs-td>

                            <vs-td>
                                <p class="product-price">{{ tr.amount }}</p>
                            </vs-td>

                            <!-- <vs-td>
                                <vs-progress :percent="Number(tr.popularity)" :color="getPopularityColor(Number(tr.popularity))" class="shadow-md" />
                            </vs-td>

                            <vs-td>
                                <vs-chip :color="getOrderStatusColor(tr.order_status)" class="product-order-status">{{ tr.order_status | title }}</vs-chip>
                            </vs-td>

                            <vs-td>
                                <p class="product-price">${{ tr.price }}</p>
                            </vs-td> -->

                            <!-- <vs-td class="whitespace-no-wrap">
                                <feather-icon icon="EditIcon" svgClasses="w-5 h-5 hover:text-primary stroke-current" @click.stop="editData(tr)" />
                                <feather-icon icon="TrashIcon" svgClasses="w-5 h-5 hover:text-danger stroke-current" class="ml-2" @click.stop="deleteData(tr.id)" />
                            </vs-td> -->

                            </vs-tr>
                        </tbody>
                    </template>
                </vs-table>
          </div>
        </div>
    </div>
</template>
<script>
// import DataViewSidebar from '@/views/components/ui-elements/data-list/DataViewSidebar.vue'
import axios from "@/axios.js";
import apiConfig from "@/apiConfig.js";
import { ref } from 'vue';
import moduleDataList from "@/store/data-list/moduleDataList.js";
import vSelect from 'vue-select';
import DFileManager from '@/views/form-builder/DFileManager.vue';
export default {
  data() {
    return {
        tab: 'member',
        sections: [
            { key: 'member', icon: 'UserIcon', title: 'ព័ត៌មានសមាជិក' },
            { key: 'payment', icon: 'CreditCardIcon', title: 'ព័ត៌មានការបង់ប្រាក់' },
            { key: 'extra', icon: 'PaperclipIcon', title: 'កំណត់សម្គាល់ និងឯកសារ' },
        ],
        apiPayable: apiConfig._apiOfficerPayable,
        api: apiConfig._apiOfficerPayableInfo,
        apiOfficerGeneralDataForPayable: apiConfig._apiOfficerGeneralDataForPayable,
        selected: [],
        paymentHistories: [],
        itemsPerPage: 4,
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
        addNewDataSidebar: false,
        sidebarData: {},
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
    currentPage() {
      if(this.isMounted) {
        return this.$refs.table.currentx
      }
      return 0
    },
    // products() {
    //   return this.$store.state.dataList.products
    // },
    queriedItems() {
      return this.$refs.table ? this.$refs.table.queriedResults.length : this.payableData.length
    }
  },
  methods: {
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
    addNewData() {
      this.sidebarData = {}
      this.toggleDataSidebar(true)
    },
    deleteData(id) {
      this.$store.dispatch("dataList/removeItem", id).catch(err => { console.error(err) })
    },
    editData(data) {
      // this.sidebarData = JSON.parse(JSON.stringify(this.blankData))
      this.sidebarData = data
      this.toggleDataSidebar(true)
    },
    getOrderStatusColor(status) {
      if(status == 'on_hold') return "warning"
      if(status == 'delivered') return "success"
      if(status == 'canceled') return "danger"
      return "primary"
    },
    getPopularityColor(num) {
      if(num > 90) return "success"
      if(num >70) return "primary"
      if(num >= 50) return "warning"
      if(num < 50) return "danger"
      return "primary"
    },
    toggleDataSidebar(val=false) {
      this.addNewDataSidebar = val
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
    submitForm() {
        let _data = [
            {officer_id: this.officer_id["value"]},
            {account_id: 0},
            {payment_method_id: this.payment_method_id["value"]},
            {payment_date: this.payment_date},
            {status: this.status["value"]},
            {remark: this.remark},
        ];
        this.$validator.validateAll().then(result => {
            if (result) {
                this.$vs.loading();
                return new Promise((resolve, reject) => {
                    axios.put(this.apiPayable+"/"+this.payable["id"], _data)
                        .then((response) => {
                            if (response.data.success) {
                                this.$vs.notify({
                                    title: 'Message',
                                    text: response.data.message,
                                    iconPack: 'feather',
                                    icon: 'icon-check-circle',
                                    color: 'primary',
                                    position: 'top-right'
                                })
                                this.resetAll();
                                this.$vs.loading.close();
                                // this.$router.push('/account/expense').catch(() => { })
                            }else{
                                this.$vs.notify({
                                    title: 'Message',
                                    text: 'Error, while trying to do action, please double check criteria form!',
                                    iconPack: 'feather',
                                    icon: 'icon-check-circle',
                                    color: 'danger',
                                    position: 'top-right'
                                })
                                this.$vs.loading.close();
                            }
                        }).catch((error) => {
                            reject(error)
                            this.$vs.notify({
                                title: 'Message',
                                text: 'Error, while trying to do action or no permission allow!',
                                iconPack: 'feather',
                                icon: 'icon-check-circle',
                                color: 'danger',
                                position: 'top-right'
                            })
                            this.$vs.loading.close();
                        })
                    });
            }else{
                this.$refs.tabs.focusFirstError();
                this.$vs.notify({
                    title: 'Message',
                    text: 'Error, while trying to do action, please double check criteria form!',
                    iconPack: 'feather',
                    icon: 'icon-check-circle',
                    color: 'danger',
                    position: 'top-right'
                })
            }
        });
    }
  },
  created() {
    let _search = null;
    this.getGeneralDataForPayable(_search);
    if(!moduleDataList.isRegistered) {
      this.$store.registerModule('dataList', moduleDataList)
      moduleDataList.isRegistered = true
    }
    this.$store.dispatch("dataList/fetchDataListItems")
  },
  mounted() {
    this.isMounted = true;
  }
}
</script>

<style lang="scss">
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

    .vs-con-table {
        padding: 8px 12px 12px;
        background: transparent;
    }

    .vs-table--thead th {
        font-size: .78rem;
        font-weight: 700;
        color: #6b6d7a;
        background: #f7f8fa;
    }
}

.theme-dark .payable-history {
    background: #262c49;
    border-color: #414561;

    &__head {
        border-color: #414561;

        h3 {
            color: #fff;
        }
    }
}
</style>

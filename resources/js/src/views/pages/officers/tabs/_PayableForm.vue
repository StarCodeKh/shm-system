<template>
    <div class="vx-row">
        <!-- <d-form @clickForm="initTableData" ref="refModalForm" :data="data" :dataInfo="dataInfo"
                    :parentDataInfo="parentDataInfo" :formAttributes="formAttributes" :api="api"
                    :rowDisplay="rowDisplay"/> -->
        <template title="modal" class="modal">
            <d-file-manager ref="refModelFileManager" @clicked="initPassProps"></d-file-manager>
        </template>
        <div class="vx-col md:w-2/3 w-full mt-5">
            <div class="container">
                <div class="header">
                    <h1><i class="fas fa-credit-card"></i> បង់ប្រាក់</h1>
                    <p>បំពេញព័ត៌មានខាងក្រោមដើម្បីបង់ប្រាក់</p>
                </div>
                
                <div class="form-container">
                    <div class="success-message" id="successMessage">
                        <i class="fas fa-check-circle"></i> ការបង់ប្រាក់ត្រូវបានបង្កើតដោយជោគជ័យ!
                    </div>
    
                    <form id="paymentForm">
                        <!-- Customer Information -->
                        <div class="section-title">
                            <i class="fas fa-user"></i>
                            ព័ត៌មានសមាជិក
                        </div>
    
                        <div class="form-row">
                            <div class="form-group">
                                <label class="required">ស្វែងរកឈ្មោះសមាជិក</label>
                                <div class="input-icon">
                                    <i class="fas fa-user"></i>
                                    <!-- <input type="text" placeholder="ស្វែងរកឈ្មោះសមាជិក" required> -->
                                    <v-select label="label" :options="data" v-model="officer_id" @search="onSearchOfficer" @input="onSelectOfficer($event)" :filterable="false" :dir="$vs.rtl ? 'rtl' : 'ltr'" />
                                     <!-- <vs-select v-model="selectedValue" @input="onSelectOfficer($event)" :dir="$vs.rtl ? 'rtl' : 'ltr'" v-validate="'required'" class="mt-1 w-full">
                                        <vs-select-item :key="index" :value="item.value" :text="item.label" v-for="(item, index) in data" />
                                    </vs-select> -->
                                    <!-- <select @change="onChangeElement($event)">                                            
                                <option value="test">Test</option>
                                <option value="test2">Test2</option>
                                </select> -->
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="required">អត្ថលេខសមាជិក</label>
                                <div class="input-icon">
                                    <i class="fas fa-user"></i>
                                    <input readonly v-model="identity_member_no" type="text" placeholder="អត្ថលេខសមាជិក">
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="required">អង្គភាព</label>
                                <div class="input-icon">
                                    <i class="fas fa-phone"></i>
                                    <textarea readonly v-model="department" type="text" placeholder="អង្គភាព"/>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="required">នាយកដ្ឋាន</label>
                                <div class="input-icon">
                                    <i class="fas fa-phone"></i>
                                    <textarea readonly v-model="entity" type="text" placeholder="នាយកដ្ឋាន"/>
                                </div>
                            </div>
                        </div>
    
                        <!-- Payment Details -->
                        <div class="section-title">
                            <i class="fas fa-wallet"></i>
                            ព័ត៌មានការបង់ប្រាក់
                        </div>
    
                        <div class="vx-row">
                            <div class="vx-col md:w-1/2 w-full">
                                <div class="form-group">
                                    <label class="required">ចំនួនទឹកប្រាក់</label>
                                    <div class="amount-display">
                                        <label>សរុប</label>
                                        <input type="number" v-model="amount" id="amount" placeholder="0.00" required>
                                    </div>
                                </div>
                            </div>

                            <div class="vx-col md:w-1/2 w-full">
                                <div class="form-group">
                                    <label class="required">ប្រាក់នៅជំពាក់តាមឆ្នាំ</label>
                                    <v-select label="label" :options="payables" v-model="payable" @input="onSelectPayable($event)" :filterable="false" :dir="$vs.rtl ? 'rtl' : 'ltr'" />
                                    <input type="hidden" v-model="payable_id" />
                                </div>
                                <div class="form-group">
                                    <label class="required">កាលបរិច្ឆេទបង់ប្រាក់</label>
                                    <div class="input-icon">
                                        <i class="fas fa-calendar"></i>
                                        <input type="date" v-model="payment_date" required>
                                    </div>
                                </div>
                                <!-- <div class="form-group">
                                    <label class="required">រូបបីយវត្ថុ</label>
                                    <v-select label="label" :options="currencies" v-model="currency_id" @input="onSelectCurrency($event)" :filterable="false" :dir="$vs.rtl ? 'rtl' : 'ltr'" />
                                </div> -->
                                <div class="form-group">
                                    <label class="required">វិធីបង់ប្រាក់</label>
                                    <v-select label="label" :options="paymentMethods" v-model="payment_method_id" :filterable="false" :dir="$vs.rtl ? 'rtl' : 'ltr'" />
                                </div>
                                <div class="form-group">
                                    <label class="required">ស្ថានភាពបង់ប្រាក់</label>
                                    <v-select label="label" :options="paymentStatus" v-model="status" :filterable="false" :dir="$vs.rtl ? 'rtl' : 'ltr'" />
                                </div>
                            </div>
                        </div>
    
                        <!-- <div class="form-group full-width">
                            <label class="required">វិធីបង់ប្រាក់</label>
                            <div class="payment-methods">
                                <div class="payment-option selected" onclick="selectPayment(this)">
                                    <i class="fas fa-money-bill-wave"></i>
                                    <span>សាច់ប្រាក់</span>
                                </div>
                                <div class="payment-option" onclick="selectPayment(this)">
                                    <i class="fas fa-mobile-alt"></i>
                                    <span>ABA</span>
                                </div>
                                <div class="payment-option" onclick="selectPayment(this)">
                                    <i class="fas fa-university"></i>
                                    <span>ធនាគារ</span>
                                </div>
                            </div>
                        </div> -->
    
                        <div class="form-group full-width">
                            <label>ការកំណត់សម្គាល់</label>
                            <textarea rows="3" v-model="remark" placeholder="បញ្ចូលការកំណត់សម្គាល់ (បើមាន)"></textarea>
                        </div>

                        <!-- <div class="form-group full-width">
                            <label>ឯកសារយោង</label>
                            <input type="file"/>
                        </div> -->
                        <div class="form-group">
                            <label>ឯកសារភ្ជាប់ <span class="badge">Optional</span></label>
                            <div class="file-upload-wrapper">
                                <img v-if="iconThumnail!=null" style="width:100px" class="attach" :src="iconThumnail" />
                                <vs-input type="hidden" :placeholder="$t('attach')"
                                    v-model="attach" :value="''"
                                    class="mt-2 w-full" />
                                <label for="attach" @click="initOpenFileManager('attach')" class="file-upload-label">
                                    <i class="fas fa-camera"></i>
                                    <span>ជ្រើសរើសឯកសារភ្ជាប់</span>
                                </label>
                            </div>
                        </div>
    
                        <!-- <div class="form-row">
                            <div class="form-group">
                                <label>អ្នកទទួលប្រាក់</label>
                                <input type="text" value="" readonly>
                            </div>
                        </div> -->
    
                        <button type="submit" @click.prevent="submitForm" class="btn-submit">
                            <i class="fas fa-save"></i> {{$t("btn_process") }}
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="vx-col md:w-1/3 w-full mt-5">
            <div class="section-title">
                <i class="fas fa-user"></i>
                ប្រវត្តិបង់ប្រាក់
            </div>

            <!-- <div id="data-list-list-view" class="data-list-container"> -->
                <div style="display: none;">{{ paymentHistories | json }}</div>
                <vs-table multiple v-model="selected" pagination :max-items="itemsPerPage" search :data="paymentHistories">
                    <template slot="thead" style="background: rgb(96 113 173) !important; color: #ffffff; font-weight: bold;">
                        <vs-th sort-key="category" style="background: rgb(96 113 173) !important; color: #ffffff; font-weight: bold;">ឆ្នាំ</vs-th>
                        <vs-th sort-key="popularity" style="background: rgb(96 113 173) !important; color: #ffffff; font-weight: bold;">ចំនួន</vs-th>
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
            <!-- </div> -->

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
        this.$validator.validateAll().then(result => {
            if (result) {
                return new Promise((resolve, reject) => {
                    axios.post(this.apiOfficerGeneralDataForPayable, _keySearch)
                        .then((response) => {
                            this.data = response.data.officers;
                            this.paymentMethods = response.data.payment_method_id;
                            this.currencies = response.data.currency_id;
                            this.payables = response.data.payables;
                            this.paymentStatus = response.data.status;
                            resolve(response.data);   // return data
                        })
                        .catch((error) => {
                            console.error(error);
                            reject(error);            // return error
                        });
                });
            }
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
        this.paymentHistories = {};
        // console.log("Selected:", value);
        this.$validator.validateAll().then(result => {
            if (result) {
                return new Promise((resolve, reject) => {
                    axios.post(this.api, value)
                        .then((response) => {
                            if (response.data.success) {
                                this.entity = response.data.officerDetail.entity.name_kh;
                                this.department = response.data.officerDetail.department.name_kh;
                                this.identity_member_no = response.data.officerDetail.identity_member_no;
                                this.paymentHistories = response.data.paymentHistories;
                                this.payables = response.data.payables;
                            }
                        })
                    });
            }else{
                alert("error!");
            }
        });
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
        this.amount = obj.value;
        this.payable_id = obj.id;
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
                                this.clearForm();
                                this.officer_id = null;
                                this.paymentHistories = [];
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
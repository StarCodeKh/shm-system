<template>
    <vx-card :title="$t('setting_account_administration')" code-toggler>
        <div class="mt-5">
            <vs-tabs position="left">
                <vs-tab v-if="tab1" :label="$t('setting_payment_type')">
                    <payment-type></payment-type>
                </vs-tab>

                <vs-tab v-if="tab2" :label="$t('setting_payment_term')">
                    <payment-term></payment-term>
                </vs-tab>

                <vs-tab v-if="tab3" :label="$t('setting_payment_method')">
                    <payment-method></payment-method>
                </vs-tab>

                <vs-tab v-if="tab4" :label="$t('setting_account_type')">
                    <account-type></account-type>
                </vs-tab>

                <vs-tab v-if="tab5" :label="$t('setting_letter_type')">
                    <letter-type></letter-type>
                </vs-tab>

                <vs-tab v-if="tab6" :label="$t('setting_bank')">
                    <bank></bank>
                </vs-tab>

                <vs-tab v-if="tab7" :label="$t('setting_currency')">
                    <currency></currency>
                </vs-tab>

                <vs-tab :label="$t('setting_currency_exchange')">
                    <currency-exchange></currency-exchange>
                </vs-tab>

                <vs-tab v-if="tab8" :label="$t('setting_unit')">
                    <unit></unit>
                </vs-tab>

                <vs-tab v-if="tab9" :label="$t('setting_planning')">
                    <planning></planning>
                </vs-tab>

                <vs-tab v-if="tab10" :label="$t('setting_account_type_group')">
                    <account-type-group></account-type-group>
                </vs-tab>

                <vs-tab v-if="tab11" :label="$t('setting_group_chapter')">
                    <account-group></account-group>
                </vs-tab>

                <vs-tab v-if="tab12" :label="$t('setting_account_sub_account')">
                    <account></account>
                </vs-tab>

                <vs-tab v-if="tab13" :label="$t('setting_category')">
                    <item-category></item-category>
                </vs-tab>

                <vs-tab v-if="tab14" :label="$t('setting_product_service')">
                    <item></item>
                </vs-tab>

                <vs-tab v-if="tab15" :label="$t('setting_budget_template')">
                    <budget-template></budget-template>
                </vs-tab>

                <!-- <vs-tab :label="$t('setting_cost_list')">
                    <price-list-type></price-list-type>
                </vs-tab> -->
            </vs-tabs>
        </div>
    </vx-card>
</template>

<script>
import moduleDataList from "@/store/data-list/moduleDataList.js";
import Planning from '@/views/pages/settings/_Planning.vue';
import Item from '@/views/pages/settings/_Item.vue';
import ItemCategory from '@/views/pages/settings/_Item_Category.vue';
import AccountTypeGroup from '@/views/pages/settings/_Account_Type_Group.vue';
import AccountType from '@/views/pages/settings/_Account_Type.vue';
import AccountGroup from '@/views/pages/settings/_Account_Group.vue';
import Account from '@/views/pages/settings/_Account.vue';
import Unit from '@/views/pages/settings/_Unit.vue';
import Bank from '@/views/pages/settings/_Bank.vue';
import LetterType from '@/views/pages/settings/_LetterType.vue';
import Currency from '@/views/pages/settings/_Currency.vue';
import CurrencyExchange from '@/views/pages/settings/_CurrencyExchange.vue';
import PriceListType from '@/views/pages/settings/_Price_List_Type.vue';
import BudgetTemplate from '@/views/pages/settings/_Budget_Template.vue';
import PaymentMethod from '@/views/pages/settings/_PaymentMethod.vue';
import PaymentTerm from '@/views/pages/settings/_PaymentTerm.vue';
import PaymentType from '@/views/pages/settings/_PaymentType.vue';
export default {
    components: {
        Planning,
        ItemCategory,
        Item,
        AccountTypeGroup,
        AccountType,
        AccountGroup,
        Account,
        Unit,
        Bank,
        LetterType,
        PriceListType,
        BudgetTemplate,
        Currency,
        CurrencyExchange,
        PaymentMethod,
        PaymentTerm,
        PaymentType
    },
    data() {
        return {
            // popupActive: false,
            selected: [],
            // products: [],
            itemsPerPage: 4,
            isMounted: false,
            // Data Sidebar
            addNewDataSidebar: false,
            // tab
            tab1: true,
            tab2: true,
            tab3: true,
            tab4: true,
            tab5: true,
            tab6: true,
            tab7: true,
            tab8: true,
            tab9: true,
            tab10: true,
            tab11: true,
            tab12: true,
            tab13: true,
            tab14: true,
            tab15: true,
            sidebarData: {},
            price_list_groups: [
                {
                    code: "1",
                    name: "សិក្ខាសាលា /បណ្តុះបណ្តាល និងកិច្ចប្រជុំ ក្នុងប្រទេស",
                    description: "",
                    order_level: 1,
                    is_active: 1
                },
                {
                    code: "1.1",
                    name: "កញ្ចប់សេវាសិក្ខាសាលា/បណ្តុះបណ្តាលនៅសណ្ឋាគារផ្កាយ៤",
                    description: "",
                    order_level: 1,
                    is_active: 1
                },
                {
                    code: "2",
                    name: "ទស្សកិច្ចសិក្សា/បណ្តុះបណ្តាល និងកិច្ចប្រជុំ ក្រៅប្រទេស",
                    description: "",
                    order_level: 1,
                    is_active: 1
                },
                {
                    code: "2.1",
                    name: "ថ្លៃ​វគ្គបណ្តុះបណ្តាល",
                    description: "",
                    order_level: 1,
                    is_active: 1
                },
            ],
            accountGroups: [
                {
                    code: "60",
                    name: "ការទិញ",
                    description: "",
                    order_level: 1,
                    is_active: 1
                },
            ],
            accounts: [
                {
                    code: "6001",
                    parent: "",
                    name: "សម្ភារៈផ្គត់ផ្គង់ថែទាំ",
                    description: "",
                    order_level: 1,
                    is_active: 1
                },
                {
                    code: "60011",
                    parent: "សម្ភារៈផ្គត់ផ្គង់ថែទាំ",
                    name: "សម្ភារៈផ្គត់ផ្គង់សម្អាត និងអនាម័យ",
                    description: "",
                    order_level: 1,
                    is_active: 1
                },
                {
                    code: "60012",
                    parent: "សម្ភារៈផ្គត់ផ្គង់ថែទាំ",
                    name: "សម្ភារៈផ្គត់ផ្គង់សម្រាប់ថែទាំអាគារ ប្រសាទ និងសំណង់ផ្សេងៗ",
                    description: "",
                    order_level: 1,
                    is_active: 1
                },
            ],
            items: [
                {
                    code: "ITM0001",
                    account_group: "ការទិញ",
                    account_type: "សម្ភារៈផ្គត់ផ្គង់ថែទាំ->សម្ភារៈផ្គត់ផ្គង់និងអនាម័យ",
                    name: "រាយនាមទំនិញ/សេវ៉ាកម្ម",
                    unit: "ឯកតា",
                    description: "",
                    order_level: 1,
                    is_active: 1
                },
                {
                    code: "ITM0001",
                    account_group: "ការទិញ",
                    account_type: "សម្ភារៈផ្គត់ផ្គង់ថែទាំ->សម្ភារៈផ្គត់ផ្គង់និងអនាម័យ",
                    name: "រាយនាមទំនិញ/សេវ៉ាកម្ម",
                    unit: "ឯកតា",
                    description: "",
                    order_level: 1,
                    is_active: 1
                },
                {
                    code: "ITM0001",
                    account_group: "ការទិញ",
                    account_type: "សម្ភារៈផ្គត់ផ្គង់ថែទាំ->សម្ភារៈផ្គត់ផ្គង់និងអនាម័យ",
                    name: "រាយនាមទំនិញ/សេវ៉ាកម្ម",
                    unit: "ឯកតា",
                    description: "",
                    order_level: 1,
                    is_active: 1
                },
            ],
        }
    },
    computed: {
        currentPage() {
            if (this.isMounted) {
                return this.$refs.table.currentx
            }
            return 0
        },
        products() {
            return this.$store.state.dataList.products
        },
        queriedItems() {
            return this.$refs.table ? this.$refs.table.queriedResults.length : this.products.length
        }
    },
    methods: {
        viewBudgetList() {
            this.$router.push('/module/budget-arrangement/budget-ceiling/list').catch(() => { });
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
            if (status == 'on_hold') return "warning"
            if (status == 'completed') return "success"
            if (status == 'canceled') return "danger"
            return "primary"
        },
        getPopularityColor(num) {
            if (num > 90) return "success"
            if (num > 70) return "primary"
            if (num >= 50) return "warning"
            if (num < 50) return "danger"
            return "primary"
        },
        toggleDataSidebar(val = false) {
            this.addNewDataSidebar = val
        },
        viewCeiling() {
            alert("testing");
        }
    },
    created() {
        if (!moduleDataList.isRegistered) {
            this.$store.registerModule('dataList', moduleDataList)
            moduleDataList.isRegistered = true
        }
        this.$store.dispatch("dataList/fetchDataListItems")
        this.data;
        this.$vs.loading();
        // Parse data from localStorage Roel Menu
        const stored = JSON.parse(localStorage.getItem("role_menus")) || [];
        if (stored) {
            try {
                this.roleMenuTab = stored;
                // this.tab1 = !!this.roleMenuTab.list_payment_type;
                // this.tab2 = !!this.roleMenuTab.list_payment_term; //getAllow("payment_terms");
                // this.tab3 = !!this.roleMenuTab.list_payment_method;//getAllow("payment_method");
                // this.tab4 = !!this.roleMenuTab.list_account_type;//getAllow("account_type");
                // this.tab5 = !!this.roleMenuTab.list_letter_type;//getAllow("type_of_letter");
                // this.tab6 = !!this.roleMenuTab.list_bank;//getAllow("bank");
                // this.tab7 = !!this.roleMenuTab.list_currency;//getAllow("three_dimensional_figure");
                // this.tab8 = !!this.roleMenuTab.list_unit; //getAllow("unit");
                // this.tab9 = !!this.roleMenuTab.list_plan; //getAllow("plan");
                // this.tab10 = !!this.roleMenuTab.list_account_group;//getAllow("account_group");
                // this.tab11 = !!this.roleMenuTab.list_account_chapter; //getAllow("account_chapter");
                // this.tab12 = !!this.roleMenuTab.list_account; //getAllow("account_sub_account");
                // this.tab13 = !!this.roleMenuTab.list_product_service_type; //getAllow("product_service_type");
                // this.tab14 = !!this.roleMenuTab.list_products_services; //getAllow("products_services");
                // this.tab15 = !!this.roleMenuTab.list_budget_template;//getAllow("budget_template");
                console.log(this.tab1);
            } catch (e) {
                console.error("Invalid localStorage format for role_menus_2", e);
            }
        }
        this.$vs.loading.close();
    },
    mounted() {
        this.isMounted = true;
    }
}
</script>
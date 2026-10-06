<template>
    <div class="mt-5">
        <vs-tabs position="top" alignment="center" class="mt-8">

            <vs-tab v-if="tab1" icon-pack="feather" icon="icon-database" :label="$t('setting_finance_resource')">
                <!-- គម្រោងវិនិយោគសាធារណៈ Finance Resource-->
                <PIP-finance-resource></PIP-finance-resource>
            </vs-tab>

            <vs-tab v-if="tab2" icon-pack="feather" icon="icon-activity" :label="$t('setting_project_status')">
                <!-- គម្រោងវិនិយោគសាធារណៈ Project Status-->
                <PIP-project-status></PIP-project-status>
            </vs-tab>

            <vs-tab v-if="tab3" icon-pack="feather" icon="icon-package" :label="$t('setting_project_type')">
                <!-- គម្រោងវិនិយោគសាធារណៈ Project Type-->
                <PIP-project-type></PIP-project-type>
            </vs-tab>

            <vs-tab v-if="tab4" icon-pack="feather" icon="icon-folder" :label="$t('setting_expenditure_group')">
                <expenditure-group></expenditure-group>
            </vs-tab>

            <vs-tab v-if="tab5" icon-pack="feather" icon="icon-pie-chart" :label="$t('setting_expenditure_type')">
                <expenditure-type></expenditure-type>
            </vs-tab>

            <vs-tab v-if="tab6" icon-pack="feather" icon="icon-layers" :label="$t('ceiling_group')">
                <ceiling-group></ceiling-group>
            </vs-tab>

            <vs-tab v-if="tab7" icon-pack="feather" icon="icon-bar-chart-2" :label="$t('ceiling_type')">
                <ceiling-type></ceiling-type>
            </vs-tab>

            <vs-tab v-if="tab8" icon-pack="feather" icon="icon-trending-up" :label="$t('setting_credit_movement_type')">
                <CreditMovementType />
            </vs-tab>

        </vs-tabs>
    </div>
</template>


<script>
import moduleDataList from "@/store/data-list/moduleDataList.js";
import PIPFinanceResource from '@/views/pages/settings/include_budget_arrangement/_PIPFinanceResource.vue';
import PIPProjectStatus from '@/views/pages/settings/include_budget_arrangement/_PIPProjectStatus.vue';
import PIPProjectType from '@/views/pages/settings/include_budget_arrangement/_PIPProjectType.vue';
import ExpenditureGroup from '@/views/pages/settings/include_budget_arrangement/_ExpenditureGroup.vue';
import ExpenditureType from '@/views/pages/settings/include_budget_arrangement/_ExpenditureType.vue';
import CreditMovementType from '@/views/pages/settings/include_budget_arrangement/_CreditMovementType.vue';
// Ceiling
import CeilingGroup from '@/views/pages/settings/ceiling/_CeilingGroup.vue';
import CeilingType from '@/views/pages/settings/ceiling/_CeilingType.vue';

export default {
    components: {
        PIPFinanceResource,
        PIPProjectStatus,
        PIPProjectType,
        ExpenditureGroup,
        ExpenditureType,
        CeilingGroup,
        CeilingType,
        CreditMovementType
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
            tab1: true, 
            tab2: true, 
            tab3: true, 
            tab4: true, 
            tab5: true, 
            tab6: true, 
            tab7: true, 
            tab8: true,
            sidebarData: {},
            data: [
                {
                    name: "ក្រសួង-ស្ថាប័ន",
                    description: "អគ្គលេខាធិការដ្ឋានគណៈកម្មាធិការដឹកនាំការងារកែទម្រង់ការគ្រប់គ្រងហិរញ្ញវត្ថុសាធារណៈ",
                },
                {
                    name: "អង្គភាពក្រោមឱវាទ កសហវ",
                    description: "ថវិកាដែលប្រើប្រាស់ក្រោមអង្គភាពក្រោមឱវាទ កសហវ",
                },
            ],
            pip: [
                {
                    code: "GD01",
                    name: "នាយកដ្ឋានផែនការថវិកា",
                    secretary: "អគ្គលេខាធិការដ្ឋាន អ.គ.ហ",
                    parent: "អគ្គលេខាធិការដ្ឋាន អ.គ.ហ",
                    order_level: 1,
                },
            ],
            titles: [
                {
                    name: "កញ្ញា",
                    description: "Miss",
                },
                {
                    name: "ជំទាវ",
                    description: "YE",
                },
                {
                    name: "លោក",
                    description: "Mr",
                },
                {
                    name: "លោកស្រី",
                    description: "Mrs",
                },
                {
                    name: "អ្នកនាង",
                    description: "Ms.",
                },
            ],
            positions: [
                {
                    name: "អគ្គលេខាធិការ",
                    description: "Secretary General",
                },
                {
                    name: "អគ្គលេខាធិការរង",
                    description: "Deputy Secretary General",
                },
                {
                    name: "មន្ត្រីជំនាញជាន់ខ្ពស់",
                    description: "",
                },
                {
                    name: "មន្ត្រីជំនាញ",
                    description: "",
                },
                {
                    name: "ជំនួយការមន្រ្តីជំនាញ",
                    description: "",
                },
            ]
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

        // Parse data from localStorage Role Menu
        const stored = JSON.parse(localStorage.getItem("role_menus")) || [];
        if (stored) {
            try {
                this.roleMenuTab = stored;

                // Assign flags safely
                this.tab1 = true;//getAllow("financial_source");
                this.tab2 = true;//getAllow("project_status");
                this.tab3 = true;//getAllow("project_type");
                this.tab4 = true;//getAllow("expense_group");
                this.tab5 = true;//getAllow("expense_type");
                this.tab6 = true;//getAllow("budget_ceiling_group_chapter");
                this.tab7 = true;//getAllow("budget_ceiling_type");
                this.tab8 = true;//getAllow("credit_movement_type");
                console.log(this.tab1);
            } catch (e) {
                console.error("Invalid localStorage format for role_menus_4", e);
            }
        }

    },
    mounted() {
        this.isMounted = true;
    }
}
</script>
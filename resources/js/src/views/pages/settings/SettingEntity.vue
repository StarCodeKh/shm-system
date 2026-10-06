<template>
    <div class="mt-5">
        <!-- <h6 class="mb-3"> {{ $t("setting_department") }} </h6> -->
        <vs-tabs position="top" alignment="center" class="mt-8">

            <vs-tab v-if="tab_secretary" icon-pack="feather" icon="icon-users" :label="$t('setting_secretary')">
                <department></department>
            </vs-tab>

            <vs-tab v-if="tab_entity" icon-pack="feather" icon="icon-home" :label="$t('setting_entity')">
                <entity></entity>
            </vs-tab>

            <vs-tab v-if="tab_tittle" icon-pack="feather" icon="icon-award" :label="$t('setting_title')">
                <titles></titles>
            </vs-tab>

            <vs-tab v-if="tab_position" icon-pack="feather" icon="icon-briefcase" :label="$t('setting_position')">
                <position></position>
            </vs-tab>

            <!-- <vs-tab :label="$t('setting_entity_officer')">
                <entity-officer></entity-officer>
            </vs-tab> -->
        </vs-tabs>
    </div>
</template>


<script>
import moduleDataList from "@/store/data-list/moduleDataList.js"
import Department from '@/views/pages/settings/include_entity/_Department.vue';
import Entity from '@/views/pages/settings/include_entity/_Entity.vue';
import EntityOfficer from '@/views/pages/settings/include_entity/_EntityOfficer.vue';
import Position from '@/views/pages/settings/include_entity/_Position.vue';
import Secretary from '@/views/pages/settings/include_entity/_Secretary.vue';
import Titles from '@/views/pages/settings/include_entity/_Title.vue';
export default {
    components: {
        Department,
        Entity,
        EntityOfficer,
        Position,
        Secretary,
        Titles,
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
            sidebarData: {},
            tab_secretary: false,
            tab_entity: false,
            tab_tittle: false,
            tab_position: false,
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
            departments: [
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
        // Parse data from localStorage Roel Menu
        const stored = JSON.parse(localStorage.getItem("role_menus")) || [];
        if (stored) {
            try {
                this.roleMenuTab = stored;
                // Assign flags safely
                this.tab_secretary = true;//getAllow("secretary");
                this.tab_entity = true;//getAllow("unit");
                this.tab_tittle = true;//getAllow("job");
                this.tab_position = true;//getAllow("position");
                console.log(this.tab_secretary);
            } catch (e) {
                console.error("Invalid localStorage format for role_menus_2", e);
            }
        }
    },
    mounted() {
        this.isMounted = true;
    }
}
</script>
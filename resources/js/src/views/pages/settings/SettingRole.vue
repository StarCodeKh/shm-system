<template>
    <vx-card>
        <div class="mt-5">
            <vs-tabs alignment="center">
                <vs-tab v-if="tab1" icon-pack="feather" icon="icon-user" :label="$t('setting_user')">
                    <user></user>
                </vs-tab>
                <vs-tab v-if="tab2" icon-pack="feather" icon="icon-shield" :label="$t('setting_role')">
                    <role></role>
                </vs-tab>
            </vs-tabs>
        </div>
    </vx-card>
</template>
<script>
import Role from '@/views/pages/settings/_Role.vue';
import User from '@/views/pages/settings/_User.vue';
export default {
    components: {
        Role,
        User
    },
    data() {
        return {
            selected: [],
            itemsPerPage: 4,
            isMounted: false,
            // Data Sidebar
            addNewDataSidebar: false,
            sidebarData: {},
            tab1: true,
            tab2: true
        }
    },
    computed: {

    },
    methods: {
        toggleDataSidebar(val = false) {
            this.addNewDataSidebar = val
        },
    },
    created() {
        // Parse data from localStorage Roel Menu
        const stored = JSON.parse(localStorage.getItem("role_menus")) || [];
        if (stored) {
            try {
                this.roleMenuTab = stored;
                this.tab1 = !!this.roleMenuTab.list_users;//getAllow("tab_user");
                this.tab2 = !!this.roleMenuTab.list_roles;//getAllow("usage_rights");
            } catch (e) {
                console.error("Invalid localStorage format for role_menus_3", e);
            }
        }
    },
    mounted() {
        this.isMounted = true;
    }
}
</script>

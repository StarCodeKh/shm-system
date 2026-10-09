<template>
    <vx-card>
        <div class="mt-5">
            <vs-tabs alignment="center">
                <vs-tab v-if="tabs.users" icon-pack="feather" icon="icon-user" :label="$t('setting_user')">
                    <user></user>
                </vs-tab>
                <vs-tab v-if="tabs.roles" icon-pack="feather" icon="icon-shield" :label="$t('setting_role')">
                    <role></role>
                </vs-tab>
            </vs-tabs>
        </div>
    </vx-card>
</template>
<script>
import { allowedTabs } from "./settingsAccess.js";
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
            // Sub-tabs shown in this section (by permission, see settingsAccess.js)
            tabs: allowedTabs("roles")
        }
    },
    methods: {
    },
    mounted() {
        this.isMounted = true;
    }
}
</script>

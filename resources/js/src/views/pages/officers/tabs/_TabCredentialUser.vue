<template>
    <vx-card :title="$t('setting_credential_access')">
        <vs-tabs position="left">
             <vs-tab icon-pack="feather" icon="icon-bell" :label="$t('setting_notification')">
                <TabNotification/>
            </vs-tab>
            <vs-tab icon-pack="feather" icon="icon-user" :label="$t('setting_user_detail')">
                <TabUserDetail/>
            </vs-tab>
            <vs-tab icon-pack="feather" icon="icon-smartphone" :label="$t('setting_user_device')">
                <TabSecurityLogin/>
            </vs-tab>
        </vs-tabs>
    </vx-card>
</template>


<script>
import TabUserDetail from '@/views/pages/officers/tabs/_TabUserDetail.vue';
import TabSecurityLogin from '@/views/pages/officers/tabs/_TabSecurityLogin.vue';
import TabNotification from '@/views/pages/officers/tabs/_TabNotification.vue';
export default {
    components: {
        TabUserDetail,
        TabSecurityLogin,
        TabNotification
    },
    data() {
        return {

        }
    },
    methods: {

    },
    created() {

    },
    mounted() {

    }
}
</script>

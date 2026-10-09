<template>
    <vx-card>
        <audit />
    </vx-card>
</template>
<script>
import Audit from '@/views/pages/settings/_Audit.vue';
export default {
    components: {
        Audit,
    },
    data() {
        return {
            isMounted: false,

            // Data Sidebar

        }
    },
    methods: {

    },
    created() {

    },
    mounted() {
        this.isMounted = true;
    }
}
</script>

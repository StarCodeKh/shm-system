<template>
    <div class="mt-5">
        <vs-tabs position="top" alignment="center" class="mt-8">

            <vs-tab v-if="tab_secretary" icon-pack="feather" icon="icon-users" :label="$t('setting_secretary')">
                <department></department>
            </vs-tab>

            <vs-tab v-if="tab_entity" icon-pack="feather" icon="icon-home" :label="$t('setting_entity')">
                <entity></entity>
            </vs-tab>

            <vs-tab v-if="tab_tittle" icon-pack="feather" icon="icon-award" :label="$t('setting_officer_title')">
                <titles></titles>
            </vs-tab>

            <vs-tab v-if="tab_position" icon-pack="feather" icon="icon-briefcase" :label="$t('setting_position')">
                <position></position>
            </vs-tab>

        </vs-tabs>
    </div>
</template>

<script>
import Department from '@/views/pages/settings/include_entity/_Department.vue';
import Entity from '@/views/pages/settings/include_entity/_Entity.vue';
import Position from '@/views/pages/settings/include_entity/_Position.vue';
import Titles from '@/views/pages/settings/include_entity/_Title.vue';

export default {
    components: {
        Department,
        Entity,
        Position,
        Titles,
    },
    data() {
        return {
            // Sub-tabs shown in this section (Settings › Entity is guarded by its own permission)
            tab_secretary: true,
            tab_entity: true,
            tab_tittle: true,
            tab_position: true,
        }
    },
}
</script>

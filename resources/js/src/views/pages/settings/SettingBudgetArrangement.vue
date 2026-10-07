<template>
    <div class="mt-5">
        <vs-tabs position="top" alignment="center" class="mt-8">

            <vs-tab v-if="tab1" icon-pack="feather" icon="icon-database" :label="$t('setting_finance_resource')">
                <!-- គម្រោងវិនិយោគសាធារណៈ Finance Resource-->
                <finance-resource></finance-resource>
            </vs-tab>

            <vs-tab v-if="tab2" icon-pack="feather" icon="icon-activity" :label="$t('setting_project_status')">
                <!-- គម្រោងវិនិយោគសាធារណៈ Project Status-->
                <project-status></project-status>
            </vs-tab>

            <vs-tab v-if="tab3" icon-pack="feather" icon="icon-package" :label="$t('setting_project_type')">
                <!-- គម្រោងវិនិយោគសាធារណៈ Project Type-->
                <project-type></project-type>
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
import FinanceResource from '@/views/pages/settings/include_budget_arrangement/_FinanceResource.vue';
import ProjectStatus from '@/views/pages/settings/include_budget_arrangement/_ProjectStatus.vue';
import ProjectType from '@/views/pages/settings/include_budget_arrangement/_ProjectType.vue';
import ExpenditureGroup from '@/views/pages/settings/include_budget_arrangement/_ExpenditureGroup.vue';

import ExpenditureType from '@/views/pages/settings/include_budget_arrangement/_ExpenditureType.vue';

import CreditMovementType from '@/views/pages/settings/include_budget_arrangement/_CreditMovementType.vue';

import CeilingGroup from '@/views/pages/settings/ceiling/_CeilingGroup.vue';

import CeilingType from '@/views/pages/settings/ceiling/_CeilingType.vue';


export default {
    components: {
        FinanceResource,
        ProjectStatus,
        ProjectType,
        ExpenditureGroup,
        ExpenditureType,
        CeilingGroup,
        CeilingType,
        CreditMovementType,
    },
    data() {
        return {
            // Sub-tabs shown in this section
            tab1: true,
            tab2: true,
            tab3: true,
            tab4: true,
            tab5: true,
            tab6: true,
            tab7: true,
            tab8: true,
        }
    },
}
</script>
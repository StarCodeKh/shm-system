<template>
    <vx-card :title="$t('setting_account_administration')">
        <div class="mt-5">
            <vs-tabs position="left">
                <vs-tab v-if="tab1" icon-pack="feather" icon="icon-tag" :label="$t('setting_payment_type')">
                    <payment-type></payment-type>
                </vs-tab>

                <vs-tab v-if="tab2" icon-pack="feather" icon="icon-calendar" :label="$t('setting_payment_term')">
                    <payment-term></payment-term>
                </vs-tab>

                <vs-tab v-if="tab3" icon-pack="feather" icon="icon-credit-card" :label="$t('setting_payment_method')">
                    <payment-method></payment-method>
                </vs-tab>

                <vs-tab v-if="tab4" icon-pack="feather" icon="icon-layers" :label="$t('setting_account_type')">
                    <account-type></account-type>
                </vs-tab>

                <vs-tab v-if="tab5" icon-pack="feather" icon="icon-file-text" :label="$t('setting_letter_type')">
                    <letter-type></letter-type>
                </vs-tab>

                <vs-tab v-if="tab6" icon-pack="feather" icon="icon-briefcase" :label="$t('setting_bank')">
                    <bank></bank>
                </vs-tab>

                <vs-tab v-if="tab7" icon-pack="feather" icon="icon-dollar-sign" :label="$t('setting_currency')">
                    <currency></currency>
                </vs-tab>

                <vs-tab icon-pack="feather" icon="icon-repeat" :label="$t('setting_currency_exchange')">
                    <currency-exchange></currency-exchange>
                </vs-tab>

                <vs-tab v-if="tab9" icon-pack="feather" icon="icon-clipboard" :label="$t('setting_planning')">
                    <planning></planning>
                </vs-tab>

                <vs-tab v-if="tab10" icon-pack="feather" icon="icon-grid" :label="$t('setting_account_type_group')">
                    <account-type-group></account-type-group>
                </vs-tab>

                <vs-tab v-if="tab11" icon-pack="feather" icon="icon-folder" :label="$t('setting_group_chapter')">
                    <account-group></account-group>
                </vs-tab>

                <vs-tab v-if="tab12" icon-pack="feather" icon="icon-list" :label="$t('setting_account_sub_account')">
                    <account></account>
                </vs-tab>
            </vs-tabs>
        </div>
    </vx-card>
</template>

<script>
import Planning from '@/views/pages/settings/_Planning.vue';


import AccountTypeGroup from '@/views/pages/settings/_Account_Type_Group.vue';
import AccountType from '@/views/pages/settings/_Account_Type.vue';
import AccountGroup from '@/views/pages/settings/_Account_Group.vue';
import Account from '@/views/pages/settings/_Account.vue';

import Bank from '@/views/pages/settings/_Bank.vue';
import LetterType from '@/views/pages/settings/_LetterType.vue';
import Currency from '@/views/pages/settings/_Currency.vue';
import CurrencyExchange from '@/views/pages/settings/_CurrencyExchange.vue';

import PaymentMethod from '@/views/pages/settings/_PaymentMethod.vue';
import PaymentTerm from '@/views/pages/settings/_PaymentTerm.vue';
import PaymentType from '@/views/pages/settings/_PaymentType.vue';

export default {
    components: {
        Planning,
        AccountTypeGroup,
        AccountType,
        AccountGroup,
        Account,
        Bank,
        LetterType,
        Currency,
        CurrencyExchange,
        PaymentMethod,
        PaymentTerm,
        PaymentType,
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
            tab9: true,
            tab10: true,
            tab11: true,
            tab12: true,
        }
    },
}
</script>

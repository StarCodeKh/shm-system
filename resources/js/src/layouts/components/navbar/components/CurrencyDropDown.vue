<template>
    <vs-dropdown vs-custom-content vs-trigger-click class="cursor-pointer">
        <!-- Selected currency -->
        <span class="cursor-pointer flex items-center">
            <p class="label-padding">{{ $t('switching_currency') }} : </p>
            <img class="h-4 w-5" :src="currencyImg" :alt="currency" />
            <span class="hidden sm:block ml-2 label-padding">{{ currency }}</span>
        </span>

        <!-- Dropdown menu -->
        <vs-dropdown-menu class="w-32 vx-navbar-dropdown">
            <vs-dropdown-item @click="selectCurrency('KHR')">
                <img class="h-4 w-5 mr-1" src="@assets/images/flags/kh.svg" />
                KHR
            </vs-dropdown-item>

            <vs-dropdown-item @click="selectCurrency('USD')">
                <img class="h-4 w-5 mr-1" src="@assets/images/flags/en.svg" />
                USD
            </vs-dropdown-item>
        </vs-dropdown-menu>
    </vs-dropdown>
</template>

<script>
import flagKh from '@assets/images/flags/kh.svg'
import flagEn from '@assets/images/flags/en.svg'
export default {
    name: 'CurrencyDropDown',

    /**
     * Enables v-model
     */
    props: {
        value: {
            type: String,
            default: 'KHR'
        }
    },

    computed: {
        currency() {
            return this.value
        },
        currencyImg() {
            return this.currency === 'KHR' ? flagKh : flagEn
        }
    },

    methods: {
        selectCurrency(currency) {
            // v-model support
            this.$emit('input', currency)

            // custom event (optional)
            this.$emit('change', currency)
        }
    }
}
</script>
<style>
  .label-padding{
        padding-right: 10px;
        font-family: var(--font-base) !important;
  }
</style>

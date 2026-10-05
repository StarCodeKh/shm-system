<template>
    <div id="invoice-page">
        <!-- Poup Data Table -->
        <vs-popup fullscreen :title="$t('income_expense_implementing_result')" :active.sync="popupActive">
            <popup :proPlan="proPlan"></popup>
        </vs-popup><!--###End Poup Data Table-->

        <div class="flex flex-wrap items-center justify-between">
            <vx-input-group class="mb-base mr-3">
                <!-- <vs-input v-model="mailTo" :placeholder="$t('text_search')" />
                <template slot="append">
                  <div class="append-text btn-addon">
                    <vs-button type="border" @click="mailTo = ''" class="whitespace-no-wrap">{{$t("btn_search")}}</vs-button>
                  </div>
                </template> -->
            </vx-input-group>
            <div class="flex items-center">
                <vs-button class="mb-base mr-3" type="border" icon-pack="feather" icon="icon icon-download">{{
                    $t('Download') }}</vs-button>
                <vs-button class="mb-base mr-3" icon-pack="feather" icon="icon icon-file" @click="printInvoice">{{
                    $t("Print") }}</vs-button>
            </div>
        </div>

        <div class="vx-row">
            <vs-navbar text-color="rgba(255,255,255,.6)" active-text-color="rgba(255,255,255,1)" color="#F4F4F4"
                style="box-shadow: 1px 5px 15px;" class="p-2 mb-6">

                <div slot="title">
                    <vs-navbar-title>
                        <span>{{ $t('income_expense_implementing_result') }}</span>
                    </vs-navbar-title>
                </div>

                <vs-navbar-item index="0">
                    <a href="#">សរុបកម្មវិធី</a>
                </vs-navbar-item>
            </vs-navbar>

            <!-- Background Color -->
            <div class="vx-col w-full sm:w-1/2 md:w-1/2 lg:w-1/2 xl:w-1/2 mb-base">
                <vx-card title="" title-color="#fff" card-background="#3fccca" content-color="#fff" code-toggler
                    @click="initProPlan(1)" color="primary">
                    <center>
                        <h5 class="pb-4" style="line-height: 30px;"><b>{{ $t('income_implementing_result') }}</b></h5>
                    </center>
                </vx-card>
            </div>

            <!-- Background Color -->
            <div class="vx-col w-full sm:w-1/2 md:w-1/2 lg:w-1/2 xl:w-1/2 mb-base">
                <vx-card title="" title-color="#fff" card-background="#cfb007" content-color="#fff" code-toggler
                    @click="initProPlan(2)">
                    <center>
                        <h5 class="pb-4" style="line-height: 30px;"><b>{{ $t('expense_implementing_result') }}</b></h5>
                    </center>
                </vx-card>
            </div>
        </div>
    </div>
</template>

<script>

import Popup from './Popup.vue'
export default {
    data() {
        return {
            title: "income_expense_implementing_result",
            popupActive: false,
            mailTo: "",
            proPlan: 1,
        }
    },
    computed: {

    },
    methods: {
        printInvoice() {
            window.print()
        },
        initProPlan(proPlan) {
            this.proPlan = proPlan;
            this.popupActive = true;
        }
    },
    components: {
        Popup
    },
    mounted() {
        this.$emit("setAppClasses", "invoice-page")
    }
}
</script>

<style lang="scss">
@media print {
    .invoice-page {
        * {
            visibility: hidden;
        }

        #content-area {
            margin: 0 !important;
        }

        #invoice-container,
        #invoice-container * {
            visibility: visible;
        }

        #invoice-container {
            position: absolute;
            left: 0;
            top: 0;
            box-shadow: none;
        }
    }
}
</style>
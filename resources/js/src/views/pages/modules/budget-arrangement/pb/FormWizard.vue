<template>
    <!-- <vx-card title=""> -->
    <!-- <step-costing></step-costing> -->
    <div class="mt-5">
        <form-wizard color="rgba(var(--vs-primary), 1)" errorColor="rgba(var(--vs-danger), 1)" :title="null"
            :subtitle="null" finishButtonText="Submit">

            <!-- Tab 1 -->
            <tab-content :title="$t('pb_heading_costing')" class="mb-5" icon="feather icon-dollar-sign"
                :before-change="validateStep2">
                <step-costing></step-costing>
            </tab-content>

            <!-- Tab 2 -->
            <tab-content :title="$t('pb_heading_pb_revenue')" class="mb-5" icon="feather icon-activity"
                :before-change="validateStep3">
                <step-revenue ref="refInitCalRequestRev"></step-revenue>
            </tab-content>

            <!-- Tab 3 -->
            <tab-content :title="$t('pb_heading_pb_expense')" class="mb-5" icon="feather icon-clipboard"
                :before-change="validateStep4">
                <step-expense ref="refInitCalRequestExp"></step-expense>
            </tab-content>

            <!-- Tab 4 -->
            <tab-content :title="$t('pb_heading_staff_expense')" class="mb-5" icon="feather icon-users"
                :before-change="validateStep5">
                <step-staff-costing ref="refInitCalRequestStaffCosting"></step-staff-costing>
            </tab-content>

            <!-- Tab 5 -->
            <tab-content :title="$t('pb_heading_target_indicator')" class="mb-5" icon="feather icon-codepen">
                <step-indicator ref="refInitCalRequestIndicator"></step-indicator>
            </tab-content>

            <!-- Tab 2 content -->
            <!-- <tab-content :title="$t('pb_verify')" class="mb-5" icon="feather icon-briefcase"
                    :before-change="validateStep2">
                    <center>
                        <h2>Page is under construction</h2>
                    </center>
                </tab-content> -->

        </form-wizard>
    </div>

    <!-- </vx-card> -->
</template>

<script>
import { FormWizard, TabContent } from '@/components/form-wizard'
import 'vue-form-wizard/dist/vue-form-wizard.min.css'
import StepIndicator from "./_StepIndicator.vue"
import StepCosting from "./_StepCosting.vue"
import StepStaffCosting from "./_StepStaffCosting.vue"
import StepRevenue from "./_StepRevenue.vue"
import StepExpense from "./_StepExpense.vue"

// For custom error message
const dict = {
    custom: {
        first_name: {
            required: 'First name is required'
        },
        last_name: {
            required: 'Last name is required',
            alpha: "Last name may only contain alphabetic characters"
        },
        email: {
            required: 'Email is required',
            email: "Please enter valid email"
        },
        job_title: {
            required: 'Job title name is required',
            alpha: "Job title may only contain alphabetic characters"
        },
        proposal_title: {
            required: 'Proposal title name is required',
            alpha: "Proposal title may only contain alphabetic characters"
        },
        event_name: {
            required: 'Event name is required',
            alpha: "Event name may only contain alphabetic characters"
        },
    }
};

// register custom messages
// Field messages for Khmer; merged without changing the app's validation language

export default {
    data() {
        return {
            firstName: "",
            lastName: "",
            email: "",
            city: "new-york",
            proposalTitle: "",
            jobTitle: "",
            textarea: "",
            eventName: "",
            eventLocation: "san-francisco",
            status: "plannning",
            cityOptions: [
                { text: "New York", value: "new-york" },
                { text: "Chicago", value: "chicago" },
                { text: "San Francisco", value: "san-francisco" },
                { text: "Boston", value: "boston" },
            ],
            statusOptions: [
                { text: "Plannning", value: "plannning" },
                { text: "In Progress", value: "in progress" },
                { text: "Finished", value: "finished" },
            ],
            LocationOptions: [
                { text: "New York", value: "new-york" },
                { text: "Chicago", value: "chicago" },
                { text: "San Francisco", value: "san-francisco" },
                { text: "Boston", value: "boston" },
            ],
        }
    },
    methods: {
        validateStep2() {
            this.$refs.refInitCalRequestRev.initRequest();
            return new Promise((resolve, reject) => {
                this.$validator.validateAll("step-2").then(result => {
                    if (result) {
                        resolve(true)
                    } else {
                        reject("correct all values");
                    }
                })
            })
        },
        validateStep3() {
            this.$refs.refInitCalRequestExp.initRequest();
            return new Promise((resolve, reject) => {
                this.$validator.validateAll("step-3").then(result => {
                    if (result) {
                        // alert("Form submitted!");
                        resolve(true)
                    } else {
                        reject("correct all values");
                    }
                })
            })
        },
        validateStep4() {
            this.$refs.refInitCalRequestStaffCosting.initRequest();
            return new Promise((resolve, reject) => {
                this.$validator.validateAll("step-3").then(result => {
                    if (result) {
                        // alert("Form submitted!");
                        resolve(true)
                    } else {
                        reject("correct all values");
                    }
                })
            })
        },
        validateStep5() {
            this.$refs.refInitCalRequestIndicator.initRequest();
            return new Promise((resolve, reject) => {
                this.$validator.validateAll("step-3").then(result => {
                    if (result) {
                        // alert("Form submitted!");
                        resolve(true)
                    } else {
                        reject("correct all values");
                    }
                })
            })
        }
    },
    components: {
        FormWizard,
        TabContent,
        StepIndicator,
        StepCosting,
        StepStaffCosting,
        StepRevenue,
        StepExpense
    }
}
</script>

<template>
  <div>
    <vx-card title="">
      <!-- <component :is="selectedComponent3"></component> -->
      <!-- <step-indicator></step-indicator> -->
      <div class="mt-5">
        <form-wizard color="rgba(var(--vs-primary), 1)" errorColor="rgba(var(--vs-danger), 1)" :title="null"
          :subtitle="null" finishButtonText="Submit">
          <!-- Tab 1  -->
          <tab-content :title="$t('bsp_text_subprogram')" class="mb-5" icon="feather icon-package"
            :before-change="validateStep2">
            <step-subprogram></step-subprogram>
          </tab-content>

          <!-- Tab 2  -->
          <tab-content :title="$t('bsp_text_define_indicator')" class="mb-5" icon="feather icon-code"
            :before-change="validateStep3">
            <step-indicator ref="refInitCalRequestIndicator"></step-indicator>
          </tab-content>

          <!-- Tab 3  -->
          <tab-content :title="$t('bsp_text_income_plan')" class="mb-5" icon="feather icon-edit-2"
            :before-change="validateStep4">
            <step-income-plan ref="refInitCalRequestIncomePlan"></step-income-plan>
          </tab-content>

          <!-- Tab 4  -->
          <tab-content :title="$t('bsp_text_expense_demand')" class="mb-5" icon="feather icon-dollar-sign"
            :before-change="validateStep5">
            <step-expense-demand ref="refInitCalRequestExpDemand"></step-expense-demand>
          </tab-content>

          <!-- tab 5 -->
          <!-- <tab-content :title="$t('bsp_text_printing')" class="mb-5" icon="feather icon-image">
            <vs-navbar text-color="rgba(255,255,255,.6)" active-text-color="rgba(255,255,255,1)" :type="type"
              color="#F4F4F4" v-model="activeItem" style="box-shadow: 1px 5px 15px;" class="p-2 mb-6">

              <div slot="title">
                <vs-navbar-title>
                  <span>{{ $t("bsp_verify_printing") }}</span>
                </vs-navbar-title>
              </div>

              <vs-navbar-item index="0">
                <a href="#" style="color: #000000;">{{ $t("bsp_text_printing") }}</a>
              </vs-navbar-item>
            </vs-navbar>

            <div class="vx-row">
              
              <div class="vx-col w-full sm:w-1/4 md:w-1/4 lg:w-1/4 xl:w-1/4 mb-base">
                <vx-card :title="$t('bsp_text_printing')" title-color="#fff" card-background="primary"
                  content-color="#fff">
                  <center>
                    <h5 class="pb-4" style="line-height: 30px;">{{ $t("bsp_text_subprogram") }}
                    </h5>
                  </center>
                  <p class="mb-3​">
                    <center><vs-button class="mt-4 mr-2 shadow-lg" type="border" color="#ffffff"
                        gradient-color-secondary="#CE9FFC">{{ $t("Download") }}</vs-button></center>
                  </p>
                </vx-card>
              </div>

              <div class="vx-col w-full sm:w-1/4 md:w-1/4 lg:w-1/4 xl:w-1/4 mb-base">
                <vx-card :title="$t('bsp_text_printing')" title-color="#fff" card-background="danger" content-color="#fff"
>
                  <center>
                    <h5 class="pb-4" style="line-height: 30px;">{{ $t("bsp_text_define_indicator") }}</h5>
                  </center>
                  <p class="mb-3​">
                    <center><vs-button class="mt-4 mr-2 shadow-lg" type="border" color="#ffffff"
                        gradient-color-secondary="#CE9FFC">{{ $t("Download") }}</vs-button></center>
                  </p>
                </vx-card>
              </div>

              <div class="vx-col w-full sm:w-1/4 md:w-1/4 lg:w-1/4 xl:w-1/4 mb-base">
                <vx-card :title="$t('bsp_text_printing')" title-color="#fff" card-background="warning"
                  content-color="#fff">
                  <center>
                    <h5 class="pb-4" style="line-height: 30px;">{{ $t("bsp_text_income_plan") }}</h5>
                  </center>
                  <p class="mb-3​">
                    <center><vs-button class="mt-4 mr-2 shadow-lg" type="border" color="#ffffff"
                        gradient-color-secondary="#CE9FFC">{{ $t("Download") }}</vs-button></center>
                  </p>
                </vx-card>
              </div>

              <div class="vx-col w-full sm:w-1/4 md:w-1/4 lg:w-1/4 xl:w-1/4 mb-base">
                <vx-card :title="$t('bsp_text_printing')" title-color="#fff" card-background="primary"
                  content-color="#fff">
                  <center>
                    <h5 class="pb-4" style="line-height: 30px;">{{ $t('bsp_text_expense_demand') }}</h5>
                  </center>
                  <p class="mb-3​">
                    <center><vs-button class="mt-4 mr-2 shadow-lg" type="border" color="#ffffff"
                        gradient-color-secondary="#CE9FFC">{{ $t("Download") }}</vs-button></center>
                  </p>
                </vx-card>
              </div>

            </div>
          </tab-content> -->

        </form-wizard>
      </div>
    </vx-card>
  </div>
</template>

<script>
import { FormWizard, TabContent } from '@/components/form-wizard'
import 'vue-form-wizard/dist/vue-form-wizard.min.css'
import StepExpenseDemand from "./_StepExpenseDemand.vue"
import StepIncomePlan from "./_StepIncomePlan.vue"
import StepSubprogram from "./_StepSubprogram.vue"
import StepIndicator from "./_StepIndicator.vue"

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
      type: "",
      activeItem: true,
      showSubPro: false,
      showIndicator: false,
      showIncomePlan: false,
      showExpense: false,
      selectedComponent1: 'StepSubprogram',
      selectedComponent2: 'StepIndicator',
      selectedComponent3: 'StepIncomePlan',
      selectedComponent4: 'StepExpenseDemand',
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
    selectedComponent(index, component) {
      if (index == 1) {
        this.$refs.ref.getRequestData();
        this.selectedComponent1 = component;
      }
      if (index == 2) {
        this.selectedComponent2 = component;
      }
      if (index == 3) {
        this.selectedComponent3 = component;
      }
      if (index == 4) {
        this.selectedComponent4 = component;
      }
    },

    validateStep2() {
      this.$refs.refInitCalRequestIndicator.initRequest();
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
      this.$refs.refInitCalRequestIncomePlan.initRequest();
      return new Promise((resolve, reject) => {
        this.$validator.validateAll("step-3").then(result => {
          if (result) {
            resolve(true)
          } else {
            reject("correct all values");
          }
        })
      })
    },
    validateStep4() {
      this.$refs.refInitCalRequestExpDemand.initRequest();
      return new Promise((resolve, reject) => {
        this.$validator.validateAll("step-3").then(result => {
          if (result) {
            resolve(true)
          } else {
            reject("correct all values");
          }
        })
      })
    },
    validateStep5() {
      return new Promise((resolve, reject) => {
        this.$validator.validateAll("step-3").then(result => {
          if (result) {
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
    StepExpenseDemand,
    StepIncomePlan,
    StepSubprogram,
    StepIndicator,

  }
}
</script>

<style>
th.th-width-300 {
  min-width: 260px !important;
}

td.td-width-150 {
  min-width: 150px !important;
}

table tr td,
th {
  border: 1px solid #d7d7d7;
}

th {
  text-align: center !important;
  /* display: inline; */
}

th.rotate {
  transform: rotate(-90deg)
}

.vs-table--thead th .vs-table-text {
  text-transform: uppercase;
  font-weight: 600;
  text-align: center;
  display: inline !important;
}
</style>
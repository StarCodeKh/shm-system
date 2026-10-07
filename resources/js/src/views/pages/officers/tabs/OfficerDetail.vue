<template>
  <div class="vx-row">
    <!-- COL 1 -->
    <div class="vx-col w-full lg:w-1/4">
      <vx-card :title="$t('officer_detail')" class="mt-base">
          <!-- ACTION SLOT -->
          <template #actions>
            <feather-icon icon="MoreHorizontalIcon"></feather-icon>
          </template>

          <!-- USER BIO -->
          <!-- <div class="user-bio">
            <p>Tart I love sugar plum I love oat cake. Sweet roll caramels I love jujubes. Topping cake wafer.</p>
          </div> -->
          <div class="mt-5">
            <img :src="dataInfo.photo" @error="loadErrorImage" style="width: 100px" />
          </div>
          <!-- OTEHR DATA -->
           <div class="mt-5">
              <h6>{{ $t('identity_member_no') }}: {{ dataInfo.identity_member_no }}</h6>
          </div>
          <div class="mt-5">
              <h6>{{ $t('officer_name') }}: {{ dataInfo.first_name }} {{ dataInfo.last_name }}</h6>
          </div>

          <div class="mt-5">
              <h6>{{ $t('officer_entity') }}:​ {{ dataInfo.entity.name_kh }}</h6>
          </div>

          <div class="mt-5">
              <h6>{{ $t('officer_position') }}: {{ dataInfo.position }}</h6>
          </div>

          <div class="mt-5">
              <h6>{{ $t('officer_telephone_1') }}:​ {{ dataInfo.telephone_1 }}</h6>
          </div>

          <div class="mt-5">
              <h6>{{ $t('officer_email') }}: {{ dataInfo.email }}</h6>
          </div>

          <div class="mt-5">
              <h6>{{ $t('officer_address') }}: {{ dataInfo.address }}</h6>
          </div>

          <!-- <div class="social-links flex mt-4">
              <feather-icon svgClasses="h-7 w-7 cursor-pointer bg-primary p-1 text-white rounded" class="mr-2" icon="FacebookIcon"></feather-icon>
              <feather-icon svgClasses="h-7 w-7 cursor-pointer bg-primary p-1 text-white rounded" class="mr-2" icon="TwitterIcon"></feather-icon>
              <feather-icon svgClasses="h-7 w-7 cursor-pointer bg-primary p-1 text-white rounded" class="mr-2" icon="InstagramIcon"></feather-icon>
          </div> -->
      </vx-card>
    </div>

    <div class="vx-col w-full lg:w-3/4">
      <vs-tabs :position="'top'" class="tabs-shadow-none" id="profile-tabs" :key="isSmallerScreen">

        <!-- <vs-tab icon-pack="feather" icon="icon-lock" :label="$t('user_detail')">
          <div class="tab-text md:ml-4 md:mt-0 mt-4 ml-0 pt-2">
            <tab-user-detail />
          </div>
        </vs-tab>

        <vs-tab icon-pack="feather" icon="icon-lock" :label="$t('security_login')">n
          <div class="tab-text md:ml-4 md:mt-0 mt-4 ml-0 pt-2">
            <tab-security-login />
          </div>
        </vs-tab> -->
        
        <vs-tab icon-pack="feather" icon="icon-info" :label="$t('general')">
          <div class="tab-text md:ml-4 md:mt-0 mt-4 ml-0 pt-2">
            <tab-detail />
          </div>
        </vs-tab>

        <vs-tab icon-pack="feather" icon="icon-info" :label="$t('officer_document')">
          <div class="tab-text md:ml-4 md:mt-0 mt-4 ml-0 pt-2">
            <tab-document />
          </div>
        </vs-tab>

        <vs-tab icon-pack="feather" icon="icon-dollar-sign" :label="$t('repay')">
          <div class="tab-text md:ml-4 md:mt-0 mt-4 ml-0 pt-2">
            <tab-payable />
          </div>
        </vs-tab>

        <vs-tab icon-pack="feather" icon="icon-clock" :label="$t('payment_history')">
          <div class="tab-text md:ml-4 md:mt-0 mt-4 ml-0 pt-2">
            <tab-payment-history />
          </div>
        </vs-tab>

        <vs-tab icon-pack="feather" icon="icon-file" :label="$t('request')">
          <div class="tab-text md:ml-4 md:mt-0 mt-4 ml-0 pt-2">
            <tab-request />
          </div>
        </vs-tab>

        <vs-tab icon-pack="feather" icon="icon-dollar-sign" :label="$t('incentive')">
          <div class="tab-text md:ml-4 md:mt-0 mt-4 ml-0 pt-2">
            <tab-incentive />
          </div>
        </vs-tab>

         <vs-tab icon-pack="feather" icon="icon-lock" :label="$t('credentail_user_access')">
          <div class="tab-text md:ml-4 md:mt-0 mt-4 ml-0 pt-2">
            <tab-credential-user />
          </div>
        </vs-tab>

        <vs-tab icon-pack="feather" icon="icon-calendar" :label="$t('recurring_schedule')">
          <div class="tab-text md:ml-4 md:mt-0 mt-4 ml-0 pt-2">
            <tab-recurring />
          </div>
        </vs-tab>

      </vs-tabs>
    </div>
  </div>
</template>

<script>
import axios from "@/axios.js";
import apiConfig from "@/apiConfig.js";
import { ref } from 'vue';
import TabCredentialUser from "./_TabCredentialUser.vue";
import TabDetail from "./_TabDetail.vue";
import TabDocument from "./_TabDocument.vue";
import TabIncentive from "./_TabIncentive.vue";
import TabPaymentHistory from "./_TabPaymentHistory.vue";
import TabPayable from "./_TabPayable.vue";
import TabRequest from "./_TabRequest.vue";
import TabRecurring from "./_TabRecurring.vue";

export default {
  components: {
    TabCredentialUser,
    TabDetail,
    TabDocument,
    TabIncentive,
    TabPaymentHistory,
    TabPayable,
    TabRequest,
    TabRecurring
  },
  data() {
    return {
      api: apiConfig._apiOfficer,
      parentDataInfo:{},
      data: {
          data: [
          ],
          limit: 10,
          total: 3,
      },
      dataInfo: null,
      notFoundFile: "/images/no-account.png"
    }
  },
  methods: {
    getDataInfo(){
      let _id = this.$route.params.id;
      return new Promise((resolve, reject) => {
        axios.get(this.api + "/" +_id)
            .then((response) => {
            this.data = response.data;
            this.dataInfo = response.data.data.data;
            this.$refs.refModalForm.showDataForm(response.data.data);
            this.$vs.loading.close();
        }).catch((error) => { 
          reject(error) 
        })
      })
    },
    loadErrorImage(event) {
      event.target.src = this.notFoundFile;
    },
  },
  created() {
    this.$vs.loading();
    this.getDataInfo();
  },
  computed: {
    isSmallerScreen() {
      return this.$store.state.windowWidth < 768
    }
  }
}
</script>

<style lang="scss">
#profile-tabs {
  .vs-tabs--content {
    padding: 0;
  }
}
</style>
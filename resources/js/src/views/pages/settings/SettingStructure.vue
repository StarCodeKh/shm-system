<template>
  <vx-card no-shadow>
    <program-structure></program-structure>
    <!-- <div class="mb-base">
      <h3 class="mb-4">កំពុងស្ថិតក្នុងការត្រួតពិនិត្យ</h3>
    </div> -->

    <!-- Save & Reset Button -->
    <!-- <div class="flex flex-wrap items-center justify-end mt-base">
      <vs-button color="primary" type="filled" class="ml-auto mt-2">{{ $t('btn_save') }}</vs-button>
      <vs-button color="warning" type="border" class="ml-4 mt-2">{{ $t('btn_reset') }}</vs-button>
    </div> -->

  </vx-card>
</template>

<script>
import ProgramStructure from '@/views/pages/modules/program-management/Index.vue';

export default {
  data() {
    return {

    }
  },
  components: {
    ProgramStructure,
  },
  computed: {
    activeUserInfo() {
      return this.$store.state.AppActiveUser
    },
  }
}
</script>

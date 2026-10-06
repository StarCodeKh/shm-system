<template>
  <vx-card>
    <audit />
  </vx-card>
</template>
<script>
import moduleDataList from "@/store/data-list/moduleDataList.js";
import Audit from '@/views/pages/settings/_Audit.vue';
export default {
  components: {
    Audit,
  },
  data() {
    return {
      // popupActive: false,
      // products: [],
      isMounted: false,

      // Data Sidebar

    }
  },
  computed: {

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
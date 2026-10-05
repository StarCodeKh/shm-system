<template>
  <vx-card>
    <div class="mt-5">
      <vs-tabs alignment="center">
        <audit></audit>
      </vs-tabs>
    </div>
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
      selected: [],
      // products: [],
      itemsPerPage: 4,
      isMounted: false,

      // Data Sidebar
      addNewDataSidebar: false,
      sidebarData: {},

    }
  },
  computed: {

  },
  methods: {

    toggleDataSidebar(val = false) {
      this.addNewDataSidebar = val
    },

  },
  created() {

  },
  mounted() {
    this.isMounted = true;
  }
}
</script>
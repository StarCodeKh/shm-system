<template>
  <!-- CART DROPDOWN -->
  <feather-icon icon="SettingsIcon" @click="$router.push('/setting')" class="cursor-pointer -ml-1 mr-6 mt-1"/>
</template>

<script>
import VuePerfectScrollbar from 'vue-perfect-scrollbar'

export default {
  components: {
    VuePerfectScrollbar
  },
  data() {
    return {
      settings: { // perfectscrollbar settings
        maxScrollbarLength: 60,
        wheelSpeed: .60,
      },
    }
  },
  computed: {
    // CART DROPDOWN
    cartItems() {
        return this.$store.state.eCommerce.cartItems.slice().reverse();
    },
  },
  methods: {
    removeItemFromCart(item) {
        this.$store.dispatch('eCommerce/toggleItemInCart', item)
    },
  }
}

</script>

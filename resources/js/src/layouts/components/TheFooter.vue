<template functional>
    <footer class="the-footer flex-wrap justify-between" :class="classes">
        <span>&copy; {{ new Date().getFullYear() }} <a href="#" target="_blank" rel="nofollow">SHM System</a>. {{ parent.$t('footer_rights') }}</span>
        <span class="md:flex hidden items-center">
            <span>{{ parent.$t('footer_ministry') }}</span>
            <!-- <feather-icon icon="HeartIcon" svgClasses="stroke-current text-danger w-6 h-6" class="ml-2" /> -->
        </span>

        <!-- buyNow component -->
        <!-- <component :is="injections.components.BuyNow"></component> -->
    </footer>
</template>

<script>
export default {
    name: "the-footer",
    props: {
        classes: {
            type: String,
        },
    },
    inject: {
      components:{
          default: {
          }
      }
    }
}
</script>

<template functional>
    <footer class="the-footer flex-wrap justify-between" :class="classes">
        <span>បានចម្លងតាមច្បាប់ដើម. {{ new Date().getFullYear() }} <a href="#" target="_blank" rel="nofollow">SHM System</a>, រក្សាសិទ្ធគ្រប់យ៉ាង</span>
        <span class="md:flex hidden items-center">
            <span>ក្រសួងសេដ្ឋកិច្ច និងហិរញ្ញវត្ថុ</span>
            <!-- <feather-icon icon="HeartIcon" svgClasses="stroke-current text-danger w-6 h-6" class="ml-2" /> -->
        </span>

        <!-- buyNow component -->
        <!-- <component :is="injections.components.BuyNow"></component> -->
    </footer>
</template>

<script>
import BuyNow from '../../components/BuyNow.vue'

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
            BuyNow
          }
      }
    }
}
</script>

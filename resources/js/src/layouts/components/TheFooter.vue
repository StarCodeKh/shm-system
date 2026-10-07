<template>
    <footer class="the-footer flex-wrap justify-between" :class="classes">
        <span>&copy; {{ new Date().getFullYear() }} <a href="#" target="_blank" rel="nofollow">SHM System</a>. {{ $t('footer_rights') }}</span>
        <span class="md:flex hidden items-center">
            <span>{{ $t('footer_ministry') }}</span>
        </span>

        <!-- buyNow component -->
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

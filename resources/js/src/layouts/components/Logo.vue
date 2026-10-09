<template>
    <div>
        <img width="100%" :src="url" :alt="alt || 'logo'" class="mx-auto" @error="failed = true">
    </div>
</template>

<script>
import builtIn from "@assets/images/logo/logo.png"

// The logo from Settings › General (src), or the built-in one when none is set or the file can't load
export default {
    name: "logo",
    props: {
        src: { type: String, default: null },
        alt: { type: String, default: null },
    },
    data() {
        return { failed: false }
    },
    computed: {
        url() {
            if (!this.src || this.failed) return builtIn
            return /^(https?:)?\/\//.test(this.src) || this.src.startsWith("/") ? this.src : "/" + this.src
        },
    },
    watch: {
        src() {
            this.failed = false // a newly uploaded logo gets its own chance to load
        },
    },
}
</script>

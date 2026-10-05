<template>
    <div class="vx-col lg:w-1/2 w-full">
        <vs-input v-validate="'required|alpha_dash'" placeholder="ពិព័ណ៌នា" name="description" v-model="description"
            class="mt-5 w-full" />
    </div>
</template>


<script>
    export default {
        props: {
            formAttributes: {
                required: true
            },
        }
    }
</script>
<template>
    <div class="vx-col lg:w-1/2 w-full">
        <vs-input :v-validate="'required|alpha'" :placeholder="$tt('ឈ្មោះគោលបំណង')" name="name" class="mt-5 w-full" />
        <span class="text-danger text-sm" v-show="errors.has('name')">{{ errors.first('name')
        }}</span>
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
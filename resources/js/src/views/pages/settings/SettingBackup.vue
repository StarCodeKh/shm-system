<template>
    <vx-card no-shadow>

        <div class="mb-base">
            <h6 class="mb-4">Run Schedule Backup</h6>

            <div class="flex items-center mb-4">
                <vs-switch v-model="comment" />
                <span class="ml-4">Between 6:00-12:00PM</span>
            </div>
            <div class="flex items-center mb-4">
                <vs-switch v-model="answer" />
                <span class="ml-4">Between 21:00-24:00PM</span>
            </div>
            <div class="flex items-center mb-4">
                <vs-switch v-model="everyday" />
                <span class="ml-4">Every Day</span>
            </div>
        </div>

        <!-- Save & Reset Button -->
        <div class="flex flex-wrap items-center justify-end mt-base">
            <vs-button color="primary" type="filled" class="ml-auto mt-2">{{ $t('btn_save') }}</vs-button>
            <vs-button color="warning" type="border" class="ml-4 mt-2">{{ $t('btn_reset') }}</vs-button>
        </div>

    </vx-card>
</template>

<script>
export default {
    data() {
        return {
            comment: false,
            answer: false,
            everyday: true,
            news: false,
            product_update: false,
            blog: true,
        }
    },
}
</script>

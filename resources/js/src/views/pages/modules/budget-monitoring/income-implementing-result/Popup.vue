<template>
    <div>
        <span v-if="proPlan==1">
            <income-implementing-result></income-implementing-result>
        </span>
        <span v-if="proPlan==2">
            <expense-implementing-result></expense-implementing-result>
        </span>
    </div>
</template>

<script>
    import apiConfig from "@/apiConfig.js"
    import { ref } from 'vue';
    import IncomeImplementingResult from './_income_implementing_result.vue';
    import ExpenseImplementingResult from './_expenditure_implementing_result.vue';
    export default {
        props: {
            proPlan: {
                required: true,
            }
        },
        components: { IncomeImplementingResult, ExpenseImplementingResult },
        data() {
            return {
            data:[]
            }
        }
    }
</script>
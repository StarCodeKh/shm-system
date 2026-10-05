<template>
    <div>
        <span v-if="proPlan==1">
            <income-program></income-program>
        </span>
        <span v-if="proPlan==2">
            <expense-program></expense-program>
        </span>
    </div>
</template>

<script>
    import apiConfig from "@/apiConfig.js"
    import { ref } from 'vue';
    import IncomeProgram from './_income_program.vue';
    import ExpenseProgram from './_expense_program.vue';
    export default {
        props: {
            proPlan: {
                required: true,
            }
        },
        components: { IncomeProgram, ExpenseProgram },
        data() {
            return {
            data:[]
            }
        }
    }
</script>
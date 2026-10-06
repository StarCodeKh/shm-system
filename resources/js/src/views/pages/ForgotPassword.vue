

<template>
    <div class="h-screen flex w-full bg-img">
        <div class="vx-col w-4/5 sm:w-4/5 md:w-3/5 lg:w-3/4 xl:w-3/5 mx-auto self-center">
            <vx-card>
                <div slot="no-body" class="full-page-bg-color">
                    <div class="vx-row">
                        <div class="vx-col hidden sm:hidden md:hidden lg:block lg:w-1/2 mx-auto self-center">
                            <img width="250px" src="@assets/images/pages/logo.png" alt="login" class="mx-auto">
                        </div>
                        <div class="vx-col sm:w-full md:w-full lg:w-1/2 mx-auto self-center d-theme-dark-bg">
                            <div class="p-8">
                                <div class="vx-card__title mb-8">
                                    <h4 class="mb-4">Recover your password</h4>
                                    <p>Please enter your email address and we'll send you instructions on how to reset your password.</p>
                                </div>

                                <vs-input type="email" label-placeholder="Email" v-model="value1" class="w-full mb-8" />
                                <vs-button color="dark" type="border" to="/pages/login" class="px-4 w-full md:w-auto">{{ $t('btn_back_to_login') }}</vs-button>
                                <vs-button color="primary" type="filled" class="float-right px-4 w-full md:w-auto mt-3 mb-8 md:mt-0 md:mb-0">{{ $t('btn_recover_password') }}</vs-button>
                            </div>
                        </div>
                    </div>
                </div>
            </vx-card>
        </div>
    </div>
</template>

<script>
export default {
    data() {
        return {
            value1: ''
        }
    }
}
</script>

<template>
    <!-- Same look as the startup screen (application.blade.php) -->
    <div class="auth-loading" role="status" aria-label="Loading">
        <div class="auth-loading__logo">
            <img src="@assets/images/logo/logo.png" alt="">
        </div>
        <div class="auth-loading__title">SHM-System</div>
    </div>
</template>

<script>
    export default {
        methods: {
            handleLoginEvent(data) {
                this.$store.dispatch('updateUserInfo', {
                    displayName: data.profile.name,
                    email: data.profile.email,
                    photoURL: data.profile.picture,
                    providerId: data.profile.sub.substr(0, data.profile.sub.indexOf('|')),
                    uid: data.profile.sub
                })
                this.$router.push(data.state.target || "/");
            }
        },
        created() {
            this.$auth.handleAuthentication()
            if (this.$auth.isAuthenticated()) this.$router.push('/').catch(() => {})
        }
    }

</script>

<style lang="scss" scoped>
    .auth-loading {
        position: fixed;
        inset: 0;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 16px;
        background: linear-gradient(150deg, #5972ac 0%, #324693 100%); // sidebar blue, as the startup screen
        color: #fff;

        &__logo {
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 76px;
            height: 76px;

            img {
                width: 56px; height: 56px; padding: 4px; border-radius: 50%; object-fit: contain;
                background: #fff;
                box-shadow: 0 0 0 4px rgba(255, 255, 255, .18), 0 10px 24px rgba(0, 0, 0, .25);
            }

            &::before {
                content: '';
                position: absolute;
                inset: 0;
                border-radius: 50%;
                border: 3px solid rgba(255, 255, 255, .18);
                border-top-color: #fff;
                animation: auth-spin .75s linear infinite;
            }
        }

        &__title { font-size: 1rem; font-weight: 600; }
    }

    .theme-dark .auth-loading { background: linear-gradient(150deg, #324693 0%, #10163a 100%); }

    @keyframes auth-spin { to { transform: rotate(360deg); } }
</style>

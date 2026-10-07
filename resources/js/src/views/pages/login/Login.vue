<template>
    <div id="page-login" class="login-page">
        <div class="login-shell">

            <!-- Brand panel -->
            <aside class="login-brand">
                <span class="login-brand__ring login-brand__ring--lg"></span>
                <span class="login-brand__ring login-brand__ring--sm"></span>

                <div class="login-brand__content">
                    <div class="login-brand__logo">
                        <img src="@assets/images/pages/logo.png" alt="logo">
                    </div>
                    <h2 class="login-brand__title">{{ $t('welcome') }}</h2>
                    <div class="login-brand__divider"></div>
                </div>

                <p class="login-brand__footer">&copy; {{ year }}</p>
            </aside>

            <!-- Form panel -->
            <main class="login-form">
                <!-- Same sliding language switch as the top bar -->
                <div class="login-lang">
                    <i18n />
                </div>

                <div class="login-form__inner">
                    <img src="@assets/images/pages/logo.png" alt="logo" class="login-form__mobile-logo">

                    <h1 class="login-form__title">{{ $t('login') }}</h1>
                    <p class="login-form__subtitle">{{ $t('Authentication') }}</p>

                    <login-jwt />
                </div>
            </main>

        </div>
    </div>
</template>


<script>
import LoginJwt from "./LoginJWT.vue"
import I18n from "@/layouts/components/navbar/components/I18n.vue"

export default {
    components: {
        LoginJwt,
        I18n
    },
    data() {
        return {
            year: new Date().getFullYear()
        }
    }
}
</script>

<style lang="scss">
// Same blue as the sidebar and the loading screen
$login-blue: #5972ac;
$login-blue-dark: #324693;

.login-page {
    min-height: 100vh;
    width: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 24px 16px;
    background:
        radial-gradient(circle at 0% 0%, rgba(var(--vs-primary), .14), transparent 40%),
        radial-gradient(circle at 100% 100%, rgba(50, 70, 147, .14), transparent 45%),
        #f3f4f8;
}

.login-shell {
    width: 100%;
    max-width: 1040px;
    min-height: 600px;
    display: flex;
    background: #fff;
    border-radius: 20px;
    overflow: hidden;
    box-shadow: 0 30px 60px -20px rgba(23, 23, 74, .25), 0 0 0 1px rgba(23, 23, 74, .04);
    animation: login-shell-in .5s cubic-bezier(.22, 1, .36, 1) backwards;
}

@keyframes login-shell-in {
    from { opacity: 0; transform: translateY(16px) scale(.98); }
}

@media (prefers-reduced-motion: reduce) {
    .login-shell { animation: none; }
}

/* ---------- Brand panel ---------- */
.login-brand {
    position: relative;
    flex: 0 0 46%;
    display: flex;
    flex-direction: column;
    justify-content: center;
    padding: 48px 40px;
    color: #fff;
    overflow: hidden;
    background: linear-gradient(150deg, $login-blue 0%, $login-blue-dark 100%);

    &__ring {
        position: absolute;
        border-radius: 50%;
        border: 1px solid rgba(255, 255, 255, .08);
        pointer-events: none;

        &--lg {
            width: 520px;
            height: 520px;
            top: -180px;
            right: -220px;
            box-shadow: inset 0 0 0 60px rgba(255, 255, 255, .02);
        }
        &--sm {
            width: 300px;
            height: 300px;
            bottom: -120px;
            left: -110px;
            background: rgba(var(--vs-primary), .12);
            border: none;
        }
    }

    &__content {
        position: relative;
        text-align: center;
    }

    &__logo {
        width: 210px;
        height: 210px;
        margin: 0 auto 32px;
        padding: 18px;
        border-radius: 50%;
        background: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 0 0 10px rgba(255, 255, 255, .08), 0 20px 40px rgba(0, 0, 0, .25);

        img {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
        }
    }

    &__title {
        color: #fff;
        font-size: 1.35rem;
        line-height: 1.8;
        font-weight: 600;
        margin: 0 auto;
        max-width: 340px;
    }

    &__divider {
        width: 56px;
        height: 4px;
        margin: 20px auto 0;
        border-radius: 4px;
        background: rgb(var(--vs-primary));
    }

    &__footer {
        position: absolute;
        left: 0;
        right: 0;
        bottom: 20px;
        text-align: center;
        font-size: .8rem;
        color: rgba(255, 255, 255, .5);
    }
}

/* ---------- Form panel ---------- */
.login-lang {
    position: absolute;
    top: 20px;
    right: 20px;

    .nav-lang-switch {
        margin-right: 0;
    }
}

.login-form {
    position: relative;
    flex: 1;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 48px 56px;

    &__inner {
        width: 100%;
        max-width: 380px;
    }

    &__mobile-logo {
        display: none;
        width: 96px;
        margin: 0 auto 20px;
    }

    &__title {
        font-size: 1.4rem;
        font-weight: 700;
        color: #1f2140;
        margin-bottom: 6px;
    }

    &__subtitle {
        color: #8a8ca5;
        margin-bottom: 24px;
        font-size: .9rem;
    }
}

@media (max-width: 900px) {
    .login-shell {
        max-width: 440px;
        min-height: 0;
    }
    .login-brand {
        display: none;
    }
    .login-form {
        padding: 40px 28px;

        &__mobile-logo {
            display: block;
        }
        &__title,
        &__subtitle {
            text-align: center;
        }
    }
}

/* Dark theme */
.theme-dark {
    .login-page {
        background: #10163a;
    }
    .login-shell {
        background: #262c49;
    }
    .login-brand {
        background: linear-gradient(150deg, #324693 0%, #10163a 100%);
    }
    .login-form__title {
        color: #fff;
    }
}
</style>

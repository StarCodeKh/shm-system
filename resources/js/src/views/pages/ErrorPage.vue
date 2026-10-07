<template>
    <main class="error-page" :class="'error-page--' + code">
        <div class="error-page__inner">
            <img class="error-page__logo" src="@assets/images/logo/logo.png" alt="">

            <p class="error-page__code" aria-hidden="true">{{ code }}</p>
            <h1 class="error-page__title">{{ $t(`error_${code}_title`) }}</h1>
            <p class="error-page__text">{{ $t(`error_${code}_text`) }}</p>

            <p v-if="requested" class="error-page__path">
                <span>{{ $t('error_requested') }}</span>
                <code>{{ requested }}</code>
            </p>

            <div class="error-page__actions">
                <button v-if="canGoBack" type="button" class="error-page__btn" @click="$router.back()">
                    <feather-icon icon="ArrowLeftIcon" svgClasses="h-4 w-4" />
                    <span>{{ $t('btn_go_back') }}</span>
                </button>
                <router-link :to="home.to" class="error-page__btn error-page__btn--primary">
                    <feather-icon :icon="home.icon" svgClasses="h-4 w-4" />
                    <span>{{ $t(home.label) }}</span>
                </router-link>
            </div>
        </div>
    </main>
</template>

<script>
// One page for every error: the code comes from the route (meta.errorCode: 404, 403 or 500),
// the texts from i18n (error_<code>_title / error_<code>_text), so it follows the app's language.
export default {
    name: 'ErrorPage',
    computed: {
        code() {
            return [403, 404, 500].includes(this.$route.meta.errorCode) ? this.$route.meta.errorCode : 404
        },
        // An unknown address shows the 404 in place, so the address that failed is still known
        requested() {
            return this.$route.name === 'page-not-found' ? this.$route.fullPath : null
        },
        signedIn() {
            return Boolean(localStorage.getItem('accessToken') && localStorage.getItem('userInfo'))
        },
        home() {
            return this.signedIn
                ? { to: '/dashboard', icon: 'HomeIcon', label: 'btn_back_home' }
                : { to: '/pages/login', icon: 'LogInIcon', label: 'btn_go_login' }
        },
        // Opened straight from a link or the address bar: there is nothing to go back to
        canGoBack() {
            return Boolean(window.history.state && window.history.state.back)
        },
    },
}
</script>

<style lang="scss">
.error-page {
    display: flex;
    align-items: center;
    justify-content: center;
    min-height: 100vh;
    padding: 32px 16px;
    background:
        radial-gradient(circle at 15% 10%, rgba(var(--vs-primary), .08), transparent 40%),
        radial-gradient(circle at 85% 90%, rgba(var(--brand-navy), .06), transparent 45%),
        #f7f8fb;

    &__inner {
        width: 100%;
        max-width: 480px;
        text-align: center;
        animation: error-page-in .35s ease-out both;
    }

    &__logo {
        width: 76px;
        height: 76px;
        margin: 0 auto 20px;
        object-fit: contain;
    }

    &__code {
        margin: 0;
        font-size: clamp(72px, 16vw, 112px);
        font-weight: 800;
        line-height: 1;
        letter-spacing: -.04em;
        color: rgb(var(--vs-primary));
        font-variant-numeric: tabular-nums;
    }

    &--403 &__code { color: rgb(var(--brand-gold)); }
    &--500 &__code { color: rgb(var(--vs-danger)); }

    &__title {
        margin: 16px 0 8px;
        font-size: 24px;
        font-weight: 700;
        line-height: 1.5;
        color: #2b2d42;
    }

    &__text {
        margin: 0 auto;
        max-width: 400px;
        font-size: 15px;
        line-height: 1.7;
        color: #6e7191;
    }

    &__path {
        display: inline-flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: center;
        gap: 6px;
        max-width: 100%;
        margin: 20px 0 0;
        padding: 6px 12px;
        font-size: 13px;
        color: #6e7191;
        background: #fff;
        border: 1px solid #e4e6ef;
        border-radius: 8px;

        code {
            overflow-wrap: anywhere;
            font-family: ui-monospace, SFMono-Regular, Menlo, monospace;
            color: #2b2d42;
        }
    }

    &__actions {
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        gap: 12px;
        margin-top: 32px;
    }

    // ".error-page .error-page__btn": stronger than the global a / a:visited colour, which would
    // otherwise turn the text green on the green button once the link has been visited
    .error-page__btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        min-width: 150px;
        height: 44px;
        padding: 0 20px;
        font-size: 14px;
        font-weight: 600;
        color: #2b2d42;
        background: #fff;
        border: 1px solid #dfe1ea;
        border-radius: 10px;
        cursor: pointer;
        transition: border-color .15s ease, background .15s ease, box-shadow .15s ease, transform .15s ease;

        &:visited { color: #2b2d42; }
        &:hover { border-color: rgb(var(--vs-primary)); color: rgb(var(--vs-primary)); }
        &:active { transform: translateY(1px); }
        &:focus-visible { outline: 2px solid rgb(var(--vs-primary)); outline-offset: 2px; }

        &--primary,
        &--primary:visited {
            color: #fff;
            background: rgb(var(--vs-primary));
            border-color: rgb(var(--vs-primary));
            box-shadow: 0 6px 16px rgba(var(--vs-primary), .25);

            &:hover { color: #fff; box-shadow: 0 8px 20px rgba(var(--vs-primary), .35); }
        }
    }

    @media (max-width: 420px) {
        .error-page__btn { flex: 1 1 100%; }
    }
}

@keyframes error-page-in {
    from { opacity: 0; transform: translateY(8px); }
    to { opacity: 1; transform: none; }
}

@media (prefers-reduced-motion: reduce) {
    .error-page__inner { animation: none; }
}

.theme-dark .error-page {
    background: #10163a;

    &__title, &__path code { color: #e4e6f5; }
    &__text, &__path { color: #a7abc6; }
    &__path { background: #262c49; border-color: #414561; }
    .error-page__btn:not(.error-page__btn--primary) { color: #e4e6f5; background: #262c49; border-color: #414561; }
}
</style>

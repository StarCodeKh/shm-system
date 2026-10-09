<template>
    <form class="login-jwt" @submit.prevent="loginJWT" novalidate>
        <!-- Alert -->
        <transition name="login-alert">
            <div v-if="alert" :key="alert.type + alert.title" class="login-alert" :class="'login-alert--' + alert.type" role="alert">
                <span class="login-alert__icon"><feather-icon :icon="alertIcon" svgClasses="h-4 w-4" /></span>
                <div class="login-alert__body">
                    <p class="login-alert__title">{{ $t(alert.title) }}</p>
                    <p class="login-alert__text">{{ $t(alert.text, { seconds: waitSeconds }) }}</p>
                </div>
                <button type="button" class="login-alert__close" @click="alert = null">
                    <feather-icon icon="XIcon" svgClasses="h-3 w-3" />
                </button>
            </div>
        </transition>

        <!-- Email -->
        <div class="login-field" :class="{ 'has-error': errors.has('email') }">
            <label for="login-email">{{ $t('email') }}</label>
            <div class="login-field__control">
                <span class="login-field__icon"><feather-icon icon="MailIcon" svgClasses="h-4 w-4" /></span>
                <input id="login-email" v-model.trim="email" v-validate="'required|email|min:3'" data-vv-validate-on="blur"
                    name="email" type="email" autocomplete="username" placeholder="name@example.com">
            </div>
            <span v-if="errors.has('email')" class="login-field__error">{{ fieldError('email') }}</span>
        </div>

        <!-- Password -->
        <div class="login-field" :class="{ 'has-error': errors.has('password') }">
            <label for="login-password">{{ $t('password') }}</label>
            <div class="login-field__control">
                <span class="login-field__icon"><feather-icon icon="LockIcon" svgClasses="h-4 w-4" /></span>
                <input id="login-password" v-model="password" v-validate="'required|min:3|max:50'" data-vv-validate-on="blur"
                    name="password" :type="showPassword ? 'text' : 'password'" autocomplete="current-password"
                    placeholder="••••••••">
                <button type="button" class="login-field__toggle" tabindex="-1" @click="showPassword = !showPassword">
                    <feather-icon :icon="showPassword ? 'EyeOffIcon' : 'EyeIcon'" svgClasses="h-4 w-4" />
                </button>
            </div>
            <span v-if="errors.has('password')" class="login-field__error">{{ fieldError('password') }}</span>
        </div>

        <div class="flex items-center justify-between mt-2 mb-6">
            <vs-checkbox v-model="checkbox_remember_me" class="login-remember">{{ $t('remember_me') }}</vs-checkbox>
        </div>

        <button type="submit" class="login-submit" :disabled="!validateForm || loading || waitSeconds > 0">
            <span v-if="loading" class="login-submit__spinner"></span>
            <span>{{ loading ? $t('login_signing_in') : waitSeconds > 0 ? $t('login_wait_seconds', { seconds: waitSeconds }) : $t('login') }}</span>
        </button>
    </form>
</template>

<script>
import axios, { retryAfterSeconds } from "@/axios.js";

// i18n keys for each failure kind shown in the inline alert
const ALERTS = {
    credentials: { type: 'danger', title: 'login_err_title_credentials', text: 'login_err_credentials' },
    network: { type: 'danger', title: 'login_err_title_network', text: 'login_err_network' },
    server: { type: 'danger', title: 'login_err_title_server', text: 'login_err_server' },
    throttle: { type: 'warning', title: 'login_err_title_throttle', text: 'login_err_throttle_wait' },
}

export default {
    data() {
        return {
            // Pre-filled only on a developer machine (npm run dev); production builds leave these out entirely
            email: import.meta.env.DEV ? 'admin@admin.com' : '',
            password: import.meta.env.DEV ? 'Ibs@dmin123' : '',
            checkbox_remember_me: true,
            showPassword: false,
            loading: false,
            alert: null,
            // After too many attempts: when sign-in opens again, and a clock for the countdown
            waitUntil: 0,
            now: Date.now(),
            ticker: null,
            api: "/api/v1/role_menu/permission/index",
        }
    },
    computed: {
        validateForm() {
            return !this.errors.any() && this.email != '' && this.password != '';
        },
        waitSeconds() {
            return Math.max(0, Math.ceil((this.waitUntil - this.now) / 1000))
        },
        alertIcon() {
            return this.alert && this.alert.type === 'warning' ? 'AlertTriangleIcon' : 'AlertCircleIcon'
        },
    },
    watch: {
        // Hide the alert once the user starts correcting their input (the wait alert stays until it ends)
        email() { if (!this.waitSeconds) this.alert = null },
        password() { if (!this.waitSeconds) this.alert = null },
    },
    beforeUnmount() {
        clearInterval(this.ticker)
    },
    methods: {
        // Translated replacement for vee-validate's English-only messages
        fieldError(field) {
            const error = this.errors.items.find(e => e.field === field)
            if (!error) return ''
            const rules = {
                email: {
                    required: this.$t('login_val_email_required'),
                    email: this.$t('login_val_email_invalid'),
                    min: this.$t('login_val_email_invalid'),
                },
                password: {
                    required: this.$t('login_val_password_required'),
                    min: this.$t('login_val_password_min', { n: 3 }),
                    max: this.$t('login_val_password_max', { n: 50 }),
                },
            }
            return rules[field][error.rule] || error.msg
        },
        alertFor(error) {
            const response = error && error.response
            if (response) {
                if (response.status === 429) return ALERTS.throttle
                if (response.status === 401 || response.status === 422) return ALERTS.credentials
                return ALERTS.server
            }
            // Axios errors without a response never reached the server
            if (error && (error.isAxiosError || error.request)) return ALERTS.network
            // The store rejects with a plain object when the API returns no user
            return ALERTS.credentials
        },
        // Count down the wait after too many attempts; sign-in opens again at zero
        startWait(error) {
            this.waitUntil = Date.now() + retryAfterSeconds(error) * 1000
            this.now = Date.now()
            clearInterval(this.ticker)
            this.ticker = setInterval(() => {
                this.now = Date.now()
                if (this.waitSeconds > 0) return
                clearInterval(this.ticker)
                if (this.alert === ALERTS.throttle) this.alert = null
            }, 250)
        },
        loginJWT() {
            if (!this.validateForm || this.loading || this.waitSeconds) return

            this.alert = null
            this.loading = true

            const payload = {
                checkbox_remember_me: this.checkbox_remember_me,
                userDetails: {
                    email: this.email,
                    password: this.password
                }
            }

            this.$store.dispatch('auth/loginJWT', payload)
                .then((res) => {
                    this.loading = false
                    if (res.data.accessToken) {
                        const user = res.data.userData || {}
                        this.$vs.notify({
                            title: this.$t('login_success_title'),
                            text: this.$t('login_success_text', { name: user.username || user.name || user.email || '' }),
                            iconPack: 'feather',
                            icon: 'icon-check-circle',
                            color: 'success',
                            position: 'top-right',
                            time: 3000
                        })
                        this.retireveRoleMenu();
                    }
                }).catch((error) => {
                    this.loading = false
                    this.alert = this.alertFor(error)
                    if (this.alert === ALERTS.throttle) this.startWait(error)
                })
        },
        retireveRoleMenu() {
            const params = {
                sort: "id",
                order: "desc",
                page_number: 1,
                limit : 300
            };

            // This page is already navigated away from, so report failures as a toast
            return axios.post(this.api, params)
                .then((response) => {
                    localStorage.setItem("role_menus",  JSON.stringify(response.data.permissions));
                }).catch(() => {
                    this.$vs.notify({
                        title: this.$t('login_err_title_permission'),
                        text: this.$t('login_err_permission'),
                        iconPack: 'feather',
                        icon: 'icon-alert-circle',
                        color: 'danger',
                        position: 'top-right'
                    })
                })
        },
    }
}

</script>

<style>
.login-jwt {
    /* "Remember me": the same checkbox as the forms (20px box, 6px corners, theme colour), inline under the fields */
    .login-remember.con-vs-checkbox {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        margin: 0 !important;
        cursor: pointer;
        user-select: none;

        .vs-checkbox {
            flex-shrink: 0;
            width: 20px !important;
            height: 20px !important;
            margin: 0 !important;
            border-radius: 6px !important;
        }

        .con-slot-label {
            font-size: 14px;
            line-height: 20px;
            color: #4b4d68;
        }

        /* Keyboard focus only (not shown for mouse clicks) */
        &:has(:focus-visible) {
            border-radius: 6px;
            outline: 2px solid rgb(var(--vs-primary));
            outline-offset: 4px;
        }
    }

    .login-field {
        margin-bottom: 16px;

        /* Same label, control and button as every other form (tokens in custom.css :root) */
        label {
            display: block;
            font-size: .875rem;
            font-weight: 600;
            color: #4b4d68;
            margin-bottom: 6px;
        }

        &__control {
            position: relative;
        }

        /* Wrapper span (not the feather-icon itself): Tailwind's `.relative` is */
        /* !important and would otherwise un-pin the icon. */
        &__icon {
            position: absolute;
            top: 0;
            bottom: 0;
            left: 12px;
            display: flex;
            align-items: center;
            color: #a3a5bb;
            pointer-events: none;
            transition: color .15s;

            .feather-icon {
                display: flex;
            }
        }

        input {
            display: block;
            width: 100%;
            height: var(--input-height, 42px);
            padding: 0 40px 0 38px;
            font-size: .95rem;
            font-family: inherit;
            color: #2c2e45;
            background: #fff;
            border: 1px solid var(--input-border, #d8d9e3) !important;
            border-radius: var(--input-radius, 10px) !important;
            outline: none;
            transition: border-color .15s, box-shadow .15s;

            &::placeholder {
                color: #b4b6c8;
            }

            &:hover {
                border-color: var(--input-border-hover, #c3c5d4) !important;
            }

            &:focus {
                border-color: rgb(var(--vs-primary)) !important;
            }
        }

        &__control:focus-within &__icon {
            color: rgb(var(--vs-primary));
        }

        &__toggle {
            position: absolute;
            top: 50%;
            right: 6px;
            transform: translateY(-50%);
            width: 30px;
            height: 30px;
            padding: 0;
            border: none;
            background: transparent;
            color: #a3a5bb;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 6px;

            .feather-icon {
                display: flex;
            }

            &:hover {
                color: #5e6078;
                background: #f1f2f6;
            }
        }

        &__error {
            display: block;
            margin-top: 4px;
            font-size: .78rem;
            color: rgb(var(--vs-danger));
        }

        &.has-error {
            input {
                border-color: rgb(var(--vs-danger)) !important;
            }
            .login-field__icon {
                color: rgb(var(--vs-danger));
            }
        }
    }

    .login-submit {
        width: 100%;
        height: var(--input-height, 42px);
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        font-size: .95rem;
        font-weight: 600;
        font-family: inherit;
        color: #fff;
        border: none;
        border-radius: var(--input-radius, 10px);
        cursor: pointer;
        background: rgb(var(--vs-primary));
        box-shadow: 0 6px 14px -6px rgba(var(--vs-primary), .7);
        transition: filter .15s, box-shadow .15s, opacity .15s, transform .15s;

        &:hover:not(:disabled) {
            filter: brightness(.95);
            box-shadow: 0 8px 18px -8px rgba(var(--vs-primary), .8);
        }

        &:active:not(:disabled) {
            transform: scale(.98);
        }

        &:disabled {
            opacity: .6;
            cursor: not-allowed;
        }

        &__spinner {
            width: 16px;
            height: 16px;
            border-radius: 50%;
            border: 2px solid rgba(255, 255, 255, .4);
            border-top-color: #fff;
            animation: login-spin .7s linear infinite;
        }
    }
}

.theme-dark .login-jwt {
    .login-remember .con-slot-label {
        color: #c2c6dc;
    }

    .login-field label {
        color: #c2c6dc;
    }
    .login-field input {
        color: #fff;
        background: #10163a;
        border-color: #414561 !important;
    }
    .login-field__toggle:hover {
        background: #343a5c;
    }
}

@keyframes login-spin {
    to { transform: rotate(360deg); }
}

.login-alert {
    --alert-color: var(--vs-danger);
    display: flex;
    align-items: flex-start;
    gap: 10px;
    padding: 12px 12px 12px 14px;
    margin-bottom: 20px;
    border-radius: var(--input-radius, 10px);
    border: 1px solid rgba(var(--alert-color), .25);
    border-left: 3px solid rgb(var(--alert-color));
    background: rgba(var(--alert-color), .07);

    &--warning {
        --alert-color: var(--vs-warning);
    }

    &__icon {
        display: flex;
        flex-shrink: 0;
        margin-top: 2px;
        color: rgb(var(--alert-color));

        .feather-icon {
            display: flex;
        }
    }

    &__body {
        flex: 1;
        min-width: 0;
    }

    &__title {
        font-size: .875rem;
        font-weight: 600;
        color: #2c2e45;
        line-height: 1.5;
    }

    &__text {
        font-size: .8rem;
        color: #5e6078;
        line-height: 1.6;
        margin-top: 2px;
    }

    &__close {
        flex-shrink: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        width: 22px;
        height: 22px;
        padding: 0;
        border: none;
        border-radius: 6px;
        background: transparent;
        color: #8a8ca5;
        cursor: pointer;

        .feather-icon {
            display: flex;
        }

        &:hover {
            background: rgba(var(--alert-color), .12);
            color: rgb(var(--alert-color));
        }
    }
}

.login-alert-enter-active,
.login-alert-leave-active {
    transition: opacity .2s, transform .2s;
}
.login-alert-enter-from,
.login-alert-leave-to {
    opacity: 0;
    transform: translateY(-6px);
}

/* Shake the alert in when it appears so a repeated failure is still noticed */
.login-alert-enter-active {
    animation: login-shake .35s ease;
}

@keyframes login-shake {
    20%, 60% { transform: translateX(-4px); }
    40%, 80% { transform: translateX(4px); }
}

.theme-dark .login-alert {
    &__title { color: #fff; }
    &__text { color: #c2c6dc; }
}
</style>

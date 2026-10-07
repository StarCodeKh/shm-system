<template>
    <div class="user-profile">
        <!-- Header: who this is -->
        <div class="user-profile__head">
            <template v-if="profile">
                <span class="user-profile__avatar">
                    <img v-if="avatarUrl && !avatarFailed" :src="avatarUrl" alt="" @error="avatarFailed = true">
                    <span v-else>{{ initials }}</span>
                </span>
                <div class="user-profile__who">
                    <h2>{{ profile.fullname || profile.username }}</h2>
                    <p>
                        <span v-if="profile.latin">{{ profile.latin }}</span>
                        <span v-if="profile.email">· {{ profile.email }}</span>
                    </p>
                    <div class="user-profile__chips">
                        <span v-if="profile.role" class="dt-badge dt-badge--success">{{ profile.role }}</span>
                        <span v-if="entityLabel" class="user-profile__chip">
                            <feather-icon icon="BriefcaseIcon" svgClasses="h-3 w-3" /> {{ entityLabel }}
                        </span>
                        <span v-if="profile.username" class="user-profile__chip">
                            <feather-icon icon="AtSignIcon" svgClasses="h-3 w-3" /> {{ profile.username }}
                        </span>
                    </div>
                </div>
            </template>
            <template v-else-if="!userId">
                <span class="user-profile__avatar"><feather-icon icon="UserPlusIcon" svgClasses="h-7 w-7" /></span>
                <div class="user-profile__who">
                    <h2>{{ $t('profile_new_title') }}</h2>
                    <p>{{ $t('profile_new_hint') }}</p>
                </div>
            </template>
            <div v-else class="user-profile__head-skeleton"><span /><span /></div>
        </div>

        <!-- Tabs -->
        <div class="user-profile__tabs" role="tablist">
            <button v-for="t in tabs" :key="t.key" type="button" role="tab" class="user-profile__tab"
                :class="{ 'is-active': tab === t.key }" :aria-selected="tab === t.key" @click="tab = t.key">
                <feather-icon :icon="t.icon" svgClasses="h-4 w-4" />
                <span>{{ $t(t.label) }}</span>
            </button>
        </div>

        <div class="user-profile__body">
            <user-settings-general v-show="tab === 'general'" :profile="profile" :options="options" :loading="loading"
                :user-id="userId" @saved="load" />
            <user-settings-change-password v-if="userId" v-show="tab === 'password'" :profile="profile" :user-id="userId" />
        </div>
    </div>
</template>

<script>
import axios from "@/axios.js"
import apiConfig from "@/apiConfig.js"
import UserSettingsGeneral from "./UserSettingsGeneral.vue"
import UserSettingsChangePassword from "./UserSettingsChangePassword.vue"

export default {
    components: {
        UserSettingsGeneral,
        UserSettingsChangePassword,
    },
    data() {
        return {
            tab: this.tabFromRoute(),
            loading: false,
            profile: null,   // the user being viewed
            options: {},     // titles, roles, positions, departments, entities
            avatarFailed: false,
        }
    },
    computed: {
        userId() {
            return this.$route.params.userId || null
        },
        tabs() {
            const tabs = [{ key: 'general', icon: 'UserIcon', label: 'profile_tab_general' }]
            if (this.userId) tabs.push({ key: 'password', icon: 'LockIcon', label: 'profile_tab_password' })
            return tabs
        },
        avatarUrl() {
            return this.profile && (this.profile.avatar || this.profile.photo)
        },
        initials() {
            const name = (this.profile && (this.profile.latin || this.profile.fullname || this.profile.username)) || '?'
            return name.trim().split(/\s+/).slice(0, 2).map(part => part.charAt(0)).join('').toUpperCase()
        },
        entityLabel() {
            const entity = this.profile && this.profile.entity
            return entity && typeof entity === 'object' ? [entity.code, entity.name_kh].filter(Boolean).join(' - ') : ''
        },
    },
    watch: {
        userId() {
            this.tab = this.tabFromRoute()
            this.load()
        },
    },
    created() {
        this.load()
    },
    methods: {
        // ?tab=password opens the password tab (the lock button on the users list)
        tabFromRoute() {
            return this.$route.params.userId && this.$route.query.tab === 'password' ? 'password' : 'general'
        },
        // One request: the user plus every option list both tabs need
        load() {
            this.loading = true
            this.avatarFailed = false
            // New user: the same call still returns the option lists; the form stays blank
            axios.post(apiConfig._apiUserProfile, this.userId ? { user_id: this.userId } : {})
                .then(({ data }) => {
                    const payload = (data && data.data) || {}
                    this.profile = this.userId ? (payload.data || {}) : null
                    this.options = {
                        titles: payload.title || [],
                        roles: payload.role || [],
                        positions: payload.position || [],
                        departments: payload.departments || [],
                        entities: payload.entities || [],
                    }
                })
                .catch(() => {
                    this.$vs.notify({ title: 'Message', text: 'msg.action_failed', iconPack: 'feather', icon: 'icon-alert-circle', color: 'danger' })
                })
                .finally(() => { this.loading = false })
        },
    },
}
</script>

<style lang="scss">
.user-profile {
    &__head {
        display: flex;
        align-items: center;
        gap: 18px;
        padding: 22px 24px;
        border: 1px solid #eceef4;
        border-radius: 16px;
        background:
            linear-gradient(120deg, rgba(var(--vs-primary), .08), rgba(var(--vs-primary), 0) 55%),
            #fff;
        box-shadow: 0 4px 24px -10px rgba(23, 23, 74, .12);
    }

    &__avatar {
        flex-shrink: 0;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 76px;
        height: 76px;
        font-size: 1.5rem;
        font-weight: 700;
        color: rgb(var(--vs-primary));
        border: 3px solid #fff;
        border-radius: 999px;
        background: rgba(var(--vs-primary), .14);
        box-shadow: 0 8px 20px -10px rgba(var(--vs-primary), .9);
        overflow: hidden;

        img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
    }

    &__who {
        min-width: 0;

        h2 {
            font-size: 1.3rem;
            font-weight: 700;
            color: #2c2e45;
        }

        p {
            margin-top: 2px;
            font-size: .85rem;
            color: #8a8ca5;
        }
    }

    &__chips {
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
        margin-top: 8px;
    }

    &__chip {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 3px 10px;
        font-size: .76rem;
        font-weight: 600;
        color: #5f6170;
        border-radius: 999px;
        background: #f1f2f5;

        .feather-icon {
            display: flex;
        }
    }

    &__head-skeleton {
        display: grid;
        gap: 10px;
        width: 320px;
        max-width: 100%;

        span {
            height: 16px;
            border-radius: 6px;
            background: linear-gradient(90deg, #f1f2f5 25%, #e7e9ee 50%, #f1f2f5 75%);
            background-size: 200% 100%;
            animation: dt-shimmer 1.2s infinite;

            &:first-child {
                height: 22px;
                width: 60%;
            }
        }
    }

    &__tabs {
        display: flex;
        gap: 6px;
        margin: 18px 0 14px;
    }

    // The panel shown by v-show replays the shared tab entrance each time it appears
    &__body > * {
        animation: tab-content-in var(--tab-fade) var(--tab-ease) backwards;

        @media (prefers-reduced-motion: reduce) {
            animation: none;
        }
    }

    &__tab {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        height: var(--control-sm-height, 34px);
        padding: 0 14px;
        font-family: inherit;
        font-size: .86rem;
        font-weight: 600;
        color: #5f6170;
        border: 1px solid #e6e8ee;
        border-radius: 999px;
        background: #fff;
        cursor: pointer;
        transition: color var(--tab-tap) ease, background var(--tab-tap) ease, border-color var(--tab-tap) ease;

        .feather-icon {
            display: flex;
        }

        &:hover {
            border-color: rgba(var(--vs-primary), .45);
        }

        &:active {
            transform: scale(.96);
        }

        &.is-active {
            color: rgb(var(--vs-primary));
            border-color: rgba(var(--vs-primary), .45);
            background: rgba(var(--vs-primary), .1);
        }
    }

    // Shared by both tab forms
    .user-form {
        padding: 22px 24px;
        border: 1px solid #eceef4;
        border-radius: 16px;
        background: #fff;
        box-shadow: 0 4px 24px -10px rgba(23, 23, 74, .12);

        &__section {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 4px;
            font-size: .95rem;
            font-weight: 700;
            color: #2c2e45;

            .feather-icon {
                display: flex;
                color: rgb(var(--vs-primary));
            }
        }

        &__hint {
            font-size: .8rem;
            color: #8a8ca5;
        }

        &__actions {
            display: flex;
            justify-content: flex-end;
            gap: 8px;
            margin-top: 22px;
            padding-top: 16px;
            border-top: 1px solid #eef0f4;
        }
    }

    @media (max-width: 640px) {
        &__head {
            flex-direction: column;
            text-align: center;
        }

        &__chips {
            justify-content: center;
        }

        .user-form {
            padding: 16px;
        }
    }
}

.theme-dark .user-profile {
    &__head,
    .user-form,
    &__tab {
        background: #10163a;
        border-color: #414561;
    }

    &__who h2,
    .user-form__section {
        color: #fff;
    }
}
</style>

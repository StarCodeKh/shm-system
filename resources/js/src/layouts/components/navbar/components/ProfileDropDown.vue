<template>
    <vs-dropdown v-if="activeUserInfo.username" vs-custom-content vs-trigger-click class="cursor-pointer">

        <button type="button" class="nav-user">
            <span class="nav-user__avatar">
                <img v-if="avatar && !avatarFailed" :src="avatar" alt="" @error="avatarFailed = true" />
                <span v-else>{{ initials }}</span>
                <i class="nav-user__status"></i>
            </span>
            <span class="nav-user__meta hidden md:flex">
                <span class="nav-user__name">{{ displayName }}</span>
                <span v-if="activeUserInfo.email" class="nav-user__email">{{ activeUserInfo.email }}</span>
            </span>
            <feather-icon icon="ChevronDownIcon" svgClasses="h-4 w-4" class="nav-user__chevron hidden md:flex" />
        </button>

        <vs-dropdown-menu class="nav-user-menu vx-navbar-dropdown">
            <div class="nav-user-menu__header">
                <span class="nav-user__avatar nav-user__avatar--lg">
                    <img v-if="avatar && !avatarFailed" :src="avatar" alt="" @error="avatarFailed = true" />
                    <span v-else>{{ initials }}</span>
                </span>
                <div class="min-w-0">
                    <p class="nav-user-menu__name">{{ displayName }}</p>
                    <p v-if="activeUserInfo.email" class="nav-user-menu__email">{{ activeUserInfo.email }}</p>
                </div>
            </div>

            <div class="nav-user-menu__group">
                <button type="button" class="nav-user-menu__item"
                    @click="$router.push('/user/edit/' + activeUserInfo.id).catch(() => { })">
                    <feather-icon icon="UserIcon" svgClasses="w-4 h-4" />
                    <span>{{ $t('nav_profile') }}</span>
                </button>
                <button v-if="canOpenSettings()" type="button" class="nav-user-menu__item" @click="$router.push('/setting').catch(() => { })">
                    <feather-icon icon="SettingsIcon" svgClasses="w-4 h-4" />
                    <span>{{ $t('nav_settings') }}</span>
                </button>
            </div>

            <div class="nav-user-menu__group">
                <button type="button" class="nav-user-menu__item nav-user-menu__item--danger" @click="logout">
                    <feather-icon icon="LogOutIcon" svgClasses="w-4 h-4" />
                    <span>{{ $t('nav_logout') }}</span>
                </button>
            </div>
        </vs-dropdown-menu>
    </vs-dropdown>
</template>

<script>
import { setAuthHeader } from '@/axios.js'
import { canOpenSettings } from '@/views/pages/settings/settingsAccess.js'

export default {
    computed: {
        activeUserInfo() {
            return this.$store.state.AppActiveUser
        },
        displayName() {
            return this.$store.getters.activeUserName(this.$i18n.locale)
        },
        avatar() {
            return this.$store.getters.activeUserAvatar
        },
        initials() {
            return this.$store.getters.activeUserInitials
        },
    },
    data: () => ({ avatarFailed: false }),
    watch: {
        // A new photo gets another chance to load
        avatar() { this.avatarFailed = false },
    },
    methods: {
        canOpenSettings,
        logout() {
            localStorage.removeItem("accessToken")
            localStorage.removeItem('userInfo')
            setAuthHeader(null)

            // Change role on logout. Same value as initialRole of acl.js
            this.$acl.change('admin')

            this.$router.push('/pages/login').catch(() => { })
        },
    }
}
</script>

<style lang="scss">
.nav-user {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 4px 8px 4px 4px;
    font-family: inherit;
    text-align: left;
    border: none;
    border-radius: 12px;
    background: transparent;
    cursor: pointer;
    transition: background .15s;

    &:hover {
        background: #f4f5f9;
    }

    &__avatar {
        position: relative;
        flex-shrink: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        width: 38px;
        height: 38px;
        border-radius: 50%;
        font-size: .85rem;
        font-weight: 700;
        color: #fff;
        background: linear-gradient(135deg, rgb(var(--vs-primary)), #26266b);

        img {
            width: 100%;
            height: 100%;
            border-radius: 50%;
            object-fit: cover;
        }

        &--lg {
            width: 44px;
            height: 44px;
            font-size: .95rem;
        }
    }

    &__status {
        position: absolute;
        right: 0;
        bottom: 0;
        width: 11px;
        height: 11px;
        border-radius: 50%;
        background: #28c76f;
        box-shadow: 0 0 0 2px #fff;
    }

    &__meta {
        flex-direction: column;
        min-width: 0;
        max-width: 180px;
        line-height: 1.3;
    }

    &__name {
        font-size: .875rem;
        font-weight: 600;
        color: #2c2e45;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    &__email {
        font-size: .75rem;
        color: #8a8ca5;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    &__chevron {
        color: #a3a5bb;
    }
}

.nav-user-menu {
    .vs-dropdown--custom {
        padding: 0;
        width: 260px;
        overflow: hidden;
    }

    &__header {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 16px;
        background: #f8f9fb;
        border-bottom: 1px solid #eef0f5;
    }

    &__name {
        font-weight: 600;
        color: #2c2e45;
    }

    &__email {
        font-size: .78rem;
        color: #8a8ca5;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    &__group {
        padding: 6px;

        & + & {
            border-top: 1px solid #eef0f5;
        }
    }

    &__item {
        display: flex;
        align-items: center;
        gap: 10px;
        width: 100%;
        padding: 9px 10px;
        font-family: inherit;
        font-size: .875rem;
        color: #4b4d68;
        text-align: left;
        border: none;
        border-radius: 8px;
        background: transparent;
        cursor: pointer;
        transition: background .15s, color .15s;

        .feather-icon {
            display: flex;
        }

        &:hover {
            color: rgb(var(--vs-primary));
            background: rgba(var(--vs-primary), .08);
        }

        &--danger:hover {
            color: rgb(var(--vs-danger));
            background: rgba(var(--vs-danger), .08);
        }
    }
}

.theme-dark {
    .nav-user:hover {
        background: #262c49;
    }
    .nav-user__name,
    .nav-user-menu__name {
        color: #fff;
    }
    .nav-user__status {
        box-shadow: 0 0 0 2px #10163a;
    }
    .nav-user-menu__header {
        background: #262c49;
        border-color: #414561;
    }
    .nav-user-menu__group + .nav-user-menu__group {
        border-color: #414561;
    }
    .nav-user-menu__item {
        color: #c2c6dc;
    }
}
</style>

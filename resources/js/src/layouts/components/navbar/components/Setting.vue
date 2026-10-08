<template>
    <!-- Shown only when the role may open at least one section of Settings -->
    <button v-if="canOpenSettings()" type="button" class="nav-icon-btn" :class="{ 'is-active': $route.path === '/setting' }"
        :title="$t('nav_settings')" @click="$router.push('/setting').catch(() => {})">
        <feather-icon icon="SettingsIcon" svgClasses="h-5 w-5" />
    </button>
</template>

<script>
import { canOpenSettings } from '@/views/pages/settings/settingsAccess.js'

export default {
    name: 'navbar-setting',
    methods: { canOpenSettings },
}
</script>

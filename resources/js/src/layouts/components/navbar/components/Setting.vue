<template>
  <button type="button" class="nav-icon-btn" :class="{ 'is-active': $route.path === '/setting' }"
    :title="$t('nav_settings')" @click="$router.push('/setting').catch(() => {})">
    <feather-icon icon="SettingsIcon" svgClasses="h-5 w-5" />
  </button>
</template>

<script>
export default {
  name: 'navbar-setting'
}
</script>

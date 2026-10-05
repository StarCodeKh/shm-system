<template>
  <div class="nav-lang-switch" role="group" :aria-label="$t('nav_language')">
    <button v-for="lang in languages" :key="lang.code" type="button"
      :class="{ active: lang.code === $i18n.locale }" :aria-pressed="lang.code === $i18n.locale"
      :title="lang.name" @click="updateLocale(lang.code)">
      <img :src="lang.flag" :alt="lang.code" />
      <span>{{ lang.label }}</span>
    </button>
  </div>
</template>

<script>
import { setLocale } from '@/i18n/i18n'

const LANGUAGES = [
  { code: 'kh', label: 'ខ្មែរ', name: 'ភាសាខ្មែរ', flag: require('@assets/images/flags/kh.jpg') },
  { code: 'en', label: 'EN', name: 'English', flag: require('@assets/images/flags/en.png') },
]

export default {
  data() {
    return {
      languages: LANGUAGES
    }
  },
  methods: {
    updateLocale(locale) {
      if (locale !== this.$i18n.locale) setLocale(this.$i18n, locale)
    },
  }
}
</script>

<style lang="scss">
// Same segmented style as the Dashboard currency switch
.nav-lang-switch {
  display: inline-flex;
  padding: 3px;
  margin-right: 6px;
  border-radius: 999px;
  background: #eef0f4;

  button {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: .35rem .8rem;
    font-family: inherit;
    font-size: .875rem;
    color: #626262;
    border: 0;
    border-radius: 999px;
    background: transparent;
    cursor: pointer;
    transition: all .2s ease;

    img {
      width: 18px;
      height: 13px;
      border-radius: 2px;
      object-fit: cover;
      box-shadow: 0 0 0 1px rgba(0, 0, 0, .08);
      opacity: .6;
      transition: opacity .2s ease;
    }

    &:hover:not(.active) {
      color: #2c2e45;
    }

    &.active {
      background: #fff;
      color: rgba(var(--vs-primary), 1);
      font-weight: 600;
      box-shadow: 0 2px 6px rgba(0, 0, 0, .1);

      img {
        opacity: 1;
      }
    }
  }

  // Flags only on small screens
  @media (max-width: 640px) {
    button {
      padding: .4rem .6rem;

      span {
        display: none;
      }
    }
  }
}

.theme-dark .nav-lang-switch {
  background: #10163a;

  button {
    color: #c2c6dc;

    &.active {
      background: #262c49;
    }
  }
}
</style>

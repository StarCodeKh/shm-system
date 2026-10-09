<template>
    <!-- Name, organisation and website come from Settings › General (site.js); translations fill any gap -->
    <footer class="the-footer app-footer" :class="classes">
        <p class="app-footer__copy">
            <span>&copy; {{ year }}</span>
            <a v-if="site.website" :href="site.website" target="_blank" rel="noopener">{{ name }}</a>
            <strong v-else>{{ name }}</strong>
            <span class="app-footer__dot" aria-hidden="true">·</span>
            <span>{{ $t('footer_rights') }}</span>
        </p>
        <p class="app-footer__owner">
            <feather-icon icon="BriefcaseIcon" svgClasses="h-4 w-4" />
            <span>{{ owner }}</span>
        </p>
    </footer>
</template>

<script>
import { site, loadSite } from "@/site.js"

export default {
    name: "the-footer",
    props: {
        classes: {
            type: String,
        },
    },
    data() {
        return { site, year: new Date().getFullYear() }
    },
    computed: {
        name() {
            return this.site.name || this.$t('brand_name_fallback')
        },
        owner() {
            return this.site.owner || this.$t('footer_ministry')
        },
    },
    created() {
        loadSite()
    },
}
</script>

<style>
.app-footer {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    justify-content: space-between;
    gap: 6px 24px;
    margin-top: 8px;
    border-top: 1px solid rgba(23, 23, 74, .06);
    font-size: .85rem;
    color: #8a8ca5;

    p {
        display: inline-flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 6px;
        margin: 0;
        min-width: 0;
    }

    a,
    strong {
        font-weight: 600;
        color: rgb(var(--vs-primary));
    }

    a {
        text-decoration: none;
        text-underline-offset: 3px;

        &:hover {
            text-decoration: underline;
        }
    }

    &__dot {
        color: #c4c6d0;
    }

    &__owner {
        color: #6b6d7a;

        .feather-icon {
            display: flex;
            color: #b0b2bd;
        }
    }

    /* Phones: one centred column */
    @media (max-width: 640px) {
        flex-direction: column;
        justify-content: center;
        text-align: center;

        p {
            justify-content: center;
        }
    }
}

.theme-dark .app-footer {
    border-color: #414561;
    color: #a8abc4;

    .app-footer__owner {
        color: #c2c6dc;
    }
}
</style>

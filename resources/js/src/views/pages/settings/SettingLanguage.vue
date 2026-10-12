<template>
    <vx-card class="lang-settings">
        <!-- Header: what this is, and a live preview of the switch the users will see -->
        <div class="lang-settings__head">
            <div class="min-w-0">
                <h4>{{ $t('setting_tab_language') }}</h4>
                <p>{{ $t('lang_default_hint') }}</p>
            </div>
            <button v-if="canCreate" type="button" class="lang-btn lang-btn--primary" @click="openNew">
                <feather-icon icon="PlusIcon" svgClasses="h-4 w-4" />
                <span>{{ $t('lang_add') }}</span>
            </button>
        </div>

        <div class="lang-preview">
            <span class="lang-preview__label">
                <feather-icon icon="EyeIcon" svgClasses="h-4 w-4" />
                {{ $t('lang_preview_hint') }}
            </span>
            <i18n />
        </div>

        <!-- One card per language, in switch order; cards glide to their new place when reordered -->
        <transition-group v-if="rows.length" tag="div" name="lang-card" class="lang-grid">
            <article v-for="(row, i) in rows" :key="row.id" class="lang-card"
                :class="{ 'is-off': !isActive(row), 'is-default': row.id === defaultId, 'is-current': code(row) === $i18n.locale }"
                :style="{ '--delay': (i * 50) + 'ms' }">

                <span v-if="row.id === defaultId" class="lang-card__ribbon">
                    <feather-icon icon="StarIcon" svgClasses="h-3 w-3" />
                    {{ $t('lang_default') }}
                </span>

                <div class="lang-card__top">
                    <span class="lang-card__flag">
                        <img v-if="flagOf(row)" :src="flagOf(row)" :alt="code(row)" @error="row._noFlag = true" />
                        <b v-else>{{ code(row) }}</b>
                    </span>
                    <div class="min-w-0">
                        <h5 :title="row.name">{{ row.name }}</h5>
                        <div class="lang-card__chips">
                            <code>{{ code(row) }}</code>
                            <span v-if="code(row) === $i18n.locale" class="lang-chip lang-chip--current">
                                <i class="lang-chip__dot" /> {{ $t('lang_in_use') }}
                            </span>
                            <span v-else-if="!hasTexts(row)" class="lang-chip lang-chip--warn" :title="$t('lang_no_texts_hint')">
                                {{ $t('lang_no_texts') }}
                            </span>
                        </div>
                    </div>
                </div>

                <div class="lang-card__bar">
                    <label class="lang-toggle" :class="{ 'is-busy': busy[row.id] }" :title="$t(isActive(row) ? 'lang_active' : 'lang_inactive')">
                        <input type="checkbox" :checked="isActive(row)" :disabled="!canUpdate || busy[row.id]"
                            @change="toggle(row, $event)" />
                        <span class="lang-toggle__track"><span class="lang-toggle__thumb" /></span>
                        <span class="lang-toggle__text">{{ $t(isActive(row) ? 'lang_active' : 'lang_inactive') }}</span>
                    </label>

                    <div class="lang-card__actions">
                        <button v-if="isActive(row) && code(row) !== $i18n.locale" type="button" class="lang-icon" :title="$t('lang_use')" @click="use(row)">
                            <feather-icon icon="CheckIcon" svgClasses="h-4 w-4" />
                        </button>
                        <template v-if="canUpdate">
                            <button type="button" class="lang-icon" :title="$t('lang_move_up')" :disabled="i === 0 || reordering" @click="move(i, -1)">
                                <feather-icon icon="ArrowUpIcon" svgClasses="h-4 w-4" />
                            </button>
                            <button type="button" class="lang-icon" :title="$t('lang_move_down')" :disabled="i === rows.length - 1 || reordering" @click="move(i, 1)">
                                <feather-icon icon="ArrowDownIcon" svgClasses="h-4 w-4" />
                            </button>
                            <button type="button" class="lang-icon" :title="$t('dt_edit')" @click="edit(row)">
                                <feather-icon icon="Edit2Icon" svgClasses="h-4 w-4" />
                            </button>
                        </template>
                        <button v-if="canDelete" type="button" class="lang-icon lang-icon--danger" :title="$t('dt_delete')" @click="confirmDelete(row)">
                            <feather-icon icon="Trash2Icon" svgClasses="h-4 w-4" />
                        </button>
                    </div>
                </div>
            </article>
        </transition-group>

        <div v-else-if="loaded" class="lang-empty">
            <feather-icon icon="GlobeIcon" svgClasses="h-8 w-8" />
            <p>{{ $t('dt_empty') }}</p>
        </div>

        <!-- The standard form, built from the table's columns (app/Services/FormSchema.php) -->
        <d-modal-form ref="form" :api="api" title="setting_language" :data="response" :parentDataInfo="{}"
            :formAttributes="formAttributes" :dataAttributes="{}" @clicked="changed" />
    </vx-card>
</template>

<script>
import axios from "@/axios.js"
import apiConfig from "@/apiConfig.js"
import { can } from "@/acl/access.js"
import { setLocale } from "@/i18n/i18n"
import { describe, loadLanguages } from "@/i18n/languages"
import DModalForm from "@/views/form-builder/DModalForm.vue"
import I18n from "@/layouts/components/navbar/components/I18n.vue"

// Record status ids (Status::ACTIVE / INACTIVE, app/Models/Settings/Status.php)
const ACTIVE = 13
const INACTIVE = 14

// Settings › Language: the languages of the switch in the top bar and on the sign-in page.
// Order here is the order of the switch; the first active language is the default.
export default {
    components: { DModalForm, I18n },
    data() {
        return {
            api: apiConfig._apiLanguage,
            rows: [],
            response: {}, // last list answer: the form reads its choice lists from it, as in DTableList
            formAttributes: [],
            loaded: false,
            busy: {}, // row id -> saving its toggle
            reordering: false,
        }
    },
    computed: {
        canCreate: () => can("create_language"),
        canUpdate: () => can("update_language"),
        canDelete: () => can("delete_language"),
        defaultId() {
            return (this.rows.find(this.isActive) || {}).id
        },
    },
    methods: {
        code: row => String(row.code || "").toLowerCase(),
        isActive: row => Number(row.status) === ACTIVE,
        flagOf(row) {
            return row._noFlag ? null : describe({ code: this.code(row), name: row.name, image: row.image }).flag
        },
        // Has the app its own texts in this language? Without them it shows in English
        hasTexts(row) {
            return this.$i18n.availableLocales.includes(this.code(row))
        },
        load() {
            return axios.post(this.api + "/search", { sort: "order_level", order: "asc", limit: "all" })
                .then(({ data }) => {
                    this.response = data
                    this.rows = data.data || []
                    if (data.form_attributes) this.formAttributes = data.form_attributes
                })
                .catch(this.failed)
                .finally(() => { this.loaded = true })
        },
        // Something was saved: reload the cards, and the switch everywhere
        changed() {
            this.load()
            loadLanguages(true)
        },
        notify(text, color = "primary") {
            this.$vs.notify({ title: "Message", text, color })
        },
        failed(error) {
            const data = error && error.response && error.response.data
            this.notify((data && data.message) || "msg.action_failed", "danger")
        },
        openNew() {
            this.$refs.form.openNewForm({})
        },
        edit(row) {
            this.$refs.form.initForm({ ...row })
        },
        use(row) {
            setLocale(this.$i18n, this.code(row))
        },
        toggle(row, event) {
            const status = event.target.checked ? ACTIVE : INACTIVE
            const before = row.status
            row.status = status // the switch moves at once; put back if the server says no
            this.busy = { ...this.busy, [row.id]: true }
            axios.put(this.api + "/" + row.id, { status })
                .then(({ data }) => {
                    this.notify(data.message)
                    loadLanguages(true)
                })
                .catch(error => {
                    row.status = before
                    event.target.checked = this.isActive(row)
                    this.failed(error)
                })
                .finally(() => { this.busy = { ...this.busy, [row.id]: false } })
        },
        // Swap with the neighbour, then number every card 1, 2, 3 … so the order is always clear
        move(index, step) {
            const rows = [...this.rows]
            const [row] = rows.splice(index, 1)
            rows.splice(index + step, 0, row)
            this.rows = rows
            const changes = rows
                .map((r, i) => ({ row: r, order: i + 1 }))
                .filter(({ row: r, order }) => Number(r.order_level) !== order)
            changes.forEach(({ row: r, order }) => { r.order_level = order })
            this.reordering = true
            Promise.all(changes.map(({ row: r, order }) => axios.put(this.api + "/" + r.id, { order_level: order })))
                .then(() => loadLanguages(true))
                .catch(error => {
                    this.failed(error)
                    this.load()
                })
                .finally(() => { this.reordering = false })
        },
        confirmDelete(row) {
            this.$vs.dialog({
                type: "confirm",
                color: "danger",
                title: this.$t("dt_delete_title"),
                text: this.$t("dt_delete_text_named", { name: row.name }),
                acceptText: this.$t("dt_delete"),
                cancelText: this.$t("dt_cancel"),
                accept: () => axios.delete(this.api + "/" + row.id)
                    .then(({ data }) => {
                        this.notify(data.message)
                        this.changed()
                    })
                    .catch(this.failed),
            })
        },
    },
    created() {
        this.load()
    },
}
</script>

<style>
.lang-settings {
    --lang-accent: var(--tile, 111, 66, 193);

    &__head {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 1rem;
        margin-bottom: 1rem;

        h4 {
            margin: 0 0 .25rem;
            font-weight: 600;
        }

        p {
            margin: 0;
            font-size: .875rem;
            color: #8a8d9f;
        }
    }
}

.lang-btn {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    flex-shrink: 0;
    padding: .55rem 1rem;
    font-family: inherit;
    font-size: .875rem;
    font-weight: 600;
    border: 0;
    border-radius: 10px;
    cursor: pointer;
    transition: transform .15s ease, box-shadow .2s ease;

    &--primary {
        color: #fff;
        background: rgba(var(--vs-primary), 1);
        box-shadow: 0 4px 12px rgba(var(--vs-primary), .3);
    }

    &:hover {
        transform: translateY(-1px);
        box-shadow: 0 6px 16px rgba(var(--vs-primary), .35);
    }

    &:active {
        transform: translateY(0) scale(.97);
    }
}

/* Live preview of the switch, on a soft dotted surface */
.lang-preview {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    justify-content: space-between;
    gap: .75rem;
    padding: .9rem 1rem;
    margin-bottom: 1.25rem;
    border: 1px dashed rgba(var(--lang-accent), .35);
    border-radius: 14px;
    background:
        radial-gradient(rgba(var(--lang-accent), .12) 1px, transparent 1px) 0 0 / 14px 14px,
        rgba(var(--lang-accent), .04);

    &__label {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: .8125rem;
        color: #6e7187;
    }

    .nav-lang-switch {
        margin-right: 0;
    }
}

.lang-grid {
    position: relative;
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
    gap: 1rem;
}

.lang-card {
    position: relative;
    display: flex;
    flex-direction: column;
    gap: 1rem;
    padding: 1.1rem;
    border: 1px solid #ebedf2;
    border-radius: 16px;
    background: #fff;
    box-shadow: 0 1px 2px rgba(0, 0, 0, .04);
    transition: transform .25s ease, box-shadow .25s ease, border-color .25s ease, opacity .25s ease, filter .25s ease;
    animation: lang-card-in .45s cubic-bezier(.22, 1, .36, 1) both;
    animation-delay: var(--delay, 0ms);

    &:hover {
        transform: translateY(-3px);
        border-color: rgba(var(--lang-accent), .35);
        box-shadow: 0 10px 24px rgba(30, 34, 70, .1);

        .lang-card__flag {
            transform: rotate(-4deg) scale(1.05);
        }
    }

    /* Default language: a soft glowing ring */
    &.is-default {
        border-color: transparent;
        background:
            linear-gradient(#fff, #fff) padding-box,
            linear-gradient(135deg, rgba(var(--vs-primary), .9), rgba(var(--lang-accent), .6), rgba(var(--vs-warning, 255, 159, 67), .8)) border-box;
    }

    &.is-off {
        opacity: .6;
        filter: grayscale(.7);

        &:hover {
            opacity: .85;
            filter: none;
        }
    }

    &__ribbon {
        position: absolute;
        top: -9px;
        right: 14px;
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 2px 9px;
        font-size: .6875rem;
        font-weight: 700;
        letter-spacing: .02em;
        color: #fff;
        border-radius: 999px;
        background: linear-gradient(135deg, rgba(var(--vs-primary), 1), rgba(var(--lang-accent), 1));
        box-shadow: 0 3px 8px rgba(var(--vs-primary), .35);
    }

    &__top {
        display: flex;
        align-items: center;
        gap: .9rem;

        h5 {
            margin: 0 0 .35rem;
            font-size: 1.05rem;
            font-weight: 600;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
    }

    &__flag {
        display: grid;
        place-items: center;
        flex-shrink: 0;
        width: 58px;
        height: 42px;
        overflow: hidden;
        border-radius: 8px;
        box-shadow: 0 0 0 1px rgba(0, 0, 0, .06), 0 4px 10px rgba(0, 0, 0, .1);
        background: linear-gradient(135deg, rgba(var(--vs-primary), 1), rgba(var(--lang-accent), 1));
        transition: transform .35s cubic-bezier(.34, 1.56, .64, 1);

        img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        b {
            font-size: 1rem;
            letter-spacing: .05em;
            text-transform: uppercase;
            color: #fff;
        }
    }

    &__chips {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 6px;

        code {
            padding: 1px 7px;
            font-size: .75rem;
            font-weight: 600;
            text-transform: uppercase;
            color: rgba(var(--lang-accent), 1);
            border-radius: 6px;
            background: rgba(var(--lang-accent), .1);
        }
    }

    &__bar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: .5rem;
        padding-top: .8rem;
        margin-top: auto;
        border-top: 1px solid #f0f1f5;
    }

    &__actions {
        display: flex;
        gap: 2px;
    }
}

.lang-chip {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 1px 8px;
    font-size: .6875rem;
    font-weight: 600;
    border-radius: 999px;

    &--current {
        color: rgba(var(--vs-success), 1);
        background: rgba(var(--vs-success), .12);
    }

    &--warn {
        color: #b7791f;
        background: rgba(255, 159, 67, .15);
        cursor: help;
    }

    /* Pulsing "live" dot on the language in use */
    &__dot {
        width: 6px;
        height: 6px;
        border-radius: 50%;
        background: currentColor;
        animation: lang-pulse 1.6s ease-out infinite;
    }
}

.lang-icon {
    display: grid;
    place-items: center;
    width: 30px;
    height: 30px;
    color: #8a8d9f;
    border: 0;
    border-radius: 8px;
    background: transparent;
    cursor: pointer;
    transition: color .15s ease, background .15s ease, transform .15s ease;

    &:hover:not(:disabled) {
        color: rgba(var(--vs-primary), 1);
        background: rgba(var(--vs-primary), .1);
    }

    &:active:not(:disabled) {
        transform: scale(.88);
    }

    &:disabled {
        opacity: .3;
        cursor: default;
    }

    &--danger:hover:not(:disabled) {
        color: rgba(var(--vs-danger), 1);
        background: rgba(var(--vs-danger), .1);
    }
}

/* On / off switch */
.lang-toggle {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    font-size: .8125rem;
    font-weight: 500;
    color: #6e7187;
    cursor: pointer;
    user-select: none;

    input {
        position: absolute;
        opacity: 0;
        pointer-events: none;
    }

    &__track {
        position: relative;
        width: 36px;
        height: 20px;
        border-radius: 999px;
        background: #d8dbe5;
        transition: background .25s ease;
    }

    &__thumb {
        position: absolute;
        top: 2px;
        left: 2px;
        width: 16px;
        height: 16px;
        border-radius: 50%;
        background: #fff;
        box-shadow: 0 1px 3px rgba(0, 0, 0, .2);
        transition: transform .3s cubic-bezier(.34, 1.56, .64, 1);
    }

    input:checked + &__track {
        background: rgba(var(--vs-success), 1);

        .lang-toggle__thumb {
            transform: translateX(16px);
        }
    }

    input:focus-visible + &__track {
        box-shadow: 0 0 0 3px rgba(var(--vs-primary), .3);
    }

    input:disabled + &__track {
        cursor: not-allowed;
    }

    &.is-busy &__thumb {
        animation: lang-busy .8s ease-in-out infinite alternate;
    }
}

.lang-empty {
    display: grid;
    place-items: center;
    gap: .5rem;
    padding: 3rem 1rem;
    color: #a0a3b5;
}

/* Reorder: cards glide to their new place */
.lang-card-move {
    transition: transform .45s cubic-bezier(.22, 1, .36, 1);
}

.lang-card-enter-from,
.lang-card-leave-to {
    opacity: 0;
    transform: scale(.94);
}

.lang-card-leave-active {
    position: absolute;
    transition: opacity .25s ease, transform .25s ease;
}

@keyframes lang-card-in {
    from {
        opacity: 0;
        transform: translateY(10px) scale(.98);
    }
}

@keyframes lang-pulse {
    0% {
        box-shadow: 0 0 0 0 currentColor;
    }

    100% {
        box-shadow: 0 0 0 6px transparent;
    }
}

@keyframes lang-busy {
    to {
        opacity: .5;
    }
}

/* Dark theme */
.theme-dark {
    .lang-card {
        border-color: #414561;
        background: #262c49;

        &.is-default {
            background:
                linear-gradient(#262c49, #262c49) padding-box,
                linear-gradient(135deg, rgba(var(--vs-primary), .9), rgba(var(--lang-accent), .6), rgba(255, 159, 67, .8)) border-box;
        }

        &__bar {
            border-top-color: #414561;
        }
    }

    .lang-preview__label,
    .lang-toggle,
    .lang-settings__head p {
        color: #b0b4cc;
    }

    .lang-toggle__track {
        background: #414561;
    }
}

@media (max-width: 640px) {
    .lang-settings__head {
        flex-direction: column;
    }
}

@media (prefers-reduced-motion: reduce) {
    .lang-card,
    .lang-card-move,
    .lang-card__flag,
    .lang-chip__dot,
    .lang-toggle__thumb {
        transition: none;
        animation: none;
    }
}
</style>

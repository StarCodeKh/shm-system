<template>
    <div class="vx-row">
        <!-- COL 1 -->
        <div class="vx-col w-full lg:w-1/4">
            <vx-card :title="$t('officer_detail')" class="mt-base">
                <!-- ACTION SLOT -->
                <template #actions>
                    <feather-icon icon="MoreHorizontalIcon"></feather-icon>
                </template>

                <!-- Profile: photo (or initials), name, member no. and status, centered -->
                <div v-if="dataInfo" class="profile-card">
                    <div class="profile-card__head">
                        <div class="profile-card__avatar">
                            <img v-if="photo" :src="photo" :alt="fullName" @error="photoFailed = true" />
                            <span v-else>{{ initials }}</span>
                            <i v-if="status" class="profile-card__dot" :class="{ 'is-off': !isActive }" :title="status"></i>
                        </div>
                        <h4 class="profile-card__name">{{ fullName || '—' }}</h4>
                        <p v-if="dataInfo.latin" class="profile-card__latin">{{ dataInfo.latin }}</p>
                        <div class="profile-card__tags">
                            <span v-if="dataInfo.identity_member_no" class="profile-card__tag" :title="$t('identity_member_no')">
                                <feather-icon icon="HashIcon" svgClasses="h-3 w-3" />{{ dataInfo.identity_member_no }}
                            </span>
                            <span v-if="status" class="profile-card__tag" :class="isActive ? 'is-on' : 'is-off'">{{ status }}</span>
                        </div>
                    </div>

                    <ul class="profile-card__list">
                        <li v-for="item in details" :key="item.label">
                            <span class="profile-card__icon"><feather-icon :icon="item.icon" svgClasses="h-4 w-4" /></span>
                            <div class="min-w-0">
                                <small>{{ item.label }}</small>
                                <a v-if="item.href && item.value" :href="item.href">{{ item.value }}</a>
                                <span v-else>{{ item.value || '—' }}</span>
                            </div>
                        </li>
                    </ul>
                </div>
            </vx-card>
        </div>

        <div class="vx-col w-full lg:w-3/4">
            <vs-tabs :position="'top'" class="tabs-shadow-none" id="profile-tabs" :key="isSmallerScreen">

                <vs-tab icon-pack="feather" icon="icon-info" :label="$t('general')">
                    <div class="tab-text md:ml-4 md:mt-0 mt-4 ml-0 pt-2">
                        <tab-detail @saved="getDataInfo" />
                    </div>
                </vs-tab>

                <vs-tab icon-pack="feather" icon="icon-paperclip" :label="$t('officer_document')">
                    <div class="tab-text md:ml-4 md:mt-0 mt-4 ml-0 pt-2">
                        <tab-document />
                    </div>
                </vs-tab>

                <vs-tab icon-pack="feather" icon="icon-dollar-sign" :label="$t('repay')">
                    <div class="tab-text md:ml-4 md:mt-0 mt-4 ml-0 pt-2">
                        <tab-payable />
                    </div>
                </vs-tab>

                <vs-tab icon-pack="feather" icon="icon-clock" :label="$t('payment_history')">
                    <div class="tab-text md:ml-4 md:mt-0 mt-4 ml-0 pt-2">
                        <tab-payment-history />
                    </div>
                </vs-tab>

                <vs-tab icon-pack="feather" icon="icon-file" :label="$t('request')">
                    <div class="tab-text md:ml-4 md:mt-0 mt-4 ml-0 pt-2">
                        <tab-request />
                    </div>
                </vs-tab>

                <vs-tab icon-pack="feather" icon="icon-dollar-sign" :label="$t('incentive')">
                    <div class="tab-text md:ml-4 md:mt-0 mt-4 ml-0 pt-2">
                        <tab-incentive />
                    </div>
                </vs-tab>

                    <vs-tab icon-pack="feather" icon="icon-lock" :label="$t('credentail_user_access')">
                        <div class="tab-text md:ml-4 md:mt-0 mt-4 ml-0 pt-2">
                            <tab-credential-user />
                        </div>
                </vs-tab>

                <vs-tab icon-pack="feather" icon="icon-calendar" :label="$t('recurring_schedule')">
                    <div class="tab-text md:ml-4 md:mt-0 mt-4 ml-0 pt-2">
                        <tab-recurring />
                    </div>
                </vs-tab>

            </vs-tabs>
        </div>
    </div>
</template>

<script>
import axios from "@/axios.js";
import apiConfig from "@/apiConfig.js";
import TabCredentialUser from "./_TabCredentialUser.vue";
import TabDetail from "./_TabDetail.vue";
import TabDocument from "./_TabDocument.vue";
import TabIncentive from "./_TabIncentive.vue";
import TabPaymentHistory from "./_TabPaymentHistory.vue";
import TabPayable from "./_TabPayable.vue";
import TabRequest from "./_TabRequest.vue";
import TabRecurring from "./_TabRecurring.vue";

export default {
    components: {
        TabCredentialUser,
        TabDetail,
        TabDocument,
        TabIncentive,
        TabPaymentHistory,
        TabPayable,
        TabRequest,
        TabRecurring
    },
    data() {
        return {
            api: apiConfig._apiOfficer,
            parentDataInfo:{},
            data: {
                data: [
                ],
                limit: 10,
                total: 3,
            },
            dataInfo: null,
            photoFailed: false, // the photo couldn't load: show initials instead
        }
    },
    methods: {
        getDataInfo(){
            let _id = this.$route.params.id;
            return new Promise((resolve, reject) => {
                axios.get(this.api + "/" +_id)
                    .then((response) => {
                    this.data = response.data;
                    this.dataInfo = response.data.data.data;
                    this.photoFailed = false;
                    resolve(this.dataInfo);
                }).catch((error) => {
                    reject(error)
                }).finally(() => {
                    this.$vs.loading.close();
                })
            })
        },
        // A related record's name (entity, status) in the current language
        localName(record, field = 'name') {
            if (!record) return ''
            const kh = this.$i18n.locale === 'kh'
            const [first, second] = kh ? [record[field + '_kh'], record[field + '_en']] : [record[field + '_en'], record[field + '_kh']]
            return first || second || ''
        },
    },
    created() {
        this.$vs.loading();
        this.getDataInfo();
    },
    computed: {
        photo() {
            const photo = this.dataInfo && this.dataInfo.photo
            return photo && !this.photoFailed ? photo : ''
        },
        fullName() {
            const d = this.dataInfo || {}
            return d.full_name || [d.first_name, d.last_name].filter(Boolean).join(' ')
        },
        // "SD" from the Latin name, else the first letters of the names
        initials() {
            const d = this.dataInfo || {}
            const words = d.latin ? d.latin.split(/\s+/) : [d.last_name, d.first_name]
            return words.filter(Boolean).slice(0, 2).map(w => Array.from(w)[0]).join('').toUpperCase() || '?'
        },
        status() {
            return this.localName(this.dataInfo && this.dataInfo.status, 'title')
        },
        isActive() {
            const status = this.dataInfo && this.dataInfo.status
            return !!status && status.title_en === 'Active'
        },
        details() {
            const d = this.dataInfo || {}
            return [
                { icon: 'BriefcaseIcon', label: this.$t('officer_entity'), value: this.localName(d.entity) },
                { icon: 'AwardIcon', label: this.$t('officer_position'), value: d.position },
                { icon: 'PhoneIcon', label: this.$t('officer_telephone_1'), value: d.telephone_1, href: d.telephone_1 && 'tel:' + String(d.telephone_1).replace(/[^\d+]/g, '') },
                { icon: 'MailIcon', label: this.$t('officer_email'), value: d.email, href: d.email && 'mailto:' + d.email },
                { icon: 'MapPinIcon', label: this.$t('officer_address'), value: d.address },
            ]
        },
        isSmallerScreen() {
            return this.$store.state.windowWidth < 768
        }
    }
}
</script>

<style>
.profile-card {
    &__head {
        display: flex;
        flex-direction: column;
        align-items: center;
        padding: 8px 0 20px;
        text-align: center;
        border-bottom: 1px solid #eef0f4;
    }

    &__avatar {
        position: relative;
        display: flex;
        align-items: center;
        justify-content: center;
        width: 112px;
        height: 112px;
        margin-bottom: 14px;
        font-size: 2.1rem;
        font-weight: 700;
        letter-spacing: .02em;
        color: rgb(var(--vs-primary));
        border-radius: 50%;
        background: rgba(var(--vs-primary), .1);
        box-shadow: 0 0 0 4px #fff, 0 0 0 6px rgba(var(--vs-primary), .25), 0 10px 24px rgba(30, 40, 90, .12);

        img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 50%;
        }
    }

    &__dot {
        position: absolute;
        right: 8px;
        bottom: 8px;
        width: 16px;
        height: 16px;
        border: 3px solid #fff;
        border-radius: 50%;
        background: rgb(40, 199, 111);

        &.is-off {
            background: #b8bac9;
        }
    }

    &__name {
        margin: 0;
        font-size: 1.15rem;
        font-weight: 700;
        line-height: 1.5;
        color: #2c2e45;
        word-break: break-word;
    }

    &__latin {
        margin: 2px 0 0;
        font-size: .8rem;
        letter-spacing: .06em;
        color: #8a8ca5;
    }

    &__tags {
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        gap: 6px;
        margin-top: 12px;
    }

    &__tag {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 3px 10px;
        font-size: .76rem;
        font-weight: 600;
        border-radius: 999px;
        color: #5c6378;
        background: #f1f2f6;
        font-variant-numeric: tabular-nums;

        .feather-icon {
            display: flex;
        }

        &.is-on {
            color: rgb(30, 150, 85);
            background: rgba(40, 199, 111, .12);
        }

        &.is-off {
            color: #7a7d93;
            background: #eceef3;
        }
    }

    &__list {
        display: flex;
        flex-direction: column;
        gap: 14px;
        margin: 0;
        padding: 18px 0 0;
        list-style: none;

        li {
            display: flex;
            align-items: flex-start;
            gap: 12px;
        }

        small {
            display: block;
            font-size: .74rem;
            color: #8a8ca5;
        }

        span,
        a {
            display: block;
            font-size: .9rem;
            font-weight: 500;
            line-height: 1.5;
            color: #2c2e45;
            word-break: break-word;
        }

        a:hover {
            color: rgb(var(--vs-primary));
            text-decoration: underline;
        }
    }

    &__icon {
        display: inline-flex !important;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        width: 34px;
        height: 34px;
        color: rgb(var(--vs-primary)) !important;
        border-radius: 10px;
        background: rgba(var(--vs-primary), .08);

        .feather-icon {
            display: flex;
        }
    }
}

.theme-dark .profile-card {
    &__head { border-color: #2c3155; }
    &__avatar { box-shadow: 0 0 0 4px #262c49, 0 0 0 6px rgba(var(--vs-primary), .35); }
    &__dot { border-color: #262c49; }
    &__name,
    &__list span,
    &__list a { color: #fff; }
    &__tag { color: #c2c6dc; background: #1b2142; }
}

#profile-tabs {
    .vs-tabs--content {
        padding: 0;
    }
}
</style>

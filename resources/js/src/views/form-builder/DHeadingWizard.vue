<template>
    <div>
        <div class="flex justify-between flex-wrap mb-4">
            <!-- <vs-button class="mt-4 mr-2 shadow-lg" type="gradient" color="#7367F0" gradient-color-secondary="#CE9FFC"
                @click.prevent="downloadDoc(item)">{{ $t("text_download_sample") }}</vs-button> -->
            <!-- <vs-checkbox color="success">{{ $t("controlled") }}</vs-checkbox> -->
            <!-- <vs-button class="mt-4" type="border" color="#30c974"><feather-icon icon="PlusIcon" svgClasses="h-4 w-4" />
                {{ $t("AddNew") }}</vs-button> -->
        </div>
        <div class="clearfix"></div>
        <div class="_container-inner">
            <center>
                <span class="font-mef2">
                    {{ $t('kindom_of_cambodia') }}
                    <br />
                    {{ $t('nation_religion_king') }}
                    <br />
                    <center>***</center>
                </span>
            </center>
            <div class="font-mef2" style="float: left;">
                <!-- <img width="120px" src="../../../../assets/images/logo/logo.png"/> -->
                <span v-html="$t('ministry_of_economy')"></span>
            </div><br /><br />
            <div class="clearfix"></div>
            <center>
                <!-- <span class="font-mef2" style="color: #0d72a2; font-size: 12pt">
                            បញ្ជីសរុប<br/>
                        </span> -->
                <br />
                <span class="font-mef2" style="color: #0d72a2; font-size: 12pt">
                    {{ $t(dataAttributes.headingReport) }}
                </span>
                <div class="clearfix"></div>
                <br />
            </center>
        </div>
        <div class="clearfix"></div>
    </div>
</template>

<script>
export default {
    props: {
        dataAttributes: {
            required: true,
        }
    },
    data() {
        return {

        }
    },
    methods() {

    }
}
</script>
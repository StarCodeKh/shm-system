<template>
    <div>
        <div class="flex justify-between flex-wrap mb-4">
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
                <span v-html="$t('ministry_of_economy')"></span>
            </div><br /><br />
            <div class="clearfix"></div>
            <center>
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

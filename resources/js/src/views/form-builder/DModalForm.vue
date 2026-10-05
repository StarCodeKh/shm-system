<template>
    <div class="demo-alignment">
        <vs-popup v-if="dataAttributes.popupFullscreen" class="d-modal" :class="sizeClass" classContent="popup-example" :title="popupTitle"
            :active.sync="showModalForm">
            <!-- Modal Indicator Sub Program-->
            <!-- <modal-indicator-sub-program v-if="dataAttributes.hasIndicatorSubPro" :dataInfo="dataInfo"
                ref="refInitRequest"></modal-indicator-sub-program> -->

            <span v-if="!dataAttributes.hideFormData">
                <d-form @clickForm="initTableData" @cancel="showModalForm = false" ref="refModalForm" :data="data" :dataInfo="dataInfo"
                    :parentDataInfo="parentDataInfo" :formAttributes="formAttributes" :api="api"
                    :rowDisplay="rowDisplay"/>
            </span>

            <!-- <d-indicator v-if="dataAttributes.hasIndicatorSubPro" :dataInfo="dataInfo"
                :dataAttributes="dataAttributes"></d-indicator> -->
        </vs-popup>
        
        <vs-popup v-else class="d-modal" :class="sizeClass" classContent="popup-example" :title="popupTitle" :active.sync="showModalForm">
            <d-form @clickForm="initTableData" @cancel="showModalForm = false" ref="refModalForm" :data="data" :dataInfo="dataInfo"
                :parentDataInfo="parentDataInfo" :formAttributes="formAttributes" :api="api"
                :rowDisplay="rowDisplay"/>
            <!-- <d-indicator v-if="dataAttributes.hasIndicator" :dataInfo="dataInfo"
                :dataAttributes="dataAttributes"></d-indicator> -->

            <!-- Modal Indicator Sub Program-->
            <!-- <modal-indicator-sub-program v-if="dataAttributes.hasIndicatorSubPro" :dataInfo="dataInfo"
                ref="refInitRequest"></modal-indicator-sub-program> -->
        </vs-popup>
    </div>
</template>

<script>
import { gridColumns } from "@/formGrid.js";
import SettingBackup from "@/views/pages/settings/SettingBackup.vue"
import { ref } from 'vue';
import Vue from "vue";
import apiConfig from "@/apiConfig.js"
import DForm from '@/views/form-builder/DForm.vue'
import DModalForm from '@/views/form-builder/DModalForm.vue';
// import DIndicator from '@/views/form-builder/DIndicator.vue';
// import ModalIndicatorSubProgram from '@/views/modules/program-management/indicator/_modal_indicator_subprogram.vue';
// Vue.use("DForm", DForm);

export default {
    components: {
        SettingBackup,
        DForm,
        DModalForm
    },
    props: {
        data: {
            required: true,
        },
        parentDataInfo: {
            required: true,
        },
        api: { type: String },
        formAttributes: {
            type: Array,
            required: true
        },
        dataAttributes: {
            required: true
        },
        rowDisplay: { type: String },
        title: {
            required: true,
        }
    },
    data() {
        return {
            showModalForm: false,
            dataInfo: {},
            dataIndicators: [
                {
                    id: "23",
                    code: "12-",
                    indicator_name: "ឈ្មោះសូចនាករ",
                }
            ]
        }
    },
    computed: {
        // Popup width follows the form: column layout, number of fields, child tables
        sizeClass() {
            const fields = (this.formAttributes || []).filter(f => f && f.type !== 'hidden')
            if (fields.some(f => f.type === 'grid' || f.type === 'text_group')) return 'd-modal--xl'
            const columns = gridColumns(this.rowDisplay)
            const used = Math.min(columns, Math.max(fields.length, 1))
            if (used <= 1 || fields.length <= 3) return 'd-modal--sm'
            return { 2: 'd-modal--md', 3: 'd-modal--lg' }[used]
        },
        // "Create …" for new records, "Edit …" when a record is loaded
        popupTitle() {
            const name = this.$te(this.title) ? this.$t(this.title) : this.title
            return this.dataInfo && this.dataInfo.id
                ? this.$t('form_edit_title', { name })
                : this.$t('form_new_title', { name })
        },
    },
    methods: {
        // To show popup form when create new data
        openNewForm(data) {
            this.dataInfo = {};
            this.showModalForm = true;
            this.$refs.refModalForm.showNewForm(data);
        },
        openNewFormByParent(obj) {
            this.dataInfo = {};
            this.showModalForm = true;
            this.$refs.refModalForm.showNewFormByParent(obj);
        },
        initForm(data) {
            this.showModalForm = true;
            this.dataInfo = data;
            this.$refs.refModalForm.showDataForm(data);
            // this.$refs.refInitRequest.showRequestData(data);
        },
        initTableData(flag) {
            let _search_params = {
                dataInfo: this.parentDataInfo
            }
            this.$emit('clicked', _search_params);
            if(flag==""){
                this.showModalForm = false;
            }
        }
    },
    created() {
        // console.log("check data form objects", this.data);
    }
}
</script>
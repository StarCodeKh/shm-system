<template>
    <div class="demo-alignment">
        <vs-popup v-if="dataAttributes.popupFullscreen" class="d-modal" :class="sizeClass" classContent="popup-example" :title="popupTitle"
            v-model:active="showModalForm">
            <!-- Modal Indicator Sub Program-->
            <!-- <modal-indicator-sub-program v-if="dataAttributes.hasIndicatorSubPro" :dataInfo="dataInfo"
                ref="refInitRequest"></modal-indicator-sub-program> -->

            <span v-if="!dataAttributes.hideFormData">
                <d-form @clickForm="initTableData" @cancel="showModalForm = false" ref="refModalForm" :data="data" :dataInfo="dataInfo"
                    :parentDataInfo="parentDataInfo" :formAttributes="formAttributes" :api="api" :submitLabel="submitLabel" :submitIcon="submitIcon"
                    :rowDisplay="layout.grid"/>
            </span>

            <!-- <d-indicator v-if="dataAttributes.hasIndicatorSubPro" :dataInfo="dataInfo"
                :dataAttributes="dataAttributes"></d-indicator> -->
        </vs-popup>
        
        <vs-popup v-else class="d-modal" :class="sizeClass" classContent="popup-example" :title="popupTitle" v-model:active="showModalForm">
            <d-form @clickForm="initTableData" @cancel="showModalForm = false" ref="refModalForm" :data="data" :dataInfo="dataInfo"
                :parentDataInfo="parentDataInfo" :formAttributes="formAttributes" :api="api" :submitLabel="submitLabel" :submitIcon="submitIcon"
                :rowDisplay="layout.grid"/>
            <!-- <d-indicator v-if="dataAttributes.hasIndicator" :dataInfo="dataInfo"
                :dataAttributes="dataAttributes"></d-indicator> -->

            <!-- Modal Indicator Sub Program-->
            <!-- <modal-indicator-sub-program v-if="dataAttributes.hasIndicatorSubPro" :dataInfo="dataInfo"
                ref="refInitRequest"></modal-indicator-sub-program> -->
        </vs-popup>
    </div>
</template>

<script>
import { ref } from 'vue';
import apiConfig from "@/apiConfig.js"
import DForm from '@/views/form-builder/DForm.vue'
// import DIndicator from '@/views/form-builder/DIndicator.vue';
// import ModalIndicatorSubProgram from '@/views/modules/program-management/indicator/_modal_indicator_subprogram.vue';
// Vue.use("DForm", DForm);

export default {
    components: {
        DForm,
    },
    props: {
        // A popup that does one thing (e.g. send a notification) keeps this title instead of "Create …" / "Edit …"
        fixedTitle: { type: String, default: '' },
        submitLabel: { type: String, default: '' },
        submitIcon: { type: String, default: '' },
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
        // Popup layout follows the form, so it fits without scrolling where it can and rows fill evenly.
        // Every field takes one grid cell (inputs, selects, dates, notes and attachments alike).
        //   up to 4 fields -> 1 column; 5-8 -> 2; 9 or more -> 3,
        //   except 10 -> 2 columns (5 full rows instead of 3 + 3 + 3 + one field alone)
        // Forms with child tables get the extra-wide popup and keep their own grid.
        layout() {
            const fields = (this.formAttributes || []).filter(f => f && f.type !== 'hidden')
            if (fields.some(f => f.type === 'grid' || f.type === 'text_group')) return { size: 'd-modal--xl', grid: this.rowDisplay }
            const cells = fields.length
            if (cells <= 4) return { size: 'd-modal--sm', grid: '1grid' }
            if (cells <= 8 || cells === 10) return { size: 'd-modal--md', grid: '2col' }
            return { size: 'd-modal--lg', grid: '3grid' }
        },
        sizeClass() {
            return this.layout.size
        },
        // "Create …" for new records, "Edit …" when a record is loaded
        popupTitle() {
            if (this.fixedTitle) return this.fixedTitle
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
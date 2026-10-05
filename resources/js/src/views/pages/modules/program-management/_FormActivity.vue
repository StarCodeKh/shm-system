<template>
    <d-modal-form @clicked="initTableData" ref="refModalForm" :data="dataTables" :api="api" :formAttributes="formAttributes"
        :rowDisplay="rowDisplay" :dataAttributes="dataAttributes" :title="$t(title)"></d-modal-form>
</template>
<script>
import { gridClass } from "@/formGrid.js";
import axios from "@/axios.js"
import { ref } from 'vue';
import apiConfig from "@/apiConfig.js"
import DFormInput from '@/views/form-builder/DForm.vue'
import DForm from '@/views/form-builder/form-control/DFormInput.vue'
import vSelect from 'vue-select'
import flatPickr from 'vue-flatpickr-component';
import 'flatpickr/dist/flatpickr.css';
import DTableList from '@/views/form-builder/DTableList.vue'

export default {
    data() {
        return {
            date: null,
            program_id: "",
            sub_program_id: "",
            entity_id: "",
            form: {
                attribute: {}
            },
            showModalForm: false,
            // Data
            title: "ថវិកាគ្រោង",
            api: apiConfig._apiGetTaskDetail,
            dataAttributes: {
                tableStyle: 1,
                page_number: 1,
                offset: 0,
                dataGrid: "row",
                popupFullscreen: true,
                hideSearchBar: true
            },
            dataInfo: {
                entity: "entity",
                entity_member: "entity_member",
                cluster_activity_id: "cluster_activity_id",
                activity_id: "activity_id",
                name_en: "name_en",
                name_kh: "name_kh",
                start_date: "start_date",
                end_date: "end_date",
                assign_to: ""
            },
            data: {

            },
            formAttributes: [
                {
                    name: "cluster_activity_id",
                    type: "select",
                    required: true,
                    options: [],
                },
                {
                    name: "entity_id",
                    type: "select",
                    required: true,
                    hasFilter: true,
                    filterObj: "entity_member_id",
                    api: apiConfig._apiMemberByEntity,
                    options: [],
                },
                {
                    name: "entity_member_id",
                    type: "select",
                    required: true,
                    options: [],
                },
                {
                    name: "name_en",
                    type: "text",
                    required: true
                },
                {
                    name: "name_kh",
                    type: "text",
                    required: true
                },
                {
                    name: "order_level",
                    type: "number",
                    required: false
                },
                {
                    name: "remark",
                    type: "textarea",
                    required: false
                },

            ],
            rowDisplay: "3grid", //1grid, 2grid, 3grid, 4grid
            dataFields: [],
            dataInfo: {}
        }
    },
    methods: {
        showForm(data) {
            this.$refs.refModalForm.openNewForm();
            this.dataInfo = data;
            let _param = { task_id: 1 };
            return new Promise((resolve, reject) => {
                axios.post(this.api, _param)
                    .then((response) => {
                        this.$vs.notify({
                            title: 'Message',
                            text: response.data.message,
                            iconPack: 'feather',
                            icon: 'icon-check-circle',
                            color: 'primary',
                            position: 'top-right'
                        })
                        if (response.data.data) {
                            // console.log("my", response.data.data);
                            this.data = response.data.data;
                        } else {
                            this.data = this.data;
                        }
                        // this.$emit('clickForm');
                        // this.$router.push('/account/expense').catch(() => { })
                    }).catch((error) => {
                        reject(error)
                        this.$vs.notify({
                            title: 'Message',
                            text: "Error, while trying to do action or no permission allow!",
                            iconPack: 'feather',
                            icon: 'icon-check-circle',
                            color: 'danger',
                            position: 'top-right'
                        })
                        this.$vs.loading.close();
                    })
            })

        },
        submitForm() {
            this.dataFields = [];
            let _formAttribute = this.formAttributes;
            this.formAttributes.forEach(_formAttribute => {
                let _d = {
                    [_formAttribute["name"]]: this.form.attribute[_formAttribute["name"]]
                }
                this.dataFields.push(_d);
            });
            // for (let i = 0; i < this.form.attribute.length > 0; i++) {
            //     let _d = {
            //         [this.form.attribute[i]]: this.form.attribute[_formAttribute["name"]]
            //     }
            //     this.dataFields.push(_d);
            // }
            // console.log("Here is form data===>", this.dataFields);
            // return false;
            this.$validator.validateAll().then(result => {
                if (result) {
                    let _data = this.dataFields;
                    // this.$vs.loading();
                    if (this.dataInfo.id) {
                        // alert("Edit");
                        let _id = this.dataInfo.id;
                        // this.$vs.loading.close();
                        return new Promise((resolve, reject) => {
                            axios.put(this.api + '/' + _id, _data)
                                .then((response) => {
                                    this.$vs.notify({
                                        title: 'Message',
                                        text: response.data.message,
                                        iconPack: 'feather',
                                        icon: 'icon-check-circle',
                                        color: 'primary',
                                        position: 'top-right'
                                    })
                                    this.$emit('clickForm');
                                    // this.$router.push('/account/expense').catch(() => { })
                                }).catch((error) => {
                                    reject(error)
                                    this.$vs.notify({
                                        title: 'Message',
                                        text: "Error, while trying to do action or no permission allow!",
                                        iconPack: 'feather',
                                        icon: 'icon-check-circle',
                                        color: 'danger',
                                        position: 'top-right'
                                    })
                                    this.$vs.loading.close();
                                })
                        })
                    } else {
                        return new Promise((resolve, reject) => {
                            axios.post(this.api, _data)
                                .then((response) => {
                                    this.$vs.notify({
                                        title: 'Message',
                                        text: response.data.message,
                                        iconPack: 'feather',
                                        icon: 'icon-check-circle',
                                        color: 'primary',
                                        position: 'top-right'
                                    })
                                    // this.$emit('clickForm');
                                    // this.$router.push('/account/expense').catch(() => { })
                                }).catch((error) => {
                                    reject(error)
                                    this.$vs.notify({
                                        title: 'Message',
                                        text: "Error, while trying to do action or no permission allow!",
                                        iconPack: 'feather',
                                        icon: 'icon-check-circle',
                                        color: 'danger',
                                        position: 'top-right'
                                    })
                                    this.$vs.loading.close();
                                })
                        })
                    }

                }
                this.$vs.loading.close();
            })
        }
    },
    created() {
        this.styleClass = gridClass(this.rowDisplay);
    },
    computed: {
        isSmallerScreen() {
            return this.$store.state.windowWidth < 768
        }
    },
    components: {
        vSelect,
        DForm,
        DFormInput,
        DTableList
    },
}
</script>
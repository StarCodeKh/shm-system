<template>
    <div class="p4">
        <d-form @clickForm="initTableData" ref="refModalForm" :data="data" :dataInfo="dataInfo"
                    :parentDataInfo="parentDataInfo" :formAttributes="formAttributes" :api="api"
                    :rowDisplay="rowDisplay"/>
    </div>
</template>

<script>
    import axios from "@/axios.js";
    import apiConfig from "@/apiConfig.js";
    import DForm from '@/views/form-builder/DForm.vue'
    export default {
        data() {
            return {
                title: "setting_bank",
                api: apiConfig._apiOfficer,
                parentDataInfo:{},
                data: {
                    data: [
                    ],
                    limit: 10,
                    total: 3,
                },
                formAttributes: [
                    {
                        "name": "user_id",
                        "type": "hidden",
                        "required": false
                    },
                    {
                        "name": "photo",
                        "type": "file",
                        "required": true
                    },
                    {
                        "name": "first_name",
                        "type": "text",
                        "required": true
                    },
                    {
                        "name": "last_name",
                        "type": "text",
                        "required": true
                    },
                    {
                        "name": "latin",
                        "type": "text",
                        "required": true
                    },
                    {
                        "name": "identity_no",
                        "type": "text",
                        "required": false
                    },
                    {
                        "name": "identity_member_no",
                        "type": "text",
                        "required": true
                    },
                    {
                        "name": "nssf_card_no",
                        "type": "text",
                        "required": false
                    },
                    {
                        "name": "register_date",
                        "type": "date",
                        "required": false
                    },
                    {
                        "name": "dob",
                        "type": "date",
                        "required": true
                    },
                    {
                        "name": "nationality",
                        "type": "text",
                        "required": false
                    },
                    {
                        "name": "title",
                        "type": "select",
                        "required": false
                    },
                    {
                        "name": "duty",
                        "type": "text",
                        "required": false
                    },
                    {
                        "name": "department_id",
                        "type": "select",
                        "required": true,
                        "hasFilter": true,
                        "hasFormData": false,
                        "filterObj": "entity_id",
                        "api": apiConfig._apiEntityByDept,
                    },
                    {
                        "name": "entity_id",
                        "type": "select",
                        "required": true
                    },
                    {
                        "name": "gender",
                        "type": "select",
                        "required": false
                    },
                    {
                        "name": "address",
                        "type": "textarea",
                        "required": false
                    },
                    {
                        "name": "email",
                        "type": "text",
                        "required": true
                    },
                    {
                        "name": "bank",
                        "type": "select",
                        "required": false
                    },
                    {
                        "name": "bank_code",
                        "type": "text",
                        "required": false
                    },
                    {
                        "name": "bank_account_no",
                        "type": "text",
                        "required": false
                    },
                    {
                        "name": "updated_bank_acc",
                        "type": "text",
                        "required": false
                    },
                    {
                        "name": "position",
                        "type": "text",
                        "required": false
                    },
                    {
                        "name": "telephone_1",
                        "type": "text",
                        "required": false
                    },
                    {
                        "name": "telephone_2",
                        "type": "text",
                        "required": false
                    },
                    {
                        "name": "status",
                        "type": "select",
                        "required": true
                    },
                    {
                        "name": "is_nssf_member",
                        "type": "select",
                        "required": true
                    },
                    {
                        "name": "order_level",
                        "type": "number",
                        "required": false
                    }
                ],
                rowDisplay: "2grid", //1grid, 2grid, 3grid, 4grid
                dataFields: [],
                dataInfo: {}
            }
        },
        components: {
            DForm,
        },
        methods: {
            getDataInfo(){
                let _id = this.$route.params.id;
                return new Promise((resolve, reject) => {
                    axios.get(this.api + "/" +_id)
                        .then((response) => {
                        // this.formAttributes = response.data.data.form_attributes;
                        this.data = response.data.data;
                        this.dataInfo = response.data.data.data;
                        // if(this.formAttributes){
                        this.$refs.refModalForm.showDataForm(this.data.data);
                        // }
                        this.$vs.loading.close();
                    }).catch((error) => { reject(error) })
                })
            },
        },
        mounted(){
            // this.$refs.refModalForm.showDataForm(this.data.data);
        },
        created() {
            this.$vs.loading();
            this.getDataInfo();
        },
        watch: {
            
        }
    }
</script>
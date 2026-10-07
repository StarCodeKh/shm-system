<template>
    <div class="demo-alignment">
        <vs-popup class="d-modal d-modal--md" classContent="popup-example" :title="title" v-model:active="showModalForm">
            <form name="file">
                <input type="file" name="file" @change="selectedFile($event)"/>
            </form>
            <span class="pull">
                <vs-button type="filled" @click="initUploadFile()" class="mt-5 block">{{
                    $t("btn_import") }}</vs-button>
            </span>
        </vs-popup>
    </div>
</template>

<script>
import axios from "@/axios.js";

export default {
    components: {

    },
    props: {
    },
    data() {
        return {
            title: "File Uploader",
            showModalForm: false,
            popupFullscreen: true,
            data: {},
            api: ""
        }
    },
    methods: {
        initFileUploader(dataTables, api) {
            this.showModalForm = true;
            this.data = dataTables;
            this.api = api;
            this.modelName = dataTables.model_name;
        },
        selectedFile(event) {
            this.file = event.target.files[0];
        },
        initUploadFile(){
            this.$vs.loading();
            let formData = new FormData();
            formData.append('file', this.file);
            for(var pair of formData.entries()) {
            }
            return new Promise((resolve, reject) => {
                axios.post(this.api+"/import", formData)
                    .then((response) => {
                        if (response.data.message) {
                            this.$vs.notify({
                                title: 'Message',
                                text: response.data.message,
                                iconPack: 'feather',
                                icon: 'icon-check-circle',
                                color: 'primary',
                                position: 'top-right'
                            })
                        } else {
                            this.$vs.notify({
                                title: 'Message',
                                text: response.data.message,
                                iconPack: 'feather',
                                icon: 'icon-check-circle',
                                color: 'danger',
                                position: 'top-right'
                            })
                        }
                        this.$emit('clicked', '');
                        this.showModalForm = false;
                        this.$vs.loading.close();
                    }).catch((error) => {
                        reject(error)
                        this.$vs.notify({
                            title: 'Message',
                            text: 'msg.save_failed',
                            iconPack: 'feather',
                            icon: 'icon-check-circle',
                            color: 'danger',
                            position: 'top-right',
                        })
                        this.$vs.loading.close();
                    })
            })
        },
    },
    created() {
    }
}
</script>

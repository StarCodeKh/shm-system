<template>
    <vx-card title="Inner Popup">

        <!-- <p>You can add one popup inside another</p> -->

        <div class="demo-alignment">

            <!-- <vs-button @click="popupActive2=true" color="primary" type="filled">Open Popup</vs-button> -->

            <vs-popup classContent="popup-example" title="Lorem ipsum dolor sit amet" v-model:active="activePopBool">
                <vs-input class="inputx mb-3" placeholder="Placeholder" v-model="value1" />
                <vs-input disabled class="inputx mb-3" placeholder="Disabled" v-model="value2" />
                <vs-button @click="popupActive3=true" color="primary" type="filled">Open Inner Popup</vs-button>

                <vs-popup title="Inner popup" v-model:active="popupActive3">
                    <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.</p>
                </vs-popup>
            </vs-popup>
        </div>
    </vx-card>
</template>

<script>
    export default {
        
        data() {
            return {
                activePopBool: false,
                value1: '',
                value2: '',
                popupActive2: false,
                popupActive3: false
            }
        },
        methods: {
            setValue: function(bool) {
                this.activePopBool = bool;
            }
        }
    }
</script>

<template>
    <div class="d-form">
        <div class="vx-row">
            <!-- {{dataInfo | json}} -->
            <div :key="i" v-for="(formAttribute, i) in formAttributes"
                :class="[styleClass, { 'd-field--textarea': formAttribute.type == 'textarea' }]">
                <!-- Form Input Text -->
                <div v-if="formAttribute.type == 'text'" class="mt-4 d-field">
                    <label class="mb-2">{{ $tField(formAttribute.name) }}</label>
                    <span v-if="formAttribute.required">
                        <vs-input v-validate="'required'" size="small" v-model="form.attribute[formAttribute.name]"
                            :placeholder="$tField(formAttribute.name)" :name="formAttribute.name" class="w-full" />
                        <span class="text-danger text-sm" size="small" v-show="errors.has(formAttribute.name)">{{
                            $tRequired(formAttribute.name)
                        }}</span>
                    </span>
                    <vs-input v-else :placeholder="$tField(formAttribute.name)" size="small"
                        v-model="form.attribute[formAttribute.name]" :name="formAttribute.name" class="w-full" />
                </div>

                <!-- Form Input Number -->
                <div v-if="formAttribute.type == 'number'" class="mt-4 d-field">
                    <label class="mb-2">{{ $tField(formAttribute.name) }}</label>
                    <span v-if="formAttribute.required">
                        <vs-input-number :name="formAttribute.name" v-model="form.attribute[formAttribute.name]"
                            icon-inc="expand_less" icon-dec="expand_more" class="w-full" />
                        <span class="text-danger text-sm" v-show="errors.has(formAttribute.name)">{{
                            $tRequired(formAttribute.name)
                        }}</span>
                    </span>
                    <vs-input-number v-else :name="formAttribute.name" v-model="form.attribute[formAttribute.name]"
                        icon-inc="expand_less" icon-dec="expand_more" class="w-full" />
                </div>

                <!-- <div v-if="formAttribute.type == 'select'" class="mt-4 d-field">
                        <label class="mb-2">{{ $tField(formAttribute.name) }}</label>
                        <span v-if="formAttribute.required">
                            <v-select :name="formAttribute.name" v-model="form.attribute[formAttribute.name]" :options="formAttribute.options" :dir="$vs.rtl ? 'rtl' : 'ltr'"
                                v-validate="'required'" />
                            <span class="text-danger text-sm" v-show="errors.has(formAttribute.name)">{{ $tRequired(formAttribute.name)
                            }}</span>
                        </span>
                        <span v-else>
                            <v-select v-model="form.attribute[formAttribute.name]" @v-blur="onChange($event)" :options="formAttribute.options" :clearable="false" :dir="$vs.rtl ? 'rtl' : 'ltr'" class="w-full"></v-select>
                        </span>
                    </div> -->

                <div v-if="formAttribute.type == 'select'" class="mt-4 d-field">
                    <label class="mb-2">{{ $tField(formAttribute.name) }}</label>
                    <span v-if="formAttribute.required">
                        <vs-select :name="formAttribute.name" v-model="form.attribute[formAttribute.name]"
                            :dir="$vs.rtl ? 'rtl' : 'ltr'" v-validate="'required'" class="w-full">
                            <vs-select-item :key="index" :value="item.value" :text="item.label"
                                v-for="(item, index) in data[formAttribute.name]" />
                        </vs-select>
                        <span class="text-danger text-sm" v-show="errors.has(formAttribute.name)">{{
                            $tRequired(formAttribute.name)
                        }}</span>
                    </span>
                    <span v-else>
                        <vs-select :name="formAttribute.name" v-model="form.attribute[formAttribute.name]" class="w-full">
                            <vs-select-item :key="index" :value="item.value" :text="item.label"
                                v-for="(item, index) in data[formAttribute.name]" />
                        </vs-select>
                    </span>
                </div>

                <div v-if="formAttribute.type == 'select_autocomplete'" class="mt-4 d-field">
                    <label class="mb-2">{{ $tField(formAttribute.name) }} - {{ form.attribute[formAttribute.name] }}</label>
                    <span v-if="formAttribute.required">
                        <vs-select :name="formAttribute.name" @change="onChangeElement($event, formAttribute.name)"
                            v-model="form.attribute[formAttribute.name]" :dir="$vs.rtl ? 'rtl' : 'ltr'"
                            v-validate="'required'" class="w-full">
                            <vs-select-item :key="index" :value="item.value" :text="item.label"
                                v-for="(item, index) in data[formAttribute.name]" />
                        </vs-select>
                        <span class="text-danger text-sm" v-show="errors.has(formAttribute.name)">{{ $tRequired(formAttribute.name)
                        }}</span>
                    </span>
                    <span v-else>
                        <vs-select :name="formAttribute.name" @change="onChangeElement($event, formAttribute.name)"
                            v-model="form.attribute[formAttribute.name]" class="w-full">
                            <vs-select-item :key="index" :value="item.value" :text="item.label"
                                v-for="(item, index) in data[formAttribute.name]" />
                        </vs-select>
                    </span>
                </div>

                <!-- Form Check Box -->
                <div v-if="formAttribute.type == 'checkbox'" class="mt-4 d-field">
                    <label class="mb-2">{{ $tField(formAttribute.name) }}</label>
                    <span v-if="formAttribute.required">
                        <vs-checkbox :key="index" v-model="form.attribute[formAttribute.name]">
                            {{ $tField(formAttribute.name) }}</vs-checkbox>
                    </span>
                    <span v-else>
                        <!-- <vs-checkbox :key="index" v-model="form.attribute[formAttribute.name]"> {{$tField(formAttribute.name)}}</vs-checkbox> -->
                        <!-- <vs-checkbox :key="index" v-model="form.attribute[rowf.value]" v-for="(rowf, index) in formAttribute.attributes"> {{ $t(rowf.name)}}</vs-checkbox> -->
                        <vs-checkbox :key="index" v-model="form.attribute[formAttribute.name]"
                            v-for="(rowf, index) in formAttribute.attributes"> {{ $t(rowf.name) }}</vs-checkbox>
                    </span>
                </div>

                <!-- Form Textarea -->
                <div v-if="formAttribute.type == 'textarea'" class="mt-4 d-field">
                    <label class="mb-2">{{ $tField(formAttribute.name) }}</label>
                    <span v-if="formAttribute.required">
                        <vs-textarea v-validate="'required'" class="w-full" v-model="form.attribute[formAttribute.name]" />
                        <span class="text-danger text-sm" v-show="errors.has(formAttribute.name)">{{ $tRequired(formAttribute.name)
                        }}</span>
                    </span>
                    <span v-else>
                        <vs-textarea class="w-full" v-model="form.attribute[formAttribute.name]" />
                    </span>
                </div>

                <!-- Form Date -->
                <div v-if="formAttribute.type == 'date'" class="mt-4 d-field">
                    <label class="mb-2">{{ $tField(formAttribute.name) }}</label>
                    <span v-if="formAttribute.required">
                        <flat-pickr v-validate="'required'" :name="formAttribute.name"
                            v-model="form.attribute[formAttribute.name]" class="w-full" />
                        <span class="text-danger text-sm" v-show="errors.has(formAttribute.name)">{{ $tRequired(formAttribute.name)
                        }}</span>
                    </span>
                    <span v-else>
                        <flat-pickr v-model="form.attribute[formAttribute.name]" class="w-full" />
                    </span>
                </div>

                <!-- Form Group -->
                <div v-if="formAttribute.type == 'text_group'" class="mt-4 vx-col lg:w-1/1 w-full">
                    <div>
                        <label class="mb-2">{{ $tField(formAttribute.name) }}</label>
                        <div :key="j" v-for="(childformAttribute, j) in formAttribute.child_form"
                            class="vx-col lg:w-1/1 w-full">

                            <!-- Form Dynamic -->
                            <!-- Form Input Text -->
                            <div v-if="childformAttribute.type == 'text'" class="mt-4 d-field">
                                <label class="mb-2">{{ $tField(childformAttribute.name) }}</label>
                                <span v-if="childformAttribute.required">
                                    <vs-input v-validate="'required'" size="small"
                                        v-model="form.attribute[childformAttribute.name]"
                                        :placeholder="$tField(childformAttribute.name)" :name="childformAttribute.name"
                                        class="w-full" />
                                    <span class="text-danger text-sm" size="small"
                                        v-show="errors.has(childformAttribute.name)">{{ $tRequired(childformAttribute.name)
                                        }}</span>
                                </span>
                                <vs-input v-else :placeholder="$tField(childformAttribute.name)" size="small"
                                    v-model="form.attribute[childformAttribute.name]" :name="childformAttribute.name"
                                    class="w-full" />
                            </div>

                            <!-- Form Input Number -->
                            <div v-if="childformAttribute.type == 'number'" class="mt-4 d-field">
                                <label class="mb-2">{{ $tField(childformAttribute.name) }}</label>
                                <span v-if="childformAttribute.required">
                                    <vs-input-number :name="childformAttribute.name"
                                        v-model="form.attribute[childformAttribute.name]" icon-inc="expand_less"
                                        icon-dec="expand_more" class="w-full" />
                                    <span class="text-danger text-sm" v-show="errors.has(childformAttribute.name)">{{
                                        $tRequired(childformAttribute.name)
                                    }}</span>
                                </span>
                                <vs-input-number v-else :name="childformAttribute.name"
                                    v-model="form.attribute[childformAttribute.name]" icon-inc="expand_less"
                                    icon-dec="expand_more" class="w-full" />
                            </div>

                            <div v-if="childformAttribute.type == 'select'" class="mt-4 d-field">
                                <label class="mb-2">{{ $tField(childformAttribute.name) }}</label>
                                <span v-if="childformAttribute.required">
                                    <vs-select :name="childformAttribute.name"
                                        v-model="form.attribute[childformAttribute.name]" :dir="$vs.rtl ? 'rtl' : 'ltr'"
                                        v-validate="'required'" class="w-full">
                                        <vs-select-item :key="index" :value="item.value" :text="item.label"
                                            v-for="(item, index) in data[childformAttribute.name]" />
                                    </vs-select>
                                    <span class="text-danger text-sm" v-show="errors.has(childformAttribute.name)">{{
                                        $tRequired(childformAttribute.name)
                                    }}</span>
                                </span>
                                <span v-else>
                                    <vs-select :name="childformAttribute.name"
                                        v-model="form.attribute[childformAttribute.name]" class="w-full">
                                        <vs-select-item :key="index" :value="item.value" :text="item.label"
                                            v-for="(item, index) in data[childformAttribute.name]" />
                                    </vs-select>
                                </span>
                            </div>

                            <!-- Form Check Box -->
                            <div v-if="childformAttribute.type == 'checkbox'" class="mt-4 d-field">
                                <label class="mb-2">{{ $tField(childformAttribute.name) }}</label>
                                <span v-if="childformAttribute.required">
                                    <vs-checkbox :key="index" v-model="form.attribute[childformAttribute.name]"> {{
                                        $tField(childformAttribute.name) }}</vs-checkbox>
                                </span>
                                <span v-else>
                                    <!-- <vs-checkbox :key="index" v-model="form.attribute[childformAttribute.name]"> {{$tField(childformAttribute.name)}}</vs-checkbox> -->
                                    <!-- <vs-checkbox :key="index" v-model="form.attribute[rowf.value]" v-for="(rowf, index) in childformAttribute.attributes"> {{ $t(rowf.name)}}</vs-checkbox> -->
                                    <vs-checkbox :key="index" v-model="form.attribute[childformAttribute.name]"
                                        v-for="(rowf, index) in childformAttribute.attributes"> {{ $t(rowf.name)
                                        }}</vs-checkbox>
                                </span>
                            </div>

                            <!-- Form Textarea -->
                            <div v-if="childformAttribute.type == 'textarea'" class="mt-4 d-field">
                                <label class="mb-2">{{ $tField(childformAttribute.name) }}</label>
                                <span v-if="childformAttribute.required">
                                    <vs-textarea v-validate="'required'" class="w-full"
                                        v-model="form.attribute[childformAttribute.name]" />
                                    <span class="text-danger text-sm" v-show="errors.has(childformAttribute.name)">{{
                                        $tRequired(childformAttribute.name)
                                    }}</span>
                                </span>
                                <span v-else>
                                    <vs-textarea class="w-full" v-model="form.attribute[childformAttribute.name]" />
                                </span>
                            </div>

                            <!-- Form Date -->
                            <div v-if="childformAttribute.type == 'date'" class="mt-4 d-field">
                                <label class="mb-2">{{ $tField(childformAttribute.name) }}</label>
                                <span v-if="childformAttribute.required">
                                    <flat-pickr v-validate="'required'" :name="childformAttribute.name"
                                        v-model="form.attribute[childformAttribute.name]" class="w-full" />
                                    <span class="text-danger text-sm" v-show="errors.has(childformAttribute.name)">{{
                                        $tRequired(childformAttribute.name)
                                    }}</span>
                                </span>
                                <span v-else>
                                    <flat-pickr v-model="form.attribute[childformAttribute.name]" class="w-full" />
                                </span>
                            </div>
                            <!-- #End Form Dynamic -->

                        </div>
                    </div>
                </div>
            </div>

        </div>
        <span class="pull-right">
            <vs-button type="filled" @click.prevent="submitForm" class="mt-5 block">{{ $t("btn_save") }}</vs-button>
        </span>
    </div>
</template>

<script>
import { gridClass } from "@/formGrid.js";
import axios from "@/axios.js"
import DFormInput from '@/views/form-builder/form-control/DFormInput.vue'
import vSelect from 'vue-select'
import flatPickr from 'vue-flatpickr-component';
import 'flatpickr/dist/flatpickr.css';
export default {
    props: {
        formAttributes: {
            required: true
        },
        data: {
            required: true
        },
        rowDisplay: {
            required: true
        },
        api: {
            type: String
        },
        dataInfo: {
            required: true
        }
    },
    data() {
        return {
            styleClass: "",
            formAttribute: "",
            dataFields: [],

            form: {
                attribute: [
                    {
                        order_level: 1,
                        title: "",
                    }
                ]
            },
            form_filter: [],
            date: null,
            form_element: [
                {
                    id: 1,
                },
                {
                    id: 2
                }
            ],
        }
    },
    components: {
        DFormInput,
        vSelect,
        flatPickr
    },
    computed: {
    },
    methods: {
        onChangeElement(e, form_name) {
            this.form.attribute[form_name] = "ddddd";
            this.form_element.push();
        },
        showNewForm() {
            this.form.attribute = [];
            this.formAttributes.forEach(_formAttribute => {
                if (_formAttribute["hasDefault"]) {
                    this.form.attribute[_formAttribute["name"]] = _formAttribute["defaultOptions"]["value"];
                }
            });
        },
        showNewFormByParent(obj) {
            this.form.attribute = [];
            this.formAttributes.forEach(_formAttribute => {
                if (_formAttribute["type"] == "select") {
                    this.form.attribute[_formAttribute["name"]] = "";
                }
                if (_formAttribute["name"] == obj.field) {
                    this.form.attribute[obj.field] = obj.id;
                }
            });
        },
        showDataForm(data) {
            // let _formAttribute = this.formAttributes;
            // for (let i = 0; i < _formAttribute.length; i++) {
            //     this.form.attribute[_formAttribute[i]["name"]] = data[_formAttribute[i]["name"]];// "I got all";
            // }
            // alert("test");
            this.formAttributes.forEach(_formAttribute => {
                if (_formAttribute["type"] == "select") {
                    this.form.attribute[_formAttribute["name"]] = data[_formAttribute["name"]];
                } else {
                    this.form.attribute[_formAttribute["name"]] = data[_formAttribute["name"]];
                }
                this.$validator.validateAll().then(result => {
                })
            });
        },
        onChange(e) {
            this.$validator.validateAll().then(result => {
            });
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
            // return false;
            this.$validator.validateAll().then(result => {
                if (result) {
                    let _data = this.dataFields;
                    this.$vs.loading();
                    if (this.dataInfo.id) {
                        let _id = this.dataInfo.id;
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
                                        text: 'msg.save_failed',
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
                                    this.$emit('clickForm');
                                    // this.$router.push('/account/expense').catch(() => { })
                                }).catch((error) => {
                                    reject(error)
                                    this.$vs.notify({
                                        title: 'Message',
                                        text: 'msg.save_failed',
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
    mounted() {

    },
    created() {
        this.styleClass = gridClass(this.rowDisplay);
    },
    watch: {

    }
}
</script>
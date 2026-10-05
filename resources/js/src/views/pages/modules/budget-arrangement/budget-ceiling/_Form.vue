<template>
    <div>
        <!-- Here is ceiling entity -->
        <!-- Form Input Text -->
        <vs-popup fullscreen classContent="popup-example" title="ទម្រង់បង្កើត Form" :active.sync="showModalForm">
            <div class="vx-row">
                <template v-for="(formAttribute, i) in formAttributes">
                    <!-- Form Input Text -->
                    <div v-if="formAttribute.type == 'text'" :class="styleClass" dclass="mt-4">
                        <label class="mb-2">{{ $t(formAttribute.name) }}</label>
                        <span v-if="formAttribute.required">
                            <vs-input v-validate="'required'" size="small" v-model="form.attribute[formAttribute.name]"
                                :placeholder="$t(formAttribute.name)" :name="formAttribute.name" class="w-full" />
                            <span class="text-danger text-sm" size="small" v-show="errors.has(formAttribute.name)">{{
                                $t("required_" + formAttribute.name)
                            }}</span>
                        </span>
                        <vs-input v-else v-validate="''" :placeholder="$t(formAttribute.name)" size="small"
                            v-model="form.attribute[formAttribute.name]" :name="formAttribute.name" class="w-full" />
                    </div>

                    <!-- Form Input Number -->
                    <div v-if="formAttribute.type == 'number'" :class="styleClass" dclass="mt-4">
                        <label class="mb-2">{{ $t(formAttribute.name) }}</label>
                        <span v-if="formAttribute.required">
                            <vs-input type="number" v-validate="'required'" :name="formAttribute.name"
                                v-model="form.attribute[formAttribute.name]" icon-inc="expand_less" icon-dec="expand_more"
                                class="w-full" />
                            <span class="text-danger text-sm" v-show="errors.has(formAttribute.name)">
                                {{ $t("required_" + formAttribute.name) }}
                            </span>
                        </span>
                        <vs-input type="number" v-validate="''" v-else :name="formAttribute.name"
                            v-model="form.attribute[formAttribute.name]" icon-inc="expand_less" icon-dec="expand_more"
                            class="w-full" />
                    </div>

                    <div v-if="formAttribute.type == 'select'" :class="styleClass" dclass="mt-4">
                        <label class="mb-2">{{ $t(formAttribute.name) }} <span
                                v-if="formAttribute.required">*</span></label>
                        <span v-if="formAttribute.required">
                            <!-- <vs-select :name="formAttribute.name" v-model="form.attribute[formAttribute.name]"
                            :dir="$vs.rtl ? 'rtl' : 'ltr'" v-validate="'required'" class="w-full">
                            <vs-select-item :key="index" :value="item.value" :text="item.label"
                                v-for="(item, index) in data[formAttribute.name]" />
                        </vs-select> -->
                            <template v-if="formAttribute.hasFilter">
                                <v-select size="small"
                                    v-on:input="onInitChange($event, formAttribute.filterObj, formAttribute.name, formAttribute.api)"
                                    v-validate="'required'" v-model="form.attribute[formAttribute.name]"
                                    :name="formAttribute.name" :options="data[formAttribute.name]"
                                    :dir="$vs.rtl ? 'rtl' : 'ltr'" class="mt-2 w-full" />
                                <span class="text-danger text-sm" v-show="errors.has(formAttribute.name)">{{
                                    $t("required_" + formAttribute.name)
                                }}</span>
                                <!-- <v-select v-model="form.attribute[formAttribute.name]" :clearable="false"
                                :options="data[formAttribute.name]" v-validate="'required'" :name="formAttribute.name"
                                :dir="$vs.rtl ? 'rtl' : 'ltr'" />
                            <span class="text-danger text-sm" v-show="errors.has(formAttribute.name)">{{ $t("required_" +
                                formAttribute.name) }}</span> -->
                            </template>
                            <template v-else>
                                <v-select size="small" v-validate="'required'" :name="formAttribute.name"
                                    v-model="form.attribute[formAttribute.name]" :options="data[formAttribute.name]"
                                    :dir="$vs.rtl ? 'rtl' : 'ltr'" class="mt-2 w-full" />
                                <span class="text-danger text-sm" v-show="errors.has(formAttribute.name)">{{
                                    $t("required_" + formAttribute.name)
                                }}</span>
                            </template>
                        </span>
                        <span v-else>
                            <template v-if="formAttribute.hasFilter">
                                <v-select size="small" v-validate="''"
                                    v-on:input="onInitChange($event, formAttribute.filterObj, formAttribute.name, formAttribute.api)"
                                    v-model="form.attribute[formAttribute.name]" :name="formAttribute.name"
                                    :options="data[formAttribute.name]" :dir="$vs.rtl ? 'rtl' : 'ltr'"
                                    class="mt-2 w-full" />
                            </template>
                            <template v-else>
                                <v-select size="small" v-validate="''" :name="formAttribute.name"
                                    v-model="form.attribute[formAttribute.name]" :options="data[formAttribute.name]"
                                    :dir="$vs.rtl ? 'rtl' : 'ltr'" class="mt-2 w-full" />
                            </template>
                            <!-- <vs-select :name="formAttribute.name" v-validate="''" v-model="form.attribute[formAttribute.name]"
                            class="w-full">
                            <vs-select-item :key="index" :value="item.value" :text="item.label"
                                v-for="(item, index) in data[formAttribute.name]" />
                        </vs-select> -->
                        </span>
                    </div>

                    <div v-if="formAttribute.type == 'select_autocomplete'" :class="styleClass" dclass="mt-4">
                        <label class="mb-2">{{ $t(formAttribute.name) }} - {{ form.attribute[formAttribute.name] }}</label>
                        <span v-if="formAttribute.required">
                            <vs-select :name="formAttribute.name" @change="onChangeElement($event, formAttribute.name)"
                                v-model="form.attribute[formAttribute.name]" :dir="$vs.rtl ? 'rtl' : 'ltr'"
                                v-validate="'required'" class="w-full">
                                <vs-select-item :key="index" :value="item.value" :text="item.label"
                                    v-for="(item, index) in data[formAttribute.name]" />
                            </vs-select>
                            <span class="text-danger text-sm" v-show="errors.has(formAttribute.name)">{{ $t("required_" +
                                formAttribute.name)
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
                    <div v-if="formAttribute.type == 'checkbox'" :class="styleClass" dclass="mt-4">
                        <label class="mb-2">{{ $t(formAttribute.name) }}</label>
                        <span v-if="formAttribute.required">
                            <vs-checkbox :key="index" v-model="form.attribute[formAttribute.name]">
                                {{ $t(formAttribute.name) }}
                            </vs-checkbox>
                        </span>
                        <span v-else>
                            <!-- <vs-checkbox :key="index" v-model="form.attribute[formAttribute.name]"> {{$t(formAttribute.name)}}</vs-checkbox> -->
                            <!-- <vs-checkbox :key="index" v-model="form.attribute[rowf.value]" v-for="(rowf, index) in formAttribute.attributes"> {{ $t(rowf.name)}}</vs-checkbox> -->
                            <vs-checkbox :key="index" v-model="form.attribute[formAttribute.name]"
                                v-for="(rowf, index) in formAttribute.attributes"> {{ $t(rowf.name) }}</vs-checkbox>
                        </span>
                    </div>

                    <!-- Form Textarea -->
                    <div v-if="formAttribute.type == 'textarea'" :class="styleClass" dclass="mt-4">
                        <label class="mb-2">{{ $t(formAttribute.name) }}</label>
                        <span v-if="formAttribute.required">
                            <vs-textarea v-validate="'required'" class="w-full"
                                v-model="form.attribute[formAttribute.name]" />
                            <span class="text-danger text-sm" v-show="errors.has(formAttribute.name)">{{ $t("required_" +
                                formAttribute.name)
                            }}</span>
                        </span>
                        <span v-else>
                            <vs-textarea v-validate="''" class="w-full" v-model="form.attribute[formAttribute.name]" />
                        </span>
                    </div>

                    <!-- Form Date -->
                    <div v-if="formAttribute.type == 'date'" :class="styleClass" dclass="mt-4">
                        <label class="mb-2">{{ $t(formAttribute.name) }}</label>
                        <span v-if="formAttribute.required">
                            <flat-pickr v-validate="'required'" :name="formAttribute.name"
                                v-model="form.attribute[formAttribute.name]" class="w-full" />
                            <span class="text-danger text-sm" v-show="errors.has(formAttribute.name)">{{ $t("required_" +
                                formAttribute.name)
                            }}</span>
                        </span>
                        <span v-else>
                            <flat-pickr v-validate="''" v-model="form.attribute[formAttribute.name]" class="w-full" />
                        </span>
                    </div>

                    <!-- Form Group -->
                    <div v-if="formAttribute.type == 'text_group'" class="mt-4 vx-col lg:w-1/1 w-full" d>
                        <div>
                            <label class="mb-2">{{ $t(formAttribute.name) }}</label>
                            <div :key="j" v-for="(childformAttribute, j) in formAttribute.child_form"
                                class="vx-col lg:w-1/1 w-full">

                                <!-- Form Dynamic -->
                                <!-- Form Input Text -->
                                <div v-if="childformAttribute.type == 'text'" class="mt-4">
                                    <label class="mb-2">{{ $t(childformAttribute.name) }}</label>
                                    <span v-if="childformAttribute.required">
                                        <vs-input v-validate="'required'" size="small"
                                            v-model="form.attribute[childformAttribute.name]"
                                            :placeholder="$t(childformAttribute.name)" :name="childformAttribute.name"
                                            class="w-full" />
                                        <span class="text-danger text-sm" size="small"
                                            v-show="errors.has(childformAttribute.name)">{{ $t("required_" +
                                                childformAttribute.name)
                                            }}</span>
                                    </span>
                                    <vs-input v-else :placeholder="$t(childformAttribute.name)" size="small"
                                        v-model="form.attribute[childformAttribute.name]" :name="childformAttribute.name"
                                        class="w-full" />
                                </div>

                                <!-- Form Input Number -->
                                <div v-if="childformAttribute.type == 'number'" class="mt-4">
                                    <label class="mb-2">{{ $t(childformAttribute.name) }}</label>
                                    <span v-if="childformAttribute.required">
                                        <vs-input-number :name="childformAttribute.name"
                                            v-model="form.attribute[childformAttribute.name]" icon-inc="expand_less"
                                            icon-dec="expand_more" class="w-full" />
                                        <span class="text-danger text-sm" v-show="errors.has(childformAttribute.name)">{{
                                            $t("required_" + childformAttribute.name)
                                        }}</span>
                                    </span>
                                    <vs-input-number v-else :name="childformAttribute.name"
                                        v-model="form.attribute[childformAttribute.name]" icon-inc="expand_less"
                                        icon-dec="expand_more" class="w-full" />
                                </div>

                                <div v-if="childformAttribute.type == 'select'" class="mt-4">
                                    <label class="mb-2">{{ $t(childformAttribute.name) }}</label>
                                    <span v-if="childformAttribute.required">
                                        <vs-select :name="childformAttribute.name"
                                            v-model="form.attribute[childformAttribute.name]" :dir="$vs.rtl ? 'rtl' : 'ltr'"
                                            v-validate="'required'" class="w-full">
                                            <vs-select-item :key="index" :value="item.value" :text="item.label"
                                                v-for="(item, index) in data[childformAttribute.name]" />
                                        </vs-select>
                                        <span class="text-danger text-sm" v-show="errors.has(childformAttribute.name)">{{
                                            $t("required_" + childformAttribute.name)
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
                                <div v-if="childformAttribute.type == 'checkbox'" class="mt-4">
                                    <label class="mb-2">{{ $t(childformAttribute.name) }}</label>
                                    <span v-if="childformAttribute.required">
                                        <vs-checkbox :key="index" v-model="form.attribute[childformAttribute.name]"> {{
                                            $t(childformAttribute.name) }}</vs-checkbox>
                                    </span>
                                    <span v-else>
                                        <!-- <vs-checkbox :key="index" v-model="form.attribute[childformAttribute.name]"> {{$t(childformAttribute.name)}}</vs-checkbox> -->
                                        <!-- <vs-checkbox :key="index" v-model="form.attribute[rowf.value]" v-for="(rowf, index) in childformAttribute.attributes"> {{ $t(rowf.name)}}</vs-checkbox> -->
                                        <vs-checkbox :key="index" v-model="form.attribute[childformAttribute.name]"
                                            v-for="(rowf, index) in childformAttribute.attributes"> {{ $t(rowf.name)
                                            }}</vs-checkbox>
                                    </span>
                                </div>

                                <!-- Form Textarea -->
                                <div v-if="childformAttribute.type == 'textarea'" class="mt-4">
                                    <label class="mb-2">{{ $t(childformAttribute.name) }}</label>
                                    <span v-if="childformAttribute.required">
                                        <vs-textarea v-validate="'required'" class="w-full"
                                            v-model="form.attribute[childformAttribute.name]" />
                                        <span class="text-danger text-sm" v-show="errors.has(childformAttribute.name)">{{
                                            $t("required_" + childformAttribute.name)
                                        }}</span>
                                    </span>
                                    <span v-else>
                                        <vs-textarea class="w-full" v-model="form.attribute[childformAttribute.name]" />
                                    </span>
                                </div>

                                <!-- Form Date -->
                                <div v-if="childformAttribute.type == 'date'" class="mt-4">
                                    <label class="mb-2">{{ $t(childformAttribute.name) }}</label>
                                    <span v-if="childformAttribute.required">
                                        <flat-pickr v-validate="'required'" :name="childformAttribute.name"
                                            v-model="form.attribute[childformAttribute.name]" class="w-full" />
                                        <span class="text-danger text-sm" v-show="errors.has(childformAttribute.name)">{{
                                            $t("required_" + childformAttribute.name)
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

                    <!-- Form Grid -->
                    <div v-if="formAttribute.type == 'grid'" class="mt-4 vx-col lg:w-1/1 w-full">
                        <!-- <vs-alert icon="warning" active="true" color="primary" class="mt-5"> -->
                        <div style="background-color:#253358; color: #ffffff; padding: 5px;"><span>{{ $t(formAttribute.name)
                        }}</span></div>
                        <!-- </vs-alert> -->
                    </div>

                </template>
            </div>

            <div class="vx-row">
                <vs-tabs position="top" class="tabs-shadow-none" id="profile-tabs" :key="isSmallerScreen">
                    <vs-tab icon-pack="feather" icon="icon-edit" :label="!isSmallerScreen ? 'បន្ទុកបុគ្គលិក' : ''">
                        <div class="tab-general md:ml-4 md:mt-0 mt-4 ml-0">
                            <div class="vx-row">
                                <div class="vx-col lg:w-1/3 w-full mt-4">
                                    <label class="mb-2">{{ 'ច្បាប់ឆ្នាំ N' }}</label>
                                    <vs-input type="number" size="small" class="w-full" />
                                </div>

                                <div class="vx-col lg:w-1/3 w-full mt-4">
                                    <label class="mb-2">{{ 'ចំណាយប្រចាំ' }}</label>
                                    <vs-input type="number" size="small" class="w-full" />
                                </div>

                                <div class="vx-col lg:w-1/3 w-full mt-4">
                                    <label class="mb-2">{{ 'ចំណាយមិនប្រចាំ' }}</label>
                                    <vs-input type="number" size="small" class="w-full" />
                                </div>
                            </div>

                            <!-- Form Grid -->
                            <div class="mt-4 vx-col lg:w-1/1 w-full">
                                <!-- <vs-alert icon="warning" active="true" color="primary" class="mt-5"> -->
                                <div style="background-color:#253358; color: #ffffff; padding: 5px;">
                                    <span>{{ "ពិដានចំណាយចរន្ត" }}</span>
                                </div>

                                <!-- row -->
                                <div class="vx-row">
                                    <div class="vx-col lg:w-1/3 w-full mt-4">
                                        <label class="mb-2">{{ 'ឆ្នាំ ២០២៣' }}</label>
                                        <vs-input type="number" size="small" class="w-full" />
                                    </div>

                                    <div class="vx-col lg:w-1/3 w-full mt-4">
                                        <label class="mb-2">{{ 'ឆ្នាំ ២០២៤' }}</label>
                                        <vs-input type="number" size="small" class="w-full" />
                                    </div>

                                    <div class="vx-col lg:w-1/3 w-full mt-4">
                                        <label class="mb-2">{{ 'ឆ្នាំ ២០២៥' }}</label>
                                        <vs-input type="number" size="small" class="w-full" />
                                    </div>
                                </div>

                            </div>

                            <!-- Form Grid -->
                            <div class="mt-4 vx-col lg:w-1/1 w-full">
                                <!-- <vs-alert icon="warning" active="true" color="primary" class="mt-5"> -->
                                <div style="background-color:#253358; color: #ffffff; padding: 5px;">
                                    <span>{{ "អាត្រាកើន/ថយ" }}</span>
                                </div>

                                <!-- row -->
                                <div class="vx-row">
                                    <div class="vx-col lg:w-1/3 w-full mt-4">
                                        <label class="mb-2">{{ 'អាត្រាឆ្នាំ ២០២៣' }}</label>
                                        <vs-input type="number" size="small" class="w-full" />
                                    </div>

                                    <div class="vx-col lg:w-1/3 w-full mt-4">
                                        <label class="mb-2">{{ 'អាត្រាឆ្នាំ ២០២៤' }}</label>
                                        <vs-input type="number" size="small" class="w-full" />
                                    </div>

                                    <div class="vx-col lg:w-1/3 w-full mt-4">
                                        <label class="mb-2">{{ 'អាត្រាឆ្នាំ ២០២៥' }}</label>
                                        <vs-input type="number" size="small" class="w-full" />
                                    </div>
                                </div>

                            </div>
                        </div>
                    </vs-tab>

                    <vs-tab icon-pack="feather" icon="icon-settings" :label="!isSmallerScreen ? 'ក្រៅបន្ទុកបុគ្គលិក' : ''">
                        <div class="tab-general md:ml-4 md:mt-0 mt-4 ml-0">
                            <div class="vx-row">
                                <div class="vx-col lg:w-1/3 w-full mt-4">
                                    <label class="mb-2">{{ 'ច្បាប់ឆ្នាំ N' }}</label>
                                    <vs-input type="number" size="small" class="w-full" />
                                </div>

                                <div class="vx-col lg:w-1/3 w-full mt-4">
                                    <label class="mb-2">{{ 'ចំណាយប្រចាំ' }}</label>
                                    <vs-input type="number" size="small" class="w-full" />
                                </div>

                                <div class="vx-col lg:w-1/3 w-full mt-4">
                                    <label class="mb-2">{{ 'ចំណាយមិនប្រចាំ' }}</label>
                                    <vs-input type="number" size="small" class="w-full" />
                                </div>
                            </div>

                            <!-- Form Grid -->
                            <div class="mt-4 vx-col lg:w-1/1 w-full">
                                <!-- <vs-alert icon="warning" active="true" color="primary" class="mt-5"> -->
                                <div style="background-color:#253358; color: #ffffff; padding: 5px;">
                                    <span>{{ "ពិដានចំណាយចរន្ត" }}</span>
                                </div>

                                <!-- row -->
                                <div class="vx-row">
                                    <div class="vx-col lg:w-1/3 w-full mt-4">
                                        <label class="mb-2">{{ 'ឆ្នាំ ២០២៣' }}</label>
                                        <vs-input type="number" size="small" class="w-full" />
                                    </div>

                                    <div class="vx-col lg:w-1/3 w-full mt-4">
                                        <label class="mb-2">{{ 'ឆ្នាំ ២០២៤' }}</label>
                                        <vs-input type="number" size="small" class="w-full" />
                                    </div>

                                    <div class="vx-col lg:w-1/3 w-full mt-4">
                                        <label class="mb-2">{{ 'ឆ្នាំ ២០២៥' }}</label>
                                        <vs-input type="number" size="small" class="w-full" />
                                    </div>
                                </div>

                            </div>

                            <!-- Form Grid -->
                            <div class="mt-4 vx-col lg:w-1/1 w-full">
                                <!-- <vs-alert icon="warning" active="true" color="primary" class="mt-5"> -->
                                <div style="background-color:#253358; color: #ffffff; padding: 5px;">
                                    <span>{{ "អាត្រាកើន/ថយ" }}</span>
                                </div>

                                <!-- row -->
                                <div class="vx-row">
                                    <div class="vx-col lg:w-1/3 w-full mt-4">
                                        <label class="mb-2">{{ 'អាត្រាឆ្នាំ ២០២៣' }}</label>
                                        <vs-input type="number" size="small" class="w-full" />
                                    </div>

                                    <div class="vx-col lg:w-1/3 w-full mt-4">
                                        <label class="mb-2">{{ 'អាត្រាឆ្នាំ ២០២៤' }}</label>
                                        <vs-input type="number" size="small" class="w-full" />
                                    </div>

                                    <div class="vx-col lg:w-1/3 w-full mt-4">
                                        <label class="mb-2">{{ 'អាត្រាឆ្នាំ ២០២៥' }}</label>
                                        <vs-input type="number" size="small" class="w-full" />
                                    </div>
                                </div>

                            </div>
                        </div>
                    </vs-tab>
                </vs-tabs>
            </div>
            <span class="pull-right">
                <vs-button type="filled" @click.prevent="submitForm" class="mt-5 block">{{ $t("btn_save") }}</vs-button>
            </span>
        </vs-popup>
    </div>
</template>
<script>
import axios from "@/axios.js"
import apiConfig from "@/apiConfig.js"
import DFormInput from '@/views/form-builder/form-control/DFormInput.vue'
import vSelect from 'vue-select'
import flatPickr from 'vue-flatpickr-component';
import 'flatpickr/dist/flatpickr.css';
export default {
    props: {
        data: {
            required: true
        },
    },
    data() {
        return {
            date: null,
            program_id: "",
            sub_program_id: "",
            entity_id: "",
            rowDisplay: "3grid",
            data: {
                entity_id: [
                    {
                        id: 1,
                        label: "អង្គភាពទី១"
                    }
                ]
            },
            form: {
                attribute: {}
            },
            showModalForm: false,
            formAttributes: [
                {
                    name: "program_id", // ប្រភេទមុខសញ្ញា
                    type: "select",
                    required: true,
                    hasFilter: true,
                    filterObj: "sub_program_id",
                    api: apiConfig._apiSubProgramByPro,
                    options: [],
                },
                {
                    name: "sub_program_id", // ប្រភេទមុខសញ្ញា
                    type: "select",
                    required: true,
                    hasFilter: true,
                    filterObj: "entity_id",
                    api: apiConfig._apiEntityBySubPro,
                    options: [],
                },
                {
                    name: "entity_id", // អង្គភាព
                    type: "select",
                    required: true,
                    options: [],
                },
                {
                    name: "tab_budget_ceiling",
                    type: "tab_budget_ceiling_group",
                    required: false,
                },
            ]
        }
    },
    methods: {
        onInitChange($event, _formAttribute, _formName, _apiRequest) {
            // alert("tes");
            // if (!this.validateForm) return
            // alert("testing");
            let _data = {
                param: this.form.attribute[_formName]
            };
            this.form.attribute[_formAttribute] = null;
            this.data[_formAttribute] = [];
            // alert(this.form.attribute[_formName]);
            return new Promise((resolve, reject) => {
                axios.post(_apiRequest, _data)
                    .then((response) => {
                        this.$vs.notify({
                            title: 'Message',
                            text: "Loading...",
                            iconPack: 'feather',
                            icon: 'icon-check-circle',
                            color: 'primary',
                            position: 'top-right'
                        });
                        for (let i = 0; i < response["data"]["data"].length; i++) {
                            let _d = {
                                "label": response["data"]["data"][i]["label"],
                                "value": response["data"]["data"][i]["value"],
                            }
                            this.data[_formAttribute].push(_d);
                        }
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
        showForm(data) {
            this.showModalForm = true;
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
        if (this.rowDisplay == "1grid") {
            this.styleClass = "vx-col lg:w-1/1 w-full mt-4";
        }
        if (this.rowDisplay == "2grid") {
            this.styleClass = "vx-col lg:w-1/2 w-full mt-4";
        }
        if (this.rowDisplay == "3grid") {
            this.styleClass = "vx-col lg:w-1/3 w-full mt-4";
        }
        if (this.rowDisplay == "4grid") {
            this.styleClass = "vx-col lg:w-1/4 w-full mt-4";
        }
        if (this.rowDisplay == "full-grid") {
            this.styleClass = "vx-col lg:w-1/1 w-full";
        }
    },
    computed: {
        isSmallerScreen() {
            return this.$store.state.windowWidth < 768
        }
    },
    components: {
        vSelect,
    },
}
</script>
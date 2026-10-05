 <!-- ###### If data attribute has indicator mean can be able to allow create form data  -->
<template>
    <div class="mt-6">
        <div class="mt-5">
            <vs-tabs>
                <vs-tab label="Detail">
                    <div class="tab-text">
                        <p>Muffin cupcake candy chocolate cake gummi bears fruitcake donut dessert pie. Wafer toffee bonbon
                            dragée. Jujubes cotton candy gummies chupa chups. Sweet fruitcake cheesecake biscuit cotton
                            candy. Cookie powder marshmallow donut. Candy cookie sweet roll bear claw sweet roll. Cake
                            tiramisu cotton candy gingerbread cheesecake toffee cake. Cookie liquorice dessert candy canes
                            jelly.</p>
                        <p class="mt-2">Sweet chocolate muffin fruitcake gummies jujubes pie lollipop. Brownie marshmallow
                            caramels gingerbread jelly beans chocolate bar oat cake wafer. Chocolate bar danish icing sweet
                            apple pie jelly-o carrot cake cookie cake. </p>
                    </div>
                </vs-tab>
                <vs-tab :label="$t('indicator')">
                    <div class="tab-text">
                        <h3>{{ $t("indicator") }}</h3>
                        <!-- ADD NEW -->
                        <vs-popup fullscreen classContent="popup-example" :title="title" :active.sync="showModalForm">
                            <div>
                                <div class="vx-row">
                                    <template v-for="(formAttribute, i) in formAttributes">
                                        <!-- Form Input Text -->
                                        <div v-if="formAttribute.type == 'text'" :class="styleClass" dclass="mt-4">
                                            <label class="mb-2">{{ $t(formAttribute.name) }}</label>
                                            <span v-if="formAttribute.required">
                                                <vs-input v-validate="'required'" size="small"
                                                    v-model="form.attribute[formAttribute.name]"
                                                    :placeholder="$t(formAttribute.name)" :name="formAttribute.name"
                                                    class="w-full" />
                                                <span class="text-danger text-sm" size="small"
                                                    v-show="errors.has(formAttribute.name)">{{
                                                        $t("required_" + formAttribute.name)
                                                    }}</span>
                                            </span>
                                            <vs-input v-else :placeholder="$t(formAttribute.name)" size="small"
                                                v-model="form.attribute[formAttribute.name]" :name="formAttribute.name"
                                                class="w-full" />
                                        </div>

                                        <!-- Form Input Number -->
                                        <div v-if="formAttribute.type == 'number'" :class="styleClass" dclass="mt-4">
                                            <label class="mb-2">{{ $t(formAttribute.name) }}</label>
                                            <span v-if="formAttribute.required">
                                                <vs-input type="number" :name="formAttribute.name"
                                                    v-model="form.attribute[formAttribute.name]" icon-inc="expand_less"
                                                    icon-dec="expand_more" class="w-full" />
                                                <span class="text-danger text-sm" v-show="errors.has(formAttribute.name)">{{
                                                    $t("required_" + formAttribute.name)
                                                }}</span>
                                            </span>
                                            <vs-input type="number" v-else :name="formAttribute.name"
                                                v-model="form.attribute[formAttribute.name]" icon-inc="expand_less"
                                                icon-dec="expand_more" class="w-full" />
                                        </div>

                                        <div v-if="formAttribute.type == 'select'" :class="styleClass" dclass="mt-4">
                                            <label class="mb-2">{{ $t(formAttribute.name) }}</label>
                                            <span v-if="formAttribute.required">
                                                <vs-select :name="formAttribute.name"
                                                    v-model="form.attribute[formAttribute.name]"
                                                    :dir="$vs.rtl ? 'rtl' : 'ltr'" v-validate="'required'" class="w-full">
                                                    <vs-select-item :key="index" :value="item.value" :text="item.label"
                                                        v-for="(item, index) in data[formAttribute.name]" />
                                                </vs-select>
                                                <span class="text-danger text-sm" v-show="errors.has(formAttribute.name)">{{
                                                    $t("required_" + formAttribute.name)
                                                }}</span>
                                            </span>
                                            <span v-else>
                                                <vs-select :name="formAttribute.name"
                                                    v-model="form.attribute[formAttribute.name]" class="w-full">
                                                    <vs-select-item :key="index" :value="item.value" :text="item.label"
                                                        v-for="(item, index) in data[formAttribute.name]" />
                                                </vs-select>
                                            </span>
                                        </div>

                                        <div v-if="formAttribute.type == 'select_autocomplete'" :class="styleClass"
                                            dclass="mt-4">
                                            <label class="mb-2">{{ $t(formAttribute.name) }} - {{
                                                form.attribute[formAttribute.name]
                                            }}</label>
                                            <span v-if="formAttribute.required">
                                                <vs-select :name="formAttribute.name"
                                                    @change="onChangeElement($event, formAttribute.name)"
                                                    v-model="form.attribute[formAttribute.name]"
                                                    :dir="$vs.rtl ? 'rtl' : 'ltr'" v-validate="'required'" class="w-full">
                                                    <vs-select-item :key="index" :value="item.value" :text="item.label"
                                                        v-for="(item, index) in data[formAttribute.name]" />
                                                </vs-select>
                                                <span class="text-danger text-sm" v-show="errors.has(formAttribute.name)">{{
                                                    $t("required_"
                                                        +
                                                        formAttribute.name)
                                                }}</span>
                                            </span>
                                            <span v-else>
                                                <vs-select :name="formAttribute.name"
                                                    @change="onChangeElement($event, formAttribute.name)"
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
                                                    {{ $t(formAttribute.name) }}</vs-checkbox>
                                            </span>
                                            <span v-else>
                                                <!-- <vs-checkbox :key="index" v-model="form.attribute[formAttribute.name]"> {{$t(formAttribute.name)}}</vs-checkbox> -->
                                                <!-- <vs-checkbox :key="index" v-model="form.attribute[rowf.value]" v-for="(rowf, index) in formAttribute.attributes"> {{ $t(rowf.name)}}</vs-checkbox> -->
                                                <vs-checkbox :key="index" v-model="form.attribute[formAttribute.name]"
                                                    v-for="(rowf, index) in formAttribute.attributes"> {{ $t(rowf.name)
                                                    }}</vs-checkbox>
                                            </span>
                                        </div>

                                        <!-- Form Textarea -->
                                        <div v-if="formAttribute.type == 'textarea'" :class="styleClass" dclass="mt-4">
                                            <label class="mb-2">{{ $t(formAttribute.name) }}</label>
                                            <span v-if="formAttribute.required">
                                                <vs-textarea v-validate="'required'" class="w-full"
                                                    v-model="form.attribute[formAttribute.name]" />
                                                <span class="text-danger text-sm" v-show="errors.has(formAttribute.name)">{{
                                                    $t("required_"
                                                        +
                                                        formAttribute.name)
                                                }}</span>
                                            </span>
                                            <span v-else>
                                                <vs-textarea class="w-full" v-model="form.attribute[formAttribute.name]" />
                                            </span>
                                        </div>

                                        <!-- Form Date -->
                                        <div v-if="formAttribute.type == 'date'" :class="styleClass" dclass="mt-4">
                                            <label class="mb-2">{{ $t(formAttribute.name) }}</label>
                                            <span v-if="formAttribute.required">
                                                <flat-pickr v-validate="'required'" :name="formAttribute.name"
                                                    v-model="form.attribute[formAttribute.name]" class="w-full" />
                                                <span class="text-danger text-sm" v-show="errors.has(formAttribute.name)">{{
                                                    $t("required_"
                                                        +
                                                        formAttribute.name)
                                                }}</span>
                                            </span>
                                            <span v-else>
                                                <flat-pickr v-model="form.attribute[formAttribute.name]" class="w-full" />
                                            </span>
                                        </div>

                                        <!-- Form Group -->
                                        <div v-if="formAttribute.type == 'text_group'" class="mt-4 vx-col lg:w-1/1 w-full"
                                            d>
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
                                                                :placeholder="$t(childformAttribute.name)"
                                                                :name="childformAttribute.name" class="w-full" />
                                                            <span class="text-danger text-sm" size="small"
                                                                v-show="errors.has(childformAttribute.name)">{{
                                                                    $t("required_" +
                                                                        childformAttribute.name)
                                                                }}</span>
                                                        </span>
                                                        <vs-input v-else :placeholder="$t(childformAttribute.name)"
                                                            size="small" v-model="form.attribute[childformAttribute.name]"
                                                            :name="childformAttribute.name" class="w-full" />
                                                    </div>

                                                    <!-- Form Input Number -->
                                                    <div v-if="childformAttribute.type == 'number'" class="mt-4">
                                                        <label class="mb-2">{{ $t(childformAttribute.name) }}</label>
                                                        <span v-if="childformAttribute.required">
                                                            <vs-input-number :name="childformAttribute.name"
                                                                v-model="form.attribute[childformAttribute.name]"
                                                                icon-inc="expand_less" icon-dec="expand_more"
                                                                class="w-full" />
                                                            <span class="text-danger text-sm"
                                                                v-show="errors.has(childformAttribute.name)">{{
                                                                    $t("required_" + childformAttribute.name)
                                                                }}</span>
                                                        </span>
                                                        <vs-input-number v-else :name="childformAttribute.name"
                                                            v-model="form.attribute[childformAttribute.name]"
                                                            icon-inc="expand_less" icon-dec="expand_more" class="w-full" />
                                                    </div>

                                                    <div v-if="childformAttribute.type == 'select'" class="mt-4">
                                                        <label class="mb-2">{{ $t(childformAttribute.name) }}</label>
                                                        <span v-if="childformAttribute.required">
                                                            <vs-select :name="childformAttribute.name"
                                                                v-model="form.attribute[childformAttribute.name]"
                                                                :dir="$vs.rtl ? 'rtl' : 'ltr'" v-validate="'required'"
                                                                class="w-full">
                                                                <vs-select-item :key="index" :value="item.value"
                                                                    :text="item.label"
                                                                    v-for="(item, index) in data[childformAttribute.name]" />
                                                            </vs-select>
                                                            <span class="text-danger text-sm"
                                                                v-show="errors.has(childformAttribute.name)">{{
                                                                    $t("required_" + childformAttribute.name)
                                                                }}</span>
                                                        </span>
                                                        <span v-else>
                                                            <vs-select :name="childformAttribute.name"
                                                                v-model="form.attribute[childformAttribute.name]"
                                                                class="w-full">
                                                                <vs-select-item :key="index" :value="item.value"
                                                                    :text="item.label"
                                                                    v-for="(item, index) in data[childformAttribute.name]" />
                                                            </vs-select>
                                                        </span>
                                                    </div>

                                                    <!-- Form Check Box -->
                                                    <div v-if="childformAttribute.type == 'checkbox'" class="mt-4">
                                                        <label class="mb-2">{{ $t(childformAttribute.name) }}</label>
                                                        <span v-if="childformAttribute.required">
                                                            <vs-checkbox :key="index"
                                                                v-model="form.attribute[childformAttribute.name]"> {{
                                                                    $t(childformAttribute.name) }}</vs-checkbox>
                                                        </span>
                                                        <span v-else>
                                                            <!-- <vs-checkbox :key="index" v-model="form.attribute[childformAttribute.name]"> {{$t(childformAttribute.name)}}</vs-checkbox> -->
                                                            <!-- <vs-checkbox :key="index" v-model="form.attribute[rowf.value]" v-for="(rowf, index) in childformAttribute.attributes"> {{ $t(rowf.name)}}</vs-checkbox> -->
                                                            <vs-checkbox :key="index"
                                                                v-model="form.attribute[childformAttribute.name]"
                                                                v-for="(rowf, index) in childformAttribute.attributes"> {{
                                                                    $t(rowf.name)
                                                                }}</vs-checkbox>
                                                        </span>
                                                    </div>

                                                    <!-- Form Textarea -->
                                                    <div v-if="childformAttribute.type == 'textarea'" class="mt-4">
                                                        <label class="mb-2">{{ $t(childformAttribute.name) }}</label>
                                                        <span v-if="childformAttribute.required">
                                                            <vs-textarea v-validate="'required'" class="w-full"
                                                                v-model="form.attribute[childformAttribute.name]" />
                                                            <span class="text-danger text-sm"
                                                                v-show="errors.has(childformAttribute.name)">{{
                                                                    $t("required_" + childformAttribute.name)
                                                                }}</span>
                                                        </span>
                                                        <span v-else>
                                                            <vs-textarea class="w-full"
                                                                v-model="form.attribute[childformAttribute.name]" />
                                                        </span>
                                                    </div>

                                                    <!-- Form Date -->
                                                    <div v-if="childformAttribute.type == 'date'" class="mt-4">
                                                        <label class="mb-2">{{ $t(childformAttribute.name) }}</label>
                                                        <span v-if="childformAttribute.required">
                                                            <flat-pickr v-validate="'required'"
                                                                :name="childformAttribute.name"
                                                                v-model="form.attribute[childformAttribute.name]"
                                                                class="w-full" />
                                                            <span class="text-danger text-sm"
                                                                v-show="errors.has(childformAttribute.name)">{{
                                                                    $t("required_" + childformAttribute.name)
                                                                }}</span>
                                                        </span>
                                                        <span v-else>
                                                            <flat-pickr v-model="form.attribute[childformAttribute.name]"
                                                                class="w-full" />
                                                        </span>
                                                    </div>
                                                    <!-- #End Form Dynamic -->

                                                </div>
                                            </div>
                                        </div>

                                        <!-- Form Grid -->
                                        <div v-if="formAttribute.type == 'grid'" class="mt-4 vx-col lg:w-1/1 w-full">
                                            <!-- <vs-alert icon="warning" active="true" color="primary" class="mt-5"> -->
                                            <div style="background-color:#253358; color: #ffffff; padding: 5px;"><span>{{
                                                $t(formAttribute.name)
                                            }}</span></div>
                                            <!-- </vs-alert> -->
                                        </div>

                                    </template>
                                </div>
                                <span class="pull-right">
                                    <vs-button type="filled" @click.prevent="submitForm" class="mt-5 block">{{
                                        $t("btn_save") }}</vs-button>
                                </span>
                            </div>
                        </vs-popup>

                        <div v-if="!dataInfo.indicator || dataInfo.indicator"
                            class="flex flex-wrap-reverse items-center data-list-btn-container">
                            <div class="btn-add-new p-3 mb-4 mr-4 rounded-lg cursor-pointer flex items-center justify-center text-lg font-medium text-base text-primary border border-solid border-primary"
                                @click="addIndicator(dataInfo)">
                                <feather-icon icon="PlusIcon" svgClasses="h-4 w-4" />
                                <span class="ml-2 text-base text-primary">{{ $t("AddNew") }}</span>
                            </div>
                        </div>

                        <vs-table :data="data.data">
                            <!-- <vs-table> -->
                            <template slot="thead">
                                <vs-th style="background-color: #28C76F; color: #ffffff; font-weight: bold;">{{ $t("no")
                                }}</vs-th>
                                <vs-th style="background-color: #28C76F; color: #ffffff; font-weight: bold;" :key="i"
                                    v-for="(header, i) in dataHeaders">{{ $t(header) }}</vs-th>
                                <vs-th style="background-color: #28C76F; color: #ffffff; font-weight: bold;">{{ $t("Action")
                                }}</vs-th>
                            </template>
                            <template slot-scope="{data}">
                                <template>
                                    <vs-tr :key="indextr" v-for="(row, indextr) in data">
                                        <vs-td>{{ indextr + 1 }}</vs-td>
                                        <vs-td v-for="header in dataHeaders" :key="header.indextr">{{ row[header] }}</vs-td>
                                        <vs-td>
                                            <template v-if="dataAttributes.actionButton">
                                                <feather-icon style="cursor: pointer;"
                                                    v-for="rowBtnAction in dataAttributes.actionButton"
                                                    :key="rowBtnAction.indextr" :icon="rowBtnAction.icon"
                                                    svgClasses="mr-2 w-5 h-5 hover:text-primary stroke-current"
                                                    @click.stop="initAction(row, rowBtnAction.method)" />
                                            </template>
                                        </vs-td>
                                    </vs-tr>
                                </template>
                            </template>
                        </vs-table>
                    </div>
                </vs-tab>
            </vs-tabs>
        </div>

    </div>
</template>

<script>
import axios from "@/axios.js";
import apiConfig from "@/apiConfig.js";
import { ref } from 'vue';
import DForm from '@/views/form-builder/DForm.vue';

export default {
    props: {
        dataInfo: {
            required: true,
        },
    },
    data() {
        return {
            title: "indicator",
            api: apiConfig._apiIndicatorSubPro,
            sub_program_id: 0,
            dataId: 0,
            dataAttributes: {
                popupFullscreen: true,
                backgroundColor: "warning",
                tableStyle: 2,
                page_number: 1,
                offset: 0,
                dataGrid: "row",
                hasIndicatorSubPro: true,
                allowDel: true,
                actionButton: [
                    {
                        icon: "ListIcon",
                        path: "#",
                        method: "Indicator",
                        allow: true
                    },
                    {
                        icon: "ViewIcon",
                        path: "#",
                        method: "View",
                        allow: true
                    },
                    {
                        icon: "EditIcon",
                        path: "#",
                        method: "Edit",
                        allow: true
                    },
                    {
                        icon: "TrashIcon",
                        path: "#",
                        method: "Delete",
                        allow: true
                    },
                ]
            },
            dataHeaders: {
                header1: "code",
                header2: "kpi_name_en",
                header3: "kpi_name_kh",
                header4: "order_level",
                header5: "status"
            },
            showModalForm: false,
            form: {
                attribute: [
                    {
                        order_level: 1,
                        title: "",
                    }
                ]
            },
            data: {
                data: [{}],
                limit: 10,
                total: 3
            },
            formAttributes: [
                {
                    name: "sub_program_id",
                    type: "text",
                    required: false
                },
                {
                    name: "code",
                    type: "text",
                    required: true
                },
                {
                    name: "kpi_name_en",
                    type: "text",
                    required: true
                },
                {
                    name: "kpi_name_kh",
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
                }
            ],
            rowDisplay: "3grid", //1grid, 2grid, 3grid, 4grid
            dataFields: [],
            dataInfo: {},
            dataIndicators: {},
            dataTables: {},
            showIndicatorForm: false
        }
    },
    components: {
        DForm
    },
    methods: {
        addRow() {
        },
        initEdit(dataInfo) {
            this.$refs.refModalForm.initForm(dataInfo);
        },
        addIndicator(dataInfo) {
            this.dataId = 0;
            this.showModalForm = true;
            this.form.attribute["sub_program_id"] = dataInfo.id;
        },
        showRequestData(dataInfo) {
            this.sub_program_id = dataInfo.id;
            this.$vs.loading();
            this.getData();
        },
        initAction(data, method) {
            this.dataId = data.id;
            if (method == "View") {
                this.showModalForm = true;
                this.formAttributes.forEach(_formAttribute => {
                    if (_formAttribute["type"] == "select") {
                        this.form.attribute[_formAttribute["name"]] = data[_formAttribute["name"]];
                    } else {
                        this.form.attribute[_formAttribute["name"]] = data[_formAttribute["name"]];
                    }
                    this.$validator.validateAll().then(result => {
                    })
                });
                // this.$refs.refModalForm.initForm(data);
            }
            if (method == "Edit") {
                this.showModalForm = true;
                this.formAttributes.forEach(_formAttribute => {
                    if (_formAttribute["type"] == "select") {
                        this.form.attribute[_formAttribute["name"]] = data[_formAttribute["name"]];
                    } else {
                        this.form.attribute[_formAttribute["name"]] = data[_formAttribute["name"]];
                    }
                    this.$validator.validateAll().then(result => {
                    })
                });
                console.log("data Indicator", data);
                // this.$refs.refModalForm.initForm(data);
            }
            if (method == "Delete") {
                this.$vs.dialog({
                    type: 'confirm',
                    color: 'danger',
                    title: `Confirm`,
                    text: 'តើអ្នកពិតជាចង់ធ្វើការលុបទិន្នន័យនេះជារៀងរហូត?',
                    accept: this.acceptAlert
                })
            }
            if (method == "Indicator") {
                this.$refs.refModalForm.initForm(data);
            }
        },
        acceptAlert() {
            let id = this.dataId;
            let _search_params = {
                pageNum: this.current_page,
                searchFields: []
            }
            return new Promise((resolve, reject) => {
                axios.delete(this.api + '/' + id)
                    .then((response) => {
                        this.$vs.notify({
                            title: 'Message',
                            text: response.data.message,
                            iconPack: 'feather',
                            icon: 'icon-check-circle',
                            color: 'primary',
                            position: 'top-right'
                        })
                        this.getData();
                        // this.$emit('clicked', _search_params);
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

            // return false;
            this.$validator.validateAll().then(result => {
                if (result) {
                    let _data = this.dataFields;
                    // this.$vs.loading();
                    if (this.dataId > 0) {
                        // alert("Edit");
                        let _id = this.dataId;
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
                                    this.showModalForm = false;
                                    this.getData();
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

                                    this.showModalForm = false;
                                    this.getData();
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
        },
        getDataTable(_search_criteria) {
            let _params = {};
            if (_search_criteria.search_field) {
                let _formAttribute = this.formAttributes;
                this.dataFields = [];
                this.formAttributes.forEach(_formAttribute => {
                    if (_search_criteria.search_field[_formAttribute["name"]]) {
                        let _d = {
                            [_formAttribute["name"]]: _search_criteria.search_field[_formAttribute["name"]]
                        }
                        this.dataFields.push(_d);
                    }

                });
                _params = {
                    sort: _search_criteria.sort,
                    order: _search_criteria.order,
                    page_number: _search_criteria.page_number,
                    search_field: this.dataFields,
                };
            } else {
                _params = {
                    sort: _search_criteria.sort,
                    order: _search_criteria.order,
                    page_number: _search_criteria.page_number,
                    search_field: [
                        {
                            "sub_program_id": this.sub_program_id
                        }
                    ]
                };
            }

            return new Promise((resolve, reject) => {
                axios.post(this.api + "/search", _params)
                    .then((response) => {
                        if (response.data) {
                            this.data = response.data;
                        } else {
                            this.data = this.data;
                        }
                        this.$vs.loading.close();
                    }).catch((error) => {
                        // reject(error)
                        this.$vs.loading.close();
                    })
            })
        },
        getData() {
            let _search_criteria = {
                sort: "id",
                order: "",
                page_number: this.dataAttributes.page_number
            }
            this.getDataTable(_search_criteria);
        },
        initTableData(searchQuery) {
            this.$vs.loading();
            let _search_criteria = {
                sort: "id",
                order: "",
                page_number: searchQuery.pageNum,
            }
            this.getDataTable(_search_criteria);
            return false;
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
    watch: {

    }
}
</script>
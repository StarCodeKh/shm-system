<template>
    <vs-popup style="z-index: 52000;" class="d-modal d-modal--xl" classContent="popup-example" :title="$t('បញ្ជីទិន្នន័យ')"
        :active.sync="showModalForm">
        <!-- <form-ceiling ref="refOpenPrivatePopupForm" :api="dataAttributes.api" :dataAttributes="dataAttributes"
            :dataInfo="dataInfo"></form-ceiling> -->
        <d-table-list v-if="showTblList" @clicked="initTableData" :api="api" ref="refInitPage" :allowDel="true" :title="title"
            :dataInfo="dataInfo" :dataAttributes="dataAttributes" :dataHeaders="dataHeaders" :dataTables="data"
            :formAttributes="formAttributes" :rowDisplay="rowDisplay" @clickPrivateForm="initOpenForm"></d-table-list>
    </vs-popup>
</template>

<script>
    import { gridClass } from "@/formGrid.js";
    import axios from "@/axios.js"
    import apiConfig from "@/apiConfig.js"
    // import DForm from '@/views/form-builder/DForm.vue'
    import DFormInput from '@/views/form-builder/form-control/DFormInput.vue'
    import vSelect from 'vue-select'
    import flatPickr from 'vue-flatpickr-component';
    import 'flatpickr/dist/flatpickr.css';
    // import FormCeiling from '@/views/pages/modules/budget-arrangement/budget-ceiling/include/_FormCeilingEntity.vue';
    import DTableList from '@/views/form-builder/DTableList.vue';
    import { ref } from 'vue';
    export default {
        components: {
            // DForm,
            vSelect,
            // FormCeiling,
            DTableList
        },
        props: {
            api: { type: String },
        },
        data() {
            return {
                showTblList: true,
                date: null,
                columnField: "sub_program_id",
                form: {
                    attribute: {}
                },
                showModalForm: false,
                // Data
                title: "ពិដានតាមអង្គភាព",
                // api: apiConfig._apiCeilingEntity,
                dataAttributes: {
                    tableStyle: 3,
                    page_number: 1,
                    offset: 0,
                    dataGrid: "row",
                    popupFullscreen: true,
                    hideSearchBar: false,
                },
                dataHeaders: {
                    header1: {
                        width: 350,
                        label: "បរិយាយមុខសញ្ញា",
                        rowspan: 3,
                        colspan: 0,
                    },
                    header2: {
                        label: "ច្បាប់ក្នុងឆ្នាំ",
                        rowspan: 2,
                        colspan: 0,
                    },
                    header3: {
                        label: "ចំណាយមិនប្រចាំ",
                        rowspan: 2,
                        colspan: 0,
                    },
                    header4: {
                        label: "ចំណាយប្រចាំ",
                        rowspan: 2,
                        colspan: 0,
                    },
                    // header5: {
                    //     label: "ពិដានចំណាយចរន្ត",
                    //     rowspan: 0,
                    //     colspan: 3,
                    // },
                    // header6: {
                    //     label: "អាត្រាកំណើនប្រចាំឆ្នាំ",
                    //     rowspan: 0,
                    //     colspan: 3,
                    // },
                    header7: {
                        label: "សម្គាល់",
                        rowspan: 2,
                        colspan: 0,
                    },
                    header8: {
                        label: "ប៊ូតុងសកម្ម",
                        rowspan: 3,
                        colspan: 0,
                    }
                },
                data: {
                    dataHeaders: {
                        // header1: {
                        //     label: "២០២៣",
                        //     rowspan: 0,
                        //     colspan: 0,
                        // },
                        // header2: {
                        //     label: "២០២៤",
                        //     rowspan: 0,
                        //     colspan: 0,
                        // },
                        // header3: {
                        //     label: "២០២៥",
                        //     rowspan: 0,
                        //     colspan: 0,
                        // },
                        // header4: {
                        //     label: "២០២៣",
                        //     rowspan: 0,
                        //     colspan: 0,
                        // },
                        // header5: {
                        //     label: "២០២៤",
                        //     rowspan: 0,
                        //     colspan: 0,
                        // },
                        // header6: {
                        //     label: "២០២៥",
                        //     rowspan: 0,
                        //     colspan: 0,
                        // },
                        // header7: {
                        //     label: "",
                        //     rowspan: 0,
                        //     colspan: 0,
                        // }
                    },
                    dataSubHeaders: {
                        header1: {
                            label: "1",
                            rowspan: 0,
                            colspan: 0,
                        },
                        header2: {
                            label: "2",
                            rowspan: 0,
                            colspan: 0,
                        },
                        header3: {
                            label: "3",
                            rowspan: 0,
                            colspan: 0,
                        },
                        header4: {
                            label: "4",
                            rowspan: 0,
                            colspan: 0,
                        },
                        // header5: {
                        //     label: "5",
                        //     rowspan: 0,
                        //     colspan: 0,
                        // },
                        // header6: {
                        //     label: "6",
                        //     rowspan: 0,
                        //     colspan: 0,
                        // },
                        // header7: {
                        //     label: "7=(4-1)/1",
                        //     rowspan: 0,
                        //     colspan: 0,
                        // },
                        // header8: {
                        //     label: "8=(5-4)/4",
                        //     rowspan: 0,
                        //     colspan: 0,
                        // },
                        // header9: {
                        //     label: "9=(6-5)/5",
                        //     rowspan: 0,
                        //     colspan: 0,
                        // },
                        // header10: {
                        //     label: "10",
                        //     rowspan: 0,
                        //     colspan: 0,
                        // },
                        // header11: {
                        //     label: "",
                        //     rowspan: 0,
                        //     colspan: 0,
                        // },
                    },
                    data: [
                        {
                            id: 1,
                            name: "ផ្នែកទី១៖ ពិដានចំណាយចរន្ត ឆ្នាំ២០២៣-២០២៥ សរុបរួមក្រសួង (យោងលេខ ០៣ សរណន ចុះថ្ងៃទី៨ ខែមិថុនា ឆ្នាំ២០២២",
                            name_kh: "ផ្នែកទី១៖ ពិដានចំណាយចរន្ត ឆ្នាំ២០២៣-២០២៥ សរុបរួមក្រសួង (យោងលេខ ០៣ សរណន ចុះថ្ងៃទី៨ ខែមិថុនា ឆ្នាំ២០២២",
                            remark: "",
                            status: 1,
                            hasColspan: true,
                            colspan: 12,
                            order_level: "",
                            data: {
                                summary: {
                                    id: 1,
                                    name: "សរុបរួមក្រសួង",
                                    values: ["452,594.0", "6,310.2", "446,283.8", "438,037.0", "449,033.0", "460,503.0", "-3.2 %", "2.5 %", "2.6 %", ""]
                                },
                                children: [
                                    {
                                        id: 1,
                                        name: "I.រដ្ឋបាលកណ្តាល",
                                        values: ["426, 143.0", "6, 310.2", "419, 832.8", "410,031.0", "419, 931.0", "430, 259.0", "- 3.8 %", "2.4 %", "2.5 %", ""],
                                        data: [
                                            {
                                                id: 1,
                                                name: "- បន្ទុកបុគ្គលិក",
                                                values: ["143, 662.0", "264.0", "143, 398.0", "130, 455.0", "136, 978.0", "143, 827.0", "- 9.2 %", "5.0 %", "5.0 %",
                                                    ""]
                                            },
                                            {
                                                id: 2,
                                                name: "- ក្រៅបន្ទុកបុគ្គលិក",
                                                values: ["282, 481.0", "6,046.2", "276, 434.8", "279, 576.0", "282, 953.0", "286, 432.0", "- 1.0", "% 1.2", "% 1.2 %"
                                                    , ""]
                                            }
                                        ],
                                        dataDetails: [
                                            {
                                                id: 1,
                                                name: "១. អនុកម្មវិធីទី១.១ អគ្គនាយកដ្ឋាន​គោលនយោបាយ",
                                                entity: {},
                                                values: ["143, 662.0", "264.0", "143, 398.0", "130, 455.0", "136, 978.0", "143, 827.0", "- 9.2 %", "5.0 %", "5.0 %",
                                                    ""],
                                                data: [
                                                    {
                                                        id: 1,
                                                        name: "- បន្ទុកបុគ្គលិក",
                                                        values: [0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, ""]
                                                    },
                                                    {
                                                        id: 1,
                                                        name: "- ក្រៅបន្ទុកបុគ្គលិក",
                                                        values: ["282, 481.0", "6,046.2", "276, 434.8", "279, 576.0", "282, 953.0", "286, 432.0", "- 1.0", "% 1.2", "% 1.2 %"
                                                            , ""]
                                                    }
                                                ],
                                                ceiling_entities: [
                                                    {
                                                        id: 1,
                                                        name: "- បន្ទុកបុគ្គលិក",
                                                        values: [0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, ""]
                                                    },
                                                    {
                                                        id: 1,
                                                        name: "- ក្រៅបន្ទុកបុគ្គលិក",
                                                        values: ["282, 481.0", "6,046.2", "276, 434.8", "279, 576.0", "282, 953.0", "286, 432.0", "- 1.0", "% 1.2", "% 1.2 %"
                                                            , ""]
                                                    }
                                                ]
                                            },
                                            {
                                                id: 2,
                                                name: "២. អនុកម្មវិធីទី១.២ អគ្គលេខាធិការដ្ឋាន​គណៈកម្មាធិការជាតិ​ផលិតភាពសេដ្ឋកិច្ច​កម្ពុជា",
                                                entity: {},
                                                values: [0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, ""],
                                                data: [
                                                    {
                                                        id: 1,
                                                        name: "- បន្ទុកបុគ្គលិក",
                                                        values: [0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, ""]
                                                    },
                                                    {
                                                        id: 1,
                                                        name: "- ក្រៅបន្ទុកបុគ្គលិក",
                                                        values: [0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, ""]
                                                    }
                                                ]
                                            },
                                        ]
                                    },
                                    {
                                        id: 2,
                                        name: "II.មន្ទីរសេដ្ឋកិច្ច​និងហិរញ្ញវត្ថុ​ រាជធានី ខេត្ត",
                                        values: [0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, ""],
                                        data: [
                                            {
                                                id: 1,
                                                name: "- បន្ទុកបុគ្គលិក",
                                                values: [0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, ""]
                                            },
                                            {
                                                id: 1,
                                                name: "- ក្រៅបន្ទុកបុគ្គលិក",
                                                values: [0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, ""]
                                            }
                                        ],
                                        dataDetails: [
                                            {
                                                id: 1,
                                                name: "១. មន្ទីរសេដ្ឋកិច្ចនិងហិរញ្ញវត្ថុខេត្តបាត់ដំបង",
                                                entity: {},
                                                values: [0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, ""],
                                                data: [
                                                    {
                                                        id: 1,
                                                        name: "- បន្ទុកបុគ្គលិក",
                                                        values: [0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, ""]
                                                    },
                                                    {
                                                        id: 1,
                                                        name: "- ក្រៅបន្ទុកបុគ្គលិក",
                                                        values: [0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, ""]
                                                    }
                                                ]
                                            },
                                            {
                                                id: 2,
                                                name: "២. មន្ទីរសេដ្ឋកិច្ចនិងហិរញ្ញវត្ថុខេត្តបន្ទាយមានជ័យ",
                                                entity: {},
                                                values: [0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, ""],
                                                data: [
                                                    {
                                                        id: 1,
                                                        name: "- បន្ទុកបុគ្គលិក",
                                                        values: [0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, ""]
                                                    },
                                                    {
                                                        id: 1,
                                                        name: "- ក្រៅបន្ទុកបុគ្គលិក",
                                                        values: [0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, ""]
                                                    }
                                                ]
                                            },
                                        ]
                                    },
                                ]
                            }
                        }
                    ],
                    program_id: [],
                    sub_program_id: [],
                    entity_id: [],
                    target_type_id: [
                        {
                            "label": "បន្ទុកបុគ្គលិក",
                            "value": 1,
                        },
                        {
                            "label": "ក្រៅបន្ទុកបុគ្គលិក",
                            "value": 2,
                        }
                    ],
                    ceiling_rule: [
                        {
                            "label": "ពិដានឆ្នាំ២០២៣",
                            "value": "2023"
                        }
                    ],
                    ceiling_exp_group: [
                        {
                            "label": "I. រដ្ឋបាលកណ្ដាល",
                            "value": "I. រដ្ឋបាលកណ្ដាល"
                        },
                        {
                            "label": "II. មន្ទីរសេដ្ឋកិច្ច​និងហិរញ្ញវត្ថុ​ រាជធានី ខេត្ត",
                            "value": "II. មន្ទីរសេដ្ឋកិច្ច​និងហិរញ្ញវត្ថុ​ រាជធានី ខេត្ត"
                        }
                    ],
                    limit: 10,
                    total: 3,
                },
                formAttributes: [
                    {
                        name: "program_id",
                        type: "select",
                        required: true,
                        hasFilter: true,
                        filterObj: "sub_program_id",
                        api: apiConfig._apiSubProgramByPro,
                        options: [],
                    },
                    {
                        name: "sub_program_id",
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
                    {
                        name: "ceiling_exp_group", // ប្រភេទមុខសញ្ញា
                        type: "select",
                        required: true,
                        options: [],
                    },
                    {
                        name: "ceiling_exp_type", // ប្រភេទមុខសញ្ញា
                        type: "select",
                        required: true,
                        options: [],
                    },
                    {
                        name: "remark", // អត្រាកំណើនប្រចាំឆ្នាំ
                        type: "textarea",
                        required: false
                    },
                    // {
                    //     name: "ceiling_rule", //ច្បាប់ពិដាន
                    //     type: "number",
                    //     required: true,
                    // },
                    // {
                    //     name: "reg_expense", // ចំណាយប្រចាំ
                    //     type: "number",
                    //     required: false,
                    // },
                    // {
                    //     name: "non_reg_expense", // ចំណាយមិនប្រចាំ
                    //     type: "number",
                    //     required: false
                    // },
                    // {
                    //     name: "ceiling_tran_year_0", // ពិដានចំណាយចរន្ត
                    //     type: "number",
                    //     required: false
                    // },
                    // {
                    //     name: "ceiling_tran_year_1", // ពិដានចំណាយចរន្ត
                    //     type: "number",
                    //     required: false
                    // },
                    // {
                    //     name: "ceiling_tran_year_2", // ពិដានចំណាយចរន្ត
                    //     type: "number",
                    //     required: false
                    // },
                    // {
                    //     name: "increase_rate_year_0", // អត្រាកំណើនប្រចាំឆ្នាំ
                    //     type: "number",
                    //     required: false
                    // },
                    // {
                    //     name: "increase_rate_year_1", // អត្រាកំណើនប្រចាំឆ្នាំ
                    //     type: "number",
                    //     required: false
                    // },
                    // {
                    //     name: "increase_rate_year_2", // អត្រាកំណើនប្រចាំឆ្នាំ
                    //     type: "number",
                    //     required: false
                    // },

                    // {
                    //     name: "increase_rate_year_2", // អត្រាកំណើនប្រចាំឆ្នាំ
                    //     type: "number",
                    //     required: false
                    // },
                    {
                        name: "បន្ទុកបុគ្គលិក", // អត្រាកំណើនប្រចាំឆ្នាំ
                        type: "grid",
                        required: false
                    },
                    {
                        name: "staff_exp_ceiling_rule", //ច្បាប់ពិដាន
                        type: "number",
                        required: true,
                    },
                    {
                        name: "staff_exp_reg_expense", // ចំណាយប្រចាំ
                        type: "number",
                        required: false,
                    },
                    {
                        name: "staff_exp_non_reg_expense", // ចំណាយមិនប្រចាំ
                        type: "number",
                        required: false
                    },
                    {
                        name: "ក្រៅបន្ទុកបុគ្គលិក", // អត្រាកំណើនប្រចាំឆ្នាំ
                        type: "grid",
                        required: false
                    },
                    {
                        name: "out_staff_exp_ceiling_rule", //ច្បាប់ពិដាន
                        type: "number",
                        required: true,
                    },
                    {
                        name: "out_staff_exp_reg_expense", // ចំណាយប្រចាំ
                        type: "number",
                        required: false,
                    },
                    {
                        name: "out_staff_exp_non_reg_expense", // ចំណាយមិនប្រចាំ
                        type: "number",
                        required: false
                    },
                    {
                        name: "ព័ត៌មានបន្ថែម", // អត្រាកំណើនប្រចាំឆ្នាំ
                        type: "grid",
                        required: false
                    },
                    {
                        name: "remark", // អត្រាកំណើនប្រចាំឆ្នាំ
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
                this.showTblList = true;
                this.dataInfo = data;
                if(this.showTblList){
                    
                    let _params = {
                        sort: "",
                        order: "",
                        page_number: 1,
                        search_field: [
                            // This ID will belong to the parent ID of this KPI
                            // data
                        ],
                        data_info: this.dataInfo
                    }

                    this.$vs.notify({
                        title: 'Message',
                        text: "កំពុងទាញទិន្នន័យ...",
                        iconPack: 'feather',
                        icon: 'icon-check-circle',
                        color: 'primary',
                        position: 'top-right'
                    })

                    this.showModalForm = true;
                    return new Promise((resolve, reject) => {
                        axios.post(this.api + "/search", _params)
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
                                    this.data = response.data;
                                } else {
                                    this.data = this.data;
                                }
                                this.showModalForm = true;
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
            },
            getDataTable(_search_criteria) {
                if(this.showTblList){
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
                            data_info: _search_criteria.dataInfo
                        };
                    } else {
                        _params = {
                            sort: _search_criteria.sort,
                            order: _search_criteria.order,
                            page_number: _search_criteria.page_number,
                        };
                    }

                    // this.data = this.data;
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
                }
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
                    search_field: searchQuery.searchFields,
                    dataInfo: searchQuery.dataInfo
                }
                this.getDataTable(_search_criteria);
                return false;
            },
            initOpenForm(data) {
                this.$refs.refOpenPrivatePopupForm.showForm(data);
            }
        },
        created() {
            // this.$vs.loading();
            this.getData();
            this.styleClass = gridClass(this.rowDisplay);
        },
        computed: {
            isSmallerScreen() {
                return this.$store.state.windowWidth < 768
            }
        },
    }
</script>
<template>
    <div id="table-demo">
        <h2>គម្រោងវិនិយោគសាធារណៈ</h2>
        <div class="vx-row">
            <!-- {{dataInfo | json}} -->
            <div class="vx-col lg: w - 1 / 2 w - full">
                <!-- Form Input Text -->
                <div class="mt-4">
                    <label class="mb-2">{{ $t("tet") }}</label>
                    <vs-input size="small" class="w-full" />
                </div>
            </div>

            <div class="vx-col lg: w - 1 / 2 w - full">
                <!-- Form Input Text -->
                <div class="mt-4">
                    <label class="mb-2">{{ $t("tet") }}</label>
                    <vs-input size="small" class="w-full" />
                </div>
            </div>

            <div class="vx-col lg: w - 1 / 2 w - full">
                <!-- Form Input Text -->
                <div class="mt-4">
                    <label class="mb-2">{{ $t("tet") }}</label>
                    <vs-input size="small" class="w-full" />
                </div>
            </div>
        </div>

        <div class="vx-row">
            <vs-alert icon="warning" active="true" color="primary" class="mt-5">
                <span>ចំណាយប៉ាន់ស្មានសរុប</span>
            </vs-alert>

            <!-- {{dataInfo | json}} -->
            <div class="vx-col lg: w - 1 / 2 w - full">
                <!-- Form Input Text -->
                <div class="mt-4">
                    <label class="mb-2">{{ $t("២០២៣") }}</label>
                    <vs-input size="small" class="w-full" />
                </div>
            </div>

            <div class="vx-col lg: w - 1 / 2 w - full">
                <!-- Form Input Text -->
                <div class="mt-4">
                    <label class="mb-2">{{ $t("២០២៤") }}</label>
                    <vs-input size="small" class="w-full" />
                </div>
            </div>

            <div class="vx-col lg: w - 1 / 2 w - full">
                <!-- Form Input Text -->
                <div class="mt-4">
                    <label class="mb-2">{{ $t("២០២៥") }}</label>
                    <vs-input size="small" class="w-full" />
                </div>
            </div>
        </div>

        <div class="vx-row">
            <vs-alert icon="warning" active="true" color="primary" class="mt-5">
                <span>ហិរញ្ញប្បទានសន្យាផ្តល់</span>
            </vs-alert>

            <!-- {{dataInfo | json}} -->
            <div class="vx-col lg: w - 1 / 2 w - full">
                <!-- Form Input Text -->
                <div class="mt-4">
                    <label class="mb-2">{{ $t("២០២៣") }}</label>
                    <vs-input size="small" class="w-full" />
                </div>
            </div>

            <div class="vx-col lg: w - 1 / 2 w - full">
                <!-- Form Input Text -->
                <div class="mt-4">
                    <label class="mb-2">{{ $t("២០២៤") }}</label>
                    <vs-input size="small" class="w-full" />
                </div>
            </div>

            <div class="vx-col lg: w - 1 / 2 w - full">
                <!-- Form Input Text -->
                <div class="mt-4">
                    <label class="mb-2">{{ $t("២០២៥") }}</label>
                    <vs-input size="small" class="w-full" />
                </div>
            </div>
        </div>

        <div class="vx-row">
            <vs-alert icon="warning" active="true" color="primary" class="mt-5">
                <span>តម្រូវការហិរញ្ញប្បទានបំពេញបន្ថែម</span>
            </vs-alert>

            <!-- {{dataInfo | json}} -->
            <div class="vx-col lg: w - 1 / 2 w - full">
                <!-- Form Input Text -->
                <div class="mt-4">
                    <label class="mb-2">{{ $t("២០២៣") }}</label>
                    <vs-input size="small" class="w-full" />
                </div>
            </div>

            <div class="vx-col lg: w - 1 / 2 w - full">
                <!-- Form Input Text -->
                <div class="mt-4">
                    <label class="mb-2">{{ $t("២០២៤") }}</label>
                    <vs-input size="small" class="w-full" />
                </div>
            </div>

            <div class="vx-col lg: w - 1 / 2 w - full">
                <!-- Form Input Text -->
                <div class="mt-4">
                    <label class="mb-2">{{ $t("២០២៥") }}</label>
                    <vs-input size="small" class="w-full" />
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import axios from "@/axios.js"
import apiConfig from "@/apiConfig.js"
import { ref } from 'vue';
import FormPIP from '@/views/pages/modules/budget-arrangement/pip/_form_pip.vue'
import DTableList from '@/views/form-builder/DTableList.vue'

export default {
    data() {
        return {
            title: "pip_form_title",
            api: apiConfig._apiPIP,
            dataAttributes: {
                tableStyle: 3,
                customizeForm: true,
                popupFullscreen: true,
                page_number: 1,
                offset: 0,
                dataGrid: "row",
                actionButton: [
                    {
                        icon: "EditIcon",
                        path: "/module/budget-arrangement/budget-ceiling/list",
                        method: "Edit"
                    },
                    {
                        icon: "TrashIcon",
                        path: "/module/budget-arrangement/budget-ceiling/list",
                        method: "Delete"
                    }
                ]
            },
            dataHeaders: {
                header11: {
                    width: 350,
                    label: "program_id",
                    rowspan: 2,
                    colspan: 0,
                },
                header1: {
                    width: 350,
                    label: "sub_program_id",
                    // label: "ឈ្មោះកម្មវិធី/ ឈ្មោះអនុកម្មវិធី /ឈ្មោះគម្រោង",
                    rowspan: 2,
                    colspan: 0,
                },
                header2: {
                    label: "pip_no",
                    rowspan: 2,
                    colspan: 0,
                },
                header3: {
                    label: "project_status_id",
                    // label: "ស្ថានភាពគម្រោង",
                    rowspan: 2,
                    colspan: 0,
                },
                header4: {
                    label: "project_type_id",
                    rowspan: 2,
                    colspan: 0,
                },
                header5: {
                    label: "ចំណាត់ថ្នាក់ប៉ាន់ស្ថានសរុប",
                    rowspan: 0,
                    colspan: 4,
                },
                header6: {
                    label: "ហិរញ្ញប្បទានសន្យាផ្តល់",
                    rowspan: 0,
                    colspan: 4,
                },
                header7: {
                    label: "តម្រូវការហិរញ្ញប្បទានបំពេញបន្ថែម",
                    rowspan: 0,
                    colspan: 4,
                },
                header8: {
                    label: "ប្រភពហិរញ្ញប្បទាន",
                    rowspan: 2,
                    colspan: 0,
                },
                header9: {
                    label: "សម្គាល់",
                    rowspan: 2,
                    colspan: 0,
                },
                header10: {
                    width: 80,
                    label: "ប៊ូតុងសកម្ម",
                    rowspan: 2,
                    colspan: 0,
                }
            },
            data: {
                dataHeaders: {
                    header1: {
                        label: "២០២៣",
                        rowspan: 0,
                        colspan: 0,
                    },
                    header2: {
                        label: "២០២៤",
                        rowspan: 0,
                        colspan: 0,
                    },
                    header3: {
                        label: "២០២៥",
                        rowspan: 0,
                        colspan: 0,
                    },
                    header4: {
                        label: "សរុប៣ឆ្នាំ",
                        rowspan: 0,
                        colspan: 0,
                    },
                    header5: {
                        label: "២០២៣",
                        rowspan: 0,
                        colspan: 0,
                    },
                    header6: {
                        label: "២០២៤",
                        rowspan: 0,
                        colspan: 0,
                    },
                    header7: {
                        label: "២០២៥",
                        rowspan: 0,
                        colspan: 0,
                    },
                    header8: {
                        label: "សរុប៣ឆ្នាំ",
                        rowspan: 0,
                        colspan: 0,
                    },
                    header9: {
                        label: "២០២៣",
                        rowspan: 0,
                        colspan: 0,
                    },
                    header10: {
                        label: "២០២៤",
                        rowspan: 0,
                        colspan: 0,
                    },
                    header11: {
                        label: "២០២៥",
                        rowspan: 0,
                        colspan: 0,
                    },
                    header12: {
                        label: "សរុប៣ឆ្នាំ",
                        rowspan: 0,
                        colspan: 0,
                    },
                },
                dataSubHeaders: {
                },
                data: [
                    {
                        id: 1,
                        name: "សរុបរួមក្រសួង​សេដ្ឋ​កិច្ច​និង​ហិរញ្ញវត្ថ",
                        name_kh: "សរុបរួមក្រសួង​សេដ្ឋ​កិច្ច​និង​ហិរញ្ញវត្ថ",
                        remark: "",
                        status: 1,
                        order_level: "",
                        hasColspan: false,
                        values: [0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, "", ""],
                        data: {
                            summary: {
                                id: 1,
                                name: "កម្មវិធីទី១៖ ការគ្រប់គ្រងចំណូលសាធារណៈ និងទ្រព្យសម្បត្តិរដ្ឋ",
                                values: [0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, "", ""],
                            },
                            children: [
                                {
                                    id: 1,
                                    name: "អនុកម្មវិធី ៣.១ ៖ អគ្គនាយកដ្ឋានគយ និងរដ្ឋាករកម្ពុជា",
                                    values: [0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, "", ""],
                                    data: [

                                    ],
                                    dataDetails: [
                                        {
                                            id: 1,
                                            name: "១២២. គម្រោងសាងសង់អគារសាខាពន្ធដារខណ្ឌចំនួន០១ និងអគារការិយាល័យពន្ធដារស្រុកស្ទឹងហាវ ០១",
                                            entity: {},
                                            values: ["659", "គម្រោងស្នើសុំថ្មី", "វិនិយោគជាទុន", 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, "", ""],
                                            data: [

                                            ]
                                        },
                                        {
                                            id: 2,
                                            name: "២. គម្រោងជួសជុលនិងកែលម្អអគារសាខាពន្ធដារខណ្ឌចំនួន០១ ការិយាល័យពន្ធដារក្រុងចំនួន០១ និងស្រុកចំនួន ០១  ",
                                            entity: {},
                                            values: [0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, "", ""],
                                            data: [

                                            ]
                                        },
                                    ]
                                },
                                {
                                    id: 1,
                                    name: "អនុកម្មវិធី ៣.២ ៖ អគ្គនាយកដ្ឋានពន្ធដារ",
                                    values: [0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, "", ""],
                                    data: [

                                    ],
                                    dataDetails: [
                                        {
                                            id: 1,
                                            name: "១. គម្រោងសាងសង់អគារសាខាពន្ធដារខណ្ឌចំនួន០១ និងអគារការិយាល័យពន្ធដារស្រុកស្ទឹងហាវ ០១",
                                            entity: {},
                                            values: [0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, "", ""],
                                            data: [

                                            ]
                                        },
                                        {
                                            id: 2,
                                            name: "២. គម្រោងជួសជុលនិងកែលម្អអគារសាខាពន្ធដារខណ្ឌចំនួន០១ ការិយាល័យពន្ធដារក្រុងចំនួន០១ និងស្រុកចំនួន ០១  ",
                                            entity: {},
                                            values: [0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, "", ""],
                                            data: [

                                            ]
                                        },
                                    ]
                                },
                            ]
                        }
                    }
                ],
                project_type_id: [],
                project_status_id: [],
                sub_program_id: [],
                entity_id: [],
                limit: 10,
                total: 3,
            },
            formAttributes: [
                {
                    name: "entity_id",
                    type: "select",
                    options: [],
                    required: true
                },
                // {
                //   name: "program_id",
                //   type: "select_autocomplete",
                //   required: true,
                //   // hasDefault: true,
                //   // defaultOptions: {
                //   //   label: "គោលបំណងទី១",
                //   //   value: 1,
                //   // },
                //   options: [],
                // },
                {
                    name: "sub_program_id",
                    type: "select",
                    required: true,
                    // hasDefault: true,
                    // defaultOptions: {
                    //   label: "គោលបំណងទី១",
                    //   value: 1,
                    // },
                    options: [],
                },
                {
                    name: "pip_code",
                    type: "text",
                    required: true
                },
                {
                    name: "project_type_id",
                    type: "select",
                    required: true,
                    options: [],
                },
                {
                    name: "project_status_id",
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
                    name: "pip_source_funding",
                    type: "checkbox",
                    required: false,
                    attributes: [
                        {
                            name: "ថវិកាជាតិ",
                            value: "1"
                        },
                        {
                            name: "ជំនួយឥតសំណង",
                            value: "2"
                        },
                        {
                            name: "ឥណទានមិនមែនសម្បទាន",
                            value: "3"
                        }
                    ]
                },

                {
                    name: "មុំនៃគោលនយោបាយក្នុងយុទ្ធសាស្រ្តចតុកោណ ដំណាក់កាលទី៤ ដែលគម្រោងស្ថិតនៅ៖ ",
                    type: "textarea",
                    required: false
                },
                {
                    name: "ការចូលរួមចំណែករបស់គម្រោងក្នុងការសម្រេចបានគោលនយោបាយអាទិភាពខាងលើ ",
                    type: "textarea",
                    required: false
                }, {
                    name: "វិស័យ (សូមប្រើវិស័យ អនុវិស័យ ដែលមានចែងក្នុង ផ.យ.អ.ជ. ២០១៩-២០២៣) ",
                    type: "textarea",
                    required: false
                }, {
                    name: "ទីតាំងអនុវត្តគម្រោង (ពិពណ៌នាពីទីតាំងដែលត្រូវអនុវត្តគម្រោង- ឈ្មោះខេត្ត)",
                    type: "textarea",
                    required: false
                }, {
                    name: "គោលបំណងរបស់គម្រោង (ពិពណ៌នាគោលបំណងចម្បងៗរបស់គម្រោង)",
                    type: "textarea",
                    required: false
                }, {
                    name: "ការពិពណ៌នាអំពីគម្រោង (ពិពណ៌នាអំពីសមាសភាគទាំងអស់របស់គម្រោង)",
                    type: "textarea",
                    required: false
                }, {
                    name: "យុត្តិកម្មលើគម្រោង (សូមផ្តល់ហេតុផលដែលគម្រោងនេះត្រូវបានចាត់ទុកថាមានប្រយោជន៍)",
                    type: "textarea",
                    required: false
                }, {
                    name: "ផលប្រយោជន៍របស់គម្រោង (រៀបរាប់ពីអ្នកដែលនឹងទទួលប្រយោជន៍ផ្ទាល់និងមិនផ្ទាល់ពីការអនុវត្តគម្រោង)",
                    type: "textarea",
                    required: false
                }, {
                    name: "ផលប៉ះពាល់ផ្នែកបរិស្ថាននិងសង្គម (ពណ៌នាសង្ខេបពីផលប៉ះពាល់របស់គម្រោងលើប្រជាជននិងបរិស្ថានជុំវិញ ប្រសិនបើមាន។ តើគម្រោងនេះនឹងជួយដល់ការកាត់បន្ថយភាពក្រីក្រដែរឬទេ?)",
                    type: "textarea",
                    required: false
                }, {
                    name: "ការវិភាគយេនឌ័រ (តើគម្រោងនេះមានផលប៉ះពាល់ដល់តួនាទីរបស់បុរសនិងស្រ្តីក្នុងទីតាំងរបស់គម្រោងនេះដូចម្តេច? តើស្រ្តីនឹងត្រូវចូលរួមយ៉ាងសកម្មក្នុងការអនុវត្តគម្រោងនេះដែរឬទេ?)",
                    type: "textarea",
                    required: false
                }, {
                    name: "សមត្ថភាពអនុវត្តគម្រោង (តើក្រសួងមានជំនាញនិងបទពិសោធន៍គ្រប់គ្រាន់សម្រាប់អនុវត្តគម្រោងដែរឬទេ?)",
                    type: "textarea",
                    required: false
                }, {
                    name: "ស្ថានភាពនៃការអនុវត្តគម្រោង (ពិពណ៌នាសង្ខេបអំពីវឌ្ឍនភាពរបស់គម្រោងនិងបង្ហាញអំពីភាពប្រឈមនានាដែលបណ្តាលឲ្យមានការពន្យារពេលក្នុងការអនុវត្តគម្រោង។)",
                    type: "textarea",
                    required: false
                }, {
                    name: "ការចូលរួមរបស់ដៃគូអភិវឌ្ឍ (ផ្តល់ព័ត៌មានអំពីដៃគូអភិវឌ្ឍសក្តានុពល ដែលពាក់ព័ន្ធនឹងគម្រោងនេះ)",
                    type: "textarea",
                    required: false
                },
            ],
            rowDisplay: "2grid", //1grid, 2grid, 3grid, 4grid
            dataFields: [],
            dataInfo: {}
        }
    },
    components: {
        DTableList,
        FormPIP
    },
    methods: {
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
                search_field: searchQuery.searchFields
            }
            this.getDataTable(_search_criteria);
            return false;
        }

    },
    created() {
        this.$vs.loading();
        this.getData();
    },
    watch: {
    }
}
</script>

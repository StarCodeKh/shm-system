<template>
    <vx-card :title="''">
        <div class="p-3 mb-4" style="overflow: scroll;">
            <vs-table style="min-width: 500px;min-width: 200%;">
                <template slot="thead" style="background: #28c76f;">
                    <vs-th>{{ $t("ល.រ") }}</vs-th>
                    <vs-th>{{ $t("ឈ្មោះចំណាត់ថ្នាក់ចំណូល") }}</vs-th>
                    <vs-th>{{ $t("ច្បាប់ហិរញ្ញវត្ថុ") }}</vs-th>
                    <vs-th v-html="$t('ទឹកប្រាក់<small>(សរុបគម្រោងកម្មវិធីប្រចាំឆ្នាំ)</small>')"></vs-th>
                    <vs-th>{{ $t("%ច្បាប់") }}</vs-th>
                    <vs-th>{{ $t("គ្រោងចំណូលតាម ត្រីមាសទី១") }}</vs-th>
                    <vs-th>{{ $t("គ្រោងចំណូលតាម ត្រីមាសទី២") }}</vs-th>
                    <vs-th>{{ $t("គ្រោងចំណូលតាម ត្រីមាសទី៣") }}</vs-th>
                    <vs-th>{{ $t("គ្រោងចំណូលតាម ត្រីមាសទី៤") }}</vs-th>
                    <vs-th>{{ $t("ផ្សេងៗ") }}</vs-th>
                    <vs-th>{{ $t("Action") }}</vs-th>
                </template>
                <tbody>
                    <!-- <vs-tr style="background-color: rgb(85 255 161)">
                        <td colspan="11"><b>70-ចំណូលសារពើពន្ធក្នុងស្រុក</b></td>
                    </vs-tr>
                    <vs-tr style="background-color: #b2ffd9;">
                        <td colspan="11"><b>7001-ពន្ធផ្ទាល់ (ពន្ធលើផលទុន ប្រាក់ចំណូល)</b></td>
                    </vs-tr> -->
                    <vs-tr>
                        <vs-td><b>{{ "70025" }}</b></vs-td>
                        <vs-td style="width: 400px">{{ "អាករលើតម្លៃបន្ថែម របបនាំចូល (សហគ្រាសប្រេងឥន្ធនៈ)" }}</vs-td>
                        <td style="width: 200px">
                            <vs-input type="number" v-validate="'required'" size="small" class="w-full" />
                        </td>
                        <td style="width: 200px">
                            <vs-input type="number" v-validate="'required'" size="small" class="w-full" />
                        </td>
                        <td style="width: 200px">
                            <vs-input type="number" v-validate="'required'" size="small" class="w-full" />
                        </td>
                        <td style="width: 1500px">
                            <div style="background-color: rgb(178 255 217);">
                                <center>ត្រីមាសទី១</center>
                            </div>
                            <div class="pb-3 pt-3"><vx-card>
                                    <center>គ្រោងដើមឆ្នាំ</center>
                                </vx-card></div>
                            <div class="vx-row">
                                <div class="vx-col md:w-1/1 w-full">
                                    <div class="vx-row">
                                        <div class="vx-col sm:w-1/3 w-full">
                                            ខែ១<vs-input type="number" class="w-full" />
                                        </div>
                                        <div class="vx-col sm:w-1/3 w-full">
                                            ខែ២<vs-input type="number" class="w-full" />
                                        </div>
                                        <div class="vx-col sm:w-1/3 w-full">
                                            ខែ៣<vs-input type="number" class="w-full" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- <div class="pb-3 pt-3">
                                <center>ប៉ាន់ស្មានអនុវត្ត</center>
                            </div>
                            <div class="vx-row">
                                <div class="vx-col md:w-1/1 w-full">
                                    <div class="vx-row">
                                        <div class="vx-col sm:w-1/3 w-full">
                                            អនុ.ខែ១<vs-input type="number" class="w-full" />
                                        </div>
                                        <div class="vx-col sm:w-1/3 w-full">
                                            អនុ.ខែ២<vs-input type="number" class="w-full" />
                                        </div>
                                        <div class="vx-col sm:w-1/3 w-full">
                                            អនុ.ខែ៣<vs-input type="number" class="w-full" />
                                        </div>
                                    </div>
                                </div>
                            </div> -->
                        </td>
                        <td style="width: 1500px">
                            <div style="background-color: rgb(178 255 217);">
                                <center>ត្រីមាសទី២</center>
                            </div>
                            <div class="pb-3 pt-3"><vx-card>
                                    <center>គ្រោងដើមឆ្នាំ</center>
                                </vx-card></div>
                            <div class="vx-row">
                                <div class="vx-col md:w-1/1 w-full">
                                    <div class="vx-row">
                                        <div class="vx-col sm:w-1/3 w-full">
                                            ខែ៤<vs-input type="number" class="w-full" />
                                        </div>
                                        <div class="vx-col sm:w-1/3 w-full">
                                            ខែ៥<vs-input type="number" class="w-full" />
                                        </div>
                                        <div class="vx-col sm:w-1/3 w-full">
                                            ខែ៦<vs-input type="number" class="w-full" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- <div class="pb-3 pt-3">
                                <center>ស្នើសុំកែតម្រូវ</center>
                            </div>
                            <div class="vx-row">
                                <div class="vx-col md:w-1/1 w-full">
                                    <div class="vx-row">
                                        <div class="vx-col sm:w-1/3 w-full">
                                            អនុ.ខែ៤<vs-input type="number" class="w-full" />
                                        </div>
                                        <div class="vx-col sm:w-1/3 w-full">
                                            អនុ.ខែ៥<vs-input type="number" class="w-full" />
                                        </div>
                                        <div class="vx-col sm:w-1/3 w-full">
                                            អនុ.ខែ៦<vs-input type="number" class="w-full" />
                                        </div>
                                    </div>
                                </div>
                            </div> -->
                        </td>
                        <td style="width: 1500px">
                            <div style="background-color: rgb(178 255 217);">
                                <center>ត្រីមាសទី៣</center>
                            </div>
                            <div class="pb-3 pt-3"><vx-card>
                                    <center>គ្រោងដើមឆ្នាំ</center>
                                </vx-card></div>
                            <div class="vx-row">
                                <div class="vx-col md:w-1/1 w-full">
                                    <div class="vx-row">
                                        <div class="vx-col sm:w-1/3 w-full">
                                            ខែ៧<vs-input type="number" class="w-full" />
                                        </div>
                                        <div class="vx-col sm:w-1/3 w-full">
                                            ខែ៨<vs-input type="number" class="w-full" />
                                        </div>
                                        <div class="vx-col sm:w-1/3 w-full">
                                            ខែ៩<vs-input type="number" class="w-full" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- <div class="pb-3 pt-3">
                                <center>ស្នើសុំកែតម្រូវ</center>
                            </div>
                            <div class="vx-row">
                                <div class="vx-col md:w-1/1 w-full">
                                    <div class="vx-row">
                                        <div class="vx-col sm:w-1/3 w-full">
                                            អនុ.ខែ៦<vs-input type="number" class="w-full" />
                                        </div>
                                        <div class="vx-col sm:w-1/3 w-full">
                                            អនុ.ខែ៧<vs-input type="number" class="w-full" />
                                        </div>
                                        <div class="vx-col sm:w-1/3 w-full">
                                            អនុ.ខែ៨<vs-input type="number" class="w-full" />
                                        </div>
                                    </div>
                                </div>
                            </div> -->
                        </td>
                        <td style="width: 1500px">
                            <div style="background-color: rgb(178 255 217);">
                                <center>ត្រីមាសទី៤</center>
                            </div>
                            <div class="pb-3 pt-3"><vx-card>
                                    <center>គ្រោងដើមឆ្នាំ</center>
                                </vx-card></div>
                            <div class="vx-row">
                                <div class="vx-col md:w-1/1 w-full">
                                    <div class="vx-row">
                                        <div class="vx-col sm:w-1/3 w-full">
                                            ខែ១០<vs-input type="number" class="w-full" />
                                        </div>
                                        <div class="vx-col sm:w-1/3 w-full">
                                            ខែ១១<vs-input type="number" class="w-full" />
                                        </div>
                                        <div class="vx-col sm:w-1/3 w-full">
                                            ខែ១២<vs-input type="number" class="w-full" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- <div class="pb-3 pt-3">
                                <center>ស្នើសុំកែតម្រូវ</center>
                            </div>
                            <div class="vx-row">
                                <div class="vx-col md:w-1/1 w-full">
                                    <div class="vx-row">
                                        <div class="vx-col sm:w-1/3 w-full">
                                            អនុ.ខែ១០<vs-input type="number" class="w-full" />
                                        </div>
                                        <div class="vx-col sm:w-1/3 w-full">
                                            អនុ.ខែ១១<vs-input type="number" class="w-full" />
                                        </div>
                                        <div class="vx-col sm:w-1/3 w-full">
                                            អនុ.ខែ១២<vs-input type="number" class="w-full" />
                                        </div>
                                    </div>
                                </div>
                            </div> -->
                        </td>
                        <td style="width: 600px">
                            <vs-input v-validate="'required'" size="small" class="mb-2 pb-2 w-full" />
                        </td>
                        <td></td>
                    </vs-tr>
                </tbody>
            </vs-table>
        </div>

        <div class="my-5">
            <!-- <vs-pagination :total="calPaginNumber(dataTables.total / dataTables.limit)" v-model="current_page"></vs-pagination> -->
        </div>

    </vx-card>
</template>

<script>

import axios from "@/axios.js";
import { stringify } from 'querystring';
import { ref } from 'vue';
export default {
    data() {
        return {
            // title: "budget_monitor_income_program",
            // api: '',//apiConfig._apiObjective,
            // dataAttributes: {
            //     tableStyle: 6,
            //     page_number: 1,
            //     offset: 0,
            //     dataGrid: "row",
            //     hasHeadingReport: false,
            //     headingReport: "",
            //     popupFullscreen: true,
            //     hideSearchBar: true,
            //     enableToggleForm: true,// if allow to show form by toggling but no popup
            //     hasChildTable: true
            // },
            // dataHeaders: {
            //     header1: {
            //         width: 150,
            //         label: "account_group_id",
            //         rowspan: 3,
            //         colspan: 0,
            //     },
            //     header2: {
            //         label: "account_id",
            //         rowspan: 3,
            //         colspan: 0,
            //     },
            //     header3: {
            //         label: "sub_account_id",
            //         rowspan: 3,
            //         colspan: 0,
            //     },
            //     header4: {
            //         label: "line_ministry",
            //         rowspan: 3,
            //         colspan: 0,
            //     },
            //     header5: {
            //         label: "finance_rule",
            //         rowspan: 3,
            //         colspan: 0,
            //     },
            //     header6: {
            //         label: "total_annual_program_project",
            //         rowspan: 2,
            //         colspan: 2,
            //     },
            //     header7: {
            //         label: "semester1",
            //         rowspan: 0,
            //         colspan: 8,
            //     },
            //     header8: {
            //         label: "semester2",
            //         rowspan: 0,
            //         colspan: 8,
            //     },
            //     header9: {
            //         label: "semester3",
            //         rowspan: 0,
            //         colspan: 8,
            //     },
            //     header10: {
            //         label: "semester4",
            //         rowspan: 0,
            //         colspan: 8,
            //     },
            //     header11: {
            //         label: "other",
            //         rowspan: 3,
            //         colspan: 0,
            //     }
            // },
            // data: {
            //     dataHeaders: {
            //         header1: {
            //             label: "គ្រោងដើមឆ្នាំ",
            //             rowspan: 0,
            //             colspan: 4,
            //         },
            //         header2: {
            //             label: "ប៉ាន់ស្មានអនុវត្ត",
            //             rowspan: 0,
            //             colspan: 4,
            //         },
            //         header3: {
            //             label: "គ្រោងដើមឆ្នាំ",
            //             rowspan: 0,
            //             colspan: 4,
            //         },
            //         header4: {
            //             label: "ប៉ាន់ស្មានអនុវត្ត",
            //             rowspan: 0,
            //             colspan: 4,
            //         },
            //         header5: {
            //             label: "គ្រោងដើមឆ្នាំ",
            //             rowspan: 0,
            //             colspan: 4,
            //         },
            //         header6: {
            //             label: "ប៉ាន់ស្មានអនុវត្ត",
            //             rowspan: 0,
            //             colspan: 4,
            //         },
            //         header7: {
            //             label: "គ្រោងដើមឆ្នាំ",
            //             rowspan: 0,
            //             colspan: 4,
            //         },
            //         header8: {
            //             label: "ប៉ាន់ស្មានអនុវត្ត",
            //             rowspan: 0,
            //             colspan: 4,
            //         }
            //     },
            //     dataSubHeaders: {
            //         header1: {
            //             label: "ទឹកប្រាក់",
            //             rowspan: 0,
            //             colspan: 0,
            //         },
            //         header2: {
            //             label: "%ច្បាប់",
            //             rowspan: 0,
            //             colspan: 0,
            //         },

            //         header3: {
            //             label: "សរុប",
            //             rowspan: 0,
            //             colspan: 0,
            //         },
            //         header4: {
            //             label: "ខែទី១",
            //             rowspan: 0,
            //             colspan: 0,
            //         },
            //         header5: {
            //             label: "ខែទី២",
            //             rowspan: 0,
            //             colspan: 0,
            //         },
            //         header6: {
            //             label: "ខែទី៣",
            //             rowspan: 0,
            //             colspan: 0,
            //         },
            //         header7: {
            //             label: "សរុប",
            //             rowspan: 0,
            //             colspan: 0,
            //         },
            //         header8: {
            //             label: "អនុ.ខែទី១",
            //             rowspan: 0,
            //             colspan: 0,
            //         },
            //         header9: {
            //             label: "អនុ.ខែទី២",
            //             rowspan: 0,
            //             colspan: 0,
            //         },
            //         header10: {
            //             label: "អនុ.ខែទី៣",
            //             rowspan: 0,
            //             colspan: 0,
            //         },

            //         header11: {
            //             label: "សរុប",
            //             rowspan: 0,
            //             colspan: 0,
            //         },
            //         header12: {
            //             label: "ខែទី៤",
            //             rowspan: 0,
            //             colspan: 0,
            //         },
            //         header13: {
            //             label: "ខែទី៥",
            //             rowspan: 0,
            //             colspan: 0,
            //         },
            //         header14: {
            //             label: "ខែទី៦",
            //             rowspan: 0,
            //             colspan: 0,
            //         },
            //         header15: {
            //             label: "សរុប",
            //             rowspan: 0,
            //             colspan: 0,
            //         },
            //         header16: {
            //             label: "អនុ.ខែទី៤",
            //             rowspan: 0,
            //             colspan: 0,
            //         },
            //         header17: {
            //             label: "អនុ.ខែទី៥",
            //             rowspan: 0,
            //             colspan: 0,
            //         },
            //         header18: {
            //             label: "អនុ.ខែទី៦",
            //             rowspan: 0,
            //             colspan: 0,
            //         },

            //         header19: {
            //             label: "សរុប",
            //             rowspan: 0,
            //             colspan: 0,
            //         },
            //         header20: {
            //             label: "ខែទី៧",
            //             rowspan: 0,
            //             colspan: 0,
            //         },
            //         header21: {
            //             label: "ខែទី៨",
            //             rowspan: 0,
            //             colspan: 0,
            //         },
            //         header22: {
            //             label: "ខែទី៩",
            //             rowspan: 0,
            //             colspan: 0,
            //         },
            //         header23: {
            //             label: "សរុប",
            //             rowspan: 0,
            //             colspan: 0,
            //         },
            //         header24: {
            //             label: "អនុ.ខែទី៧",
            //             rowspan: 0,
            //             colspan: 0,
            //         },
            //         header25: {
            //             label: "អនុ.ខែទី៨",
            //             rowspan: 0,
            //             colspan: 0,
            //         },
            //         header26: {
            //             label: "អនុ.ខែទី៩",
            //             rowspan: 0,
            //             colspan: 0,
            //         },

            //         header27: {
            //             label: "សរុប",
            //             rowspan: 0,
            //             colspan: 0,
            //         },
            //         header28: {
            //             label: "ខែទី១០",
            //             rowspan: 0,
            //             colspan: 0,
            //         },
            //         header29: {
            //             label: "ខែទី១១",
            //             rowspan: 0,
            //             colspan: 0,
            //         },
            //         header30: {
            //             label: "ខែទី១២",
            //             rowspan: 0,
            //             colspan: 0,
            //         },
            //         header31: {
            //             label: "សរុប",
            //             rowspan: 0,
            //             colspan: 0,
            //         },
            //         header32: {
            //             label: "អនុ.ខែទី១០",
            //             rowspan: 0,
            //             colspan: 0,
            //         },
            //         header33: {
            //             label: "អនុ.ខែទី១១",
            //             rowspan: 0,
            //             colspan: 0,
            //         },
            //         header34: {
            //             label: "អនុ.ខែទី១២",
            //             rowspan: 0,
            //             colspan: 0,
            //         }
            //     },
            //     dataFillables: {
            //         field1: "account_group",
            //         field2: "account",
            //         field3: "sub_account",
            //         field4: "department",
            //         field5: "finance_rule",
            //         field6: "amount",
            //         field7: "percentage_implementing_rule",
            //         fiedl8: "costing_plan_semester1",
            //         fiedl9: "costing_plan_semester2",
            //         fiedl10: "costing_plan_semester3",
            //         fiedl11: "costing_plan_semester4",
            //         fiedl12: "remark"
            //     },
            //     // group_fields: { field1: "rev_group", field2: "rev_type" },
            //     summary: [],
            //     data: [
            //         {
            //             id: 1,
            //             account_group: { id: 1, code: "001", name: "Account Group" },
            //             account: { id: 1, code: "001", name: "Account Group" },
            //             sub_account: { id: 1, code: "001", name: "Account Group" },
            //             department: { id: 1, name: "អគ្គនាយកដ្ឋាន" },
            //             finance_rule: 1222,
            //             amount: 1222,
            //             percentage_implementing_rule: 12,
            //             costing_plan_semester1: {
            //                 planing_year: {
            //                     cost_plan_jan: 12,
            //                     cost_plan_feb: 12,
            //                     cost_plan_mar: 12,
            //                 },
            //                 estimate_cost: {
            //                     cost_plan_jan: 12,
            //                     cost_plan_feb: 12,
            //                     cost_plan_mar: 12,
            //                 },
            //                 adjustment_request: {
            //                     cost_plan_jan: 12,
            //                     cost_plan_feb: 12,
            //                     cost_plan_mar: 12,
            //                 },
            //                 total_plan: 36,
            //             },
            //             costing_plan_semester2: {
            //                 planing_year: {
            //                     cost_plan_jan: 12,
            //                     cost_plan_feb: 12,
            //                     cost_plan_mar: 12,
            //                 },
            //                 estimate_cost: {
            //                     cost_plan_jan: 12,
            //                     cost_plan_feb: 12,
            //                     cost_plan_mar: 12,
            //                 },
            //                 adjustment_request: {
            //                     cost_plan_jan: 12,
            //                     cost_plan_feb: 12,
            //                     cost_plan_mar: 12,
            //                 },
            //                 total_plan: 36,
            //             },
            //             costing_plan_semester3: {
            //                 planing_year: {
            //                     cost_plan_jan: 12,
            //                     cost_plan_feb: 12,
            //                     cost_plan_mar: 12,
            //                 },
            //                 estimate_cost: {
            //                     cost_plan_jan: 12,
            //                     cost_plan_feb: 12,
            //                     cost_plan_mar: 12,
            //                 },
            //                 adjustment_request: {
            //                     cost_plan_jan: 12,
            //                     cost_plan_feb: 12,
            //                     cost_plan_mar: 12,
            //                 },
            //                 total_plan: 36,
            //             },
            //             costing_plan_semester4: {
            //                 planing_year: {
            //                     cost_plan_jan: 12,
            //                     cost_plan_feb: 12,
            //                     cost_plan_mar: 12,
            //                 },
            //                 estimate_cost: {
            //                     cost_plan_jan: 12,
            //                     cost_plan_feb: 12,
            //                     cost_plan_mar: 12,
            //                 },
            //                 adjustment_request: {
            //                     cost_plan_jan: 12,
            //                     cost_plan_feb: 12,
            //                     cost_plan_mar: 12,
            //                 },
            //                 total_plan: 36,
            //             },
            //             remark: ""
            //         }
            //     ],
            //     account_group_id: [
            //         {
            //             value: 1,
            //             label: "70-"
            //         }, {
            //             value: 2,
            //             label: "73-"
            //         },
            //     ],
            //     account_id: [
            //         {
            //             value: 2,
            //             label: "7302"
            //         },
            //     ],
            //     sub_account_id: [
            //         {
            //             value: 2,
            //             label: "73023"
            //         },
            //     ],
            //     department_id: [
            //         {
            //             value: 2,
            //             label: "ក្រសួង-ស្ថាប័ន"
            //         }
            //     ],
            //     finance_rule: [
            //         {
            //             value: 1,
            //             label: "2023"
            //         }
            //     ],
            //     limit: 10,
            //     total: 3,
            // },
            // formAttributes: [
            //     {
            //         name: "account_group_id",
            //         type: "select",
            //         options: [],
            //         required: true
            //     },
            //     {
            //         name: "account_id",
            //         type: "select",
            //         options: [],
            //         required: true
            //     },
            //     {
            //         name: "sub_account_id",
            //         type: "select",
            //         options: [],
            //         required: true
            //     },
            //     {
            //         name: "department_id",
            //         type: "select",
            //         options: [],
            //         required: true
            //     },
            //     // {
            //     //     name: "remark",
            //     //     type: "textarea",
            //     //     required: false
            //     // },
            //     // {
            //     //     name: "semester1",
            //     //     type: "text_group",
            //     //     child_form: [
            //     //         {
            //     //             name: "income_budget_jan",
            //     //             type: "text",
            //     //             required: false,
            //     //         },
            //     //         {
            //     //             name: "income_budget_feb",
            //     //             type: "text",
            //     //             required: false,
            //     //         },
            //     //         {
            //     //             name: "income_budget_mar",
            //     //             type: "text",
            //     //             required: false,
            //     //         },
            //     //     ],
            //     //     required: false, 
            //     // },
            //     // {
            //     //     name: "semester2",
            //     //     type: "text_group",
            //     //     child_form: [
            //     //         {
            //     //             name: "income_budget_apr",
            //     //             type: "text",
            //     //             required: false,
            //     //         },
            //     //         {
            //     //             name: "income_budget_may",
            //     //             type: "text",
            //     //             required: false,
            //     //         },
            //     //         {
            //     //             name: "income_budget_jun",
            //     //             type: "text",
            //     //             required: false,
            //     //         },
            //     //     ],
            //     //     required: false,
            //     // },
            //     // {
            //     //     name: "semester3",
            //     //     type: "text_group",
            //     //     child_form: [
            //     //         {
            //     //             name: "income_budget_jul",
            //     //             type: "text",
            //     //             required: false,
            //     //         },
            //     //         {
            //     //             name: "income_budget_aug",
            //     //             type: "text",
            //     //             required: false,
            //     //         },
            //     //         {
            //     //             name: "income_budget_sep",
            //     //             type: "text",
            //     //             required: false,
            //     //         },
            //     //     ],
            //     //     required: false,
            //     // },
            //     // {
            //     //     name: "semester4",
            //     //     type: "text_group",
            //     //     child_form: [
            //     //         {
            //     //             name: "income_budget_oct",
            //     //             type: "text",
            //     //             required: false,
            //     //         },
            //     //         {
            //     //             name: "income_budget_nov",
            //     //             type: "text",
            //     //             required: false,
            //     //         },
            //     //         {
            //     //             name: "income_budget_dec",
            //     //             type: "text",
            //     //             required: false,
            //     //         },
            //     //     ],
            //     //     required: false,
            //     // },
            // ],
            // rowDisplay: "3grid", //1grid, 2grid, 3grid, 4grid
            // dataFields: [],
            // dataInfo: {}
        }
    },
}
</script>

<style>
th.th-width-300 {
    min-width: 260px !important;
}

td.td-width-150 {
    min-width: 150px !important;
}

table tr td,
th {
    border: 1px solid #d7d7d7;
}

th {
    text-align: center !important;
    /* display: inline; */
}

th.rotate {
    transform: rotate(-90deg)
}

.vs-table--thead th .vs-table-text {
    text-transform: uppercase;
    font-weight: 600;
    text-align: center;
    display: inline !important;
}
</style>

<style lang="scss">
#data-list-list-view {
    font-family: var(--font-base);

    .vs-con-table {

        /*
        Below media-queries is fix for responsiveness of action buttons
        Note: If you change action buttons or layout of this page, Please remove below style
        */
        @media (max-width: 689px) {
            .vs-table--search {
                margin-left: 0;
                max-width: unset;
                width: 100%;

                .vs-table--search-input {
                    width: 100%;
                }
            }
        }

        @media (max-width: 461px) {
            .items-per-page-handler {
                display: none;
            }
        }

        @media (max-width: 341px) {
            .data-list-btn-container {
                width: 100%;

                .dd-actions,
                .btn-add-new {
                    width: 100%;
                    margin-right: 0 !important;
                }
            }
        }

        .product-name {
            max-width: 23rem;
        }

        .vs-table--header {
            display: flex;
            flex-wrap: wrap;
            margin-left: 1.5rem;
            margin-right: 1.5rem;

            >span {
                display: flex;
                flex-grow: 1;
            }

            .vs-table--search {
                padding-top: 0;

                .vs-table--search-input {
                    padding: 0.9rem 2.5rem;
                    font-size: 1rem;

                    &+i {
                        left: 1rem;
                    }

                    &:focus+i {
                        left: 1rem;
                    }
                }
            }
        }

        .vs-table {
            border-collapse: separate;
            border-spacing: 0 1.3rem;
            padding: 0 1rem;

            tr {
                box-shadow: 0 4px 20px 0 rgba(0, 0, 0, .05);

                td {
                    padding: 20px;

                    &:first-child {
                        border-top-left-radius: .5rem;
                        border-bottom-left-radius: .5rem;
                    }

                    &:last-child {
                        border-top-right-radius: .5rem;
                        border-bottom-right-radius: .5rem;
                    }
                }

                td.td-check {
                    padding: 20px !important;
                }
            }
        }

        .vs-table--thead {
            th {
                padding-top: 0;
                padding-bottom: 0;

                .vs-table-text {
                    text-transform: uppercase;
                    font-weight: 600;
                }
            }

            th.td-check {
                padding: 0 15px !important;
            }

            tr {
                background: none;
                box-shadow: none;
            }
        }

        .vs-table--pagination {
            justify-content: center;
        }
    }
}
</style>


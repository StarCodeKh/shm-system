<template>
    <vx-card :title="$t(title)" code-toggler>
        <d-modal-form @clicked="initTableData" ref="refModalForm" :data="dataTables" :api="api"
            :formAttributes="formAttributes" :rowDisplay="rowDisplay"></d-modal-form>

        <div class="flex flex-wrap-reverse items-center data-list-btn-container">
            <!-- ADD NEW -->
            <div class="btn-add-new p-3 mb-4 mr-4 rounded-lg cursor-pointer flex items-center justify-center text-lg font-medium text-base text-primary border border-solid border-primary"
                @click="openForm">
                <feather-icon icon="PlusIcon" svgClasses="h-4 w-4" />
                <span class="ml-2 text-base text-primary">{{ $t("AddNew") }}</span>
            </div>
        </div>

        <d-search @searchQuery="searchQuery" @clicked="initTableData" :data="dataTables" :api="api"
            :formAttributes="formAttributes" :rowDisplay="'4grid'" :dataInfo="''"></d-search>

        <vs-table v-if="dataTables.data && dataTables.data.length && dataTables.children" :max-items="dataTables.limit"
            :data="dataTables.data">
            <template slot="thead">
                <vs-th>{{ $t("no") }}</vs-th>
                <vs-th :key="i" v-for="(header, i) in dataHeaders">{{ $t(header) }}</vs-th>
                <vs-th>{{ $t("Action") }}</vs-th>
            </template>

            <template slot-scope="{data}">
                <tbody :key="pindextr" v-for="(ptr, pindextr) in data">
                    <vs-tr :state="dataTables.backgroundColor">
                        <td colspan="9">{{ ptr.parent }}</td>
                    </vs-tr>
                    <vs-tr :key="indextr" v-for="(tr, indextr) in ptr.children">
                        <vs-td>{{ calPageIncreaseNumber(dataTables.limit, indextr) }}</vs-td>
                        <vs-td v-for="header in dataHeaders" :key="header.indextr">{{ tr[header] }}</vs-td>
                        <vs-td>
                            <feather-icon icon="EditIcon" svgClasses="w-5 h-5 hover:text-primary stroke-current"
                                @click.stop="initEdit(tr)" />
                            <feather-icon v-if="allowDel" icon="TrashIcon"
                                svgClasses="w-5 h-5 hover:text-danger stroke-current" class="ml-2"
                                @click.stop="initDel(tr.id)" />
                        </vs-td>
                    </vs-tr>
                </tbody>
            </template>
        </vs-table>

        <vs-table v-else-if="dataTables.data && dataTables.data.length && dataTables.dispalyAdvanceTable"
            :max-items="dataTables.limit" :data="dataTables.data">
            <template>
                <vs-tr style="background-color: #28C76F; color: #ffffff; font-weight: bold;">
                    <vs-td style="width: auto" rowspan="2">
                        <center>បរិយាយមុខសញ្ញា</center>
                    </vs-td>
                    <vs-td style="" rowspan="2">
                        <center>ច្បាប់ឆ្នាំ២០២២</center>
                    </vs-td>
                    <vs-td style="" rowspan="2">
                        <center>ចំណាយមិនប្រចាំ</center>
                    </vs-td>
                    <vs-td style="" rowspan="2">
                        <center>ចំណាយប្រចាំ</center>
                    </vs-td>
                    <vs-td style="width: 300px" colspan="3">
                        <center>ពិដានចំណាយចរន្ត</center>
                    </vs-td>
                    <vs-td style="width: 300px" colspan="3">
                        <center>អាត្រាកំណើនប្រចាំឆ្នាំ</center>
                    </vs-td>
                    <vs-td style="" rowspan="2">សម្គាល់</vs-td>
                </vs-tr>

                <vs-tr style="background-color: #28C76F; color: #ffffff; font-weight: bold;">
                    <vs-td>២០២៣</vs-td>
                    <vs-td>២០២៤</vs-td>
                    <vs-td>២០២៥</vs-td>
                    <vs-td>២០២៦</vs-td>
                    <vs-td>២០២៧</vs-td>
                    <vs-td>២០២៨</vs-td>
                </vs-tr>

                <vs-tr>
                    <vs-td></vs-td>
                    <vs-td><small>1</small></vs-td>
                    <vs-td><small>2</small></vs-td>
                    <vs-td><small>3=1-2</small></vs-td>
                    <vs-td><small>4</small></vs-td>
                    <vs-td><small>5</small></vs-td>
                    <vs-td><small>6</small></vs-td>
                    <vs-td><small>7=(4-1)/1</small></vs-td>
                    <vs-td><small>8=(5-4)/4</small></vs-td>
                    <vs-td><small>9=(6-5)/5</small></vs-td>
                    <vs-td><small>10</small></vs-td>
                </vs-tr>

                <vs-tr :state="'success'">
                    <vs-td colspan="11">ផ្នែកទី១៖ ពិដានចំណាយចរន្ត ឆ្នាំ២០២៣-២០២៥ សរុបរួមក្រសួង (យោងលេខ ០៣ សរណន
                        ចុះថ្ងៃទី៨ ខែមិថុនា ឆ្នាំ២០២២)</vs-td>
                </vs-tr>

                <vs-tr :state="'primary'">
                    <vs-td>សរុបរួមក្រសួង</vs-td>
                    <vs-td>0.00</vs-td>
                    <vs-td>0.00</vs-td>
                    <vs-td>0.00</vs-td>
                    <vs-td>0.00</vs-td>
                    <vs-td>0.00</vs-td>
                    <vs-td>0.00</vs-td>
                    <vs-td>0.00</vs-td>
                    <vs-td>0.00</vs-td>
                    <vs-td>0.00</vs-td>
                    <vs-td></vs-td>
                </vs-tr>

                <vs-tr :state="'warning'">
                    <vs-td>I.រដ្ឋបាលកណ្តាល</vs-td>
                    <vs-td>0.00</vs-td>
                    <vs-td>0.00</vs-td>
                    <vs-td>0.00</vs-td>
                    <vs-td>0.00</vs-td>
                    <vs-td>0.00</vs-td>
                    <vs-td>0.00</vs-td>
                    <vs-td>0.00</vs-td>
                    <vs-td>0.00</vs-td>
                    <vs-td>0.00</vs-td>
                    <vs-td></vs-td>
                </vs-tr>

                <vs-tr>
                    <vs-td>- បន្ទុកបុគ្គលិក</vs-td>
                    <vs-td>0.00</vs-td>
                    <vs-td>0.00</vs-td>
                    <vs-td>0.00</vs-td>
                    <vs-td>0.00</vs-td>
                    <vs-td>0.00</vs-td>
                    <vs-td>0.00</vs-td>
                    <vs-td>0.00</vs-td>
                    <vs-td>0.00</vs-td>
                    <vs-td>0.00</vs-td>
                    <vs-td></vs-td>
                </vs-tr>
                <vs-tr>
                    <vs-td>- ក្រៅបន្ទុកបុគ្គលិក</vs-td>
                    <vs-td>0.00</vs-td>
                    <vs-td>0.00</vs-td>
                    <vs-td>0.00</vs-td>
                    <vs-td>0.00</vs-td>
                    <vs-td>0.00</vs-td>
                    <vs-td>0.00</vs-td>
                    <vs-td>0.00</vs-td>
                    <vs-td>0.00</vs-td>
                    <vs-td>0.00</vs-td>
                    <vs-td></vs-td>
                </vs-tr>

                <vs-tr :state="'warning'">
                    <vs-td>II.មន្ទីរសេដ្ឋកិច្ច​និងហិរញ្ញវត្ថុ​ រាជធានី ខេត្ត</vs-td>
                    <vs-td>0.00</vs-td>
                    <vs-td>0.00</vs-td>
                    <vs-td>0.00</vs-td>
                    <vs-td>0.00</vs-td>
                    <vs-td>0.00</vs-td>
                    <vs-td>0.00</vs-td>
                    <vs-td>0.00</vs-td>
                    <vs-td>0.00</vs-td>
                    <vs-td>0.00</vs-td>
                    <vs-td></vs-td>
                </vs-tr>

                <vs-tr>
                    <vs-td>- បន្ទុកបុគ្គលិក</vs-td>
                    <vs-td>0.00</vs-td>
                    <vs-td>0.00</vs-td>
                    <vs-td>0.00</vs-td>
                    <vs-td>0.00</vs-td>
                    <vs-td>0.00</vs-td>
                    <vs-td>0.00</vs-td>
                    <vs-td>0.00</vs-td>
                    <vs-td>0.00</vs-td>
                    <vs-td>0.00</vs-td>
                    <vs-td></vs-td>
                </vs-tr>
                <vs-tr>
                    <vs-td>- ក្រៅបន្ទុកបុគ្គលិក</vs-td>
                    <vs-td>0.00</vs-td>
                    <vs-td>0.00</vs-td>
                    <vs-td>0.00</vs-td>
                    <vs-td>0.00</vs-td>
                    <vs-td>0.00</vs-td>
                    <vs-td>0.00</vs-td>
                    <vs-td>0.00</vs-td>
                    <vs-td>0.00</vs-td>
                    <vs-td>0.00</vs-td>
                    <vs-td></vs-td>
                </vs-tr>

                <vs-tr :state="'success'">
                    <vs-td colspan="11">ផ្នែកទី២៖ ពិដានចំណាយចរន្ត ឆ្នាំ២០២៣-២០២៥ តាមអង្គភាពថវិកា</vs-td>
                </vs-tr>

                <vs-tr :state="'primary'">
                    <vs-td>សរុបរួមក្រសួង</vs-td>
                    <vs-td>0.00</vs-td>
                    <vs-td>0.00</vs-td>
                    <vs-td>0.00</vs-td>
                    <vs-td>0.00</vs-td>
                    <vs-td>0.00</vs-td>
                    <vs-td>0.00</vs-td>
                    <vs-td>0.00</vs-td>
                    <vs-td>0.00</vs-td>
                    <vs-td>0.00</vs-td>
                    <vs-td></vs-td>
                </vs-tr>

                <vs-tr :state="'warning'">
                    <vs-td>I.រដ្ឋបាលកណ្តាល</vs-td>
                    <vs-td>0.00</vs-td>
                    <vs-td>0.00</vs-td>
                    <vs-td>0.00</vs-td>
                    <vs-td>0.00</vs-td>
                    <vs-td>0.00</vs-td>
                    <vs-td>0.00</vs-td>
                    <vs-td>0.00</vs-td>
                    <vs-td>0.00</vs-td>
                    <vs-td>0.00</vs-td>
                    <vs-td></vs-td>
                </vs-tr>

                <vs-tr>
                    <vs-td>- បន្ទុកបុគ្គលិក</vs-td>
                    <vs-td>0.00</vs-td>
                    <vs-td>0.00</vs-td>
                    <vs-td>0.00</vs-td>
                    <vs-td>0.00</vs-td>
                    <vs-td>0.00</vs-td>
                    <vs-td>0.00</vs-td>
                    <vs-td>0.00</vs-td>
                    <vs-td>0.00</vs-td>
                    <vs-td>0.00</vs-td>
                    <vs-td></vs-td>
                </vs-tr>
                <vs-tr>
                    <vs-td>- ក្រៅបន្ទុកបុគ្គលិក</vs-td>
                    <vs-td>0.00</vs-td>
                    <vs-td>0.00</vs-td>
                    <vs-td>0.00</vs-td>
                    <vs-td>0.00</vs-td>
                    <vs-td>0.00</vs-td>
                    <vs-td>0.00</vs-td>
                    <vs-td>0.00</vs-td>
                    <vs-td>0.00</vs-td>
                    <vs-td>0.00</vs-td>
                    <vs-td></vs-td>
                </vs-tr>

                <vs-tr :state="'warning'">
                    <vs-td>១. អនុកម្មវិធីទី១.១ អគ្គនាយកដ្ឋាន​គោលនយោបាយ</vs-td>
                    <vs-td>0.00</vs-td>
                    <vs-td>0.00</vs-td>
                    <vs-td>0.00</vs-td>
                    <vs-td>0.00</vs-td>
                    <vs-td>0.00</vs-td>
                    <vs-td>0.00</vs-td>
                    <vs-td>0.00</vs-td>
                    <vs-td>0.00</vs-td>
                    <vs-td>0.00</vs-td>
                    <vs-td></vs-td>
                </vs-tr>

                <vs-tr>
                    <vs-td>- បន្ទុកបុគ្គលិក</vs-td>
                    <vs-td>0.00</vs-td>
                    <vs-td>0.00</vs-td>
                    <vs-td>0.00</vs-td>
                    <vs-td>0.00</vs-td>
                    <vs-td>0.00</vs-td>
                    <vs-td>0.00</vs-td>
                    <vs-td>0.00</vs-td>
                    <vs-td>0.00</vs-td>
                    <vs-td>0.00</vs-td>
                    <vs-td></vs-td>
                </vs-tr>
                <vs-tr>
                    <vs-td>- ក្រៅបន្ទុកបុគ្គលិក</vs-td>
                    <vs-td>0.00</vs-td>
                    <vs-td>0.00</vs-td>
                    <vs-td>0.00</vs-td>
                    <vs-td>0.00</vs-td>
                    <vs-td>0.00</vs-td>
                    <vs-td>0.00</vs-td>
                    <vs-td>0.00</vs-td>
                    <vs-td>0.00</vs-td>
                    <vs-td>0.00</vs-td>
                    <vs-td></vs-td>
                </vs-tr>

                <vs-tr :state="'warning'">
                    <vs-td>២. អនុកម្មវិធីទី១.២ អគ្គលេខាធិការដ្ឋាន​គណៈកម្មាធិការជាតិ​ផលិតភាពសេដ្ឋកិច្ច​កម្ពុជា</vs-td>
                    <vs-td>0.00</vs-td>
                    <vs-td>0.00</vs-td>
                    <vs-td>0.00</vs-td>
                    <vs-td>0.00</vs-td>
                    <vs-td>0.00</vs-td>
                    <vs-td>0.00</vs-td>
                    <vs-td>0.00</vs-td>
                    <vs-td>0.00</vs-td>
                    <vs-td>0.00</vs-td>
                    <vs-td></vs-td>
                </vs-tr>

                <vs-tr>
                    <vs-td>- បន្ទុកបុគ្គលិក</vs-td>
                    <vs-td>0.00</vs-td>
                    <vs-td>0.00</vs-td>
                    <vs-td>0.00</vs-td>
                    <vs-td>0.00</vs-td>
                    <vs-td>0.00</vs-td>
                    <vs-td>0.00</vs-td>
                    <vs-td>0.00</vs-td>
                    <vs-td>0.00</vs-td>
                    <vs-td>0.00</vs-td>
                    <vs-td></vs-td>
                </vs-tr>
                <vs-tr>
                    <vs-td>- ក្រៅបន្ទុកបុគ្គលិក</vs-td>
                    <vs-td>0.00</vs-td>
                    <vs-td>0.00</vs-td>
                    <vs-td>0.00</vs-td>
                    <vs-td>0.00</vs-td>
                    <vs-td>0.00</vs-td>
                    <vs-td>0.00</vs-td>
                    <vs-td>0.00</vs-td>
                    <vs-td>0.00</vs-td>
                    <vs-td>0.00</vs-td>
                    <vs-td></vs-td>
                </vs-tr>

            </template>
        </vs-table>

        <vs-table v-else-if="dataTables.data && dataTables.data.length" :max-items="dataTables.limit"
            :data="dataTables.data">
            <template slot="thead">
                <vs-th>{{ $t("no") }}</vs-th>
                <vs-th :key="i" v-for="(header, i) in dataHeaders">{{ $t(header) }}</vs-th>
                <vs-th>{{ $t("Action") }}</vs-th>
            </template>

            <template slot-scope="{data}">
                <vs-tr :key="indextr" v-for="(tr, indextr) in data">
                    <vs-td>{{ calPageIncreaseNumber(dataTables.limit, indextr) }}</vs-td>
                    <vs-td v-for="header in dataHeaders" :key="header.indextr">{{ tr[header] }}</vs-td>
                    <vs-td>
                        <template v-if="dataTables.actionButton">
                            <feather-icon v-for="rowBtnAction in dataTables.actionButton" :key="rowBtnAction.indextr"
                                :icon="rowBtnAction.icon" svgClasses="mr-2 w-5 h-5 hover:text-primary stroke-current"
                                @click.stop="viewUrl(rowBtnAction)" />
                        </template>
                        <feather-icon icon="EditIcon" svgClasses="w-5 h-5 hover:text-primary stroke-current"
                            @click.stop="initEdit(tr)" />
                        <feather-icon v-if="allowDel" icon="TrashIcon"
                            svgClasses="w-5 h-5 hover:text-danger stroke-current" class="ml-2"
                            @click.stop="initDel(tr.id)" />
                    </vs-td>
                </vs-tr>
            </template>
        </vs-table>

        <div class="my-5">
            <vs-pagination :total="calPaginNumber(dataTables.total / dataTables.limit)"
                v-model="current_page"></vs-pagination>
            <!-- <p class="mb-3">Current Page: {{ current_page }}</p>
            <vs-button @click="current_page++">Increment</vs-button>
            <vs-button class="ml-4 mb-4" @click="current_page--">Decrement</vs-button> -->
        </div>

    </vx-card>
</template>

<script>

import axios from "@/axios.js"
import DSearch from '@/views/form-builder/DSearch.vue'
import DModalForm from '@/views/form-builder/DModalForm.vue'
import { stringify } from 'querystring';
import { ref } from 'vue';
export default {
    props: {
        title: {
            type: String,
            required: true
        },
        dataAttributes: {
            required: true,
        },
        dataHeaders: {
            required: true,
        },
        dataTables: {
            required: true,
        },
        allowDel: {
            type: Boolean
        },
        api: { type: String },
        formAttributes: { required: true },
        rowDisplay: { type: String }
    },
    data() {
        return {
            currentx: 0,
            current_page: 1,
            dataId: 0
        }
    },
    components: {
        DSearch,
        DModalForm
    },
    methods: {
        viewUrl(obj) {
            console.log(obj);
            this.$router.push(obj.path).catch(() => { });
        },
        openForm() {
            this.$refs.refModalForm.openNewForm();
        },
        calPaginNumber(n) {
            // alert(n);
            let _newVal = 0;
            if (Number(n) === n && n % 1 === 0) {
                _newVal = n;
            } else {
                _newVal = Math.floor(n + 1);
            }
            return _newVal;
        },
        calPageIncreaseNumber(_page_limit, index) {
            let _offset = (this.current_page - 1) * _page_limit;
            let _result = _offset + (index + 1);
            return _result;
        },
        initTableData() {
            let _search_params = {
                pageNum: this.current_page,
                searchFields: []
            }
            this.$emit('clicked', _search_params);
        },
        searchQuery(_search_fields) {
            let _search_params = {
                pageNum: this.current_page,
                searchFields: _search_fields
            }
            this.$emit('clicked', _search_params);
        },
        initDel(id) {
            this.dataId = id;
            this.$vs.dialog({
                type: 'confirm',
                color: 'danger',
                title: `Confirm`,
                text: 'តើអ្នកពិតជាចង់ធ្វើការលុបទិន្នន័យនេះជារៀងរហូត?',
                accept: this.acceptAlert
            })
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
                        this.$emit('clicked', _search_params);
                        // this.$router.push('/account/expense').catch(() => { })
                    }).catch((error) => {
                        reject(error)
                        this.$vs.notify({
                            title: 'Message',
                            text: "មិនអាចដំណើរកាបានទេ,​ សូមត្រួតពិនិត្យពត៌មានឡើងវិញ។",
                            iconPack: 'feather',
                            icon: 'icon-check-circle',
                            color: 'danger',
                            position: 'top-right'
                        })
                    })
            })
        },
        initEdit(data) {
            this.$refs.refModalForm.initForm(data);
        }

    },
    created() {
        this.$vs.loading.close();
    },
    watch: {
        current_page: function (val) {
            let _search_params = {
                pageNum: this.current_page,
                searchFields: []
            }
            this.$emit('clicked', _search_params)
        }
    }
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


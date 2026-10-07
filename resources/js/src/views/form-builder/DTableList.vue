<template>
    <vx-card class="dt-list">
        <!-- Customize Officer -->
        <div class="vx-row" v-if="dataAttributes.enableDashboardOfficer">
            <div class="vx-col w-full md:w-1/3 lg:w-1/3 xl:w-1/3">
                <statistics-card-line
                  hideChart
                  class="mb-base"
                  icon="UsersIcon"
                  icon-right
                  :statistic="dataTables.total"
                  :statisticTitle="$t('stat_members_total')" />
            </div>

            <div class="vx-col w-full md:w-1/3 lg:w-1/3 xl:w-1/3">
                <statistics-card-line
                  hideChart
                  class="mb-base"
                  icon="UserIcon"
                  icon-right
                  :statistic="summary.male"
                  :statisticTitle="$t('stat_male_total')"
                  color="primary" />
            </div>
            
            <div class="vx-col w-full md:w-1/3 lg:w-1/3 xl:w-1/3">
                <statistics-card-line
                  hideChart
                  class="mb-base"
                  icon="UserIcon"
                  icon-right
                  :statistic="summary.female"
                  :statisticTitle="$t('stat_female_total')"
                  color="primary" />
            </div>
        </div>
        <!-- Customize Request Dashboard -->
        <div class="vx-row" v-if="dataAttributes.enableDashboardRequest">

            <div class="vx-col w-full sm:w-1/2 md:w-1/2 lg:w-1/4 xl:w-1/4">
                <statistics-card-line
                  hideChart
                  class="mb-base"
                  icon="ServerIcon"
                  icon-right
                  :statistic="summary.approved"
                  :statisticTitle="$t('stat_approved')"
                  color="primary" />
            </div>

            <div class="vx-col w-full sm:w-1/2 md:w-1/2 lg:w-1/4 xl:w-1/4">
                <statistics-card-line
                  hideChart
                  class="mb-base"
                  icon="CpuIcon"
                  icon-right
                  :statistic="summary.in_review"
                  :statisticTitle="$t('stat_reviewing')" />
            </div>

            <div class="vx-col w-full sm:w-1/2 md:w-1/2 lg:w-1/4 xl:w-1/4">
                <statistics-card-line
                  hideChart
                  class="mb-base"
                  icon="ServerIcon"
                  icon-right
                   :statistic="summary.in_progress"
                  :statisticTitle="$t('stat_processing')"
                  color="primary" />
            </div>

            <!-- <div class="vx-col w-full sm:w-1/2 md:w-1/2 lg:w-1/4 xl:w-1/4">
                <statistics-card-line
                  hideChart
                  class="mb-base"
                  icon="CpuIcon"
                  icon-right
                  statistic="17"
                  :statisticTitle="$t('stat_pending')" />
            </div> -->

            <div class="vx-col w-full sm:w-1/2 md:w-1/2 lg:w-1/4 xl:w-1/4">
                <statistics-card-line
                  hideChart
                  class="mb-base"
                  icon="ServerIcon"
                  icon-right
                  :statistic="summary.rejected"
                  :statisticTitle="$t('stat_rejected')"
                  color="primary" />
            </div>
        </div>
        <!-- Customize Incentive Dashboard -->
        <div class="vx-row" v-if="dataAttributes.enableDashboardIncentive">
            <div class="vx-col w-full md:w-1/3 lg:w-1/3 xl:w-1/3">
                <statistics-card-line
                  hideChart
                  class="mb-base"
                  icon="CreditCardIcon"
                  icon-right
                  :statistic="dataTables.total"
                  :statisticTitle="$t('stat_grand_total')" />
            </div>

            <div class="vx-col w-full md:w-1/3 lg:w-1/3 xl:w-1/3">
                <statistics-card-line
                  hideChart
                  class="mb-base"
                  icon="DollarSignIcon"
                  icon-right
                  :statistic="dataTables.total_amount"
                  :statisticTitle="$t('stat_incentive_paid_riel')"
                  color="primary" />
            </div>
        </div>
        <!-- Customize Account Payable Dashboard -->
        <div class="vx-row" v-if="dataAttributes.enableDashboardPayable">

            <div class="vx-col w-full md:w-1/3 lg:w-1/3 xl:w-1/3">
                <small><statistics-card-line
                  hideChart
                  class="mb-base"
                  icon="HashIcon"
                  icon-right
                  :statistic="dataTables.total"
                  :statisticTitle="$t('stat_grand_total')"
                  color="dark" /></small>
            </div>

            <div class="vx-col w-full md:w-1/3 lg:w-1/3 xl:w-1/3">
                <small><statistics-card-line
                  hideChart
                  class="mb-base"
                  icon="CheckCircleIcon"
                  icon-right
                  :statistic="summary.total_paid_khr"
                  :statisticTitle="$t('stat_paid_riel')" /></small>
            </div>

            <div class="vx-col w-full md:w-1/3 lg:w-1/3 xl:w-1/3">
                <small><statistics-card-line
                  hideChart
                  class="mb-base"
                  icon="AlertCircleIcon"
                  icon-right
                  :statistic="summary.total_unpaid_khr"
                  :statisticTitle="$t('stat_unpaid_riel')"
                  color="warning" /></small>
            </div>

        </div>
        <!-- Customize Account Payment History Dashboard -->
        <div class="vx-row" v-if="dataAttributes.enablePaymentHistory">
            <div class="vx-col w-full sm:w-1/2 md:w-1/2 lg:w-1/4 xl:w-1/4">
                <statistics-card-line
                  hideChart
                  class="mb-base"
                  icon="CpuIcon"
                  icon-right
                  :statistic="dataTables.total"
                  :statisticTitle="$t('stat_grand_total')" />
            </div>

            <div class="vx-col w-full sm:w-1/2 md:w-1/2 lg:w-1/4 xl:w-1/4">
                <statistics-card-line
                  hideChart
                  class="mb-base"
                  icon="ServerIcon"
                  icon-right
                  :statistic="summary.paid"
                  :statisticTitle="$t('stat_paid')"
                  color="primary" />
            </div>

            <div class="vx-col w-full sm:w-1/2 md:w-1/2 lg:w-1/4 xl:w-1/4">
                <statistics-card-line
                  hideChart
                  class="mb-base"
                  icon="ServerIcon"
                  icon-right
                  :statistic="summary.void"
                  :statisticTitle="$t('stat_void')"
                  color="primary" />
            </div>

            <div class="vx-col w-full sm:w-1/2 md:w-1/2 lg:w-1/4 xl:w-1/4">
                <statistics-card-line
                  hideChart
                  class="mb-base"
                  icon="CpuIcon"
                  icon-right
                  :statistic="summary.exceptional"
                  :statisticTitle="$t('stat_exempt')" />
            </div>

        </div>
        <!-- #End Customize Dashboard Information -->
        
        <DModalForm @clicked="initTableData" ref="refModalForm" :data="dataTables" :api="api"
            :formAttributes="formAttributes" :parentDataInfo="dataInfo" :rowDisplay="rowDisplay"
            :dataAttributes="dataAttributes" :title="$t(title)" />

        <template title="modal" class="modal">
            <d-file-manager ref="refModelFileManager"></d-file-manager>
        </template>

        <template title="modal" class="modal">
            <d-file-uploader ref="refModelFileUploader" @clicked="initTableData"></d-file-uploader>
        </template>

        <vs-prompt title="Export To Excel" class="export-options" @cancle="clearFields" @accept="exportToExcel"
            accept-text="Export" @close="clearFields" :active.sync="activePrompt">
            <vs-input v-model="fileName" :placeholder="$tt('Enter File Name..')" class="w-full" />
            <v-select v-model="selectedFormat" :options="formats" class="my-4" />
            <div class="flex">
                <span class="mr-4">Cell Auto Width:</span>
                <vs-switch v-model="cellAutoWidth">Cell Auto Width</vs-switch>
            </div>
        </vs-prompt>

        <div class="dt-toolbar">
            <div class="dt-toolbar__title">
                <h3>{{ $t(title) }}</h3>
                <p v-if="dataTables.total !== undefined">{{ $t('dt_records', { n: formatCount(dataTables.total) }) }}</p>
            </div>

            <div class="dt-toolbar__actions">
                <button type="button" class="dt-btn dt-btn--icon" :class="{ 'is-active': !isHidden }"
                    :title="$t('dt_filter')" @click="isHidden = !isHidden">
                    <feather-icon icon="FilterIcon" svgClasses="h-4 w-4" />
                </button>

                <button v-if="can('print')" type="button" class="dt-btn dt-btn--icon" :title="$t('dt_print')" @click="initPrint">
                    <feather-icon icon="PrinterIcon" svgClasses="h-4 w-4" />
                </button>

                <button v-if="can('import')" type="button" class="dt-btn dt-btn--icon" :title="$t('dt_import')"
                    @click="initImport(dataTables, api)">
                    <feather-icon icon="UploadIcon" svgClasses="h-4 w-4" />
                </button>

                <button v-if="can('download')" type="button" class="dt-btn dt-btn--icon" :title="$t('dt_download')"
                    @click="initDowlonadElsx">
                    <feather-icon icon="DownloadIcon" svgClasses="h-4 w-4" />
                </button>

                <button v-if="can('filemanager')" type="button" class="dt-btn dt-btn--icon" :title="$t('dt_files')"
                    @click="initOpenFileManager">
                    <feather-icon icon="FolderIcon" svgClasses="h-4 w-4" />
                </button>

                <button v-if="!dataAttributes.enableToggleForm && can('create')" type="button" class="dt-btn dt-btn--primary"
                    @click="openForm">
                    <feather-icon icon="PlusIcon" svgClasses="h-4 w-4" />
                    <span>{{ $t("AddNew") }}</span>
                </button>

                <!-- If has private button -->
                <button v-if="dataAttributes.hasPrivateButton" type="button" class="dt-btn dt-btn--primary"
                    @click="openPrivateForm('')">
                    <feather-icon icon="PlusIcon" svgClasses="h-4 w-4" />
                    <span>{{ $t("AddNew") }}</span>
                </button>
            </div>
        </div>

        <d-heading-wizard v-if="dataAttributes.hasHeadingReport" :dataAttributes="dataAttributes"></d-heading-wizard>

        <template v-if="!isHidden">
            <d-search v-if="!dataAttributes.hideSearchBar" @searchQuery="searchQuery" @clicked="initTableData"
                :data="dataTables" :api="api" :formAttributes="formAttributes" :rowDisplay="'4grid'"
                :dataInfo="dataInfo"></d-search>
        </template>

        <div class="print-only">
            <!-- & dataTables.data.length -->
            <vs-table :noDataText="$t('dt_empty')" v-if="dataTables.data && dataAttributes.tableStyle == 1" :max-items="dataTables.limit"
                :data="dataTables.data">
                <template slot="thead">
                    <!-- 28C76F -->
                    <vs-th>{{ $t("no") }}</vs-th>
                    <vs-th :key="i" :class="{ 'dt-num': isAmountColumn(header) }"
                        v-for="(header, i) in dataHeaders">{{ $tField(header) }}</vs-th>
                    <vs-th class="dt-actions-head">{{ $t("Action")
                    }}</vs-th>
                </template>
                <template slot-scope="{data}">
                    <tbody :key="indextr" v-for="(tr, indextr) in data">
                        <vs-tr :state="dataAttributes.backgroundColor">
                            <vs-td>{{ calPageIncreaseNumber(dataTables.limit, indextr) }}</vs-td>
                            <vs-td v-for="header in dataHeaders" :key="header.indextr"
                                :class="{ 'dt-num': isAmountColumn(header), 'dt-date': isDateColumn(header), 'dt-path': header == 'path', 'dt-file-cell': isFileColumn(header) }">
                                <template v-if="isFileColumn(header)">
                                    <!-- 44px thumbnail: image preview, or a coloured file-type badge -->
                                    <a v-if="fileCell(tr, header).url" class="dt-file" :href="fileCell(tr, header).url" target="_blank"
                                        rel="noopener" :title="fileCell(tr, header).url" @click.stop>
                                        <img v-if="fileCell(tr, header).isImage && !brokenFiles[fileKey(tr, header)]"
                                            :src="fileCell(tr, header).url" alt="" loading="lazy"
                                            @error="$set(brokenFiles, fileKey(tr, header), true)">
                                        <span v-else class="dt-file__badge" :style="{ '--tone': fileCell(tr, header).tone }">
                                            {{ fileCell(tr, header).ext || 'file' }}
                                        </span>
                                    </a>
                                    <span v-else class="dt-file dt-file--empty">—</span>
                                </template>
                                <template v-else-if="header == 'status' && tr[header] && typeof tr[header] === 'object'">
                                    <span class="dt-badge" :class="'dt-badge--' + statusTone(tr[header])">{{ statusTitle(tr[header]) }}</span>
                                </template>
                                <template v-else-if="isAmountColumn(header)">
                                    <span class="dt-amount">{{ formatAmount(customField(tr, header)) }}</span>
                                </template>
                                <template v-else-if="header == 'path'">
                                    <span class="dt-path__text" :title="tr[header]">{{ tr[header] }}</span>
                                </template>
                                <template v-else>
                                    <template>{{ customField(tr, header) }}</template>
                                    <!-- <template v-if="header == 'status'">
                                        <template v-if="tr[header]!=null && tr[header]['title'] != undefined">{{ tr[header]["title"] }} </template>
                                        <template v-else>{{ tr[header] }} </template>
                                    </template>
                                    <template v-else>
                                        {{ tr[header] }}    
                                    </template> -->
                                </template>
                            </vs-td>
                            <vs-td class="dt-actions-cell">
                                <div class="dt-actions">
                                    <template v-for="(rowBtnAction, bi) in (dataAttributes.actionButton || [])">
                                        <button :key="'act-' + bi"
                                            v-if="rowBtnAction.allow && can(rowBtnAction.permission)"
                                            type="button" class="dt-action" :title="rowBtnAction.title ? $t(rowBtnAction.title) : ''"
                                            @click.stop="initAction(rowBtnAction, tr, rowBtnAction.method)">
                                            <feather-icon :icon="rowBtnAction.icon" svgClasses="w-4 h-4" />
                                        </button>
                                    </template>

                                    <button v-if="can('edit')" type="button" class="dt-action dt-action--edit"
                                        :title="$t('dt_edit')" @click.stop="initEdit(tr)">
                                        <feather-icon icon="Edit2Icon" svgClasses="w-4 h-4" />
                                    </button>

                                    <button v-if="allowDel && can('delete')" type="button" class="dt-action dt-action--delete"
                                        :title="$t('dt_delete')" @click.stop="initDel(tr.id, tr)">
                                        <feather-icon icon="Trash2Icon" svgClasses="w-4 h-4" />
                                    </button>
                                </div>
                            </vs-td>
                        </vs-tr>
                    </tbody>
                </template>
            </vs-table>

            <vs-table :noDataText="$t('dt_empty')" v-if="dataTables.data && dataAttributes.tableStyle == 2" :max-items="dataTables.limit"
                :data="dataTables.data">

                <template slot="thead">
                    <!-- 28C76F -->
                    <vs-th class="dt-th-strong">{{ $t("no") }}</vs-th>
                    <vs-th class="dt-th-strong" :key="i"
                        v-for="(header, i) in dataHeaders">{{ $tField(header) }}</vs-th>
                    <vs-th class="dt-th-strong">{{ $t("Action")
                    }}</vs-th>
                </template>

                <template slot-scope="{data}">
                    <tbody :key="pindextr" v-for="(ptr, pindextr) in data">
                        <vs-tr :state="dataAttributes.backgroundColor">
                            <!-- <td :colspan="Object.keys(ptr.children[0]).length">{{ ptr.name_kh }}</td> -->
                            <td :colspan="Object.keys(dataHeaders).length + 1">
                                {{ ptr.code }}-{{ ptr.name_kh }}
                                <feather-icon style="cursor: pointer;" @click="openPrivateForm(ptr)"
                                    icon="DollarSignIcon" svgClasses="w-5 h-5 hover:text-primary stroke-current" />
                                <feather-icon style="cursor: pointer;" @click="openFormByParent(ptr)" icon="PlusIcon"
                                    svgClasses="w-5 h-5 hover:text-primary stroke-current" />
                                <vs-progress :percent="75" color="warning"></vs-progress>
                            </td>
                            <td>
                                <center><feather-icon style="cursor: pointer;" @click="openFormByParent(ptr)"
                                        icon="PlusIcon" svgClasses="w-5 h-5 hover:text-primary stroke-current" />
                                </center>
                            </td>
                        </vs-tr>

                        <vs-tr :key="indextr" v-for="(tr, indextr) in ptr.children">
                            <vs-td>{{ calPageIncreaseNumber(dataTables.limit, indextr) }}</vs-td>
                            <vs-td v-for="(value, name, index) in dataHeaders" :key="name.indextr">
                                <template v-if="name == 'indicator'">
                                    <template v-if="tr[name]['data'].length > 0">
                                        <div class="mb-2" :key="indexI"
                                            v-for="(dataRow, indexI) in tr[name]['data']">
                                            <vx-card>
                                                {{ dataRow["code"] }}-{{ dataRow["kpi_name_kh"] }}
                                            </vx-card>
                                        </div>
                                    </template>
                                    <template v-else>
                                        {{ $t("no_kpi") }}
                                    </template>
                                </template>
                                <template v-if="name != 'indicator'">
                                    {{ tr[value] }}
                                </template>

                            </vs-td>
                            <vs-td>
                                <template v-if="dataAttributes.actionButton">
                                    <template v-for="rowBtnAction in dataAttributes.actionButton">
                                        <feather-icon v-if="rowBtnAction.allow" style="cursor: pointer;"
                                            :icon="rowBtnAction.icon"
                                            svgClasses="mr-2 w-5 h-5 hover:text-primary stroke-current"
                                            @click.stop="initAction(rowBtnAction, tr, rowBtnAction.method)" />
                                    </template>
                                </template>
                                <template v-else>
                                    <center>
                                        <feather-icon style="cursor: pointer;" :key="sindex" icon="EditIcon"
                                            svgClasses="mr-2 w-5 h-5 hover:text-primary stroke-current"
                                            @click.stop="initAction(rowBtnAction, tr, 'Edit')" />
                                    </center>
                                    <!-- <feather-icon style="cursor: pointer;" :key="sindex" icon="DollarSignIcon"
                                    svgClasses="mr-2 w-5 h-5 hover:text-primary stroke-current"
                                    @click.stop="initAction(rowBtnAction, tr, 'PrivateForm')" /> -->
                                </template>

                            </vs-td>
                        </vs-tr>
                        <!-- </template> -->
                    </tbody>
                </template>
            </vs-table>

            <vs-table :noDataText="$t('dt_empty')" v-if="dataTables.data && dataAttributes.tableStyle == 3" :max-items="dataTables.limit"
                :data="dataTables.data" style="overflow-x: auto">
                <template slot-scope="{data}">
                    <vs-tr>
                        <vs-td :style="style(header)" :key="i" v-for="(header, i) in dataHeaders"
                            :colspan="colspan(header)" :rowspan="rowspan(header)">
                            <span v-if="header.label">
                                <center>{{ $t($planText(header.label)) }} </center>
                            </span>
                            <span v-else>
                                <center>{{ $t($planText(header.label)) }}</center>
                            </span>
                        </vs-td>
                    </vs-tr>

                    <vs-tr class="dt-th-strong" v-if="dataTables.dataHeaders"
                       >
                        <vs-td :key="j" v-for="(header, j) in dataTables.dataHeaders" :colspan="colspan(header)"
                            :rowspan="rowspan(header)">{{ $planText(header.label) }} </vs-td>
                    </vs-tr>

                    <vs-tr class="dt-th-strong" v-if="dataTables.dataSubHeaders"
                       >
                        <vs-td :key="k" v-for="(header, k) in dataTables.dataSubHeaders"
                            :colspan="colspan(header)" :rowspan="rowspan(header)"><small>{{ $planText(header.label) }}</small></vs-td>
                    </vs-tr>

                    <tbody :key="pindextr" v-for="(ptr, pindextr) in data">
                        <vs-tr :state="'success'">
                            <template v-if="ptr.hasColspan == false">
                                <td>{{ ptr.name }}</td>
                                <td :key="index" v-for="(row, index) in ptr.values">{{ row }}</td>
                            </template>
                            <td v-else :colspan="ptr.colspan">{{ ptr.name }}</td>
                        </vs-tr>
                        <vs-tr :state="'primary'">
                            <td>{{ ptr.data.summary.name }}</td>
                            <td :key="index" v-for="(row, index) in ptr.data.summary.values">{{ row }}</td>
                            <td></td>
                        </vs-tr>

                        <template v-for="row in ptr.data.children">
                            <vs-tr :state="'warning'">
                                <td>{{ row.name }}
                                    <template v-if="dataAttributes.hasPrivateButton">
                                        <feather-icon style="cursor: pointer;" @click="openPrivateForm(row)"
                                            icon="PlusIcon" svgClasses="w-5 h-5 hover:text-primary stroke-current" />
                                    </template>
                                    <template v-else>
                                        <feather-icon style="cursor: pointer;" @click="openForm" icon="PlusIcon"
                                            svgClasses="w-5 h-5 hover:text-primary stroke-current" />
                                    </template>
                                </td>
                                <td :key="index1" v-for="(row, index1) in row.values">{{ row }} </td>
                                <td>
                                    <template v-if="dataAttributes.hasPrivateButton">
                                        <feather-icon style="cursor: pointer;" @click="openPrivateForm(row)"
                                            icon="PlusIcon" svgClasses="w-5 h-5 hover:text-primary stroke-current" />

                                    </template>
                                    <template v-else>
                                        <feather-icon style="cursor: pointer;" @click="openForm" icon="PlusIcon"
                                            svgClasses="w-5 h-5 hover:text-primary stroke-current" />
                                    </template>
                                </td>
                            </vs-tr>
                            <vs-tr :key="index" v-for="(row2, index) in row.data">
                                <td>{{ row2.name }}</td>
                                <td :key="index3" v-for="(row, index3) in row2.values">{{ row }}</td>
                                <td></td>
                            </vs-tr>

                            <template v-for="row3 in row.dataDetails">
                                <vs-tr class="dt-row-highlight">
                                    <td>{{ row3.name }}</td>
                                    <td :key="index4" v-for="(row, index4) in row3.values">{{ row }}</td>
                                    <td>
                                        <template v-if="dataAttributes.hasPrivateButton">
                                            <feather-icon style="cursor: pointer;" icon="EditIcon"
                                                svgClasses="w-5 h-5 hover:text-primary stroke-current"
                                                @click.stop="openPrivateForm(row3)" />
                                        </template>
                                        <template v-else>
                                            <feather-icon style="cursor: pointer;" icon="EditIcon"
                                                svgClasses="w-5 h-5 hover:text-primary stroke-current"
                                                @click.stop="initEdit(row3)" />
                                        </template>
                                        <!-- <template v-if="dataAttributes.actionButton">
                                        <feather-icon style="cursor: pointer;" v-for="rowBtnAction in dataAttributes.actionButton"
                                            :key="rowBtnAction.indextr" :icon="rowBtnAction.icon"
                                            svgClasses="mr-2 w-5 h-5 hover:text-primary stroke-current" @click.stop="viewUrl(rowBtnAction)" />
                                    </template> -->
                                    </td>
                                </vs-tr>

                                <vs-tr :key="index" v-for="(row2, index) in row3.data">
                                    <td>{{ row2.name }}</td>
                                    <td :key="index3" v-for="(row, index3) in row2.values">{{ row }}</td>
                                    <td></td>
                                </vs-tr>
                            </template>
                        </template>

                    </tbody>
                </template>
            </vs-table>

            <vs-table :noDataText="$t('dt_empty')" v-if="dataTables.data && dataAttributes.tableStyle == 4" :max-items="dataTables.limit"
                :data="dataTables.data" style="overflow-x: auto">
                <template slot-scope="{data}">
                    <vs-tr>
                        <vs-td :style="style(header)" :key="i" v-for="(header, i) in dataHeaders"
                            :colspan="colspan(header)" :rowspan="rowspan(header)">
                            <span v-if="header.label">
                                <center>{{ $t($planText(header.label)) }}</center>
                            </span>
                            <span v-else>
                                <center>{{ $tField(header) }}</center>
                            </span>
                        </vs-td>
                    </vs-tr>

                    <vs-tr class="dt-th-strong" v-if="dataTables.dataHeaders"
                       >
                        <vs-td :key="j" v-for="(sheader, j) in dataTables.dataHeaders">{{ sheader }}</vs-td>
                    </vs-tr>

                    <vs-tr v-if="dataTables.dataSubHeaders">
                        <vs-td :key="k" v-for="(sheader, k) in dataTables.dataSubHeaders"><small>{{ sheader
                        }}</small></vs-td>
                    </vs-tr>

                    <tbody :key="pindextr" v-for="(ptr, pindextr) in data">
                        <vs-tr :state="'success'">
                            <template v-if="ptr.hasColspan == false">
                                <td>{{ ptr.name }}</td>
                                <td :key="index" v-for="(row, index) in ptr.values">{{ row }}</td>
                            </template>
                            <td v-else :colspan="ptr.colspan">{{ ptr.name }}</td>
                        </vs-tr>

                        <template v-if="ptr.children" v-for="(row, index) in ptr.children">

                            <vs-tr class="dt-row-group">
                                <!-- <td></td> -->
                                <td>{{ row.name }}</td>
                                <td>{{ row.entity }}</td>
                                <td>{{ row.entity_member }}</td>
                                <td>
                                    <!-- <center><feather-icon style="cursor: pointer;" @click="openForm" icon="PlusIcon"
                                        svgClasses="w-5 h-5 hover:text-primary stroke-current" /></center> -->
                                </td>
                            </vs-tr>

                            <template v-if="row.children" v-for="row in row.children">

                                <vs-tr class="dt-row-level">
                                    <!-- <td></td> -->
                                    <td>{{ row.name }}</td>
                                    <td>{{ row.entity }}</td>
                                    <td>{{ row.entity_member }}</td>
                                    <td>
                                        <!-- <center><feather-icon style="cursor: pointer;" @click="openForm" icon="PlusIcon"
                                            svgClasses="w-5 h-5 hover:text-primary stroke-current" /></center> -->
                                    </td>
                                </vs-tr>

                                <template v-if="row.children" v-for="row in row.children">
                                    <vs-tr class="dt-row-highlight">
                                        <!-- <td></td> -->
                                        <td>
                                            {{ row.name }}
                                            <center><feather-icon style="cursor: pointer;" @click="openForm"
                                                    icon="PlusIcon"
                                                    svgClasses="w-5 h-5 hover:text-primary stroke-current" /></center>
                                        </td>
                                        <td>{{ row.entity }}</td>
                                        <td>{{ row.entity_member }}</td>
                                        <td>
                                            <center><feather-icon style="cursor: pointer;" @click="openForm"
                                                    icon="PlusIcon"
                                                    svgClasses="w-5 h-5 hover:text-primary stroke-current" /></center>
                                        </td>
                                    </vs-tr>

                                    <template v-for="row in row.children">
                                        <vs-tr>
                                            <!-- <td></td> -->
                                            <td>{{ row.name }}</td>
                                            <td>{{ row.entity }}</td>
                                            <td>{{ row.entity_member }}</td>
                                            <td></td>
                                        </vs-tr>
                                    </template>

                                </template>

                                <!-- <vs-tr :key="index" v-for="(row2, index) in row3.data">
                                <td>{{ row2.name }}</td>
                                <td :key="index3" v-for="(row, index3) in row2.values">{{ row }}</td>
                                <td></td>
                            </vs-tr> -->
                            </template>
                        </template>

                    </tbody>
                </template>
            </vs-table>

            <vs-table :noDataText="$t('dt_empty')" v-if="dataTables.data && dataAttributes.tableStyle == 5" :max-items="dataTables.limit"
                :data="dataTables.data" style="overflow-x: auto">

                <template slot-scope="{data}">

                    <vs-tr>
                        <vs-td :style="style(header)" :key="i" v-for="(header, i) in dataHeaders"
                            :colspan="colspan(header)" :rowspan="rowspan(header)">
                            <span v-if="header.label">
                                <center>{{ $t($planText(header.label)) }} </center>
                            </span>
                            <span v-else>
                                <center>{{ $t($planText(header.label)) }}</center>
                            </span>
                        </vs-td>
                    </vs-tr>

                    <vs-tr class="dt-th-strong" v-if="dataTables.dataHeader.dataHeaders"
                       >
                        <vs-td :key="j" v-for="(header, j) in dataTables.dataHeader.dataHeaders"
                            :colspan="colspan(header)" :rowspan="rowspan(header)">{{ $planText(header.label) }} </vs-td>
                    </vs-tr>

                    <vs-tr class="dt-th-strong" v-if="dataTables.dataHeader.dataSubHeaders"
                       >
                        <vs-td :key="k" v-for="(header, k) in dataTables.dataHeader.dataSubHeaders"
                            :colspan="colspan(header)" :rowspan="rowspan(header)"><small>{{ $planText(header.label) }}</small></vs-td>
                    </vs-tr>

                    <tbody :key="pindextr" v-for="(ptr, pindextr) in data">

                        <vs-tr :state="'success'">
                            <template v-if="dataTables.dataHeader.hasColspan == false">
                                <td>{{ ptr.name }}</td>
                                <td :key="index" v-for="(row, index) in ptr.values">{{ row }}</td>
                            </template>
                            <td v-else :colspan="dataTables.dataHeader.colspan">{{ ptr.name }}</td>
                        </vs-tr>
                        <!-- Level 1 -->
                        <template v-if="ptr.children" v-for="(row, index) in ptr.children">

                            <vs-tr :state="'warning'">
                                <td>{{ row.name }}</td>
                                <td>{{ }}</td>
                                <td>{{ }}</td>
                                <td>{{ }}</td>
                                <td>{{ }}</td>
                                <td>{{ }}</td>
                                <td>{{ }}</td>
                                <td>{{ }}</td>
                                <td>{{ }}</td>
                            </vs-tr>

                            <!-- Indicator -->
                            <template v-for="indicator_row in row.indicator">
                                <vs-tr>
                                    <td>{{ indicator_row.indicator }}</td>
                                    <td>{{ indicator_row.code }}</td>
                                    <td>{{ }}</td>
                                    <td>{{ }}</td>
                                    <td>{{ }}</td>
                                    <td>{{ }}</td>
                                    <td>{{ }}</td>
                                    <td>{{ }}</td>
                                    <td></td>
                                </vs-tr>
                            </template>

                            <!-- Level 2 -->
                            <template v-if="row.children" v-for="row in row.children">

                                <vs-tr :state="'danger'">
                                    <td>{{ row.name }}</td>
                                    <td>{{ }}</td>
                                    <td>{{ }}</td>
                                    <td>{{ }}</td>
                                    <td>{{ }}</td>
                                    <td>{{ }}</td>
                                    <td>{{ }}</td>
                                    <td>{{ }}</td>
                                    <td>
                                        <center><feather-icon style="cursor: pointer;" @click="openForm" icon="PlusIcon"
                                                svgClasses="w-5 h-5 hover:text-primary stroke-current" /></center>
                                    </td>
                                </vs-tr>

                                <!-- Indicator -->
                                <template v-for="indicator_row in row.indicator">
                                    <vs-tr>
                                        <td>{{ indicator_row.indicator }}</td>
                                        <td>{{ indicator_row.code }}</td>
                                        <td>{{ }}</td>
                                        <td>{{ }}</td>
                                        <td>{{ }}</td>
                                        <td>{{ }}</td>
                                        <td>{{ }}</td>
                                        <td>{{ }}</td>
                                        <td>

                                        </td>
                                    </vs-tr>
                                </template>

                                <!-- Level 3 -->
                                <template v-if="row.children" v-for="row in row.children">
                                    <vs-tr class="dt-row-highlight">
                                        <td>{{ row.name }}</td>
                                        <td>{{ }}</td>
                                        <td>{{ }}</td>
                                        <td>{{ }}</td>
                                        <td>{{ }}</td>
                                        <td>{{ }}</td>
                                        <td>{{ }}</td>
                                        <td>{{ }}</td>
                                        <td>
                                            <center><feather-icon style="cursor: pointer;" @click="openForm"
                                                    icon="PlusIcon"
                                                    svgClasses="w-5 h-5 hover:text-primary stroke-current" /></center>
                                        </td>
                                    </vs-tr>

                                    <!-- Level 4 -->
                                    <template v-for="row in row.children">
                                        <vs-tr class="dt-row-highlight">
                                            <td>{{ row.name }}</td>
                                            <td>{{ }}</td>
                                            <td>{{ }}</td>
                                            <td>{{ }}</td>
                                            <td>{{ }}</td>
                                            <td>{{ }}</td>
                                            <td>{{ }}</td>
                                            <td>{{ }}</td>
                                            <td>
                                                <center><feather-icon style="cursor: pointer;" @click="openForm"
                                                        icon="PlusIcon"
                                                        svgClasses="w-5 h-5 hover:text-primary stroke-current" />
                                                </center>
                                            </td>
                                        </vs-tr>
                                    </template>

                                </template>
                            </template>
                        </template>

                    </tbody>
                </template>
            </vs-table>

            <vs-table :noDataText="$t('dt_empty')" v-if="dataTables.data && dataAttributes.tableStyle == 6" :max-items="dataTables.limit"
                :data="dataTables.data" style="overflow-x: auto">
                <template slot-scope="{data}">

                    <vs-tr>
                        <vs-td :style="style(header)" :key="i" v-for="(header, i) in dataHeaders"
                            :colspan="colspan(header)" :rowspan="rowspan(header)">
                            <span v-if="header.label">
                                <center><span v-html="$t($planText(header.label))"></span></center>
                            </span>
                            <span v-else>
                                <center><span v-html="$t($planText(header.label))"></span></center>
                            </span>
                        </vs-td>
                    </vs-tr>

                    <vs-tr class="dt-th-strong" v-if="dataTables.dataHeaders"
                       >
                        <vs-td :style="style(header)" :key="j" v-for="(header, j) in dataTables.dataHeaders"
                            :colspan="colspan(header)" :rowspan="rowspan(header)">{{ $planText(header.label) }} </vs-td>
                    </vs-tr>

                    <vs-tr class="dt-th-strong" v-if="dataTables.dataSubHeaders"
                       >
                        <vs-td :key="k" v-for="(header, k) in dataTables.dataSubHeaders"
                            :colspan="colspan(header)" :rowspan="rowspan(header)"><small>{{ $planText(header.label) }}</small></vs-td>
                    </vs-tr>

                    <tbody :key="pindextr" v-for="(ptr, pindextr) in data">

                        <vs-tr class="dt-row-muted" :key="cindex" v-for="(cusField, cindex) in dataTables.group_fields"
                           >
                            <td :key="scindex" v-for="(scfield, scindex) in ptr[cusField]">
                                <center>{{ scfield }}</center>
                            </td>
                        </vs-tr>

                        <template v-for="parent in ptr.children">
                            <vs-tr class="dt-row-group">
                                <vs-td :key="sindex" v-for="(dataField, sindex) in dataTables.dataFillables">
                                    {{ parent[dataField] }}
                                </vs-td>
                            </vs-tr>

                            <template v-for="child in parent.children">
                                <vs-tr class="dt-row-highlight">
                                    <vs-td :key="sindex" v-for="(dataField, sindex) in dataTables.dataFillables">
                                        {{ child[dataField] }}
                                    </vs-td>
                                </vs-tr>

                                <vs-tr :key="sindex" v-for="(schild, sindex) in child.children">
                                    <vs-td :key="sindex" v-for="(dataField, sindex) in dataTables.dataFillables">
                                        {{ schild[dataField] }}
                                    </vs-td>
                                </vs-tr>
                            </template>
                        </template>
                    </tbody>
                </template>
            </vs-table>

            <vs-table :noDataText="$t('dt_empty')" v-if="dataTables.data && dataAttributes.tableStyle == 7" :max-items="dataTables.limit"
                :data="dataTables.data" style="overflow-x: auto">
                <template slot-scope="{data}">

                    <vs-tr>
                        <vs-td :style="style(header)" :key="i" v-for="(header, i) in dataHeaders"
                            :colspan="colspan(header)" :rowspan="rowspan(header)">
                            <span v-if="header.label">
                                <center><span v-html="$t($planText(header.label))"></span></center>
                            </span>
                            <span v-else>
                                <center><span v-html="$t($planText(header.label))"></span></center>
                            </span>
                        </vs-td>
                    </vs-tr>

                    <vs-tr class="dt-th-strong" v-if="dataTables.dataHeaders"
                       >
                        <vs-td :style="style(header)" :key="j" v-for="(header, j) in dataTables.dataHeaders"
                            :colspan="colspan(header)" :rowspan="rowspan(header)">{{ $planText(header.label) }} </vs-td>
                    </vs-tr>

                    <vs-tr class="dt-th-strong" v-if="dataTables.dataSubHeaders"
                       >
                        <vs-td :key="k" v-for="(header, k) in dataTables.dataSubHeaders"
                            :colspan="colspan(header)" :rowspan="rowspan(header)"><small>{{ $planText(header.label) }}</small></vs-td>
                    </vs-tr>

                    <tbody :key="pindextr" v-for="(ptr, pindextr) in data">

                        <!-- <vs-tr class="dt-row-muted" :key="cindex" v-for="(  cusField, cindex  ) in   dataTables.group_fields  "
                           >
                            <td :key="scindex" v-for="(  scfield, scindex  ) in   ptr[cusField]  ">
                                <center>{{ scfield }}</center>
                            </td>
                        </vs-tr> -->
                        <vs-tr class="dt-row-muted" :key="pindextr">
                            <td colspan="6">{{ ptr.entity.name }}</td>
                        </vs-tr>
                        <template v-for="parent in ptr.children">
                            <vs-tr class="dt-row-group">
                                <vs-td :key="sindex" v-for="(dataField, sindex) in dataTables.dataFillables">
                                    {{ parent[dataField] }}
                                </vs-td>
                            </vs-tr>

                            <template v-for="child in parent.children">
                                <vs-tr class="dt-row-highlight">
                                    <vs-td :key="sindex" v-for="(dataField, sindex) in dataTables.dataFillables">
                                        {{ child[dataField] }}
                                    </vs-td>
                                </vs-tr>

                                <vs-tr :key="sindex" v-for="(schild, sindex) in child.children">
                                    <vs-td :key="sindex" v-for="(dataField, sindex) in dataTables.dataFillables">
                                        {{ schild[dataField] }}
                                    </vs-td>
                                </vs-tr>
                            </template>
                        </template>
                    </tbody>
                </template>
            </vs-table>

            <!-- Income Form -->
            <vs-table :noDataText="$t('dt_empty')" v-if="dataTables.data && dataAttributes.tableStyle == 8" :max-items="dataTables.limit"
                :data="dataTables.data" style="overflow-x: auto">
                <template slot-scope="{data}">

                    <vs-tr>
                        <vs-td :style="style(header)" :key="i" v-for="(header, i) in dataHeaders"
                            :colspan="colspan(header)" :rowspan="rowspan(header)">
                            <span v-if="header.label">
                                <center><span v-html="$t($planText(header.label))"></span></center>
                            </span>
                            <span v-else>
                                <center><span v-html="$t($planText(header.label))"></span></center>
                            </span>
                        </vs-td>
                    </vs-tr>

                    <vs-tr class="dt-th-strong" v-if="dataTables.dataHeaders"
                       >
                        <vs-td :style="style(header)" :key="j" v-for="(header, j) in dataTables.dataHeaders"
                            :colspan="colspan(header)" :rowspan="rowspan(header)">{{ $planText(header.label) }} </vs-td>
                    </vs-tr>

                    <vs-tr class="dt-th-strong" v-if="dataTables.dataSubHeaders"
                       >
                        <vs-td :key="k" v-for="(header, k) in dataTables.dataSubHeaders"
                            :colspan="colspan(header)" :rowspan="rowspan(header)"><small>{{ $planText(header.label) }}</small></vs-td>
                    </vs-tr>

                    <tbody :key="pindextr" v-for="(ptr, pindextr) in data">

                        <vs-tr class="text-danger" :key="cindex" v-for="(cusField, cindex) in dataTables.group_fields"
                           >
                            <td :key="scindex" v-for="(scfield, scindex) in ptr[cusField]">
                                <center>{{ scfield }}</center>
                            </td>
                        </vs-tr>

                        <!-- <template v-for="ctr in ptr"> -->
                        <vs-tr class="dt-row-group">
                            <vs-td colspan="3"></vs-td>
                            <vs-td>{{ ptr.entity }}</vs-td>
                            <vs-td>{{ ptr.finance_law }}</vs-td>
                            <vs-td>{{ ptr.total_amount }}</vs-td>
                            <vs-td>{{ ptr.total_percentage_implementing_rule }}</vs-td>

                            <!-- Semester 1 -->
                            <vs-td>{{ ptr.costing_plan_semester1.year_planning.s1_total_bp }}</vs-td>
                            <vs-td>{{ ptr.costing_plan_semester1.year_planning.s1_bp_jan }}</vs-td>
                            <vs-td>{{ ptr.costing_plan_semester1.year_planning.s1_bp_feb }}</vs-td>
                            <vs-td>{{ ptr.costing_plan_semester1.year_planning.s1_bp_mar }}</vs-td>

                            <vs-td>{{ ptr.costing_plan_semester1.impl_est.s1_total_est_imp }}</vs-td>
                            <vs-td>{{ ptr.costing_plan_semester1.impl_est.s1_est_imp_jan }}</vs-td>
                            <vs-td>{{ ptr.costing_plan_semester1.impl_est.s1_est_imp_feb }}</vs-td>
                            <vs-td>{{ ptr.costing_plan_semester1.impl_est.s1_est_imp_mar }}</vs-td>

                            <!-- Semester 2 -->
                            <vs-td>{{ ptr.costing_plan_semester2.year_planning.s2_total_bp }}</vs-td>
                            <vs-td>{{ ptr.costing_plan_semester2.year_planning.s2_bp_apr }}</vs-td>
                            <vs-td>{{ ptr.costing_plan_semester2.year_planning.s2_bp_may }}</vs-td>
                            <vs-td>{{ ptr.costing_plan_semester2.year_planning.s2_bp_jun }}</vs-td>

                            <vs-td>{{ ptr.costing_plan_semester2.impl_est.s2_total_est_imp }}</vs-td>
                            <vs-td>{{ ptr.costing_plan_semester2.impl_est.s2_est_imp_apr }}</vs-td>
                            <vs-td>{{ ptr.costing_plan_semester2.impl_est.s2_est_imp_may }}</vs-td>
                            <vs-td>{{ ptr.costing_plan_semester2.impl_est.s1_est_imp_jun }}</vs-td>
                            <!-- Semester 3 -->
                            <vs-td>{{ ptr.costing_plan_semester3.year_planning.s3_total_pb }}</vs-td>
                            <vs-td>{{ ptr.costing_plan_semester3.year_planning.s3_bp_jul }}</vs-td>
                            <vs-td>{{ ptr.costing_plan_semester3.year_planning.s3_bp_aug }}</vs-td>
                            <vs-td>{{ ptr.costing_plan_semester3.year_planning.s3_bp_sep }}</vs-td>

                            <vs-td>{{ ptr.costing_plan_semester3.impl_est.s3_total_est_imp }}</vs-td>
                            <vs-td>{{ ptr.costing_plan_semester3.impl_est.s3_est_imp_jul }}</vs-td>
                            <vs-td>{{ ptr.costing_plan_semester3.impl_est.s3_est_imp_aug }}</vs-td>
                            <vs-td>{{ ptr.costing_plan_semester3.impl_est.s3_est_imp_sep }}</vs-td>
                            <!-- Semester 4 -->
                            <vs-td>{{ ptr.costing_plan_semester4.year_planning.s4_total_bp }}</vs-td>
                            <vs-td>{{ ptr.costing_plan_semester4.year_planning.s4_bp_oct }}</vs-td>
                            <vs-td>{{ ptr.costing_plan_semester4.year_planning.s4_bp_nov }}</vs-td>
                            <vs-td>{{ ptr.costing_plan_semester4.year_planning.s4_bp_dec }}</vs-td>

                            <vs-td>{{ ptr.costing_plan_semester4.impl_est.s4_total_est_imp }}</vs-td>
                            <vs-td>{{ ptr.costing_plan_semester4.impl_est.s4_est_imp_oct }}</vs-td>
                            <vs-td>{{ ptr.costing_plan_semester4.impl_est.s4_est_imp_nov }}</vs-td>
                            <vs-td>{{ ptr.costing_plan_semester4.impl_est.s4_est_imp_dec }}</vs-td>

                            <!-- ផ្សេងៗ -->
                            <vs-td>{{ ptr.remark }}</vs-td>
                        </vs-tr>
                        <template v-for="ctr in ptr.children">
                            <!-- <vs-tr>
                                <vs-td :key="sindex" v-for="(dataField, sindex) in dataTables.dataFillables">
                                    <center>{{ parent[dataField] }} </center>
                                </vs-td>
                            </vs-tr> -->
                            <vs-tr class="dt-row-group" style="display: none;">
                                <vs-td colspan="4">{{ ctr.department }}</vs-td>
                                <vs-td>{{ ctr.finance_law }}</vs-td>
                                <vs-td>{{ ctr.total_amount }}</vs-td>
                                <vs-td>{{ ctr.total_percentage_implementing_rule }}</vs-td>

                                <!-- Semester 1 -->
                                <vs-td>{{ ctr.costing_plan_semester1.year_planning.s1_total_bp }}</vs-td>
                                <vs-td>{{ ctr.costing_plan_semester1.year_planning.s1_bp_jan }}</vs-td>
                                <vs-td>{{ ctr.costing_plan_semester1.year_planning.s1_bp_feb }}</vs-td>
                                <vs-td>{{ ctr.costing_plan_semester1.year_planning.s1_bp_mar }}</vs-td>

                                <vs-td>{{ ctr.costing_plan_semester1.impl_est.s1_total_est_imp }}</vs-td>
                                <vs-td>{{ ctr.costing_plan_semester1.impl_est.s1_est_imp_jan }}</vs-td>
                                <vs-td>{{ ctr.costing_plan_semester1.impl_est.s1_est_imp_feb }}</vs-td>
                                <vs-td>{{ ctr.costing_plan_semester1.impl_est.s1_est_imp_mar }}</vs-td>

                                <!-- Semester 2 -->
                                <vs-td>{{ ctr.costing_plan_semester2.year_planning.s2_total_bp }}</vs-td>
                                <vs-td>{{ ctr.costing_plan_semester2.year_planning.s2_bp_apr }}</vs-td>
                                <vs-td>{{ ctr.costing_plan_semester2.year_planning.s2_bp_may }}</vs-td>
                                <vs-td>{{ ctr.costing_plan_semester2.year_planning.s2_bp_jun }}</vs-td>

                                <vs-td>{{ ctr.costing_plan_semester2.impl_est.s2_total_est_imp }}</vs-td>
                                <vs-td>{{ ctr.costing_plan_semester2.impl_est.s2_est_imp_apr }}</vs-td>
                                <vs-td>{{ ctr.costing_plan_semester2.impl_est.s2_est_imp_may }}</vs-td>
                                <vs-td>{{ ctr.costing_plan_semester2.impl_est.s1_est_imp_jun }}</vs-td>
                                <!-- Semester 3 -->
                                <vs-td>{{ ctr.costing_plan_semester3.year_planning.s3_total_pb }}</vs-td>
                                <vs-td>{{ ctr.costing_plan_semester3.year_planning.s3_bp_jul }}</vs-td>
                                <vs-td>{{ ctr.costing_plan_semester3.year_planning.s3_bp_aug }}</vs-td>
                                <vs-td>{{ ctr.costing_plan_semester3.year_planning.s3_bp_sep }}</vs-td>

                                <vs-td>{{ ctr.costing_plan_semester3.impl_est.s3_total_est_imp }}</vs-td>
                                <vs-td>{{ ctr.costing_plan_semester3.impl_est.s3_est_imp_jul }}</vs-td>
                                <vs-td>{{ ctr.costing_plan_semester3.impl_est.s3_est_imp_aug }}</vs-td>
                                <vs-td>{{ ctr.costing_plan_semester3.impl_est.s3_est_imp_sep }}</vs-td>
                                <!-- Semester 4 -->
                                <vs-td>{{ ctr.costing_plan_semester4.year_planning.s4_total_bp }}</vs-td>
                                <vs-td>{{ ctr.costing_plan_semester4.year_planning.s4_bp_oct }}</vs-td>
                                <vs-td>{{ ctr.costing_plan_semester4.year_planning.s4_bp_nov }}</vs-td>
                                <vs-td>{{ ctr.costing_plan_semester4.year_planning.s4_bp_dec }}</vs-td>

                                <vs-td>{{ ctr.costing_plan_semester4.impl_est.s4_total_est_imp }}</vs-td>
                                <vs-td>{{ ctr.costing_plan_semester4.impl_est.s4_est_imp_oct }}</vs-td>
                                <vs-td>{{ ctr.costing_plan_semester4.impl_est.s4_est_imp_nov }}</vs-td>
                                <vs-td>{{ ctr.costing_plan_semester4.impl_est.s4_est_imp_dec }}</vs-td>

                                <!-- ផ្សេងៗ -->
                                <vs-td>{{ ctr.remark }}</vs-td>
                            </vs-tr>

                            <!--/*** cctr children in loop children table row data  */ -->
                            <template v-for="cctr in ctr.children">
                                <vs-tr>
                                    <vs-td>{{ cctr.account_group.code }}</vs-td>
                                    <vs-td>{{ cctr.account.code }}</vs-td>
                                    <vs-td>{{ cctr.sub_account.code }}</vs-td>
                                    <vs-td>{{ cctr.sub_account.name }}</vs-td>
                                    <vs-td>{{ cctr.finance_law }}</vs-td>
                                    <vs-td>{{ cctr.total_amount }}</vs-td>
                                    <vs-td>{{ cctr.total_percentage_implementing_rule }}</vs-td>

                                    <!-- Semester 1 -->
                                    <vs-td>{{ cctr.costing_plan_semester1.year_planning.s1_total_bp }}</vs-td>
                                    <vs-td>{{ cctr.costing_plan_semester1.year_planning.s1_bp_jan }}</vs-td>
                                    <vs-td>{{ cctr.costing_plan_semester1.year_planning.s1_bp_feb }}</vs-td>
                                    <vs-td>{{ cctr.costing_plan_semester1.year_planning.s1_bp_mar }}</vs-td>

                                    <vs-td>{{ cctr.costing_plan_semester1.impl_est.s1_total_est_imp }}</vs-td>
                                    <vs-td>{{ cctr.costing_plan_semester1.impl_est.s1_est_imp_jan }}</vs-td>
                                    <vs-td>{{ cctr.costing_plan_semester1.impl_est.s1_est_imp_feb }}</vs-td>
                                    <vs-td>{{ cctr.costing_plan_semester1.impl_est.s1_est_imp_mar }}</vs-td>

                                    <!-- Semester 2 -->
                                    <vs-td>{{ cctr.costing_plan_semester2.year_planning.s2_total_bp }}</vs-td>
                                    <vs-td>{{ cctr.costing_plan_semester2.year_planning.s2_bp_apr }}</vs-td>
                                    <vs-td>{{ cctr.costing_plan_semester2.year_planning.s2_bp_may }}</vs-td>
                                    <vs-td>{{ cctr.costing_plan_semester2.year_planning.s2_bp_jun }}</vs-td>

                                    <vs-td>{{ cctr.costing_plan_semester2.impl_est.s2_total_est_imp }}</vs-td>
                                    <vs-td>{{ cctr.costing_plan_semester2.impl_est.s2_est_imp_apr }}</vs-td>
                                    <vs-td>{{ cctr.costing_plan_semester2.impl_est.s2_est_imp_may }}</vs-td>
                                    <vs-td>{{ cctr.costing_plan_semester2.impl_est.s1_est_imp_jun }}</vs-td>
                                    <!-- Semester 3 -->
                                    <vs-td>{{ cctr.costing_plan_semester3.year_planning.s3_total_pb }}</vs-td>
                                    <vs-td>{{ cctr.costing_plan_semester3.year_planning.s3_bp_jul }}</vs-td>
                                    <vs-td>{{ cctr.costing_plan_semester3.year_planning.s3_bp_aug }}</vs-td>
                                    <vs-td>{{ cctr.costing_plan_semester3.year_planning.s3_bp_sep }}</vs-td>

                                    <vs-td>{{ cctr.costing_plan_semester3.impl_est.s3_total_est_imp }}</vs-td>
                                    <vs-td>{{ cctr.costing_plan_semester3.impl_est.s3_est_imp_jul }}</vs-td>
                                    <vs-td>{{ cctr.costing_plan_semester3.impl_est.s3_est_imp_aug }}</vs-td>
                                    <vs-td>{{ cctr.costing_plan_semester3.impl_est.s3_est_imp_sep }}</vs-td>
                                    <!-- Semester 4 -->
                                    <vs-td>{{ cctr.costing_plan_semester4.year_planning.s4_total_bp }}</vs-td>
                                    <vs-td>{{ cctr.costing_plan_semester4.year_planning.s4_bp_oct }}</vs-td>
                                    <vs-td>{{ cctr.costing_plan_semester4.year_planning.s4_bp_nov }}</vs-td>
                                    <vs-td>{{ cctr.costing_plan_semester4.year_planning.s4_bp_dec }}</vs-td>

                                    <vs-td>{{ cctr.costing_plan_semester4.impl_est.s4_total_est_imp }}</vs-td>
                                    <vs-td>{{ cctr.costing_plan_semester4.impl_est.s4_est_imp_oct }}</vs-td>
                                    <vs-td>{{ cctr.costing_plan_semester4.impl_est.s4_est_imp_nov }}</vs-td>
                                    <vs-td>{{ cctr.costing_plan_semester4.impl_est.s4_est_imp_dec }}</vs-td>


                                    <!-- ផ្សេងៗ -->
                                    <vs-td>{{ cctr.remark }}</vs-td>
                                </vs-tr>
                            </template>
                            <!-- <template v-for="child in parent.children">
                                <vs-tr>
                                    <vs-td :key="sindex" v-for="(dataField, sindex) in dataTables.dataFillables">
                                        <center>{{ child[dataField] }} </center>
                                    </vs-td>
                                </vs-tr>

                                <vs-tr :key="sindex" v-for="(schild, sindex) in child.children">
                                    <vs-td :key="sindex" v-for="(dataField, sindex) in dataTables.dataFillables">
                                        <center>{{ schild[dataField] }} </center>
                                    </vs-td>
                                </vs-tr>
                            </template> -->
                        </template>
                        <!-- </template> -->
                    </tbody>
                </template>
            </vs-table>

            <!-- Expense Form  -->
            <vs-table :noDataText="$t('dt_empty')" v-if="dataTables.data && dataAttributes.tableStyle == 9" :max-items="dataTables.limit"
                :data="dataTables.data" style="overflow-x: auto">
                <template slot-scope="{data}">

                    <vs-tr>
                        <vs-td :style="style(header)" :key="i" v-for="(header, i) in dataHeaders"
                            :colspan="colspan(header)" :rowspan="rowspan(header)">
                            <span v-if="header.label">
                                <center><span v-html="$t($planText(header.label))"></span></center>
                            </span>
                            <span v-else>
                                <center><span v-html="$t($planText(header.label))"></span></center>
                            </span>
                        </vs-td>
                    </vs-tr>

                    <vs-tr class="dt-th-strong" v-if="dataTables.dataHeaders"
                       >
                        <vs-td :style="style(header)" :key="j" v-for="(header, j) in dataTables.dataHeaders"
                            :colspan="colspan(header)" :rowspan="rowspan(header)">
                            <center>{{ $planText(header.label) }}</center>
                        </vs-td>
                    </vs-tr>

                    <vs-tr class="dt-th-strong" v-if="dataTables.dataSubHeaders"
                       >
                        <vs-td :key="k" v-for="(header, k) in dataTables.dataSubHeaders"
                            :colspan="colspan(header)" :rowspan="rowspan(header)"><small>{{ $planText(header.label) }}</small></vs-td>
                    </vs-tr>

                    <tbody :key="pindextr" v-for="(ptr, pindextr) in data">

                        <vs-tr class="text-danger" :key="cindex" v-for="(cusField, cindex) in dataTables.group_fields"
                           >
                            <td :key="scindex" v-for="(scfield, scindex) in ptr[cusField]">
                                <center>{{ scfield }}</center>
                            </td>
                        </vs-tr>

                        <!-- <template v-for="ctr in ptr"> -->
                        <vs-tr class="dt-th-strong">
                            <vs-td colspan="4">{{ ptr.entity }}</vs-td>
                            <vs-td>{{ ptr.finance_law }}</vs-td>
                            <vs-td>{{ ptr.total_amount }}</vs-td>
                            <vs-td>{{ ptr.total_percentage_implementing_rule }}</vs-td>

                            <!-- Semester 1 -->
                            <vs-td>{{ ptr.costing_plan_semester1.year_planning.total_amount }}</vs-td>
                            <vs-td>{{ ptr.costing_plan_semester1.year_planning.amount_plan_month_1 }}</vs-td>
                            <vs-td>{{ ptr.costing_plan_semester1.year_planning.amount_plan_month_2 }}</vs-td>
                            <vs-td>{{ ptr.costing_plan_semester1.year_planning.amount_plan_month_3 }}</vs-td>

                            <vs-td>{{ ptr.costing_plan_semester1.impl_est.impl_est_amount }}</vs-td>
                            <vs-td>{{ ptr.costing_plan_semester1.impl_est.imple_amount_plan_month_1 }}</vs-td>
                            <vs-td>{{ ptr.costing_plan_semester1.impl_est.imple_amount_plan_month_2 }}</vs-td>
                            <vs-td>{{ ptr.costing_plan_semester1.impl_est.imple_amount_plan_month_3 }}</vs-td>

                            <!-- Semester 2 -->
                            <vs-td>{{ ptr.costing_plan_semester2.year_planning.total_amount }}</vs-td>
                            <vs-td>{{ ptr.costing_plan_semester2.year_planning.amount_plan_month_4 }}</vs-td>
                            <vs-td>{{ ptr.costing_plan_semester2.year_planning.amount_plan_month_5 }}</vs-td>
                            <vs-td>{{ ptr.costing_plan_semester2.year_planning.amount_plan_month_6 }}</vs-td>

                            <vs-td>{{ ptr.costing_plan_semester2.impl_est.impl_est_amount }}</vs-td>
                            <vs-td>{{ ptr.costing_plan_semester2.impl_est.imple_amount_plan_month_4 }}</vs-td>
                            <vs-td>{{ ptr.costing_plan_semester2.impl_est.imple_amount_plan_month_5 }}</vs-td>
                            <vs-td>{{ ptr.costing_plan_semester2.impl_est.imple_amount_plan_month_6 }}</vs-td>
                            <!-- Semester 3 -->
                            <vs-td>{{ ptr.costing_plan_semester3.year_planning.total_amount }}</vs-td>
                            <vs-td>{{ ptr.costing_plan_semester3.year_planning.amount_plan_month_7 }}</vs-td>
                            <vs-td>{{ ptr.costing_plan_semester3.year_planning.amount_plan_month_8 }}</vs-td>
                            <vs-td>{{ ptr.costing_plan_semester3.year_planning.amount_plan_month_9 }}</vs-td>

                            <vs-td>{{ ptr.costing_plan_semester3.impl_est.impl_est_amount }}</vs-td>
                            <vs-td>{{ ptr.costing_plan_semester3.impl_est.imple_amount_plan_month_7 }}</vs-td>
                            <vs-td>{{ ptr.costing_plan_semester3.impl_est.imple_amount_plan_month_8 }}</vs-td>
                            <vs-td>{{ ptr.costing_plan_semester3.impl_est.imple_amount_plan_month_9 }}</vs-td>
                            <!-- Semester 4 -->
                            <vs-td>{{ ptr.costing_plan_semester4.year_planning.total_amount }}</vs-td>
                            <vs-td>{{ ptr.costing_plan_semester4.year_planning.amount_plan_month_10 }}</vs-td>
                            <vs-td>{{ ptr.costing_plan_semester4.year_planning.amount_plan_month_11 }}</vs-td>
                            <vs-td>{{ ptr.costing_plan_semester4.year_planning.amount_plan_month_12 }}</vs-td>

                            <vs-td>{{ ptr.costing_plan_semester4.impl_est.impl_est_amount }}</vs-td>
                            <vs-td>{{ ptr.costing_plan_semester4.impl_est.imple_amount_plan_month_10 }}</vs-td>
                            <vs-td>{{ ptr.costing_plan_semester4.impl_est.imple_amount_plan_month_11 }}</vs-td>
                            <vs-td>{{ ptr.costing_plan_semester4.impl_est.imple_amount_plan_month_12 }}</vs-td>

                            <!-- ផ្សេងៗ -->
                            <vs-td>{{ ptr.remark }}</vs-td>
                        </vs-tr>
                        <template v-for="ctr in ptr.children">
                            <!-- <vs-tr>
                                <vs-td :key="sindex" v-for="(dataField, sindex) in dataTables.dataFillables">
                                    <center>{{ parent[dataField] }} </center>
                                </vs-td>
                            </vs-tr> -->
                            <vs-tr class="dt-row-group">
                                <vs-td colspan="4">{{ ctr.department }}</vs-td>
                                <vs-td>{{ ctr.finance_law }}</vs-td>
                                <vs-td>{{ ctr.total_amount }}</vs-td>
                                <vs-td>{{ ctr.total_percentage_implementing_rule }}</vs-td>

                                <!-- Semester 1 -->
                                <vs-td>{{ cctr.costing_plan_semester1.year_planning.total_amount }}</vs-td>
                                <vs-td>{{ cctr.costing_plan_semester1.year_planning.amount_plan_month_1 }}</vs-td>
                                <vs-td>{{ cctr.costing_plan_semester1.year_planning.amount_plan_month_2 }}</vs-td>
                                <vs-td>{{ cctr.costing_plan_semester1.year_planning.amount_plan_month_3 }}</vs-td>

                                <vs-td>{{ cctr.costing_plan_semester1.impl_est.impl_est_amount }}</vs-td>
                                <vs-td>{{ cctr.costing_plan_semester1.impl_est.imple_amount_plan_month_1 }}</vs-td>
                                <vs-td>{{ cctr.costing_plan_semester1.impl_est.imple_amount_plan_month_2 }}</vs-td>
                                <vs-td>{{ cctr.costing_plan_semester1.impl_est.imple_amount_plan_month_3 }}</vs-td>

                                <!-- Semester 2 -->
                                <vs-td>{{ cctr.costing_plan_semester2.year_planning.total_amount }}</vs-td>
                                <vs-td>{{ cctr.costing_plan_semester2.year_planning.amount_plan_month_4 }}</vs-td>
                                <vs-td>{{ cctr.costing_plan_semester2.year_planning.amount_plan_month_5 }}</vs-td>
                                <vs-td>{{ cctr.costing_plan_semester2.year_planning.amount_plan_month_6 }}</vs-td>

                                <vs-td>{{ cctr.costing_plan_semester2.impl_est.impl_est_amount }}</vs-td>
                                <vs-td>{{ cctr.costing_plan_semester2.impl_est.imple_amount_plan_month_4 }}</vs-td>
                                <vs-td>{{ cctr.costing_plan_semester2.impl_est.imple_amount_plan_month_5 }}</vs-td>
                                <vs-td>{{ cctr.costing_plan_semester2.impl_est.imple_amount_plan_month_6 }}</vs-td>
                                <!-- Semester 3 -->
                                <vs-td>{{ cctr.costing_plan_semester3.year_planning.total_amount }}</vs-td>
                                <vs-td>{{ cctr.costing_plan_semester3.year_planning.amount_plan_month_7 }}</vs-td>
                                <vs-td>{{ cctr.costing_plan_semester3.year_planning.amount_plan_month_8 }}</vs-td>
                                <vs-td>{{ cctr.costing_plan_semester3.year_planning.amount_plan_month_9 }}</vs-td>

                                <vs-td>{{ cctr.costing_plan_semester3.impl_est.impl_est_amount }}</vs-td>
                                <vs-td>{{ cctr.costing_plan_semester3.impl_est.imple_amount_plan_month_7 }}</vs-td>
                                <vs-td>{{ cctr.costing_plan_semester3.impl_est.imple_amount_plan_month_8 }}</vs-td>
                                <vs-td>{{ cctr.costing_plan_semester3.impl_est.imple_amount_plan_month_9 }}</vs-td>
                                <!-- Semester 4 -->
                                <vs-td>{{ cctr.costing_plan_semester4.year_planning.total_amount }}</vs-td>
                                <vs-td>{{ cctr.costing_plan_semester4.year_planning.amount_plan_month_10 }}</vs-td>
                                <vs-td>{{ cctr.costing_plan_semester4.year_planning.amount_plan_month_11 }}</vs-td>
                                <vs-td>{{ cctr.costing_plan_semester4.year_planning.amount_plan_month_12 }}</vs-td>

                                <vs-td>{{ cctr.costing_plan_semester4.impl_est.impl_est_amount }}</vs-td>
                                <vs-td>{{ cctr.costing_plan_semester4.impl_est.imple_amount_plan_month_10 }}</vs-td>
                                <vs-td>{{ cctr.costing_plan_semester4.impl_est.imple_amount_plan_month_11 }}</vs-td>
                                <vs-td>{{ cctr.costing_plan_semester4.impl_est.imple_amount_plan_month_12 }}</vs-td>

                                <!-- ផ្សេងៗ -->
                                <vs-td>{{ ctr.remark }}</vs-td>
                            </vs-tr>

                            <!--/*** cctr children in loop children table row data  */ -->
                            <template v-for="cctr in ctr.children">
                                <vs-tr>
                                    <vs-td>{{ cctr.account_group.code + '-' + cctr.account_group.name }}</vs-td>
                                    <vs-td>{{ cctr.account.code + "-" + cctr.account.name }}</vs-td>
                                    <vs-td>{{ cctr.sub_account.code + "-" + cctr.sub_account.name }}</vs-td>
                                    <vs-td>{{ ctr.department }}</vs-td>
                                    <vs-td>{{ cctr.finance_law }}</vs-td>
                                    <vs-td>{{ cctr.total_amount }}</vs-td>
                                    <vs-td>{{ cctr.total_percentage_implementing_rule }}</vs-td>

                                    <!-- Semester 1 -->
                                    <vs-td>{{ cctr.costing_plan_semester1.year_planning.total_amount }}</vs-td>
                                    <vs-td>{{ cctr.costing_plan_semester1.year_planning.amount_plan_month_1 }}</vs-td>
                                    <vs-td>{{ cctr.costing_plan_semester1.year_planning.amount_plan_month_2 }}</vs-td>
                                    <vs-td>{{ cctr.costing_plan_semester1.year_planning.amount_plan_month_3 }}</vs-td>

                                    <vs-td>{{ cctr.costing_plan_semester1.impl_est.impl_est_amount }}</vs-td>
                                    <vs-td>{{ cctr.costing_plan_semester1.impl_est.imple_amount_plan_month_1 }}</vs-td>
                                    <vs-td>{{ cctr.costing_plan_semester1.impl_est.imple_amount_plan_month_2 }}</vs-td>
                                    <vs-td>{{ cctr.costing_plan_semester1.impl_est.imple_amount_plan_month_3 }}</vs-td>

                                    <!-- Semester 2 -->
                                    <vs-td>{{ cctr.costing_plan_semester2.year_planning.total_amount }}</vs-td>
                                    <vs-td>{{ cctr.costing_plan_semester2.year_planning.amount_plan_month_4 }}</vs-td>
                                    <vs-td>{{ cctr.costing_plan_semester2.year_planning.amount_plan_month_5 }}</vs-td>
                                    <vs-td>{{ cctr.costing_plan_semester2.year_planning.amount_plan_month_6 }}</vs-td>

                                    <vs-td>{{ cctr.costing_plan_semester2.impl_est.impl_est_amount }}</vs-td>
                                    <vs-td>{{ cctr.costing_plan_semester2.impl_est.imple_amount_plan_month_4 }}</vs-td>
                                    <vs-td>{{ cctr.costing_plan_semester2.impl_est.imple_amount_plan_month_5 }}</vs-td>
                                    <vs-td>{{ cctr.costing_plan_semester2.impl_est.imple_amount_plan_month_6 }}</vs-td>
                                    <!-- Semester 3 -->
                                    <vs-td>{{ cctr.costing_plan_semester3.year_planning.total_amount }}</vs-td>
                                    <vs-td>{{ cctr.costing_plan_semester3.year_planning.amount_plan_month_7 }}</vs-td>
                                    <vs-td>{{ cctr.costing_plan_semester3.year_planning.amount_plan_month_8 }}</vs-td>
                                    <vs-td>{{ cctr.costing_plan_semester3.year_planning.amount_plan_month_9 }}</vs-td>

                                    <vs-td>{{ cctr.costing_plan_semester3.impl_est.impl_est_amount }}</vs-td>
                                    <vs-td>{{ cctr.costing_plan_semester3.impl_est.imple_amount_plan_month_7 }}</vs-td>
                                    <vs-td>{{ cctr.costing_plan_semester3.impl_est.imple_amount_plan_month_8 }}</vs-td>
                                    <vs-td>{{ cctr.costing_plan_semester3.impl_est.imple_amount_plan_month_9 }}</vs-td>
                                    <!-- Semester 4 -->
                                    <vs-td>{{ cctr.costing_plan_semester4.year_planning.total_amount }}</vs-td>
                                    <vs-td>{{ cctr.costing_plan_semester4.year_planning.amount_plan_month_10 }}</vs-td>
                                    <vs-td>{{ cctr.costing_plan_semester4.year_planning.amount_plan_month_11 }}</vs-td>
                                    <vs-td>{{ cctr.costing_plan_semester4.year_planning.amount_plan_month_12 }}</vs-td>

                                    <vs-td>{{ cctr.costing_plan_semester4.impl_est.impl_est_amount }}</vs-td>
                                    <vs-td>{{ cctr.costing_plan_semester4.impl_est.imple_amount_plan_month_10 }}</vs-td>
                                    <vs-td>{{ cctr.costing_plan_semester4.impl_est.imple_amount_plan_month_11 }}</vs-td>
                                    <vs-td>{{ cctr.costing_plan_semester4.impl_est.imple_amount_plan_month_12 }}</vs-td>

                                    <!-- ផ្សេងៗ -->
                                    <vs-td>{{ cctr.remark }}</vs-td>
                                </vs-tr>
                            </template>
                            <!-- <template v-for="child in parent.children">
                                <vs-tr>
                                    <vs-td :key="sindex" v-for="(dataField, sindex) in dataTables.dataFillables">
                                        <center>{{ child[dataField] }} </center>
                                    </vs-td>
                                </vs-tr>

                                <vs-tr :key="sindex" v-for="(schild, sindex) in child.children">
                                    <vs-td :key="sindex" v-for="(dataField, sindex) in dataTables.dataFillables">
                                        <center>{{ schild[dataField] }} </center>
                                    </vs-td>
                                </vs-tr>
                            </template> -->
                        </template>
                        <!-- </template> -->
                    </tbody>
                </template>
            </vs-table>

            <vs-table :noDataText="$t('dt_empty')" v-if="dataTables.data && dataAttributes.tableStyle == 10" :max-items="dataTables.limit"
                :data="dataTables.data" style="overflow-x: auto">
                <template slot-scope="{data}">
                    <vs-tr>
                        <vs-td :style="style(header)" :key="i" v-for="(header, i) in dataHeaders"
                            :colspan="colspan(header)" :rowspan="rowspan(header)">
                            <span v-if="header.label">
                                <center>{{ $t($planText(header.label)) }} </center>
                            </span>
                            <span v-else>
                                <center>{{ $t($planText(header.label)) }}</center>
                            </span>
                        </vs-td>
                    </vs-tr>

                    <vs-tr class="dt-th-strong" v-if="dataTables.dataHeaders"
                       >
                        <vs-td :key="j" v-for="(header, j) in dataTables.dataHeaders" :colspan="colspan(header)"
                            :rowspan="rowspan(header)">{{ $t($planText(header.label)) }} </vs-td>
                    </vs-tr>

                    <vs-tr class="dt-th-muted" v-if="dataTables.dataSubHeaders"
                       >
                        <vs-td :key="k" v-for="(header, k) in dataTables.dataSubHeaders"
                            :colspan="colspan(header)" :rowspan="rowspan(header)">
                            <center><small>{{ $t($planText(header.label)) }}</small></center>
                        </vs-td>
                    </vs-tr>

                    <tbody :key="pindextr" v-for="(ptr, pindextr) in data">
                        <vs-tr :state="'success'">
                            <template v-if="ptr.hasColspan == false">
                                <td>{{ ptr.name }}</td>
                                <td :key="index" v-for="(row, index) in ptr.values">{{ row }}</td>
                            </template>
                            <td v-else :colspan="ptr.colspan">{{ ptr.name }}</td>
                        </vs-tr>

                        <template v-for="row in ptr.data">
                            <vs-tr :state="'primary'">
                                <td>{{ row.summary.name }}</td>
                                <td :key="index" v-for="(row, index) in row.summary.values">{{ row }}</td>
                                <td></td>
                            </vs-tr>

                            <template v-for="row in row.children">
                                <vs-tr :state="'warning'">
                                    <td>{{ row.name }} <feather-icon style="cursor: pointer;"
                                            @click.stop="initAction(rowBtnAction, row, 'Edit')" icon="PlusIcon"
                                            svgClasses="w-5 h-5 hover:text-primary stroke-current" />
                                    </td>
                                    <td :key="index1" v-for="(row, index1) in row.values">{{ row }} </td>
                                    <td>
                                        <center><feather-icon style="cursor: pointer;"
                                                @click.stop="initAction(rowBtnAction, row, 'Edit')" icon="PlusIcon"
                                                svgClasses="w-5 h-5 hover:text-primary stroke-current" />
                                        </center>
                                    </td>
                                </vs-tr>
                                <vs-tr :key="index" v-for="(row2, index) in row.data">
                                    <td>{{ row2.name }}</td>
                                    <td :key="index3" v-for="(row, index3) in row2.values">{{ row }}</td>
                                    <td></td>
                                </vs-tr>

                                <template v-for="row3 in row.dataDetails">
                                    <vs-tr class="dt-row-highlight">
                                        <td>
                                            {{ row3.name }}
                                            <template v-if="dataAttributes.actionButton">
                                                <feather-icon style="cursor: pointer;"
                                                    v-for="rowBtnAction in dataAttributes.actionButton"
                                                    :key="rowBtnAction.indextr" :icon="rowBtnAction.icon"
                                                    svgClasses="mr-2 w-5 h-5 hover:text-primary stroke-current"
                                                    @click.stop="initAction(rowBtnAction, row3, rowBtnAction.method)" />
                                            </template>
                                            <!-- <feather-icon style="cursor: pointer;" icon="EditIcon"
                            svgClasses="w-5 h-5 hover:text-primary stroke-current" @click.stop="initEdit(tr)" /> -->
                                        </td>
                                        <td :key="index4" v-for="(row, index4) in row3.values">{{ row }}</td>
                                        <td>
                                            <!-- <center><feather-icon style="cursor: pointer;" icon="EditIcon"
                                                svgClasses="w-5 h-5 hover:text-primary stroke-current"
                                                @click.stop="initEdit(row3)" /></center> -->
                                            <template v-if="dataAttributes.actionButton">
                                                <feather-icon style="cursor: pointer;"
                                                    v-for="rowBtnAction in dataAttributes.actionButton"
                                                    :key="rowBtnAction.indextr" :icon="rowBtnAction.icon"
                                                    svgClasses="mr-2 w-5 h-5 hover:text-primary stroke-current"
                                                    @click.stop="viewUrl(rowBtnAction)" />
                                            </template>
                                        </td>
                                    </vs-tr>

                                    <vs-tr :key="index" v-for="(row4, index) in row3.data">
                                        <td>{{ row4.name }}</td>
                                        <td :key="index3" v-for="(row, index3) in row4.values">{{ row }}</td>
                                        <td></td>
                                    </vs-tr>
                                </template>
                            </template>
                        </template>

                    </tbody>
                </template>
            </vs-table>

            <vs-table :noDataText="$t('dt_empty')" v-if="dataTables.data && dataAttributes.tableStyle == 11" :max-items="dataTables.limit"
                :data="dataTables.data" style="overflow-x: auto">
                <template slot-scope="{data}">

                    <vs-tr>
                        <vs-td :style="style(header)" :key="i" v-for="(header, i) in dataHeaders"
                            :colspan="colspan(header)" :rowspan="rowspan(header)">
                            <span v-if="header.label">
                                <center><span v-html="$t($planText(header.label))"></span></center>
                            </span>
                            <span v-else>
                                <center><span v-html="$t($planText(header.label))"></span></center>
                            </span>
                        </vs-td>
                    </vs-tr>

                    <vs-tr class="dt-th-strong" v-if="dataTables.dataHeaders"
                       >
                        <vs-td :style="style(header)" :key="j" v-for="(header, j) in dataTables.dataHeaders"
                            :colspan="colspan(header)" :rowspan="rowspan(header)">{{ $planText(header.label) }} </vs-td>
                    </vs-tr>

                    <vs-tr class="dt-th-strong" v-if="dataTables.dataSubHeaders"
                       >
                        <vs-td :key="k" v-for="(header, k) in dataTables.dataSubHeaders"
                            :colspan="colspan(header)" :rowspan="rowspan(header)"><small>{{ $planText(header.label) }}</small></vs-td>
                    </vs-tr>

                    <tbody :key="pindextr" v-for="(ptr, pindextr) in data">

                        <vs-tr class="dt-row-muted" :key="cindex" v-for="(cusField, cindex) in dataTables.group_fields"
                           >
                            <td :key="scindex" v-for="(scfield, scindex) in ptr[cusField]">
                                <center>{{ scfield }}</center>
                            </td>
                        </vs-tr>

                        <template v-for="parent in ptr.children">
                            <vs-tr class="dt-row-level">
                                <vs-td :key="sindex" v-for="(dataField, sindex) in dataTables.dataFillables">
                                    {{ parent[dataField] }}
                                    <template v-if="dataField == 'desc'">
                                        <!-- <feather-icon style="cursor: pointer;" :key="sindex" icon="PlusIcon"
                                        svgClasses="mr-2 w-5 h-5 hover:text-primary stroke-current"
                                        @click="openPrivateForm('')" /> -->
                                    </template>
                                </vs-td>
                                <vs-td>

                                </vs-td>
                            </vs-tr>

                            <template v-for="child in parent.children">
                                <vs-tr class="dt-row-highlight">
                                    <vs-td :key="sindex" v-for="(dataField, sindex) in dataTables.dataFillables">
                                        <span v-if="child[dataField] == 'desc'">> {{ child[dataField] }}</span>
                                        <span v-else>{{ child[dataField] }}</span>
                                    </vs-td>
                                    <vs-td></vs-td>
                                </vs-tr>

                                <template v-for="(schild, sindex) in child.children">
                                    <vs-tr class="dt-row-muted">
                                        <vs-td :key="sindex"
                                            v-for="(dataField, sindex) in dataTables.dataFillables">
                                            <span v-if="v == 'desc'">> {{ schild[dataField] }}</span>
                                            <span v-else>{{ schild[dataField] }}</span>
                                        </vs-td>
                                        <vs-td>

                                        </vs-td>
                                    </vs-tr>

                                    <template v-for="(schild2, sindex) in schild.children">
                                        <vs-tr class="dt-row-muted">
                                            <vs-td :key="sindex"
                                                v-for="(dataField, sindex) in dataTables.dataFillables">
                                                {{ schild2[dataField] }}
                                                <template v-if="dataField == 'desc'">
                                                    <feather-icon style="cursor: pointer;" :key="sindex"
                                                        icon="DollarSignIcon"
                                                        svgClasses="mr-2 w-5 h-5 hover:text-primary stroke-current"
                                                        @click.stop="initAction(rowBtnAction, schild2, 'PrivateForm')" />
                                                    <feather-icon style="cursor: pointer;" :key="sindex" icon="PlusIcon"
                                                        svgClasses="mr-2 w-5 h-5 hover:text-primary stroke-current"
                                                        @click.stop="initAction(rowBtnAction, schild2, 'Edit')" />
                                                </template>
                                            </vs-td>
                                            <vs-td>
                                                <feather-icon style="cursor: pointer;" :key="sindex"
                                                    icon="DollarSignIcon"
                                                    svgClasses="mr-2 w-5 h-5 hover:text-primary stroke-current"
                                                    @click.stop="initAction(rowBtnAction, schild2, 'PrivateForm')" />
                                                <feather-icon style="cursor: pointer;" :key="sindex" icon="PlusIcon"
                                                    svgClasses="mr-2 w-5 h-5 hover:text-primary stroke-current"
                                                    @click.stop="initAction(rowBtnAction, schild2, 'Edit')" />
                                            </vs-td>
                                        </vs-tr>

                                        <template v-for="(schild3, ssindex) in schild2.children">
                                            <vs-tr>
                                                <vs-td :key="ssindex"
                                                    v-for="(dataField, ssindex) in dataTables.dataFillables">
                                                    {{ schild3[dataField] }}
                                                </vs-td>
                                                <vs-td>
                                                    <template v-if="dataAttributes.actionButton">
                                                        <feather-icon style="cursor: pointer;"
                                                            v-for="rowBtnAction in dataAttributes.actionButton"
                                                            :key="rowBtnAction.indextr" :icon="rowBtnAction.icon"
                                                            svgClasses="mr-2 w-5 h-5 hover:text-primary stroke-current"
                                                            @click.stop="initAction(rowBtnAction, schild3, rowBtnAction.method)" />
                                                    </template>
                                                </vs-td>
                                            </vs-tr>
                                        </template>
                                    </template>
                                </template>
                            </template>
                        </template>
                    </tbody>
                </template>
            </vs-table>

            <!-- Table 12 -->
            <!-- <vs-table v-if="dataTables.data && dataAttributes.tableStyle == 12" :max-items="dataTables.limit" :data="dataTables"
            style="overflow: scroll"> -->
            <vs-table :noDataText="$t('dt_empty')" v-if="dataTables.data && dataAttributes.tableStyle == 12" :max-items="dataTables.limit"
                :data="dataTables.data">
                <template slot-scope="{data}">
                    <vs-tr>
                        <vs-td :style="style(header)" :key="i" v-for="(header, i) in dataHeaders"
                            :colspan="colspan(header)" :rowspan="rowspan(header)">
                            <span v-if="header.label">
                                <center><span v-html="$t($planText(header.label))"></span></center>
                            </span>
                            <span v-else>
                                <center><span v-html="$t($planText(header.label))"></span></center>
                            </span>
                        </vs-td>
                    </vs-tr>

                    <vs-tr class="dt-th-strong" v-if="dataTables.dataHeaders"
                       >
                        <vs-td :style="style(header)" :key="j" v-for="(header, j) in dataTables.dataHeaders"
                            :colspan="colspan(header)" :rowspan="rowspan(header)">{{ $planText(header.label) }} </vs-td>
                    </vs-tr>

                    <vs-tr class="dt-th-strong" v-if="dataTables.dataSubHeaders"
                       >
                        <vs-td :key="k" v-for="(header, k) in dataTables.dataSubHeaders"
                            :colspan="colspan(header)" :rowspan="rowspan(header)"><small>{{ $planText(header.label) }}</small></vs-td>
                    </vs-tr>
                    <tbody :key="pindextr" v-for="(ptr, pindextr) in data">
                        <vs-tr class="dt-row-muted" :key="cindex" v-for="(cusField, cindex) in dataTables.group_fields"
                           >
                            <td :key="scindex" v-for="(scfield, scindex) in ptr[cusField]">
                                <center>{{ scfield }}</center>
                            </td>
                        </vs-tr>
                        <!-- <template v-for="(l2, l2index) in ptr"> -->
                        <!-- dataTables.dataFillables | json }} -->
                        <!-- <vs-tr style="background-color: rgb(255, 234, 43);">
                        <vs-td>
                            {{ pindextr }} --- {{ ptr["name"] }}
                        </vs-td>
                    </vs-tr> -->
                        <vs-tr class="dt-row-level">
                            <vs-td :key="sindex" v-for="(dataField, sindex) in dataTables.dataFillables">
                                {{ ptr[dataField] }}
                                <template v-if="dataField == 'name'">

                                </template>
                            </vs-td>

                            <vs-td>

                            </vs-td>
                        </vs-tr>

                        <template v-for="(child, sindex) in ptr.children">
                            <vs-tr class="dt-row-highlight">
                                <vs-td :key="sindex" v-for="(dataField, sindex) in dataTables.dataFillables">
                                    <template v-if="dataField == 'name'">
                                        {{ child[dataField] }}
                                        <feather-icon style="cursor: pointer;" :key="sindex" icon="PlusIcon"
                                            svgClasses="mr-2 w-5 h-5 hover:text-primary stroke-current"
                                            @click.stop="initAction(rowBtnAction, child, 'Edit')" />
                                    </template>
                                </vs-td>
                                <vs-td>
                                    <template v-if="dataField == 'name'">
                                        <feather-icon style="cursor: pointer;" :key="sindex" icon="PlusIcon"
                                            svgClasses="mr-2 w-5 h-5 hover:text-primary stroke-current"
                                            @click.stop="initAction(rowBtnAction, child, 'Edit')" />
                                    </template>
                                </vs-td>
                            </vs-tr>
                            <template v-for="(schild2, sindex) in child.children">
                                <vs-tr class="dt-row-muted">
                                    <vs-td :key="sindex" v-for="(dataField, sindex) in dataTables.dataFillables">
                                        {{ schild2[dataField] }}
                                        <template v-if="dataField == 'name'">
                                            <template v-if="dataAttributes.actionButton">
                                                <feather-icon style="cursor: pointer;"
                                                    v-for="rowBtnAction in dataAttributes.actionButton"
                                                    :key="rowBtnAction.indextr" :icon="rowBtnAction.icon"
                                                    svgClasses="mr-2 w-5 h-5 hover:text-primary stroke-current"
                                                    @click.stop="initAction(rowBtnAction, schild2, rowBtnAction.method)" />
                                            </template>
                                            <feather-icon style="cursor: pointer;" :key="sindex" icon="DollarSignIcon"
                                                svgClasses="mr-2 w-5 h-5 hover:text-primary stroke-current"
                                                @click.stop="initAction(rowBtnAction, schild2, 'PrivateForm')" />
                                        </template>
                                        <!-- <template v-if="dataField == 'name'">
                                            <feather-icon style="cursor: pointer;" :key="sindex" icon="PlusIcon"
                                                svgClasses="mr-2 w-5 h-5 hover:text-primary stroke-current"
                                                @click.stop="initAction(rowBtnAction, child, 'Edit')" />
                                        </template> -->
                                    </vs-td>
                                    <vs-td>
                                        <feather-icon style="cursor: pointer;" :key="sindex" icon="PlusIcon"
                                            svgClasses="mr-2 w-5 h-5 hover:text-primary stroke-current"
                                            @click.stop="initAction(rowBtnAction, child, 'Edit')" />
                                    </vs-td>
                                </vs-tr>

                                <template v-for="(schild3, ssindex) in schild2.children">
                                    <vs-tr>
                                        <vs-td :key="ssindex"
                                            v-for="(dataField, ssindex) in dataTables.dataFillables">
                                            {{ schild3[dataField] }}
                                            <template v-if="dataField == 'name'">
                                                <!-- ###### Level Task ########-->
                                                <!-- <feather-icon style="cursor: pointer;" :key="sindex"
                                                    icon="DollarSignIcon"
                                                    svgClasses="mr-2 w-5 h-5 hover:text-primary stroke-current"
                                                    @click.stop="initAction(rowBtnAction, schild3, 'PrivateForm')" /> -->
                                            </template>
                                        </vs-td>
                                        <vs-td>
                                            <template v-if="dataAttributes.actionButton">
                                                <feather-icon style="cursor: pointer;"
                                                    v-for="rowBtnAction in dataAttributes.actionButton"
                                                    :key="rowBtnAction.indextr" :icon="rowBtnAction.icon"
                                                    svgClasses="mr-2 w-5 h-5 hover:text-primary stroke-current"
                                                    @click.stop="initAction(rowBtnAction, schild3, rowBtnAction.method)" />
                                            </template>
                                        </vs-td>
                                    </vs-tr>
                                </template>
                            </template>
                        </template>

                        <!-- </template> -->

                    </tbody>
                </template>
            </vs-table>
        </div>

        <div class="dt-footer">
            <span class="dt-footer__info">{{ $t('dt_showing', pageRange) }}</span>
            <d-pagination :total="calPaginNumber(dataTables.total / dataTables.limit)" v-model="current_page" />
            <!-- <p class="mb-3">Current Page: {{ current_page }}</p>
            <vs-button @click="current_page++">Increment</vs-button>
            <vs-button class="ml-4 mb-4" @click="current_page--">Decrement</vs-button> -->
        </div>
    </vx-card>
</template>

<script>
import vSelect from 'vue-select'
import Vue from "vue";
import axios from "@/axios.js";
import DSearch from '@/views/form-builder/DSearch.vue';
import DModalForm from '@/views/form-builder/DModalForm.vue';
// import DForm from '@/views/form-builder/DForm.vue'
import DFormElement from '@/views/form-builder/DFormElement.vue'
import DHeadingWizard from '@/views/form-builder/DHeadingWizard.vue';
import DChildTable from '@/views/form-builder/DChildTable.vue'
import DFileManager from '@/views/form-builder/DFileManager.vue';
import { stringify } from 'querystring';
import { ref } from 'vue';
import DFileUploader from '@/views/form-builder/DFileUploader.vue';
// import DModalForm from "./DModalForm.vue";
Vue.use("DModalForm", DModalForm);
import StatisticsCardLine from '@/components/statistics-cards/StatisticsCardLine.vue'
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
        dataInfo: {
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
            titleStr: "",
            currentx: 0,
            current_page: 1,
            dataId: 0,
            // Remove Data Info Obj just for testing
            // dataInfo: {
            //     indicator: {
            //         data: {

            //         }
            //     }
            // },
            enableToggleForm: true,
            dataElements: [],
            isHidden: true,
            // ###################
            fileName: "",
            formats: ["xlsx", "csv", "txt"],
            cellAutoWidth: true,
            selectedFormat: "xlsx",
            headerTitle: ["Id", "Email", "Username", "Name", "Website"],
            headerVal: ["id", "email", "username", "name", "website"],
            users: [
                {
                    "id": 1,
                    "name": "Leanne Graham",
                    "username": "Bret",
                    "email": "Sincere@april.biz",
                    "website": "hildegard.org",
                },
                {
                    "id": 2,
                    "name": "Ervin Howell",
                    "username": "Antonette",
                    "email": "Shanna@melissa.tv",
                    "website": "anastasia.net",
                },
                {
                    "id": 3,
                    "name": "Clementine Bauch",
                    "username": "Samantha",
                    "email": "Nathan@yesenia.net",
                    "website": "ramiro.info",
                },
                {
                    "id": 4,
                    "name": "Patricia Lebsack",
                    "username": "Karianne",
                    "email": "Julianne.OConner@kory.org",
                    "website": "kale.biz",
                },
                {
                    "id": 5,
                    "name": "Chelsey Dietrich",
                    "username": "Kamren",
                    "email": "Lucio_Hettinger@annie.ca",
                    "website": "demarco.info",
                },
                {
                    "id": 6,
                    "name": "Mrs. Dennis Schulist",
                    "username": "Leopoldo_Corkery",
                    "email": "Karley_Dach@jasper.info",
                    "website": "ola.org",
                },
                {
                    "id": 7,
                    "name": "Kurtis Weissnat",
                    "username": "Elwyn.Skiles",
                    "email": "Telly.Hoeger@billy.biz",
                    "website": "elvis.io",
                },
                {
                    "id": 8,
                    "name": "Nicholas Runolfsdottir V",
                    "username": "Maxime_Nienow",
                    "email": "Sherwood@rosamond.me",
                    "website": "jacynthe.com",
                },
                {
                    "id": 9,
                    "name": "Glenna Reichert",
                    "username": "Delphine",
                    "email": "Chaim_McDermott@dana.io",
                    "website": "conrad.com",
                },
                {
                    "id": 10,
                    "name": "Clementina DuBuque",
                    "username": "Moriah.Stanton",
                    "email": "Rey.Padberg@karina.biz",
                    "website": "ambrose.net",
                },
            ],
            activePrompt: false,
            notFoundFile: "/images/attach.png",
            brokenFiles: {}, // thumbnails that failed to load fall back to the type badge
        }
    },
    components: {
        DSearch,
        DModalForm,
        DHeadingWizard,
        // DForm,
        DFormElement,
        DChildTable,
        DFileManager,
        vSelect,
        DFileUploader,
        StatisticsCardLine
    },
    computed: {
        // Totals for the cards above the table; empty until the list has loaded
        summary() {
            return (this.dataTables && this.dataTables.summary) || {}
        },
        pageRange() {
            const total = Number(this.dataTables.total) || 0
            const limit = Number(this.dataTables.limit) || 10
            const from = total ? (this.current_page - 1) * limit + 1 : 0
            const to = Math.min(this.current_page * limit, total)
            return { from: this.formatCount(from), to: this.formatCount(to), total: this.formatCount(total) }
        },
    },
    methods: {
        isFileColumn(header) {
            return ['photo', 'image', 'thumbnail', 'attach', 'file'].includes(header);
        },
        isDateColumn(header) {
            return /(^|_)(date|at)$|^dob$/.test(String(header));
        },
        fileKey(row, header) {
            return (row.id || '') + ':' + header;
        },
        // { url, ext, isImage, tone } for a file/image cell; image columns prefer the stored path
        fileCell(row, header) {
            const extOf = value => {
                const clean = String(value || '').split(/[?#]/)[0];
                const dot = clean.lastIndexOf('.');
                return dot > clean.lastIndexOf('/') ? clean.slice(dot + 1).toLowerCase() : '';
            };
            const value = row[header];
            const path = row.path && ['image', 'thumbnail'].includes(header) ? row.path : null;
            const url = path || value || '';
            const ext = String(row.type || extOf(url) || '').toLowerCase();
            const isImage = ['jpg', 'jpeg', 'png', 'gif', 'svg', 'webp', 'bmp'].includes(ext)
                || (!ext && ['photo', 'image', 'thumbnail'].includes(header));
            const tones = { pdf: '220, 53, 69', doc: '43, 87, 154', docx: '43, 87, 154', xls: '33, 115, 70', xlsx: '33, 115, 70', csv: '33, 115, 70' };
            return { url, ext, isImage, tone: tones[ext] || (isImage ? '115, 103, 240' : '92, 99, 120') };
        },
        loadErrorImage(event) {
            event.target.src = this.notFoundFile;
        },
        transformData(obj) {
            let _str = JSON.parse(obj);
            return _str;
        },
        getDocumentSize(obj) {
            return obj ? obj.length : 0;
        },
        style(obj) {
            if (obj.width) {
                return "background-color: #7367f0; color: #ffffff; font-weight: bold;min-width:" + obj.width + "px";
            } else {
                return "background-color: #7367f0; color: #ffffff; font-weight: bold;";
            }
        },
        colspan(obj) {
            let _val = "";
            if (obj.colspan) {
                _val = obj.colspan;
            }
            return _val;
        },
        rowspan(obj) {
            let _val = "";
            if (obj.rowspan) {
                _val = obj.rowspan;
            }
            return _val;
        },
        viewUrl(obj) {
            this.$router.push(obj.path).catch(() => { });
        },
        initPushDataTable(obj) {
            this.dataElements = [];
            let _data = [
                {
                    id: 1,
                    name: "tests"
                },
                {
                    id: 2,
                    name: "test2"
                }
            ];
            _data.forEach(_obj => {
                this.dataElements.push(_obj);
            });
        },
        openPrivateForm(data) {
            this.$emit('clickPrivateForm', data);
        },
        initImport(dataTables, api) {
            this.$refs.refModelFileUploader.initFileUploader(dataTables, api);
        },
        initPrint() {
            window.print();
        },
        initDowlonadElsx() {
            const modelName = this.dataTables.model_name;
            this.$vs.loading();
            let _params = {};
            return new Promise((resolve, reject) => {
                axios({
                    url: this.api + '/export?param=' + modelName,
                    data: _params,
                    method: 'POST',
                    responseType: 'blob', // Important: binary data
                    // headers: {
                    // 'Authorization': 'Bearer ' + localStorage.getItem('token') // if using tokens
                    // }
                })
                    .then((response) => {
                        const url = window.URL.createObjectURL(new Blob([response.data]));
                        const link = document.createElement('a');
                        const contentDisposition = response.headers['content-disposition'];
                        const _fileName = this.dataTables.model_name;
                        const filename = contentDisposition
                            ? contentDisposition.split('filename=')[1].split(';')[0].replace(/"/g, '')
                            : _fileName + '.xlsx';
                        link.href = url;
                        link.setAttribute('download', filename);
                        document.body.appendChild(link);
                        link.click();
                        link.remove();
                        window.URL.revokeObjectURL(url);

                        this.$vs.loading.close();
                    }).catch((error) => {
                        // reject(error)
                        this.$vs.notify({
                            title: 'Message',
                            text: 'msg.action_failed',
                            iconPack: 'feather',
                            icon: 'icon-check-circle',
                            color: 'danger'
                        })
                        this.$vs.loading.close();
                    })
            })
        },
        initDownload() {
            this.$emit("initDownload");
        },
        initOpenFileManager() {
            this.$refs.refModelFileManager.initFileManager("");
        },
        openForm() {
            this.$refs.refModalForm.openNewForm(this.dataInfo);
            this.$emit("emitDataForm", "test change form attribute");
        },
        openToggleForm() {
            this.enableToggleForm = true
        },
        openFormByParent(obj) {
            this.$refs.refModalForm.openNewFormByParent(obj);
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
        initTableData(_search_fields) {
            let _search_params = {
                pageNum: this.current_page,
                searchFields: [],
                dataInfo: _search_fields.dataInfo
            }
            this.$emit('clicked', _search_params);
        },
        searchQuery(_search_fields) {
            let _search_params = {
                pageNum: this.current_page,
                searchFields: _search_fields.formAttribute,
                dataInfo: _search_fields.dataInfo
            }
            this.$emit('clicked', _search_params);
        },
        initDel(id, row) {
            this.dataId = id;
            this.confirmDelete(row);
        },
        // Best human-readable name for a row, in the current language
        rowLabel(row) {
            if (!row) return ''
            const en = this.$i18n.locale === 'en'
            const candidates = en
                ? [row.title_en, row.name_en, row.title_kh, row.name_kh]
                : [row.title_kh, row.name_kh, row.title_en, row.name_en]
            const fullName = [row.first_name, row.last_name].filter(Boolean).join(' ')
            return [...candidates, row.title, row.name, fullName, row.username].find(v => typeof v === 'string' && v.trim()) || ''
        },
        confirmDelete(row) {
            const name = this.rowLabel(row)
            this.$vs.dialog({
                type: 'confirm',
                color: 'danger',
                title: this.$t('dt_delete_title'),
                text: name ? this.$t('dt_delete_text_named', { name }) : this.$t('dt_delete_text'),
                acceptText: this.$t('dt_delete'),
                cancelText: this.$t('dt_cancel'),
                accept: this.acceptAlert
            })
        },
        acceptAlert() {
            let id = this.dataId;
            let _search_params = {
                pageNum: this.current_page,
                searchFields: [],
                dataInfo: this.dataInfo
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
                            text: 'msg.action_failed',
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
        },
        initAction(rowBtnAction, data, method) {
            this.dataInfo = data;

            if (method == "View") {
                this.$refs.refModalForm.initForm(data);
            }
            if (method == "Edit") {
                this.$refs.refModalForm.initForm(data);
            }
            if (method == "PrivateForm") {
                this.$emit('clickPrivateForm', data);
            }
            if (method == "PrivateLink") {
                this.$router.push(rowBtnAction.path + "/" + data.id)
                // this.$emit('clickPrivateForm', data);
            }
            if (method == "Delete") {
                this.dataId = data.id;
                this.confirmDelete(data);
            }
            if (method == "Indicator") {
                this.$refs.refModalForm.initForm(data);
            }
            if (method == "Execute") {
                this.$vs.loading();
                return new Promise((resolve, reject) => {
                    axios.post(rowBtnAction.api, data)
                        .then((response) => {
                            if (response.data.success) {
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
                            this.$vs.loading.close();

                        }).catch((error) => {
                            reject(error)
                            this.$vs.notify({
                                title: 'Message',
                                text: 'msg.action_failed',
                                iconPack: 'feather',
                                icon: 'icon-check-circle',
                                color: 'danger',
                                position: 'top-right'
                            })
                            this.$vs.loading.close();
                        });
                });
                // this.$refs.refModalForm.initForm(data);
            }
            // Place your customization action button
            if (method == "InitPayment") {
                this.$emit('clickPayment', data);
            }
            if (method == "sendNotification") {
                this.$emit('clickNotify', data);
            }
        },
        exportToExcel() {
            import('@/vendor/Export2Excel').then(excel => {
                const list = this.users
                const data = this.formatJson(this.headerVal, list)
                excel.export_json_to_excel({
                    header: this.headerTitle,
                    data,
                    filename: this.fileName,
                    autoWidth: this.cellAutoWidth,
                    bookType: this.selectedFormat
                })
                this.clearFields()
            })
        },
        formatJson(filterVal, jsonData) {
            return jsonData.map(v => filterVal.map(j => {
                // Add col name which needs to be translated
                // if (j === 'timestamp') {
                //   return parseTime(v[j])
                // } else {
                //   return v[j]
                // }

                return v[j]
            }))
        },
        clearFields() {
            this.filename = "",
                this.cellAutoWidth = true,
                this.selectedFormat = "xlsx"
        },
        // Customize on Data Display
        can(action) {
            const { permissions, model_name } = this.dataTables
            return Boolean(permissions && model_name && permissions[action + '_' + model_name])
        },
        statusTitle(status) {
            return (this.$i18n.locale === 'en' ? status.title_en : status.title_kh) || status.title_kh || status.title_en
        },
        statusTone(status) {
            const title = String(status.title_en || '').toLowerCase()
            if (/approv|complet|active|paid|success/.test(title) && !/inactive/.test(title)) return 'success'
            if (/review|pending|draft|progress/.test(title)) return 'warning'
            if (/cancel|delet|reject|inactive|unpaid|fail/.test(title)) return 'danger'
            return 'neutral'
        },
        isAmountColumn(column) {
            return /(^|_)(amount|total|price|balance|salary)(_|$)/.test(String(column))
        },
        formatAmount(value) {
            const number = Number(String(value).replace(/,/g, ''))
            if (value === null || value === '' || isNaN(number)) return value
            return number.toLocaleString('en-US', { maximumFractionDigits: 2 })
        },
        formatCount(value) {
            return Number(value || 0).toLocaleString('en-US')
        },
        customField(row, column) {
            switch (column) {
                case "status":
                    return row[column] ? row[column]["title_kh"] : null;
                    break;
                case "officer_id":
                    return row["officer"] ? row["officer"]["first_name"]+ ' ' +row["officer"]["last_name"]+ '-' +row["officer"]["identity_member_no"] : null;
                    break;
                case "payment_method_id":
                    return row["payment_method"] ? row["payment_method"]["name_en"] : null;
                    break;
                case "currency_id":
                    return row["currency"] ? row["currency"]["name_en"] : null;
                    break;
                case "entity_id":
                    return row["entity"] ? row["entity"]["name_en"] : null;
                    break;
                case "department_id":
                    return row["department"] ? row["department"]["name_en"] : null;
                    break;
                case "is_nssf_member":
                    return row["nssf"] ? row["nssf"]["title_kh"]: null;
                    break;
                case "user_id":
                    return row["user"] ? row["user"]["username"]: null;
                    break;
                case "notifiable_id":
                    return row["user"] ? row["user"]["username"]: null;
                    break;
                default:
                    return row[column];
                    break;
            }
            return columnHeader;
        }

    },
    created() {
        this.$vs.loading.close();
    },
    watch: {
        current_page: function (val) {
            let _search_params = {
                pageNum: this.current_page,
                searchFields: [],
                dataInfo: this.dataInfo
            }
            this.$emit('clicked', _search_params);
        }
    }
}
</script>

<style>
@media print {
    .no-print {
        display: none;
    }

    .print-only {
        display: block;
    }
}

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
<!-- List page styles (.dt-list, .dt-toolbar, .dt-btn, .dt-badge, …) are shared app-wide: see "LIST PAGES" in resources/sass/custom.scss -->

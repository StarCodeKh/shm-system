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
                    icon="CheckCircleIcon"
                    icon-right
                    :statistic="summary.approved"
                    :statisticTitle="$t('stat_approved')"
                    color="success" />
            </div>

            <div class="vx-col w-full sm:w-1/2 md:w-1/2 lg:w-1/4 xl:w-1/4">
                <statistics-card-line
                    hideChart
                    class="mb-base"
                    icon="EyeIcon"
                    icon-right
                    :statistic="summary.in_review"
                    :statisticTitle="$t('stat_reviewing')"
                    color="warning" />
            </div>

            <div class="vx-col w-full sm:w-1/2 md:w-1/2 lg:w-1/4 xl:w-1/4">
                <statistics-card-line
                    hideChart
                    class="mb-base"
                    icon="LoaderIcon"
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
                    icon="XCircleIcon"
                    icon-right
                    :statistic="summary.rejected"
                    :statisticTitle="$t('stat_rejected')"
                    color="danger" />
            </div>
        </div>
        <!-- Customize Incentive Dashboard -->
        <div class="vx-row" v-if="dataAttributes.enableDashboardIncentive">
            <div class="vx-col w-full md:w-1/3 lg:w-1/3 xl:w-1/3">
                <statistics-card-line
                    hideChart
                    class="mb-base"
                    icon="FileTextIcon"
                    icon-right
                    :statistic="dataTables.total"
                    :statisticTitle="$t('stat_total_records')"
                    color="navy" />
            </div>

            <div class="vx-col w-full md:w-1/3 lg:w-1/3 xl:w-1/3">
                <statistics-card-line
                    hideChart
                    class="mb-base"
                    icon="DollarSignIcon"
                    icon-right
                    :statistic="dataTables.total_amount"
                    :statisticTitle="$t('stat_incentive_paid_riel')"
                    color="success" />
            </div>
        </div>
        <!-- Customize Account Payable Dashboard -->
        <div class="vx-row" v-if="dataAttributes.enableDashboardPayable">

            <div class="vx-col w-full md:w-1/3 lg:w-1/3 xl:w-1/3">
                <small><statistics-card-line
                    hideChart
                    class="mb-base"
                    icon="FileTextIcon"
                    icon-right
                    :statistic="dataTables.total"
                    :statisticTitle="$t('stat_total_records')"
                    color="navy" /></small>
            </div>

            <div class="vx-col w-full md:w-1/3 lg:w-1/3 xl:w-1/3">
                <small><statistics-card-line
                    hideChart
                    class="mb-base"
                    icon="CheckCircleIcon"
                    icon-right
                    :statistic="summary.total_paid_khr"
                    :statisticTitle="$t('stat_paid_riel')"
                    color="success" /></small>
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
                    icon="FileTextIcon"
                    icon-right
                    :statistic="dataTables.total"
                    :statisticTitle="$t('stat_total_records')"
                    color="navy" />
            </div>

            <div class="vx-col w-full sm:w-1/2 md:w-1/2 lg:w-1/4 xl:w-1/4">
                <statistics-card-line
                    hideChart
                    class="mb-base"
                    icon="CheckCircleIcon"
                    icon-right
                    :statistic="summary.paid"
                    :statisticTitle="$t('stat_paid')"
                    color="success" />
            </div>

            <div class="vx-col w-full sm:w-1/2 md:w-1/2 lg:w-1/4 xl:w-1/4">
                <statistics-card-line
                    hideChart
                    class="mb-base"
                    icon="SlashIcon"
                    icon-right
                    :statistic="summary.void"
                    :statisticTitle="$t('stat_void')"
                    color="dark" />
            </div>

            <div class="vx-col w-full sm:w-1/2 md:w-1/2 lg:w-1/4 xl:w-1/4">
                <statistics-card-line
                    hideChart
                    class="mb-base"
                    icon="ShieldIcon"
                    icon-right
                    :statistic="summary.exceptional"
                    :statisticTitle="$t('stat_exempt')"
                    color="warning" />
            </div>

        </div>
        <!-- #End Customize Dashboard Information -->

        <DModalForm @clicked="initTableData" ref="refModalForm" :data="dataTables" :api="api"
            :formAttributes="formAttributes" :parentDataInfo="activeRow || dataInfo" :rowDisplay="rowDisplay"
            :dataAttributes="dataAttributes" :title="$t(title)" />

            <d-file-manager ref="refModelFileManager"></d-file-manager>

            <d-file-uploader ref="refModelFileUploader" @clicked="initTableData"></d-file-uploader>

        <vs-prompt title="Export To Excel" class="export-options" @cancle="clearFields" @accept="exportToExcel"
            accept-text="Export" @close="clearFields" v-model:active="activePrompt">
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


        <template v-if="!isHidden">
            <d-search v-if="!dataAttributes.hideSearchBar" @searchQuery="searchQuery" @clicked="initTableData"
                :data="dataTables" :api="api" :formAttributes="formAttributes" :rowDisplay="'4grid'"
                :dataInfo="dataInfo"></d-search>
        </template>

        <div class="print-only">
            <!-- & dataTables.data.length -->
            <vs-table :noDataText="$t('dt_empty')" v-if="dataTables.data && dataAttributes.tableStyle == 1" :max-items="dataTables.limit"
                :data="dataTables.data">
                <template #thead>
                    <!-- 28C76F -->
                    <vs-th>{{ $t("no") }}</vs-th>
                    <vs-th :key="i" :class="{ 'dt-num': isAmountColumn(header) }"
                        v-for="(header, i) in dataHeaders">{{ $tField(header) }}</vs-th>
                    <vs-th class="dt-actions-head">{{ $t("Action")
                    }}</vs-th>
                </template>
                <template #default="{data}">
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
                                            @error="brokenFiles[fileKey(tr, header)] = true">
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
                                    {{ customField(tr, header) }}
                                </template>
                            </vs-td>
                            <vs-td class="dt-actions-cell">
                                <div class="dt-actions">
                                    <template v-for="(rowBtnAction, bi) in (dataAttributes.actionButton || [])" :key="'act-' + bi">
                                        <button
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

                <template #thead>
                    <!-- 28C76F -->
                    <vs-th class="dt-th-strong">{{ $t("no") }}</vs-th>
                    <vs-th class="dt-th-strong" :key="i"
                        v-for="(header, i) in dataHeaders">{{ $tField(header) }}</vs-th>
                    <vs-th class="dt-th-strong">{{ $t("Action")
                    }}</vs-th>
                </template>

                <template #default="{data}">
                    <tbody :key="pindextr" v-for="(ptr, pindextr) in data">
                        <vs-tr :state="dataAttributes.backgroundColor">
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
                                </template>

                            </vs-td>
                        </vs-tr>
                    </tbody>
                </template>
            </vs-table>

            <vs-table :noDataText="$t('dt_empty')" v-if="dataTables.data && dataAttributes.tableStyle == 3" :max-items="dataTables.limit"
                :data="dataTables.data" style="overflow-x: auto">
                <template #default="{data}">
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

                        <template v-for="row in ptr.data.children" :key="index">
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
                            <vs-tr v-for="(row2, index) in row.data">
                                <td>{{ row2.name }}</td>
                                <td :key="index3" v-for="(row, index3) in row2.values">{{ row }}</td>
                                <td></td>
                            </vs-tr>

                            <template v-for="row3 in row.dataDetails" :key="index">
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
                                    </td>
                                </vs-tr>

                                <vs-tr v-for="(row2, index) in row3.data">
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
                <template #default="{data}">
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

                        <template v-for="(row, index) in ptr.children"><template v-if="ptr.children">

                            <vs-tr class="dt-row-group">
                                <td>{{ row.name }}</td>
                                <td>{{ row.entity }}</td>
                                <td>{{ row.entity_member }}</td>
                                <td>
                                </td>
                            </vs-tr>

                            <template v-for="row in row.children"><template v-if="row.children">

                                <vs-tr class="dt-row-level">
                                    <td>{{ row.name }}</td>
                                    <td>{{ row.entity }}</td>
                                    <td>{{ row.entity_member }}</td>
                                    <td>
                                    </td>
                                </vs-tr>

                                <template v-for="row in row.children"><template v-if="row.children">
                                    <vs-tr class="dt-row-highlight">
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
                                            <td>{{ row.name }}</td>
                                            <td>{{ row.entity }}</td>
                                            <td>{{ row.entity_member }}</td>
                                            <td></td>
                                        </vs-tr>
                                    </template>

                                </template></template>

                            </template></template>
                        </template></template>

                    </tbody>
                </template>
            </vs-table>

            <vs-table :noDataText="$t('dt_empty')" v-if="dataTables.data && dataAttributes.tableStyle == 5" :max-items="dataTables.limit"
                :data="dataTables.data" style="overflow-x: auto">

                <template #default="{data}">

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
                        <template v-for="(row, index) in ptr.children"><template v-if="ptr.children">

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
                            <template v-for="row in row.children"><template v-if="row.children">

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
                                <template v-for="row in row.children"><template v-if="row.children">
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

                                </template></template>
                            </template></template>
                        </template></template>

                    </tbody>
                </template>
            </vs-table>

            <vs-table :noDataText="$t('dt_empty')" v-if="dataTables.data && dataAttributes.tableStyle == 6" :max-items="dataTables.limit"
                :data="dataTables.data" style="overflow-x: auto">
                <template #default="{data}">

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

                            <template v-for="child in parent.children" :key="sindex">
                                <vs-tr class="dt-row-highlight">
                                    <vs-td :key="sindex" v-for="(dataField, sindex) in dataTables.dataFillables">
                                        {{ child[dataField] }}
                                    </vs-td>
                                </vs-tr>

                                <vs-tr v-for="(schild, sindex) in child.children">
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
                <template #default="{data}">

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

                        <vs-tr class="dt-row-muted" :key="pindextr">
                            <td colspan="6">{{ ptr.entity.name }}</td>
                        </vs-tr>
                        <template v-for="parent in ptr.children">
                            <vs-tr class="dt-row-group">
                                <vs-td :key="sindex" v-for="(dataField, sindex) in dataTables.dataFillables">
                                    {{ parent[dataField] }}
                                </vs-td>
                            </vs-tr>

                            <template v-for="child in parent.children" :key="sindex">
                                <vs-tr class="dt-row-highlight">
                                    <vs-td :key="sindex" v-for="(dataField, sindex) in dataTables.dataFillables">
                                        {{ child[dataField] }}
                                    </vs-td>
                                </vs-tr>

                                <vs-tr v-for="(schild, sindex) in child.children">
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
                <template #default="{data}">

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
                        </template>
                    </tbody>
                </template>
            </vs-table>

            <!-- Expense Form  -->
            <vs-table :noDataText="$t('dt_empty')" v-if="dataTables.data && dataAttributes.tableStyle == 9" :max-items="dataTables.limit"
                :data="dataTables.data" style="overflow-x: auto">
                <template #default="{data}">

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
                        </template>
                    </tbody>
                </template>
            </vs-table>

            <vs-table :noDataText="$t('dt_empty')" v-if="dataTables.data && dataAttributes.tableStyle == 10" :max-items="dataTables.limit"
                :data="dataTables.data" style="overflow-x: auto">
                <template #default="{data}">
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

                            <template v-for="row in row.children" :key="index">
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
                                <vs-tr v-for="(row2, index) in row.data">
                                    <td>{{ row2.name }}</td>
                                    <td :key="index3" v-for="(row, index3) in row2.values">{{ row }}</td>
                                    <td></td>
                                </vs-tr>

                                <template v-for="row3 in row.dataDetails" :key="index">
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
                                </td>
                                <td :key="index4" v-for="(row, index4) in row3.values">{{ row }}</td>
                                <td>
                                    <template v-if="dataAttributes.actionButton">
                                        <feather-icon style="cursor: pointer;"
                                            v-for="rowBtnAction in dataAttributes.actionButton"
                                            :key="rowBtnAction.indextr" :icon="rowBtnAction.icon"
                                            svgClasses="mr-2 w-5 h-5 hover:text-primary stroke-current"
                                            @click.stop="viewUrl(rowBtnAction)" />
                                    </template>
                                </td>
                                </vs-tr>

                                <vs-tr v-for="(row4, index) in row3.data">
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
                <template #default="{data}">

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
            <vs-table :noDataText="$t('dt_empty')" v-if="dataTables.data && dataAttributes.tableStyle == 12" :max-items="dataTables.limit"
                :data="dataTables.data">
                <template #default="{data}">
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

                    </tbody>
                </template>
            </vs-table>
        </div>

        <div class="dt-footer">
            <span class="dt-footer__info">{{ $t('dt_showing', pageRange) }}</span>
            <div class="dt-footer__pager">
                <v-select v-model="pageSize" :options="pageSizeOptions" :reduce="option => option.value"
                    :clearable="false" :searchable="false" class="dt-footer__size is-compact" :title="$t('dt_per_page')" />
                <d-pagination :total="calPaginNumber(dataTables.total / dataTables.limit)" v-model="current_page" />
            </div>
        </div>
    </vx-card>
</template>

<script>
import vSelect from 'vue-select'
import axios from "@/axios.js";
import { onLocaleChange } from "@/i18n/i18n.js";
import DSearch from '@/views/form-builder/DSearch.vue';
import DModalForm from '@/views/form-builder/DModalForm.vue';
import DFileManager from '@/views/form-builder/DFileManager.vue';
import DFileUploader from '@/views/form-builder/DFileUploader.vue';
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
            activeRow: null, // row whose action button was clicked last
            titleStr: "",
            currentx: 0,
            current_page: 1,
            page_size: null, // rows per page picked in the footer: a number or 'all'; null = the server's standard size
            standard_size: 0, // the server's standard page size (Settings › General › Limit)
            lastSearchFields: [], // the search on screen, kept when the list reloads for a language switch
            dataId: 0,

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
        // DForm,
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
        // Rows per page: the common sizes, the site's standard size, and "All"
        pageSizeOptions() {
            const sizes = [10, 20, 50, 100]
            if (this.standard_size && !sizes.includes(this.standard_size)) sizes.push(this.standard_size)
            return sizes.sort((a, b) => a - b)
                .map(n => ({ label: this.formatCount(n), value: n }))
                .concat({ label: this.$t('dt_all'), value: 'all' })
        },
        pageSize: {
            get() {
                return this.page_size || this.standard_size || Number(this.dataTables.limit) || 10
            },
            // A new size starts again from page 1
            set(size) {
                this.page_size = size
                if (this.current_page !== 1) {
                    this.current_page = 1 // the page watcher reloads
                } else {
                    this.$emit('clicked', { pageNum: 1, limit: size, searchFields: this.lastSearchFields, dataInfo: this.dataInfo })
                }
            },
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
        openPrivateForm(data) {
            this.$emit('clickPrivateForm', data);
        },
        initImport(dataTables, api) {
            this.$refs.refModelFileUploader.initFileUploader(dataTables, api);
        },
        // The rows of the current search (up to 1000) as the standard document: same columns and labels as on
        // screen, for the Print (PDF) and Download (Excel) buttons. Both lay out like the reports.
        async documentPayload() {
            const value = v => (v && typeof v === 'object') ? (v.label ?? v.value ?? '') : v
            const search = Object.entries(this.lastSearchFields || {})
                .filter(([, v]) => value(v) !== undefined && value(v) !== null && value(v) !== '')
            let rows = this.dataTables.data || []
            let total = Number(this.dataTables.total) || rows.length
            if (this.api) {
                const { data } = await axios.post(this.api + '/search', {
                    sort: 'id', order: '', page_number: 1, limit: 1000,
                    search_field: search.map(([k, v]) => ({ [k]: v })),
                })
                rows = data.data || rows
                total = Number(data.total) || rows.length
            }
            const headers = (this.dataHeaders || []).filter(h => !this.isFileColumn(h))
            const cell = (row, h) => {
                if (h === 'status' && row[h] && typeof row[h] === 'object') return this.statusTitle(row[h])
                const v = value(this.customField(row, h))
                return v === undefined || v === null ? '' : (typeof v === 'object' ? (v.name_kh || v.title_kh || v.name_en || '') : String(v))
            }
            return {
                title: this.$t(this.title),
                columns: headers.map(h => ({ key: h, label: this.$tField(h), numeric: this.isAmountColumn(h), date: this.isDateColumn(h) })),
                rows: rows.map(row => Object.fromEntries(headers.map(h => [h, cell(row, h)]))),
                filters: Object.fromEntries(search.map(([k, v]) => [this.$tField(k), String(value(v))])),
                total,
            }
        },
        notifyDocumentError() {
            this.$vs.notify({ title: 'Message', text: this.$t('report_error'), iconPack: 'feather', icon: 'icon-alert-circle', color: 'danger' })
        },
        async initPrint() {
            // Open the tab now (inside the click) so popup blockers allow it; fill it when the PDF is ready
            const tab = window.open('', '_blank')
            if (tab) tab.document.write(`<p style="font-family:sans-serif;padding:24px;color:#666">${this.$t('report_preparing')}</p>`)
            this.$vs.loading()
            try {
                const response = await axios.post('/api/v1/print', await this.documentPayload(), { responseType: 'blob' })
                const url = URL.createObjectURL(new Blob([response.data], { type: 'application/pdf' }))
                if (tab) tab.location.href = url
                else window.open(url, '_blank')
                setTimeout(() => URL.revokeObjectURL(url), 60000)
            } catch (e) {
                if (tab) tab.close()
                this.notifyDocumentError()
            }
            this.$vs.loading.close()
        },
        async initDowlonadElsx() {
            this.$vs.loading()
            try {
                const response = await axios.post('/api/v1/print/excel', await this.documentPayload(), { responseType: 'blob' })
                const match = (response.headers['content-disposition'] || '').match(/filename="?([^";]+)"?/)
                const url = URL.createObjectURL(new Blob([response.data], { type: response.headers['content-type'] }))
                const link = document.createElement('a')
                link.href = url
                link.download = match ? match[1] : (this.dataTables.model_name || 'list') + '.xlsx'
                document.body.appendChild(link)
                link.click()
                link.remove()
                URL.revokeObjectURL(url)
            } catch (e) {
                this.notifyDocumentError()
            }
            this.$vs.loading.close()
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
        openFormByParent(obj) {
            this.$refs.refModalForm.openNewFormByParent(obj);
        },
        calPaginNumber(n) {
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
            this.lastSearchFields = []
            let _search_params = {
                pageNum: this.current_page,
                limit: this.page_size || undefined,
                searchFields: [],
                dataInfo: _search_fields.dataInfo
            }
            this.$emit('clicked', _search_params);
        },
        searchQuery(_search_fields) {
            this.lastSearchFields = _search_fields.formAttribute || []
            let _search_params = {
                pageNum: this.current_page,
                limit: this.page_size || undefined,
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
                limit: this.page_size || undefined,
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
            // Remember the row for the popups it opens (dataInfo is a prop: read-only in Vue 3)
            this.activeRow = data;

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
                // Optional query, e.g. { tab: 'password' } to open a page on one of its tabs
                this.$router.push({ path: rowBtnAction.path + "/" + data.id, query: rowBtnAction.query || {} })
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
        // Rows and dropdown lists carry labels the server made in the old language: reload the same page and search
        this.offLocaleChange = onLocaleChange(() => {
            this.$emit('clicked', { pageNum: this.current_page, limit: this.page_size || undefined, searchFields: this.lastSearchFields, dataInfo: this.dataInfo })
        })
    },
    beforeUnmount() {
        if (this.offLocaleChange) this.offLocaleChange()
    },
    watch: {
        // Until a size is picked the server answers with its standard size: remember it as an option
        'dataTables.limit': {
            immediate: true,
            handler(limit) {
                if (!this.page_size && Number(limit) > 0) this.standard_size = Number(limit)
            },
        },
        current_page: function (val) {
            let _search_params = {
                pageNum: this.current_page,
                limit: this.page_size || undefined,
                searchFields: [],
                dataInfo: this.dataInfo
            }
            this.$emit('clicked', _search_params);
        }
    }
}
</script>

<style>
.dt-footer__pager {
    display: flex;
    align-items: center;
    gap: 10px;
    min-width: 0;
    margin-inline-start: auto;
}

.dt-footer__size {
    flex: 0 0 auto;
    min-width: 84px;
}

@media (max-width: 640px) {
    .dt-footer__pager {
        width: 100%;
        justify-content: space-between;
    }

    .dt-footer__pager .d-pagination {
        flex: 1 1 auto;
    }
}

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

<!-- List page styles (.dt-list, .dt-toolbar, .dt-btn, .dt-badge, …) are shared app-wide: see "LIST PAGES" in resources/css/custom.css -->

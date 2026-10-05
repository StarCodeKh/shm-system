<template>
    <div>
        <div class="flex justify-between flex-wrap mb-4">
            <vs-button class="mt-4 mr-2 shadow-lg" type="gradient" color="#7367F0" gradient-color-secondary="#CE9FFC">{{$t("Download")}}</vs-button>
            <vs-checkbox color="success" v-model="checkBox2">បានត្រួតពិនិត្យ</vs-checkbox>
            <vs-button class="mt-4" type="border" color="#30c974"><feather-icon icon="PlusIcon" svgClasses="h-4 w-4" /> {{$t("AddNew")}}</vs-button>
            </div>
            
            <div class="flex items-center">
            <!-- ###################### -->
            <div>
                <div class="clearfix"></div>
                <div class="_container-inner">
                    <center>
                        <span class="font-mef2">
                            ព្រះរាជាណាចក្រកម្ពុជា
                            <br/>
                            ជាតិ សាសនា ព្រះមហាក្សត្រ
                            <br/><center>***</center>
                        </span>
                    </center>
                    <div class="font-mef2" style="float: left;">
                    <!-- <img width="120px" src="@assets/images/logo/logo.png"/> -->
                    ក្រសួងសេដ្ឋកិច្ច និងហិរញ្ញវត្ថុ<br/>
                    អគ្គលេខាធិការដ្ឋានក្រសួងសេដ្ឋកិច្ចនិងហិរញ្ញវត្ថុ<br/>
                    នាយកដា្ឋនរដ្ឋបាលនិងហិរញ្ញវត្ថុ<br/>
                    ការិយាល័យផែនការថវិកា
                    </div><br/><br/>
                    <div class="clearfix"></div>
                    <center>    
                        <!-- <span class="font-mef2" style="color: #0d72a2; font-size: 12pt">
                            បញ្ជីសរុប<br/>
                        </span> -->
                        <br/>
                        <span class="font-mef2" style="color: #0d72a2; font-size: 12pt">
                        តារាង១ៈ អនុកម្មវិធី អ្នកគ្រប់គ្រង និងអង្គភាពទទួលខុសត្រូវ
                        </span>
                        <div class="clearfix"></div>
                        <br/>
                    </center>
                </div>
                <div class="clearfix"></div>
                <vs-table :data="[]">

                <template slot="thead">
                    <vs-th>ឈ្មោះគោលបំណងគោលនយោបាយ</vs-th>
                    <vs-th>ឈ្មោះកម្មវិធី និងអនុកម្មវិធី</vs-th>
                    <vs-th>អ្នកគ្រប់គ្រង</vs-th>
                    <vs-th>អ្នកទទួលខុសត្រូវ</vs-th>
                </template>

                <template>
                    <vs-tr :state="'success'">
                    <vs-td colspan="4" >ផ្នែកទី១៖ ពិដានចំណាយចរន្ត ឆ្នាំ២០២៣-២០២៥ សរុបរួមក្រសួង (យោងលេខ ០៣ សរណន ចុះថ្ងៃទី៨ ខែមិថុនា ឆ្នាំ២០២២)</vs-td>
                    </vs-tr>
                    <vs-tr style="background-color: #28C76F; color: #ffffff; font-weight: bold;">
                    <vs-td>គោលបំណងគោលនយោបាយទី១</vs-td>
                    <vs-td>កម្មវិធីទី១ៈ ការគ្រប់គ្រងវិស័យសេដ្ឋកិច្ច</vs-td>
                    <vs-td>ឯកឧត្តម/លោកជំទាវ</vs-td>
                    <vs-td></vs-td>
                    </vs-tr>
                    
                    <vs-tr :state="'warning'">
                    <vs-td></vs-td>
                    <vs-td></vs-td>
                    <vs-td>ឯកឧត្តម/លោកជំទាវ</vs-td>
                    <vs-td>អគ្គនាយកដ្ឋានគោលនយោបាយ</vs-td>
                    </vs-tr>

                    <vs-tr>
                    <vs-td></vs-td>
                    <vs-td>- ១.១.‌១.ចង្កោមសកម្មភាពទី១ៈ គាំទ្រសម្របសម្រួល និងគ្រប់គ្រងការងាររដ្ឋបាលរបស់នាយកដ្ឋាន</vs-td>
                    <vs-td>លោក/លោកស្រី</vs-td>
                    <vs-td>លេខាធិការដ្ឋាន</vs-td>
                    </vs-tr>
                    <vs-tr>
                    <vs-td></vs-td>
                    <vs-td>- ១.១.‌១.១.សកម្មភាពទី១ៈ បំពេញមុខងារជាលេខាធិការដ្ឋនា</vs-td>
                    <vs-td>លោក/លោកស្រី</vs-td>
                    <vs-td>លេខាធិការដ្ឋាន</vs-td>
                    </vs-tr>
                </template>
                </vs-table>
            </div>
            <!-- ###################### -->
        </div>
    </div>
</template>

<script>
    export default {
        data() {
            return {
                checkBox2: false
            }
        }
    }
</script>

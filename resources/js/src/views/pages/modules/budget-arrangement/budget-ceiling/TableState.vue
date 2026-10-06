<template>
    <vx-card :title="$planText('ពិដានថវិកាឆ្នាំ{P-1}')">
      <div class="flex flex-wrap-reverse items-center data-list-btn-container">
        <!-- ACTION - DROPDOWN -->
        <vs-dropdown vs-trigger-click class="dd-actions cursor-pointer mr-4 mb-4">

          <div class="p-4 shadow-drop rounded-lg d-theme-dark-bg cursor-pointer flex items-center justify-center text-lg font-medium w-32 w-full">
            <span class="mr-2">{{$t("Action")}}</span>
            <feather-icon icon="ChevronDownIcon" svgClasses="h-4 w-4" />
          </div>

          <vs-dropdown-menu>

            <vs-dropdown-item>
              <span class="flex items-center">
                <feather-icon icon="TrashIcon" svgClasses="h-4 w-4" class="mr-2" />
                <span>Delete</span>
              </span>
            </vs-dropdown-item>

            <vs-dropdown-item>
              <span class="flex items-center">
                <feather-icon icon="ArchiveIcon" svgClasses="h-4 w-4" class="mr-2" />
                <span>Archive</span>
              </span>
            </vs-dropdown-item>

            <vs-dropdown-item>
              <span class="flex items-center">
                <feather-icon icon="FileIcon" svgClasses="h-4 w-4" class="mr-2" />
                <span>Print</span>
              </span>
            </vs-dropdown-item>

            <vs-dropdown-item>
              <span class="flex items-center">
                <feather-icon icon="SaveIcon" svgClasses="h-4 w-4" class="mr-2" />
                <span>Another Action</span>
              </span>
            </vs-dropdown-item>

          </vs-dropdown-menu>
        </vs-dropdown>

        <!-- ADD NEW -->
        <div class="btn-add-new p-3 mb-4 mr-4 rounded-lg cursor-pointer flex items-center justify-center text-lg font-medium text-base text-primary border border-solid border-primary" @click="addNewData">
          <feather-icon icon="PlusIcon" svgClasses="h-4 w-4" />
          <span class="ml-2 text-base text-primary">{{$t("AddNew")}}</span>
        </div>
      </div>

      <div id="popup-demo">
        <popup-fullscreen></popup-fullscreen>
      </div>

      <vs-table :data="users">
          <template>
              <vs-tr style="background-color: #28C76F; color: #ffffff; font-weight: bold;">
                <vs-td style="width: auto" rowspan="2"><center>បរិយាយមុខសញ្ញា</center></vs-td>
                <vs-td style="" rowspan="2"><center>{{ $planText('ច្បាប់ឆ្នាំ{P-1}') }}</center></vs-td>
                <vs-td style="" rowspan="2"><center>ចំណាយមិនប្រចាំ</center></vs-td>
                <vs-td style="" rowspan="2"><center>ចំណាយប្រចាំ</center></vs-td>
                <vs-td style="width: 300px" colspan="3"><center>ពិដានចំណាយចរន្ត</center></vs-td>
                <vs-td style="width: 300px" colspan="3"><center>អាត្រាកំណើនប្រចាំឆ្នាំ</center></vs-td>
                <vs-td style="" rowspan="2">សម្គាល់</vs-td>
              </vs-tr>

              <vs-tr style="background-color: #28C76F; color: #ffffff; font-weight: bold;">
                <vs-td>{{ $planText('{P}') }}</vs-td>
                <vs-td>{{ $planText('{P+1}') }}</vs-td>
                <vs-td>{{ $planText('{P+2}') }}</vs-td>
                <vs-td>{{ $planText('{P}') }}</vs-td>
                <vs-td>{{ $planText('{P+1}') }}</vs-td>
                <vs-td>{{ $planText('{P+2}') }}</vs-td>
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

              <!-- ################## -->
              <vs-tr :state="'success'">
                <vs-td colspan="11" >{{ $planText('ផ្នែកទី១៖ ពិដានចំណាយចរន្ត ឆ្នាំ{P}-{P+2} សរុបរួមក្រសួង (យោងលេខ ០៣ សរណន ចុះថ្ងៃទី៨ ខែមិថុនា ឆ្នាំ២០២២)') }}</vs-td>
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
              
              <!-- Part1 -->
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

                <!-- Part2 -->
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
                <vs-td colspan="11" >{{ $planText('ផ្នែកទី២៖ ពិដានចំណាយចរន្ត ឆ្នាំ{P}-{P+2} តាមអង្គភាពថវិកា') }}</vs-td>
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
              
              <!-- Part1 -->
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

                <!-- Part1 -->
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

                <!-- Part1 -->
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

    </vx-card>
</template>

<script>
export default {
    data() {
        return {
           
        }
    },
}
</script>

<style>
  th.th-width-300{
    min-width: 260px !important;
  }
  td.td-width-150{
    min-width: 150px !important;
  }
  table tr td, th{
    border: 1px solid #d7d7d7;
  }
  th{
    text-align: center !important;
    /* display: inline; */
  }
  th.rotate{
    transform: rotate(-90deg)
  }
.vs-table--thead th .vs-table-text {
    text-transform: uppercase;
    font-weight: 600;
    text-align: center;
    display: inline !important;
}
</style>
<template>
    <vx-card title="ចុះលទ្ធផលអនុវត្តចំណូល" code-toggler>
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

          <template  style="border:1px solid #000">
              
              <vs-tr style="background-color: rgb(183 255 215)">
                <vs-td rowspan="3"><center>ជំពូក</center></vs-td>
                <vs-td rowspan="3"><center>គណនី</center></vs-td>
                <vs-td rowspan="3"><center>អនុគណនី</center></vs-td>
                <vs-td rowspan="3"><center>ចំណាត់ថ្នាក់ចំណូល</center></vs-td>
                <vs-td rowspan="2" colspan="3"><center>សរុបទាំងពន្ធដាថ្នាក់ក្រោមជាតិ <br/><small>សរុបរួម + ពន្ទដារថ្នាក់ក្រោមជាតិ</small></center></vs-td>
                <vs-td rowspan="2" colspan="3"><center>សរុបរួម <br/> <small>រដ្ឋបាលកណ្តាល + មន្ទីរ សហវ.</small></center></vs-td>
                <vs-td rowspan="2" colspan="3"><center>សរុបរដ្ឋបាលកណ្តាល <br/><small>ផ្នែកទី១ + ២ + ៣</small></center></vs-td>
                <vs-td colspan="12"><center>ផ្នែកទី២ ទីស្តីការក្រសួង</center></vs-td>
              </vs-tr>

              <vs-tr style="background-color: rgb(183 255 215)">
                <vs-td colspan="3">សរុបទីស្តីការក្រសួង</vs-td>
                <vs-td colspan="3">១.១. អគ្គ.ឧស្សាហកម្មហិរញ្ញវត្ថុ</vs-td>
                <vs-td colspan="3">១.២. អគ្គ.សហប្រតិ.អន្តរ.គ្រប់.បំណុល</vs-td>
                <vs-td colspan="3">១.៣. និយ័តករគណនេយ្យ និងសវនកម្ម</vs-td>
              </vs-tr>

              <vs-tr style="background-color: rgb(183 255 215)">
                <vs-td style="width: 150px;"><small>ច្បាប់ហិរញ្ញវត្ថុ</small></vs-td>
                <vs-td style="width: 150px;"><small>អនុវត្ត</small></vs-td>
                <vs-td style="width: 150px;"><small>អនុ/ច្បាប់</small></vs-td>
                
                <vs-td style="width: 150px;"><small>ច្បាប់ហិរញ្ញវត្ថុ</small></vs-td>
                <vs-td style="width: 150px;"><small>អនុវត្ត</small></vs-td>
                <vs-td style="width: 150px;"><small>អនុ/ច្បាប់</small></vs-td>

                <vs-td style="width: 150px;"><small>ច្បាប់ហិរញ្ញវត្ថុ</small></vs-td>
                <vs-td style="width: 150px;"><small>អនុវត្ត</small></vs-td>
                <vs-td style="width: 150px;"><small>អនុ/ច្បាប់</small></vs-td>

                <vs-td style="width: 150px;"><small>ច្បាប់ហិរញ្ញវត្ថុ</small></vs-td>
                <vs-td style="width: 150px;"><small>អនុវត្ត</small></vs-td>
                <vs-td style="width: 150px;"><small>អនុ/ច្បាប់</small></vs-td>

                <vs-td style="width: 150px;"><small>ច្បាប់ហិរញ្ញវត្ថុ</small></vs-td>
                <vs-td style="width: 150px;"><small>អនុវត្ត</small></vs-td>
                <vs-td style="width: 150px;"><small>អនុ/ច្បាប់</small></vs-td>

                <vs-td style="width: 150px;"><small>ច្បាប់ហិរញ្ញវត្ថុ</small></vs-td>
                <vs-td style="width: 150px;"><small>អនុវត្ត</small></vs-td>
                <vs-td style="width: 150px;"><small>អនុ/ច្បាប់</small></vs-td>

                <vs-td style="width: 150px;"><small>ច្បាប់ហិរញ្ញវត្ថុ</small></vs-td>
                <vs-td style="width: 150px;"><small>អនុវត្ត</small></vs-td>
                <vs-td style="width: 150px;"><small>អនុ/ច្បាប់</small></vs-td>
              </vs-tr>

              <!-- Row -->
              <vs-tr>
                <vs-td colspan="4"><center>សរុបរួមចំណូល(ក+ខ)</center></vs-td>
                <vs-td>0.00</vs-td>
                <vs-td>0.00</vs-td>
                <vs-td>0.00</vs-td>
                <vs-td>0.00</vs-td>
                <vs-td>0.00</vs-td>
                <vs-td>0.00</vs-td>
                <vs-td>0.00</vs-td>
                <vs-td>0.00</vs-td>
                <vs-td>0.00</vs-td>
                <vs-td>0.00</vs-td>
                <vs-td>0.00</vs-td>
                <vs-td>0.00</vs-td>
                <vs-td>0.00</vs-td>
                <vs-td>0.00</vs-td>
                <vs-td>0.00</vs-td>
                <vs-td>0.00</vs-td>
                <vs-td>0.00</vs-td>
                <vs-td>0.00</vs-td>
                <vs-td>0.00</vs-td>
                <vs-td>0.00</vs-td>
                <vs-td>0.00</vs-td>
              </vs-tr>

              <vs-tr>
                <vs-td colspan="4"><center>ក្នុងនោះចំណូលក្នុងប្រទេស</center></vs-td>
                <vs-td>0.00</vs-td>
                <vs-td>0.00</vs-td>
                <vs-td>0.00</vs-td>
                <vs-td>0.00</vs-td>
                <vs-td>0.00</vs-td>
                <vs-td>0.00</vs-td>
                <vs-td>0.00</vs-td>
                <vs-td>0.00</vs-td>
                <vs-td>0.00</vs-td>
                <vs-td>0.00</vs-td>
                <vs-td>0.00</vs-td>
                <vs-td>0.00</vs-td>
                <vs-td>0.00</vs-td>
                <vs-td>0.00</vs-td>
                <vs-td>0.00</vs-td>
                <vs-td>0.00</vs-td>
                <vs-td>0.00</vs-td>
                <vs-td>0.00</vs-td>
                <vs-td>0.00</vs-td>
                <vs-td>0.00</vs-td>
                <vs-td>0.00</vs-td>
              </vs-tr>

              <vs-tr>
                <vs-td colspan="4"><center>ក. សរុបចំណូលចរន្ត (ក្រុមទី១ + ក្រុមទី២)</center></vs-td>
                <vs-td>0.00</vs-td>
                <vs-td>0.00</vs-td>
                <vs-td>0.00</vs-td>
                <vs-td>0.00</vs-td>
                <vs-td>0.00</vs-td>
                <vs-td>0.00</vs-td>
                <vs-td>0.00</vs-td>
                <vs-td>0.00</vs-td>
                <vs-td>0.00</vs-td>
                <vs-td>0.00</vs-td>
                <vs-td>0.00</vs-td>
                <vs-td>0.00</vs-td>
                <vs-td>0.00</vs-td>
                <vs-td>0.00</vs-td>
                <vs-td>0.00</vs-td>
                <vs-td>0.00</vs-td>
                <vs-td>0.00</vs-td>
                <vs-td>0.00</vs-td>
                <vs-td>0.00</vs-td>
                <vs-td>0.00</vs-td>
                <vs-td>0.00</vs-td>
              </vs-tr>

              <vs-tr>
                <vs-td colspan="4"><center>ក្រុមទី១ ចំណូលពិត (ប្រភេទទី១ + ប្រភេទទី២)</center></vs-td>
                <vs-td>0.00</vs-td>
                <vs-td>0.00</vs-td>
                <vs-td>0.00</vs-td>
                <vs-td>0.00</vs-td>
                <vs-td>0.00</vs-td>
                <vs-td>0.00</vs-td>
                <vs-td>0.00</vs-td>
                <vs-td>0.00</vs-td>
                <vs-td>0.00</vs-td>
                <vs-td>0.00</vs-td>
                <vs-td>0.00</vs-td>
                <vs-td>0.00</vs-td>
                <vs-td>0.00</vs-td>
                <vs-td>0.00</vs-td>
                <vs-td>0.00</vs-td>
                <vs-td>0.00</vs-td>
                <vs-td>0.00</vs-td>
                <vs-td>0.00</vs-td>
                <vs-td>0.00</vs-td>
                <vs-td>0.00</vs-td>
                <vs-td>0.00</vs-td>
              </vs-tr>

              <vs-tr>
                <vs-td colspan="4"><center>ប្រភេទទី១ (ចំណូលសារពើពន្ធ)</center></vs-td>
                <vs-td>0.00</vs-td>
                <vs-td>0.00</vs-td>
                <vs-td>0.00</vs-td>
                <vs-td>0.00</vs-td>
                <vs-td>0.00</vs-td>
                <vs-td>0.00</vs-td>
                <vs-td>0.00</vs-td>
                <vs-td>0.00</vs-td>
                <vs-td>0.00</vs-td>
                <vs-td>0.00</vs-td>
                <vs-td>0.00</vs-td>
                <vs-td>0.00</vs-td>
                <vs-td>0.00</vs-td>
                <vs-td>0.00</vs-td>
                <vs-td>0.00</vs-td>
                <vs-td>0.00</vs-td>
                <vs-td>0.00</vs-td>
                <vs-td>0.00</vs-td>
                <vs-td>0.00</vs-td>
                <vs-td>0.00</vs-td>
                <vs-td>0.00</vs-td>
              </vs-tr>

              <vs-tr>
                <vs-td>70</vs-td>
                <vs-td></vs-td>
                <vs-td></vs-td>
                <vs-td>ចំណូលសារពើពន្ធក្នុងស្រុក</vs-td>
                <vs-td>0.00</vs-td>
                <vs-td>0.00</vs-td>
                <vs-td>0.00</vs-td>
                <vs-td>0.00</vs-td>
                <vs-td>0.00</vs-td>
                <vs-td>0.00</vs-td>
                <vs-td>0.00</vs-td>
                <vs-td>0.00</vs-td>
                <vs-td>0.00</vs-td>
                <vs-td>0.00</vs-td>
                <vs-td>0.00</vs-td>
                <vs-td>0.00</vs-td>
                <vs-td>0.00</vs-td>
                <vs-td>0.00</vs-td>
                <vs-td>0.00</vs-td>
                <vs-td>0.00</vs-td>
                <vs-td>0.00</vs-td>
                <vs-td>0.00</vs-td>
                <vs-td>0.00</vs-td>
                <vs-td>0.00</vs-td>
                <vs-td>0.00</vs-td>
              </vs-tr>

              <vs-tr>
                <vs-td></vs-td>
                <vs-td>7001</vs-td>
                <vs-td></vs-td>
                <vs-td>ពន្ធផ្ទាល់(ពន្ធលើទុន ប្រាក់ចំណូល)</vs-td>
                <vs-td>0.00</vs-td>
                <vs-td>0.00</vs-td>
                <vs-td>0.00</vs-td>
                <vs-td>0.00</vs-td>
                <vs-td>0.00</vs-td>
                <vs-td>0.00</vs-td>
                <vs-td>0.00</vs-td>
                <vs-td>0.00</vs-td>
                <vs-td>0.00</vs-td>
                <vs-td>0.00</vs-td>
                <vs-td>0.00</vs-td>
                <vs-td>0.00</vs-td>
                <vs-td>0.00</vs-td>
                <vs-td>0.00</vs-td>
                <vs-td>0.00</vs-td>
                <vs-td>0.00</vs-td>
                <vs-td>0.00</vs-td>
                <vs-td>0.00</vs-td>
                <vs-td>0.00</vs-td>
                <vs-td>0.00</vs-td>
                <vs-td>0.00</vs-td>
              </vs-tr>

              <vs-tr>
                <vs-td></vs-td>
                <vs-td></vs-td>
                <vs-td>7001</vs-td>
                <vs-td>ពន្ធលើប្រាក់ចំណូល</vs-td>
                <vs-td>0.00</vs-td>
                <vs-td>0.00</vs-td>
                <vs-td>0.00</vs-td>
                <vs-td>0.00</vs-td>
                <vs-td>0.00</vs-td>
                <vs-td>0.00</vs-td>
                <vs-td>0.00</vs-td>
                <vs-td>0.00</vs-td>
                <vs-td>0.00</vs-td>
                <vs-td>0.00</vs-td>
                <vs-td>0.00</vs-td>
                <vs-td>0.00</vs-td>
                <vs-td>0.00</vs-td>
                <vs-td>0.00</vs-td>
                <vs-td>0.00</vs-td>
                <vs-td>0.00</vs-td>
                <vs-td>0.00</vs-td>
                <vs-td>0.00</vs-td>
                <vs-td>0.00</vs-td>
                <vs-td>0.00</vs-td>
                <vs-td>0.00</vs-td>
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
    font-size: 12px;
    min-width: 100px;
    text-align: center;
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
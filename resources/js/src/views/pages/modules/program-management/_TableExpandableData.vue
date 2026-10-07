

<template>
    <vx-card title="កម្មវិធីទី១ៈ ការគ្រប់គ្រងវិស្ស័យសេដ្ឋកិច្ច">
      <div class="flex flex-wrap-reverse items-center data-list-btn-container">
        <!-- ADD NEW -->
        <div class="btn-add-new p-3 mb-4 mr-4 rounded-lg cursor-pointer text-right flex items-center justify-center text-lg font-medium text-base text-primary border border-solid border-primary">
            <feather-icon icon="PlusIcon" svgClasses="h-4 w-4" />
            <span class="ml-2 text-base text-primary">{{$t("AddNew")}}</span>
        </div>
      </div>

      <vs-table :data="data">

          <template #thead>
            <vs-th>{{ $tt('កូដ') }}</vs-th>
            <vs-th>{{ $tt('ចំណាត់ថ្នាក់/កូដ/អនុកម្មវិធី/ចង្កោមកម្មវិធី') }}</vs-th>
            <vs-th>{{ $tt('រៀបចំដោយ') }}</vs-th>
            <vs-th>{{ $tt('ចំនួនសកម្មភាព') }}</vs-th>
            <vs-th>{{ $tt('អង្គភាព') }}</vs-th>
            <vs-th>{{ $tt('ស្ថានភាព') }}</vs-th>
            <vs-th>{{ $t('Action') }}</vs-th>
          </template>

          <template #default="{data}">
              <vs-tr :data="tr" :key="indextr" v-for="(tr, indextr) in data">
                  <vs-td :data="tr.code">
                      {{tr.code}}
                  </vs-td>
                  <vs-td :data="tr.classication">
                      {{tr.classication}}
                  </vs-td>
                  <vs-td :data="tr.prepare_by">
                      {{tr.prepare_by}}
                  </vs-td>
                  <vs-td :data="tr.no_activity">
                      {{tr.no_activity}}
                  </vs-td>
                  <vs-td :data="tr.entity">
                      {{tr.entity}}
                  </vs-td>

                  <vs-td :data="tr.status">
                      {{tr.status}}
                  </vs-td>

                  <vs-td class="whitespace-no-wrap">
                    <feather-icon icon="DollarSignIcon" svgClasses="w-5 h-5 hover:text-primary stroke-current"/>
                    <feather-icon icon="EditIcon" svgClasses="w-5 h-5 hover:text-primary stroke-current"/>
                    <feather-icon icon="TrashIcon" svgClasses="w-5 h-5 hover:text-danger stroke-current" class="ml-2" @click.stop="deleteData(tr.id)" />
                  </vs-td>

                  <template #expand class="expand-user">
                      <div class="con-expand-users w-full">
                          <div class="con-btns-user flex items-center justify-between">
                              <div class="con-userx flex items-center justify-start">
                                  <vs-avatar :badge="tr.id" size="45px" :src="`https://randomuser.me/api/portraits/women/${tr.id}.jpg`" />
                                  <span>{{ tr.name }}</span>
                              </div>
                              <div class="flex">
                                  <vs-button type="border" size="small" icon-pack="feather" icon="icon-phone" class="mr-2"></vs-button>
                                  <vs-button type="border" size="small" icon-pack="feather" icon="icon-send" color="success" class="mr-2"></vs-button>
                                  <vs-button type="border" size="small" icon-pack="feather" icon="icon-trash" color="danger"></vs-button>
                              </div>
                          </div>
                          <vs-list>
                              <vs-list-item icon-pack="feather" icon="icon-mail" :title="tr.email"></vs-list-item>
                              <vs-list-item icon-pack="feather" icon="icon-globe" :title="tr.website"></vs-list-item>
                          </vs-list>
                      </div>
                  </template>
              </vs-tr>
          </template>
      </vs-table>

    </vx-card>
</template>

<script>
export default {
    data() {
        return {
            data: [
              {
                "code": "10101",
                "classication": "១.អនុកម្មវិធី, ១.១. ការគ្រប់គ្រងគោលនយោបាយសេដ្ឋកិច្ចនិងហិរញ្ញវត្ថុ និងគោលនយោបាយតាមវិស្ស័យផ្សេងៗ",
                "prepare_by": "នាយកដ្ឋានផែនការថវិកា",
                "no_activity": "10",
                "entity": "លេខាធិការដ្ឋាន",
                "status": "សកម្ម"
              },
            ]
        }
    },
}
</script>

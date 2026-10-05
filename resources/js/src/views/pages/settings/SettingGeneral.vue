<template>
  <vx-card no-shadow>

    <!-- Img Row -->
    <!-- <div class="flex flex-wrap items-center mb-base">
      <vs-avatar :src="activeUserInfo.photoURL" size="70px" class="mr-4 mb-4" />
      <div>
        <vs-button class="mr-4 sm:mb-0 mb-2">Upload photo</vs-button>
        <vs-button type="border" color="danger">Remove</vs-button>
        <p class="text-sm mt-2">Allowed JPG, GIF or PNG. Max size of 800kB</p>
      </div>
    </div> -->
    
    <!-- <DForm @clickForm="initTableData" ref="refModalForm" :data="data" :dataInfo="dataInfo"
                    :parentDataInfo="parentDataInfo" :formAttributes="formAttributes" :api="api"
                    :rowDisplay="rowDisplay"/> -->

    <template title="modal" class="modal">
      <d-file-manager ref="refModelFileManager" @clicked="initPassProps"></d-file-manager>
    </template>
    <div class="vx-row">
      <template v-for="(formAttribute, i) in formAttributes">
          <!-- Form Input Text -->
          <div v-if="formAttribute.type == 'text'" :class="styleClass" dclass="mt-4">
              <label class="mb-2">{{ $t(formAttribute.name) }}</label>
              <span v-if="formAttribute.required">
                  <vs-input v-validate="'required'" v-model="form.attribute[formAttribute.name]"
                      :placeholder="$t(formAttribute.name)" :name="formAttribute.name" class="mt-1 w-full" />
                  <span class="text-danger text-sm" v-show="errors.has(formAttribute.name)">{{
                      $t("required_" + formAttribute.name)
                  }}</span>
              </span>
              <vs-input v-else v-validate="''" :placeholder="$t(formAttribute.name)"
                  v-model="form.attribute[formAttribute.name]" :name="formAttribute.name" class="mt-1 w-full" />
          </div>

          <!-- Form Input Number -->
          <!-- <div v-if="formAttribute.type == 'hidden' && formAttribute.required" :class="styleClass" dclass="mt-4"> -->
              <!-- <label>{{ $t(formAttribute.name) }} -- {{ data[formAttribute.name] }}</label> -->
              <vs-input v-if="formAttribute.type == 'hidden' && formAttribute.required" type="hidden"
                  v-model="form.attribute[formAttribute.name]" :value="data[formAttribute.name]"
                  :name="formAttribute.name" class="mt-2 w-full" />
          <!-- </div> -->

          <!-- Form Input Text -->
          <div v-if="formAttribute.type == 'password'" :class="styleClass" dclass="mt-4">
              <label class="mb-2">{{ $t(formAttribute.name) }}</label>
              <span v-if="formAttribute.required">
                  <vs-input type="password" v-validate="'required'"
                      v-model="form.attribute[formAttribute.name]" :placeholder="$t(formAttribute.name)"
                      :name="formAttribute.name" class="mt-1 w-full" />
                  <span class="text-danger text-sm" v-show="errors.has(formAttribute.name)">{{
                      $t("required_" + formAttribute.name)
                  }}</span>
              </span>
              <vs-input type="password" v-else v-validate="''" :placeholder="$t(formAttribute.name)"
                  v-model="form.attribute[formAttribute.name]" :name="formAttribute.name" class="mt-1 w-full" />
          </div>

          <!-- Form Input Number -->
          <div v-if="formAttribute.type == 'number'" :class="styleClass" dclass="mt-4">
              <label class="mb-2">{{ $t(formAttribute.name) }}</label>
              <span v-if="formAttribute.required">
                  <vs-input type="number" v-validate="'required'" :name="formAttribute.name"
                      v-model="form.attribute[formAttribute.name]" icon-inc="expand_less" icon-dec="expand_more"
                      class="mt-1 w-full" />
                  <span class="text-danger text-sm" v-show="errors.has(formAttribute.name)">
                      {{ $t("required_" + formAttribute.name) }}
                  </span>
              </span>
              <vs-input type="number" v-validate="''" v-else :name="formAttribute.name"
                  v-model="form.attribute[formAttribute.name]" icon-inc="expand_less" icon-dec="expand_more"
                  class="mt-1 w-full" />
          </div>

          <div v-if="formAttribute.type == 'select'" :class="styleClass" dclass="mt-4">
              <label class="mb-2">{{ $t(formAttribute.name) }} <span
                      v-if="formAttribute.required">*</span></label>
              <button v-if="formAttribute.name != 'year' && formAttribute.name != 'gender'" type="button"
                  @click.stop="initNewForm(formAttribute.name)">
                  <feather-icon v-if="formAttribute.name != 'year' && formAttribute.name != 'gender'"
                      type="button" @click.stop="initNewForm(formAttribute.name)" icon="PlusIcon"
                      svgClasses="h-4 w-4" />
              </button>
              <span v-if="formAttribute.required">
                  <template v-if="formAttribute.hasFilter">
                      <v-select
                          v-on:input="onInitChange($event, formAttribute.filterObj, formAttribute.name, formAttribute.api)"
                          v-validate="'required'" v-model="form.attribute[formAttribute.name]"
                          :name="formAttribute.name" :options="data[formAttribute.name]"
                          :dir="$vs.rtl ? 'rtl' : 'ltr'" class="mt-2 w-full" />
                      <span class="text-danger text-sm" v-show="errors.has(formAttribute.name)">{{
                          $t("required_" + formAttribute.name)
                      }}</span>
                  </template>
                  <template v-if="formAttribute.hasFormData">
                      <v-select v-validate="''"
                          v-on:input="onInitFormData($event, formAttribute.filterObj, formAttribute.name, formAttribute.api)"
                          v-model="form.attribute[formAttribute.name]" :name="formAttribute.name"
                          :options="data[formAttribute.name]" :dir="$vs.rtl ? 'rtl' : 'ltr'"
                          class="mt-2 w-full" />
                  </template>
                  <template v-else>
                      <v-select v-validate="'required'" :name="formAttribute.name"
                          v-model="form.attribute[formAttribute.name]" :options="data[formAttribute.name]"
                          :dir="$vs.rtl ? 'rtl' : 'ltr'" class="mt-1 w-full" />
                      <span class="text-danger text-sm" v-show="errors.has(formAttribute.name)">{{
                          $t("required_" + formAttribute.name)
                      }}</span>
                  </template>
              </span>
              <span v-else>
                  <template v-if="formAttribute.hasFilter">
                      <v-select v-validate="''"
                          v-on:input="onInitChange($event, formAttribute.filterObj, formAttribute.name, formAttribute.api)"
                          v-model="form.attribute[formAttribute.name]" :name="formAttribute.name"
                          :options="data[formAttribute.name]" :dir="$vs.rtl ? 'rtl' : 'ltr'"
                          class="mt-2 w-full" />
                  </template>
                  <template v-else>
                      <v-select v-validate="''" :name="formAttribute.name"
                          v-model="form.attribute[formAttribute.name]" :options="data[formAttribute.name]"
                          :dir="$vs.rtl ? 'rtl' : 'ltr'" class="mt-1 w-full" />
                  </template>
              </span>
          </div>

          <div v-if="formAttribute.type == 'select_autocomplete'" :class="styleClass" dclass="mt-4">
              <label class="mb-2">{{ $t(formAttribute.name) }} - {{ form.attribute[formAttribute.name] }}</label>
              <span v-if="formAttribute.required">
                  <vs-select :name="formAttribute.name" @change="onChangeElement($event, formAttribute.name)"
                      v-model="form.attribute[formAttribute.name]" :dir="$vs.rtl ? 'rtl' : 'ltr'"
                      v-validate="'required'" class="mt-1 w-full">
                      <vs-select-item :key="index" :value="item.value" :text="item.label"
                          v-for="(item, index) in data[formAttribute.name]" />
                  </vs-select>
                  <span class="text-danger text-sm" v-show="errors.has(formAttribute.name)">{{ $t("required_" +
                      formAttribute.name)
                      }}</span>
              </span>
              <template v-if="formAttribute.hasFormData">
                  <v-select v-validate="''"
                      v-on:input="onInitFormData($event, formAttribute.filterObj, formAttribute.name, formAttribute.api)"
                      v-model="form.attribute[formAttribute.name]" :name="formAttribute.name"
                      :options="data[formAttribute.name]" :dir="$vs.rtl ? 'rtl' : 'ltr'"
                      class="mt-2 w-full" />
              </template>
              <span v-else>
                  <vs-select :name="formAttribute.name" @change="onChangeElement($event, formAttribute.name)"
                      v-model="form.attribute[formAttribute.name]" class="mt-1 w-full">
                      <vs-select-item :key="index" :value="item.value" :text="item.label"
                          v-for="(item, index) in data[formAttribute.name]" />
                  </vs-select>
              </span>
          </div>

          <!-- Form Check Box -->
          <div v-if="formAttribute.type == 'checkbox'" :class="styleClass" dclass="mt-4">
              <label class="mb-2">{{ $t(formAttribute.name) }}</label>
              <span v-if="formAttribute.required">
                  <vs-checkbox v-model="form.attribute[formAttribute.name]">
                      {{ $t(formAttribute.name) }}
                  </vs-checkbox>
              </span>
              <span v-else>
                  <!-- <vs-checkbox v-model="form.attribute[formAttribute.name]"> {{$t(formAttribute.name)}}</vs-checkbox> -->
                  <!-- <vs-checkbox v-model="form.attribute[rowf.value]" v-for="(rowf, index) in formAttribute.attributes"> {{ $t(rowf.name)}}</vs-checkbox> -->
                  <vs-checkbox v-model="form.attribute[formAttribute.name]">
                      {{ $t(formAttribute.name) }}
                  </vs-checkbox>
              </span>
          </div>

          <!-- Form Textarea -->
          <div v-if="formAttribute.type == 'textarea'" :class="styleClass" dclass="mt-4">
              <label class="mb-2">{{ $t(formAttribute.name) }}</label>
              <span v-if="formAttribute.required">
                  <vs-textarea v-validate="'required'" class="mt-1 w-full"
                      v-model="form.attribute[formAttribute.name]" />
                  <span class="text-danger text-sm" v-show="errors.has(formAttribute.name)">{{ $t("required_" +
                      formAttribute.name)
                      }}</span>
              </span>
              <span v-else>
                  <vs-textarea v-validate="''" class="mt-1 w-full" v-model="form.attribute[formAttribute.name]" />
              </span>
          </div>

          <!-- Form Date -->
          <div v-if="formAttribute.type == 'date'" :class="styleClass" dclass="mt-4">
              <label class="mb-2">{{ $t(formAttribute.name) }}</label>
              <span v-if="formAttribute.required">
                  <flat-pickr v-validate="'required'" :name="formAttribute.name"
                      v-model="form.attribute[formAttribute.name]" class="mt-1 w-full" />
                  <span class="text-danger text-sm" v-show="errors.has(formAttribute.name)">{{ $t("required_" +
                      formAttribute.name)
                      }}</span>
              </span>
              <span v-else>
                  <flat-pickr v-validate="''" v-model="form.attribute[formAttribute.name]" class="mt-1 w-full" />
              </span>
          </div>

          <!-- Form Group -->
          <div v-if="formAttribute.type == 'text_group'" class="mt-4 vx-col lg:w-1/1 w-full" d>
              <div>
                  <label class="mb-2">{{ $t(formAttribute.name) }}</label>
                  <div :key="j" v-for="(childformAttribute, j) in formAttribute.child_form"
                      class="vx-col lg:w-1/1 w-full">

                      <!-- Form Dynamic -->
                      <!-- Form Input Text -->
                      <div v-if="childformAttribute.type == 'text'" class="mt-4">
                          <label class="mb-2">{{ $t(childformAttribute.name) }}</label>
                          <span v-if="childformAttribute.required">
                              <vs-input v-validate="'required'"
                                  v-model="form.attribute[childformAttribute.name]"
                                  :placeholder="$t(childformAttribute.name)" :name="childformAttribute.name"
                                  class="mt-1 w-full" />
                              <span class="text-danger text-sm"
                                  v-show="errors.has(childformAttribute.name)">{{
                                      $t("required_" +
                                          childformAttribute.name)
                                  }}</span>
                          </span>
                          <vs-input v-else :placeholder="$t(childformAttribute.name)"
                              v-model="form.attribute[childformAttribute.name]" :name="childformAttribute.name"
                              class="mt-1 w-full" />
                      </div>

                      <!-- Form Input Number -->
                      <div v-if="childformAttribute.type == 'number'" class="mt-4">
                          <label class="mb-2">{{ $t(childformAttribute.name) }}</label>
                          <span v-if="childformAttribute.required">
                              <vs-input-number :name="childformAttribute.name"
                                  v-model="form.attribute[childformAttribute.name]" icon-inc="expand_less"
                                  icon-dec="expand_more" class="mt-1 w-full" />
                              <span class="text-danger text-sm" v-show="errors.has(childformAttribute.name)">{{
                                  $t("required_" + childformAttribute.name)
                              }}</span>
                          </span>
                          <vs-input-number v-else :name="childformAttribute.name"
                              v-model="form.attribute[childformAttribute.name]" icon-inc="expand_less"
                              icon-dec="expand_more" class="mt-1 w-full" />
                      </div>

                      <div v-if="childformAttribute.type == 'select'" class="mt-4">
                          <label class="mb-2">{{ $t(childformAttribute.name) }}</label>
                          <span v-if="childformAttribute.required">
                              <vs-select :name="childformAttribute.name"
                                  v-model="form.attribute[childformAttribute.name]" :dir="$vs.rtl ? 'rtl' : 'ltr'"
                                  v-validate="'required'" class="mt-1 w-full">
                                  <vs-select-item :key="index" :value="item.value" :text="item.label"
                                      v-for="(item, index) in data[childformAttribute.name]" />
                              </vs-select>
                              <span class="text-danger text-sm" v-show="errors.has(childformAttribute.name)">{{
                                  $t("required_" + childformAttribute.name)
                              }}</span>
                          </span>
                          <span v-else>
                              <vs-select :name="childformAttribute.name"
                                  v-model="form.attribute[childformAttribute.name]" class="mt-1 w-full">
                                  <vs-select-item :key="index" :value="item.value" :text="item.label"
                                      v-for="(item, index) in data[childformAttribute.name]" />
                              </vs-select>
                          </span>
                      </div>

                      <!-- Form Check Box -->
                      <div v-if="childformAttribute.type == 'checkbox'" class="mt-4">
                          <label class="mb-2">{{ $t(childformAttribute.name) }}</label>
                          <span v-if="childformAttribute.required">
                              <vs-checkbox :key="index" v-model="form.attribute[childformAttribute.name]">
                                  <!-- {{$t(childformAttribute.name) }}--></vs-checkbox>
                          </span>
                          <span v-else>
                              <!-- <vs-checkbox :key="index" v-model="form.attribute[childformAttribute.name]"> {{$t(childformAttribute.name)}}</vs-checkbox> -->
                              <!-- <vs-checkbox :key="index" v-model="form.attribute[rowf.value]" v-for="(rowf, index) in childformAttribute.attributes"> {{ $t(rowf.name)}}</vs-checkbox> -->
                              <vs-checkbox :key="index" v-model="form.attribute[childformAttribute.name]"
                                  v-for="(rowf, index) in childformAttribute.attributes"> {{ $t(rowf.name)
                                  }}</vs-checkbox>
                          </span>
                      </div>

                      <!-- Form Textarea -->
                      <div v-if="childformAttribute.type == 'textarea'" class="mt-4">
                          <label class="mb-2">{{ $t(childformAttribute.name) }}</label>
                          <span v-if="childformAttribute.required">
                              <vs-textarea v-validate="'required'" class="mt-1 w-full"
                                  v-model="form.attribute[childformAttribute.name]" />
                              <span class="text-danger text-sm" v-show="errors.has(childformAttribute.name)">{{
                                  $t("required_" + childformAttribute.name)
                              }}</span>
                          </span>
                          <span v-else>
                              <vs-textarea class="mt-1 w-full"
                                  v-model="form.attribute[childformAttribute.name]" />
                          </span>
                      </div>

                      <!-- Form Date -->
                      <div v-if="childformAttribute.type == 'date'" class="mt-4">
                          <label class="mb-2">{{ $t(childformAttribute.name) }}</label>
                          <span v-if="childformAttribute.required">
                              <flat-pickr v-validate="'required'" :name="childformAttribute.name"
                                  v-model="form.attribute[childformAttribute.name]" class="mt-1 w-full" />
                              <span class="text-danger text-sm" v-show="errors.has(childformAttribute.name)">{{
                                  $t("required_" + childformAttribute.name)
                              }}</span>
                          </span>
                          <span v-else>
                              <flat-pickr v-model="form.attribute[childformAttribute.name]" class="mt-1 w-full" />
                          </span>
                      </div>
                      <!-- #End Form Dynamic -->

                  </div>
              </div>
          </div>

          <!-- Form Grid -->
          <div v-if="formAttribute.type == 'grid'" class="mt-4 vx-col lg:w-1/1 w-full">
              <!-- <vs-alert icon="warning" active="true" color="primary" class="mt-5"> -->
              <div style="background-color:#253358; color: #ffffff; padding: 5px;"><span>{{ $t(formAttribute.name)
                      }}</span></div>
              <!-- </vs-alert> -->
          </div>

          <!-- Form Grid -->
          <!-- Form Input Text -->
          <div v-if="formAttribute.type == 'file'" :class="styleClass" dclass="mt-4">
              <label class="mb-2">{{ $t(formAttribute.name) }}</label>
              <d-file-picker class="mt-1" :name="formAttribute.name" :value="form.attribute[formAttribute.name]"
                  @browse="initOpenFileManager(formAttribute.name)"
                  @view="initViewFile"
                  @clear="$set(form.attribute, formAttribute.name, '')" />
              <vs-input type="hidden" :placeholder="$t(formAttribute.name)"
                  v-model="form.attribute[formAttribute.name]" :value="data[formAttribute.name]" class="mt-2 w-full" />
              <!-- <span v-if="formAttribute.required">
                  <vs-input v-validate="'required'" v-model="form.attribute[formAttribute.name]"
                      :placeholder="$t(formAttribute.name)" :name="formAttribute.name" class="mt-1 w-full" />
                  <span class="text-danger text-sm" v-show="errors.has(formAttribute.name)">{{
                      $t("required_" + formAttribute.name)
                  }}</span>
              </span>
              <vs-input v-else v-validate="''" :placeholder="$t(formAttribute.name)"
                  v-model="form.attribute[formAttribute.name]" :name="formAttribute.name" class="mt-1 w-full" /> -->
          </div>

      </template>
    </div>

    <span class="pull-right">
      <vs-button type="filled" @click.prevent="submitForm" class="mt-5 block">{{
        $t("btn_save") }}</vs-button>
    </span>

    <!-- </div>  -->
    <!-- Save & Reset Button -->
    <!-- <div class="flex flex-wrap items-center justify-end">
      <vs-button @click="settingConfig()" class="ml-auto mt-2">{{ $t('btn_update') }}</vs-button>
    </div> -->

  </vx-card>
</template>

<script>
import axios from "@/axios.js"
import vSelect from 'vue-select';
import flatPickr from 'vue-flatpickr-component';
import DForm from '@/views/form-builder/DForm.vue'
import apiConfig from "@/apiConfig.js"
import { ref } from 'vue';
import DFileManager from '@/views/form-builder/DFileManager.vue';
import DFilePicker from '@/views/form-builder/DFilePicker.vue';
export default {
  components: {
    DForm,
    vSelect,
    DFileManager,
    DFilePicker
  },
  data() {
    return {
      data: {
        config_country: []
      },
      dataInfo: null,
      parentDataInfo: null,
      api: apiConfig._apiConfiguration,
      rowDisplay: "3grid",
      formAttributes: [
        {
          name: "config_logo",
          type: "file",
          required: false,
        },
        {
          name: "config_owner",
          type: "textarea",
          required: true
        },
        {
          name: "config_address",
          type: "textarea",
          required: true
        },

        {
          name: "config_planning_year",
          type: "select",
          required: false,
          options: [
            {
              label: "",
              value: ""
            }
          ]
        },

        {
          name: "config_currency",
          type: "select",
          required: false,
          options: []
        },
        {
          name: "config_date_format",
          type: "select",
          required: false,
          options: []
        },
        {
          name: "config_allow_file_extension",
          type: "textarea",
          required: false
        },
        {
          name: "config_comment",
          type: "textarea",
          required: false
        },
        // {
        //   name: "config_country",
        //   type: "select",
        //   required: false,
        //   options: [
        //     {
        //       label: "",
        //       value: ""
        //     }
        //   ]
        // },
        {
          name: "config_language",
          type: "select",
          required: false,
          options: []
        },
        {
          name: "config_limit",
          type: "number",
          required: true,
        },
        {
          name: "config_default_page",
          type: "number",
          required: false
        },
        {
          name: "config_email",
          type: "textarea",
          required: false
        },
        {
          name: "config_fax",
          type: "textarea",
          required: false
        },
        {
          name: "config_FIRE_BASE_SERVER_API_KEY",
          type: "textarea",
          required: false
        },
        {
          name: "config_FIRE_BASE_SERVER_URL",
          type: "textarea",
          required: false
        },
        {
          name: "config_footer",
          type: "textarea",
          required: false
        },
        {
          name: "config_footer_template",
          type: "textarea",
          required: false
        },
        {
          name: "config_google_map",
          type: "textarea",
          required: false
        },

        {
          name: "config_header_template",
          type: "textarea",
          required: false
        },
        {
          name: "config_khmerdate",
          type: "textarea",
          required: false
        },
        {
          name: "config_mail_engine",
          type: "textarea",
          required: false
        },
        {
          name: "config_dir_image",
          type: "textarea",
          required: false
        },
        {
          name: "config_mail_parameter",
          type: "textarea",
          required: false
        },
        {
          name: "config_mail_smtp_hostname",
          type: "textarea",
          required: false
        },
        {
          name: "config_mail_smtp_password",
          type: "textarea",
          required: false
        },
        {
          name: "config_mail_smtp_port",
          type: "textarea",
          required: false
        },
        {
          name: "config_mail_smtp_time_out",
          type: "textarea",
          required: false
        },
        {
          name: "config_mail_smtp_username",
          type: "textarea",
          required: false
        },

        {
          name: "config_mail_smtp_password",
          type: "textarea",
          required: false
        },
        {
          name: "config_mail_smtp_port",
          type: "textarea",
          required: false
        },
        {
          name: "config_max_file_size",
          type: "textarea",
          required: false
        },
        {
          name: "config_menu",
          type: "textarea",
          required: false
        },
        {
          name: "config_meta_tag_description",
          type: "textarea",
          required: false
        },
        {
          name: "config_meta_tag_keyword",
          type: "textarea",
          required: false
        },
        {
          name: "config_meta_title",
          type: "textarea",
          required: false
        },
        {
          name: "config_name",
          type: "textarea",
          required: false
        },
        {
          name: "config_offset",
          type: "textarea",
          required: false
        },
        {
          name: "config_open_time",
          type: "textarea",
          required: false
        },
        {
          name: "config_social_facebook",
          type: "textarea",
          required: false
        },
        {
          name: "config_social_linkedin",
          type: "textarea",
          required: false
        },
        {
          name: "config_social_youtube",
          type: "textarea",
          required: false
        },
        {
          name: "config_social_twitter",
          type: "textarea",
          required: false
        },
        {
          name: "config_social_instagram",
          type: "textarea",
          required: false
        },
        {
          name: "config_social_telegram",
          type: "textarea",
          required: false
        },
        {
          name: "config_social_pinterest",
          type: "textarea",
          required: false
        },
      ],
      showFileManager: false,
      notFoundFile: "/images/not_found.png",
      iconThumnail: "",
      iconDoc: "/images/file-icons/doc.png",
      iconPDF: "/images/file-icons/pdf.png",
      iconExcel: "/images/file-icons/xls.png",
      form: {
        attribute: {
          photo: "",
          image: "",
          thumbnail: "",
          file: "",
          attach: "",
          config_logo: "",
        },
        attribute: { config_address: "" },
        attribute: { config_allow_file_extension: "" },
        attribute: { config_comment: "" },
        attribute: { config_planning_year: "" },
        attribute: {
          config_country: [
            {
              label: "",
              value: ""
            }
          ]
        },
        attribute: { config_currency: "" },
        attribute: { config_date_format: "" },
        attribute: { config_default_page: "" },
        attribute: { config_dir_image: "" },
        attribute: { config_email: "" },
        attribute: { config_fax: "" },
        attribute: { config_FIRE_BASE_SERVER_API_KEY: "" },
        attribute: { config_FIRE_BASE_SERVER_URL: "" },
        attribute: { config_footer: "" },
        attribute: { config_footer_template: "" },
        attribute: { config_header_template: "" },
        attribute: { config_icon: "" },
        attribute: { config_owner: "" },
        attribute: { config_khmerdate: "" },
        attribute: {
          config_language: {
            "label": "En",
            "value": "En"
          }
        },
        attribute: {
          config_limit: [
            {
              label: "10",
              value: "10"
            }
          ]
        },
        attribute: { config_mail_engine: "" },
        attribute: { config_mail_parameter: "" },
        attribute: { config_mail_smtp_hostname: "" },
        attribute: { config_mail_smtp_password: "" },
        attribute: { config_mail_smtp_port: "" },
        attribute: { config_mail_smtp_time_out: "" },
        attribute: { config_mail_smtp_username: "" },
        attribute: { config_max_file_size: "" },
        attribute: { config_menu: "" },
        attribute: { config_meta_tag_description: "" },
        attribute: { config_meta_tag_keyword: "" },
        attribute: { config_meta_title: "" },
        attribute: { config_name: "" },
        attribute: { config_offset: "" },
        attribute: { config_open_time: "" },
        attribute: { config_social_facebook: "" },
        attribute: { config_social_linkedin: "" },
        attribute: { config_social_youtube: "" },
        attribute: { config_social_twitter: "" },
        attribute: { config_social_instagram: "" },
        attribute: { config_social_telegram: "" },
        attribute: { config_social_pinterest: "" },
        attribute: { config_google_map: "" },
      },
      dataAttributes: [{
        page_number: ""
      }
      ]
    }
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
        axios.get(this.api, {
          params: _params
        }).then((response) => {
          if (response.data) {
            this.data = response.data;
          } else {
            this.data = this.data;
          }
          this.formAttributes.forEach(_formAttribute => {
            if (_formAttribute["type"] == "select") {
              this.form.attribute[_formAttribute["name"]] = (this.data.data[_formAttribute["name"]]);
            } else if (_formAttribute["type"] == "hidden") {
              this.form.attribute[_formAttribute["name"]] = (this.data.data[_formAttribute["name"]]);
            } else if (_formAttribute["type"] == "file") {
              this.form.attribute[_formAttribute["name"]] = (this.data.data[_formAttribute["name"]]);
              this.iconThumnail = (this.data.data[_formAttribute["name"]]);
            }else {
              this.form.attribute[_formAttribute["name"]] = (this.data.data[_formAttribute["name"]]);
            }
            this.$validator.validateAll().then(result => {
            })
          });

          this.$vs.loading.close();
        }).catch((error) => {
          // reject(error)
this.$vs.notify({
                    title: 'Message',
                    text: "Error, while trying to do action or no permission allow!",
                    iconPack: 'feather',
                    icon: 'icon-check-circle',
                    color: 'danger'
                  })
          this.$vs.loading.close();
        })
      })
    },
    getData() {
      let _search_criteria = {
        sort: "id",
        order: "",
        page_number: this.dataAttributes.page_number,
        search_field: ""
      }
      this.getDataTable(_search_criteria);
    },
    loadErrorImage(form_name){
      this.form.attribute["photo"] = this.notFoundFile;
      this.form.attribute["image"] = this.notFoundFile;
      this.form.attribute["thumbnail"] = this.notFoundFile;
      this.form.attribute["attach"] = this.notFoundFile;
      this.form.attribute["file"] = this.notFoundFile;
      this.form.attribute["config_logo"] = this.notFoundFile;
    },
    initPassProps(props){
        switch (props.data.type) {
            case "xlsx":
                this.$set(this.form.attribute, props.props, props.data.path);
                this.iconThumnail = this.iconExcel;
                break;
            case "xls":
                this.$set(this.form.attribute, props.props, props.data.path);
                this.iconThumnail = this.iconExcel;
                break;
            case "doc":
                this.$set(this.form.attribute, props.props, props.data.path);
                this.iconThumnail = this.iconDoc;
                break;
            case "docx":
                this.$set(this.form.attribute, props.props, props.data.path);
                this.iconThumnail = this.iconDoc;
                break;
            case "pdf":
                this.$set(this.form.attribute, props.props, props.data.path);
                this.iconThumnail = this.iconPDF;
                break;
            default:
                this.$set(this.form.attribute, props.props, props.data.path);
                this.iconThumnail = props.data.path;
                break;
        }
    },
    initOpenFileManager(props){
      this.$refs.refModelFileManager.initFileManager(props);
    },
    initViewFile(filePath){
      let route = this.$router.resolve({path: filePath});
      window.open(route.href, '_blank');
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
    },
    submitForm() {
      // alert("testing");
      let _d;
      this.dataFields = [];
      let _formAttribute = this.formAttributes;
      this.formAttributes.forEach(_formAttribute => {
        if (_formAttribute["type"] == "select") {
          try {
            _d = {
              // [_formAttribute["name"]]: this.form.attribute[_formAttribute["name"]]
              [_formAttribute["name"]]: this.form.attribute[_formAttribute["name"]].value
            }
          } catch (e) {
            _d = {
              [_formAttribute["name"]]: this.form.attribute[_formAttribute["name"]]
            }
          }
          // if (typeof this.form.attribute[_formAttribute["name"]].value !== 'error') {
          // } else {
          // }
        } else {
          _d = {
            [_formAttribute["name"]]: this.form.attribute[_formAttribute["name"]]
          }
        }
        this.dataFields.push(_d);
      });
      console.log("==========", this.dataFields);
      this.$validator.validateAll().then(result => {
        if (result) {
          let _data = this.dataFields;
          // this.$vs.loading();
          // if (this.dataInfo.id) {
          // let _id = this.dataInfo.id;
          // this.$vs.loading.close();
          return new Promise((resolve, reject) => {
            axios.put(this.api + '/' + "update", _data)
              .then((response) => {
                if (response.data.success == false) {
                  this.$vs.notify({
                    title: 'Message',
                    text: response.data.message,
                    iconPack: 'feather',
                    icon: 'icon-check-circle',
                    color: 'danger',
                    position: 'top-right'
                  })
                } else {
                  this.$vs.notify({
                    title: 'Message',
                    text: response.data.message,
                    iconPack: 'feather',
                    icon: 'icon-check-circle',
                    color: 'primary',
                    position: 'top-right'
                  })
                  this.$emit('clickForm', '');
                }
                // this.$router.push('/account/expense').catch(() => { })
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
          // } else {
          //     return new Promise((resolve, reject) => {
          //         axios.post(this.api, _data)
          //             .then((response) => {
          //                 if(response.data.message){
          //                     this.$vs.notify({
          //                         title: 'Message',
          //                         text: response.data.message,
          //                         iconPack: 'feather',
          //                         icon: 'icon-check-circle',
          //                         color: 'primary',
          //                         position: 'top-right'
          //                     })
          //                 }else{
          //                     this.$vs.notify({
          //                         title: 'Message',
          //                         text: response.data.message,
          //                         iconPack: 'feather',
          //                         icon: 'icon-check-circle',
          //                         color: 'danger',
          //                         position: 'top-right'
          //                     })
          //                     this.$vs.loading.close();
          //                 }   

          //                 this.$emit('clickForm', '');
          //                 // this.$router.push('/account/expense').catch(() => { })
          //             }).catch((error) => {
          //                 reject(error)
          //                 this.$vs.notify({
          //                     title: 'Message',
          //                     text: "Error, while trying to do action or no permission allow!",
          //                     iconPack: 'feather',
          //                     icon: 'icon-check-circle',
          //                     color: 'danger',
          //                     position: 'top-right'
          //                 })
          //                 this.$vs.loading.close();
          //             })
          //     })
          // }

        }
        this.$vs.loading.close();
      })
    }
  },
  created() {
    if (this.rowDisplay == "1grid") {
      this.styleClass = "vx-col lg:w-1/1 w-full mt-4";
    }
    if (this.rowDisplay == "2grid") {
      this.styleClass = "vx-col lg:w-1/2 w-full mt-4";
    }
    if (this.rowDisplay == "3grid") {
      this.styleClass = "vx-col lg:w-1/3 w-full mt-4";
    }
    if (this.rowDisplay == "4grid") {
      this.styleClass = "vx-col lg:w-1/4 w-full mt-4";
    }
    if (this.rowDisplay == "full-grid") {
      this.styleClass = "vx-col lg:w-1/1 w-full";
    }
    this.$vs.loading();
    this.getData();
  },
  watch: {
  },
  computed: {
    activeUserInfo() {
      return this.$store.state.AppActiveUser
    },
  }
}
</script>
